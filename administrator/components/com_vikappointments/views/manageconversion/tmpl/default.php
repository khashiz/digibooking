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

$sel = $this->conversion;

$vik = UIApplication::getInstance();

?>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	
	<div class="span6">
		<?php echo $vik->openFieldset(JText::_('VAPCUSTFIELDSLEGEND1'), 'form-horizontal'); ?>
			
			<!-- TITLE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEGROUP2').'*:'); ?>
				<input type="text" name="title" class="required" value="<?php echo $sel['title']; ?>" size="30" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- PUBLISHED - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
			$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
			?> 
			<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT3').':'); ?>
				<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
			<?php echo $vik->closeControl(); ?>

			<!-- STATUSES - Select -->
			<?php
			$elements = array();
			$elements[] = JHtml::_('select.option', '', '');
			$elements[] = JHtml::_('select.option', 'CONFIRMED', JText::_('VAPSTATUSCONFIRMED'));
			$elements[] = JHtml::_('select.option', 'PENDING', JText::_('VAPSTATUSPENDING'));
			$elements[] = JHtml::_('select.option', 'REMOVED', JText::_('VAPSTATUSREMOVED'));
			$elements[] = JHtml::_('select.option', 'CANCELED', JText::_('VAPSTATUSCANCELED'));
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION12').':'); ?>
				<select name="statuses[]" id="vap-statuses-sel" multiple>
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['statuses']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- JS FILE - Text -->
			<?php echo $vik->openControl(JText::_('VAPCUSTOMFTYPEOPTION7').':'); ?>
				<input type="url" name="jsfile" value="<?php echo $sel['jsfile']; ?>" size="48" />
			<?php echo $vik->closeControl(); ?>

			<!-- PAGE - Select -->
			<?php
			$elements = array();
			foreach ($this->pages as $page)
			{
				$elements[] = JHtml::_('select.option', $page, ucwords(str_replace('_', ' ', $page)));
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPPAGE').':'); ?>
				<select name="page" id="vap-page-sel" class="required">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['page']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>

			<!-- TYPE - Select -->
			<?php
			$elements = array();
			foreach ($this->types as $type)
			{
				$elements[] = JHtml::_('select.option', $type, ucwords(str_replace('_', ' ', $type)));
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF2').':'); ?>
				<select name="type" id="vap-type-sel" class="required">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['type']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeFieldset(); ?>
	</div>

	<div class="span6">
		<?php echo $vik->openFieldset(JText::_('VAPCODESNIPPET'), 'form-horizontal'); ?>

			<div class="control-group">
				<?php echo $vik->getCodeMirror('snippet', $sel['snippet']); ?>
			</div>

		<?php echo $vik->closeFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />

</form>

<?php
JText::script('VAPPAYMENTPOSOPT1');
?>

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-statuses-sel').select2({
			placeholder: Joomla.JText._('VAPPAYMENTPOSOPT1'),
			allowClear: true,
			width: 300
		});

		jQuery('#vap-page-sel, #vap-type-sel').select2({
			allowClear: false,
			width: 300
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
