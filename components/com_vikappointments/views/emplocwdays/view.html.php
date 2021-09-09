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
class VikAppointmentsViewemplocwdays extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}

		$lim  = 10;
		$lim0 = $app->getUserStateFromRequest('emplocwdays.limitstart', 'limitstart', 0, 'uint');
		
		$rows = array();

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_emp_worktime'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $auth->id,
				$dbo->qn('id_service') . ' = -1',
				$dbo->qn('closed') . ' = 0',
			))
			->order(array(
				$dbo->qn('day') . ' ASC',
				$dbo->qn('ts') . ' ASC',
				$dbo->qn('fromts') . ' ASC',
				$dbo->qn('closed') . ' ASC',
			));

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = $pageNav->getPagesLinks();
		}
		
		$worktime_days = array();
		$worktime_date = array();

		foreach ($rows as $w)
		{
			if ($w['ts'] <= 0)
			{
				$worktime_days[] = $w;
			}
			else
			{
				$worktime_date[] = $w;
			}
		}

		$rows = array_merge($worktime_days, $worktime_date);

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

				$code = $l['city_name'];
				
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
		
		$this->auth 		= &$auth;
		$this->worktimes 	= &$rows;
		$this->locations 	= &$locations;
		$this->navbut 		= &$navbut;
		
		// Display the template
		parent::display($tpl);
	}
}
