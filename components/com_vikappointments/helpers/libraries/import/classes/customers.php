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
 * Class used to handle an import event for the CUSTOMERS.
 *
 * @see 	ImportObject
 * @since 	1.6
 */
class ImportObjectCustomers extends ImportObject
{
	/**
	 * @override
	 * Builds the base query to export all the customers
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
		$filters['keys'] 	= $app->getUserStateFromRequest('vapcustomers.keys', 'keys', '', 'string');
		$filters['type'] 	= $app->getUserStateFromRequest('vapcustomers.type', 'utype', 0, 'int');
		$filters['country'] = $app->getUserStateFromRequest('vapcustomers.country', 'country', '', 'string');

		$q = parent::buildExportQuery($app, $dbo);

		// search filter
		if (strlen($filters['keys']))
		{
			$q->andWhere(array(
				$dbo->qn('billing_name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('billing_mail') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('billing_phone') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('company') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('vatnum') . ' = ' . $dbo->q("{$filters['keys']}"),
			));
		}

		// type filter
		if ($filters['type'] == 1)
		{
			// registered
			$q->where($dbo->qn('jid') . ' > 0');
		}
		else if ($filters['type'] == -1)
		{
			// guest
			$q->where($dbo->qn('jid') . ' <= 0');
		}

		// country filter
		if (strlen($filters['country']))
		{
			$q->where($dbo->qn('country_code') . ' = ' . $dbo->q($filters['country']));
		}

		return $q;
	}
}
