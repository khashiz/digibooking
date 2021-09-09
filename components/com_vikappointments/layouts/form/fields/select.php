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

$original = array_filter(explode(';;__;;', $cf['_choose']));
$options  = array_filter(explode(';;__;;', $cf['choose']));
$values   = $cf['multiple'] ? json_decode($value ? $value : '[]') : array($value);

JFactory::getDocument()->addScriptDeclaration(
<<<JS
jQuery(document).ready(function() {
	jQuery('select#vap-cf-select{$cf['id']}').select2({
		minimumResultsForSearch: -1,
		allowClear: false,
		width: 240
	});
});\n
JS
);

?>

<div>

	<span class="cf-label">

		<?php if ($cf['required']) { ?>

			<span class="vaprequired"><sup>*</sup></span>

		<?php } ?>

		<span id="vapcf<?php echo $cf['id']; ?>"><?php echo $label; ?></span>

	</span>
	
	<span class="cf-value">

		<select
			name="vapcf<?php echo $cf['id'] . ($cf['multiple'] ? '[]' : ''); ?>"
			id="vap-cf-select<?php echo $cf['id']; ?>"
			class="vap-cf-select"
			<?php echo ($cf['multiple'] ? 'multiple' : ''); ?>
		>

			<?php foreach ($options as $i => $opt) { ?>

				<option 
					value="<?php echo $this->escape($original[$i]); ?>"
					<?php echo (in_array($original[$i], $values) ? 'selected="selected"' : ''); ?>
				><?php echo JText::_($opt); ?></option>

			<?php } ?>

		</select>

	</span>

</div>
