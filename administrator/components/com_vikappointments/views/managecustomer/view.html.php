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
class VikAppointmentsViewmanagecustomer extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();
		VikAppointments::load_complex_select();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$type = $input->getString('type');
		$tab  = $app->getUserStateFromRequest('vapsavecustomer.tab', 'tabname', 'customer_details', 'string');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$customer = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_users'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$customer = $dbo->loadAssoc();

				$customer['fields'] = json_decode($customer['fields'], true);

				if (empty($customer['jid']))
				{
					$customer['jid'] = '';
				}
			}
		}

		// get custom fields
		
		$custom_fields = VAPCustomFields::getList(0, 0, 0, CF_EXCLUDE_REQUIRED_CHECKBOX);

		if (empty($customer))
		{
			$customer = $this->getBlankItem($custom_fields);
		}

		// get assigned user (if any)
		
		$juser = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name', 'username', 'email')))
			->from($dbo->qn('#__users'))
			->where($dbo->qn('id') . ' = ' . (int) $customer['jid'])
			->order($dbo->qn('name'));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$juser = $dbo->loadAssoc();
		}

		$countries = VikAppointmentsLocations::getCountries('country_name');
		
		$this->customer 	= &$customer;
		$this->countries 	= &$countries;
		$this->juser 		= &$juser;
		$this->customFields	= &$custom_fields;
		$this->tab 			= &$tab;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($type)
	{
		// Add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITCUSTOMER'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWCUSTOMER'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveCustomer', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseCustomer', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewCustomer', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelCustomers', JText::_('VAPCANCEL'));
	}

	/**
	 * Returns a blank item.
	 *
	 * @param 	array 	$custom_fields 	A list of custom fields to obtain 
	 *									the default country code.
	 *
	 * @return 	array 	A blank item for new requests.
	 */
	protected function getBlankItem(array $custom_fields)
	{
		$country_code = '';
		foreach ($custom_fields as $cf)
		{
			if (VAPCustomFields::isPhoneNumber($cf))
			{
				$country_code = $cf['choose'];
				break;
			}
		}

		return array(
			'id'				=> -1,
			'jid' 				=> '',
			'billing_name' 		=> '',
			'billing_mail' 		=> '',
			'billing_phone' 	=> '',
			'country_code' 		=> $country_code,
			'billing_state' 	=> '',
			'billing_city' 		=> '',
			'billing_address' 	=> '',
			'billing_address_2' => '',
			'billing_zip' 		=> '',
			'company' 			=> '',
			'vatnum' 			=> '',
			'ssn' 				=> '',
			'fields' 			=> array(),
			'notes' 			=> '',
			'user_pwd1' 		=> '',
			'user_pwd2' 		=> '',
			'image' 			=> '',
			'credit'			=> 0.0,
		);
	}
}
