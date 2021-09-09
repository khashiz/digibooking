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
 * Class used to handle the packages that can be stored within a cart.
 *
 * @since 1.6
 */
class VAPCartPackagesItem
{
	/**
	 * The package identifier.
	 *
	 * @var integer
	 */
	private $id;

	/**
	 * The package name.
	 *
	 * @var string
	 */	
	private $name;

	/**
	 * The package cost.
	 *
	 * @var float
	 */
	private $price;

	/**
	 * The number of appointments that can be redeemed with
	 * a single unit of this package.
	 *
	 * @var integer
	 */
	private $num_app;

	/**
	 * The number of selected units.
	 *
	 * @var integer
	 */
	private $quantity;
	
	/**
	 * Class constructor.
	 *
	 * @param 	integer  $id 		The package ID.
	 * @param 	string 	 $name 		The package name.
	 * @param 	float 	 $price 	The package cost.
	 * @param 	integer  $num_app 	The appointments to redeem.
	 * @param 	integer  $quantity  The number of units.
	 */
	public function __construct($id, $name, $price, $num_app, $quantity = 1)
	{
		$this->id 		= $id;
		$this->name 	= $name;
		$this->price 	= $price;
		$this->num_app 	= $num_app;
		$this->quantity = $quantity;
	}
	
	/**
	 * Returns the package identifier.
	 *
	 * @return 	integer
	 */
	public function getID()
	{
		return $this->id;
	}
	
	/**
	 * Returns the package name.
	 *
	 * @return 	string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Returns the package base price.
	 *
	 * @return 	float
	 */
	public function getPrice()
	{
		return floatval($this->price);
	}

	/**
	 * Returns the package total cost.
	 *
	 * @return 	integer
	 *
	 * @uses 	getPrice()
	 * @uses 	getQuantity()
	 */
	public function getTotalCost()
	{
		return $this->getPrice() * $this->getQuantity();
	}
	
	/**
	 * Returns the number of appointments that can be redeemed
	 * with a single unit of this package.
	 *
	 * @return 	integer
	 */
	public function getNumberAppointments() 
	{
		return $this->num_app;
	}
	
	/**
	 * Returns the number of selected units.
	 *
	 * @return 	integer
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}
	
	/**
	 * Checks if the package is active or not.
	 *
	 * @return 	boolean
	 */
	public function isActive()
	{
		return $this->quantity > 0;
	}
	
	/**
	 * Marks the package as active.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function active()
	{
		$this->quantity = 1;

		return $this;
	}

	/**
	 * Marks the package as unactive.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function remove()
	{
		$this->quantity = 0;

		return $this;
	}

	/**
	 * Increases the quantity of this package by the specified units.
	 *
	 * @param 	integer  $units  The units to add.
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function addQuantity($units = 1)
	{
		if ($this->quantity < 0)
		{
			$this->quantity = 0;
		}

		$this->quantity += $units;

		return $this;
	}

	/**
	 * Decreases the quantity of this package by the specified units.
	 *
	 * @param 	integer  $units  The units to remove.
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function removeQuantity($units = 1)
	{
		$this->quantity -= $units;

		if ($this->quantity < 0)
		{
			$this->quantity = 0;
		}

		return $this;
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
			'name' 		=> $this->getName(),
			'price' 	=> $this->getPrice(),
			'num_app' 	=> $this->getNumberAppointments(),
			'quantity' 	=> $this->getQuantity(),
		);
		
		return $arr;
	}
}

/**
 * Class used to handle the packages that can be stored within a cart.
 *
 * @since  		1.5
 * @deprecated 	1.7  Use VAPCartPackagesItem instead.
 */
class VikAppointmentsPackage extends VAPCartPackagesItem
{

}
