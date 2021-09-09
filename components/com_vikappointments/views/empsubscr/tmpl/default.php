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

$auth       = $this->auth;
$employee   = $auth->getEmployee();

$subscriptions 	= $this->subscriptions;
$payments 		= $this->payments;
$billings 		= $this->billings;

$vik = UIApplication::getInstance();

$js_subscr_arr 	= array();
$js_pay_arr 	= array();


$countries_select = '<select name="billing[country]" id="vap-billing-country">';
$countries_select .= '<option></option>';
foreach ($this->countries as $country)
{
	$countries_select .= '<option value="'.$country['country_2_code'].'" '.($billings['country'] == $country['country_2_code'] ? 'selected="selected"' : '').'>'.$country['country_name'].'</option>';
}
$countries_select .= '</select>';

$first_charge = 0;
if (count($payments))
{
	$first_charge = $payments[0]['charge'];
}

$itemid = JFactory::getApplication()->input->getInt('Itemid');

$config = UIFactory::getConfig();

?>

<?php
/**
 * The employees area toolbar is displayed from the layout below:
 * /components/com_vikappointments/layouts/emparea/toolbar.php
 * 
 * If you need to change something from this layout, just create
 * an override of this layout by following the instructions below:
 * - open the back-end of your Joomla
 * - visit the Extensions > Templates > Templates page
 * - edit the active template
 * - access the "Create Overrides" tab
 * - select Layouts > com_vikappointments > emparea
 * - start editing the toolbar.php file on your template to create your own layout
 *
 * @since 1.6
 */
echo JLayoutHelper::render('emparea.toolbar');
?>

<?php if ($this->trial !== false && $employee['active_to'] == 0) { ?>

	<div class="vap-empsubscr-trial">
		<h2><?php echo JText::_('VAPSUBSCRTRIALTITLE'); ?></h2>
		<div class="vap-trial-box">
			<?php echo JText::sprintf('VAPSUBSCRTRIALMESSAGE', "\"".$this->trial['name']."\""); ?>
		</div>
		<div class="vap-trial-button">
			<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empsubscrorder.activateTrial&Itemid=' . $itemid); ?>">
				<?php echo JText::_('VAPSUBSCRTRIALBUTTON'); ?>
			</a>
		</div>
	</div>
<?php } ?>

