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

$e 				 = $this->displayData['employee'];
$real_rating 	 = $this->displayData['rating'];
$rev_sub_title 	 = $this->displayData['review_sub'];
$reviews_enabled = $this->displayData['revsEnabled'];
$base_coord 	 = $this->displayData['baseCoord'];

$url = 'index.php?option=com_vikappointments&view=employeesearch&id_employee=' . $e['id'];

if (!empty($this->requestFilters['service']))
{
	$url .= '&id_service=' . $this->requestFilters['service'];

	$id_service = $this->requestFilters['service'];
}
else
{
	$id_service = 0;
}

if ($this->itemid)
{
	$url .= '&Itemid=' . $this->itemid;
}

$url = JRoute::_($url);

?>

<div class="vapempblock-search" id="vapempblock<?php echo $e['id']; ?>" data-employee="<?php echo $e['id']; ?>" data-service="<?php echo $id_service; ?>" data-day="">

	<!-- DETAILS -->

	<div class="emp-search-box-left">

		<!-- PROFILE -->

		<div class="emp-profile-box">

			<?php if (strlen($e['image']) > 0 && file_exists(VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $e['image'])) { ?>

				<!-- EMPLOYEE IMAGE -->

				<div class="emp-logo-image">
					<?php
					if ($this->linkHref == 2)
					{
						// by clicking the image, the system should open a popup containing the original image
						?>
						<a href="javascript: void(0);" class="vapmodal" onClick="vapOpenModalImage('<?php echo VAPMEDIA_URI . $e['image']; ?>');">
							<img src="<?php echo VAPMEDIA_SMALL_URI . $e['image']; ?>" alt="<?php echo $e['nickname']; ?>" />
						</a>
						<?php
					}
					else
					{
						// by clicking the image, the users are redirected to the details of the employee
						?>
						<a href="<?php echo $url; ?>">
							<img src="<?php echo VAPMEDIA_SMALL_URI . $e['image']; ?>" alt="<?php echo $e['nickname']; ?>" />
						</a>
						<?php
					}
					?>
				</div>

			<?php } ?>

			<!-- EMPLOYEE HEAD -->

			<div class="emp-title-box">

				<!-- EMPLOYEE NAME -->

				<div class="emp-name-box">
					<a href="<?php echo $url; ?>"><?php echo $e['nickname']; ?></a>
				</div>

				<?php if (!empty($e['group_name'])) { ?>

					<!-- EMPLOYEE GROUP NAME -->

					<div class="emp-group-box">
						<?php echo $e['group_name']; ?>
					</div>

				<?php } ?>

			</div>

		</div>

		<?php if ($reviews_enabled) { ?>

			<!-- REVIEWS -->

			<div class="emp-reviews-box">

				<!-- RATING -->

				<div class="emp-stars-box">
					<?php
					if ($real_rating > 0)
					{
						for ($i = 1; $i <= $real_rating; $i++)
						{
							// keep displaying a filled star
							?>
							<img src="<?php echo VAPASSETS_URI . 'css/images/rating-star.png'; ?>" class="emp-rating-star" />
							<?php
						}
						if (round($real_rating) != $real_rating)
						{
							// we got an half rating, display a middle star
							?>
							<img src="<?php echo VAPASSETS_URI . 'css/images/rating-star-middle.png'; ?>" class="emp-rating-star" />
							<?php
						}
						for ($i = round($real_rating); $i < 5; $i++)
						{
							// if haven't reached the limit (5 stars), keep displaying an empty star
							?>
							<img src="<?php echo VAPASSETS_URI . 'css/images/rating-star-no.png'; ?>" class="emp-rating-star" />
							<?php
						}
					}
					?>
				</div>

				<?php if (!empty($rev_sub_title)) { ?>

					<!-- REVIEWS SUBTITLE -->

					<div class="emp-rating-subtitle">
						<?php echo $rev_sub_title; ?>
					</div>

				<?php } ?>
			</div>

		<?php } ?>

		<?php if (count($e['locations_list'])) { ?>

			<!-- LOCATIONS -->

			<div class="emp-locations-box">
				
				<?php
				foreach ($e['locations_list'] as $loc)
				{
					$loc_str = $loc['city_name'];
					
					if (empty($loc_str))
					{
						$loc_str = $loc['state_name'];

						if (empty($loc_str))
						{
							$loc_str = $loc['country_name'];
						}
					}

					if (!empty($loc['address']))
					{
						$loc_str .= ", " . $loc['address'];
					}

					if (!empty($loc['zip']))
					{
						$loc_str .= " " . $loc['zip'];
					}

					?>

					<div class="emp-location-row">
						<div class="address"><?php echo $loc_str; ?></div>
						
						<?php
						if ($base_coord !== null && strlen($loc['latitude']) && strlen($loc['longitude']))
						{ 
							$distance = VikAppointments::getGeodeticaDistance(
								$loc['latitude'],
								$loc['longitude'],
								$base_coord['latitude'],
								$base_coord['longitude']
							);

							?>
							<div class="distance"><?php echo JText::sprintf("VAPDISTANCEFROMYOU", VikAppointments::formatDistance($distance)); ?></div>
							<?php
						}
						?>
					</div>

				<?php } ?>

			</div>

		<?php } ?>

		<?php if (!empty($e['rate'])) { ?>

			<!-- QUICK CONTACT -->

			<div class="emp-rate-box">
				<strong><?php echo VikAppointments::printPriceCurrencySymb($e['rate']); ?></strong>
			</div>

		<?php } ?>

		<!-- DETAILS BUTTON -->

		<div class="emp-viewdetails-box">
			<a href="<?php echo $url; ?>" class="vap-btn blue">
				<?php echo JText::_('VAPVIEWDETAILS'); ?>
			</a>
		</div>

		<?php if ($e['quick_contact']) { ?>

			<!-- QUICK CONTACT -->

			<div class="emp-quickcontact-box">
				<a class="vap-btn blue" href="javascript: void(0);" onClick="vapGoToMail('#vapempblock<?php echo $e['id']; ?>', <?php echo $e['id']; ?>, '<?php echo addslashes(JText::sprintf('VAPEMPTALKINGTO', $e['nickname'])); ?>');">
					<?php echo JText::_('VAPEMPQUICKCONTACT'); ?>
				</a>
			</div>

		<?php } ?>

		<?php if ($e['showphone']) { ?>

			<!-- PHONE NUMBER -->

			<div class="emp-phone-box">
				<span>
					<?php
					/**
					 * The phone number is now clickable to start a call on mobile devices.
					 *
					 * @since 1.6.2
					 */
					?>
					<a href="tel:<?php echo $e['phone']; ?>">
						<i class="fa fa-phone"></i>
						<?php echo $e['phone']; ?>
					</a>
				</span>
			</div>

		<?php } ?>

	</div>

	<!-- AVAILABILITY -->

	<div class="emp-search-box-right">

		<div class="emp-search-loading">
			<img src="<?php echo VAPASSETS_URI; ?>css/images/loading.gif" />
		</div>

	</div>

</div>

<script>

	jQuery(document).ready(function() {

		/**
		 * This function is declared by this view:
		 * views/employeeslist/view.html.php
		 * 
		 * @see 	addJS()
		 */
		loadEmployeeAvailTable(<?php echo $e['id']; ?>);

	});

</script>
