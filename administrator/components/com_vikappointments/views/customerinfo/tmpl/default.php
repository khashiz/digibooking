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

/**
 * It seems that Joomla stopped loading JS core for
 * the views loaded with tmpl component. We need to force
 * it to let the pagination accessing Joomla object.
 *
 * @since Joomla 3.8.7
 */
JHtml::_('behavior.core');

$config = UIFactory::getConfig();

$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

$vik = UIApplication::getInstance();

$billing_fields = array( 
	'VAPMANAGECUSTOMER2' 	=> 'billing_name',
	'VAPMANAGECUSTOMER3' 	=> 'billing_mail',
	'VAPMANAGECUSTOMER4' 	=> 'billing_phone',
	'VAPMANAGECUSTOMER10' 	=> 'company',
	'VAPMANAGECUSTOMER11' 	=> 'vatnum',
	'VAPMANAGECUSTOMER20' 	=> 'ssn',

	'VAPMANAGECUSTOMER5' 	=> 'billing_country',
	'VAPMANAGECUSTOMER6' 	=> 'billing_state',
	'VAPMANAGECUSTOMER7' 	=> 'billing_city',
	'VAPMANAGECUSTOMER8' 	=> 'billing_address',
	'VAPMANAGECUSTOMER19' 	=> 'billing_address_2',
	'VAPMANAGECUSTOMER9' 	=> 'billing_zip',
);

$keys = array_keys($billing_fields);

$bounds = array(
	array(0, 6),
	array(6, 12),
);

/**
 * Make sure there is at least a non-empty value
 * within the right box in order to avoid displaying
 * a blank fieldset (@wponly).
 *
 * @since 1.6.3
 */
for ($i = $bounds[1][0], $empty = true; $i < $bounds[1][1] && $empty; $i++)
{
	// check if the field is empty or not
	$empty = empty($this->customer[$billing_fields[$keys[$i]]]);
}

if ($empty)
{
	// unset right box as there is nothing to display
	array_splice($bounds, 1, 1);
}

$default_tz = date_default_timezone_get();

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 40px;display: none;" id="vap-custinfo-toolbar">
		<div class="btn-group pull-left">
			<button type="button" class="btn btn-success" style="width: 120px;" onClick="updateCustomerInfo();">
				<i class="icon-apply icon-white"></i>&nbsp;<?php echo JText::_('VAPSAVE'); ?>
			</button>
		</div>
	</div>

	<?php echo $vik->bootStartTabSet('custinfo', array('active' => $this->tab)); ?>

		<?php echo $vik->bootAddTab('custinfo', 'custinfo_billing', JText::_('VAPMANAGECUSTOMERTITLE2')); ?>

			<?php foreach ($bounds as $tab) { ?>
				
				<div class="span6">
					<?php echo $vik->openEmptyFieldset(); ?>

						<?php 
						for ($i = $tab[0]; $i < $tab[1]; $i++) { 
							$key = $keys[$i];
							$val = $billing_fields[$key];
							?>

							<?php if (!empty($this->customer[$val])) { ?>
								<?php echo $vik->openControl(JText::_($key).":"); ?>
									<input type="text" style="cursor: default;" value="<?php echo $this->escape($this->customer[$val]); ?>" readonly size="32" />
								<?php echo $vik->closeControl(); ?>
							<?php } ?>

						<?php } ?>

					<?php echo $vik->closeEmptyFieldset(); ?>
				</div>

			<?php } ?>

		<?php echo $vik->bootEndTab(); ?>

		<?php echo $vik->bootAddTab('custinfo', 'custinfo_notes', JText::_('VAPMANAGECUSTOMERTITLE4')); ?>

			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control">
						<textarea name="notes" id="vap-custinfo-notes" style="width:97%;height:300px;"><?php echo $this->customer['notes']; ?></textarea>
					</div>
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<?php echo $vik->bootAddTab('custinfo', 'custinfo_appointments', JText::_('VAPMANAGECUSTOMERTITLE5')); ?>


			<?php if (count($this->customer['appointments']) == 0) { ?>

				<div class="span8">
					<p><?php echo JText::_('VAPNORESERVATION'); ?></p>
				</div>
			
			<?php } else { ?>

				<!-- DO NOT use wrapper div.span12 to get a 100% full width -->
				<?php echo $vik->openEmptyFieldset(); ?>	

					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
						<?php echo $vik->openTableHead(); ?>
							<tr>
								<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;"><?php echo JText::_('VAPMANAGERESERVATION1'); ?></th>
								<th class="<?php echo $vik->getAdminThClass(); ?>" width="20%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION4'); ?></th>
								<th class="<?php echo $vik->getAdminThClass(); ?>" width="20%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION3'); ?></th>
								<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION5'); ?></th>
								<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION6'); ?></th>
								<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION9'); ?></th>
							</tr>
						<?php echo $vik->closeTableHead(); ?>
						
						<?php
						for ($i = 0, $n = count($this->customer['appointments']); $i < $n; $i++)
						{
							$row = $this->customer['appointments'][$i];

							/**
							 * Adjust timezone if needed.
							 *
							 * @since 1.6.3
							 */
							if (empty($row['timezone']))
							{
								$row['timezone'] = $default_tz;
							}

							VikAppointments::setCurrentTimezone($row['timezone']);
							?>
							<tr class="row<?php echo ($i % 2); ?>">
								<td style="text-align: left;"><?php echo $row['id'] . '-' . $row['sid']; ?></td>
								
								<td style="text-align: center;"><?php echo $row['service_name']; ?></td>
								
								<td style="text-align: center;"><?php echo $row['employee_name']; ?></td>
								
								<td style="text-align: center;"><?php echo date($dt_format, $row['checkin_ts']); ?></td>
								
								<td style="text-align: center;"><?php echo date($dt_format, VikAppointments::getCheckout($row['checkin_ts'], $row['duration'])); ?></td>
								
								<td style="text-align: center;"><?php echo VikAppointments::printPriceCurrencySymb($row['total_cost']); ?></td>
							</tr>
							<?php
						}

						// always restore timezone once the list is finished
						VikAppointments::setCurrentTimezone($default_tz);
						?>
					
					</table>
					
					<?php echo JHtml::_('form.token'); ?>
					<?php echo $this->navbut; ?>
				
				<?php echo $vik->closeEmptyFieldset(); ?>

			<?php } ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>

	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="customerinfo" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<script>

	var observer = new VikFormObserver('#adminForm');

	jQuery(document).ready(function() {

		observer.exclude('input[name="tabname"]').freeze();

		jQuery('#vap-custinfo-notes').on('keyup', function(e) {

			if (observer.isChanged()) {
				jQuery('#vap-custinfo-toolbar').show();
			} else {
				jQuery('#vap-custinfo-toolbar').hide();
			}

		});
	});

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#custinfo_"]').on('click', function() {
			var href = jQuery(this).attr('href').substr(1);
			jQuery('input[name="tabname"]').val(href);
		});

	});

	function updateCustomerInfo() {
		jQuery('#adminForm').append('<input type="hidden" name="updateinfo" value="1" />');
		jQuery('#adminForm').submit();
	}

</script>
