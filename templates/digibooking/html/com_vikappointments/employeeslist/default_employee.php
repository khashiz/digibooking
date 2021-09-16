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
}

if ($this->itemid)
{
	$url .= '&Itemid=' . $this->itemid;
}

$url = JRoute::_($url);

$vik = UIApplication::getInstance();

?>

<div class="vapempblock <?php echo $vik->getThemeClass('background'); ?>" id="vapempblock<?php echo $e['id']; ?>">

	<div class="vapempinfoblock">

		<?php if (strlen($e['image']) > 0 && file_exists(VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $e['image'])) { ?>

			<!-- display image -->

			<div class="vapempimgdiv">
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

		<div class="vap-empinfo">

			<!-- block with name and reviews -->

			<div class="vap-empheader-div">

				<div class="vapempnamediv">
					<a href="<?php echo $url; ?>">
						<?php echo $e['nickname']; ?>
					</a>
				</div>

				<?php if (!empty($e['group_name'])) { ?>

					<!-- employee group name -->

					<div class="vap-empgroup-namediv">
						<?php echo $e['group_name']; ?>
					</div>

				<?php } ?>

				<?php if ($reviews_enabled) { ?>

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

				<?php } ?>

				<?php if ($reviews_enabled && !empty($rev_sub_title)) { ?>

					<!-- review subtitle -->

					<div class="vap-empsubreview-div">
						<?php echo $rev_sub_title; ?>
					</div>

				<?php } ?>

			</div>

		</div>
	   
		<div class="vapempdescdiv">

			<!-- employee description -->

			<?php 
			$desc = VikAppointments::renderHtmlDescription($e['note'], 'employeeslist');

			if (strlen(strip_tags($desc)) > $this->descLength)
			{
				// The length of the description exceeded the maximum amount.
				// We need to display a substring of the description by stripping all the HTML tags
				// to avoid breaking the whole code.
				$desc = mb_substr(strip_tags($desc), 0, $this->descLength, 'UTF-8') . '...';
			} 
			
			echo $desc; 
			?>
		</div>
		
		<?php if (count($e['locations_list']) > 0) { ?>

			<!-- employee locations list -->

			<div class="vap-emp-avloc-block">
				
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

					<div class="vap-emp-avlocation-item">
						<span class="address"><i class="fa fa-map-marker"></i> <?php echo $loc_str; ?></span>
						
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
							<span class="distance"><?php echo JText::sprintf("VAPDISTANCEFROMYOU", VikAppointments::formatDistance($distance)); ?></span>
							<?php
						}
						?>
					</div>

				<?php } ?>

			</div>

		<?php } ?>
		
	</div>
	
	<?php if ($e['showphone'] || $e['quick_contact'] || !empty($e['rate'])) { ?>

		<!-- contact information and rate -->
		
		<div class="vapempcontactdiv">

			<span class="vapempcontactsp">

				<?php if (!empty($e['rate'])) { ?>

					<span class="vap-price-info-box left-side">
						<i class="fa fa-money"></i>

						<span class="vapempratesp">
							<?php echo VikAppointments::printPriceCurrencySymb($e['rate']); ?>
						</span>
					</span>

				<?php } ?>

				<?php if ($e['showphone']) { ?>

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
							<a href="tel:<?php echo $e['phone']; ?>"><?php echo $e['phone']; ?></a>
						</span>
					</span>

				<?php } ?>

				<?php if ($e['quick_contact']) { ?>

					<span class="vap-price-info-box">
						<i class="fa fa-envelope"></i>

						<span class="vapempquickcontsp">
							<a href="javascript: void(0);" onClick="vapGoToMail('#vapempblock<?php echo $e['id']; ?>', <?php echo $e['id']; ?>, '<?php echo addslashes(JText::sprintf('VAPEMPTALKINGTO', $e['nickname'])); ?>');">
								<?php echo JText::_('VAPEMPQUICKCONTACT'); ?>
							</a>
						</span>
					</span>

				<?php } ?>

			</span>

		</div>

	<?php } ?>

</div>
