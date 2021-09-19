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

$returnUrl 		= isset($displayData['return']) 	? $displayData['return'] 	: '';
$remember 		= isset($displayData['remember'])	? $displayData['remember']	: false;
$joomla3810terLinks 	= isset($displayData['joomla3810ter'])		? $displayData['joomla3810ter'] 	: true;

$vik = UIApplication::getInstance();

?>
<form action="index.php?option=com_users" method="post">
	<h3><?php echo JText::_('VAPLOGINTITLE'); ?></h3>
	
	<div class="vaploginfieldsdiv">
		<div class="vaploginfield">
			<span class="vaploginsplabel">
				<label for="login-username"><?php echo JText::_('VAPLOGINUSERNAME'); ?></label>
			</span>
			<span class="vaploginspinput">
				<input id="login-username" type="text" name="username" value="" size="20" class="vapinput" />
			</span>
		</div>

		<div class="vaploginfield">
			<span class="vaploginsplabel">
				<label for="login-password"><?php echo JText::_('VAPLOGINPASSWORD'); ?></label>
			</span>
			<span class="vaploginspinput">
				<input id="login-password" type="password" name="password" value="" size="20" class="vapinput" />
			</span>
		</div>

		<?php if ($remember) { ?>

			<input type="hidden" name="remember" id="remember" value="yes" />

		<?php } else { ?>

			<div class="login-fields-rem">
				<label for="login-remember"><?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME'); ?></label>
				<input id="login-remember" type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME'); ?>" />
			</div>

		<?php } ?>

		<div class="vaploginfield">
			<span class="vaploginsplabel">&nbsp;</span>
			<span class="vaploginspinput">
				<button type="submit" class="vap-btn blue" name="Login"><?php echo JText::_('VAPLOGINSUBMIT'); ?></button>
			</span>
		</div>

	</div>

	<?php if ($joomla3810terLinks) { ?>

		<div class="vap-login-joomla3810ter-links">
			<div>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>" target="_blank">
					<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?>
				</a>
			</div>
			<div>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>" target="_blank">
					<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?>
				</a>
			</div>
		</div>

	<?php } ?>

	<input type="hidden" name="return" value="<?php echo base64_encode($vik->routeForExternalUse($returnUrl)); ?>" />
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<?php echo JHtml::_('form.token'); ?>
</form>
