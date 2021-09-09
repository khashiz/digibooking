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

$config = UIFactory::getConfig();

$items_list = VikAppointmentsCartUtils::sortItemsByServiceDate($this->cart->getItemsList());

$last_service_id = -1;
$format_duration = VikAppointments::isDurationToFormat();
$cart_expanded   = VikAppointments::isCartAutoExpanded();

?>

<div class="vapsummarycont">
	<div class="vapsummaryoptionsheadtitle"><?php echo JText::_('VAPORDERSUMMARYHEADTITLE'); ?></div>
	
	<div class="vapsummaryservicescont">
	
		<?php 
		$default_tz = date_default_timezone_get();

		foreach ($items_list as $k => $item)
		{ 
			$emp_tz = VikAppointments::getEmployeeTimezone($item->getID2());
			VikAppointments::setCurrentTimezone($emp_tz ? $emp_tz : $default_tz);
			?>

			<?php
			if ($item->getID() != $last_service_id)
			{
				if ($last_service_id != -1)
				{
					?>
						</div>
					</div>
				<?php
				}
				?>
		
				<div class="vapcartitemdiv" id="vapcartitemdiv<?php echo $item->getID(); ?>">
			
					<div class="vapcartitemleft">
						<a href="javascript: void(0);"
							onClick="vapCartExtendItem(<?php echo $item->getID(); ?>);"
							class="vapcartexplink <?php echo ($cart_expanded ? 'vapcartexpopened' : 'vapcartexphidden'); ?>"
							id="vapcartexplink<?php echo $item->getID(); ?>"
						>
							<span class="vapcartitemname">
								<i class="fa fa-chevron-<?php echo $cart_expanded ? 'down' : 'right'; ?>"></i>
								<?php echo $item->getName(); ?>
							</span>
						</a>
					</div>

					<div class="vapcartitemright">
						<div class="vapcartitemprice" id="vapcartgroupitemprice<?php echo $item->getID(); ?>">
							<?php 
							$group_tcost = VikAppointmentsCartUtils::getServiceTotalCost($items_list, $item->getID());
							
							if ($group_tcost > 0)
							{
								echo VikAppointments::printPriceCurrencySymb($group_tcost);
							}
							?>
						</div>
					</div>
				
					<div class="vapcartinneritemscont" id="vapcartinneritemscont<?php echo $item->getID(); ?>" style="<?php echo ($cart_expanded ? '' : 'display: none;'); ?>">
		
			<?php } ?>
		
			<div class="vapcartinneritemdiv" id="vapcartinneritemdiv<?php echo $k; ?>">
			
				<div class="vapcartinitemup">
					<div class="vapcartinitemupleft">
						<div class="vapcartitemexp">
							<a href="javascript: void(0);"
								onClick="vapCartOpenDetails(<?php echo $k; ?>);"
								class="vapcartitemdetlink"
							>
								<span>
									<i class="fa fa-bars"></i>
									<?php echo $item->getCheckinDate($config->get('dateformat') . ' ' . $config->get('timeformat')); ?>
								</span>
							</a>
						
						<!-- START MODAL BOX - Summary - Options list -->
						<div class="vapcartitemboxdialog" id="vapcartitemboxdialog<?php echo $k; ?>" style="<?php echo ($cart_expanded ? '' : 'display: none;'); ?>">
							
							<div class="vapcartitemboxdetails"><?php echo $item->getDetails(); ?></div>
							
							<div class="vapcartitemboxoptionscont">
								<?php
								$opt_tcost = $item->getPrice();
								foreach ($item->getOptionsList() as $option)
								{
									?>
									<div class="vapcartitemboxoptiondiv" id="vapcartoption<?php echo $k.'-'.$option->getID(); ?>">
										<div class="vapcartitemboxoptionleft">
											<?php echo $option->getName(); ?>
										</div>

										<div class="vapcartitemboxoptioncenter">
											<span class="vapcartitemboxoptionprice">
												<?php
												if ($option->getPrice() != 0)
												{
													 echo VikAppointments::printPriceCurrencySymb($option->getPrice());
												}
												?>
											</span>
											<span class="vapcartitemboxoptionquant" id="vapcartitemboxoptionquant<?php echo $k . '-' . $option->getID(); ?>"><?php echo JText::_('VAPCARTQUANTITYSUFFIX') . $option->getQuantity(); ?></span>
										</div>

										<div class="vapcartitemboxoptionright">
											<?php if ($option->getMaxQuantity() > 1) { ?>
												<a
													href="javascript: void(0);"
													onClick="vapAddCartOption(<?php echo $k . "," . $option->getID() . "," . $item->getID() . "," . $item->getID2() . "," . $item->getCheckinTimeStamp(); ?>);"
													class="vapcartaddbtn"
												>
													<i class="fa fa-plus-circle"></i>
												</a>
											<?php } ?>

											<?php if ($option->getMaxQuantity() > 1 || !$option->isRequired()) { ?>
												<a
													href="javascript: void(0);"
													onClick="vapRemoveCartOption(<?php echo $k . "," . $option->getID() . "," . $item->getID() . "," . $item->getID2() . "," . $item->getCheckinTimeStamp(); ?>);"
													class="vapcartremovebtn"
												>
													<i class="fa fa-minus-circle"></i>
												</a>
											<?php } ?>
										</div>
									</div>
									<?php $opt_tcost += $option->getPrice() * $option->getQuantity(); ?>

								<?php } ?>
							</div>

							<div class="vapcartitemboxoptionsbottom">
								<span class="vapcartitemboxoptionsdur">
									<?php
									echo VikAppointments::formatMinutesToTime($item->getDuration());

									/**
									 * Display checkout time.
									 *
									 * @since 1.6
									 */
									$checkout = strtotime('+' . $item->getDuration() . ' minutes', $item->getCheckinTimeStamp());
									echo ' (' . JText::sprintf('VAPCHECKOUTAT', date($config->get('timeformat'), $checkout)) . ')';
									?>
								</span>

								<?php if ($item->getPeople() > 1) { ?>
									<span class="vapcartitemboxoptionspeople">
										<?php echo $item->getPeople(); ?>
										<i class="fa fa-users"></i>
									</span>
								<?php } ?>

								<span class="vapcartitemboxoptionstcost">
									<?php
									if ($item->getPrice() > 0)
									{
										echo VikAppointments::printPriceCurrencySymb($item->getPrice());
									}
									?>
								</span>
							</div>
						</div>
						<!-- END MODAL BOX -->
					</div>
				</div>

				<div class="vapcartinitemupright">
					<div class="vapcartitemprice"  id="vapcartitemtcost<?php echo $k; ?>">
						<?php
						if ($opt_tcost > 0)
						{
							echo VikAppointments::printPriceCurrencySymb($opt_tcost);
						}
						else
						{
							echo '&nbsp;'; 
						}
						?>
					</div>

					<div class="vapcartitemright">
						<a 
							href="javascript: void(0);"
							onClick="vapRemoveService(<?php echo $k . "," . $item->getID() . "," . $item->getID2() . "," . $item->getCheckinTimeStamp(); ?>);"
							class="vapcartremovebtn"
						>
							<i class="fa fa-minus-circle"></i>
						</a>
					</div>
				</div>
			</div>
			
		</div>
		
		<?php $last_service_id = $item->getID(); ?>
		
		<?php if ($k == count($items_list) - 1)
		{
			?></div></div><?php
		}
	} ?>
	
	</div>
	
	<?php
	$discount 	= 0;
	$creditUsed = $this->creditUsed;
	$tot_to_pay = $this->totalToPay;

	if ($tot_to_pay <= 0)
	{
		$tot_to_pay_price 	= JText::_('VAPFREE');
		$discount 			= $this->cart->getTotalCost() * -1;
	}
	else
	{
		$discount 			= $tot_to_pay - $this->cart->getTotalCost();
		$tot_to_pay_price 	= VikAppointments::printPriceCurrencySymb($tot_to_pay);
	}
	?>

	<div class="vap-cart-summary-gtotal">
	
		<?php if ($discount) { ?>
			<div class="vapsummarycoupondiv">
				<div class="vapsummarycouponrightdiv">
					<span class="vapsummarycoupontitle">
						<?php 
						if ($creditUsed > 0)
						{
							if ($creditUsed < $this->user['credit'])
							{
								$disc_title = JText::sprintf(
									'VAPUSERCREDITUSED',
									VikAppointments::printPriceCurrencySymb($this->user['credit']),
									VikAppointments::printPriceCurrencySymb($creditUsed)
								);
							}
							else
							{
								$disc_title = JText::sprintf(
									'VAPUSERCREDITFINISHED',
									VikAppointments::printPriceCurrencySymb($this->user['credit'])
								);
							}

							echo JText::_('VAPSUMMARYDISCOUNT');
							?>
							<i class="fa fa-info-circle discount-tooltip" title="<?php echo $disc_title; ?>"></i>
							<?php
						}
						else
						{
							echo JText::_('VAPSUMMARYCOUPON');
						}
						?>
					</span>
					<span class="vapsummarycouponvalue" id="vapsummarycolcoupon"><?php echo VikAppointments::printPriceCurrencySymb($discount); ?></span>
				</div>
			</div>
		<?php } ?>
		
		<div class="vapsummarytotaldiv" style="<?php echo ($this->cart->getTotalCost() == 0 ? 'display:none;' : ''); ?>">
			<span class="vapsummarytottitle"><?php echo JText::_('VAPSUMMARYTOTAL'); ?></span>
			<span class="vapsummarytotprice" id="vapsummarycoltotalcost"><?php echo $tot_to_pay_price; ?></span>
		</div>

	</div>
	
