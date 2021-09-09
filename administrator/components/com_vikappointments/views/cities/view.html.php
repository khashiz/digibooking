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
class VikAppointmentsViewcities extends JViewUI
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
		
		$ordering = OrderingManager::getColumnToOrder('cities', 'city_name', 1);

		$country = $input->getUint('country', 0);
		$state 	 = $input->getUint('state', 0);

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id_country', 'state_name')))
			->from($dbo->qn('#__vikappointments_states'))
			->where($dbo->qn('id') . ' = ' . $state);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			if (!empty($country))
			{
				$app->redirect('index.php?option=com_vikappointments&task=states&country=' . $country);
			}
			else
			{
				$app->redirect('index.php?option=com_vikappointments&task=states&countries');
			}
			exit;
		}

		$row = $dbo->loadObject();

		if (empty($country))
		{
			$country = $row->id_country;
		}

		// Set the toolbar
		$this->addToolBar($row->state_name);

		/**
		 * The filters are also handled by the export gateway.
		 *
		 * @see 	libraries.import.classes.cities
		 */
		$filters = array();
		$filters['keys'] 	= $app->getUserStateFromRequest($this->getPoolName($state) . '.keys', 'keys', '', 'string');
		$filters['status'] 	= $app->getUserStateFromRequest($this->getPoolName($state) . '.status', 'status', -1, 'int');
		$filters['country'] = $country;
		$filters['state'] 	= $state;

		//db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters, $state);
		$navbut	= "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_cities'))
			->where($dbo->qn('id_state') . ' = ' . $state)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->where($dbo->qn('city_name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"));
		}

		if ($filters['status'] != -1)
		{
			$q->where($dbo->qn('published') . ' = ' . $filters['status']);
		}

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim, $state);
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut="<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}
		
		$new_type = OrderingManager::getSwitchColumnType('cities', $ordering['column'], $ordering['type'], array(1, 2));
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
	protected function addToolBar($state_name)
	{
		// Add menu title and some buttons to the page	
		JToolBarHelper::title(JText::sprintf('VAPMAINTITLEVIEWCITIES', $state_name), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newcity', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editcity', JText::_('VAPEDIT'));
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
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteCities', JText::_('VAPDELETE'));
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
