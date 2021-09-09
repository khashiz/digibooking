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

$max_month = VikAppointments::getNumberOfMonths();

if ($this->service['choose_emp'])
{
	?>
	<div class="vapemployeeselect">
		<select name="id_emp" onChange="vapEmployeeValueChanged();" id="vapempsel">
			<?php
			foreach ($this->employees as $e)
			{
				?>
				<option value="<?php echo $e['id']; ?>" <?php echo $e['id'] == $this->idEmployee ? 'selected="selected"' : ''; ?>><?php echo $e['nickname']; ?></option>
				<?php
			}
			?>
		</select>
	</div>
	<?php
}
else
{
	?><input type="hidden" name="id_emp" value="<?php echo $this->idEmployee; ?>" /><?php
}

if ($max_month > 1)
{
	$date = getdate(mktime(0, 0, 0, $this->defMonth, 1, $this->defYear));
	?>
	<div class="vapmonthselect">
		<select name="month" id="vapmonthsel" onChange="vapMonthValueChanged();">
			<?php
			$month		= $date['mon']; // get current month
			$cont_month = 0; 			// count the total number of months displayed

			while ($cont_month < $max_month)
			{
				?>
				<option value="<?php echo $month; ?>" <?php echo $month == $this->month ? 'selected="selected"' : ''; ?>><?php echo JText::_('VAPMONTH' . $month); ?></option>
				<?php

				// go back to zero if we are in december
				$month = ($month % 12);

				$month++;
				$cont_month++;
			}
			?>
		</select>
	</div>
	<?php
}

if ($this->service['max_capacity'] > 1 && $this->service['max_per_res'] > 1)
{
	?>
	<div class="vapserpeoplediv">
		<select name="people" id="vapserpeopleselect" onChange="vapPeopleValueChanged();">
			<?php
			for ($i = $this->service['min_per_res']; $i <= $this->service['max_per_res']; $i++)
			{
				?>
				<option value="<?php echo $i; ?>"><?php echo $i . " " . strtolower(JText::_($i > 1 ? 'VAPSUMMARYPEOPLE' : 'VAPSUMMARYPERSON')); ?></option>
				<?php
			}
			?>
		</select>
	</div>
	<?php
}

if (count($this->locations) > 1)
{
	?>
	<div class="vap-empsearch-locations">
		<?php
		foreach ($this->locations as $loc)
		{
			$checked = ""; 
			
			if (count($this->reqLocations) == 0 || in_array($loc['id'], $this->reqLocations))
			{
				$checked = 'checked="checked"';
			}
			?>
			<div class="vap-empsearch-locbox">
				<input type="checkbox" value="<?php echo $loc['id']; ?>" <?php echo $checked; ?> name="locations[]" id="vaplocation<?php echo $loc['id']; ?>" class="vap-empsearch-locval" />
				<label for="vaplocation<?php echo $loc['id']; ?>">
					<?php echo $loc['name'] . (!empty($loc['address']) ? " (" . $loc['address'] . ")" : ''); ?>
				</label>
			</div>
		<?php } ?>
	</div>
	<?php
}
?>

<script>

	jQuery(document).ready(function() {

		jQuery('#vapempsel').select2({
			allowClear: false,
			width: 300
		});

		jQuery('#vapmonthsel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery('#vapserpeopleselect').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery('.vap-empsearch-locval').on('change', function() {
			document.sersearchform.submit();
		});

	});

	function vapEmployeeValueChanged() {
		jQuery('#vapempselected').val(jQuery('#vapempsel').val());
		document.sersearchform.submit();
	}

	function vapMonthValueChanged() {
		jQuery('#vapmonthselected').val(jQuery('#vapmonthsel').val());
		document.sersearchform.submit();
	}

	<?php if ($this->service['max_capacity'] > 1) { ?>

		function vapPeopleValueChanged() {
			// INCREASE PRICE
			var rate 		= <?php echo $this->service['price']; ?>;
			var per_people 	= <?php echo $this->service['priceperpeople'] ? 1 : 0; ?>;

			if (per_people && rate > 0) {
				var people = parseInt(jQuery('#vapserpeopleselect').val());
				// jQuery('.vapempserpricesp').html(Currency.getInstance().format(rate * people));
			}
			
			var day = jQuery('#vapdayselected').val();

			if (day.length > 0) {
				/**
				 * Refresh timeline to re-calculate availability.
				 * See main layout file for further details about
				 * the vapGetTimeLine() function.
				 *
				 * @link views/servicesearch/tmpl/default.php
				 */
				vapGetTimeLine(parseInt(day));
			}
		}

	<?php } ?>

</script>
