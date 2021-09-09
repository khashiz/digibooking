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

JHtml::_('bootstrap.tooltip');
//use VikDate\Date;
//use VikDate\Jalali;
use Hekmatinasser\Verta\Verta;
$worktime       = $this->worktime;
$worktime_date  = $this->worktime_date;


$vik 	= UIApplication::getInstance();
$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');
$df_separator = $date_format[1];

$opening = VikAppointments::getOpeningTime();
$closing = VikAppointments::getClosingTime();

$max_id = 0;

$merged = array_merge($worktime, $worktime_date);

if (count($merged))
{
	$max_id = max(array_map(function($val)
	{
		return $val['id'];
	}, $merged));
}

$wd_map = array();
//$one_day_ts = 24*3600;

foreach ($merged as $wd)
{

	if ($wd['ts'] == -1)
	{
		// no timestamp set, get closest day with the same day of the week
		//$week_day2 = new JDate($this->day);

        $week_day = new Verta($this->day);
		while (($week_day->format('N') % 7) != ($wd['day'] % 7))
		{
			$week_day->modify('+1 day');
		}

		$ts = $week_day->format($date_format);

		//$ts = ArasJoomlaVikApp::jdate('Y-m-d',$this->day);


	}
	else
	{
		//$ts = $wd['ts'];
		$ts = Verta::instance($wd['ts'])->format($date_format);
	}

	$is_custom = $wd['ts'] != -1 ? 1 : 0;

//    $timestamp = VikAppointments::jcreateTimestamp($ts);
//    $jget_date = ArasJoomlaVikApp::jgetdate($timestamp);
    $obj = new stdClass;
	$obj->id 	 = (int) $wd['id'];
	$obj->day 	 = (int) $wd['day'];
	$obj->from 	 = (int) $wd['from'];
	$obj->to 	 = (int) $wd['to'];
	$obj->date 	 =  $ts;//Verta::instance($ts)->format($date_format);
	$obj->closed = (int) $wd['closed'];
	$obj->ts 	 = $is_custom;
	$obj->db 	 = 1;

	if ($is_custom)
	{
	    //$j_get_date = ArasJoomlaVikApp::jgetdate($ts);
		$obj->day = Verta::instance($ts)->format('N') % 7;
	}
	//file_put_contents(JPATH_ROOT.'/debug/$obj.txt', print_r($obj, true),8);

	$wd_map[$wd['id']] = $obj;
}

$cell_height_pixel = 60;

$td_id = 0;



?>

<div class="btn-toolbar" style="height: 42px;">

	<div class="btn-group pull-left input-prepend input-append">

		<button type="button" class="btn" onclick="updateCurrentDate('-1 month');"><i class="fa fa-angle-double-left"></i></button>
		<button type="button" class="btn" onclick="updateCurrentDate('-1 week');" style="border-top-right-radius: 0;border-bottom-right-radius: 0;"><i class="fa fa-angle-left"></i></button>

		<?php
		//$date = new Verta($this->day);
        //$date = ArasJoomlaVikApp::jgetdate($this->day);

		$attrs = array();
		$attrs['onChange'] = 'updateCurrentDate(\'\');';

		echo $vik->calendar(ArasJoomlaVikApp::jdate($date_format,$this->day), 'dayfrom', null, null, $attrs);

		?>

		<button type="button" class="btn" onclick="updateCurrentDate('+1 week');"><i class="fa fa-angle-right"></i></button>
		<button type="button" class="btn" onclick="updateCurrentDate('+1 month');"><i class="fa fa-angle-double-right"></i></button>

	</div>

	<div class="btn-group pull-right vap-setfont">
		<span style="margin-right: 20px;" class="hasTooltip" title="<?php echo JText::sprintf('VAPWDLEGENDTITLE1', ArasJoomlaVikApp::jdate('l',$this->day)); ?>">
			<i class="fa fa-square" style="color: #1bd295;"></i>&nbsp;<?php echo JText::_('VAPWDLEGENDLABEL1'); ?>
		</span>
		<span style="margin-right: 20px;" class="hasTooltip" title="<?php echo JText::sprintf('VAPWDLEGENDTITLE2', ArasJoomlaVikApp::jdate('l d F Y',$this->day)); ?>">
			<i class="fa fa-square" style="color: #2eb0d2;"></i>&nbsp;<?php echo JText::_('VAPWDLEGENDLABEL2'); ?>
		</span>
	</div>

