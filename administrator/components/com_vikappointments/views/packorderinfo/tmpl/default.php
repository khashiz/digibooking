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

$order = $this->order;

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$vik = UIApplication::getInstance();

$charge_str = "";
if ($order['payment_charge'] > 0)
{
	$charge_str = ' (' . (($order['payment_charge'] > 0) ? '+' : '') . VikAppointments::printPriceCurrencySymb($order['payment_charge']) . ')';
}

$custom_f = json_decode($order['custom_f'], true);

?>


<div class="span8">
	<?php echo $vik->openEmptyFieldset(); ?>
	
		<div class="vap-orderbasket-badge large">
			<?php echo date($date_format . ' ' . $time_format, $order['createdon']); ?>
		</div>

		<?php if (strlen($order['payment_name'])) { ?>
			<div class="vap-orderbasket-badge">
			   <?php echo $order['payment_name'].$charge_str; ?> 
			</div>
		<?php } ?>
		<div class="vap-orderbasket-badge <?php echo (strlen($order['payment_name']) == 0 ? 'large' : ''); ?>">
			<?php echo VikAppointments::printPriceCurrencySymb($order['total_cost']); ?>
		</div>
		
		<div class="vap-orderbasket-badge large <?php echo strtolower($order['status']); ?>">
			<?php echo JText::_('VAPSTATUS' . strtoupper($order['status'])); ?>
		</div>
		
	<?php echo $vik->closeEmptyFieldset(); ?>
</div>

<div class="span4">
	<?php echo $vik->openEmptyFieldset(); ?>

		<?php
		foreach ($custom_f as $key => $val)
		{
			if (!empty($val))
			{
				echo $vik->openControl(JText::_($key) . ":");

				if (strlen($val) <= 40)
				{
					/**
					 * Prevent XSS attacks by escaping submitted data.
					 *
					 * @since 1.6.3
					 */
					?>
					<input type="text" value="<?php echo $this->escape($val); ?>" readonly size="32" />
					<?php
				}
				else
				{
					?>
					<textarea readonly cols="40" rows="3"><?php echo $val; ?></textarea>
					<?php
				}
				
				echo $vik->closeControl();
			}
		}
		?>
	
	<?php echo $vik->closeEmptyFieldset(); ?>
</div>

<?php if (count($order['items']) > 0) { ?>
	<div class="span12">
		<?php echo $vik->openEmptyFieldset(); ?>
		
			<?php foreach ($order['items'] as $item) { ?>
				<div class="vap-orderbasket-option">
					<div class="vap-orderbasket-option-details">
						<div class="vap-orderbasket-option-details-left">
							<span class="vap-orderbasket-option-details-name"><?php echo $item['name']; ?></span>
						</div>
						<div class="vap-orderbasket-option-details-right">
							<span class="vap-orderbasket-option-details-numapp">
								<?php echo $item['used_app']."/".$item['num_app'] . " " . strtolower(JText::_('VAPMANAGEPACKORDER16')); ?>
							</span>
							<span class="vap-orderbasket-option-details-quantity">x<?php echo $item['quantity']; ?></span>
							<span class="vap-orderbasket-option-details-price"><?php echo VikAppointments::printPriceCurrencySymb($item['price']); ?></span>
						</div>
					</div>
				</div>
			<?php } ?>
			
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
<?php } ?>

<?php if (strlen($order['log']) > 0) { ?>
	<div class="span12">
		<?php echo $vik->openEmptyFieldset(); ?>
			<div class="control-group">
				<?php echo $vik->getCodeMirror('log', $order['log']); ?>
			</div>
		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
<?php } ?>
