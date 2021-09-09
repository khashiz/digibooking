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

// require only once the file containing all the defines
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'defines.php';

// if UILoader does not exist, include it
if (!class_exists('UILoader'))
{
	include VAPLIB . DIRECTORY_SEPARATOR . 'loader' . DIRECTORY_SEPARATOR . 'loader.php';
	// append helpers folder to the base path
	UILoader::$base .= DIRECTORY_SEPARATOR . 'helpers';
}

// fix filenames with dots
UILoader::registerAlias('lib.vikappointments', 'lib_vikappointments');

// load adapters
UILoader::import('libraries.adapter.version.listener');
UILoader::import('libraries.adapter.application');
UILoader::import('libraries.adapter.bc');

// load factory
UILoader::import('libraries.system.factory');

// load mvc
UILoader::import('libraries.mvc.view');

// load dependencies
UILoader::import('libraries.employee.auth');
UILoader::import('libraries.models.customfields');
UILoader::import('libraries.models.locations');
UILoader::import('libraries.models.orderstatus');
UILoader::import('libraries.models.specialrates');
UILoader::import('libraries.license.checker');

// load component helper
UILoader::import('lib_vikappointments');
