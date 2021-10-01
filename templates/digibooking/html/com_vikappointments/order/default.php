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

	?>

    <div class="uk-text-zero wizardWrapper">
        <div class="uk-grid-collapse uk-height-1-1" data-uk-grid>
            <div class="uk-width-1-1 uk-width-auto@m">
                <div class="uk-background-secondary uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-padding uk-padding-remove-vertical">
                    <h1 class="font uk-text-white uk-h5 f500 uk-margin-remove uk-padding uk-padding-remove-horizontal"><?php echo JTEXT::_('PAGE_TITLE_'.$orders[0]['sname']); ?></h1>
                </div>
            </div>
            <div class="uk-width-expand">
                <div class="uk-background-muted uk-height-1-1">
                    <div class="uk-child-width-1-4 uk-grid-collapse uk-height-1-1" data-uk-grid>
                        <div class="stepWrapper">
                            <div class="step done uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                <div class="uk-position-relative uk-flex-1">
                                    <span class="uk-position-top-right uk-text-success uk-text-tiny font f500 steDone uk-visible@m"><?php echo JTEXT::_('STEP_DONE'); ?></span>
                                    <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                        <div class="uk-width-expand font uk-text-small uk-visible@m">
                                            <span class="stepLevel"><?php echo '۱. '; ?></span>
                                            <span class="stepText"><?php echo JTEXT::_('LOGIN_TO_SYSTEM'); ?></span>
                                        </div>
                                        <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <span class="uk-text-success">
                                            <img src="<?php echo JURI::base().'images/sprite.svg#check'; ?>" width="20" height="20" data-uk-svg>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="stepWrapper">
                            <div class="step done uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                <div class="uk-position-relative uk-flex-1">
                                    <span class="uk-position-top-right uk-text-success uk-text-tiny font f500 steDone uk-visible@m"><?php echo JTEXT::_('STEP_DONE'); ?></span>
                                    <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                        <div class="uk-width-expand font uk-text-small uk-visible@m">
                                            <span class="stepLevel"><?php echo '۲. '; ?></span>
                                            <span class="stepText"><?php echo JTEXT::_('SELECT_'.$orders[0]['sname']); ?></span>
                                        </div>
                                        <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <span class="uk-text-success">
                                            <img src="<?php echo JURI::base().'images/sprite.svg#check'; ?>" width="20" height="20" data-uk-svg>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="stepWrapper">
                            <div class="step done uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                <div class="uk-position-relative uk-flex-1">
                                    <span class="uk-position-top-right uk-text-success uk-text-tiny font f500 steDone uk-visible@m"><?php echo JTEXT::_('STEP_DONE'); ?></span>
                                    <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                        <div class="uk-width-expand font uk-text-small uk-visible@m">
                                            <span class="stepLevel"><?php echo '۳. '; ?></span>
                                            <span class="stepText"><?php echo JTEXT::_('COMPLETE_INFO'); ?></span>
                                        </div>
                                        <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <span class="uk-text-success">
                                            <img src="<?php echo JURI::base().'images/sprite.svg#check'; ?>" width="20" height="20" data-uk-svg>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="stepWrapper">
                            <div class="step current uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                <div class="uk-position-relative uk-flex-1">
                                    <span class="uk-position-top-right uk-text-success uk-text-tiny font f500 steDone uk-visible@m"><?php echo JTEXT::_('STEP_DONE'); ?></span>
                                    <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                        <div class="uk-width-expand font f600 uk-text-small uk-visible@m">
                                            <span class="stepLevel"><?php echo '۴. '; ?></span>
                                            <span class="stepText"><?php echo JTEXT::_('COMPLETE_RESERVE'); ?></span>
                                        </div>
                                        <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <span class="uk-text-success">
                                            <img src="<?php echo JURI::base().'images/sprite.svg#check'; ?>" width="16" height="16" data-uk-svg>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-width-auto uk-visible@m">
                <div class="uk-background-white uk-text-secondary uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-padding uk-padding-remove-vertical">
                    <img src="<?php echo JURI::base().'images/sprite.svg#'.$orders[0]['sname']; ?>" width="50" data-uk-svg>
                </div>
            </div>
        </div>
    </div>


	<div class="uk-padding">
        <div class="uk-margin-medium-bottom">
            <div class="uk-background-success uk-padding">
                <div class="uk-grid-small" data-uk-grid>
                    <div class="uk-width-1-1 uk-width-expand@m">
                        <h1 class="font uk-text-white uk-h5 f500 uk-margin-remove"><?php echo JText::_('VAPORDERTITLE1'); ?></h1>
                    </div>
                    <div class="uk-width-1-1 uk-width-auto@m">
                        <ul class="uk-flex-center uk-margin-remove uk-text-zero uk-child-width-auto uk-grid-medium" data-uk-grid>
                            <li>
                                <span class="font uk-margin-remove uk-h5 uk-text-gray fnum"><?php echo JText::sprintf('VAPORDERNUMBER', 100000+$order['id']); ?></span>
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
        <div class="summeryTable uk-margin-medium-bottom">
            <table class="uk-table uk-table-middle uk-table-divider uk-table-responsive uk-margin-remove">
                <thead>
                <tr>
                    <th></th>
                    <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('NAME'); ?></th>
                    <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('FLOOR'); ?></th>
                    <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('ENTRANCE_DATE'); ?></th>
                    <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('ENTRANCE_TIME'); ?></th>
                    <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('DURATION'); ?></th>
                    <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('DELETE'); ?></th>
                </tr>
                </thead>
                <tbody>

            <?php

            date_default_timezone_set('Asian/Tehran');
            $old_service = -1;


            $counter = 1;

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

            <?php } ?>


                <?php
                $db = JFactory::getDbo();
                $empDetailsQuery = $db->getQuery(true);
                $empDetailsQuery
                    ->select($db->quoteName(array('id', 'firstname', 'lastname', 'nickname')))
                    ->from($db->quoteName('#__vikappointments_employee'))
                    ->where($db->quoteName('id') . ' = ' . $ord['id_employee']);
                $empDetails = $db->setQuery($empDetailsQuery)->loadObject();
                ?>

                <?php
                $date = new JDate($ord['checkin_ts']);
                $date->toUnix();
                $now =  new JDate('now');
                ?>
                <tr>
                    <td class="uk-text-center uk-text-secondary uk-visible@m">
                        <img src="<?php echo JURI::base().'images/sprite.svg#'.$ord['sname']; ?>" width="36" height="36" data-uk-svg>
                    </td>
                    <td class="uk-text-center uk-text-secondary">
                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('NAME').'&ensp;:&ensp;'; ?></span>
                        <span class="uk-text-small fnum font"><?php echo $empDetails->firstname.' '.$empDetails->lastname; ?></span>
                    </td>
                    <td class="uk-text-center uk-text-secondary">
                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('FLOOR').'&ensp;:&ensp;'; ?></span>
                        <span class="uk-text-small fnum font"><?php echo JText::sprintf('FLOOR'.$ord['ename']); ?></span>
                    </td>
                    <td class="uk-text-center uk-text-secondary">
                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('ENTRANCE_DATE').'&ensp;:&ensp;'; ?></span>
                        <span class="uk-text-small fnum font"><?php echo ArasJoomlaVikApp::jdate($date_format, $ord['checkin_ts']); ?></span>
                    </td>
                    <td class="uk-text-center uk-text-secondary">
                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('ENTRANCE_TIME').'&ensp;:&ensp;'; ?></span>
                        <span class="uk-text-small fnum font uk-position-relative bullet uk-text-secondary green"><?php echo ArasJoomlaVikApp::jdate($date_format_time, $ord['checkin_ts']); ?></span>
                    </td>
                    <td class="uk-text-center uk-text-secondary">
                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('DURATION').'&ensp;:&ensp;'; ?></span>
                        <span class="uk-text-small fnum font uk-position-relative bullet uk-text-secondary red">
                            <?php
                            $d = floor ($ord['duration'] / 1440);
                            $h = floor (($ord['duration'] - $d * 1440) / 60);
                            $m = $ord['duration'] - ($d * 1440) - ($h * 60);

                            if (!empty($h))
                                echo $h.' '.JText::_('VAPSHORTCUTHOURS');
                            if (!empty($h) && !empty($m))
                                echo ' '.JText::_('AND').' ';
                            if (!empty($m))
                                echo $m.' '.JText::_('VAPSHORTCUTMINUTE');
                            echo ' ('.JText::sprintf('FINISH').' '.ArasJoomlaVikApp::jdate($date_format_time, VikAppointments::getCheckout($ord['checkin_ts'], $ord['duration'])).')';
                            ?>
                        </span>
                    </td>
                    <td class="uk-text-center uk-text-secondary">
                        <?php
                        // CANCELLATION
                        if ($still_confirmed_orders > 1)
                        {
                            if (VikAppointments::canUserCancelOrder($ord['checkin_ts']) && $ord['status'] == 'CONFIRMED')
                            {
                                $at_least_canc_order = true;
                                ?>

                                <button type="button" class="uk-button uk-button-danger uk-width-1-1 uk-button-outline uk-button-large font" onClick="vapCancelButtonPressed('<?php echo $cancel_uri; ?>');"><?php echo JText::_('REMOVE_FROM_LIST'); ?></button>

                                <?php
                            }
                            else
                            {
                                $ok_canc_orders = false;
                                echo '<span class="uk-text-secondary uk-text-small font">'.JTEXT::sprintf('THIS_RESERVE_FINISHED').'</span>';
                            }
                        }
                        else
                        {
                            $at_least_canc_order = true;
                            /* echo '<span class="uk-text-secondary uk-text-small font">'.JTEXT::sprintf('RESERVE_FINISHED').'</span>'; */
                        }

                        ?>

                        <?php if ($ok_canc_orders && $order['status'] == 'CONFIRMED') {
                            $canc_all_text = 'VAPORDERCANCALLBUTTON';
                            if (count($orders) == 1) {
                                $canc_all_text = 'VAPORDERCANCBUTTON';
                            }
                            if (count($orders) > 1 || VikAppointments::canUserCancelOrder($order['checkin_ts'])) { ?>
                                <button type="button" class="uk-button uk-button-large uk-button-outline uk-button-danger uk-width-1-1 font" onClick="vapCancelButtonPressed('<?php echo $global_cancel_uri; ?>');"><?php echo JText::_($canc_all_text); ?></button>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>




            <?php
            $old_service = $ord['id_service'];
            }

            $counter++;
            } ?>

                </tbody>
            </table>



			<!-- closure for the service title heading div -->
                    </div>

    <div>
        <div class="uk-child-width-1-1 uk-child-width-1-3@m uk-grid-small" data-uk-grid>
            <div class="uk-width-2-3 uk-visible@m">&emsp;</div>
            <?php if (!JFactory::getUser()->guest) { ?>
                <div>
                    <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders'); ?>" class="uk-button uk-button-large uk-button-primary uk-button-outline uk-width-1-1 font"><?php echo JText::_('VAPALLORDERSBUTTON'); ?></a>
                </div>
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
