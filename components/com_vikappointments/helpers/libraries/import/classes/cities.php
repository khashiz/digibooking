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

UILoader::import('libraries.import.object');

/**
 * Class used to handle an import event for the CITIES.
 *
 * @see 	ImportObject
 * @since 	1.6
 */
class ImportObjectCities extends ImportObject
{
	/**
	 * Overloaded bind function.
	 *
	 * @param 	object 	 &$data  The object of the record to import.
	 * @param 	array 	 $args 	 Associative list of additional parameters.
	 *
	 * @return 	boolean  True if the record should be imported, otherwise false.
	 */
	protected function bind(&$data, array $args = array())
	{
		// make sure the state ID is set in the arguments
		if (!isset($args['state']))
		{
			return false;
		}

		$data->id_state = $args['state'];

		$dbo = JFactory::getDbo();

		// check if already exists a city with the given codes
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('city_2_code', 'city_3_code')))
			->from($dbo->qn('#__vikappointments_cities'))
			->where($dbo->qn('id_state') . ' = ' . $data->id_state)
			->andWhere(array(
				$dbo->qn('city_2_code') . ' = ' . $dbo->q($data->city_2_code),
				$dbo->qn('city_3_code') . ' = ' . $dbo->q($data->city_3_code),
			), 'OR');
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if ($dbo->getNumRows() > 0)
		{
			$assoc = $dbo->loadAssoc();

			// since the 3 letters code is optional, raise an error only if it is not empty
			if ($assoc['city_2_code'] == $data->city_2_code
				|| ($assoc['city_3_code'] == $data->city_3_code && !empty($data->city_3_code)))
			{
				$this->setError($data, JText::_('VAPCITYUNIQUEERROR'));

				return false;
			}
		}

		// call parent method to check the data integrity
		return parent::bind($data);
	}

	/**
	 * @override
	 * Builds the base query to export all the cities
	 * using the filters set in the main list.
	 *
	 * @param 	mixed 	$app  The application instance.
	 * @param 	mixed 	$dbo  The database global object.
	 *
	 * @return 	mixed 	The query builder object.
	 */
	protected function buildExportQuery($app, $dbo)
	{
		// recover state ID from the request
		$state = $app->input->getUint('state', 0);

		$filters = array();
		$filters['keys'] 	= $app->getUserStateFromRequest('vapcities['.$state.'].keys', 'keys', '', 'string');
		$filters['status'] 	= $app->getUserStateFromRequest('vapcities['.$state.'].status', 'status', -1, 'int');

		$q = parent::buildExportQuery($app, $dbo);

		$q->where($dbo->qn('id_state') . ' = ' . $state);

		// search filter
		if (strlen($filters['keys']))
		{
			$q->where($dbo->qn('city_name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
		}

		// status filter
		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('published') . ' = ' . $filters['status']);
		}

		return $q;
	}
}
