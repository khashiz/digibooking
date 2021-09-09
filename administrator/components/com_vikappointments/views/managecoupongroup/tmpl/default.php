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

$sel = $this->group;

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<?php echo $vik->openEmptyFieldset(); ?>

		<!-- NAME - Text -->
		<?php echo $vik->openControl(JText::_('VAPMANAGEGROUP2').'*:'); ?>
			<input type="text" name="name" class="required" value="<?php echo $sel['name']; ?>" size="40" />
		<?php echo $vik->closeControl(); ?>
	
		<!-- DESCRIPTION - Editor -->
		<?php echo $vik->openControl(JText::_('VAPMANAGEGROUP3').':'); ?>
			<textarea name="description" style="width: 400px;height: 120px;resize: vertical;"><?php echo $sel['description']; ?></textarea>
		<?php echo $vik->closeControl(); ?>

	<?php echo $vik->closeEmptyFieldset(); ?>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<script>

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
