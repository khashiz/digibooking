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

$vik = UIApplication::getInstance();

$head = $this->handler->getColumns();
$rows = $this->rows;

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php if (!count($rows)) { ?>

	<div class="alert alert-no-items"><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<?php foreach ($head as $k => $column) { ?>
					<th class="<?php echo $vik->getAdminThClass(); ?>" style="text-align: center;"><?php echo $column->label; ?></th>
				<?php } ?>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				
				<?php foreach ($head as $k => $label) { ?>

					<td style="text-align: center;">
						<?php
						$value = $row[$k];

						if (isset($label->options[$value]))
						{
							echo '<i>' . $label->options[$value] . '</i>';
						}
						else
						{
							echo $value;
						}
						?>
					</td>

				<?php } ?>

			</tr>
		
		<?php }	?>
	
	</table>

	<div style="text-align: center;">
		<small><?php echo JText::sprintf('VAPEXPORTTABLEJOOMLA3810TER', $n, $this->handler->getTotalCount()); ?></small>
	</div>

<?php } ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="import_type" value="<?php echo $this->type; ?>" />

	<?php foreach ($this->args as $k => $v) { ?>

		<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>" />
		<input type="hidden" name="import_args[<?php echo $k; ?>]" value="<?php echo $v; ?>" />
		
	<?php } ?>

	<?php
	echo JHtml::_(
		'bootstrap.renderModal',
		'jmodal-download',
		array(
			'title'       => JText::_('VAPMAINTITLEVIEWEXPORT'),
			'closeButton' => true,
			'keyboard'    => false, 
			'bodyHeight'  => 40,
			'footer' 	  => '<button type="button" class="btn btn-success" id="export-btn" onclick="downloadExport();">' . JText::_('JAPPLY') . '</button>',
		),
		$this->loadTemplate('params')
	);
	?>

</form>

<script>

	function vapOpenJModal(id, url, jqmodal) {
		<?php echo $vik->bootOpenModalJS(); ?>
	}

	Joomla.submitbutton = function(task) {

		if (task == 'downloadExport') {
			vapOpenJModal('download', null, true);
		} else {
			Joomla.submitform(task, document.adminForm);
		}

	}

</script>
