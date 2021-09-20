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

$id_service  = isset($displayData['id_service'])  ? $displayData['id_service']  : 0;
$id_employee = isset($displayData['id_employee']) ? $displayData['id_employee'] : 0;
$checkin_day = isset($displayData['checkinDay'])  ? $displayData['checkinDay']  : 0;
$duration    = isset($displayData['duration'])    ? $displayData['duration']    : 0;
$times       = isset($displayData['times'])       ? $displayData['times']       : array();
$rates       = isset($displayData['rates'])       ? $displayData['rates']       : array();

$config = UIFactory::getConfig();

$time_format   = $config->get('timeformat');
$show_checkout = $config->getBool('showcheckout');
		
$titles_lookup = array(
	JText::_('VAPFINDRESTIMENOAV'),
	JText::_('VAPFINDRESBOOKNOW'),
	JText::_('VAPFINDRESNOENOUGHTIME'),
);

$block_id = 0;

$date = getdate($checkin_day);

foreach ($times as $block)
{
	?>
	<div class="vaptimelinewt uk-text-zero uk-grid-small uk-child-width-1-6" data-uk-grid>
		<?php
		foreach ($block as $k => $v)
		{
			$hour = floor($k / 60);
			$min  = $k % 60;
			
			$clickEvent = '';
			
			if ($v == 1)
			{
				$clickEvent = "vapTimeClicked($hour, $min, $block_id);";
			}

			$checkin = mktime($hour, $min, 0, $date['mon'], $date['mday'], $date['year']);

			/**
			 * Display checkout time if enabled.
			 *
			 * @since 1.6.2
			 */
			if ($show_checkout)
			{
				$checkout = mktime($hour, $min + $duration, 0, $date['mon'], $date['mday'], $date['year']);
			}
			
			?>
            <div>
			<a href="javascript: void(0);" title="<?php echo $titles_lookup[$v]; ?>" onClick="<?php echo $clickEvent; ?>" class="uk-margin-remove uk-display-block">
				<div 
					class="<?php if ($block_id == 0) {echo 'firstSlut';} ?> uk-margin-remove uk-display-block uk-padding-small fnum vap-timeline-block<?php echo ($time_format != "H:i" ? " large" : ""); ?> vaptlblock<?php echo $v; ?>"
					id="vaptimelineblock<?php echo $block_id; ?>"
					data-rate="<?php echo isset($rates[$k]) ? $rates[$k] : ''; ?>"
					data-hour="<?php echo $hour; ?>"
					data-min="<?php echo $min; ?>"
				>
					<?php
					echo date($time_format, $checkin);

					if ($show_checkout)
					{
						echo ' - ' . date($time_format, $checkout);
					}
					?>
				</div>
			</a>
            </div>
			<?php

			$block_id++;
		}
		?>
	</div>
	<?php
}
