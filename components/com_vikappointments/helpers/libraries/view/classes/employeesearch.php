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
 * VikAppointments Employee Search view contents handler.
 * This class provides helpful tools to enhance the
 * <head> of the employeesearch pages.
 *
 * @since  	1.6
 */
class VAPViewContentsEmployeeSearch extends VAPViewContents
{
	/**
	 * A registry containing the employee information.
	 *
	 * @var JRegistry
	 */
	protected $employee;

	/**
	 * The employee description.
	 *
	 * @var string
	 */
	protected $description = null;

	/**
	 * The maximum number of characters to display
	 * for the employee description.
	 *
	 * @var integer
	 */
	protected $descLength = 256;

	/**
	 * Class constructor.
	 *
	 * @param 	mixed 	$page 	The view object.
	 * @param 	string 	$type 	The view file/type. If not provided
	 * 							it will be calculated from the $page class name.
	 */
	public function __construct($page, $type = null)
	{
		parent::__construct($page, $type);

		if (isset($page->employee))
		{
			$tmp = $page->employee;
		}
		else
		{
			$tmp = array();
		}

		$this->employee = new JRegistry($tmp);
	}

	/**
	 * @override
	 * Sets the meta description according to the settings of the page.
	 *
	 * @return 	boolean  True if set, otherwise false.
	 */
	public function setMetaDescription()
	{
		// before all, set the description of the current menu item
		$set = parent::setMetaDescription();

		// if the description was empty or the current menu item is
		// not equals to the view set in the request, try to use
		// the description related to the employee
		if (!$set || $this->activeView != $this->currentView)
		{
			// get employee description
			$desc = strip_tags($this->employee->get('note'));

			if ($desc)
			{
				$this->page->document->setDescription($desc);

				$set = true;
			}
		}

		return $set;
	}

	/**
	 * @override
	 * Sets the browser page title.
	 *
	 * @return 	boolean  True if set, otherwise false.
	 *
	 * @since 	1.6.1
	 */
	public function setPageTitle()
	{
		// if the current menu item is not equals to the view set 
		// in the request, try to use the title related to the employee
		if ($this->activeView != $this->currentView)
		{
			$title = $this->page->document->getTitle() . ' - ' . $this->employee->get('nickname');

			$this->page->document->setTitle($title);

			return true;
		}

		return false;
	}

	/**
	 * Returns the employee description.
	 *
	 * @return 	string 	A short version of the description.
	 */
	protected function getEmployeeDescription()
	{
		if ($this->description === null)
		{
			// get employee description
			$desc = $this->employee->get('note', '');

			if ($desc)
			{
				// render HTML description to grab plugin contents and short description
				VikAppointments::renderHtmlDescription($desc, 'microdata');

				// strip HTML tags from description
				$desc = strip_tags($desc);

				// check if the description length exceeds the limit
				if (strlen($desc) > $this->descLength)
				{
					// use the first N characters of the employee description
					$desc = mb_substr(strip_tags($desc), 0, $this->descLength, 'UTF-8') . '...';
				}
			}

			// cache description
			$this->description = $desc;
		}

		return $this->description;
	}

	/**
	 * @override
	 * Creates the OPEN GRAPH protocol according to the 
	 * entity of the current page.
	 *
	 * @return 	self 	This object to support chaining.
	 *
	 * @uses 	getEmployeeDescription()
	 */
	public function buildOpenGraph()
	{
		$doc = $this->page->document;

		// basic metadata
		$doc->setMetaData('og:title'		, $this->employee->get('nickname'));
		$doc->setMetaData('og:description'	, $this->getEmployeeDescription());
		$doc->setMetaData('og:type'			, 'profile');

		if ($this->employee->get('image'))
		{
			$doc->setMetaData('og:image', VAPMEDIA_URI . $this->employee->get('image'));
		}

		// profile metadata
		$doc->setMetaData('profile:first_name'	, $this->employee->get('firstname'));
		$doc->setMetaData('profile:last_name'	, $this->employee->get('lastname'));
		$doc->setMetaData('profile:username'	, $this->employee->get('nickname'));

		// invoke parent to include generic metadata
		return parent::buildOpenGraph();
	}

