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
class VikAppointmentsViewemppaylist extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();
		
		$auth = EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=emplogin', false));
			exit;
		}

		$lim  = 10;
		$lim0 = $app->getUserStateFromRequest('emppaylist.limitstart', 'limitstart', 0, 'uint');

		$payments = array();

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('id_employee') . ' = ' . $auth->id)
			->order($dbo->qn('ordering') . ' ASC');
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$payments = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = $pageNav->getPagesLinks();
		}

		// get ordering bounds
		$q = $dbo->getQuery(true)
			->select('MIN(' . $dbo->qn('ordering') . ') AS ' . $dbo->qn('min'))
			->select('MAX(' . $dbo->qn('ordering') . ') AS ' . $dbo->qn('max'))
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('id_employee') . ' = ' . $auth->id);

		$dbo->setQuery($q);
		$dbo->execute();

		$bounds = $dbo->loadObject();
		
		$this->auth 	= &$auth;
		$this->payments = &$payments;
		$this->bounds 	= &$bounds;
		$this->navbut 	= &$navbut;
		
		// Display the template
		parent::display($tpl);
	}
}
