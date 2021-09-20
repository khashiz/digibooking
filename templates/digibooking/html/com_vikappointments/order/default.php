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

$date_format = 'l ، j F Y';
$date_format_time = $config->get('timeformat');


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
<div class="vap-payment-position uk-hidden">
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


    function convertToHoursMins($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }
	
	?>


    <div>
        <div class="uk-background-success uk-padding-large uk-flex uk-flex-middle uk-flex-center">
            <div class="page-header uk-flex-1 uk-text-zero">
                <div class="uk-grid-small" data-uk-grid>
                    <div class="uk-width-expand uk-flex uk-flex-middle">
                        <div class="uk-flex-1">
                            <h1 class="font uk-text-white uk-h2 uk-text-center f500 uk-margin-remove"><?php echo JText::_('VAPORDERTITLE1'); ?></h1>
                            <ul class="uk-flex-center uk-margin-medium-top uk-text-zero uk-child-width-auto uk-grid-medium" data-uk-grid>
                                <li>
                                    <span class="font uk-margin-remove uk-h5 uk-text-gray fnum"><?php echo JText::sprintf('VAPORDERNUMBER', $order['id']); ?></span>
                                </li>
                                <li>
                                    <span class="font uk-margin-remove uk-h5 uk-text-gray"><?php echo JText::sprintf('VAPORDERKEY', $order['sid']); ?></span>
                                </li>
                                <li class="uk-hidden">
                                    <span class="font uk-margin-remove uk-h5 uk-text-gray <?php echo strtolower($order['status']); ?>"><?php echo JText::_('VAPORDERSTATUS').JText::_('VAPSTATUS' . $order['status']); ?></span>
                                </li>
                                <?php $_custf = (array) json_decode($order['custom_f'], true); ?>
                                <?php foreach ($_custf as $key => $val) { ?>
                                    <?php if (!empty($val)) { ?>
                                        <li>
                                            <span class="font uk-margin-remove uk-h5 uk-text-gray fnum"><?php echo JText::sprintf('RESERVE_FIELD', JText::_($key), $val); ?></span>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


	<div class="uk-padding-large">
        <div class="summeryTable uk-margin-medium-bottom">
            <div class="uk-padding-small">
                <div class="uk-padding-small">
                    <div class="uk-grid-small" data-uk-grid>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small">&ensp;</div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-expand"><?php echo JText::sprintf('NAME'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('FLOOR'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('ENTRANCE_DATE'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small"><?php echo JText::sprintf('ENTRANCE_TIME'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('DURATION'); ?></div>
                        <?php if ($still_confirmed_orders > 1) { ?>
                            <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('DELETE'); ?></div>
                        <?php } ?>
                    </div>
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
					<div>
                        <?php } ?>
					
				<div>
                    <?php
                    $db = JFactory::getDbo();
                    $empDetailsQuery = $db->getQuery(true);
                    $empDetailsQuery
                        ->select($db->quoteName(array('id', 'firstname', 'lastname', 'nickname')))
                        ->from($db->quoteName('#__vikappointments_employee'))
                        ->where($db->quoteName('id') . ' = ' . $ord['id_employee']);
                    $empDetails = $db->setQuery($empDetailsQuery)->loadObject();
                    ?>
                    <hr class="uk-margin-remove">
                    <div class="uk-padding-small">
                        <div>
                            <div>
                                <div class="uk-padding-small">
                                    <div class="uk-grid-small" data-uk-grid>
                                        <div class="uk-width-small uk-flex uk-flex-middle uk-flex-center">
                                            <div class="uk-width-1-2 uk-text-secondary">
                                                <img src="<?php echo JUri::base().'images/sprite.svg#'.$ord['sname']; ?>" data-uk-svg>
                                            </div>
                                        </div>
                                        <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-secondary uk-text-small fnum font uk-position-relative"><?php echo $empDetails->firstname.' '.$empDetails->lastname; ?></span>
                                        </div>
                                        <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-secondary uk-text-small font uk-position-relative"><?php echo JText::sprintf('FLOOR'.$ord['ename']); ?></span>
                                        </div>
                                        <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-secondary uk-text-small fnum font uk-position-relative"><?php echo ArasJoomlaVikApp::jdate($date_format, $ord['checkin_ts']); ?></span>
                                        </div>
                                        <div class="uk-width-small uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-secondary uk-text-small fnum font uk-position-relative bullet green"><?php echo ArasJoomlaVikApp::jdate($date_format_time, $ord['checkin_ts']); ?></span>
                                        </div>
                                        <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-secondary uk-text-small fnum font uk-position-relative bullet red vapcartitemboxoptionsdur"><?php echo floor($ord['duration'] / 60) ? floor($ord['duration'] / 60).' '.JText::_('VAPSHORTCUTHOURS') : floor($ord['duration'] % 60).' '.JText::_('VAPSHORTCUTMINUTE'); echo ' ('.JText::sprintf('FINISH').' '.ArasJoomlaVikApp::jdate($date_format_time, VikAppointments::getCheckout($ord['checkin_ts'], $ord['duration'])).')'; ?></span>
                                        </div>
                                        <?php
                                        // CANCELLATION
                                        if ($still_confirmed_orders > 1)
                                        {
                                            if (VikAppointments::canUserCancelOrder($ord['checkin_ts']) && $ord['status'] == 'CONFIRMED')
                                            {
                                                $at_least_canc_order = true;
                                                ?>
                                                <div class="uk-width-1-6">
                                                    <button type="button" class="uk-button uk-button-danger uk-width-1-1 uk-button-outline uk-button-large font" onClick="vapCancelButtonPressed('<?php echo $cancel_uri; ?>');"><?php echo JText::_('REMOVE_FROM_LIST'); ?></button>
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
                                </div>
                            </div>
                        </div>
                    </div>

				</div>



				<?php
					$old_service = $ord['id_service'];
				}
			} ?>



			<!-- closure for the service title heading div -->
                    </div>
    </div>

    <div>
        <div class="uk-child-width-1-1 uk-child-width-1-3@m" data-uk-grid>
            <?php if (!JFactory::getUser()->guest) { ?>
                <div>
                    <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders'); ?>" class="uk-button uk-button-large uk-button-primary uk-button-outline uk-width-1-1 font"><?php echo JText::_('VAPALLORDERSBUTTON'); ?></a>
                </div>
            <?php } ?>
            <div>&emsp;</div>
            <?php if ($ok_canc_orders && $order['status'] == 'CONFIRMED') {
                $canc_all_text = 'VAPORDERCANCALLBUTTON';
                if (count($orders) == 1) {
                    $canc_all_text = 'VAPORDERCANCBUTTON';
                }
                if (count($orders) > 1 || VikAppointments::canUserCancelOrder($order['checkin_ts'])) { ?>
                    <div>
                        <button type="button" class="uk-button uk-button-large uk-button-danger uk-width-1-1 font" onClick="vapCancelButtonPressed('<?php echo $global_cancel_uri; ?>');"><?php echo JText::_($canc_all_text); ?></button>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

	</div>

	<input type="hidden" name="ordnum" value="<?php echo $order['id']; ?>" />
	<input type="hidden" name="ordkey" value="<?php echo $order['sid']; ?>" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="view" value="order" />
</form>

<div class="vap-payment-position uk-hidden">
	<div id="vap-payment-position-bottom-left" style="text-align: left;"></div>
	<div id="vap-payment-position-bottom-center" style="text-align: center;"></div>
	<div id="vap-payment-position-bottom-right" style="text-align: right;"></div>
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
                UIkit.modal.confirm("<?php echo JText::_('VAPCANCELORDERMESSAGE'); ?>", { labels: { ok: "<?php echo JText::_('VAPCANCELORDEROK'); ?>", cancel: "<?php echo JText::_('VAPCANCELORDERCANC'); ?>" } }).then(function () {
                    vapCancelOrder(uri);
                    console.log('Confirmed.')
                }, function () {
                    console.log('Rejected.')
                });
			}
		</script>
	<?php }
	
} ?>

<?php if (count($orders) && count($this->ordersLocations)) { ?>
	<div id="vap-loc-googlemap"></div>
	
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
