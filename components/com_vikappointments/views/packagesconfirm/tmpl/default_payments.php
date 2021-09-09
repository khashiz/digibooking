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

$payCount = count($this->payments);

?>

<div class="vap-packconf-box">

	<h3><?php echo JText::_('VAPMETHODOFPAYMENT'); ?></h3>

	<div class="vap-payments-list">
		<?php foreach ($this->payments as $i => $p)
		{
			$p['name'] 		= VikAppointments::getTranslation($p['id'], $p, $this->langPayments, 'name', 'name');
			$p['prenote'] 	= VikAppointments::getTranslation($p['id'], $p, $this->langPayments, 'prenote', 'prenote');
			
			$cost_str = '';

			if ($p['charge'] != 0)
			{
				$cost_str = floatval($p['charge']);

				if ($cost_str > 0)
				{
					$cost_str = '+' . $cost_str;
				}
				else if ($cost_str == 0)
				{ 
					$cost_str = '';
				}

				$cost_str = VikAppointments::printPriceCurrencySymb($cost_str);
			}
			?>

			<div class="vap-payment-wrapper vap-payment-block">

				<div class="vap-payment-title">

					<?php if ($payCount > 1) { ?>

						<input
							type="radio"
							name="vappaymentradio"
							value="<?php echo $p['id']; ?>"
							id="vappayradio<?php echo $p['id']; ?>"
							onchange="vapPaymentRadioChanged(<?php echo $p['id']; ?>, <?php echo (strlen($p['prenote']) ? 0 : 1); ?>);"
						/>
					
					<?php } else { ?>

						<input type="hidden" name="vappaymentradio" value="<?php echo $p['id']; ?>" />

					<?php } ?>

					<label for="vappayradio<?php echo $p['id']; ?>" class="vap-payment-title-label">

						<?php if ($p['icontype'] == 1) { ?>

							<i class="fa fa-<?php echo $p['icon']; ?>"></i>&nbsp;

						<?php } else if( $p['icontype'] == 2 ) { ?>

							<img src="<?php echo JUri::root() . $p['icon']; ?>" />&nbsp;

						<?php } ?>

						<span><?php echo $p['name'] . (strlen($cost_str) ? ' (' . $cost_str . ')' : ''); ?></span>
					</label>
				</div>

				<?php if (strlen($p['prenote'])) { ?>
					<div class="vap-payment-description" id="vap-payment-description<?php echo $p['id']; ?>" style="<?php echo ($payCount > 1 ? 'display: none;' : ''); ?>">
						<?php
						/**
						 * Render HTML description to interpret attached plugins.
						 * 
						 * @since 1.6.3
						 */
						echo VikAppointments::renderHtmlDescription($p['prenote'], 'paymentconfirm');
						?>
					</div>
				<?php } ?>

			</div>
			
		<?php } ?>
	</div>

</div>

<script>

	var PAY_DESC_VISIBLE = <?php echo $payCount > 1 ? 0 : 1; ?>;

	function vapPaymentRadioChanged(id, close_effect) {
		jQuery(".vap-payment-title-label").removeClass('vaprequired');

		if (close_effect) {
			jQuery('.vap-payment-description').slideUp();
		} else {
			jQuery('.vap-payment-description').hide();
		}

		if (PAY_DESC_VISIBLE) {
			jQuery('#vap-payment-description'+id).show();
		} else {
			jQuery('#vap-payment-description'+id).slideDown();
		}

		PAY_DESC_VISIBLE = !close_effect;
	}

</script>
