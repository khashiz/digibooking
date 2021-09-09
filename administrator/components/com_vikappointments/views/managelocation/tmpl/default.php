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

$sel = $this->location;

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<div class="span5">
		<?php echo $vik->openFieldset(JText::_('VAPEMPLOCATIONTITLE1'), 'form-horizontal'); ?>
		
			<!-- NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOCATION8').':*'); ?>
				<input type="text" name="name" class="required" value="<?php echo $sel['name']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- COUNTRY - Select -->
			<?php
			$options = array();
			$options[] = JHtml::_('select.option', '', '');
			foreach ($this->countries as $c)
			{
				$options[] = JHtml::_('select.option', $c['id'], $c['country_name']);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOCATION1').'*:'); ?>
				<select name="id_country" id="vap-countries-sel">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $sel['id_country']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
			<!-- STATE - Select -->
			<?php
			$options = array();
			$options[] = JHtml::_('select.option', '', '');
			foreach ($this->states as $s)
			{
				$options[] = JHtml::_('select.option', $s['id'], $s['state_name']);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOCATION2').':'); ?>
				<select name="id_state" id="vap-states-sel">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $sel['id_state']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
			<!-- CITY - Select -->
			<?php
			$options = array();
			$options[] = JHtml::_('select.option', '', '');
			foreach ($this->cities as $c)
			{
				$options[] = JHtml::_('select.option', $c['id'], $c['city_name']);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOCATION3').':'); ?>
				<select name="id_city" id="vap-cities-sel">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $sel['id_city']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
			<!-- ADDRESS - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOCATION10').':*'); ?>
				<input type="text" name="address"  class="required" value="<?php echo $sel['address']; ?>" size="40" id="vapaddress" />
				<a href="javascript: void(0);" onClick="evaluateCoordinatesFromAddress(getCompleteAddress());" style="<?php echo (strlen($sel['address']) ? '' : 'display:none;'); ?>" id="vapaddressrefresh">
					<i class="fa fa-map-marker big" style="margin-left:5px;" title="<?php echo JText::_('VAPMANAGEEMPLOCATION15'); ?>"></i>
				</a>
			<?php echo $vik->closeControl(); ?>
			
			<!-- ZIP CODE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOCATION4').':'); ?>
				<input type="text" name="zip" value="<?php echo $sel['zip']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- LATITUDE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOCATION5').':'); ?>
				<input type="text" name="latitude" class="latlng" value="<?php echo $sel['latitude']; ?>" size="40" id="vap-latitude" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- LONGITUDE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOCATION6').':'); ?>
				<input type="text" name="longitude" class="latlng" value="<?php echo $sel['longitude']; ?>" size="40" id="vap-longitude" />
			<?php echo $vik->closeControl(); ?>

			<!-- EMPLOYEE - Select -->
			<?php
			$options = array();
			$options[] = JHtml::_('select.option', '', '');
			foreach ($this->employees as $e)
			{
				$options[] = JHtml::_('select.option', $e['id'], $e['nickname']);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGEEMPLOCATION12').':'); ?>
				<select name="id_employee" id="vap-employees-sel">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $sel['id_employee']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGEEMPLOCATION12'),
					'content' 	=> JText::_('VAPMANAGEEMPLOCATION12_DESC'),
				)); ?>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeFieldset(); ?>
	</div>
	
	<div class="span6">
		<?php echo $vik->openFieldset(JText::_('VAPMANAGEEMPLOCATION7'), 'form-horizontal'); ?>
			<div class="control-group">
				<div id="googlemap" style="width:100%;height:380px;<?php echo (!empty($sel['latitude']) ? 'display:none;' : ''); ?>"></div>
			</div>
		<?php echo $vik->closeFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<script>
	
	jQuery(document).ready(function() {

		google.maps.event.addDomListener(window, 'load', initialize);

		jQuery('.latlng').on('change', function(){
			changeLatLng(
				jQuery('#vap-latitude').val(),
				jQuery('#vap-longitude').val()
			);
		});

		jQuery("#vap-countries-sel, #vap-states-sel, #vap-cities-sel, #vap-employees-sel").select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});
		
		jQuery("#vap-countries-sel").on('change', function() {
			getAndSetStates();
			
			getAndSetCities();
		});
		
		jQuery("#vap-states-sel").on('change', function() {
			getAndSetCities();
		});

		jQuery('#vapaddress').on('change', function() {
			if (jQuery('#vap-latitude').val().length == 0 || jQuery('#vap-longitude').val().length == 0) {
				evaluateCoordinatesFromAddress(getCompleteAddress());
			} else {
				jQuery('#vapaddressrefresh').show();
			}
		});
		
	});
	
	// GET STATES
	function getAndSetStates() {
		jQuery("#vap-states-sel").html('<option></option>');
		jQuery("#vap-states-sel").select2('val', '');
		
		var id_country = jQuery("#vap-countries-sel").select2("val");
		if (id_country.length == 0) {
			return;
		}
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=get_states_with_country&tmpl=component",
			data: {
				id_country: id_country
			}
		}).done(function(resp) { 
			var obj = jQuery.parseJSON(resp);
			
			jQuery.each(obj, function(k, v) {
				jQuery("#vap-states-sel").append('<option value="'+v.id+'">'+v.state_name+'</option>');
			});
			
		});
	}
	
	// GET CITIES
	function getAndSetCities() {
		jQuery("#vap-cities-sel").html('<option></option>');
		jQuery("#vap-cities-sel").select2('val', '');
		
		var id_state = jQuery("#vap-states-sel").select2("val");
		if (id_state.length == 0) {
			return;
		}
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=get_cities_with_state&tmpl=component",
			data: {
				id_state: id_state
			}
		}).done(function(resp) { 
			var obj = jQuery.parseJSON(resp);
			
			jQuery.each(obj, function(k, v) {
				jQuery("#vap-cities-sel").append('<option value="'+v.id+'">'+v.city_name+'</option>');
			});
		});
	}
	
	// GOOGLE MAP

	function getCompleteAddress() {
		var address = jQuery('#vapaddress').val();

		var state = jQuery('#vap-states-sel').val();
		if (state.length > 0) {
			address += ", "+jQuery('#vap-states-sel :selected').text();

			var city = jQuery('#vap-cities-sel').val();
			if (city.length > 0) {
				address += ", "+jQuery('#vap-cities-sel :selected').text();
			}

		}

		return address;
	}
	
	var LATITUDE 	= '';
	var LONGITUDE 	= '';
	var ANIMATE 	= false;

	<?php if (!empty($sel['latitude']) && !empty($sel['longitude'])) { ?>
		LATITUDE    = <?php echo floatval($sel['latitude']); ?>;
		LONGITUDE   = <?php echo floatval($sel['longitude']); ?>;
	<?php } ?>
	
	function initialize() {
		if (LATITUDE.length == 0) {
			jQuery('#googlemap').hide();
			return;
		}
		
		var coord = new google.maps.LatLng(LATITUDE,LONGITUDE);
		
		var mapProp = {
			center: coord,
			zoom: 15,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		
		var map = new google.maps.Map(document.getElementById("googlemap"), mapProp);
		
		var marker = new google.maps.Marker({
			position: coord,
		});
		
		if (ANIMATE) {
			marker.setAnimation(google.maps.Animation.DROP);
		}
			
		marker.setMap(map);
		
		jQuery('#googlemap').show();
	}

	function evaluateCoordinatesFromAddress(address) {

		if (address.length == 0) {
			return;
		}

		var geocoder = new google.maps.Geocoder();

		var coord = null;

		geocoder.geocode({'address': address}, function(results, status) {
			if (status == "OK") {
				coord = {
					"lat": results[0].geometry.location.lat(),
					"lng": results[0].geometry.location.lng()
				};

				jQuery('#vap-latitude').val(coord.lat);
				jQuery('#vap-longitude').val(coord.lng);

				changeLatLng(coord.lat, coord.lng);
			}
		});
	}
	
	function changeLatLng(lat, lng) {
		LATITUDE = lat;
		LONGITUDE = lng;

		if (LATITUDE.length == 0 || LONGITUDE.length == 0) {
			LATITUDE = LONGITUDE = "";
		}

		ANIMATE = true;
		initialize();
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
