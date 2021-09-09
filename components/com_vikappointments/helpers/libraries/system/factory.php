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
 * Factory application class.
 *
 * @since 1.6
 */
final class UIFactory
{
	/**
	 * Application configuration handlers.
	 *
	 * @var UIConfig[]
	 */
	private static $config = array();

	/**
	 * Application event dispatcher.
	 *
	 * @var UIEventDispatcher
	 */
	private static $eventDispatcher = null;

	/**
	 * Default configuration class handler.
	 *
	 * @var string
	 */
	public static $defaultConfigClass = 'database';

	/**
	 * Class constructor.
	 * @private This object cannot be instantiated. 
	 */
	private function __construct()
	{
		// never called
	}

	/**
	 * Class cloner.
	 * @private This object cannot be cloned.
	 */
	private function __clone()
	{
		// never called
	}

	/**
	 * Get the current configuration object.
	 *
	 * @param 	string 	$class 	The handler to use.
	 * @param 	array 	$args 	An options array.
	 *
	 * @return 	UIConfig
	 *
	 * @throws 	Exception 	When the configuration class doesn't exist.
	 */
	public static function getConfig($class = null, array $args = array())
	{
		// if class not set, get the default one
		if ($class === null)
		{
			$class = static::$defaultConfigClass;
		}

		// check if config class is already instantiated
		if (!isset(static::$config[$class]))
		{
			// build classname
			$classname = 'UIConfig' . ucwords($class[0]) . substr($class, 1);

			// try to import it (on failure, throws exception)
			if (!UILoader::import('libraries.config.classes.' . $class)
				|| !class_exists($classname))
			{
				throw new Exception("Config {$class} not found!");
			}

			// cache instantiation
			static::$config[$class] = new $classname($args);
		}

		return static::$config[$class];
	}

	/**
	 * Returns the internal event dispatcher instance.
	 *
	 * @return 	UIEventDispatcher 	The event dispatcher.
	 */
	public static function getEventDispatcher()
	{
		if (static::$eventDispatcher === null)
		{
			UILoader::import('libraries.event.dispatcher');

			// obtain the software version always from the database
			$version = static::getConfig()->get('version', VIKAPPOINTMENTS_SOFTWARE_VERSION);

			// build options array
			$options = array(
				'alias' 	=> 'com_vikappointments',
				'version' 	=> $version,
				'admin' 	=> JFactory::getApplication()->isAdmin(),
				'call' 		=> null, // call is useless as it would be always the same
			);

			static::$eventDispatcher = UIEventDispatcher::getInstance($options);
		}

		return static::$eventDispatcher;
	}
}
