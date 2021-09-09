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
class VikAppointmentsViewreservations extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		AppointmentsHelper::load_css_js();
		VikAppointments::load_currency_js();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$ordering = OrderingManager::getColumnToOrder('reservations', 'r.id', 2);

		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keysearch'] 	= $app->getUserStateFromRequest('vapres.keysearch', 'keysearch', '', 'string');
		$filters['status']		= $app->getUserStateFromRequest('vapres.status', 'status', '', 'string');
		$filters['id_payment']	= $app->getUserStateFromRequest('vapres.id_payment', 'id_payment', 0, 'uint');
		$filters['type']		= $app->getUserStateFromRequest('vapres.type', 'type', -1, 'int');
		$filters['datefilter']	= $app->getUserStateFromRequest('vapres.datefilter', 'datefilter', '', 'string');
		$filters['res_id'] 		= $app->getUserStateFromRequest('vapres.res_id', 'res_id', 0, 'int');

		// db object
		$lim 	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		$rows = array();

		// unset filters if we are searching by ID
		if ($filters['res_id'] > 0)
		{
			$filters['keysearch'] 	= '';
			$filters['status'] 		= '';
			$filters['id_payment'] 	= 0;
			$filters['datefilter'] 	= 0;
			$lim0 = 0;
		}

		// get payments

		$payments = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('appointments') . ' = 1')
			->where($dbo->qn('id_employee') . ' <= 0')
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$payments = $dbo->loadAssocList();
		}

		/**
		 * Unset payment filter in case the list is empty.
		 * 
		 * @since 1.6.3
		 */
		if (!count($payments))
		{
			$filters['id_payment'] = 0;
		}

		// get reservations details

		$search_has_id = false;

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `r`.*')
			->select(array(
				$dbo->qn('e.nickname', 'ename'),
				$dbo->qn('e.timezone'),
				$dbo->qn('s.name', 'sname'),
				$dbo->qn('p.name', 'payname'),
				$dbo->qn('u.name', 'createdby_name'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
			->leftjoin($dbo->qn('#__vikappointments_gpayments', 'p') . ' ON ' . $dbo->qn('r.id_payment') . ' = ' . $dbo->qn('p.id'))
			->leftjoin($dbo->qn('#__users', 'u') . ' ON ' . $dbo->qn('r.createdby') . ' = ' . $dbo->qn('u.id'))
			->where(1)
			->order($dbo->qn($ordering['column']) . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keysearch']))
		{
			if (strpos($filters['keysearch'], 'id:') === 0)
			{
				$id = intval(substr($filters['keysearch'], 3));

				$q->where($dbo->qn('r.id') . ' = ' . $id);

				$search_has_id = true;
			}
			else
			{
				$q->andWhere(array(
					$dbo->qn('sid') . ' = ' . $dbo->q($filters['keysearch']),
					$dbo->qn('coupon_str') . ' LIKE ' . $dbo->q("{$filters['keysearch']}%"), // must start with the coupon code
					$dbo->qn('purchaser_nominative') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
					$dbo->qn('purchaser_mail') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
					$dbo->qn('purchaser_phone') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
					$dbo->qn('s.name') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
					$dbo->qn('e.nickname') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"),
				), 'OR');
			}
		}

		if (!empty($filters['status']))
		{
			if ($filters['status'] == 'CONFIRMED')
			{
				$q->where(array(
					$dbo->qn('status') . ' = ' . $dbo->q($filters['status']),
					$dbo->qn('r.closure') . ' = 0',
				));
			}
			else if ($filters['status'] == 'CLOSURE')
			{
				$q->where($dbo->qn('r.closure') . ' = 1');
			}
			else
			{
				$q->where($dbo->qn('status') . ' = ' . $dbo->q($filters['status']));
			}
		}

		if ($filters['id_payment'] > 0)
		{
			$q->where($dbo->qn('id_payment') . ' = ' . $filters['id_payment']);
		}

		if ($filters['type'] != -1)
		{
			$q->where($dbo->qn('paid') . ' = ' . $filters['type']);
		}

		if (!empty($filters['datefilter']))
		{
			$start  = VikAppointments::createTimestamp($filters['datefilter'], 0, 0);
			$end 	= VikAppointments::createTimestamp($filters['datefilter'], 23, 59);

			$q->where($dbo->qn('r.checkin_ts') . ' BETWEEN ' . $start . ' AND ' . $end);
		}

		if ($filters['res_id'] > 0)
		{
			$q->andWhere(array(
				$dbo->qn('r.id') . ' = ' . $filters['res_id'],
				$dbo->qn('r.id_parent') . ' = ' . $filters['res_id'],
			), 'OR');
		}

		// Hide the parent orders if the reservations are not sorted by ID,
		// or we are filtering by date.
		if ($ordering['column'] != 'r.id' || $filters['datefilter'])
		{
			$q->where($dbo->qn('r.id_parent') . ' <> -1');
		}
		// Otherwise hide the children reservations (only if we are not filtering
		// or searching by reservation ID).
		else if ($filters['res_id'] == 0 && !$search_has_id)
		{
			$q->andWhere(array(
				$dbo->qn('r.id_parent') . ' = -1',
				$dbo->qn('r.id_parent') . ' = ' . $dbo->qn('r.id'),
			), 'OR');
		}

		// hide closures in case the ordering is not supported
		$closure_columns = array(
			'r.id',
			'r.checkin_ts',
			'r.status',
			'e.nickname',
		);

		if (!in_array($ordering['column'], $closure_columns))
		{
			$q->where($dbo->qn('r.closure') . ' = 0');
		}

		/**
		 * Add support for manipulating query through the plugins.
		 *
		 * @see 	/site/helpers/libraries/mvc/view.php @ JViewUI::onBeforeListQuery()
		 *
		 * @since 	1.6.2
		 */
		$this->onBeforeListQuery($q);
		
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
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}

		// get custom fields

		$mask = CF_EXCLUDE_REQUIRED_CHECKBOX | CF_EXCLUDE_SEPARATOR;
		$custom_fields = VAPCustomFields::getList(0, 0, 0, $mask);

		$new_type = OrderingManager::getSwitchColumnType('reservations', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);
		
		$this->rows 		= &$rows;
		$this->navbut 		= &$navbut;
		$this->ordering 	= &$ordering;
		$this->filters 		= &$filters;
		$this->payments 	= &$payments;
		$this->customFields = &$custom_fields;
		
		$pdf_params = VikAppointments::getPdfParams();
		$pdf_const 	= VikAppointments::getPdfConstraints();
		$this->pdfParams 		= &$pdf_params;
		$this->pdfConstraints 	= &$pdf_const;
		
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
		
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWRESERVATIONS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('findreservation', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{	
			JToolBarHelper::editList('editreservation', JText::_('VAPEDIT'));
			JToolBarHelper::divider();
		}

		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments') && VikAppointments::isRecurrenceEnabled(true))
		{
			JToolBarHelper::custom('makerecurrence', 'loop', 'loop', JText::_('VAPMAKERECURRENCE'), true);
			JToolBarHelper::divider();	
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::custom('exportres', 'archive', 'archive', JText::_('VAPEXPORT'), false);
			JToolBarHelper::divider();

			JToolBarHelper::custom('printorders', 'print', 'print', JText::_('VAPPRINT'), true);
			JToolBarHelper::spacer();
			
			if ($this->isApiSmsConfigured())
			{
				JToolBarHelper::custom('sendsms', 'mobile', 'mobile', JText::_('VAPSENDSMS'), true);
				JToolBarHelper::spacer();
			}
			
			JToolBarHelper::custom('generateInvoices', 'vcard', 'vcard', JText::_('VAPINVOICE'), true);
			JToolBarHelper::spacer();

			JToolBarHelper::custom('manageclosure', 'unpublish', 'unpublish', JText::_('VAPBLOCK'), false);
			JToolBarHelper::spacer();
		}
		
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteReservations', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return (!empty($this->filters['status'])
			|| $this->filters['id_payment'] > 0
			|| $this->filters['type'] != -1
			|| !empty($this->filters['datefilter']));
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
