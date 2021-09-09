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
 * Class used to handle a generic import event.
 * This class is able to import a list of records starting 
 * from a CSV file.
 *
 * The CSV must start with a valid heading, otherwise the first row
 * will be skipped.
 *
 * @since 	1.6
 */
class ImportObject
{
	/**
	 * The XML instructions object.
	 *
	 * @var SimpleXMLElement
	 */
	protected $xml;

	/**
	 * The import entity type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The filter input handler.
	 *
	 * @var mixed
	 */
	protected $filter;

	/**
	 * The path of the file to import.
	 *
	 * @var string
	 */
	protected $file = null;

	/**
	 * The total number of records fetched.
	 * Used by both the import and export methods.
	 *
	 * @var integer
	 */
	protected $total = 0;

	/**
	 * A list of errors.
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Class constructor.
	 *
	 * @param 	object 	$xml 	The XML object.
	 * @param 	string 	$type 	The entity type to import.
	 *
	 */
	public function __construct($xml, $type)
	{
		$this->xml 		= $xml;
		$this->type 	= $type;
		$this->filter 	= JFilterInput::getInstance();
	}

	/**
	 * Returns the path of the file to import.
	 * The path found is always cached to avoid retrieving
	 * it during the next accesses.
	 *
	 * @return 	mixed 	The file path if exists, otherwise false.
	 */
	public function getFile()
	{
		if ($this->file === null)
		{
			$folder = VAPADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;

			$file = glob($folder . $this->type . '_*.csv');

			if (count($file))
			{
				$this->file = array_pop($file);
			}
			else
			{
				$this->file = false;
			}
		}

		return $this->file;
	}

	/**
	 * Checks if the file is ready to be imported.
	 *
	 * @return 	boolean  True if the file exists, otherwise false.
	 *
	 * @uses 	getFile()
	 */
	public function hasFile()
	{
		return (bool) $this->getFile();
	}

	/**
	 * Returns the database table to use while importing the records.
	 *
	 * @return 	string 	The DB table name.
	 */
	public function getTable()
	{
		return (string) $this->xml->table->attributes()->name;
	}

	/**
	 * Returns the primary key of the database table.
	 *
	 * @param 	string 	$def 	The default primary key to use
	 * 							if not specified.
	 *
	 * @return 	string 	The primary key.
	 */
	public function getPrimaryKey($def = 'id')
	{
		$pk = (string) $this->xml->table->attributes()->pk;

		if (empty($pk))
		{
			$pk = $def;
		}

		return $pk;
	}

	/**
	 * Returns all the available columns that can be assigned
	 * to the values listed in the CSV file.
	 *
	 * @param 	boolean  $translate  True to automatically translate the labels.
	 *
	 * @return 	array 	 The list of available columns.
	 */
	public function getColumns($translate = true)
	{
		$columns = array();

		foreach ($this->xml->table->column as $column)
		{
			$obj = new stdClass;

			$obj->name 		= (string) 	$column->attributes()->name;
			$obj->label 	= (string) 	$column->attributes()->label;
			$obj->required 	= (int) 	$column->attributes()->required;
			$obj->default 	= (string) 	$column->attributes()->default;
			$obj->filter 	= (string) 	$column->attributes()->filter;
			$obj->options 	= array();

			foreach ($column->option as $opt)
			{
				$k = (string) $opt->attributes()->value;
				$v = (string) $opt;

				if ($translate)
				{
					$v = JText::_($v);
				}

				$obj->options[$k] = $v;
			}

			if ($translate)
			{
				$obj->label = JText::_($obj->label);
			}

			$columns[$obj->name] = $obj;
		}

		return $columns;
	}

	/**
	 * Returns the cancellation task, if any.
	 *
	 * @return 	mixed 	The cancel task if specified, otherwise false.
	 */
	public function getCancelTask()
	{
		$task = (string) $this->xml->cancel->attributes()->task;

		if (empty($task))
		{
			return false;
		}

		return $task;
	}

	/**
	 * Returns the file containing the sample data to import this type of object.
	 *
	 * @return 	mixed 	The file path if exists, otherwise false.
	 */
	public function getSampleFile()
	{
		$sample = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . $this->type . '.csv';

		if (file_exists($sample))
		{
			return $sample;
		}

		return false;
	}

	/**
	 * Checks if this object owns any sample data file.
	 *
	 * @return 	boolean  True if exists, otherwise false.
	 *
	 * @uses 	getSampleFile()
	 */
	public function hasSampleFile()
	{
		return $this->getSampleFile() !== false;
	}

	/**
	 * Returns a preview of the records contained in the file.
	 *
	 * @param 	integer  $lim 	The maximum number of records to obtain.
	 *
	 * @return 	array 	 The records list.
	 *
	 * @uses 	getFile()
	 */
	public function getRecords($lim = 10)
	{
		$rows = array();

		$file = $this->getFile();

		$handle = fopen($file, 'r');

		$count = 0;

		while (($buffer = fgetcsv($handle)) && $count <= $lim)
		{
			$rows[] = $buffer;
			$count++;
		}

		fclose($handle);

		return $rows;
	}

	/**
	 * Returns the total number of records fetched.
	 *
	 * @return 	integer  The total count.
	 */
	public function getTotalCount()
	{
		return $this->total;
	}

