<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$db = JFactory::getDbo();
/* Latest Orders Query */
$lastXOrders = $db->getQuery(true);
$lastXOrders
    ->select($db->quoteName(array('id','id_employee', 'id_service', 'checkin_ts', 'duration', 'status', 'sid', 'conf_key', 'createdby', 'createdon')))
    ->from($db->quoteName('#__vikappointments_reservation'))
    ->where($db->quoteName('createdby') . ' = ' . JFactory::getUser()->id)
    ->order('id DESC')
    ->setLimit('3');
$orders = $db->setQuery($lastXOrders)->loadObjectList();

/* Count Sluts */
$countParkingSluts = $db->getQuery(true);
$countParkingSluts
    ->select($db->quoteName(array('id_group')))
    ->from($db->quoteName('#__vikappointments_employee'))
    ->where($db->quoteName('id_group') . ' = ' . 1);
$parkings = $db->setQuery($countParkingSluts)->loadObjectList();

/* Count Rooms */
$countRoomsSluts = $db->getQuery(true);
$countRoomsSluts
    ->select($db->quoteName(array('id_group')))
    ->from($db->quoteName('#__vikappointments_employee'))
    ->where($db->quoteName('id_group') . ' = ' . 6);
$rooms = $db->setQuery($countRoomsSluts)->loadObjectList();

/* Count Tables */
$countTablesSluts = $db->getQuery(true);
$countTablesSluts
    ->select($db->quoteName(array('id_group')))
    ->from($db->quoteName('#__vikappointments_employee'))
    ->where($db->quoteName('id_group') . ' = ' . 5);
