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

// load calendar behavior
JHtml::_('behavior.calendar');
ArasJoomlaVikApp::datePicker();
$sel = $this->coupon;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');

$df_joomla = $vik->jdateFormat($date_format);

if ($sel['dstart'] != -1)
{
	/**
	 * Convert timestamps using native date() to avoid
	 * timezone issues, as integer dates are always treated as UTC.
	 *
	 * @since 1.6.3
	 */
	$sel['dstart'] = ArasJoomlaVikApp::jdate($date_format, $sel['dstart']);
	$sel['dend']   = ArasJoomlaVikApp::jdate($date_format, $sel['dend']);
}
else
{
	$sel['dstart'] = $sel['dend'] = '';
}

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">

	<?php echo $vik->bootStartTabSet('coupon', array('active' => $this->tab)); ?>

		<!-- DETAILS -->
				
		<?php echo $vik->bootAddTab('coupon', 'coupon_details', JText::_('VAPMANAGECOUPONFIELDSET1')); ?>
	
			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					
					<!-- GROUP - Select -->
					<?php
					if (count($this->groups))
					{
						$elements = array();
						$elements[] = JHtml::_('select.option', '', '');

						foreach ($this->groups as $group)
						{
							$elements[] = JHtml::_('select.option', $group['id'], $group['name']);
						}

						?>
						<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE10').':'); ?>
							<select name="id_group" id="vap-group-sel">
								<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['id_group']); ?>
							</select>
						<?php echo $vik->closeControl(); ?>
					<?php } ?>

					<!-- COUPON CODE - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON2').'*:'); ?>
						<input type="text" name="code" class="required" value="<?php echo $sel['code']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- TYPE CODE - Select -->
					<?php 
					$elements = array(
						JHtml::_('select.option', 1, JText::_('VAPCOUPONTYPEOPTION1')),
						JHtml::_('select.option', 2, JText::_('VAPCOUPONTYPEOPTION2')),
					);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON3').':'); ?>
						<select name="type" id="vap-gift-select">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['type']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- MAX QUANTITY - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON13').':'); ?>
						<input type="number" name="max_quantity" value="<?php echo $sel['max_quantity']; ?>" size="40" min="1" max="99999999" id="vap-maxq-field"
							<?php echo ($sel['type'] == 1 ? 'readonly' : ''); ?> />
					<?php echo $vik->closeControl(); ?>

					<!-- USED QUANTITY - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON14').':'); ?>
						<input type="number" name="used_quantity" value="<?php echo $sel['used_quantity']; ?>" size="40" min="1" max="99999999" />
					<?php echo $vik->closeControl(); ?>

					<!-- REMOVE GIFT - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['remove_gift'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['remove_gift'] == 0);

					$giftControl = array();
					$giftControl['style'] = $sel['type'] == 1 ? 'display: none;' : '';
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON15').':', 'vap-gift-child', $giftControl); ?>
						<?php echo $vik->radioYesNo('remove_gift', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- PERCENT OR TOTAL - Select -->
					<?php 
					$elements = array(
						JHtml::_('select.option', 1, JText::_('VAPCOUPONPERCENTOTOPTION1')),
						JHtml::_('select.option', 2, $config->get('currencysymb')),
					);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON4').':'); ?>
						<select name="percentot" id="vap-percentot-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['percentot']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>
					
					<!-- VALUE - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON5').':'); ?>
						<input type="number" name="value" value="<?php echo $sel['value']; ?>" size="40" min="0" max="999999" step="any" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- MINIMUM COST - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON6').':'); ?>
						<input type="number" name="mincost" value="<?php echo $sel['mincost']; ?>" size="40" min="0" max="999999" step="any" />
					<?php echo $vik->closeControl(); ?>

					<!-- PUBLISHING MODE - Select -->
					<?php 
					$elements = array(
						JHtml::_('select.option', 1, JText::_('VAPCOUPONPUBMODEOPT1')),
						JHtml::_('select.option', 2, JText::_('VAPCOUPONPUBMODEOPT2')),
					);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON21').':'); ?>
						<select name="pubmode" id="vap-pubmode-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['pubmode']); ?>
						</select>
						<?php
						echo $vik->createPopover(array(
							'title'   => JText::_('VAPMANAGECOUPON21'),
							'content' => JText::_('VAPMANAGECOUPON21_DESC'),
						));
						?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- START DATE - Calendar -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON7').':'); ?>
						<?php echo $vik->calendar($sel['dstart'], 'dstart', 'dstart'); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- END DATE - Calendar -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON8').':'); ?>
						<?php echo $vik->calendar($sel['dend'], 'dend', 'dend'); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- LAST MINUTE - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['lastminute'] > 0, 'onClick="displayLastMinuteAmount(1);"');
					$elem_no  = $vik->initRadioElement('', '', $sel['lastminute'] == 0, 'onClick="displayLastMinuteAmount(0);"');
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON10').':'); ?>
						<?php echo $vik->radioYesNo('islastminute', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- LAST MINUTE AMOUNT - Number -->
					<?php
					$lmControl = array();
					$lmControl['style'] = $sel['lastminute'] == 0 ? 'display: none;' : '';
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON11').':', 'vap-lastminute-child', $lmControl); ?>
						<input type="number" name="lastminute" id="vap-lastminute-input" value="<?php echo $sel['lastminute']; ?>" size="40" min="0" max="9999" />
						&nbsp;<?php echo JText::_('VAPFORMATHOURS'); ?>
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- ASSIGNMENTS -->

		<?php echo $vik->bootAddTab('coupon', 'coupon_assoc', JText::_('VAPFIELDSETASSOC')); ?>

			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>

					<!-- SERVICES LIST - Select -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON17').':'); ?>
						<select name="id_services[]" id="vap-services-select" multiple>
							<?php foreach ($this->services as $g) { ?>
								<?php if (strlen($g['name'])) { ?>
									<optgroup label="<?php echo $g['name']; ?>">
								<?php } ?>

								<?php foreach ($g['list'] as $s) { ?>
									<option value="<?php echo $s['id']; ?>" <?php echo (in_array($s['id'], $this->couponServicesAssocList) ? 'selected="selected"' : ''); ?>><?php echo $s['name']; ?></option>
								<?php } ?> 

								<?php if (strlen($g['name'])) { ?>
									</optgroup>
								<?php } ?>
							<?php } ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- EMPLOYEES LIST - Select -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON18').':'); ?>
						<select name="id_employees[]" id="vap-employees-select" multiple>
							<?php foreach ($this->employees as $g) { ?>
								<?php if (strlen($g['name'])) { ?>
									<optgroup label="<?php echo $g['name']; ?>">
								<?php } ?>

								<?php foreach ($g['list'] as $e) { ?>
									<option value="<?php echo $e['id']; ?>" <?php echo (in_array($e['id'], $this->couponEmployeesAssocList) ? 'selected="selected"' : ''); ?>><?php echo $e['name']; ?></option>
								<?php } ?> 

								<?php if (strlen($g['name'])) { ?>
									</optgroup>
								<?php } ?>
							<?php } ?>
						</select>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- NOTES -->

		<?php echo $vik->bootAddTab('coupon', 'coupon_note', JText::_('VAPMANAGECOUPON16')); ?>

			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>

					<div class="control-group">
						<textarea name="notes" style="width: 97%;height: 300px;"><?php echo $sel['notes']; ?></textarea>
					</div>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>     
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-gift-select').on('change', function() {
			if (jQuery(this).val() == '2') {
				jQuery('.vap-gift-child').show();
				jQuery('#vap-maxq-field').prop('readonly', false);
			} else {
				jQuery('.vap-gift-child').hide();
				jQuery('#vap-maxq-field').prop('readonly', true);
			}
		});

		jQuery('#vap-group-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300,
		});

		jQuery('#vap-gift-select, #vap-percentot-sel, #vap-pubmode-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery('#vap-services-select').select2({
			placeholder: '<?php echo addslashes(JText::_("VAPMANAGECOUPON19")); ?>',
			allowClear: true,
			width: 350,
		});

		jQuery('#vap-employees-select').select2({
			placeholder: '<?php echo addslashes(JText::_("VAPMANAGECOUPON20")); ?>',
			allowClear: true,
			width: 350,
		});

	});

	function displayLastMinuteAmount(is) {
		if (is) {
			jQuery('#vap-lastminute-input').val(24);
			jQuery('.vap-lastminute-child').show();
		} else {
			jQuery('#vap-lastminute-input').val(0);
			jQuery('.vap-lastminute-child').hide();
		}
	}

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#coupon_"]').on('click', function() {
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
