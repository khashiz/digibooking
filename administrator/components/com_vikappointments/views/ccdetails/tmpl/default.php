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

$credit_card = $this->creditCard;
$order 		 = $this->order;

$config = UIFactory::getConfig();

$vik = UIApplication::getInstance();

$dt_format = $config->get('dateformat') . ' @ ' . $config->get('timeformat');

$delete_href = sprintf(
	'index.php?option=com_vikappointments&task=ccdetails&tmpl=component&id=%d&type=%s&rmhash=%s',
	$this->id,
	$this->type,
	$this->rmHash
);

?>

<div class="btn-toolbar" style="height:32px;">

	<div class="btn-group pull-left vap-setfont">
		<strong><?php echo JText::sprintf('VAPCREDITCARDAUTODELMSG', date($dt_format, $this->expDate)); ?></strong>
	</div>

	<div class="btn-group pull-right">
		<a href="<?php echo $delete_href; ?>" class="btn btn-danger" onclick="return confirmCreditCardDelete(event);">
			<?php echo JText::_('VAPDELETE'); ?>
		</a>
	</div>

</div>

<div class="span6">
	<?php echo $vik->openEmptyFieldset(); ?>

		<?php foreach ($credit_card as $k => $v) { ?>

			<?php echo $vik->openControl($v->label . ':'); ?>
				<input type="text" value="<?php echo $v->value; ?>" readonly size="32" />
				
				<?php if ($k == 'cardNumber') { ?>
					<img src="<?php echo VAPADMIN_URI . 'payments/off-cc/resources/icons/' . $credit_card->brand->alias . '.png'; ?>" />
				<?php } ?>

			<?php echo $vik->closeControl(); ?>

		<?php } ?>

	<?php echo $vik->closeEmptyFieldset(); ?>
</div>

<?php
JText::script('VAPSYSTEMCONFIRMATIONMSG');
?>

<script type="text/javascript">

	function confirmCreditCardDelete(event) {
		// turn off any previously attached events
		jQuery(event.target).off('click');
			
		// make sure the user confirmed the prompt
		if (confirm(Joomla.JText._('VAPSYSTEMCONFIRMATIONMSG')))
		{
			<?php
			// check the current platform
			if (VersionListener::getPlatform() == 'joomla')
			{
				?>
				// just return TRUE on Joomla to hit the URL HREF
				return true;
				<?php
			}
			else
			{
				?>
				// stop propagating the event
				event.stopPropagation();
				event.preventDefault();

				// get ID of the current modal
				var id = jQuery('div.modal.fade.in').first().find('.modal-body-wrapper').attr('id');
				// retrieve contents via AJAX by reaching the link HREF
				wpAppendModalContent(id, jQuery(event.target).attr('href'));
				// go ahead to always return false in WordPress
				<?php
			}
			?>
		}

		// return false, the customer didn't confirm the prompt
		return false;
	}

</script>
