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

$resStats       = $this->resStats;
$customers      = $this->customers;
$customersCount = $this->customersCount;

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$itemid = JFactory::getApplication()->input->getInt('Itemid');
	
// SET EMPLOYEE TIMEZONE
VikAppointments::setCurrentTimezone($employee['timezone']); 

$active_to_val = $active_to_class = '';

if ($employee['active_to'] == -1)
{
	$active_to_val 	 = JText::_('VAPACCOUNTVALIDTHRU1');
	$active_to_class = 'active';    
}
else if ($employee['active_to'] == 0)
{
	$active_to_val 	 = JText::_('VAPACCOUNTVALIDTHRU2');
	$active_to_class = 'pending'; 
}
else
{
	$active_to_val = date($date_format . ' ' . $time_format, $employee['active_to']);
	$now = time();

	if ($employee['active_to'] < $now)
	{
		$active_to_class = 'expired';
	}
	else if ($employee['active_to'] < $now + 86400 * 7)
	{
		$active_to_class = 'pending'; 
	}
	else
	{
		$active_to_class = 'active';
	}
}

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

<div class="vap-account-stat">
	
	<!-- ACCOUNT ACTIVE TO -->
	<div class="vap-account-info">
		<div class="vap-account-infolabel"><?php echo JText::_('VAPACCOUNTSTATUS1'); ?>:</div>
		<div class="vap-account-infovalue <?php echo $active_to_class; ?>">
			<?php echo $active_to_val; ?>
		</div>
	</div>
	
	<!-- ACCOUNT ACTIVE SINCE -->
	<?php if ($employee['active_since'] != -1) { ?>
		<div class="vap-account-info">
			<div class="vap-account-infolabel"><?php echo JText::_('VAPACCOUNTSTATUS6'); ?>:</div>
			<div class="vap-account-infovalue">
				<?php echo date($date_format . ' ' . $time_format, $employee['active_since']); ?>
			</div>
		</div>
	<?php } ?>
	
	<!-- CONFIRMED RESERVATIONS -->
	<div class="vap-account-info">
		<div class="vap-account-infolabel"><?php echo JText::_('VAPACCOUNTSTATUS2'); ?>:</div>
		<div class="vap-account-infovalue">
			<?php echo $resStats['conf_count']; ?>
		</div>
	</div>
	
	<!-- TOTAL RESERVATIONS -->
	<div class="vap-account-info">
		<div class="vap-account-infolabel"><?php echo JText::_('VAPACCOUNTSTATUS3'); ?>:</div>
		<div class="vap-account-infovalue">
			<?php echo $resStats['all_count']; ?>
		</div>
	</div>
	
	<!-- TOTAL EARNING -->
	<div class="vap-account-info">
		<div class="vap-account-infolabel"><?php echo JText::_('VAPACCOUNTSTATUS4'); ?>:</div>
		<div class="vap-account-infovalue">
			<?php echo VikAppointments::printPriceCurrencySymb($resStats['tot_earned']); ?>
		</div>
	</div>
	
	<!-- UNIQUE CUSTOMERS -->
	<div class="vap-account-info">
		<div class="vap-account-infolabel"><?php echo JText::_('VAPACCOUNTSTATUS5'); ?>:</div>
		<div class="vap-account-infovalue">
			<?php echo $customersCount; ?>
		</div>
	</div>
	
	<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empaccountstat&Itemid=' . $itemid);?>" method="POST">
	
		<div class="vap-accountstat-toolbar">
			<div class="vap-control pull-right">
				<input type="text" name="keysfilter" value="<?php echo $this->escape($this->keysFilter); ?>" class="vap-accountstat-search" placeholder="<?php echo JText::_('VAPACCOUNTSEARCHCUST'); ?>" />
			</div>
		</div>
	
		<?php if (count($customers) == 0) { ?>

			<div class="vap-allorders-void"><?php echo JText::_('VAPACCOUNTNOCUSTOMER'); ?></div>

		<?php } else { ?>

			<style>

				.vap-allorders-subrow {
					width: 100%;
					display: inline-block;
					margin-top: 8px;
					border-top: 1px dashed #ddd;
					padding: 5px 0;
				}

			</style>
			
			<div class="vap-allorders-list">
				<?php 
				$sub_data_lookup = array(
					'billing_state',
					'billing_city',
					'billing_address',
					'billing_zip',
					'company',
					'vatnum',
				);

				$kk = 1;
				foreach ($customers as $cust)
				{
					$image_path = '';
					
					if (empty($cust['image']))
					{
						$image_path = VAPASSETS_URI . 'css/images/default-profile.png';
					}
					else
					{
						$image_path = VAPASSETS_URI . 'customers/' . $cust['image'];
					}

					$has_sub_data = true;

					foreach ($sub_data_lookup as $key)
					{
						$has_sub_data &= (bool) $cust[$key];
					}

					?>
					<div class="vap-allorders-singlerow vap-empstats-tbl vap-allorders-row<?php echo $kk; ?>">
						<span class="vap-allorders-column" style="width: 7%;">
							<span class="accountstat-image-wrapper">
								<img src="<?php echo $image_path; ?>" class="vap-accstat-custimage" />
							</span>
						</span>
						<span class="vap-allorders-column" style="width: 20%;">
							<?php echo $cust['billing_name']; ?>
						</span>
						<span class="vap-allorders-column" style="width: 25%;">
							<?php echo $cust['billing_mail']; ?>
						</span>
						<span class="vap-allorders-column" style="width: 20%;">
							<?php echo (empty($cust['phone_prefix']) ? '' : $cust['phone_prefix'] . ' ') . $cust['billing_phone']; ?>
						</span>
						<span class="vap-allorders-column" style="width: 15%;">
							<?php
							if ($cust['country_code'])
							{
								?>
								<img src="<?php echo VAPASSETS_URI . 'css/flags/' . strtolower($cust['country_code']) . '.png'; ?>" alt="<?php echo $cust['country_code']; ?>" title="<?php echo $cust['country_code']; ?>" />
								<?php
							}
							else
							{
								echo '&nbsp;';
							}
							?>
						</span>
						<span class="vap-allorders-column vap-empstats-lastcolumn">
							<?php
							if ($has_sub_data)
							{
								?>
								<a href="javascript: void(0);" onclick="toggleBillingDetails(<?php echo $cust['id']; ?>, this);">
									<i class="fa fa-chevron-down big"></i>
								</a>
								<?php
							}
							else
							{
								echo '&nbsp;';
							}
							?>
						</span>

						<?php
						if ($has_sub_data)
						{
							?>
							<div class="vap-allorders-subrow vap-empstats-tbl" style="display:none;" id="cust-billing<?php echo $cust['id']; ?>">
								<span class="vap-allorders-column" style="width: 7%;">
									<?php echo $cust['billing_state']; ?>
								</span>
								<span class="vap-allorders-column" style="width: 20%;">
									<?php echo $cust['billing_city']; ?>
								</span>
								<span class="vap-allorders-column" style="width: 25%;">
									<?php echo $cust['billing_address']; ?>
								</span>
								<span class="vap-allorders-column" style="width: 20%;">
									<?php echo $cust['billing_zip']; ?>
								</span>
								<span class="vap-allorders-column" style="width: 15%;">
									<?php echo $cust['company']; ?>
								</span>
								<span class="vap-allorders-column vap-empstats-lastcolumn">
									<?php echo $cust['vatnum']; ?>
								</span>
							</div>
							<?php
						}
						?>
					</div>
				<?php
					$kk = ($kk + 1) % 2; 
				} 
				?>
			</div>
		
			<div class="vap-list-pagination"><?php echo $this->navbut; ?></div>
			<input type="hidden" name="option" value="com_vikappointments" />
			<input type="hidden" name="view" value="empaccountstat" />
			<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
	
		<?php } ?>
	
	</form>
	
	<?php
		
	$line_chart_labels 	= array();
	$line_chart_data 	= array();
	
	$min_line_chart_value = -1;
	$max_line_chart_value = -1;

	// setup available services
	foreach ($this->lineChartStat as $stat)
	{
		if (empty($line_chart_data[$stat['id_service']]))
		{
			$line_chart_data[$stat['id_service']] = array( 
				'label' 	=> $stat['sname'] . ' (' . $stat['rescount'] . ')',
				'months' 	=> array(),
				'rescount' 	=> $stat['rescount'],
			);
		}
		else
		{
			 $line_chart_data[$stat['id_service']]['rescount'] += $stat['rescount'];
			 $line_chart_data[$stat['id_service']]['label'] = $stat['sname'] . ' (' . $line_chart_data[$stat['id_service']]['rescount'] . ')';
		}
	}
	
	// setup labels
	$date_range = getdate($this->startRange);
	while ($date_range[0] < $this->endRange)
	{
		$date_id = $date_range['year'] . '-' . $date_range['mon'];
			
		$label = mb_substr(JText::_('VAPMONTH' . $date_range['mon']), 0, 3, 'UTF-8') . ' ' . $date_range['year'];
		array_push($line_chart_labels, $label);
		$date_range = getdate(mktime(0, 0, 0, $date_range['mon'] + 1, 1, $date_range['year']));
		
		// reset all values to all services
		foreach ($line_chart_data as $id_ser => $value)
		{
			$line_chart_data[$id_ser]['months'][$date_id] = 0;
		}
	}
	
	// setup statistics in services data
	foreach ($this->lineChartStat as $stat)
	{
		$line_chart_data[$stat['id_service']]['months'][$stat['month']] = $stat['earning'];
		
		if ($min_line_chart_value == -1 || $stat['earning'] < $min_line_chart_value)
		{
			$min_line_chart_value = $stat['earning'];
		}
		
		if ($max_line_chart_value == -1 || $stat['earning'] > $max_line_chart_value)
		{
			$max_line_chart_value = $stat['earning'];
		}
	}
	
	foreach ($line_chart_data as $id_ser => $dataset)
	{
		foreach ($dataset['months'] as $mon => $val)
		{
			if ($val == 0)
			{
				$min_line_chart_value = 0;
				break;
			}
		}
	}
	
	?>
	
	<div class="vap-charts-top">
	
		<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empaccountstat&Itemid=' . $itemid);?>" method="POST" id="vapchartform">
			
			<div class="vap-charts-filter">
				<div class="vap-charts-filter-dates">
					<span class="vap-charts-date-control">
						<label for="vapstartrange"><?php echo JText::_('VAPSTARTRANGE'); ?></label>
						<input class="vap-control-date calendar" type="text" value="<?php echo date($date_format, $this->startRange); ?>" id="vapstartrange" name="startrange" size="20" />
					</span>
					<span class="vap-charts-date-control">
						<label for="vapendrange"><?php echo JText::_('VAPENDRANGE'); ?></label>
						<input class="vap-control-date calendar" type="text" value="<?php echo date($date_format, $this->endRange); ?>" id="vapendrange" name="endrange" size="20" />
					</span>
				</div>
				
				<div class="vap-charts-filter-services">
					<?php
					foreach ($this->allServices as $s)
					{
						$selected = '';
						if (count($this->selectedServices) == 0 || in_array($s['id'], $this->selectedServices))
						{
							$selected = 'checked="checked"';
						}
						?>
						<div class="vap-charts-service-control">
							<input type="checkbox" value="<?php echo $s['id']; ?>" name="services[]" id="vapservice<?php echo $s['id']; ?>" <?php echo $selected; ?> />
							<label for="vapservice<?php echo $s['id']; ?>"><?php echo $s['name']; ?></label>
						</div>
					<?php } ?>
				</div>
				
				<div class="vap-charts-filter-submit">
					<button type="submit" class="vap-btn large blue"><?php echo JText::_('VAPCHARTSFILTER'); ?></button>
				</div>
			</div>
			
			<input type="hidden" name="animate" value="1" />
			<input type="hidden" name="option" value="com_vikappointments" />
			<input type="hidden" name="view" value="empaccountstat"/>
			<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
		</form>
		
		<div class="vap-pie-container">
			<div class="vap-piechart-wrapper">
				<canvas id="vap-piechart" class="piechart-graphics"></canvas>
			</div>
		</div>
		
	</div>
	
	<div class="vap-charts-container">
		<div class="vap-linechart-wrapper">
			<canvas id="vap-linechart" class="linechart-graphics"></canvas>
		</div>
		<div class="vap-linechart-sublegend">
			<small><i><?php echo JText::_('VAPLINECHARTSUBLEG'); ?></i></small>
		</div>
	</div>
	
