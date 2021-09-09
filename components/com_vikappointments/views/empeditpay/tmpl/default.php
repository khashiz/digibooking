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

$payment = $this->payment;

$editor = JFactory::getEditor();

$vik = UIApplication::getInstance();

$type = $payment['id'] > 0 ? 2 : 1;

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
		<h2><?php echo JText::_($type == 2 ? 'VAPEDITPAYTITLE' : 'VAPNEWPAYTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->managePayments()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSavePayment(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
			<div class="vapempbtn">
				<button type="button" onClick="vapSavePayment(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>

		<?php if ($auth->managePayments() && $type == 2) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapRemovePayment();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapClosePayment();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditpay'); ?>" method="post" name="empareaForm" id="empareaForm">
	
	<?php echo $vik->bootStartTabSet('set', array('active' => 'set_details')); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('set', 'set_details', JText::_('VAPORDERTITLE2')); ?>

			<?php echo $vik->openEmptyFieldset(); ?>
		
				<?php echo $vik->openControl(JText::_('VAPEDITPAYMENT1').'*:'); ?>
					<input type="text" name="name" value="<?php echo $this->escape($payment['name']); ?>" size="30" id="vapname" class="required" />
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITPAYMENT2').'*:'); ?>
					<select name="file" id="vap-file-sel" class="required">
						<option></option>
						<?php
						foreach ($vik->getPaymentDrivers() as $driver)
						{
							$name = basename($driver);
							?>
							<option value="<?php echo $name; ?>" <?php echo $payment['file'] == $name ? " selected=\"selected\"" : ""; ?>><?php echo $name ?></option>
						<?php } ?>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITPAYMENT3').':'); ?>
					<select name="published" class="vik-dropdown-small">
						<option value="1"<?php echo intval($payment['published']) == 1 ? " selected=\"selected\"" : ""; ?>><?php echo JText::_('VAPYES'); ?></option>
						<option value="0"<?php echo intval($payment['published']) == 0 ? " selected=\"selected\"" : ""; ?>><?php echo JText::_('VAPNO'); ?></option>
					</select>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITPAYMENT4').':'); ?>
					<input type="number" name="charge" value="<?php echo $payment['charge']; ?>" max="99999" step="any" size="5" />
					<?php echo VikAppointments::getCurrencySymb(); ?>
				<?php echo $vik->closeControl(); ?>
				
				<?php echo $vik->openControl(JText::_('VAPEDITPAYMENT7').':'); ?>
					<select name="setconfirmed" class="vik-dropdown-small">
						<option value="1"<?php echo intval($payment['setconfirmed']) == 1 ? " selected=\"selected\"" : ""; ?>><?php echo JText::_('VAPYES'); ?></option>
						<option value="0"<?php echo intval($payment['setconfirmed']) == 0 ? " selected=\"selected\"" : ""; ?>><?php echo JText::_('VAPNO'); ?></option>
					</select>

					<?php echo $vik->createPopover(array(
						'title' 	=> JText::_('VAPEDITPAYMENT7'),
						'content' 	=> JText::_('VAPEDITPAYMENTDESC7'),
					)); ?>
				<?php echo $vik->closeControl(); ?>


				<?php
				$elements = array(
					$vik->initOptionElement('', '', $payment['icontype'] == 0),
					$vik->initOptionElement(1, JText::_('VAPPAYMENTICONOPT1'), $payment['icontype'] == 1),
				);

				$font_icons = array(
					$vik->initOptionElement('', '', false),
					$vik->initOptionElement('paypal', 'PayPal', $payment['icon'] == 'paypal'),
					$vik->initOptionElement('credit-card', 'Credit Card', $payment['icon'] == 'credit-card'),
					$vik->initOptionElement('credit-card-alt', 'Credit Card Alt', $payment['icon'] == 'credit-card-alt'),
					$vik->initOptionElement('money', 'Money', $payment['icon'] == 'money'),

					$vik->initOptionElement('cc-visa', 'Visa', $payment['icon'] == 'cc-visa'),
					$vik->initOptionElement('cc-mastercard', 'Mastercard', $payment['icon'] == 'cc-mastercard'),
					$vik->initOptionElement('cc-amex', 'American Express', $payment['icon'] == 'cc-amex'),
					$vik->initOptionElement('cc-discover', 'Discovery', $payment['icon'] == 'cc-discover'),
					$vik->initOptionElement('cc-jcb', 'JCB', $payment['icon'] == 'cc-jcb'),
					$vik->initOptionElement('cc-diners-club', 'Diners Club', $payment['icon'] == 'cc-diners-club'),
					$vik->initOptionElement('cc-stripe', 'Stripe', $payment['icon'] == 'cc-stripe'),

					$vik->initOptionElement('eur', 'Euro', $payment['icon'] == 'eur'),
					$vik->initOptionElement('usd', 'Dollar', $payment['icon'] == 'usd'),
					$vik->initOptionElement('gbp', 'Pound', $payment['icon'] == 'gbp'),
				);
				?>
				<?php echo $vik->openControl(JText::_('VAPEDITPAYMENT10')); ?>
					<?php echo $vik->dropdown('icontype', $elements, 'vap-icontype-sel'); ?>
					
					<div id="vap-fonticon-wrapper" style="margin-top: 5px;<?php echo ($payment['icontype'] == 1 ? '' : 'display: none;'); ?>">
						<?php echo $vik->dropdown('font_icon', $font_icons, 'vap-fonticon-sel'); ?>
						<span id="vap-fonticon-preview"><i class="fa fa-<?php echo $payment['icon']; ?> big" style="margin-left: 10px;"></i></span>
					</div>

				<?php echo $vik->closeControl(); ?>

			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

		<!-- PARAMETERS -->

		<?php echo $vik->bootAddTab('set', 'set_params', JText::_('VAPEDITPAYMENT5')); ?>

			<?php echo $vik->openEmptyFieldset('vikpayparamdiv'); ?>

				<div class="control-group"><?php echo JText::_('VAPEDITPAYMENT6'); ?></div>

			<?php echo $vik->closeEmptyFieldset(); ?>

			<!-- used to display correctly a popover used on the last field -->
			<div style="margin-bottom: 30px;">&nbsp;</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- NOTES BEFORE PURCHASE -->

		<?php echo $vik->bootAddTab('set', 'set_prenote', JText::_('VAPEDITPAYMENT8')); ?>

			<?php echo $vik->openEmptyFieldset(); ?>

				<div class="control-group">
					<?php echo $editor->display('prenote', $payment['prenote'], 400, 200, 20, 20); ?>
				</div>

			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

		<!-- NOTES AFTER PURCHASE -->

		<?php echo $vik->bootAddTab('set', 'set_note', JText::_('VAPEDITPAYMENT9')); ?>

			<?php echo $vik->openEmptyFieldset(); ?>

				<div class="control-group">
					<?php echo $editor->display('note', $payment['note'], 400, 200, 20, 20); ?>
				</div>

			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" />
	
	<input type="hidden" name="id" value="<?php echo $payment['id']; ?>" />
	<input type="hidden" name="task" value="empeditpay.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<?php
JText::script('VAPCONFDIALOGMSG');
JText::script('VAPEDITPAYMENT6');
JText::script('VAPPAYMENTICONOPT0');
?>

<script>

    jQuery(document).ready(function() {

        jQuery('.vik-dropdown').select2({
            placeholder: '--',
            allowClear: true,
            width: 300
        });

        jQuery('.vik-dropdown-small').select2({
        	minimumResultsForSearch: -1,
            allowClear: false,
            width: 100
        });

        jQuery('#vap-file-sel').select2({
        	placeholder: '--',
        	allowClear: true,
        	width: 300
        });

        jQuery('#vap-icontype-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: true,
			placeholder: Joomla.JText._('VAPPAYMENTICONOPT0'),
			width: 200
		});

		jQuery('#vap-fonticon-sel').select2({
			placeholder: '--',
			allowClear: false,
			width: 200
		});

		jQuery('#vap-icontype-sel').on('change', function() {

			var val = jQuery(this).val();

			if (val == 1) {
				jQuery('#vap-fonticon-wrapper').show();
			} else {
				jQuery('#vap-fonticon-wrapper').hide();
			}

		});

		jQuery('#vap-fonticon-sel').on('change', function() {
			jQuery('#vap-fonticon-preview i').attr('class', 'fa fa-'+jQuery(this).val()+' big');
		});

        jQuery('#vap-file-sel').on('change', vapPaymentGatewayChanged);

        <?php if ($payment['id'] != -1) { ?>

			vapPaymentGatewayChanged();

		<?php } ?>

    });
	
	function vapClosePayment() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emppaylist&Itemid=' . $itemid, false); ?>';
	}

	var validator = new VikFormValidator('#empareaForm');
	
	<?php if ($auth->managePayments()) { ?>

		function vapSavePayment(close) {
			
			if (validator.validate()) {
				if(close) {
					jQuery('#vaphiddenreturn').val('1');
				}
				
				document.empareaForm.submit();
			}
		}

	<?php } ?>
	
	<?php if ($auth->managePayments() && $type == 2) { ?>

		function vapRemovePayment() {

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			document.location.href = '<?php echo JRoute::_("index.php?option=com_vikappointments&task=empeditpay.delete&cid[]=" . $payment['id'] . "&Itemid=" . $itemid, false); ?>';
		}

	<?php } ?>
	
	function vapPaymentGatewayChanged() {
		var gp = jQuery('#vap-file-sel').val();
		
		jQuery.noConflict();
		
		validator.unregisterFields('.vikpayparamdiv .required');

		jQuery('.vikpayparamdiv').html('');
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditpay.getPaymentFields&tmpl=component&Itemid=' . $itemid, false); ?>",
			data: {
				gpn: gp,
				id_gp: <?php echo $payment['id']; ?>
			}
		}).done(function(resp) {

			var obj = null;

			try {
				obj = jQuery.parseJSON(resp);
			} catch (e) {
				obj = [''];
				console.log(e, resp);
			}
			
			if (obj[0].length == 0) {
				obj[0] = '<div class="control-group">'+Joomla.JText._('VAPEDITPAYMENT6')+'</div>';
			}

			jQuery('.vikpayparamdiv').html(obj[0]);

			jQuery('.vikpayparamdiv select').select2({
				allowClear: false,
				width: 300,
			});

			validator.registerFields('.vikpayparamdiv .required');

			jQuery('.vikpayparamdiv .vap-quest-popover').popover();
		});
	}
	
</script>
