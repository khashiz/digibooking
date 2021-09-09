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
		AppointmentsHelper::load_css_js();
		VikAppointments::load_complex_select();

		$app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();
		
		$id_emp = $input->getUint('id_emp');

		// get employee working days
		
		$rows = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_emp_worktime'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $id_emp,
				$dbo->qn('id_service') . ' = -1',
				$dbo->qn('closed') . ' = 0',
			))
			->andWhere(array(
				$dbo->qn('ts') . ' = -1',
				$dbo->qn('ts') . ' > ' . time(),
			), 'OR')
			->order(array(
				$dbo->qn('day') . ' ASC',
				$dbo->qn('ts') . ' ASC',
				$dbo->qn('fromts') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
		}
		
		$worktimes = array();
		$workdays = array();

		foreach ($rows as $w)
		{
			if ($w['ts'] == -1)
			{
				$worktimes[] = $w;
			}
			else
			{
				$workdays[] = $w;
			}
		}

		// get employee/global locations

		$locations = array();

		$q = $dbo->getQuery(true);

		$q->select('`l`.*')
			->from($dbo->qn('#__vikappointments_employee_location', 'l'));
			
		$q->select($dbo->qn(array('c.country_name', 'c.country_2_code')))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.id') . ' = ' . $dbo->qn('l.id_country'));

		$q->select($dbo->qn(array('s.state_name', 'state_2_code')))
			->leftjoin($dbo->qn('#__vikappointments_states', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('l.id_state'));

		$q->select($dbo->qn(array('ci.city_name', 'ci.city_2_code')))
			->leftjoin($dbo->qn('#__vikappointments_cities', 'ci') . ' ON ' . $dbo->qn('ci.id') . ' = ' . $dbo->qn('l.id_city'));

		$q->where(array(
				$dbo->qn('l.id_employee') . ' = ' . $id_emp,
				$dbo->qn('l.id_employee') . ' = -1',
			), 'OR')
			->order($dbo->qn('l.id_employee') . ' DESC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$locations = $dbo->loadAssocList();
			foreach ($locations as $k => $l)
			{
				$code = $l['city_name'];
				if (empty($code))
				{
					$code = $l['state_2_code'];
					if (empty($code))
					{
						$code = $l['country_2_code'];
					}
				}
				
				$locations[$k]['label'] = $l['name'] . " ({$l['address']}, $code)";
			}
		}
		
		$this->idEmployee = &$id_emp;
		$this->worktimes  = &$worktimes;
		$this->workdays   = &$workdays;
		$this->locations  = &$locations;

		// Display the template
		parent::display($tpl);
	}
}
