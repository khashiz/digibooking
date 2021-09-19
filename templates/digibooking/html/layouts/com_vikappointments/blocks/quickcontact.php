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

$title 			= isset($displayData['title'])			? $displayData['title'] 		: '';
$id_service 	= isset($displayData['id_service'])		? $displayData['id_service'] 	: 0;
$id_employee 	= isset($displayData['id_employee'])	? $displayData['id_employee']	: 0;
$returnUri		= isset($displayData['return'])			? $displayData['return']		: '';
$gdpr 			= isset($displayData['gdpr'])			? $displayData['gdpr']			: null;
$itemid 	 	= isset($displayData['itemid'])			? $displayData['itemid']		: null;

if ($id_service)
{
	// reviews for a service
	$col_name 	= 'id_service';
	$col_value 	= $id_service;
	$contact_task = 'quickcontactservice';
}
else
{
	// reviews for an employee
	$col_name 	= 'id_employee';
	$col_value 	= $id_employee;
	$contact_task = 'quickcontact';
}

if (is_null($gdpr))
{
	// gdpr setting not provided, get it from the global configuration
	$gdpr = UIFactory::getConfig()->getBool('gdpr', false);
}

if (is_null($itemid))
{
	// item id not provided, get the current one (if set)
	$itemid = JFactory::getApplication()->input->getInt('Itemid');
}

$vik = UIApplication::getInstance();

?>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&task=' . $contact_task . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" name="quickcontactform">
	
	<div class="vapqcdiv <?php echo $vik->getThemeClass('background'); ?>" style="display: none;">
		
		<h2 class="vapqcnominative"><?php echo $title; ?></h2>
		
		<div class="vapqcsendname">
			<label for="qc-send-name"><?php echo JText::_('VAPEMPSENDERNAMELABEL'); ?></label>
			<input id="qc-send-name" type="text" name="sendername" value="" class="required" size="32" />
		</div>
		
		<div class="vapqcsendmail">
			<span class="vapqcmailsp">
				<label for="qc-send-mail"><?php echo JText::_('VAPEMPSENDERMAILLABEL'); ?></label>
				<input id="qc-send-mail" type="text" name="sendermail" value="" class="required" size="32" />
			</span>
		</div>
		
		<div class="vapqcmailcont">
			<label for="qc-send-text" style="vertical-align: top;"><?php echo JText::_('VAPEMPMAILCONTENTLABEL'); ?></label>
			<textarea name="mail_content" id="qc-send-text" class="required"></textarea>
		</div>

		<?php
		// check if global captcha is configured
		if ($vik->isGlobalCaptcha())
		{
			// display reCaptcha plugin
			echo $vik->reCaptcha();
		}
		?>
		
		<span class="vapqcbuttonsp">
			<button type="submit" class="vap-btn blue" onClick="return vapValidateBeforeSendMail();">
				<?php echo JText::_('VAPEMPSENDMAILOK'); ?>
			</button>

			<button type="button" class="vap-btn" onClick="vapCancelMail();">
				<?php echo JText::_('VAPEMPSENDMAILCANCEL'); ?>
			</button> 
		</span>

		<?php
		/**
		 * Display a joomla3810ter message for GDPR that
		 * inform the users that the specified data
		 * are not stored within the database of the website.
		 *
		 * @since 	1.6
		 */

		if ($gdpr)
		{
			?>
			<p class="gdpr-joomla3810ter-disclaimer">
				<i class="fa fa-info-circle"><span><?php echo JText::_('GDPR_DISCLAIMER'); ?></span></i>
			</p>
			<?php
		}
		?>
		
	</div>
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="<?php echo $col_name; ?>" value="<?php echo $col_value; ?>" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="<?php echo $contact_task; ?>" /> 
	<input type="hidden" name="return" value="<?php echo base64_encode($returnUri); ?>" />
</form>

<script>

	var qcValidator = new VikFormValidator('form[name="quickcontactform"]', 'vaprequiredfield');
	var cancelElement = null;

	function vapValidateBeforeSendMail() {
		return qcValidator.validate(function() {

			var field = jQuery('#qc-send-mail');
			var email = field.val();

			if (field.hasClass(qcValidator.clazz)) {
				return false;
			}

			if (!isEmailCompliant(email)) {
				qcValidator.setInvalid(field);
				return false;
			}

			qcValidator.unsetInvalid(field);
			return true;
		});
	}
	
	function vapCancelMail() {
		jQuery('.vapqcdiv').fadeOut();

		if (cancelElement) {
			jQuery('html,body').animate( {scrollTop: (jQuery(cancelElement).offset().top - 20)}, {duration:'normal'} );
		}
	}

	// used when clicking "QUICK CONTACT" from the list
	function vapGoToMail(elem, id, name) {

		cancelElement = elem;

		if (id) { 
			jQuery('input[name="<?php echo $col_name; ?>"]').val(id);
		}

		if (name) {
			jQuery('.vapqcnominative').text(name);
		}

		jQuery('.vapqcdiv').fadeIn();
		jQuery('html,body').animate( {scrollTop: (jQuery('.vapqcdiv').offset().top - 20)}, {duration:'normal'} );
	}

</script>
