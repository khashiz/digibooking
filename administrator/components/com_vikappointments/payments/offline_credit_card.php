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
defined('_JEXEC') or die('Restricted Area');

/**
 * The Offline Credit Card payment gateway (seamless) is not a real method of payment. 
 * This gateway collects the credit card details of your customers and then send them via e-mail to the administrator, 
 * so that it is able to make the transaction with a virtual pos.
 *
 * After the form submission the status of the order will be changed to CONFIRMED.
 * If you want to leave the status to PENDING (to change it manually) it is needed to change the default status 
 * from the parameters of your gateway.
 *
 * For PCI compliance, the system encrypts the details of the credit card and store them partially in the database.
 * The remaining details are sent to the e-mail of the administrator.
 *
 * @since 1.0
 */
class VikAppointmentsPayment
{
	/**
	 * The esit of the transaction.
	 *
	 * @var boolean
	 */
	private $validation = 0;
	
	/**
	 * The order information needed to complete the payment process.
	 *
	 * @var array
	 */
	private $order_info;

	/**
	 * The payment configuration.
	 *
	 * @var array
	 */
	private $params;
	
	/**
	 * Returns the fields that should be filled in from the details of the payment.
	 * The configuration fields are listed below:
	 * @property 	newstatus 	The status assumed after a successful transaction.
	 * @property 	usessl 		True to have the payment form under HTTPS.
	 * @property 	brands 		The accepted credit card brands.
	 *
	 * @return 	array 	The fields array.
	 */
	public static function getAdminParameters()
	{
		return array( 
			'newstatus' => array(
				'type' 		=> 'select', 
				'label' 	=> 'Set Order Status to://Use PENDING in case you want to manually verify the credit card.',
				'options' 	=> array(
					'PENDING' 	=> JText::_('VAPSTATUSPENDING'),
					'CONFIRMED' => JText::_('VAPSTATUSCONFIRMED'),
				),
			),
			'usessl' => array(
				'type' 		=> 'select',
				'label' 	=> 'Use SSL',
				'options' 	=> array(
					1 => JText::_('JYES'),
					0 => JText::_('JNO'),
				),
			),
			'brands' => array(
				'type' 		=> 'select',
				'label' 	=> 'Accepted CC Brands//Leave this field empty to accept all the brands.',
				'multiple' 	=> 1,
				'options' 	=> array(
					'visa' 			=> 'Visa',
					'mastercard' 	=> 'Master Card',
					'amex' 			=> 'American Express',
					'diners' 		=> 'Diners Club',
					'discover' 		=> 'Discover',
					'jcb' 			=> 'JCB',
				),
			),
		);
	}
	
	/**
	 * Class constructor.
	 *
	 * @param 	array 	$order 	 The order info array.
	 * @param 	array 	$params  The payment configuration. These fields are the 
	 * 							 same of the getAdminParameters() function.
	 */
	public function __construct($order, $params = array())
	{
		$this->order_info = $order;
		$this->params 	  = $params;

		$this->params['brands'] = (array) $this->params['brands'];

		// Import dependencies in constructor to avoid loading
		// files while accessing the admin parameters.
		UILoader::import('libraries.banking.creditcard');
	}
	
	/**
	 * This method is invoked every time a user visits the page of a 
	 * reservation with PENDING Status.
	 * Displays the form to collect the details of a given credit card.
	 *
	 * @return 	void
	 *
	 * @uses 	hasCreditCard()
	 */
	public function showPayment()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		if ($this->params['usessl'])
		{
			// change scheme from URLs
			$this->order_info['notify_url'] = str_replace('http:', 'https:', $this->order_info['notify_url']);
			$this->order_info['return_url'] = str_replace('http:', 'https:', $this->order_info['return_url']);
			$this->order_info['error_url'] 	= str_replace('http:', 'https:', $this->order_info['error_url']);

			$uri = JUri::getInstance();

			if (strtolower($uri->getScheme()) != 'https')
			{
				// forward to HTTPS
				$uri->setScheme('https');
				$app->redirect((string) $uri, 301);
				exit;
			}
		}

		// make sure the customer hasn't provided yet its CC details
		if ($this->hasCreditCard())
		{
			return false;
		}

		// load resources
		VikAppointments::load_font_awesome();
		$vik = UIApplication::getInstance();
		$doc = JFactory::getDocument();

