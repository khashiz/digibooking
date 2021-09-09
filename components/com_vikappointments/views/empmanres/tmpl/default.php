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

$auth 		= $this->auth;
$employee 	= $auth->getEmployee();

$sel = $this->row;

$cfields = $this->customFields;
$options = $this->options;
$res_opt = $this->res_opt;

$editor = JFactory::getEditor();

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

// SET EMPLOYEE TIMEZONE
VikAppointments::setCurrentTimezone($employee['timezone']);

$cf_data = (array) json_decode($sel['custom_f'], true);

$option_select = "";
if (count($options))
{
	$option_select = '<select id="vapoptionselect" onChange="vapOptionValueChanged();" class="vik-dropdown">';
	foreach ($options as $o)
	{
		$option_select .= '<option value="'.$o['id'].'">'.$o['name'].'</option>';
	}
	$option_select .= '<select>';
}

$last_id = 0;

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$uploaded_files = json_decode($sel['uploads'], true);

if (empty($sel['purchaser_country']))
{
	foreach ($cfields as $cf)
	{
		if ($cf['type'] == 'text' && VAPCustomFields::isPhoneNumber($cf))
		{
			$sel['purchaser_country'] = $cf['choose'];
			break;
		}
	}
}

if (empty($sel['id_user']))
{
	$sel['id_user'] = '';
}

