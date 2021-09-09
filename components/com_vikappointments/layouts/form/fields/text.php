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

$label 		= $displayData['label'];
$value 		= $displayData['value'];
$cf    		= $displayData['field'];
$user  		= $displayData['user'];
$countries 	= $displayData['countries'];
$zipid 		= isset($displayData['zip']) ? $displayData['zip'] : -1;
$enable_zip = isset($displayData['enableZip']) ? $displayData['enableZip'] : false;

if (empty($value) && VAPCustomFields::isEmail($cf))
{
	$value = JFactory::getUser()->email;
}

// input width (in px)
$text_width = "220";

$onkeypress = "";
$onblur 	= $enable_zip && $cf['id'] == $zipid ? 'onblur="vapValidateZipCode();"' : '';

?>

<div>

	<span class="cf-label">

		<?php if ($cf['required']) { ?>

			<span class="vaprequired"><sup>*</sup></span>

		<?php } ?>

		<span id="vapcf<?php echo $cf['id']; ?>"><?php echo $label; ?></span>

	</span>

	<span class="cf-value">

		<?php 
		if (VAPCustomFields::isPhoneNumber($cf) && VikAppointments::isShowPhonesPrefix())
		{
			// make the input smaller
			$text_width = '117';
			if (empty($user['country_code']))
			{
				$user['country_code'] = $cf['choose'];
			}
			?>

			<select name="vapcf<?php echo $cf['id'] . '_prfx'; ?>" class="vap-phones-select" id="vap-phones-select<?php echo $cf['id']; ?>">

				<?php foreach ($countries as $country) { ?>

					<option 
						value="<?php echo $country['id'] . '_' . $country['country_2_code']; ?>"
						title="<?php echo trim($country['country_name']); ?>"
						<?php echo ($user['country_code'] == $country['country_2_code'] ? 'selected="selected"' : ''); ?>
					><?php echo $country['phone_prefix']; ?></option>

				<?php } ?>

			</select>

			<?php
			// onkeypress event to accept only digit
			$onkeypress = 'onkeypress="return event.charCode >= 48 && event.charCode <= 57;"';

			$root = VAPASSETS_URI;

			// include script to render the select
			JFactory::getDocument()->addScriptDeclaration(
<<<JS
jQuery(document).ready(function(){
	jQuery("#vap-phones-select{$cf['id']}").select2({
		allowClear: true,
		width: 100,
		minimumResultsForSearch: -1,
		formatResult: formatFlagSelect2,
		formatSelection: formatFlagSelect2,
		escapeMarkup: function(m) { return m; }
	});
});

function formatFlagSelect2(state) {
	if(!state.id) return state.text; // optgroup

	return '<img class="vap-opt-flag" src="{$root}css/flags/' + state.id.toLowerCase().split("_")[1] + '.png" />' + state.text;
}
JS
			);
		}    
		?>

		<input 
			type="text" 
			name="vapcf<?php echo $cf['id']; ?>" 
			id="vapcfinput<?php echo $cf['id']; ?>" 
			value="<?php echo $this->escape($value); ?>" 
			size="40" 
			class="vapinput"
			style="width: <?php echo $text_width; ?>px;"
			<?php echo $onkeypress; ?>
			<?php echo $onblur; ?>
		/>

	</span>
	
</div>
