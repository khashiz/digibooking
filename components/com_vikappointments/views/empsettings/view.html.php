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
class VikAppointmentsViewempsettings extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$dbo = JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}

		// get settings
		
		$settings = VikAppointments::getEmployeeSettings($auth->id);
		$settings['synckey'] 	= $auth->synckey;
		$settings['timezone'] 	= $auth->timezone;

		// get custom fields

		$mask   = CF_EXCLUDE_REQUIRED_CHECKBOX | CF_EXCLUDE_SEPARATOR;
		$fields = VAPCustomFields::getList(0, $auth->id, 0, $mask);

		$fields = array_filter($fields, function($cf) use ($auth)
		{
			/**
			 * Since the controller doesn't allow the selection of custom
			 * fields that haven't been created by this employee, it doesn't
			 * make sense to display the global fields.
			 *
			 * So, it is needed to filter the list to include only
			 * the fields that are owned by the current employee.
			 *
			 * @since 1.6
			 */
			return $cf['id_employee'] == $auth->id;
		});
		
		$this->auth 		= &$auth;
		$this->settings 	= &$settings;
		$this->customFields = &$fields;
		
		// Display the template
		parent::display($tpl);
	}
}
