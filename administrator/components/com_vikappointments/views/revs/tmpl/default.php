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

$config = UIFactory::getConfig();

foreach (array('r.id', 'r.timestamp', 'r.rating', 'r.published') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('revs', JText::_('VAPMANAGEREVIEW1'), 'r.id', $ordering['r.id'], 1, $filters, 'vapheadcolactive'.(($ordering['r.id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('revs', JText::_('VAPMANAGEREVIEW4'), 'r.timestamp', $ordering['r.timestamp'], 1, $filters, 'vapheadcolactive'.(($ordering['r.timestamp'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('revs', JText::_('VAPMANAGEREVIEW5'), 'r.rating', $ordering['r.rating'], 1, $filters, 'vapheadcolactive'.(($ordering['r.rating'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('revs', JText::_('VAPMANAGEREVIEW7'), 'r.published', $ordering['r.published'], 1, $filters, 'vapheadcolactive'.(($ordering['r.published'] == 2) ? 1 : 2)),
);

$date_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

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

		<?php
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('VAPFILTERSELECTRATING'));
		for ($i = 5; $i > 0; $i--)
		{
			$options[] = JHtml::_('select.option', $i, $i . ' ' . JText::_('VAPSTAR' . ($i > 1 ? 'S' : '')));
		}
		?>
		<div class="btn-group pull-left">
			<select name="rating" id="vap-rating-sel" class="<?php echo ($filters['rating'] != 0 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['rating']); ?>
			</select>
		</div>

		<?php
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('VAPFILTERSELECTTYPE'));
		$options[] = JHtml::_('select.option', 'employee', JText::_('VAPREVIEWSFILTEROPT1'));
		$options[] = JHtml::_('select.option', 'service', JText::_('VAPREVIEWSFILTEROPT2'));
		?>
		<div class="btn-group pull-left">
			<select name="type" id="vap-type-sel" class="<?php echo (!empty($filters['type']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['type']); ?>
			</select>
		</div>

		<?php
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('VAPFILTERSELECTLANG'));
		foreach (VikAppointments::getKnownLanguages() as $lang)
		{
			$options[] = JHtml::_('select.option', $lang, $lang);
		}
		?>
		<div class="btn-group pull-left">
			<select name="lang" id="vap-lang-sel" class="<?php echo (!empty($filters['lang']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['lang']); ?>
			</select>
		</div>

	</div>
	
<?php if (count($rows) == 0) { ?>
		
	<p><?php echo JText::_('VAPNOREVIEW');?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo JText::_('VAPMANAGEREVIEW2'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEREVIEW3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEREVIEW6'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo $links[3]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo JText::_('VAPMANAGEREVIEW9'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo JText::_('VAPMANAGEREVIEW8'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			
			$country = explode('-', $row['langtag']);
			$country = $country[1];
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: left;"><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;"><a href="index.php?option=com_vikappointments&amp;task=editrev&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
				
				<td style="text-align: center;"><?php echo $row['name']; ?></td>
				
				<td style="text-align: center;"><?php echo ArasJoomlaVikApp::jdate($date_format, $row['timestamp']); ?></td>
				
				<td style="text-align: center;">
					<?php for ($j = 1; $j <= $row['rating']; $j++) { ?>
						<img src="<?php echo VAPASSETS_URI . 'css/images/rating-star.png'; ?>" style="width: 16px;height:16px;"/>
					<?php } ?>
				</td>
				
				<td style="text-align: center;"><?php echo (!empty($row['sername']) ? $row['sername'] : $row['empname']); ?></td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
					   <a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=reviews&column_db=published&val=<?php echo $row['published']; ?>&id=<?php echo $row['id']; ?>&return_task=revs">
						  <?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					   </a>
					<?php } else { ?>
						<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php } ?>
				</td>
				
				<td style="text-align: center;">
					<?php if (strlen($row['comment']) > 0) {
						$comment = $row['comment'];
						if (strlen($comment) > 1000) { 
							$comment = mb_substr($comment, 0, 800, 'UTF-8') . '...';
						}
						?>
						<img src="<?php echo VAPASSETS_ADMIN_URI . 'images/comment.png'; ?>" title="<?php echo $comment; ?>" class="vap-comment" />
					<?php } else { ?>
						<img src="<?php echo VAPASSETS_ADMIN_URI . 'images/nocomment.png'; ?>" />
					<?php } ?>
				</td>
				
				<td style="text-align: center;">
					<img src="<?php echo VAPASSETS_URI . 'css/flags/'.strtolower($country).'.png'; ?>" />
				</td>
			</tr>
		
		<?php }	?>
	
	</table>
	
<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="revs" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>
	
<script>
	
	jQuery(document).ready(function() {

		jQuery('.vap-comment').tooltip();

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-status-sel').updateChosen(-1);
		jQuery('#vap-rating-sel').updateChosen(0);
		jQuery('#vap-type-sel').updateChosen('');
		jQuery('#vap-lang-sel').updateChosen('');
		
		document.adminForm.submit();
	}
	
</script>
