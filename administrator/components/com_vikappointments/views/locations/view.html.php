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
class VikAppointmentsViewlocations extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		AppointmentsHelper::load_css_js();
		VikAppointments::load_googlemaps();

		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();

		$ordering = OrderingManager::getColumnToOrder('locations', 'id', 1);

		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keys'] = $app->getUserStateFromRequest('vaploc.keys', 'keys', '', 'string');

		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut = "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `l`.*')
			->select($dbo->qn(array('e.nickname', 'c.country_name', 's.state_name')))
			->from($dbo->qn('#__vikappointments_employee_location', 'l'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('l.id_employee'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.id') . ' = ' . $dbo->qn('l.id_country'))
			->leftjoin($dbo->qn('#__vikappointments_states', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('l.id_state'))
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));
		
		if (strlen($filters['keys']))
		{
			$q->where($dbo->qn('name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
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

		// get all coordinates

		$js_coordinates = array();

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('name'),
				$dbo->qn('latitude', 'lat'),
				$dbo->qn('longitude', 'lng'),
			))
			->from($dbo->qn('#__vikappointments_employee_location'))
			->where(array(
				$dbo->qn('latitude') . ' IS NOT NULL',
				$dbo->qn('longitude') . ' IS NOT NULL',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$js_coordinates = $dbo->loadAssocList();
		}

		$new_type = OrderingManager::getSwitchColumnType('locations', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);
		
		$this->rows 			= &$rows;
		$this->jsCoordinates 	= &$js_coordinates;
		$this->navbut 			= &$navbut;
		$this->ordering 		= &$ordering;
		$this->filters 			= &$filters;
		
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWLOCATIONS'), 'vikappointments');
	
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newlocation', JText::_('VAPNEW'));
			JToolBarHelper::divider();  
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editlocation', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteLocations', JText::_('VAPDELETE'));
		}
	}
}
