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
class VikAppointmentsViewmanagefile extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;

		AppointmentsHelper::load_css_js();
		
		$file = $input->getString('file');
		$file_name = basename($file);		
		
		$content = '';

		if (!is_file($file))
		{
			exit ('File not found!');	
		}

		$handle = fopen($file, 'rb');

		while (!feof($handle))
		{
			$content .= fread($handle, 8192);
		}

		fclose($handle);
		
		$this->filePath = &$file;
		$this->fileName = &$file_name;
		$this->content 	= &$content;

		// Display the template
		parent::display($tpl);
	}
}
