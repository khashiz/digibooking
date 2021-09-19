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

$cart_enabled 			= isset($displayData['cartEnabled']) 		? $displayData['cartEnabled'] 		: false;
$cart_empty				= isset($displayData['cartEmpty'])			? $displayData['cartEmpty']			: true; 
$waiting_list_enabled 	= isset($displayData['waitlistEnabled']) 	? $displayData['waitlistEnabled'] 	: false;
$recurrence_enabled 	= isset($displayData['recurrenceEnabled']) 	? $displayData['recurrenceEnabled'] : false;
$recurrence 			= isset($displayData['recurrenceParams']) 	? $displayData['recurrenceParams'] 	: array();
$itemid 	 			= isset($displayData['itemid'])				? $displayData['itemid']			: null;

if (is_null($itemid))
{
	// item id not provided, get the current one (if set)
	$itemid = JFactory::getApplication()->input->getInt('Itemid');
}

$vik = UIApplication::getInstance();

if ($recurrence_enabled)
{ 		
	?>
	<div class="vaprecurrencediv <?php echo $vik->getThemeClass('background'); ?>" style="display: none;">
		
		<div class="vaprecurrenceprediv">
			<input type="checkbox" value="1" onChange="vapRecurrenceConfirmValueChanged();" id="vaprecokcheck" />

			<label for="vaprecokcheck"><?php echo JText::_('VAPRECURRENCECONFIRM'); ?></label>
		</div>

		<div class="vaprecurrencenextdiv" style="display: none">
			
			<div class="recurrence-repeat-box">
				<span class="vaprecurrencerepeatlabel">
					<label for="vaprepeatbyrecsel"><?php echo JText::_('VAPRECURRENCEREPEAT'); ?></label>
				</span>

				<span class="vaprecurrencerepeatselect">
					<select id="vaprepeatbyrecsel" onChange="vapRecurrenceSelectChanged();">
						<option value="0"><?php echo JText::_('VAPRECURRENCENONE'); ?></option>
						<?php
						$repeat_text = array(
							'VAPDAY',
							'VAPWEEK',
							'VAPMONTH',
						);

						for ($i = 0; $i < count($recurrence['repeat']); $i++)
						{
							if ($recurrence['repeat'][$i] == 1)
							{
								?>
								<option value="<?php echo ($i + 1); ?>"><?php echo JText::_($repeat_text[$i]); ?></option>
								<?php
							}
						}
						?>
					</select>
				</span>
			</div>

			<div class="recurrence-for-box">
				<span class="vaprecurrenceforlabel">
					<label for="vapamountrecsel"><?php echo JText::_('VAPRECURRENCEFOR'); ?></label>
				</span>

				<span class="vaprecurrenceamountselect">
					<select id="vapamountrecsel">
						<?php
						for ($i = $recurrence['min']; $i <= $recurrence['max']; $i++)
						{
							?>
							<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php
						}
						?>
					</select>
				</span>

				<span class="vaprecurrenceforselect">
					<select id="vapfornextrecsel">
						<option value="0"><?php echo JText::_('VAPRECURRENCENONE'); ?></option>
						<?php
						$for_text = array(
							'VAPDAYS',
							'VAPWEEKS',
							'VAPMONTHS',
						);

						for ($i = 0; $i < count($recurrence['for']); $i++)
						{
							if ($recurrence['for'][$i] == 1)
							{
								?>
								<option value="<?php echo ($i + 1); ?>"><?php echo JText::_($for_text[$i]); ?></option>
								<?php
							}
						}
						?>
					</select>
				</span>
			</div>

		</div>
	</div>

<?php } ?>

