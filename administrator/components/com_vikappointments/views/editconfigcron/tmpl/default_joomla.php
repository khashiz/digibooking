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
		<p>To configure properly a cron job you have to follow the instructions below:</p>
		
		<ol>
			<li>Create a cron job from the "Cron Jobs List" in the settings tab.</li>
			<li>Pick the right cron job from the apposite dropdown and click on the "cron_runnable.php" link.</li>
			<li>Rename the downloaded file with a unique name.</li>
			<li>Create a new cron job from the control panel of your server and assign the downloaded file as script.</li>
		</ol>

		<p>If you have no idea how to create a new cron job from your control panel, you should contact your hosting provider.</p>
	</td>
</tr>

<!-- DOWNLOAD RUNNABLE FILE - Form -->
<?php 
$crons = array();
$crons[0] = array(JHtml::_('select.option', '', ''));

foreach ($this->cronJobs as $k => $list)
{
	$k = JText::_($k);

	$crons[$k] = array();
	
	foreach ($list as $c)
	{
		$crons[$k][] = JHtml::_('select.option', $c['id'], $c['name']);
	}
}
?>
<tr>
	<td width="200" class="adminparamcol"> <b><?php echo JText::_('VAPMANAGECONFIGCRON7');?></b> </td>
	<td>
		<?php if (count($this->cronJobs)) { ?>
			
			<?php
			$args = array(
				'id' 			=> 'vap-cron-select',
				'list.attr' 	=> array('class' => 'required'),
				'group.items' 	=> null,
				'list.select'	=> '',
			);
			echo JHtml::_('select.groupedList', $crons, '', $args);
			?>

			<a href="index.php?option=com_vikappointments&task=download_cron_installation_file&tmpl=component" onClick="return updateDownloadLink(this);" target="_blank">cron_runnable.php</a>
		
		<?php } else { ?>
			
			<?php echo JText::_('VAPCRONJOBERROR2'); ?>

		<?php } ?>
	</td>
</tr>

<!-- SCRIPT -->

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-cron-select').select2({
			placeholder: '--',
			allowClear: false,
			width: 300
		});

	});

	var validator = new VikFormValidator('#adminForm');

	function updateDownloadLink(link) {

		if (!validator.validate()) {
			return false;
		}

		var id = jQuery('#vap-cron-select').val();

		jQuery(link).attr( 'href', jQuery(link).attr('href')+"&id_cron="+id );

		return true;
	}
	
</script>
