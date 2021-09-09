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
class VikAppointmentsViewcustomf extends JViewUI
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

		$ordering = OrderingManager::getColumnToOrder('customf', 'ordering', 1);

		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keys']	= $app->getUserStateFromRequest("vapcf.keys", 'keys', '', 'string');
		$filters['group'] 	= $app->getUserStateFromRequest("vapcf.group", 'group', 0, 'uint');
		$filters['type']	= $app->getUserStateFromRequest("vapcf.type", 'type', '', 'string');
		$filters['rule']	= $app->getUserStateFromRequest("vapcf.rule", 'rule', -1, 'int');
		$filters['owner'] 	= $app->getUserStateFromRequest("vapcf.owner", 'owner', -1, 'int');
		$filters['status'] 	= $app->getUserStateFromRequest("vapcf.status", 'status', -1, 'int');

		if ($filters['group'] == 1)
		{
			// unset unsupported filters
			$filters['rule'] 	= -1;
			$filters['owner'] 	= -1;
		}

		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters, $filters['group']);
		$navbut	= "";

		$rows = array();

		$inner = $dbo->getQuery(true)
			->select('COUNT(1)')
			->from($dbo->qn('#__vikappointments_cf_service_assoc', 'a'))
			->where($dbo->qn('a.id_field') . ' = ' . $dbo->qn('c.id'));

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `c`.*')
			->select($dbo->qn('e.nickname', 'ename'))
			->select('(' . $inner . ') AS ' . $dbo->qn('services_count'))
			->from($dbo->qn('#__vikappointments_custfields', 'c'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('c.id_employee'))
			->where($dbo->qn('group') . ' = ' . $filters['group'])
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->andWhere(array(
				$dbo->qn('c.name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('e.nickname') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
			), 'OR');
		}

		if (!empty($filters['type']))
		{
			$q->where($dbo->qn('c.type') . ' = ' . $dbo->q($filters['type']));
		}

		if ($filters['rule'] != -1)
		{
			$q->where($dbo->qn('c.rule') . ' = ' . $filters['rule']);
		}

		if ($filters['owner'] != -1)
		{
			if ($filters['owner'] == 1)
			{
				$q->where($dbo->qn('c.id_employee') . ' > 0');
			}
			if ($filters['owner'] == 2)
			{
				$q->having($dbo->qn('services_count') . ' > 0');
			}
			else
			{
				$q->where($dbo->qn('c.id_employee') . ' <= 0');
				$q->having($dbo->qn('services_count') . ' = 0');
			}
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('c.required') . ' = ' . $filters['status']);
		}

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim, $filters['group']);

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}

		$new_type = OrderingManager::getSwitchColumnType('customf', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		// get ordering bounds

		$q = $dbo->getQuery(true)
			->select('MIN(`ordering`) AS `min`, MAX(`ordering`) AS `max`')
			->from($dbo->qn('#__vikappointments_custfields'))
			->where($dbo->qn('group') . ' = ' . $filters['group']);

		$dbo->setQuery($q);
		$dbo->execute();
		$bounds = $dbo->loadAssoc();

		$this->rows 		= &$rows;
		$this->navbut 		= &$navbut;
		$this->ordering 	= &$ordering;
		$this->filters 		= &$filters;
		$this->bounds 		= &$bounds;
		
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWCUSTOMFS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newcustomf', JText::_('VAPNEW'));
			JToolBarHelper::divider();
		}
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editcustomf', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteCustomf', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['group'] > 0
			|| !empty($this->filters['type'])
			|| $this->filters['rule'] != -1
			|| $this->filters['owner'] != -1
			|| $this->filters['status'] != -1);
	}
}
