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

$sel = $this->state;

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<div class="span5">
		<?php echo $vik->openEmptyFieldset(); ?>
			
			<!-- STATE NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGESTATE1').'*:'); ?>
				<input type="text" name="state_name" class="required" value="<?php echo $sel['state_name']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- STATE 2 CODE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGESTATE2').'*:'); ?>
				<input type="text" name="state_2_code" class="required" value="<?php echo $sel['state_2_code']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- STATE 3 CODE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGESTATE3').':'); ?>
				<input type="text" name="state_3_code" value="<?php echo $sel['state_3_code']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- PUBLISHED - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
			$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGESTATE4').':'); ?>
				<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="country" value="<?php echo $this->country; ?>" />
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