		$doc->addStyleSheet(VAPADMIN_URI . 'payments/off-cc/resources/off-cc.css');
		$vik->addScript(VAPADMIN_URI . 'payments/off-cc/resources/off-cc.js');

		$form = '<form action="' . $this->order_info['notify_url'] . '" method="post" name="offlineccpaymform" id="offlineccpaymform">';
		$form .= '<div class="offcc-payment-wrapper">';
		$form .= '<div class="offcc-payment-box">';

		// accepted brands
		$form .= '<div class="offcc-payment-field">';

		$form .= '<div class="offcc-payment-field-wrapper">';
		foreach ((count($this->params['brands']) ? $this->params['brands'] : CreditCard::getAllBrands()) as $brand)
		{
			$form .= '<img src="' . VAPADMIN_URI . 'payments/off-cc/resources/icons/' . $brand . '.png" title="' . $brand . '" alt="' . $brand . '"/> ';
		}
		$form .= '</div>';

		$form .= '</div>';

		// Cardholder Name
		$form .= '<div class="offcc-payment-field">';

		$form .= '<div class="offcc-payment-field-wrapper">';
		$form .= '<span class="offcc-payment-icon"><i class="fa fa-user"></i></span>';
		$form .= '<input type="text" name="cardholder" value="' . $this->order_info['details']['purchaser_nominative'] . '" placeholder="' . JText::_('VAPCCNAME') . '"/>';
		$form .= '</div>';

		$form .= '</div>';

		// Credit Card
		$form .= '<div class="offcc-payment-field">';

		$form .= '<div class="offcc-payment-field-wrapper">';
		$form .= '<span class="offcc-payment-icon"><i class="fa fa-credit-card-alt"></i></span>';
		$form .= '<input type="text" name="cardnumber" value="" placeholder="' . JText::_('VAPCCNUMBER') . '" maxlength="16" autocomplete="off"/>';
		$form .= '<span class="offcc-payment-cctype-icon" id="credit-card-brand"></span>';
		$form .= '</div>';

		$form .= '</div>';

		// Expiry Date and CVC
		$form .= '<div class="offcc-payment-field">';

		$form .= '<div class="offcc-payment-field-wrapper inline">';
		$form .= '<span class="offcc-payment-icon"><i class="fa fa-calendar"></i></span>';
		$form .= '<input type="text" name="expdate" value="" placeholder="' . JText::_('VAPEXPIRINGDATEFMT') . '" class="offcc-small" maxlength="7"/>';
		$form .= '</div>';

		$form .= '<div class="offcc-payment-field-wrapper inline">';
		$form .= '<span class="offcc-payment-icon"><i class="fa fa-lock"></i></span>';
		$form .= '<input type="text" name="cvc" value="" placeholder="' . JText::_('VAPCVV') . '" class="offcc-small" maxlength="4" autocomplete="off"/>';
		$form .= '</div>';

		$form .= '</div>';

		// Submit
		$form .= '<div class="offcc-payment-field">';

		$form .= '<div class="offcc-payment-field-wrapper inline">';
		$form .= '<button type="submit" onclick="return validateCreditCardForm();" class="cc-submit-btn">' . JText::_('VAPCCPAYNOW') . '</button>';
		$form .= '</div>';

		$form .= '</div>';

		$form .= '</div>';
		$form .= '</div>';
		$form .= '</form>';
		
		//output
		echo $form;
		
