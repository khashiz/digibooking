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

$sel = $this->order;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$js_subscr 			= array();
$subscr_sel_name 	= '';
$employee_sel_name 	= '';
$payment_sel_name 	= '';

?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">
	
	<div class="span5">
		<?php echo $vik->openEmptyFieldset(); ?>
			
			<!-- SUBSCRIPTION - Dropdown -->
			<?php
			$subscriptions = array();
			$subscriptions[0] = array(JHtml::_('select.option', '', ''));

			$keys = array(
				JText::_('JUNPUBLISHED'),
				JText::_('JPUBLISHED'),
			);

			foreach ($this->subscriptions as $s)
			{
				$key = $keys[$s['published']];

				if (!isset($subscriptions[$key]))
				{
					$subscriptions[$key] = array();
				}

				$js_subscr[$s['id']] = $s['price'];
	
				if ($s['id'] == $sel['id_subscr'])
				{
					$subscr_sel_name = $s['name'];
				}

				$subscriptions[$key][] = JHtml::_('select.option', $s['id'], $s['name']);
			}

			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCRORD5').'*:'); ?>
				<?php if ($sel['status'] == 'PENDING') { ?>
					<?php
					$params = array(
						'id' 			=> 'vap-subscr-select',
						'list.attr'		=> array('class' => 'required', 'onChange' => 'subscrChanged();'),
						'group.items' 	=> null,
						'list.select'	=> $sel['id_subscr'],
					);
					echo JHtml::_('select.groupedList', $subscriptions, 'id_subscr', $params);
					?>
				<?php } else { ?>
					<strong><?php echo $subscr_sel_name; ?></strong>
					<input type="hidden" name="id_subscr" value="<?php echo $sel['id_subscr']; ?>"/>
				<?php } ?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- EMPLOYEE - Dropdown -->
			<?php
			$employees = array();
			$employees[] = JHtml::_('select.option', '', '');

			foreach ($this->employees as $e)
			{
				if ($e['id'] == $sel['id_employee'])
				{
					$employee_sel_name = $e['lastname'] . ' ' . $e['firstname'];
				}

				$employees[] = JHtml::_('select.option', $e['id'], $e['lastname'] . ' ' . $e['firstname']);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCRORD4').'*:'); ?>
				<?php if ($sel['status'] == 'PENDING') { ?>
					<select name="id_employee" id="vap-employee-select" class="required">
						<?php echo JHtml::_('select.options', $employees, 'value', 'text', $sel['id_employee']); ?>
					</select>
				<?php } else { ?>
					<strong><?php echo $employee_sel_name; ?></strong>
					<input type="hidden" name="id_employee" value="<?php echo $sel['id_employee']; ?>"/>
				<?php } ?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- PAYMENT - Dropdown -->
			<?php
			$payments = array();
			$payments[0] = array(JHtml::_('select.option', '', ''));

			$keys = array(
				JText::_('JUNPUBLISHED'),
				JText::_('JPUBLISHED'),
			);

			foreach ($this->payments as $p)
			{
				$key = $keys[$p['published']];

				if (!isset($payments[$key]))
				{
					$payments[$key] = array();
				}
	
				if ($p['id'] == $sel['id_payment'])
				{
					$payment_sel_name = $p['name'];
				}

				$payments[$key][] = JHtml::_('select.option', $p['id'], $p['name']);
			}
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCRORD9').':'); ?>
				<?php if ($sel['status'] == 'PENDING') { ?>
					<?php
					$params = array(
						'id' 			=> 'vap-payment-select',
						'group.items' 	=> null,
						'list.select'	=> $sel['id_payment'],
					);
					echo JHtml::_('select.groupedList', $payments, 'id_payment', $params);
					?>
				<?php } else { ?>
					<strong><?php echo $payment_sel_name; ?></strong>
					<input type="hidden" name="id_payment" value="<?php echo $sel['id_payment']; ?>"/>
				<?php } ?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- TOTAL COST - Number -->
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCRORD6').':'); ?>
				<input type="number" name="total_cost" value="<?php echo $sel["total_cost"]; ?>" 
					size="40" min="0" step="any" id="vaptcost" <?php echo ($sel['status'] == 'PENDING' ? '' : 'readonly="readonly"'); ?> />
				&nbsp;<?php echo $config->get('currencysymb'); ?>
			<?php echo $vik->closeControl(); ?>
			
			<!-- STATUS - Dropdown -->
			<?php 
			$elements = array(
				JHtml::_('select.option', 'CONFIRMED', JText::_('VAPSTATUSCONFIRMED')),
				JHtml::_('select.option', 'PENDING', JText::_('VAPSTATUSPENDING')),
				JHtml::_('select.option', 'REMOVED', JText::_('VAPSTATUSREMOVED')),
			);
			?>
			<?php echo $vik->openControl(JText::_('VAPMANAGESUBSCRORD8').':'); ?>
				<select name="status" id="vap-status-sel">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $sel['status']); ?>
				</select>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-subscr-select, #vap-employee-select, #vap-payment-select').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('#vap-status-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

	});

	var SUBSCRIPTIONS = <?php echo json_encode($js_subscr); ?>;

	function subscrChanged() {
		var sub = jQuery('#vap-subscr-select').val();
		if (!jQuery.isEmptyObject(SUBSCRIPTIONS[sub])) {
			jQuery('#vaptcost').val(SUBSCRIPTIONS[sub]);
		}
	}

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
