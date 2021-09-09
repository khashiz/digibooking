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

?>
	
<div class="vap-allorders-userhead">
	<div class="vap-allorders-userleft">
		<h2><?php echo JText::_('VAPALLORDERSPACKBUTTON'); ?></h2>
	</div>

	<div class="vap-allorders-userright">
		<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" class="vap-btn blue">
			<?php echo JText::_('VAPALLORDERSBUTTON'); ?>
		</a>
	</div>
</div>
	
<?php if (!count($this->orders)) { ?>

	<div class="vap-allorders-void"><?php echo JText::_('VAPALLORDERSVOID'); ?></div>

<?php } else {

	$config = UIFactory::getConfig();
	
	$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');
	
	?>
	
	<div class="vap-allorders-list">
		<?php 
		foreach ($this->orders as $i => $ord)
		{
			$i = ($i + 1) % 2;

			$order_uri = "index.php?option=com_vikappointments&view=packagesorder&ordnum={$ord['id']}&ordkey={$ord['sid']}";

			if ($this->itemid)
			{
				$order_uri .= "&Itemid={$this->itemid}";
			}

			?>
			<div class="vap-allorders-singlerow vap-allorders-row<?php echo $i; ?>">

				<span class="vap-allorders-column" style="width: 35%;">
					<a href="<?php echo JRoute::_($order_uri); ?>">
						<?php echo $ord['id'] . "-" . $ord['sid']; ?>
					</a>
				</span>

				<span class="vap-allorders-column" style="width: 24%;">
					<?php echo VikAppointments::formatTimestamp($dt_format, $ord['createdon']); ?>
				</span>

				<span class="vap-allorders-column vap-allorders-status<?php echo strtolower($ord['status']); ?>" style="width: 24%;">
					<?php echo strtoupper(JText::_('VAPSTATUS' . $ord['status'])); ?>
				</span>

				<span class="vap-allorders-column" style="width: 15%;">
					<?php
					if ($ord['total_cost'] > 0)
					{
						echo VikAppointments::printPriceCurrencySymb($ord['total_cost']);
					}
					?>
				</span>

			</div>
		<?php } ?>
	</div>
	
	<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=packorders' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post">
		<?php echo JHtml::_('form.token'); ?>
		<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
		<input type="hidden" name="option" value="com_vikappointments" />
		<input type="hidden" name="view" value="packorders" />
	</form>
	
<?php } ?>
