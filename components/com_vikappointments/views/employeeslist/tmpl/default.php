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
echo $this->loadTemplate('toolbar');
?>
	
<!-- employees list -->
	
<div class="vapempallblocks">

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

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=employeeslist' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post">
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
