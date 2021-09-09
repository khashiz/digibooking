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

JHtml::_('formbehavior.chosen');

$inv_files  = $this->invoicesFiles;
$each       = $this->eachInvoice;
$seek       = $this->seek;

$filters = $this->filters;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

JFactory::getDocument()->addStyleDeclaration('input.cid {display: none;}');

$is_searching = $this->hasFilters();

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar vapgroup-keyfilter-block" id="filter-bar">

		<div class="btn-group pull-left input-append">
			<input type="text" name="keysearch" id="vapkeysearch" class="vapkeysearch" size="32" 
				value="<?php echo $this->filters['keysearch']; ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />

			<button type="submit" class="btn">
				<i class="icon-search"></i>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn <?php echo ($is_searching ? 'btn-primary' : ''); ?>" onclick="vapToggleSearchToolsButton(this);">
				<?php echo JText::_('JSEARCH_TOOLS'); ?>&nbsp;<i class="fa fa-caret-<?php echo ($is_searching ? 'up' : 'down'); ?>" id="vap-tools-caret"></i>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>
		
		<div class="btn-group pull-right">
			<button type="button" class="btn" onClick="selectAll(1);">
				<?php echo JText::_('VAPMEDIASELECTALL'); ?>
			</button>
			<button type="button" class="btn" onClick="selectAll(0);">
				<?php echo JText::_('VAPMEDIASELECTNONE'); ?>
			</button>
		</div>
	</div>

	<div class="btn-toolbar" id="vap-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<?php
		$options = array(
			JHtml::_('select.option', ''			, JText::_('VAPMENUCUSTOMERS')),
			JHtml::_('select.option', 'packages'	, JText::_('VAPMENUPACKAGES')),
			JHtml::_('select.option', 'employees'	, JText::_('VAPMENUEMPLOYEES')),
		);
		?>
		<div class="btn-group pull-left vap-setfont">
			<select name="group" id="vap-group-sel" class="<?php echo (!empty($filters['group']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['group']); ?>
			</select>
		</div>

	</div>

	<div class="vap-archive-main">

		<div class="vap-archive-filestree">
			<ul class="root">
				<?php foreach ($this->tree as $year => $months) { ?>
					<li class="year <?php echo ($seek['year'] == $year ? 'expanded' : 'wrapped' ); ?>">
						<div class="year-node"><?php echo ($year != -1 ? $year : JText::_('VAPARCHIVEOTHERS')); ?></div>
						<ul class="monthslist" style="<?php echo ($seek['year'] != $year ? 'display: none;' : '' ); ?>">
							
							<?php foreach ($months as $mon) { ?>
								<li class="month <?php echo ($seek['year'] == $year && $seek['month'] == $mon ? 'picked' : '' ); ?>">
									<div class="month-node">
										<a href="javascript: void(0);" onClick="loadInvoiceOn(<?php echo $year; ?>,<?php echo $mon; ?>, jQuery(this));">
											<?php echo ($mon != -1 ? JText::_('VAPMONTH' . $mon) : JText::_('VAPARCHIVEOTHERSALL')); ?>
										</a>
									</div>
								</li>
							<?php } ?>

						</ul>
					</li>
				<?php } ?>
			</ul>
		</div>
	
		<div class="vap-archive-filespool">
			
			<?php if (count($inv_files) == 0) { ?>
				
				<p><?php echo JText::_('VAPNOINVOICESONARCHIVE'); ?></p>

			<?php } else {
	
				$cont = 0;
				$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');
				
				foreach ($inv_files as $inv)
				{
					$cont++;

					$url = VAPINVOICE_URI . ($filters['group'] ? $filters['group'] . '/' : '') . basename($inv);

					?>
					
					<div class="vap-archive-fileblock">
						<div class="vap-archive-fileicon">
							<img src="<?php echo VAPASSETS_ADMIN_URI . 'images/invoice@big.png'; ?>" />
						</div>
						<div class="vap-archive-filename">
							<a href="<?php echo $url; ?>" target="_blank">
								<?php echo $each[$inv]['filename']; ?>
							</a>
							<div><?php echo $each[$inv]['details']; ?></div>
						</div>
						<input type="checkbox" id="cb<?php echo $cont;?>" name="cid[]" class="cid" value="<?php echo $each[$inv]['filename']; ?>" onChange="<?php echo $vik->checkboxOnClick(); ?>">
					</div>
					
				<?php } ?>

			<?php } ?>

		</div>
		
	</div>
	
	<input type="hidden" name="year" value="<?php echo $seek['year']; ?>" id="vapseekyear" />
	<input type="hidden" name="month" value="<?php echo $seek['month']; ?>" id="vapseekmonth" />
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="invfiles" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<div class="vap-media-footer">
	<div class="vap-media-loadbuttons" style="<?php echo ($this->loadedAll ? "display: none;" : ""); ?>">
		<button type="button" class="btn btn-success" onClick="loadMoreInvoices(<?php echo $this->mediaLimit; ?>);"><?php echo JText::_('VAPLOADMOREINVOICES'); ?></button>
		<button type="button" class="btn btn-success" onClick="loadMoreInvoices(-1);"><?php echo JText::_('VAPLOADALLINVOICES'); ?></button>
	</div>
	<div class="vap-media-wait" style="display: none;">
		<img src="<?php echo VAPASSETS_ADMIN_URI . 'images/loading.gif'; ?>" />
	</div>
