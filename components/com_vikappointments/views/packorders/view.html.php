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
class VikAppointmentsViewpackorders extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$user = JFactory::getUser();

		$itemid = $input->getInt('Itemid', 0);
		
		if ($user->guest)
		{
			$app->redirect(JRoute::_('index.php?option=com_vikappointments&view=allorders' . ($itemid ? '&Itemid=' . $itemid : ''), false));
			exit;
		}

		$lim 	= 5;
		$lim0 	= $app->getUserStateFromRequest('packorders.limitstart', 'limitstart', 0, 'uint');
		$navbut = "";
		
		$orders 	= array();
		$customer 	= array();
	
		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS ' . $dbo->qn('o.id'));
		$q->select($dbo->qn(array(
			'o.sid', 'o.status', 'o.total_cost', 'o.createdon',
		)));

		$q->from($dbo->qn('#__vikappointments_package_order', 'o'));
		$q->leftjoin($dbo->qn('#__vikappointments_users', 'u') . ' ON ' . $dbo->qn('o.id_user') . ' = ' . $dbo->qn('u.id'));

		$q->where($dbo->qn('u.jid') . ' = ' . $user->id);

		$q->order($dbo->qn('o.id') . ' DESC');
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();
	
		if ($dbo->getNumRows())
		{
			$orders = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut  = $pageNav->getPagesLinks();
		}
		
		$this->user 	= &$user;
		$this->orders 	= &$orders;
		$this->navbut 	= &$navbut;
		$this->itemid 	= &$itemid;
		
		// Display the template
		parent::display($tpl);
	}
}
