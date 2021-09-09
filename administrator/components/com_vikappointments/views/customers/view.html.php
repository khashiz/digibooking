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
class VikAppointmentsViewcustomers extends JViewUI
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

		$ordering = OrderingManager::getColumnToOrder('customers', 'u.id', 1);

		// Set the toolbar
		$this->addToolBar();

		/**
		 * The filters are also handled by the export gateway.
		 *
		 * @see 	libraries.import.classes.customers
		 */
		$filters = array();
		$filters['keys'] 	= $app->getUserStateFromRequest('vapcustomers.keys', 'keys', '', 'string');
		$filters['type'] 	= $app->getUserStateFromRequest('vapcustomers.type', 'utype', 0, 'int');
		$filters['country'] = $app->getUserStateFromRequest('vapcustomers.country', 'country', '', 'string');

		//db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut = "";

		$rows = array();

		$count = $dbo->getQuery(true)
			->select('COUNT(1)')
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->where(array(
				$dbo->qn('r.id_user') . ' <> -1',
				$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn('r.id_parent') . ' <> -1',
			));

		/**
		 * @since 1.6 	A reservation is owned by a customer also when
		 * 				the e-mail of the reservation matches the e-mail
		 * 				of the customer (in this case, the reservation 
		 * 				must not be assigned to anyone).
		 *
		 * (r.id_user = u.id OR (r.id_user <= 0 AND r.purchaser_mail = u.billing_mail))
		 */
		$count->andWhere(array(
			$dbo->qn('r.id_user') . ' = ' . $dbo->qn('u.id'),
			$dbo->qn('r.id_user') . ' <= 0 AND ' . $dbo->qn('r.purchaser_mail') . ' = ' . $dbo->qn('u.billing_mail'),
		), 'OR');
		//

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `u`.*')
			->select('(' . $count . ') AS ' . $dbo->qn('rescount'))
			->from($dbo->qn('#__vikappointments_users', 'u'))
			->where($dbo->qn('u.billing_name') . ' <> ' . $dbo->q(''))
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keys']))
		{
			$q->andWhere(array(
				$dbo->qn('u.billing_name') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('u.billing_mail') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('u.billing_phone') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('u.company') . ' LIKE ' . $dbo->q("%{$filters['keys']}%"),
				$dbo->qn('u.vatnum') . ' = ' . $dbo->q("{$filters['keys']}"),
			));
		}

		if ($filters['type'] == 1)
		{
			// registered
			$q->where($dbo->qn('u.jid') . ' > 0');
		}
		else if ($filters['type'] == -1)
		{
			// guest
			$q->where($dbo->qn('u.jid') . ' <= 0');
		}

		if (strlen($filters['country']))
		{
			$q->where($dbo->qn('u.country_code') . ' = ' . $dbo->q($filters['country']));
		}
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim);

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut="<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}

		$countries = VikAppointmentsLocations::getCountries('country_name');
		
		$new_type = OrderingManager::getSwitchColumnType('customers', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);
		
		$is_sms = $this->isApiSmsConfigured();
		
		$this->rows 	 = &$rows;
		$this->navbut 	 = &$navbut;
		$this->ordering  = &$ordering;
		$this->filters 	 = &$filters;
		$this->isSms 	 = &$is_sms;
		$this->countries = &$countries;
		
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
		//Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWCUSTOMERS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newcustomer', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editcustomer', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}

		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::custom('import', 'upload', 'upload', JText::_('VAPIMPORT'), false);
			JToolBarHelper::spacer();
		}

		JToolBarHelper::custom('export', 'download', 'download', JText::_('VAPEXPORT'), false);
		JToolBarHelper::spacer();

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteCustomers', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['type'] != 0
			|| !empty($this->filters['country']));
	}
	
	/**
	 * Check if the SMS API is configured.
	 *
	 * @return 	boolean  True on success, otherwise false.
	 */
	protected function isApiSmsConfigured()
	{
		$smsapi = VikAppointments::getSmsApi();
		$sms_api_path = VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $smsapi;
		
		if (file_exists($sms_api_path) && strlen($smsapi) > 0)
		{
			require_once $sms_api_path;

			if (method_exists('VikSmsApi', 'sendMessage'))
			{
				return true;
			}
		}

		return false;
	}
}
