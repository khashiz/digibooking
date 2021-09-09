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

$services  = $this->services;
$employees = $this->employees;
$bookings  = $this->bookings;

$selected_employee 	= $this->id_emp;
$selected_service 	= $this->id_ser;
$selected_year 		= $this->year;

$statistics = $this->statistics;

VikAppointments::setCurrentTimezone($this->employeeTimezone);

$arr = ArasJoomlaVikApp::jgetdate();
$arr = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime(0, 0, 0, 1, 1, $selected_year));

$max_capacity_selected_service = (count($services) > 0 ? $services[0]['max_capacity'] : 0);

$service_select = '<select name="option" id="vapsersel" onChange="serviceValueChanged();">';

foreach ($services as $s)
{
	if ($s['id'] == $selected_service)
	{
		$max_capacity_selected_service = $s['max_capacity'];
	}

 	$service_select .= '<option value="' . $s['id'] . '" ' . ($s['id'] == $selected_service ? 'selected="selected"' : '') . '>' . $s['name'] . ' (' . $s['duration'] . ' ' . JText::_('VAPSHORTCUTMINUTE') . ')</option>';	
}

$service_select .= '</select>';

$employee_select = '<select name="option" id="vapempsel" onChange="loadCalendars(' . $arr['year'] . ', false);">';

foreach ($employees as $e)
{
 	$employee_select .= '<option value="' . $e['id'] . '" ' . ($e['id'] == $selected_employee ? 'selected="selected"' : '') . '>' . $e['nickname'] . '</option>';	
}

$employee_select .= '</select>';


$day_shift = 0;//VikAppointments::getCalendarFirstWeekDay();

$ncal = 12;

$DAYS = array();
for ($i = 0; $i < 7; $i++)
{
	$DAYS[$i] = (6 - ($day_shift - $i) + 1) % 7;
	/**
	 * 
	 *	DAY = ( (NUM_DAYS-1) - ( SHIFT - DAY_INDEX ) + 1 ) % NUM_DAYS 
	 *
	 * 	SATURDAY
	 * 	0				1				2				3				4				5				6
	 * 	6-(6-0)+1%7=1	6-(6-1)+1%7=2	6-(6-2)+1%7=3	6-(6-3)+1%7=4	6-(6-4)+1%7=5	6-(6-5)+1%7=6	6-(6-6)+1%7=0
	 * 
	 * 	SUNDAY
	 * 	0				1				2				3				4				5				6
	 * 	6-(0-0)+1%7=0	6-(0-1)+1%7=1	6-(0-2)+1%7=2	6-(0-3)+1%7=3	6-(0-4)+1%7=4	6-(0-5)+1%7=5	6-(0-6)+1%7=6
	 * 
	 * 	MONDAY
	 * 	0				1				2				3				4				5				6
	 * 	6-(1-0)+1%7=6 	6-(1-1)+1%7=0	6-(1-2)+1%7=1	6-(1-3)+1%7=2	6-(1-4)+1%7=3	6-(1-5)+1%7=4	6-(1-6)+1%7=5
	 * 
	 * 	WEDNESDAY
	 * 	0				1				2				3				4				5				6
	 * 	6-(3-0)+1%7=4 	6-(3-1)+1%7=5	6-(3-2)+1%7=6	6-(3-3)+1%7=0	6-(3-4)+1%7=1	6-(3-5)+1%7=2	6-(3-6)+1%7=3
	 */
}

$_DAY_TEXT = ArasJoomlaVikApp::weekDaysShort();

$_SINGLE_DAY_SEC = 86400;

$book_index = 0;

$closing_days 		= VikAppointments::getClosingDays();
$closing_periods 	= VikAppointments::getClosingPeriods();

$dbo = JFactory::getDbo();

$is_stat = VikAppointments::isStatisticsVisible() ? 1 : 0;

$stat_displayed = 'display: inline-block;';
if (!$is_stat)
{
	$stat_displayed = 'display: none;';
}

