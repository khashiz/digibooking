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

$sel = $this->reservation;

$cfields 	= $this->custom_fields;
$payments 	= $this->payments;
$config  	= $this->config;

$vik = UIApplication::getInstance();

$curr_symb = $config->get('currencysymb');

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

if (empty($sel['purchaser_country']))
{
	foreach ($cfields as $cf)
	{
		if ($cf['type'] == 'text' && VAPCustomFields::isPhoneNumber($cf))
		{
			$sel['purchaser_country'] = $cf['choose'];
			break;
		}
	}
}

if (empty($sel['id_user']) || $sel['id_user'] <= 0)
{
	$sel['id_user'] = '';
}

VikAppointments::setCurrentTimezone($this->employeeTimezone);

$selected_charge = 0;
$last_id = 0;

if ($this->tab == 'reservation_options')
{
	$this->tab = 'reservation_details';
}

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">

	<?php echo $vik->bootStartTabSet('reservation', array('active' => $this->tab)); ?>

		<!-- DETAILS -->
				
		<?php echo $vik->bootAddTab('reservation', 'reservation_details', JText::_('VAPMANAGERESERVATIONTITLE1')); ?>
					
			<!-- DETAILS -->

			<div class="span6">
				<?php echo $vik->openEmptyFieldset(); ?>

					<!-- CREATED ON - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION37').':'); ?>
						<input type="text" value="<?php echo date($date_format . ' ' . $time_format, $sel['createdon']); ?>" size="40" readonly />
					<?php echo $vik->closeControl(); ?>

					<!-- CREATED BY - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION41').':'); ?>
						<input type="text" value="<?php echo empty($sel['username']) ? JText::_('VAPRESLISTGUEST') : $sel['username']; ?>" size="40" readonly />
					<?php echo $vik->closeControl(); ?>
					
					<!-- USER - Dropdown -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION33').':'); ?>
						<input type="hidden" name="id_user" class="vap-users-select" value="<?php echo $sel['id_user']; ?>"/>
					<?php echo $vik->closeControl(); ?>
					
					<!-- NOMINATIVE - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION32').':'); ?>
						<input type="text" id="vapname" name="purchaser_nominative" value="<?php echo $this->escape($sel['purchaser_nominative']); ?>" size="40"/>
					<?php echo $vik->closeControl(); ?>
					
					<!-- MAIL - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION8').':'); ?>
						<input type="text" id="vapemail" name="purchaser_mail" value="<?php echo $sel['purchaser_mail']; ?>" size="40" onBlur="composeMailFields();"/>
					<?php echo $vik->closeControl(); ?>
					
					<!-- PHONE - Custom -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION27').':'); ?>
						<?php 
						echo '<select name="phone_prefix" class="vap-phones-select">';
						foreach ($this->countries as $i => $ctry)
						{
							$suffix = "";
							if (($i != 0 && $this->countries[$i-1]['phone_prefix'] == $ctry['phone_prefix']) 
								|| ($i != count($this->countries) - 1 && $this->countries[$i+1]['phone_prefix'] == $ctry['phone_prefix']))
							{
								$suffix = ' : '.$ctry['country_2_code'];
							}
							echo '<option value="'.$ctry['id']."_".$ctry['country_2_code'].'" title="'.trim($ctry['country_name']).'" '.($sel['purchaser_country'] == $ctry['country_2_code'] ? 'selected="selected"' : '').'>'.$ctry['phone_prefix'].$suffix.'</option>';
						}
						echo '</select>';
						?>
						<input type="text" id="vapphone" name="purchaser_phone" value="<?php echo $sel['purchaser_phone']; ?>" size="40" onBlur="composePhoneFields();" style="width: 168px !important;" />
					<?php echo $vik->closeControl(); ?>

					<!-- COUPON - Form -->
					<?php if (count($this->coupons)) { ?>
						<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION21').':'); ?>
							<?php if (strlen($sel['coupon_str'])) {
								list($code, $type, $amount) = explode(';;', $sel['coupon_str']);
								echo $code . ' : ' . $amount . ' ' . ($type == 1 ? '%' : $curr_symb);
								?><input type="hidden" name="coupon_str" value="<?php echo $sel['coupon_str']; ?>" /><?php
							} else {
								$coupon_types = array(
									JText::_('VAPCOUPONVALID0'),
									JText::_('VAPCOUPONVALID1'),
									JText::_('VAPCOUPONVALID2'),
								);

								$now = time();

								$elements = array();
								$elements[0] = array(JHtml::_('select.option', '', ''));
								foreach ($this->coupons as $cpn)
								{
									// check if the coupon is incoming
									if ($cpn['dstart'] > $now)
									{
										$type = 2;
									}
									// Check if the coupon has no expiration date or if the expiration is in the future.
									// In addition, make sure coupon is permanent or it remain enough usages.
									else if (($cpn['dend'] == -1 || $cpn['dend'] > $now) 
										&& ($cpn['type'] == 1 || $cpn['max_quantity'] - $cpn['used_quantity'] > 0))
									{
										$type = 1;
									}
									// otherwise mark the coupon has expired
									else
									{
										$type = 0;
									}

									$type = $coupon_types[$type];

									if (!isset($elements[$type]))
									{
										$elements[$type] = array();
									}

									$elements[$type][] = JHtml::_('select.option', $cpn['id'], $cpn['code'] . ' - ' . $cpn['value'] . ' ' . ($cpn['percentot'] == 1 ? '%' : $curr_symb));
								}

								$params = array(
									'id' 			=> 'vap-coupons-select',
									// 'list.attr' 	=> array('class' => 'required'),
									'group.items' 	=> null,
									'list.select'	=> '',
								);
								echo JHtml::_('select.groupedList', $elements, 'coupon', $params);
							} ?>
						<?php echo $vik->closeControl(); ?>
					<?php } ?>

					<!-- PAYMENTS - Dropdown -->
					<?php if (count($payments) > 0) { ?>
						<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION28').':'); ?>
							<select name="id_payment" id="vap-payment-sel">
								<option value="" data-charge="0"></option>
								<?php
								foreach ($payments as $group => $list)
								{
									?>
									<optgroup label="<?php echo JText::_($group ? 'JPUBLISHED' : 'JUNPUBLISHED'); ?>">
										<?php
										foreach ($list as $p)
										{
											$selected = '';
											if ($sel['id_payment'] == $p['id'])
											{
												$selected_charge = $p['charge'];
												$selected = 'selected="selected"';
											}

											$pay_text = $p['name'] . ($p['charge'] != 0 ? ' ('.VikAppointments::printPriceCurrencySymb($p['charge']).')' : '');

											?>
											<option value="<?php echo $p['id']; ?>" data-charge="<?php echo $p['charge']; ?>" <?php echo $selected; ?>>
												<?php echo $pay_text; ?>
											</option>
										<?php } ?>
									</optgroup>
								<?php } ?>
							</select>
						<?php echo $vik->closeControl(); ?>
					<?php } ?>

					<!-- STATUS - Dropdown -->
					<?php
					$elements = array();
					foreach (array('CONFIRMED', 'PENDING', 'REMOVED', 'CANCELED') as $s)
					{
						$elements[] = JHtml::_('select.option', $s, 'VAPSTATUS' . $s);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION19').':'); ?>
						<select name="status" id="vapstatussel" class="vap-status-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['status'], true); ?>
						<select>
					<?php echo $vik->closeControl(); ?>

					<!-- TOTAL COST - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION9').':'); ?>
						<input type="number" name="total_cost" value="<?php echo $sel['total_cost']; ?>" size="10" min="0" id="vaprtotalcost" step="any" />
						<?php echo $curr_symb; ?></td>
					<?php echo $vik->closeControl(); ?>
					
					<!-- PAID - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['paid'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['paid'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION11').':'); ?>
						<?php echo $vik->radioYesNo('paid', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- NOTIFY CUSTOMER - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', false);
					$elem_no  = $vik->initRadioElement('', '', true);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION24').':'); ?>
						<?php echo $vik->radioYesNo('notifycust', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- ORDER STATUS -->
				
		<?php echo $vik->bootAddTab('reservation', 'reservation_orderstatus', JText::_('VAPORDERSTATUSES')); ?>

			<?php echo $this->loadTemplate('orderstatus'); ?>

		<?php echo $vik->bootEndTab(); ?>

		<?php if (count($cfields) > 0) { ?>

			<!-- CUSTOM FIELDS -->
				
			<?php echo $vik->bootAddTab('reservation', 'reservation_custfields', JText::_('VAPMANAGERESERVATIONTITLE2')); ?>

				<?php echo $this->loadTemplate('fields'); ?>

			<?php echo $vik->bootEndTab(); ?>

		<?php } ?>

		<!-- DESCRIPTION -->

		<?php echo $vik->bootAddTab('reservation', 'reservation_notes', JText::_('VAPMANAGERESERVATIONTITLE4')); ?>
	
			<?php echo $this->loadTemplate('notes'); ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id_employee" value="<?php echo $sel['id_employee']; ?>" />
	<?php foreach ($this->allServices as $service) { ?>
		<input type="hidden" name="id_service[]" value="<?php echo $service; ?>" />
	<?php } ?>

	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<div id="dialog-confirm" title="<?php echo JText::_('VAPWLNOTIFYMODALTITLE');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-mail-closed" style="float: left; margin: 0 7px 20px 0;"></span>
		<span><?php echo JText::_('VAPWLNOTIFYMODALCONTENT'); ?></span>
	</p>
</div>

<?php
JText::script('JYES');
JText::script('JNO');
JText::script('JCANCEL');
JText::script('VAPMULTIORDERUPDATECHILDREN');
?>

<script>

	var BILLING_USERS_POOL = {};

	var PAYMENT_CHARGE = <?php echo $selected_charge; ?>;

	var DIALOG_3_STATES = null;
	
	jQuery(document).ready(function(){

		DIALOG_3_STATES = new VikConfirmDialog(Joomla.JText._('VAPMULTIORDERUPDATECHILDREN'))
			.addButton(Joomla.JText._('JYES'), function(task) {
				jQuery('#adminForm').append('<input type="hidden" name="updatechild" value="1" />');

				Joomla.submitform(task, document.adminForm);
			})
			.addButton(Joomla.JText._('JNO'), function(task) {
				Joomla.submitform(task, document.adminForm);
			})
			.addButton(Joomla.JText._('JCANCEL'));
		
		jQuery('.vap-phones-select').on('change', function(){
			jQuery('.vap-phones-select').select2('val', jQuery(this).val());
		});

		jQuery('.vap-phones-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100
		});

		jQuery('#vap-payment-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('.vap-status-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 200
		});

		jQuery('#vap-coupons-select').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('.vap-cf-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300,
		});
		
		jQuery('.vap-users-select').select2({
			placeholder: '--',
			allowClear: true,
			width: 300,
			minimumInputLength: 2,
			ajax: {
				url: 'index.php?option=com_vikappointments&task=search_users&tmpl=component',
				dataType: 'json',
				type: "POST",
				quietMillis: 50,
				data: function(term) {
					return {
						term: term
					};
				},
				results: function(data) {
					return {
						results: jQuery.map(data, function (item) {

							if (!BILLING_USERS_POOL.hasOwnProperty(item.id))
							{
								BILLING_USERS_POOL[item.id] = item;
							}

							return {
								text: item.billing_name,
								id: item.id
							}
						})
					};
				},
			},
			initSelection: function(element, callback) {
				// The input tag has a value attribute preloaded that points to a preselected repository's id.
				// This function resolves that id attribute to an object that select2 can render
				// using its formatResult renderer - that way the repository name is shown preselected
				var id = jQuery(element).val();
				
				jQuery.ajax("index.php?option=com_vikappointments&task=search_users&tmpl=component&id="+id, {
					dataType: "json"
				}).done(function(data) {
					if (data.hasOwnProperty('id')) {
						callback(data); 
					}
				});
			},
			formatSelection: function(data) {
				if (jQuery.isEmptyObject(data.billing_name)) {
					// display data retured from ajax parsing
					return data.text;
				}
				// display pre-selected value
				return data.billing_name;
			},

			dropdownCssClass: "bigdrop",
		});

		jQuery('.vap-users-select').on('change', function() {

			var id = jQuery(this).val();
			
			if (BILLING_USERS_POOL[id].hasOwnProperty('billing_name')) {
				jQuery('#vapname').val(BILLING_USERS_POOL[id].billing_name);
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('billing_mail')) {
				jQuery('#vapemail').val(BILLING_USERS_POOL[id].billing_mail);
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('billing_phone')) {
				jQuery('#vapphone').val(BILLING_USERS_POOL[id].billing_phone);
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('country_code')) {
				jQuery('select[name="phone_prefix"] option').each(function() {
					var code = jQuery(this).val();

					if (code.split('_')[1] == BILLING_USERS_POOL[id].country_code) {
						jQuery('.vap-phones-select').select2('val', code);
						return false;
					}
				});
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('fields')) {
				
				jQuery.each(BILLING_USERS_POOL[id].fields, function(k, v) {

					jQuery('*[data-name="'+k+'"]').val(v);

				});

			}

		});

		// the total cost shouldn't be altered because the payment charge is always excluded
		jQuery('#vap-payment-sel').on('change', function() {

			// var new_charge = parseFloat(jQuery(this).find(':selected').data('charge'));

			// var tcost = parseFloat(jQuery('input[name="total_cost"]').val()) + new_charge - PAYMENT_CHARGE;

			// jQuery('input[name="total_cost"]').val(tcost.toFixed(tcost == Math.floor(tcost) ? 0 : 2));

			// PAYMENT_CHARGE = new_charge;

		});

		jQuery('.vap-status-sel').on('change', function() {
			// update other select too
			jQuery('.vap-status-sel').not(this).select2('val', jQuery(this).val());
			// trigger change event
			statusValueChanged();
		});
	});
	
	function composeMailFields() {
		var email = jQuery('#vapemail').val();
		jQuery('.vapemailfield').val(email);
	}
	
	function composePhoneFields() {
		var phone = jQuery('#vapphone').val();
		jQuery('.vapphonefield').val(phone);
	}
	
	function statusValueChanged() {
		<?php if ($sel['status'] != 'CONFIRMED') { ?>

		if (jQuery('#vapstatussel').val() == 'CONFIRMED') {
			jQuery('input[name="notifycust"]').prop('checked', true);
		}

		<?php } ?>
	}

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#reservation_"]').on('click', function() {
			var href = jQuery(this).attr('href').substr(1);
			jQuery('input[name="tabname"]').val(href);
		});

	});

	// form observer

	var formObserver = null;

	jQuery(document).ready(function() {

		formObserver = new VikFormObserver('#adminForm');

		callbackOn(
			// check if the NOTES editor is ready
			function() {
				return Joomla.editors.instances.hasOwnProperty('notes');
			},
			// when ready, freeze the form
			function() {
				formObserver.exclude('input[name="task"]')
					.exclude('input[name="tabname"]')
					.exclude('input[name="notifycust"]')
					.setCustom('textarea[name="notes"]', function() {
						return Joomla.editors.instances.notes.getValue();
					})
					.freeze();
			}
		);

	});

	// validate

	var validator = new VikFormValidator('#adminForm');

	Joomla.submitbutton = function(task) {
		if (task.indexOf('save') !== -1) {
			if (validator.validate()) {

				if (formObserver.isChanged()) {
					DIALOG_3_STATES.show(task);
				} else {
					Joomla.submitform(task, document.adminForm);	
				}
			}
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}

</script>
