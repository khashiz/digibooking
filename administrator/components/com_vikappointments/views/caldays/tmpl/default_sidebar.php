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

$vik 	= UIApplication::getInstance();
$config = UIFactory::getConfig();

?>

<!-- SERVICES -->

<?php echo $vik->openEmptyFieldset(); ?>
		
	<div class="cal-services-list">
		<?php
		foreach ($this->services as $s)
		{
			$checked = empty($this->filters['services']) || in_array($s->id, $this->filters['services']) ? 'checked="checked"' : '';

			?>
			<div class="cal-service">
				<span class="check">
					<input type="checkbox" name="services[]" id="service-checkbox<?php echo $s->id; ?>" 
						value="<?php echo $s->id; ?>" <?php echo $checked; ?> onchange="document.adminForm.submit();" />
				</span>

				<span class="name">
					<label for="service-checkbox<?php echo $s->id; ?>"><?php echo $s->name; ?></span>
				</span>

				<span class="color-thumb clickable" data-id="<?php echo $s->id; ?>" data-hex="<?php echo $s->color; ?>" style="background-color: #<?php echo $s->color; ?>;">&nbsp;</span>
			</div>
			<?php
		}
		?>
	</div>

<?php echo $vik->closeEmptyFieldset(); ?>

<!-- EVENTS -->

<?php echo $vik->openEmptyFieldset(); ?>
	
	<div class="cal-events-list">

		<?php
		if (!$this->calendar->has())
		{
			?>
			<p class="no-event"><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></p>
			<?php
		}
		else
		{
			$max_events = 5;
			$i = 0;

			foreach ($this->calendar->getEventsList() as $i => $event)
			{
				?>
				<div class="event-row" style="<?php echo $i >= $max_events ? 'display: none;' : ''; ?>">

					<div class="event-id">
						<span><?php echo $event->id; ?></span>
					</div>

					<div class="event-checkin">
						<span><?php echo ArasJoomlaVikApp::jdate($config->get('dateformat'), $event->checkin_ts); ?></span>
						<span>
							<span>
								<a href="javascript: void(0);" onclick="goToSlot(<?php echo $event->id; ?>);">
									@<?php echo ArasJoomlaVikApp::jdate($config->get('timeformat'), $event->checkin_ts); ?>
								</a>
							</span>
							<span class="duration"><?php echo VikAppointments::formatMinutesToTime($event->duration); ?></span>
						</span>
					</div>

					<div class="event-details">
						<span class="service-name" style="color: #<?php echo $event->service_color; ?>;"><?php echo $event->service_name; ?></span>
						<span><?php echo $event->employee_name; ?></span>
					</div>

					<div class="event-customer">
						<span><?php echo $event->purchaser_nominative; ?></span>
					</div>

					<div class="event-guests">
						<?php
						if ($event->people > 1)
						{
							?>
							<span><?php echo $event->people; ?> <i class="fa fa-users"></i></span>
							<?php
						}
						?>
					</div>

				</div>
				<?php
			}

			if ($i >= $max_events)
			{
				?>
				<div class="show-all-events">
					<button type="button" class="btn" onclick="jQuery('.event-row').show();jQuery(this).hide();"><?php echo JText::_('VAPSHOWALL'); ?></button>
				</div>
				<?php
			}
		}
		?>

	</div>

<?php echo $vik->closeEmptyFieldset(); ?>

<div class="slot-pointer" style="display: none;"></div>

<script>

	jQuery(document).ready(function() {

		var THUMB_ELEM  = null;
		var THUMB_COLOR = null;

		jQuery('.color-thumb.clickable').ColorPicker({
			onShow: function() {
				THUMB_COLOR = jQuery(this).attr('data-hex');
				jQuery(this).ColorPickerSetColor('#' + THUMB_COLOR.toUpperCase());

				THUMB_ELEM = this;
			},
			onChange: function (hsb, hex, rgb) {
				THUMB_COLOR = hex;
			},
			onHide: function() {
				if (THUMB_COLOR.toUpperCase() != jQuery(THUMB_ELEM).attr('data-hex').toUpperCase()) {
					changeThumbColor(THUMB_COLOR, THUMB_ELEM);
				}
			}
		});

	});

	function changeThumbColor(color, elem) {
		jQuery(elem).attr('data-hex', color);
		jQuery(elem).css('background-color', '#' + color);

		var id_service = jQuery(elem).data('id');

		jQuery('td div[data-service="' + id_service + '"]').css('background-color', '#' + color);

		doAjaxWithRetries('index.php?option=com_vikappointments&task=change_service_color&tmpl=component', {
			id: 	id_service,
			color: 	color
		});
	}

	function goToSlot(id) {

		jQuery('td div[data-id]').each(function() {
			var ids = jQuery(this).data('id');

			if (!isNaN(ids)) {
				ids = [ids];
			} else {
				ids = ids.split(',');
			}

			for (var i = 0; i < ids.length; i++) {
				if (ids[i] == id) {
					animateSlot(this);
					return false;
				}
			}
		});

	}

	var POINTER_TIMER = null;

	function animateSlot(slot) {
		// animate only in case the slot is not visible
		var px_to_scroll = isBoxOutOfMonitor(jQuery(slot));
			
		if (px_to_scroll !== false) {
			px_to_scroll += jQuery(slot).height() / 2;
			jQuery('html,body').animate( {scrollTop: "+=" + px_to_scroll}, {duration:'normal'} );
		}

		var pointer = jQuery('.slot-pointer');
		var offset  = jQuery(slot).offset();

		var top  = offset.top + jQuery(slot).height() / 2 - pointer.height() / 2;
		var left = offset.left + jQuery(slot).width() + 30;

		pointer.css('top', top + 'px');
		pointer.css('left', left + 'px');

		pointer.show();

		if (POINTER_TIMER) {
			clearTimeout(POINTER_TIMER);
		}
		
		POINTER_TIMER = setTimeout(function() {
			pointer.hide();
		}, 2000);
	}

</script>
