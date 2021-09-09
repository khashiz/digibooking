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

<div class="vap-packconf-box">

	<h3><?php echo JText::_('VAPORDERSUMMARYHEADTITLE'); ?></h3>

	<div class="vap-packages-cart">

		<div class="vap-packages-cart-list">

			<?php foreach ($this->cart->getPackagesList() as $p) { ?>

				<div class="vap-packages-cart-row">
					<span class="cart-name"><?php echo $p->getName(); ?></span>
					<span class="cart-quantity">x<?php echo $p->getQuantity(); ?></span>
					<span class="cart-price"><?php echo VikAppointments::printPriceCurrencySymb($p->getTotalCost()); ?></span>
				</div>

			<?php } ?>

		</div>

	</div>

	<div class="vap-packages-checkout">

		<div class="shop-right">
			<div class="vap-packages-cart-tcost"><?php echo VikAppointments::printPriceCurrencySymb($this->cart->getTotalCost()); ?></div>
		</div>

		<div class="shop-left">
			<div class="vap-packages-continueshop">
				<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=packages&Itemid=' . $this->itemid); ?>" class="vap-btn">
					<?php echo JText::_('VAPPACKAGESCONTINUESHOP'); ?>
				</a>
			</div>
		</div>
		
	</div>

</div>
