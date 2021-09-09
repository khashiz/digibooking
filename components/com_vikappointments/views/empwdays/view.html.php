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
class VikAppointmentsViewempwdays extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();

		$itemid = $app->input->getInt('Itemid', 0);
		
		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin' . ($itemid ? '&Itemid=' . $itemid : ''), false));
			exit;
		}

		// get working days

		$lim  = 10;
		$lim0 = $app->getUserStateFromRequest('empwdays.limitstart', 'limitstart', 0, 'uint');
		
		$worktimes 		= array();
		$worktimes_date = array();

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `w`.*')
			->select($dbo->qn(array('l.name', 'l.address', 'l.zip')))
			->from($dbo->qn('#__vikappointments_emp_worktime', 'w'))
			->leftjoin($dbo->qn('#__vikappointments_employee_location', 'l') . ' ON ' . $dbo->qn('l.id') . ' = ' . $dbo->qn('w.id_location'))
			->where(array(
				$dbo->qn('w.id_employee') . ' = ' . $auth->id,
				$dbo->qn('w.id_service') . ' = -1',
			))
			->order(array(
				$dbo->qn('w.ts') . ' ASC',
				$dbo->qn('w.day') . ' ASC',
				$dbo->qn('w.fromts') . ' ASC',
				$dbo->qn('w.closed') . ' DESC',
			));
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadAssocList() as $w)
			{
				$w['location_label'] = '';
				
				if (!empty($w['address']))
				{
					$w['location_label'] = "{$w['name']} ({$w['address']}, {$w['zip']})";
				}

				if ($w['ts'] <= 0)
				{
					$worktimes[] = $w;
				}
				else
				{
					$worktimes_date[] = $w;
				}
			}

			$worktimes = array_merge($worktimes, $worktimes_date);

			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = $pageNav->getPagesLinks();
		}
		
		$this->auth 		= &$auth;
		$this->worktimes 	= &$worktimes;
		$this->navbut 		= &$navbut;
		$this->itemid 		= &$itemid;
		
		// Display the template
		parent::display($tpl);
	}
}
