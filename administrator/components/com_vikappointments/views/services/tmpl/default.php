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

foreach (array('s.id', 's.name', 's.duration', 's.price', 's.published', 'g.name', 's.ordering') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('services', JText::_('VAPMANAGESERVICE1'), 's.id', $ordering['s.id'], 1, $filters, 'vapheadcolactive'.(($ordering['s.id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('services', JText::_('VAPMANAGESERVICE2'), 's.name', $ordering['s.name'], 1, $filters, 'vapheadcolactive'.(($ordering['s.name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('services', JText::_('VAPMANAGESERVICE4'), 's.duration', $ordering['s.duration'], 1, $filters, 'vapheadcolactive'.(($ordering['s.duration'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('services', JText::_('VAPMANAGESERVICE5'), 's.price', $ordering['s.price'], 1, $filters, 'vapheadcolactive'.(($ordering['s.price'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('services', JText::_('VAPMANAGESERVICE6'), 's.published', $ordering['s.published'], 1, $filters, 'vapheadcolactive'.(($ordering['s.published'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('services', JText::_('VAPMANAGESERVICE10'), 'g.name', $ordering['g.name'], 1, $filters, 'vapheadcolactive'.(($ordering['g.name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('services', JText::_('VAPMANAGECUSTOMF6'), 's.ordering', $ordering['s.ordering'], 1, $filters, 'vapheadcolactive'.(($ordering['s.ordering'] == 2) ? 1 : 2)),
);

$format_dur = VikAppointments::isDurationToFormat(true);

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
			<button type="button" class="btn" onclick="document.location.href='index.php?option=com_vikappointments&amp;task=rates';">
				<?php echo JText::_('VAPMANAGESPECIALRATES'); ?>
			</button>
		</div>

	</div>

	<div class="btn-toolbar" id="vap-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<?php
		$options = array(
			JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTSTATUS')),
			JHtml::_('select.option', 1, JText::_('JPUBLISHED')),
			JHtml::_('select.option', 0, JText::_('JUNPUBLISHED')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vap-status-sel" class="<?php echo ($filters['status'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status']); ?>
			</select>
		</div>

		<?php
		$options = array(
			JHtml::_('select.option', 0, JText::_('VAPFILTERSELECTGROUP')),
			JHtml::_('select.option', -1, JText::_('VAPSERVICENOGROUP')),
		);
		foreach ($this->groups as $g)
		{
			$options[] = JHtml::_('select.option', $g['id'], $g['name']);
		}
		?>
		<div class="btn-group pull-left">
			<select name="id_group" id="vap-group-sel" class="<?php echo (!empty($filters['id_group']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['id_group']); ?>
			</select>
		</div>

	</div>
	
<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOSERVICE'); ?></p>
	
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
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[3]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGESERVICE21'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="70" style="text-align: center;"><?php echo $links[4]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[5]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="70" style="text-align: center;"><?php echo JText::_('VAPMANAGESERVICE15'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="70" style="text-align: center;"><?php echo JText::_('VAPMANAGESERVICE25'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="70" style="text-align: center;"><?php echo JText::_('VAPMANAGESERVICE9'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="90" style="text-align: center;">
					<?php echo $links[6]; ?>
					<?php if ($core_edit) { ?>
						<a href="javascript: saveSort();" class="vaporderingsavelink">
							<i class="fa fa-floppy-o big"></i>
						</a>
					<?php } ?>
				</th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			
			$icon_type = 1;

			if (empty($row['image']))
			{
				$icon_type = 2; // icon not uploaded
			}
			else if (!file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $row['image']))
			{
				$icon_type = 0; // missing icon
			}
			
			$img_title = JText::_('VAPIMAGESTATUS' . $icon_type);
			
			$capacity_text = '/';
			if ($row['max_capacity'] > 1)
			{
				$capacity_text = '<strong>'.$row['max_capacity'].'</strong> ('.$row['min_per_res'].'-'.$row['max_per_res'].')';
			}
			
			$_sleep_text = '';
			if ($row['sleep'] != 0)
			{
				if ($row['sleep'] > 0)
				{
					$_sleep_text = ' (+'.VikAppointments::formatMinutesToTime($row['sleep'], $format_dur).')';
				}
				else
				{
					$_sleep_text = ' ('.VikAppointments::formatMinutesToTime($row['sleep'], $format_dur).')';
				}
			}
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">

				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;"><a href="index.php?option=com_vikappointments&amp;task=editservice&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td>
				
				<td style="text-align: center;">
					<?php echo VikAppointments::formatMinutesToTime($row['duration'], $format_dur) . $_sleep_text; ?>
				</td>
				
				<td style="text-align: center;"><?php echo VikAppointments::printPriceCurrencySymb($row['price']); ?></td>
				
				<td style="text-align: center;"><?php echo $capacity_text; ?></td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
					   <a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=service&column_db=published&val=<?php echo $row['published']; ?>&id=<?php echo $row['id']; ?>&return_task=services">
						  <?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					   </a>
					<?php } else { ?>
						<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php } ?>
				</td>
				
				<td style="text-align: center;">
					<?php if (!empty($row['group_name'])) { ?>
						<a href="index.php?option=com_vikappointments&task=editgroup&from=services&cid[]=<?php echo $row['id_group']; ?>"><?php echo $row['group_name']; ?></a>
					<?php } else { ?>
						/
					<?php } ?>
				</td>
				
				<td style="text-align: center;">
					<a href="javascript: void(0);" onclick="SELECTED_SERVICE=<?php echo $row['id']; ?>;SERVICE_NAME='<?php echo addslashes($row['name']); ?>';vapOpenJModal('serviceinfo', null, true); return false;">
						<i class="fa fa-search big"></i>
					</a>
				</td>
				
				<td style="text-align: center;">
					<a href="index.php?option=com_vikappointments&task=serworkdays&id=<?php echo $row['id']; ?>">
						<i class="fa fa-calendar big"></i>
					</a>
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

						<?php if ($ordering['s.ordering'] > 0) { ?>
							<?php if ($row['ordering'] > $this->bounds['min']) { ?>
								<a href="index.php?option=com_vikappointments&amp;task=sortfield&amp;cid[]=<?php echo $row['id']; ?>&amp;mode=up&amp;db_table=service&amp;return_task=services">
									<i class="fa fa-arrow-<?php echo ($ordering['s.ordering'] == 2 ? 'up' : 'down'); ?>"></i>
								</a>
							<?php } else { ?>
								<i class="empty"></i>
							<?php } ?>

							<?php if ($row['ordering'] < $this->bounds['max']) { ?>
								<a href="index.php?option=com_vikappointments&amp;task=sortfield&amp;cid[]=<?php echo $row['id']; ?>&amp;mode=down&amp;db_table=service&amp;return_task=services">
									<i class="fa fa-arrow-<?php echo ($ordering['s.ordering'] == 2 ? 'down' : 'up'); ?>"></i>
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
	<input type="hidden" name="task" value="services" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-serviceinfo',
	array(
		'title'       => '',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function saveSort() {
		jQuery('input[name=task]').val('saveServiceSort');
		document.adminForm.submit();
	}
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-status-sel').updateChosen(-1);
		jQuery('#vap-group-sel').updateChosen(0);
		
		document.adminForm.submit();
	}

	// modal

	SELECTED_SERVICE = -1;
	SERVICE_NAME = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'serviceinfo') {
			url = 'index.php?option=com_vikappointments&task=serviceinfo&tmpl=component&id=' + SELECTED_SERVICE;

			jQuery('#jmodal-serviceinfo .modal-header h3').html(SERVICE_NAME);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}
	
</script>
