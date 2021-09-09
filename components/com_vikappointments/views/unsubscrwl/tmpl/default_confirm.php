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

?>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&task=unsubscr_confirm' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post" name="vapunsubscrform">

	<div class="vap-unsubscrwl-content">

		<!-- SUMMARY -->
		<div class="vap-unsubscrwl-summary">
			<?php echo JText::_('VAPUNSUBSCRWAITLISTTEXT'); ?>
		</div>

		<!-- EMAIL -->
		<div class="vap-pushwl-control">
			<div class="vap-pushwl-control-label"><?php echo JText::_('CUSTOMF_EMAIL') ?></div>
			<div class="vap-pushwl-control-value">
				<input type="text" id="vap-field-mail" name="email" class="vap-is-field vap-is-mail" value="<?php echo $this->user->email; ?>" />
			</div>
		</div>

		<!-- PHONE NUMBER -->
		<div class="vap-pushwl-control">
			<div class="vap-pushwl-control-label"><?php echo JText::_('CUSTOMF_PHONE') ?></div>
			<div class="vap-pushwl-control-value">
				<input type="text" id="vap-field-phone" name="phone_number" class="vap-is-field vap-is-req" value="<?php echo $this->user->phoneNumber; ?>" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
			</div>
		</div>

		<!-- SUBMIT -->
		<div class="vap-pushwl-bottom">
			<button type="submit" class="vap-pushwl-btn ok" onClick="return vapValidateBeforeSubmit();"><?php echo JText::_('VAPSUBMIT'); ?></button>
		</div>

	</div>

	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="unsubscr_confirm" />
	<?php echo JHtml::_('form.token'); ?>

</form>

<script>

	jQuery(document).ready(function() {

		jQuery('.vap-is-req').on('blur', function() {
			vapValidateField(this);
		});

		jQuery('.vap-is-mail').on('blur', function() {
			if (vapValidateField(this)) {
				vapValidateMail(this);
			}
		});

	});

	function vapValidateField(elem) {
		if (jQuery(elem).val().length == 0) {
			jQuery(elem).addClass('vaprequiredfield');
			return false;
		} else {
			jQuery(elem).removeClass('vaprequiredfield');
			return true;
		}
	}
	
	function vapValidateMail(elem) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if (re.test(jQuery(elem).val())) {
			jQuery(elem).removeClass('vaprequiredfield');
			return true;
		} else {
			jQuery(elem).addClass('vaprequiredfield');
			return false;
		}
	}

	function vapValidateBeforeSubmit() {
		var args = {
			mail: jQuery('#vap-field-mail').val(),
			phone: jQuery('#vap-field-phone').val(),
		};
		
		var ok = true;
		jQuery.each(args, function(k, v){
			if (v.length == 0) {
				ok = false;
				jQuery('#vap-field-' + k).addClass('vaprequiredfield');
			} else {
				jQuery('#vap-field-' + k).removeClass('vaprequiredfield');
			}
		});

		return ok;
	}

</script>
