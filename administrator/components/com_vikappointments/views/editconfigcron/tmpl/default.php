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

$params = $this->params;

$session = JFactory::getSession();
$_selected_tab_view = $session->get('vaptabactive', 1, 'vapconfigcron');

$vik = UIApplication::getInstance();

?>

<div id="navigation">
	<ul>
		<?php for( $i = 1; $i <= 2; $i++ ) { ?>
			<li id="vaptabli<?php echo $i; ?>" class="vaptabli<?php echo (($_selected_tab_view == $i) ? ' vapconfigtabactive' : ''); ?>"><a href="javascript: changeTabView(<?php echo $i; ?>);"><?php echo JText::_('VAPCONFIGCRONTITLE'.$i); ?></a></li>
		<?php } ?>
	</ul>
</div>

<?php
// print config search bar
UILoader::import('libraries.widget.layout');
echo UIWidgetLayout::getInstance('searchbar')->display();
?>

<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data">
	
	<!-- CRON JOBS SECTION -->
	
	<div id="vaptabview1" class="vaptabview" style="<?php echo (($_selected_tab_view != 1) ? 'display: none;' : ''); ?>">
		
		<!-- Cron Jobs Settings -->
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPCONFIGCRONTITLE1'); ?></legend>
			<table class="admintable table" cellspacing="1">
				
				<!-- SECURE KEY - Text -->
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_('VAPMANAGECONFIGCRON1');?></b> </td>
					<td><input type="text" name="cron_secure_key" value="<?php echo $params['cron_secure_key']; ?>" size="32"/></td>
				</tr>
				
				<!-- REGISTER LOG - Dropdown -->
				<?php
				$elements = array(
					JHtml::_('select.option', 1, JText::_('VAPMANAGECONFIGCRON3')),
					JHtml::_('select.option', 2, JText::_('VAPMANAGECONFIGCRON4')),
				);
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_('VAPMANAGECONFIGCRON2');?></b> </td>
					<td>
						<select name="cron_log_mode" class="medium">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['cron_log_mode']); ?>
						</select>
					</td>
				</tr>
				
				<!-- CRON JOBS LIST - Button -->
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_('VAPMANAGECONFIGCRON5');?></b> </td>
					<td><button type="button" class="btn btn-success" onClick="document.location.href='index.php?option=com_vikappointments&task=cronjobs';"><?php echo JText::_('VAPMANAGECONFIGCRON6'); ?></button></td>
				</tr>
				
			</table>
		</fieldset>
		
	</div>

	<div id="vaptabview2" class="vaptabview" style="<?php echo (($_selected_tab_view != 2) ? 'display: none;' : ''); ?>">
		
		<!-- Cron Jobs Installation -->
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPCONFIGCRONTITLE2'); ?></legend>
			<table class="admintable table" cellspacing="1">

				<?php
				/**
				 * Display the instructions template depending
				 * on the current platform as there might be
				 * different steps to follow.
				 *
				 * @since 1.6.3
				 */
				echo $this->loadTemplate(VersionListener::getPlatform());
				?>

			</table>
		</fieldset>
		
	</div>
	
	<input type="hidden" name="task" value="" id="vapconfigtask"/>
	<input type="hidden" name="option" value="com_vikappointments"/>
</form>

<!-- SCRIPT -->

<script>

	jQuery(document).ready(function() {

		jQuery('select.medium').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 200
		});

	});
	
	// TAB LISTENER
	
	var last_tab_view = <?php echo $_selected_tab_view; ?>;
	
	function changeTabView(tab_pressed) {
		if( tab_pressed != last_tab_view ) {
			jQuery('.vaptabli').removeClass('vapconfigtabactive');
			jQuery('#vaptabli'+tab_pressed).addClass('vapconfigtabactive');
			
			jQuery('.vaptabview').hide();
			jQuery('#vaptabview'+tab_pressed).fadeIn('fast');
			
			storeTabSelected(tab_pressed);
			
			last_tab_view = tab_pressed;
		}
	}
	
	function storeTabSelected(tab) {
		jQuery.noConflict();
			
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=store_tab_selected&tmpl=component&group=vapconfigcron",
			data: { option: "com_vikappointments", task: "store_tab_selected", tab: tab, tmpl: "component" }
		}).done(function(resp){
			
		}).fail(function(resp){
			
		});
	}
	
</script>
