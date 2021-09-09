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

$canRegister 	= isset($displayData['register']) 	? $displayData['register'] 	: false;
$returnUrl 		= isset($displayData['return']) 	? $displayData['return'] 	: '';
$remember 		= isset($displayData['remember'])	? $displayData['remember']	: false;
$useCaptcha 	= isset($displayData['captcha']) 	? $displayData['captcha']	: null;
$gdpr 			= isset($displayData['gdpr'])		? $displayData['gdpr']		: null;
$joomla3810terLinks 	= isset($displayData['joomla3810ter'])		? $displayData['joomla3810ter'] 	: true;
$active			= isset($displayData['active'])		? $displayData['active']	: 'login';

$vik = UIApplication::getInstance();

if (is_null($useCaptcha))
{
	// check if 'recaptcha' is configured
	$useCaptcha = $vik->isCaptcha();
}

if (!$canRegister && $active == 'registration')
{
	// restore active tab to "login" as the registration is disabled
	$active = 'login';
}

if (is_null($gdpr))
{
	$config = UIFactory::getConfig();

	// gdpr setting not provided, get it from the global configuration
	$gdpr 	= $config->getBool('gdpr', false);
	$policy = $config->get('policylink');
}

if ($joomla3810terLinks)
{
	// load com_users site language to display joomla3810ter messages
	JFactory::getLanguage()->load('com_users', JPATH_SITE, JFactory::getLanguage()->getTag(), true);
}