$itemid = JFactory::getApplication()->input->getInt('Itemid', 0);

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
		<h2><?php echo JText::_((!empty($sel['id'])) ? 'VAPEDITRESTITLE' : 'VAPNEWRESTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if( ($auth->resmanage() && !empty($sel['id'])) || ($auth->rescreate()) ) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveReservation(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveReservation(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>
		<div class="vapempbtn">
			<button type="button" onClick="vapCloseReservation();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empmanres&Itemid=' . $itemid); ?>" method="post" enctype="multipart/form-data" name="empareaForm" id="empareaForm">
	
	<?php echo $vik->bootStartTabSet('set', array('active' => 'set_details')); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('set', 'set_details', JText::_('VAPMANAGERESERVATIONTITLE1')); ?>

			<?php echo $vik->openEmptyFieldset('box-right box50'); ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION3').'*:'); ?>
					<input type="text" class="required" value="<?php echo $this->escape($employee['nickname']); ?>" size="40" readonly />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION4').'*:'); ?>
					<input type="text" class="required" value="<?php echo $this->escape($sel['sname']); ?>" size="40" readonly />
				<?php echo $vik->closeControl(); ?>

				<?php
				// SET EMPLOYEE TIMEZONE
				VikAppointments::setCurrentTimezone($employee['timezone']);
				?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION5').'*:'); ?>
					<input type="text" class="required" value="<?php echo ArasJoomlaVikApp::jdate($date_format . ' ' . $time_format, $sel['checkin_ts']); ?>" size="40" readonly />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION6').'*:'); ?>
					<?php $checkout = VikAppointments::getCheckout($sel['checkin_ts'], $sel['duration']); ?>
					<input type="text" class="required" value="<?php echo ArasJoomlaVikApp::jdate($date_format . ' ' . $time_format, $checkout); ?>" size="40" readonly />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(''); ?>
					<a href="<?php echo JRoute::_("index.php?option=com_vikappointments&service={$sel['id_service']}&view=emplogin&id_res={$sel['id']}&last_day={$sel['day_ts']}&Itemid={$itemid}"); ?>" class="vap-btn large blue"><?php echo JText::_('VAPMANAGERESERVATION7'); ?></a>
				<?php echo $vik->closeControl(); ?>

				<?php if ($sel['max_capacity'] > 1) { ?>
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION25').':'); ?>
						<input type="number" name="people" value="<?php echo (!empty($sel['people']) ? $sel['people']: 1); ?>" size="40" min="1" max="9999999" />
					<?php echo $vik->closeControl(); ?>
				<?php } ?>

				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION9').':'); ?>
					<input type="number" name="total_cost" value="<?php echo $sel['total_cost']; ?>" size="10" min="0" max="9999999999" step="any" id="vaprtotalcost" />
					&nbsp;<?php echo VikAppointments::getCurrencySymb(); ?>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION10').':'); ?>
					<input type="number" name="duration" value="<?php echo $sel['duration']; ?>" size="10" min="0" max="9999999999" id="vaprduration" />
					&nbsp;<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeEmptyFieldset(); ?>
	
			<?php echo $vik->openEmptyFieldset('box-left box50'); ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION28').':'); ?>
					<input type="hidden" name="id_user" class="vap-users-select" value="<?php echo $sel['id_user']; ?>" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION27').':'); ?>
					<input type="text" id="vapname" name="purchaser_nominative" value="<?php echo $this->escape($sel['purchaser_nominative']); ?>" size="40" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION8').':'); ?>
					<input type="text" id="vapemail" name="purchaser_mail" value="<?php echo $sel['purchaser_mail']; ?>" size="40" onBlur="vapComposeMailFields();" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION26').':'); ?>
					
					<?php 
					echo '<select name="phone_prefix" class="vap-phones-select">';
					foreach ($this->countries as $i => $ctry)
					{
						$suffix = "";
						if (($i != 0 && $this->countries[$i-1]['phone_prefix'] == $ctry['phone_prefix'])
							|| ($i != count($this->countries)-1 && $this->countries[$i+1]['phone_prefix'] == $ctry['phone_prefix']))
						{
							$suffix = ' : '.$ctry['country_2_code'];
						}

						echo '<option value="'.$ctry['id']."_".$ctry['country_2_code'].'" title="'.trim($ctry['country_name']).'" '.($sel['purchaser_country'] == $ctry['country_2_code'] ? 'selected="selected"' : '').'>'.$ctry['phone_prefix'].$suffix.'</option>';
					}
					echo '</select>';
					?>

					<input type="text" id="vapphone" name="purchaser_phone" value="<?php echo $sel['purchaser_phone']; ?>" size="40" onBlur="vapComposePhoneFields();" style="width: 144px;margin-left: 5px;" />
					
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION11').':'); ?>
					<select name="paid" class="vik-dropdown-small">
						<option value="1" <?php echo ($sel['paid'] ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPYES'); ?></option>
						<option value="0" <?php echo (!$sel['paid'] ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPNO'); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php
				$status_list = array(
					'PENDING',
					'REMOVED',
					'CANCELED',
				);

				/**
				 * @note 	If the status was already confirmed, resconfirm() will return 
				 * 			true even if the rule is disabled.
				 *
				 * @since 	1.6
				 */ 
				if ($auth->resconfirm($sel['id']))
				{
					// the employee is allowed to confirm reservation (or it was already confirmed)
					array_unshift($status_list, 'CONFIRMED');
				}

				?>
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION19').':'); ?>
					<select name="status" class="vik-dropdown">
						<?php foreach ($status_list as $s) { ?>
							<option value="<?php echo $s; ?>" <?php echo $s == $sel['status'] ? 'selected="selected"' : ''; ?>><?php echo JText::_('VAPSTATUS' . $s); ?></option>
						<?php } ?>
					<select>
				<?php echo $vik->closeControl(); ?>

				<?php if (count($this->payments)) { ?>
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION13').':'); ?>
						<select name="id_payment" class="vap-payment-select">
							<option></option>';
							<?php foreach ($this->payments as $p) { ?>
								<option value="<?php echo $p['id']; ?>" <?php echo $p['id'] == $sel['id_payment'] ? 'selected="selected"' : ''; ?>><?php echo $p['name']; ?></option>
							<?php } ?>
						</select>
					<?php echo $vik->closeControl(); ?>
				<?php } ?>
				
				<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION24').':'); ?>
					<select name="notifycust" class="vik-dropdown-small">
						<option value="1"><?php echo JText::_('VAPYES'); ?></option>
						<option value="0" selected="selected"><?php echo JText::_('VAPNO'); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

		<!-- CUSTOM FIELDS -->

		<?php echo $vik->bootAddTab('set', 'set_custfields', JText::_('VAPMANAGERESERVATIONTITLE2')); ?>
	
			<?php if (count($cfields)) { ?>

				<?php echo $vik->openEmptyFieldset(); ?>

					<?php
					foreach ($cfields as $cf)
					{
						if (!empty($cf['poplink']))
						{
							$fname = "<a href=\"" . $cf['poplink'] . "\" id=\"vapcf" . $cf['id'] . "\" rel=\"{handler: 'iframe', size: {x: 750, y: 600}}\" target=\"_blank\" class=\"modal\">" . JText::_($cf['name']) . "</a>";
						}
						else
						{
							$fname = "<span id=\"vapcf" . $cf['id'] . "\">" . JText::_($cf['name']) . "</span>";
						}
						
						$_val = "";
						if (count($cf_data) > 0 && !empty($cf_data[$cf['name']]))
						{
							/**
							 * Prevent XSS attacks by escaping submitted data.
							 *
							 * @since 1.6.3
							 */
							$_val = $this->escape($cf_data[$cf['name']]);
						}

						if (VAPCustomFields::isSeparator($cf))
						{
							echo '<div class="control-group"><h3>' . $fname . '</h3>';
						}
						else
						{
							echo $vik->openControl($fname . ':');
						}
						
						if (VAPCustomFields::isInputText($cf))
						{
							$input_class = '';
							
							if (VAPCustomFields::isEmail($cf))
							{
								$input_class = 'vapemailfield';
							}
							else if (VAPCustomFields::isPhoneNumber($cf))
							{
								$input_class = 'vapphonefield';
							}
							
							$text_width = '';
							
							if (VAPCustomFields::isPhoneNumber($cf))
							{
								$text_width = 'width: 168px !important';
								echo '<select name="vapcf'.$cf['id'].'_prfx" class="vap-phones-select">';
								foreach ($this->countries as $i => $ctry) {
									$suffix = "";
									if (($i != 0 && $this->countries[$i-1]['phone_prefix'] == $ctry['phone_prefix']) 
										|| ($i != count($this->countries) - 1 && $this->countries[$i+1]['phone_prefix'] == $ctry['phone_prefix'])) {
										$suffix = ' : '.$ctry['country_2_code'];
									}
									echo '<option value="'.$ctry['id']."_".$ctry['country_2_code'].'" title="'.trim($ctry['country_name']).'" '.($sel['purchaser_country'] == $ctry['country_2_code'] ? 'selected="selected"' : '').'>'.$ctry['phone_prefix'].$suffix.'</option>';
								}
								echo '</select>';
							}    
							?>
							<input type="text" name="vapcf<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" class="<?php echo $input_class; ?>" size="40" style="<?php echo $text_width; ?>" data-name="<?php echo $cf['name']; ?>"/>
								
						<?php } else if (VAPCustomFields::isTextArea($cf)) { ?>
							
							<textarea name="vapcf<?php echo $cf['id']; ?>" style="width: 40%;height: 100px;max-width: 80%;" class="vaptextarea"><?php echo $_val; ?></textarea>
						
						<?php } else if (VAPCustomFields::isInputNumber($cf)) { ?>

							<input type="number" name="vapcf<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" />

						<?php } else if (VAPCustomFields::isCalendar($cf)) { ?>
							
							<input type="text" name="vapcf<?php echo $cf['id']; ?>" id="vapcfinput<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" size="25" class="vapinput calendar" />

							<?php $this->includeCalendarScript($date_format); ?>
						
						<?php } else if (VAPCustomFields::isSelect($cf)) { ?>

							<?php
							$choose = array_filter(explode(";;__;;", $cf['choose']));
							$values = $cf['multiple'] ? json_decode($_val ? $_val : '[]') : array($_val);
							?>

							<select 
								name="vapcf<?php echo $cf['id'] . ($cf['multiple'] ? '[]' : ''); ?>"
								class="vap-cf-select"
								<?php echo $cf['multiple'] ? 'multiple' : ''; ?>
							>

								<?php foreach ($choose as $aw) { ?>

									<option value="<?php echo $aw; ?>" <?php echo (in_array($aw, $values) ? 'selected="selected"' : ''); ?>><?php echo $aw; ?></option>

								<?php } ?>

							</select>

						<?php } else if (VAPCustomFields::isSeparator($cf)) { ?>
							
						<?php } else if (VAPCustomFields::isInputFile($cf)) { ?>
							
							<?php if (!empty($uploaded_files[JText::_($cf['name'])]) && file_exists(VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . $uploaded_files[JText::_($cf['name'])])) { ?>
								<a href="<?php echo VAPCUSTOMERS_UPLOADS_URI . $uploaded_files[JText::_($cf['name'])]; ?>" target="_blank">
									<i class="fa fa-cloud-download big" style="font-size: 26px;"></i>
								</a>
							<?php } ?>

						<?php } else if (VAPCustomFields::isCheckbox($cf)){ ?>

							<input type="checkbox" name="vapcf<?php echo $cf['id']; ?>" value="1" <?php echo ($_val ? 'checked="checked"' : ''); ?> />
						
						<?php } ?>
						
						<?php
						if (VAPCustomFields::isSeparator($cf))
						{
							echo '</div>';
						}
						else
						{
							echo $vik->closeControl();
						}
						?>
						
					<?php } ?>
				
				<?php echo $vik->closeEmptyFieldset(); ?>

			<?php } ?>

		<?php echo $vik->bootEndTab(); ?>

		<!-- OPTIONS -->

		<?php if (count($options)) { ?>

			<?php echo $vik->bootAddTab('set', 'set_options', JText::_('VAPMANAGERESERVATIONTITLE3')); ?>

				<?php echo $vik->openEmptyFieldset('box-left box60'); ?>
					
					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION14').':'); ?>
						<?php echo $option_select; ?>
					<?php echo $vik->closeControl(); ?>
					
					<?php
					$control = array();
					$control['style'] = 'display: none';
					echo $vik->openControl(JText::_('VAPMANAGERESERVATION31').':', 'vap-option-vars', $control); ?>
						<select id="vapoptvar" class="vik-dropdown" onChange="vapVariationValueChanged();"></select>
					<?php echo $vik->closeControl(); ?>

					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION15').':'); ?>
						<input type="number" value="" name="opt_price" size="6" min="0" max="999999" step="any" id="vapoprice" />
						&nbsp;<?php echo VikAppointments::getCurrencySymb(true); ?>
					<?php echo $vik->closeControl(); ?>

					<?php echo $vik->openControl(JText::_('VAPMANAGERESERVATION17').':'); ?>
						<input type="number" value="" name="opt_quant" size="6" min="1" max="999999" id="vapoquant" />
					<?php echo $vik->closeControl(); ?>

					<?php echo $vik->openControl(JText::_('')); ?>
						<button type="button" class="vap-btn large blue" onClick="vapAddSelectedOption();" id="vapobutton"><?php echo JText::_('VAPMANAGERESERVATION18');?></button>
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeEmptyFieldset(); ?>

				<?php echo $vik->openEmptyFieldset('box-right box40'); ?>

					<div id="vapoptioncont">
						<?php foreach ($res_opt as $ro) { ?>
							<div id="vaporow<?php echo $ro['id']; ?>" class="vap-res-option-row">
								<span class="name-block">
									<input type="text" readonly value="<?php echo $ro['name'] . (strlen($ro['var_name']) ? ' - ' . $ro['var_name'] : ''); ?>" style="width: 100%;" />
								</span>
								<span class="quantity-block">
									x<?php echo $ro['quantity']; ?>
								</span>
								<span class="price-block">
									<?php echo VikAppointments::printPriceCurrencySymb($ro['inc_price']); ?>
								</span>
								<span class="remove-block">
									<a href="javascript: void(0);" onClick="vapRemoveOptionRow(<?php echo $ro['id']; ?>)">
										<i class="fa fa-times big"></i>
									</a>
								</span>
								<input type="hidden" id="vapoprice<?php echo $ro['id']; ?>" value="<?php echo $ro['inc_price']; ?>" />
							</div>
							<?php 
							
							$last_id = $ro['id'];
							
							?>
							
						<?php } ?>
					</div>

				<?php echo $vik->closeEmptyFieldset(); ?>

			<?php echo $vik->bootEndTab(); ?>

		<?php } ?>

		<!-- NOTES -->

		<?php echo $vik->bootAddTab('set', 'set_notes', JText::_('VAPMANAGERESERVATIONTITLE4')); ?>
	
			<div class="control-group">
				<?php echo $editor->display('notes', $sel['notes'], 400, 200, 70, 20); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id_service" value="<?php echo $sel['id_service']; ?>" />
	<input type="hidden" name="checkin_ts" value="<?php echo $sel['checkin_ts']; ?>" />
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" />
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="empmanres.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<script>

	var BILLING_USERS_POOL = {};

	var opt_cont = <?php echo ($last_id+1); ?>;
	
	jQuery(document).ready(function() {

		<?php if (count($options)) { ?>

			vapOptionValueChanged();

		<?php } ?>
		
		jQuery('.vap-phones-select').on('change', function(){
			jQuery('.vap-phones-select').select2('val', jQuery(this).val());
		});
		
		jQuery(".vap-users-select").select2({
			placeholder: '<?php echo addslashes(JText::_("VAPMANAGERESERVATION29")); ?>',
			allowClear: true,
			width: 300,
			minimumInputLength: 2,
			ajax: {
				url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=empmanres.searchusers&tmpl=component&Itemid=' . $itemid, false); ?>",
				dataType: 'json',
				type: "POST",
				quietMillis: 50,
				data: function(term) {
					return {
						term: term
					};
				},
				results: function(data) {
					return {
						results: jQuery.map(data, function (item) {

							if (!BILLING_USERS_POOL.hasOwnProperty(item.id))
							{
								BILLING_USERS_POOL[item.id] = item;
							}

							return {
								text: item.billing_name,
								id: item.id
							}
						})
					};
				},
			},
			initSelection: function(element, callback) {
				// The input tag has a value attribute preloaded that points to a preselected repository's id.
				// This function resolves that id attribute to an object that select2 can render
				// using its formatResult renderer - that way the repository name is shown preselected
				var id = jQuery(element).val();
				
				jQuery.ajax("index.php?option=com_vikappointments&task=empmanres.searchusers&tmpl=component&id="+id, {
					dataType: "json"
				}).done(function(data) {
					if (data.hasOwnProperty('id')) {
						callback(data); 
					}
				});
			},
			formatSelection: function(data) {
				if (jQuery.isEmptyObject(data.billing_name)) {
					// display data retured from ajax parsing
					return data.text;
				}
				// display pre-selected value
				return data.billing_name;
			},
			dropdownCssClass: "bigdrop",
		});

		jQuery(".vap-payment-select").select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('.vap-phones-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100
		});

		jQuery(".vik-dropdown, .vap-cf-select").select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300
		});

		jQuery(".vik-dropdown-small").select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100
		});

		jQuery('.vap-users-select').on('change', function() {

			var id = jQuery(this).val();
			
			if (BILLING_USERS_POOL[id].hasOwnProperty('billing_name')) {
				jQuery('#vapname').val(BILLING_USERS_POOL[id].billing_name);
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('billing_mail')) {
				jQuery('#vapemail').val(BILLING_USERS_POOL[id].billing_mail);
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('billing_phone')) {
				jQuery('#vapphone').val(BILLING_USERS_POOL[id].billing_phone);
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('country_code')) {
				jQuery('select[name="phone_prefix"] option').each(function() {
					var code = jQuery(this).val();
					if (code.split('_')[1] == BILLING_USERS_POOL[id].country_code) {
						jQuery('.vap-phones-select').select2('val', code);
						return false;
					}
				});
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('fields')) {
				
				jQuery.each(BILLING_USERS_POOL[id].fields, function(k, v) {

					jQuery('*[data-name="'+k+'"]').val(v);

				});

			}

		});
	});
	
	function vapComposeMailFields() {
		var email = jQuery('#vapemail').val();
		jQuery('.vapemailfield').val(email);
	}
	
	function vapComposePhoneFields() {
		var phone = jQuery('#vapphone').val();
		jQuery('.vapphonefield').val(phone);
	}
	
	function vapOptionValueChanged() {
		vapSetOptionFieldEnabled(false);
		
		var id_opt = jQuery('#vapoptionselect').val();
		
		jQuery.noConflict();
			
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=empmanres.getOptionDetails&tmpl=component&Itemid=' . $itemid, false); ?>",
			data: {
				id_opt: id_opt
			}
		}).done(function(resp){
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0]) {
				vapSetOptionFieldEnabled(true);
				vapSetOptionFieldValue(obj[1]);
			} else {
				alert(obj[1]);
			}
			
		});
		
	}
	
	function vapSetOptionFieldEnabled(status) {
		jQuery('#vapoprice').prop('readonly', !status);
		jQuery('#vapoquant').prop('readonly', !status);
		jQuery('#vapobutton').prop('disabled', !status);
	}
	
	function vapSetOptionFieldValue(arr) {
		var _vars_html = "";
		for (var i = 0; i < arr.variations.length; i++) {
			var _price = parseFloat(arr.variations[i].price)+parseFloat(arr.price);
			_vars_html += '<option value="'+arr.variations[i].id+'" data-price="'+_price+'">'+arr.variations[i].name+'</option>\n';
		}

		jQuery('#vapoptvar').html(_vars_html);
		jQuery("#vapoptvar").select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300
		});

		if (_vars_html.length > 0) {
			jQuery('.vap-option-vars').show();
		} else {
			jQuery('.vap-option-vars').hide();
		}

		jQuery('#vapoprice').val(arr.price);
		jQuery('#vapoquant').val(1);
		if( arr.single == 0 ) {
			jQuery('#vapoquant').prop('readonly', true);
		}
	}

	function vapVariationValueChanged() {
		var _price = parseFloat(jQuery('#vapoptvar :selected').attr('data-price'));
		jQuery('#vapoprice').val(_price.toFixed(2));
	}
	
	function vapAddSelectedOption() {
		var id_opt 	 = jQuery('#vapoptionselect').val();
		var id_var 	 = jQuery('#vapoptvar').val();
		var price 	 = jQuery('#vapoprice').val();
		var quant 	 = jQuery('#vapoquant').val();
		var name 	 = jQuery('#vapoptionselect :selected').text();
		var var_name = jQuery('#vapoptvar :selected').text();
		
		jQuery('#vapoptioncont').append(
			'<div id="vaporow'+opt_cont+'" class="vap-res-option-row">\n'+
				'<span class="name-block">\n'+
					'<input type="text" readonly value="' + name + (var_name.length ? ' - ' + var_name : '') + '" style="width: 100%;"/>\n'+
				'</span>\n'+
				'<span class="quantity-block">\n'+
					'x' + quant + '\n'+
				'</span>\n'+
				'<span class="price-block">\n'+
					Currency.getInstance().format(price * quant) + '\n'+
				'</span>\n'+
				'<span class="remove-block">\n'+
					'<a href="javascript: void(0);" onClick="vapRemoveOptionRow('+opt_cont+')">\n'+
						'<i class="fa fa-times big"></i>\n'+
					'</a>\n'+
				'</span>\n'+
				'<input type="hidden" name="new_opt_id[]" value="'+id_opt+'" />\n'+
				'<input type="hidden" name="new_opt_var[]" value="'+(id_var ? id_var : -1)+'" />\n'+
				'<input type="hidden" name="new_opt_price[]" id="vapoprice'+opt_cont+'" value="'+(quant*price)+'" />\n'+
				'<input type="hidden" name="new_opt_quant[]" value="'+quant+'" />\n'+
			'</div>\n'
		);
		
		jQuery('#vaprtotalcost').val( (parseFloat(jQuery('#vaprtotalcost').val())+(quant*price)).toFixed(2) );
		
		opt_cont++;
	}
	
	function vapRemoveOptionRow(id) {
		var price = 0;
		if (jQuery('#vapoprice'+id).length > 0) {
			price = jQuery('#vapoprice'+id).val();
		}
		
		var _p = parseFloat(jQuery('#vaprtotalcost').val())-parseFloat(price);
		
		jQuery('#vaprtotalcost').val( _p.toFixed(2) );

		jQuery('#vaporow'+id).remove();
		jQuery('#empareaForm').append('<input type="hidden" name="del_opt_id[]" value="'+id+'" />');
	}
	
	function vapCloseReservation() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false); ?>';
	}

	var validator = new VikFormValidator('#empareaForm');
	
	<?php if (($auth->rescreate() && empty($sel['id'])) || $auth->resmanage()) { ?>

		function vapSaveReservation(close) {

			if (validator.validate()) {
				if (close) {
					jQuery('#vaphiddenreturn').val('1');
				}
			}
			
			document.empareaForm.submit();
		}

	<?php } ?>
	
</script>
