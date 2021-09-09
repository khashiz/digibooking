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

UILoader::import('libraries.cartpack.item');

/**
 * Class used to handle a cart to book the packages.
 *
 * @since 1.6
 */
class VAPCartPackages
{
	/**
	 * The instance of the Cart.
	 * There should be only one cart instance for the whole session.
	 *
	 * @var VAPCartPackages
	 *
	 * @since 1.6
	 */
	protected static $instance = null;

	/**
	 * The list containing the selected items.
	 *
	 * @var VAPCartPackagesItem[]
	 */
	private $cart = array();
	
	/**
	 * The configuration array.
	 *
	 * @var array
	 */
	private $params = array(
		self::MAX_SIZE => self::UNLIMITED,	
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
	public function setMaxSize($max_size)
	{
		$this->params[self::MAX_SIZE] = $max_size;

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
		
		foreach ($this->cart as $p)
		{
			if ($p->isActive())
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
		
		foreach ($app as $pack)
		{
			if ($pack->isActive())
			{
				$this->cart[] = $pack;
			}
		}

		return $this;
	}
	
	/**
	 * Pushes a new package within the cart.
	 * This method checks if the item can be added as the cart
	 * may own an internal size limit.
	 *
	 * @param 	VAPCartPackagesItem  $pack 	The package to push.
	 * 
	 * @return 	boolean  True on success, false otherwise.
	 *
	 * @uses 	getPackagesInCart()
	 * @uses 	indexOf()
	 * @uses 	getFirstAvailableIndex()
	 */
	public function addPackage(VAPCartPackagesItem $pack)
	{	
		if ($this->params[self::MAX_SIZE] == self::UNLIMITED || $this->getPackagesInCart() < $this->params[self::MAX_SIZE])
		{	
			$index = $this->indexOf($pack->getID());
		
			if ($index == -1)
			{
				$this->cart[$this->getFirstAvailableIndex()] = $pack;
			
				return true;
			}
			else
			{
				if (!$this->cart[$index]->isActive())
				{
					$this->cart[$index]->active();
				}
				else
				{
					$this->cart[$index]->addQuantity();
				}

				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Removes the specified package.
	 *
	 * @param 	integer  $id 	 The package ID.
	 * @param 	integer  $units  The number of units to remove.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 *
	 * @uses 	indexOf()
	 * @uses 	isEmpty()
	 * @uses 	emptyCart()
	 */
	public function removePackage($id, $units = 1)
	{
		$index = $this->indexOf($id);

		if ($index != -1)
		{
			$this->cart[$index]->removeQuantity($units);
			
			if ($this->isEmpty())
			{
				$this->emptyCart();
			}

			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the position within the list of the specified package.
	 *
	 * @param 	integer  $id 	The package ID.
	 *
	 * @return 	integer  The package index if exists, -1 otherwise.
	 *
	 * @uses 	getCartLength()
	 */
	public function indexOf($id)
	{
		$cart_len = $this->getCartLength();

		for ($i = 0; $i < $cart_len; $i++)
		{
			if ($this->cart[$i]->getID() == $id && $this->cart[$i]->isActive())
			{
				return $i;
			}
		}
		
		return -1;
	}
	
	/**
	 * Returns the total cost of the cart.
	 *
	 * @return 	float
	 */
	public function getTotalCost()
	{
		$tcost = 0;

		foreach ($this->cart as $p)
		{
			if ($p->isActive())
			{
				$tcost += $p->getTotalCost();
			}
		}
		
		return $tcost;
	}
	
	/**
	 * Returns the package at the specified position.
	 *
	 * @param 	integer  $index
	 *
	 * @return 	mixed 	 The package if exists, null otherwise.
	 *
	 * @uses 	getCartLength()
	 */
	public function getPackageAt($index)
	{
		if ($index >= 0 && $index < $this->getCartLength())
		{
			return $this->cart[$index];
		}
		
		return null;
	}
	
	/**
	 * Returns the number of packages within the cart.
	 * The list may contain also packages that are no more
	 * active.
	 *
	 * @return 	integer
	 */
	public function getCartLength()
	{
		return count($this->cart);
	}
	
	/**
	 * Returns the number of active packages within the list.
	 *
	 * @return 	integer
	 */
	public function getPackagesInCart()
	{
		$cont = 0;

		foreach ($this->cart as $p)
		{
			if ($p->isActive())
			{
				$cont += $p->getQuantity();
			}
		}

		return $cont;
	}
	
	/**
	 * Returns a list containing all the active packages.
	 *
	 * @return 	array
	 */
	public function getPackagesList()
	{
		$list = array();

		foreach ($this->cart as $p)
		{
			if ($p->isActive())
			{
				$list[] = $p;
			}
		}
		
		return $list;
	}
	
	/**
	 * Returns the first available index to push a new package.
	 * Used to replace a unactive package with a new one.
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
	 * Setting name used to retrieve the maximum number of items
	 * that can be added within the list.
	 *
	 * @var string
	 */
	const MAX_SIZE = "maxsize";
	
	/**
	 * CART_SESSION_KEY identifier for session key.
	 *
	 * @var string
	 *
	 * @since 1.6
	 */
	const CART_SESSION_KEY = 'vapcartpackdev';
}

/**
 * Class used to handle a cart to book the packages.
 *
 * @since  		1.5
 * @deprecated 	1.7  Use VAPCartPackages instead.
 */
class VikAppointmentsCartPackages extends VAPCartPackages
{

}
