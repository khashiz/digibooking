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

UILoader::import('libraries.widget.input');

/**
 * Base class to implement a search bar.
 *
 * @since 	1.6
 */
class UISearchBar implements UIInput
{
	/**
	 * The layout name.
	 *
	 * @var string
	 */
	private $tpl;

	/**
	 * Class constructor.
	 *
	 * @param 	string 	$tpl 	The layout name.
	 */
	public function __construct($tpl = null)
	{
		$this->tpl = is_null($tpl) ? 'default' : $tpl;
	}

	/**
	 * @override
	 * Call this method to build and return the HTML of the input.
	 *
	 * @return 	string 	The input HTML.
	 */
	public function display()
	{
		ob_start();
		$included 	= UILoader::import('libraries.widget.searchbar.layouts.' . $this->tpl);
		$html 		= ob_get_contents();
		ob_end_clean();
		
		if (!$included)
		{
			throw new Exception('Search bar [' . $this->tpl . '] layout not found!');
		}

		return $html;
	}
}
