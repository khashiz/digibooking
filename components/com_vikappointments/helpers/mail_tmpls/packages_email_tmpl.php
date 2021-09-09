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

$fields = json_decode($order['custom_f']);

/**
 * VikAppointments - Packages E-Mail Template
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
			
			<!-- TOP BOX [company logo and name] -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<div style="display: inline-block; float: left;">{logo}</div>
					<h3 style="display: inline-block; float: right;">{company_name}</h3>
				</td>
			</tr>

			<!-- ORDER NUMBER AND ORDER KEY -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 15px; font-size: 14px; background: #f2f3f7;">
						<tr>
							<td style="padding: 12px 10px; line-height: 1.4em; text-align: left;">
								<?php echo JText::_('VAPORDERNUMBER'); ?>: {order_number}
							</td>
							<td style="padding: 12px 10px; line-height: 1.4em; text-align: right;">
								{order_key}
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- ORDER STATUS -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 15px; font-size: 14px; background: #f2f3f7;">
						<tr>
							<td style="padding: 12px 10px; line-height: 1.4em; text-align: left;">
								<span style="text-transform:uppercase; font-weight:bold; color:{order_status_color}">
									{order_status}
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- TOTAL COST AND PAYMENT GATEWAY -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 15px; font-size: 14px; background: #f2f3f7;">
						<tr>
							<?php
							if (!empty($order['payment_name']))
							{
								?>
								<td style="padding: 12px 10px; line-height: 1.4em; text-align: left;">
									{order_payment}
								</td>
								<?php
							}
							?>

							<?php
							if ($order['total_cost'] > 0)
							{
								?>
								<td style="padding: 12px 10px; line-height: 1.4em; text-align: right;">
									{order_total_cost}
								</td>
								<?php
							}
							?>
						</tr>
					</table>
				</td>
			</tr>

			<!-- ITEMS LIST -->

			<?php
			foreach ($order['items'] as $p)
			{
				?>
				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-spacing: 0; margin: 10px auto 0; font-size: 14px; background: #f2f3f7;">
							<tr>
								<td style="line-height: 1.4em; text-align: left; display: flex; flex-wrap: wrap;">
									<div style="display: inline-block; width: 100%; padding: 10px; box-sizing: border-box; background: #f8f8f8; flex: 100%;">
										<div style="float:left; display: inline-block; width: 100%;">
											<?php echo $p['name']; ?> - <?php echo JText::sprintf('VAPPACKAGESMAILAPP', $p['num_app']); ?>
											<span style="margin-left: 10px; float: right;">x<?php echo $p['quantity']; ?></span>
										</div>
									</div>

									<?php
									if ($p['price'] > 0)
									{
										?>
										<div style="background: #eee; border-top: 1px solid #ddd; padding: 10px; text-align: right; flex: 100%;">
											<?php echo VikAppointments::printPriceCurrencySymb($p['price'] * $p['quantity']); ?>
										</div>
									<?php
									}
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
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

			<tr class="no-printable">
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 0; font-size: 14px; background: #f2f3f7;">
						<tr>
							<td style="padding: 0; line-height: 1.4em; text-align: left;">
								<div style="background: #eee; border-bottom: 1px solid #ddd; padding: 10px;"><?php echo JText::_('VAPORDERLINK'); ?></div>
								<div style="padding: 10px; background-color: #f2f3f7;">
									<a href="{order_link}" target="_blank" style="word-break: break-word;">{order_link}</a>
								</div>
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
 * @var string|null	 {logo}					The logo image of your company. Null if not specified.
 * @var int 		 {order_number}			The unique ID of the reservation.
 * @var string 		 {order_key}			The serial key of the reservation.
 * @var string 		 {order_status}			The status of the order [CONFIRMED, PENDING, REMOVED or CANCELLED].
 * @var string 		 {order_status_class}	The status of the order [confirmed, pending, removed or canceled].
 * @var string 		 {order_status_color}	The color of the order status.
 * @var string|null	 {order_payment}		The name of the payment processor selected, otherwise NULL.
 * @var string|null  {order_payment_notes}	The notes of the payment processor selected, otherwise NULL.
 * @var float 		 {order_total_cost}		The total cost of the order.
 * @var string		 {order_link}			The direct url to the page of the order.
 * @var string|null  {company_name}			The name of the company.
 */
