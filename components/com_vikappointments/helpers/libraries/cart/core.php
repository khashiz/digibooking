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

UILoader::import('libraries.cart.cart');

/**
 * Helper class used to retrive the current cart instance.
 *
 * @since 		1.6
 * @deprecated 	1.7  Use VAPCart instead.
 */
class VAPCartCore
{	
	/**
	 * Gets the instance of the cart object stored within the PHP session.
	 * 
	 * @param 	array 		$properties 		The settings array.
	 * @param 	boolean 	$create_instance 	@deprecated never used.
	 *
	 * @return 	VAPCart 	The cart instance.
	 *
	 * @deprecated 	1.7 	Use VAPCart::getInstance() instead.
	 */
	public function getCartObject(array $properties = array(), $create_instance = true)
	{
		return VAPCart::getInstance(array(), $properties);
	}
	
	/**
	 * Stores the cart instance into the PHP session.
	 *
	 * @param 	VAPCart  $cart  The cart object to store.
	 *
	 * @return 	void
	 *
	 * @deprecated 	1.7  Use VAPCart::store() instead.
	 */
	public function storeCart($cart)
	{
		$cart->store();
	}
}

/**
 * Helper class used to retrive the current cart instance.
 *
 * @since  		1.4
 * @deprecated 	1.7  Use VAPCart instead.
 */
class VikAppointmentsCartCore extends VAPCartCore
{

}
