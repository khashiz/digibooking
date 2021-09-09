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
 * Class used to handle an import event for the COUPONS.
 *
 * @see 	ImportObject
 * @since 	1.6
 */
class ImportObjectCoupons extends ImportObject
{
	/**
	 * @override
	 * Overloaded bind function.
	 *
	 * @param 	object 	 &$data  The object of the record to import.
	 * @param 	array 	 $args 	 Associative list of additional parameters.
	 *
	 * @return 	boolean  True if the record should be imported, otherwise false.
	 */
	protected function bind(&$data, array $args = array())
	{
		// make sure the type is an accepted value
		if (!in_array($data->type, array(1, 2)))
		{
			// set the default PERMANENT type
			$data->type = 1;
		}

		// make sure the percentot is an accepted value
		if (!in_array($data->percentot, array(1, 2)))
		{
			// set the default TOTAL type
			$data->percentot = 2;
		}

		// only ABS numbers
		$data->value 	= abs($data->value);
		$data->mincost 	= abs($data->mincost);

		// only if the date start is not empty and it is a string
		// try to get the correct timestamp
		if (!empty($data->dstart) && !is_numeric($data->dstart))
		{
			$data->dstart = VikAppointments::createTimestamp($data->dstart, 0, 0);
		}

		// if the date start is still empty, set it to the default -1 value
		if (empty($data->dstart))
		{
			$data->dstart = -1;
		}

		// only if the date end is not empty and it is a string
		// try to get the correct timestamp
		if (!empty($data->dend) && !is_numeric($data->dend))
		{
			$data->dend = VikAppointments::createTimestamp($data->dend, 23, 59);
		}

		// if the date end is still empty, set it to the default -1 value
		if (empty($data->dend))
		{
			$data->dend = -1;
		}

		// make sure the start date is not higher than the end date
		if ($data->dstart > $data->dend)
		{
			$app 			= $data->dstart;
			$data->dstart 	= $data->dend;
			$data->dend 	= $app;
		}

		// call parent method to check the data integrity
		return parent::bind($data, $args);
	}

	/**
	 * @override
	 * Builds the base query to export all the coupons
	 * using the filters set in the main list.
	 *
	 * @param 	mixed 	$app  The application instance.
	 * @param 	mixed 	$dbo  The database global object.
	 *
	 * @return 	mixed 	The query builder object.
	 */
	protected function buildExportQuery($app, $dbo)
	{
		$filters = array();
		$filters['keys'] 	 = $app->getUserStateFromRequest('vapcoupons.keys', 'keys', '', 'string');
		$filters['type'] 	 = $app->getUserStateFromRequest('vapcoupons.type', 'type', 0, 'uint');
		$filters['value'] 	 = $app->getUserStateFromRequest('vapcoupons.value', 'value', 0, 'uint');
		$filters['status'] 	 = $app->getUserStateFromRequest('vapcoupons.status', 'status', 0, 'uint');
		$filters['id_group'] = $app->getUserStateFromRequest('vapcoupons.group', 'id_group', -1, 'int');

		$q = parent::buildExportQuery($app, $dbo);

		// search filter
		if (strlen($filters['keys']))
		{
			$q->where($dbo->qn('code') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
		}

		// type filter (GIFT or PERMANENT)
		if ($filters['type'])
		{
			$q->where($dbo->qn('type') . ' = ' . $filters['type']);
		}

		// value filter (PERCENT or TOTAL)
		if ($filters['value'])
		{
			$q->where($dbo->qn('percentot') . ' = ' . $filters['value']);
		}

		// status filter (ACTIVE, NOT ACTIVE or EXPIRED)
		if ($filters['status'] == 1)
		{
			$q->where(array(
				$dbo->qn('dend') . ' > 0',
				$dbo->qn('dend') . ' < ' . time(),
			));
		}
		else if ($filters['status'] == 2)
		{
			$q->andWhere(array(
				$dbo->qn('dend') . ' <= 0',
				time() . ' BETWEEN ' . $dbo->qn('dstart') . ' AND ' . $dbo->qn('dend'),
			), 'OR');
		}
		else if ($filters['status'] == 3)
		{
			$q->where(array(
				$dbo->qn('dstart') . ' > 0',
				$dbo->qn('dstart') . ' > ' . time(),
			));
		}

		// group filter
		if ($filters['id_group'] != -1)
		{
			$q->where($dbo->qn('id_group') . ' = ' . $filters['id_group']);
		}

		return $q;
	}
}
