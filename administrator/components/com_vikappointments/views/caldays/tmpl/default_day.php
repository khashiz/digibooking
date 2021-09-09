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
use Hekmatinasser\Verta\Verta;

defined('_JEXEC') or die('Restricted access');

$vik 	= UIApplication::getInstance();
$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$opening = VikAppointments::getOpeningTime();
$closing = VikAppointments::getClosingTime();

$day = $this->filters['date']; // UNIX timestamp

$cell_height_pixel = 60;

/**
 * Use date string instead of UNIX timestamp.
 *
 * @since 1.6.3
 */
$date = new Verta($day);

// display only relevant hours
$opening['hour'] = $this->calendar->getMinimumHour($opening['hour']);
if ($opening['hour'] > 0)
{
	$opening['hour']--;
}
$j_date = ArasJoomlaVikApp::jgetdate();
?>

<?php echo $vik->openFieldset($date->format(JText::_('DATE_FORMAT_LC')), 'form-horizontal'); ?>

<div style="overflow-x: scroll;">
	<table class="vap-workday-calendar" cellspacing="0" style="width: 99%;">
		<thead>
			<tr>
				<th width="7%">&nbsp;</th>
				<?php
				foreach ($this->employees as $e)
				{
					?>
					<th width="12%">
						<?php echo $e->nickname; ?>
					</th>
					<?php
				}
				?>
			</tr>
		</thead>

		<tbody>
			<?php

			$end = ($closing['min'] == 0 ? $closing['hour'] - 1 : $closing['hour']);
			for ($h = $opening['hour']; $h <= $end; $h++)
			{
				?>
				<tr>
					<td style="text-align: right;">
						<?php echo ArasJoomlaVikApp::jdate($time_format, ArasJoomlaVikApp::jmktime($h, 0, 0, 1, 1, $j_date['year'])); ?>
					</td>
					<?php
					foreach ($this->employees as $e)
					{
						$td_class = array();

						// calculate timestamp of cell delimiters
						$bounds = array();

						$_date = clone $date;

						$_date->modify($h . ':00:00');
						$bounds[] = ArasJoomlaVikApp::jDateTimeToTimestamp($_date->format('Y-m-d H:i:s'));

						$_date->modify(($h + 1) . ':00:00');
						$bounds[] = ArasJoomlaVikApp::jDateTimeToTimestamp($_date->format('Y-m-d H:i:s'));

						if (isset($e->calendar))
						{
							$rects = $e->calendar->getIntersections($bounds[0], $bounds[1]);
						}
						else
						{
							$rects = array();
						}

						$td_data = array(
							'cell-from' => $h * 60,
							'cell-to'   => ($h + 1) * 60,
							'cell-day'	=> $date->format('N') % 7,
							'cell-date' => $date->format($date_format),
							'cell-emp'	=> $e->id,
						);

						// calculate divs to display
						$divs = array();

						if ($rects)
						{
							// Iterate the rects as there may be multiple cells within the same block.
							// For example, there may be the following appointments:
							// - from 14:00 to 15:30
							// - from 15:30 to 17:00
							// So, the closing div of the first appointment and the opening div of
							// the second appointment should share the same cell.
							foreach ($rects as $app)
							{
								// do not fill if not needed
								$class  = '';
								$height = '';
								$bg 	= '';
								$label 	= '&nbsp;';
								$margin = 0;

								$use_label = false;

								if ($app->startsAt($h))
								{
									$class  = 'time-starts';
									$shift  = $app->startHM() - $h * 60;
									$margin = $shift * ($cell_height_pixel / 60); // height pixel / max minutes (ratio)

									// in case the checkout is at midnight, we need to
									// return 24 * 60 (1440) in order to calculate the
									// height properly
									$end_hm = $app->endH() > 0 ? $app->endHM() : 1440;

									$class .= ' time-ends';
									$top 	= $end_hm - $h * 60;


									if ($app->isSameDay())
									{
										$diff = abs($top - $shift);
									}
									else
									{
										// appointment between 2 days
										$diff = 24 * 60 - $app->startHM();
										$class .= ' trim-end';
									}

									$height = $diff * ($cell_height_pixel / 60); // height pixel / max minutes (ratio)
									$height += ceil($height / $cell_height_pixel) - 1; // includes 1 px for each border that the div covers
									$height = "height: {$height}px;";

									$use_label = true;
								}
								else if (!$app->isSameDay() && $h == 0)
								{
									// display the remaining box of an appointment
									// that started on the previous day.
									$class  = 'time-starts trim-start time-ends';
									$top 	= $app->endHM() - $h * 60;

									$height = $top * ($cell_height_pixel / 60); // height pixel / max minutes (ratio)
									$height += ceil($height / $cell_height_pixel) - 1; // includes 1 px for each border that the div covers
									$height = "height: {$height}px;";
								}
								else if ($app->endsAt($h))
								{
									$class  = 'time-ends';
									$shift  = $app->endHM() - $h * 60;
									$margin = ($cell_height_pixel - $shift * ($cell_height_pixel / 60)) * -1; // height pixel / max minutes (ratio)
								}
								else if ($app->containsAt($h))
								{
									$class  = 'time-contains';
									$margin = 0;
								}

								$data = array();

								// build rect data
								$data['id'] 	 	= array();
								$data['service']	= array();
								$data['employee']	= array();

								foreach ($app->events() as $e)
								{
									$data['id'][] = $e->id;

									if (!in_array($e->id_service, $data['service']))
									{
										$data['service'][]  = $e->id_service;
									}

									if (!in_array($e->id_employee, $data['employee']))
									{
										$data['employee'][]  = $e->id_employee;
									}
								}

								if ($use_label)
								{
									// first block, define event label

									if (count($data['service']) == 1 && count($data['employee']) == 1)
									{
										// Only one service/employee or multiple appointments 
										// for the same service/employee.
										// Use service name as label.
										$label = $app->event('service_name');
									}
									else
									{
										$label = JText::sprintf('VAPCALNUMAPP', $app->getEventsCount());
									}
								}

								if (count($data['service']) > 1)
								{
									// use custom color for shared blocks
									$color = '00d498';
								}
								else
								{
									$color = $app->event('service_color');
								}

								$bg = 'background-color: #' . $color. ';';

								// merge app data with cell data
								$data = array_merge($data, $td_data);

								// push div within the list
								$divs[] = array(
									'class' 		=> $class,
									'height'		=> $height,
									'margin' 		=> $margin,
									'label'			=> $label,
									'background' 	=> $bg,
									'data'			=> $data,
								);

								$td_class[] = $class;
							}
						}
						else
						{
							$divs[] = array(
								'class' 		=> 'time-empty',
								'height'		=> '',
								'margin' 		=> 0,
								'label'			=> '&nbsp;',
								'background' 	=> '',
								'data'			=> $td_data,
							);

							$td_class[] = 'time-empty';
						}

						$td_class = array_unique($td_class);

						if (count($td_class) > 1)
						{
							// time-starts + time-ends
							$td_class = 'time-contains';
						}
						else
						{
							// use only the specified class
							$td_class = $td_class[0];
						}

						?>
						<td class="<?php echo $td_class; ?>" style="position: relative;">
							<?php
							foreach ($divs as $div)
							{
								$data_str = '';
								foreach ($div['data'] as $k => $v)
								{
									if (is_array($v))
									{
										$v = implode(',', $v);
									}

									$data_str .= " data-{$k}=\"{$v}\"";
								}

								?>
								<div 
									<?php echo $data_str; ?>
									class="<?php echo $div['class']; ?>"
									style="position: absolute; width:100%;
									top: <?php echo $div['margin']; ?>px;
									<?php echo $div['background']; ?>
									<?php echo $div['height']; ?>"
								>
									<span class="time-box-label">
										<?php echo $div['label']; ?>
									</span>
								</div>
								<?php
							}
							?>
						</td>
						<?php
					}
					?>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>

<?php echo $vik->closeFieldset(); ?>
