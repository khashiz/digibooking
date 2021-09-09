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

$templates = $this->templates;

?>

<!-- E-mail Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE5'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- ADMIN MAIL - Text -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG1"); ?></b> </td>
			<td>
				<input type="text" name="adminemail" value="<?php echo $params['adminemail']?>" size="40">
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG1'),
					'content' 	=> JText::_('VAPMANAGECONFIG1_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- SENDER MAIL - Text -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG24"); ?></b> </td>
			<td><input type="text" name="senderemail" value="<?php echo $params['senderemail']?>" size="40"></td>
		</tr>
		
		<!--  MAIL ATTACHMENT - File -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG49"); ?></b> </td>
			<td>
				<span>
					<input type="file" name="mailattach" size="40"/>
					<?php if (strlen($params['mailattach'])) { ?>
						<a href="<?php echo VAPBASE_URI . 'helpers/mail_attach/' . $params['mailattach']; ?>" id="vapmailattach" target="_blank"><?php echo $params['mailattach']; ?></a>
						<button type="button" class="btn" onclick="jQuery('#vapmailattach').remove();jQuery('#vapremovemailattach').val(1);this.hide()">
							<i class="icon-remove"></i>
						</button>
						<input type="hidden" name="remove_mail_attach" value="0" id="vapremovemailattach"/>
					<?php } ?>
				</span>

				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG49'),
					'content' 	=> JText::_('VAPMANAGECONFIG49_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- SEND TO CUSTOMER WHEN - Dropdown -->
		<?php
		$elements = array(
			JHtml::_('select.option', 1, JText::_('VAPCONFIGSENDMAILWHEN1')),
			JHtml::_('select.option', 2, JText::_('VAPCONFIGSENDMAILWHEN2')),
			JHtml::_('select.option', 0, JText::_('VAPCONFIGSENDMAILWHEN3')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG71"); ?></b> </td>
			<td>
				<select name="mailcustwhen" class="medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['mailcustwhen']); ?>
				</select>
			</td>
		</tr>
		
		<!-- SEND TO EMPLOYEE WHEN - Dropdown -->
		<?php
		$elements = array(
			JHtml::_('select.option', 1, JText::_('VAPCONFIGSENDMAILWHEN1')),
			JHtml::_('select.option', 2, JText::_('VAPCONFIGSENDMAILWHEN2')),
			JHtml::_('select.option', 0, JText::_('VAPCONFIGSENDMAILWHEN3')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG72"); ?></b> </td>
			<td>
				<select name="mailempwhen" class="medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['mailempwhen']); ?>
				</select>
			</td>
		</tr>
		
		<!-- SEND TO ADMIN WHEN - Dropdown -->
		<?php
		$elements = array(
			JHtml::_('select.option', 1, JText::_('VAPCONFIGSENDMAILWHEN1')),
			JHtml::_('select.option', 2, JText::_('VAPCONFIGSENDMAILWHEN2')),
			JHtml::_('select.option', 0, JText::_('VAPCONFIGSENDMAILWHEN3')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG73"); ?></b> </td>
			<td>
				<select name="mailadminwhen" class="medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['mailadminwhen']); ?>
				</select>
			</td>
		</tr>
		
		<!-- CUSTOMER EMAIL TEMPLATE -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG62"); ?></b> </td>
			<td>
				<select name="mailtmpl" class="medium-large" id="vap-emailtmpl-sel">
					<?php echo JHtml::_('select.options', $templates, 'value', 'text', $params['mailtmpl']); ?>
				</select>

				<div class="btn-group">
					<button type="button" class="btn" onclick="TEMPLATE_ID = 'vap-emailtmpl-sel';vapOpenJModal('emailtmpl', null, true); return false;">
						<i class="icon-pencil" style="margin-right: 0;"></i>
					</button>
					<button type="button" class="btn" onclick="goToMailPreview('vap-emailtmpl-sel', 'customer');">
						<i class="icon-eye" style="margin-right: 0;"></i>
					</button>
					<button type="button" class="btn btn-success" onclick="document.location.href = 'index.php?option=com_vikappointments&task=mailtextcust';">
						<?php echo JText::_('VAPMANAGECONFIG69'); ?>
					</button>
				</div>
			</td>
		</tr>

		<!-- ADMINISTRATOR EMAIL TEMPLATE -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG91"); ?></b> </td>
			<td>
				<select name="adminmailtmpl" class="medium-large" id="vap-adminemailtmpl-sel">
					<?php echo JHtml::_('select.options', $templates, 'value', 'text', $params['adminmailtmpl']); ?>
				</select>
				
				<div class="btn-group">
					<button type="button" class="btn" onclick="TEMPLATE_ID = 'vap-adminemailtmpl-sel';vapOpenJModal('emailtmpl', null, true); return false;">
						<i class="icon-pencil" style="margin-right: 0;"></i>
					</button>
					<button type="button" class="btn" onclick="goToMailPreview('vap-adminemailtmpl-sel', 'admin');">
						<i class="icon-eye" style="margin-right: 0;"></i>
					</button>
				</div>
			</td>
		</tr>

		<!-- EMPLOYEE EMAIL TEMPLATE -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG92"); ?></b> </td>
			<td>
				<select name="empmailtmpl" class="medium-large" id="vap-empemailtmpl-sel">
					<?php echo JHtml::_('select.options', $templates, 'value', 'text', $params['empmailtmpl']); ?>
				</select>

				<div class="btn-group">
					<button type="button" class="btn" onclick="TEMPLATE_ID = 'vap-empemailtmpl-sel';vapOpenJModal('emailtmpl', null, true); return false;">
						<i class="icon-pencil" style="margin-right: 0;"></i>
					</button>
					<button type="button" class="btn" onclick="goToMailPreview('vap-empemailtmpl-sel', 'employee');">
						<i class="icon-eye" style="margin-right: 0;"></i>
					</button>
				</div>
			</td>
		</tr>

		<!-- CANCELLATION EMAIL TEMPLATE -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG93"); ?></b> </td>
			<td>
				<select name="cancmailtmpl" class="medium-large" id="vap-cancemailtmpl-sel">
					<?php echo JHtml::_('select.options', $templates, 'value', 'text', $params['cancmailtmpl']); ?>
				</select>

				<div class="btn-group">
					<button type="button" class="btn" onclick="TEMPLATE_ID = 'vap-cancemailtmpl-sel';vapOpenJModal('emailtmpl', null, true); return false;">
						<i class="icon-pencil" style="margin-right: 0;"></i>
					</button>
					<button type="button" class="btn" onclick="goToMailPreview('vap-cancemailtmpl-sel', 'cancellation');">
						<i class="icon-eye" style="margin-right: 0;"></i>
					</button>
				</div>
			</td>
		</tr>
		
		<!-- ATTACH ICS TO - Checkbox List -->
		<?php 
		$ics_values = explode(';', $params['icsattach']);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG65"); ?></b> </td>
			<td>
				<?php for ($i = 0; $i < count($ics_values); $i++) { ?>
					<input type="checkbox" name="icsattach<?php echo ($i+1); ?>" value="1" id="icsattach<?php echo ($i+1); ?>" <?php echo ( ( $ics_values[$i] == 1) ? "checked=\"checked\"" : "" ); ?>/>
					<label for="icsattach<?php echo ($i+1); ?>" style="display: inline-block;"><?php echo JText::_("VAPCONFIGSMSAPITO".$i); ?></label>&nbsp;&nbsp; 
				<?php } ?>
			</td>
		</tr>
		
		<!-- ATTACH CSV TO - Checkbox List -->
		<?php 
		$csv_values = explode(';', $params['csvattach']);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG66"); ?></b> </td>
			<td>
				<?php for ($i = 0; $i < count($csv_values); $i++) { ?>
					<input type="checkbox" name="csvattach<?php echo ($i+1); ?>" value="1" id="csvattach<?php echo ($i+1); ?>" <?php echo ( ( $csv_values[$i] == 1) ? "checked=\"checked\"" : "" ); ?>/>
					<label for="csvattach<?php echo ($i+1); ?>" style="display: inline-block;"><?php echo JText::_("VAPCONFIGSMSAPITO".$i); ?></label>&nbsp;&nbsp; 
				<?php } ?>
			</td>
		</tr>
		
	</table>
</fieldset>
