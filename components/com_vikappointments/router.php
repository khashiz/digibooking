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
defined('_JEXEC') or die('Restricted Access');

// require autoloader
require_once implode(DIRECTORY_SEPARATOR, array(JPATH_SITE, 'components', 'com_vikappointments', 'helpers', 'libraries', 'autoload.php'));

/**
 * Routing class for com_vikappointments
 *
 * @since  1.4
 */
class VikAppointmentsRouter
{
	/**
	 * Application object to use in the router.
	 *
	 * @var    JApplicationCms
	 * @since  1.6
	 */
	protected $app;

	/**
	 * Menu object to use in the router.
	 *
	 * @var    JMenu
	 * @since  1.6
	 */
	protected $menu;

	/**
	 * The current language tag.
	 *
	 * @var string
	 */
	protected $langtag;

	/**
	 * Class constructor.
	 *
	 * @param   JApplicationCms  $app   Application-object that the router should use.
	 * @param   JMenu            $menu  Menu-object that the router should use.
	 *
	 * @since   1.6
	 */
	public function __construct($app = null, $menu = null)
	{
		if ($app)
		{
			$this->app = $app;
		}
		else
		{
			$this->app = JFactory::getApplication('site');
		}

		if ($menu)
		{
			$this->menu = $menu;
		}
		else
		{
			$this->menu = $this->app->getMenu();
		}

		$this->langtag = JFactory::getLanguage()->getTag();

		UILoader::import('libraries.helpers.alias');
	}

	/**
	 * @override
	 * This method is meant to transform the query parameters into a more human
	 * readable form. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		$active = $this->menu->getActive();
		
		$segments = array();

		if (!isset($query['view']))
		{
			// view not set, ignore routing
			return $segments;
		}

		// services list

		if ($query['view'] == 'serviceslist' && isset($query['service_group']))
		{
			// try to obtain the proper Itemid that belong to the serviceslist view with the given group
			$itemid = $this->getProperItemID('serviceslist', array('service_group' => $query['service_group']));

			if ($itemid)
			{
				// overwrite the Itemid set in the query in order
				// to rewrite the base URI
				$query['Itemid'] = $itemid;

				unset($query['service_group']);
			}
		}

		// employees list

		else if ($query['view'] == 'employeeslist' && isset($query['employee_group']))
		{
			// try to obtain the proper Itemid that belong to the employeeslist view with the given group
			$itemid = $this->getProperItemID('employeeslist', array('employee_group' => $query['employee_group']));

			if ($itemid)
			{
				// overwrite the Itemid set in the query in order
				// to rewrite the base URI
				$query['Itemid'] = $itemid;

				unset($query['employee_group']);
			}
		}

		// service details

		else if ($query['view'] == 'servicesearch')
		{
			// build URL for service details
			if (isset($query['id_ser']))
			{
				// arguments used to check if the active menu item
				// matches the values set in query string
				$args = array(
					'view' 		=> 'servicesearch',
					'id_ser' 	=> $query['id_ser'],
				);

				/**
				 * Make sure the ID of the service is not set within the query of the menu item.
				 * This because the link may be a self redirect, causing duplicated aliases.
				 * For example, if we have something like:
				 * /services/service-name/
				 * we need to avoid pushing the alias of the service.
				 */
				if (!$this->matchItemArguments($active, $args))
				{
					// service ID was set
					$alias = AliasHelper::getRecordAlias($query['id_ser'], 'service', $this->langtag);

					if ($alias)
					{
						// alias found, push it within the segments array
						$segments[] = $alias;
					}

					// get parent group
					$id_group = AliasHelper::getRecordColumn($query['id_ser'], 'id_group', 'service', $this->langtag);

					// try to obtain the proper Itemid that belong to the serviceslist view with the given group
					$itemid = $this->getProperItemID('serviceslist', array('service_group' => $id_group));

					if (!$itemid)
					{
						// fallback to obtain the proper Itemid that belong to the serviceslist view
						$itemid = $this->getProperItemID('serviceslist');
					}

					if ($itemid)
					{
						// overwrite the Itemid set in the query in order
						// to rewrite the base URI
						$query['Itemid'] = $itemid;
					}
				}

