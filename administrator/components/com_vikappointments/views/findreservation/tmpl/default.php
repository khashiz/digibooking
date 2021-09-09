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

$services  = $this->services;
$employees = $this->employees;
$bookings  = $this->bookings;

$selected_employee 	= $this->id_emp;
$selected_service 	= $this->id_ser;
$selected_res 		= $this->id_res;
$last_day 			= $this->last_day;

$max_capacity_selected_service = (count($services) ? $services[0]['max_capacity'] : 0);

// build service select
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

// build employee select
$employee_select = '<select name="option" id="vapempsel" onChange="loadCalendars(' . ($this->searchMode == 1 ? 'false' : 'true') . ');">';

if ($this->searchMode == 2 && count($employees) != 1)
{
	$employee_select .= '<option></option>';
}

foreach ($employees as $e)
{
	$employee_select .= '<option value="' . $e['id'] . '" ' . ($e['id'] == $selected_employee ? 'selected="selected"' : '') . '>' . $e['nickname'] . '</option>';
}

$employee_select .= '</select>';

//

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

$arr = ArasJoomlaVikApp::jgetdate();
$arr = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime(0, 0, 0, $arr['mon'], 1, $arr['year']));

$_SINGLE_DAY_SEC = 86400;

$book_index = 0;

$closing_days 		= VikAppointments::getClosingDays();
$closing_periods 	= VikAppointments::getClosingPeriods();

$dbo = JFactory::getDbo();

$time_format = VikAppointments::getTimeFormat();

