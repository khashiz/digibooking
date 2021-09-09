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
class VikAppointmentsViewempeditwdays extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_datepicker_regional();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		$type = 1;
		$cid  = $input->getUint('cid', array());
		
		$worktime = array();

		if (count($cid))
		{
			$type = 2;

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_emp_worktime'))
				->where(array(
					$dbo->qn('id') . ' = ' . $cid[0],
					$dbo->qn('id_service') . ' = -1',
					$dbo->qn('id_employee') . ' = ' . $auth->id,
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$worktime = $dbo->loadAssoc();
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPWDAYEDITAUTH0'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=empwdays', false));
				exit;
			}
		}
		// do not create if not authorised
		else if (!$auth->manageWorkDays())
		{
			$app->enqueueMessage(JText::_('VAPEMPWDAYEDITAUTH0'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=empwdays', false));
			exit;
		}

		if (!count($worktime))
		{
			$worktime = $this->getBlankItem();
		}

		// get locations
		$locations = array();

		$q = $dbo->getQuery(true)
			->select('`l`.*')
			->select($dbo->qn(array('c.country_name', 's.state_name', 'ci.city_name')))
			->select($dbo->qn(array('c.country_2_code', 's.state_2_code', 'ci.city_2_code')))
			->from($dbo->qn('#__vikappointments_employee_location', 'l'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.id') . ' = ' . $dbo->qn('l.id_country'))
			->leftjoin($dbo->qn('#__vikappointments_states', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('l.id_state'))
			->leftjoin($dbo->qn('#__vikappointments_cities', 'ci') . ' ON ' . $dbo->qn('ci.id') . ' = ' . $dbo->qn('l.id_city'))
			->where(array(
				$dbo->qn('l.id_employee') . ' = ' . $auth->id,
				$dbo->qn('l.id_employee') . ' = -1',
			), 'OR')
			// global locations come after
			->order($dbo->qn('l.id_employee') . ' DESC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadAssocList() as $l)
			{
				if (!isset($locations[$l['id_employee']]))
				{
					$locations[$l['id_employee']] = array();
				}

				$code = $l['city_2_code'];
				
				if (empty($code))
				{
					$code = $l['state_2_code'];

					if (empty($code))
					{
						$code = $l['country_2_code'];
					}
				}

				$l['label'] = "{$l['name']} ({$l['address']}, $code)";

				$locations[$l['id_employee']][] = $l;
			}
		}

		/**
		 * Get services to be assigned while creating a new working day.
		 *
		 * @since 1.6.2
		 */
		$services = array();

		if ($type == 1)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('s.id', 's.name')))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
				->where($dbo->qn('a.id_employee') . ' = ' . $auth->id)
				->order($dbo->qn('s.ordering') . ' ASC');

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$services = $dbo->loadObjectList();
			}
		}
		
		$this->auth      = &$auth;
		$this->worktime  = &$worktime;
		$this->locations = &$locations;
		$this->services  = &$services;
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Returns a blank item for the creation page.
	 *
	 * @return 	array 	The item.
	 */
	protected function getBlankItem()
	{
		return array(
			'id' 			=> -1,
			'fromts' 		=> 510,
			'endts' 		=> 960,
			'day' 			=> 1,
			'ts' 			=> -1,
			'closed' 		=> 0,
			'id_location'	=> -1,
		);
	}
}