</div>

<?php
JText::script('VAPFREE');
JText::script('VAPCARTQUANTITYSUFFIX');
?>

<script>

	var vap_t_price = <?php echo $this->cart->getTotalCost(); ?>;

	jQuery(document).ready(function() {

		jQuery('.discount-tooltip').tooltip();

	});

	function vapRemoveService(id_html, id, id2, ts) {
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=remove_item_cart_rq&tmpl=component' . ($this->itemid ? '&Itemid=' . $this->itemid : false), false); ?>",
			data: {
				id: id,
				id2: id2,
				ts: ts
			}
		}).done(function(resp){
			
			obj = jQuery.parseJSON(resp);
			
			if (obj[0]) {
				if (jQuery('#vapcartinneritemscont'+id).children().length > 1) {
					jQuery('#vapcartinneritemdiv'+id_html).remove();
					vapUpdateCartServicePrice(id, obj[3]);
					if (typeof VIKAPPOINTMENTS_CART_INSTANCE !== "undefined") {
						jQuery('#vapmodcartinneritemdiv'+id_html).remove();
						vapModUpdateCartServicePrice(id, obj[3]);
					}
				} else {
					jQuery('#vapcartitemdiv'+id).remove();
					if (typeof VIKAPPOINTMENTS_CART_INSTANCE !== "undefined") {
						jQuery('#vapmodcartitemdiv'+id).remove();
					}
				}
				
				vapUpdateCartPrice(obj[4]);
				vapUpdateCouponValue(obj[4] - obj[1], obj[5]);

				if (typeof VIKAPPOINTMENTS_CART_INSTANCE !== "undefined") {
					vapModUpdateCartPrice(obj[1]);
				}
				
				if (obj[2]) {
					document.location.href = obj.pop();
				}
			} else {
				alert(obj[1]);
			}
			
		});
	}

	function vapCartExtendItem(id) {
		var obj = jQuery('#vapcartinneritemscont'+id);
		if (!obj.is(':visible')) {
			jQuery('#vapcartinneritemscont'+id).slideDown('fast');
			jQuery('#vapcartexplink'+id).addClass('vapcartexpopened');
			jQuery('#vapcartexplink'+id).removeClass('vapcartexphidden');

			jQuery('#vapcartexplink'+id).find('i.fa').removeClass('fa-chevron-right').addClass('fa-chevron-down');
		} else {
			jQuery('#vapcartinneritemscont'+id).slideUp('fast');
			jQuery('#vapcartexplink'+id).removeClass('vapcartexpopened');
			jQuery('#vapcartexplink'+id).addClass('vapcartexphidden');

			jQuery('#vapcartexplink'+id).find('i.fa').removeClass('fa-chevron-down').addClass('fa-chevron-right');
		}
	}

	function vapCartOpenDetails(id) {
		if (jQuery('#vapcartitemboxdialog'+id).is(':visible')) {
			jQuery('#vapcartitemboxdialog'+id).slideUp();
		} else {
			jQuery('#vapcartitemboxdialog'+id).slideDown();
		}
	}

	function vapRemoveCartOption(id_html, id_opt, id, id2, ts) {
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=remove_option_cart_rq&tmpl=component' . ($this->itemid ? '&Itemid=' . $this->itemid : false), false); ?>",
			data: { id_opt: id_opt, id: id, id2: id2, ts: ts }
		}).done(function(resp){
			
			obj = jQuery.parseJSON(resp);
			
			if( obj[0] ) {
				
				if( obj[3] > 0 ) {
					jQuery('#vapcartitemboxoptionquant'+id_html+'-'+id_opt).html(Joomla.JText._('VAPCARTQUANTITYSUFFIX') + obj[3]);
					if( typeof VIKAPPOINTMENTS_CART_INSTANCE !== "undefined" ) {
						jQuery('#vapmodcartitemboxoptionquant'+id_html+'-'+id_opt).html(Joomla.JText._('VAPCARTQUANTITYSUFFIX') + obj[3]);
					}
				} else {
					jQuery('#vapcartoption'+id_html+'-'+id_opt).remove();
					if( typeof VIKAPPOINTMENTS_CART_INSTANCE !== "undefined" ) {
						jQuery('#vapmodcartoption'+id_html+'-'+id_opt).remove();
					}
				}
				
				vapUpdateCartPrice(obj[5]);
				vapUpdateCartItemPrice(id_html, obj[2]);
				vapUpdateCartServicePrice(id, obj[4]);

				vapUpdateCouponValue(obj[5] - obj[1], obj[6]);

				if (typeof VIKAPPOINTMENTS_CART_INSTANCE !== "undefined") {
					vapModUpdateCartPrice(obj[1]);
					vapModUpdateCartItemPrice(id_html, obj[2]);
					vapModUpdateCartServicePrice(id, obj[4]);
				}
				
			} else {
				alert(obj[1])
			}
			
		});
		
	}

	function vapAddCartOption(id_html, id_opt, id, id2, ts) {
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=add_option_cart_rq&tmpl=component' . ($this->itemid ? '&Itemid=' . $this->itemid : false), false); ?>",
			data: { id_opt: id_opt, id: id, id2: id2, ts: ts }
		}).done(function(resp){
			
			obj = jQuery.parseJSON(resp);
			
			if( obj[0] ) {
				
				jQuery('#vapcartitemboxoptionquant'+id_html+'-'+id_opt).html(Joomla.JText._('VAPCARTQUANTITYSUFFIX') + obj[3]); 
				
				vapUpdateCartPrice(obj[5]);
				vapUpdateCartItemPrice(id_html, obj[2]);
				vapUpdateCartServicePrice(id, obj[4]);

				vapUpdateCouponValue(obj[5] - obj[1], obj[6]);

				if (typeof VIKAPPOINTMENTS_CART_INSTANCE !== "undefined") {
					jQuery('#vapmodcartitemboxoptionquant'+id_html+'-'+id_opt).html(Joomla.JText._('VAPCARTQUANTITYSUFFIX') + obj[3]);
					
					vapModUpdateCartPrice(obj[1]);
					vapModUpdateCartItemPrice(id_html, obj[2]);
					vapModUpdateCartServicePrice(id, obj[4]);
				}
				
			} else {
				alert(obj[1])
			}
			
		});
	}

	function vapUpdateCartPrice(price) {
		if (price > 0) {
			_html = Currency.getInstance().format(price);
		} else {
			_html = Joomla.JText._('VAPFREE');
		}

		jQuery('#vapsummarycoltotalcost').html(_html);
	}

	function vapUpdateCartItemPrice(item_id, price) {
		jQuery('#vapcartitemtcost'+item_id).html(Currency.getInstance().format(price));
	}

	function vapUpdateCartServicePrice(service_id, price) {
		var _html = '';

		if (price > 0) {
			_html = Currency.getInstance().format(price);
		} else {
			// _html = Joomla.JText::_('VAPFREE');
			_html = '';
		}

		jQuery('#vapcartgroupitemprice'+service_id).html(_html);
	}

	function vapUpdateCouponValue(value, title) {
		if (value < 0) {
			jQuery('.vapsummarycoupondiv').show();
		} else {
			jQuery('.vapsummarycoupondiv').hide();
		}

		jQuery('#vapsummarycolcoupon').html(Currency.getInstance().format(value));

		if (title) {
			jQuery('.discount-tooltip').attr('title', title);
		}
	}

</script>
