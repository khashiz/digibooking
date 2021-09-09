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

$rows   = $this->rows;
$navbut = $this->navbut;

$ordering = $this->ordering;

$filters = $this->filters;

$vik = UIApplication::getInstance();

foreach (array('m.id', 'm.name') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('mailtextcust', JText::_('VAPMANAGECUSTMAIL1'), 'm.id', $ordering['m.id'], 1, $filters, 'vapheadcolactive'.(($ordering['m.id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('mailtextcust', JText::_('VAPMANAGECUSTMAIL2'), 'm.name', $ordering['m.name'], 1, $filters, 'vapheadcolactive'.(($ordering['m.name'] == 2) ? 1 : 2)),
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
		$positions = array(
			'{custom_position_top}',
			'{custom_position_middle}',
			'{custom_position_bottom}',
			'{custom_position_footer}',
		);

		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('VAPFILTERSELECTPOSITION'));
		foreach ($positions as $p)
		{
			$options[] = JHtml::_('select.option', $p, $p);
		}
		?>
		<div class="btn-group pull-left">
			<select name="position" id="vap-position-sel" class="<?php echo (!empty($filters['position']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['position']); ?>
			</select>
		</div>

		<?php
		$statuses = array(
			'CONFIRMED',
			'PENDING',
			'REMOVED',
			'CANCELED',
		);
		
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('VAPFILTERSELECTSTATUS'));
		foreach ($statuses as $s)
		{
			$options[] = JHtml::_('select.option', $s, JText::_('VAPSTATUS' . $s));
		}
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vap-status-sel" class="<?php echo (!empty($filters['status']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status']); ?>
			</select>
		</div>

		<?php
		$all_tmpl_files = glob(VAPBASE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mail_tmpls' . DIRECTORY_SEPARATOR . '*.php');
		
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('VAPFILTERSELECTFILE'));
		foreach ($all_tmpl_files as $file)
		{
			$file = basename($file);
			$options[] = JHtml::_('select.option', $file, $file);
		}
		?>
		<div class="btn-group pull-left">
			<select name="file" id="vap-file-sel" class="<?php echo (!empty($filters['file']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['file']); ?>
			</select>
		</div>

		<?php
		$languages = VikAppointments::getKnownLanguages();
		
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('VAPFILTERSELECTLANG'));
		foreach ($languages as $lang)
		{
			$options[] = JHtml::_('select.option', $lang, $lang);
		}
		?>
		<div class="btn-group pull-left">
			<select name="tag" id="vap-tag-sel" class="<?php echo (!empty($filters['tag']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['tag']); ?>
			</select>
		</div>

	</div>

<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOCUSTMAIL');?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTMAIL3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTMAIL4'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTMAIL5'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPORDERSERVICE'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPORDEREMPLOYEE'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTMAIL6'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			
			$lang = explode('-', $row['tag']);
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td><?php echo $row['id']; ?></td>
				
				<td><a href="index.php?option=com_vikappointments&amp;task=editmailtext&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td>
				
				<td style="text-align: center;"><?php echo $row['position']; ?></td>
				
				<td style="text-align: center;" class="vapreservationstatus<?php echo strtolower($row['status']); ?>"><?php echo JText::_('VAPSTATUS'.$row['status']); ?></td>
				
				<td style="text-align: center;"><?php echo $row['file']; ?></td>

				<td style="text-align: center;"><?php echo $row['id_service'] ? $row['sname'] : '--'; ?></td>

				<td style="text-align: center;"><?php echo $row['id_employee'] ? $row['ename'] : '--'; ?></td>
				
				<td style="text-align: center;">
					<img src="<?php echo VAPASSETS_URI . 'css/flags/' . strtolower($lang[1]) . '.png'; ?>"/>
				</td>
			</tr>
		<?php } ?>

	</table>

<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="mailtextcust" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-position-sel').updateChosen('');
		jQuery('#vap-status-sel').updateChosen('');
		jQuery('#vap-file-sel').updateChosen('');
		jQuery('#vap-tag-sel').updateChosen('');
		
		document.adminForm.submit();
	}

</script>
