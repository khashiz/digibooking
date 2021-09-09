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

$payments = $this->payments;

$itemid = JFactory::getApplication()->input->getUint('Itemid', 0);

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
		<h2><?php echo JText::sprintf('VAPEMPPAYLISTTITLE', $employee['firstname'] . ' ' . $employee['lastname']); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->managePayments()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapCreatePayment();" id="vapempbtnnew" class="vap-btn blue employee"><?php echo JText::_('VAPNEW'); ?></button>
			</div>

			<div class="vapempbtn">
				<button type="button" onClick="vapRemovePayments();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapClosePaymentsList();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments'); ?>" method="post" name="empareaForm" id="empareaForm" style="min-height: 250px;">
	
	<?php if (count($payments)) { ?>

		<div class="vapemppaylistcont">

			<div class="vap-allorders-singlerow vap-allorders-row1 head" style="text-align: center;">
				<span class="vap-allorders-column" style="text-align:left; width: 5%;">
					<input type="checkbox" onclick="EmployeeArea.checkAll(this)" value="" class="checkall-toggle" />
				</span>
				<span class="vap-allorders-column" style="width: 25%;">
					<?php echo JText::_('VAPEDITPAYMENT1'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 20%;">
					<?php echo JText::_('VAPEDITPAYMENT2'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 16%;">
					<?php echo JText::_('VAPEDITPAYMENT4'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 16%;">
					<?php echo JText::_('VAPEDITPAYMENT3'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 15%;">
					<?php echo JText::_('VAPEDITSERVICE23'); ?>
				</span>
			</div>	

			<?php 
			$kk = 0;
			foreach ($payments as $i => $p)
			{ 
				$kk = ($kk + 1) % 2;
				?>

				<div class="vap-allorders-singlerow vap-allorders-row<?php echo $kk; ?>" style="text-align: center;">
					<span class="vap-allorders-column" style="text-align:left; width: 5%;">
						<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $p['id']; ?>" onClick="EmployeeArea.isChecked(this.checked);" />
					</span>
					<span class="vap-allorders-column" style="width: 25%;">
						<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditpay&cid[]='.$p['id'] . '&Itemid=' . $itemid); ?>">
							<?php echo $p['name']; ?>
						</a>
					</span>
					<span class="vap-allorders-column" style="width: 20%;">
						<?php echo $p['file']; ?>
					</span>
					<span class="vap-allorders-column" style="width: 16%;">
						<?php echo VikAppointments::printPriceCurrencySymb($p['charge']); ?>
					</span>
					<span class="vap-allorders-column" style="width: 16%;">
						<?php if ($auth->managePayments()) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditpay.publish&cid[]='.$p['id'].'&status='.($p['published'] ? 0 : 1) . '&Itemid=' . $itemid); ?>">
								<?php echo intval($p['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
							</a>
						<?php } else { ?>
							<?php echo intval($p['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
						<?php } ?>
					</span>
					<span class="vap-allorders-column" style="width: 15%;">
						<?php if ($p['ordering'] > $this->bounds->min) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditpay.move&cid[]='.$p['id'].'&mode=up' . '&Itemid=' . $itemid); ?>">
								<i class="fa fa-chevron-up big"></i>
							</a>
						<?php } else { ?>
							<i class="fa fa-chevron-up big" style="color: rgba(255, 255, 255, 0);"></i>
						<?php } ?>

						<?php if ($p['ordering'] < $this->bounds->max) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditpay.move&cid[]='.$p['id'].'&mode=down' . '&Itemid=' . $itemid); ?>">
								<i class="fa fa-chevron-down big"></i>
							</a>
						<?php } else { ?>
							<i class="fa fa-chevron-down big" style="color: rgba(255, 255, 255, 0);"></i>
						<?php } ?>
					</span>
				</div>
				
			<?php } ?>

		</div>

	<?php } else { ?>

		<div class="vap-allorders-void long"><?php echo JText::_('VAPEMPNOPAYMENTS'); ?></div>

	<?php } ?>

	<?php echo JHtml::_('form.token'); ?>
	<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="emppaylist" />
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />

</form>

<?php
JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
JText::script('VAPCONFDIALOGMSG');
?>

<script>
	
	function vapClosePaymentsList() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false); ?>';
	}
	
	<?php if ($auth->managePayments()) { ?>

		function vapCreatePayment() {
			document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditpay&Itemid=' . $itemid, false); ?>';
		}

		function vapRemovePayments() {
			if (!EmployeeArea.hasChecked()) {
				alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
				return;
			}

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			Joomla.submitform('empeditpay.delete', document.empareaForm);
		}

	<?php } ?>
	
</script>
