<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   (C) 2012 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var JDocumentError $this */

$app  = JFactory::getApplication();
$user = JFactory::getUser();

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$format   = $app->input->getCmd('format', 'html');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
JHtml::_('jquery.framework', false);

// Add js
JHtml::_('script', 'uikit.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'custom.js', array('version' => 'auto', 'relative' => true));

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta charset="utf-8" />
	<title><?php echo $this->title; ?> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/uikit-rtl.min.css" rel="stylesheet" />
    <link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/digibooking.css" rel="stylesheet" />
    <script src="<?php echo $this->baseurl; ?>/media/jui/js/jquery.min.js"></script>
    <script src="<?php echo $this->baseurl; ?>/media/jui/js/jquery-migrate.min.js"></script>
    <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/uikit.min.js"></script>
    <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/custom.js"></script>
	<?php if ($app->get('debug_lang', '0') == '1' || $app->get('debug', '0') == '1') : ?>
		<link href="<?php echo JUri::root(true); ?>/media/cms/css/debug.css" rel="stylesheet" />
	<?php endif; ?>
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
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
            <div><?php echo JHtml::_('content.prepare','{loadposition aside}'); ?></div>
        </aside>
    </div>
    <div class="uk-width-1-1 uk-width-expand@m">
        <div>
            <div class="vap-allorders-void uk-flex uk-flex-center uk-flex-middle" data-uk-height-viewport="expand: true">
                <div class="uk-text-center">
                    <div class="uk-margin-medium-bottom"><img src="<?php echo JUri::base().'images/404-error.svg'; ?>" width="387" height="339" class="uk-width-medium"></div>
                    <div class="font f500 uk-text-large"><?php echo JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></div>
                </div>
            </div>
            <jdoc:include type="message" />
            <jdoc:include type="component" />
        </div>
    </div>
</div>
</body>
</html>
