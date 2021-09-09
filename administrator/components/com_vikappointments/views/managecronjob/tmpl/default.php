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

$sel = $this->cronjob;

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm"> 
	
	<div class="span6">
		<?php echo $vik->openFieldset(JText::_('VAPMANAGECRONJOBFIELDSET1'), 'form-horizontal'); ?>
			
			<!-- COUPON CODE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECRONJOB2').'*:'); ?>
				<input type="text" name="name" class="required" value="<?php echo $sel['name']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- TYPE CODE - Select -->
			<?php 
			$elements = array(
				JHtml::_('select.option', '', ''),
			);
			foreach ($this->allCronFiles as $file => $title)
			{
				$elements[] = JHtml::_('select.option', $file, $title);
			}

			/**
			 * Disable type selection if we are editing an existing cron job
			 * in order to avoid unexpected behaviors due to the created DB column(s).
			 *
			 * A popover is shown to explain why the type cannot be changed.
			 *
			 * @since 1.6.2
			 */
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGECRONJOB3').'*:'); ?>
				<select name="class" id="vap-cron-class" class="required" <?php echo $sel['id'] > 0 ? 'disabled="disabled"' : ''; ?>>
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['class']); ?>
				</select>
				
				<?php
				if ($sel['id'] > 0)
				{
					echo $vik->createPopover(array(
						'title'   => JText::_('VAPCRONJOBTYPEDISABLED'),
						'content' => JText::_('VAPCRONJOBTYPEDISABLED_HELP'),
					));

					// use hidden field because disabled dropdowns may not send the value set
					?>
					<input type="hidden" name="class" value="<?php echo $sel['class']; ?>" />
					<?php
				}
				?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- PUBLISHED - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
			$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGECRONJOB4').':'); ?>
				<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeFieldset(); ?>
	</div>
	
	<div class="span6">
		<?php echo $vik->openFieldset(JText::_('VAPMANAGECRONJOBFIELDSET2'), 'form-horizontal'); ?>
			<div class="control" id="vap-cron-params"><?php echo JText::_('VAPMANAGECRONJOB7'); ?></div>
		<?php echo $vik->closeFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<script>
	
	jQuery(document).ready(function() {
		
		<?php if ($sel['id'] != -1) { ?>
			loadCronFields();
		<?php } ?>
		
		jQuery('#vap-cron-class').on('change', function(){
			loadCronFields();
		});

		jQuery('#vap-cron-class').select2({
			placeholder: '<?php echo addslashes(JText::_('VAPMANAGECRONJOB6')); ?>',
			allowClear: false,
			width: 300
		});

	});
	
	function loadCronFields() {

		var clazz = jQuery("#vap-cron-class").val();
		if (clazz.length == 0) {
			jQuery('#vap-cron-params').html("<?php echo addslashes(JText::_('VAPMANAGECRONJOB7')); ?>");
			return;
		}

		jQuery('#vap-cron-params').html('');
				
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=get_cron_fields&tmpl=component",
			data: {
				cron: clazz,
				id: <?php echo $sel['id']; ?>
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0] == 1) {
				jQuery('#vap-cron-params').html(obj[1]);

				validator.registerFields('#vap-cron-params .required');
			} else {
				alert(obj[1]);
			}
			
		}).fail(function(resp) {
			console.log(resp);
		});
	}

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
