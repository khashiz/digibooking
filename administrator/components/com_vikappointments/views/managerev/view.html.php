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
class VikAppointmentsViewmanagerev extends JViewUI
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
		$tab  = $app->getUserStateFromRequest('vapsavereview.tab', 'tabname', 'review_details', 'string');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$review = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));

			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_reviews'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$review = $dbo->loadAssoc();
			}
		}

		if (empty($review))
		{
			$review = $this->getBlankItem();
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

		// get services

		$services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_service'))
			->order($dbo->qn('name') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$services = $dbo->loadAssocList();
		}

		// get users

		$users = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'username', 'email')))
			->from($dbo->qn('#__users'))
			->order($dbo->qn('username') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$users = $dbo->loadAssocList();
		}
		
		$this->review 		= &$review;
		$this->employees 	= &$employees;
		$this->services 	= &$services;
		$this->users 		= &$users;
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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITREVIEW'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWREVIEW'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveReview', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseReview', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewReview', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelReview', JText::_('VAPCANCEL'));
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
			'title' 		=> '',
			'name' 			=> '',
			'email' 		=> '',
			'jid' 			=> '',
			'comment' 		=> '',
			'published' 	=> 0,
			'timestamp' 	=> time(),
			'rating' 		=> 5,
			'langtag' 		=> 'en-GB',
			'id_employee' 	=> -1,
			'id_service' 	=> -1,
		);
	}
}
