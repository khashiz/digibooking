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

$date_format = UIFactory::getConfig()->get('dateformat');

// CALENDAR

static $loaded = 0;

if (!$loaded)
{
	VikAppointments::load_datepicker_regional();

	JFactory::getDocument()->addScriptDeclaration(
<<<JS
jQuery(function(){

	var sel_format 	 = "$date_format";
	var df_separator = sel_format[1];

	sel_format = sel_format.replace(new RegExp('\\\'+df_separator, 'g'), "");

	if (sel_format == "Ymd") {

		Date.prototype.format = "yy"+df_separator+"mm"+df_separator+"dd";

	} else if (sel_format == "mdY") {

		Date.prototype.format = "mm"+df_separator+"dd"+df_separator+"yy";

	} else {

		Date.prototype.format = "dd"+df_separator+"mm"+df_separator+"yy";

	}

	jQuery(document).ready(function() {
		jQuery(".vapinput.calendar").datepicker({
			dateFormat: new Date().format,
		});
	});
	
});
JS
	);

	$loaded = 1;
}

?>

<div>

	<span class="cf-label">

		<?php if ($cf['required']) { ?>

			<span class="vaprequired"><sup>*</sup></span>

		<?php } ?>

		<span id="vapcf<?php echo $cf['id']; ?>"><?php echo $label; ?></span>

	</span>

	<span class="cf-value">

		<input
			type="text"
			name="vapcf<?php echo $cf['id']; ?>"
			id="vapcfinput<?php echo $cf['id']; ?>"
			value="<?php echo $this->escape($value); ?>"
			size="25"
			class="vapinput calendar"
		/>

	</span>

</div>
