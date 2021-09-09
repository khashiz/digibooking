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

JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen');

$rows 	= $this->rows;
$navbut = $this->navbut;

$ordering = $this->ordering;

$filters = $this->filters;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

foreach (array('id', 'name', 'price', 'ordering') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('options', JText::_('VAPMANAGEOPTION1'), 'id', $ordering['id'], 1, $filters, 'vapheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('options', JText::_('VAPMANAGEOPTION2'), 'name', $ordering['name'], 1, $filters, 'vapheadcolactive'.(($ordering['name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('options', JText::_('VAPMANAGEOPTION4'), 'price', $ordering['price'], 1, $filters, 'vapheadcolactive'.(($ordering['price'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('options', JText::_('VAPMANAGEOPTION10'), 'ordering', $ordering['ordering'], 1, $filters, 'vapheadcolactive'.(($ordering['ordering'] == 2) ? 1 : 2)),
);

$curr_symb 	= $config->get('currencysymb');
$symb_pos 	= $config->get('currsymbpos');

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

	</div>

	<div class="btn-toolbar" id="vap-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<?php
		$options = array(
			JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTSTATUS')),
			JHtml::_('select.option', 1, JText::_('VAPMANAGESERVICE6')),
			JHtml::_('select.option', 0, JText::_('VAPMANAGECRONJOB10')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vap-status-sel" class="<?php echo ($filters['status'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status']); ?>
			</select>
		</div>

		<?php
		$options = array(
			JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTTYPE')),
			JHtml::_('select.option', 1, JText::_('VAPMANAGEOPTION12')),
			JHtml::_('select.option', 0, JText::_('VAPMANAGECONFIG43')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="type" id="vap-type-sel" class="<?php echo ($filters['type'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['type']); ?>
			</select>
		</div>

	</div>

<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOOPTION');?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGESERVICE6'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEOPTION5'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEOPTION12'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEOPTION9'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;">
					<?php echo $links[3]; ?>
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
			
			$icon_type = 1;
			if (empty($row['image']))
			{
				$icon_type = 2; // icon not uploaded
			}
			else if (!file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $row['image']))
			{
				$icon_type = 0;
			}
			
			$img_title = JText::_('VAPIMAGESTATUS' . $icon_type);			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;"><a href="index.php?option=com_vikappointments&amp;task=editoption&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td>
				
				<td style="text-align: center;"><?php echo VikAppointments::printPriceCurrencySymb($row['price'], $curr_symb, $symb_pos); ?></td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
					   <a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=option&column_db=published&val=<?php echo $row['published']; ?>&id=<?php echo $row['id']; ?>&return_task=options">
						  <?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					   </a>
					<?php } else { ?>
						<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php }?>
				</td>

				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
					   <a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=option&column_db=single&val=<?php echo $row['single']; ?>&id=<?php echo $row['id']; ?>&return_task=options">
						  <?php echo intval($row['single']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					   </a>
					<?php } else { ?>
						<?php echo intval($row['single']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php }?>
				</td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
					   <a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=option&column_db=required&val=<?php echo $row['required']; ?>&id=<?php echo $row['id']; ?>&return_task=options">
						  <?php echo intval($row['required']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					   </a>
					<?php } else { ?>
						<?php echo intval($row['required']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php }?>
				</td>
				
				<td style="text-align: center;">
					<?php if ($icon_type == 1) { ?>
						<a href="<?php echo VAPMEDIA_URI . $row['image']; ?>" class="modal" target="_blank">
							<img src="<?php echo VAPASSETS_ADMIN_URI . "images/imagepreview.png"; ?>" title="<?php echo $img_title ?>"/>
						</a>
					<?php } else if ($icon_type == 0) { ?>
						<img src="<?php echo VAPASSETS_ADMIN_URI . "images/imagenotfound.png"; ?>" title="<?php echo $img_title ?>"/>
					<?php } else { ?>
						<img src="<?php echo VAPASSETS_ADMIN_URI . "images/imageno.png"; ?>" title="<?php echo $img_title ?>"/>
					<?php } ?>
				</td>

				<td style="text-align: center;">
					<?php if ($core_edit) { ?>

						<?php if ($ordering['ordering'] > 0) { ?>
							<?php if ($row['ordering'] > $this->bounds['min']) { ?>
								<a href="index.php?option=com_vikappointments&amp;task=sortfield&amp;cid[]=<?php echo $row['id']; ?>&amp;mode=up&amp;db_table=option&amp;return_task=options">
									<i class="fa fa-arrow-<?php echo ($ordering['ordering'] == 2 ? 'up' : 'down'); ?>"></i>
								</a>
							<?php } else { ?>
								<i class="empty"></i>
							<?php } ?>

							<?php if ($row['ordering'] < $this->bounds['max']) { ?>
								<a href="index.php?option=com_vikappointments&amp;task=sortfield&amp;cid[]=<?php echo $row['id']; ?>&amp;mode=down&amp;db_table=option&amp;return_task=options">
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

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="options" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function saveSort() {
		jQuery('input[name=task]').val('saveOptionSort');
		document.adminForm.submit();
	}
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-type-sel').updateChosen(-1);
		jQuery('#vap-status-sel').updateChosen(-1);
		
		document.adminForm.submit();
	}
	
</script>
