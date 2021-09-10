<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

?>
<div class="uk-margin-bottom login<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1 class="font uk-h4 f500 uk-text-center uk-margin-medium-bottom"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>
	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
		<div class="login-description">
	<?php endif; ?>
	<?php if ($this->params->get('logindescription_show') == 1) : ?>
		<?php echo $this->params->get('login_description'); ?>
	<?php endif; ?>
	<?php if ($this->params->get('login_image') != '') : ?>
		<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JText::_('COM_USERS_LOGIN_IMAGE_ALT'); ?>" />
	<?php endif; ?>
	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
		</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-validate form-horizontal well">
		<fieldset class="uk-padding-remove uk-margin-remove">
            <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid>
                <?php echo $this->form->renderFieldset('credentials'); ?>
                <?php if ($this->tfa) : ?>
                    <?php echo $this->form->renderField('secretkey'); ?>
                <?php endif; ?>
                <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
                    <div class="control-group uk-hidden">
                        <div class="control-label">
                            <label for="remember">
                                <?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME'); ?>
                            </label>
                        </div>
                        <div class="controls">
                            <input id="remember" type="checkbox" name="remember" class="inputbox" value="yes" checked />
                        </div>
                    </div>
                <?php endif; ?>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" class="uk-button uk-button-primary uk-button-large uk-width-1-1 uk-margin-small-top"><?php echo JText::_('JLOGIN'); ?>&ensp;<img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" width="24" height="24" alt="" data-uk-svg></button>
                    </div>
                </div>
                <?php $return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('login_redirect_menuitem'))); ?>
                <input type="hidden" name="return" value="<?php echo base64_encode($return); ?>" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
		</fieldset>
	</form>
</div>
<div class="uk-text-left">
    <a class="font uk-text-small f600 authBottomLink" href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
    <!--
    <a href="<?php /* echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('COM_USERS_LOGIN_REMIND'); */ ?></a>
    -->
    <?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
    <?php if ($usersConfig->get('allowUserRegistration')) : ?>
        <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"><?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a>
    <?php endif; ?>
</div>