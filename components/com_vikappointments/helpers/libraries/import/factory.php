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
 * Factory class to access the handler object to import items.
 *
 * @since 	1.6
 */
final class ImportFactory
{
	/**
	 * A list containing all the classes already instantiated.
	 *
	 * @var array
	 */
	private static $instances = array();

	/**
	 * A list containing all the exportable classes already instantiated.
	 *
	 * @var array
	 */
	private static $exportable = array();

	/**
	 * Gets the import handler object, only creating it
	 * if it doesn't exist yet.
	 *
	 * @param 	string 	$type 	The entity type to import.
	 *
	 * @return 	mixed 	ImportObject on success, otherwise null.
	 */
	public static function getObject($type)
	{
		if (!isset(static::$instances[$type]))
		{
			static::$instances[$type] = null;

			if (static::isSupported($type))
			{
				UILoader::import('libraries.import.object');

				$classname = 'ImportObject';

				if (UILoader::import('libraries.import.classes.' . $type))
				{
					$classname .= ucwords($type);
				}

				$xml = static::loadXml($type);

				static::$instances[$type] = new $classname($xml, $type);
			}

		}

		return static::$instances[$type];
	}

	/**
	 * Checks if the specified entity type is supported.
	 *
	 * @param 	string 	 $type 	The entity type to import.
	 *
	 * @return 	boolean  True if supported, otherwise false.
	 */
	public static function isSupported($type)
	{
		return file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . $type . '.xml');
	}

	/**
	 * Loads the XML containing the instructions for the import.
	 *
	 * @param 	string 	$type 	The entity type to import.
	 *
	 * @return 	SimpleXMLElement
	 */
	protected static function loadXml($type)
	{
		return simplexml_load_file(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . $type . '.xml');
	}

	/**
	 * Gets the export handler object, only creating it
	 * if it doesn't exist yet.
	 *
	 * @param 	string 	$type 	The export handler.
	 * @param 	array 	$args 	An array of options.
	 *
	 * @return 	mixed 	Exportable on success, otherwise null.
	 */
	public static function getExportable($type, array $args = array())
	{
		if (!isset(static::$exportable[$type]))
		{
			static::$exportable[$type] = null;

			UILoader::import('libraries.import.exportable');

			$classname = 'Exportable';

			if (UILoader::import('libraries.import.export.' . $type))
			{
				$classname .= ucwords($type);

				if (class_exists($classname))
				{
					static::$exportable[$type] = new $classname($args);
				}
			}
		}

		return static::$exportable[$type];
	}

	/**
	 * Returns the list of all the supported exportable handlers.
	 *
	 * @param 	string 	$query 	The query to use.
	 * 							The character '*' obtains all the files.
	 *
	 * @return 	array 	The exportable list.
	 */
	public static function getExportList($query = '*')
	{
		if (!$query)
		{
			$query = '*';
		}

		$pool = array();

		foreach (glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $query . '.php') as $file)
		{
			$file = basename($file);
			$file = substr($file, 0, strrpos($file, '.'));

			$exportable = static::getExportable($file);

			if ($exportable)
			{
				$pool[$file] = $exportable;
			}
		}

		return $pool;
	}

	/**
	 * Returns the JForm object used to display the additional
	 * parameters of the given export type.
	 *
	 * @param 	string 	$type 	The export type.
	 * @param 	array 	$args 	The data to bind.
	 *
	 * @return 	mixed 	The form object on success, otherwise false.
	 */
	public static function getExportableForm($type, array $args = array())
	{
		$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $type . '.xml';

		// check if the XML fieldset exists
		if (!is_file($path))
		{
			return false;
		}

		// try to load the form
		$form = JForm::getInstance('exportable' . $type, $path);

		if (!$form)
		{
			return false;
		}

		// inject the form values
		$form->bind($args);

		return $form;
	}
}
