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
class VikAppointmentsViewuserprofile extends JViewUI
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
		VikAppointments::load_utils();
		
		$app  = JFactory::getApplication();
		$dbo  = JFactory::getDbo();
		$user = JFactory::getUser();

		$itemid = $app->input->getInt('Itemid', 0);

		if ($user->guest)
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=allorders', false));
			exit;
		}
		
		$customer = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_users'))
			->where($dbo->qn('jid') . ' = ' . $user->id);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$customer = $dbo->loadAssoc();
		}
		else
		{
			$customer = array(
				"jid"				=> $user->id,
				"billing_name" 		=> $user->name,
				"billing_mail" 		=> $user->email,
				"billing_phone" 	=> "",
				"country_code" 		=> "",
				"billing_state" 	=> "",
				"billing_city" 		=> "",
				"billing_zip" 		=> "",
				"billing_address" 	=> "",
				"billing_address_2" => "",
				"company" 			=> "",
				"vatnum" 			=> "", 
				"ssn" 				=> "",
				"image" 			=> "",
			);

			$data = (object) $customer;

			$dbo->insertObject('#__vikappointments_users', $data, 'id');

			$customer['id'] = $data->id;
		}
		
		$countries = VikAppointmentsLocations::getCountries('country_name');
		
		$this->user 		= &$user;
		$this->customer 	= &$customer;
		$this->countries 	= &$countries;
		$this->itemid 		= &$itemid;
		
		// Display the template
		parent::display($tpl);
	}
}
