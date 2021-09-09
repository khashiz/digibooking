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
defined('_JEXEC') or die('Restricted Area');

/**
 * Class used to export a list of reservations in CSV format.
 */
class VikExporterCSV
{
	/**
	 * The starting timestamp to filter the reservations.
	 *
	 * @var integer
	 */	
	private $from;

	/**
	 * The ending timestamp to filter the reservations.
	 *
	 * @var integer
	 */
	private $to;

	/**
	 * The ID of the employee to filter the reservations.
	 * A number equals or lower than 0 will skip this filter.
	 *
	 * @var integer
	 */
	private $id_emp;

	/**
	 * The ID of the order to use. If the number is higher than 0,
	 * all the other filters will be skipped.
	 */
	private $id_order = -1;
	
	/**
	 * Flag to know if the export is for an administrator or for a customer.
	 *
	 * @var boolean
	 */
	private $is_admin = true;
	
	/**
	 * Flag to start the auto-download or not.
	 *
	 * @var boolean
	 */
	private $auto_download = true;
	
	/**
	 * Class constructor.
	 *
	 * @param 	integer  $from_ts 	The starting timestamp.
	 * @param 	integer  $to_ts 	The ending timestamp.
	 * @param 	integer  $id_emp 	The employee ID.
	 */
	public function __construct($from_ts, $to_ts, $id_emp = 0)
	{
		$this->from   = (int) $from_ts;
		$this->to     = (int) $to_ts;
		$this->id_emp = (int) $id_emp;
	}
	
	/**
	 * Forces the ID of the order to fetch.
	 *
	 * @param 	integer  $id_order 	The order ID.
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function forceOrderID($id_order)
	{
		$this->id_order = $id_order;

		return $this;
	}
	
	/**
	 * Sets if the download starts automatically or not.
	 *
	 * @param 	boolean  $enabled 	True for the auto-download,
	 * 								false to write the contents only.
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function setAutoDownload($enabled)
	{
		$this->auto_download = $enabled;

		return $this;
	}

	/**
	 * Forces the ID of the employee.
	 *
	 * @param 	integer  $id_emp 	The ID of the employee.
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function setEmployee($id_emp)
	{
		$this->id_emp = $id_emp;
		
		return $this;
	}
	
	/**
	 * Sets if the CSV can contain sensitive data.
	 * For example the name of the employee (if not visible) will
	 * be shown only for an administrator.
	 *
	 * @param 	boolean  True to allow sensitive data, otherwise false.
	 * 
	 * @return 	self 	 This object to support chaining.
	 */
	public function setAdminInterface($is)
	{
		$this->is_admin = $is;

		return $this;
	}
	
