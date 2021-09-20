<?php
/** 
 * @package     VikAppointments
 * @subpackage  mod_vikappointments_search
 * @author      Matteo Galletti - e4j
 * @copyright   Copyright (C) 2018 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$last_values 	= $references['lastValues'];
$services 		= $references['services'];
$employees 		= $references['employees'];

$date_format = VikAppointments::getDateFormat();

$last_values['day'] = date($date_format, $last_values['day']);

$closing_periods = VikAppointments::getClosingPeriods();
$closing_days 	 = VikAppointments::getClosingDays();

$itemid = $params->get('itemid', null);

$randid = isset($module) && is_object($module) && property_exists($module, 'id') ? $module->id : rand(1, 999);

?>

<div class="moduletablevikapp vapmainsearchmod <?php echo $params->get('orientation', 'vertical'); ?>">

	<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=serviceslist' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="get" name="vapmodulesearch">
		<fieldset class="vapformfieldsetmod">
			
			<div class="vapsearchinputdivmod">
				<label class="vapsearchinputlabelmod" for="vapcalendarmod<?php echo $randid; ?>"><?php echo JText::_('VAPDATE'); ?></label>
				<div class="vapsearchentryinputmod">
					<input class="vapsearchdatemod" type="text" value="" id="vapcalendarmod<?php echo $randid; ?>" name="date" size="20" />
				</div>
			</div>

			<div class="vapsearchinputdivmod">
				<label class="vapsearchinputlabelmod" for="vapserselmod<?php echo $randid; ?>"><?php echo JText::_('VAPSERVICE'); ?></label>
				<div class="vapsearchentryselectmod">
					<select name="id_ser" id="vapserselmod<?php echo $randid; ?>" onChange="vapModServiceValueChanged(this, <?php echo $randid; ?>);">
						<?php
						foreach ($services as $group)
						{
							if (!empty($group['id']))
							{
								?>
								<optgroup label="<?php echo $group['name']; ?>">
								<?php
							}

							foreach ($group['list'] as $s)
							{
								?>
						 		<option
						 			value="<?php echo $s['id']; ?>"
						 			<?php echo $s['id'] == $last_values['id_ser'] ? 'selected="selected"' : ''; ?>
						 			data-url="<?php echo JRoute::_('index.php?option=com_vikappointments&view=servicesearch&id_ser=' . $s['id'] . ($itemid ? '&Itemid=' . $itemid : '')); ?>"
						 		>
						 			<?php echo $s['name']; ?>
						 		</option>
						 		<?php
						 	}
							
							if (!empty($group['id']))
							{
								?>
								</optgroup>
								<?php
							}
						}
						?>
					</select>
				</div>
			</div>

			<div class="vapsearchinputdivmod">
				<div id="vapemprowmod<?php echo $randid; ?>" style="<?php echo (!count($employees) ? 'display: none;' : ''); ?>">
					<label class="vapsearchinputlabelmod" for="vapempselmod<?php echo $randid; ?>"><?php echo JText::_('VAPEMPLOYEE'); ?></label>
					<div class="vapsearchentryselectmod">
						<select name="id_emp" id="vapempselmod<?php echo $randid; ?>">
							<?php
							foreach ($employees as $e)
							{
								?>
								<option value="<?php echo $e['id']; ?>" <?php echo $e['id'] == $last_values['id_emp'] ? 'selected="selected"' : ''; ?>><?php echo $e['name']; ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
			</div>

			<div class="vapsearchinputdivmod mod-booknow">
				<button type="submit" class="vapsearchsubmitmod"><?php echo JText::_('VAPFINDAPPOINTMENT'); ?></button>
			</div>
			
			<input type="hidden" value="com_vikappointments" name="option" />
			<input type="hidden" value="servicesearch" name="view" />

			<?php if ($itemid) { ?>
				<input type="hidden" value="<?php echo $itemid; ?>" name="Itemid" />
			<?php } ?>

		</fieldset>
	</form>

</div>

<script>

	var closingPeriods 	= <?php echo json_encode($closing_periods); ?>;
	var closingDays 	= <?php echo json_encode($closing_days); ?>;

	jQuery(document).ready(function() {

		jQuery('#vapcalendarmod<?php echo $randid; ?>').val('<?php echo $last_values["day"]; ?>');

		<?php if ((int) $params->get('advselect', 1)) { ?>

			jQuery('#vapserselmod<?php echo $randid; ?>, #vapempselmod<?php echo $randid; ?>').select2({
				allowClear: false,
				width: '100%'
			});

		<?php } ?>

		<?php if (count($services)) { ?>
			// trigger change the update form action using the first service
			vapModUpdateFormAction(jQuery('#vapserselmod<?php echo $randid; ?>'));
		<?php } ?>
	});

	var sel_format 	 = "<?php echo $date_format; ?>";
	var df_separator = sel_format[1];

	sel_format = sel_format.replace(new RegExp("\\"+df_separator, 'g'), "");

	if (sel_format == "Ymd") {
		Date.prototype.format = "yy"+df_separator+"mm"+df_separator+"dd";
	} else if (sel_format == "mdY") {
		Date.prototype.format = "mm"+df_separator+"dd"+df_separator+"yy";
	} else {
		Date.prototype.format = "dd"+df_separator+"mm"+df_separator+"yy";
	}

	var today 	= new Date();
	var end_day = new Date(<?php echo VikAppointmentsSearchHelper::getMaxTimeStamp() * 1000; ?>);

	jQuery("#vapcalendarmod<?php echo $randid; ?>:input").datepicker({
		minDate: today,
		maxDate: end_day,
		dateFormat: today.format,
		beforeShowDay: vapModSetupCalendar
	});

	function vapModSetupCalendar(date) {
		
		for (var i = 0; i < closingPeriods.length; i++) {
			if (new Date(closingPeriods[i]['start'] * 1000).valueOf() <= date.valueOf() && date.valueOf() <= new Date(closingPeriods[i]['end'] * 1000).valueOf()) {
				return [false,""];
			}
		}
		
		for (var i = 0; i < closingDays.length; i++) {
			var _d = vapModGetDate(closingDays[i]['date']);
			
			if (closingDays[i]['freq'] == 0) {
				if (_d.valueOf() == date.valueOf()) {
					return [false,""];
				}
			} else if (closingDays[i]['freq'] == 1) {
				if (_d.getDay() == date.getDay()) {
					return [false,""];
				}
			} else if (closingDays[i]['freq'] == 2) {
				if (_d.getDate() == date.getDate()) {
					return [false,""];
				} 
			} else if (closingDays[i]['freq'] == 3) {
				if (_d.getDate() == date.getDate() && _d.getMonth() == date.getMonth()) {
					return [false,""];
				} 
			}
		}
		
		return [true, ""];
	}

	function vapModGetDate(day) {
		var formats = today.format.split(df_separator);
		var date_exp = day.split(df_separator);
		
		var _args = new Array();
		for (var i = 0; i < formats.length; i++) {
			_args[formats[i]] = parseInt( date_exp[i] );
		}
		
		return new Date(_args['yy'], _args['mm']-1, _args['dd']);
	}

	function vapModServiceValueChanged(select, mod_id) {
		
		var id_ser = jQuery(select).val();
		
		jQuery.noConflict();

		jQuery('#vapempselmod' + mod_id).attr('disabled', true);
				
		var jqxhr = jQuery.ajax({
			type: "post",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=get_employees_rel_service&tmpl=component' . ($itemid ? '&Itemid=' . $itemid : ''), false); ?>",
			data: {
				id_ser: id_ser
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0] == 1 && obj[1] == 1) {
				vapModComposeEmployeeSelect(obj[2], mod_id);
				jQuery('#vapemprowmod' + mod_id).show();
			} else {
				jQuery('#vapemprowmod' + mod_id).hide();
				jQuery('#vapempselmod' + mod_id).html('');
			}
		});

		vapModUpdateFormAction(select);
	}

	function vapModUpdateFormAction(select) {
		// update form action with rewritten URL of the selected service
		document.vapmodulesearch.action = jQuery(select).find('option:selected').data('url');
	}

	function vapModComposeEmployeeSelect(arr, mod_id) {
		var _html = '';
		
		for (var i = 0; i < arr.length; i++) {
			_html += '<option value="' + arr[i]['id'] + '">' + arr[i]['nickname'] + '</option>';
		}
		
		jQuery('#vapempselmod' + mod_id).html(_html).attr('disabled', false);
		jQuery('#vapempselmod' + mod_id).trigger('change.select2');
	}

</script>
