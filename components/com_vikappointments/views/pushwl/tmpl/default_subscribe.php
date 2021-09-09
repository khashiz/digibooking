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

$user = $this->user;

$date_format = UIFactory::getConfig()->get('dateformat');

?>

<div class="vap-pushwl-content">

	<!-- SUMMARY -->
	<div class="vap-pushwl-summary">
		<?php echo JText::sprintf('VAPWAITLISTSUMMARY', $this->serviceName, date($date_format, $this->timestamp)); ?>
	</div>

	<!-- EMAIL -->
	<div class="vap-pushwl-control">
		<div class="vap-pushwl-control-label"><?php echo JText::_('CUSTOMF_EMAIL') ?></div>
		<div class="vap-pushwl-control-value">
			<input type="text" id="vap-field-mail" class="vap-is-field vap-is-mail" value="<?php echo $user->email; ?>" />
		</div>
	</div>

	<!-- PHONE PREFIX - PHONE NUMBER -->
	<div class="vap-pushwl-control">
		<div class="vap-pushwl-control-label"><?php echo JText::_('CUSTOMF_PHONE') ?></div>
		<div class="vap-pushwl-control-value">
			<select name="phone_prefix" class="vap-field-prefix" id="vap-field-prefix">
				
				<?php foreach ($this->countries as $ctry) { ?>

					<option 
						value="<?php echo $ctry['id'] . "_" . $ctry['country_2_code']; ?>"
						title="<?php echo trim($ctry['country_name']); ?>" 
						<?php echo ($user->phonePrefix == $ctry['phone_prefix'] ? 'selected="selected"' : ''); ?>
					><?php echo $ctry['phone_prefix']; ?></option>

				<?php } ?>

			</select>

			<input type="text" id="vap-field-phone" style="width: 147px;" class="vap-is-field vap-is-req" 
				value="<?php echo $user->phoneNumber; ?>" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
		</div>
	</div>

	<!-- RESPONSE -->
	<div class="vap-pushwl-response"></div>

	<!-- ACTIONS -->
	<div class="vap-pushwl-bottom">
		<button type="button" class="vap-btn green ok" onClick="vapPushInWaitingList();"><?php echo JText::_('VAPSUBMIT'); ?></button>
		<button type="button" class="vap-btn" onClick="vapCloseWaitListOverlay('vapaddwaitlistoverlay');"><?php echo JText::_('VAPCANCEL'); ?></button>
	</div>

</div>

<script>

	jQuery(document).ready(function() {

		jQuery('.vap-is-req').on('blur', function() {
			vapValidateField(this);
		});

		jQuery('.vap-is-mail').on('blur', function() {
			if (vapValidateField(this)) {
				vapValidateMail(this);
			}
		});

		jQuery(document).ready(function(){
			jQuery(".vap-field-prefix").select2({
				allowClear: true,
				width: 100,
				minimumResultsForSearch: -1,
				formatResult: format,
				formatSelection: format,
				escapeMarkup: function(m) { return m; }
			});
		});

		function format(state) {
			if(!state.id) return state.text; // optgroup

			return '<img class="vap-opt-flag" src="<?php echo VAPASSETS_URI; ?>css/flags/' + state.id.toLowerCase().split("_")[1] + '.png"/>' + state.text;
		}

	});

	function vapValidateField(elem) {
		if (jQuery(elem).val().length == 0) {
			jQuery(elem).addClass('vaprequiredfield');
			return false;
		} else {
			jQuery(elem).removeClass('vaprequiredfield');
			return true;
		}
	}
	
	function vapValidateMail(elem) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if (re.test(jQuery(elem).val())) {
			jQuery(elem).removeClass('vaprequiredfield');
			return true;
		} else {
			jQuery(elem).addClass('vaprequiredfield');
			return false;
		}
	}
	
	function vapPushInWaitingList() {
		
		var args = {
			mail: jQuery('#vap-field-mail').val(),
			phone: jQuery('#vap-field-phone').val(),
			prefix: jQuery('#vap-field-prefix').val()
		};
		
		var ok = true;
		jQuery.each(args, function(k, v){
			if (v.length == 0) {
				ok = false;
				jQuery('#vap-field-'+k).addClass('vaprequiredfield');
			} else {
				jQuery('#vap-field-'+k).removeClass('vaprequiredfield');
			}
		});
		
		if (!ok) {
			return;
		}
		
		jQuery('.vap-is-field').attr('readonly', true);
		jQuery('.vap-pushwl-bottom').hide();
		
		jQuery.noConflict();
	
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=add_in_waiting_list&tmpl=component&Itemid=' . $this->itemid, false); ?>",
			data: {
				ts: <?php echo $this->timestamp; ?>,
				id_service: <?php echo $this->idService; ?>,
				id_employee: <?php echo $this->idEmployee; ?>,
				mail: args.mail,
				phone: args.phone,
				prefix: args.prefix
			}
		}).done(function(resp){
			obj = jQuery.parseJSON(resp);
			
			if (obj[0]) {
				jQuery('.vap-pushwl-response').html('<div class="good">'+obj[1]+'</div>');
			
				setTimeout(function(){
					vapCloseWaitListOverlay('vapaddwaitlistoverlay');
				}, 2000);
			} else {
				var htm = '<div class="bad">'+obj[1]+'</div>\n';
				jQuery('.vap-pushwl-response').html(htm);
				jQuery('.vap-is-field').attr('readonly', false);  
				jQuery('.vap-pushwl-bottom').show(); 
			}
		}).fail(function(resp){
			var htm = '<div class="bad">'+resp+'</div>\n';
			jQuery('.vap-pushwl-response').html(htm);
			jQuery('.vap-is-field').attr('readonly', false);   
			jQuery('.vap-pushwl-bottom').show();
		});
		
	}

</script>
