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
class VikAppointmentsViewempeditserwdays extends JViewUI
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
		
		$auth = EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}
		
		$id_ser = $input->getUint('id', 0);
		
		$name = ""; // service name

		$q = $dbo->getQuery(true)
			->select($dbo->qn('s.name'))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('a.id_service') . ' = ' . $dbo->qn('s.id'))
			->where(array(
				$dbo->qn('s.id') . ' = ' . $id_ser,
				$dbo->qn('a.id_employee') . ' = ' . $auth->id,
			));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$name = $dbo->loadResult();
		}
		else
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=empserviceslist', false));
			exit;
		}

		$lim  = 10;
		$lim0 = $app->getUserStateFromRequest('empeditserwdays.limitstart', 'limitstart', 0, 'uint');
		
		// get working days
		$rows = array();

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_emp_worktime'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $auth->id,
				$dbo->qn('id_service') . ' = ' . $id_ser,
			))
			->order(array(
				$dbo->qn('ts') . ' ASC',
				$dbo->qn('day') . ' ASC',
				$dbo->qn('fromts') . ' ASC',
				$dbo->qn('closed') . ' DESC',
			));

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = $pageNav->getPagesLinks();
		}

		$this->auth 	= &$auth;
		$this->rows 	= &$rows;
		$this->id_ser 	= &$id_ser;
		$this->ser_name = &$name;
		$this->id_emp 	= &$id_emp;
		$this->navbut 	= &$navbut;
		
		// Display the template (default.php)
		parent::display($tpl);
	}
}
