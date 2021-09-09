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
class VikAppointmentsViewmanageservice extends JViewUI
{	
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		VikAppointments::load_font_awesome();
		VikAppointments::load_complex_select();
		
		$dbo   = JFactory::getDbo();
		$app   = JFactory::getApplication();
		$input = $app->input;
		
		$type = $input->getString('type', 'new');
		$tab  = $app->getUserStateFromRequest('vapsaveser.tab', 'tabname', 'service_details', 'string');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$service = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));

			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_service'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$service = $dbo->loadAssoc();
			}
		}

		if (empty($service))
		{
			$service = $this->getBlankItem();
		}

		// get groups

		$groups = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_group'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}

		// get options
		
		$options = array();
		
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name', 'published')))
			->from($dbo->qn('#__vikappointments_option'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$options = $dbo->loadAssocList();
		}

		// get assigned options
		
		$service_options = array();

		if ($service['id'] > 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('o.id', 'o.name', 'o.published')))
				->from($dbo->qn('#__vikappointments_option', 'o'))
				->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'))
				->where($dbo->qn('a.id_service') . ' = ' . $service['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$service_options = $dbo->loadAssocList();
			}
		}

		// get employees
		
		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('e.id', 'e.firstname', 'e.lastname')))
			->select($dbo->qn('g.name', 'group_name'))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_employee_group', 'g') . ' ON ' . $dbo->qn('e.id_group') . ' = ' . $dbo->qn('g.id'))
			->order(array(
				$dbo->qn('g.ordering') . ' ASC',
				$dbo->qn('e.lastname') . ' ASC',
				$dbo->qn('e.firstname') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList();
		}

		// get assigned employees
		
		$service_employees = array();

		if ($service['id'])
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('e.id', 'e.firstname', 'e.lastname')))
				->from($dbo->qn('#__vikappointments_employee', 'e'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('a.id_employee'))
				->where($dbo->qn('a.id_service') . ' = ' . $service['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$service_employees = $dbo->loadAssocList();
			}
		}

		/**
		 * Load com_contents language file to obtain metadata 
		 * labels and descriptions.
		 *
		 * @since 1.6.1
		 */
		JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);
		
		$this->service 		= &$service;
		$this->groups 		= &$groups;
		$this->options 		= &$options;
		$this->employees 	= &$employees;
		$this->tab 			= &$tab;

		$this->serviceOptions 	= &$service_options;
		$this->serviceEmployees = &$service_employees;	

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Returns a blank item.
	 *
	 * @return 	array 	A blank item for new requests.
	 */
	protected function getBlankItem()
	{
		return array(
			'id'				 => -1,
			'name' 				 => '',
			'alias'				 => '',
			'description' 		 => '',
			'duration' 			 => 60,
			'sleep' 			 => 0,
			'interval' 			 => 1,
			'price' 			 => 0.0,
			'max_capacity' 		 => 1,
			'min_per_res' 		 => 1,
			'max_per_res' 		 => 1,
			'priceperpeople' 	 => 1,
			'app_per_slot' 		 => 1,
			'published' 		 => 1,
			'quick_contact' 	 => 0,
			'choose_emp' 		 => 0,
			'has_own_cal' 		 => 0,
			'checkout_selection' => 0,
			'display_seats' 	 => 0,
			'enablezip' 		 => 0,
			'use_recurrence' 	 => 0,
			'image' 			 => '',
			'start_publishing' 	 => -1,
			'end_publishing' 	 => -1,
			'id_group' 			 => 0,
			'level'				 => 1,
			'metadata'			 => null,
		);
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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITSERVICE'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWSERVICE'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveService', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseService', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewService', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelService', JText::_('VAPCANCEL'));
	}
}
