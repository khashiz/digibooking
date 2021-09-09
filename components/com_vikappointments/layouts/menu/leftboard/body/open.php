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

// displayed after menu.php layout file

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   boolean  $compressed  True if the menu is compressed.
 */

// fix content padding to stick the menu to the left side
UIApplication::getInstance()->fixContentPadding();

?>

<div class="vap-task-wrapper<?php echo $compressed ? ' extended' : ''; ?>">