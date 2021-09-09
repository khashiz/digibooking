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
class VikAppointmentsViewpackorderinfo extends JViewUI
{
	
	/**
	 * VikAppointments view display method
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();

		$dbo 	= JFactory::getDbo();
		$input 	= JFactory::getApplication()->input;
		
		$oid = $input->getUint('oid', array(0));

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'sid')))
			->from($dbo->qn('#__vikappointments_package_order'))
			->where($dbo->qn('id') . ' = ' . $oid[0]);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		if ($dbo->getNumRows() == 0)
		{
			exit('order not found.');
		}

		$row = $dbo->loadAssoc();
		
		$order = VikAppointments::fetchPackagesOrderDetails($row['id'], $row['sid'], VikAppointments::getDefaultLanguage('site'));
		
		$this->order = &$order;

		// Display the template
		parent::display($tpl);
	}
}