				// unset service ID from query
				unset($query['id_ser']);
			}
		}

		// employee details

		else if ($query['view'] == 'employeesearch')
		{
			// build URL for employee details
			if (isset($query['id_employee']))
			{
				// arguments used to check if the active menu item
				// matches the values set in query string
				$args = array(
					'view' 		  => 'employeesearch',
					'id_employee' => $query['id_employee'],
				);

				/**
				 * Make sure the ID of the employee is not set within the query of the menu item.
				 * This because the link may be a self redirect, causing duplicated aliases.
				 * For example, if we have something like:
				 * /employees/employee-name/
				 * we need to avoid pushing the alias of the employee.
				 */
				if (!$this->matchItemArguments($active, $args))
				{
					// employee ID was set
					$alias = AliasHelper::getRecordAlias($query['id_employee'], 'employee', $this->langtag);

					if ($alias)
					{
						// alias found, push it within the segments array
						$segments[] = $alias;
					}

					// get parent group
					$id_group = AliasHelper::getRecordColumn($query['id_employee'], 'id_group', 'employee', $this->langtag);

					// try to obtain the proper Itemid that belong to the employeeslist view with the given group
					$itemid = $this->getProperItemID('employeeslist', array('employee_group' => $id_group));

					if (!$itemid)
					{
						// fallback to obtain the proper Itemid that belong to the employeeslist view
						$itemid = $this->getProperItemID('employeeslist');
					}

					if ($itemid)
					{
						// overwrite the Itemid set in the query in order
						// to rewrite the base URI
						$query['Itemid'] = $itemid;
					}
				}

				// unset employee ID from query
				unset($query['id_employee']);
			}
		}

		// order | allorders

		else if ($query['view'] == 'order' || $query['view'] == 'allorders')
		{
			// build URL for order details
			if (isset($query['ordnum']) && isset($query['ordkey']))
			{
				// ordnum and ordkey must be set
				// $segments[] = 'order';
				$segments[] = AliasHelper::stringToAlias(intval($query['ordnum']) . "-" . $query['ordkey']);

				// unset ord num and ord key
				unset($query['ordnum']);
				unset($query['ordkey']);
			}

			// try to obtain the proper Itemid that belong to the allorders view
			$itemid = $this->getProperItemID('allorders');

			if (!$itemid)
			{
				// fallback to obtain the proper Itemid that belong to the order view
				$itemid = $this->getProperItemID('order');
			}

			if ($itemid)
			{
				// overwrite the Itemid set in the query in order
				// to rewrite the base URI
				$query['Itemid'] = $itemid;
			}
			else
			{
				// prepend order as it doesn't exist a parent menu item
				array_unshift($segments, 'order');
			}
		}

		/**
		 * The code below is used to push the view name within the $segments array in
		 * case that view is not related to the current menu item.
		 *
		 * The resulting segment will look like:
		 * /emplogin/empeditprofile/
		 * instead of:
		 * /emplogin?view=empeditprofile
		 *
		 * @since 1.6
		 */

		else
		{
			$itemid = $this->getProperItemID($query['view']);

			// if itemid doesn't exist and the current view is different 
			// than the specified one, push the view within the segments
			if (empty($itemid) && (!$active || $query['view'] != $active->query['view']))
			{
				if (substr($query['view'], 0, 3) == 'emp' && !in_array($query['view'], array('employeeslist', 'employeesearch')))
				{
					// fallback to obtain a parent item for employees area views
					$query['Itemid'] = $this->getProperItemID('emplogin');
				}

				$segments[] = $query['view'];
			}
			else if ($itemid)
			{
				// overwrite the Itemid set in the query in order
				// to rewrite the base URI
				$query['Itemid'] = $itemid;
			}
		}

		// unset query view to avoid reporting it within the URL
		unset($query['view']);

		return $segments;
	}

	/**
	 * @override
	 * This method is meant to transform the human readable URL back into
	 * query parameters. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		$total  	= count($segments);
		$active 	= $this->menu->getActive();		
		$query_view = empty($active->query['view']) ? '' : $active->query['view'];
		$vars 		= array();

		if (!$total)
		{
			// no vars
			return $vars;
		}

		// order details

		if ($segments[0] == 'order')
		{
			$vars['view'] = 'order';

			if (count($segments) > 1)
			{
				// make sure the order number and the order key are set
				$exp = explode(":", $segments[1]);

				if (count($exp) == 2)
				{
					$vars['ordnum'] = $exp[0];
					$vars['ordkey'] = $exp[1];
				}
			}
		}

		// order | all orders

		else if ($query_view == 'allorders' || $query_view == 'order')
		{
			// fallback to check if we passed the ordnum and ordkey to retrieve the order
			if (preg_match("/[\d]+:[a-z0-9]{16,16}$/", $segments[0]))
			{
				list($ordnum, $ordkey) = explode(':', $segments[0]);

				$vars['view'] 	= 'order';
				$vars['ordnum'] = $ordnum;
				$vars['ordkey'] = $ordkey;
			}
			else
			{
				$vars['view'] = $segments[0];
			}
		}

		// employees list 

		else if ($segments[0] == 'employeeslist')
		{
			$vars['view'] = 'employeeslist';

			$itemid = $this->getProperItemID($vars['view']);
			
			if (!empty($itemid))
			{
				$vars['Itemid'] = $itemid;
			}
		}

		// services list

		// else if ($segments[0] == 'serviceslist' && ($query_view == 'allorders' || $query_view == 'packages'))
		else if ($segments[0] == 'serviceslist')
		{
			$vars['view'] = 'serviceslist';

			$itemid = $this->getProperItemID($vars['view']);

			if (!empty($itemid))
			{
				$vars['Itemid'] = $itemid;
			}
		}

		// employee login

		else if ($segments[0] == 'emplogin')
		{
			$vars['view'] = 'emplogin';

			$itemid = $this->getProperItemID($vars['view']);

			if (!empty($itemid))
			{
				$vars['Itemid'] = $itemid;
			}
		}

		// waiting list

		else if ($segments[0] == 'pushwl')
		{
			/**
			 * The view name is contained within the segments array.
			 * Without this block, the view name "pushwl" would be
			 * considered as an alias for the service/employee to get.
			 */
			$vars['view'] = $segments[0];
		}

		// service search

		// else if ($segments[0] == 'servicesearch' || $query_view == 'serviceslist' || $query_view == 'allorders' || $query_view == 'packages')
		else if ($segments[0] == 'servicesearch' || $query_view == 'serviceslist')
		{
			// find the index in which the alias should be stored
			$pos 	= $total == 1 ? 0 : 1;
			$alias 	= str_replace(':', '-', $segments[$pos]);

			$id = AliasHelper::getRecordWithAlias($alias, 'service');

			if ($id)
			{
				$vars['id_ser'] = $id;
				$vars['view'] 	= 'servicesearch';

				$itemid = $this->getProperItemID($vars['view']);

				if (!empty($itemid))
				{
					$vars['Itemid'] = $itemid;
				}
			}
		}

		// employee search

		else if ($segments[0] == 'employeesearch' || $query_view == 'employeeslist')
		{
			// find the index in which the alias should be stored
			$pos 	= $total == 1 ? 0 : 1;
			$alias 	= str_replace(':', '-', $segments[$pos]);

			$id = AliasHelper::getRecordWithAlias($alias, 'employee');

			if ($id)
			{
				$vars['id_employee'] = $id;
				$vars['view'] 		 = 'employeesearch';
			}
		}

		/**
		 * The code below is used to retrieve the view name from the $segments array.
		 *
		 * @since 1.6
		 */

		else
		{
			// the view name is contained within the segments array
			$vars['view'] = $segments[0];
		}

		return $vars;
	}
	
	/**
	 * Method used to find the item ID that matches the specified type.
	 *
	 * @param 	string 	 $type 	The item type.
	 * @param 	array 	 $args 	An associative array with the arguments to match.
	 *
	 * @return 	integer  The item ID that matches the type, "zero" otherwise.
	 *
	 * @uses 	matchItemArguments()
	 */
	private function getProperItemID($type, array $args = array())
	{
		foreach ($this->menu->getMenu() as $itemid => $item)
		{
			if ($item->query['option'] == 'com_vikappointments' && $item->query['view'] == $type
				&& ($item->language == '*' || $item->language == $this->langtag)
				&& $this->matchItemArguments($item, $args))
			{
				return $itemid;
			}
		}

		return 0;
	}

	/**
	 * Checks if the item matches all the specified arguments.
	 * The arguments must be contained within the query property.
	 *
	 * @param 	object 	 $item 	The menu item object.
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if the item matches, false otherwise.
	 *
	 * @since 	1.6
	 */
	private function matchItemArguments($item, $args)
	{
		// True in case the arguments are not provided or
		// in case the item query contains all the specified arguments.
		// array_diff_assoc() will return an empty array in case of success.
		return !count($args) || (isset($item->query) && !array_diff_assoc($args, $item->query));
	}
}

/**
 * Builds the route for the com_vikappointments component.
 *
 * This function is a proxy for the new router interface
 * for old SEF extensions.
 *
 * @param   array  &$query  An array of URL arguments.
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @deprecated  4.0  Use Class based routers instead.
 */
function vikappointmentsBuildRoute(&$query)
{
	if (UIFactory::getConfig()->getBool('router', false) === false)
	{
		// router is disabled, return an empty segment
		return array();
	}

	$router = new VikAppointmentsRouter();

	return $router->build($query);
}

/**
 * Parses the segments of a URL.
 *
 * This function is a proxy for the new router interface
 * for old SEF extensions.
 *
 * @param   array  $segments  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @deprecated  4.0  Use Class based routers instead.
 */
function vikappointmentsParseRoute($segments)
{
	if (UIFactory::getConfig()->getBool('router', false) === false)
	{
		// router is disabled, return an empty segment
		return array();
	}

	$router = new VikAppointmentsRouter();

	return $router->parse($segments);
}
