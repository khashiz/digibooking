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

$sel = $this->payment;

$vik = UIApplication::getInstance();

$editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

$config = UIFactory::getConfig();

?>

<?php if ($sel['id'] != -1 && VikAppointments::isMultilanguage()) { ?>

	<div class="btn-toolbar" id="filter-bar">
		<div class="btn-group pull-right">
			<?php foreach (VikAppointments::getKnownLanguages() as $lang) { 
				$lang_name = explode('-', $lang);
				$lang_name = $lang_name[1];
				?>
				<button type="button" class="btn" onClick="SELECTED_TAG='<?php echo $lang; ?>';vapOpenJModal('langtag', null, true);">
					<i class="icon">
						<img src="<?php echo VAPASSETS_URI . 'css/flags/' . strtolower($lang_name) . '.png'; ?>" />
					</i>
					&nbsp;<?php echo $lang; ?>
				</button>
			<?php } ?>
		</div>
	</div>

<?php } ?>

<form name="adminForm" id="adminForm" action="index.php" method="post">

	<?php echo $vik->bootStartTabSet('payment', array('active' => 'payment_details')); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('payment', 'payment_details', JText::_('VAPORDERPAYMENT')); ?>
	
			<div class="span6">
				<?php echo $vik->openFieldset(JText::_('VAPORDERTITLE2'), 'form-horizontal'); ?>
					
					<!-- NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT1').'*:'); ?>
						<input type="text" name="name" class="required" value="<?php echo $sel['name']; ?>" size="30" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- PAYMENT CLASS - Select -->
					<?php
					$drivers = array();
					$drivers[] = JHtml::_('select.option', '', '');
					foreach ($vik->getPaymentDrivers() as $driver)
					{
						$name = basename($driver);
						$drivers[] = JHtml::_('select.option', $name, $name);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT2').'*:'); ?>
						<select name="file" class="required" id="vap-file-sel">
							<?php echo JHtml::_('select.options', $drivers, 'value', 'text', $sel['file']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- CHARGE - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT4').':'); ?>
						<input type="number" name="charge" value="<?php echo $sel['charge']; ?>" size="5" step="any" />
						&nbsp;<?php echo $config->get('currencysymb'); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- PUBLISHED - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
					?> 
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT3').':'); ?>
						<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- ALLOWED FOR - Select -->
					<?php
					$options = array();
					$options[] = JHtml::_('select.option', 1, JText::_('VAPMANAGEPAYALLOWEDFOROPT1'));
					$options[] = JHtml::_('select.option', 2, JText::_('VAPMANAGEPAYALLOWEDFOROPT2'));
					$options[] = JHtml::_('select.option', 3, JText::_('VAPMANAGEPAYALLOWEDFOROPT3'));

					if ($sel['appointments'] && $sel['subscr'])
					{
						$allowed_for = 3;
					}
					else if ($sel['subscr'])
					{
						$allowed_for = 2;
					}
					else
					{
						$allowed_for = 1;
					}

					?> 
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT11').':'); ?>
						<select name="allowedfor" id="vap-allowedfor-sel" <?php echo $sel['id_employee'] > 0 ? 'disabled="disabled"' : ''; ?>>
							<?php echo JHtml::_('select.options', $options, 'value', 'text', $allowed_for); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- ACCESS - Select -->
					<?php
					echo $vik->openControl(JText::_('JFIELD_ACCESS_LABEL').':');
					echo JHtml::_('access.level', 'level', $sel['level'], '', false, 'vap-level-select');
					echo $vik->createPopover(array(
						'title' 	=> JText::_('JFIELD_ACCESS_LABEL'),
						'content' 	=> JText::_('JFIELD_ACCESS_DESC'),
					));
					echo $vik->closeControl();
					?>
					
					<!-- SET AUTO CONFIRMED - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['setconfirmed'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['setconfirmed'] == 0);
					?> 
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT5').':'); ?>
						<?php echo $vik->radioYesNo('setconfirmed', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGEPAYMENT5'),
							'content'	=> JText::_('VAPMANAGEPAYMENT5_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- ICON - Fieldset -->
					<?php
					$elements = array(
						$vik->initOptionElement('', '', $sel['icontype'] == 0),
						$vik->initOptionElement(1, JText::_('VAPPAYMENTICONOPT1'), $sel['icontype'] == 1),
						$vik->initOptionElement(2, JText::_('VAPPAYMENTICONOPT2'), $sel['icontype'] == 2)
					);

					$font_icons = array(
						$vik->initOptionElement('', '', false),
						$vik->initOptionElement('paypal', 'PayPal', $sel['icon'] == 'paypal'),
						$vik->initOptionElement('credit-card', 'Credit Card', $sel['icon'] == 'credit-card'),
						$vik->initOptionElement('credit-card-alt', 'Credit Card Alt', $sel['icon'] == 'credit-card-alt'),
						$vik->initOptionElement('money', 'Money', $sel['icon'] == 'money'),

						$vik->initOptionElement('cc-visa', 'Visa', $sel['icon'] == 'cc-visa'),
						$vik->initOptionElement('cc-mastercard', 'Mastercard', $sel['icon'] == 'cc-mastercard'),
						$vik->initOptionElement('cc-amex', 'American Express', $sel['icon'] == 'cc-amex'),
						$vik->initOptionElement('cc-discover', 'Discovery', $sel['icon'] == 'cc-discover'),
						$vik->initOptionElement('cc-jcb', 'JCB', $sel['icon'] == 'cc-jcb'),
						$vik->initOptionElement('cc-diners-club', 'Diners Club', $sel['icon'] == 'cc-diners-club'),
						$vik->initOptionElement('cc-stripe', 'Stripe', $sel['icon'] == 'cc-stripe'),

						$vik->initOptionElement('eur', 'Euro', $sel['icon'] == 'eur'),
						$vik->initOptionElement('usd', 'Dollar', $sel['icon'] == 'usd'),
						$vik->initOptionElement('gbp', 'Pound', $sel['icon'] == 'gbp'),
					);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT15')); ?>
						<?php echo $vik->dropdown('icontype', $elements, 'vap-icontype-sel'); ?>
						
						<div id="vap-fonticon-wrapper" style="margin-top: 5px;<?php echo ($sel['icontype'] == 1 ? '' : 'display: none;'); ?>">
							<?php echo $vik->dropdown('font_icon', $font_icons, 'vap-fonticon-sel'); ?>
							<span id="vap-fonticon-preview"><i class="fa fa-<?php echo $sel['icon']; ?> big" style="margin-left: 10px;"></i></span>
						</div>

						<div id="vap-iconupload-wrapper" style="margin-top: 5px;<?php echo ($sel['icontype'] == 2 ? '' : 'display: none;'); ?>">
							<?php echo $vik->getMediaField('upload_icon', $sel['icontype'] == 2 ? $sel['icon'] : ''); ?>
						</div>

					<?php echo $vik->closeControl(); ?>

					<!-- POSITION - Select -->
					<?php
					$elements = array(
						JHtml::_('select.option', '', ''),
						JHtml::_('select.option', 'vap-payment-position-top-left', JText::_('VAPPAYMENTPOSOPT2')),
						JHtml::_('select.option', 'vap-payment-position-top-center', JText::_('VAPPAYMENTPOSOPT3')),
						JHtml::_('select.option', 'vap-payment-position-top-right', JText::_('VAPPAYMENTPOSOPT4')),
						JHtml::_('select.option', 'vap-payment-position-bottom-left', JText::_('VAPPAYMENTPOSOPT5')),
						JHtml::_('select.option', 'vap-payment-position-bottom-center', JText::_('VAPPAYMENTPOSOPT6')),
						JHtml::_('select.option', 'vap-payment-position-bottom-right', JText::_('VAPPAYMENTPOSOPT7')),
					);
					?> 
					<?php echo $vik->openControl(JText::_('VAPMANAGEPAYMENT12').':'); ?>
						<select name="position" id="vap-position-sel">
							<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['position']); ?>
						</select>
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGEPAYMENT12'),
							'content'	=> JText::_('VAPMANAGEPAYMENT12_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
				<?php echo $vik->closeFieldset(); ?>
			</div>

			<?php if ($this->isOwner) { ?>

				<div class="span6">
					<?php echo $vik->openFieldset(JText::_('VAPMANAGEPAYMENT8'), 'form-horizontal'); ?>

						<div class="vikpayparamdiv">
							<div class="vappaymentparam"><?php echo JText::_('VAPMANAGEPAYMENT9'); ?></div>
						</div>

					<?php echo $vik->closeFieldset(); ?>
				</div>

			<?php } ?>

		<?php echo $vik->bootEndTab(); ?>

		<!-- NOTES -->

		<?php echo $vik->bootAddTab('payment', 'payment_notes', JText::_('VAPMANAGEPAYMENT7')); ?>

			<div class="span6">
				<?php echo $vik->openFieldset(JText::_('VAPMANAGEPAYMENT13'), 'form-horizontal'); ?>
					<div class="control-group">
						<?php echo $editor->display('prenote', $sel['prenote'], 400, 200, 70, 20); ?>
					</div>
				<?php echo $vik->closeFieldset(); ?>
			</div>
	
			<div class="span6">
				<?php echo $vik->openFieldset(JText::_('VAPMANAGEPAYMENT14'), 'form-horizontal'); ?>
					<div class="control-group">
						<?php echo $editor->display('note', $sel['note'], 400, 200, 70, 20); ?>
					</div>
				<?php echo $vik->closeFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id_employee" value="<?php echo $sel['id_employee']; ?>" />
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />

</form>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-langtag',
	array(
		'title'       => JText::_('VAPPAYLANGTRANSLATION') . '<span id="tag-target"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);

JText::script('VAPPAYMENTPOSOPT1');
JText::script('VAPPAYMENTICONOPT0');
?>

<script>

	jQuery(document).ready(function() {

		<?php if ($this->isOwner && $sel['id'] > 0) { ?>
			vapPaymentGatewayChanged();
		<?php } ?>

		jQuery('#vap-file-sel').select2({
			placeholder: '--',
			allowClear: false,
			width: 250
		});

		jQuery('#vap-position-sel').select2({
			minimumResultsForSearch: -1,
			placeholder: Joomla.JText._('VAPPAYMENTPOSOPT1'),
			allowClear: true,
			width: 250
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

		jQuery('#vap-allowedfor-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 250
		});

		jQuery('#vap-level-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 250
		});

		jQuery('#vap-icontype-sel').on('change', function(){

			var val = jQuery(this).val();

			if( val == 1 ) {
				jQuery('#vap-fonticon-wrapper').show();
				jQuery('#vap-iconupload-wrapper').hide();
			} else if( val == 2 ) {
				jQuery('#vap-fonticon-wrapper').hide();
				jQuery('#vap-iconupload-wrapper').show();
			} else {
				jQuery('#vap-fonticon-wrapper').hide();
				jQuery('#vap-iconupload-wrapper').hide();
			}

		});

		jQuery('#vap-fonticon-sel').on('change', function(){
			jQuery('#vap-fonticon-preview i').attr('class', 'fa fa-'+jQuery(this).val()+' big');
		});

		<?php if ($this->isOwner) { ?>

			jQuery('#vap-file-sel').on('change', vapPaymentGatewayChanged);

		<?php } else { ?>

			jQuery('#vap-file-sel').attr('disabled', true);

		<?php } ?>

	});

	<?php if ($this->isOwner) { ?>
	
		function vapPaymentGatewayChanged() {
			var gp = jQuery('#vap-file-sel').val();
			
			jQuery.noConflict();
			
			jQuery('.vikpayparamdiv').html('');
			
			var jqxhr = jQuery.ajax({
				type: 'POST',
				url: 'index.php?option=com_vikappointments&task=get_payment_fields&tmpl=component',
				data: {
					gpn: gp,
					id_gp: <?php echo $sel['id']; ?>
				}
			}).done(function(resp) {
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

	<?php } ?>

	// TRANSLATIONS
	
	var SELECTED_TAG = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'langtag') {
			url = 'index.php?option=com_vikappointments&task=managelangpayment&id_payment=<?php echo $sel['id']; ?>&tag=' + SELECTED_TAG;

			jQuery('#tag-target').text(' ' + SELECTED_TAG);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#payment_"]').on('click', function() {
			var href = jQuery(this).attr('href').substr(1);
			jQuery('input[name="tabname"]').val(href);
		});

	});

	// validate

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
	
</script>
