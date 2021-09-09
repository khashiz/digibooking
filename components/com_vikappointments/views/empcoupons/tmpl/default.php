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

$coupons = $this->coupons;

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');

$itemid = JFactory::getApplication()->input->getInt('Itemid');

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
		<h2><?php echo JText::sprintf('VAPEMPCOUPONSPAGETITLE', $employee['firstname'] . ' ' . $employee['lastname']); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manageCoupons()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapCreateCoupon();" id="vapempbtnnew" class="vap-btn blue employee"><?php echo JText::_('VAPNEW'); ?></button>
			</div>

			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveCoupons();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseCoupons();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments'); ?>" method="post" name="empareaForm" id="empareaForm" style="min-height: 250px;">

	<?php if (count($coupons)) { ?>

		<div class="vap-emploc-container">

			<div class="vap-allorders-singlerow vap-allorders-row1 head" style="text-align: center;">
				<span class="vap-allorders-column" style="text-align:left; width: 5%;">
					<input type="checkbox" onclick="EmployeeArea.checkAll(this)" value="" class="checkall-toggle" />
				</span>
				<span class="vap-allorders-column" style="width: 20%;">
					<?php echo JText::_('VAPEMPMANAGECOUPON1'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 15%;">
					<?php echo JText::_('VAPEMPMANAGECOUPON2'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 10%;">
					<?php echo JText::_('VAPEMPMANAGECOUPON4'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 22%;">
					<?php echo JText::_('VAPEMPMANAGECOUPON18'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 10%;">
					<?php echo JText::_('VAPEMPMANAGECOUPON15'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 15%;">
					<?php echo JText::_('VAPEMPMANAGECOUPON8'); ?>
				</span>
			</div>
			
			<?php 
			$kk = 0;
			foreach ($coupons as $i => $c)
			{ 
				$now   = time();
				$valid = 0;
				$class = 'removed';

				if ($c['type'] == 1 || $c['max_quantity'] - $c['used_quantity'] > 0)
				{
					if ($c['dstart'] == -1 || ($c['dstart'] <= $now && $now <= $c['dend']) || ($c['pubmode'] == 2 && $now <= $c['dend']))
					{
						$valid = 1;
						$class = 'confirmed';
					}
					else if ($c['dstart'] > $now)
					{
						$valid = 2;
						$class = 'pending';
					}
				}

				$tooltip = JText::sprintf('VAPEMPCOUPONINFOTIP', 
					$c['used_quantity'], 
					($c['type'] == 2 ? max(array(0, $c['max_quantity']-$c['used_quantity'])) : "&infin;"), 
					(strlen($c['notes']) ? '<br /><br />' : '') . $c['notes']
				);

				$kk = ($kk + 1) % 2;
				?>
				<div class="vap-allorders-singlerow vap-allorders-row<?php echo $kk; ?>" style="text-align: center;">
					<span class="vap-allorders-column" style="text-align:left; width: 5%;">
						<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $c['id']; ?>" onClick="EmployeeArea.isChecked(this.checked);" />
					</span>
					<span class="vap-allorders-column" style="width: 20%;">
						<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditcoupon&cid[]=' . $c['id'] . '&Itemid=' . $itemid); ?>">
							<?php echo $c['code']; ?>
						</a>
					</span>
					<span class="vap-allorders-column" style="width: 15%;">
						<?php echo JText::_('VAPEMPCOUPONTYPE'.$c['type']); ?>
					</span>
					<span class="vap-allorders-column" style="width: 10%;">
						<?php echo ($c['percentot'] == 1 ? $c['value'].' %' : VikAppointments::printPriceCurrencySymb($c['value'])); ?>
					</span>
					<span class="vap-allorders-column" style="width: 22%;">
						<?php echo ($c['dstart'] == -1 ? JText::_('VAPEMPMANAGECOUPON11') : ArasJoomlaVikApp::jdate($date_format, $c['dstart']) . " - " . ArasJoomlaVikApp::jdate($date_format, $c['dend'])); ?>
					</span>
					<span class="vap-allorders-column" style="width: 10%;">
						<i class="fa fa-ticket big vap-notes-tip" title="<?php echo $tooltip; ?>"></i>
					</span>
					<span class="vap-allorders-column vap-allorders-status<?php echo strtolower($class); ?>" style="width: 15%;">
						<?php echo strtoupper(JText::_('VAPEMPCOUPONVALID' . ($valid))); ?>
					</span>
				</div>
			<?php } ?>
			
		</div>

	<?php } else { ?>

		<div class="vap-allorders-void long"><?php echo JText::_('VAPNOCOUPON'); ?></div>

	<?php } ?>

	<?php echo JHtml::_('form.token'); ?>
	<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="empsubscrorder" />
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />

</form>

<?php
JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
JText::script('VAPCONFDIALOGMSG');
?>

<script>
	
	jQuery(document).ready(function() {
		jQuery('.vap-notes-tip').tooltip();
	});

	function vapCloseCoupons() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false); ?>';
	}
	
	<?php if ($auth->manageCoupons()) { ?>

		function vapCreateCoupon() {
			document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditcoupon&Itemid=' . $itemid, false); ?>';
		}

		function vapRemoveCoupons() {
			if (!EmployeeArea.hasChecked()) {
				alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
				return;
			}

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			Joomla.submitform('empeditcoupon.delete', document.empareaForm);
		}

	<?php } ?>
	
</script>