<div class="vapbookbuttondiv">
	<div class="uk-grid-collapse uk-child-width-1-2 uk-margin-medium-top" data-uk-grid>
        <div>
            <div class="bookerrordiv" style="display: none;">
                <span id="bookerror-msg"><?php echo JText::_('VAPBOOKNOTIMESELECTED'); ?></span>
            </div>
            <div class="booksuccessdiv" style="display: none;">
                <span id="booksuccess-msg"><?php echo JText::_('VAPCARTITEMADDOK'); ?></span>
            </div>
        </div>
        <div>
            <div class="uk-grid-small" data-uk-grid>
                <?php if ($cart_enabled) { ?>
                    <div class="uk-width-1-1 uk-width-2-5@m additem">
                        <button type="button" class="uk-button uk-button-success uk-button-large uk-width-1-1" id="vapadditembutton" onClick="vapAddItemToCart();"><img src="<?php echo JUri::base().'images/sprite.svg#plus'; ?>" width="24" height="24" alt="" data-uk-svg>&ensp;<?php echo JText::_('VAPADDCARTBUTTON'); ?></button>
                    </div>
                <?php } ?>
                <div class="uk-width-1-1 uk-width-expand@m checkout">
                    <button type="button" class="uk-button uk-button-primary uk-button-large uk-width-1-1" onClick="vapBookNow();"><?php echo JText::_('VAPBOOKNOWBUTTON'); ?>&ensp;<img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" width="24" height="24" alt="" data-uk-svg></button>
                </div>
            </div>
        </div>
	</div>

	<?php if ($waiting_list_enabled) { ?>

		<div class="vapbookbuttoninnerdiv waitlist" id="vapwaitlistbox" style="display: none;">
			<?php
			/**
			 * See waiting list layout for further details about 
			 * the vapOpenWaitListOverlay() function.
			 *
			 * @link layouts/blocks/waitlist.php
			 *
			 * See the following views for further details about LAST_TIMESTAMP_USED.
			 *
			 * @link views/servicesearch/tmpl/default.php
			 * @link views/employeesearch/tmpl/default.php
			 */
			?>
			<button type="button" class="vap-btn dark-gray vapwaitlistbutton" onClick="vapOpenWaitListOverlay('vapaddwaitlistoverlay', LAST_TIMESTAMP_USED);">
				<i class="fa fa-calendar-plus-o"></i>
				<?php echo JText::_('VAPWAITLISTADDBUTTON'); ?>
			</button>
		</div>

	<?php } ?>

</div>

<?php
JText::script('VAPBOOKNOTIMESELECTED');
JText::script('VAPCARTITEMADDOK');
JText::script('VAPCARTMULTIITEMSADDOK');
?>

