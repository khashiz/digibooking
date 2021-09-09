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

$allOrders = $this->allOrders;

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
		<h2><?php echo JText::sprintf('VAPEMPSUBSCRLISTTITLE', $employee['firstname'] . ' ' . $employee['lastname']); ?></h2>
	</div>
</div>

<form action="<?php echo JRoute::_("index.php?option=com_vikappointments&view=empsubscrorder&Itemid=$itemid"); ?>" method="post" name="empareaForm" id="empareaForm" style="min-height: 250px;">

	<?php if (count($allOrders)) { ?>

		<div class="vap-ordersubscr-cont">
		
			<div class="vap-allorders-list">

				<div class="vap-allorders-singlerow vap-allorders-row1 head" style="text-align: center;">
					<span class="vap-allorders-column" style="width: 25%;">
						<?php echo JText::_( 'VAPORDERNUM' ); ?>
					</span>
					<span class="vap-allorders-column" style="width: 20%;">
						<?php echo JText::_( 'VAPINVDATE' ); ?>
					</span>
					<span class="vap-allorders-column" style="width: 27%;">
						<?php echo JText::_( 'VAPSUBSCRIPTION' ); ?>
					</span>
					<span class="vap-allorders-column" style="width: 15%;">
						<?php echo JText::_( 'VAPORDERSTATUS' ); ?>
					</span>
					<span class="vap-allorders-column" style="width: 10%;">
						<?php echo JText::_( 'VAPORDERDEPOSIT' ); ?>
					</span>
				</div>

				<?php 
				$kk = 0;
				foreach ($allOrders as $ord)
				{
					$kk = ($kk + 1) % 2; 
					?>

					<div class="vap-allorders-singlerow vap-allorders-row<?php echo $kk; ?>" style="text-align: center;">
						<span class="vap-allorders-column" style="width: 25%;">
							<a href="<?php echo JRoute::_("index.php?option=com_vikappointments&view=empsubscrorder&id={$ord['id']}&Itemid=$itemid"); ?>">
								<?php echo $ord['id'] . '-' . $ord['sid']; ?>
							</a>
						</span>
						<span class="vap-allorders-column" style="width: 20%;">
							<?php echo date($date_format . ' ' . $time_format, $ord['createdon']); ?>
						</span>
						<span class="vap-allorders-column" style="width: 27%;">
							<?php echo $ord['sub_name']; ?>
						</span>
						<span class="vap-allorders-column vap-allorders-status<?php echo strtolower($ord['status']); ?>" style="width: 15%;">
							<?php echo strtoupper(JText::_('VAPSTATUS' . ($ord['status']))); ?>
						</span>
						<span class="vap-allorders-column" style="width: 10%;">
							<?php if ($ord['total_cost'] > 0)
							{
								echo VikAppointments::printPriceCurrencySymb($ord['total_cost']);
							}
							?>
						</span>
					</div>

				<?php } ?>
			</div>

		</div>
		
	<?php } else { ?>

		<div class="vap-allorders-void long"><?php echo JText::_('VAPALLORDERSVOID'); ?></div>

	<?php } ?>

	<?php echo JHtml::_('form.token'); ?>
	<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="view" value="empsubscrorder" />
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>
