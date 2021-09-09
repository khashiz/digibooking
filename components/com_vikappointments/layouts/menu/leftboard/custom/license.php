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

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   boolean  $pro  True if there is an active PRO license.
 */

?>

<div class="license-box custom <?php echo $pro ? 'is-pro' : 'get-pro'; ?>">
	
	<?php
	if (!$pro)
	{
		?>
			<a href="admin.php?page=vikappointments&view=gotopro">
				<i class="fa fa-rocket"></i>
				<span><?php echo JText::_('VAPGOTOPROBTN'); ?></span>
			</a>
		<?php
	}
	else
	{
		?>
		<a href="admin.php?page=vikappointments&view=gotopro">
			<i class="fa fa-trophy"></i>
			<span><?php echo JText::_('VAPISPROBTN'); ?></span>
		</a>
		<?php
	}
	?>

</div>