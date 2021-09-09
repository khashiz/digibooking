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
class VikAppointmentsViewrevs extends JViewUI
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
		
		$ordering = OrderingManager::getColumnToOrder('revs', 'r.id', 2);
		
		$filters = array();
		$filters['keys'] 	= $app->getUserStateFromRequest('vapreviews.keys', 'keys', '', 'string');
		$filters['status']	= $app->getUserStateFromRequest('vapreviews.status', 'status', -1, 'int');
		$filters['rating']	= $app->getUserStateFromRequest('vapreviews.rating', 'rating', 0, 'uint');
		$filters['type'] 	= $app->getUserStateFromRequest('vapreviews.type', 'type', '', 'string');
		$filters['lang'] 	= $app->getUserStateFromRequest('vapreviews.lang', 'lang', '', 'string');

		//db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `r`.*')
			->select(array(
				$dbo->qn('s.name', 'sername'),
				$dbo->qn('e.nickname', 'empname'),
			))
			->from($dbo->qn('#__vikappointments_reviews', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
			->where(1)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->andWhere(array(
				$dbo->qn('r.name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('r.title') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('s.name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('e.nickname') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
			));
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('r.published') . ' = ' . $filters['status']);
		}

		if ($filters['rating'] != 0)
		{
			$q->where($dbo->qn('r.rating') . ' = ' . $filters['rating']);
		}

		if (!empty($filters['type']))
		{
			$q->where($dbo->qn('r.id_' . $filters['type']) . ' <> -1');
		}

		if (!empty($filters['lang']))
		{
			$q->where($dbo->qn('r.langtag') . ' = ' . $dbo->q($filters['lang']));
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
		
		$new_type = OrderingManager::getSwitchColumnType('revs', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);
		
		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;
		
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWREVIEWS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newrev', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editrev', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}
		
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteReviews', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['status'] != -1
			|| $this->filters['rating'] != 0
			|| !empty($this->filters['type'])
			|| !empty($this->filters['lang']));
	}
}
