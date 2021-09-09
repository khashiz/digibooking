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
class VikAppointmentsViewmanagepackage extends JViewUI
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
		VikAppointments::load_font_awesome();
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$type = $input->getString('type');
		$tab  = $app->getUserStateFromRequest('vapsavepack.tab', 'tabname', 'package_details', 'string');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$package = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_package'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$package = $dbo->loadAssoc();
			}
		}

		if (empty($package))
		{
			$package = $this->getBlankItem();
		}

		// get groups

		$groups = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'title')))
			->from($dbo->qn('#__vikappointments_package_group'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}

		// get all grouped services 

		$services = array();

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('s.id'),
				$dbo->qn('s.name', 'service_name'),
				$dbo->qn('g.name', 'group_name'),
				$dbo->qn('g.id', 'id_group'),
			))
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
						'name' 	=> $s['group_name'],
						'list' 	=> array(),
					);

					$last_id_group = $s['id_group'];
				}

				$services[count($services)-1]['list'][] = array(
					'id' 	=> $s['id'],
					'name' 	=> $s['service_name'],
				);
			}
		}

		// get assigned services

		$assoc = array();

		if (!empty($package['id']))
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_service'))
				->from($dbo->qn('#__vikappointments_package_service'))
				->where($dbo->qn('id_package') . ' = ' . $package['id']);
			
			$dbo->setQuery($q);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$assoc = $dbo->loadColumn();
			}
		}
		
		$this->package 		= &$package;
		$this->packGroups 	= &$groups;
		$this->services 	= &$services;
		$this->assoc 		= &$assoc;
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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITPACKAGE'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWPACKAGE'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('savePackage', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndClosePackage', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewPackage', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelPackage', JText::_('VAPCANCEL'));
	}

	/**
	 * Returns a blank item.
	 *
	 * @return 	array 	A blank item for new requests.
	 */
	protected function getBlankItem()
	{
		return array(
			'id'			=> -1,
			'name' 			=> '',
			'description' 	=> '',
			'price' 		=> 0.0,
			'num_app' 		=> 1,
			'published' 	=> 0,
			'start_ts'		=> -1,
			'end_ts'		=> -1,
			'id_group' 		=> -1,
			'level'			=> 1,
		);
	}
}
