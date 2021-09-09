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
 * Helper class used to manipulate the items within the cart.
 *
 * @since 1.6
 */
abstract class VAPCartUtils
{
	/**
	 * Sorts the items by service and checkin date.
	 *
	 * @param 	array 	$items 	The items to sort.
	 *
	 * @return 	array 	The sorted items.
	 *
	 * @uses 	quicksort() 
	 */
	public static function sortItemsByServiceDate($items)
	{
		return self::quicksort($items);
	}
	
	/**
	 * Returns the total cost of all the appointments for the given service.
	 *
	 * @param 	array 	 $items  The items list.
	 * @param 	integer  $id 	 The service ID.
	 *
	 * @return 	float 	 The resulting total cost.
	 */
	public static function getServiceTotalCost($items, $id)
	{
		$price = 0;

		for ($i = 0; $i < count($items) && ($price == 0 || $items[$i]->getID() == $id); $i++)
		{
			if ($items[$i]->getID() == $id)
			{
				$price += $items[$i]->getTotalCost();
			} 			
		}
		
		return $price;
	}

	/**
	 * Categorizes the items array by service.
	 *
	 * @param 	array 	$items 	The items list.
	 *
	 * @return 	array 	The grouped items.
	 *
	 * @since 	1.6
	 */
	public static function groupItemsByService(array $items)
	{
		$groups = array();

		$last_id = -1;

		foreach ($items as $item)
		{
			if ($item->getID() != $last_id)
			{
				$groups[] = array(
					'id'	=> $item->getID(),
					'name' 	=> $item->getName(),
					'total' => self::getServiceTotalCost($items, $item->getID()),
					'items' => array(),
				);

				$last_id = $item->getID();
			}

			$groups[count($groups) - 1]['items'][] = $item;
		}

		return $groups;
	}

	/**
	 * Sorts the cart items using QuickSort method.
	 *
	 * @param 	array 	$items 	The items to sort.
	 *
	 * @return 	array 	The sorted items.
	 *
	 * @since 	1.6
	 */
	private static function quicksort($items)
	{
		usort($items, function($a, $b)
		{
			if ($a->getID() > $b->getID())
			{
				return 1;
			}
			else if ($a->getID() < $b->getID())
			{
				return -1;
			}

			if ($a->getCheckinTimeStamp() > $b->getCheckinTimeStamp())
			{
				return 1;
			}
			else if ($a->getCheckinTimeStamp() < $b->getCheckinTimeStamp())
			{
				return -1;
			}

			return 0;
		});

		return $items;
	}
	
	/**
	 * Sorts the cart items using BubbleSort method.
	 *
	 * @param 	array 	$items 	The items to sort.
	 *
	 * @return 	array 	The sorted items.
	 *
	 * @deprecated 	1.7  Use VAPCartUtils::quicksort() instead.
	 */
	private static function bubble_sort($items)
	{
		$size = count($items);
		
		for ($i = 0; $i < $size; $i++)
		{
			for ($j = 0; $j < $size - 1 - $i ; $j++)
			{
				if ($items[$j + 1]->getID() < $items[$j]->getID())
				{
					self::swap($items, $j, $j + 1);
				}
				else if ($items[$j + 1]->getID() == $items[$j]->getID() && $items[$j + 1]->getCheckinTimeStamp() < $items[$j]->getCheckinTimeStamp())
				{
					self::swap($items, $j, $j+1);
				}
			}
		}

		return $items;
	}
	
	/**
	 * Helper method used to swich the elements of an array.
	 *
	 * @param 	array 	 &$items  The items list.
	 * @param 	integer  $i 	  The first index.
	 * @param 	integer  $j 	  The second index.
	 *
	 * @return 	void
	 *
	 * @deprecated 	1.7  Use VAPCartUtils::quicksort() instead.
	 */
	private static function swap(&$items, $i, $j)
	{
		$app = $items[$i];
		$items[$i] = $items[$j];
		$items[$j] = $app;
	}
}

/**
 * Helper class used to manipulate the items within the cart.
 *
 * @since  		1.4
 * @deprecated 	1.7  Use VAPCartUtils instead.
 */
abstract class VikAppointmentsCartUtils extends VAPCartUtils
{

}
