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
defined('_JEXEC') or die('Restricted Area');

$fields = json_decode($orders[0]['custom_f']);

/**
 * VikAppointments - Employee E-Mail Template
 * @see the bottom of the page to check the available TAGS to use.
 */

?>

<style>
	@media print {
		.no-printable {
			display: none;
		}
	}
</style>

<div style="background:#fff; color: #666; width: 100%; table-layout: fixed;">
	<div style="max-width: 600px; margin:0 auto;">

		<!--[if (gte mso 9)|(IE)]>
		<table width="800" align="center">
		<tr>
		<td>
		<![endif]-->

		<table align="center" style="margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; font-family: sans-serif;">
			
			<!-- HEADING TITLE -->

			<tr>
				<td style="padding: 0;">
					<h3 style="display: inline-block;"><?php echo JText::_('VAPADMINEMAILHEADTITLE'); ?></h3>
				</td>
			</tr>

			<!-- STATUS AND TOTAL COST -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 15px; font-size: 14px; background: #f2f3f7;">
						<tr>
							<td style="padding: 12px 10px; line-height: 1.4em; text-align: left;">
								<div style="float:left; display:inline-block;">
									<span style="text-transform:uppercase; font-weight:bold; color:{order_status_color}">
										{order_status}
									</span>
								</div>

								<?php
								if ($orders[0]['total_cost'] > 0)
								{
									?>
									<div style="float:right; display:inline-block;">{order_total_cost}</div>
									<?php
								}
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- COUPON CODE -->

			<?php
			if (!empty($orders[0]['coupon_str']))
			{
				?>
				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 15px; font-size: 14px; background: #f2f3f7;">
							<tr>
								<td style="padding: 12px 10px; line-height: 1.4em; text-align: left;">
									<div style="float:left; display:inline-block;">
										<?php echo JText::_('VAPORDERCOUPON'); ?>
									</div>
									<div style="float:right; display:inline-block;">
										{order_coupon_code}
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
			}
			?>

			<!-- APPOINTMENTS BOX -->

			<?php
			for ($i = ($orders[0]['id_service'] == -1 ? 1 : 0); $i < count($orders); $i++)
			{
				$row = $orders[$i];
				
				?>
				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 0; font-size: 14px; background: #f2f3f7;">
							
							<tr>
								<td style="padding: 0; line-height: 1.4em; text-align: left;">
									<div>

										<div style="padding: 10px; border-bottom: 0; background-color: #f8f8f8;">
											<?php echo $row['id'] . ' - ' . $row['sid']; ?>

											<br />

											<?php
											echo $row['sname'];

											/**
											 * Display number of participants if higher than 1.
											 *
											 * @since 1.6.3
											 */
											if ($row['people'] > 1)
											{
												echo ' x' . $row['people'] . ' ';
											}
											
											echo ' - ' . $row['formatted_checkin'] . ' - ' . $row['formatted_duration'];

											if ($row['formatted_location'])
											{
												?>
												<br />
												<?php

												echo $row['formatted_location'];
											}
											?>
										</div>

										<?php
										if (count($row['options']))
										{
											?>
											<div style="padding: 10px; border-top: 1px solid #ddd; background-color: #eee;">
												<?php
												foreach ($row['options'] as $opt)
												{
													?>
													<div style="padding: 2px 0; width: 100%; display: inline-block;">
														<div style="float: left; display: inline-block; text-align: left;">
															<?php echo $opt['full_name']; ?>
															<span style="display: inline-block; margin-left: 10px;"><?php echo $opt['formatted_quantity']; ?></span>
														</div>
														<?php
														if ($opt['price'] != 0)
														{
															?>
															<div style="float:right; display: inline-block; text-align: right;"><?php echo $opt['formatted_price']; ?></div>
															<?php
														}
														?>
													</div>
													<?php
												}
												?>
											</div>
											<?php
										}

										if ($row['total_cost'] > 0)
										{
											?>
											<div style="border-top: 1px solid #ddd; padding: 5px; text-align: right;">
												<span><?php echo $row['formatted_total']; ?></span>
											</div>
											<?php
										}
										?>
										
									</div>
								</td>
							</tr>

						</table>
					</td>
				</tr>
				<?php
				// end for
			}
			?>

			<!-- CUSTOMER DETAILS -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 0; font-size: 14px; background: #f2f3f7;">
						<tr>
							<td style="padding: 0; line-height: 1.4em; text-align: left;">
								<div style="background: #eee; border-bottom: 1px solid #ddd; padding: 10px;"><?php echo JText::_('VAPPERSONALDETAILS'); ?></div>
								<div style="padding: 10px; background-color: #f2f3f7;">
								<?php
								foreach ($fields as $label => $value)
								{
									?>
									<div style="padding: 2px 0;">
										<div style="display: inline-block; width: 180px;"><?php echo $label; ?>:</div>
										<div style="display: inline-block;"><?php echo $value; ?></div>
									</div>
									<?php
								}
								?>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- ORDER LINK -->

			<?php
			if (count($orders) == 1)
			{
				?>
				<tr class="no-printable">
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 0; font-size: 14px; background: #f2f3f7;">
							<tr>
								<td style="padding: 0; line-height: 1.4em; text-align: left;">
									<div style="background: #eee; border-bottom: 1px solid #ddd; padding: 10px;"><?php echo JText::_('VAPORDERLINK'); ?></div>
									<div style="padding: 10px;">
										<a href="{order_link}" target="_blank" style="word-break: break-word;">{order_link}</a>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
			}
			?>

			<!-- CONFIRMATION LINK -->

			<?php
			if ($orders[0]['status'] == 'PENDING' && count($orders) == 1)
			{
				?>
				<tr class="no-printable">
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 0; font-size: 14px; background: #f2f3f7;">
							<tr>
								<td style="padding: 0; line-height: 1.4em; text-align: left;">
									<div style="background: #eee; border-bottom: 1px solid #ddd; padding: 10px;"><?php echo JText::_('VAPCONFIRMATIONLINK'); ?></div>
									<div style="padding: 10px;">
										<a href="{confirmation_link}" target="_blank" style="word-break: break-word;">{confirmation_link}</a>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
			}
			?>

		</table>

		<!--[if (gte mso 9)|(IE)]>
		</td>
		</tr>
		</table>
		<![endif]-->

	</div>
</div>

<?php
/**
 * @var string|null	 {logo}					The logo image of your company. Null if not specified.
 * @var string 		 {order_status}			The status of the order [CONFIRMED, PENDING, REMOVED or CANCELLED].
 * @var string 		 {order_status_class}	The status of the order [confirmed, pending, removed or canceled].
 * @var string 		 {order_status_color}	The color of the order status.
 * @var string 		 {order_payment}		The name of the payment processor selected. Returns "None" if empty.
 * @var float 		 {order_total_cost}		The total cost of the appointments.
 * @var string 		 {order_coupon_code}	The coupon code used for the order. Returns "None" if empty.
 * @var string		 {order_link}			The direct url to the page of the order.
 * @var string|null	 {confirmation_link}	The direct url to confirm the order. Null when the status of the order is not PENDING.
 * @var string|null  {company_name}			The name of the company.
 */
