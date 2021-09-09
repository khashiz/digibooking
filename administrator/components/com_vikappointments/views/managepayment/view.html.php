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
class VikAppointmentsViewmanagepayment extends JViewUI
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

		$type 	= $input->getString('type');
		$id_emp = $input->getUint('id_employee', 0);

		// Set the toolbar
		$this->addToolBar($type, $id_emp);
		
		$payment = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_gpayments'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$payment = $dbo->loadAssoc();
			}
		}

		if (empty($payment))
		{
			$payment = $this->getBlankItem($id_emp);
		}

		// check if the current user is the owner of the payment:
		// global payment OR new payment OR current user equals to payment author
		$owner = $payment['id_employee'] == 0 || $payment['id'] <= 0 || $payment['createdby'] == JFactory::getUser()->id;
		
		$this->payment = &$payment;
		$this->isOwner = &$owner;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Returns a blank item.
	 *
	 * @param 	integer  $id_emp 	The ID of the employee.
	 *
	 * @return 	array 	A blank item for new requests.
	 */
	protected function getBlankItem($id_emp)
	{
		return array(
			'id'			=> -1,
			'name' 			=> '',
			'file' 			=> '',
			'published' 	=> 0,
			'prenote' 		=> '',
			'note' 			=> '',
			'charge' 		=> 0.0,
			'icontype' 		=> 0,
			'icon' 			=> '',
			'setconfirmed' 	=> 0,
			'appointments'	=> 1,
			'subscr' 		=> 0,
			'position' 		=> '',
			'level'			=> 1,
			'id_employee'	=> $id_emp,
			'createdby' 	=> 0,
		);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($type, $id_emp)
	{
		// Add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITPAYMENT'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWPAYMENT'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('savePayment', JText::_('VAPSAVE'));
			JToolBarHelper::save($id_emp ? 'saveAndCloseEmployeePayment' : 'saveAndClosePayment', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom($id_emp ? 'saveAndNewEmployeePayment' : 'saveAndNewPayment', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel($id_emp ? 'cancelEmployeePayment' : 'cancelPayment', JText::_('VAPCANCEL'));
	}
}
