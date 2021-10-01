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

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * VikAppointments Component Controller
 */
class VikAppointmentsController extends JControllerUI
{
	/**
	 * Typical view method for MVC based architecture.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types.
	 *
	 * @return  void
	 */
	function display($cachable = false, $urlparams = false)
	{
		$input = JFactory::getApplication()->input;

		$view = strtolower($input->get('view', ''));

		switch ($view)
		{
			case 'confirmapp':
				$this->confirmapp();
				break;

			case 'packagesconfirm':
				$this->packagesconfirm();
				break;

			case 'employeeslist':
			case 'serviceslist':
			case 'servicesearch':
			case 'employeesearch':
			case 'order':
			case 'allorders':
			case 'packages':
			case 'packorders':
			case 'packagesorder':
			case 'userprofile':
			case 'pushwl':
				// do nothing
				break;

			case 'unsubscr_waiting_list':
				$input->set('view', 'unsubscrwl');
				break;

			case 'empaccountstat':
			case 'empattachser':
			case 'empcoupons':
			case 'empcustfields':
			case 'empeditcoupon':
			case 'empeditcustfield':
			case 'empeditlocation':
			case 'empeditpay':
			case 'empeditprofile':
			case 'empeditservice':
			case 'empeditserwdays':
			case 'empeditwdays':
			case 'emplocations':
			case 'emplocwdays':
			case 'emplogin':
			case 'empmanres':
			case 'emppaylist':
			case 'empserviceslist':
			case 'empsettings':
			case 'empsubscr':
			case 'empsubscrorder':
			case 'empwdays':
				UIApplication::getInstance()->loadEmployeeAreaAssets();
				break;

			default:
				$input->set('view', 'serviceslist');
		}

		parent::display();
	}
	
	/**
	 * Echoes the current software identifier.
	 * The key 'e4jmg' must be set in the REQUEST.
	 *
	 * @return 	void
	 */
	function get_version()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$config = UIFactory::getConfig();

		$key = $input->getString('key', '');
		if (strcmp($key, 'e4jmg'))
		{
			die ('You are not authorised to view this resource!');
		}

		$version 	= $config->get('version', 0);
		$subversion = $config->get('subversion', 0);

