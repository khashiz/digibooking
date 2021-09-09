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
class VikAppointmentsViewcustomerinfo extends JViewUI
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
	
		$id 	= $input->getUint('id');
		$tab 	= $input->getString('tabname', 'custinfo_billing');

		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart(array('id' => $id));
		$navbut	= "";

		if ($input->getBool('updateinfo'))
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_users'))
				->set($dbo->qn('notes') . ' = ' . $dbo->q($input->getString('notes')))
				->where($dbo->qn('id') . ' = ' . $id);
			
			$dbo->setQuery($q);
			$dbo->execute();
			
			if ($dbo->getAffectedRows())
			{
				$app->enqueueMessage(JText::_('VAPCUSTOMEREDITED1'));
			}
		}

		$customer = array();

		$q = $dbo->getQuery(true)
			->select('`u`.*')->select($dbo->qn('c.country_name', 'billing_country'))
			->from($dbo->qn('#__vikappointments_users', 'u'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.country_2_code') . ' = ' . $dbo->qn('u.country_code'))
			->where($dbo->qn('u.id') . ' = ' . $id);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			exit ('customer not found');
		}

		$customer = $dbo->loadAssoc();
		$customer['appointments'] = array();

		// get appointments

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `r`.*')
			->select(array(
				$dbo->qn('s.name', 'service_name'),
				$dbo->qn('e.nickname', 'employee_name'),
				$dbo->qn('e.timezone'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
			->where(array(
				$dbo->qn('r.id_parent') . ' <> -1',
				$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
			))
			->andWhere(array(
				$dbo->qn('r.id_user') . ' = ' . $customer['id'],
				$dbo->qn('r.id_user') . ' <= 0 AND ' . $dbo->qn('r.purchaser_mail') . ' = ' . $dbo->q($customer['billing_mail']),
			), 'OR')
			->order($dbo->qn('r.id') . ' DESC');
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim);
		
		if ($dbo->getNumRows())
		{
			$customer['appointments'] = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}
		
		$this->customer = &$customer;
		$this->id 		= &$id;
		$this->navbut 	= &$navbut;
		$this->tab 		= &$tab;

		// Display the template
		parent::display($tpl);
	}
}
