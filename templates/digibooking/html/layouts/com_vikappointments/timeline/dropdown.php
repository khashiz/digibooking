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

$id_service 	= isset($displayData['id_service'])  ? $displayData['id_service'] 	: 0;
$id_employee 	= isset($displayData['id_employee']) ? $displayData['id_employee']	: 0;
$checkin_day 	= isset($displayData['checkinDay'])  ? $displayData['checkinDay']	: 0;
$duration 		= isset($displayData['duration']) 	 ? $displayData['duration']		: 0;
$times 			= isset($displayData['times']) 		 ? $displayData['times']		: array();
$rates 			= isset($displayData['rates']) 		 ? $displayData['rates']		: array();
$time_format = UIFactory::getConfig()->get('timeformat');
$date = getdate($checkin_day);
$checkin_ymd = date('Y-m-d', $date[0]);
?>
<div class="uk-child-width-auto uk-flex-center uk-grid-small" data-uk-grid>
    <!-- CHECKIN -->
    <div>
        <span class="uk-text-tiny uk-text-muted uk-display-block uk-margin-small-bottom uk-text-center uk-text-right@m font"><?php echo JTEXT::_('HOUR_START').' :'; ?></span>
        <select id="vap-checkin-sel" class="uk-select uk-width-small uk-text-small uk-text-secondary uk-border-rounded font timeSelect" onchange="checkinSelectValueChanged(this);">

            <option><?php echo JTEXT::_('SELECT'); ?></option>

            <?php
            $link = 0;
            foreach ($times as $block)
            {
                $link++;
                foreach ($block as $k => $v)
                {
                    $hour = floor($k / 60);
                    $min  = $k % 60;

                    if ($v == 1)
                    {
                        $checkin  = mktime($hour, $min, 0, $date['mon'], $date['mday'], $date['year']);
                        $checkout = mktime($hour, $min + $duration, 0, $date['mon'], $date['mday'], $date['year']);

                        /**
                         * Hide time slots that exceed the midnight.
                         * The "hidden" class should do the trick for select2 plugin.
                         *
                         * @since 1.6.2
                         */
                        $should_hide = $checkin_ymd != date('Y-m-d', $checkin) ? 'hidden' : '';

                        ?>
                        <option
                                value="<?php echo $checkin; ?>"
                                class="<?php echo $should_hide; ?>"
                                data-hour="<?php echo $hour; ?>"
                                data-min="<?php echo $min; ?>"
                                data-link="<?php echo $link; ?>"
                                data-checkout="<?php echo $checkout; ?>"
                                data-checkout-date="<?php echo date($time_format, $checkout); ?>"
                        ><?php echo date($time_format, $checkin); ?></option>
                        <?php
                    }
                    else
                    {
                        $link++;
                    }
                }
            }
            ?>

        </select>
    </div>
    <div class="uk-flex uk-flex-bottom">
        <span class="uk-text-small uk-text-muted uk-margin-small-bottom font"><?php echo JTEXT::_('TO'); ?></span>
    </div>
    <!-- CHECKOUT -->
    <div>
        <span class="uk-text-tiny uk-text-muted uk-display-block uk-margin-small-bottom uk-text-center uk-text-right@m font"><?php echo JTEXT::_('HOUR_END').' :'; ?></span>
        <select id="vap-checkout-sel" class="uk-select uk-width-small uk-text-small uk-text-secondary uk-border-rounded font timeSelect" disabled="disabled" onchange="checkoutSelectValueChanged(this);">
            <option><?php echo JTEXT::_('SELECT'); ?></option>
        </select>
    </div>
</div>
<!--
<script>
	// jQuery('#vap-checkin-sel, #vap-checkout-sel').select2({
	// 	minimumResultsForSearch: -1,
	// 	placeholder: '--',
	// 	allowClear: false,
	// 	width: 150
	// });
</script>
-->