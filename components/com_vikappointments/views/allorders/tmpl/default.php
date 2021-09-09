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

if ($this->user->guest)
{
	exit ('No direct access');
}

?>

<div class="vap-allorders-userhead">
	<div class="vap-allorders-userleft">
		<h2><?php echo JText::sprintf('VAPALLORDERSTITLE', $this->user->name); ?></h2>
	</div>

	<div class="vap-allorders-userright">
		<?php if ($this->hasPackages) { ?>
			<button type="button" class="vap-btn blue" onClick="document.location.href='<?php echo JRoute::_('index.php?option=com_vikappointments&view=packorders'); ?>';">
				<?php echo JText::_('VAPALLORDERSPACKBUTTON'); ?>
			</button>
		<?php } ?>

		<button type="button" class="vap-btn blue" onClick="document.location.href='<?php echo JRoute::_('index.php?option=com_vikappointments&view=userprofile'); ?>';">
			<?php echo JText::_('VAPALLORDERSPROFILEBUTTON'); ?>
		</button>

		<button type="button" class="vap-btn" onClick="document.location.href='<?php echo JRoute::_('index.php?option=com_vikappointments&task=userlogout'); ?>';">
			<?php echo JText::_('VAPLOGOUTTITLE'); ?>
		</button>
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
			$row_text = '';
			$date = '';
			
			if ($ord['id_parent'] != -1)
			{
				$ord['sername'] = VikAppointments::getTranslation($ord['serid'], $ord, $this->langServices, 'sername', 'name');
				$ord['empname'] = VikAppointments::getTranslation($ord['empid'], $ord, $this->langEmployees, 'empname', 'nickname');
				
				$row_text = $ord['sername'];
				if ($ord['view_emp'])
				{
					$row_text .= ", " . $ord['empname'];
				}
				
				VikAppointments::setCurrentTimezone($ord['timezone']);

				$date = ArasJoomlaVikApp::jdate($dt_format, $ord['checkin_ts']);
			}
			else if ($ord['createdon'] != -1)
			{
				$date = '<strong>' . ArasJoomlaVikApp::jdate($dt_format, $ord['createdon']) . '</strong>';
			}
			
			$child_class = "";
			
			if (empty($ord['child']))
			{
				$i = ($i + 1) % 2;
			}
			else
			{
				$child_class = "vap-allord-child" . $ord['id_parent'];
			}
			
			?>
			<div class="vap-allorders-singlerow vap-allorders-row<?php echo $i . ' ' . $child_class; ?>" style="<?php echo empty($ord['child']) ? '' : 'display:none;'; ?>">
				<span class="vap-allorders-column" style="width: 25%;">
					<?php if (empty($ord['child'])) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=order&ordnum=' . $ord['id'].'&ordkey=' . $ord['sid']); ?>">
							<?php echo $ord['id'] . "-" . $ord['sid']; ?>
						</a>
					<?php } ?>
				</span>

				<span class="vap-allorders-column" style="width: 20%;">
					<?php echo $date; ?>
				</span>

				<span class="vap-allorders-column" style="width: 27%;">
					<?php
					if ($ord['id_parent'] != -1)
					{
						echo $row_text;
					}
					else
					{
						?>
						<a href="javascript: void(0);" onClick="vapDisplayOrderChildren(<?php echo $ord['id']; ?>);">
							<strong><?php echo JText::_('VAPALLORDERSMULTIPLE'); ?></strong>
						</a>
						<?php
					}
					?>
				</span>

				<span class="vap-allorders-column vap-allorders-status<?php echo strtolower($ord['status']); ?>" style="width: 15%;">
					<?php echo strtoupper(JText::_('VAPSTATUS' . $ord['status'])); ?>
				</span>

				<span class="vap-allorders-column" style="width: 10%;">
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
	
	<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=allorders' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post">
		<?php echo JHtml::_('form.token'); ?>
		<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
		<input type="hidden" name="option" value="com_vikappointments" />
		<input type="hidden" name="view" value="allorders" />
	</form>
	
	<script>
		
		function vapDisplayOrderChildren(id) {
			if (jQuery('.vap-allord-child'+id).first().is(':visible')) {
				jQuery('.vap-allord-child'+id).slideUp();
			} else {
				jQuery('.vap-allord-child'+id).slideDown();
			}
		}
		
	</script>
	
<?php } ?>
