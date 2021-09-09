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
class VikAppointmentsViewoptions extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		AppointmentsHelper::load_css_js();

		$app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();

		// Set the toolbar
		$this->addToolBar();
		
		$ordering = OrderingManager::getColumnToOrder('options', 'ordering', 1);

		$filters = array();
		$filters['keys']   = $app->getUserStateFromRequest('vapoption.keys', 'keys', '', 'string');
		$filters['status'] = $app->getUserStateFromRequest('vapoption.status', 'status', -1, 'int');
		$filters['type']   = $app->getUserStateFromRequest('vapoption.type', 'type', -1, 'int');
		
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_option'))
			->order($ordering['column'] . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->where($dbo->qn('name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('published') . ' = ' . $filters['status']);
		}

		if ($filters['type'] != -1)
		{
			$q->where($dbo->qn('required') . ' = ' . $filters['type']);
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
		
		$new_type = OrderingManager::getSwitchColumnType('options', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		// get ordering bounds

		$q = $dbo->getQuery(true)
			->select('MIN(`ordering`) AS `min`, MAX(`ordering`) AS `max`')
			->from($dbo->qn('#__vikappointments_option'));

		$dbo->setQuery($q);
		$dbo->execute();
		$bounds = $dbo->loadAssoc();
		
		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;
		$this->bounds 	= &$bounds;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar()
	{
		// Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWOPTIONS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newoption', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editoption', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteOptions', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['type'] != -1
			|| $this->filters['status'] != -1);
	}
}
