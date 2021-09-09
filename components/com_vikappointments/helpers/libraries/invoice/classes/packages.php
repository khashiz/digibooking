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

UILoader::import('libraries.invoice.classes.appointments');

/**
 * Class used to generate the invoices of the packages.
 * Since the invoices of the packages are almost equals to
 * the invoices of the appointments, this class will extend
 * the methods declared by VAPInvoiceAppointments.
 *
 * @since 	1.6
 */
class VAPInvoicePackages extends VAPInvoiceAppointments
{
	/**
	 * Class constructor.
	 *
	 * @param 	array 	The order details.
	 */
	public function __construct($order)
	{
		// push the order within an array to a have a multi-dimension
		// array, which is compliant with appointments orders
		parent::__construct(array($order));
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
			'packages',
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
			'order' => $this->order[0],
		);

		if (JFactory::getApplication()->isAdmin())
		{
			$base = VAPBASE . DIRECTORY_SEPARATOR . 'layouts';
		}
		else
		{
			$base = null;
		}

		return JLayoutHelper::render('templates.invoice.package', $data, $base);
	}
}
