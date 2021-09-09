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
class VikAppointmentsViewempcoupons extends JViewUI
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
		$lim0 = $app->getUserStateFromRequest('empcoupons.limitstart', 'limitstart', 0, 'uint');
		
		$coupons = array();

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `c`.*')
			->from($dbo->qn('#__vikappointments_coupon', 'c'))
			->leftjoin($dbo->qn('#__vikappointments_coupon_employee_assoc', 'a') . ' ON ' . $dbo->qn('a.id_coupon') . ' = ' . $dbo->qn('c.id'))
			->where($dbo->qn('a.id_employee') . ' = ' . $auth->id);

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$coupons = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = $pageNav->getPagesLinks();
		}
		
		$this->auth 	= &$auth;
		$this->coupons 	= &$coupons;
		$this->navbut 	= &$navbut;
		
		// Display the template
		parent::display($tpl);
	}
}
