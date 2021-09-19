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

$skip_payments = $this->skipPayments;

$this->zip_field_id = VikAppointments::getZipCodeValidationFieldId($cart->getItemAt(0)->getID2());

/**
 * Get login requirements:
 * [0] - Never
 * [1] - Optional
 * [2] - Required on confirmation page
 * [3] - Required on calendars page
 */
$login_req = VikAppointments::getLoginRequirements();

// If the login is mandatory/optional and the customer is not logged in, we need to show
// a form to allow the customers to login or at least to create a new account.
// Login req = 0 means [NEVER]
if ($login_req > 0 && !VikAppointments::isUserLogged())
{
	// display login/registration form
	echo $this->loadTemplate('login');
	
	// We should stop the flow only if the login is mandatory.
	// Login req = 1 means [OPTIONAL]
	if ($login_req > 1)
	{
		return;
	}
}

// load coupon form, only if there is at least a valid coupon code
echo $this->loadTemplate('coupon');
?>
<div class="vapseparatordiv"></div>
<div class="uk-height-medium uk-background-green uk-padding-large uk-flex uk-flex-middle uk-flex-center">
    <div class="page-header uk-flex-1 uk-text-zero">
        <div class="uk-grid-small" data-uk-grid>
            <div class="uk-width-expand uk-flex uk-flex-middle">
                <div class="uk-flex-1">
                    <h1 class="font uk-text-white uk-h2 uk-text-center f500 uk-margin-remove"><?php echo JText::_('VAPORDERSUMMARYHEADTITLE'); ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&task=saveorder'); ?>" id="vappayform" name="vappayform" method="POST" enctype="multipart/form-data">
    <div class="uk-padding-large">
        <?php
        // load summary cart
        echo $this->loadTemplate('cart');
        ?>
        <?php
        // display a continue button only if the shop feature is enabled
        if(VikAppointments::isCartEnabled())
        {
            $shop_group = VikAppointments::getShopGroupFilter();

            if ($shop_group != 0)
            {
                $url = '';

                if ($shop_group != -2)
                {
                    $url = JRoute::_('index.php?option=com_vikappointments&view=serviceslist' . ($shop_group != -1 ? '&service_group=' . $shop_group : '') . '&Itemid=' . $this->itemid);
                }
                else
                {
                    $url = VikAppointments::getShopCustomLink();
                }
                ?>

                <div class="vapcontinueshopdiv uk-hidden">
                    <a href="<?php echo $url; ?>" class="vap-btn"><?php echo JText::_('VAPCONTINUESHOPPINGLINK'); ?></a>
                </div>
            <?php
            }
        }
        ?>
        <div class="vapcompleteorderdiv">

            <?php
            if (count($cfields))
            {
                // display custom fields form to collect
                // the billing details of the customers
                echo $this->loadTemplate('fields');
            }
            ?>

        </div>
        <div class="vapcompleteorderdiv" id="vappaymentsdiv" style="display: none;">

            <?php
            // display the list of the payments that the customers can choose
            echo $this->loadTemplate('payments');
            ?>

        </div>
        <button type="button" class="vap-btn big blue" id="vapcontinuebutton" onClick="vapContinueButton();"><?php echo JText::_($skip_payments ? 'VAPCONFIRMRESBUTTON' : 'VAPCONTINUEBUTTON'); ?></button>
        <input type="hidden" name="option" value="com_vikappointments" />
        <input type="hidden" name="task" value="saveorder" />
    </div>
</form>


