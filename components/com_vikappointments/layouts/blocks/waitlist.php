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

$id_service  = isset($displayData['id_service'])  	? $displayData['id_service'] 	: 0;
$id_employee = isset($displayData['id_employee']) 	? $displayData['id_employee'] 	: 0;
$title 		 = isset($displayData['title'])			? $displayData['title']			: '';
$itemid 	 = isset($displayData['itemid'])		? $displayData['itemid']		: null;

if (is_null($itemid))
{
	// item id not provided, get the current one (if set)
	$itemid = JFactory::getApplication()->input->getInt('Itemid');
}

?>

<div class="vap-overlay" id="vapaddwaitlistoverlay" style="display: none;">
	<div class="vap-modal-box" style="width: 80%;max-width: 800px;height: 60%;margin-top:10px;">
		
		<div class="vap-modal-head">
			<div class="vap-modal-head-title">
				<h3><?php echo $title; ?></h3>
			</div>

			<div class="vap-modal-head-dismiss">
				<a href="javascript: void(0);" onClick="vapCloseWaitListOverlay('vapaddwaitlistoverlay');">×</a>
			</div>
		</div>

		<div class="vap-modal-body" style="height:90%;overflow:scroll;">
			
		</div>

	</div>
</div>

<?php
JText::script('VAPWAITLISTADDED0');
?>

<script>

	function vapOpenWaitListOverlay(ref, timestamp, title) {
		
		if (title) {
			jQuery('.vap-modal-head-title h3').text(title);
		}
		
		jQuery('#' + ref).show();
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: 'POST',
			url: '<?php echo JRoute::_("index.php?option=com_vikappointments&tmpl=component&view=pushwl" . ($itemid ? "&Itemid=" . $itemid : ''), false); ?>',
			data: { 
				ts: timestamp, 
				id_service: <?php echo $id_service; ?>, 
				id_employee: <?php echo $id_employee; ?> 
			}
		}).done(function(resp) {

			resp = jQuery.parseJSON(resp)[0];

			jQuery('.vap-modal-body').html(resp);

		}).fail(function(err) {

			console.log(err, err.responseText);
			alert(Joomla.JText._('VAPWAITLISTADDED0'));

		});
		
	}

	function vapCloseWaitListOverlay(ref) {
		jQuery('#' + ref).hide();
		jQuery('.vap-modal-body').html('');
	}

	jQuery('.vap-modal-box').on('click', function(e) {
		// ignore outside clicks
		e.stopPropagation();
	});

	jQuery('.vap-overlay').on('click', function() {
		vapCloseWaitListOverlay('vapaddwaitlistoverlay');
	});

</script>