		return true;
	}
	
	/**
	 * Validates the transaction details sent from the bank. 
	 * This method is invoked by the system every time the Notify URL 
	 * is visited (the one used in the showPayment() method). 
	 *
	 * @return 	array 	The array result, which MUST contain the "verified" key (1 or 0).
	 *
	 * @uses 	registerCreditCard()
	 * @uses 	notifyAdmin()
	 */
	public function validatePayment()
	{
		$array_result = array();
		$array_result['verified'] = 0;
		$array_result['tot_paid'] = 0.0;
		$array_result['log'] 	  = '';

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		// post data (only data in POST method)
		$request = array();
		$request['cardholder'] 	= $input->post->getString('cardholder');
		$request['cardnumber'] 	= $input->post->get('cardnumber');
		$request['expdate'] 	= $input->post->get('expdate');
		$request['cvc'] 		= $input->post->get('cvc');
		// end post data

		foreach ($request as $k => $v)
		{
			if (empty($v))
			{
				// Missing required field.
				// Do not register logs for invalid data.
				return $array_result;
			}
		}

		if (strlen($request['expdate']) != 4)
		{
			// Expiry date must have 4 characters to represent mmYY format.
			// Do not register logs for invalid data.
			return $array_result;
		}

		$now = getdate();

		$month 	= intval(substr($request['expdate'], 0, 2));
		$year 	= intval(substr($now['year'], 0, 2) . substr($request['expdate'], 2, 2));

		$card = CreditCard::getBrand($request['cardnumber'], $request['cvc'], $month, $year, $request['cardholder']);

		if( 
			// impossible to identify credit card brand
			!($card instanceof CreditCard)
			// impossible to charge the credit card
			|| !$card->isChargeable()
			// the brand of the credit card is not accepted (empty brands means "all brands are accepted")
			|| (count($this->params['brands']) && !in_array($card->getBrandAlias(), $this->params['brands'])) 
		) {
			// Do not register logs for invalid data.
			return $array_result;
		}

		// register credit card in order information
		if ($this->registerCreditCard($card))
		{
			// notify administrator via e-mail
			$this->notifyAdmin($card);
		}
		else
		{
			$array_result['log'] = 'Impossible to register credit card details';

			return $array_result;
		}

		// credit card information received
		
		$this->validation = 1;
		
		if ($this->params['newstatus'] == 'CONFIRMED')
		{
			$array_result['verified'] = 1;	
		}
		
		return $array_result;
	}
	
	/**
	 * This function is called after the payment has been validated for redirect actions.
	 * When this method is called, the class is invoked after the validatePayment() function.
	 *
	 * @param 	boolean  $res 	The result of the transaction.
	 *
	 * @return 	void
	 */
	public function afterValidation($res = 0)
	{
		$app = JFactory::getApplication();
		
		/**
		 * override result with the one calculated previously.
		 *
		 * @see validatePayment()
		 */
		$res = $this->validation;

		if ($res < 1)
		{
			$app->enqueueMessage(JText::_('VAPPAYNOTVERIFIED'), 'error');
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPPAYMENTRECEIVED'));
		}
		
		$app->redirect($this->order_info['return_url']);
		exit;
	}

	///////////
	// UTILS //
	///////////

	/**
	 * Checks if the order already owns some credit card details.
	 *
	 * @return 	boolean  True if any, otherwise false.
	 */
	private function hasCreditCard()
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		/**
		 * The table must not refers directly to 
		 * the appointments as this payment can be
		 * used to pay also the packages and the subscriptions.
		 */
		switch ($this->order_info['type'])
		{
			case 'packages':
				$table = '#__vikappointments_package_order';
				break;

			case 'employees':
				$table = '#__vikappointments_subscr_order';
				break;

			default:
				$table = '#__vikappointments_reservation';
		}

		$q->select($dbo->qn('cc_data'))
			->from($dbo->qn($table))
			->where($dbo->qn('id') . ' = ' . (int) $this->order_info['oid']);


		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		return ($dbo->getNumRows() && strlen($dbo->loadResult()));
	}

	/**
	 * Encrypts the partial details of the credit card and registers
	 * them within the database.
	 *
	 * @param 	CreditCard 	$card 	The credit card details.
	 *
	 * @return 	boolean 	True on success, otherwise false.
	 */
	private function registerCreditCard(CreditCard $card)
	{
		if ($card === null)
		{
			return false;
		}

		UILoader::import('libraries.crypt.cipher');

		$dbo = JFactory::getDbo();

		// build object
		$obj = new stdClass;

		$obj->brand = new stdClass;
		$obj->brand->label = JText::_('VAPCCBRAND');
		$obj->brand->value = $card->getBrandName();
		$obj->brand->alias = $card->getBrandAlias();

		$obj->cardHolder = new stdClass;
		$obj->cardHolder->label = JText::_('VAPCCNAME');
		$obj->cardHolder->value = $card->getCardholderName();

		$obj->cardNumber = new stdClass;
		$obj->cardNumber->label = JText::_('VAPCCNUMBER');
		$obj->cardNumber->value = $card->getMaskedCardNumber();
		// get only short masked card number
		$obj->cardNumber->value = $obj->cardNumber->value[0];

		$obj->expiryDate = new stdClass;
		$obj->expiryDate->label = JText::_('VAPEXPIRINGDATE');
		$obj->expiryDate->value = $card->getExpiryDate();

		$obj->cvc = new stdClass;
		$obj->cvc->label = JText::_('VAPCVV');
		$obj->cvc->value = $card->getCvc();

		// JSON encode
		$json = json_encode($obj);

		/**
		 * Since the encryption is made using mcrypt package, an exception
		 * could be thrown as the server might not have it installed.
		 * 			
		 * We need to wrap the code below within a try/catch and take
		 * the plain string without encrypting it. This was just an 
		 * additional security layer that doesn't alter the compliance
		 * with PCI/DSS rules.
		 *
		 * @since 1.6.2
		 */
		try
		{
			// mask secure key
			$cipher = SecureCipher::getInstance();

			$data = $cipher->safeEncodingEncryption($json);
		}
		catch (Exception $e)
		{
			// This server doesn't support current encryption algorithm.
			// Use plain TEXT, encoded in Base64.
			$data = base64_encode($json);
		}

		// register credit card details in database
		
		/**
		 * The table must not refers directly to 
		 * the appointments as this payment can be
		 * used to pay also the packages and the subscriptions.
		 */
		switch ($this->order_info['type'])
		{
			case 'packages':
				$table = '#__vikappointments_package_order';
				break;

			case 'employees':
				$table = '#__vikappointments_subscr_order';
				break;

			default:
				$table = '#__vikappointments_reservation';
		}

		$q = $dbo->getQuery(true);

		$q->update($dbo->qn($table))
			->set($dbo->qn('cc_data') . ' = ' . $dbo->q($data))
			->where($dbo->qn('id') . ' = ' . (int) $this->order_info['oid']);

		$dbo->setQuery($q);
		$dbo->execute();

		return (bool) $dbo->getAffectedRows();
	}

	/**
	 * Notifies the administrator via e-mail with the remaining
	 * details of the credit card.
	 *
	 * @param 	CreditCard 	The credit card details.
	 *
	 * @return 	void
	 */
	private function notifyAdmin(CreditCard $card)
	{
		$tag 		= JFactory::getLanguage()->getTag();
		$def_tag 	= VikAppointments::getDefaultLanguage();

		// load default language
		if ($def_tag != $tag)
		{
			VikAppointments::loadLanguage($def_tag);
		}
	
		// get mailing settings
		$admin_mail_list 	= VikAppointments::getAdminMailList();
		$sendermail 		= VikAppointments::getSenderMail();
		if (empty($sendermail))
		{
			$sendermail = $admin_mail_list[0];
		}
		$fromname = VikAppointments::getAgencyName();

		/**
		 * The task must not refers directly to 
		 * the reservations as this payment can be
		 * used to pay also the packages and the subscriptions.
		 */
		switch ($this->order_info['type'])
		{
			case 'packages':
				$task 	= 'packorders';
				$query 	= 'keysearch=id:' . $this->order_info['oid'];
				break;

			case 'employees':
				$task 	= 'subscrorders';
				$query 	= 'keysearch=id:' . $this->order_info['oid'];
				break;

			default:
				$task 	= 'reservations';
				$query 	= 'res_id=' . $this->order_info['oid'];
		}

		$vik = UIApplication::getInstance();

		// get information to send
		$masked_card_number = $card->getMaskedCardNumber();

		/**
		 * Route administrator URL depending on the current platform.
		 *
		 * @since 1.6.3
		 */
		$admin_link = $vik->adminUrl('index.php?option=com_vikappointments&task=' . $task . '&' . $query);
		$admin_link = '<a href="' . $admin_link . '">' . $admin_link . '</a>';

		// build subject
		$subject = JText::_('VAPOFFCCMAILSUBJECT');
	
		// build message
		$mess = JText::sprintf('VAPOFFCCMAILCONTENT', $this->order_info['oid'], $masked_card_number[1], $admin_link);
		
		foreach ($admin_mail_list as $_m)
		{
			$vik->sendMail($sendermail, $fromname, $_m, $_m, $subject, $mess);
		}

		// reload customer language
		if ($def_tag != $tag)
		{
			VikAppointments::loadLanguage($tag);
		}
	}	
}
