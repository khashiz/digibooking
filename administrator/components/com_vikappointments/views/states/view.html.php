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
class VikAppointmentsViewstates extends JViewUI
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
		
		$ordering = OrderingManager::getColumnToOrder('states', 's.state_name', 1);

		$country = $input->getUint('country', 0);

		$q = $dbo->getQuery(true)
			->select($dbo->qn('country_name'))
			->from($dbo->qn('#__vikappointments_countries'))
			->where($dbo->qn('id') . ' = ' . $country);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			$app->redirect('index.php?option=com_vikappointments&task=countries');
			exit;
		}

		// Set the toolbar
		$this->addToolBar($dbo->loadResult());

		/**
		 * The filters are also handled by the export gateway.
		 *
		 * @see 	libraries.import.classes.states
		 */
		$filters = array();
		$filters['keys'] 	= $app->getUserStateFromRequest($this->getPoolName($country) . '.keys', 'keys', '', 'string');
		$filters['status'] 	= $app->getUserStateFromRequest($this->getPoolName($country) . '.status', 'status', -1, 'int');
		$filters['country'] = $country;

		//db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters, $country);
		$navbut	= "";

		$rows = array();

		$citiesCount = $dbo->getQuery(true)
			->select('COUNT(1)')
			->from($dbo->qn('#__vikappointments_cities', 'c'))
			->where($dbo->qn('c.id_state') . ' = ' . $dbo->qn('s.id'));

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `s`.*')
			->select('(' . $citiesCount . ') AS ' . $dbo->qn('cities_count'))
			->from($dbo->qn('#__vikappointments_states', 's'))
			->where($dbo->qn('s.id_country') . ' = ' . $country)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->where($dbo->qn('s.state_name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('s.published') . ' = ' . $filters['status']);
		}
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim, $country);

		if ($dbo->getNumRows() > 0)
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}
		
		$new_type = OrderingManager::getSwitchColumnType('states', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);
		
		$this->rows 	= &$rows;	
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->country 	= &$country;
		$this->filters 	= &$filters;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($country_name)
	{
		// Add menu title and some buttons to the page	
		JToolBarHelper::title(JText::sprintf('VAPMAINTITLEVIEWSTATES', $country_name), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newstate', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editstate', JText::_('VAPEDIT'));
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
			JToolBarHelper::deleteList( VikAppointments::getConfirmSystemMessage(), 'deleteStates', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return $this->filters['status'] != -1;
	}
}
