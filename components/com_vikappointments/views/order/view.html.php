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
 * VikAppointments View.
 */
class VikAppointmentsVieworder extends JViewUI
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
		$sid = $input->getString('ordkey', '');

		$itemid = $input->getInt('Itemid', 0);
		
		$orders 	 = array();
		$array_order = array();

		// specify default payment vars
		$payment = array();
		$payment['name']	= '';
		$payment['charge'] 	= 0;
		$payment['prenote'] = '';
		$payment['note']	= '';
		
		if (!empty($oid) && !empty($sid))
		{	
			$orders = VikAppointments::fetchOrderDetails($oid, $sid, JFactory::getLanguage()->getTag());

			if ($orders)
			{
				$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');
				
				$all_locations = array();

				foreach ($orders as $ord)
				{
					if ($ord['id_parent'] != -1 || $ord['id'] == $ord['id_parent'])
					{	
						// SET EMPLOYEE TIMEZONE
						VikAppointments::setCurrentTimezone($ord['ord_timezone']);
								
						$id_location = VikAppointments::getEmployeeLocationFromTime($ord['id_employee'], $ord['id_service'], $ord['checkin_ts']);
						
						if ($id_location)
						{
							$loc 	 = VikAppointments::fillEmployeeLocation($id_location);
							$loc_str = VikAppointments::locationToString($loc);
							if ($loc && strlen($loc['latitude']) && strlen($loc['longitude']))
							{
								$arr_marker = array(
									"label" => $ord['sname'].($ord['view_emp'] ? ", ".$ord['ename'] : "")."<br/>".date($dt_format, $ord['checkin_ts'])."<br/>".$loc_str,
									"lat" 	=> $loc['latitude'], 
									"lng" 	=> $loc['longitude'],
								);
								
								$found = false;
								for ($i = 0; $i < count($all_locations) && !$found; $i++)
								{
									$found = (
										$all_locations[$i]['lat'] === $arr_marker['lat'] &&
										$all_locations[$i]['lng'] === $arr_marker['lng']
									);
								}

								if (!$found)
								{
									array_push($all_locations, $arr_marker);
								}
							}
						}
					}
				}

				if (count($all_locations))
				{
					VikAppointments::load_googlemaps();
				}

				$this->ordersLocations = &$all_locations;
				
				if ($orders[0]['id_payment'] > 0)
				{
					$payment = VikAppointments::getPayment($orders[0]['id_payment'], false);
					
					if ($payment)
					{
						$lang_payments = VikAppointments::getTranslatedPayments($payment['id']);

						$payment['name'] 	= VikAppointments::getTranslation($payment['id'], $payment, $lang_payments, 'name', 'name');
						$payment['prenote'] = VikAppointments::getTranslation($payment['id'], $payment, $lang_payments, 'prenote', 'prenote');
						$payment['note'] 	= VikAppointments::getTranslation($payment['id'], $payment, $lang_payments, 'note', 'note');
					}
					
					if ($this->orderHasExpired($orders, $dbo))
					{
						for ($i = 0; $i < count($orders); $i++)
						{
							$orders[$i]['status'] = "REMOVED";
						}
					}
					
					if ($orders[0]['status'] == "PENDING")
					{
						$total_cost = $orders[0]['total_cost'];

						$vik = UIApplication::getInstance();

						/**
						 * The payment URLs are correctly routed for external usage.
						 *
						 * @since 1.6
						 */
						$return_url = $vik->routeForExternalUse("index.php?option=com_vikappointments&view=order&ordnum={$oid}&ordkey={$sid}", false, $itemid);
						$error_url  = $vik->routeForExternalUse("index.php?option=com_vikappointments&view=order&ordnum={$oid}&ordkey={$sid}", false, $itemid);
						$notify_url = $vik->routeForExternalUse("index.php?option=com_vikappointments&task=notifypayment&ordnum={$oid}&ordkey={$sid}", false, $itemid);
						
						$transaction_name = JText::sprintf('VAPTRANSACTIONNAME', $config->get('agencyname'));
				
						$array_order['oid'] 				 = $oid;
						$array_order['sid'] 				 = $sid;
						$array_order['attempt']				 = $orders[0]['payment_attempt'];
						$array_order['transaction_currency'] = $config->get('currencyname');
						$array_order['transaction_name'] 	 = $transaction_name;
						$array_order['currency_symb'] 		 = $config->get('currencysymb');
						$array_order['tax'] 				 = 0;
						$array_order['return_url'] 			 = $return_url;
						$array_order['error_url'] 			 = $error_url;
						$array_order['notify_url'] 			 = $notify_url;
						$array_order['total_to_pay'] 		 = $total_cost + $payment['charge'] - $orders[0]['tot_paid'];
						$array_order['total_net_price'] 	 = $total_cost + $payment['charge'] - $orders[0]['tot_paid'];
						$array_order['total_tax'] 			 = 0;
						$array_order['leave_deposit'] 		 = 0;
						$array_order['payment_info'] 		 = $payment;
						$array_order['type'] 				 = 'appointments';

						$array_order['details'] = array(
							'purchaser_mail' 		=> $orders[0]['purchaser_mail'],
							'purchaser_phone' 		=> $orders[0]['purchaser_phone'],
							'purchaser_nominative' 	=> $orders[0]['purchaser_nominative'],
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

				$orders = $this->sortOrdersByServiceDate($orders);

				// check if the customer should pay the full amount (only for PENDING orders)
				$pay_full_amount = $this->shouldPayFullAmount($orders, $input, $dbo);

				$this->payFullAmount = &$pay_full_amount;
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPORDERRESERVATIONERROR'), 'error');
				$orders = array();
			}
		}
		
		if (!$orders)
		{
			$tpl = 'track';
		}
		else
		{
			// print conversion code if needed
			UILoader::import('libraries.models.conversion');
			VAPConversion::getInstance(array('page' => 'order'))->trackCode($orders[0]);
		}

		$this->orders 		= &$orders;
		$this->payment 		= &$payment;
		$this->array_order  = &$array_order;
		$this->itemid 		= &$itemid;

		// prepare page content
		VikAppointments::prepareContent($this);
		
		// Display the template
		parent::display($tpl);
	}

	protected function sortOrdersByServiceDate($items)
	{
		$size = count($items);

		for ($i = 0; $i < $size; $i++)
		{
			for ($j = 0; $j < $size - 1 - $i ; $j++)
			{
				if ($items[$j+1]['id_service'] < $items[$j]['id_service'])
				{
					$this->swap($items, $j, $j+1);
				}
				else if ($items[$j+1]['id_service'] == $items[$j]['id_service'] && $items[$j+1]['checkin_ts'] < $items[$j]['checkin_ts'])
				{
					$this->swap($items, $j, $j+1);
				}
			}
		}

		return $items;
	}
	
	protected function swap(&$items, $i, $j)
	{
		$app = $items[$i];
		$items[$i] = $items[$j];
		$items[$j] = $app;
	}
	
	protected function orderHasExpired($order, $dbo)
	{	
		if ($order[0]['status'] != 'PENDING' || time() <= $order[0]['locked_until'])
		{
			return false;
		}

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_reservation'))
			->set($dbo->qn('status') . ' = ' . $dbo->q('REMOVED'))
			->where(array(
				$dbo->qn('id') . ' = ' . $order[0]['id'],
				$dbo->qn('id_parent') . ' = ' . $order[0]['id'],
			));
		
		$dbo->setQuery($q);
		$dbo->execute();
		
		return $dbo->getAffectedRows();
	}

	protected function shouldPayFullAmount($orders, $input, $dbo)
	{
		if (UIFactory::getConfig()->getUint('usedeposit') != 1 || $orders[0]['status'] == 'CONFIRMED')
		{
			return 0;
		}

		// value set after checking the input to pay the full amount instead than the deposit
		$pay_full_amount = $input->getUint('payfull', null);

		if (!is_null($pay_full_amount))
		{
			$orders[0]['skip_deposit'] = $pay_full_amount;

			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_reservation'))
				->set($dbo->qn('skip_deposit') . ' = ' . $pay_full_amount)
				->where(array(
					$dbo->qn('id') . ' = ' . $orders[0]['id'],
					$dbo->qn('id_parent') . ' = ' . $orders[0]['id'],
				), 'OR');

			$dbo->setQuery($q);
			$dbo->execute();
		}

		$pay_full_amount = $orders[0]['skip_deposit'];

		return $pay_full_amount;
	}
}
