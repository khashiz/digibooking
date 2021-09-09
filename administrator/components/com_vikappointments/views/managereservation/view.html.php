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
class VikAppointmentsViewmanagereservation extends JViewUI
{
	/**
	 * Flag for multi-order layout.
	 *
	 * @var boolean
	 */
	private $multiOrder = false;

	/**
	 * VikAppointments view display method.
	 *
	 * @return void
	 */
	function display($tpl = null)
	{	
		VikAppointments::load_font_awesome();
		VikAppointments::load_complex_select();
		VikAppointments::load_currency_js();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$config = UIFactory::getConfig();
		
		$type = $input->getString('type');
		$from = $input->getString('from');
		$tab  = $app->getUserStateFromRequest('vapsaveres.tab', 'tabname', 'reservation_details', 'string');
		
		$args = array();
		$args['id_employee'] 		= $input->getInt('id_emp', 0);
		$args['id_service'] 		= $input->getInt('id_ser');
		$args['checkin_ts'] 		= $input->getInt('day', 0);
		$args['checkin_ts_hour'] 	= $input->getInt('hour', 0);
		$args['checkin_ts_min'] 	= $input->getInt('min', 0);
		$args['people'] 			= $input->getInt('people', 1);
		
		$employee_tz = VikAppointments::getEmployeeTimezone($args['id_employee']);
		VikAppointments::setCurrentTimezone($employee_tz);

		$args['checkin_ts'] = VikAppointments::createTimestamp(
			date($config->get('dateformat'), $args['checkin_ts']),
			$args['checkin_ts_hour'],
			$args['checkin_ts_min']
		);
		
		$reservation = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('`r`.*')
				->select(array(
					$dbo->qn('e.nickname', 'ename'),
					$dbo->qn('e.timezone'),
					$dbo->qn('e.notify'),
					$dbo->qn('s.name', 'sname'),
					$dbo->qn('s.duration', 'sduration'),
					$dbo->qn('j.username'),
					$dbo->qn('u.jid'),
				))
				->from($dbo->qn('#__vikappointments_reservation', 'r'))
				->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
				->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
				->leftjoin($dbo->qn('#__users', 'j') . ' ON ' . $dbo->qn('r.createdby') . ' = ' . $dbo->qn('j.id'))
				->leftjoin($dbo->qn('#__vikappointments_users', 'u') . ' ON ' . $dbo->qn('r.id_user') . ' = ' . $dbo->qn('u.id'))
				->where($dbo->qn('r.id') . ' = ' . $ids[0])
				->where($dbo->qn('r.closure') . ' = 0');

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$reservation = $dbo->loadAssoc();
				
				if ($reservation['id_parent'] == -1)
				{
					// switch layout to multi-order
					$tpl = 'multiorder';
					$this->multiOrder = true;
				}
				else if (!empty($args['id_employee']))
				{
					foreach ($args as $key => $val)
					{
						if (!empty($val))
						{
							$reservation[$key] = $val;
						}
					}

					$q = $dbo->getQuery(true)
						->select(array(
							$dbo->qn('s.name', 'sname'),
							$dbo->qn('s.duration'),
							$dbo->qn('s.price'),
							$dbo->qn('s.priceperpeople'),
							$dbo->qn('e.nickname', 'ename'),
							$dbo->qn('e.timezone'),
							$dbo->qn('e.notify'),
							$dbo->qn('a.rate'),
						))
						->from($dbo->qn('#__vikappointments_service', 's'))
						->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('a.id_service') . ' = ' . $dbo->qn('s.id'))
						->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('a.id_employee') . ' = ' . $dbo->qn('e.id'))
						->where(array(
							$dbo->qn('s.id') . ' = ' . $args['id_service'],
							$dbo->qn('e.id') . ' = ' . $args['id_employee'],
						));
				
					$dbo->setQuery($q, 0, 1);
					$dbo->execute();

