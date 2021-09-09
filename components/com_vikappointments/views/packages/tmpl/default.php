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

// packages dyamic style
$packages_per_row = VikAppointments::getPackagesPerRow();
$pack_width = floor((100 / $packages_per_row) - 3);

?>
		
<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=packagesconfirm' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post" name="vappackform" id="vappackform">
	
	<div class="vap-packages-groups-container" id="vap-packages-groups-container">
	
		<?php foreach ($this->packagesGroups as $g)
		{
			$g['title']			= VikAppointments::getTranslation($g['id'], $g, $this->langPackGroups, 'title', 'title');
			$g['description']	= VikAppointments::getTranslation($g['id'], $g, $this->langPackGroups, 'description', 'description');

			?>

			<div class="vap-packages-group">

				<div class="vap-package-group-details">
					<div class="vap-package-group-details-title">
						<h3><?php echo $g['title']; ?></h3>
					</div>
					<div class="vap-package-group-details-description">
						<?php
						/**
						 * Render HTML description to interpret attached plugins.
						 * 
						 * @since 1.6.3
						 */
						echo VikAppointments::renderHtmlDescription($g['description'], 'packages');
						?>
					</div>
				</div>

				<div class="vap-package-group-list">

					<?php
					foreach ($g['packages'] as $p)
					{ 
						$p['name'] 			= VikAppointments::getTranslation($p['id'], $p, $this->langPackages, 'name', 'name');
						$p['description'] 	= VikAppointments::getTranslation($p['id'], $p, $this->langPackages, 'description', 'description');

						?>

						<div class="vap-package-block" style="width:<?php echo $pack_width; ?>%;" id="vap-package<?php echo $p['id']; ?>">

							<div class="vap-package-name">
								<?php echo $p['name']; ?>
							</div>

							<div class="vap-package-price">
								<?php echo ($p['price'] > 0 ? VikAppointments::printPriceCurrencySymb($p['price']) : JText::_('VAPFREE')); ?>
							</div>

							<div class="vap-package-numapp">
								<?php echo JText::sprintf('VAPPACKAGESNUMAPP', $p['num_app']); ?>
							</div>
							
							<?php if (strlen($p['description'])) { ?>
								<div class="vap-package-description">
									<?php
									/**
									 * Render HTML description to interpret attached plugins.
									 * 
									 * @since 1.6.3
									 */
									echo VikAppointments::renderHtmlDescription($p['description'], 'packages');
									?>
								</div>
							<?php } ?>

							<div class="vap-package-button">
								<button type="button" onclick="vapAddPackageToCart(<?php echo $p['id']; ?>);"><?php echo JText::_('VAPPACKAGEORDERNOW'); ?></button>
							</div>

						</div>

					<?php } ?>

				</div>

			</div>

		<?php } ?>
	
	</div>

	<div class="vap-packages-errorbox" id="vap-packages-errorbox" style="display: none;"></div>

	<div class="vap-packages-shop" id="vap-packages-shop" style="<?php echo ($this->cart->isEmpty() ? 'display:none;' : ''); ?>">

		<h3><?php echo JText::_('VAPORDERSUMMARYHEADTITLE'); ?></h3>

		<div class="vap-packages-cart vap-packages-cart-shop" id="vap-packages-cart">

			<div class="vap-packages-cart-list" id="vap-packages-cart-list">

				<?php foreach ($this->cart->getPackagesList() as $p) { ?>

					<div class="vap-packages-cart-row" id="vap-cart-row<?php echo $p->getID(); ?>">
						
						<div class="cart-row-left">
							<span class="cart-name"><?php echo $p->getName(); ?></span>
							<span class="cart-quantity">x<?php echo $p->getQuantity(); ?></span>
							<span class="cart-price"><?php echo VikAppointments::printPriceCurrencySymb($p->getTotalCost()); ?></span>
						</div>

						<div class="cart-row-right">
							<span class="cart-remove">
								<a href="javascript: void(0);" onClick="vapRemovePackageFromCart(<?php echo $p->getID(); ?>);"><i class="fa fa-minus-circle"></i></a>
							</span>
						</div>

					</div>

				<?php } ?>

			</div>
			<div class="vap-packages-cart-total">
				<div class="vap-packages-cart-tcost" id="vap-packages-cart-tcost"><?php echo VikAppointments::printPriceCurrencySymb($this->cart->getTotalCost()); ?></div>
			</div>

		</div>

		<div class="vap-packages-checkout">		
			<div class="shop-left">
				<div class="vap-packages-ordernow">
					<button type="submit" class="vap-btn green"><?php echo JText::_('VAPPACKAGESCHECKOUT'); ?></button>
				</div>

				<div class="vap-packages-emptyact">
					<button type="button" class="vap-btn" onClick="vapEmptyCart();"><?php echo JText::_('VAPPACKAGESEMPTYCART'); ?></button>
				</div>
			</div>

		</div>

	</div>
		
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="view" value="packagesconfirm" />	
	<input type="hidden" name="Itemid" value="<?php echo $this->itemid; ?>" />  
