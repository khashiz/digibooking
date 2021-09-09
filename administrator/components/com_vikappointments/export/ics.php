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

/**
 * Class used to export a list of reservations in ICS format.
 *
 * The ICS format is compatible with any calendar client/cloud service,
 * such as Apple iCal or Google Calendar.
 *
 * Since Microsoft Outlook Calendar uses non-standard syntax, this ICS
 * may not display the correct timezone when the DST is on.
 */
class VikExporterICS
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
	 * The heading details of the ICS.
	 *
	 * @var string
	 */
	private $head;
	
	/**
	 * Class constructor.
	 *
	 * @param 	integer  $from_ts 	The starting timestamp.
	 * @param 	integer  $to_ts 	The ending timestamp.
	 * @param 	integer  $id_emp 	The employee ID.
	 *
	 * @uses 	setHeader()
	 */
	public function __construct($from_ts, $to_ts, $id_emp)
	{
		$this->from 	= (int) $from_ts;
		$this->to 		= (int) $to_ts;
		$this->id_emp 	= (int) $id_emp;
		
		$this->setHeader();
	}
	
	/**
	 * Sets the ICS heading information.
	 *
	 * @param 	string 	$version 	The ICS syntax version.
	 * @param 	string 	$calscale 	The scale of the calendar.
	 * @param 	string 	$timezone 	The timezone to use.
	 */
	public function setHeader($version = '2.0', $calscale = 'GREGORIAN', $timezone = null)
	{
		$this->head = "VERSION:{$version}\n";
		$this->head .= "PRODID:-//e4j//VikAppointments " . VIKAPPOINTMENTS_SOFTWARE_VERSION . "//EN\n";
		$this->head .= "CALSCALE:{$calscale}\n";

		if (empty($timezone))
		{
			$timezone = date_default_timezone_get();
		}

		$this->head .= "X-WR-TIMEZONE:{$timezone}\n";
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
	 * Used to set a different title depending on the client.
	 * For administrator: SERVICE_NAME - FIRST_NAME LAST_NAME
	 * For customers: SERVICE_NAME
	 *
	 * Also used to display sensitive data or not.
	 * For example the name of the employee (if not visible) will
	 * be shown only for an administrator.
	 *
	 * @param 	boolean  True for administrator, otherwise false.
	 * 
	 * @return 	self 	 This object to support chaining.
	 */
	public function setAdminInterface($is)
	{
		$this->is_admin = $is;
	}
	
	/**
	 * Fetches the records to export.
	 *
	 * @return 	string 	The resulting string to export.
	 *
	 * @uses 	fetchArray()
	 */
	public function getString()
	{
		$dbo = JFactory::getDbo();

		// build query

		$q = $dbo->getQuery(true)
			->select('`r`.*')
			->select(array(
				$dbo->qn('e.lastname'),
				$dbo->qn('e.firstname'),
				$dbo->qn('e.timezone'),
				$dbo->qn('s.name', 'sname'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
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
		};

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			// fetch the array
			$rows = $this->fetchArray($dbo->loadAssocList());
		}
		else
		{
			$rows = "";
		}

		// fill the ICS string to export

		$ics  = "BEGIN:VCALENDAR\n";
		$ics .= $this->head; 
		$ics .= $rows;
		$ics .= "END:VCALENDAR\n";
		
		return $ics;
	}
	
	/**
	 * Fetches the provided records to build the body of the ICS.
	 *
	 * @param 	array 	$rows 	The records to parse, usually retrieved with the getString() method.
	 *
	 * @return 	string 	The body of the ICS.
	 *
	 * @uses 	escape()
	 * @uses 	tsToCal()
	 */
	protected function fetchArray(array $rows)
	{
		$config = UIFactory::getConfig();
		
		$address 	 = $config->get('agencyname');
		$time_format = $config->get('timeformat');
		$default_tz  = date_default_timezone_get();

		$str = '';
		
		foreach ($rows as $r)
		{
			if ($r['id_parent'] != -1 && !empty($r['checkin_ts']))
			{
				$uri = JUri::root() . "index.php?option=com_vikappointments&view=order&ordnum={$r['id']}&ordkey={$r['sid']}";
				
				$cf = json_decode($r['custom_f'], true);

				if (empty($cf))
				{
					$cf = array();
				}

				// service
				$description = JText::_('VAPMANAGERESERVATION4') . ": " . $r['sname'] . "\\n";

				// the name of the employee should be displayed only if it is visible
				// or if the ICS is made for an administrator
				if ($this->is_admin || $r['view_emp'])
				{
					// employee
					$description .= JText::_('VAPMANAGERESERVATION3') . ": " . $r['lastname'] . ' ' . $r['firstname'] . "\\n";
				}
					
				$cust_name = $r['purchaser_nominative'];

				foreach ($cf as $k => $v)
				{
					if (!empty($v))
					{
						if ($v[0] == '[' && $v[strlen($v) - 1] == ']')
						{
							$tmp = json_decode($v);

							if (is_array($tmp))
							{
								$v = implode(', ', $tmp);
							}
						}

						$description .= JText::_($k).": ".$v."\\n";
					}
				}
				
				// strip the trailing new line
				$description = rtrim($description, "\\n");

				$summary = $r['sname'];
				// sets a different title for the administrator
				if ($this->is_admin && !empty($cust_name))
				{
					$summary .= ' - ' . $cust_name;
				}
				
				if (empty($r['timezone']))
				{
					$r['timezone'] = $default_tz;
				}

				VikAppointments::setCurrentTimezone($r['timezone']);

				// compose ICS body for this record
					
				$str .= "BEGIN:VEVENT\n";
				$str .= "DTEND;TZID={$r['timezone']}:" . $this->tsToCal($r['checkin_ts'] + $r['duration'] * 60, false) . "\n";
				$str .= "UID:{$r['id']}{$r['sid']}\n";
				$str .= "DTSTAMP:" . $this->tsToCal(time()) . "\n";
				$str .= "LOCATION:" . $this->escape($address) . "\n";
					
				if (strlen($description))
				{
					$str .= "DESCRIPTION:" . $this->escape($description) . "\n";
				}

				$str .= "URL;VALUE=URI:" . $this->escape($uri) . "\n";
				$str .= "SUMMARY:" . $this->escape($summary) . "\n";
				$str .= "DTSTART;TZID={$r['timezone']}:" . $this->tsToCal($r['checkin_ts'], false) . "\n";
				$str .= "END:VEVENT\n";
			}
		}
		
		return $str;
	}
	
	/**
	 * Exports the provided ICS string in the apposite format.
	 * When the auto-download is enabled, the method will stop the flow (exit).
	 *
	 * @param 	string 	$ics 		The ICS string to export, usually fetched 
	 * 								with the getString() method.
	 * @param 	string 	$file_name 	The file name to use. In case the auto-download
	 * 								is disabled, the ICS will by written in this file (full path).
	 *
	 * @return 	void
	 */
	public function export($ics = '', $file_name = '')
	{
		if ($this->auto_download)
		{
			header("Content-Type: application/octet-stream;"); 
			header("Content-Disposition: attachment;filename=\"{$file_name}\"");
			header("Cache-Control: no-store, no-cache");
		
			$f = fopen('php://output', 'w');
			fwrite($f, $ics);
			fclose($f);

			exit;
		}
		else
		{
			$f = fopen($file_name, 'w+');
			$w = fwrite($f, $ics);
			fclose($f);
		}
	}

	/**
	 * Displays the ICS string on a blank web page.
	 *
	 * @param 	string 	$ics 		The ICS string to export, usually fetched 
	 * 								with the getString() method.
	 * @param 	string 	$file_name 	The file name to use.
	 *
	 * @return 	void
	 */
	public function renderBrowser($ics, $file_name)
	{
		header("Content-Type: text/calendar; charset=utf-8");
		header("Content-Disposition: attachment; filename=\"{$file_name}\"");
		echo $ics;
	}
	
	/**
	 * Converts the timestamp using the apposite ICS datetime format.
	 *
	 * @param 	integer  $ts 		The timestamp to convert
	 * @param 	boolean  $use_tz 	Flag to consider the timezone or not.
	 *
	 * @return 	string 	 The resulting datetime string.
	 */
	protected function tsToCal($ts, $use_tz = true)
	{
		return date($use_tz ? self::ICS_DATETIME_FORMAT : self::ICS_DATETIME_FORMAT_NO_TZ, $ts);
	}
	
	/**
	 * Escapes the provided to string to avoid syntax errors.
	 *
	 * @param 	string 	$str 	The string to escape.
	 *
	 * @return 	string 	The escaped string.
	 */ 
	protected function escape($str)
	{
		return preg_replace('/([\,;])/','\\\$1', $str);
	}
	
	/**
	 * The ICS standard datetime format (with timezone).
	 *
	 * @var string
	 */
	const ICS_DATETIME_FORMAT = 'Ymd\THis\Z';
	
	/**
	 * The ICS standard datetime format (without timezone).
	 *
	 * @var string
	 */
	const ICS_DATETIME_FORMAT_NO_TZ = 'Ymd\THis';
}
