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
 * Class used to dispatch a cron job.
 *
 * @since 	 1.5
 * @see 	 CronJob
 */
class CronDispatcher
{
	/**
	 * The base path can be accessed from external classes.
	 *
	 * @var string
	 */
	public static $BASE_PATH = DIRECTORY_SEPARATOR;

	/**
	 * Class constructor.
	 */
	private function __construct()
	{
		// construct is not accessible
	}

	/**
	 * Instantiates the cron job class based on the specified action.
	 *
	 * @param 	string 	 $action 	The name of the file (case insensitive).
	 * @param 	integer  $id 		The ID of the cron job.
	 * @param 	mixed 	 $args 		The parameters of the cron job.
	 *
	 * @return 	mixed 	 The CronJob object or null.
	 *
	 * @uses 	includeJob()
	 */

	public static function getJob($action, $id, $args = array())
	{
		// include the cronjob and get its classname
		$classname = self::includeJob($action);

		if ($classname !== null && class_exists($classname))
		{
			// if the classname is not NULL and the class exists,
			// instantiate a new object
			$obj = new $classname($id, $args);

			// make sure the object in as instance of CronJob class
			if ($obj instanceof CronJob)
			{
				return $obj;
			}
		}

		// return NULL if the cronjob or the getJob method don't exist
		return null;
	}

	/**
	 * Returns the fields needed for the configuration of the cron job.
	 *
	 * @param 	string 	$action  The name of the file (not case sensitive). 
	 *
	 * @return 	mixed 	The CronFormField array or null.
	 *
	 * @uses 	includeJob()
	 */

	public static function getJobConfiguration($action)
	{
		// include the cronjob and get its classname
		$classname = self::includeJob($action);

		if ($classname !== null && method_exists($classname, 'getConfiguration'))
		{
			// if the classname is not NULL and the class owns the getConfiguration
			// static method, return the object containing its configuration
			return $classname::getConfiguration();
		}

		 // return NULL if the cronjob or the getConfiguration method don't exist
		return null;
	}

	/**
	 * Includes all the cron jobs contained in the base path.
	 *
	 * @param 	boolean  $assoc  True to get an associative array using
	 * 							 the filename as keys. False to return a 
	 * 							 sequential array.
	 *
	 * @return 	array 	 The list of included cron jobs.
	 *
	 * @uses 	includeJob()
	 * @since 	1.6
	 */
	public static function includeAll($assoc = true)
	{
		$pool = array();

		// get the list of files stored in the base path
		foreach (glob(self::$BASE_PATH . '*.php') as $file)
		{
			// strip the full path and take the basename only
			$basename = basename($file);

			// try to include the cron job
			if ($classname = self::includeJob($basename))
			{
				// if associative array use the basename as key
				// and push the classname into the list
				if ($assoc)
				{
					$pool[$basename] = $classname;
				}
				// otherwise push the classname only
				else
				{
					$pool[] = $classname;
				}
			}
		}

		return $pool;
	}

	/**
	 * Includes the cron job file based on the specified action.
	 * Returns the name of the class on success, otherwise NULL.
	 *
	 * @param 	string 	$action  The name of the file (case insensitive). 
	 *
	 * @return 	mixed 	The classname or null.
	 */

	public static function includeJob($action)
	{
		if (empty($action))
		{
			return null;
		}

		// if exists, remove file extension from action
		$action = ($dot_pos = strrpos($action, '.')) ? substr($action, 0, $dot_pos) : $action;

		if (file_exists(self::$BASE_PATH . $action . '.php'))
		{
			// if the file exists include it
			require_once self::$BASE_PATH . $action . '.php';

			$classname = "CronJob$action";

			if (class_exists($classname))
			{
				// return the classname of the included cronjob
				return $classname;
			}
		}

		// return null if the cronjob doesn't exist
		return null;
	}
}
