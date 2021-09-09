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

foreach (array('id', 'name', 'class') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('cronjobs', JText::_('VAPMANAGECRONJOB1'), 'id', $ordering['id'], 1, $filters, 'vapheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('cronjobs', JText::_('VAPMANAGECRONJOB2'), 'name', $ordering['name'], 1, $filters, 'vapheadcolactive'.(($ordering['name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('cronjobs', JText::_('VAPMANAGECRONJOB3'), 'class', $ordering['class'], 1, $filters, 'vapheadcolactive'.(($ordering['class'] == 2) ? 1 : 2)),
);

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

$is_searching = $this->hasFilters();

$config = UIFactory::getConfig();

$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');
$securekey = md5($config->get('cron_secure_key'));

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
			JHtml::_('select.option', '', JText::_('VAPFILTERSELECTTYPE')),
		);
		foreach ($this->cronClasses as $file => $cron)
		{
			$options[] = JHtml::_('select.option', $file, $cron);
		}
		?>
		<div class="btn-group pull-left">
			<select name="class" id="vap-class-sel" class="<?php echo (!empty($filters['class']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['class']); ?>
			</select>
		</div>

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

	</div>

<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOCRONJOB'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECRONJOB4'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="200" style="text-align: center;"><?php echo JText::_('VAPMANAGECRONJOB8'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECRONJOB5'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECRONJOB9'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: left;"><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;">
					<?php if ($core_edit) { ?>
						<a href="index.php?option=com_vikappointments&amp;task=editcronjob&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a>
					<?php } else { ?>
						<?php echo $row['name']; ?>
					<?php } ?>
				</td>
				
				<td style="text-align: center;"><?php echo $row['class']; ?></td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
						<a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=cronjob&column_db=published&val=<?php echo $row['published']; ?>&id=<?php echo $row['id']; ?>&return_task=cronjobs">
							<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
						</a>
					<?php } else { ?>
						<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php } ?>
				</td>
				
				<td style="text-align: center;">
					<?php
					if (!empty($row['lastlog']['id']))
					{
						echo VikAppointments::formatTimestamp($dt_format, $row['lastlog']['createdon']) . 
						' - <span class="vapreservationstatus' . ($row['lastlog']['status'] ? 'confirmed' : 'removed') . '">' . 
						JText::_('VAPCRONLOGSTATUS' . ($row['lastlog']['status'] ? 'OK' : 'ERROR')) . '</span>';
					}
					else
					{
						echo "/";
					}
					?>
				</td>
				
				<td style="text-align: center;">
					<a href="index.php?option=com_vikappointments&task=cronjoblogs&id_cron=<?php echo $row['id']; ?>">
						<i class="fa fa-sticky-note big"></i>
					</a>
				</td>
				
				<td style="text-align: center;">
					<?php if ($row['published']) { ?>
						<a href="<?php echo JUri::root()."index.php?option=com_vikappointments&task=cronjob_listener_rq&tmpl=component&id_cron=".$row['id']."&secure_key=".$securekey; ?>" target="_blank">
							<i class="fa fa-rocket big"></i>
						</a>
					<?php } else { ?>
						<i class="fa fa-rocket big"></i>
					<?php } ?>
				</td>
			</tr>
		<?php }	?>

	</table>

<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="cronjobs" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-class-sel').updateChosen('');
		jQuery('#vap-status-sel').updateChosen(-1);
		
		document.adminForm.submit();
	}

</script>
