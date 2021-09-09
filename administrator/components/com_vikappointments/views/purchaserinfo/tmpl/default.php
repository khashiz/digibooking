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

$rows = $this->rows;
$btns = $this->btns;

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$vik = UIApplication::getInstance();

for ($i = 0; $i < count($rows); $i++)
{	
	$row = $rows[$i];
	
	VikAppointments::setCurrentTimezone($row['ord_timezone']);

	$in_date  = ArasJoomlaVikApp::jdate($date_format, $row['checkin_ts']);
	$in_time  = ArasJoomlaVikApp::jdate($time_format, $row['checkin_ts']);
	$out_time = ArasJoomlaVikApp::jdate($time_format, VikAppointments::getCheckout($row['checkin_ts'], $row['duration']));
	
	$charge_str = "";
	if ($row['payment_charge'] != 0)
	{
		$charge_str = ' (' . ($row['payment_charge'] > 0 ? '+' : '') . VikAppointments::printPriceCurrencySymb($row['payment_charge']) . ')';
	}
	
	$coupon = array();
	if (!empty($row['coupon_str']))
	{
		$coupon = explode(';;', $row['coupon_str']);
	}

	$custom_f = json_decode($row['custom_f'], true);
	
	?>

	<?php if ($btns) { ?>
			
		<div class="btn-toolbar" style="height: 32px;">
			<div class="btn-group pull-right">
				<button type="button" class="btn" onClick="vapEditButtonPressed(<?php echo $row['id']; ?>);">
					<?php echo JText::_('VAPMANAGERESERVATION22'); ?>
				</button>
				<button type="button" class="btn btn-danger" onClick="vapRemoveButtonPressed(<?php echo $row['id']; ?>);">
					<?php echo JText::_('VAPMANAGERESERVATION23'); ?>
				</button>
			</div>
		</div>

	<?php } ?>

	<div>

		<div class="span7">
			<?php echo $vik->openEmptyFieldset(); ?>
			
				<div class="vap-orderbasket-badge <?php echo ($row['people'] == 1 ? 'large' : ''); ?>">
					<?php echo JText::sprintf('VAPSAYRESCHECKINDETAILS', $in_date, $in_time, $out_time); ?>
				</div>

				<?php if ($row['people'] > 1) { ?>
					<div class="vap-orderbasket-badge"><?php echo "x" . $row['people'] . " " . JText::_('VAPMANAGERESERVATION25'); ?></div>
				<?php } ?>
				
				<div class="vap-orderbasket-badge"><?php echo $row['sname']; ?></div>
				<div class="vap-orderbasket-badge"><?php echo $row['ename']; ?></div>

				<?php if (strlen($row['payment_name'])) { ?>
					<div class="vap-orderbasket-badge">
					   <?php echo $row['payment_name'] . $charge_str; ?> 
					</div>
				<?php } ?>

				<div class="vap-orderbasket-badge <?php echo (strlen($row['payment_name']) == 0 ? 'large' : ''); ?>">
					<?php echo VikAppointments::printPriceCurrencySymb($row['total_cost']); ?>
				</div>
				
				<div class="vap-orderbasket-badge large <?php echo strtolower($row['status']); ?>">
					<?php echo JText::_('VAPSTATUS' . strtoupper($row['status'])); ?>
				</div>
				
				<?php if (count($coupon)) { ?>
					<div class="vap-orderbasket-badge large">
						<?php echo $coupon[0] . ' : ' . ($coupon[1] == 1 ? $coupon[2] . '%' : VikAppointments::printPriceCurrencySymb($coupon[2]) ); ?>
					</div>
				<?php } ?>
				
			<?php echo $vik->closeEmptyFieldset(); ?>
		</div>

		<div class="span5">
			<?php echo $vik->openEmptyFieldset(); ?>
			
				<?php
				foreach ($custom_f as $key => $val)
				{
					if (!empty($val))
					{
						echo $vik->openControl(JText::_($key) . ":");

						if (strlen($val) <= 40)
						{
							/**
							 * Prevent XSS attacks by escaping submitted data.
							 *
							 * @since 1.6.3
							 */
							?>
							<input type="text" value="<?php echo $this->escape($val); ?>" readonly size="32" />
							<?php
						}
						else
						{
							?>
							<textarea readonly cols="40" rows="3"><?php echo $val; ?></textarea>
							<?php
						}
						
						echo $vik->closeControl();
					}
				}
				?>
			
			<?php echo $vik->closeEmptyFieldset(); ?>
		</div>

		<?php if (count($row['options'])) { ?>
			<div class="span12">
				<?php echo $vik->openEmptyFieldset(); ?>
				
					<?php foreach ($row['options'] as $opt) { ?>
						<div class="vap-orderbasket-option">
							<div class="vap-orderbasket-option-details">
								<div class="vap-orderbasket-option-details-left">
									<span class="vap-orderbasket-option-details-name"><?php echo $opt['name'].(strlen($opt['var_name']) ? " - ".$opt['var_name'] : ""); ?></span>
								</div>
								<div class="vap-orderbasket-option-details-right">
									<span class="vap-orderbasket-option-details-quantity">x<?php echo $opt['quantity']; ?></span>
									<span class="vap-orderbasket-option-details-price">
										<?php if ($opt['price'] != 0)
										{
											echo VikAppointments::printPriceCurrencySymb($opt['price']);
										}
										?>
									</span>
								</div>
							</div>
						</div>
					<?php } ?>
					
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>
		<?php } ?>

		<?php if (strlen($row['notes'])) { ?>
			<div class="span12">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control-group">
						<div class="vap-orderbasket-genericnotes"><?php echo $row['notes']; ?></div>
					</div>
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>      
		<?php } ?>

		<?php if (strlen($row['log'])) { ?>
			<div class="span12">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control-group">
						<?php echo $vik->getCodeMirror('log', $row['log']); ?>
					</div>
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>      
		<?php } ?>

		<?php if ($i < count($rows) - 1) { ?>
			<div class="span12" style="width: 97%;">
				<hr style="border: 1px dashed #bbb;">
			</div>
		<?php } ?>

	</div> 
	
<?php } ?>

<?php
JText::script('VAPRESERVATIONREMOVEMESSAGE');
?>

<script>

	var _fromTask = '<?php echo ($this->from ? $this->from : 'findreservation'); ?>';

	function vapEditButtonPressed(id) {
		var url = 'index.php?option=com_vikappointments&task=editreservation&from='+_fromTask+'&cid[]='+id;

		window.parent.location.href = url;
	}
	
	function vapRemoveButtonPressed(id) {

		var r = confirm(Joomla.JText._('VAPRESERVATIONREMOVEMESSAGE'));

		if (r) {
			var url = 'index.php?option=com_vikappointments&task=deleteReservations&from='+_fromTask+'&cid[]='+id;

			window.parent.location.href = url;
		}
	}
	
</script>
