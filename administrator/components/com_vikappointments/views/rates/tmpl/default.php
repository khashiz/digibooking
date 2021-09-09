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

JHtml::_('formbehavior.chosen');

$rows 	= $this->rows;
$navbut = $this->navbut;

$ordering = $this->ordering;

$filters = $this->filters;

$vik = UIApplication::getInstance();

foreach (array('id', 'name', 'people', 'charge', 'createdon') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('rates', JText::_('VAPMANAGESERVICE1'), 'id', $ordering['id'], 1, $filters, 'vapheadcolactive'.(($ordering['id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('rates', JText::_('VAPMANAGEGROUP2'), 'name', $ordering['name'], 1, $filters, 'vapheadcolactive'.(($ordering['name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('rates', JText::_('VAPMANAGERESERVATION25'), 'people', $ordering['people'], 1, $filters, 'vapheadcolactive'.(($ordering['people'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('rates', JText::_('VAPMANAGEPAYMENT4'), 'charge', $ordering['charge'], 1, $filters, 'vapheadcolactive'.(($ordering['charge'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('rates', JText::_('VAPMANAGERESERVATION37'), 'createdon', $ordering['createdon'], 1, $filters, 'vapheadcolactive'.(($ordering['createdon'] == 2) ? 1 : 2)),
);

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

$is_searching = $this->hasFilters();

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$days_lookup = array(
	JText::_('SUN'),
	JText::_('MON'),
	JText::_('TUE'),
	JText::_('WED'),
	JText::_('THU'),
	JText::_('FRI'),
	JText::_('SAT'),
);

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left input-append">
			<input type="text" name="keys" id="vapkeysearch" size="32" 
				value="<?php echo $filters['keys']; ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />

			<button type="submit" class="btn">
				<i class="icon-search"></i>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn <?php echo ($is_searching ? 'btn-primary' : ''); ?>" onclick="vapToggleSearchToolsButton(this);">
				<?php echo JText::_('JSEARCH_TOOLS'); ?>&nbsp;<i class="fa fa-caret-<?php echo ($is_searching ? 'up' : 'down'); ?>" id="vap-tools-caret"></i>
			</button>
		</div>
		
		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>

		<div class="btn-group pull-right">
			<button type="button" class="btn" onclick="vapOpenJModal('ratestest', null, true);">
				<?php echo JText::_('VAPTESTRATES'); ?>
			</button>
		</div>

	</div>

	<div class="btn-toolbar" id="vap-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<?php
		$options = array(
			JHtml::_('select.option', -1, JText::_('VAPFILTERSELECTSTATUS')),
			JHtml::_('select.option', 1, JText::_('JPUBLISHED')),
			JHtml::_('select.option', 0, JText::_('JUNPUBLISHED')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vap-status-sel" class="<?php echo ($filters['status'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status']); ?>
			</select>
		</div>

	</div>

<?php if (count($this->rows) == 0) { ?>
	
	<p><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="50" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="150" style="text-align: left;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPMANAGEGROUP3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPWEEKDAYS'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPTIME'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo $links[3]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPMANAGEPAYMENT3'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[4]; ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];

			if (count($row['services']))
			{
				if ($row['description'])
				{
					$row['description'] .= "<br /><br />";
				}
				$row['description'] .= implode(', ', $row['services']);
			}
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td><?php echo $row['id']; ?></td>

				<td>
					<?php if ($core_edit) { ?>
						<a href="index.php?option=com_vikappointments&amp;task=editspecialrate&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a>
					<?php } else { ?>
						<?php echo $row['name']; ?>
					<?php } ?>
				</td>

				<td style="text-align: center;">
					<?php echo $vik->createPopover(array(
						'title' 	=> JText::_('VAPMANAGEGROUP3'),
						'content' 	=> $row['description'],
					)); ?>
				</td>

				<td style="text-align: center;">
					<?php
					$wdays = strlen($row['weekdays']) ? explode(',', $row['weekdays']) : array();

					if (count($wdays))
					{
						$wdays = array_map(function($day) use ($days_lookup)
						{
							return $days_lookup[$day];
						}, $wdays);

						$wdays = implode(', ', $wdays);
					}
					else
					{
						// all the week days
						$wdays = '/';
					}

					echo $wdays;
					?>
				</td>

				<td style="text-align: center;">
					<?php
					if ($row['fromdate'])
					{
						// do not format as it doesn't depend on the timezone set
						echo JDate::getInstance($row['fromdate'])->format($date_format) . ' - ';
						
						if ($row['todate'])
						{
							echo JDate::getInstance($row['todate'])->format($date_format);
						}
						else
						{
							echo '&infin;';
						}
					}
					else
					{
						echo '/';
					}
					?>
				</td>

				<td style="text-align: center;">
					<?php
					if ($row['fromtime'] && $row['fromtime'] < $row['totime'])
					{
						$fh = floor($row['fromtime'] / 60);
						$fm = $row['fromtime'] % 60;

						$th = floor($row['totime'] / 60);
						$tm = $row['totime'] % 60;
						
						/**
						 * Create dates in UTC format.
						 *
						 * @since 1.6.2
						 */
						$from = JDate::getInstance("today $fh:$fm:00");
						$to   = JDate::getInstance("today $th:$tm:00");

						// do not format as it doesn't depend on the timezone set
						echo $from->format($time_format) . ' - ' . $to->format($time_format);
					}
					else
					{
						echo '/';
					}
					?>
				</td>

				<td style="text-align: center;"><?php echo $row['people'] ? $row['people'] : '/'; ?></td>
				
				<td style="text-align: center;"><?php echo VikAppointments::printPriceCurrencySymb($row['charge']); ?></td>
				
				<td style="text-align: center;">
					<?php if ($core_edit) { ?>
						<a href="index.php?option=com_vikappointments&task=changeStatusColumn&table_db=special_rates&column_db=published&val=<?php echo $row['published']; ?>&id=<?php echo $row['id']; ?>&return_task=rates">
							<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
						</a>
					<?php } else { ?>
						<?php echo intval($row['published']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
					<?php } ?>
				</td>

				<td style="text-align: center;">
					<?php echo JHtml::_('date', $row['createdon'], $date_format . ' ' . $time_format); ?>
				</td>
			</tr>  
		<?php } ?>
		
	</table>
	
<?php } ?>

	<input type="hidden" name="task" value="rates" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-ratestest',
	array(
		'title'       => JText::_('VAPMANAGESPECIALRATES'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => 'index.php?option=com_vikappointments&task=ratestest&tmpl=component',
	)
);
?>
	
<script>

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-status-sel').updateChosen(-1);
		
		document.adminForm.submit();
	}

	function vapOpenJModal(id, url, jqmodal) {
		<?php echo $vik->bootOpenModalJS(); ?>
	}

</script>