<script>

	var step 		  = 0;
	var skip_payments = <?php echo $skip_payments; ?>;
	var num_payments  = <?php echo count($payments); ?>;	

	jQuery(document).ready(function() {
		if (_ZIP_FIELD_ID_ > 0 && jQuery('#vapcfinput'+_ZIP_FIELD_ID_).val().length > 0) {
			vapValidateZipCode();
		}
	});
		
	function vapContinueButton() {

		if (_ZIP_FIELD_ID_ !== -1 && !_ZIP_VALIDATED_) {
			_ZIP_VALID_ = vapValidateZipCode();
		}
		
		if (step == 0) {
			var cf_valid = vapValidateCustomFields();
			if (cf_valid && _ZIP_VALID_) {
				if( skip_payments == 0 ) {
					jQuery("#vappaymentsdiv").fadeIn("normal");
					jQuery("#vapcontinuebutton").text('<?php echo addslashes(JText::_('VAPCONFIRMRESBUTTON')); ?>');
				}
				step++;
				jQuery('#vapordererrordiv').fadeOut();
			} else {
				if (!cf_valid) {
						if (vap_is_mail_valid) {
						   jQuery('#vapordererrordiv').html('<?php echo addslashes(JText::_('VAPCONFAPPREQUIREDERROR')); ?>');
						} else {
						   jQuery('#vapordererrordiv').html('<?php echo addslashes(JText::_('VAPCONFAPPREQUIREDMAILERROR')); ?>');   
						}
					} else if (_ZIP_VALIDATED_) {
						jQuery('#vapordererrordiv').html('<?php echo addslashes(JText::_('VAPCONFAPPZIPERROR')); ?>');
					}
				jQuery('#vapordererrordiv').fadeIn();
			}
		} else {
			ok = false;
			if( jQuery('input[name="vappaymentradio"]:checked').length > 0 || jQuery('input[name="vappaymentradio"]').length == 1 ) {
				ok = true;
			}

			if (ok) {
				var cf_valid = vapValidateCustomFields();
				if (cf_valid && _ZIP_VALID_) {
					step++;
					jQuery('#vapordererrordiv').fadeOut();
				} else {
					if (!cf_valid) {
						if (vap_is_mail_valid) {
						   jQuery('#vapordererrordiv').html('<?php echo addslashes(JText::_('VAPCONFAPPREQUIREDERROR')); ?>');
						} else {
						   jQuery('#vapordererrordiv').html('<?php echo addslashes(JText::_('VAPCONFAPPREQUIREDMAILERROR')); ?>');   
						}
					} else if (_ZIP_VALIDATED_) {
						jQuery('#vapordererrordiv').html('<?php echo addslashes(JText::_('VAPCONFAPPZIPERROR')); ?>');
					}
					jQuery('#vapordererrordiv').fadeIn();
				}
			} else {
				jQuery("#vappaymentsdiv label").css('color','#D90000');
			}
		}

		if (step > 1 || skip_payments == 1) {
			if (vapValidateCustomFields() && _ZIP_VALID_) {
				jQuery("#vappayform").submit();
			} 
		}
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

	var _ZIP_FIELD_ID_ 	= <?php echo $this->zip_field_id; ?>;
	var _ZIP_VALID_ 	= <?php echo $this->enableZip ? 1 : 0; ?> == 0 ? true : false;
	var _ZIP_VALIDATED_ = _ZIP_VALID_;

	<?php if ($this->zip_field_id != -1 && $this->enableZip) { ?>

		function vapValidateZipCode() {
			var zip = jQuery('#vapcfinput'+_ZIP_FIELD_ID_).val();
		
			jQuery.noConflict();
			
			var jqxhr = jQuery.ajax({
				type: "POST",
				url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=validate_zip_code&tmpl=component' . ($this->itemid ? '&Itemid=' . $this->itemid : false), false); ?>",
				data: {
					zipcode: zip
				}
			}).done(function(resp){
				var obj = jQuery.parseJSON(resp); 
				
				if (obj[0]) {
					jQuery('#vapcfinput'+_ZIP_FIELD_ID_).addClass('vaprequiredfieldok');
					jQuery('#vapcfinput'+_ZIP_FIELD_ID_).removeClass('vaprequiredfield');
					_ZIP_VALID_ = true;
					jQuery('#vapordererrordiv').fadeOut();
				} else {
					jQuery('#vapcfinput'+_ZIP_FIELD_ID_).addClass('vaprequiredfield');
					jQuery('#vapcfinput'+_ZIP_FIELD_ID_).removeClass('vaprequiredfieldok');
					_ZIP_VALID_ = false;
					jQuery('#vapordererrordiv').html('<?php echo addslashes(JText::_('VAPCONFAPPZIPERROR')); ?>');
					jQuery('#vapordererrordiv').fadeIn();
				}
				
				_ZIP_VALIDATED_ = true;
			});	
		}
		
	<?php } ?>

</script>
