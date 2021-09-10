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
$netparsi = JTEXT::_('NETPARSI');

$lang = JFactory::getLanguage();
$languages = JLanguageHelper::getLanguages('lang_code');
$languageCode = $languages[ $lang->getTag() ]->sef;

JHtml::_('jquery.framework');

// Add Stylesheets
if (JFactory::getLanguage()->isRtl()) {
    JHtml::_('stylesheet', 'uikit-rtl.min.css', array('version' => 'auto', 'relative' => true));
    JHtml::_('stylesheet', 'rtl/orgbattery-rtl.css', array('version' => 'auto', 'relative' => true));
} else {
    JHtml::_('stylesheet', 'uikit.min.css', array('version' => 'auto', 'relative' => true));
    JHtml::_('stylesheet', 'ltr/orgbattery-ltr.css', array('version' => 'auto', 'relative' => true));
}

// Add js
JHtml::_('script', 'uikit.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'uikit-icons.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'custom.js', array('version' => 'auto', 'relative' => true));

$socialsicons = json_decode( $params->get('socials'),true);
$total = count($socialsicons['icon']);
?>
<!DOCTYPE html>
<html lang="<?php echo JFactory::getLanguage()->getTag(); ?>" dir="<?php echo JFactory::getLanguage()->isRtl() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="<?php echo $params->get('presetcolor'); ?>">
    <jdoc:include type="head" />
