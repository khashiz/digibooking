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

$custfields = $this->customFields;

$canSort = $auth->manageCustomFields();

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
		<h2><?php echo JText::sprintf('VAPEMPCUSTOMFPAGETITLE', $employee['firstname'] . ' ' . $employee['lastname']); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manageCustomFields()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapCreateCustomField();" id="vapempbtnnew" class="vap-btn blue employee"><?php echo JText::_('VAPNEW'); ?></button>
			</div>

			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveCustomFields();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseCustomField();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments'); ?>" method="post" name="empareaForm" id="empareaForm" style="min-height: 250px;">

	<?php if (count($custfields)) { ?>

		<div class="vap-emploc-container">

			<div class="vap-allorders-singlerow vap-allorders-row1 head" style="text-align: center;">
				<span class="vap-allorders-column" style="text-align:left; width: 5%;">
					<input type="checkbox" onclick="EmployeeArea.checkAll(this)" value="" class="checkall-toggle" />
				</span>
				<span class="vap-allorders-column" style="width: <?php echo $canSort ? 25 : 35; ?>%;">
					<?php echo JText::_('VAPEMPMANAGECUSTOMF1'); ?>
				</span>
				<span class="vap-allorders-column" style="width: <?php echo $canSort ? 20 : 25; ?>%;">
					<?php echo JText::_('VAPEMPMANAGECUSTOMF2'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 12%;">
					<?php echo JText::_('VAPEMPMANAGECUSTOMF3'); ?>
				</span>
				<span class="vap-allorders-column" style="width: 20%;">
					<?php echo JText::_('VAPEMPMANAGECUSTOMF10'); ?>
				</span>
				<?php if ($canSort) { ?>
					<span class="vap-allorders-column" style="width: 15%;">
						<?php echo JText::_('VAPEMPMANAGECUSTOMF6'); ?>
					</span>
				<?php } ?>
			</div>
			
			<?php 
			$kk = 0;
			foreach ($custfields as $i => $c)
			{
				$type_label = "";

				switch ($c['type'])
				{
					case 'text':        $type_label = JText::_('VAPEMPCUSTOMFTYPEOPT1'); break;
					case 'textarea':    $type_label = JText::_('VAPEMPCUSTOMFTYPEOPT2'); break;
					case 'date':        $type_label = JText::_('VAPEMPCUSTOMFTYPEOPT3'); break;
					case 'select':      $type_label = JText::_('VAPEMPCUSTOMFTYPEOPT4'); break;
					case 'checkbox':    $type_label = JText::_('VAPEMPCUSTOMFTYPEOPT5'); break;
					case 'separator':   $type_label = JText::_('VAPEMPCUSTOMFTYPEOPT6'); break;
					case 'file':        $type_label = JText::_('VAPEMPCUSTOMFTYPEOPT7'); break;
				}

				$kk = ($kk + 1) % 2;
				?>
				<div class="vap-allorders-singlerow vap-allorders-row<?php echo $kk; ?>" style="text-align: center;">
					<span class="vap-allorders-column" style="text-align:left; width: 5%;">
						<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $c['id']; ?>" onClick="EmployeeArea.isChecked(this.checked);" />
					</span>
					<span class="vap-allorders-column" style="width: <?php echo $canSort ? 25 : 35; ?>%;">
						<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditcustfield&cid[]='.$c['id'] . '&Itemid=' . $itemid); ?>">
							<?php echo JText::_($c['name']); ?>
						</a>
					</span>
					<span class="vap-allorders-column" style="width: <?php echo $canSort ? 20 : 25; ?>%;">
						<?php echo $type_label; ?>
					</span>
					<span class="vap-allorders-column" style="width: 12%;">
						<?php if ($auth->manageCustomFields()) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditcustfield.publish&cid[]='.$c['id'].'&status='.($c['required'] ? 0 : 1) . '&Itemid=' . $itemid); ?>">
								<?php echo intval($c['required']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
							</a>
						<?php } else { ?>
							<?php echo intval($c['required']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
						<?php } ?>
					</span>
					<span class="vap-allorders-column" style="width: 20%;">
						<?php 
						$clazz = '';
						switch ($c['rule'])
						{
							case VAPCustomFields::NOMINATIVE:
								$clazz = 'male';
								break;
							
							case VAPCustomFields::EMAIL:
								$clazz = 'envelope';
								break;

							case VAPCustomFields::PHONE_NUMBER:
								$clazz = 'phone';
								break;

							case VAPCustomFields::STATE:
								$clazz = 'map';
								break;

							case VAPCustomFields::CITY:
								$clazz = 'map-signs';
								break;

							case VAPCustomFields::ADDRESS:
								$clazz = 'road';
								break;

							case VAPCustomFields::ZIP:
								$clazz = 'map-marker';
								break;

							case VAPCustomFields::COMPANY:
								$clazz = 'building';
								break;

							case VAPCustomFields::VATNUM:
								$clazz = 'briefcase';
								break;
						}

						if (!empty($clazz))
						{
							?>
							<i class="fa fa-<?php echo $clazz; ?> big rule-tooltip" title="<?php echo JText::_('VAPEMPCUSTFIELDRULE'.$c['rule']); ?>"></i>
							<?php
						}
						?>
					</span>
					 <?php if ($canSort) { ?>
						<span class="vap-allorders-column" style="width: 15%;">
							<?php if ($c['ordering'] > $this->bounds->min) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditcustfield.move&cid[]='.$c['id'].'&mode=up' . '&Itemid=' . $itemid); ?>">
									<i class="fa fa-chevron-up big"></i>
								</a>
							<?php } else { ?>
								<i class="fa fa-chevron-up big" style="color: rgba(255, 255, 255, 0);"></i>
							<?php } ?>

							<?php if ($c['ordering'] < $this->bounds->max) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&task=empeditcustfield.move&cid[]='.$c['id'].'&mode=down' . '&Itemid=' . $itemid); ?>">
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

		<div class="vap-allorders-void long"><?php echo JText::_('VAPNOCUSTOMF'); ?></div>

	<?php } ?>

	<?php echo JHtml::_('form.token'); ?>
	<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="empcustfields" />
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />

</form>

<?php
JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
JText::script('VAPCONFDIALOGMSG');
?>

<script>

	jQuery(document).ready(function() {

		jQuery('i.rule-tooltip').tooltip();

	});

	function vapCloseCustomField() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false); ?>';
	}
	
	<?php if ($auth->manageCustomFields()) { ?>

		function vapCreateCustomField() {
			document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditcustfield&Itemid=' . $itemid, false); ?>';
		}

		function vapRemoveCustomFields() {
			if (!EmployeeArea.hasChecked()) {
				alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
				return;
			}

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			Joomla.submitform('empeditcustfield.delete', document.empareaForm);
		}

	<?php } ?>
	
</script>
