<?php
/** 
 * @package     VikAppointments
 * @subpackage  mod_vikappointments_search
 * @author      Matteo Galletti - e4j
 * @copyright   Copyright (C) 2018 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// require autoloader
require_once implode(DIRECTORY_SEPARATOR, array(JPATH_SITE, 'components', 'com_vikappointments', 'helpers', 'libraries', 'autoload.php'));

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.php';

// backward compatibility

$vik = UIApplication::getInstance();

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'modules/mod_vikappointments_search/mod_vikappointments_search.css');
$document->addStyleSheet(VAPASSETS_URI . 'css/jquery-ui.min.css');

// since jQuery is a required dependency, the framework should be 
// invoked even if jQuery is disabled
$vik->loadFramework('jquery.framework');

if ((bool) $params->get('loadjquery'))
{
	$vik->addScript(VAPASSETS_URI . 'js/jquery-1.11.1.min.js');
}

$vik->addScript(VAPASSETS_URI . 'js/jquery-ui-1.11.1.min.js');

// load JS dependencies

VikAppointments::load_datepicker_regional();
VikAppointments::load_complex_select();

// get view data

$references = VikAppointmentsSearchHelper::getViewHtmlReferences();

// load specified layout

require JModuleHelper::getLayoutPath('mod_vikappointments_search', $params->get('layout'));
