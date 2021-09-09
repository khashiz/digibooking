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
class VikAppointmentsViewempsubscrorder extends JViewUI
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
		
		$itemid = $input->getInt('Itemid');

		$auth = EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false));
			exit;
		}
		
		$id_order = $input->getInt('id');
		
		$order 			= array();
		$payment 		= array();
		$payment_args 	= array();

		// get translations
		$lang_subscr 	= VikAppointments::getTranslatedSubscriptions();
		$lang_payments 	= VikAppointments::getTranslatedPayments();

		// get order and payment details
		if (!empty($id_order))
		{
			$q = $dbo->getQuery(true)
				->select('`o`.*')
				->select($dbo->qn('s.name', 'sub_name'))
				->from($dbo->qn('#__vikappointments_subscr_order', 'o'))
				->leftjoin($dbo->qn('#__vikappointments_subscription', 's') . ' ON ' . $dbo->qn('o.id_subscr') . ' = ' . $dbo->qn('s.id'))
				->where(array(
					$dbo->qn('o.id_employee') . ' = ' . $auth->id,
					$dbo->qn('o.id') . ' = ' . $id_order,
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$order = $dbo->loadAssoc();

				// translate subscription name
				$order['sub_name'] = VikAppointments::getTranslation($order['id_subscr'], $order, $lang_subscr, 'sub_name', 'name');
				
				$order['transaction_name'] = JText::sprintf('VAPSUBSCRTRANSACTION', $order['sub_name'], VikAppointments::getAgencyName());
				
				$payment = VikAppointments::getPayment($order['id_payment'], false);

				if ($payment)
				{
					$payment['name'] 	= VikAppointments::getTranslation($payment['id'], $payment, $lang_payments, 'name', 'name');
					$payment['prenote'] = VikAppointments::getTranslation($payment['id'], $payment, $lang_payments, 'prenote', 'prenote');
					$payment['note'] 	= VikAppointments::getTranslation($payment['id'], $payment, $lang_payments, 'note', 'note');
				}
				
				if ($order['status'] == 'PENDING' && $payment)
				{	 
					$total_cost = $order['total_cost'];

					$vik = UIApplication::getInstance();

					/**
					 * The payment URLs are correctly routed for external usage.
					 *
					 * @since 1.6
					 */
					$return_url = $vik->routeForExternalUse("index.php?option=com_vikappointments&view=empsubscrorder&id={$order['id']}", false, $itemid);
					$error_url  = $vik->routeForExternalUse("index.php?option=com_vikappointments&view=empsubscrorder&id={$order['id']}", false, $itemid);
					$notify_url = $vik->routeForExternalUse("index.php?option=com_vikappointments&task=empsubscrorder.notifyPayment&id={$order['id']}&ordkey={$order['sid']}", false, $itemid);
			
					$payment_args['oid'] 					= $order['id'];
					$payment_args['sid'] 					= $order['sid'];
					$payment_args['attempt']				= $order['payment_attempt'];
					$payment_args['transaction_currency'] 	= VikAppointments::getCurrencyName();
					$payment_args['transaction_name'] 		= $order['transaction_name'];
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
						'purchaser_mail' 		=> $auth->email,
						'purchaser_phone' 		=> $auth->phone,
						'purchaser_nominative' 	=> $auth->lastname . ' ' . $auth->firstname,
					);

					// decode params
					$payment['params'] = (array) json_decode($payment['params'], true);

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
					UIFactory::getEventDispatcher()->trigger('onInitPaymentTransaction', array(&$payment_args, &$payment['params']));
				}
			}
		}
		
		// get all orders

		$lim  = 5;
		$lim0 = $app->getUserStateFromRequest('empsubscrorder.limitstart', 'limitstart', 0, 'uint');
		
		$all_orders = array();

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `o`.*')
			->select($dbo->qn('s.name', 'sub_name'))
			->select($dbo->qn('p.name', 'pay_name'))
			->from($dbo->qn('#__vikappointments_subscr_order', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_subscription', 's') . ' ON ' . $dbo->qn('o.id_subscr') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_gpayments', 'p') . ' ON ' . $dbo->qn('o.id_payment') . ' = ' . $dbo->qn('p.id'))
			->where($dbo->qn('o.id_employee') . ' = ' . $auth->id)
			->order($dbo->qn('o.id') . ' DESC');

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{	
			$all_orders = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = $pageNav->getPagesLinks();

			// translate records
			foreach ($all_orders as $i => $o)
			{
				$all_orders[$i]['sub_name'] = VikAppointments::getTranslation($o['id_subscr'], $o, $lang_subscr, 'sub_name', 'name');
				$all_orders[$i]['pay_name'] = VikAppointments::getTranslation($o['id_payment'], $o, $lang_payments, 'pay_name', 'name');
			}
		}

		if (count($order))
		{
			$tpl = 'order';
		}
		
		$this->auth 		= &$auth;
		$this->allOrders 	= &$all_orders;
		$this->order 		= &$order;
		$this->payment 		= &$payment;
		$this->payment_args = &$payment_args;
		$this->navbut 		= &$navbut;
		
		// Display the template
		parent::display($tpl);
	}
}
