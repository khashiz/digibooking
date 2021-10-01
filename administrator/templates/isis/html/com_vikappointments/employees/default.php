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

foreach (array('id', 'nickname', 'id_group', 'listable') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('employees', JText::_('VAPMANAGEEMPLOYEE1'), 'id', $ordering['id'], 1, $filters, 'vapheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('employees', JText::_('VAPMANAGEEMPLOYEE4'), 'nickname', $ordering['nickname'], 1, $filters, 'vapheadcolactive'.(($ordering['nickname'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('employees', JText::_('VAPMANAGEEMPLOYEE26'), 'id_group', $ordering['id_group'], 1, $filters, 'vapheadcolactive'.(($ordering['id_group'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('employees', JText::_('VAPMANAGEEMPLOYEE18'), 'listable', $ordering['listable'], 1, $filters, 'vapheadcolactive'.(($ordering['listable'] == 2) ? 1 : 2)),
);

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

$lifetime_label = JText::_('VAPSUBSCRTYPE5');

$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

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
			JHtml::_('select.option', 0, JText::_('VAPFILTERSELECTGROUP')),
			JHtml::_('select.option', -1, JText::_('VAPSERVICENOGROUP')),
		);
		foreach ($this->groups as $g)
		{
			$options[] = JHtml::_('select.option', $g['id'], $g['name']);
		}
		?>
		<div class="btn-group pull-left vap-setfont">
			<select name="id_group" id="vap-group-sel" class="<?php echo (!empty($filters['id_group']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['id_group']); ?>
			</select>
		</div>

	</div>
	
<?php if (count($rows) == 0) { ?>

	<p><?php echo JText::_('VAPNOEMPLOYEE');?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">

		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: center;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: center;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGEEMPLOYEE8'); ?></th>

                <?php /* ?>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGEEMPLOYEE10'); ?></th>
                <?php */ ?>

				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[2]; ?></th>
                <?php /* ?>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEEMPLOYEE23'); ?>ttttt</th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEEMPLOYEE21'); ?>gggg</th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEEMPLOYEE29'); ?>gtgtg</th>
                <?php */ ?>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[3]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGEEMPLOYEE7'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
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
			
			$listable_title = $lifetime_label;
			if ($row['active_to'] > 0)
			{
				$listable_title = date($dt_format, $row['active_to']);
			}
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: center;"><?php echo $row['id']; ?></td>
				
				<td style="text-align: center;"><a href="index.php?option=com_vikappointments&amp;task=editemployee&amp;cid[]=<?php echo $row['id']; ?>"><?php echo JTEXT::_('FLOOR'.$row['nickname']); ?></a></td>
				
				<td style="text-align: center;"><?php echo $row['email']; ?></td>

                <?php /* ?>
				<td style="text-align: center;"><?php echo $row['phone']; ?></td>
                <?php */ ?>

				<td style="text-align: center;">
					<?php if (!empty($row['gname'])) { ?>
                        <?php /* ?>
                        <a href="index.php?option=com_vikappointments&task=editempgroup&from=employees&cid[]=<?php echo $row['id_group']; ?>"><?php echo $row['gname']; ?></a>
                        <?php */ ?>
                        <?php echo JText::sprintf($row['gname']); ?>
					<?php } else { ?>
						/
					<?php } ?>
				</td>

                <?php /* ?>
				<td style="text-align: center;">
					<a href="index.php?option=com_vikappointments&task=emprates&id_emp=<?php echo $row['id']; ?>">
						<i class="fa fa-usd big"></i>
					</a>
				</td>
				
				<td style="text-align: center;">
					<a href="index.php?option=com_vikappointments&task=emppayments&id_emp=<?php echo $row['id']; ?>">
						<i class="fa fa-credit-card big"></i>
					</a>
				</td>
				
				<td style="text-align: center;">
					<a href="index.php?option=com_vikappointments&task=emplocations&id_emp=<?php echo $row['id']; ?>">
						<i class="fa fa-globe big"></i>
					</a>
				</td>
                <?php */ ?>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
						<a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=employee&column_db=listable&val=<?php echo $row['listable']; ?>&id=<?php echo $row['id']; ?>&return_task=employees">
							<img src="<?php echo intval($row['listable']) == 1 ? VAPASSETS_ADMIN_URI . "images/ok.png" : VAPASSETS_ADMIN_URI . "images/no.png"; ?>"/>
						</a>
					<?php } else { ?>
						<img src="<?php echo intval($row['listable']) == 1 ? VAPASSETS_ADMIN_URI . "images/ok.png" : VAPASSETS_ADMIN_URI . "images/no.png"; ?>"/>
					<?php } ?>
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
			</tr>
			
		<?php }	?>

	</table>

<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="employees" />
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
		
		document.adminForm.submit();
	}
	
</script>
