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
class VikAppointmentsViewmanagecoupon extends JViewUI
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
		$tab  = $app->getUserStateFromRequest('vapsavecoupon.tab', 'tabname', 'coupon_details', 'string');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$coupon = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_coupon'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$coupon = $dbo->loadAssoc();
			}
		}

		if (empty($coupon))
		{
			$coupon = $this->getBlankItem();
		}

		// get coupon groups

		$groups = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_coupon_group'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}

		// get employees

		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('e.id', 'e.nickname', 'e.id_group')))
			->select($dbo->qn('g.name', 'group_name'))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_employee_group', 'g') . ' ON ' . $dbo->qn('e.id_group') . ' = ' . $dbo->qn('g.id'))
			->order(array(
				$dbo->qn('g.ordering') . ' ASC',
				$dbo->qn('e.nickname') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$last_id_group = -2;
			foreach ($dbo->loadAssocList() as $e)
			{
				if ($last_id_group != $e['id_group'])
				{
					$employees[] = array(
						'id' 	=> $e['id_group'],
						'name' 	=> $e['group_name'],
						'list' 	=> array(),
					);

					$last_id_group = $e['id_group'];
				}

				$employees[count($employees)-1]['list'][] = array(
					'id' 	=> $e['id'],
					'name' 	=> $e['nickname'],
				);
			}
		}

		// get coupon employees

		$coupon_employees_assoc_list = array();

		if ($coupon['id'] > 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_employee'))
				->from($dbo->qn('#__vikappointments_coupon_employee_assoc'))
				->where($dbo->qn('id_coupon') . ' = ' . $coupon['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$coupon_employees_assoc_list = $dbo->loadColumn();
			}
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
						'name' 	=> $s['group_name'],
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

		$coupon_services_assoc_list = array();

		if ($coupon['id'] > 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_service'))
				->from($dbo->qn('#__vikappointments_coupon_service_assoc'))
				->where($dbo->qn('id_coupon') . ' = ' . $coupon['id']);
			
			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$coupon_services_assoc_list = $dbo->loadColumn();
			}
		}
		
		$this->coupon 		= &$coupon;
		$this->groups 		= &$groups;
		$this->employees 	= &$employees;
		$this->services 	= &$services;
		$this->tab 			= &$tab;

		$this->couponEmployeesAssocList = &$coupon_employees_assoc_list;
		$this->couponServicesAssocList 	= &$coupon_services_assoc_list;

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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITCOUPON'), 'vikappointments');
		} else {
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWCOUPON'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveCoupon', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseCoupon', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewCoupon', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelCoupon', JText::_('VAPCANCEL'));
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
			'code' 			=> VikAppointments::generateSerialCode(12),
			'type' 			=> 1,
			'percentot' 	=> 2,
			'value' 		=> 0.0,
			'mincost' 		=> 0.0,
			'pubmode' 		=> 1,
			'dstart' 		=> -1,
			'dend' 			=> -1,
			'lastminute' 	=> 0,
			'max_quantity' 	=> 1,
			'used_quantity' => 0,
			'remove_gift' 	=> 0,
			'notes' 		=> '',
			'id_group'		=> 0,
		);
	}
}
