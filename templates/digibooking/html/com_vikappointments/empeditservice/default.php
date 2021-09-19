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

$auth 		= $this->auth;
$employee 	= $auth->getEmployee();

$groups  = $this->groups;
$service = $this->service;

$vik = UIApplication::getInstance();

$editor = JFactory::getEditor();

$type = $service['id'] > 0 ? 2 : 1;

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');

$service['start_publishing'] = $service['start_publishing'] > 0 ? date($date_format, $service['start_publishing']) 	: '';
$service['end_publishing'] 	 = $service['end_publishing'] > 0 	? date($date_format, $service['end_publishing'])	: '';

$itemid = JFactory::getApplication()->input->getInt('Itemid');

?>
	
<?php
/**
 * The employees area toolbar is displayed from the layout below:
 * /components/com_vikappointments/layouts/emparea/toolbar.php
 * 
 * If you need to change something from this layout, just create
 * an override of this layout by following the instructions below:
 * - open the back-end of your Joomla
 * - visit the Extensions > Templates > Templates page
 * - edit the active template
 * - access the "Create Overrides" tab
 * - select Layouts > com_vikappointments > emparea
 * - start editing the toolbar.php file on your template to create your own layout
 *
 * @since 1.6
 */
echo JLayoutHelper::render('emparea.toolbar', array('active' => false));
?>

