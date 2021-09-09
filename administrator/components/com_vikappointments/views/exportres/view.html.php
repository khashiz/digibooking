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
class VikAppointmentsViewexportres extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();
		VikAppointments::load_complex_select();
		
		$dbo 	= JFactory::getDbo();
		$config = UIFactory::getConfig();

		$this->addToolBar();

		// get dates range
		
		$dates = array('', '');

		$q = $dbo->getQuery(true)
			->select(array(
				'MIN(' . $dbo->qn('checkin_ts') . ') AS ' . $dbo->qn('min_date'),
				'MAX(' . $dbo->qn('checkin_ts') . ') AS ' . $dbo->qn('max_date'),
			))
			->from($dbo->qn('#__vikappointments_reservation'))
			->where($dbo->qn('id_parent') . ' <> -1');
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$row = $dbo->loadAssoc();

			$dates[0] = date($config->get('dateformat'), $row['min_date']);
			$dates[1] = date($config->get('dateformat'), $row['max_date']);
		}

		// get employees
		
		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('e.id', 'e.lastname', 'e.firstname')))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->order(array(
				$dbo->qn('e.lastname') . ' ASC',
				$dbo->qn('e.firstname') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList(); 
		} 
		
		$this->dates 	 = &$dates;
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEEXPORTRES'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::custom('exportReservations', 'download', 'download', JText::_('VAPDOWNLOAD'), false);
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelReservation', JText::_('VAPCANCEL'));
	}
}
