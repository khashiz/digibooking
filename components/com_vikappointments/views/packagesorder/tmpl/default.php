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

$order          = $this->order;
$payment        = $this->payment;
$array_order    = $this->array_order;

$config = UIFactory::getConfig();

$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

?>

<div class="vaporder-backbox">
	<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=packorders' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" class="vap-btn blue">
		<?php echo JText::_('VAPALLORDERSBUTTON'); ?>
	</a>
</div>

<div class="vap-payment-position">
	<div id="vap-payment-position-top-left" style="text-align: left;"></div>
	<div id="vap-payment-position-top-center" style="text-align: center;"></div>
	<div id="vap-payment-position-top-right" style="text-align: right;"></div>
</div>
	
<div class="vaporderpagediv">
	
	<div class="vaporderboxcontent">
		<div class="vap-order-first">
			<h3 class="vaporderheader vap-head-first"><?php echo JText::_('VAPORDERTITLE1'); ?></h3>

			<?php 
			$is = file_exists(VAPINVOICE . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . $order['id'] . '-' . $order['sid'] . '.pdf');
			if ($is) { ?>

				<div class="vap-printable">
					<a
						href="<?php echo VAPINVOICE_URI . "packages/{$order['id']}-{$order['sid']}.pdf"; ?>"
						target="_blank"
						title="<?php echo JText::_('VAPORDERINVOICEACT'); ?>"
					>
						<i class="fa fa-file-pdf-o"></i>
					</a>
				</div>

			<?php } ?>
		</div>
		<div class="vaporderboxleft">
			<div class="vapordercontentinfo">
				<div class="vaporderinfo">
					<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERNUMBER'); ?>:</span> <span class="vaporderinfo-value"><?php echo $order['id']; ?></span>
				</div>

				<div class="vaporderinfo">
					<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERKEY'); ?>:</span> <span class="vaporderinfo-value"><?php echo $order['sid']; ?></span>
				</div>

				<div class="vaporderinfo" style="display: inline-block;">
					<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERSTATUS'); ?>:</span>
				</div>
				<div class="vaporderinfo vapreservationstatus<?php echo strtolower($order['status']); ?>" style="display: inline-block;">
					<span class="vaporderinfo-value"><?php echo JText::_('VAPSTATUS' . $order['status']); ?></span>
				</div>
				
				<?php if (!empty($order['payment_name']) && $order['total_cost'] > 0) { ?>
					<br clear="all"/><br/>

					<div class="vaporderinfo">
						<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERPAYMENT'); ?>:</span> <span class="vaporderinfo-value"><?php echo $order['payment_name'] . ($order['payment_charge'] != 0 ? ' (' . ($order['payment_charge'] > 0 ? '+' : '') . VikAppointments::printPriceCurrencySymb($order['payment_charge']) . ')' : '' ); ?></span>
					</div>

					<div class="vaporderinfo">
						<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERRESERVATIONCOST'); ?>:</span> <span class="vaporderinfo-value"><?php echo VikAppointments::printPriceCurrencySymb(($order['total_cost'])); ?></span>
					</div>
				<?php } ?>

				<?php if ($order['total_cost'] > 0) { ?>
					<div class="vaporderinfo">
						<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERDEPOSIT'); ?>:</span> <span class="vaporderinfo-value"><?php echo VikAppointments::printPriceCurrencySymb($order['total_cost'] + $order['payment_charge']); ?></span>
					</div>

					<?php if ($order['tot_paid'] > 0) { ?>
						<div class="vaporderinfo">
							<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERTOTALPAID'); ?>:</span> <span class="vaporderinfo-value"><?php echo VikAppointments::printPriceCurrencySymb($order['tot_paid']); ?></span>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>

		<div class="vaorderboxright">
			<?php
			$_custf = (array) json_decode($order['custom_f'], true);
			
			foreach ($_custf as $key => $val)
			{
				if (!empty($val))
				{
					?><div class="vaporderinfo"><span class="vaporderinfo-lbl"><?php echo JText::_($key);?>:</span> <span class="vaporderinfo-value"><?php echo $val; ?></span></div><?php
				}
			}
			?>
		</div>
	</div>
	
	<?php foreach ($order['items'] as $i) { ?>
	   <div class="vaporderboxcontent">
			<div class="vap-order-first">
				<h3 class="vaporderheader vap-head-first"><?php echo $i['title']; ?></h3>
			</div>

			<?php foreach ($i['packages'] as $p) { ?>
			
				<div class="vaporderdetailsbox">    
					<div class="vapordercontentinfoleft" style="width: 100%">
						<h3 class="vaporderheader"><?php echo JText::_('VAPORDERTITLE2'); ?></h3>
						<div class="vapordercontentinfo">
							<div class="vaporderinfo">
								<span class="name"><?php echo $p['name']; ?></span>
								<span class="numapp"><?php echo JText::sprintf('VAPPACKAGESMAILAPP', $p['num_app']); ?></span>
								<span class="quantity">x<?php echo $p['quantity']; ?></span>
								<span class="price"><?php echo VikAppointments::printPriceCurrencySymb($p['price']); ?></span>
							</div>

							<?php if ($p['used_app'] > 0) { ?>
								<div class="vaporderinfo">
									<?php echo JText::sprintf('VAPPACKAGELASTUSED', $p['used_app'], $p['num_app'], VikAppointments::formatTimestamp($dt_format, $p['modifiedon'])); ?>
								</div>
							<?php } ?>

							<div class="vap-pack-avservices">
								<div class="services-title"><?php echo JText::_('VAPPACKAVAILABLESERVICES'); ?></div>
								<div class="services-list">
									<?php
									if (count($p['services']))
									{
										foreach ($p['services'] as $service)
										{
											$uri = "index.php?option=com_vikappointments&view=servicesearch&id_ser={$service['id']}";

											if ($this->itemid)
											{
												$uri .= "&Itemid={$this->itemid}";
											}

											?>
											<a href="<?php echo JRoute::_($uri); ?>"><?php echo $service['name']; ?></a>
											<?php 
										}
									}
									else
									{
										$uri = "index.php?option=com_vikappointments&view=serviceslist";

										if ($this->itemid)
										{
											$uri .= "&Itemid={$this->itemid}";
										}
											
										?>
										<a href="<?php echo JRoute::_($uri); ?>"><?php echo JText::_('VAPPACKALLSERVICES'); ?></a>
										<?php
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>

			<?php } ?>
		</div>

	<?php } ?>

</div>

<div class="vap-payment-position">
	<div id="vap-payment-position-bottom-left" style="text-align: left;"></div>
	<div id="vap-payment-position-bottom-center" style="text-align: center;"></div>
	<div id="vap-payment-position-bottom-right" style="text-align: right;"></div>
</div>

<?php 

if (count($array_order) > 0 && !empty($payment['file']))
{	
	/**
	 * Instantiate the payment using the platform handler.
	 *
	 * @since 1.6.3
	 */
	$obj = UIApplication::getInstance()->getPaymentInstance($payment['file'], $array_order, $payment['params']);
	
	echo '<div id="vap-pay-box">';

	if (!empty($payment['prenote']))
	{
		?>
		<div class="vappaymentouternotes">
			<div class="vappaymentnotes">
				<?php
				/**
				 * Render HTML description to interpret attached plugins.
				 * 
				 * @since 1.6.3
				 */
				echo VikAppointments::renderHtmlDescription($payment['prenote'], 'paymentorder');
				?>
			</div>
		</div>
		<?php
	}

	$obj->showPayment();

	echo '</div>';

	if (strlen($payment['position'])) { ?>

		<script>
			jQuery(document).ready(function(){
				jQuery('#vap-pay-box').appendTo('#<?php echo $payment["position"]; ?>');
			});
		</script>

	<?php }
	
}
else if (!empty($payment['note']) && $order['status'] == 'CONFIRMED')
{
	?>
	<div class="vappaymentouternotes">
		<div class="vappaymentnotes">
			<?php
			/**
			 * Render HTML description to interpret attached plugins.
			 * 
			 * @since 1.6.3
			 */
			echo VikAppointments::renderHtmlDescription($payment['note'], 'payment');
			?>
		</div>
	</div>
	<?php
}
