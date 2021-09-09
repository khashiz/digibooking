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

UILoader::import('libraries.calendar.wrapper');

/**
 * VikAppointments View
 */
class VikAppointmentsViewcaldays extends JViewUI
{
	/**
	 * A list of supported colors.
	 *
	 * @var array
	 */
	private $colors = array(
		'ff1c68', // red
		'ff9526', // orange
		'ffcd33', // yellow
		'5ddd4b', // green
		'0094fb', // blue
		'ce6edd', // violet
		'a28461', // brown
	);

	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		/**
		 * It seems that Joomla stopped loading JS core for
		 * the views loaded with tmpl component. We need to force
		 * it to let the pagination accessing Joomla object.
		 *
		 * @since Joomla 3.8.7
		 */
		JHtml::_('behavior.core');

		VikAppointments::load_colorpicker();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$layout = $input->getString('mode');

		$filters = array();

		if ($layout == 'day')
		{
			$filters['date'] 	 = $input->getString('date', '');
			$filters['employee'] = 0;
            
			// get all reservations for the specified date
			$begin = $filters['date'] ? VikAppointments::jcreateTimestamp($filters['date'], 0, 0) : strtotime('today 00:00:00');
			$end   = strtotime('23:59:59', $begin);
		}
		else
		{
			$filters['date'] 	 = $app->getUserStateFromRequest('caldays.date', 'date', '', 'string');
          
			$filters['employee'] = $app->getUserStateFromRequest('caldays.employee', 'employee', 0, 'uint');
            //file_put_contents(JPATH_ROOT.'/debug/today.txt', print_r(strtotime('today 00:00:00'), true)); 
			// get all reservations between the specified day and the next 7 days
			$begin = $filters['date'] ? VikAppointments::jcreateTimestamp($filters['date'], 0, 0) : strtotime('today 00:00:00');
            
			//$end   = strtotime('+7 days 00:00:00', $begin);
			$end   = addDay($begin,7);
		}

		$filters['services'] = $input->get('services', array(), 'uint');

		if ($input->getBool('employee_changed'))
		{
			// unset selected services every time the employee changes
			$filters['services'] = array();
		}

		// update filter too
		$filters['date'] = $begin;

