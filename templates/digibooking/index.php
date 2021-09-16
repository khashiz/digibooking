<?php
defined('_JEXEC') or die;
/** @var JDocumentHtml $this */
$document = JFactory::getDocument();
$app  = JFactory::getApplication();
$user = JFactory::getUser();
// Output as HTML5
$this->setHtml5(true);
// Getting params from template
$params = $app->getTemplate(true)->params;
$menu = $app->getMenu();
$active = $menu->getActive();
$pageparams = $menu->getParams( $active->id );
$pageclass = $pageparams->get( 'pageclass_sfx' );
// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

$lang = JFactory::getLanguage();
$languages = JLanguageHelper::getLanguages('lang_code');
$languageCode = $languages[ $lang->getTag() ]->sef;

JHtml::_('jquery.framework');

// Add Stylesheets
JHtml::_('stylesheet', 'uikit-rtl.min.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'digibooking.css', array('version' => 'auto', 'relative' => true));

// Add js
JHtml::_('script', 'uikit.min.js', array('version' => 'auto', 'relative' => true));
?>
<!DOCTYPE html>
<html lang="<?php echo JFactory::getLanguage()->getTag(); ?>" dir="<?php echo JFactory::getLanguage()->isRtl() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="<?php echo $params->get('presetcolor'); ?>">
    <jdoc:include type="head" />
</head>
<body>
<div class="uk-grid-collapse" data-uk-grid>
    <div class="uk-width-1-1 uk-width-medium@m">
        <aside class="uk-background-primary uk-position-fixed uk-width-medium uk-height-viewport uk-padding">
            <div class="uk-text-center uk-margin-medium-bottom">
                <a href="<?php echo JUri::base(); ?>" target="_self" rel="nofollow" class="uk-display-inline-block">
                    <img src="<?php echo JUri::base().'images/digikala-logo.png'; ?>" alt="DigiKala" width="215" height="53">
                </a>
                <span class="uk-text-muted uk-display-block uk-margin-small-top font"><?php echo JText::sprintf('RESERVE_SYSTEM'); ?></span>
            </div>
            <div><jdoc:include type="modules" name="aside" style="xhtml" /></div>
        </aside>
    </div>
    <div class="uk-width-1-1 uk-width-expand@m">
        <div>
            <jdoc:include type="message" />
            <jdoc:include type="component" />


            <?php /* ?>
             <a class="uk-button uk-button-default" href="#editProfile" uk-toggle>YouTube</a>
            <div id="editProfile" class="uk-flex-top" data-uk-modal>
                <div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-width-large@m">
                    <button class="uk-modal-close-outside" type="button" uk-close></button>
                    <iframe src="http://localhost/digibooking/edit-profile?tmpl=component" class="uk-height-large uk-width-1-1" frameborder="0"></iframe>
                </div>
            </div>
            <?php JHTML::_('behavior.modal'); ?>
            <a class="modal" href="index.php?option=com_users&view=profile&layout=edit&tmpl=component?" rel="{handler: 'iframe', size: {x: 640, y: 540}}"> Edit Login Details</a>
            <?php */ ?>
        </div>
    </div>
</div>
</body>
</html>