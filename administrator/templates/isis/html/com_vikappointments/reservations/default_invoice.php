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

$vik = UIApplication::getInstance();

$date_format = UIFactory::getConfig()->get('dateformat');

?>

<!-- invoice properties modal -->
<?php echo $vik->bootStartTabSet('pdftab', array('active' => 'pdftabinfo')); ?>

	<?php echo $vik->bootAddTab('pdftab', 'pdftabinfo', JText::_('VAPINVOICEDETAILS')); ?>
		<table class="admintable table" cellspacing="1">
			<!-- INVOICE IDENTIFIER - Number/Text -->
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICE1"); ?></b> </td>
				<td><span>
					<input type="number" name="invoicenumber" value="<?php echo $this->pdfParams['invoicenumber']; ?>" min="1" style="text-align: right;">
					/
					<input type="text" name="invoicesuffix" value="<?php echo $this->pdfParams['invoicesuffix']; ?>" size="6">
				</span></td>
			</tr>
			
			<!-- DATE - Dropdown -->
			<?php
			$options = array(
				JHtml::_('select.option', 1, JText::sprintf('VAPINVOICEDATEOPT1', ArasJoomlaVikApp::jdate($date_format))),
				JHtml::_('select.option', 2, JText::_('VAPINVOICEDATEOPT2')),
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICE2"); ?></b> </td>
				<td>
					<select name="datetype" id="vap-invdatetype-sel">
						<?php echo JHtml::_('select.options', $options, 'value', 'text', $this->pdfParams['datetype']); ?>
					</select>
				</td>
			</tr>
			
			<!-- Taxes - Number -->
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICE3"); ?></b> </td>
				<td><span><input type="number" name="taxes" value="<?php echo $this->pdfParams['taxes']; ?>" min="0" max="100" step="any" style="text-align: right;">&nbsp;%</span></td>
			</tr>
			
			<!-- Legal Information - Number -->
			<tr>
				<td width="200" class="adminparamcol" style="vertical-align:top;"> <b><?php echo JText::_("VAPMANAGEINVOICE4"); ?></b> </td>
				<td><textarea name="legalinfo" style="width: 95%;height: 70px;"><?php echo $this->pdfParams['legalinfo']; ?></textarea></td>
			</tr>
			
			<!-- Send Invoice via e-mail - Checkbox -->
			<?php
			$yes = $vik->initRadioElement('', '', $this->pdfParams['sendinvoice']);
			$no  = $vik->initRadioElement('', '', !$this->pdfParams['sendinvoice']);
			?>
			<tr>
				<td colspan="2">
					<?php echo $vik->radioYesNo('sendinvoice', $yes, $no); ?>
					<span style="vertical-align: top;line-height: 32px;margin-left: 10px;"><?php echo JText::_("VAPMANAGEINVOICE5"); ?></span>
				</td>
			</tr>
		</table>
	<?php echo $vik->bootEndTab(); ?>
	
	<?php echo $vik->bootAddTab('pdftab', 'pdftabprop', JText::_('VAPINVOICEPROPERTIES')); ?>
		<table class="admintable table" cellspacing="1">
			<!-- PAGE ORIENTATION - Dropdown -->
			<?php
			$options = array(
				JHtml::_('select.option', VikAppointmentsConstraintsPDF::PAGE_ORIENTATION_PORTRAIT, JText::_('VAPINVOICEPROPORIENTATIONOPT1')),
				JHtml::_('select.option', VikAppointmentsConstraintsPDF::PAGE_ORIENTATION_LANDSCAPE, JText::_('VAPINVOICEPROPORIENTATIONOPT2')),
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICEPROP1"); ?></b> </td>
				<td>
					<select name="page_orientation" id="vap-invpageor-sel">
						<?php echo JHtml::_('select.options', $options, 'value', 'text', $this->pdfConstraints->pageOrientation); ?>
					</select>
				</td>
			</tr>
			
			<!-- PAGE FORMAT - Dropdown -->
			<?php
			$options = array(
				JHtml::_('select.option', VikAppointmentsConstraintsPDF::PAGE_FORMAT_A4, 'A4'),
				JHtml::_('select.option', VikAppointmentsConstraintsPDF::PAGE_FORMAT_A5, 'A5'),
				JHtml::_('select.option', VikAppointmentsConstraintsPDF::PAGE_FORMAT_A6, 'A6'),
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICEPROP2"); ?></b> </td>
				<td>
					<select name="page_format" id="vap-invpageformat-sel">
						<?php echo JHtml::_('select.options', $options, 'value', 'text', $this->pdfConstraints->pageFormat); ?>
					</select>
				</td>
			</tr>
			
			<!-- UNIT - Dropdown -->
			<?php
			$options = array(
				JHtml::_('select.option', VikAppointmentsConstraintsPDF::UNIT_POINT, JText::_('VAPINVOICEPROPUNITOPT1')),
				JHtml::_('select.option', VikAppointmentsConstraintsPDF::UNIT_MILLIMETER, JText::_('VAPINVOICEPROPUNITOPT2')),
				JHtml::_('select.option', VikAppointmentsConstraintsPDF::UNIT_CENTIMETER, JText::_('VAPINVOICEPROPUNITOPT3')),
				JHtml::_('select.option', VikAppointmentsConstraintsPDF::UNIT_INCH, JText::_('VAPINVOICEPROPUNITOPT4')),
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICEPROP3"); ?></b> </td>
				<td>
					<select name="unit" id="vap-invunit-sel">
						<?php echo JHtml::_('select.options', $options, 'value', 'text', $this->pdfConstraints->unit); ?>
					</select>
				</td>
			</tr>
			
			<!-- SCALE - Number -->
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGEINVOICEPROP4"); ?></b> </td>
				<td><span><input type="number" name="scale" value="<?php echo max(array(5, round($this->pdfConstraints->imageScaleRatio*100))); ?>" min="5" max="10000" step="any" style="text-align: right;">&nbsp;%</span></td>
			</tr>
			
		</table>
	<?php echo $vik->bootEndTab(); ?>
	
<?php echo $vik->bootEndTabSet(); ?>
