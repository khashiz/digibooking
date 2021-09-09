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

JHtml::_('behavior.calendar');
JHtml::_('formbehavior.chosen');

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');

$nowdf = $vik->jdateFormat($date_format);

$chart_value_primary   = $this->valueType == 'ordcount' ? 'ordcount' : 'earning';
$chart_value_secondary = $this->valueType == 'ordcount' ? 'earning'  : 'ordcount';

?>

<form action="index.php" method="POST" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left">
			<?php echo $vik->calendar(date($date_format, $this->startRange), 'startrange', 'vapstartdate'); ?>
		</div>

		<div class="btn-group pull-left">
			<?php echo $vik->calendar(date($date_format, $this->endRange), 'endrange', 'vapenddate'); ?>
		</div>

		<?php
		$options = array(
			JHtml::_('select.option', 'earning', JText::_('VAPREPORTSVALUETYPEOPT1')),
			JHtml::_('select.option', 'ordcount', JText::_('VAPREPORTSVALUETYPEOPT3')),
		);
		?>
		<div class="btn-group pull-left">
			<select name="valuetype" id="vap-valuetype-sel">
				<?php echo JHtml::_('select.options', $options, 'value', 'text', $this->valueType)?>
			</select>
		</div>

		<div class="btn-group pull-left">
			<button type="submit" class="btn">
				<?php echo JText::_('VAPRESERVATIONBUTTONFILTER'); ?>
			</button>
		</div>

	</div>
	
	<?php
	if (count($this->groups) > 1)
	{
		?>
		<div class="vap-charts-filter-employees">
			<?php foreach ($this->groups as $g)
			{ 
				$selected = '';
				if (count($this->selectedGroups) == 0 || in_array($g['id'], $this->selectedGroups))
				{
					$selected = 'checked="checked"';
				}
				?>
				<div class="vap-charts-employee-control">
					<input type="checkbox" value="<?php echo $g['id']; ?>" name="groups[]" id="vapemployee<?php echo $g['id']; ?>" <?php echo $selected; ?>/>
					<label for="vapemployee<?php echo $g['id']; ?>"><?php echo $g['title']; ?></label>
				</div>
			<?php } ?>
		</div>
		<?php
	}
		
	$line_chart_labels 	= array();
	$line_chart_data 	= array();
	
	$min_line_chart_value = -1;
	$max_line_chart_value = -1;

	// setup available employees
	foreach ($this->lineChartStat as $stat)
	{
		if (empty($line_chart_data[$stat['id_group']]))
		{
			$line_chart_data[$stat['id_group']] = array( 
				'label'  => '',
				'months' => array(),
				'value'  => 0,
			);
		}

		$line_chart_data[$stat['id_group']]['value'] += $stat[$chart_value_secondary];

		$label = '(' . $line_chart_data[$stat['id_group']]['value'] . ')';

		if ($chart_value_secondary == 'earning')
		{
			if ($line_chart_data[$stat['id_group']]['value'] > 0)
			{
				// format currency if we need to show the earning as secondary value
				$label = '(' . VikAppointments::printPriceCurrencySymb($line_chart_data[$stat['id_group']]['value']) . ')';
			}
			else
			{
				// do not use price label as the services might not have online costs
				$label = '';
			}
		}
		
		$line_chart_data[$stat['id_group']]['label'] = $stat['name'] . ' ' . $label;
	}
	
	// setup labels
	$date_range = getdate($this->startRange);
	while ($date_range[0] < $this->endRange)
	{
		$date_id = $date_range['year'] . '-' . $date_range['mon'];
			
		$label = mb_substr(JText::_('VAPMONTH'.$date_range['mon']), 0, 3, 'UTF-8') . ' ' . $date_range['year'];
		$line_chart_labels[] = $label;
		$date_range = getdate(mktime(0, 0, 0, $date_range['mon'] + 1, 1, $date_range['year']));
		
		// reset all values to all employees
		foreach ($line_chart_data as $id_group => $value)
		{
			$line_chart_data[$id_group]['months'][$date_id] = 0;
		}
	}
	
	// setup statistics in employees data
	foreach ($this->lineChartStat as $stat)
	{
		$line_chart_data[$stat['id_group']]['months'][$stat['month']] = $stat[$chart_value_primary];
		
		if ($min_line_chart_value == -1 || $stat[$chart_value_primary] < $min_line_chart_value)
		{
			$min_line_chart_value = $stat[$chart_value_primary];
		}
		
		if ($max_line_chart_value == -1 || $stat[$chart_value_primary] > $max_line_chart_value)
		{
			$max_line_chart_value = $stat[$chart_value_primary];
		}
	}
	
	foreach ($line_chart_data as $id_group => $dataset)
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

	<div>
		<div class="span12">
			<?php echo $vik->openEmptyFieldset(); ?>
			
				<div class="vap-empchart-box">
					
					<div class="vap-linechart-container">
						<div class="vap-linechart-wrapper">
							<canvas id="vap-linechart" class="linechart-graphics"></canvas>
						</div>
						<div class="vap-linechart-sublegend">
							<small><i><?php echo JText::_('VAPLINECHARTSUBLEG3'); ?></i></small>
						</div>
					</div>
					
				</div>

			<?php echo $vik->closeEmptyFieldset(); ?>
		</div>
	</div>

	<div>
		<?php
		foreach ($this->pieChartStat as $id_group => $group)
		{
			$label =
				'<span class="report-pack-group">' . $group['name'] . '</span>' . 
				'<span class="report-pack-total">' . VikAppointments::printPriceCurrencySymb($group['earning']) . '</span>' . 
				'<span class="report-pack-count">' . $group['count'] . ' ' . strtolower(JText::_('VAPMENUPACKAGES')) . '</span>'
			;

			?>
			<div class="span6">
				<?php echo $vik->openFieldset($label); ?>

					<div class="vap-piechart-wrapper">
						<canvas id="vap-piechart-<?php echo $id_group; ?>" class="piechart-graphics"></canvas>
					</div>

				<?php echo $vik->closeFieldset(); ?>
			</div>
			<?php
		}
		?>
	</div>
	
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="task" value="reportspack" />