if ($canRegister)
{
	$input = JFactory::getApplication()->input;

	// evaluate the task that will be used to register the account
	if ($input->get('view') != 'emplogin')
	{
		$register_task = 'registeruser';
	}
	else
	{
		$register_task = 'registeremployee';
	}

	?>

	<!-- REGISTRATION -->
	
	<script>

		function vapLoginValueChanged() {
			if (jQuery('input[name=loginradio]:checked').val() == 1) {
				jQuery('.vapregisterblock').css('display', 'none');
				jQuery('.vaploginblock').fadeIn();
			} else {
				jQuery('.vaploginblock').css('display', 'none');
				jQuery('.vapregisterblock').fadeIn();
			}
		}

		function vapValidateRegistrationFields() {
			var names = ["fname", "lname", "email", "username", "password", "confpassword"];
			var fields = {};

			var elem = null;
			var ok = true;

			for (var i = 0;  i < names.length; i++) {
				elem = jQuery('#vapregform input[name="'+names[i]+'"]');

				fields[names[i]] = elem.val();

				if (fields[names[i]].length > 0) {
					elem.removeClass('vaprequiredfield');
				} else {
					ok = false;
					elem.addClass('vaprequiredfield');
				}
			}

			if (ok) {
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				if (!re.test(fields.email)) {
					ok = false;
					jQuery('#vapregform input[name="email"]').addClass('vaprequiredfield');
				}
			}

			if (ok) {
				if (fields.password !== fields.confpassword) {
					ok = false;
					jQuery('#vapregform input[name="password"], #vapregform input[name="confpassword"]').addClass('vaprequiredfield');
				}
			}

			<?php if ($gdpr) { ?>

				if (jQuery('#gdpr-register').is(':checked')) {
					jQuery('#gdpr-register').next().removeClass('vapinvalid');
				} else {
					jQuery('#gdpr-register').next().addClass('vapinvalid');
					ok = false;
				}

			<?php } ?>

			return ok;
		}
	</script>
	
	<div class="vaploginradiobox" id="vaploginradiobox">
		<span class="vaploginradiosp">
			<label for="logradio1"><?php echo JText::_('VAPLOGINRADIOCHOOSE1'); ?></label>
			<input type="radio" id="logradio1" name="loginradio" value="1" onChange="vapLoginValueChanged();" <?php echo $active == 'login' ? 'checked="checked"' : ''; ?> />
		</span>
		<span class="vaploginradiosp">
			<label for="logradio2"><?php echo JText::_('VAPLOGINRADIOCHOOSE2'); ?></label>
			<input type="radio" id="logradio2" name="loginradio" value="2" onChange="vapLoginValueChanged();" <?php echo $active != 'login' ? 'checked="checked"' : ''; ?> />
		</span>
	</div>
	
	<div class="vapregisterblock" style="<?php echo $active != 'login' ? '' : 'display: none;'; ?>">
		<form action="<?php echo JRoute::_('index.php?option=com_vikappointments'); ?>" method="post" name="vapregform" id="vapregform">
			<h3><?php echo JText::_('VAPREGISTRATIONTITLE'); ?></h3>
			
			<div class="vaploginfieldsdiv">
				<div class="vaploginfield">
					<span class="vaploginsplabel" id="vapfname">
						<label for="register-fname"><?php echo JText::_('VAPREGNAME'); ?><sup>*</sup>:</label>
					</span>
					<span class="vaploginspinput">
						<input id="register-fname" type="text" name="fname" value="" size="20" class="vapinput" />
					</span>
				</div>

				<div class="vaploginfield">
					<span class="vaploginsplabel" id="vaplname">
						<label for="register-lname"><?php echo JText::_('VAPREGLNAME'); ?><sup>*</sup>:</label>
					</span>
					<span class="vaploginspinput">
						<input id="register-lname" type="text" name="lname" value="" size="20" class="vapinput" />
					</span>
				</div>

				<div class="vaploginfield">
					<span class="vaploginsplabel" id="vapemail">
						<label for="register-email"><?php echo JText::_('VAPREGEMAIL'); ?><sup>*</sup>:</label>
					</span>
					<span class="vaploginspinput">
						<input id="register-email" type="text" name="email" value="" size="20" class="vapinput" />
					</span>
				</div>

				<div class="vaploginfield">
					<span class="vaploginsplabel" id="vapusername">
						<label for="register-username"><?php echo JText::_('VAPREGUNAME'); ?><sup>*</sup>:</label>
					</span>
					<span class="vaploginspinput">
						<input id="register-username" type="text" name="username" value="" size="20" class="vapinput" />
					</span>
				</div>

				<div class="vaploginfield">
					<span class="vaploginsplabel" id="vappassword">
						<label for="register-password"><?php echo JText::_('VAPREGPWD'); ?><sup>*</sup>:</label>
					</span>
					<span class="vaploginspinput">
						<input id="register-password" type="password" name="password" value="" size="20" class="vapinput" />
					</span>
				</div>

				<div class="vaploginfield">
					<span class="vaploginsplabel" id="vapconfpassword">
						<label for="register-confpassword"><?php echo JText::_('VAPREGCONFIRMPWD'); ?><sup>*</sup>:</label>
					</span>
					<span class="vaploginspinput">
						<input id="register-confpassword" type="password" name="confpassword" value="" size="20" class="vapinput" />
					</span>
				</div>

				<?php
				if ($useCaptcha)
				{
					?>
					<div class="vaploginfield">
						<?php echo $vik->reCaptcha(); ?>
					</div>
					<?php
				}
				?>

				<?php
				if ($gdpr)
				{
					?>
					<div class="vaploginfield">
						<span class="vaploginsplabel" class="">&nbsp;</span>
						<span class="vaploginspinput">
							<input type="checkbox" class="required" id="gdpr-register" value="1" />
							<label for="gdpr-register" style="display: inline-block;">
								<?php
								if ($policy)
								{
									// label with link to read the privacy policy
									echo JText::sprintf(
										'GDPR_POLICY_AUTH_LINK',
										'javascript: void(0);',
										'vapOpenPopup(\'' . $policy . '\');'
									);
								}
								else
								{
									// label without link
									echo JText::_('GDPR_POLICY_AUTH_NO_LINK');
								}
								?>
							</label>
						</span>
					</div>
					<?php
				}
				?>

				<div class="vaploginfield">
					<span class="vaploginsplabel" class="">&nbsp;</span>
					<span class="vaploginspinput">
						<button type="submit" class="vap-btn blue" name="registerbutton" onClick="return vapValidateRegistrationFields();"><?php echo JText::_('VAPREGSIGNUPBTN'); ?></button>
					</span>
				</div>
			</div>
	
			<input type="hidden" name="option" value="com_vikappointments" />
			<input type="hidden" name="task" value="<?php echo $register_task; ?>" />
			<input type="hidden" name="return" value="<?php echo base64_encode($returnUrl); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>

<?php } ?>

<!-- LOGIN -->

<div class="vaploginblock" style="<?php echo $active == 'login' ? '' : 'display: none;'; ?>">
	<?php
	/**
	 * The login form is displayed from the layout below:
	 * /components/com_vikappointments/layouts/blocks/login/[PLATFORM_NAME].php
	 * which depends on the current platform ("joomla" or "wordpress").
	 * 
	 * If you need to change something from this layout, just create
	 * an override of this layout by following the instructions below:
	 * - open the back-end of your Joomla
	 * - visit the Extensions > Templates > Templates page
	 * - edit the active template
	 * - access the "Create Overrides" tab
	 * - select Layouts > com_vikappointments > blocks
	 * - start editing the login/[platform].php file on your template to create your own layout
	 *
	 * @since 1.6.3
	 */
	echo $this->sublayout(VersionListener::getPlatform(), $displayData);
	?>
</div>
