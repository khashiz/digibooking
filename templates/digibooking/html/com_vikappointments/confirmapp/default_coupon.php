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

if ($this->anyCoupon == 1 && $this->cart->getTotalCost() > 0) { ?>

	<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=confirmapp&Itemid=' . $this->itemid); ?>" name="couponform" method="post">
		<div class="vapcouponcodediv">
			<h3 class="vapheading3"><?php echo JText::_('VAPENTERYOURCOUPON'); ?></h3>
			<input class="vapcouponcodetext" type="text" name="couponkey" />
			<button type="submit" class="vap-btn blue"><?php echo JText::_('VAPAPPLYCOUPON'); ?></button>
		</div>
		
		<input type="hidden" name="option" value="com_vikappointments"/>
		<input type="hidden" name="view" value="confirmapp"/>

		<?php
		// use token to prevent brute force attacks
		echo JHtml::_('form.token');
		?>
	</form>

<?php } ?>
