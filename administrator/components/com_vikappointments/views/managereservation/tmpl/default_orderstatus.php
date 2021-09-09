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

$sel = $this->reservation;

$config = UIFactory::getConfig();
$vik 	= UIApplication::getInstance();

$handler = VAPOrderStatus::getInstance();

// in case of multi-order, merge the records with the parent
$id = isset($sel['id_parent']) && $sel['id'] != $sel['id_parent'] ? array($sel['id'], $sel['id_parent']) : $sel['id'];

$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');
// use also the seconds
$dt_format = str_replace(':i', ':i:s', $dt_format);

$tracks = $handler->getOrderTrack($id, true);

?>

<div class="span6" style="margin-left: 0;">
	<?php echo $vik->openEmptyFieldset(); ?>

		<table class="order-status-table">

			<thead>
				<tr>
					<th style="text-align: left;"><?php echo JText::_('VAPORDERSTATUS'); ?></th>
					<th style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION33'); ?></th>
					<th style="text-align: center;"><?php echo JText::_('VAPREFERER'); ?></th>
					<th style="text-align: center;"><?php echo JText::_('VAPREMOTEADDR'); ?></th>
					<th style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION37'); ?></th>
				</tr>
			</thead>

			<tbody>

				<?php

				if (count($tracks))
				{
					foreach ($tracks as $track)
					{
						?>
						<tr>
							<td style="text-align: left;">
								<span class="vapreservationstatus<?php echo strtolower($track->statusCode); ?>">
									<?php echo $track->status; ?>
								</span>
							</td>

							<td style="text-align: center;">
								<?php echo $track->createdby ? $track->name . ' (' . $track->username . ')' : strtolower(JText::_('VAPRESLISTGUEST')); ?>
							</td>
							
							<td style="text-align: center;">
								<?php echo JText::_($track->client ? 'JADMINISTRATOR' : 'JSITE'); ?>
							</td>

							<td style="text-align: center;">
								<?php echo $track->ip; ?>
							</td>

							<td style="text-align: center;">
								<span class="os-tooltip" title="<?php echo JHtml::_('date', $track->createdon, $dt_format); ?>">
									<?php echo JHtml::_('date.relative', $track->createdon, null, null, $dt_format); ?>
								</span>
							</td>
						</tr>
						<?php

						if ($track->comment)
						{
							?>
							<tr class="track-comment">
								<td colspan="5" style="text-align: left;">
									<span style="width:98%;display:inline-block;">
										<?php echo $track->comment; ?>
									</span>

									<?php if ($track->id_order != $sel['id']) { ?>
										<i class="fa fa-chain os-tooltip" title="<?php echo JText::_('VAPPARENTORDER'); ?>"></i>
									<?php } ?>
								</td>
							</tr>
							<?php
						}
					}
				}
				else
				{
					?>
					<tr class="track-warning">
						<td colspan="5">
							<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
						</td>
					</tr>
					<?php
				}
				?>

			</tbody>

		</table>

	<?php echo $vik->closeEmptyFieldset(); ?>
</div>

<div class="span6">
	<?php echo $vik->openEmptyFieldset(); ?>

		<!-- STATUS - Dropdown -->

		<?php
		$elements = array();
		foreach (array('CONFIRMED', 'PENDING', 'REMOVED', 'CANCELED') as $s)
		{
			$elements[] = JHtml::_('select.option', $s, 'VAPSTATUS' . $s);
		}
		?>
		<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION19') . ':'); ?>
			<select class="vap-status-sel">
				<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['status'], true); ?>
			<select>
		<?php echo $vik->closeControl(); ?>

		<!-- COMMENT - Textarea -->

		<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW9') . ':'); ?>
			<textarea name="comment" style="width:80%;height:100px;" disabled></textarea>
		<?php echo $vik->closeControl(); ?>

	<?php echo $vik->closeEmptyFieldset(); ?>
</div>

<script>

	jQuery(document).ready(function() {

		jQuery('.os-tooltip').tooltip();

		jQuery('.vap-status-sel').on('change', function() {
			var disabled = jQuery(this).val() == '<?php echo $sel['status']; ?>';
			jQuery('textarea[name="comment"]').attr('disabled', disabled);
		});

	});

</script>
