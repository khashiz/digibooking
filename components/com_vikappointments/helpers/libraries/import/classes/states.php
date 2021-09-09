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
 * Class used to handle an import event for the STATES.
 *
 * @see 	ImportObject
 * @since 	1.6
 */
class ImportObjectStates extends ImportObject
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
		// make sure the country ID is set in the arguments
		if (!isset($args['country']))
		{
			return false;
		}

		$data->id_country = $args['country'];

		$dbo = JFactory::getDbo();

		// check if already exists a state with the given codes
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('state_2_code', 'state_3_code')))
			->from($dbo->qn('#__vikappointments_states'))
			->where($dbo->qn('id_country') . ' = ' . $data->id_country)
			->andWhere(array(
				$dbo->qn('state_2_code') . ' = ' . $dbo->q($data->state_2_code),
				$dbo->qn('state_3_code') . ' = ' . $dbo->q($data->state_3_code),
			), 'OR');
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if ($dbo->getNumRows() > 0)
		{
			$assoc = $dbo->loadAssoc();

			// since the 3 letters code is optional, raise an error only if it is not empty
			if ($assoc['state_2_code'] == $data->state_2_code
				|| ($assoc['state_3_code'] == $data->state_3_code && !empty($data->state_3_code)))
			{
				$this->setError($data, JText::_('VAPSTATEUNIQUEERROR'));

				return false;
			}
		}

		// call parent method to check the data integrity
		return parent::bind($data);
	}

	/**
	 * @override
	 * Builds the base query to export all the states
	 * using the filters set in the main list.
	 *
	 * @param 	mixed 	$app  The application instance.
	 * @param 	mixed 	$dbo  The database global object.
	 *
	 * @return 	mixed 	The query builder object.
	 */
	protected function buildExportQuery($app, $dbo)
	{
		// recover country ID from the request
		$country = $app->input->getUint('country', 0);

		$filters = array();
		$filters['keys'] 	= $app->getUserStateFromRequest('vapstates['.$country.'].keys', 'keys', '', 'string');
		$filters['status'] 	= $app->getUserStateFromRequest('vapstates['.$country.'].status', 'status', -1, 'int');

		$q = parent::buildExportQuery($app, $dbo);

		$q->where($dbo->qn('id_country') . ' = ' . $country);

		// search filter
		if (strlen($filters['keys']))
		{
			$q->where($dbo->qn('state_name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
		}

		// status filter
		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('published') . ' = ' . $filters['status']);
		}

		return $q;
	}
}