<div class="vapeditempheaderdiv">
	<div class="vapeditemptitlediv">
		<h2><?php echo JText::_($type == 2 ? 'VAPEDITSERTITLE' : 'VAPNEWSERTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if (($auth->manageServices($service) && $type == 2) || ($auth->create() && $type == 1)) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveService(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveService(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>

		<?php if ($auth->remove() && $type == 2) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveService();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseService();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditservice'); ?>" method="post" enctype="multipart/form-data" name="empareaForm" id="empareaForm">
	
	<?php echo $vik->bootStartTabSet('set', array('active' => 'set_details')); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('set', 'set_details', JText::_('VAPORDERTITLE2')); ?>

			<?php echo $vik->openEmptyFieldset(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE2').'*:'); ?>
					<input type="text" name="name" value="<?php echo $this->escape($service["name"]); ?>" size="40" id="vapname" class="required" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE4').':'); ?>
					<input type="number" name="duration" value="<?php echo $service["duration"]; ?>" size="10" min="1" max="99999999" id="vapdurationinput" onChange="vapDurationValueChanged();" />
					<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE16').':'); ?>
					<input type="number" name="sleep" value="<?php echo $service["sleep"]; ?>" size="10" min="-9999999" max="99999999" id="vapsleepinput" onChange="vapDurationValueChanged();" />
					<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE17').':'); ?>
					<select name="interval" class="vik-dropdown" id="vap-duration-sel">
						<option value="1" <?php echo ( ( $service["interval"] == 1) ? 'selected="selected"' : '' ); ?>><?php echo JText::sprintf("VAPSERVICETIMESLOTSLEN1", ($service['duration']+$service['sleep'])); ?></option>
						<option value="0" <?php echo ( ( $service["interval"] == 0) ? 'selected="selected"' : '' ); ?>><?php echo JText::sprintf("VAPSERVICETIMESLOTSLEN2", VikAppointments::getMinuteIntervals()); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE5').':'); ?>
					<input type="number" name="price" value="<?php echo $service["rate"]; ?>" size="10" min="0" max="99999999" step="any" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE18').':'); ?>
					<input type="number" name="max_capacity" value="<?php echo $service["max_capacity"]; ?>" size="10" min="<?php echo $service["max_per_res"]; ?>" max="999999" onChange="vapMaximumCapacityChanged();" />
				<?php echo $vik->closeControl(); ?>
				
				<?php
				$control = array();
				$control['style'] = $service['max_capacity'] <= 1 ? 'display: none;' : '';
				echo $vik->openControl(JText::_('VAPEDITSERVICE19').':', 'vaptrmaxcapchild', $control); ?>
					<input type="number" name="min_per_res" value="<?php echo $service["min_per_res"]; ?>" size="10" min="1" max="<?php echo $service["max_per_res"]; ?>" onChange="vapMinimumPeopleChanged();" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE20').':', 'vaptrmaxcapchild', $control); ?>
					<input type="number" name="max_per_res" value="<?php echo $service["max_per_res"]; ?>" size="10" min="<?php echo $service["min_per_res"]; ?>" max="<?php echo $service["max_capacity"]; ?>" onChange="vapMaximumPeopleChanged();" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE21').':', 'vaptrmaxcapchild', $control); ?>
					<select name="priceperpeople" class="vik-dropdown-small">
						<option value="1" <?php echo ( ( $service["priceperpeople"] == 1) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPYES"); ?></option>
						<option value="0" <?php echo ( ( $service["priceperpeople"] == 0) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPNO"); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE6').':'); ?>
					<select name="published" class="vik-dropdown-small">
						<option value="1" <?php echo ( ( $service["published"] == 1) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPYES"); ?></option>
						<option value="0" <?php echo ( ( $service["published"] == 0) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPNO"); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE25').':'); ?>
					<input type="text" name="start_publishing" id="vapdatestart" size="20" value="<?php echo $service['start_publishing']; ?>" class="calendar" />
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE26').':'); ?>
					<input type="text" name="end_publishing" id="vapdateend" size="20" value="<?php echo $service['end_publishing']; ?>" class="calendar" />
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE27').':'); ?>
					<select name="has_own_cal" class="vik-dropdown-small">
						<option value="1" <?php echo ( ( $service["has_own_cal"] == 1) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPYES"); ?></option>
						<option value="0" <?php echo ( ( $service["has_own_cal"] == 0) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPNO"); ?></option>
					</select>

					<?php
					echo $vik->createPopover(array(
						'title' => JText::_('VAPEDITSERVICE27'),
						'content' => JText::_('VAPHASOWNCALMESSAGE'),
					));
					?>

				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE24').':'); ?>
					<select name="enablezip" class="vik-dropdown-small">
						<option value="1" <?php echo ( ( $service["enablezip"] == 1) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPYES"); ?></option>
						<option value="0" <?php echo ( ( $service["enablezip"] == 0) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPNO"); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE22').':'); ?>
					<select name="use_recurrence" class="vik-dropdown-small">
						<option value="1" <?php echo ( ( $service["use_recurrence"] == 1) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPYES"); ?></option>
						<option value="0" <?php echo ( ( $service["use_recurrence"] == 0) ? 'selected="selected"' : '' ); ?>><?php echo JText::_("VAPNO"); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE7').':'); ?>
					<span id="vapimagesp">
						<input type="file" name="image" size="35" />
						<?php if (!empty($service['image']) && file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $service['image'])) { ?>
							<a href="javascript: void(0);" class="vapmodal" onClick="vapOpenModalImage('<?php echo VAPMEDIA_URI . $service['image']; ?>');"><?php echo $service['image']; ?></a>
						<?php } ?>
					</span>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE8').':'); ?>
					<select name="group" class="vap-group-select">
					<option value=""></option>
					
					<?php foreach ($groups as $g) { ?>
						<option value="<?php echo $g['id']; ?>" <?php echo ($g['id'] == $service['id_group'] ? 'selected="selected"' : ''); ?>><?php echo $g['name']; ?></option>
					<?php } ?>

					</select>
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeEmptyFieldset(); ?>
	
		<?php echo $vik->bootEndTab(); ?>

		<!-- DESCRIPTION -->

		<?php echo $vik->bootAddTab('set', 'set_description', JText::_('VAPEDITSERVICE3')); ?>
			
			<?php echo $vik->openEmptyFieldset(); ?>
				
				<div class="control-group">
					<?php echo $editor->display('description', $service['description'], 400, 200, 70, 20); ?>
				</div>
			
			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" />
	
	<input type="hidden" name="id" value="<?php echo $service['id']; ?>" />
	<input type="hidden" name="task" value="empeditservice.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<?php
JText::script('VAPCONFDIALOGMSG');
?>

<script>
	
	jQuery(document).ready(function() {

		jQuery('.vap-group-select').select2({
			placeholder: '<?php echo addslashes(JText::_("VAPSERVICENOGROUP")); ?>',
			allowClear: true,
			width: 300
		});

		jQuery('.vik-dropdown').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300
		});

		jQuery('.vik-dropdown-small').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100
		});

	});
	
	function vapCloseService() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empserviceslist&Itemid=' . $itemid, false); ?>';
	}

	var validator = new VikFormValidator('#empareaForm');
	
	<?php if (($auth->create() && $type == 1) || ($auth->manageServices($service) && $type == 2)) { ?>
		
		function vapSaveService(close) {
			
			callback = function() {

				var dInput = jQuery('input[name="duration"]');
				var sInput = jQuery('input[name="sleep"]');
				
				var duration 	= parseInt(dInput.val());
				var sleep 		= parseInt(sInput.val());

				if (isNaN(duration)) {
					duration = 0;
				}

				if (isNaN(sleep)) {
					sleep = 0;
				}

				if (duration > 0 && duration + sleep > 5 ) {
					validator.unsetInvalid(dInput);
					return true;
				}

				validator.setInvalid(dInput);
				return false;
			}

			if (validator.validate(callback)) {
				if (close) {
					jQuery('#vaphiddenreturn').val('1');
				}
				
				document.empareaForm.submit();
			}
		}

	<?php } ?>
	
	<?php if ($auth->remove() && $type == 2) { ?>

		function vapRemoveService() {

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			document.location.href = '<?php echo JRoute::_("index.php?option=com_vikappointments&task=empeditservice.delete&cid[]=" . $service['id'] . "&Itemid=" . $itemid, false); ?>';
		}

	<?php } ?>

	var old_dur_sleep_value = <?php echo ($service['duration']+$service['sleep']); ?>;
	
	function vapDurationValueChanged() {
		var duration = parseInt(jQuery('#vapdurationinput').val());
		var sleep = parseInt(jQuery('#vapsleepinput').val());

		if (isNaN(duration)) {
			duration = 0;
		}

		if (isNaN(sleep)) {
			sleep = 0;
		}

		var label = jQuery('#vap-duration-sel').children()[0].text;
		
		//jQuery('#vaptslenlabel').text( jQuery('#vaptslenlabel').text().replace(old_dur_sleep_value, (duration+sleep)) );
		jQuery('#vap-duration-sel').children()[0].text = label.replace(old_dur_sleep_value, (duration+sleep));
		old_dur_sleep_value = parseInt(duration+sleep);

		jQuery('#vap-duration-sel').select2("destroy");
		jQuery('#vap-duration-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300
		});
	}
	
	function vapMaximumCapacityChanged() {
		var max = vapGetByName('max_capacity').val();
		
		if( max <= 1 ) {
			jQuery('.vaptrmaxcapchild').fadeOut('fast');
		} else {
			jQuery('.vaptrmaxcapchild').fadeIn('fast');
		}
		
		vapGetByName('max_per_res').prop('max', max );
	}
	
	function vapMinimumPeopleChanged() {
		var min = parseInt(vapGetByName('min_per_res').val());

		if (isNaN(min)) {
			min = 1;
		}

		vapGetByName('max_per_res').prop('min', min );
	}
	
	function vapMaximumPeopleChanged() {
		var max = parseInt(vapGetByName('max_per_res').val());

		if (isNaN(max)) {
			max = 1;
		}

		vapGetByName('min_per_res').prop('max', max );
		vapGetByName('max_capacity').prop('min', max );
	}
	
	function vapGetByName(name) {
		return jQuery(':input[name='+name+']');
	}

	// datepicker

	jQuery(document).ready(function() {

		jQuery(function(){
			var sel_format 	 = "<?php echo $date_format; ?>";
			var df_separator = sel_format[1];

			sel_format = sel_format.replace(new RegExp("\\"+df_separator, 'g'), "");

			if (sel_format == "Ymd") {
				Date.prototype.format = "yy"+df_separator+"mm"+df_separator+"dd";
			} else if (sel_format == "mdY") {
				Date.prototype.format = "mm"+df_separator+"dd"+df_separator+"yy";
			} else {
				Date.prototype.format = "dd"+df_separator+"mm"+df_separator+"yy";
			}
		
			var today = new Date();
		
			jQuery("#vapdatestart:input, #vapdateend:input").datepicker({
				dateFormat: new Date().format,
			});
		});

	});
	
</script>
