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
class VikAppointmentsViewpackagesconfirm extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_complex_select();
		VikAppointments::load_fancybox();
		VikAppointments::load_font_awesome();
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$juser 	= JFactory::getUser();

		$itemid = $input->getInt('Itemid', 0);

		VikAppointments::loadCartPackagesLibrary();
		$core = new VikAppointmentsCartPackagesCore();
		$cart = $core->getCartObject();
		
		// get payments

		$payments = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where(array(
				$dbo->qn('published') . ' = 1',
				$dbo->qn('subscr') . ' = 1',
			))
			->order($dbo->qn('ordering') . ' ASC');

		/**
		 * Retrieve only the payments the belong to the view
		 * access level of the current user.
		 *
		 * @since 1.6.2
		 */
		$levels = $juser->getAuthorisedViewLevels();

		if ($levels)
		{
			$q->where($dbo->qn('level') . ' IN (' . implode(', ', $levels) . ')');
		}

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$payments = $dbo->loadAssocList();
		}

		// get custom fields

		$fields = VAPCustomFields::getList(0, 0, 0, CF_EXCLUDE_FILE);
		VAPCustomFields::translate($fields);
		
		// get user data
	
		$user = array('fields' => '');

		if (!$juser->guest)
		{
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_users'))
				->where($dbo->qn('jid') . ' = ' . $juser->id);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$user = $dbo->loadAssoc();
			}
		}
		else
		{
			$tpl = 'login';
		}

		// get countries
		
		$countries = VikAppointmentsLocations::getCountries('phone_prefix');
		
		$this->cart 		= &$cart;
		$this->customFields = &$fields;
		$this->payments 	= &$payments;
		$this->user 		= &$user;
		$this->juser 		= &$juser;
		$this->countries 	= &$countries;
		$this->itemid 		= &$itemid;

		$lang_payments 		= VikAppointments::getTranslatedPayments();
		$this->langPayments = &$lang_payments;
		
		// Display the template
		parent::display($tpl);
	}
}
