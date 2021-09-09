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
class VikAppointmentsViewvikappointments extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return void
	 */
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		// Set the toolbar
		$this->addToolBar();
		
		$ajax_params = array(
			'from' => array(
				$input->getInt('from_id', 0),
				$input->getInt('from_wait_id', 0),
				$input->getInt('from_cust_id', 0),
				$input->getInt('from_pack_id', 0),
			),
			'last' => array(
				$input->getInt('last_id', 0),
				$input->getInt('last_wait_id', 0),
				$input->getInt('last_cust_id', 0),
				$input->getInt('last_pack_id', 0),
			),
		);

		$is_tmpl = !strcmp($input->get('tmpl'), 'component');

		$waiting_list_enabled 	= VikAppointments::isWaitingList(true);
		$packages_enabled 		= VikAppointments::isPackagesEnabled(true);
		
		// LATEST RESERVATIONS

		$latest_reservations = array();

		$q = $dbo->getQuery(true)
			->select('`r`.*')
			->select(array(
				$dbo->qn('s.name', 'sname'),
				$dbo->qn('e.nickname', 'ename'),
				$dbo->qn('r.status'),
				$dbo->qn('e.timezone'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
			->where(array(
				$dbo->qn('r.id_parent') . ' <> -1',
				$dbo->qn('r.closure') . ' = 0',
			))
			->order($dbo->qn('r.id') . ' DESC');

		$dbo->setQuery($q, 0, 10);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$latest_reservations = $dbo->loadAssocList();
		}

		// INCOMING RESERVATIONS

		$incoming_reservations = array();

		$q = $dbo->getQuery(true)
			->select('`r`.*')
			->select(array(
				$dbo->qn('s.name', 'sname'),
				$dbo->qn('e.nickname', 'ename'),
				$dbo->qn('r.status'),
				$dbo->qn('e.timezone'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
			->where(array(
				$dbo->qn('r.id_parent') . ' <> -1',
				$dbo->qn('r.closure') . ' = 0',
				$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn('r.checkin_ts') . ' > ' . time(),
			))
			->order($dbo->qn('r.checkin_ts') . ' ASC');

		$dbo->setQuery($q, 0, 10);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$incoming_reservations = $dbo->loadAssocList();
		}

		// LATEST WAITING LIST
		
		$latest_waiting = array();

		if ($waiting_list_enabled)
		{
			$q = $dbo->getQuery(true)
				->select('`w`.*')
				->select(array(
					$dbo->qn('s.name', 'sname'),
					$dbo->qn('e.nickname', 'ename'),
					$dbo->qn('e.timezone'),
				))
				->from($dbo->qn('#__vikappointments_waitinglist', 'w'))
				->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('w.id_service') . ' = ' . $dbo->qn('s.id'))
				->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('w.id_employee') . ' = ' . $dbo->qn('e.id'))
				->order($dbo->qn('w.id') . ' DESC');

			$dbo->setQuery($q, 0, 10);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$latest_waiting = $dbo->loadAssocList();
			}
		}

		// INCOMING WAITING LIST

		$incoming_waiting = array();

		if ($waiting_list_enabled)
		{
			$q = $dbo->getQuery(true)
				->select('`w`.*')
				->select(array(
					$dbo->qn('s.name', 'sname'),
					$dbo->qn('e.nickname', 'ename'),
					$dbo->qn('e.timezone'),
				))
				->from($dbo->qn('#__vikappointments_waitinglist', 'w'))
				->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('w.id_service') . ' = ' . $dbo->qn('s.id'))
				->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('w.id_employee') . ' = ' . $dbo->qn('e.id'))
				->where($dbo->qn('w.timestamp') . ' > ' . (time() - 86400))
				->order($dbo->qn('w.timestamp') . ' ASC');

			$dbo->setQuery($q, 0, 10);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$incoming_waiting = $dbo->loadAssocList();
			}
		}

		// LATEST CUSTOMERS

		$latest_customers = array();

		$q = $dbo->getQuery(true)
			->select('`u`.*')
			->from($dbo->qn('#__vikappointments_users', 'u'))
			->where($dbo->qn('u.billing_name') . ' <> ""')
			->order($dbo->qn('u.id') . ' DESC');

		$dbo->setQuery($q, 0, 10);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$latest_customers = $dbo->loadAssocList();
		}

		// LOGGED CUSTOMERS

		$logged_customers = UIApplication::getInstance()->getLoggedUsers(10);

		// LATEST PACKAGES

		$latest_packages = array();

		if ($packages_enabled)
		{
			$q = $dbo->getQuery(true)
				->select('`o`.*')
				->from($dbo->qn('#__vikappointments_package_order', 'o'))
				->where(array(
					$dbo->qn('o.status') . ' = ' . $dbo->q('CONFIRMED'),
					$dbo->qn('o.status') . ' = ' . $dbo->q('PENDING'),
				), 'OR')
				->order($dbo->qn('o.id') . ' DESC');

			$dbo->setQuery($q, 0, 10);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$latest_packages = $dbo->loadAssocList();
			}
		}

		// REDEEMED PACKAGES

		$used_packages = array();

		if ($packages_enabled)
		{
			$q = $dbo->getQuery(true)
				->select('`o`.*')
				->select(array(
					$dbo->qn('p.name', 'package_name'),
					$dbo->qn('a.modifiedon'),
					$dbo->qn('a.used_app'),
					$dbo->qn('a.num_app'),
				))
				->from($dbo->qn('#__vikappointments_package_order', 'o'))
				->leftjoin($dbo->qn('#__vikappointments_package_order_item', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_order'))
				->leftjoin($dbo->qn('#__vikappointments_package', 'p') . ' ON ' . $dbo->qn('p.id') . ' = ' . $dbo->qn('a.id_package'))
				->where(array(
					$dbo->qn('o.status') . ' = ' . $dbo->q('CONFIRMED'),
					$dbo->qn('a.used_app') . ' > 0',
				))
				->order($dbo->qn('a.modifiedon') . ' DESC');

			$dbo->setQuery($q, 0, 10);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$used_packages = $dbo->loadAssocList();
			}
		}
		
		$this->latestReservations 	= &$latest_reservations;
		$this->incomingReservations = &$incoming_reservations;

		$this->latestWaiting 	= &$latest_waiting;
		$this->incomingWaiting 	= &$incoming_waiting;

		$this->latestCustomers = &$latest_customers;
		$this->loggedCustomers = &$logged_customers;

		$this->latestPackages 	= &$latest_packages;
		$this->usedPackages 	= &$used_packages;

		$this->filters 				= &$filters;
		$this->ajaxParams 			= &$ajax_params;
		$this->isTmpl 				= &$is_tmpl;
		$this->waitingListEnabled 	= &$waiting_list_enabled;
		$this->packagesEnabled 		= &$packages_enabled;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar()
	{
		// Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWDASHBOARD'), 'vikappointments');

		if (JFactory::getUser()->authorise('core.admin', 'com_vikappointments'))
		{
			JToolBarHelper::preferences('com_vikappointments');
		}
	}
}
