<?php
/** 
 * @package   	VikAppointments
 * @subpackage 	com_vikappointments
 * @author    	Matteo Galletti - e4j
 * @copyright 	Copyright (C) 2017 e4j - Extensionsforjoomla.com. All Rights Reserved.
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
class VikAppointmentsViewmanagelangsubscr extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$tag 		= $input->getString('tag', '');
		$id_subscr 	= $input->getUint('id_subscr', 0);
		
		$row = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_lang_subscr'))
			->where(array(
				$dbo->qn('tag') . ' = ' . $dbo->q($tag),
				$dbo->qn('id_subscr') . ' = ' . $id_subscr,
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$row = $dbo->loadAssoc();
		}

		// get default

		$default = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('name'))
			->from($dbo->qn('#__vikappointments_subscription'))
			->where($dbo->qn('id') . ' = ' . $id_subscr);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$default = $dbo->loadAssoc();
		}
		
		$this->tag 		= &$tag;
		$this->idSubscr = &$id_subscr;
		$this->row 		= &$row;
		$this->default 	= &$default;

		// Display the template
		parent::display($tpl);		
	}
}
