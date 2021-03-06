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

$e 				 = $this->displayData['employee'];
$real_rating 	 = $this->displayData['rating'];
$rev_sub_title 	 = $this->displayData['review_sub'];
$reviews_enabled = $this->displayData['revsEnabled'];
$base_coord 	 = $this->displayData['baseCoord'];

$url = 'index.php?option=com_vikappointments&view=employeesearch&id_employee=' . $e['id'];

if (!empty($this->requestFilters['service']))
{
	$url .= '&id_service=' . $this->requestFilters['service'];

	$id_service = $this->requestFilters['service'];
}
else
{
	$id_service = 0;
}

if ($this->itemid)
{
	$url .= '&Itemid=' . $this->itemid;
}

$url = JRoute::_($url);

?>

<?php if ($e['group_name'] == 'MEETING_ROOMS') { ?>
    <div>
        <div class="empItem" id="vapempblock<?php echo $e['id']; ?>" data-employee="<?php echo $e['id']; ?>" data-service="<?php echo $id_service; ?>" data-day="">
            <a href="<?php echo '#modal-'. $e['id']; ?>" data-uk-toggle class="uk-display-block uk-display-block uk-border-rounded-large uk-overflow-hidden uk-link-toggle itemWrapper reservable">
                <div class="uk-grid-collapse" data-uk-grid>
                    <div class="uk-width-expand">
                        <div class="uk-padding-small uk-text-zero">
                            <div class="uk-padding-small uk-padding-remove-vertical uk-height-1-1">
                                <div class="uk-child-width-1-3 uk-grid-small uk-text-center" data-uk-grid>
                                    <div class="uk-text-secondary uk-flex uk-flex-middle uk-flex-center"><img src="<?php echo JUri::base().'images/sprite.svg#'.$e['group_name']; ?>" data-uk-svg></div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center">
                                        <div>
                                            <span class="uk-display-block uk-text-tiny uk-text-muted font itemTitle"><?php echo JText::sprintf($e['group_name'].'_NAME'); ?></span>
                                            <span class="uk-display-block uk-text-large uk-text-secondary font f500 fnum"><?php echo $e['lastname']; ?></span>
                                        </div>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center">
                                        <div>
                                            <span class="uk-display-block uk-text-tiny uk-text-muted font itemTitle"><?php echo JText::sprintf('FLOOR'); ?></span>
                                            <span class="uk-display-block uk-text-large uk-text-secondary font f500"><?php echo JText::sprintf('FLOOR'.$e['nickname']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php /* ?>
                    <div class="uk-width-auto">
                        <div class="uk-height-1-1 uk-position-relative statusWrapper reservable"><span class="uk-position-absolute uk-text-white uk-text-nowrap font status"><?php echo JText::sprintf('RESERVABLE'); ?></span>&emsp;&emsp;&emsp;</div>
                    </div>
                    <?php */ ?>
                </div>
            </a>
        </div>
    </div>
    <!-- Meeting Rooms Modal -->
    <?php $itemID = $e['id']; ?>
    <div id="modal-<?php echo $e['id']; ?>" data-uk-modal="container: #modalContainer" class="uk-text-zero uk-flex-top uk-position-absolute uk-background-primary transparented">
        <div class="uk-modal-dialog uk-modal-body uk-background-secondary uk-margin-auto-vertical uk-border-rounded uk-padding-large">
            <div class="uk-padding-small uk-text-primary uk-position-top-right">
                <a href="#" class="uk-link-reset uk-modal-close"><img src="<?php echo JUri::base().'images/sprite.svg#x-square'; ?>" width="24" height="24" data-uk-svg /></a>
            </div>
            <div>
                <div data-uk-grid>
                    <div class="uk-width-1-3 uk-visible@m">
                        <div class="uk-text-white"><img src="<?php echo JUri::base().'images/sprite.svg#'.$e['group_name']; ?>" class="uk-width-2-3" data-uk-svg></div>
                    </div>
                    <div class="uk-width-1-2 uk-width-1-3@m uk-flex uk-flex-middle uk-flex-center">
                        <div>
                            <span class="uk-display-inline-block uk-text-tiny uk-text-muted uk-margin-small-left font itemTitle"><?php echo JText::sprintf($e['group_name'].'_NAME'); ?></span>
                            <span class="uk-display-inline-block uk-text-large uk-text-white font f500 fnum"><?php echo $e['lastname']; ?></span>
                        </div>
                    </div>
                    <div class="uk-width-1-2 uk-width-1-3@m uk-flex uk-flex-middle uk-flex-center">
                        <div>
                            <span class="uk-display-inline-block uk-text-tiny uk-text-muted uk-margin-small-left font itemTitle"><?php echo JText::sprintf('FLOOR'); ?></span>
                            <span class="uk-display-inline-block uk-text-large uk-text-white font f500"><?php echo JText::sprintf('FLOOR'.$e['nickname']); ?></span>
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <div class="uk-placeholder uk-border-rounded uk-padding-small">
                            <div class="uk-grid-small" data-uk-grid>
                                <div class="uk-width-auto uk-text-white">
                                    <a href="javascript: void(0)" onclick="copyToClipboard('#email-<?php echo $itemID; ?>');UIkit.notification({message: '<?php echo JText::sprintf('EMAIL_COPIED'); ?>', status: 'success', pos: 'bottom-left'})" class="uk-link-reset" data-uk-tooltip="title: <?php echo JText::sprintf('COPY_EMAIL'); ?>; pos: left; offset: 10;"><img src="<?php echo JUri::base().'images/sprite.svg#clipboard-plus'; ?>" width="32" height="32" data-uk-svg /></a>
                                </div>
                                <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-left">
                                    <p class="uk-margin-remove uk-text-white uk-h5 font" id="email-<?php echo $e['id']; ?>"><?php echo $e['email']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <p class="uk-text-small uk-text-muted font uk-text-center"><?php echo JText::sprintf('ROOM_TEXT'); ?></p>
                    </div>
                    <div class="uk-width-1-1 uk-hidden@m uk-text-muted">
                        <a href="mailto:<?php echo $e['email']; ?>" class="uk-text-small uk-link-reset font uk-text-center uk-display-block"><?php echo JText::sprintf('ROOM_TEXT_SEND_EMAIL'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div data-floor="floor<?php echo $e['nickname']; ?>">
        <div class="empItem" id="vapempblock<?php echo $e['id']; ?>" data-employee="<?php echo $e['id']; ?>" data-service="<?php echo $id_service; ?>" data-day="">
            <div href="<?php echo $url; ?>" class="uk-display-block uk-display-block uk-border-rounded-large uk-overflow-hidden uk-link-toggle itemWrapper reservable">
                <div class="uk-grid-collapse" data-uk-grid>
                    <div class="uk-width-expand">
                        <div class="uk-padding-small uk-text-zero">
                            <div class="uk-padding-small uk-padding-remove-vertical uk-height-1-1">
                                <div class="uk-child-width-1-3 uk-grid-small uk-text-center" data-uk-grid>
                                    <div class="uk-text-secondary uk-flex uk-flex-middle uk-flex-center">
                                        <img src="<?php echo JUri::base().'images/sprite.svg#'.$e['group_name']; ?>" class="uk-width-2-3" data-uk-svg>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center">
                                        <div>
                                            <span class="uk-display-block uk-text-tiny uk-text-muted font itemTitle"><?php echo JText::sprintf($e['group_name'].'_NAME'); ?></span>
                                            <span class="uk-display-block uk-text-large uk-text-secondary font f500 fnum"><?php echo $e['lastname']; ?></span>
                                        </div>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center">
                                        <div>
                                            <span class="uk-display-block uk-text-tiny uk-text-muted font itemTitle"><?php echo JText::sprintf('FLOOR'); ?></span>
                                            <span class="uk-display-block uk-text-large uk-text-secondary font f500"><?php echo JText::sprintf('FLOOR'.$e['nickname']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- AVAILABILITY -->
                <div class="emp-search-box-right uk-text-center uk-margin-remove">
                    <div class="uk-padding-small uk-padding-remove-top">
                        <div data-uk-spinner="ratio: .5"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>




<script>

	jQuery(document).ready(function() {

		/**
		 * This function is declared by this view:
		 * views/employeeslist/view.html.php
		 * 
		 * @see 	addJS()
		 */
		loadEmployeeAvailTable(<?php echo $e['id']; ?>);

	});

</script>