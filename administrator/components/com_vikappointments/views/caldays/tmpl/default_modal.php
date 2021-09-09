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

echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-purchinfo',
	array(
		'title'       => JText::_('VAPMANAGERESERVATIONTITLE1'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '', // it will be filled dinamically
	)
);

?>

<script>

	jQuery(document).ready(function() {

		jQuery('div.time-starts').on('click', function() {
			var ids = jQuery(this).data('id');

			if (isNaN(ids)) {
				ids = ids.split(',');
			} else {
				ids = [ids];
			}

			url = 'index.php?option=com_vikappointments&task=purchaserinfo&tmpl=component&joomla3810t_btns=1&from=caldays';

			for (var i = 0; i < ids.length; i++) {
				url += '&oid[]=' + ids[i];
			}

			vapOpenJModal('purchinfo', url, true);
		});

	});

	function vapOpenJModal(id, url, jqmodal) {
		<?php echo $vik->bootOpenModalJS(); ?>
	}

</script>
