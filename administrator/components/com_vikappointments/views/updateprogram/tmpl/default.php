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

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<div class="span12">
		<?php echo $vik->openFieldset($this->version->shortTitle, 'form-horizontal'); ?>

			<div class="control"><strong><?php echo $this->version->title; ?></strong></div>

			<div class="control" style="margin-top: 10px;">
				<button type="button" class="btn btn-primary" onclick="downloadSoftware(this);">
					<?php echo JText::_($this->version->compare == 1 ? 'VAPDOWNLOADUPDATEBTN1' : 'VAPDOWNLOADUPDATEBTN0'); ?>
				</button>
			</div>

			<div class="control vap-box-error" id="update-error" style="display: none;margin-top: 10px;"></div>

			<?php if (isset($this->version->changelog) && count($this->version->changelog)) { ?>

				<div class="control vap-update-changelog" style="margin-top: 10px;">

					<?php echo $this->digChangelog($this->version->changelog); ?>

				</div>

			<?php } ?>

		<?php echo $vik->closeFieldset(); ?>
	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="option" value="com_vikappointments"/>
</form>

<?php
JText::script('VAPCONNECTIONLOSTERROR');
JText::script('VAP_PLEASE_WAIT_MESSAGE');
?>

<script type="text/javascript">

	var isRunning = false;

	function downloadSoftware(btn) {

		if (isRunning) {
			return;
		}

		switchRunStatus(btn);
		setError(null);

		var jqxhr = jQuery.ajax({
			url: "index.php?option=com_vikappointments&task=launch_update&tmpl=component",
			type: "POST",
			data: {}
		}).done(function(resp) {

			var obj = jQuery.parseJSON(resp);
			
			if (obj === null) {

				// connection failed. Something gone wrong while decoding JSON
				alert(Joomla.JText._('VAPCONNECTIONLOSTERROR'));

			} else if (obj.status) {

				document.location.href = 'index.php?option=com_vikappointments';
				return;

			} else {

				console.log("### ERROR ###");
				console.log(obj);

				if (obj.hasOwnProperty('error')) {
					setError(obj.error);
				} else {
					var link = '<?php echo addslashes($vik->getManufacturer(array('link' => true, 'long' => true))); ?>';

					setError('Your website does not own a valid support license!<br />Please visit ' + link + ' to purchase a license or to receive assistance.');
				}

			}

			switchRunStatus(btn);

		}).fail(function(resp){
			console.log('### FAILURE ###');
			console.log(resp);
			alert(Joomla.JText._('VAPCONNECTIONLOSTERROR'));

			switchRunStatus(btn);
		}); 
	}

	function switchRunStatus(btn) {
		isRunning = !isRunning;

		jQuery(btn).prop('disabled', isRunning);

		if (isRunning) {
			// start loading
			openLoadingOverlay(true, Joomla.JText._('VAP_PLEASE_WAIT_MESSAGE'));
		} else {
			// stop loading
			closeLoadingOverlay();
		}
	}

	function setError(err) {

		if( err !== null && err !== undefined && err.length ) {
			jQuery('#update-error').show();
		} else {
			jQuery('#update-error').hide();
		}

		jQuery('#update-error').html(err);

	}

</script>
