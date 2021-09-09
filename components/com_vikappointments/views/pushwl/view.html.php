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
class VikAppointmentsViewpushwl extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$itemid = $input->getInt('Itemid', 0);

		$ts 		 = $input->getUint('ts', 0);
		$id_service  = $input->getUint('id_service', 0);
		$id_employee = $input->getInt('id_employee', -1);

		$user = JFactory::getUser();
		$user->phoneNumber = '';
		$user->phonePrefix = '';
		
		if (!$user->guest)
		{
			$q = $dbo->getQuery(true)
				->select(array(
					$dbo->qn('u.billing_phone', 'phoneNumber'),
					$dbo->qn('c.phone_prefix', 'phonePrefix'),
				))
				->from($dbo->qn('#__vikappointments_users', 'u'))
				->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.country_2_code') . ' = ' . $dbo->qn('u.country_code'))
				->where($dbo->qn('u.jid') . ' = ' . $user->id);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				foreach ($dbo->loadAssoc() as $k => $v)
				{
					$user->{$k} = $v;
				}
			}
		}

		if (empty($user->phonePrefix))
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('choose'))
				->from($dbo->qn('#__vikappointments_custfields'))
				->where($dbo->qn('rule') . ' = ' . VAPCustomFields::PHONE_NUMBER);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$c = VikAppointmentsLocations::getCountryFromCode($dbo->loadResult());

				if ($c !== false)
				{
					$user->phonePrefix = $c['phone_prefix'];
				}
			}
		}

		$service_name = "null";

		$q = $dbo->getQuery(true)
			->select($dbo->qn('name'))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . $id_service);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$service_name = $dbo->loadResult();

			$translated_service = VikAppointments::getTranslatedServices($id_service);
			$service_name = VikAppointments::getTranslation($id_service, array("name" => $service_name), $translated_service, 'name', 'name');
		}
		
		$countries = VikAppointmentsLocations::getCountries('phone_prefix');

		$this->timestamp 	= &$ts;
		$this->idService 	= &$id_service;
		$this->idEmployee 	= &$id_employee;
		$this->user 		= &$user;
		$this->countries 	= &$countries;
		$this->serviceName 	= &$service_name;
		$this->itemid 		= &$itemid;
			
		// Display the template
		parent::display($tpl);
	}
}
