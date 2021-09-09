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

<!-- INSTRUCTIONS - Label -->
<tr>
	<td width="200" class="adminparamcol" style="vertical-align: top;"> <b><?php echo JText::_('VAPMANAGECONFIGCRON8');?></b> </td>
	<td>
		<p>A cron job can be scheduled at server-level by following the instructions below:</p>

		<ol>
			<li>Create a cron job through the <b>Cron Jobs List</b> in the settings tab.</li>
			<li>Access your server control panel (e.g. <em>CPanel</em> or <em>Siteworx</em>) in order to schedule a job.</li>
			<li>Visit the cron job section and click the button that will be used to create a new schedule.</li>
			<li>Select an interval that should be suitable to your needs (e.g. every 30 minutes).</li>
			<li>Assign the cron job to the <b>wp-cron.php</b> file located in the root of your website (an absolute path should be used here).</li>
		</ol>

		<p>If you have no idea how to schedule a cron job through the control panel of your server, you should contact your hosting provider.</p>

		<p>Note that, even if you don't schedule a server cron, the jobs that you created from VikAppointments are still periodically executed, but only in case your website is visited by some users.<br />
		A server cron job is indeed used to prevent this limitation, as the server periodically invokes itself, just to simulate a user ping/visit.</p>
	</td>
</tr>