</form>

<script>
	
	// GLOBAL CHARTS
	Chart.defaults.global.responsive = true;
	
	var RGBs = [
		// {r:151, g:187, b:220},
		'97bbdc',
		// {r:100, g:195, b:132},
		'64c384',
		// {r:205, g: 65, b: 13},
		'cd410d',
		// {r:250, g:145, b:15},
		'fa910f',
		// {r:187, g:187, b:187},
		'bbbbbb',
		// {r:  0, g:200, b:215},
		'00c8d7',
		// {r:180, g:230, b: 15},
		'b4e60f',
		// {r: 65, g: 65, b: 65},
		'414141',
	];

	var currency = Currency.getInstance();

	var valueTypeAxisY = '<?php echo $chart_value_primary; ?>';

	jQuery(document).ready(function() {

		VikRenderer.chosen('.btn-toolbar');

	});
	
<?php if (count($this->lineChartStat) > 0) { ?>

	// LINE CHART

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
	
	<?php foreach ($line_chart_data as $id_group => $dataset) { ?>
		var d_data = new Array();
		<?php foreach ($dataset['months'] as $mon => $d) { ?>
			d_data.push(parseFloat('<?php echo $d; ?>'));
		<?php } ?>
		
		var c = data.datasets.length % RGBs.length;
		var hex = RGBs[c];
		
		data.datasets.push({
			// the label string that appears when hovering the mouse above the lines intersection points
			label: "<?php echo addslashes(trim($dataset['label'])); ?>",
			// the background color drawn behind the line
			//backgroundColor: "rgba("+RGBs[c].r+","+RGBs[c].g+","+RGBs[c].b+",0.2)",
			backgroundColor: "#" + hex + "33", // 33 is 20% opacity
			// the fill color of the line
			// borderColor: "rgba("+RGBs[c].r+","+RGBs[c].g+","+RGBs[c].b+",1)",
			borderColor: "#" + hex,
			// the fill color of the points
			// pointBackgroundColor: "rgba("+RGBs[c].r+","+RGBs[c].g+","+RGBs[c].b+",1)",
			pointBackgroundColor: "#" + hex,
			// the border color of the points
			pointBorderColor: "#fff",
			// the radius of the points (in pixel)
			pointRadius: 4,
			// the fill color of the points when hovered
			pointHoverBackgroundColor: "#fff",
			// the border color of the points when hovered
			// pointHoverBorderColor: "rgba("+RGBs[c].r+","+RGBs[c].g+","+RGBs[c].b+",1)",
			pointHoverBorderColor: "#" + hex,
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
						if (valueTypeAxisY == 'earning') {
							// do not display decimal values on Y axis
							return currency.format(value, 0);
						}

						// return reservations count
						return value;
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

					if (valueTypeAxisY == 'earning') {
						// format value as currency
						label += currency.format(tooltipItem.value);
					} else {
						// use reservations count
						label += tooltipItem.value;
					}

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

<?php if (count($this->pieChartStat)) { ?>

	// PIE CHARTS

	<?php foreach ($this->pieChartStat as $id_group => $groupStat) { ?>
	
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
		
		<?php foreach ($groupStat['list'] as $dataset) { ?> 
			var c = pie_data.labels.length % RGBs.length;
			// use service color if set, otherwise use random one
			var hex = RGBs[c];

			pie_data.datasets[0].data.push(parseFloat('<?php echo $dataset[$chart_value_primary]; ?>'));
			pie_data.datasets[0].backgroundColor.push('#' + hex);
			pie_data.labels.push('<?php echo addslashes($dataset['name']); ?>');
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

						if (valueTypeAxisY == 'earning') {
							// format value as currency
							label += currency.format(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]);
						} else {
							// use reservations count
							label += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
						}

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
		var pie_ctx = document.getElementById('vap-piechart-<?php echo $id_group; ?>').getContext('2d');

		// display PIE chart
		var myPieChart = new Chart(pie_ctx, {
			type: 'doughnut',
			data: pie_data,
			options: pie_options,
		});

	<?php } ?>
	
<?php } ?>
	
</script>
