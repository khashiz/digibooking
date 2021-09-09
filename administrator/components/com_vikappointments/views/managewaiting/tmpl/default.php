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

// load calendar behavior
JHtml::_('behavior.calendar');

$sel = $this->waiting;

$vik = UIApplication::getInstance();

$date_format = UIFactory::getConfig()->get('dateformat');

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<div class="span5">
		<?php echo $vik->openEmptyFieldset(); ?>
			
			<!-- SERVICE - Dropdown -->
			<?php
			$options = array();
			foreach ($this->services as $s)
			{
				$options[] = JHtml::_('select.option', $s['id'], $s['name']);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGEWAITLIST1').'*:'); ?>
				<select name="id_service" id="vap-services-select">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $sel['id_service']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
			<!-- EMPLOYEE - Dropdown -->
			<?php
			$options = array();
			$options[] = JHtml::_('select.option', '', '');
			foreach ($this->employees as $e)
			{
				$options[] = JHtml::_('select.option', $e['id'], $e['nickname']);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGEWAITLIST2').':'); ?>
				<select name="id_employee" id="vap-employees-select">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $sel['id_employee']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
			<!-- CHECKIN - Calendar -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEWAITLIST3').'*:'); ?>
				<?php echo $vik->calendar(date($date_format, $sel['timestamp']), 'timestamp', 'timestamp'); ?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- USER - Dropdown -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEWAITLIST4').':'); ?>
				<input type="hidden" name="id_user" id="vap-users-select" value="<?php echo $sel['id_user']; ?>" />
			<?php echo $vik->closeControl(); ?>

			<!-- EMAIL - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEWAITLIST5').'*:'); ?>
				<input class="required vap-user-child" type="text" name="email" value="<?php echo $sel['email']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>

			<!-- PHONE NUMBER - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGEWAITLIST6').':'); ?>
				<input class="vap-user-child" type="text" name="phone_number" value="<?php echo $sel['phone_number']; ?>" size="40" />
			<?php echo $vik->closeControl(); ?>
			
			
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<script>

	var BILLING_USERS_POOL = {};

	jQuery(document).ready(function() {

		jQuery('#vap-services-select').select2({
			allowClear: false,
			width: 300
		});

		jQuery('#vap-employees-select').select2({
			placeholder: '<?php echo addslashes(JText::_('VAPMANAGEEMPLOYEE20')); ?>',
			allowClear: true,
			width: 300
		});

		jQuery('#vap-services-select').on('change', function() {
			refreshEmployees(jQuery(this).val());
		});

		jQuery('#vap-users-select').select2({
			placeholder: '--',
			allowClear: true,
			width: 300,
			minimumInputLength: 2,
			ajax: {
				url: 'index.php?option=com_vikappointments&task=search_users&tmpl=component',
				dataType: 'json',
				type: "POST",
				quietMillis: 50,
				data: function(term) {
					return {
						term: term
					};
				},
				results: function(data) {
					return {
						results: jQuery.map(data, function (item) {

							if (!BILLING_USERS_POOL.hasOwnProperty(item.id))
							{
								BILLING_USERS_POOL[item.id] = item;
							}

							return {
								text: item.billing_name,
								id: item.id
							}
						})
					};
				},
			},
			initSelection: function(element, callback) {
				// The input tag has a value attribute preloaded that points to a preselected repository's id.
				// This function resolves that id attribute to an object that select2 can render
				// using its formatResult renderer - that way the repository name is shown preselected
				var id = jQuery(element).val();
				
				jQuery.ajax("index.php?option=com_vikappointments&task=search_users&tmpl=component&id="+id, {
					dataType: "json"
				}).done(function(data) {
					if (data.hasOwnProperty('id')) {
						callback(data); 
					}
				});
			},
			formatSelection: function(data) {
				if (jQuery.isEmptyObject(data.billing_name)) {
					// display data retured from ajax parsing
					return data.text;
				}
				// display pre-selected value
				return data.billing_name;
			},

			dropdownCssClass: "bigdrop",
		});

		jQuery("#vap-users-select").on('change', function() {
			
			var id = jQuery(this).val();

			if (!BILLING_USERS_POOL.hasOwnProperty(id)) {
				return false;
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('billing_mail')) {
				jQuery('input[name="email"]').val(BILLING_USERS_POOL[id].billing_mail);
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('billing_phone')) {
				jQuery('input[name="phone_number"]').val(BILLING_USERS_POOL[id].billing_phone);
			}

		});

	});

	function refreshEmployees(id_ser) {

		jQuery('#vap-employees-select').prop('disabled', true);
		
		jQuery.noConflict();
			
		var jqxhr = jQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_vikappointments&task=get_services_employees&tmpl=component',
			data: {
				id_ser: id_ser
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			var _html = '<option></option>\n';
			jQuery.each(obj, function(k, v){
				_html += '<option value="'+v.id+'">'+v.nickname+'</option>\n';
			});

			jQuery('#vap-employees-select').html(_html);

			jQuery('#vap-employees-select').prop('disabled', false);

			jQuery('#vap-employees-select').select2('val', null);
			
		}).fail(function(resp) {
			jQuery('#vap-employees-select').prop('disabled', false);
		});
	}

	// validate

	var validator = new VikFormValidator('#adminForm');

	Joomla.submitbutton = function(task) {
		if (task.indexOf('save') !== -1) {
			if (validator.validate()) {
				Joomla.submitform(task, document.adminForm);    
			}
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}

</script>
