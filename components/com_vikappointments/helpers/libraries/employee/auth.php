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
 * Class used to check the authorisations of the attached employee.
 *
 * @since 1.2
 */
class EmployeeAuth
{
	/**
	 * A list of instances.
	 *
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * An associative array containing the details of the employee.
	 *
	 * @var array
	 */
	protected $employee = null;

	/**
	 * Configuration class handler.
	 *
	 * @var UIConfig
	 */
	protected $config;
	
	/**
	 * Class constructor.
	 *
	 * @param 	integer   $id 		The user ID.
	 * @param 	UIConfig  $config  	The config handler.
	 */
	public function __construct($id, UIConfig $config = null)
	{
		if (is_null($config))
		{
			$this->config = UIFactory::getConfig();
		}
		else
		{
			$this->config = $config;
		}

		$this->loadEmployee($id);
	}

	/**
	 * Provides the instance of the employee auth object,
	 * only creating it if it doesn't already exist.
	 *
	 * @param 	integer   $id 		The user ID.
	 * @param 	UIConfig  $config  	The config handler.
	 *
	 * @return 	self 	  A new instance.
	 *
	 * @since 	1.6
	 */
	public static function getInstance($id = null, $config = null)
	{
		if (is_null($id))
		{
			$id = JFactory::getUser()->id;
		}

		if (!isset(static::$instances[$id]))
		{
			static::$instances[$id] = new static($id, $config);
		}

		return static::$instances[$id];
	}

	/**
	 * Method used to load the details of the employee
	 * assigned to the specified user ID.
	 *
	 * @param 	integer  $user_id 	The Joomla user ID.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	protected function loadEmployee($user_id)
	{
		if ($user_id <= 0)
		{
			return;
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_employee'))
			->where($dbo->qn('jid') . ' = ' . (int) $user_id);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$this->employee = $dbo->loadAssoc();
		}
	}

	/**
	 * Magic method to access the properties of the employee.
	 *
	 * @param 	string 	$name 	The property name.
	 *
	 * @return 	mixed 	The property value if exists, otherwise null.
	 *
	 * @since 	1.6
	 */
	public function __get($name)
	{
		if ($this->isEmployee() && isset($this->employee[$name]))
		{
			return $this->employee[$name];
		}

		return null;
	}

	/**
	 * Checks if the current user is an employee.
	 *
	 * @return 	boolean
	 *
	 * @since 	1.6
	 */
	public function isEmployee()
	{
		return $this->employee !== null;
	}

	/**
	 * Returns the employee details.
	 *
	 * @return 	mixed 	An array if exists, otherwise null.
	 *
	 * @since 	1.6
	 */
	public function getEmployee()
	{
		return $this->employee;
	}

	/**
	 * Checks if an employee can manage its profile.
	 *
	 * @return 	boolean
	 */
	public function manage()
	{
		return $this->isEmployee() && $this->config->getBool('empmanage');
	}
	
	/**
	 * Checks if an employee can create a new service.
	 *
	 * @return 	boolean
	 */
	public function create()
	{
		return $this->isEmployee() && $this->config->getBool('empcreate');
	}

	/**
	 * Checks if an employee can remove an existing service.
	 *
	 * @return 	boolean
	 */
	public function remove()
	{
		return $this->isEmployee() && $this->config->getBool('empremove');
	}
	
	/**
	 * Checks if an employee can update an existing service.
	 *
	 * @param 	mixed 	$service 	The service details of the service ID.
	 *
	 * @return 	boolean
	 */
	public function manageServices($service = array())
	{
		if (!$this->isEmployee())
		{
			return false;
		}

		if (!empty($service) && !is_array($service))
		{
			// the service is an ID, load it from the database
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select($dbo->qn('createdby'))
				->from($dbo->qn('#__vikappointments_service'))
				->where($dbo->qn('id') . ' = ' . (int) $service);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$service = $dbo->loadAssoc();
			}
			else
			{
				$service = array();
			}
		}

		// check if the service has been created by this employee
		if (!empty($service['createdby']) && $service['createdby'] == $this->jid)
		{
			// in this case, we don't need to check the configuration
			return true;
		}

