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
 * Interface used to define the structure of a class that should
 * be able to export records with an abstract format.
 *
 * @since 	1.6
 */
abstract class Exportable
{
	/**
	 * The configuration object.
	 *
	 * @var JRegistry
	 */
	protected $options;

	/**
	 * Class constructor.
	 *
	 * @param 	array 	$options 	An array of options.
	 */
	public function __construct(array $options = array())
	{
		$this->options = new JRegistry($options);
	}

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
	abstract public function download($name, array $records = array(), ImportObject $obj = null);
}
