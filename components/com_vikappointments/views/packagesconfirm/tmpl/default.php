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

$cart 		= $this->cart;
$cfields 	= $this->customFields;
$payments 	= $this->payments;

$skip_payments = !count($payments) ? 1 : 0; // 1 = skip, 0 = not skip

if ($this->juser->guest)
{
	exit ('No direct access');
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&task=savepackagesorder&Itemid=' . $this->itemid); ?>" id="vappayform" name="vappayform" method="post" enctype="multipart/form-data">
	
	<?php

	// load summary cart
	echo $this->loadTemplate('cart');

	if (count($cfields))
	{
		// display custom fields form to collect
		// the billing details of the customers
		echo $this->loadTemplate('fields');
	}

	if (count($payments) > 0 && $this->cart->getTotalCost() > 0)
	{
		// display payments layout
		echo $this->loadTemplate('payments');
	}

	?>
	
	<button type="button" class="vap-btn big blue" id="vapcontinuebutton" onClick="return vapContinueButton();"><?php echo JText::_('VAPPACKSCONFIRMORDER'); ?></button>

	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="savepackagesorder" />
</form>

<script>
	
	function vapContinueButton() {

		var cf_valid = vapValidateCustomFields();
		if (cf_valid) {
			jQuery('#vapordererrordiv').fadeOut();
		} else {
			if (!cf_valid) {
				if (vap_is_mail_valid) {
				   jQuery('#vapordererrordiv').html('<?php echo addslashes(JText::_('VAPCONFAPPREQUIREDERROR')); ?>');
				} else {
				   jQuery('#vapordererrordiv').html('<?php echo addslashes(JText::_('VAPCONFAPPREQUIREDMAILERROR')); ?>');   
				}
			}

			jQuery('#vapordererrordiv').fadeIn();
			return false;
		}
		
		<?php
		/**
		 * Use length <= 1 because, in case of a single published payment,
		 * the input radio is replaced by an input hidden.
		 *
		 * @since 1.6.3
		 */
		?>
		if (jQuery('input[name="vappaymentradio"]:checked').length > 0 || jQuery('input[name="vappaymentradio"]').length <= 1) {
			jQuery("#vappayform").submit();
			return true;
		}

		jQuery(".vap-payment-block label").css('color','#D90000');
		return false;
	}

	var vap_is_mail_valid = true;
		
	function vapValidateCustomFields() {
		var vapvar = document.vappayform;
		
		<?php

		if (count($cfields) > 0) {
			foreach ($cfields as $cf) {
				if (intval($cf['required']) == 1) { ?>

					var field   = vapvar.vapcf<?php echo $cf['id']; ?>;
					var idLabel = 'vapcf<?php echo $cf['id']; ?>';

					<?php if ($cf['type'] == "text" || $cf['type'] == "textarea" || $cf['type'] == "date") { ?>

						if (!field.value.match(/\S/)) {
							document.getElementById(idLabel).style.color = '#D90000';
							return false;
						} else {
							var is_mail = <?php echo (int) VAPCustomFields::isEmail($cf); ?>;
							if (is_mail && !vapValidateMailField(field.value)) {
								document.getElementById(idLabel).style.color = '#D90000';
								vap_is_mail_valid = false;
								return false;
							} else {
								vap_is_mail_valid = true;
							}
							document.getElementById(idLabel).style.color = '';
						}
					
					<?php } else if ($cf['type'] == "select") { ?>

						<?php if ($cf['multiple']) { ?>

							var val = jQuery('select[name="vapcf<?php echo $cf['id']; ?>[]"]').val();

							if (!val || val.length == 0) {
								document.getElementById(idLabel).style.color = '#D90000';
								return false;
							} else {
								document.getElementById(idLabel).style.color = '';
							}

						<?php } else { ?>

							if (!field.value.match(/\S/)) {
								document.getElementById(idLabel).style.color = '#D90000';
								return false;
							} else {
								document.getElementById(idLabel).style.color = '';
							}

						<?php } ?>
					
					<?php } else if ($cf['type'] == "checkbox") { ?>
						
						if (field.checked) {
							document.getElementById(idLabel).style.color = '';
						} else {
							document.getElementById(idLabel).style.color = '#D90000';
							return false;
						}
					
					<?php } else if ($cf['type'] == "file") { ?>
						
						if (!field.value.match(/\S/) && !vapvar.old_vapcf<?php echo $cf['id']; ?>.value.match(/\S/)) {
							document.getElementById(idLabel).style.color = '#D90000';
							return false;
						} else {
							document.getElementById(idLabel).style.color = '';
						}

					<?php } else if ($cf['type'] == "number") { ?>

						if (!field.value.match(/\S/)) {
							document.getElementById(idLabel).style.color = '#D90000';
							return false;
						} else {
							document.getElementById(idLabel).style.color = '';
						}
					
					<?php } ?>

				<?php }
			}
		} ?>

		return true;
	}

	function vapValidateMailField(email) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	}

</script>
