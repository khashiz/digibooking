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

$rows 	= $this->rows;
$navbut = $this->navbut;

$ordering = $this->ordering;

$filters = $this->filters;

$vik = UIApplication::getInstance();

foreach (array('id', 'code', 'value', 'dstart') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('coupons', JText::_('VAPMANAGECOUPON1'), 'id', $ordering['id'], 1, $filters, 'vapheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('coupons', JText::_('VAPMANAGECOUPON2'), 'code', $ordering['code'], 1, $filters, 'vapheadcolactive'.(($ordering['code'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('coupons', JText::_('VAPMANAGECOUPON5'), 'value', $ordering['value'], 1, $filters, 'vapheadcolactive'.(($ordering['value'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('coupons', JText::_('VAPMANAGECOUPON12'), 'dstart', $ordering['dstart'], 1, $filters, 'vapheadcolactive'.(($ordering['dstart'] == 2) ? 1 : 2)),
);

$config = UIFactory::getConfig();

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

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

		<div class="btn-group pull-right">
			<a href="index.php?option=com_vikappointments&task=coupongroups" class="btn">
				<?php echo JText::_('VAPMANAGEGROUPS'); ?>
			</a>
		</div>
	</div>

	<div class="btn-toolbar" id="vap-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<?php
		$options = array(
			JHtml::_('select.option', 0, JText::_('VAPFILTERSELECTTYPE')),
			JHtml::_('select.option', 1, JText::_('VAPCOUPONTYPEOPTION1')),
			JHtml::_('select.option', 2, JText::_('VAPCOUPONTYPEOPTION2')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="type" id="vap-type-sel" class="<?php echo ($filters['type'] != 0 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['type']); ?>
			</select>
		</div>

		<?php
		$options = array(
			JHtml::_('select.option', 0, JText::_('VAPFILTERSELECTVAL')),
			JHtml::_('select.option', 1, JText::_('VAPCOUPONVALUETYPE1')),
			JHtml::_('select.option', 2, JText::_('VAPCOUPONVALUETYPE2')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="value" id="vap-value-sel" class="<?php echo ($filters['value'] != 0 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['value']); ?>
			</select>
		</div>

		<?php
		$options = array(
			JHtml::_('select.option', 0, JText::_('VAPFILTERSELECTSTATUS')),
			JHtml::_('select.option', 1, JText::_('VAPCOUPONVALID0')),
			JHtml::_('select.option', 2, JText::_('VAPCOUPONVALID1')),
			JHtml::_('select.option', 3, JText::_('VAPCOUPONVALID2')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vap-status-sel" class="<?php echo ($filters['status'] != 0 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status']); ?>
			</select>
		</div>

		<?php
		$options = array(
			JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTGROUP')),
			JHtml::_('select.option', 0, JText::_('VAPSERVICENOGROUP')),
		);

		foreach ($this->groups as $group)
		{
			$options[] = JHtml::_('select.option', $group['id'], $group['name']);
		}

		?>
		<div class="btn-group pull-left">
			<select name="id_group" id="vap-group-sel" class="<?php echo ($filters['id_group'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['id_group']); ?>
			</select>
		</div>

	</div>

<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOCOUPON'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGECOUPON3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGECOUPON6'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[3]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo JText::_('VAPMANAGECOUPON10'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo JText::_('VAPMANAGECOUPON16'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGECOUPON9'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			
			$now = time();
			
			$valid = 0;
			$class = 'vapreservationstatusremoved';
			
			if ($row['type'] == 1 || $row['max_quantity'] - $row['used_quantity'] > 0)
			{
				if ($row['dstart'] == -1 || ($row['dstart'] <= $now && $now <= $row['dend']) || ($row['pubmode'] == 2 && $now <= $row['dend']))
				{
					$valid = 1;
					$class = 'vapreservationstatusconfirmed';
				}
				else if ($row['dstart'] > $now)
				{
					$valid = 2;
					$class = 'vapreservationstatuspending';
				}
			}

			$tooltip = JText::sprintf('VAPCOUPONINFOTIP', 
				$row['used_quantity'], 
				($row['type'] == 2 ? max(array(0, $row['max_quantity'] - $row['used_quantity'])) : "&infin;"), 
				(strlen($row['notes']) ? '<br /><br />' : '') . $row['notes']
			);
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: left;"><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;"><a href="index.php?option=com_vikappointments&amp;task=editcoupon&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['code']; ?></a></td>
				
				<td style="text-align: center;"><?php echo JText::_('VAPCOUPONTYPEOPTION' . $row['type'] ); ?></td>
				
				<td style="text-align: center;"><?php echo ($row['percentot'] == 1 ? $row['value'] . JText::_('VAPCOUPONPERCENTOTOPTION1') : VikAppointments::printPriceCurrencySymb($row['value'])); ?></td>
				
				<td style="text-align: center;"><?php echo VikAppointments::printPriceCurrencySymb($row['mincost']); ?></td>
				
				<td style="text-align: center;"><?php echo ($row['dstart'] != -1 ? ArasJoomlaVikApp::jdate($config->get('dateformat'), $row['dstart']) . ' - ' . ArasJoomlaVikApp::jdate($config->get('dateformat'), $row['dend']) : '/'); ?></td>
				
				<td style="text-align: center;">
				    <?php echo intval($row['lastminute']) > 0 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
				</td>
				
				<td style="text-align: center;">
					<a href="javascript: void(0);" class="vap-coupon-notes" title="<?php echo $tooltip; ?>">
						<i class="fa fa-sticky-note big"></i>
					</a>
				</td>
				
				<td style="text-align: center;" class="<?php echo $class; ?>"><?php echo JText::_("VAPCOUPONVALID".$valid); ?></td>
			</tr>
		
		<?php }	?>
	
	</table>
<?php } ?>

	<!-- hidden input for import tool -->
	<input type="hidden" name="import_type" value="coupons" />

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="coupons" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

		jQuery('.vap-coupon-notes').tooltip();

	});
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-type-sel').updateChosen(0);
		jQuery('#vap-value-sel').updateChosen(0);
		jQuery('#vap-status-sel').updateChosen(0);
		jQuery('#vap-group-sel').updateChosen(-1);
		
		document.adminForm.submit();
	}

</script>
