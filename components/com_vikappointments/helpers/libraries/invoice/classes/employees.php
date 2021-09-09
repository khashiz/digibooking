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

/**
 * Class used to generate the invoices of the employees.
 *
 * @since 	1.6
 */
class VAPInvoiceEmployees extends VAPInvoice
{
	/**
	 * An object containing the billing details
	 * of the employee.
	 *
	 * @var object
	 */
	protected $billing = null;

	/**
	 * Class constructor.
	 *
	 * @param 	array 	The order details.
	 */
	public function __construct($order)
	{
		parent::__construct($order);

		if ($this->order)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('email', 'billing_json')))
				->from($dbo->qn('#__vikappointments_employee'))
				->where($dbo->qn('id') . ' = ' . (int) $this->order['id_employee']);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$row = $dbo->loadObject();

				$this->billing 			= json_decode($row->billing_json);
				$this->order['email'] 	= $row->email;
			}

			if (!isset($this->order['subscription']))
			{
				// get subscription details (false to obtain also unpublished elements)
				$subscription = VikAppointments::getSubscription($this->order['id_subscr'], false);
				
				if ($subscription)
				{
					$this->order['subscription'] = $subscription;
				}
			}

			if (empty($this->order['payment']))
			{
				// use empty payment to avoid PHP notices
				$this->order['payment'] = array(
					'name' 	 => '',
					'charge' => 0,
				);
			}
		}
	}

	/**
	 * @override
	 * Returns the destination path of the invoice.
	 *
	 * @return 	string 	The invoice path.
	 */
	protected function getInvoicePath()
	{
		$parts = array(
			VAPINVOICE,
			'employees',
			$this->order['id'] . '-' . $this->order['sid'] . '.pdf',
		);

		return implode(DIRECTORY_SEPARATOR, $parts);
	}

	/**
	 * @override
	 * Returns the page template that will be used to 
	 * generate the invoice.
	 *
	 * @return 	string 	The base HTML.
	 */
	protected function getPageTemplate()
	{
		$data = array(
			'order' => $this->order,
		);

		if (JFactory::getApplication()->isAdmin())
		{
			$base = VAPBASE . DIRECTORY_SEPARATOR . 'layouts';
		}
		else
		{
			$base = null;
		}

		return JLayoutHelper::render('templates.invoice.employee', $data, $base);
	}

	/**
	 * @override
	 * Parses the given template to replace the placeholders
	 * with the values contained in the order details.
	 *
	 * @param 	string 	The template to parse.
	 *
	 * @return 	mixed 	The invoice page or an array of pages.
	 */
	protected function parseTemplate($tmpl)
	{
		$tmpl = parent::parseTemplate($tmpl);

		$config = UIFactory::getConfig();

		$date_format = $config->get('dateformat');
		
		$invoice_date = date($date_format);

		if ($this->params['datetype'] == 2)
		{
			$invoice_date = date($date_format, $this->order['createdon']);
		}

		$tmpl = str_replace('{invoice_date}', $invoice_date, $tmpl);
		
		// billing info
		$billing_info = "";

		if ($this->billing)
		{
			$parts = array();

			// VAT and company name
			$company_info = '';

			if (!empty($this->billing->company))
			{
				$company_info .= $this->billing->company . ' ';
			}

			if (!empty($this->billing->vat))
			{
				$company_info .= $this->billing->vat;
			}

			if ($company_info)
			{
				$parts[] = $company_info;
			}
			
			// City information
			$city_info = '';

			if (!empty($this->billing->state))
			{
				$city_info .= $this->billing->state . ', ';
			}

			if (!empty($this->billing->city))
			{
				$city_info .= $this->billing->city . ' ';
			}

			if (!empty($this->billing->zip))
			{
				$city_info .= $this->billing->zip;
			}

			if ($city_info)
			{
				$parts[] = $city_info;
			}
			
			// Address information
			$address_info = '';

			if (!empty($this->billing->address))
			{
				$address_info .= $this->billing->address;
			}

			if ($address_info)
			{
				$parts[] = $address_info;
			}

			// build details
			$billing_info = implode("<br />\n", $parts);
		}

		$tmpl = str_replace('{billing_info}', $billing_info, $tmpl);
		
		// total summary
		$total 	= $this->order['total_cost'] + $this->order['payment']['charge'];
		$net 	= $total * 100 / ($this->params['taxes'] + 100);
		$taxes 	= $total - $net;

		$tmpl = str_replace('{invoice_totalnet}'	, VikAppointments::printPriceCurrencySymb($net - $this->order['payment']['charge']), $tmpl);
		$tmpl = str_replace('{invoice_totaltax}'	, VikAppointments::printPriceCurrencySymb($taxes), $tmpl);
		$tmpl = str_replace('{invoice_grandtotal}'	, VikAppointments::printPriceCurrencySymb($total), $tmpl);
		$tmpl = str_replace('{invoice_paycharge}'	, VikAppointments::printPriceCurrencySymb($this->order['payment']['charge']), $tmpl);

		return $tmpl;
	}

	/**
	 * @override
	 * Returns the e-mail address of the employee that should
	 * receive the invoice via mail.
	 *
	 * @return 	string 	The employee e-mail.
	 */
	protected function getRecipient()
	{
		return isset($this->order['email']) ? $this->order['email'] : '';
	}
}
