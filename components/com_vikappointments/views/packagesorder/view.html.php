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
class VikAppointmentsViewpackagesorder extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_font_awesome();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$config = UIFactory::getConfig();
		
		$oid = $input->getUint('ordnum', 0);
		$sid = $input->getString('ordkey');

		$itemid = $input->getInt('Itemid', 0);
		
		$order = VikAppointments::fetchPackagesOrderDetails($oid, $sid, JFactory::getLanguage()->getTag());

		if ($order === false)
		{
			$app->enqueueMessage(JText::_('VAPORDERRESERVATIONERROR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=packorders' . ($itemid ? '&Itemid=' . $itemid : ''), false));
			exit;
		}

		$payment 	 = array();
		$array_order = array();
		
		if ($order['id_payment'] > 0)
		{	
			$payment = VikAppointments::getPayment($order['id_payment'], false);
					
			if ($payment)
			{
				$lang_payments = VikAppointments::getTranslatedPayments($payment['id']);

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
				$return_url = $vik->routeForExternalUse("index.php?option=com_vikappointments&view=packagesorder&ordnum={$oid}&ordkey={$sid}", false, $itemid);
				$error_url  = $vik->routeForExternalUse("index.php?option=com_vikappointments&view=packagesorder&ordnum={$oid}&ordkey={$sid}", false, $itemid);
				$notify_url = $vik->routeForExternalUse("index.php?option=com_vikappointments&task=notifypackpayment&ordnum={$oid}&ordkey={$sid}", false, $itemid);
				
				$transaction_name = JText::sprintf('VAPTRANSACTIONNAMEPACK', VikAppointments::getAgencyName());
		
				$array_order['oid'] 					= $oid;
				$array_order['sid'] 					= $sid;
				$array_order['attempt']				 	= $order['payment_attempt'];
				$array_order['transaction_currency'] 	= $config->get('currencyname');
				$array_order['transaction_name'] 		= $transaction_name;
				$array_order['currency_symb'] 			= $config->get('currencysymb');
				$array_order['tax'] 					= 0;
				$array_order['return_url'] 				= $return_url;
				$array_order['error_url'] 				= $error_url;
				$array_order['notify_url'] 				= $notify_url;
				$array_order['total_to_pay'] 			= $total_cost + $payment['charge'] - $order['tot_paid'];
				$array_order['total_net_price'] 		= $total_cost + $payment['charge'] - $order['tot_paid'];
				$array_order['total_tax'] 				= 0;
				$array_order['leave_deposit'] 			= 0;
				$array_order['payment_info'] 			= $payment;
				$array_order['type']		 			= 'packages';
				
				$array_order['details'] = array(
					'purchaser_mail' 		=> $order['purchaser_mail'],
					'purchaser_phone' 		=> $order['purchaser_phone'],
					'purchaser_nominative' 	=> $order['purchaser_nominative'],
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
				UIFactory::getEventDispatcher()->trigger('onInitPaymentTransaction', array(&$array_order, &$payment['params']));
			} 
		}

		$order = $this->categorizeOrderItems($order);

		$this->order 		= &$order;
		$this->payment 		= &$payment;
		$this->array_order 	= &$array_order;
		$this->itemid 		= &$itemid;
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Helper method used to group the order items.
	 *
	 * @param 	array 	$order 	The order records.
	 * @param 	mixed 	$dbo 	The database object.
	 *
	 * @return 	array 	The grouped order.
	 */
	private function categorizeOrderItems($order, $dbo = null)
	{
		if (is_null($dbo))
		{
			$dbo = JFactory::getDbo();
		}

		/**
		 * Retrieve only the services the belong to the view
		 * access level of the current user.
		 *
		 * @since 1.6
		 */
		$levels = JFactory::getUser()->getAuthorisedViewLevels();

		$lang_services = VikAppointments::getTranslatedServices('', JFactory::getLanguage()->getTag(), $dbo);

		$app = array();
		$last_id = -1;

		foreach ($order['items'] as $o)
		{
			if ($last_id != $o['id_group'])
			{
				$app[] = array(
					'id' 		=> $o['id_group'],
					'title' 	=> $o['group_title'],
					'packages' 	=> array(),
				);

				$last_id = $o['id_group'];

				unset($o['id_group']);
				unset($o['group_title']);
			}

			if (!empty($o['id']))
			{
				$o['services'] = array();

				$q = $dbo->getQuery(true)
					->select($dbo->qn(array('s.id', 's.name')))
					->from($dbo->qn('#__vikappointments_service', 's'))
					->leftjoin($dbo->qn('#__vikappointments_package_service', 'a') . ' ON ' . $dbo->qn('a.id_service') . ' = ' . $dbo->qn('s.id'))
					->where($dbo->qn('a.id_package') . ' = ' . $o['id']);

				if ($levels)
				{
					$q->where($dbo->qn('s.level') . ' IN (' . implode(', ', $levels) . ')');
				}

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$o['services'] = $dbo->loadAssocList();

					for ($i = 0; $i < count($o['services']); $i++)
					{
						$o['services'][$i]['name'] = VikAppointments::getTranslation($o['services'][$i]['id'], $o['services'][$i], $lang_services, 'name', 'name');
					}
				}

				$app[count($app)-1]['packages'][] = $o;
			}
		}

		$order['items'] = $app;

		return $order;
	}
}
