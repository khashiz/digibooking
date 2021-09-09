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
 * Class used to generate the invoices of the appointments.
 *
 * @since 	1.6
 */
class VAPInvoiceAppointments extends VAPInvoice
{
	/**
	 * An object containing the billing details
	 * of the user.
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

		if ($this->order && $this->order[0]['id_user'] > 0)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_users'))
				->where($dbo->qn('id') . ' = ' . (int) $this->order[0]['id_user']);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$this->billing = $dbo->loadObject();
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
			$this->order[0]['id'] . '-' . $this->order[0]['sid'] . '.pdf',
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

		return JLayoutHelper::render('templates.invoice.appointment', $data, $base);
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
			$invoice_date = date($date_format, $this->order[0]['createdon']);
		}

		$tmpl = str_replace('{invoice_date}', $invoice_date, $tmpl);
		
		// customer info
		$custinfo = "";
		$custdata = json_decode($this->order[0]['custom_f']);

		foreach ($custdata as $kc => $vc)
		{
			if (!empty($vc))
			{
				$custinfo .= JText::_($kc) . ': ' . $vc . "<br/>\n";
			}
		}

		$tmpl = str_replace('{customer_info}', $custinfo, $tmpl);
		
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

			if (!empty($this->billing->vatnum))
			{
				$company_info .= $this->billing->vatnum;
			}

			if ($company_info)
			{
				$parts[] = $company_info;
			}
			
			// City information
			$city_info = '';

			if (!empty($this->billing->billing_state))
			{
				$city_info .= $this->billing->billing_state . ', ';
			}

			if (!empty($this->billing->billing_city))
			{
				$city_info .= $this->billing->billing_city . ' ';
			}

			if (!empty($this->billing->billing_zip))
			{
				$city_info .= $this->billing->billing_zip;
			}

			if ($city_info)
			{
				$parts[] = $city_info;
			}
			
			// Address information
			$address_info = '';

			if (!empty($this->billing->billing_address))
			{
				$address_info .= $this->billing->billing_address;
			}

			if (!empty($this->billing->billing_address_2))
			{
				$address_info .= ", " . $this->billing->billing_address_2;
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
		$total 	= $this->order[0]['total_cost'] + $this->order[0]['payment_charge'];
		$net 	= $total * 100 / ($this->params['taxes'] + 100);
		$taxes 	= $total - $net;

		$tmpl = str_replace('{invoice_totalnet}'	, VikAppointments::printPriceCurrencySymb($net - $this->order[0]['payment_charge']), $tmpl);
		$tmpl = str_replace('{invoice_totaltax}'	, VikAppointments::printPriceCurrencySymb($taxes), $tmpl);
		$tmpl = str_replace('{invoice_grandtotal}'	, VikAppointments::printPriceCurrencySymb($total), $tmpl);
		$tmpl = str_replace('{invoice_paycharge}'	, VikAppointments::printPriceCurrencySymb($this->order[0]['payment_charge']), $tmpl);
		
		return $tmpl;
	}

	/**
	 * @override
	 * Returns the e-mail address of the user that should
	 * receive the invoice via mail.
	 *
	 * @return 	string 	The customer e-mail.
	 */
	protected function getRecipient()
	{
		return $this->order[0]['purchaser_mail'];
	}
}
