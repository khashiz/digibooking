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

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   integer  $id_employee  The employee ID.
 * @var   integer  $id_service   The service ID.
 * @var   array    $table        The associative array containing all the available times.
 * @var   integer  $max_rows     The maximum number of available times per day.
 * @var   mixed    $prev_day     The day to use after clicking "prev" arrow. Null if the button is disabled.
 * @var   integer  $next_day 	 The day to use after clicking "next" arrow.
 * @var   integer  $itemid       The current Item ID.
 */

$time_format = UIFactory::getConfig()->get('timeformat');

/**
 * The maximum number of times that should
 * be visible for each day.
 *
 * @var integer
 */
$times_per_rows = 4;

/**
 * Since the table of an employee may be empty,
 * we need to display at least the minimum number
 * of times for each column.
 *
 * @see $times_per_rows
 */
$max_rows = max(array($max_rows, $times_per_rows));

// build base URI
$base = "index.php?option=com_vikappointments&view=employeesearch&id_employee={$id_employee}&id_service={$id_service}";

if ($itemid)
{
	$base .= "&Itemid={$itemid}";
}

?>

<div class="emp-avail-table">

	<!-- TABLE HEADING (days and arrows) -->

	<div class="avail-table-head">

		<!-- LEFT ARROW TO SEE PREVIOUS 4 DAYS -->

		<div class="table-head-left-arrow">
			<?php
			if ($prev_day)
			{
				?>
				<a href="javascript: void(0);" onclick="loadOtherTableTimes(<?php echo $id_employee; ?>, <?php echo $prev_day; ?>);"><i class="fa fa-chevron-left"></i></a>
				<?php
			}
			else
			{
				?>
				<i class="fa fa-chevron-left"></i>
				<?php
			}
			?>
		</div>

		<!-- CURRENT DAYS -->

		<div class="table-head-center">
			<?php
			foreach (array_keys($table) as $day)
			{
				$date = JFactory::getDate($day);

				/**
				 * The dates are now displayed according to the configuration timezone.
				 *
				 * @since 1.6.1
				 */
				?>
				<div class="table-head-day">
					<div class="day-name"><?php echo JHtml::_('date', $date, 'D'); ?></div>
					<div class="day-desc"><?php echo JHtml::_('date', $date, 'j M'); ?></div>
				</div>
				<?php
			}
			?>
		</div>

		<!-- RIGHT ARROW TO SEE NEXT 4 DAYS -->

		<div class="table-right-arrow">
			<a href="javascript: void(0);" onclick="loadOtherTableTimes(<?php echo $id_employee; ?>, <?php echo $next_day; ?>);"><i class="fa fa-chevron-right"></i></a>
		</div>

	</div>

	<!-- TABLE BODY (times) -->

	<div class="avail-table-body" id="avail-tbody<?php echo $id_employee; ?>">

		<div class="table-body-arrow-col">&nbsp;</div>

		<div class="avail-table-body-cols">
			<?php
			foreach ($table as $day => $times)
			{
				$count = count($times);

				?>
				<div class="avail-table-day-col">
					<?php

					for ($i = 0; $i < $max_rows; $i++)
					{
						$hidden = $i <= $times_per_rows ? '' : ' hidden';

						// display available time
						if ($i < $count)
						{
							$date = getdate($day);
							$date = mktime($times[$i]['hour'], $times[$i]['min'], 0, $date['mon'], $date['mday'], $date['year']);
							$url  = JRoute::_($base . "&day={$day}&hour={$times[$i]['hour']}&min={$times[$i]['min']}"); 
							?>
							<div class="table-body-free-slot timetable-slot<?php echo $hidden; ?>">
								<a href="<?php echo $url; ?>">
									<?php echo date($time_format, $date); ?>
								</a>
							</div>
							<?php
						}
						// display empty slot
						else
						{
							?>
							<div class="table-body-empty-slot timetable-slot<?php echo $hidden; ?>">--</div>
							<?php
						}
					}
					?>
				</div>
				<?php
			}
			?>
		</div>

		<div class="table-body-arrow-col">&nbsp;</div>

	</div>

	<?php if ($max_rows > $times_per_rows) { ?>

		<!-- TABLE JOOMLA3810TER (show more link) -->

		<div class="avail-table-joomla3810ter">
			<a href="javascript: void(0);" onclick="showMoreTimesFromTable(<?php echo $id_employee; ?>, this);"><?php echo JText::_('VAPSHOWMORETIMES'); ?></a>
		</div>

	<?php } ?>

</div>
