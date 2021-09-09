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

foreach (array('c.country_name', 'c.phone_prefix', 'states_count') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('countries', JText::_('VAPMANAGECOUNTRY1'), 'c.country_name', $ordering['c.country_name'], 1, $filters, 'vapheadcolactive'.(($ordering['c.country_name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('countries', JText::_('VAPMANAGECOUNTRY4'), 'c.phone_prefix', $ordering['c.phone_prefix'], 1, $filters, 'vapheadcolactive'.(($ordering['c.phone_prefix'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('countries', JText::_('VAPMENUSTATES'), 'states_count', $ordering['states_count'], 1, $filters, 'vapheadcolactive'.(($ordering['states_count'] == 2) ? 1 : 2)),
);

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
		
	<p><?php echo JText::_('VAPNOCOUNTRY');?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="200" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;">&nbsp;</th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECOUNTRY2'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECOUNTRY3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo JText::_('VAPMANAGECOUNTRY5'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo JText::_('VAPMANAGECOUNTRY6'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: left;"><a href="index.php?option=com_vikappointments&amp;task=editcountry&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['country_name']; ?></a></td>

				<td style="text-align: center;">
					<a href="index.php?option=com_vikappointments&amp;task=states&amp;country=<?php echo $row['id']; ?>">
						<i class="fa fa-bars big"></i>
					</a>
				</td>
				
				<td style="text-align: center;"><?php echo $row['country_2_code']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['country_3_code']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['phone_prefix']; ?></td>
				
				<td style="text-align: center;"><?php echo ($row['states_count'] > 0 ? $row['states_count'] : ''); ?></td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
					   <a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=countries&column_db=published&val=<?php echo $row['published']; ?>&id=<?php echo $row['id']; ?>&return_task=countries">
						  <?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					   </a>
					<?php } else { ?>
						<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php } ?>
				</td>
				
				<td style="text-align: center;">
					<img src="<?php echo VAPASSETS_URI . 'css/flags/'.strtolower($row['country_2_code']).'.png'; ?>"/>
				</td>
			</tr>
		
		<?php } ?>
	
	</table>

<?php } ?>
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="countries" />
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
		
		document.adminForm.submit();
	}
	
</script>
