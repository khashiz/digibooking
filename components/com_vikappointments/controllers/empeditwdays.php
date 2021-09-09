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
 * Employee edit working day controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpEditWdays extends UIControllerAdmin
{
	/**
	 * Save employee working day.
	 *
	 * @return 	void
	 */
	public function save()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->manageWorkDays())
		{
			$app->enqueueMessage(JText::_('VAPEMPWDAYEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empwdays');
			exit;
		}
		
		$args = array();
		$args['type']		 = $input->getUint('type', 1);
		$args['day'] 		 = $input->getUint('day', 0);
		$args['from_hour'] 	 = $input->getUint('from_hour');
		$args['from_min'] 	 = $input->getUint('from_min');
		$args['end_hour'] 	 = $input->getUint('end_hour');
		$args['end_min'] 	 = $input->getUint('end_min');
		$args['closed'] 	 = $input->getUint('closed', 0);
		$args['date_from'] 	 = $input->getString('date_from', '');
		$args['date_to'] 	 = $input->getString('date_to', '');
		$args['id_location'] = $input->getInt('id_location', -1);
		$args['id'] 		 = $input->getInt('id', -1);

		if ($args['type'] == 2)
		{
			$args['day'] = -1;
		}

		if (empty($args['id_location']))
		{
			$args['id_location'] = -1;
		}

		$op_time = VikAppointments::getOpeningTime();
		$cl_time = VikAppointments::getClosingTime();

		// validation
		if ($args['day'] < -1 || $args['day'] > 6)
		{
			$args['day'] = 0;
		}
		if ($args['from_hour'] <= $op_time['hour'])
		{
			$args['from_hour'] = $op_time['hour'];
			if ($args['from_min'] < $op_time['min'])
			{
				$args['from_min'] = $op_time['min'];
			}
		}

		if ($args['from_min'] % 5 !== 0 || $args['from_min'] >= 60)
		{
			$args['from_min'] = 0;
		}

		if ($args['end_min'] % 5 !== 0 || $args['end_min'] >= 60)
		{
			$args['end_min'] = 0;
		}

		if ($args['end_hour'] >= $cl_time['hour'])
		{
			$args['end_hour'] = $cl_time['hour'];
			if ($args['end_min'] > $cl_time['min'])
			{
				$args['end_min'] = $cl_time['min'];
			}
		}

		$default_tz = date_default_timezone_get();

		$start_ts = $end_ts = -1;

		if ($args['day'] == -1)
		{
			/**
			 * Timestamp has to be stored in UTC.
			 *
			 * @since 1.6.1
			 */
			date_default_timezone_set('UTC');

			$start_ts 	= VikAppointments::jcreateTimestamp($args['date_from'], 0, 0);
			$end_ts 	= VikAppointments::jcreateTimestamp($args['date_to'], 0, 0);
			if ($end_ts < $start_ts)
			{
				$end_ts = $start_ts;
			}

			date_default_timezone_set($default_tz);
		}

		$args['fromts'] = $args['from_hour'] * 60 + $args['from_min'];
		$args['endts'] 	= $args['end_hour'] * 60 + $args['end_min'];
		if ($args['endts'] < $args['fromts'])
		{
			$args['endts'] = $cl_time['hour'] * 60 + $cl_time['min'];
		}

		// bind data
		$data = new stdClass;
		$data->id 			= $args['id'];
		$data->day 			= $args['day'];
		$data->ts 			= $start_ts;
		$data->fromts 		= $args['fromts'];
		$data->endts 		= $args['endts'];
		$data->closed 		= $args['closed'];
		$data->id_location 	= $args['id_location'];
		$data->id_employee 	= $auth->id;

		$return_id = $data->id;

		// insert new working days
		if ($data->id <= 0)
		{
			// get services
			$services = array();

			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_service'))
				->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
				->where($dbo->qn('id_employee') . ' = ' . $auth->id);

			/**
			 * Copy the inserted working day(s) only for the selected services.
			 *
			 * @since 1.6.2
			 */
			$selected_services = $input->get('services', array(), 'uint');

			if ($selected_services)
			{
				// extend query instead of using the $selected_services array to make sure 
				// that the employee really owns the specified services
				$q->where($dbo->qn('id_service') . ' IN (' . implode(',', $selected_services) . ')');
			}

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$services = $dbo->loadColumn();
			}	

			$ok = true;

			// insert working days until the start_ts is higher than the end_ts
			while ($data->ts <= $end_ts)
			{
				// reset attrs for default working day
				$data->id 			= -1;
				$data->id_service 	= -1;
				unset($data->parent);

				$dbo->insertObject('#__vikappointments_emp_worktime', $data, 'id');

				$ok = $ok && $data->id;

				$return_id = $data->id;
				
				$data->parent = $data->id;
				foreach ($services as $sid)
				{
					// reset attrs for child days
					$data->id 			= -1;
					$data->id_service 	= $sid;

					$dbo->insertObject('#__vikappointments_emp_worktime', $data, 'id');
				}

				/**
				 * Timestamp has to be stored in UTC.
				 *
				 * @since 1.6.1
				 */
				date_default_timezone_set('UTC');

				// go to the next day
				$date = ArasJoomlaVikApp::jgetdate($data->ts);
				$data->ts = ArasJoomlaVikApp::jmktime(0, 0, 0, $date['mon'], $date['mday'] + 1, $date['year']);

				date_default_timezone_set($default_tz);
			}

			if ($ok)
			{
				$app->enqueueMessage(JText::_('VAPEMPWDCREATED1'));
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPWDCREATED0'), 'error');
			}
		}
		// update existing working day
		else
		{
			// update original working day (update only if same id_employee)
			$dbo->updateObject('#__vikappointments_emp_worktime', $data, array('id', 'id_employee'));

			$aff = $dbo->getAffectedRows();

			// update related working day
			$data->parent = $data->id;
			unset($data->id);

			// update only if same id_employee
			$dbo->updateObject('#__vikappointments_emp_worktime', $data, array('parent', 'id_employee'));

			if ($aff || $dbo->getAffectedRows())
			{
				$app->enqueueMessage(JText::_('VAPEMPWDUPDATED1'));
			}
		}
		
		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=empwdays';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empeditwdays&cid[]=' . $return_id;
		}

		$this->redirect($url);
	}

	/**
	 * Deletes a list of selected working days.
	 *
	 * @return 	void
	 */
	public function delete()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->manageWorkDays())
		{
			$app->enqueueMessage(JText::_('VAPEMPWDAYEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empwdays');
			exit;
		}
		
		$cid = $input->getUint('cid', array());
		$aff = false;

		// if the working days is owned by the employee we can remove it from the DB
		$removed = $this->_delete($cid, '#__vikappointments_emp_worktime', array('id_employee' => $auth->id));

		// delete children working days
		$options = array();
		$options['table'] 	= '#__vikappointments_emp_worktime';
		$options['pk']		= 'parent';

		$removed = $this->_delete($cid, $options, array('id_employee' => $auth->id)) || $removed;
		
		if ($removed)
		{
			$app->enqueueMessage(JText::_('VAPEMPWDREMOVED1'));
		}

		$this->redirect('index.php?option=com_vikappointments&view=empwdays&limitstart=0');
	}

	/**
	 * Clones the specified working day.
	 *
	 * @return 	void
	 */
	public function duplicate()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		$cid = $input->getUint('cid', array());
		
		if (!$auth->manageWorkDays() || !count($cid))
		{
			$app->enqueueMessage(JText::_('VAPEMPWDAYEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empwdays');
			exit;
		}

		// get employee services
		$services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_service'))
			->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
			->where($dbo->qn('id_employee') . ' = ' . $auth->id);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$services = $dbo->loadColumn();
		}

		// get all the working days (owned by the employee)
		$rows = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_emp_worktime'))
			->where(array(
				$dbo->qn('id') . ' IN (' . implode(',', $cid) . ')',
				$dbo->qn('id_employee') . ' = ' . $auth->id,
			));

		$dbo->setQuery($q, 0, count($cid));
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadObjectList();
		}

		$created = false;
		
		// clone the records
		foreach ($rows as $data)
		{
			/**
			 * Check if we are cloning a day of the week or a day of the year.
			 * In this way, we can unset the opposite property.
			 *
			 * @since 1.6.1
			 */
			if ($data->day != -1)
			{
				$data->day = ($data->day + 1) % 7;
				$data->ts  = -1;
			}
			else
			{
				$data->ts  = strtotime('+1 day 00:00:00', $data->ts);
				$data->day = -1;
			}

			// reset properties
			$data->id_service 	= -1;
			$data->parent 		= -1;
			$data->id 			= 0;

			$dbo->insertObject('#__vikappointments_emp_worktime', $data, 'id');

			$created = $created || $data->id > 0;

			/**
			 * Inherit parent ID.
			 *
			 * @since 1.6.1
			 */
			$data->parent = $data->id;

			// create children working days (for services)
			foreach ($services as $s)
			{
				$data->id_service 	= $s;
				$data->id 			= 0;

				$dbo->insertObject('#__vikappointments_emp_worktime', $data, 'id');
			}
		}

		if ($created)
		{
			$app->enqueueMessage(JText::_('VAPEMPWDCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPEMPWDCREATED0'), 'error');
		}

		$this->redirect('index.php?option=com_vikappointments&view=empwdays');
	}
}
