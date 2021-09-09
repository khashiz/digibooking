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

$ordering = $this->ordering;

$filters = $this->filters;

$vik = UIApplication::getInstance();

foreach (array('id', 'name', 'address', 'country_name', 'state_name', 'nickname') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('locations', JText::_('VAPMANAGEEMPLOCATION11'), 'id', $ordering['id'], 1, $filters, 'vapheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('locations', JText::_('VAPMANAGEEMPLOCATION8'), 'name', $ordering['name'], 1, $filters, 'vapheadcolactive'.(($ordering['name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('locations', JText::_('VAPMANAGEEMPLOCATION10'), 'address', $ordering['address'], 1, $filters, 'vapheadcolactive'.(($ordering['address'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('locations', JText::_('VAPMANAGEEMPLOCATION2'), 'state_name', $ordering['state_name'], 1, $filters, 'vapheadcolactive'.(($ordering['state_name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('locations', JText::_('VAPMANAGEEMPLOCATION1'), 'country_name', $ordering['country_name'], 1, $filters, 'vapheadcolactive'.(($ordering['country_name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('locations', JText::_('VAPMANAGEEMPLOCATION12'), 'nickname', $ordering['nickname'], 1, $filters, 'vapheadcolactive'.(($ordering['nickname'] == 2) ? 1 : 2)),
);

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left input-append">
			<input type="text" name="keys" id="vapkeysearch" size="32" 
				value="<?php echo $filters['keys']; ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />

			<button type="submit" class="btn">
				<i class="icon-search"></i>
			</button>
		</div>
		
		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>

		<div class="btn-group pull-right">
			<button type="button" class="btn" onClick="vapOpenJModal('gmap', null, true);">
				<i class="icon-location"></i>&nbsp;<?php echo JText::_('VAPMANAGEEMPLOCATION7'); ?>
			</button>
		</div>

	</div>
	
<?php if (count($this->rows) == 0) { ?>
		
	<p><?php echo JText::_('VAPNOLOCATION'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[3]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo $links[4]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION4' ); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_( 'VAPMANAGEEMPLOCATION13' ); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[5]; ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: left;"><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;">
					<a href="index.php?option=com_vikappointments&amp;task=editlocation&amp;cid[]=<?php echo $row['id']; ?>">
						<?php echo $row['name']; ?>
					</a>
				</td>
				
				<td style="text-align: center;"><?php echo $row['address']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['state_name']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['country_name']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['zip']; ?></td>
				
				<td style="text-align: center;">
					<?php 
					if (strlen($row['latitude']) && strlen($row['longitude']))
					{
						echo number_format($row['latitude'], 3) . ', ' . number_format($row['longitude'], 3); 
					}
					?>
				</td>
				
				<td style="text-align: center;"><?php echo (strlen($row['nickname']) ? $row['nickname'] : JText::_('VAPMANAGEEMPLOCATION14')); ?></td>
			</tr>  
			<?php } ?>
		
	</table>

<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="locations" />
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
?>

<script>
	
	jQuery(document).ready(function(){
		
		jQuery('#jmodal-gmap').on('show', function() {
			setTimeout(function() {
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
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');

		document.adminForm.submit();
	}
	
</script>
