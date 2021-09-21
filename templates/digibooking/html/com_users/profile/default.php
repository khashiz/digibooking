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
$lastXOrders = $db->getQuery(true);
$lastXOrders
    ->select($db->quoteName(array('id','id_employee', 'checkin_ts', 'duration', 'status', 'sid', 'conf_key', 'createdby')))
    ->from($db->quoteName('#__vikappointments_reservation'))
    ->where($db->quoteName('createdby') . ' = ' . JFactory::getUser()->id)
    ->order('id DESC')
    ->setLimit('4');
$orders = $db->setQuery($lastXOrders)->loadObjectList();
?>
<div class="profile<?php echo $this->pageclass_sfx; ?>">
    <div class="uk-background-secondary uk-padding-large uk-flex uk-flex-middle uk-flex-right">
        <?php if ($this->params->get('show_page_heading')) : ?>
            <div class="page-header">
                <h1 class="font uk-text-white uk-h2 f500 uk-margin-remove"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
            </div>
        <?php endif; ?>
    </div>
    <div class="uk-padding-large uk-background-muted">
        <div>
            <div class="uk-child-width-1-1 uk-child-width-1-3@m" data-uk-grid>
                <div>
                    <div>ggggg</div>
                </div>
                <div>
                    <div>ggggg</div>
                </div>
                <div>
                    <div>ggggg</div>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-padding-large">
        <div class="summeryTable">
            <div class="uk-padding-small">
                <div class="uk-padding-small uk-padding-remove-vertical">
                    <div class="uk-grid-small uk-grid" data-uk-grid="">
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small uk-first-column">&ensp;</div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-expand">نام</div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6">طبقه</div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6">تاریخ ورود</div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small">ساعت ورود</div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6">وضعیت</div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6">&emsp;</div>
                    </div>
                </div>
            </div>
            <?php for ($k=0;$k<count($orders);$k++) { ?>
                <div>
                    <hr class="uk-margin-remove">
                    <div>
                        <div class="uk-padding-small">
                            <div class="uk-padding-small uk-padding-remove-vertical">
                                <div class="uk-grid-small uk-grid" data-uk-grid="">
                                    <div class="uk-width-small uk-flex uk-flex-middle uk-flex-center uk-first-column">
                                        <div class="uk-width-2-5 uk-text-secondary uk-height-1-1 uk-flex uk-flex-middle uk-flex-center rowIcon">
                                            <img src="https://netparsi.ir/images/sprite.svg#PARKINGS" data-uk-svg="" hidden=""><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 106.75 90.09" class=" uk-svg" width="106.75" height="90.09">
                                                <path d="M89.23,8.67H84a1.14,1.14,0,0,0-1.26,1V26.56c0,.7.83,1.06,1.69,1.06s1.68-.36,1.68-1.06v-6H89c3.42,0,6.09-1.64,6.09-5.89v-.15C95.12,10.25,92.53,8.67,89.23,8.67Zm2.52,6.25c0,2-1,3-2.72,3h-2.9V11.63H89c1.68,0,2.72,1,2.72,3Z" transform="translate(0 0)"></path>
                                                <path d="M74.84,57.33c-.66-.37-1.31-.76-1.93-1.18l-10.06-6.8a18.28,18.28,0,0,0-10.3-3.15H34.09a18.24,18.24,0,0,0-13,5.36,24.78,24.78,0,0,1-6,4.42,4,4,0,0,0,1.82,7.47h6.3a1.52,1.52,0,0,0,0-3h-6.3a.92.92,0,0,1-.42-1.73,27.6,27.6,0,0,0,6.79-5,15.27,15.27,0,0,1,10.84-4.47h4.13V60.41h-5a1.52,1.52,0,1,0,0,3H74.87a2.27,2.27,0,0,0,2.26-2.27,4.38,4.38,0,0,0-2.29-3.85ZM41.26,60.41V49.24H52.55a15.39,15.39,0,0,1,8.6,2.63l10.06,6.8c.7.48,1.43.92,2.17,1.33a1.44,1.44,0,0,1,.47.42Z" transform="translate(0 0)"></path>
                                                <path d="M45,65.77h-4.7a1.52,1.52,0,1,0,0,3H45a1.52,1.52,0,0,0,0-3Z" transform="translate(0 0)"></path>
                                                <path d="M105.44,64.49l-.48-.8a12.51,12.51,0,0,0-8.26-5.8l-3.93-.78V37.21h10.16a3.8,3.8,0,0,0,3.8-3.8V3.79A3.8,3.8,0,0,0,102.93,0H73.32a3.8,3.8,0,0,0-3.8,3.79V15a1.52,1.52,0,1,0,3,0V3.79A.76.76,0,0,1,73.32,3h29.61a.76.76,0,0,1,.76.75V33.41a.75.75,0,0,1-.76.75H73.32a.75.75,0,0,1-.76-.75v-8.5a1.52,1.52,0,1,0-3,0h0v8.5a3.81,3.81,0,0,0,3.8,3.8H84.84V55.53l-2-.4a21.5,21.5,0,0,1-7.3-3.45l-10.06-6.8a23,23,0,0,0-12.91-4H34.09a22.89,22.89,0,0,0-16.28,6.72,20.05,20.05,0,0,1-10,5.42l-2.18.45A7.08,7.08,0,0,0,0,60.42V75.15a4.47,4.47,0,0,0,2.64,4.07L8.7,81.91a10.08,10.08,0,0,0,19.61.83H78.82a10.08,10.08,0,0,0,19.41,0h2.89a5.64,5.64,0,0,0,5.63-5.64V69.21A9.1,9.1,0,0,0,105.44,64.49ZM87.88,37.21h1.85v19.3l-1.85-.38ZM23.59,85a7.05,7.05,0,0,1-11.4-7.91l0-.06a3.59,3.59,0,0,1,.17-.34l.11-.2.08-.13.1-.17.1-.15.1-.15L13,75.7l.1-.13.16-.18.08-.1.24-.25,0,0,0,0a2.09,2.09,0,0,1,.22-.2l0,0,.22-.19h0a7,7,0,0,1,8.87,0h0l.23.2,0,0a3.07,3.07,0,0,1,.26.25l.27.28.11.13.13.15.14.19.08.1.16.23,0,0A7.07,7.07,0,0,1,23.59,85Zm69.92,0a7,7,0,0,1-10.88-8.84l0,0a2,2,0,0,1,.17-.23.86.86,0,0,1,.07-.1l.14-.19.13-.15.11-.13.27-.28a7,7,0,0,1,10,0h0a3.45,3.45,0,0,1,.26.28.69.69,0,0,1,.11.13l.13.15.15.18.07.1c.06.08.11.16.16.24l0,0A7.07,7.07,0,0,1,93.51,85Zm10.2-7.88a2.6,2.6,0,0,1-2.6,2.6h-2.5a9.88,9.88,0,0,0-2.24-6l0,0-.27-.31L96,73.23l-.3-.31s0,0,0,0A10,10,0,0,0,91,70.21a10.28,10.28,0,0,0-4.45-.11,10,10,0,0,0-5.15,2.77l0,0-.3.31-.07.08-.27.32,0,0a9.88,9.88,0,0,0-2.24,6H28.69a10,10,0,0,0-2.25-6v0c-.1-.12-.19-.23-.29-.33l-.05-.06-.35-.37a10.09,10.09,0,0,0-14.27,0l-.33.34-.09.1a2.75,2.75,0,0,0-.22.26l-.1.11-.22.28-.07.09c-.19.26-.36.53-.53.81v0c-.08.14-.16.27-.23.41v0a10.2,10.2,0,0,0-1.05,3.23L3.87,76.43A1.41,1.41,0,0,1,3,75.14V60.42A4,4,0,0,1,6.22,56.5L8.4,56A23,23,0,0,0,20,49.81,19.9,19.9,0,0,1,34.09,44H52.55A20,20,0,0,1,63.76,47.4l10.06,6.8a24.25,24.25,0,0,0,8.41,3.92l13.88,2.76a9.48,9.48,0,0,1,6.25,4.39l.48.8a6.12,6.12,0,0,1,.87,3.15Z" transform="translate(0 0)"></path>
                                                <path d="M21.87,76.73a4.62,4.62,0,1,0,0,6.53h0a4.59,4.59,0,0,0,0-6.49Zm-2.15,4.38a1.58,1.58,0,1,1-2.23-2.23,1.59,1.59,0,0,1,2.23,0h0a1.58,1.58,0,0,1,0,2.22Z" transform="translate(0 0)"></path>
                                                <path d="M91.79,76.73A4.62,4.62,0,1,0,93.14,80,4.62,4.62,0,0,0,91.79,76.73Zm-2.15,4.38a1.58,1.58,0,0,1-2.23-2.23,1.59,1.59,0,0,1,2.23,0A1.62,1.62,0,0,1,90.1,80a1.57,1.57,0,0,1-.46,1.11Z" transform="translate(0 0)"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small fnum font uk-position-relative">پارکینگ D۴۵</span>
                                    </div>
                                    <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small font uk-position-relative">اول</span>
                                    </div>
                                    <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small fnum font uk-position-relative">دوشنبه ، ۵ مهر ۱۴۰۰</span>
                                    </div>
                                    <div class="uk-width-small uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small fnum font uk-position-relative bullet green">۰۹:۰۰</span>
                                    </div>
                                    <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small fnum font uk-position-relative bullet <?php echo strtolower($orders[$k]->status); ?>"><?php echo strtoupper(JText::_('VAPSTATUS' . $orders[$k]->status)); ?></span>
                                    </div>
                                    <div class="uk-width-1-6">
                                        <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=order&ordnum=' . $orders[$k]->id.'&ordkey=' . $orders[$k]->sid); ?>" class="uk-button uk-button-primary uk-width-1-1 uk-button-large font" onClick="vapCancelButtonPressed('<?php echo $cancel_uri; ?>');"><?php echo JText::sprintf('RESERVE_DETAILS'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
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
                    <a href="<?php echo JUri::base().'profile'; ?>" class="uk-button uk-button-primary uk-button-large uk-width-medium uk-margin-small-top" target=""><?php echo JText::sprintf('CHANGE_PASSWORD'); ?>&ensp;<img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" width="24" height="24" alt="" data-uk-svg></a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>