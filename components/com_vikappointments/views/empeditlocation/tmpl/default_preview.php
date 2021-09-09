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

$label = $location['name'] . ' <br />';

foreach ($this->countries as $c)
{
	if ($c['id'] == $location['id_country'])
	{
		$label .= $c['country_name'] . ', ';
		break; 
	}
}

foreach ($this->states as $s)
{
	if ($s['id'] == $location['id_state'])
	{
		$label .= $s['state_name'] . ', ';
		break; 
	}
}

foreach ($this->cities as $c)
{
	if ($c['id'] == $location['id_city'])
	{
		$label .= $c['city_name'] . ', ';
		break; 
	}
}

$label = rtrim($label, ' ');
$label = rtrim($label, ',');

$label .= '<br />' . $location['address'] . ', ' . $location['zip'];

?>
	
<div id="vap-googlemap" style="width:100%;height: 500px;"></div>

<script>
	
	jQuery(document).ready(function() {

		google.maps.event.addDomListener(window, 'load', function() {

			var coord = new google.maps.LatLng(
				<?php echo floatval($location['latitude']); ?>,
				<?php echo floatval($location['longitude']); ?>
			);
		
			var mapProp = {
				center: coord,
				zoom: 16,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			
			var map = new google.maps.Map(document.getElementById("vap-googlemap"),mapProp);
			
			var marker = new google.maps.Marker({
				position: coord,
			});

			marker.setAnimation(google.maps.Animation.DROP);

			var infowindow = new google.maps.InfoWindow();
			infowindow.setContent('<?php echo addslashes($label); ?>');

			google.maps.event.addListener(marker, 'click', (function() {
				return function() {
					infowindow.open(map, marker);
				}
			})());
				
			marker.setMap(map);
			
			jQuery('#vap-googlemap').show();

		});
		
	});
	
</script>
