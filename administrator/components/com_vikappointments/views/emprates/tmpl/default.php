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

JHtml::_('formbehavior.chosen');

$row = $this->row;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$curr_symb = $config->get('currencysymb');

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

<?php if (count($this->services) == 0) { ?>
		
	<p><?php echo JText::_('VAPNORATES'); ?></p>

<?php } else { ?>

	<div class="btn-toolbar" style="height: 32px;">
		
		<div class="btn-group pull-left">

			<?php
			$options = array();
			foreach ($this->services as $s)
			{
				$options[] = JHtml::_('select.option', $s['id'], $s['name']);
			}
			?>
			<select name="id_ser" id="vap-service-sel" onChange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $this->idService); ?>
			</select>

		</div>

	</div>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="20%" style="text-align: left;"><?php echo JText::_('VAPMANAGERATES1'); ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="20%" style="text-align: left;"><?php echo JText::_('VAPMANAGERATES2'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="20%" style="text-align: center;"><?php echo JText::_('VAPMANAGERATES3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="20%" style="text-align: center;"><?php echo JText::_('VAPMANAGERATES4'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="20%" style="text-align: center;"><?php echo JText::_('VAPMANAGERATES5'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<!-- rate -->
		<?php
		if ($row['rate'] == $row['max_price'])
		{
			$color = 'color: #BB0000;';
		}
		else if ($row['rate'] == $row['min_price'])
		{
			$color = 'color: #009900;';
		}
		else
		{
			$color = 'color: #000000;';
		}

		$style = "style=\"font-weight: bold;$color\"";
		?>
		<tr class="row1">
			<td style="text-align: left;"><?php echo JText::_('VAPMANAGERATES6'); ?></td>
			
			<td style="text-align: left;">
				<input type="text" value="<?php echo $row['rate']; ?>" <?php echo $style; ?> name="rate" size="10" id="vaprateinput<?php echo $row['id']; ?>" 
					onkeyup="inputValueChanged('vaprateinput<?php echo $row['id']; ?>',<?php echo $row['min_price']; ?>,<?php echo $row['max_price']; ?>);" />
				&nbsp;<?php echo $curr_symb; ?>
			</td>
			
			<td style="text-align: center;">
				<?php echo VikAppointments::printPriceCurrencySymb($row['default_price']); ?>
			</td>
			
			<td style="text-align: center;color: #009900;font-weight: bold;">
				<?php echo VikAppointments::printPriceCurrencySymb($row['min_price']); ?>
			</td>
			
			<td style="text-align: center;color: #DD0000;font-weight: bold;">
				<?php echo VikAppointments::printPriceCurrencySymb($row['max_price']); ?>
			</td>
		</tr>

		<!-- duration -->
		<?php
		if ($row['duration'] == $row['max_duration'])
		{
			$color = 'color: #BB0000;';
		}
		else if ($row['duration'] == $row['min_duration'])
		{
			$color = 'color: #009900;';
		}
		else
		{
			$color = 'color: #000000;';
		}

		$style = "style=\"font-weight: bold;$color\"";
		?>
		<tr class="row0">
			<td style="text-align: left;"><?php echo JText::_('VAPMANAGERATES7'); ?></td>
			
			<td style="text-align: left;">
				<input type="text" value="<?php echo $row['duration']; ?>" <?php echo $style; ?> name="duration" size="10" id="vapdurationinput<?php echo $row['id']; ?>" 
					onkeyup="inputValueChanged('vapdurationinput<?php echo $row['id']; ?>',<?php echo $row['min_duration']; ?>,<?php echo $row['max_duration']; ?>);" />
				&nbsp;<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
			</td>
			
			<td style="text-align: center;">
				<?php echo $row['default_duration']; ?> <?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
			</td>
			
			<td style="text-align: center;color: #009900;font-weight: bold;">
				<?php echo $row['min_duration']; ?> <?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
			</td>
			
			<td style="text-align: center;color: #DD0000;font-weight: bold;">
				<?php echo $row['max_duration']; ?> <?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
			</td>
		</tr>

		<!-- duration -->
		<?php
		if ($row['sleep'] == $row['max_sleep'])
		{
			$color = 'color: #BB0000;';
		}
		else if ($row['sleep'] == $row['min_sleep'])
		{
			$color = 'color: #009900;';
		}
		else
		{
			$color = 'color: #000000;';
		}

		$style = "style=\"font-weight: bold;$color\"";
		?>
		<tr class="row1">
			<td style="text-align: left;"><?php echo JText::_('VAPMANAGERATES8'); ?></td>
			
			<td style="text-align: left;">
				<input type="text" value="<?php echo $row['sleep']; ?>" <?php echo $style; ?> name="sleep" size="10" id="vapsleepinput<?php echo $row['id']; ?>" 
					onkeyup="inputValueChanged('vapsleepinput<?php echo $row['id']; ?>',<?php echo $row['min_sleep']; ?>,<?php echo $row['max_sleep']; ?>);" />
				&nbsp;<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
			</td>
			
			<td style="text-align: center;">
				<?php echo $row['default_sleep']; ?> <?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
			</td>
			
			<td style="text-align: center;color: #009900;font-weight: bold;">
				<?php echo $row['min_sleep']; ?> <?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
			</td>
			
			<td style="text-align: center;color: #DD0000;font-weight: bold;">
				<?php echo $row['max_sleep']; ?> <?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
			</td>
		</tr> 

		<?php
		/**
		 * Added support for description override.
		 *
		 * @since 1.6.2
		 */
		?>
		<tr class="row0">
			<td style="text-align: left;">
				<?php echo JText::_('VAPMANAGESERVICE3'); ?>

				<span>
					<?php
					echo $vik->createPopover(array(
						'title'   => JText::_('VAPMANAGESERVICE3'),
						'content' => JText::_('VAPDESCOVERRIDE_HELP'),
					));
					?>
				</span>
			</td>
			
			<td style="text-align: left;" colspan="2">
				<textarea name="description" style="width: calc(100% - 10px); height: 120px; resize: vertical;"><?php echo $row['description']; ?></textarea>
			</td>
			
			<td style="text-align: left;" colspan="2">
				<textarea readonly="readonly" style="width: calc(100% - 10px); height: 120px; resize: vertical;"><?php echo $row['default_desc']; ?></textarea>
			</td>
		</tr> 
		
	</table>

<?php } ?>

	<input type="hidden" name="assoc" value="<?php echo $row['id']; ?>" />
	<input type="hidden" name="task" value="emprates" />
	<input type="hidden" name="id_emp" value="<?php echo $this->idEmployee; ?>" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function inputValueChanged(id, min, max) {
		var val = parseFloat(jQuery('#'+id).val());
		
		if (val <= min && min != max)
		{
			jQuery('#'+id).css('color', '#009900');
		}
		else if (val >= max && min != max)
		{
			jQuery('#'+id).css('color', '#DD0000');
		}
		else
		{
			jQuery('#'+id).css('color', '#000000');
		}
		
		if (val < 0) {
			jQuery('#'+id).val(0);
		}
	}
	
</script>
