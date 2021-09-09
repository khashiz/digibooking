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
 * Class used to keep a list of events within a calendar rectangle.
 *
 * @since 1.6
 */
class CalendarRect
{
	/**
	 * The starting delimiter.
	 *
	 * @var integer
	 */
	protected $start;

	/**
	 * The ending delimiter.
	 *
	 * @var integer
	 */
	protected $end;

	/**
	 * The events list.
	 *
	 * @var array
	 */
	protected $events;

	/**
	 * Class constructor.
	 *
	 * @param 	integer  $start  The starting delimiter.
	 * @param  	integer  $end 	 The ending delimiter.
	 * @param 	mixed 	 $event  The event(s) to push.
	 *
	 * @uses 	addEvent()
	 */
	public function __construct($start, $end, $event = null)
	{
		$this->start  = $start;
		$this->end 	  = $end;
		$this->events = array();

		if ($event)
		{
			$this->addEvent($event);
		}
	}

	/**
	 * Pushes a new event within the internal list.
	 *
	 * @param 	object 	The event object.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function addEvent($event)
	{
		if (!is_array($event))
		{
			$event = array($event);
		}

		foreach ($event as $e)
		{
			$this->events[] = $e;
		}

		return $this;
	}

	/**
	 * Returns the starting delimiter.
	 *
	 * @return 	integer
	 */
	public function start()
	{
		return $this->start;
	}

	/**
	 * Returns the hours of the starting delimiter.
	 *
	 * @return 	integer
	 */
	public function startH()
	{
		return (int) date('H', $this->start);
	}

	/**
	 * Returns the minutes of the starting delimiter.
	 *
	 * @return 	integer
	 */
	public function startM()
	{
		return (int) date('i', $this->start);
	}

	/**
	 * Returns the hour-min amount of the starting delimiter.
	 *
	 * @return 	integer
	 *
	 * @uses 	startH()
	 * @uses 	startM()
	 */
	public function startHM()
	{
		return $this->startH() * 60 + $this->startM();
	}

	/**
	 * Returns the ending delimiter.
	 *
	 * @return 	integer
	 */
	public function end()
	{
		return $this->end;
	}

	/**
	 * Returns the hours of the ending delimiter.
	 *
	 * @return 	integer
	 */
	public function endH()
	{
		return (int) date('H', $this->end);
	}

	/**
	 * Returns the minutes of the ending delimiter.
	 *
	 * @return 	integer
	 */
	public function endM()
	{
		return (int) date('i', $this->end);
	}

	/**
	 * Returns the hour-min amount of the ending delimiter.
	 *
	 * @return 	integer
	 *
	 * @uses 	endH()
	 * @uses 	endM()
	 */
	public function endHM()
	{
		return $this->endH() * 60 + $this->endM();
	}

	/**
	 * Checks if the events are referring to the same day or not.
	 * This method return false in the following case:
	 * - start 	2018-07-13 @ 23:00
	 * - end 	2018-07-14 @ 02:00
	 *
	 * @return 	boolean  True if the delimiters are within the same day, false otherwise.
	 */
	public function isSameDay()
	{
		$checkin  = getdate($this->start);
		$checkout = getdate($this->end - 1); // exclusive

		return $checkin['mday'] == $checkout['mday'] 
			&& $checkin['mon'] == $checkout['mon']
			&& $checkin['year'] == $checkout['year'];
	}

	/**
	 * Returns the events list.
	 *
	 * @return 	array
	 */
	public function events()
	{
		return $this->events;
	}

	/**
	 * Returns the first event or the given property (if specified) of the first event.
	 *
	 * @param 	string 	$key  The property to get. Null to return the whole object.
	 * @param  	mixed 	$def  The default value in case the property doesn't exist.
	 *
	 * @return 	mixed 	The event object or a specific property of the object.
	 */
	public function event($key = null, $def = null)
	{
		if (!$key)
		{
			return $this->events[0];
		}

		if (isset($this->events[0]->{$key}))
		{
			return $this->events[0]->{$key};
		}

		return $def;
	}

	/**
	 * Returns the number of events contained within the list
	 *
	 * @return 	integer
	 */
	public function getEventsCount()
	{
		return count($this->events);
	}

	/**
	 * Extends the bounds of this box according to the delimiters of the given event.
	 *
	 * @param 	integer  $start  The starting delimiter.
	 * @param  	integer  $end 	 The ending delimiter.
	 * @param 	mixed 	 $event  The event(s) to push.
	 *
	 * @return 	self 	 This object to support chaining.
	 *
	 * @uses 	addEvent() 	  
	 */
	public function extendBounds($start, $end, $event)
	{
		$this->start = min(array($this->start, $start));
		$this->end 	 = max(array($this->end, $end));

		return $this->addEvent($event);
	}

	/**
	 * Checks if there is an intersection between the rect and the given delimiters.
	 *
	 * @param 	integer  $start  The starting delimiter.
	 * @param  	integer  $end 	 The ending delimiter.
	 *
	 * @return 	boolean  True if it is, false otherwise.
	 */
	public function intersects($start, $end)
	{
		return ($this->start <= $start && $start < $this->end) 	// second app. starts within first app. (or maybe are equals)
			|| ($start <= $this->start && $this->start < $end) 	// first app. starts within second app. (or maybe are equals)
			|| ($this->start < $start && $end < $this->end) 	// first app. contains second app.
			|| ($start < $this->start && $this->end < $end); 	// second app. contains first app.
	}

	/**
	 * Checks if the appointment starts at the specified hour.
	 * This method doesn't consider the minutes (e.g. 9:30 starts @ 9:00).
	 *
	 * @param 	integer  $hour  The hour to check.
	 *
	 * @return 	boolean  True if starts, false otherwise.
	 */
	public function startsAt($hour)
	{
		if ($this->startH() == $hour)
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks if the appointment ends at the specified hour.
	 * This method doesn't consider the minutes (e.g. 9:30 starts @ 9:00).
	 *
	 * @param 	integer  $hour  The hour to check.
	 *
	 * @return 	boolean  True if ends, false otherwise.
	 */
	public function endsAt($hour)
	{
		if ($this->endHM() > $hour * 60 && $this->endHM() <= ($hour + 1) * 60)
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks if the appointment is between the given hour and the next one.
	 * This method doesn't consider the minutes.
	 *
	 * @param 	integer  $hour  The hour to check.
	 *
	 * @return 	boolean  True if contained, false otherwise.
	 */
	public function containsAt($hour)
	{
		if ($this->startH() < $hour && $hour < $this->endH())
		{
			return true;
		}

		return false;
	}
}
