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

$services = $this->services;

$canSort = array_filter($services, function($elem) use ($auth)
{
	return $elem['createdby'] == $auth->jid;
});

$itemid = JFactory::getApplication()->input->getInt('Itemid');

$vik = UIApplication::getInstance();

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
		<h2><?php echo JText::sprintf('VAPEMPSERLISTTITLE', $employee['firstname'] . ' ' . $employee['lastname']); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->create()) { ?>
			<div class="vapempbtn">
				<button type="button" id="vapempbtnnew" class="vap-btn blue employee"><?php echo JText::_('VAPNEW'); ?></button>
			</div>
		<?php } else if ($auth->attachServices()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapAttachService();" id="vapempbtnnew" class="vap-btn blue employee"><?php echo JText::_('VAPNEW'); ?></button>
			</div>
		<?php } ?>

		<?php if ($auth->remove()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveServices();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseServicesList();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments'); ?>" method="post" name="empareaForm" id="empareaForm" style="min-height: 250px;">

	<?php if (count($services)) { ?>

		<div class="vapempserlistcont">

			<div class="vap-allorders-singlerow vap-allorders-row1 head" style="text-align: center;">
				<span class="vap-allorders-column" style="text-align:left; width: 5%;">
					<input type="checkbox" onclick="EmployeeArea.checkAll(this)" value="" class="checkall-toggle" />
				</span>
				<span class="vap-allorders-column" style="width: <?php echo $canSort ? 20 : 30; ?>%;">
					<?php echo JText::_('VAPEDITSERVICE2'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 12%;">
					<?php echo JText::_('VAPEDITSERVICE5'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 12%;">
					<?php echo JText::_('VAPEDITSERVICE4'); ?>
				</span>
				<span class="vap-allorders-column" style="width: <?php echo $canSort ? 18 : 23; ?>%;">
					<?php echo JText::_('VAPEDITSERVICE8'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 15%;">
					<?php echo JText::_('VAPEMPWORKDAYSTITLE'); ?>
				</span>
				<?php if ($canSort) { ?>
					<span class="vap-allorders-column" style="width: 15%;">
						<?php echo JText::_('VAPEDITSERVICE23'); ?>
					</span>
				<?php } ?>
			</div>

			<?php 
			$kk = 0;
			foreach ($services as $i => $s)
			{
				$kk = ($kk + 1) % 2;
				?>
				<div class="vap-allorders-singlerow vap-allorders-row<?php echo $kk; ?>" style="text-align: center;">
					<span class="vap-allorders-column" style="text-align:left; width: 5%;">
						<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $s['id']; ?>" onClick="EmployeeArea.isChecked(this.checked);" />
					</span>
					<span class="vap-allorders-column" style="width: <?php echo $canSort ? 20 : 30; ?>%;">
						<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditservice&cid[]=' . $s['id'] . '&Itemid=' . $itemid); ?>">
							<?php echo $s['name']; ?>
						</a>
					</span>
					<span class="vap-allorders-column" style="width: 12%;">
						<?php echo VikAppointments::printPriceCurrencySymb($s['rate']); ?>
					</span>
					<span class="vap-allorders-column" style="width: 12%;">
						<?php echo VikAppointments::formatMinutesToTime($s['override_duration']); ?>
					</span>
					<span class="vap-allorders-column" style="width: <?php echo $canSort ? 18 : 23; ?>%;">
						<?php echo (empty($s['group_name']) ? '/' : $s['group_name']); ?>
					</span>
					<span class="vap-allorders-column" style="width: 15%;">
						<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditserwdays&id=' . $s['id'] . '&Itemid=' . $itemid); ?>">
							<i class="fa fa-calendar big" title="<?php echo JText::sprintf('VAPEMPSERWDTITLE', $s['name']); ?>"></i>
						</a>
					</span>
					<?php if ($canSort && $s['createdby'] == $auth->jid) { ?>
						<span class="vap-allorders-column" style="width: 15%;">
							<?php if ($s['ordering'] > $this->bounds->min) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditservice.move&cid[]=' . $s['id'] . '&mode=up' . '&Itemid=' . $itemid); ?>">
									<i class="fa fa-chevron-up big"></i>
								</a>
							<?php } else { ?>
								<i class="fa fa-chevron-up big" style="color: rgba(255, 255, 255, 0);"></i>
							<?php } ?>

							<?php if ($s['ordering'] < $this->bounds->max) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditservice.move&cid[]=' . $s['id'] . '&mode=down' . '&Itemid=' . $itemid); ?>">
									<i class="fa fa-chevron-down big"></i>
								</a>
							<?php } else { ?>
								<i class="fa fa-chevron-down big" style="color: rgba(255, 255, 255, 0);"></i>
							<?php } ?>
						</span>
					<?php } ?>
				</div>
				
			<?php } ?>
			
		</div>

	<?php } else { ?>

		<div class="vap-allorders-void long"><?php echo JText::_('VAPNOSERVICE'); ?></div>

	<?php } ?>

	<?php echo JHtml::_('form.token'); ?>
	<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="empserviceslist" />
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />

</form>

<?php if ($auth->create()) { ?>

	<!-- TMPL for new services choice -->

	<div class="popover fade bottom in" style="display: none;" id="vap-choice-popover">
		<div class="arrow"></div>
		<div class="popover-content">
			<div id="vap-new-choice-box">

				<div class="vap-new-choice-action">
					<a href="javascript: void(0)" onclick="vapCreateService();">Create New</a>
				</div>

				<div class="vap-new-choice-action">
					<a href="javascript: void(0)" onclick="vapAttachService();">Assign Existing</a>
				</div>

			</div>
		</div>
	</div>

<?php } ?>

<?php
JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
JText::script('VAPCONFDIALOGMSG');
JText::script('VAPNEW');
?>

<script>
	
	function vapCloseServicesList() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false); ?>';
	}

	<?php if ($auth->create()) { ?>

		jQuery(document).ready(function() {

			jQuery('#vapempbtnnew').on('click', function() {

				var popover = jQuery('#vap-choice-popover');

				if (!popover.is(':visible')) {

					var left = jQuery(this).position().left + jQuery(this).outerWidth() / 2 - popover.width() / 2;
					var top  = jQuery(this).position().top + jQuery(this).outerHeight();

					popover.css('left', left + 'px');
					popover.css('top', top + 'px');

				}

				popover.toggle();

			});

		});

		function vapCreateService() {
			document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditservice&Itemid=' . $itemid, false); ?>';
		}

	<?php } ?>

	<?php if ($auth->attachServices()) { ?>

		function vapAttachService() {
			document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empattachser&Itemid=' . $itemid, false); ?>';
		}

	<?php } ?>

	<?php if ($auth->remove()) { ?>

		function vapRemoveServices() {
			if (!EmployeeArea.hasChecked()) {
				alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
				return;
			}

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			Joomla.submitform('empeditservice.delete', document.empareaForm);
		}

	<?php } ?>
	
</script>
