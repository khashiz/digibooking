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

$rows = $this->rows;

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$default_tz = date_default_timezone_get();

?>

<style type="text/css">

	body {
		font-size: 14px !important;
		font-family: courier !important;
	}
	
	.separator {
		width: 100%;
		height: 3px;
		border-bottom: 1px dashed black;
		margin-top: 7px;
		margin-bottom: 10px;
	}

</style>

<?php

$first = true;

foreach ($rows as $r)
{	
	$fields = json_decode($r['custom_f'], true);

    if (empty($r['timezone']))
    {
        $r['timezone'] = $default_tz;
    }
    VikAppointments::setCurrentTimezone($r['timezone']);
	
	if (!$first)
	{
		?><div class="separator"></div><?php
	}

	$first = false;

	?>
	
	<div><?php echo $r['id'] . '-' . $r['sid']; ?></div>

	<div><?php echo ArasJoomlaVikApp::jdate($date_format . ' ' . $time_format, $r['checkin_ts']); ?></div>

	<div><?php echo $r['sname'] . ': ' . VikAppointments::printPriceCurrencySymb($r['total_cost']) . ' - ' . $r['duration'] . ' ' . JText::_('VAPSHORTCUTMINUTE'); ?></div>
	
	<?php if ($r['view_emp']) { ?>
	    <div><?php echo $r['ename']; ?></div>
    <?php } ?>

    <?php if ($r['people'] > 1) { ?>
		<div><?php echo JText::_('VAPMANAGERESERVATION25') . ': ' . $r['people']; ?></div>
	<?php } ?>

	<br />

	<?php foreach ($fields as $k => $v)
	{ 
		if (!empty($v))
		{ 
			?><div><?php echo JText::_($k) . ': ' . $v; ?></div><?php
		} 
	}

	// if the record contains at least an option, enter a <br> between the options and the customer info
	if ($r['options'])
	{
		?><br /><?php

		foreach ($r['options'] as $opt)
		{
			$_str = $opt['name'] . (strlen($opt['var_name']) ? ' - ' . $opt['var_name'] : '');
			$_str .= ' x' . $opt['quantity'] . ' ' . VikAppointments::printPriceCurrencySymb($opt['price']);

			?><div><?php echo $_str; ?></div><?php
		}
	}
} 

?>

<script>
	
	jQuery(document).ready(function() {
		window.print();
	});
	
</script>
	