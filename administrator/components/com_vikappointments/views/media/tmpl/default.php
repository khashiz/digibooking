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

$img_files = $this->imgFiles;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left input-append">
			<input type="text" name="keysearch" id="vapkeysearch" size="32" 
				value="<?php echo $this->keyFilter; ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />

			<button type="submit" class="btn">
				<i class="icon-search"></i>
			</button>
		</div>
		
		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>

		<div class="btn-group pull-right">
			<button type="button" class="btn" onClick="mediaSelectAll(1);">
				<?php echo JText::_('VAPMEDIASELECTALL'); ?>
			</button>
			<button type="button" class="btn" onClick="mediaSelectAll(0);">
				<?php echo JText::_('VAPMEDIASELECTNONE'); ?>
			</button>
		</div>

	</div>

<?php if (count($img_files) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOMEDIA');?></p>

<?php } else { ?>

	<div class="vap-mediaimg-filespool">

		<?php 
		$cont = 0;
		$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');
		
		foreach ($img_files as $img)
		{
			$cont++; 
			$img_name = basename($img);
			
			$short_name = $img_name;

			if (strlen($short_name) > 24)
			{
				$short_name = substr($short_name, 0, 16) . '...' . substr($short_name, strrpos($short_name, '.')-2);
			}
			
			$name_no_ext = substr($img_name, 0, strrpos($img_name, '.'));
			$img_ext 	 = substr($img_name, strrpos($img_name, '.'));
			
			$file_to_get = str_replace('media@small', 'media', $img);
			$file_prop 	 = AppointmentsHelper::getFileProperties($file_to_get, array('dateformat' => $dt_format));
			
			?>
			<div class="vap-mediaimg-block" id="vapblock<?php echo $cont; ?>">
				<div class="vap-mediaimg-innerblock">
					<div class="vap-mediaimg-wrapper" id="vapwrapper<?php echo $cont; ?>">
						<div class="vap-mediaimg-thumb">
							<div class="vap-mediaimg-thumbchild">
								<a href="javascript: void(0);" onClick="jQuery('#vapblock<?php echo $cont; ?>').addClass('hover');">
									<img src="<?php echo VAPMEDIA_SMALL_URI . $img_name; ?>" id="vap-media-img<?php echo $cont; ?>"/>
								</a>
							</div>
							<div class="vap-mediaimg-infos">
								<div class="vap-mediaimg-info pull-left"><?php echo $file_prop['creation']; ?></div>
								<div class="vap-mediaimg-info pull-right"><?php echo $file_prop['size']; ?></div>
							</div>
						</div>
						<div class="vap-mediaimg-controls">
							<div class="vap-mediaimg-control pull-left">
								<input type="checkbox" id="cb<?php echo $cont;?>" name="cid[]" class="vap-mediaimg-check" value="<?php echo $img_name; ?>" onChange="mediaCheckedAction(<?php echo $cont; ?>);<?php echo $vik->checkboxOnClick(); ?>">
								<label for="cb<?php echo $cont; ?>"><?php echo $short_name; ?></label>
							</div>
							<div class="vap-mediaimg-control pull-right">
								<a href="javascript: void(0)" class="no-decoration" onClick="jQuery('#vapblock<?php echo $cont; ?>').addClass('hover');">
									<i class="fa fa-pencil big"></i>
								</a>
								<a href="javascript: void(0)" class="no-decoration" onClick="openRemoveMediaDialog('<?php echo $img_ext; ?>', <?php echo $cont; ?>);">
									<i class="fa fa-trash big"></i>
								</a>
							</div>
						</div>
					</div>
					
					<div class="vap-mediaimg-wrapper-back">
						<div class="vap-mediaimg-details">
							<h3><?php echo JText::_('VAPMANAGEMEDIA2'); ?></h3>
							<input type="text" value="<?php echo $name_no_ext; ?>" id="vap-imgname-input<?php echo $cont; ?>" size="24" onkeypress="return event.keyCode != 13;"/><?php echo $img_ext; ?>
							<input type="hidden" value="<?php echo $name_no_ext; ?>" id="vap-imgold-input<?php echo $cont; ?>"/>
							<div class="vap-mediaimg-stats" id="vapmediastats<?php echo $cont; ?>"></div>
						</div>
						<div class="vap-mediaimg-controls">
							<div class="vap-mediaimg-control pull-right">
								<button type="button" class="btn" onClick="cancelMediaDetails('<?php echo $img_ext; ?>', <?php echo $cont; ?>);"><?php echo JText::_('VAPCANCEL'); ?></button>
								<button type="button" class="btn btn-success" onClick="doneMediaDetails('<?php echo $img_ext; ?>', <?php echo $cont; ?>);"><?php echo JText::_('VAPDONE'); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	
	</div>
	
<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="media" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php if (!$this->loadedAll) { ?>

	<div class="vap-media-footer">
		<div class="vap-media-loadbuttons">
			<button type="button" class="btn btn-success" onClick="loadMoreMedia(<?php echo $this->mediaLimit; ?>);"><?php echo JText::_('VAPLOADMOREMEDIA'); ?></button>
			<button type="button" class="btn btn-success" onClick="loadMoreMedia(<?php echo $this->maxLimit; ?>);"><?php echo JText::_('VAPLOADALLMEDIA'); ?></button>
		</div>
		<div class="vap-media-wait" style="display: none;">
			<img src="<?php echo VAPASSETS_ADMIN_URI . 'images/loading.gif'; ?>"/>
		</div>
	</div>

<?php } ?>

<div id="dialog-confirm" title="<?php echo JText::_('VAPMEDIAREMOVETITLE');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
		<span><?php echo JText::_('VAPMEDIAREMOVEMESSAGE'); ?></span>
	</p>
</div>

<script>

	jQuery(document).ready(function() {
		renderImageProperties();
	});
	
	function renderImageProperties() {
		jQuery('.vap-mediaimg-wrapper').hover(
			function() {
				jQuery(this).find('.vap-mediaimg-infos').fadeIn('fast');
			},
			function() {
				jQuery(this).find('.vap-mediaimg-infos').fadeOut('fast');
			}
		);
	}
	
	function restoreSelectedImage(image_name, image_ext, id) {
		var displayText = image_name + image_ext;
		if (displayText.length > 24)
		{
			displayText = displayText.substr(0, 16) + '...' + displayText.substr(displayText.lastIndexOf('.') - 2);
		}

		jQuery('#vap-imgname-input' + id).val(image_name); // rename input details
		jQuery('#vap-imgold-input' + id).val(image_name); // rename old input details
		jQuery('#cb' + id).next().html(displayText); // rename label checkbox
	} 
	
	function renameSelectedImage(image_ext, id) {
		var text = jQuery('#vap-imgname-input'+id).val();
		var old_name = jQuery('#vap-imgold-input'+id).val() + image_ext;
		
		restoreSelectedImage(text, image_ext, id);
		
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php",
			data: { option: "com_vikappointments", task: "renameMedia", oldname: old_name, newname: text, id: id, tmpl: "component" }
		}).done(function(resp){
			var obj = jQuery.parseJSON(resp); 
			
			if( obj[0] ) {
				
			} else {
				restoreSelectedImage(old_name, image_ext, id);
				alert(obj[1]);
			}
			
		});
		
	}
	
	function deleteSelectedImage(image_name, id) {
		
		jQuery.noConflict();
		
		jQuery('#vapblock'+id).fadeOut('slow', function(){
			jQuery(this).remove();
		});
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php",
			data: { option: "com_vikappointments", task: "deleteOneMediaAjax", image: image_name, tmpl: "component" }
		}).done(function(resp){
			
		});
		
		START_LIMIT--;
		MEDIA_REMOVED++;
		if( MEDIA_REMOVED == 3 ) {
			MEDIA_REMOVED = 0;
		}
		
	}
	
	function cancelMediaDetails(image_ext, id) {
		jQuery('#vapblock'+id).removeClass('hover');
		var name = jQuery('#vap-imgold-input'+id).val();
		restoreSelectedImage(name, image_ext, id);
	}
	
	function doneMediaDetails(image_ext, id) {
		jQuery('#vapblock'+id).removeClass('hover');
		renameSelectedImage(image_ext, id);
	}

	function mediaSelectAll(is) {
		if (is)
		{
			if (jQuery('.vap-mediaimg-check:not(:checked)').length != 0)
			{
				jQuery('.vap-mediaimg-check').trigger('click');
			}
		}
		else
		{
			if (jQuery('.vap-mediaimg-check:checked').length != 0)
			{
				jQuery('.vap-mediaimg-check').trigger('click');
			}
		}
	}
	
	function mediaCheckedAction(id) {
		if (jQuery('#cb'+id).is(':checked')) {
		   jQuery('#vapwrapper'+id).addClass('vap-media-selected');
		} else {
		   jQuery('#vapwrapper'+id).removeClass('vap-media-selected');
		}
	}
	
	function openRemoveMediaDialog(image_ext, id) {
		var image = jQuery('#vap-imgold-input'+id).val()+image_ext;
		
		jQuery( "#dialog-confirm" ).dialog({
			resizable: false,
			height:220,
			modal: true,
			buttons: {
				"<?php echo JText::_('VAPRENEWSESSIONCONFOK'); ?>": function() {
					jQuery( this ).dialog( "close" );
					deleteSelectedImage(image, id);
				},
				"<?php echo JText::_('VAPRENEWSESSIONCONFCANC'); ?>": function() {
					jQuery( this ).dialog( "close" );
				}
			}
		});
	}
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		
		document.adminForm.submit();
	}
	
	// LOAD MORE
	
	var START_LIMIT = <?php echo $this->mediaLimit; ?>;
	var MEDIA_REMOVED = 0;
	
	function loadMoreMedia(lim) {
		jQuery('.vap-media-loadbuttons').hide();
		jQuery('.vap-media-wait').show();
		
		jQuery.noConflict();
		
		lim += MEDIA_REMOVED;
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=loadmedia&tmpl=component",
			data: {
				start_limit: START_LIMIT,
				limit: lim,
				keysearch: jQuery('#vapkeysearch').val()
			}
		}).done(function(resp){
			var obj = jQuery.parseJSON(resp); 
			
			if( obj[0] ) {
				
				START_LIMIT = obj[1];
				MEDIA_REMOVED = 0;
				
				for( var i = 0; i < obj[3].length; i++ ) {
					jQuery('.vap-mediaimg-filespool').append(obj[3][i]);
				}
				
				renderImageProperties();
				
			} else {
				alert(obj[1]);
			}
			
			jQuery('.vap-media-wait').hide();
			if( obj[2] ) {
				jQuery('.vap-media-loadbuttons').show();
			}
			
		});
	}
	
	// ANALYZE
	
	function analyzeMedia() {
		var formdata = new FormData();
		jQuery('.vap-mediaimg-check:checked').each(function(){
			formdata.append('cid[]', jQuery(this).val());
		});
		
		jQuery('.vap-mediaimg-stats').removeClass('ok bad');
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=analyzemedia&tmpl=component",
			contentType:false,
			processData: false,
			cache: false,
			data: formdata
		}).done(function(resp){
			var obj = jQuery.parseJSON(resp); 
			
			jQuery.each(obj, function(k, v){
				var id = jQuery('input[value="'+k+'"]').attr('id').split('cb')[1];
				jQuery('#vapmediastats'+id).html(v['label']);
				if( v['count'] > 0 ) {
					jQuery('#vapmediastats'+id).addClass('ok');    
				} else {
					jQuery('#vapmediastats'+id).addClass('bad');
				}
				jQuery('#vapblock'+id).addClass('hover');
			});
			
		});
	}
	
	Joomla.submitbutton = function(task) {
		if (task == 'analyzemedia') {
			analyzeMedia();
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}
	
</script>
