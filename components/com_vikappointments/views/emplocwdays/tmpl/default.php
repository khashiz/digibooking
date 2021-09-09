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
		<h2><?php echo JText::sprintf('VAPEMPLOCWDAYSPAGETITLE', $employee['firstname'].' '.$employee['lastname']); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manageWorkDays()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapUpdateWorkDaysLocations(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
			<div class="vapempbtn">
				<button type="button" onClick="vapUpdateWorkDaysLocations(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>

		<div class="vapempbtn">
			<button type="button" onClick="vapCloseWorkDaysLocations();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empeditwdays'); ?>" method="post" name="empareaForm" id="empareaForm" style="min-height: 250px;">
		
	<?php if (count($this->worktimes)) { ?>
		<div class="vap-emploc-maincont">
			
			<div class="vap-allorders-singlerow vap-allorders-row1 head" style="text-align: center;">
				<span class="vap-allorders-column" style="width: 22%;">
					<?php echo JText::_( 'VAPEDITWD1' ); ?>
				</span>
				<span class="vap-allorders-column" style="width: 15%;">
					<?php echo JText::_( 'VAPEDITWD2' ); ?>
				</span>
				<span class="vap-allorders-column" style="width: 15%;">
					<?php echo JText::_( 'VAPEDITWD3' ); ?>
				</span>
				<span class="vap-allorders-column" style="width: 45%;">
					<?php echo JText::_( 'VAPEDITWD10' ); ?>
				</span>
			</div>

			<?php 
			$kk = 0;
			for ($i = 0; $i < count($this->worktimes); $i++)
			{
				$kk = ($kk + 1) % 2; 

				$row = $this->worktimes[$i];
			
				$day = '';
				if ($row['ts'] <= -1)
				{
					$day = JText::_('VAPDAY'.($row['day'] > 0 ? $row['day'] : 7));
				}
				else
				{
					/**
					 * The dates are now displayed using UTC timezone.
					 *
					 * @since 1.6.2
					 */
					// $day = JHtml::_('date', $row['ts'], $date_format);
					$day = JDate::getInstance($row['ts'])->format($date_format);

					if ($row['day'] == -1)
					{
						$row['day'] = date('w', $row['ts']);
					}

					// $day .= ' (' . JHtml::_('date', $row['ts'], 'D') . ')';
					$day .= ' (' . JDate::getInstance($row['ts'])->format('D') . ')';
				}
				
				$fh = intval($row['fromts'] / 60);
				$fm = $row['fromts'] % 60;
				if ($fh < 10)
				{
					$fh = '0' . $fh;
				}
				if ($fm < 10)
				{
					$fm = '0' . $fm;
				}
				
				$eh = intval($row['endts'] / 60);
				$em = $row['endts'] % 60;
				if ($eh < 10)
				{
					$eh = '0' . $eh;
				}
				if ($em < 10)
				{
					$em = '0' . $em;
				}
				
				?>
				<div class="vap-allorders-singlerow vap-allorders-row<?php echo $kk; ?>" style="text-align: center;">
					<span class="vap-allorders-column" style="width: 22%;">
						<?php echo $day; ?>
					</span>
					<span class="vap-allorders-column" style="width: 15%;">
						<td style="text-align: center;"><?php echo "$fh:$fm"; ?></td>
					</span>
					<span class="vap-allorders-column" style="width: 15%;">
						<td style="text-align: center;"><?php echo "$eh:$em"; ?></td>
					</span>
					<span class="vap-allorders-column" style="width: 45%;">
						<select name="location[<?php echo $row['id']; ?>]" class="vap-locations-sel">
							<option></option>
							<?php foreach ($this->locations as $id_emp => $list) { ?>

								<optgroup label="<?php echo ($id_emp > 0 ? $employee['nickname'] : JText::_('VAPEMPSETTINGSGLOBAL')); ?>">
								
									<?php foreach ($list as $l) { ?>
									
										<option value="<?php echo $l['id']; ?>" <?php echo ($l['id']==$row['id_location'] ? 'selected="selected"' : ''); ?>><?php echo $l['label']; ?></option>

									<?php } ?>

								</optgroup>

							<?php } ?>
						</select>
					</span>
				</div>
			<?php } ?>

		</div>

	<?php } else { ?>

		<div class="vap-allorders-void long">
			<p><?php echo JText::_('VAPNOWORKINGDAYS'); ?></p>
			<p><?php echo JText::sprintf('VAPSERWDLINKTO', JRoute::_('index.php?option=com_vikappointments&view=empeditwdays')); ?></p>
		</div>

	<?php } ?>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" /> 
	
	<?php echo JHtml::_('form.token'); ?>
	<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="emplocwdays" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<script>

	jQuery(document).ready(function() {
		jQuery(".vap-locations-sel").select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});
	});
	
	function vapCloseWorkDaysLocations() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false); ?>';
	}
	
	<?php if ($auth->manageWorkDays()) { ?>

		function vapUpdateWorkDaysLocations(close) {
			
			if (close) {
				jQuery('#vaphiddenreturn').val('1');
			}
			
			Joomla.submitform('emplocwdays.save', document.empareaForm);
		}

	<?php } ?>
	
</script>
