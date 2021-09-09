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

$label = $displayData['label'];
$value = $displayData['value'];
$cf    = $displayData['field'];

?>

<div>

	<span class="cf-label top">

		<?php if ($cf['required']) { ?>

			<span class="vaprequired"><sup>*</sup></span>

		<?php } ?>

		<span id="vapcf<?php echo $cf['id']; ?>"><?php echo $label; ?></span>

	</span>

	<span class="cf-value">

		<textarea 
			name="vapcf<?php echo $cf['id']; ?>"
			rows="5"
			cols="30"
			class="vaptextarea"><?php echo $value; ?></textarea>

	</span>

</div>
