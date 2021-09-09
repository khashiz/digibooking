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
$_selected_tab_view = $session->get('vaptabactive', 1, 'vapconfigemp');

$vik = UIApplication::getInstance();

?>

<div id="navigation">
	<ul>
		<?php for ($i = 1; $i <= 3; $i++) { ?>
			<li id="vaptabli<?php echo $i; ?>" class="vaptabli<?php echo (($_selected_tab_view == $i) ? ' vapconfigtabactive' : ''); ?>"><a href="javascript: changeTabView(<?php echo $i; ?>);"><?php echo JText::_('VAPCONFIGEMPTITLE'.$i); ?></a></li>
		<?php } ?>
	</ul>
</div>

<?php
// print config search bar
UILoader::import('libraries.widget.layout');
echo UIWidgetLayout::getInstance('searchbar')->display();
?>

<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data">
	
	<!-- EMPLOYEE SECTION -->
	
	<div id="vaptabview1" class="vaptabview" style="<?php echo (($_selected_tab_view != 1) ? 'display: none;' : ''); ?>">
		
		<!-- Employees Registration Fieldset -->
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPCONFIGEMPTITLE1'); ?></legend>
			<table class="admintable table" cellspacing="1">
				<!-- ENABLE EMPLOYEE SIGN UP - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empsignup"] == "1", 'onClick="signUpValueChanged(1);"');
					$elem_no  = $vik->initRadioElement('', '', $params["empsignup"] == "0", 'onClick="signUpValueChanged(0);"');
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP1"); ?></b> </td>
					<td>
						<?php echo $vik->radioYesNo('empsignup', $elem_yes, $elem_no); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP1'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP1_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- EMPLOYEE SIGN UP STATUS - Select -->
				<?php
				$elements = array();
				for ($i = 1; $i <= 2; $i++)
				{
					$elements[] = JHtml::_('select.option', $i, JText::_('VAPCONFIGEMPSIGNUPSTATUS'.$i));
				}
				?>
				<tr class="vapempsignuptr" style="<?php echo (($params['empsignup'] == "0") ? 'display: none;' : ''); ?>">
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP2"); ?></b> </td>
					<td>
						<select name="empsignstatus" class="small-medium">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['empsignstatus']); ?>
						</select>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP2'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP2_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- EMPLOYEE SIGN UP USER GROUP - Select -->
				<?php
				/**
				 * Obtain user groups through the native helper.
				 * The missing second parameter (false by default) will
				 * exclude the Super User group for being added.
				 *
				 * @since 1.6.3
				 */
				$elements = JHtml::_('user.groups');
				?>
				<tr class="vapempsignuptr" style="<?php echo (($params['empsignup'] == "0") ? 'display: none;' : ''); ?>">
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP3"); ?></b> </td>
					<td>
						<select name="empsignrule" class="small-medium">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['empsignrule']); ?>
						</select>
					</td>
				</tr>
				
				<!-- SERVICES AUTO-ASSIGNMENT - Multi Select -->
				<?php
				$picked_services = explode(',', $params['empassignser']);
				$services_multi_select = '<select name="empassignser[]" id="vap-empser-assignsel" multiple>';
				$services_multi_select .= '<option></option>';
				foreach( $this->services as $ser ) {
					$services_multi_select .= '<option value="'.$ser['id'].'" '.(in_array($ser['id'], $picked_services) ? 'selected="selected"' : '').'>'.$ser['name'].'</option>';
				}
				$services_multi_select .= '</select>';
				?>
				<tr class="vapempsignuptr" style="<?php echo (($params['empsignup'] == "0") ? 'display: none;' : ''); ?>">
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP15"); ?></b> </td>
					<td>
						<?php echo $services_multi_select; ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP15'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP15_DESC'),
						)); ?>
					</td>
				</tr>
			</table>
		</fieldset>
		
	</div>

	<div id="vaptabview2" class="vaptabview" style="<?php echo (($_selected_tab_view != 2) ? 'display: none;' : ''); ?>">
		
		<!-- Employees Auth Fieldset -->
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPCONFIGEMPTITLE2'); ?></legend>
			<table class="admintable table" cellspacing="1">
				<!-- EMPLOYEE CREATE RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empcreate"] == "1", 'onClick="createValueChanged(1);"');
					$elem_no  = $vik->initRadioElement('', '', $params["empcreate"] == "0", 'onClick="createValueChanged(0);"');
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP4"); ?></b> </td>
					<td>
						<?php echo $vik->radioYesNo('empcreate', $elem_yes, $elem_no); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP4'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP4_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- EMPLOYEE CREATE MAX SERVICES - Number -->
				<tr class="vapempcreatetr" style="<?php echo (($params['empcreate'] == "0") ? 'display: none;' : ''); ?>">
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP7"); ?></b> </td>
					<td>
						<input type="number" name="empmaxser" value="<?php echo $params['empmaxser']; ?>" min="1" max="999999" size="20" />
					</td>
				</tr>

				<!-- EMPLOYEE CAN ATTACH SERVICES - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empattachser"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empattachser"] == "0");
				?>
				<tr class="vapempcreatetr-non" style="<?php echo (($params['empcreate'] == "1") ? 'display: none;' : ''); ?>">
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP20"); ?></b> </td>
					<td>
						<?php echo $vik->radioYesNo('empattachser', $elem_yes, $elem_no); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP20'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP20_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- EMPLOYEE MANAGE SERVICES RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empmanageser"] == "1", 'onClick="manageServiceValueChanged(1);"');
					$elem_no  = $vik->initRadioElement('', '', $params["empmanageser"] == "0", 'onClick="manageServiceValueChanged(0);"');
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP12"); ?></b> </td>
					<td>
						<?php echo $vik->radioYesNo('empmanageser', $elem_yes, $elem_no); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP12'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP12_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- EMPLOYEE MANAGE RATE RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empmanagerate"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empmanagerate"] == "0");
				?>
				<tr class="vapempmanageratetr" style="<?php echo (($params['empmanageser'] == "1") ? 'display: none;' : ''); ?>">
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP14"); ?></b> </td>
					<td>
						<?php echo $vik->radioYesNo('empmanagerate', $elem_yes, $elem_no); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP14'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP14_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- EMPLOYEE REMOVE SERVICE RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empremove"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empremove"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP6"); ?></b> </td>
					<td>
						<?php echo $vik->radioYesNo('empremove', $elem_yes, $elem_no); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP6'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP6_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- EMPLOYEE MANAGE PROFILE RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empmanage"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empmanage"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP5"); ?></b> </td>
					<td><?php echo $vik->radioYesNo('empmanage', $elem_yes, $elem_no); ?></td>
				</tr>
				
				<!-- EMPLOYEE MANAGE WORKDAYS RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empmanagewd"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empmanagewd"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP16"); ?></b> </td>
					<td><?php echo $vik->radioYesNo('empmanagewd', $elem_yes, $elem_no); ?></td>
				</tr>

				<!-- EMPLOYEE MANAGE COUPONS RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empmanagecoupon"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empmanagecoupon"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP18"); ?></b> </td>
					<td><?php echo $vik->radioYesNo('empmanagecoupon', $elem_yes, $elem_no); ?></td>
				</tr>
				
				<!-- EMPLOYEE MANAGE PAYMENTS RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empmanagepay"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empmanagepay"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP13"); ?></b> </td>
					<td><?php echo $vik->radioYesNo('empmanagepay', $elem_yes, $elem_no); ?></td>
				</tr>

				<!-- EMPLOYEE MANAGE CUSTOM FIELDS RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empmanagecustfield"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empmanagecustfield"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP19"); ?></b> </td>
					<td><?php echo $vik->radioYesNo('empmanagecustfield', $elem_yes, $elem_no); ?></td>
				</tr>
				
				<!-- EMPLOYEE MANAGE LOCATIONS RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empmanageloc"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empmanageloc"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP17"); ?></b> </td>
					<td><?php echo $vik->radioYesNo('empmanageloc', $elem_yes, $elem_no); ?></td>
				</tr>
			</table>
		</fieldset>

	</div>

	<div id="vaptabview3" class="vaptabview" style="<?php echo (($_selected_tab_view != 3) ? 'display: none;' : ''); ?>">
		
		<!-- Employee Reservations Fieldset -->
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPCONFIGEMPTITLE3'); ?></legend>
			<table class="admintable table" cellspacing="1">
				<!-- EMPLOYEE RES CREATE RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["emprescreate"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["emprescreate"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP8"); ?></b> </td>
					<td><?php echo $vik->radioYesNo('emprescreate', $elem_yes, $elem_no); ?></td>
				</tr>
				
				<!-- EMPLOYEE RES MANAGE RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empresmanage"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empresmanage"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP9"); ?></b> </td>
					<td><?php echo $vik->radioYesNo('empresmanage', $elem_yes, $elem_no); ?></td>
				</tr>

				<!-- EMPLOYEE RES CONFIRM RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empresconfirm"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empresconfirm"] == "0");
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP21"); ?></b> </td>
					<td>
						<?php echo $vik->radioYesNo('empresconfirm', $elem_yes, $elem_no); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP21'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP21_DESC'),
						)); ?>
					</td>
				</tr>
				
				<!-- EMPLOYEE RES REMOVE RULE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empresremove"] == "1", 'onClick="reservationRemoveValueChanged(1);"');
					$elem_no  = $vik->initRadioElement('', '', $params["empresremove"] == "0", 'onClick="reservationRemoveValueChanged(0);"');
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP10"); ?></b> </td>
					<td><?php echo $vik->radioYesNo('empresremove', $elem_yes, $elem_no); ?></td>
				</tr>
				
				<!-- EMPLOYEE RES NOTIFY ON DELETE - Radio Button -->
				<?php
					$elem_yes = $vik->initRadioElement('', '', $params["empresnotify"] == "1");
					$elem_no  = $vik->initRadioElement('', '', $params["empresnotify"] == "0");
				?>
				<tr class="vapempresremovetr" style="<?php echo (($params['empresremove'] == "0") ? 'display: none;' : ''); ?>">
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIGEMP11"); ?></b> </td>
					<td>
						<?php echo $vik->radioYesNo('empresnotify', $elem_yes, $elem_no); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGECONFIGEMP11'),
							'content' 	=> JText::_('VAPMANAGECONFIGEMP11_DESC'),
						)); ?>
					</td>
				</tr>
			</table>
		</fieldset>

	</div>
	
	<input type="hidden" name="task" value="" id="vapconfigtask"/>
	<input type="hidden" name="option" value="com_vikappointments"/>
