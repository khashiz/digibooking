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
 * Employee edit service working day controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpEditSerWdays extends UIControllerAdmin
{
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
		
		$cid 	= $input->getUint('cid', array());
		$id_ser = $input->getUint('id', 0);
		$aff 	= false;

		// if the working days is owned by the employee and it is related to the service, we can remove it from the DB
		$removed = $this->_delete($cid, '#__vikappointments_emp_worktime', array('id_employee' => $auth->id, 'id_service' => $id_ser));
		
		if ($removed)
		{
			$app->enqueueMessage(JText::_('VAPSERWDUPDATED1'));
		}

		$this->redirect('index.php?option=com_vikappointments&view=empeditserwdays&limitstart=0&id=' . $id_ser);
	}

	/**
	 * Restores the default working days.
	 *
	 * @return 	void
	 */
	public function restore()
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
		
		$id_ser = $input->getUint('id', 0);
		$aff 	= false;

		// get all working days
		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_emp_worktime'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $auth->id,
				$dbo->qn('id_service') . ' = -1',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$worktimes = $dbo->loadObjectlist();

			// count service working days
			$q = $dbo->getQuery(true)
				->select('COUNT(1)')
				->from($dbo->qn('#__vikappointments_emp_worktime'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $auth->id,
					$dbo->qn('id_service') . ' = ' . $id_ser,
				));

			$dbo->setQuery($q);
			$dbo->execute();

			// proceed only if the count of service working days doesn't match the default ones
			if ((int) $dbo->loadResult() != count($worktimes))
			{
				// remove all the service/employee working days
				$removed = $this->_delete('*', '#__vikappointments_emp_worktime', array('id_employee' => $auth->id, 'id_service' => $id_ser));

				// reset all the working days
				foreach ($worktimes as $wd)
				{
					// keep a relation with the parent
					$wd->parent 	= $wd->id;
					$wd->id_service = $id_ser;

					// unset ID to avoid duplicated primary key errors
					unset($wd->id);

					$dbo->insertObject('#__vikappointments_emp_worktime', $wd, 'id');

					$aff = $aff || $wd->id > 0;
				}
			}
		}
		
		if ($aff)
		{
			$app->enqueueMessage(JText::_('VAPSERWDRESTORED1'));
		}

		$this->redirect('index.php?option=com_vikappointments&view=empeditserwdays&limitstart=0&id=' . $id_ser);
	}
}
