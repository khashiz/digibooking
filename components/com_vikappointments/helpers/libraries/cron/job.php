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
 * The class used to manage the procedure and the configuration of a CronJob.
 *
 * @since 	 1.5
 */
abstract class CronJob
{
	/**
	 * The unique identifier of the cron job.
	 *
	 * @var integer
	 */
	private $id;

	/** 
	 * The list containing the settings of the cron job.
	 * The settings of the cron job cannot be directly accessed (@see get() method).
	 *
	 * @var array
	 */
	private $args;

	/**
	 * The construct of the cron job to initialize the required parameters of this object.
	 *
	 * @param 	integer  $id 	The Cron Job ID.
	 * @param 	mixed 	 $args 	The configuration array.
	 */
	public function __construct($id, $args = array())
	{
		$this->id 	= $id;
		$this->args = $args;
	}

	/**
	 * Returns the ID of the cron job.
	 *
	 * @return 	integer  The identifier of the cron job.
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Returns the title of the cron job.
	 *
	 * @return 	string 	The title of the cron job.
	 */
	public static function title()
	{
		return '';
	}

	/**
	 * Returns the value of the setting specified. An empty value if the setting doesn't exist.
	 * The settings of the cronjob are not accessible from external classes.
	 *
	 * @param 	string  $key 	The name of the setting.
	 * @param 	mixed 	$def 	The default value to get if the
	 * 							setting doesn't exist.
	 *
	 * @return 	string 	The value of the setting.
	 */
	protected function get($key, $def = '')
	{
		if (array_key_exists($key, $this->args))
		{
			return $this->args[$key];
		}
		
		return $def;
	}

	/**	 
	 * Performs the work that the cron job should do.
	 *
	 * @return 	CronResponse  The response of the job.
	 */
	public abstract function doJob();

	/**
	 * Returns the fields of the configuration in an array.
	 *
	 * @return 	array 	The CronFormField list used for the configuration. 
	 */
	public static function getConfiguration()
	{
		return array();
	}

	/**
	 * This function is called only once during the installation of the cron job.
	 * Returns true on success, otherwise false.
	 *
	 * @return  boolean	 The status of the installation.
	 */
	public function install()
	{
		return true;
	}

	/**
	 * This function is called only once during the uninstallation of the cron job.
	 * Returns true on success, otherwise false.
	 *
	 * @return 	boolean  The status of the uninstallation.
	 */
	public function uninstall()
	{
		return true;
	}
}
