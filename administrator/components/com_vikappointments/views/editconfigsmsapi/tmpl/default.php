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
$_selected_tab_view = $session->get('vaptabactive', 1, 'vapconfigsmsapi');

$vik = UIApplication::getInstance();

$languages = VikAppointments::getKnownLanguages();

?>

<style>
	.vap-uc-text-green{
		color: green;
		font-weight: bold;
	}
	
	.vap-uc-text-red{
		color: red;
		font-weight: bold;
	}
</style>

<?php
$titles = array(
	JText::_('VAPCONFIGTABNAME4'),
	JText::_('VAPCONFIGSMSTITLE1'),
);
?>
<div id="navigation">
	<ul>
		<?php for( $i = 1; $i <= 2; $i++ ) { ?>
			<li id="vaptabli<?php echo $i; ?>" class="vaptabli<?php echo (($_selected_tab_view == $i) ? ' vapconfigtabactive' : ''); ?>"><a href="javascript: changeTabView(<?php echo $i; ?>);"><?php echo $titles[$i-1]; ?></a></li>
		<?php } ?>
	</ul>
</div>

<?php
// print config search bar
UILoader::import('libraries.widget.layout');
echo UIWidgetLayout::getInstance('searchbar')->display();
?>

<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data">
	
	<!-- SMS APIs SETTINGS -->
	
	<?php 
	$sms_apis = array();
	
	foreach (glob(VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . '*.php') as $f)
	{
		$sms_apis[] = basename($f);
	}
	
	$sms_api_to = explode(',', $params['smsapito']);
	?>
	
	<div id="vaptabview1" class="vaptabview" style="<?php echo (($_selected_tab_view != 1) ? 'display: none;' : ''); ?>">
		
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPCONFIGTABNAME4'); ?></legend>
			<table class="admintable table" cellspacing="1">
			
				<!-- SMS APIs FILE - Select -->
				<?php
				$elements = array(
					JHtml::_('select.option', '', '')
				);

				foreach ($sms_apis as $api)
				{
					$elements[] = JHtml::_('select.option', $api, $api);
				}
				?> 
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGSMS1"); ?></b> </td>
					<td>
						<select name="smsapi" id="smsapiselect" onChange="refreshSmsApiParameters();">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['smsapi']); ?>
						</select>
					</td>
				</tr>
				
				<!-- ENABLE AUTO SMS APIs - Radio Button -->
				<?php
				$elem_yes = $vik->initRadioElement('', '', $params['smsenabled'] == 1);
				$elem_no  = $vik->initRadioElement('', '', $params['smsenabled'] == 0);
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGSMS2"); ?></b> </td>
					<td>
						<?php echo $vik->radioYesNo('smsenabled', $elem_yes, $elem_no); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGSMS2'),
							'content' 	=> JText::_('VAPMANAGECONFIGSMS2_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- SMS APIs TO - Checkbox List -->
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGSMS3"); ?></b> </td>
					<td><input type="checkbox" name="smsapitocust" value="1" id="smsapito0" <?php echo ( ( $sms_api_to[0] == "1") ? "checked=\"checked\"" : "" ); ?>> <label for="smsapito0" style="display: inline-block;"><?php echo JText::_("VAPCONFIGSMSAPITO0"); ?></label> &nbsp;&nbsp; 
						<input type="checkbox" name="smsapitoemp" value="1" id="smsapito1" <?php echo ( ( $sms_api_to[1] == "1") ? "checked=\"checked\"" : "" ); ?>> <label for="smsapito1" style="display: inline-block;"><?php echo JText::_("VAPCONFIGSMSAPITO1"); ?></label> &nbsp;&nbsp; 
						<input type="checkbox" name="smsapitoadmin" value="1" id="smsapito2" <?php echo ( ( $sms_api_to[2] == "1") ? "checked=\"checked\"" : "" ); ?>> <label for="smsapito2" style="display: inline-block;"><?php echo JText::_("VAPCONFIGSMSAPITO2"); ?></label></td>
				</tr>
				
				<!-- SMS APIs ADMIN PHONE - Text -->
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGSMS4"); ?></b> </td>
					<td>
						<input type="text" name="smsapiadminphone" value="<?php echo $params['smsapiadminphone']; ?>" size="16" />
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGSMS4'),
							'content' 	=> JText::_('VAPMANAGECONFIGSMS4_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- SMS APIs PARAMS - Form -->
				<tr>
					<td width="200" class="adminparamcol" style="vertical-align: top;"> <b><?php echo JText::_("VAPMANAGECONFIGSMS5"); ?></b> </td>
					<td><div class="vikpayparamdiv">
							
						</div></td>
				</tr>
				
				<?php
				$can_estimate = false;
				$sms_api_path = VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $params['smsapi'];
				
				if (file_exists($sms_api_path) && strlen($params['smsapi']))
				{
					require_once $sms_api_path;

					if (method_exists('VikSmsApi', 'estimate'))
					{ 
						$can_estimate = true;
						?>
						<tr>
							<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGSMS7"); ?></b> </td>
							<td>
								<span id="usercreditspan" style="margin-right: 50px;">/</span>
								<button type="button" onClick="estimateSmsApiUserCredit();" class="btn"><?php echo JText::_("VAPMANAGECONFIGSMS8"); ?></button>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
				
			</table>
		</fieldset>
			
	</div>
	
	<!-- SMS TEMPLATES -->
	
	<div id="vaptabview2" class="vaptabview" style="<?php echo (($_selected_tab_view != 2) ? 'display: none;' : ''); ?>">
		
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPCONFIGSMSTITLE2'); ?></legend>

			<div class="btn-toolbar" style="height: 28px;">
				<div class="btn-group pull-left">
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(1, '{total_cost}');">{total_cost}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(1, '{checkin}');">{checkin}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(1, '{service}');">{service}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(1, '{employee}');">{employee}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(1, '{company}');">{company}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(1, '{created_on}');">{created_on}</button>
				</div>
			</div>
			<div class="control">
				<?php 
				$sms_tmpl_cust = array(json_decode($params['smstmplcust'], true), json_decode($params['smstmplcustmulti'], true));
				$placeholders  = array(array(), array());

				// keep current language
				$default = JFactory::getLanguage()->getTag();

				foreach ($languages as $k => $lang)
				{ 
					$lang_name = explode('-', $lang);
					$lang_name = strtolower($lang_name[1]);

					$translator = JFactory::getLanguage();
					$translator->load('com_vikappointments', JPATH_ADMINISTRATOR, $lang, true);

					$placeholders[0][$lang] = $translator->_('VAPSMSMESSAGECUSTOMER');
					$placeholders[1][$lang] = $translator->_('VAPSMSMESSAGECUSTOMERMULTI');
					
					for ($i = 0; $i < 2; $i++)
					{ 
						$content = "";
						if (!empty($sms_tmpl_cust[$i][$lang]))
						{
							$content = $sms_tmpl_cust[$i][$lang];
						}
						?>
						<textarea class="vap-smscont-1" id="vapsmscont<?php echo $lang_name; ?>-<?php echo ($i+1); ?>" placeholder="<?php echo $placeholders[$i][$lang]; ?>"
						style="width: 95%;height: 200px;<?php echo ($k != 0 || $i == 1 ? 'display:none;' : ''); ?>" name="smstmplcust[<?php echo $i; ?>][]"><?php echo $content; ?></textarea>
					<?php }
				}

				// restore default language
				if (end($languages) != $default)
				{
					JFactory::getLanguage()->load('com_vikappointments', JPATH_ADMINISTRATOR, $default, true);
				}
				?>
			</div>  
			<!-- LANGUAGES -->
			<div class="btn-toolbar" style="width: 95%;">
				<div class="btn-group pull-left">
					<button type="button" class="btn active vap-smscust1-type" id="vap-switch1-button-0" onClick="switchSmsContent(0, 1);"><?php echo JText::_('VAPSMSCONTSWITCHSINGLE'); ?></button>
					<button type="button" class="btn vap-smscust1-type" id="vap-switch1-button-1" onClick="switchSmsContent(1, 1);"><?php echo JText::_('VAPSMSCONTSWITCHMULTI'); ?></button>
				</div>

				<div class="btn-group pull-right">
					<?php foreach ($languages as $k => $lang)
					{ 
						$lang_name = explode('-', $lang);
						$lang_name = strtolower($lang_name[1]);
						?>
						<button type="button" class="vap-sms-langtag btn <?php echo ($k == 0 ? 'active' : ''); ?>" id="vapsmstag<?php echo $lang_name; ?>" onClick="changeLanguageSMS('<?php echo $lang_name; ?>');">
							<i class="icon">
								<img src="<?php echo VAPASSETS_URI . 'css/flags/'.$lang_name.'.png';?>"/>
							</i>
							&nbsp;<?php echo strtoupper($lang_name); ?>
						</button>
					<?php } ?>
				</div>
			</div>  
		</fieldset>
		
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPCONFIGSMSTITLE3'); ?></legend>

			<div class="btn-toolbar" style="height: 28px;">
				<div class="btn-group pull-left">
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(2, '{total_cost}');">{total_cost}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(2, '{checkin}');">{checkin}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(2, '{service}');">{service}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(2, '{employee}');">{employee}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(2, '{company}');">{company}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(2, '{customer}');">{customer}</button>
					<button type="button" class="btn" onClick="putSmsTagOnActiveContent(2, '{created_on}');">{created_on}</button>
				</div>
			</div>
			<div class="control">
				<?php 
				$sms_tmpl_admin = array($params['smstmpladmin'], $params['smstmpladminmulti']);
				$placeholders 	= array('VAPSMSMESSAGEADMIN', 'VAPSMSMESSAGEADMINMULTI');
				for ($i = 0; $i < 2; $i++) { ?>
					<textarea class="vap-smscont-2" id="vapsmscontadmin-<?php echo ($i+1); ?>" style="width: 95%;height: 200px;<?php echo ($i != 0 ? 'display:none;' : ''); ?>"
						name="smstmpladmin[]" placeholder="<?php echo JText::_($placeholders[$i]); ?>"><?php echo $sms_tmpl_admin[$i]; ?></textarea>
				<?php } ?>
			</div>
			
			<div class="btn-toolbar" style="width: 95%;">
				<div class="btn-group pull-left">
					<button type="button" class="btn active vap-smscust2-type" id="vap-switch2-button-0" onClick="switchSmsContent(0, 2);"><?php echo JText::_('VAPSMSCONTSWITCHSINGLE'); ?></button>
					<button type="button" class="btn vap-smscust2-type" id="vap-switch2-button-1" onClick="switchSmsContent(1, 2);"><?php echo JText::_('VAPSMSCONTSWITCHMULTI'); ?></button>
				</div>
			</div>
		</fieldset>

	</div>
	
	<input type="hidden" name="task" value="" id="vapconfigtask"/>
	<input type="hidden" name="option" value="com_vikappointments"/>
</form>

<script>
	
	jQuery(document).ready(function() {
		
		<?php if (!empty($params['smsapi'])) { ?>

			refreshSmsApiParameters();

		<?php } else { ?>

			jQuery('.vikpayparamdiv').html('<?php echo JText::_('VAPMANAGECONFIGSMS6'); ?>');

		<?php } ?>

		jQuery('#smsapiselect').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
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
			url: "index.php?option=com_vikappointments&task=store_tab_selected&tmpl=component&group=vapconfigsmsapi",
			data: { tab: tab }
		}).done(function(resp){
			
		}).fail(function(resp){
			
		});
	}
	
	// SMS

	var validator = new VikFormValidator('#adminForm');

	Joomla.submitbutton = function(task) {
		if (task.indexOf('save') !== -1) {
			if (validator.validate()) {
				Joomla.submitform(task, document.adminForm);	
			}
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}
	
	function refreshSmsApiParameters() {
		var sms_api = jQuery('#smsapiselect').val();
		
		jQuery.noConflict();
		
		jQuery('.vikpayparamdiv').html('');
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=get_sms_api_fields&tmpl=component",
			data: {
				sms_api: sms_api
			}
		}).done(function(resp){
			var obj = jQuery.parseJSON(resp);

			jQuery('.vikpayparamdiv').html(obj[0]);

			jQuery('.vikpayparamdiv select').select2({
				allowClear: false,
				width: 285,
			});

			validator.registerFields('.vikpayparamdiv .required');

			jQuery('.vikpayparamdiv .vap-quest-popover').popover();
		});
	}
	
	<?php if ($can_estimate) { ?>
		function estimateSmsApiUserCredit() {
			var sms_api = '<?php echo $params['smsapi']; ?>';
			var sms_api_phone = '<?php echo $params['smsapiadminphone']; ?>';
			
			jQuery.noConflict();
			
			jQuery('#usercreditspan').html('/');
			
			var jqxhr = jQuery.ajax({
				type: "POST",
				url: "index.php",
				data: { option: "com_vikappointments", task: "get_sms_api_credit", sms_api: sms_api, sms_api_phone: sms_api_phone, tmpl: "component" }
			}).done(function(resp){
				var obj = jQuery.parseJSON(resp); 
				
				if( obj[0] ) {
					if( obj[1] > 0 ) {
						jQuery('#usercreditspan').addClass('vap-uc-text-green');
						jQuery('#usercreditspan').removeClass('vap-uc-text-red');
					} else {
						jQuery('#usercreditspan').addClass('vap-uc-text-red');
						jQuery('#usercreditspan').removeClass('vap-uc-text-green');
					}
					jQuery('#usercreditspan').html(obj[2]);
				} else {
					alert(obj[1]);
				}
			});
		}
	<?php } ?>
	
	function putSmsTagOnActiveContent(id, cont) {
		
		var area = null;
		jQuery('.vap-smscont-'+id).each(function(){
			if( jQuery(this).css('display') != 'none' ) {
				area = jQuery(this);
			}
		});
		
		if( area == null ) {
			return;
		}
		
		var start = area.get(0).selectionStart;
		var end = area.get(0).selectionEnd;
		area.val(area.val().substring(0, start) + cont + area.val().substring(end));
		area.get(0).selectionStart = area.get(0).selectionEnd = start + cont.length;
		area.focus();
	}
	
	function changeLanguageSMS(tag) {
		jQuery('.vap-sms-langtag').removeClass('active');
		jQuery('#vapsmstag'+tag).addClass('active');
		
		var area = null;
		jQuery('.vap-smscont-1').each(function(){
			if( jQuery(this).css('display') != 'none' ) {
				area = jQuery(this);
			}
		});
		
		if( area == null ) {
			return;
		}
		
		jQuery('.vap-smscont-1').hide();
		jQuery('#vapsmscont'+tag+'-'+area.attr('id').split("-")[1]).show();
	}

	function switchSmsContent(section, cont) {

		if (jQuery('#vap-switch'+cont+'-button-'+section).hasClass('active')) {
			return false;
		}

		jQuery('.vap-smscust'+cont+'-type').removeClass('active');
		jQuery('#vap-switch'+cont+'-button-'+section).addClass('active');
		
		var area = null;
		jQuery('.vap-smscont-'+cont).each(function() {
			if (jQuery(this).css('display') != 'none') {
				area = jQuery(this);
			}
		});
		
		if (area == null) {
			return;
		}
		
		var id = area.attr('id').split('-');
		area.hide();

		jQuery('#'+id[0]+'-'+(section + 1)).show();
	}
	
</script>