<?php if ($subscriptions) { ?>
	
	<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empsubscrorder.save'); ?>" method="post">
		<div class="vap-empsubscr-purchase">
			<h2><?php echo JText::_('VAPSUBSCRPURCHASETITLE'); ?></h2>

			<div class="vap-purchase-shop">
				<div class="vap-purchase-list">
					<div class="vap-purchase-subscr">
						<div class="vap-purchase-subscr-text">
							<h3><?php echo JText::_('VAPSUBSCRPURCHASEMESSAGE'); ?></h3>
						</div>
						<div class="vap-purchase-subscr-fields">
							<?php 
							$checked = false;
							foreach ($subscriptions as $sub)
							{
								$js_subscr_arr[$sub['id']] = array(
									'price' => $sub['price'],
									'name' 	=> $sub['name'],
								);
								?>
								
								<label for="vapsubscropt<?php echo $sub['id']; ?>" id="vapsubscrlbl<?php echo $sub['id']; ?>" class="vap-subscr-label-plan <?php echo (!$checked ? 'picked' : ''); ?>">
									<div class="vap-subscr-option-plan" id="vapsubscrdiv<?php echo $sub['id']; ?>">
										<span class="vap-subscr-option-radio">
											<input type="radio" name="id_subscr" value="<?php echo $sub['id']; ?>" id="vapsubscropt<?php echo $sub['id']; ?>" class="vap-subscr-option" <?php echo ($checked ? '' : 'checked="checked"'); ?>/>
										</span>
										<span class="vap-subscr-option-name">
											<?php echo $sub['name']; ?>
										</span>
										<span class="vap-subscr-option-price">
											<?php echo VikAppointments::printPriceCurrencySymb($sub['price']); ?>
										</span>
									</div>
								</label>
								
							<?php 
								$checked = true;
							} 
							?>
						</div>
					</div>
					
					<div class="vap-purchase-billingwrapper">
						<div class="vap-purchase-payment">
							<div class="vap-purchase-payment-text">
								<h3><?php echo JText::_('VAPSUBSCRPAYMENTMESSAGE'); ?></h3>
							</div>
							<div class="vap-purchase-payments-fields">
								
								<!-- BILLING COUNTRY -->
								<div class="vap-purchase-payments-field vap-purchasefield-country">
									<div class="vap-purchase-payments-label">
										<label for="vap-billing-country"><?php echo JText::_('VAPUSERPROFILEFIELD4'); ?></label>
									</div>
									<div class="vap-purchase-payments-value">
										<?php echo $countries_select; ?>
									</div>
								</div>
								
								<!-- BILLING STATE/PROVINCE -->
								<div class="vap-purchase-payments-field">
									<div class="vap-purchase-payments-label">
										<label for="vap-billing-state"><?php echo JText::_('VAPUSERPROFILEFIELD5'); ?></label>
									</div>
									<div class="vap-purchase-payments-value">
										<input type="text" name="billing[state]" value="<?php echo $this->escape($billings['state']); ?>" id="vap-billing-state" />
									</div>
								</div>
								
								<!-- BILLING CITY -->
								<div class="vap-purchase-payments-field">
									<div class="vap-purchase-payments-label">
										<label for="vap-billing-city"><?php echo JText::_('VAPUSERPROFILEFIELD6'); ?></label>
									</div>
									<div class="vap-purchase-payments-value">
										<input type="text" name="billing[city]" value="<?php echo $this->escape($billings['city']); ?>" id="vap-billing-city" />
									</div>
								</div>
								
								<!-- BILLING ADDRESS -->
								<div class="vap-purchase-payments-field">
									<div class="vap-purchase-payments-label">
										<label for="vap-billing-address"><?php echo JText::_('VAPUSERPROFILEFIELD7'); ?></label>
									</div>
									<div class="vap-purchase-payments-value">
										<input type="text" name="billing[address]" value="<?php echo $this->escape($billings['address']); ?>" id="vap-billing-address" />
									</div>
								</div>
								
								<!-- BILLING ZIP CODE -->
								<div class="vap-purchase-payments-field">
									<div class="vap-purchase-payments-label">
										<label for="vap-billing-zip"><?php echo JText::_('VAPUSERPROFILEFIELD9'); ?></label>
									</div>
									<div class="vap-purchase-payments-value">
										<input type="text" name="billing[zip]" value="<?php echo $this->escape($billings['zip']); ?>" id="vap-billing-zip" />
									</div>
								</div>
								
								<!-- BILLING COMPANY -->
								<div class="vap-purchase-payments-field">
									<div class="vap-purchase-payments-label">
										<label for="vap-billing-company"><?php echo JText::_('VAPUSERPROFILEFIELD10'); ?></label>
									</div>
									<div class="vap-purchase-payments-value">
										<input type="text" name="billing[company]" value="<?php echo $this->escape($billings['company']); ?>" id="vap-billing-company" />
									</div>
								</div>
								
								<!-- BILLING VAT -->
								<div class="vap-purchase-payments-field">
									<div class="vap-purchase-payments-label">
										<label for="vap-billing-vat"><?php echo JText::_('VAPUSERPROFILEFIELD11'); ?></label>
									</div>
									<div class="vap-purchase-payments-value">
										<input type="text" name="billing[vat]" value="<?php echo $this->escape($billings['vat']); ?>" id="vap-billing-vat" />
									</div>
								</div>

								<?php
								/**
								 * If GDPR is enabled, display a checkbox to force the
								 * customers to accept the privacy policy.
								 *
								 * @since 1.6
								 */
								if ($config->getBool('gdpr'))
								{
									$policy = $config->get('policylink', '');
									?>

									<!-- PRIVACY POLICY -->
									<div class="vap-empsubscr-gdpr">
										
										<input type="checkbox" class="required" id="gdpr-register" value="1" />
										<label for="gdpr-register" style="display: inline-block;">
											<?php
											if ($policy)
											{
												// label with link to read the privacy policy
												echo JText::sprintf(
													'GDPR_POLICY_AUTH_LINK',
													'javascript: void(0);',
													'vapOpenPopup(\'' . $policy . '\');'
												);
											}
											else
											{
												// label without link
												echo JText::_('GDPR_POLICY_AUTH_NO_LINK');
											}
											?>
										</label>
										
									</div>

									<?php
								}
								?>
								
								<!-- METHOD OF PAYMENT -->
								<div class="vap-purchase-payments-field vap-purchasefield-payments">
									<div class="vap-purchase-payments-label">
										<label for="vap-billing-zip"><?php echo JText::_('VAPMETHODOFPAYMENT'); ?></label>
									</div>
									<div class="vap-purchase-payments-value">
										<?php 
										$checked = false;
										foreach ($payments as $pay)
										{
											$js_pay_arr[$pay['id']] = array(
												'price' => $pay['charge'],
												'name' 	=> $pay['name'],
											);

											$charge_str = '';
											if ($pay['charge'] != 0)
											{
												$charge_str = VikAppointments::printPriceCurrencySymb($pay['charge']);
												if ($pay['charge'] > 0)
												{
													$charge_str = '+ ' . $charge_str;
												}
											}
											?>
											<span class="vap-payment-method">
												<input type="radio" name="id_payment" id="vappayradio<?php echo $pay['id']; ?>" value="<?php echo $pay['id']; ?>" <?php echo (!$checked ? 'checked="checked"' : ''); ?> class="vap-payment-option"/>
												<label for="vappayradio<?php echo $pay['id']; ?>"><?php echo $pay['name'] . (( strlen($charge_str) > 0 ) ? ' ('.$charge_str.')' : ''); ?></label>
											</span>
										<?php 
											$checked = true;
										}
										?>
									</div>
								</div>
								
							</div>
						</div>
						
						<div class="vap-purchase-cart">
							<h3><?php echo JText::_('VAPSUBSCRCARTHEAD'); ?></h3>
							
							<div class="vap-purchase-summary">
								<span class="vap-purchase-summary-item" id="vap-subscr-item"><?php echo $subscriptions[0]['name']; ?></span>
								<span class="vap-purchase-summary-price" id="vap-subscr-price"><?php echo VikAppointments::printPriceCurrencySymb($subscriptions[0]['price']); ?></span>
							</div>
							
							<div class="vap-purchase-summary">
								<span class="vap-purchase-summary-item" id="vap-charge-item"><?php echo JText::_('VAPSUBSCRPAYCHARGE'); ?></span>
								<span class="vap-purchase-summary-price" id="vap-charge-price"><?php echo VikAppointments::printPriceCurrencySymb($first_charge); ?></span>
							</div>
							
							<div class="vap-purchase-subscr-total">
								<?php echo VikAppointments::printPriceCurrencySymb($subscriptions[0]['price'] + $first_charge); ?>
							</div>
							
							<div class="vap-purchase-button">
								<button type="submit" onclick="return vapValidateFieldsBeforeSubmit();">
									<?php echo JText::_('VAPSUBSCRPURCHASEBUTTON'); ?>
								</button>
							</div>
						</div>
					
					</div>
					
				</div>
				
			</div>
		</div>
		
		<input type="hidden" name="option" value="com_vikappointments" />
		<input type="hidden" name="task" value="empsubscrorder.save" />
		<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
	</form>
	
	<script>
	
	   var CART_TOTAL_COST = <?php echo ($subscriptions[0]['price'] + $first_charge); ?>;
	   
	   var SUBSCRIPTIONS_MAP = <?php echo json_encode($js_subscr_arr); ?>;
	   var PAYMENTS_MAP = <?php echo json_encode($js_pay_arr); ?>;
	   
	   jQuery(document).ready(function() {

			jQuery('.vap-subscr-option').on('change', function() {
				var sub = vapGetSubscriptionPlan();
				var pay = vapGetSelectedPayment();
				
				jQuery('.vap-subscr-label-plan').removeClass('picked');
				jQuery('#vapsubscrlbl'+sub).addClass('picked');
				
				vapUpdateTotalCost(sub, pay);
			});

			jQuery('.vap-payment-option').on('change', function() {
				var sub = vapGetSubscriptionPlan();
				var pay = vapGetSelectedPayment();
				vapUpdateTotalCost(sub, pay);
			});
			
			jQuery('#vap-billing-country').select2({
				placeholder: '--',
				allowClear: true,
				width: '100%'
			});
		});
		
		function vapGetSubscriptionPlan() {
			return jQuery('.vap-subscr-option:checked').val();
		}
		
		function vapGetSelectedPayment() {
			return jQuery('.vap-payment-option:checked').val();
		}
		
		function vapUpdateTotalCost(sub, pay) {
			CART_TOTAL_COST = 0;
			
			if (!jQuery.isEmptyObject(SUBSCRIPTIONS_MAP[sub])) {
				CART_TOTAL_COST += parseFloat(SUBSCRIPTIONS_MAP[sub]['price']);
				jQuery('#vap-subscr-item').html(SUBSCRIPTIONS_MAP[sub]['name']);
				jQuery('#vap-subscr-price').html(Currency.getInstance().format(CART_TOTAL_COST));
			}
			
			if (!jQuery.isEmptyObject(PAYMENTS_MAP[pay])) {
				var charge = parseFloat(PAYMENTS_MAP[pay]['price']);
				CART_TOTAL_COST += charge;
				jQuery('#vap-charge-price').html(Currency.getInstance().format(charge));
			}
			
			jQuery('.vap-purchase-subscr-total').html(Currency.getInstance().format(CART_TOTAL_COST));
		}

		function vapValidateFieldsBeforeSubmit() {
			var ok = true;

			<?php if ($config->get('gdpr')) { ?>

				if (jQuery('#gdpr-register').is(':checked')) {
					jQuery('#gdpr-register').next().removeClass('vapinvalid');
				} else {
					jQuery('#gdpr-register').next().addClass('vapinvalid');
					ok = false;
				}

			<?php } ?>

			return ok;
		}
		
	</script>
	
<?php } ?>
