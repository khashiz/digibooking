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

$rows = $this->rows;

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
echo JLayoutHelper::render('emparea.toolbar', array('active' => false));
?>

<div class="vapeditempheaderdiv">
	<div class="vapeditemptitlediv">
		<h2><?php echo JText::sprintf('VAPEMPSERWDTITLE', $this->ser_name); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manageWorkDays()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapRemoveSerWorkingDays();" id="vapempbtnremove" class="vap-btn blue employee"><?php echo JText::_('VAPDELETE'); ?></button>
			</div>
			<div class="vapempbtn">
				<button type="button" onClick="vapRestoreSerWorkingDays();" id="vapempbtnrestore" class="vap-btn blue employee"><?php echo JText::_('VAPRESTORE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseSerWorkingDays();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>	

<form action="index.php?option=com_vikappointments" method="post" name="empareaForm" id="empareaForm">
	
<?php if (!count($rows)) { ?>

	<p><?php echo JText::_('VAPNOWORKINGDAYS'); ?></p>

<?php } else { ?>

	<div class="vap-allorders-singlerow vap-allorders-row1 head" style="text-align: center;">
        <span class="vap-allorders-column" style="text-align:left; width: 5%;">
            <input type="checkbox" onclick="EmployeeArea.checkAll(this)" value="" class="checkall-toggle" />
        </span>
        <span class="vap-allorders-column" style="width: 30%;">
            <?php echo JText::_( 'VAPEDITWD1' ); ?>
        </span>
        <span class="vap-allorders-column" style="width: 20%;">
            <?php echo JText::_( 'VAPEDITWD2' ); ?>
        </span>
        <span class="vap-allorders-column" style="width: 20%;">
            <?php echo JText::_( 'VAPEDITWD3' ); ?>
        </span>
        <span class="vap-allorders-column" style="width: 20%;">
            <?php echo JText::_( 'VAPEDITWD4' ); ?>
        </span>
    </div>

	<?php 
	$kk = 0;
	foreach ($rows as $w)
	{
		if ($w['ts'] <= -1)
		{
			$day = JText::_('VAPDAY'.($w['day'] > 0 ? $w['day'] : 7));
		}
		else
		{
			/**
			 * The dates are now displayed using UTC timezone.
			 *
			 * @since 1.6.2
			 */
			// $day = JHtml::_('date', $w['ts'], $date_format);
			$day = JDate::getInstance($w['ts'])->format($date_format);

			if ($w['day'] == -1)
			{
				$w['day'] = date('w', $w['ts']);
			}

			// $day .= ' (' . JHtml::_('date', $w['ts'], 'D') . ')';
			$day .= ' (' . JDate::getInstance($w['ts'])->format('D') . ')';
		}
		
		$from = date($time_format, mktime($w['fromts'] / 60, $w['fromts'] % 60, 0, 1, 1, 2000));
		$to   = date($time_format, mktime($w['endts'] / 60, $w['endts'] % 60, 0, 1, 1, 2000));
		
		?>

		<div class="vap-allorders-singlerow vap-allorders-row<?php echo $kk; ?>" style="text-align: center;">
			<span class="vap-allorders-column" style="text-align:left; width: 5%;">
				<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $w['id']; ?>" onClick="EmployeeArea.isChecked(this.checked);" />
			</span>
			<span class="vap-allorders-column" style="width: 30%;">
				<a href="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditwdays&cid[]=' . $w['parent'] . '&Itemid=' . $itemid); ?>">
					<?php echo $day; ?>
				</a>
			</span>
			<span class="vap-allorders-column" style="width: 20%;">
				<?php echo ($w['closed'] ? '/' : $from); ?>
			</span>
			<span class="vap-allorders-column" style="width: 20%;">
				<?php echo ($w['closed'] ? '/' : $to); ?>
			</span>
			<span class="vap-allorders-column" style="width: 20%;">
				<?php echo intval($w['closed']) == 0 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "css/images/disabled.png\"/>"; ?>
			</span>
		</div> 

		<?php
		$kk = ($kk + 1) % 2;	
	} ?>
	
<?php } ?>
	
	<?php echo JHtml::_('form.token'); ?>
	<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="empeditserwdays" />
	<input type="hidden" name="id" value="<?php echo $this->id_ser; ?>" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<?php
JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
JText::script('VAPCONFDIALOGMSG');
?>

<script>
	
	function vapCloseSerWorkingDays() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=empserviceslist&Itemid=' . $itemid, false); ?>';
	}
	
	<?php if ($auth->manageWorkDays()) { ?>

		function vapRemoveSerWorkingDays() {
			if (!EmployeeArea.hasChecked()) {
				alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
				return;
			}

			if (!confirm(Joomla.JText._('VAPCONFDIALOGMSG'))) {
				return;
			}

			Joomla.submitform('empeditserwdays.delete', document.empareaForm);
		}
		
		function vapRestoreSerWorkingDays() {
			Joomla.submitform('empeditserwdays.restore', document.empareaForm);
		}

	<?php } ?>
	
</script>
