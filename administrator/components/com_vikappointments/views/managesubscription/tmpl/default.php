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

$sel = $this->subscr;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

?>

<?php if ($sel['id'] != -1 && VikAppointments::isMultilanguage()) { ?>

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
	
	<div class="span5">
		<?php echo $vik->openEmptyFieldset(); ?>
			
			<!-- NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCR1').'*:'); ?>
				<input type="text" name="name" class="required" value="<?php echo $sel['name']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- AMOUNT - Number -->
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCR2').':'); ?>
				<input type="number" name="amount" value="<?php echo $sel['amount']; ?>" size="40" min="1" id="vapsubscramount" <?php echo ($sel['type'] == 5 ? 'readonly="readonly"' : ''); ?> />
			<?php echo $vik->closeControl(); ?>
			
			<!-- TYPE - Select -->
			<?php 
			$elements = array();
			for ($i = 1; $i <= 5; $i++)
			{
				$elements[] = JHtml::_('select.option', $i, JText::_('VAPSUBSCRTYPE' . $i));
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCR3').':'); ?>
				<select name="type" id="vapsubscrtype" class="vapsubscrtype" onchange="subscrTypeChanged();">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['type']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
			<!-- PRICE - Number -->
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCR4').':'); ?>
				<input type="number" name="price" value="<?php echo $sel['price']; ?>" size="40" min="0" step="any" />
				&nbsp;<?php echo $config->get('currencysymb'); ?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- PUBLISHED - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
			$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCR5').':'); ?>
				<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- TRIAL - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $sel['trial'] == 1);
			$elem_no  = $vik->initRadioElement('', '', $sel['trial'] == 0);
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCR6').':'); ?>
				<?php echo $vik->radioYesNo('trial', $elem_yes, $elem_no, false); ?>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-langtag',
	array(
		'title'       => JText::_('VAPSUBSCRLANGTRANSLATION') . '<span id="tag-target"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<script>

	jQuery(document).ready(function() {

		jQuery('#vapsubscrtype').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

	});
	
	var SELECTED_TAG = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'langtag') {
			url = 'index.php?option=com_vikappointments&task=managelangsubscr&id_subscr=<?php echo $sel['id']; ?>&tag=' + SELECTED_TAG;

			jQuery('#tag-target').text(' ' + SELECTED_TAG);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

	function subscrTypeChanged() {
		jQuery('#vapsubscramount').prop('readonly', jQuery('#vapsubscrtype').val() == 5 ? true : false);
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
