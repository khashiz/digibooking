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
$options 	= $this->options;
$res_opt 	= $this->res_opt;
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
			$sel['purchaser_country'] = $this->reservation['purchaser_country'] = $cf['choose'];
			break;
		}
	}
}

if (empty($sel['id_user']) || $sel['id_user'] <= 0)
{
	$sel['id_user'] = '';
}
$this->employeeTimezone = 'Asian/Tehran';
VikAppointments::setCurrentTimezone($this->employeeTimezone);

$selected_charge = 0;
$last_id = 0;

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">

	<?php echo $vik->bootStartTabSet('reservation', array('active' => $this->tab)); ?>

		<!-- DETAILS -->
				
		<?php echo $vik->bootAddTab('reservation', 'reservation_details', JText::_('VAPMANAGERESERVATIONTITLE1')); ?>
					
			<!-- DETAILS -->

			<div class="span6">
				<?php echo $vik->openEmptyFieldset(); ?>
					
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

					<!-- NOTIFY EMPLOYEE - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', false);
					$elem_no  = $vik->initRadioElement('', '', true);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION36').':'); ?>
						<?php echo $vik->radioYesNo('notifyemp', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

			<!-- SUMMARY -->

			<div class="span6">
				<?php echo $vik->openEmptyFieldset(); ?>
				
					<!-- EMPLOYEE - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION3').'*:'); ?>
						<input type="text" value="<?php echo $this->escape($sel['ename']); ?>" size="40" readonly />
					<?php echo $vik->closeControl(); ?>
					
					<!-- SERVICE - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION4').'*:'); ?>
						<input type="text" value="<?php echo $this->escape($sel['sname']); ?>" size="40" readonly />
					<?php echo $vik->closeControl(); ?>
					
					<!-- CHECKIN - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION5').'*:'); ?>
						<input type="text" value="<?php echo ArasJoomlaVikApp::jdate($date_format . ' ' . $time_format, $sel['checkin_ts']); ?>" size="40" readonly />
					<?php echo $vik->closeControl(); ?>
					
					<!-- CHECKOUT - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION6').'*:'); ?>
						<input type="text" value="<?php echo ArasJoomlaVikApp::jdate($date_format . ' ' . $time_format, VikAppointments::getCheckout($sel['checkin_ts'], $sel['duration'])); ?>" size="40" readonly />
					<?php echo $vik->closeControl(); ?>
					
					<!-- CHANGE DATA - Text -->
					<?php if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments') && $sel['id'] > 0) { ?>
						<?php echo $vik->openControl(''); ?>
							<button type="button" onClick="changeReservationValues();" class="btn"><?php echo JText::_('VAPMANAGERESERVATION7');?></button>
						<?php echo $vik->closeControl(); ?>
					<?php } ?>

					<!-- PEOPLE - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION25').':'); ?>
						<input type="number" name="people" value="<?php echo $sel['people']; ?>" size="10" min="1" />
					<?php echo $vik->closeControl(); ?>

					<!-- TOTAL COST - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION9').':'); ?>
						<input type="number" name="total_cost" value="<?php echo $sel['total_cost']; ?>" size="10" min="0" id="vaprtotalcost" step="any" />
						<?php echo $curr_symb; ?></td>
						<a href="javascript: void(0);" onclick="vapOpenJModal('ratestest', null, true);">
							<i class="fa fa-info-circle big" style="margin-left: 5px;"></i>
						</a>
					<?php echo $vik->closeControl(); ?>
					
					<!-- DURATION - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION10').':'); ?>
						<input type="number" name="duration" value="<?php echo $sel['duration']; ?>" size="10" min="0" id="vaprduration" />
						<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
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

		<!-- OPTIONS -->

		<?php echo $vik->bootAddTab('reservation', 'reservation_options', JText::_('VAPMANAGERESERVATIONTITLE3')); ?>
		
			<?php if (count($options) == 0) { ?>

				<div class="span6">
					<?php echo $vik->openEmptyFieldset(); ?>
						<div class="control-group">
							<?php echo JText::_('VAPRESERVATIONHASNOOPTION'); ?>
						</div>
					<?php echo $vik->closeEmptyFieldset(); ?>
				</div>

			<?php } else { ?>

				<div class="span6">
					<?php echo $vik->openEmptyFieldset(); ?>

						<!-- OPTION - Dropdown -->
						<?php
						$elements = array();
						$elements[0] = array();

						$unpublished_opt_group = JText::_('VAPMANAGECRONJOB10');

						foreach ($options as $o)
						{
							$group = $o['published'] ? 0 : $unpublished_opt_group;

							if (!isset($elements[$group]))
							{
								$elements[$group] = array();
							}

							$elements[$group][] = JHtml::_('select.option', $o['id'], $o['name']);
						}
						?>
						<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION14').':'); ?>
							<?php
							$params = array(
								'id' 			=> 'vapoptionselect',
								'list.attr' 	=> array('onchange' => 'optionValueChanged();'),
								'group.items' 	=> null,
								'list.select'	=> null,
							);
							echo JHtml::_('select.groupedList', $elements, '', $params);
							?>
						<?php echo $vik->closeControl(); ?>

						<!-- VARIATION - Dropdown -->
						<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION39').':', 'vap-option-vars', array('style' => 'display: none;')); ?>
							<select id="vapoptvar" onChange="variationValueChanged();"></select>
						<?php echo $vik->closeControl(); ?>

						<!-- OPTION PRICE - Number -->
						<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION15').':'); ?>
							<input type="number" value="" name="opt_price" size="6" min="-999999" max="999999" id="vapoprice" step="any" />&nbsp;<?php echo $curr_symb; ?>
						<?php echo $vik->closeControl(); ?>

						<!-- OPTION QUANTITY - Number -->
						<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION17').':'); ?>
							<input type="number" value="" name="opt_quant" size="6" min="1" max="999999" id="vapoquant" />
						<?php echo $vik->closeControl(); ?>

						<!-- ADD OPTION - Button -->
						<?php echo $vik->openControl(''); ?>
							<button type="button" onClick="addSelectedOption();" id="vapobutton" class="btn"><?php echo JText::_('VAPMANAGERESERVATION18');?></button>
						<?php echo $vik->closeControl(); ?>

					<?php echo $vik->closeEmptyFieldset(); ?>
				</div>

				<div class="span6">
					<?php echo $vik->openEmptyFieldset(); ?>

						<!-- OPTIONS CONTAINER - Container -->
						<div id="vapoptioncont">
							<?php foreach ($res_opt as $ro) { ?>
								<div id="vaporow<?php echo $ro['id']; ?>" style="margin-bottom: 5px;">
									<span style="display: inline-block;width: 40%">
										<input type="text" readonly value="<?php echo $ro['name'].(strlen($ro['var_name']) ? ' - '.$ro['var_name'] : ''); ?>" style="width: 90% !important;">
									</span>
									<span style="display: inline-block;width: 15%">
										x<?php echo $ro['quantity']; ?>
									</span>
									<span style="display: inline-block;width: 20%">
										<?php echo VikAppointments::printPriceCurrencySymb($ro['inc_price']); ?>
									</span>
									<a href="javascript: void(0);" style="display: inline-block;width: 18%;" onClick="removeOptionRow(<?php echo $ro['id']; ?>)">
										<i class="fa fa-trash big"></i>
									</a>
									<input type="hidden" id="vapoprice<?php echo $ro['id']; ?>" value="<?php echo $ro['inc_price']; ?>" />
								</div>
								<?php $last_id = $ro['id']; ?>
								
							<?php } ?>
						</div>

					<?php echo $vik->closeEmptyFieldset(); ?>
				</div>

			<?php } ?>

		<?php echo $vik->bootEndTab(); ?>

		<!-- DESCRIPTION -->

		<?php echo $vik->bootAddTab('reservation', 'reservation_notes', JText::_('VAPMANAGERESERVATIONTITLE4')); ?>
	
			<?php echo $this->loadTemplate('notes'); ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id_employee" value="<?php echo $sel['id_employee']; ?>" />
	<input type="hidden" name="id_service" value="<?php echo $sel['id_service']; ?>" />
	<input type="hidden" name="checkin_ts" value="<?php echo $sel['checkin_ts']; ?>" />
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />

	<?php if ($this->from) { ?>
		<input type="hidden" name="from" value="<?php echo $this->from; ?>" />
	<?php } ?>
