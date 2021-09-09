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

$auth 		= $this->auth;
$employee 	= $auth->getEmployee();

if (!$auth->isEmployee())
{
	exit ('No direct access');
}

$config = UIFactory::getConfig();
	
// SET EMPLOYEE TIMEZONE
VikAppointments::setCurrentTimezone($employee['timezone']); 

$services 			= $this->services;
$id_res 			= $this->id_res;
$selected_service 	= $this->id_ser;
$selected_year 		= $this->year;
$bookings 			= $this->bookings;
$upcoming 			= $this->upcoming;

$old_gid 	= '';
$grp_opened = false;

$curr_service = null;
$max_people_service_selected = count($services) ? $services[0]['max_capacity'] : 0;

$service_select = '<select name="service" id="vapsersel" onChange="vapServiceValueChanged();">';
foreach ($services as $s)
{
	if (empty($old_gid) || $old_gid != $s['gid'])
	{
		if (!empty($old_gid) && $grp_opened)
		{
			$service_select .= '</optgroup>';
			$grp_opened = false;
		}
		
		if (strlen($s['gname']))
		{
			$service_select .= '<optgroup label="'.$s['gname'].'">';
			$grp_opened = true;
		}
		
		$old_gid = $s['gid'];
	}
	
	$_price = '';
	if ($s['price'] > 0)
	{
		$_price = ' ' . VikAppointments::printPriceCurrencySymb($s['price']);
	}
	
	if ($s['sid'] == $selected_service)
	{
		$max_people_service_selected = $s['max_capacity'];
		$curr_service = $s;
	}
	
	$service_select .= "<option value=\"{$s['sid']}\" " . ($s['sid'] == $selected_service ? 'selected="selected"' : '') . ">{$s['sname']} {$_price} ({$s['duration']} " . JText::_('VAPSHORTCUTMINUTE') . ")</option>";
}
$service_select .= '</optgroup>';
$service_select .= '</select>';

// people select
$people_select = '';
if ($max_people_service_selected > 1 && $curr_service['min_per_res'] < $curr_service['max_per_res'])
{
	$people_select = '<select name="people" id="vapserpeopleselect" onChange="vapPeopleValueChanged();" />';
	for ($i = $curr_service['min_per_res']; $i <= $curr_service['max_per_res']; $i++)
	{
		$people_select .= '<option value="' . $i . '">' . $i . " " . strtolower(JText::_($i > 1 ? 'VAPSUMMARYPEOPLE' : 'VAPSUMMARYPERSON')) . '</option>';
	}
	$people_select .= '</select>';
}
//

VikAppointments::setCurrentTimezone($employee['timezone']);
$arr = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime(0, 0, 0, $this->selMonth, 1, $selected_year));

$ncal = $this->empSettings['numcals'];
$months_select = '';
if ($ncal < 12)
{
	$months_select = '<select name="month" id="vap-month-sel" onChange="vapLoadCalendars(' . $selected_year . ');">';
	for ($i = 1; $i <= 12; $i++)
	{
		$months_select .= '<option value="' . $i . '" ' . ($i == $this->selMonth ? 'selected="selected"' : '') . '>' . JText::_(ArasJoomlaVikApp::getMonthName($i)) . '</option>';
	}
	$months_select .= '</select>';
}

$day_shift = 0;//VikAppointments::getCalendarFirstWeekDay();

$DAYS = array();
for ($i = 0; $i < 7; $i++)
{
	$DAYS[$i] = (6 - ($day_shift - $i) + 1) % 7;
	/**
	 * 
	 *  DAY = ( (NUM_DAYS-1) - ( SHIFT - DAY_INDEX ) + 1 ) % NUM_DAYS 
	 *
	 *  SATURDAY
	 *  0               1               2               3               4               5               6
	 *  6-(6-0)+1%7=1   6-(6-1)+1%7=2   6-(6-2)+1%7=3   6-(6-3)+1%7=4   6-(6-4)+1%7=5   6-(6-5)+1%7=6   6-(6-6)+1%7=0
	 * 
	 *  SUNDAY
	 *  0               1               2               3               4               5               6
	 *  6-(0-0)+1%7=0   6-(0-1)+1%7=1   6-(0-2)+1%7=2   6-(0-3)+1%7=3   6-(0-4)+1%7=4   6-(0-5)+1%7=5   6-(0-6)+1%7=6
	 * 
	 *  MONDAY
	 *  0               1               2               3               4               5               6
	 *  6-(1-0)+1%7=6   6-(1-1)+1%7=0   6-(1-2)+1%7=1   6-(1-3)+1%7=2   6-(1-4)+1%7=3   6-(1-5)+1%7=4   6-(1-6)+1%7=5
	 * 
	 *  WEDNESDAY
	 *  0               1               2               3               4               5               6
	 *  6-(3-0)+1%7=4   6-(3-1)+1%7=5   6-(3-2)+1%7=6   6-(3-3)+1%7=0   6-(3-4)+1%7=1   6-(3-5)+1%7=2   6-(3-6)+1%7=3
	 */
}

