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

$max_month 			= VikAppointments::getNumberOfMonths();
$format_duration 	= VikAppointments::isDurationToFormat();

$old_gid 	= 0;
$grp_opened = false;

?>

<div class="vapemployeeselect">
	<select name="id_service" id="vapsersel" onChange="vapServiceValueChanged();">
		<?php
		foreach ($this->services as $s)
		{	
			$sname = $s['name'];
			
			if (empty($old_gid) || $old_gid != $s['gid'])
			{
				if (!empty($old_gid) && $grp_opened)
				{
					?></optgroup><?php
					$grp_opened = false;
				}

				$gname = $s['gname'];
				
				if (strlen($gname))
				{
					?><optgroup label="<?php echo $gname; ?>"><?php
					$grp_opened = true;
				}
				
				$old_gid = $s['gid'];
			}
			
			$_price = '';

			if ($s['price'] > 0)
			{
				$_price = ' ' . VikAppointments::printPriceCurrencySymb($s['price']);
			}
			
			$duration_label = VikAppointments::formatMinutesToTime($s['duration'], $format_duration);
			
			?>
			<option value="<?php echo $s['id']; ?>" <?php echo ($s['id'] == $this->idService ? 'selected="selected"' : ''); ?>><?php echo $sname . $_price . ' (' . $duration_label . ')'; ?></option>
			<?php
		}

		if ($grp_opened)
		{
			?></optgroup><?php
		}
	?>
	</select>
</div>

<?php
if ($max_month > 1)
{
	$date = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime(0, 0, 0, $this->defMonth, 1, $this->defYear));
	?>
	<div class="vapmonthselect">
		<select name="month" id="vapmonthsel" onChange="vapMonthValueChanged();">
			<?php
			$month		= $date['mon']; // get current month
			$cont_month = 0; 			// count the total number of months displayed

			while ($cont_month < $max_month)
			{
				?>
				<option value="<?php echo $month; ?>" <?php echo $month == $this->month ? 'selected="selected"' : ''; ?>><?php echo JText::_(ArasJoomlaVikApp::getMonthName($month)); ?></option>
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

if ($this->selectedService['max_capacity'] > 1 && $this->selectedService['max_per_res'] > 1)
{
	?>
	<div class="vapserpeoplediv">
		<select name="people" id="vapserpeopleselect" onChange="vapPeopleValueChanged();">
			<?php
			for ($i = $this->selectedService['min_per_res']; $i <= $this->selectedService['max_per_res']; $i++)
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

		jQuery('#vapsersel').select2({
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
			document.empsearchform.submit();
		});

	});

	function vapServiceValueChanged() {
		// jQuery('#vapserselected').val(jQuery('#vapsersel').val());
		document.empsearchform.submit();
	}

	function vapMonthValueChanged() {
		// jQuery('#vapmonthselected').val(jQuery('#vapmonthsel').val());
		document.empsearchform.submit();
	}

	<?php if ($this->selectedService['max_capacity'] > 1) { ?>

		function vapPeopleValueChanged() {
			// INCREASE PRICE
			var rate 		= <?php echo $this->selectedService['price']; ?>;
			var per_people 	= <?php echo $this->selectedService['priceperpeople'] ? 1 : 0; ?>;

			if (per_people && rate > 0) {
				var people = parseInt(jQuery('#vapserpeopleselect').val());
				// jQuery('#vapratebox').html(Currency.getInstance().format(rate * people));
			}
			
			var day = jQuery('#vapdayselected').val();

			if (day.length > 0) {
				/**
				 * Refresh timeline to re-calculate availability.
				 * See main layout file for further details about
				 * the vapGetTimeLine() function.
				 *
				 * @link views/employeesearch/tmpl/default.php
				 */
				vapGetTimeLine(parseInt(day));
			}
		}

	<?php } ?>

	function vapUpdateServiceRate(rate) {
		/**
		 * @todo 	Should the rate be updated
		 * 			also in case the new cost has been 
		 * 			nullified (free)?
		 */

		if (rate > 0) {
			// update only if the rate is higher than 0
			jQuery('#vapratebox').html(Currency.getInstance().format(rate));
		}
	}

</script>
