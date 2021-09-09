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

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$last_id_emp = (count($this->locations) ? $this->locations[0]['id_employee'] : 0);

$global_opt_group = JText::_('VAPMENUTITLEHEADER3');

$rows = array('weekday' => $this->worktimes, 'custom' => $this->workdays);
$tabs = array('weekday' => 'VAPWDLEGENDLABEL1', 'custom' => 'VAPWDLEGENDLABEL2')

?>

<form action="index.php" method="POST" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 32px;margin-top: 0;">
		<div class="btn-group pull-left">
			<button type="submit" class="btn btn-success" style="width: 120px;">
				<i class="icon-apply icon-white"></i>
				<?php echo JText::_('VAPSAVE'); ?>
			</button>
		</div>
	</div>

<?php if (!count($this->worktimes) && !count($this->workdays)) { ?>

	<p><?php echo JText::_('VAPNOWORKINGDAYS'); ?></p>
	<p><?php echo JText::sprintf('VAPSERWDLINKTO', 'index.php?option=com_vikappointments&task=editemployee&cid[]=' . $this->idEmployee); ?></p>

<?php } else { ?>

	<!-- START TAB SET -->

	<?php echo $vik->bootStartTabSet('emplocwd', array('active' => 'emplocwd_weekday')); ?>

		<?php foreach ($rows as $tab => $worktimes) { ?>

			<!-- WEEKLY WORKING DAYS -->
			
			<?php echo $vik->bootAddTab('emplocwd', 'emplocwd_' . $tab, JText::_($tabs[$tab])); ?>
	
				<?php if (count($worktimes) == 0) { ?>

					<p><?php echo JText::_('VAPNOWORKINGDAYS'); ?></p>

				<?php } else { ?>

					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
						
						<?php echo $vik->openTableHead(); ?>
							<tr>
								<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;"><?php echo JText::_('VAPMANAGEWD2'); ?></th>
								<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWD3'); ?></th>
								<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWD4'); ?></th>
								<th class="<?php echo $vik->getAdminThClass(); ?>" width="30%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWD7'); ?></th>
								<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;">&nbsp;</th>
							</tr>
						<?php echo $vik->closeTableHead(); ?>
						
						<?php for ($i = 0, $n = count($worktimes); $i < $n; $i++) {
							$row = $worktimes[$i];
							
							$day = '';

							if ($row['ts'] == -1)
							{
								$day = JText::_('VAPDAY' . ($row['day'] > 0 ? $row['day'] : 7));
							}
							else
							{
								$day = JDate::getInstance($row['ts'])->format('D d F, Y');
							}

							$fh = floor($row['fromts'] / 60);
							$fm = $row['fromts'] % 60;
							
							$eh = floor($row['endts'] / 60);
							$em = $row['endts'] % 60;

							$options = array();
							$options[0][] = JHtml::_('select.option', '', '');

							foreach ($this->locations as $location)
							{
								$group = $location['id_employee'] > 0 ? 0 : $global_opt_group;

								if (!isset($options[$group]))
								{
									$options[$group] = array();
								}

								$options[$group][] = JHtml::_('select.option', $location['id'], $location['label']);
							}
							
							?>

							<tr class="row<?php echo ($i % 2); ?>">
								
								<td style="text-align: left;"><?php echo $day; ?></td>
								
								<td style="text-align: center;"><?php echo date($config->get('timeformat'), mktime($fh, $fm, 0, 1, 1, 2000)); ?></td>
								
								<td style="text-align: center;"><?php echo date($config->get('timeformat'), mktime($eh, $em, 0, 1, 1, 2000)); ?></td>
								
								<td style="text-align: center;">
									<?php
									$attrs = array('class' => 'vap-locations-sel', 'data-location' => $tab, 'data-id' => $row['id']);

									$params = array(
										'id' 			=> 'vap-location-' . $row['id'],
										'list.attr' 	=> $attrs,
										'group.items' 	=> null,
										'list.select'	=> $row['id_location'],
									);
									echo JHtml::_('select.groupedList', $options, 'location[' . $row['id'] . ']', $params);
									?>
								</td>

								<td style="text-align: left;">
									<button type="button" class="btn btn-update-locs" id="btn-upd-loc<?php echo $row['id']; ?>" 
										style="display: none;" onclick="updateAllLocations(<?php echo $row['id']; ?>, '<?php echo $tab; ?>');">
										<?php echo JText::_('VAPUPDATEALL'); ?>
									</button>
								</td>

							</tr>
						<?php } ?>

					</table>
				<?php } ?>

			<?php echo $vik->bootEndTab(); ?>

			<!-- CLOSE TAB PANE -->

		<?php } ?>

	<?php echo $vik->bootEndTabSet(); ?>

<?php } ?>

	<!-- CLOSE TAB SET -->
	
	<input type="hidden" name="id_employee" value="<?php echo $this->idEmployee; ?>" />
	<input type="hidden" name="task" value="updateLocationsWorktimesAssoc" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<script>
	
	jQuery(document).ready(function() {

		jQuery('.vap-locations-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

		jQuery('.vap-locations-sel').on('change', function() {
			jQuery('.btn-update-locs').hide();
			jQuery('#btn-upd-loc' + jQuery(this).data('id')).show();
		});

	});

	function updateAllLocations(id, tab) {

		var selected = jQuery('#vap-location-'+id).val();
		jQuery('select[data-location="'+tab+'"]').select2('val', selected);

	}
	
</script>
