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

$sel = $this->review;

$vik = UIApplication::getInstance();

$languages = VikAppointments::getKnownLanguages();
ArasJoomlaVikApp::datePicker();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">

	<?php echo $vik->bootStartTabSet('review', array('active' => $this->tab)); ?>

		<!-- DETAILS -->
				
		<?php echo $vik->bootAddTab('review', 'review_details', JText::_('VAPORDERTITLE2')); ?>
	
			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					
					<!-- TITLE - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW2').'*:'); ?>
						<input type="text" name="title" class="required" value="<?php echo $sel['title']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>

					<!-- EMPLOYEE / SERVICE - Dropdown -->
					<?php
					$items = array();
					$items[0] = array(JHtml::_('select.option', '', ''));

					if (count($this->services))
					{
						$key = JText::_('VAPMENUSERVICES');
						$items[$key] = array();

						foreach ($this->services as $s)
						{
							$items[$key][] = JHtml::_('select.option', 'ser-' . $s['id'], $s['name']);
						}
					}

					if (count($this->employees))
					{
						$key = JText::_('VAPMENUEMPLOYEES');
						$items[$key] = array();

						foreach ($this->employees as $e)
						{
							$items[$key][] = JHtml::_('select.option', 'emp-' . $e['id'], $e['lastname'] . ' ' . $e['firstname']);
						}
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW6').'*:'); ?>
						<?php
						$params = array(
							'id' 			=> 'vap-seremp-select',
							'list.attr'		=> array('class' => 'required'),
							'group.items' 	=> null,
							'list.select'	=> $sel['id_service'] > 0 ? 'ser-' . $sel['id_service'] : 'emp-' . $sel['id_employee'],
						);
						echo JHtml::_('select.groupedList', $items, 'id_seremp', $params);
						?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- DATE - Date -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW4').':'); ?>
						<?php echo $vik->calendar($sel['timestamp'], 'timestamp', 'timestamp'); ?>
						<input type="hidden" name="hour" value="<?php echo ArasJoomlaVikApp::jdate('H', $sel['timestamp']); ?>" />
						<input type="hidden" name="min" value="<?php echo ArasJoomlaVikApp::jdate('i', $sel['timestamp']); ?>" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- RATING - Rating -->
					<?php 
					$elements = array();
					for ($i = 5; $i > 0; $i--)
					{
						$elements[] = JHtml::_('select.option', $i, $i . ' ' . JText::_('VAPSTAR' . ($i > 1 ? 'S' : '')));
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW5').'*:'); ?>
						<select name="rating" id="vap-rating-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['rating']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- LANGUAGE - Dropdown -->
					<?php
					$elements = array();
					foreach ($languages as $lang)
					{
						$elements[] = JHtml::_('select.option', $lang, $lang);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW8').':'); ?>
						<select name="langtag" id="vap-langtag-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['langtag']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>
					
					<!-- PUBLISHED - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW7').':'); ?>
						<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- COMMENT - Textarea -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW9').':'); ?>
						<textarea name="comment" style="width:100%;height:180px;"><?php echo $sel['comment']; ?></textarea>
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- USER -->

		<?php echo $vik->bootAddTab('review', 'review_user', JText::_('VAPMANAGEREVIEW10')); ?>

			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>

					<!-- USER - Dropdown -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW10').'*:'); ?>
						<input type="hidden" name="jid" class="vap-users-select required" value="<?php echo $sel['jid']; ?>" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- USER NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW3').':'); ?>
						<input type="text" name="name" value="<?php echo $sel['name']; ?>" size="40" id="user-name" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- USER EMAIL - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEREVIEW11').':'); ?>
						<input type="text" name="email" value="<?php echo $sel['email']; ?>" size="40" id="user-mail" />
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<script>

	var BILLING_USERS_POOL = {};

	jQuery(document).ready(function() {
		
		jQuery('#vap-seremp-select').select2({
			placeholder: '--',
			allowClear: false,
			width: 300
		});

		jQuery('#vap-rating-sel, #vap-langtag-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery('.vap-users-select').select2({
			placeholder: '--',
			allowClear: false,
			width: 300,
			minimumInputLength: 2,
			ajax: {
				url: 'index.php?option=com_vikappointments&task=search_jusers&tmpl=component',
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
								text: item.username,
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
				
				jQuery.ajax("index.php?option=com_vikappointments&task=search_jusers&tmpl=component&id="+id, {
					dataType: "json"
				}).done(function(data) {
					if (data.hasOwnProperty('id')) {
						callback(data); 
					}
				});
			},
			formatSelection: function(data) {
				if (jQuery.isEmptyObject(data.username)) {
					// display data retured from ajax parsing
					return data.text;
				}
				// display pre-selected value
				return data.username;
			},

			dropdownCssClass: "bigdrop"
		});

		jQuery('.vap-users-select').on('change', function(){
			
			var id = jQuery(this).val();
			
			if (BILLING_USERS_POOL[id].hasOwnProperty('username')) {
				jQuery('#user-name').val(BILLING_USERS_POOL[id].username);
			}

			if (BILLING_USERS_POOL[id].hasOwnProperty('email')) {
				jQuery('#user-mail').val(BILLING_USERS_POOL[id].email);
			}

		});
	
	});

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#review_"]').on('click', function() {
			var href = jQuery(this).attr('href').substr(1);
			jQuery('input[name="tabname"]').val(href);
		});

	});

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
