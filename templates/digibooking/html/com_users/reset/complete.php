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
<div class="uk-margin-bottom reset-complete<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1 class="font uk-h4 f500 uk-text-center uk-margin-medium-bottom"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.complete'); ?>" method="post" class="form-validate form-horizontal well">
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
			<fieldset class="uk-padding-remove uk-margin-remove">
                <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid>
                    <?php /* if (isset($fieldset->label)) : ?><p><?php echo JText::_($fieldset->label); ?></p><?php endif; */ ?>
                    <?php echo $this->form->renderFieldset($fieldset->name); ?>
                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" class="uk-button uk-button-primary uk-button-large uk-width-1-1 uk-margin-small-top validate"><?php echo JText::_('JSUBMIT'); ?></button>
                        </div>
                    </div>
                </div>
			</fieldset>
		<?php endforeach; ?>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
<div class="uk-text-left">
    <a class="font uk-text-small f600 authBottomLink" href="<?php echo JRoute::_('index.php?option=com_users&view=login'); ?>"><?php echo JText::_('COM_USERS_BACK_TO_LOGIN'); ?></a>
</div>