</form>

<!-- SCRIPT -->

<script>
	
	jQuery(document).ready(function() {

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

		jQuery("#vap-empser-assignsel").select2({
			placeholder: '--',
			allowClear: true,
			width: 500
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
			url: "index.php?option=com_vikappointments&task=store_tab_selected&tmpl=component&group=vapconfigemp",
			data: { tab: tab }
		}).done(function(resp){
			
		}).fail(function(resp){
			
		});
	}
	
	// EMPLOYEES
	
	function signUpValueChanged(val) {
		if( val ) {
			jQuery('.vapempsignuptr').show();
		} else {
			jQuery('.vapempsignuptr').hide();
		}
	}
	
	function createValueChanged(val) {
		if( val ) {
			jQuery('.vapempcreatetr').show();
			jQuery('.vapempcreatetr-non').hide();
		} else {
			jQuery('.vapempcreatetr').hide();
			jQuery('.vapempcreatetr-non').show();
		}
	}
	
	function manageServiceValueChanged(val) {
		if( !val ) {
			jQuery('.vapempmanageratetr').show();
		} else {
			jQuery('.vapempmanageratetr').hide();
		}
	}
	
	function reservationRemoveValueChanged(val) {
		if( val ) {
			jQuery('.vapempresremovetr').show();
		} else {
			jQuery('.vapempresremovetr').hide();
		}
	}
	
</script>
