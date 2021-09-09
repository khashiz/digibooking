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

$order 			= $this->order;
$payment 		= $this->payment;
$payment_args 	= $this->payment_args;

// SET EMPLOYEE TIMEZONE
VikAppointments::setCurrentTimezone($employee['timezone']);

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

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
echo JLayoutHelper::render('emparea.toolbar');
?>

<div class="vapeditempheaderdiv">
	<div class="vapeditemptitlediv">
		<h2><?php echo JText::sprintf('VAPEMPSUBSCRORDERTITLE', $employee['firstname'] . ' ' . $employee['lastname']); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<div class="vapempbtn">
			<button type="button" onClick="vapCloseOrder();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<div class="vap-subscrord-cont">
	
	<div class="vap-subscrord-left">
		<div class="vap-subscrord-line order-subscr">
			<span>
				<?php echo $order['transaction_name']; ?>
			</span>

			<?php 
			$is = file_exists(VAPINVOICE . DIRECTORY_SEPARATOR . 'employees' . DIRECTORY_SEPARATOR . $order['id'] . '-' . $order['sid'] . '.pdf');
			if ($is) { ?>

				<div class="vap-printable">
					<a
						href="<?php echo VAPINVOICE_URI . "employees/{$order['id']}-{$order['sid']}.pdf"; ?>"
						target="_blank"
						title="<?php echo JText::_('VAPORDERINVOICEACT'); ?>"
					>
						<i class="fa fa-file-pdf-o"></i>
					</a>
				</div>

			<?php } ?>
		</div>
		<div class="vap-subscrord-line order-numkey">
			<?php echo $order['id'] . '-' . $order['sid']; ?>
		</div>
		<div class="vap-subscrord-line order-date">
			<?php echo date($date_format . ' ' . $time_format, $order['createdon']); ?>
		</div>
		<div class="vap-subscrord-line order-status vap-allorders-status<?php echo strtolower($order['status']); ?>">
			<?php echo strtoupper(JText::_('VAPSTATUS' . ($order['status']))); ?>
		</div>
	</div>
	
	<div class="vap-subscrord-right">
		<div class="vap-subscrord-top">
			
		</div>
		<div class="vap-subscrord-middle">
			<?php
			if (count($payment_args) > 0)
			{
				echo VikAppointments::printPriceCurrencySymb($payment_args['total_to_pay']);
			}
			else if ($order['total_cost'] > 0)
			{
				echo VikAppointments::printPriceCurrencySymb($order['total_cost']);
			}
			else
			{
				echo strtoupper(JText::_('VAPTRIAL'));
			}
			?>
		</div>
		
		<div class="vap-subscrord-bottom" style="display: none;">
			<?php
			if (count($payment_args))
			{
				if (!empty($payment['file']))
				{
					/**
					 * Instantiate the payment using the platform handler.
					 *
					 * @since 1.6.3
					 */
					$obj = UIApplication::getInstance()->getPaymentInstance($payment['file'], $payment_args, $payment['params']);
					
					$obj->showPayment();
				}
			}
			else if ($order['tot_paid'] > 0)
			{
				?>
				<div class="vap-subscrord-line order-totpaid">
					<?php echo JText::sprintf('VAPEMPSUBSCRTOTPAID', VikAppointments::printPriceCurrencySymb($order['tot_paid'])); ?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	
</div>

<?php
if (!empty($payment['note']) && $order['status'] == 'CONFIRMED')
{
	?>
	<div class="vappaymentouternotes emp-subscr">
		<div class="vappaymentnotes">
			<?php
			/**
			 * Render HTML description to interpret attached plugins.
			 * 
			 * @since 1.6.3
			 */
			echo VikAppointments::renderHtmlDescription($payment['note'], 'payment');
			?>
		</div>
	</div>
	<?php
}
else if (!empty($payment['prenote']))
{
	?>
	<div class="vappaymentouternotes emp-subscr">
		<div class="vappaymentnotes">
			<?php
			/**
			 * Render HTML description to interpret attached plugins.
			 * 
			 * @since 1.6.3
			 */
			echo VikAppointments::renderHtmlDescription($payment['prenote'], 'paymentorder');
			?>	
		</div>
	</div>
	<?php
}
?>

<script>
	
	<?php
	/**
	 * The statement below cannot be wrapped within 
	 * a jQuery block as the events MUST be registered
	 * only once the payment box has been moved.
	 *
	 * @since 1.6
	 */
	?>
	var bottomBox  = jQuery('.vap-subscrord-bottom');

	if (bottomBox.height() > 128) {
		var html = bottomBox.html();
		bottomBox.remove();
		jQuery('<div class="vap-subscrord-bigpay">'+html+'</div>').insertAfter('.vap-subscrord-cont');
	}

	bottomBox.show();	

	function vapCloseOrder() {
		document.location.href = '<?php echo JRoute::_("index.php?option=com_vikappointments&view=empsubscrorder&Itemid=$itemid", false); ?>';
	}
	
</script>
