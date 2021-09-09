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
	 * @param 	integer  id_service 	The ID of the service for which
	 * 									the customer is interested to (required).
	 */
	'id_service' => $this->service['id'],

	/**
	 * @param 	integer  id_employee 	The ID of the employee for which
	 * 									the customer is interested to (optional).
	 */
	'id_employee' => $this->idEmployee,

	/**
	 * @param 	string   title 			An optional title to use for the modal box (optional).
	 */
	'title' => JText::_('VAPWAITLISTADDBUTTON'),

	/**
	 * @param 	integer  itemid 		The Item ID that will be used to route the URL used for AJAX.
	 * 									If not provided, the current one will be used.
	 */
	'itemid' => $this->itemid,
);

/**
 * The waiting list modal is displayed from the layout below:
 * /components/com_vikappointments/layouts/blocks/waitlist.php
 * 
 * If you need to change something from this layout, just create
 * an override of this layout by following the instructions below:
 * - open the back-end of your Joomla
 * - visit the Extensions > Templates > Templates page
 * - edit the active template
 * - access the "Create Overrides" tab
 * - select Layouts > com_vikappointments > blocks
 * - start editing the waitlist.php file on your template to create your own layout
 *
 * @since 1.6
 */
echo JLayoutHelper::render('blocks.waitlist', $data);
