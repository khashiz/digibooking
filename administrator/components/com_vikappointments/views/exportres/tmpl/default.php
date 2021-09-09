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

JHtml::_('behavior.calendar');

$employees  = $this->employees;
$dates 		= $this->dates;

$classfiles = array();
foreach (glob(VAPADMIN . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . '*.php') as $file)
{
	$name = basename($file);
	$name = substr($name, 0, strrpos($name, '.'));
	
	$classfiles[] = $name;
}

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">

<?php if (count($classfiles) == 0) { ?>

	<p><?php echo JText::_('VAPEXPORTNOFILESERR'); ?></p>

<?php } else { ?>

	<div class="span6">
		<?php echo $vik->openEmptyFieldset(); ?>
			
			<!-- NAME - Text -->

			<?php echo $vik->openControl(JText::_('VAPEXPORTRES1').':'); ?>
				<input type="text" name="filename" value="" id="vapfilename" placeholder="name" /> 
				<small id="vapfnlabel"><?php echo '.' . $classfiles[0]; ?></small>
			<?php echo $vik->closeControl(); ?>
			
			<!-- CLASS - Select -->

			<?php
			$options = array();
			foreach ($classfiles as $name)
			{
				$options[] = JHtml::_('select.option', $name, strtoupper($name));
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPEXPORTRES2').':'); ?>
				<select class="" name="export_type" id="vap-exptype-sel" onChange="exportTypeChanged()">
					<?php echo JHtml::_('select.options', $options); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- DATE START - Calendar -->
			
			<?php echo $vik->openControl(JText::_('VAPEXPORTRES3').':'); ?>
				<?php echo $vik->calendar($dates[0], 'date_start', 'vapdatestart'); ?>
			<?php echo $vik->closeControl(); ?>

			<!-- DATE END - Calendar -->

			<?php echo $vik->openControl(JText::_('VAPEXPORTRES4').':'); ?>
				<?php echo $vik->calendar($dates[1], 'date_end', 'vapdateend'); ?>
			<?php echo $vik->closeControl(); ?>

			<!-- EMPLOYEE - Select -->
			
			<?php
			$options = array();
			$options[] = JHtml::_('select.option', '', '');
			foreach ($employees as $e)
			{
				$options[] = JHtml::_('select.option', $e['id'], $e['lastname'] . ' ' . $e['firstname']);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPEXPORTRES5').':'); ?>
				<select name="employee" id="vap-employee-sel">
					<?php echo JHtml::_('select.options', $options); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
		
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>

<?php } ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<?php
// load language tags for JS use
JText::script('VAPEXPORTRES6');
?>

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-exptype-sel').select2({
			allowClear: false,
			width: 300
		});

		jQuery('#vap-employee-sel').select2({
			placeholder: Joomla.JText._('VAPEXPORTRES6'),
			allowClear: false,
			width: 300
		});

	});

	function exportTypeChanged() {
		jQuery('#vapfnlabel').text('.' + jQuery('#vap-exptype-sel').val());
	}

</script>
