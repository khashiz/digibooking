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

/**
 * Class used to wrap the PDF constraints.
 *
 * @since 1.4
 */
class VikAppointmentsConstraintsPDF
{
	/**
	 * The page orientation (landscape or portrait).
	 *
	 * @var string
	 */
	public $pageOrientation = self::PAGE_ORIENTATION_PORTRAIT;

	/**
	 * The PDF measure unite (mm, cm, point or inch).
	 *
	 * @var string
	 */
	public $unit = self::UNIT_MILLIMETER;

	/**
	 * The page format (A4, A5, or A6).
	 */
	public $pageFormat = self::PAGE_FORMAT_A4;

	/**
	 * The margins of the pages.
	 *
	 * @var array
	 */
	public $margins = array(
		"top" 		=> 10,
		"bottom" 	=> 10,
		"right" 	=> 10,
		"left" 		=> 10,
		"header" 	=> 5,
		"joomla3810ter" 	=> 5,
	);

	/**
	 * Ratio used to adjust the conversion of pixels to user units.
	 *
	 * @var float
	 */
	public $imageScaleRatio = 1.25;
	
	/**
	 * The font sizes to use for each specified section.
	 *
	 * @var array
	 */
	public $fontSizes = array(
		"header" 	=> 10,
		"body" 		=> 10,
		"joomla3810ter" 	=> 10,
	);
	
	// PAGE ORIENTATION
	const PAGE_ORIENTATION_LANDSCAPE = 'L';
	const PAGE_ORIENTATION_PORTRAIT  = 'P';
	
	// UNIT
	const UNIT_POINT      = 'pt';
	const UNIT_MILLIMETER = 'mm';
	const UNIT_CENTIMETER = 'cm';
	const UNIT_INCH       = 'in';
	
	// PAGE FORMAT
	const PAGE_FORMAT_A4 = 'A4';
	const PAGE_FORMAT_A5 = 'A5';
	const PAGE_FORMAT_A6 = 'A6';
}
