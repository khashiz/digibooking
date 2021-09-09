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

/**
 * VikAppointments locations (countries, states and cities) class handler.
 *
 * @since 1.6
 */
abstract class VAPLocations
{
	/**
	 * Returns the list of supported countries.
	 *
	 * @param 	mixed 	$orderby  The ordering column or an array with column and direction.
	 *
	 * @return 	array 	The countries list.
	 */
	public static function getCountries($orderby = 'id')
	{
		if (is_scalar($orderby))
		{
			$orderby = array($orderby, 'ASC');
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_countries'))
			->where($dbo->qn('published') . ' = 1')
			->order($dbo->qn($orderby[0]) . ' ' . $orderby[1]);
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadAssocList();
		}
		
		return array();
	}
	
	/**
	 * Returns the published country that match the given 2 letters code.
	 *
	 * @param 	string  $country_2_code  The 2 letters code (ISO 3166).
	 *
	 * @return 	mixed 	The country details on success, false otherwise.
	 */
	public static function getCountryFromCode($country_2_code)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_countries'))
			->where($dbo->qn('published') . ' = 1')
			->where($dbo->qn('country_2_code') . ' = ' . $dbo->q($country_2_code));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadAssoc();
		}
		
		return false;
	}
	
	/**
	 * Returns the list of supported states that belong to the given country.
	 *
	 * @param 	integer  $id_country  The country ID.
	 * @param 	mixed 	 $orderby  	  The ordering column or an array with column and direction.
	 *
	 * @return 	array 	 The states list.
	 */
	public static function getStates($id_country, $orderby = 'id')
	{
		if (is_scalar($orderby))
		{
			$orderby = array($orderby, 'ASC');
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_states'))
			->where($dbo->qn('published') . ' = 1')
			->where($dbo->qn('id_country') . ' = ' . (int) $id_country)
			->order($dbo->qn($orderby[0]) . ' ' . $orderby[1]);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadAssocList();
		}
		
		return array();
	}
	
	/**
	 * Returns the list of supported cities that belong to the given state.
	 *
	 * @param 	integer  $id_state  The state ID.
	 * @param 	mixed 	 $orderby   The ordering column or an array with column and direction.
	 *
	 * @return 	array 	 The cities list.
	 */
	public static function getCities($id_state, $orderby = 'id')
	{
		if (is_scalar($orderby))
		{
			$orderby = array($orderby, 'ASC');
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_cities'))
			->where($dbo->qn('published') . ' = 1')
			->where($dbo->qn('id_state') . ' = ' . (int) $id_state)
			->order($dbo->qn($orderby[0]) . ' ' . $orderby[1]);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadAssocList();
		}
		
		return array();
	}
}

/**
 * VikAppointments locations (countries, states and cities) class handler.
 *
 * @since  		1.4
 * @deprecated 	1.7  Use VAPLocations instead.
 */
class VikAppointmentsLocations extends VAPLocations
{

}
