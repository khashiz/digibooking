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

$sel = $this->rate;

$vik = UIApplication::getInstance();

$editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

$config = UIFactory::getConfig();

?>

<form name="adminForm" id="adminForm" action="index.php" method="post">

	<?php echo $vik->bootStartTabSet('specialrate', array('active' => $this->tab)); ?>

	<!-- DETAILS -->

		<?php echo $vik->bootAddTab('specialrate', 'rate_options', JText::_('JGLOBAL_FIELDSET_BASIC')); ?>
	
			<div class="span6">
				<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND1'), 'form-horizontal'); ?>
					
					<!-- NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEGROUP2').'*:'); ?>
						<input type="text" name="name" class="required" value="<?php echo $sel['name']; ?>" size="30" />
					<?php echo $vik->closeControl(); ?>

					<!-- CHARGE - Number -->
					<?php
					$elements = array();
					$elements[] = JHtml::_('select.option', -1, '-');
					$elements[] = JHtml::_('select.option', 1, '+');
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT4').'*:'); ?>
						<select name="factor" id="vap-factor-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['charge'] > 0 ? 1 : -1); ?>
						</select>
						<input type="number" name="charge" class="required" value="<?php echo abs($sel['charge']); ?>" size="5" min="0" step="any" />
						&nbsp;<?php echo $config->get('currencysymb'); ?>
						<?php
						echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGEPAYMENT4'),
							'content'	=> JText::_('VAPSPECIALRATECHARGE_HELP'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

					<!-- PEOPLE - Number -->
					<?php
					$elements = array();
					$elements[] = JHtml::_('select.option', 0, JText::_('VAPIGNORE'));
					$elements[] = JHtml::_('select.option', 1, JText::_('VAPMANAGECONFIG97'));
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION25').':'); ?>
						<select name="enablepeople" id="vap-people-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['people'] > 0 ? 1 : 0); ?>
						</select>
						<input type="number" name="people" class="required" value="<?php echo $sel['people'] ? $sel['people'] : 1; ?>" size="5" min="1" step="1"
							style="<?php echo ($sel['people'] ? '' : 'display: none;'); ?>" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- PUBLISHED - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
					?> 
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT3').':'); ?>
						<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- WEEKDAYS - Number -->
					<?php
					$elements = array();
					$elements[] = JHtml::_('select.option', '', '');
					$elements[] = JHtml::_('select.option', 0, JText::_('SUNDAY'));
					$elements[] = JHtml::_('select.option', 1, JText::_('MONDAY'));
					$elements[] = JHtml::_('select.option', 2, JText::_('TUESDAY'));
					$elements[] = JHtml::_('select.option', 3, JText::_('WEDNESDAY'));
					$elements[] = JHtml::_('select.option', 4, JText::_('THURSDAY'));
					$elements[] = JHtml::_('select.option', 5, JText::_('FRIDAY'));
					$elements[] = JHtml::_('select.option', 6, JText::_('SATURDAY'));
					?>
					<?php echo $vik->openControl(JText::_('VAPWEEKDAYS').':'); ?>
						<select name="weekdays[]" id="vap-weekdays-sel" multiple>
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['weekdays']); ?>
						</select>
						<?php
						echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPWEEKDAYS'),
							'content'	=> JText::_('VAPSPECIALRATEWD_HELP'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

					<!-- START PUBLISHING - Calendar -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE29').':'); ?>
						<?php echo $vik->calendar($sel['fromdate'], 'fromdate', 'vap-startpub-date'); ?>
						<?php
						echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGESERVICE29'),
							'content'	=> JText::_('VAPSPECIALRATESTARTPUB_HELP'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

					<!-- END PUBLISHING - Calendar -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE30').':'); ?>
						<?php echo $vik->calendar($sel['todate'], 'todate', 'vap-endpub-date'); ?>
						<?php
						echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGESERVICE30'),
							'content'	=> JText::_('VAPSPECIALRATEENDPUB_HELP'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

					<!-- USE TIME FILTER - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['fromtime'] < $sel['totime'], 'onclick="useTimeValueChanged(1);"');
					$elem_no  = $vik->initRadioElement('', '', $sel['fromtime'] >= $sel['totime'], 'onclick="useTimeValueChanged(0);"');
					?> 
					<?php echo $vik->openControl(JText::_('VAPUSETIMEFILTER').':'); ?>
						<?php echo $vik->radioYesNo('usetime', $elem_yes, $elem_no, false); ?>
						<?php
						echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPUSETIMEFILTER'),
							'content'	=> JText::_('VAPSPECIALRATETIME_HELP'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

					<!-- FROM TIME - Select -->
					<?php
					$hours = array();
					for ($h = 0; $h <= 24; $h++)
					{
						$hours[] = JHtml::_('select.option', $h, ($h < 10 ? '0' : '') . $h);
					}
					$mins = array();
					for ($m = 0; $m < 60; $m += 5)
					{
						$mins[] = JHtml::_('select.option', $m, ($m < 10 ? '0' : '') . $m);
					}

					$control = array();
					$control['style'] = $sel['fromtime'] < $sel['totime'] ? '' : 'display: none;';
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE14').':', 'time-field', $control); ?>
						<select name="fromhour" class="hour-min-select">
							<?php echo JHtml::_('select.options', $hours, 'value', 'text', floor($sel['fromtime'] / 60)); ?>
						</select>
						<select name="frommin" class="hour-min-select">
							<?php echo JHtml::_('select.options', $mins, 'value', 'text', $sel['fromtime'] % 60); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- TO TIME - Select -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE15').':', 'time-field', $control); ?>
						<select name="tohour" class="hour-min-select">
							<?php echo JHtml::_('select.options', $hours, 'value', 'text', floor($sel['totime'] / 60)); ?>
						</select>
						<select name="tomin" class="hour-min-select">
							<?php echo JHtml::_('select.options', $mins, 'value', 'text', $sel['totime'] % 60); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- USER GROUPS - Select -->
					<?php
					$groups = array();
					$groups[] = JHtml::_('select.option', '', '');

					$groups = array_merge($groups, JHtml::_('user.groups', true));

					// remove hiphens used to create the tree structure of the groups
					$groups = array_map(function($group)
					{
						$group->text = preg_replace("/^(-\s?)+/", "", $group->text);

						return $group;
					}, $groups);
					?>
					<?php echo $vik->openControl(JText::_('VAPUSERGROUPS').':'); ?>
						<select name="usergroups[]" id="vap-usergroup-sel" multiple>
							<?php echo JHtml::_('select.options', $groups, 'value', 'text', $sel['usergroups']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- SERVICES - Select -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECOUPON17').':'); ?>
						<select name="services[]" id="vap-service-sel" multiple>
							<?php foreach ($this->services as $g) { ?>
								<?php if (strlen($g['name'])) { ?>
									<optgroup label="<?php echo $g['name']; ?>">
								<?php } ?>

								<?php foreach ($g['list'] as $s) { ?>
									<option value="<?php echo $s['id']; ?>" <?php echo (in_array($s['id'], $sel['services']) ? 'selected="selected"' : ''); ?>><?php echo $s['name']; ?></option>
								<?php } ?> 

								<?php if (strlen($g['name'])) { ?>
									</optgroup>
								<?php } ?>
							<?php } ?>
						</select>
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeFieldset(); ?>
			</div>

			<div class="span6">
				<?php echo $vik->openFieldset(JText::_('VAPMANAGEGROUP3'), 'form-horizontal'); ?>

					<div class="control-group">
						<textarea name="description" style="width: 80%;height: 180px;resize: vertical;"><?php echo $sel['description']; ?></textarea>
					</div>

				<?php echo $vik->closeFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- ADVANCED -->

		<?php echo $vik->bootAddTab('specialrate', 'rate_advanced', JText::_('JGLOBAL_FIELDSET_ADVANCED')); ?>

			<div class="span6">
				<?php echo $vik->openEmptyFieldset(); ?>

					<!-- TIMELINE CLASS -->
					<?php
					$class_sfx = '';

					if (!empty($sel['params']['class_sfx']))
					{
						$class_sfx = $sel['params']['class_sfx'];
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPRATETIMELINECLASS') . ':'); ?>
						<textarea name="params[class_sfx]" style="resize:vertical;"><?php echo $class_sfx; ?></textarea>
						
						<?php
						echo $vik->createPopover(array(
							'title'   => JText::_('VAPRATETIMELINECLASS'),
							'content' => JText::_('VAPRATETIMELINECLASS_HELP'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

					<!-- TIMELINE STYLE -->
					<?php
					$style_class = '';

					if (!empty($sel['params']['style_class']))
					{
						$style_class = $sel['params']['style_class'];
					}

					$options = array(
						JHtml::_('select.option', '', ''),
						JHtml::_('select.option', 'top-right-red-circle', JText::_('VAPRATETIMELINESTYLE_OPT1')),
						JHtml::_('select.option', 'purple-border', JText::_('VAPRATETIMELINESTYLE_OPT2')),
					);

					?>
					<?php echo $vik->openControl(JText::_('VAPRATETIMELINESTYLE') . ':'); ?>
						<select name="params[style_class]" id="vap-style-sel">
							<?php echo JHtml::_('select.options', $options, 'value', 'text', $style_class); ?>
						</select>
						
						<?php
						echo $vik->createPopover(array(
							'title'   => JText::_('VAPRATETIMELINESTYLE'),
							'content' => JText::_('VAPRATETIMELINESTYLE_HELP'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

					<!-- CALENDAR CLASS -->
					<?php
					$class_sfx = '';

					if (!empty($sel['params']['cal_class_sfx']))
					{
						$class_sfx = $sel['params']['cal_class_sfx'];
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPRATECALENDARCLASS') . ':'); ?>
						<textarea name="params[cal_class_sfx]" style="resize:vertical;"><?php echo $class_sfx; ?></textarea>
						
						<?php
						echo $vik->createPopover(array(
							'title'   => JText::_('VAPRATECALENDARCLASS'),
							'content' => JText::_('VAPRATECALENDARCLASS_HELP'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

					<!-- CALENDAR STYLE -->
					<?php
					$style_class = '';

					if (!empty($sel['params']['cal_style_class']))
					{
						$style_class = $sel['params']['cal_style_class'];
					}

					$options = array(
						JHtml::_('select.option', '', ''),
						JHtml::_('select.option', 'top-right-red-circle', JText::_('VAPRATETIMELINESTYLE_OPT1')),
					);

					?>
					<?php echo $vik->openControl(JText::_('VAPRATECALENDARSTYLE') . ':'); ?>
						<select name="params[cal_style_class]" id="vap-cal-style-sel">
							<?php echo JHtml::_('select.options', $options, 'value', 'text', $style_class); ?>
						</select>
						
						<?php
						echo $vik->createPopover(array(
							'title'   => JText::_('VAPRATECALENDARSTYLE'),
							'content' => JText::_('VAPRATECALENDARSTYLE_HELP'),
						));
						?>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />

</form>

<?php
JText::script('VAPEXPORTRES6');
JText::script('VAPIGNORE');
?>

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-factor-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 50
		});

		jQuery('.hour-min-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 75
		});

		jQuery('#vap-people-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 120
		});

		jQuery('#vap-weekdays-sel').select2({
			placeholder: Joomla.JText._('VAPEXPORTRES6'), // - ALL -
			allowClear: true,
			width: 300
		});

		jQuery('#vap-usergroup-sel').select2({
			placeholder: Joomla.JText._('VAPEXPORTRES6'), // - ALL -
			allowClear: true,
			width: 300
		});

		jQuery('#vap-service-sel').select2({
			placeholder: Joomla.JText._('VAPEXPORTRES6'), // - ALL -
			allowClear: true,
			width: 300
		});

		jQuery('#vap-style-sel, #vap-cal-style-sel').select2({
			placeholder: Joomla.JText._('VAPIGNORE'), // Ignore
			allowClear: true,
			width: 250
		});

		jQuery('#vap-people-sel').on('change', function() {
			if (parseInt(jQuery(this).val()) == 1) {
				jQuery('input[name="people"]').show();
			} else {
				jQuery('input[name="people"]').hide();
			}
		});

	});

	function useTimeValueChanged(is) {
		if (is) {
			jQuery('.time-field').show();
		} else {
			jQuery('.time-field').hide();
		}
	}


	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#rate_"]').on('click', function() {
			var href = jQuery(this).attr('href').substr(1);
			jQuery('input[name="tabname"]').val(href);
		});

	});

	// validate

	var validator = new VikFormValidator('#adminForm');

	Joomla.submitbutton = function(task) {
		if (task.indexOf('save') !== -1) {
			if (validator.validate(extendValidation)) {
				Joomla.submitform(task, document.adminForm);	
			}
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}

	function extendValidation() {
		var input 	= jQuery('input[name="charge"]');
		var val 	= parseInt(input.val());

		if (val > 0) {
			validator.unsetInvalid(input);
			return true;
		}
		
		validator.setInvalid(input);
		return true;
	}
	
</script>
