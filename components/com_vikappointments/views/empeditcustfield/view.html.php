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
class VikAppointmentsViewempeditcustfield extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		$type = 1;
		$cid  = $input->getUint('cid', array());
		
		$custfield = array();

		if (count($cid))
		{
			$type = 2;
			
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_custfields'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $auth->id,
					$dbo->qn('id') . ' = ' . $cid[0],
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$custfield = $dbo->loadAssoc();
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPCUSTOMFEDITAUTH0'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
				exit;
			}
		}
		// do not create if not authorised
		else if (!$auth->manageCustomFields())
		{
			$app->enqueueMessage(JText::_('VAPEMPCUSTOMFEDITAUTH0'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}

		if (empty($custfield))
		{
			$custfield = $this->getBlankItem();
		}

		$countries = VikAppointmentsLocations::getCountries();
		
		$this->auth 		= &$auth;
		$this->customField 	= &$custfield;
		$this->countries 	= &$countries;
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Returns a blank item for the creation page.
	 *
	 * @return 	array 	The item.
	 */
	protected function getBlankItem()
	{
		return array(
			'name' 			=> '',
			'type' 			=> 'text',
			'rule'			=> 0,
			'required' 		=> 0,
			'multiple' 		=> 0,
			'poplink' 		=> '',
			'choose' 		=> '',
			'id' 			=> -1,
		);
	}
}
