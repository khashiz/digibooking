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
class VikAppointmentsVieweditconfigcldays extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();
		VikAppointments::load_complex_select();
	
		// Set the toolbar
		$this->addToolBar();

		$dbo = JFactory::getDbo();

		$this->services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('s.id', 's.name')))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('s.id_group') . ' = ' . $dbo->qn('g.id'))
			->order(array(
				$dbo->qn('g.ordering') . ' ASC',
				$dbo->qn('s.ordering') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadObjectList() as $s)
			{
				$this->services[$s->id] = $s->name;
			}
		}

		// Display the template
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
		JToolBarHelper::title(JText::_('VAPMAINTITLECONFIG'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveConfigurationClosingDays', JText::_('VAPSAVE'));
			JToolBarHelper::divider();
		}
	
		JToolBarHelper::cancel('dashboard', JText::_('VAPCANCEL'));
	}
}
