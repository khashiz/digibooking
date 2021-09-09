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
 * Vikappointments back-end component helper.
 *
 * @since 1.0
 */
abstract class AppointmentsHelper
{
	/**
	 * Displays the main menu of the component.
	 *
	 * @return 	void
	 *
	 * @see 	printJoomla3810ter() it is needed to invoke also this method when the menu is displayed.
	 *
	 * @uses 	getParentTask()
	 * @uses 	getAuthorisations()
	 * @uses 	isLeftBoardMenuCompressed()
	 * @uses 	getCheckVersionParams()
	 */
	public static function printMenu()
	{
		$vik = UIApplication::getInstance();

		// load font awesome framework
		VikAppointments::load_font_awesome();

		$task = self::getParentTask();
		$auth = self::getAuthorisations();

		$base_href = 'index.php?option=com_vikappointments';

		// load menu factory
		UILoader::import('libraries.menu.factory');

		$board = MenuFactory::createMenu();

		///// DASHBOARD /////

		if ($auth['dashboard']['numactives'] > 0)
		{
			$parent = MenuFactory::createSeparator(JText::_('VAPMENUDASHBOARD'), $base_href, $task == 'vikappointments');

			$board->push($parent->setCustom('dashboard'));
		}

		///// MANAGEMENT /////

		if ($auth['management']['numactives'] > 0)
		{
			$parent = MenuFactory::createSeparator(JText::_('VAPMENUTITLEHEADER1'));

			if ($auth['management']['actions']['groups'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUGROUPS'), $base_href . '&task=groups', $task == 'groups');
				$parent->addChild($item->setCustom('th-list'));
			}

			if ($auth['management']['actions']['employees'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUEMPLOYEES'), $base_href . '&task=employees', $task == 'employees');
				$parent->addChild($item->setCustom('user-md'));
			}

			if ($auth['management']['actions']['services'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUSERVICES'), $base_href . '&task=services', $task == 'services');
				$parent->addChild($item->setCustom('flask'));
			}

			if ($auth['management']['actions']['options'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUOPTIONS'), $base_href . '&task=options', $task == 'options');
				$parent->addChild($item->setCustom('tags'));
			}

			if ($auth['management']['actions']['locations'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENULOCATIONS'), $base_href . '&task=locations', $task == 'locations');
				$parent->addChild($item->setCustom('map-marker'));
			}

			if ($auth['management']['actions']['packages'] && VikAppointments::isPackagesEnabled(true))
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUPACKAGES'), $base_href . '&task=packorders', $task == 'packorders');
				$parent->addChild($item->setCustom('gift'));
			}

			$board->push($parent->setCustom('briefcase'));
		}

		///// APPOINTMENTS /////

		if ($auth['appointments']['numactives'] > 0)
		{
			$parent = MenuFactory::createSeparator(JText::_('VAPMENUTITLEHEADER2'));

			if ($auth['appointments']['actions']['reservations'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENURESERVATIONS'), $base_href . '&task=reservations', $task == 'reservations');
				$parent->addChild($item->setCustom('shopping-basket'));
			}

			if ($auth['appointments']['actions']['waitinglist'] && VikAppointments::isWaitingList())
			{
				$item = MenuFactory::createItem(JText::_('VAPCONFIGGLOBTITLE14'), $base_href . '&task=waitinglist', $task == 'waitinglist');
				$parent->addChild($item->setCustom('hourglass'));
			}

			if ($auth['appointments']['actions']['customers'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUCUSTOMERS'), $base_href . '&task=customers', $task == 'customers');
				$parent->addChild($item->setCustom('user'));
			}

			if ($auth['appointments']['actions']['coupons'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUCOUPONS'), $base_href . '&task=coupons', $task == 'coupons');
				$parent->addChild($item->setCustom('gift'));
			}

			if ($auth['appointments']['actions']['calendar'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUCALENDAR'), $base_href . '&task=calendar', $task == 'calendar');
				$parent->addChild($item->setCustom('calendar'));
			}

			$board->push($parent->setCustom('shopping-cart'));
		}

		///// PORTAL /////

		if ($auth['appointments']['numactives'] > 0)
		{
			$parent = MenuFactory::createSeparator(JText::_('VAPMENUTITLEHEADER4'));

			if ($auth['portal']['actions']['countries'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUCOUNTRIES'), $base_href . '&task=countries', $task == 'countries');
				$parent->addChild($item->setCustom('map'));
			}

			if ($auth['portal']['actions']['reviews'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUREVIEWS'), $base_href . '&task=revs', $task == 'revs');
				$parent->addChild($item->setCustom('star'));
			}

			if ($auth['portal']['actions']['subscriptions'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUSUBSCRIPTIONS'), $base_href . '&task=subscriptions', $task == 'subscriptions');
				$parent->addChild($item->setCustom('ticket'));

				$item = MenuFactory::createItem(JText::_('VAPMENUSUBSCRIPTIONORDERS'), $base_href . '&task=subscrorders', $task == 'subscrorders');
				$parent->addChild($item->setCustom('shopping-basket'));
			}

			$board->push($parent->setCustom('certificate'));
		}

		///// GLOBAL /////

		if ($auth['appointments']['numactives'] > 0)
		{
			$parent = MenuFactory::createSeparator(JText::_('VAPMENUTITLEHEADER3'));

			if ($auth['global']['actions']['custfields'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUCUSTOMF'), $base_href . '&task=customf', $task == 'customf');
				$parent->addChild($item->setCustom('filter'));
			}

			if ($auth['global']['actions']['payments'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUPAYMENTS'), $base_href . '&task=payments', $task == 'payments');
				$parent->addChild($item->setCustom('credit-card-alt'));
			}

			if ($auth['global']['actions']['archive'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUARCHIVE'), $base_href . '&task=invfiles', $task == 'invfiles');
				$parent->addChild($item->setCustom('archive'));
			}

			if ($auth['global']['actions']['media'])
			{
				$item = MenuFactory::createItem(JText::_('VAPMENUMEDIA'), $base_href . '&task=media', $task == 'media');
				$parent->addChild($item->setCustom('camera'));
			}

			$board->push($parent->setCustom('globe'));
		}

		///// CONFIGURATION /////

		if ($auth['configuration']['numactives'] > 0)
		{
			$parent = MenuFactory::createSeparator(JText::_('VAPMENUCONFIG'), $base_href . '&task=editconfig', $task == 'editconfig');

			$item = MenuFactory::createItem(JText::_('VAPCONFIGTABNAME2'), $base_href . '&task=editconfigemp', $task == 'editconfigemp');
			$parent->addChild($item->setCustom('users'));

			$item = MenuFactory::createItem(JText::_('VAPCONFIGTABNAME3'), $base_href . '&task=editconfigcldays', $task == 'editconfigcldays');
			$parent->addChild($item->setCustom('calendar-times-o'));

			$item = MenuFactory::createItem(JText::_('VAPCONFIGTABNAME4'), $base_href . '&task=editconfigsmsapi', $task == 'editconfigsmsapi');
			$parent->addChild($item->setCustom('comment'));

			$item = MenuFactory::createItem(JText::_('VAPCONFIGTABNAME5'), $base_href . '&task=editconfigcron', $task == 'editconfigcron');
			$parent->addChild($item->setCustom('tasks'));

			$board->push($parent->setCustom('cogs'));
		}

		// CUSTOM
		$line_separator = MenuFactory::createCustomItem('line');

		// split
		$board->push($line_separator);
		$board->push(MenuFactory::createCustomItem('split'));

		// check version
		if ($auth['configuration']['numactives'] > 0)
		{
			/**
			 * Detect current platform and use the correct version button:
			 * - VikUpdater for Joomla
			 * - Go To PRO for WordPress
			 *
			 * @since 1.6.3
			 */
			if (VersionListener::getPlatform() == 'joomla')
			{
				if ($task == 'vikappointments' || $task == 'editconfig')
				{
					$board->push($line_separator);
					$board->push(MenuFactory::createCustomItem('version', self::getCheckVersionParams()));
				}
			}
			else if (VersionListener::getPlatform() == 'wordpress')
			{
				// always display license button
				$board->push(MenuFactory::createCustomItem('license'));
			}
		}
		
		///// BUILD MENU /////

		/**
		 * Trigger event to allow the plugins to manipulate the back-end menu of VikAppointments.
		 *
		 * @param 	MenuShape  &$menu 	The menu to build.
		 *
		 * @return 	void
		 *
		 * @since 	1.6.3
		 */
		UIFactory::getEventDispatcher()->trigger('onBeforeBuildVikAppointmentsMenu', array(&$board));

		echo $board->build();

		/**
		 * Open body by using the specific menu handler.
		 *
		 * @since 1.6.3
		 */
		echo $board->openBody();
	}

	/**
	 * Displays the footer of the component.
	 *
	 * @return 	void
	 *
	 * @see 	printMenu() it is needed to invoke also this method when the footer is displayed.
	 */
	public static function printJoomla3810ter()
	{
		/**
		 * Close body by using the specific menu handler.
		 *
		 * @since 1.6.3
		 */
		echo MenuFactory::createMenu()->closeBody();
		
		if (VikAppointments::isJoomla3810terVisible())
		{
			/**
			 * Find manufacturer name according to the platform in use.
			 * Display a link in the format [SHORT] - [LONG].
			 *
			 * @since 1.6.3
			 */
			$manufacturer = UIApplication::getInstance()
				->getManufacturer(array('link' => true, 'short' => true, 'long' => true));

			?>
			<p id="vapfooter">
				<?php echo JText::sprintf('VAPJOOMLA3810TER', VIKAPPOINTMENTS_SOFTWARE_VERSION) . ' ' . $manufacturer; ?>
			</p>
			<?php
		}
	}

	/**
	 * Returns the parent to which the task belongs.
	 * For example, if we are visiting the states list,
	 * the parent will be "countries", as the states don't
	 * have a specific menu item.
	 *
	 * @param 	string 	$task 	The task to check.
	 * 							If empty, it will be taken from the request.
	 *
	 * @return 	void
	 */
	public static function getParentTask($task = null)
	{
		$input = JFactory::getApplication()->input;

		if ($task === null)
		{
			$task = $input->get('view');

			/**
			 * Fallback to &task in case &view was not set.
			 *
			 * @since 1.6.3
			 */
			if (empty($task))
			{
				$task = $input->get('task');
			}
		}
		
		if (empty($task))
		{
			$task = 'vikappointments';
		}

		switch ($task)
		{
			case 'serworkdays':
			case 'rates':
				$task = 'services';
				break;
			
			case 'emppayments':
			case 'emprates':
			case 'emplocations':
				$task = 'employees';
				break;
			
			case 'packages':
			case 'packgroups':
				$task = 'packorders';
				break;
			
			case 'states':
			case 'cities':
				$task = 'countries';
				break;

			case 'coupongroups':
				$task = 'coupons';
				break;

			case 'caldays':
				$task = 'calendar';
				break;

			case 'import':
				// recursive search to make sure we select the correct parent
				$task = self::getParentTask($input->getString('import_type', ''));
				break;
		}

		return $task;
	}

	/**
	 * Checks if the left-board menu is compressed or not.
	 *
	 * @return 	boolean  True if compressed, false otherwise.
	 */
	public static function isLeftBoardMenuCompressed()
	{
		return (bool) (UIFactory::getConfig()->getUint('mainmenustatus', 1) == 2);
	}

	/**
	 * Returns the arguments used to display a link to check the version.
	 *
	 * @return 	array
	 */
	protected static function getCheckVersionParams()
	{
		$data = array(
			'hn'  => getenv('HTTP_HOST'),
			'sn'  => getenv('SERVER_NAME'),
			'app' => CREATIVIKAPP,
			'ver' => VIKAPPOINTMENTS_SOFTWARE_VERSION,
		);

		return array(
			'url' 	=> 'https://extensionsforjoomla.com/vikcheck/?' . http_build_query($data),
			'label' => 'Check Updates',
		);
	}
	
	/**
	 * Loads the base CSS and JS resources.
	 *
	 * @return 	void
	 */
	public static function load_css_js()
	{
		$doc = JFactory::getDocument();
		$vik = UIApplication::getInstance();
		
		$vik->loadFramework('jquery.framework');

		$options = array(
			'version' => VIKAPPOINTMENTS_SOFTWARE_VERSION,
		);
		
		/**
		 * Do not load jQuery again.
		 *
		 * @since 1.6.2
		 */
		// $vik->addScript(VAPASSETS_URI . 'js/jquery-1.11.1.min.js');
		$vik->addScript(VAPASSETS_URI . 'js/jquery-ui-1.11.1.min.js');
		$vik->addScript(VAPASSETS_URI . 'js/jquery-ui.sortable.min.js');
		$doc->addStyleSheet(VAPASSETS_URI . 'css/jquery-ui.min.css');
		
		$vik->addScript(VAPASSETS_ADMIN_URI . 'js/vikappointments.js', $options);
		VikAppointments::load_utils($options);
		
		$doc->addStyleSheet(VAPASSETS_ADMIN_URI . 'css/vikappointments.css', $options);
		$doc->addStyleSheet(VAPASSETS_ADMIN_URI . 'css/vap-admin.css', $options);
	}

	/**
	 * Loads the stylesheets needed to use Font Awesome.
	 *
	 * @param 	boolean  $fix 	True to fix the menu padding.
	 *
	 * @return 	void
	 *
	 * @deprecated 	1.7  Use VikAppointments::load_font_awesome() instead.
	 * @deprecated 	1.7  Use UIApplication::fixContentPadding() externally.
	 */
	public static function load_font_awesome($fix = false)
	{
		$vik = UIApplication::getInstance();

		VikAppointments::load_font_awesome();

		if ($fix)
		{
			$vik->fixContentPadding($doc);
		}
	}
	
	/**
	 * Loads the scripts needed to use Fancybox jQuery plugin.
	 *
	 * @return 	void
	 *
	 * @deprecated 	1.7  Use VikAppointments::load_fancybox() instead.
	 */
	public static function load_fancybox()
	{
		VikAppointments::load_fancybox();
	}

	/**
	 * Validates the specified group arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateGroup($args)
	{
		return self::validator($args, array('name'));
	}

	/**
	 * Validates the specified service arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateService($args)
	{
		return self::validator($args, array('name', 'alias', 'duration', 'price'));
	}
	
	/**
	 * Validates the specified employee arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateEmployee($args)
	{
		return self::validator($args, array('firstname', 'lastname', 'nickname', 'alias'));
	}
	
	/**
	 * Validates the specified option arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateOption($args)
	{
		return self::validator($args, array('name', 'price'));
	}

	/**
	 * Validates the specified package arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validatePackage($args)
	{
		return self::validator($args, array('name'));
	}

	/**
	 * Validates the specified package group arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validatePackageGroup($args)
	{
		return self::validator($args, array('title'));
	}

	/**
	 * Validates the specified package order arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validatePackageOrder($args)
	{
		return self::validator($args, array('id_user'));
	}

	/**
	 * Validates the specified waiting user arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateWaiting($args)
	{
		return self::validator($args, array('id_service', 'timestamp'));
	}
	
	/**
	 * Validates the specified customer arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateCustomer($args)
	{
		return self::validator($args, array('billing_name', 'billing_mail'));
	}
	
	/**
	 * Validates the specified coupon arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateCoupon($args)
	{
		return self::validator($args, array('code'));
	}

	/**
	 * Validates the specified payment arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validatePayment($args)
	{
		return self::validator($args, array('name', 'file'));
	}
	
	/**
	 * Validates the specified custom field arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateCustomf($args)
	{
		return self::validator($args, array('name', 'type'));
	}
	
	/**
	 * Validates the specified country arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateCountry($args)
	{
		return self::validator($args, array('country_name', 'country_2_code', 'country_3_code', 'phone_prefix'));
	}
	
	/**
	 * Validates the specified state arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateState($args)
	{
		return self::validator($args, array('state_name', 'state_2_code'));
	}
	
	/**
	 * Validates the specified city arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateCity($args)
	{
		return self::validator($args, array('city_name', 'city_2_code'));
	}
	
	/**
	 * Validates the specified review arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateReview($args)
	{
		return self::validator($args, array('title', 'jid'));
	}
	
	/**
	 * Validates the specified subscription arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateSubscription($args)
	{
		return self::validator($args, array('name'));
	}
	
	/**
	 * Validates the specified subscription order arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateSubscriptionOrder($args)
	{
		return self::validator($args, array('id_subscr', 'id_employee'));
	}
	
	/**
	 * Validates the specified employee location arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateEmployeeLocation($args)
	{
		return self::validator($args, array('name', 'id_employee', 'id_country', 'address'));
	}
	
	/**
	 * Validates the specified cron job arguments.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	validator()
	 */
	public static function validateCronjob($args)
	{
		return self::validator($args, array('name', 'class'));
	}

	/**
	 * Validates the specified record arguments according to the required fields.
	 *
	 * @param 	array 	 $args 	The associative array to check.
	 * @param 	array 	 $req 	The array containing the required fields.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 */
	protected static function validator(array $args, array $req = array())
	{
		$keys = array();

		foreach ($req as $key)
		{
			if (!array_key_exists($key, $args) || is_null($args[$key]) || !strlen($args[$key]))
			{
				$keys[] = $key;
			}
		}
		
		return $keys;
	}
	
	/**
	 * Returns a list containing all the media files.
	 *
	 * @param 	boolean  $order_by_creation
	 *
	 * @return 	array
	 */
	public static function getAllMedia($order_by_creation = false)
	{
		// $arr = glob(VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . '*.{png,jpg}', GLOB_BRACE);

		// since certain server configurations may not support GLOB_BRACE mask,
		// we need to filter the list manually
		$arr = glob(VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . '*');

		$arr = array_filter($arr, function($path)
		{
			return preg_match("/.*\.(png|jpe?g|gif|bmp)$/i", $path);
		});
		
		if ($order_by_creation)
		{
			/**
			 * Replaced create_function with anonymous function
			 * as it has been declared deprecated since PHP 7.2.
			 *
			 * In case of PHP 5.2 or lower, this code may raise a fatal
			 * error as this version doesn't support yet anonymous functions.
			 *
			 * @since 1.6.1
			 */
			usort($arr, function($a, $b)
			{
				return filemtime($b) - filemtime($a);
			});
		}

		return $arr;
	}
	
	/**
	 * Returns the list of the invoices related to the specified group.
	 *
	 * @param 	string 	 $group  The invoices group.	 
	 * @param 	boolean  $sort 	 True to sort the invoices by name.
	 *
	 * @return 	array 	 The invoices list.
	 */
	public static function getAllInvoices($group = '', $sort = false)
	{
		$parts = array(
			VAPINVOICE,
		);

		if ($group)
		{
			$parts[] = $group;
		}

		$parts[] = '*.pdf';

		$arr = glob(implode(DIRECTORY_SEPARATOR, $parts));
		
		if ($sort)
		{
			sort($arr);
		}

		return $arr;
	}
	
	/**
	 * Returns an associative array containing the properties of the specified file:
	 * - size
	 * - creation
	 *
	 * @param 	string 	$file  The file path.
	 * @param 	array 	$attr  The attributes to use (e.g. dateformat for creation).
	 *
	 * @return 	array
	 *
	 * @uses 	getDefaultFileAttributes()
	 */
	public static function getFileProperties($file, $attr = array())
	{
		$attr = self::getDefaultFileAttributes($attr);
		
		$file_prop = array(
			'size' 		=> filesize($file),
			'creation' 	=> date($attr['dateformat'], filemtime($file)),
		);

		$file_prop['size'] = JHtml::_('number.bytes', $file_prop['size'], 'auto', 0);
		
		return $file_prop;
	}
	
	/**
	 * Returns the default file attributes in case they were not provided:
	 * - dateformat
	 *
	 * @param 	array 	$attr  The attributes to check.
	 *
	 * @return 	array 	The filled attributes.
	 */
	private static function getDefaultFileAttributes($attr = array())
	{
		if (empty($attr['dateformat']))
		{
			$attr['dateformat'] = UIFactory::getConfig()->get('dateformat');
		}

		return $attr;
	}
	
	/**
	 * Creates a dropdown containing all the media files.
	 *
	 * @param 	mixed 	 $value 		The selected value.
	 * @param 	boolean  $first_null 	True to insert an empty value as first option.
	 * @param 	array 	 $prop 			An associative array of input properties.
	 *
	 * @return 	string 	 The HTML of the dropdown.
	 *
	 * @uses 	getAllMedia()
	 */
	public static function composeMediaSelect($value = null, $first_null = true, array $prop = array())
	{
		if (!isset($prop['name']))
		{
			$prop['name'] = 'media';
		}

		$prop_str = '';
		foreach ($prop as $k => $v)
		{
			$prop_str .= ' ' . $k . (!empty($v) ? '="' . htmlspecialchars($v) . '"' : '');
		}

		$options = array();
		
		if ($first_null)
		{
			$options[] = JHtml::_('select.option', '', '');
		}

		foreach (self::getAllMedia() as $img)
		{
			$img = basename($img);
			
			$options[] = JHtml::_('select.option', $img, $img);
		}

		$selector = isset($prop['id']) ? '#' . $prop['id'] : 'input[name="' . $prop['name'] . '"]';

		JFactory::getDocument()->addScriptDeclaration("jQuery(document).ready(function() {
			jQuery('$selector').select2({
				placeholder: '" . (!$first_null ? addslashes(JText::_('VAPFIRSTIMAGENULL')) : '--') . "',
				allowClear: true,
				width: 300
			});
		});");
		
		return '<select' . $prop_str . '>' . JHtml::_('select.options', $options, 'value', 'text', $value) . '</select>';
	}
	
	/**
	 * Returns an associative array containing the authorisations used
	 * to check which views can be visited by the logged-in user.
	 *
	 * @return 	array
	 */
	public static function getAuthorisations()
	{	
		$rules = array(
			'dashboard' 	=> array(
				'actions' 		=> array('dashboard' => 0),
				'numactives' 	=> 0,
			),
			'management' 	=> array( 
				'actions' 		=> array('employees' => 0, 'groups' => 0, 'services' => 0, 'options' => 0, 'locations' => 0, 'packages' => 0),
				'numactives' 	=> 0,
			),
			'appointments' 	=> array( 
				'actions' 		=> array('reservations' => 0, 'waitinglist' => 0, 'customers' => 0, 'coupons' => 0, 'calendar' => 0),
				'numactives' 	=> 0,
			),
			'portal' 		=> array( 
				'actions' 		=> array('countries' => 0, 'reviews' => 0, 'subscriptions' => 0),
				'numactives' 	=> 0,
			), 
			'global' 		=> array( 
				'actions' 		=> array('custfields' => 0, 'payments' => 0, 'archive' => 0, 'media' => 0),
				'numactives' 	=> 0,
			),
			'configuration' => array(
				'actions' 		=> array('config' => 0),
				'numactives'	=> 0,
			),
		);
		
		$user = JFactory::getUser();
		
		foreach ($rules as $group => $rule)
		{
			foreach ($rule['actions'] as $action => $val)
			{
				$rules[$group]['actions'][$action] = $user->authorise("core.access.$action", "com_vikappointments");
				
				if ($rules[$group]['actions'][$action])
				{
					$rules[$group]['numactives']++;
				}
			}
		}
		
		return $rules;
	}

	/**
	 * Register a new Joomla user with the details
	 * specified in the given $args array.
	 *
	 * All the restrictions specified in com_users
	 * component are always bypassed.
	 *
	 * @param 	array 	$args 	The user details.
	 *
	 * @return 	mixed 	The ID of the user on success, otherwise false.
	 */
	public static function createNewJoomlaUser($args)
	{
		jimport('joomla.application.component.helper');
		$params = JComponentHelper::getParams('com_users');

		$vik = UIApplication::getInstance();

		$user = new JUser;
		$data = array();

		if (empty($args['usertype']))
		{
			$groups = array($params->get('new_usertype', 2));
		}
		else
		{
			if (is_array($args['usertype']))
			{
				$groups = $args['usertype'];
			}
			else
			{
				$groups = array((string) $args['usertype']);
			}
		}

		if (empty($args['user_username']))
		{
			// empty username, use the specified name
			$args['user_username'] = $args['user_name'];
		}

		// get the default new user group, Registered if not specified
		$data['groups'] 	= $groups;
		$data['name'] 		= $args['user_name'];
		$data['username'] 	= $args['user_username'];
		$data['email'] 		= $vik->emailToPunycode($args['user_mail']);
		$data['password'] 	= $args['user_pwd1'];
		$data['password2']	= $args['user_pwd2'];
		$data['sendEmail'] 	= 0;
		
		if (!$user->bind($data))
		{
			return false;
		}

		if (!$user->save())
		{
			return false;
		}

		return $user->id;
	}

	/**
	 * Removes the credit card details for the order that have been CONFIRMED
	 * and don't need them anymore.
	 *
	 * @param 	boolean  $force  Flag used to skip the waiting check.
	 * @param 	integer  $wait 	 The number of minutes to wait between each check.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	public static function removeExpiredCreditCards($force = false, $wait = 30)
	{
		$dbo 	 = JFactory::getDbo();
		$session = JFactory::getSession();

		$now = time();

		// if doesn't exist, get a time in the past
		$check = (int) $session->get('cc-flush-check', $now - 3600, 'vikappointments');

		if ($force || $check < $now)
		{
			// the reservations must have a checkin date previous than yesterday
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_reservation'))
				->set($dbo->qn('cc_data') . ' = ' . $dbo->q(''))
				->where($dbo->qn('checkin_ts') . ' < ' . ($now - 86400));

			$dbo->setQuery($q);
			$dbo->execute();

			// The packages must be confirmed and the creation date 
			// should be at least one week old. Otherwise remove them
			// after one month since the creation date.
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_package_order'))
				->set($dbo->qn('cc_data') . ' = ' . $dbo->q(''))
				->where(array(
					$dbo->qn('status') . ' = ' . $dbo->q('CONFIRMED'),
					$dbo->qn('createdon') . ' < ' . ($now - 86400 * 7),
				))
				->orWhere($dbo->qn('createdon') . ' < ' . ($now - 86400 * 30));

			$dbo->setQuery($q);
			$dbo->execute();

			// The subscriptions must be confirmed and the creation date 
			// should be at least one week old. Otherwise remove them
			// after one month since the creation date.
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_subscr_order'))
				->set($dbo->qn('cc_data') . ' = ' . $dbo->q(''))
				->where(array(
					$dbo->qn('status') . ' = ' . $dbo->q('CONFIRMED'),
					$dbo->qn('createdon') . ' < ' . ($now - 86400 * 7),
				))
				->orWhere($dbo->qn('createdon') . ' < ' . ($now - 86400 * 30));

			$dbo->setQuery($q);
			$dbo->execute();

			// update time for next check
			$session->set('cc-flush-check', time() + $wait * 60, 'vikappointments');
		}
	}

	/**
	 * Updates the extra fields of VikAppointments to let them
	 * be sent to our servers during Joomla! updates.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	public static function registerUpdaterFields()
	{
		// make sure the Joomla version is 3.2.0 or higher
		// otherwise the extra_fields wouldn't be available
		$jv = new JVersion();
		if (version_compare($jv->getShortVersion(), '3.2.0', '<'))
		{
			// stop to avoid fatal errors
			return;
		}

		$config = UIFactory::getConfig();
		$extra_fields = $config->getInt('update_extra_fields', 0);	

		if ($extra_fields > time())
		{
			// not needed to rewrite extra fields
			return;
		}

		// get current domain
		$server = JFactory::getApplication()->input->server;
		$domain = base64_encode($server->getString('HTTP_HOST'));
		$ip 	= $server->getString('REMOTE_ADDR');

		// import url update handler
		UILoader::import('libraries.update.urihandler');

		$update = new UriUpdateHandler('com_vikappointments');

		$update->addExtraField('domain', $domain)
			->addExtraField('ip', $ip)
			->register();

		// validate schema version
		$update->checkSchema($config->get('version'));

		// rewrite extra fields next week
		$config->set('update_extra_fields', time() + 7 * 86400);
	}

	/**
	 * Get the actions.
	 *
	 * @param 	integer  $id
	 *
	 * @return 	object
	 */
	public static function getActions($id = 0)
	{
		jimport('joomla.access.access');

		$user 	= JFactory::getUser();
		$result = new stdClass;

		if (empty($id))
		{
			$assetName = 'com_vikappointments';
		}
		else
		{
			$assetName = 'com_vikappointments.message.' . (int) $id;
		}

		$actions = JAccess::getActions('com_vikappointments', 'component');

		foreach ($actions as $action)
		{
			$result->{$action->name} = $user->authorise($action->name, $assetName);
		};

		return $result;
	}
}

if (!class_exists('OrderingManager'))
{
	/**
	 * Helper class used to handle lists ordering.
	 *
	 * @since 1.0
	 */
	class OrderingManager
	{
		/**
		 * The component name.
		 *
		 * @var string
		 */
		protected static $option = 'com_vikappointments';

		/**
		 * The value in query string that will be used to 
		 * recover the selected ordering column.
		 *
		 * @var string
		 */
		protected static $columnKey = 'vapordcolumn';

		/**
		 * The value in query string that will be used to 
		 * recover the selected ordering direction.
		 *
		 * @var string
		 */
		protected static $typeKey = 'vapordtype';
		
		/**
		 * Class constructor.
		 */
		protected function __construct()
		{
			// not accessible
		}

		/**
		 * Prepares the class with custom configuration.
		 *
		 * @param 	string 	$option
		 * @param 	string 	$column
		 * @param 	string 	$type
		 *
		 * @return 	void
		 */
		public static function getInstance($option = '', $column = '', $type = '')
		{
			if (!empty($option))
			{
				self::$option = $option;
			}

			if (!empty($column))
			{
				self::$columnKey = $column;
			}

			if (!empty($type))
			{
				self::$typeKey = $type;
			}
		}
		
		/**
		 * Returns the link that will be used to sort the column.
		 *
		 * @param 	string 	$task 			The task to reach after clicking the link.
		 * @param 	string 	$text 			The link text.
		 * @param 	string 	$col 			The column to sort.
		 * @param 	string 	$type 			The new direction value (1 ASC, 2 DESC).
		 * @param 	string 	$def_type 		The default direction if $type is empty.
		 * @param 	array 	$params 		An associative array with addition params to include in the URL-
		 * @param 	string 	$active_class 	The class used in case of active link.
		 *
		 * @return 	string 	The HTML of the link.
		 */
		public static function getLinkColumnOrder($task, $text, $col, $type = '', $def_type = '', $params = array(), $active_class = '')
		{
			if (empty($type))
			{
				$type 			= $def_type;
				$active_class 	= '';
			}

			if (!is_array($params))
			{
				if (empty($params))
				{
					$params = array();
				}
				else
				{
					$params = array($params);
				}
			}

			// inject URL vars in $params array
			$params['option'] 			= self::$option;
			$params['task']				= $task;
			$params[self::$columnKey] 	= $col;
			$params[self::$typeKey] 	= $type;

			$href = 'index.php?' . http_build_query($params);
			
			return '<a class="' . $active_class . '" href="' . $href . '">' . $text . '</a>';
		}
		
		/**
		 * Returns the ordering details for the specified values.
		 *
		 * @param 	string 	$task 		The task where we are.
		 * @param 	string 	$def_col 	The default column to sort.
		 * @param 	string 	$def_type 	The default ordering direction.
		 *
		 * @return 	array 	An associative array containing the ordering column and direction.
		 */
		public static function getColumnToOrder($task, $def_col = 'id', $def_type = 1)
		{
			$app = JFactory::getApplication();

			$col 	= $app->getUserStateFromRequest(self::$columnKey . "[$task]", self::$columnKey, $def_col, 'string');
			$type 	= $app->getUserStateFromRequest(self::$typeKey . "[$task]", self::$typeKey, $def_type, 'uint');
			
			return array('column' => $col, 'type' => $type);
		}
		
		/**
		 * Returns the ordering direction, based on the current one.
		 *
		 * @param 	string 	$task 		The task where we are.
		 * @param 	string 	$col 		The column we need to alter.
		 * @param 	string 	$curr_type 	The current direction.
		 *
		 * @return 	string  The new direction value.
		 */
		public static function getSwitchColumnType($task, $col, $curr_type)
		{
			$stored = JFactory::getApplication()->getUserStateFromRequest(self::$columnKey . "[$task]", self::$columnKey, '', 'string');
			
			$types = array(1, 2);

			if ($stored == $col)
			{
				$index = array_search($curr_type, $types);

				if ($index >= 0)
				{
					return $types[($index + 1) % 2];
				}
			} 
			
			return end($types);
		}
	}
}
