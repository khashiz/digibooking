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

$vik = UIApplication::getInstance();

$rates = isset($this->trace['rates']) ? $this->trace['rates'] : array();

?>

<div>
	<?php echo $vik->openEmptyFieldset(); ?>

		<table class="rates-table table" id="rates-table">
			<thead>
				<tr>
					<th><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
					<th><?php echo JText::_('JDETAILS'); ?></th>
					<th style="text-align:center;"><?php echo JText::_('VAPCHDISC'); ?></th>
				</tr>
			</thead>

			<tbody>

				<tr>
					<td class="rate-id"></td>
					<td class="rate-details"><?php echo JText::_('VAPBASECOST'); ?></td>
					<td class="rate-price"><?php echo VikAppointments::printPriceCurrencySymb($this->trace['basecost']); ?></td>
				</tr>

				<?php
				foreach ($rates as $rate)
				{
					?>
					<tr class="rate-child">
						<td class="rate-id"><?php echo $rate->id; ?></td>
						<td class="rate-details">
							<?php
							echo $rate->name;

							if ($rate->description)
							{
								?>
								<div><small><?php echo $rate->description; ?></small></div>
								<?php
							}
							?>
						</td>
						<td class="rate-price"><?php echo VikAppointments::printPriceCurrencySymb($rate->charge); ?></td>
					</tr>
					<?php
				}
				?>

			</tbody>

			<tjoomla3810t>

				<?php
				if ($this->finalCost != $this->rate)
				{
					// the final cost has been multiplied by the number of guests
					?>
					<tr>
						<td class="rate-id"></td>
						<td class="rate-details"><?php echo JText::_('VAPCOSTPP'); ?></td>
						<td class="rate-price"><?php echo VikAppointments::printPriceCurrencySymb($this->rate); ?></td>
					</tr>
					<?php
				}
				?>

				<tr>
					<td class="rate-id"></td>
					<td class="rate-details"><?php echo JText::_('VAPFINALCOST'); ?></td>
					<td class="rate-price"><?php echo VikAppointments::printPriceCurrencySymb($this->finalCost); ?></td>
				</tr>

			</tjoomla3810t>
		</table>

	<?php echo $vik->closeEmptyFieldset(); ?>
</div>

<script>
	
	// for debug purposes
	console.log(<?php echo json_encode($this->trace); ?>); 

</script>
