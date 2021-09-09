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
class VikAppointmentsViewserworkdays extends JViewUI
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
		
		$id_ser = $input->getUint('id', 0);
		$id_emp = $input->getUint('id_emp', 0);

		$filters = array();
		$filters['status'] 	= $app->getUserStateFromRequest('vapserwd.status', 'status', -1, 'int');
		$filters['type']	= $app->getUserStateFromRequest('vapserwd.type', 'type', -1, 'int');
		
		// get service name

		$q = $dbo->getQuery(true)
			->select($dbo->qn('name'))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . $id_ser);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			$app->redirect('index.php?option=com_vikappointments&task=services');
		}

		// Set the toolbar
		$this->addToolBar($dbo->loadResult());

		// get employees list

		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('e.id', 'e.firstname', 'e.lastname')))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('a.id_employee'))
			->where($dbo->qn('a.id_service') . ' = ' . $id_ser);
		
		$dbo->setQuery($q);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			$app->enqueueMessage(JText::_('VAPNOSERWORKDAYSERR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=services');
		}

		$employees = $dbo->loadAssocList();

		if (empty($id_emp))
		{
			$id_emp = $employees[0]['id'];
		}

		// get working days

		$rows = array();

		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters, $id_emp . ':' . $id_ser);
		$navbut	= "";

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_emp_worktime'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $id_emp,
				$dbo->qn('id_service') . ' = ' . $id_ser,
			))
			->order(array(
				$dbo->qn('ts') . ' ASC',
				$dbo->qn('day') . ' ASC',
				$dbo->qn('fromts') . ' ASC',
				$dbo->qn('closed') . ' ASC',
			));

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('closed') . ' <> ' . $filters['status']);
		}

		if ($filters['type'] != -1)
		{
			$comparator = ($filters['type'] != 1 ? '<>' : '=');

			$q->where($dbo->qn('ts') . " $comparator -1");
		}

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim, $id_emp . ':' . $id_ser);

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}

		$this->rows 		= &$rows;
		$this->navbut 		= &$navbut;
		$this->idService	= &$id_ser;
		$this->idEmployee	= &$id_emp;
		$this->employees 	= &$employees;
		$this->filters 		= &$filters;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($name)
	{
		// Add menu title and some buttons to the page
		JToolBarHelper::title(JText::sprintf('VAPSERWORKDAYSTITLE', $name), 'vikappointments');
	
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteServiceWorkTimes', JText::_('VAPDELETE'));
			JToolBarHelper::divider();
			JToolBarHelper::custom('restoreSerWorkDays', 'refresh', 'refresh', JText::_('VAPRESTORE'), false);
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelService', JText::_('VAPCANCEL'));
	}
}
