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
class VikAppointmentsViewempsubscr extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$dbo = JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		// get subscriptions

		$trial 			= VikAppointments::getTrialSubscription();
		$subscriptions 	= VikAppointments::getSubscriptions();
		
		// get payments

		$payments = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where(array(
				$dbo->qn('published') . ' = 1',
				$dbo->qn('subscr') . ' = 1',
				$dbo->qn('id_employee') . ' = 0',
			))
			->order($dbo->qn('ordering') . ' ASC');

		/**
		 * Retrieve only the payments the belong to the view
		 * access level of the current user.
		 *
		 * @since 1.6.2
		 */
		$levels = JFactory::getUser()->getAuthorisedViewLevels();

		if ($levels)
		{
			$q->where($dbo->qn('level') . ' IN (' . implode(', ', $levels) . ')');
		}

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$payments = $dbo->loadAssocList();

			// translate payments
			$lang_payments = VikAppointments::getTranslatedPayments();

			foreach ($payments as $i => $p)
			{
				$payments[$i]['name'] 	 = VikAppointments::getTranslation($p['id'], $p, $lang_payments, 'name', 'name');
				$payments[$i]['prenote'] = VikAppointments::getTranslation($p['id'], $p, $lang_payments, 'prenote', 'prenote');
			}
		}

		// get countries
		
		$countries = VikAppointmentsLocations::getCountries('country_name');

		// get billings
		
		$billings = json_decode($auth->billing_json ? $auth->billing_json : '[]', true);
		
		if (count($billings) == 0)
		{
			$billings = array(
				'country' 	=> '',
				'state' 	=> '',
				'city' 		=> '',
				'address' 	=> '',
				'zip' 		=> '',
				'company' 	=> '',
				'vat' 		=> '',
			);
		}
		
		$this->auth 			= &$auth;
		$this->trial 			= &$trial;
		$this->subscriptions 	= &$subscriptions;
		$this->payments 		= &$payments;
		$this->countries 		= &$countries;
		$this->billings 		= &$billings;
		
		// Display the template
		parent::display($tpl);
	}
}
