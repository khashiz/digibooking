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

UILoader::import('libraries.import.exportable');

/**
 * This class implements the abstract methods defined by the
 * exportable interface in order to export the records in SQL format.
 *
 * @since 	1.6
 */
class ExportableSql extends Exportable
{
	/**
	 * Creates the file with the given records and downloads it
	 * using the specified filename.
	 *
	 * @param 	string 		  $name 	The file name.
	 * @param 	array 		  $records 	The records to export.
	 * @param 	ImportObject  $obj 		The object handler.
	 *
	 * @return 	void
	 */
	public function download($name, array $records = array(), ImportObject $obj = null)
	{
		$dbo = JFactory::getDbo();
		
		// get the maximum number of records to insert within the same query
		$maxquery = $this->options->get('maxquery', 1000);
		// get the database prefix to use (if not provided, the current one will be used)
		$dbprefix = $this->options->get('dbprefix', $dbo->getPrefix());

		$q = $dbo->getQuery(true);

		// replace placeholder with the real database prefix
		$db_table = $obj->getTable();
		$db_table = preg_replace("/^#__/", $dbprefix, $db_table);

		// prepare INSERT query
		$q->insert($dbo->qn($db_table));

		// iterate the columns
		foreach ($obj->getColumns() as $key => $column)
		{
			$q->columns($dbo->qn($key));
		}

		// make sure the maxquery is positive to avoid loops
		if ($maxquery <= 0)
		{
			$maxquery = 1000;
		}

		$app = array();

		// splice the records until we have an empty array
		while (count($records) > $maxquery)
		{
			// push the sub-array within the temporary list
			$app[] = array_splice($records, 0, $maxquery); 
		}

		// if there is still something to export, push it at the end of the list
		if (count($records))
		{
			$app[] = $records;
		}

		// start headers to download SQL file
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-Disposition: attachment;filename=\"{$name}.sql\"");

		// open output files
		$handle = fopen('php://output', 'w');

		// iterate the sub-arrays
		foreach ($app as $subarray)
		{
			// clear previous values
			$q->clear('values');

			foreach ($subarray as $row)
			{
				$values = array();

				foreach ($row as $k => $v)
				{
					if (is_null($v))
					{
						// use NULL operator
						$values[] = 'NULL';
					}
					else
					{
						// escape the value
						$values[] = $dbo->q($v);
					}
				}

				$q->values(implode(",", $values));
			}

			// add a new line after each record
			$buffer = ltrim(preg_replace("/\),\(/", "),\n(", (string) $q)) . ";\n";

			fwrite($handle, $buffer);
		}
		
		fclose($handle);
		exit;
	}
}
