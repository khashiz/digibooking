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

$languages = VikAppointments::getKnownLanguages();

?>

<!-- Shop Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE8'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- DEFAULT STATUS - Select -->
		<?php
		$elements = array(
			JHtml::_('select.option', 'CONFIRMED', JText::_('VAPSTATUSCONFIRMED')),
			JHtml::_('select.option', 'PENDING', JText::_('VAPSTATUSPENDING')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG27"); ?></b> </td>
			<td>
				<select name="defstatus" class="small-medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['defstatus']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG27'),
					'content' 	=> JText::_('VAPMANAGECONFIG27_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- ENABLE CART FRAMEWORK - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['enablecart'] == "1", 'onClick="enableCartValueChanged(1);"');
		$elem_no  = $vik->initRadioElement('', '' , $params['enablecart'] == "0", 'onClick="enableCartValueChanged(0);"');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG45"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('enablecart', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG45'),
					'content' 	=> JText::_('VAPMANAGECONFIG45_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- MAX ITEMS IN CART - Number -->
		<?php
		$elements = array(
			JHtml::_('select.option', 1, JText::_('VAPMANAGECONFIG47')),
			JHtml::_('select.option', 2, JText::_('VAPMANAGECONFIG97')),
		);
		?>
		<tr class="vapcartchildtr" style="<?php echo ( $params['enablecart'] == "0" ? 'display: none' : '' ); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG46"); ?></b> </td>
			<td>
				<select class="small-medium" onchange="maxCartSizeValueChanged(this);">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['maxcartsize'] > 0 ? 2 : 1); ?>
				</select>
				<input type="number" name="maxcartsize" value="<?php echo $params['maxcartsize']; ?>" size="20" min="1" max="99999999" id="vapmaxcartsize" style="<?php echo ($params['maxcartsize'] == "-1" ? 'display: none;' : ''); ?>"/> 
			</td>
		</tr>

		<!-- CART ALLOW SYNC - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['cartallowsync'] == "1");
		$elem_no  = $vik->initRadioElement('', '' , $params['cartallowsync'] == "0");
		?>
		<tr class="vapcartchildtr" style="<?php echo ( $params['enablecart'] == "0" ? 'display: none' : '' ); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG98"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('cartallowsync', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG98'),
					'content'	=> JText::_('VAPMANAGECONFIG98_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- SHOP CONTINUE LINK - Select -->
		<?php
		$elements = array(
			JHtml::_('select.option', '', ''),
			JHtml::_('select.option', -2, JText::_('VAPMANAGECONFIGSHOPOPT3')),
			JHtml::_('select.option', -1, JText::_('VAPMANAGECONFIGSHOPOPT2')),
		);
		foreach ($this->groups as $grp)
		{
			$elements[] = JHtml::_('select.option', $grp['id'], $grp['name']);
		}
		?>
		<tr class="vapcartchildtr" style="<?php echo ($params['enablecart'] == "0" ? 'display: none' : '' ); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG53"); ?></b> </td>
			<td>
				<select name="shoplink" id="vap-shoplink-dropdown">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['shoplink']); ?>
				</select>&nbsp;
				<input type="text" name="shoplinkcustom" id="vap-shoplink-text" value="<?php echo $params['shoplinkcustom']; ?>" style="<?php echo ($params['shoplink'] == -2 ? '' : 'display:none;'); ?>" size="48" placeholder="index.php"/>
			
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG53'),
					'content'	=> JText::_('VAPMANAGECONFIG53_DESC'),
				)); ?>
			</td>
		</tr>

		<!-- CART ALREADY EXPANDED -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['confcartdisplay'] == "1");
		$elem_no  = $vik->initRadioElement('', '' , $params['confcartdisplay'] == "0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG84"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('confcartdisplay', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG84'),
					'content'	=> JText::_('VAPMANAGECONFIG84_DESC'),
				)); ?>
			</td>
		</tr>

		<!-- SHOW CHECKOUT -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['showcheckout'] == "1");
		$elem_no  = $vik->initRadioElement('', '' , $params['showcheckout'] == "0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG118"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('showcheckout', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG118'),
					'content'	=> JText::_('VAPMANAGECONFIG118_DESC'),
				)); ?>
			</td>
		</tr>

		<!-- USE DEPOSIT - Select -->
		<?php
		$elements = array(
			JHtml::_('select.option', 0, 'VAPCONFIGDEPOSITOPT0'),
			JHtml::_('select.option', 1, 'VAPCONFIGDEPOSITOPT1'),
			JHtml::_('select.option', 2, 'VAPCONFIGDEPOSITOPT2'),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG110"); ?></b> </td>
			<td>
				<select name="usedeposit" class="medium" onChange="useDepositValueChanged(this);">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['usedeposit'], true); ?>
				</select>

				<?php
				$content = '';
				for ($i = 0; $i < count($elements); $i++)
				{
					if ($i != 0)
					{
						$content .= '<br />';
					}

					$content .= '<b>' . JText::_('VAPCONFIGDEPOSITOPT' . $i) . '</b><br />' . JText::_('VAPCONFIGDEPOSITOPT' . $i . '_DESC');
				}

				echo $vik->createPopover(array(
					"title" 	=> JText::_('VAPMANAGECONFIG110'),
					"content"	=> $content,
				));
				?>
			</td>
		</tr>
		
		<!-- DEPOSIT AFTER VALUE - Number -->
		<tr class="vap-deposit-child" style="<?php echo ($params['usedeposit'] == 0 ? 'display:none;' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG50"); ?></b> </td>
			<td>
				<input type="number" name="depositafter" value="<?php echo $params['depositafter']?>" min="0" max="999999999" size="10" step="any">
				&nbsp;<?php echo $params['currencysymb']; ?>
				<?php
				echo $vik->createPopover(array(
					"title" 	=> JText::_('VAPMANAGECONFIG50'),
					"content"	=> JText::_('VAPMANAGECONFIG50_DESC'),
				));
				?>
			</td>
		</tr>
		
		<!-- DEPOSIT AMOUNT - Number -->
		<?php
		$elements = array(
			JHtml::_('select.option', 1, '%'),
			JHtml::_('select.option', 2, $params['currencysymb']),
		);
		?> 
		<tr class="vap-deposit-child" style="<?php echo ($params['usedeposit'] == 0 ? 'display:none;' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG52"); ?></b> </td>
			<td>
				<input type="number" name="depositvalue" value="<?php echo $params['depositvalue']?>" min="1" max="999999999" size="10" step="any">&nbsp;
				<select name="deposittype" class="short">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['deposittype']); ?>
				</select>
			</td>
		</tr>
		
		<!-- LOGIN REQUIREMENTS - Select -->
		<?php
		$elements = array(
			JHtml::_('select.option', 0, JText::_('VAPCONFIGSENDMAILWHEN3')),
			JHtml::_('select.option', 1, JText::_('VAPMANAGECONFIG43')),
			JHtml::_('select.option', 2, JText::_('VAPMANAGECONFIG44')),
			JHtml::_('select.option', 3, JText::_('VAPMANAGECONFIG89')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG42"); ?></b> </td>
			<td>
				<select name="loginreq" class="medium-large">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['loginreq']); ?>
				</select>
			</td>
		</tr>
		
		<!-- ENABLE CANCELLATION - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('canc1', '', $params['enablecanc'] == "1", 'onClick="cancValueChanged(1);"');
		$elem_no  = $vik->initRadioElement('canc0', '', $params['enablecanc'] == "0", 'onClick="cancValueChanged(0);"');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG30"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('enablecanc', $elem_yes, $elem_no); ?></td>
		</tr>
		
		<!-- MIN CANCELLATION TIME - Number -->
		<tr class="vapconfcanctr" style="<?php echo ($params['enablecanc'] == "0" ? 'display: none' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG31"); ?></b> </td>
			<td><input type="number" name="canctime" value="<?php echo $params['canctime']; ?>" size="40" min="0" max="999" step="1"> <?php echo JText::_('VAPDAYSLABEL'); ?></td>
		</tr>

		<!-- USER CREDIT - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['usercredit'] == "1");
		$elem_no  = $vik->initRadioElement('', '', $params['usercredit'] == "0");
		?>
		<tr class="vapconfcanctr" style="<?php echo ($params['enablecanc'] == "0" ? 'display: none' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG114"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('usercredit', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG114'),
					'content' 	=> JText::_('VAPMANAGECONFIG114_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- PRINTABLE ORDERS - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['printorders'] == "1");
		$elem_no  = $vik->initRadioElement('', '' , $params['printorders'] == "0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG59"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('printorders', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG59'),
					'content' 	=> JText::_('VAPMANAGECONFIG59_DESC'),
				)); ?>
			</td>
		</tr>

		<!-- AUTO GENERATE INVOICE - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['invoiceorders'] == "1", 'onClick="vapOpenJModal( \'invoice\', null, true);"');
		$elem_no  = $vik->initRadioElement('', '' , $params['invoiceorders'] == "0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG95"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('invoiceorders', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG95'),
					'content' 	=> JText::_('VAPMANAGECONFIG95_DESC'),
				)); ?>
			</td>
		</tr>
		
	</table>
</fieldset>

<!-- Waiting List Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE14'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- ENABLE WAITING LIST - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['enablewaitlist'] == "1", 'onClick="waitlistValueChanged(1);"');
		$elem_no  = $vik->initRadioElement('', '', $params['enablewaitlist'] == "0", 'onClick="waitlistValueChanged(0);"');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG100"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('enablewaitlist', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG100'),
					'content' 	=> JText::_('VAPMANAGECONFIG100_DESC'),
				)); ?>
			</td>
		</tr>

		<!-- WAITING LIST EMAIL TEMPLATE -->
		<tr class="vapwaitlistrow" style="<?php echo (($params['enablewaitlist'] == "0") ? 'display: none' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG102"); ?></b> </td>
			<td>
				<select name="waitlistmailtmpl" class="medium-large" id="vap-wlemailtmpl-sel">
					<?php echo JHtml::_('select.options', $templates, 'value', 'text', $params['waitlistmailtmpl']); ?>
				</select>

				<div class="btn-group">
					<button type="button" class="btn" onclick="TEMPLATE_ID = 'vap-wlemailtmpl-sel';vapOpenJModal('emailtmpl', null, true); return false;">
						<i class="icon-pencil" style="margin-right: 0px;"></i>
					</button>
					<button type="button" class="btn" onclick="goToMailPreview('vap-wlemailtmpl-sel', 'waitlist');">
						<i class="icon-eye" style="margin-right: 0px;"></i>
					</button>
				</div>
			</td>
		</tr>

		<!-- SMS CONTENT - Form -->
		<tr class="vapwaitlistrow" style="<?php echo (($params['enablewaitlist'] == "0") ? 'display: none' : ''); ?>">
			<td width="200" style="vertical-align: top;" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG101"); ?></b> </td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group pull-left">
						<button type="button" class="btn" onClick="putSmsTagOnActiveContentWL(1, '{checkin_day}');">{checkin_day}</button>
						<button type="button" class="btn" onClick="putSmsTagOnActiveContentWL(1, '{checkin_time}');">{checkin_time}</button>
						<button type="button" class="btn" onClick="putSmsTagOnActiveContentWL(1, '{service}');">{service}</button>
						<button type="button" class="btn" onClick="putSmsTagOnActiveContentWL(1, '{company}');">{company}</button>
						<button type="button" class="btn" onClick="putSmsTagOnActiveContentWL(1, '{details_url}');">{details_url}</button>
					</div>
				</div>
				<div class="control">
					<?php
					$sms_tmpl_cust = json_decode($params['waitlistsmscont'], true);
					foreach ($languages as $k => $lang)
					{ 
						$lang_name = explode('-', $lang);
						$lang_name = strtolower($lang_name[1]);
						for ($i = 0; $i < 2; $i++)
						{ 
							$content = "";
							if (!empty($sms_tmpl_cust[$i][$lang]))
							{
								$content = $sms_tmpl_cust[$i][$lang];
							}
							?>
							<textarea class="vap-smswlcont-1" id="vapsmswlcont<?php echo $lang_name; ?>-<?php echo ($i+1); ?>" 
							style="width: 95%;height: 200px;<?php echo ($k != 0 || $i == 1 ? 'display:none;' : ''); ?>" name="waitlistsmscont[<?php echo $i; ?>][]"><?php echo $content; ?></textarea>
						<?php
						}
					} ?>
				</div>  
				<!-- LANGUAGES -->
				<div class="btn-toolbar" style="width: 95%;">
					<div class="btn-group pull-left">
						<button type="button" class="btn active vap-smswl-type" id="vap-switchwl-button-0" onClick="switchSmsContentWL(0);"><?php echo JText::_('VAPSMSCONTSWITCHSINGLE'); ?></button>
						<button type="button" class="btn vap-smswl-type" id="vap-switchwl-button-1" onClick="switchSmsContentWL(1);"><?php echo JText::_('VAPSMSCONTSWITCHMULTI'); ?></button>
					</div>
					<div class="btn-group pull-right">
						<?php foreach ($languages as $k => $lang)
						{ 
							$lang_name = explode('-', $lang);
							$lang_name = strtolower($lang_name[1]);
							?>
							<button type="button" class="vap-smswl-langtag btn <?php echo ($k == 0 ? 'active' : ''); ?>" id="vapsmswltag<?php echo $lang_name; ?>" onClick="changeLanguageSMSWL('<?php echo $lang_name; ?>');">
								<i class="icon">
									<img src="<?php echo VAPASSETS_URI . 'css/flags/'.$lang_name.'.png';?>"/>
								</i>
								&nbsp;<?php echo strtoupper($lang_name); ?>
							</button>
						<?php } ?>
					</div>
				</div> 
			</td>
		</tr>
		
	</table>
