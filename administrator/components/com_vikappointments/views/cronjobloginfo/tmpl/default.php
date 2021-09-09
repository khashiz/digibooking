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

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$dt_format = $config->get('dateformat') . ' @ ' . $config->get('timeformat') . ':s';

?>

<div class="vap-cron-loginfo-head">
	<div class="vap-cron-loginfo-head-left">
		<strong><?php echo date($dt_format, $this->row['createdon']); ?></strong>
	</div>
	<div class="vap-cron-loginfo-head-right">
		<span class="vapreservationstatus<?php echo ($this->row['status'] ? 'confirmed' : 'removed'); ?>">
			<?php echo JText::_('VAPCRONLOGSTATUS' . ($this->row['status'] ? 'OK' : 'ERROR')); ?>
		</span>
	</div>
</div>

<div class="vap-cron-loginfo-middle">
	<pre><?php echo $this->row['content']; ?></pre>
</div>
