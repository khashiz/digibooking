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
if ($id_service == 1) {
    $times_per_rows = 1;
} elseif ($id_service == 5) {
    $times_per_rows = 5;
}

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
<hr class="uk-display-block uk-margin-remove">
<div class="emp-avail-table uk-padding-small uk-text-zero">
	<!-- TABLE HEADING (days and arrows) -->
	<div class="uk-grid-small" data-uk-grid>
		<!-- CURRENT DAYS -->
		<div class="uk-width-1-1">
            <div>
                <div class="uk-child-width-1-4 uk-grid-small uk-grid-divider" data-uk-grid>
                    <?php foreach (array_keys($table) as $day) { ?>
                        <?php $date = JFactory::getDate($day); ?>
                        <div>
                            <div class="uk-text-tiny font uk-visible@m"><?php echo JHtml::_('date', $date, 'D'); ?></div>
                            <div class="uk-text-tiny font fnum"><?php echo JHtml::_('date', $date, 'j M'); ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
		</div>
	</div>
	<!-- TABLE BODY (times) -->
	<div id="avail-tbody<?php echo $id_employee; ?>">
		<div class="uk-child-width-1-4 uk-grid-small uk-grid-divider" data-uk-grid>
			<?php foreach ($table as $day => $times) { ?>
                <?php $count = count($times); ?>
				<div>
					<?php for ($i = 0; $i < $max_rows; $i++) { ?>
                        <?php $hidden = $i <= $times_per_rows ? '' : ' hidden'; ?>
                        <?php if ($i < $count) { ?>
                            dd
                            <?php
							$date = getdate($day);
							$date = mktime($times[$i]['hour'], $times[$i]['min'], 0, $date['mon'], $date['mday'], $date['year']);
							$url  = JRoute::_($base . "&day={$day}&hour={$times[$i]['hour']}&min={$times[$i]['min']}"); 
							?>
							<div class="<?php echo $hidden; ?>">
                                <?php if ($id_service == 1) { ?>
                                    <a href="<?php echo $url; ?>" class="uk-text-tiny uk-display-block uk-button uk-button-success uk-border-rounded slutButtonText uk-padding-small"><?php echo JTEXT::_('RESERVABLE'); ?></a>
                                <?php } else { ?>
                                    <a href="<?php echo $url; ?>" class="uk-text-tiny uk-display-block uk-button uk-button-success uk-border-rounded slutButtonTime"><?php echo date($time_format, $date); ?></a>
                                <?php } ?>
							</div>
                        <?php } else { ?>
                            <?php if ($id_service == 1) { ?>
                                <div class="uk-text-tiny uk-text-danger uk-height-1-1 uk-flex uk-flex-center uk-flex-bottom timetable-slot<?php echo $hidden; ?>">
                                    <img src="<?php echo JURI::base().'images/sprite.svg#x-lg'; ?>" width="16" height="16" class="uk-margin-small-bottom uk-margin-top" data-uk-svg>
                                </div>
                            <?php } else { ?>
                                <div class="uk-text-tiny uk-background-muted uk-border-rounded slutButtonTime uk-text-tiny uk-text-muted uk-width-1-1 timetable-slot<?php echo $hidden; ?>">--</div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
				</div>
            <?php } ?>
		</div>
	</div>

    <div class="uk-margin-top">
        <div class="uk-grid-divider uk-grid-small uk-child-width-1-6 uk-flex-center" data-uk-grid>
            <!-- LEFT ARROW TO SEE PREVIOUS 4 DAYS -->
            <div class="uk-text-secondary">
                <?php if ($prev_day) { ?>
                    <a data-uk-tooltip="title: <?php echo JTEXT::_('X_DAYS_PREV'); ?>; pos: right; offset: 10;" href="javascript: void(0);" class="uk-link-reset uk-display-inline-block" onclick="loadOtherTableTimes(<?php echo $id_employee; ?>, <?php echo $prev_day; ?>);">
                        <img src="<?php echo JURI::base().'images/sprite.svg#arrow-right-circle-fill'; ?>" width="20" height="20" data-uk-svg>
                    </a>
                <?php } ?>
            </div>
            <!-- RIGHT ARROW TO SEE NEXT 4 DAYS -->
            <div class="uk-text-secondary">
                <a data-uk-tooltip="title: <?php echo JTEXT::_('X_DAYS_NEXT'); ?>; pos: left; offset: 10;" href="javascript: void(0);" class="uk-link-reset uk-display-inline-block" onclick="loadOtherTableTimes(<?php echo $id_employee; ?>, <?php echo $next_day; ?>);">
                    <img src="<?php echo JURI::base().'images/sprite.svg#arrow-left-circle-fill'; ?>" width="20" height="20" data-uk-svg>
                </a>
            </div>
        </div>
    </div>

	<?php if ($max_rows > $times_per_rows && $id_service != 1) { ?>
		<!-- TABLE JOOMLA3810TER (show more link) -->
		<div class="">
			<a href="javascript: void(0);" onclick="showMoreTimesFromTable(<?php echo $id_employee; ?>, this);"><?php echo JText::_('VAPSHOWMORETIMES'); ?></a>
		</div>
	<?php } ?>

</div>