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

foreach (array('id', 'title', 'page', 'type', 'createdon') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('conversions', JText::_('VAPMANAGESERVICE1'), 'id', $ordering['id'], 1, $filters, 'vapheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('conversions', JText::_('VAPMANAGEGROUP2'), 'title', $ordering['title'], 1, $filters, 'vapheadcolactive'.(($ordering['title'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('conversions', JText::_('VAPPAGE'), 'page', $ordering['page'], 1, $filters, 'vapheadcolactive'.(($ordering['page'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('conversions', JText::_('VAPMANAGECUSTOMF2'), 'type', $ordering['type'], 1, $filters, 'vapheadcolactive'.(($ordering['type'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('conversions', JText::_('VAPMANAGERESERVATION37'), 'createdon', $ordering['createdon'], 1, $filters, 'vapheadcolactive'.(($ordering['createdon'] == 2) ? 1 : 2)),
);

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

$is_searching = $this->hasFilters();

$config = UIFactory::getConfig();

$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

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

<?php if (count($this->rows) == 0) { ?>
	
	<p><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION12'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPCUSTOMFTYPEOPTION7'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPCODESNIPPET'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[3]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPMANAGEPAYMENT3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[4]; ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];

			$statuses = (array) json_decode($row['statuses']);

			if (count($statuses))
			{
				$statuses = array_map(function($val)
				{
					return JText::_('VAPSTATUS' . strtoupper($val));
				}, $statuses);

				$statuses = implode(', ', $statuses);
			}
			else
			{
				$statuses = '*';
			}

			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td><?php echo $row['id']; ?></td>

				<td>
					<?php if ($core_edit) { ?>
						<a href="index.php?option=com_vikappointments&amp;task=editconversion&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a>
					<?php } else { ?>
						<?php echo $row['title']; ?>
					<?php } ?>
				</td>

				<td style="text-align: center;"><?php echo $statuses; ?></td>

				<td style="text-align: center;">
					<?php
					if ($row['jsfile'])
					{
						?>
						<i class="fa fa-file big conv-tooltip" title="<?php echo $row['jsfile']; ?>"></i>
						<?php
					}
					?>
				</td>

				<td style="text-align: center;">
					<?php
					if ($row['snippet'])
					{
						?>
						<i class="fa fa-code big conv-tooltip" title="<?php echo htmlentities($row['snippet']); ?>"></i>
						<?php
					}
					?>
				</td>

				<td style="text-align: center;">
					<?php echo ucwords(str_replace('_', ' ', $row['page'])); ?>
				</td>

				<td style="text-align: center;">
					<?php echo ucwords(str_replace('_', ' ', $row['type'])); ?>
				</td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
						<a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=conversion&column_db=published&val=<?php echo $row['published']; ?>&id=<?php echo $row['id']; ?>&return_task=conversions">
							<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
						</a>
					<?php } else { ?>
						<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php } ?>
				</td>

				<td style="text-align: center;">
					<span class="conv-tooltip" title="<?php echo JHtml::_('date', $row['createdon'], $dt_format); ?>">
						<?php echo JHtml::_('date.relative', $row['createdon'], null, null, $dt_format); ?>
					</span>
				</td>
			</tr>  
		<?php } ?>
		
	</table>
	
<?php } ?>

	<input type="hidden" name="task" value="conversions" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>
	
<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

		jQuery('.conv-tooltip').tooltip();

	});
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-status-sel').updateChosen(-1);
		
		document.adminForm.submit();
	}

</script>
