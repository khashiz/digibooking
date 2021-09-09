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

$data = array(
	/**
	 * @param 	boolean  cartEnabled 		True if the cart system is enabled (false by default).
	 * 										When enabled, it will be displayed a button
	 * 										to add multiple services into the cart.
	 */
	'cartEnabled' => $this->displayData['cartEnabled'],

	/**
	 * @param 	boolean  cartEmpty 			True if the cart doesn't contain yet any appointment.
	 * 										When false, it will be possible to proceed to the 
	 * 										checkout (the cart must be enabled too).
	 * 										
	 */
	'cartEmpty' => $this->cart_empty,

	/**
	 * @param 	boolean  waitlistEnabled 	True if the waiting list is enabled (false by default).
	 * 										When enabled, it will be displayed a button to allow
	 * 										the customers register a subscription for the current service.
	 */
	'waitlistEnabled' => $this->displayData['waitlistEnabled'],

	/**
	 * @param 	boolean  recurrenceEnabled	True if the recurrence is enabled (false by default).
	 * 										When enabled, the customers will be able to book an appointment
	 * 										for the selected service with recurrence.
	 */
	'recurrenceEnabled' => $this->displayData['recurrence']['enabled'],

	/**
	 * @param 	array    recurrenceParams 	An associative array containing the recurrence params:
	 * 										- repeat 	a list containing the values allowed to start the recurrence (1: day, 2: week, 3: month);
	 * 										- for 		a list containing the values allowed to end the recurrence (1: day, 2: week, 3: month);
	 * 										- min 		the minimum recurrence number;
	 * 										- max 		the maximum recurrence number.
	 */
	'recurrenceParams' => $this->displayData['recurrence']['params'],

	/**
	 * @param 	integer  itemid 			The Item ID that will be used to route the URL used for AJAX.
	 * 										If not provided, the current one will be used.
	 */
	'itemid' => $this->itemid,
);

/**
 * The checkout block is displayed from the layout below:
 * /components/com_vikappointments/layouts/blocks/checkout.php
 * 
 * If you need to change something from this layout, just create
 * an override of this layout by following the instructions below:
 * - open the back-end of your Joomla
 * - visit the Extensions > Templates > Templates page
 * - edit the active template
 * - access the "Create Overrides" tab
 * - select Layouts > com_vikappointments > blocks
 * - start editing the checkout.php file on your template to create your own layout
 *
 * @since 1.6
 */
echo JLayoutHelper::render('blocks.checkout', $data);
