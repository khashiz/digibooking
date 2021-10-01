<?php
/** 
 * @package   	VikAppointments
 * @subpackage 	com_vikappointments
 * @author    	Matteo Galletti - e4j
 * @copyright 	Copyright (C) 2019 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license  	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link 		https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$employees 	= $this->employees;
$sel_emp 	= $this->selEmployee;
$itemid 	= $this->itemid;

$reviews_enabled = VikAppointments::isEmployeesReviewsEnabled();
$no_review_label = JText::_('VAPNOREVIEWSSUBTITLE');

$base_coord = null;

if ($this->filtersInRequest && !empty($this->requestFilters['base_coord']))
{
	$coord = explode(',', $this->requestFilters['base_coord']);

	if (count($coord) < 2)
	{
		$coord = array(0, 0);
	}

	$base_coord = array(
		'latitude' 	=> floatval($coord[0]),
		'longitude' => floatval($coord[1]),
	);
}

?>

<?php
$now = strtotime(new JDate('now'));
$db = JFactory::getDbo();
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
$parkingReserve = true;
if (count($awaitingParkings) >= JFactory::getApplication()->getTemplate(true)->params->get('limitparkings'))
    $parkingReserve = false;

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
function toHours($hours, $minutes) {
    return $hours + round($minutes / 60, 2);
}
$tableReserve = true;
if (toHours(0, $tablesMinutesSum) >= JFactory::getApplication()->getTemplate(true)->params->get('limittables'))
    $tableReserve = false;

?>



<?php if ($this->employees[0]['group_name'] == 'PARKINGS') { ?>
    <?php if ($parkingReserve) { ?>
        <?php
// Register some vars within a property of this class
// to make it available also for the sublayout.
        $this->displayData = array();
// get toolbar template, used for filters
//echo $this->loadTemplate('toolbar');
//?>
        <!-- employees list -->
        <div class="vapempallblocks">
            <?php if ($this->employees[0]['group_name'] == 'MEETING_ROOMS') { ?>
                <div class="uk-background-secondary uk-padding-large uk-flex uk-flex-middle uk-flex-center">
                    <div class="page-header uk-flex-1 uk-text-zero">
                        <div class="uk-grid-small" data-uk-grid>
                            <div class="uk-width-1-1 uk-width-expand@m uk-flex uk-flex-middle">
                                <div class="uk-flex-1 uk-text-center uk-text-right@m">
                                    <h1 class="font uk-text-white uk-h2 f500 uk-margin-remove"><?php echo $this->document->title; ?></h1>
                                    <?php /* ?>
                        <div class="uk-flex uk-flex-center uk-flex-right@m"><?php echo JHtml::_('content.prepare','{loadposition breadcrumb}'); ?></div>
                        <?php */ ?>
                                </div>
                            </div>
                            <div class="uk-width-1-1 uk-width-auto@m uk-visible@m">
                                <div>
                                    <div class="uk-child-width-auto" data-uk-grid>
                                        <div>
                                            <div class="uk-text-white"><img src="<?php echo JUri::base().'images/sprite.svg#'.$this->employees[0]['group_name']; ?>" alt="" data-uk-svg /></div>
                                        </div>
                                        <?php /* ?>
                            <div class="uk-text-center uk-flex uk-flex-middle uk-flex-center">
                                <div>
                                    <div class="uk-h1 uk-text-white uk-margin-remove font uk-text-bold fnum"><?php echo count($employees); ?></div>
                                    <div class="uk-text-small uk-text-white font"><?php echo JText::sprintf('ALL_'.$this->employees[0]['group_name']); ?></div>
                                </div>
                            </div>
                            <?php */ ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="uk-text-zero wizardWrapper uk-visible@m">
                    <div class="uk-grid-collapse uk-height-1-1" data-uk-grid>
                        <div class="uk-width-1-1 uk-width-auto@m">
                            <div class="uk-background-secondary uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-padding uk-padding-remove-vertical">
                                <h1 class="font uk-text-white uk-h5 f500 uk-margin-remove uk-padding uk-padding-remove-horizontal"><?php echo JTEXT::_('PAGE_TITLE_'.$this->employees[0]['group_name']); ?></h1>
                            </div>
                        </div>
                        <div class="uk-width-expand">
                            <div class="uk-background-muted uk-height-1-1">
                                <div class="uk-child-width-1-4 uk-grid-collapse uk-height-1-1" data-uk-grid>
                                    <div class="stepWrapper">
                                        <div class="step done uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                            <div class="uk-position-relative uk-flex-1">
                                                <span class="uk-position-top-right uk-text-success uk-text-tiny font f500 steDone uk-visible@m"><?php echo JTEXT::_('STEP_DONE'); ?></span>
                                                <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                                    <div class="uk-width-expand font uk-text-small uk-visible@m">
                                                        <span class="stepLevel"><?php echo '۱. '; ?></span>
                                                        <span class="stepText"><?php echo JTEXT::_('LOGIN_TO_SYSTEM'); ?></span>
                                                    </div>
                                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                                <span class="uk-text-success">
                                                    <img src="<?php echo JURI::base().'images/sprite.svg#check'; ?>" width="20" height="20" data-uk-svg>
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stepWrapper">
                                        <div class="step current uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                            <div class="uk-position-relative uk-flex-1">
                                                <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                                    <div class="uk-width-expand font f600 uk-text-small uk-visible@m">
                                                        <span class="stepLevel fnum"><?php echo '۲. '; ?></span>
                                                        <span class="stepText"><?php echo JTEXT::_('SELECT_'.$this->employees[0]['group_name']); ?></span>
                                                    </div>
                                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                                <span class="uk-text-secondary">
                                                    <img src="<?php echo JURI::base().'images/sprite.svg#pencil'; ?>" width="16" height="16" data-uk-svg>
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stepWrapper">
                                        <div class="step uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                            <div class="uk-position-relative uk-flex-1">
                                                <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                                    <div class="uk-width-expand font uk-text-small uk-visible@m">
                                                        <span class="stepLevel fnum"><?php echo '۳. '; ?></span>
                                                        <span class="stepText"><?php echo JTEXT::_('COMPLETE_INFO'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stepWrapper">
                                        <div class="step uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                            <div class="uk-position-relative uk-flex-1">
                                                <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                                    <div class="uk-width-expand font uk-text-small uk-visible@m">
                                                        <span class="stepLevel fnum"><?php echo '۴. '; ?></span>
                                                        <span class="stepText"><?php echo JTEXT::_('COMPLETE_RESERVE'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-auto uk-visible@m">
                            <div class="uk-background-white uk-text-secondary uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-padding uk-padding-remove-vertical">
                                <img src="<?php echo JURI::base().'images/sprite.svg#'.$this->employees[0]['group_name']; ?>" width="50" data-uk-svg>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div data-uk-height-viewport="expand: true" id="modalContainer" class="uk-position-relative">
                <div class="uk-padding" data-uk-filter="target: .parkingSluts; animation: fade;">
                    <div class="uk-padding-small uk-background-primary uk-margin-medium-bottom">
                        <div class="uk-padding-small">
                            <div>
                                <div class="uk-grid-small" data-uk-grid>
                                    <div class="uk-width-1-1 uk-width-expand@m uk-flex uk-flex-center uk-flex-right@m uk-flex-middle">
                                        <h1 class="font uk-text-white uk-h5 f500 uk-margin-remove"><?php echo JTEXT::_('SELECT_POSITION'); ?></h1>
                                    </div>
                                    <div class="uk-width-1-1 uk-width-auto@m">
                                        <select id="floorFilter" class="uk-select uk-width-1-1 uk-width-small@m uk-border-rounded uk-height-1-1 uk-text-small font">
                                            <option value=""><?php echo JTEXT::_('SELECT_FLOOR'); ?></option>
                                            <option data-uk-filter-control="filter: [data-floor*=floor1]; group: data-floor" value="filter: [data-floor*=floor1]; group: data-floor"><?php echo JTEXT::_('FLOOR').' '.JTEXT::_('FLOOR1'); ?></option>
                                            <option data-uk-filter-control="filter: [data-floor*=floor2]; group: data-floor" value="filter: [data-floor*=floor2]; group: data-floor"><?php echo JTEXT::_('FLOOR').' '.JTEXT::_('FLOOR2'); ?></option>
                                            <option data-uk-filter-control="filter: [data-floor*=floor3]; group: data-floor" value="filter: [data-floor*=floor3]; group: data-floor"><?php echo JTEXT::_('FLOOR').' '.JTEXT::_('FLOOR3'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-child-width-1-3@l uk-child-width-1-4@xl parkingSluts" data-uk-grid>
                            <?php
                            foreach ($employees as $e)
                            {
                                $e['nickname'] 	= VikAppointments::getTranslation($e['id'], $e, $this->langEmployees, 'nickname', 'nickname');
                                $e['note'] 		= VikAppointments::getTranslation($e['id'], $e, $this->langEmployees, 'note', 'note');

                                if (!empty($e['group_name']))
                                {
                                    $e['group_name'] = VikAppointments::getTranslation($e['id_group'], $e, $this->langGroups, 'group_name', 'name');
                                }

                                $real_rating = VikAppointments::roundHalfClosest($e['rating_avg']);

                                // review subtitle
                                if ($e['reviews_count'] > 0)
                                {
                                    $rev_sub_title = JText::sprintf('VAPREVIEWSSUBTITLE1', $e['reviews_count']);
                                }
                                else
                                {
                                    $rev_sub_title = $no_review_label;
                                }

                                // Register some vars within a property of this class
                                // to make it available also for the sublayout.
                                $this->displayData = array(
                                    'employee' 		=> $e,
                                    'rating' 		=> $real_rating,
                                    'review_sub' 	=> $rev_sub_title,
                                    'revsEnabled' 	=> $reviews_enabled,
                                    'baseCoord'		=> $base_coord,
                                );

                                if ($this->ajaxSearch)
                                {
                                    // AJAX search enabled, use a minified layout to include
                                    // also the availability table
                                    $tmpl = 'employee_search';
                                }
                                else
                                {
                                    // otherwise use the default layout
                                    $tmpl = 'employee';
                                }

                                // get employee template, used to display profile information
                                echo $this->loadTemplate($tmpl);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeeslist' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" class="uk-hidden">
            <?php echo JHtml::_('form.token'); ?>
            <div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
            <input type="hidden" name="option" value="com_vikappointments" />
            <input type="hidden" name="view" value="employeeslist" />
        </form>
        <!-- employees contact form -->
        <?php
// Register some vars within a property of this class
// to make it available also for the sublayout.
        $this->displayData = array();

// get quick contact template
        echo $this->loadTemplate('contact');
        ?>
    <?php } else { ?>
        <div class="vap-allorders-void uk-flex uk-flex-center uk-flex-middle uk-padding-large" data-uk-height-viewport="expand: true">
            <div class="uk-text-center">
                <div class="uk-margin-medium-bottom uk-width-1-2 uk-width-1-1@m uk-margin-auto"><img src="<?php echo JUri::base().'images/barricade.svg'; ?>" width="387" height="339" class="uk-width-medium"></div>
                <div class="font f500"><?php echo JText::_('YOU_CAN_NOT_RESERVE_PARKING'); ?></div>
            </div>
        </div>
        <div class="uk-padding-large uk-text-center">
            <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders'); ?>" class="uk-text-muted font f500"><?php echo JText::sprintf('ALL_MY_RESERVES'); ?></a>
        </div>
    <?php } ?>
<?php } elseif ($this->employees[0]['group_name'] == 'TABLES') { ?>
    <?php if ($tableReserve) { ?>
        <?php
// Register some vars within a property of this class
// to make it available also for the sublayout.
        $this->displayData = array();
// get toolbar template, used for filters
//echo $this->loadTemplate('toolbar');
//?>
        <!-- employees list -->
        <div class="vapempallblocks">
            <?php if ($this->employees[0]['group_name'] == 'MEETING_ROOMS') { ?>
                <div class="uk-background-secondary uk-padding-large uk-flex uk-flex-middle uk-flex-center">
                    <div class="page-header uk-flex-1 uk-text-zero">
                        <div class="uk-grid-small" data-uk-grid>
                            <div class="uk-width-1-1 uk-width-expand@m uk-flex uk-flex-middle">
                                <div class="uk-flex-1 uk-text-center uk-text-right@m">
                                    <h1 class="font uk-text-white uk-h2 f500 uk-margin-remove"><?php echo $this->document->title; ?></h1>
                                    <?php /* ?>
                        <div class="uk-flex uk-flex-center uk-flex-right@m"><?php echo JHtml::_('content.prepare','{loadposition breadcrumb}'); ?></div>
                        <?php */ ?>
                                </div>
                            </div>
                            <div class="uk-width-1-1 uk-width-auto@m uk-visible@m">
                                <div>
                                    <div class="uk-child-width-auto" data-uk-grid>
                                        <div>
                                            <div class="uk-text-white"><img src="<?php echo JUri::base().'images/sprite.svg#'.$this->employees[0]['group_name']; ?>" alt="" data-uk-svg /></div>
                                        </div>
                                        <?php /* ?>
                            <div class="uk-text-center uk-flex uk-flex-middle uk-flex-center">
                                <div>
                                    <div class="uk-h1 uk-text-white uk-margin-remove font uk-text-bold fnum"><?php echo count($employees); ?></div>
                                    <div class="uk-text-small uk-text-white font"><?php echo JText::sprintf('ALL_'.$this->employees[0]['group_name']); ?></div>
                                </div>
                            </div>
                            <?php */ ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="uk-text-zero wizardWrapper uk-visible@m">
                    <div class="uk-grid-collapse uk-height-1-1" data-uk-grid>
                        <div class="uk-width-1-1 uk-width-auto@m">
                            <div class="uk-background-secondary uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-padding uk-padding-remove-vertical">
                                <h1 class="font uk-text-white uk-h5 f500 uk-margin-remove uk-padding uk-padding-remove-horizontal"><?php echo JTEXT::_('PAGE_TITLE_'.$this->employees[0]['group_name']); ?></h1>
                            </div>
                        </div>
                        <div class="uk-width-expand">
                            <div class="uk-background-muted uk-height-1-1">
                                <div class="uk-child-width-1-4 uk-grid-collapse uk-height-1-1" data-uk-grid>
                                    <div class="stepWrapper">
                                        <div class="step done uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                            <div class="uk-position-relative uk-flex-1">
                                                <span class="uk-position-top-right uk-text-success uk-text-tiny font f500 steDone uk-visible@m"><?php echo JTEXT::_('STEP_DONE'); ?></span>
                                                <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                                    <div class="uk-width-expand font uk-text-small uk-visible@m">
                                                        <span class="stepLevel"><?php echo '۱. '; ?></span>
                                                        <span class="stepText"><?php echo JTEXT::_('LOGIN_TO_SYSTEM'); ?></span>
                                                    </div>
                                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                                <span class="uk-text-success">
                                                    <img src="<?php echo JURI::base().'images/sprite.svg#check'; ?>" width="20" height="20" data-uk-svg>
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stepWrapper">
                                        <div class="step current uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                            <div class="uk-position-relative uk-flex-1">
                                                <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                                    <div class="uk-width-expand font f600 uk-text-small uk-visible@m">
                                                        <span class="stepLevel fnum"><?php echo '۲. '; ?></span>
                                                        <span class="stepText"><?php echo JTEXT::_('SELECT_'.$this->employees[0]['group_name']); ?></span>
                                                    </div>
                                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                                <span class="uk-text-secondary">
                                                    <img src="<?php echo JURI::base().'images/sprite.svg#pencil'; ?>" width="16" height="16" data-uk-svg>
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stepWrapper">
                                        <div class="step uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                            <div class="uk-position-relative uk-flex-1">
                                                <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                                    <div class="uk-width-expand font uk-text-small uk-visible@m">
                                                        <span class="stepLevel fnum"><?php echo '۳. '; ?></span>
                                                        <span class="stepText"><?php echo JTEXT::_('COMPLETE_INFO'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stepWrapper">
                                        <div class="step uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                                            <div class="uk-position-relative uk-flex-1">
                                                <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                                    <div class="uk-width-expand font uk-text-small uk-visible@m">
                                                        <span class="stepLevel fnum"><?php echo '۴. '; ?></span>
                                                        <span class="stepText"><?php echo JTEXT::_('COMPLETE_RESERVE'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-auto uk-visible@m">
                            <div class="uk-background-white uk-text-secondary uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-padding uk-padding-remove-vertical">
                                <img src="<?php echo JURI::base().'images/sprite.svg#'.$this->employees[0]['group_name']; ?>" width="50" data-uk-svg>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div data-uk-height-viewport="expand: true" id="modalContainer" class="uk-position-relative">
                <div class="uk-padding">
                    <div>
                        <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-child-width-1-3@l uk-child-width-1-4@xl" data-uk-grid>
                            <?php
                            foreach ($employees as $e)
                            {
                                $e['nickname'] 	= VikAppointments::getTranslation($e['id'], $e, $this->langEmployees, 'nickname', 'nickname');
                                $e['note'] 		= VikAppointments::getTranslation($e['id'], $e, $this->langEmployees, 'note', 'note');

                                if (!empty($e['group_name']))
                                {
                                    $e['group_name'] = VikAppointments::getTranslation($e['id_group'], $e, $this->langGroups, 'group_name', 'name');
                                }

                                $real_rating = VikAppointments::roundHalfClosest($e['rating_avg']);

                                // review subtitle
                                if ($e['reviews_count'] > 0)
                                {
                                    $rev_sub_title = JText::sprintf('VAPREVIEWSSUBTITLE1', $e['reviews_count']);
                                }
                                else
                                {
                                    $rev_sub_title = $no_review_label;
                                }

                                // Register some vars within a property of this class
                                // to make it available also for the sublayout.
                                $this->displayData = array(
                                    'employee' 		=> $e,
                                    'rating' 		=> $real_rating,
                                    'review_sub' 	=> $rev_sub_title,
                                    'revsEnabled' 	=> $reviews_enabled,
                                    'baseCoord'		=> $base_coord,
                                );

                                if ($this->ajaxSearch)
                                {
                                    // AJAX search enabled, use a minified layout to include
                                    // also the availability table
                                    $tmpl = 'employee_search';
                                }
                                else
                                {
                                    // otherwise use the default layout
                                    $tmpl = 'employee';
                                }

                                // get employee template, used to display profile information
                                echo $this->loadTemplate($tmpl);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeeslist' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" class="uk-hidden">
            <?php echo JHtml::_('form.token'); ?>
            <div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
            <input type="hidden" name="option" value="com_vikappointments" />
            <input type="hidden" name="view" value="employeeslist" />
        </form>
        <!-- employees contact form -->
        <?php
// Register some vars within a property of this class
// to make it available also for the sublayout.
        $this->displayData = array();

// get quick contact template
        echo $this->loadTemplate('contact');
        ?>
    <?php } else { ?>
        <div class="vap-allorders-void uk-flex uk-flex-center uk-flex-middle uk-padding-large" data-uk-height-viewport="expand: true">
            <div class="uk-text-center">
                <div class="uk-margin-medium-bottom uk-width-1-2 uk-width-1-1@m uk-margin-auto"><img src="<?php echo JUri::base().'images/barricade.svg'; ?>" width="387" height="339" class="uk-width-medium"></div>
                <div class="font f500"><?php echo JText::_('YOU_CAN_NOT_RESERVE_TABLE'); ?></div>
            </div>
        </div>
        <div class="uk-padding-large uk-text-center">
            <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders'); ?>" class="uk-text-muted font f500"><?php echo JText::sprintf('ALL_MY_RESERVES'); ?></a>
        </div>
    <?php } ?>
<?php } else { ?>
    <?php
// Register some vars within a property of this class
// to make it available also for the sublayout.
    $this->displayData = array();
// get toolbar template, used for filters
//echo $this->loadTemplate('toolbar');
//?>
    <!-- employees list -->
    <div class="vapempallblocks">
        <div class="uk-background-secondary uk-padding-large uk-flex uk-flex-middle uk-flex-center">
            <div class="page-header uk-flex-1 uk-text-zero">
                <div class="uk-grid-small" data-uk-grid>
                    <div class="uk-width-1-1 uk-width-expand@m uk-flex uk-flex-middle">
                        <div class="uk-flex-1 uk-text-center uk-text-right@m">
                            <h1 class="font uk-text-white uk-h2 f500 uk-margin-remove"><?php echo $this->document->title; ?></h1>
                            <?php /* ?>
                        <div class="uk-flex uk-flex-center uk-flex-right@m"><?php echo JHtml::_('content.prepare','{loadposition breadcrumb}'); ?></div>
                        <?php */ ?>
                        </div>
                    </div>
                    <div class="uk-width-1-1 uk-width-auto@m uk-visible@m">
                        <div>
                            <div class="uk-child-width-auto" data-uk-grid>
                                <div>
                                    <div class="uk-text-white"><img src="<?php echo JUri::base().'images/sprite.svg#'.$this->employees[0]['group_name']; ?>" alt="" data-uk-svg /></div>
                                </div>
                                <?php /* ?>
                            <div class="uk-text-center uk-flex uk-flex-middle uk-flex-center">
                                <div>
                                    <div class="uk-h1 uk-text-white uk-margin-remove font uk-text-bold fnum"><?php echo count($employees); ?></div>
                                    <div class="uk-text-small uk-text-white font"><?php echo JText::sprintf('ALL_'.$this->employees[0]['group_name']); ?></div>
                                </div>
                            </div>
                            <?php */ ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div data-uk-height-viewport="expand: true" id="modalContainer" class="uk-position-relative">
            <div class="uk-padding">
                <div>
                    <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-child-width-1-3@l uk-child-width-1-4@xl" data-uk-grid>
                        <?php
                        foreach ($employees as $e)
                        {
                            $e['nickname'] 	= VikAppointments::getTranslation($e['id'], $e, $this->langEmployees, 'nickname', 'nickname');
                            $e['note'] 		= VikAppointments::getTranslation($e['id'], $e, $this->langEmployees, 'note', 'note');

                            if (!empty($e['group_name']))
                            {
                                $e['group_name'] = VikAppointments::getTranslation($e['id_group'], $e, $this->langGroups, 'group_name', 'name');
                            }

                            $real_rating = VikAppointments::roundHalfClosest($e['rating_avg']);

                            // review subtitle
                            if ($e['reviews_count'] > 0)
                            {
                                $rev_sub_title = JText::sprintf('VAPREVIEWSSUBTITLE1', $e['reviews_count']);
                            }
                            else
                            {
                                $rev_sub_title = $no_review_label;
                            }

                            // Register some vars within a property of this class
                            // to make it available also for the sublayout.
                            $this->displayData = array(
                                'employee' 		=> $e,
                                'rating' 		=> $real_rating,
                                'review_sub' 	=> $rev_sub_title,
                                'revsEnabled' 	=> $reviews_enabled,
                                'baseCoord'		=> $base_coord,
                            );

                            if ($this->ajaxSearch)
                            {
                                // AJAX search enabled, use a minified layout to include
                                // also the availability table
                                $tmpl = 'employee_search';
                            }
                            else
                            {
                                // otherwise use the default layout
                                $tmpl = 'employee';
                            }

                            // get employee template, used to display profile information
                            echo $this->loadTemplate($tmpl);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeeslist' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" class="uk-hidden">
        <?php echo JHtml::_('form.token'); ?>
        <div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
        <input type="hidden" name="option" value="com_vikappointments" />
        <input type="hidden" name="view" value="employeeslist" />
    </form>
    <!-- employees contact form -->
    <?php
// Register some vars within a property of this class
// to make it available also for the sublayout.
    $this->displayData = array();

// get quick contact template
    echo $this->loadTemplate('contact');
    ?>
<?php } ?>


<script>
    let sel = document.getElementById("floorFilter");
    sel.addEventListener("change", function(){
        let child = sel.querySelectorAll("option[data-uk-filter-control='" + sel.value + "']")[0];
        UIkit.filter('[data-uk-filter="target: .parkingSluts; animation: fade;"]', { target: ".parkingSluts" } ).apply( child );
    });
</script>
