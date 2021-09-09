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
class VikAppointmentsViewinvfiles extends JViewUI
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

		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keysearch'] 	= $input->getString('keysearch', '');
		$filters['group'] 		= $input->getString('group', '');
		
		$year 	= $input->getInt('year');
		$month 	= $input->getInt('month');

		if ($filters['group'] == 'packages')
		{
			$table = '#__vikappointments_package_order';
		}
		else if ($filters['group'] == 'employees')
		{
			$table = '#__vikappointments_subscr_order';
		}
		else
		{
			$table = '#__vikappointments_reservation';
		}
		
		$tree = array();

		$q = $dbo->getQuery(true);

		$q->select('DATE_FORMAT(FROM_UNIXTIME(' . $dbo->qn('createdon') . '), "%Y") AS ' . $dbo->qn('year'))
			->select('DATE_FORMAT(FROM_UNIXTIME(' . $dbo->qn('createdon') . '), "%c") AS ' . $dbo->qn('mon'))
			->from($dbo->qn($table))
			->group($dbo->qn(array('year', 'mon')))
			->order(array(
				'CAST(' . $dbo->qn('year') . ' AS unsigned) DESC',
				'CAST(' . $dbo->qn('mon') . ' AS unsigned) DESC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadAssocList() as $r)
			{
				if (empty($r['year']))
				{
					$r['year'] = $r['mon'] = -1;
				}
				
				if (empty($tree[$r['year']]))
				{
					$tree[$r['year']] = array();
				}

				$tree[$r['year']][] = $r['mon'];
			}
		}

		/**
		 * Obtain the invoices related to the specifie group.
		 *
		 * @since 1.6
		 */
		$all_inv = AppointmentsHelper::getAllInvoices($filters['group'], true);

		if (!empty($filters['keysearch']))
		{
			$app = array();

			foreach ($all_inv as $invoice)
			{
				$file_name = basename($invoice);
				if (strpos($file_name, $filters['keysearch']) !== false)
				{
					$app[] = $invoice;
				}
			}

			$all_inv = $app;
			unset($app);
		}
		
		$each = array();

		foreach ($all_inv as $invoice)
		{
			$file_name 	= basename($invoice);
			$split 		= explode('-', $file_name);

			$each[$invoice] = array(
				'id' 		=> intval($split[0]),
				'filename' 	=> $file_name,
				'details'	=> '',
				'attr' 		=> array(),
			);

			$q = $dbo->getQuery(true)
				->select($dbo->qn('createdon'))
				->from($dbo->qn($table))
				->where($dbo->qn('id') . ' = ' . $each[$invoice]['id']);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$each[$invoice]['attr'] = $dbo->loadAssoc();
			}
		}

		if (empty($year) || empty($month))
		{
			$today 	= getdate();
			$year 	= $today['year'];
			$month 	= $today['mon'];
		}

		// filter by date

		$rows = array();

		foreach ($all_inv as $invoice)
		{
			if (!empty($each[$invoice]['attr']['createdon']))
			{
				$createdon = $each[$invoice]['attr']['createdon'];
				$arr = getdate($createdon);

				if (($year == -1 && $month = -1 && $createdon == -1) || ($arr['mon'] == $month && $arr['year'] == $year))
				{
					$rows[] = $invoice;
				}
			}
		}

		$all_inv = $rows;

		// slice the list
		
		$limit 		= 20;
		$max_limit 	= count($all_inv);

		if ($max_limit > $limit)
		{
			$all_inv = array_slice($all_inv, 0, $limit);
		}
		
		$loadedAll = $max_limit <= $limit;
		
		$seek = array(
			'year' 	=> $year,
			'month' => $month,
		);

		// compose invoice details

		if (count($all_inv))
		{
			$ids = array_map(function($elem) use ($each)
			{
				return $each[$elem]['id']; 
			}, $all_inv);

			$q = $dbo->getQuery(true)
				->select($dbo->qn('o.id'))
				->from($dbo->qn($table, 'o'))
				->where($dbo->qn('o.id') . ' IN (' . implode(',', $ids) . ')');

			if ($filters['group'] == 'employees')
			{
				$q->select(array(
					$dbo->qn('e.nickname', 'name'),
					$dbo->qn('e.email', 'mail'),
				));

				$q->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('o.id_employee'));
			}
			else
			{
				$q->select(array(
					$dbo->qn('o.purchaser_nominative', 'name'),
					$dbo->qn('o.purchaser_mail', 'mail'),
				));
			}

			$dbo->setQuery($q, 0, count($ids));
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				foreach ($dbo->loadAssocList() as $details)
				{
					$index = $this->invoiceAt($details['id'], $each);

					$each[$index]['details'] = !empty($details['name']) ? $details['name'] : $details['mail']; 
				}
			}
		}
		
		$this->invoicesFiles = &$all_inv;
		$this->eachInvoice 	 = &$each;
		$this->filters 	 	 = &$filters;
		$this->loadedAll 	 = &$loadedAll;
		$this->mediaLimit 	 = &$limit;
		$this->maxLimit 	 = &$max_limit;
		$this->tree 		 = &$tree;
		$this->seek 		 = &$seek;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Returns the key of the specified invoice.
	 *
	 * @param 	integer  $id 	The invoice ID.
	 * @param 	array 	 $pool 	The multi-dimensional array containing the invoices.
	 *
	 * @return 	mixed 	 The key/index of the invoice on success, otherwise false.
	 */
	protected function invoiceAt($id, array $pool)
	{
		foreach ($pool as $k => $row)
		{
			if (array_key_exists('id', $row) && $row['id'] == $id)
			{
				return $k;
			}
		}

		return false;
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar()
	{
		// Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWARCHIVE'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::custom('downloadInvoices', 'download', 'download', JText::_('VAPDOWNLOAD'), true, false);
			JToolBarHelper::divider();  
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList( VikAppointments::getConfirmSystemMessage(), 'deleteInvoices', JText::_('VAPDELETE'));
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return !empty($this->filters['group']);
	}
}