		return $this->config->getBool('empmanageser');
	}

	/**
	 * Checks if the employee can create relationships with global services.
	 *
	 * @return 	boolean
	 *
	 * @since 	1.6
	 */
	public function attachServices()
	{
		return $this->isEmployee() && $this->config->getBool('empattachser');
	}
	
	/**
	 * Checks if an employee can update the service rates.
	 *
	 * @return 	boolean
	 */
	public function manageServicesRates()
	{
		return $this->isEmployee() && $this->config->getBool('empmanagerate');
	}
	
	/**
	 * Checks if an employee can create, edit and remove payments.
	 * If the payment ID is provided, checks if the payment is
	 * owned by the current employee.
	 *
	 * @param 	integer  $id 	The payment ID.
	 *
	 * @return 	boolean
	 */
	public function managePayments($id = 0)
	{
		if (!$this->isEmployee())
		{
			return false;
		}

		// check if the employee is the owner
		if ($id > 0)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select(1)
				->from($dbo->qn('#__vikappointments_gpayments'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $this->id,
					$dbo->qn('id') . ' = ' . (int) $id,
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				return false;
			}
		}

		return $this->config->getBool('empmanagepay');
	}

	/**
	 * Checks if an employee can create, edit and remove coupons.
	 * If the coupon ID is provided, checks if the coupon is
	 * owned by the current employee.
	 *
	 * @param 	integer  $id 	The coupon ID.
	 *
	 * @return 	boolean
	 */
	public function manageCoupons($id = 0)
	{
		if (!$this->isEmployee())
		{
			return false;
		}

		// check if the employee is the owner
		if ($id > 0)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select(1)
				->from($dbo->qn('#__vikappointments_coupon_employee_assoc'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $this->id,
					$dbo->qn('id_coupon') . ' = ' . (int) $id,
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				return false;
			}
		}

		return $this->config->getBool('empmanagecoupon');
	}

	/**
	 * Checks if an employee can create, edit and remove custom fields.
	 * If the custom field ID is provided, checks if it is
	 * owned by the current employee.
	 *
	 * @param 	integer  $id 	The field ID.
	 *
	 * @return 	boolean
	 */
	public function manageCustomFields($id = 0)
	{
		if (!$this->isEmployee())
		{
			return false;
		}

		// check if the employee is the owner
		if ($id > 0)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select(1)
				->from($dbo->qn('#__vikappointments_custfields'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $this->id,
					$dbo->qn('id') . ' = ' . (int) $id,
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				return false;
			}
		}

		return $this->config->getBool('empmanagecustfield');
	}
	
	/**
	 * Checks if an employee can create, edit and remove working days.
	 *
	 * @return 	boolean
	 */
	public function manageWorkDays()
	{
		return $this->isEmployee() && $this->config->getBool('empmanagewd');
	}
	
	/**
	 * Checks if an employee can create, edit and remove locations.
	 * If the location ID is provided, checks if the location is
	 * owned by the current employee.
	 *
	 * @param 	integer  $id 	The location ID.
	 *
	 * @return 	boolean
	 */
	public function manageLocations($id = 0)
	{
		if (!$this->isEmployee())
		{
			return false;
		}

		// check if the employee is the owner
		if ($id > 0)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select(1)
				->from($dbo->qn('#__vikappointments_employee_location'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $this->id,
					$dbo->qn('id') . ' = ' . (int) $id,
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				return false;
			}
		}

		return $this->config->getBool('empmanageloc');
	}

	/**
	 * Checks if an employee can create new reservations.
	 *
	 * @return 	boolean
	 */
	public function rescreate()
	{
		return $this->isEmployee() && $this->config->getBool('emprescreate');
	}
	
	/**
	 * Checks if an employee can update existing reservations.
	 * If the reservation ID is provided, checks if the reservation is
	 * owned by the current employee.
	 *
	 * @param 	integer  $id 	The reservation ID.
	 *
	 * @return 	boolean
	 */
	public function resmanage($id = null)
	{
		if (!$this->isEmployee())
		{
			return false;
		}

		// check if the employee is the owner
		if ($id > 0)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select(1)
				->from($dbo->qn('#__vikappointments_reservation'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $this->id,
					$dbo->qn('id') . ' = ' . (int) $id,
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				return false;
			}
		}

		return $this->config->getBool('empresmanage');
	}

	/**
	 * Checks if an employee can confirm existing reservations.
	 * If the reservation ID is provided, checks if the reservation is
	 * owned by the current employee.
	 *
	 * @param 	integer  $id 	The reservation ID.
	 *
	 * @return 	boolean
	 *
	 * @since 	1.6
	 */
	public function resconfirm($id = null)
	{
		if (!$this->isEmployee())
		{
			return false;
		}

		$status = null;

		// check if the employee is the owner
		if ($id > 0)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select($dbo->qn('status'))
				->from($dbo->qn('#__vikappointments_reservation'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $this->id,
					$dbo->qn('id') . ' = ' . (int) $id,
				));

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if (!$dbo->getNumRows())
			{
				return false;
			}

			// get the reservation status
			$status = $dbo->loadResult();
		}

		// reservations that are already confirmed are always allowed, 
		// even if the confirmation rule is turned off
		return $status == "CONFIRMED" || $this->config->getBool('empresconfirm');
	}
	
	/**
	 * Checks if an employee can remove existing reservations.
	 *
	 * @return 	boolean
	 */
	public function resremove()
	{
		return $this->isEmployee() && $this->config->getBool('empresremove');
	}

	/**
	 * Checks if a user can register a new account.
	 *
	 * @return 	boolean
	 */
	public function register()
	{
		return $this->config->getBool('empsignup');
	}
	
	/**
	 * Returns the default status of an employee after its registration.
	 *
	 * @return 	string
	 */
	public function getSignUpStatus()
	{
		return $this->config->getString('empsignstatus');
	}
	
	/**
	 * Returns the default user group assigned to the employee.
	 *
	 * @return 	integer
	 */
	public function getSignUpUserGroup()
	{
		return $this->config->getUint('empsignrule');
	}

	/**
	 * Returns the list of all the services to auto-assign to the employee.
	 *
	 * @return 	array
	 */
	public function getServicesToAssign()
	{
		$assign = $this->config->getString('empassignser', '');

		return array_filter(array_map('intval', explode(',', $assign)));
	}
	
	/**
	 * Returns the maximum number of services that an employee
	 * can own simultaneously.
	 *
	 * @return 	integer
	 */
	public function getServicesMaximumNumber()
	{
		return $this->config->getInt('empmaxser');
	}
	
	/**
	 * Checks if the administrator should be notified every
	 * time an employee removes a reservation.
	 *
	 * @return 	boolean
	 */
	public function isNotifyOnReservationDelete()
	{
		return $this->config->getBool('empresnotify');
	}
}
