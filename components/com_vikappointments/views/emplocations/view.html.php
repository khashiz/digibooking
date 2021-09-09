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
class VikAppointmentsViewemplocations extends JViewUI
{
	/**
	 * VikAppointments view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_googlemaps();
		
		$dbo = JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		$locations = array();

		$q = $dbo->getQuery(true)
			->select('`l`.*')
			->select($dbo->qn(array('c.country_name', 's.state_name', 'ci.city_name')))
			->from($dbo->qn('#__vikappointments_employee_location', 'l'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.id') . ' = ' . $dbo->qn('l.id_country'))
			->leftjoin($dbo->qn('#__vikappointments_states', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('l.id_state'))
			->leftjoin($dbo->qn('#__vikappointments_cities', 'ci') . ' ON ' . $dbo->qn('ci.id') . ' = ' . $dbo->qn('l.id_city'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $auth->id,
				$dbo->qn('id_employee') . ' <= 0',
			), 'OR')
			->order($dbo->qn('id_employee') . ' DESC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$locations = $dbo->loadAssocList();
		}
		
		$this->auth 		= &$auth;
		$this->locations 	= &$locations;
		
		// Display the template
		parent::display($tpl);
	}
}
