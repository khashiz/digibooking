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
class VikAppointmentsViewemplocations extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();
        VikAppointments::load_googlemaps();

        $app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();
		
		$id_emp = $input->getUint('id_emp');
		
        $rows 		= array();

		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart(array('id_emp' => $id_emp));
		$navbut = "";

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('nickname'))
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

		// get employee locations

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `l`.*')
			->select($dbo->qn(array(
				'c.country_name', 's.state_name', 'ci.city_name',
			)))
			->from($dbo->qn('#__vikappointments_employee_location', 'l'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.id') . ' = ' . $dbo->qn('l.id_country'))
			->leftjoin($dbo->qn('#__vikappointments_states', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('l.id_state'))
			->leftjoin($dbo->qn('#__vikappointments_cities', 'ci') . ' ON ' . $dbo->qn('ci.id') . ' = ' . $dbo->qn('l.id_city'))
			->where($dbo->qn('l.id_employee') . ' = ' . $id_emp);
        
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

		// get coordinates for map

		$js_coordinates = array();

		$q = $dbo->getQuery(true);

		$q->select(array(
				$dbo->qn('name'),
				$dbo->qn('latitude', 'lat'),
				$dbo->qn('longitude', 'lng'),
			))
			->from($dbo->qn('#__vikappointments_employee_location'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $id_emp,
				$dbo->qn('latitude') . ' IS NOT NULL',
				$dbo->qn('longitude') . ' IS NOT NULL',
			));

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$js_coordinates = $dbo->loadAssocList();
		}
		
		$this->rows 		 = &$rows;
		$this->navbut 		 = &$navbut;
		$this->idEmployee 	 = &$id_emp;
		$this->jsCoordinates = &$js_coordinates;
		
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
		JToolBarHelper::title(JText::sprintf('VAPMAINTITLEVIEWEMPLOCATIONS', $nickname), 'vikappointments');
	
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
            JToolBarHelper::addNew('newemplocation', JText::_('VAPNEW'));
            JToolBarHelper::divider();  
        }
        if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
        {
            JToolBarHelper::editList('editemplocation', JText::_('VAPEDIT'));
            JToolBarHelper::spacer();
        }
        if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
        {
            JToolBarHelper::deleteList( VikAppointments::getConfirmSystemMessage(), 'deleteEmployeeLocations', JText::_('VAPDELETE'));
        }

        JToolBarHelper::cancel('cancelEmployee', JText::_('VAPCANCEL'));
	}	
}
