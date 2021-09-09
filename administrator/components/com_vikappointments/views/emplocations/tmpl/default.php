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

$rows = $this->rows;

$vik = UIApplication::getInstance();

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">
	
	<div class="vapser-keyfilter-block btn-toolbar" id="filter-bar">
		<div class="btn-group pull-left">
			<button type="button" class="btn" onClick="vapOpenJModal('gmap', null, true);">
				<i class="icon-location"></i>&nbsp;<?php echo JText::_('VAPMANAGEEMPLOCATION7'); ?>
			</button>
		</div>
		<div class="btn-group pull-left">
			<button type="button" class="btn" onClick="vapOpenJModal('wdays', null, true);">
				<i class="icon-calendar"></i>&nbsp;<?php echo JText::_('VAPMANAGEEMPLOCATION9'); ?>
			</button>
		</div>
	</div>
	
<?php if (count($this->rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOEMPLOCATION'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION8' ); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION10' ); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION3' ); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION2' ); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION1' ); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION4' ); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION5' ); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION6' ); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				<td style="text-align: left;"><a href="index.php?option=com_vikappointments&amp;task=editemplocation&amp;cid[]=<?php echo $row['id']; ?>&amp;id_emp=<?php echo $this->idEmployee; ?>">
					<?php echo $row['name']; ?>
				</a></td>
				<td style="text-align: center;"><?php echo $row['address']; ?></td>
				<td style="text-align: center;"><?php echo $row['city_name']; ?></td>
				<td style="text-align: center;"><?php echo $row['state_name']; ?></td>
				<td style="text-align: center;"><?php echo $row['country_name']; ?></td>
				<td style="text-align: center;"><?php echo $row['zip']; ?></td>
				<td style="text-align: center;"><?php echo $row['latitude']; ?></td>
				<td style="text-align: center;"><?php echo $row['longitude']; ?></td>
			</tr>
		<?php } ?>
		
	</table>
<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="emplocations" />
	<input type="hidden" name="id_emp" value="<?php echo $this->idEmployee; ?>" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<?php echo $this->navbut; ?>
</form>

<?php
// google map modal
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-gmap',
	array(
		'title'       => JText::_('VAPMANAGEEMPLOCATION7'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
	),
	'<div id="googlemap" style="width:100%;height:90%;"></div>'
);

// working days modal
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-wdays',
	array(
		'title'       => JText::_('VAPMANAGEEMPLOCATION9'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => 'index.php?option=com_vikappointments&tmpl=component&task=emplocwdays&id_emp=' . $this->idEmployee,
	)
);
?>

<script>
	
	jQuery(document).ready(function() {
		
		jQuery('#jmodal-gmap').on('show', function() {
			setTimeout(function(){
				initializeGoogleMap();
			}, 250);           
		});
	   
	});
	
	var COORDINATES = <?php echo json_encode($this->jsCoordinates); ?>;
	
	var gmap = null;
	
	function initializeGoogleMap() {
		
		gmap = new google.maps.Map(document.getElementById('googlemap'), {
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		var infowindow = new google.maps.InfoWindow();

		var marker, i;
		
		var markerBounds = new google.maps.LatLngBounds();

		for (i = 0; i < COORDINATES.length; i++) {
			var position = new google.maps.LatLng(COORDINATES[i].lat, COORDINATES[i].lng);
			
			marker = new google.maps.Marker({
				position: position,
				map: gmap
			});
			
			markerBounds.extend(position);

			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					infowindow.setContent(COORDINATES[i].name);
					infowindow.open(gmap, marker);
				}
			})(marker, i));
		}
		
		gmap.fitBounds(markerBounds);
		gmap.setCenter(markerBounds.getCenter());
		
	}

	function vapOpenJModal(id, url, jqmodal) {
		<?php echo $vik->bootOpenModalJS(); ?>
	}
	
</script>
