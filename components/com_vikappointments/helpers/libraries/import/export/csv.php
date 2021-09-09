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
 * exportable interface in order to export the records in CSV format.
 *
 * @since 	1.6
 */
class ExportableCsv extends Exportable
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
		$head = array();

		// iterate the columns to build the header
		foreach ($obj->getColumns() as $col)
		{
			$head[] = $col->label;
		}

		// prepend the CSV heading at the beginning of the records list
		array_unshift($records, $head);

		$delimiter = $this->options->get('delimiter', ',');
		$enclosure = $this->options->get('enclosure', '"');

		//header("Content-Type: application/octet-stream; ");
		//header("Cache-Control: no-store, no-cache");
		header("Content-Type: text/csv");
		//header('Content-Encoding: UTF-8');
		//header('Content-type: text/csv; charset=UTF-8');
		header("Content-Disposition: attachment;filename=\"{$name}\"");
		//echo "\xEF\xBB\xBF"; // UTF-8 BOM for correct encoding on excel
		
		$handle = fopen('php://output', 'w');

		foreach ($records as $row)
		{
			fputcsv($handle, $row, $delimiter, $enclosure);
		}

		fclose($handle);
		exit;
	}
}
