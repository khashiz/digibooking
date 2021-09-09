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
class VikAppointmentsViewempaccountstat extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_charts();
		VikAppointments::load_datepicker_regional();
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->isEmployee()) {
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		// SET EMPLOYEE TIMEZONE
		VikAppointments::setCurrentTimezone($auth->timezone);
		
		// get services
		$all_services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('s.id', 's.name')))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
			->where($dbo->qn('a.id_employee') . ' = ' . $auth->id)
			->order($dbo->qn('s.name') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$all_services = $dbo->loadAssocList();
		}
		
		// get appointments stats
		$resStats = array();

		$inner = $dbo->getQuery(true)
			->select('COUNT(1)')
			->from($dbo->qn('#__vikappointments_reservation'))
			->where($dbo->qn('id_employee') . ' = ' . $auth->id);

		$q = $dbo->getQuery(true)
			->select('COUNT(1) AS ' . $dbo->qn('conf_count'))
			->select('SUM(' . $dbo->qn('total_cost') . ') AS ' . $dbo->qn('tot_earned'))
			->select('SUM(' . $dbo->qn('tot_paid') . ') AS ' . $dbo->qn('tot_earned_online'))
			->select('(' . $inner . ') AS ' . $dbo->qn('all_count'))
			->from($dbo->qn('#__vikappointments_reservation'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $auth->id,
				$dbo->qn('status') . ' = ' . $dbo->q('CONFIRMED'),
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$resStats = $dbo->loadAssoc();
		}
		
		$keys = $app->getUserStateFromRequest('empaccountstat.keys', 'keysfilter', '', 'string');

		// setup list settings
		$lim  = 10;
		$lim0 = $this->getListLimitStart(array('keys' => $keys));
		
		// get customers
		$customers = array();
		$customersCount = 0;

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `u`.*')
			->select($dbo->qn('c.phone_prefix'))
			->from($dbo->qn('#__vikappointments_users', 'u'))
			->leftjoin($dbo->qn('#__vikappointments_reservation', 'r') . ' ON ' . $dbo->qn('u.id') . ' = ' . $dbo->qn('r.id_user'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.country_2_code') . ' = ' . $dbo->qn('u.country_code'))
			->where($dbo->qn('r.id_employee') . ' = ' . $auth->id)
			->group($dbo->qn('u.id'))
			->order($dbo->qn('u.billing_name') . ' ASC');

		/**
		 * Do not filter the reservations by status as it could be helpful to 
		 * display also the customers that tried to book an appointment
		 * for this employee.
		 */

		if (!empty($keys))
		{
			$q->andWhere(array(
				$dbo->qn('u.billing_name') . ' LIKE ' . $dbo->q("%$keys%"),
				$dbo->qn('u.billing_mail') . ' LIKE ' . $dbo->q("%$keys%"),
				$dbo->qn('u.billing_phone') . ' LIKE ' . $dbo->q("%$keys%"),
			), 'OR');
		}

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$customers = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$customersCount = $dbo->loadResult();
			$pageNav = new JPagination($customersCount, $lim0, $lim);
			$navbut = $pageNav->getPagesLinks();
		}
		
		// get services count
		$q = $dbo->getQuery(true)
			->select('COUNT(1)')
			->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
			->where($dbo->qn('id_employee') . ' = ' . $auth->id);

		$dbo->setQuery($q);
		$dbo->execute();

		$servicesCount = (int) $dbo->loadResult();
		
		// get selected services
		$sel_services = $app->getUserStateFromRequest('empaccountstat.services', 'services', array(), 'uint');
		
		$sel_services_where = '';
		if (count($sel_services) > 0 && count($sel_services) < $servicesCount) {
			$sel_services_where = " AND `r`.`id_service` IN (".implode(',',$sel_services).")";
		}
		
		// LINE CHART
		
		list($start_range, $end_range) = $this->getRangeDates($app);
		
		$line_chart_stat = array();

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('r.id_service'),
				$dbo->qn('s.name', 'sname'),
				'SUM(' . $dbo->qn('r.total_cost') . ') AS ' . $dbo->qn('earning'),
				'COUNT(1) AS ' . $dbo->qn('rescount'),
				'FROM_UNIXTIME(' . $dbo->qn('r.checkin_ts') . ', \'%Y-%c\') AS ' . $dbo->qn('month'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->where(array(
				$dbo->qn('r.id_employee') . ' = ' . $auth->id,
				$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn('r.id_parent') . ' <> -1',
				$dbo->qn('r.checkin_ts') . ' BETWEEN ' . $start_range . ' AND ' . $end_range,
			))
			->group(array(
				$dbo->qn('r.id_service'),
				$dbo->qn('month'),
			))
			->order(array(
				$dbo->qn('r.id_service') . ' ASC',
				$dbo->qn('r.checkin_ts') . ' ASC',
			));

		if (count($sel_services))
		{
			$q->where($dbo->qn('r.id_service') . ' IN (' . implode(',', $sel_services) . ')');
		}

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$line_chart_stat = $dbo->loadAssocList();
		}

		// PIE CHART
		
		$pie_chart_stat = array();

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('r.id_service'),
				$dbo->qn('s.name', 'sname'),
				'SUM(' . $dbo->qn('r.total_cost') . ') AS ' . $dbo->qn('earning'),
				'COUNT(1) AS ' . $dbo->qn('rescount'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->where(array(
				$dbo->qn('r.id_employee') . ' = ' . $auth->id,
				$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn('r.id_parent') . ' <> -1',
				$dbo->qn('r.checkin_ts') . ' BETWEEN ' . $start_range . ' AND ' . $end_range,
			))
			->group($dbo->qn('r.id_service'))
			->order($dbo->qn('r.id_service') . ' ASC');

		if (count($sel_services))
		{
			$q->where($dbo->qn('r.id_service') . ' IN (' . implode(',', $sel_services) . ')');
		}

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$pie_chart_stat = $dbo->loadAssocList();
		}
		
		$this->auth 		= &$auth;
		$this->allServices 	= &$all_services;
		
		$this->resStats 		= &$resStats;
		$this->lineChartStat 	= &$line_chart_stat;
		$this->pieChartStat 	= &$pie_chart_stat;
		$this->startRange 		= &$start_range;
		$this->endRange 		= &$end_range;
		$this->selectedServices = &$sel_services;
		
		$this->customers 		= &$customers;
		$this->customersCount 	= &$customersCount;
		
		$this->navbut 		= &$navbut;
		$this->keysFilter 	= &$keys;
		
		// Display the template
		parent::display($tpl);
	}
	
	/**
	 * Get the request range of dates.
	 *
	 * @return 	array 	The start and end date.
	 */
	private function getRangeDates($app)
	{
		$start_range 	= $app->getUserStateFromRequest('empaccountstat.startrange', 'startrange', '', 'string');
		$end_range 		= $app->getUserStateFromRequest('empaccountstat.endrange', 'endrange', 'string');
		
		if (!empty($start_range))
		{
			$start_range = VikAppointments::createTimestamp($start_range, 0, 0);
			if ($start_range == -1)
			{
				$start_range = '';
			}
		}
		
		if (!empty($end_range))
		{
			$end_range = VikAppointments::createTimestamp($end_range, 23, 59);
			if ($end_range == -1)
			{
				$end_range = '';
			}
		}

		if (intval($start_range) > intval($end_range) || intval($end_range) - intval($start_range) > 86400 * 366 * 3)
		{
			$start_range = $end_range = '';
		}
		
		if (empty($start_range) || empty($end_range))
		{
			$now = getdate();

			$start_range = mktime(0, 0, 0, $now['mon']-2, 1, $now['year']);
			$end_range 	 = mktime(0, 0, 0, $now['mon']+4, 1, $now['year'])-1;
		}
		
		return array($start_range, $end_range);
	}
}
