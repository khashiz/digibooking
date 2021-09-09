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

$filename = '';
if ($this->file)
{
	$filename = basename($this->file);
}

?>

<div class="span12" style="margin-top: 15px;display: none;" id="vap-uploads">
	<?php echo $vik->openEmptyFieldset(); ?>
		
		<div class="control-group" id="vap-uploads-cont"></div>

	<?php echo $vik->closeEmptyFieldset(); ?>
</div>

<div class="span12" style="margin-top: 15px;margin-left: 0px;">
	<?php echo $vik->openEmptyFieldset(); ?>

		<div class="control-group">
			
			<div class="vap-media-droptarget">
				<p class="icon">
					<i class="fa fa-upload" style="font-size: 48px;"></i>
				</p>

				<div class="lead">
					<a href="javascript: void(0);" id="upload-file"><?php echo JText::_('VAPMANUALUPLOAD'); ?></a>&nbsp;<?php echo JText::_('VAPCSVDRAGDROP'); ?>
				</div>

				<p class="maxsize">
					<?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', 
					JHtml::_('number.bytes', ini_get('upload_max_filesize'), 'auto', 0)
					); ?>
				</p>

				<input type="file" id="legacy-upload" style="display: none;"/>
			</div>

		</div>

	<?php echo $vik->closeEmptyFieldset(); ?>
</div>

<form action="index.php" method="post" name="adminForm">

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="import_type" value="<?php echo $this->type; ?>" />

	<?php foreach ($this->args as $k => $v) { ?>

		<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>" />
		<input type="hidden" name="import_args[<?php echo $k; ?>]" value="<?php echo $v; ?>" />
		
	<?php } ?>

</form>