</head>
<body class="uk-background-muted <?php echo $view.' '.$layout.' '.$task; ?>">
<?php if ($pageclass != 'checkout') { ?>
    <header class="uk-card uk-card-default uk-box-shadow-small" id="header">
        <div class="uk-text-zero uk-padding-small uk-padding-remove-horizontal uk-padding-remove-bottom">
            <div class="uk-container">
                <div>
                    <div class="uk-grid-small uk-grid-collapse" data-uk-grid>
                        <div class="uk-width-1-4 uk-width-auto@m uk-hidden@m hamIcon">
                            <div class="uk-flex uk-flex-right">
                                <a href="#hamMenu" data-uk-toggle class="uk-border-rounded uk-text-primary uk-background-muted uk-text-zero uk-lineheight-zero uk-padding-tiny"><img src="<?php echo JURI::base().'images/sprite.svg#bars'; ?>" width="24" height="24" alt="<?php echo $sitename; ?>" data-uk-svg></a>
                            </div>
                        </div>
                        <div class="uk-width-1-2 uk-width-auto@m uk-flex uk-flex-middle uk-flex-center uk-flex-right@m logo">
                            <a href="<?php echo JURI::base(); ?>" title="<?php echo $sitename; ?>" class="uk-display-inline-block uk-text-primary" target="_self">
                                <img src="<?php echo JUri::base().'images/globe.svg' ?>" width="41" height="40" alt="<?php echo $sitename; ?>" class="uk-margin-small-left">
                                <img src="<?php echo JUri::base().'images/sprite.svg#narin' ?>" width="75" height="40" alt="<?php echo $sitename; ?>" data-uk-svg>
                            </a>
                        </div>
                        <div class="uk-width-1-4 uk-width-auto@m uk-hidden@m userIcon">
                            <div class="uk-flex uk-flex-left">
                                <a href="#userMenu" data-uk-toggle class="uk-border-rounded uk-background-muted uk-text-zero uk-lineheight-zero uk-padding-tiny">
                                    <div class="uk-position-relative uk-text-<?php echo $user->id ? 'primary' : 'muted'; ?>">
                                        <img src="<?php echo JURI::base().'images/sprite.svg#user'; ?>" width="24" height="24" alt="<?php echo $sitename; ?>" data-uk-svg>
                                        <?php if($user->id) { ?>
                                            <span class="userIndicator uk-flex uk-flex-center uk-flex-middle"></span>
                                        <?php } ?>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="uk-width-1-1 uk-hidden@m mobileSpacer"></div>
                        <div class="uk-width-expand search">
                            <jdoc:include type="modules" name="search" style="xhtml" />
                            <div id="offlajn-ajax-tile-results"></div>
                        </div>
                        <div class="uk-width-auto uk-visible@m"><jdoc:include type="modules" name="usermenu" style="xhtml" /></div>
                        <div class="uk-width-1-1 uk-visible@m"></div>
                        <div class="uk-width-auto uk-width-1-1@m uk-flex uk-flex-column uk-flex-center uk-flex-left@m uk-visibl">
                            <div class="stickyHeader" data-uk-sticky="top: 120; animation: uk-animation-slide-top; show-on-up: false; media:@m;">
                                <div class="uk-container">
                                    <div class="uk-grid-small" data-uk-grid>
                                        <div class="uk-width-expand menu"><jdoc:include type="modules" name="menu" style="xhtml" /></div>
                                        <div class="uk-width-auto cart"><jdoc:include type="modules" name="cart" style="xhtml" /></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
<?php } ?>
<?php if ($pageclass == 'checkout') { ?>
    <header class="uk-position-relative">
        <div class="uk-text-zero uk-box-shadow-small bgWhite stickyHeader" data-uk-sticky="top: 120; animation: uk-animation-slide-top;">
            <div class="uk-container">
                <div class="stickyEffect">
                    <div class="uk-padding-small uk-padding-remove-horizontal">
                        <div class="uk-grid-small uk-flex-middle uk-flex-center" data-uk-grid>
                            <div class="uk-width-1-4 uk-hidden@m">
                                <div class="uk-flex uk-flex-right">
                                    <a href="#hamMenu" data-uk-toggle class="uk-border-rounded uk-text-primary uk-background-muted uk-text-zero uk-lineheight-zero uk-padding-tiny"><img src="<?php echo JURI::base().'images/sprite.svg#bars'; ?>" width="24" height="24" alt="<?php echo $sitename; ?>" data-uk-svg></a>
                                </div>
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-center uk-flex-right@m">
                                <div>
                                    <div class="uk-grid-small logoContainer" data-uk-grid>
                                        <div class="uk-width-auto uk-flex uk-flex-middle">
                                            <a href="<?php echo JURI::base(); ?>" title="<?php echo $sitename; ?>" class="uk-display-inline-block uk-text-primary" target="_self">
                                                <img src="<?php echo JUri::base().'images/globe.svg' ?>" width="41" height="40" alt="<?php echo $sitename; ?>" class="uk-margin-small-left">
                                                <img src="<?php echo JUri::base().'images/sprite.svg#narin' ?>" width="75" height="40" alt="<?php echo $sitename; ?>" data-uk-svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-left uk-visible@m">
                                <a href="<?php echo JURI::base(); ?>" title="" class="uk-text-tiny uk-text-bold uk-text-muted font">
                                    <span><?php echo JText::sprintf('BACK'); ?></span>
                                    <span class="uk-visible@m"><?php echo JText::sprintf('TOSHOP'); ?></span>
                                </a>
                            </div>
                            <div class="uk-width-1-4 uk-hidden@m homeIcon">
                                <div class="uk-flex uk-flex-left">
                                    <a href="<?php echo JURI::base(); ?>" class="uk-border-rounded uk-background-muted uk-text-zero uk-lineheight-zero uk-padding-tiny uk-text-primary">
                                        <img src="<?php echo JURI::base().'images/sprite.svg#home'; ?>" width="24" height="24" alt="<?php echo $sitename; ?>" data-uk-svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
<?php } ?>
<?php if ($pageparams->get('show_page_heading')) : ?>
    <section class="bgSecondary uk-padding uk-padding-remove-horizontal uk-text-zero pageHeading">
        <div class="uk-container">
            <div>
                <div class="uk-grid-small" data-uk-grid>
                    <div class="uk-width-1-1">
                        <h2 class="uk-margin-remove uk-text-white uk-text-center font"><?php echo $pageparams->get('page_heading'); ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<jdoc:include type="modules" name="pagetop" style="xhtml" />
