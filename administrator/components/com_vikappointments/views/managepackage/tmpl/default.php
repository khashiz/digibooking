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

JHtml::_('behavior.calendar');
ArasJoomlaVikApp::datePicker();
$sel = $this->package;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$curr_symb 		= $config->get('currencysymb');
$date_format 	= $config->get('dateformat');

$df_joomla = $vik->jdateFormat($date_format);

$editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

if ($sel['start_ts'] != -1)
{
	$sel['start_ts'] 	= ArasJoomlaVikApp::jdate($sel['start_ts']); //JDate::getInstance($sel['start_ts'])->format($date_format);
	$sel['end_ts'] 		= ArasJoomlaVikApp::jdate($sel['end_ts']); //JDate::getInstance($sel['end_ts'])->format($date_format);
}
else
{
	$sel['start_ts'] = $sel['end_ts'] = '';
}


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
	
	<?php echo $vik->bootStartTabSet('package', array('active' => $this->tab)); ?>

		<!-- DETAILS -->
				
		<?php echo $vik->bootAddTab('package', 'package_details', JText::_('VAPMANAGEPACKAGEFIELDSET1')); ?>

			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					
					<!-- NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEPACKAGE1').'*:'); ?>
						<input type="text" name="name" class="required" value="<?php echo $sel['name']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- PRICE - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEPACKAGE3').':'); ?>
						<input type="number" name="price" value="<?php echo $sel['price']; ?>" size="40" min="0" max="99999999" step="any" />
						&nbsp;<?php echo $curr_symb; ?>
					<?php echo $vik->closeControl(); ?>

					<!-- NUMBER OF APPOINTMENTS - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEPACKAGE4').':'); ?>
						<input type="number" name="num_app" value="<?php echo $sel['num_app']; ?>" size="40" min="0" max="99999999" />
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGEPACKAGE4'),
							'content' 	=> JText::_('VAPMANAGEPACKAGE4_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- PUBLISHED - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEPACKAGE5').':'); ?>
						<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- START DATE - Calendar -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEPACKAGE6').':'); ?>
						<?php echo $vik->calendar($sel['start_ts'], 'start_ts', 'start_ts'); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- END DATE - Calendar -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEPACKAGE7').':'); ?>
						<?php echo $vik->calendar($sel['end_ts'], 'end_ts', 'end_ts'); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- GROUP - Select -->
					<?php
					$elements = array();
					$elements[] = $vik->initOptionElement('', '', false);
					foreach ($this->packGroups as $g) {
						$elements[] = JHtml::_('select.option', $g['id'], $g['title']);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEPACKAGE8').':'); ?>
						<select name="id_group" id="vap-group-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['id_group']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- ACCESS - Select -->
					<?php
					echo $vik->openControl(JText::_('JFIELD_ACCESS_LABEL').':');
					echo JHtml::_('access.level', 'level', $sel['level'], '', false, 'vap-level-select');
					echo $vik->createPopover(array(
						'title' 	=> JText::_('JFIELD_ACCESS_LABEL'),
						'content' 	=> JText::_('JFIELD_ACCESS_DESC'),
					));
					echo $vik->closeControl();
					?>

					<!-- SERVICES LIST - Select -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEPACKAGE11').':'); ?>
						<select name="id_services[]" id="vap-services-select" multiple>
							<?php foreach ($this->services as $g) { ?>
								<?php if (strlen($g['name'])) { ?>
									<optgroup label="<?php echo $g['name']; ?>">
								<?php } ?>

								<?php foreach ($g['list'] as $s) { ?>
									<option value="<?php echo $s['id']; ?>" <?php echo (in_array($s['id'], $this->assoc) ? 'selected="selected"' : ''); ?>>
										<?php echo $s['name']; ?>
									</option>
								<?php } ?> 

								<?php if (strlen($g['name'])) { ?>
									</optgroup>
								<?php } ?>
							<?php } ?>
						</select>
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGEPACKAGE11'),
							'content' 	=> JText::_('VAPMANAGEPACKAGE11_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- DESCRIPTION -->

		<?php echo $vik->bootAddTab('package', 'package_description', JText::_('VAPMANAGEPACKAGE2')); ?>

			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control">
						<?php echo $editor->display('description', $sel['description'], 400, 200, 70, 20); ?>
					</div>
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?> 
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-langtag',
	array(
		'title'       => JText::_('VAPPACKAGELANGTRANSLATION') . '<span id="tag-target"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);

JText::script('VAPMANAGEPACKAGE12');
?>

<script>

	jQuery(document).ready(function() {

	    jquery('#adminForm').on('submit',function (e) {
            e.preventDefault();
            alert(jQuery('#start_ts').val());
        })


		jQuery('#vap-group-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('#vap-services-select').select2({
			placeholder: Joomla.JText._("VAPMANAGEPACKAGE12"),
			allowClear: true,
			width: 300
		});

		jQuery('#vap-level-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300
		});
		
	});
	
	var SELECTED_TAG = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'langtag') {
			url = 'index.php?option=com_vikappointments&task=managelangpackage&id_package=<?php echo $sel['id']; ?>&tag=' + SELECTED_TAG;

			jQuery('#tag-target').text(' ' + SELECTED_TAG);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#package_"]').on('click', function() {
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
