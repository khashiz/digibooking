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

UILoader::import('libraries.cart.option');

/**
 * Class used to handle the items that can be stored within a cart.
 *
 * @since 1.6
 */
class VAPCartItem
{
	/**
	 * The service identifier.
	 *
	 * @var integer
	 */
	private $id;

	/**
	 * The employee identifier (-1 if not specified).
	 *
	 * @var integer
	 */
	private $id2;

	/**
	 * The service name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The employee name (empty if not specified).
	 *
	 * @var string
	 */
	private $name2;

	/**
	 * The service price.
	 *
	 * @var float
	 */
	private $price;

	/**
	 * The service duration (in minutes).
	 *
	 * @var integer
	 */
	private $duration;

	/**
	 * The appointment checkin date and time (UNIX timestamp).
	 *
	 * @var integer
	 */
	private $checkin;

	/**
	 * The number of people.
	 *
	 * @var integer
	 */
	private $people;

	/**
	 * A string containing the appointment details.
	 *
	 * @var string
	 */
	private $details;

	/**
	 * A factor used to increase the duration by the given number.
	 * For example, if we have a factor equals to 3 and the service lasts 1 hour,
	 * the resulting duration will be equals to 3 hours (3 * 60 min).
	 *
	 * @var integer
	 */
	private $factor = 1;
	
	/**
	 * Flag used to check if the item is active or not.
	 *
	 * @var boolean
	 */
	private $status = true;

	/**
	 * Flag used to check if the item is discounted by a specific deal.
	 * For example, when the service is redeemed by using a package.
	 *
	 * @var boolean
	 */
	private $discounted = false;
	
	/**
	 * A list of selected options.
	 *
	 * @var VAPCartOption[]
	 */
	private $options = array();
	
	/**
	 * Class constructor.
	 *
	 * @param 	integer  $id 		The service ID.
	 * @param 	integer  $id2 		The employee ID.
	 * @param 	string 	 $name 		The service name.
	 * @param 	string 	 $name2 	The employee name.
	 * @param 	float 	 $price 	The service price.
	 * @param 	integer  $duration 	The service duration.
	 * @param 	integer  $checkin 	The appointment checkin timestamp.
	 * @param 	integer  $people 	The number of guests.
	 * @param 	string 	 $details 	The appointment details.
	 */
	public function __construct($id, $id2, $name, $name2, $price, $duration, $checkin, $people = 1, $details = '')
	{
		$this->id 		= $id;
		$this->id2 		= $id2;
		$this->name 	= $name;
		$this->name2 	= $name2;
		$this->price 	= $price;
		$this->duration = $duration;
		$this->checkin 	= $checkin;
		$this->people 	= $people;
		$this->details 	= $details;
	}
	
	/**
	 * Returns the service identifier.
	 *
	 * @return 	integer
	 */
	public function getID()
	{
		return $this->id;
	}
	
	/**
	 * Returns the employee identifier.
	 *
	 * @return 	integer
	 */
	public function getID2()
	{
		return $this->id2;
	}
	
	/**
	 * Returns the service name.
	 *
	 * @return 	string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Returns the employee name.
	 *
	 * @return 	string
	 */
	public function getName2()
	{
		return $this->name2;
	}
	
	/**
	 * Returns the service price.
	 * In case the service is discounted, the value will be 0.
	 *
	 * @return 	float
	 *
	 * @uses 	isDiscounted()
	 */
	public function getPrice()
	{
		return $this->isDiscounted() ? 0.0 : floatval($this->price);
	}
	
	/**
	 * Returns the total cost of the service.
	 *
	 * @return 	float
	 *
	 * @uses 	getPrice()
	 */
	public function getTotalCost()
	{
		$tprice = $this->getPrice();

		foreach ($this->options as $o)
		{
			$tprice += $o->getPrice() * $o->getQuantity();
		}

		return $tprice;
	}
	
	/**
	 * Returns the service base duration.
	 *
	 * @return 	integer
	 */
	public function getDuration()
	{
		return $this->duration;
	}
	
	/**
	 * Returns the checkin timestamp.
	 *
	 * @return 	integer
	 */
	public function getCheckinTimeStamp()
	{
		return $this->checkin;
	}
	
	/**
	 * Returns the formatted checkin.
	 *
	 * @param 	string 	$format  The date format.
	 *
	 * @return 	string
	 */
	public function getCheckinDate($format)
	{
		return ArasJoomlaVikApp::jdate($format, $this->checkin);
	}
	
	/**
	 * Returns the number of guests.
	 *
	 * @return 	integer
	 */
	public function getPeople()
	{
		return $this->people;
	}
	
	/**
	 * Returns the appointment details.
	 *
	 * @return 	string
	 */
	public function getDetails()
	{
		return $this->details;
	}

	/**
	 * Checks if the service is discounted.
	 *
	 * @return 	boolean
	 */
	public function isDiscounted()
	{
		return $this->discounted;
	}

	/**
	 * Marks the service as discounted or not.
	 *
	 * @param 	mixed 	$s 	True if discounted, false otherwise.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function setDiscounted($s)
	{
		$this->discounted = (bool) $s;

		return $this;
	}

	/**
	 * Returns the duration factor of the service.
	 *
	 * @return 	integer
	 */
	public function getFactor()
	{
		return $this->factor;
	}

	/**
	 * Sets the duration factor.
	 *
	 * @param 	integer  $f  The factor.
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function setFactor($f)
	{
		$this->factor = (int) $f;

		return $this;
	}
	
	/**
	 * Checks if this item is active or not.
	 *
	 * @return 	boolean
	 */
	public function isActive()
	{
		return $this->status;
	}
	
