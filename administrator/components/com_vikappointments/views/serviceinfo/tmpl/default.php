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

$vik = UIApplication::getInstance();

?>

<div class="span6">
	<?php echo $vik->openFieldset(JText::_('VAPMANAGESERVICE13'), 'form-horizontal'); ?>

		<?php
		if ($this->employees)
		{
			?>
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
				<?php foreach ($this->employees as $e) { ?>
					<tr>
						<td style="text-align: center;">
							<span><?php echo $e['lastname'] . ' ' . $e['firstname']; ?></span>
						</td>
					</tr>
				<?php } ?>
			</table>
			<?php
		}
		else
		{
			?>
			<p><?php echo JText::_('VAPNOEMPLOYEE'); ?></p>
			<?php
		}
		?>

	<?php echo $vik->closeFieldset(); ?>
</div>

<div class="span6">
	<?php echo $vik->openFieldset(JText::_('VAPMANAGESERVICE11'), 'form-horizontal'); ?>

		<?php
		if ($this->options)
		{
			?>
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
				<?php foreach ($this->options as $o) { ?>
					<tr>
						<td style="text-align: center;">
							<span><?php echo $o['name']; ?></span>
						</td>
					</tr>
				<?php } ?>
			</table>
			<?php
		}
		else
		{
			?>
			<p><?php echo JText::_('VAPNOOPTION'); ?></p>
			<?php
		}
		?>

	<?php echo $vik->closeFieldset(); ?>
</div>
