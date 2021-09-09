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

//JHtml::_('behavior.calendar');
ArasJoomlaVikApp::datePicker();
$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$session = JFactory::getSession();
$_selected_tab_view = $session->get('vaptabactive', 1, 'vapconfigcldays');

$date_format = $config->get('dateformat');

$df_joomla = $vik->jdateFormat($date_format);

// get closing days
$closing_days = VikAppointments::getClosingDays();

// fetch closing days to evaluate the label
foreach ($closing_days as &$day)
{
	if ($day['freq'] == 1)
	{
		// weekly
		$day['label'] = JText::_('VAPDAY' . strtoupper(ArasJoomlaVikApp::jdate('D', $day['ts'])));
	}
	else
	{
		$day['label'] = JText::_('VAPFREQUENCYTYPE' . $day['freq']);
	}
}

// get closing periods
$closing_periods = VikAppointments::getClosingPeriods();

$titles = array(
	JText::_('VAPMANAGECONFIG11'),
	JText::_('VAPMANAGECONFIG28'),
); 
?>
<div id="navigation">
	<ul>
		<?php for ($i = 1; $i <= 2; $i++) { ?>
			<li id="vaptabli<?php echo $i; ?>" class="vaptabli<?php echo (($_selected_tab_view == $i) ? ' vapconfigtabactive' : ''); ?>"><a href="javascript: changeTabView(<?php echo $i; ?>);"><?php echo $titles[$i-1]; ?></a></li>
		<?php } ?>
	</ul>
</div>

<?php
// print config search bar
UILoader::import('libraries.widget.layout');
echo UIWidgetLayout::getInstance('searchbar')->display();
?>

<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data">
	
	<!-- CLOSING DAYS SECTION -->
	
	<div id="vaptabview1" class="vaptabview" style="<?php echo (($_selected_tab_view != 1) ? 'display: none;' : ''); ?>">
		
		<!-- Closing Days Fieldset -->
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPMANAGECONFIG11'); ?></legend>
			<table class="admintable table" cellspacing="1">
				<?php
				/**
				 * 0: SINGLE DAY
				 * 1: WEEK
				 * 2: MONTH
				 * 3: YEAR
				 */
				$elements = array();
				for ($i = 0; $i <= 3; $i++)
				{
					$elements[] = JHtml::_('select.option', $i, JText::_('VAPFREQUENCYTYPE' . $i));
				}

				$services = array();
				foreach ($this->services as $id => $name)
				{
					$services[] = JHtml::_('select.option', $id, $name);
				}
				?>
				<tr>
					<td style="vertical-align: top;" width="200" class="adminparamcol"> <b><?php echo JText::_('VAPMANAGECONFIG11');?></b> </td>
					<td>
						<div class="btn-toolbar" style="height:auto;">
							<div class="btn-group pull-left">
								<?php echo $vik->calendar('', 'vapday', 'vapday'); ?>
							</div>
							<div class="btn-group pull-left" style="font-size: 13px;">
								<select id="vapfrequency">
									<?php echo JHtml::_('select.options', $elements); ?>
								</select>
							</div>
							<div class="btn-group pull-left" style="font-size: 13px;">
								<select id="vapservices" multiple>
									<?php echo JHtml::_('select.options', $services); ?>
								</select>
							</div>
							<div class="btn-group pull-left">
								<button type="button" onClick="addClosingDay();" class="btn"><?php echo JText::_('VAPMANAGECONFIG12'); ?></button>
							</div>
						</div>
						<br/ clear="all">
						<div id="vapclosingdayscont">
							<?php
							$days_cont = 0;
							foreach ($closing_days as $cd)
							{
								if (is_array($cd['services']))
								{
									$slist = $this->services;

									$s_names = implode(', ', array_map(function($s) use ($slist)
									{
										return $slist[$s];
									}, $cd['services']));
								}
								else
								{
									$s_names = JText::_('VAPALLSERVICES');
								}

								?>
								<div id="vapcdrow<?php echo $days_cont; ?>" style="margin-bottom: 5px;">
									<span>
										<input type="text" value="<?php echo $cd['date']; ?> (<?php echo $cd['label']; ?>) - <?php echo $s_names; ?>" size="48" readonly />
										<a href="javascript: void(0);" class="input-align" onClick="removeClosingDay(<?php echo $days_cont; ?>);">
											<i class="fa fa-times big" style="margin-left: 5px;"></i>
										</a>
									</span>
								</div>
								<input id="vapcdhidden<?php echo $days_cont; ?>" name="closing_days[]"
									type="hidden" value="<?php echo $cd['date'] . ':' . $cd['freq'] . ':' . implode(',', (array) $cd['services']); ?>" />
								<?php 
								$days_cont++;
							}
							?>
						</div>
					</td>
				</tr>
			</table>
		</fieldset>
		
	</div>

	<div id="vaptabview2" class="vaptabview" style="<?php echo (($_selected_tab_view != 2) ? 'display: none;' : ''); ?>">
		
		<!-- Closing Periods Fieldset -->
		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPMANAGECONFIG28'); ?></legend>
			<table class="admintable table" cellspacing="1">
				<tr>
					<td style="vertical-align: top;" width="200" class="adminparamcol"> <b><?php echo JText::_('VAPMANAGECONFIG28');?></b> </td>
					<td>
						<div class="btn-toolbar" style="height: auto;">
							<div class="btn-group pull-left">
								<?php echo $vik->calendar('', 'vapstartperiod', 'vapstartperiod'); ?>
							</div>
							<div class="btn-group pull-left">
								<?php echo $vik->calendar('', 'vapendperiod', 'vapendperiod'); ?>
							</div>
							<div class="btn-group pull-left" style="font-size: 13px;">
								<select id="vapperiodservices" multiple>
									<?php echo JHtml::_('select.options', $services); ?>
								</select>
							</div>
							<div class="btn-group pull-left">
								<button type="button" onClick="addClosingPeriod();" class="btn"><?php echo JText::_('VAPMANAGECONFIG29'); ?></button>
							</div>
						</div>
						<br/ clear="all">
						<div id="vapclosingperiodscont">
							<?php 
							$periods_cont = 0;
							foreach ($closing_periods as $cp)
							{
								if (is_array($cp['services']))
								{
									$slist = $this->services;
									
									$s_names = implode(', ', array_map(function($s) use ($slist)
									{
										return $slist[$s];
									}, $cp['services']));
								}
								else
								{
									$s_names = JText::_('VAPALLSERVICES');
								}

								?>
								<div id="vapcprow<?php echo $periods_cont; ?>" style="margin-bottom: 5px;">
									<span>
										<input type="text" value="<?php echo $cp['datestart']; ?>" readonly />
										&nbsp;-&nbsp;
										<input type="text" value="<?php echo $cp['dateend']; ?>" readonly />
										&nbsp;-&nbsp;
										<input type="text" value="<?php echo $s_names; ?>" readonly />
										<a href="javascript: void(0);" class="input-align" onClick="removeClosingPeriod(<?php echo $periods_cont; ?>)">
											<i class="fa fa-times big" style="margin-left: 5px;"></i>
										</a>
									</span>
								</div>
								<input id="vapcphidden<?php echo $periods_cont; ?>"
									name="closing_periods[]" type="hidden" value="<?php echo $cp['datestart'] . ';;' . $cp['dateend'] . ';;' . implode(',', (array) $cp['services']); ?>" />
								<?php 
								$periods_cont++;
							} 
							?>
						</div>
					</td>
				</tr>
			</table>
		</fieldset>

	</div>
	
	<input type="hidden" name="task" value="" id="vapconfigtask"/>
	<input type="hidden" name="option" value="com_vikappointments"/>