$tables = $db->setQuery($countTablesSluts)->loadObjectList();
?>
<div class="profile<?php echo $this->pageclass_sfx; ?>">
    <div class="uk-background-secondary uk-padding-large uk-flex uk-flex-middle uk-flex-center uk-flex-right@m">
        <?php if ($this->params->get('show_page_heading')) : ?>
            <div class="page-header">
                <h1 class="font uk-text-white uk-h2 f500 uk-margin-remove uk-text-center uk-text-right@m"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
            </div>
        <?php endif; ?>
    </div>
    <div class="uk-padding-large uk-padding-remove-top uk-padding-remove-bottom uk-position-relative">
        <div class="dashboardBoxWrapper">
            <div data-uk-slider>
                <div class="uk-position-relative">
                    <ul class="uk-slider-items uk-child-width-1-1 uk-child-width-1-3@m uk-grid uk-grid-large">
                        <li>
                            <div class="uk-background-white uk-margin-bottom">
                                <div class="summeryTable">
                                    <div class="uk-padding">
                                        <div>
                                            <div class="uk-child-width-1-2 uk-grid-small" data-uk-grid>
                                                <div class="uk-flex uk-flex-center uk-flex-middle uk-text-center uk-text-primary">
                                                    <div><img src="<?php echo JUri::base().'images/sprite.svg#PARKINGS'; ?>" data-uk-svg></div>
                                                </div>
                                                <div class="uk-flex uk-flex-center uk-flex-middle uk-text-center">
                                                    <div>
                                                        <div class="uk-h1 uk-text-primary uk-margin-remove font uk-text-bold fnum"><?php echo count($parkings); ?></div>
                                                        <div class="uk-text-small uk-text-secondary font"><?php echo JText::sprintf('ALL_PARKINGS'); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeeslist&employee_group=1'); ?>" class="uk-button uk-button-primary uk-width-1-1 uk-button-large font"><?php echo JText::sprintf('PARKING_RESERVE'); ?>&ensp;<img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" width="24" height="24" alt="" data-uk-svg></a>
                            </div>
                        </li>
                        <li>
                            <div class="uk-background-white uk-margin-bottom">
                                <div class="summeryTable">
                                    <div class="uk-padding">
                                        <div>
                                            <div class="uk-child-width-1-2 uk-grid-small" data-uk-grid>
                                                <div class="uk-flex uk-flex-center uk-flex-middle uk-text-center uk-text-primary">
                                                    <div><img src="<?php echo JUri::base().'images/sprite.svg#MEETING_ROOMS'; ?>" data-uk-svg></div>
                                                </div>
                                                <div class="uk-flex uk-flex-center uk-flex-middle uk-text-center">
                                                    <div>
                                                        <div class="uk-h1 uk-text-primary uk-margin-remove font uk-text-bold fnum"><?php echo count($rooms); ?></div>
                                                        <div class="uk-text-small uk-text-secondary font"><?php echo JText::sprintf('ALL_MEETING_ROOMS'); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeeslist&employee_group=6'); ?>" class="uk-button uk-button-primary uk-width-1-1 uk-button-large font"><?php echo JText::sprintf('SEE_MEETIBG_ROOMS'); ?>&ensp;<img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" width="24" height="24" alt="" data-uk-svg></a>
                            </div>
                        </li>
                        <li>
                            <div class="uk-background-white uk-margin-bottom">
                                <div class="summeryTable">
                                    <div class="uk-padding">
                                        <div>
                                            <div class="uk-child-width-1-2 uk-grid-small" data-uk-grid>
                                                <div class="uk-flex uk-flex-center uk-flex-middle uk-text-center uk-text-primary">
                                                    <div><img src="<?php echo JUri::base().'images/sprite.svg#TABLES'; ?>" data-uk-svg></div>
                                                </div>
                                                <div class="uk-flex uk-flex-center uk-flex-middle uk-text-center">
                                                    <div>
                                                        <div class="uk-h1 uk-text-primary uk-margin-remove font uk-text-bold fnum"><?php echo count($tables); ?></div>
                                                        <div class="uk-text-small uk-text-secondary font"><?php echo JText::sprintf('ALL_TABLES'); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeeslist&employee_group=5'); ?>" class="uk-button uk-button-primary uk-width-1-1 uk-button-large font"><?php echo JText::sprintf('TABLES_RESERVE'); ?>&ensp;<img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" width="24" height="24" alt="" data-uk-svg></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
            </div>
        </div>
    </div>
    <div class="uk-padding-large">
        <?php if (count($orders)) { ?>
            <div class="uk-margin-bottom">
                <div class="uk-grid-medium uk-flex-center uk-flex-right@m" data-uk-grid>
                    <div class="uk-width-auto">
                        <h2 class="uk-margin-remove uk-h3 uk-text-secondary font f600"><?php echo JText::sprintf('RESERVES_RECENT'); ?></h2>
                    </div>
                    <div class="uk-width-auto uk-text-success uk-flex uk-flex-middle uk-visible@m">
                        <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders'); ?>" class="uk-flex uk-flex-middle uk-link-reset font f500">
                            <span class="uk-margin-small-left"><?php echo JText::sprintf('ALL_MY_RESERVES'); ?></span>
                            <span><img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" data-uk-svg></span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="summeryTable">
                <div class="uk-padding-small uk-visible@m">
                    <div class="uk-padding-small uk-padding-remove-vertical removePaddingOnTouch">
                        <div class="uk-grid-small" data-uk-grid>
                            <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small">&ensp;</div>
                            <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-expand"><?php echo JText::sprintf('ORDER_ID'); ?></div>
                            <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('RESERVE_KEY'); ?></div>
                            <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('RESERVE_DATE'); ?></div>
                            <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small"><?php echo JText::sprintf('RESERVE_TYPE'); ?></div>
                            <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('STATUS'); ?></div>
                            <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6">&emsp;</div>
                        </div>
                    </div>
                </div>
                <?php for ($k=0;$k<count($orders);$k++) { ?>
                    <?php
                    $date = new JDate($orders[$k]->checkin_ts);
                    $date->toUnix();
                    $now =  new JDate('now');
                    ?>
                    <div class="<?php echo $orders[$k]->id_parent != -1 ? 'main' : 'parent'; ?> <?php echo empty($orders[$k]->child) ? '' : 'childItem'; ?>" <?php echo empty($orders[$k]->child) ? '' : 'style="display:none;"'; ?>>
                        <hr class="uk-margin-remove">
                        <div class="<?php if (!empty($orders[$k]->child)) {echo '';} ?>">
                            <div class="uk-padding-small <?php if($date < $now && $orders[$k]->id_parent != -1) { echo 'uk-background-muted'; } ?>">
                                <div class="uk-padding-small uk-padding-remove-vertical removePaddingOnTouch">
                                    <div class="uk-grid-small" data-uk-grid>
                                        <div class="uk-width-small uk-flex uk-flex-middle uk-flex-center uk-visible@m">
                                            <div class="uk-width-2-5 uk-height-1-1 uk-flex uk-flex-middle uk-flex-center rowIcon  <?php echo ($date < $now && $orders[$k]->id_parent != -1) ? 'uk-text-gray' : 'uk-text-secondary'; ?>">
                                                <img src="<?php echo JUri::base().'images/sprite.svg#receipt-cutoff'; ?>" width="36" height="36" data-uk-svg>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-1 uk-width-expand@m uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('ORDER_ID').'&ensp;:&ensp;'; ?></span>
                                            <span class="uk-text-small fnum font uk-position-relative <?php echo ($date < $now && $orders[$k]->id_parent != -1) ? 'uk-text-muted' : 'uk-text-secondary'; ?>"><?php echo $orders[$k]->id+100000; ?></span>
                                        </div>
                                        <div class="uk-width-1-1 uk-width-1-6@m uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('RESERVE_KEY').'&ensp;:&ensp;'; ?></span>
                                            <span class="uk-text-small font uk-position-relative <?php echo ($date < $now && $orders[$k]->id_parent != -1) ? 'uk-text-muted' : 'uk-text-secondary'; ?>"><?php echo $orders[$k]->sid; ?></span>
                                        </div>
                                        <div class="uk-width-1-1 uk-width-1-6@m uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('RESERVE_DATE').'&ensp;:&ensp;'; ?></span>
                                            <span class="uk-text-small fnum font uk-position-relative <?php echo ($date < $now && $orders[$k]->id_parent != -1) ? 'uk-text-muted' : 'uk-text-secondary'; ?>"><?php echo JHtml::date(new JDate($orders[$k]->createdon), 'D ، d M Y'); ?></span>
                                        </div>
                                        <div class="uk-width-1-1 uk-width-small@m uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('RESERVE_TYPE').'&ensp;:&ensp;'; ?></span>
                                            <div class="uk-text-center uk-text-small fnum font uk-position-relative <?php echo ($date < $now && $orders[$k]->id_parent != -1) ? 'uk-text-muted' : 'uk-text-secondary'; ?>"><?php echo $orders[$k]->id_parent == -1 ? JText::sprintf('RESERVE_MULTIPLE') : JText::sprintf('RESERVE').' '.JText::sprintf('SERVICE_ID_'.$orders[$k]->id_service).'</span>'; ?></div>
                                        </div>
                                        <div class="uk-width-1-1 uk-width-1-6@m uk-flex uk-flex-middle uk-flex-center">
                                            <?php if($orders[$k]->id_parent != -1) { ?>
                                                <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('STATUS').'&ensp;:&ensp;'; ?></span>
                                                <span class="uk-text-small fnum font uk-position-relative bullet <?php echo $date < $now ? 'uk-text-muted' : 'uk-text-secondary green'; ?>"><?php echo $date < $now ? JText::sprintf('RESERVE_FINISHED') : JText::sprintf('RESERVE_PENDING'); ?></span>
                                            <?php } ?>
                                        </div>
                                        <div class="uk-width-1-1 uk-width-1-6@m uk-flex uk-flex-middle uk-flex-center">
                                            <a href="/reserves/12-2ny7024b642vkegb" class="uk-button uk-button-primary uk-width-1-1 uk-button-large font" onclick="vapCancelButtonPressed('');">جزئیات رزرو</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="uk-hidden@m uk-margin-top">
                <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders'); ?>" class="uk-button uk-button-success uk-button-large uk-width-1-1 uk-flex uk-flex-middle uk-flex-center font f500">
                    <span class="uk-margin-small-left"><?php echo JText::sprintf('ALL_MY_RESERVES'); ?></span>
                    <span><img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" data-uk-svg></span>
                </a>
            </div>
        <?php } else { ?>
            <div class="vap-allorders-void uk-flex uk-flex-center uk-flex-middle" data-uk-height-viewport="expand: true">
                <div>
                    <div class="uk-margin-medium-bottom uk-text-center"><img src="<?php echo JUri::base().'images/empty-box.svg'; ?>" width="372" height="423" class="uk-width-small"></div>
                    <div class="font f500 uk-text-large"><?php echo JText::_('VAPALLORDERSVOID'); ?></div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php
$user = JFactory::getUser();

// Get a db connection.
$db = JFactory::getDbo();
// Create a new query object.
$query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
$query->select($db->quoteName(array('user_id', 'profile_key', 'profile_value', 'ordering')));
$query->from($db->quoteName('#__user_profiles'));
$query->where($db->quoteName('profile_key') . ' LIKE ' . $db->quote('custom.%'));
$query->order('ordering ASC');

// Reset the query using our newly populated query object.
$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$results = $db->loadObjectList();
?>
<?php /* echo $this->loadTemplate('core'); ?>
<?php echo $this->loadTemplate('params'); ?>
<?php echo $this->loadTemplate('custom'); */ ?>

<?php if ($user->requireReset) { ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            UIkit.modal('#resetPassModal').show();
        });
    </script>
    <div id="resetPassModal" class="uk-modal-full" data-uk-modal="esc-close:false">
        <div class="uk-modal-dialog uk-background-primary transparented">
            <div class="uk-padding uk-height-viewport uk-flex uk-flex-middle uk-flex-center">
                <div class="uk-text-center">
                    <h3 class="uk-h3 uk-text-white uk-margin-medium-bottom f500 font"><?php echo JText::sprintf('RESET_PASS_TEXT'); ?></h3>
                    <a href="<?php echo JUri::base().'profile'; ?>" class="uk-button uk-button-primary uk-button-large uk-width-1-1 uk-width-medium@m uk-margin-small-top" target=""><?php echo JText::sprintf('CHANGE_PASSWORD'); ?>&ensp;<img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" width="24" height="24" alt="" data-uk-svg></a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>