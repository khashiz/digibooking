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

// load tooltip behavior
JHtml::_('behavior.tooltip');

$vik = UIApplication::getInstance();

$code_mirror = $vik->getCodeMirror('code', $this->content);

?>

<form action="index.php" method="POST" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 48px;">
		<div class="btn-group pull-left">
			<button type="submit" class="btn btn-success" style="width: 120px;">
				<i class="icon-apply icon-white"></i>&nbsp;<?php echo JText::_('VAPSAVE'); ?>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn" onClick="saveAsCopyButtonPressed();">
				<i class="icon-save-copy"></i>&nbsp;<?php echo JText::_('VAPSAVEASCOPY'); ?>
			</button>
		</div>
	</div>
	
	<div class="control-group">
		<?php echo $code_mirror; ?>
	</div>
	
	<input type="hidden" name="file" value="<?php echo $this->filePath; ?>" />
	<input type="hidden" name="task" value="storefile" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
</form>

<div id="dialog-confirm" title="<?php echo JText::_('VAPEXPORTRES1');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-pencil" style="float: left; margin: 0 7px 20px 0;"></span>
		<span><input type="text" id="dialog-confirm-input" value="<?php echo $this->fileName; ?>"/></span>
	</p>
</div>

<?php
// make language translations available via javascript
JText::script('VAPNEWFILENAMETITLE');
?>

<script>
	
	function saveAsCopyButtonPressed() {

		var newname = prompt(Joomla.JText._('VAPNEWFILENAMETITLE'), '<?php echo addslashes($this->fileName); ?>');

		if (newname) {

			jQuery('#adminForm').append('<input type="hidden" name="newname" value="'+newname+'" />');
			jQuery('#adminForm').append('<input type="hidden" name="ascopy" value="1" />');
			jQuery('#adminForm').submit();

		}
	}
	
</script>
