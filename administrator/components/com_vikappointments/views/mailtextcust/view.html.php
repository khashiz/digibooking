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
class VikAppointmentsViewmailtextcust extends JViewUI
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

		$ordering = OrderingManager::getColumnToOrder('mailtextcust', 'id', 1);
		
		$this->addToolBar();

		$filters = array();
		$filters['keys'] 	 = $app->getUserStateFromRequest('vapmailtext.keys', 'keys', '', 'string');
		$filters['position'] = $app->getUserStateFromRequest('vapmailtext.position', 'position', '', 'string');
		$filters['status'] 	 = $app->getUserStateFromRequest('vapmailtext.status', 'status', '', 'string');
		$filters['file'] 	 = $app->getUserStateFromRequest('vapmailtext.file', 'file', '', 'string');
		$filters['tag'] 	 = $app->getUserStateFromRequest('vapmailtext.tag', 'tag', '', 'string');
		
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut = "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `m`.*')
			->select($dbo->qn('s.name', 'sname'))
			->select($dbo->qn('e.nickname', 'ename'))
			->from($dbo->qn('#__vikappointments_cust_mail', 'm'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('m.id_service'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('m.id_employee'))
			->where(1)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->andWhere(array(
				$dbo->qn('m.name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('s.name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('e.nickname') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
			), 'OR');
		}

		if (!empty($filters['position']))
		{
			$q->where($dbo->qn('m.position') . ' = ' . $dbo->q($filters['position']));
		}

		if (!empty($filters['status']))
		{
			$q->where($dbo->qn('m.status') . ' = ' . $dbo->q($filters['status']));
		}

		if (!empty($filters['file']))
		{
			$q->where($dbo->qn('m.file') . ' = ' . $dbo->q($filters['file']));
		}

		if (!empty($filters['tag']))
		{
			$q->where($dbo->qn('m.tag') . ' = ' . $dbo->q($filters['tag']));
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

		$new_type = OrderingManager::getSwitchColumnType('mailtextcust', $ordering['column'], $ordering['type']);
		$ordering = array($ordering['column'] => $new_type);
		
		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;

		// Display the template
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWMAILTEXTCUST'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newmailtext', JText::_('VAPNEW'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editmailtext', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteCustMail', JText::_('VAPDELETE'));
		}
		
		JToolBarHelper::cancel('cancelConfig', JText::_('VAPCANCEL'));
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return (!empty($this->filters['position'])
			|| !empty($this->filters['status'])
			|| !empty($this->filters['file'])
			|| !empty($this->filters['tag']));
	}
}
