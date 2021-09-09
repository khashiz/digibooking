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
 * This class extends the CronFormBuilder methods to create
 * a custom structure of the configuration fields.
 *
 * @see 	CronFormBuilder
 * @since 	1.5
 */
class VikAppointmentsCronFormBuilder extends CronFormBuilder
{
	/**
	 * @override
	 * Returns the field wrapped within the following structure:
	 *	[
	 *		[LABEL] [$html]
	 *	]
	 *
	 * @param 	string 	$html 	The html of the field to wrap.
	 * @param 	object 	$f 		The CronFormField object.
	 *
	 * @return 	string 	The resulting HTML string.
	 */
	public function wrapField($html, $f)
	{
		$vik = UIApplication::getInstance();

		$label = '';

		if (strlen($f->getLabel()))
		{
			$label = $f->getLabel() . ($f->isRequired() ? '*' : '') . ':';
		}

		return $vik->openControl($label) . 
			$html . (strlen($f->getSecondaryLabel()) > 0 ? '&nbsp;' . $f->getSecondaryLabel() : '') .
		$vik->closeControl();
	}

	/**
	 * @override
	 * Method used to bind data before building the field.
	 *
	 * @param 	CronFormField 	$f 	The CronFormField object containing the info of the field.
	 *
	 * @return 	boolean 		True to start building, otherwise false.
	 */
	protected function bind(&$f)
	{
		// append the 'required' class if the field is mandatory
		if ($f->isRequired() && $f->getType() != CronFormField::HTML)
		{
			$f->addConstraint('class', 'required');
		}

		return true;
	}
}
