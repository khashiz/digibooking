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

$auth 	 = isset($displayData['auth']) 			? $displayData['auth'] 	 		: EmployeeAuth::getInstance();
$has_cap = isset($displayData['has_capacity'])  ? $displayData['has_capacity']  : 0;
$emp_tz  = isset($displayData['timezone']) 		? $displayData['timezone'] 		: null;
$orders  = isset($displayData['orders']) 		? $displayData['orders'] 		: array();
$itemid  = isset($displayData['itemid'])		? $displayData['itemid'] 		: null;

$config = UIFactory::getConfig();

?>

<div class="vapempserlistcont">

	<div class="vap-allorders-singlerow vap-allorders-row1 head" style="text-align: center;">
		
		<span class="vap-allorders-column" style="width: 5%;"><?php echo JText::_('VAPMANAGERESERVATION0'); ?></span>
		<span class="vap-allorders-column" style="width: 10%;"><?php echo JText::_('VAPMANAGERESERVATION5'); ?></span>
		<span class="vap-allorders-column" style="width: 10%;"><?php echo JText::_('VAPMANAGERESERVATION6'); ?></span>
		<span class="vap-allorders-column" style="width: 15%;"><?php echo JText::_('VAPMANAGERESERVATION4'); ?></span>

		<?php
		if ($has_cap)
		{
			// the employee may be interested in knowing the number of guests
			?>
			<span class="vap-allorders-column" style="width: 7%;"><?php echo JText::_('VAPMANAGERESERVATION25'); ?></span>
			<?php
		}
		?>

		<span class="vap-allorders-column" style="width: 20%;"><?php echo JText::_('VAPMANAGERESERVATION30'); ?></span>

		<?php
		if (!$has_cap)
		{
			// no capacity, display a different info (such as the total cost)
			?>
			<span class="vap-allorders-column" style="width: 7%;"><?php echo JText::_('VAPMANAGERESERVATION9'); ?></span>
			<?php
		}
		?>

		<span class="vap-allorders-column" style="width: 20%;"><?php echo JText::_('VAPMANAGERESERVATION7'); ?></span>
		<span class="vap-allorders-column" style="width: 5%;"><?php echo JText::_('VAPMANAGERESERVATION22'); ?></span>

		<?php
		if ($auth->resremove())
		{
			?>
			<span class="vap-allorders-column" style="width: 5%;"><?php echo JText::_('VAPMANAGERESERVATION23'); ?></span>
			<?php
		}
		?>

	</div>

	<?php
	foreach ($orders as $i => $row)
	{
		// set employee timezone
		VikAppointments::setCurrentTimezone($emp_tz);

		$tot_paid = '';
		if ($row['tot_paid'] > 0)
		{
			$tot_paid = ' (' . VikAppointments::printPriceCurrencySymb($row['tot_paid']) . ')';
		}

		$edit_uri = JRoute::_("index.php?option=com_vikappointments&view=empmanres&cid[]={$row['rid']}&Itemid={$itemid}", false);
		$del_uri  = JRoute::_("index.php?option=com_vikappointments&task=empmanres.delete&cid[]={$row['rid']}&Itemid={$itemid}", false);

		?>
		<div class="vap-allorders-singlerow vap-allorders-row<?php echo $i % 2 ? 0 : 1; ?> vapemprestr" id="vaptabrow<?php echo $row['rid']; ?>" style="text-align: center;">

			<span class="vap-allorders-column order-id" style="width: 5%;">
				<?php echo $row['rid']; ?>
			</span>

			<span class="vap-allorders-column order-checkin" style="width: 10%;">
				<?php echo ArasJoomlaVikApp::jdate($config->get('timeformat'), $row['checkin']); ?>
			</span>

			<span class="vap-allorders-column order-checkout" style="width: 10%;">
				<?php echo ArasJoomlaVikApp::jdate($config->get('timeformat'), VikAppointments::getCheckout($row['checkin'], $row['rduration'])); ?>
			</span>

			<span class="vap-allorders-column order-service" style="width: 15%;">
				<?php echo $row['sname']; ?>
			</span>

			<?php
			if ($has_cap)
			{
				// the employee may be interested in knowing the number of guests
				?>
				<span class="vap-allorders-column order-people" style="width: 7%;">
					<?php echo $row['people']; ?>
				</span>
				<?php
			}
			?>

			<span class="vap-allorders-column order-customer" style="width: 20%;">
				<?php echo $row['purchaser_nominative']; ?>
			</span>

			<?php
			if (!$has_cap)
			{
				// no capacity, display a different info (such as the total cost)
				?>
				<span class="vap-allorders-column order-total" style="width: 7%;">
					<?php echo VikAppointments::printPriceCurrencySymb($row['total_cost']) . $tot_paid; ?>
				</span>
				<?php
			}
			?>

			<span class="vap-allorders-column vap-allorders-status<?php echo strtolower($row['status']); ?>" style="width: 20%;">
				<?php echo strtoupper(JText::_('VAPSTATUS' . $row['status'])); ?>
			</span>

			<span class="vap-allorders-column order-edit" style="width: 5%;">
				<a href="<?php echo $edit_uri; ?>"><i class="fa fa-edit big"></i></a>
			</span>

			<?php
			if ($auth->resremove())
			{
				?>
				<span class="vap-allorders-column order-delete" style="width: 5%;">
					<a href="<?php echo $del_uri; ?>" onclick="return confirm('<?php echo addslashes(JText::_('VAPCONFDIALOGMSG')); ?>');">
						<i class="fa fa-trash big"></i>
					</a>
				</span>
				<?php
			}
			?>

		</div>
		<?php
	}		
	?>

</div>
