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
class VikAppointmentsViewempeditpay extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
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
		
		$payment = array();

		if (count($cid))
		{
			$type = 2;

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_gpayments'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $auth->id,
					$dbo->qn('id') . ' = ' . $cid[0],
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$payment = $dbo->loadAssoc();
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPPAYRESTRICTED'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emppaylist', false));
				exit;
			}
		}
		// do not create if not authorised
		else if (!$auth->managePayments())
		{
			$app->enqueueMessage(JText::_('VAPEMPPAYNEWAUTH0'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emppaylist', false));
			exit;
		}

		if (!count($payment))
		{
			$payment = $this->getBlankItem();
		}
		
		$this->auth 	= &$auth;
		$this->payment 	= &$payment;
		
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
			'id'			=> -1,
			'name' 			=> '',
			'file' 			=> '',
			'published' 	=> 0,
			'prenote' 		=> '',
			'note' 			=> '',
			'charge' 		=> 0.0,
			'icontype' 		=> 0,
			'icon' 			=> '',
			'setconfirmed' 	=> 0,
		);
	}
}