$time_format = VikAppointments::getTimeFormat();

?>

<form action="index.php" method="post" name="adminForm"  id="adminForm">
	
	<?php if (count($services) > 0 && count($employees) > 0) { ?>
		<div class="btn-toolbar" style="height: 32px;">
			<div class="btn-group pull-left vap-setfont">
				<?php echo $employee_select; ?>
			</div>
		
			<div class="btn-group pull-left vap-setfont">
				<?php echo $service_select; ?>
			</div>
		
			<div class="btn-group pull-right">
				<button type="button" class="btn" onClick="vapSwitchLayout();"><?php echo JText::_('VAPSWITCHTODAILYVIEW'); ?></button>
			</div>
			
			<div class="btn-group pull-right">
				<button type="button" class="btn" onClick="vapChangeStatistics();" id="vapcalstatbtn"><?php echo JText::_('VAPCALSTATBUTTON' . $is_stat); ?></button>
			</div>
		</div>
	<?php } ?>
	
	<div class="vapallcalhead" style="font-size: 18px;">
		<span class="vapprevyearsp">
			<a href="javascript: void(0);" onClick="loadCalendars(<?php echo $arr['year'] - 1; ?>, true);">
				<i class="fa fa-chevron-left big"></i>
			</a>
		</span>
		
		<span class="vaptitleyearsp">
			<?php echo $arr['year']; ?>
		</span>
		
		<span class="vaptitle2yearsp vapstatisticelem" style="<?php echo $stat_displayed; ?>">
			<?php echo '- ' . JText::sprintf('VAPCALYEARTOTALEARN', VikAppointments::printPriceCurrencySymb(floatval($statistics->getYearTotalEarning())), $statistics->getYearTotalReservations()); ?>
		</span>
		
		<span class="vapnextyearsp">
			<a href="javascript: void(0);" onClick="loadCalendars(<?php echo $arr['year'] + 1; ?>, true);">
				<i class="fa fa-chevron-right big"></i>
			</a>
		</span>
	</div>
	
	<div class="vapallcaldiv">
		<?php for ($cal = 0; $cal < $ncal; $cal++)
		{ 	
			$num_weeks = 1;
			?>
			
			<div class="vapcalendardiv">
				
				<div class="vapcaltabdiv">
					<table class="vapcaltable">
						<thead class="vaptheadcal">
							<tr>
								<td colspan="7" style="text-align: center;">
									<?php echo $arr['month']; ?> 
								</td>
							</tr>
							<tr>
							<?php for ($i = 0; $i < 7; $i++) { ?>
								<th class="vapthtabcal"><?php echo $_DAY_TEXT[VikAppointments::getShiftedDay($i, $day_shift)]; ?></th>
							<?php } ?>
							</tr>
						</thead>
						
						<tbody class="vaptbodycal">
							<tr>
								<?php
								$cont = 0; 
								for ($i = 0, $n = $DAYS[$arr['wday']]; $i < $n; $i++, $cont++) { ?>
									<td class="vaptdnoday">
										<div class="vapdivday">/</div>
									</td>
								<?php } ?>
								
								<?php 
								$last_month = $arr['mon'];
								while ($arr['mon'] == $last_month)
								{
									if ($cont > 6)
									{
										$cont = 0;
										$num_weeks++;
										?>
										</tr>
										<tr>
										<?php 
									}
									
									$add_class = 'vaptdgrey';
									if (VikAppointments::isTableDayAvailable($selected_employee, $selected_service, $arr[0], $closing_days, $closing_periods, $dbo))
									{
									
										$res = VikAppointments::evaluateBookingArray($bookings, $book_index, $arr[0]);
										
										$add_class = 'vaptdgreen';
										if (count($res[1]) > 0)
										{
											$add_class = 'vaptdred';
											if (VikAppointments::isFreeIntervalOnDay($selected_employee, $selected_service, $res[1], $arr[0], $max_capacity_selected_service))
											{
												$add_class = 'vaptdyellow';
											}
										}
										
										$book_index = $res[0];
									}
									
									?>
									<td class="vaptdday <?php echo $add_class; ?>" id="vapday<?php echo $arr[0]; ?>">
										<a href="javascript: void(0);" onClick="getTimeLine(<?php echo $arr[0]; ?>);">
											<div class="vapdivday">
												<?php echo $arr['mday']; ?>
											</div>
										</a>
									</td>
									<?php
								
									// get next day
									$arr = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime($arr['hours'], $arr['minutes'], 0, $arr['mon'], $arr['mday'] + 1, $arr['year']));

									$cont++;
								}
								
								for ($i = $cont; $i < 7; $i++) { ?>
									<td class="vaptdnoday">
										<div class="vapdivday">/</div>
									</td>
								<?php }
								
								for ($i = 0; $i < 6 - $num_weeks; $i++) { ?>
									<tr style="background-color:transparent; border-style:solid none solid none; border-width:0px 0px 0px 0px">
										<td colspan="7" style="background-color:transparent; border-style:solid none solid none; border-width:0px 0px 0px 0px" class="vaptdday"></td>
									</tr>
								<?php } ?>
							</tr>
							
						</tbody>
					</table>
				</div>
				
				<div class="vapstattabdiv vapstatisticelem" style="<?php echo $stat_displayed; ?>">
					<table class="vapstattable">
						<thead class="vaptheadstat">
							<tr>
								<th class="vapthtabstat"><?php echo JText::_('VAPCALSTATTHEAD1'); ?></th>
								<th class="vapthtabstat"><?php echo JText::_('VAPCALSTATTHEAD2'); ?></th>
								<th class="vapthtabstat"><?php echo JText::_('VAPCALSTATTHEAD3'); ?></th>
							</tr>
						</thead>
						<tbody class="vaptbodystat"
							<tr class="vapstatrowtot">
								<td><?php echo JText::_('VAPCALSTATTOTALLABEL'); ?></td>
								<td><?php echo VikAppointments::printPriceCurrencySymb(floatval($statistics->getMonthTotalEarning($cal + 1))); ?></td>
								<td><?php echo $statistics->getMonthTotalReservations($cal + 1); ?></td>
							</tr>
							<tr class="vapstatrowemptot">
								<td><?php echo JText::_('VAPCALSTATEMPTOTALLABEL'); ?></td>
								<td><?php echo VikAppointments::printPriceCurrencySymb(floatval($statistics->getEmployeeMonthTotalEarning($cal + 1))); ?></td>
								<td><?php echo $statistics->getEmployeeMonthTotalReservations($cal + 1); ?></td>
							</tr>
							<?php foreach ($statistics->getEmployeeMonthServiceArray($cal + 1) as $list) { ?>
								<tr class="vapstatrowsertot">
									<td><?php echo $list['sname']; ?></td>
									<td><?php echo VikAppointments::printPriceCurrencySymb(floatval($list['total'])); ?></td>
									<td><?php echo $list['numres']; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				
			</div>
			
		<?php } ?>
	</div>
	
	<div class="vap-calendar-book">
		<div class="vaptimeline" id="vaptimeline" style="display: inline-block;">
					
		</div>

		<button type="button" class="btn btn-primary" onclick="vapBookNow(this);" style="display: none;" id="booknow"><?php echo JText::_('VAPFINDRESBOOKNOW'); ?></button>
	</div>
	
	<div class="vapreservationslistdiv">
		
	</div>
	
	<input type="hidden" name="day" value="" id="vapdayselected" />
	
	<input type="hidden" name="task" value="newreservation"/>
	<input type="hidden" name="option" value="com_vikappointments"/>
