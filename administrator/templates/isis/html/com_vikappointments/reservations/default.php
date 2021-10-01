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

$config = UIFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$nowdf = $vik->jdateFormat($date_format);

/**
 * Prepares CodeMirror editor scripts for being used
 * via Javascript/AJAX.
 *
 * @wponly
 */
$vik->prepareEditor('codemirror');

// ORDERING LINKS

foreach (array('r.id', 'r.checkin_ts', 'r.purchaser_nominative', 'r.purchaser_mail', 'r.purchaser_phone', 'r.total_cost', 'r.paid', 'r.status', 'e.nickname', 's.name') as $c)
{
	if (empty($ordering[$c]))
	{
		$ordering[$c] = 0;
	}
}

$links = array(
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION1'), 'r.id', $ordering['r.id'], 1, $filters, 'vapheadcolactive'.(($ordering['r.id'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION5'), 'r.checkin_ts', $ordering['r.checkin_ts'], 1,$filters, 'vapheadcolactive'.(($ordering['r.checkin_ts'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION8'), 'r.purchaser_mail', $ordering['r.purchaser_mail'], 1, $filters, 'vapheadcolactive'.(($ordering['r.purchaser_mail'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION9'), 'r.total_cost', $ordering['r.total_cost'], 1, $filters, 'vapheadcolactive'.(($ordering['r.total_cost'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION11'), 'r.paid', $ordering['r.paid'], 1, $filters, 'vapheadcolactive'.(($ordering['r.paid'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION12'), 'r.status', $ordering['r.status'], 1, $filters, 'vapheadcolactive'.(($ordering['r.status'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION3'), 'e.nickname', $ordering['e.nickname'], 1, $filters, 'vapheadcolactive'.(($ordering['e.nickname'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION4'), 's.name', $ordering['s.name'], 1, $filters, 'vapheadcolactive'.(($ordering['s.name'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION27'), 'r.purchaser_phone', $ordering['r.purchaser_phone'], 1, $filters, 'vapheadcolactive'.(($ordering['r.purchaser_phone'] == 2) ? 1 : 2)),
	OrderingManager::getLinkColumnOrder('reservations', JText::_('VAPMANAGERESERVATION32'), 'r.purchaser_nominative', $ordering['r.purchaser_nominative'], 1, $filters, 'vapheadcolactive'.(($ordering['r.purchaser_nominative'] == 2) ? 1 : 2)),
);

$cart_enabled 	 = VikAppointments::isCartEnabled();
$listable_fields = VikAppointments::getListableFields();

$all_list_fields = array('id', 'sid', 'payment', 'checkin_ts', 'checkout', 'employee', 'service', 'people', 'nominative', 'mail', 'phone', 'info', 'coupon', 'total', 'paid', 'invoice', 'status');
$all_fields = array();
foreach ($all_list_fields as $f)
{
	$all_fields[$f] = in_array($f, $listable_fields);
}

$listable_cf = (array) $config->getJSON('listablecf', array());

$multi_orders_cols = 0;

$core_edit = JFactory::getUser()->authorise('core.edit', 'com_vikappointments');

$created_by_default = JText::_('VAPRESLISTGUEST');

$default_tz = date_default_timezone_get();

$is_searching = $this->hasFilters();

$is_disabled = $filters['res_id'] > 0 ? 'disabled="disabled"' : '';

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">
	
	<div class="btn-toolbar" hidden>

		<div class="btn-group pull-left input-append">
			<input type="text" name="keysearch" id="vapkeysearch" class="vapkeysearch" size="32" <?php echo $is_disabled; ?>
				value="<?php echo $filters['keysearch']; ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />

			<button type="submit" class="btn">
				<i class="icon-search"></i>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn<?php echo ($is_searching ? ' btn-primary' : ''); ?><?php echo ($filters['res_id'] > 0 ? ' disabled' : ''); ?>"
				<?php echo $is_disabled; ?> onclick="vapToggleSearchToolsButton(this);">
				<?php echo JText::_('JSEARCH_TOOLS'); ?>&nbsp;<i class="fa fa-caret-<?php echo ($is_searching ? 'up' : 'down'); ?>" id="vap-tools-caret"></i>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::_($filters['res_id'] > 0 ? 'JTOOLBAR_BACK' : 'JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>

		<?php if ($filters['res_id'] > 0 && count($rows) > 0) { ?>

			<div class="btn-group pull-right">
				<button type="button" class="btn btn-primary" onClick="document.location.href='index.php?option=com_vikappointments&task=notifyCustomer&ordnum=<?php echo $filters['res_id']; ?>';">
					<?php echo JText::_('VAPMANAGERESERVATION31'); ?>
				</button>
			</div>

			<?php if (strlen($rows[0]['cc_data'])) { ?>

				<div class="btn-group pull-right">
					<button type="button" class="btn btn-primary" onclick="vapOpenJModal('ccdetails', null, true); return false;">
						<i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;<?php echo JText::_('VAPSEECCDETAILS'); ?>
					</button>
				</div>

			<?php } ?>

		<?php } ?>

	</div>

	<?php
	/**
	 * Display search tools only in case we are not focusing a single order.
	 *
	 * @since 1.6.3
	 */
	if ($filters['res_id'] <= 0)
	{
		?>
		<div class="btn-toolbar" id="vap-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

			<?php
			$options = array();
			$options[] = JHtml::_('select.option', '', 'VAPFILTERSELECTSTATUS');
			$options[] = JHtml::_('select.option', 'CONFIRMED', 'VAPSTATUSCONFIRMED');
			$options[] = JHtml::_('select.option', 'PENDING', 'VAPSTATUSPENDING');
			$options[] = JHtml::_('select.option', 'REMOVED', 'VAPSTATUSREMOVED');
			$options[] = JHtml::_('select.option', 'CANCELED', 'VAPSTATUSCANCELED');
			$options[] = JHtml::_('select.option', 'CLOSURE', 'VAPSTATUSCLOSURE');
			?>
			<div class="btn-group pull-left">
				<select name="status" id="vap-status-sel" class="<?php echo (!empty($filters['status']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();" <?php echo $is_disabled; ?>>
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['status'], true); ?>
				</select>
			</div>

			<?php
			/**
			 * Display payments filter only in case of non-empty list.
			 *
			 * @since 1.6.3
			 */
			if (count($this->payments))
			{
				$options = array();
				$options[] = JHtml::_('select.option', 0, 'VAPFILTERSELECTPAYMENT');
				foreach ($this->payments as $p)
				{
					$options[] = JHtml::_('select.option', $p['id'], $p['name']);
				}
				?>
				<div class="btn-group pull-left">
					<select name="id_payment" id="vap-payment-sel" class="<?php echo (!empty($filters['id_payment']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();" <?php echo $is_disabled; ?>>
						<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['id_payment'], true); ?>
					</select>
				</div>
				<?php
			}
			?>

			<?php
			$options = array();
			$options[] = JHtml::_('select.option', -1, 'VAPFILTERSELECTTYPE');
			$options[] = JHtml::_('select.option', 1, 'VAPMANAGERESERVATION11');
			$options[] = JHtml::_('select.option', 0, 'VAPMANAGERESERVATION40');
			?>
			<div class="btn-group pull-left">
				<select name="type" id="vap-type-sel" class="<?php echo ($filters['type'] != -1 ? 'active' : ''); ?>" onchange="document.adminForm.submit();" <?php echo $is_disabled; ?>>
					<?php echo JHtml::_('select.options', $options, 'value', 'text', $filters['type'], true); ?>
				</select>
			</div>

			<div class="btn-group pull-left">
				<?php echo $vik->calendar($filters['datefilter'], 'datefilter', 'vapdatefilter', $nowdf, array('class'=>'vapdatefilter', 'onChange' => 'document.adminForm.submit();')); ?>
			</div>

		</div>
		<?php
	}
	?>
	
<?php if (count($rows) == 0) { ?>
	
	<p><?php echo JText::_('VAPNORESERVATION');?></p>

<?php } else { ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>
				<th width="20">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>
				<?php
				if ($all_fields['id'])
				{
					?><th class="<?php echo $vik->getAdminThClass('left'); ?>" width="70" style="text-align: center;"><?php echo $links[0]; ?></th><?php
				}
				if ($all_fields['sid'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION2');?></th><?php
				}
				if ($all_fields['payment'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION13');?></th><?php
				}
				if ($all_fields['checkin_ts'])
				{
					$multi_orders_cols++;
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[1]; ?></th><?php
				}
				if ($all_fields['checkout'])
				{
					$multi_orders_cols++;
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="60" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION6');?></th><?php
				}
				if ($all_fields['people'])
				{
					$multi_orders_cols++;
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION25');?></th><?php
				}
				if ($all_fields['employee'])
				{
					$multi_orders_cols++;
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[6]; ?></th><?php
				}
				if ($all_fields['service'])
				{
					$multi_orders_cols++;
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[7]; ?></th><?php
				}
				if ($all_fields['info'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION20');?></th><?php
				}
				if ($all_fields['coupon'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION21');?></th><?php
				}
				if ($all_fields['nominative'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[9]; ?></th><?php
				}
				if ($all_fields['mail'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[2]; ?></th><?php
				}
				if ($all_fields['phone'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[8]; ?></th><?php
				}

				/**
				 * Here's the custom fields that should be shown within the head of the table.
				 */
				foreach ($this->customFields as $field)
				{
					if (in_array($field['name'], $listable_cf))
					{
						?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo JText::_($field['name']); ?></th><?php
					}
				}

				if ($all_fields['total'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="80" style="text-align: center;"><?php echo $links[3]; ?></th><?php
				}
				if ($all_fields['paid'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="50" style="text-align: center;"><?php echo $links[4]; ?></th><?php
				}
				if ($all_fields['invoice'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="75" style="text-align: center;"><?php echo JText::_('VAPMANAGERESERVATION35');?></th><?php
				}
				if ($all_fields['status'])
				{
					?><th class="<?php echo $vik->getAdminThClass(); ?>" width="100" style="text-align: center;"><?php echo $links[5]; ?></th><?php
				}
				?>
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			
			if (empty($row['timezone']))
			{
				$row['timezone'] = $default_tz;
			}

			VikAppointments::setCurrentTimezone($row['timezone']);

			$tz_str = '';
			if ($config->getBool('multitimezone'))
			{
				$tz_str = str_replace('_', ' ', date_default_timezone_get());
			}
			
			$ASSOC_STATUS = array('CONFIRMED' => 0, 'REMOVED' => 1, 'PENDING' => 2);
			
			$edit_link = 'index.php?option=com_vikappointments&task=editreservation&cid[]='.$row['id'];
			
			if ($row['id_parent'] == -1 && $filters['res_id'] <= 0)
			{
				$edit_link = 'index.php?option=com_vikappointments&task=reservations&res_id='.$row['id'];
			}
			
			$coupon_str = '';
			$coupon = array();
			
			if(strlen($row['coupon_str']) > 0 && ($row['id_parent'] == -1 || $row['id_parent'] == $row['id']))
			{
				list($code, $type, $amount) = explode(';;', $row['coupon_str']);
				$tcost_no_coupon = $row['total_cost'];
				
				if ($type == 2)
				{
					$tcost_no_coupon += $amount;
				}
				else if ($amount < 100)
				{
					$tcost_no_coupon = $tcost_no_coupon / (1 - ($amount / 100.0));
				}
				else
				{
					$tcost_no_coupon = 0;
				}
				
				$coupon_str = $code . ' = ' . VikAppointments::printPriceCurrencySymb($tcost_no_coupon) . ' - [' . ($type == 2 ? VikAppointments::printPriceCurrencySymb($amount) : $amount . ' %') . ']';

				$coupon = array('code' => $code, 'type' => $type, 'amount' => $amount);
			}

			$oid_tooltip = "";
			
			if ($row['createdon'] != -1)
			{
				if ($row['createdby'] != -1)
				{
					$created_by = $row['createdby_name'];
				}
				else
				{
					$created_by = $created_by_default;
				}

				$oid_tooltip = JText::sprintf('VAPRESLISTCREATEDTIP', ArasJoomlaVikApp::jdate($date_format . " " . $time_format, $row['createdon']), $created_by);
			}

			if ($row['closure'])
			{
				$row['status'] = 'CLOSURE';
			}

			$cf_json = (array) json_decode($row['custom_f'], true);
			
			?>
			<tr class="row<?php echo ($i % 2); ?>">
				
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<?php if ($all_fields['id']) { ?>
				<td style="text-align: center;">
					<span class="vapresidlistspــ" title="<?php echo $oid_tooltip; ?>"><?php echo 100000+$row['id']; ?></span>
					<?php if ($row['id'] == $row['id_parent']) { ?>
						<?php if ($cart_enabled) { ?>
							<span class="vappackagesp" title="<?php echo JText::_('VAPMANAGERESERVATION30'); ?>"></span>
						<?php } ?>
					<?php } else if ($row['closure'] != 1) { ?>
						<span class="vapchainsp vapchainicon <?php echo 'roword'.($row['id_parent'] != -1 ? $row['id_parent'] : $row['id']); ?>"></span>
					<?php } ?>
				</td>
				<?php } ?>
				
				<?php if ($all_fields['sid']) { ?>

					<td style="text-align: center;">
						<?php
						if ($row['closure'] == 0)
						{
							?>
							<a href="<?php echo $edit_link; ?>"><?php echo $row['sid']; ?></a>
							<?php
						}
						else
						{
							echo '&nbsp;';
						}
						?>
					</td>

				<?php } ?>
				
				<?php if ($all_fields['payment']) { ?>
					<td style="text-align: center;"><?php echo (!empty($row['payname']) ? $row['payname'] : ''); ?></td>
				<?php } ?>
				
				<?php if ($row['id_parent'] != -1) { ?>
					
					<?php if ($all_fields['checkin_ts']) {
						$checkin = ArasJoomlaVikApp::jdate($date_format . ' ' . $time_format, $row['checkin_ts']);
						?>
						<td style="text-align: center;">
							<span title="<?php echo $tz_str; ?>" class="vaptz">
								<?php if (!$all_fields['sid'])
								{
									// the order key is hidden, attach the edit link to the checkin field
									?><a href="<?php echo $edit_link; ?>"><?php echo $checkin; ?></a><?php
								}
								else
								{
									echo $checkin;
								}
								?>
							</span>
						</td>
					<?php } ?>
					
					<?php if ($all_fields['checkout']) { ?>
						<td style="text-align: center;">
							<?php echo ArasJoomlaVikApp::jdate($time_format, VikAppointments::getCheckout($row['checkin_ts'], $row['duration'])); ?>
						</td>
					<?php } ?>
					
					<?php if ($all_fields['people']) { ?>
						<td style="text-align: center;"><?php echo $row['people']; ?></td>
					<?php } ?>
					
					<?php if ($all_fields['employee']) { ?>
						<td style="text-align: center;"><?php echo JText::sprintf('FLOOR'.$row['ename']); ?></td>
					<?php } ?>
					
					<?php if ($all_fields['service']) { ?>
						<td style="text-align: center;"><?php echo JText::sprintf($row['sname']); ?></td>
				   <?php } ?>

				<?php } else if ($row['closure']) { ?>

					<?php if ($all_fields['checkin_ts']) { ?>
						<td style="text-align: center;">
							<span title="<?php echo $tz_str; ?>" class="vaptz">
								<?php echo ArasJoomlaVikApp::jdate($date_format . ' ' . $time_format, $row['checkin_ts']); ?>
							</span>
						</td>
					<?php } ?>
					
					<?php if ($all_fields['checkout']) { ?>
						<td style="text-align: center;">
							<?php echo ArasJoomlaVikApp::jdate($time_format, VikAppointments::getCheckout($row['checkin_ts'], $row['duration'])); ?>
						</td>
					<?php } ?>
					
					<?php if ($all_fields['people']) { ?>
						<td style="text-align: center;">&nbsp;</td>
					<?php } ?>
					
					<?php if ($all_fields['employee']) { ?>
						<td style="text-align: center;"><?php echo $row['ename']; ?></td>
					<?php } ?>
					
					<?php if ($all_fields['service']) { ?>
						<td style="text-align: center;">&nbsp;</td>
				   <?php } ?>

				<?php } else if ($multi_orders_cols > 0) { ?>

					<td colspan="<?php echo $multi_orders_cols; ?>" style="text-align: center;">
						<a href="<?php echo $edit_link; ?>">
							<span class="vaporderparentbox" id="<?php echo $row['id']; ?>"><?php echo JText::_('VAPMANAGERESERVATION29'); ?></span>
						</a>
					</td>

				<?php } ?>
				
				<?php if ($all_fields['info']) { ?>

					<td style="text-align: center;">
						<?php if ($row['id_parent'] != -1) { ?>
							<a href="javascript: void(0);" onclick="SELECTED_ORDER=<?php echo $row['id']; ?>;vapOpenJModal('respinfo', null, true); return false;">
								<img src="<?php echo VAPASSETS_ADMIN_URI . "images/info_icon.png"; ?>"/>
							</a>
						<?php } else if ($row['closure'] == 0) { ?>
							<a href="index.php?option=com_vikappointments&task=reservations&res_id=<?php echo $row['id']; ?>">
								<img src="<?php echo VAPASSETS_ADMIN_URI . "images/info_icon.png"; ?>"/>
							</a>
						<?php } else { ?>
							<i class="fa fa-ban big vapreservationstatusclosure" style="font-size: 24px;"></i>
						<?php } ?>
					</td>

				<?php } ?>
				
				<?php if ($all_fields['coupon']) { ?>
					<td style="text-align: center;" title="<?php echo (count($coupon) > 0 ? $coupon['code'] : ''); ?>">
						<?php if (count($coupon) > 0) { 
							echo ($coupon['type'] == 2 ? VikAppointments::printPriceCurrencySymb($coupon['amount']) : $coupon['amount'] . '%');    
						} ?> 
					</td>
				<?php } ?>
				
				<?php if ($all_fields['nominative']) { ?>
					<td style="text-align: center;">
						<?php if ($row['id_user'] > 0) { ?>
							<a href="javascript: void(0);" onclick="SELECTED_CUSTOMER=<?php echo $row['id_user']; ?>;vapOpenJModal('custinfo', null, true); return false;">
								<?php echo $row['purchaser_nominative']; ?>
							</a>
						<?php } else { ?>
							<?php echo $row['purchaser_nominative']; ?>
						<?php } ?>
					</td>
				<?php } ?>

				<?php if ($all_fields['mail']) { ?>
					<td style="text-align: center;"><?php echo $row['purchaser_mail']; ?></td>
				<?php } ?>

				<?php if ($all_fields['phone']) { ?>
					<td style="text-align: center;">
						<?php if (!empty($row['purchaser_phone'])) { ?>
							<?php echo (!empty($row['purchaser_prefix']) ? $row['purchaser_prefix'] . ' ' : '') . $row['purchaser_phone']; ?>
						<?php } ?>
					</td>
				<?php } ?>

				<?php
				/**
				 * Here's the custom fields that should be shown within the body of the table.
				 */
				foreach ($this->customFields as $field)
				{
					if (in_array($field['name'], $listable_cf))
					{
						?>
						<td style="text-align: center;">
							<?php echo isset($cf_json[$field['name']]) ? $cf_json[$field['name']] : ''; ?>
						</td>
						<?php
					}
				}
				?>
				
				<?php if ($all_fields['total']) { ?>
					<td style="text-align: center;" class="<?php echo (($row['id_parent'] == -1 && $row['total_cost'] > $row['tot_paid'] && $core_edit) ? 'vaptcostcoltext' : ''); ?>" title="<?php echo $coupon_str; ?>">
						
						<?php
						if ($row['closure'] == 0)
						{
							?>
							<span id="tcost<?php echo $row['id']; ?>">
								<?php echo VikAppointments::printPriceCurrencySymb($row['total_cost']); ?>
								<input type="hidden" id="tcost<?php echo $row['id']; ?>hidden" value="<?php echo $row['total_cost']; ?>"/>
							</span>
							
							<?php
							if ($row['tot_paid'] > 0)
							{
								echo ' (' . VikAppointments::printPriceCurrencySymb($row['tot_paid']) . ')';
							}
						}
						else
						{
							echo '&nbsp';
						}
						?>

					</td>
				<?php } ?>
				
				<?php if ($all_fields['paid']) { ?>
					<td style="text-align: center;">

						<?php
						if ($row['closure'] == 0)
						{
							?>
							<a href="index.php?option=com_vikappointments&task=changeReservationPaidColumn&val=<?php echo $row['paid']; ?>&id_res=<?php echo $row['id']; ?>&return_task=reservations">
								<?php echo intval($row['paid']) == 1 ? "<img src=\"".VAPASSETS_ADMIN_URI . "images/ok.png\"/>" : "<img src=\"".VAPASSETS_ADMIN_URI . "images/no.png\"/>"; ?>
							</a>
							<?php
						}
						else
						{
							echo '&nbsp;';
						}
						?>
						
					</td>
				<?php } ?>
				
				<?php if ($all_fields['invoice']) {
					$id_invoice = $row['id'];
					if ($row['id_parent'] != -1 && $row['id'] != $row['id_parent'])
					{
						$id_invoice = $row['id_parent'];
					}

					$is = file_exists(VAPINVOICE . DIRECTORY_SEPARATOR . $id_invoice . '-' . $row['sid'] . '.pdf');
					?>
					<td style="text-align: center;">
						<?php if ($is) { ?>
							<a href="<?php echo VAPINVOICE_URI . $id_invoice . '-' . $row['sid'] . '.pdf'; ?>" target="_blank">
								<img src="<?php echo VAPASSETS_ADMIN_URI . "images/invoice.png"; ?>"/>
							</a>
						<?php } ?>
					</td>
				<?php } ?>
				
				<?php if ($all_fields['status']) { ?>
					<td style="text-align: center;" class="vapreservationstatus<?php echo strtolower($row['status']); ?>">
						<?php if ($core_edit && $row['closure'] == 0) { ?>
							<a href="index.php?option=com_vikappointments&task=changeReservationStatusColumn&s_index=<?php echo $ASSOC_STATUS[$row['status']]; ?>&id_res=<?php echo $row['id']; ?>">
								<?php echo JText::_('VAPSTATUS' . $row['status']); ?>
							</a>
						<?php } else { ?>
							<?php echo JText::_('VAPSTATUS' . $row['status']); ?>
						<?php } ?>
					</td>
				<?php } ?>
			</tr>
			<?php
		}

		// always restore timezone once the list is finished
		VikAppointments::setCurrentTimezone($default_tz);
		?>

	</table>

<?php } ?>

	<?php
	// invoice modal
	echo JHtml::_(
		'bootstrap.renderModal',
		'jmodal-invoice',
		array(
			'title'       => JText::_('VAPINVOICESTITLE'),
			'closeButton' => true,
			'bodyHeight'  => 60,
			'footer'	  => '<button type="button" class="btn btn-success" onClick="Joomla.submitform(\'generateInvoices\', document.adminForm);">
				<i class="icon-file"></i>&nbsp;' . JText::_('VAPMANAGEINVOICE6') . '
			</button>
			<button type="button" class="btn" onClick="' . $vik->bootDismissModalJS('#jmodal-invoice') . '">' . JText::_('VAPCANCEL') . '</button>'
		),
		$this->loadTemplate('invoice')
	);
	?>
	
	<input type="hidden" name="from" value="reservations" />

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="reservations" />
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
			'url'		  => 'index.php?option=com_vikappointments&task=ccdetails&tmpl=component&id=' . $rows[0]['id'],
		)
	);
}

// reservation modal
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-respinfo',
	array(
		'title'       => JText::_('VAPMANAGERESERVATIONTITLE1'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);

// customer modal
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-custinfo',
	array(
		'title'       => JText::_('VAPMANAGECUSTOMER21'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<div id="dialog-confirm" title="<?php echo JText::_('VAPRESEDITCOSTTITLE');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-pencil" style="float: left; margin: 0 7px 20px 0;"></span>
		<span>
			<?php echo JText::_('VAPRESEDITCOSTMESSAGE'); ?><br />
			<input type="text" id="dialog-confirm-input" />&nbsp;<?php echo $config->get('currencysymb'); ?>
		</span>
	</p>
</div>

<script>

	var last_row_id = -1;

	jQuery(document).ready(function() {
		
		jQuery('.vapresidlistsp, .vaptz').tooltip();
		
		<?php if ($core_edit) { ?>
			jQuery('.vaptcostcoltext').click(function(){
				openTotalCostDialog(jQuery(this).find('span').attr('id'));
			});
		<?php } ?>
	});

	function openTotalCostDialog(id) {
		jQuery("#dialog-confirm-input").val(jQuery('#'+id+'hidden').val());
		var real_id = id.split('tcost');
		real_id = real_id[1];
		
		jQuery("#dialog-confirm").dialog({
			resizable: false,
			height: 180,
			modal: true,
			buttons: {
				"<?php echo JText::_('VAPSAVE'); ?>": function() {
					jQuery(this).dialog("close");
					storeTotalCost(real_id, jQuery("#dialog-confirm-input").val());
				},
				"<?php echo JText::_('VAPCANCEL'); ?>": function() {
					jQuery(this).dialog("close");
				}
			}
		});
	}

	function storeTotalCost(id, tcost) {	
		
		jQuery.noConflict();
			
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=store_total_cost&tmpl=component",
			data: {
				id: id,
				total_cost: tcost
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp);
			
			if (obj[0]) {
				jQuery('#tcost'+id).html(Currency.getInstance().format(obj[1]) + '\n<input type="hidden" id="tcost'+id+'hidden" value="'+obj[1]+'" />');
			} else {
				alert(obj[1]);
			}
		});
	}
		
	function clearFilters() {
		jQuery('#vapkeysearch').val('');
		jQuery('#vapdatefilter').val('');
		jQuery('#vap-status-sel').updateChosen('');
		jQuery('#vap-payment-sel').updateChosen(0);
		jQuery('#vap-type-sel').updateChosen(-1);

		jQuery('#adminForm').append('<input type="hidden" name="res_id" value="0" />');
		
		document.adminForm.submit();
	}

	// modal

	SELECTED_ORDER = -1;
	SELECTED_CUSTOMER = -1;

	jQuery(document).ready(function(){

		VikRenderer.chosen('.btn-toolbar');
		VikRenderer.chosen('#jmodal-invoice');
		
	});

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'respinfo') {
			url = 'index.php?option=com_vikappointments&task=purchaserinfo&tmpl=component&oid[]=' + SELECTED_ORDER;
		} else if (id == 'custinfo') {
			url = 'index.php?option=com_vikappointments&task=customerinfo&tmpl=component&id=' + SELECTED_CUSTOMER;
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

	Joomla.submitbutton = function(task) {
		if (task == 'generateInvoices') {
			vapOpenJModal('invoice', null, true);
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}

</script>
