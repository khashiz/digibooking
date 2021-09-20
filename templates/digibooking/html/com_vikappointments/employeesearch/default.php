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

$employee = $this->employee;
$service  = $this->selectedService;

// SET EMPLOYEE TIMEZONE
VikAppointments::setCurrentTimezone($employee['timezone']);

$date_format = VikAppointments::getDateFormat();
$time_format = VikAppointments::getTimeFormat();
$dt_format 	 = $date_format . ' ' . $time_format;

$cart_enabled = VikAppointments::isCartEnabled();

// recurrence
$recurrence = array(
	'enabled' 	=> VikAppointments::isRecurrenceEnabled() && $service['use_recurrence'],
	'params'	=> array(),
);

if ($recurrence['enabled'])
{
	$recurrence['params'] = VikAppointments::getRecurrenceParams();
}

// reviews section
$real_rating = VikAppointments::roundHalfClosest($employee['rating_avg']);

$reviews_enabled = VikAppointments::isEmployeesReviewsEnabled();
$rev_sub_title 	 = '';

if ($this->reviews['size'] > 0)
{
	$rev_sub_title = JText::sprintf('VAPREVIEWSSUBTITLE1', $this->reviews['size']);
}

if ($this->reviews['votes'] > 0 && $this->reviews['votes'] != $this->reviews['size'])
{
	if (!empty($rev_sub_title))
	{
		$rev_sub_title .= ", "; 
	}

	$rev_sub_title .= JText::sprintf('VAPREVIEWSSUBTITLE2', $this->reviews['votes']);
}

if (empty($rev_sub_title))
{
	$rev_sub_title = JText::_('VAPNOREVIEWSSUBTITLE');
}

// waiting list
$waiting_list_enabled = VikAppointments::isWaitingList();

?>

<!-- EMPLOYEE DETAILS -->

<?php
// Register the employee details within a property of this class 
// to make it available also for the sublayout.
$this->displayData = array(
	'reviewsEnabled' 	=> $reviews_enabled,
	'reviewRating' 		=> $real_rating,
	'review_sub' 		=> $rev_sub_title,
);

// Get employee template with name, description,
// image and so on.
echo $this->loadTemplate('employee');
?>

<!-- CONTACT FORM -->

<?php
// load the contact form only if enabled
if ($employee['quick_contact'])
{
	// Register the quick contact details within a property of this class 
	// to make it available also for the sublayout.
	$this->displayData = array();

	// get quick contact template
	echo $this->loadTemplate('contact');
}
?>

<!-- SEARCH FORM -->
<div class="uk-padding-large">
    <form id="plateForm" name="empsearchform" action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeesearch&id_employee=' . $employee['id']  . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post" id="vapempsearchform">

        <!-- FILTER BAR -->

        <?php
        // Register the filterbar details within a property of this class
        // to make it available also for the sublayout.
        $this->displayData = array();

        // Get filterbar template, containing the selection of the service,
        // the month and the number of people.
         echo $this->loadTemplate('filterbar');
        ?>

        <!-- CALENDARS -->

        <?php
        // Register the calendar details within a property of this class
        // to make it available also for the sublayout.
        $this->displayData = array();

        // get calendars (and timeline) template
        echo $this->loadTemplate('calendars');
        ?>

        <!-- SERVICE DESCRIPTION -->

        <?php /* ?>
        <div class="vapserdescriptiondiv">
            <?php // echo VikAppointments::renderHtmlDescription($this->selectedService['description'], 'servicesearch'); ?>
        </div>
        <?php */ ?>

        <!-- OPTIONS -->

        <?php
        /*
        if (count($this->options) > 0)
        {
            // Register the options details within a property of this class
            // to make it available also for the sublayout.
            $this->displayData = array();

            // get options template
            echo $this->loadTemplate('options');
        } */
        ?>

        <!-- CHECKOUT -->

        <?php
        // Register the checkout details within a property of this class
        // to make it available also for the sublayout.
        $this->displayData = array(
            'cartEnabled' 		=> $cart_enabled,
            'waitlistEnabled' 	=> $waiting_list_enabled,
            'recurrence' 		=> $recurrence,
        );

        // get checkout template containing the recurrence box and the checkout buttons
        echo $this->loadTemplate('checkout');
        ?>

        <input type="hidden" name="id_employee" value="<?php echo $employee['id']; ?>" />
        <input type="hidden" name="day" value="" id="vapdayselected" />

        <input type="hidden" name="task" value="employeesearch" />
        <input type="hidden" name="option" value="com_vikappointments" />
    </form>
</div>
<!-- CONFIRMATION FORM -->
<form name="confirmapp" action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=confirmapp' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post" id="vapempconfirmapp" class="uk-hidden">
	
	<input type="hidden" name="id_service" value="" id="vapconfserselected" />
	<input type="hidden" name="id_employee" value="" id="vapconfempselected" />
	<input type="hidden" name="day" value="" id="vapconfdayselected" />
	<input type="hidden" name="hour" value="" id="vapconfhourselected" />
	<input type="hidden" name="min" value="" id="vapconfminselected" />
	<input type="hidden" name="people" value="1" id="vapconfpeopleselected" />
	
	<input type="hidden" name="from" value="2" />
	<input type="hidden" name="view" value="confirmapp" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
</form>

<?php /* ?>
<!-- REVIEWS -->
<?php 
if ($reviews_enabled)
{
	// Register the review details within a property of this class 
	// to make it available also for the sublayout.
	$this->displayData = array(
		'subtitle' 			=> $rev_sub_title,
		'datetime_format' 	=> $dt_format,
	);

	// get reviews template
	echo $this->loadTemplate('reviews');		
}
?>

<!-- WAITING LIST -->
<?php
if ($waiting_list_enabled)
{
	// Register the waiting list details within a property of this class 
	// to make it available also for the sublayout.
	$this->displayData = array();

	// get waiting list template (modal box)
	echo $this->loadTemplate('waitlist');
}
?>

<?php */ ?>

<script>

	/**
	 * @usedby 	views/employeesearch/tmpl/default_calendars.php
	 * @usedby 	layouts/blocks/reviews.php
	 */
	var vapDoAnimation = <?php echo $this->doAnimation ? 1 : 0; ?>;

	/**
	 * @usedby 	views/employeesearch/tmpl/default_calendars.php
	 * @usedby 	layouts/blocks/checkout.php 	
	 */
	var LAST_TIMESTAMP_USED = null;

	// CART FUNCTIONS

	function vapIsCartPublished() {
		return typeof VIKAPPOINTMENTS_CART_INSTANCE !== "undefined";
	}



    jQuery(document).ready(function () {
        jQuery('#reserveSubmit').on('click', function () {
            document.cookie = "username=khashhhh;path=/";
        })
    });


</script>