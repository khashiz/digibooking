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

UILoader::import('libraries.menu.shape');

/**
 * Extends the MenuShape class to handle an horizontal menu.
 *
 * @since 1.6.3
 */
class HorizontalMenuShape extends MenuShape
{
	/**
	 * @overrides
	 * Builds and returns the html structure of the menu that wraps the children.
	 * This method must be implemented to define a specific graphic of the menu.
	 *
	 * @param 	string 	$html 	The full structure of the children of the menu.
	 *
	 * @return 	string 	The html of the menu.
	 */
	protected function buildHtml($html)
	{
		$layout = new JLayoutFile('menu.horizontal.menu', null, array('client' => 'site'));

		return $layout->render(array('html' => $html));
	}

	/**
	 * @override
	 * Builds and returns the html opening that will wrap the body contents.
	 * This html will be displayed after the menu.
	 *
	 * @return 	string 	The body opening html.
	 *
	 * @since 	1.6.3
	 */
	public function openBody()
	{
		$layout = new JLayoutFile('menu.horizontal.body.open', null, array('client' => 'site'));

		return $layout->render();
	}

	/**
	 * @override
	 * Builds and returns the html closing that will wrap the body contents.
	 * This html will be displayed after the menu and the body opening.
	 *
	 * @return 	string 	The body closing html.
	 *
	 * @since 	1.6.3
	 */
	public function closeBody()
	{
		$layout = new JLayoutFile('menu.horizontal.body.close', null, array('client' => 'site'));

		return $layout->render();
	}
}
