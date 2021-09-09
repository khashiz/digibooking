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

$closure = $this->rows;

$config = UIFactory::getConfig();

?>

<div class="puchinfo-closure">

	<h3><?php echo JText::_('VAPSTATUSCLOSURE') . ' #' . $closure->id; ?></h3>

	<p>
		<?php
		/**
		 * e.g. "The employee John Smith is closed on YYYY-MM-DD from HH:mm to HH:mm
		 */
		echo JText::sprintf(
			'VAPCLOSUREINFOMESSAGE',
			$closure->nickname,
			date($config->get('dateformat'), $closure->checkin_ts),
			date($config->get('timeformat'), $closure->checkin_ts),
			date($config->get('timeformat'), VikAppointments::getCheckout($closure->checkin_ts, $closure->duration))
		);
		?>
	</p>

</div>
