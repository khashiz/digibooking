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

UILoader::import('libraries.menu.leftboard.separator');

/**
 * Extends the SeparatorItemShape class to handle a menu group.
 *
 * @since 1.6.3
 */
class HorizontalSeparatorItemShape extends SeparatorItemShape
{
	/**
	 * @override
	 * Builds and returns the html structure of the separator that wraps the children.
	 * This method must be implemented to define a specific graphic of the separator.
	 *
	 * @param 	string 	$html 	The full structure of the children of the separator.
	 *
	 * @return 	string 	The html of the separator.
	 */
	protected function buildHtml($html)
	{
		$data = array(
			'selected'  => $this->isSelected(),
			'collapsed' => $this->isCollapsed(),
			'href'      => $this->getHref(),
			'icon'      => $this->getCustom(),
			'title'     => $this->getTitle(),
			'children'  => $this->children(),
			'html'      => $html,
		);

		$layout = new JLayoutFile('menu.horizontal.separator', null, array('client' => 'site'));

		return $layout->render($data);
	}
}
