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
class VikAppointmentsViewpackorders extends JViewUI
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

		// Set the toolbar
		$this->addToolBar();

		$ordering = OrderingManager::getColumnToOrder('packorders', 'id', 2);

		$filters = array();
		$filters['keysearch'] 	= $app->getUserStateFromRequest('vappackord.keysearch', 'keysearch', '', 'string');
		$filters['datefilter'] 	= $app->getUserStateFromRequest('vappackord.datefilter', 'datefilter', '', 'string');
		$filters['status']		= $app->getUserStateFromRequest('vappackord.status', 'status', '', 'string');
		$filters['id_payment']	= $app->getUserStateFromRequest('vappackord.id_payment', 'id_payment', 0, 'uint');

		// db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `o`.`id`')
			->select($dbo->qn(array(
				'o.sid',
				'o.id_payment', 
				'o.status',
            	'o.total_cost',
            	'o.tot_paid',
	            'o.purchaser_nominative',
	            'o.purchaser_mail',
	            'o.createdon',
	            'o.createdby',
	            'o.id_user',
			)))
			->select($dbo->qn('p.name', 'payment_name'))
			->select('SUM(' . $dbo->qn('i.used_app') . ') AS ' . $dbo->qn('total_used'))
			->select('SUM(' . $dbo->qn('i.num_app') . ') AS ' . $dbo->qn('total_num'))
			->from($dbo->qn('#__vikappointments_package_order', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_gpayments', 'p') . ' ON ' . $dbo->qn('o.id_payment') . ' = ' . $dbo->qn('p.id'))
			->leftjoin($dbo->qn('#__vikappointments_package_order_item', 'i') . ' ON ' . $dbo->qn('i.id_order') . ' = ' . $dbo->qn('o.id'))
			->where(1)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keysearch']))
		{
			if (strpos($filters['keysearch'], 'id:') !== 0)
			{
				$q->andWhere(array(
					$dbo->qn('o.sid') . ' = ' . $dbo->q($filters['keysearch']),
					$dbo->qn('o.purchaser_nominative') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
					$dbo->qn('o.purchaser_mail') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
					$dbo->qn('o.purchaser_phone') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
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

		if ($filters['id_payment'])
		{
			$q->where($dbo->qn('o.id_payment') . ' = ' . $filters['id_payment']);
		}

		if (strlen($filters['datefilter']))
		{
			$start  = VikAppointments::createTimestamp($filters['datefilter'], 0, 0);
			$end 	= VikAppointments::createTimestamp($filters['datefilter'], 23, 59);

			$q->where($dbo->qn('o.createdon') . ' BETWEEN ' . $start . ' AND ' . $end);
		}

		$q->group($dbo->qn('o.id'));

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

		// get invoices
		$this->hasInvoices = false;

		foreach ($rows as $k => $row)
		{
			$path = VAPINVOICE . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . $row['id'] . '-' . $row['sid'] . '.pdf';

			if (file_exists($path))
			{
				$url = VAPINVOICE_URI . 'packages/' . basename($path);

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

		$new_type = OrderingManager::getSwitchColumnType('packorders', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->filters 	= &$filters;
		$this->ordering = &$ordering;
		$this->payments = &$payments;
		
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWPACKORDERS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newpackorder', JText::_('VAPNEW'));
			JToolBarHelper::divider();
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editpackorder', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
			JToolBarHelper::custom('reportspack', 'bars', 'bars', JText::_('VAPREPORTS'), false);
			JToolBarHelper::divider();
		}
		
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deletePackOrders', JText::_('VAPDELETE'));
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
			|| !empty($this->filters['id_payment']));
	}
}
