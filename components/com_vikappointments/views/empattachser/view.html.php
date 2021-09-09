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
class VikAppointmentsViewempattachser extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return void
	 */
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		if (!$auth->create() && !$auth->attachServices())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=serviceslist', false));
			exit;
		}

		// get assigned services
		$attached = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('s.id'))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
			->where($dbo->qn('a.id_employee') . ' = ' . $auth->id)
			->order($dbo->qn('s.name') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$attached = $dbo->loadColumn();
		}

		// get all services
		$services = array();

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('s.id'),
				$dbo->qn('s.name'),
				$dbo->qn('s.id_group'),
				$dbo->qn('g.name', 'group_name'),
			))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('s.id_group') . ' = ' . $dbo->qn('g.id'))
			->where(array(
				$dbo->qn('s.published') . ' = 1',
				$dbo->qn('s.createdby') . ' <= 0',
			))
			->order(array(
				$dbo->qn('g.name') . ' ASC',
				$dbo->qn('s.name') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadObjectList() as $row)
			{
				if (!isset($services[$row->id_group]))
				{
					$services[$row->id_group] = array(
						'id'		=> $row->id_group,
						'name' 		=> $row->group_name,
						'services' 	=> array(),
					);
				}

				$services[$row->id_group]['services'][] = array(
					'id'	=> $row->id,
					'name' 	=> $row->name,
				);
			}
		}

		if (key($services) <= 0)
		{
			$shift = array_shift($services);
			$services[-1] = $shift;
		}
		
		$this->auth 	= &$auth;
		$this->services = &$services;
		$this->attached = &$attached;
		
		// Display the template
		parent::display($tpl);
	}
}
