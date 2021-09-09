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
class VikAppointmentsViewempeditprofile extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_datepicker_regional();

		$dbo = JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->isEmployee()) {
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		// get groups
		$groups = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_employee_group'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}

		// get custom fields
		$fields = VAPCustomFields::getList(1, null, null, CF_EXCLUDE_REQUIRED_CHECKBOX);
		
		$this->groups 	= &$groups;
		$this->auth 	= &$auth;
		$this->fields 	= &$fields;
		
		// Display the template
		parent::display($tpl);
	}
}
