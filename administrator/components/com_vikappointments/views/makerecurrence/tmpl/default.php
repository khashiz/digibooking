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

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$recurrence_params = VikAppointments::getRecurrenceParams();

$title = JText::sprintf(
		'VAPSAYRESERVATIONDETAILS',
		$this->order['sname'],
		$this->order['ename'],
		date($date_format . ' ' . $time_format, $this->order['checkin_ts'])
	);

?>

<form action="index.php" name="adminForm" id="adminForm">

	<div class="span12">
		<?php echo $vik->openFieldset($title, 'form-horizontal'); ?>

			<div class="vap-makerec-box">
				<div class="vap-recurrence-form">
					<span class="lbl"><?php echo JText::_('VAPMANAGECONFIGREC2'); ?></span>
					
					<span>
						<?php
						$options = array();
						for ($i = 0; $i < count($recurrence_params['repeat']); $i++)
						{
							$options[] = JHtml::_('select.option', $i + 1, JText::_('VAPMANAGECONFIGRECSINGOPT' . ($i + 1)));
						}
						?>
						<select id="vaprepeatbyrecsel" onChange="recurrenceSelectChanged();">
							<?php echo JHtml::_('select.options', $options); ?>
						</select>
					</span>&nbsp;&nbsp;
					
					<span class="lbl"><?php echo JText::_('VAPMANAGECONFIGREC5'); ?></span>
					
					<span>
						<?php
						$options = array();
						for ($i = $recurrence_params['min']; $i <= $recurrence_params['max']; $i++)
						{
							$options[] = JHtml::_('select.option', $i, $i);
						}
						?>
						<select id="vapamountrecsel">
							<?php echo JHtml::_('select.options', $options); ?>
						</select>
					</span>
					
					<span>
						<?php
						$options = array();
						for ($i = 0; $i < count($recurrence_params['for']); $i++)
						{
							$options[] = JHtml::_('select.option', $i + 1, JText::_('VAPMANAGECONFIGRECMULTOPT' . ($i + 1)));
						}
						?>
						<select id="vapfornextrecsel">
							<?php echo JHtml::_('select.options', $options); ?>
						</select>
					</span>
				</div>

				<div class="vap-recurrence-button">
					<button type="button" class="btn" onClick="launchRecurrencePreview(this);">
						<?php echo JText::_('VAPMAKERECGETPREVIEW'); ?>
					</button>
				</div>

			</div>

			<div class="vap-recpreview-box" id="vap-recpreview-box" style="display: none;">
				<div class="vap-recpreview-container" id="vap-recpreview-container">

				</div>
				<div class="vap-recpreview-button">
					<button type="button" class="btn" onClick="makeRecurrence(this);" id="vap-mkrec-btn">
						<?php echo JText::_('VAPMAKERECLAUNCHPROC'); ?>
					</button>
				</div>
			</div>

			<div class="vap-confirmrec-box" id="vap-confirmrec-box" style="display: none;">

			</div>

			<div class="vap-recerror-box" id="vap-recerror-box" style="display: none;">

			</div>

		<?php echo $vik->closeFieldset(); ?>
	</div>

	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->order['id']; ?>" />
</form>

<?php
JText::script('VAPCONNECTIONLOSTERROR');
?>

<script>

	jQuery(document).ready(function() {

		jQuery('#vaprepeatbyrecsel, #vapfornextrecsel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 120
		});

		jQuery('#vapamountrecsel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 75
		});

	});

	function recurrenceSelectChanged() {
		var val = jQuery('#vaprepeatbyrecsel').val();
		jQuery('#vapfornextrecsel').select2('val', val);
	}

	function launchRecurrencePreview(btn) {

		changeButtonStatus(btn, 0);

		jQuery('#vap-recpreview-box').fadeOut();
		jQuery('#vap-recpreview-container').html('');
		jQuery('#vap-recerror-box').fadeOut();
		jQuery('#vap-confirmrec-box').fadeOut();

		var _by 	= jQuery('#vaprepeatbyrecsel').val();
		var _amount = jQuery('#vapamountrecsel').val();
		var _for 	= jQuery('#vapfornextrecsel').val();

		jQuery.noConflict();

		var jqxhr = jQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_vikappointments&task=get_recurrence_preview&tmpl=component&id=<?php echo $this->order['id']; ?>',
			data: {
				r_by: _by,
				r_amount: _amount,
				r_for: _for
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp);

			if (obj.status) {

				var at_least_one = false;

				var _html = '';
				jQuery.each(obj.dates, function(k, v){
					_html += '<div class="recurrence-date">\n'+
						'<span class="format">'+v.format+'</span>\n'+
						'<span class="msg '+(v.available ? 'available' : 'occupied')+'">'+v.message+'</span>\n'+
					'</div>\n';

					if (v.available) {
						at_least_one = true;
					}
				})

				changeButtonStatus(jQuery('#vap-mkrec-btn'), at_least_one);

				jQuery('#vap-recpreview-container').html(_html);
				jQuery('#vap-recpreview-box').fadeIn();
			} else {
				jQuery('#vap-recpreview-box').fadeOut();

				jQuery('#vap-recerror-box').html(obj.errstr);
				jQuery('#vap-recerror-box').fadeIn();
			}

			changeButtonStatus(btn, 1);

		}).fail(function(resp) {
			jQuery('#vap-recpreview-box').fadeOut();

			jQuery('#vap-recerror-box').html(Joomla.JText._('VAPCONNECTIONLOSTERROR'));
			jQuery('#vap-recerror-box').fadeIn();

			changeButtonStatus(btn, 1);
		});

	}

	var _RESPONSE_ERROR = null;

	function makeRecurrence(btn) {

		changeButtonStatus(btn, 0);

		jQuery('#vap-confirmrec-box').fadeOut();
		jQuery('#vap-recerror-box').fadeOut();

		var _by 	= jQuery('#vaprepeatbyrecsel').val();
		var _amount = jQuery('#vapamountrecsel').val();
		var _for 	= jQuery('#vapfornextrecsel').val();

		jQuery.noConflict();

		var jqxhr = jQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_vikappointments&task=create_recurrence_for&tmpl=component&id=<?php echo $this->order['id']; ?>',
			data: {
				r_by: _by,
				r_amount: _amount,
				r_for: _for
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp);

			if (obj.status) {
				jQuery('#vap-confirmrec-box').html(obj.message);
				jQuery('#vap-confirmrec-box').fadeIn();
			} else {
				jQuery('#vap-confirmrec-box').fadeOut();

				jQuery('#vap-recerror-box').html(obj.errstr);
				jQuery('#vap-recerror-box').fadeIn();
			}

			changeButtonStatus(btn, 1);

		}).fail(function(resp) {
			console.log(resp);

			jQuery('#vap-confirmrec-box').fadeOut();

			jQuery('#vap-recerror-box').html(Joomla.JText._('VAPCONNECTIONLOSTERROR'));
			jQuery('#vap-recerror-box').fadeIn();

			changeButtonStatus(btn, 1);
		});

	}

	function changeButtonStatus(btn, status) {
		jQuery(btn).prop('disabled', status ? false : true);
	}

</script>
