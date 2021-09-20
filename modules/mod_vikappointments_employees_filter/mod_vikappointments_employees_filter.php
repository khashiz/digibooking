<?php
/** 
 * @package     VikAppointments
 * @subpackage  mod_vikappointments_employees_filter
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
$document->addStyleSheet(JUri::root() . 'modules/mod_vikappointments_employees_filter/mod_vikappointments_employees_filter.css');
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

VikAppointments::load_complex_select();
VikAppointments::load_googlemaps();
VikAppointments::load_currency_js();

// get configuration filters

$filters = VikAppointmentsEmployeesFilterHelper::getFilters($params);

// get view data

$ref = VikAppointmentsEmployeesFilterHelper::getViewHtmlReferences();

// get groups list

if ($filters['filters']['group'])
{
	$groups = VikAppointmentsEmployeesFilterHelper::getServicesGroups();
}
else
{
	$groups = array();
}

$id_group = $ref['filters']['group'] ? $ref['filters']['group'] : $filters['defaults']['group'];

// get services list

if ($filters['filters']['service'])
{
	$services = VikAppointmentsEmployeesFilterHelper::getServicesList($id_group);
}
else
{
	$services = array();
}

$id_service = $ref['filters']['service'] ? $ref['filters']['service'] : $filters['defaults']['service'];

// get countries

if ($filters['filters']['country'] && !$filters['filters']['zip'])
{
	$countries = VikAppointmentsLocations::getCountries('country_name');
}
else
{
	$country = array();
}

$id_country = $ref['filters']['country'] ? $ref['filters']['country'] : $filters['defaults']['country'];

// get states

if ($filters['filters']['state'] && !$filters['filters']['zip'])
{
	$states = VikAppointmentsLocations::getStates($id_country, 'state_name');
}
else
{
	$states = array();
}

$id_state = $ref['filters']['state'];

// get cities

if ($filters['filters']['city'] && !$filters['filters']['zip'])
{
	$cities = VikAppointmentsLocations::getCities($id_state, 'city_name');
}
else
{
	$cities = array();
}

// get price range

$picked_price_range = array($filters['price_range']['def'][0], $filters['price_range']['def'][1]);

if (count($ref['filters']['price']) == 2)
{
	$picked_price_range = $ref['filters']['price'];
}

// load specified layout

require JModuleHelper::getLayoutPath('mod_vikappointments_employees_filter', $params->get('layout'));
