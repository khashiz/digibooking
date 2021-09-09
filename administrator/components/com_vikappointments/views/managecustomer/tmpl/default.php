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

JHtml::_('behavior.modal');

$sel = $this->customer;

$cfields = $this->customFields;
$cf_data = $sel['fields'];

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">

	<?php echo $vik->bootStartTabSet('customer', array('active' => $this->tab)); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('customer', 'customer_details', JText::_('VAPORDERTITLE2')); ?>
	
			<div class="span6">
				<?php echo $vik->openFieldset(JText::_('VAPMANAGECUSTOMERTITLE2'), 'form-horizontal'); ?>
				
					<!-- BILLING NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER2')."*:"); ?>
						<input type="text" name="billing_name" class="required" value="<?php echo $this->escape($sel['billing_name']); ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING MAIL - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER3')."*:"); ?>
						<input type="text" name="billing_mail" class="required" value="<?php echo $sel['billing_mail']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING PHONE - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER4').":"); ?>
						<input type="text" name="billing_phone" value="<?php echo $sel['billing_phone']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING COUNTRY - Select -->
					<?php
					$elements = array(
						JHtml::_('select.option', '', ''),
					);
					foreach ($this->countries as $country)
					{
						$elements[] = JHtml::_('select.option', $country['country_2_code'], $country['country_name']);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER5').":"); ?>
						<select name="country_code" id="vap-countries-sel" class="vap-countries-sel" onChange="countriesSelectValueChanged();">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['country_code']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING STATE - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER6').":"); ?>
						<input type="text" name="billing_state" value="<?php echo $this->escape($sel['billing_state']); ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING CITY - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER7').":"); ?>
						<input type="text" name="billing_city" value="<?php echo $this->escape($sel['billing_city']); ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING ADDRESS - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER8').":"); ?>
						<input type="text" name="billing_address" value="<?php echo $this->escape($sel['billing_address']); ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING ADDRESS 2 - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER19').":"); ?>
						<input type="text" name="billing_address_2" value="<?php echo $this->escape($sel['billing_address_2']); ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING ZIP CODE - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER9').":"); ?>
						<input type="text" name="billing_zip" value="<?php echo $this->escape($sel['billing_zip']); ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING COMPANY - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER10').":"); ?>
						<input type="text" name="company" value="<?php echo $this->escape($sel['company']); ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING VAT NUMBER - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER11').":"); ?>
						<input type="text" name="vatnum" value="<?php echo $this->escape($sel['vatnum']); ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- BILLING SSN - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER20').":"); ?>
						<input type="text" name="ssn" value="<?php echo $this->escape($sel['ssn']); ?>" size="40" style="text-transform: uppercase;" />
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeFieldset(); ?>
			</div>

			<div class="span6">
				<?php echo $vik->openFieldset(JText::_('VAPMANAGECUSTOMERTITLE1'), 'form-horizontal'); ?>
					
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER12').":"); ?>
						<input type="hidden" name="jid" id="vap-users-select" value="<?php echo $sel['jid']; ?>"/>
						<button type="button" class="btn" onClick="userSelectValueChanged(this);"><?php echo JText::_('VAPMANAGECUSTOMER16'); ?></button>
						<input type="hidden" name="create_new_user" value="0" />

						<div class="profile-image-wrapper">
							<?php if (empty($sel['image'])) { ?>
								<img src="<?php echo VAPASSETS_URI . 'css/images/default-profile.png'; ?>" class="vap-customer-image" />
							<?php } else { ?>
								<a href="<?php echo VAPCUSTOMERS_AVATAR_URI . $sel['image']; ?>" class="modal" target="_blank">
									<img src="<?php echo VAPCUSTOMERS_AVATAR_URI . $sel['image']; ?>" class="vap-customer-image" />
								</a>
							<?php } ?>
						</div>
					<?php echo $vik->closeControl(); ?>
					
					<!-- JOOMLA USER NAME - Text -->
					<?php
					$loginControl = array();
					$loginControl['style'] = 'display: none';
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER2')."*:", 'vap-account-row', $loginControl); ?>
						<input type="text" name="user_name" value="<?php echo $this->escape($sel['billing_name']); ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- JOOMLA USER MAIL - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER3')."*:", 'vap-account-row', $loginControl); ?>
						<input type="text" name="user_mail" value="<?php echo $sel['billing_mail']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- JOOMLA USER GENERATE PWD - Button -->
					<?php echo $vik->openControl('', 'vap-account-row', $loginControl); ?>
						<button type="button" id="vap-genpwd-button" class="btn"><?php echo JText::_('VAPMANAGECUSTOMER17'); ?></button>
					<?php echo $vik->closeControl(); ?>
					
					<!-- JOOMLA USER PWD - Password -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER13')."*:", 'vap-account-row', $loginControl); ?>
						<input class="vap-genpwd-input" type="password" name="user_pwd1" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- JOOMLA USER CONFIRM PWD - Password -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMER14')."*:", 'vap-account-row', $loginControl); ?>
						<input class="vap-genpwd-input" type="password" name="user_pwd2" size="40" />
					<?php echo $vik->closeControl(); ?>

					<?php if ($config->getBool('usercredit')) { ?>
						
						<!-- USER CREDIT - Number -->
						<?php echo $vik->openControl(JText::_('VAPUSERCREDIT').":"); ?>
							<input type="number" name="credit" value="<?php echo $sel['credit']; ?>" step="any" min="0" size="40" />
							&nbsp;<?php echo $config->get('currencysymb'); ?>
						<?php echo $vik->closeControl(); ?>

					<?php } ?>
					
				<?php echo $vik->closeFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>


		<?php if (count($cfields) > 0) { ?>

			<?php echo $vik->bootAddTab('customer', 'customer_fields', JText::_('VAPMANAGERESERVATIONTITLE2')); ?>
	
				<div class="span8">
					<?php echo $vik->openEmptyFieldset(); ?>

						<?php
						foreach ($cfields as $cf)
						{
							if (!empty($cf['poplink']))
							{
								$fname = "<a href=\"" . $cf['poplink'] . "\" id=\"vapcf" . $cf['id'] . "\" rel=\"{handler: 'iframe', size: {x: 750, y: 600}}\" target=\"_blank\" class=\"modal\">" . JText::_($cf['name']) . "</a>";
							}
							else
							{
								$fname = "<span id=\"vapcf" . $cf['id'] . "\">" . JText::_($cf['name']) . "</span>";
							}
							
							$_val = "";
							if (count($cf_data) > 0 && !empty($cf_data[$cf['name']]))
							{
								/**
								 * Prevent XSS attacks by escaping submitted data.
								 * 
								 * @since 1.6.3
								 */
								$_val = $this->escape($cf_data[$cf['name']]);
							}

							if (VAPCustomFields::isSeparator($cf))
							{
								echo '<div class="control-group"><h3>' . $fname . '</h3>';
							}
							else
							{
								echo $vik->openControl($fname . ':');
							}
							
							if (VAPCustomFields::isInputText($cf))
							{
								$input_class = '';
								
								if (VAPCustomFields::isEmail($cf))
								{
									$input_class = 'vapemailfield';
								}
								else if (VAPCustomFields::isPhoneNumber($cf))
								{
									$input_class = 'vapphonefield';
								}
								
								$text_width = '';
								
								if (VAPCustomFields::isPhoneNumber($cf))
								{
									$text_width = 'width: 168px !important';
									echo '<select name="vapcf'.$cf['id'].'_prfx" class="vap-phones-select">';
									foreach ($this->countries as $i => $ctry) {
										$suffix = "";
										if (($i != 0 && $this->countries[$i-1]['phone_prefix'] == $ctry['phone_prefix']) 
											|| ($i != count($this->countries) - 1 && $this->countries[$i+1]['phone_prefix'] == $ctry['phone_prefix'])) {
											$suffix = ' : '.$ctry['country_2_code'];
										}
										echo '<option value="'.$ctry['id']."_".$ctry['country_2_code'].'" title="'.trim($ctry['country_name']).'" '.($sel['country_code'] == $ctry['country_2_code'] ? 'selected="selected"' : '').'>'.$ctry['phone_prefix'].$suffix.'</option>';
									}
									echo '</select>';
								}    
								?>
								<input type="text" name="vapcf<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" class="<?php echo $input_class; ?>" size="40" style="<?php echo $text_width; ?>" data-name="<?php echo $cf['name']; ?>"/>
									
							<?php } else if (VAPCustomFields::isTextArea($cf)) { ?>
								
								<textarea name="vapcf<?php echo $cf['id']; ?>" rows="5" cols="30" class="vaptextarea"><?php echo $_val; ?></textarea>
							
							<?php } else if (VAPCustomFields::isInputNumber($cf)) { ?>

								<input type="number" name="vapcf<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" />

							<?php } else if (VAPCustomFields::isCalendar($cf)) { ?>
								
								<?php echo $vik->calendar($_val, 'vapcf'.$cf['id'], 'vapcf'.$cf['id'].'date'); ?>
							
							<?php } else if (VAPCustomFields::isSelect($cf)) { ?>

								<?php
								$choose = array_filter(explode(";;__;;", $cf['choose']));
								$values = $cf['multiple'] ? json_decode($_val ? $_val : '[]') : array($_val);
								?>

								<select 
									name="vapcf<?php echo $cf['id'] . ($cf['multiple'] ? '[]' : ''); ?>"
									class="vap-cf-select"
									<?php echo $cf['multiple'] ? 'multiple' : ''; ?>
								>

									<?php foreach ($choose as $aw) { ?>

										<option value="<?php echo $aw; ?>" <?php echo (in_array($aw, $values) ? 'selected="selected"' : ''); ?>><?php echo $aw; ?></option>

									<?php } ?>

								</select>

							<?php } else if (VAPCustomFields::isSeparator($cf)) { ?>

								<!-- do nothing here -->

							<?php } else if (VAPCustomFields::isInputFile($cf)) { ?>
								
								<?php if (file_exists(VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . $_val)) { ?>
									<a href="<?php echo VAPCUSTOMERS_UPLOADS_URI . $_val; ?>" target="_blank">
										<i class="fa fa-cloud-download big" style="font-size: 26px;"></i>
									</a>
								<?php } ?>

								<input type="hidden" name="old_vapcf<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" />

							<?php } else if (VAPCustomFields::isCheckbox($cf)){ ?>

								<input type="checkbox" name="vapcf<?php echo $cf['id']; ?>" value="1" <?php echo ($_val ? 'checked="checked"' : ''); ?> />
							
							<?php } ?>
							
							<?php
							if (VAPCustomFields::isSeparator($cf))
							{
								echo '</div>';
							}
							else
							{
								echo $vik->closeControl();
							}
							?>
							
					<?php } ?>
					
					<?php echo $vik->closeEmptyFieldset(); ?>
				</div>

			<?php echo $vik->bootEndTab(); ?>

		<?php } ?>

		<?php echo $vik->bootAddTab('customer', 'customer_notes', JText::_('VAPMANAGECUSTOMERTITLE4')); ?>
	
			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control-group">
						<textarea style="width: 90%;height: 400px;" name="notes"><?php echo $sel['notes']; ?></textarea>
					</div>
				<?php echo $vik->closeFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<script>

	var JUSERS_POOL = {};

	jQuery(document).ready(function() {

		jQuery('#vap-genpwd-button').on('click', function() {
			var pwd = generatePassword(8);

			jQuery('.vap-genpwd-input').attr('type', 'text');
			jQuery('.vap-genpwd-input').val(pwd);
		});
		
		jQuery('#vap-countries-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('.vap-phones-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100
		});

		jQuery('.vap-cf-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300,
		});

		jQuery('#vap-users-select').select2({
			placeholder: '<?php echo addslashes(JText::_('VAPMANAGECUSTOMER15')); ?>',
			allowClear: true,
			width: 300,
			minimumInputLength: 2,
			ajax: {
				url: 'index.php?option=com_vikappointments&task=search_jusers_customers&tmpl=component&id=<?php echo $sel['jid']; ?>',
				dataType: 'json',
				type: 'POST',
				quietMillis: 50,
				data: function(term) {
					return {
						term: term
					};
				},
				results: function(data) {

					return {
						results: jQuery.map(data, function (item) {
							
							if (!JUSERS_POOL.hasOwnProperty(item.id)) {
								JUSERS_POOL[item.id] = item;
							}

							return {
								text: item.name + (item.name != item.username ? ' | ' + item.username : ''),
								id: item.id,
								disabled: (item.disabled == 1 ? true : false)
							}
						})
					};
				},
			},
			initSelection: function(element, callback) {
				// the input tag has a value attribute preloaded that points to a preselected repository's id
				// this function resolves that id attribute to an object that select2 can render
				// using its formatResult renderer - that way the repository name is shown preselected
				var id = jQuery(element).val();
				
				jQuery.ajax("index.php?option=com_vikappointments&task=search_jusers&tmpl=component&id="+id, {
					dataType: "json"
				}).done(function(data) {
					if (data.hasOwnProperty('id')) {
						callback(data); 
					}
				});
			},
			formatSelection: function(data) {
				if (jQuery.isEmptyObject(data.name)) {
					// display data retured from ajax parsing
					return data.text;
				}
				// display pre-selected value
				return data.name + (data.name != data.username ? ' | ' + data.username : '');
			},
			dropdownCssClass: 'bigdrop'
		});

		jQuery('input[name="billing_name"], input[name="billing_mail"], input[name="user_name"], input[name="user_mail"]').on('change', function() {
			var parts = jQuery(this).attr('name').split('_');

			if (parts[0] == 'billing') {
				parts[0] = 'user';
			} else {
				parts[0] = 'billing';
			}

			var selector = 'input[name="'+parts.join('_')+'"]';

			if (jQuery(selector).val().length == 0) {
				jQuery(selector).val(jQuery(this).val());
			}
		});

		jQuery('#vap-users-select').on('change', function() {

			var id = jQuery(this).val();

			if (!JUSERS_POOL.hasOwnProperty(id)) {
				return false;
			}

			if (JUSERS_POOL[id].name.length && jQuery('input[name="billing_name"]').val().length == 0) {
				jQuery('input[name="billing_name"]').val(JUSERS_POOL[id].name);
			}

			if (JUSERS_POOL[id].email.length && jQuery('input[name="billing_mail"]').val().length == 0) {
				jQuery('input[name="billing_mail"]').val(JUSERS_POOL[id].email);
			}

		});

	});
	
	function countriesSelectValueChanged() {
		var index = jQuery('#vap-countries-sel option:selected').index();
		jQuery('.vap-phones-select').prop('selectedIndex', index-1);	
		jQuery('.vap-phones-select').trigger('change.select2');
	}
	
	function userSelectValueChanged(btn) {

		if (jQuery(btn).hasClass('active')) {

			jQuery(btn).removeClass('active');

			jQuery('#vap-users-select').prop('disabled', false);

			jQuery('.vap-account-row').hide();
			jQuery('.vap-account-row input').removeClass('required');

			jQuery('input[name="create_new_user"]').val(0);

		} else {

			jQuery(btn).addClass('active');

			jQuery('#vap-users-select').prop('disabled', true);

			jQuery('.vap-account-row input').addClass('required');
			jQuery('.vap-account-row').show();

			jQuery('input[name="create_new_user"]').val(1);
		}
	}
	
	function generatePassword(length) {
		var charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-_",
			pwd = "";
		for (var i = 0; i < length; i++) {
			pwd += charset.charAt(Math.floor(Math.random() * charset.length));
		}
		return pwd;
	}

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#customer_"]').on('click', function() {
			var href = jQuery(this).attr('href').substr(1);
			jQuery('input[name="tabname"]').val(href);
		});

	});

	// validate

	var validator = new VikFormValidator('#adminForm');

	Joomla.submitbutton = function(task) {
		if (task.indexOf('save') !== -1) {
			if (validator.validate(newUserCustomValidation)) {
				Joomla.submitform(task, document.adminForm);	
			}
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}

	function newUserCustomValidation() {
		<?php
		/**
		 * Ignore custom validation in case we are not registering
		 * a new account. This avoids errors in case a browser auto
		 * completes only one of the "password" fields.
		 *
		 * @since 1.6.2
		 */
		?>
		if (parseInt(jQuery('input[name="create_new_user"]').val()) == 0) {
	        return true;
	    }

		if (!validator.isInvalid('input[name="user_pwd1"]')
			&& !validator.isInvalid('input[name="user_pwd2"]')) {

			if (jQuery('input[name="user_pwd1"]').val() == jQuery('input[name="user_pwd2"]').val()) {
				validator.unsetInvalid('input[name="user_pwd1"]');
				validator.unsetInvalid('input[name="user_pwd2"]');

				return true;
			} else {
				validator.setInvalid('input[name="user_pwd1"]');
				validator.setInvalid('input[name="user_pwd2"]');
			}
		}

		return false;
	}
	
</script>
