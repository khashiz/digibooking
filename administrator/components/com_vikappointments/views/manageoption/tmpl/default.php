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

JHtml::_('behavior.modal');

$sel = $this->option;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$img_url = '';
if (!empty($sel['image']))
{
	$img_url = VAPMEDIA_URI . $sel['image'];
}

$media_prop = array(
	'name'     => 'media',
	'id'       => 'vapmediaselect',
	'onChange' => 'imageSelectChanged()'
);

$_ASSETS = VAPASSETS_ADMIN_URI . 'images/';

$last_var_id = 0;

$curr_symb = $config->get('currencysymb');

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

	<?php echo $vik->bootStartTabSet('option', array('active' => $this->tab)); ?>

		<!-- DETAILS -->
				
		<?php echo $vik->bootAddTab('option', 'option_details', JText::_('VAPOPTIONFIELDSETTITLE1')); ?>

			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					
					<!-- NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEOPTION2').'*:'); ?>
						<input type="text" name="name" class="required" value="<?php echo $sel['name']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- PRICE - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEOPTION4').':'); ?>
						<input type="number" name="price" value="<?php echo $sel['price']; ?>" size="10" step="any" />
						&nbsp;<?php echo $curr_symb; ?>
					<?php echo $vik->closeControl(); ?>

					<!-- PUBLISHED - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE6').':'); ?>
						<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- MULTIPLE SELECTION - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['single'] == 1, 'onClick="singleCheckboxChanged(1);"');
					$elem_no  = $vik->initRadioElement('', '', $sel['single'] == 0, 'onClick="singleCheckboxChanged(0);"');
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEOPTION5').':'); ?>
						<?php echo $vik->radioYesNo('single', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- MAX QUANTITY - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEOPTION6').':'); ?>
						<input type="number" id="vapmaxq" name="maxq" value="<?php echo $sel['maxq']; ?>" size="6" min="1" max="999999" <?php echo ($sel['single'] == 0 ? 'readonly' : ''); ?> />
					<?php echo $vik->closeControl(); ?>
					
					<!-- REQUIRED - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['required'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['required'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEOPTION12').':'); ?>
						<?php echo $vik->radioYesNo('required', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- DISPLAY MODE - Dropdown -->
					<?php
					$elements = array();
					for ($i = 0; $i < 3; $i++)
					{
						$elements[] = JHtml::_('select.option', $i, JText::_('VAPMANAGEOPTDISPLAYMODE' . $i));
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEOPTION11').':'); ?>
						<select name="displaymode" id="vap-dispmode-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['displaymode']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>
					
					 <!-- UPLOAD IMAGE - File -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEOPTION7').':'); ?>
						<span id="vapimagespan">
							<input type="file" name="image" size="35" onChange="uploadImage();">
						</span>
					<?php echo $vik->closeControl(); ?>
					
					<!-- CHOOSE IMAGE - Select -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEOPTION8').':'); ?>
						<span>
							<?php echo AppointmentsHelper::composeMediaSelect($sel['image'], true, $media_prop); ?>
							<a href="<?php echo $img_url ?>" id="vapimagelink" class="modal no-decoration" style="margin-left: 5px;" target="_blank">
								<?php if (!empty($sel['image'])) { ?>
									<i class="fa fa-camera big"></i>
								<?php } ?>
							</a>
						</span>
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- DESCRIPTION -->
				
		<?php echo $vik->bootAddTab('option', 'option_description', JText::_('VAPMANAGEOPTION3')); ?>

			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control-group">
						<textarea name="description" style="width: 95%;height: 300px;"><?php echo $sel['description']; ?></textarea>
					</div>
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- VARIATIONS -->

		<?php echo $vik->bootAddTab('option', 'option_variations', JText::_('VAPOPTIONFIELDSETTITLE2')); ?>

			<div class="span4">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control-group">
						
						<div class="vap-option-variations">
							<?php foreach ($this->variations as $var) { ?>
								<div id="vapvardiv<?php echo $var['id']; ?>" class="vap-option-var">
									<span class="vap-option-varsp">
										<span class="vap-sort-box"></span>
										<input type="hidden" name="var_id[]" id="vapvarid<?php echo $var['id']; ?>" value="<?php echo $var['id']; ?>" />
										<input type="text" name="var_name[]" class="required" value="<?php echo $var['name']; ?>" size="32" placeholder="<?php echo JText::_('VAPMANAGEOPTION13'); ?>" />
										<input type="number" name="var_price[]" value="<?php echo $var['inc_price']; ?>" size="6" placeholder="<?php echo JText::_('VAPMANAGEOPTION4'); ?>" step="any" /> <?php echo $curr_symb; ?>
										<a href="javascript: void(0);" onClick="removeVariation(<?php echo $var['id']; ?>);">
											<i class="fa fa-trash input-align"></i>
										</a>
									</span>
								</div>
								<?php $last_var_id = max(array($var['id'], $last_var_id)); ?>
							<?php } ?>
						</div>
						
						<div class="vap-option-addvar" style="margin-top: 20px;">
							<button type="button" class="btn" onClick="addNewVariation();">
								<?php echo JText::_('VAPMANAGEOPTION14'); ?>
							</button>
						</div>
						
					</div>
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="uploadImage" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-langtag',
	array(
		'title'       => JText::_('VAPOPTLANGTRANSLATION') . '<span id="tag-target"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);

JText::_('VAPMANAGEOPTION4');
JText::_('VAPMANAGEOPTION13');
?>

<script>

	jQuery(document).ready(function() {
		makeSortable();

		jQuery('#vap-dispmode-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 200
		});

	});
	
	function singleCheckboxChanged(sel) {
		jQuery('#vapmaxq').prop( 'readonly', sel ? 0 : 1 );
	}

	// VARIATIONS
	
	curr_var_index = <?php echo ($last_var_id+1); ?>;
	
	function addNewVariation() {
		jQuery('.vap-option-variations').append(
			'<div id="vapvardiv'+curr_var_index+'" class="vap-option-var">\n'+
				'<span class="vap-option-varsp">\n'+
					'<span class="vap-sort-box"></span>\n'+
					'<input type="hidden" name="var_id[]" id="vapvarid'+curr_var_index+'" value="-1" />\n'+
					'<input type="text" name="var_name[]" class="required" value="" size="32" placeholder="' + Joomla.JText._('VAPMANAGEOPTION13') + '" />\n'+
					'<input type="number" name="var_price[]" value="" size="6" placeholder="' + Joomla.JText._('VAPMANAGEOPTION4') + '" step="any" /> <?php echo $curr_symb; ?>\n'+
					'<a href="javascript: void(0);" onClick="removeVariation('+curr_var_index+');">\n'+
						'<i class="fa fa-trash input-align"></i>\n'+
					'</a>\n'+
				'</span>\n'+
			'</div>\n');
			
		curr_var_index++;
		
		makeSortable();
		validator.registerFields(jQuery('input[name="var_name[]"]').last());
	}
	
	function removeVariation(var_id) {
		var table_row = jQuery('#vapvarid'+var_id).val();
		jQuery('#vapvardiv'+var_id).remove();
		if (table_row != -1) {
			jQuery('#adminForm').append('<input type="hidden" name="remove_variation[]" value="'+table_row+'"/>');
		}
	}

	function makeSortable() {
		
		jQuery(".vap-option-variations").sortable({
			revert: true
		});
		
	}

	// IMAGE
	
	function uploadImage() {
		if (jQuery('#vapverifyimg').length == 0) {
			jQuery('#vapimagespan').append('<img src="<?php echo $_ASSETS . 'loading.gif'; ?>" id="vapverifyimg" />');
		} else {
			jQuery('#vapverifyimg').prop('src', '<?php echo $_ASSETS . 'loading.gif'; ?>');
		}
		
		var formData = new FormData(jQuery('form#adminForm')[0]);
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=uploadImage&tmpl=component",
			data: formData,
			cache: false,
			processData: false,
			contentType: false
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0] == 1) {
				jQuery('#vapverifyimg').prop('src', '<?php echo $_ASSETS . 'ok.png'; ?>');
				jQuery('#vapmediaselect').append('<option value="'+obj[1]+'" selected="selected">'+obj[1]+'</option>');
				jQuery('#vapmediaselect').select2('val', obj[1]);
				imageSelectChanged();
			} else {
				jQuery('#vapverifyimg').prop('src', '<?php echo $_ASSETS . 'no.png'; ?>');
				alert(obj[1]);
			}
			
		});
	}
	
	function imageSelectChanged() {
		var img = jQuery('#vapmediaselect').val();
		if (img.length > 0) {
			jQuery('#vapimagelink').prop('href', '<?php echo VAPMEDIA_URI; ?>'+img);
			jQuery('#vapimagelink').html('\n<i class="fa fa-camera big"></i>\n');
		} else {
			jQuery('#vapimagelink').prop('href', '');
			jQuery('#vapimagelink').html('');
		}
	}
	
	// TRANSLATIONS
	
	var SELECTED_TAG = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'langtag') {
			url = 'index.php?option=com_vikappointments&task=managelangoption&id_option=<?php echo $sel['id']; ?>&tag=' + SELECTED_TAG;

			jQuery('#tag-target').text(' ' + SELECTED_TAG);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#option_"]').on('click', function() {
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
