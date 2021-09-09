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
 * Employee attached services controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpAttachSer extends UIControllerAdmin
{
	/**
	 * Save employee-services assignments.
	 *
	 * @return 	void
	 */
	public function save()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();
		
		// authorise
		if (!$auth->create() && !$auth->attachServices())
		{
			$app->enqueueMessage(JText::_('VAPEMPSERNEWAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empserviceslist');
			exit;
		}

		$services = $input->getUint('services', array());

		// get service controller
		UILoader::import('controllers.empeditservice', VAPBASE);
		$controller = new VikAppointmentsControllerEmpEditService();

		if (count($services))
		{
			// get existing services, if any
			$q = $dbo->getQuery(true)
				->select($dbo->qn('s.id'))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
				->where(array(
					$dbo->qn('a.id_employee') . ' = ' . $auth->id,
					$dbo->qn('a.id_service') . ' IN (' . implode(',', $services) . ')',
				))
				->orWhere(array(
					// return the service also if it has been created by a different employee
					$dbo->qn('s.createdby') . ' > 0',
					$dbo->qn('s.createdby') . ' <> ' . $auth->jid,
				), 'and');

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				// exclude the services that already exists
				$services = array_diff($services, $dbo->loadColumn());
			}

			$ok = false;

			foreach ($services as $id)
			{
				$q = $dbo->getQuery(true)
					->select(array(
						$dbo->qn('id', 'id_service'),
						$dbo->qn('price', 'rate'),
						$dbo->qn('duration'),
						$dbo->qn('sleep'),
					))
					->from($dbo->qn('#__vikappointments_service'))
					->where($dbo->qn('id') . ' = ' . $id);

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$assoc = $dbo->loadObject();
					$assoc->id_employee = $auth->id;

					$dbo->insertObject('#__vikappointments_ser_emp_assoc', $assoc, 'id');

					if ($assoc->id)
					{
						$ok = true;

						// attach service working days
						$controller->attachWorkingDays($id, $auth->id);
					}
				}
			}

			if ($ok)
			{
				$app->enqueueMessage(JText::_('VAPEMPATTACHSERCREATED'));
			}
		}
		
		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=empserviceslist';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empattachser';
		}

		$this->redirect($url);
	}
}