	/**
	 * @override
	 * Returns the microdata object to include within the head of the page.
	 * Inherits this method to dispatch the attachment of the properties to 
	 * children classes.
	 *
	 * @param 	object 	 &$json  The root object used to attach microdata.
	 *
	 * @return 	boolean  True in case the object should be attached, otherwise false.
	 */
	protected function getMicrodataObject(&$json)
	{
		/**
		 * Organization schema type.
		 *
		 * @link https://schema.org/Organization
		 */

		$config = UIFactory::getConfig();

		// define schema type
		$json->{"@context"} = 'http://schema.org';
		$json->{"@type"}	= 'Organization';

		// add employee information
		$json->name 		= $this->employee->get('nickname');
		$json->description  = $this->getEmployeeDescription();

		if ($this->employee->get('image'))
		{
			$json->image = VAPMEDIA_URI . $this->employee->get('image');
		}

		// add reviews
		if ($this->page->reviews['size'] > 0)
		{
			/**
			 * AggregateRating schema type.
			 *
			 * @link https://schema.org/AggregateRating
			 */
			$json->aggregateRating = new stdClass;
			$json->aggregateRating->{"@type"} 	= 'AggregateRating';
			$json->aggregateRating->ratingValue = $this->employee->get('rating_avg');
			$json->aggregateRating->reviewCount = $this->page->reviews['size'];

			// add latest 2 reviews
			$json->review = array();

			$lim = 2; // number of reviews to show
			$lim = min(array((int) $this->page->reviews['size'], $lim));

			for ($i = 0; $i < $lim; $i++)
			{
				$tmp = $this->page->reviews['rows'][$i];

				/**
				 * Review schema type.
				 *
				 * @link https://schema.org/Review
				 */
				$review = new stdClass;
				$review->{"@type"} 		= 'Review';
				$review->author 		= $tmp['name'];
				$review->datePublished 	= JDate::getInstance($tmp['timestamp'])->format('Y-m-d');
				$review->description 	= $tmp['comment'];
				$review->name 			= $tmp['title'];

				/**
				 * Rating schema type.
				 *
				 * @link https://schema.org/Rating
				 */
				$review->reviewRating 	= new stdClass;
				$review->reviewRating->{"@type"} 	= "Rating";
				$review->reviewRating->bestRating 	= 5;
				$review->reviewRating->ratingValue 	= $tmp['rating'];
				$review->reviewRating->worstRating 	= 1;

				$json->review[] = $review;
			}
		}

		// add offers
		if ($this->page->services)
		{
			$json->makesOffer = array();

			foreach ($this->page->services as $service)
			{
				/**
				 * Offer schema type.
				 *
				 * @link https://schema.org/Offer
				 */
				$offer = new stdClass;
				$offer->{"@type"} 	  = 'Offer';
				$offer->name 		  = $service['name'];
				$offer->price 	 	  = $service['rate'];
				$offer->priceCurrency = $config->get('currencyname');

				$json->makesOffer[] = $offer;
			}

			if (count($json->makesOffer) == 1)
			{
				// use a singe offer instead of an array in case
				// the employee is assigned to only one service
				$json->makesOffer = $json->makesOffer[0];
			}
		}

		// add location address
		if ($this->page->locations)
		{
			$json->address = array();

			foreach ($this->page->locations as $location)
			{
				/**
				 * PostalAddress schema type.
				 *
				 * @link https://schema.org/PostalAddress
				 */
				$addr = new stdClass;
				$addr->{"@type"} 		= 'PostalAddress';
				$addr->name 			= $location['name'];
				$addr->postalCode 		= $location['zip'];
				$addr->streetAddress 	= $location['address'];

				if ($location['country_2_code'])
				{
					$addr->addressCountry = $location['country_2_code'];
				}

				if ($location['state_2_code'])
				{
					$addr->addressRegion = $location['state_2_code'];
				}

				if ($location['city_name'])
				{
					$addr->addressLocality = $location['city_name'];
				}

				// add opening hours
				$worktimes = $this->getEmployeeWorktimes($location['id']);

				if ($worktimes)
				{
					$addr->hoursAvailable = array();

					foreach ($worktimes as $time)
					{
						$addr->hoursAvailable[] = $time;
					}
				}

				$json->address[] = $addr;
			}

			if (count($json->address) == 1)
			{
				// use a singe address instead of an array in case
				// the employee is located in only one venue
				$json->address = $json->address[0];
			}
		}

		// add contact info
		if ($this->employee->get('email'))
		{
			$json->email = $this->employee->get('email');
		}

		if ($this->employee->get('showphone') && $this->employee->get('phone'))
		{
			$json->telephone = $this->employee->get('phone');
		}

		return true;
	}

