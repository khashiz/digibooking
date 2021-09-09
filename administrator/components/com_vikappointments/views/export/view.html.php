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
class VikAppointmentsViewexport extends JViewUI
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

		$type = $input->getString('import_type');
		$args = $input->get('import_args', array(), 'array');

		UILoader::import('libraries.import.factory');

		$handler = ImportFactory::getObject($type);

		if (!$handler)
		{
			throw new Exception('Import type not supported.', 404);
		}

		$rows = $handler->getExportableRows($args);

		// get the list of all the available exportable classes
		$export_list = ImportFactory::getExportList();

		$this->addToolBar($handler);

		$this->type 		= &$type;
		$this->args 		= &$args;
		$this->rows 		= &$rows;
		$this->handler 		= &$handler;
		$this->exportList 	= &$export_list;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($handler)
	{
		// Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWEXPORT'), 'vikappointments');

		JToolBarHelper::custom('downloadExport', 'download', 'download', JText::_('VAPDOWNLOAD'), false);

		if ($task = $handler->getCancelTask())
		{
			JToolBarHelper::cancel($task, JText::_('VAPCANCEL'));
		}
	}
}