$_DAY_TEXT = ArasJoomlaVikApp::weekDays();

// for ($i = 0; $i < 7; $i++)
// {
// 	$_DAY_TEXT[$i] = mb_substr( $_DAY_TEXT[$i], 0, 3, 'UTF-8' );
// }

$_SINGLE_DAY_SEC = 86400;

$book_index = 0;

$closing_days 	 = VikAppointments::getClosingDays();
$closing_periods = VikAppointments::getClosingPeriods();

$dbo = JFactory::getDbo();

if ($selected_service <= 0 && count($services))
{
	$selected_service = $services[0]['sid'];
}

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$dt_format = $date_format . " " . $time_format;

$itemid = JFactory::getApplication()->input->getInt('Itemid', 0);

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
echo JLayoutHelper::render('emparea.toolbar');
?>

<?php if ($employee['active_to'] >= 0 && $employee['active_to'] <= time()) { ?>

	<div class="vap-employee-activate">
		<span class="vap-activate-message"><?php echo JText::_('VAPACTIVATEPROFILEMSG'); ?></span>
		<span class="vap-activate-button">
			<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empsubscr'); ?>">
				<?php echo JText::_('VAPACTIVATEPROFILEBTN'); ?>
			</a>
		</span>
	</div>

<?php } ?>

