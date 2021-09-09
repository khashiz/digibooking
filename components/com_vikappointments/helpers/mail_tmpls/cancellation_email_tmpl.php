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

$vik = UIApplication::getInstance();

/**
 * VikAppointments - Cancellation E-Mail Template
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
			
			<!-- TOP BOX [logo and cancellation content] -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<div>{logo}</div>
					<div style="margin-top: 10px;">{cancellation_content}</div>
				</td>
			</tr>

			<!-- ORDER LINK -->

			<?php
			// administrator
			if ($type == 1)
			{
				?>
				<tr class="no-printable">
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-spacing: 0; margin: 10px auto 0; padding: 15px; font-size: 14px; background: #f2f3f7;">
							<tr>
								<td style="padding: 12px 10px; line-height: 1.4em; text-align: left;">
									<div>
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

			<!-- APPOINTMENTS BOX -->

			<?php
			for ($i = ($orders[0]['id_service'] == -1 ? 1 : 0); $i < count($orders); $i++)
			{
				$row = $orders[$i];

				if ($type == 1)
				{
					$url = $vik->adminUrl('index.php?option=com_vikappointments&task=editreservation&cid[]=' . $row['id']);
				}
				else
				{
					$url = $vik->routeForExternalUse('index.php?option=com_vikappointments&view=empmanres&cid[]=' . $row['id']);
				}
				?>

				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-spacing: 0; margin: 10px auto 0; font-size: 14px; background: #f2f3f7;">
							<tr>
								<td style="line-height: 1.4em; text-align: left;">
									<div style="display: inline-block; width: 100%; background: #eee; border-bottom: 1px solid #ddd; padding: 10px; box-sizing: border-box;">
										<div style="float:left; display: inline-block;"><?php echo $row['id'] . ' - ' . $row['sid']; ?></div>
										<div style="float:right; display: inline-block; color: #F01B17; text-transform: uppercase; font-weight: bold;">
											<?php echo JText::_('VAPSTATUSCANCELED'); ?>
										</div>
									</div>
									<div style="padding: 10px;">
										<?php echo $row['sname'] . ($type == 1 ? ' - '.$row['ename'] : ''); ?><br />
										<?php echo $row['formatted_checkin']; ?> - <?php echo $row['formatted_duration']; ?>
									</div>
									<div style="border-top: 1px solid #ddd; padding: 10px; background: #eee;" class="no-printable">
										<a href="<?php echo $url; ?>" target="_blank" style="word-break: break-word;"><?php echo $url; ?></a>
									</div>
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
 * @var string|null	 {logo}					 The logo image of your company.
 * @var string|null  {cancellation_content}	 The content specified in the language file at VAPORDERCANCELEDCONTENT for admin and VAPORDERCANCELEDCONTENTEMP for employee.
 * @var string		 {order_link}			 The direct url to the details page of the order.
 * @var string|null  {company_name}			 The name of the company.
 */
