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
 * VikAppointments conversion code class handler.
 * In order to work, the configuration must own the 
 * following settings:
 * - conversion_track 	boolean  Flag to check if the conversion track is enabled.
 *
 * @since  	1.6
 */
class VAPConversion
{
	/**
	 * A list of instances.
	 *
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * A list of supported conversion rules.
	 *
	 * @var array
	 */
	protected $list = array();

	/**
	 * The database table.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The page that we are visiting.
	 *
	 * @var string
	 */
	protected $page;

	/**
	 * Returns the list of all the pages that support conversions.
	 *
	 * @return array
	 */
	public static function getSupportedPages()
	{
		return array(
			'confirmapp',
			'order',
		);
	}

	/**
	 * Returns the list of all the types (db tables) that support conversions.
	 *
	 * @return array
	 */
	public static function getSupportedTypes()
	{
		return array(
			'reservation',
		);
	}

	/**
	 * Returns a new instance of this object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param 	mixed 	$options 	The database table or an array of options.
	 *
	 * @return 	self 	A new instance of this object.
	 *
	 * @see 	__construct() 	for further details about the $options array.
	 */
	public static function getInstance($options = null)
	{
		$sign = serialize($options);

		if (!isset(static::$instances[$sign]))
		{
			static::$instances[$sign] = new static($options);
		}

		return static::$instances[$sign];
	}

	/**
	 * Class constructor.
	 *
	 * @param 	mixed 	$options 	The database table or an array of options.
	 *								The options array can contain the values below
	 * 								- table 	 the database table name ("reservations" by default).
	 * 											 Since we are using a class of VikAppointments,
	 * 											 the prefix "#__vikappointments_" must be omitted;
	 *
	 * @uses 	loadConversionRules()
	 */
	public function __construct($options = null)
	{
		if (!is_array($options))
		{
			// string given, create an array of options
			$options = array('table' => $options);
		}

		if (empty($options['table']))
		{
			// the table attribute is empty, use the default table
			$options['table'] = '#__vikappointments_reservation';
		}
		else
		{
			// prepend the table prefix to the existing value
			$options['table'] = '#__vikappointments_' . $options['table'];
		}

		if (empty($options['page']))
		{
			// the page attribute is empty, ignore this filter
			$options['page'] = '*';
		}

		$this->table = $options['table'];
		$this->page  = $options['page'];

		$this->loadConversionRules();
	}

	/**
	 * Loads all the conversion rules supported
	 * by the specified table.
	 *
	 * @return 	void
	 */
	protected function loadConversionRules()
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_conversion'))
			->where(array(
				$dbo->qn('published') . ' = 1',
				$dbo->qn('type') . ' = ' . $dbo->q(str_replace('#__vikappointments_', '', $this->table)),
			));

		if ($this->page != '*')
		{
			$q->where($dbo->qn('page') . ' = ' . $dbo->q($this->page));
		}

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadObjectList() as $obj)
			{
				// decode statuses array
				$obj->statuses = (array) json_decode($obj->statuses);
				// push the record within the list
				$this->list[] = $obj;
			}
		}
	}

	/**
	 * Attaches the script used for the conversion code.
	 * The script will be printed only if it is configured
	 * and the order hasn't been tracked yet.
	 *
	 * @param 	array 	$order 	The order details. The array supports
	 * 							all these keys:
	 * 							- id 			the order identifier;
	 * 							- status 		the order status;
	 * 							- conversion 	the conversion identifier;
	 *							- total_cost 	the total cost;
	 * 							- tot_paid 		the total amount paid.
	 *
	 * @return 	void
	 *
	 * @uses 	shouldBeTracked()
	 * @uses 	registerOrder()
	 * @uses 	parseSnippet()
	 */
	public function trackCode(array $order = array())
	{
		$config = UIFactory::getConfig();

		// check if conversion is enabled
		$enabled = $config->getBool('conversion_track', 0);

		if (!$enabled)
		{
			// disabled conversion
			return;
		}

		// get compliant conversion object
		$conversion = $this->shouldBeTracked($order);

		if (!$conversion)
		{
			// conversion code disabled or not compliant
			return;
		}

		// register order
		$this->registerOrder($order);

		if ($conversion->jsfile)
		{
			// append JS file
			JHtml::_('script', $conversion->jsfile);
		}

		// parse snippet
		$snippet = $this->parseSnippet($conversion->snippet, $order);

		if ($snippet)
		{
			// attach the snippet to the document
			JFactory::getDocument()->addScriptDeclaration($snippet);
		}
	}

	/**
	 * Checks if the given order should be tracked.
	 * 
	 * @param 	array 	&$order  The order that should be tracked.
	 *
	 * @return 	mixed 	The conversion record object if found, otherwise false.
	 */
	public function shouldBeTracked(array &$order)
	{
		if (!$this->list)
		{
			// no conversion track found
			return false;
		}

		if (isset($order['conversion']))
		{
			$conversion = $order['conversion'];
		}
		else
		{
			$cookie = JFactory::getApplication()->input->cookie;
			// try to get the last conversion used from the cookie
			$conversion = $cookie->get('vapconversion', '', 'string');
		}
		
		$status = isset($order['status']) ? $order['status'] : '*';

		$new_code = $this->page . '.' . $status;

		// iterate the records list
		foreach ($this->list as $code)
		{
			if (($status == '*' || in_array($status, $code->statuses)) && strcasecmp($new_code, $conversion))
			{
				// the status changed, track it
				$order['conversion'] = $new_code;

				// return the tracking record
				return $code;
			}
		}

		// type not supported or same conversion type
		return false;
	}

	/**
	 * Updates the order in the database to register the conversion code.
	 * In case the ID is not provided, the conversion will be registered in
	 * the cookie of the browser.
	 *
	 * @param 	array 	$order  The order to track.
	 *
	 * @return 	void
	 */
	protected function registerOrder(array $order)
	{
		if (isset($order['id']))
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->update($dbo->qn($this->table))
				->set($dbo->qn('conversion') . ' = ' . $dbo->q($order['conversion']))
				->where($dbo->qn('id') . ' = ' . (int) $order['id']);

			$dbo->setQuery($q);
			$dbo->execute();
		}
		else
		{
			$cookie = JFactory::getApplication()->input->cookie;

			// keep the tracking cookie only for 15 minutes
			$cookie->set('vapconversion', $order['conversion'], time() + (15 * 60), '/');
		}
	}

	/**
	 * Parses the snippet to inject some information about 
	 * the order, such as the total amount paid.
	 *
	 * @param 	string 	$snippet  The javascript snippet.
	 * @param 	array 	$order 	  The order to track.
	 *
	 * @param 	string 	The resulting snippet.
	 */
	public function parseSnippet($snippet, array $order)
	{
		// remove starting <script> tag (if any)
		$snippet = preg_replace("/^\s*<\s*?script[\s0-9a-zA-Z=\"'\/]*>/", '', $snippet);

		// remove ending </script> tag (if any)
		$snippet = preg_replace("/<\s*?\/\s*?script\s*?>\s*$/", '' ,$snippet);

		foreach ($order as $k => $v)
		{
			if (is_scalar($v))
			{
				// replace specified placeholders with the order vars
				$snippet = preg_replace("/{$k}/i", $v, $snippet);
			}
		}

		return $snippet;
	}
}
