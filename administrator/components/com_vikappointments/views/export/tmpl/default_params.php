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

JHtml::_('formbehavior.chosen');
JHtml::_('bootstrap.popover');

$vik = UIApplication::getInstance();

?>

<style>

	.export-params label {
		font-weight: bold;
	}

</style>

<div style="padding: 10px;">
	<?php echo $vik->openEmptyFieldset(); ?>

		<!-- NAME - Text -->
		<?php echo $vik->openControl(JText::_('VAPEXPORTRES1') . '*'); ?>
			<input type="text" name="filename" value="<?php echo $this->type; ?>" class="required" size="32" />
		<?php echo $vik->closeControl(); ?>

		<!-- EXPORT CLASS - Select -->
		<?php
		$elements = array();
		$elements[] = JHtml::_('select.option', '', '--');

		foreach ($this->exportList as $key => $exportable)
		{
			$elements[] = JHtml::_('select.option', $key, strtoupper($key));
		}
		?>
		<?php echo $vik->openControl(JText::_('VAPEXPORTRES2') . '*'); ?>
			<select name="export_class" class="required" id="export-class">
				<?php echo JHtml::_('select.options', $elements); ?>
			</select>
		<?php echo $vik->closeControl(); ?>

		<div class="export-params"></div>

	<?php echo $vik->closeEmptyFieldset(); ?>
</div>

<?php
JText::script('VAPCONNECTIONLOSTERROR');
?>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('#adminForm');

		jQuery('#export-class').on('change', function() {

			var type = jQuery(this).val();

			validator.unregisterFields('.export-params .required');

			jQuery('.export-params').html('');

			if (!type.length) {
				return;
			}

			// disable export btn
			jQuery('#export-btn').prop('disabled', true);

			var xhr = jQuery.ajax({
				type: 'post',
				url: 'index.php?option=com_vikappointments&task=get_export_params&tmpl=component',
				data: {
					import_type: '<?php echo $this->type; ?>',
					export_class: type
				}
			}).done(function(resp) {

				var html = jQuery.parseJSON(resp);
				jQuery('.export-params').html(html);

				// register new required fields
				validator.registerFields('.export-params .required');

				// render new select
				VikRenderer.chosen('.export-params');

				// render popover
				jQuery('.export-params .hasPopover').popover({trigger: 'hover'});

				// enable export btn again
				jQuery('#export-btn').prop('disabled', false);

			}).fail(function(resp) {

				console.log(resp);
				alert(Joomla.JText._('VAPCONNECTIONLOSTERROR'));

				// enable export btn again
				jQuery('#export-btn').prop('disabled', false);

			});

		});

	});

	// validate

	var validator = new VikFormValidator('#adminForm');

	function downloadExport() {

		if (!validator.validate()) {
			return false;
		}

		jQuery('#jmodal-download').modal('hide');

		Joomla.submitform('downloadExport', document.adminForm);
	}

</script>
