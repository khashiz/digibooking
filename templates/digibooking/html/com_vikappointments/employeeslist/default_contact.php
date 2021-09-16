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

$vik = UIApplication::getInstance();

$returnUri = 'index.php?option=com_vikappointments&view=employeeslist';

foreach ($this->requestFilters as $k => $v)
{
	if ($v)
	{
		$returnUri .= "&filters[$k]=$v";
	}
}

if ($this->limitStart)
{
	$returnUri .= "&limitstart={$this->limitStart}";
}

if ($this->selGroup > 0)
{
	$returnUri .= "&employee_group={$this->selGroup}";
}

/**
 * If both the id_employee and id_service attributes are empty,
 * it is assumed that we are in the employees list view, in which
 * the selection of the employee is dynamic.
 *
 * @see 	vapGoToMail() JS function
 */
$data = array(
	/**
	 * @param 	string   title 		  The quick contact heading title.
	 * 								  Empty by default.
	 * 									
	 */
	// 'title' => '',

	/**
	 * @param 	integer  id_service   The ID of the service for which
	 * 								  the customer may ask a question.
	 */
	// 'id_service' => 0,

	/**
	 * @param 	integer  id_employee  The ID of the employee for which
	 * 								  the customer may ask a question.
	 * 								  Provide this attribute ONLY if the id_service
	 * 								  attribute is not specified.
	 */
	// 'id_employee' => 0,

	/**
	 * @param 	string   return  	  The plain return URI.
	 */
	'return' => $returnUri,

	/**
	 * @param 	boolean  gdpr 	  	  True to place a disclaimer for GDPR European law, otherwise false.
	 * 								  If not provided, the value will be retrived from the global configuration.
	 */
	// 'gdpr' => false,

	/**
	 * @param 	integer  itemid 	  The Item ID that will be used to route the URL used for AJAX.
	 * 								  If not provided, the current one will be used.
	 */
	'itemid' => $this->itemid,
);

/**
 * The quick contact block is displayed from the layout below:
 * /components/com_vikappointments/layouts/blocks/quickcontact.php
 * 
 * If you need to change something from this layout, just create
 * an override of this layout by following the instructions below:
 * - open the back-end of your Joomla
 * - visit the Extensions > Templates > Templates page
 * - edit the active template
 * - access the "Create Overrides" tab
 * - select Layouts > com_vikappointments > blocks
 * - start editing the quickcontact.php file on your template to create your own layout
 *
 * @since 1.6
 */
echo JLayoutHelper::render('blocks.quickcontact', $data);
