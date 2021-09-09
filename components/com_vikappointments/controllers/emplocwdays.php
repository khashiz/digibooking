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
 * Employee edit working days - locations controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpLocWdays extends UIControllerAdmin
{
	/**
	 * A map to check if the employee is the owner of the locations.
	 *
	 * @var array
	 */
	protected $locations = array();

	/**
	 * Save employee working days - locations relationships.
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
			$app->enqueueMessage(JText::_('VAPEDITEMPAUTH0'));
			$this->redirect('index.php?option=com_vikappointments&view=emplocwdays');
			exit;
		}

		// get args
		$locations = $input->get('location', array(), 'array');

		$affected = false;
		
		foreach ($locations as $id_wd => $id_loc)
		{
			$id_wd  = intval($id_wd);
			$id_loc = intval($id_loc);
			
			if (empty($id_loc))
			{
				$id_loc = -1;
			}

			if ($this->isLocationOwner($auth->id, $id_loc))
			{
				$q = $dbo->getQuery(true)
					->update($dbo->qn('#__vikappointments_emp_worktime'))
					->set($dbo->qn('id_location') . ' = ' . $id_loc)
					->where($dbo->qn('id_employee') . ' = ' . $auth->id)
					->andWhere(array(
						$dbo->qn('id') . ' = ' . $id_wd,
						$dbo->qn('parent') . ' = ' . $id_wd,
					), 'OR');

				$dbo->setQuery($q);
				$dbo->execute();

				$affected = $affected || $dbo->getAffectedRows();
			}
		}

		if ($affected)
		{
			$app->enqueueMessage(JText::_('VAPEMPLOCWDAYSUPDATED'));
		}

		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=emplogin';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=emplocwdays';
		}

		$this->redirect($url);
	}

	/**
	 * Checks if the specified employee owns the given location
	 * or if the location can be used globally.
	 *
	 * @param 	integer  $id_employee  The employee ID.
	 * @param 	integer  $id_location  The location ID.
	 *
	 * @return 	boolean  True if the employee is the owner, otherwise false.
	 */
	public function isLocationOwner($id_employee, $id_location)
	{
		if ($id_location <= 0)
		{
			// return always true if the location is not set
			return true;
		}

		if (!isset($this->locations[$id_location]))
		{
			$this->locations[$id_location] = 0;

			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_employee'))
				->from($dbo->qn('#__vikappointments_employee_location'))
				->where($dbo->qn('id') . ' = ' . (int) $id_location);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				if (in_array($dbo->loadResult(), array(-1, $id_employee)))
				{
					$this->locations[$id_location] = 1;
				}
			}
		}

		return $this->locations[$id_location];
	}
}
