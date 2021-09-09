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

$row = $this->row;

$vik = UIApplication::getInstance();

$editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

?>

<form action="index.php" method="POST" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 48px;">
		<div class="btn-group pull-left">
			<button type="submit" class="btn btn-success" style="width: 120px;">
				<i class="icon-apply icon-white"></i>&nbsp;<?php echo JText::_('VAPSAVE'); ?>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="toggleLangSection(this);">
				<i class="icon-eye"></i>&nbsp;<?php echo JText::_('VAPSEEORIGINAL'); ?>
			</button>
		</div>
	</div>
	
	<div id="translation">
		<?php echo $vik->openEmptyFieldset(); ?>

			<!-- NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEGROUP2').':'); ?>
				<input type="text" name="name" value="<?php echo (!empty($row['name']) ? $row['name'] : ''); ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
		
			<!-- DESCRIPTION - Editor -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEGROUP3').':'); ?>
				<?php echo $editor->display('langdesc', (!empty($row['description']) ? $row['description'] : ''), 400, 200, 70, 20); ?>
			<?php echo $vik->closeControl(); ?>

		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>

	<div style="display: none;" id="original">
		<?php echo $vik->openEmptyFieldset(); ?>

			<!-- NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEGROUP2').':'); ?>
				<input type="text" value="<?php echo (!empty($this->default['name']) ? $this->default['name'] : ''); ?>" size="40" readonly />
			<?php echo $vik->closeControl(); ?>
		
			<!-- DESCRIPTION - Editor -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEGROUP3').':'); ?>
				<textarea style="width: 100%;height: 300px;min-height: 180px;resize: vertical;" readonly><?php echo (!empty($this->default['description']) ? $this->default['description'] : ''); ?></textarea>
			<?php echo $vik->closeControl(); ?>

		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
		
	<input type="hidden" name="id" value="<?php echo (!empty($row['id']) ? $row['id'] : -1); ?>" />
	<input type="hidden" name="id_group" value="<?php echo $this->idGroup; ?>" />
	<input type="hidden" name="tag" value="<?php echo $this->tag; ?>" />
	<input type="hidden" name="task" value="saveLangGroup" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<script>

	function toggleLangSection(button) {

		if (jQuery(button).hasClass('active'))
		{
			jQuery('#translation').show();
			jQuery('#original').hide();
			jQuery(button).removeClass('active').find('i').removeClass('icon-eye-close').addClass('icon-eye');
		}
		else
		{
			jQuery('#original').show();
			jQuery('#translation').hide();
			jQuery(button).addClass('active').find('i').removeClass('icon-eye').addClass('icon-eye-close');
		}

	}

</script>
