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
class VikAppointmentsViewempeditlocation extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_googlemaps();
	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		$type = 1;
		$cid  = $input->getUint('cid', array());

		$isTmpl = $input->get('tmpl') == 'component';
		
		$location = array();

		if (count($cid))
		{
			$type = 2;
			
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_employee_location'))
				->where($dbo->qn('id') . ' = ' . $cid[0]);

			if ($isTmpl)
			{
				$q->andWhere(array(
					$dbo->qn('id_employee') . ' = ' . $auth->id,
					$dbo->qn('id_employee') . ' = ' . -1,
				), 'OR');
			}
			else
			{
				$q->where($dbo->qn('id_employee') . ' = ' . $auth->id);
			}
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$location = $dbo->loadAssoc();
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPLOCATIONEDITAUTH0'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplocations', false));
				exit;
			}
		}
		// do not create if not authorised
		else if (!$auth->manageLocations())
		{
			$app->enqueueMessage(JText::_('VAPEMPLOCATIONEDITAUTH0'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplocations', false));
			exit;
		}

		if (empty($location))
		{
			$location = $this->getBlankItem();
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
		
		$this->auth 		= &$auth;
		$this->location 	= &$location;
		$this->countries 	= &$countries;
		$this->states 		= &$states;
		$this->cities 		= &$cities;
		$this->type 		= &$type;

		if ($location['id'] > 0 && $isTmpl)
		{
			$tpl = 'preview';
		}
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Returns a blank item for the creation page.
	 *
	 * @return 	array 	The item.
	 */
	protected function getBlankItem()
	{
		return array(
			'name' 			=> '',
			'id_country' 	=> -1,
			'id_state' 		=> -1,
			'id_city' 		=> -1,
			'address' 		=> '',
			'zip' 			=> '',
			'latitude' 		=> '',
			'longitude' 	=> '',
			'id' 			=> -1,
		);
	}
}
