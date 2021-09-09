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
class VikAppointmentsViewserviceinfo extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$id 	= $input->getUint('id');
		$name 	= $input->getString('name');
		
		// get options

		$options = array();

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn(array('o.id', 'o.name')))
			->from($dbo->qn('#__vikappointments_option', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'))
			->where($dbo->qn('a.id_service') . ' = ' . $id);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$options = $dbo->loadAssocList();
		}

		// get employees

		$employees = array();

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn(array('e.id', 'e.firstname', 'e.lastname')))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('a.id_employee'))
			->where($dbo->qn('a.id_service') . ' = ' . $id);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList();
		}
		
		$this->options 		= &$options;
		$this->employees 	= &$employees;
		$this->serviceName 	= &$name;

		// Display the template
		parent::display($tpl);
	}
}
