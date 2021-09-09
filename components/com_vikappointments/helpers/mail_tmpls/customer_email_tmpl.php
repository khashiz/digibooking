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
 * VikAppointments - Customer E-Mail Template
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

<div style="font-family: tahoma !important; background:#fff; color: #666; width: 100%; table-layout: fixed;">
	<div style="max-width: 600px; margin:0 auto;">

		<!--[if (gte mso 9)|(IE)]>
		<table width="800" align="center">
		<tr>
		<td>
		<![endif]-->

		<table align="center" style="margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; font-family: tahoma;">
			
			<!-- TOP BOX [company logo and name] -->

			<tr>
				<td style="padding: 0;">
					<p style="display: inline-block; float: right; max-width: 150px;">{logo}</p>
					<h3 style="display: inline-block; float: right;">{company_name}</h3>
				</td>
			</tr>

			<!-- CUSTOM POSITION TOP -->

			<tr>
				<td style="padding: 0;">
					{custom_position_top}
				</td>
			</tr>

			<!-- ORDER NUMBER BOX -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 15px; font-size: 14px; background: #f2f3f7;">
						<tr>
							<td style="padding: 12px 10px; line-height: 1.4em; text-align: right;">
								<div style="float:right; display:inline-block;">
									 <span style="font-weight:bold;"><?php echo JText::_('VAPORDERNUMBER'); ?></span>: {order_number}
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- ORDER KEY AND STATUS -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 15px; font-size: 14px; background: #f2f3f7;">
						<tr>
							<td style="padding: 12px 10px; line-height: 1.4em; text-align: left;">
								<div style="float:left; display:inline-block;">
									{order_key}
								</div>
								<div style="float:right; display:inline-block;">
									<span style="text-transform:uppercase; font-weight:bold; color:{order_status_color}">
										{order_status}
									</span>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- TOTAL COST AND PAYMENT GATEWAY -->

			<?php
			if ($orders[0]['total_cost'] > 0)
			{
				?>
				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 15px; font-size: 14px; background: #f2f3f7;">
							<tr>
								<td style="padding: 12px 10px; line-height: 1.4em; text-align: left;">
									<div style="float:right; display:inline-block;">
										<?php
										if (!empty($orders[0]['payment_name']))
										{
											// show payment name
											?>
											{order_payment}
											<?php
										}
										else
										{
											// show grand total label
											echo JText::_('VAPORDERDEPOSIT');
										}
										?>
									</div>

									<div style="float:left; display:inline-block;">{order_total_cost}</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
			}
			?>

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
									<div style="float:right; display:inline-block;">
										<?php echo JText::_('VAPORDERCOUPON'); ?>
									</div>
									<div style="float:left; display:inline-block;">
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

			<!-- CUSTOM POSITION MIDDLE -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 0 auto">
						<tr>
							<td>
								{custom_position_middle}
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- APPOINTMENTS BOX -->

			<?php
			for ($i = ($orders[0]['id_service'] == -1 ? 1 : 0); $i < count($orders); $i++)
			{
				$row = $orders[$i];

				$options_total = 0.0;
				foreach ($row['options'] as $opt)
				{
					$options_total += $opt['price'];
				}
				?>

				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 0; font-size: 14px; background: #f2f3f7;">

							<tr>
								<td style="padding: 0; line-height: 1.4em; text-align: center;">
									<div>

										<div style="padding: 10px;background-color: #f8f8f8;text-align: center; font-weight: 900;">
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

											// display separator for employee/checkin
											echo ' - ';

											/**
											 * Display employee name only if it was chosen by
											 * the customer.
											 *
											 * @since 1.6.3
											 */
											if ($row['view_emp'])
											{
												echo $row['ename'];

												?>
												<br />
												<?php
											}

											echo $row['formatted_checkin'] . ' - ' . $row['formatted_duration'];

											if ($row['formatted_location'])
											{
												?>
												<br />
												<?php

												echo $row['formatted_location'];
											}

											/**
											 * Display NET cost in case of additional options.
											 *
											 * @since 1.6.2
											 */
											if ($options_total)
											{
												?>
												<span style="float: right;">
													<?php
													echo VikAppointments::printPriceCurrencySymb($row['total_cost'] - $options_total);
													?>
												</span>
												<?php
											}
											?>
										</div>

										<?php
										if (count($row['options']))
										{
											?>
											<div style="border-top: 1px solid #ddd; padding: 10px; background-color: #eee;">
												<?php
												foreach ($row['options'] as $opt)
												{
													?>
													<div style="padding: 2px 0; width: 100%; display: inline-block;">
														<div style="float: left; display: inline-block; text-align: right;">
															<?php echo $opt['full_name']; ?>
															<span style="display: inline-block; margin-left: 10px;"><?php echo $opt['formatted_quantity']; ?></span>
														</div>
														<?php
														if ($opt['price'] != 0)
														{
															?>
															<div style="float: right; display: inline-block; text-align: right;"><?php echo $opt['formatted_price']; ?></div>
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
											<div style="border-top: 1px solid #ddd; padding: 5px 10px; text-align: center;">
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
								<div style="background: #eee; border-bottom: 1px solid #ddd; padding: 10px; font-weight: 600;font-family: tahoma; text-align: right;"><?php echo JText::_('VAPPERSONALDETAILS'); ?></div>
								<div style="padding: 10px;text-align: right;">
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

			<!-- CUSTOM POSITION BOTTOM -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 0 auto;">
						<tr>
							<td>
								{custom_position_bottom}
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- ORDER LINK -->

			<tr class="no-printable">
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 0; font-size: 14px; background: #f2f3f7;">
						<tr>
							<td style="padding: 0; line-height: 1.4em; font-weight: 600;font-family: tahoma; text-align: right;">
								<div style="background: #eee; border-bottom: 1px solid #ddd; padding: 10px;"><?php echo JText::_('VAPORDERLINK'); ?></div>
								<div style="padding: 10px;">
									<a href="{order_link}" target="_blank" style="color:#fff;background:green; padding:10px 20px;border-radius:8px; text-decoration:none;;">مشاهده آنلاین نوبت</a>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- CANCELLATION LINK -->

			<tr class="no-printable">
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 0; font-size: 14px; background: #f2f3f7;">
						<tr>
							<td style="padding: 0; line-height: 1.4em; text-align: left;">
								<?php
								if ($orders[0]['status'] == 'CONFIRMED' && VikAppointments::isCancellationEnabled())
								{
									?>
									<div style="background: #eee; border-bottom: 1px solid #ddd; padding: 10px;"><?php echo JText::_('VAPCANCELLATIONLINK'); ?></div>
									<div style="padding: 10px;">
										<a href="{cancellation_link}" target="_blank" style="word-break: break-word;">{cancellation_link}</a>
									</div>
									<?php
								}
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- CUSTOM POSITION JOOMLA3810TER -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 0 auto;">
						<tr>
							<td style="">
								{custom_position_joomla3810ter}
							</td>
						</tr>
					</table>
				</td>
			</tr>

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
 * @var string|null	 {logo}						The logo image of your company. Null if not specified.
 * @var int 		 {order_number}				The unique ID of the reservation.
 * @var string 		 {order_key}				The serial key of the reservation.
 * @var string 		 {order_status}				The status of the order [CONFIRMED, PENDING, REMOVED or CANCELLED].
 * @var string 		 {order_status_class}		The status of the order [confirmed, pending, removed or canceled].
 * @var string 		 {order_status_color}		The color of the order status.
 * @var string|null	 {order_payment}			The name of the payment processor selected (*), otherwise NULL.
 * @var string|null  {order_payment_notes}		The notes of the payment processor selected, otherwise NULL.
 * @var float 		 {order_total_cost}			The total cost of the order. (**)
 * @var string 		 {order_coupon_code}		The coupon code used for the order. (***)
 * @var string		 {order_link}				The direct url to the page of the order.
 * @var string		 {cancellation_link}		The direct url to cancel the order. (^)
 * @var string|null  {company_name}				The name of the company.
 * @var string|null  {custom_position_top}		This tag will be replaced with all the Custom Text Contents assigned to the top position.
 * @var string|null  {custom_position_middle}	This tag will be replaced with all the Custom Text Contents assigned to the middle position.
 * @var string|null  {custom_position_bottom}	This tag will be replaced with all the Custom Text Contents assigned to the bottom position.
 * @var string|null  {custom_position_joomla3810ter}	This tag will be replaced with all the Custom Text Contents assigned to the joomla3810ter position.
 *
 *
 * (*) - the payment name is displayed only if the total cost of the order is higher than 0.00 and if the website owns one or more payment processors.
 *
 * (**) - the total cost is displayed only if it is higher than 0.00.
 *
 * (***) - the coupon code is displayed only if customer used it.
 *
 * (^) - available only if the cancellation is allowed and the status of the order is CONFIRMED.
 */
