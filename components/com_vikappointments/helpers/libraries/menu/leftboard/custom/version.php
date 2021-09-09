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
 * Extends the CustomShape class to display a button to check the Joomla software version.
 *
 * @since 1.5
 * @since 1.6.3 Renamed from LeftBoardMenuVersion to LeftboardCustomShapeVersion.
 */
class LeftboardCustomShapeVersion extends CustomShape
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
		$dispatcher = UIFactory::getEventDispatcher();

		// check if VikUpdater is available
		$callable  = $dispatcher->is('isCallable');
		$to_update = 0;

		// prepare display data
		$data = array(
			'newupdate'  => (bool) $to_update,
			'vikupdater' => (bool) $callable,
			'connect'    => false,
			'url'        => $this->get('url'),
			'label'      => $this->get('label'),
			'title' 	 => '',
		);

		$config = UIFactory::getConfig();

		$params = new stdClass;
		$params->version 	= $config->get('version');
		$params->alias 		= CREATIVIKAPP; 

		// search for a cached update
		$result = $dispatcher->triggerOnce('getVersionContents', array(&$params));

		if ($result)
		{ 
			if ($result->status)
			{
				if ($result->response->status)
				{
					$data['label'] = $result->response->shortTitle;
					$data['title'] = $result->response->title;

					if ($result->response->compare == 1)
					{
						$to_update = 1;
					}
				}
				else
				{
					$data['label'] = JText::_('ERROR');
					$data['title'] = $result->response->error;
				}
			}
			else
			{
				$data['label'] = JText::_('ERROR');
			}
		}

		$data['connect'] = !$result;

		$layout = new JLayoutFile('menu.leftboard.custom.version', null, array('client' => 'site'));
		
		return $layout->render($data);
	}
}
