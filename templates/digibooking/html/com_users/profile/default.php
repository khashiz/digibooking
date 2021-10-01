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
    <div class="uk-background-secondary uk-padding uk-flex uk-flex-middle uk-flex-center uk-flex-right@m">
        <?php if ($this->params->get('show_page_heading')) : ?>
            <div class="page-header">
                <h1 class="font uk-text-white uk-h2 f500 uk-margin-remove uk-text-center uk-text-right@m"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
            </div>
        <?php endif; ?>
    </div>
    <div class="uk-padding uk-padding-remove-top uk-padding-remove-bottom uk-position-relative">
        <div class="dashboardBoxWrapper">
            <div data-uk-slider>
                <div class="uk-position-relative">
                    <ul class="uk-slider-items uk-child-width-1-1 uk-child-width-1-3@m uk-grid">
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
    <div class="uk-padding">
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
                <table class="uk-table uk-table-middle uk-table-divider uk-table-responsive uk-margin-remove">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('NAME'); ?></th>
                        <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('FLOOR'); ?></th>
                        <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('ENTRANCE_DATE'); ?></th>
                        <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('ENTRANCE_TIME'); ?></th>
                        <th class="uk-text-small uk-text-center uk-text-secondary font f600"><?php echo JText::sprintf('DURATION'); ?></th>
                        <th></th>
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
                                <span class="uk-text-small fnum font uk-position-relative bullet <?php echo $date < $now ? 'uk-text-muted' : 'uk-text-secondary green'; ?>"><?php echo JHTML::date($orders[$k]->checkin_ts, 'H:i'); ?></span>
                            </td>
                            <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                                <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('DURATION_SHORT').'&ensp;:&ensp;'; ?></span>
                                <span class="uk-text-small fnum font uk-position-relative bullet <?php echo $date < $now ? 'uk-text-muted' : 'uk-text-secondary red'; ?>">
                            <?php
                            $checkOutTime = new JDate($orders[$k]->checkin_ts);
                            $checkOutTime->modify('+' . $orders[$k]->duration . ' minutes');

                            $d = floor ($orders[$k]->duration / 1440);
                            $h = floor (($orders[$k]->duration - $d * 1440) / 60);
                            $m = $orders[$k]->duration - ($d * 1440) - ($h * 60);

                            if (!empty($h))
                                echo $h.' '.JText::_('VAPSHORTCUTHOURS');
                            if (!empty($h) && !empty($m))
                                echo ' '.JText::_('AND').' ';
                            if (!empty($m))
                                echo $m.' '.JText::_('VAPSHORTCUTMINUTE');
                            echo ' ('.JText::sprintf('FINISH').' '.JHTML::date($checkOutTime, 'H:i').')';
                            ?>
                        </span>
                            </td>
                            <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                                <?php if (empty($ord['child'])) { ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=order&ordnum=' . $orders[$k]->id .'&ordkey=' . $orders[$k]->sid); ?>" class="uk-button uk-button-primary uk-width-1-1 uk-button-large font" onClick="vapCancelButtonPressed('<?php echo $cancel_uri; ?>');"><?php echo JText::sprintf('RESERVE_DETAILS'); ?></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
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