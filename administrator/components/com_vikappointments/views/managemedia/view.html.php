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
class VikAppointmentsViewmanagemedia extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		VikAppointments::load_font_awesome();
		
		// ONLY INSERT
		
		// Set the toolbar
		$this->addToolBar();

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
		
		JToolBarHelper::title(JText::_('VAPMAINTITLENEWMEDIA'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveMedia', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseMedia', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelMedia', JText::_('VAPCANCEL'));
	}
}
