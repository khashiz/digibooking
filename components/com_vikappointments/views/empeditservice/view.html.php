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
class VikAppointmentsViewempeditservice extends JViewUI
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
		
		$type = 1;
		$cid  = $input->getUint('cid', array());
		
		// get service details
		$service = array();

		if (count($cid))
		{
			$type = 2;

			$q = $dbo->getQuery(true)
				->select('`s`.*')
				->select(array(
					$dbo->qn('a.rate'),
					$dbo->qn('a.duration', 'assoc_duration'),
					$dbo->qn('a.sleep', 'assoc_sleep'),
					$dbo->qn('a.description', 'assoc_desc'),
				))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
				->where(array(
					$dbo->qn('a.id_employee') . ' = ' . $auth->id,
					$dbo->qn('s.id') . ' = ' . $cid[0],
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$service = $dbo->loadAssoc();

				$service['duration'] = $service['assoc_duration'];
				$service['sleep'] 	 = $service['assoc_sleep'];
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPSERVICERESTRICTED'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=empserviceslist', false));
			}
		}
		// do not create if not authorised
		else if (!$auth->create())
		{
			$app->enqueueMessage(JText::_('VAPEMPSERNEWAUTH0'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=empserviceslist', false));
		}

		if (!count($service))
		{
			$service = $this->getBlankItem();
		}
		
		// get groups
		$groups = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_group'))
			->order($dbo->qn('name') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}
		
		$this->auth 	= &$auth;
		$this->groups 	= &$groups;
		$this->service 	= &$service;

		if ($type == 2 && !$auth->manageServices($service) && $auth->manageServicesRates())
		{
			$tpl = 'rate';
		}
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Returns a blank item for the creation page.
	 *
	 * @return 	array 	The item.
	 */
	protected function getBlankItem()
	{
		return array(
			'id' 				=> -1,
			'name' 				=> '',
			'description' 		=> '',
			'duration' 			=> 60,
			'sleep' 			=> 0,
			'interval' 			=> 1,
			'rate'				=> 0.0,
			'max_capacity' 		=> 1,
			'min_per_res' 		=> 1,
			'max_per_res'	 	=> 1,
			'priceperpeople' 	=> 0,
			'published' 		=> 0,
			'has_own_cal' 		=> 0,
			'enablezip' 		=> 0,
			'use_recurrence' 	=> 0,
			'start_publishing' 	=> -1,
			'end_publishing' 	=> -1,
			'image' 			=> '',
			'id_group' 			=> -1,
			'createdby'			=> 0,
		);
	}
}
