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

$sel = $this->field;

$vik = UIApplication::getInstance();

?>

<?php if ($sel['id'] != -1 && VikAppointments::isMultilanguage(true)) { ?>

	<div class="btn-toolbar" id="filter-bar">
		<div class="btn-group pull-right">
			<?php foreach (VikAppointments::getKnownLanguages() as $lang) { 
				$lang_name = explode('-', $lang);
				$lang_name = $lang_name[1];
				?>
				<button type="button" class="btn" onClick="SELECTED_TAG='<?php echo $lang; ?>';vapOpenJModal('langtag', null, true);">
					<i class="icon">
						<img src="<?php echo VAPASSETS_URI . 'css/flags/' . strtolower($lang_name) . '.png'; ?>" />
					</i>
					&nbsp;<?php echo $lang; ?>
				</button>
			<?php } ?>
		</div>
	</div>

<?php } ?>
	
<form name="adminForm" action="index.php" method="post" id="adminForm">
		
	<div class="span6">
		<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND1'), 'form-horizontal'); ?>

			<?php if ($sel['id'] <= 0) { ?>

				<!-- GROUP - Select -->
				<?php
				$elements = array(
					JHtml::_('select.option', 0, JText::_('VAPMENUCUSTOMERS')),
					JHtml::_('select.option', 1, JText::_('VAPMENUEMPLOYEES')),
				);
				?>
				<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE10').'*:'); ?>
					<select name="group" id="vap-group-sel">
						<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['group']); ?>
					</select>
					<?php echo $vik->createPopover(array(
						'title' 	=> JText::_('VAPMANAGESERVICE10'),
						'content' 	=> JText::_('VAPCFGROUP_DESC'),
					)); ?>
				<?php echo $vik->closeControl(); ?>

			<?php } else  { ?>

				<input type="hidden" name="group" value="<?php echo $sel['group']; ?>" />

			<?php } ?>
		
			<!-- NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF1').'*:'); ?>
				<input class="required" type="text" name="name" value="<?php echo $sel['name']; ?>" size="30" />
			<?php echo $vik->closeControl(); ?>

			<!-- FORM NAME - Text -->
			<?php
			$groupEmpControl = array();
			$groupEmpControl['style'] = $sel['group'] != 1 ? 'display: none;' : '';
			/**
			 * Disable form name field if we are editing an existing custom field
			 * in order to avoid unexpected behaviors due to the created DB column(s).
			 *
			 * A popover is shown to explain why the field cannot be changed.
			 *
			 * @since 1.6.2
			 */
			echo $vik->openControl(JText::_('VAPMANAGECUSTOMF13').':', 'employees-field', $groupEmpControl); ?>
				<input type="text" name="formname" value="<?php echo $sel['formname']; ?>" size="30" <?php echo $sel['id'] >= 0 ? 'readonly="readonly"' : ''; ?> />
				<?php
				$popover = array();
				$popover['title'] = JText::_('VAPMANAGECUSTOMF13');

				if ($sel['id'] <= 0)
				{
					// display information about this field
					$popover['content']	= JText::_('VAPCFFORMNAME_DESC');
				}
				else
				{
					// explain why the field is disabled
					$popover['content'] = JText::_('VAPCFFORMNAMEDISABLED_HELP');
					$popover['icon']    = 'exclamation-circle';
				}

				echo $vik->createPopover($popover);
				?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- TYPE - Select -->
			<?php
			$elements = array(
				JHtml::_('select.option', 'text', 		JText::_('VAPCUSTOMFTYPEOPTION1')),
				JHtml::_('select.option', 'textarea', 	JText::_('VAPCUSTOMFTYPEOPTION2')),
				JHtml::_('select.option', 'number', 	JText::_('VAPCUSTOMFTYPEOPTION8')),
				JHtml::_('select.option', 'date', 		JText::_('VAPCUSTOMFTYPEOPTION3')),
				JHtml::_('select.option', 'select', 	JText::_('VAPCUSTOMFTYPEOPTION4')),
				JHtml::_('select.option', 'checkbox', 	JText::_('VAPCUSTOMFTYPEOPTION5')),
				JHtml::_('select.option', 'file', 		JText::_('VAPCUSTOMFTYPEOPTION7')),
				JHtml::_('select.option', 'separator', 	JText::_('VAPCUSTOMFTYPEOPTION6')),
			);
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF2').':'); ?>
				<select name="type" id="vap-type-sel">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['type']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- RULE - Dropdown -->
			<?php
			$elements = array();
			$elements[] = JHtml::_('select.option', '', '');

			for ($i = 1; $i <= 9; $i++)
			{
				$elements[] = JHtml::_('select.option', $i, JText::_('VAPCUSTFIELDRULE' . $i));
			}

			$ruleControl = array();
			$ruleControl['style']    = $sel['type'] == 'separator' || $sel['group'] != 0 ? 'display: none;' : '';
			$ruleControl['idparent'] = 'vap-rule-control';
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF12').':', 'customers-field', $ruleControl); ?>
				<select name="rule" id="vap-rule-sel">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['rule']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
			<!-- REQUIRED - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $sel['required'] == 1);
			$elem_no  = $vik->initRadioElement('', '', $sel['required'] == 0);

			$reqControl = array();
			$reqControl['style']    = $sel['type'] == 'separator' ? 'display: none;' : '';
			$reqControl['idparent'] = 'vap-required-control';
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF3').':', '', $reqControl); ?>
				<?php echo $vik->radioYesNo('required', $elem_yes, $elem_no, false); ?>
			<?php echo $vik->closeControl(); ?>

			<!-- EMPLOYEE - Dropdown -->
			<?php
			$elements = array();
			$elements[] = JHtml::_('select.option', '', '');
			foreach ($this->employees as $e)
			{
				$elements[] = JHtml::_('select.option', $e['id'], $e['nickname']);
			}

			$groupCustControl = array();
			$groupCustControl['style'] = $sel['group'] != 0 ? 'display: none;' : '';
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF10').':', 'customers-field', $groupCustControl); ?>
				<select name="id_employee" id="vap-employees-sel">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['id_employee']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- SERVICES - Dropdown -->
			<?php echo $vik->openControl(JText::_('VAPMENUSERVICES').':', 'customers-field', $groupCustControl); ?>
				<select name="id_services[]" id="vap-services-sel" multiple>
					<?php foreach ($this->services as $g) { ?>
						<?php if (strlen($g['name'])) { ?>
							<optgroup label="<?php echo $g['name']; ?>">
						<?php } ?>

						<?php foreach ($g['list'] as $s) { ?>
							<option value="<?php echo $s['id']; ?>" <?php echo (in_array($s['id'], $this->assignedServices) ? 'selected="selected"' : ''); ?>><?php echo $s['name']; ?></option>
						<?php } ?> 

						<?php if (strlen($g['name'])) { ?>
							</optgroup>
						<?php } ?>
					<?php } ?>
				</select>
			<?php echo $vik->closeControl(); ?>
		
		<?php echo $vik->closeFieldset(); ?>
	</div>

	<!-- RIGHT FIELDSETS -->

	<div class="span6" id="right-container">
		<?php echo $vik->openEmptyFieldset(); ?>

			<!-- TEXTAREA type fieldset -->

			<div class="span12" id="vap-customf-textarea-box" style="<?php echo ($sel['type'] == 'textarea' && $sel['group'] == 1 ? '' : 'display: none;'); ?>">
				<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND2'), 'form-horizontal'); ?>

					<?php
					$settings = $sel['type'] == 'textarea' && strlen($sel['choose']) ? json_decode($sel['choose'], true) : array();

					$elem_yes = $vik->initRadioElement('', '', !empty($settings['editor']));
					$elem_no  = $vik->initRadioElement('', '', empty($settings['editor']));
					
					echo $vik->openControl(JText::_('VAPUSEEDITOR') . ':');
					echo $vik->radioYesNo('use_editor', $elem_yes, $elem_no, false);
					echo $vik->createPopover(array(
						'title'   => JText::_('VAPUSEEDITOR'),
						'content' => JText::_('VAPUSEEDITOR_DESC'),
					));
					echo $vik->closeControl();
					?>
						
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeFieldset(); ?>
			</div>

			<!-- SELECT type fieldset -->

			<div class="span12" id="vap-customf-select-box" style="<?php echo ($sel['type'] == 'select' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND2'), 'form-horizontal'); ?>

					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['multiple'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['multiple'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMULTIPLE').':'); ?>
						<?php echo $vik->radioYesNo('multiple', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>

					<?php echo $vik->openControl(JText::_('VAPCUSTOMFTYPEOPTION4').':'); ?>
						<div id="vap-customf-select-choose">

							<?php
							$options_list = array_filter(explode(';;__;;', $sel['choose']));
							foreach ($options_list as $i => $v)
							{
								if (!empty($v)) { ?>
									<div id="vapchoose<?php echo $i; ?>" class="vap-customf-choose">
										<span class="vap-sortable"></span>
										<input type="text" name="choose[]" value="<?php echo $v; ?>" size="40" />
										<a href="javascript: void(0)" onclick="removeElement(<?php echo $i; ?>);">
											<i class="fa fa-times big"></i>
										</a>
									</div>
								<?php }
							} ?>

						</div>

						<div style="margin-top: 10px;">
							<button type="button" class="btn" onclick="addElement();">
								<?php echo JText::_('VAPCUSTOMFSELECTADDANSWER'); ?>
							</button>
						</div>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeFieldset(); ?>
			</div>

			<!-- NUMBER type fieldset -->

			<div class="span12" id="vap-customf-number-box" style="<?php echo ($sel['type'] == 'number' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND2'), 'form-horizontal'); ?>

					<?php
					$settings = $sel['type'] == 'number' && strlen($sel['choose']) ? json_decode($sel['choose'], true) : array();

					$min = isset($settings['min']) ? $settings['min'] : '';
					$max = isset($settings['max']) ? $settings['max'] : '';
					?>

					<?php echo $vik->openControl(JText::_('VAPMINVAL').':'); ?>
						<input type="number" name="number_min" value="<?php echo $min; ?>" size="30" step="any" />
					<?php echo $vik->closeControl(); ?>

					<?php echo $vik->openControl(JText::_('VAPMAXVAL').':'); ?>
						<input type="number" name="number_max" value="<?php echo $max; ?>" size="30" step="any" />
					<?php echo $vik->closeControl(); ?>

					<?php
					$elem_yes = $vik->initRadioElement('', '', !empty($settings['decimals']));
					$elem_no  = $vik->initRadioElement('', '',  empty($settings['decimals']));
					?>
					<?php echo $vik->openControl(JText::_('VAPALLOWDECIMALS').':'); ?>
						<?php echo $vik->radioYesNo('number_decimals', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeFieldset(); ?>
			</div>

			<!-- CHECKBOX type fieldset -->

			<div class="span12" id="vap-customf-checkbox-box" style="<?php echo ($sel['type'] == 'checkbox' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND2'), 'form-horizontal'); ?>

					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF5').':'); ?>
						<input type="text" name="poplink" value="<?php echo $sel['poplink']; ?>" size="40" />
						<?php
						echo $vik->createPopover(array(
							'title'   => JText::_('VAPMANAGECUSTOMF5'),
							'content' => JText::_('VAPMANAGECUSTOMF5_DESC'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeFieldset(); ?>
			</div>

			<!-- FILE type fieldset -->

			<div class="span12" id="vap-customf-file-box" style="<?php echo ($sel['type'] == 'file' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND2'), 'form-horizontal'); ?>

					<?php echo $vik->openControl(JText::_('VAPCUSTOMFFILEFILTER').':'); ?>
						<input type="text" placeholder="png, jpg, pdf" value="<?php echo $sel['choose']; ?>" size="30" name="filters" />
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeFieldset(); ?>
			</div>

			<!-- SEPARATOR type fieldset -->

			<div class="span12" id="vap-customf-separator-box" style="<?php echo ($sel['type'] == 'separator' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND2'), 'form-horizontal'); ?>

					<?php echo $vik->openControl(JText::_('VAPSUFFIXCLASS').':'); ?>
						<input type="text" name="sep_suffix" value="<?php echo $sel['choose']; ?>" size="30" />
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeFieldset(); ?>
			</div>

			<!-- PHONE NUMBER rule fieldset -->

			<div class="span12" id="vap-rule-phone-box" style="<?php echo ($sel['rule'] == 3 ? '' : 'display: none;'); ?>margin-left: 0px;">
				<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND3'), 'form-horizontal'); ?>

					<!-- DEFAULT PREFIX - Text -->
					<?php
					$code = $sel['rule'] == 3 && !empty($sel['choose']) ? $sel['choose'] : 'US';

					$options = array();
					$options[] = JHtml::_('select.option', '', '');

					foreach ($this->countries as $country)
					{
						$options[] = JHtml::_('select.option', $country['country_2_code'], $country['country_name']);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF9').':'); ?>
						<select name="def_prfx" id="vap-country-sel">
							<?php echo JHtml::_('select.options', $options, 'value', 'text', $code); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeFieldset(); ?>
			</div>

		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
			
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />

</form>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-langtag',
	array(
		'title'       => JText::_('VAPCUSTOMFLANGTRANSLATION') . '<span id="tag-target"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);

JText::script('VAPCUSTFIELDRULE0');
JText::script('VAPMANAGECUSTOMF11');
?>

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-group-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300
		});

		jQuery('#vap-type-sel').select2({
			allowClear: false,
			width: 300
		});

		jQuery('#vap-rule-sel').select2({
			placeholder: Joomla.JText._('VAPCUSTFIELDRULE0'),
			allowClear: true,
			width: 300
		});

		jQuery('#vap-country-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('#vap-employees-sel').select2({
			placeholder: Joomla.JText._('VAPMANAGECUSTOMF11'),
			allowClear: true,
			width: 300
		});

		jQuery('#vap-services-sel').select2({
			placeholder: Joomla.JText._('VAPMANAGECUSTOMF11'),
			allowClear: true,
			width: 300
		});

		jQuery('#vap-group-sel').on('change', function() {
			var group = jQuery(this).val();

			if (group == 0) {
				// customers active
				jQuery('.customers-field').show();
			}
			else {
				// customers unactive
				jQuery('.customers-field').hide();
			}

			if (group == 1) {
				// employees active
				jQuery('.employees-field').show();
			} else {
				// employees unactive
				jQuery('.employees-field').hide();
			}

			jQuery('#vap-rule-sel').select2('val', 0);
			jQuery('#vap-rule-sel').trigger('change');
			jQuery('#vap-type-sel').trigger('change');
		});

		jQuery('#vap-type-sel').on('change', function() {
			var val = jQuery(this).val();

			jQuery('#vap-rule-sel').find('option').prop('disabled', false);
			jQuery('#right-container div[class^="span"]').hide();

			isTextareaValueChanged(val == 'textarea');
			isSelectValueChanged(val == 'select');
			isCheckboxValueChanged(val == 'checkbox');
			isFileValueChanged(val == 'file');
			isNumberValueChanged(val == 'number');
			isDateValueChanged(val == 'date');
			isSeparatorValueChanged(val == 'separator');

			jQuery('#vap-rule-sel').trigger('change');
		});

		jQuery('#vap-rule-sel').on('change', function(){
			var val = jQuery(this).val();

			isPhoneValueChanged(val == 3);
		});

		jQuery('#vap-type-sel').trigger('change');

		makeSortable();

	});

	// select handler

	var CHOOSE_COUNT = <?php echo count($options_list); ?>;

	function addElement() {
		jQuery('#vap-customf-select-choose').append('<div id="vapchoose'+CHOOSE_COUNT+'" class="vap-customf-choose">\n'+
			'<span class="vap-sortable"></span>\n'+
			'<input type="text" name="choose[]" value="" size="40"/>\n'+
			'<a href="javascript: void(0)" onclick="removeElement('+CHOOSE_COUNT+');">\n'+
				'<i class="fa fa-times big"></i>\n'+
			'</a>\n'+
		'</div>\n');

		CHOOSE_COUNT++;

		makeSortable();
	}

	function removeElement(id) {
		jQuery('#vapchoose'+id).remove();
	}

	function makeSortable() {
		jQuery("#vap-customf-select-choose").sortable({
			revert: true
		});
	}

	// type boxes

	function isTextareaValueChanged(is) {
		if (is && (<?php echo $sel['group']; ?> == 1 && jQuery('#vap-group-sel').val() != 0)) {
			jQuery('#vap-customf-textarea-box').show();
		}
	}

	function isSelectValueChanged(is) {
		if (is) {
			jQuery('#vap-customf-select-box').show();
		}
	}

	function isCheckboxValueChanged(is) {
		if (is) {
			jQuery('#vap-customf-checkbox-box').show();
			
			// enable/disable all rules
			jQuery('#vap-rule-sel').find('option').prop('disabled', true);
			jQuery('#vap-rule-sel').select2('val', '');
		}
	}

	function isFileValueChanged(is) {
		if (is) {
			jQuery('#vap-customf-file-box').show();

			// enable/disable all rules
			jQuery('#vap-rule-sel').find('option').prop('disabled', true);
			jQuery('#vap-rule-sel').select2('val', '');
		}
	}

	function isNumberValueChanged(is) {
		if (is) {
			jQuery('#vap-customf-number-box').show();

			// enable/disable all rules
			jQuery('#vap-rule-sel').find('option').prop('disabled', true);
			jQuery('#vap-rule-sel').select2('val', '');
		}
	}

	function isDateValueChanged(is) {
		if (is) {
			// enable/disable all rules
			jQuery('#vap-rule-sel').find('option').prop('disabled', true);
			jQuery('#vap-rule-sel').select2('val', '');
		}
	}

	function isSeparatorValueChanged(is) {
		if (is) {
			jQuery('#vap-rule-control, #vap-required-control').hide();
			jQuery('#vap-customf-separator-box').show();

			jQuery('#vap-rule-sel').find('option').prop('disabled', true);
			jQuery('#vap-rule-sel').select2('val', '');
		} else {
			<?php
			if ($sel['group'] == 0)
			{
				// never turn on RULE in case of custom field for employees
				?>
				if (jQuery('#vap-group-sel').length == 0 || jQuery('#vap-group-sel').val() == 0) {
					// customers group active
					jQuery('#vap-rule-control').show();
				}	
				<?php	
			}
			?>

			jQuery('#vap-required-control').show();
		}
	}

	// rule boxes

	function isPhoneValueChanged(is) {
		if (is) {
			jQuery('#vap-rule-phone-box').show();
		} else {
			jQuery('#vap-rule-phone-box').hide();
		}
	}

	// translation
	
	var SELECTED_TAG = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'langtag') {
			url = 'index.php?option=com_vikappointments&task=managelangcustomf&id_customf=<?php echo $sel['id']; ?>&tag=' + SELECTED_TAG;

			jQuery('#tag-target').text(' ' + SELECTED_TAG);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

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
