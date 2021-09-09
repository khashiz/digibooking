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

$params['pdfparams'] 		= VikAppointments::getPdfParams();
$params['pdfconstraints'] 	= VikAppointments::getPdfConstraints();

$vik = UIApplication::getInstance();

?>

<br clear="all" />

<?php echo $vik->bootStartTabSet('pdftab', array('active' => 'pdftabinfo')); ?>
			
	<?php echo $vik->bootAddTab('pdftab', 'pdftabinfo', JText::_('VAPINVOICEDETAILS')); ?>
		<table class="admintable table" cellspacing="1">
			<!-- INVOICE IDENTIFIER - Number/Text -->
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICE1"); ?></b> </td>
				<td><span>
					<input type="number" name="attr_invoicenumber" value="<?php echo $params['pdfparams']['invoicenumber']; ?>" min="1" style="text-align: right;">
					/
					<input type="text" name="attr_invoicesuffix" value="<?php echo $params['pdfparams']['invoicesuffix']; ?>" size="6">
				</span></td>
			</tr>
			
			<!-- DATE - Dropdown -->
			<?php
			$elements = array(
				$vik->initOptionElement(1, JText::sprintf('VAPINVOICEDATEOPT1', date($params['dateformat'])), $params['pdfparams']['datetype']==1),
				$vik->initOptionElement(2, JText::_('VAPINVOICEDATEOPT2'), $params['pdfparams']['datetype']==2)
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICE2"); ?></b> </td>
				<td><?php echo $vik->dropdown('attr_datetype', $elements, '', 'invoice-select'); ?></td>
			</tr>
			
			<!-- Taxes - Number -->
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICE3"); ?></b> </td>
				<td><span><input type="number" name="attr_taxes" value="<?php echo $params['pdfparams']['taxes']; ?>" min="0" max="100" step="any" style="text-align: right;">&nbsp;%</span></td>
			</tr>
			
			<!-- Legal Information - Number -->
			<tr>
				<td width="200" class="adminparamcol" style="vertical-align:top;"> <b><?php echo JText::_("VAPMANAGEINVOICE4"); ?></b> </td>
				<td><textarea name="attr_legalinfo" style="width: 95%;height: 70px;"><?php echo $params['pdfparams']['legalinfo']; ?></textarea></td>
			</tr>
			
			<!-- Send Invoice via e-mail - Checkbox -->
			<tr>
				<td colspan="2">
					<input type="checkbox" name="attr_sendinvoice" value="1" id="vapsendinvoicebox" <?php echo ($params['pdfparams']['sendinvoice'] ? 'checked="checked"' : ''); ?>/>
					<label for="vapsendinvoicebox" style="display: inline-block;"><?php echo JText::_("VAPMANAGEINVOICE5"); ?></label>
				</td>
			</tr>
		</table>
	<?php echo $vik->bootEndTab(); ?>
	
	<?php echo $vik->bootAddTab('pdftab', 'pdftabprop', JText::_('VAPINVOICEPROPERTIES')); ?>
		<table class="admintable table" cellspacing="1">
			<!-- PAGE ORIENTATION - Dropdown -->
			<?php
			$PORTRAIT = VikAppointmentsConstraintsPDF::PAGE_ORIENTATION_PORTRAIT;
			$LANDSCAPE = VikAppointmentsConstraintsPDF::PAGE_ORIENTATION_LANDSCAPE;
			$elements = array(
				$vik->initOptionElement($PORTRAIT, JText::_('VAPINVOICEPROPORIENTATIONOPT1'), $params['pdfconstraints']->pageOrientation==$PORTRAIT),
				$vik->initOptionElement($LANDSCAPE, JText::_('VAPINVOICEPROPORIENTATIONOPT2'), $params['pdfconstraints']->pageOrientation==$LANDSCAPE)
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICEPROP1"); ?></b> </td>
				<td><?php echo $vik->dropdown('prop_page_orientation', $elements, '', 'invoice-select'); ?></td>
			</tr>
			
			<!-- PAGE FORMAT - Dropdown -->
			<?php
			$A4 = VikAppointmentsConstraintsPDF::PAGE_FORMAT_A4;
			$A5 = VikAppointmentsConstraintsPDF::PAGE_FORMAT_A5;
			$A6 = VikAppointmentsConstraintsPDF::PAGE_FORMAT_A6;
			$elements = array(
				$vik->initOptionElement($A4, 'A4', $params['pdfconstraints']->pageFormat==$A4),
				$vik->initOptionElement($A5, 'A5', $params['pdfconstraints']->pageFormat==$A5),
				$vik->initOptionElement($A6, 'A6', $params['pdfconstraints']->pageFormat==$A6),
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICEPROP2"); ?></b> </td>
				<td><?php echo $vik->dropdown('prop_page_format', $elements, '', 'invoice-select'); ?></td>
			</tr>
			
			<!-- UNIT - Dropdown -->
			<?php
			$PT = VikAppointmentsConstraintsPDF::UNIT_POINT;
			$MM = VikAppointmentsConstraintsPDF::UNIT_MILLIMETER;
			$CM = VikAppointmentsConstraintsPDF::UNIT_CENTIMETER;
			$IN = VikAppointmentsConstraintsPDF::UNIT_INCH;
			$elements = array(
				$vik->initOptionElement($PT, JText::_('VAPINVOICEPROPUNITOPT1'), $params['pdfconstraints']->unit==$PT),
				$vik->initOptionElement($MM, JText::_('VAPINVOICEPROPUNITOPT2'), $params['pdfconstraints']->unit==$MM),
				$vik->initOptionElement($CM, JText::_('VAPINVOICEPROPUNITOPT3'), $params['pdfconstraints']->unit==$CM),
				$vik->initOptionElement($IN, JText::_('VAPINVOICEPROPUNITOPT4'), $params['pdfconstraints']->unit==$IN),
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICEPROP3"); ?></b> </td>
				<td><?php echo $vik->dropdown('prop_unit', $elements, '', 'invoice-select'); ?></td>
			</tr>
			
			<!-- SCALE - Number -->
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICEPROP4"); ?></b> </td>
				<td><span><input type="number" name="prop_scale" value="<?php echo max(array(5, round($params['pdfconstraints']->imageScaleRatio*100))); ?>" min="5" max="10000" step="any" style="text-align: right;">&nbsp;%</span></td>
			</tr>
			
		</table>
	<?php echo $vik->bootEndTab(); ?>
	
<?php echo $vik->bootEndTabSet(); ?>

<script>

	jQuery(document).ready(function() {
		jQuery('select.invoice-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 200,
		});
	});

</script>
