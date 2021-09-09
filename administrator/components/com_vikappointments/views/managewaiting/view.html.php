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
class VikAppointmentsViewmanagewaiting extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();
		VikAppointments::load_complex_select();

		$app 	= JFactory::getApplication();
		$input	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$type = $input->getString('type');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$waiting = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('`w`.*')
				->select($dbo->qn('u.id', 'id_user'))
				->from($dbo->qn('#__vikappointments_waitinglist', 'w'))
				->leftjoin($dbo->qn('#__vikappointments_users', 'u') . ' ON ' . $dbo->qn('w.jid') . ' = ' . $dbo->qn('u.jid'))
				->where($dbo->qn('w.id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$waiting = $dbo->loadAssoc();
			}
		}

		if (empty($waiting))
		{
			$waiting = $this->getBlankItem();
		}

		// get services

		$services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('published') . ' = 1')
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$services = $dbo->loadAssocList();
		}

		if ($waiting['id_service'] == -1)
		{
			$waiting['id_service'] = count($services) ? $services[0]['id'] : -1;
		}

		$employees = array();

		// get employees

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('e.id', 'e.nickname')))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('a.id_employee') . ' = ' . $dbo->qn('e.id'))
			->where(array(
				$dbo->qn('a.id_service') . ' = ' . $waiting['id_service'],
				$dbo->qn('e.listable') . ' = 1',
			))
			->order($dbo->qn('e.nickname') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList();
		}
		
		$this->waiting 		= &$waiting;
		$this->services 	= &$services;
		$this->employees 	= &$employees;

		// Display the template
		parent::display($tpl);
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
			'id_service' 	=> -1,
			'id_employee' 	=> -1,
			'timestamp' 	=> time(),
			'id_user' 		=> '',
			'email' 		=> '',
			'phone_prefix' 	=> '',
			'phone_number' 	=> '',
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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITWAITING'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWWAITING'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveWaiting', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseWaiting', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewWaiting', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel( 'cancelWaitinglist', JText::_('VAPCANCEL'));
	}
}
