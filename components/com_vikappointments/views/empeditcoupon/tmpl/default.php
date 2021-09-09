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

$auth       = $this->auth;
$employee   = $auth->getEmployee();

$coupon = $this->coupon;

$type = $coupon['id'] > 0 ? 2 : 1;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$curr_symb 	 = $config->get('currencysymb');
$date_format = $config->get('dateformat');

$coupon['dstart'] = $coupon['dstart'] > 0 ? ArasJoomlaVikApp::jdate($date_format, $coupon['dstart']) : '';
$coupon['dend']   = $coupon['dend'] > 0   ? ArasJoomlaVikApp::jdate($date_format, $coupon['dend'])   : '';

$itemid = JFactory::getApplication()->input->getInt('Itemid');

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
		<h2><?php echo JText::_(($type == 2) ? 'VAPEDITEMPCOUPONTITLE' : 'VAPNEWEMPCOUPONTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">

		<?php if ($auth->manageCoupons()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveCoupon(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
		
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveCoupon(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>

		<?php if ($auth->manageCoupons() && $type == 2) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveCoupon();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseCoupons();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditcoupon'); ?>" method="post" name="empareaForm" id="empareaForm">
	
	<?php echo $vik->bootStartTabSet('set', array('active' => 'set_details')); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('set', 'set_details', JText::_('VAPORDERTITLE2')); ?>

			<?php echo $vik->openEmptyFieldset(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON1').'*:'); ?>
					<input type="text" name="code" value="<?php echo $coupon['code']; ?>" size="40" id="vapcode" class="required" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON2').':'); ?>
					<select name="coupon_type" id="vap-type-sel">
						<option value="1" <?php echo ($coupon['type'] == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCOUPONTYPE1'); ?></option>
						<option value="2" <?php echo ($coupon['type'] == 2 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPEMPCOUPONTYPE2'); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON12').':'); ?>
					<input type="number" name="max_quantity" value="<?php echo $coupon['max_quantity']; ?>" size="40" min="1" id="vap-maxq-input" <?php echo ($coupon['type'] == 1 ? 'disabled="disabled"' : ''); ?> />
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON13').':'); ?>
					<input type="number" name="used_quantity" value="<?php echo $coupon['used_quantity']; ?>" size="40" min="1" />
				<?php echo $vik->closeControl(); ?>

				<?php
				$control = array();
				$control['style'] = $coupon['type'] == 1 ? 'display: none;' : '';
				echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON14').':', 'vap-autoremove-box', $control); ?>
					<select name="remove_gift" id="vap-autoremove-sel">
						<option value="1" <?php echo ($coupon['remove_gift'] == 1 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPYES'); ?></option>
						<option value="0" <?php echo ($coupon['remove_gift'] == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPNO'); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON3').':'); ?>
					<select name="percentot" id="vap-percentot-sel">
						<option value="1" <?php echo ($coupon['percentot'] == 1 ? 'selected="selected"' : ''); ?>>%</option>
						<option value="2" <?php echo ($coupon['percentot'] == 2 ? 'selected="selected"' : ''); ?>><?php echo $curr_symb; ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON4').':'); ?>
					<input type="number" name="value" value="<?php echo $coupon['value']; ?>" size="40" min="0" step="any" />
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON5').':'); ?>
					<input type="number" name="mincost" value="<?php echo $coupon['mincost']; ?>" size="40" min="0" step="any" />
				<?php echo $vik->closeControl(); ?>

				<?php 
				$elements = array(
					JHtml::_('select.option', 1, JText::_('VAPEMPCOUPONPUBMODEOPT1')),
					JHtml::_('select.option', 2, JText::_('VAPEMPCOUPONPUBMODEOPT2')),
				);
				?>
				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON19').':'); ?>
					<select name="pubmode" id="vap-pubmode-sel">
						<?php echo JHtml::_('select.options', $elements, 'value', 'text', $coupon['pubmode']); ?>
					</select>
					<?php
					echo $vik->createPopover(array(
						'title'   => JText::_('VAPEMPMANAGECOUPON19'),
						'content' => JText::_('VAPEMPMANAGECOUPON19_DESC'),
					));
					?>
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON6').':'); ?>
					<input class="calendar" type="text" name="datestart" id="vapdatestart" size="20" value="<?php echo $coupon['dstart']; ?>" />
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON7').':'); ?>
					<input class="calendar" type="text" name="dateend" id="vapdateend" size="20" value="<?php echo $coupon['dend']; ?>" />
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON9').':'); ?>
					<select id="vap-lastminute-sel">
						<option value="1" <?php echo ($coupon['lastminute'] > 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPYES'); ?></option>
						<option value="0" <?php echo ($coupon['lastminute'] == 0 ? 'selected="selected"' : ''); ?>><?php echo JText::_('VAPNO'); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>

				<?php
				$control = array();
				$control['style'] = $coupon['lastminute'] <= 0 ? 'display: none;' : '';
				echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON10').':', 'vap-lastminute-box', $control); ?>
					<input type="number" name="lastminute" value="<?php echo $coupon['lastminute']; ?>" size="40" min="1" />
					<?php echo JText::_('VAPFORMATDAYS'); ?>
				<?php echo $vik->closeControl(); ?>

				<?php echo $vik->openControl(JText::_('VAPEMPMANAGECOUPON16').':'); ?>
					<select name="id_services[]" multiple id="vap-services-sel">
						<?php foreach ($this->services as $service) { ?>
							<option value="<?php echo $service['id']; ?>" <?php echo (in_array($service['id'], $this->selectedServices) ? 'selected="selected"' : ''); ?>><?php echo $service['name']; ?></option>
						<?php } ?>
					</select>
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

		<!-- NOTES -->

		<?php echo $vik->bootAddTab('set', 'set_notes', JText::_('VAPEMPMANAGECOUPON15')); ?>

			<?php echo $vik->openEmptyFieldset(); ?>

				<div class="control-group">
					<textarea name="notes" style="width: 80%; height: 200px;resize: vertical;"><?php echo $coupon['notes']; ?></textarea>
				</div>

			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" />
	
	<input type="hidden" name="id" value="<?php echo $coupon['id']; ?>" />
	<input type="hidden" name="task" value="empeditcoupon.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<?php
JText::script('VAPCONFDIALOGMSG');
?>

<script>
	
	function vapCloseCoupons() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empcoupons&Itemid=' . $itemid, false); ?>';
	}

	var validator = new VikFormValidator('#empareaForm');
	
	<?php if ($auth->manageCoupons()) { ?>

		function vapSaveCoupon(close) {
			
			if (validator.validate()) {
				if(close) {
					jQuery('#vaphiddenreturn').val('1');
				}
				
				document.empareaForm.submit();
			}
		}

	<?php } ?>
	
	<?php if ($auth->manageCoupons() && $type == 2) { ?>
		
		function vapRemoveCoupon() {

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			document.location.href = '<?php echo JRoute::_("index.php?option=com_vikappointments&task=empeditcoupon.delete&cid[]=" . $coupon['id'] . "&Itemid=" . $itemid, false); ?>';
		}

	<?php } ?>
	
	jQuery(document).ready(function() {

		jQuery("#vap-type-sel").select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});
		
		jQuery("#vap-percentot-sel, #vap-lastminute-sel, #vap-autoremove-sel").select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 100
		});

		jQuery("#vap-pubmode-sel").select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery("#vap-services-sel").select2({
			placeholder: '<?php echo addslashes(JText::_("VAPEMPMANAGECOUPON17")); ?>',
			allowClear: true,
			width: 300
		});

		jQuery("#vap-lastminute-sel").on('change', function(){
			if( jQuery(this).val() == "1" ) {
				jQuery('.vap-lastminute-box input').val(1);
				jQuery('.vap-lastminute-box').show();
			} else {
				jQuery('.vap-lastminute-box input').val(0);
				jQuery('.vap-lastminute-box').hide();
			}
		});

		jQuery("#vap-type-sel").on('change', function(){
			if( jQuery(this).val() == "1" ) {
				jQuery('#vap-maxq-input').prop('disabled', true);
				jQuery('.vap-autoremove-box').hide();
			} else {
				jQuery('#vap-maxq-input').prop('disabled', false);
				jQuery('.vap-autoremove-box').show();
			}
		});
		
	});

	jQuery(function() {

		var sel_format 	 = "<?php echo $date_format; ?>";
		var df_separator = sel_format[1];

		sel_format = sel_format.replace(new RegExp("\\"+df_separator, 'g'), "");

		if (sel_format == "Ymd") {
			Date.prototype.format = "yy"+df_separator+"mm"+df_separator+"dd";
		} else if (sel_format == "mdY") {
			Date.prototype.format = "mm"+df_separator+"dd"+df_separator+"yy";
		} else {
			Date.prototype.format = "dd"+df_separator+"mm"+df_separator+"yy";
		}
	
		var today = new Date();
	
		jQuery("#vapdatestart:input, #vapdateend:input").datepicker({
			dateFormat: new Date().format,
		});
		
	});
	
</script>
