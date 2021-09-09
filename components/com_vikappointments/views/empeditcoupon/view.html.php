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
class VikAppointmentsViewempeditcoupon extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return void
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
		
		$coupon = array();
		$selected_services = array();

		if (count($cid))
		{
			$type = 2;
			
			$q = $dbo->getQuery(true)
				->select('`c`.*')
				->from($dbo->qn('#__vikappointments_coupon', 'c'))
				->leftjoin($dbo->qn('#__vikappointments_coupon_employee_assoc', 'a') . ' ON ' . $dbo->qn('a.id_coupon') . ' = ' . $dbo->qn('c.id'))
				->where(array(
					$dbo->qn('a.id_employee') . ' = ' . $auth->id,
					$dbo->qn('c.id') . ' = ' . $cid[0],
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$coupon = $dbo->loadAssoc();

				$q = $dbo->getQuery(true)
					->select($dbo->qn('id_service'))
					->from($dbo->qn('#__vikappointments_coupon_service_assoc'))
					->where($dbo->qn('id_coupon') . ' = ' . $coupon['id']);

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$selected_services = $dbo->loadColumn();
				}
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPCOUPONEDITAUTH0'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=empcoupons', false));
				exit;
			}
		}
		// do not create if not authorised 
		else if (!$auth->manageCoupons())
		{
			$app->enqueueMessage(JText::_('VAPEMPCOUPONEDITAUTH0'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=empcoupons', false));
			exit;
		}

		if (empty($coupon))
		{
			$coupon = $this->getBlankItem();
		}

		// get services
		$services = array();

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
			$services = $dbo->loadAssocList();
		}
		
		$this->auth 			= &$auth;
		$this->coupon 			= &$coupon;
		$this->services 		= &$services;
		$this->selectedServices = &$selected_services;
		$this->type 			= &$type;
		
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
			'code' 			=> VikAppointments::generateSerialCode(12),
			'type' 			=> 1,
			'percentot' 	=> 2,
			'value' 		=> 0.0,
			'mincost' 		=> 0.0,
			'pubmode'		=> 1,
			'dstart' 		=> '',
			'dend' 			=> '',
			'lastminute' 	=> 0,
			'max_quantity' 	=> 1,
			'used_quantity' => 0,
			'remove_gift' 	=> 0,
			'notes' 		=> '',
			'id' 			=> -1,
		);
	}
}
