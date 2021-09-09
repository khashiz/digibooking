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
class VikAppointmentsViewgroups extends JViewUI
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
		
		$ordering = OrderingManager::getColumnToOrder('groups', 'ordering', 1);

		$page_type = UIFactory::getConfig()->getUint('pagegroup');
		
		// Set the toolbar
		$this->addToolBar($page_type);

		$filters = array();
		$filters['keysearch'] = $app->getUserStateFromRequest($this->getPoolName($page_type) . '.keysearch', 'keysearch', '', 'string');

		//db object
		$lim  	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters, $page_type);
		$navbut = "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `g`.*, COUNT(1) AS `count`')
			->group($dbo->qn('g.id'))
			->order($ordering['column'] . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if ($page_type == 1)
		{
			$q->select($dbo->qn('s.id', 'child_id'))
				->from($dbo->qn('#__vikappointments_group', 'g'))
				->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('g.id') . ' = ' . $dbo->qn('s.id_group'));
		}
		else
		{
			$q->select($dbo->qn('e.id', 'child_id'))
				->from($dbo->qn('#__vikappointments_employee_group', 'g'))
				->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('g.id') . ' = ' . $dbo->qn('e.id_group'));
		}

		if (strlen($filters['keysearch']))
		{
			$q->where($dbo->qn('g.name') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"));
		}
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim, $page_type);

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}
		
		$new_type = OrderingManager::getSwitchColumnType('groups', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		// get ordering bounds

		$q = $dbo->getQuery(true)
			->select('MIN(`ordering`) AS `min`, MAX(`ordering`) AS `max`')
			->from($dbo->qn('#__vikappointments' . ($page_type == 1 ? '' : '_employee') . '_group'));

		$dbo->setQuery($q);
		$dbo->execute();
		$bounds = $dbo->loadAssoc();
		
		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;
		$this->pageType = &$page_type;
		$this->bounds 	= &$bounds;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($page)
	{
		// Add menu title and some buttons to the page	
		JToolBarHelper::title(JText::_($page == 1 ? 'VAPMAINTITLEVIEWGROUPS' : 'VAPMAINTITLEVIEWEMPGROUPS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew($page == 1 ? 'newgroup' : 'newempgroup', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList($page == 1 ? 'editgroup' : 'editempgroup', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}
		
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), $page == 1 ? 'deleteGroups' : 'deleteEmployeeGroups', JText::_('VAPDELETE'));
		}
	}
}
