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

// route return URL
$returnUrl = $vik->routeForExternalUse($returnUrl);

// create login URL for Wordpress
$url = wp_login_url($returnUrl);
// append action=login
$url .= (strpos($url, '?') !== false ? '&' : '?') . 'action=login';

?>
<form action="<?php echo $url; ?>" method="post">
	<h3><?php echo JText::_('VAPLOGINTITLE'); ?></h3>
	
	<div class="vaploginfieldsdiv">
		<div class="vaploginfield">
			<span class="vaploginsplabel">
				<label for="login-username"><?php echo JText::_('VAPLOGINUSERNAME'); ?></label>
			</span>
			<span class="vaploginspinput">
				<input id="login-username" type="text" name="log" value="" size="20" class="vapinput" />
			</span>
		</div>

		<div class="vaploginfield">
			<span class="vaploginsplabel">
				<label for="login-password"><?php echo JText::_('VAPLOGINPASSWORD'); ?></label>
			</span>
			<span class="vaploginspinput">
				<input id="login-password" type="password" name="pwd" value="" size="20" class="vapinput" />
			</span>
		</div>

		<?php if ($remember) { ?>

			<input type="hidden" name="remember" id="remember" value="1" />

		<?php } else { ?>

			<div class="login-fields-rem">
				<label for="login-remember"><?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME'); ?></label>
				<input id="login-remember" type="checkbox" name="remember" class="inputbox" value="1" alt="<?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME'); ?>" />
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
				<a href="<?php echo wp_lostpassword_url(); ?>" target="_blank">
					<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?>
				</a>
			</div>
		</div>

	<?php } ?>

	<?php echo JHtml::_('form.token'); ?>
</form>
