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
 * Base interface to display a custom input.
 *
 * @since 	1.6
 */
interface UIInput
{
	/**
	 * Call this method to build and return the HTML of the input.
	 *
	 * @return 	string 	The input HTML.
	 */
	public function display();
}
