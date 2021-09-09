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
class VikAppointmentsViewcalendar extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_complex_select();

		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();	
		
		$id_emp = $app->getUserStateFromRequest('calendar.id_emp', 'id_emp', -1, 'int');
		$id_ser = $app->getUserStateFromRequest('calendar.id_ser', 'id_ser', -1, 'int');
		$year 	= $app->getUserStateFromRequest('calendar.year', 'year', -1, 'int');
		
		// Set the toolbar
		$this->addToolBar();
		
		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('e.id', 'e.nickname')))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_reservation', 'r') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('r.id_employee'))
			->group($dbo->qn('e.id'))
			->order('COUNT(' . $dbo->qn('r.id') . ') DESC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList();
		}
		
		if ($id_emp <= 0 && count($employees))
		{
			$id_emp = $employees[0]['id'];
		}
		
		$services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('s.id', 's.name', 's.duration', 's.max_capacity')))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
			->where($dbo->qn('a.id_employee') . ' = ' . $id_emp)
			->order($dbo->qn('s.name') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$services = $dbo->loadAssocList();
		}

		/**
		 * After switching employee, we can face this scenario:
		 * - id_employee = X
		 * - id_service = Y
		 *
		 * then:
		 * - id_employee = Z
		 * - id_service = Y
		 *
		 * Since the id_service is not changed, we need to make
		 * sure that it is supported by the selected employee.
		 * If not so, we need to unset the selected service.
		 *
		 * @since 1.6 
		 */
		if (!$this->isSupported($id_ser, $services))
		{
			$id_ser = -1;
		}
		
		if ($id_ser <= 0 && count($services))
		{
			$id_ser = $services[0]['id'];
		}
		
		$employee_tz = VikAppointments::getEmployeeTimezone($id_emp);
		VikAppointments::setCurrentTimezone($employee_tz);
		
		$arr = ArasJoomlaVikApp::jgetdate();
		if ($year == -1)
		{
			$year = $arr['year'];
		}
		
		$arr = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime(0, 0, 0, 1, 1, $year));
		$start 	= $arr[0];
		$end 	= $arr[0] + (60 * 60 * 24 * 366);
		
		$bookings = VikAppointments::getAllEmployeeReservations($id_emp, $id_ser, $start, $end, $dbo);
		
		UILoader::import('libraries.models.statistics');

		$statistics = new VikAppointmentsStatistics($year, $id_emp, $id_ser);
		
		$this->services 		= &$services;
		$this->employees 		= &$employees;
		$this->bookings 		= &$bookings;
		$this->id_emp 			= &$id_emp;
		$this->id_ser 			= &$id_ser;
		$this->year 			= &$year;
		$this->statistics 		= &$statistics;
		$this->employeeTimezone = &$employee_tz;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		// Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWCALENDAR'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::custom('reportsall', 'bars', 'bars', JText::_('VAPREPORTS'), false);
			JToolBarHelper::divider();
		}
	}

	/**
	 * Checks if the specified service/employee is contained within the list.
	 *
	 * @param 	integer  $needle 	The service/employee to search.
	 * @param 	array 	 $haystack 	The haystack.
	 *
	 * @return 	boolean  True if supported, otherwise false.
	 *
	 * @since 	1.6
	 */
	protected function isSupported($needle, array $haystack)
	{
		foreach ($haystack as $tmp)
		{
			if ($tmp['id'] == $needle)
			{
				return true;
			}
		}

		return false;
	}
}
