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
class VikAppointmentsViewemployeesearch extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		VikAppointments::load_complex_select();
		VikAppointments::load_fancybox();
		VikAppointments::load_currency_js();
		VikAppointments::load_font_awesome();
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$user 	= JFactory::getUser();

		$itemid = $input->getInt('Itemid', 0);
		
		// request args
		$id_employee = $input->getUint('id_employee', 0);
		$id_ser 	 = $input->getInt('id_service', -1);
		$last_day 	 = $input->getUint('day', 0); // timestamp
		$month 		 = $input->getUint('month', 0);
		$hour 		 = $input->getInt('hour', null);
		$min 		 = $input->getInt('min', null);

		if (VikAppointments::getLoginRequirements() == 3 && !VikAppointments::isUserLogged())
		{
			// login is required
			$tpl = 'login';
		}
		else
		{
			// inner query to calculate the average rating of the employee
			$rating = $dbo->getQuery(true)
				->select('AVG(' . $dbo->qn('re.rating') . ')')
				->from($dbo->qn('#__vikappointments_reviews', 're'))
				->where(array(
					$dbo->qn('e.id') . ' = ' . $dbo->qn('re.id_employee'),
					$dbo->qn('re.published') . ' = 1',
				));

			// get employee details
			$q = $dbo->getQuery(true)
				->select('`e`.*')
				->select('(' . $rating . ') AS ' . $dbo->qn('rating_avg'))
				->from($dbo->qn('#__vikappointments_employee', 'e'))
				->where(array(
					$dbo->qn('e.id') . ' = ' . $id_employee,
					$dbo->qn('e.listable') . ' = 1',
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				$app->enqueueMessage(JText::_('VAPEMPNOTFOUNDERROR'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=employeeslist' . ($itemid ? '&Itemid=' . $itemid : ''), false));
				exit;
			}
			
			$emp = $dbo->loadAssoc();

			$lang_employees = VikAppointments::getTranslatedEmployees();

			$emp['nickname'] = VikAppointments::getTranslation($emp['id'], $emp, $lang_employees, 'nickname', 'nickname');
			$emp['note'] 	 = VikAppointments::getTranslation($emp['id'], $emp, $lang_employees, 'note', 'note');
			
			$this->employee = &$emp;

			// get services assigned to this employee

			$services = array();

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('s.id', 's.name', 's.description', 's.start_publishing', 's.end_publishing', 's.use_recurrence', 's.checkout_selection')))
				->select($dbo->qn(array('s.max_capacity', 's.min_per_res', 's.max_per_res', 's.priceperpeople', 's.app_per_slot', 's.image')))
				->select($dbo->qn(array('a.rate', 'a.duration', 'a.sleep')))
				->select($dbo->qn('a.description', 'override_description'))
				->select(array(
					$dbo->qn('g.id', 'gid'),
					$dbo->qn('g.name', 'gname'),
				))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('g.id') . ' = ' . $dbo->qn('s.id_group'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
				->where(array(
					$dbo->qn('a.id_employee') . ' = ' . $id_employee,
					$dbo->qn('s.published') . ' = 1',
				))
				->andWhere(array(
					$dbo->qn('s.end_publishing') . ' = -1',
					$dbo->qn('s.end_publishing') . ' >= ' . time(),
				), 'OR')
				->order(array(
					$dbo->qn('g.ordering') . ' ASC',
					$dbo->qn('s.ordering') . ' ASC',
				));

			/**
			 * Retrieve only the services the belong to the view
			 * access level of the current user.
			 *
			 * @since 1.6
			 */
			$levels = $user->getAuthorisedViewLevels();

			if ($levels)
			{
				$q->where($dbo->qn('s.level') . ' IN (' . implode(', ', $levels) . ')');
			}

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$services = $dbo->loadAssocList();
				
				$lang_services 	= VikAppointments::getTranslatedServices();
				$lang_groups 	= VikAppointments::getTranslatedGroups();

				foreach ($services as $k => $s)
				{
					$services[$k]['price'] = $s['rate'];

					// translate service
					$services[$k]['name'] 			= VikAppointments::getTranslation($s['id'], $s, $lang_services, 'name', 'name');
					$services[$k]['description'] 	= VikAppointments::getTranslation($s['id'], $s, $lang_services, 'description', 'description');
					// translate group
					$services[$k]['gname']			= VikAppointments::getTranslation($s['gid'], $s, $lang_groups, 'gname', 'name');

					/**
					 * Append override description to original text.
					 *
					 * @since 1.6.2
					 */
					$services[$k]['description'] .= (string) $s['override_description'];
				}
			}

			/**
			 * Get the selected service to obtain here the details to use.
			 *
			 * @since 1.6
			 */
			$selected_service = $this->getSelectedService($id_ser, $services);
			
			if (!$selected_service && count($services))
			{
				// find the default service
				$id_ser = $services[0]['id'];

				$selected_service = $this->getSelectedService($id_ser, $services);
			}
			
			// set employee timezone
			VikAppointments::setCurrentTimezone($emp['timezone']);
			
			// remove all the expired reservations (switch status from PENDING to REMOVED)
			VikAppointments::removeAllReservationsOutOfTime($emp['id'], $dbo);
			
			// animate the page when the date is specified
			
			$doAnimation = true;
			
			if ($last_day <= 0)
			{
				$last_day = ArasJoomlaVikApp::jgetdate(time());
				$last_day = ArasJoomlaVikApp::jmktime(0, 0, 0, $last_day['mon'], $last_day['mday'], $last_day['year']);
				
				$doAnimation = false;
			}
			else
			{	
				// $last_day is already a UNIX timestamp
			}

			$this->doAnimation = &$doAnimation;

			// get options assigned to the selected service

			$options = array();

			$q = $dbo->getQuery(true)
				->select('`o`.*')
				->select(array(
					$dbo->qn('v.id', 'id_var'),
					$dbo->qn('v.name', 'var_name'),
					$dbo->qn('v.inc_price'),
				))
				->from($dbo->qn('#__vikappointments_option', 'o'))
				->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'))
				->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('v.id_option'))
				->where(array(
					$dbo->qn('a.id_service') . ' = ' . $id_ser,
					$dbo->qn('o.published') . ' = 1',
				))
				->order(array(
					$dbo->qn('o.ordering') . ' ASC',
					$dbo->qn('v.ordering') . ' ASC',
				));
			
			$dbo->setQuery($q);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$options 		= $this->groupOptions($dbo->loadAssocList());
				$lang_options 	= VikAppointments::getTranslatedOptions();

				// translate the options
				foreach ($options as $k => $option)
				{
					$options[$k]['name'] 		= VikAppointments::getTranslation($option['id'], $option, $lang_options, 'name', 'name');
					$options[$k]['description'] = VikAppointments::getTranslation($option['id'], $option, $lang_options, 'description', 'description');

					$vars_json = VikAppointments::getTranslation($option['id'], $option, $lang_options, 'vars_json', 'vars_json', array());

					foreach ($options[$k]['variations'] as $j => $var)
					{
						if (isset($vars_json[$var['id']]))
						{
							$options[$k]['variations'][$j]['name'] = $vars_json[$var['id']];
						}
					}
				}
			}

			// prepare calendar settings
			
			$arr 	=  ArasJoomlaVikApp::jgetdate();
			$year 	= $arr['year'];
			
			$num_cals 		= VikAppointments::getNumberOfCalendars();
			$first_month 	= VikAppointments::getCalendarFirstMonth();
			$first_year 	= VikAppointments::getCalendarFirstYear();

			$check_month = 0;
			$check_year  = 0;
			
			if ($arr['year'] < $first_year || ($arr['year'] == $first_year && $arr['mon'] <= $first_month))
			{
				// The first year/month pair is in the future,
				// we need to use them to start the calendar.
				$check_month = $first_month;
				$check_year  = $first_year;
			}
			else
			{
				// The first year/month pair is in the past,
				// we need to use the current period to start the calendar.
				$check_month = $arr['mon'];
				$check_year  = $arr['year'];
			}
			
			if (empty($month) || $month <= 0 || $month > 12)
			{
				// the selected month is invalid, reset it
				$month = $first_month = $check_month;
				// reset the year too
				$first_year = $year = $check_year; 
			}
			else if ($month < $check_month)
			{
				// the selected month is prior than the first month, reset it
				$first_month = $check_month;
				// reset the year too
				$first_year  = $check_year;

				$year++;
			}
			else
			{
				// all fine, setup the first month/year pair
				$first_month = $check_month;

				$first_year = $year = $check_year;
			}

			// get very first and last timestamps that will be available on the calendar
			$arr 	= ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime( 0, 0, 0, $month, 1, $year));

			/**
			 * Consider service start publishing in case the fetched
			 * timestamp is lower.
			 *
			 * @since 1.6.2
			 */
			if ($selected_service && $arr[0] < (int) $selected_service['start_publishing'])
			{
				// update initial date
				$arr = ArasJoomlaVikApp::jgetdate($selected_service['start_publishing']);

				// update first year and month too
				$first_month = $month = $arr['mon'];
				$first_year  = $year  = $arr['year'];
			}

			$start 	= $arr[0];
			$end 	= $arr[0] + (60 * 60 * 24 * ($num_cals * 31)); // 31 max days in a month
			
			if ($last_day < $start)
			{
				// The last day cannot be used as it is prior than the first timestamp available.
				// Use the very first timestamp instead.
				$last_day = $start;
			}
			else if ($last_day > $end)
			{
				/**
				 * The last day cannot be used as it is after the last timestamp available.
				 * Use the very first timestamp instead.
				 *
				 * @since 1.6
				 */

				$today = ArasJoomlaVikApp::jgetdate();

				if ($arr['mon'] == $today['mon'])
				{
					// same month, use the current day
					$last_day = ArasJoomlaVikApp::jmktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);
				}
				else
				{
					// different month, use the very first day
					$last_day = $start;
				}
			}

			// get all employee bookings
			$bookings = VikAppointments::getAllEmployeeReservations($emp['id'], $id_ser, $start, $end, $dbo);

			// load cart instance
			
			VikAppointments::loadCartLibrary();
			
			$core = new VikAppointmentsCartCore();
			$cart = $core->getCartObject();

			$cart_is_empty = $cart->isEmpty();
			
			// get locations

			$locations = array();

			/**
			 * @todo 	Since the query is using a group by, we need to split
			 * 			`l`.* to retrieve the exact columns that will be used.
			 * 			The STAR/GROUP BY pair is not a SQL standard.
			 */

			$q = $dbo->getQuery(true)
				->select('`l`.*')
				->select($dbo->qn(array(
					'c.country_name',
					'c.country_2_code',
					's.state_name',
					's.state_2_code',
					'ci.city_name',
				)))
				->from($dbo->qn('#__vikappointments_employee_location', 'l'))
				->leftjoin($dbo->qn('#__vikappointments_emp_worktime', 'w') . ' ON ' . $dbo->qn('l.id') . ' = ' . $dbo->qn('w.id_location'))
				->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.id') . ' = ' . $dbo->qn('l.id_country'))
				->leftjoin($dbo->qn('#__vikappointments_states', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('l.id_state'))
				->leftjoin($dbo->qn('#__vikappointments_cities', 'ci') . ' ON ' . $dbo->qn('ci.id') . ' = ' . $dbo->qn('l.id_city'))
				->where(array(
					$dbo->qn('w.id_employee') . ' = ' . $id_employee,
					$dbo->qn('w.id_service') . ' = ' . $id_ser,
				))
				->andWhere(array(
					$dbo->qn('l.id_employee') . ' = -1',
					$dbo->qn('l.id_employee') . ' = ' . $id_employee,
				), 'OR')
				->group($dbo->qn('l.id'));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$locations = $dbo->loadAssocList();
			}

			// get locations from request

			$req_locations = $input->getUint('locations', array());
			
			if (count($req_locations) == 0 && count($locations) > 1)
			{
				// use all the locations if not provided
				foreach ($locations as $loc)
				{
					$req_locations[] = $loc['id'];
				}
			}

			// get reviews

			$base_uri = "index.php?option=com_vikappointments&view=employeesearch&id_employee={$emp['id']}&id_service={$id_ser}";

			$limit_start 	= $input->getUint('limitstart', 0);
			$rev_ord_by 	= $input->getString('revordby', '');
			$rev_ord_mode 	= $input->getString('revordmode', '');
			
			$can_leave_reviews 		= VikAppointments::userCanLeaveEmployeeReview($emp['id']);
			$reviews_ordering_links = VikAppointments::getReviewsOrderingLinks($base_uri, $rev_ord_by, $rev_ord_mode);

			$reviews = VikAppointments::loadReviews('employee', $emp['id'], $limit_start);

			if ($selected_service && $selected_service['checkout_selection'])
			{
				/**
				 * In case the checkout selection is allowed, we need to include
				 * the script used to handle the checkin/checkout events.
				 *
				 * @since 1.6
				 */
				$js = JLayoutHelper::render('javascript.timeline.dropdown');
				$this->document->addScriptDeclaration($js);
			}
			
			// define properties

			$this->services 	= &$services;
			$this->bookings 	= &$bookings;
			$this->id_ser 		= &$id_ser;
			$this->last_day 	= &$last_day;
			$this->defMonth 	= &$first_month;
			$this->year 		= &$year;
			$this->defYear 		= &$first_year;
			$this->options 		= &$options;
			$this->cart_empty 	= &$cart_is_empty;

			/**
			 * @since 1.6
			 */
			$this->selectedService = &$selected_service;
			
			$this->locations 	= &$locations;
			$this->reqLocations = &$req_locations;
			
			$this->reviews 				= &$reviews;
			$this->reviewsOrderingLinks = &$reviews_ordering_links;
			$this->userCanLeaveReview 	= &$can_leave_reviews;
		}

		$this->idEmployee = $id_employee;
		$this->idService  = $id_ser;
		$this->lastDay 	  = $last_day;
		$this->month 	  = $month;
		$this->hour 	  = $hour;
		$this->min 		  = $min;
		$this->itemid 	  = $itemid;

		// prepare view contents and microdata
		VikAppointments::prepareContent($this);

		// extend pathway for breadcrumbs module
		$this->extendPathway($app);
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Groups the list of options with the related variations.
	 *
	 * @param 	array 	$options 	The list of options to group.
	 *
	 * @return 	array 	The grouped list.
	 *
	 * @since 	1.6
	 */
	protected function groupOptions(array $options)
	{
		$arr = array();

		$last_opt_id = -1;

		foreach ($options as $o)
		{
			if ($last_opt_id != $o['id'])
			{
				$last_opt_id 	 = $o['id'];
				$o['variations'] = array();

				$arr[] = $o;
			}

			if (!empty($o['id_var']))
			{
				$arr[count($arr) - 1]['variations'][] = array(
					'id' 		=> $o['id_var'],
					'name' 		=> $o['var_name'],
					'inc_price' => $o['inc_price'],
				);
			}
		}

		return $arr;
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
			if ($s['id'] == $id_ser)
			{
				return $s;
			}
		}

		return false;
	}

	/**
	 * Extends the pathway for breadcrumbs module.
	 *
	 * @param 	mixed 	$app  The application instance.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	protected function extendPathway($app)
	{
		$pathway = $app->getPathway();
		$items   = $pathway->getPathway();
		$last 	 = end($items);

		$name = $this->employee['nickname'];
		$id   = $this->employee['id'];

		// Make sure this employee is not a menu item, otherwise
		// the pathway will display something like:
		// Home > Menu > Item > Item
		if ($last && strpos($last->link, '&id_employee=' . $id) === false)
		{
			$link = 'index.php?option=com_vikappointments&view=employeesearch&id_employee=' . $id;

			$pathway->addItem($name, $link);
		}
	}
}
