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
JHtml::_('script', 'fancyTable.min.js', array('version' => 'auto', 'relative' => true));
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
    <?php if (in_array(10, $user->get('groups'))) { ?>
        <?php
        $db = JFactory::getDbo();
        /* Latest Orders Query */
        $lastXOrders = $db->getQuery(true);
        $lastXOrders
            ->select($db->quoteName(array('id','id_employee', 'id_service', 'checkin_ts', 'duration', 'status', 'sid', 'conf_key', 'createdby', 'createdon', 'custom_f')))
            ->from($db->quoteName('#__vikappointments_reservation'))
            ->where($db->quoteName('id_service') . ' = ' . 1)
            ->order('id DESC');
        $orders = $db->setQuery($lastXOrders)->loadObjectList();
        ?>
        <div class="uk-padding">
            <?php if (count($orders)) { ?>
                <div class="uk-margin-bottom">
                    <div class="uk-grid-medium uk-flex-center uk-flex-right@m" data-uk-grid>
                        <div class="uk-width-expand">
                            <h2 class="uk-margin-remove uk-h3 uk-text-secondary font f600"><?php echo JText::sprintf('ALL_PARKINGS_RESERVES'); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="summeryTable">
                    <table class="uk-table uk-table-middle uk-table-divider uk-table-responsive uk-margin-remove" id="dataTables">
                        <thead>
                        <tr>
                            <th class="uk-width-small"></th>
                            <th class="uk-width-small uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('NAME'); ?></th>
                            <th class="uk-width-medium uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('POSITION'); ?></th>
                            <th class="uk-width-small uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('FLOOR'); ?></th>
                            <th class="uk-width-medium uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('ENTRANCE_DATE'); ?></th>
                            <th class="uk-table-expand uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('PLATE_NUMBER'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php for ($k=0;$k<count($orders);$k++) { ?>
                            <?php if($orders[$k]->id_parent != -1) { ?>
                                <?php
                                $date = new JDate($orders[$k]->checkin_ts);
                                $date->toUnix();
                                $now =  new JDate('now');

                                $empDetailsQuery = $db->getQuery(true);
                                $empDetailsQuery
                                    ->select($db->quoteName(array('id', 'firstname', 'lastname', 'nickname')))
                                    ->from($db->quoteName('#__vikappointments_employee'))
                                    ->where($db->quoteName('id') . ' = ' . $orders[$k]->id_employee);
                                $empDetails = $db->setQuery($empDetailsQuery)->loadObject();

                                if ($orders[$k]->id_service == 1) {$cat = 'PARKINGS';} elseif ($orders[$k]->id_service == 5) {$cat = 'TABLES';}
                                ?>
                                <tr class="<?php echo ($date < $now) ? 'uk-background-muted' : ''; ?>">
                                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-gray' : 'uk-text-secondary'; ?> uk-visible@m"><img src="<?php echo JUri::base().'images/sprite.svg#'.$cat; ?>" width="36" height="36" data-uk-svg></td>
                                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('NAME').'&ensp;:&ensp;'; ?></span>
                                        <span class="uk-text-small fnum font"><?php $karmand = JFactory::getUser($orders[$k]->createdby); echo $karmand->get('name'); ?></span>
                                    </td>
                                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('POSITION').'&ensp;:&ensp;'; ?></span>
                                        <span class="uk-text-small fnum font"><?php echo $empDetails->firstname.' '.$empDetails->lastname; ?></span>
                                    </td>
                                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('FLOOR').'&ensp;:&ensp;'; ?></span>
                                        <span class="uk-text-small fnum font"><?php echo JText::sprintf('FLOOR'.$empDetails->nickname); ?></span>
                                    </td>
                                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('ENTRANCE_DATE').'&ensp;:&ensp;'; ?></span>
                                        <span class="uk-text-small fnum font"><?php echo JHTML::date($orders[$k]->checkin_ts, 'D ، d M Y'); ?></span>
                                    </td>
                                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('ENTRANCE_TIME').'&ensp;:&ensp;'; ?></span>
                                        <span class="uk-text-small fnum font <?php echo $date < $now ? 'uk-text-muted' : 'uk-text-secondary'; ?>"><?php echo json_decode($orders[$k]->custom_f)->plate_area; ?></span>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <?php $now = strtotime(new JDate('now')); ?>
        <?php $db = JFactory::getDbo(); ?>
        <?php
        $awaitingParkingsQuery = $db->getQuery(true);
        $awaitingParkingsQuery
            ->select($db->quoteName(array('id','id_employee', 'checkin_ts', 'duration', 'status', 'sid', 'conf_key', 'id_parent', 'id_service', 'createdby')))
            ->from($db->quoteName('#__vikappointments_reservation'))
            ->where($db->quoteName('createdby') . ' = ' . JFactory::getUser()->id)
            ->where($db->quoteName('id_parent') . ' != ' . -1)
            ->where($db->quoteName('id_service') . ' = ' . 1)
            ->where($db->quoteName('status') . ' LIKE ' . $db->quote('CONFIRMED'))
            ->where($db->quoteName('checkin_ts') . ' > ' . $now);
        $awaitingParkings = $db->setQuery($awaitingParkingsQuery)->loadObjectList();
        ?>
        <?php
        $awaitingTablesQuery = $db->getQuery(true);
        $awaitingTablesQuery
            ->select($db->quoteName(array('id','id_employee', 'checkin_ts', 'duration', 'status', 'sid', 'conf_key', 'id_parent', 'id_service', 'createdby')))
            ->from($db->quoteName('#__vikappointments_reservation'))
            ->where($db->quoteName('createdby') . ' = ' . JFactory::getUser()->id)
            ->where($db->quoteName('id_parent') . ' != ' . -1)
            ->where($db->quoteName('id_service') . ' = ' . 5)
            ->where($db->quoteName('status') . ' LIKE ' . $db->quote('CONFIRMED'))
            ->where($db->quoteName('checkin_ts') . ' > ' . $now);
        $awaitingTables = $db->setQuery($awaitingTablesQuery)->loadObjectList();
        $tablesMinutesSum = 0;
        for ($c=0;$c<count($awaitingTables);$c++) {
            $tablesMinutesSum += $awaitingTables[$c]->duration;
        }
        function convert($hours, $minutes) {
            return $hours + round($minutes / 60, 2);
        }
        ?>
        <div class="uk-grid-collapse" data-uk-grid>
            <div class="uk-width-1-1 uk-width-medium@m uk-visible@m">
                <aside class="uk-background-primary uk-position-fixed uk-width-medium uk-height-viewport uk-padding">
                <div class="uk-text-center uk-margin-medium-bottom">
                    <a href="<?php echo JUri::base(); ?>" target="_self" class="uk-display-inline-block">
                        <img src="<?php echo JUri::base().'images/sprite.svg#logo'; ?>" alt="DigiKala" width="212" height="50" data-uk-svg>
                    </a>
                    <span class="uk-text-muted uk-display-block uk-margin-small-top font"><?php echo JText::sprintf('RESERVE_SYSTEM'); ?></span>
                </div>
                <div class="uk-margin-medium-bottom uk-text-white">
                    <div class="limitsWrapper">
                        <div>
                            <div class="uk-child-width-1-1" data-uk-grid>
                                <!-- Parkings Credit -->
                                <div>
                                    <div>
                                        <div class="uk-grid-small" data-uk-grid>
                                            <div class="uk-width-expand uk-flex uk-flex-bottom">
                                                <h5 class="uk-margin-remove uk-text-tiny uk-text-white uk-text-small font"><?php echo JTEXT::_('CREDIT_PARKINGS'); ?></h5>
                                            </div>
                                            <div class="uk-width-1-4 uk-flex uk-flex-middle">
                                                <img src="<?php echo JURI::base().'images/sprite.svg#PARKINGS'; ?>" data-uk-svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-margin-small-top uk-margin-remove-bottom">
                                        <progress class="uk-progress <?php echo count($awaitingParkings) >= $params->get('limitparkings') ? 'danger' : 'success'; ?>" value="<?php echo count($awaitingParkings); ?>" max="<?php echo $params->get('limitparkings'); ?>"></progress>
                                    </div>
                                    <div>
                                        <div class="uk-grid-small" data-uk-grid>
                                            <div class="uk-width-expand uk-flex uk-flex-bottom">
                                                <h6 class="uk-margin-remove uk-text-small uk-h6 uk-text-<?php echo count($awaitingParkings) >= $params->get('limitparkings') ? 'danger' : 'success'; ?> font"><?php echo count($awaitingParkings) >= $params->get('limitparkings') ? JTEXT::_('NOT_ELIGIBLE') : JTEXT::_('ELIGIBLE'); ?></h6>
                                            </div>
                                            <div class="uk-width-auto uk-flex uk-flex-middle">
                                                <span class="uk-margin-remove uk-text-small uk-h6 uk-text-<?php echo count($awaitingParkings) >= $params->get('limitparkings') ? 'danger' : 'success'; ?> font fnum"><?php echo $params->get('limitparkings').' / '.count($awaitingParkings).' '.JTEXT::_('DAY'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tables Credit -->
                                <div>
                                    <div>
                                        <div class="uk-grid-small" data-uk-grid>
                                            <div class="uk-width-expand uk-flex uk-flex-bottom">
                                                <h5 class="uk-margin-remove uk-text-tiny uk-text-white font"><?php echo JTEXT::_('CREDIT_TABLES'); ?></h5>
                                            </div>
                                            <div class="uk-width-1-4 uk-flex uk-flex-middle">
                                                <img src="<?php echo JURI::base().'images/sprite.svg#TABLES'; ?>" data-uk-svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-margin-small-top uk-margin-remove-bottom">
                                        <progress class="uk-progress <?php echo convert(0, $tablesMinutesSum) >= $params->get('limittables') ? 'danger' : 'success'; ?>" value="<?php echo convert(0, $tablesMinutesSum); ?>" max="<?php echo $params->get('limittables'); ?>"></progress>
                                    </div>
                                    <div>
                                        <div class="uk-grid-small" data-uk-grid>
                                            <div class="uk-width-expand uk-flex uk-flex-bottom">
                                                <h6 class="uk-margin-remove uk-text-small uk-h6 uk-text-<?php echo convert(0, $tablesMinutesSum) >= $params->get('limittables') ? 'danger' : 'success'; ?> font"><?php echo convert(0, $tablesMinutesSum) >= $params->get('limittables') ? JTEXT::_('NOT_ELIGIBLE') : JTEXT::_('ELIGIBLE'); ?></h6>
                                            </div>
                                            <div class="uk-width-auto uk-flex uk-flex-middle">
                                                <span class="uk-margin-remove uk-text-small uk-h6 uk-text-<?php echo convert(0, $tablesMinutesSum) >= $params->get('limittables') ? 'danger' : 'success'; ?> font fnum"><?php echo $params->get('limittables').' / '.convert(0, $tablesMinutesSum).' '.JTEXT::_('HOUR'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div><jdoc:include type="modules" name="aside" style="xhtml" /></div>
                <div class="uk-padding-small uk-position-absolute uk-text-zero sideFooter">
                    <div class="uk-grid-small" data-uk-grid>
                        <?php if ($params->get('showreportlink') || $params->get('showversion')) { ?>
                            <div class="uk-width-expand">
                                <?php if ($params->get('showreportlink')) { ?>
                                    <a href="<?php echo $params->get('reportlink'); ?>" class="uk-text-tiny uk-text-white uk-margin-small-left uk-position-relative font errorReport" target="_blank"><?php echo JText::sprintf('REPORT_AN_ERROR'); ?></a>
                                <?php } ?>
                                <?php if ($params->get('showversion')) { ?>
                                    <span class="uk-text-tiny uk-text-gray font"><?php echo $params->get('verson'); ?></span>
                                <?php } ?>
                            </div>
                        <?php } ?>
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
                            <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-left">
                                <a href="<?php echo JUri::base(); ?>" target="_self" class="uk-display-inline-block">
                                    <img src="<?php echo JUri::base().'images/sprite.svg#logo'; ?>" alt="DigiKala" width="150" data-uk-svg />
                                </a>
                            </div>
                            <div class="uk-width-auto uk-hidden">
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
                <div class="limitsWrapper">
                    <div>
                        <div class="uk-child-width-1-1" data-uk-grid>
                            <!-- Parkings Credit -->
                            <div>
                                <div>
                                    <div class="uk-grid-small" data-uk-grid>
                                        <div class="uk-width-expand uk-flex uk-flex-bottom">
                                            <h5 class="uk-margin-remove uk-text-tiny uk-text-white uk-text-small font"><?php echo JTEXT::_('CREDIT_PARKINGS'); ?></h5>
                                        </div>
                                        <div class="uk-width-1-4 uk-flex uk-flex-middle">
                                            <img src="<?php echo JURI::base().'images/sprite.svg#PARKINGS'; ?>" data-uk-svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-margin-small-top uk-margin-remove-bottom">
                                    <progress class="uk-progress <?php echo count($awaitingParkings) >= $params->get('limitparkings') ? 'danger' : 'success'; ?>" value="<?php echo count($awaitingParkings); ?>" max="<?php echo $params->get('limitparkings'); ?>"></progress>
                                </div>
                                <div>
                                    <div class="uk-grid-small" data-uk-grid>
                                        <div class="uk-width-expand uk-flex uk-flex-bottom">
                                            <h6 class="uk-margin-remove uk-text-small uk-h6 uk-text-<?php echo count($awaitingParkings) >= $params->get('limitparkings') ? 'danger' : 'success'; ?> font"><?php echo count($awaitingParkings) >= $params->get('limitparkings') ? JTEXT::_('NOT_ELIGIBLE') : JTEXT::_('ELIGIBLE'); ?></h6>
                                        </div>
                                        <div class="uk-width-auto uk-flex uk-flex-middle">
                                            <span class="uk-margin-remove uk-text-small uk-h6 uk-text-<?php echo count($awaitingParkings) >= $params->get('limitparkings') ? 'danger' : 'success'; ?> font fnum"><?php echo $params->get('limitparkings').' / '.count($awaitingParkings).' '.JTEXT::_('DAY'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tables Credit -->
                            <div>
                                <div>
                                    <div class="uk-grid-small" data-uk-grid>
                                        <div class="uk-width-expand uk-flex uk-flex-bottom">
                                            <h5 class="uk-margin-remove uk-text-tiny uk-text-white font"><?php echo JTEXT::_('CREDIT_TABLES'); ?></h5>
                                        </div>
                                        <div class="uk-width-1-4 uk-flex uk-flex-middle">
                                            <img src="<?php echo JURI::base().'images/sprite.svg#TABLES'; ?>" data-uk-svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-margin-small-top uk-margin-remove-bottom">
                                    <progress class="uk-progress <?php echo convert(0, $tablesMinutesSum) >= $params->get('limittables') ? 'danger' : 'success'; ?>" value="<?php echo convert(0, $tablesMinutesSum); ?>" max="<?php echo $params->get('limittables'); ?>"></progress>
                                </div>
                                <div>
                                    <div class="uk-grid-small" data-uk-grid>
                                        <div class="uk-width-expand uk-flex uk-flex-bottom">
                                            <h6 class="uk-margin-remove uk-text-small uk-h6 uk-text-<?php echo convert(0, $tablesMinutesSum) >= $params->get('limittables') ? 'danger' : 'success'; ?> font"><?php echo convert(0, $tablesMinutesSum) >= $params->get('limittables') ? JTEXT::_('NOT_ELIGIBLE') : JTEXT::_('ELIGIBLE'); ?></h6>
                                        </div>
                                        <div class="uk-width-auto uk-flex uk-flex-middle">
                                            <span class="uk-margin-remove uk-text-small uk-h6 uk-text-<?php echo convert(0, $tablesMinutesSum) >= $params->get('limittables') ? 'danger' : 'success'; ?> font fnum"><?php echo $params->get('limittables').' / '.convert(0, $tablesMinutesSum).' '.JTEXT::_('HOUR'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div><jdoc:include type="modules" name="aside" style="xhtml" /></div>
            <div class="uk-padding-small uk-position-absolute uk-text-zero sideFooter">
                <div class="uk-grid-small" data-uk-grid>
                    <?php if ($params->get('showreportlink') || $params->get('showversion')) { ?>
                        <div class="uk-width-expand">
                            <?php if ($params->get('showreportlink')) { ?>
                                <a href="<?php echo $params->get('reportlink'); ?>" class="uk-text-tiny uk-text-white uk-margin-small-left uk-position-relative font errorReport" target="_blank"><?php echo JText::sprintf('REPORT_AN_ERROR'); ?></a>
                            <?php } ?>
                            <?php if ($params->get('showversion')) { ?>
                                <span class="uk-text-tiny uk-text-gray font"><?php echo $params->get('verson'); ?></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="uk-width-auto uk-flex uk-flex-bottom">
                        <a href="https://uxdee.org" rel="nofollow" class="uk-display-inline-block uxdee" target="_blank"><img src="<?php echo JUri::base().'images/uxdee.svg'; ?>" width="60"></a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
    <?php } ?>
<?php } ?>
</body>
</html>