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

    <div class="vap-allorders-void uk-flex uk-flex-center uk-flex-middle uk-padding-large" data-uk-height-viewport="expand: true">
        <div>
            <div class="uk-margin-medium-bottom uk-text-center"><img src="<?php echo JUri::base().'images/empty-box.svg'; ?>" width="372" height="423" class="uk-width-1-2 uk-width-medium@m"></div>
            <div class="font f500 uk-text-large"><?php echo JText::_('VAPALLORDERSVOID'); ?></div>
        </div>
    </div>

<?php } else { 

	$config = UIFactory::getConfig();

    $dt_format = 'l ، j F Y';
    $dt_format_time = $config->get('timeformat');

	/* $dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat'); */
	
	?>

	<div class="uk-padding vap-allorders-list_" data-uk-filter="target: .reservesList; animation: delayed-fade;">
        <div class="uk-text-zero uk-grid-small uk-grid-divider uk-child-width-auto uk-margin-medium-bottom" data-uk-grid>
            <div>
                <ul class="uk-subnav uk-subnav-pill">
                    <li class="uk-active" data-uk-filter-control><a href="#" class="uk-text-small font"><?php echo JTEXT::_('ALL'); ?></a></li>
                </ul>
            </div>
            <div>
                <ul class="uk-subnav uk-subnav-pill">
                    <li data-uk-filter-control="filter: [data-group='parkings']; group: data-group"><a href="#" class="uk-text-small font"><?php echo JTEXT::_('PARKINGS'); ?></a></li>
                    <li data-uk-filter-control="filter: [data-group='tables']; group: data-group"><a href="#" class="uk-text-small font"><?php echo JTEXT::_('TABLES'); ?></a></li>
                </ul>
            </div>
            <div>
                <ul class="uk-subnav uk-subnav-pill">
                    <li data-uk-filter-control="filter: [data-active='yes']; group: data-active"><a href="#" class="uk-text-small font"><?php echo JTEXT::_('ACTIVE'); ?></a></li>
                    <li data-uk-filter-control="filter: [data-active='no']; group: data-active"><a href="#" class="uk-text-small font"><?php echo JTEXT::_('NOT_ACTIVE'); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="summeryTable">
            <table class="uk-table uk-table-middle uk-table-divider uk-table-responsive uk-margin-remove" data-uk-filter="target: .reservesList">
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
                <tbody class="reservesList">
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

                <?php
                $date = new JDate($ord['checkin_ts']);
                $date->toUnix();
                $now =  new JDate('now');


                if ($ord['id_parent'] != -1) {
                    $db = JFactory::getDbo();
                    $empDetailsQuery = $db->getQuery(true);
                    $empDetailsQuery
                        ->select($db->quoteName(array('id', 'firstname', 'lastname', 'nickname')))
                        ->from($db->quoteName('#__vikappointments_employee'))
                        ->where($db->quoteName('id') . ' = ' . $ord['empid']);
                    $empDetails = $db->setQuery($empDetailsQuery)->loadObject();

                }

                ?>
                    <?php if($ord['id_parent'] != -1) { ?>
                <tr data-active="<?php echo ($date < $now || $ord['status'] == 'CANCELED') ? 'no' : 'yes'; ?>" data-group="<?php echo strtolower($ord['sername']); ?>" class="<?php if ($date < $now) echo 'uk-background-muted'; ?><?php if ($ord['status'] == 'CANCELED') echo ' uk-background-canceled'; ?>">
                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-gray' : 'uk-text-secondary'; ?> uk-visible@m"><img src="<?php echo JUri::base().'images/sprite.svg#'.$ord['sername']; ?>" width="36" height="36" data-uk-svg></td>
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
                        <span class="uk-text-small fnum font"><?php echo ArasJoomlaVikApp::jdate($dt_format, $ord['checkin_ts']); ?></span>
                    </td>
                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('ENTRANCE_TIME').'&ensp;:&ensp;'; ?></span>
                        <span class="uk-text-small fnum font uk-position-relative bullet <?php echo $date < $now ? 'uk-text-muted' : 'uk-text-secondary green'; ?>"><?php echo ArasJoomlaVikApp::jdate($dt_format_time, $ord['checkin_ts']); ?></span>
                    </td>
                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                        <span class="uk-text-small fnum font uk-hidden@m"><?php echo JText::sprintf('DURATION_SHORT').'&ensp;:&ensp;'; ?></span>
                        <span class="uk-text-small fnum font uk-position-relative bullet <?php echo $date < $now ? 'uk-text-muted' : 'uk-text-secondary red'; ?>">
                            <?php
                            $d = floor ($ord['duration'] / 1440);
                            $h = floor (($ord['duration'] - $d * 1440) / 60);
                            $m = $ord['duration'] - ($d * 1440) - ($h * 60);

                            if (!empty($h))
                                echo $h.' '.JText::_('VAPSHORTCUTHOURS');
                            if (!empty($h) && !empty($m))
                                echo ' '.JText::_('AND').' ';
                            if (!empty($m))
                                echo $m.' '.JText::_('VAPSHORTCUTMINUTE');
                            echo ' ('.JText::sprintf('FINISH').' '.ArasJoomlaVikApp::jdate($dt_format_time, VikAppointments::getCheckout($ord['checkin_ts'], $ord['duration'])).')';
                            ?>
                        </span>
                    </td>
                    <td class="uk-text-center <?php echo ($date < $now) ? 'uk-text-muted' : 'uk-text-secondary'; ?>">
                        <?php if (empty($ord['child'])) { ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=order&ordnum=' . $ord['id'].'&ordkey=' . $ord['sid']); ?>" class="uk-button uk-button-primary uk-width-1-1 uk-button-large font" onClick="vapCancelButtonPressed('<?php echo $cancel_uri; ?>');"><?php echo JText::sprintf('RESERVE_DETAILS'); ?></a>
                        <?php } ?>
                    </td>
                </tr>
                        <?php } ?>
            <?php } ?>
                </tbody>
            </table>
        </div>
	</div>
	
	<form class="uk-hidden" action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post">
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