</form>

<?php
JText::script('VAPMANAGECOUPON19');
JText::script('VAPALLSERVICES');
?>

<script>

	var daysCont = <?php echo $days_cont; ?>;
	var periodsCont = <?php echo $periods_cont; ?>;

	var _DAYS = new Array();
	<?php $_D = ArasJoomlaVikApp::weekDays(); //array( 'SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT' ); ?>
	<?php for( $i = 0; $i < 7; $i++ ) { ?>
		_DAYS[<?php echo $i; ?>] = '<?php echo JText::_($_D[$i]); ?>';
	<?php } ?>
	
	jQuery(document).ready(function(){

		jQuery('#vapendperiod').focus(function(){
			if (jQuery('#vapendperiod').val().length == 0) {
				jQuery('#vapendperiod').val(jQuery('#vapstartperiod').val());
			}
		});
		
		jQuery('#vapendperiod_img').click(function(){
			if (jQuery('#vapendperiod').val().length == 0) {
				jQuery('#vapendperiod').val(jQuery('#vapstartperiod').val());
			}
		});

		jQuery('#vapfrequency').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 150
		});

		jQuery('#vapservices, #vapperiodservices').select2({
			placeholder: Joomla.JText._('VAPMANAGECOUPON19'),
			allowClear: true,
			width: 300
		});
		
	});

	function putClosingDay(day, f_id, f_val, services) {
		var s_ids   = Object.keys(services);
		var s_names = Object.values(services);

		s_ids   = s_ids.length   ? s_ids.join(',') : '*';
		s_names = s_names.length ? s_names.join(', ') : Joomla.JText._('VAPALLSERVICES');

		jQuery('#vapclosingdayscont').append(
			'<div id="vapcdrow' + daysCont + '" style="margin-bottom: 5px;">\n'+
				'<span>\n'+
					'<input type="text" value="' + day + ' (' + f_val + ') - ' + s_names + '" size="48" readonly />\n'+
					'<a href="javascript: void(0);" class="input-align" onClick="removeClosingDay(' + daysCont + ')">\n'+
						'<i class="fa fa-times big" style="margin-left: 5px;"></i>\n'+
					'</a>\n'+
				'</span>\n'+
			'</div>\n'
		);

		jQuery('#adminForm').append('<input id="vapcdhidden' + daysCont + '" name="closing_days[]" type="hidden" value="' + day + ':' + f_id + ':' + s_ids + '" />');
	}
	
	function addClosingDay() {
		var day = jQuery('#vapday').val();
		if (day.length > 0) {
			var f_id = jQuery('#vapfrequency').val();
			var f_tx = jQuery('#vapfrequency option:selected').text();
			
			if (f_id == 1) { // WEEKLY
				f_tx = _DAYS[getDate(day).getDay()];    
			}

			var services = {};

			jQuery('#vapservices option:checked').each(function() {
				services[jQuery(this).val()] = jQuery(this).text();
			});
			
			putClosingDay(day, f_id, f_tx, services);
			
			jQuery('#vapday').val(day);
			
			daysCont++;
		}
	}
	
	function putClosingPeriod(start, end, services) {
		var s_ids   = Object.keys(services);
		var s_names = Object.values(services);

		s_ids   = s_ids.length   ? s_ids.join(',') : '*';
		s_names = s_names.length ? s_names.join(', ') : Joomla.JText._('VAPALLSERVICES');

		jQuery('#vapclosingperiodscont').append(
			'<div id="vapcprow' + periodsCont + '" style="margin-bottom: 10px;">\n'+
				'<span>\n'+
					'<input type="text" value="' + start + '" readonly />\n'+
					'&nbsp;-&nbsp;\n'+
					'<input type="text" value="' + end + '" readonly />\n'+
					'&nbsp;-&nbsp;\n'+
					'<input type="text" value="' + s_names + '" readonly />\n'+
					'<a href="javascript: void(0);" class="input-align" onClick="removeClosingPeriod(' + periodsCont + ')">\n'+
						'<i class="fa fa-times big" style="margin-left: 5px;"></i>\n'+
					'</a>\n'+
				'</span>\n'+
			'</div>\n'
		);

		jQuery('#adminForm').append('<input id="vapcphidden' + periodsCont + '" name="closing_periods[]" type="hidden" value="' + start + ';;' + end + ';;' + s_ids + '" />');
	}
	
	function addClosingPeriod() {
		var day_start = jQuery('#vapstartperiod').val();
		var day_end = jQuery('#vapendperiod').val();
		
		if (day_start.length > 0 && day_end.length > 0) {
			var services = {};

			jQuery('#vapperiodservices option:checked').each(function() {
				services[jQuery(this).val()] = jQuery(this).text();
			});

			putClosingPeriod(day_start, day_end, services);
			periodsCont++;
			
			jQuery('#vapstartperiod').val('');
			jQuery('#vapendperiod').val('');
		}
	}
	
	function removeClosingDay(index) {
		jQuery('#vapcdrow'+index).remove();
		jQuery('#vapcdhidden'+index).remove();
	}
	
	function removeClosingPeriod(index) {
		jQuery('#vapcprow'+index).remove();
		jQuery('#vapcphidden'+index).remove();
	}
	
	function getDate(day) {
		var formats = '<?php echo $date_format; ?>'.split('<?php echo $date_format[1]; ?>');
		var date_exp = day.split('<?php echo $date_format[1]; ?>');
		
		var _args = new Array();
		for (var i = 0; i < formats.length; i++) {
			_args[formats[i]] = parseInt( date_exp[i] );
		}
		
		return new Date( _args['Y'], _args['m']-1, _args['d'] );
	}
	
	// TAB LISTENER
	
	var last_tab_view = <?php echo $_selected_tab_view; ?>;
	
	function changeTabView(tab_pressed) {
		if (tab_pressed != last_tab_view) {
			jQuery('.vaptabli').removeClass('vapconfigtabactive');
			jQuery('#vaptabli'+tab_pressed).addClass('vapconfigtabactive');
			
			jQuery('.vaptabview').hide();
			jQuery('#vaptabview'+tab_pressed).fadeIn('fast');
			
			storeTabSelected(tab_pressed);
			
			last_tab_view = tab_pressed;
		}
	}
	
	function storeTabSelected(tab) {
		jQuery.noConflict();
			
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=store_tab_selected&tmpl=component&group=vapconfigcldays",
			data: { tab: tab }
		}).done(function(resp){
			
		}).fail(function(resp){
			
		});
	}
	
</script>