		// get employees
		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'nickname')))
			->from($dbo->qn('#__vikappointments_employee'))
			->order(array(
				$dbo->qn('lastname') . ' ASC',
				$dbo->qn('firstname') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadObjectList();
		}

		// get services
		$services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('s.id', 's.name', 's.color')))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->where(1)
			->order($dbo->qn('s.ordering') . ' ASC');

		// if the employee is set, obtain only the related services

		if ($filters['employee'])
		{
			$q->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'));
			$q->where($dbo->qn('a.id_employee') . ' = ' . $filters['employee']);
		}

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$colors_map  = array();
			$color_index = 0;

			$services = $dbo->loadObjectList();

			foreach ($services as $i => $s)
			{
				if (empty($s->color) || strlen($s->color) == 1)
				{
					if (!isset($colors_map[$s->id]))
					{
						$colors_map[$s->id] = $color_index;
						$color_index = ($color_index + 1) % count($this->colors);
					}

					$services[$i]->color = $this->colors[$colors_map[$s->id]];

					// update service color
					$q = $dbo->getQuery(true)
						->update($dbo->qn('#__vikappointments_service'))
						->set($dbo->qn('color') . ' = ' . $dbo->q($services[$i]->color))
						->where($dbo->qn('id') . ' = ' . $s->id);

					$dbo->setQuery($q);
					$dbo->execute();
				}
			}
		}

		// find appointments
		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn(array(
			'r.id',
			'r.id_service',
			'r.id_employee',
			'r.checkin_ts',
			'r.duration',
			'r.people',
			'r.total_cost',
			'r.purchaser_nominative',
		)));
		$q->select($dbo->qn('e.nickname', 'employee_name'));
		$q->select($dbo->qn('s.name', 'service_name'));
		$q->select($dbo->qn('s.color', 'service_color'));

		$q->from($dbo->qn('#__vikappointments_reservation', 'r'));
		$q->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('r.id_employee'));
		$q->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('r.id_service'));

		$q->where(array(
			$dbo->qn('r.status') . ' IN (\'CONFIRMED\', \'PENDING\')',
			$dbo->qn('r.closure') . ' = 0',
			$dbo->qn('r.id_parent') . ' <> -1',
		));

		if ($layout == 'day')
		{
			/**
			 * It is needed to intersect the delimiters with checkin and checkout in order
			 * to retrieve also the appointments that start on a day and ends on the next one.
			 */

			$q->andWhere(array(
				$dbo->qn('r.checkin_ts') . ' BETWEEN ' . $begin . ' AND ' . $end,
				'(' . $dbo->qn('r.checkin_ts') . ' + ' . $dbo->qn('r.duration') . ' * 60) BETWEEN ' . $begin . ' AND ' . $end ,
			));
		}
		else
		{
			$q->where($dbo->qn('r.checkin_ts') . ' BETWEEN ' . $begin . ' AND ' . $end);
		}

		if ($filters['employee'])
		{
			$q->where($dbo->qn('r.id_employee') . ' = ' . $filters['employee']);
		}

		if (($count = count($filters['services'])) && $count != count($services))
		{
			$q->where($dbo->qn('r.id_service') . ' IN (' . implode(', ', $filters['services']) . ')');
		}

		$q->order($dbo->qn('r.checkin_ts') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadObjectList();
		}

		if ($layout == 'day')
		{
			// create a calendar wrapper for each employee
			$this->groupReservationsByEmployee($rows, $employees);
		}

		// create calendar wrapper
		$rows = $this->groupReservations($rows);

		// add view references
		$this->calendar  = &$rows;
		$this->filters 	 = &$filters;
		$this->employees = &$employees;
		$this->services  = &$services;
		$this->layout 	 = &$layout;

		// setup toolbar
		$this->addToolBar();

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
			if ($this->filters['employee'] == 0)
			{
				JToolBarHelper::custom('reportsallser', 'bars', 'bars', JText::_('VAPREPORTS'), false);
			}
			else
			{
				JToolBarHelper::custom('reportsemp', 'bars', 'bars', JText::_('VAPREPORTS'), false);
			}

			JToolBarHelper::divider();
		}

		if ($this->layout == 'day')
		{
			JToolBarHelper::cancel('backToCal');
		}
	}

	/**
	 * Groups the reservations depending on their checkin.
	 *
	 * @param 	array 	$rows 	The reservations to group.
	 *
	 * @return 	array 	The grouped reservations.
	 */
	protected function groupReservations(array $rows)
	{
		$wrapper = new CalendarWrapper();

		foreach ($rows as $row)
		{
			$row->checkout_ts = VikAppointments::getCheckout($row->checkin_ts, $row->duration);

			$app = $wrapper->getIntersection($row->checkin_ts, $row->checkout_ts);

			if ($app !== false)
			{
				$app->extendBounds($row->checkin_ts, $row->checkout_ts, $row);
			}
			else
			{
				$wrapper->push(new CalendarRect($row->checkin_ts, $row->checkout_ts, $row));
			}
		}

		return $wrapper;
	}

	/**
	 * Groups the reservations by employee depending on their checkin.
	 *
	 * @param 	array 	$rows 		The reservations to group.
	 * @param 	array 	$employees 	The list of employees to which the calendar should be attached.
	 *
	 * @return 	void
	 *
	 * @uses 	groupReservations()
	 */
	protected function groupReservationsByEmployee(array $rows, array &$employees)
	{
		$tmp = array();

		// group reservations by employee
		foreach ($rows as $row)
		{
			$id_emp = $row->id_employee;

			if (!isset($tmp[$id_emp]))
			{
				$tmp[$id_emp] = array();
			}

			$tmp[$id_emp][] = $row;
		}

		foreach ($employees as $i => $e)
		{
			if (isset($tmp[$e->id]))
			{
				// create calendar wrapper
				$employees[$i]->calendar = $this->groupReservations($tmp[$e->id]);
			}
		}

		// free space
		unset($tmp);
	}
}