<jdoc:include type="message" />
<main class="uk-overflow-hidden">
    <div class="<?php echo $pageclass != 'home' ? 'uk-padding uk-padding-remove-horizontal' : ''; ?>">
        <jdoc:include type="modules" name="bodytop" style="xhtml" />
        <div>
            <div class="<?php echo $pageparams->get('gridsize', 'uk-container'); if ($pageclass == 'checkout') { echo ' uk-container-xsmall';} ?> ">
                <div class="hikashop_cpanel_main_interface">
                    <div class="hikashop_dashboard" id="hikashop_dashboard" data-uk-grid>
                        <?php if ($this->countModules( 'sidestart' )) { ?>
                            <div class="uk-width-1-1 uk-width-1-4@m uk-visible@m">
                                <aside data-uk-sticky="offset: 110; bottom: true;">
                                    <div>
                                        <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid><jdoc:include type="modules" name="sidestart" style="xhtml" /></div>
                                    </div>
                                </aside>
                            </div>
                        <?php } ?>
                        <div class="uk-width-1-1 uk-width-expand@m">
                            <div><jdoc:include type="component" /></div>
                        </div>
                        <?php if ($this->countModules( 'sideend' )) { ?>
                            <div class="uk-width-1-1 uk-width-1-4@m">
                                <aside data-uk-sticky="offset: 110; bottom: true;">
                                    <div>
                                        <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid><jdoc:include type="modules" name="sideend" style="xhtml" /></div>
                                    </div>
                                </aside>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <jdoc:include type="modules" name="bodybottom" style="xhtml" />
    </div>
</main>
<jdoc:include type="modules" name="pagebottom" style="xhtml" />
<?php if ($pageclass != 'checkout') { ?>
<footer class="uk-text-zero">
    <div class="socials">
        <div class="uk-container">
            <div>
                <div data-uk-grid>
                    <div class="uk-width-1-1 uk-width-1-3@m uk-flex-last uk-flex-first@m">
                        <div class="uk-padding-small bgPrimary footerSocials">
                            <div class="uk-grid-small uk-child-width-auto uk-flex-center" data-uk-grid>
                                <div class="uk-flex uk-flex-middle"><span class="uk-text-white uk-text-small font"><?php echo JTEXT::_('FOLLOWUS'); ?></span></div>
                                <div>
                                    <div>
                                        <div class="uk-child-width-auto uk-grid-small" data-uk-grid>
                                            <?php for($i=0;$i<$total;$i++) { ?>
                                                <?php if ($socialsicons['link'][$i] != '') { ?>
                                                    <div>
                                                        <a href="<?php echo $socialsicons['link'][$i]; ?>" class="uk-text-white hoverWhite" target="_blank" title="<?php echo $socialsicons['title'][$i]; ?>" data-uk-tooltip="offset: 28"><img src="<?php echo JURI::base().'images/sprite.svg#'.$socialsicons['icon'][$i] ?>" width="24" height="24" alt="<?php echo $socialsicons['title'][$i]; ?>" data-uk-svg></a>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($this->countModules( 'breadcrumbs' )) { ?>
                        <div class="uk-width-1-1 uk-width-expand@m uk-text-small uk-flex uk-flex-left uk-visible@m"><jdoc:include type="modules" name="breadcrumbs" style="xhtml" /></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-padding uk-padding-remove-horizontal uk-padding-remove-top footer bgDark">
        <div class="uk-container">
            <div data-uk-grid>
                <div class="uk-width-1-1 uk-width-1-3@m">
                    <div>
                        <div class="uk-padding bgWhite footerContact">
                            <div>
                                <div class="uk-child-width-1-1 uk-grid-medium" data-uk-grid>
                                    <?php if (!empty($params->get('address')) || !empty($params->get('phone')) || !empty($params->get('fax')) || !empty($params->get('cellphone')) || !empty($params->get('email'))) { ?>
                                        <div>
                                            <div>
                                                <div class="uk-child-width-1-1 uk-grid-divider uk-grid-small" data-uk-grid>
                                                    <?php if (!empty($params->get('address'))) { ?>
                                                        <div>
                                                            <div>
                                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                                    <div class="uk-width-auto uk-text-secondary"><img src="<?php echo JURI::base().'images/sprite.svg#map' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                    <div class="uk-width-expand"><span class="uk-text-small uk-text-dark value font"><?php echo $params->get('address'); ?></span></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($params->get('phone')) || !empty($params->get('fax')) || !empty($params->get('cellphone'))) { ?>
                                                        <div>
                                                            <div>
                                                                <div class="uk-child-width-1-2" data-uk-grid>
                                                                    <?php if (!empty($params->get('phone'))) { ?>
                                                                        <div>
                                                                            <div>
                                                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                                                    <div class="uk-width-auto uk-text-secondary"><img src="<?php echo JURI::base().'images/sprite.svg#phone' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                                    <div class="uk-width-expand"><span class="uk-text-small uk-text-dark value font"><?php echo nl2br($params->get('phone')); ?></span></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <?php if (!empty($params->get('fax')) || !empty($params->get('cellphone'))) { ?>
                                                                        <div>
                                                                            <div>
                                                                                <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid>
                                                                                    <?php if (!empty($params->get('fax'))) { ?>
                                                                                        <div>
                                                                                            <div class="uk-grid-small contactFields" data-uk-grid>
                                                                                                <div class="uk-width-auto uk-text-secondary"><img src="<?php echo JURI::base().'images/sprite.svg#fax' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                                                <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-dark value font"><?php echo $params->get('fax'); ?></span></div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php } ?>
                                                                                    <?php if (!empty($params->get('cellphone'))) { ?>
                                                                                        <div>
                                                                                            <div class="uk-grid-small contactFields" data-uk-grid>
                                                                                                <div class="uk-width-auto uk-text-secondary"><img src="<?php echo JURI::base().'images/sprite.svg#mobile' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                                                <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-dark value font"><?php echo $params->get('cellphone'); ?></span></div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($params->get('email'))) { ?>
                                                        <div>
                                                            <div>
                                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                                    <div class="uk-width-auto uk-text-secondary"><img src="<?php echo JURI::base().'images/sprite.svg#envelope' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                    <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-dark value font"><?php echo $params->get('email'); ?></span></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-1 uk-width-2-3@m">
                    <div>
                        <div class="uk-padding uk-padding-remove-horizontal uk-padding-remove-bottom modulesWrapper">
                            <div>
                                <div class="uk-child-width-1-1 uk-child-width-1-2@m" data-uk-grid>
                                    <jdoc:include type="modules" name="footer" style="xhtml" />
                                    <div class="uk-flex uk-flex-column uk-flex-between">
                                        <div class="uk-padding-small uk-text-center uk-border-rounded uk-box-shadow-small uk-margin-bottom uk-background-white">
                                            <div class="uk-child-width-1-3 uk-grid-small" data-uk-grid>
                                                <div class="uk-flex uk-flex-middle uk-flex-center"><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQwIiBoZWlnaHQ9IjM2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KCTxwYXRoIGQ9Im0xMjAgMjQzbDk0LTU0IDAtMTA5IC05NCA1NCAwIDEwOSAwIDB6IiBmaWxsPSIjODA4Mjg1Ii8+Cgk8cGF0aCBkPSJtMTIwIDI1NGwtMTAzLTYwIDAtMTE5IDEwMy02MCAxMDMgNjAgMCAxMTkgLTEwMyA2MHoiIHN0eWxlPSJmaWxsOm5vbmU7c3Ryb2tlLWxpbmVqb2luOnJvdW5kO3N0cm9rZS13aWR0aDo1O3N0cm9rZTojMDBhZWVmIi8+Cgk8cGF0aCBkPSJtMjE0IDgwbC05NC01NCAtOTQgNTQgOTQgNTQgOTQtNTR6IiBmaWxsPSIjMDBhZWVmIi8+Cgk8cGF0aCBkPSJtMjYgODBsMCAxMDkgOTQgNTQgMC0xMDkgLTk0LTU0IDAgMHoiIGZpbGw9IiM1ODU5NWIiLz4KCTxwYXRoIGQ9Im0xMjAgMTU3bDQ3LTI3IDAtMjMgLTQ3LTI3IC00NyAyNyAwIDU0IDQ3IDI3IDQ3LTI3IiBzdHlsZT0iZmlsbDpub25lO3N0cm9rZS1saW5lY2FwOnJvdW5kO3N0cm9rZS1saW5lam9pbjpyb3VuZDtzdHJva2Utd2lkdGg6MTU7c3Ryb2tlOiNmZmYiLz4KCTx0ZXh0IHg9IjE1IiB5PSIzMDAiIGZvbnQtc2l6ZT0iMjVweCIgZm9udC1mYW1pbHk9IidCIFlla2FuJyIgc3R5bGU9ImZpbGw6IzI5Mjk1Mjtmb250LXdlaWdodDpib2xkIj7Yudi22Ygg2KfYqtit2KfYr9uM2Ycg2qnYtNmI2LHbjDwvdGV4dD4KCTx0ZXh0IHg9IjgiIHk9IjM0MyIgZm9udC1zaXplPSIyNXB4IiBmb250LWZhbWlseT0iJ0IgWWVrYW4nIiBzdHlsZT0iZmlsbDojMjkyOTUyO2ZvbnQtd2VpZ2h0OmJvbGQiPtqp2LPYqCDZiCDaqdin2LHZh9in24wg2YXYrNin2LLbjDwvdGV4dD4KPC9zdmc+ " alt="" width="125" height="136" onclick="window.open('https://ecunion.ir/verify/orgbattery.com?token=76957855f6f40d86f024', 'Popup','toolbar=no, location=no, statusbar=no, menubar=no, scrollbars=1, resizable=0, width=580, height=600, top=30')" style="cursor:pointer; width: 96px;height: 144px;"></div>
                                                <div class="uk-flex uk-flex-middle uk-flex-center"><a referrerpolicy="origin" target="_blank" href="https://trustseal.enamad.ir/?id=134691&amp;Code=TWmhKGm61cBM9seD52S4"><img referrerpolicy="origin" src="https://Trustseal.eNamad.ir/logo.aspx?id=134691&amp;Code=TWmhKGm61cBM9seD52S4" alt="" style="cursor:pointer" width="125" height="136" id="TWmhKGm61cBM9seD52S4"></a></div>
                                                <div class="uk-flex uk-flex-middle uk-flex-center"><img referrerpolicy="origin" id = 'jxlzfukzapfujxlzjxlzwlao' style = 'cursor:pointer' onclick = 'window.open("https://logo.samandehi.ir/Verify.aspx?id=165114&p=rfthgvkadshwrfthrfthaods", "Popup","toolbar=no, scrollbars=no, location=no, statusbar=no, menubar=no, resizable=0, width=450, height=630, top=30")' alt = 'logo-samandehi'  width='125' height='136' src = 'https://logo.samandehi.ir/logo.aspx?id=165114&p=nbpdwlbqujynnbpdnbpdshwl' /></div>
                                            </div>
                                        </div>
                                        <div class="uk-text-left uk-visible@m">
                                            <a class="transition uk-flex uk-flex-middle uk-flex-left font goToTop" href="#header" data-uk-scroll>
                                                <span><img src="<?php echo JURI::base().'images/sprite.svg#chevron-circle-up' ?>" width="12" height="12" alt="" data-uk-svg></span>
                                                <span class="uk-text-tiny">&ensp;<?php echo JTEXT::_('GOTOTOP'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright bgDark">
        <div class="uk-container">
            <div class="uk-padding-small uk-padding-remove-horizontal uk-padding-remove-top">
                <div class="uk-grid-small uk-text-white uk-text-center uk-text-<?php echo JFactory::getLanguage()->isRtl() ? 'right' : 'left' ?>@m font" data-uk-grid>
                    <div class="uk-width-1-1 uk-width-expand@m">
                        <p class="uk-text-small f500"><?php echo JTEXT::sprintf('COPYRIGHT', $sitename); ?></p>
                    </div>
                    <div class="uk-width-1-1 uk-width-auto@m">
                        <p class="uk-text-small f500"><?php echo JTEXT::sprintf('DESIGNER', '<a href="https://netparsi.com" class="uk-text-white hoverAccent" target="_blank" title="'.$netparsi.'">'.$netparsi.'</a>'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php } ?>
<?php if ($pageclass == 'checkout') { ?>
    <footer class="uk-text-zero uk-padding-small uk-padding-remove-horizontal">
        <div class="copyright">
            <div class="uk-container">
                <div class="uk-padding-small uk-padding-remove-horizontal uk-padding-remove-top">
                    <div class="uk-grid-small uk-text-white uk-text-center font" data-uk-grid>
                        <div class="uk-width-1-1">
                            <div>
                                <div class="uk-child-width-auto uk-grid-small uk-flex-center uk-grid-divider" data-uk-grid>
                                    <?php if (!empty($params->get('phone'))) { ?>
                                        <div>
                                            <div>
                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                    <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#phone' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                    <div class="uk-width-expand"><span class="uk-text-small uk-text-gray value font"><?php $phones = explode("\n", $params->get('phone')); echo $phones[0]; ?></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($params->get('email'))) { ?>
                                        <div>
                                            <div>
                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                    <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#envelope' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                    <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-gray value font"><?php echo $params->get('email'); ?></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($params->get('cellphone'))) { ?>
                                        <div>
                                            <div class="uk-grid-small contactFields" data-uk-grid>
                                                <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#mobile' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-gray value font"><?php echo $params->get('cellphone'); ?></span></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-1-1">
                            <p class="uk-text-small uk-text-gray f500"><?php echo JTEXT::sprintf('COPYRIGHT', $sitename); ?></p>
                        </div>
                        <div class="uk-width-1-1">
                            <p class="uk-text-small uk-text-gray f500 uk-margin-bottom"><?php echo JTEXT::sprintf('DESIGNER', '<a href="https://netparsi.com" class="uk-text-gray hoverAccent" target="_blank" title="'.$netparsi.'">'.$netparsi.'</a>'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<?php } ?>
<div id="hamMenu" data-uk-offcanvas="overlay: true">
    <div class="uk-offcanvas-bar uk-card uk-card-default uk-padding-remove bgWhite">
        <div class="uk-flex uk-flex-column uk-height-1-1">
            <div class="uk-width-expand">
                <div class="offcanvasTop uk-box-shadow-small uk-position-relative uk-flex-stretch">
                    <div class="uk-grid-collapse uk-height-1-1" data-uk-grid>
                        <div class="uk-flex uk-width-1-4 uk-flex uk-flex-center uk-flex-middle"><a onclick="UIkit.offcanvas('#hamMenu').hide();" class="uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-width-1-1 uk-margin-remove"><img src="<?php echo JURI::base().'images/sprite.svg#chevron-right'; ?>" width="24" height="24" alt="Close" data-uk-svg></a></div>
                        <div class="uk-flex uk-width-expand uk-flex uk-flex-right uk-flex-middle uk-text-white"><img src="<?php echo JUri::base().'images/sprite.svg#narin' ?>" width="75" height="40" alt="<?php echo $sitename; ?>" data-uk-svg></div>
                    </div>
                </div>
                <div class="uk-padding-small"><jdoc:include type="modules" name="offcanvas" style="xhtml" /></div>
            </div>
        </div>
    </div>
</div>
<div id="userMenu" data-uk-offcanvas="overlay: true">
    <div class="uk-offcanvas-bar uk-card uk-card-default uk-padding-remove bgWhite">
        <div class="uk-flex uk-flex-column uk-height-1-1">
            <div class="uk-width-expand">
                <div class="offcanvasTop uk-box-shadow-small uk-position-relative uk-flex-stretch">
                    <div class="uk-grid-collapse uk-height-1-1" data-uk-grid>
                        <div class="uk-flex uk-width-1-4 uk-flex uk-flex-center uk-flex-middle"><a onclick="UIkit.offcanvas('#userMenu').hide();" class="uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-width-1-1 uk-margin-remove"><img src="<?php echo JURI::base().'images/sprite.svg#chevron-right'; ?>" width="24" height="24" alt="Close" data-uk-svg></a></div>
                        <div class="uk-flex uk-width-expand uk-flex uk-flex-right uk-flex-middle uk-text-white"><img src="<?php echo JUri::base().'images/sprite.svg#narin' ?>" width="75" height="40" alt="<?php echo $sitename; ?>" data-uk-svg></div>
                    </div>
                </div>
                <div class="uk-padding-small"><jdoc:include type="modules" name="offcanvasuser" style="xhtml" /></div>
            </div>
        </div>
    </div>
</div>
<div class="uk-position-fixed uk-position-top-left uk-width-1-1 uk-height-1-1 uk-visible@m pageCover" id="pageCover"></div>
</body>
</html>












خرید باتری لپ تاپ Asus  GL752VLM