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

$auth       = $this->auth;
$employee   = $auth->getEmployee();

$location = $this->location;

$type = $location['id'] > 0 ? 2 : 1;

$vik = UIApplication::getInstance();

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
		<h2><?php echo JText::_($type == 2 ? 'VAPEDITEMPLOCATIONTITLE' : 'VAPNEWEMPLOCATIONTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manageLocations()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveLocation(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
		
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveLocation(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>

		<?php if ($auth->manageLocations() && $type == 2) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveLocation();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseLocations();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditlocation'); ?>" method="post" name="empareaForm" id="empareaForm">
	
	<?php echo $vik->openEmptyFieldset(); ?>
		
		<?php echo $vik->openControl(JText::_('VAPEMPLOCATION1').'*:'); ?>
			<input type="text" name="name" value="<?php echo $this->escape($location['name']); ?>" size="40" id="vapname" class="required" />
		<?php echo $vik->closeControl(); ?>
		
		<?php echo $vik->openControl(JText::_('VAPEMPLOCATION2').'*:'); ?>
			<select name="id_country" id="vap-countries-sel" class="required">
				<option></option>
				<?php foreach ($this->countries as $c) { ?>
					<option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $location['id_country'] ? 'selected="selected"' : ''); ?>><?php echo $c['country_name']; ?></option>
				<?php } ?>
			</select>
		<?php echo $vik->closeControl(); ?>
		
		<?php echo $vik->openControl(JText::_('VAPEMPLOCATION3').':'); ?>
			<select name="id_state" id="vap-states-sel">
				<option></option>
				<?php foreach ($this->states as $s) { ?>
					<option value="<?php echo $s['id']; ?>" <?php echo ($s['id'] == $location['id_state'] ? 'selected="selected"' : ''); ?>><?php echo $s['state_name']; ?></option>
				<?php } ?>
			</select>
		<?php echo $vik->closeControl(); ?>
		
		<?php echo $vik->openControl(JText::_('VAPEMPLOCATION4').':'); ?>
			<select name="id_city" id="vap-cities-sel">
				<option></option>
				<?php foreach ($this->cities as $c) { ?>
					<option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $location['id_city'] ? 'selected="selected"' : ''); ?>><?php echo $c['city_name']; ?></option>
				<?php } ?>
			</select>
		<?php echo $vik->closeControl(); ?>
		
		<?php echo $vik->openControl(JText::_('VAPEMPLOCATION5').'*:'); ?>
			<input type="text" name="address" value="<?php echo $location['address']; ?>" size="40" id="vapaddress" class="required" />
			<a href="javascript: void(0);" onClick="vapEvaluateCoordinatesFromAddress(getCompleteAddress());" style="display:<?php echo (strlen($location['address']) ? 'block' : 'none'); ?>;" id="vapaddrrefresh">
				<?php echo JText::_('VAPEMPLOCATION10'); ?>
			</a>
		<?php echo $vik->closeControl(); ?>
		
		<?php echo $vik->openControl(JText::_('VAPEMPLOCATION6').':'); ?>
			<input type="text" name="zip" value="<?php echo $location['zip']; ?>" size="40" id="vapzip" />
		<?php echo $vik->closeControl(); ?>
		
		<?php echo $vik->openControl(JText::_('VAPEMPLOCATION7').':'); ?>
			<input type="text" name="latitude" value="<?php echo $location['latitude']; ?>" size="40" id="vaplatitude" class="vap-latlng" />
		<?php echo $vik->closeControl(); ?>
		
		<?php echo $vik->openControl(JText::_('VAPEMPLOCATION8').':'); ?>
			<input type="text" name="longitude" value="<?php echo $location['longitude']; ?>" size="40" id="vaplongitude" class="vap-latlng" />
		<?php echo $vik->closeControl(); ?>
	
		<div id="vap-googlemap" style="width:100%;height:420px;<?php echo (!empty($location['latitude']) ? 'display:none;' : ''); ?>"></div>

	<?php echo $vik->closeEmptyFieldset(); ?>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" />
	<input type="hidden" name="type" value="<?php echo $type; ?>" /> 
	
	<input type="hidden" name="id" value="<?php echo $location['id']; ?>" />
	<input type="hidden" name="task" value="empeditlocation.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<?php
JText::script('VAPCONFDIALOGMSG');
?>