<div class="vap-emplogin-dash">
	<div class="vap-allorders-list vap-emplogin-orderslist">
		<?php foreach ($upcoming as $kk => $ord) { ?>
			<div class="vap-allorders-singlerow vap-allorders-row<?php echo (($kk+1)%2); ?>">
				<span class="vap-allorders-column" style="width: 25%;">
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empmanres&id_res=' . $ord['id']); ?>">
						<?php echo $ord['id'] . "-" . $ord['sid']; ?>
					</a>
				</span>
				<span class="vap-allorders-column" style="width: 20%;">
					<?php
					VikAppointments::setCurrentTimezone($employee['timezone']);
					echo ArasJoomlaVikApp::jdate($dt_format, $ord['checkin_ts']);
					?>
				</span>
				<span class="vap-allorders-column" style="width: 27%;"><?php echo $ord['sername']; ?></span>
				<span class="vap-allorders-column vap-allorders-status<?php echo strtolower($ord['status']); ?>" style="width: 15%;">
					<?php echo strtoupper(JText::_('VAPSTATUS' . $ord['status'])); ?>
				</span>
				<span class="vap-allorders-column" style="width: 10%;">
					<?php if ($ord['total_cost'] > 0) {
						echo VikAppointments::printPriceCurrencySymb($ord['total_cost']);
					} ?>
				</span>
			</div>
		<?php } ?>
	</div>

	<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid); ?>" method="post">
		<?php echo JHtml::_('form.token'); ?>
		<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
	</form>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid); ?>" method="post" name="empareaForm" id="empareaForm">

	<div class="vapfiltersdiv">
	
		<div class="vepserfilterdiv">
			<span class="vapserselectsp"><?php echo $service_select; ?></span>

			<?php if (strlen($months_select)) { ?>
				<span class="vapserselectsp"><?php echo $months_select; ?></span>
			<?php } ?>

			<?php if (strlen($people_select)) { ?>
				<span class="vapserselectsp"><?php echo $people_select; ?></span>
			<?php } ?>
		</div>
	
	</div>
	
	<div class="vapallcalhead" style="font-size: 18px;">
		<span class="vapprevyearsp">
			<a href="javascript: void(0);" onClick="vapLoadCalendars(<?php echo $arr['year']-1; ?>);"><i class="fa fa-chevron-left big"></i></a>
		</span>
		
		<span class="vaptitleyearsp">
			<?php echo $arr['year']; ?>
		</span>
		
		<span class="vapnextyearsp">
			<a href="javascript: void(0);" onClick="vapLoadCalendars(<?php echo $arr['year']+1; ?>);"><i class="fa fa-chevron-right big"></i></a>
		</span>
	</div>
	
	<!-- START CALENDAR -->
	
	<div class="vapallcaldiv">
	<?php for ($cal = 0; $cal < $ncal; $cal++)
	{ 	
		$num_weeks = 1;	
		?>
		
		<div class="vapcalendardiv vapcaldivemplogin">
			
			<div class="vapcaltabdiv">
				<table class="vapcaltable">
					<thead class="vaptheadcal">
						<tr>
							<td colspan="7" style="text-align: center;">
								<?php echo JText::_( $arr['month']); ?>
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
							for ($i = 0, $cont = 0, $n = $DAYS[$arr['wday']]; $i < $n; $i++, $cont++) { ?>
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

								VikAppointments::setCurrentTimezone($employee['timezone']);
								
								$add_class = 'vaptdgrey';
								if (VikAppointments::isTableDayAvailable($employee['id'], $selected_service, $arr[0], $closing_days, $closing_periods, $dbo))
								{
									$res = VikAppointments::evaluateBookingArray($bookings, $book_index, $arr[0]);
									
									$add_class = 'vaptdgreen';
									if (count($res[1]))
									{
										$add_class = 'vaptdred';
										if (VikAppointments::isFreeIntervalOnDay($employee['id'], $selected_service, $res[1], $arr[0], $max_people_service_selected ))
										{
											$add_class = 'vaptdyellow';
										}
									}
									
									$book_index = $res[0];
								}
								
								?><td class="vaptdday <?php echo $add_class; ?>" id="vapday<?php echo $arr[0]; ?>">
									<a href="javascript: void(0);" onClick="vapGetTimeLine(<?php echo $arr[0]; ?>);">
										<div class="vapdivday">
											<?php echo $arr['mday']; ?>
										</div>
									</a>
								</td><?php
							
								/**
								* @deprecated
								*$curr_daylight = date('I', $arr[0]);
								*$next_daylight = date('I', $arr[0] + $_SINGLE_DAY_SEC);
								*$daylight = 0;
								*
								*if( $curr_daylight != $next_daylight ) {
								*	if( $next_daylight == 1 ) {
								*		$daylight = -3600;
								*	} else {
								*		$daylight = +3600;
								*	}
								*}
								*$arr = getdate( $arr[0] + $_SINGLE_DAY_SEC + $daylight );
								*/
								
								VikAppointments::setCurrentTimezone($employee['timezone']);
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
									<td colspan="7" style="background-color:transparent; border-style:solid none solid none; border-width:0px 0px 0px 0px" class="vaptdnoday">
										<div class="vapdivday">&nbsp;</div>
									</td>
								</tr>
							<?php } ?>
						</tr>
					</tbody>
				</table>
			</div>
			
		</div>
		
	<?php } ?>
	
	</div>
	
	<div class="vaptimeline" id="vaptimeline">
				
	</div>
	
	<div class="vapreservationslistdiv">
		
	</div>
	
	<div id="vap-token-separator"></div>
	
	<input type="hidden" name="day" value="" id="vapdayselected" />

	<?php if ($id_res > 0) { ?>
		<input type="hidden" name="id_res" value="<?php echo $id_res; ?>" />
	<?php } ?>

	<input type="hidden" name="year" value="<?php echo $this->year; ?>" />

	<?php if (!$months_select) { ?>
		<input type="hidden" name="month" value="<?php echo $this->selMonth; ?>" />
	<?php } ?>
	
	<!--<input type="hidden" name="task" value="" />-->
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<?php // END CALENDAR ?>

