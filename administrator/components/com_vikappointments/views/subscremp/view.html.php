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
class VikAppointmentsViewsubscremp extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_font_awesome();
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$ordering = OrderingManager::getColumnToOrder('subscremp', 'lastname', 1);

		$filters = array();
		$filters['keysearch'] 	= $app->getUserStateFromRequest('vapsubscremp.keysearch', 'keysearch', '', 'string');
		$filters['status'] 		= $app->getUserStateFromRequest('vapsubscremp.status', 'status', -1, 'int');
		$filters['type'] 		= $app->getUserStateFromRequest('vapsubscremp.type', 'type', '', 'string');
		$filters['tmpl'] 		= 'component';

		//db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_employee'))
			->where($dbo->qn('jid') . ' <> -1')
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keysearch']))
		{
			$q->andWhere(array(
				$dbo->qn('lastname') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
				$dbo->qn('firstname') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
				$dbo->qn('email') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
			));
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('listable') . ' = ' . $filters['status']);
		}

		if (!empty($filters['type']))
		{
			if ($filters['type'] == 'active')
			{
				$q->where($dbo->qn('active_to') . ' > ' . time());
			}
			else if ($filters['type'] == 'pending')
			{
				$q->where($dbo->qn('active_to') . ' = 0');
			}
			else if ($filters['type'] == 'expired')
			{
				$q->where($dbo->qn('active_to') . ' < ' . time());
			}
			else
			{
				$q->where($dbo->qn('active_to') . ' = -1');
			}
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
		
		$new_type = OrderingManager::getSwitchColumnType('subscremp', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);
		
		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['status'] != -1
			|| !empty($this->filters['type']));
	}
}
