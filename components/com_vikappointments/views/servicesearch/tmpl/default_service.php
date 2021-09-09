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

$reviews_enabled 	= $this->displayData['reviewsEnabled'];
$real_rating 	 	= $this->displayData['reviewRating'];
$rev_sub_title 	 	= $this->displayData['review_sub'];

$format_duration = VikAppointments::isDurationToFormat();

$vik = UIApplication::getInstance();

?>

<div class="vapempblock <?php echo $vik->getThemeClass('background'); ?>" id="vapempblock<?php echo $this->service['id']; ?>">

	<div class="vapempinfoblock">

		<?php
		if (strlen($this->service['image']) && file_exists(VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $this->service['image']))
		{
			?>
			<div class="vapempimgdiv">
				<a href="javascript: void(0);" class="vapmodal" onClick="vapOpenModalImage('<?php echo VAPMEDIA_URI . $this->service['image']; ?>');">
					<img src="<?php echo VAPMEDIA_SMALL_URI . $this->service['image']; ?>" alt="<?php echo $this->service['name']; ?>" />
				</a>
			</div>
			<?php
		}
		?>

		<div class="vap-empmain-block">

			<div class="vap-empheader-div">

				<div class="vapempnamediv">
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=servicesearch&id_ser=' . $this->service['id'] . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>">
						<?php echo $this->service['name']; ?>
					</a>
				</div>

				<?php
				if ($reviews_enabled)
				{
					?>
					<div class="vapempratingdiv">
						<?php
						if ($real_rating > 0)
						{
							for ($i = 1; $i <= $real_rating; $i++)
							{
								// keep displaying a filled star
								?>
								<img src="<?php echo VAPASSETS_URI . 'css/images/rating-star.png'; ?>" class="vap-rating-star" />
								<?php
							}
							if (round($real_rating) != $real_rating)
							{
								// we got an half rating, display a middle star
								?>
								<img src="<?php echo VAPASSETS_URI . 'css/images/rating-star-middle.png'; ?>" class="vap-rating-star" />
								<?php
							}
							for ($i = round($real_rating); $i < 5; $i++)
							{
								// if haven't reached the limit (5 stars), keep displaying an empty star
								?>
								<img src="<?php echo VAPASSETS_URI . 'css/images/rating-star-no.png'; ?>" class="vap-rating-star" />
								<?php
							}
						}
						?>
					</div>
					<?php
				}
				?>

			</div>
			
			<?php
			if ($reviews_enabled && !empty($rev_sub_title))
			{
				?>
				<div class="vap-empsubreview-div">
					<?php echo $rev_sub_title; ?>
				</div>
				<?php
			}
			?>

			<div class="vapempdescdiv">
				<?php echo VikAppointments::renderHtmlDescription($this->service['description'], 'servicesearch'); ?>
			</div>

		</div>
		
	</div>
	
	<div class="vapempcontactdiv">
		<span class="vapempcontactsp">

			<?php
			if ($this->service['quick_contact'])
			{
				?>
				<span class="vap-price-info-box left-side">
					<i class="fa fa-envelope"></i>

					<span class="vapempquickcontsp">
						<a href="javascript: void(0);" onClick="vapGoToMail('.vapempinfoblock');">
							<?php echo JText::_('VAPSERQUICKCONTACT'); ?>
						</a>
					</span>
				</span>
				<?php
			}
			?>

			<?php

			if ($this->service['price'])
			{
				$service_cost = VAPSpecialRates::getRate($this->service['id'], $this->idEmployee, $this->lastDay, $this->service['min_per_res']);

				if ($this->service['priceperpeople'])
				{
					$service_cost *= $this->service['min_per_res'];
				}
				?>
				<span class="vap-price-info-box">
					<i class="fa fa-money"></i>

					<span class="vapempserpricesp">
						<?php echo VikAppointments::printPriceCurrencySymb($service_cost); ?>
					</span>
				</span>
				<?php
			}
			?>
			
			<span class="vap-price-info-box">
				<i class="fa fa-clock-o"></i>

				<span class="vapempsertimesp">
					<?php echo VikAppointments::formatMinutesToTime($this->service['duration'], $format_duration); ?> 
				</span>
			</span>

		</span>
	</div>

</div>

<script>

	function vapUpdateServiceRate(rate) {
		/**
		 * @todo 	Should the rate be updated
		 * 			also in case the new cost has been 
		 * 			nullified (free)?
		 */

		if (rate > 0) {
			// update only if the rate is higher than 0
			jQuery('.vapempserpricesp').html(Currency.getInstance().format(rate));
		}
	}

</script>
