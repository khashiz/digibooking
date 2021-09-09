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
class VikAppointmentsViewmanagelocation extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();
        VikAppointments::load_googlemaps();
        VikAppointments::load_complex_select();
        VikAppointments::load_font_awesome();

        $dbo   = JFactory::getDbo();
		$app   = JFactory::getApplication();
		$input = $app->input;
        
        $type = $input->getString('type');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$location = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_employee_location'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$location = $dbo->loadAssoc();
			}
		}

		if (empty($location))
		{
			$location = $this->getBlankItem();
		}

		// get employees

		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'nickname')))
			->from($dbo->qn('#__vikappointments_employee'));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList();
		}
        
        $countries = VikAppointmentsLocations::getCountries('country_name');

        $states = array();
        if (!empty($location['id_country']))
        {
            $states = VikAppointmentsLocations::getStates($location['id_country'], 'state_name');
        }

        $cities = array();
        if (!empty($location['id_state']))
        {
            $cities = VikAppointmentsLocations::getCities($location['id_state'], 'city_name');
        }
		
		$this->location 	= &$location;
        $this->employees 	= &$employees;
        $this->countries 	= &$countries;
        $this->states 		= &$states;
        $this->cities 		= &$cities;

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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITEMPLOCATION'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWEMPLOCATION'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveLocation', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseLocation', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewLocation', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelLocation', JText::_('VAPCANCEL'));
	}

	/**
	 * Returns a blank item.
	 *
	 * @return 	array 	 A blank item for new requests.
	 */
	protected function getBlankItem()
	{
		return array(
			'id'			=> -1,
			'name' 			=> '',
			'id_employee' 	=> -1,
			'id_country' 	=> 0,
			'id_state' 		=> 0,
			'id_city' 		=> 0,
			'address'		=> '',
			'zip' 			=> '',
			'longitude' 	=> null,
			'latitude' 		=> null,
		);
	}
}
