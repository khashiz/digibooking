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
class VikAppointmentsViewsubscrorders extends JViewUI
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

		$ordering = OrderingManager::getColumnToOrder('subscrorders', 'o.id', 2);

		// Set the toolbar
		$this->addToolBar();
		
		$filters = array();
		$filters['keysearch'] 	= $app->getUserStateFromRequest('vapsubscrord.keysearch', 'keysearch', '', 'string');
		$filters['datefilter'] 	= $app->getUserStateFromRequest('vapsubscrord.datefilter', 'datefilter', '', 'string');
		$filters['status']		= $app->getUserStateFromRequest('vapsubscrord.status', 'status', '', 'string');
		$filters['id_subscr']	= $app->getUserStateFromRequest('vapsubscrord.id_subscr', 'id_subscr', 0, 'uint');
		$filters['id_payment']	= $app->getUserStateFromRequest('vapsubscrord.id_payment', 'id_payment', 0, 'uint');

		//db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `o`.*')
			->select(array(
				$dbo->qn('s.name', 'subname'),
				$dbo->qn('e.id', 'empid'),
				$dbo->qn('e.lastname'),
				$dbo->qn('e.firstname'),
				$dbo->qn('p.name', 'payname'),
			))
			->from($dbo->qn('#__vikappointments_subscr_order', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_subscription', 's') . ' ON ' . $dbo->qn('o.id_subscr') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('o.id_employee') . ' = ' . $dbo->qn('e.id'))
			->leftjoin($dbo->qn('#__vikappointments_gpayments', 'p') . ' ON ' . $dbo->qn('o.id_payment') . ' = ' . $dbo->qn('p.id'))
			->where(1)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));
		
		if (strlen($filters['keysearch']))
		{
			if (strpos($filters['keysearch'], 'id:') !== 0)
			{
				$q->andWhere(array(
					$dbo->qn('o.sid') . ' = ' . $dbo->q($filters['keysearch']),
					$dbo->qn('e.nickname') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
					$dbo->qn('e.email') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
				));
			}
			else
			{
				$id = intval(substr($filters['keysearch'], 3));

				$q->where($dbo->qn('o.id') . ' = ' . $id);
			}
		}

		if (strlen($filters['status']))
		{
			$q->where($dbo->qn('o.status') . ' = ' . $dbo->q($filters['status']));
		}

		if ($filters['id_subscr'])
		{
			$q->where($dbo->qn('o.id_subscr') . ' = ' . $filters['id_subscr']);
		}

		if ($filters['id_payment'])
		{
			$q->where($dbo->qn('o.id_payment') . ' = ' . $filters['id_payment']);
		}

		if (strlen($filters['datefilter']))
		{
			$start  = VikAppointments::jcreateTimestamp($filters['datefilter'], 0, 0);
			$end 	= VikAppointments::jcreateTimestamp($filters['datefilter'], 23, 59);

			$q->where($dbo->qn('o.createdon') . ' BETWEEN ' . $start . ' AND ' . $end);
		}
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim);

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}

		// get subscriptions
		$subscriptions = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_subscription'))
			->order($dbo->qn('price') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$subscriptions = $dbo->loadAssocList();
		}

		// get invoices
		$this->hasInvoices = false;

		foreach ($rows as $k => $row)
		{
			$path = VAPINVOICE . DIRECTORY_SEPARATOR . 'employees' . DIRECTORY_SEPARATOR . $row['id'] . '-' . $row['sid'] . '.pdf';

			if (file_exists($path))
			{
				$url = VAPINVOICE_URI . 'employees/' . basename($path);

				$rows[$k]['invoice'] = $url;

				// at least an invoice has been found, it is needed to 
				// display the columns to open the invoices
				$this->hasInvoices = true;
			}
			else
			{
				$rows[$k]['invoice'] = '';
			}
		}

		// get payments
		$payments = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('subscr') . ' = 1')
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$payments = $dbo->loadAssocList();
		}
		
		$new_type = OrderingManager::getSwitchColumnType('subscrorders', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);
		
		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;

		$this->payments 		= &$payments;
		$this->subscriptions 	= &$subscriptions;
		
		// Display the template (default.php)
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWSUBSCRORDERS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newsubscrorder', JText::_('VAPNEW'));
			JToolBarHelper::divider();
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editsubscrorder', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteSubscriptionOrders', JText::_('VAPDELETE'));
		}	
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return (!empty($this->filters['datefilter'])
			|| !empty($this->filters['status'])
			|| !empty($this->filters['id_payment'])
			|| !empty($this->filters['id_subscr']));
	}
}
