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
 * VikAppointments loader class.
 *
 * @since  1.6
 */
abstract class UILoader
{
	/**
	 * The list containing all the resources loaded.
	 *
	 * @var array
	 */
	private static $includes = array();

	/**
	 * The list containing all the filename aliases.
	 *
	 * @var array
	 */
	private static $aliases = array();

	/**
	 * Base path to load resources.
	 *
	 * @var string
	 */
	public static $base = VAPBASE;

	/**
	 * Loads the specified file.
	 *
	 * @param   string  $key   The class name to look for (dot notation).
	 * @param   string  $base  Search this directory for the class.
	 *
	 * @return  boolean  True on success, otherwise false.
	 */
	public static function import($key, $base = null)
	{
		// if the resource is not loaded, try to do it
		if (!isset(static::$includes[$key]))
		{
			$success = false;

			// if no base provided, use the default one
			if (empty($base))
			{
				$base = static::$base;
			}

			// remove trailing slash (if any)
			$base = rtrim($base, DIRECTORY_SEPARATOR);

			$parts = explode('.', $key);
			$class = array_pop($parts);

			// if the file has been registered with an alias, replace it with the original one
			if (isset(static::$aliases[$class]))
			{
				$class = static::$aliases[$class];
			}

			// re-insert class to build the relative path
			$parts[] = $class;

			// build the path
			$path = implode(DIRECTORY_SEPARATOR, $parts);

			// if the file exists, load it
			if (is_file($base . DIRECTORY_SEPARATOR . $path . '.php'))
			{
				$success = (bool) include $base . DIRECTORY_SEPARATOR . $path . '.php';
			}

			// cache the loading status
			static::$includes[$key] = $success;
		}

		return static::$includes[$key];
	}

	/**
	 * Register an alias of a given class filename.
	 * This is useful for those files that contain a dot in their name.
	 *
	 * @param 	string 	$name 	The filename to register.
	 * @param 	string 	$alias 	The alias to use.
	 */
	public static function registerAlias($name, $alias)
	{	
		if (!isset(static::$aliases[$alias]))
		{
			static::$aliases[$alias] = $name;
		}
	}
}
