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
class VikAppointmentsViewconfirmapp extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		VikAppointments::load_fancybox();
		VikAppointments::load_complex_select();
		VikAppointments::load_currency_js();
		VikAppointments::load_font_awesome();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		VikAppointments::loadCartLibrary();
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject();
		
		$itemid 	= $input->getInt('Itemid', 0);
		$enable_zip = $input->getBool('enable_zip');
		
		$items = $cart->getItemsList();

		// Check if all the services booked owns the same employee
		// and obtain the list of all the services booked.
		// It is not needed to check if $items contains at least
		// an element as, if we are here, the cart must be not empty.
		$same_emp_id = true;
		$services_id = array($items[0]->getID());

		for ($i = 1; $i < count($items); $i++)
		{
			$same_emp_id = $items[$i]->getID2() == $items[0]->getID2();

			if (!in_array($items[$i]->getID(), $services_id))
			{
				$services_id[] = $items[$i]->getID();
			}
		}

		if ($same_emp_id)
		{
			// we can obtain the custom payments of the booked employee
			$payments = VikAppointments::getAllEmployeePayments($items[0]->getID2());
		}
		else
		{
			// the cart owns different employees (or maybe a single one hidden), we
			// need to get the global payments (no argument to exclude the employee filter).
			$payments = VikAppointments::getAllEmployeePayments();
		}

		// Get custom fields for checkout.
		// If there is only one employee, get also its own fields.
		$cust_f = VAPCustomFields::getList(0, $same_emp_id ? $items[0]->getID2() : 0, $services_id);
		// translate the fields using the current language
		VAPCustomFields::translate($cust_f);
		
		$coupon = null;
		
		// get coupon key from POST
		$coupon_key = $input->post->getString('couponkey', '');

		if (strlen($coupon_key))
		{
			/**
			 * Validate form token to prevent brute force attacks.
			 *
			 * @since 1.6
			 */
			if (JSession::checkToken())
			{
				$q = $dbo->getQuery(true)
					->select('*')
					->from($dbo->qn('#__vikappointments_coupon'))
					->where($dbo->qn('code') . ' = ' . $dbo->q($coupon_key));

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$coupon = $dbo->loadAssoc();
					
					if (VikAppointments::validateCoupon($coupon, $cart))
					{
						JFactory::getSession()->set('vap_coupon_data', $coupon);
						$app->enqueueMessage(JText::_('VAPCOUPONFOUND'));
					}
					else
					{
						$app->enqueueMessage(JText::_('VAPCOUPONNOTVALID'), 'error');
						// unset coupon code
						$coupon = null;
					}
				}
				else
				{
					$app->enqueueMessage(JText::_('VAPCOUPONNOTVALID'), 'error');
				}
			}
			else
			{
				$app->enqueueMessage(JText::_('JINVALID_TOKEN_NOTICE'), 'error');
			}
		}

		/**
		 * If the coupon is not set, obtain it from the session.
		 *
		 * @since 1.6
		 */
		if (!$coupon)
		{
			$coupon = JFactory::getSession()->get('vap_coupon_data', null);
		}
		
		// Check if there is at least a coupon stored in the system.
		// It is not needed to check if it is valid because we have just 
		// to know if the owner used them, so that the system can display 
		// a form to redeem the coupons or not.
		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_coupon'));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$any_coupon = (bool) $dbo->getNumRows();
		
		// Retrieve user details (only if logged in) from the database.
		// Search the customer record by user ID or user e-mail (with JID not assigned to any user).
		$user = array('fields' => '', 'credit' => 0.0);
		
		if (VikAppointments::isUserLogged())
		{
			$curr_user = JFactory::getUser();

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_users'))
				->where($dbo->qn('jid') . ' = ' . $curr_user->id)
				->orWhere(array(
					$dbo->qn('jid') . ' <= 0',
					$dbo->qn('billing_mail') . ' = ' . $dbo->q($curr_user->email),
				), 'AND');

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$user = $dbo->loadAssoc();
			}
		}

		// translate payments
		if ($payments)
		{
			$lang_payments = VikAppointments::getTranslatedPayments();

			foreach ($payments as $i => $p)
			{
				$payments[$i]['name'] 	 = VikAppointments::getTranslation($p['id'], $p, $lang_payments, 'name', 'name');
				$payments[$i]['prenote'] = VikAppointments::getTranslation($p['id'], $p, $lang_payments, 'prenote', 'prenote');
			}
		}
		
		// get countries
		$countries = VikAppointmentsLocations::getCountries('phone_prefix');
		
		$this->cart 		= &$cart;
		$this->customFields = &$cust_f;
		$this->payments 	= &$payments;
		$this->anyCoupon 	= &$any_coupon;
		$this->user 		= &$user;
		$this->itemid 		= &$itemid;
		$this->enableZip 	= &$enable_zip;
		$this->coupon 		= &$coupon;
		$this->countries 	= &$countries;

		/**
		 * Build payment vars.
		 *
		 * @since 1.6
		 */
		$this->creditUsed 	= 0;
		$this->totalToPay 	= VikAppointments::getDiscountTotalCost($cart->getTotalCost(), $coupon, $user['credit'], $this->creditUsed);
		$this->skipPayments = !count($payments) || $this->totalToPay <= 0 ? 1 : 0;
		
		// print conversion code if needed
		UILoader::import('libraries.models.conversion');
		VAPConversion::getInstance(array('page' => 'confirmapp'))->trackCode(array('total_cost' => $cart->getTotalCost()));

		// Display the template
		parent::display($tpl);
	}
}
