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

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<div class="span8">
		<?php echo $vik->openEmptyFieldset(); ?>
			
			<!-- EMPLOYEES - Select -->

			<?php
			$options = array();
			$options[] = JHtml::_('select.option', '', '');

			foreach ($this->employees as $e)
			{
				$options[] = JHtml::_('select.option', $e->id, $e->nickname);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMENUEMPLOYEES') . '*:'); ?>
				<select name="employees[]" id="vap-employees-sel" class="required" multiple>
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $this->item->employees); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- FROM DATE - Calendar -->

			<?php echo $vik->openControl(JText::_('VAPMANAGEWD2') . '*:'); ?>
				<?php echo $vik->calendar($this->item->fromDate, 'fromdate', null, null, array('class' => 'required')); ?>
			<?php echo $vik->closeControl(); ?>

			<!-- FROM HOUR AND MINUTES - Select -->
			<?php
			$hours 	 = array();
			$minutes = array();

			for ($h = 0; $h <= 24; $h++)
			{
				$hours[] = JHtml::_('select.option', $h, date('H', mktime($h, 0, 0, 1, 1, 2000)));
			}

			for ($m = 0; $m <= 55; $m += 5)
			{
				$minutes[] = JHtml::_('select.option', $m, date('i', mktime(0, $m, 0, 1, 1, 2000)));
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGEWD3') . '*:'); ?>
				<select name="fromhour" class="time-dropdown required">
					<?php echo JHtml::_('select.options', $hours, 'value', 'text', $this->item->fromHour); ?>
				</select>
				<select name="frommin" class="time-dropdown required">
					<?php echo JHtml::_('select.options', $minutes, 'value', 'text', $this->item->fromMin); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- TO HOUR AND MINUTES - Select -->

			<?php echo $vik->openControl(JText::_('VAPMANAGEWD4') . '*:'); ?>
				<select name="tohour" class="time-dropdown required">
					<?php echo JHtml::_('select.options', $hours, 'value', 'text', $this->item->toHour); ?>
				</select>
				<select name="tomin" class="time-dropdown required">
					<?php echo JHtml::_('select.options', $minutes, 'value', 'text', $this->item->toMin); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="from" value="<?php echo $this->from; ?>" />
</form>

<script>
	
	jQuery(document).ready(function() {

		jQuery('#vap-employees-sel').select2({
			placeholder: '--',
			allowClear: false,
			width: 300
		});

		jQuery('.time-dropdown').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100
		});

	});

	// validate

	var validator = new VikFormValidator('#adminForm');

	Joomla.submitbutton = function(task) {
		if (task.indexOf('save') !== -1) {
			if (validator.validate()) {
				Joomla.submitform(task, document.adminForm);    
			}
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}
	
</script>
