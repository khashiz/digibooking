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
class VikAppointmentsViewmanagecity extends JViewUI
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
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$type = $input->getString('type');

		$country = $input->getUint('country', 0);
		$state 	 = $input->getUint('state', 0);

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id_country', 'state_name')))
			->from($dbo->qn('#__vikappointments_states'))
			->where($dbo->qn('id') . ' = ' . $state);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			if (!empty($country))
			{
				$app->redirect('index.php?option=com_vikappointments&task=states&country=' . $country);
			}
			else
			{
				$app->redirect('index.php?option=com_vikappointments&task=states&countries');
			}
			exit;
		}

		$row = $dbo->loadObject();

		if (empty($country))
		{
			$country = $row->id_country;
		}
		
		// Set the toolbar
		$this->addToolBar($type, $row->state_name);
		
		$city = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));

			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_cities'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$city = $dbo->loadAssoc();
			}
		}

		if (empty($city))
		{
			$city = $this->getBlankItem();
		}
		
		$this->city 	 = &$city;
		$this->country 	 = &$country;
		$this->state 	 = &$state;
		$this->stateName = &$row->state_name;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($type, $state_name)
	{
		// Add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolBarHelper::title(JText::sprintf('VAPMAINTITLEEDITCITY', $state_name), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::sprintf('VAPMAINTITLENEWCITY', $state_name), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveCity', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseCity', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewCity', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelCity', JText::_('VAPCANCEL'));
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
			'city_name' 	=> '',
			'city_2_code' 	=> '',
			'city_3_code' 	=> '',
			'latitude' 		=> '',
			'longitude' 	=> '',
			'published' 	=> 0, 
		);
	}
}
