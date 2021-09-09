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

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $html        The menu HTML.
 * @var   boolean  $compressed  True if the menu is compressed.
 */

?>

<div class="vap-leftboard-menu<?php echo $compressed ? ' compressed' : ''; ?>" id="vap-main-menu">
	<?php echo $html; ?>
</div>