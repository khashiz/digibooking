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

$params = $this->params;

$vik = UIApplication::getInstance();

?>

<!-- Currency Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE6'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- CURRENCY SYMBOL - Text -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG7"); ?></b> </td>
			<td><input type="text" name="currencysymb" value="<?php echo $params['currencysymb']?>" size="10" onchange="formatSamplePrice();"></td>
		</tr>
		
		<!-- CURRENCY NAME - Text -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG8"); ?></b> </td>
			<td>
				<input type="text" name="currencyname" value="<?php echo $params['currencyname']?>" size="10">
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG8'),
					'content' 	=> JText::_('VAPMANAGECONFIG8_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- CURRENCY SYMBOL POSITION - Select -->
		<?php
		$elements = array(
			JHtml::_('select.option', '1', JText::_('VAPCONFIGSYMBPOSITION2')),
			JHtml::_('select.option', '2', JText::_('VAPCONFIGSYMBPOSITION1')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG25"); ?></b> </td>
			<td>
				<select name="currsymbpos" class="small-medium" onchange="formatSamplePrice();">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['currsymbpos']); ?>
				</select>
			</td>
		</tr>

		<!-- CURRENCY DECIMAL SEPARATOR - Text -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG103"); ?></b> </td>
			<td><input type="text" name="currdecimalsep" value="<?php echo $params['currdecimalsep']?>" size="10" onchange="formatSamplePrice();"></td>
		</tr>

		<!-- CURRENCY THOUSANDS SEPARATOR - Text -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG104"); ?></b> </td>
			<td><input type="text" name="currthousandssep" value="<?php echo $params['currthousandssep']?>" size="10" onchange="formatSamplePrice();"></td>
		</tr>

		<!-- CURRENCY NUMBER OF DECIMALS - Number -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG105"); ?></b> </td>
			<td><input type="number" name="currdecimaldig" value="<?php echo $params['currdecimaldig']; ?>" min="0" max="9999" onchange="formatSamplePrice();"/></td>
		</tr>

		<!-- FINAL RESULT - LABEL -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG116"); ?></b> </td>
			<td id="currency-sample-price">
				<?php echo VikAppointments::printPriceCurrencySymb(1234.56); ?>
			</td>
		</tr>
		
	</table>
</fieldset>

<script>

	function formatSamplePrice() {
		var currency = Currency.getInstance();

		currency.decimals 	= jQuery('input[name="currdecimalsep"]').val();
		currency.digits 	= parseInt(jQuery('input[name="currdecimaldig"]').val());
		currency.position 	= parseInt(jQuery('select[name="currsymbpos"]').val());
		currency.symbol 	= jQuery('input[name="currencysymb"]').val();
		currency.thousands 	= currency.decimals == '.' ? ',' : '.';

		jQuery('#currency-sample-price').html(currency.format(1234.56));
	}

</script>
