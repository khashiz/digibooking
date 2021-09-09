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

?>

<div class="vap-unsubscrwl-content">
	<div class="vap-unsubscrwl-summary">
		<?php
		if ($this->numRows > 0)
		{
			echo JText::_('VAPUNSUBSCRWAITLISTDONE');
		}
		else
		{
			echo JText::_('VAPUNSUBSCRWAITLISTFAIL'); 
		}
		?>
	</div>
</div>