</div>

<div>
	<div class="span8" style="margin-left: 0px;">
		<?php
        $date = new Verta($this->day);
		//$date = ArasJoomlaVikApp::jgetdate($this->day);
		?>
		<?php $vik->openFieldset(ArasJoomlaVikApp::jdate('F Y',$this->day), 'form-horizontal'); ?>

		<table class="vap-workday-calendar" cellspacing="0">
			<thead>
				<tr>
					<th width="7%">&nbsp;</th>
					<?php
					for ($i = 0; $i < 7; $i++)
					{
						?>
						<th width="12%">
							<?php echo $date->format('D d'); ?>
						</th>
						<?php

						$date->modify('+1 day');
                        //$this->day = addDay($this->day,1);
					}
					?>
				</tr>
			</thead>
            <?php
            //dddd( ArasJoomlaVikApp::jgetdate($this->day))
            ?>
			<tbody>
				<?php
				$end = ($closing['min'] == 0 ? $closing['hour'] - 1 : $closing['hour']);
				for ($h = $opening['hour']; $h <= $end; $h++)
				{
					?>
					<tr>
						<td style="text-align: right;">
							<?php echo ArasJoomlaVikApp::jdate($time_format, ArasJoomlaVikApp::jmktime($h, 0, 0, 1, 1, 1398)); ?>
						</td>
						<?php
                        // saber important
						//$date = new JDate($this->day);
						$date = new Verta($this->day);
						//$jgetdate = ArasJoomlaVikApp::jgetdate($this->day);
						for ($i = 0; $i < 7; $i++)
						{
							$times = $this->getWorkingDays($date, $worktime, $worktime_date);
							//file_put_contents(JPATH_ROOT.'/debug/$times.txt', print_r($times, true),8);
							// do not fill if not needed
							$height = '';
							$wd 	= null;
							$label 	= '&nbsp;';
							$margin = 0;

							if ($wd = $this->isClosed($date, $times))
							{
								$class = 'day-closed';
							}
							else if ($wd = $this->startsHere($h, $times))
							{
								$class  = 'time-starts';
								$shift  = $wd['from'] - $h * 60;
								$margin = $shift * ($cell_height_pixel / 60); // height pixel / max minutes (ratio)

								// check if working day starts and ends in the same slot
								// if ($this->endsHere($h, array($wd)))
								// {
								$class .= ' time-ends';
								$top 	= $wd['to'] - $h * 60;
								$height = abs($shift - $top) * ($cell_height_pixel / 60); // height pixel / max minutes (ratio)
								$height += ceil($height / $cell_height_pixel) - 1; // includes 1 px for each border that the div covers
								$height = "height: {$height}px;";
								// }

								$label =  ArasJoomlaVikApp::jdate($time_format, ArasJoomlaVikApp::jmktime(floor($wd['from'] / 60), $wd['from'] % 60, 0, 1, 1, 1398)) .
									' - ' . ArasJoomlaVikApp::jdate($time_format, ArasJoomlaVikApp::jmktime(floor($wd['to'] / 60), $wd['to'] % 60, 0, 1, 1, 1398));

							}
							else if ($wd = $this->endsHere($h, $times))
							{
								$class  = 'time-ends';
								$shift  = $wd['to'] - $h * 60;
								$margin = ($cell_height_pixel - $shift * ($cell_height_pixel / 60)) * -1; // height pixel / max minutes (ratio)
							}
							else if ($wd = $this->isContained($h, $times))
							{
								$class  = 'time-contains';
								$margin = 0;
							}
							else
							{
								$class  = 'time-empty';
								$margin = 0;
							}

							$data = array();

							if ($wd)
							{
								$data['id'] = $wd['id'];

								if ($wd['ts'] != -1)
								{
									$class .= ' custom-day';
								}
							}

							//$j_date_to_timestamp = ArasJoomlaVikApp::jDateToTimestamp($date->format($date_format),$df_separator);
                            //$j_get_date = ArasJoomlaVikApp::jgetdate($j_date_to_timestamp);
                            // 'cell-day'	=> $j_get_date['wday']

							$data = array_merge($data, $def = array(
								'cell-from' => $h * 60,
								'cell-to'   => ($h + 1) * 60,
								'cell-day'	=> ArasJoomlaVikApp::fixDayIndex($date->format('N') % 7),// matin2
								'cell-date' => $date->format($date_format),
								'index'		=> $td_id++,
							));

							$data_str = '';
							foreach ($data as $k => $v)
							{
								$data_str .= " data-{$k}=\"{$v}\"";
							}

							?>
							<td class="<?php echo $class; ?>" style="position: relative;" <?php echo $data_str; ?>>
								<div
									class="<?php echo $class; ?>"
									style="position: absolute; width:100%;
									top: <?php echo $margin; ?>px;
									<?php echo $height; ?>"
								>
									<?php echo $label; ?>
								</div>
							</td>
							<?php

							$date->modify('+1 day');
						}
						?>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>

		<input type="hidden" name="wdjson" value="" />
		<input type="hidden" name="wdremove" value="" />
		<input type="hidden" name="dayrule" value="<?php echo $this->day; ?>" />

		<?php echo $vik->closeFieldset(); ?>
	</div>

	<div class="span4" id="wd-edit-fieldset" style="display: none;">
		<?php echo $vik->openFieldset(JText::_('VAPEDIT'), 'form-horizontal'); ?>

			<?php
			$hours = array();
			for ($h = $opening['hour']; $h <= $closing['hour']; $h++)
			{
				$label = ($h < 10 ? '0' : '') . $h;
				$hours[] = JHtml::_('select.option', $h, $label);
			}
			$mins = array();
			for ($m = 0; $m < 60; $m += 5)
			{
				$label = ($m < 10 ? '0' : '') . $m;
				$mins[] = JHtml::_('select.option', $m, $label);
			}
			?>

			<!-- FREQUENCY - Select -->

			<?php
			$options = array();
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGEWD2') . ':'); ?>
				<select id="vap-freq-sel">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', null); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- FROM HOUR/MIN - Select -->

			<?php echo $vik->openControl(JText::_('VAPMANAGEWD3') . ':', 'wd-close-condition'); ?>
				<select id="vap-from-hour-sel" class="vap-hm-sel">
					<?php echo JHtml::_('select.options', $hours, 'value', 'text', null); ?>
				</select>
				<select id="vap-from-min-sel" class="vap-hm-sel">
					<?php echo JHtml::_('select.options', $mins, 'value', 'text', null); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- TO HOUR/MIN - Select -->

			<?php echo $vik->openControl(JText::_('VAPMANAGEWD4') . ':', 'wd-close-condition'); ?>
				<select id="vap-to-hour-sel" class="vap-hm-sel">
					<?php echo JHtml::_('select.options', $hours, 'value', 'text', null); ?>
				</select>
				<select id="vap-to-min-sel" class="vap-hm-sel">
					<?php echo JHtml::_('select.options', $mins, 'value', 'text', null); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- CLOSED - Checkbox -->

			<?php
			$elem_yes = $vik->initRadioElement('', '', false, 'onclick="toggleClosingCheckbox(1);"');
			$elem_no  = $vik->initRadioElement('', '', true, 'onclick="toggleClosingCheckbox(0);"');
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE22') . ':'); ?>
				<?php echo $vik->radioYesNo('wd-closed', $elem_yes, $elem_no, true); ?>
			<?php echo $vik->closeControl(); ?>

			<!-- ACTIONS - Checkbox -->

			<?php
				$trash = '<a href="javascript: void(0);" onclick="removeSelectedWorkingDay();" style="font-size: 18px;"><i class="fa fa-trash"></i></a>';
			?>
			<?php echo $vik->openControl($trash); ?>
				<button type="button" class="btn btn-success" onclick="updateWorkingDay();">
					<i class="icon-apply"></i>&nbsp;
					<?php echo JText::_('VAPSAVE'); ?>
				</button>

				<button type="button" class="btn btn-success" onclick="updateWorkingDay(true);" id="btn-as-copy">
					<i class="icon-apply"></i>&nbsp;
					<?php echo JText::_('VAPSAVEASCOPY'); ?>
				</button>
			<?php echo $vik->closeControl(); ?>

		<?php echo $vik->closeFieldset(); ?>
	</div>
</div>
<?php

?>
<script>

	var WD_COUNT = <?php echo $max_id; ?>;
	var WD_MAP = <?php echo (empty($wd_map) ? '{}' : json_encode($wd_map)); ?>;

	var WEEK_DAYS_FREQ = [];
	<?php for ($d = 0; $d < 7; $d++) { ?>
	WEEK_DAYS_FREQ.push('<?php echo addslashes(JText::sprintf(ArasJoomlaVikApp::weekDay($d).' ها')); ?>');
	<?php } ?>
    console.log(WEEK_DAYS_FREQ)
	var SELECTED_WD 	= -1;
	var SELECTED_INDEX  = -1;

	var AUTO_ADD_LENGTH = false;

	var BOUNDS = {
		opening: <?php echo json_encode($opening); ?>,
		closing: <?php echo json_encode($closing); ?>
	};

	jQuery(document).ready(function() {

		jQuery('.vap-hm-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 75
		});

		jQuery('#vap-freq-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		// handle cell click event
		jQuery('.vap-workday-calendar tbody td:not(:first-child)').on('click', function() {

			// check if we should stop increasing the current time slot
			if (AUTO_ADD_LENGTH === true)
			{
				AUTO_ADD_LENGTH = false;

				// stop creating the working day and render it properly
				renderWorkingDay(WD_MAP[SELECTED_WD]);
			}
			// or make sure the slot has not been yet clicked
			else if (jQuery(this).hasClass('time-empty'))
			{
				insertWorkingDay(this);

				// keep index of selected <td>
				SELECTED_INDEX = jQuery(this).data('index');

				AUTO_ADD_LENGTH = true;
			}
			// otherwise pick the selected working day
			else
			{
				selectWorkingDay(this);
			}

		});

		// auto increase WD length when you enter in a cell
		jQuery('.vap-workday-calendar tbody td:not(:first-child)').hover(function() {

			if (!AUTO_ADD_LENGTH) {
				return false;
			}

			// Get only the cell the belong to the parent column.
			// This because I may start creating a working day on Monday and
			// I may extend the workign day by hovering the mouse on Tuesday column.
			var start = jQuery('td[data-index="'+SELECTED_INDEX+'"]');
			var diff  = parseInt(jQuery(start).data('cell-day')) - parseInt(jQuery(this).data('cell-day'));
			var index = parseInt(jQuery(this).data('index')) + diff;
			var elem  = jQuery('td[data-index="' + index + '"]');

			var cell_id = parseInt(jQuery(elem).attr('data-id'));

			if (!isNaN(cell_id) && cell_id != SELECTED_WD) {
				// Impossible to overlap an existing working day.
				// Stop auto increasing the slots.
				jQuery(elem).trigger('click');
				return false;
			}

			var to = parseInt(jQuery(elem).data('cell-to'));

			if (WD_MAP[SELECTED_WD].from >= to) {
				// Impossible to have a starting time higher than the ending time.
				// Stop auto increasing the slots.
				jQuery(elem).trigger('click');
				return false;
			}

			WD_MAP[SELECTED_WD].to = to;
			adjustBounds(SELECTED_WD);

			registerWorkingDay(WD_MAP[SELECTED_WD]);

			renderWorkingDay(WD_MAP[SELECTED_WD]);

			openEditFieldset(WD_MAP[SELECTED_WD]);

		}, function() {
			// do not handle leave event
		});

		// handle change event for frequency dropdown
		jQuery('#vap-freq-sel').on('change', function() {

			var val  = jQuery(this).val();
			var date = WD_MAP[SELECTED_WD].date;
			setSubmitButtonText(val == date);

		});

	});

	function setSubmitButtonText(is_new) {

		if (is_new) {

			jQuery('#btn-as-copy').show();

		} else {

			jQuery('#btn-as-copy').hide();

		}

	}

	function insertWorkingDay(cell) {

		WD_COUNT++;

		// jQuery(cell).removeClass('time-empty').addClass('time-starts time-ends');
		// jQuery(cell).find('div.time-empty').removeClass('time-empty').addClass('time-starts time-ends');

		jQuery(cell).attr('data-id', WD_COUNT);

		WD_MAP[WD_COUNT] = {
			id: 	WD_COUNT,
			from: 	jQuery(cell).data('cell-from'),
			to: 	jQuery(cell).data('cell-to'),
			day: 	jQuery(cell).data('cell-day'),
			date: 	jQuery(cell).data('cell-date'),
			ts: 	0,
			closed: 0,
			db: 	0
		};


		if (hasCustomDay(WD_COUNT)) {
			WD_MAP[WD_COUNT].ts = 1;
		}

		adjustBounds(WD_COUNT);

		var from_to = [
			getFormattedTime(
				Math.floor(WD_MAP[WD_COUNT].from / 60),
				WD_MAP[WD_COUNT].from % 60,
				'<?php echo $time_format; ?>'
			),
			getFormattedTime(
				Math.floor(WD_MAP[WD_COUNT].to / 60),
				WD_MAP[WD_COUNT].to % 60,
				'<?php echo $time_format; ?>'
			)
		];

		jQuery(cell).find('div').text(from_to[0] + ' - ' + from_to[1]);

		renderWorkingDay(WD_MAP[WD_COUNT]);

		openEditFieldset(WD_MAP[WD_COUNT]);

		// update working days input field
		registerWorkingDay(WD_MAP[WD_COUNT]);
	}

	function selectWorkingDay(cell) {
		var id = parseInt(jQuery(cell).attr('data-id'));

		if (!WD_MAP.hasOwnProperty(id)) {
			return false;
		}

		var wd = WD_MAP[id];
    //console.log(WD_MAP)
		openEditFieldset(wd);

	}

	function openEditFieldset(wd) {
        //console.log(wd) // matin
		changeSelectionWD(wd.id);

		jQuery('input[name="wd-closed"]').prop('checked', wd.closed ? true : false);

		var _options = '';
		_options += '<option value="'+wd.date+'">' + wd.date + '</option>';
		_options += '<option value="'+wd.day+'">' + WEEK_DAYS_FREQ[wd.day] + '</option>';

		//console.log(wd.day)

		jQuery('#vap-freq-sel').html(_options);
		jQuery('#vap-freq-sel').select2('val', wd.ts != 0 ? wd.date : wd.day);
		// do not allow selection of weekday if there is at least a custom day
		jQuery('#vap-freq-sel option[value="'+wd.day+'"]').prop('disabled', wd.ts == 1 ? true : false);
		// update always button text
		setSubmitButtonText(false);

		jQuery('#vap-from-hour-sel').select2('val', Math.floor(wd.from / 60));
		jQuery('#vap-from-min-sel').select2('val', wd.from % 60);

		jQuery('#vap-to-hour-sel').select2('val', Math.floor(wd.to / 60));
		jQuery('#vap-to-min-sel').select2('val', wd.to % 60);

		toggleClosingCheckbox(wd.closed);

		jQuery('#wd-edit-fieldset').show();

		// trigger scroll to make it visible for the current position
		windowScrollControl();
	}

	function closeEditFieldset() {

		changeSelectionWD(-1);

		jQuery('#wd-edit-fieldset').hide();

	}

	function toggleClosingCheckbox(is) {

		if (is) {
			jQuery('.wd-close-condition').hide();
		} else {
			jQuery('.wd-close-condition').show();
		}

	}

	function removeSelectedWorkingDay(res) {

		if (typeof res === 'undefined' || !res) {
			res = confirm('<?php echo addslashes(JText::_('VAPSYSTEMCONFIRMATIONMSG')); ?>');
		}

		if (!res) {
			return false;
		}

		clearTableCells(SELECTED_WD);

		var has_cd = hasCustomDay(SELECTED_WD);

		var weekday = WD_MAP[SELECTED_WD].day;
		var isDB	= WD_MAP[SELECTED_WD].db;

		delete WD_MAP[SELECTED_WD];

		jQuery('td[data-id="'+SELECTED_WD+'"]').attr('data-id', null);

		registerDeletedWorkingDay(SELECTED_WD, isDB);

		// this method unsets SELECTED_WD
		closeEditFieldset();

		// check if the working day should be replaced (only if the column doesn't own custom days)
		if (!has_cd) {
			jQuery.each(WD_MAP, function(k, wd) {

				if (wd.day == weekday) {
					renderWorkingDay(wd);
				}

			});
		}
	}

	function updateWorkingDay(as_copy) {

		if (typeof as_copy === 'undefined') {
			as_copy = false;
		}

		if (!WD_MAP.hasOwnProperty(SELECTED_WD)) {
			return false;
		}

		if (AUTO_ADD_LENGTH) {
			// disable hover if we are still creating the working day
			AUTO_ADD_LENGTH = false;
		}

		var bounds = {
			from: 	parseInt(jQuery('#vap-from-hour-sel').val()) * 60 + parseInt(jQuery('#vap-from-min-sel').val()),
			to: 	parseInt(jQuery('#vap-to-hour-sel').val()) * 60 + parseInt(jQuery('#vap-to-min-sel').val())
		};

		// check if FROM is higher than TO
		if (bounds.from > bounds.to)
		{
			// then swap FROM and TO
			var app = bounds.from;
			bounds.from = bounds.to;
			bounds.to = app;
		}
		// otherwise check if FROM is equals to TO
		else if (bounds.from == bounds.to)
		{
			// then increase TO value by an hour
			bounds.to += 60;
		}

		var wd;

		var frequency = jQuery('#vap-freq-sel').val() == WD_MAP[SELECTED_WD].date ? 1 : 0;

		// make sure the frequency has not changed
		// if (frequency == WD_MAP[SELECTED_WD].ts)
		// make sure the working day should not be saved as copy
		if (!as_copy)
		{
			wd = WD_MAP[SELECTED_WD];
		}
		// otherwise it is needed to insert a new working day
		else
		{
			wd = {};

			jQuery.each(WD_MAP[SELECTED_WD], function(k, v) {
				wd[k] = v;
			});

			WD_COUNT++;

			wd.id = WD_COUNT;
			wd.db = 0;

			WD_MAP[WD_COUNT] = wd;

			// changeSelectionWD(WD_COUNT);

			// clear all the working days with weekly frequency
			jQuery.each(WD_MAP, function(k, v) {
				if (v.day == wd.day && v.ts != frequency) {
					clearTableCells(v.id);
				}
			});
		}

		wd.closed = jQuery('input[name="wd-closed"]').is(':checked') ? 1 : 0;

		wd.from = bounds.from;
		wd.to 	= bounds.to;

		wd.ts 	= frequency;

		adjustBounds(wd.id);

		renderWorkingDay(wd);

		// changeSelectionWD(wd.id);
		openEditFieldset(wd);

		// update working days input field
		registerWorkingDay(wd);
	}

	function clearTableCells(id) {

		jQuery('td[data-id="'+id+'"]').each(function() {

			jQuery(this).removeClass('time-starts')
				.removeClass('time-ends')
				.removeClass('time-contains')
				.removeClass('day-closed')
				.removeClass('custom-day')
				.addClass('time-empty')
				.attr('data-id', null);

			jQuery(this).find('div').removeClass('time-starts')
				.removeClass('time-ends')
				.removeClass('time-contains')
				.removeClass('day-closed')
				.removeClass('custom-day')
				.addClass('time-empty')
				.attr('style', '')
				.text('');

		});

	}

	function renderWorkingDay(wd) {

		// clear all the cell assigned to the current working day
		clearTableCells(wd.id);

		var selector = '';
		if (wd.ts == 1) {
			selector = 'td[data-cell-date="'+wd.date+'"]';
		} else {
			selector = 'td[data-cell-day="'+wd.day+'"]';
		}

		// evaluate all the cells to render the working day
		jQuery(selector).each(function() {

			var cell = new WDCell(this);

			var clazz 	= '';
			var text  	= '';
			var margin 	= 0;
			var height 	= null;

			// Snippet used to display concurrent working days,
			// for example that starts and ends within the same slot
			// (e.g. ends @ 14:15 - starts @ 14:45).
			var tmp 	= wd;
			var wd2 	= null;
			var wd2_id 	= jQuery(this).attr('data-id');

			if (wd.id != wd2_id && WD_MAP.hasOwnProperty(wd2_id))
			{
				wd2 = WD_MAP[wd2_id];
			}

			if (cell.isClosed(wd))
			{
				clazz = 'day-closed';
			}
			else if (cell.starts(wd) || (wd2 && cell.starts(wd2)))
			{
				if (wd2 && cell.starts(wd2)) {
					// swap current working day
					wd = wd2;
				}

				clazz = 'time-starts';
				text  = getFormattedTime(Math.floor(wd.from / 60), wd.from % 60, '<?php echo $time_format; ?>') +
					" - " + getFormattedTime(Math.floor(wd.to / 60), wd.to % 60, '<?php echo $time_format; ?>');

				margin = (wd.from - cell.from) * (<?php echo $cell_height_pixel; ?> / 60); // height pixel / max minutes (ratio)

				// if (cell.ends(wd)) {
				if (!AUTO_ADD_LENGTH) {
					// add ends time only if we are not creating a working days
					clazz += ' time-ends';
				}

				height = Math.abs((wd.from - cell.from) - (wd.to - cell.from)) * (<?php echo $cell_height_pixel; ?> / 60); // height pixel / max minutes (ratio)

				height += Math.ceil(height / <?php echo $cell_height_pixel; ?>) - 1;
				// }
			}
			else if (cell.ends(wd))
			{
				clazz = 'time-ends';

				margin = (<?php echo $cell_height_pixel; ?> - (wd.to - cell.from) * (<?php echo $cell_height_pixel; ?> / 60)) * -1; // height pixel / max minutes (ratio)
			}
			else if (cell.contains(wd))
			{
				clazz = 'time-contains';
			}

			if (clazz.length)
			{
				jQuery(this).removeClass('time-empty')
					.removeClass('time-starts')
					.removeClass('time-ends')
					.removeClass('time-contains')
					.removeClass('day-closed')
					.removeClass('custom-day');

				jQuery(this).find('div').removeClass('time-empty')
					.removeClass('time-starts')
					.removeClass('time-ends')
					.removeClass('time-contains')
					.removeClass('day-closed')
					.removeClass('custom-day');

				if (wd.ts == 1) {
					clazz += ' custom-day';
				}

				jQuery(this).addClass(clazz)
					.attr('data-id', wd.id);

				jQuery(this).find('div')
					.addClass(clazz)
					.css('position', 'absolute')
					.css('width', '100%')
					.css('top', margin + 'px')
					.text(text);

				if (!AUTO_ADD_LENGTH) {
					if (height) {
						// set the height for default rendering
						jQuery(this).find('div')
							.css('height', height+'px');
					}

					// hide again ending and middle times
					if (!jQuery(this).hasClass('time-starts') && (jQuery(this).hasClass('time-ends') || jQuery(this).hasClass('time-contains'))) {
						jQuery(this).find('div')
							.css('z-index', 0)
							.css('display', 'none');
					} else {
						jQuery(this).find('div')
							.css('z-index', 1)
							.css('display', 'block');
					}
				} else {
					// we are creating a working day, display also the ending and middle times
					jQuery(this).find('div')
						.css('z-index', 1)
						.css('display', 'block');
				}
			}

			// restore always the working day
			wd = tmp;
		});

	}

	function changeSelectionWD(id) {

		jQuery('td div.box-selected').removeClass('box-selected');

		SELECTED_WD = id;

		if (SELECTED_WD != -1) {
			jQuery('td[data-id="'+SELECTED_WD+'"] div').addClass('box-selected');
		}
	}

	function adjustBounds(id) {
		var from = WD_MAP[id].from;
		var to   = WD_MAP[id].to;

		var opening = BOUNDS.opening.hour * 60 + BOUNDS.opening.min;
		var closing = BOUNDS.closing.hour * 60 + BOUNDS.closing.min;

		WD_MAP[id].from = Math.max(from, opening);
		WD_MAP[id].to   = Math.min(to, closing);
	}

	function hasCustomDay(id) {
		var wd = WD_MAP[id];

		var has = false;

		jQuery.each(WD_MAP, function(k, v) {
			if (v.id != wd.id
				&& v.day == wd.day
				&& v.ts == 1)
			{
				has = true;
				return false;
			}
		});

		return has;
	}

	function registerWorkingDay(wd, type) {

		if (typeof type === 'undefined') {
			type = 'push';
		}

		var json = jQuery('input[name="wdjson"]').val();

		var map = null;

		if (json.length) {
			map = jQuery.parseJSON(json);
		} else {
			map = {};
		}

		if (type == 'push') {
			map[wd.id] = wd;
		} else if (type == 'unset') {
			delete map[wd.id];
		}

		jQuery('input[name="wdjson"]').val(JSON.stringify(map));
	}

	function registerDeletedWorkingDay(id, isDB) {

		// remove only if already stored in the DB
		if (isDB) {
			var json = jQuery('input[name="wdremove"]').val();

			var map = null;

			if (json.length) {
				map = jQuery.parseJSON(json);
			} else {
				map = [];
			}

			map.push(id);

			jQuery('input[name="wdremove"]').val(JSON.stringify(map));
		}

		// remove the record from the wdjson object (if any)
		registerWorkingDay({id: id}, 'unset');
	}

	// handle calendar inputs

	function updateCurrentDate(rule) {

		// var rule = rule + (rule.length ? ' ' : '') + jQuery('#dayfrom').val();

		jQuery('input[name="dayrule"]').val(rule);

		if (formObserver.isChanged()) {
			if (!confirm('<?php echo addslashes(JText::_('VAPFORMCHANGEDCONFIRMTEXT')); ?>')) {
				return false;
			}
		}

		jQuery('input[name="task"]').val('editemployee');
		jQuery('#adminForm').append('<input type="hidden" name="cid[]" value="<?php echo $this->employee['id']; ?>" />');
		jQuery('#adminForm').submit();
	}

	// handle edit fieldset scroll

	jQuery(window).on('scroll', debounce(
		windowScrollControl, 250
	));

	function windowScrollControl() {
		var fieldset = jQuery('#wd-edit-fieldset');

		// use 100px of margin to ignore scrollspy
		var margin = 100;

		if (fieldset.is(':visible')) {
			var marginTop = Math.max(0, jQuery(window).scrollTop() + margin - fieldset.height());
			fieldset.css('margin-top', marginTop);
		}
	}

	// Working Day Cell class

	function WDCell(td) {
		this.td = td;

		this.from 	= parseInt(jQuery(this.td).data('cell-from'));
		this.to 	= parseInt(jQuery(this.td).data('cell-to'));
		this.day 	= parseInt(jQuery(this.td).data('cell-day'));
		this.date 	= 		   jQuery(this.td).data('cell-date');

		return this;
	}

	WDCell.prototype.isClosed = function(wd) {
		return wd.closed == 1;
	}

	WDCell.prototype.starts = function(wd) {
		return (this.from <= wd.from && wd.from < this.to);
	}

	WDCell.prototype.ends = function(wd) {
		return (this.from < wd.to && wd.to <= this.to);
	}

	WDCell.prototype.contains = function(wd) {
		return (wd.from < this.from && this.to < wd.to);
	}

</script>
