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

JHtml::_('behavior.modal');

$def_val = array(
	'ORI_W' => VikAppointments::getOriginalWidthResize(true),
	'ORI_H' => VikAppointments::getOriginalHeightResize(true),
	'SML_W' => VikAppointments::getSmallWidthResize(true),
	'SML_H' => VikAppointments::getSmallHeightResize(true),
	'ISRES' => VikAppointments::isImageResize(true),
);

$vik = UIApplication::getInstance();

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">

	<div class="span6">
		<?php echo $vik->openFieldset(JText::_('VAPMANAGEMEDIATITLE1'), 'form-horizontal'); ?>

			<div class="control-group">
				
				<div class="vap-media-droptarget">
					<p class="icon">
						<i class="fa fa-upload" style="font-size: 48px;"></i>
					</p>

					<div class="lead">
						<a href="javascript: void(0);" id="upload-file"><?php echo JText::_('VAPMANUALUPLOAD'); ?></a>&nbsp;<?php echo JText::_('VAPMEDIADRAGDROP'); ?>
					</div>

					<p class="maxsize">
						<?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', 
						JHtml::_('number.bytes', ini_get('upload_max_filesize'), 'auto', 0)
						); ?>
					</p>

					<input type="file" id="legacy-upload" style="display: none;"/>
				</div>

			</div>

		<?php echo $vik->closeFieldset(); ?>
	</div>
	
	<div class="span6">
		<?php echo $vik->openFieldset(JText::_('VAPMEDIAPROPBOXTITLE'), 'form-horizontal'); ?>
			
			<!-- RESIZE - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $def_val['ISRES'] == 1, 'onClick="resizeStatusChanged(1);"');
			$elem_no = $vik->initRadioElement('', '', $def_val['ISRES'] == 0, 'onClick="resizeStatusChanged(0);"');
			?>
			<?php echo $vik->openControl(JText::_("VAPMANAGEMEDIA8").':'); ?>
				<?php echo $vik->radioYesNo('is_res', $elem_yes, $elem_no, false); ?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- ORIGINAL SIZE - Properties -->
			<?php echo $vik->openControl(JText::_("VAPMANAGEMEDIA6").':'); ?>
				<span>
					<?php echo JText::_('VAPMANAGEMEDIA9'); ?>
					<input type="number" id="vaporiw" name="oriwres" value="<?php echo $def_val['ORI_W']; ?>" size="4" min="64" max="9999" <?php echo ($def_val['ISRES'] ? '' : 'readonly'); ?> />&nbsp;px&nbsp;
					<?php echo JText::_('VAPMANAGEMEDIA10'); ?>
					<input type="number" id="vaporih" name="orihres" value="<?php echo $def_val['ORI_H']; ?>" size="4" min="64" max="9999" <?php echo ($def_val['ISRES'] ? '' : 'readonly'); ?> />&nbsp;px
				</span>
			<?php echo $vik->closeControl(); ?>
			
			<!-- THUMBNAIL SIZE - Properties -->
			<?php echo $vik->openControl(JText::_("VAPMANAGEMEDIA7").':'); ?>
				<span>
					<?php echo JText::_('VAPMANAGEMEDIA9'); ?>
					<input type="number" name="smallwres" value="<?php echo $def_val['SML_W']; ?>" size="4" min="16" max="1024" />&nbsp;px&nbsp;
					<?php echo JText::_('VAPMANAGEMEDIA10'); ?>
					<input type="number" name="smallhres" value="<?php echo $def_val['SML_H']; ?>" size="4" min="16" max="1024" />&nbsp;px
				</span>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeFieldset(); ?>
	</div>

	<div class="span12" style="display: none;margin-left: 0px;" id="vap-uploads">
		<?php echo $vik->openFieldset(JText::_('VAPMANAGEMEDIATITLE3'), 'form-horizontal'); ?>
			<div class="control" id="vap-uploads-cont"></div>
		<?php echo $vik->closeFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="-1"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="option" value="com_vikappointments"/>
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

	});

	function resizeStatusChanged(is) {
		jQuery('#vaporiw').prop('readonly', ((is) ? false : true) );
		jQuery('#vaporih').prop('readonly', ((is) ? false : true) );
	}	
	
	// upload
	
	function execUploads(files) {
		jQuery('#vap-uploads').show();
		
		for (var i = 0; i < files.length; i++) {
			if (isAnImage(files[i].name)) {
				var status = new createStatusBar();
				status.setFileNameSize(files[i].name, files[i].size);
				status.setProgress(0);
				
				jQuery('#vap-uploads-cont').append(status.getHtml());
				
				fileUploadThread(status, files[i]);
			}
		}
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
				sizeStr = sizeMB.toFixed(2)+" MB";
			} else if (size > 1024) {
				var sizeKB = size/1024;
				sizeStr = sizeKB.toFixed(2)+" kB";
			} else {
				sizeStr = size.toFixed(2)+" B";
			}
	 
			this.filename.html(name);
			this.size.html(sizeStr);
		}
		
		this.setProgress = function(progress) {       
			var progressBarWidth = progress*this.progressBar.width()/100;  
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
		}
		
		this.setAbort = function(jqxhr) {
			var bar = this.progressBar;
			this.abort.click(function() {
				jqxhr.abort();
				this.hide();
				bar.find('div').addClass('aborted').css('width', '100%');
			});
		}
		
		this.getHtml = function() {
			return this.statusbar;
		}
	}

	var formData = null;
	
	function fileUploadThread(status, file) {
		jQuery.noConflict();

		<?php
		/**
		 * The form didn't pass the correct values to 
		 * resize the images.
		 *
		 * @since 1.6.1
		 */
		?>
		
		formData = new FormData();
		formData.append('image', file);
		formData.append('oriwres', jQuery('input[name="oriwres"]').val());
		formData.append('orihres', jQuery('input[name="orihres"]').val());
		formData.append('smallwres', jQuery('input[name="smallwres"]').val());
		formData.append('smallhres', jQuery('input[name="smallhres"]').val());
		formData.append('is_res', jQuery('input[name="is_res"]').is(':checked') ? 1 : 0);
		
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
			url: 'index.php?option=com_vikappointments&task=uploadimageajax&tmpl=component',
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
					status.progressBar.find('div').addClass('aborted');
				}
			}
		}); 
	 
		status.setAbort(jqxhr);
	}
	
	function isAnImage(name) {
		return name.toLowerCase().match(/\.(jpg|jpeg|png|gif)$/);
	}
	
</script>
