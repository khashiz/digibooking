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
class VikAppointmentsViewemppayments extends JViewUI
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

		$ordering = OrderingManager::getColumnToOrder('emppayments', 'ordering', 1);

		$id_emp = $input->getUint('id_emp', 0);
		
		$q = $dbo->getQuery(true)
			->select($dbo->qn('nickname'))
			->from($dbo->qn('#__vikappointments_employee'))
			->where($dbo->qn('id') . ' = ' . $id_emp);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			$app->redirect('index.php?option=com_vikappointments&task=employees');
			exit;
		}

		// Set the toolbar		
		$this->addToolBar($dbo->loadResult());

		$filters = array();
		$filters['keys'] 	= $app->getUserStateFromRequest($this->getPoolName($id_emp) . '.keys', 'keys', '', 'string');
		$filters['status'] 	= $app->getUserStateFromRequest($this->getPoolName($id_emp) . '.status', 'status', -1, 'int');

		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters, $id_emp);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('id_employee') . ' = ' . $id_emp)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->andWhere(array(
				$dbo->qn('name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('file') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
			), 'OR');
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('published') . ' = ' . $filters['status']);
		}

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim, $id_emp);

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}

		$new_type = OrderingManager::getSwitchColumnType('locations', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		// get ordering bounds

		$q = $dbo->getQuery(true)
			->select('MIN(`ordering`) AS `min`, MAX(`ordering`) AS `max`')
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('id_employee') . ' = ' . $id_emp);

		$dbo->setQuery($q);
		$dbo->execute();
		$bounds = $dbo->loadAssoc();
		
		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;
		$this->bounds 	= &$bounds;
		$this->id_emp 	= &$id_emp;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($nickname)
	{
		// Add menu title and some buttons to the page
		JToolBarHelper::title(JText::sprintf('VAPMAINTITLEVIEWEMPPAYMENTS', $nickname), 'vikappointments');
	
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newemppayment', JText::_('VAPNEW'));
			JToolBarHelper::divider();
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editemppayment', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteEmployeePayments', JText::_('VAPDELETE'));
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelEmployee', JText::_('VAPCANCEL'));
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['status'] != -1);
	}
}
