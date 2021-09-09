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

$locations = $this->locations;

$itemid = JFactory::getApplication()->input->getInt('Itemid');

$js_coordinates = array();

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
echo JLayoutHelper::render('emparea.toolbar');
?>

<div class="vapeditempheaderdiv">
	<div class="vapeditemptitlediv">
		<h2><?php echo JText::sprintf('VAPEMPLOCATIONSPAGETITLE', $employee['firstname'] . ' ' . $employee['lastname']); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manageLocations()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapCreateLocation();" id="vapempbtnnew" class="vap-btn blue employee"><?php echo JText::_('VAPNEW'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseLocations();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<?php if (count($locations)) { ?>

<div class="vap-emploc-container">
	
	<?php foreach ($locations as $l)
	{ 
		$cont = $l['country_name'];
		
		if (!empty($l['state_name']))
		{
			$cont .= ', ' . $l['state_name'];
		}

		if (!empty($l['city_name']))
		{
			$cont .= ', ' . $l['city_name'];
		}

		$cont .= ', ' . $l['address'] . ' ' . $l['zip'];

		if ($l['id_employee'] > 0)
		{
			$url = JRoute::_("index.php?option=com_vikappointments&view=empeditlocation&cid[]={$l['id']}&Itemid={$itemid}");
		}
		else
		{
			$url = 'javascript: void(0);';
		}
		?>
		
		<a href="<?php echo $url; ?>">
			<div class="vap-emplocation-block">
				
				<div class="vap-emplocation-title">
					<?php echo $l['name']; ?>
				</div>
				
				<div class="vap-emplocation-content">
					<?php echo $cont; ?>
				</div>
				
				<div class="vap-emplocation-coord">
					<?php if( strlen($l['latitude']) && strlen($l['longitude']) ) {
						array_push($js_coordinates, array(
							"name" => $l['name'], 
							"lat" => floatval($l['latitude']),
							"lng" => floatval($l['longitude']),
						));
						echo $l['latitude'].', '.$l['longitude'];
					} ?>
				</div>
				
			</div>
		</a>

	<?php } ?>
	
</div>

<div id="vap-googlemap" style="width:100%;height:420px;"></div>

<script>

	jQuery(document).ready(function() {
		google.maps.event.addDomListener(window, 'load', vapInitializeGoogleMap);
	});
	
	function vapInitializeGoogleMap() {
		
		var COORDINATES = <?php echo json_encode($js_coordinates); ?>;
		
		var map = new google.maps.Map(document.getElementById('vap-googlemap'), {
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		var infowindow = new google.maps.InfoWindow();

		var marker, i;
		
		var markerBounds = new google.maps.LatLngBounds();

		for (i = 0; i < COORDINATES.length; i++) {
			var position = new google.maps.LatLng(COORDINATES[i].lat, COORDINATES[i].lng);
			
			marker = new google.maps.Marker({
				position: position,
				map: map
			});
			
			markerBounds.extend(position);

			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					infowindow.setContent(COORDINATES[i].name);
					infowindow.open(map, marker);
				}
			})(marker, i));
		}
		
		map.fitBounds(markerBounds);
		map.setCenter(markerBounds.getCenter());
	}

</script>

<?php } else { ?>

	<div class="vap-allorders-void long"><?php echo JText::_('VAPNOLOCATION'); ?></div>

<?php } ?>

<script>
	
	function vapCloseLocations() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false); ?>';
	}
	
	<?php if ($auth->manageLocations()) { ?>

		function vapCreateLocation() {
			document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditlocation&Itemid=' . $itemid, false); ?>';
		}

	<?php } ?>
	
</script>
