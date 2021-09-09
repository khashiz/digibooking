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

UILoader::import('libraries.controllers.admin');

/**
 * Employee edit settings controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpSettings extends UIControllerAdmin
{
	/**
	 * Save employee settings.
	 *
	 * @return 	void
	 */
	public function save()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->manage())
		{
			$app->enqueueMessage(JText::_('VAPEDITEMPAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empsettings');
			exit;
		}
		
		// get settings
		$args = array();
		$args['listlimit'] 		= $input->getUint('listlimit', 5);
		$args['listposition'] 	= $input->getUint('listposition', 1);
		$args['listordering'] 	= $input->getString('listordering', 'ASC');
		$args['numcals'] 		= $input->getUint('numcals', 6);
		$args['firstmonth'] 	= $input->getInt('firstmonth', -1);
		$args['zip_field_id'] 	= $input->getInt('zip_field_id', -1);
		$args['zipcodes'] 		= array();

		// get employee data
		$emp = array();
		$emp['synckey']  = $input->getString('synckey', '');
		$emp['timezone'] = $input->getString('timezone', '');

		// build zip codes JSON
		$zc_from_arr = $input->get('zip_code_from', array(), 'array');
		$zc_to_arr 	 = $input->get('zip_code_to', array(), 'array');

		if (count($zc_from_arr))
		{
			$_len = min(array(count($zc_from_arr), count($zc_to_arr)));

			for ($i = 0; $i < $_len; $i++)
			{
				if (!empty($zc_from_arr[$i]))
				{
					if (empty($zc_to_arr[$i]))
					{
						$zc_to_arr[$i] = $zc_from_arr[$i];
					}
					
					$args['zipcodes'][] = array(
						'from' 	=> $zc_from_arr[$i],
						'to' 	=> $zc_to_arr[$i],
					);
				}
			}
		}

		$args['zipcodes'] = json_encode($args['zipcodes']);

		// validate ZIP code
		if ($args['zip_field_id'] != -1 && !$auth->manageCustomFields($args['zip_field_id']))
		{
			$args['zip_field_id'] = -1;
		}

		// validate list limit
		if (!in_array($args['listlimit'], array(5, 10, 15, 20, 50)))
		{
			$args['listlimit'] = 5;
		}

		// validate list position
		if (!in_array($args['listposition'], array(1, 2)))
		{
			$args['listposition'] = 1;
		}

		// validate list ordering
		if (!in_array($args['listordering'], array('ASC', 'DESC')))
		{
			$args['listordering'] = 'ASC';
		}

		// validate number of calendars
		if (!in_array($args['numcals'], array(1, 3, 6, 9, 12)))
		{
			$args['numcals'] = 6;
		}

		// validate first month
		if ($args['firstmonth'] != -1 && ($args['firstmonth'] < 1 || $args['firstmonth'] > 12))
		{
			$args['firstmonth'] = -1;
		}

		// fill SYNC KEY if empty
		if (empty($emp['synckey']))
		{
			$emp['synckey'] = VikAppointments::generateSerialCode(12);
		}

		// update settings
		$data = (object) $args;
		$data->id_employee = $auth->id;
		
		$dbo->updateObject('#__vikappointments_employee_settings', $data, 'id_employee');

		$aff = $dbo->getAffectedRows();

		// update employee data
		$data = (object) $emp;
		$data->id = $auth->id;

		$dbo->updateObject('#__vikappointments_employee', $data, 'id');

		if ($aff || $dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPEMPSETTINGSUPDATED'));

			// refresh settings
			VikAppointments::refreshEmployeeSettings($auth->id);
		}
		
		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=emplogin';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empsettings';
		}

		$this->redirect($url);
	}
}
