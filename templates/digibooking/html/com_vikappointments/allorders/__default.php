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

$orders 		= $this->orders;
$itemid = $this->itemid;

if ($this->user->guest)
{
	exit ('No direct access');
}

?>
<div class="uk-background-secondary uk-padding-large uk-flex uk-flex-middle uk-flex-center">
    <div class="page-header">
        <h1 class="font uk-text-white uk-h2 f500 uk-margin-remove"><?php echo JText::sprintf('RESERVES_HISTORY'); ?></h1>
        <div><?php echo JHtml::_('content.prepare','{loadposition breadcrumb}'); ?></div>
    </div>
</div>
<div class="vap-allorders-userhead uk-hidden">
	<div class="vap-allorders-userleft">
		<h2><?php echo JText::sprintf('VAPALLORDERSTITLE', $this->user->name); ?></h2>
	</div>
	<div class="vap-allorders-userright">
		<?php if ($this->hasPackages) { ?>
			<button type="button" class="vap-btn blue" onClick="document.location.href='<?php echo JRoute::_('index.php?option=com_vikappointments&view=packorders'); ?>';"><?php echo JText::_('VAPALLORDERSPACKBUTTON'); ?></button>
		<?php } ?>
		<button type="button" class="vap-btn blue" onClick="document.location.href='<?php echo JRoute::_('index.php?option=com_vikappointments&view=userprofile'); ?>';"><?php echo JText::_('VAPALLORDERSPROFILEBUTTON'); ?></button>
		<button type="button" class="vap-btn" onClick="document.location.href='<?php echo JRoute::_('index.php?option=com_vikappointments&task=userlogout'); ?>';"><?php echo JText::_('VAPLOGOUTTITLE'); ?></button>
	</div>
</div>
	
<?php if (!count($this->orders)) { ?>

	<div class="vap-allorders-void uk-flex uk-flex-center uk-flex-middle" data-uk-height-viewport="expand: true">
        <div>
            <div class="uk-margin-medium-bottom"><img src="<?php echo JUri::base().'images/empty-box.svg'; ?>" width="372" height="423" class="uk-width-medium"></div>
            <div class="font f500 uk-text-large"><?php echo JText::_('VAPALLORDERSVOID'); ?></div>
        </div>
    </div>

<?php } else { 

	$config = UIFactory::getConfig();

    $dt_format = 'l ، j F Y';
    $dt_format_time = $config->get('timeformat');

	?>


	<div class="uk-padding-large vap-allorders-list_">

        <div class="summeryTable">

            <div class="uk-padding-small">
                <div class="uk-padding-small">
                    <div class="uk-grid-small" data-uk-grid>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small">&ensp;</div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-expand"><?php echo JText::sprintf('NAME'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('FLOOR'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('ENTRANCE_DATE'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small"><?php echo JText::sprintf('ENTRANCE_TIME'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('DURATION'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('DELETE'); ?></div>
                    </div>
                </div>
            </div>

            <?php
            foreach ($this->orders as $i => $ord)
            {
                echo $ord['child'];

                $row_text = '';
                $date = '';

                if ($ord['id_parent'] != -1)
                {
                    $ord['sername'] = VikAppointments::getTranslation($ord['serid'], $ord, $this->langServices, 'sername', 'name');
                    $ord['empname'] = VikAppointments::getTranslation($ord['empid'], $ord, $this->langEmployees, 'empname', 'nickname');

                    $row_text = $ord['sername'];
                    if ($ord['view_emp'])
                    {
                        $row_text .= ", " . $ord['empname'];
                    }

                    VikAppointments::setCurrentTimezone($ord['timezone']);

                    $date = ArasJoomlaVikApp::jdate($dt_format, $ord['checkin_ts']);
                }
                else if ($ord['createdon'] != -1)
                {
                    $date = ArasJoomlaVikApp::jdate($dt_format, $ord['createdon']);
                }

                $child_class = "";

                if (empty($ord['child']))
                {
                    $i = ($i + 1) % 2;
                }
                else
                {
                    $child_class = "vap-allord-child" . $ord['id_parent'];
                }

                ?>

                <?php
//                $db = JFactory::getDbo();
//                $empDetailsQuery = $db->getQuery(true);
//                $empDetailsQuery
//                    ->select($db->quoteName(array('id', 'firstname', 'lastname', 'nickname')))
//                    ->from($db->quoteName('#__vikappointments_employee'))
//                    ->where($db->quoteName('id') . ' = ' . $ord['empid']);
//                $empDetails = $db->setQuery($empDetailsQuery)->loadObject();
                ?>

                <hr class="uk-margin-remove">
                <div class="uk-padding-small">
                    <div>
                        <div>
                            <div class="uk-padding-small">
                                <div class="uk-grid-small" data-uk-grid>
                                    <div class="uk-width-small uk-flex uk-flex-middle uk-flex-center">
                                        <div class="uk-width-1-2 uk-text-secondary">
                                            <img src="<?php echo JUri::base().'images/sprite.svg#'.$ord['sername']; ?>" data-uk-svg>
                                        </div>
                                    </div>
                                    <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small fnum font uk-position-relative"><?php echo $empDetails->firstname.' '.$empDetails->lastname; ?></span>
                                    </div>
                                    <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small font uk-position-relative"><?php echo JText::sprintf('FLOOR'.$empDetails->nickname); ?></span>
                                    </div>
                                    <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small fnum font uk-position-relative"><?php echo ArasJoomlaVikApp::jdate($dt_format, $ord['checkin_ts']); ?></span>
                                    </div>
                                    <div class="uk-width-small uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small fnum font uk-position-relative bullet green"><?php echo ArasJoomlaVikApp::jdate($dt_format_time, $ord['checkin_ts']); ?></span>
                                    </div>
                                    <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small fnum font uk-position-relative bullet red vapcartitemboxoptionsdur">cccc</span>
                                    </div>
                                    <div class="uk-width-1-6">
                                        <button type="button" class="uk-button uk-button-danger uk-width-1-1 uk-button-outline uk-button-large font" onClick="vapCancelButtonPressed('<?php echo $cancel_uri; ?>');">delete</button>
                                        <span class="uk-hidden vap-allorders-status<?php echo strtolower($ord['status']); ?>">
                                            <?php echo strtoupper(JText::_('VAPSTATUS' . $ord['status'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <?php if (empty($ord['child'])) { ?>
                        <a class="uk-hidde" href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=order&ordnum=' . $ord['id'].'&ordkey=' . $ord['sid']); ?>"><?php echo $ord['id'] . "-" . $ord['sid']; ?></a>
                    <?php } ?>
            <?php } ?>

        </div>

	</div>
	
	<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post">
		<?php echo JHtml::_('form.token'); ?>
		<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
		<input type="hidden" name="option" value="com_vikappointments" />
		<input type="hidden" name="view" value="allorders" />
	</form>
	
<?php } ?>
