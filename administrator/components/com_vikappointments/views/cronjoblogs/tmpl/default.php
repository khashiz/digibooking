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

$rows 	= $this->rows;
$navbut = $this->navbut;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$dt_format = $config->get('dateformat') . ' @ ' . $config->get('timeformat') . ':s';

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

<?php if(count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOCRONJOBLOG'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo JText::_('VAPMANAGECRONJOBLOG1'); ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo JText::_('VAPMANAGECRONJOBLOG2'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="250" style="text-align: center;"><?php echo JText::_('VAPMANAGECRONJOBLOG3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGECRONJOBLOG4'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGECRONJOBLOG5'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];

			$content = strip_tags($row['content']);
			if (strlen($content) > 300)
			{
				$content = substr($content, 0, 250) . "...";
			}
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: left;"><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;">
					<a href="javascript: void(0);" onClick="LOG_ID_CLICKED=<?php echo $row['id']; ?>;vapOpenJModal('loginfo', null, true);" 
						title="<?php echo date($dt_format, $row['createdon']); ?>">
						<?php echo VikAppointments::formatTimestamp($dt_format, $row['createdon']); ?>
					</a>
				</td>
				
				<td style="text-align: left;"><?php echo $content; ?></td>
				
				<td style="text-align: center;">
					<?php echo '<span class="vapreservationstatus'.(($row['status']) ? 'confirmed' : 'removed').'">'.
						JText::_('VAPCRONLOGSTATUS'.($row['status'] ? 'OK' : 'ERROR')).
					'</span>'; ?>
				</td>
				
				<td style="text-align: center;">
					<?php echo intval($row['mailed']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
				</td>
			</tr>
		<?php } ?>

	</table>

<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="cronjoblogs" />
	<input type="hidden" name="id_cron" value="<?php echo $this->idCron; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<?php
// log info modal
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-loginfo',
	array(
		'title'       => JText::_('VAPMAINTITLEVIEWCRONJOBLOGINFO'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<script>

	var LOG_ID_CLICKED = 0;

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'loginfo') {
			url = 'index.php?option=com_vikappointments&tmpl=component&task=cronjobloginfo&id=' + LOG_ID_CLICKED;
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

</script>
