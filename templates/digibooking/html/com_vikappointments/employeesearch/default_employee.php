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
<div class="uk-background-blue uk-padding-large uk-flex uk-flex-middle uk-flex-center">
    <div class="page-header uk-flex-1 uk-text-zero">
        <div class="uk-grid-small" data-uk-grid>
            <div class="uk-width-expand uk-flex uk-flex-middle">
                <div class="uk-flex-1">
                    <p class="font uk-text-white uk-h4 uk-text-center f500"><?php echo $this->services[0]['gname']; ?></p>
                    <h1 class="font uk-text-white uk-h2 uk-text-center f500 uk-margin-remove fnum"><?php echo JText::sprintf('GROUP_'.$this->employee['id_group'].'_SINLE_PAGE_MAIN_TITLE', $this->employee['lastname']); ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="vapempblock<?php echo $this->employee['id']; ?>">
    <?php echo VikAppointments::renderHtmlDescription($this->employee['note'], 'employeesearch'); ?>
</div>