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
class VikAppointmentsViewempmanres extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_datepicker_regional();	

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		$args = array();
		$args['id_ser'] = $input->getInt('id_ser', 0);
		$args['id_res'] = $input->getInt('id_res', 0);
		$args['day'] 	= $input->getString('day', 0);
		$args['hour'] 	= $input->getInt('hour', 0);
		$args['min'] 	= $input->getInt('min', 0);
		$args['people']	= $input->getUint('people', 1);
		
		$cid  = $input->getUint('cid', array());
		$type = count($cid) ? 2 : 1;
		
		if (!empty($args['id_res']))
		{
			$cid  = array($args['id_res']);
			$type = 2;
		}

		// get record
		
		$row = array();

		if ($type == 2)
		{
			$q = $dbo->getQuery(true)
				->select('`r`.*')
				->select(array(
					$dbo->qn('s.name', 'sname'),
					$dbo->qn('s.duration', 'sduration'),
					$dbo->qn('s.max_capacity'),
				))
				->from($dbo->qn('#__vikappointments_reservation', 'r'))
				->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('r.id_service'))
				->where(array(
					$dbo->qn('r.id_employee') . ' = ' . $auth->id,
					$dbo->qn('r.id') . ' = ' . $cid[0],
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$row = $dbo->loadAssoc();
			}
			else
			{
				// reservation not found
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
				exit;
			}
		}
		// get service details for new reservation
		else
		{
			$q = $dbo->getQuery(true)
				->select(array(
					$dbo->qn('s.id', 'id_service'),
					$dbo->qn('s.name', 'sname'),
					$dbo->qn('s.duration', 'sduration'),
					$dbo->qn('a.duration'),
					$dbo->qn('a.rate', 'total_cost'),
					$dbo->qn('s.max_capacity'),
				))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('a.id_service') . ' = ' . $dbo->qn('s.id'))
				->where(array(
					$dbo->qn('s.id') . ' = ' . $args['id_ser'],
					$dbo->qn('a.id_employee') . ' = ' . $auth->id,
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$row = $dbo->loadAssoc();
				
				$row['id'] 					 = -1;
				$row['custom_f'] 			 = '';
				$row['status'] 				 = '';
				$row['id_payment'] 			 = -1;
				$row['purchaser_nominative'] = '';
				$row['purchaser_mail'] 		 = '';
				$row['purchaser_phone'] 	 = '';
				$row['notes'] 				 = '';
				$row['uploads'] 			 = '';
				$row['paid']				 = 0;
				$row['people']				 = $args['people'];
			}
			else
			{
				// bad details
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
				exit;
			}

			/**
			 * Multiply the duration by the provided factor
			 * in order to support dropdown timeline.
			 *
			 * @since 1.6
			 */
			$row['duration'] *= max(array(1, $input->getUint('duration_factor', 1)));
		}
		
		if (count($row) == 0)
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}

		// SET EMPLOYEE TIMEZONE
		VikAppointments::setCurrentTimezone($auth->timezone);
		
		if (!empty($args['day']))
		{
			$row['checkin_ts'] = VikAppointments::createTimestamp(date(VikAppointments::getDateFormat(), $args['day']), $args['hour'], $args['min']);
		}
		
		$date = getdate($row['checkin_ts']);
		$row['day_ts'] = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
		
		// get custom fields

		$custom_fields = VAPCustomFields::getList(0, $auth->id, $row['id_service'], CF_EXCLUDE_REQUIRED_CHECKBOX);

		// get options
		
		$options = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('o.id', 'o.name')))
			->from($dbo->qn('#__vikappointments_option', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'))
			->where($dbo->qn('a.id_service') . ' = ' . $row['id_service'])
			->order($dbo->qn('o.ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$options = $dbo->loadAssocList();
		}

		// get reservation options
		
		$res_opt = array();

		if (!empty($row['id']))
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('a.id', 'a.inc_price', 'a.quantity', 'o.name')))
				->select($dbo->qn('v.name', 'var_name'))
				->from($dbo->qn('#__vikappointments_option', 'o'))
				->leftjoin($dbo->qn('#__vikappointments_res_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'))
				->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('v.id') . ' = ' . $dbo->qn('a.id_variation'))
				->where($dbo->qn('a.id_reservation') . ' = ' . $row['id'])
				->order($dbo->qn('o.ordering') . ' ASC');

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$res_opt = $dbo->loadAssocList();
			}
		}

		$payments = VikAppointments::getAllEmployeePayments($auth->id);
		
		$countries = VikAppointmentsLocations::getCountries('phone_prefix');
		
		$this->auth 			= &$auth;
		$this->row 				= &$row;
		$this->customFields 	= &$custom_fields;
		$this->options 			= &$options;
		$this->res_opt 			= &$res_opt;
		$this->payments 		= &$payments;
		$this->countries 		= &$countries;
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Method used to include the calendar script (only once).
	 *
	 * @param 	string 	$date_format 	The date format.
	 *
	 * @return 	void
	 */
	protected function includeCalendarScript($date_format)
	{
		static $loaded = 0;

		if (!$loaded)
		{
			JFactory::getDocument()->addScriptDeclaration(
<<<JS
jQuery(function(){

	var sel_format 	 = "$date_format";
	var df_separator = sel_format[1];

	sel_format = sel_format.replace(new RegExp('\\\'+df_separator, 'g'), "");

	if (sel_format == "Ymd") {

		Date.prototype.format = "yy"+df_separator+"mm"+df_separator+"dd";

	} else if (sel_format == "mdY") {

		Date.prototype.format = "mm"+df_separator+"dd"+df_separator+"yy";

	} else {

		Date.prototype.format = "dd"+df_separator+"mm"+df_separator+"yy";

	}

	jQuery(document).ready(function() {
		jQuery(".vapinput.calendar").datepicker({
			dateFormat: new Date().format,
		});
	});
	
});
JS
			);

			$loaded = 1;
		}
	}
}
