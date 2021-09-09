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

/**
 * It seems that Joomla stopped loading JS core for
 * the views loaded with tmpl component. We need to force
 * it to let the pagination accessing Joomla object.
 *
 * @since Joomla 3.8.7
 */
JHtml::_('behavior.core');
JHtml::_('formbehavior.chosen');

$rows   = $this->rows;
$navbut = $this->navbut;

$ordering = $this->ordering;

$filters = $this->filters;

$vik = UIApplication::getInstance();

foreach (array('id', 'lastname', 'active_since', 'active_to') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('subscremp', JText::_('VAPMANAGEEMPLOYEE1'), 'id', $ordering['id'], 1, $filters, 'vapheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('subscremp', JText::_('VAPMANAGEEMPLOYEE4'), 'lastname', $ordering['lastname'], 1, $filters, 'vapheadcolactive'.(($ordering['lastname'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('subscremp', JText::_('VAPMANAGEEMPLOYEE28'), 'active_since', $ordering['active_since'], 1, $filters, 'vapheadcolactive'.(($ordering['active_since'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('subscremp', JText::_('VAPMANAGEEMPLOYEE27'), 'active_to', $ordering['active_to'], 1, $filters, 'vapheadcolactive'.(($ordering['active_to'] == 2) ? 1 : 2)),
);

$config = UIFactory::getConfig();

$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

$lifetime_label = JText::_('VAPSUBSCRTYPE5');

$is_searching = $this->hasFilters();

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">
	
	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left input-append">
			<input type="text" name="keysearch" id="vapkeysearch" class="vapkeysearch" size="32" 
				value="<?php echo $filters['keysearch']; ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />

			<button type="submit" class="btn">
				<i class="icon-search"></i>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn <?php echo ($is_searching ? 'btn-primary' : ''); ?>" onclick="vapToggleSearchToolsButton(this, 'frame');">
				<?php echo JText::_('JSEARCH_TOOLS'); ?>&nbsp;<i class="fa fa-caret-<?php echo ($is_searching ? 'up' : 'down'); ?>" id="vap-tools-caret-frame"></i>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>

	</div>
	
	<div class="btn-toolbar" id="vap-search-tools-frame" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<?php
		$options = array();
		$options[] = JHtml::_('select.option', -1, 'VAPFILTERSELECTSTATUS');
		$options[] = JHtml::_('select.option', 1, 'JPUBLISHED');
		$options[] = JHtml::_('select.option', 0, 'JUNPUBLISHED');
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vap-status-sel" class="<?php echo ($filters['status'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status'], true); ?>
			</select>
		</div>

		<?php
		$options = array();
		$options[] = JHtml::_('select.option', '', 'VAPFILTERSELECTTYPE');
		$options[] = JHtml::_('select.option', 'active', 'VAPACTIVE');
		$options[] = JHtml::_('select.option', 'pending', 'VAPSTATUSPENDING');
		$options[] = JHtml::_('select.option', 'expired', 'VAPEXPIRED');
		$options[] = JHtml::_('select.option', 'lifetime', 'VAPSUBSCRTYPE5');
		?>
		<div class="btn-group pull-left">
			<select name="type" id="vap-type-sel" class="<?php echo (!empty($filters['type']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['type'], true); ?>
			</select>
		</div>

	</div>
	
<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOEMPLOYEE'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="75" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGEEMPLOYEE19'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGEEMPLOYEE18'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[3]; ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			
			$now = time();
			$active_to_text  = "";
			$active_to_class = "";
			
			if ($row['active_to'] == -1)
			{
				$active_to_text  = $lifetime_label;
				$active_to_class = 'vapreservationstatusconfirmed'; 
			}
			else if ($row['active_to'] == 0)
			{
				$active_to_text  = '--';
				$active_to_class = 'vapreservationstatuspending';
			}
			else
			{
				$active_to_text = date($dt_format, $row['active_to']);
				
				if ($row['active_to'] < $now)
				{
					$active_to_class = 'vapreservationstatusremoved';
				}
				else if ($row['active_to'] < $now + 86400 * 7)
				{
					$active_to_class = 'vapreservationstatuspending';
				}
				else
				{
					$active_to_class = 'vapreservationstatusconfirmed';
				}
			}
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: left;"><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;"><?php echo $row['lastname'].' '.$row['firstname']; ?></td>
				
				<td style="text-align: center;">#<?php echo $row['jid']; ?></td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) {?>
						<a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=employee&column_db=listable&val=<?php echo $row['listable']; ?>&id=<?php echo $row['id']; ?>&return_task=subscremp&params[tmpl]=component">
							<img src="<?php echo intval($row['listable']) == 1 ? VAPASSETS_ADMIN_URI . "images/ok.png" : VAPASSETS_ADMIN_URI . "images/no.png"; ?>"/>
						</a>
					<?php } else { ?>
						<img src="<?php echo intval($row['listable']) == 1 ? VAPASSETS_ADMIN_URI . "images/ok.png" : VAPASSETS_ADMIN_URI . "images/no.png"; ?>"/>
					<?php } ?>
				</td>
				
				<td style="text-align: center;"><?php echo ($row['active_since'] > 0 ? date($dt_format, $row['active_since']) : '--'); ?></td>
				
				<td style="text-align: center;" class="<?php echo $active_to_class; ?>">
					<?php echo $active_to_text; ?>
				</td>
			</tr>
		
		<?php } ?>
	
	</table>
	
<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="subscremp" />
	<input type="hidden" name="tmpl" value="component" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<script>
	
	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
		
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-status-sel').updateChosen(-1);
		jQuery('#vap-type-sel').updateChosen('');
		
		document.adminForm.submit();
	}

</script>