		echo "Software Version: /e4j/ com_vikappointments {$version}.{$subversion}";
		die;
	}
	
	/**
	 * Fetches the given order and prepares the view
	 * that is going to be printed. The template is
	 * immediately echoed and the print popup is 
	 * triggered automatically.
	 * 
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  oid 	The order number.
	 * @param 	string 	 sid 	The order key.
	 *
	 * @return 	void
	 */
	function printorder()
	{
		$input = JFactory::getApplication()->input;

		$oid = $input->getUint('oid', 0);
		$sid = $input->getString('sid', '');
		
		$order_details = VikAppointments::fetchOrderDetails($oid, $sid, JFactory::getLanguage()->getTag());

		if ($order_details === false)
		{
			die (JText::_('VAPORDERRESERVATIONERROR'));
		}
		
		$tmpl = VikAppointments::loadEmailTemplate($order_details);
		$html = VikAppointments::parseEmailTemplate($tmpl, $order_details, false);

		$input->set('tmpl', 'component');
		
		$html .= "<script>window.print();</script>\n";

		/**
		 * Use the specific blank layout to print the view
		 * and exit to avoid including internal and external assets,
		 * which may alter the default style of the template.
		 *
		 * @since 1.6
		 */
		echo JLayoutHelper::render('document.blankpage', array('body' => $html));
		exit;
	}
	
	/**
	 * This task is used to confirm an order (only PENDING status).
	 * After a successful confirmation, the owner of the
	 * appointment will be notified via e-mail.
	 *
	 * The response of this action is echoed directly.
	 *
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  oid 		The order number.
	 * @param 	string 	 conf_key 	The confirmation key.
	 *
	 * @return 	void
	 */
	function confirmord()
	{
		$input 	= JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();

		$oid 		= $input->getUint('oid', 0);
		$conf_key 	= $input->getString('conf_key');
		
		if (empty($conf_key))
		{
			echo '<div class="vap-confirmpage order-error">'.JText::_('VAPCONFORDNOROWS').'</div>';
			return;
		}
		
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'sid', 'status')))
			->from($dbo->qn('#__vikappointments_reservation'))
			->where(array(
				$dbo->qn('id') . ' = ' . $oid,
				$dbo->qn('conf_key') . ' = ' . $dbo->q($conf_key),
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo '<div class="vap-confirmpage order-error">'.JText::_('VAPCONFORDNOROWS').'</div>';
			return;
		}
		
		$order = $dbo->loadObject();

		if ($order->status != 'PENDING')
		{
			if ($order->status == 'CONFIRMED')
			{
				echo '<div class="vap-confirmpage order-notice">'.JText::_('VAPCONFORDISCONFIRMED').'</div>';
			}
			else
			{
				echo '<div class="vap-confirmpage order-error">'.JText::_('VAPCONFORDISREMOVED').'</div>';
			}

			return;
		}
		
		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_reservation'))
			->set($dbo->qn('status') . ' = ' . $dbo->q('CONFIRMED'))
			->where(array(
				$dbo->qn('id') . ' = ' . $oid,
				$dbo->qn('id_parent') . ' = ' . $oid,
			), 'OR');

		$dbo->setQuery($q);
		$dbo->execute();

		// the status has been altered, we can track it
		VAPOrderStatus::getInstance()->keepTrack('CONFIRMED', $oid, 'VAP_STATUS_CONFIRMED_WITH_LINK');
		//
		
		echo '<div class="vap-confirmpage order-good">'.JText::_('VAPCONFORDCOMPLETED').'</div>';
		
		$order_details = VikAppointments::fetchOrderDetails($oid, $order->sid);
		VikAppointments::sendCustomerEmail($order_details);
		////////
		$order_details_original = VikAppointments::fetchOrderDetails($order_details[0]['id'], $order_details[0]['sid'], VikAppointments::getDefaultLanguage('site'));
		VikAppointments::sendAdminEmail($order_details_original);
	}
	
	/**
	 * Task used to send a contact message to the specified employee.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id_employee 	The ID of the employee.
	 * @param 	string 	 sendername 	The sender name.
	 * @param 	string 	 sendermail 	The sender e-mail.
	 * @param 	string 	 mail_content 	The contents to send via mail.
	 * @param 	string 	 return 		An optional return URL.
	 *
	 * @return 	void
	 */
	function quickcontact()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$itemid = $input->getInt('Itemid');

		$args = array();
		$args['id_employee'] 	= $input->getUint('id_employee', 0);
		$args['sendername'] 	= $input->getString('sendername');
		$args['sendermail'] 	= $input->getString('sendermail');
		$args['content'] 		= $input->getString('mail_content');

		$return_url = $input->getBase64('return');

		if ($return_url)
		{
			$return_url = base64_decode($return_url);
		}
		else
		{
			$return_url = 'index.php?option=com_vikappointments&view=employeeslist';	
		}

		$return_url .= '&id_emp=' . $args['id_employee'];

		if ($itemid)
		{
			$return_url .= '&Itemid=' . $itemid;
		}

		$return_url = JRoute::_($return_url, false);

		if (!JSession::checkToken())
		{
			// invalid session token
			$app->enqueueMessage(JText::_('JINVALID_TOKEN'), 'error');
			$app->redirect($return_url);
			exit;
		}

		$vik = UIApplication::getInstance();

		if ($vik->isGlobalCaptcha() && !$vik->reCaptcha('check'))
		{
			// invalid captcha
			$app->enqueueMessage(JText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL'), 'error');
			$app->redirect($return_url);
			exit;
		}

		if (empty($args['sendermail']) || empty($args['sendername']) || empty($args['content']))
		{
			// missing required fields
			$app->enqueueMessage(JText::_('VAPQUICKCONTACTNOCONTENT'), 'error');
			$app->redirect($return_url);
			exit;
		}
		
		// get employee details
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('quick_contact', 'email')))
			->from($dbo->qn('#__vikappointments_employee'))
			->where($dbo->qn('id') . ' = ' . $args['id_employee']);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// the employee doesn't exist
			$app->enqueueMessage(JText::_('VAPEMPNOTFOUNDERROR'), 'error');
			$app->redirect($return_url);
			exit;
		}

		$emp = $dbo->loadAssoc();

		if (!$emp['quick_contact'])
		{
			// the employee doesn't allow quick contact messages
			$app->enqueueMessage(JText::_('VAPEMPNOTREACHABLE'), 'error');
			$app->redirect($return_url);
			exit;
		}
		
		$subject = JText::_('VAPEMPQUICKCONTACTSUBJECT');
		$is_html = false;

		/**
		 * Trigger event to allow the plugins to manipulate quick contact messages for the given employee.
		 *
		 * @param 	string 	 $id_emp 	The employee ID.
		 * @param 	string 	 &$subject  The e-mail subject.
		 * @param 	string 	 &$content  The e-mail content (the customer message). 
		 * @param 	boolean  $is_html 	True if the e-mail should support HTML tags (false by default).
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		UIFactory::getEventDispatcher()->trigger('onBeforeQuickContactSend', array($args['id_employee'], &$subject, &$args['content'], &$is_html));
			
		$vik->sendMail($args['sendermail'], $args['sendername'], $emp['email'], $args['sendermail'], $subject, $args['content'], null, $is_html);
		
		$app->enqueueMessage(JText::_('VAPQUICKCONTACTMAILSENT'));
		$app->redirect($return_url);
	}

	/**
	 * Task used to send a contact message for the specified service.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id_service 	The ID of the service.
	 * @param 	string 	 sendername 	The sender name.
	 * @param 	string 	 sendermail 	The sender e-mail.
	 * @param 	string 	 mail_content 	The contents to send via mail.
	 * @param 	string 	 return 		An optional return URL.
	 *
	 * @return 	void
	 */
	function quickcontactservice()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$itemid = $input->getInt('Itemid');

		$args = array();
		$args['id_service'] 	= $input->getUint('id_service', 0);
		$args['sendername'] 	= $input->getString('sendername');
		$args['sendermail'] 	= $input->getString('sendermail');
		$args['content'] 		= $input->getString('mail_content');

		$return_url = $input->getBase64('return');

		if ($return_url)
		{
			$return_url = base64_decode($return_url);
		}
		else
		{
			$return_url = 'index.php?option=com_vikappointments&view=servicesearch&id_ser=' . $args['id_service'];	
		}

		if ($itemid)
		{
			$return_url .= '&Itemid=' . $itemid;
		}

		$return_url = JRoute::_($return_url, false);
		
		if (!JSession::checkToken())
		{
			// invalid session token
			$app->enqueueMessage(JText::_('JINVALID_TOKEN'), 'error');
			$app->redirect($return_url);
			exit;
		}

		$vik = UIApplication::getInstance();

		if ($vik->isGlobalCaptcha() && !$vik->reCaptcha('check'))
		{
			// invalid captcha
			$app->enqueueMessage(JText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL'), 'error');
			$app->redirect($return_url);
			exit;
		}

		if (empty($args['sendermail']) || empty($args['sendername']) || empty($args['content']))
		{
			// missing required fields
			$app->enqueueMessage(JText::_('VAPQUICKCONTACTNOCONTENT'), 'error');
			$app->redirect($return_url);
			exit;
		}

		// get service details
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('quick_contact', 'name')))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . $args['id_service']);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// the service doesn't exist
			$app->enqueueMessage(JText::_('VAPSERNOTFOUNDERROR'), 'error');
			$app->redirect($return_url);
			exit;
		}
		
		$ser = $dbo->loadAssoc();

		if (!$ser['quick_contact'])
		{
			// the service doesn't allow quick contact messages
			$app->enqueueMessage(JText::_('VAPSERNOTREACHABLE'), 'error');
			$app->redirect($return_url);
			exit;
		}
			
		$subject 	 = JText::sprintf('VAPSERQUICKCONTACTSUBJECT', $ser['name']);
		$admail_list = VikAppointments::getAdminMailList();
		$is_html 	 = false;

		/**
		 * Trigger event to allow the plugins to manipulate quick contact messages for administrators.
		 *
		 * @param 	string 	 $id_ser 	The service ID.
		 * @param 	string 	 &$subject  The e-mail subject.
		 * @param 	string 	 &$content  The e-mail content (the customer message). 
		 * @param 	boolean  $is_html 	True if the e-mail should support HTML tags (false by default).
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		UIFactory::getEventDispatcher()->trigger('onBeforeQuickContactServiceSend', array($args['id_service'], &$subject, &$args['content'], &$is_html));
		
		foreach ($admail_list as $admail)
		{
			$vik->sendMail($args['sendermail'], $args['sendername'], $admail, $args['sendermail'], $subject, $args['content'], null, false);
		}

		$app->enqueueMessage(JText::_('VAPQUICKCONTACTMAILSENT'));
		$app->redirect($return_url);
	}

	/**
	 * AJAX end-point used to subscribe a user into the waiting list.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  ts 			The unix timestamp of the day for which the user should be subscribed.
	 * @param 	integer  id_service 	The ID of the service for which the user is interested.
	 * @param 	integer  id_employee 	The ID of the employee for which the user is interested.
	 * @param 	string 	 email 			The user e-mail.
	 * @param 	string 	 phone_number 	The user phone number.
	 * @param 	string 	 phone_prefix 	The user phone prefix.
	 * 
	 * @return 	void
	 */
	function add_in_waiting_list()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$user 	= JFactory::getUser();

		if (!VikAppointments::isWaitingList())
		{
			// waiting list feature is blocked
			echo json_encode(array(0, 'You are not authorised to perform this request!'));
			exit;
		}

		$args = array();
		$args['timestamp'] 		= $input->getUint('ts', 0);
		$args['id_service'] 	= $input->getUint('id_service', 0);
		$args['id_employee'] 	= $input->getInt('id_employee', -1);
		$args['email'] 			= $input->getString('mail');
		$args['phone_number'] 	= $input->getString('phone');
		$args['phone_prefix'] 	= explode("_", $input->getString('prefix', ''));

		if (empty($args['email']) || empty($args['phone_number']) || count($args['phone_prefix']) != 2)
		{
			// missing required fields
			echo json_encode(array(0, JText::_('VAPERRINSUFFCUSTF')));
			exit;
		}

		// validate phone prefix

		$q = $dbo->getQuery(true)
			->select($dbo->qn('phone_prefix'))
			->from($dbo->qn('#__vikappointments_countries'))
			->where($dbo->qn('id') . ' = ' . (int) $args['phone_prefix'][0]);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// the country code doesn't exist
			echo json_encode(array(0, JText::_('VAPERRINSUFFCUSTF')));
			exit;
		}

		$args['phone_prefix'] = $dbo->loadResult();

		// validate service and employee

		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
			->where($dbo->qn('s.id') . ' = ' . $args['id_service'])
			->andWhere(array(
				$dbo->qn('a.id_employee') . ' = ' . $args['id_employee'],
				$dbo->qn('s.choose_emp') . ' = 0',
			), 'OR');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// the service doesn't exist, or the employee/service relation is not ok
			echo json_encode(array(0, JText::_('VAPERRINSUFFCUSTF')));
			exit;
		}

		// make sure the user is not yet subscribed to this waiting list

		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_waitinglist'))
			->where(array(
				$dbo->qn('id_service') . ' = ' . $args['id_service'],
				$dbo->qn('id_employee') . ' = ' . $args['id_employee'],
			));

		if ($user->guest)
		{
			// check email or phone number (it doesn't need to have both them identical)
			$dbo->andWhere(array(
				$dbo->qn('email') . ' = ' . $dbo->q($args['email']),
				$dbo->qn('phone_number') . ' = ' . $dbo->q($args['phone_number']),
			), 'OR');
		}
		else
		{
			$dbo->where($dbo->qn('jid') . ' = ' . $user->id);
		}

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			// already in waiting list
			echo json_encode(array(0, JText::_('VAPWAITLISTALREADYIN')));
			exit;
		}

		// insert in waiting list
		$data = (object) $args;
		$data->jid 			= $user->id;
		$data->created_on 	= time();

		$dbo->insertObject('#__vikappointments_waitinglist', $data, 'id');

		if ($data->id > 0)
		{
			echo json_encode(array(1, JText::_('VAPWAITLISTADDED1')));
		}
		else
		{
			echo json_encode(array(0, JText::_('VAPWAITLISTADDED0')));
		}

		exit;
	}

	/**
	 * Task used to unsubscribe a user from the waiting list.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	string 	email 		  The user e-mail.
	 * @param 	string 	phone_number  The user phone number.
	 * 
	 * @return 	void
	 */
	function unsubscr_confirm()
	{
		$app 	 = JFactory::getApplication();
		$input 	 = $app->input;
		$dbo 	 = JFactory::getDbo();
		$user 	 = JFactory::getUser();
		$session = JFactory::getSession();

		$itemid = $input->getInt('Itemid', 0);

		if (!JSession::checkToken())
		{
			$app->enqueueMessage(JText::_('JINVALID_TOKEN'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=unsubscr_waiting_list' . ($itemid ? '&Itemid=' . $itemid : ''), false));
			exit;
		}

		$mail  = $input->getString('email');
		$phone = $input->getString('phone_number');

		$q = $dbo->getQuery(true)
			->delete($dbo->qn('#__vikappointments_waitinglist'))
			->where(array(
				$dbo->qn('email') . ' = ' . $dbo->q($mail),
				$dbo->qn('phone_number') . ' = ' . $dbo->q($phone),
			));
			
		if (!$user->guest)
		{
			// (email = 'mail@mail.com' AND phone_number = '') OR (jid = 512)
			$q->orWhere($dbo->qn('jid') . ' = ' . $user->id);
		}

		$dbo->setQuery($q);
		$dbo->execute();

		$num_rows = $dbo->getAffectedRows();

		if ($num_rows)
		{
			$session->set('vap-unsubscribed-rows', $num_rows);
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPUNSUBSCRWAITLISTFAIL'), 'error');
		}

		$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=unsubscr_waiting_list' . ($itemid ? '&Itemid=' . $itemid : ''), false));
	}
	
	/**
	 * Task used to access the confirmapp view.
	 *
	 * @return 	void
	 */
	function confirmapp()
	{	
		$app 	= JFactory::getApplication();
		$input  = $app->input;
		$dbo 	= JFactory::getDbo();
		$config = UIFactory::getConfig();
		
		$args = array();
		$args['id_employee'] 	= $input->getInt('id_employee', -1);
		$args['id_service'] 	= $input->getUint('id_service', 0);
		$args['day'] 			= $input->getUint('day', 0);
		$args['hour'] 			= $input->getUint('hour', 0);
		$args['min'] 			= $input->getUint('min', 0);
		$args['people'] 		= $input->getUint('people', 1);
		$args['from'] 			= $input->getUint('from', 2);
		
		$args['checkin_ts'] 		= intval($args['day']);
		$args['checkin_ts_hour'] 	= $args['hour'];
		$args['checkin_ts_min'] 	= $args['min'];

		$args['people'] = max(array($args['people'], 1));

		$default_tz = date_default_timezone_get();

		VikAppointments::setCurrentTimezone(VikAppointments::getEmployeeTimezone($args['id_employee'])); 

		if (empty($args['day']))
		{
			$args['day'] = time();
		}

		$args['date'] = date($config->get('dateformat'), $args['day']);
		
		$enable_zip = 0;
		
		VikAppointments::loadCartLibrary();
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject();

		/**
		 * If the cart is empty and there are not data to add a new appointment,
		 * redirect the users to the services list without displaying any error message.
		 *
		 * @since 1.6
		 */
		if ($cart->isEmpty() && $args['id_employee'] <= 0 && $args['id_service'] <= 0)
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=serviceslist', false));
			exit;
		}

		/**
		 * Check if the time has been selected before clicking the Book Now button.
		 * In this way, the button can be used to add the services into the cart 
		 * more than once.
		 *
		 * It is also needed to check if the appointment is already in the cart,
		 * so that we can assume that a refresh has been performed.
		 *
		 * @note 	Refreshing the page after deleting the appointment that has
		 * 			been added will cause a loop that will re-add automatically
		 * 			that appointment (only if the cart contains 2 or more items).
		 *
		 * @since 1.6
		 */
		$time_is_selected = (bool) strlen($input->getString('hour'));

		if ($time_is_selected)
		{
			$checkin = getdate($args['checkin_ts']);
			$checkin = mktime($args['checkin_ts_hour'], $args['checkin_ts_min'], 0, $checkin['mon'], $checkin['mday'], $checkin['year']);

			// Check if the specified appointment is already in the cart.
			// Use "5" as duration to avoid a failure due to "bounds" method.
			if ($cart->indexOf($args['id_service'], $args['id_employee'], $checkin, 5) != -1)
			{
				// the appointment is already in the cart, we don't need to add it
				$time_is_selected = false;
			}
		}
		//
		
		// add to cart [appoint.] from request when the cart is disabled or empty or the time is selected
		if ((!VikAppointments::isCartEnabled() && !empty($args['id_service'])) || $cart->isEmpty() || $time_is_selected)
		{	
			$insert_data = array(
				'id_emp' 	=> $args['id_employee'], 
				'id_ser' 	=> $args['id_service'], 
				'ts' 		=> $args['checkin_ts'],
				'ts_hour' 	=> $args['checkin_ts_hour'],
				'ts_min' 	=> $args['checkin_ts_min'], 
				'people' 	=> $args['people'],
			 );
			
			if (!$this->add_recur_item_cart_rq($insert_data, false))
			{
				if ($args['from'] == 1)
				{
					$app->redirect(JRoute::_("index.php?option=com_vikappointments&view=employeesearch&id_employee={$args['id_employee']}&id_service={$args['id_service']}&day={$args['day']}", false));
				}
				else
				{
					$app->redirect(JRoute::_("index.php?option=com_vikappointments&view=servicesearch&id_ser={$args['id_service']}&id_emp={$args['id_employee']}&date={$args['date']}", false));
				}
				exit;
			}
		}
		
		// refresh cart
		$cart = $core->getCartObject();
		
		if ($cart->isEmpty())
		{
			$app->enqueueMessage(JText::_('VAPCARTEMPTYERR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=serviceslist', false));
			exit;
		}
		
		$items_to_remove = array();
		
		$df = $config->get('dateformat') . ' ' . $config->get('timeformat');
		
		foreach ($cart->getItemsList() as $item) 
		{	
			$service = array();

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('duration', 'sleep', 'choose_emp', 'max_capacity', 'enablezip')))
				->from($dbo->qn('#__vikappointments_service'))
				->where($dbo->qn('id') . ' = ' . (int) $item->getID());

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				$app->enqueueMessage(JText::_('VAPFINDRESSERVICENOTEXISTS'), 'error');

				if ($args['from'] == 1)
				{
					$app->redirect(JRoute::_("index.php?option=com_vikappointments&view=employeesearch&id_employee={$args['id_employee']}&day={$args['day']}", false));
				}
				else
				{
					$app->redirect(JRoute::_("index.php?option=com_vikappointments&view=serviceslist", false));
				}
				exit;
			}
	
			$service = $dbo->loadAssoc();
			
			$enable_zip = $enable_zip || $service['enablezip'];
			
			if ($service['choose_emp'])
			{
				$q = $dbo->getQuery(true)
					->select(1)
					->from($dbo->qn('#__vikappointments_employee'))
					->where($dbo->qn('id') . ' = ' . (int) $item->getID2());

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if (!$dbo->getNumRows())
				{
					$app->enqueueMessage(JText::_('VAPFINDRESEMPLOYEENOTEXISTS'), 'error');
					
					if ($args['from'] == 1)
					{
						$app->redirect(JRoute::_("index.php?option=com_vikappointments&view=employeeslist", false));
					}
					else
					{
						$app->redirect(JRoute::_("index.php?option=com_vikappointments&view=servicesearch&id_ser={$args['id_service']}&day={$args['day']}", false));
					}
					exit;
				} 
				
				$q = $dbo->getQuery(true)
					->select($dbo->qn(array('id', 'rate', 'duration', 'sleep')))
					->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
					->where(array(
						$dbo->qn('id_service') . ' = ' . (int) $item->getID(),
						$dbo->qn('id_employee') . ' = ' . (int) $item->getID2(),
					));

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if (!$dbo->getNumRows())
				{
					$app->enqueueMessage(JText::_('VAPFINDRESEMPSERNOTASSOC'), 'error');

					if ($args['from'] == 1)
					{
						$app->redirect(JRoute::_("index.php?option=com_vikappointments&view=employeesearch&id_employee={$args['id_employee']}&day={$args['day']}", false));
					}
					else
					{
						$app->redirect(JRoute::_("index.php?option=com_vikappointments&view=servicesearch&id_ser={$args['id_service']}&day={$args['day']}", false));
					}
					exit;
				}

				$overrides = $dbo->loadAssoc();

				$service['price'] 		= $overrides['rate'];
				$service['duration'] 	= $overrides['duration'];
				$service['sleep'] 		= $overrides['sleep'];
			}

			// SET EMPLOYEE TIMEZONE
			VikAppointments::setCurrentTimezone(VikAppointments::getEmployeeTimezone($item->getID2()));
	
			$valid = -2; // time in the past
			if (!VikAppointments::isTimeInThePast($item->getCheckinTimeStamp()))
			{
				$valid = 0;

				$total_duration = ($service['duration'] + $service['sleep']) * $item->getFactor();
				
				if ($item->getID2() != -1)
				{
					$valid = VikAppointments::isEmployeeAvailableFor($item->getID2(), $item->getID(), -1, $item->getCheckinTimeStamp(), $total_duration, $item->getPeople(), $service['max_capacity'], $dbo);
				}
				else
				{
					$emp = VikAppointments::getAvailableEmployeeOnService($item->getID(), $item->getCheckinTimeStamp(), $total_duration, $item->getPeople(), $service['max_capacity'], $dbo);
					
					/**
					 * The employee must be a value higher than 0.
					 * It is not enough to have it different than -1.
					 * 
					 * @since 1.6.2
					 */
					if ($emp > 0)
					{
						$valid = 1;
					}
				}
			}
			
			if ($valid != 1)
			{
				$err_key = 'VAPCARTITEMNOTAVERR1';
				if ($valid == -1)
				{
					$err_key = 'VAPCARTITEMNOTAVERR2';
				}
				else if ($valid == -2)
				{
					$err_key = 'VAPCARTITEMNOTAVERR3';
				}

				$app->enqueueMessage(JText::sprintf($err_key, $item->getName() . ' @ ' . $item->getCheckinDate($df)), 'error');
				$items_to_remove[] = $item;
			}
		}

		foreach ($items_to_remove as $item)
		{
			$cart->removeItem($item->getID(), $item->getID2(), $item->getCheckinTimeStamp());
		}

		VikAppointments::usePackagesForServicesInCart($cart, $dbo);
		
		$cart->balance();
		$core->storeCart($cart);

		if ($cart->isEmpty())
		{
			$app->enqueueMessage(JText::_('VAPCARTEMPTYERR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=serviceslist', false));
			exit;
		}

		$input->set('enable_zip', $enable_zip);

		/**
		 * @since 1.6 	The view is automatically displayed from the display() method.
		 */
		// parent::display();
	}

	/**
	 * Task used to register a new order.
	 *
	 * @return 	void // matin
	 */
	function saveorder()
	{
		$app 		= JFactory::getApplication();
		$input 		= $app->input;
		$dbo 		= JFactory::getDbo();
		$curr_user 	= JFactory::getUser();
		$dispatcher = UIFactory::getEventDispatcher();

		$args = array();

		VikAppointments::loadCartLibrary();
		
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject();

		if ($cart->isEmpty())
		{
			$app->enqueueMessage(JText::_('VAPCARTEMPTYERR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=serviceslist', false));
			exit;
		}

		/**
		 * Trigger event to manipulate the cart instance.
		 *
		 * @param 	mixed 	&$cart 	The cart instance.
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		$dispatcher->trigger('onInitSaveOrder', array(&$cart));

		$services  = array();
		$employees = array();
		
		$items_to_remove = array();

		$enable_zip  = false;
		$same_emp_id = true;
		
		$items = $cart->getItemsList();

		$default_tz = date_default_timezone_get();
		
		for ($i = 0; $i < count($items); $i++)
		{	
			$item = $items[$i];
			
			$service = array();

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('duration', 'sleep', 'choose_emp', 'max_capacity', 'enablezip')))
				->from($dbo->qn('#__vikappointments_service'))
				->where($dbo->qn('id') . ' = ' . $item->getID());

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				$app->enqueueMessage(JText::_('VAPFINDRESSERVICENOTEXISTS'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=confirmapp', false));
				exit;
			} 
	
			$service = $dbo->loadAssoc();
			
			$enable_zip  = $enable_zip || $service['enablezip'];
			$same_emp_id = $same_emp_id && ($items[$i]->getID2() == $items[0]->getID2());	
			
			if ($service['choose_emp'] || $item->getID2() > 0)
			{
				$q = $dbo->getQuery(true)
					->select($dbo->qn(array('rate', 'duration', 'sleep')))
					->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
					->where(array(
						$dbo->qn('id_employee') . ' = ' . $item->getID2(),
						$dbo->qn('id_service') . ' = ' . $item->getID(),
					));

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if (!$dbo->getNumRows())
				{
					$app->enqueueMessage(JText::_('VAPFINDRESEMPLOYEENOTEXISTS'), 'error');
					$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=confirmapp', false));
					exit;
				} 

				$overrides = $dbo->loadAssoc();

				$service['duration'] = $overrides['duration'];
				$service['sleep'] 	 = $overrides['sleep'];
			}

			if ($item->getID2() > 0)
			{
				$service['view_emp'] = 1;
			}
			else
			{
				$service['view_emp'] = (int) $service['choose_emp'];
			}

			$services[$i] = $service;

			// SET EMPLOYEE TIMEZONE
			$emp_tz = VikAppointments::getEmployeeTimezone($item->getID2());
			
			if (empty($emp_tz))
			{
				$emp_tz = $default_tz;
			}
            //$emp_tz = 'Asian/Tehran';
			VikAppointments::setCurrentTimezone($emp_tz);

			try
			{
				// check availability
				$id_employee = UIApplication::getInstance()->checkAvailability($item, $service);

				if (!$id_employee)
				{
					// throw exception in case of no availability
					throw new RuntimeException(JText::sprintf('VAPCARTITEMNOTAVERR1', $item->getName()));
				}

				// assign employee ID to list on success
				$employees[$i] = $id_employee;
			}
			catch (RuntimeException $e)
			{
				// item must be removed from the cart
				$items_to_remove[] = $item;
				// raise error message
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
		
		// RESTORE TIMEZONE
		VikAppointments::setCurrentTimezone($default_tz);

		foreach ($items_to_remove as $item)
		{
			$cart->removeItem($item->getID(), $item->getID2(), $item->getCheckinTimeStamp());
		}
		
		if (count($items_to_remove))
		{
			$cart->balance();
			$core->storeCart($cart);
			
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=confirmapp', false));
		}
		
		// VALIDATE ZIP CODE
		$_zip_field_id = VikAppointments::getZipCodeValidationFieldId($employees[0]);

		if ($_zip_field_id != -1 && $enable_zip && !VikAppointments::validateZipCode($input->getString('vapcf' . $_zip_field_id, ''), array_unique($employees, SORT_NUMERIC)))
		{
			$app->enqueueMessage(JText::_('VAPCONFAPPZIPERROR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=confirmapp', false));
			exit;
		}

		// GET CUSTOM FIELDS

		$cf_services = array();
		
		foreach ($items as $item)
		{
			if (!in_array($item->getID(), $cf_services))
			{
				$cf_services[] = $item->getID();
			}
		}
		
		$_cf = VAPCustomFields::getList(0, ($same_emp_id ? $items[0]->getID2() : 0), $cf_services, CF_EXCLUDE_REQUIRED_CHECKBOX);

		// define rules that should be used by the custom fields
		$args['purchaser_nominative'] 	= '';
		$args['purchaser_mail']			= '';
		$args['purchaser_phone']		= '';
		$args['purchaser_prefix']		= '';
		$args['purchaser_country']		= '';

		$args['uploads'] = array();
		
		// validate custom fields
		try
		{
			$cust_req = VAPCustomFields::loadFromRequest($_cf, $args);
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=confirmapp', false));
			exit;
		}

		/**
		 * Trigger event to manipulate the custom fields array and the
		 * billing information of the customer, extrapolated from the rules
		 * of the custom fields.
		 *
		 * @param 	array 	&$cust_req 	The custom fields values.
		 * @param 	array 	$args 		The billing array.
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		$dispatcher->trigger('onPrepareFieldsSaveOrder', array(&$cust_req, &$args));

		// attach 'uploads' list to the native var
		$uploaded_files = $args['uploads'];
		// JSON encode custom fields list
		$args['custom_f'] = json_encode($cust_req);
		
		// GET COUPON VALUES
		$session = JFactory::getSession();
		$coupon = $session->get('vap_coupon_data', '');
		$session->set('vap_coupon_data', '');
		$args['coupon_str'] = "";

		/**
		 * Trigger event to manipulate any coupon code. It is also possible
		 * to apply additional events in case a specific coupon code is applied.
		 *
		 * @param 	mixed 	&$coupon  The coupon code array, if any. Otherwise an empty string.
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		$dispatcher->trigger('onBeforeCouponSaveOrder', array(&$coupon));

		if (!empty($coupon))
		{
			$args['coupon_str'] = $coupon['code'].';;'.$coupon['percentot'].';;'.$coupon['value'];
			
			VikAppointments::couponUsed($coupon, $dbo);
		}
		// END COUPON

		// EVALUATING TOTAL COST

		$total_cost = $cart->getTotalCost();
		$credit 	= false;
		$creditUsed = 0.0;

		/**
		 * Retrieve the user credit to apply an additional
		 * discount (if any).
		 *
		 * @since 1.6
		 */
		if (!$curr_user->guest)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('credit'))
				->from($dbo->qn('#__vikappointments_users'))
				->where($dbo->qn('jid') . ' = ' . $curr_user->id)
				->orWhere(array(
					$dbo->qn('jid') . ' <= 0',
					$dbo->qn('billing_mail') . ' = ' . $dbo->q($curr_user->email),
				), 'AND');

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$credit = (float) $dbo->loadResult();
			}
		}

		/**
		 * Trigger event to manipulate the total cost before it is going to be calculated.
		 *
		 * @param 	float 	&$total 	The base total cost.
		 * @param 	mixed 	&$credit 	The credit (float) of the user, if logged-in. Otherwise false.
		 * @param 	JUser 	$user 		The instance of the current user.
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		$dispatcher->trigger('onBeforeCalculateTotalSaveOrder', array(&$total_cost, &$credit, $curr_user));
		
		$total_cost = VikAppointments::getDiscountTotalCost($total_cost, $coupon, $credit, $creditUsed);
		
		// GET METHOD OF PAYMENTS
		
		$payment_id = $input->getInt('vappaymentradio', 0);
		$payment	= array('id' => -1, 'setconfirmed' => true);
		$payments_found_row = -1;
		
		// VALIDATE PAYMENT
		if ($total_cost > 0)
		{
			$payments = array();

			if ($same_emp_id)
			{
				$payments = VikAppointments::getAllEmployeePayments($items[0]->getID2());
			}
			else
			{
				// get global payments
				$payments = VikAppointments::getAllEmployeePayments();
			}

			/**
			 * Trigger event to manipulate the selected payment gateway.
			 *
			 * @param 	integer  &$id_payment  The ID of the selected payment.
			 * @param 	array 	 &$payments    The list of the available payments.
			 *
			 * @return 	void
			 *
			 * @since 	1.6
			 */
			$dispatcher->trigger('onSwitchPaymentSaveOrder', array(&$payment_id, &$payments));
			
			if (count($payments))
			{
				for ($i = 0, $n = count($payments); $i < $n && $payments_found_row == -1; $i++)
				{
					if ($payments[$i]['id'] == $payment_id)
					{
						$payments_found_row = $i;
					}
				}
				
				if ($payments_found_row == -1)
				{
					$app->enqueueMessage(JText::_('VAPERRINVPAYMENT'), 'error');
					$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=confirmapp', false));
				}
				
				$payment = $payments[$payments_found_row];
			}
		}

		// EVALUATING STATUS 

		$status_comment = false;
		
		$args['status'] = "PENDING";

		if ($total_cost == 0 || count($payment) == 0 || $payment['setconfirmed'] == 1)
		{
			$args['status'] = VikAppointments::getDefaultStatus();

			if ($args['status'] == 'CONFIRMED')
			{
				if ($total_cost == 0)
				{
					$status_comment = 'VAP_STATUS_CONFIRMED_AS_NO_COST';
				}
				else if (!count($payments))
				{
					$status_comment = 'VAP_STATUS_CONFIRMED_AS_NO_PAYMENT';
				}
				else
				{
					$status_comment = 'VAP_STATUS_CONFIRMED_RESULT_OF_PAYMENT';
				}
			}
		} 

		// END VALIDATION
		
		$locked_until = time() + VikAppointments::getAppointmentsLockedTime() * 60;
		
		// GENERATE SID
		$sid 		= VikAppointments::generateSerialCode(16);
		$conf_key 	= VikAppointments::generateSerialCode(12);
		$oid = -1;
		
		// CREATION PARAMS
		$created_on = time();
		$created_by = -1;

		if (!$curr_user->guest)
		{
			$created_by = $curr_user->id;
		}
		
		$items_count = count($items);
		
		if ($items_count > 1)
		{
			// push a multi-order parent as first appointment
			array_unshift($items, new VikAppointmentsItem(-1, -1, '', '', $total_cost, -1, -1, -1, -1));
			 // same_emp_id ? id_employee(emp payment) : -1 (glob payment)
			array_unshift($employees, ($same_emp_id ? $employees[0] : -1));
			// push an empty service as first
			array_unshift($services, array('choose_emp' => 0, 'sleep' => 0, 'view_emp' => 0));

			$items_count++;
		}
		
		// PROCESS CART STORING (DB)
		
		for ($i = 0; $i < $items_count; $i++)
		{
			$item = $items[$i];

			$order = new stdClass;
			$order->sid						= $sid;
			$order->conf_key				= $conf_key;
			$order->id_employee				= $employees[$i];
			$order->id_service				= $item->getID();
			$order->id_payment				= $payment['id'];
			$order->checkin_ts				= $item->getCheckinTimeStamp();
			$order->people					= $item->getPeople();
			$order->duration				= $item->getDuration();
			$order->sleep					= $services[$i]['sleep'];
			$order->total_cost				= ($items_count > 1 ? $item->getTotalCost() : $total_cost);
			$order->paid					= 0;
			$order->purchaser_nominative	= $args['purchaser_nominative'];
			$order->purchaser_mail			= $args['purchaser_mail'];
			$order->purchaser_phone			= $args['purchaser_phone'];
			$order->purchaser_prefix		= $args['purchaser_prefix'];
			$order->purchaser_country		= $args['purchaser_country'];
			$order->langtag					= JFactory::getLanguage()->getTag();
			$order->custom_f				= $args['custom_f'];
			$order->coupon_str				= $args['coupon_str'];
			$order->status					= $args['status'];
			$order->locked_until			= $locked_until;
			$order->view_emp				= $services[$i]['view_emp'];
			$order->uploads					= json_encode($uploaded_files);
			// the mother (-1), the children (next lid)
			$order->id_parent				= $oid;
			$order->createdon				= $created_on;
			$order->createdby				= $created_by;
			$order->id_user					= $created_by;

			/**
			 * Trigger event to manipulate the order item details before storing it.
			 *
			 * @param 	object  &$order  The order item details object.
			 * @param 	mixed 	$item    The cart item instance.
			 *
			 * @return 	void
			 *
			 * @since 	1.6
			 */
			$dispatcher->trigger('onBeforeSaveOrder', array(&$order, $item));
			
			$dbo->insertObject('#__vikappointments_reservation', $order, 'id');
			
			$lid = $order->id;
			
			if ($oid == -1)
			{
				$oid = $lid;
			}

			/**
			 * Trigger event after storing the order item details.
			 *
			 * @param 	object  &$order  The order item details object.
			 * @param 	mixed 	$item    The cart item instance.
			 *
			 * @return 	void
			 *
			 * @since 	1.6
			 */
			$dispatcher->trigger('onAfterSaveOrder', array(&$order, $item));
			
			foreach ($item->getOptionsList() as $o)
			{
				$option = new stdClass;
				$option->id_reservation = $lid;
				$option->id_option 		= $o->getID();
				$option->id_variation 	= $o->getVariationID();
				$option->inc_price 		= $o->getPrice() * $o->getQuantity();
				$option->quantity 		= $o->getQuantity();

				/**
				 * Trigger event to manipulate the order item option before storing it.
				 *
				 * @param 	object 	&$option  The option details object.
				 * @param 	object  $order 	  The order item details object.
				 * @param 	mixed 	$item     The cart item instance.
				 *
				 * @return 	void
				 *
				 * @since 	1.6
				 */
				$dispatcher->trigger('onBeforeOptionSaveOrder', array(&$option, $order, $item));

				$dbo->insertObject('#__vikappointments_res_opt_assoc', $option, 'id');
			}
		}

		if (count($items) == 1)
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_reservation'))
				->set($dbo->qn('id_parent') . ' = ' . $dbo->qn('id'))
				->where($dbo->qn('id') . ' = ' . $oid);

			$dbo->setQuery($q);
			$dbo->execute();	
		}

		if ($curr_user->id || !empty($args['purchaser_mail']))
		{
			$id_customer = -1;
			
			// inject uploads within custom fields JSON
			foreach ($uploaded_files as $k => $v)
			{
				$cust_req[$k] = $v;
			}

			// build customer object for query
			$data = new stdClass;
			$data->jid 		= $curr_user->id;
			$data->fields 	= json_encode($cust_req);

			$lookup = array(
				// the KEY is the column of the DB table
				// the VAL is the key contained in $args var
				'billing_name' 		=> 'purchaser_nominative',
				'billing_mail' 		=> 'purchaser_mail',
				'billing_phone'		=> 'purchaser_phone',
				'country_code'		=> 'purchaser_country',
				'billing_state'		=> false,
				'billing_city'		=> false,
				'billing_address'	=> false,
				'billing_zip'		=> false,
				'company'			=> false,
				'vatnum'			=> false,
			);

			// try to retrieve the billing details from the custom
			// fields specified by the users during the checkout
			foreach ($lookup as $userColumn => $key)
			{
				if (!$key)
				{
					$key = $userColumn;
				}

				if (!empty($args[$key]))
				{
					$data->{$userColumn} = $args[$key];
				}
			}

			// get customer ID
			$q = $dbo->getQuery(true);

			$q = $q->select($dbo->qn('id'))
				->from($dbo->qn('#__vikappointments_users'));

			if ($curr_user->id)
			{
				// the user is logged in, search by ID
				$q->where($dbo->qn('jid') . ' = ' . $curr_user->id);
			}
			else
			{
				$q->where(0);
			}

			// search also by e-mail (and the user ID is null)
			$q->orWhere(array(
				$dbo->qn('billing_mail') . ' = ' . $dbo->q($args['purchaser_mail']),
				$dbo->qn('jid') . ' <= 0',
			), 'AND');
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			// update if the customer already exists
			if ($dbo->getNumRows())
			{
				$data->id = $dbo->loadResult();

				if ($data->jid <= 0)
				{
					// unset Joomla ID if the customer is not logged in
					unset($data->jid);
				}

				// update always the credit of the user
				$data->credit = $credit - $creditUsed;
				
				$dbo->updateObject('#__vikappointments_users', $data, 'id');

				$is_new = false;
			}
			// otherwise insert a new record
			else
			{
				$dbo->insertObject('#__vikappointments_users', $data, 'id');

				$is_new = true;
			}

			$arrayData = (array) $data;

			/**
			 * Trigger event to allow the plugins to save the customer.
			 *
			 * @param 	array 	 &$arrayData 	An array containing the customer info.
			 * @param 	boolean  $is_new 		True if the customer has just been inserted, otherwise false.
			 *
			 * @return 	void
			 *
			 * @since 	1.6
			 */
			$dispatcher->trigger('onCustomerSave', array(&$arrayData, $is_new));
			
			// update customer ID on order record
			if ($data->id > 0)
			{
				$q = $dbo->getQuery(true)
					->update($dbo->qn('#__vikappointments_reservation'))
					->set($dbo->qn('id_user') . ' = ' . $data->id)
					->where(array(
						$dbo->qn('id') . ' = ' . $oid,
						$dbo->qn('id_parent') . ' = ' . $oid,
					), 'OR');

				$dbo->setQuery($q);
				$dbo->execute();
			}
		}
		
		$send_when = VikAppointments::getSendMailWhen();
		
		// SEND EMAILS
		$order_details = VikAppointments::fetchOrderDetails($oid, $sid);
		if ($send_when['admin'] == 2 || $send_when['employee'] == 2 || $order_details[0]['status'] == 'CONFIRMED')
		{
			$order_details_original = VikAppointments::fetchOrderDetails($order_details[0]['id'], $order_details[0]['sid'], VikAppointments::getDefaultLanguage('site'));
			VikAppointments::sendAdminEmail($order_details_original);
		}

		if ($send_when['customer'] != 0 && ($send_when['customer'] == 2 || $order_details[0]['status'] == 'CONFIRMED'))
		{
			VikAppointments::sendCustomerEmail($order_details);
		}
		// END SEND EMAILS
		
		// SEND SMS NOTIFICATIONS
		if ($order_details[0]['status'] == 'CONFIRMED')
		{
			VikAppointments::sendSmsAction($order_details[0]['purchaser_prefix'] . $order_details[0]['purchaser_phone'], $order_details);
		}
		// END SMS

		// flush waiting list
		VikAppointments::flushWaitingList($order_details);
		//

		// update packages used
		$redeemed = VikAppointments::registerPackagesUsed($order_details);

		if ($redeemed)
		{
			$status_comment = 'VAP_STATUS_PACKAGE_REDEEMED';
		}
		//

		// track always the first order status (register also a reason if set)
		VAPOrderStatus::getInstance()->keepTrack($order_details[0]['status'], $order_details[0]['id'], $status_comment);
		
		$cart->emptyCart();
		$core->storeCart($cart);
		
		$app->redirect(JRoute::_("index.php?option=com_vikappointments&view=order&ordnum={$oid}&ordkey={$sid}", false));
	}

	/**
	 * This is the end-point used by the gateway to validate a payment transaction.
	 * It is mandatory to send the following parameters (via GET or POST) in order to
	 * retrieve the correct details of the order transaction.
	 *
	 * @param 	integer  ordnum 	The order number (ID).
	 * @param 	string 	 ordkey 	The order key (SID).
	 *
	 * @return 	void
	 */
	function notifypayment()
	{
		$app 		= JFactory::getApplication();
		$input 		= $app->input;
		$dbo 		= JFactory::getDbo();
		$config 	= UIFactory::getConfig();
		$dispatcher = UIFactory::getEventDispatcher();
		$ui         = UIApplication::getInstance();

		$oid = $input->getUint('ordnum', 0);
		$sid = $input->getString('ordkey', '');

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_reservation'))
			->where(array(
				$dbo->qn('id') . ' = ' . $oid,
				$dbo->qn('sid') . ' = ' . $dbo->q($sid),
				$dbo->qn('closure') . ' = 0',
			));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// order not found
			throw new Exception("Order [$oid - $sid] not found.", 404);
		}

		$order = $dbo->loadAssoc();
		
		if ($order['status'] == "PENDING")
		{	
			$payment = VikAppointments::getPayment($order['id_payment'], false);

			if (!$payment)
			{
				throw new Exception('Payment [' . $order['id_payment'] . '] not found.', 404);
			}

			/**
			 * The payment URLs are correctly routed for external usage.
			 *
			 * @since 1.6
			 */
			$return_url = $ui->routeForExternalUse("index.php?option=com_vikappointments&view=order&ordnum={$order['id']}&ordkey={$order['sid']}", false);
			$error_url  = $ui->routeForExternalUse("index.php?option=com_vikappointments&view=order&ordnum={$order['id']}&ordkey={$order['sid']}", false);
			$notify_url = $ui->routeForExternalUse("index.php?option=com_vikappointments&task=notifypayment&ordnum={$order['id']}&ordkey={$order['sid']}", false);

			$transaction_name = JText::sprintf('VAPTRANSACTIONNAME', VikAppointments::getAgencyName());
			
			$total_to_pay = $order['total_cost'];
			
			// calculate deposit
			$deposit = VikAppointments::getDepositAmountToLeave($order['total_cost'], $order['skip_deposit']);

			if ($deposit !== false)
			{
				// a deposit should be left
				$total_to_pay = $deposit; 
			}

			// add the payment charge to the total to pay and remove any amount paid
			$total_to_pay = $total_to_pay + $payment['charge'] - $order['tot_paid'];
	
			$array_order['oid'] 					= $oid;
			$array_order['sid'] 					= $sid;
			$array_order['attempt']					= $order['payment_attempt'];
			$array_order['transaction_currency'] 	= $config->get('currencyname');
			$array_order['transaction_name'] 		= $transaction_name;
			$array_order['currency_symb'] 			= $config->get('currencysymb');
			$array_order['tax'] 					= 0;
			$array_order['return_url'] 				= $return_url;
			$array_order['error_url'] 				= $error_url;
			$array_order['notify_url'] 				= $notify_url;
			$array_order['total_to_pay'] 			= $total_to_pay;
			$array_order['total_net_price'] 		= $total_to_pay;
			$array_order['total_tax'] 				= 0;
			$array_order['leave_deposit'] 			= 0;
			$array_order['payment_info'] 			= $payment;
			$array_order['type']					= 'appointments';

			$array_order['details'] = array(
				'purchaser_mail' 		=> $order['purchaser_mail'],
				'purchaser_phone' 		=> $order['purchaser_phone'],
				'purchaser_nominative' 	=> $order['purchaser_nominative'],
			);
		
			$params = array();
			
			if (!empty($payment['params']))
			{
				$params = json_decode($payment['params'], true);
			}

			/**
			 * Trigger event to manipulate the payment details.
			 *
			 * @param 	array 	&$order   The transaction details.
			 * @param 	array 	&$params  The payment configuration array.
			 *
			 * @return 	void
			 *
			 * @since 	1.6
			 */
			$dispatcher->trigger('onInitPaymentTransaction', array(&$array_order, &$params));
			
			/**
			 * Instantiate the payment using the platform handler.
			 *
			 * @since 1.6.3
			 */
			$obj = $ui->getPaymentInstance($payment['file'], $array_order, $params);
			
			try
			{
				$res_args = $obj->validatePayment();
			}
			catch (Exception $e)
			{
				$res_args['verified'] 	= 0;
				$res_args['log'] 		= $e->getMessage();
			}
			
			// successful response
			if ($res_args['verified'] == 1)
			{
				$already_paid = 0;

				if (!empty($res_args['tot_paid']) && $res_args['tot_paid'] >= $array_order['total_to_pay'])
				{
					$already_paid = 1;
				}
				else if (empty($res_args['tot_paid']))
				{
					// the amount paid is not specified
					$res_args['tot_paid'] = 0;
				}

				// sum the total paid to the previous amount
				$res_args['tot_paid'] += $order['tot_paid'];

				// update the total paid amount to the parent reservation
				$q = $dbo->getQuery(true)
					->update($dbo->qn('#__vikappointments_reservation'))
					->set($dbo->qn('tot_paid') . ' = ' . (float) $res_args['tot_paid'])
					->where($dbo->qn('id') . ' = ' . $oid);

				$dbo->setQuery($q);
				$dbo->execute();

				// update the status of the parent and children
				$q = $dbo->getQuery(true)
					->update($dbo->qn('#__vikappointments_reservation'))
					->set($dbo->qn('status') . ' = ' . $dbo->q('CONFIRMED'))
					->set($dbo->qn('paid') . ' = ' . $already_paid)
					->where(array(
						$dbo->qn('id') . ' = ' . $oid,
						$dbo->qn('id_parent') . ' = ' . $oid,
					), 'OR');

				$dbo->setQuery($q);
				$dbo->execute();

				// track the reason of the status change
				VAPOrderStatus::getInstance()->keepTrack('CONFIRMED', $oid, 'VAP_STATUS_CHANGED_FROM_PAY');
				
				$send_when = VikAppointments::getSendMailWhen();
				
				// SEND EMAILS
				$order_details = VikAppointments::fetchOrderDetails($order['id'], $order['sid']);

				if ($send_when['admin'] == 2 || $order_details[0]['status'] == 'CONFIRMED')
				{
					$order_details_original = VikAppointments::fetchOrderDetails($order_details[0]['id'], $order_details[0]['sid'], VikAppointments::getDefaultLanguage('site'));
					VikAppointments::sendAdminEmail($order_details_original);
				}

				if ($send_when['customer'] != 0 && ($send_when['customer'] == 2 || $order_details[0]['status'] == 'CONFIRMED'))
				{
					VikAppointments::sendCustomerEmail($order_details);
				}
				// END SEND EMAILS
				
				// SEND SMS NOTIFICATIONS
				if ($order_details[0]['status'] == 'CONFIRMED')
				{
					VikAppointments::sendSmsAction($order_details[0]['purchaser_prefix'] . $order_details[0]['purchaser_phone'], $order_details);
				}
				// END SMS

				// INVOICE GENERATION
				VikAppointments::generateInvoice($order_details);
				// END INVOICE

				/**
				 * Trigger event after the validation of a successful transaction.
				 *
				 * @param 	array 	$order  The transaction details.
				 * @param 	array 	$args   The response array.
				 *
				 * @return 	void
				 *
				 * @since 	1.6
				 */
				$dispatcher->trigger('onSuccessPaymentTransaction', array($array_order, $res_args));
				
				if (method_exists($obj, 'afterValidation'))
				{
					$obj->afterValidation(1);
				}
			}
			// failure response
			else
			{
				// send email to admin with $res_args['log']
				if (strlen($res_args['log']) > 0)
				{
					$sendermail 	= VikAppointments::getSenderMail();
					$admail_list 	= VikAppointments::getAdminMailList();
					$adname 		= VikAppointments::getAgencyName();
					
					$subject 	= JText::_('VAPINVALIDPAYMENTSUBJECT');
					$hmess 		= JText::_('VAPINVALIDPAYMENTCONTENT') . "\n\n" . $res_args['log'];
					
					foreach ($admail_list as $admail)
					{
						$ui->sendMail($sendermail, $adname, $admail, $admail, $subject, $hmess, '', false);
					}

					if (!empty($order['log']))
					{
						$res_args['log'] = $order['log'] . "\n\n" . $res_args['log'];
					}

					// concat the logs to the parent reservation
					$q = $dbo->getQuery(true)
						->update($dbo->qn('#__vikappointments_reservation'))
						->set($dbo->qn('log') . ' = ' . $dbo->q($res_args['log']))
						->set($dbo->qn('payment_attempt') . ' = ' . ($order['payment_attempt'] + 1))
						->where($dbo->qn('id') . ' = ' . $oid);

					$dbo->setQuery($q);
					$dbo->execute();
				}

				/**
				 * Trigger event after the validation of a failed transaction.
				 *
				 * @param 	array 	$order  The transaction details.
				 * @param 	array 	$args   The response array.
				 *
				 * @return 	void
				 *
				 * @since 	1.6
				 */
				$dispatcher->trigger('onFailPaymentTransaction', array($array_order, $res_args));

				if (method_exists($obj, 'afterValidation'))
				{
					$obj->afterValidation(0);
				}
			}
		}
	}

	/**
	 * Helper method used to generate the invoices related to the specified order.
	 *
	 * @param 	array 	 $order_details  The order details.
	 *
	 * @return 	boolean  True on success, otherwise false.
	 *
	 * @deprecated 	1.7  Use VikAppointments::generateInvoice() instead.
	 */
	private function generateInvoice($order_details)
	{
		return VikAppointments::generateInvoice($order_details);
	}

	/**
	 * Mark the specified order as cancelled.
	 *
	 * @return 	void
	 */
	public function cancel_order()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$oid 		= $input->getUint('oid', 0);
		$sid 		= $input->getString('sid', '');
		$id_parent 	= $input->getInt('parent', -1);

		$return_uri = JRoute::_("index.php?option=com_vikappointments&view=order&ordnum={$id_parent}&ordkey={$sid}", false);
		
		if (!VikAppointments::isCancellationEnabled())
		{
			$app->enqueueMessage(JText::_('VAPORDERCANCDISABLEDERROR'), 'error');
			$app->redirect($return_uri);
			exit;
		}

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('r.checkin_ts', 'r.id_parent', 'r.tot_paid', 'p.charge')))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_gpayments', 'p') . ' ON ' . $dbo->qn('p.id') . ' = ' . $dbo->qn('r.id_payment'))
			->where(array(
				$dbo->qn('r.id') . ' = ' . $oid,
				$dbo->qn('r.sid') . ' = ' . $dbo->q($sid),
				$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
			));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			$app->enqueueMessage(JText::_('VAPORDERCANCDISABLEDERROR'), 'error');
			$app->redirect($return_uri);
			exit;
		}
		
		$row = $dbo->loadAssoc();
		
		if (!VikAppointments::canUserCancelOrder($row['checkin_ts']) && $row['id_parent'] != -1)
		{
			$app->enqueueMessage(JText::sprintf('VAPORDERCANCEXPIREDERROR', VikAppointments::getCancelBeforeTime()), 'error');
			$app->redirect($return_uri);
			exit;
		}

		/**
		 * Trigger event before the cancellation of the specified order.
		 *
		 * @param 	integer  $id  The order ID to cancel.
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		UIFactory::getEventDispatcher()->trigger('onBeforeCancelOrder', array($oid));

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_reservation'))
			->set($dbo->qn('status') . ' = ' . $dbo->q('CANCELED'))
			->where(array(
				$dbo->qn('id') . ' = ' . $oid,
				$dbo->qn('id_parent') . ' = ' . $oid,
			), 'OR');
		
		$dbo->setQuery($q);
		$dbo->execute();
		
		$order_details = VikAppointments::fetchOrderDetails($oid, $sid);
		VikAppointments::sendCustomerEmail($order_details);

		$order_details_original = VikAppointments::fetchOrderDetails($oid, $sid, VikAppointments::getDefaultLanguage('site'));
//		VikAppointments::sendCancellationAdminEmail($order_details_original);

		// check for customers in waiting list
		VikAppointments::notifyCustomersInWaitingList($order_details);
		//

		$user = JFactory::getUser();

		/**
		 * The user credit is kept only if the following conditions are verified:
		 * - user must be logged-in
		 * - the user credit setting must be enabled
		 * - the total paid must be higher than 0.00
		 * - the order must be a parent (multi-order) or a single appointment
		 */
		if (!$user->guest && UIFactory::getConfig()->get('usercredit') && $row['tot_paid'] > 0
			&& ($row['id_parent'] == -1 || $row['id_parent'] == $oid))
		{
			// Remove the payment charge from the total paid.
			// Ignore the charge if it is a discount.
			$credit = $row['tot_paid'] - max(array((float) $row['charge'], 0));

			// keep user credit
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_users'))
				->set($dbo->qn('credit') . ' = (' . $dbo->qn('credit') . ' + ' . $credit. ')')
				->where($dbo->qn('jid') . ' = ' . $user->id);

			$dbo->setQuery($q);
			$dbo->execute();
		}

		/**
		 * Try to unredeem the packages that might have been used for this order.
		 *
		 * @since 1.6.3
		 */
		if ($order_details['total_cost'] == 0)
		{
			$unredeemed = VikAppointments::registerPackagesUsed($order_details, $increase = false);

			if ($unredeemed)
			{
				$app->enqueueMessage(JText::_('VAPRESTOREPACKSONCANCEL'), 'notice');
			}
		}

		// track the cancellation of the order
		VAPOrderStatus::getInstance()->keepTrack('CANCELED', $oid, 'VAP_STATUS_ORDER_CANCELLED');
		
		$app->redirect($return_uri);
	}

	/**
	 * Task used to create a new user account from
	 * the registration form used by VikAppointments.
	 *
	 * @param 	integer  $type 		The registration type (1 for employees, 2 for users).
	 * @param 	mixed 	 $callback 	A callback to launch after registering the user.
	 *
	 * @return 	void
	 */
	function registeruser($type = null, $callback = null)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		$return_url = base64_decode($input->getBase64('return'));

		if (empty($return_url))
		{
			$return_url = 'index.php';
		}
		else
		{
			$return_url = JRoute::_($return_url, false);
		}

		if (!JSession::checkToken())
		{
			// invalid session token
			$app->enqueueMessage(JText::_('JINVALID_TOKEN'), 'error');
			$app->redirect($return_url);
			exit;
		}

		$vik = UIApplication::getInstance();

		if ($vik->isCaptcha() && !$vik->reCaptcha('check'))
		{
			// invalid captcha
			$app->enqueueMessage(JText::_('PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL'), 'error');
			$app->redirect($return_url);
			exit;
		}
		
		$args = array();
		$args['firstname'] 		= $input->getString('fname');
		$args['lastname'] 		= $input->getString('lname');
		$args['email'] 			= $input->getString('email');
		$args['username'] 		= $input->getString('username');
		$args['password'] 		= $input->getString('password');
		$args['confpassword'] 	= $input->getString('confpassword');
		
		if (!VikAppointments::checkUserArguments($args))
		{
			// missing required field
			$app->enqueueMessage(JText::_('VAPREGISTRATIONFAILED2'), 'error');
			$app->redirect($return_url);
			exit;
		}

		if (!$type)
		{
			$type = VikAppointments::REGISTER_USERS;
		}
		
		// try to register a new Joomla User
		$userid = VikAppointments::createNewJoomlaUser($args, $type);

		// launch callback
		if ($callback)
		{
			$args['id'] = (int) $userid;
			call_user_func_array($callback, array($args));
		}
		
		if (!$userid || $userid == 'useractivate' || $userid == 'adminactivate')
		{
			// use native com_users messages
			$app->redirect($return_url);
			exit;
		}
		
		// AUTO LOG IN
		$credentials = array(
			'username' => $args['username'],
			'password' => $args['password'],
			'remember' => true,
		);
		
		$app->login($credentials);

		$user = JFactory::getUser();
		$user->setLastVisit(time());
		$user->set('guest', 0);
		
		$app->redirect($return_url);		
	}

	/**
	 * Task used to create a new user account and the related employee
	 * from the registration form used by VikAppointments > Employee Login.
	 *
	 * @return 	void
	 *
	 * @uses 	registeruser()
	 */
	function registeremployee()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$auth 	= EmployeeAuth::getInstance();
		
		$return_url = base64_decode($input->getBase64('return'));

		if (empty($return_url))
		{
			$return_url = 'index.php';
		}
		else
		{
			$return_url = JRoute::_($return_url, false);
		}

		if (!$auth->register())
		{
			// employee registration not allowed
			$app->enqueueMessage(JText::_('VAPREGISTRATIONFAILED1'), 'error');
			$app->redirect($return_url);
			exit;
		}

		// dispatch user registration by passing a specific
		// callback to create also a new employee
		$this->registeruser(VikAppointments::REGISTER_EMPLOYEE, array($this, '_registerEmployee'));
	}

	/**
	 * Helper method to create a new employee record
	 * after a successful transaction.
	 *
	 * @param 	array 	$args 	The user details.
	 *
	 * @return 	void
	 */
	private function _registerEmployee(array $args)
	{
		$dbo  = JFactory::getDbo();
		$auth = EmployeeAuth::getInstance();
		
		$listable  = $auth->getSignUpStatus() == 2 ? 1 : 0;
		$active_to = -1;

		if (!$listable)
		{
			$active_to = 0;
		}

		// Even if the user is not active, it is still assigned to a specific ID.
		// We should recover the user ID that matches the username specified in the args.
		if (!isset($args['id']) || (int) $args['id'] <= 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id'))
				->from($dbo->qn('#__users'))
				->where($dbo->qn('username') . ' = ' . $dbo->q($args['username']));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				/**
				 * Do not proceed with the employee creation in case
				 * the user registration failed (e.g. due to a duplicated e-mail).
				 *
				 * @since 1.6.2
				 */
				return;
			}

			$args['id'] = (int) $dbo->loadResult();
		}

		// build record
		$employee = new stdClass;
		$employee->firstname 	= $args['firstname'];
		$employee->lastname 	= $args['lastname'];
		$employee->nickname 	= $args['lastname'] . ' ' . $args['firstname'];
		$employee->email 		= $args['email'];
		$employee->jid 			= $args['id'];
		$employee->listable 	= $listable;
		$employee->active_to 	= $active_to;
		$employee->synckey 		= VikAppointments::generateSerialCode(16);

		/**
		 * Generate an alias automatically every time a new employee is registered from the front-end.
		 * The alias is based on the specified nickname (first name + last name).
		 * Once the employee is saved, the alias could be changed only from the back-end.
		 *
		 * @since 1.6.2
		 */
		UILoader::import('libraries.helpers.alias');
		$employee->alias = AliasHelper::getUniqueAlias($employee->nickname, 'employee');

		// insert new record
		$dbo->insertObject('#__vikappointments_employee', $employee, 'id');
		
		if ($employee->id)
		{
			// auto assign services
			$auto_services = $auth->getServicesToAssign();

			// make sure the list is not empty
			if (count($auto_services))
			{
				// get each service details
				$q = $dbo->getQuery(true)
					->select($dbo->qn(array('id', 'price', 'duration', 'sleep')))
					->from($dbo->qn('#__vikappointments_service'))
					->where($dbo->qn('id') . ' IN (' . implode(',', $auto_services) . ')');

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$services = $dbo->loadObjectList();

					// build a unique insert
					$q = $dbo->getQuery(true)
						->insert($dbo->qn('#__vikappointments_ser_emp_assoc'))
						->columns($dbo->qn(array('id_service', 'id_employee', 'rate', 'duration', 'sleep')));

					// iterate the services
					foreach ($services as $service)
					{
						// append the service details to the insert
						$values = array(
							$service->id,
							$employee->id,
							$service->price,
							$service->duration,
							$service->sleep,
						);

						// quote the values array
						$values = array_map(array($dbo, 'q'), $values);
						// implode the array to get the values as string
						$q->values(implode(',', $values));
					}

					$dbo->setQuery($q);
					$dbo->execute();
				}
			}

			// MAIL

			$admin_mail_list 	= VikAppointments::getAdminMailList();
			$sendermail 		= VikAppointments::getSenderMail();
			$adminname 			= VikAppointments::getAgencyName();

			$admin_subject = JText::sprintf('VAPEMPREGADMINSUBJECT', $employee->nickname);
			$admin_content = JText::sprintf('VAPEMPREGADMINCONTENT', $employee->nickname);

			$vik = UIApplication::getInstance();

			foreach ($admin_mail_list as $_m)
			{
				$vik->sendMail($_m, $_m, $_m, $_m, $admin_subject, $admin_content, '', true);
			}
			
			/**
			 * The e-mail sent to the new employee is deprecated.
			 * The contents inform the user that the account is pending and
			 * requires a manual check from the administrator.
			 * In case the account is not activated immediately, all the info
			 * are sent via mail from com_users registration function.
			 *
			 * @since 	1.6
			 */

			// $front_subject = JText::sprintf('VAPEMPREGFRONTSUBJECT', $employee->firstname);
			// $front_content = JText::sprintf('VAPEMPREGFRONTCONTENT', $employee->firstname);
			// $vik->sendMail($sendermail, $adminname, $employee->email, $admin_mail_list[0], $front_subject, $front_content, '', true);
		}
	}

	/**
	 * Task used to access the packagesconfirm view.
	 *
	 * @return 	void
	 */
	function packagesconfirm()
	{
		VikAppointments::loadCartPackagesLibrary();
		$core = new VikAppointmentsCartPackagesCore();
		$cart = $core->getCartObject();

		if ($cart->isEmpty())
		{
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_vikappointments&view=packages', false));
			exit;
		}

		/**
		 * The view is automatically displayed from the display() method.
		 *
		 * @since 1.6
		 */
		// parent::display();
	}

	/**
	 * Task used to register a new package order.
	 *
	 * @return 	void
	 */
	function savepackagesorder()
	{
		$app 		= JFactory::getApplication();
		$input 		= $app->input;
		$dbo 		= JFactory::getDbo();
		$user 		= JFactory::getUser();
		$dispatcher = UIFactory::getEventDispatcher();

		$itemid = $input->getInt('Itemid', 0);

		VikAppointments::loadCartPackagesLibrary();
		
		$core = new VikAppointmentsCartPackagesCore();
		$cart = $core->getCartObject();
		
		if ($cart->isEmpty())
		{
			$app->enqueueMessage(JText::_('VAPCARTEMPTYERR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=packages&Itemid=' . $itemid, false));
			exit;
		}

		if ($user->guest)
		{
			$app->enqueueMessage(JText::_('VAPPACKLOGINREQERR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=packagesconfirm&Itemid=' . $itemid, false));
			exit;
		}

		$args = array();

		// GET CUSTOM FIELDS
		
		$_cf = VAPCustomFields::getList(0, 0, 0, CF_EXCLUDE_REQUIRED_CHECKBOX | CF_EXCLUDE_SEPARATOR | CF_EXCLUDE_FILE);
		
		// define rules that should be used by the custom fields
		$args['purchaser_nominative'] 	= '';
		$args['purchaser_mail']			= '';
		$args['purchaser_phone']		= '';
		$args['purchaser_prefix']		= '';
		$args['purchaser_country']		= '';

		$args['uploads'] = array();
		
		// validate custom fields
		try
		{
			$cust_req = VAPCustomFields::loadFromRequest($_cf, $args);
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=packagesconfirm', false));
			exit;
		}

		// JSON encode custom fields list
		$args['custom_f'] = json_encode($cust_req);

		// TOTAL COST

		$args['total_cost'] = $cart->getTotalCost();
		
		// GET METHOD OF PAYMENTS
		
		$id_payment = $input->getUint('vappaymentradio', 0);
		$payment = array('id' => -1, 'setconfirmed' => true);
		
		// VALIDATE PAYMENT

		if ($args['total_cost'] > 0)
		{
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_gpayments'))
				->where(array(
					$dbo->qn('id') . ' = ' . $id_payment,
					$dbo->qn('published') . ' = 1',
					$dbo->qn('subscr') . ' = 1',
				));

			/**
			 * Retrieve only the payments the belong to the view
			 * access level of the current user.
			 *
			 * @since 1.6.2
			 */
			$levels = $user->getAuthorisedViewLevels();

			if ($levels)
			{
				$q->where($dbo->qn('level') . ' IN (' . implode(', ', $levels) . ')');
			}

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$payment = $dbo->loadAssoc();
			}
			else
			{
				// payment not found, make sure there is no published payments
				$q = $dbo->getQuery(true)
					->select(1)
					->from($dbo->qn('#__vikappointments_gpayments'))
					->where(array(
						$dbo->qn('published') . ' = 1',
						$dbo->qn('subscr') . ' = 1',
					));

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if ($dbo->getNumRows())
				{ 
					// if at least a payment is found, the specified payment doesn't exist
					$app->enqueueMessage(JText::_('VAPERRINVPAYMENT'), 'error');
					$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=packagesconfirm&Itemid=' . $this->itemid, false));
					exit;
				}
			}
		}

		// EVALUATING STATUS 
		
		$args['status'] = "PENDING";
		if ($args['total_cost'] == 0 || $payment['setconfirmed'])
		{
			$args['status'] = "CONFIRMED";
		} 

		// END VALIDATION
		
		// CREATION PARAMS
		$args['createdon'] = time();
		$args['createdby'] = $user->id;
			
		// INSERT RESERVATION

		$order = new stdClass;
		$order->sid 					= VikAppointments::generateSerialCode(16);
		$order->id_payment 				= $payment['id'];
		$order->total_cost 				= $args['total_cost'];
		$order->purchaser_nominative 	= $args['purchaser_nominative'];
		$order->purchaser_mail 			= $args['purchaser_mail'];
		$order->purchaser_phone 		= $args['purchaser_phone'];
		$order->purchaser_prefix 		= $args['purchaser_prefix'];
		$order->purchaser_country 		= $args['purchaser_country'];
		$order->langtag 				= JFactory::getLanguage()->getTag();
		$order->custom_f 				= $args['custom_f'];
		$order->status 					= $args['status'];
		$order->createdon 				= time();
		$order->createdby 				= $user->id;
		// $order->id_user 				= $user->id;
		
		$dbo->insertObject('#__vikappointments_package_order', $order, 'id');

		if ($order->id <= 0)
		{
			$app->enqueueMessage(JText::_('VAPSUBSCRINSERTERR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=packagesconfirm&Itemid=' . $itemid, false));
			exit;
		}

		// INSERT PACKAGES IN RESERVATION
		foreach ($cart->getPackagesList() as $p)
		{
			$item = new stdClass;
			$item->id_order 	= $order->id;
			$item->id_package 	= $p->getID();
			$item->price 		= $p->getPrice();
			$item->quantity 	= $p->getQuantity();
			$item->num_app 		= $p->getNumberAppointments() * $p->getQuantity();

			$dbo->insertObject('#__vikappointments_package_order_item', $item, 'id');
		}

		// build customer object for query
		$data = new stdClass;
		$data->jid 		= $user->id;
		$data->fields 	= json_encode($cust_req);

		$lookup = array(
			// the KEY is the column of the DB table
			// the VAL is the key contained in $args var
			'billing_name' 		=> 'purchaser_nominative',
			'billing_mail' 		=> 'purchaser_mail',
			'billing_phone'		=> 'purchaser_phone',
			'country_code'		=> 'purchaser_country',
			'billing_state'		=> false,
			'billing_city'		=> false,
			'billing_address'	=> false,
			'billing_zip'		=> false,
			'company'			=> false,
			'vatnum'			=> false,
		);

		// try to retrieve the billing details from the custom
		// fields specified by the users during the checkout
		foreach ($lookup as $userColumn => $key)
		{
			if (!$key)
			{
				$key = $userColumn;
			}

			if (!empty($args[$key]))
			{
				$data->{$userColumn} = $args[$key];
			}
		}

		// CREATE CUSTOMER IF NOT EXISTS
		$id_customer = -1;

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id'))
			->from($dbo->qn('#__vikappointments_users'))
			->where($dbo->qn('jid') . ' = ' . $user->id);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// insert customer
			$dbo->insertObject('#__vikappointments_users', $data, 'id');

			$is_new = true;
		}
		else
		{
			// update customer
			$data->id = $dbo->loadResult();
			$dbo->updateObject('#__vikappointments_users', $data, 'id');

			$is_new = false;
		}

		/**
		 * Trigger event to allow the plugins to save the customer.
		 *
		 * @since 1.6
		 */
		$arrayData = (array) $data;
		$dispatcher->trigger('onCustomerSave', array(&$arrayData, $is_new));

		if ($data->id)
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_package_order'))
				->set($dbo->qn('id_user') . ' = ' . $data->id)
				->where($dbo->qn('id') . ' = ' . $order->id);

			$dbo->setQuery($q);
			$dbo->execute();
		}
		
		// SEND EMAILS
		$send_when = VikAppointments::getSendMailWhen();

		$order_details = VikAppointments::fetchPackagesOrderDetails($order->id, $order->sid);

		if ($send_when['admin'] == 2 || $order_details['status'] == 'CONFIRMED')
		{
			$order_details_original = VikAppointments::fetchPackagesOrderDetails($order->id, $order->sid, VikAppointments::getDefaultLanguage('site'));
			VikAppointments::sendPackagesAdminEmail($order_details_original);
		}

		if ($send_when['customer'] != 0 && ($send_when['customer'] == 2 || $order_details['status'] == 'CONFIRMED'))
		{
			VikAppointments::sendPackagesCustomerEmail($order_details);
		}
		// END SEND EMAILS
		
		$cart->emptyCart();
		$core->storeCart($cart);
		
		$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=packagesorder&ordnum=' . $order->id . '&ordkey=' . $order->sid . '&Itemid=' . $itemid, false));
	}

	/**
	 * This is the end-point used by the gateway to validate a payment transaction
	 * for the packages orders only.
	 * It is mandatory to send the following parameters (via GET or POST) in order to
	 * retrieve the correct details of the order transaction.
	 *
	 * @param 	integer  ordnum 	The order number (ID).
	 * @param 	string 	 ordkey 	The order key (SID).
	 *
	 * @return 	void
	 */
	function notifypackpayment()
	{
		$app 		= JFactory::getApplication();
		$input 		= $app->input;
		$dbo 		= JFactory::getDbo();
		$config 	= UIFactory::getConfig();
		$dispatcher = UIFactory::getEventDispatcher();
		$ui 		= UIApplication::getInstance();

		$oid = $input->getUint('ordnum', 0);
		$sid = $input->getString('ordkey', '');

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_package_order'))
			->where(array(
				$dbo->qn('id') . ' = ' . $oid,
				$dbo->qn('sid') . ' = ' . $dbo->q($sid),
			));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// order not found
			throw new Exception("Package order [$oid - $sid] not found.", 404);
		}
		
		$order = $dbo->loadAssoc();
		
		if ($order['status'] == "PENDING")
		{
			$payment = VikAppointments::getPayment($order['id_payment'], false);

			if (!$payment)
			{
				throw new Exception('Payment [' . $order['id_payment'] . '] not found.', 404);
			}

			/**
			 * The payment URLs are correctly routed for external usage.
			 *
			 * @since 1.6
			 */
			$return_url = $ui->routeForExternalUse("index.php?option=com_vikappointments&view=packagesorder&ordnum={$order['id']}&ordkey={$order['sid']}", false);
			$error_url  = $ui->routeForExternalUse("index.php?option=com_vikappointments&view=packagesorder&ordnum={$order['id']}&ordkey={$order['sid']}", false);
			$notify_url = $ui->routeForExternalUse("index.php?option=com_vikappointments&task=notifypackpayment&ordnum={$order['id']}&ordkey={$order['sid']}", false);
			
			$transaction_name = JText::sprintf('VAPTRANSACTIONNAMEPACK', VikAppointments::getAgencyName());
			
			// add the payment charge to the total to pay and remove any amount paid
			$total_to_pay = $order['total_cost'] + $payment['charge'] - $order['tot_paid'];
	
			$array_order['oid'] 					= $oid;
			$array_order['sid'] 					= $sid;
			$array_order['attempt']					= $order['payment_attempt'];
			$array_order['transaction_currency'] 	= $config->get('currencyname');
			$array_order['transaction_name'] 		= $transaction_name;
			$array_order['currency_symb'] 			= $config->get('currencysymb');
			$array_order['tax'] 					= 0;
			$array_order['return_url'] 				= $return_url;
			$array_order['error_url'] 				= $error_url;
			$array_order['notify_url'] 				= $notify_url;
			$array_order['total_to_pay'] 			= $total_to_pay;
			$array_order['total_net_price'] 		= $total_to_pay;
			$array_order['total_tax'] 				= 0;
			$array_order['leave_deposit'] 			= 0;
			$array_order['payment_info'] 			= $payment;
			$array_order['type']					= 'packages';

			$array_order['details'] = array(
				'purchaser_mail' 		=> $order['purchaser_mail'],
				'purchaser_phone' 		=> $order['purchaser_phone'],
				'purchaser_nominative' 	=> $order['purchaser_nominative'],
			);
	
			$params = array();
			
			if (!empty($payment['params']))
			{
				$params = json_decode($payment['params'], true);
			}

			/**
			 * Trigger event to manipulate the payment details.
			 *
			 * @param 	array 	&$order   The transaction details.
			 * @param 	array 	&$params  The payment configuration array.
			 *
			 * @return 	void
			 *
			 * @since 	1.6
			 */
			$dispatcher->trigger('onInitPaymentTransaction', array(&$array_order, &$params));
		
			/**
			 * Instantiate the payment using the platform handler.
			 *
			 * @since 1.6.3
			 */
			$obj = $ui->getPaymentInstance($payment['file'], $array_order, $params);
		
			try
			{
				$res_args = $obj->validatePayment();
			}
			catch (Exception $e)
			{
				$res_args['verified'] 	= 0;
				$res_args['log'] 		= $e->getMessage();
			}
			
			// successful response
			if ($res_args['verified'] == 1)
			{
				$status = "PENDING";

				if (empty($res_args['tot_paid']) || $res_args['tot_paid'] >= $array_order['total_to_pay'])
				{
					// Confirm the order only if the total amount has been fully paid.
					// Since a payment may not return the total paid amount, we need to
					// confirm a reservation also if the tot_paid attribute is empty.
					$status = "CONFIRMED";
				}

				if (empty($res_args['tot_paid']))
				{
					$res_args['tot_paid'] = 0;
				}

				// sum the total paid to the previous amount
				$res_args['tot_paid'] += $order['tot_paid'];

				$q = $dbo->getQuery(true)
					->update($dbo->qn('#__vikappointments_package_order'))
					->set($dbo->qn('status') . ' = ' . $dbo->q($status))
					->set($dbo->qn('tot_paid') . ' = ' . $res_args['tot_paid'])
					->where($dbo->qn('id') . ' = ' . $oid);
				
				$dbo->setQuery($q);
				$dbo->execute();
				
				$send_when = VikAppointments::getSendMailWhen();
				
				// SEND EMAILS
				$order_details = VikAppointments::fetchPackagesOrderDetails($order['id'], $order['sid']);
				
				if ($send_when['admin'] == 2 || $order_details['status'] == 'CONFIRMED')
				{
					$order_details_original = VikAppointments::fetchPackagesOrderDetails($order['id'], $order['sid'], VikAppointments::getDefaultLanguage('site'));
					VikAppointments::sendPackagesAdminEmail($order_details_original);
				}

				if ($send_when['customer'] != 0 && ($send_when['customer'] == 2 || $order_details['status'] == 'CONFIRMED'))
				{
					VikAppointments::sendPackagesCustomerEmail($order_details);
				}
				// END SEND EMAILS

				// INVOICE GENERATION
				if ($order_details['status'] == 'CONFIRMED')
				{
					/**
					 * Generate invoice for the employee.
					 *
					 * @since 1.6
					 */
					VikAppointments::generateInvoice($order_details, 'packages');
				}
				// END INVOICE

				/**
				 * Trigger event after the validation of a successful transaction.
				 *
				 * @param 	array 	$order  The transaction details.
				 * @param 	array 	$args   The response array.
				 *
				 * @return 	void
				 *
				 * @since 	1.6
				 */
				$dispatcher->trigger('onSuccessPaymentTransaction', array($array_order, $res_args));
				
				if (method_exists($obj, 'afterValidation'))
				{
					$obj->afterValidation(1);
				}
			}
			else
			{
				// send email to admin with $res_args['log']
				if (strlen($res_args['log']) > 0)
				{
					$sendermail 	= VikAppointments::getSenderMail();
					$admail_list 	= VikAppointments::getAdminMailList();
					$adname 		= VikAppointments::getAgencyName();
					
					$subject 	= JText::_('VAPINVALIDPAYMENTSUBJECT');
					$hmess 		= JText::_('VAPINVALIDPAYMENTCONTENT') . "\n\n" . $res_args['log'];
					
					foreach ($admail_list as $admail)
					{
						$ui->sendMail($sendermail, $adname, $admail, $admail, $subject, $hmess, '', false);
					}

					if (!empty($order['log']))
					{
						$res_args['log'] = $order['log'] . "\n\n" . $res_args['log'];
					}

					// concat the logs to the parent reservation
					$q = $dbo->getQuery(true)
						->update($dbo->qn('#__vikappointments_package_order'))
						->set($dbo->qn('log') . ' = ' . $dbo->q($res_args['log']))
						->set($dbo->qn('payment_attempt') . ' = ' . ($order['payment_attempt'] + 1))
						->where($dbo->qn('id') . ' = ' . $oid);

					$dbo->setQuery($q);
					$dbo->execute();
				}

				/**
				 * Trigger event after the validation of a failed transaction.
				 *
				 * @param 	array 	$order  The transaction details.
				 * @param 	array 	$args   The response array.
				 *
				 * @return 	void
				 *
				 * @since 	1.6
				 */
				$dispatcher->trigger('onFailPaymentTransaction', array($array_order, $res_args));

				if (method_exists($obj, 'afterValidation'))
				{
					$obj->afterValidation(0);
				}
			}
		}
	}

	/**
	 * Task used to perform the logout of the current user.
	 * The user will be redirected to the "allorders" page.
	 *
	 * @return 	void
	 */
	function userlogout()
	{
		$app = JFactory::getApplication();
		$app->logout(JFactory::getUser()->id);

		$itemid = $app->input->getInt('Itemid', 0);

		$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=allorders' . ($itemid ? '&Itemid=' . $itemid : ''), false));
	}

	/**
	 * Task used to save the billing details of the logged-in user.
	 * If the task is reached by a guest user, it will be redirected 
	 * to the "allorders" page.
	 *
	 * @return 	void
	 */
	function saveUserProfile()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$user 	= JFactory::getUser();

		$itemid = $input->getInt('Itemid', 0);
		
		if ($user->guest)
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=allorders' . ($itemid ? '&Itemid=' . $itemid : ''), false));
			exit;
		}
		
		// get current customer image (if any)
		$customer_image = "";

		$q = $dbo->getQuery(true)
			->select('image')
			->from($dbo->qn('#__vikappointments_users'))
			->where($dbo->qn('jid') . ' = ' . $user->id);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$customer_image = $dbo->loadResult();
		}
		
		// get customer details from request
		$args = array();
		$args['jid'] 				= $user->id;
		$args['billing_name'] 		= $input->getString('billing_name');
		$args['billing_mail'] 		= $input->getString('billing_mail');
		$args['billing_phone'] 		= $input->getString('billing_phone');
		$args['country_code'] 		= $input->getString('country_code');
		$args['billing_state'] 		= $input->getString('billing_state');
		$args['billing_city'] 		= $input->getString('billing_city');
		$args['billing_address'] 	= $input->getString('billing_address');
		$args['billing_address_2'] 	= $input->getString('billing_address_2');
		$args['billing_zip'] 		= $input->getString('billing_zip');
		$args['company'] 			= $input->getString('company');
		$args['vatnum'] 			= $input->getString('vatnum');
		$args['ssn'] 				= $input->getString('ssn');

		/**
		 * Billing name and e-mail are mandatory.
		 *
		 * @since 1.6.3
		 */
		if (empty($args['billing_name']) || empty($args['billing_mail']))
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=userprofile' . ($itemid ? '&Itemid=' . $itemid : ''), false));
			exit;
		}
		
		// make SSN uppercase
		$args['ssn'] = strtoupper($args['ssn']);

		$img = $input->files->get('image', null, 'array');
		
		// upload image
		$ori = VikAppointments::uploadImage($img, VAPCUSTOMERS_AVATAR . DIRECTORY_SEPARATOR);
		
		// check upload response
		if ($ori['esit'] == -1)
		{
			$app->enqueueMessage(JText::_('VAPCONFIGUPLOADERROR'), 'error');
		}
		else if ($ori['esit'] == -2)
		{
			$app->enqueueMessage(JText::_('VAPCONFIGFILETYPEERROR'), 'error');
		}
		else if ($ori['esit'] == 1)
		{
			// success
			$args['image'] = $ori['name'];

			// unlink old customer image
			if ($customer_image)
			{
				unlink(VAPCUSTOMERS_AVATAR . DIRECTORY_SEPARATOR . $customer_image);
			}
		}
		
		// if the country code doesn't exist, make it empty
		if (VikAppointmentsLocations::getCountryFromCode($args['country_code']) === false)
		{
			/**
			 * Use an empty value instead of "US".
			 *
			 * @since 1.6.3
			 */
			$args['country_code'] = '';
		}

		$customer = (object) $args;

		$dbo->updateObject('#__vikappointments_users', $customer, 'jid');

		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPUSERPROFILEDATASTORED'));
		}
		
		$return_url = 'index.php?option=com_vikappointments&view=userprofile';

		if ($input->getUint('return', 0) == 1)
		{
			$return_url = 'index.php?option=com_vikappointments&view=allorders';    
		}
		
		$app->redirect(JRoute::_($return_url . ($itemid ? '&Itemid=' . $itemid : ''), false));
	}
	
	// AJAX EMPLOYEE SEARCH
	
	/**
	 * AJAX end-point to return the availability timeline
	 * for the selected employee and additional details.
	 *
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id_emp 	The employee ID.
	 * @param 	integer  id_ser 	The service ID.
	 * @param 	integer  day 		The unix timestamp of the day.
	 * @param 	integer  people 	The number of guests (1 by default).
	 * @param 	string 	 locations 	The selected locations.
	 *
	 * @return 	mixed 	 If the method is called directly, the timeline object
	 * 					 will be returned. Otherwise void.
	 */
	function get_day_time_line(array $args = array())
	{
		$app 	= JFactory::getApplication();
		$input	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		if (!$args)
		{
			$id_emp 	= $input->getUint('id_emp', 0);
			$id_ser 	= $input->getUint('id_ser', 0);
			$day 		= $input->getUint('day', 0);
			$people 	= $input->getUint('people', 1);
			$locations 	= $input->getVar('locations', '');
		}
		else
		{
			extract($args);
		}
		
		if (empty($people))
		{
			$people = 1;
		}

		$default_tz = date_default_timezone_get();
		
		// set timezone
		$emp_tz = VikAppointments::getEmployeeTimezone($id_emp, $dbo);
		VikAppointments::setCurrentTimezone($emp_tz);

		// get locations
		
		if (empty($locations))
		{
			$locations = array();
		}
		else
		{
			$locations = array_map('intval', explode(',', $locations));
		}

		/**
		 * Check if the user is an employee in order to
		 * include times even if they are in the past.
		 *
		 * @since 1.6.3
		 */
		$auth = EmployeeAuth::getInstance();
		
		$date = getdate();

		if (!$auth->isEmployee() && $day < mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']))
		{
			if (!$args)
			{
				echo json_encode(array(0, JText::_('VAPFINDRESTIMEINTHEPAST')));
				die;
			}
			else
			{
				return array();
			}
		}

		// force timezone to avoid reset
		VikAppointments::setCurrentTimezone($emp_tz);
		
		if (VikAppointments::isClosingDay($day, $id_ser))
		{
			if (!$args)
			{
				echo json_encode(array(0, JText::_('VAPFINDRESCLOSINGDAY')));
				die;
			}
			else
			{
				return array();
			}
		}

		// working times must be calculated with the default timezone        
		/**
		 * Default timezone seems to be no more ok as the day should
		 * be always parsed by using the same timezone.
		 *
		 * @since 1.6.2
		 */
		// VikAppointments::setCurrentTimezone($default_tz);
		VikAppointments::setCurrentTimezone($emp_tz);
		$worktime = VikAppointments::getEmployeeWorkingTimes($id_emp, $id_ser, $day, $locations);
		// restore custom timezone
		VikAppointments::setCurrentTimezone($emp_tz);
		
		if (count($worktime) == 0)
		{
			if (!$args)
			{
				echo json_encode(array(0, JText::_('VAPFINDRESNODAYEMPLOYEE')));
				die;
			}
			else
			{
				return array();
			}
		}
		
		$bookings = VikAppointments::getAllEmployeeReservations($id_emp, $id_ser, $day, $day + 86399);

		// force timezone to avoid reset
		VikAppointments::setCurrentTimezone($emp_tz);

		$q = $dbo->getQuery(true);
		
		// load overrides
		$q->select($dbo->qn(array('a.duration', 'a.sleep')));
		// load service details
		$q->select($dbo->qn(array('s.id', 's.interval', 's.start_publishing', 's.end_publishing', 's.checkout_selection')));
		$q->select($dbo->qn(array('s.max_capacity', 's.max_per_res', 's.min_per_res', 's.priceperpeople', 's.app_per_slot', 's.display_seats')));

		$q->from($dbo->qn('#__vikappointments_service', 's'));
		$q->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'));

		$q->where(array(
			$dbo->qn('s.id') . ' = ' . $id_ser,
			$dbo->qn('a.id_employee') . ' = ' . $id_emp,
		));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			if (!$args)
			{
				echo json_encode(array(0, JText::_('VAPFINDRESSERVICENOTEXISTS')));
				die;
			}
			else
			{
				return array();
			}
		}
		
		$service = $dbo->loadAssoc();

		// force timezone to avoid reset
		VikAppointments::setCurrentTimezone($emp_tz);
		
		if ($service['max_capacity'] > 1 && ($people > $service['max_per_res'] || $people < $service['min_per_res']))
		{
			if (!$args)
			{
				echo json_encode(array(0, JText::_('VAPFINDRESPEOPLENOTVALID')));
				die;
			}
			else
			{
				return array();
			}
		}

		if ($service['start_publishing'] > 0 && ($service['start_publishing'] > $day || $service['end_publishing'] < $day))
		{
			if (!$args)
			{
				echo json_encode(array(0, JText::_('VAPFINDRESNODAYEMPLOYEE')));
				die;
			}
			else
			{
				return array();
			}
		}

		$seats = array();
		
		if ($service['max_capacity'] == 1 || $service['app_per_slot'] == 0)
		{
			// elaborate standard timeline if the maximum capacity is 1 or 
			// if the service doesn't allow multiple appointments per slot
			$arr = VikAppointments::elaborateTimeLine($worktime, $bookings, $service);
		}
		else
		{
			// calculate the timeline considering the maximum capacity of the service
			$arr = array();
			// it is needed to push the timeline within an array to be compliant with the specs of the view
			$arr[] = VikAppointments::elaborateTimeLineGroupService($worktime, $bookings, $service, $people, $seats);
		}

		VikAppointments::loadCartLibrary();
		
		$cart_properties = array( 
			VikAppointmentsCart::CART_ENABLED 	=> VikAppointments::isCartEnabled(),
			VikAppointmentsCart::ALLOW_SYNC 	=> VikAppointments::canAddSameCheckinItems(),
		);
		
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject($cart_properties);

		$_d = getdate($day);
		
		$json  = array();
		$rates = array();

		/**
		 * Keep track of all the rates found.
		 *
		 * @since 1.6.2
		 */
		$ratesTrace = array();

		for ($i = 0; $i < count($arr); $i++)
		{
			foreach ($arr[$i] as $k => $v)
			{
				$hour = floor($k / 60);
				$min  = $k % 60;

				$checkin = mktime($hour, $min, 0, $_d['mon'], $_d['mday'], $_d['year']);

				if ($arr[$i][$k] && !$auth->isEmployee() && VikAppointments::isTimeInThePast($day + $hour * 3600 + $min * 60))
				{
					// unset block if it is in the past (only when the selected day is equals to today)
					unset($arr[$i][$k]);
				}
				else if ($k >= 1440)
				{
					/**
					 * Unset the time slots that exceed the midnight.
					 *
					 * @since 1.6
					 */
					if (!$service['checkout_selection'])
					{
						/**
						 * Time-slots that exceed the midnight are mandatory for
						 * the checkout selection. Unset them only whether the
						 * "checkout_selection" parameter is turned off.
						 *
						 * @since 1.6.2
						 */
						unset($arr[$i][$k]);
					}
				}
				else if ($v == 1 && $cart_properties[VikAppointmentsCart::CART_ENABLED] && $cart->indexOf($id_ser, $id_emp, $checkin, $service['duration']) !== -1)
				{
					/**
					 * There is a conflict with the cart, disable the time.
					 *
					 * @since 1.5
					 */
					$arr[$i][$k] = 0;
				}

				if (isset($arr[$i][$k]))
				{
					$trace = array();

					// calculate rate for the current time block
					$rates[$k] = VAPSpecialRates::getRate($id_ser, $id_emp, $checkin, $people, $trace);

					if ($trace && !empty($trace['rates']))
					{
						// register rate trace
						$ratesTrace[$k] = $trace;
					}

					// multiply by the number of guests (if enabled)
					if ($service['priceperpeople'])
					{
						$rates[$k] *= $people;
					}
				}
			}

			if (count($arr[$i]))
			{
				$json[] = $arr[$i];
			}
		}

		// all times are in the past
		if (!count($json))
		{
			if (!$args)
			{
				echo json_encode(array(0, JText::_('VAPFINDRESNOLONGERAVAILABLE')));
				die;
			}
			else
			{
				return array();
			}
		}

		/**
		 * If $args, return the timeline array.
		 *
		 * @since 1.6
		 */
		if ($args)
		{
			return $json;
		}

		// build array data for timeline layout
		$data = array(
			'id_service'  => $id_ser,
			'id_employee' => $id_emp,
			'checkinDay'  => $day,
			'duration' 	  => $service['duration'],
			'times'		  => $json,
			'rates'		  => $rates,
		);

		// recalculate rate by specifing the selected day (at midnight)
		$newRate = VAPSpecialRates::getRate($id_ser, $id_emp, $day, $people);

		// multiply by the number of guests (if enabled)
		if ($service['priceperpeople'])
		{
			$newRate *= $people;
		}

		// switch timeline layout depending on the configuration of the system
		if ($service['checkout_selection'])
		{
			/**
			 * We are here as the service allows the selection of the checkout.
			 *
			 * @layout 	timeline/dropdown.php
			 */
			$layout = 'dropdown';
		}
		else if ($service['display_seats'])
		{
			/**
			 * We are here as the service needs to display the remaining seats.
			 *
			 * @layout 	timeline/seats.php
			 */
			$layout = 'seats';

			// make $seats array available for layout
			$data['seats'] 		= $seats;
			$data['totalSeats'] = $service['max_capacity'];
		}
		else if (count(array_unique($rates)) > 1 || (float) reset($rates) != VAPSpecialRates::getBaseCost($id_ser, $id_emp))
		{
			/**
			 * We are here as a result of these conditions:
			 * - the rates list contains different prices
			 * - the first element (as well as any other element) is not equals to the base cost
			 *
			 * @layout 	timeline/ratesgrid.php
			 */
			$layout = 'ratesgrid';

			// make $ratesTrace array available for layout
			$data['ratesTrace'] = $ratesTrace;
		}
		else
		{
			/**
			 * Use the standard timeline layout.
			 *
			 * @layout 	timeline/default.php
			 */
			$layout = 'default';
		}

		/**
		 * The timeline HTML block is displayed from the layout below:
		 * /components/com_vikappointments/layouts/timeline/default.php
		 * 
		 * If you need to change something from this layout, just create
		 * an override of this layout by following the instructions below:
		 * - open the back-end of your Joomla
		 * - visit the Extensions > Templates > Templates page
		 * - edit the active template
		 * - access the "Create Overrides" tab
		 * - select Layouts > com_vikappointments > timeline
		 * - start editing all the files on your template to create your own layouts
		 *
		 * @since 1.6
		 */
		$timeline = JLayoutHelper::render('timeline.' . $layout, $data);
		
		echo json_encode(array(1, $json, $timeline, $newRate));
		die;
	}
	
	/**
	 * AJAX end-point to return the availability timeline
	 * for the selected service and additional details.
	 *
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id_emp 	The employee ID (optional).
	 * @param 	integer  id_ser 	The service ID.
	 * @param 	integer  day 		The unix timestamp of the day.
	 * @param 	integer  people 	The number of guests (1 by default).
	 *
	 * @return 	void
	 *
	 * @uses 	get_day_time_line() in case the employee is provided.
	 */
	function get_day_time_line_service()
	{
		$app 	= JFactory::getApplication();
		$input	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$id_emp = $input->getInt('id_emp', 0);
		
		if ($id_emp > 0)
		{
			$this->get_day_time_line();
			// get_day_time_line will break the flow
		}

		$id_ser 	= $input->getUint('id_ser', 0);
		$day 		= $input->getUint('day', 0);
		$people 	= $input->getUint('people', 1);
		$locations 	= $input->getVar('locations', '');

		if (empty($people))
		{
			$people = 1;
		}

		// get locations
		
		if (empty($locations))
		{
			$locations = array();
		}
		else
		{
			$locations = array_map('intval', explode(',', $locations));
		}
		
		$date = getdate();

		if ($day < mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESTIMEINTHEPAST')));
			die;
		}
		
		if (VikAppointments::isClosingDay($day, $id_ser))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESCLOSINGDAY')));
			die;
		}
		
		$q = $dbo->getQuery(true);
		$q->select($dbo->qn(array('id', 'duration', 'sleep', 'interval', 'max_capacity', 'min_per_res', 'max_per_res', 'priceperpeople', 'app_per_slot', 'start_publishing', 'end_publishing')));
		$q->from($dbo->qn('#__vikappointments_service'));
		$q->where($dbo->qn('id') . ' = ' . $id_ser);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo json_encode(array(0, JText::_('VAPFINDRESSERVICENOTEXISTS')));
			die;
		}
		
		$service = $dbo->loadAssoc();
		
		if ($service['max_capacity'] > 1 && ($people > $service['max_per_res'] || $people < $service['min_per_res']))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESPEOPLENOTVALID')));
			die;
		}

		if ($service['start_publishing'] > 0 && ($service['start_publishing'] > $day || $service['end_publishing'] < $day))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESNODAYSERVICE')));			
			die;
		}
		
		$employees = VikAppointments::getEmployeesRelativeToService($id_ser);

		/**
		 * In case the service is assigned to a single employee,
		 * the get_day_time_line() method should be used instead.
		 * In order to support also the dropdown layout for 
		 * the checkout selection or for the remaining seats.
		 *
		 * @since 1.6
		 */
		if (count($employees) == 1)
		{
			// inject employee ID within the request
			$input->set('id_emp', $employees[0]['id']);
			// return day time line
			$this->get_day_time_line();
			// get_day_time_line will break the flow
		}
		
		$emp_arr = array();

		foreach ($employees as $e)
		{
			$worktime = VikAppointments::getEmployeeWorkingTimes($e['id'], $id_ser, $day, $locations);
			$bookings = VikAppointments::getAllEmployeeReservations($e['id'], $id_ser, $day, $day + 86399, $dbo);
			
			if ($service['max_capacity'] == 1 || $service['app_per_slot'] == 0)
			{
				// elaborate standard timeline if the maximum capacity is 1 or 
				// if the service doesn't allow multiple appointments per slot
				$emp_arr[] = VikAppointments::elaborateTimeLineService($worktime, $bookings, $service);
			}
			else
			{
				// calculate the timeline considering the maximum capacity of the service
				$emp_arr[] = VikAppointments::elaborateTimeLineGroupService($worktime, $bookings, $service, $people);
			}
		}
		
		$arr = array(VikAppointments::parseServiceTimeline($emp_arr));

		/**
		 * Sort employees working days to be listed in ascending ordering.
		 *
		 * @since 1.6.1
		 */
		ksort($arr[0]);

		VikAppointments::loadCartLibrary();
		
		$cart_properties = array( 
			VikAppointmentsCart::CART_ENABLED 	=> VikAppointments::isCartEnabled(),
			VikAppointmentsCart::ALLOW_SYNC 	=> VikAppointments::canAddSameCheckinItems(),
		);
		
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject($cart_properties);

		$_d = getdate($day);

		$json  = array();
		$rates = array();

		/**
		 * Keep track of all the rates found.
		 *
		 * @since 1.6.2
		 */
		$ratesTrace = array();

		for ($i = 0; $i < count($arr); $i++)
		{
			foreach ($arr[$i] as $k => $v)
			{
				$hour = floor($k / 60);
				$min  = $k % 60;

				$checkin = mktime($hour, $min, 0, $_d['mon'], $_d['mday'], $_d['year']);

				if ($arr[$i][$k] && VikAppointments::isTimeInThePast($checkin))
				{
					// unset block if it is in the past (only when the selected day is equals to today)
					unset($arr[$i][$k]);
				}
				else if ($k >= 1440)
				{
					/**
					 * Unset the time slots that exceeds the midnight.
					 *
					 * @since 1.6
					 */
					if (!$service['checkout_selection'])
					{
						/**
						 * Time-slots that exceed the midnight are mandatory for
						 * the checkout selection. Unset them only whether the
						 * "checkout_selection" parameter is turned off.
						 *
						 * @since 1.6.2
						 */
						unset($arr[$i][$k]);
					}
				}
				else if ($v == 1 && $cart_properties[VikAppointmentsCart::CART_ENABLED] && $cart->indexOf($id_ser, $id_emp, $checkin, $service['duration']) !== -1)
				{
					/**
					 * There is a conflict with the cart, disable the time.
					 *
					 * @since 1.5
					 */
					$arr[$i][$k] = 0;
				}

				if (isset($arr[$i][$k]))
				{
					$trace = array();

					// calculate rate for the current time block
					$rates[$k] = VAPSpecialRates::getRate($id_ser, $id_emp, $checkin, $people, $trace);

					if ($trace && !empty($trace['rates']))
					{
						// register rate trace
						$ratesTrace[$k] = $trace;
					}

					// multiply by the number of guests (if enabled)
					if ($service['priceperpeople'])
					{
						$rates[$k] *= $people;
					}
				}
			}

			if (count($arr[$i]))
			{
				$json[] = $arr[$i];
			}
		}

		// all times are in the past
		if (!count($json))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESNOLONGERAVAILABLE')));
			die;
		}

		// build array data for timeline layout
		$data = array(
			'id_service' => $id_ser,
			'checkinDay' => $day,
			'duration' 	 => $service['duration'],
			'times'		 => $json,
			'rates'		 => $rates,
		);

		// recalculate rate by specifing the selected day (at midnight)
		$newRate = VAPSpecialRates::getRate($id_ser, 0, $day, $people);

		// multiply by the number of guests (if enabled)
		if ($service['priceperpeople'])
		{
			$newRate *= $people;
		}

		$layout = 'default';

		/**
		 * Switch to timeline.ratesgrid layout every time a block owns a price
		 * higher or lower than the base cost of the service.
		 */
		if (count(array_unique($rates)) > 1 || (float) reset($rates) != VAPSpecialRates::getBaseCost($id_ser))
		{
			// We are here as a result of these conditions:
			// - the rates list contains different prices
			// - the first element (as well as any other element) is not equals to the base cost
			$layout = 'ratesgrid';

			// make $ratesTrace array available for layout
			$data['ratesTrace'] = $ratesTrace;
		}

		/**
		 * The timeline HTML block is displayed from the layout below:
		 * /components/com_vikappointments/layouts/timeline/default.php
		 * 
		 * If you need to change something from this layout, just create
		 * an override of this layout by following the instructions below:
		 * - open the back-end of your Joomla
		 * - visit the Extensions > Templates > Templates page
		 * - edit the active template
		 * - access the "Create Overrides" tab
		 * - select Layouts > com_vikappointments > timeline
		 * - start editing the default.php file on your template to create your own layout
		 *
		 * @since 1.6
		 */
		$timeline = JLayoutHelper::render('timeline.' . $layout, $data);
		
		echo json_encode(array(1, $json, $timeline, $newRate));
		die;
	}

	/**
	 * AJAX task used to return the availability table of a certain employee.
	 *
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id_emp 	The employee ID.
	 * @param 	integer  id_ser 	The service ID.
	 * @param 	integer  day 		The unix timestamp of the day.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 *
	 * @uses 	get_day_time_line()
	 */
	function get_employee_avail_table()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$args = array();
		$args['id_emp'] 	= $input->getUint('id_emp', 0);
		$args['id_ser'] 	= $input->getUint('id_ser', 0);
		$args['day'] 		= $input->getUint('day', 0);
		$args['people'] 	= 1;
		$args['locations'] 	= '';

		if (empty($args['day']))
		{
			$args['day'] = strtotime('today 00:00:00');
		}

		$baseday = $args['day'];

		if ($args['id_ser'] <= 0)
		{
			// service not set, get the first one available
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('s.id', 's.min_per_res')))
				->from($dbo->qn('#__vikappointments_service', 's'))
				->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('g.id') . ' = ' . $dbo->qn('s.id_group'))
				->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
				->where(array(
					$dbo->qn('a.id_employee') . ' = ' . $args['id_emp'],
					$dbo->qn('s.published') . ' = 1',
				))
				->andWhere(array(
					$dbo->qn('s.end_publishing') . ' = -1',
					$dbo->qn('s.end_publishing') . ' >= ' . $args['day'],
				), 'OR')
				->order(array(
					$dbo->qn('g.ordering') . ' ASC',
					$dbo->qn('s.ordering') . ' ASC',
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$obj = $dbo->loadObject();

				$args['id_ser'] = $obj->id;

				if ($obj->min_per_res > 1)
				{
					// update also the number of guests to be compliant with the
					// configuration of the service
					$args['people'] = $obj->min_per_res;
				}
			}
		}

		$table = array();

		/**
		 * This value determines the number of columns 
		 * to show within the table. The value should be
		 * in the range of [2-5].
		 *
		 * @var integer
		 */
		$num_iter = 4;

		/**
		 * This value is used to calculate the maximum 
		 * number of times available for a certain day.
		 *
		 * @var integer
		 */
		$max_rows = 0;

		for ($i = 1; $i <= $num_iter; $i++)
		{
			// get timeline array for the given day
			$shifts = $this->get_day_time_line($args);

			$slots = array();

			// filter the times to obtain only the available slots
			foreach ($shifts as $k => $times)
			{	
				foreach ($times as $t => $avail)
				{
					if ($avail == 1)
					{
						$slots[] = array(
							'hour' 	=> floor($t / 60),
							'min'	=> $t % 60,
						);
					}
				}

				// check if MAX ROWS should be updated
				$max_rows = max(array($max_rows, count($slots)));
			}

			// insert updated times within the table
			$table[$args['day']] = $slots;

			// increase day by one
			$args['day'] = strtotime('+1 day', $args['day']);
		}

		// calculate prev/next days to use for arrow buttons
		$prev_day = strtotime("-{$num_iter} days", $baseday);
		$next_day = strtotime("+{$num_iter} days", $baseday);

		if ($prev_day < strtotime('today 00:00:00'))
		{
			// day in the past
			$prev_day = null;
		}

		// build array data for timeline layout
		$data = array(
			'id_employee' 	=> $args['id_emp'],
			'id_service'	=> $args['id_ser'],
			'table' 		=> $table,
			'max_rows'		=> $max_rows,
			'prev_day' 		=> $prev_day,
			'next_day'		=> $next_day,
			'itemid' 		=> $input->getInt('Itemid'),
		);

		/**
		 * The time table HTML block is displayed from the layout below:
		 * /components/com_vikappointments/layouts/timeline/table.php
		 * 
		 * If you need to change something from this layout, just create
		 * an override of this layout by following the instructions below:
		 * - open the back-end of your Joomla
		 * - visit the Extensions > Templates > Templates page
		 * - edit the active template
		 * - access the "Create Overrides" tab
		 * - select Layouts > com_vikappointments > timeline
		 * - start editing the table.php file on your template to create your own layout
		 *
		 * @since 1.6
		 */
		$timetable = JLayoutHelper::render('timeline.table', $data);
		
		echo json_encode(array(1, $table, $timetable));
		die;
	}

	/**
	 * AJAX task used to validated the specified zip code.
	 *
	 * @return 	void
	 */
	function validate_zip_code()
	{	
		$zip_code = JFactory::getApplication()->input->getString('zipcode');

		VikAppointments::loadCartLibrary();
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject();

		$employees = array();
		foreach ($cart->getItemsList() as $item)
		{
			$employees[] = $item->getID2();
		}
		
		$_resp = VikAppointments::validateZipCode($zip_code, array_unique($employees, SORT_NUMERIC));
		
		echo json_encode(array($_resp));
		die;
	}

	/**
	 * AJAX task used to validated the specified zip code
	 * for the given employee and service.
	 *
	 * @return 	void
	 */
	function validate_zip_code_service()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$id_ser 	= $input->getInt('id_ser', 0);
		$id_emp 	= $input->getInt('id_emp', 0);
		$zip_code 	= $input->getString('zip', '');
		
		if (VikAppointments::getZipCodeValidationFieldId($id_emp) == -1)
		{
			// zip disabled, return success
			echo json_encode(array(1));
			die;
		}

		if (empty($id_emp))
		{
			$id_emp = -1;
		}

		$q = $dbo->getQuery(true)
			->select('enablezip')
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . $id_ser);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			if ($dbo->loadResult() == 0)
			{
				// enable zip disabled, return success
				echo json_encode(array(1));
				die;
			}
		}
		else
		{
			// service not found, return failure
			echo json_encode(array(0));
			die;
		}
		
		$_resp = VikAppointments::validateZipCode($zip_code, array($id_emp), true);
		
		echo json_encode(array($_resp));
		die;
	}
	
	/**
	 * AJAX task used to return the list of employees
	 * assigned to the specified service.
	 *
	 * This task is used by the SEARCH module to obtain
	 * the employees after switching value from the services
	 * dropdown.
	 *
	 * @return 	void
	 */
	function get_employees_rel_service()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$id_ser = $input->getUint('id_ser', 0);

		$q = $dbo->getQuery(true)
			->select($dbo->qn('choose_emp'))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . $id_ser);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// service not found, error
			echo json_encode(array(0));
			die;
		}
		
		$service = $dbo->loadAssoc();
		
		$employees = array();
		
		// make sure the employee selection is allowed
		if ($service['choose_emp'])
		{
			// true is used to sort the employees by name
			$employees = VikAppointments::getEmployeesRelativeToService($id_ser, true, $dbo);
			
			if (!count($employees))
			{
				// the service is not assigned to any employee
				echo json_encode(array(0));
				die;
			}
		}
		
		echo json_encode(array(1, $service['choose_emp'], $employees));
		die;
	}
	
	/**
	 * AJAX task to empty the appointments cart.
	 *
	 * This task is used by the CART module.
	 *
	 * @return 	void
	 */
	function empty_cart_rq()
	{	
		VikAppointments::loadCartLibrary();
		
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject();
		
		/**
		 * Trigger event after flushing the cart.
		 *
		 * @param 	mixed 	$cart 	The cart instance.
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		UIFactory::getEventDispatcher()->trigger('onEmptyCart', array($cart));

		$cart->emptyCart();
		$core->storeCart($cart);
		
		$this->revalidateCouponCode($cart);
		
		die;
	}
	
	/**
	 * Method used to add an item into the cart.
	 * This action can be reached also via AJAX.
	 *
	 * @param 	array  	 $args 	The arguments to use. If not provided
	 * 							they will be taken from the request.
	 * @param 	boolean  $ajax 	True if the task has been called via AJAX.
	 *
	 * @return 	void|boolean 	If not ajax, the result of the action.
	 *
	 * @uses 	revalidateCouponCode 		Revalidate the coupon code to make sure it is still accepted.
	 * @uses 	hasEmptyRequiredOptions() 	Checks if all the required options have been provided.
	 */
	function add_item_cart_rq($args = array(), $ajax = true)
	{
		$app 		= JFactory::getApplication();
		$input 		= $app->input;
		$dbo 		= JFactory::getDbo();
		$config 	= UIFactory::getConfig();
		$dispatcher = UIFactory::getEventDispatcher();
		
		// get params from request
		
		if ($args)
		{
			$id_ser 	= $args['id_ser'];
			$id_emp 	= $args['id_emp'];
			$ts 		= $args['ts'];
			$ts_hour 	= $args['ts_hour'];
			$ts_min 	= $args['ts_min'];
			$people 	= $args['people'];
		}
		else
		{
			$id_ser 	= $input->getUint('id_ser', 0);
			$id_emp 	= $input->getInt('id_emp', 0);
			$ts 		= $input->getUint('ts', 0);
			$ts_hour 	= $input->getUint('ts_hour', 0);
			$ts_min 	= $input->getUint('ts_min', 0);
			$people 	= $input->getUint('people', 1);
		}

		// do not accept 0 as value
		$people = max(array(1, $people));
		
		$opt_ids_str 	= $input->getString('opt_ids', '');
		$opt_quant_str 	= $input->getString('opt_quant', '');
		$opt_vars_str 	= $input->getString('opt_vars', '');
		
		$opt_ids 	= array();
		$opt_quant 	= array();
		$opt_vars 	= array();

		if (strlen($opt_ids_str))
		{
			$opt_ids 	= explode(',', $opt_ids_str);
			$opt_quant 	= explode(',', $opt_quant_str);
			$opt_vars 	= explode(',', $opt_vars_str);
		}

		// set employee timezone

		$emp_tz = VikAppointments::getEmployeeTimezone($id_emp, $dbo);
		VikAppointments::setCurrentTimezone($emp_tz);
		
		$ts_exp = explode('-', ArasJoomlaVikApp::jdate('Y-m-d', $ts));
		$ts = ArasJoomlaVikApp::jmktime($ts_hour, $ts_min, 0, intval($ts_exp[1]), intval($ts_exp[2]), intval($ts_exp[0]));

		// validate options
		
		$empty_options = $this->hasEmptyRequiredOptions($id_ser, $opt_ids);

		if ($empty_options !== false)
		{
			if ($ajax)
			{
				// -5 : identifier for empty options error
				echo json_encode(array(0, JText::_('VAPOPTIONREQUIREDERR'), -5, $empty_options));
				die;
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPOPTIONREQUIREDERR'), 'error');
				return false;
			}
		}

		// get service details and overrides

		$q = $dbo->getQuery(true);
		
		$q->select(array(
			$dbo->qn('s.name', 'ser_name'),
			$dbo->qn('s.price'),
			$dbo->qn('s.duration'),
			$dbo->qn('s.choose_emp'),
			$dbo->qn('s.priceperpeople'),
			$dbo->qn('s.checkout_selection'),
		));

		$q->select(array(
			$dbo->qn('e.firstname'),
			$dbo->qn('e.lastname'),
		));

		$q->select(array(
			$dbo->qn('a.rate'),
			$dbo->qn('a.duration', 'assoc_duration'),
		));

		$q->from($dbo->qn('#__vikappointments_service', 's'));
		$q->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'));
		$q->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('a.id_employee'));

		$q->where(array(
			$dbo->qn('s.id') . ' = ' . (int) $id_ser,
			/**
			 * Make sure the service is published.
			 *
			 * @since 1.6
			 */
			$dbo->qn('s.published') . ' = 1',
		));
		$q->andWhere(array(
			$dbo->qn('e.id') . ' = ' . (int) $id_emp,
			$dbo->qn('s.choose_emp') . ' = 0',
		), 'OR');
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// the service doesn't exist
			if ($ajax)
			{
				echo json_encode(array(0, JText::_('VAPSERNOTFOUNDERROR')));
				die;
			}
			else
			{
				return false;
			}
		}
		
		// ignore availability check
		
		$args = $dbo->loadAssoc();
		
		// get translations
		$lang_services = VikAppointments::getTranslatedServices($id_ser);
		
		// establish service price and item title, details
		
		$date_format = $config->get('dateformat');
		$time_format = $config->get('timeformat');
		
		$ser_name 	= VikAppointments::getTranslation($id_ser, $args, $lang_services, 'ser_name', 'name');
		$emp_name 	= '';
		$details 	= $ser_name;
		$duration 	= $args['assoc_duration'];

		/**
		 * The price is calculated using the special rates.
		 *
		 * @since 1.6
		 */
		$price = VAPSpecialRates::getRate($id_ser, $id_emp, $ts, $people);

		if (!$args['choose_emp'] && $id_emp == -1)
		{
			$duration = $args['duration'];
		}
		else
		{
			$emp_name = $args['firstname'] . ' ' . $args['lastname'];
			$details .= ' - ' . $emp_name;
		}
		
		if ($args['priceperpeople'] && $price > 0)
		{
			$price *= $people;
		}

		/**
		 * In case the checkout selection is allowed, we need to extend the price
		 * and the duration by the number of selected slots.
		 *
		 * @since 1.6
		 */
		if ($args['checkout_selection'])
		{
			$factor 	= max(array(1, $input->getUint('duration_factor', 1)));
			$duration 	*= $factor;
			$price 		*= $factor;
		}
		else
		{
			$factor = 1;
		}

		VikAppointments::setCurrentTimezone($emp_tz);
		
		/**
		 * D 	A textual representation of a day, three letters 			Mon through Sun
		 * d 	Day of the month, 2 digits with leading zeros 				01 to 31
		 * F 	A full textual representation of a month 					January through December 		
		 * Y 	A full numeric representation of a year, 4 digits	 		1999 or 2003
		 */
		$det_date = ArasJoomlaVikApp::jdate('D:d:F:Y:' . $time_format, $ts);
		$det_date = explode(':', $det_date);

		$week_day 	= JText::_(strtoupper($det_date[0]));
		$month_name = JText::_(strtoupper($det_date[2]));
		
		// e.g. @ Fri 18, May 2018 12:28 
		$details .= ' @ ' . $week_day . ' ' . $det_date[1] . ', ' . $month_name . ' ' . $det_date[3] . ' ' . $det_date[4] . ':' . $det_date[5];
		
		// load cart instance
		
		VikAppointments::loadCartLibrary();
		
		$cart_properties = array( 
			VikAppointmentsCart::CART_ENABLED 	=> VikAppointments::isCartEnabled(),
			VikAppointmentsCart::MAX_SIZE 		=> VikAppointments::getMaxCartSize(),
			VikAppointmentsCart::ALLOW_SYNC 	=> VikAppointments::canAddSameCheckinItems(),
		);
		
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject($cart_properties);

		/**
		 * Obtain the location even if the employee is not choosable.
		 * In this case the location of the first employee found will be taken.
		 *
		 * @since 1.6
		 */
		$location = VikAppointments::getEmployeeLocationFromTime($id_emp, $id_ser, $ts);

		if ($location !== false)
		{
			$loc_str = VikAppointments::locationToString(VikAppointments::fillEmployeeLocation($location));

			if (!empty($loc_str))
			{
				$details .= "<br/>" . $loc_str;
			}
		}
		
		// create new cart item
		$item = new VikAppointmentsItem($id_ser, $id_emp, $ser_name, $emp_name, $price, $duration, $ts, $people, $details);
		$item->setFactor($factor);
		
		// validate specified options
		for ($i = 0; $i < count($opt_ids); $i++)
		{
			$q = $dbo->getQuery(true);

			$q->select($dbo->qn(array('o.id', 'o.name', 'o.price', 'o.maxq', 'o.required')));
			$q->select(array(
				$dbo->qn('v.id', 'id_variation'),
				$dbo->qn('v.name', 'var_name'),
				$dbo->qn('v.inc_price', 'var_price'),
			));

			$q->from($dbo->qn('#__vikappointments_option', 'o'));
			$q->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'));
			$q->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('v.id_option'));

			$q->where(array(
				$dbo->qn('o.id') . ' = ' . (int) $opt_ids[$i],
				$dbo->qn('o.published') . ' = 1',
				$dbo->qn('a.id_service') . ' = ' . (int) $id_ser,
			));
			$q->andWhere(array(
				(int) $opt_vars[$i] . ' = -1',
				$dbo->qn('v.id') . ' = ' . (int) $opt_vars[$i],
			), 'OR');

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$opt 	= $dbo->loadAssoc();
				$quant 	= min(array(intval($opt_quant[$i]), $opt['maxq']));
				
				$lang_options = VikAppointments::getTranslatedOptions($opt['id']);
				
				$opt['name'] 	= VikAppointments::getTranslation($opt['id'], $opt, $lang_options, 'name', 'name');
				$vars_json 		= VikAppointments::getTranslation($opt['id'], $opt, $lang_options, 'vars_json', 'vars_json', array());

				$opt['var_name'] = !empty($vars_json[$opt['id_variation']]) ? $vars_json[$opt['id_variation']] : $opt['var_name'];

				$opt_name = $opt['name'].(strlen($opt['var_name']) ? " - ".$opt['var_name'] : "");

				$option = new VikAppointmentsOption($opt['id'], $opt['id_variation'], $opt_name, $opt['price'] + $opt['var_price'], $opt['maxq'], $opt['required'], $quant);

				/**
				 * Trigger event before adding an option to the cart item.
				 *
				 * @param 	mixed 	 $item 		The cart item object.
				 * @param 	mixed 	 &$option 	The item option object.
				 *
				 * @return 	boolean  False to avoid adding the option.
				 *
				 * @since 	1.6
				 */
				if (!$dispatcher->not('onAddOptionCart', array($item, &$option)))
				{
					// push option in case no plugin was triggered
					// or in case we got only positive results
					$item->addOption($option);
				}
			}
		}

		// junk variable used by plugins to set custom errors
		$err = '';

		/**
		 * Trigger event before adding an item into the cart.
		 *
		 * @param 	mixed 	 $cart 	 The cart instance.
		 * @param 	mixed 	 &$item  The cart item object.
		 * @param 	string 	 &$err 	 String used to raise custom errors.
		 *
		 * @return 	boolean  False to avoid adding the item.
		 *
		 * @since 	1.6
		 */
		if ($dispatcher->not('onAddItemCart', array($cart, &$item, &$err)))
		{
			// Avoid pushing the item into the cart in case at least a plugin
			// returns a negative value. If no plugin is attached to this event,
			// the item will be added correctly.

			if ($ajax)
			{
				// raise custom error
				echo json_encode(array(0, $err ? $err : JText::_('ERROR')));
				exit;
			}
			else
			{
				return false;
			}
		}
		
		// push item into the cart
		$res = $cart->addItem($item);
		
		// check item integrity on cart
		if (!$res)
		{
			// an error occurred while inserting the item
			if ($ajax)
			{
				if (!VikAppointments::canAddItemToCart($cart->getCartRealLength()))
				{
					// limit reached
					echo json_encode(array(0, JText::_('VAPCARTITEMADDERR1')));
				}
				else
				{
					// service already in the cart
					echo json_encode(array(0, JText::_('VAPCARTITEMADDERR2')));
				}
				die;
			}
			else
			{
				return false;
			}
		}
		
		// store the cart instance
		$core->storeCart($cart);
		
		// re-validate coupon code
		$coupon = $this->revalidateCouponCode($cart);
			
		// get the sum of total cost for each service (of this kind) in the cart 
		$group_item_t_cost = VikAppointmentsCartUtils::getServiceTotalCost(VikAppointmentsCartUtils::sortItemsByServiceDate($cart->getItemsList()), $id_ser);
		
		if ($ajax)
		{
			$df = $date_format . ' ' . $time_format;
			
			// get discounted cost
			$discount_t_cost = VikAppointments::getDiscountTotalCost($cart->getTotalCost(), $coupon);
			
			echo json_encode(array(1, $item->toArray(), $cart->getTotalCost(), $group_item_t_cost, $item->getCheckinDate($df), $discount_t_cost));
			die;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Method used to add an item into the cart with recurrence.
	 * This action can be reached also via AJAX.
	 *
	 * @param 	array  	 $args 	The arguments to use. If not provided
	 * 							they will be taken from the request.
	 * @param 	boolean  $ajax 	True if the task has been called via AJAX.
	 *
	 * @return 	void|boolean 	If not ajax, the result of the action.
	 *
	 * @uses 	add_item_cart_rq() 					In case the recurrence params are empty.
	 * @uses 	validateRecurringAvailability() 	Checks the recurring availability.
	 * @uses 	revalidateCouponCode 				Revalidate the coupon code to make sure it is still accepted.
	 * @uses 	hasEmptyRequiredOptions() 			Checks if all the required options have been provided.
	 */
	function add_recur_item_cart_rq($args = array(), $ajax = true)
	{
		$app 		= JFactory::getApplication();
		$input 		= $app->input;
		$dbo 		= JFactory::getDbo();
		$config 	= UIFactory::getConfig();
		$dispatcher = UIFactory::getEventDispatcher();
		
		// vaidate recurrence [repeat, for, amount]
		$recurrence = $input->getString('recurrence');
		
		if (empty($recurrence))
		{
			$recurrence = '-1,-1,-1';
		}
		
		$recurrence = explode(',', $recurrence);
		if (!VikAppointments::validateRecurringData($recurrence[0], $recurrence[2], $recurrence[1]))
		{
			// This function dies and remove the next instructions from the execution stack (in case of AJAX).
			// Otherwise it will return the value to the caller.
			return $this->add_item_cart_rq($args, $ajax);
		}
		
		// get params from request
		
		if ($args)
		{
			$id_ser 	= $args['id_ser'];
			$id_emp 	= $args['id_emp'];
			$ts 		= $args['ts'];
			$ts_hour 	= $args['ts_hour'];
			$ts_min 	= $args['ts_min'];
			$people 	= $args['people'];
		}
		else
		{
			$id_ser 	= $input->getUint('id_ser', 0);
			$id_emp 	= $input->getInt('id_emp', 0);
			$ts 		= $input->getUint('ts', 0);
			$ts_hour 	= $input->getUint('ts_hour', 0);
			$ts_min 	= $input->getUint('ts_min', 0);
			$people 	= $input->getUint('people', 1);
		}

		// do not accept 0 as value
		$people = max(array(1, $people));
		
		$opt_ids_str 	= $input->getString('opt_ids', '');
		$opt_quant_str 	= $input->getString('opt_quant', '');
		$opt_vars_str 	= $input->getString('opt_vars', '');
		
		$opt_ids 	= array();
		$opt_quant 	= array();
		$opt_vars 	= array();

		if (strlen($opt_ids_str))
		{
			$opt_ids 	= explode(',', $opt_ids_str);
			$opt_quant 	= explode(',', $opt_quant_str);
			$opt_vars 	= explode(',', $opt_vars_str);
		}

		// set employee timezone

		$emp_tz = VikAppointments::getEmployeeTimezone($id_emp, $dbo);
		VikAppointments::setCurrentTimezone($emp_tz);
		
		$ts_exp = explode('-', date('Y-m-d', $ts));
		$ts = mktime($ts_hour, $ts_min, 0, intval($ts_exp[1]), intval($ts_exp[2]), intval($ts_exp[0]));

		// validate options
		
		$empty_options = $this->hasEmptyRequiredOptions($id_ser, $opt_ids);

		if ($empty_options !== false)
		{
			if ($ajax)
			{
				// -5 : identifier for empty options error
				echo json_encode(array(0, JText::_('VAPOPTIONREQUIREDERR'), -5, $empty_options));
				die;
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPOPTIONREQUIREDERR'), 'error');
				return false;
			}
		}
		
		// get service details and overrides

		$q = $dbo->getQuery(true);
		
		$q->select(array(
			$dbo->qn('s.name', 'ser_name'),
			$dbo->qn('s.price'),
			$dbo->qn('s.duration'),
			$dbo->qn('s.choose_emp'),
			$dbo->qn('s.priceperpeople'),
			$dbo->qn('s.checkout_selection'),
		));

		$q->select(array(
			$dbo->qn('e.firstname'),
			$dbo->qn('e.lastname'),
		));

		$q->select(array(
			$dbo->qn('a.rate'),
			$dbo->qn('a.duration', 'assoc_duration'),
		));

		$q->from($dbo->qn('#__vikappointments_service', 's'));
		$q->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'));
		$q->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('a.id_employee'));

		$q->where(array(
			$dbo->qn('s.id') . ' = ' . (int) $id_ser,
			/**
			 * Make sure the service is published.
			 *
			 * @since 1.6
			 */
			$dbo->qn('s.published') . ' = 1',
		));
		$q->andWhere(array(
			$dbo->qn('e.id') . ' = ' . (int) $id_emp,
			$dbo->qn('s.choose_emp') . ' = 0',
		), 'OR');
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// the service doesn't exist
			if ($ajax)
			{
				echo json_encode(array(0, JText::_('VAPSERNOTFOUNDERROR')));
				die;
			}
			else
			{
				return false;
			}
		}

		$args = $dbo->loadAssoc();
		
		// load cart instance
		VikAppointments::loadCartLibrary();
			
		$cart_properties = array( 
			VikAppointmentsCart::CART_ENABLED 	=> VikAppointments::isCartEnabled(),
			VikAppointmentsCart::MAX_SIZE 		=> VikAppointments::getMaxCartSize(),
			VikAppointmentsCart::ALLOW_SYNC 	=> VikAppointments::canAddSameCheckinItems(),
		);
		
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject($cart_properties);
		
		// compose timestamp recurrence
		
		$mon_day_len = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$arr = getdate($ts);
		
		$timestamp_array = array($ts);
		
		$steps = 1;
		// repeat every
		if ($recurrence[0] == 2)
		{
			// Weeks
			$steps = 7;
		}
		else if ($recurrence[0] == 3)
		{
			// Months
			$steps = $mon_day_len[$arr['mon'] - 1];
		}
		
		// recurrence amount
		$num_iter = $recurrence[2];
		
		// for the next
		if ($recurrence[1] == 2)
		{
			// Weeks
			$num_iter *= 7;
		}
		else if ($recurrence[1] == 3)
		{
			// Months
			$num_iter *= 31;
		}
		
		for ($i = $steps; $i <= $num_iter; $i += $steps)
		{

			$date 		= getdate($ts);
			$_eval_ts 	= mktime($date['hours'], $date['minutes'], 86400 * $i, $date['mon'], $date['mday'], $date['year']);
			
			$timestamp_array[] = $_eval_ts;

			if ($recurrence[0] == 3)
			{
				// update the number of steps depending on the length of the month
				$arr = getdate($_eval_ts);
				$steps = $mon_day_len[$arr['mon'] - 1];
			}
		}
		
		// validate specified options
		
		$options_to_add = array();
		for ($i = 0; $i < count($opt_ids); $i++)
		{
			$q = $dbo->getQuery(true);

			$q->select($dbo->qn(array('o.id', 'o.name', 'o.price', 'o.maxq', 'o.required')));
			$q->select(array(
				$dbo->qn('v.id', 'id_variation'),
				$dbo->qn('v.name', 'var_name'),
				$dbo->qn('v.inc_price', 'var_price'),
			));

			$q->from($dbo->qn('#__vikappointments_option', 'o'));
			$q->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'));
			$q->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('v.id_option'));

			$q->where(array(
				$dbo->qn('o.id') . ' = ' . (int) $opt_ids[$i],
				$dbo->qn('o.published') . ' = 1',
				$dbo->qn('a.id_service') . ' = ' . (int) $id_ser,
			));
			$q->andWhere(array(
				(int) $opt_vars[$i] . ' = -1',
				$dbo->qn('v.id') . ' = ' . (int) $opt_vars[$i],
			), 'OR');

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$opt 	= $dbo->loadAssoc();
				$quant 	= min(array(intval($opt_quant[$i]), $opt['maxq']));
				
				$lang_options = VikAppointments::getTranslatedOptions($opt['id']);
				
				$opt['name'] 	= VikAppointments::getTranslation($opt['id'], $opt, $lang_options, 'name', 'name');
				$vars_json 		= VikAppointments::getTranslation($opt['id'], $opt, $lang_options, 'vars_json', 'vars_json', array());

				$opt['var_name'] = !empty($vars_json[$opt['id_variation']]) ? $vars_json[$opt['id_variation']] : $opt['var_name'];

				$opt_name = $opt['name'] . (strlen($opt['var_name']) ? " - " . $opt['var_name'] : "");
				
				// push option
				$options_to_add[] = array(
					'id' 			=> $opt['id'],
					'id_variation' 	=> $opt['id_variation'],
					'name' 			=> $opt_name,
					'price' 		=> $opt['price'] + $opt['var_price'],
					'maxq' 			=> $opt['maxq'],
					'required' 		=> $opt['required'],
					'quant' 		=> $quant,
				);
			}
		}
		
		$date_format = $config->get('dateformat');
		$time_format = $config->get('timeformat');

		$df = $date_format . ' ' . $time_format;
		
		$items_validation = array();
		$ok_items_count = 0;
		
		foreach ($timestamp_array as $t)
		{
			// get translations
			$lang_services = VikAppointments::getTranslatedServices($id_ser);
		
			// establish service price and item title, details
					
			$ser_name 	= VikAppointments::getTranslation($id_ser, $args, $lang_services, 'ser_name', 'name');
			$emp_name 	= '';
			$details 	= $ser_name;
			$duration 	= $args['assoc_duration'];

			/**
			 * The price is calculated using the special rates.
			 *
			 * @since 1.6
			 */
			$price = VAPSpecialRates::getRate($id_ser, $id_emp, $ts, $people);

			if (!$args['choose_emp'] && $id_emp == -1)
			{
				$duration = $args['duration'];
			}
			else
			{
				$emp_name = $args['firstname'] . ' ' . $args['lastname'];
				$details .= ' - ' . $emp_name;
			}
			
			if ($args['priceperpeople'] && $price > 0)
			{
				$price *= $people;
			}

			/**
			 * In case the checkout selection is allowed, we need to extend the price
			 * and the duration by the number of selected slots.
			 *
			 * @since 1.6
			 */
			if ($args['checkout_selection'])
			{
				$factor 	= max(array(1, $input->getUint('duration_factor', 1)));
				$duration 	*= $factor;
				$price 		*= $factor;
			}
			else
			{
				$factor = 1;
			}

			VikAppointments::setCurrentTimezone($emp_tz);

			// validate timestamp
			$avail = true;

			if ($ajax)
			{
				$avail = $this->validateRecurringAvailability($id_ser, $id_emp, $t, $people, $factor, $dbo);
			}
			else
			{
				/**
				 * Ignore the validation when the function is called manually as
				 * the confirmapp view makes the validation of the availability
				 * every time the confirmation page is visited.
				 * So, in case of conflict, the error would be raised by the
				 * confirmapp() task.
				 */
			}

			VikAppointments::setCurrentTimezone($emp_tz);
			
			/**
			 * D 	A textual representation of a day, three letters 			Mon through Sun
			 * d 	Day of the month, 2 digits with leading zeros 				01 to 31
			 * F 	A full textual representation of a month 					January through December 		
			 * Y 	A full numeric representation of a year, 4 digits	 		1999 or 2003
			 */
			$det_date = date('D:d:F:Y:' . $time_format, $ts);
			$det_date = explode(':', $det_date);

			$week_day 	= JText::_(strtoupper($det_date[0]));
			$month_name = JText::_(strtoupper($det_date[2]));
			
			// e.g. @ Fri 18, May 2018 12:28 
			$details .= ' @ ' . $week_day . ' ' . $det_date[1] . ', ' . $month_name . ' ' . $det_date[3] . ' ' . $det_date[4] . ':' . $det_date[5];
			
			// elaborate location details

			/**
			 * Obtain the location even if the employee is not choosable.
			 * In this case the location of the first employee found will be taken.
			 *
			 * @since 1.6
			 */
			$location = VikAppointments::getEmployeeLocationFromTime($id_emp, $id_ser, $ts);
			
			if ($location !== false)
			{
				$loc_str = VikAppointments::locationToString(VikAppointments::fillEmployeeLocation($location));
				
				if (!empty($loc_str))
				{
					$details .= "<br/>" . $loc_str;
				}
			}
			
			// create new item
			$item = new VikAppointmentsItem($id_ser, $id_emp, $ser_name, $emp_name, $price, $duration, $t, $people, $details);
			$item->setFactor($factor);
			
			// push options into the item
			foreach ($options_to_add as $opt)
			{ 
				$option = new VikAppointmentsOption(
					$opt['id'],
					$opt['id_variation'],
					$opt['name'],
					$opt['price'],
					$opt['maxq'],
					$opt['required'],
					$opt['quant']
				);

				/**
				 * Trigger event before adding an option to the cart item.
				 *
				 * @param 	mixed 	 $item 		The cart item object.
				 * @param 	mixed 	 &$option 	The item option object.
				 *
				 * @return 	boolean  False to avoid adding the option.
				 *
				 * @since 	1.6
				 */
				if (!$dispatcher->not('onAddOptionCart', array($item, &$option)))
				{
					// push option in case no plugin was triggered
					// or in case we got only positive results
					$item->addOption($option);
				}
			}

			// junk variable used by plugins to set custom errors
			$err = '';
			$res = 0;

			/**
			 * Trigger event before adding an item into the cart.
			 *
			 * @param 	mixed 	 $cart 	 The cart instance.
			 * @param 	mixed 	 &$item  The cart item object.
			 * @param 	string 	 &$err 	 String used to raise custom errors.
			 *
			 * @return 	boolean  False to avoid adding the item.
			 *
			 * @since 	1.6
			 */
			if ($dispatcher->not('onAddItemCart', array($cart, &$item, &$err)))
			{
				// Avoid pushing the item into the cart in case at least a plugin
				// returns a negative value. If no plugin is attached to this event,
				// the item will be added correctly.

				$avail = false;
				$err   = $err ? $err : JText::_('ERROR');
			}

			if ($avail)
			{
				// push item in cart only if the servce is available for the current day
				$res = $cart->addItem($item);
			}
			
			// check item integrity on cart
			
			if (!$res)
			{
				$checkin_date = $item->getCheckinDate($df);

				if ($err)
				{
					// raise custom error
					$items_validation[] = array(0, $err);
				}
				else if (!VikAppointments::canAddItemToCart($cart->getCartRealLength()))
				{
					 // max cart length reached
					$items_validation[] = array(0, JText::sprintf('VAPCARTRECURITEMERR1', $checkin_date), $item->toArray(), $checkin_date);
				}
				else if ($avail)
				{
					 // item already in cart
					$items_validation[] = array(-1, JText::sprintf('VAPCARTRECURITEMERR2', $checkin_date), $item->toArray(), $checkin_date);
				}
				else
				{
					 // item not available
					$items_validation[] = array(-2, JText::sprintf('VAPCARTRECURITEMERR3', $checkin_date), $item->toArray(), $checkin_date);
				}
			}
			else
			{
				 // item added
				$items_validation[] = array(1, 'OK', $item->toArray(), $item->getCheckinDate($df));
				$ok_items_count++;
			}
		}
		
		// store cart changes
		$core->storeCart($cart);
		
		// re-validate coupon
		$coupon = $this->revalidateCouponCode($cart);
		
		// get the sum of total cost for each service (of this kind) in the cart 
		$group_item_t_cost = VikAppointmentsCartUtils::getServiceTotalCost(VikAppointmentsCartUtils::sortItemsByServiceDate($cart->getItemsList()), $id_ser);
		
		if ($ajax)
		{	
			$discount_t_cost = VikAppointments::getDiscountTotalCost($cart->getTotalCost(), $coupon);
			
			echo json_encode(array(2, $ok_items_count, $items_validation, $cart->getTotalCost(), $group_item_t_cost, $discount_t_cost));
			die;
		}
		else
		{
			return true;
		}
	}

	/**
	 * AJAX task used to removed the specified item from the cart.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id 	The service ID.
	 * @param 	integer  id2 	The employee ID.
	 * @param 	integer  ts 	The checkin timestamp.
	 * 
	 * @return 	void
	 */
	function remove_item_cart_rq()
	{
		$app 		= JFactory::getApplication();
		$input		= $app->input;
		$dbo 		= JFactory::getDbo();
		$dispatcher = UIFactory::getEventDispatcher();
		
		$id_ser = $input->getInt('id', 0);
		$id_emp = $input->getInt('id2', 0);
		$ts 	= $input->getInt('ts', 0);

		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
			->where($dbo->qn('s.id') . ' = ' . $id_ser)
			->andWhere(array(
				$dbo->qn('a.id_employee') . ' = ' . $id_emp,
				$dbo->qn('s.choose_emp') . ' = 0',
			), 'OR');
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo json_encode(array(0, JText::_('VAPSERNOTFOUNDERROR')));
			die;
		}
		
		// remove item from cart
		
		VikAppointments::loadCartLibrary();
		
		$cart_properties = array( 
			VikAppointmentsCart::CART_ENABLED 	=> VikAppointments::isCartEnabled(),
			VikAppointmentsCart::MAX_SIZE 		=> VikAppointments::getMaxCartSize(),
			VikAppointmentsCart::ALLOW_SYNC 	=> VikAppointments::canAddSameCheckinItems(),
		);
		
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject($cart_properties);

		/**
		 * Trigger event before deleting an item from the cart.
		 *
		 * @param 	mixed 	 $cart 		The cart instance.
		 * @param 	integer  $id_ser 	The service ID.
		 * @param 	integer  $id_emp 	The employee ID.
		 * @param 	integer  $ts 		The checkin timestamp.
		 *
		 * @return 	boolean  False to avoid deleting the item.
		 *
		 * @since 	1.6
		 */
		if ($dispatcher->not('onRemoveItemCart', array($cart, $id_ser, $id_emp, $ts)))
		{
			// Avoid deleting the item into the cart in case at least a plugin
			// returns a negative value. If no plugin is attached to this event,
			// the item will be removed correctly.

			echo json_encode(array(0, JText::_('VAPCARTITEMDELERR')));
			die;
		}
		
		$res = $cart->removeItem($id_ser, $id_emp, $ts);
		
		// check item removed
		
		if (!$res)
		{
			echo json_encode(array(0, JText::_('VAPCARTITEMDELERR')));
			die;
		}
		
		//$cart->balance();
		
		$core->storeCart($cart);
		
		$coupon = $this->revalidateCouponCode($cart);
		
		$group_item_t_cost = VikAppointmentsCartUtils::getServiceTotalCost(VikAppointmentsCartUtils::sortItemsByServiceDate($cart->getItemsList()), $id_ser);

		/**
		 * Calculate discount considering the user credit.
		 *
		 * @since 1.6
		 */
		$credit 	= true;
		$creditUsed = 0.0;
		$disc_title = '';
		
		$discount_t_cost = VikAppointments::getDiscountTotalCost($cart->getTotalCost(), $coupon, $credit, $creditUsed);

		if ($creditUsed > 0)
		{
			if ($creditUsed < $credit)
			{
				$disc_title = JText::sprintf(
					'VAPUSERCREDITUSED',
					VikAppointments::printPriceCurrencySymb($credit),
					VikAppointments::printPriceCurrencySymb($creditUsed)
				);
			}
			else
			{
				$disc_title = JText::sprintf(
					'VAPUSERCREDITFINISHED',
					VikAppointments::printPriceCurrencySymb($credit)
				);
			}
		}
		//

		$return = '';

		if ($cart->isEmpty())
		{
			$url = 'index.php?option=com_vikappointments&view=servicesearch&id_ser=' . $id_ser;

			if ($id_emp > 0)
			{
				$url .= '&id_emp=' . $id_emp;
			}

			$date = date(VikAppointments::getDateFormat(), $ts);
			$url .= '&date=' . $date;

			$itemid = $input->getInt('Itemid');

			if ($itemid)
			{
				$url .= '&Itemid=' . $itemid;
			}

			$return = JRoute::_($url, false);
		}
		
		// return item details : successful(1), cart(total cost), cart(is empty), group(items total cost), cart(total cost - discount), discount tooltip, redirect url
		
		echo json_encode(array(1, $cart->getTotalCost(), ($cart->isEmpty() ? 1 : 0), $group_item_t_cost, $discount_t_cost, $disc_title, $return));
		die;
	}
	
	/**
	 * AJAX task used to removed the specified option from
	 * a certain item stored into the cart.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id_opt  The option ID.
	 * @param 	integer  id 	 The service ID.
	 * @param 	integer  id2 	 The employee ID.
	 * @param 	integer  ts 	 The checkin timestamp.
	 * 
	 * @return 	void
	 */
	function remove_option_cart_rq()
	{	
		$app 		= JFactory::getApplication();
		$input		= $app->input;
		$dbo 		= JFactory::getDbo();
		$dispatcher = UIFactory::getEventDispatcher();
		
		$id_opt = $input->getInt('id_opt', 0);
		$id_ser = $input->getInt('id', 0);
		$id_emp = $input->getInt('id2', 0);
		$ts 	= $input->getInt('ts', 0);

		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
			->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'ao') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('ao.id_service'))
			->where(array(
				$dbo->qn('s.id') . ' = ' . $id_ser,
				$dbo->qn('ao.id_option') . ' = ' . $id_opt,
			))
			->andWhere(array(
				$dbo->qn('a.id_employee') . ' = ' . $id_emp,
				$dbo->qn('s.choose_emp') . ' = 0',
			), 'OR');
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo json_encode(array(0, JText::_('VAPOPTNOTFOUNDERROR')));
			die;
		}
		
		// remove item from cart
		
		VikAppointments::loadCartLibrary();
		
		$cart_properties = array( 
			VikAppointmentsCart::CART_ENABLED 	=> VikAppointments::isCartEnabled(),
			VikAppointmentsCart::MAX_SIZE 		=> VikAppointments::getMaxCartSize(),
			VikAppointmentsCart::ALLOW_SYNC 	=> VikAppointments::canAddSameCheckinItems(),
		);
		
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject($cart_properties);
		
		$item = $cart->getItemAt($cart->indexOf($id_ser, $id_emp, $ts));

		/**
		 * Trigger event before detaching an option from the item.
		 *
		 * @param 	integer  $id_opt 	The option ID.
		 * @param 	mixed 	 $item 		The cart item instance.
		 *
		 * @return 	boolean  False to avoid detaching the option.
		 *
		 * @since 	1.6
		 */
		if ($dispatcher->not('onRemoveOptionCart', array($id_opt, $item)))
		{
			// Avoid detaching the option from the item in case at least a plugin
			// returns a negative value. If no plugin is attached to this event,
			// the option will be detached correctly.

			echo json_encode(array(0, JText::_('VAPCARTOPTDELERR')));
			die;
		}

		$res = false;

		if ($item)
		{
			$res = $item->removeOption($id_opt);
		}
		
		// check option removed
		
		if ($res === false)
		{
			echo json_encode(array(0, JText::_('VAPCARTOPTDELERR')));
			die;
		}
		
		//$cart->balance();
		
		$core->storeCart($cart);
		
		$coupon = $this->revalidateCouponCode($cart);
		
		$group_item_t_cost = VikAppointmentsCartUtils::getServiceTotalCost(VikAppointmentsCartUtils::sortItemsByServiceDate($cart->getItemsList()), $item->getID());
		
		/**
		 * Calculate discount considering the user credit.
		 *
		 * @since 1.6
		 */
		$credit 	= true;
		$creditUsed = 0.0;
		$disc_title = '';
		
		$discount_t_cost = VikAppointments::getDiscountTotalCost($cart->getTotalCost(), $coupon, $credit, $creditUsed);

		if ($creditUsed > 0)
		{
			if ($creditUsed < $credit)
			{
				$disc_title = JText::sprintf(
					'VAPUSERCREDITUSED',
					VikAppointments::printPriceCurrencySymb($credit),
					VikAppointments::printPriceCurrencySymb($creditUsed)
				);
			}
			else
			{
				$disc_title = JText::sprintf(
					'VAPUSERCREDITFINISHED',
					VikAppointments::printPriceCurrencySymb($credit)
				);
			}
		}
		//
		
		// return item details : successful(1), cart(total cost), item(total cost), quantity, group(items total cost), cart(total cost - discount), discount title
		
		echo json_encode(array(1, $cart->getTotalCost(), $item->getTotalCost(), $res, $group_item_t_cost, $discount_t_cost, $disc_title));
		die;
	}

	/**
	 * AJAX task used to increase the quantity (by one) of the specified 
	 * option from a certain item stored into the cart.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id_opt  The option ID.
	 * @param 	integer  id 	 The service ID.
	 * @param 	integer  id2 	 The employee ID.
	 * @param 	integer  ts 	 The checkin timestamp.
	 * 
	 * @return 	void
	 */
	function add_option_cart_rq()
	{	
		$app 		= JFactory::getApplication();
		$input		= $app->input;
		$dbo 		= JFactory::getDbo();
		$dispatcher = UIFactory::getEventDispatcher();
		
		$id_opt = $input->getInt('id_opt', 0);
		$id_ser = $input->getInt('id', 0);
		$id_emp = $input->getInt('id2', 0);
		$ts 	= $input->getInt('ts', 0);

		$q = $dbo->getQuery(true)
			->select($dbo->qn('o.maxq'))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
			->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'ao') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('ao.id_service'))
			->leftjoin($dbo->qn('#__vikappointments_option', 'o') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('ao.id_option'))
			->where(array(
				$dbo->qn('s.id') . ' = ' . $id_ser,
				$dbo->qn('ao.id_option') . ' = ' . $id_opt,
				$dbo->qn('o.published') . ' = 1',
			))
			->andWhere(array(
				$dbo->qn('a.id_employee') . ' = ' . $id_emp,
				$dbo->qn('s.choose_emp') . ' = 0',
			), 'OR');
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo json_encode(array(0, JText::_('VAPOPTNOTFOUNDERROR')));
			die;
		}
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo json_encode(array(0, JText::_('VAPOPTNOTFOUNDERROR')));
			die;
		}
		
		$opt_max_q = $dbo->loadResult();
		
		// remove item from cart
		
		VikAppointments::loadCartLibrary();
		
		$cart_properties = array( 
			VikAppointmentsCart::CART_ENABLED 	=> VikAppointments::isCartEnabled(),
			VikAppointmentsCart::MAX_SIZE 		=> VikAppointments::getMaxCartSize(),
			VikAppointmentsCart::ALLOW_SYNC 	=> VikAppointments::canAddSameCheckinItems(),
		);
		
		$core = new VikAppointmentsCartCore();
		$cart = $core->getCartObject($cart_properties);
		
		$item 	= $cart->getItemAt($cart->indexOf($id_ser, $id_emp, $ts));

		if (!$item)
		{
			echo json_encode(array(0, JText::_('VAPCARTOPTADDERR1')));
			die;
		}

		$option = $item->getOptionAt($item->indexOf($id_opt));
		
		if ($option === null)
		{
			echo json_encode(array(0, JText::_('VAPCARTOPTADDERR1')));
			die;
		}
		
		if ($option->getQuantity() + 1 > $opt_max_q)
		{
			echo json_encode(array(0, JText::_('VAPOPTIONMAXQUANTITYNOTICE')));
			die;
		}

		/**
		 * Trigger event before adding an option to the cart item.
		 *
		 * @param 	mixed 	 $item 		The cart item object.
		 * @param 	mixed 	 &$option 	The item option object.
		 *
		 * @return 	boolean  False to avoid adding the option.
		 *
		 * @since 	1.6
		 */
		if ($dispatcher->not('onAddOptionCart', array($item, &$option)))
		{
			// Avoid adding the option into the item in case at least a plugin
			// returns a negative value. If no plugin is attached to this event,
			// the option will be added correctly.

			echo json_encode(array(0, JText::_('VAPCARTOPTADDERR1')));
			die;
		}
		
		$option->add();
		
		//$cart->balance();
		
		$core->storeCart($cart);
		
		$coupon = $this->revalidateCouponCode($cart);
		
		$group_item_t_cost = VikAppointmentsCartUtils::getServiceTotalCost(VikAppointmentsCartUtils::sortItemsByServiceDate($cart->getItemsList()), $item->getID());
		
		/**
		 * Calculate discount considering the user credit.
		 *
		 * @since 1.6
		 */
		$credit 	= true;
		$creditUsed = 0.0;
		$disc_title = '';
		
		$discount_t_cost = VikAppointments::getDiscountTotalCost($cart->getTotalCost(), $coupon, $credit, $creditUsed);

		if ($creditUsed > 0)
		{
			if ($creditUsed < $credit)
			{
				$disc_title = JText::sprintf(
					'VAPUSERCREDITUSED',
					VikAppointments::printPriceCurrencySymb($credit),
					VikAppointments::printPriceCurrencySymb($creditUsed)
				);
			}
			else
			{
				$disc_title = JText::sprintf(
					'VAPUSERCREDITFINISHED',
					VikAppointments::printPriceCurrencySymb($credit)
				);
			}
		}
		//
		
		// return item details : successful(1), cart(total cost), item(total cost), item(quantity), group(items total cost), cart(total cost - discount), discount title
		
		echo json_encode(array(1, $cart->getTotalCost(), $item->getTotalCost(), $option->getQuantity(), $group_item_t_cost, $discount_t_cost, $disc_title));
		die;
	}

	/**
	 * Helper method used to re-validate the coupon code
	 * redeemed by the customer (if any).
	 *
	 * @param 	mixed 	$cart 	The cart instance.
	 *
	 * @return 	array 	The coupon code (an empty array if not valid or unused).
	 */
	private function revalidateCouponCode($cart)
	{
		$session 	= JFactory::getSession();
		$coupon 	= $session->get('vap_coupon_data', array());
		
		if (empty($coupon) || count($coupon) == 0 || !VikAppointments::validateCoupon($coupon, $cart))
		{
			$session->set('vap_coupon_data', array());
			return array();
		}
		
		return $coupon;
	}

	/**
	 * Helper method used to make sure all the required options have been selected.
	 *
	 * @param 	integer  $id_ser 	The service ID.
	 * @param 	array 	 $options 	An array containing the selected options.
	 * 
	 * @return 	array 	 The list of the required options that haven't been selected, otherwise false.
	 */
	private function hasEmptyRequiredOptions($id_ser, $options)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('o.id'))
			->from($dbo->qn('#__vikappointments_option', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'ao') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('ao.id_option'))
			->where(array(
				$dbo->qn('ao.id_service') . ' = ' . (int) $id_ser,
				$dbo->qn('o.required') . ' = 1',
				$dbo->qn('o.published') . ' = 1',
			));

		if (count($options))
		{
			$options = array_map('intval', $options);
			$q->where($dbo->qn('o.id') . ' NOT IN (' . implode(',', $options) . ')');
		}
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			// missing options
			return $dbo->loadColumn();
		}
		
		// success
		return false;
	}
	
	/**
	 * Helper method used to validate the recurring availability.
	 *
	 * @param 	integer  $id_ser  The service ID.
	 * @param 	integer  $id_emp  The employee ID.
	 * @param 	integer  $ts 	  The checkin timestamp.
	 * @param 	integer  $people  The number of people.
	 * @param 	mixed 	 $dbo 	  The database object.
	 *
	 * @return 	boolean  True if available, otherwise false.
	 */
	private function validateRecurringAvailability($id_ser, $id_emp, $ts, $people, $factor = 1, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}
		
		$service = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('duration', 'sleep', 'choose_emp', 'max_capacity', 'start_publishing', 'end_publishing')))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . (int) $id_ser);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			return false;
		} 

		$service = $dbo->loadAssoc();

		if ($service['start_publishing'] > 0 && ($service['start_publishing'] > $ts || $service['end_publishing'] < $ts))
		{
			return false;
		}
		
		if ($service['choose_emp'])
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('a.duration', 'a.sleep')))
				->from($dbo->qn('#__vikappointments_ser_emp_assoc', 'a'))
				->where(array(
					$dbo->qn('a.id_employee') . ' = ' . (int) $id_emp,
					$dbo->qn('a.id_service') . ' = ' . (int) $id_ser,
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				return false;
			}

			$overrides = $dbo->loadAssoc();

			$service['duration'] = $overrides['duration'];
			$service['sleep'] 	 = $overrides['sleep'];
		} 

		if (!VikAppointments::isTimeInThePast($ts))
		{
			$total_duration = ($service['duration'] + $service['sleep']) * $factor;

			if ($id_emp != -1)
			{
				$valid = VikAppointments::isEmployeeAvailableFor($id_emp, $id_ser, -1, $ts, $total_duration, $people, $service['max_capacity'], $dbo);
				
				if ($valid == 1)
				{
					return true;
				}
			}
			else
			{
				$id_emp = VikAppointments::getAvailableEmployeeOnService($id_ser, $ts, $total_duration, $people, $service['max_capacity'], $dbo);
				
				/**
				 * The employee must be a value higher than 0.
				 * It is not enough to have it different than -1.
				 * 
				 * @since 1.6.2
				 */
				if ($id_emp > 0)
				{
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * AJAX task used to return the list of states that
	 * belong to the specified country.
	 *
	 * @return 	void.
	 */
	function get_states_with_country()
	{	
		$id_country = JFactory::getApplication()->input->getUint('id_country', 0);
		
		$states = VikAppointmentsLocations::getStates($id_country, 'state_name');
		
		echo json_encode($states);
		exit;	
	}
	
	/**
	 * AJAX task used to return the list of cities that
	 * belong to the specified state.
	 *
	 * @return 	void.
	 */
	function get_cities_with_state()
	{	
		$id_state = JFactory::getApplication()->input->getUint('id_state', 0);

		$cities = VikAppointmentsLocations::getCities($id_state, 'city_name');

		echo json_encode($cities);
		exit;
	}
	
	/**
	 * AJAX task used to return the list of services that
	 * belong to the specified group.
	 *
	 * This task is used by the EMPLOYEES FILTER module to 
	 * obtain the list of services after switching group.
	 *
	 * @return 	void.
	 */
	function get_services_with_group()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$id_group = $input->getInt('id_group', 0);
		
		$arr = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_service'))
			->order($dbo->qn('ordering') . ' ASC');

		if ($id_group > 0)
		{
			$q->where($dbo->qn('id_group') . ' = ' . $id_group);
		}
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$arr = $dbo->loadAssocList();

			$langtag = JFactory::getLanguage()->getTag();

			$lang_services = VikAppointments::getTranslatedServices('', $langtag, $dbo);

			foreach ($arr as $i => $s)
			{
				$arr[$i]['name'] = VikAppointments::getTranslation($s['id'], $s, $lang_services, 'name', 'name');
			}
		}
		
		echo json_encode($arr);
		exit;
	}

	/**
	 * AJAX end-point to push a new package into the cart.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	string 	id_package 	The ID of the package to push.
	 *
	 * @return 	void
	 */
	function add_package_cart_rq()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;	
		$dbo 	= JFactory::getDbo();
		
		$id_package = $input->getUint('id_package', 0);
		
		$now = time();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_package'))
			->where($dbo->qn('id') . ' = ' . $id_package)
			->where($dbo->qn('published') . ' = 1')
			->andWhere(array(
				$dbo->qn('start_ts') . ' = -1',
				$now . ' BETWEEN ' . $dbo->qn('start_ts') . ' AND ' . $dbo->qn('end_ts'),
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// order not found
			echo json_encode(array(0, JText::_('VAPPACKNOTFOUNDERR')));
			die;
		}
		
		$args = $dbo->loadAssoc();
		
		// get translations
		$lang_packages = VikAppointments::getTranslatedPackages($id_package);
		
		$args['name'] = VikAppointments::getTranslation($id_package, $args, $lang_packages, 'name', 'name');
		
		// put package into cart
		
		VikAppointments::loadCartPackagesLibrary();
		
		$cart_properties = array( 
			VikAppointmentsCartPackages::MAX_SIZE => VikAppointments::getMaxPackagesCart(),
		);
		
		$core = new VikAppointmentsCartPackagesCore();
		$cart = $core->getCartObject($cart_properties);
		
		$pack = new VikAppointmentsPackage($args['id'], $args['name'], $args['price'], $args['num_app']);
		
		
		$res = $cart->addPackage($pack);
		
		// check package integrity on cart
		
		if (!$res)
		{
			echo json_encode(array(0, JText::_('VAPCARTPACKADDERR')));
			die;
		}
		
		$core->storeCart($cart);

		$updated_pack = $cart->getPackageAt($cart->indexOf($args['id']));
		
		// res = [status (bool), package (object), package total cost (string), cart total cost (string)]

		echo json_encode(array(1, 
			$updated_pack->toArray(), 
			VikAppointments::printPriceCurrencySymb($updated_pack->getTotalCost()),
			VikAppointments::printPriceCurrencySymb($cart->getTotalCost())
		));

		die;
	}

	/**
	 * AJAX end-point to remove an existing package from the cart.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	string 	id_package 	The ID of the package to remove.
	 *
	 * @return 	void
	 */
	function remove_package_cart_rq()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;	
		$dbo 	= JFactory::getDbo();
		
		$id_package = $input->getUint('id_package', 0);
		
		$now = time();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_package'))
			->where($dbo->qn('id') . ' = ' . $id_package)
			->where($dbo->qn('published') . ' = 1')
			->andWhere(array(
				$dbo->qn('start_ts') . ' = -1',
				$now . ' BETWEEN ' . $dbo->qn('start_ts') . ' AND ' . $dbo->qn('end_ts'),
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo json_encode(array("status" => 0, "errstr" => JText::_('VAPPACKNOTFOUNDERR')));
			die;
		}
		
		// update units in cart
		
		VikAppointments::loadCartPackagesLibrary();
		
		$core = new VikAppointmentsCartPackagesCore();
		$cart = $core->getCartObject();

		// decrease quantity by one
		$res = $cart->removePackage($id_package);
		
		// check package integrity on cart
		
		if (!$res)
		{
			echo json_encode(array("status" => 0, "errstr" => JText::_('VAPPACKNOTFOUNDERR')));
			die;
		}
		
		$core->storeCart($cart);

		$index = $cart->indexOf($id_package);

		if ($index != -1)
		{
			// the package has been removed
			$updated_pack = $cart->getPackageAt($index);
			
			// res = [status (bool), id package (int), quantity (int), cart empty (bool), package total cost (string), cart total cost (string)]

			echo json_encode(array(
				"status" 			=> 1, 
				"id" 				=> $id_package, 
				"quantity" 			=> $updated_pack->getQuantity(),
				"cart_empty" 		=> $cart->isEmpty(),
				"pack_total_cost" 	=> VikAppointments::printPriceCurrencySymb($updated_pack->getTotalCost()),
				"cart_total_cost" 	=> VikAppointments::printPriceCurrencySymb($cart->getTotalCost()),
			));
		}
		else
		{
			// there are one or more units remaining of this package
			echo json_encode(array(
				"status" 			=> 1, 
				"id" 				=> $id_package, 
				"quantity" 			=> 0, 
				"cart_empty" 		=> $cart->isEmpty(),
				"pack_total_cost" 	=> '',
				"cart_total_cost" 	=> VikAppointments::printPriceCurrencySymb($cart->getTotalCost()),
			));
		}

		die;
	}

	/**
	 * AJAX end-point to flush the packages stored in the cart.
	 *
	 * @return 	void
	 */
	function empty_cart_packages_rq()
	{	
		VikAppointments::loadCartPackagesLibrary();
		
		$core = new VikAppointmentsCartPackagesCore();
		$cart = $core->getCartObject();
		$cart->emptyCart();
		$core->storeCart($cart);
		
		die;
	}
	
	/**
	 * Task used to sumbit a new review for the specified service.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	string 	 title 		 The review title.
	 * @param 	string 	 comment 	 The review comment.
	 * @param 	integer  rating 	 The review rating (1-5).
	 * @param 	integer  id_service  The ID of the service to review.
	 *
	 * @return 	void
	 */	
	function submit_service_review()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
			
		$args = array();
		$args['title'] 		= $input->getString('title');
		$args['comment'] 	= $input->getString('comment');
		$args['rating'] 	= $input->getUint('rating', 0);
		$args['id_service'] = $input->getUint('id_ser', 0);
		$args['user'] 		= JFactory::getUser();
		$args['published'] 	= VikAppointments::isReviewsAutoPublished();
		$args['timestamp'] 	= time();
		$args['langtag'] 	= JFactory::getLanguage()->getTag();

		$return_url = JRoute::_('index.php?option=com_vikappointments&view=servicesearch&id_ser=' . $args['id_service'], false);
		
		// user cannot leave a review for this element
		if (!VikAppointments::userCanLeaveServiceReview($args['id_service']))
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWAUTHERR'), 'error');
			$app->redirect($return_url);
			exit;
		}
		
		// title or rating or service are empties
		if (empty($args['title']) || empty($args['rating']) || empty($args['id_service']))
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWFILLERR'), 'error');
			$app->redirect($return_url);
			exit;
		}
		
		// comment required and empty
		if (VikAppointments::isReviewsCommentRequired() && empty($args['comment']))
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWFILLERR'), 'error');
			$app->redirect($return_url);
			exit;
		}

		// comment length higher than 0 but lower than min length
		if (strlen($args['comment']) > 0 && strlen($args['comment']) < VikAppointments::getReviewsCommentMinLength())
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWFILLERR'), 'error');
			$app->redirect($return_url);
			exit;
		}

		$args['comment'] = mb_substr($args['comment'], 0, VikAppointments::getReviewsCommentMaxLength(), 'UTF-8');

		// insert review
		$review = new stdClass;
		$review->jid 		= $args['user']->id;
		$review->timestamp 	= $args['timestamp'];
		$review->name 		= $args['user']->username;
		$review->email 		= $args['user']->email;
		$review->title 		= $args['title'];
		$review->comment 	= $args['comment'];
		$review->rating 	= $args['rating'];
		$review->published 	= $args['published'];
		$review->langtag 	= $args['langtag'];
		$review->id_service = $args['id_service'];

		/**
		 * Trigger event before creating a new review.
		 *
		 * @param 	object   $review  The object containing the review details.
		 * @param 	boolean  $isNew   True if the review is going to be created, otherwise false. 	
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		UIFactory::getEventDispatcher()->trigger('onBeforeSaveReview', array(&$review, true));

		$dbo->insertObject('#__vikappointments_reviews', $review, 'id');
		
		if ($review->id > 0)
		{
			$app->enqueueMessage(JText::_($args['published'] ? 'VAPPOSTREVIEWCREATEDCONF' : 'VAPPOSTREVIEWCREATEDPEND'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWINSERTERR'), 'error');
		}
		
		$app->redirect($return_url);
	}
	
	/**
	 * Task used to sumbit a new review for the specified employee.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	string 	 title 		  The review title.
	 * @param 	string 	 comment 	  The review comment.
	 * @param 	integer  rating 	  The review rating (1-5).
	 * @param 	integer  id_employee  The ID of the employee to review.
	 *
	 * @return 	void
	 */
	function submit_employee_review()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
			
		$args = array();
		$args['title'] 		 = $input->getString('title');
		$args['comment'] 	 = $input->getString('comment');
		$args['rating'] 	 = $input->getUint('rating', 0);
		$args['id_employee'] = $input->getUint('id_emp', 0);
		$args['user'] 		 = JFactory::getUser();
		$args['published'] 	 = VikAppointments::isReviewsAutoPublished();
		$args['timestamp'] 	 = time();
		$args['langtag'] 	 = JFactory::getLanguage()->getTag();

		$return_url = JRoute::_('index.php?option=com_vikappointments&view=employeesearch&id_employee=' . $args['id_employee'], false);
		
		// user cannot leave a review for this element
		if (!VikAppointments::userCanLeaveEmployeeReview($args['id_employee'])) 
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWAUTHERR'), 'error');
			$app->redirect($return_url);
			exit;
		}
		
		// title or rating or employee are empties
		if (empty($args['title']) || empty($args['rating']) || empty($args['id_employee']))
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWFILLERR'), 'error');
			$app->redirect($return_url);
			exit;
		}
		
		// comment required and empty
		if (VikAppointments::isReviewsCommentRequired() && empty($args['comment']))
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWFILLERR'), 'error');
			$app->redirect($return_url);
			exit;
		}

		// comment length higher than 0 but lower than min length
		if (strlen($args['comment']) > 0 && strlen($args['comment']) < VikAppointments::getReviewsCommentMinLength())
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWFILLERR'), 'error');
			$app->redirect($return_url);
			exit;
		}

		$args['comment'] = mb_substr($args['comment'], 0, VikAppointments::getReviewsCommentMaxLength(), 'UTF-8');
		
		// insert review
		$review = new stdClass;
		$review->jid 		 = $args['user']->id;
		$review->timestamp 	 = $args['timestamp'];
		$review->name 		 = $args['user']->username;
		$review->email 		 = $args['user']->email;
		$review->title 		 = $args['title'];
		$review->comment 	 = $args['comment'];
		$review->rating 	 = $args['rating'];
		$review->published 	 = $args['published'];
		$review->langtag 	 = $args['langtag'];
		$review->id_employee = $args['id_employee'];

		/**
		 * Trigger event before creating a new review.
		 *
		 * @param 	object   $review  The object containing the review details.
		 * @param 	boolean  $isNew   True if the review is going to be created, otherwise false. 	
		 *
		 * @return 	void
		 *
		 * @since 	1.6
		 */
		UIFactory::getEventDispatcher()->trigger('onBeforeSaveReview', array(&$review, true));

		$dbo->insertObject('#__vikappointments_reviews', $review, 'id');
		
		if ($review->id > 0)
		{
			$app->enqueueMessage(JText::_($args['published'] ? 'VAPPOSTREVIEWCREATEDCONF' : 'VAPPOSTREVIEWCREATEDPEND'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPPOSTREVIEWINSERTERR'), 'error');
		}
		
		$app->redirect($return_url);
	}
	
	/**
	 * AJAX task used to load more reviews for the specified element.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id_emp  The employee ID.
	 * @param 	integer  id_ser  The service ID (only in case id_emp is empty).
	 * @param 	integer  lim0 	 The starting limit.
	 *
	 * @return 	void
	 */
	function load_more_reviews()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$config = UIFactory::getConfig();

		$id_emp = $input->getUint('id_emp', 0);
		$id_ser = $input->getUint('id_ser', 0);
		$lim0 	= $input->getUint('lim0', 0);
		
		$id = $id_emp;
		$figure = 'employee';

		if (empty($id_emp))
		{
			$id = $id_ser;
			$figure = 'service';
		}
		
		$reviews = VikAppointments::loadReviews($figure, $id, $lim0);
		
		$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');
		
		$html_arr = array();
		
		foreach ($reviews['rows'] as $review)
		{
			$data = array(
				/**
				 * @param 	array 	 review 	 	  An associative array containing the review details.
				 */
				'review' => $review,

				/**
				 * @param 	string   datetime_format  The date time format used to display when the review was created.
				 * 									  If not provided, it will be used the default one (military format).
				 */
				'datetime_format' => $dt_format,
			);

			/**
			 * The review block is displayed from the layout below:
			 * /components/com_vikappointments/layouts/review/default.php
			 * 
			 * If you need to change something from this layout, just create
			 * an override of this layout by following the instructions below:
			 * - open the back-end of your Joomla
			 * - visit the Extensions > Templates > Templates page
			 * - edit the active template
			 * - access the "Create Overrides" tab
			 * - select Layouts > com_vikappointments > review
			 * - start editing the default.php file on your template to create your own layout
			 *
			 * @since 1.6
			 */
			$html = JLayoutHelper::render('review.default', $data);
			
			$html_arr[] = $html;
		}
		
		$resp = array(1, count($reviews['rows']), $reviews['size'], $html_arr);
		
		echo json_encode($resp);
		exit;
	}
	
	/**
	 * End-point used to export the appointments in ICS format.
	 * This task can be used by external applications to syncronize
	 * the appointments within their calendars (e.g Apple iCal or Google Calendar).
	 *
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  employee 	An ID to obtain only the appointments
	 * 								assigned to the specified employee.
	 * 								If not provided, this filter will be ignored.
	 * @param 	string 	 key 		The secure key to access the appointments.
	 *
	 * @return 	void
	 */
	function appsync()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$dstart = $dend = null;

		$q = $dbo->getQuery(true);

		$q->select(array(
				'MIN(' . $dbo->qn('checkin_ts') . ') AS ' . $dbo->qn('min'),
				'MAX(' . $dbo->qn('checkin_ts') . ') AS ' . $dbo->qn('max'),
			))
			->from($dbo->qn('#__vikappointments_reservation'))
			->where($dbo->qn('status') . ' = ' . $dbo->q('CONFIRMED'));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$arr = $dbo->loadAssoc();

			$dstart = $arr['min'];
			$dend 	= $arr['max'];
		}
		
		$id_emp = $input->getInt('employee', -1);
		$key 	= $input->getString('key', '');
		
		$match = '';
		
		if ($id_emp <= 0)
		{
			$match = array(
				'synckey' 	=> VikAppointments::getSyncSecretKey(),
				'timezone' 	=> null,
			);
		}
		else
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('synckey', 'timezone')))
				->from($dbo->qn('#__vikappointments_employee'))
				->where($dbo->qn('id') . ' = ' . $id_emp);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows() == 0)
			{
				throw new Exception('Employee not found.', 404);
			}
			
			$match = $dbo->loadAssoc();
		}
		
		$filename 		= date('Y-m-d-H-m-i');
		$export_class 	= 'ics';
		
		if (strcmp($key, $match['synckey']))
		{
			throw new Exception('You are not authorized to access this resource.', 403);
		}
		
		$file_path = VAPADMIN . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $export_class .'.php';
		
		if (!file_exists($file_path))
		{
			throw new Exception('Exporter handler not found.', 404);
		}
		
		require_once $file_path;
			
		$vik_exp = new VikExporterICS($dstart, $dend, $id_emp);
		$vik_exp->setHeader('2.0', 'GREGORIAN', $match['timezone']);

		$str = $vik_exp->getString();

		$vik_exp->renderBrowser($str, $filename . '.' . $export_class);
		exit;
	} 

	/**
	 * End-point used to dispatch the specified CRON JOB.
	 * This method expects the following parameters to be sent
	 * via POST or GET.
	 *
	 * @param 	integer  id_cron 	 The ID of the cron to launch.
	 * @param 	string 	 secure_key  The secure key to execute the command.
	 *
	 * @return 	void
	 */
	function cronjob_listener_rq()
	{
		try
		{
			$app 	= JFactory::getApplication();
			$input 	= $app->input;
			$dbo 	= JFactory::getDbo();

			// get request params
			$id_cron 	= $input->getInt('id_cron', 0);
			$secure_key = $input->getString('secure_key');
			
			if (empty($secure_key) || empty($id_cron))
			{
				throw new Exception('Missing argument in request', 400);
			}
			
			// match the specified secure key with the one stored in the configuration
			$match = md5(VikAppointments::getCronSecureKey());
			
			if (strcmp($match, $secure_key))
			{
				throw new Exception('Secure key is not correct', 403);
			}
			
			// retrieve cron job arguments

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('class', 'params')))
				->from($dbo->qn('#__vikappointments_cronjob'))
				->where(array(
					$dbo->qn('id') . ' = ' . $id_cron,
					$dbo->qn('published') . ' = 1',
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				throw new Exception('Cron job not found', 404);
			}
			
			$row = $dbo->loadAssoc();
			
			$cron_job_action = $row['class']; 
			$cron_job_params = json_decode($row['params'], true);
			
			// dispatch cron job
			VikAppointments::loadCronLibrary();
			$job = CronDispatcher::getJob($cron_job_action, $id_cron, $cron_job_params);
			
			if (!$job)
			{
				throw new Exception('Cron job not executable', 500);
			}
			
			$response = $job->doJob();
			
			// save response
			$cron_logging_mode = VikAppointments::getCronLoggingMode();
			
			if ($cron_logging_mode == 1 && $response->isVerified())
			{
				// get last succesful log
				$q = $dbo->getQuery(true)
					->select($dbo->qn('id'))
					->from($dbo->qn('#__vikappointments_cronjob_log'))
					->where(array(
						$dbo->qn('id_cronjob') . ' = ' . $id_cron,
						$dbo->qn('status') . ' = 1',
					));
				
				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					// remove last successful log
					$q = $dbo->getQuery(true)
						->delete($dbo->qn('#__vikappointments_cronjob_log'))
						->where($dbo->qn('id') . ' = ' . (int) $dbo->loadResult());

					$dbo->setQuery($q);
					$dbo->execute();
				}
			}

			$log = new stdClass;
			$log->content 		= $response->getContent();
			$log->status 		= (int) $response->isVerified();
			$log->mailed 		= (int) $response->isNotify();
			$log->createdon 	= time();
			$log->id_cronjob 	= $id_cron;
			
			$dbo->insertObject('#__vikappointments_cronjob_log', $log, 'id');
			
			// mail response
			if ($response->isNotify())
			{
				$admin_mail_list = VikAppointments::getAdminMailList();

				$subject 	= JText::sprintf('VAPCRONJOBNOTIFYSUBJECT', VikAppointments::getAgencyName(true));
				$html_mess 	= JText::sprintf('VAPCRONJOBNOTIFYCONTENT', 
					date('Y-m-d H:i:s', $response->getLastUpdate()), 
					JText::_('VAPCRONLOGSTATUS' . $response->isVerified()),
					$response->getContent()
				);
				
				$vik = UIApplication::getInstance();

				foreach ($admin_mail_list as $_m)
				{
					$vik->sendMail($_m, $_m, $_m, $_m, $subject, $html_mess, array(), true);
				}
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();

			if ($code = $e->getCode())
			{
				echo ' : ' . $code;
			}

			exit;
		}
		
		// terminate response
		echo 'process terminated correctly';
		exit;
	}
}
