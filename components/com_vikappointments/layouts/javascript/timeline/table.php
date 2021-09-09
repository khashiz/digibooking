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

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string  $url  The URL used to load the time table via AJAX.
 */

JText::script('VAPWAITLISTADDED0');

$loading_img = VAPASSETS_URI . 'css/images/loading.gif';

echo 
<<<JS
function loadEmployeeAvailTable(id) {

	doAjaxWithRetries(
		'$url',
		{
			id_emp: id,
			id_ser: jQuery('#vapempblock' + id).data('service'),
			day: 	jQuery('#vapempblock' + id).attr('data-day'),
		},
		function(resp) {
			var obj = jQuery.parseJSON(resp);

			jQuery('#vapempblock' + id).find('.emp-search-box-right').html(obj[2]);
		},
		function(err) {
			jQuery('#vapempblock' + id).find('.emp-search-box-right').html(
				'<div class="emp-search-error">' + Joomla.JText._('VAPWAITLISTADDED0') + '</div>'
			);
		}
	);

}

function showMoreTimesFromTable(id, btn) {
	// display hidden times
	jQuery('#avail-tbody' + id).find('.timetable-slot.hidden').removeClass('hidden').addClass('visible');
	// hide "show more" button
	jQuery(btn).closest('.avail-table-joomla3810ter').hide();
}

function loadOtherTableTimes(id, day) {
	jQuery('#vapempblock' + id).find('.emp-search-box-right').html(
		'<div class="emp-search-loading"><img src="$loading_img" /></div>'
	);

	jQuery('#vapempblock' + id).attr('data-day', day);

	loadEmployeeAvailTable(id);
}
JS
;
