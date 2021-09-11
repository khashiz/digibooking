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
JHtml::_('stylesheet', 'digiauth.css', array('version' => 'auto', 'relative' => true));

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
<body class="uk-height-viewport">
<jdoc:include type="modules" name="pagetop" style="xhtml" />
<main class="uk-height-viewport uk-flex uk-flex-middle">
    <div class="uk-flex-1">
        <div class="uk-container uk-container-xsmall">
            <div class="uk-width-1-1 uk-width-1-2@m uk-margin-auto">
                <div class="uk-text-center uk-margin-medium-bottom">
                    <a href="<?php echo JUri::base(); ?>" target="_blank" rel="nofollow" class="uk-display-inline-block">
                        <img src="<?php echo JUri::base().'images/digikala-logo.png'; ?>" alt="DigiKala" width="215" height="53">
                    </a>
                    <span class="uk-text-muted uk-display-block uk-margin-small-top font"><?php echo JText::sprintf('RESERVE_SYSTEM'); ?></span>
                </div>
                <div class="uk-card uk-card-default uk-border-rounded-large uk-margin-medium-bottom">
                    <div class="uk-card-body">
                        <jdoc:include type="message" />
                        <jdoc:include type="component" />
                    </div>
                </div>
                <div class="uk-text-center">
                    <a href="https://uxdee.org" target="_blank" rel="nofollow" class="uk-display-inline-block">
                        <img src="<?php echo JUri::base().'images/uxdee-logo.png'; ?>" alt="UXDee" width="66" height="16">
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>