<script>

	jQuery(document).ready(function() {

		var dragCounter = 0;

		// drag&drop actions on target div

		jQuery('.vap-media-droptarget').on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
			e.preventDefault();
			e.stopPropagation();
		});

		jQuery('.vap-media-droptarget').on('dragenter', function(e) {
			// increase the drag counter because we may
			// enter into a child element
			dragCounter++;

			jQuery(this).addClass('drag-enter');
		});

		jQuery('.vap-media-droptarget').on('dragleave', function(e) {
			// decrease the drag counter to check if we 
			// left the main container
			dragCounter--;

			if (dragCounter <= 0) {
				jQuery(this).removeClass('drag-enter');
			}
		});

		jQuery('.vap-media-droptarget').on('drop', function(e) {

			jQuery(this).removeClass('drag-enter');
			
			var files = e.originalEvent.dataTransfer.files;
			
			execUploads(files);
			
		});

		jQuery('.vap-media-droptarget #upload-file').on('click', function() {

			jQuery('input#legacy-upload').trigger('click');

		});

		jQuery('input#legacy-upload').on('change', function() {
			
			execUploads(jQuery(this)[0].files);

		});

		<?php if ($this->file) { ?>

			jQuery('#vap-uploads').show();

			var status = new createStatusBar();
			status.setFileNameSize(
				'<?php echo substr($filename, strpos($filename, '_') + 1); ?>',
				parseFloat('<?php echo filesize($this->file); ?>')
			);
			
			jQuery('#vap-uploads-cont').append(status.getHtml());

			// this avoids to auto-aubmit the form
			FILE_UPLOADED = true;

			status.setProgress(100);
			status.complete();

		<?php } ?>

	});

	var FILE_UPLOADED = false;
	var IS_UPLOADING = false;

	Joomla.submitbutton = function(task) {

		if (task == 'manageimport' && !FILE_UPLOADED) {
			alert('<?php echo addslashes(JText::_('VAPIMPORTCSVUPLOADALERT')); ?>');

			return false;
		} else if (task == 'downloadSampleImport') {

			document.adminForm.target = '_blank';

		}	
		
		Joomla.submitform(task, document.adminForm);
		document.adminForm.target = '';
	}

	// upload
	
	function execUploads(files) {

		if (IS_UPLOADING) {
			return false;
		}
		
		for (var i = 0; i < files.length; i++) {
			// search for the first CSV file
			if (isCsv(files[i].name)) {
				FILE_UPLOADED = false;
				IS_UPLOADING = true;

				jQuery('#vap-uploads').show();

				var status = new createStatusBar();
				status.setFileNameSize(files[i].name, files[i].size);
				status.setProgress(0);

				jQuery('#vap-uploads-cont').html(status.getHtml());
				
				fileUploadThread(status, files[i]);

				return true;
			}
		}

		return false;
	}
	
	var fileCount = 0;
	function createStatusBar() {
		fileCount++;
		this.statusbar = jQuery("<div class='vap-progressbar-status'></div>");
		this.filename = jQuery("<div class='vap-progressbar-filename'></div>").appendTo(this.statusbar);
		this.size = jQuery("<div class='vap-progressbar-filesize'></div>").appendTo(this.statusbar);
		this.progressBar = jQuery("<div class='vap-progressbar'><div></div></div>").appendTo(this.statusbar);
		this.abort = jQuery("<div class='vap-progressbar-abort'>Abort</div>").appendTo(this.statusbar);
		this.statusinfo = jQuery("<div class='vap-progressbar-info' style='display:none;'><?php echo addslashes(JText::_('VAPMANAGEMEDIA11')); ?></div>").appendTo(this.statusbar);
		this.completed = false;
	 
		this.setFileNameSize = function(name, size) {
			var sizeStr = "";
			
			if (size > 1024*1024) {
				var sizeMB = size/(1024*1024);
				sizeStr = sizeMB.toFixed(2) + " MB";
			} else if (size > 1024) {
				var sizeKB = size/1024;
				sizeStr = sizeKB.toFixed(2) + " kB";
			} else {
				sizeStr = size.toFixed(2) + " B";
			}
	 
			this.filename.html(name);
			this.size.html(sizeStr);
		}
		
		this.setProgress = function(progress) {       
			var progressBarWidth = progress * this.progressBar.width() / 100;  
			
			this.progressBar.find('div').css('width', progressBarWidth+'px').html(progress + "% ");
			
			if (parseInt(progress) >= 100) {
				if (!this.completed) {
					this.abort.hide();
					this.statusinfo.show();
				}
			}
		}
		
		this.complete = function() {
			this.completed = true;
			this.abort.hide();
			this.statusinfo.hide();
			this.setProgress(100);
			this.progressBar.find('div').addClass('completed');

			if (!FILE_UPLOADED) {
				Joomla.submitform('manageimport', document.adminForm);			
			} else {
				this.progressBar.hide();
				this.statusinfo.html('<a href="javascript: void(0);" onclick="deleteExistingFile();">\n'+
					'<i class="fa fa-times"></i> <?php echo addslashes(JText::_('VAPDELETE')); ?>\n'+
				'</a>').css('float', 'right');
				this.statusinfo.show();
			}

			FILE_UPLOADED = true;
			IS_UPLOADING = false;
		}
		
		this.setAbort = function(jqxhr) {
			var bar = this.progressBar;
			
			this.abort.click(function() {
				jqxhr.abort();
				this.hide();
				bar.find('div').addClass('aborted').css('width', '100%');

				IS_UPLOADING = false;
			});
		}
		
		this.getHtml = function() {
			return this.statusbar;
		}
	}
	
	function fileUploadThread(status, file) {
		jQuery.noConflict();
		
		var formData = new FormData();
		formData.append('source', file);
		formData.append('import_type', '<?php echo $this->type; ?>');
		
		var jqxhr = jQuery.ajax({
			xhr: function() {
				var xhrobj = jQuery.ajaxSettings.xhr();
				if (xhrobj.upload) {
					xhrobj.upload.addEventListener('progress', function(event) {
						var percent = 0;
						var position = event.loaded || event.position;
						var total = event.total;
						if (event.lengthComputable) {
							percent = Math.ceil(position / total * 100);
						}
						//Set progress
						status.setProgress(percent);
					}, false);
				}
				return xhrobj;
			},
			url: 'index.php?option=com_vikappointments&task=uploadimportajax&tmpl=component',
			type: 'POST',
			contentType:false,
			processData: false,
			cache: false,
			data: formData,
			success: function(resp){
				var obj = jQuery.parseJSON(resp);
				
				status.complete();
				
				if (obj[0]) {
					status.filename.html(obj[1]);
				} else {
					console.log(obj);
					status.progressBar.find('div').addClass('aborted');
				}
			}
		}); 
	 
		status.setAbort(jqxhr);
	}
	
	function isCsv(name) {
		return name.toLowerCase().match(/\.csv$/);
	}

	function deleteExistingFile() {

		Joomla.submitform('deleteImportedFiles', document.adminForm);

	}

</script>
