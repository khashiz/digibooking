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
class VikAppointmentsViewmanageemplocation extends JViewUI
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
		
		$id_emp = $input->getUint('id_emp');
		$type 	= $input->getString('type');

		if (empty($id_emp))
		{
			$app->redirect('index.php?option=com_vikappointments&task=employees');
			exit;
		}
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$location = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));

			$q = $dbo->getQuery(true)
				->select('*')
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
			$location = $this->getBlankItem($id_emp);
		}

		// get countries, states and cities
		
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
		$this->idEmployee 	= &$id_emp;
		$this->countries 	= &$countries;
		$this->states 		= &$states;
		$this->cities 		= &$cities;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @param 	string 	$type 	The request type (new or edit).
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
			JToolBarHelper::apply('saveEmployeeLocation', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseEmployeeLocation', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewEmployeeLocation', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelEmployeeLocation', JText::_('VAPCANCEL'));
	}

	/**
	 * Returns a blank item.
	 *
	 * @param 	integer  $id_emp 	The ID of the employee.
	 *
	 * @return 	array 	 A blank item for new requests.
	 */
	protected function getBlankItem($id_emp)
	{
		return array(
			'id'			=> -1,
			'name' 			=> '',
			'id_employee' 	=> $id_emp,
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
