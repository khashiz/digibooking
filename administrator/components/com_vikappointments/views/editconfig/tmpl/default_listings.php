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

<!-- Employees Listing Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE9'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- EMPLOYEES LISTINGS ORDERING - Select -->
		<?php
		$emp_list_mode = json_decode($params['emplistmode'], true);
		?>
		<tr>
			<td width="200" class="adminparamcol" style="vertical-align: top;"> <b><?php echo JText::_("VAPMANAGECONFIG17"); ?></b> </td>
			<td><div class="vap-config-empord-fieldslist">
				<?php foreach ($emp_list_mode as $i => $active)
				{
					$options = array(
						JHtml::_('select.option', 1, JText::_('JYES')),
						JHtml::_('select.option', 0, JText::_('JNO')),
					);
					?>
					<div class="vap-config-empord-field" style="margin-bottom: 5px;">
						<span class="vap-sort-box"></span>
						<input type="text" readonly value="<?php echo JText::_('VAPCONFIGEMPLISTMODE'.$i); ?>" />&nbsp;
						<select name="emplistmode[<?php echo $i; ?>]" class="short">
							<?php echo JHtml::_('select.options', $options, 'value', 'text', $active); ?>
						</select>
					</div>
				<?php } ?>
			</div></td>
		</tr>
		
		<!-- EMPLOYEES LIST LIMIT - Number -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG80"); ?></b> </td>
			<td>
				<input type="number" name="emplistlim" value="<?php echo $params['emplistlim']; ?>" min="1" />
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG80'),
					'content' 	=> JText::_('VAPMANAGECONFIG80_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- EMPLOYEES DESCRIPTION LENGTH - Number -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG60"); ?></b> </td>
			<td><input type="number" name="empdesclength" value="<?php echo $params['empdesclength']; ?>" min="32"/>&nbsp;<?php echo JText::_('VAPCHARS'); ?></td>
		</tr>
		
		<!-- EMPLOYEES IMAGE LINK ACTION - Dropdown -->
		<?php
		$elements = array(
			JHtml::_('select.option', 1, JText::_('VAPCONFIGLINKHREF1')),
			JHtml::_('select.option', 2, JText::_('VAPCONFIGLINKHREF2')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG61"); ?></b> </td>
			<td>
				<select name="emplinkhref" class="medium-large">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['emplinkhref']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG61'),
					'content' 	=> JText::_('VAPMANAGECONFIG61_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- ENABLE EMPLOYEES GROUPS FILTER - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['empgroupfilter'] == "1");
		$elem_no  = $vik->initRadioElement('', '', $params['empgroupfilter'] == "0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG88"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('empgroupfilter', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG88'),
					'content' 	=> JText::_('VAPMANAGECONFIG88_DESC'),
				)); ?>
			</td>
		</tr>

		<!-- ENABLE EMPLOYEES ORDERING FILTER - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['empordfilter'] == "1");
		$elem_no  = $vik->initRadioElement('', '', $params['empordfilter'] == "0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG90"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('empordfilter', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG90'),
					'content' 	=> JText::_('VAPMANAGECONFIG90_DESC'),
				)); ?>
			</td>
		</tr>

		<!-- ENABLE AJAX SEARCH - Dropdown -->
		<?php
		$elements = array(
			JHtml::_('select.option', 0, JText::_('VAPCONFIGAJAXSEARCHOPT0')),
			JHtml::_('select.option', 2, JText::_('VAPCONFIGAJAXSEARCHOPT2')),
			JHtml::_('select.option', 1, JText::_('VAPCONFIGAJAXSEARCHOPT1')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG115"); ?></b> </td>
			<td>
				<select name="empajaxsearch" class="medium-large">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['empajaxsearch']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG115'),
					'content' 	=> JText::_('VAPMANAGECONFIG115_DESC'),
				)); ?> 
			</td>
		</tr>
		
	</table>
</fieldset>

<!-- Services Listing Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE11'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- SERVICES DESCRIPTION LENGTH - Number -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG60"); ?></b> </td>
			<td><input type="number" name="serdesclength" value="<?php echo $params['serdesclength']; ?>" min="32"/>&nbsp;<?php echo JText::_('VAPCHARS'); ?></td>
		</tr>
		
		<!-- SERVICES IMAGE LINK ACTION - Dropdown -->
		<?php
		$elements = array(
			JHtml::_('select.option', 1, JText::_('VAPCONFIGLINKHREF3')),
			JHtml::_('select.option', 2, JText::_('VAPCONFIGLINKHREF2')),
			JHtml::_('select.option', 3, JText::_('VAPCONFIGLINKHREF4')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG61"); ?></b> </td>
			<td>
				<select name="serlinkhref" class="medium-large">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['serlinkhref']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG61'),
					'content' 	=> JText::_('VAPMANAGECONFIG61_DESC'),
				)); ?>
			</td>
		</tr>
		
	</table>
</fieldset>