if ($selected_service <= 0 && count($services) > 0)
{
	$selected_service = $services[0]['id'];
}

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<?php if (count($services) > 0 || count($employees) > 0) { ?>

		<div class="btn-toolbar vapresfiltertoolbar" id="filter-bar" style="font-size:inherit;">
			
			<div class="btn-group pull-left" style="font-size:inherit;">
				<?php echo ($this->searchMode == 1 ? $employee_select : $service_select); ?>
			</div>

			<div class="btn-group pull-left" style="font-size:inherit;">
				<?php echo ($this->searchMode == 1 ? $service_select : $employee_select); ?>
			</div>

			<div class="btn-group pull-left">
				<input type="number" name="people" value="<?php echo $this->people_sel_res; ?>" min="1" max="<?php echo $max_capacity_selected_service; ?>" id="vapinputpeople" onChange="peopleValueChanged();"
					title="" data-original-title="<?php echo JText::_('VAPMANAGERESERVATION25'); ?>"/>
			</div>

			<div class="btn-group pull-right">
				<button type="button" class="btn" onClick="changeSearchMode();">
					<i class="icon-loop"></i>&nbsp;<?php echo JText::_('VAPFINDRESREVSEARCH'); ?>
				</button>
			</div>
		</div>

	<?php } ?>
	
	<div class="vapallcaldiv">
		<?php for ($cal = 0; $cal < $ncal; $cal++) { ?>
			
			<div class="vapcalendardiv">
				<table class="vapcaltable">
					<thead class="vaptheadcal">
						<tr>
							<td colspan="7" style="text-align: center;">
								<?php echo JText::_($arr['month']) . ' - ' . $arr['year']; ?>
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
									// close and re-open row as we reached the limit (7)
									?>
									</tr>
									<tr>
									<?php 
								}
								
								// EVALUATING DAY STATUS
								
								$add_class = 'vaptdgrey';
								
								$day_available = false;

								if ($selected_employee != -1)
								{
									$day_available = VikAppointments::isTableDayAvailable($selected_employee, $selected_service, $arr[0], $closing_days, $closing_periods, $dbo);
								}
								else
								{
									$day_available = VikAppointments::isGenericTableDayAvailable($employees, $selected_service, $arr[0], $closing_days, $closing_periods, $dbo);
								}
								
								if ($day_available)
								{
									$res = VikAppointments::evaluateBookingArray($bookings, $book_index, $arr[0]);
									
									$add_class = 'vaptdgreen';

									if (count($res[1]))
									{
										$add_class = 'vaptdred';

										if ($this->searchMode == 1)
										{
											if (VikAppointments::isFreeIntervalOnDay($selected_employee, $selected_service, $res[1], $arr[0], $max_capacity_selected_service))
											{
												$add_class = 'vaptdyellow';
											}
										}
										else
										{
											if ($max_capacity_selected_service == 1)
											{
												if (VikAppointments::isFreeIntervalOnDayService($employees, $selected_service, $res[1], $arr[0]))
												{
													$add_class = 'vaptdyellow';
												}
											}
											else
											{
												if (VikAppointments::isFreeIntervalOnDayGroupService($employees, $selected_service, $res[1], $arr[0], $max_capacity_selected_service))
												{
													$add_class = 'vaptdyellow';
												}
											}
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
							?>
						</tr>
					</tbody>

				</table>
			</div>
			
		<?php } ?>
	</div>
	
	<div class="vaptimeline" id="vaptimeline">
				
	</div>

	<div class="vaptimeline-hover-tip" style="display:none;">
		<i class="fa fa-support"></i>&nbsp;
		<?php echo JText::_('VAPTIMELINEHOVERTIP'); ?>
	</div>
	
	<input type="hidden" name="day" value="" id="vapdayselected" />
	<input type="hidden" name="id_res" value="<?php echo $selected_res; ?>" />
	
	<input type="hidden" name="task" value="newreservation" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="searchmode" value="<?php echo $this->searchMode; ?>" id="vapsearchmode" />
</form>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-respinfo',
	array(
		'title'       => JText::_('VAPMANAGERESERVATIONTITLE1'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<?php
JText::script('VAPFINDRESALLEMPLOYEES');
JText::script('VAPFINDRESTIMENOAV');
JText::script('VAPFINDRESBOOKNOW');
JText::script('VAPFINDRESNOENOUGHTIME');
JText::script('VAPCONNECTIONLOSTERROR');
JText::script('VAPMANAGECUSTOMER15');
JText::script('VAPGUESTS');
?>

<script>

	var CAPACITY = new Array(); 

	jQuery(document).ready(function(){
		
		jQuery('#vapinputpeople').tooltip();
		
		<?php foreach($services as $s ) { ?>
			CAPACITY[CAPACITY.length] = <?php echo $s['max_capacity']; ?>;
		<?php } ?>
		
		var day = '<?php echo $last_day; ?>';

		if (day.length > 0) {
			getTimeLine(parseInt(day));
		}
	});

	function changeSearchMode() {
		var mode = (jQuery('#vapsearchmode').val() == 1 ? 2 : 1);
		var id_emp = jQuery('#vapempsel').val();
		var id_ser = jQuery('#vapsersel').val();
		var id_res = <?php echo (!empty($selected_res) ? $selected_res : -1); ?>;
		
		document.location.href = 'index.php?option=com_vikappointments&task=findreservation&id_emp='+id_emp+'&id_ser='+id_ser+'&id_res='+id_res+'&searchmode='+mode;
	}

	function loadCalendars(print_ser) {
		var id_emp = jQuery('#vapempsel').val();
		var id_ser = jQuery('#vapsersel').val();
		var id_res = <?php echo (!empty($selected_res) ? $selected_res : -1); ?>;
		var mode = jQuery('#vapsearchmode').val();
		
		if (print_ser) {
			document.location.href = 'index.php?option=com_vikappointments&task=findreservation&id_emp='+id_emp+'&id_ser='+id_ser+'&id_res='+id_res+'&searchmode='+mode;
		} else {
			document.location.href = 'index.php?option=com_vikappointments&task=findreservation&id_emp='+id_emp+'&id_res='+id_res+'&searchmode='+mode;
		}
	}

	function getTimeLine(timestamp) {
		
		jQuery('.vaptdday').removeClass('vaptdselected');
		jQuery('#vapday' + timestamp).addClass('vaptdselected');
		
		var id_emp = jQuery('#vapempsel').val();
		var id_ser = jQuery('#vapsersel').val();
		var id_res = <?php echo (!empty($selected_res) ? $selected_res : -1); ?>;
		var people = jQuery('#vapinputpeople').val();
		
		jQuery('#vapdayselected').val(timestamp);
		
		jQuery.noConflict();
		
		if (id_emp > 0) {

			var jqxhr = jQuery.ajax({
				type: "POST",
				url: "index.php?option=com_vikappointments&task=get_day_time_line&tmpl=component",
				data: {
					id_emp: id_emp,
					day: timestamp,
					id_ser: id_ser,
					id_res: id_res,
					people: people
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

		} else {
			
			var jqxhr = jQuery.ajax({
				type: "POST",
				url: "index.php?option=com_vikappointments&task=get_day_time_line_all_employees&tmpl=component",
				data: { 
					day: timestamp,
					id_ser: id_ser,
					id_res: id_res,
					people: people
				}
			}).done(function(resp) {
				var obj = jQuery.parseJSON(resp);
				
				if (obj[0]) {
					renderEmployeesTimeLine(obj[1]);
				} else {
					jQuery('#vapdayselected').val('');
					jQuery('.vaptimeline').html(obj[1]);
				}
			});
		}
	}

	function renderTimeLine(arr, timeline, newRate) {
		
		jQuery('#vaptimeline').html(timeline);

		// iterate each RED block to support view details action
		jQuery('.vaptlblock0').each(function() {
			jQuery(this).on('click', function() {
				vapViewDetails(jQuery(this).data('hour'), jQuery(this).data('min'), this);
			});
		});

		// iterate each block with at least an occupied seat (only when max capacity > 1)
		var count = 0;
		jQuery('.vap-timeline-block[data-seats!="0"]').each(function() {
			if (jQuery(this).data('seats')) {
				// proceed only if the slot owns data-seats attr
				jQuery(this).hover(function() {
					onSlotHover(this);
				}, function() {
					onSlotLeave();
				});

				count++;
			}
		});

		if (count) {
			// display "hover" label
			jQuery('.vaptimeline-hover-tip').show();
		} else {
			// hide "hover" label
			jQuery('.vaptimeline-hover-tip').hide();
		}

		// animate only in case the timeline is not visible
		var px_to_scroll = isBoxOutOfMonitor(jQuery('#vaptimeline'));
			
		if (px_to_scroll !== false) {
			jQuery('html,body').animate( {scrollTop: "+=" + px_to_scroll}, {duration:'normal'} );
		}
	}

	function renderEmployeesTimeLine(arr) {
		
		var timeline = '';
		
		for (var w = 0; w < arr.length; w++) {
			if (arr[w].timeline.length != 0) {
				timeline += '<div class="vaptimeline-empblock" data-id="' + arr[w].id + '">\n';
				timeline += '<h3>' + arr[w].label + '</h3>\n';
				timeline += '<div class="vaptimelinewt">\n';
				timeline += arr[w].html;
				timeline += '</div>';
				timeline += '</div>';
			}
		}

		renderTimeLine([], timeline, 0);

		// unset onclick event as vapTimeClicked needs to be replaced
		jQuery('a[onclick^="vapTimeClicked"]').attr('onclick', '');

		// iterate each GREEN block to override time click event
		jQuery('.vaptlblock1').each(function() {
			jQuery(this).on('click', function() {
				employeeTimeClicked(
					jQuery(this).closest('.vaptimeline-empblock').data('id'),
					jQuery(this).data('hour'),
					jQuery(this).data('min')
				);
			});
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

	function vapTimeClicked(hour, min, id) {
		var id_emp = jQuery('#vapempsel').val();

		employeeTimeClicked(id_emp, hour, min);
	}

	function vapViewDetails(hour, min, btn) {
		var id_emp = parseInt(jQuery('#vapempsel').val());

		if (isNaN(id_emp) || id_emp <= 0) {
			// fall back to obtain the employee ID
			id_emp = jQuery(btn).closest('.vaptimeline-empblock').data('id');
		}

		employeeViewDetails(id_emp, hour, min);
	}

	function employeeTimeClicked(id_emp, hour, min) {
		var id_ser 	= jQuery('#vapsersel').val();
		var day 	= jQuery('#vapdayselected').val();
		var people 	= jQuery('#vapinputpeople').val();
		
		var cid 	= '';
		var res_id 	= <?php echo (int) $selected_res; ?>;
		var task 	= 'newreservation';

		if (res_id != -1) {
			cid  = '&cid[]='+ res_id;
			task = 'editreservation';
		}

		// close overlay in order to abort any pending AJAX request
		closeSmartOverlay();
		
		document.location.href = 'index.php?option=com_vikappointments&task='+task+'&id_emp='+id_emp+'&id_ser='+id_ser+'&day='+day+'&hour='+hour+'&min='+min+'&people='+people+cid;
	}

	function employeeViewDetails(id_emp, hour, min) {
		var day = jQuery('#vapdayselected').val();
		
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=get_appointment_details&tmpl=component",
			data: {
				id_emp: id_emp,
				day: day,
				hour: hour,
				min: min
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0]) {
				displayDetailsView(obj[1], day);
			} else {
				alert(obj[1]);
			}
		});
	}

	jQuery(document).ready(function() {

		jQuery('#vapsersel').select2({
			allowClear: false,
			width: 300
		});

		jQuery('#vapempsel').select2({
			placeholder: Joomla.JText._('VAPFINDRESALLEMPLOYEES'),
			allowClear: true,
			width: 300
		});
		
	});

	var QUERY_STRING = "";

	function displayDetailsView(rid, day) {
		QUERY_STRING = '';
		for (var i = 0; i < rid.length; i++) {
			QUERY_STRING += '&oid[]=' + rid[i]['rid'];
		}

		vapOpenJModal('respinfo', null, true);
	}

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'respinfo') {
			url = 'index.php?option=com_vikappointments&task=purchaserinfo&joomla3810t_btns=1&tmpl=component'+QUERY_STRING;
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

	function serviceValueChanged() {
		/*
		var x = document.getElementById("vapsersel").selectedIndex;
		jQuery('#vapinputpeople').prop('max', CAPACITY[x]);
		
		var day = jQuery('#vapdayselected').val();
		if( day.length > 0 ) {
			getTimeLine( day );
		}*/
		loadCalendars(true);
	}

	function peopleValueChanged() {
		var day = jQuery('#vapdayselected').val();
		if( day.length > 0 ) {
			getTimeLine(parseInt(day));
		}
	}

	/* GROUP INFO EVENTS */

	var SLOT_HOVER_TIMEOUT 	= null;
	var SLOT_CURRENT_TARGET = null;
	var SLOT_AJAX_HANDLE 	= null;
	var SLOTS_CACHE 		= {};

	function onSlotHover(slot) {
		SLOT_HOVER_TIMEOUT = setTimeout(function() {
			if (slot == SLOT_CURRENT_TARGET) {
				return;
			}

			openSmartOverlay(slot);
		}, 1000);
	}

	function onSlotLeave() {
		clearTimeout(SLOT_HOVER_TIMEOUT);
	}

	function openSmartOverlay(slot) {
		closeSmartOverlay();

		SLOT_CURRENT_TARGET = slot;

		var html = '<div class="smart-overlay-loading">\n'+
			'<i class="fa fa-circle-o-notch fa-spin fa-3x" style="font-size: 32px;"></i>\n'+
		'</div>';

		jQuery('#adminForm').append('<div class="smart-overlay">' + html + '</div>');

		var overlay = jQuery('.smart-overlay');

		calculateOverlayPosition(overlay);

		overlay.on('click', function(event) {
			event.preventDefault();
			event.stopPropagation();
		});

		loadSmartOverlayData(overlay);
	}

	function calculateOverlayPosition(overlay) {
		if (!SLOT_CURRENT_TARGET) {
			return;
		}

		var offset = jQuery(SLOT_CURRENT_TARGET).offset();

		var left = offset.left;

		if (left + overlay.width() >= jQuery(window).width() - 5) {
			left = jQuery(window).width() - overlay.width() - 5;
		}

		overlay.css('top', (offset.top - overlay.height() - 5) + 'px');
		overlay.css('left', left + 'px');
	}

	function loadSmartOverlayData(overlay) {
		var id_emp = parseInt(jQuery('#vapempsel').val());

		if (isNaN(id_emp) || id_emp <= 0) {
			// fall back to obtain the employee ID
			id_emp = jQuery(SLOT_CURRENT_TARGET).closest('.vaptimeline-empblock').data('id');
		}

		var day  = jQuery('#vapdayselected').val();
		var hour = jQuery(SLOT_CURRENT_TARGET).data('hour');
		var min  = jQuery(SLOT_CURRENT_TARGET).data('min');

		// get records key if cached
		var cached = isResultCached([id_emp, day, hour, min]);
		if (cached) {
			// fill smart overlay with cached values
			fillSmartOverlay(SLOTS_CACHE[cached], overlay);
			return;
		}

		SLOT_AJAX_HANDLE = jQuery.ajax({
			type: 'post',
			url: 'index.php?option=com_vikappointments&task=get_reservations_det_at&tmpl=component',
			data: {
				id_emp: id_emp,
				day: day,
				hour: hour,
				min: min
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp);
			
			fillSmartOverlay(obj, overlay);
			// cache result for later use
			cacheResult([id_emp, day, hour, min], obj);

		}).fail(function(resp) {
			if (resp.statusText != 'abort') {
				console.log(resp, resp.responseText);
				alert(Joomla.JText._('VAPCONNECTIONLOSTERROR'));
			}
		});
	}

	function fillSmartOverlay(obj, overlay) {
		var html = '';

		var _guest  = Joomla.JText._('VAPMANAGECUSTOMER15').toLowerCase();
		var _guests = Joomla.JText._('VAPGUESTS').toLowerCase();

		for (var i = 0; i < obj.length; i++) {
			html += '<div class="overlay-record">\n';
			html += '<div class="record-column col-id">#' + obj[i].id + '</div>\n';
			html += '<div class="record-column col-name">' + obj[i].name + '</div>\n';
			if (obj[i].id_service == <?php echo $this->id_ser; ?>) {
				html += '<div class="record-column col-count">' + obj[i].people + ' ' + (obj.people > 1 ? _guest : _guests) + '</div>\n';
			} else {
				html += '<div class="record-column col-count">' + obj[i].service_name + '</div>\n';
			}
			html += '</div>\n';
		}

		overlay.html(html);
		calculateOverlayPosition(overlay);
	}

	function closeSmartOverlay() {
		jQuery('.smart-overlay').remove();
		SLOT_CURRENT_TARGET = null;

		if (SLOT_AJAX_HANDLE) {
			SLOT_AJAX_HANDLE.abort();
			SLOT_AJAX_HANDLE = null;
		}
	}

	jQuery(window).click(function() {
		closeSmartOverlay();
	});

	jQuery(window).on('resize', function() {
		calculateOverlayPosition(jQuery('.smart-overlay'));
	});

	function isResultCached(arr) {
		var sign = arr.join(':');

		if (SLOTS_CACHE.hasOwnProperty(sign)) {
			return sign;
		}

		return false;
	}

	function cacheResult(arr, obj) {
		var sign = arr.join(':');
		SLOTS_CACHE[sign] = obj;
	}
	
</script>
