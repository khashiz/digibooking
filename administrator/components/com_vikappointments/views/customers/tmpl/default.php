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

$rows   = $this->rows;
$navbut = $this->navbut;

$ordering = $this->ordering;

$filters = $this->filters;

$vik = UIApplication::getInstance();

foreach (array('u.id', 'u.billing_name', 'u.billing_mail', 'rescount', 'u.credit') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$config = UIFactory::getConfig();

$links = array(
	OrderingManager::getLinkColumnOrder('customers', JText::_('VAPMANAGECUSTOMER1'), 'u.id', $ordering['u.id'], 1, $filters, 'vapheadcolactive'.(($ordering['u.id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('customers', JText::_('VAPMANAGECUSTOMER2'), 'u.billing_name', $ordering['u.billing_name'], 1, $filters, 'vapheadcolactive'.(($ordering['u.billing_name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('customers', JText::_('VAPMANAGECUSTOMER3'), 'u.billing_mail', $ordering['u.billing_mail'], 1, $filters, 'vapheadcolactive'.(($ordering['u.billing_mail'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('customers', JText::_('VAPMANAGECUSTOMER18'), 'rescount', $ordering['rescount'], 1, $filters, 'vapheadcolactive'.(($ordering['rescount'] == 2) ? 1 : 2)),
);

if ($config->getBool('usercredit'))
{
	$links[] = OrderingManager::getLinkColumnOrder('customers', JText::_('VAPUSERCREDIT'), 'u.credit', $ordering['u.credit'], 1, $filters, 'vapheadcolactive'.(($ordering['u.credit'] == 2) ? 1 : 2));
}

$is_searching = $this->hasFilters();

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">
	
	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left input-append">
			<input type="text" name="keys" id="vapkeysearch" size="32" 
				value="<?php echo $filters['keys']; ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />

			<button type="submit" class="btn">
				<i class="icon-search"></i>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn <?php echo ($is_searching ? 'btn-primary' : ''); ?>" onclick="vapToggleSearchToolsButton(this);">
				<?php echo JText::_('JSEARCH_TOOLS'); ?>&nbsp;<i class="fa fa-caret-<?php echo ($is_searching ? 'up' : 'down'); ?>" id="vap-tools-caret"></i>
			</button>
		</div>
		
		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>

	</div>

	<div class="btn-toolbar" id="vap-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<?php
		$options = array(
			JHtml::_('select.option', 0, JText::_('VAPFILTERSELECTTYPE')),
			JHtml::_('select.option', 1, JText::_('VAPREGISTERED')),
			JHtml::_('select.option', -1, JText::_('VAPMANAGECUSTOMER15')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="utype" id="vap-type-sel" class="<?php echo ($filters['type'] != 0 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['type']); ?>
			</select>
		</div>

		<?php
		$options = array(
			JHtml::_('select.option', '', JText::_('VAPFILTERSELECTCOUNTRY')),
		);
		foreach ($this->countries as $c)
		{
			$options[] = JHtml::_('select.option', $c['country_2_code'], $c['country_name']);
		}
		?>
		<div class="btn-group pull-left">
			<select name="country" id="vap-country-sel" class="<?php echo (!empty($filters['country']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['country']); ?>
			</select>
		</div>

	</div>
	
<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOCUSTOMER');?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[2];?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER4');?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER5');?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER7');?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER10');?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[3]; ?></th>

				<?php if ($config->getBool('usercredit')) { ?>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[4]; ?></th>
				<?php } ?>

				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER21');?></th>

				<?php if ($this->isSms) { ?>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPSENDSMS');?></th>
				<?php } ?>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;"><a href="index.php?option=com_vikappointments&amp;task=editcustomer&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['billing_name']; ?></a></td>
				
				<td style="text-align: center;"><?php echo $row['billing_mail']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['billing_phone']; ?></td>
				
				<td style="text-align: center;">
					<?php if (!empty($row['country_code'])) { ?>
						<img src="<?php echo VAPASSETS_URI . 'css/flags/'.strtolower($row['country_code']).'.png'; ?>" />
					<?php } ?>
				</td>
				
				<td style="text-align: center;"><?php echo $row['billing_city']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['company']; ?></td>
				
				<td style="text-align: center;"><?php echo ($row['jid'] != -1 ? $row['rescount'] : JText::_('VAPMANAGECUSTOMER15')); ?></td>

				<?php if ($config->getBool('usercredit')) { ?>
					<td style="text-align: center;">
						<?php
						if ((float) $row['credit'])
						{
							echo VikAppointments::printPriceCurrencySymb($row['credit']);
						}
						?>
					</td>
				<?php } ?>
				
				<td style="text-align: center;">
					<a href="javascript: void(0);" onclick="SELECTED_CUSTOMER=<?php echo $row['id']; ?>;vapOpenJModal('custinfo', null, true); return false;">
						<i class="fa fa-search big"></i>
					</a>
				</td>
				
				<?php if ($this->isSms) { ?>
					<td style="text-align: center;">
						<input type="hidden" id="vap-hidden-name-<?php echo $row['id']; ?>" value="<?php echo JText::sprintf('VAPSMSDIALOGTITLE', $row['billing_name']); ?>" />
						<?php if (!empty($row['billing_phone'])) { ?>
							<a href="javascript: void(0);" onClick="openSmsDialog(<?php echo $row['id']; ?>);">
								<i class="fa fa-comment big"></i>
							</a>
						<?php } else { ?>
							<i class="fa fa-comment big"></i>
						<?php } ?>
					</td>
				<?php } ?>
			</tr>
		
		<?php }	?>
	
	</table>

<?php } ?>
	
	<!-- hidden input for import tool -->
	<input type="hidden" name="import_type" value="customers" />

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="customers" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<?php 
if ($this->isSms)
{
	$sms_default_text = VikAppointments::getSmsDefaultCustomersText(true);
}
else
{
	$sms_default_text = "";
}

echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-custinfo',
	array(
		'title'       => JText::_('VAPMANAGECUSTOMER21'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<div id="dialog-confirm" title="" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-comment" style="float: left; margin: 0 7px 20px 0;"></span>
		<span id="msg-required"><?php echo JText::_('VAPSMSDIALOGMESSAGE'); ?></span>
		<div><textarea style="width: 90%;height: 120px;resize: none;" id="dialog-confirm-input" maxlength="160"><?php echo $sms_default_text; ?></textarea></div>
		<div>
			<input type="checkbox" value="1" id="vap-keepmsg-asdef" />
			<label for="vap-keepmsg-asdef" style="display: inline-block;"><?php echo JText::_('VAPKEEPSMSTEXTDEF'); ?></label>
		</div>
	</p>
</div>

<form action="index.php?option=com_vikappointments&task=sendcustsms" id="vapsmsform" method="POST">
	<input type="hidden" name="id_cust" value="" id="vapcustid_h" />
	<input type="hidden" name="msg" value="" id="vapmsg_h" />
	<input type="hidden" name="keepdef" value="" id="vapkeepdef_h" />
</form>

<script>

	function openSmsDialog(id) {
		var title = jQuery('#vap-hidden-name-'+id).val();
		jQuery('#dialog-confirm').dialog({title: title});
		
		jQuery("#dialog-confirm").dialog({
			resizable: false,
			width: 480,
			height: 340,
			modal: true,
			buttons: {
				"<?php echo JText::_('VAPSENDSMS'); ?>": function() {

					if (jQuery('#dialog-confirm-input').val().length) {
						jQuery(this).dialog('close');
						sendSms(id);
					} else {
						jQuery('#msg-required').addClass('invalid');
					}
				},
				"<?php echo JText::_('VAPCANCEL'); ?>": function() {
					jQuery(this).dialog('close');
					jQuery('#msg-required').removeClass('invalid');
				}
			}
		});
	}    
	
	function sendSms(id) {
		
		var sms_msg = jQuery('#dialog-confirm-input').val();
		var keep_msg = (jQuery('#vap-keepmsg-asdef').is(':checked') ? 1 : 0);
		
		jQuery('#vapcustid_h').val(id);
		jQuery('#vapmsg_h').val(sms_msg);
		jQuery('#vapkeepdef_h').val(keep_msg);
		
		jQuery('#vapsmsform').submit();
	}
	
	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-type-sel').updateChosen(0);
		jQuery('#vap-country-sel').updateChosen('');
		
		document.adminForm.submit();
	}

	// modal

	SELECTED_CUSTOMER = -1;

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'custinfo') {
			url = 'index.php?option=com_vikappointments&tmpl=component&task=customerinfo&id=' + SELECTED_CUSTOMER;
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}
	
</script>
