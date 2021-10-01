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

$reviews_enabled 	= $this->displayData['reviewsEnabled'];
$real_rating 	 	= $this->displayData['reviewRating'];
$rev_sub_title 	 	= $this->displayData['review_sub'];


?>
<div class="uk-text-zero wizardWrapper">
    <div class="uk-grid-collapse uk-height-1-1" data-uk-grid>
        <div class="uk-width-1-1 uk-width-auto@m">
            <div class="uk-background-secondary uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-padding uk-padding-remove-vertical">
                <h1 class="font uk-text-white uk-h5 f500 uk-margin-remove uk-padding uk-padding-remove-horizontal"><?php echo JTEXT::_('PAGE_TITLE_'.$this->services[0]['name']); ?></h1>
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
                        <div class="step done uk-height-1-1 uk-padding-small uk-flex uk-flex-middle">
                            <div class="uk-position-relative uk-flex-1">
                                <span class="uk-position-top-right uk-text-success uk-text-tiny font f500 steDone uk-visible@m"><?php echo JTEXT::_('STEP_DONE'); ?></span>
                                <div class="uk-grid-small uk-flex-center" data-uk-grid>
                                    <div class="uk-width-expand font uk-text-small uk-visible@m">
                                        <span class="stepLevel"><?php echo '۲. '; ?></span>
                                        <span class="stepText"><?php echo JTEXT::_('SELECT_'.$this->services[0]['name']); ?></span>
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
                                        <span class="stepLevel"><?php echo '۳. '; ?></span>
                                        <span class="stepText"><?php echo JTEXT::_('COMPLETE_INFO'); ?></span>
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
                                        <span class="stepLevel"><?php echo '۴. '; ?></span>
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
                <img src="<?php echo JURI::base().'images/sprite.svg#'.$this->services[0]['name']; ?>" width="50" data-uk-svg>
            </div>
        </div>
    </div>
</div>
<div id="vapempblock<?php echo $this->employee['id']; ?>">
    <?php echo VikAppointments::renderHtmlDescription($this->employee['note'], 'employeesearch'); ?>
</div>