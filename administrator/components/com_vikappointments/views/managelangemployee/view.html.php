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
class VikAppointmentsViewmanagelangemployee extends JViewUI
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
		
		$tag 		 = $input->getString('tag', '');
		$id_employee = $input->getUint('id_employee', 0);
		
		$row = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_lang_employee'))
			->where(array(
				$dbo->qn('tag') . ' = ' . $dbo->q($tag),
				$dbo->qn('id_employee') . ' = ' . $id_employee,
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
			->select($dbo->qn(array('nickname', 'alias', 'note')))
			->from($dbo->qn('#__vikappointments_employee'))
			->where($dbo->qn('id') . ' = ' . $id_employee);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$default = $dbo->loadAssoc();
		}

		$this->tag 			= &$tag;
		$this->idEmployee 	= &$id_employee;
		$this->row 			= &$row;
		$this->default 		= &$default;

		// Display the template
		parent::display($tpl);
	}
}
