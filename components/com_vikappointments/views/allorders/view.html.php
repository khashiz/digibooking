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
class VikAppointmentsViewallorders extends JViewUI
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
		$user 	= JFactory::getUser();

		$itemid = $input->getInt('Itemid', 0);
		
		if (!$user->guest)
		{
			$lim 	= 1000;
			$lim0 	= $app->getUserStateFromRequest('allorders.limitstart', 'limitstart', 0, 'uint');
			$navbut = "";
			
			$orders 	= array();
			$customer 	= array('id' => 0);

			$has_packages = false;
			
			$lang_services 	= array();
			$lang_employees = array();

			$q = $dbo->getQuery(true);

			$q->select('SQL_CALC_FOUND_ROWS ' . $dbo->qn('r.id'));
			$q->select($dbo->qn(array(
				'r.sid', 'r.checkin_ts', 'r.status', 'r.total_cost', 'r.createdon', 'r.view_emp', 'r.id_parent', 'r.duration',
			)));
			$q->select(array(
				$dbo->qn('e.id', 'empid'),
				$dbo->qn('e.nickname', 'empname'),
				$dbo->qn('e.timezone'),
				$dbo->qn('s.id', 'serid'),
				$dbo->qn('s.name', 'sername'),
			));

			$q->from($dbo->qn('#__vikappointments_reservation', 'r'));
			$q->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'));
			$q->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'));
			$q->leftjoin($dbo->qn('#__vikappointments_users', 'u') . ' ON ' . $dbo->qn('r.id_user') . ' = ' . $dbo->qn('u.id'));

			$q->where($dbo->qn('u.jid') . ' = ' . $user->id);
			$q->andWhere(array(
				$dbo->qn('r.id_parent') . ' = -1',
				$dbo->qn('r.id_parent') . ' = ' . $dbo->qn('r.id'),
			), 'OR');

			$q->order($dbo->qn('r.id') . ' DESC');
			
			$dbo->setQuery($q, $lim0, $lim);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$rows = $dbo->loadAssocList();
				$dbo->setQuery('SELECT FOUND_ROWS();');
				jimport('joomla.html.pagination');
				$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
				$navbut  = $pageNav->getPagesLinks();
				
				for ($i = 0; $i < count($rows); $i++)
				{
					$orders[] = $rows[$i];

					if ($rows[$i]['id_parent'] == -1)
					{
						// remove where clause and adjust it to recover children orders
						$q->clear('where');
						$q->clear('limit');
						$q->where($dbo->qn('r.id_parent') . ' = ' . $rows[$i]['id']);
						
						$dbo->setQuery($q);
						$dbo->execute();

						if ($dbo->getNumRows())
						{
							foreach ($dbo->loadAssocList() as $child)
							{
								$child['child'] = 1;
								$orders[] = $child;
							}
						}    
					}
				}
				
				$lang_services  = VikAppointments::getTranslatedServices();
				$lang_employees = VikAppointments::getTranslatedEmployees();
			}

			// get customer details

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_users'))
				->where($dbo->qn('jid') . ' = ' . $user->id);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$customer = $dbo->loadAssoc();

				// check if the customer owns at least a package order

				$q = $dbo->getQuery(true)
					->select(1)
					->from($dbo->qn('#__vikappointments_package_order'))
					->where($dbo->qn('id_user') . ' = ' . $customer['id']);
				
				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				$has_packages = (bool) $dbo->getNumRows();
			}

			$this->orders 		 = &$orders;
			$this->navbut 		 = &$navbut;
			$this->customer 	 = &$customer;
			$this->langServices  = &$lang_services;
			$this->langEmployees = &$lang_employees;
			$this->hasPackages 	 = &$has_packages;
		}
		else
		{
			// load fancybox to support login GDPR popup
			VikAppointments::load_fancybox();

			// user not logged in, use the login/registration layout
			$tpl = 'login';
		}
		
		$this->user   = &$user;
		$this->itemid = &$itemid;
		
		// prepare page content
		VikAppointments::prepareContent($this);
		
		// Display the template
		parent::display($tpl);
	}
}
