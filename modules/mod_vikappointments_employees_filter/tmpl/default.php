<?php
/** 
 * @package   	VikAppointments
 * @subpackage 	com_vikappointments
 * @author    	Matteo Galletti - e4j
 * @copyright 	Copyright (C) 2018 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license  	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link 		https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$itemid = $params->get('itemid', null);

?>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeeslist' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" id="vap-modemp-form">

	<div class="vap-empfilter-mainmod">
		
		<?php if ($filters['filters']['group']) { ?>

			<div class="vap-empfilter-item">
				<div class="vap-empfilter-item-label">
					<?php echo JText::_('VAPGROUP'); ?>
				</div>
				<div class="vap-empfilter-item-input">
					<select name="filters[group]" class="vap-modfilter-sel vap-mod-select2" id="vap-modgroups-sel">
						<option></option>
						<?php
						foreach ($groups as $g)
						{
							?>
							<option value="<?php echo $g['id']; ?>" <?php echo $g['id'] == $id_group ? 'selected="selected"' : ''; ?>><?php echo $g['name']; ?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>

		<?php } ?>
		
		<?php if ($filters['filters']['service']) { ?>

			<div class="vap-empfilter-item">
				<div class="vap-empfilter-item-label">
					<?php echo JText::_('VAPSERVICE'); ?>
				</div>
				<div class="vap-empfilter-item-input">
					<select name="filters[service]" class="vap-modfilter-sel vap-mod-select2" id="vap-modservices-sel">
						<option></option>
						<?php
						foreach ($services as $s)
						{
							?>
							<option value="<?php echo $s['id']; ?>" <?php echo $s['id'] == $id_service ? 'selected="selected"' : ''; ?>><?php echo $s['name']; ?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>

		<?php } ?>
			
		<?php if ($filters['filters']['country'] && !$filters['filters']['zip']) { ?>

			<div class="vap-empfilter-item">
				<div class="vap-empfilter-item-label">
					<?php echo JText::_('VAPCOUNTRY'); ?>
				</div>
				<div class="vap-empfilter-item-input">
					<select name="filters[country]" class="vap-modfilter-sel vap-mod-select2" id="vap-modcountries-sel">
						<option></option>
						<?php
						foreach ($countries as $c)
						{
							?>
							<option value="<?php echo $c['id']; ?>" <?php echo $c['id'] == $id_country ? 'selected="selected"' : ''; ?>><?php echo $c['country_name']; ?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>

		<?php } ?>
		
		<?php if ($filters['filters']['state'] && !$filters['filters']['zip']) { ?>

			<div class="vap-empfilter-item">
				<div class="vap-empfilter-item-label">
					<?php echo JText::_('VAPSTATE'); ?>
				</div>
				<div class="vap-empfilter-item-input">
					<select name="filters[state]" class="vap-modfilter-sel vap-mod-select2" id="vap-modstates-sel">
						<option></option>
						<?php
						foreach ($states as $s)
						{
							?>
							<option value="<?php echo $s['id']; ?>" <?php echo $s['id'] == $id_state ? 'selected="selected"' : ''; ?>><?php echo $s['state_name']; ?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>

		<?php } ?>
		
		<?php if ($filters['filters']['city'] && !$filters['filters']['zip']) { ?>

			<div class="vap-empfilter-item">
				<div class="vap-empfilter-item-label">
					<?php echo JText::_('VAPCITY'); ?>
				</div>
				<div class="vap-empfilter-item-input">
					<select name="filters[city]" class="vap-modfilter-sel vap-mod-select2" id="vap-modcities-sel">
						<option></option>
						<?php
						foreach ($cities as $c)
						{
							?>
							<option value="<?php echo $c['id']; ?>" <?php echo $c['id'] == $ref['filters']['city'] ? 'selected="selected"' : ''; ?>><?php echo $c['city_name']; ?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>

		<?php } ?>
		
		<?php if ($filters['filters']['zip']) { ?>

			<div class="vap-empfilter-item">
				<div class="vap-empfilter-item-label">
					<?php echo JText::_('VAPZIP'); ?>
				</div>
				<div class="vap-empfilter-item-input">
					<input type="text" name="filters[zip]" value="<?php echo $ref['filters']['zip']; ?>" id="vap-modzip-text" /> 
				</div>
			</div>

		<?php } ?>

		<?php
		/**
		 * Display the custom filters.
		 *
		 * @since 1.2
		 */
		if ($filters['filters']['custom'])
		{
			foreach ($filters['filters']['custom'] as $cf)
			{
				$key 	= 'field_' . $cf['formname'];
				$value 	= !empty($ref['filters'][$key]) ? $ref['filters'][$key] : '';

				?>
				<div class="vap-empfilter-item">
					<div class="vap-empfilter-item-label">
						<?php echo $cf['langname']; ?>
					</div>
					<div class="vap-empfilter-item-input">

						<?php
						if (VAPCustomFields::isSelect($cf))
						{
							$original = array_filter(explode(';;__;;', $cf['_choose']));
							$options  = array_filter(explode(';;__;;', $cf['choose']));

							?>
							<select name="filters[<?php echo $key; ?>]" id="vap-<?php echo $key; ?>-select" class="vap-modfilter-sel vap-mod-select2">
								
								<option></option>

								<?php foreach ($options as $i => $opt) { ?>

									<option 
										value="<?php echo $original[$i]; ?>"
										<?php echo ($original[$i] == $value ? 'selected="selected"' : ''); ?>
									><?php echo JText::_($opt); ?></option>

								<?php } ?>

							</select>
							<?php
						}
						else
						{
							?>
							<input type="text" name="filters[<?php echo $key; ?>]" value="<?php echo $value; ?>" id="vap-<?php echo $key; ?>-text" /> 
							<?php
						}
						?>

					</div>
				</div>
				<?php
			}
		}
		?>

		<?php if ($filters['filters']['nearby']) { ?>

			<div class="vap-empfilter-item">
				<div class="vap-empfilter-item-label">
					<input type="checkbox" name="filters[nearby]" value="1" onChange="vapModNearbyValueChanged(this);" id="vap-modnearby-check" />
					<label for="vap-modnearby-check"><?php echo JText::_('VAPNEARBY'); ?></label>
				</div>
			</div>

			<div class="vap-empfilter-item" id="vap-moddistance-field" style="display: none;">
				<div class="vap-empfilter-item-label">
					<?php echo JText::_('VAPDISTANCE'); ?>
				</div>
				<div class="vap-empfilter-item-input">
					<select name="filters[distance]" class="vap-modfilter-sel" id="vap-moddistance-sel">
						<?php
						foreach ($filters['nearby_params']['distances'] as $d)
						{
							$unit = $filters['nearby_params']['distunit'] ? $filters['nearby_params']['distunit'] : 'km';
							?>
							<option
								value="<?php echo $d; ?>"
								<?php echo $d == $ref['filters']['distance'] ? 'selected="selected"' : ''; ?>
							><?php echo $d . ' ' . $unit; ?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>

		<?php } ?>
		
		<?php if ($filters['filters']['price']) { ?>

			<div class="vap-empfilter-item">
				<div class="vap-empfilter-item-label">
					<?php echo JText::_('VAPPRICE'); ?>
				</div>
				<div class="vap-empfilter-item-input">
					<div id="vap-modpricerange-slider"></div>
					<div class="vap-modpricerange-values">
						<span class="left">
							<?php echo VikAppointmentsEmployeesFilterHelper::printPrice($filters['price_range']['min']); ?>
						</span>
						<span class="center" id="vappricerange">
							<?php echo VikAppointmentsEmployeesFilterHelper::printPrice($picked_price_range[0]); ?>,
							<?php echo VikAppointmentsEmployeesFilterHelper::printPrice($picked_price_range[1]); ?>
						</span>
						<span class="right">
							<?php echo VikAppointmentsEmployeesFilterHelper::printPrice($filters['price_range']['max']); ?>+
						</span>
					</div>
				</div>

				<input type="hidden" name="filters[price]" value="<?php echo $picked_price_range[0] . ':' . $picked_price_range[1]; ?>" id="vapfilterprice" />
			</div>

		<?php } ?>
		
		<div class="vap-empfilter-search">
			<button type="submit" class="vap-empfilter-submit">
				<?php echo JText::_('VAPSEARCHBTN'); ?>
			</button>
		</div>
		
	</div>
	
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="view" value="employeeslist" />

	<?php
	/**
	 * Auto-fill current position if set in the request.
	 *
	 * @since 1.2
	 */
	if ($ref['filters']['base_coord'])
	{
		?>
		<input type="hidden" name="filters[base_coord]" value="<?php echo $ref['filters']['base_coord']; ?>" />
		<?php
	}
	?>

	<?php
	/**
	 * Display the custom filters.
	 *
	 * @since 1.2
	 */
	if ($filters['nearby_params']['distunit'])
	{
		?>
		<input type="hidden" name="filters[distunit]" value="<?php echo $filters['nearby_params']['distunit']; ?>" />
		<?php
	}
	?>

	<?php if ($itemid) { ?>
		<input type="hidden" name="Itemid" value="<?php echo $params->get('itemid', ''); ?>" />
	<?php } ?>
	
</form>

<?php
JText::script('VAPGEOLOCATIONERR1');
JText::script('VAPGEOLOCATIONERR2');
?>

<script>

	jQuery(document).ready(function() {

		jQuery(".vap-mod-select2").select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('#vap-moddistance-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300,
		});
		
		jQuery("#vap-modpricerange-slider").slider({
			range: true,
			min: <?php echo $filters['price_range']['min']; ?>,
			max: <?php echo $filters['price_range']['max']; ?>,
			values: <?php echo json_encode($picked_price_range); ?>,
			slide: function(event, ui) {
				var currency = Currency.getInstance();
				jQuery("#vappricerange").html(currency.format(ui.values[0], 0) + ', ' + currency.format(ui.values[1], 0));
				jQuery("#vapfilterprice").val(ui.values[0]+":"+ui.values[1]);
			}
		});
		
		jQuery("#vap-modgroups-sel").on('change', function(){
			vapModGetAndSetServices();
		});
		
		jQuery("#vap-modcountries-sel").on('change', function(){
			vapModGetAndSetStates();
			
			vapModGetAndSetCities();
		});
		
		jQuery("#vap-modstates-sel").on('change', function(){
			vapModGetAndSetCities();
		});

		<?php if ($ref['filters']['base_coord']) { ?>

			// auto-check neary input
			jQuery('input[name="filters[nearby]"]').prop('checked', true).trigger('change');

		<?php } ?>
		
	});
	
	// GET SERVICES

	function vapModGetAndSetServices() {
		if (jQuery("#vap-modservices-sel").length == 0) {
			// exit if services dropdown doesn't exist
			return;
		}
		
		jQuery("#vap-modservices-sel").html('<option></option>');
		jQuery("#vap-modservices-sel").select2('val', '');
		
		var id_group = jQuery("#vap-modgroups-sel").select2("val");
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "post",
			url: '<?php echo JRoute::_("index.php?option=com_vikappointments&task=get_services_with_group&tmpl=component" . ($itemid ? "&Itemid=" . $itemid : ''), false); ?>',
			data: {
				id_group: id_group
			}
		}).done(function(resp) { 
			var obj = jQuery.parseJSON(resp);
			
			jQuery.each(obj, function(k, v) {
				jQuery("#vap-modservices-sel").append('<option value="' + v.id + '">' + v.name + '</option>');
			});
			
		});
	}
	
	// GET STATES

	function vapModGetAndSetStates() {
		if (jQuery("#vap-modstates-sel").length == 0) {
			// exit if states dropdown doesn't exist
			return;
		}
		
		jQuery("#vap-modstates-sel").html('<option></option>');
		jQuery("#vap-modstates-sel").select2('val', '');
		
		var id_country = jQuery("#vap-modcountries-sel").select2("val");
		if (id_country.length == 0) {
			return;
		}
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "post",
			url: '<?php echo JRoute::_("index.php?option=com_vikappointments&task=get_states_with_country&tmpl=component" . ($itemid ? "&Itemid=" . $itemid : ''), false); ?>',
			data: {
				id_country: id_country
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp);
			
			jQuery.each(obj, function(k, v) {
				jQuery("#vap-modstates-sel").append('<option value="' + v.id + '">' + v.state_name + '</option>');
			});
			
		});
	}
	
	// GET CITIES

	function vapModGetAndSetCities() {
		if (jQuery("#vap-modcities-sel").length == 0) {
			// exit if cities dropdown doesn't exist
			return;
		}
		
		jQuery("#vap-modcities-sel").html('<option></option>');
		jQuery("#vap-modcities-sel").select2('val', '');
		
		var id_state = jQuery("#vap-modstates-sel").select2("val");
		if (id_state.length == 0) {
			return;
		}
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "post",
			url: '<?php echo JRoute::_("index.php?option=com_vikappointments&task=get_cities_with_state&tmpl=component" . ($itemid ? "&Itemid=" . $itemid : ''), false); ?>',
			data: {
				id_state: id_state
			}
		}).done(function(resp) { 
			var obj = jQuery.parseJSON(resp);
			
			jQuery.each(obj, function(k, v){
				jQuery("#vap-modcities-sel").append('<option value="' + v.id + '">' + v.city_name + '</option>');
			});

		});
	}

	// NEARBY FUNCTIONS

	function vapModNearbyValueChanged(checkbox) {

		var is = jQuery(checkbox).is(':checked');

		jQuery('#vap-modcountries-sel').prop('disabled', (is ? true : false));
		jQuery('#vap-modstates-sel').prop('disabled', (is ? true : false));
		jQuery('#vap-modcities-sel').prop('disabled', (is ? true : false));
		jQuery('#vap-modzip-text').prop('disabled', (is ? true : false));

		if (is) {
			jQuery('#vap-moddistance-field').slideDown();

			vapModGetUserCoordinates();
		} else {
			jQuery('#vap-moddistance-field').slideUp();
		}
	}

	function vapModGetUserCoordinates() {
		if (jQuery('#vap-modemp-form').find('input[name="filters[base_coord]"]').length > 0) {
			// if the base_coord input already exists, the position won't be evaluated
			return;
		}

		// Try HTML5 geolocation
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				/**
				 * @todo do not use Google Map LatLng object.
				 */
				var coord = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
				jQuery('#vap-modemp-form').append('<input type="hidden" name="filters[base_coord]" value="' + coord.lat() + "," + coord.lng() + '" />');
			}, function() {
				// ignore failed geolocation service
				vapModGeolocationFailed(Joomla.JText._("VAPGEOLOCATIONERR1"), false);
			});
		} else {
			// Browser doesn't support Geolocation
			vapModGeolocationFailed(Joomla.JText._("VAPGEOLOCATIONERR2"), true);
		}

	}

	function vapModGeolocationFailed(message, disable) {
		alert(message);

		jQuery('#vap-modnearby-check').prop('checked', false).prop('disabled', disable).trigger('change');
	}

</script>