</form>

<?php
JText::script('VAPFINDRESTIMENOAV');
JText::script('VAPFINDRESBOOKNOW');
JText::script('VAPFINDRESNOENOUGHTIME');

JText::script('VAPCALSTATBUTTON0');
JText::script('VAPCALSTATBUTTON1');

JText::script('VAPRESERVATIONREMOVEMESSAGE');
?>

<script>

	jQuery(document).ready(function() {

		jQuery('#vapsersel, #vapempsel').select2({
			allowClear: false,
			width: 200
		});

	});

	function loadCalendars(year, print_ser) {
		var id_emp = jQuery('#vapempsel').val();
		var id_ser = jQuery('#vapsersel').val();
		
		if (print_ser) {
			document.location.href = 'index.php?option=com_vikappointments&task=calendar&id_emp='+id_emp+'&id_ser='+id_ser+'&year='+year;
		} else {
			document.location.href = 'index.php?option=com_vikappointments&task=calendar&id_emp='+id_emp+'&year='+year;
		}
	}

	function getTimeLine(timestamp) {
		
		jQuery('.vaptdday').removeClass('vaptdselected');
		jQuery('#vapday'+timestamp).addClass('vaptdselected');
		
		var id_emp = jQuery('#vapempsel').val();
		var id_ser = jQuery('#vapsersel').val();
		
		jQuery('#vapdayselected').val(timestamp);
		
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=get_day_time_line&tmpl=component",
			data: {
				id_emp: id_emp,
				day: timestamp,
				id_ser: id_ser
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0]) {
				renderTimeLine(obj[1], obj[2], obj[3]);
			} else {
				jQuery('#vapdayselected').val('');
				jQuery('.vaptimeline').html(obj[1]);
			}
		});
		
		getReservationsOnDay(timestamp);
	}

	function renderTimeLine(arr, timeline, newRate) {
		
		jQuery('#vaptimeline').html(timeline);

		// iterate each RED block to support view details action
		jQuery('.vaptlblock0').each(function() {
			jQuery(this).on('click', function() {
				vapViewDetails(jQuery(this).data('hour'), jQuery(this).data('min'), this);
			});
		});

		// animate only in case the timeline is not visible
		var px_to_scroll = isBoxOutOfMonitor(jQuery('#vaptimeline'));
			
		if (px_to_scroll !== false) {
			jQuery('html,body').animate( {scrollTop: "+=" + px_to_scroll}, {duration:'normal'} );
		}
	}

	function getReservationsOnDay(timestamp) {
		var id_emp = jQuery('#vapempsel').val();
		var id_ser = jQuery('#vapsersel').val();
		
		jQuery.noConflict();
		
		jQuery('.vapreservationslistdiv').html('');
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=get_day_reservations&tmpl=component",
			data: { 
				id_emp: id_emp,
				day: timestamp,
				id_ser: id_ser
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0]) {
				jQuery('.vapreservationslistdiv').html(obj[1]);
			} else {
				alert(obj[1]);
			}
		});
	}

	function timeFormat(hour, min) {
		if ('<?php echo $time_format; ?>' == 'H:i') {
			return hour + ':' + ((min < 10) ? '0' : '') + min;
		}
		
		var _th = ( hour > 12 ? hour-12 : hour );
		if (_th < 10) {
			_th = '0'+_th;
		}

		return _th+':'+(min < 10 ? '0' : '')+min+(hour >= 12 ? ' PM' : ' AM');
	}

	function vapTimeClicked(hour, min, block_id) {
		var id_emp 	= jQuery('#vapempsel').val();
		var id_ser 	= jQuery('#vapsersel').val();
		var day 	= jQuery('#vapdayselected').val();

		var slot = jQuery('#vaptimelineblock' + block_id);

		if (slot.hasClass('vaptimeselected')) {
			// unset selection and remove filters
			jQuery('#booknow').hide();
			jQuery('.vap-timeline-block').removeClass('vaptimeselected');
			removeHighlightFromRow();
			return;
		}

		/**
		 * Filter day reservations instead of submitting the form.
		 *
		 * @since 1.6
		 */
		vapViewDetails(hour, min);

		jQuery('.vap-timeline-block').removeClass('vaptimeselected');
		slot.addClass('vaptimeselected');

		jQuery('#booknow')
			.attr('data-emp', id_emp)
			.attr('data-ser', id_ser)
			.attr('data-day', day)
			.attr('data-hour', hour)
			.attr('data-min', min)
			.show();
	}

	function vapBookNow(btn) {
		var url = 'index.php?option=com_vikappointments&from=calendar&task=newreservation';

		url += '&id_emp=' + jQuery(btn).attr('data-emp');
		url += '&id_ser=' + jQuery(btn).attr('data-ser');
		url += '&day=' + jQuery(btn).attr('data-day');
		url += '&hour=' + jQuery(btn).attr('data-hour');
		url += '&min=' + jQuery(btn).attr('data-min');

		document.location.href = url;
	}

	function vapViewDetails(hour, min) {
		var id_emp 	= jQuery('#vapempsel').val();
		var day 	= jQuery('#vapdayselected').val();

		// get records key if cached
		var cached = isResultCached([id_emp, day, hour, min]);
		if (cached) {
			// highlight records with cached values
			highlightRecords(RECORDS_CACHE[cached]);
			return;
		}
		
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=get_reservation_at&tmpl=component",
			data: {
				id_emp: id_emp,
				day: day,
				hour: hour,
				min: min,
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp);
			
			if (obj[0]) {
				
				var arr = obj[1]
				if (!Array.isArray(arr)) {
					arr = [arr];
				}

				highlightRecords(arr);
				// cache result for later use
				cacheResult([id_emp, day, hour, min], arr);

			} else {
				removeHighlightFromRow();
			}
		});
	}

	function highlightRecords(arr) {
		jQuery('.row0, .row1').each(function() {
			if (arr.indexOf(jQuery(this).data('id')) != -1) {
				jQuery(this).show();
			} else {
				jQuery(this).hide();
			}
		});
		
		// animate only in case the (last) box is not visible
		var px_to_scroll = isBoxOutOfMonitor(jQuery('#vaptabrow' + arr[arr.length - 1]));
			
		if (px_to_scroll !== false) {
			jQuery('html,body').animate( {scrollTop: "+=" + px_to_scroll}, {duration:'normal'} );
		}
	}

	function removeHighlightFromRow() {
		jQuery('.row0, .row1').show();
	}

	var RECORDS_CACHE = {};

	function isResultCached(arr) {
		var sign = arr.join(':');

		if (RECORDS_CACHE.hasOwnProperty(sign)) {
			return sign;
		}

		return false;
	}

	function cacheResult(arr, obj) {
		var sign = arr.join(':');
		RECORDS_CACHE[sign] = obj;
	}

	function serviceValueChanged() {
		loadCalendars(<?php echo $selected_year; ?>, true);
	}

	var is_stat = <?php echo $is_stat; ?>;

	var STAT_TEXT_ARR = new Array(
		Joomla.JText._('VAPCALSTATBUTTON0'),
		Joomla.JText._('VAPCALSTATBUTTON1')
	);

	function vapSwitchLayout() {
		document.location.href = 'index.php?option=com_vikappointments&task=switchcal&layout=caldays';
	}

	function vapChangeStatistics() {
		is_stat = (is_stat + 1) % 2;
		jQuery('#vapcalstatbtn').text(STAT_TEXT_ARR[is_stat]);
		
		if (is_stat) {
			jQuery('.vapstatisticelem').fadeIn();
		} else {
			jQuery('.vapstatisticelem').fadeOut();
		}
		
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=save_is_stat&tmpl=component",
			data: {
				is_stat: is_stat
			}
		});
	}

	function onDeleteReservation(id) {
		if (!confirm(Joomla.JText._('VAPRESERVATIONREMOVEMESSAGE'))) {
			return;
		}

		var link = 'index.php?option=com_vikappointments&task=deleteReservations&from=calendar&cid[]=' + id;

		document.location.href = link;
	}

	function onEditReservation(id) {
		var link = 'index.php?option=com_vikappointments&task=editreservation&from=calendar&cid[]=' + id;

		document.location.href = link;
	}
	
</script>
