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

$isreq = $cf['required'] == 1 ? "<span class=\"vaprequired\"><sup>*</sup></span> " : '';
				
if (!empty($cf['poplink']))
{
	$label = "<a href=\"javascript: void(0);\" onclick=\"vapOpenPopup('" . $cf['poplink'] . "');\" id=\"vapcf" . $cf['id'] . "\">" . $isreq . $label . "</a>";
}
else
{
	$label = "<span id=\"vapcf" . $cf['id'] . "\">" . $isreq . $label . "</span>";
}

?>

<div>

	<span class="cf-label">&nbsp;</span>

	<span class="cf-value">

		<input
			type="checkbox"
			id="vapcf<?php echo $cf['id']; ?>cb"
			name="vapcf<?php echo $cf['id']; ?>"
			value="1"
			<?php echo ($value == 1 ? 'checked="checked"' : ''); ?>
		/>
		
		<label for="vapcf<?php echo $cf['id']; ?>cb"><?php echo $label; ?></label>

	</span>

</div>
