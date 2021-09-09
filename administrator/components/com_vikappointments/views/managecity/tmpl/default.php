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

$sel = $this->city;

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<div class="span5">
		<?php echo $vik->openFieldset(JText::_('VAPMANAGECITYTITLE1'), 'form-horizontal'); ?>
			
			<!-- CITY NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECITY1').'*:'); ?>
				<input type="text" name="city_name" class="required" value="<?php echo $sel['city_name']; ?>" size="40" id="vapcity" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- CITY 2 CODE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECITY2').'*:'); ?>
				<input type="text" name="city_2_code" class="required" value="<?php echo $sel['city_2_code']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- CITY 3 CODE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECITY3').':'); ?>
				<input type="text" name="city_3_code" value="<?php echo $sel['city_3_code']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- LATITUDE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECITY4').':'); ?>
				<input class="latlng" type="text" name="latitude" value="<?php echo $sel['latitude']; ?>" size="40" id="vap-latitude" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- LONGITUDE - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECITY5').':'); ?>
				<input class="latlng" type="text" name="longitude" value="<?php echo $sel['longitude']; ?>" size="40" id="vap-longitude" />
			<?php echo $vik->closeControl(); ?>
			
			<!-- PUBLISHED - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
			$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGECITY6').':'); ?>
				<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeFieldset(); ?>
	</div>
	
	<div class="span6">
		<?php echo $vik->openFieldset(JText::_('VAPMANAGECITYTITLE2'), 'form-horizontal'); ?>
			<div class="control-group">
				<div id="googlemap" style="width:100%;height:380px;<?php echo (!empty($sel['latitude']) ? 'display:none;' : ''); ?>"></div>
			</div>
		<?php echo $vik->closeFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="country" value="<?php echo $this->country; ?>" />
	<input type="hidden" name="state" value="<?php echo $this->state; ?>" />
</form>

<script>
	
	jQuery(document).ready(function() {
	   google.maps.event.addDomListener(window, 'load', initialize);
	   
	   jQuery('.latlng').on('change', function() {
		   changeLatLng(
			   jQuery('#vap-latitude').val(),
			   jQuery('#vap-longitude').val()
		   );
	   });

	   jQuery('#vapcity').on('change', function() {
			evaluateCoordinatesFromAddress(jQuery(this).val());
		});

	});
	
	var LATITUDE    = '';
	var LONGITUDE   = '';
	var ANIMATE     = false;

	<?php if (strlen($sel['latitude']) && strlen($sel['longitude'])) { ?>
		LATITUDE  = <?php echo floatval($sel['latitude']); ?>;
		LONGITUDE = <?php echo floatval($sel['longitude']); ?>;
	<?php } ?>
	
	function initialize() {
		if (LATITUDE.length == 0) {
			jQuery('#googlemap').hide();
			return;
		}
		
		var coord = new google.maps.LatLng(LATITUDE,LONGITUDE);
		
		var mapProp = {
			center: coord,
			zoom: 10,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		
		var map = new google.maps.Map(document.getElementById('googlemap'), mapProp);
		
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

		address += ' <?php echo addslashes($this->stateName); ?>';

		var geocoder = new google.maps.Geocoder();

		var coord = null;

		geocoder.geocode({'address': address}, function(results, status) {
			if (status == 'OK') {
				coord = {
					'lat': results[0].geometry.location.lat(),
					'lng': results[0].geometry.location.lng()
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
			LATITUDE = LONGITUDE = '';
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
