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

	/* $dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat'); */
	
	?>
	
	<div class="uk-padding-large vap-allorders-list_">
        <div class="summeryTable">
            <div class="uk-padding-small">
                <div class="uk-padding-small uk-padding-remove-vertical">
                    <div class="uk-grid-small" data-uk-grid>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small">&ensp;</div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-expand"><?php echo JText::sprintf('NAME'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('FLOOR'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('ENTRANCE_DATE'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-small"><?php echo JText::sprintf('ENTRANCE_TIME'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6"><?php echo JText::sprintf('STATUS'); ?></div>
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-small font f600 uk-width-1-6">&emsp;</div>
                    </div>
                </div>
            </div>
            <?php foreach ($this->orders as $i => $ord)
            {
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
                    $date = '<strong>' . ArasJoomlaVikApp::jdate($dt_format, $ord['createdon']) . '</strong>';
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
                <div class="<?php echo $ord['id_parent'] != -1 ? 'main' : 'parent'; ?> <?php echo $i . ' ' . $child_class; ?> <?php echo empty($ord['child']) ? '' : 'childItem'; ?>" <?php echo empty($ord['child']) ? '' : 'style="display:none;"'; ?>>
                    <hr class="uk-margin-remove">
                    <?php
                    $date = new JDate($ord['checkin_ts']);
                    $date->toUnix();
                    $now =  new JDate('now');
                    ?>
                    <div class="<?php if (!empty($ord['child'])) {echo '';} ?>  <?php if($date < $now && $ord['id_parent'] != -1) { echo 'finished'; } ?>">
                        <div class="uk-padding-small">
                            <div class="uk-padding-small uk-padding-remove-vertical">
                                <div class="uk-grid-small" data-uk-grid>
                                    <?php if ($ord['id_parent'] != -1) { ?>
                                        <?php
                                        $db = JFactory::getDbo();
                                        $empDetailsQuery = $db->getQuery(true);
                                        $empDetailsQuery
                                            ->select($db->quoteName(array('id', 'firstname', 'lastname', 'nickname')))
                                            ->from($db->quoteName('#__vikappointments_employee'))
                                            ->where($db->quoteName('id') . ' = ' . $ord['empid']);
                                        $empDetails = $db->setQuery($empDetailsQuery)->loadObject();
                                        ?>
                                        <div class="uk-width-small uk-flex uk-flex-middle uk-flex-center">
                                            <div class="uk-width-2-5 uk-text-secondary uk-height-1-1 uk-flex uk-flex-middle uk-flex-center rowIcon">
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
                                    <?php } else { ?>
                                        <div class="uk-width-small uk-flex uk-flex-middle uk-flex-center">
                                            <a href="javascript: void(0);" onClick="vapDisplayOrderChildren(<?php echo $ord['id']; ?>);" class="uk-width-2-5 uk-text-secondary uk-height-1-1 uk-flex uk-flex-middle uk-flex-center rowIcon">
                                                <img src="<?php echo JUri::base().'images/sprite.svg#caret-down-square'; ?>" width="36" height="36" data-uk-svg>
                                            </a>
                                        </div>
                                        <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-center">
                                            <span class="uk-text-secondary uk-text-small font"><?php echo JText::_('VAPALLORDERSMULTIPLE'); ?></span>
                                        </div>
                                    <?php } ?>
                                    <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                        <span class="uk-text-secondary uk-text-small fnum font uk-position-relative bullet <?php echo strtolower($ord['status']); ?>"><?php echo strtoupper(JText::_('VAPSTATUS' . $ord['status'])); ?></span>
                                    </div>
                                    <div class="uk-width-1-6 uk-flex uk-flex-middle uk-flex-center">
                                        <?php if($date < $now && $ord['id_parent'] != -1) { ?>
                                            <span class="uk-text-secondary uk-text-small font uk-position-relative"><?php echo JText::sprintf('RESERVE_FINISHED'); ?></span>
                                        <?php } else { ?>
                                            <?php if (empty($ord['child'])) { ?>
                                                <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=order&ordnum=' . $ord['id'].'&ordkey=' . $ord['sid']); ?>" class="uk-button uk-button-primary uk-width-1-1 uk-button-large font" onClick="vapCancelButtonPressed('<?php echo $cancel_uri; ?>');"><?php echo JText::sprintf('RESERVE_DETAILS'); ?></a>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
	</div>
	
	<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post">
		<?php echo JHtml::_('form.token'); ?>
		<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
		<input type="hidden" name="option" value="com_vikappointments" />
		<input type="hidden" name="view" value="allorders" />
	</form>
	
	<script>
		
		function vapDisplayOrderChildren(id) {
			if (jQuery('.vap-allord-child'+id).first().is(':visible')) {
				jQuery('.vap-allord-child'+id).slideUp();
			} else {
				jQuery('.vap-allord-child'+id).slideDown();
			}
		}
		
	</script>
	
<?php } ?>
