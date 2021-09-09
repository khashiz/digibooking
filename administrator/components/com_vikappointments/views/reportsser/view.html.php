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
class VikAppointmentsViewreportsser extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();
		VikAppointments::load_charts();
		VikAppointments::load_currency_js();
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		// Set the toolbar
		$this->addToolbar();

		// get services
		$cid = $input->get('cid', array(), 'uint');

		// get range dates
		$start_range = $input->getString('startrange');
		$end_range 	 = $input->getString('endrange');

		list($start_range, $end_range) = $this->getRangeDates($start_range, $end_range);

		// get Y-AXIS value type
		$value_type = $input->get('valuetype');

		// load services list

		$services = array();

		if (count($cid))
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('id', 'name')))
				->from($dbo->qn('#__vikappointments_service'))
				->where($dbo->qn('id') . ' IN (' . implode(',', $cid) . ')');

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				foreach ($dbo->loadAssocList() as $row)
				{
					$services[$row['id']] = $row;
				}
			}
		}
		
		// load charts array

		$charts = array();
		
		foreach ($services as $id => $service)
		{
			// line chart array

			$line_chart_stat = array();

			$q = $dbo->getQuery(true)
				->select(array(
					$dbo->qn('r.id_employee'),
					$dbo->qn('e.nickname', 'ename'),
					'FROM_UNIXTIME(' . $dbo->qn('r.checkin_ts') . ', "%Y-%c") AS ' . $dbo->qn('month'),
					'SUM(' . $dbo->qn('r.total_cost') . ') AS ' . $dbo->qn('earning'),
					'COUNT(1) AS ' . $dbo->qn('rescount'),
				))
				->from($dbo->qn('#__vikappointments_reservation', 'r'))
				->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
				->where(array(
					$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
					$dbo->qn('r.id_service') . ' = ' . $id,
					$dbo->qn('r.id_parent') . ' <> -1',
					$dbo->qn('r.closure') . ' = 0',
					$dbo->qn('r.checkin_ts') . ' BETWEEN ' . $start_range . ' AND ' . $end_range,
				))
				->group($dbo->qn(array('r.id_employee', 'month')))
				->order(array(
					$dbo->qn('e.id') . ' ASC',
					$dbo->qn('r.checkin_ts') . ' ASC',
				));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$line_chart_stat = $dbo->loadAssocList();
			}

			// pie chart array
			
			$pie_chart_stat = array();

			$q = $dbo->getQuery(true)
				->select(array(
					$dbo->qn('r.id_employee'),
					$dbo->qn('e.nickname', 'ename'),
					'SUM(' . $dbo->qn('r.total_cost') . ') AS ' . $dbo->qn('earning'),
					'COUNT(1) AS ' . $dbo->qn('rescount'),
				))
				->from($dbo->qn('#__vikappointments_reservation', 'r'))
				->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
				->where(array(
					$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
					$dbo->qn('r.id_service') . ' = ' . $id,
					$dbo->qn('r.id_parent') . ' <> -1',
					$dbo->qn('r.closure') . ' = 0',
					$dbo->qn('r.checkin_ts') . ' BETWEEN ' . $start_range . ' AND ' . $end_range,
				))
				->group($dbo->qn('r.id_employee'))
				->order($dbo->qn('e.id') . ' ASC');

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$pie_chart_stat = $dbo->loadAssocList();
			}
			
			$charts[] = array(
				'service' 	=> $service,
				'line2d' 	=> $line_chart_stat,
				'pie2d' 	=> $pie_chart_stat,
			);
		}

		if (!$value_type)
		{
			// auto detect value type

			$q = $dbo->getQuery(true)
				->select(1)
				->from($dbo->qn('#__vikappointments_service'))
				->where($dbo->qn('price') . ' > 0')
				->where($dbo->qn('published') . ' = 1');

			$dbo->setQuery($q);
			$dbo->execute();

			// use rescount in case all the services has NO price
			$value_type = $dbo->getNumRows() ? 'earning' : 'rescount';
		}

		$this->charts 		= &$charts;
		$this->cid 			= &$cid;
		$this->startRange 	= &$start_range;
		$this->endRange 	= &$end_range;
		$this->valueType 	= &$value_type;
		
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
		JToolBarHelper::title(JText::_('VAPREPORTSSERTITLE'), 'vikappointments');

		$from = JFactory::getApplication()->input->get('from', 'service');

		if ($from == 'calendar')
		{
			JToolBarHelper::cancel('cancelCalendar', JText::_('VAPCANCEL'));
		}
		else
		{
			JToolBarHelper::cancel('cancelService', JText::_('VAPCANCEL'));
		}
	}
	
	/**
	 * Returns the proper range dates.
	 *
	 * @param 	string 	$start_range 	The starting date.
	 * @param 	string 	$end_range 		The ending date.
	 *
	 * @return 	array 	A list containing the resulting starting (at [0] index) 
	 * 					and ending (at [1] index) range.
	 */
	private function getRangeDates($start_range, $end_range)
	{	
		if (!empty($start_range))
		{
			$start_range = VikAppointments::jcreateTimestamp($start_range, 0, 0);
			
			if ($start_range == -1)
			{
				$start_range = '';
			}
		}
		
		if (!empty($end_range))
		{
			$end_range = VikAppointments::jcreateTimestamp($end_range, 23, 59);
			
			if ($end_range == -1)
			{
				$end_range = '';
			}
		}

		// make sure the start range is lower than the end range and that the selected period is not higher than 3 years
		if (intval($start_range) > intval($end_range) || intval($end_range) - intval($start_range) > 86400 * 366 * 3)
		{
			$start_range = $end_range = '';
		}
		
		if (empty($start_range) || empty($end_range))
		{
			$now = ArasJoomlaVikApp::jgetdate();

			$start_range 	= ArasJoomlaVikApp::jmktime(0, 0, 0, $now['mon'] - 2, 1, $now['year']);
			$end_range 		= ArasJoomlaVikApp::jmktime(0, 0, 0, $now['mon'] + 4, 1, $now['year']) - 1;
		}
		
		return array($start_range, $end_range);
	}	
}