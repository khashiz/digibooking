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
class VikAppointmentsViewwaitinglist extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$ordering = OrderingManager::getColumnToOrder('waitinglist', 'w.timestamp', 1);

		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keys'] 	= $app->getUserStateFromRequest('vapwaiting.keys', 'keys', '', 'string');
		$filters['status'] 	= $app->getUserStateFromRequest('vapwaiting.status', 'status', 0, 'int');

		$today = getdate();
		$today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);

		//db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut = "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `w`.*')
			->select(array(
				$dbo->qn('s.name', 'service_name'),
				$dbo->qn('e.nickname', 'employee_name'),
				$dbo->qn('u.name', 'username'),
			))
			->from($dbo->qn('#__vikappointments_waitinglist', 'w'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('w.id_service'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('w.id_employee'))
			->leftjoin($dbo->qn('#__users', 'u') . ' ON ' . $dbo->qn('u.id') . ' = ' . $dbo->qn('w.jid'))
			->where(1)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->andWhere(array(
				$dbo->qn('w.email') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('w.phone_number') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('s.name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('e.nickname') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('u.name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
			), 'OR');
		}

		// show active list only if status filter is not set
		if ($filters['status'] == 0)
		{
			$q->where($dbo->qn('w.timestamp') . ' >= ' . $today);
		}
		// show past list if status is set to trashed
		else if ($filters['status'] == -1)
		{
			$q->where($dbo->qn('w.timestamp') . ' < ' . $today);
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

		$new_type = OrderingManager::getSwitchColumnType('waitinglist', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;
		$this->today 	= &$today;
		
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWWAITLIST'), 'vikappointments');

		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newwaiting', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editwaiting', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteWaitingRows', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return $this->filters['status'] != 0;
	}
}
