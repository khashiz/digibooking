<?php
/** 
 * @package     VikAppointments
 * @subpackage  mod_vikappointments_search
 * @author      Matteo Galletti - e4j
 * @copyright   Copyright (C) 2018 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class used by the Search module.
 *
 * @since 1.3
 */
class VikAppointmentsSearchHelper
{
	/**
	 * Returns an associative array containing the data set
	 * in the request.
	 *
	 * @return 	array 	The data array.
	 */
	public static function getViewHtmlReferences()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$user 	= JFactory::getUser();
		
		$last_values = array(
			'day' 		=> $input->getUint('day', time()),
			'id_ser' 	=> $input->getUint('id_ser', 0),
			'id_emp' 	=> $input->getUint('id_emp', 0),
		);

		// get services
		
		$services 	= array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('s.id', 's.name', 's.choose_emp', 's.id_group')))
			->select($dbo->qn('g.id', 'gid'))
			->select($dbo->qn('g.name', 'gname'))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('s.id_group') . ' = ' . $dbo->qn('s.id'))
			->where($dbo->qn('s.published') . ' = 1')
			->order(array(
				$dbo->qn('g.ordering') . ' ASC',
				$dbo->qn('s.ordering') . ' ASC',
				$dbo->qn('s.name') . ' ASC',
			));

		// retrieve only the services the belong to the view
		// access level of the current user
		$levels = $user->getAuthorisedViewLevels();

		if ($levels)
		{
			$q->where($dbo->qn('s.level') . ' IN (' . implode(', ', $levels) . ')');
		}
			
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$tmp = $dbo->loadAssocList();
			
			$cont = 0;
			while ($tmp[0]['id_group'] == -1 && $cont < count($tmp))
			{
				$tmp[] = array_shift($tmp);
                $cont++;
			}

			$langtag 		= JFactory::getLanguage()->getTag();
			$lang_groups 	= VikAppointments::getTranslatedGroups('', $langtag, $dbo);
			$lang_services 	= VikAppointments::getTranslatedServices('', $langtag, $dbo);

			foreach ($tmp as $r)
			{
				if (!isset($r['id_group']))
				{
					$r['gname'] = VikAppointments::getTranslation($r['gid'], $r, $lang_groups, 'gname', 'name');

					$services[$r['id_group']] = array(
						'id' 	=> $r['gid'],
						'name'	=> $r['gname'],
						'list'	=> array(),
					);
				}

				$r['name'] = VikAppointments::getTranslation($r['id'], $r, $lang_services, 'name', 'name');

				$services[$r['id_group']]['list'][] = array(
					'id' 		 => $r['id'],
					'name' 		 => $r['name'],
					'choose_emp' => $r['choose_emp'],
					'id_group'	 => $r['id_group'],
				);
			}
		}

		$group = reset($services);
		
		if (!$last_values['id_ser'] && count($services) && count($group['list']))
		{
			$last_values['id_ser'] = $group['list'][0]['id'];
		}

		// get employees

		$employees 	= array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('e.id'))
			->select($dbo->qn('e.nickname', 'name'))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('a.id_employee'))
			->where($dbo->qn('a.id_service') . ' = ' . (int) $last_values['id_ser'])
			->order(array(
				$dbo->qn('e.nickname') . ' ASC',
			));
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$tmp = $dbo->loadAssocList();

			$lang_employees	= VikAppointments::getTranslatedEmployees('', $langtag, $dbo);

			foreach ($tmp as $e)
			{
				$e['name'] = VikAppointments::getTranslation($e['id'], $e, $lang_employees, 'name', 'nickname');

				$employees[] = $e;
			}
		}
		
		return array(
			'lastValues' 	=> $last_values,
			'services' 		=> $services,
			'employees' 	=> $employees,
		);
	}
	
	/**
	 * Returns the maximum timestamp that can be used within the
	 * jQuery Datepicker.
	 *
	 * @return 	integer  The maximum timestamp.
	 */
	public static function getMaxTimeStamp()
	{
		$num_months = VikAppointments::getNumberOfCalendars();

		$arr = getdate();
		
		for ($i = 0; $i < $num_months; $i++)
		{
			$arr = getdate(mktime(0, 0, 0, $arr['mon'] + 1, 1, $arr['year']));
		}

		return mktime(0, 0, 0, $arr['mon'], $arr['mday'] - 1, $arr['year']);
	}
}

/**
 * Helper class used by the Search module.
 *
 * @since  		1.0
 * @deprecated 	1.7  Use VikAppointmentsSearchHelper instead.
 */
class modVikAppointments_searchHelper extends VikAppointmentsSearchHelper
{

}
