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
class VikAppointmentsViewemployees extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return void
	 */
	function display($tpl = null)
	{
		AppointmentsHelper::load_css_js();

		$app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();

		$ordering = OrderingManager::getColumnToOrder('employees', 'id', 1);

		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keys'] 		= $app->getUserStateFromRequest('vapemployees.keys', 'keys', '', 'string');
		$filters['id_group'] 	= $app->getUserStateFromRequest('vapemployees.group', 'id_group', 0, 'int');

		// db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `e`.*')
			->select(array($dbo->qn('g.id', 'gid'), $dbo->qn('g.name', 'gname')))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_employee_group', 'g') . ' ON ' . $dbo->qn('e.id_group') . ' = ' . $dbo->qn('g.id'))
			->where("1")
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (!empty($filters['id_group']))
		{
			$q->where($dbo->qn('e.id_group') . ' = ' . $filters['id_group'], 'AND');
		}

		if (strlen($filters['keys']))
		{
			$q->andWhere(array(
				$dbo->qn('firstname') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('lastname') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('email') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
			), 'OR');
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

		// load groups
		
		$groups = array();

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_employee_group'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}
		
		$new_type = OrderingManager::getSwitchColumnType('employees', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);
		
		$this->rows 	= &$rows;
		$this->groups 	= &$groups;
		$this->lim0 	= &$lim0;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;
		
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWEMPLOYEES'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newemployee', JText::_('VAPNEW'));
			JToolBarHelper::spacer();
			JToolBarHelper::custom('duplicateEmployee', 'copy', 'copy', JText::_('VAPCLONE'), true);
			JToolBarHelper::divider();
		}
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editemployee', JText::_('VAPEDIT'));
			JToolBarHelper::divider();
			JToolBarHelper::custom('reportsemp', 'bars', 'bars', JText::_('VAPREPORTS'), true);
			JToolBarHelper::divider();
			JToolBarHelper::custom('manageclosure', 'unpublish', 'unpublish', JText::_('VAPBLOCK'));
			JToolBarHelper::divider();
		}
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteEmployees', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return !empty($this->filters['id_group']);
	}
}
