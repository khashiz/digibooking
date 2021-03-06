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

$employee 	= $this->employee;
$service 	= $this->selectedService;
$bookings 	= $this->bookings;

$dbo = JFactory::getDbo();

$day_shift = 0;//VikAppointments::getCalendarFirstWeekDay();

$ncal = VikAppointments::getNumberOfCalendars();

$DAYS = array();

for ($i = 0; $i < 7; $i++)
{
	$DAYS[$i] = (6 - ($day_shift - $i) + 1) % 7;
	/**
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

$_DAY_TEXT = ArasJoomlaVikApp::weekDays();

VikAppointments::setCurrentTimezone($employee['timezone']);
$arr = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime(0, 0, 0, $this->month, 1, $this->year));

$book_index = 0;

$closing_days 	 = VikAppointments::getClosingDays();
$closing_periods = VikAppointments::getClosingPeriods();

$no_day_char = JText::_('VAPRESNODAYCHAR');

$time_format = UIFactory::getConfig()->get('timeformat');
?>
<div class="uk-background-primary">
    <h2 class="uk-text-center uk-text-white uk-padding-small uk-margin-remove uk-h4 font"><?php echo JTEXT::_('COMPLETE_RESERVE_INFO'); ?></h2>
    <span class="mobSpacer uk-hidden@m uk-margin-bottom uk-display-block"></span>
</div>
<div class="empSearchWrapper">
    <div class="uk-grid-collapse uk-child-width-1-1 uk-child-width-1-2@m" data-uk-grid>
        <div>
            <div class="uk-padding uk-text-zero leftBordered">
                <?php if (VikAppointments::isCalendarLegendVisible()) { $legend_arr = array('green', 'yellow', 'red', 'blue', 'grey'); ?>
                    <div class="vapallcaldiv_">
                        <h3 class="uk-margin-bottom font f500 uk-h4 uk-text-secondary uk-text-center"><?php echo JText::sprintf('SELECT_YOUR_DATE'); ?></h3>
                        <div data-uk-slider="finite: true;">
                            <div class="uk-grid-small" data-uk-grid>
                                <div class="uk-width-auto uk-width-1-6@xl uk-flex uk-flex-middle uk-text-muted uk-visible@m">
                                    <a href="#" class="uk-flex uk-flex-middle uk-flex-center uk-link-reset" data-uk-slider-item="previous">
                                        <span><img src="<?php echo JUri::base().'images/sprite.svg#chevron-right'; ?>" width="18" height="18" data-uk-svg></span>
                                        <span class="font uk-text-small uk-margin-small-right uk-visible@xl"><?php echo JText::sprintf('PREV_MONTH'); ?></span>
                                    </a>
                                </div>
                                <div class="uk-width-expand">
                                    <div class="uk-overflow-hidden">
                                        <div class="uk-slider-items uk-child-width-1-1">
                                            <?php for ($cal = 0; $cal < $ncal; $cal++) { ?>
                                                <div class="vapcalendardiv_">
                                                    <table class="uk-table uk-table-middle uk-margin-remove vapcaltable">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="7" class="uk-text-secondary uk-text-center font fnum"><?php echo JText::_($arr['month']) . ' - ' . $arr['year']; ?></th>
                                                        </tr>

                                                        <tr>

                                                            <?php
                                                            for ($i = 0; $i < 7; $i++)
                                                            {
                                                                ?>
                                                                <th class="uk-text-center uk-text-muted font"><?php echo $_DAY_TEXT[VikAppointments::getShiftedDay($i, $day_shift)]; ?></th>
                                                                <?php
                                                            }
                                                            ?>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        <tr>
                                                            <?php
                                                            for ($i = 0, $cont = 0, $n = $DAYS[$arr['wday']]; $i < $n; $i++, $cont++)
                                                            {
                                                                ?>
                                                                <td class="vaptdnoday">
                                                                    <div class="vapdivday_ fnum"><?php echo $no_day_char; ?></div>
                                                                </td>
                                                                <?php
                                                            }

                                                            $last_month = $arr['mon'];

                                                            while ($arr['mon'] == $last_month)
                                                            {
                                                            VikAppointments::setCurrentTimezone($employee['timezone']);

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

                                                            $curr_mktime = ArasJoomlaVikApp::jmktime(23, 59, 59, $arr['mon'], $arr['mday'], $arr['year']);

                                                            // insert 86400 seconds (1 day) to end publishing
                                                            if (!VikAppointments::isTimeInThePast($curr_mktime)
                                                                && ($service['start_publishing'] == -1
                                                                    || ($service['start_publishing'] <= $curr_mktime && $curr_mktime < $service['end_publishing'] + 86400)
                                                                ))
                                                            {
                                                                // check if the selected employee is available for this day
                                                                if (VikAppointments::isTableDayAvailable($this->idEmployee, $this->idService, $arr[0], $closing_days, $closing_periods, $dbo, $this->reqLocations))
                                                                {
                                                                    // evaluate the appointments booked for this day
                                                                    $res = VikAppointments::evaluateBookingArray($bookings, $book_index, $arr[0]);

                                                                    // by default the cell if free
                                                                    $add_class = 'vaptdgreen';

                                                                    if (count($res[1]))
                                                                    {
                                                                        // there is at least an appointments found, mark the cell as fully-occupied
                                                                        $add_class = 'vaptdred';

                                                                        if ($service['max_capacity'] == 1 || $service['app_per_slot'] == 0)
                                                                        {
                                                                            // the service doesn't support multiple appointments at the same date and time
                                                                            if( VikAppointments::isFreeIntervalOnDay($this->idEmployee, $this->idService, $res[1], $arr[0], 1, $dbo, $this->reqLocations))
                                                                            {
                                                                                // there is at least a free appointment, use partially-occupied status
                                                                                $add_class = 'vaptdyellow';
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            // the service supports multiple appointments at the same date and time
                                                                            if (VikAppointments::isFreeIntervalOnDayGroupService(array($employee), $this->idService, $res[1], $arr[0], $service['max_capacity'], $dbo, $this->reqLocations ))
                                                                            {
                                                                                // there is at least a free appointment, use partially-occupied status
                                                                                $add_class = 'vaptdyellow';
                                                                            }
                                                                        }
                                                                    }

                                                                    // keep the last index evaluated of the $bookings array
                                                                    $book_index = $res[0];

                                                                    /**
                                                                     * Fetch whether the current day owns any active rates.
                                                                     *
                                                                     * @since 1.6.2
                                                                     */
                                                                    $active_rates = VAPSpecialRates::getRatesOnDay($service['id'], $arr[0]);

                                                                    if ($active_rates)
                                                                    {
                                                                        // extract class suffix based on rates (use "calendar" caller)
                                                                        $add_class .= VAPSpecialRates::extractClass($active_rates, 'calendar');
                                                                    }
                                                                }
                                                            }

                                                            ?>
                                                            <td class="vaptdday <?php echo $add_class; ?>" id="vapday<?php echo $arr[0]; ?>">
                                                                <a href="javascript: void(0);" onClick="vapGetTimeLine(<?php echo $arr[0]; ?>);">
                                                                    <div class="vapdivday fnum">
                                                                        <?php echo $arr['mday']; ?>
                                                                    </div>
                                                                </a>
                                                            </td>
                                                            <?php
                                                            $employee['timezone'] = 'Asian/Tehran';
                                                            VikAppointments::setCurrentTimezone($employee['timezone']);
                                                            // get next day
                                                            $arr = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime($arr['hours'], $arr['minutes'], 0, $arr['mon'], $arr['mday'] + 1, $arr['year']));

                                                            $cont++;
                                                            }

                                                            // fix the remaining cells with a "no-day" character
                                                            for ($i = $cont; $i < 7; $i++)
                                                            {
                                                                ?>
                                                                <td class="vaptdnoday">
                                                                    <div class="vapdivday"><?php echo $no_day_char; ?></div>
                                                                </td>
                                                                <?php
                                                            }
                                                            ?>
                                                        </tr>

                                                        </tbody>

                                                    </table>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-width-auto uk-width-1-6@xl uk-flex uk-flex-middle uk-flex-left uk-text-muted uk-visible@m">
                                    <a href="#" class="uk-flex uk-flex-middle uk-flex-center uk-link-reset" data-uk-slider-item="next">
                                        <span class="font uk-text-small uk-margin-small-left uk-visible@xl"><?php echo JText::sprintf('NEXT_MONTH'); ?></span>
                                        <span><img src="<?php echo JUri::base().'images/sprite.svg#chevron-left'; ?>" width="18" height="18" data-uk-svg></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-margin-top">
                        <ul class="uk-child-width-1-2 uk-child-width-auto@m uk-flex-center uk-grid-small" data-uk-grid>
                            <?php foreach ($legend_arr as $color) { ?>
                                <?php if ($color != 'grey') { ?>
                                    <li>
                                        <div class="uk-flex uk-flex-middle">
                                            <span class="uk-border-rounded uk-display-inline-block uk-margin-small-left uk-flex-none vap-cal-box <?php echo $color; ?>"></span>
                                            <span class="uk-text-tiny uk-text-muted uk-display-inline-block font"><?php echo JText::_('CALENDARLEGEND' . strtoupper($color)); ?></span>
                                        </div>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                        <hr class="uk-hidden@m uk-margin-medium-top uk-margin-medium-bottom">
                    </div>
                <?php } ?>
            </div>
        </div>
        <div>
            <div class="bottomBordered uk-background-muted uk-visible@m">
                <div class="uk-padding">
                    <h3 class="uk-margin-bottom font f500 uk-h4 uk-text-secondary uk-text-center"><?php echo JTEXT::_('SELECT_POSITION'); ?></h3>
                    <ul class="uk-child-width-auto uk-flex-center font uk-text-small" data-uk-grid>
                        <li>
                            <span class="uk-text-muted"><?php echo JText::sprintf('POSITION_NAME').' : '; ?></span>
                            <span class="uk-text-secondary"><?php echo $employee['firstname'].' '.$employee['lastname']; ?></span>
                        </li>
                        <li>
                            <span class="uk-text-muted"><?php echo JText::sprintf('FLOOR').' : '; ?></span>
                            <span class="uk-text-secondary"><?php echo JText::sprintf('FLOOR'.$employee['nickname']); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="uk-padding">
                <h3 class="uk-margin-bottom font f500 uk-h4 uk-text-secondary uk-text-center"><?php echo $service['name'] == 'PARKINGS' ? JText::sprintf('SELECT_YOUR_PLATE') : JText::sprintf('SELECT_YOUR_HOUR'); ?></h3>
                <?php if ($service['name'] == 'PARKINGS') { ?>
                    <script>
                        jQuery(document).ready(function () {
                            plateSpliteInserter();
                        });
                    </script>
                    <div class="uk-margin-auto uk-width-1-1 uk-width-2-3@xl">
                    <div class="uk-text-zero uk-border-rounded plateWrapper">
                        <div>
                            <div class="uk-grid-collapse" data-uk-grid>
                                <div class="uk-width-1-5">
                                    <div class="uk-background-white uk-border-rounded uk-overflow-hidden sidePart">
                                        <div>
                                            <input type="tel" name="sideDigit" placeholder="_&ensp;_" maxlength="2" id="sideDigit" class="uk-width-1-1 ltr" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-width-expand">
                                    <div class="uk-background-white uk-border-rounded uk-overflow-hidden mainPart">
                                        <div>
                                            <div class="uk-grid-collapse uk-flex-around" data-uk-grid>
                                                <div class="uk-width-2-5">
                                                    <input type="tel" name="threeDigit" placeholder="_&ensp;_&ensp;_" maxlength="3" id="threeDigit" class="uk-width-1-1 uk-margin-auto ltr">
                                                </div>
                                                <div class="uk-width-expand">
                                                    <div class="uk-grid-collapse uk-flex-around" data-uk-grid>
                                                        <div class="uk-width-expand">
                                                            <select class="uk-select uk-height-1-1 uk-width-1-1" id="alphabet">
                                                                <option value="" selected="selected"></option>
                                                                <option value="??????">??????</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                                <option value="??">??</option>
                                                            </select>
                                                        </div>
                                                        <div class="uk-width-1-2">
                                                            <input type="tel" name="twoDigit" placeholder="_&ensp;_" maxlength="2" id="twoDigit" class="uk-width-1-1 ltr">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-width-auto uk-visible@m">
                                    <div class="uk-border-rounded uk-height-1-1">
                                        <div class="uk-border-rounded uk-height-1-1 plateBlue">
                                            <div class="uk-flex uk-flex-column uk-flex-between uk-height-1-1">
                                                <div>
                                                    <span class="uk-display-block flag"></span>
                                                </div>
                                                <div>
                                                    <span class="uk-display-block uk-text-white font uk-text-small uk-text-left ltr iran">I.R.<br>IRAN</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="<?php if ($service['name'] == 'PARKINGS') {echo 'uk-hidden';} ?>">
                    <div class="vaptimeline uk-text-center" id="vaptimeline"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

	jQuery(document).ready(function() {

		var day = '<?php echo $this->last_day; ?>';
	
		if (day.length > 0) {
			vapGetTimeLine(parseInt(day));
            <?php if ($service['name'] == 'PARKINGS') { ?>
            setTimeout(function(){ jQuery('.vaptimelinewt > div:first-child > a').click(); }, 1000);
            <?php } ?>
		}

	});

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

		/**
		 * @see views/employeesearch/tmpl/default.php
		 */
		LAST_TIMESTAMP_USED = timestamp;
		
		/**
		 * @see layouts/blocks/checkout.php
		 */
		isTimeChoosen = false;

		// unset hours and minutes as the checkin day has been changed
		jQuery('#vapconfhourselected').val('');
		jQuery('#vapconfminselected').val('');
		
		jQuery('.vaptdday').removeClass('vaptdselected');
		jQuery('#vapday' + timestamp).addClass('vaptdselected');
		
		jQuery('.vaptlblock1').removeClass('.vaptimeselected');	
		
		var id_emp = <?php echo $this->idEmployee; ?>;
		var id_ser = <?php echo $this->idService; ?>;
		
		var is_people 	= <?php echo $service['max_capacity'] > 1 ? 1 : 0; ?>;
		var people 		= 1;

		if (is_people) {
			people = jQuery('#vapserpeopleselect').val();
		}
		
		jQuery('#vapdayselected').val(timestamp);
		jQuery('#vappeopleselected').val(people);

		var locations = "";
		var all = true;

		jQuery('.vap-empsearch-locval').each(function() {
			if (jQuery(this).is(':checked')) {
				if (locations.length > 0) {
					locations += ",";
				}

				locations += jQuery(this).val();
			} else {
				all = false;
			}
		});

		if (all) {
			locations = "";
		}
		
		jQuery.noConflict();
				
		TIMELINE_XHR = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=get_day_time_line&tmpl=component' . ($this->itemid ? '&Itemid=' . $this->itemid : ''), false); ?>",
			data: {
				id_emp: id_emp,
				id_ser: id_ser,
				day: timestamp,
				people: people,
				locations: locations
			}
		}).done(function(resp) {
			TIMELINE_XHR = null;

			var obj = jQuery.parseJSON(resp);
			
			if (obj[0]) {
				// successful response
				// [1] full JSON response
				// [2] obtain layout HTML
				// [3] the new rate
				vapRenderTimeLine(obj[1], obj[2], obj[3]);
			} else {
				// hide wait list button
				jQuery('#vapwaitlistbox').hide();
				// show add cart button
				jQuery('#vapadditembutton').show();

				jQuery('#vapdayselected').val('');

				// show error message
                UIkit.notification({message: obj[1], status: 'warning', pos: 'bottom-left'});
				
				if (vapDoAnimation) {
					var px_to_scroll = isTimelineOutOfMonitor();
					
					if (px_to_scroll !== false) {
						jQuery('html,body').animate( {scrollTop: "+=" + px_to_scroll}, {duration:'normal'} );
					}
				}
				
				vapDoAnimation = true;
			}
		}).fail(function(resp) {
			TIMELINE_XHR = null;

			/**
			 * @todo retries automatically on connection lost error
			 */
		});

		<?php if ($service['name'] == 'PARKINGS') { ?>
        setTimeout(function(){ jQuery('.vaptimelinewt > div:first-child > a').click(); }, 1000);
        <?php } ?>

	}

	var HOUR_MIN_SELECTED = false;

	function vapRenderTimeLine(arr, timeline, newRate) {
		jQuery('#vaptimeline').html(timeline);

		// update base cost
		if (newRate) {
			/**
			 * @see views/employeesearch/tmpl/default_filterbar.php
			 */
			vapUpdateServiceRate(newRate);
		}

		var at_least_one = false;
		var at_least_one_red = false;
		
		for (var w = 0; w < arr.length; w++) {
			jQuery.each(arr[w], function(key, val) {
				at_least_one 		= at_least_one || (val == 1);
				at_least_one_red 	= at_least_one_red || (val == 0);
			});
		}
			
		if (vapDoAnimation) {
			var px_to_scroll = isTimelineOutOfMonitor();
			if (px_to_scroll !== false) {
				jQuery('html,body').animate( {scrollTop: "+=" + px_to_scroll}, {duration:'normal'} );
			}
		}
		
		vapDoAnimation = true;

		if (at_least_one) {
			jQuery('#vapadditembutton').show();
		} else {
			jQuery('#vapadditembutton').hide();
		}

		if (at_least_one_red) {
			jQuery('#vapwaitlistbox').show();
		} else {
			jQuery('#vapwaitlistbox').hide();
		}

		<?php
		if (!is_null($this->hour) && !is_null($this->min))
		{
			/**
			 * If hours and minutes are set, try to pre-select
			 * the specified block.
			 *
			 * @since 1.6
			 */

			?>

			if (!HOUR_MIN_SELECTED) {

				var hour = <?php echo (int) $this->hour; ?>;
				var min  = <?php echo (int) $this->min; ?>;

				<?php
				if ($service['checkout_selection'])
				{
					// used for dropdown layout
					?>

					jQuery('#vap-checkin-sel option').each(function() {

						if (hour == jQuery(this).data('hour') && min == jQuery(this).data('min')) {
							// trigger click of selected option
							jQuery('#vap-checkin-sel').val(jQuery(this).val()).trigger('change');
						}

					});

					<?php
				}
				else
				{
					// used for any other timeline layout
					?>
			
					jQuery('.vap-timeline-block').each(function() {

						if (hour == jQuery(this).data('hour') && min == jQuery(this).data('min')) {
							// invoke vapTimeClicked() function
							jQuery(this).closest('a').trigger('click');
						}

					});

					<?php
				}
				?>

				// pre-select time block only once
				HOUR_MIN_SELECTED = true;

			}

		<?php } ?>
	}

	/**
	 * Checks if the timeline is currently visible within the monitor.
	 *
	 * @return 	integer  The pixels to scroll if the time line is not visible, otherwise false.
	 */
	function isTimelineOutOfMonitor() {
		var timeline_y 		 = jQuery('#vaptimeline').offset().top;
		var scroll 			 = jQuery(window).scrollTop();
		var screen_height 	 = jQuery(window).height();
		var min_height_const = 150;
		
		if (timeline_y - scroll + min_height_const > screen_height) {
			return timeline_y - scroll + min_height_const - screen_height;
		}
		
		return false;
	}

	function vapTimeClicked(hour, min, id) {
		// get new rate as string
		var newRate = '' + jQuery('#vaptimelineblock' + id).data('rate');

		if (newRate.length) {
			/**
			 * Dispatch rate update only if the data is set.
			 *
			 * @see views/employeesearch/tmpl/default_filterbar.php
			 */
			vapUpdateServiceRate(parseFloat(newRate));
		}
		
		jQuery('#vapconfempselected').val(<?php echo $this->idEmployee; ?>);
		jQuery('#vapconfserselected').val(<?php echo $this->idService; ?>);
		jQuery('#vapconfdayselected').val(jQuery('#vapdayselected').val());
		jQuery('#vapconfhourselected').val(hour);
		jQuery('#vapconfminselected').val(min);
		jQuery('#vapconfpeopleselected').val(jQuery('#vappeopleselected').val());
		
		jQuery('.vaptlblock1').removeClass('vaptimeselected');
		jQuery('#vaptimelineblock' + id).addClass('vaptimeselected');
		
		var opt_div = jQuery('.vapseroptionscont');
		if (opt_div.length > 0) {
			opt_div.slideDown();
		}
		
		var rec_div = jQuery('.vaprecurrencediv');
		if (rec_div.length > 0) {
			rec_div.slideDown();
		}
		
		isTimeChoosen = true;
	}


    function setPlateCookies()
    {
        document.cookie = "plate_sideDigit="+jQuery('#sideDigit').val()+"; path=/";
        document.cookie = "plate_threeDigit="+jQuery('#threeDigit').val()+"; path=/";
        document.cookie = "plate_alphabetDigit="+jQuery('#alphabet').val()+"; path=/";
        document.cookie = "plate_twoDigit="+jQuery('#twoDigit').val()+"; path=/";
    }

</script>
