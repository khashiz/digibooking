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
class VikAppointmentsViewmanageclosure extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_complex_select();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$this->addToolbar();

		$ids 	= $input->getUint('cid', array());
		$from 	= $input->getString('from', 'employees');

		$employees = array();

		// get employees
		
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'nickname')))
			->from($dbo->qn('#__vikappointments_employee'))
			->order(array(
				$dbo->qn('lastname') . ' ASC',
				$dbo->qn('firstname') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadObjectList();
		}

		// build item

		$item = new stdClass;
		$item->employees = array();
		$item->fromDate  = time();
		$item->fromHour  = 8;
		$item->fromMin 	 = 0;
		$item->toHour 	 = 17;
		$item->toMin 	 = 0;

		if ($from == 'employees')
		{
			// CID contains a list of the selected employees
			$item->employees = $ids;
		}
		else if ($ids)
		{
			// CID contains a list of the selected reservations
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('id_employee', 'checkin_ts', 'duration')))
				->from($dbo->qn('#__vikappointments_reservation'))
				->where(array(
					$dbo->qn('id') . ' = ' . (int) $ids[0],
					$dbo->qn('closure') . ' = 0',
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$order = $dbo->loadObject();

				$checkin  = getdate($order->checkin_ts);
				$checkout = getdate(strtotime('+' . $order->duration . ' minutes', $checkin[0]));

				$item->fromDate = strtotime('00:00:00', $checkin[0]);
				$item->fromHour = $checkin['hours'];
				$item->fromMin 	= $checkin['minutes'];
				$item->toHour 	= $checkout['hours'];
				$item->toMin 	= $checkout['minutes'];

				// we don't need to push the same employee as
				// it is already blocked
				// $item->employees[] = $order->id_employee;
			}
		}

		$this->ids 		 = &$ids;
		$this->from 	 = &$from;
		$this->item 	 = &$item;
		$this->employees = &$employees;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar()
	{
		// Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLENEWCLOSURE'), 'vikappointments');	
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveClosure', JText::_('VAPSAVE'));
			JToolBarHelper::custom('saveAndNewClosure', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelClosure', JText::_('VAPCANCEL'));
	}
}
