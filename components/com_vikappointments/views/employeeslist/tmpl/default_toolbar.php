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

$filtersUri = 'index.php?option=com_vikappointments&view=employeeslist';

foreach ($this->requestFilters as $k => $v)
{
	$filtersUri .= "&filters[$k]=$v";
}

if ($this->itemid)
{
	$filtersUri .= "&Itemid={$this->itemid}";
}

?>

<div class="vap-emplist-toolbar">
	
	<?php
	/**
	 * The toolbar TOP box contains the select to filter the employees by group
	 * and a button to toggle the box containing the sortable fields.
	 */
	?>

	<div class="vap-emplist-toolbar-top">

		<?php
		// check if the group selection is allowed and the groups list
		// contains more than one element
		if (count($this->groups) > 1 && $this->groupsFilter)
		{ 
			$url = $filtersUri . '&ordering=' . $this->ordering;
			
			?>

			<div class="vap-empgroup-filterblock">

				<form action="<?php echo JRoute::_($url); ?>" id="vap-empgroup-form" method="post">
					
					<div class="vap-emplist-groups">

						<select name="employee_group" class="vap-empgroup-sel">
							<option></option>
							<?php
							foreach ($this->groups as $g)
							{
								$g['name'] = VikAppointments::getTranslation($g['id'], $g, $this->langGroups, 'name', 'name');
								?>

								<option
									value="<?php echo $g['id']; ?>"
									<?php echo $g['id'] == $this->selGroup ? 'selected="selected"' : ''; ?>
								><?php echo $g['name']; ?></option>

								<?php
							}
							?>
						</select>

					</div>

				</form>

			</div>

		<?php
		}
		
		// check if the customers are allowed to sort the employees list
		if ($this->orderingFilter)
		{
			// display the button to toggle the sortable fields
			?>
			<div class="vap-emplist-ordering">
				<span>
					<button type="button" class="vap-btn small blue" onClick="vapDisplayFilters(this);">
						<?php echo JText::_('VAPEMPORDERINGTITLE'); ?>
					</button>
				</span>

				<?php
				// popup used to select the ordering
				if ($this->orderingFilter)
				{
					// display the hidden box that contains the sortable fields
					?>
					<div class="vap-emplist-ordering-fields">
						<ul>
							<?php
							foreach ($this->availableOrderings as $ord)
							{
								if (empty($this->requestFilters['service']) && in_array($ord, array(7, 8)))
								{
									// it is not possible to sort by rate if the service hasn't been selected
									continue;
								}

								$url = $filtersUri . '&ordering=' . $ord;
								?>

								<li class="<?php echo $ord == $this->ordering ? 'selected' : ''; ?>">
									<?php
									if ($ord != $this->ordering)
									{
										// create a link to sort the employees using this type
										?>
										<a href="<?php echo $url; ?>"><?php echo JText::_('VAPEMPORDERING' . $ord); ?></a>
										<?php
									}
									else
									{
										// this sortable type is already selected
										?>
										<span><?php echo JText::_('VAPEMPORDERING' . $ord); ?></span>
										<?php
									}
									?>
								</li>

							<?php } ?>
						</ul>
					</div>
					<?php
				}
				// end popup
				?>

			</div>
			<?php
		}
		?>

	</div>

	<?php
	/**
	 * End of toolbar top box.
	 */

	// check if the customer is filtering the employees list
	if ($this->filtersInRequest)
	{
		// we need to show a response about the filtered used
		?>
		<div class="vap-empfilters_response">
			<?php
			if ($this->employeesCount > 0)
			{
				// we found something while searching with the given filters
				?>
				<span class="success-result">
					<?php
					if ($this->employeesCount > 1)
					{
						// the search found more than one employee
						echo JText::sprintf('VAPEMPLISTRESULTPLUS', $this->employeesCount);
					}
					else
					{
						// the search found only one employee
						echo JText::_('VAPEMPLISTRESULT1');
					}
					?>
				</span>
				<?php
			}
			else
			{
				// no employee found while searching with the given filters
				?>
				<span class="bad-result">
					<?php echo JText::_('VAPEMPLISTRESULT0'); ?>
				</span>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>
</div>

<?php
JText::script('VAPEMPALLGROUPSOPTION');
?>

<script>
	
	jQuery(document).ready(function() {

		jQuery('.vap-empgroup-sel').select2({
			placeholder: Joomla.JText._('VAPEMPALLGROUPSOPTION'),
			allowClear: true,
			width: 300
		});

		jQuery('.vap-empgroup-sel').on('change', function() {
			jQuery('#vap-empgroup-form').submit();
		});

	});

	function vapDisplayFilters(button) {
		var fields = jQuery('.vap-emplist-ordering-fields');
		if (!fields.is(':visible')) {
			fields.slideDown();
			jQuery(button).addClass('active');
		} else {
			fields.slideUp();
			jQuery(button).removeClass('active');
		}
	}

</script>
