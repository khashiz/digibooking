<?php
/** 
 * @package   	VikAppointments
 * @subpackage 	com_vikappointments
 * @author    	Matteo Galletti - e4j
 * @copyright 	Copyright (C) 2019 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license  	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link 		https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * VikAppointments View
 */
class VikAppointmentsViewcronjobs extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_font_awesome();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$ordering = OrderingManager::getColumnToOrder('cronjobs', 'id', 1);

		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keys'] 	= $app->getUserStateFromRequest('vapcronjobs.keys', 'keys', '', 'string');
		$filters['class'] 	= $app->getUserStateFromRequest('vapcronjobs.class', 'class', '', 'string');
		$filters['status'] 	= $app->getUserStateFromRequest('vapcronjobs.status', 'status', -1, 'int');

		//db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_cronjob'))
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->where($dbo->qn('name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
		}

		if (!empty($filters['class']))
		{
			$q->where($dbo->qn('class') . ' = ' . $dbo->q($filters['class']));
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('published') . ' = ' . $filters['status']);
		}

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim);
		
		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}

		// get logs

		foreach ($rows as $k => $r)
		{
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_cronjob_log'))
				->where($dbo->qn('id_cronjob') . ' = ' . $r['id'])
				->order($dbo->qn('id') . ' DESC');

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			$rows[$k]['lastlog'] = array();
			
			if ($dbo->getNumRows())
			{
				$rows[$k]['lastlog'] = $dbo->loadAssoc();
			}
		}

		// get cron classes

		VikAppointments::loadCronLibrary();

		$cron_classes = array();

		foreach (CronDispatcher::includeAll() as $file => $cron)
		{
			$cron_classes[$file] = $cron::title();
		}

		asort($cron_classes);

		//

		$new_type = OrderingManager::getSwitchColumnType('cronjobs', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;

		$this->cronClasses = &$cron_classes;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return (!empty($this->filters['class'])
			|| $this->filters['status'] != -1);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar()
	{
		//Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWCRONJOBS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newcronjob', JText::_('VAPNEW'));
			JToolBarHelper::divider();
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editcronjob', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList( VikAppointments::getConfirmSystemMessage(), 'deleteCronjobs', JText::_('VAPDELETE'));
		}

		JToolBarHelper::cancel('cancelConfigCron', JText::_('VAPCANCEL'));
	}
}
