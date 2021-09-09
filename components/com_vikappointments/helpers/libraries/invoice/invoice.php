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

UILoader::import('pdf.tcpdf.tcpdf');

/**
 * Abstract class used to implement the common functions
 * that will be invoked to generate and send invoices.
 *
 * @since 	1.6
 */
abstract class VAPInvoice
{
	/**
	 * The order details.
	 *
	 * @var array
	 */
	protected $order;

	/**
	 * The invoice arguments (e.g. increment number or legal info).
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * The invoice properties (e.g. page margins or units).
	 *
	 * @var object
	 */
	protected $constraints;

	/**
	 * The title of the invoice.
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * The logo of the invoice.
	 *
	 * @var string
	 */
	protected $logo = '';

	/**
	 * Flag used to show/hide the invoice header.
	 *
	 * @var boolean
	 */
	protected $showHeader = false;

	/**
	 * Class constructor.
	 *
	 * @param 	array 	The order details.
	 *
	 * @uses 	getParams()
	 */
	public function __construct($order)
	{
		$this->order = $order;

		// init params
		$this->getParams();
	}

	/**
	 * Returns an array containing the invoice arguments.
	 *
	 * @return 	array 	The invoice arguments.
	 */
	public function getParams()
	{
		if (!$this->params)
		{
			$this->params = UIFactory::getConfig()->getArray('pdfparams', null);

			if (!$this->params)
			{
				$this->params = array(
					'invoicenumber' => 1,
					'invoicesuffix' => date('Y'),
					'datetype' 		=> 1, // 1: today, 2: booking date
					'taxes' 		=> 20,
					'legalinfo' 	=> '',
					'sendinvoice' 	=> 0
				);
			}
		}
		
		return $this->params;
	}
	
	/**
	 * Returns an object containing the invoice properties.
	 *
	 * @return 	object 	The invoice properties.
	 */
	public function getConstraints()
	{
		if (!$this->constraints)
		{
			require_once VAPHELPERS . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'pdf.constraints.php';

			$this->constraints = UIFactory::getConfig()->getObject('pdfconstraints', null);

			/**
			 * Even if an object is empty, it will be considered as TRUE.
			 * For this reason, we have to make sure the constraints is 
			 * an object and it contains at least a property.
			 *
			 * @since 1.6.3
			 */
			if (!is_object($this->constraints) || !get_object_vars($this->constraints))
			{
				$this->constraints = new VikAppointmentsConstraintsPDF();
				$this->constraints = json_decode(json_encode($this->constraints));
			}
		}

		return $this->constraints;
	}

	/**
	 * Increase the invoice number after a successful generation.
	 *
	 * @return 	void
	 */
	protected function increaseNumber()
	{
		$this->params['invoicenumber']++;

		UIFactory::getConfig()->set('pdfparams', $this->params);
	}