</div>

<div id="dialog-confirm" title="<?php echo JText::_('VAPMEDIAREMOVETITLE');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
		<span><?php echo JText::_('VAPMEDIAREMOVEMESSAGE'); ?></span>
	</p>
</div>

<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

		jQuery('.year-node').on('click', function() {
			var mlist = jQuery(this).next();
			
			if (mlist.is(':visible')) {
				jQuery(this).parent().addClass('wrapped');
				jQuery(this).parent().removeClass('expanded');
				mlist.slideUp();
			} else {
				jQuery(this).parent().addClass('expanded');
				jQuery(this).parent().removeClass('wrapped');
				mlist.slideDown();
			}
		});
		
		registerFileAction();
	});

	function registerFileAction() {

		jQuery('input.cid').off('change');
		jQuery('.vap-archive-fileicon').off('click');

		jQuery('input.cid').on('change', function() {

			var parent = jQuery(this).closest('.vap-archive-fileblock');

			if (jQuery(this).is(':checked')) {
				parent.addClass('selected');
			} else {
				parent.removeClass('selected');
			}

		});

		jQuery('.vap-archive-fileicon').on('click', function() {
			var checkbox = jQuery(this).parent().find('input.cid');

			jQuery(checkbox).trigger('click');
		});

	}

	function selectAll(is) {
		if (is)
		{
			if (jQuery('input.cid:not(:checked)').length != 0)
			{
				jQuery('input.cid:not(:checked)').trigger('click');
			}
		}
		else
		{
			if (jQuery('input.cid:checked').length != 0)
			{
				jQuery('input.cid:checked').trigger('click');
			}
		}
	}
	
	var QUERY_ARGS 	= <?php echo json_encode($seek); ?>;
	var RUNNING 	= false;
	
	function loadInvoiceOn(year, month, node) {
		if (RUNNING) {
			return;
		}

		// unset selection to avoid Joomla button to be stuck on "already selected"
		selectAll(0);
		
		RUNNING = true;
		
		QUERY_ARGS['year']  = year;
		QUERY_ARGS['month'] = month;
		
		jQuery('#vapseekyear').val(year);
		jQuery('#vapseekmonth').val(month);
		
		START_LIMIT = 0;
		
		jQuery('.month').removeClass('picked');
		node.parent().parent().addClass('picked');
		
		jQuery('.vap-archive-filespool').html('');
		
		loadMoreInvoices(LIMIT);
	}
	
	// LOAD MORE
	
	var START_LIMIT = <?php echo $this->mediaLimit; ?>;
	var LIMIT 		= START_LIMIT;
	var MAX_LIMIT 	= <?php echo $this->maxLimit; ?>
	
	function loadMoreInvoices(lim) {
		jQuery('.vap-media-loadbuttons').hide();
		jQuery('.vap-media-wait').show();
		
		if (lim <= 0) {
			lim = MAX_LIMIT;
		}
		
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=loadinvoices&tmpl=component",
			data: { 
				year: QUERY_ARGS['year'],
				month: QUERY_ARGS['month'],
				start_limit: START_LIMIT,
				limit: lim, 
				keysearch: '<?php echo addslashes($this->filters['keysearch']); ?>',
				group: '<?php echo addslashes($this->filters['group']); ?>'
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0]) {
				
				START_LIMIT = obj[1];
				
				for (var i = 0; i < obj[3].length; i++) {
					jQuery('.vap-archive-filespool').append(obj[3][i]);
				}
				
				registerFileAction();
				
			} else {
				alert(obj[1]);
			}
			
			jQuery('.vap-media-wait').hide();
			if (obj[2]) {
				jQuery('.vap-media-loadbuttons').show();
			}
			
			MAX_LIMIT = obj[4];
			
			RUNNING = false;
			
		}).fail(function(resp) {
			RUNNING = false;
		});
	}

	//////////////////////////////////////////////////////////
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-group-sel').updateChosen('');
		
		document.adminForm.submit();
	}
	
</script>
