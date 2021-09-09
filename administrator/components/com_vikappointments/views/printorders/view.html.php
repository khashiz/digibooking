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
class VikAppointmentsViewprintorders extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$input  = $app->input;	
		$dbo 	= JFactory::getDbo();

		$ids = $input->getUint('cid', array(0));
		
		$rows = array();

		foreach ($ids as $id)
		{
			$orders = VikAppointments::fetchOrderDetails($id);

			if ($orders)
			{
				if ($orders[0]['id_parent'] == -1)
				{
					// exclude parent order
					array_shift($orders);
				}

				foreach ($orders as $ord)
				{
					// make sure the order doesn't already exist
					if (!array_key_exists($ord['id'], $rows))
					{
						$rows[] = $ord;
					}
				}
			}
		}

		if (!$rows)
		{
			$app->redirect('index.php?option=com_vikappointments&view=reservations');
			exit;
		}
		
		$this->rows = &$rows;

		// Display the template
		parent::display($tpl);
	}
}
