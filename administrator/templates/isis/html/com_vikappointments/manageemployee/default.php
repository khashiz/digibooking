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
//JHtml::_('behavior.calendar');


ArasJoomlaVikApp::datePicker();

$sel    = $this->employee;
$groups = $this->groups;

$vik = UIApplication::getInstance();

$img_url = '';
if (!empty($sel['image']))
{
	$img_url = VAPMEDIA_URI . $sel['image'];
}

$media_prop = array(
	'name'     => 'media',
	'id'       => 'vapmediaselect',
	'onChange' => 'imageSelectChanged()',
);

$_ASSETS = VAPASSETS_ADMIN_URI . 'images/';

$editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

// END SELECT

$max_id = -1;

// MEDIA

$is_media_prop = VikAppointments::isMediaPropertiesConfigured();

$def_val = array(
	'ORI_W' => VikAppointments::getOriginalWidthResize(),
	'ORI_H' => VikAppointments::getOriginalHeightResize(),
	'SML_W' => VikAppointments::getSmallWidthResize(),
	'SML_H' => VikAppointments::getSmallHeightResize(),
	'ISRES' => VikAppointments::isImageResize(),
);

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

	<?php echo $vik->bootStartTabSet('employee', array('active' => $this->tab)); ?>

		<!-- DETAILS -->
			
		<?php echo $vik->bootAddTab('employee', 'employee_details', JText::_('VAPORDEREMPLOYEE')); ?>
	
			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>

                <!-- GROUP - Select -->
                <?php
                $options = array(
                    JHtml::_('select.option', '', ''),
                );
                foreach ($groups as $g) {
                    $options[] = JHtml::_('select.option', $g['id'], $g['name']);
                }
                ?>
                <?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE26').':'); ?>
                <select name="group" id="vap-group-sel">
                    <?php echo JHtml::_('select.options', $options, 'value', 'text', $sel['id_group']); ?>
                </select>
                <?php echo $vik->closeControl(); ?>
				
					<!-- FIRST NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE2').'*:'); ?>
						<input class="required" type="text" name="firstname" value="<?php echo $sel['firstname']; ?>" size="40" id="vapfirstnametext" onBlur="firstNameFocusLost()" />
					<?php echo $vik->closeControl(); ?> 
					
					<!-- LAST NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE3').'*:'); ?>
						<input class="required" type="text" name="lastname" value="<?php echo $sel['lastname']; ?>" size="40" id="vaplastnametext" onBlur="lastNameFocusLost()" />
					<?php echo $vik->closeControl(); ?> 
					
					<!-- NICKNAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE4').'*:'); ?>
						<input class="required" type="text" name="nickname" value="<?php echo $sel['nickname']; ?>" id="vapnominativetext" size="40" />
					<?php echo $vik->closeControl(); ?>

					<!-- ALIAS - Text -->
					<?php echo $vik->openControl(JText::_('JFIELD_ALIAS_LABEL').':'); ?>
						<input type="text" name="alias" value="<?php echo $sel['alias']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- EMAIL - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE8').':'); ?>
						<input type="text" name="email" value="<?php echo $sel['email']; ?>" size="40" />
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGEEMPLOYEE8'),
							'content' 	=> JText::_('VAPMANAGEEMPLOYEE8_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>

                <div style="display: none">
					<!-- PHONE - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE10').':'); ?>
						<input type="text" name="phone" value="<?php echo $sel['phone']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- USER ID - Dropdown -->
					<?php
					$options = array(
						JHtml::_('select.option', '', ''),
					);
					foreach ($this->jusers as $u) {
						$options[] = JHtml::_('select.option', $u['id'], $u['name'] . ($u['name'] != $u['username'] ? ' | ' . $u['username'] : ''));
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE19').':'); ?>
						<select name="jid" id="vap-jusers-sel">
							<?php echo JHtml::_('select.options', $options, 'value', 'text', $sel['jid']); ?>
						</select>
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGEEMPLOYEE19'),
							'content' 	=> JText::_('VAPMANAGEEMPLOYEE19_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>

					

					
					<!-- NOTIFY BOOKINGS - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['notify'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['notify'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE9').':'); ?>
						<?php echo $vik->radioYesNo('notify', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- SHOW PHONE - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['showphone'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['showphone'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE16').':'); ?>
						<?php echo $vik->radioYesNo('showphone', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- QUICK CONTACT - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['quick_contact'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['quick_contact'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE17').':'); ?>
						<?php echo $vik->radioYesNo('quick_contact', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGEEMPLOYEE17'),
							'content'	=> JText::_('VAPMANAGEEMPLOYEE17_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
                </div>
					
					<!-- LISTABLE - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['listable'] == 1, 'onClick="jQuery(\'.vapactiverow\').show();"');
					$elem_no  = $vik->initRadioElement('', '', $sel['listable'] == 0, 'onClick="jQuery(\'.vapactiverow\').hide();"');
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE18').':'); ?>
						<?php echo $vik->radioYesNo('listable', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGEEMPLOYEE18'),
							'content'	=> JText::_('VAPMANAGEEMPLOYEE18_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- TIMEZONE - Select -->
					<?php if (VikAppointments::isMultipleTimezones()) { ?>
						<?php
						$zones = array(
							0 => array(JHtml::_('select.option', '', '')),
						);
						foreach (timezone_identifiers_list() as $zone)
						{
							$parts = explode('/', $zone);

							$continent  = isset($parts[0]) ? $parts[0] : '';
							$city 		= (isset($parts[1]) ? $parts[1] : $continent) . (isset($parts[2]) ? '/' . $parts[2] : '');
							$city 		= ucwords(str_replace('_', ' ', $city));

							if (!isset($zones[$continent]))
							{
								$zones[$continent] = array();
							}

							$zones[$continent][] = JHtml::_('select.option', $zone, $city);
						}
						?>
						<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE30').':'); ?>
							<?php
							$params = array(
								'id' 			=> 'vap-timezone-sel',
								'group.items' 	=> null,
								'list.select'	=> $sel['timezone'],
							);
							echo JHtml::_('select.groupedList', $zones, 'timezone', $params);
							?>
						<?php echo $vik->closeControl(); ?>
					<?php } ?>
					
					<!-- ACTIVE TO - Custom -->
					<?php
					if ($this->hasSubscr)
					{
						$active_to_type = 'date';
						if ($sel['active_to'] == 0)
						{
							$active_to_type = 'pending';
						}
						else if ($sel['active_to'] == -1)
						{
							$active_to_type = 'lifetime';
						}

						$options = array(
							JHtml::_('select.option', 'date', 'VAPINVDATE'),
							JHtml::_('select.option', 'pending', 'VAPSTATUSPENDING'),
							JHtml::_('select.option', 'lifetime', 'VAPSUBSCRTYPE5'),
						);

						$control = array();
						$control['style'] = $sel['listable'] == 0 ? 'display: none;' : '';
						
						echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE27').':', 'vapactiverow', $control); ?>
							<select name="active_to_type" id="vap-activeto-sel">
								<?php echo JHtml::_('select.options', $options, 'value', 'text', $active_to_type, true); ?>
							</select>

							<span class="vapactivetosp" style="<?php echo ($sel['active_to'] <= 0 ? 'display: none;' : ''); ?>">
								<?php $active_date = ($sel['active_to'] <= 0 ? $sel['active_to'] : ArasJoomlaVikApp::jdate($date_format, $sel['active_to'])); ?>

                                <?php echo $vik->calendar($active_date, 'active_to', 'vapactivetodate'); ?>
							</span>

							<?php echo $vik->createPopover(array(
								'title'		=> JText::_('VAPMANAGEEMPLOYEE27'),
								'content'	=> JText::_('VAPMANAGEEMPLOYEE27_DESC'),
							)); ?>
						<?php echo $vik->closeControl(); ?>
						<?php
					}
					else
					{
						/**
						 * In case there are no subscriptions available, we need to 
						 * have the employee lifetime listable (-1).
						 *
						 * @since 1.6.2
						 */
						?>
						<input type="hidden" name="active_to_type" value="-1" />
						<?php
					}
					?>
					
					<!-- SYNC KEY - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE24').'*:'); ?>
						<input type="text" name="synckey" value="<?php echo $sel['synckey']; ?>" class="required" maxlength="32" size="40" id="synckey" onBlur="updateSyncKey();" />
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGEEMPLOYEE24'),
							'content'	=> JText::_('VAPMANAGEEMPLOYEE24_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- SYNC URL - Text -->
					<?php if ($sel['id'] > 0) { ?>
						<?php $sync_url = JUri::root() . 'index.php?option=com_vikappointments&task=appsync&employee=' . $sel['id'] . '&key=' . $sel['synckey']; ?>
						<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE25').':'); ?>
							<input type="text" value="<?php echo $sync_url; ?>" size="64" id="syncurl" readonly />
							<?php echo $vik->createPopover(array(
								'title'		=> JText::_('VAPMANAGEEMPLOYEE25'),
								'content'	=> JText::_('VAPMANAGEEMPLOYEE25_DESC'),
							)); ?>
						<?php echo $vik->closeControl(); ?>
					<?php } ?>
					
					<!-- UPLOAD IMAGE - File -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE5').':'); ?>
						<span id="vapimagespan">
							<input type="file" name="image" id="vapimagefileinput" size="35" onChange="uploadImage();" onClick="return openMediaPropertiesDialog();" />
						</span>
					<?php echo $vik->closeControl(); ?>
					
					<!-- CHOOSE IMAGE - Select -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOYEE6').':'); ?>
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

		<?php if (count($this->customFields)) { ?>

			<!-- CUSTOM FIELDS -->
				
			<?php echo $vik->bootAddTab('employee', 'employee_custfields', JText::_('VAPMANAGERESERVATIONTITLE2')); ?>

				<?php echo $this->loadTemplate('fields'); ?>

			<?php echo $vik->bootEndTab(); ?>

		<?php } ?>

		<!-- DESCRIPTION -->

		<?php echo $vik->bootAddTab('employee', 'employee_description', JText::_('VAPMANAGEEMPLOYEE11')); ?>
	
			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control-group"><?php echo $editor->display('note', $sel['note'], 400, 200, 40, 20); ?></div>
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- WORKING DAYS -->

		<?php echo $vik->bootAddTab('employee', 'employee_workdays', JText::_('VAPMANAGEEMPLOYEE12')); ?>
					
			<?php echo $this->loadTemplate('workdays'); ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="uploadImage" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<div id="dialog-confirm" title="<?php echo JText::_('VAPMEDIAPROPBOXTITLE');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-gear" style="float: left; margin: 0 7px 20px 0;"></span>
		<span style="font-size: 13px;font-weight: bold;"><?php echo JText::_('VAPMEDIAPROPBOXMESSAGE'); ?></span>
		<table class="adminform">
			
			<tr>
				<td width="125"><b><?php echo JText::_('VAPMANAGEMEDIA6');?>:</b></td>
				<td><span>
					<?php echo JText::_('VAPMANAGEMEDIA9'); ?>
					<input type="number" id="vaporiw" value="<?php echo $def_val['ORI_W']; ?>" size="4" min="64" max="9999" <?php echo (!$def_val['ISRES'] ? 'readonly' : ''); ?>/>&nbsp;px&nbsp;
					<?php echo JText::_('VAPMANAGEMEDIA10'); ?>
					<input type="number" id="vaporih" value="<?php echo $def_val['ORI_H']; ?>" size="4" min="64" max="9999" <?php echo (!$def_val['ISRES'] ? 'readonly' : ''); ?>/>&nbsp;px &nbsp;&nbsp;
					<label for="vaporiresize"><?php echo JText::_('VAPMANAGEMEDIA8'); ?></label>
					<input type="checkbox" id="vaporiresize" value="1" onChange="resizeStatusChanged();" <?php echo (($def_val['ISRES']) ? 'checked="checked"' : '' ); ?>/>
				</span></td>
			</tr>
			
			<tr>
				<td width="125"><b><?php echo JText::_('VAPMANAGEMEDIA7');?>:</b> </td>
				<td><span>
					<?php echo JText::_('VAPMANAGEMEDIA9'); ?>
					<input type="number" id="vapsmallw" value="<?php echo $def_val['SML_W']; ?>" size="4" min="16" max="1024" />&nbsp;px&nbsp;
					<?php echo JText::_('VAPMANAGEMEDIA10'); ?>
					<input type="number" id="vapsmallh" value="<?php echo $def_val['SML_H']; ?>" size="4" min="16" max="1024" />&nbsp;px
				</span></td>
			</tr>
			
		</table>
	</p>
</div>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-langtag',
	array(
		'title'       => JText::_('VAPEMPLANGTRANSLATION') . '<span id="tag-target"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<script>

	var cont = <?php echo ($max_id+1); ?>;
	
	var DAYS = new Array();
	<?php for( $i = 1; $i <= 7; $i++ ) { ?>
		DAYS[<?php echo ($i-1); ?>] = '<?php echo JText::_('VAPDAY'.$i); ?>';
	<?php } ?>
	
	jQuery(document).ready(function() {
		
		jQuery('#vap-jusers-sel, #vap-timezone-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		// jQuery('#vap-group-sel').select2({
		// 	placeholder: '--',
		// 	allowClear: true,
		// 	width: 300
		// });

		jQuery('#vap-group-sel').on('change', function () {
		    if (jQuery('#vap-group-sel option:selected').text() == "PARKINGS") {
		        jQuery('input[name="firstname"]').attr('value', 'پارکینگ');
            }
            if (jQuery('#vap-group-sel option:selected').text() == "TABLES") {
                jQuery('input[name="firstname"]').attr('value', 'میز');
            }
            if (jQuery('#vap-group-sel option:selected').text() == "MEETING_ROOMS") {
                jQuery('input[name="firstname"]').attr('value', 'اتاق');
            }
        })

		jQuery('#vap-activeto-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});
		
		//jQuery('#vapactivetodate').val('<?php echo ($sel['active_to'] <= 0 ? $sel['active_to'] : date($date_format, $sel['active_to'])); ?>');
		jQuery('#vap-activeto-sel').on('change', function(){
			var val = jQuery(this).val();
			if( val == 'date' ) {
				jQuery('.vapactivetosp').show();
				jQuery('#vapactivetodate').val('');
				jQuery('#vapactivetodate').attr('data-alt-value', '');
			} else {
				jQuery('.vapactivetosp').hide();
				if( val == 'pending' ) {
					jQuery('#vapactivetodate').val(0);
					jQuery('#vapactivetodate').attr('data-alt-value', 0);
				} else {
					jQuery('#vapactivetodate').val(-1);
					jQuery('#vapactivetodate').attr('data-alt-value', -1);
				}
			}
		});
	});

	/*
	
	var FIRST_NAME_BLUR = <?php echo (empty($sel['nickname']) ? 0 : 1); ?>;
	var LAST_NAME_BLUR  = FIRST_NAME_BLUR;
	
	function firstNameFocusLost() {
		if (!FIRST_NAME_BLUR) {
			var fn = jQuery('#vapfirstnametext').val();
			if (fn.length > 0) {
				var val = jQuery('#vapnominativetext').val();
				if (val.length > 0) {
					val += ' ';
				}
				jQuery('#vapnominativetext').val(val+fn);
				FIRST_NAME_BLUR = true;
			}
		}
	}
	
	function lastNameFocusLost() {
		if (!LAST_NAME_BLUR) {
			var ln = jQuery('#vaplastnametext').val();
			if( ln.length > 0 ) {
				var val = jQuery('#vapnominativetext').val();
				if( val.length > 0 ) {
					val += ' ';
				}
				jQuery('#vapnominativetext').val(val+ln);
				LAST_NAME_BLUR = true;
			}
		}
	}
	*/
	
	function closedCheckValueChanged() {
		if (jQuery('#vapcheckclosed').is(':checked')) {
			jQuery('.vapfromtosp').hide();
		} else {
			jQuery('.vapfromtosp').fadeIn();
		}
	}
	
	// IMAGE LISTENER
	
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
		}).done(function(resp){
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
	
	var configure_media = <?php echo ($is_media_prop ? 0 : 1); ?>;
	
	function openMediaPropertiesDialog() {
		if (configure_media) {
			
			jQuery("#dialog-confirm").dialog({
				resizable: false,
				width: 480,
				height: 260,
				modal: true,
				buttons: {
					"<?php echo JText::_('VAPSAVE'); ?>": function() {
						jQuery( this ).dialog( "close" );
						storeMediaProperties();
						jQuery('#vapimagefileinput').click();
					},
					"<?php echo JText::_('VAPCANCEL'); ?>": function() {
						jQuery( this ).dialog( "close" );
					}
				}
			});
			
			if (configure_media) {
				return false;
			}
			
		}
		
	}
	
	function storeMediaProperties() {
		var prop = new Array(
			jQuery('#vaporiw').val(),
			jQuery('#vaporih').val(),
			jQuery('#vapsmallw').val(),
			jQuery('#vapsmallh').val(),
			jQuery('#vaporiresize').is(':checked') ? 1 : 0
		);
		
		configure_media = 0;
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=storeMediaProperties&tmpl=component",
			data: { oriwres: prop[0], orihres: prop[1], smallwres: prop[2], smallhres: prop[3], isresize: prop[4] }
		}).done(function(resp){
			
		});
	}
	
	function resizeStatusChanged() {
		var is = jQuery('#vaporiresize').is(':checked');
		jQuery('#vaporiw').prop('readonly', ((is) ? false : true) );
		jQuery('#vaporih').prop('readonly', ((is) ? false : true) );
	}
	
	function updateSyncKey() {
		var url_input = jQuery('#syncurl');
		if (url_input.length == 0) {
			return;
		}
		
		var url = url_input.val();
		url = url.substr(0, url.lastIndexOf('=')+1)+jQuery('#synckey').val();
		url_input.val(url);
	}

	// TRANSLATIONS
	
	var SELECTED_TAG = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'langtag') {
			url = 'index.php?option=com_vikappointments&task=managelangemployee&id_employee=<?php echo $sel['id']; ?>&tag=' + SELECTED_TAG;

			jQuery('#tag-target').text(' ' + SELECTED_TAG);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#employee_"]').on('click', function() {
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

	// form observer

	var formObserver = null;

	jQuery(document).ready(function() {

		formObserver = new VikFormObserver('#adminForm');

		callbackOn(
			// check if the NOTE editor is ready
			function() {
				return Joomla.editors.instances.hasOwnProperty('note');
			},
			// when ready, freeze the form
			function() {
				formObserver.exclude('input[name="cid[]"]')
					.exclude('input[name="task"]')
					.exclude('input[name="dayrule"]')
					.exclude('input[name="dayfrom"]')
					.exclude('input[name="tabname"]')
					.setCustom('textarea[name="note"]', function() {
						return Joomla.editors.instances.note.getValue();
					})
					.freeze();
			}
		);

	});
	
</script>
