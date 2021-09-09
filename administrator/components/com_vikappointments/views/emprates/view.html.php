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
class VikAppointmentsViewemprates extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$dbo   = JFactory::getDbo();
		$app   = JFactory::getApplication();
		$input = $app->input;

		// Set the toolbar
		
		$id_emp = $input->getUint('id_emp');
		$id_ser = $input->getUint('id_ser', 0);
		
		$q = "SELECT `nickname` FROM `#__vikappointments_employee` WHERE `id`=".$id_emp." LIMIT 1";
		$dbo->setQuery($q);
		$dbo->execute();
		if (!$dbo->getNumRows())
		{
			$app->redirect('index.php?option=com_vikappointments&task=employees');
		}

		$this->addToolBar($dbo->loadResult());

		// load services assigned to the employee

		$services = array();

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn(array('s.id', 's.name')))
			->from($dbo->qn('#__vikappointments_ser_emp_assoc', 'a'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('a.id_service') . ' = ' . $dbo->qn('s.id'))
			->where($dbo->qn('a.id_employee') . ' = ' . $id_emp)
			->order($dbo->qn('s.ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$services = $dbo->loadAssocList();
		}

		if (empty($id_ser) && count($services))
		{
			$id_ser = $services[0]['id'];
		}

		// load override

		$row = array();

		$inner = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('id_service'),
				$dbo->qn('rate', 'ser_rate'),
				$dbo->qn('duration', 'ser_duration'),
				$dbo->qn('sleep', 'ser_sleep'),
				$dbo->qn('description', 'ser_desc'),
			))
			->from($dbo->qn('#__vikappointments_ser_emp_assoc'));

		$q = $dbo->getQuery(true);

		$q->select('`a`.*')
			->select(array(
				$dbo->qn('s.price', 'default_price'),
				$dbo->qn('s.duration', 'default_duration'),
				$dbo->qn('s.sleep', 'default_sleep'),
				$dbo->qn('s.description', 'default_desc'),
			))
			->select('MIN(' . $dbo->qn('ser_rate') . ') AS ' . $dbo->qn('min_price'))
			->select('MAX(' . $dbo->qn('ser_rate') . ') AS ' . $dbo->qn('max_price'))
			->select('MIN(' . $dbo->qn('ser_duration') . ') AS ' . $dbo->qn('min_duration'))
			->select('MAX(' . $dbo->qn('ser_duration') . ') AS ' . $dbo->qn('max_duration'))
			->select('MIN(' . $dbo->qn('ser_sleep') . ') AS ' . $dbo->qn('min_sleep'))
			->select('MAX(' . $dbo->qn('ser_sleep') . ') AS ' . $dbo->qn('max_sleep'))
			->from($dbo->qn('#__vikappointments_ser_emp_assoc', 'a'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('a.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin('(' . $inner . ') AS ' . $dbo->qn('a2') . ' ON ' . $dbo->qn('a2.id_service') . ' = ' . $dbo->qn('s.id'))
			->where(array(
				$dbo->qn('a.id_employee') . ' = ' . $id_emp,
				$dbo->qn('a.id_service') . ' = ' . $id_ser,
			))
			->group(array(
				$dbo->qn('a.id_employee'),
				$dbo->qn('a.id_service'),
				$dbo->qn('a.rate'),
				$dbo->qn('s.name'),
				$dbo->qn('s.price'),
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$row = $dbo->loadAssoc();
		}
		
		$this->services 	= &$services;
		$this->row 			= &$row;
		$this->idEmployee 	= &$id_emp;
		$this->idService 	= &$id_ser;
		
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
		JToolBarHelper::title(JText::sprintf('VAPMAINTITLEVIEWEMPRATES', $nickname), 'vikappointments');
	
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveEmployeeRates', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseEmployeeRates', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelEmployee', JText::_('VAPCANCEL'));
	}	
}
