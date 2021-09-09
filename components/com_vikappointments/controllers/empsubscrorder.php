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

UILoader::import('libraries.controllers.admin');

/**
 * Employee subscription order controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpSubscrOrder extends UIControllerAdmin
{
	/**
	 * Creates a new employee subscription order.
	 *
	 * @param 	integer  $id 	The subscription ID.
	 *
	 * @return 	void
	 */
	public function save($id = 0)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();

		// check if authenticated
		if (!$auth->isEmployee())
		{
			$this->redirect('index.php?option=com_vikappointments&view=emplogin');
			exit;
		}

		// get args
		$args = array();
		$args['id_subscr'] = (int) $id;
		
		if (empty($id))
		{
			$args['id_subscr'] = $input->getUint('id_subscr');
		}
		
		$args['id_payment'] = $input->getInt('id_payment', -1);

		// get published subscription
		$subscription = VikAppointments::getSubscription($args['id_subscr']);

		if (!$subscription)
		{
			$app->enqueueMessage(JText::_('VAPSUBSCRNOTEXISTSERR'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empsubscr');
			exit;
		}

		if ($subscription['trial'] && $auth->active_to != 0)
		{
			$app->enqueueMessage(JText::_('VAPSUBSCRTRIALUSEDERR'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empsubscr');
			exit;
		}
		
		$status = 'PENDING';
		if ($subscription['price'] == 0)
		{
			$status = 'CONFIRMED';
		}
		else
		{
			$payment = VikAppointments::getPayment($args['id_payment']);

			if (!$payment)
			{
				$app->enqueueMessage(JText::_('VAPERRINVPAYMENT'), 'err');
				$this->redirect('index.php?option=com_vikappointments&view=empsubscr');
				exit;
			}
		}

		// bind data
		$data = (object) $args;

		$data->id  			= 0;
		$data->sid 			= VikAppointments::generateSerialCode(16);
		$data->id_employee 	= $auth->id;
		$data->total_cost 	= $subscription['price'];
		$data->status 		= $status;
		$data->createdon 	= time();

		// save record
		$res = $dbo->insertObject('#__vikappointments_subscr_order', $data, 'id');

		if (!$res || !$data->id)
		{
			$app->enqueueMessage(JText::_('VAPSUBSCRINSERTERR'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empsubscr');
			exit;
		}
		
		if ($status == 'CONFIRMED')
		{
			VikAppointments::applyAdditionalSubscription($subscription, $auth->getEmployee(), $app, $dbo);
		}

		// get billing
		$billing = $input->get('billing', array(), 'array');

		if (count($billing))
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_employee'))
				->set($dbo->qn('billing_json') . ' = ' . $dbo->q(json_encode($billing)))
				->where($dbo->qn('id') . ' = ' . $auth->id);

			$dbo->setQuery($q);
			$dbo->execute();
		}
		
		$this->redirect('index.php?option=com_vikappointments&view=empsubscrorder&id=' . $data->id);
	}

	/**
	 * Activates the subscription trial.
	 *
	 * @return 	void
	 *
	 * @uses 	save()
	 */
	public function activateTrial()
	{
		$trial = VikAppointments::getTrialSubscription();

		if ($trial === false)
		{
			$this->redirect('index.php?option=com_vikappointments&view=empsubscr');
			exit;
		}

		$this->save($trial['id']);
	}

	/**
	 * Method used to handle the payment transaction.
	 *
	 * @return 	void
	 */
	public function notifyPayment()
	{
		$app 		= JFactory::getApplication();
		$input 		= $app->input;
		$dbo 		= JFactory::getDbo();
		$dispatcher = UIFactory::getEventDispatcher();

		$oid = $input->getUint('id');
		$sid = $input->getString('ordkey');

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_subscr_order'))
			->where(array(
				$dbo->qn('id') . ' = ' . $oid,
				$dbo->qn('sid') . ' = ' . $dbo->q($sid),
			));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// the order does not exist
			throw new Exception('Order [' . $oid . '-' . $sid . '] not found.', 404);
		}

		$order = $dbo->loadAssoc();
		
		if ($order['status'] != "PENDING")
		{
			// do nothing, the order is already confirmed (or REMOVED)
			return;
		}
			
		// get employee details
		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_employee'))
			->where($dbo->qn('id') . ' = ' . $order['id_employee']);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// the employee has been probably removed by the administrator
			throw new Exception('Employee [' . $order['id_employee'] . '] not found.', 404);
		}

		$employee = $dbo->loadAssoc();
		
		// get subscription details (false to obtain also unpublished subscriptions)
		$subscription = VikAppointments::getSubscription($order['id_subscr'], false);

		if (!$subscription)
		{
			// the subscription has been probably removed by the administrator
			throw new Exception('Subscription [' . $order['id_subscr'] . '] not found.', 404);
		}
		
		// get payment details
		$payment = VikAppointments::getPayment($order['id_payment'], false);

		if (!$payment)
		{
			throw new Exception('Payment [' . $order['id_payment'] . '] not found.', 404);
		}
			
		$total_cost = $order['total_cost'];

		$vik = UIApplication::getInstance();

		/**
		 * The payment URLs are correctly routed for external usage.
		 *
		 * @since 1.6
		 */
		$return_url = $vik->routeForExternalUse("index.php?option=com_vikappointments&view=empsubscrorder&id={$order['id']}", false);
		$error_url  = $vik->routeForExternalUse("index.php?option=com_vikappointments&view=empsubscrorder&id={$order['id']}", false);
		$notify_url = $vik->routeForExternalUse("index.php?option=com_vikappointments&task=empsubscrorder.notifyPayment&id={$order['id']}&ordkey={$order['sid']}", false);

		$payment_args['oid'] 					= $order['id'];
		$payment_args['sid'] 					= $order['sid'];
		$payment_args['attempt']				= $order['payment_attempt'];
		$payment_args['transaction_currency'] 	= VikAppointments::getCurrencyName();
		$payment_args['transaction_name'] 		= JText::sprintf('VAPSUBSCRTRANSACTION', $subscription['name'], VikAppointments::getAgencyName());
		$payment_args['currency_symb'] 			= VikAppointments::getCurrencySymb();
		$payment_args['tax'] 					= 0;
		$payment_args['return_url'] 			= $return_url;
		$payment_args['error_url'] 				= $error_url;
		$payment_args['notify_url'] 			= $notify_url;
		$payment_args['total_to_pay'] 			= $total_cost + $payment['charge'] - $order['tot_paid'];
		$payment_args['total_net_price'] 		= $total_cost + $payment['charge'] - $order['tot_paid'];
		$payment_args['total_tax'] 				= 0;
		$payment_args['leave_deposit'] 			= 0;
		$payment_args['payment_info'] 			= $payment;
		$payment_args['type']					= 'employees';

		$payment_args['details'] = array(
			'purchaser_mail' 		=> $employee['email'],
			'purchaser_phone' 		=> $employee['phone'],
			'purchaser_nominative' 	=> $employee['lastname'] . ' ' . $employee['firstname'],
		);
	
		$params = array();
		if (!empty($payment['params']))
		{
			$params = json_decode($payment['params'], true);
		}

		/**
		 * Trigger event to manipulate the payment details.
		 *
		 * @param 	array 	&$order   The transaction details.
		 * @param 	array 	&$params  The payment configuration array.
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		$dispatcher->trigger('onInitPaymentTransaction', array(&$payment_args, &$params));
	
		/**
		 * Instantiate the payment using the platform handler.
		 *
		 * @since 1.6.3
		 */
		$obj = UIApplication::getInstance()->getPaymentInstance($payment['file'], $payment_args, $payment['params']);
		
		// validate transaction
		try
		{
			$res_args = $obj->validatePayment();
		}
		catch (Exception $e)
		{
			$res_args['verified'] 	= 0;
			$res_args['log'] 		= $e->getMessage();
		}
		
		// transaction verified
		if ($res_args['verified'] == 1)
		{
			if (empty($res_args['tot_paid']))
			{
				$res_args['tot_paid'] = 0;
			}
			
			$order['status'] = 'CONFIRMED';

			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_subscr_order'))
				->set($dbo->qn('status') . ' = ' . $dbo->q($order['status']))
				->set($dbo->qn('tot_paid') . ' = (' . $dbo->qn('tot_paid') . '+' . $res_args['tot_paid'] . ')')
				->where($dbo->qn('id') . ' = ' . $oid);
			
			$dbo->setQuery($q);
			$dbo->execute();
			
			// activate subscription
			if ($order['status'] == 'CONFIRMED')
			{
				VikAppointments::applyAdditionalSubscription($subscription, $employee, $app, $dbo);

				/**
				 * Generate invoice for the employee.
				 *
				 * @since 1.6
				 */
				$order['subscription']  = $subscription;
				$order['payment']		= $payment;
				VikAppointments::generateInvoice($order, 'employees');
			}

			/**
			 * Trigger event after the validation of a successful transaction.
			 *
			 * @param 	array 	$order  The transaction details.
			 * @param 	array 	$args   The response array.
			 *
			 * @return 	void
			 *
			 * @since 	1.6
			 */
			$dispatcher->trigger('onSuccessPaymentTransaction', array($payment_args, $res_args));
		}
		// transaction failure
		else
		{
			// send email to admin with $res_args['log']
			if (strlen($res_args['log']))
			{
				$sendermail 	= VikAppointments::getSenderMail();
				$admail_list 	= VikAppointments::getAdminMailList();
				$adname 		= VikAppointments::getAgencyName();
				
				$subject = JText::_('VAPINVALIDPAYMENTSUBJECT');
				$hmess 	 = JText::_('VAPINVALIDPAYMENTCONTENT') . "<br /><br />" . $res_args['log'];
			
				$vik = UIApplication::getInstance();
				foreach ($admail_list as $admail)
				{
					$vik->sendMail($sendermail, $adname, $admail, $admail, $subject, $hmess, '', true);
				}
			}

			// increase payment attemps
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_subscr_order'))
				->set($dbo->qn('payment_attempt') . ' = ' . ($order['payment_attempt'] + 1))
				->where($dbo->qn('id') . ' = ' . $oid);

			$dbo->setQuery($q);
			$dbo->execute();

			/**
			 * Trigger event after the validation of a failed transaction.
			 *
			 * @param 	array 	$order  The transaction details.
			 * @param 	array 	$args   The response array.
			 *
			 * @return 	void
			 *
			 * @since 	1.6
			 */
			$dispatcher->trigger('onFailPaymentTransaction', array($payment_args, $res_args));
		}

		// dispatch payment after validation
		if (method_exists($obj, 'afterValidation'))
		{
			$obj->afterValidation($res_args['verified']);
		}
	}
}
