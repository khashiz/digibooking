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
class VikAppointmentsViewrates extends JViewUI
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

		$ordering = OrderingManager::getColumnToOrder('rates', 'id', 2);

		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keys'] 		= $app->getUserStateFromRequest('vapsprates.keys', 'keys', '', 'string');
		$filters['status'] 		= $app->getUserStateFromRequest('vapsprates.status', 'status', -1, 'int');
		// not used as filter (needed to retrieve the rates assigned to the given service)
		$filters['id_service'] 	= $app->getUserStateFromRequest('vapsprates.service', 'id_service', 0, 'uint');

		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `r`.*')
			->from($dbo->qn('#__vikappointments_special_rates', 'r'))
			->where(1)
			->order($dbo->qn("r.{$ordering['column']}") . ' ' . ($ordering['type'] == 2  ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->andWhere(array(
				$dbo->qn('r.name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('r.description') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
			), 'OR');
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('r.published') . ' = ' . $filters['status']);
		}

		if ($filters['id_service'] > 0)
		{
			$q->leftjoin($dbo->qn('#__vikappointments_ser_rates_assoc', 'a') . ' ON ' . $dbo->qn('r.id') . ' = ' . $dbo->qn('a.id_special_rate'));
			$q->where($dbo->qn('a.id_service') . ' = ' . $filters['id_service']);
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

		$new_type = OrderingManager::getSwitchColumnType('rates', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		foreach ($rows as $i => $row)
		{
			$rows[$i]['services'] = array();

			$q = $dbo->getQuery(true)
				->select($dbo->qn('s.name'))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_ser_rates_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
				->where($dbo->qn('a.id_special_rate') . ' = ' . $row['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$rows[$i]['services'] = $dbo->loadColumn();
			}
		}

		$this->rows 	= &$rows;
		$this->navbut	= &$navbut;
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWSPECIALRATES'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newspecialrate', JText::_('VAPNEW'));
			JToolBarHelper::divider();
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editspecialrate', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteSpecialRates', JText::_('VAPDELETE'));
		}

		JToolBarHelper::cancel('cancelService', JText::_('VAPCANCEL'));
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['status'] != -1);
	}
}
