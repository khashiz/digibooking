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

$auth       = $this->auth;
$employee   = $auth->getEmployee();

$attached = $this->attached;
$services = $this->services;

$vik = UIApplication::getInstance();

$itemid = JFactory::getApplication()->input->getInt('Itemid');

?>
	
<?php
/**
 * The employees area toolbar is displayed from the layout below:
 * /components/com_vikappointments/layouts/emparea/toolbar.php
 * 
 * If you need to change something from this layout, just create
 * an override of this layout by following the instructions below:
 * - open the back-end of your Joomla
 * - visit the Extensions > Templates > Templates page
 * - edit the active template
 * - access the "Create Overrides" tab
 * - select Layouts > com_vikappointments > emparea
 * - start editing the toolbar.php file on your template to create your own layout
 *
 * @since 1.6
 */
echo JLayoutHelper::render('emparea.toolbar', array('active' => false));
?>

<div class="vapeditempheaderdiv">
	<div class="vapeditemptitlediv">
		<h2><?php echo JText::_('VAPNEWSERTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<div class="vapempbtn">
			<button type="button" onClick="vapSaveService(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
		</div>
	
		<div class="vapempbtn">
			<button type="button" onClick="vapSaveService(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
		</div>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseServices();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empattachser'); ?>" method="post" name="empareaForm" id="empareaForm">
	
	<div class="vap-newservices-tip"><?php echo JText::_('VAPEMPATTACHSERTIP'); ?></div>

	<div class="vap-newservices-wrapper">

		<select name="services[]" id="vap-services-sel" class="required" multiple>

			<?php foreach ($services as $group) { ?>

				<optgroup label="<?php echo $group['name'] ? $group['name'] : '--'; ?>">

					<?php foreach ($group['services'] as $service) { ?>

						<option
							value="<?php echo $service['id']; ?>"
							<?php echo in_array($service['id'], $attached) ? 'disabled="disabled"' : ''; ?>
						><?php echo $service['name']; ?></option>

					<?php } ?>

				</optgroup>

			<?php } ?>

		</select>

	</div>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" />
	
	<input type="hidden" name="task" value="empattachser.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<script>
	
	function vapCloseServices() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empserviceslist&Itemid=' . $itemid); ?>';
	}

	var validator = new VikFormValidator('#empareaForm');
	validator.setLabel(jQuery('#vap-services-sel'), '.vap-newservices-tip');

	function vapSaveService(close) {
		
		if (validator.validate()) {
			if(close) {
				jQuery('#vaphiddenreturn').val('1');
			}
			
			document.empareaForm.submit();
		}
	}

	jQuery(document).ready(function() {

		jQuery('#vap-services-sel').select2({
			allowClear: true,
			width: '100%',
		});

	});
	
</script>