</form>

<script>

	var vapCheckoutProceed = <?php echo ($this->cart->isEmpty() ? 0 : 1); ?>;
	var vapFirstAnimation = true;

	function vapAddPackageToCart(id_package) {

		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: '<?php echo JRoute::_("index.php?option=com_vikappointments&task=add_package_cart_rq&tmpl=component&Itemid={$this->itemid}", false); ?>',
			data: {
				id_package: id_package
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0] == 1) {
				
				vapUpdateCart(obj);
				
				vapCheckoutProceed = 1;
				
			} else {
				vapDisplayErrorMessage(obj[1]);
			}
			
		}).fail(function(resp) {
			vapDisplayErrorMessage(resp.responseText);
		});

	}

	function vapUpdateCart(item) {

		jQuery('#vap-packages-shop').show();
		
		if (jQuery('#vap-cart-row'+item[1].id).length == 0) {
			// add

			var _html = '<div class="vap-packages-cart-row" id="vap-cart-row'+item[1].id+'">\n'+
					'<div class="cart-row-left">\n'+
						'<span class="cart-name">' + item[1].name + '</span>\n'+
						'<span class="cart-quantity">x' + item[1].quantity + '</span>\n'+
						'<span class="cart-price">' + item[2] + '</span>\n'+
					'</div>\n'+
					'<div class="cart-row-right">\n'+
						'<span class="cart-remove">\n'+
							'<a href="javascript: void(0);" onClick="vapRemovePackageFromCart(' + item[1].id + ');"><i class="fa fa-minus-circle"></i></a>\n'+
						'</span>\n'+
					'</div>\n'+
				'</div>';

			jQuery('#vap-packages-cart-list').append(_html);
		} else {
			// update

			jQuery('#vap-cart-row'+item[1].id).find('.cart-quantity').html('x'+item[1].quantity);
			jQuery('#vap-cart-row'+item[1].id).find('.cart-price').html(item[2]);
		}

		jQuery('#vap-packages-cart-tcost').html(item[3]);

		if (vapFirstAnimation) {
			jQuery('html,body').animate( {scrollTop: (jQuery('#vap-cart-row'+item[1].id).offset().top-5)}, {duration:'normal'} );
			vapFirstAnimation = false;
		}

	}

	function vapRemovePackageFromCart(id_package) {

		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: '<?php echo JRoute::_("index.php?option=com_vikappointments&task=remove_package_cart_rq&tmpl=component&Itemid={$this->itemid}", false); ?>',
			data: {
				id_package: id_package
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj.status == 1) { // single item
				
				if (obj.quantity == 0) {
					jQuery('#vap-cart-row'+obj.id).remove();
				} else {
					jQuery('#vap-cart-row'+obj.id).find('.cart-quantity').html('x'+obj.quantity);
					jQuery('#vap-cart-row'+obj.id).find('.cart-price').html(obj.pack_total_cost);
				}

				jQuery('#vap-packages-cart-tcost').html(obj.cart_total_cost);
				
				if (obj.cart_empty) {
					vapResetCartBox();
				}
				
			} else {
				vapDisplayErrorMessage(obj.errstr);
			}
			
		}).fail(function(resp) {
			vapDisplayErrorMessage(resp.responseText);
		});
	}

	function vapEmptyCart() {

		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: '<?php echo JRoute::_("index.php?option=com_vikappointments&task=empty_cart_packages_rq&tmpl=component&Itemid={$this->itemid}", false); ?>',
			data: {}
		}).done(function(resp) {
			
			vapResetCartBox();

		}).fail(function(resp) {
			
		});

	}

	function vapResetCartBox() {
		jQuery('#vap-packages-cart-list').html('');
		jQuery('#vap-packages-shop').hide();

		jQuery('html,body').animate( {scrollTop: (jQuery('#vap-packages-groups-container').offset().top-5)}, {duration:'normal'} );

		vapCheckoutProceed = false;
		vapFirstAnimation = true;
	}

	var _bad_timeout = null;

	function vapDisplayErrorMessage(errstr) {

		var elem = jQuery('#vap-packages-errorbox');

		elem.html(errstr);

		if (_bad_timeout != null) {
			clearTimeout(_bad_timeout);
		}
		
		elem.stop(true, true).fadeIn();
		_bad_timeout = setTimeout(function() { elem.fadeOut(); }, 2500 );

		jQuery('html,body').animate( {scrollTop: (elem.offset().top-5)}, {duration:'normal'} );
	}

</script>
