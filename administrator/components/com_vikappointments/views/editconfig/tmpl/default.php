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

$params = $this->params;

$session = JFactory::getSession();
$_selected_tab_view = $session->get('vaptabactive', 1, 'vapconfig');

$vik = UIApplication::getInstance();

/**
 * Prepares CodeMirror editor scripts for being used
 * via Javascript/AJAX.
 *
 * @wponly
 */
$vik->prepareEditor('codemirror');

?>

<?php
$titles = array(
	JText::_('VAPCONFIGTABNAME1'),
	JText::_('VAPCONFIGGLOBTITLE5'),
	JText::_('VAPCONFIGGLOBTITLE6'),
	JText::_('VAPCONFIGGLOBTITLE8'),
	JText::_('VAPCONFIGGLOBTITLE15'),
);
?>
<div id="navigation">
	<ul>
		<?php for ($i = 1; $i <= 5; $i++) { ?>
			<li id="vaptabli<?php echo $i; ?>" class="vaptabli<?php echo (($_selected_tab_view == $i) ? ' vapconfigtabactive' : ''); ?>"><a href="javascript: void(0);" onclick="changeTabView(<?php echo $i; ?>);"><?php echo $titles[$i-1]; ?></a></li>
		<?php } ?>
	</ul>
</div>

<?php
// print config search bar
UILoader::import('libraries.widget.layout');
echo UIWidgetLayout::getInstance('searchbar')->display();
?>

<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data">
	
	<!-- GLOBAL SECTION -->
	
	<div id="vaptabview1" class="vaptabview" style="<?php echo (($_selected_tab_view != 1) ? 'display: none;' : ''); ?>">
		
		<?php echo $this->loadTemplate('global'); ?>
		
	</div>

	<div id="vaptabview2" class="vaptabview" style="<?php echo (($_selected_tab_view != 2) ? 'display: none;' : ''); ?>">

		<?php echo $this->loadTemplate('mail'); ?>

	</div>

	<div id="vaptabview3" class="vaptabview" style="<?php echo (($_selected_tab_view != 3) ? 'display: none;' : ''); ?>">
		
		<?php echo $this->loadTemplate('currency'); ?>

	</div>

	<div id="vaptabview4" class="vaptabview" style="<?php echo (($_selected_tab_view != 4) ? 'display: none;' : ''); ?>">

		<?php echo $this->loadTemplate('shop'); ?>

	</div>

	<div id="vaptabview5" class="vaptabview" style="<?php echo (($_selected_tab_view != 5) ? 'display: none;' : ''); ?>">

		<?php echo $this->loadTemplate('listings'); ?>

	</div>

	<!-- invoice properties modal -->
	<?php
	echo JHtml::_(
		'bootstrap.renderModal',
		'jmodal-invoice',
		array(
			'title'       => JText::_('VAPINVOICESTITLE'),
			'closeButton' => true,
			'bodyHeight'  => 60,
			'footer' 	  => '<button type="button" class="btn btn-success" onclick="' . $vik->bootDismissModalJS('#jmodal-invoice') . '">' . JText::_('JAPPLY') . '</button>',
		),
		$this->loadTemplate('invoice')
	);
	?>
	
	<input type="hidden" name="task" value="" id="vapconfigtask"/>
	<input type="hidden" name="option" value="com_vikappointments"/>
</form>

<?php
// email template modal
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-emailtmpl',
	array(
		'title'       => JText::_('VAPJMODALEMAILTMPL') . ' - <span id="modal-title-fname"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<!-- SCRIPT -->

<script>
	
	jQuery(document).ready(function(){

		jQuery('.hasTooltip').tooltip();

		// dropdown rendering

		jQuery('select.short').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100
		});

		jQuery('select.small-medium').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery('select.medium').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 200
		});

		jQuery('select.medium-large').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 250
		});

		jQuery('select.large').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			// do not use 300px because the input may exceed in width (13" screen)
			//width: 300
			width: 250
		});

		//

		makeSortable();
		
	});

	function makeSortable() {
		
		jQuery(".vap-config-empord-fieldslist").sortable({
			revert: true
		});
		
	}

	// lock / unlock an input starting from the specified link

	function lockUnlockInput(link) {

		var input = jQuery(link).prev();

		if( input.prop('readonly') ) {
			input.prop('readonly', false);

			jQuery(link).find('i').removeClass('fa-lock');
			jQuery(link).find('i').addClass('fa-unlock-alt');
		} else {
			input.prop('readonly', true);

			jQuery(link).find('i').removeClass('fa-unlock-alt');
			jQuery(link).find('i').addClass('fa-lock');
		}

	}
	
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
			url: "index.php?option=com_vikappointments&task=store_tab_selected&tmpl=component&group=vapconfig",
			data: { tab: tab }
		}).done(function(resp){
			
		}).fail(function(resp){
			
		});
	}

	// MAIL PREVIEW

	function goToMailPreview(select, type) {
		var layout = jQuery('#' + select).val();

		window.open('index.php?option=com_vikappointments&task=preview_mail_template&tmpl=component&layout=' + layout + '&type=' + type, '_blank');
	}
	
	// MODAL BOXES

	var TEMPLATE_ID = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'emailtmpl') {
			var file = jQuery('#' + TEMPLATE_ID).val();
			jQuery('#modal-title-fname').text(file);

			url = 'index.php?option=com_vikappointments&tmpl=component&task=managefile&file=' + getManageFilePath(file);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

	function getManageFilePath(file) {
		return "<?php echo addslashes(VAPBASE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mail_tmpls' . DIRECTORY_SEPARATOR); ?>" + file;
	}
	
</script>
