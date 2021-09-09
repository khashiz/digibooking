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

?>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=order' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" name="orderform" id="orderform" method="get">

	<div class="vaporderpagediv">
		<div class="vapordertitlediv"><?php echo JText::_('VAPORDERTITLE1'); ?></div>
		
		<div class="vapordercomponentsdiv">
			<div class="vaporderinputdiv">
				<label class="vaporderlabel" for="vapordnum"><?php echo JText::_('VAPORDERNUMBER'); ?>:</label>
				<input class="" type="text" id="vapordnum" name="ordnum" size="32" />
			</div>
			
			<div class="vaporderinputdiv">
				<label class="vaporderlabel" for="vapordkey"><?php echo JText::_('VAPORDERKEY'); ?>:</label>
				<input class="" type="text" id="vapordkey" name="ordkey" size="32" />
			</div>
			
			<div class="vaporderinputdiv">
				<button type="submit" class="vap-btn blue"><?php echo JText::_('VAPORDERSUBMITBUTTON'); ?></button>
			</div>
		</div>
	</div>
	
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="view" value="order" />
</form>
