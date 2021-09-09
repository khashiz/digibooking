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

// require autoloader
require_once implode(DIRECTORY_SEPARATOR, array(JPATH_SITE, 'components', 'com_vikappointments', 'helpers', 'libraries', 'autoload.php'));
require_once( JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_vikappointments'. DIRECTORY_SEPARATOR. 'arascode' . DIRECTORY_SEPARATOR .'ArasJoomlaVikApp.php');
include_jdate();
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_vikappointments'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);	
}

// require helper files
require_once implode(DIRECTORY_SEPARATOR, array(JPATH_ADMINISTRATOR, 'components', 'com_vikappointments', 'helpers', 'helper.php'));

// import joomla controller library
jimport('joomla.application.component.controller');

// Add CSS file for all pages
AppointmentsHelper::load_css_js();

// remove expired credit cards
AppointmentsHelper::removeExpiredCreditCards();

// check updater fields
AppointmentsHelper::registerUpdaterFields();

// Get an instance of the controller prefixed by vikappointments
$controller = JControllerLegacy::getInstance('VikAppointments');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
