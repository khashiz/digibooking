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
JHtml::_('behavior.calendar');

$rows   = $this->rows;
$navbut = $this->navbut;

$ordering = $this->ordering;

$filters = $this->filters;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();
ArasJoomlaVikApp::datePicker();
foreach (array('o.id', 'o.createdon', 'o.total_cost', 'o.tot_paid') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('subscrorders', JText::_('VAPMANAGESUBSCRORD1'), 'o.id', $ordering['o.id'], 1, $filters, 'vapheadcolactive'.(($ordering['o.id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('subscrorders', JText::_('VAPMANAGESUBSCRORD3'), 'o.createdon', $ordering['o.createdon'], 1, $filters, 'vapheadcolactive'.(($ordering['o.createdon'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('subscrorders', JText::_('VAPMANAGESUBSCRORD6'), 'o.total_cost', $ordering['o.total_cost'], 1, $filters, 'vapheadcolactive'.(($ordering['o.total_cost'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('subscrorders', JText::_('VAPMANAGESUBSCRORD7'), 'o.tot_paid', $ordering['o.tot_paid'], 1, $filters, 'vapheadcolactive'.(($ordering['o.tot_paid'] == 2) ? 1 : 2)),
);

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');
$dt_format   = $date_format . ' ' . $time_format;

$nowdf = $vik->jdateFormat($date_format);

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

$is_searching = $this->hasFilters();

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left input-append">
			<input type="text" name="keysearch" id="vapkeysearch" class="vapkeysearch" size="32" 
				value="<?php echo $filters['keysearch']; ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />

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
			<button type="button" class="btn" onClick="vapOpenJModal('subscremp', null, true);">
				<?php echo JText::_('VAPGOTOEMPSUBSCR'); ?>
			</button>
		</div>

		<?php if (count($rows) == 1 && strlen($rows[0]['cc_data'])) { ?>

			<div class="btn-group pull-right">
				<button type="button" class="btn btn-primary" onclick="vapOpenJModal('ccdetails', null, true); return false;">
					<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;<?php echo JText::_('VAPSEECCDETAILS'); ?>
				</button>
			</div>

		<?php } ?>

	</div>
	
	<div class="btn-toolbar" id="vap-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">
		
		<?php
		$options = array();
		$options[] = JHtml::_('select.option', '', 'VAPFILTERSELECTSTATUS');
		$options[] = JHtml::_('select.option', 'confirmed', 'VAPSTATUSCONFIRMED');
		$options[] = JHtml::_('select.option', 'pending', 'VAPSTATUSPENDING');
		$options[] = JHtml::_('select.option', 'removed', 'VAPSTATUSREMOVED');
		$options[] = JHtml::_('select.option', 'canceled', 'VAPSTATUSCANCELED');
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vap-status-sel" class="<?php echo (!empty($filters['status']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status'], true); ?>
			</select>
		</div>

		<?php
		$options = array();
		$options[] = JHtml::_('select.option', 0, 'VAPFILTERSELECTSUBSCR');
		foreach ($this->subscriptions as $s)
		{
			$options[] = JHtml::_('select.option', $s['id'], $s['name']);
		}
		?>
		<div class="btn-group pull-left">
			<select name="id_subscr" id="vap-subscr-sel" class="<?php echo (!empty($filters['id_subscr']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['id_subscr'], true); ?>
			</select>
		</div>

		<?php
		$options = array();
		$options[] = JHtml::_('select.option', 0, 'VAPFILTERSELECTPAYMENT');
		foreach ($this->payments as $p)
		{
			$options[] = JHtml::_('select.option', $p['id'], $p['name']);
		}
		?>
		<div class="btn-group pull-left">
			<select name="id_payment" id="vap-payment-sel" class="<?php echo (!empty($filters['id_payment']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['id_payment'], true); ?>
			</select>
		</div>

		<div class="btn-group pull-left">
			<?php echo $vik->calendar($filters['datefilter'], 'datefilter', 'vapdatefilter', $nowdf, array('class'=>'vapdatefilter', 'onChange' => 'document.adminForm.submit();')); ?>
		</div>

	</div>
	
<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNOSUBSCRORDER'); ?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="75" style="text-align: left;"><?php echo $links[0]; ?></th>
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="100" style="text-align: left;"><?php echo JText::_('VAPMANAGESUBSCRORD2'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[1]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGESUBSCRORD4'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGESUBSCRORD5'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="150" style="text-align: center;"><?php echo JText::_('VAPMANAGESUBSCRORD9'); ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo $links[2]; ?></th>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo $links[3]; ?></th>
				<?php if ($this->hasInvoices) { ?>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION35'); ?></th>
				<?php } ?>
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGESUBSCRORD8'); ?></th>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td style="text-align: left;"><?php echo $row['id']; ?></td>
				
				<td style="text-align: left;"><a href="index.php?option=com_vikappointments&amp;task=editsubscrorder&amp;cid[]=<?php echo $row['id']; ?>"><?php echo $row['sid']; ?></a></td>
				
				<td style="text-align: center;"><?php echo ArasJoomlaVikApp::jdate($dt_format, $row['createdon']); ?></td>
				
				<td style="text-align: center;">
					<a href="index.php?option=com_vikappointments&amp;task=editemployee&amp;cid[]=<?php echo $row['empid']; ?>">
						<?php echo $row['lastname'] . ' ' . $row['firstname']; ?>
					</a>
				</td>
				
				<td style="text-align: center;"><?php echo $row['subname']; ?></td>
				
				<td style="text-align: center;"><?php echo $row['payname']; ?></td>
				
				<td style="text-align: center;"><?php echo VikAppointments::printPriceCurrencySymb($row['total_cost']); ?></td>
				
				<td style="text-align: center;"><?php echo VikAppointments::printPriceCurrencySymb($row['tot_paid']); ?></td>
				
				<?php if ($this->hasInvoices) { ?>
					<td style="text-align: center;">
						<?php if ($row['invoice']) { ?>
							<a href="<?php echo $row['invoice']; ?>" target="_blank">
								<i class="fa fa-file-pdf-o big"></i>
							</a>
						<?php } ?>
					</td>
				<?php } ?>

				<td style="text-align: center;" class="vapreservationstatus<?php echo strtolower($row['status']); ?>" >
					<?php echo JText::_('VAPSTATUS' . $row['status']); ?>
				</td>
			</tr>
		<?php }	?>
	
	</table>

<?php } ?>
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="subscrorders" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $navbut; ?>
</form>

<?php
if (count($rows) == 1)
{
	// credit card modal
	echo JHtml::_(
		'bootstrap.renderModal',
		'jmodal-ccdetails',
		array(
			'title'       => JText::_('VAPSEECCDETAILS'),
			'closeButton' => true,
			'keyboard'    => false, 
			'bodyHeight'  => 60,
			'modalWidth'  => 60,
			'url'		  => 'index.php?option=com_vikappointments&task=ccdetails&tmpl=component&type=employees&id=' . $rows[0]['id'],
		)
	);
}

// overview modal
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-subscremp',
	array(
		'title'       => JText::_('VAPEMOSUBSCROVERVIEW'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => 'index.php?option=com_vikappointments&task=subscremp&tmpl=component',
	)
);
?>

<script>

	function vapOpenJModal(id, url, jqmodal) {
		<?php echo $vik->bootOpenModalJS(); ?>
	}
	
	// search

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
		
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vap-status-sel').updateChosen('');
		jQuery('#vap-subscr-sel').updateChosen(0);
		jQuery('#vap-payment-sel').updateChosen(0);
		jQuery('#vapdatefilter').val('');
		
		document.adminForm.submit();
	}

</script>