<script>
	
	function vapCloseLocations() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplocations&Itemid=' . $itemid, false); ?>';
	}

	var validator = new VikFormValidator('#empareaForm');
	
	<?php if ($auth->manageLocations()) { ?>

		function vapSaveLocation(close) {
			
			if (validator.validate()) {
				if(close) {
					jQuery('#vaphiddenreturn').val('1');
				}
				
				document.empareaForm.submit();
			}
		}

	<?php } ?>
	
	<?php if ($auth->manageLocations() && $type == 2) { ?>
		
		function vapRemoveLocation() {

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditlocation.delete&cid[]=' . $location['id'] . '&Itemid=' . $itemid, false); ?>';
		}

	<?php } ?>
	
	jQuery(document).ready(function() {
		google.maps.event.addDomListener(window, 'load', vapInitialize);

		jQuery('.vap-latlng').on('change', function(){
			vapChangeLatLng(
				jQuery('#vaplatitude').val(),
				jQuery('#vaplongitude').val()
			);
		});

		jQuery("#vap-countries-sel, #vap-states-sel, #vap-cities-sel").select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});
		
		jQuery("#vap-countries-sel").on('change', function() {
			vapGetAndSetStates();
			
			vapGetAndSetCities();
		});
		
		jQuery("#vap-states-sel").on('change', function() {
			vapGetAndSetCities();
		});

		jQuery('#vapaddress').on('change', function() {
			if (jQuery('#vaplatitude').val().length == 0 || jQuery('#vaplongitude').val().length == 0) {
				vapEvaluateCoordinatesFromAddress(getCompleteAddress());
			} else {
				jQuery('#vapaddrrefresh').css('display', 'block');
			}
		});
		
	});
	
	// GET STATES
	function vapGetAndSetStates() {
		jQuery("#vap-states-sel").html('<option></option>');
		jQuery("#vap-states-sel").select2('val', '');
		
		var id_country = jQuery("#vap-countries-sel").select2("val");
		if (id_country.length == 0) {
			return;
		}
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: '<?php echo JRoute::_("index.php?option=com_vikappointments&task=get_states_with_country&tmpl=component&Itemid=" . $itemid, false); ?>',
			data: {
				id_country: id_country
			}
		}).done(function(resp) { 
			var obj = jQuery.parseJSON(resp);
			
			jQuery.each(obj, function(k, v){
				jQuery("#vap-states-sel").append('<option value="'+v.id+'">'+v.state_name+'</option>');
			});
			
		});
	}
	
	// GET CITIES
	function vapGetAndSetCities() {
		jQuery("#vap-cities-sel").html('<option></option>');
		jQuery("#vap-cities-sel").select2('val', '');
		
		var id_state = jQuery("#vap-states-sel").select2("val");
		if (id_state.length == 0) {
			return;
		}
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: '<?php echo JRoute::_("index.php?option=com_vikappointments&task=get_cities_with_state&tmpl=component&Itemid=" . $itemid, false); ?>',
			data: {
				id_state: id_state
			}
		}).done(function(resp) { 
			var obj = jQuery.parseJSON(resp);
			
			jQuery.each(obj, function(k, v){
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

	<?php if (!empty($location['latitude']) && !empty($location['longitude'])) { ?>
		LATITUDE 	= <?php echo floatval($location['latitude']); ?>;
		LONGITUDE 	= <?php echo floatval($location['longitude']); ?>;
	<?php } ?>
	
	function vapInitialize() {
		if (LATITUDE.length == 0) {
			jQuery('#vap-googlemap').hide();
			return;
		}
		
		var coord = new google.maps.LatLng(LATITUDE,LONGITUDE);
		
		var mapProp = {
			center: coord,
			zoom: 15,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		
		var map = new google.maps.Map(document.getElementById("vap-googlemap"),mapProp);
		
		var marker = new google.maps.Marker({
			position: coord,
		});

		if (ANIMATE) {
			marker.setAnimation(google.maps.Animation.DROP);
		}
			
		marker.setMap(map);
		
		jQuery('#vap-googlemap').show();
	}

	function vapEvaluateCoordinatesFromAddress(address) {

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

				jQuery('#vaplatitude').val(coord.lat);
				jQuery('#vaplongitude').val(coord.lng);

				vapChangeLatLng(coord.lat, coord.lng);
			}
		});
	}
	
	function vapChangeLatLng(lat, lng) {
		LATITUDE 	= lat;
		LONGITUDE 	= lng;
		
		if (LATITUDE.length == 0 || LONGITUDE.length == 0) {
			LATITUDE = LONGITUDE = "";
		}

		ANIMATE = true;
		vapInitialize();
	}
	
</script>
