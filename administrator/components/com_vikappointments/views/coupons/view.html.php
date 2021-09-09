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
class VikAppointmentsViewcoupons extends JViewUI
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

		$ordering = OrderingManager::getColumnToOrder('coupons', 'id', 1);

		// Set the toolbar
		$this->addToolBar();

		/**
		 * The filters are also handled by the export gateway.
		 *
		 * @see 	libraries.import.classes.coupons
		 */
		$filters = array();
		$filters['keys'] 	 = $app->getUserStateFromRequest('vapcoupons.keys', 'keys', '', 'string');
		$filters['type'] 	 = $app->getUserStateFromRequest('vapcoupons.type', 'type', 0, 'uint');
		$filters['value'] 	 = $app->getUserStateFromRequest('vapcoupons.value', 'value', 0, 'uint');
		$filters['status'] 	 = $app->getUserStateFromRequest('vapcoupons.status', 'status', 0, 'uint');
		$filters['id_group'] = $app->getUserStateFromRequest('vapcoupons.group', 'id_group', -1, 'int');

		//db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_coupon'))
			->where(1)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->where($dbo->qn('code') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
		}

		if ($filters['type'])
		{
			$q->where($dbo->qn('type') . ' = ' . $filters['type']);
		}

		if ($filters['value'])
		{
			$q->where($dbo->qn('percentot') . ' = ' . $filters['value']);
		}

		if ($filters['status'] == 1)
		{
			$q->where(array(
				$dbo->qn('dend') . ' > 0',
				$dbo->qn('dend') . ' < ' . time(),
			));
		}
		else if ($filters['status'] == 2)
		{
			$q->andWhere(array(
				$dbo->qn('dend') . ' <= 0',
				time() . ' BETWEEN ' . $dbo->qn('dstart') . ' AND ' . $dbo->qn('dend'),
			), 'OR');
		}
		else if ($filters['status'] == 3)
		{
			$q->where(array(
				$dbo->qn('dstart') . ' > 0',
				$dbo->qn('dstart') . ' > ' . time(),
			));
		}

		if ($filters['id_group'] != -1)
		{
			$q->where($dbo->qn('id_group') . ' = ' . $filters['id_group']);
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
			$navbut = "<table align=\"center\"><tr><td> " . $pageNav->getListFooter() . "</td></tr></table>";
		}

		// get coupon groups

		$groups = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_coupon_group'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}

		$new_type = OrderingManager::getSwitchColumnType('coupons', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;
		$this->groups 	= &$groups;
		
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
		//Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWCOUPONS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newcoupon', JText::_('VAPNEW'));
			JToolBarHelper::divider();
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editcoupon', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::custom('import', 'upload', 'upload', JText::_('VAPIMPORT'), false);
			JToolBarHelper::spacer();
		}

		JToolBarHelper::custom('export', 'download', 'download', JText::_('VAPEXPORT'), false);
		JToolBarHelper::spacer();
		
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteCoupons', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['type'] != 0
			|| $this->filters['value'] != 0
			|| $this->filters['status'] != 0
			|| $this->filters['id_group'] != -1);
	}
}