</fieldset>

<?php 

$repeat_by = explode(';',$params['repeatbyrecur']);
$for_next = explode(';',$params['fornextrecur']);

$recurr_repeatby_select = '<select id="vaprecrepeatbysel" class="short">';
$recurr_fornext_select = '<select id="vaprecfornextsel" class="short">';

$recurr_amount_select = '<select id="vaprecamountsel" class="short">';
for ($i = $params['minamountrecur']; $i <= $params['maxamountrecur']; $i++)
{
	$recurr_amount_select .= '<option value="'.$i.'">'.$i.'</option>';
}
$recurr_amount_select .= '</select>';

?>

<!-- Recurrence Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE3'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- ENABLE RECURRENCE - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['enablerecur'] == "1", 'onClick="recurrenceValueChanged(1);"');
		$elem_no  = $vik->initRadioElement('', '', $params['enablerecur'] == "0", 'onClick="recurrenceValueChanged(0);"');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGREC1"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('enablerecur', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIGREC1'),
					'content' 	=> JText::_('VAPMANAGECONFIGREC1_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- RECURRENCE REPEAT BY - Checkbox List -->
		<tr class="vaprecurrtr" style="<?php echo (($params['enablerecur'] == "0") ? 'display: none' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGREC2"); ?></b> </td>
			<td>
				<?php for ($i = 0; $i < count($repeat_by); $i++) { ?>
					<input type="checkbox" name="repeatby<?php echo ($i+1); ?>" value="1" id="repeatby<?php echo ($i+1); ?>" <?php echo ( ( $repeat_by[$i] == 1) ? "checked=\"checked\"" : "" ); ?> onChange="renderRecurrenceBarRepeatSelect();">
					<label for="repeatby<?php echo ($i+1); ?>" style="display: inline-block;"><?php echo JText::_("VAPMANAGECONFIGRECSINGOPT".($i+1)); ?></label> &nbsp;&nbsp; 
				<?php 
					if ($repeat_by[$i] == 1)
					{
						$recurr_repeatby_select .= '<option value="'.$repeat_by[$i].'">'.JText::_("VAPMANAGECONFIGRECSINGOPT".($i+1)).'</option>';
					}
				} ?>
			</td>
		</tr>
		
		<!-- RECURRENCE MIN VALUE - Number -->
		<tr class="vaprecurrtr" style="<?php echo (($params['enablerecur'] == "0") ? 'display: none' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGREC3"); ?></b> </td>
			<td><input type="number" name="minamountrecur" id="minamountrecur" value="<?php echo $params['minamountrecur']; ?>" size="40" min="1" max="999" onChange="renderRecurrenceBarAmountSelect();">
		</tr>
		
		<!-- RECURRENCE MAX VALUE - Number -->
		<tr class="vaprecurrtr" style="<?php echo (($params['enablerecur'] == "0") ? 'display: none' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGREC4"); ?></b> </td>
			<td><input type="number" name="maxamountrecur" id="maxamountrecur" value="<?php echo $params['maxamountrecur']; ?>" size="40" min="1" max="999" onChange="renderRecurrenceBarAmountSelect();">
		</tr>
		
		<!-- RECURRENCE FOR NEXT - Checkbox List -->
		<tr class="vaprecurrtr" style="<?php echo (($params['enablerecur'] == "0") ? 'display: none' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGREC5"); ?></b> </td>
			<td>
				<?php for ($i = 0; $i < count($repeat_by); $i++) { ?>
					<input type="checkbox" name="fornext<?php echo ($i+1); ?>" value="1" id="fornext<?php echo ($i+1); ?>" <?php echo ( ( $for_next[$i] == 1) ? "checked=\"checked\"" : "" ); ?> onChange="renderRecurrenceBarForSelect();">
					<label for="fornext<?php echo ($i+1); ?>" style="display: inline-block;"><?php echo JText::_("VAPMANAGECONFIGRECMULTOPT".($i+1)); ?></label> &nbsp;&nbsp; 
				<?php 
					if ($for_next[$i] == 1)
					{
						$recurr_fornext_select .= '<option value="'.$for_next[$i].'">'.JText::_("VAPMANAGECONFIGRECMULTOPT".($i+1)).'</option>';
					}
				} ?>
			</td>
		</tr>
		
		<?php 
			$recurr_repeatby_select .= '</select>';
			$recurr_fornext_select .= '</select>';
		?>
		
		<!-- RECURRENCE DEMO BOX -->
		<tr class="vaprecurrtr" style="<?php echo (($params['enablerecur'] == "0") ? 'display: none' : ''); ?>">
			<td width="200" class="">&nbsp;</td>
			<td>
				<span>
					<?php echo JText::_('VAPMANAGECONFIGREC2'); ?>
					<?php echo $recurr_repeatby_select; ?>&nbsp;&nbsp;
					<?php echo JText::_('VAPMANAGECONFIGREC5'); ?>
					<?php echo $recurr_amount_select; ?>&nbsp;
					<?php echo $recurr_fornext_select; ?>
				</span>
				<span style="font-size: 11px;font-style: italic;margin-left: 30px;"><?php echo JText::_("VAPMANAGECONFIGREC6"); ?></span>
			</td>
		</tr>
		
	</table>
