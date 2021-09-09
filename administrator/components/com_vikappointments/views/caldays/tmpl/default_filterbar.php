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

JHtml::_('formbehavior.chosen');
ArasJoomlaVikApp::datePicker('vikappointments',false,true,true);
$vik 	= UIApplication::getInstance();
$config = UIFactory::getConfig();

if ($this->layout == 'day')
{
	$_rules = array(
		'-1 week',
		'-1 day',
		'+1 day',
		'+1 week',
	);
}
else
{
	$_rules = array(
		'-1 month',
		'-1 week',
		'+1 week',
		'+1 month',
	);
}

$date_rules = array();
//var_dump($this->filters['date']);
foreach ($_rules as $r)
{
	$date_rules[] = ArasJoomlaVikApp::jdate($config->get('dateformat'), strtotime($r, $this->filters['date']));
}

 

?>

<div class="btn-toolbar" style="height: 42px;">

	<div class="btn-group pull-left input-prepend input-append">

		<button type="button" class="btn" onclick="updateCurrentDate('<?php echo $date_rules[0]; ?>');"><i class="fa fa-angle-double-left"></i></button>
		<button type="button" class="btn" onclick="updateCurrentDate('<?php echo $date_rules[1]; ?>');" style="border-top-right-radius: 0;border-bottom-right-radius: 0;"><i class="fa fa-angle-left"></i></button>

		<?php
		$attrs = array();
		$attrs['onChange'] = 'updateCurrentDate(\'\');';
		echo $vik->calendar($this->filters['date'], 'dayfrom', null, null, $attrs); 
		?>

		<button type="button" class="btn" onclick="updateCurrentDate('<?php echo $date_rules[2]; ?>');"><i class="fa fa-angle-right"></i></button>
		<button type="button" class="btn" onclick="updateCurrentDate('<?php echo $date_rules[3]; ?>');"><i class="fa fa-angle-double-right"></i></button>

	</div>

	<?php
	if ($this->layout != 'day')
	{
		?>
		<div class="btn-group pull-left">
			<?php
			$options = array();
			$options[] = JHtml::_('select.option', 0, JText::_('VAPFINDRESALLEMPLOYEES'));
			foreach ($this->employees as $e)
			{
				$options[] = JHtml::_('select.option', $e->id, $e->nickname);
			}
			?>
			<select name="employee" id="vap-employee-sel" onchange="employeeValueChanged();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $this->filters['employee']); ?>
			</select>
		</div>
		<?php
	}
	?>

	<div class="btn-group pull-right">
		<button type="button" class="btn" onClick="vapSwitchLayout();"><?php echo JText::_('VAPSWITCHTOCALVIEW'); ?></button>
	</div>

</div>

<input type="hidden" name="date" value="<?php echo date($config->get('dateformat'), $this->filters['date']); ?>" />

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});

	function vapSwitchLayout() {
		document.location.href = 'index.php?option=com_vikappointments&task=switchcal&layout=calendar';
	}

	// handle calendar inputs

	function updateCurrentDate(rule) {
		if (!rule.length) {
			rule = jQuery('#dayfrom').val();
		}

		document.adminForm.date.value = rule;
		document.adminForm.submit();
	}

	function employeeValueChanged() {
		jQuery('#adminForm').append('<input type="hidden" name="employee_changed" value="1" />');
		document.adminForm.submit();
	}

</script>