	/**
	 * Pushes a new error in the list.
	 *
	 * @param 	object 	$data 	The record failed.
	 * @param 	string 	$err 	The error message.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	protected function setError($data, $err = '')
	{
		if (empty($err))
		{
			$err = JText::_('VAPIMPORTINSERTERR');
		}

		$str .= '<b>' . $err . '</b><br />';

		$data = (array) $data;

		if ($data)
		{
			$str .= '<pre>' . implode(', ', $data) . '</pre>';
		}

		$this->errors[] = $str;

		return $this;
	}

	/**
	 * Returns a list of errors raised.
	 *
	 * @return 	array 	An errors list.
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Processes the event to import all the records
	 * contained in the CSV file.
	 *
	 * @param 	array 	 $assoc 	Associative array used to match
	 * 								the columns of the table with the columns
	 * 								of the CSV records.
	 * @param 	array 	 $args 		Associative list of additional parameters.
	 *
	 * @return 	integer  The number of imported records.
	 *
	 * @uses 	getColumns()
	 * @uses 	getFile()
	 * @uses 	getTable()
	 * @uses 	getPrimaryKey()
	 * @uses 	bind()
	 */
	public function save(array $assoc, array $args = array())
	{
		$dbo = JFactory::getDbo();
		
		$cols 	= $this->getColumns();
		$file 	= $this->getFile();
		$table 	= $this->getTable();
		$pk 	= $this->getPrimaryKey();

		$handle = fopen($file, 'r');

		$count = $this->total = 0;

		// reset errors list
		$this->errors = array();

		$head = null;

		while (($buffer = fgetcsv($handle)))
		{
			if ($head === null)
			{
				$head = $buffer;
			}
			else
			{
				$record = new stdClass;
				$valid 	= true;

				foreach ($buffer as $k => $v)
				{
					if (!empty($assoc[$head[$k]]))
					{
						// get the column related to the specified CSV head
						$column = $assoc[$head[$k]];

						// if the value is empty, try to use the default column value
						if (empty($v) && !empty($cols[$column]->default))
						{
							$v = $cols[$column]->default;
						}

						// try to filter the specified value
						if (!empty($cols[$column]->filter))
						{
							$v = $this->filter->clean($v, $cols[$column]->filter);
						}

						// check if the value MUST NOT be empty
						if (empty($v) && $cols[$column]->required)
						{
							// empty required value, the object
							// should not be imported
							$valid = false;
						}

						$record->{$assoc[$head[$k]]} = $v;
					}
				}

				if ($valid && $this->bind($record, $args))
				{
					try
					{
						$res = $dbo->insertObject($table, $record, $pk) && $record->{$pk};
						$msg = null;
					}
					catch (Exception $e)
					{
						$res = false;
						$msg = $e->getMessage();
					}

					if ($res)
					{
						$count++;
					}
					else
					{
						$this->setError($record, $msg);
					}
				}

				$this->total++;
			}
		}

		fclose($handle);

		return $count;
	}

	/**
	 * Method used to bind the provided object. By returning
	 * false the system won't proceed importing the current record.
	 *
	 * A record won't be imported if it doesn't own any property.
	 *
	 * @param 	object 	 &$data  The object of the record to import.
	 * @param 	array 	 $args 	 Associative list of additional parameters.
	 *
	 * @return 	boolean  True if the record should be imported, otherwise false.
	 */
	protected function bind(&$data, array $args = array())
	{
		$vars = get_object_vars($data);

		return !is_null($vars) && count(array_keys($vars));
	}

	/**
	 * Returns a list of the records to export.
	 *
	 * @param 	boolean  $full 	True to return the full list.
	 * 							False to return a few elements for preview.
	 *
	 * @return 	array 	 The records to export.
	 *
	 * @uses 	buildExportQuery()
	 */
	public function getExportableRows($full = false)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();

		$q = $this->buildExportQuery($app, $dbo);

		if ($full)
		{
			$lim0 = $lim = null;
		}
		else
		{
			$lim0 = 0;
			$lim  = 10;
		}

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();

			// get the total number of rows
			$dbo->setQuery('SELECT FOUND_ROWS();');
			$this->total = $dbo->loadResult();

			return $rows;
		}

		return array();
	}

	/**
	 * Builds the base query to export all the records.
	 *
	 * @param 	mixed 	$app  The application instance.
	 * @param 	mixed 	$dbo  The database global object.
	 *
	 * @return 	mixed 	The query builder object.
	 *
	 * @uses 	getColumns()
	 * @uses 	getTable()
	 * @uses 	getPrimaryKey()
	 */
	protected function buildExportQuery($app, $dbo)
	{
		$columns = $this->getColumns();
		$table 	 = $this->getTable();
		$pk 	 = $this->getPrimaryKey();

		$q = $dbo->getQuery(true);

		// Calculate the total number of records fetched.
		// Pop the first column to concat SQL_CALC_FOUND_ROWS.
		$q->select('SQL_CALC_FOUND_ROWS ' . $dbo->qn(array_shift($columns)->name));
		// map the columns to select
		$q->select(array_map(array($dbo, 'qn'), array_keys($columns)));

		// define the table to access
		$q->from($dbo->qn($table));

		$ids = $app->input->get('cid', array(), 'string');

		if (count($ids))
		{
			// map the array to quote each element
			$ids = array_map(array($dbo, 'q'), $ids);
			// build IN statement
			$q->where($dbo->qn($pk) . ' IN (' . implode(', ', $ids) . ')');
		}

		return $q;
	}

	/**
	 * Exports the records using the given handler.
	 *
	 * @param 	Exportable 	$handler 	The export handler.
	 * @param 	string 		$name  		The file name.
	 *
	 * @return 	void
	 *
	 * @uses 	getColumns()
	 * @uses 	getExportableRows()
	 */
	public function export($handler, $name)
	{
		// get rows to export (true: ignore limits)
		$rows = $this->getExportableRows(true);

		if (!$name)
		{
			$name = strtolower($this->type);
		}

		// export the records
		$handler->download($name, $rows, $this);
	}
}