					if ($dbo->getNumRows())
					{
						$_app = $dbo->loadAssoc();
						$reservation['ename'] 		= $_app['ename'];
						$reservation['sname'] 		= $_app['sname'];
						$reservation['duration'] 	= $_app['duration'];
						$reservation['total_cost'] 	= $_app['rate'];
						$reservation['timezone'] 	= $_app['timezone'];
						$reservation['notify'] 		= $_app['notify'];

						/**
						 * Calculate the reservation cost using the special rates.
						 *
						 * @since 1.6
						 */
						$trace = array('id_user' => (int) $reservation['jid']);

						$reservation['total_cost'] = VAPSpecialRates::getRate($args['id_service'], $args['id_employee'], $args['checkin_ts'], $args['people'], $trace);

						if ($_app['priceperpeople'])
						{
							$reservation['total_cost'] *= $args['people'];
						}
					}
				} 
			}
		}

		if (empty($reservation))
		{
			if (!$args['id_employee'])
			{
				// go back to the list as we are probably trying to edit a closure record
				$app->redirect('index.php?option=com_vikappointments&task=reservations');
				exit;
			}

			$reservation = $this->getBlankItem($args, $dbo);
		}
		else if ($this->multiOrder)
		{
			$this->allServices = array();

			// If multi order, we should get all the children orders
			// to check if there is only one employee.

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('id_employee', 'view_emp', 'id_service')))
				->from($dbo->qn('#__vikappointments_reservation'))
				->where($dbo->qn('id_parent') . ' = ' . $reservation['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$map = array();

				foreach ($dbo->loadAssocList() as $list)
				{
					// in case the employee has been already set, the last 'view_emp' will be taken
					$map[$list['id_employee']] = $list['view_emp'];

					if (!in_array($list['id_service'], $this->allServices))
					{
						$this->allServices[] = $list['id_service'];
					}
				}

				$employees = array_unique(array_keys($map));

				// make sure there is only one employee
				if (count($employees) == 1)
				{
					// inject employee ID
					$reservation['id_employee'] = $employees[0];
					// inject 'view_emp'
					$reservation['view_emp'] = reset($map);
				}
			}
		}

		// Set the toolbar
		$this->addToolBar($type, $from);

		if (!empty($reservation['timezone']))
		{
			$employee_tz = $reservation['timezone']; 
			VikAppointments::setCurrentTimezone($employee_tz);
		}
		$date = getdate($reservation['checkin_ts']);
		$reservation['day_ts'] = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);

		// get custom fields

		$cf_emp = 0;
		if ($reservation['view_emp'] && $reservation['id_employee'] > 0)
		{
			$cf_emp = $reservation['id_employee'];
		}

		$cf_ser = 0;
		if (!empty($this->allServices))
		{
			$cf_ser = $this->allServices;	
		}
		else
		{
			$cf_ser = $reservation['id_service'];
		}

		$custom_fields = VAPCustomFields::getList(0, $cf_emp, $cf_ser, CF_EXCLUDE_REQUIRED_CHECKBOX);

		// get options
		
		$options = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('o.id', 'o.name', 'o.published')))
			->from($dbo->qn('#__vikappointments_option', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'))
			->where($dbo->qn('a.id_service') . ' = ' . $reservation['id_service'])
			->order($dbo->qn('o.ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$options = $dbo->loadAssocList();
		}

		// get reservation options
		
		$res_opt = array();

		if ($reservation['id'] > 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('a.id', 'a.inc_price', 'a.quantity', 'o.name')))
				->select($dbo->qn('v.name', 'var_name'))
				->from($dbo->qn('#__vikappointments_option', 'o'))
				->leftjoin($dbo->qn('#__vikappointments_res_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'))
				->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('v.id') . ' = ' . $dbo->qn('a.id_variation'))
				->where($dbo->qn('a.id_reservation') . ' = ' . $reservation['id'])
				->order($dbo->qn('a.id') . ' ASC');

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$res_opt = $dbo->loadAssocList();
			}
		}

		// get coupons

		$coupons = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_coupon'))
			->order($dbo->qn('id') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$coupons = $dbo->loadAssocList();
		}

		// get payments
		
		$payments_groups = -1;
		if (!empty($reservation['view_emp']))
		{
			$payments_groups = $reservation['id_employee'];
		}

		$payments = array();

		foreach (VikAppointments::getAllEmployeePayments($payments_groups) as $p)
		{
			$key = $p['published'];

			if (!isset($payments[$key]))
			{
				$payments[$key] = array();
			}

			$payments[$key][] = $p;
		}
		
		// get countries

		$countries = VikAppointmentsLocations::getCountries('phone_prefix');
		
		$this->reservation 		= &$reservation;
		$this->custom_fields 	= &$custom_fields;
		$this->options 			= &$options;
		$this->res_opt 			= &$res_opt;
		$this->payments 		= &$payments;
		$this->countries 		= &$countries;
		$this->coupons 			= &$coupons;
		$this->config 			= &$config;
		$this->tab 				= &$tab;
		$this->employeeTimezone = &$employee_tz;
		$this->from 			= &$from;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Returns a blank item.
	 *
	 * @return 	array 	A blank item for new requests.
	 */
	protected function getBlankItem(array $args, $dbo)
	{
		$reservation = array();

		$q = $dbo->getQuery(true);

		$q->select(array(
			$dbo->qn('s.name', 'sname'),
			$dbo->qn('s.duration', 'sduration'),
			$dbo->qn('s.price'),
			$dbo->qn('s.priceperpeople'),
			$dbo->qn('e.nickname', 'ename'),
			$dbo->qn('e.timezone'),
			$dbo->qn('e.notify'),
			$dbo->qn('a.rate'),
		))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('a.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('a.id_employee') . ' = ' . $dbo->qn('e.id'))
			->where(array(
				$dbo->qn('s.id') . ' = ' . $args['id_service'],
				$dbo->qn('e.id') . ' = ' . $args['id_employee'],
			));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$reservation = $dbo->loadAssoc();

			$reservation['id']					 = -1;
			$reservation['id_employee'] 		 = $args['id_employee'];
			$reservation['id_service'] 			 = $args['id_service'];
			$reservation['checkin_ts'] 			 = $args['checkin_ts'];
			$reservation['duration'] 			 = $reservation['sduration'];
			$reservation['total_cost'] 			 = $reservation['rate'];
			$reservation['people'] 			 	 = $args['people'];
			$reservation['custom_f'] 			 = '';
			$reservation['status'] 			 	 = '';
			$reservation['id_payment'] 			 = -1;
			$reservation['purchaser_nominative'] = '';
			$reservation['purchaser_mail'] 		 = '';
			$reservation['purchaser_phone'] 	 = '';
			$reservation['coupon_str'] 			 = '';
			$reservation['paid'] 				 = 0;
			$reservation['notes'] 				 = '';
			$reservation['uploads'] 			 = '';
			$reservation['view_emp'] 			 = 1;

			/**
			 * Calculate the reservation cost using the special rates.
			 *
			 * @since 1.6
			 */
			$reservation['total_cost'] = VAPSpecialRates::getRate($args['id_service'], $args['id_employee'], $args['checkin_ts'], $args['people']);

			if ($reservation['priceperpeople'])
			{
				$reservation['total_cost'] *= $args['people'];
			}
		}

		return $reservation;
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($type, $from)
	{
		// Add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITRESERVATION'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWRESERVATION'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			if (!$this->multiOrder)
			{
				JToolBarHelper::apply('saveReservation', JText::_('VAPSAVE'));
				JToolBarHelper::save('saveAndCloseReservation', JText::_('VAPSAVEANDCLOSE'));
				JToolBarHelper::custom('saveAndNewReservation', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			}
			else
			{
				JToolBarHelper::apply('saveMultiOrder', JText::_('VAPSAVE'));
				JToolBarHelper::save('saveAndCloseMultiOrder', JText::_('VAPSAVEANDCLOSE'));
			}

			JToolBarHelper::divider();
		}

		if (!$from)
		{
			$from = 'cancelReservation';
		}
		
		JToolBarHelper::cancel($from, JText::_('VAPCANCEL'));
	}
}
