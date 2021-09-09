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
 * The PayPal payment gateway (hosted) prints the standard orange PayPal button to start the transaction.
 * The payment will come on PayPal website and, only after the transaction, the customers will be 
 * redirected to the order page on your website.
 *
 * @since 1.0
 */
class VikAppointmentsPayment
{
	/**
	 * The PayPal e-mail account.
	 *
	 * @var string
	 */
	private $account = "";

	/**
	 * The sandbox environment status (ON enabled, OFF disabled).
	 *
	 * @var string
	 */
	private $sandbox = 0;

	/**
	 * The image URL used to display the Pay Now button.
	 *
	 * @var 	string
	 * @since 	1.6
	 */
	private $image = "";
	
	/**
	 * The order information needed to complete the payment process.
	 *
	 * @var array
	 */
	private $order_info;
	
	/**
	 * Returns the fields that should be filled in from the details of the payment.
	 * The configuration fields are listed below:
	 * @property 	logo 		The PayPal image logo.
	 * @property 	account 	The PayPal e-mail account.
	 * @property 	sandbox 	The PayPal environment to use.
	 *
	 * @return 	array 	The fields array.
	 */
	public static function getAdminParameters()
	{
		return array(
			'logo' => array(
				'type' 	=> 'custom', 
				'label' => '', 
				'html' 	=> '<img src="https://www.paypalobjects.com/webstatic/i/ex_ce2/logo/logo_paypal_106x29.png"/>',
			),
			'account' => array(
				'type' 		=> 'text', 
				'label' 	=> 'PayPal Account://The PayPal account <b>e-mail address</b> must be specified instead of the <b>merchant account</b>.',
				'required' 	=> 1,
			),
			'sandbox' => array(
				'type' 		=> 'select', 
				'label' 	=> 'Test Mode://When enabled, the PayPal SANDBOX will be used. Turn OFF this option to collect PRODUCTION payments.', 
				'options' 	=> array(
					1 => 'ON',
					0 => 'OFF',
				),
			),
			'safemode' => array(
				'type' 		=> 'select',
				'label' 	=> 'Safe Connection://When enabled, the connection to PayPal will be established only through the TLS 1.2 protocol.',
				'options' 	=> array(
					1 => 'ON',
					0 => 'OFF',
				),
			),
			'image' => array(
				'type' 		=> 'text',
				'label' 	=> 'Image URL//The image URL that will be used to display the Pay Now button.',
				'default' 	=> 'https://www.paypal.com/en_GB/i/btn/btn_paynow_SM.gif',
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
		
		$this->account  = (!empty($params['account'])) ? $params['account'] : $this->account;
		$this->sandbox  = (int) (!empty($params['sandbox'])) ? $params['sandbox'] : $this->sandbox;
		$this->safemode = (int) (!empty($params['safemode'])) ? $params['safemode'] : 0;
		$this->image    = (!empty($params['image'])) ? $params['image'] : $this->image;
	}
	
	/**
	 * This method is invoked every time a user visits the page of a reservation with PENDING Status.
	 * Display the PayPal paynow button to begin a transaction.
	 *
	 * @return 	void
	 */
	public function showPayment()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		if ($input->getUint('status'))
		{
			$app->enqueueMessage('Payment done! The validation may take a few minutes. Please, try to refresh the page.');
			return true;
		}

		if ($this->sandbox == 1)
		{
			$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		}
		else
		{
			$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		}

		if (empty($this->image))
		{
			$adminParams = static::getAdminParameters();
			$this->image = $adminParams['image']['default'];
		}

		$amount = number_format($this->order_info['total_net_price'], 2, '.', '');
		$tax    = number_format($this->order_info['total_tax'], 2, '.', '');
		
		$form  = "<form action=\"{$paypal_url}\" method=\"post\" name=\"paypalform\">\n";

		$form .= "<input type=\"hidden\" name=\"business\" value=\"{$this->account}\" />\n";
		$form .= "<input type=\"hidden\" name=\"cmd\" value=\"_xclick\" />\n";
		$form .= "<input type=\"hidden\" name=\"amount\" value=\"{$amount}\" />\n";
		$form .= "<input type=\"hidden\" name=\"item_name\" value=\"{$this->order_info['transaction_name']}\" />\n";
		$form .= "<input type=\"hidden\" name=\"quantity\" value=\"1\" />\n";
		$form .= "<input type=\"hidden\" name=\"tax\" value=\"{$tax}\" />\n";
		$form .= "<input type=\"hidden\" name=\"shipping\" value=\"0.00\" />\n";
		$form .= "<input type=\"hidden\" name=\"currency_code\" value=\"{$this->order_info['transaction_currency']}\" />\n";
		$form .= "<input type=\"hidden\" name=\"no_shipping\" value=\"1\" />\n";
		$form .= "<input type=\"hidden\" name=\"rm\" value=\"2\" />\n";
		$form .= "<input type=\"hidden\" name=\"notify_url\" value=\"{$this->order_info['notify_url']}\" />\n";
		$form .= "<input type=\"hidden\" name=\"return\" value=\"{$this->order_info['return_url']}&status=1\" />\n";

		$form .= "<input type=\"image\" src=\"{$this->image}\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\" style=\"border:0;\">\n";

		$form .= "</form>\n";

		echo $form;
		
		return true;
	}
	
	/**
	 * Validate the transaction details sent from the bank. 
	 * This method is invoked by the system every time the Notify URL 
	 * is visited (the one used in the showPayment() method). 
	 *
	 * @return 	array 	The array result, which MUST contain the "verified" key (1 or 0).
	 */
	public function validatePayment()
	{
		$array_result = array();
		$array_result['verified'] = 0;
		$array_result['tot_paid'] = 0.0;
		$array_result['log'] = '';
		
		//cURL Method HTTP1.1 October 2013
		$raw_post_data 	= file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		
		$myPost = array();
		foreach ($raw_post_array as $keyval)
		{
			$keyval = explode('=', $keyval);
			if (count($keyval) == 2)
			{
				$myPost[$keyval[0]] = urldecode($keyval[1]);
			}
		}

		// check if the form has been spoofed
		$against = array(
			'business' 	  => $this->account,
			'mc_gross' 	  => number_format($this->order_info['total_net_price'], 2, '.', ''),
			'mc_currency' => $this->order_info['transaction_currency'],
			'tax'		  => number_format($this->order_info['total_tax'], 2, '.', ''),
		);

		/**
		 * If the account name contains the merchant code instead
		 * of the e-mail related to the account, the spoofing check will fail
		 * as the merchant code is always converted into the account e-mail.
		 *
		 * For example, if we specify 835383648, PayPal will return the related
		 * account: dev@e4j.com
		 * Then, 2 different values will be compared:
		 * "835383648" ($this->account) againt "dev@e4j.com" ($myPost['business'])
		 */

		// inject the original values within the payment data
		foreach ($against as $k => $v)
		{
			if (isset($myPost[$k]))
			{
				$myPost[$k] = $v;
			}
		}
		//

		$req = 'cmd=_notify-validate';
		if (function_exists('get_magic_quotes_gpc'))
		{
			$get_magic_quotes_exists = true;
		}

		foreach ($myPost as $key => $value)
		{
			if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
			{
				$value = urlencode(stripslashes($value));
			}
			else
			{
				$value = urlencode($value);
			}

			$req .= "&$key=$value";
			$array_result['log'] .= "&$key=$value\n";
		}
		
		if (!function_exists('curl_init'))
		{
			$array_result['log'] = "FATAL ERROR: cURL is not installed on the server\n\n" . $array_result['log'];

			return $array_result;
		}
		
		if ($this->sandbox == 1)
		{
			$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		}
		else
		{
			$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		}
		
		$ch = curl_init($paypal_url);
		if ($ch == false)
		{
			$array_result['log'] = "Curl error: " . curl_error($ch) . "\n\n" . $array_result['log'];

			return $array_result;
		}
		
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		
		/**
		 * Turn on TLS 1.2 protocol in case of safe mode or sandbox enabled.
		 *
		 * @since 1.6.2
		 */
		if (defined('CURLOPT_SSLVERSION') && ($this->sandbox || $this->safemode))
		{
			curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		}

		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		
		// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and copy it in the same folder as this php file
		// This is mandatory for some environments.
		// $cert = dirname(__FILE__) . "/cacert.pem";
		// curl_setopt($ch, CURLOPT_CAINFO, $cert);
		
		$res = curl_exec($ch);

		if (curl_errno($ch) != 0)
		{
			$array_result['log'] .= date('[Y-m-d H:i e] ') . " Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL;

			curl_close($ch);

			return $array_result;
		}
		else
		{
			$array_result['log'] .= date('[Y-m-d H:i e]') . " HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL;
			$array_result['log'] .= date('[Y-m-d H:i e]') . " HTTP response of validation request: $res" . PHP_EOL;
			
			curl_close($ch);
		}
		
		if (strcmp(trim($res), 'VERIFIED') == 0)
		{
			$array_result['tot_paid'] = $_POST['mc_gross'];
			$array_result['verified'] = 1;
			$array_result['log'] = '';
		}
		else if (strcmp(trim($res), 'INVALID') == 0)
		{
			$array_result['log'] .= date('[Y-m-d H:i e]'). " Invalid IPN: $req\n\nResponse: [$res]" . PHP_EOL;
		}
		else
		{
			$array_result['log'] .= date('[Y-m-d H:i e]'). " Unknown Error: $req\n\nResponse: [$res]" . PHP_EOL;
		}
		
		//END cURL Method HTTP1.1 October 2013
		
		return $array_result;
	}
}
