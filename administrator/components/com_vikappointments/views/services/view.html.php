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
class VikAppointmentsViewservices extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();

		$ordering = OrderingManager::getColumnToOrder('services', 's.ordering', 1);

		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keys'] 		= $app->getUserStateFromRequest('vapservices.keys', 'keys', '', 'string');
		$filters['status']		= $app->getUserStateFromRequest('vapservices.status', 'status', -1, 'int');
		$filters['id_group'] 	= $app->getUserStateFromRequest('vapservices.id_group', 'id_group', 0, 'int');

		// db object

		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut = "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `s`.*')
			->select($dbo->qn('g.name', 'group_name'))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('s.id_group') . ' = ' . $dbo->qn('g.id'))
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2  ? 'DESC' : 'ASC'));

		if (!empty($filters['keys']))
		{
			$q->where($dbo->qn('s.name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('s.published') . ' = ' . $filters['status']);
		}

		if ($filters['id_group'] != 0)
		{
			$q->where($dbo->qn('s.id_group') . ' = ' . $filters['id_group']);
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
		
		// get groups

		$groups = array();

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_group'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows() > 0)
		{
			$groups = $dbo->loadAssocList();
		}
		
		$new_type = OrderingManager::getSwitchColumnType('services', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		// get ordering bounds

		$q = $dbo->getQuery(true)
			->select('MIN(`ordering`) AS `min`, MAX(`ordering`) AS `max`')
			->from($dbo->qn('#__vikappointments_service'));

		$dbo->setQuery($q);
		$dbo->execute();
		$bounds = $dbo->loadAssoc();
		
		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->groups 	= &$groups;
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWSERVICES'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newservice', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editservice', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
			JToolBarHelper::custom('reportsser', 'bars', 'bars', JText::_('VAPREPORTS'), true);
			JToolBarHelper::divider();
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteServices', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return (!empty($this->filters['id_group'])
			|| $this->filters['status'] != -1);
	}
}
