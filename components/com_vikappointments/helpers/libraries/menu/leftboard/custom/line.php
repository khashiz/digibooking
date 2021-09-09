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

UILoader::import('libraries.menu.custom');

/**
 * Extends the CustomShape class to display a line separator.
 *
 * @since 1.5
 * @since 1.6.3 Renamed from LeftBoardMenuLine to LeftboardCustomShapeLine.
 */
class LeftboardCustomShapeLine extends CustomShape
{
	/**
	 * @override
	 * Builds and returns the html structure of the custom menu item.
	 * This method must be implemented to define a specific graphic of the custom item.
	 *
	 * @return 	string 	The html of the custom item.
	 */
	public function buildHtml()
	{
		$layout = new JLayoutFile('menu.leftboard.custom.line', null, array('client' => 'site'));

		return $layout->render();
	}
}
