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

// import Joomla controller library
jimport('joomla.application.component.controller');
// import Joomla view library
jimport('joomla.application.component.view');

// this should be already loaded from autoload.php
UILoader::import('libraries.adapter.version.listener');

if (class_exists('JViewLegacy'))
{
	/* Joomla 3.x adapters */

	class JViewBaseUI extends JViewLegacy
	{
		/* adapter for JViewLegacy */
	}

	class JControllerUI extends JControllerLegacy
	{
		/* adapter for JControllerLegacy */
	}
}
else
{
	/* Joomla 2.5 adapters */

	class JViewBaseUI extends JView
	{
		/* adapter for JView */
	}

	class JControllerUI extends JController
	{
		/* adapter for JController */
	}
}
