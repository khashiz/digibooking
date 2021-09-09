<?php
/** 
 * @package       VikAppointments
 * @subpackage     com_vikappointments
 * @author        Matteo Galletti - e4j
 * @copyright     Copyright (C) 2019 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link         https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$service     = $this->service;
$employees     = $this->employees;
$bookings     = $this->bookings;

$dbo = JFactory::getDbo();

$employees_arr_for_eval = $employees;

if ($service['choose_emp'])
{
    $employees_arr_for_eval = array(array("id" => $this->idEmployee));
}

$day_shift = 0;//VikAppointments::getCalendarFirstWeekDay();

$ncal = VikAppointments::getNumberOfCalendars();

$DAYS = array();

for ($i = 0; $i < 7; $i++)
{
    $DAYS[$i] = (6 - ($day_shift - $i) + 1) % 7;
    /**
     *    DAY = ( (NUM_DAYS-1) - ( SHIFT - DAY_INDEX ) + 1 ) % NUM_DAYS 
     *
     *     SATURDAY
     *     0                1                2                3                4                5                6
     *     6-(6-0)+1%7=1    6-(6-1)+1%7=2    6-(6-2)+1%7=3    6-(6-3)+1%7=4    6-(6-4)+1%7=5    6-(6-5)+1%7=6    6-(6-6)+1%7=0
     * 
     *     SUNDAY
     *     0                1                2                3                4                5                6
     *     6-(0-0)+1%7=0    6-(0-1)+1%7=1    6-(0-2)+1%7=2    6-(0-3)+1%7=3    6-(0-4)+1%7=4    6-(0-5)+1%7=5    6-(0-6)+1%7=6
     * 
     *     MONDAY
     *     0                1                2                3                4                5                6
     *     6-(1-0)+1%7=6     6-(1-1)+1%7=0    6-(1-2)+1%7=1    6-(1-3)+1%7=2    6-(1-4)+1%7=3    6-(1-5)+1%7=4    6-(1-6)+1%7=5
     * 
     *     WEDNESDAY
     *     0                1                2                3                4                5                6
     *     6-(3-0)+1%7=4     6-(3-1)+1%7=5    6-(3-2)+1%7=6    6-(3-3)+1%7=0    6-(3-4)+1%7=1    6-(3-5)+1%7=2    6-(3-6)+1%7=3
     */
}

$_DAY_TEXT = ArasJoomlaVikApp::weekDaysShort();

VikAppointments::setCurrentTimezone($service['timezone']);
$arr = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime(0, 0, 0, $this->month, 1, $this->year));

$book_index = 0;

$closing_days      = VikAppointments::getClosingDays();
$closing_periods = VikAppointments::getClosingPeriods();

$no_day_char = JText::_('VAPRESNODAYCHAR');

$time_format = UIFactory::getConfig()->get('timeformat');

// display legend bar
if (VikAppointments::isCalendarLegendVisible())
{ 
    $legend_arr = array('green', 'yellow', 'red', 'blue', 'grey');

    ?>
    <div class="vap-calendar-legend-box">
        <ul class="vap-cal-legend">
            <?php foreach ($legend_arr as $color) { ?> 
                <li>
                    <span class="vap-cal-box-<?php echo $color; ?>"></span>
                    &nbsp;<?php echo JText::_('VAPCALENDARLEGEND' . strtoupper($color)); ?>
                </li>
            <?php } ?>
        </ul>
    </div>
    <?php
}

?>

