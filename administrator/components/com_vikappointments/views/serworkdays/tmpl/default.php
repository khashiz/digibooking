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

$filters = $this->filters;

$employees = $this->employees;

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$vik = UIApplication::getInstance();

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">
	
	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left">
			<?php
			$options = array();
			foreach ($employees as $e)
			{
				$options[] = JHtml::_('select.option', $e['id'], $e['firstname'] . ' ' . $e['lastname']);
			}
			?>
			<select name="id_emp" id="vap-employee-sel" class="active" onChange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $this->idEmployee); ?>
			</select>
		</div>

		<div class="btn-group pull-left">
			<?php
			$options = array();
			$options[] = JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTTYPE'));
			$options[] = JHtml::_('select.option', 1, JText::_('VAPWDLEGENDLABEL1'));
			$options[] = JHtml::_('select.option', 2, JText::_('VAPWDLEGENDLABEL2'));
			?>
			<select name="type" id="vap-type-sel" class="<?php echo ($filters['type'] != -1 ? 'active' : ''); ?>" onChange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['type']); ?>
			</select>
		</div>

		<div class="btn-group pull-left">
			<?php
			$options = array();
			$options[] = JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTSTATUS'));
			$options[] = JHtml::_('select.option', 1, JText::_('VAPMANAGEWD5'));
			$options[] = JHtml::_('select.option', 0, JText::_('VAPMANAGEEMPLOYEE22'));
			?>
			<select name="status" id="vap-status-sel" class="<?php echo ($filters['status'] != -1 ? 'active' : ''); ?>" onChange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status']); ?>
			</select>
		</div>

	</div>
	
<?php  if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOWORKINGDAYS'); ?></p>
	<p><?php echo JText::sprintf('VAPSERWDLINKTO', 'index.php?option=com_vikappointments&task=editemployee&cid[]='.$this->idEmployee); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="5%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="30%" style="text-align: left;"><?php echo JText::_('VAPMANAGEWD2'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWD3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWD4'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWD5'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php for ($i = 0, $n = count($rows); $i < $n; $i++) {
			
			$row = $rows[$i];
			
			if ($row['ts'] == -1)
			{
				$day = JText::_('VAPDAY' . ($row['day'] > 0 ? $row['day'] : 7));
			}
			else
			{
				$day = JDate::getInstance($row['ts'])->format('D d F, Y');
			}
			
			$from 	= date($time_format, mktime(floor($row['fromts'] / 60), $row['fromts'] % 60, 0, 1, 1, 2000));
			$to 	= date($time_format, mktime(floor($row['endts'] / 60), $row['endts'] % 60, 0, 1, 1, 2000));
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td><?php echo $day; ?></td>
				
				<td style="text-align: center;"><?php echo ($row['closed'] ? '/' : $from); ?></td>
				
				<td style="text-align: center;"><?php echo ($row['closed'] ? '/' : $to); ?></td>
				
				<td style="text-align: center;">
					<?php echo intval($row['closed']) == 0 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "css/images/disabled.png\"/>"; ?>
				</td>
			</tr>
		
		<?php } ?>
		
	</table>
<?php } ?>

	<input type="hidden" name="task" value="serworkdays" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="id" value="<?php echo $this->idService; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});

</script>