<script>

	var isTimeChoosen 		= false;
	var vapCheckoutProceed 	= <?php echo ($cart_enabled && !$cart_empty ? 1 : 0); ?>;

	jQuery(document).ready(function() {

		jQuery('#vaprepeatbyrecsel, #vapfornextrecsel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery('#vapamountrecsel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 70
		});

	});

	/**
	 * Used to book the selected details by submitting the form.
	 */
	function vapBookNow() {
		if (isTimeChoosen || vapCheckoutProceed) {

			try
			{
				var options = vapGetSelectedOptions();
			}
			catch (error)
			{
				if (error == 'MissingRequiredOptionException')
				{
					// do not proceed as the customer forgot to fill
					// one or more required fields
					return;
				}

				// Proceed because the service doesn't own any option 
				// and the function vapGetSelectedOptions() hasn't been declared.
				// Define an empty options array to avoid breaking the flow.
				var options = [];
			}
			
			var opt_ids 	= '';
			var opt_quant 	= '';
			var opt_vars 	= '';
			
			for (var i = 0; i < options.length; i++) {
				if (opt_ids.length > 0) {
					opt_ids 	+= ',';
					opt_quant 	+= ',';
					opt_vars 	+= ',';
				}

				opt_ids 	+= options[i]['id'];
				opt_quant 	+= options[i]['quant'];
				opt_vars 	+= options[i]['variation'];
			}
			
			jQuery('#vapempconfirmapp').append(
				'<input type="hidden" name="opt_ids" value="' + opt_ids + '" />'+
				'<input type="hidden" name="opt_quant" value="' + opt_quant + '" />'+
				'<input type="hidden" name="opt_vars" value="' + opt_vars + '" />'
			);

			var recurrence = vapGetSelectedRecurrence();
			
			if (recurrence) {
				jQuery('#vapempconfirmapp').append('<input type="hidden" name="recurrence" value="' + recurrence + '" />');
			}
			
			document.confirmapp.submit();
			
		} else {
			vapDisplayWrongMessage(2500, Joomla.JText._('VAPBOOKNOTIMESELECTED'));
		}
	}

	var _items_add_count 	= 0;
	var _items_timeout 		= null;
	var _items_bad_timeout 	= null;

	/**
	 * Used to book one or more services via AJAX.
	 */
	function vapAddItemToCart() {
		if( isTimeChoosen ) {
			
			var id_ser 	= jQuery("#vapconfserselected").val();
			var id_emp 	= jQuery("#vapconfempselected").val();
			var day 	= jQuery("#vapconfdayselected").val();
			var hour 	= jQuery("#vapconfhourselected").val();
			var min 	= jQuery("#vapconfminselected").val();
			var people	= jQuery("#vapconfpeopleselected").val();
				
			var ts 		= parseInt(day)
			var ts_hour = parseInt(hour);
			var ts_min 	= parseInt(min);
			
			try
			{
				var options = vapGetSelectedOptions();
			}
			catch (error)
			{
				if (error == 'MissingRequiredOptionException')
				{
					// do not proceed as the customer forgot to fill
					// one or more required fields
					return;
				}

				// Proceed because the service doesn't own any option 
				// and the function vapGetSelectedOptions() hasn't been declared.
				// Define an empty options array to avoid breaking the flow.
				var options = [];
			}
			
			var opt_ids 	= '';
			var opt_quant 	= '';
			var opt_vars 	= '';

			for (var i = 0; i < options.length; i++) {
				if (opt_ids.length > 0) {
					opt_ids 	+= ',';
					opt_quant 	+= ',';
					opt_vars 	+= ',';
				}

				opt_ids 	+= options[i]['id'];
				opt_quant 	+= options[i]['quant'];
				opt_vars 	+= options[i]['variation'];
			}

			// It doesn't matter if the checkout select exists
			// as the controller won't use this value (because the
			// checkout selection is disabled for this service).
			var factor = jQuery('#vap-checkout-sel').val();
				
			var recurrence = vapGetSelectedRecurrence();
			
			var _url = '<?php echo JRoute::_('index.php?option=com_vikappointments&task=add_item_cart_rq&tmpl=component' . ($itemid ? '&Itemid=' . $itemid : ''), false); ?>';
			if (recurrence) {
				_url = '<?php echo JRoute::_('index.php?option=com_vikappointments&task=add_recur_item_cart_rq&tmpl=component' . ($itemid ? '&Itemid=' . $itemid : ''), false); ?>';
			} else {
				// set default "no" recurrence
				recurrence = [-1, -1, -1].join(',');
			}
			
			jQuery('.option-required').removeClass('vapoptred');
			
			jQuery.noConflict();
			
			var jqxhr = jQuery.ajax({
				type: "POST",
				url: _url,
				data: {
					id_ser: id_ser,
					id_emp: id_emp,
					ts: ts,
					ts_hour: ts_hour,
					ts_min: ts_min,
					people: people,
					opt_ids: opt_ids,
					opt_quant: opt_quant,
					opt_vars: opt_vars,
					recurrence: recurrence,
					duration_factor: factor
				}
			}).done(function(resp) {
				var obj = jQuery.parseJSON(resp); 
				
				if (obj[0] == 1) { // single item
					
					// display message for one service only
					vapDisplayRightMessage(1);
					
					if (vapIsCartPublished()) {
						// update cart too if published
						vapModAddCartItem(obj);
					}
					
					// we can proceed with the checkout
					vapCheckoutProceed = 1;
					
				} else if (obj[0] == 2) { // recurring items
					
					// ok count
					if (obj[1] > 0) {
						// recurring appointments -> get number of items added
						vapDisplayRightMessage(obj[1]);
					}
					
					var err_found  = 0;
					var wrong_html = '';

					for (var i = 0; i < obj[2].length; i++) {
						if (obj[2][i][0] != 1) {
							err_found++;
							wrong_html += obj[2][i][1] + '<br />';
						}
					}
					
					if (err_found != 0) {
						// at least an error found, display a wrong message too
						vapDisplayWrongMessage(Math.max(err_found * 1500, 2500), wrong_html);
					}
					
					if (vapIsCartPublished()) {
						// update cart too if published
						vapModAddRecurringCartItems(obj);
					}
					
					// we can proceed with the checkout
					vapCheckoutProceed = 1;

				} else {
					// display an error message
					vapDisplayWrongMessage(2500, obj[1]);
					
					if (obj.length > 2) {
						if (obj[2] == -5) {
							// missing options
							jQuery.each(obj[3], function(k,v) {
								jQuery('#vapreqopt' + v).addClass('vapoptred');
							});
						}
					}
				}
				
			});
			
		} else {
			vapDisplayWrongMessage(2500, Joomla.JText._('VAPBOOKNOTIMESELECTED'));
		}
	}

	function vapDisplayRightMessage(init_count) {
		if (!jQuery('.booksuccessdiv').is(':visible')) {
			_items_add_count = init_count;
		} else {
			_items_add_count++;
		}
		
		if (_items_add_count == 1) {
			jQuery('.booksuccessdiv #booksuccess-msg').text(Joomla.JText._('VAPCARTITEMADDOK'));
		} else {
			jQuery('.booksuccessdiv #booksuccess-msg').text(_items_add_count + ' ' + Joomla.JText._('VAPCARTMULTIITEMSADDOK'));
		}
		
		if (_items_timeout != null) {
			clearTimeout(_items_timeout);
		}
		
		jQuery('.booksuccessdiv').stop(true, true).fadeIn();
        UIkit.notification({message: '<?php echo JText::sprintf('VAPCARTITEMADDOK'); ?>', status: 'success', pos: 'bottom-left'});

		_items_timeout = setTimeout(function() {
			jQuery('.booksuccessdiv').fadeOut();
		}, 2500);
	}

	function vapDisplayWrongMessage(ms, html) {
		if (_items_bad_timeout != null) {
			clearTimeout(_items_bad_timeout);
		}

		if (html) {
			jQuery('.bookerrordiv #bookerror-msg').html(html);
		}
		
		jQuery('.bookerrordiv').stop(true, true).fadeIn();

		_items_bad_timeout = setTimeout(function() {
			jQuery('.bookerrordiv').fadeOut();
		}, ms);
	}

	function vapRecurrenceSelectChanged() {
		var val = jQuery('#vaprepeatbyrecsel').val();
		
		if (val > 0) { 

			if (jQuery('#vapfornextrecsel option[value="' + val + '"]').length > 0) {

				// update select to have the same interval
				jQuery('#vapfornextrecsel').select2('val', val);

			} else if (jQuery('#vapfornextrecsel').val() == "0") {
				// option not found, select the first index available
				jQuery('#vapfornextrecsel').prop('selectedIndex', 1);

				// update val on select2
				jQuery('#vapfornextrecsel').select2('val', jQuery('#vapfornextrecsel').val());
			}

		} else {

			jQuery('#vaprecokcheck').prop('checked', false);
			jQuery('.vaprecurrencenextdiv').hide();
			jQuery('.vaprecurrenceprediv').fadeIn();

		}
	}

	function vapRecurrenceConfirmValueChanged() {
		// change index
		jQuery('#vaprepeatbyrecsel').prop('selectedIndex', 1);

		// update val on select2
		jQuery('#vaprepeatbyrecsel').select2('val', jQuery('#vaprepeatbyrecsel').val());

		// trigger change to update [fornext] select
		vapRecurrenceSelectChanged();

		jQuery('.vaprecurrenceprediv').hide();
		jQuery('.vaprecurrencenextdiv').fadeIn();
	}

	function vapGetSelectedRecurrence() {
		var enabled = <?php echo (int) $recurrence_enabled; ?>;

		if (!enabled) {
			return false;
		}

		var recurrence = [];

		recurrence.push(parseInt(jQuery('#vaprepeatbyrecsel').val()));
		recurrence.push(parseInt(jQuery('#vapfornextrecsel').val()));
		recurrence.push(parseInt(jQuery('#vapamountrecsel').val()));

		return recurrence.join(',');
	}

</script>
