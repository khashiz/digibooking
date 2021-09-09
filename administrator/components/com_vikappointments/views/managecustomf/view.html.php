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
class VikAppointmentsViewmanagecustomf extends JViewUI
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

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$type = $input->getString('type');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$field = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_custfields'))
				->where($dbo->qn('id') . ' = ' . (int) $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$field = $dbo->loadAssoc();
			}
		}

		if (empty($field))
		{
			$field = $this->getBlankItem($input);
		}

		// get employees

		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'nickname')))
			->from($dbo->qn('#__vikappointments_employee'))
			->order($dbo->qn('nickname') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList();
		}

		// get services

		$services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('s.id', 's.name', 's.id_group')))
			->select($dbo->qn('g.name', 'group_name'))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('s.id_group') . ' = ' . $dbo->qn('g.id'))
			->order(array(
				$dbo->qn('g.ordering') . ' ASC',
				$dbo->qn('s.ordering') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$last_id_group = -2;

			foreach ($dbo->loadAssocList() as $s)
			{
				if ($last_id_group != $s['id_group'])
				{
					$services[] = array(
						'id' 	=> $s['id_group'],
						'name' 	=> $s['group_name'] ? $s['group_name'] : '--',
						'list' 	=> array(),
					);

					$last_id_group = $s['id_group'];
				}

				$services[count($services)-1]['list'][] = array(
					'id' 	=> $s['id'],
					'name' 	=> $s['name'],
				);
			}
		}

		if (count($services) > 1 && $services[0]['id'] <= 0)
		{
			$services[] = array_shift($services);
		}

		// get services assoc

		$assoc_services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_service'))
			->from($dbo->qn('#__vikappointments_cf_service_assoc'))
			->where($dbo->qn('id_field') . ' = ' . (int) $field['id']);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$assoc_services = $dbo->loadColumn();
		}

		// get countries

		$countries = VikAppointmentsLocations::getCountries();
		
		$this->field 	 = &$field;
		$this->employees = &$employees;
		$this->services  = &$services;
		$this->countries = &$countries;

		$this->assignedServices = &$assoc_services;
		
		// Display the template (default.php)
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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITCUSTOMF'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWCUSTOMF'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveCustomf', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseCustomf', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewCustomf', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelCustomf', JText::_('VAPCANCEL'));
	}

	/**
	 * Returns a blank item.
	 *
	 * @return 	array 	A blank item for new requests.
	 */
	protected function getBlankItem($input)
	{
		return array(
			'id'			=> -1,
			'name' 			=> '',
			'formname'		=> '',
			'type' 			=> 'text',
			'rule'			=> 0,
			'required' 		=> 0,
			'multiple' 		=> 0,
			'poplink' 		=> '',
			'choose' 		=> '',
			'id_employee' 	=> -1,
			'group'			=> $input->getUint('group', 0),
		);
	}
}
