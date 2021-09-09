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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * VikAppointments View
 */
class VikAppointmentsViewupdateprogram extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_font_awesome();

		$this->addToolBar();

		$config = UIFactory::getConfig();

		$params = new stdClass;
		$params->version 	= $config->get('version');
		$params->alias 		= CREATIVIKAPP;

		$dispatcher = UIFactory::getEventDispatcher();
		
		$result = $dispatcher->triggerOnce('getVersionContents', array(&$params));

		if (!$result)
		{
			$result = $dispatcher->triggerOnce('checkVersion', array(&$params));
		}

		if (!$result || !$result->status || !$result->response->status)
		{
			throw new Exception('An error occurred', 500);
		}

		$this->version = &$result->response;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar()
	{
		// Add menu title and some buttons to the page
		JToolbarHelper::title(JText::_('VAPMAINTITLEUPDATEPROGRAM'), 'vikappointments');
		
		JToolbarHelper::cancel('dashboard', JText::_('VAPCANCEL'));	
	}

	/**
	 * Scan changelog structure.
	 *
	 * @param 	array 	$arr 	The list containing changelog elements.
	 * @param 	mixed 	$html 	The html built. 
	 * 							Specify false to echo the structure immediately.
	 *
	 * @return 	string|void 	The HTML structure or nothing.
	 */
	public function digChangelog(array $arr, $html = '')
	{
		foreach ($arr as $elem)
		{
			if (isset($elem->tag))
			{
				// build attributes

				$attributes = "";
				if (isset($elem->attributes))
				{
					foreach ($elem->attributes as $k => $v)
					{
						$attributes .= " $k=\"$v\"";
					}
				}

				// build tag opening

				$str = "<{$elem->tag}$attributes>";

				if ($html)
				{
					$html .= $str;
				}
				else
				{
					echo $str;
				}

				// display contents

				if (isset($elem->content))
				{
					if ($html)
					{
						$html .= $elem->content;
					}
					else
					{
						echo $elem->content;
					}

				}

				// recursive iteration for elem children

				if (isset($elem->children))
				{
					$this->digChangelog($elem->children, $html);
				}

				// build tag closure

				$str = "</{$elem->tag}>";

				if ($html)
				{
					$html .= $str;
				}
				else
				{
					echo $str;
				}

			}
		}

		return $html;
	}
}
