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
class VikAppointmentsViewmanagesubscrorder extends JViewUI
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
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$order = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_subscr_order'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$order = $dbo->loadAssoc();
			}
		}

		if (empty($order))
		{
			$order = $this->getBlankItem();
		}
		
		// get subscriptions

		$subscriptions = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name', 'price', 'published')))
			->from($dbo->qn('#__vikappointments_subscription'))
			->order(array(
				$dbo->qn('published') . ' DESC',
				$dbo->qn('price') . ' ASC'
			));

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$subscriptions = $dbo->loadAssocList();
		}

		// get employees
		
		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'lastname', 'firstname')))
			->from($dbo->qn('#__vikappointments_employee'))
			->order(array(
				$dbo->qn('lastname') . ' ASC',
				$dbo->qn('firstname') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList();
		}

		// get payments
		
		$payments = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name', 'published')))
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('subscr') . ' = 1')
			->order(array(
				$dbo->qn('published') . ' DESC',
				$dbo->qn('ordering') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$payments = $dbo->loadAssocList();
		}
		
		$this->order 			= &$order;
		$this->subscriptions 	= &$subscriptions;
		$this->employees 		= &$employees;
		$this->payments 		= &$payments;

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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITSUBSCRORDER'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWSUBSCRORDER'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveSubscriptionOrder', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseSubscriptionOrder', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewSubscriptionOrder', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelSubscriptionOrder', JText::_('VAPCANCEL'));
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
			'id_subscr' 	=> -1,
			'id_employee' 	=> -1,
			'id_payment' 	=> -1,
			'total_cost' 	=> 0.0,
			'status' 		=> 'PENDING',
		);
	}
}