</div>

<script>
	
	// DATEPICKER
	jQuery(document).ready(function() {

		var sel_format 		= "<?php echo $date_format; ?>";
		var df_separator 	= sel_format[1];

		sel_format = sel_format.replace(new RegExp("\\"+df_separator, 'g'), "");

		if (sel_format == "Ymd") {
			Date.prototype.format = "yy"+df_separator+"mm"+df_separator+"dd";
		} else if (sel_format == "mdY") {
			Date.prototype.format = "mm"+df_separator+"dd"+df_separator+"yy";
		} else {
			Date.prototype.format = "dd"+df_separator+"mm"+df_separator+"yy";
		}
		
		var today = new Date();
		jQuery("#vapstartrange:input, #vapendrange:input").datepicker({
			dateFormat: today.format,
		});
		
		<?php if (JFactory::getApplication()->input->getBool('animate', 0)) { ?>
			jQuery('html,body').animate( {scrollTop: (jQuery('.vap-charts-filter').first().offset().top-5)}, {duration:'normal'} );
		<?php } ?>
	});
	
	// GLOBAL CHARTS
	Chart.defaults.global.responsive = true;
	
	var RGBs = [
		{r:151, g:187, b:220},
		{r:100, g:195, b:132},
		{r:205, g: 65, b: 13},
		{r:250, g:145, b:15},
		{r:187, g:187, b:187},
		{r:  0, g:200, b:215},
		{r:180, g:230, b: 15},
		{r: 65, g: 65, b: 65},
	]; 

	var currency = Currency.getInstance();
	
	// LINE CHART
	
	<?php if (count($this->lineChartStat)) { ?>

		var MIN_LINE_CHART_VALUE = parseFloat('<?php echo $min_line_chart_value; ?>');
		var MAX_LINE_CHART_VALUE = parseFloat('<?php echo $max_line_chart_value; ?>'); 
		var LINE_CHART_STEPS = 15;
		
		if (MIN_LINE_CHART_VALUE > 0) {
			MIN_LINE_CHART_VALUE = Math.max(0, MIN_LINE_CHART_VALUE-25);
		}

		var data = {
			labels: <?php echo json_encode($line_chart_labels); ?>,
			datasets: []
		};
		
		<?php foreach ($line_chart_data as $id_ser => $dataset) { ?>
			var d_data = new Array();
			<?php foreach ($dataset['months'] as $mon => $d) { ?>
				d_data.push(parseFloat('<?php echo $d; ?>'));
			<?php } ?>
			
			var c = data.datasets.length%RGBs.length;
			
			data.datasets.push({
				// the label string that appears when hovering the mouse above the lines intersection points
				label: "<?php echo addslashes($dataset['label']); ?>",
				// the background color drawn behind the line
				backgroundColor: "rgba("+RGBs[c].r+","+RGBs[c].g+","+RGBs[c].b+",0.2)",
				// the fill color of the line
				borderColor: "rgba("+RGBs[c].r+","+RGBs[c].g+","+RGBs[c].b+",1)",
				// the fill color of the points
				pointBackgroundColor: "rgba("+RGBs[c].r+","+RGBs[c].g+","+RGBs[c].b+",1)",
				// the border color of the points
				pointBorderColor: "#fff",
				// the radius of the points (in pixel)
				pointRadius: 4,
				// the fill color of the points when hovered
				pointHoverBackgroundColor: "#fff",
				// the border color of the points when hovered
				pointHoverBorderColor: "rgba("+RGBs[c].r+","+RGBs[c].g+","+RGBs[c].b+",1)",
				// the radius of the points (in pixel) when hovered
				pointHoverRadius: 5,
				// the line dataset
				data: d_data,
			});
		<?php } ?>
		
		var options = {
			// display legend below the chart
			legend: {
				display: true,
				position: 'bottom',
			},
			// axes handling
			scales: {
				// change labels for Y axis
				yAxes: [{
					ticks: {
						// format value as curency
						callback: function(value, index, values) {
							// do not display decimal values on Y axis
							return currency.format(value, 0);
						},
					},
				}],
			},
			// tooltip handling
			tooltips: {
				// tooltip callbacks are used to customize default texts
				callbacks: {
					// format the tooltip text displayed when hovering a point
					label: function(tooltipItem, data) {
						// keep default label
						var label = data.datasets[tooltipItem.datasetIndex].label || '';

						if (label) {
							label += ': ';
						}

						// format value as currency
						label += currency.format(tooltipItem.value);

						return ' ' + label;
					},
					// change label colors because, by default, the legend background is blank
					labelColor: function(tooltipItem, chart) {
						// get tooltip item meta data
						var meta = chart.data.datasets[tooltipItem.datasetIndex];

						return {
							// use white border
							borderColor: 'rgb(0,0,0)',
							// use same item background color
							backgroundColor: meta.borderColor,
						};
					},
				},
			},
		};
		
		// get 2D canvas for LINE chart
		var ctx = document.getElementById('vap-linechart').getContext('2d');

		// display LINE chart
		var myLineChart = new Chart(ctx, {
			type:    'line',
			data:    data,
			options: options,
		});
	
	<?php } ?>
	
	// PIE CHART

	<?php if (count($this->pieChartStat)) { ?>
		
		// build PIE data
		var pie_data = {
			// dataset options goes here
			datasets: [{
				// dataset values
				data: [],
				// dataset color
				backgroundColor: [],
			}],
			// dataset labels
			labels: [],
		};

		<?php foreach ($this->pieChartStat as $dataset) { ?> 
			var c = pie_data.labels.length % RGBs.length;

			pie_data.datasets[0].data.push(parseFloat('<?php echo $dataset['earning']; ?>'));
			pie_data.datasets[0].backgroundColor.push('rgb(' + RGBs[c].r + ', ' + RGBs[c].g + ', ' + RGBs[c].b + ')');
			pie_data.labels.push('<?php echo addslashes($dataset['sname']); ?>');
		<?php } ?>
			
		var pie_options = {
			// hide legend
			legend: {
				display: false,
			},
			// tooltip handling
			tooltips: {
				// tooltip callbacks are used to customize default texts
				callbacks: {
					// format the tooltip text displayed when hovering a point
					label: function(tooltipItem, data) {
						// keep default label
						var label = data.labels[tooltipItem.index] || '';

						if (label) {
							label += ': ';
						}

						// format value as currency
						label += currency.format(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]);

						return ' ' + label;
					},
					// change label colors because, by default, the legend background is blank
					labelColor: function(tooltipItem, chart) {
						// get tooltip item meta data
						var meta = chart.data.datasets[tooltipItem.datasetIndex];

						return {
							// use white border
							borderColor: 'rgb(0,0,0)',
							// use same item background color
							backgroundColor: meta.backgroundColor[tooltipItem.index],
						};
					},
				},
			},
		};
			
		// get 2D canvas for PIE chart
		var pie_ctx = document.getElementById('vap-piechart').getContext('2d');

		// display PIE chart
		var myPieChart = new Chart(pie_ctx, {
			type: 'doughnut',
			data: pie_data,
			options: pie_options,
		});
	
	<?php } ?>

	function toggleBillingDetails(id, btn) {

		if (jQuery('#cust-billing' + id).is(':visible')) {
			jQuery(btn).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
		} else {
			jQuery(btn).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
		}

		jQuery('#cust-billing' + id).toggle();

	}
	
</script>
