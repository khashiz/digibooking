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

$user 		= $this->user;
$customer 	= $this->customer;
$itemid 	= $this->itemid;
	
/**
 * Use default country code for country dropdown.
 *
 * @since 1.6.3
 */
if (empty($customer['country_code']))
{
	// langtag is NULL to auto-detect the current lang tag;
	// required is FALSE to avoid obtainining a default value (US).
	$customer['country_code'] = VAPCustomFields::getDefaultCountryCode($langtag = null, $required = false);
}

?>

<div class="vap-userprofile-toolbar">

	<div class="vap-userprofile-title">
		<h2><?php echo JText::_('VAPUSERPROFILETITLE'); ?></h2>
	</div>

	<div class="vap-userprofile-controls">
		<button class="vap-btn blue" onClick="vapSaveProfile(0);"><?php echo JText::_('VAPSAVE'); ?></button>
		<button class="vap-btn blue" onClick="vapSaveProfile(1);"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
		<button class="vap-btn blue" onClick="vapCloseProfile();"><?php echo JText::_('VAPCLOSE'); ?></button>
	</div>
</div>

<form name="usersaveprofile" action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=userprofile' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" enctype="multipart/form-data">
	
	<div class="vap-userprofile-container">
		
		<div class="vap-userprofile-leftwrapper">
	
			<!-- Full Name -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-name"><?php echo JText::_('VAPUSERPROFILEFIELD1'); ?><sup>*</sup></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="billing_name" class="required" id="billing-name" value="<?php echo $this->escape($customer['billing_name']); ?>" />
				</div>
			</div>
			
			<!-- E-Mail -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-mail"><?php echo JText::_('VAPUSERPROFILEFIELD2'); ?><sup>*</sup></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="billing_mail" class="required" id="billing-mail" value="<?php echo $customer['billing_mail']; ?>" />
				</div>
			</div>
			
			<!-- Phone Number -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-phone"><?php echo JText::_('VAPUSERPROFILEFIELD3'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="billing_phone" id="billing-phone" value="<?php echo $customer['billing_phone']; ?>" />
				</div>
			</div>
			
			<!-- Profile Image -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="user-image"><?php echo JText::_('VAPUSERPROFILEFIELD13'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="file" name="image" id="user-image" />
					<?php if (!empty($customer['image'])) { ?>
						<a href="javascript: void(0);" class="vapmodal" onClick="vapOpenModalImage('<?php echo VAPCUSTOMERS_AVATAR_URI . $customer['image']; ?>');">
							<?php echo $customer['image']; ?>
						</a>
					<?php } ?>
				</div>
			</div>
			
		</div>
		
		<div class="vap-userprofile-rightwrapper">
			
			<!-- Country -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="country-code"><?php echo JText::_('VAPUSERPROFILEFIELD4'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<select name="country_code" id="country-code">
						<option></option>

						<?php foreach ($this->countries as $country) { ?>

							<option
								value="<?php echo $country['country_2_code']; ?>" 
								<?php echo $country['country_2_code'] == $customer['country_code'] ? 'selected="selected"' : ''; ?>
							><?php echo $country['country_name']; ?></option>

						<?php } ?>
					</select>
				</div>
			</div>
			
			<!-- State -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-state"><?php echo JText::_('VAPUSERPROFILEFIELD5'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="billing_state" id="billing-state" value="<?php echo $this->escape($customer['billing_state']); ?>" />
				</div>
			</div>
			
			<!-- City -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-city"><?php echo JText::_('VAPUSERPROFILEFIELD6'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="billing_city" id="billing-city" value="<?php echo $this->escape($customer['billing_city']); ?>" />
				</div>
			</div>
			
			<!-- Address -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-address"><?php echo JText::_('VAPUSERPROFILEFIELD7'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="billing_address" id="billing-address" value="<?php echo $this->escape($customer['billing_address']); ?>" />
				</div>
			</div>
			
			<!-- Address 2 -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-address-2"><?php echo JText::_('VAPUSERPROFILEFIELD8'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="billing_address_2" id="billing-address-2" value="<?php echo $this->escape($customer['billing_address_2']); ?>" />
				</div>
			</div>
			
			<!-- Zip Code -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-zip"><?php echo JText::_('VAPUSERPROFILEFIELD9'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="billing_zip" id="billing-zip" value="<?php echo $this->escape($customer['billing_zip']); ?>" />
				</div>
			</div>
			
			<!-- Company -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-company"><?php echo JText::_('VAPUSERPROFILEFIELD10'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="company" id="billing-company" value="<?php echo $this->escape($customer['company']); ?>" />
				</div>
			</div>
			
			<!-- Vat Num -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-vatnum"><?php echo JText::_('VAPUSERPROFILEFIELD11'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="vatnum" id="billing-vatnum" value="<?php echo $this->escape($customer['vatnum']); ?>" />
				</div>
			</div>
			
			<!-- SSN -->
			<div class="vap-userprofile-field">
				<div class="vap-userprofile-field-label">
					<label for="billing-ssn"><?php echo JText::_('VAPUSERPROFILEFIELD12'); ?></label>
				</div>
				<div class="vap-userprofile-field-control">
					<input type="text" name="ssn" id="billing-ssn" value="<?php echo $this->escape($customer['ssn']); ?>" style="text-transform: uppercase;" />
				</div>
			</div>
		  
		  </div>
			
	</div>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" /> 
	
	<input type="hidden" name="task" value="saveUserProfile" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<script>

	var validator = new VikFormValidator('form[name="usersaveprofile"]');
	validator.setLabel(jQuery('#billing-name'), jQuery('label[for="billing-name"]'));
	validator.setLabel(jQuery('#billing-mail'), jQuery('label[for="billing-mail"]'));

	jQuery(document).ready(function() {

		jQuery('#country-code').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

	});
	
	function vapCloseProfile() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders' . ($itemid ? '&Itemid=' . $itemid : ''), false); ?>';
	}
	
	function vapSaveProfile(close) {
		if (validator.validate()) {
			if (close) {
				jQuery('#vaphiddenreturn').val('1');
			}
			
			document.usersaveprofile.submit();
		}
	}
	
</script>
