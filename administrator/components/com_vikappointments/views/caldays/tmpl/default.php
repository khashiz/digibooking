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

?>

<form action="index.php" method="post" name="adminForm"  id="adminForm">
	
	<?php
	// load filter bar
	echo $this->loadTemplate('filterbar');
	?>

	<div id="employee_workdays">
		<div class="span8" style="margin-left: 0px;">
			<?php
			if ($this->layout == 'day')
			{
				// load day contents
				echo $this->loadTemplate('day');
			}
			else
			{
				// load calendar content
				echo $this->loadTemplate('calendar');
			}
			?>
		</div>

		<div class="span4" class="cal-sidebar">
			<?php
			// load sidebar content
			echo $this->loadTemplate('sidebar');
			?>
		</div>
	</div>

	<input type="hidden" name="mode" value="<?php echo $this->layout; ?>" />
	<input type="hidden" name="task" value="caldays" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<?php
// load modal content
echo $this->loadTemplate('modal');
?>

<script>

	Joomla.submitbutton = function(task) {
		if (task == 'backToCal') {
			// unset form data to retrieve the values stored in the user state
			document.adminForm.date.remove();

			document.adminForm.mode.value = '';
			task = 'caldays';
		} else if (task == 'reportsemp') {
			// Include employee ID for being used by reports view.
			// Include from=calendar in order to return to this view when exiting from reports view.
			jQuery('#adminForm').append(
				'<input type="hidden" name="cid[]" value="<?php echo $this->filters['employee']; ?>" />\n' +
				'<input type="hidden" name="from" value="calendar" />\n'
			);
		}

		Joomla.submitform(task, document.adminForm);
	}

</script>
