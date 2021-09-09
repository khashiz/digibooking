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

$orders 		= $this->orders;
$payment 		= $this->payment;
$array_order 	= $this->array_order;

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

$ok_canc_orders = (bool) VikAppointments::isCancellationEnabled();

$at_least_canc_order = false;

$still_confirmed_orders = 0;
foreach ($orders as $o)
{
	if ($o['id_parent'] != -1 && $orders[0]['status'] == $o['status'])
	{
		$still_confirmed_orders++;  
	}
}

$itemid = $this->itemid;

if (!count($orders))
{
	exit ('No direct access');
}

?>

<?php if (!JFactory::getUser()->guest) { ?>

	<div class="vaporder-backbox">
		<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders'); ?>" class="vap-btn blue">
			<?php echo JText::_('VAPALLORDERSBUTTON'); ?>
		</a>
	</div>

<?php } ?>

<div class="vap-payment-position">
	<div id="vap-payment-position-top-left" style="text-align: left;"></div>
	<div id="vap-payment-position-top-center" style="text-align: center;"></div>
	<div id="vap-payment-position-top-right" style="text-align: right;"></div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=order'); ?>" name="orderform" id="orderform" method="get">
		
	<?php 
		
	$order = $orders[0];
		
	$coupon_str = '';
	if (strlen($order['coupon_str']))
	{
		$coupon = explode(';;', $order['coupon_str']);
		$tcost_no_coupon = $order['total_cost'];
		
		if ($coupon[1] == 2)
		{
			$tcost_no_coupon += $coupon[2];
		}
		else if ($coupon[2] < 100)
		{
			$tcost_no_coupon = $tcost_no_coupon / (1 - ($coupon[2] / 100.0));
		}
		else
		{
			$tcost_no_coupon = 0;
		}
		
		$coupon_str = ' (' . VikAppointments::printPriceCurrencySymb($tcost_no_coupon) . ' - ' . ($coupon[1] == 2 ? VikAppointments::printPriceCurrencySymb($coupon[2]) : $coupon[2] . ' %' ) . ')';
	}
	
	// if "full amount" and "optional deposit", the deposit won't be calculated
	$deposit = VikAppointments::getDepositAmountToLeave($order['total_cost'], $this->payFullAmount);

	$global_cancel_uri = JRoute::_("index.php?option=com_vikappointments&task=cancel_order&oid={$order['id']}&sid={$order['sid']}&parent={$order['id']}&Itemid={$itemid}");
	
	?>
		
	<div class="vaporderpagediv">
			
		<div class="vaporderboxcontent">
				
			<div class="vap-order-first">
				<h3 class="vaporderheader vap-head-first"><?php echo JText::_('VAPORDERTITLE1'); ?></h3>
				<?php if (VikAppointments::isPrintableOrders()) { ?>

					<div class="vap-printable">
						<a 
							href="<?php echo 'index.php?option=com_vikappointments&task=printorder&oid=' . $orders[0]['id'] . '&sid=' . $orders[0]['sid']; ?>&tmpl=component" 
							target="_blank"
							title="<?php echo JText::_('VAPORDERPRINTACT'); ?>"
						>
							<i class="fa fa-print"></i>
						</a>
					</div>

				<?php } ?>

				<?php 
				$is = file_exists(VAPINVOICE . DIRECTORY_SEPARATOR . $order['id'] . '-' . $order['sid'] . '.pdf');
				if ($is) { ?>

					<div class="vap-printable">
						<a
							href="<?php echo VAPINVOICE_URI . "{$order['id']}-{$order['sid']}.pdf"; ?>"
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
						
					<?php if (!empty($payment['name']) && $order['total_cost'] > 0) { ?>
						<br clear="all"/><br/>

						<div class="vaporderinfo">
							<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERPAYMENT'); ?>:</span> <span class="vaporderinfo-value"><?php echo $payment['name'] . ($payment['charge'] != 0 ? ' (' . ($payment['charge'] > 0 ? '+' : '' ) . VikAppointments::printPriceCurrencySymb($payment['charge']) . ')' : '' ); ?></span>
						</div>

						<?php if ($deposit === false) { ?>
							<div class="vaporderinfo">
								<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERRESERVATIONCOST'); ?>:</span> <span class="vaporderinfo-value"><?php echo VikAppointments::printPriceCurrencySymb($order['total_cost']); ?></span>
							</div>
						<?php } ?>
					<?php } ?>

					<?php if ($order['total_cost'] > 0) { ?>
						<div class="vaporderinfo">
							<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERDEPOSIT'); ?>:</span> <span class="vaporderinfo-value"><?php echo VikAppointments::printPriceCurrencySymb($order['total_cost'] + $payment['charge']) . $coupon_str; ?></span>
						</div>

						<?php if ($deposit !== false) { ?>
							
						<?php } ?>

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
						?><div class="vaporderinfo"><span class="vaporderinfo-lbl"><?php echo JText::_($key); ?>:</span> <span class="vaporderinfo-value"><?php echo $val; ?></span></div><?php
					}
				}
				?>
			</div>

		</div>
			
		<?php

        date_default_timezone_set('Asian/Tehran');
		$old_service = -1;
		foreach ($orders as $ord)
		{
			if ($ord['id_parent'] != -1 && $ord['status'] == $order['status'])
			{
				$cancel_uri = JRoute::_("index.php?option=com_vikappointments&task=cancel_order&oid={$ord['id']}&sid={$ord['sid']}&parent={$order['id']}&Itemid={$itemid}");

				// SET EMPLOYEE TIMEZONE
				VikAppointments::setCurrentTimezone($ord['ord_timezone']);
					
				if ($old_service != $ord['id_service'])
				{
					if ($old_service != -1)
					{
						?></div><?php
					}
					?>
					<div class="vaporderboxcontent">
						<div class="vap-order-first">
							<h3 class="vaporderheader vap-head-first"><?php echo $ord['sname']; ?></h3>
						</div>
						<?php
				} ?>
					
				<div class="vaporderdetailsbox">    
					<div class="vapordercontentinfoleft">
						<h3 class="vaporderheader"><?php echo JText::_('VAPORDERTITLE2'); ?></h3>
						<div class="vapordercontentinfo">
							<div class="vaporderinfo">
								<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERBEGINDATETIME'); ?>:</span> <span class="vaporderinfo-value"><?php echo ArasJoomlaVikApp::jdate($date_format, $ord['checkin_ts']); ?></span>
							</div>

							<div class="vaporderinfo">
								<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERENDDATETIME'); ?>:</span> <span class="vaporderinfo-value"><?php echo ArasJoomlaVikApp::jdate($date_format, VikAppointments::getCheckout($ord['checkin_ts'], $ord['duration'])); ?></span>
							</div>

							<?php if ($ord['view_emp']) { ?>
								<div class="vaporderinfo">
									<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDEREMPLOYEE'); ?>:</span> <span class="vaporderinfo-value"><?php echo $ord['ename']; ?></span>
								</div>
							<?php } ?>

							<div class="vaporderinfo">
								<span class="vaporderinfo-lbl"><?php echo JText::_('VAPORDERSERVICE'); ?>:</span> <span class="vaporderinfo-value"><?php echo $ord['sname'] . ($ord['total_cost'] > 0 ? ' ' . VikAppointments::printPriceCurrencySymb($ord['total_cost']) : '') . ' (' . $ord['duration'] . ' ' . JText::_('VAPSHORTCUTMINUTE') . ')'; ?></span>
							</div>

							<?php if ($ord['people'] > 1) { ?>
								<div class="vaporderinfo">
									<span class="vaporderinfo-lbl"><?php echo JText::_('VAPSUMMARYPEOPLE'); ?>:</span> <span class="vaporderinfo-value"><?php echo $ord['people']; ?></span>
								</div>
							<?php } ?>
						</div>
					</div>
					
					<?php if (count($ord['options'])) { ?>
						<div class="vapordercontentinforight">
							<h3 class="vaporderheader"><?php echo JText::_('ddd'); ?></h3>
							<div class="vapordercontentinfo">
								<?php foreach ($ord['options'] as $opt) { ?>
									<div class="vaporderinfo">
										<?php
										echo $opt['name'] . (strlen($opt['var_name']) ? " - " . $opt['var_name'] : '') 
											. ($opt['single'] ? ' x' . $opt['quantity'] : '') 
											. ($opt['price'] != 0 ? ' ' . VikAppointments::printPriceCurrencySymb($opt['price']) : '');
										?>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>

					<?php
					// CANCELLATION
					if ($still_confirmed_orders > 1)
					{
						if (VikAppointments::canUserCancelOrder($ord['checkin_ts']) && $ord['status'] == 'CONFIRMED')
						{ 
							$at_least_canc_order = true;
							?>
							<div class="vapordercancdiv">
								<button type="button" class="vap-btn red" onClick="vapCancelButtonPressed('<?php echo $cancel_uri; ?>');">
									<i class="fa fa-calendar-times-o"></i>
									<?php echo JText::_('VAPORDERCANCBUTTON'); ?>
								</button>
							</div>
							<?php
						}
						else
						{
							$ok_canc_orders = false;
						}
					}
					else
					{
						$at_least_canc_order = true;
					}

					?>
				</div>
				<?php
					$old_service = $ord['id_service'];
				}
			} ?>

			<?php
            //var_dump(date_default_timezone_get());
			// CANCELLATION

			if ($ok_canc_orders && $order['status'] == 'CONFIRMED')
			{ 
				$canc_all_text = 'VAPORDERCANCALLBUTTON';
				if (count($orders) == 1)
				{
					$canc_all_text = 'VAPORDERCANCBUTTON';
				}

				if (count($orders) > 1 || VikAppointments::canUserCancelOrder($order['checkin_ts'])) { ?>
					<div class="vapordercancdiv vapcancallbox">
						<button type="button" class="vap-btn red" onClick="vapCancelButtonPressed('<?php echo $global_cancel_uri; ?>');">
							<i class="fa fa-calendar-times-o"></i>
							<?php echo JText::_($canc_all_text); ?>
						</button>
					</div>
				<?php } ?>
			<?php } ?>

			<!-- closure for the service title heading div -->
		</div>
			
	</div>

	<input type="hidden" name="ordnum" value="<?php echo $order['id']; ?>" />
	<input type="hidden" name="ordkey" value="<?php echo $order['sid']; ?>" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="view" value="order" />
</form>

<div class="vap-payment-position">
	<div id="vap-payment-position-bottom-left" style="text-align: left;"></div>
	<div id="vap-payment-position-bottom-center" style="text-align: center;"></div>
	<div id="vap-payment-position-bottom-right" style="text-align: right;"></div>
</div>

<div id="dialog-confirm" title="<?php echo JText::_('VAPCANCELORDERTITLE');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-cancel" style="float: left; margin: 0 7px 20px 0;"></span>
		<span><?php echo JText::_('VAPCANCELORDERMESSAGE'); ?></span>
	</p>
</div>

<script>

	function payFullAmount(pay) {
		jQuery('.vap-deposit-choice').hide();
		jQuery('#orderform').append('<input type="hidden" name="payfull" value="' + pay + '" />');
		jQuery('#orderform').submit();
	}

</script>

<?php
if (count($orders))
{
	if (count($array_order) > 0 && !empty($payment['file']))
	{	
		if ($deposit !== false)
		{
			$array_order['total_to_pay'] 	= $deposit; 
			$array_order['total_net_price'] = $deposit; 
		}
		
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

		if ($config->getUint('usedeposit') == 1 && ($deposit || $this->payFullAmount))
		{
			$value = $deposit ? 1 : 0;
			?>
			<div class="vap-deposit-choice">
				<input type="checkbox" value="<?php echo $value; ?>" onchange="payFullAmount(<?php echo $value; ?>);" id="deposit-checkbox" />
				<label for="deposit-checkbox"><?php echo JText::_('VAPORDERPAYFULLDEPOSIT' . ($deposit ? '' : 'BACK')); ?></label>
			</div>
			<?php
		}

		$obj->showPayment();

		echo '</div>';

		if (strlen($payment['position'])) { ?>

			<script>
				jQuery(document).ready(function() {
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
	
	// CANCELLATION SCRIPT
	
	if ($at_least_canc_order)
	{
		?>
		<script>
			jQuery(document).ready(function() {
				if (window.location.hash === '#cancel') {
					vapCancelButtonPressed('<?php echo $global_cancel_uri; ?>');
				} 
			});

			function vapCancelOrder(uri) {
				document.location.href = uri;
			}
			
			function vapCancelButtonPressed(uri) {
				jQuery( "#dialog-confirm" ).dialog({
					resizable: false,
					height: 180,
					modal: true,
					buttons: {
						"<?php echo JText::_('VAPCANCELORDEROK'); ?>": function() {
							jQuery(this).dialog("close");
							vapCancelOrder(uri);
						},
						"<?php echo JText::_('VAPCANCELORDERCANC'); ?>": function() {
							jQuery(this).dialog("close");
						}
					}
				});
			}
		</script>
	<?php }
	
} ?>

<?php if (count($orders) && count($this->ordersLocations)) { ?>
	<div id="vap-loc-googlemap" style="width:100%;height:380px;margin-top:10px;"></div>
	
	<script>

		jQuery(document).ready(function(){
			google.maps.event.addDomListener(window, 'load', vapInitializeGoogleMap);
		});
		
		function vapInitializeGoogleMap() {
		
			var COORDINATES = <?php echo json_encode($this->ordersLocations); ?>;
			
			var map = new google.maps.Map(document.getElementById('vap-loc-googlemap'), {
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				zoom: 18
			});
	
			var infowindow = new google.maps.InfoWindow();
	
			var marker, i;
			
			var markerBounds = new google.maps.LatLngBounds();
	
			for (i = 0; i < COORDINATES.length; i++) {
				var position = new google.maps.LatLng(COORDINATES[i].lat, COORDINATES[i].lng);
				
				marker = new google.maps.Marker({
					position: position,
					map: map,
					icon: "<?php echo VAPASSETS_URI . "css/images/red-marker.png"; ?>"
				});
				
				marker.setAnimation(google.maps.Animation.DROP);
				
				markerBounds.extend(position);
	
				google.maps.event.addListener(marker, 'click', (function(marker, i) {
					return function() {
						infowindow.setContent(COORDINATES[i].label);
						infowindow.open(map, marker);
					}
				})(marker, i));
			}
			
			if (COORDINATES.length > 1) {
				map.fitBounds(markerBounds);
			}

			map.setCenter(markerBounds.getCenter());
		}
		
	</script>
	
<?php } ?>
