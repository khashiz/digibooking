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

$auth 		= $this->auth;
$employee 	= $auth->getEmployee();

$service = $this->service;

$vik = UIApplication::getInstance();

$editor = JFactory::getEditor();

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
		<h2><?php echo JText::_('VAPEDITSERTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manageServicesRates()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveService(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveService(1);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>

		<?php if ($auth->remove()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveService();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseService();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditservice'); ?>" method="post" enctype="multipart/form-data" name="empareaForm" id="empareaForm">

	<?php echo $vik->bootStartTabSet('set', array('active' => 'set_details')); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('set', 'set_details', JText::_('VAPORDERTITLE2')); ?>

			<?php echo $vik->openEmptyFieldset(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE2').':'); ?>
					<input type="text" value="<?php echo $this->escape($service["name"]); ?>" size="40" id="vapname" readonly />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE4').':'); ?>
					<input type="number" name="duration" value="<?php echo $service["duration"]; ?>" size="10" min="1" max="99999999" id="vapdurationinput" />
					<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE16').':'); ?>
					<input type="number" name="sleep" value="<?php echo $service["sleep"]; ?>" size="10" min="-9999999" max="99999999" id="vapsleepinput" />
					<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITSERVICE5').':'); ?>
					<input type="number" name="price" value="<?php echo $service["rate"]; ?>" size="10" min="0" max="99999999" step="any" />
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

		<!-- DESCRIPTION -->

		<?php echo $vik->bootAddTab('set', 'set_description', JText::_('VAPEDITSERVICE3')); ?>
			
			<?php echo $vik->openEmptyFieldset(); ?>
				
				<div class="control-group">
					<?php echo $editor->display('description', $service['assoc_desc'], 400, 200, 70, 20); ?>
				</div>
			
			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" />
	
	<input type="hidden" name="id" value="<?php echo $service['id']; ?>" />
	<input type="hidden" name="task" value="empeditservice.saveRate" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<?php
JText::script('VAPCONFDIALOGMSG');
?>

<script>
	
	function vapCloseService() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empserviceslist&Itemid=' . $itemid, false); ?>';
	}

	var validator = new VikFormValidator('#empareaForm');
	
	<?php if ($auth->manageServicesRates()) { ?>
		
		function vapSaveService(close) {
			
			callback = function() {

				var dInput = jQuery('input[name="duration"]');
				var sInput = jQuery('input[name="sleep"]');
				
				var duration 	= parseInt(dInput.val());
				var sleep 		= parseInt(sInput.val());

				if (isNaN(duration)) {
					duration = 0;
				}

				if (isNaN(sleep)) {
					sleep = 0;
				}

				if (duration > 0 && duration + sleep > 5 ) {
					validator.unsetInvalid(dInput);
					return true;
				}

				validator.setInvalid(dInput);
				return false;
			}

			if (validator.validate(callback)) {
				if (close) {
					jQuery('#vaphiddenreturn').val('1');
				}
				
				document.empareaForm.submit();
			}
		}

	<?php } ?>

	<?php if ($auth->remove()) { ?>

		function vapRemoveService() {

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			document.location.href = '<?php echo JRoute::_("index.php?option=com_vikappointments&task=empeditservice.delete&cid[]=" . $service['id'] . "&Itemid=" . $itemid, false); ?>';
		}

	<?php } ?>
	
</script>
