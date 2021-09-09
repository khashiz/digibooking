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
class VikAppointmentsViewpurchaserinfo extends JViewUI
{	
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$input	= $app->input;
		$dbo 	= JFactory::getDbo();

		$oid 	= $input->getUint('oid', array(0));
		$btns 	= $input->getUint('joomla3810t_btns', 0);
		$from 	= $input->get('from');

		$rows = array();
		
		foreach ($oid as $id)
		{
			$order = VikAppointments::fetchOrderDetails($id);

			if ($order)
			{
				$rows[] = $order[0];
			}
		}

		if (!$rows && count($oid) == 1)
		{
			/**
			 * Fall back to check if we are looking for a closure.
			 *
			 * @since 1.6
			 */

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('r.id', 'r.checkin_ts', 'r.duration')))
				->select($dbo->qn('e.nickname'))
				->from($dbo->qn('#__vikappointments_reservation', 'r'))
				->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
				->where(array(
					$dbo->qn('r.id') . ' = ' . $oid[0],
					$dbo->qn('r.closure') . ' = 1',
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$rows = $dbo->loadObject();
				// switch template layout
				$tpl = 'closure';
			}
		}
		
		$this->rows = &$rows;
		$this->btns = &$btns;
		$this->from = &$from;

		// Display the template
		parent::display($tpl);
	}
}
