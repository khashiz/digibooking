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

$s 				 = $this->displayData['service'];
$real_rating 	 = $this->displayData['rating'];
$rev_sub_title 	 = $this->displayData['review_sub'];
$reviews_enabled = $this->displayData['revsEnabled'];

$vik = UIApplication::getInstance();

?>

<div class="vapserblock <?php echo $vik->getThemeClass('background'); ?>" id="vapserblock<?php echo $s['id']; ?>">
							
	<div class="vapserwrapper">
		
		<div class="vapserimage" id="vapimage<?php echo $s['id']; ?>">
			<?php
			if (!empty($s['image']))
			{
				if ($this->linkHref == 2)
				{
					// by clicking the image, the system should open a popup containing the original image
					?>
					<a href="javascript: void(0);" class="vapmodal" onClick="vapOpenModalImage('<?php echo VAPMEDIA_URI . $s['image']; ?>');">
						<img src="<?php echo VAPMEDIA_URI . $s['image']; ?>" alt="<?php echo $s['name']; ?>" />
					</a>
					<?php
				}
				else
				{
					// by clicking the image, the users are redirected to the details of the service
					?>
					<a href="<?php echo JRoute::_("index.php?option=com_vikappointments&view=servicesearch&id_ser={$s['id']}" . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>">
						<img src="<?php echo VAPMEDIA_URI . $s['image']; ?>" alt="<?php echo $s['name']; ?>" />
					</a>
					<?php
				}
			}
			?>
		</div>
		
		<div class="vapsername">
			<a href="<?php echo JRoute::_("index.php?option=com_vikappointments&view=servicesearch&id_ser={$s['id']}" . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>">
				<?php echo $s['name']; ?>
			</a>
		</div>
		
		<?php
		if ($reviews_enabled)
		{
			?>

			<div class="vapserbottomreview">
				<div class="reviewleft">
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

				<div class="reviewright">
					<?php echo $rev_sub_title; ?>
				</div>
			</div>

		<?php } ?>
		
	</div>
	
	<?php
	if ($this->linkHref == 3 || empty($s['image']))
	{
		// If the image is empty or the service description should be shown,
		// put a box containing the description of the service.
		?>

		<div class="vapserdescwrap <?php echo (empty($s['image']) ? 'always' : '' ); ?>" id="vapdesc<?php echo $s['id']; ?>">
			<div class="vapserdesc">
				<?php  
				$desc = VikAppointments::renderHtmlDescription($s['description'], 'serviceslist');

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
		</div>

	<?php } ?>
		
</div>
