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
 * Class used to handle the options that can be attached to the cart items.
 *
 * @since 1.6
 */
class VAPCartOption
{
	/**
	 * The option identifier.
	 *
	 * @var integer
	 */
	private $id;

	/**
	 * The variation identifier.
	 *
	 * @var integer
	 */
	private $id_variation;

	/**
	 * The option name. If the variation is set, the name is built as:
	 * [OPTION_NAME] - [VARIATION_NAME]
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The option price plus the variation price (if any).
	 *
	 * @var float
	 */
	private $price;

	/**
	 * The selected quantity of the option.
	 *
	 * @var integer
	 */
	private $quantity;

	/**
	 * The maximum number of units that can be selected.
	 *
	 * @var integer
	 */
    private $maxQuantity;

    /**
	 * Flag used to check if the option is required (mandatory selection) or not.
	 *
	 * @var boolean
	 */
    private $required;
	
	/**
	 * Class constructor.
	 *
	 * @param 	integer  $id 		The option ID.
	 * @param 	integer  $var 		The variation ID (-1 if not specified).
	 * @param 	string 	 $name 		The option name (variation included).
	 * @param 	float 	 $price 	The option price (variation included).
	 * @param 	integer  $maxq 		The maximum quantity.
	 * @param 	boolean  $required 	True if required, false otherwise.
	 * @param 	integer  $quantity 	The selected units.
	 */
	public function __construct($id, $var, $name, $price, $maxq, $required, $quantity = 1)
	{
		$this->id 			= $id;
		$this->id_variation = $var;
		$this->name 		= $name;
		$this->price 		= $price;
		$this->quantity 	= $quantity;
        $this->maxQuantity 	= $maxq;
        $this->required 	= $required;
	}
	
	/**
	 * Returns the option identifier.
	 *
	 * @return 	integer
	 */
	public function getID()
	{
		return $this->id;
	}

	/**
	 * Returns the option variation identifier.
	 * If not set, returns -1.
	 *
	 * @return 	integer
	 */
	public function getVariationID()
	{
		return $this->id_variation > 0 ? $this->id_variation : -1;
	}
	
	/**
	 * Returns the option and variation name.
	 *
	 * @return 	string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Returns the option price.
	 *
	 * @return 	float
	 */
	public function getPrice()
	{
		return floatval($this->price);
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
	 * Returns the maximum number of units that can be selected.
	 *
	 * @return 	integer
	 */
    public function getMaxQuantity()
    {
        return $this->maxQuantity;
    }
    
    /**
	 * Checks if the option is required.
	 *
	 * @return 	boolean
	 */
    public function isRequired()
    {
        return $this->required;
    }
	
	/**
	 * Decreases the number of selected units by the specified amount.
	 *
	 * @param 	integer  $unit 	The number of units to remove (1 by default).
	 *
	 * @return 	integer  The remaining quantity.
	 */
	public function remove($unit = 1)
	{
	    if ($this->quantity == 1 && $this->required)
	    {
	        return $this->quantity;
	    }
        
		$this->quantity -= $unit;

		if ($this->quantity < 0)
		{
			$this->quantity = 0;
		}

		return $this->quantity;
	}
	
	/**
	 * Increases the number of selected units by the specified amount.
	 *
	 * @param 	integer  $unit 	The number of units to add (1 by default).
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function add($unit = 1)
	{
		$this->quantity += $unit;

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
		return 'ID = ' . $this->id . '<br />' .
			'Variation ID = '. $this->id_variation . '<br />' .
			'Name = ' . $this->name . '<br />' .
			'Price = ' . $this->price . '<br />' .
			'Quantity = ' . $this->quantity;
	}
	
	/**
	 * Returns an array containing the details of this instance.
	 *
	 * @return 	array
	 */
	public function toArray()
	{
		return array(
			'id' 			=> $this->getID(),
			'id_variation' 	=> $this->getVariationID(),
			'name' 			=> $this->getName(),
			'price' 		=> $this->getPrice(),
			'quantity' 		=> $this->getQuantity(),
		);
	}
}

/**
 * Class used to handle the options that can be attached to the cart items.
 *
 * @since  		1.4
 * @deprecated 	1.7  Use VAPCartOption instead.
 */
class VikAppointmentsOption extends VAPCartOption
{

}
