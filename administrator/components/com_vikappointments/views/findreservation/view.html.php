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
class VikAppointmentsViewfindreservation extends JViewUI
{	
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_font_awesome();
		VikAppointments::load_complex_select();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;	
		$dbo 	= JFactory::getDbo();
		$config = UIFactory::getConfig();

		// Set the toolbar
		$this->addToolBar();
		
		$id_emp = $app->getUserStateFromRequest('findres.id_emp', 'id_emp', -1, 'int');
		$id_ser = $app->getUserStateFromRequest('findres.id_ser', 'id_ser', -1, 'int');

		$id_res 	 = $input->getInt('id_res', -1);
		$last_day 	 = $input->getUint('last_day');
		$search_mode = $input->getUint('searchmode');

		if (empty($id_ser))
		{
			$id_ser = -1;
		}

		if (empty($id_emp))
		{
			$id_emp = -1;
		}
		
		$store_sm = $config->getUint('findresmode');

		if (empty($search_mode))
		{
			$search_mode = $store_sm;
		}
		else if ($store_sm != $search_mode)
		{
			$config->set('findresmode', $search_mode);
		}
		
		$employees = array();
		$services  = array();
		
		// search by employee > service
		if ($search_mode == 1)
		{
			// get employees (sorted by the total number of assigned reservations)
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

				if ($id_emp <= 0)
				{
					$id_emp = $employees[0]['id'];
				}
			}
			
			// get services
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('s.id', 's.name', 's.duration', 's.max_capacity')))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
				->where($dbo->qn('a.id_employee') . ' = ' . $id_emp)
				->order(array(
					$dbo->qn('s.published') . ' DESC',
					$dbo->qn('s.name') . ' ASC',
				));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$services = $dbo->loadAssocList();

				/**
				 * After switching search mode, we can face this scenario:
				 * - id_service = X
				 * - id_employee = ALL EMPLOYEES
				 *
				 * is switched to:
				 * - id_employee = Y (the first employee available)
				 * - id_service = X
				 *
				 * Since the id_service is not changed, we need to make
				 * sure that it is supported by the selected employee.
				 * If not so, we need to use the first service available.
				 *
				 * @since 1.6 
				 */
				if ($id_ser <= 0 || !$this->isSupported($id_ser, $services))
				{
					$id_ser = $services[0]['id'];
				}
			}
		}
		// search by service > employee
		else
		{
			// get services
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('s.id', 's.name', 's.duration', 's.max_capacity')))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->order(array(
					$dbo->qn('s.published') . ' DESC',
					$dbo->qn('s.name') . ' ASC',
				));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$services = $dbo->loadAssocList();

				if ($id_ser <= 0)
				{
					$id_ser = $services[0]['id'];
				}
			}
			
			// get employees (sorted by the total number of assigned reservations)
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('e.id', 'e.nickname')))
				->from($dbo->qn('#__vikappointments_employee', 'e'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('a.id_employee'))
				->where($dbo->qn('a.id_service') . ' = ' . $id_ser)
				->order(array(
					$dbo->qn('e.lastname') . ' ASC',
					$dbo->qn('e.firstname') . ' ASC',
				));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$employees = $dbo->loadAssocList();

				/**
				 * After switching service, we can face this scenario
				 * - id_service = X
				 * - id_employee = Y
				 *
				 * then:
				 * - id_service = Z
				 * - id_employee = Y
				 *
				 * Since the id_employee is not changed, we need to make
				 * sure that it is supported by the selected service.
				 * If not so, we need to unset the selected employee.
				 *
				 * @since 1.6 
				 */
				if ($id_emp > 0 && !$this->isSupported($id_emp, $employees))
				{
					$id_emp = -1;
				}

				if ($id_emp <= 0 && count($employees) == 1)
				{
					// if the list contains only one employee, use it
					$id_emp = $employees[0]['id'];
				}
			}
		}
		
		$people_sel_res = 1;

		if ($id_res != -1)
		{
			// retrieve last number of people related to the given reservation
			$q = $dbo->getQuery(true)
				->select($dbo->qn('people'))
				->from($dbo->qn('#__vikappointments_reservation'))
				->where($dbo->qn('id') . ' = ' . $id_res);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$people_sel_res = $dbo->loadResult();
			}
		}
		
		$arr = getdate();
		$arr = getdate(mktime(0, 0, 0, $arr['mon'], 1, $arr['year']));

		$start 	= $arr[0];
		$end 	= $arr[0] + (60 * 60 * 24 * 366);
		
		// get the reservations for the next 365 days
		$bookings = VikAppointments::getAllEmployeeReservations($id_emp, $id_ser, $start, $end, $dbo);
		
		$this->services 		= &$services;
		$this->employees 		= &$employees;
		$this->bookings 		= &$bookings;
		$this->id_emp 			= &$id_emp;
		$this->id_ser 			= &$id_ser;
		$this->id_res 			= &$id_res;
		$this->last_day 		= &$last_day;
		$this->people_sel_res 	= &$people_sel_res;
		$this->searchMode 		= &$search_mode;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		//Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLEFINDRESERVATION'), 'vikappointments');
		
		JToolBarHelper::cancel('cancelReservation', JText::_('VAPCANCEL'));
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
