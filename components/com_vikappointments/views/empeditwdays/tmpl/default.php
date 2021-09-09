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
ArasJoomlaVikApp::datePicker();
$auth       = $this->auth;
$employee   = $auth->getEmployee();

$worktime = $this->worktime;

$vik = UIApplication::getInstance();

$type = $worktime['id'] > 0 ? 2 : 1;

$worktime['from_hour'] 	= floor($worktime['fromts'] / 60);
$worktime['from_min'] 	= $worktime['fromts'] % 60;

$worktime['end_hour'] 	= floor($worktime['endts'] / 60);
$worktime['end_min'] 	= $worktime['endts'] % 60;

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');
$hour_format = str_replace(':i', '', $time_format);

$op_time = VikAppointments::getOpeningTime();
$cl_time = VikAppointments::getClosingTime();

/**
 * Dates must be displayed in UTC format.
 *
 * @since 1.6.2
 */
// $date_ts = $worktime['ts'] > 0 ? date($date_format, $worktime['ts']) : '';
$date_ts = $worktime['ts'] > 0 ? Hekmatinasser\Verta\Verta::instance($worktime['ts'])->format($date_format) : '';

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
		<h2><?php echo JText::_($type == 2 ? 'VAPEDITEMPWDAYTITLE' : 'VAPNEWEMPWDAYTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manageWorkDays()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveWorkDay(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
		
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveWorkDay(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>

		<?php if ($auth->manageWorkDays() && $type == 2) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveWorkDay();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseWorkDays();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditwdays'); ?>" method="post" name="empareaForm" id="empareaForm">

	<?php echo $vik->openEmptyFieldset(); ?>

		<?php echo $vik->openControl(JText::_('VAPEDITWD9').'*:'); ?>
			<select name="type" id="vaptype" class="vik-dropdown required">
				<option value="1" <?php echo ($worktime['day'] != -1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPWDTYPEOPT1'); ?></option>
				<option value="2" <?php echo ($worktime['day'] == -1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPWDTYPEOPT2'); ?></option>
			</select>
		<?php echo $vik->closeControl(); ?>
		
		<?php
		$control = array();
		$control['style'] = $worktime['day'] == -1 ? 'display: none;' : '';
		echo $vik->openControl(JText::_('VAPEDITWD1').'*:', 'vapweek-child', $control); ?>
			<select name="day" id="vapday" class="vik-dropdown required">
				<?php for ($i = 1; $i <= 7; $i++) {
					$val = $i < 7 ? $i : 0;
					?>
					<option value="<?php echo $val; ?>" <?php echo ($worktime['day'] == $val ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPDAY'.$i); ?></option>
				<?php } ?>
			</select>
		<?php echo $vik->closeControl(); ?>

		<?php echo $vik->openControl(JText::_('VAPEDITWD' . ($type == 2 ? 6 : 7)).'*:', 'vapday-child', $control); ?>
			<!--<input type="text" name="date_from" value="<?php echo $date_ts; ?>" id="vapdatefrom" class="calendar<?php echo $worktime['day'] == -1 ? ' required' : ''; ?> datepicker" />-->
    <?php echo $vik->calendar($date_ts, 'date_from', 'vapdatefrom'); ?>
        <?php echo $vik->closeControl(); ?>

		<?php if ($type == 1) { ?>

			<?php echo $vik->openControl(JText::_('VAPEDITWD8').'*:', 'vapday-child', $control); ?>
				<!--<input type="text" name="date_to" value="<?php echo $date_ts; ?>" id="vapdateto" class="calendar<?php echo $worktime['day'] == -1 ? ' required' : ''; ?> datepicker" />-->
            <?php echo $vik->calendar($date_ts, 'date_from', 'vapdatefrom'); ?>
            <?php echo $vik->closeControl(); ?>

		<?php } ?>

		<?php echo $vik->openControl(JText::_('VAPEDITWD2').'*:'); ?>
			<select name="from_hour" id="vapfromhour" class="vik-dropdown-small required" <?php echo ($worktime['closed'] ? 'disabled' : ''); ?>>
				<?php for ($h = $op_time['hour']; $h <= $cl_time['hour']; $h++) { ?>
					<option value="<?php echo $h; ?>" <?php echo ($worktime['from_hour'] == $h ? 'selected="selected"' : ''); ?>>
						<?php echo ArasJoomlaVikApp::jdate($hour_format, ArasJoomlaVikApp::jmktime($h, 0, 0, 1, 1, 1398)); ?>
					</option>
				<?php } ?>
			</select>

			<select name="from_min" id="vapfrommin" class="vik-dropdown-small required" <?php echo ($worktime['closed'] ? 'disabled' : ''); ?>>
				<?php for ($m = 0; $m < 60; $m += 5) { ?>
					<option value="<?php echo $m; ?>" <?php echo ($worktime['from_min'] == $m ? 'selected="selected"' : ''); ?>>
						<?php echo ArasJoomlaVikApp::jdate("i", ArasJoomlaVikApp::jmktime(0, $m, 0, 1, 1, 1398)); ?>
					</option>
				<?php } ?>
			</select>
		<?php echo $vik->closeControl(); ?>

		<?php echo $vik->openControl(JText::_('VAPEDITWD3').'*:'); ?>
			<select name="end_hour" id="vapendhour" class="vik-dropdown-small required" <?php echo ($worktime['closed'] ? 'disabled' : ''); ?>>
				<?php for ($h = $op_time['hour']; $h <= $cl_time['hour']; $h++) { ?>
					<option value="<?php echo $h; ?>" <?php echo ($worktime['end_hour'] == $h ? 'selected="selected"' : ''); ?>>
						<?php echo ArasJoomlaVikApp::jdate($hour_format, ArasJoomlaVikApp::jmktime($h, 0, 0, 1, 1, 1398)); ?>
					</option>
				<?php } ?>
			</select>

			<select name="end_min" id="vapendmin" class="vik-dropdown-small required" <?php echo ($worktime['closed'] ? 'disabled' : ''); ?>>
				<?php for ($m = 0; $m < 60; $m += 5) { ?>
					<option value="<?php echo $m; ?>" <?php echo ($worktime['end_min'] == $m ? 'selected="selected"' : ''); ?>>
						<?php echo ArasJoomlaVikApp::jdate("i", ArasJoomlaVikApp::jmktime(0, $m, 0, 1, 1, 1398)); ?>
					</option>
				<?php } ?>
			</select>
		<?php echo $vik->closeControl(); ?>

		<?php echo $vik->openControl(JText::_('VAPEDITWD5').':'); ?>
			<select name="closed" id="vapclosed" class="vik-dropdown-small">
				<option value="1" <?php echo ($worktime['closed'] == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPYES'); ?></option>
				<option value="0" <?php echo ($worktime['closed'] == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPNO'); ?></option>
			</select>
		<?php echo $vik->closeControl(); ?>

		<?php echo $vik->openControl(JText::_('VAPEDITWD10').':'); ?>
			<select name="id_location" id="vap-locations-sel" <?php echo ($worktime['closed'] ? 'disabled' : ''); ?>>
				<option></option>
				<?php foreach ($this->locations as $id_emp => $list) { ?>

					<optgroup label="<?php echo ($id_emp > 0 ? $employee['nickname'] : JText::_('VAPEMPSETTINGSGLOBAL')); ?>">
					
						<?php foreach ($list as $l) { ?>
						
							<option value="<?php echo $l['id']; ?>" <?php echo ($l['id'] == $worktime['id_location'] ? 'selected="selected"' : ''); ?>><?php echo $l['label']; ?></option>

						<?php } ?>

					</optgroup>

				<?php } ?>
			</select>
		<?php echo $vik->closeControl(); ?>

		<?php
		if ($this->services)
		{
			echo $vik->openControl(JText::_('VAPEDITWD11').':');
			?>
			<select name="services[]" multiple id="vap-services-sel">
				<?php echo JHtml::_('select.options', $this->services, 'id', 'name'); ?>
			</select>
			<?php
			// display popover
			echo $vik->createPopover(array(
				'title'   => JText::_('VAPEDITWD11'),
				'content' => JText::_('VAPEDITWD11_HELP'),
			));

			echo $vik->closeControl();
		}
		?>
		
	<?php echo $vik->closeEmptyFieldset(); ?>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" />
	
	<input type="hidden" name="id" value="<?php echo $worktime['id']; ?>" />
	<input type="hidden" name="task" value="empeditwdays.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>"/>
</form>

<?php
JText::script('VAPUSEALLSERVICES');
?>

<script>
	
	function vapCloseWorkDays() {
		document.location.href = '<?php echo JRoute::_("index.php?option=com_vikappointments&view=empwdays&Itemid={$itemid}", false); ?>';
	}

	var validator = new VikFormValidator('#empareaForm');
	
	<?php if ($auth->manageWorkDays()) { ?>

		function vapSaveWorkDay(close) {
			
			if (validator.validate()) {
				if(close) {
					jQuery('#vaphiddenreturn').val('1');
				}
				
				document.empareaForm.submit();
			}
		}

	<?php } ?>
	
	<?php if ($auth->manageWorkDays() && $type == 2) { ?>

		function vapRemoveWorkDay() {
			document.location.href = '<?php echo JRoute::_("index.php?option=com_vikappointments&task=empeditwdays.delete&cid[]={$worktime['id']}&Itemid={$itemid}", false); ?>';
		}

	<?php } ?>
	
	jQuery(document).ready(function() {

		jQuery(".vik-dropdown").select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 200
		});
		jQuery(".vik-dropdown-small").select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100
		});

		jQuery("#vap-locations-sel").select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery("#vap-services-sel").select2({
			placeholder: Joomla.JText._('VAPUSEALLSERVICES'),
			allowClear: true,
			width: 300
		});

		jQuery('#vaptype').on('change', function() {

			if (jQuery(this).val() == 2) {

				jQuery('.vapday-child input').addClass('required');
				validator.registerFields('.vapday-child input');

				validator.unregisterFields('.vapweek-child select');
				jQuery('.vapweek-child select').removeClass('required');

				jQuery('.vapday-child').show();
				jQuery('.vapweek-child').hide();
			} else {

				jQuery('.vapweek-child select').addClass('required');
				validator.registerFields('.vapweek-child select');

				validator.unregisterFields('.vapday-child input');
				jQuery('.vapday-child input').removeClass('required');

				jQuery('.vapweek-child').show();
				jQuery('.vapday-child').hide();
			}

		});

		<?php if ($type == 1) { ?>

			jQuery('#vapdatefrom').on('change', function() {
				var from = jQuery(this).val();
				var to = jQuery('#vapdateto').val();

				if (to.length == 0 || new Date(from).getTime() > new Date(to).getTime()) {
					jQuery('#vapdateto').val(from);
				}

				jQuery('#vapdatefrom, #vapdateto').trigger('blur');
			});

			jQuery('#vapdateto').on('change', function() {
				var from = jQuery('#vapdatefrom').val();
				var to = jQuery(this).val();

				if (from.length == 0 || new Date(from).getTime() > new Date(to).getTime()) {
					jQuery('#vapdatefrom').val(to);
				}

				jQuery('#vapdatefrom, #vapdateto').trigger('blur');
			});

		<?php } else { ?>

			jQuery('#vapdatefrom').on('change', function() {
				jQuery(this).trigger('blur');
			});

		<?php } ?>

		jQuery('#vapclosed').on('change', function() {
			jQuery('#vapfromhour, #vapfrommin, #vapendhour, #vapendmin, #vap-locations-sel').prop('disabled', (jQuery(this).val() == 1 ? true : false));
		});
		
	});

	jQuery(document).ready(function() {

		jQuery(function() {

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
		
			jQuery("#vapdatefrom:input, #vapdateto:input").datepicker({
				dateFormat: new Date().format,
			});
		
		});

	});
	
</script>
