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
class VikAppointmentsViewunsubscrwl extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app 	 = JFactory::getApplication();
		$dbo 	 = JFactory::getDbo();
		$session = JFactory::getSession();
		$user 	 = JFactory::getUser();

		$itemid = $app->input->getInt('Itemid', 0);
		
		$user->phoneNumber = "";

		if (!$user->guest)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('billing_phone'))
				->from($dbo->qn('#__vikappointments_users'))
				->where($dbo->qn('jid') . ' = ' . $user->id);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$user->phoneNumber = $dbo->loadResult();
			}
		}
		
		$unsubscribed = $session->has('vap-unsubscribed-rows');

		if ($unsubscribed)
		{
			$n = $session->get('vap-unsubscribed-rows');
			$this->numRows = &$n;
		}
		else
		{
			$tpl = 'confirm';
		}

		$this->user 		= &$user;
		$this->unsubscribed = &$unsubscribed;
		$this->itemid 		= &$itemid;
		
		// Display the template
		parent::display($tpl);
	}
}
