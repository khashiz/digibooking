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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * VikAppointments View
 */
class VikAppointmentsViewccdetails extends JViewUI
{	
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$input 	= JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();

		// request
		
		$id 		= $input->getUint('id', 0);
		$type 		= $input->getString('type', '');
		$rm_hash 	= $input->getString('rmhash', ''); // used to confirm removal

		switch ($type)
		{
			case 'packages':
				$dtcol = 'createdon';
				$table = '#__vikappointments_package_order';
				break;

			case 'employees':
				$dtcol = 'createdon';
				$table = '#__vikappointments_subscr_order';
				break;

			default:
				$dtcol = 'checkin_ts';
				$table = '#__vikappointments_reservation';
		}

		$real_hash = $this->checkForRemove($id, $table, $rm_hash, $dbo);

		//
		
		$credit_card = null;

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array($dtcol, 'cc_data', 'status')))
			->from($dbo->qn($table))
			->where($dbo->qn('id') . ' = ' . $id);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// order does not exist
			exit (JText::_('JGLOBAL_NO_MATCHING_RESULTS'));
		}

		$order = $dbo->loadAssoc();

		if (!strlen($credit_card = $order['cc_data']))
		{
			// credit card details empty
			exit (JText::_('JGLOBAL_NO_MATCHING_RESULTS'));
		}

		UILoader::import('libraries.crypt.cipher');

		/**
		 * Since the decryption is made using mcrypt package, an exception
		 * could be thrown as the server might not have it installed.
		 * 			
		 * We need to wrap the code below within a try/catch and take
		 * the plain string without decrypting it. This was just an 
		 * additional security layer that doesn't alter the compliance
		 * with PCI/DSS rules.
		 *
		 * @since 1.6.2
		 */
		try
		{
			// unmask encrypted string
			$cipher = SecureCipher::getInstance();

			$credit_card = $cipher->safeEncodingDecryption($credit_card);
		}
		catch (Exception $e)
		{
			// This server doesn't support current decryption algorithm.
			// Try decoding plain text
			$credit_card = base64_decode($credit_card);
		}

		// decode credit card JSON-string
		$credit_card = json_decode($credit_card);

		// get expiration date

		$this->expDate = $order[$dtcol];

		if ($type == 'packages' || $type == 'employees')
		{
			$this->expDate += 86400 * ($order['status'] == 'CONFIRMED' ? 7 : 30);
		}
		else
		{
			$this->expDate += 86400;
		}

		//
		
		$this->creditCard 	= &$credit_card;
		$this->order 		= &$order;
		$this->id 			= &$id;
		$this->type 		= &$type;
		$this->rmHash 		= &$real_hash;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Checks if the credit card data should be removed.
	 *
	 * @param 	integer  $id 	 The order ID.
	 * @param 	string 	 $table  The DB table.
	 * @param 	string 	 $hash 	 The confirmation hash received.
	 * @param 	mixed 	 $dbo 	 The database object.
	 *
	 * @return 	string 	 The correct confirmation hash.
	 */
	private function checkForRemove($id, $table, $hash, $dbo)
	{
		// create correct hash
		$real_hash = md5($id . ':' . $table);

		if (!empty($hash) && !strcmp($hash, $real_hash))
		{
			// the received hash matches the requested one, remove CC data

			$q = $dbo->getQuery(true)
				->update($dbo->qn($table))
				->set($dbo->qn('cc_data') . ' = ' . $dbo->q(''))
				->where($dbo->qn('id') . ' = ' . $id);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getAffectedRows())
			{
				// remove was successul, display message
				exit (JText::_('VAPCREDITCARDREMOVED'));
			}
		}

		return $real_hash;
	}
}
