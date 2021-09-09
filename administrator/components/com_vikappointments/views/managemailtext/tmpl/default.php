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

$sel = $this->sel;

$vik = UIApplication::getInstance();

$editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

$languages = VikAppointments::getKnownLanguages();

$positions = array(
	'{custom_position_top}',
	'{custom_position_middle}',
	'{custom_position_bottom}',
	'{custom_position_footer}',
);

$statuses = array(
	'CONFIRMED',
	'PENDING',
	'REMOVED',
	'CANCELED',
);

$all_tmpl_files = glob(VAPBASE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mail_tmpls' . DIRECTORY_SEPARATOR . '*.php');

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">

	<?php echo $vik->bootStartTabSet('mailtext', array('active' => $this->tab)); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('mailtext', 'mailtext_details', JText::_('VAPCUSTMAILTITLE1')); ?>
	
			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
				
					<!-- NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTMAIL2').'*:'); ?>
						<input type="text" name="name" value="<?php echo $sel['name']; ?>" class="required" size="40" />
					<?php echo $vik->closeControl(); ?>

					<!-- FILE - Dropdown -->
					<?php
					$elements = array();
					$elements[] = JHtml::_('select.option', '', '');
					foreach ($all_tmpl_files as $file)
					{
						$file = basename($file);
						$elements[] = JHtml::_('select.option', $file, $file);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTMAIL5').'*:'); ?>
						<select name="file" id="vap-file-sel" class="required">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['file']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>
				
					<!-- POSITION - Dropdown -->
					<?php
					$elements = array();
					foreach ($positions as $p)
					{
						$elements[] = JHtml::_('select.option', $p, $p);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTMAIL3').':'); ?>
						<select name="position" id="vap-position-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['position']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- STATUS - Dropdown -->
					<?php
					$elements = array();
					foreach ($statuses as $s)
					{
						$elements[] = JHtml::_('select.option', $s, JText::_('VAPSTATUS' . $s));
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTMAIL4').':'); ?>
						<select name="status" id="vap-status-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['status']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- SERVICE - Dropdown -->
					<?php
					$elements = array();
					$elements[] = JHtml::_('select.option', '', '');
					foreach ($this->services as $service)
					{
						$elements[] = JHtml::_('select.option', $service->id, $service->name);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION4').':'); ?>
						<select name="id_service" id="vap-service-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['id_service']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- EMPLOYEE - Dropdown -->
					<?php
					$elements = array();
					$elements[] = JHtml::_('select.option', '', '');
					foreach ($this->employees as $employee)
					{
						$elements[] = JHtml::_('select.option', $employee->id, $employee->nickname);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION3').':'); ?>
						<select name="id_employee" id="vap-employee-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['id_employee']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- LANG TAG - Dropdown -->
					<?php
					$elements = array();
					foreach ($languages as $lang)
					{
						$elements[] = JHtml::_('select.option', $lang, $lang);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGECUSTMAIL6').':'); ?>
						<select name="tag" id="vap-tag-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['tag']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- DESCRIPTION -->
				
		<?php echo $vik->bootAddTab('mailtext', 'mailtext_description', JText::_('VAPCUSTMAILTITLE2')); ?>
	
			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control-group">
						<?php echo $editor->display('cont', $sel['content'], 400, 200, 40, 20); ?>
					</div>
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?> "/>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-position-sel, #vap-status-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300
		});

		jQuery('#vap-file-sel').select2({
			placeholder: '--',
			allowClear: false,
			width: 300
		});

		jQuery('#vap-service-sel, #vap-employee-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('#vap-tag-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

	});

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#mailtext_"]').on('click', function() {
			var href = jQuery(this).attr('href').substr(1);
			jQuery('input[name="tabname"]').val(href);
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
