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

$filters = $this->filters;

$ordering = $this->ordering;

$vik = UIApplication::getInstance();

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

// ORDERING LINKS

foreach (array('id', 'name', 'ordering') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('customf', 'ID', 'id', $ordering['id'], 1, $filters, 'vrheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('customf', JText::_('VAPMANAGECUSTOMF1'), 'name', $ordering['name'], 1, $filters, 'vrheadcolactive'.(($ordering['name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('customf', JText::_('VAPMANAGECUSTOMF6'), 'ordering', $ordering['ordering'], 1, $filters, 'vrheadcolactive'.(($ordering['ordering'] == 2) ? 1 : 2)),
);

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
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('VAPMENUCUSTOMERS'));
		$options[] = JHtml::_('select.option', 1, JText::_('VAPMENUEMPLOYEES'));
		?>
		<div class="btn-group pull-left">
			<select name="group" id="vap-group-sel" class="<?php echo ($filters['group'] > 0 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['group']); ?>
			</select>
		</div>

		<?php
		$options = array();
		$options[] = JHtml::_('select.option', '', 			JText::_('VAPFILTERSELECTTYPE'));
		$options[] = JHtml::_('select.option', 'text', 		JText::_('VAPCUSTOMFTYPEOPTION1'));
		$options[] = JHtml::_('select.option', 'textarea', 	JText::_('VAPCUSTOMFTYPEOPTION2'));
		$options[] = JHtml::_('select.option', 'number', 	JText::_('VAPCUSTOMFTYPEOPTION8'));
		$options[] = JHtml::_('select.option', 'date', 		JText::_('VAPCUSTOMFTYPEOPTION3'));
		$options[] = JHtml::_('select.option', 'select', 	JText::_('VAPCUSTOMFTYPEOPTION4'));
		$options[] = JHtml::_('select.option', 'checkbox', 	JText::_('VAPCUSTOMFTYPEOPTION5'));
		$options[] = JHtml::_('select.option', 'file', 		JText::_('VAPCUSTOMFTYPEOPTION7'));
		$options[] = JHtml::_('select.option', 'separator', JText::_('VAPCUSTOMFTYPEOPTION6'));
		?>
		<div class="btn-group pull-left">
			<select name="type" id="vap-type-sel" class="<?php echo (!empty($filters['type']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['type']); ?>
			</select>
		</div>

		<?php if ($filters['group'] == 0) { ?>

			<?php
			$options = array();
			$options[] = JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTRULE'));
			for ($i = 0; $i <= 9; $i++)
			{
				$options[] = JHtml::_('select.option', $i, JText::_('VAPCUSTFIELDRULE' . $i));
			}
			?>
			<div class="btn-group pull-left">
				<select name="rule" id="vap-rule-sel" class="<?php echo ($filters['rule'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['rule']); ?>
				</select>
			</div>

			<?php
			$options = array();
			$options[] = JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTOWNER'));
			$options[] = JHtml::_('select.option', 0, JText::_('VAPMENUTITLEHEADER3'));
			$options[] = JHtml::_('select.option', 1, JText::_('VAPMENUEMPLOYEES'));
			$options[] = JHtml::_('select.option', 2, JText::_('VAPMENUSERVICES'));
			?>
			<div class="btn-group pull-left">
				<select name="owner" id="vap-owner-sel" class="<?php echo ($filters['owner'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['owner']); ?>
				</select>
			</div>

		<?php } ?>

		<?php
		$options = array();
		$options[] = JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTSTATUS'));
		$options[] = JHtml::_('select.option', 1, JText::_('VAPREQUIRED'));
		$options[] = JHtml::_('select.option', 0, JText::_('VAPOPTIONAL'));
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vap-status-sel" class="<?php echo ($filters['status'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status']); ?>
			</select>
		</div>

	</div>

<?php if (count($this->rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOCUSTOMF'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="100" style="text-align: left;"><?php echo JText::_('VAPMANAGECUSTOMF2'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMF3'); ?></th>
				
				<?php if ($filters['group'] == 0) { ?>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMF12'); ?></th>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMF10'); ?></th>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMENUSERVICES'); ?></th>
				<?php } ?>

				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;">
					<?php echo $links[2]; ?>
					<?php if ($core_edit) { ?>
						<a href="javascript: saveSort();" class="vaporderingsavelink">
							<i class="fa fa-floppy-o big"></i>
						</a>
					<?php } ?>
				</th>
				
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td><?php echo $row['id']; ?></td>
				
				<td><a href="index.php?option=com_vikappointments&amp;task=editcustomf&amp;cid[]=<?php echo $row['id']; ?>"><?php echo JText::_($row['name']); ?></a></td>
				
				<td><?php echo ucwords($row['type']); ?></td>
				
				<td style="text-align: center;">
					<?php if ($core_edit && $row['type'] != 'separator') {?>
						<a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=custfields&column_db=required&val=<?php echo $row['required']; ?>&id=<?php echo $row['id']; ?>&return_task=customf">
							<?php echo intval($row['required']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
						</a>
					<?php } else { ?>
						<?php echo intval($row['required']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php } ?>
				</td>

				<?php if ($filters['group'] == 0) { ?>
				
					<td style="text-align: center;">
						<?php 
						$clazz = '';
						switch ($row['rule'])
						{
							case VAPCustomFields::NOMINATIVE:
								$clazz = 'male';
								break;
							
							case VAPCustomFields::EMAIL:
								$clazz = 'envelope';
								break;

							case VAPCustomFields::PHONE_NUMBER:
								$clazz = 'phone';
								break;

							case VAPCustomFields::STATE:
								$clazz = 'map';
								break;

							case VAPCustomFields::CITY:
								$clazz = 'map-signs';
								break;

							case VAPCustomFields::ADDRESS:
								$clazz = 'road';
								break;

							case VAPCustomFields::ZIP:
								$clazz = 'map-marker';
								break;

							case VAPCustomFields::COMPANY:
								$clazz = 'building';
								break;

							case VAPCustomFields::VATNUM:
								$clazz = 'briefcase';
								break;
						}

						if (!empty($clazz))
						{
							?>
							<span style="display: inline-block;text-align: center;width: 30%;"><i class="fa fa-<?php echo $clazz; ?> big"></i></span>
							<span style="display: inline-block;text-align: left;width: 65%;"><?php echo JText::_('VAPCUSTFIELDRULE'.$row['rule']); ?></span>
							<?php
						}
						?>
					</td>
					
					<td style="text-align: center;"><?php echo strlen($row['ename']) ? $row['ename'] : JText::_('VAPMANAGECUSTOMF11'); ?></td>

					<td style="text-align: center;"><?php echo !empty($row['services_count']) ? $row['services_count'] : JText::_('VAPMANAGECUSTOMF11'); ?></td>

				<?php } ?>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
						
						<?php if ($ordering['ordering'] > 0) { ?>
							<?php if ($row['ordering'] > $this->bounds['min']) { ?>
								<a href="index.php?option=com_vikappointments&amp;task=sortfield&amp;cid[]=<?php echo $row['id']; ?>&amp;mode=up&amp;db_table=custfields&amp;return_task=customf&amp;where[group]=<?php echo $filters['group']; ?>">
									<i class="fa fa-arrow-<?php echo ($ordering['ordering'] == 2 ? 'up' : 'down'); ?>"></i>
								</a>
							<?php } else { ?>
								<i class="empty"></i>
							<?php } ?>

							<?php if ($row['ordering'] < $this->bounds['max']) { ?>
								<a href="index.php?option=com_vikappointments&amp;task=sortfield&amp;cid[]=<?php echo $row['id']; ?>&amp;mode=down&amp;db_table=custfields&amp;return_task=customf&amp;where[group]=<?php echo $filters['group']; ?>">
									<i class="fa fa-arrow-<?php echo ($ordering['ordering'] == 2 ? 'down' : 'up'); ?>"></i>
								</a>
							<?php } else { ?>
								<i class="empty"></i>
							<?php } ?>
						<?php } ?>

					<?php } ?>
					<input type="text" size="4" style="margin-bottom: 0;text-align: center;" value="<?php echo $row['ordering']; ?>" name="row_ord[<?php echo $row['id']; ?>][]"/>
				</td>
			</tr>
		<?php } ?>
		
	</table>

<?php } ?>

	<input type="hidden" name="task" value="customf" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-group-sel').updateChosen(0);
		jQuery('#vap-type-sel').updateChosen('');
		jQuery('#vap-rule-sel').updateChosen(-1);
		jQuery('#vap-owner-sel').updateChosen(-1);
		jQuery('#vap-status-sel').updateChosen(-1);
		
		document.adminForm.submit();
	}

	function saveSort() {
		jQuery('input[name=task]').val('saveCustomFieldSort');
		document.adminForm.submit();
	}

</script>
