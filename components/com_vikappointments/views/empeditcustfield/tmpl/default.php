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

$auth       = $this->auth;
$employee   = $auth->getEmployee();

$custfield = $this->customField;

$type = $custfield['id'] > 0 ? 2 : 1;

$vik = UIApplication::getInstance();

$itemid = JFactory::getApplication()->input->getInt('Itemid');

?>
	
<?php
/**
 * The employees area toolbar is displayed from the layout below:
 * /components/com_vikappointments/layouts/emparea/toolbar.php
 * 
 * If you need to change something from this layout, just create
 * an override of this layout by following the instructions below:
 * - open the back-end of your Joomla
 * - visit the Extensions > Templates > Templates page
 * - edit the active template
 * - access the "Create Overrides" tab
 * - select Layouts > com_vikappointments > emparea
 * - start editing the toolbar.php file on your template to create your own layout
 *
 * @since 1.6
 */
echo JLayoutHelper::render('emparea.toolbar', array('active' => false));
?>

<div class="vapeditempheaderdiv">
	<div class="vapeditemptitlediv">
		<h2><?php echo JText::_($type == 2 ? 'VAPEDITEMPCUSTOMFTITLE' : 'VAPNEWEMPCUSTOMFTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manageCustomFields()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveCustomField(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
		
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveCustomField(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>

		<?php if ($auth->manageCustomFields() && $type == 2) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveCustomField();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseCustomFields();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditcustfield'); ?>" method="post" name="empareaForm" id="empareaForm">
	
	<div class="span6">
		<?php echo $vik->openEmptyFieldset(); ?>
			

			<?php echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF1').'*:'); ?>
				<input class="required" type="text" name="name" value="<?php echo $this->escape($custfield['name']); ?>" size="30" />
			<?php echo $vik->closeControl(); ?>
			
			<?php echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF2').'*:'); ?>
				<select name="type" id="vap-type-sel" class="required">
					<option value="text" <?php echo ($custfield['type'] == "text" ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCUSTOMFTYPEOPT1'); ?></option>
					<option value="textarea" <?php echo ($custfield['type'] == "textarea" ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCUSTOMFTYPEOPT2'); ?></option>
					<option value="number" <?php echo ($custfield['type'] == "number" ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCUSTOMFTYPEOPT8'); ?></option>
					<option value="date" <?php echo ($custfield['type'] == "date" ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCUSTOMFTYPEOPT3'); ?></option>
					<option value="select" <?php echo ($custfield['type'] == "select" ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCUSTOMFTYPEOPT4'); ?></option>
					<option value="checkbox" <?php echo ($custfield['type'] == "checkbox" ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCUSTOMFTYPEOPT5'); ?></option>
					<option value="separator" <?php echo ($custfield['type'] == "separator" ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCUSTOMFTYPEOPT6'); ?></option>
					<option value="file" <?php echo ($custfield['type'] == "file" ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCUSTOMFTYPEOPT7'); ?></option>
				</select>
			<?php echo $vik->closeControl(); ?>

			<?php
			$control = array();
			$control['style']    = $custfield['type'] == 'separator' ? 'display: none;' : '';
			$control['idparent'] = 'vap-rule-control';
			echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF10').':', '', $control); ?>
				<select name="rule" id="vap-rule-sel">
					<option></option>
					<?php for ($i = 1; $i <= 9; $i++) { ?>
						<option value="<?php echo $i; ?>" <?php echo ($i == $custfield['rule'] ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCUSTFIELDRULE' . $i); ?></option>
					<?php } ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<?php
			// hidden as previously declared
			$control['idparent'] = 'vap-required-control';
			echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF3').':', '', $control); ?>
				<select name="required" class="vik-dropdown-small">
					<option value="1" <?php echo ($custfield['required'] == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPYES'); ?></option>
					<option value="0" <?php echo ($custfield['required'] == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPNO'); ?></option>
				</select>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>

	<!-- RIGHT FIELDSETS -->

	<div class="span6" id="right-container">
		<?php echo $vik->openEmptyFieldset(); ?>

			<!-- SELECT type fieldset -->

			<div class="span12" id="vap-customf-select-box" style="<?php echo ($custfield['type'] == 'select' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openEmptyFieldset(); ?>

					<?php echo $vik->openControl(JText::_('VAPMULTIPLE').':'); ?>
						<select name="multiple" class="vik-dropdown-small">
							<option value="1" <?php echo ($custfield['multiple'] == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPYES'); ?></option>
							<option value="0" <?php echo ($custfield['multiple'] == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPNO'); ?></option>
						</select>
					<?php echo $vik->closeControl(); ?>

					<?php echo $vik->openControl(JText::_('VAPEMPCUSTOMFTYPEOPT4').':'); ?>
						<div id="vap-customf-select-choose">

							<?php
							$options_list = array_filter(explode(';;__;;', $custfield['choose']));
							foreach ($options_list as $i => $v) { ?>
								<div id="vapchoose<?php echo $i; ?>" class="vap-customf-choose">
									<span class="vap-sortable"></span>
									<input type="text" name="choose[]" value="<?php echo $v; ?>" size="40" />
									<a href="javascript: void(0)" onclick="removeElement(<?php echo $i; ?>);">
										<i class="fa fa-times big"></i>
									</a>
								</div>
							<?php } ?>

						</div>

						<div style="margin-top: 10px;">
							<button type="button" class="btn" onclick="addElement();">
								<?php echo JText::_('VAPEMPCUSTOMFADDANSWER'); ?>
							</button>
						</div>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

			<!-- NUMBER type fieldset -->

			<div class="span12" id="vap-customf-number-box" style="<?php echo ($custfield['type'] == 'number' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openEmptyFieldset(); ?>

					<?php
					$settings = $custfield['type'] == 'number' && strlen($custfield['choose']) ? json_decode($custfield['choose'], true) : array();
					?>

					<?php echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF12').':'); ?>
						<input type="number" name="number_min" value="<?php echo $settings['min']; ?>" size="30" step="any" />
					<?php echo $vik->closeControl(); ?>

					<?php echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF13').':'); ?>
						<input type="number" name="number_max" value="<?php echo $settings['max']; ?>" size="30" step="any" />
					<?php echo $vik->closeControl(); ?>

					<?php
					$elem_yes = $vik->initRadioElement('', '', !empty($settings['decimals']));
					$elem_no  = $vik->initRadioElement('', '',  empty($settings['decimals']));
					?>
					<?php echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF14').':'); ?>
						<select name="number_decimals" class="vik-dropdown-small">
							<option value="1" <?php echo (!empty($settings['decimals']) ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPYES'); ?></option>
							<option value="0" <?php echo (empty($settings['decimals']) ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPNO'); ?></option>
						</select>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

			<!-- CHECKBOX type fieldset -->

			<div class="span12" id="vap-customf-checkbox-box" style="<?php echo ($custfield['type'] == 'checkbox' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openEmptyFieldset(); ?>

					<?php echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF5').':'); ?>
						<input type="text" name="poplink" value="<?php echo $custfield['poplink']; ?>" size="40" /><br />
						<small>Ex. <i>index.php?option=com_content&view=article&id=#JoomlaArticleID#&tmpl=component</i></small>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

			<!-- FILE type fieldset -->

			<div class="span12" id="vap-customf-file-box" style="<?php echo ($custfield['type'] == 'file' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openEmptyFieldset(); ?>

					<?php echo $vik->openControl(JText::_('VAPEMPCUSTOMFFILEFILTER').':'); ?>
						<input type="text" placeholder="png, jpg, pdf" value="<?php echo $custfield['choose']; ?>" size="30" name="filters" />
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

			<!-- SEPARATOR type fieldset -->

			<div class="span12" id="vap-customf-separator-box" style="<?php echo ($custfield['type'] == 'separator' ? '' : 'display: none;'); ?>">
				<?php echo $vik->openEmptyFieldset(); ?>

					<?php echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF11').':'); ?>
						<input type="text" name="sep_suffix" value="<?php echo $custfield['choose']; ?>" size="30" />
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

			<!-- PHONE NUMBER rule fieldset -->

			<div class="span12" id="vap-rule-phone-box" style="<?php echo ($custfield['rule'] == 3 ? '' : 'display: none;'); ?>margin-left: 0px;">
				<?php echo $vik->openEmptyFieldset(); ?>

					<!-- DEFAULT PREFIX - Text -->
					<?php
					$code = $custfield['rule'] == 3 && !empty($custfield['choose']) ? $custfield['choose'] : 'US';
					?>
					<?php echo $vik->openControl(JText::_('VAPEMPMANAGECUSTOMF9').':'); ?>
						<select name="def_prfx" id="vap-country-sel">
							<option></option>
							<?php foreach ($this->countries as $country) { ?>

								<option value="<?php echo $country['country_2_code']; ?>" <?php echo ($code == $country['country_2_code'] ? 'selected="selected"' : ''); ?>><?php echo $country['country_name']; ?></option>

							<?php } ?>
						</select>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" />
	
	<input type="hidden" name="id" value="<?php echo $custfield['id']; ?>" />
	<input type="hidden" name="task" value="empeditcustfield.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<?php
JText::script('VAPCONFDIALOGMSG');
JText::script('VAPEMPCUSTFIELDRULE0');
?>

<script>
	
	function vapCloseCustomFields() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empcustfields&Itemid=' . $itemid, false); ?>';
	}

	var validator = new VikFormValidator('#empareaForm');
	
	<?php if ($auth->manageCustomFields()) { ?>

		function vapSaveCustomField(close) {
			
			if (validator.validate()) {
				if (close) {
					jQuery('#vaphiddenreturn').val('1');
				}
				
				document.empareaForm.submit();
			}
		}

	<?php } ?>
	
	<?php if ($auth->manageCustomFields() && $type == 2) { ?>

		function vapRemoveCustomField() {

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditcustfield.delete&cid[]=' . $custfield['id'] . '&Itemid=' . $itemid, false); ?>';
		}

	<?php } ?>
	
	jQuery(document).ready(function(){

		jQuery("#vap-type-sel").select2({
			allowClear: false,
			width: 300
		});

		jQuery("#vap-rule-sel").select2({
			placeholder: Joomla.JText._('VAPEMPCUSTFIELDRULE0'),
			allowClear: true,
			width: 300
		});

		jQuery('#vap-country-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery(".vik-dropdown-small").select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery('#vap-type-sel').on('change', function() {
			var val = jQuery(this).val();

			jQuery('#vap-rule-sel').find('option').prop('disabled', false);
			jQuery('#right-container div[class^="span"]').hide();

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
			jQuery('#vap-rule-control, #vap-required-control').show();
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
	
</script>
