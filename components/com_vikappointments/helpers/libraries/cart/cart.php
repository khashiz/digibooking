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

UILoader::import('libraries.cart.item');

/**
 * Class used to handle a cart to book the appointments.
 *
 * @since 1.6
 */
class VAPCart
{
	/**
	 * The instance of the Cart.
	 * There should be only one cart instance for the whole session.
	 *
	 * @var VAPCart
	 *
	 * @since 1.6
	 */
	protected static $instance = null;

	/**
	 * The list containing the selected items.
	 *
	 * @var VAPCartItem[]
	 */
	private $cart = array();
	
	/**
	 * The configuration array.
	 *
	 * @var array
	 */
	private $params = array(
		'append' 	=> true,
		'maxsize' 	=> self::UNLIMITED,
		'allowsync' => true,
	);

	/**
	 * Returns the instance of the cart object, only creating it
	 * if doesn't exist yet.
	 * 
	 * @param 	array 	$cart 	 The array containing all the items to push.
	 * @param 	array 	$params  The settings array.
	 *
	 * @return 	self 	A new instance.
	 *
	 * @since 	1.6
	 */
	public static function getInstance(array $cart = array(), array $params = array())
	{
		if (static::$instance === null)
		{
			// get cart from session
			$session_cart = JFactory::getSession()->get(self::CART_SESSION_KEY, null);

			if (empty($session_cart))
			{
				$cart = new static($cart, $params);
			}
			else
			{
				$cart = unserialize($session_cart);
			}

			static::$instance = $cart;
		}

		// always overwrite existing params
		static::$instance->setParams($params);

		return static::$instance;
	}
	
	/**
	 * Class constructor.
	 *
	 * @param 	array 	$cart 	 The array containing all the items to push.
	 * @param 	array 	$params  The settings array.
	 *
	 * @uses 	setParams()
	 */
	public function __construct(array $cart = array(), array $params = array())
	{
		$this->cart = $cart;
		$this->setParams($params);
	}

	/**
	 * Store this instance into the PHP session.
	 *
	 * @return 	self 	This object to support chaining.
	 *
	 * @since 	1.6
	 */
	public function store()
	{
		JFactory::getSession()->set(self::CART_SESSION_KEY, serialize($this));

		return $this;
	}
	
	/**
	 * Sets the configuration of the cart.
	 * The configuration values are overwritten only
	 * if the new settings are not empty.
	 *
	 * @param 	array 	$params  The settings array.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function setParams(array $params = array())
	{
		foreach ($params as $k => $v)
		{
			if (array_key_exists($k, $this->params))
			{
				$this->params[$k] = $v;
			}
		}

		return $this;
	}
	
	/**
	 * @deprecated 	1.7  Use setParams() instead.
	 */
	public function setAppendValue($b)
	{
		$this->params['append'] = $b;

		return $this;
	}
	
	/**
	 * @deprecated 	1.7  Use setParams() instead.
	 */
	public function setMaxSize($max_size)
	{
		$this->params['maxsize'] = $max_size;

		return $this;
	}

	/**
	 * @deprecated 	1.7  Use setParams() instead.
	 */
	public function setAllowAppointmentsSameTime($s)
	{
		$this->params['allowsync'] = $s;

		return $this;
	}
	
