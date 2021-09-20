<?php
/** 
 * @package   	VikAppointments
 * @subpackage 	com_vikappointments
 * @author    	Matteo Galletti - e4j
 * @copyright 	Copyright (C) 2018 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license  	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link 		https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class used by the Employees Filter module.
 *
 * @since 1.2
 */
class VikAppointmentsEmployeesFilterHelper
{  
	/**
	 * Get filters posted in the request.
	 *
	 * @return 	array 	An associative array containing the specified filters.
	 */
	public static function getViewHtmlReferences()
	{
		$input = JFactory::getApplication()->input;

		$filters = $input->get('filters', array(), 'array');
		
		$filters_lookup = array(
			'group',
			'service',
			'price',
			'country',
			'state',
			'city',
			'zip',
			'distance',
			'base_coord',
		);

		foreach ($filters_lookup as $f)
		{
			if (!array_key_exists($f, $filters))
			{
				$filters[$f] = '';
			}
		}
		
		if (!empty($filters['price']))
		{
			$filters['price'] = explode(':', $filters['price']);
		}
		
		return array('filters' => $filters);
	}
	
	/**
	 * Returns an array containing the configuration filters.
	 *
	 * @param 	JRegistry 	$params  The configuration registry.
	 *
	 * @return 	array 	The configuration array.
	 */
	public static function getFilters($params)
	{
		$f = array(
			"filters" => array(
				"group" 	=> intval($params->get('filters_group')),
				"service" 	=> intval($params->get('filters_service')),
				"nearby" 	=> intval($params->get('filters_nearby')),
				"price" 	=> intval($params->get('filters_price')),
				"country" 	=> intval($params->get('filters_country')),
				"state" 	=> intval($params->get('filters_state')),
				"city" 		=> intval($params->get('filters_city')),
				"zip" 		=> intval($params->get('filters_zip')),
				"custom"	=> $params->get('filters_custom', array()),
			),

			"nearby_params" => array(
				"distances" => array(),
				"distunit"	=> $params->get('nearby_distunit'),
			),

			"price_range" => array(
				"min" => intval($params->get('price_range_min')),
				"max" => intval($params->get('price_range_max')),
				"def" => array(),
			),

			"defaults" => array(
				"group" 	=> intval($params->get('default_group')),
				"service" 	=> intval($params->get('default_service')),
				"country" 	=> intval($params->get('default_country')),
			),
		);
		
		$def_range = explode(',', $params->get('price_range_def'));
		if (count($def_range) == 2)
		{
			foreach ($def_range as $v)
			{
				$f['price_range']['def'][] = intval(trim($v));
			}
		}
		else
		{
			$f['price_range']['def'] = array($f['price_range']['min'], $f['price_range']['max']);
		}

		$distances = explode(',', $params->get('nearby_distances'));

		foreach ($distances as $d)
		{
			$f['nearby_params']['distances'][] = intval($d);
		}

		/**
		 * Obtain custom fields.
		 *
		 * @since 1.2
		 */
		if ($f['filters']['custom'])
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_custfields'))
				->where(array(
					$dbo->qn('group') . ' = 1',
					$dbo->qn('id') . ' IN (' . implode(',', $f['filters']['custom']) . ')',
				));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$f['filters']['custom'] = $dbo->loadAssocList();
				VAPCustomFields::translate($f['filters']['custom']);
			}
			else
			{
				$f['filters']['custom'] = array();
			}
		}
		
		return $f;
	}
	
	/**
	 * Returns the list of all the published services groups.
	 *
	 * @return 	array 	The groups list.
	 */
	public static function getServicesGroups()
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_group'))
			->order($dbo->qn('ordering') . ' ASC');
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();

			$langtag 		= JFactory::getLanguage()->getTag();
			$lang_groups 	= VikAppointments::getTranslatedGroups('', $langtag, $dbo);

			foreach ($groups as $i => $g)
			{
				$groups[$i]['name'] = VikAppointments::getTranslation($g['id'], $g, $lang_groups, 'name', 'name');
			}

			return $groups;
		}
		
		return array();
	}
	
	/**
	 * Returns the list of services that belong to the specified group.
	 *
	 * @param 	integer  $id_group 	The group ID.
	 *
	 * @return 	arrary 	 The services list.
	 */
	public static function getServicesList($id_group = 0)
	{
		$dbo  = JFactory::getDbo();
		$user = JFactory::getUser();
		
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('published') . ' = 1')
			->order($dbo->qn('ordering') . ' ASC');

		if ($id_group > 0)
		{
			$q->where($dbo->qn('id_group') . ' = ' . (int) $id_group);
		}

		/**
		 * Retrieve only the services the belong to the view
		 * access level of the current user.
		 *
		 * @since 1.2
		 */
		$levels = $user->getAuthorisedViewLevels();

		if ($levels)
		{
			$q->where($dbo->qn('level') . ' IN (' . implode(', ', $levels) . ')');
		}
		
		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows()) 
		{
			$services = $dbo->loadAssocList();

			$langtag 		= JFactory::getLanguage()->getTag();
			$lang_services 	= VikAppointments::getTranslatedServices('', $langtag, $dbo);

			foreach ($services as $i => $s)
			{
				$services[$i]['name'] = VikAppointments::getTranslation($s['id'], $s, $lang_services, 'name', 'name');
			}

			return $services;
		}
		
		return array();
	}

	/**
	 * Helper method used to format currency amounts with no decimals.
	 *
	 * @param 	float 	$price 	The amount to format.
	 *
	 * @return 	string 	The formatted amount.
	 */
	public static function printPrice($price)
	{
		$config = UIFactory::getConfig();

		$curr_symb 	= $config->get('currencysymb');
		$pos 		= $config->get('currsymbpos');
		
		$price = floatval($price);
		
		// AFTER PRICE
		if ($pos == 1)
		{
			return number_format($price, 0) . ' ' . $curr_symb;
		}

		// BEFORE PRICE
		return $curr_symb . ' ' . number_format($price, 0);
	}
}

/**
 * Helper class used by the Employees Filter module.
 *
 * @since  		1.0
 * @deprecated 	1.7  Use VikAppointmentsEmployeesFilterHelper instead.
 */
class modVikAppointments_employees_filterHelper extends VikAppointmentsEmployeesFilterHelper
{

}
