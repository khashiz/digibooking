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

$auth 	= isset($displayData['auth']) 	? $displayData['auth'] 	 : EmployeeAuth::getInstance();
$active = isset($displayData['active']) ? $displayData['active'] : true;
$itemid = isset($displayData['itemid'])	? $displayData['itemid'] : null;

$payments_enabled 	= $auth->managePayments();
$customf_enabled 	= $auth->manageCustomFields();
$is_subscr 			= VikAppointments::isSubscriptions();

$input = JFactory::getApplication()->input;

if (is_null($itemid))
{
	// item id not provided, get the current one (if set)
	$itemid = $input->getInt('Itemid');
}

if ($itemid)
{
	$itemid = '&Itemid=' . $itemid;
}

$active_view = $input->get('view');

?>
 
<div class="vapemplogintoolbardiv">
	
	<?php
	/**
	 * IMPORTANT.
	 * The following <div> tags must be stuck without spaces in order to avoid bad alignments
	 * while hovering with the mouse above the links.
	 *
	 * So, we must have this:
	 * </div><div class
	 *
	 * Instead than:
	 * </div>
	 * <div class
	 */
	?>
	<div class="vapemploginactionlink<?php echo !$active ? 'disabled' : ''; ?> vapemploginactionprofile <?php echo $active_view == 'empeditprofile' ? 'item-active' : ''; ?>">
		<a href="<?php echo $active ? JRoute::_('index.php?option=com_vikappointments&view=empeditprofile' . $itemid) : 'javascript:void(0);'; ?>">
			<i class="fa fa-user"></i> <?php echo JText::_('VAPEMPPROFILETITLE'); ?>
		</a>
	</div><div class="vapemploginactionlink<?php echo !$active ? 'disabled' : ''; ?> vapemploginactionwdays <?php echo $active_view == 'empwdays' ? 'item-active' : ''; ?>">
		<a href="<?php echo $active ? JRoute::_('index.php?option=com_vikappointments&view=empwdays' . $itemid) : 'javascript:void(0);'; ?>">
			<i class="fa fa-calendar"></i> <?php echo JText::_('VAPEMPWORKDAYSTITLE'); ?>
		</a>
	</div><div class="vapemploginactionlink<?php echo !$active ? 'disabled' : ''; ?> vapemploginactionservices <?php echo $active_view == 'empserviceslist' ? 'item-active' : ''; ?>">
		<a href="<?php echo $active ? JRoute::_('index.php?option=com_vikappointments&view=empserviceslist' . $itemid) : 'javascript:void(0);'; ?>">
			<i class="fa fa-list"></i> <?php echo JText::_('VAPEMPSERVICESTITLE'); ?>
		</a>
	</div><?php
	if ($payments_enabled)
	{
		?><div class="vapemploginactionlink<?php echo !$active ? 'disabled' : ''; ?> vapemploginactionpayments <?php echo $active_view == 'emppaylist' ? 'item-active' : ''; ?>">
			<a href="<?php echo $active ? JRoute::_('index.php?option=com_vikappointments&view=emppaylist' . $itemid) : 'javascript:void(0);'; ?>">
				<i class="fa fa-credit-card"></i> <?php echo JText::_('VAPEMPPAYMENTSTITLE'); ?>
			</a>
		</div>
		<?php
	}
	?>
	
    <div class="vap-emplogin-rcont">

		<div class="vap-emplogin-rbox">
			<div class="vap-emplogin-rphoto">
				<?php
				if ($auth->image)
				{
					?>
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin' . $itemid); ?>">
						<img src="<?php echo VAPMEDIA_SMALL_URI . $auth->image; ?>" />
					</a>
					<?php
				}
				?>
			</div>
			<div class="vap-emplogin-rtitle">
				<a href="javascript: void(0);"><?php echo $auth->nickname; ?></a>
			</div>
		</div>
		
		<div class="vap-emplogin-modal" style="display: none;">
			<ul>
				<li class="separator <?php echo $active_view == 'empaccountstat' ? 'item-active' : ''; ?>">
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empaccountstat' . $itemid); ?>">
						<?php echo JText::_('VAPEMPACCOUNTSTATUSTITLE'); ?>
					</a>
				</li>
				
				<li class="<?php echo $active_view == 'empcoupons' ? 'item-active' : ''; ?>">
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empcoupons' . $itemid); ?>">
						<?php echo JText::_('VAPEMPCOUPONSTITLE'); ?>
					</a>
				</li>
				
				<li class="<?php echo $active_view == 'emplocations' ? 'item-active' : ''; ?>">
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplocations' . $itemid); ?>">
						<?php echo JText::_('VAPEMPLOCATIONSTITLE'); ?>
					</a>
				</li>
				
				<li class="separator <?php echo $active_view == 'emplocwdays' ? 'item-active' : ''; ?>">
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplocwdays' . $itemid); ?>">
						<?php echo JText::_('VAPEMPLOCWDTITLE'); ?>
					</a>
				</li>

				<?php
				if ($is_subscr)
				{
					?>
					<li class="<?php echo $active_view == 'empsubscrorder' ? 'item-active' : ''; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empsubscrorder' . $itemid); ?>">
							<?php echo JText::_('VAPEMPSUBSCRPURCHTITLE'); ?>
						</a>
					</li>
					
					<li class="separator <?php echo $active_view == 'empsubscr' ? 'item-active' : ''; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empsubscr' . $itemid); ?>">
							<?php echo JText::_('VAPEMPSUBSCRTITLE'); ?>
						</a>
					</li>
					<?php
				}
				?>

				<?php
				if ($customf_enabled)
				{
					?>
					<li class="<?php echo $active_view == 'empcustfields' ? 'item-active' : ''; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empcustfields' . $itemid); ?>">
							<?php echo JText::_('VAPEMPCUSTOMFTITLE'); ?>
						</a>
					</li>
					<?php
				}
				?>

				<li class="separator <?php echo $active_view == 'empsettings' ? 'item-active' : ''; ?>">
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empsettings' . $itemid); ?>">
						<?php echo JText::_('VAPEMPSETTINGSTITLE'); ?>
					</a>
				</li>
				
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=emplogin.logout' . $itemid); ?>">
						<?php echo JText::_('VAPLOGOUTTITLE'); ?>
					</a>
				</li>
			</ul>
		</div>

	</div>
	
</div>

<script>

	jQuery(document).ready(function() {
		jQuery('html').click(function() {
			jQuery('.vap-emplogin-modal').hide();
		});

		jQuery('.vap-emplogin-rtitle').click(function(event) {
			event.stopPropagation();
			jQuery('.vap-emplogin-modal').toggle();
		});
	});

</script>