	/**
	 * Fetches the records to export.
	 *
	 * @return 	array 	The resulting array to export.
	 */
	public function getString()
	{	
		$dbo 	= JFactory::getDbo();
		$config = UIFactory::getConfig();
		
		$date_format = $config->get('dateformat');
		$time_format = $config->get('timeformat');

		// build query

		$q = $dbo->getQuery(true)
			->select('`r`.*')
			->select(array(
				$dbo->qn('e.nickname'),
				$dbo->qn('s.name', 'sname'),
				$dbo->qn('a.quantity'),
				$dbo->qn('a.inc_price', 'oprice'),
				$dbo->qn('o.name', 'oname'),
				$dbo->qn('v.name', 'var_name'),
				$dbo->qn('e.timezone'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
			->leftjoin($dbo->qn('#__vikappointments_res_opt_assoc', 'a') . ' ON ' . $dbo->qn('r.id') . ' = ' . $dbo->qn('a.id_reservation'))
			->leftjoin($dbo->qn('#__vikappointments_option', 'o') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'))
			->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('v.id') . ' = ' . $dbo->qn('a.id_variation'))
			->where($dbo->qn('r.closure') . ' = 0');
		
		// check if the reservations should be filtered by employee
		if ($this->id_emp > 0)
		{
			$q->where($dbo->qn('r.id_employee') . ' = ' . $this->id_emp);
		}
		
		// check if the ID of the order is not forced
		if ($this->id_order == -1)
		{
			$q->where(array(
				$dbo->qn('r.id_parent') . ' <> -1',
				$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn('r.checkin_ts') . ' BETWEEN ' . $this->from . ' AND ' . $this->to,
			));
		}
		// otherwise get only the selected order
		else
		{
			$q->andWhere(array(
				$dbo->qn('r.id') . ' = ' . $this->id_order,
				$dbo->qn('r.id_parent') . ' = ' . $this->id_order,
			), 'OR');
		}
		
		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows() == 0)
		{
			return array();
		}
		
		$rows = $dbo->loadAssocList();
		
		// get custom fields

		$cf_emp = 0;

		if ($this->id_emp > 0)
		{
			// search for the employee custom fields too
			$cf_emp = $this->id_emp;
		}

		$mask 	= CF_EXCLUDE_REQUIRED_CHECKBOX | CF_EXCLUDE_SEPARATOR | CF_EXCLUDE_FILE;
		$cf 	= VAPCustomFields::getList(0, $cf_emp, 0, $mask);
		
		// compose table header

		$header = array(
			// id
			JText::_('VAPMANAGERESERVATION0'),
			// sid
			JText::_('VAPMANAGERESERVATION1'),
			// employee
			JText::_('VAPMANAGERESERVATION3'),
			// service
			JText::_('VAPMANAGERESERVATION4'),
			// checkin
			JText::_('VAPMANAGERESERVATION26'),
			// duration
			JText::_('VAPMANAGERESERVATION10'),
			// total cost
			JText::_('VAPMANAGERESERVATION9'),
			// people
			JText::_('VAPMANAGERESERVATION25'),
		);
		
		// custom fields
		foreach ($cf as $c)
		{
			$header[] = JText::_($c['name']);
		}
		
		// options
		$header[] = JText::_('VAPMANAGERESERVATION14');
		
		$default_tz = date_default_timezone_get();
		
		// build the CSV array
		$csv = array($header);

		$_last_id = $_last_index = -1;

		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			if ($rows[$i]['id_parent'] != -1 && !empty($rows[$i]['checkin_ts']))
			{
				// check if the element is already in the list (the last fetched)
				if ($_last_id != $rows[$i]['id'])
				{	
					if (empty($rows[$i]['timezone']))
					{
						$rows[$i]['timezone'] = $default_tz;
					}

					// overwrite always the existing timezone
					VikAppointments::setCurrentTimezone($rows[$i]['timezone']);
					
					$employee_name = $rows[$i]['nickname'];

					// if the CSV is for a customer and the employee is not visible,
					// we need to mask the employee name with something else (like a slash).
					if (!$this->is_admin && !$rows[$i]['view_emp'])
					{
						$employee_name = '/';
					}
					
					$_app = array( 
						$rows[$i]['id'],
						$rows[$i]['sid'],
						$employee_name,
						$rows[$i]['sname'],
						date($date_format . ' ' . $time_format, $rows[$i]['checkin_ts']),
						VikAppointments::formatMinutesToTime($rows[$i]['duration'], $config->getBool('formatduration')),
						VikAppointments::printPriceCurrencySymb($rows[$i]['total_cost']),
						$rows[$i]['people'],
					);
					
					$cfields = json_decode($rows[$i]['custom_f'], true);

					if (empty($cfields))
					{
						$cfields = array();
					}
					
					// custom fields
					foreach ($cf as $c)
					{
						if (!empty($cfields[$c['name']]))
						{
							$value = $cfields[$c['name']];

							if ($c['multiple'])
							{
								$value = implode(', ', json_decode($value, true));
							}
						}
						else
						{
							$value = '';
						}

						$_app[] = $value;
					}
					
					// options (set empty)
					$_app[] = '';

					// push the record in the CSV list
					$csv[] = $_app;
					
					// store the index of the record
					$_last_index = count($csv) - 1;
				} 
				
				// if we are fetching the same record and the option name is not empty,
				// push the option in the list only
				if ($_last_id == $rows[$i]['id'] || !empty($rows[$i]['oname']))
				{
					$_str = '';

					if (strlen(end($csv[$_last_index])) > 0)
					{
						$_str .= ', ';
					}

					$_str .= $rows[$i]['oname'] .
						(strlen($rows[$i]['var_name']) ? ' - ' . $rows[$i]['var_name'] : '') .
						' x' . $rows[$i]['quantity'];

					if ($rows[$i]['oprice'] != 0)
					{
						$_str .= ' ' . VikAppointments::printPriceCurrencySymb($rows[$i]['oprice']);
					}
						
					$csv[$_last_index][count($csv[$_last_index]) - 1] .= $_str;
				}
				
				$_last_id = $rows[$i]['id'];
			}
		}
		
		return $csv;
	}
	
	/**
	 * Exports the provided records using the CSV format.
	 * When the auto-download is enabled, the method will stop the flow (exit).
	 *
	 * @param 	array 	$csv 		The records to export, usually fetched 
	 * 								with the getString() method.
	 * @param 	string 	$file_name 	The file name to use. In case the auto-download
	 * 								is disabled, the CSV will by written in this file (full path).
	 *
	 * @return 	void
	 */
	public function export(array $csv = array(), $file_name = '')
	{
		// should we auto-download the CSV?
		if ($this->auto_download)
		{
			//header("Content-Type: application/octet-stream; ");
			//header("Cache-Control: no-store, no-cache");
			header("Content-Type: text/csv");
			//header('Content-Encoding: UTF-8');
			//header('Content-type: text/csv; charset=UTF-8');
			header("Content-Disposition: attachment;filename=\"{$file_name}\"");
			//echo "\xEF\xBB\xBF"; // UTF-8 BOM for correct encoding on excel
			
			$f = fopen('php://output', 'w');
			foreach ($csv as $fields)
			{
				fputcsv($f, $fields);
			}
			fclose($f);
			exit;
		}
		else
		{
			$f = fopen($file_name, 'w+');
			foreach ($csv as $fields)
			{
				fputcsv($f, $fields);
			}
			fclose($f);
		}
	}	
}
