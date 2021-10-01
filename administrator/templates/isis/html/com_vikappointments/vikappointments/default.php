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

// load tooltip behavior
JHtml::_('behavior.keepalive');

$config = UIFactory::getConfig();

$curr_symb  = $config->get('currencysymb');
$symb_pos   = $config->get('currsymbpos');

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');
$dt_format   = $date_format . " " . $time_format;

$last_ids = array(0, 0, 0, 0);
if (count($this->latestReservations))
{
	$last_ids[0] = $this->latestReservations[0]['id'];
}
if (count($this->latestWaiting))
{
	$last_ids[1] = $this->latestWaiting[0]['id'];
}
if (count($this->latestCustomers))
{
	$last_ids[2] = $this->latestCustomers[0]['id'];
}
if (count($this->latestPackages))
{
	$last_ids[3] = $this->latestPackages[0]['id'];
}

$default_tz = date_default_timezone_get();

$session = JFactory::getSession();

// dashboard properties
$dashboard_properties = $session->get('dashboard-properties', '', 'vap');
if (empty($dashboard_properties))
{
	$dashboard_properties = array('appointments' => 1, 'waiting' => 1, 'customers' => 1, 'packages' => 1);
}

$now = ArasJoomlaVikApp::jgetdate();

?>

<?php
if ($this->isTmpl)
{
	ob_start();
}
?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

	<!-- FIRST BOXES : RESERVATIONS - WAITING LIST -->

	<div class="vap-dashboard-box-wrapper">

		<!-- RESERVATIONS -->
		
		<?php $app_active_tab = $dashboard_properties['appointments']; ?>
		
		<div class="vap-dashboard-box <?php echo ($this->waitingListEnabled ? 'big' : 'full'); ?>">
			<div class="vapdash-title"><i class="fa fa-shopping-basket"></i>&nbsp;<?php echo JText::_('VAPMENURESERVATIONS'); ?></div>

			<div class="vapdash-container">
				<div class="vapdash-tab-head" style="display: none">
					<div class="vapdash-tab-button appointments-tab">
						<a href="javascript: void(0);" onClick="switchAppointmentsDashboardTab(1, this);" class="<?php echo ($app_active_tab == 1 ? 'active' : ''); ?>">
							<?php echo JText::_('VAPDASHLATESTRESERVATIONS'); ?>
						</a>
					</div>
					<div class="vapdash-tab-button appointments-tab">
						<a href="javascript: void(0);" onClick="switchAppointmentsDashboardTab(2, this);" class="<?php echo ($app_active_tab == 2 ? 'active' : ''); ?>">
							<?php echo JText::_('VAPDASHINCOMINGRESERVATIONS'); ?>
						</a>
					</div>
				</div>
				
				<table id="vapdash-appointments-list1" class="vap-incoming-table appointments-list listener" style="<?php echo ($app_active_tab != 1 ? 'display:none;' : ''); ?>">
					<th class="vapdashtabtitle" width="10%" style="text-align: left;"><?php echo JText::_('VAPMANAGERESERVATION0'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION26'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION4'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION3'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION38'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION37'); ?></th>
					<th class="vapdashtabtitle" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION12'); ?></th>
					
					<?php foreach ($this->latestReservations as $r)
					{ 
						if (empty($r['timezone']))
						{
							$r['timezone'] = $default_tz;
						}

						VikAppointments::setCurrentTimezone($r['timezone']);
						?>
						<tr class="<?php echo ( ( $this->isTmpl && $this->ajaxParams['from'][0] < $r['id'] ) ? 'vapdashrowhighlight' : ''); ?>" data-identifier="<?php echo $r['id']; ?>">
							<td><?php echo $r['id']; ?> - <a href="index.php?option=com_vikappointments&task=printorders&tmpl=component&cid[]=<?php echo $r['id']; ?>" target="_blank"><i class="fa fa-print"></i></a></td>
							<td style="text-align: center;"><a href="index.php?option=com_vikappointments&task=editreservation&cid[]=<?php echo $r['id']; ?>" target="_blank"><?php echo ArasJoomlaVikApp::jdate( $dt_format, $r['checkin_ts'] ); ?></a></td>
							<td style="text-align: center;"><?php echo $r['sname']; ?></td>
							<td style="text-align: center;"><?php echo $r['ename']; ?></td>
							<td style="text-align: center;">
								<?php if( strlen($r['purchaser_mail']) ) { ?>
									<a href="mailto:<?php echo $r['purchaser_mail']; ?>"><?php echo (strlen($r['purchaser_nominative']) ? $r['purchaser_nominative'] : $r['purchaser_mail']); ?></a>
								<?php } else if( strlen($r['purchaser_nominative']) ) { 
									echo $r['purchaser_nominative'];
								} ?>
							</td>
							<td style="text-align: center;"><?php echo ($r['createdon'] != -1 ? VikAppointments::formatTimestamp($dt_format, $r['createdon']) : ''); ?></td>
							<td style="text-align: center;" class="<?php echo 'vapreservationstatus'.strtolower($r['status']); ?>"><?php echo JText::_('VAPSTATUS'.$r['status']); ?></td>
						</tr>
					<?php } ?>
				</table>
				
				<table id="vapdash-appointments-list2" class="vap-incoming-table appointments-list listener" style="<?php echo ($app_active_tab != 2 ? 'display:none;' : ''); ?>">
					<th class="vapdashtabtitle" width="10%" style="text-align: left;"><?php echo JText::_('VAPMANAGERESERVATION0'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION26'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION4'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION3'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION38'); ?></th>
					<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION37'); ?></th>
					<th class="vapdashtabtitle" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION12'); ?></th>
					
					<?php foreach ($this->incomingReservations as $r)
					{
						if (empty($r['timezone']))
						{
							$r['timezone'] = $default_tz;
						}

						VikAppointments::setCurrentTimezone($r['timezone']);
						?>
						<tr class="<?php echo ( ( $this->isTmpl && $this->ajaxParams['from'][0] < $r['id'] ) ? 'vapdashrowhighlight' : ''); ?>" data-identifier="<?php echo $r['id']; ?>">
							<td><?php echo $r['id']; ?> - <a href="index.php?option=com_vikappointments&task=printorders&tmpl=component&cid[]=<?php echo $r['id']; ?>" target="_blank"><i class="fa fa-print"></i></a></td>
							<td style="text-align: center;"><a href="index.php?option=com_vikappointments&task=editreservation&cid[]=<?php echo $r['id']; ?>" target="_blank">
								<?php echo VikAppointments::formatCheckinTimestamp( $dt_format, $time_format, $r['checkin_ts'] ); ?>
							</a></td>
							<td style="text-align: center;"><?php echo $r['sname']; ?></td>
							<td style="text-align: center;"><?php echo $r['ename']; ?></td>
							<td style="text-align: center;">
								<?php if( strlen($r['purchaser_mail']) ) { ?>
									<a href="mailto:<?php echo $r['purchaser_mail']; ?>"><?php echo (strlen($r['purchaser_nominative']) ? $r['purchaser_nominative'] : $r['purchaser_mail']); ?></a>
								<?php } else if( strlen($r['purchaser_nominative']) ) { 
									echo $r['purchaser_nominative'];
								} ?>
							</td>
							<td style="text-align: center;"><?php echo ($r['createdon'] != -1 ? VikAppointments::formatTimestamp($dt_format, $r['createdon']) : ''); ?></td>
							<td style="text-align: center;" class="<?php echo 'vapreservationstatus'.strtolower($r['status']); ?>"><?php echo JText::_('VAPSTATUS'.$r['status']); ?></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>

		<!-- WAITING LIST -->

		<?php if ($this->waitingListEnabled) { ?>
		
			<?php $wait_active_tab = $dashboard_properties['waiting']; ?>
			
			<div class="vap-dashboard-box small">
				<div class="vapdash-title"><i class="fa fa-hourglass"></i>&nbsp;<?php echo JText::_('VAPCONFIGGLOBTITLE14'); ?></div>

				<div class="vapdash-container">
					<div class="vapdash-tab-head">
						<div class="vapdash-tab-button waiting-tab">
							<a href="javascript: void(0);" onClick="switchWaitingDashboardTab(1, this);" class="<?php echo ($wait_active_tab == 1 ? 'active' : ''); ?>">
								<?php echo JText::_('VAPDASHLATESTRESERVATIONS'); ?>
							</a>
						</div>
						<div class="vapdash-tab-button waiting-tab">
							<a href="javascript: void(0);" onClick="switchWaitingDashboardTab(2, this);" class="<?php echo ($wait_active_tab == 2 ? 'active' : ''); ?>">
								<?php echo JText::_('VAPDASHINCOMINGRESERVATIONS'); ?>
							</a>
						</div>
					</div>
					
					<table id="vapdash-waiting-list1" class="vap-incoming-table waiting-list listener" style="<?php echo ($wait_active_tab != 1 ? 'display:none;' : ''); ?>">
						<th class="vapdashtabtitle" width="20%" style="text-align: left;"><?php echo JText::_('VAPMANAGEWAITLIST7'); ?></th>
						<th class="vapdashtabtitle" width="30%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST1'); ?></th>
						<th class="vapdashtabtitle" width="30%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST2'); ?></th>
						<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST4'); ?></th>
						
						<?php foreach ($this->latestWaiting as $w)
						{ 
							if (empty($w['timezone']))
							{
								$w['timezone'] = $default_tz;
							}

							VikAppointments::setCurrentTimezone($w['timezone']);
							?>
							<tr class="<?php echo ( ( $this->isTmpl && $this->ajaxParams['from'][1] < $w['id'] ) ? 'vapdashrowhighlight' : ''); ?>" data-identifier="<?php echo $w['id']; ?>">
								<td><i class="fa fa-calendar vap-icon-tooltip" title="<?php echo ArasJoomlaVikApp::jdate($date_format, $w['timestamp']); ?>"></i>&nbsp;<?php echo VikAppointments::formatTimestamp($date_format, $w['created_on']); ?></td>
								<td style="text-align: center;"><?php echo $w['sname']; ?></td>
								<td style="text-align: center;"><?php echo $w['ename']; ?></td>
								<td style="text-align: center;">
									<?php if( strlen($w['email']) ) { ?>
										<a href="mailto:<?php echo $w['email']; ?>"><i class="fa fa-envelope vap-icon-tooltip" title="<?php echo $w['email']; ?>"></i></a>
									<?php } ?>
									<?php if( strlen($w['phone_number']) ) { ?>
										<i class="fa fa-phone vap-icon-tooltip" title="<?php echo $w['phone_prefix']." ".$w['phone_number']; ?>"></i>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</table>
					
					<table id="vapdash-waiting-list2" class="vap-incoming-table waiting-list listener" style="<?php echo ($wait_active_tab != 2 ? 'display:none;' : ''); ?>">
						<th class="vapdashtabtitle" width="20%" style="text-align: left;"><?php echo JText::_('VAPMANAGEWAITLIST3'); ?></th>
						<th class="vapdashtabtitle" width="30%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST1'); ?></th>
						<th class="vapdashtabtitle" width="30%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST2'); ?></th>
						<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGEWAITLIST4'); ?></th>
						
						<?php foreach ($this->incomingWaiting as $w)
						{
							if (empty($w['timezone']))
							{
								$w['timezone'] = $default_tz;
							}

							VikAppointments::setCurrentTimezone($w['timezone']);
							?>
							<tr class="<?php echo ( ( $this->isTmpl && $this->ajaxParams['from'][1] < $w['id'] ) ? 'vapdashrowhighlight' : ''); ?>" data-identifier="<?php echo $w['id']; ?>">
								<td><?php 
									if( ArasJoomlaVikApp::jmktime(0, 0, 0, $now['mon'], $now['mday'], $now['year']) == $w['timestamp'] ) {
										echo JText::_('VAPTODAY');
									} else if( ArasJoomlaVikApp::jmktime(0, 0, 0, $now['mon'], $now['mday']+1, $now['year']) == $w['timestamp'] ) {
										echo JText::_('VAPTOMORROW');
									} else {
										echo ArasJoomlaVikApp::jdate($date_format, $w['timestamp']); 
									}
								?></td>
								<td style="text-align: center;"><?php echo $w['sname']; ?></td>
								<td style="text-align: center;"><?php echo $w['ename']; ?></td>
								<td style="text-align: center;">
									<?php if( strlen($w['email']) ) { ?>
										<a href="mailto:<?php echo $w['email']; ?>"><i class="fa fa-envelope vap-icon-tooltip" title="<?php echo $w['email']; ?>"></i></a>
									<?php } ?>
									<?php if( strlen($w['phone_number']) ) { ?>
										<i class="fa fa-phone vap-icon-tooltip" title="<?php echo $w['phone_prefix']." ".$w['phone_number']; ?>"></i>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>

		<?php } ?>

	</div>

	<!-- SECOND BOXES : CUSTOMERS -->

	<div class="vap-dashboard-box-wrapper" style="display: none">

		<!-- CUSTOMERS -->
		
		<?php $cust_active_tab = $dashboard_properties['customers']; ?>
		
		<div class="vap-dashboard-box <?php echo ($this->packagesEnabled ? 'small' : 'full'); ?>">
			<div class="vapdash-title"><i class="fa fa-user"></i>&nbsp;<?php echo JText::_('VAPMENUCUSTOMERS'); ?></div>

			<div class="vapdash-container">
				<div class="vapdash-tab-head">
					<div class="vapdash-tab-button customers-tab">
						<a href="javascript: void(0);" onClick="switchCustomersDashboardTab(1, this);" class="<?php echo ($cust_active_tab == 1 ? 'active' : ''); ?>">
							<?php echo JText::_('VAPDASHLATESTCUSTOMERS'); ?>
						</a>
					</div>
					<div class="vapdash-tab-button customers-tab">
						<a href="javascript: void(0);" onClick="switchCustomersDashboardTab(2, this);" class="<?php echo ($cust_active_tab == 2 ? 'active' : ''); ?>">
							<?php echo JText::_('VAPDASHLOGGEDCUSTOMERS'); ?>
						</a>
					</div>
				</div>
				
				<table id="vapdash-customers-list1" class="vap-incoming-table customers-list listener" style="<?php echo ($cust_active_tab != 1 ? 'display:none;' : ''); ?>">
					<th class="vapdashtabtitle" width="10%" style="text-align: left;"><?php echo JText::_('VAPMANAGECUSTOMER1'); ?></th>
					<th class="vapdashtabtitle" width="30%" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER2'); ?></th>
					<th class="vapdashtabtitle" width="40%" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER3'); ?></th>
					<th class="vapdashtabtitle" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER5'); ?></th>
					
					<?php foreach ($this->latestCustomers as $c) { ?>
						<tr class="<?php echo ( ( $this->isTmpl && $this->ajaxParams['from'][2] < $c['id'] ) ? 'vapdashrowhighlight' : ''); ?>" data-identifier="<?php echo $c['id']; ?>">
							<td><?php echo ($c['jid'] > 0 ? '#'.$c['jid'] : ''); ?></td>
							<td style="text-align: center;"><a href="index.php?option=com_vikappointments&task=editcustomer&cid[]=<?php echo $c['id']; ?>" target="_blank">
								<?php echo $c['billing_name']; ?>
							</a></td>
							<td style="text-align: center;"><a href="mailto:<?php echo $c['billing_mail']; ?>">
								<?php echo $c['billing_mail']; ?>
							</a></td>
							<td style="text-align: center;">
								<?php if( !empty($c['country_code']) ) { ?>
									<img src="<?php echo VAPASSETS_URI . 'css/flags/'.strtolower($c['country_code']).'.png'; ?>" />
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</table>

				<table id="vapdash-customers-list2" class="vap-incoming-table customers-list listener" style="<?php echo ($cust_active_tab != 2 ? 'display:none;' : ''); ?>">
					<th class="vapdashtabtitle" width="10%" style="text-align: left;"><?php echo JText::_('VAPMANAGECUSTOMER1'); ?></th>
					<th class="vapdashtabtitle" width="30%" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER2'); ?></th>
					<th class="vapdashtabtitle" width="40%" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER3'); ?></th>
					<th class="vapdashtabtitle" width="10%" style="text-align: center;"><?php echo JText::_('VAPMANAGECUSTOMER5'); ?></th>
					
					<?php foreach ($this->loggedCustomers as $c) { ?>
						<tr class="<?php echo ( ( $this->isTmpl && $this->ajaxParams['from'][2] < $c['id'] ) ? 'vapdashrowhighlight' : ''); ?>" data-identifier="<?php echo $c['id']; ?>">
							<td><?php echo ($c['jid'] > 0 ? '#'.$c['jid'] : ''); ?></td>
							<td style="text-align: center;"><a href="index.php?option=com_vikappointments&amp;task=editcustomer&amp;cid[]=<?php echo $c['id']; ?>" target="_blank">
								<?php echo $c['billing_name']; ?>
							</a></td>
							<td style="text-align: center;"><a href="mailto:<?php echo $c['billing_mail']; ?>">
								<?php echo $c['billing_mail']; ?>
							</a></td>
							<td style="text-align: center;">
								<?php if( !empty($c['country_code']) ) { ?>
									<img src="<?php echo VAPASSETS_URI . 'css/flags/'.strtolower($c['country_code']).'.png'; ?>" />
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</table>

			</div>
		</div>

		<!-- PACKAGES -->

		<?php if ($this->packagesEnabled) { ?>
		
			<?php $pack_active_tab = $dashboard_properties['packages']; ?>
			
			<div class="vap-dashboard-box big">
				<div class="vapdash-title"><i class="fa fa-gift"></i>&nbsp;<?php echo JText::_('VAPMENUPACKORDERS'); ?></div>

				<div class="vapdash-container">
					<div class="vapdash-tab-head">
						<div class="vapdash-tab-button packages-tab">
							<a href="javascript: void(0);" onClick="switchPackagesDashboardTab(1, this);" class="<?php echo ($pack_active_tab == 1 ? 'active' : ''); ?>">
								<?php echo JText::_('VAPDASHLATESTBOOKEDPACKAGES'); ?>
							</a>
						</div>
						<div class="vapdash-tab-button packages-tab">
							<a href="javascript: void(0);" onClick="switchPackagesDashboardTab(2, this);" class="<?php echo ($pack_active_tab == 2 ? 'active' : ''); ?>">
								<?php echo JText::_('VAPDASHLATESTUSEDPACKAGES'); ?>
							</a>
						</div>
					</div>
					
					<table id="vapdash-packages-list1" class="vap-incoming-table packages-list listener" style="<?php echo ($pack_active_tab != 1 ? 'display:none;' : ''); ?>">
						<th class="vapdashtabtitle" width="10%" style="text-align: left;"><?php echo JText::_('VAPMANAGEPACKORDER1'); ?></th>
						<th class="vapdashtabtitle" width="25%" style="text-align: center;"><?php echo JText::_('VAPMANAGEPACKORDER9'); ?></th>
						<th class="vapdashtabtitle" width="30%" style="text-align: center;"><?php echo JText::_('VAPMANAGEPACKORDER6'); ?></th>
						<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGEPACKORDER5'); ?></th>
						<th class="vapdashtabtitle" width="15%" style="text-align: center;"><?php echo JText::_('VAPMANAGEPACKORDER4'); ?></th>
						
						<?php foreach ($this->latestPackages as $p) { ?>
							<tr class="<?php echo ( ( $this->isTmpl && $this->ajaxParams['from'][3] < $p['id'] ) ? 'vapdashrowhighlight' : ''); ?>" data-identifier="<?php echo $p['id']; ?>">
								<td><?php echo $p['id']; ?></td>
								<td style="text-align: center;">
									<?php echo VikAppointments::formatTimestamp($dt_format, $p['createdon']); ?>
								</td>
								<td style="text-align: center;">
									<?php if( strlen($p['purchaser_mail']) ) { ?>
										<a href="mailto:<?php echo $p['purchaser_mail']; ?>"><?php echo (strlen($p['purchaser_nominative']) ? $p['purchaser_nominative'] : $p['purchaser_mail']); ?></a>
									<?php } else if( strlen($p['purchaser_nominative']) ) { 
										echo $p['purchaser_nominative'];
									} ?>
								</td>
								<td style="text-align: center;">
									<?php echo VikAppointments::printPriceCurrencySymb($p['total_cost'], $curr_symb, $symb_pos, true); ?>
								</td>
								<td style="text-align: center;" class="<?php echo 'vapreservationstatus'.strtolower($p['status']); ?>">
									<?php echo JText::_('VAPSTATUS'.$p['status']); ?>
								</td>
							</tr>
						<?php } ?>
					</table>

					<table id="vapdash-packages-list2" class="vap-incoming-table packages-list listener" style="<?php echo ($pack_active_tab != 2 ? 'display:none;' : ''); ?>">
						<th class="vapdashtabtitle" width="10%" style="text-align: left;"><?php echo JText::_('VAPMANAGEPACKORDER1'); ?></th>
						<th class="vapdashtabtitle" width="20%" style="text-align: center;"><?php echo JText::_('VAPMANAGEPACKORDER17'); ?></th>
						<th class="vapdashtabtitle" width="25%" style="text-align: center;"><?php echo JText::_('VAPMANAGEPACKORDER18'); ?></th>
						<th class="vapdashtabtitle" width="25%" style="text-align: center;"><?php echo JText::_('VAPMANAGEPACKORDER6'); ?></th>
						<th class="vapdashtabtitle" width="12%" style="text-align: center;"><?php echo JText::_('VAPMANAGEPACKORDER16'); ?></th>
						
						<?php foreach ($this->usedPackages as $p) { ?>
							<tr class="<?php echo ( ( $this->isTmpl && $this->ajaxParams['from'][3] < $p['id'] ) ? 'vapdashrowhighlight' : ''); ?>" data-identifier="<?php echo $p['id']; ?>">
								<td><?php echo $p['id']; ?></td>
								<td style="text-align: center;">
									<?php echo VikAppointments::formatTimestamp($dt_format, $p['modifiedon']); ?>
								</td>
								<td style="text-align: center;">
									<?php echo $p['package_name']; ?>
								</td>
								<td style="text-align: center;">
									<?php if( strlen($p['purchaser_mail']) ) { ?>
										<a href="mailto:<?php echo $p['purchaser_mail']; ?>"><?php echo (strlen($p['purchaser_nominative']) ? $p['purchaser_nominative'] : $p['purchaser_mail']); ?></a>
									<?php } else if( strlen($p['purchaser_nominative']) ) { 
										echo $p['purchaser_nominative'];
									} ?>
								</td>
								<td style="text-align: center;">
									<?php echo $p['used_app']."/".$p['num_app']; ?>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>

		<?php } ?>

	</div>

	<?php if ($this->isTmpl) { ?>
		<script>

			_LAST_ID_ = <?php echo json_encode($last_ids); ?>;
			if( 
				_LAST_ID_[0] > <?php echo $this->ajaxParams['last'][0]; ?> || 
				_LAST_ID_[1] > <?php echo $this->ajaxParams['last'][1]; ?> || 
				_LAST_ID_[2] > <?php echo $this->ajaxParams['last'][2]; ?> ||
				_LAST_ID_[3] > <?php echo $this->ajaxParams['last'][3]; ?> 
			) {
				playNotificationSound();
			}

		</script>
	<?php } ?>
	
	<input type="hidden" name="option" value="com_vikappointments"
	<input type="hidden" name="task" value="vikappointments">
</form>

<?php if (!$this->isTmpl) { ?>

<audio preload="true" id="vap-notification-audio">
	<source src="<?php echo VAPASSETS_ADMIN_URI . 'audio/notification.mp3'; ?>" type="audio/mpeg" />
</audio>

<script>
	
	var _LAST_ID_ = <?php echo json_encode($last_ids); ?>;
	var _FROM_ID_ = <?php echo json_encode($last_ids); ?>;
	
	jQuery(document).ready(function(){
		setInterval("refreshDashboard()", <?php echo (VikAppointments::getNotificationRefreshTime(true)*1000); ?>);

		makeTooltip();
	});
	
	function refreshDashboard() {
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=vikappointments&tmpl=component",
			data: { 
				last_id: _LAST_ID_[0],
				from_id: _FROM_ID_[0],

				last_wait_id: _LAST_ID_[1],
				from_wait_id: _FROM_ID_[1],

				last_cust_id: _LAST_ID_[2],
				from_cust_id: _FROM_ID_[2],

				last_pack_id: _LAST_ID_[3],
				from_pack_id: _FROM_ID_[3],
			}
		}).done(function(resp) {

			var resp = jQuery.parseJSON(resp)[0];

			jQuery('#adminForm').replaceWith(resp);

			// reservations
			jQuery('.vap-incoming-table.appointments-list.listener tr').on('click', function(){
				jQuery(this).removeClass('vapdashrowhighlight');
				var row_id = parseInt(jQuery(this).attr('data-identifier'));
				if( row_id > _FROM_ID_[0] ) {
					_FROM_ID_[0] = row_id;
				}
			});

			// waiting list
			jQuery('.vap-incoming-table.waiting-list.listener tr').on('click', function(){
				jQuery(this).removeClass('vapdashrowhighlight');
				var row_id = parseInt(jQuery(this).attr('data-identifier'));
				if( row_id > _FROM_ID_[1] ) {
					_FROM_ID_[1] = row_id;
				}
			});

			// customers
			jQuery('.vap-incoming-table.customers-list.listener tr').on('click', function(){
				jQuery(this).removeClass('vapdashrowhighlight');
				var row_id = parseInt(jQuery(this).attr('data-identifier'));
				if( row_id > _FROM_ID_[2] ) {
					_FROM_ID_[2] = row_id;
				}
			});

			// packages
			jQuery('.vap-incoming-table.packages-list.listener tr').on('click', function(){
				jQuery(this).removeClass('vapdashrowhighlight');
				var row_id = parseInt(jQuery(this).attr('data-identifier'));
				if( row_id > _FROM_ID_[3] ) {
					_FROM_ID_[3] = row_id;
				}
			});

			makeTooltip();
		});
	}
	
	function playNotificationSound() {
		document.getElementById('vap-notification-audio').play();
	}

	function storeDashboardProperties() {
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=store_dashboard_properties&tmpl=component",
			data: {
				a_page: DASHBOARD_PROPERTIES.appointments, 
				w_page: DASHBOARD_PROPERTIES.waiting, 
				c_page: DASHBOARD_PROPERTIES.customers,
				p_page: DASHBOARD_PROPERTIES.packages
			}
		}).done(function(resp){
			
		}).fail(function(resp){
			
		});
	}
	
	var DASHBOARD_PROPERTIES = <?php echo json_encode($dashboard_properties); ?>;

	function switchAppointmentsDashboardTab(page, elem) {
		jQuery('.vapdash-tab-button.appointments-tab a').removeClass("active");
		jQuery(elem).addClass('active');
		
		jQuery('.vap-incoming-table.appointments-list').hide();
		jQuery('#vapdash-appointments-list'+page).show();
		
		DASHBOARD_PROPERTIES.appointments = page;
		storeDashboardProperties();
	}

	function switchWaitingDashboardTab(page, elem) {
		jQuery('.vapdash-tab-button.waiting-tab a').removeClass("active");
		jQuery(elem).addClass('active');
		
		jQuery('.vap-incoming-table.waiting-list').hide();
		jQuery('#vapdash-waiting-list'+page).show();
		
		DASHBOARD_PROPERTIES.waiting = page;
		storeDashboardProperties();
	}

	function switchCustomersDashboardTab(page, elem) {
		jQuery('.vapdash-tab-button.customers-tab a').removeClass("active");
		jQuery(elem).addClass('active');
		
		jQuery('.vap-incoming-table.customers-list').hide();
		jQuery('#vapdash-customers-list'+page).show();
		
		DASHBOARD_PROPERTIES.customers = page;
		storeDashboardProperties();
	}

	function switchPackagesDashboardTab(page, elem) {
		jQuery('.vapdash-tab-button.packages-tab a').removeClass("active");
		jQuery(elem).addClass('active');
		
		jQuery('.vap-incoming-table.packages-list').hide();
		jQuery('#vapdash-packages-list'+page).show();
		
		DASHBOARD_PROPERTIES.packages = page;
		storeDashboardProperties();
	}

	function makeTooltip() {
		jQuery('.vap-icon-tooltip').tooltip();
	}
		
</script>

<?php } else {
	$content = ob_get_contents();
	ob_end_clean();

	echo json_encode(array($content));

	exit;
} ?> 
