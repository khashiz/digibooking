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
 * Employee edit location controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpEditLocation extends UIControllerAdmin
{
	/**
	 * Save employee location.
	 *
	 * @return 	void
	 */
	public function save()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		// get args
		$args = array();
		$args['name'] 			= $input->getString('name', '');
		$args['id_country'] 	= $input->getInt('id_country', 0);
		$args['id_state'] 		= $input->getInt('id_state', 0);
		$args['id_city'] 		= $input->getInt('id_city', 0);
		$args['address'] 		= $input->getString('address', '');
		$args['zip'] 			= $input->getString('zip', '');
		$args['latitude'] 		= $input->getFloat('latitude', 0);
		$args['longitude'] 		= $input->getFloat('longitude', 0);
		$args['id'] 			= $input->getInt('id', -1);
		$args['id_employee'] 	= $auth->id;
		
		// authorise
		if (!$auth->manageLocations($args['id']))
		{
			$app->enqueueMessage(JText::_('VAPEMPLOCATIONEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emplocations');
			exit;
		}

		// validation

		if (empty($args['latitude']) || empty($args['longitude']))
		{
			$args['latitude'] 	= null;
			$args['longitude'] 	= null;
		}

		/**
		 * The ZIP code is no more a required field,
		 * as a few countries may not use it.
		 *
		 * @since 1.6.1
		 */
		$required = array('name', 'id_country', 'address');

		foreach ($required as $key)
		{
			if (empty($args[$key]))
			{
				$app->enqueueMessage(JText::_('VAPERRINSUFFCUSTF'), 'error');
				$this->redirect('index.php?option=com_vikappointments&view=emplocations');
				exit;
			} 
		}

		// bind data
		$data = (object) $args;

		// insert new location
		if ($data->id <= 0)
		{
			$dbo->insertObject('#__vikappointments_employee_location', $data, 'id');

			if ($data->id > 0)
			{
				$app->enqueueMessage(JText::_('VAPEMPLOCATIONCREATED1'));
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPLOCATIONCREATED0'), 'error');
			}
		}
		// update existing location
		else
		{
			$dbo->updateObject('#__vikappointments_employee_location', $data, 'id', true);

			if ($dbo->getAffectedRows())
			{
				$app->enqueueMessage(JText::_('VAPEMPLOCATIONUPDATED1'));
			}
		}
		
		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=emplocations';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empeditlocation&cid[]=' . $data->id;
		}

		$this->redirect($url);
	}

	/**
	 * Removes the location.
	 *
	 * @return 	void
	 */
	public function delete()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		$cid = $input->getUint('cid', array(0));
		$id  = array_shift($cid);

		if (!$auth->manageLocations($id))
		{
			$app->enqueueMessage(JText::_('VAPEMPLOCATIONEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emplocations');
			exit;
		}

		// remove location
		$q = $dbo->getQuery(true)
			->delete($dbo->qn('#__vikappointments_employee_location'))
			->where($dbo->qn('id') . ' = ' . $id);

		$dbo->setQuery($q);
		$dbo->execute();

		$affected = (bool) $dbo->getAffectedRows();

		// unset location from working days
		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_emp_worktime'))
			->set($dbo->qn('id_location') . ' = -1')
			->where($dbo->qn('id_location') . ' = ' . $id);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($affected)
		{
			$app->enqueueMessage(JText::_('VAPEMPLOCATIONREMOVED1'));
		}

		$this->redirect('index.php?option=com_vikappointments&view=emplocations');
	}
}
