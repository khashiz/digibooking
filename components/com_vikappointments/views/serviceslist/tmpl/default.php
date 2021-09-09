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

$reviews_enabled = VikAppointments::isServicesReviewsEnabled();
$no_review_label = JText::_('VAPNOREVIEWSSUBTITLE');

$vik = UIApplication::getInstance();

?>	
		
<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=servicesearch' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post" name="servicesform">
	
	<div class="vapserallblocks">
	
		<?php
		foreach ($this->groups as $id_group => $group)
		{
			/**
			 * @todo retrieve translation within view.html.php
			 */
			$gname = VikAppointments::getTranslation($id_group, $group, $this->langGroups, 'name', 'name');
			$gdesc = VikAppointments::getTranslation($id_group, $group, $this->langGroups, 'description', 'description');
			?>

			<div class="vapsergroup <?php echo $vik->getThemeClass('background'); ?>">

				<?php
				// Register the group details within a property of this class 
				// to make it available also for the sublayout.
				$this->displayData = array(
					'id'			=> $id_group,
					'name' 			=> $gname,
					'description' 	=> $gdesc,
				);

				// get group template
				echo $this->loadTemplate('group');
				?>

				<div class="vapservicescont">

					<?php
					foreach ($group['services'] as $s)
					{
						/**
						 * @todo retrieve translation within view.html.php
						 */
						$s['name'] 		  = VikAppointments::getTranslation($s['id'], $s, $this->langServices, 'name', 'name');
						$s['description'] = VikAppointments::getTranslation($s['id'], $s, $this->langServices, 'description', 'description');
						
						$real_rating = VikAppointments::roundHalfClosest($s['rating_avg']);
					
						// review subtitle
						if ($s['reviews_count'])
						{
							$rev_sub_title = JText::sprintf('VAPREVIEWSSUBTITLE1', $s['reviews_count']);
						}
						else
						{
							$rev_sub_title = $no_review_label;
						}	

						// Register the group details within a property of this class 
						// to make it available also for the sublayout.
						$this->displayData = array(
							'service'		=> $s,
							'rating' 		=> $real_rating,
							'review_sub' 	=> $rev_sub_title,
							'revsEnabled' 	=> $reviews_enabled,
						);			
						
						// get service template
						echo $this->loadTemplate('service');
					}
					?>

				</div>

			</div>
				
		<?php } ?>
	
	</div>
		
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="view" value="servicesearch" />	
</form>
