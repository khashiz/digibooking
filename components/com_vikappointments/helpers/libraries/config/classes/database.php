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

// include parent class in order to extend the configuration without errors
UILoader::import('libraries.config.abstract');

/**
 * Utility class working with a physical configuration stored into the Joomla database.
 *
 * @since  	1.6
 */
class UIConfigDatabase extends UIConfig
{
	/**
	 * Class constructor.
	 *
	 * @param 	array 	$options 	An array of options.
	 */
	public function __construct(array $options = array())
	{
		if (!isset($options['table']))
		{
			$options['table'] = '#__vikappointments_config';
		}

		if (!isset($options['key']))
		{
			$options['key'] = 'param';
		}

		if (!isset($options['value']))
		{
			$options['value'] = 'setting';
		}

		parent::__construct($options);
	}

	/**
	 * @override
	 * Retrieves the value of the setting stored in the Joomla database.
	 *
	 * @param   string 	$key 	The name of the setting.
	 *
	 * @return  mixed 	The value of the setting if exists, otherwise false.
	 */
	protected function retrieve($key)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn($this->options['value']))
			->from($dbo->qn($this->options['table']))
			->where($dbo->qn($this->options['key']) . ' = ' . $dbo->q($key));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadResult();
		}

		return false;
	}

	/**
	 * @override
	 * Registers the value of the setting into the Joomla database.
	 * All the array and objects will be stringified in JSON.
	 *
	 * @param   string  $key 	The name of the setting.
	 * @param   mixed   $val 	The value of the setting.
	 *
	 * @return  bool 	True in case of success, otherwise false.
	 */
	protected function register($key, $val)
	{
		if (is_array($val) || is_object($val))
		{
			$val = json_encode($val);
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$q->update($dbo->qn($this->options['table']))
			->set($dbo->qn($this->options['value']) . ' = ' . $dbo->q($val))
			->where($dbo->qn($this->options['key']) . ' = ' . $dbo->q($key));

		$dbo->setQuery($q);
		$dbo->execute();

		return ($dbo->getAffectedRows() ? true : false);
	}
}
