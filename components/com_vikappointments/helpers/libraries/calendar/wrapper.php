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

UILoader::import('libraries.calendar.rect');

/**
 * Class used to wrap a list of calendar appointments.
 *
 * @since 	1.6
 * @see 	CalendarRect
 */
class CalendarWrapper
{
	/**
	 * A list of appointments.
	 *
	 * @var CalendarRect[]
	 */
	protected $list;

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		$this->list = array();
	}

	/**
	 * Pushes a new appointment within the internal list.
	 *
	 * @param 	CalendarRect  $app 	The appointment to push.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function push(CalendarRect $app)
	{
		$this->list[] = $app;

		return $this;
	}

	/**
	 * Checks if there are reservations within the calendar.
	 *
	 * @return 	boolean  True if any, false otherwise.
	 */
	public function has()
	{
		return count($this->list);
	}

	/**
	 * Returns the full events list.
	 *
	 * @return 	array
	 */
	public function getEventsList()
	{
		$events = array();

		foreach ($this->list as $app)
		{
			$events = array_merge($events, $app->events());
		}

		return $events;
	}

	/**
	 * Returns the minimum hour found. In case there are no appointments
	 * the default value will be returned.
	 *
	 * @param 	integer  $def 	The default opening hour.
	 *
	 * @return 	integer  The minimum hour.
	 */
	public function getMinimumHour($def)
	{
		if (!$this->has())
		{
			// no appointments found
			return $def;
		}

		$min = null;

		foreach ($this->list as $rect)
		{
			if (!$rect->isSameDay())
			{
				// we can stop immediately as we have an 
				// appointment that started in the previous day
				return 0;
			}
			else if (is_null($min))
			{
				$min = $rect->startH();
			}
			else
			{
				$min = min(array($rect->startH(), $min));
			}
		}

		return $min;
	}

	/**
	 * Returns the appointment that intersects the given bounds.
	 *
	 * @param 	integer  $start  The start delimiter.
	 * @param 	integer  $end 	 The end delimiter.
	 *
	 * @return 	mixed 	 The appointment in case there is an intersection, false otherwise.
	 *
	 * @uses 	getIntersections()
	 */
	public function getIntersection($start, $end)
	{
		$tmp = $this->getIntersections($start, $end);

		if ($tmp)
		{
			return $tmp[0];
		}

		return false;
	}

	/**
	 * Returns a list of appointments that intersect the given bounds.
	 *
	 * @param 	integer  $start  The start delimiter.
	 * @param 	integer  $end 	 The end delimiter.
	 *
	 * @return 	array 	 The appointments that intersect the bounds.
	 */
	public function getIntersections($start, $end)
	{
		$tmp = array();

		foreach ($this->list as $i => $app)
		{
			if ($app->intersects($start, $end))
			{
				$tmp[] = $this->list[$i];
			}
		}

		// return all intersections found
		return $tmp;
	}
}
