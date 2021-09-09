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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * VikAppointments View.
 */
class VikAppointmentsViewemployeeslist extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		VikAppointments::load_fancybox();
		VikAppointments::load_complex_select();
		VikAppointments::load_utils();
		VikAppointments::load_font_awesome();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$sel_emp 	= $input->getInt('id_emp', 0);
		$sel_group 	= $input->getInt('employee_group', 0);
		$itemid 	= $input->getInt('Itemid');

		// pagination limit
		$lim 	= VikAppointments::getEmployeesListLimit();
		$lim0 	= $input->getUint('limitstart', 0);
		$navbut = '';
		
		// ordering
		$available_orderings = VikAppointments::getEmployeesAvailableOrderings();
		$default_ordering 	 = VikAppointments::getEmployeesListingMode();

		$listing_mode = $app->getUserStateFromRequest('employees.ordering', 'ordering', null, 'uint');

		if (empty($listing_mode) || !in_array($listing_mode, $available_orderings))
		{
			$listing_mode = $default_ordering;
		}

		// search query (filters in request)
		$filters = $input->get('filters', array(), 'array');
		$filters_in_request = (bool) $filters;

		// check if the service is set in the request
		if (!isset($filters['service']))
		{
			// since the service is not specified, we cannot sort
			// the employees depending on the given rate
			if ($listing_mode == 7 || $listing_mode == 8)
			{
				// use the defauld one
				$listing_mode = $default_ordering;
			}

			// if the listing mode is still set to "price",
			// it means that the first mode cannot be used
			if ($listing_mode == 7 || $listing_mode == 8)
			{
				// use a..Z
				$listing_mode = 1;
			}
		}
		
		$employees = array();
		
		// standard query (no filters)
		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `e`.*');
		$q->select($dbo->qn('eg.name', 'group_name'));
		$q->select('(' . $this->getRatingQuery($dbo) . ') AS ' . $dbo->qn('rating_avg'));
		$q->select('(' . $this->getReviewsQuery($dbo) . ') AS ' . $dbo->qn('reviews_count'));

		$q->from($dbo->qn('#__vikappointments_employee', 'e'));
		$q->leftjoin($dbo->qn('#__vikappointments_employee_group', 'eg') . ' ON ' . $dbo->qn('e.id_group') . ' = ' . $dbo->qn('eg.id'));

		$q->where($dbo->qn('e.listable') . ' = 1');

		if (!empty($sel_group))
		{
			$q->where($dbo->qn('e.id_group') . ' = ' . $sel_group);
		}

		$q->andWhere(array(
			$dbo->qn('e.active_to') . ' = -1',
			$dbo->qn('e.active_to') . ' >= ' . time(),
		), 'OR');

		$q->group($dbo->qn('e.id'));
		
		if ($listing_mode == 1)
		{
			// alphabetically a..Z
			$q->order(array(
				$dbo->qn('e.lastname') . ' ASC',
				$dbo->qn('e.firstname') . ' ASC',
			));
		}
		else if ($listing_mode == 2)
		{
			// alphabetically Z..a
			$q->order(array(
				$dbo->qn('e.lastname') . ' DESC',
				$dbo->qn('e.firstname') . ' DESC',
			));
		}
		else if ($listing_mode == 3)
		{
			// newest 
			$q->order($dbo->qn('e.id') . ' DESC');
		}
		else if ($listing_mode == 4)
		{
			// oldest 
			$q->order($dbo->qn('e.id') . ' ASC');
		}
		else if ($listing_mode == 5)
		{
			// most popular
			$q->leftjoin($dbo->qn('#__vikappointments_reservation', 'r') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('r.id_employee'));
			$q->order('COUNT(' . $dbo->qn('r.id') . ') DESC');
		}
		else if ($listing_mode == 6)
		{
			// highest rating 
			$q->order(array(
				$dbo->qn('rating_avg') . ' DESC',
				$dbo->qn('reviews_count') . ' DESC',
			));
		}
		else if ($listing_mode == 7)
		{
			// lowest price
			$q->order($dbo->qn('a.rate') . ' ASC');
		}
		else if ($listing_mode == 8)
		{
			// highest price
			$q->order($dbo->qn('a.rate') . ' DESC');
		}

		if ($filters_in_request)
		{
			$this->buildQueryWithFilters($q, $filters, $dbo);
		}
		
		$employees_count = 0;
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$employees_count = $dbo->loadResult();
			$pageNav = new JPagination($employees_count, $lim0, $lim);

			$this->preparePageLinks($filters, $sel_group, $pageNav);
			$navbut = $pageNav->getPagesLinks();
			
			foreach ($employees as $i => $e)
			{
				$employees[$i]['locations_list'] = $this->getEmployeeLocations($e['id'], $dbo);
			}
		}

		// get all employees groups
		$groups = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('g.id', 'g.name')))
			->from($dbo->qn('#__vikappointments_employee_group', 'g'))
			->order($dbo->qn('g.ordering') . ' ASC');

		/**
		 * Load only the groups that contain at least an employee.
		 * In case you need to display also the groups with no employees,
		 * just change the flag below to "true".
		 *
		 * @since 1.6
		 */
		$get_empty_groups = false;

		if (!$get_empty_groups)
		{
			// create a new inner query to count
			// the employees assigned to the groups
			$tmp = $dbo->getQuery(true)
				->select('COUNT(1)')
				->from($dbo->qn('#__vikappointments_employee', 'e'))
				->where(array(
					$dbo->qn('e.id_group') . ' = ' . $dbo->qn('g.id'),
				));

			// add inner query to obtain the number of employees assigned to this group
			$q->select('(' . $tmp . ') AS ' . $dbo->qn('count'));
			// get only the groups that own at least an employee
			$q->having($dbo->qn('count') . ' > 0');
		}

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}
		
		$listing_details = VikAppointments::getEmployeesListingDetails();

		if ($listing_details['ajaxsearch'] == 2)
		{
			// if AJAX seach should be used only with filters, make sure
			// there is at least a filter set
			$listing_details['ajaxsearch'] = (int) $filters_in_request;
		}
		
		$this->employees 	= &$employees;
		$this->groups 		= &$groups;
		$this->selEmployee 	= &$sel_emp;
		$this->selGroup 	= &$sel_group;
		$this->itemid 		= &$itemid;
		$this->navbut 		= &$navbut;
		$this->limitStart	= &$lim0;
		
		$this->descLength 		= &$listing_details['desclength'];
		$this->linkHref 		= &$listing_details['linkhref'];
		$this->groupsFilter 	= &$listing_details['filtergroups'];
		$this->orderingFilter 	= &$listing_details['filterordering'];
		$this->ajaxSearch 		= &$listing_details['ajaxsearch'];
		
		$this->filtersInRequest 	= &$filters_in_request;
		$this->requestFilters 		= &$filters;
		$this->employeesCount 		= &$employees_count;
		$this->ordering 			= &$listing_mode;
		$this->availableOrderings 	= &$available_orderings;
		
		$lang_employees = VikAppointments::getTranslatedEmployees();
		$lang_groups 	= VikAppointments::getTranslatedEmployeeGroups();

		$this->langEmployees 	= &$lang_employees;
		$this->langGroups 		= &$lang_groups;

		if ($this->ajaxSearch)
		{
			$this->addJS();
		}

		// prepare page content
		VikAppointments::prepareContent($this);
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Pushes the filters parameters into the pagination links.
	 *
	 * @param 	array 	$filters 	The filters to push.
	 * @param 	mixed 	$group 		The selected group (if any).
	 * @param 	mixed 	&$pageNav 	The pagination object.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	protected function preparePageLinks(array $filters, $group, &$pageNav)
	{
		foreach ($filters as $k => $v)
		{
			/**
			 * Appends only filters that own a value as it doesn't
			 * make sense to populate the URL using empty variables.
			 *
			 * @since 1.6.2
			 */
			if ($v)
			{
				$pageNav->setAdditionalUrlParam("filters[$k]", $v);
			}
		}

		/**
		 * Include also the employee group (if specified) within
		 * the links used by the pagination to switch pages.
		 *
		 * @since 1.6 
		 */
		if ($group)
		{
			$pageNav->setAdditionalUrlParam("employee_group", $group);
		}

		return $this;
	}

	/**
	 * Returns the inner query that should be used to calculate the
	 * average rating of the employees.
	 *
	 * @param 	mixed 	$dbo 	The database object.
	 *
	 * @return 	mixed 	The database query.
	 *
	 * @since 	1.6
	 */
	protected function getRatingQuery($dbo)
	{
		return $dbo->getQuery(true)
			->select('AVG(' . $dbo->qn('re.rating') . ')')
			->from($dbo->qn('#__vikappointments_reviews', 're'))
			->where(array(
				$dbo->qn('e.id') . ' = ' . $dbo->qn('re.id_employee'),
				$dbo->qn('re.published') . ' = 1',
			));
	}

	/**
	 * Returns the inner query that should be used to calculate the
	 * number of reviews of the employees.
	 *
	 * @param 	mixed 	$dbo 	The database object.
	 *
	 * @return 	mixed 	The database query.
	 *
	 * @since 	1.6
	 */
	protected function getReviewsQuery($dbo)
	{
		return $dbo->getQuery(true)
			->select('COUNT(' . $dbo->qn('re.rating') . ')')
			->from($dbo->qn('#__vikappointments_reviews', 're'))
			->where(array(
				$dbo->qn('e.id') . ' = ' . $dbo->qn('re.id_employee'),
				$dbo->qn('re.published') . ' = 1',
			));
	}
	
	/**
	 * Extends the search query using the filters set.
	 *
	 * @param 	mixed 	&$q 		The query builder object.
	 * @param 	array 	$filters 	The associative array of filters.
	 * @param 	mixed 	$dbo 		The database object.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	protected function buildQueryWithFilters(&$q, array $filters, $dbo)
	{
		if (!empty($filters['group']) || !empty($filters['service']) || !empty($filters['price']))
		{
			$q->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('a.id_employee') . ' = ' . $dbo->qn('e.id'));
			$q->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('a.id_service') . ' = ' . $dbo->qn('s.id'));
			
			if (!empty($filters['group']))
			{
				$q->where($dbo->qn('s.id_group') . ' = ' . intval($filters['group']));
			}

			if (!empty($filters['service']))
			{
				$q->select($dbo->qn('a.rate'));
				$q->where($dbo->qn('s.id') . ' = ' . intval($filters['service']));

				/**
				 * Retrieve only the services the belong to the view
				 * access level of the current user.
				 *
				 * @since 1.6
				 */
				$levels = JFactory::getUser()->getAuthorisedViewLevels();

				if ($levels)
				{
					$q->where($dbo->qn('s.level') . ' IN (' . implode(', ', $levels) . ')');
				}
			}

			if (!empty($filters['price']))
			{
				$range = explode(':', $filters['price']);

				if (count($range) != 2)
				{
					$range = array(0, 0);
				}
				else
				{
					$range = array_map('intval', $range);
				}

				$q->where($dbo->qn('a.rate') . ' BETWEEN ' . implode(' AND ', $range));
			}
		}
		
		if (!empty($filters['country']) || !empty($filters['state']) || !empty($filters['city'])
			|| !empty($filters['zip']) || !empty($filters['nearby']) || !empty($filters['id_location']))
		{
			$q->leftjoin($dbo->qn('#__vikappointments_emp_worktime', 'w') . ' ON ' . $dbo->qn('w.id_employee') . ' = ' . $dbo->qn('e.id'));
			$q->leftjoin($dbo->qn('#__vikappointments_employee_location', 'l') . ' ON ' . $dbo->qn('w.id_location') . ' = ' . $dbo->qn('l.id'));

			if (empty($filters['nearby']))
			{
				if (!empty($filters['country']))
				{
					$q->where($dbo->qn('l.id_country') . ' = ' . intval($filters['country']));
				}

				if (!empty($filters['state']))
				{
					$q->where($dbo->qn('l.id_state') . ' = ' . intval($filters['state']));
				}

				if (!empty($filters['city']))
				{
					$q->where($dbo->qn('l.id_city') . ' = ' . intval($filters['city']));
				}

				if (!empty($filters['zip']))
				{
					$q->where($dbo->qn('l.zip') . ' = ' . $dbo->q($filters['zip']));
				}
			}
			// make sure the browser has been authorised to obtain the coordinates
			else if (!empty($filters['base_coord']))
			{
				// get query for geodetica
				$distance 	= intval($filters['distance']);
				$coord 		= explode(",", $filters['base_coord']);
				
				if (count($coord) < 2)
				{
					$coord = array(0, 0);
				}

				list($lat, $lng) = array_map('floatval', $coord);

				/**
				 * Convert distance to km for query as the Earth radius
				 * is specified in kilometers.
				 *
				 * @since 1.6
				 */
				$distance = VikAppointments::convertDistanceToKilometers($distance, $filters);

				$q->where($this->getNearbyWhereQuery($lat, $lng, $distance));
			}

			/**
			 * Filter the employees also by location ID.
			 *
			 * @since 1.6
			 */
			if (!empty($filters['id_location']))
			{
				$q->where($dbo->qn('w.id_location') . ' = ' . (int) $filters['id_location']);
			}
		}
		
		/**
		 * Extend query using the employees custom fields.
		 *
		 * @since 1.6
		 */
		VikAppointments::extendQueryWithCustomFilters($q, $filters, 'e', $dbo);

		return $this;
	}

	/**
	 * Returns the WHERE statement used to filter the employees
	 * in the nearby of the specified coordinates.
	 *
	 * @param 	float 	 $lat 		The center latitude.
	 * @param 	float 	 $lng 		The center longitude.
	 * @param 	integer  $distance 	The circle radius (in km).
	 *
	 * @return 	string 	 The query WHERE statement.
	 */
	protected function getNearbyWhereQuery($lat, $lng, $distance)
	{
		$lat = $lat * pi() / 180.0;
		$lng = $lng * pi() / 180.0;

		/** 
		 * Distance between 2 coordinates.
		 *
		 * R = 6371 (Eart radius ~6371 km)
		 *
		 * Coordinates in radiants
		 * lat1, lng1, lat2, lng2
		 *
		 * Calculate the included angle fi
		 * fi = abs( lng1 - lng2 );
		 *
		 * Calculate the third side of the spherical triangle
		 * p = acos( 
		 *      sin(lat2) * sin(lat1) + 
		 *      cos(lat2) * cos(lat1) * 
		 *      cos( fi ) 
		 * )
		 * 
		 * Multiply the third side per the Earth radius (distance in km)
		 * D = p * R;
		 *
		 * MINIFIED EXPRESSION
		 *
		 * acos( 
		 *      sin(lat2) * sin(lat1) + 
		 *      cos(lat2) * cos(lat1) * 
		 *      cos( abs(lng1 - lng2) ) 
		 * ) * R
		 *
		 */

		return "`l`.`latitude` IS NOT NULL AND (ACOS(
				SIN(RADIANS(`l`.`latitude`)) * SIN($lat) +
				COS(RADIANS(`l`.`latitude`)) * COS($lat) *
				COS(ABS($lng - RADIANS(`l`.`longitude`)))
			) * 6371) < $distance";
	}

	/**
	 * Returns the list of locations assigned to the given employee.
	 *
	 * @param 	integer  $id 	The employee ID.
	 * @param 	mixed 	 $dbo 	The database object.
	 *
	 * @return 	array 	 The locations list.
	 *
	 * @since 	1.6
	 */
	protected function getEmployeeLocations($id, $dbo)
	{
		$locations = array();

		$q = $dbo->getQuery(true);
		
		// EMPLOYEE LOCATIONS
		$q->select($dbo->qn(array(
			'l.name',
			'l.address',
			'l.zip',
			'c.country_name',
			's.state_name',
			'ci.city_name',
			'l.latitude',
			'l.longitude',
		)));

		$q->from($dbo->qn('#__vikappointments_employee_location', 'l'));
		$q->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.id') . ' = ' . $dbo->qn('l.id_country'));
		$q->leftjoin($dbo->qn('#__vikappointments_states', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('l.id_state'));
		$q->leftjoin($dbo->qn('#__vikappointments_cities', 'ci') . ' ON ' . $dbo->qn('ci.id') . ' = ' . $dbo->qn('l.id_city'));

		$q->where($dbo->qn('l.id_employee') . ' = ' . (int) $id);

		$dbo->setQuery($q);
		$dbo->execute();
		if ($dbo->getNumRows())
		{
			$locations = $dbo->loadAssocList();
		}

		// GLOBAL LOCATIONS (adjust the query built previously)
		$q->leftjoin($dbo->qn('#__vikappointments_emp_worktime', 'w') . ' ON ' . $dbo->qn('l.id') . ' = ' . $dbo->qn('w.id_location'));

		// clear existing where
		$q->clear('where');
		$q->where(array(
			$dbo->qn('l.id_employee') . ' = -1',
			$dbo->qn('w.id_employee') . ' = ' . (int) $id,
		));
		
		$q->group($dbo->qn('l.id'));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$locations = array_merge($locations, $dbo->loadAssocList());
		}

		return $locations;
	}

	/**
	 * Helper method used to include the JS functions
	 * required for the AJAX search tool.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	private function addJS()
	{
		// load font awesome for arrows
		VikAppointments::load_font_awesome();

		// build AJAX end-point
		$url = JRoute::_("index.php?option=com_vikappointments&task=get_employee_avail_table&tmpl=component" . ($this->itemid ? "&Itemid=" . $this->itemid : ''), false);

		// setup display data for layout
		$data = array(
			'url' => $url,
		);

		/**
		 * The javascript functions needed by the time table are declared by the layout below:
		 * /components/com_vikappointments/layouts/javascript/timeline/table.php
		 * 
		 * If you need to change something from this layout, just create
		 * an override of this layout by following the instructions below:
		 * - open the back-end of your Joomla
		 * - visit the Extensions > Templates > Templates page
		 * - edit the active template
		 * - access the "Create Overrides" tab
		 * - select Layouts > com_vikappointments > javascript
		 * - start editing the timeline/table.php file on your template to create your own layout
		 *
		 * @since 1.6
		 */
		$js = JLayoutHelper::render('javascript.timeline.table', $data);

		$this->document->addScriptDeclaration($js);
	}
}
