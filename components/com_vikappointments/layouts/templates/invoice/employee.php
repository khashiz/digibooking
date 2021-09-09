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

$order = isset($displayData['order']) ? $displayData['order'] : array();

$has_charge = isset($order['payment']['charge']) && $order['payment']['charge'] != 0;

?>

<table width="100%"  border="0">

	<tr>
		<td>
			<table width="100%"  border="0" cellspacing="5" cellpadding="5">
				<tr>
					<td width="70%">{company_logo}<br/>{company_info}</td>
					<td width="30%"align="right" valign="bottom">
						<table width="100%" border="0" cellpadding="1" cellspacing="1">
							<tr>
								<td align="right" bgcolor="#FFFFFF"><strong><?php echo JText::_('VAPINVNUM'); ?> {invoice_number}{invoice_suffix}</strong></td>
							</tr>
							<tr>
								<td align="right" bgcolor="#FFFFFF"><strong><?php echo JText::_('VAPINVDATE'); ?> {invoice_date}</strong></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<table width="100%"  border="0" cellspacing="1" cellpadding="2">
				<tr bgcolor="#E1E1E1" style="background-color: #E1E1E1;">
					<td width="65%"><strong><?php echo JText::_('VAPINVITEMDESC'); ?></strong></td>
					<td width="35%"><strong><?php echo JText::_('VAPINVITEMPRICE'); ?></strong></td>
				</tr>
				
				<?php
				if (isset($order['subscription']))
				{
					$sub = $order['subscription'];
					?>
					<tr>
						<td width="60%"><strong><?php echo $sub['name']; ?></strong></td>
						<td width="35%"><?php echo VikAppointments::printPriceCurrencySymb($sub['price']); ?></td>
					</tr>
					<?php
				}
				?>

				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<table width="100%" border="0" cellspacing="1" cellpadding="2">
				<tr bgcolor="#E1E1E1">
					<td width="70%" colspan="2" rowspan="<?php echo $has_charge ? 4 : 3; ?>" valign="top">
						<strong><?php echo JText::_('VAPINVCUSTINFO'); ?></strong><br/>{billing_info}
					</td>
					<td width="30%" align="left">
						<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
							<td align="left"><strong><?php echo JText::_('VAPINVTOTAL'); ?></strong></td>
							<td align="right">{invoice_totalnet}</td>
						</tr></table>
					</td>
				</tr>
				<?php if ($has_charge) { ?>
					<tr bgcolor="#E1E1E1">
						<td width="30%" align="left">
							<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
								<td align="left"><strong><?php echo JText::_('VAPINVPAYCHARGE', $order['payment']['name']); ?></strong></td>
								<td align="right">{invoice_paycharge}</td>
							</tr></table>
						</td>
					</tr>
				<?php } ?>
				<tr bgcolor="#E1E1E1">
					<td width="30%" align="left">
						<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
							<td align="left"><strong><?php echo JText::_('VAPINVTAXES'); ?></strong></td>
							<td align="right">{invoice_totaltax}</td>
						</tr></table>
					</td>
				</tr>
				<tr bgcolor="#E1E1E1">
					<td width="30%" align="left" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
							<td align="left"><strong><?php echo JText::_('VAPINVGRANDTOTAL'); ?></strong></td>
							<td align="right">{invoice_grandtotal}</td>
						</tr></table>
					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>