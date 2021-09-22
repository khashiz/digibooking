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

<!-- employees toolbar  -->
	
<?php
// Register some vars within a property of this class 
// to make it available also for the sublayout.
$this->displayData = array();
// get toolbar template, used for filters
//echo $this->loadTemplate('toolbar');
//?>
<!-- employees list -->
<div class="vapempallblocks">
    <div class="uk-height-mediu uk-background-secondary uk-padding-large uk-flex uk-flex-middle uk-flex-center">
        <div class="page-header uk-flex-1 uk-text-zero">
            <div class="uk-grid-small" data-uk-grid>
                <div class="uk-width-1-1 uk-width-expand@m uk-flex uk-flex-middle">
                    <div class="uk-flex-1 uk-text-center uk-text-right@m">
                        <h1 class="font uk-text-white uk-h2 f500 uk-margin-remove"><?php echo $this->document->title; ?></h1>
                        <div class="uk-flex uk-flex-center uk-flex-right@m"><?php echo JHtml::_('content.prepare','{loadposition breadcrumb}'); ?></div>
                    </div>
                </div>
                <div class="uk-width-1-1 uk-width-auto@m uk-visible@m">
                    <div>
                        <div class="uk-child-width-auto" data-uk-grid>
                            <div>
                                <div class="uk-text-white"><img src="<?php echo JUri::base().'images/sprite.svg#'.$this->employees[0]['group_name']; ?>" alt="" data-uk-svg /></div>
                            </div>
                            <div class="uk-text-center uk-flex uk-flex-middle uk-flex-center">
                                <div>
                                    <div class="uk-h1 uk-text-white uk-margin-remove font uk-text-bold fnum"><?php echo count($employees); ?></div>
                                    <div class="uk-text-small uk-text-white font"><?php echo JText::sprintf('ALL_'.$this->employees[0]['group_name']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div data-uk-height-viewport="expand: true" id="modalContainer" class="uk-position-relative">
        <div class="uk-padding-large">
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

<script>

	jQuery(document).ready(function() {
		
		<?php if (!empty($sel_emp)) { ?>

			jQuery('html,body').animate( {scrollTop: (jQuery('#vapempblock<?php echo $sel_emp; ?>').offset().top-5)}, {duration:'normal'} );

		<?php } ?>

	});
	
</script>