	/**
	 * Marks this item as active.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function active()
	{
		$this->status = true;

		return $this;
	}
	
	/**
	 * Marks this item as unactive.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function remove()
	{
		$this->status = false;

		return $this;
	}
	
	/**
	 * Adds a new option within the list.
	 *
	 * @param 	VAPCartOption 	$opt  The option to add.	
	 *
	 * @return 	self 			This object to support chaining.
	 *
	 * @uses 	indexOf()
	 * @uses 	getFirstAvailableIndex()
	 */
	public function addOption(VAPCartOption $opt)
	{
		$index = $this->indexOf($opt->getID());

		if ($index != -1)
		{
			$this->options[$index]->add($opt->getQuantity());
		}
		else
		{
			$this->options[$this->getFirstAvailableIndex()] = $opt;
		}

		return $this;
	}
	
	/**
	 * Removes the specified option.
	 *
	 * @param 	integer  $id 	The option ID.
	 * @param 	integer  $unit  The number of units to remove.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 *
	 * @uses 	indexOf()
	 * @uses 	isEmpty()
	 * @uses 	emptyOptions()
	 */
	public function removeOption($id, $unit = 1) 
	{
		$index = $this->indexOf($id);

		if ($index != -1)
		{
			$res = $this->options[$index]->remove($unit);
			
			if ($this->isEmpty())
			{
				$this->emptyOptions();
			}

			return $res;
		}
		
		return false;
	}
	
	/**
	 * Returns the position within the list of the specified option.
	 *
	 * @param 	integer  $id 	The option ID.
	 *
	 * @return 	integer  The option index if exists, -1 otherwise.
	 *
	 * @uses 	getOptionsLength()
	 */
	public function indexOf($id)
	{
		$opt_len = $this->getOptionsLength();

		for ($i = 0; $i < $opt_len; $i++)
		{
			if ($this->options[$i]->getID() == $id)
			{
				return $i;
			}
		}

		return -1;
	}
	
	/**
	 * Returns the option at the specified index.
	 *
	 * @param 	integer  $index  The option index.
	 *
	 * @return 	mixed 	 The option if exists, false otherwise.
	 *
	 * @uses 	getoptionsLength()
	 */
	public function getOptionAt($index)
	{
		if ($index >= 0 && $index < $this->getOptionsLength())
		{
			return $this->options[$index];
		}
		
		return null;
	}
	
	/**
	 * Returns the number of options within the list.
	 * The list may contain also options that are no more
	 * active (quantity less than 1).
	 * 
	 * @return 	integer
	 */
	public function getOptionsLength()
	{
		return count($this->options);
	}
	
	/**
	 * Empties the options list.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function emptyOptions()
	{
		$this->options = array();

		return $this;
	}
	
	/**
	 * Checks if there are no options within the list.
	 *
	 * @return 	boolean
	 */
	public function isEmpty()
	{
		if (!count($this->options))
		{
			return true;
		}
		
		foreach ($this->options as $o)
		{
			if ($o->getQuantity() > 0)
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Balances the item in order to remove all the options
	 * with quantity lower than 1.
	 *
	 * @return 	self 	This object to support chaining.
	 *
	 * @uses 	emptyOptions()
	 */
	public function balance()
	{
		$app = $this->options;
		$this->emptyOptions();
		
		foreach ($app as $opt)
		{
			if ($opt->getQuantity() > 0)
			{
				$this->options[] = $opt;
			}
		}

		return $this;
	}
	
	/**
	 * Returns a list containing all the active options.
	 *
	 * @return 	array
	 */
	public function getOptionsList()
	{
		$options = array();

		foreach ($this->options as $opt)
		{
			if ($opt->getQuantity())
			{
				$options[] = $opt;
			}
		}

		return $options;
	}
	
	/**
	 * Returns the first available index to push a new option.
	 * Used to replace a unactive option with a new one.
	 *
	 * @return 	integer
	 *
	 * @uses 	getOptionsLength()
	 */
	protected function getFirstAvailableIndex()
	{
		$opt_len = $this->getOptionsLength();

		for ($i = 0; $i < $opt_len; $i++)
		{
			if ($this->options[$i]->getQuantity() == 0)
			{
				return $i;
			}
		}
		
		return $opt_len;
	}

	/**
	 * Returns a string representation of this instance.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.7  Use __tostring() magic method directly.
	 */
	public function toString()
	{
		return (string) $this;
	}
	
	/**
	 * Magic method used to return a string representation of this instance.
	 *
	 * @return 	string
	 */
	public function __tostring()
	{
		return '<pre>' . print_r($this, true) . '</pre>';
	}
	
	/**
	 * Returns an array containing the details of this instance.
	 *
	 * @return 	array
	 */
	public function toArray()
	{
		$arr = array(
			'id' 		=> $this->getID(),
			'id2' 		=> $this->getID2(),
			'name' 		=> $this->getName(),
			'name2' 	=> $this->getName2(),
			'price' 	=> $this->getPrice(),
			'totalcost' => $this->getTotalCost(),
			'duration' 	=> $this->getDuration(),
			'checkin' 	=> $this->getCheckinTimeStamp(),
			'people' 	=> $this->getPeople(),
			'details' 	=> $this->getDetails(),
			'options' 	=> array(),
		);
		
		foreach ($this->options as $o)
		{
			if ($o->getQuantity() > 0)
			{
				$arr['options'][] = $o->toArray();
			}
		}
		
		return $arr;
	}
}

/**
 * Class used to handle the items that can be stored within a cart.
 *
 * @since  		1.4
 * @deprecated 	1.7  Use VAPCartItem instead.
 */
class VikAppointmentsItem extends VAPCartItem
{

}
