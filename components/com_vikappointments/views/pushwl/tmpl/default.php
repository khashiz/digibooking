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
 * If you need to change the layout of this view,
 * leave this file as it is and create an override of
 * default_subscribe.php file on your template.
 *
 * @since 1.6
 */

// load subscription view
$contents = $this->loadTemplate('subscribe');

// encode the HTML of the view in JSON to avoid encoding errors
echo json_encode(array($contents));

// stop the flow and return the contents to the AJAX call that requested this view
exit;
