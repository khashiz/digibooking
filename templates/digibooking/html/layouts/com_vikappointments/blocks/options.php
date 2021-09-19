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

$options = isset($displayData['options']) ? $displayData['options'] : array();

$vik = UIApplication::getInstance();

?>

<div class="vapseroptionscont <?php echo $vik->getThemeClass('background'); ?>" style="display: none;">

	<div class="vapseroptionsheader">
		<?php echo JText::_('VAPSERAVAILOPTIONSTITLE'); ?>
	</div>
	
	<div class="vapseroptionsdiv">
		
		<?php
		foreach ($options as $k => $o)
		{ 
			$oname = $o['name'];
			$odesc = $o['description'];

			$oprice = $o['price'];
			if (count($o['variations']))
			{
				$oprice += $o['variations'][0]['inc_price'];
			}

			$olabel = $oname . ($o['required'] ? '*' : '');

			?>

			<div class="vapsersingoption">
				
				<div class="vapseroptrow">
					
					<?php
					if ($o['displaymode'] == 2)
					{
						// display option image before the name
						?>
						<span class="vapseroptimage left-side">
							<a href="javascript: void(0);" class="vapmodal" onClick="vapOpenModalImage('<?php echo VAPMEDIA_URI . $o['image']; ?>');">
								<img src="<?php echo VAPMEDIA_SMALL_URI . $o['image']; ?>" alt="<?php echo $oname; ?>" />
							</a>
						</span>
						<?php
					}
					?>

					<span class="vapseroptname" title="<?php echo str_replace('"', '&quot;', $odesc); ?>">
						<?php
						if ($o['displaymode'] == 1)
						{
							// open the image in a modal box after clicking the option name
							?>
							<a href="javascript: void(0);" class="vapmodal <?php echo ($o['required'] ? 'option-required' : ''); ?>" 
								<?php echo ($o['required'] ? 'id="vapreqopt' . $o['id'] . '"' : ''); ?>
								onClick="vapOpenModalImage('<?php echo VAPMEDIA_URI . $o['image']; ?>');">
								<?php echo $olabel; ?>
							</a>
							<?php
						}
						else
						{
							// just use a label to toggle the checkbox
							?>
							<label 
								class="<?php echo ($o['required'] ? 'option-required' : ''); ?>"
								<?php echo ($o['required'] ? 'id="vapreqopt' . $o['id'] . '"' : ''); ?>
								for="vapoptchbox<?php echo $k; ?>"><?php echo $olabel; ?></label>
							<?php
						}
						?>
					</span>

					<?php if (count($o['variations'])) { ?>

						<span class="vapseropt-variations">
							<select id="vapoptvar<?php echo $k; ?>" class="vap-optvar-sel" onChange="vapOptionVarValueChanged(<?php echo $k; ?>);">
								<?php
								foreach ($o['variations'] as $var)
								{
									$var_price = $o['price'] + $var['inc_price'];

									if ($var_price != 0)
									{
										$tot_price_label = VikAppointments::printPriceCurrencySymb($var_price);
									}
									else
									{
										$tot_price_label = JText::_('VAPFREE');
									}

									if ($var['inc_price'] != 0)
									{
										$var_price_label = ' ' . VikAppointments::printPriceCurrencySymb($var['inc_price']);
									}
									else
									{
										$var_price_label = '';
									}
									?>
									<option value="<?php echo $var['id']; ?>" data-price="<?php echo $tot_price_label; ?>">
										<?php echo $var['name'] . $var_price_label; ?>
									</option>
								<?php } ?>
							</select>
						</span>

					<?php } ?>

					<span id="vapseroptprice<?php echo $k; ?>" class="vapseroptprice">
						<?php echo ($oprice != 0 ? VikAppointments::printPriceCurrencySymb($oprice) : JText::_('VAPFREE')); ?>
					</span>

				</div>

				<div class="vapseroptact">
					<?php
					if ($o['single'])
					{
						?>
						<input type="number" value="1" size="4" min="1" max="<?php echo $o['maxq']; ?>"
							id="vapoptmaxq<?php echo $k; ?>" onChange="vapQuantityValueChanged(<?php echo $k; ?>);" style="max-width: 80px;" />
						<?php
					}
					else
					{
						?>
						<input type="hidden" value="1" id="vapoptmaxq<?php echo $k; ?>" />	
						<?php
					}
					?>
					<input type="checkbox" value="1" id="vapoptchbox<?php echo $k; ?>" class="option-checkbox <?php echo $o['required'] ? 'required' : ''; ?>" data-id="<?php echo $o['id']; ?>" />
					<input type="hidden" value="<?php echo $o['id']; ?>" id="vapoptid<?php echo $k; ?>" />
				</div>

			</div>

		<?php } ?>

	</div>

</div>

<script>

	jQuery(document).ready(function() {

		jQuery('.vap-optvar-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 200
		});

		jQuery('.vapseroptname').tooltip();

	});

	function vapGetSelectedOptions() {
		var options = new Array();
		var app;
		
		for (var i = 0; i < <?php echo count($options); ?>; i++) {
			
			if (jQuery('#vapoptchbox' + i).is(':checked')) {

				app = jQuery('#vapoptvar' + i).val();

				options[options.length] = {
					id : 		jQuery('#vapoptid' + i).val(),
					quant : 	jQuery('#vapoptmaxq' + i).val(),
					variation : (app !== undefined ? app : -1),
				};

			} else if (jQuery('#vapoptchbox' + i).hasClass('required')) {
				markRequiredOptions(true);
				throw "MissingRequiredOptionException";
			}
		}

		markRequiredOptions(false);

		return options;
	}

	function vapOptionVarValueChanged(id) {
		jQuery('#vapseroptprice'+id).html(jQuery('#vapoptvar'+id+' :selected').attr('data-price'));
		jQuery('#vapoptchbox'+id).prop('checked', true);
	}

	function vapQuantityValueChanged(id) {
		jQuery('#vapoptchbox'+id).prop('checked', true);
	}

	function markRequiredOptions(s) {
		jQuery('.option-checkbox.required').each(function() {

			var id 		= jQuery(this).data('id');
			var label 	= jQuery('#vapreqopt' + id);

			if (s) {
				label.addClass('vapoptred');
			} else {
				label.removeClass('vapoptred');
			}

		});
	}

</script>