	/**
	 * Generate the invoices related to the specified order.
	 *
	 * @return 	mixed 	The invoice path on success, otherwise false.
	 *
	 * @uses 	getInvoicePath()
	 * @uses 	getPageTemplate()
	 * @uses 	parseTemplate()
	 * @uses 	increaseNumber()
	 */
	public function generate()
	{
		if (!$this->order)
		{
			return false;
		}

		// get invoice path
		$path = $this->getInvoicePath();
		// get constraints
		$constraints = $this->getConstraints();

		if (is_file($path))
		{
			// unlink pdf if exists
			@unlink($path);
		}
		
		$font = 'courier';

		if (is_file(VAPHELPERS . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'tcpdf' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'dejavusans.php'))
		{
			$font = 'dejavusans';    
		}

		$title 	= $this->title;
		$logo 	= $this->logo;
		$width 	= 'auto';
		
		$pdf = new TCPDF($constraints->pageOrientation, $constraints->unit, $constraints->pageFormat, true, 'UTF-8', false);

		if ($title)
		{
			$pdf->SetTitle($title);
		}

		if ($this->showHeader)
		{
			$pdf->SetHeaderData($logo, $width, $title, '');
		}
		else
		{
			$pdf->SetPrintHeader(false);
		}

		// header and joomla3810ter fonts
		$pdf->setHeaderFont(array($font, '', $constraints->fontSizes->header));
		$pdf->setJoomla3810terFont(array($font, '', $constraints->fontSizes->joomla3810ter));

		// default monospaced font
		// $pdf->SetDefaultMonospacedFont('courier');

		// margins
		$pdf->SetMargins($constraints->margins->left, $constraints->margins->top, $constraints->margins->right);
		$pdf->SetHeaderMargin($constraints->margins->header);
		$pdf->SetJoomla3810terMargin($constraints->margins->joomla3810ter);

		$pdf->SetAutoPageBreak(true, $constraints->margins->bottom);
		$pdf->setImageScale($constraints->imageScaleRatio);
		$pdf->SetFont($font, '', $constraints->fontSizes->body);
		
		
		// always hide joomla3810ter
		$pdf->SetPrintJoomla3810ter(false);

		// get invoice template
		$tmpl = $this->getPageTemplate($this->order);

		// parse template
		$pages = $this->parseTemplate($tmpl);

		if (!is_array($pages))
		{
			$pages = array($pages);
		}

		// add pages
		foreach ($pages as $page)
		{
			$pdf->addPage();
			$pdf->writeHTML($page, true, false, true, false, '');
		}
		
		// write file
		$pdf->Output($path, 'F');

		// check if the file has been created
		if (!is_file($path))
		{
			return false;
		}

		// increase invoice number on success
		$this->increaseNumber();

		return $path;
	}

	/**
	 * Sends the invoice via e-mail to the customer.
	 *
	 * @param 	string 	 $path 	The invoice path, which will be 
	 * 							included as attachment within the e-mail.
	 *
	 * @return 	boolean  True on success, otherwise false.
	 *
	 * @uses 	getRecipient()
	 */
	public function send($path)
	{
		$to = $this->getRecipient();

		if (!$to)
		{
			return false;
		}

		$admin_mail_list 	= VikAppointments::getAdminMailList();
		$sendermail 		= VikAppointments::getSenderMail();
		$fromname 			= VikAppointments::getAgencyName();

		$id = basename($path);
		$id = substr($id, 0, strrpos($id, '.'));
		
		$subject = JText::sprintf('VAPINVMAILSUBJECT', $id);
		
		$vik = UIApplication::getInstance();
		return $vik->sendMail($sendermail, $fromname, $to, $admin_mail_list[0], $subject, '', array($path), true);
	}

	/**
	 * Parses the given template to replace the placeholders
	 * with the values contained in the order details.
	 *
	 * @param 	string 	The template to parse.
	 *
	 * @return 	mixed 	The invoice page or an array of pages.
	 */
	protected function parseTemplate($tmpl)
	{
		$config = UIFactory::getConfig();

		$logo_name = $config->get('companylogo');

		// company logo
		if ($logo_name)
		{ 
			$logo_str = '<img src="' . VAPMEDIA_URI . $logo_name . '" />';
		}
		else
		{
			$logo_str = '';
		}

		$tmpl = str_replace('{company_logo}', $logo_str, $tmpl);
		
		// company info
		$tmpl = str_replace('{company_info}', nl2br($this->params['legalinfo']), $tmpl);
		
		// invoice details
		$suffix = '';
		if (!empty($this->params['invoicesuffix']))
		{
			$suffix = '/' . $this->params['invoicesuffix'];
		}

		$tmpl = str_replace('{invoice_number}', $this->params['invoicenumber']	, $tmpl);
		$tmpl = str_replace('{invoice_suffix}', $suffix 						, $tmpl);

		return $tmpl;
	}

	/**
	 * Returns the destination path of the invoice.
	 *
	 * @return 	string 	The invoice path.
	 */
	abstract protected function getInvoicePath();

	/**
	 * Returns the page template that will be used to 
	 * generate the invoice.
	 *
	 * @return 	string 	The base HTML.
	 */
	abstract protected function getPageTemplate();

	/**
	 * Returns the e-mail address of the user that should
	 * receive the invoice via mail.
	 *
	 * @return 	string 	The customer e-mail.
	 */
	abstract protected function getRecipient();
}