	/**
	 * Returns the employee working times using the
	 * OpeningHoursSpecification schema format.
	 *
	 * @param 	integer  $location 	The ID of the location for which the employee works.
	 *
	 * @return 	array 	 The available working times.
	 */
	protected function getEmployeeWorktimes($location)
	{
		if (!isset($this->worktimes))
		{
			$dbo = JFactory::getDbo();

			$this->worktimes = array();

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_emp_worktime'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $this->employee->get('id'),
					$dbo->qn('id_service') . ' <= 0', 
				))
				->andWhere(array(
					$dbo->qn('ts') . ' <= 0',
					$dbo->qn('ts') . ' BETWEEN ' . strtotime('today 00:00:00') . ' AND ' . strtotime('+7 days 00:00:00'),
				), 'OR')
				->order(array(
					$dbo->qn('ts') . ' ASC',
					$dbo->qn('day') . ' ASC',
				));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				foreach ($dbo->loadObjectList() as $time)
				{
					if (!isset($this->worktimes[$time->id_location]))
					{
						$this->worktimes[$time->id_location] = array();
					}

					$this->worktimes[$time->id_location][] = $time;
				}
			}
		}

		$times = array();

		if (isset($this->worktimes[$location]))
		{
			$times = $this->worktimes[$location];
		}

		if ($location > 0 && isset($this->worktimes["-1"]))
		{
			// merge global working times
			$times = array_merge($times, $this->worktimes["-1"]);
		}

		$tmp = array();

		foreach ($times as $time)
		{
			/**
			 * OpeningHoursSpecification schema type.
			 *
			 * @link https://schema.org/OpeningHoursSpecification
			 */
			$spec = new stdClass;
			$spec->{"@type"} = 'OpeningHoursSpecification';

			if (!$time->closed)
			{
				/**
				 * The place is open if the opens property is specified, and closed otherwise.
				 * So, do not provide *opens* and *closes* in case the working day is closed.
				 */

				$from_hour = floor($time->fromts / 60);
				$from_min  = $time->fromts % 60;

				$end_hour  = floor($time->endts / 60);
				$end_min   = $time->endts % 60;

				$spec->closes 	 = date('H:i:s', strtotime(sprintf('today %s:%s:00', $from_hour, $from_min)));
				$spec->opens 	 = date('H:i:s', strtotime(sprintf('today %s:%s:00', $end_hour, $end_min)));
			}

			if ($time->ts <= 0)
			{
				// day of the week
				switch ($time->day)
				{
					case 1:
						$spec->dayOfWeek = 'Monday';
						break;

					case 2:
						$spec->dayOfWeek = 'Tuesday';
						break;

					case 3:
						$spec->dayOfWeek = 'Wednesday';
						break;

					case 4:
						$spec->dayOfWeek = 'Thursday';
						break;

					case 5:
						$spec->dayOfWeek = 'Friday';
						break;

					case 6:
						$spec->dayOfWeek = 'Saturday';
						break;

					default:
						$spec->dayOfWeek = 'Sunday';

				}

				$spec->dayOfWeek = 'http://schema.org/' . $spec->dayOfWeek;
			}
			else
			{
				// day of the year
				$spec->validFrom = JDate::getInstance($time->ts)->format('Y-m-d');
				$spec->validTo   = JDate::getInstance('+1 day ' . $spec->validFrom)->format('Y-m-d');
			}

			$tmp[] = $spec;
		}

		return $tmp;
	}
}
