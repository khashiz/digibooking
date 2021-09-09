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
class VikAppointmentsViewratestest extends JViewUI
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
		VikAppointments::load_currency_js();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$layout = $input->get('layout');

		$args = array();

		if ($layout == 'quick')
		{
			/**
			 * QUICK LAYOUT
			 */

			$args['id_service'] 	= $input->getUint('id_service', 0);
			$args['id_employee'] 	= $input->getUint('id_employee', 0);
			$args['checkin'] 		= $input->getUint('checkin', 0);
			$args['people'] 		= $input->getUint('people', 1);

			$args['id_user']	= $input->getUint('uid', 0);
			$args['juser']		= $input->getUint('jid', 0);
			$args['usergroup']	= $input->getUint('usergroup', 0);

			$service = new stdClass;

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('max_capacity', 'priceperpeople')))
				->from($dbo->qn('#__vikappointments_service'))
				->where($dbo->qn('id') . ' = ' . $args['id_service']);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				throw new Exception("Service not found", 404);
			}

			$service = $dbo->loadObject();

			$trace 	= array('debug' => array());

			if ($args['usergroup'])
			{
				// inject property to force usergroup
				$trace['usergroup']	= $args['usergroup'];
			}
			else if ($args['juser'])
			{
				// inject property to force the usergroup 
				// related to the specified Joomla user
				$trace['id_user'] = $args['juser'];
			}
			else if ($args['id_user'])
			{
				// inject property to force the usergroup 
				// related to the specified customer
				$q = $dbo->getQuery(true)
					->select($dbo->qn('jid'))
					->from($dbo->qn('#__vikappointments_users'))
					->where($dbo->qn('id') . ' = ' . $args['id_user']);

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$trace['id_user'] = (int) $dbo->loadResult();
				}
			}

			$rate 	= VAPSpecialRates::getRate($args['id_service'], $args['id_employee'], $args['checkin'], $args['people'], $trace);

			$finalcost = $rate;

			if ($service->max_capacity > 1 && $service->priceperpeople == 1)
			{
				$finalcost *= $args['people'];
			}

			$this->trace 		= &$trace;
			$this->rate  		= &$rate;
			$this->finalCost 	= &$finalcost;

			$this->setLayout($layout);
		}
		else
		{
			/**
			 * DEFAULT LAYOUT
			 */

			$args['id_service'] 	= $app->getUserState('ratestest.id_service', 0);
			$args['id_employee'] 	= $app->getUserState('ratestest.id_employee', 0);
			$args['usergroup'] 		= $app->getUserState('ratestest.usergroup', 0);
			$args['checkin'] 		= $app->getUserState('ratestest.checkin', null);
			$args['people'] 		= $app->getUserState('ratestest.people', 1);
			$args['debug'] 			= $app->getUserState('ratestest.debug', 0);

			// get services

			$services = array();

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('s.id', 's.name', 's.id_group', 's.max_capacity', 's.min_per_res', 's.max_per_res', 's.priceperpeople')))
				->select($dbo->qn('g.name', 'group_name'))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('s.id_group') . ' = ' . $dbo->qn('g.id'))
				->order(array(
					$dbo->qn('g.ordering') . ' ASC',
					$dbo->qn('s.ordering') . ' ASC',
				));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$last_id_group = -2;

				foreach ($dbo->loadAssocList() as $s)
				{
					if ($last_id_group != $s['id_group'])
					{
						$services[] = array(
							'id' 	=> $s['id_group'],
							'name' 	=> $s['group_name'],
							'list' 	=> array(),
						);

						$last_id_group = $s['id_group'];
					}

					$range = array(1, 1);
					if ($s['max_capacity'] > 1)
					{
						$range = array($s['min_per_res'], $s['max_per_res']);
					}

					$services[count($services)-1]['list'][] = array(
						'id' 			 => $s['id'],
						'name' 			 => $s['name'],
						'capacity'  	 => $range,
						'priceperpeople' => $s['priceperpeople'],
					);
				}
			}

			$this->services = &$services;
		}
		
		$this->args = &$args;
		
		// Display the template (default.php)
		parent::display($tpl);
	}
}
