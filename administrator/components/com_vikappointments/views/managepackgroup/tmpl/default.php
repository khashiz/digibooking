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

$editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

$vik = UIApplication::getInstance();

?>

<?php if ($sel['id'] != -1 && VikAppointments::isMultilanguage(true)) { ?>

	<div class="btn-toolbar" id="filter-bar">
		<div class="btn-group pull-right">
			<?php foreach (VikAppointments::getKnownLanguages() as $lang) { 
				$lang_name = explode('-', $lang);
				$lang_name = $lang_name[1];
				?>
				<button type="button" class="btn" onClick="SELECTED_TAG='<?php echo $lang; ?>';vapOpenJModal('langtag', null, true);">
					<i class="icon">
						<img src="<?php echo VAPASSETS_URI . 'css/flags/' . strtolower($lang_name) . '.png'; ?>" />
					</i>
					&nbsp;<?php echo $lang; ?>
				</button>
			<?php } ?>
		</div>
	</div>

<?php } ?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	
	<?php echo $vik->openEmptyFieldset(); ?>
	
		<!-- NAME - Text -->
		<?php echo $vik->openControl(JText::_('VAPMANAGEPACKGROUP2').'*:'); ?>
			<input type="text" name="title" class="required" value="<?php echo $sel['title']; ?>" size="40" />
		<?php echo $vik->closeControl(); ?>
		
		<!-- DESCRIPTION - Editor -->
		<?php echo $vik->openControl(JText::_('VAPMANAGEPACKGROUP3').':'); ?>
			<?php echo $editor->display('description', $sel['description'], 400, 200, 70, 20); ?>
		<?php echo $vik->closeControl(); ?>

	<?php echo $vik->closeEmptyFieldset(); ?>
		
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-langtag',
	array(
		'title'       => JText::_('VAPPACKGROUPLANGTRANSLATION') . '<span id="tag-target"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<script>
	
	var SELECTED_TAG = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'langtag') {
			url = 'index.php?option=com_vikappointments&task=managelangpackgroup&id_package_group=<?php echo $sel['id']; ?>&tag=' + SELECTED_TAG;

			jQuery('#tag-target').text(' ' + SELECTED_TAG);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
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
