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

$editor = JFactory::getEditor();

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
		<h2><?php echo JText::_('VAPEDITEMPTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manage()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveProfile(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveProfile(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>
		<div class="vapempbtn">
			<button type="button" onClick="vapCloseProfile();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditprofile' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" enctype="multipart/form-data" name="empareaForm" id="empareaForm">

	<?php echo $vik->bootStartTabSet('set', array('active' => 'set_details')); ?>
		
		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('set', 'set_details', JText::_('VAPORDERTITLE2')); ?>
			
			<?php echo $vik->openEmptyFieldset(); ?>

				<?php echo $vik->openControl(JText::_('VAPEDITEMPLOYEE2').'*:'); ?>
					<input type="text" name="firstname" value="<?php echo $employee['firstname']; ?>" size="40" class="required" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITEMPLOYEE3').'*:'); ?>
					<input type="text" name="lastname" value="<?php echo $employee['lastname']; ?>" size="40" class="required" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITEMPLOYEE4').'*:'); ?>
					<input type="text" name="nickname" value="<?php echo $this->escape($employee['nickname']); ?>" size="40" class="required" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITEMPLOYEE6').'*:'); ?>
					<span>
						<input type="text" name="email" value="<?php echo $employee['email']; ?>" size="40" class="required" />
						<label for="vapnotifybox"><?php echo JText::_('VAPEDITEMPLOYEE7');?>:</label>
						<input type="checkbox" id="vapnotifybox" name="notify" value="1" <?php echo (($employee['notify']) ? 'checked="checked"' : '' ); ?> class="required" />
					</span>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITEMPLOYEE8').'*:'); ?>
					<span>
						<input type="text" name="phone" value="<?php echo $employee['phone']; ?>" size="40" class="required" />
						<label for="vapshophonebox"><?php echo JText::_('VAPEDITEMPLOYEE14');?>:</label>
						<input type="checkbox" id="vapshophonebox" name="showphone" value="1" <?php echo (($employee['showphone']) ? 'checked="checked"' : '' ); ?> />
					</span>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITEMPLOYEE16').':'); ?>
					<select name="id_group" class="vap-group-select">
						<option value=""></option>
						
						<?php foreach ($this->groups as $g) { ?>
							
							<option value="<?php echo $g['id']; ?>" <?php echo ($g['id'] == $employee['id_group'] ? 'selected="selected"' : ''); ?>><?php echo $g['name']; ?></option>
						
						<?php } ?>

					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITEMPLOYEE15').':'); ?>
					<select name="quick_contact"class="vik-dropdown-small">
						<option value="1" <?php echo ($employee['quick_contact'] == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPYES'); ?></option>
						<option value="0" <?php echo ($employee['quick_contact'] == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPNO'); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITEMPLOYEE5').':'); ?>
					<span id="vapimagesp">
						<input type="file" name="image" size="40">
						<?php if (!empty($employee['image']) && file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $employee['image'])) { ?>
							<a href="javascript: void(0);" class="vapmodal" onClick="vapOpenModalImage('<?php echo VAPMEDIA_URI . $employee['image']; ?>');"><?php echo $employee['image']; ?></a>
						<?php } ?>
					</span>
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

		<?php if (count($this->fields)) { ?>

			<!-- CUSTOM FIELDS -->

			<?php echo $vik->bootAddTab('set', 'set_fields', JText::_('VAPMANAGERESERVATIONTITLE2')); ?>
			
				<?php echo $this->loadTemplate('fields'); ?>

			<?php echo $vik->bootEndTab(); ?>

		<?php } ?>

		<!-- DESCRIPTION -->

		<?php echo $vik->bootAddTab('set', 'set_description', JText::_('VAPEDITEMPLOYEE9')); ?>
			
			<?php echo $vik->openEmptyFieldset(); ?>
				
				<div class="control-group">
					<?php echo $editor->display('note', $employee['note'], 400, 200, 70, 20 ); ?>
				</div>
			
			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" /> 
	
	<input type="hidden" name="task" value="empeditprofile.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<?php
JText::script('VAPSERVICENOGROUP');
?>

<script>

	jQuery(document).ready(function() {

		jQuery('.vap-group-select').select2({
			placeholder: Joomla.JText._('VAPSERVICENOGROUP'),
			allowClear: true,
			width: 300
		});

		jQuery('.vik-dropdown-small').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100,
		});

	});
	
	function vapCloseProfile() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false); ?>';
	}
	
	<?php if ($auth->manage()) { ?>

		var validator = new VikFormValidator('#empareaForm');

		function vapSaveProfile(close) {
			
			if (validator.validate()) {
				if(close) {
					jQuery('#vaphiddenreturn').val('1');
				}
				
				document.empareaForm.submit();
			}
		}

	<?php } ?>
	
</script>