<script>
	
	var _LIST_POSITION_ = <?php echo $this->empSettings['listposition']; ?>;
	
	jQuery(document).ready(function() {
		if (_LIST_POSITION_ == 2) {
			jQuery('.vap-emplogin-dash').insertAfter('#vap-token-separator');
		}

		if (<?php echo (int) $this->lastDay; ?> > 0) {
			vapGetTimeLine(<?php echo $this->lastDay; ?>)
		}

		jQuery('#vapsersel').select2({
			allowClear: false,
			width: 300
		});

		jQuery('#vap-month-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery('#vapserpeopleselect').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});
	});
	
	function vapLoadCalendars(year) {
		if (year) {
			document.empareaForm.year.value = year;
		}

		document.empareaForm.submit();
	}
	
	function vapLoadMonth(year, month) {
		document.empareaForm.month.value = month;
		vapLoadCalendars(year);
	}

	function vapServiceValueChanged() {
		vapLoadCalendars();
	}

	<?php
	/**
	 * In case of concurrent requests, the confirmation form
	 * may be overwritten when the first request made ends afters
	 * the second one.
	 * So, we should abort any connections every time we request
	 * for a new timeline.
	 *
	 * @since 1.6.2
	 */
	?>
	var TIMELINE_XHR = null;
	
	function vapGetTimeLine(timestamp) {

		if (TIMELINE_XHR !== null) {
			// abort previous request
			TIMELINE_XHR.abort();
		}
		
		jQuery('.vaptdday').removeClass('vaptdselected');
		jQuery('#vapday'+timestamp).addClass('vaptdselected');
		
		var id_emp = <?php echo $employee['id']; ?>;
		var id_ser = jQuery('#vapsersel').val();

		var people = 1;
		if (<?php echo ($curr_service['max_capacity'] > 1 ? 1 : 0); ?>) {
			people = jQuery('#vapserpeopleselect').val();
		}
		
		jQuery('#vapdayselected').val(timestamp);
		
		jQuery.noConflict();
				
		TIMELINE_XHR = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=get_day_time_line&tmpl=component', false); ?>",
			data: {
				id_emp: id_emp,
				day: timestamp,
				id_ser: id_ser,
				people: people
			}
		}).done(function(resp){
			TIMELINE_XHR = null;

			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0]) {
				vapRenderTimeLine(obj[1], obj[2], obj[3]);
			} else {
				jQuery('#vapdayselected').val('');
				jQuery('.vaptimeline').html(obj[1]);
			}

			// animate only in case the timeline is not visible
			var px_to_scroll = isBoxOutOfMonitor(jQuery('#vaptimeline'));
				
			if (px_to_scroll !== false) {
				jQuery('html,body').animate( {scrollTop: "+=" + px_to_scroll}, {duration:'normal'} );
			}
			
		}).fail(function(resp) {
			TIMELINE_XHR = null;

			/**
			 * @todo retries automatically on connection lost error
			 */
		});
		
		vapGetReservationsOnDay(timestamp);
	}
	
	function vapRenderTimeLine(arr, timeline, newRate) {
		
		// var timeline = '';
		
		// var TITLES = new Array(
		// 	"<?php echo JText::_('VAPFINDRESTIMENOAV'); ?>",
		// 	"<?php echo JText::_('VAPFINDRESBOOKNOW'); ?>",
		// 	"<?php echo JText::_('VAPFINDRESNOENOUGHTIME'); ?>"
		// );
		
		// for (var w = 0; w < arr.length; w++) {
		// 	timeline += '<div class="vaptimelinewt">';
		// 	jQuery.each(arr[w], function(key, val) {
		// 		var hour = parseInt(key/60);
		// 		var min = key%60;
				
		// 		var _function = 'vapTimeClicked('+hour+', '+min+');';
		// 		if (val == 0) {
		// 			_function = 'vapViewDetails('+hour+', '+min+');';
		// 		} else if (val == 2) {
		// 			_function = '';
		// 		}
				
		// 		timeline += '<a href="javascript: void(0);" title="'+TITLES[val]+'" onClick="'+_function+'"><div class="vap-timeline-block<?php echo ($time_format != "H:i" ? " large" : ""); ?> vaptlblock'+val+'">'+vapTimeFormat(hour,min)+'</div></a>';
		// 	});

		// 	timeline += '</div>';
					
		// 	jQuery('#vaptimeline').html(timeline);
		// }

		jQuery('#vaptimeline').html(timeline);

		// iterate each RED block to support view details action
		jQuery('.vaptlblock0').each(function() {
			jQuery(this).on('click', function() {
				vapViewDetails(jQuery(this).data('hour'), jQuery(this).data('min'));
			});
		});
	}
	
	function vapGetReservationsOnDay(timestamp) {
		
		var id_emp = <?php echo $employee['id']; ?>;
		var id_ser = jQuery('#vapsersel').val();
		jQuery.noConflict();
		
		jQuery('.vapreservationslistdiv').html('');
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=emplogin.getDayReservations&tmpl=component', false); ?>",
			data: {
				id_emp: id_emp,
				day: timestamp,
				id_ser: id_ser,
				Itemid: <?php echo $itemid; ?>
			}
		}).done(function(resp){
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0]) {
				jQuery('.vapreservationslistdiv').html( obj[1] );
			} 
		});
	}
	
	function vapTimeFormat(hour, min) {
		return getFormattedTime(hour, min, "<?php echo $time_format; ?>");
	}

	/// TIME CLICK EVENT ///
	
	function vapTimeClicked(hour, min) {
		var id_ser 	= jQuery('#vapsersel').val();
		var day 	= jQuery('#vapdayselected').val();

		// inject details within the form
		document.empareaForm.day.value = day;
		jQuery('#empareaForm').append('<input type="hidden" name="id_ser" value="'+id_ser+'" />');
		jQuery('#empareaForm').append('<input type="hidden" name="hour" value="'+hour+'" />');
		jQuery('#empareaForm').append('<input type="hidden" name="min" value="'+min+'" />');

		<?php
		if (!isset($this->checkoutSelection))
		{
			/**
			 * Overwrite the form action and submit the form only
			 * in case the checkout selection is disabled, otherwise the form
			 * will be submitted without selecting the checkout as the checkin
			 * select triggers vapTimeClicked() function every time its value changes.
			 *
			 * The form submission should be launched after selecting the checkout.
			 *
			 * @see checkout-changed
			 */
			?>

			// overwrite the form action
			document.empareaForm.action = "<?php echo JRoute::_('index.php?option=com_vikappointments&view=empmanres&Itemid=' . $itemid, false); ?>";

			vapLoadCalendars();

			<?php
		}
		?>
	}

	<?php
	if (isset($this->checkoutSelection))
	{
		/**
		 * @see vapTimeClicked() for further details.
		 */
		?>

		jQuery(document).on('checkout-changed', function() {
			// overwrite the form action
			document.empareaForm.action = "<?php echo JRoute::_('index.php?option=com_vikappointments&view=empmanres&Itemid=' . $itemid, false); ?>";

			// submit the form
			vapLoadCalendars();
		});

		<?php
	}
	?>

	////////////////////////
	
	var _row_timeout_ = null;
	
	function vapViewDetails(hour, min) {
		var id_emp = <?php echo $employee['id']; ?>;
		var day = jQuery('#vapdayselected').val();
		
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=emplogin.getReservationAt&tmpl=component&Itemid=' . $itemid, false); ?>",
			data: {
				id_emp: id_emp,
				day: day,
				hour: hour,
				min: min
			}
		}).done(function(resp){
			var obj = jQuery.parseJSON(resp);

			if (obj[0]) {
				if (_row_timeout_ != null) {
					clearTimeout(_row_timeout_);
				}
				
				vapAddHighlightFromRow(obj[1]);

				_row_timeout_ = setTimeout(function() {
					vapRemoveHighlightFromRow(obj[1]);
				}, 3000);
			} 
		});
	}

	function vapAddHighlightFromRow(id) {
		if (typeof id !== 'object') {
			id = [id];
		}

		jQuery('.vapemprestr').removeClass('vaprowhighlight');

		for (var i in id) {
			jQuery('#vaptabrow' + id[i]).addClass('vaprowhighlight');
		}

		// animate only in case the selected row is not visible
		var px_to_scroll = isBoxOutOfMonitor(jQuery('#vaptabrow' + id[0]));
			
		if (px_to_scroll !== false) {
			jQuery('html,body').animate( {scrollTop: "+=" + px_to_scroll}, {duration:'normal'} );
		}
	}
	
	function vapRemoveHighlightFromRow(id) {
		if (typeof id !== 'object') {
			id = [id];
		}

		for (var i in id) {
			jQuery('#vaptabrow' + id[i]).removeClass('vaprowhighlight');
		}
	}

	function vapPeopleValueChanged() {
		var day = jQuery('#vapdayselected').val();

		if (day.length > 0) {
			vapGetTimeLine(parseInt(day));
		}
	}
	
</script>