</fieldset>

<!-- Reviews Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE12'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- ENABLE REVIEWS - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['enablereviews'] == "1", 'onClick="reviewsValueChanged(1);"');
		$elem_no  = $vik->initRadioElement('', '', $params['enablereviews'] == "0", 'onClick="reviewsValueChanged(0);"');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG74"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('enablereviews', $elem_yes, $elem_no); ?></td>
		</tr>
		
		<!-- SERVICES REVIEWS - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['revservices'] == "1");
		$elem_no  = $vik->initRadioElement('', '', $params['revservices'] == "0");
		?>
		<tr class="vapreviewstr" <?php echo ($params['enablereviews'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG75"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('revservices', $elem_yes, $elem_no); ?></td>
		</tr>
		
		<!-- EMPLOYEES REVIEWS - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['revemployees'] == "1");
		$elem_no  = $vik->initRadioElement('', '', $params['revemployees'] == "0");
		?>
		<tr class="vapreviewstr" <?php echo ($params['enablereviews'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG76"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('revemployees', $elem_yes, $elem_no); ?></td>
		</tr>
		
		<!-- REVIEW COMMENT REQUIRED - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['revcommentreq'] == "1");
		$elem_no  = $vik->initRadioElement('', '', $params['revcommentreq'] == "0");
		?>
		<tr class="vapreviewstr" <?php echo ($params['enablereviews'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG77"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('revcommentreq', $elem_yes, $elem_no); ?></td>
		</tr>
		
		<!-- MIN COMMENT LENGTH - Number -->
		<tr class="vapreviewstr" <?php echo ($params['enablereviews'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG78"); ?></b> </td>
			<td><input type="number" name="revminlength" value="<?php echo $params['revminlength']; ?>" min="0"/>&nbsp;<?php echo JText::_('VAPCHARS'); ?></td>
		</tr>
		
		<!-- MAX COMMENT LENGTH - Number -->
		<tr class="vapreviewstr" <?php echo ($params['enablereviews'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG79"); ?></b> </td>
			<td><input type="number" name="revmaxlength" value="<?php echo $params['revmaxlength']; ?>" min="32"/>&nbsp;<?php echo JText::_('VAPCHARS'); ?></td>
		</tr>
		
		<!-- REVIEWS LIST LIMIT - Number -->
		<tr class="vapreviewstr" <?php echo ($params['enablereviews'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG80"); ?></b> </td>
			<td><input type="number" name="revlimlist" value="<?php echo $params['revlimlist']; ?>" min="1"/></td>
		</tr>
		
		<!-- AUTO PUBLISHED - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['revautopublished'] == "1");
		$elem_no  = $vik->initRadioElement('', '', $params['revautopublished'] == "0");
		?>
		<tr class="vapreviewstr" <?php echo ($params['enablereviews'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG82"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('revautopublished', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG82'),
					'content' 	=> JText::_('VAPMANAGECONFIG82_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- FILTER BY LANGUAGE - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['revlangfilter'] == "1");
		$elem_no  = $vik->initRadioElement('', '', $params['revlangfilter'] == "0");
		?>
		<tr class="vapreviewstr" <?php echo ($params['enablereviews'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG81"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('revlangfilter', $elem_yes, $elem_no); ?></td>
		</tr>
		
		<!-- REVIEWS LOAD MODE - Dropdown -->
		<?php
		$elements = array(
			JHtml::_('select.option', 1, JText::_('VAPCONFIGREVLOADMODE1')),
			JHtml::_('select.option', 2, JText::_('VAPCONFIGREVLOADMODE2')),
		);
		?>
		<tr class="vapreviewstr" <?php echo ($params['enablereviews'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG85"); ?></b> </td>
			<td>
				<select name="revloadmode" class="medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['revloadmode']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG85'),
					'content' 	=> JText::_('VAPMANAGECONFIG85_DESC'),
				)); ?>
			</td>
		</tr>
		
	</table>
</fieldset>

<!-- Packages Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE16'); ?></legend>
	<table class="admintable table" cellspacing="1">

		<!-- ENABLE PACKAGES - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['enablepackages'] == "1", 'onClick="packagesValueChanged(1);"');
		$elem_no  = $vik->initRadioElement('', '', $params['enablepackages'] == "0",  'onClick="packagesValueChanged(0);"');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG109"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('enablepackages', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG109'),
					'content' 	=> JText::_('VAPMANAGECONFIG109_DESC'),
				)); ?>
			</td>
		</tr>

		<!-- PACKAGES PER ROW - Dropdown -->
		<?php
		$elements = array();
		for ($i = 1; $i <= 6; $i++)
		{
			$elements[] = JHtml::_('select.option', $i, $i);
		}
		?>
		<tr class="vappackagesrow" <?php echo ($params['enablepackages'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG106"); ?></b> </td>
			<td>
				<select name="packsperrow" class="short">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['packsperrow']); ?>
				</select>
			</td>
		</tr>
		
		<!-- MAX PACKAGES IN CART - Number -->
		<?php
		$elements = array(
			JHtml::_('select.option', 1, JText::_('VAPMANAGECONFIG47')),
			JHtml::_('select.option', 2, JText::_('VAPMANAGECONFIG97')),
		);
		?>
		<tr class="vappackagesrow" <?php echo ($params['enablepackages'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG107"); ?></b> </td>
			<td>
				<select class="small-medium" onchange="maxPacksCartValueChanged(this);">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['maxpackscart'] == -1 ? 1 : 2); ?>
				</select>&nbsp;
				<input type="number" name="maxpackscart" value="<?php echo $params['maxpackscart']; ?>" size="20" min="1" max="99999999" id="vapmaxpackscart" style="<?php echo ($params['maxpackscart'] == "-1" ? 'display: none;' : ''); ?>"/> 
			</td>
		</tr>

		<!-- ALLOW USER REGISTRATION - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['packsreguser']==1);
		$elem_no  = $vik->initRadioElement('', '', $params['packsreguser']==0);
		?>
		<tr class="vappackagesrow" <?php echo ($params['enablepackages'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG108"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo("packsreguser", $elem_yes, $elem_no); ?>
			</td>
		</tr>

		<!-- PACKAGES EMAIL TEMPLATE -->
		<tr class="vappackagesrow" <?php echo ($params['enablepackages'] == "0" ? 'style="display:none;"' : ''); ?>>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG102"); ?></b> </td>
			<td>
				<select name="packmailtmpl" class="medium-large" id="vap-packemailtmpl-sel">
					<?php echo JHtml::_('select.options', $templates, 'value', 'text', $params['packmailtmpl']); ?>
				</select>
				
				<div class="btn-group">
					<button type="button" class="btn" onclick="TEMPLATE_ID = 'vap-packemailtmpl-sel';vapOpenJModal('emailtmpl', null, true); return false;">
						<i class="icon-pencil" style="margin-right: 0px;"></i>
					</button>
					<button type="button" class="btn" onclick="goToMailPreview('vap-packemailtmpl-sel', 'package');">
						<i class="icon-eye" style="margin-right: 0px;"></i>
					</button>
				</div>
			</td>
		</tr>
		
	</table>
</fieldset>

<?php
JText::script('VAPMANAGECONFIGSHOPOPT1');
JText::script('VAPMANAGECONFIGRECSINGOPT1');
JText::script('VAPMANAGECONFIGRECSINGOPT2');
JText::script('VAPMANAGECONFIGRECSINGOPT3');
JText::script('VAPMANAGECONFIGRECMULTOPT1');
JText::script('VAPMANAGECONFIGRECMULTOPT2');
JText::script('VAPMANAGECONFIGRECMULTOPT3');
?>

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-shoplink-dropdown').select2({
			placeholder: Joomla.JText._('VAPMANAGECONFIGSHOPOPT1'),
			allowClear: true,
			width: 250
		});

		jQuery('#vap-shoplink-dropdown').on('change', function(){
			if (jQuery(this).val() == "-2") {
				jQuery('#vap-shoplink-text').show();
			} else {
				jQuery('#vap-shoplink-text').hide();
			}
		});

	});

	function cancValueChanged(val){
		if( val ) {
			jQuery('.vapconfcanctr').show();
		} else {
			jQuery('.vapconfcanctr').hide();
		}
	}
	
	function enableCartValueChanged(is) {
		if( is ) {
			jQuery('.vapcartchildtr').show();
		} else {
			jQuery('.vapcartchildtr').hide();
		}
		jQuery('#vapmaxcartsize').val( (is ? 1 : 0) );
	}
	
	function maxCartSizeValueChanged(select) {
		if( jQuery(select).val() == "1" ) {
			jQuery('#vapmaxcartsize').val(-1);
			jQuery('#vapmaxcartsize').hide();
		} else {
			jQuery('#vapmaxcartsize').val(1);
			jQuery('#vapmaxcartsize').show();
		}
	}

	function maxPacksCartValueChanged(select) {
		if( jQuery(select).val() == "1" ) {
			jQuery('#vapmaxpackscart').val(-1);
			jQuery('#vapmaxpackscart').hide();
		} else {
			jQuery('#vapmaxpackscart').val(1);
			jQuery('#vapmaxpackscart').show();
		}
	}
	
	function recurrenceValueChanged(is) {
		if( is ) {
			jQuery('.vaprecurrtr').show();
		} else {
			jQuery('.vaprecurrtr').hide();
		}
	}

	function waitlistValueChanged(is) {
		if( is ) {
			jQuery('.vapwaitlistrow').show();
		} else {
			jQuery('.vapwaitlistrow').hide();
		}
	}
	
	function reviewsValueChanged(is) {
		if( is ) {
			jQuery('.vapreviewstr').show();
		} else {
			jQuery('.vapreviewstr').hide();
		}
	}

	function packagesValueChanged(is) {
		if( is ) {
			jQuery('.vappackagesrow').show();
		} else {
			jQuery('.vappackagesrow').hide();
		}
	}

	function useDepositValueChanged(select) {
		var val = parseInt(jQuery(select).val());

		if (val == 0) {
			jQuery('.vap-deposit-child').hide();
		} else {
			jQuery('.vap-deposit-child').show();
		}
	}
	
	function renderRecurrenceBarRepeatSelect() {
		jQuery('#vaprecrepeatbysel').html('');
		
		var _text = new Array(
			Joomla.JText._('VAPMANAGECONFIGRECSINGOPT1'),
			Joomla.JText._('VAPMANAGECONFIGRECSINGOPT2'),
			Joomla.JText._('VAPMANAGECONFIGRECSINGOPT3')
		); 
		for( var i = 0; i < _text.length; i++ ) {
			if( jQuery('#repeatby'+(i+1)).is(':checked') ) {
				jQuery('#vaprecrepeatbysel').append('<option>'+_text[i]+'</option>');
			}
		}
	}
	
	function renderRecurrenceBarForSelect() {
		jQuery('#vaprecfornextsel').html('');
		
		var _text = new Array(
			Joomla.JText._('VAPMANAGECONFIGRECMULTOPT1'),
			Joomla.JText._('VAPMANAGECONFIGRECMULTOPT2'),
			Joomla.JText._('VAPMANAGECONFIGRECMULTOPT3')
		); 
		for( var i = 0; i < _text.length; i++ ) {
			if( jQuery('#fornext'+(i+1)).is(':checked') ) {
				jQuery('#vaprecfornextsel').append('<option>'+_text[i]+'</option>');
			}
		}
	}
	
	function renderRecurrenceBarAmountSelect() {
		jQuery('#vaprecamountsel').html('');
		
		var min = parseInt(jQuery('#minamountrecur').val());
		var max = parseInt(jQuery('#maxamountrecur').val());
		if( min > max ) {
			min = max;
		}
		 
		for( var i = min; i <= max; i++ ) {
			jQuery('#vaprecamountsel').append('<option>'+i+'</option>');
		}
	}

	// SMS WAITING LIST

	function putSmsTagOnActiveContentWL(id, cont) {
		
		var area = null;
		jQuery('.vap-smswlcont-'+id).each(function(){
			if( jQuery(this).css('display') != 'none' ) {
				area = jQuery(this);
			}
		});
		
		if( area == null ) {
			return;
		}
		
		var start = area.get(0).selectionStart;
		var end = area.get(0).selectionEnd;
		area.val(area.val().substring(0, start) + cont + area.val().substring(end));
		area.get(0).selectionStart = area.get(0).selectionEnd = start + cont.length;
		area.focus();
	}
	
	function changeLanguageSMSWL(tag) {
		jQuery('.vap-smswl-langtag').removeClass('active');
		jQuery('#vapsmswltag'+tag).addClass('active');
		
		var area = null;
		jQuery('.vap-smswlcont-1').each(function(){
			if( jQuery(this).css('display') != 'none' ) {
				area = jQuery(this);
			}
		});
		
		if( area == null ) {
			return;
		}
		
		jQuery('.vap-smswlcont-1').hide();
		jQuery('#vapsmswlcont'+tag+'-'+area.attr('id').split("-")[1]).show();
	}
	
	function switchSmsContentWL(section) {

		if (jQuery('#vap-switchwl-button-'+section).hasClass('active')) {
			return false;
		}

		jQuery('.vap-smswl-type').removeClass('active');
		jQuery('#vap-switchwl-button-'+section).addClass('active');
		
		var area = null;
		jQuery('.vap-smswlcont-1').each(function() {
			if (jQuery(this).css('display') != 'none') {
				area = jQuery(this);
			}
		});
		
		if (area == null) {
			return;
		}
		
		var id = area.attr('id').split('-');
		area.hide();

		jQuery('#'+id[0]+'-'+(section + 1)).show();
	}

</script>
