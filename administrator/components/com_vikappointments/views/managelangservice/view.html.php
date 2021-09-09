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
class VikAppointmentsViewmanagelangservice extends JViewUI
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
		$id_service = $input->getUint('id_service', 0);
		
		$row = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_lang_service'))
			->where(array(
				$dbo->qn('tag') . ' = ' . $dbo->q($tag),
				$dbo->qn('id_service') . ' = ' . $id_service,
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
			->select($dbo->qn(array('name', 'alias', 'description')))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . $id_service);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$default = $dbo->loadAssoc();
		}
		
		$this->tag 		 = &$tag;
		$this->idService = &$id_service;
		$this->row 		 = &$row;
		$this->default 	 = &$default;

		// Display the template
		parent::display($tpl);
	}
}
