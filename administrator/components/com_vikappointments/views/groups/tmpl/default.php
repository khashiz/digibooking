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

$rows 	= $this->rows;
$navbut = $this->navbut;

$ordering = $this->ordering;

$filters = $this->filters;

$vik = UIApplication::getInstance();

foreach (array('id', 'name', 'ordering') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('groups', JText::_('VAPMANAGEGROUP1'), 'id', $ordering['id'], 1, $filters, 'vapheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('groups', JText::_('VAPMANAGEGROUP2'), 'name', $ordering['name'], 1, $filters, 'vapheadcolactive'.(($ordering['name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('groups', JText::_('VAPMANAGECUSTOMF6'), 'ordering', $ordering['ordering'], 1, $filters, 'vapheadcolactive'.(($ordering['ordering'] == 2) ? 1 : 2)),
);

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">
	
	<div class="btn-toolbar vapgroup-keyfilter-block" id="filter-bar">

		<div class="btn-group pull-left input-append">
			<input type="text" name="keysearch" id="vapkeysearch" class="vapkeysearch" size="32" 
				value="<?php echo $filters['keysearch']; ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />

			<button type="submit" class="btn">
				<i class="icon-search"></i>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>

		<div class="btn-group pull-right">
			<button type="button" class="btn" onClick="switchGroupView(<?php echo $this->pageType; ?>);">
				<?php echo JText::_('VAPGROUPSWITCHPAGE' . $this->pageType); ?>
			</button>
		</div>

	</div>
	
<?php if (count($rows) == 0) { ?>

	<p><?php echo JText::_('VAPNOGROUP');?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="300" style="text-align: center;"><?php echo JText::_('VAPMANAGEGROUP3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_($this->pageType == 1 ? 'VAPMANAGEGROUP4' : 'VAPMANAGEGROUP5'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="90" style="text-align: center;">
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
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			
			$gdesc = $row['description'];
			if (strlen($gdesc) > 512)
			{
				$gdesc = mb_substr(strip_tags($gdesc), 0, 512, 'UTF-8').'...';
			}
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;">
					<a href="index.php?option=com_vikappointments&amp;task=edit<?php echo ($this->pageType == 1 ? '' : 'emp'); ?>group&amp;cid[]=<?php echo $row['id']; ?>">
						<?php echo $row['name']; ?>
					</a>
				</td>
				
				<td style="text-align: left;"><?php echo $gdesc; ?></td>
				
				<td style="text-align: center;"><?php echo (empty($row['child_id']) ? 0 : $row['count']); ?></td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
						
						<?php if ($ordering['ordering'] > 0) { ?>
							<?php if ($row['ordering'] > $this->bounds['min']) { ?>
								<a href="index.php?option=com_vikappointments&amp;task=sortfield&amp;cid[]=<?php echo $row['id']; ?>&amp;mode=up&amp;db_table=<?php echo ($this->pageType == 1 ? '' : 'employee_'); ?>group&amp;return_task=groups">
									<i class="fa fa-arrow-<?php echo ($ordering['ordering'] == 2 ? 'up' : 'down'); ?>"></i>
								</a>
							<?php } else { ?>
								<i class="empty"></i>
							<?php } ?>

							<?php if ($row['ordering'] < $this->bounds['max']) { ?>
								<a href="index.php?option=com_vikappointments&amp;task=sortfield&amp;cid[]=<?php echo $row['id']; ?>&amp;mode=down&amp;db_table=<?php echo ($this->pageType == 1 ? '' : 'employee_'); ?>group&amp;return_task=groups">
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
		<?php }	?>

	</table>
<?php } ?>
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="groups"/>
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<script>
	
	function saveSort() {
		jQuery('input[name=task]').val('save<?php echo ($this->pageType == 1 ? '' : 'Employee'); ?>GroupSort');
		document.adminForm.submit();
	}
	
	function switchGroupView(page) {
		document.location.href = "index.php?option=com_vikappointments&task=switchgroup&pagegroup=<?php echo $this->pageType; ?>";
	}

	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		
		document.adminForm.submit();
	}
	
</script>
