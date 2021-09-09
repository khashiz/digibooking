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
 * Employee edit service controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpEditService extends UIControllerAdmin
{
	/**
	 * Save employee service.
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
		$args['name'] 				= $input->getString('name', '');
		$args['description'] 		= $input->getRaw('description', '');
		$args['duration'] 			= $input->getUint('duration', 0);
		$args['sleep'] 				= $input->getInt('sleep', 0);
		$args['interval'] 			= $input->getInt('interval', 0);
		$args['price'] 				= $input->getFloat('price', 0);
		$args['max_capacity'] 		= $input->getUint('max_capacity', 1);
		$args['min_per_res'] 		= $input->getUint('min_per_res', 1);
		$args['max_per_res'] 		= $input->getUint('max_per_res', 1);
		$args['priceperpeople'] 	= $input->getUint('priceperpeople', 0);
		$args['published'] 			= $input->getUint('published', 0);
		$args['has_own_cal'] 		= $input->getUint('has_own_cal', 0);
		$args['enablezip'] 			= $input->getUint('enablezip', 0);
		$args['use_recurrence'] 	= $input->getUint('use_recurrence', 0);
		$args['start_publishing'] 	= $input->getString('start_publishing', ''); 
		$args['end_publishing'] 	= $input->getString('end_publishing', '');
		$args['id_group'] 			= $input->getInt('group', -1);
		$args['id'] 				= $input->getInt('id', -1);

		$img = $input->files->get('image', null, 'array');
		
		// authorise
		if ($args['id'] > 0 && !$auth->manageServices($args['id']))
		{
			$app->enqueueMessage(JText::_('VAPEMPSEREDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empserviceslist');
			exit;
		}
		else if ($args['id'] <= 0 && !$auth->create())
		{
			$app->enqueueMessage(JText::_('VAPEMPSERNEWAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empserviceslist');
			exit;
		}

		// make sure the employee haven't reached the limit
		if ($args['id'] <= 0)
		{
			$q = $dbo->getQuery(true)
				->select('COUNT(1)')
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
				->where(array(
					$dbo->qn('a.id_employee') . ' = ' . $auth->id,
					$dbo->qn('s.createdby') . ' = ' . $auth->jid, // consider only the services creates by the employee
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			$count = (int) $dbo->loadResult();

			if ($count >= $auth->getServicesMaximumNumber())
			{
				$app->enqueueMessage(JText::_('VAPEMPSERMAXNUMERR'), 'error');
				$this->redirect('index.php?option=com_vikappointments&view=empserviceslist');
				exit;
			}
		}

		// validation
		$args['price'] = abs($args['price']);
		
		$args['max_per_res'] = min(array($args['max_per_res'], $args['max_capacity']));
		$args['min_per_res'] = min(array($args['min_per_res'], $args['max_per_res']));

		if (empty($args['start_publishing']) || empty($args['end_publishing']))
		{
			$args['start_publishing'] = $args['end_publishing'] = -1;
		}
		else
		{
			$args['start_publishing'] 	= VikAppointments::createTimestamp($args['start_publishing'], 0, 0, true);
			$args['end_publishing'] 	= VikAppointments::createTimestamp($args['end_publishing'], 23, 59, true);
			
			if ($args['start_publishing'] > $args['end_publishing'])
			{
				$args['start_publishing'] = $args['end_publishing'] = -1;
			}
		}
		
		$required = array('name');

		foreach ($required as $key)
		{
			if (empty($args[$key]))
			{
				$app->enqueueMessage(JText::_('VAPEMPSERUPDATED0'), 'error');
				$this->redirect('index.php?option=com_vikappointments&view=empserviceslist');
				exit;
			} 
		}

		// upload picture
		$ori = VikAppointments::uploadImage($img, VAPMEDIA . DIRECTORY_SEPARATOR);
		
		if ($ori['esit'] == -1)
		{
			$app->enqueueMessage(JText::_('VAPCONFIGUPLOADERROR'), 'error');
		}
		else if ($ori['esit'] == -2)
		{
			$app->enqueueMessage(JText::_('VAPCONFIGFILETYPEERROR'), 'error');
		}
		else if ($ori['esit'] == 1)
		{
			UILoader::import('libraries.image.resizer');
			
			$original 	= VAPMEDIA . DIRECTORY_SEPARATOR . $ori['name'];
			$thumb 		= VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $ori['name'];

			VikAppointmentsImageResizer::proportionalImage($original,  $thumb, VikAppointments::getSmallWidthResize(), VikAppointments::getSmallHeightResize());

			/**
			 * Crop also the original image, if needed.
			 *
			 * @since 1.6.1
			 */
			if (VikAppointments::isImageResize())
			{
				$final_dest = VAPMEDIA . DIRECTORY_SEPARATOR . $ori['name'];
				$crop_dest 	= str_replace($ori['name'], '$_' . $ori['name'], $final_dest);
				VikAppointmentsImageResizer::proportionalImage($final_dest, $crop_dest, VikAppointments::getOriginalWidthResize(), VikAppointments::getOriginalHeightResize());
				
				copy($crop_dest, $final_dest);
				unlink($crop_dest);
			}

			$args['image'] = $ori['name'];
		}

		/**
		 * Validate group against the existing one.
		 * Fixed an issue that was considering the groups
		 * for the employees instead the ones for the services.
		 *
		 * @since 1.6.2
		 */
		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_group'))
			->where($dbo->qn('id') . ' = ' . $args['id_group']);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if (!$dbo->getNumRows())
		{
			$args['id_group'] = -1;
		} 

		// bind data
		$data = (object) $args;

		// insert new service
		if ($data->id <= 0)
		{
			// get ordering
			$q = $dbo->getQuery(true)
				->select('MAX(' . $dbo->qn('ordering') . ')')
				->from($dbo->qn('#__vikappointments_service'));

			$dbo->setQuery($q);
			$dbo->execute();

			$data->ordering  = (int) $dbo->loadResult() + 1;
			$data->createdby = JFactory::getUser()->id;

			/**
			 * Generate an alias automatically every time a new service is created from the front-end.
			 * The alias is based on the specified service name.
			 * Once the service is placed, the alias could be changed only from the back-end.
			 *
			 * @since 1.6.2
			 */
			UILoader::import('libraries.helpers.alias');
			$data->alias = AliasHelper::getUniqueAlias($data->name, 'service');

			$dbo->insertObject('#__vikappointments_service', $data, 'id');

			if ($data->id)
			{
				$app->enqueueMessage(JText::_('VAPEMPSERCREATED1'));
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPSERCREATED0'), 'error');
			}

			// insert employee-service relationship
			$assoc = new stdClass;
			$assoc->id_service 	= $data->id;
			$assoc->id_employee = $auth->id;
			$assoc->rate 		= $data->price;
			$assoc->duration 	= $data->duration;
			$assoc->sleep 		= $data->sleep;

			$dbo->insertObject('#__vikappointments_ser_emp_assoc', $assoc, 'id');

			// attach working days to the service
			$this->attachWorkingDays($data->id, $auth->id);
		}
		// update existing service
		else
		{
			$dbo->updateObject('#__vikappointments_service', $data, 'id');

			if ($dbo->getAffectedRows())
			{
				$app->enqueueMessage(JText::_('VAPEMPSERUPDATED1'));
			}

			// update employee-service relationship
			$assoc = new stdClass;
			$assoc->id_service 	= $data->id;
			$assoc->id_employee = $auth->id;
			$assoc->rate 		= $data->price;
			$assoc->duration 	= $data->duration;
			$assoc->sleep 		= $data->sleep;

			$dbo->updateObject('#__vikappointments_ser_emp_assoc', $assoc, array('id_employee', 'id_service'));
		}
		
		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=empserviceslist';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empeditservice&cid[]=' . $data->id;
		}

		$this->redirect($url);
	}

	/**
	 * Attaches the working days of the employee to the specified service.
	 *
	 * @param 	integer  $id_service 	The service ID.
	 * @param 	integer  $id_employee 	The employee ID.
	 *
	 * @return 	boolean  True if attached, otherwise false.
	 */
	public function attachWorkingDays($id_service, $id_employee)
	{
		$dbo = JFactory::getDbo();

		$attached = false;

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_emp_worktime'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $id_employee,
				$dbo->qn('id_service') . ' = -1',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadObjectList() as $w)
			{
				/**
				 * Keep a relation with the parent working day
				 * while assigning a new employee to this service.
				 *
				 * @since 1.6.2
				 */
				$w->parent     = $w->id;
				
				// inject service ID
				$w->id_service = $id_service;
				// unset ID for insert
				unset($w->id);

				$dbo->insertObject('#__vikappointments_emp_worktime', $w, 'id');

				$attached = $attached || $w->id;
			}
		}

		return $attached;
	}

	/**
	 * Updates the service overrides of the employee.
	 *
	 * @return 	void
	 */
	public function saveRate()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		// authorise
		if (!$auth->manageServicesRates())
		{
			$app->enqueueMessage(JText::_('VAPEMPSEREDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empserviceslist');
			exit;
		}
		
		$args = array();
		$args['rate']        = $input->getFloat('price', 0);
		$args['duration']    = $input->getUint('duration', 0);
		$args['sleep']       = $input->getInt('sleep', 0);
		$args['description'] = $input->getRaw('description', '');
		$args['id_service']  = $input->getUint('id', 0);
		
		// bind object
		$data = (object) $args;
		$data->id_employee = $auth->id;

		$dbo->updateObject('#__vikappointments_ser_emp_assoc', $data, array('id_service', 'id_employee'));
		
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPEMPSERUPDATED1'));
		}

		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=empserviceslist';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empeditservice&cid[]=' . $data->id_service;
		}

		$this->redirect($url);
	}

	/**
	 * Removes the service or detach it from the employee.
	 *
	 * @return 	void
	 */
	public function delete()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		if (!$auth->remove())
		{
			$app->enqueueMessage(JText::_('VAPEMPSERDELAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empserviceslist');
			exit;
		}

		$cid = $input->getUint('cid', array());
		
		// shift the ID if the array contains only one value
		if (count($cid) == 1)
		{
			$cid  = array_shift($cid);
		}

		// if the service has been created by this employee,
		// we can remove the service from the DB
		$removed = $this->_delete($cid, '#__vikappointments_service', array('createdby' => $auth->jid));

		// detach relationship
		$options = array();
		$options['table'] 	= '#__vikappointments_ser_emp_assoc';
		$options['pk']		= 'id_service';

		$removed = $this->_delete($cid, $options, array('id_employee' => $auth->id)) || $removed;

		// remove working days
		$options = array();
		$options['table'] 	= '#__vikappointments_emp_worktime';
		$options['pk']		= 'id_service';

		$removed = $this->_delete($cid, $options, array('id_employee' => $auth->id)) || $removed;

		if ($removed)
		{
			$app->enqueueMessage(JText::_('VAPEMPSERREMOVED1'));
		}

		$this->redirect('index.php?option=com_vikappointments&view=empserviceslist&limitstart=0');
	}

	/**
	 * Moves the selected record up or down.
	 *
	 * @return 	void
	 */
	public function move()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$auth = EmployeeAuth::getInstance();

		$cid = $input->getUint('cid', array(0));
		$id  = array_shift($cid);

		// make sure the employee is the owner
		if (!$auth->manageServices($id))
		{
			$app->enqueueMessage(JText::_('VAPEMPSERVICERESTRICTED'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empserviceslist');
			exit;
		}

		$mode = $input->getString('mode', 'up');

		// dispatch sort to parent class
		$this->_move($id, '#__vikappointments_service', $mode, array('createdby' => $auth->jid));

		$this->redirect('index.php?option=com_vikappointments&view=empserviceslist');
	}
}