</form>

<div id="dialog-confirm" title="<?php echo JText::_('VAPWLNOTIFYMODALTITLE');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-mail-closed" style="float: left; margin: 0 7px 20px 0;"></span>
		<span><?php echo JText::_('VAPWLNOTIFYMODALCONTENT'); ?></span>
	</p>
</div>

<?php
$query = array(
	'id_service' 	=> $sel['id_service'],
	'id_employee' 	=> $sel['id_employee'],
	'checkin' 		=> $sel['checkin_ts'],
	'people' 		=> $sel['people'],
	'uid'			=> $sel['id_user'],
);

echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-ratestest',
	array(
		'title'       => JText::_('VAPMANAGESPECIALRATES'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 60,
		'modalWidth'  => 60,
		'url'		  => 'index.php?option=com_vikappointments&task=ratestest&tmpl=component&layout=quick&' . http_build_query($query),
	)
);
?>

<script>

	var BILLING_USERS_POOL = {};

	var OPTIONS_FIELDS_POOL = {};

	var PAYMENT_CHARGE = <?php echo $selected_charge; ?>;

	var opt_cont = <?php echo ($last_id + 1); ?>;
	
	jQuery(document).ready(function(){
		
		<?php if (count($options)) { ?>
			optionValueChanged();
		<?php } ?>
		
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

		jQuery('#vapoptionselect, #vapoptvar').select2({
			allowClear: false,
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
	
	function changeReservationValues() {
		document.location.href = 'index.php?option=com_vikappointments&task=findreservation&id_emp=<?php echo $sel['id_employee']; ?>&id_ser=<?php echo $sel['id_service']; ?>&id_res=<?php echo $sel['id']; ?>&last_day=<?php echo $sel['day_ts']; ?>';
	}
	
	function composeMailFields() {
		var email = jQuery('#vapemail').val();
		jQuery('.vapemailfield').val(email);
	}
	
	function composePhoneFields() {
		var phone = jQuery('#vapphone').val();
		jQuery('.vapphonefield').val(phone);
	}
	
	function optionValueChanged() {
		
		var id_opt = jQuery('#vapoptionselect').val();

		// check for the cached fields of the option
		if (OPTIONS_FIELDS_POOL.hasOwnProperty(id_opt)) {
			setOptionFieldValue(OPTIONS_FIELDS_POOL[id_opt]);
			return;
		}

		setOptionFieldEnabled(false);
		
		jQuery.noConflict();
			
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=get_option_details&tmpl=component",
			data: {
				id_opt: id_opt
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0]) {
				setOptionFieldEnabled(true);
				setOptionFieldValue(obj[1]);

				// cache option fields
				OPTIONS_FIELDS_POOL[id_opt] = obj[1];
			} else {
				setOptionFieldEnabled(true);
				alert(obj[1]);
			}
			
		});
		
	}
	
	function setOptionFieldEnabled(status) {
		jQuery('#vapoprice').prop('readonly', !status);
		jQuery('#vapoquant').prop('readonly', !status);
		jQuery('#vapobutton').prop('disabled', !status);
	}
	
	function setOptionFieldValue(arr) {

		var _vars_html = "";
		var _value = 0;
		for (var i = 0; i < arr.variations.length; i++) {
			var _price = parseFloat(arr.variations[i].price) + parseFloat(arr.price);

			_vars_html += '<option value="'+arr.variations[i].id+'" data-price="'+_price+'">'+arr.variations[i].name+'</option>\n';

			if (i == 0) {
				_value = arr.variations[i].id;
			}
		}
		jQuery('#vapoptvar').html(_vars_html);
		jQuery('#vapoptvar').select2('val', _value);

		if (_vars_html.length > 0) {
			jQuery('.vap-option-vars').show();
		} else {
			jQuery('.vap-option-vars').hide();
		}

		jQuery('#vapoprice').val(arr.price);
		jQuery('#vapoquant').val(1);

		if (arr.single == 0) {
			jQuery('#vapoquant').prop('readonly', true);
		}
	}

	function variationValueChanged() {
		var _price = parseFloat(jQuery('#vapoptvar :selected').attr('data-price'));
		jQuery('#vapoprice').val(_price.toFixed(2));
	}
	
	function addSelectedOption() {
		var id_opt = jQuery('#vapoptionselect').val();
		var id_var = jQuery('#vapoptvar').val();
		var price = jQuery('#vapoprice').val();
		var quant = jQuery('#vapoquant').val();
		var name = jQuery('#vapoptionselect :selected').text();
		var var_name = jQuery('#vapoptvar :selected').text();
		
		jQuery('#vapoptioncont').append(
			'<div id="vaporow'+opt_cont+'" style="margin-bottom: 5px;">\n'+
				'<span style="display: inline-block;width: 40%">\n'+
					'<input type="text" readonly value="'+name+(var_name.length ? " - "+var_name : '')+'" style="width:90% !important;"/>\n'+
				'</span>\n'+
				'<span style="display: inline-block;width: 15%">\n'+
					'x' + quant + '\n'+
				'</span>\n'+
				'<span style="display: inline-block;width: 20%">\n'+
					Currency.getInstance().format(quant*price) + '\n'+
				'</span>\n'+
				'<a href="javascript: void(0);" style="display: inline-block;width: 18%;" onClick="removeOptionRow('+opt_cont+')">\n'+
					'<i class="fa fa-trash big"></i>\n'+
				'</a>\n'+
				'<input type="hidden" name="new_opt_id[]" value="'+id_opt+'" />\n'+
				'<input type="hidden" name="new_opt_var[]" value="'+(id_var ? id_var : -1)+'" />\n'+
				'<input type="hidden" name="new_opt_price[]" id="vapoprice'+opt_cont+'" value="'+price+'" />\n'+
				'<input type="hidden" name="new_opt_quant[]" value="'+quant+'" />\n'+
			'</div>\n'
		);
		
		jQuery('#vaprtotalcost').val( (parseFloat(jQuery('#vaprtotalcost').val())+(quant*price)).toFixed(2) );
		
		opt_cont++;
	}
	
	function removeOptionRow(id) {
		var price = 0;
		if( jQuery('#vapoprice'+id).length > 0 ) {
			price = jQuery('#vapoprice'+id).val();
		}
		
		var _p = parseFloat(jQuery('#vaprtotalcost').val())-parseFloat(price);
		
		jQuery('#vaprtotalcost').val( _p.toFixed(2) );

		jQuery('#vaporow'+id).remove();
		jQuery('#adminForm').append('<input type="hidden" name="del_opt_id[]" value="'+id+'" />');
	}

	var CANCELLATION_MODAL_OPEN = <?php echo (VikAppointments::isWaitingList(true) ? 0 : 1); ?>;
	
	function statusValueChanged() {
		if (jQuery('#vapstatussel').val() == 'CONFIRMED') {

			<?php if ($sel['status'] != 'CONFIRMED') { ?>
			
				jQuery('input[name="notifycust"]').prop('checked', true);
				
				<?php if ($sel['notify']) { ?>
					jQuery('input[name="notifyemp"]').prop('checked', true);
				<?php } ?>	

			<?php } ?>

		} else if (!CANCELLATION_MODAL_OPEN && jQuery('#vapstatussel').val() == 'CANCELED') {
			openCacellationDialogWL();

			CANCELLATION_MODAL_OPEN = true;
		}
	}

	function openCacellationDialogWL() {
		jQuery("#dialog-confirm").dialog({
			resizable: false,
			height: 180,
			modal: true,
			buttons: {
				"<?php echo JText::_('VAPYES'); ?>": function() {
					jQuery('#adminForm').append('<input type="hidden" name="notifywl" value="1"/>');

					jQuery( this ).dialog( "close" );
				},
				"<?php echo JText::_('VAPNO'); ?>": function() {
					jQuery( this ).dialog( "close" );
				}
			}
		});
	}

	// bootstrap modal

	function vapOpenJModal(id, url, jqmodal) {
		<?php echo $vik->bootOpenModalJS(); ?>
	}

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#reservation_"]').on('click', function() {
			var href = jQuery(this).attr('href').substr(1);
			jQuery('input[name="tabname"]').val(href);
		});

	});

	// validate

	var validator = new VikFormValidator('#adminForm');

	Joomla.submitbutton = function(task) {
		if (task.indexOf('save') !== -1) {
			if (validator.validate()) {
				Joomla.submitform(task, document.adminForm);	
			}
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}
	
</script>
