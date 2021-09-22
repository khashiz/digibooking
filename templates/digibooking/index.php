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

JHtml::_('jquery.framework', false);

// Add Stylesheets
JHtml::_('stylesheet', 'uikit-rtl.min.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'digibooking.css', array('version' => 'auto', 'relative' => true));

// Add js
JHtml::_('script', 'uikit.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'persianumber.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'custom.js', array('version' => 'auto', 'relative' => true));
?>
<!DOCTYPE html>
<html lang="<?php echo JFactory::getLanguage()->getTag(); ?>" dir="<?php echo JFactory::getLanguage()->isRtl() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="<?php echo $params->get('presetcolor'); ?>">
    <jdoc:include type="head" />
</head>
<body>
<?php if ($pageclass == 'auth') { ?>
    <main class="uk-flex uk-flex-middle uk-background-primary uk-padding uk-padding-remove-horizontal" data-uk-height-viewport="expand: true">
        <div class="uk-flex-1">
            <div class="uk-container uk-container-xsmall">
                <div class="uk-width-1-1 uk-width-1-2@m uk-margin-auto">
                    <div class="uk-text-center uk-margin-medium-bottom">
                        <a href="<?php echo JUri::base(); ?>" target="_blank" class="uk-display-inline-block">
                            <img src="<?php echo JUri::base().'images/sprite.svg#logo'; ?>" alt="DigiKala" width="212" height="50" data-uk-svg>
                        </a>
                        <span class="uk-text-muted uk-display-block uk-margin-small-top font"><?php echo JText::sprintf('RESERVE_SYSTEM'); ?></span>
                    </div>
                    <div class="uk-card uk-card-default uk-border-rounded-large uk-margin-medium-bottom uk-box-shadow-xlarge uk-border-rounded">
                        <div class="uk-card-body">
                            <jdoc:include type="message" />
                            <jdoc:include type="component" />
                        </div>
                    </div>
                    <div class="uk-text-center">
                        <a href="https://uxdee.org" rel="nofollow" class="uk-display-inline-block uxdee" target="_blank"><img src="<?php echo JUri::base().'images/uxdee.svg'; ?>" width="60"></a>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php } else { ?>
    <?php $now = strtotime(new JDate('now')); ?>
    <?php
    $db = JFactory::getDbo();
    $countActiveOrders = $db->getQuery(true);
    $countActiveOrders
        ->select($db->quoteName(array('id','id_employee', 'checkin_ts', 'duration', 'status', 'sid', 'conf_key', 'id_parent', 'createdby')))
        ->from($db->quoteName('#__vikappointments_reservation'))
        ->where($db->quoteName('createdby') . ' = ' . JFactory::getUser()->id)
        ->where($db->quoteName('id_parent') . ' != ' . -1)
        ->where($db->quoteName('status') . ' LIKE ' . $db->quote('CONFIRMED'))
        ->where($db->quoteName('checkin_ts') . ' > ' . $now);
    $orders = $db->setQuery($countActiveOrders)->loadObjectList();
    ?>
    <div class="uk-grid-collapse" data-uk-grid>
        <div class="uk-width-1-1 uk-width-medium@m uk-visible@m">
            <aside class="uk-background-primary uk-position-fixed uk-width-medium uk-height-viewport uk-padding">
                <div class="uk-text-center uk-margin-large-bottom">
                    <a href="<?php echo JUri::base(); ?>" target="_self" class="uk-display-inline-block">
                        <img src="<?php echo JUri::base().'images/sprite.svg#logo'; ?>" alt="DigiKala" width="212" height="50" data-uk-svg>
                    </a>
                    <span class="uk-text-muted uk-display-block uk-margin-small-top font"><?php echo JText::sprintf('RESERVE_SYSTEM'); ?></span>
                </div>
                <div class="uk-margin-large-bottom uk-text-white">
                    <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders'); ?>" class="uk-link-reset uk-padding-small uk-background-success uk-flex uk-flex-middle uk-flex-center uk-text-white uk-box-shadow-small uk-text-zero countActiveUsers">
                        <?php if (count($orders)) { ?>
                            <span class="uk-margin-left count fnum font uk-text-small uk-flex uk-flex-middle uk-flex-center"><?php echo count($orders); ?></span>
                            <span class="text uk-text-small font uk-text-small"><?php echo JText::sprintf('ACTIVE_RESERVE'); ?></span>
                        <?php } else { ?>
                            <span class="text uk-text-small font uk-text-small"><?php echo JText::sprintf('NO_ACTIVE_RESERVE'); ?></span>
                        <?php } ?>
                    </a>
                </div>
                <div><jdoc:include type="modules" name="aside" style="xhtml" /></div>
                <div class="uk-padding-small uk-position-absolute uk-text-zero sideFooter">
                    <div class="uk-grid-small" data-uk-grid>
                        <div class="uk-width-expand">
                            <a href="#" class="uk-text-tiny uk-text-white uk-margin-small-left uk-position-relative font errorReport" target="_blank"><?php echo JText::sprintf('REPORT_AN_ERROR'); ?></a>
                            <span class="uk-text-tiny uk-text-gray font">v.1.2</span>
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-bottom">
                            <a href="https://uxdee.org" rel="nofollow" class="uk-display-inline-block uxdee" target="_blank"><img src="<?php echo JUri::base().'images/uxdee.svg'; ?>" width="60"></a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
        <div class="uk-width-1-1 uk-width-expand@m">
            <div>
                <div class="uk-hidden@m">
                    <div class="uk-background-primary uk-padding-small" data-uk-sticky>
                        <div class="uk-grid-medium uk-child-width-auto" data-uk-grid>
                            <div class="uk-width-auto">
                                <a href="#offcanvas-overlay" class="uk-button uk-button-primary uk-flex uk-flex-center uk-flex-middle offcanvasButton" data-uk-toggle><img src="<?php echo JUri::base().'images/sprite.svg#list'; ?>" width="40" height="40" data-uk-svg></a>
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-center">
                                <a href="<?php echo JUri::base(); ?>" target="_self" class="uk-display-inline-block">
                                    <img src="<?php echo JUri::base().'images/sprite.svg#logo'; ?>" alt="DigiKala" width="150" data-uk-svg />
                                </a>
                            </div>
                            <div class="uk-width-auto">
                                <a href="<?php echo JRoute::_('index.php?option=com_users&view=login&layout=logout&task=user.menulogout'); ?>" class="uk-button uk-button-danger uk-flex uk-flex-center uk-flex-middle offcanvasButton"><img src="<?php echo JUri::base().'images/sprite.svg#power'; ?>" width="40" height="40" data-uk-svg></a>
                            </div>
                        </div>
                    </div>
                </div>
                <jdoc:include type="message" />
                <jdoc:include type="component" />
            </div>
        </div>
    </div>

    <div id="offcanvas-overlay" data-uk-offcanvas="overlay: true">
        <aside class="uk-offcanvas-bar uk-background-primary">
            <div class="uk-margin-medium-bottom uk-text-white">
                <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders'); ?>" class="uk-link-reset uk-padding-small uk-background-success uk-flex uk-flex-middle uk-flex-center uk-text-white uk-box-shadow-small uk-text-zero countActiveUsers">
                    <?php if (count($orders)) { ?>
                        <span class="uk-margin-left count fnum font uk-text-small uk-flex uk-flex-middle uk-flex-center"><?php echo count($orders); ?></span>
                        <span class="text uk-text-small font uk-text-small"><?php echo JText::sprintf('ACTIVE_RESERVE'); ?></span>
                    <?php } else { ?>
                        <span class="text uk-text-small font uk-text-small"><?php echo JText::sprintf('NO_ACTIVE_RESERVE'); ?></span>
                    <?php } ?>
                </a>
            </div>
            <div><jdoc:include type="modules" name="aside" style="xhtml" /></div>
            <div class="uk-padding-small uk-position-absolute uk-text-zero sideFooter">
                <div class="uk-grid-small" data-uk-grid>
                    <div class="uk-width-expand">
                        <a href="#" class="uk-text-tiny uk-text-white uk-margin-small-left uk-position-relative font errorReport" target="_blank"><?php echo JText::sprintf('REPORT_AN_ERROR'); ?></a>
                        <span class="uk-text-tiny uk-text-gray font">v.1.2</span>
                    </div>
                    <div class="uk-width-auto uk-flex uk-flex-bottom">
                        <a href="https://uxdee.org" rel="nofollow" class="uk-display-inline-block uxdee" target="_blank"><img src="<?php echo JUri::base().'images/uxdee.svg'; ?>" width="60"></a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
<?php } ?>
</body>
</html>