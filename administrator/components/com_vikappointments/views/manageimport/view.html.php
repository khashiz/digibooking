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

UILoader::import('libraries.import.factory');

/**
 * VikAppointments View
 */
class VikAppointmentsViewmanageimport extends JViewUI
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

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$this->addToolBar();

		$type = $input->getString('import_type');
		$args = $input->get('import_args', array(), 'array');

		$handler = ImportFactory::getObject($type);

		if (!$handler)
		{
			throw new Exception('Import type not supported.', 404);
		}

		if (!$handler->hasFile())
		{
			$app->redirect('index.php?option=com_vikappointments&task=import&import_type=' . $type);
			exit;
		}

		$this->type 	= &$type;
		$this->args 	= &$args;
		$this->handler 	= &$handler;
		
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEMANAGEIMPORT'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveImport', JText::_('VAPSAVE'));
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelImport', JText::_('VAPCANCEL'));
	}
}
