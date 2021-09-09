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
class VikAppointmentsViewmanagemailtext extends JViewUI
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

		$dbo   = JFactory::getDbo();
		$app   = JFactory::getApplication();
		$input = $app->input;
		
		$type = $input->getString('type');
		$tab  = $app->getUserStateFromRequest('vapsavemailtext.tab', 'tabname', 'mailtext_details', 'string');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$sel = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_cust_mail'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$sel = $dbo->loadAssoc();
			}
		}

		if (empty($sel))
		{
			$sel = $this->getBlankItem();
		}

		// get services
		$services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_service'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$services = $dbo->loadObjectList();
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
			$employees = $dbo->loadObjectList();
		}
		
		$this->sel 		 = &$sel;
		$this->tab 		 = &$tab;
		$this->services  = &$services;
		$this->employees = &$employees;

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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITMAILTEXT'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWMAILTEXT'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveCustMail', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseCustMail', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewCustMail', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			if ($type == 'edit')
			{
				JToolBarHelper::custom('saveAsCopyCustMail', 'save-copy', 'save-copy', JText::_('VAPSAVEASCOPY'), false);
			}
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelCustMail', JText::_('VAPCANCEL'));
	}

	/**
	 * Returns a blank item.
	 *
	 * @return 	array 	 A blank item for new requests.
	 */
	protected function getBlankItem()
	{
		return array(
			'id'		  => -1,
			'name' 		  => '',
			'position' 	  => '',
			'status' 	  => '',
			'tag' 		  => '',
			'file' 		  => '',
			'content' 	  => '',
			'id_service'  => 0,
			'id_employee' => 0,
		);
	}
}
