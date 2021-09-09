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

UILoader::import('libraries.invoice.invoice');

/**
 * Invoices factory class.
 *
 * @since 	1.6
 */
class VAPInvoiceFactory
{
	/**
	 * A list of instances.
	 *
	 * @var array
	 */
	protected static $classes = array();

	/**
	 * Returns a new instance of this object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param 	array 	$order 	The order details.
	 * @param 	string 	$group 	The invoices group.
	 *
	 * @return 	self 	A new instance of this object.
	 */
	public static function getInstance($order, $group = null)
	{
		if (!isset(static::$classes[$group]))
		{
			if (!UILoader::import('libraries.invoice.classes.' . $group))
			{
				throw new Exception('Invoice group [' . $group . '] not supported', 404);
			}

			$classname = 'VAPInvoice' . ucwords($group);

			if (!class_exists($classname))
			{
				throw new Exception('Invoice handler [' . $classname . '] not found', 404);
			}

			static::$classes[$group] = $classname;
		}

		// get cached classname
		$classname = static::$classes[$group];

		// instantiate new object
		$obj = new $classname($order);

		if (!$obj instanceof VAPInvoice)
		{
			throw new Exception('The invoice handler [' . $classname . '] is not a valid instance', 500);
		}

		return $obj;
	}
}
