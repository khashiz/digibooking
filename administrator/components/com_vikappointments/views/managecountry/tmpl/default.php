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

$sel = $this->country;

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<div class="span5">
		<?php echo $vik->openEmptyFieldset(); ?>
			
			<!-- COUNTRY NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECOUNTRY1').'*:'); ?>
				<input type="text" name="country_name" class="required" value="<?php echo $sel['country_name']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- COUNTRY 2 CODE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECOUNTRY2').'*:'); ?>
				<input type="text" name="country_2_code" class="required" value="<?php echo $sel['country_2_code']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- COUNTRY 3 CODE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECOUNTRY3').'*:'); ?>
				<input type="text" name="country_3_code" class="required" value="<?php echo $sel['country_3_code']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- PHONE PREFIX - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECOUNTRY4').'*:'); ?>
				<input type="text" name="phone_prefix" class="required" value="<?php echo $sel['phone_prefix']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- PUBLISHED - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
			$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGECOUNTRY5').':'); ?>
				<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="option" value="com_vikappointments"/>
</form>

<script>

	jQuery(document).ready(function() {

		jQuery('input[name="phone_prefix"]').on('keypress', function(e) {
			
			if (e.charCode != 43 
				&& (e.charCode < 48 || e.charCode > 57)) {
				return false;
			}

			if (e.charCode == 43 && jQuery(this).val().indexOf('+') != -1) {
				return false;
			}

		});

		jQuery('input[name="phone_prefix"]').on('keyup', function(e) {

			var val = jQuery(this).val();

			if (val.length && val.charAt(0) != '+') {
				jQuery(this).val('+' + val);
			}

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