<div class="vapallcaldiv">

    <?php
    for ($cal = 0; $cal < $ncal; $cal++)
    {
        ?>

        <div class="vapcalendardiv">
            <table class="vapcaltable">
                <thead class="vaptheadcal">
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            <?php echo JText::_($arr['month']) . ' - ' . $arr['year']; ?>
                        </td>
                    </tr>

                    <tr>

                    <?php
                    for ($i = 0; $i < 7; $i++)
                    {
                        ?>
                        <th class="vapthtabcal"><?php echo $_DAY_TEXT[VikAppointments::getShiftedDay($i, $day_shift)]; ?></th>
                        <?php
                    }
                    ?>
                    </tr>
                </thead>
                
                <tbody class="vaptbodycal">
                    <tr>
                        <?php

                        for ($i = 0, $cont = 0, $n = $DAYS[$arr['wday']]; $i < $n; $i++, $cont++)
                        {
                            ?>
                            <td class="vaptdnoday">
                                <div class="vapdivday"><?php echo $no_day_char; ?></div>
                            </td>
                            <?php
                        }

                        $last_month = $arr['mon'];

                        while ($arr['mon'] == $last_month) 
                        {
                            VikAppointments::setCurrentTimezone($service['timezone']);

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
                                if ($service['choose_emp'])
                                {
                                    // check if the selected employee is available for this day
                                    $day_available = VikAppointments::isTableDayAvailable($this->idEmployee, $service['id'], $arr[0], $closing_days, $closing_periods, $dbo, $this->reqLocations);
                                }
                                else
                                {
                                    // check if the service owns at least an employee available to this day
                                    $day_available = VikAppointments::isGenericTableDayAvailable($employees, $service['id'], $arr[0], $closing_days, $closing_periods, $dbo, $this->reqLocations);
                                }

                                if ($day_available)
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
                                            if (VikAppointments::isFreeIntervalOnDayService($employees_arr_for_eval, $service['id'], $res[1], $arr[0], $dbo, $this->reqLocations))
                                            {
                                                // there is at least a free appointment, use partially-occupied status
                                                $add_class = 'vaptdyellow';
                                            }
                                        }
                                        else
                                        {
                                            // the service supports multiple appointments at the same date and time
                                            if (VikAppointments::isFreeIntervalOnDayGroupService($employees_arr_for_eval, $service['id'], $res[1], $arr[0], $service['max_capacity'], $dbo, $this->reqLocations))
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
                                    <div class="vapdivday">
                                        <?php echo $arr['mday']; ?>
                                    </div>
                                </a>
                            </td>
                            <?php
                            
                            VikAppointments::setCurrentTimezone($service['timezone']);
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
        
        <?php
    }
    ?>

</div>

<div class="vaptimeline" id="vaptimeline">
            
</div>

<script>

    var emp_choosable = <?php echo $service['choose_emp'] ? 1 : 0; ?>;

    jQuery(document).ready(function() {

        var day = '<?php echo $this->last_day; ?>';
    
        if (day.length > 0) {
            vapGetTimeLine(parseInt(day));
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
         * @see views/servicesearch/tmpl/default.php
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
        
        var id_ser = <?php echo $service['id']; ?>;
        var id_emp = -1;

        if (emp_choosable) {
            var id_emp = jQuery('#vapempsel').val();
        }
        
        var is_people     = <?php echo $service['max_capacity'] > 1 ? 1 : 0; ?>;
        var people         = 1;

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
            url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=get_day_time_line_service&tmpl=component' . ($this->itemid ? '&Itemid=' . $this->itemid : ''), false); ?>",
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
                jQuery('.vaptimeline').html(obj[1]);
                
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
        
    }

    function vapRenderTimeLine(arr, timeline, newRate) {
        jQuery('#vaptimeline').html(timeline);

        // update base cost
        if (newRate) {
            vapUpdateServiceRate(newRate);
        }

        var at_least_one = false;
        var at_least_one_red = false;
        
        for (var w = 0; w < arr.length; w++) {
            jQuery.each(arr[w], function(key, val) {
                at_least_one         = at_least_one || (val == 1);
                at_least_one_red     = at_least_one_red || (val == 0);
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
    }

    /**
     * Checks if the timeline is currently visible within the monitor.
     *
     * @return     integer  The pixels to scroll if the time line is not visible, otherwise false.
     */
    function isTimelineOutOfMonitor() {
        var timeline_y          = jQuery('#vaptimeline').offset().top;
        var scroll              = jQuery(window).scrollTop();
        var screen_height      = jQuery(window).height();
        var min_height_const = 150;
        
        if (timeline_y - scroll + min_height_const > screen_height) {
            return timeline_y - scroll + min_height_const - screen_height;
        }
        
        return false;
    }

    function vapTimeClicked(hour, min, id) {
        var id_emp = -1;
        
        if (emp_choosable) {
            var id_emp = jQuery('#vapempsel').val();
        }

        // get new rate as string
        var newRate = '' + jQuery('#vaptimelineblock' + id).data('rate');

        if (newRate.length) {
            // dispatch rate update only if the data is set
            vapUpdateServiceRate(parseFloat(newRate));
        }
        
        jQuery('#vapconfserselected').val(<?php echo $service['id']; ?>);
        jQuery('#vapconfempselected').val(id_emp);
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

</script>
