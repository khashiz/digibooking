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

$data = array(
	/**
	 * @param 	array 	 reviews 	 	  An associative array containing the following keys:
	 * 									  - rows 	the list containing the reviews to show;
	 * 									  - size  	the total number of reviews for this employee/service.
	 */
	'reviews' => $this->reviews,

	/**
	 * @param 	boolean  canLeave 	 	  True if the current customer can leave a
	 * 									  review for the selected service or employee.
	 * 									  If not provided, false by default.
	 */
	'canLeave' => $this->userCanLeaveReview,

	/**
	 * @param 	array 	 orderingLinks 	  An array containing the ordering links to sort the reviews.
	 * 									  Each element of the list must be an associative array containing:
	 * 									  - uri  	the URI to reload the reviews with a different ordering;	
	 * 									  - active 	true if the current ordering is active;
	 * 									  - mode 	the ordering mode (ASC or DESC).
	 */
	'orderingLinks' => $this->reviewsOrderingLinks,

	/**
	 * @param 	integer  id_service 	  The ID of the service for which
	 * 									  the customer may leave a review.
	 */
	// 'id_service' => 0,

	/**
	 * @param 	integer  id_employee 	  The ID of the employee for which
	 * 									  the customer may leave a review.
	 * 									  Provide this attribute ONLY if the id_service
	 * 									  attribute is not specified.
	 */
	'id_employee' => $this->idEmployee,

	/**
	 * @param 	string   subtitle 		  The subtitle that describes the average ratio
	 * 									  and the total count of reviews.
	 * 									
	 */
	'subtitle' => $this->displayData['subtitle'],

	/**
	 * @param 	string   datetime_format  The date time format used to display when the reviews were created.
	 * 									  If not provided, it will be taken from the configuration of the program.
	 */
	'datetime_format' => $this->displayData['datetime_format'],

	/**
	 * @param 	integer  itemid 		  The Item ID that will be used to route the URL used for AJAX.
	 * 									  If not provided, the current one will be used.
	 */
	'itemid' => $this->itemid,
);

/**
 * The reviews block is displayed from the layout below:
 * /components/com_vikappointments/layouts/blocks/reviews.php
 * 
 * If you need to change something from this layout, just create
 * an override of this layout by following the instructions below:
 * - open the back-end of your Joomla
 * - visit the Extensions > Templates > Templates page
 * - edit the active template
 * - access the "Create Overrides" tab
 * - select Layouts > com_vikappointments > blocks
 * - start editing the reviews.php file on your template to create your own layout
 *
 * @since 1.6
 */
echo JLayoutHelper::render('blocks.reviews', $data);