	/**
	 * Empties the items within the cart.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function emptyCart()
	{
		$this->cart = array();

		return $this;
	}
	
	/**
	 * Checks if the cart is empty.
	 *
	 * @return 	boolean  True if empty, false otherwise.
	 */
	public function isEmpty()
	{
		if (!count($this->cart))
		{
			return true;
		}
		
		foreach ($this->cart as $i)
		{
			if ($i->isActive())
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Balances the cart in order to empty the free slots
	 * created after removing one or more items.
	 *
	 * @return 	self 	This object to support chaining.
	 *
	 * @uses 	emptyCart()
	 */
	public function balance()
	{
		$app = $this->cart;
		$this->emptyCart();
		
		foreach ($app as $item)
		{
			if ($item->isActive())
			{
				$item->balance();
				$this->cart[] = $item;
			}
		}

		return $this;
	}
	
	/**
	 * Pushes a new item within the cart.
	 * This method checks if the item can be added as the cart
	 * may own an internal size limit.
	 *
	 * @param 	VAPCartItem  $item 	The item to push.
	 * 
	 * @return 	boolean 	 True on success, false otherwise.
	 *
	 * @uses 	getCartRealLength()
	 * @uses 	indexOf()
	 * @uses 	emptyCart()
	 * @uses 	getFirstAvailableIndex()
	 */
	public function addItem(VAPCartItem $item)
	{	
		if ($this->params['maxsize'] == -1 || $this->getCartRealLength() < $this->params['maxsize'] || !$this->params['append'])
		{	
			$index = $this->indexOf($item->getID(), $item->getID2(), $item->getCheckinTimeStamp(), $item->getDuration());
		
			if ($index == -1 || !$this->params['append'])
			{	
				if (!$this->params['append'])
				{
					$this->emptyCart();
				}
			
				$this->cart[$this->getFirstAvailableIndex()] = $item;
			
				return true;	
			}
			else if (!$this->cart[$index]->isActive())
			{
				$this->cart[$index]->active();
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Removes the item from the cart considering the specified arguments.
	 *
	 * @param 	integer  $id 		The service ID.
	 * @param 	integer  $id2 		The employee ID.
	 * @param 	integer  $checkin 	The checkin timestamp.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 *
	 * @uses 	indexOf()
	 * @uses 	isEmpty()
	 * @uses 	emptyCart()
	 */
	public function removeItem($id, $id2, $checkin)
	{
		// reset allow sync to retrieve the correct item
		$app = $this->params['allowsync'];
		$this->params['allowsync'] = true;

		$index = $this->indexOf($id, $id2, $checkin);
		$this->params['allowsync'] = $app;

		if ($index != -1)
		{
			$this->cart[$index]->remove();

			if ($this->isEmpty())
			{
				$this->emptyCart();
			}

			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the index of the item that matches the specified arguments.
	 *
	 * @param 	integer  $id 		The service ID.
	 * @param 	integer  $id2 		The employee ID.
	 * @param 	integer  $checkin 	The checkin timestamp.
	 * @param 	integer  $duration 	The duration used to calculate the ending delimiter
	 * 								to check if there is an intersection.
	 *
	 * @return 	integer  The item index on success, -1 on failure.
	 *
	 * @uses 	getCartLength()
	 * @uses 	bounds()
	 */
	public function indexOf($id, $id2, $checkin, $duration = 0)
	{
		$cart_len = $this->getCartLength();

		for ($i = 0; $i < $cart_len; $i++)
		{
			if ($this->cart[$i]->isActive())
			{
				if ($this->params['allowsync'])
				{
					if ($this->cart[$i]->getID() == $id && $this->cart[$i]->getID2() == $id2 && $this->cart[$i]->getCheckinTimeStamp() == $checkin)
					{
						return $i;
					}
				}
				else
				{
					if ($this->bounds($this->cart[$i]->getCheckinTimeStamp(), $this->cart[$i]->getDuration() * 60, $checkin, $duration * 60))
					{
						return $i;
					}
				}
			}
		}
		
		return -1;
	}

	/**
	 * Checks if there is an intersection between the specified delimiters.
	 *
	 * @param 	integer  $x1  The first initial delimiter.
	 * @param 	integer  $y1  The first ending delimiter.
	 * @param 	integer  $x2  The second initial delimiter.
	 * @param 	integer  $x2  The second ending delimiter.
	 *
	 * @return 	boolean  True if they intersect, false otherwise.
	 */
	private function bounds($x1, $y1, $x2, $y2)
	{
		return ($x1 == $x2)
			|| ($x1 + $y1 == $x2 + $y2)
			|| ($x1 + $y1 > $x2 && $x1 + $y1 <= $x2 + $y2)
			|| ($x2 + $y2 > $x1 && $x2 + $y2 <= $x1 + $y1)
			|| ($x1 <= $x2 && $x2 + $y2 <= $x1 + $y1)
			|| ($x2 <= $x1 && $x1 + $y1 <= $x2 + $y2);
	}
	
	/**
	 * Returns the total cost of the cart.
	 *
	 * @return 	float
	 */
	public function getTotalCost()
	{
		$tcost = 0;

		foreach ($this->cart as $i)
		{
			if ($i->isActive())
			{
				$tcost += $i->getTotalCost();
			}
		}
		
		return $tcost;
	}
	
	/**
	 * Returns the item at the specified position.
	 *
	 * @param 	integer  $index
	 *
	 * @return 	mixed 	 The item if exists, null otherwise.
	 *
	 * @uses 	getCartLength()
	 */
	public function getItemAt($index)
	{
		if ($index >= 0 && $index < $this->getCartLength())
		{
			return $this->cart[$index];
		}
		
		return null;
	}
	
	/**
	 * Returns the number of items within the cart.
	 * The list may contain also items that are no more
	 * active.
	 *
	 * @return 	integer
	 */
	public function getCartLength()
	{
		return count($this->cart);
	}
	
	/**
	 * Returns the number of active items within the list.
	 *
	 * @return 	integer
	 */
	public function getCartRealLength()
	{
		$cont = 0;

		foreach ($this->cart as $i)
		{
			if ($i->isActive())
			{
				$cont++;
			}
		}

		return $cont;
	}
	
	/**
	 * Returns a list containing all the active items.
	 *
	 * @return 	array
	 */
	public function getItemsList()
	{
		$list = array();

		foreach ($this->cart as $i)
		{
			if ($i->isActive())
			{
				$list[] = $i;
			}
		}
		
		return $list;
	}
	
	/**
	 * Returns the first available index to push a new item.
	 * Used to replace a unactive item with a new one.
	 *
	 * @return 	integer
	 *
	 * @uses 	getCartLength()
	 */
	protected function getFirstAvailableIndex()
	{
		$cart_len = $this->getCartLength();

		for ($i = 0; $i < $cart_len; $i++)
		{
			if (!$this->cart[$i]->isActive())
			{
				return $i;
			}
		}
		
		return $cart_len;
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
		return '<pre>' . print_r($this, true) . '</pre><br />Total Cost = ' . $this->getTotalCost();
	}
	
	/**
	 * Identifier used to make the size of the cart unlimited.
	 *
	 * @var integer
	 */
	const UNLIMITED = -1;
	
	/**
	 * Setting name used to check if the cart is enabled or not.
	 * In case the cart is disabled, before pushing a new item, the list
	 * will be always emptied.
	 *
	 * @var string
	 */
	const CART_ENABLED = 'append';

	/**
	 * Setting name used to retrieve the maximum number of items
	 * that can be added within the list.
	 *
	 * @var string
	 */
	const MAX_SIZE = 'maxsize';

	/**
	 * Setting name used to check if the cart can contain more than
	 * one appointment at the same date and time.
	 *
	 * @var string
	 */
	const ALLOW_SYNC = 'allowsync';

	/**
	 * CART_SESSION_KEY identifier for session key.
	 *
	 * @var string
	 *
	 * @since 1.6
	 */
	const CART_SESSION_KEY = 'vapcartdev';
}

/**
 * Class used to handle a cart to book the appointments.
 *
 * @since  		1.4
 * @deprecated 	1.7  Use VAPCart instead.
 */
class VikAppointmentsCart extends VAPCart
{

}
