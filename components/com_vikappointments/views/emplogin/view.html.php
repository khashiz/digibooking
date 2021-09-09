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
class VikAppointmentsViewemplogin extends JViewUI
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

		$auth = EmployeeAuth::getInstance();

		if ($auth->isEmployee())
		{
			$id_ser 	= $app->getUserStateFromRequest('emplogin.id_ser', 'service', -1, 'int');
			$year 		= $app->getUserStateFromRequest('emplogin.year', 'year', -1, 'int');
			$sel_month 	= $app->getUserStateFromRequest('emplogin.month', 'month', 0, 'int');

			$id_res 	= $input->getInt('id_res', -1);
			$last_day 	= $input->getInt('last_day', -1);
		
			$services = array();
			$bookings = array();
			$upcoming = array();

			$navbut = '';

			// SET EMPLOYEE TIMEZONE
			VikAppointments::setCurrentTimezone($auth->timezone);
			
			// get employee settings
			$settings = VikAppointments::getEmployeeSettings($auth->id);

			// get employee services
			$q = $dbo->getQuery(true)
				->select(array(
					$dbo->qn('s.name', 'sname'),
					$dbo->qn('s.id', 'sid'),
					$dbo->qn('s.max_capacity'),
					$dbo->qn('s.min_per_res'),
					$dbo->qn('s.max_per_res'),
					$dbo->qn('s.checkout_selection'),
					$dbo->qn('a.rate', 'price'),
					$dbo->qn('a.duration'),
					$dbo->qn('g.name', 'gname'),
					$dbo->qn('g.id', 'gid'),
				))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('g.id') . ' = ' . $dbo->qn('s.id_group'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
				->where($dbo->qn('a.id_employee') . ' = ' . $auth->id)
				->order(array(
					$dbo->qn('g.ordering') . ' ASC',
					$dbo->qn('s.ordering') . ' ASC',
				));
			
			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$services = $dbo->loadAssocList();

				if ($id_ser <= 0)
				{
					$id_ser = $services[0]['sid'];
				}
			}

			// calculate calendar options
			$arr = ArasJoomlaVikApp::jgetdate();

			if (empty($sel_month))
			{
				$sel_month = $arr['mon'];
				if ($settings['firstmonth'] != -1)
				{
					$sel_month = $settings['firstmonth'];
				}
			}
		
			if ($year == -1)
			{
				$year = $arr['year'];
			}
			
			$arr 	 = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime(0, 0, 0, $sel_month, 1, $year));
			$start 	 = $arr[0];
			$totdays = $settings['numcals'] * 31;
			$end 	 = $arr[0] + (60 * 60 * 24 * $totdays);
			
			// get employee reservations within the given time range
			$bookings = VikAppointments::getAllEmployeeReservations($auth->id, $id_ser, $start, $end, $dbo);
			
			// get reservation details
			$lim  = $settings['listlimit']; // get from param
			$lim0 = $app->getUserStateFromRequest('emplogin.limitstart', 'limitstart', 0, 'uint');

			$q = $dbo->getQuery(true)
				->select('SQL_CALC_FOUND_ROWS `r`.*')
				->select($dbo->qn('s.name', 'sername'))
				->from($dbo->qn('#__vikappointments_reservation', 'r'))
				->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
				->where(array(
					$dbo->qn('r.id_employee') . ' = ' . $auth->id,
					$dbo->qn('r.id_parent') . ' <> -1',
					$dbo->qn('r.closure') . ' = 0',
					$dbo->qn('r.checkin_ts') . ' > ' . time(),
					$dbo->qn('r.status') . ' IN (\'CONFIRMED\', \'PENDING\')',
				))
				->order($dbo->qn('r.checkin_ts') . ' ' . $settings['listordering']);
			
			$dbo->setQuery($q, $lim0, $lim);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$upcoming = $dbo->loadAssocList();
				
				$dbo->setQuery('SELECT FOUND_ROWS();');
				jimport('joomla.html.pagination');
				$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
				$navbut = $pageNav->getPagesLinks();	
			}

			$selected_service = $this->getSelectedService($id_ser, $services);

			if ($selected_service && $selected_service['checkout_selection'])
			{
				$data['formname'] = 'empareaForm';
				$data['autofill'] = false;

				/**
				 * In case the checkout selection is allowed, we need to include
				 * the script used to handle the checkin/checkout events.
				 *
				 * @since 1.6
				 */
				$js = JLayoutHelper::render('javascript.timeline.dropdown', $data);
				$this->document->addScriptDeclaration($js);

				$this->checkoutSelection = 1;
			}

			$this->services 	= &$services;
			$this->id_ser 		= &$id_ser;
			$this->id_res 		= &$id_res;
			$this->year 		= &$year;
			$this->selMonth 	= &$sel_month;
			$this->lastDay 		= &$last_day;
			$this->bookings 	= &$bookings;
			$this->upcoming 	= &$upcoming;
			$this->navbut 		= &$navbut;
			$this->empSettings 	= &$settings;
		}
		else
		{
			$tpl = 'login';
		}

		$this->auth 	= &$auth;
		$this->itemid 	= $input->getInt('Itemid', 0);

		// prepare page content
		VikAppointments::prepareContent($this);
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Finds the specified service located in the given array.
	 *
	 * @param 	integer  $id_ser 	The service to search.
	 * @param 	array 	 $services 	The haystack.
	 *
	 * @return 	mixed 	 The service array on success, otherwise false.
	 *
	 * @since 	1.6
	 */
	protected function getSelectedService($id_ser, array $services)
	{
		foreach ($services as $s)
		{
			if ($s['sid'] == $id_ser)
			{
				return $s;
			}
		}

		return false;
	}
}
