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
 * Employee edit profile controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpLogin extends UIControllerAdmin
{
	/**
	 * Performs the logout of the employee.
	 *
	 * @return 	void
	 */
	public function logout()
	{
		$app = JFactory::getApplication();
		$app->logout(JFactory::getUser()->id);

		$this->redirect('index.php?option=com_vikappointments&view=emplogin');
	}

	/**
	 * Method used to return the ID of the reservation at the given date and time.
	 *
	 * @return 	void
	 */
	public function getReservationAt()
	{
		$input = JFactory::getApplication()->input;
		$auth  = EmployeeAuth::getInstance();

		$res = array(0, JText::_('VAPEMPLOYEEAPPNOTFOUND'));

		if ($auth->isEmployee())
		{
			$id_emp = $auth->id;
			$day 	= $input->getUint('day', 0);
			$hour 	= $input->getUint('hour', 0);
			$min 	= $input->getUint('min', 0);
			
			$ts = $day + ($hour * 3600) + ($min * 60);
			
			$app = VikAppointments::getEmployeeAppointmentAt($id_emp, $ts);

			if (count($app))
			{
				$ids = array_map(function($row)
				{
					return $row['rid'];
				}, $app);

				$res = array(1, $ids);
			}
		}
		
		echo json_encode($res);
		die;
	}

	/**
	 * AJAX end-point to access the reservations list (in HTML)
	 * for the given day.
	 *
	 * @return 	void
	 */
	public function getDayReservations()
	{
		$input 	= JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();
		$auth 	= EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			echo json_encode(array(0, ''));
			die;
		}

		$id_emp = $auth->id;
		$day 	= $input->getInt('day', 0);
		$id_ser = $input->getInt('id_ser', 0);
		$itemid = $input->getInt('Itemid', 0);
		
		// set timezone
		$emp_tz = VikAppointments::getEmployeeTimezone($id_emp);
		VikAppointments::setCurrentTimezone($emp_tz);

		if (VikAppointments::isClosingDay($day, $id_ser))
		{
			echo json_encode(array(0, ''));
			die;
		}
		
		$worktime = VikAppointments::getEmployeeWorkingTimes($id_emp, $id_ser, $day);
		
		if (count($worktime) == 0)
		{
			echo json_encode(array(0, ''));
			die;
		}
		
		$res = VikAppointments::getAllEmployeeExtendedReservations($id_emp, $id_ser, $day, $day + 86399);
		
		if (count($res) == 0)
		{
			echo json_encode(array(1, JText::_('VAPNORESERVATION')));
			die;
		}

		// check if the employee owns at least a service with maximum capacity higher than 1
		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
			->where(array(
				$dbo->qn('a.id_employee') . ' = ' . $id_emp,
				$dbo->qn('s.published') . ' = 1',
				$dbo->qn('s.max_capacity') . ' > 1',
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$has_cap = (bool) $dbo->getNumRows();

		// build reservations list
		$data = array(
			'auth' 			=> $auth,
			'has_capacity' 	=> $has_cap,
			'timezone' 		=> $emp_tz,
			'orders' 		=> $res,
			'itemid' 		=> $itemid,
		);

		$tab = JLayoutHelper::render('emparea.dayorders', $data);
		
		echo json_encode(array(1, $tab));
		die;
	}
}
