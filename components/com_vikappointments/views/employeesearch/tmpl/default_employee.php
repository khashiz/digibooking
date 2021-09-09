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

?>

<div class="vapempblock" id="vapempblock<?php echo $this->employee['id']; ?>">

	<div class="vapempinfoblock">

		<?php
		if (strlen($this->employee['image']) && file_exists(VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $this->employee['image']))
		{
			?>
			<div class="vapempimgdiv">
				<a href="javascript: void(0);" class="vapmodal" onClick="vapOpenModalImage('<?php echo VAPMEDIA_URI . $this->employee['image']; ?>');">
					<img src="<?php echo VAPMEDIA_SMALL_URI . $this->employee['image']; ?>" alt="<?php echo $this->employee['nickname']; ?>" />
				</a>
			</div>
			<?php
		}
		?>

		<div class="vap-empmain-block">

			<div class="vap-empheader-div">

				<div class="vapempnamediv">
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeesearch&id_employee=' . $this->employee['id'] . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>">
						<?php echo $this->employee['nickname']; ?>
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

			</div>

			<div class="vapempdescdiv">
				<?php echo VikAppointments::renderHtmlDescription($this->employee['note'], 'employeesearch'); ?>
			</div>
		</div>
		
	</div>
	
	<?php
	if ($this->employee['showphone'] || $this->employee['quick_contact'] || ($this->selectedService && $this->selectedService['rate'] > 0))
	{
		?>
		<div class="vapempcontactdiv">
			<span class="vapempcontactsp">
				<?php

				if ($this->selectedService && $this->selectedService['rate'] > 0)
				{
					?>
					<span class="vap-price-info-box left-side">
						<i class="fa fa-money"></i>

						<span class="vap-toolbar-ratedetails" id="vapratebox">
							<?php echo VikAppointments::printPriceCurrencySymb($this->selectedService['rate'] * $this->selectedService['min_per_res']); ?>
						</span>
					</span>
					<?php
				}

				if ($this->employee['showphone'])
				{
					?>
					<span class="vap-price-info-box">
						<i class="fa fa-phone"></i>
						
						<span class="vapempphonesp">
							<?php
							/**
							 * The phone number is now clickable to start a call on mobile devices.
							 *
							 * @since 1.6.2
							 */
							?>
							<a href="tel:<?php echo $this->employee['phone']; ?>"><?php echo $this->employee['phone']; ?></a>
						</span>
					</span>
					<?php
				}
				
				if ($this->employee['quick_contact'])
				{
					?>
					<span class="vap-price-info-box">
						<i class="fa fa-envelope"></i>

						<span class="vapempquickcontsp">
							<?php //echo UIApplication::getInstance()->safeMailTag($this->employee['email']); ?>
							<a href="javascript: void(0);" onClick="vapGoToMail('.vapempblock');">
								<?php echo JText::_('VAPEMPQUICKCONTACT'); ?>
							</a>
						</span>
					</span>
					<?php
				}
				?>
			</span>
		</div>
		<?php
	}
	?>
</div>
