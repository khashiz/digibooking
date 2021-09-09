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

foreach (array('w.timestamp', 'w.created_on') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('waitinglist', JText::_('VAPMANAGEWAITLIST3'), 'w.timestamp', $ordering['w.timestamp'], 1, $filters, 'vapheadcolactive'.(($ordering['w.timestamp'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('waitinglist', JText::_('VAPMANAGEWAITLIST7'), 'w.created_on', $ordering['w.created_on'], 1, $filters, 'vapheadcolactive'.(($ordering['w.created_on'] == 2) ? 1 : 2)),
);

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$today = $this->today;

$tomorrow = getdate($today);
$tomorrow = mktime(0, 0, 0, $tomorrow['mon'], $tomorrow['mday'] + 1, $tomorrow['year']);

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
			JHtml::_('select.option', 0, JText::_('VAPFILTERSELECTSTATUS')),
			JHtml::_('select.option', -1, JText::_('JTRASHED')),
			JHtml::_('select.option', 1, JText::_('JALL')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vap-status-sel" class="<?php echo ($filters['status'] != 0 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status']); ?>
			</select>
		</div>

	</div>

<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOWAITLIST'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="100" style="text-align: left;"><?php echo JText::_('VAPMANAGEWAITLIST1'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST2'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST4'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST5'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST6'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[1]; ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: left;"><a href="index.php?option=com_vikappointments&amp;task=editwaiting&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['service_name']; ?></a></td>
				
				<td style="text-align: center;"><?php echo (empty($row['employee_name']) ? '/' : $row['employee_name']); ?></td>
				
				<td style="text-align: center;">
					<?php
					if ($today == $row['timestamp'])
					{
						echo JText::_('VAPTODAY');
					}
					else if ($tomorrow == $row['timestamp'])
					{
						echo JText::_('VAPTOMORROW');
					}
					else
					{
						echo date($date_format, $row['timestamp']); 
					}
					?>
				</td>
				
				<td style="text-align: center;"><?php echo $row['username']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['email']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['phone_prefix'] . ' ' . $row['phone_number']; ?></td>
				
				<td style="text-align: center;"><?php echo VikAppointments::formatTimestamp($date_format . ' ' . $time_format, $row['created_on']); ?></td>
			</tr>
		<?php } ?>

	</table>

<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="waitinglist" />
	<?php echo JHtml::_( 'form.token' ); ?>
	<?php echo $navbut; ?>
</form>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-status-sel').updateChosen(0);
		
		document.adminForm.submit();
	}

</script>
