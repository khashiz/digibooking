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
 * VikAppointments component helper class.
 *
 * @since 	1.0
 */
abstract class VikAppointments
{
	/**
	 * Returns the agency name.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getAgencyName()
	{
		return self::getFieldFromConfig('agencyname', 'vapGetAgencyName');
	}
	
	/**
	 * Checks if the system supports multi-lingual contents.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isMultilanguage()
	{
		return (int) self::getFieldFromConfig('ismultilang', 'vapIsMultilanguage');
	}
	
	/**
	 * Returns a list containing all the administrator e-mails.
	 *
	 * @return 	array
	 */
	public static function getAdminMailList()
	{
		$admin_mail_list = self::getFieldFromConfig('adminemail', 'vapGetAdminMail');

		if (!strlen($admin_mail_list))
		{
			return array();
		}

		return array_map('trim', explode(',', $admin_mail_list));
	} 
	
	/**
	 * Returns the administrator e-mail.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getAdminMail()
	{
		return self::getFieldFromConfig( 'adminemail', 'vapGetAdminMail');
	} 
	
	/**
	 * Returns the sender e-mail. If not provided, the first one 
	 * specified for the admin e-mail field will be used.
	 *
	 * @return 	string
	 */
	public static function getSenderMail()
	{
		$sender = self::getFieldFromConfig('senderemail', 'vapGetSenderMail');

		if (empty($sender))
		{
			// no sender, get the admin e-mail list
			$list = self::getAdminMailList();

			if (count($list))
			{
				// get the first admin e-mail as sender
				$sender = $list[0];
			}
		}

		return $sender;
	} 
	
	/**
	 * Returns the company logo image name.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCompanyLogoPath()
	{
		return self::getFieldFromConfig('companylogo', 'vapGetCompanyLogo');
	}
	
	/**
	 * Returns the file name to attach within the e-mail for customers (if any).
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getMailAttachment()
	{
		return self::getFieldFromConfig('mailattach', 'vapGetMailAttachment');
	}
	
	/**
	 * Returns the file path to attach within the e-mail for customers (if any).
	 *
	 * @return 	string
	 */
	public static function getMailAttachmentURL()
	{
		$attach = self::getMailAttachment();

		if (empty($attach))
		{
			return '';
		}

		return VAPHELPERS . DIRECTORY_SEPARATOR . 'mail_attach' . DIRECTORY_SEPARATOR . $attach;
	}
	
	/**
	 * Returns an array containing the e-mail sending rules.
	 * The array contains the rules for these entities: customer, employee, admin.
	 *
	 * @return 	array
	 */
	public static function getSendMailWhen()
	{
		return array(
			"customer" 	=> (int) self::getFieldFromConfig('mailcustwhen', 'vapGetSendMailCustomerWhen'),
			"employee" 	=> (int) self::getFieldFromConfig('mailempwhen', 'vapGetSendMailEmployeeWhen'),
			"admin" 	=> (int) self::getFieldFromConfig('mailadminwhen', 'vapGetSendMailAdminWhen'),
		);
	}

	/**
	 * Returns an array containing the rules to attach the ICS file within the e-mail.
	 * The array contains the rules for these entities: customer, employee, admin.
	 *
	 * @return 	array
	 */
	public static function getAttachmentPropertiesICS()
	{
		$str = self::getFieldFromConfig('icsattach', 'vapGetAttachmentICS');
		$ics = explode(';', $str);

		return array(
			"customer" 	=> $ics[0],
			"employee" 	=> $ics[1],
			"admin" 	=> $ics[2],
		);
	}
	
	/**
	 * Returns an array containing the rules to attach the CSV file within the e-mail.
	 * The array contains the rules for these entities: customer, employee, admin.
	 *
	 * @return 	array
	 */
	public static function getAttachmentPropertiesCSV()
	{
		$str = self::getFieldFromConfig('csvattach', 'vapGetAttachmentCSV');
		$csv = explode(';', $str);

		return array(
			"customer" 	=> $csv[0],
			"employee" 	=> $csv[1],
			"admin" 	=> $csv[2],
		);
	}

	/**
	 * Returns the file name of the e-mail template for the customers.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getMailTemplateName()
	{
		return self::getFieldFromConfig('mailtmpl', 'vapGetMailTemplateName');
	}

	/**
	 * Returns the file name of the e-mail template for the administrator(s).
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getAdminMailTemplateName()
	{
		return self::getFieldFromConfig('adminmailtmpl', 'vapGetAdminMailTemplateName');
	}

	/**
	 * Returns the file name of the e-mail template for the employees.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getEmployeeMailTemplateName()
	{
		return self::getFieldFromConfig('empmailtmpl', 'vapGetEmployeeMailTemplateName');
	}

	/**
	 * Returns the file name of the e-mail template for the cancelled orders.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCancellationMailTemplateName()
	{
		return self::getFieldFromConfig('cancmailtmpl', 'vapGetCancellationMailTemplateName');
	}

	/**
	 * Returns the file name of the e-mail template for the packages.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getPackagesMailTemplateName()
	{
		return self::getFieldFromConfig('packmailtmpl', 'vapGetPackagesMailTemplateName');
	}

	/**
	 * Returns the file name of the e-mail template for the waiting customers.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getWaitListMailTemplateName()
	{
		return self::getFieldFromConfig('waitlistmailtmpl', 'vapGetWaitListMailTemplateName');
	}
	
	/**
	 * Returns the global date format.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getDateFormat()
	{
		return self::getFieldFromConfig('dateformat', 'vapGetDateFormat');
	}
	
	/**
	 * Returns the global time format.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getTimeFormat()
	{
		return self::getFieldFromConfig('timeformat', 'vapGetTimeFormat');
	}
	
	/**
	 * Checks if the duration should be formatted to the closest unit.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isDurationToFormat()
	{
		return (int) self::getFieldFromConfig('formatduration', 'vapGetDurationToFormat');
	}
	
	/**
	 * Returns the global currency symbol.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCurrencySymb()
	{
		return self::getFieldFromConfig('currencysymb', 'vapGetCurrencySymb');
	}
	
	/**
	 * Returns the global currency name. It must be a value
	 * allowed by the ISO 4217.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCurrencyName()
	{
		return self::getFieldFromConfig('currencyname', 'vapGetCurrencyName');
	}
	
	/**
	 * Returns the position of the currency symbol:
	 * - [1] after the price
	 * - [2] before the price
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCurrencySymbPosition()
	{
		return (int) self::getFieldFromConfig('currsymbpos', 'vapGetCurrencySymbPosition');
	}

	/**
	 * Returns the decimal separator to use for prices.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCurrencyDecimalSeparator()
	{
		return self::getFieldFromConfig('currdecimalsep', 'vapGetCurrencyDecimalSeparator');
	}

	/**
	 * Returns the thousands separator to use for prices.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCurrencyThousandsSeparator()
	{
		return self::getFieldFromConfig('currthousandssep', 'vapGetCurrencyThousandsSeparator');
	}

	/**
	 * Returns the number of decimals to use for prices.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCurrencyDecimalDigits()
	{
		return (int) self::getFieldFromConfig('currdecimaldig', 'vapGetCurrencyDecimalDigits');
	}
	
	/**
	 * Checks if the system should allow the selection of the phone prefix.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isShowPhonesPrefix()
	{
		return (int) self::getFieldFromConfig('showphprefix', 'vapGetShowPhonesPrefix');
	}
	
	/**
	 * Returns the minimum amount to apply the deposit feature.
	 *
	 * @return 	floatval
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getDepositAfterAmount()
	{
		return (float) self::getFieldFromConfig('depositafter', 'vapGetDepositAfterAmount');
	}
	
	/**
	 * Returns the amount that will be used as deposit.
	 *
	 * @return 	floatval
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getDepositValue()
	{
		return (float) self::getFieldFromConfig('depositvalue', 'vapGetDepositValue');
	}
	
	/**
	 * Returns the deposit type:
	 * - [1] percentage
	 * - [2] total value
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getDepositType()
	{
		return (int) self::getFieldFromConfig('deposittype', 'vapGetDepositType');
	}

	/**
	 * Returns the default status to use in case of missing payment gateways.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getDefaultStatus()
	{
		return self::getFieldFromConfig('defstatus', 'vapGetDefaultStatus');
	}
	
	/**
	 * Returns the global minutes intervals.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getMinuteIntervals()
	{
		return (int) self::getFieldFromConfig('minuteintervals', 'vapGetMinuteIntervals');
	}

	/**
	 * Returns an array containing the opening hours and minutes.
	 *
	 * @return 	array
	 */
	public static function getOpeningTime()
	{
		$op = self::getFieldFromConfig('openingtime', 'vapGetOpeningTime');
		$op = explode(':', $op);

		return array(
			'hour' => (int) $op[0],
			'min'  => (int) $op[1],
		);
	}
	
	/**
	 * Returns an array containing the closing hours and minutes.
	 *
	 * @return 	array
	 */
	public static function getClosingTime()
	{
		$cl = self::getFieldFromConfig('closingtime', 'vapGetClosingTime');
		$cl = explode(':', $cl);

		return array(
			'hour' => (int) $cl[0],
			'min'  => (int) $cl[1],
		);
	}
	
	/**
	 * Returns the maximum number of employees to display per page.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getEmployeesListLimit()
	{
		return (int) self::getFieldFromConfig('emplistlim', 'vapGetEmployeesListLimit');
	}

	/**
	 * Returns an array containing all the available ways to sort the employees.
	 *
	 * @return 	array
	 */
	public static function getEmployeesAvailableOrderings()
	{
		$ways = json_decode(self::getFieldFromConfig('emplistmode', 'vapGetEmployeesListingMode'), true);
		$arr  = array();

		foreach ($ways as $i => $v)
		{
			if ($v == 1)
			{
				$arr[] = $i;
			}
		}

		if (!count($arr))
		{
			// always allow the default ordering (a..Z)
			$arr[0] = 1;
		}

		return $arr;
	}

	/**
	 * Returns the default ordering to use to list the employees.
	 *
	 * @return 	string
	 */
	public static function getEmployeesListingMode()
	{
		$arr = self::getEmployeesAvailableOrderings();

		return $arr[0];
	}

	/**
	 * Returns the configuration array containing the listing details
	 * of the employees. The array will contain the following keys:
	 *
	 * @property 	integer  desclength 		The maximum number of characters.
	 * @property 	integer  linkhref 			The event to use when clicking the image.
	 * @property 	integer  filtergroups 		Whether the group filtering is enabled or not.
	 * @property 	integer  filterordering 	Whether the ordering selection is enabled or not.
	 * @property 	integer  ajaxsearch 		The type of AJAX search.
	 *
	 * @return 	array 	An associative array.
	 */
	public static function getEmployeesListingDetails()
	{
		$config = UIFactory::getConfig();

		return array( 
			"desclength" 		=> $config->getInt('empdesclength'),
			"linkhref" 			=> $config->getInt('emplinkhref'),
			"filtergroups" 		=> $config->getInt('empgroupfilter'),
			"filterordering" 	=> $config->getInt('empordfilter'),
			"ajaxsearch" 		=> $config->getInt('empajaxsearch'),
		);
	}
	
	/**
	 * Returns the configuration array containing the listing details
	 * of the services. The array will contain the following keys:
	 *
	 * @property 	integer  desclength  The maximum number of characters.
	 * @property 	integer  linkhref 	 The event to use when clicking the image.
	 *
	 * @return 	array 	An associative array.
	 */
	public static function getServicesListingDetails()
	{
		return array( 
			"desclength" => (int) self::getFieldFromConfig('serdesclength', 'vapGetServicesDescriptionLength'),
			"linkhref" 	 => (int) self::getFieldFromConfig('serlinkhref', 'vapGetServicesLinkHref'),
		);
	}
	
	/**
	 * Returns the number of months to display per time within the calendar.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getNumberOfCalendars()
	{
		return (int) self::getFieldFromConfig('numcals', 'vapGetNumberOfCalendars');
	}

	/**
	 * Returns the maximum number of months (in advance) that can be selected.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getNumberOfMonths()
	{
		return (int) self::getFieldFromConfig('nummonths', 'vapGetNumberOfMonths');
	}

	/**
	 * Returns the first week day.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCalendarFirstWeekDay()
	{
		return (int) self::getFieldFromConfig('firstday', 'vapGetCalendarFirstWeekDay');
	}
	
	/**
	 * Returns the first month of the calendar to display (used only if the month is not in the past).
	 *
	 * @return 	integer
	 *
	 * @see 	getCalendarFirstYear()
	 */
	public static function getCalendarFirstMonth()
	{
		return (int) self::getFieldFromConfig('calsfrom', 'vapGetCalendarFirstMonth');
	}
	
	/**
	 * Returns the year to which the first month is referring to.
	 *
	 * @return 	integer
	 *
	 * @see 	getCalendarFirstMonth()
	 */
	public static function getCalendarFirstYear()
	{
		$year = (int) self::getFieldFromConfig('calsfromyear', 'vapGetCalendarFirstYear');
		
		if (!$year)
		{
			// return current year
			$arr  = ArasJoomlaVikApp::jgetdate();
			$year = $arr['year'];
		}

		return $year;
	}

	/**
	 * Checks if the calendar legend should be displayed.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isCalendarLegendVisible()
	{
		return (int) self::getFieldFromConfig('legendcal', 'vapIsCalendarLegendVisisble');
	}

	/**
	 * Returns the maximum number of minutes for which a pending 
	 * appointment can be marked as locked.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getAppointmentsLockedTime()
	{
		return (int) self::getFieldFromConfig('keepapplock', 'vapGetAppointmentsLockedTime');
	}

	/**
	 * Returns the minimum number of minutes in advance to book an appointment.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getBookingMinutesRestriction()
	{
		return (int) self::getFieldFromConfig('minrestr', 'vapGetBookingMinutesRestriction');
	}

	/**
	 * Returns the task to display while clicking the DASHBOARD link within the back-end.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getDefaultTask()
	{
		return self::getFieldFromConfig('deftask', 'vapGetDefaultTask');
	}
	
	/**
	 * Returns the number of seconds to refresh the dashboard.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getNotificationRefreshTime()
	{
		return (int) self::getFieldFromConfig('refreshtime', 'vapGetNotificationRefreshTime');
	}
	
	/**
	 * Returns the global login requirements:
	 * - [0] never
	 * - [1] optional
	 * - [2] required on confirmation page
	 * - [3] required on service/employee details page
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getLoginRequirements()
	{
		return (int) self::getFieldFromConfig('loginreq', 'vapGetLoginRequirements');
	}
	
	/**
	 * Checks if the appointments cancellation is allowed
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isCancellationEnabled()
	{
		return (int) self::getFieldFromConfig('enablecanc', 'vapIsCancellationEnabled');
	}
	
	/**
	 * Returns the minimum number of days in advance to cancel an appointment.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCancelBeforeTime()
	{
		return (int) self::getFieldFromConfig('canctime', 'vapGetCancelBeforeTime');
	}
	
	/**
	 * Checks if an order can be printed from the front-end.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isPrintableOrders()
	{
		return (int) self::getFieldFromConfig('printorders', 'vapIsPrintableOrders');
	}

	/**
	 * Checks if the system can generate automatically an invoice after the purchase.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isAutoGenerateInvoice()
	{
		return (int) self::getFieldFromConfig('invoiceorders', 'vapIsAutoGenerateInvoice');
	}
	
	/**
	 * Checks if the shopping cart system is enabled.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isCartEnabled()
	{
		return (int) self::getFieldFromConfig('enablecart', 'vapIsCartEnabled');
	}
	
	/**
	 * Returns the maximum number of appointments that can be purchased at once.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getMaxCartSize()
	{
		return (int) self::getFieldFromConfig('maxcartsize', 'vapGetMaxCartSize');
	}
	
	/**
	 * Checks if the customer can add an item into its cart.
	 *
	 * @param 	integer  $cart_size  The current number of items.
	 *
	 * @return 	boolean  True if allowed, false otherwise.
	 */
	public static function canAddItemToCart($cart_size)
	{
		$max_cart_size = self::getMaxCartSize();

		return ($max_cart_size == -1 || $cart_size < $max_cart_size || !self::isCartEnabled());
	}

	/**
	 * Checks if the cart can contains several items with the same checkin.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function canAddSameCheckinItems()
	{
		return (int) self::getFieldFromConfig('cartallowsync', 'vapGetCartAllowSync');
	}
	
	/**
	 * Returns the link type that will be used by clicking the "Continue Shopping" button:
	 * - [0]  link disabled
	 * - [-1] serviceslist (without group filtering) 
	 * - [-2] custom link
	 * - [1+] serviceslist (with group filter equals to the specified ID)
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getShopGroupFilter()
	{
		return (int) self::getFieldFromConfig('shoplink', 'vapGetShopGroupFilter');
	}

	/**
	 * Returns the custom link that will be used by clicking the "Continue Shopping" button.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getShopCustomLink()
	{
		return self::getFieldFromConfig('shoplinkcustom', 'vapGetShopCustomLink');
	}
	
	/**
	 * Checks if the cart is auto expanded.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isCartAutoExpanded()
	{
		return (int) self::getFieldFromConfig('confcartdisplay', 'vapGetConfirmCartAutoExpanded');
	}
	
	/**
	 * Checks if system should load jQuery framework (UI is always loaded).
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isLoadJQuery()
	{
		return (int)  self::getFieldFromConfig('loadjquery', 'vapIsLoadJQuery');
	}

	/**
	 * Checks if the joomla3810ter logo should be displayed within the back-end.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isJoomla3810terVisible()
	{
		return (int)  self::getFieldFromConfig('showjoomla3810ter', 'vapGetShowJoomla3810ter');
	}

	/**
	 * Checks if the packages system is enabled.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isPackagesEnabled()
	{
		return (int) self::getFieldFromConfig('enablepackages', 'vapGetEnablePackages');
	}

	/**
	 * Returns the maximum number of packages to display per row.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getPackagesPerRow()
	{
		return (int) self::getFieldFromConfig('packsperrow', 'vapGetPackagesPerRow');
	}

	/**
	 * Returns the maximum number of packages that can be purchased at once.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getMaxPackagesCart()
	{
		return (int) self::getFieldFromConfig('maxpackscart', 'vapGetMaxPackagesCart');
	}

	/**
	 * Checks if the users can register a new account while purchasing a package.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isPackagesUserRegistrationAllowed()
	{
		return (int) self::getFieldFromConfig('packsreguser', 'vapGetPackagesUserRegistrationAllowed');
	}
	
	/**
	 * Returns the confirmation message that will be asked while deleting an item.
	 * In case the confirmation message is disabled, an empty string will be returned.
	 *
	 * @return 	string
	 */
	public static function getConfirmSystemMessage()
	{
		if ((int) self::getFieldFromConfig('askconfirm', 'vapGetAskConfirm'))
		{
			return JText::_('VAPSYSTEMCONFIRMATIONMSG');
		}

		return '';
	}
	
	/**
	 * Returns the search mode used by the findreservation view within the back-end:
	 * - [1] search by employee > service
	 * - [2] search by service > employee
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getFindReservationSearchMode()
	{
		return (int) self::getFieldFromConfig('findresmode', 'vapGetFindReservationSearchMode');
	}
	
	/**
	 * Returns a list containing the closing days.
	 *
	 * Each element of the list is an associative array with the following properties:
	 * @property  integer 	ts 	  The unix timestamp.
	 * @property  string 	date  The formatted date.
	 * @property  integer 	freq  The closing frequency (0 = single day, 1 = weekly, 2 = monthly, 3 = yearly).
	 *
	 * @param 	integer  $id_ser  If specified, filters the closing days for this service.
	 *
	 * @return 	array
	 */
	public static function getClosingDays($id_ser = null)
	{
		$_str = self::getFieldFromConfig('closingdays', 'vapGetClosingDays');

		if (!strlen($_str))
		{
			return array();
		}

		static $pool = array();

		// check if the closing days were already fetched
		if (isset($pool[$id_ser]))
		{
			// return cached list
			return $pool[$id_ser];
		}

		$cd = explode(';;', $_str);

		$list = array();

		for ($i = 0, $n = count($cd); $i < $n; $i++)
		{
			$_app = explode(':', $cd[$i]);

			/**
			 * Fetch services assigned to the closing day.
			 *
			 * @since 1.6.3
			 */
			$_app[2] = empty($_app[2]) || $_app[2] == '*' ? '*' : explode(',', $_app[2]);

			// copy closing day only if it can be used for the specified service, if any
			if (!$id_ser || $_app[2] == '*' || in_array($id_ser, $_app[2]))
			{
				$list[] = array(
					'ts'       => $_app[0],
					'date'     => ArasJoomlaVikApp::jdate(self::getDateFormat(), $_app[0]),
					'freq'     => $_app[1],
					'services' => $_app[2],
				);
			}
		}

		// cache closing days
		$pool[$id_ser] = $list;

		return $list;
	}

	/**
	 * Returns a list containing the closing periods.
	 *
	 * Each element of the list is an associative array with the following properties:
	 * @property  start  start 	The starting closing period (UNIX timestamp).
	 * @property  end 	 end 	The ending closing period (UNIX timestamp).
	 *
	 * @param 	integer  $id_ser  If specified, filters the closing days for this service.
	 *
	 * @return 	array
	 */
	public static function getClosingPeriods($id_ser = null)
	{
		$_str = self::getFieldFromConfig('closingperiods', 'vapGetClosingPeriods');

		if (!strlen($_str))
		{
			return array();
		}

		static $pool = array();

		// check if the closing periods were already fetched
		if (isset($pool[$id_ser]))
		{
			// return cached list
			return $pool[$id_ser];
		}

		$cp = explode(';;', $_str);

		$list = array();

		for ($i = 0, $n = count($cp); $i < $n; $i++)
		{
			$_app = explode('-', $cp[$i]);

			/**
			 * Fetch services assigned to the closing day.
			 *
			 * @since 1.6.3
			 */
			$_app[2] = empty($_app[2]) || $_app[2] == '*' ? '*' : explode(',', $_app[2]);

			// copy closing day only if it can be used for the specified service, if any
			if (!$id_ser || $_app[2] == '*' || in_array($id_ser, $_app[2]))
			{
				$list[] = array(
					'start'     => $_app[0],
					'end'       => $_app[1],
					'datestart' => ArasJoomlaVikApp::jdate(self::getDateFormat(), $_app[0]),
					'dateend'   => ArasJoomlaVikApp::jdate(self::getDateFormat(), $_app[1]),
					'services'  => $_app[2],
				);
			}
		}

		// cache closing periods
		$pool[$id_ser] = $list;

		return $list;
	}
	
	/**
	 * Returns the ID of the custom field that will be used to validate the ZIP code.
	 *
	 * @param 	integer  $id_employee  The employee ID to search for in case the global field is not set.
	 *
	 * @return 	integer
	 */
	public static function getZipCodeValidationFieldId($id_employee)
	{
		$id = (int) self::getFieldFromConfig('zipcfid', 'vapGetZipCustomFieldId');

		if ($id == -1)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select($dbo->qn('zip_field_id'))
				->from($dbo->qn('#__vikappointments_employee_settings'))
				->where($dbo->qn('id_employee') . ' = ' . (int) $id_employee);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$id = $dbo->loadResult();
			}
		}

		return $id;
	}
	
	/**
	 * Returns a list containing the accepted ZIP codes.
	 *
	 * @return 	array
	 */
	public static function getZipCodes()
	{
		$_str = self::getFieldFromConfig('zipcodes', 'vapGetZipCodes');

		return (array) json_decode($_str, true);
	}
	
	/**
	 * Checks if the recurrence selection is enabled.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isRecurrenceEnabled()
	{
		return (int) self::getFieldFromConfig('enablerecur', 'vapIsRecurrenceEnabled');
	}
	
	/**
	 * Returns the configuration array containing the recurrence parameters.
	 * The array will contain the following keys:
	 *
	 * @property  array 	repeat  The allowed repeat options.
	 * @property  integer  	min 	The minimum number of elements that can be selected.
	 * @property  integer  	max 	The maximum number of elements that can be selected.
	 * @property  array  	for 	The allowed for options.
	 *
	 * @return 	array
	 */
	public static function getRecurrenceParams()
	{
		return array( 
			'repeat' => explode(';', self::getFieldFromConfig('repeatbyrecur', 'vapGetRecurrenceRepeatBy')),
			'min' 	 => (int) self::getFieldFromConfig('minamountrecur', 'vapGetRecurrenceMinAmount'),
			'max' 	 => (int) self::getFieldFromConfig('maxamountrecur', 'vapGetRecurrenceMaxAmount'),
			'for' 	 => explode(';', self::getFieldFromConfig('fornextrecur', 'vapGetRecurrenceForNext')),
		);
	}
	
	/**
	 * Checks if the reviews are enabled.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isReviewsEnabled()
	{
		return (int) self::getFieldFromConfig('enablereviews', 'vapIsReviewsEnabled');
	}
	
	/**
	 * Checks if the reviews for the services are enabled.
	 *
	 * @return 	boolean
	 */
	public static function isServicesReviewsEnabled()
	{
		return self::isReviewsEnabled() && (int) self::getFieldFromConfig('revservices', 'vapIsReviewsServicesEnabled');
	}
	
	/**
	 * Checks if the reviews for the employees are enabled.
	 *
	 * @return 	boolean
	 */
	public static function isEmployeesReviewsEnabled()
	{
		return self::isReviewsEnabled() && (int) self::getFieldFromConfig('revemployees', 'vapIsReviewsEmployeesEnabled');
	}
	
	/**
	 * Checks if it is mandatory to write a comment while leaving a review.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isReviewsCommentRequired()
	{
		return (int) self::getFieldFromConfig('revcommentreq', 'vapIsReviewsCommentRequired');
	}
	
	/**
	 * Returns the minimum number of characters to use for comments.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getReviewsCommentMinLength()
	{
		return (int) self::getFieldFromConfig('revminlength', 'vapGetReviewsCommentMinLength');
	}
	
	/**
	 * Returns the maximum number of characters to use for comments.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getReviewsCommentMaxLength()
	{
		return (int) self::getFieldFromConfig('revmaxlength', 'vapGetReviewsCommentMaxLength');
	}
	
	/**
	 * Returns the maximum number of reviews to display per time.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getReviewsListLimit()
	{
		return (int) self::getFieldFromConfig('revlimlist', 'vapGetReviewsListLimit');
	}
	
	/**
	 * Checks if the reviews should be published automatically after submitting them.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isReviewsAutoPublished()
	{
		return (int) self::getFieldFromConfig('revautopublished', 'vapGetReviewsAutoPublished');
	}

	/**
	 * Checks if the reviews should be filtered by language.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isReviewsLangFilter()
	{
		return (int) self::getFieldFromConfig('revlangfilter', 'vapGetReviewsLangFilter');
	}
	
	/**
	 * Returns the mode used to load the reviews:
	 * - [1] on scroll down
	 * - [2] button click
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getReviewsLoadMode()
	{
		return (int) self::getFieldFromConfig('revloadmode', 'vapGetReviewsLoadMode');
	}

	/**
	 * Checks if the waiting list system is enabled.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isWaitingList()
	{
		return (int) self::getFieldFromConfig('enablewaitlist', 'vapIsWaitingList');
	}

	/**
	 * Returns an array containing the SMS templates of the waiting list.
	 *
	 * @return 	array
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getWaitingListContentSMS()
	{
		return json_decode(self::getFieldFromConfig('waitlistsmscont', 'vapGetWaitingListSmsContent'), true);
	}
	
	/**
	 * Checks if the system supports multiple time offsets.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isMultipleTimezones()
	{
		return (int) self::getFieldFromConfig('multitimezone', 'vapGetMultipleTimezones');
	}
	
	/**
	 * Returns an array containing the fields to display within the reservations list (back-end).
	 *
	 * @return 	array
	 */
	public static function getListableFields()
	{
		$str = self::getFieldFromConfig('listablecols', 'vapGetListableColumns');

		if (empty($str))
		{
			return array();
		}
		
		return explode(",", $str);
	}

	/**
	 * Returns the original width resize (in pixel).
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getOriginalWidthResize()
	{
		return (int) self::getFieldFromConfig('oriwres', 'vapGetOriginalWidthResize');
	}
	
	/**
	 * Returns the original height resize (in pixel).
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getOriginalHeightResize()
	{
		return (int) self::getFieldFromConfig('orihres', 'vapGetOriginalHeightResize');
	}
	
	/**
	 * Returns the thumbnail width resize (in pixel).
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getSmallWidthResize()
	{
		return (int) self::getFieldFromConfig('smallwres', 'vapGetSmallWidthResize');
	}
	
	/**
	 * Returns the thumbnail height resize (in pixel).
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getSmallHeightResize()
	{
		return (int) self::getFieldFromConfig('smallhres', 'vapGetSmallHeightResize');
	}
	
	/**
	 * Checks if the original image should be resized.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isImageResize()
	{
		return (int) self::getFieldFromConfig('isresize', 'vapIsImageResize');
	}
	
	/**
	 * Checks if the media properties have been configured.
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isMediaPropertiesConfigured()
	{
		return (int) self::getFieldFromConfig('isconfig', 'vapIsMediaPropertiesConfigured');
	}

	/**
	 * Checks if the statistics are displayed within the calendar view (back-end).
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function isStatisticsVisible()
	{
		return (int) self::getFieldFromConfig('is_stat', 'vapIsStatistics');
	}
	
	/**
	 * Returns the driver used to send SMS messages. 
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getSmsApi()
	{
		return self::getFieldFromConfig('smsapi', 'vapGetSmsApi');
	}
	
	/**
	 * Checks if the SMS notifications can be be sent automatically.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getSmsApiEnabled()
	{
		return (int) self::getFieldFromConfig('smsenabled', 'vapGetSmsApiEnabled');
	}
	
	/**
	 * Checks if the SMS notifications should be send to the customers.
	 *
	 * @return 	integer
	 */
	public static function getSmsApiToCustomer()
	{
		$str = self::getFieldFromConfig('smsapito', 'vapGetSmsApiTo');
		$str = explode(',', $str);
		return intval($str[0]);
	}
	
	/**
	 * Checks if the SMS notifications should be send to the employees.
	 *
	 * @return 	integer
	 */
	public static function getSmsApiToEmployee()
	{
		$str = self::getFieldFromConfig('smsapito', 'vapGetSmsApiTo');
		$str = explode(',', $str);
		return intval($str[1]);
	}
	
	/**
	 * Checks if the SMS notifications should be send to the administrator.
	 *
	 * @return 	integer
	 */
	public static function getSmsApiToAdmin()
	{
		$str = self::getFieldFromConfig('smsapito', 'vapGetSmsApiTo');
		$str = explode(',', $str);
		return intval($str[2]);
	}
	
	/**
	 * Returns the administrator phone number.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getSmsApiAdminPhoneNumber()
	{
		return self::getFieldFromConfig('smsapiadminphone', 'vapGetSmsApiAdminPhoneNumber');
	}
	
	/**
	 * Returns the configuration array of the selected SMS driver.
	 *
	 * @return 	array
	 */
	public static function getSmsApiFields()
	{
		$_str = self::getFieldFromConfig('smsapifields', 'vapGetSmsApiFields');

		if (!empty($_str))
		{
			return json_decode($_str, true);
		}

		return array();
	}
	
	/**
	 * Returns the default text to use for SMS.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getSmsDefaultCustomersText()
	{
		return self::getFieldFromConfig('smstextcust', 'vapGetSmsCustomersText');
	}
	
	/**
	 * Returns the global synchronization secret key.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getSyncSecretKey()
	{
		return self::getFieldFromConfig('synckey', 'vapGetSyncSecretKey');
	}
	
	/**
	 * Returns the global CRON JOBS secret key.
	 *
	 * @return 	string
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCronSecureKey()
	{
		return self::getFieldFromConfig('cron_secure_key', 'vapGetCronSecureKey');
	}
	
	/**
	 * Returns logging mode used by the CRON JOBS:
	 * - [1] only with errors
	 * - [2] always
	 *
	 * @return 	integer
	 *
	 * @deprecated 	1.8 	Use UIConfig instead.
	 */
	public static function getCronLoggingMode()
	{
		return (int) self::getFieldFromConfig('cron_log_mode', 'vapGetCronLoggingMode');
	}

	/**
	 * Returns the value of the specified configuration setting.
	 *
	 * @param 	string 	$param 	The setting name.
	 *
	 * @return 	string 	The configuration value.
	 */
	private static function getFieldFromConfig($param)
	{
		return UIFactory::getConfig()->getString($param, '');
	}

	/**
	 * Checks if the login URL is routable according to the Joomla version.
	 * If Joomla! is higher than 3.4.8, it is no more able to recognize internal
	 * URLs if they don't start with the base domain.
	 *
	 * @return 	boolean  True if the URL can be routed, false otherwise.
	 *
	 * @deprecated 	1.8  Use UIApplication::routeForExternalUse() instead.
	 */
	public static function isLoginUrlRoutable()
	{
		$version = new JVersion();
		
		return version_compare($version->getShortVersion(), '3.4.8', '<');
	}
	
	/**
	 * Loads the cart framework.
	 *
	 * @return 	void
	 */
	public static function loadCartLibrary()
	{
		UILoader::import('libraries.cart.cart');
		UILoader::import('libraries.cart.utils');
		UILoader::import('libraries.cart.core');
	}

	/**
	 * Loads the cart packages framework.
	 *
	 * @return 	void
	 */
	public static function loadCartPackagesLibrary()
	{
		UILoader::import('libraries.cartpack.cart');
		UILoader::import('libraries.cartpack.core');
	}
	
	/**
	 * Loads the cron framework.
	 *
	 * @return 	boolean  True if the framework was loaded, false otherwise. 
	 */
	public static function loadCronLibrary()
	{
		UILoader::import('libraries.cron.core');
		UILoader::import('libraries.cron.overrides.vapcronbuilder');

		CronDispatcher::$BASE_PATH = VAPADMIN . DIRECTORY_SEPARATOR . 'cronjobs' . DIRECTORY_SEPARATOR;
	}
	
	/**
	 * Checks if the user can cancel the appointment with the checkin timestamp.
	 *
	 * @param 	integer  $checkin_ts  The checkin timestamp of the appointment to cancel.
	 *
	 * @return 	boolean  True if allowed, false otherwise.
	 */
	public static function canUserCancelOrder($checkin_ts)
	{
		return self::isCancellationEnabled() && time() + self::getCancelBeforeTime() * 60 * 60 * 24 < $checkin_ts;
	}
	
	/**
	 * Helper method used to upload the given image (retrieved from $_FILES)
	 * into the specified destination.
	 *
	 * @param 	array 	$img 	An associative array with the file details.
	 * @param 	string 	$dest 	The destination path.
	 *
	 * @return 	array 	The uploading result.
	 */
	public static function uploadImage($img, $dest)
	{
		jimport('joomla.filesystem.file');

		$args = array(
			'esit' => 0,
			'name' => '',
		);

		$dest = rtrim($dest, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		
		if (!empty($img['name']))
		{
			$filename 	= JFile::makeSafe(str_replace(" ", "-", strtolower($img['name'])));
			$src 		= $img['tmp_name'];

			$j = "";

			if (file_exists($dest . $filename))
			{
				$j = 2;

				while (file_exists($dest . $j . '-' . $filename))
				{
					$j++;
				}

				$j = $j . '-';
			}

			$finaldest = $dest . $j . $filename;
			
			$check = getimagesize($img['tmp_name']);

			if ($check[2] & imagetypes())
			{
				if (JFile::upload($src, $finaldest))
				{
					// file uploaded correctly
					$args['name'] = $j . $filename;
					$args['esit'] = 1;
				}
				else
				{
					// an error occurred while uploading the image
					$args['esit'] = -1;
				}
			}
			else
			{
				// the file to upload is not an image
				$args['esit'] = -2;
			}
		}
		
		return $args;
	}
	
	/**
	 * Helper method used to upload the given file (retrieved from $_FILES)
	 * into the specified destination.
	 *
	 * @param 	array 	$file 		An associative array with the file details.
	 * @param 	string 	$filters 	A string containing the allowed extensions.
	 * @param 	string 	$dest 		The destination path.
	 * @param 	string 	$filename 	The filename to use. If not provided, a random one will be generated.
	 *
	 * @return 	array 	The uploading result.
	 *
	 * @uses 	isFileTypeCompatible()
	 */
	public static function uploadFile($file, $filters, $dest, $filename = '')
	{
		jimport('joomla.filesystem.file');

		$args = array(
			'esit' => 0,
			'name' => '',
		);

		$dest = rtrim($dest, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		
		if (!empty($file['name']))
		{
			if (empty($filename))
			{
				$filename = rand(171, 1717) . time() . substr($file['name'], strrpos($file['name'], '.')); 
			}

			$src = $file['tmp_name'];

			$j = '';
			
			if (file_exists($dest . $filename))
			{
				$j = 2;

				while (file_exists($dest . $j . '-' . $filename))
				{
					$j++;
				}

				$j = $j . '-';
			}

			$finaldest = $dest . $j . $filename;
			
			if (self::isFileTypeCompatible($file, $filters))
			{
				if (JFile::upload($src, $finaldest))
				{
					// file uploaded successfully
					$args['name'] = $j . $filename;
					$args['esit'] = 1;
				}
				else
				{
					// an error occurred while uploading the file
					$args['esit'] = -1;
				}
			}
			else
			{
				// the file type is not supported
				$args['esit']  = -2;
				$args['error'] = $file['type'];
			}
			
		}
		
		return $args;
	}
	
	/**
	 * Helper method used to print formatted prices according to the global configuration.
	 *
	 * @param 	float 	 $price  The price to format.
	 * @param 	string 	 $symb 	 The currency symbol. If not provided the default one will be used.
	 * @param 	integer  $pos 	 The currency position (1 = after price, 2 = before price).
	 * 							 If not provided, the default one will be used.
	 *
	 * @return 	string 	 The formatted price.
	 */
	public static function printPriceCurrencySymb($price, $symb = null, $pos = null)
	{
		if (!$symb)
		{
			$symb = self::getCurrencySymb();
		}
		
		if (!$pos)
		{
			$pos = self::getCurrencySymbPosition();
		}

		$dec_separator 	= self::getCurrencyDecimalSeparator();
		$tho_separator 	= self::getCurrencyThousandsSeparator();
		$dec_digits 	= self::getCurrencyDecimalDigits();
		
		$price = floatval($price);

		// after price
		if ($pos == 1)
		{
			return number_format($price, $dec_digits, $dec_separator, $tho_separator) . ' ' . $symb;
		}

		// before price
		return $symb . ' ' . number_format($price, $dec_digits, $dec_separator, $tho_separator);
	}
	
	/**
	 * Checks if the value for the specified custom field is valid.
	 *
	 * @param 	array 	 $cf 	The custom field details.
	 * @param 	mixed 	 $val 	The given value.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 */
	public static function isCustomFieldValid($cf, $val)
	{
		return $cf['required'] == 0
		|| ($cf['type'] != 'file' && strlen($val))
		|| ($cf['type'] == 'file' && !empty($val['name']));
	}
	
	/**
	 * Checks if the given file is supported.
	 *
	 * @param 	array 	 $file 		The file details (retrieved by $_FILES).
	 * @param 	string   $filters 	The string with the allowed filters (separated by a comma).
	 *
	 * @return 	boolean  True if allowed, false otherwise.
	 */
	public static function isFileTypeCompatible($file, $filters)
	{
		if (strlen($filters) == 0)
		{
			// if no filter provided, consider it as always allowed.
			return true;
		}
		
		$types = explode(',', $filters);

		for ($i = 0; $i < count($types); $i++)
		{
			$types[$i] = trim($types[$i]);

			if (strpos($file['type'], $types[$i]) !== false || $types[$i] == '*')
			{
				return true;
			}
		}
		
		return false;
	} 
	
	/**
	 * Validates the given coupon code.
	 *
	 * @param 	array 	 $coupon  The coupon details.
	 * @param 	mixed 	 $cart 	  The cart instance.
	 *
	 * @return 	boolean  True if the coupon can be redeemed, false otherwise.
	 */
	public static function validateCoupon($coupon, $cart)
	{
		$_today = time();
		
		$total_cost = $cart->getTotalCost();
		$items = $cart->getItemsList();
		
		if ($coupon['type'] == 1 || $coupon['max_quantity'] - $coupon['used_quantity'] > 0)
		{
			/**
			 * Validate publishing dates using specified mode.
			 *
			 * @since 1.6.3
			 */
			if ($coupon['dstart'] != -1)
			{
				if ($coupon['pubmode'] == 1)
				{
					// compare publishing dates with current date
					if ($coupon['dstart'] > $_today || $_today > $coupon['dend'])
					{
						// not valid, the coupon is not yet active or expired
						return false;
					}
				}
				else
				{
					// all items must match the specified dates
					foreach ($items as $i)
					{
						// compare publishing dates with checkin dates
						if ($coupon['dstart'] > $i->getCheckinTimestamp() || $i->getCheckinTimestamp() > $coupon['dend'])
						{
							// not valid, the coupon is not yet active or expired for the selected checkin
							return false;
						}
					}
				}
			}

			$coupon_services  = self::getAllCouponServices($coupon['id']);
			$coupon_employees = self::getAllCouponEmployees($coupon['id']);

			$ok_coupon_service  = true;
			$ok_coupon_employee = true;
			
			foreach ($items as $i)
			{
				$ok_coupon_service  = $ok_coupon_service  && (count($coupon_services)  == 0 || in_array($i->getID() , $coupon_services));
				$ok_coupon_employee = $ok_coupon_employee && (count($coupon_employees) == 0 || in_array($i->getID2(), $coupon_employees));
			}

			if (!$ok_coupon_service || !$ok_coupon_employee)
			{
				return false;
			}

			if ($coupon['lastminute'] == 0)
			{
				return true;
			}
			
			foreach ($items as $i)
			{
				if ($_today + $coupon['lastminute'] * 3600 < $i->getCheckinTimestamp())
				{
					return false;
				}
			}

			return true;
		}
		
		return false;
	}

	/**
	 * Returns all the services assigned to the specified coupon.
	 *
	 * @param 	integer  $id_coupon  The coupon ID.
	 *
	 * @return 	array 	 A list containing the ID of the assigned services.
	 */
	public static function getAllCouponServices($id_coupon)
	{
		$services = array();
		
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_service'))
			->from($dbo->qn('#__vikappointments_coupon_service_assoc'))
			->where($dbo->qn('id_coupon') . ' = ' . (int) $id_coupon);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$services = $dbo->loadColumn();
		}

		return $services;
	}

	/**
	 * Returns all the employees assigned to the specified coupon.
	 *
	 * @param 	integer  $id_coupon  The coupon ID.
	 *
	 * @return 	array 	 A list containing the ID of the assigned employees.
	 */
	public static function getAllCouponEmployees($id_coupon)
	{
		$employees = array();
		
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_employee'))
			->from($dbo->qn('#__vikappointments_coupon_employee_assoc'))
			->where($dbo->qn('id_coupon') . ' = ' . (int) $id_coupon);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadColumn();
		}

		return $employees;
	}

	/**
	 * Marks the specified coupon as used.
	 * In addition, removes the coupon if it should be deleted once
	 * the maximum number of usages is reached.
	 * 
	 * @param 	array 	 $coupon 	The coupon details.
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 */
	public static function couponUsed($coupon, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		if (!$coupon || empty($coupon['id']))
		{
			return false;
		}

		$coupon['used_quantity']++;

		if ($coupon['max_quantity'] - $coupon['used_quantity'] <= 0 && $coupon['remove_gift'] && $coupon['type'] == 2)
		{
			$q = $dbo->getQuery(true)
				->delete($dbo->qn('#__vikappointments_coupon'))
				->where($dbo->qn('id') . ' = ' . $coupon['id']);

			$dbo->setQuery($q);
			$dbo->execute();
		}
		else
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_coupon'))
				->set($dbo->qn('used_quantity') . ' = ' . $dbo->qn('used_quantity') . ' + 1')
				->where($dbo->qn('id') . ' = ' . $coupon['id']);

			$dbo->setQuery($q);
			$dbo->execute();
		}

		return (bool) $dbo->getAffectedRows();
	}
	
	/**
	 * Validates the specified recurring data.
	 *
	 * @param 	integer  $repeat  The repeat by identifier.
	 * @param 	integer  $amount  The selected amount.
	 * @param 	integer  $for 	  The repeat for identifier.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 */
	public static function validateRecurringData($repeat, $amount, $for)
	{
		if (!self::isRecurrenceEnabled())
		{
			return false;
		}
		
		$params = self::getRecurrenceParams();
		
		if (($repeat - 1) < 0 || ($repeat - 1) >= count($params['repeat']) || $params['repeat'][$repeat - 1] == 0)
		{
			return false;
		}
		
		if (($for - 1) < 0 || ($for - 1) >= count($params['for']) || $params['for'][$for - 1] == 0)
		{
			return false;
		}
		
		if ($amount < $params['min'] || $params['max'] < $amount)
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Calculates the discounted total cost considering the coupon code and
	 * the user credit (if specified).
	 *
	 * @param 	float 	$total_cost 	The base total cost.
	 * @param 	array 	$coupon 		The coupon code.
	 * @param 	mixed 	&$credit 		The current user credit. Provide true
	 * 									to retrieve the user credit from the database.
	 * @param 	float 	&$creditUsed 	The credit amount that has been used.
	 *
	 * @return 	float 	The final discounted total cost.
	 */
	public static function getDiscountTotalCost($total_cost, $coupon, &$credit = false, &$creditUsed = 0)
	{
		if (!empty($coupon))
		{
			if ($coupon['percentot'] == 1)
			{
				// percent
				$total_cost -= $total_cost * $coupon['value'] / 100.0;
			}
			else
			{
				// total
				$total_cost -= $coupon['value'];
			}
		}

		/**
		 * If the credit is specified, use it.
		 *
		 * @since 1.6
		 */
		if ($credit === true)
		{
			$user 	= JFactory::getUser();
			$credit = 0.0;

			if (!$user->guest)
			{
				$dbo = JFactory::getDbo();

				$q = $dbo->getQuery(true)
					->select($dbo->qn('credit'))
					->from($dbo->qn('#__vikappointments_users'))
					->where($dbo->qn('jid') . ' = ' . $user->id)
					->orWhere(array(
						$dbo->qn('jid') . ' <= 0',
						$dbo->qn('billing_mail') . ' = ' . $dbo->q($user->email),
					), 'AND');

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$credit = (float) $dbo->loadResult();
				}
			}
		}

		if ($credit && $total_cost > 0)
		{
			if ($credit > $total_cost)
			{
				$creditUsed = $total_cost;
			}
			else
			{
				$creditUsed = $credit;
			}

			$total_cost -= $credit;
		}
		
		return max(array($total_cost, 0));
	}
	
	/**
	 * Returns the deposit amount that should be left.
	 *
	 * @param 	float 	 $total_cost 	The total cost of the order.
	 * @param 	boolean  $ignore 		True to skip the deposit calculation.
	 * 									It should be verified when the customer decides
	 * 									to pay the full amount (only for OPTIONAL mode).
	 *
	 * @return 	mixed 	 The new amount if the deposit should be left, otherwise false.
	 */
	public static function getDepositAmountToLeave($total_cost, $ignore = false)
	{
		$config = UIFactory::getConfig();

		$use = $config->getUint('usedeposit');

		if (!$use)
		{
			// [NO] do not use deposit
			return false;
		}

		if ($use == 1 && $ignore)
		{
			// [OPTIONAL] the customer decided to pay the full amount
			return false;
		}

		$deposit_after 	= $config->getFloat('depositafter', 0);
		$deposit_value 	= $config->getFloat('depositvalue', 0);
		$deposit_type 	= $config->getUint('deposittype', 1);
		
		// make sure the condition is verified
		if ($total_cost > $deposit_after)
		{
			if ($deposit_type == 1)
			{
				// percent
				return round($total_cost * $deposit_value / 100.0, 2);
			}
			else
			{
				// total
				return $deposit_value;
			}
		}
		
		// the total cost is still lower than the minimum required
		return false;
	}

	/**
	 * Returns all the ZIP codes of the given employee.
	 *
	 * @param 	integer  $id_employee 	The employee ID.
	 *
	 * @return 	mixed 	 The ZIP codes array if any, false otherwise.
	 */
	public static function getEmployeeZipCodes($id_employee)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('zipcodes'))
			->from($dbo->qn('#__vikappointments_employee_settings'))
			->where($dbo->qn('id_employee') . ' = ' . (int) $id_employee);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$zips = $dbo->loadResult();

			if (!empty($zips))
			{
				return json_decode($zips, true);
			}
		}

		return false;
	}
	
	/**
	 * Helper method used to validate the specified ZIP code.
	 *
	 * @param 	string 	 $zip_code 		The specified ZIP code.
	 * @param 	array 	 $id_employees 	The employees list.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @uses 	getEmployeeZipCodes()
	 */
	public static function validateZipCode($zip_code, $id_employees)
	{
		$cf_id = self::getZipCodeValidationFieldId($id_employees[0]);

		if ($cf_id == -1)
		{
			return true;	
		}
		else if (empty($zip_code))
		{
			return false;
		}

		$global_zips = self::getZipCodes();

		foreach ($id_employees as $id)
		{
			$args = false;

			if ($id != -1)
			{
				$args = self::getEmployeeZipCodes($id);
			}

			if ($args === false || !$args || count($args) == 0)
			{
				$args = $global_zips;
			}

			$valid = false;

			for ($i = 0; $i < count($args) && !$valid; $i++)
			{
				if ($args[$i]['from'] <= $zip_code && $zip_code <= $args[$i]['to'])
				{
					$valid = true;
				}
			}

			if (!$valid)
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Helper method used to format a UNIX timestamp to the closest unit.
	 * In case there is not a close unit, the specified date format will be used.
	 *
	 * @param 	string 	 $dt_f 	The date format.
	 * @param 	integer  $ts 	The timestamp to format.
	 *
	 * @return 	string 	 The formatted date.
	 */
	public static function formatTimestamp($dt_f, $ts)
	{	
		$diff = time() - $ts;

		if (abs($diff) < 60)
		{
			return JText::_('VAPDFNOW');
		}
		
		$minutes = abs($diff) / 60;

		if ($minutes < 60)
		{
			return JText::sprintf('VAPDFMINS' . ($diff > 0 ? 'AGO' : 'AFT'), floor($minutes));
		}
		
		$hours = $minutes / 60;

		if ($hours < 24)
		{
			$hours = floor($hours);

			if ($hours == 1)
			{
				return JText::_('VAPDFHOUR' . ($diff > 0 ? 'AGO' : 'AFT'));
			}

			return JText::sprintf('VAPDFHOURS' . ($diff > 0 ? 'AGO' : 'AFT'), $hours);
		}
		
		$days = $hours / 24;

		if ($days < 7)
		{
			$days = floor($days);

			if ($days == 1)
			{
				return JText::_('VAPDFDAY' . ($diff > 0 ? 'AGO' : 'AFT'));
			}

			return JText::sprintf('VAPDFDAYS' . ($diff > 0 ? 'AGO' : 'AFT'), $days);
		}
		
		$weeks = $days / 7;

		if ($weeks < 3)
		{
			$weeks = floor($weeks);

			if ($weeks == 1)
			{
				return JText::_('VAPDFWEEK' . ($diff > 0 ? 'AGO' : 'AFT'));
			}

			return JText::sprintf('VAPDFWEEKS'.($diff > 0 ? 'AGO' : 'AFT'), $weeks);
		}
		
		return ArasJoomlaVikApp::jdate($dt_f, $ts);
	}
	
	/**
	 * Helper method to format the specified minutes to the closest unit.
	 * For example, 150 minutes will be formatted as "1 hour & 30 min.".
	 *
	 * @param 	string 	 $minutes 	The minutes amount.
	 * @param 	boolean  $apply 	True to format, false to return it plain.
	 *
	 * @return 	string 	 The formatted string.
	 */
	public static function formatMinutesToTime($minutes, $apply = true)
	{
		$min_str = array(
			JText::_('VAPSHORTCUTMINUTE'), 	// singular
			'', 							// plural
		);
		
		if (!$apply)
		{
			return $minutes . ' ' . $min_str[0];
		}
		
		$hours_str = array(
			JText::_('VAPFORMATHOUR'), 	// singular
			JText::_('VAPFORMATHOURS'), // plural
		);

		$days_str  = array(
			JText::_('VAPFORMATDAY'), 	// singular
			JText::_('VAPFORMATDAYS'), 	// plural
		);

		$weeks_str = array(
			JText::_('VAPFORMATWEEK'), 	// singular
			JText::_('VAPFORMATWEEKS'), // plural
		);
		
		$comma_char = JText::_('VAPFORMATCOMMASEP');
		$and_char 	= JText::_('VAPFORMATANDSEP');
		
		$is_negative = $minutes < 0 ? 1 : 0;
		$minutes = abs($minutes);
		
		$format = "";

		while ($minutes >= 60)
		{
			$app_str = "";

			if ($minutes >= 10080)
			{
				// weeks
				$val = floor($minutes / 10080);

				$app_str = $val . ' ' . $weeks_str[(int) ($val > 1)]; // if greater than 1 then plural, otherwise singular
				$minutes = $minutes % 10080;
			} 
			else if ($minutes >= 1440)
			{
				// days
				$val = floor($minutes / 1440);

				$app_str = $val . ' ' . $days_str[(int) ($val > 1)]; // if greater than 1 then plural, otherwise singular
				$minutes = $minutes % 1440;
			}
			else
			{
				// hours
				$val = floor($minutes / 60);

				$app_str = $val . ' ' . $hours_str[(int) ($val > 1)]; // if greater than 1 then plural, otherwise singular
				$minutes = $minutes % 60;
			}
			
			$sep = '';

			if ($minutes > 0)
			{
				$sep = $comma_char;
			}
			else if ($minutes == 0)
			{
				$sep = " $and_char";
			}
			
			$format .= (!empty($format) ? $sep . ' ' : '') . $app_str;
		}
		
		if ($minutes > 0)
		{
			$format .= (!empty($format) ? " $and_char " : '') . $minutes . ' ' . $min_str[0];
		}
		
		if ($is_negative)
		{
			$format = '-' . $format;
		}
			
		return $format;
	}

	/**
	 * Helper method used to format the checkin timestamp.
	 * It may return all the following values:
	 * - today 		In case the checkin is for the current day (e.g. today in 2 hours).
	 * - tomorrow 	In case the checkin is for the next day (e.g. tomorrow @ 10:00).
	 * - datetime 	A formatted datetime (e.g. 2018-07-28 @ 10:00).
	 *
	 * @param 	string 	 $dt_f 	The default date format.
	 * @param 	string 	 $t_f 	The default time format.
	 * @param 	integer  $ts 	The checkin timestamp.
	 *
	 * @return 	string 	 The formatted checkin.
	 *
	 * @uses 	formatMinutesToTime()
	 */
	public static function formatCheckinTimestamp($dt_f, $t_f, $ts)
	{
		$today = getdate();
		$date  = getdate($ts);
		$diff  = $date[0] - $today[0];

		$today_no_time  = strtotime('00:00:00');
		$date_no_time   = strtotime('00:00:00', $ts);
		$diff_no_time  	= $date_no_time - $today_no_time;

		if ($diff > 0 && $diff_no_time >= -3600 && $diff_no_time <= 3600)
		{
			return JText::sprintf('VAPTODAYIN', self::formatMinutesToTime(ceil($diff / 60)));
		}
		else if ($diff_no_time >= 82800 && $diff_no_time <= 90000)
		{
			return JText::sprintf('VAPTOMORROWAT', date($t_f, $ts));
		}
		
		return date($dt_f, $ts);
	}
	
	/**
	 * Helper method used to render the contents of HTML descriptions.
	 *
	 * @param 	string 	$description 	The description to render.
	 * @param 	string 	$task 			The view/task that invoked this method.
	 * @param 	array 	$params 		An array of options.
	 *
	 * @return 	string 	The rendered description.
	 */
	public static function renderHtmlDescription($description, $task, $params = array())
	{
		$dispatcher = UIFactory::getEventDispatcher();
		$dispatcher->import('content');

		$content = JTable::getInstance('content');
		$content->text = $description;

		$lookup = array(
			'employeeslist'	 => 0, // short
			'employeesearch' => 1, // full
			'serviceslist'	 => 0, // short
			'servicesearch'	 => 1, // full
			'microdata' 	 => 0, // short
			'paymentconfirm' => 0, // short
			'paymentorder'   => 1, // full
		);

		// checks if the task should use the short or full description
		$full = !empty($lookup[$task]);

		/**
		 * Lets the platform handler prepares the content.
		 *
		 * @since 1.6.3
		 */
		UIApplication::getInstance()->onContentPrepare($content, $full);

		// trigger event to render contents
		$dispatcher->trigger('onContentPrepare', array('com_vikappointments.' . $task, &$content, &$params, 0));
		
		return $content->text;
	}
	
	/**
	 * Loads the main assets (CSS and JS) of the component.
	 *
	 * @return 	void
	 */
	public static function load_css_js()
	{
		$doc = JFactory::getDocument();
		$vik = UIApplication::getInstance();

		$options = array(
			'version' => VIKAPPOINTMENTS_SOFTWARE_VERSION,
		);

		// since jQuery is a required dependency, the framework should be 
		// invoked even if jQuery is disabled
		$vik->loadFramework('jquery.framework');
		
		if (VikAppointments::isLoadJQuery())
		{
			$vik->addScript(VAPASSETS_URI . 'js/jquery-1.11.1.min.js');
		}
		
		$vik->addScript(VAPASSETS_URI . 'js/jquery-ui-1.11.1.min.js');
		$vik->addScript(VAPASSETS_URI . 'js/vikappointments.js', $options);
		
		$doc->addStyleSheet(VAPASSETS_URI . 'css/jquery-ui.min.css');
		$doc->addStyleSheet(VAPASSETS_URI . 'css/vikappointments.css', $options);
		$doc->addStyleSheet(VAPASSETS_URI . 'css/vikappointments-mobile.css', $options);
		
		// custom
		if (is_file(VAPBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'vap-custom.css'))
		{
			$doc->addStyleSheet(VAPASSETS_URI . 'css/vap-custom.css', $options);
		}

		/**
		 * Adjust component layout to fit the specified theme.
		 *
		 * @since 1.6
		 */
		$theme = UIFactory::getConfig()->get('sitetheme');

		if ($theme)
		{
			$doc->addStyleSheet(VAPASSETS_URI . 'css/themes/' . $theme . '.css', $options);
		}
	}
	
	/**
	 * Loads the scripts needed to use Select2 jQuery plugin.
	 *
	 * @return 	void
	 */
	public static function load_complex_select()
	{
		$doc = JFactory::getDocument();
		$vik = UIApplication::getInstance();
		
		$vik->addScript(VAPASSETS_URI . 'js/select2/select2.min.js');
		$doc->addStyleSheet(VAPASSETS_URI . 'js/select2/select2.css');
	}

	/**
	 * Loads the stylesheets needed to use Font Awesome.
	 *
	 * @return 	void
	 */
	public static function load_font_awesome()
	{
		JFactory::getDocument()->addStyleSheet(VAPASSETS_ADMIN_URI . 'css/font-awesome/css/font-awesome.min.css');
	}
	
	/**
	 * Loads the scripts needed to use Chart JS jQuery plugin.
	 *
	 * @return 	void
	 */
	public static function load_charts()
	{	
		UIApplication::getInstance()->addScript(VAPASSETS_URI . 'js/charts-framework/Chart.min.js');
	}
	
	/**
	 * Loads the scripts needed to use Fancybox jQuery plugin.
	 *
	 * @return 	void
	 */
	public static function load_fancybox()
	{
		$doc = JFactory::getDocument();
		$vik = UIApplication::getInstance();
		
		$vik->addScript(VAPASSETS_URI . 'js/jquery.fancybox.min.js');
		$doc->addStyleSheet(VAPASSETS_URI . 'css/jquery.fancybox.min.css');
	}
	
	/**
	 * Loads the scripts needed to use Google Maps javascript framework.
	 * Requires a valid Google API Key.
	 *
	 * @return 	void
	 */
	public static function load_googlemaps()
	{
		$vik 	= UIApplication::getInstance();
		$config = UIFactory::getConfig();
		
		$key = $config->get('googleapikey', '');
		
		$vik->addScript('https://maps.googleapis.com/maps/api/js?key=' . $key);
	}

	/**
	 * Loads the scripts needed to use Colorpicker jQuery plugin.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	public static function load_colorpicker()
	{
		$doc = JFactory::getDocument();
		$vik = UIApplication::getInstance();
		
		$vik->addScript(VAPASSETS_URI . 'js/colorpicker/colorpicker.js');
		$vik->addScript(VAPASSETS_URI . 'js/colorpicker/eye.js');
		$vik->addScript(VAPASSETS_URI . 'js/colorpicker/utils.js');

		$doc->addStyleSheet(VAPASSETS_URI . 'css/colorpicker/colorpicker.css');
	}

	/**
	 * Loads the javascript utils.
	 *
	 * @param 	array  $options  A list of options for the scripts to load.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	public static function load_utils(array $options = array())
	{
		static $loaded = 0;

		if ($loaded)
		{
			return;
		}

		$loaded = 1;

		$default = array(
			'version' => VIKAPPOINTMENTS_SOFTWARE_VERSION,
		);

		UIApplication::getInstance()->addScript(VAPASSETS_URI . 'js/utils.js', array_merge($default, $options));
	}

	/**
	 * Loads the javascript utils and configure the Currency JS object.
	 *
	 * @return 	void
	 *
	 * @uses 	load_utils()
	 *
	 * @since 	1.6
	 */
	public static function load_currency_js()
	{
		self::load_utils();

		$config = UIFactory::getConfig();

		$options = new stdClass;
		$options->position  = $config->getUint('currsymbpos', 1);
		$options->separator = $config->getString('currdecimalsep', '.');
		$options->digits 	= $config->getUint('currdecimaldig', 2);

		$symbol = $config->getString('currencysymb');

		JFactory::getDocument()->addScriptDeclaration("Currency.getInstance('{$symbol}', " . json_encode($options) . ");");
	}

	/**
	 * Prepares the datepicker regional object.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	public static function load_datepicker_regional()
	{	
		// Labels
		$done 	= JText::_('VAPJQCALDONE');
		$prev 	= JText::_('VAPJQCALPREV');
		$next 	= JText::_('VAPJQCALNEXT');
		$today 	= JText::_('VAPJQCALTODAY');
		$wk 	= JText::_('VAPJQCALWKHEADER');

		// Months
		$months = array(
			JText::_('JANUARY'),
			JText::_('FEBRUARY'),
			JText::_('MARCH'),
			JText::_('APRIL'),
			JText::_('MAY'),
			JText::_('JUNE'),
			JText::_('JULY'),
			JText::_('AUGUST'),
			JText::_('SEPTEMBER'),
			JText::_('OCTOBER'),
			JText::_('NOVEMBER'),
			JText::_('DECEMBER'),
		);

		$months_short = array(
			JText::_('JANUARY_SHORT'),
			JText::_('FEBRUARY_SHORT'),
			JText::_('MARCH_SHORT'),
			JText::_('APRIL_SHORT'),
			JText::_('MAY_SHORT'),
			JText::_('JUNE_SHORT'),
			JText::_('JULY_SHORT'),
			JText::_('AUGUST_SHORT'),
			JText::_('SEPTEMBER_SHORT'),
			JText::_('OCTOBER_SHORT'),
			JText::_('NOVEMBER_SHORT'),
			JText::_('DECEMBER_SHORT'),
		);

		$months 		= json_encode($months);
		$months_short 	= json_encode($months_short);

		// Days
		$days = array(
			JText::_('SUNDAY'),
			JText::_('MONDAY'),
			JText::_('TUESDAY'),
			JText::_('WEDNESDAY'),
			JText::_('THURSDAY'),
			JText::_('FRIDAY'),
			JText::_('SATURDAY'),
		);

		$days_short_3 = array(
			JText::_('SUN'),
			JText::_('MON'),
			JText::_('TUE'),
			JText::_('WED'),
			JText::_('THU'),
			JText::_('FRI'),
			JText::_('SAT'),
		);

		$days_short_2 = array();
		foreach ($days_short_3 as $d)
		{
			$days_short_2[] = mb_substr($d, 0, 2, 'UTF-8');
		}

		// snippet used to make sure the substring of
		// the week days doesn't return the same value (see Hebrew)
		// for all the elements
		$days_short_2 = array_unique($days_short_2);

		if (count($days_short_2) != count($days_short_3))
		{
			// the count doesn't match, use the 3 chars days
			$days_short_2 = $days_short_3;
		}

		$days 			= json_encode($days);
		$days_short_3 	= json_encode($days_short_3);
		$days_short_2 	= json_encode($days_short_2);

		// should return a value between 0-6 (1: Monday, 0: Sunday)
		/**
		 * @todo 	use internal setting to pick first week day if 
		 * 			VAPJQFIRSTDAY doesn't exist (see modules).
		 *
		 * 			Otherwise evaluate to pick FIRST DAY and IS RTL
		 * 			from language manifest.
		 */
		$start_of_week = (int) JText::_('VAPJQFIRSTDAY');

		if (JText::_('VAPJQISRTL') == 'true')
		{
			$isRtl = 'true';
		}
		else
		{
			$isRtl = 'false';
		}

		JFactory::getDocument()->addScriptDeclaration(
<<<JS
jQuery(function($){
	$.datepicker.regional["vikappointments"] = {
		closeText: "$done",
		prevText: "$prev",
		nextText: "$next",
		currentText: "$today",
		monthNames: $months,
		monthNamesShort: $months_short,
		dayNames: $days,
		dayNamesShort: $days_short_3,
		dayNamesMin: $days_short_2,
		weekHeader: "$wk",
		firstDay: $start_of_week,
		isRTL: $isRtl,
		showMonthAfterYear: false,
		yearSuffix: ""
	};

	$.datepicker.setDefaults($.datepicker.regional["vikappointments"]);
});
JS
		);
	}
	
	/**
	 * Creates a UNIX timestamp starting from a string date.
	 *
	 * @param 	string 	 $date 	The date to parse.
	 * @param 	integer  $hour 	The hours to use.
	 * @param 	integer  $min 	The minutes to use.
	 *
	 * @return 	integer  The resulting UNIX timestamp.
	 */
	public static function createTimestamp($date, $hour = 0, $min = 0)
	{
		$date_format = UIFactory::getConfig()->get('dateformat');

		if (JFactory::getDbo()->getNullDate() == $date)
		{
			/**
			 * Return invalid timestamp in case a NULL DB date was passed.
			 *
			 * @since 1.6
			 */

			return -1;
		}

		$df_separator = $date_format[1]; // second char of $date_format can be only [/.-]

		$formats 	= explode($df_separator, $date_format);
		$d_exp 		= explode($df_separator, $date);
		
		if (count($d_exp) != 3)
		{
			return -1;
		}
		
		$_attr = array();

		for ($i = 0, $n = count($formats); $i < $n; $i++)
		{
			$_attr[$formats[$i]] = $d_exp[$i];
		}

     
		
		return mktime((int) $hour, (int) $min, 0, (int) $_attr['m'], (int) $_attr['d'], (int) $_attr['Y']);
	}

public static function jcreateTimestamp($date, $hour = 0, $min = 0) {

        $date_format = UIFactory::getConfig()->get('dateformat');

        $df_separator = $date_format[1]; // second char of $date_format can be only ['/', '.', '-']

        $formats = explode($df_separator, $date_format);

        $d_exp = explode($df_separator, $date);

        if( count( $d_exp ) != 3 ) {
            return -1;
        }
        /*if($d_exp[2]>2000){
            return strtotime($date);
        }*/ 
       
        //$date = ArasJoomlaVikApp::dateJoomlaMode($date,4);  /// saber

        $_attr = array();

        for ($i = 0, $n = count($formats); $i < $n; $i++)
        {
            $_attr[$formats[$i]] = $d_exp[$i];
        }

     /*     $_attr = Array(
    [m] => 1394
    [d] => 05
    [Y] => 13
    )   */ 

          /// جای ایندکس ماه و سال و روز به علت نوع خروجی تاریخ شمسی جوملا تغییر داده شد
          // $_attr['d'] ماه
          // $_attr['Y'] روز
          // $_attr['m'] سال


        $timestamp = ArasJoomlaVikApp::jmktime(intval( $hour ), intval( $min ), 0, intval( $_attr['m'] ), intval( $_attr['d'] ), intval( $_attr['Y'] ) ); //// saber

        return $timestamp;
    }

	/**
	 * Returns a UNIX timestamp of the checkout.
	 *
	 * @param 	integer  $checkin 	The checkin timestamp of the appointment.
	 * @param 	integer  $duration 	The duration of the appointment (in minutes).
	 * 
	 * @return 	integer  The resulting UNIX timestamp.
	 *
	 * @since 	1.6
	 */
	public static function getCheckout($checkin, $duration)
	{
		$arr = ArasJoomlaVikApp::jgetdate($checkin);

		return ArasJoomlaVikApp::jmktime($arr['hours'], $arr['minutes'], $arr['seconds'] + $duration * 60, $arr['mon'], $arr['mday'], $arr['year']);
	}
	
	/**
	 * Checks if the given minute is a correct/supported interval.
	 *
	 * @param 	integer  $minute 	The minute value to check.
	 *
	 * @return 	boolean  True if correct, false otherwise.
	 */
	public static function isMinuteAnInterval($minute)
	{
		$min = VikAppointments::getMinuteIntervals();

		for ($i = 0; $i < 60; $i += $min)
		{
			if ($i == $minute)
			{
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Helper method used to calculate the right day with the given shift.
	 * It is used to display the correct position of the days depending on the
	 * first day of the week.
	 *
	 * @param 	integer  $day_index  The index of the day.
	 * @param 	integer  $shift 	 The index of the first day.
	 *
	 * @return 	integer  The resulting position
	 */
	public static function getShiftedDay($day_index, $shift)
	{
		if ($day_index + $shift < 7)
		{
			return $day_index + $shift;
		}
	 
		return $day_index + $shift - 7;
	}
	
	/**
	 * Helper method used to generate a serial code.
	 * In a remote case, this method may generate 2 identical codes.
	 * The probability to have 2 identical strings is:
	 * 1 / count($map)^$len
	 *
	 * @param 	integer  $len 	The length of the serial code. 
	 * @param 	array 	 $map 	A map containing all the allowed tokens.
	 *
	 * @return 	string 	 The resulting serial code.
	 */
	public static function generateSerialCode($len = 12, $map = null)
	{
		if (!$map)
		{
			$map = array(
				'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'0123456789'
			);
		}

		$_key = '';
		
		for ($i = 0; $i < $len; $i++)
		{
			$_row = rand(0, count($map) - 1);
			$_col = rand(0, strlen($map[$_row]) - 1);

			$_key .= (string) $map[$_row][$_col];
		}

		return $_key;
	}

	/**
	 * Checks if the given service has a own calendar.
	 *
	 * @param 	integer  $id_ser  The service ID.
	 * @param 	mixed  	 $dbo 	  The database object.
	 *
	 * @return 	boolean  True if own calendar, false otherwise.
	 */
	public static function hasServiceOwnCalendar($id_ser, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		if ($id_ser != -1)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('has_own_cal'))
				->from($dbo->qn('#__vikappointments_service'))
				->where($dbo->qn('id') . ' = ' . (int) $id_ser);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				return (bool) $dbo->loadResult();
			}
		}

		return false;
	}
	
	/**
	 * Helper method used to get all the reservations (with extended details) that belong
	 * to the specified employee and service.
	 *
	 * @param 	integer  $id_emp 	The employee ID.
	 * @param 	integer  $id_ser 	The service ID.
	 * @param 	integer  $start_ts 	The start of the time range.
	 * @param 	integer  $end_ts 	The end of the time range.
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	array 	 The list containing all the reservations found.
	 */
	public static function getAllEmployeeExtendedReservations($id_emp, $id_ser, $start_ts, $end_ts, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		// if id_ser NOT -1 and service has own calendar, don't consider the 
		// reservations of the other services (of the same employee)
		if (!self::hasServiceOwnCalendar($id_ser))
		{
			// don't apply own search (unset service)
			$id_ser = -1;
		}
		
		/*$q = "SELECT `r`.`id` AS `rid`, `r`.`checkin_ts` AS `checkin`, `r`.`people`, `r`.`duration` AS `rduration`, `r`.`total_cost` AS `total_cost`, `r`.`status` AS `status`, `r`.`sid` AS `rsid`, `r`.`purchaser_mail` AS `rmail`,
		`r`.`sleep` AS `rsleep`, `r`.`paid` AS `paid`, `r`.`tot_paid`, `r`.`id_payment`, `r`.`purchaser_nominative`, `e`.`id` AS `id_employee`, `e`.`nickname` AS `ename`, `s`.`name` AS `sname` 
		FROM `#__vikappointments_reservation` AS `r` 
		LEFT JOIN `#__vikappointments_employee` AS `e` ON `r`.`id_employee`=`e`.`id` 
		LEFT JOIN `#__vikappointments_service` AS `s` ON `r`.`id_service`=`s`.`id`  
		WHERE `r`.`status`<>'REMOVED' AND `r`.`status`<>'CANCELED' AND `e`.`id`=$id_emp AND 
		((`s`.`has_own_cal`=0 AND $id_ser=-1) OR (`s`.`has_own_cal`=1 AND `r`.`id_service`=$id_ser)) AND 
		$start_ts <= `r`.`checkin_ts` AND `r`.`checkin_ts` <= $end_ts 
		ORDER BY `r`.`checkin_ts`;";*/

		$excluded_status = array('REMOVED', 'CANCELED');

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('r.id', 'rid'),
				$dbo->qn('r.sid', 'rsid'),
				$dbo->qn('r.id_employee'),
				$dbo->qn('r.id_service'),
				$dbo->qn('r.checkin_ts', 'checkin'),
				$dbo->qn('r.people'),
				$dbo->qn('r.duration', 'rduration'),
				$dbo->qn('r.sleep', 'rsleep'),
				$dbo->qn('r.total_cost'),
				$dbo->qn('r.tot_paid'),
				$dbo->qn('r.paid'),
				$dbo->qn('r.id_payment'),
				$dbo->qn('r.status'),
				$dbo->qn('r.purchaser_nominative'),
				$dbo->qn('r.purchaser_mail', 'rmail'),
				$dbo->qn('r.closure'),
			))
			->select($dbo->qn('e.nickname', 'ename'))
			->select($dbo->qn('s.name', 'sname'));

		$q->from($dbo->qn('#__vikappointments_reservation', 'r'));
		$q->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'));
		$q->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'));

		$q->where(array(
			$dbo->qn('r.status') . ' NOT IN (' . implode(', ', array_map(array($dbo, 'q'), $excluded_status)) . ')',
			$dbo->qn('e.id') . ' = ' . (int) $id_emp,
			$dbo->qn('r.checkin_ts') . ' BETWEEN ' . (int) $start_ts . ' AND ' . (int) $end_ts,
		));

		/**
		 * Do not display closure records within the front-end.
		 *
		 * @since 1.6
		 */
		if (JFactory::getApplication()->isSite())
		{
			$q->where($dbo->qn('r.closure') . ' = 0');
		}

		/**
		 * (
		 * 	   (`s`.`has_own_cal` = 0 AND $id_ser          = -1     ) OR 
		 *     (`s`.`has_own_cal` = 1 AND `r`.`id_service` = $id_ser)
		 * )";
		 */
		$q->andWhere(array(
			'(' . $dbo->qn('s.has_own_cal') . ' = 0 AND ' . (int) $id_ser . ' = -1)',
			'(' . $dbo->qn('s.has_own_cal') . ' = 1 AND ' . (int) $id_ser . ' = ' . $dbo->qn('r.id_service') . ')',
		), 'OR');

		$q->order($dbo->qn('r.checkin_ts') . ' ASC');
			
		$dbo->setQuery($q);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			return array();
		}

		$rows = $dbo->loadAssocList();
		for ($i = 0; $i < count($rows); $i++)
		{
			$rows[$i]['pname'] = '';
			if ($rows[$i]['id_payment'] != -1)
			{
				$p = self::getPayment($rows[$i]['id_payment'], false);
				if (count($p) > 0)
				{
					$rows[$i]['pname'] = $p['name'];
				}
			}
		}
		
		return $rows;
	}
	
	/**
	 * Returns the list of the employee reservations for the given day.
	 * In order to support midnight reservations (that starts on a day and ends on the next one),
	 * it is needed to return also the reservations for the previous day and for the next day.
	 *
	 * @param 	integer  $id_emp 	The employee ID.
	 * @param 	integer  $id_ser 	The service ID.
	 * @param 	integer  $start_ts 	The starting delimiter (UNIX timestamp).
	 * @param 	integer  $end_ts 	The ending delimiter (UNIX timestamp)
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	array 	 A list of matching reservations.
	 */
	public static function getAllEmployeeReservations($id_emp, $id_ser, $start_ts, $end_ts, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		// if id_ser NOT -1 and service has own calendar -> don't consider the reservations of the other services (of the same employee)
		if (!self::hasServiceOwnCalendar($id_ser))
		{
			// don't apply own search
			$id_ser = -1; // unset service
		}

		$bounds = array($start_ts, $end_ts);

		if ($start_ts + 86399 == $end_ts)
		{
			/**
			 * We are looking for the reservations for the current day.
			 * Extend this bounds in order to support midnight reservations.
			 *
			 * Instead having:
			 * 2018-07-09 @ 00:00:00 - 2018-07-09 23:59:59,
			 * we need to have :
			 * 2018-07-08 @ 00:00:00 - 2018-07-10 23:59:59
			 *
			 * @since 1.6
			 */
			$start_ts 	= strtotime('-1 day 00:00:00', $bounds[0]);
			$end_ts 	= strtotime('+1 day 23:59:59', $bounds[0]);
		}

		$q = "SELECT `r`.`checkin_ts`, `r`.`duration`, `r`.`sleep`, `r`.`people`, SUM(`r`.`people`) AS `people_count`, `r`.`id`, `r`.`id_service`, `r`.`closure`
		FROM `#__vikappointments_reservation` AS `r` 
		LEFT JOIN `#__vikappointments_service` AS `s` ON `r`.`id_service`=`s`.`id`
		WHERE `r`.`status`<>'REMOVED' AND `r`.`status`<>'CANCELED' AND `r`.`id_employee`=$id_emp AND 
		((`s`.`has_own_cal`=0 AND $id_ser=-1) OR (`s`.`has_own_cal`=1 AND `r`.`id_service`=$id_ser)) AND 
		$start_ts <= `r`.`checkin_ts` AND `r`.`checkin_ts` <= $end_ts GROUP BY `r`.`checkin_ts` ORDER BY `r`.`checkin_ts`;";

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$list = $dbo->loadAssocList();

			foreach ($list as $i => $b)
			{
				if ($list[$i]['checkin_ts'] < $bounds[0])
				{
					$day = -1;
				}
				else if ($list[$i]['checkin_ts'] > $bounds[1])
				{
					$day = 1;
				}
				else
				{
					$day = 0;
				}

				$list[$i]['@day'] = $day;
			}

			return $list;
		}
		
		return array();
	}
	
	/**
	 * Returns the list of the employee reservations for the given day excluding the specified ID.
	 * In order to support midnight reservations (that starts on a day and ends on the next one),
	 * it is needed to return also the reservations for the previous day and for the next day.
	 *
	 * @param 	integer  $id_emp 	The employee ID.
	 * @param 	integer  $id_ser 	The service ID.
	 * @param 	integer  $no_id 	The reservation ID to exclude.
	 * @param 	integer  $start_ts 	The starting delimiter (UNIX timestamp).
	 * @param 	integer  $end_ts 	The ending delimiter (UNIX timestamp)
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	array 	 A list of matching reservations.
	 *
	 * @uses 	getAllEmployeeReservations()
	 */
	public static function getAllEmployeeReservationsExcludingResId($id_emp, $id_ser, $no_id, $start_ts, $end_ts, $dbo = null)
	{
		$bookings = self::getAllEmployeeReservations($id_emp, $id_ser, $start_ts, $end_ts, $dbo);

		if ($no_id > 0 && $bookings)
		{
			$i = 0;
			while ($i < count($bookings) && $bookings[$i]['id'] != $no_id)
			{
				// iterate while the current booking is not the one to exclude
				$i++;
			}

			if ($i < count($bookings))
			{
				// booking found, splice the array
				array_splice($bookings, $i, 1);
			}
		}

		return $bookings;
	}
	
	/**
	 * Returns the list of the service reservations for the given day.
	 *
	 * @param 	integer  $id_ser 	The service ID.
	 * @param 	integer  $start_ts 	The starting delimiter (UNIX timestamp).
	 * @param 	integer  $end_ts 	The ending delimiter (UNIX timestamp)
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	array 	 A list of matching reservations.
	 */
	public static function getAllServiceReservations($id_ser, $start_ts, $end_ts, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}
		
		$q = "SELECT `r`.`checkin_ts`, `r`.`duration`, `r`.`sleep`, `r`.`people` AS `people_count` 
		FROM `#__vikappointments_reservation` AS `r` 
		LEFT JOIN `#__vikappointments_ser_emp_assoc` AS `a` ON `a`.`id_employee`=`r`.`id_employee`
		WHERE `r`.`status`<>'REMOVED' AND `r`.`status`<>'CANCELED' 
		AND `a`.`id_service`=".$id_ser." AND ".$start_ts." <= `r`.`checkin_ts` AND (`r`.`checkin_ts`+`r`.`duration`*60+`r`.`sleep`*60) <= ".$end_ts." ORDER BY `r`.`checkin_ts`;";
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$app = $dbo->loadAssocList();
			
			return $app;
		}
		
		return array();
	}
	
	/**
	 * Evaluates all the bookings that intersect the specified day.
	 *
	 * @param 	array 	 $bookings 		The bookings list.
	 * @param 	integer  $curr_index 	The current index of the list, in order to ignore 
	 * 									all the records lower than this value.
	 * @param 	integer  $start 		The UNIX timestamp of the day.
	 *
	 * @return 	array 	 An array containing the following properties:
	 * 					 0: integer  num of bookings evaluated;
	 * 					 1: array 	 daily hour reservations found.
	 *
	 * @uses 	isBetween()
	 */
	public static function evaluateBookingArray($bookings, $curr_index, $start)
	{
		if (!count($bookings))
		{
			// no bookings
			return array(0, array());	
		}

		$end = strtotime('23:59:59', $start);
		
		$skip = $curr_index;
		while ($skip < count($bookings) && $bookings[$skip]['checkin_ts'] < $start)
		{
			$skip++;
		}

		if ($skip)
		{
			/**
			 * Consider also the previous booking as it may be straddling 2 different days (midnight appointments).
			 *
			 * @since 1.6
			 */ 
			$skip--;
		}

		$same_day = true;
		$rows 	  = array();

		for ($i = $skip, $n = count($bookings); $i < $n && $bookings[$i]['checkin_ts'] <= $end; $i++)
		{
			$same_day = self::isBetween($bookings[$i]['checkin_ts'], $start, $end);

			if (!$same_day)
			{
				/**
				 * Fallback to check if the checkout intersects the delimiters.
				 *
				 * @since 1.6
				 */
				$checkout = self::getCheckout($bookings[$i]['checkin_ts'], $bookings[$i]['duration']);
				$same_day = self::isBetween($checkout, $start + 1, $end); // -1 is used to make sure the checkout is not equals to the start delimiter
			}
			
			if ($same_day)
			{
				// $rows[$i - $skip] = $bookings[$i];
				$rows[] = $bookings[$i];
			}
		}
		
		return array($skip + count($rows), $rows);
	}
	
	/**
	 * Checks if the given value is between the 2 delimiters.
	 *
	 * @param 	integer  $val 	 The value to check.
	 * @param 	integer  $start  The starting delimiter.
	 * @param 	integer  $end 	 The ending delimiter.
	 *
	 * @return 	boolean  True if the value is between the delimiters, false otherwise.
	 */
	public static function isBetween($val, $start, $end)
	{
		return $start <= $val && $val <= $end;
	}
	
	/**
	 * Checks if the specified employee is not fully occupied for the given day.
	 *
	 * @param 	integer  $id_emp 	  The employee ID.
	 * @param 	integer  $id_ser 	  The service ID.
	 * @param 	array 	 $arr_res 	  An array containing all the daily reservations.
	 * @param 	integer  $day_ts 	  The day UNIX timestamp.
	 * @param 	integer  $max_people  The maximum number of people.
	 * @param  	mixed 	 $dbo 		  The database object.
	 * @param 	array 	 $locations   The locations array.
	 *
	 * @return 	boolean  False if fully occupied, otherwise true.
	 */
	public static function isFreeIntervalOnDay($id_emp, $id_ser, $arr_res, $day_ts, $max_people, $dbo = null, $locations = array())
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}
		
		$worktime = self::getEmployeeWorkingTimes($id_emp, $id_ser, $day_ts, $locations);
		
		foreach ($arr_res as $r)
		{
			$from = getdate($r['checkin_ts']);
			$from = ($from['hours'] * 60) + $from['minutes'];
			$to   = $from + $r['duration'] + $r['sleep'];
			
			$w_index = 0;
			for ($w_index; $w_index < count($worktime) && ($worktime[$w_index]['fromts'] > $from || $from > $worktime[$w_index]['endts']); $w_index++);
			
			if ($w_index < count($worktime))
			{
				if (empty($worktime[$w_index]['counter']))
				{
					$worktime[$w_index]['counter'] = 0;
				}
			
				if ($r['people_count'] >= $max_people)
				{
					$worktime[$w_index]['counter'] += $to - $from;
				}
			}
		}
		
		foreach ($worktime as $w)
		{
			if (empty($w['counter']) || $w['counter'] < ($w['endts'] - $w['fromts']))
			{
				return true;
			}
		}
				
		return false;
	}
	
	/**
	 * Checks if the specified service owns at least an employee that
	 * is not fully occupied for the given day.
	 *
	 * @param 	array 	 $employees	  The employees IDs.
	 * @param 	integer  $id_ser 	  The service ID.
	 * @param 	array 	 $arr_res 	  An array containing all the daily reservations.
	 * @param 	integer  $day_ts 	  The day UNIX timestamp.
	 * @param  	mixed 	 $dbo 		  The database object.
	 * @param 	array 	 $locations   The locations array.
	 *
	 * @return 	boolean  False if fully occupied, otherwise true.
	 */
	public static function isFreeIntervalOnDayService($employees, $id_ser, $arr_res, $day_ts, $dbo = null, $locations = array())
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}
		
		foreach ($employees as $emp)
		{	
			$worktime = self::getEmployeeWorkingTimes($emp['id'], $id_ser, $day_ts, $locations);
			
			if (count($worktime))
			{
				foreach ($arr_res as $r)
				{
					$from = getdate($r['checkin_ts']);
					$from = ($from['hours'] * 60) + $from['minutes'];
					$to   = $from + $r['duration'] + $r['sleep'];
					
					$w_index = 0;
					for ($w_index; $w_index < count($worktime) && ($worktime[$w_index]['fromts'] > $from || $from > $worktime[$w_index]['endts']); $w_index++);
					
					if ($w_index < count($worktime))
					{
						if (empty($worktime[$w_index]['counter']))
						{
							$worktime[$w_index]['counter'] = 0;
						}
						
						$worktime[$w_index]['counter'] += $to-$from;
					}
				}
				
				foreach ($worktime as $w)
				{
					if (empty($w['counter']) || $w['counter'] < ($w['endts'] - $w['fromts']))
					{
						return true;
					}
				}
			}
		}
		
		return false;
	}

	/**
	 * Checks if the specified service owns at least an employee that
	 * is not fully occupied for the given day. This method supports
	 * services with maximum capacity higher than 1.
	 *
	 * @param 	array 	 $employees	    The employees IDs.
	 * @param 	integer  $id_ser 	    The service ID.
	 * @param 	array 	 $arr_res 	    An array containing all the daily reservations.
	 * @param 	integer  $day_ts 	    The day UNIX timestamp.
	 * @param 	integer  $max_capacity 	The maximum number of people.
	 * @param  	mixed 	 $dbo 		    The database object.
	 * @param 	array 	 $locations     The locations array.
	 *
	 * @return 	boolean  False if fully occupied, otherwise true.
	 *
	 * @since 	1.2
	 */
	public static function isFreeIntervalOnDayGroupService($employees, $id_ser, $arr_res, $day_ts, $max_capacity, $dbo = null, $locations = array())
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}
		
		foreach ($employees as $emp)
		{	
			$worktime = self::getEmployeeWorkingTimes($emp['id'], $id_ser, $day_ts, $locations);
			
			if (count($worktime))
			{
				$cont_people = 0;

				for ($i = 0; $i < count($arr_res); $i++)
				{
					$r = $arr_res[$i];
					$cont_people += $r['people_count'];

					if ($i == count($arr_res) - 1 || $arr_res[$i + 1]['checkin_ts'] != $r['checkin_ts'])
					{
						$from = getdate($r['checkin_ts']);
						$from = ($from['hours'] * 60) + $from['minutes'];
						$to   = $from + $r['duration'] + $r['sleep'];
						
						$w_index = 0;
						for ($w_index; $w_index < count($worktime) && ($worktime[$w_index]['fromts'] > $from || $from > $worktime[$w_index]['endts']); $w_index++);
						
						if (empty( $worktime[$w_index]['counter']))
						{
							$worktime[$w_index]['counter'] = 0;
						}
						
						if ($cont_people >= $max_capacity)
						{
							$worktime[$w_index]['counter'] += $to - $from;
						}
						
						$cont_people = 0;
					}
				}
				
				foreach ($worktime as $w)
				{
					if (empty($w['counter']) || $w['counter'] < ($w['endts'] - $w['fromts']))
					{
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Method used to obtain a list of reservations at the given date and time.
	 *
	 * @param 	integer  $id_emp 	The employee ID.
	 * @param 	integer  $checkin 	The reservations checkin.
	 * @param 	mixed 	 $dbo 		The database object.
	 * @param 	mixed 	 $q 		A query builder used to overwrite SELECT and FROM statements.
	 *
	 * @return 	array 	 The list of matching reservations.
	 */
	public static function getEmployeeAppointmentAt($id_emp, $checkin, $dbo = null, $q = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		if (!$q)
		{
			$q = $dbo->getQuery(true);

			$q->select($dbo->qn('r.id', 'rid'))->from($dbo->qn('#__vikappointments_reservation', 'r'));
		}

		if ($id_emp)
		{
			$q->where($dbo->qn('r.id_employee') . ' = ' . (int) $id_emp);
		}

		$q->where(array(
			$dbo->qn('r.status') . ' IN (\'CONFIRMED\', \'PENDING\')',
			$dbo->qn('r.checkin_ts') . ' <= ' . (int) $checkin,
			(int) $checkin . ' < (' . $dbo->qn('r.checkin_ts') . ' + ' . $dbo->qn('r.duration') . ' * 60 + ' . $dbo->qn('r.sleep') . ')',
		));
	
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadAssocList();
		}
		
		return array();
	}
	
	/**
	 * Elaborates the timeline by intersecting the worktimes and the bookings found.
	 *
	 * @param 	array 	 $worktime 	The list of the available working times.
	 * @param 	array 	 $bookings 	The reservations to intersect.
	 * @param 	array 	 $service 	The service details.
	 *
	 * @return 	array  	 A list of associative arrays containing the elaborated timeline.
	 * 					 The keys contain the time (hour * 60 + min) and the values
	 * 					 contain the status (0: blocked, 1: available, 2: not enough space).
	 */
	public static function elaborateTimeLine($worktime, $bookings, $service)
	{	
		/*$min_int = 0;
		if( $service['interval'] == 1 ) {
			$min_int = $service['duration']+$service['sleep'];
		} else {
			$min_int = self::getMinuteIntervals();
		}*/
		
		$min_int = self::getMinuteIntervals();
		if ($service['interval'] == 1)
		{
			$min_int = 5;
		}
		
		$arr = array();
		
		for ($i = 0; $i < count($worktime); $i++)
		{
			//for( $j = $worktime[$i]['fromts'], $len = 0; $j < $worktime[$i]['endts']; $j+=$min_int) {
			for ($j = $worktime[$i]['fromts'], $len = 0; ($j + $min_int) <= $worktime[$i]['endts']; $j += $min_int)
			{
				$arr[$i][$j] = 1;
			}
		}
		
		foreach ($bookings as $b)
		{
			$date  = getdate($b['checkin_ts']);
			$start = ($date['hours'] * 60) + $date['minutes'];

			if (isset($b['@day']))
			{
				/**
				 * Check the day factor of a reservation to check if it is referring
				 * to the current day or if it close to the bounds of this working time.
				 * Used to support midnight reservations.
				 *
				 * @since 1.6
				 */

				if ($b['@day'] == 1)
				{
					// we are evaluating a reservation for the next day, so we need to increase the 
					// initial time by 1440 minutes (24 hours * 60).
					$start += 1440;
				}
				else if ($b['@day'] == -1)
				{
					// we are evaluating a reservation for the previous day, so we need to decrease the 
					// initial time by 1440 minutes (24 hours * 60).
					$start -= 1440;
				}
			}

			for ($i = $start; $i < $start + $b['duration'] + $b['sleep']; $i += $min_int)
			{
				$found = false;
				for ($j = 0; $j < count($arr) && !$found; $j++)
				{
					if (!empty($arr[$j][$i]))
					{
						$found = true;
						$arr[$j][$i] = 0;
					}
				}
			}
		}
		
		if ($service['interval'] != 1)
		{
			$n_step = $service['duration'] + $service['sleep'];
			
			for ($i = 0; $i < count($arr); $i++)
			{
				$step = 0;
				//for( $j = $worktime[$i]['fromts'], $len = 0; $j < $worktime[$i]['endts']; $j+=$min_int) {
				for ($j = $worktime[$i]['fromts'], $len = 0; ($j + $min_int) <= $worktime[$i]['endts']; $j += $min_int)
				{
					if ($arr[$i][$j] == 1)
					{
						$step += $min_int;
						if ($step >= $n_step)
						{
							$step-=$min_int;
						}
					}
					else
					{
						if ($step != 0 && $step < $n_step)
						{
							for ($back = $j - $min_int; $back >= $j - $step; $back -= $min_int)
							{
								$arr[$i][$back] = 2;
							}
						}
						
						$step = 0;
					}
				}
				
				if ($step != 0 && $step < $n_step)
				{
					for ($back = $j - $min_int; $back >= $j - $step; $back -= $min_int)
					{
						$arr[$i][$back] = 2;
					}
				}
			}
		}

		$mod = round(($service['duration'] + $service['sleep']) / $min_int);

		if ($service['interval'] == 1 && $mod != 1)
		{
			$new_arr = array();
			
			for ($i = 0; $i < count($arr); $i++)
			{
				$new_arr[$i] = array();
				$value = 1;
				$start = 0;
				$all_free = true;
				
				$count = 0;
				
				for ($j = $worktime[$i]['fromts']; $j < $worktime[$i]['endts']; $j += $min_int, $count++)
				{
					if ($count % $mod == 0)
					{
						$start = $j;
						$value = 1;
						$all_free = true;
					}
					
					$hourmin = intval($j / 60) . ' : ' . ($j % 60);
					if ($arr[$i][$j] == 0)
					{
						$all_free = false;
					}

					$value &= ($arr[$i][$j] == 2 ? 0 : $arr[$i][$j]); 
					
					if ((($count + 1) % $mod == 0 || $j + $min_int == $worktime[$i]['endts']))
					{
						// LAST TIME SLOTS is not enough length
						if (($count+1) % $mod != 0)
						{
							$value = 0;
						}
						
						if ($value == 0 && $all_free)
						{
							$value = 2;
						}

						$new_arr[$i][$start] = $value;
					}
				}
			}
			
			$arr = $new_arr;
		}
		
		return $arr;
	}

	/**
	 * Elaborates the timeline by intersecting the worktimes and the bookings found.
	 * Filters the arrays of timelines to support a single associative array.
	 *
	 * @param 	array 	 $worktime 	The list of the available working times.
	 * @param 	array 	 $bookings 	The reservations to intersect.
	 * @param 	array 	 $service 	The service details.
	 *
	 * @return 	array  	 An associative array containing the elaborated timeline.
	 * 					 The keys contain the time (hour * 60 + min) and the values
	 * 					 contain the status (0: blocked, 1: available, 2: not enough space).
	 *
	 * @uses 	elaborateTimeLine()
	 */
	public static function elaborateTimeLineService($worktime, $bookings, $service)
	{
		$arr = self::elaborateTimeLine($worktime, $bookings, $service);
		
		$timeline = array();
		foreach ($arr as $a)
		{
			foreach ($a as $hour => $val)
			{
				$timeline[$hour] = $val;
			}
		}
		
		return $timeline;
	}
	
	/**
	 * Elaborates the timeline by intersecting the worktimes and the bookings found.
	 * This method accepts multiple bookings at the same date and time depending on
	 * the maximum capacity defined by the given service.
	 *
	 * @param 	array 	 $worktime 	The list of the available working times.
	 * @param 	array 	 $bookings 	The reservations to intersect.
	 * @param 	array 	 $service 	The service details.
	 * @param 	integer  $people 	The number of specified people.
	 * @param 	mixed 	 &$seats 	An array containing the remaining seats for each time.
	 *
	 * @return 	array  	 An associative array containing the elaborated timeline.
	 * 					 The keys contain the time (hour * 60 + min) and the values
	 * 					 contain the status (0: blocked, 1: available, 2: not enough space).
	 */
	public static function elaborateTimeLineGroupService($worktime, $bookings, $service, $people = 1, &$seats = null)
	{	
		$min_int = 0;

		if ($service['interval'] == 1)
		{
			$min_int = $service['duration'] + $service['sleep'];
		}
		else
		{
			$min_int = self::getMinuteIntervals();
		}
		
		$arr = array();
		
		for ($i = 0; $i < count($worktime); $i++)
		{
			//for( $j = $worktime[$i]['fromts'], $len = 0; $j < $worktime[$i]['endts']; $j+=$min_int) {
			for ($j = $worktime[$i]['fromts'], $len = 0; ($j + $min_int) <= $worktime[$i]['endts']; $j += $min_int)
			{
				$arr[$i][$j] = 1;
			}
		}
		
		$cont_people = 0;
		for ($k = 0; $k < count($bookings); $k++)
		{
			$b = $bookings[$k];
			
			$cont_people += $b['people_count'];
			if ($k == count($bookings) - 1 || $bookings[$k + 1]['checkin_ts'] != $b['checkin_ts'])
			{
				$date  = getdate($b['checkin_ts']);
				$start = ($date['hours'] * 60) + $date['minutes'];

				if (isset($b['@day']))
				{
					/**
					 * Check the day factor of a reservation to check if it is referring
					 * to the current day or if it close to the bounds of this working time.
					 * Used to support midnight reservations.
					 *
					 * @since 1.6
					 */

					if ($b['@day'] == 1)
					{
						// we are evaluating a reservation for the next day, so we need to increase the 
						// initial time by 1440 minutes (24 hours * 60).
						$start += 1440;
					}
					else if ($b['@day'] == -1)
					{
						// we are evaluating a reservation for the previous day, so we need to decrease the 
						// initial time by 1440 minutes (24 hours * 60).
						$start -= 1440;
					}
				}

				for ($i = $start; $i < $start + $b['duration'] + $b['sleep']; $i += $min_int)
				{
					$found = false;
					for ($j = 0; $j < count($arr) && !$found; $j++)
					{
						/**
						 * Try to block appointments that come from a different service or
						 * if the number of people exceeds the total capacity.
						 *
						 * @since 1.6 	check if the services are different only if $arr[$j][$i] is set
						 */
						if (!empty($arr[$j][$i]) && ($cont_people + $people > $service['max_capacity'] || $b['id_service'] != $service['id'] || $b['closure']))
						{
							// if $b['id_service'] doesn't exist, take a look at the VikAppointments::getAllEmployeeReservations() function
							// if $service['id'] doesn't exist, take a look at the VikAppointmentsController::get_day_time_line() and VikAppointmentsController::get_day_time_line_service() functions
							$found = true;
							$arr[$j][$i] = 0;
						}

						/**
						 * If $seats argument is an array, push the remaining seats.
						 * 
						 * @since 1.6
						 */
						if (is_array($seats))
						{
							if ($b['id_service'] == $service['id'] && !$b['closure'])
							{
								// same service, we can display the remaining seats
								$seats[$i] = $service['max_capacity'] - $cont_people;
							}
							else
							{
								// booked for a different service, unset the remaining seats
								$seats[$i] = 0;
							}
						}
					}

					/**
					 * We may have different service that display shifted
					 * timelines. This would cause an issue as previous check
					 * ignores the times that don't matches the evaluated slots.
					 *
					 * We need to unset here all the times that intersect with
					 * an existing reservation, which might have been created for
					 * a different service.
					 *
					 * @since 1.6.2
					 */
					if (!$found)
					{
						// find all slots that intersect this one
						for ($j = 0; $j < count($arr); $j++)
						{
							foreach ($arr[$j] as $arr_hm => &$v)
							{
								if (($start < $arr_hm && $arr_hm < $start + $b['duration'] + $b['sleep'])
									|| ($arr_hm < $start && $start < $arr_hm + $service['duration'] + $service['sleep']))
								{
									$v = 0;
								}
							}
						}
					}
				}
				
				$cont_people = 0;
			}
		}
		
		$n_step = $service['duration'] + $service['sleep'];

		/*
		
		for( $i = 0; $i < count($arr); $i++ ) {
			$step = 0;
			//for( $j = $worktime[$i]['fromts'], $len = 0; $j < $worktime[$i]['endts']; $j+=$min_int ) {
			for( $j = $worktime[$i]['fromts']; ($j+$min_int) <= $worktime[$i]['endts']; $j+=$min_int ) {
				if( $arr[$i][$j] == 1 ) {
					$step+=$min_int;
					if( $step == $n_step ) {
						$step-=$min_int;
					}
				} else {
					if( $step != 0 && $step < $n_step ) {
						for( $back = $j-$min_int; $back >= $j-$step; $back-=$min_int ) {
							$arr[$i][$back] = 2;
						}
					}
					
					$step = 0;
				}
			}
			
			if( $step != 0 && $step < $n_step ) {
				for( $back = $j-$min_int; $back >= $j-$step; $back-=$min_int ) {
					$arr[$i][$back] = 2;
				}
			}
		}

		*/

		// array deep : elaborate each timeline 
		for ($level = 0; $level < count($arr); $level++)
		{
			// get all the times in the current timeline
			$keys = array_keys($arr[$level]);
			// insert the end working time to evaluate properly the last available time
			$keys[] = $worktime[$level]['endts'];

			for ($i = 0; $i < count($keys)-1; $i++)
			{
				$last_index = -1;

				for ($j = $i + 1; $j < count($keys) && $last_index == -1; $j++)
				{
					/**
					 * If index is last or if current time is not available.
					 *
					 * @since 1.6 	Use empty($arr[$level][$keys[$j]]) to avoid "Undefined Index" notices.
					 * 				These notices may be raised when the reservations were stored for certain
					 * 				times that don't exist anymore.
					 */
					// if ($keys[$j] == count($keys) -1 || $arr[$level][$keys[$j]] == 0)
					if ($keys[$j] == count($keys) -1 || empty($arr[$level][$keys[$j]]))
					{
						// store last index found and stop for statement
						$last_index = $j;
					}
				}

				// if subtraction of last index found with current index is not enough
				if ($keys[$last_index] - $keys[$i] < $n_step)
				{
					// if current time is still available
					if ($arr[$level][$keys[$i]] == 1)
					{
						// mark current time as no more available
						$arr[$level][$keys[$i]] = 2;
					}
				}
			}
		}
		
		$timeline = array();
		foreach ($arr as $a)
		{
			foreach ($a as $hour => $val)
			{
				$timeline[$hour] = $val;
			}
		}
		
		return $timeline;
	}

	// TIMEZONE

	/**
	 * Elaborates the timeline by intersecting the worktimes and the bookings found.
	 * This method should be used in case the times need to be adjusted to the
	 * employee timezone.
	 *
	 * @param 	array 	 $worktime 	The list of the available working times.
	 * @param 	array 	 $bookings 	The reservations to intersect.
	 * @param 	array 	 $service 	The service details.
	 * @param 	string 	 $timezone  The timezone string.
	 *
	 * @return 	array  	 A list of associative arrays containing the elaborated timeline.
	 * 					 The keys contain the time (hour * 60 + min) and the values
	 * 					 contain the status (0: blocked, 1: available, 2: not enough space).
	 *
	 * @since 	1.4
	 */
	public static function elaborateTimeLineTimezone($worktime, $bookings, $service, $timezone)
	{	
		$min_int = self::getMinuteIntervals();
		if ($service['interval'] == 1)
		{
			$min_int = 5;
		}
		
		$arr = array();
		
		for ($i = 0; $i < count($worktime); $i++)
		{
			for ($j = $worktime[$i]['fromts'], $len = 0; ($j + $min_int) <= $worktime[$i]['endts']; $j += $min_int)
			{
				$arr[$i][$j] = 1;
			}
		}
		
		self::setCurrentTimezone($timezone);
		
		foreach ($bookings as $b)
		{
			$date  = getdate($b['checkin_ts']);
			$start = $date['hours'] * 60 + $date['minutes'];

			if (isset($b['@day']))
			{
				/**
				 * Check the day factor of a reservation to check if it is referring
				 * to the current day or if it close to the bounds of this working time.
				 * Used to support midnight reservations.
				 *
				 * @since 1.6
				 */

				if ($b['@day'] == 1)
				{
					// we are evaluating a reservation for the next day, so we need to increase the 
					// initial time by 1440 minutes (24 hours * 60).
					$start += 1440;
				}
				else if ($b['@day'] == -1)
				{
					// we are evaluating a reservation for the previous day, so we need to decrease the 
					// initial time by 1440 minutes (24 hours * 60).
					$start -= 1440;
				}
			}

			for ($i = $start; $i < $start + $b['duration'] + $b['sleep']; $i += $min_int)
			{
				$found = false;
				for ($j = 0; $j < count($arr) && !$found; $j++)
				{
					if (!empty($arr[$j][$i]))
					{
						$found = true;
						$arr[$j][$i] = 0;
					}
				}

				/**
				 * We may have different service that display shifted
				 * timelines. This would cause an issue as previous check
				 * ignores the times that don't matches the evaluated slots.
				 *
				 * We need to unset here all the times that intersect with
				 * an existing reservation, which might have been created for
				 * a different service.
				 *
				 * @since 1.6.2
				 */
				if (!$found)
				{
					// find all slots that intersect this one
					for ($j = 0; $j < count($arr); $j++)
					{
						foreach ($arr[$j] as $arr_hm => &$v)
						{
							if (($start < $arr_hm && $arr_hm < $start + $b['duration'] + $b['sleep'])
								|| ($arr_hm < $start && $start < $arr_hm + $service['duration'] + $service['sleep']))
							{
								$v = 0;
							}
						}
					}
				}
			}
		}
		
		if ($service['interval'] != 1)
		{
			$n_step = $service['duration'] + $service['sleep'];
			
			for ($i = 0; $i < count($arr); $i++)
			{
				$step = 0;
				//for( $j = $worktime[$i]['fromts'], $len = 0; $j < $worktime[$i]['endts']; $j+=$min_int) {
				for ($j = $worktime[$i]['fromts'], $len = 0; ($j + $min_int) <= $worktime[$i]['endts']; $j += $min_int)
				{
					if ($arr[$i][$j] == 1)
					{
						$step += $min_int;
						if ($step >= $n_step)
						{
							$step-=$min_int;
						}
					}
					else
					{
						if ($step != 0 && $step < $n_step)
						{
							for ($back = $j - $min_int; $back >= $j - $step; $back -= $min_int)
							{
								$arr[$i][$back] = 2;
							}
						}
						
						$step = 0;
					}
				}
				
				if ($step != 0 && $step < $n_step)
				{
					for ($back = $j - $min_int; $back >= $j - $step; $back -= $min_int)
					{
						$arr[$i][$back] = 2;
					}
				}
			}

		}

		$mod = round(($service['duration'] + $service['sleep']) / $min_int);

		if ($service['interval'] == 1 && $mod != 1)
		{
			$new_arr = array();
			
			for ($i = 0; $i < count($arr); $i++)
			{
				$new_arr[$i] = array();
				$value = 1;
				$start = 0;
				$all_free = true;
				
				$count = 0;
				for ($j = $worktime[$i]['fromts']; $j < $worktime[$i]['endts']; $j += $min_int, $count++)
				{
					if ($count % $mod == 0)
					{
						$start = $j;
						$value = 1;
						$all_free = true;
					}
					
					$hourmin = intval($j / 60) . ' : ' . ($j % 60);
					if ($arr[$i][$j] == 0)
					{
						$all_free = false;
					}
					$value &= ($arr[$i][$j] == 2 ? 0 : $arr[$i][$j]);
					
					if ((($count + 1) % $mod == 0 || $j + $min_int == $worktime[$i]['endts']))
					{
						// LAST TIME SLOTS is not enough length
						if (($count + 1) % $mod != 0)
						{
							$value = 0;
						}
						
						if ($value == 0 && $all_free)
						{
							$value = 2;
						}

						$new_arr[$i][$start] = $value;
					}
				}
			}
			
			$arr = $new_arr;
		}
		
		return $arr;
		
	}

	/**
	 * Elaborates the timeline by intersecting the worktimes and the bookings found.
	 * Filters the arrays of timelines to support a single associative array.
	 * This method should be used in case the times need to be adjusted to the
	 * employee timezone.
	 *
	 * @param 	array 	 $worktime 	The list of the available working times.
	 * @param 	array 	 $bookings 	The reservations to intersect.
	 * @param 	array 	 $service 	The service details.
	 * @param 	string 	 $timezone 	The timezone string.
	 *
	 * @return 	array  	 An associative array containing the elaborated timeline.
	 * 					 The keys contain the time (hour * 60 + min) and the values
	 * 					 contain the status (0: blocked, 1: available, 2: not enough space).
	 *
	 * @uses 	elaborateTimeLineTimezone()
	 *
	 * @since 	1.4
	 */
	public static function elaborateTimeLineServiceTimezone($worktime, $bookings, $service, $timezone)
	{
		$arr = self::elaborateTimeLineTimezone($worktime, $bookings, $service, $timezone);
		
		$timeline = array();
		foreach ($arr as $a)
		{
			foreach ($a as $hour => $val)
			{
				$timeline[$hour] = $val;
			}
		}
		
		return $timeline;
	}
	
	/**
	 * Elaborates the timeline by intersecting the worktimes and the bookings found.
	 * This method accepts multiple bookings at the same date and time depending on
	 * the maximum capacity defined by the given service.
	 *
	 * This method always converts the timezone according to the configuration of
	 * the employee, only in case the multi-timezone setting is enabled.
	 *
	 * @param 	array 	 $worktime 	The list of the available working times.
	 * @param 	array 	 $bookings 	The reservations to intersect.
	 * @param 	array 	 $service 	The service details.
	 * @param 	integer  $people 	The number of specified people.
	 * @param 	string 	 $timezone  The employee timezone (if set).
	 * @param 	mixed 	 &$seats 	An array containing the remaining seats for each time.
	 *
	 * @return 	array  	 An associative array containing the elaborated timeline.
	 * 					 The keys contain the time (hour * 60 + min) and the values
	 * 					 contain the status (0: blocked, 1: available, 2: not enough space).
	 *
	 * @since 	1.4
	 */
	public static function elaborateTimeLineGroupServiceTimezone($worktime, $bookings, $service, $people, $timezone, &$seats = null)
	{	
		$min_int = 0;

		if ($service['interval'] == 1)
		{
			$min_int = $service['duration'] + $service['sleep'];
		}
		else
		{
			$min_int = self::getMinuteIntervals();
		}
		
		$arr = array();
		
		for ($i = 0; $i < count($worktime); $i++)
		{
			//for( $j = $worktime[$i]['fromts'], $len = 0; $j < $worktime[$i]['endts']; $j+=$min_int) {
			for ($j = $worktime[$i]['fromts'], $len = 0; ($j+$min_int) <= $worktime[$i]['endts']; $j += $min_int)
			{
				$arr[$i][$j] = 1;
			}
		}
		
		self::setCurrentTimezone($timezone);
		
		$cont_people = 0;
		for ($k = 0; $k < count($bookings); $k++)
		{
			$b = $bookings[$k];
			
			$cont_people += $b['people_count'];
			if ($k == count($bookings) - 1 || $bookings[$k + 1]['checkin_ts'] != $b['checkin_ts'])
			{
				$date = getdate($b['checkin_ts']);
				$start = ($date['hours'] * 60) + $date['minutes'];

				if (isset($b['@day']))
				{
					/**
					 * Check the day factor of a reservation to check if it is referring
					 * to the current day or if it close to the bounds of this working time.
					 * Used to support midnight reservations.
					 *
					 * @since 1.6
					 */

					if ($b['@day'] == 1)
					{
						// we are evaluating a reservation for the next day, so we need to increase the 
						// initial time by 1440 minutes (24 hours * 60).
						$start += 1440;
					}
					else if ($b['@day'] == -1)
					{
						// we are evaluating a reservation for the previous day, so we need to decrease the 
						// initial time by 1440 minutes (24 hours * 60).
						$start -= 1440;
					}
				}

				for ($i = $start; $i < $start + $b['duration'] + $b['sleep']; $i += $min_int)
				{
					$found = false;
					for ($j = 0; $j < count($arr) && !$found; $j++)
					{
						/**
						 * Try to block appointments that come from a different service or
						 * if the number of people exceeds the total capacity.
						 *
						 * @since 1.6 	check if the services are different only if $arr[$j][$i] is set
						 */
						if (!empty($arr[$j][$i]) && ($cont_people + $people > $service['max_capacity'] || $b['id_service'] != $service['id'] || $b['closure']))
						{
							// if $b['id_service'] doesn't exist, take a look at the VikAppointments::getAllEmployeeReservationsExcludingResId() function
							// if $service['id'] doesn't exist, take a look at the VikAppointmentsController::get_day_time_line() and VikAppointmentsController::get_day_time_line_service() functions
							$found = true;
							$arr[$j][$i] = 0;
						}

						/**
						 * If $seats argument is an array, push the remaining seats.
						 * 
						 * @since 1.6
						 */
						if (is_array($seats))
						{
							if ($b['id_service'] == $service['id'] && !$b['closure'])
							{
								// same service, we can display the remaining seats
								$seats[$i] = $service['max_capacity'] - $cont_people;
							}
							else
							{
								// booked for a different service, unset the remaining seats
								$seats[$i] = 0;
							}
						}
					}
				}
				
				$cont_people = 0;
			}
		}
		
		$n_step = $service['duration'] + $service['sleep'];
		
		for ($i = 0; $i < count($arr); $i++)
		{
			$step = 0;
			//for( $j = $worktime[$i]['fromts'], $len = 0; $j < $worktime[$i]['endts']; $j+=$min_int) {
			for ($j = $worktime[$i]['fromts'], $len = 0; ($j + $min_int) <= $worktime[$i]['endts']; $j += $min_int)
			{
				if ($arr[$i][$j] == 1)
				{
					$step += $min_int;
					if ($step == $n_step)
					{
						$step -= $min_int;
					}
				}
				else
				{
					if ($step != 0 && $step < $n_step)
					{
						for ($back = $j - $min_int; $back >= $j - $step; $back -= $min_int)
						{
							$arr[$i][$back] = 2;
						}
					}
					
					$step = 0;
				}
			}
			
			if ($step != 0 && $step < $n_step)
			{
				for ($back = $j - $min_int; $back >= $j - $step; $back -= $min_int)
				{
					$arr[$i][$back] = 2;
				}
			}
		}
		
		$timeline = array();
		foreach ($arr as $a)
		{
			foreach ($a as $hour => $val)
			{
				$timeline[$hour] = $val;
			}
		}
		
		return $timeline;
	}

	///////////
	
	/**
	 * Parses the timelines in the array to fetch a single timeline.
	 *
	 * @param 	array 	$timelines 	The fetched timelines.
	 *
	 * @return 	array 	The resulting timeline.
	 */
	public static function parseServiceTimeline($timelines)
	{
		$arr = array();
		
		foreach ($timelines as $tl)
		{
			foreach ($tl as $hour => $val)
			{
				$res = $val;
				$is_pending = false;
				
				for ($i = 0; $i < count($timelines) && $res != 1; $i++) 
				{
					$res = (!empty($timelines[$i][$hour])) ? $timelines[$i][$hour] : 0;
					if ($res == 2)
					{
						$is_pending = true;
					}
				}
				
				if ($res == 0 && $is_pending)
				{
					$res = 2;
				}
				
				$arr[$hour] = $res;
			}
		}
		
		return $arr;
	}
	
	/**
	 * Checks if the employees works on the specified day for the given service.
	 *
	 * @param 	integer  $id_emp 	 The employee ID.
	 * @param 	integer  $id_ser 	 The service ID.
	 * @param 	integer  $ts 		 The day UNIX timestamp.
	 * @param 	mixed 	 $dbo 		 The database object.
	 * @param 	array 	 $locations  The locations array.
	 *
	 * @return 	boolean  True if it works, otherwise false.
	 */
	public static function hasEmployeeWorkingTimeOn($id_emp, $id_ser, $ts, $dbo = '', $locations = array())
	{
		if (empty($dbo))
		{
			$dbo = JFactory::getDbo();
		}
		
		$day = getdate($ts);
		$timestamp = date('Ymd', $day[0]);

		$id_emp = (int) $id_emp;
		$id_ser = (int) $id_ser;

		/**
		 * Fixed query to ignore weekday when the timestamp is specified:
		 * AND `ts` <= 0
		 *
		 * @since 1.6.1
		 */

		/**
		 * Convert the timestamp in the database to UTC format,
		 * so that an offset lower than 0 won't shift anymore the
		 * dates to the previous ones.
		 *
		 * @see   CONVERT_TZ()
		 *
		 * @since 1.6.2
		 */
		
		// search for a closing day (don't need to filter by location)
		$q = "SELECT `id`
		FROM `#__vikappointments_emp_worktime`
		WHERE `id_employee` = $id_emp AND `id_service` = $id_ser 
		AND
		(
			(`day` = {$day['wday']} AND `ts` <= 0) 
			OR DATE_FORMAT(
				CONVERT_TZ(FROM_UNIXTIME(`ts`), @@session.time_zone, '+00:00'),
				'%Y%m%d'
			) = '$timestamp'
		) AND `closed` = 1";

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return false;
		}
		
		// REF CHANGED
		$loc_where = "";

		if (count($locations))
		{
			$loc_where = " AND (`id_location` = -1 OR `id_location` IN (" . implode(",", $locations) . ")) ";
		}
		//
		
		$q = "SELECT `id`
		FROM `#__vikappointments_emp_worktime`
		WHERE `id_employee` = $id_emp AND `id_service` = $id_ser 
		AND
		(
			(`day` = {$day['wday']} AND `ts` <= 0)
			OR DATE_FORMAT(
				CONVERT_TZ(FROM_UNIXTIME(`ts`), @@session.time_zone, '+00:00'),
				'%Y%m%d'
			) = '$timestamp'
		) $loc_where"; // REF changed

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		return (bool) $dbo->getNumRows();
	}
	
	/**
	 * Checks if the specified timestamp is in the past or
	 * doesn't follow the booking minutes restriction.
	 *
	 * @param 	integer  $timestamp  The UNIX timestamp to check.
	 *
	 * @return 	boolean  True if not allowed, false otherwise.
	 */
	public static function isTimeInThePast($timestamp)
	{
		$min_restr = self::getBookingMinutesRestriction();
		
		return $timestamp < (time() + $min_restr * 60);
	}
	
	/**
	 * Checks if the specified timestamp belong to a closing day/period.
	 *
	 * @param 	integer  $ts      The timestamp to check.
	 * @param 	integer  $id_ser  The service ID to restrict the closing days.
	 *
	 * @return 	boolean  True if closing day, false otherwise.
	 */
public static function isClosingDay($ts, $id_ser = null)
    {
        /**
         * Retrieve closing days compatible with the specified service.
         *
         * @since 1.6.3
         */
        /// matin latest
        $closing_days = self::getClosingDays($id_ser);
        $dmy          = explode('/', ArasJoomlaVikApp::jdate('d/m/Y', $ts));
        $day_of_week = ArasJoomlaVikApp::jdate('D', $ts);
        
        foreach ($closing_days as $v)
        {
            $app   = explode('/', ArasJoomlaVikApp::jdate('d/m/Y', $v['ts']));
            $app_d = ArasJoomlaVikApp::jdate('D', $v['ts']);
            
            if ($v['freq'] == 0)
            {
                if ($dmy[0] == $app[0] && $dmy[1] == $app[1] && $dmy[2] == $app[2])
                {
                    return true;
                }
            }
            else if ($v['freq'] == 1)
            {
                if ($day_of_week == $app_d)
                {
                    return true;
                }
            }
            else if ($v['freq'] == 2)
            {
                if ($dmy[0] == $app[0])
                {
                    return true;
                }
            }
            else if ($v['freq'] == 3)
            {
                if ($dmy[0] == $app[0] && $dmy[1] == $app[1])
                {
                    return true;
                }
            }
        }
        
        /**
         * Retrieve closing periods compatible with the specified service.
         *
         * @since 1.6.3
         */
        $closing_periods = self::getClosingPeriods($id_ser);
        
        foreach ($closing_periods as $v)
        {
            if ($v['start'] <= $ts && $ts <= $v['end'])
            {
                return true;
            }
        }
        
        return false;
    }
	
	/**
	 * Checks if the employees works on the specified day by checking the closing days too.
	 *
	 * @param 	integer  $id_emp 	 	  The employee ID.
	 * @param 	integer  $id_ser 	 	  The service ID.
	 * @param 	integer  $ts 		 	  The day UNIX timestamp.
	 * @param 	array 	 $closing_days 	  The closing days list.
	 * @param 	array 	 $closing_perios  The closing periods list.
	 * @param 	mixed 	 $dbo 		 	  The database object.
	 * @param 	array 	 $locations  	  The locations array.
	 *
	 * @return 	boolean  True if it works, otherwise false.
	 *
	 * @uses 	hasEmployeeWorkingTimeOn()
	 * @uses 	isClosingDay()
	 */
	public static function isTableDayAvailable($id_emp, $id_ser, $ts, $closing_days = null, $closing_periods = null, $dbo = null, $locations = array())
	{
		/**
		 * Backward compatibility to support overrides that are passing
		 * the $skip_session argument between $dbo and $locations.
		 *
		 * @deprecated 	1.7 	This BC will be removed.
		 */
		if (is_bool($locations))
		{
			// $locations matches the old skip session argument.
			// We need to get the last argument of this method.
			$args = func_get_args();
			$locations = array_pop($args);
		}

		if (!self::hasEmployeeWorkingTimeOn($id_emp, $id_ser, $ts, $dbo, $locations))
		{
			return 0;
		}
		
		return !self::isClosingDay($ts, $id_ser);
	}
	
	/**
	 * Checks if there is at least an employee that works on the 
	 * specified day by checking the closing days too.
	 *
	 * @param 	array  	 $employees 	  The employee IDs.
	 * @param 	integer  $id_ser 	 	  The service ID.
	 * @param 	integer  $ts 		 	  The day UNIX timestamp.
	 * @param 	array 	 $closing_days 	  The closing days list.
	 * @param 	array 	 $closing_perios  The closing periods list.
	 * @param 	mixed 	 $dbo 		 	  The database object.
	 * @param 	array 	 $locations  	  The locations array.
	 *
	 * @return 	boolean  True if it works, otherwise false.
	 *
	 * @uses 	hasEmployeeWorkingTimeOn()
	 * @uses 	isClosingDay()
	 */
	public static function isGenericTableDayAvailable($employees, $id_ser, $ts, $closing_days = null, $closing_periods = null, $dbo = null, $locations = array())
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}
		
		if (self::isClosingDay($ts, $id_ser))
		{
			return 0;
		}
		
		foreach ($employees as $e)
		{
			if (self::hasEmployeeWorkingTimeOn($e['id'], $id_ser, $ts, $dbo, $locations))
			{
				return 1;
			}
		}
		
		return 0;
	}
	
	/**
	 * Returns the list of employees that offer the specified service.
	 *
	 * @param 	integer  $id_ser 	The service ID.
	 * @param 	boolean  $ordering  True to sort the employees by nickname.
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	array 	 The employees list.
	 */
	public static function getEmployeesRelativeToService($id_ser, $ordering = false, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array(
				'e.id',
				'e.nickname',
				'e.timezone',
			)))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('a.id_employee'))
			->where($dbo->qn('a.id_service') . ' = ' . (int) $id_ser);

		if ($ordering)
		{
			$q->order($dbo->qn('e.nickname') . ' ASC');
		}
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadAssocList();
		}
		
		return array();
	}
	
	/**
	 * Checks if the specified employee is available for the specified checkin.
	 *
	 * @param 	integer  $id_emp 		The employee ID.
	 * @param 	integer  $id_ser 		The service ID.
	 * @param 	integer  $res_id 		The reservation to exlude, if any.
	 * @param 	integer  $checkin 		The checkin timestamp to check.
	 * @param 	integer  $duration 		The duration (in min.) to calculate the checkout.
	 * @param 	integer  $people 		The number of people.
	 * @param 	integer  $max_capacity 	The maximum number of allowed people.
	 * @param 	mixed 	 $dbo 			The database object.
	 *
	 * @return 	boolean  True if available, otherwise false.
	 */
	public static function isEmployeeAvailableFor($id_emp, $id_ser, $res_id, $checkin, $duration, $people, $max_capacity, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}
		
		$checkout = $duration * 60;
		
		$start_res = date('H:i', $checkin);
		$exp       = explode(':', $start_res);
		$start_res = $exp[0] * 60 + $exp[1];
		$end_res   = $start_res + $duration;
		
		$wt = self::getEmployeeWorkingTimes($id_emp, $id_ser, $checkin);
		
		for ($i = 0; $i < count($wt) && !self::isBetween($start_res, $wt[$i]['fromts'], $wt[$i]['endts']); $i++);

		if ($i >= count($wt))
		{
			return -1; // CLOSING TIME
		}

		// if id_ser NOT -1 and service has own calendar, don't consider 
		// the reservations of the other services (of the same employee)
		$id_ser_app = $id_ser;

		if (!self::hasServiceOwnCalendar($id_ser))
		{
			// don't apply own search
			$id_ser_app = -1; // unset service
		}

		/**
		 * Use timestamp for checkout.
		 *
		 * @since 1.6.2
		 */
		$checkout += $checkin;
		
		$q = "SELECT `r`.`id`, `r`.`people` 
		FROM `#__vikappointments_reservation` AS `r`
		LEFT JOIN `#__vikappointments_service` AS `s` ON `s`.`id`=`r`.`id_service` 
		WHERE `r`.`id` <> $res_id AND `r`.`id_employee` = $id_emp AND 
			(
				(`s`.`has_own_cal` = 0 AND $id_ser_app = -1)
				OR (`s`.`has_own_cal` = 1 AND `r`.`id_service` = $id_ser_app)
			)
			AND `r`.`status` <> 'REMOVED' AND `r`.`status` <> 'CANCELED' AND
			(
				(
					`r`.`checkin_ts` <= $checkin AND $checkin < (`r`.`checkin_ts` + `r`.`duration` * 60 + `r`.`sleep` * 60)
				)
				OR
				(
					`r`.`checkin_ts` < $checkout AND $checkout <= (`r`.`checkin_ts` + `r`.`duration` * 60 + `r`.`sleep` * 60)
				)
				OR
				(
					`r`.`checkin_ts` <= $checkin AND $checkout <= (`r`.`checkin_ts` + `r`.`duration` * 60 + `r`.`sleep` * 60)
				)
				OR
				(
					`r`.`checkin_ts` >= $checkin AND $checkout >= (`r`.`checkin_ts` + `r`.`duration` * 60 + `r`.`sleep` * 60)
				)
				OR
				(
					`r`.`checkin_ts` = $checkin AND $checkout = (`r`.`checkin_ts` + `r`.`duration` * 60 + `r`.`sleep` * 60)
				)
			)";
		
		$dbo->setQuery($q, 0, $max_capacity);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$cont_people = 0;

			foreach ($rows as $r)
			{
				$cont_people += $r['people'];
			}
			
			if ($cont_people + $people > $max_capacity)
			{
				return 0;
			}
		}
		
		for ($i = 0; $i < count($wt) && !self::isBetween($end_res, $wt[$i]['fromts'], $wt[$i]['endts']); $i++);
		
		if ($i < count($wt))
		{
			return 1; // FREE
		}
		
		return -1; // CLOSING TIME
	}

	/**
	 * Checks if there is at least an employee available for the specified checkin.
	 *
	 * @param 	integer  $id_ser 		The service ID.
	 * @param 	integer  $checkin 		The checkin timestamp to check.
	 * @param 	integer  $duration 		The duration (in min.) to calculate the checkout.
	 * @param 	integer  $people 		The number of people.
	 * @param 	integer  $max_capacity 	The maximum number of allowed people.
	 * @param 	mixed 	 $dbo 			The database object.
	 *
	 * @return 	boolean  True if available, otherwise false.
	 *
	 * @uses 	isEmployeeAvailableFor()
	 */
	public static function getAvailableEmployeeOnService($id_ser, $checkin, $duration, $people, $max_capacity, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		$id_ser = (int) $id_ser;
		
		$q = "SELECT `e`.`id`, COUNT(`r`.`id`) AS `count`
		FROM `#__vikappointments_employee` AS `e`
		LEFT JOIN `#__vikappointments_ser_emp_assoc` AS `a` ON `e`.`id` = `a`.`id_employee`
		LEFT JOIN `#__vikappointments_reservation` AS `r` ON `e`.`id` = `r`.`id_employee`
		WHERE `a`.`id_service` = $id_ser
		GROUP BY `e`.`id`
		ORDER BY `count` ASC;";
		
		$dbo->setQuery($q);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			return -1;
		}
		
		$employees = $dbo->loadAssocList();
		
		foreach ($employees as $e)
		{
			if (self::isEmployeeAvailableFor($e['id'], $id_ser, -1, $checkin, $duration, $people, $max_capacity, $dbo) == 1)
			{
				return $e['id'];
			}
		}
		
		return 0;
	}

	/**
	 * Returns the employee working times for the given day.
	 * In case of 24h working days, the system will extend the ending
	 * time of the last working day in order to support midnight appointments.
	 *
	 * @param 	integer  $id_emp 	 The employee ID.
	 * @param 	integer  $id_ser 	 The service ID.
	 * @param 	integer  $day 		 The date timestamp.
	 * @param 	array 	 $locations  The supported locations.
	 *
	 * @return 	array 	 A list containing the matching working days.
	 *
	 * @uses 	_getEmployeeWorkingTimes()
	 */
	public static function getEmployeeWorkingTimes($id_emp, $id_ser, $day, array $locations = array())
	{
		// get working times for the given day
		$worktimes = self::_getEmployeeWorkingTimes($id_emp, $id_ser, $day, $locations);

		// update current timestamp by one day
		$day = strtotime('+1 day 00:00:00', $day);

		// fallback to obtain the working times for the next day
		$next = self::_getEmployeeWorkingTimes($id_emp, $id_ser, $day, $locations);

		if ($worktimes && $next && $next[0]['fromts'] == 0)
		{
			// We have probably a 24H working time.
			// Extend the last working time with the first
			// one of the next day
			$last = &$worktimes[count($worktimes) - 1];

			$last['endts'] += $next[0]['endts'];
		}

		return $worktimes;
	}

	/**
	 * Returns the employee working times for the given day.
	 *
	 * @param 	integer  $id_emp 	 The employee ID.
	 * @param 	integer  $id_ser 	 The service ID.
	 * @param 	integer  $day 		 The date timestamp.
	 * @param 	array 	 $locations  The supported locations.
	 *
	 * @return 	array 	 A list containing the matching working days.
	 *
	 * @since 	1.6
	 */
	protected static function _getEmployeeWorkingTimes($id_emp, $id_ser, $day, array $locations = array())
	{
		$dbo = JFactory::getDbo();
		
		$date = ArasJoomlaVikApp::jgetdate($day);
		//$timestamp = mktime( 0, 0, 0, $date['mon'], $date['mday'], $date['year'] );
		$timestamp = date('Ymd', $date[0]);
		// obtain the working days for the given custom day

		/**
		 * Convert the timestamp in the database to UTC format,
		 * so that an offset lower than 0 won't shift anymore the
		 * dates to the previous ones.
		 *
		 * @see   CONVERT_TZ()
		 *
		 * @since 1.6.2
		 */

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_emp_worktime'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . (int) $id_emp,
				$dbo->qn('id_service') . ' = ' . (int) $id_ser,
				'DATE_FORMAT(
					CONVERT_TZ(FROM_UNIXTIME(' . $dbo->qn('ts') . '), @@session.time_zone, \'+00:00\'),
					\'%Y%m%d\'
				) = ' . $timestamp,
			))
			->order(array(
				$dbo->qn('closed') . ' DESC',
				$dbo->qn('fromts') . ' ASC',
			));
file_put_contents(JPATH_ROOT.'/debug/$q.txt', print_r($q, true));
		if (count($locations))
		{
			$q->andWhere(array(
				$dbo->qn('id_location') . ' = -1',
				$dbo->qn('id_location') . ' IN (' . implode(',', array_map('intval', $locations)) . ')',
			), 'OR');
		}
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();

			if ($rows[0]['closed'])
			{
				// this custom day is closed
				return array();
			}
			
			return $rows;
		}

		// obtain the working days for the given week day

		$q->clear('where');

		$q->where(array(
			$dbo->qn('id_employee') . ' = ' . (int) $id_emp,
			$dbo->qn('id_service') . ' = ' . (int) $id_ser,
			$dbo->qn('day') . ' = ' . $date['wday'],
		));

		/**
		 * Fixed query to ignore weekday when the timestamp is specified.
		 *
		 * @since 1.6.1
		 */
		$q->where($dbo->qn('ts') . ' <= 0');
	
		if (count($locations))
		{
			$q->andWhere(array(
				$dbo->qn('id_location') . ' = -1',
				$dbo->qn('id_location') . ' IN (' . implode(',', array_map('intval', $locations)) . ')',
			), 'OR');
		}
			
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();

			if ($rows[0]['closed'])
			{
				// this day of the week is closed
				return array();
			}
			
			return $rows;
		}
		
		return array();
	}
	
	/**
	 * Updates the status of all the orders out of time to REMOVED.
	 * This method is used to free the slots occupied by pending orders
	 * that haven't been confirmed within the specified range of time.
	 *
	 * Affects only the reservations that match the specified employee ID.
	 *
	 * @param 	integer  $id_emp 	The employee ID.
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	void
	 */
	public static function removeAllReservationsOutOfTime($id_emp, $dbo = null)
	{
		if (is_null($dbo))
		{
			$dbo = JFactory::getDbo();
		}

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id'))
			->from($dbo->qn('#__vikappointments_reservation'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . (int) $id_emp,
				$dbo->qn('status') . ' = ' . $dbo->q('PENDING'),
				$dbo->qn('locked_until') . ' < ' . time(),
			));
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$handler = VAPOrderStatus::getInstance();

			foreach ($dbo->loadColumn() as $id)
			{
				// remove and track the status change (REMOVED)
				$handler->remove($id, 'VAP_STATUS_ORDER_REMOVED');
			}
		}
	}
	
	/**
	 * Updates the status of all the orders out of time to REMOVED.
	 * This method is used to free the slots occupied by pending orders
	 * that haven't been confirmed within the specified range of time.
	 *
	 * Affects only the reservations that match the specified service ID.
	 *
	 * @param 	integer  $id_ser 	The service ID.
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	void
	 */
	public static function removeAllServicesReservationsOutOfTime($id_ser, $dbo = null)
	{	
		if (is_null($dbo))
		{
			$dbo = JFactory::getDbo();
		}

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id'))
			->from($dbo->qn('#__vikappointments_reservation'))
			->where(array(
				$dbo->qn('id_service') . ' = ' . (int) $id_ser,
				$dbo->qn('status') . ' = ' . $dbo->q('PENDING'),
				$dbo->qn('locked_until') . ' < ' . time(),
			));
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$handler = VAPOrderStatus::getInstance();

			foreach ($dbo->loadColumn() as $id)
			{
				// remove and track the status change (REMOVED)
				$handler->remove($id, 'VAP_STATUS_ORDER_REMOVED');
			}
		}
	}

	/**
	 * Helper method used to get HTML code to display the employee toolbar.
	 *
	 * @param 	array 	 $employee 	The employee details (@unused).
	 * @param 	boolean  $active 	True if the links are clickable, false otherwise.
	 *
	 * @return 	string 	 The HTML code.
	 *
	 * @deprecated 	1.7 	Use "emparea.toolbar" layout instead.
	 */
	public static function getToolbarEmployeeArea($employee, $active = true)
	{
		$data = array(
			'active' => $active,
		);

		return JLayoutHelper::render('emparea.toolbar', $data);
	}

	/**
	 * Returns the list of the payments created by the specified employee.
	 * If the employee doesn't own any custom payment, the global ones
	 * will be returned.
	 *
	 * @param 	integer  $id_emp 	The employee ID.
	 *
	 * @return 	array 	 The payments list.
	 */
	public static function getAllEmployeePayments($id_emp = 0)
	{
		$app = JFactory::getApplication();	
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$q->select('*')
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('appointments') . ' = 1')
			->order(array(
				// custom payments come first
				$dbo->qn('id_employee') . ' DESC',
				// published payments before unpublished ones
				$dbo->qn('published') . ' DESC',
				// finally sort by ordering column
				$dbo->qn('ordering') . ' ASC',
			));

		// if employee is set, get global and custom payments
		if ($id_emp > 0)
		{
			$q->andWhere(array(
				$dbo->qn('id_employee') . ' = 0',
				$dbo->qn('id_employee') . ' = ' . (int) $id_emp,
			), 'OR');
		}
		// otherwise get only global payments
		else
		{
			$q->where($dbo->qn('id_employee') . ' = 0');
		}

		// get published payments only for the front-end
		if ($app->isSite())
		{
			$q->where($dbo->qn('published') . ' = 1');

			/**
			 * Retrieve only the payments the belong to the view
			 * access level of the current user.
			 *
			 * @since 1.6.2
			 */
			$levels = JFactory::getUser()->getAuthorisedViewLevels();

			if ($levels)
			{
				$q->where($dbo->qn('level') . ' IN (' . implode(', ', $levels) . ')');
			}
		}
		
		$dbo->setQuery($q);
		$dbo->execute();

		// check if there is at least a payment
		if (!$dbo->getNumRows())
		{
			return array();
		}
		
		$payments = $dbo->loadAssocList();

		// if the first payment is global, the employee
		// does not own any custom payment
		if ($payments[0]['id_employee'] == 0)
		{
			return $payments;
		}

		// If the first payment is unpublished, the employee
		// does not own any valid custom payment.
		// So, we don't need to remove the global payments.
		if ($payments[0]['published'] == 0)
		{
			return $payments;
		}

		// filter the payment and remove the global ones
		return array_filter($payments, function($item)
		{
			return $item['id_employee'] > 0;
		});
	}
	
	/**
	 * Returns the payment record that matches the given ID.
	 * The payment can be global or owned by a specific employee.
	 *
	 * @param 	integer  $id_pay 	The payment ID.
	 * @param 	boolean  $strict 	True to get the payment only if it is published.
	 *
	 * @return 	mixed 	 An associative array on success, otherwise null.
	 */
	public static function getPayment($id_pay, $strict = true)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$q->select('*')
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('id') . ' = ' . $id_pay);

		if ($strict)
		{
			$q->where($dbo->qn('published') . ' = 1');

			/**
			 * Retrieve only the payments the belong to the view
			 * access level of the current user.
			 *
			 * @since 1.6.2
			 */
			$levels = JFactory::getUser()->getAuthorisedViewLevels();

			if ($levels)
			{
				$q->where($dbo->qn('level') . ' IN (' . implode(', ', $levels) . ')');
			}
		}
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadAssoc();
		}

		return null;
	}
	
	/**
	 * Returns the location related to the specified employee, service and checkin.
	 *
	 * @param 	integer  $id_emp 	The employee ID. If not provided, it won't be used.
	 * @param 	integer  $id_ser 	The service ID.
	 * @param 	integer  $ts 		The checkin UNIX timestamp.
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	mixed 	 The location ID on success, otherwise false.
	 */
	public static function getEmployeeLocationFromTime($id_emp, $id_ser, $ts, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}
		
		$date 			= getdate($ts);
		$midnight_day 	= date('Ymd', $date[0]);
		$time 			= $date['hours'] * 60 + $date['minutes'];
		$week_day 		= $date['wday'];

		$q = $dbo->getQuery(true);

		// search working day for a specific day of the year

		$q->select($dbo->qn('id_location'));
		$q->from($dbo->qn('#__vikappointments_emp_worktime'));

		if ($id_emp > 0)
		{
			$q->where($dbo->qn('id_employee') . ' = ' . (int) $id_emp);
		}

		/**
		 * Convert the timestamp in the database to UTC format,
		 * so that an offset lower than 0 won't shift anymore the
		 * dates to the previous ones.
		 *
		 * @see   CONVERT_TZ()
		 *
		 * @since 1.6.2
		 */

		$q->where(array(
			$dbo->qn('id_service') . ' = ' . (int) $id_ser,
			'DATE_FORMAT(
				CONVERT_TZ(FROM_UNIXTIME(' . $dbo->qn('ts') . '), @@session.time_zone, \'+00:00\'),
				\'%Y%m%d\'
			)' . ' = ' . $dbo->q($midnight_day),
			$time . ' BETWEEN ' . $dbo->qn('fromts') . ' AND ' . $dbo->qn('endts'),
		));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadResult();
		}

		// clear WHERE statement
		$q->clear('where');
		
		if ($id_emp > 0)
		{
			$q->where($dbo->qn('id_employee') . ' = ' . (int) $id_emp);
		}

		$q->where(array(
			$dbo->qn('id_service') . ' = ' . (int) $id_ser,
			$dbo->qn('day') . ' = ' . $week_day,
			$time . ' BETWEEN ' . $dbo->qn('fromts') . ' AND ' . $dbo->qn('endts'),
		));

		$dbo->setQuery($q, 0, 2);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadResult();
		}    
		
		return false;
	}

	/**
	 * Method used to return the details of the given location.
	 *
	 * @param 	integer  $id_location 	The location ID.
	 * @param 	mixed 	 $dbo 			The database object.
	 *
	 * @return 	mixed 	 The location details on success, false otherwise.
	 */
	public static function fillEmployeeLocation($id_location, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		$q = $dbo->getQuery(true)
			->select('`l`.*')
			->select($dbo->qn(array(
				'c.country_name',
				's.state_name',
				'ci.city_name',
			)))
			->from($dbo->qn('#__vikappointments_employee_location', 'l'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('l.id_country') . ' = ' . $dbo->qn('c.id'))
			->leftjoin($dbo->qn('#__vikappointments_states', 's') . ' ON ' . $dbo->qn('l.id_state') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_cities', 'ci') . ' ON ' . $dbo->qn('l.id_city') . ' = ' . $dbo->qn('ci.id'))
			->where($dbo->qn('l.id') . ' = ' . (int) $id_location);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadAssoc();
		}
		
		return false;
	}

	/**
	 * Parses the locations details to create a human-readable string.
	 *
	 * @param 	mixed 	$location 	The location details.
	 *
	 * @return 	string 	The location information as string.
	 */
	public static function locationToString($location)
	{
		if (!$location)
		{
			return "";
		}
		
		$str = '';
		
		if (!empty($location['country_name']))
		{
			$str .= $location['country_name'];
		}

		if (!empty($location['state_name']))
		{
			$str .= (!empty($str) ? ', ' : '') . $location['state_name'];
		}

		if (!empty($location['city_name']))
		{
			$str .= (!empty($str) ? ', ' : '') . $location['city_name'];
		}

		if (!empty($location['address']))
		{
			$str .= (!empty($str) ? ', ' : '') . $location['address'];
		}

		if (!empty($location['zip']))
		{
			$str .= (!empty($str) ? ' ' : '') . $location['zip'];
		}
		
		return $str;
	}

	/**
	 * Calculates the distance between 2 coordinates.
	 *
	 * @param 	float 	$lat_1 	The latitude of the first point.
	 * @param 	float 	$lng_1 	The longitude of the first point.
	 * @param 	float 	$lat_2 	The latitude of the first point.
	 * @param 	float 	$lng_2 	The longitude of the second point.
	 *
	 * @return 	float 	The distance between the 2 points (in km).
	 *
	 * @since 	1.5
	 */
	public static function getGeodeticaDistance($lat_1, $lng_1, $lat_2, $lng_2)
	{
		$lat_1 = $lat_1 * pi() / 180.0;
		$lng_1 = $lng_1 * pi() / 180.0;

		$lat_2 = $lat_2 * pi() / 180.0;
		$lng_2 = $lng_2 * pi() / 180.0;

		/** distance between 2 coordinates
		 * R = 6371 (Eart radius ~6371 km)
		 *
		 * coordinates in radiants
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
		 *      cos( abs(lng1-lng2) ) 
		 * ) * R
		 *
		 */

		return acos(
			sin($lat_2) * sin($lat_1) + 
			cos($lat_2) * cos($lat_1) *
			cos(abs($lng_1 - $lng_2))
		) * 6371;
	}

	/**
	 * Helper method used to format the distance
	 * in meters and kilometers.
	 *
	 * @param 	float 	$distance 	The distance to format (in km).
	 * @param 	mixed 	$unit 		The unit to use or the filters array 
	 * 								containing the unit parameter.
	 *
	 * @return 	string 	The formatted distance.
	 *
	 * @since 	1.5
	 */
	public static function formatDistance($distance, $unit = null)
	{
		UILoader::import('libraries.helpers.distance');

		if (!$unit)
		{
			$input = JFactory::getApplication()->input;
			$unit  = $input->get('filters', array(), 'array');
		}

		if (is_array($unit))
		{
			$unit = isset($unit['distunit']) ? $unit['distunit'] : DistanceHelper::METER;
		}

		// distance is always passed in meters and needs to be converted
		// to the specified unit
		return DistanceHelper::format($distance * 1000, $unit, DistanceHelper::METER);
	}

	/**
	 * Helper method used to convert the distance in kilometers.
	 *
	 * @param 	float 	$distance 	The distance to convert.
	 * @param 	mixed 	$unit 		The unit to use or the filters array 
	 * 								containing the unit parameter.
	 *
	 * @return 	string 	The converted distance.
	 *
	 * @since 	1.6
	 */
	public static function convertDistanceToKilometers($distance, $unit = null)
	{
		UILoader::import('libraries.helpers.distance');

		if (!$unit)
		{
			$input = JFactory::getApplication()->input;
			$unit  = $input->get('filters', array(), 'array');
		}

		if (is_array($unit))
		{
			$unit = isset($unit['distunit']) ? $unit['distunit'] : DistanceHelper::KILOMETER;
		}

		return DistanceHelper::convert($distance, DistanceHelper::KILOMETER, $unit);
	}
	
	/**
	 * Returns the timezone of the given employee (if any).
	 *
	 * @param 	integer  $id_emp  The employee ID.
	 * @param 	mixed 	 $dbo 	  The database object.
	 *
	 * @return 	mixed 	 The employee timezone is set, false otherwise.
	 *
	 * @uses 	isMultipleTimezones()
	 */
	public static function getEmployeeTimezone($id_emp, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}
		
		if (!self::isMultipleTimezones())
		{
			return false;
		}

		$q = $dbo->getQuery(true)
			->select($dbo->qn('timezone'))
			->from($dbo->qn('#__vikappointments_employee'))
			->where($dbo->qn('id') . ' = ' . $id_emp);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadResult();
		}
		
		return false;
	}
	
	/**
	 * Alters the server timezone with the given one.
	 *
	 * @param 	mixed 	$tz  The new timezone to set.
	 *
	 * @return 	mixed 	The changing result.
	 */
	public static function setCurrentTimezone($tz)
	{

		if (!$tz || !self::isMultipleTimezones())
		{
			return false;
		}

		return date_default_timezone_set($tz);
	}
	
	/**
	 * Helper method used to round a float value to the closest half.
	 *
	 * @param 	float 	$d 	The amount to round.
	 *
	 * @return 	float 	The rounded amount.
	 */
	public static function roundHalfClosest($d)
	{
		$floor 	= floor($d * 2) / 2;
		$ceil 	= ceil($d * 2) / 2;
		
		if (abs($d - $floor) < abs($d - $ceil))
		{
			return $floor;
		}

		return $ceil;
	}
	
	/**
	 * Loads the reviews for the given entity.
	 *
	 * @param 	string 	 $figure 	The entity to get (employee or service).
	 * @param 	integer  $id 		The entity ID.
	 * @param 	integer  $start 	The limit start.
	 *
	 * @return 	array 	 The reviews list.
	 *
	 * @since 	1.4
	 */
	public static function loadReviews($figure, $id, $start)
	{
		$dbo = JFactory::getDbo();
		
		$lim  = self::getReviewsListLimit();
		$lim0 = $start;

		$session  = JFactory::getSession();
		$ordering = $session->get('reviewsOrdering', '', 'vikappointments');

		if (empty($ordering))
		{
			$ordering = array(
				'by' => 'timestamp',
				'mode' => 'DESC',
			);
		}

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `r`.*');
		$q->select($dbo->qn('u.image'));

		$q->from($dbo->qn('#__vikappointments_reviews', 'r'));
		
		/**
		 * Fixed LEFT JOIN which was loading all the customers that was not assigned to a
		 * specific Joomla/WordPress user ID. The JOIN must exclude all the records that
		 * owns a `jid` equals or lower than 0.
		 *
		 * @since 1.6.3
		 */
		$q->leftjoin($dbo->qn('#__vikappointments_users', 'u')
			. ' ON ' . $dbo->qn('r.jid') . ' = ' . $dbo->qn('u.jid') . ' AND ' . $dbo->qn('r.jid') . ' > 0');

		$q->where(array(
			$dbo->qn('r.id_' . $figure) . ' = ' . (int) $id,
			$dbo->qn('r.published') . ' = 1',
			$dbo->qn('r.comment') . ' <> \'\'',
		));
		
		if (self::isReviewsLangFilter())
		{
			$q->where($dbo->qn('r.langtag') . ' = ' . $dbo->q(JFactory::getLanguage()->getTag()));
		}

		$q->order($dbo->qn('r.' . $ordering['by']) . ' ' . $ordering['mode']);
			
		$rows  = array();
		$size  = 0;
		$votes = 0;

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			$size = $dbo->loadResult();
			
			$q = $dbo->getQuery(true)
				->select('COUNT(1)')
				->from($dbo->qn('#__vikappointments_reviews'))
				->where(array(
					$dbo->qn('id_' . $figure) . ' = ' . (int) $id,
					$dbo->qn('published') . ' = 1',
				));

			$dbo->setQuery($q);
			$dbo->execute();

			$votes = $dbo->loadResult();
		}
		
		return array(
			"rows"  => $rows,
			"size"  => $size,
			"votes" => $votes,
		);
	}
	
	/**
	 * Returns the links used to switch ordering.
	 *
	 * @param 	string 	$base 	The base URI.
	 * @param 	string 	$by 	The current ordering column.
	 * @param 	string 	$mode 	The current ordering direction.
	 *
	 * @return 	array 	An array containing the link details.
	 *
	 * @since 	1.4
	 */
	public static function getReviewsOrderingLinks($base, $by, $mode)
	{
		$columns = array(
			'timestamp' => 'DESC',
			'rating' 	=> 'DESC',
		);
		
		$session  = JFactory::getSession();
		$ordering = $session->get('reviewsOrdering', '', 'vikappointments');

		if (empty($ordering))
		{
			$ordering = array(
				'by' 	=> 'timestamp',
				'mode' 	=> 'DESC',
			);
		}
		
		if (empty($by))
		{
			$by   = $ordering['by'];
			$mode = $ordering['mode'];
		}
		
		if (!array_key_exists($by, $columns))
		{
			$by = $ordering['by'];
		}
		
		$links = array();

		foreach ($columns as $col => $m)
		{
			$arr = array(
				'uri' 		=> '',
				'active' 	=> false,
				'mode' 		=> '',
				'name' 		=> JText::_('VAPREVIEWORDERING' . strtoupper($col)),
			);
			
			$l = "{$base}&revordby={$col}&revordmode=";

			if ($by == $col)
			{
				$l .= $mode == 'ASC' ? 'DESC' : 'ASC';
				$arr['active'] 	= true;
				$arr['mode'] 	= $mode;
			}
			else
			{
				$l .= $m;
			}

			$arr['uri'] = $l;
			
			$links[] = $arr;
		}
		
		$ordering['by'] 	= $by;
		$ordering['mode'] 	= $mode;
		
		$session->set('reviewsOrdering', $ordering, 'vikappointments');
		
		return $links;
	}

	/**
	 * Helper method used to check if the current customer
	 * is allowed to leave a review for the specified employee.
	 *
	 * @param 	integer  $id_emp 	The ID of the employee.
	 *
	 * @return 	boolean  True if the review can be left, otherwise false.
	 *
	 * @uses 	userCanLeaveReview()
	 *
	 * @since 	1.4
	 */
	public static function userCanLeaveEmployeeReview($id_emp)
	{	
		if (!self::isEmployeesReviewsEnabled())
		{
			// reviews for employees are not enabled
			return false;
		}
		
		// evaluate system criteria
		return self::userCanLeaveReview($id_emp, 'employee');
	}

	/**
	 * Helper method used to check if the current customer
	 * is allowed to leave a review for the specified service.
	 *
	 * @param 	integer  $id_ser 	The ID of the service.
	 *
	 * @return 	boolean  True if the review can be left, otherwise false.
	 *
	 * @uses 	userCanLeaveReview()
	 *
	 * @since 	1.4
	 */
	public static function userCanLeaveServiceReview($id_ser)
	{	
		if (!self::isServicesReviewsEnabled())
		{
			// reviews for services are not enabled
			return false;
		}

		// evaluate system criteria
		return self::userCanLeaveReview($id_ser, 'service');
	}

	/**
	 * Helper method used to check if the current customer
	 * is allowed to leave a review.
	 *
	 * @param 	integer  $id 	The ID of the entity.
	 * @param 	string 	 $type  The entity type (employee or service).
	 *
	 * @return 	boolean  True if the review can be left, otherwise false.
	 *
	 * @since 	1.6
	 */
	protected static function userCanLeaveReview($id, $type)
	{	
		$user 		= JFactory::getUser();
		$dispatcher = UIFactory::getEventDispatcher();
		
		/**
		 * Trigger event to override the default system criteria used to
		 * validate whether a review should be left or not.
		 *
		 * @param 	string   $type  The entity type (service or employee).
		 * @param 	integer  $id    The entity ID for which the review should be left.
		 * @param 	JUser 	 $user 	The current user object.
		 *
		 * @return 	boolean  True if the review should be left.
		 *
		 * @since 	1.6
		 */
		if ($dispatcher->is('onValidateLeaveReview', array($type, $id, $user)))
		{
			// ignore the default system criteria as the plugin overrided it
			// to allow the user to leave the review
			return true;
		}

		if ($user->guest)
		{
			// user not logged in
			return false;
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_reviews'))
			->where(array(
				$dbo->qn('id_' . $type) . ' = ' . (int) $id,
				$dbo->qn('jid') . ' = ' . $user->id,
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			// user already wrote a review for this entity
			return false;
		}

		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_users', 'u') . ' ON ' . $dbo->qn('r.id_user') . ' = ' . $dbo->qn('u.id'))
			->where(array(
				$dbo->qn('r.id_' . $type) . ' = ' . (int) $id,
				$dbo->qn('u.jid') . ' = ' . $user->id,
				$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn('r.checkin_ts') . ' < ' . time(),
			));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			// user never placed an order for this entity
			return false;
		}
		
		// the user can leave the review
		return true;
	}

	/**
	 * Checks if there is at least a published subscription.
	 *
	 * @return 	boolean  True if any, false otherwise.
	 *
	 * @uses 	_getSubscriptions()
	 */
	public static function isSubscriptions()
	{
		return (bool) self::_getSubscriptions(array('published' => 1), 1, false);
	}
	
	/**
	 * Returns the trial subscription (if any).
	 *
	 * @param 	boolean  $translate  True to translate the subscriptions, false otherwise.
	 *
	 * @return 	array 	 The trial subscription. False if it doesn't exist.
	 *
	 * @uses 	_getSubscriptions()
	 */
	public static function getTrialSubscription($translate = true)
	{
		return self::_getSubscriptions(array('published' => 1, 'trial' => 1), 1, $translate);
	}
	
	/**
	 * Returns a list of active subscriptions.
	 *
	 * @param 	boolean  $translate  True to translate the subscriptions, false otherwise.
	 *
	 * @return 	array 	 The subscriptions list. False if the list is empty.
	 *
	 * @uses 	_getSubscriptions()
	 */
	public static function getSubscriptions($trial = false, $translate = true)
	{
		return self::_getSubscriptions(array('published' => 1, 'trial' => 0), null, $translate);
	}

	/**
	 * Returns the details of the given subscription.
	 *
	 * @param 	integer  $id 		 The subscription ID.
	 * @param 	boolean  $strict 	 True to get the subscription only if it is published.
	 * @param 	boolean  $translate  True to translate the subscriptions, false otherwise.
	 *
	 * @return 	array 	 The trial subscription. False if it doesn't exist.
	 *
	 * @uses 	_getSubscriptions()
	 *
	 * @since 	1.6
	 */
	public static function getSubscription($id, $strict = true, $translate = true)
	{
		$where = array(
			'id' => (int) $id,
		);

		if ($strict)
		{
			$where['published'] = 1;
		}

		return self::_getSubscriptions($where, 1, $translate);
	}

	/**
	 * Returns a list of subscriptions matching the given query.
	 *
	 * @param 	array  	 $where 	 An associative array containing the query terms.
	 * @param 	integer  $lim 		 The number of records to retrieve. Null to ignore this value.
	 * @param 	boolean  $translate  True to translate the subscriptions, false otherwise.
	 *
	 * @return 	array 	 The subscriptions list. False if the list is empty. The associative array
	 * 					 of the subscription in case $lim is equals to 1.
	 *
	 * @since 	1.6
	 */
	public static function _getSubscriptions(array $where = array(), $lim = null, $translate = false)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_subscription'))
			->order($dbo->qn('price') . ' ASC');

		foreach ($where as $k => $v)
		{
			$q->where($dbo->qn($k) . ' = ' . $dbo->q($v));
		}
		
		$dbo->setQuery($q, 0, $lim);
		$dbo->execute();
		
		if (!$dbo->getNumRows())
		{
			return false;
		}

		$list = $dbo->loadAssocList();

		if ($translate)
		{
			/**
			 * Translate the subscriptions.
			 *
			 * @since 1.6
			 */

			if (count($list) == 1)
			{
				$ids = $list[0]['id'];
			}
			else
			{
				$ids = null;
			}

			$translations = self::getTranslatedSubscriptions($ids);

			foreach ($list as $i => $s)
			{
				$list[$i]['name'] = self::getTranslation($s['id'], $s, $translations, 'name', 'name');
			}
		}

		if ($lim == 1)
		{
			$list = $list[0];
		}

		return $list;
	}
	
	/**
	 * Method used to extend the subscription lifetime of the given employee.
	 *
	 * @param 	array 	$subscr 	The subscription purchased.
	 * @param 	array 	$employee 	The employee details.
	 * @param 	mixed 	$app 		The application object.
	 * @param 	mixed 	$dbo 		The database object.
	 *
	 * @return 	void
	 */
	public static function applyAdditionalSubscription($subscr, $employee, $app = null, $dbo = null)
	{
		if (is_null($app))
		{
			$app = JFactory::getApplication();
		}

		if (is_null($dbo))
		{
			$dbo = JFactory::getDbo();
		}

		$to  = 0;
		$str = "";
		
		$last_active_to = $employee['active_to'];

		if ($employee['active_to'] == -1)
		{
			// the employee owns a lifetime subscription,
			// we don't need to update its expiration
			return;
		}

		$now = time();
		
		// make sure the selected subscription is not lifetime
		if ($subscr['type'] != 5)
		{
			if ($last_active_to == 0 || $last_active_to < $now)
			{
				// the employee was pending or expired
				$employee['active_to'] = $now;
			}

			$arr = getdate($employee['active_to']);

			switch ($subscr['type'])
			{
				case 2:
					// weekly subscription
					$add_days = 7;
					break;
				
				case 3:
					// monthly subscription
					$add_days = 30;
					break;
				
				case 4:
					// yearly subscription
					$add_days = 365;
					break;
				
				default:
					// daily subscription
					$add_days = 1;
			}

			$add_days = $add_days * $subscr['amount'] + 1;
			
			$to = mktime(0, 0, 0, $arr['mon'], $arr['mday'] + $add_days, $arr['year']);

			$config = UIFactory::getConfig();
			$str = date($config->get('dateformat') . ' ' . $config->get('timeformat'), $to);
		}
		else
		{
			// lifetime subscription
			$to  = -1;

			/**
			 * Get LIFETIME text based on the current application client.
			 *
			 * @since 1.6.1
			 */
			if ($app->isSite())
			{
				$str = JText::_('VAPACCOUNTVALIDTHRU1');
			}
			else
			{
				$str = JText::_('VAPSUBSCRTYPE5');
			}
		}
		
		$q = $dbo->getQuery(true);
		
		// update employee expiration
		$q->update($dbo->qn('#__vikappointments_employee'));
		$q->set($dbo->qn('active_to') . ' = ' . $to);
		$q->set($dbo->qn('listable') . ' = 1');

		if ($last_active_to == 0)
		{
			$q->set($dbo->qn('active_since') . ' = ' . $now);
		}

		$q->where($dbo->qn('id') . ' = ' . (int) $employee['id']);

		$dbo->setQuery($q);
		$dbo->execute();
		
		/**
		 * Display the message based on the current application client.
		 *
		 * @since 1.6.1
		 */
		if ($app->isSite())
		{
			$str = JText::sprintf('VAPSUBSCRIPTIONEXTENDED', $str);
		}
		else
		{
			$str = JText::sprintf('VAPSUBSCRIPTIONEXTENDED', $employee['nickname'], $str);
		}

		$app->enqueueMessage($str);
	}

	// ORDER CONFIRM
	
	/**
	 * Returns the order details of the given ID.
	 *
	 * @param 	integer  $order_id 	 The order number (ID).
	 * @param 	string 	 $order_key  The order key (sid). If not provided, 
	 * 								 this field won't used while fetching the records.
	 * @param 	string 	 $langtag 	 The translation language. If not provided
	 * 								 it will be used the default one.
	 * 
	 * @return 	mixed 	 A list of orders on success, otherwise false.
	 */
	public static function fetchOrderDetails($order_id, $order_key = null, $langtag = null)
	{
		$dbo = JFactory::getDbo();

		$order_id = (int) $order_id;

		$q = $dbo->getQuery(true);

		// reservation
		$q->select('`r`.*');
		$q->from($dbo->qn('#__vikappointments_reservation', 'r'));

		// payment
		$q->select(array(
			$dbo->qn('gp.id', 'id_payment'),
			$dbo->qn('gp.name', 'payment_name'),
			$dbo->qn('gp.prenote', 'payment_prenote'),
			$dbo->qn('gp.note', 'payment_note'),
			$dbo->qn('gp.charge', 'payment_charge'),
		));
		$q->leftjoin($dbo->qn('#__vikappointments_gpayments', 'gp') . ' ON ' . $dbo->qn('gp.id') . ' = ' . $dbo->qn('r.id_payment'));
		
		// employee
		$q->select(array(
			$dbo->qn('e.id', 'empid'),
			$dbo->qn('e.nickname', 'ename'),
			$dbo->qn('e.email', 'empmail'),
			$dbo->qn('e.timezone', 'ord_timezone'),
			$dbo->qn('e.notify'),
		));
		$q->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('r.id_employee'));

		// service
		$q->select(array(
			$dbo->qn('s.id', 'serid'),
			$dbo->qn('s.name', 'sname'),
		));
		$q->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('r.id_service'));

		// options, variations
		$q->select(array(
			$dbo->qn('o.id', 'optid'),
			$dbo->qn('o.name', 'oname'),
			$dbo->qn('o.single', 'osingle'),
			$dbo->qn('ao.inc_price'),
			$dbo->qn('ao.quantity'),
			$dbo->qn('v.id', 'id_variation'),
			$dbo->qn('v.name', 'var_name'),
		));
		$q->leftjoin($dbo->qn('#__vikappointments_res_opt_assoc', 'ao') . ' ON ' . $dbo->qn('ao.id_reservation') . ' = ' . $dbo->qn('r.id'));
		$q->leftjoin($dbo->qn('#__vikappointments_option', 'o') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('ao.id_option'));
		$q->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('v.id') . ' = ' . $dbo->qn('ao.id_variation'));

		// joomla user
		$q->select(array(
			$dbo->qn('ju.id', 'jid'),
			$dbo->qn('ju.name', 'user_name'),
			$dbo->qn('ju.username', 'user_uname'),
			$dbo->qn('ju.email', 'user_email'),
		));
		$q->leftjoin($dbo->qn('#__vikappointments_users', 'u') . ' ON ' . $dbo->qn('u.id') . ' = ' . $dbo->qn('r.id_user'));
		$q->leftjoin($dbo->qn('#__users', 'ju') . ' ON ' . $dbo->qn('ju.id') . ' = ' . $dbo->qn('u.jid'));

		$q->where($dbo->qn('r.closure') . ' = 0');

		if (!is_null($order_key))
		{
			$q->where($dbo->qn('r.sid') . ' = ' . $dbo->q($order_key));
		}

		$q->andWhere(array(
			$dbo->qn('r.id') . ' = ' . $order_id,
			$dbo->qn('r.id_parent') . ' = ' . $order_id,
		), 'OR');

		$q->order($dbo->qn('r.id') . ' ASC');
		
		$dbo->setQuery($q);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			return false;
		}

		$assoc = $dbo->loadAssocList();

		$cf_employees = array();
		
		$lid = $order_index = -1;

		for ($i = 0; $i < count($assoc); $i++)
		{
			if ($assoc[$i]['view_emp'])
			{
				$cf_employees[] = $assoc[$i]['id_employee'];
			}

			if ($lid != $assoc[$i]['id'])
			{
				$assoc[$i]['options'] = array();
				$order_index = $i;
				$lid = $assoc[$i]['id'];
			}

			if (!empty($assoc[$i]['oname']))
			{
				$option = array(
					'id' 			=> $assoc[$i]['optid'],
					'id_variation' 	=> $assoc[$i]['id_variation'],
					'name' 			=> $assoc[$i]['oname'],
					'var_name' 		=> $assoc[$i]['var_name'],
					'price' 		=> $assoc[$i]['inc_price'],
					'quantity' 		=> $assoc[$i]['quantity'],
					'single' 		=> $assoc[$i]['osingle'],
				);

				// inject formatted values
				$option['full_name'] 			= $option['name'] . ($option['var_name'] ? ' - ' . $option['var_name'] : '');
				$option['formatted_quantity'] 	= $option['single'] ? 'x' . $option['quantity'] : '';
				$option['formatted_price']		= self::printPriceCurrencySymb($option['price']);
				
				$assoc[$order_index]['options'][] = $option;
			}
		}
		
		if (empty($langtag))
		{
			$langtag = $assoc[0]['langtag'];
			if (empty($langtag))
			{
				$langtag = JFactory::getLanguage()->getTag();
			}
		}
		else
		{
			$assoc[0]['langtag'] = $langtag;
		}
		
		$lang_services  = self::getTranslatedServices('', $langtag, $dbo);
		$lang_employees = self::getTranslatedEmployees('', $langtag, $dbo);
		$lang_options   = self::getTranslatedOptions('', $langtag, $dbo);
		$lang_payments  = self::getTranslatedPayments('', $langtag, $dbo);
		
		$app = array();
		foreach ($assoc as $a)
		{
			// ignore duplicate rows which contain the option details
			if (array_key_exists('options', $a))
			{
				$a['ename'] = self::getTranslation($a['empid'], $a, $lang_employees, 'ename', 'nickname');
				$a['sname'] = self::getTranslation($a['serid'], $a, $lang_services, 'sname', 'name');

				$a['payment_name'] 		= self::getTranslation($a['id_payment'], $a, $lang_payments, 'payment_name', 'name');
				$a['payment_prenote'] 	= self::getTranslation($a['id_payment'], $a, $lang_payments, 'payment_prenote', 'prenote');
				$a['payment_note'] 		= self::getTranslation($a['id_payment'], $a, $lang_payments, 'payment_note', 'note');
				
				for ($i = 0; $i < count($a['options']); $i++)
				{
					$a['options'][$i]['name'] = self::getTranslation($a['options'][$i]['id'], $a['options'][$i], $lang_options, 'name', 'name');
					$vars_json = self::getTranslation($a['options'][$i]['id'], $a['options'][$i], $lang_options, 'vars_json', 'vars_json', array());
					$a['options'][$i]['var_name'] = (!empty($vars_json[$a['options'][$i]['id_variation']]) ? $vars_json[$a['options'][$i]['id_variation']] : $a['options'][$i]['var_name']);
				}
				
				$app[] = $a;
			}
		}

		// INJECT FORMATTED FIELDS (such as date and price)

		$config 	= UIFactory::getConfig();
		$default_tz = date_default_timezone_get();

		foreach ($app as $k => $v)
		{
			if (empty($v['ord_timezone']))
			{
				$v['ord_timezone'] = $default_tz;
			}

			self::setCurrentTimezone($v['ord_timezone']);

			// formatted checkin date and time
			$app[$k]['formatted_checkin'] = date($config->get('dateformat') . ' ' . $config->get('timeformat'), $v['checkin_ts']);
			// formatted total cost
			$app[$k]['formatted_total'] = self::printPriceCurrencySymb($v['total_cost']);
			// formatted duration
			$app[$k]['formatted_duration'] = self::formatMinutesToTime($v['duration'], $config->getBool('formatduration'));
			
			// formatted location
			$app[$k]['formatted_location'] = '';

			$location = self::getEmployeeLocationFromTime($v['id_employee'], $v['id_service'], $v['checkin_ts']);

			if ($location !== false)
			{
				$loc_str = self::locationToString(self::fillEmployeeLocation($location));

				if (!empty($loc_str))
				{
					$app[$k]['formatted_location'] = $loc_str;
				}
			}
		}

		// reset timezone
		self::setCurrentTimezone($default_tz);

		// CUSTOM FIELDS TRANSLATION

		// get list containing employees unique IDs
		$cf_employees = array_unique($cf_employees);
		// If the list contained only one employee, use it.
		// Otherwse retrieve only the global custom fields.
		$id_employee  = count($cf_employees) == 1 ? reset($cf_employees) : -1;

		// get the fields for the customers
		$fields = VAPCustomFields::getList(0, $id_employee);
		// translate the fields
		VAPCustomFields::translate($fields, $langtag);

		// decode stored CF data
		$data = (array) json_decode($app[0]['custom_f'], true);
		
		// translate CF data object
		$data = VAPCustomFields::translateObject($data, $fields);

		// update order info
		$app[0]['custom_f'] = json_encode($data);
		
		return $app;
	}

	/**
	 * Countes the number of services within the purchased packages that can be still used.
	 *
	 * @param 	integer  $id_ser 	The service ID.
	 * @param 	integer  $id_user 	The user ID. If not provided, 
	 * 								the current user will be retrieved.
	 *
	 * @return 	integer  The remaining number of services.
	 *
	 * @uses 	countRemainingServicePackages()
	 */
	public static function countRemainingServicePackages($id_ser, $id_user = null)
	{
		$dbo = JFactory::getDbo();

		if (!$id_user)
		{
			$user = JFactory::getUser();

			if ($user->guest)
			{
				return 0;
			}

			$id_user = $user->id;

			// make relation with Joomla ID
			$user_column = 'u.jid';
		}
		else
		{
			// make relation using customer ID
			$user_column = 'o.id_user';
		}

		$q = $dbo->getQuery(true)
			->select('SUM(' . $dbo->qn('i.num_app') . ' - ' . $dbo->qn('i.used_app') . ') AS ' . $dbo->qn('count'))
			->from($dbo->qn('#__vikappointments_package_order', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_package_order_item', 'i') . ' ON ' . $dbo->qn('i.id_order') . ' = ' . $dbo->qn('o.id'))
			->leftjoin($dbo->qn('#__vikappointments_package_service', 'a') . ' ON ' . $dbo->qn('a.id_package') . ' = ' . $dbo->qn('i.id_package'))
			->leftjoin($dbo->qn('#__vikappointments_users', 'u') . ' ON ' . $dbo->qn('o.id_user') . ' = ' . $dbo->qn('u.id'))
			->where(array(
				$dbo->qn('o.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn($user_column) . ' = ' . $id_user,
				$dbo->qn('a.id_service') . ' = ' . (int) $id_ser,
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadResult();
		}

		return 0;
	}

	/**
	 * Redeems the remaining packages for the services contained within the cart.
	 *
	 * @param 	mixed 	&$cart 	The cart instance.
	 * @param 	mixed 	$dbo 	The database object.
	 *
	 * @return 	void
	 */
	public static function usePackagesForServicesInCart(&$cart, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		if (JFactory::getUser()->guest)
		{
			return;
		}

		$count_map = array();

		foreach ($cart->getItemsList() as $item)
		{
			$item->setDiscounted(0);

			if (!array_key_exists($item->getID(), $count_map))
			{
				$count_map[$item->getID()] = self::countRemainingServicePackages($item->getID());
			}

			if ($count_map[$item->getID()] - $item->getPeople() >= 0)
			{
				$count_map[$item->getID()] -= $item->getPeople();
				$item->setDiscounted(1);
			}
		}
	}

	/**
	 * Registers all the packages that have been used to purchase a service.
	 * 
	 * @param 	array 	 $order_details  The order details list.
	 * @param 	boolean  $increase 		 True to increase the number of used packages,
	 * 									 false to free them.
	 *
	 * @return 	integer  The number of packages used.
	 */
	public static function registerPackagesUsed($order_details, $increase = true)
	{
		$dbo = JFactory::getDbo();

		if ($order_details === null || $order_details[0]['id_user'] <= 0)
		{
			// order details not found or user not registered
			return;
		}

		$id_user = $order_details[0]['id_user'];

		// count the total number of guests for each service in the list

		$count_map = array();

		for ($i = (count($order_details) == 1 ? 0 : 1); $i < count($order_details); $i++)
		{
			$id_ser = $order_details[$i]['id_service'];

			if (!array_key_exists($id_ser, $count_map))
			{
				$count_map[$id_ser] = 0;
			}

			$count_map[$id_ser] += $order_details[$i]['people'];
		}

		$now = time();

		$reedemed = 0;

		// iterate the map
		foreach ($count_map as $id_ser => $count)
		{
			// get all the packages that can be redeemed for the service/user pair
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('i.id', 'i.num_app', 'i.used_app')))
				->from($dbo->qn('#__vikappointments_package_order', 'o'))
				->leftjoin($dbo->qn('#__vikappointments_package_order_item', 'i') . ' ON ' . $dbo->qn('i.id_order') . ' = ' . $dbo->qn('o.id'))
				->leftjoin($dbo->qn('#__vikappointments_package_service', 'a') . ' ON ' . $dbo->qn('a.id_package') . ' = ' . $dbo->qn('i.id_package'))
				->where(array(
					$dbo->qn('o.status') . ' = ' . $dbo->q('CONFIRMED'),
					$dbo->qn('o.id_user') . ' = ' . (int) $id_user,
					$dbo->qn('a.id_service') . ' = ' . (int) $id_ser,
				));

			if ($increase)
			{
				// restrict number of used apps only if we are increasing
				$q->where($dbo->qn('i.used_app') . ' < ' . $dbo->qn('i.num_app'));
			}

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$rows = $dbo->loadObjectList();

				$i = 0;
				// iterate until the total number of services is redeemed,
				// or at least until we reach the end of the array
				while ($count > 0 && $i < count($rows))
				{
					$r = $rows[$i];

					/**
					 * Evaluates if we have to increase or decrease the number
					 * of used packages.
					 *
					 * @since 1.6.3
					 */
					if ($increase)
					{
						// Get the number of packages to redeem.
						// Obtain the minimum value between the total services and the remaining packages.
						$used = min(array($count, $r->num_app - $r->used_app));

						// increase used packages
						$r->used_app += $used;
					}
					else
					{
						// Get the number of packages to redeem.
						// Obtain the minimum value between the total services and the number of used packages.
						$used = min(array($count, $r->used_app));

						// decrease used packages
						$r->used_app -= $used;
					}

					$r->modifiedon = $now;

					// update the record by increasing the total number of units used
					$dbo->updateObject('#__vikappointments_package_order_item', $r, 'id');

					// decrease the services count by the number of used packages
					$count -= $used;
					$i++;

					// increase the total number of redeemed packages
					$reedemed += $used;
				}
			}
		}

		return $reedemed;
	}

	// ADMIN E-MAIL

	/**
	 * Loads the admin e-mail template that should be parsed.
	 *
	 * @param 	array 	$orders 	The orders list.
	 *
	 * @return 	string 	The e-mail template.
	 */
	public static function loadAdminEmailTemplate(array $orders)
	{
		/**
		 * @deprecated 1.8 	Use _JEXEC instead
		 */
		defined('_VIKAPPOINTMENTSEXEC') or define('_VIKAPPOINTMENTSEXEC', '1');

		ob_start();
		include VAPHELPERS . DIRECTORY_SEPARATOR . 'mail_tmpls' . DIRECTORY_SEPARATOR . self::getAdminMailTemplateName();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Sends the notification e-mail to the administrator(s)
	 * and related employee(s).
	 *
	 * @param 	array 	$order_details 	The orders that should be notified.
	 *
	 * @return 	void
	 */
	public static function sendAdminEmail($order_details)
	{
		if (!$order_details)
		{
			return;
		}
		
		self::loadLanguage(self::getDefaultLanguage('site'));
		
		$send_when 			= self::getSendMailWhen();
		$admin_mail_list 	= self::getAdminMailList();
		$sendermail 		= self::getSenderMail();
		$adminname 			= self::getAgencyName();

		$subject = JText::sprintf('VAPADMINEMAILSUBJECT', $adminname);
		
		$admin_tmpl = self::loadAdminEmailTemplate($order_details);
		$html_mess 	= self::parseAdminEmailTemplate($admin_tmpl, $order_details);

		$emp_details = self::filterOrdersByEmployee($order_details);

		$ics_prop = self::getAttachmentPropertiesICS();
		$csv_prop = self::getAttachmentPropertiesCSV();

		$vik = UIApplication::getInstance();
		
		// CUSTOM FIELDS ATTACHMENTS
		$order_details[0]['uploads'] = json_decode($order_details[0]['uploads']);
		$custom_f_attach = self::includeMailAttachments($order_details);
		
		if ($send_when['admin'] != 0 && ($send_when['admin'] == 2 || $order_details[0]['status'] == 'CONFIRMED'))
		{
			$admin_attachments = array();

			// ADMIN ICS GENERATOR //
			$ics_file_path = "";
			if ($ics_prop['admin'])
			{
				$ics_file_path = self::composeFileICS($order_details[0]['id'], true, -1);

				if (!empty($ics_file_path))
				{
					$admin_attachments[] = $ics_file_path;
				}
			}

			// ADMIN CSV GENERATOR //
			$csv_file_path = "";
			if ($csv_prop['admin'])
			{
				$csv_file_path = self::composeFileCSV($order_details[0]['id'], true, -1);
				if (!empty($csv_file_path))
				{
					$admin_attachments[] = $csv_file_path;
				}
			}
			
			$admin_attachments = array_merge($admin_attachments, $custom_f_attach);
			
			foreach ($admin_mail_list as $_m)
			{
				$vik->sendMail($sendermail, $adminname, $_m, $sendermail, $subject, $html_mess, $admin_attachments, true);
			}
			
			if (!empty($ics_file_path) && file_exists($ics_file_path))
			{
				unlink($ics_file_path);
			}

			if (!empty($csv_file_path) && file_exists($csv_file_path))
			{
				unlink($csv_file_path);
			}
		}

		if ($send_when['employee'] != 0 && ($send_when['employee'] == 2 || $order_details[0]['status'] == 'CONFIRMED'))
		{
			foreach ($emp_details as $emp_mail => $emp_order_details)
			{
				$emp_attachments = array();
				
				// EMPLOYEE ICS GENERATOR //
				$ics_file_path = "";
				if ($ics_prop['employee'])
				{
					$ics_file_path = self::composeFileICS($order_details[0]['id'], true, $emp_order_details[0]['id_employee']);
					if (!empty($ics_file_path))
					{
						$emp_attachments[] = $ics_file_path;
					}
				}

				// EMPLOYEE CSV GENERATOR //
				$csv_file_path = "";
				if ($csv_prop['employee'])
				{
					$csv_file_path = self::composeFileCSV($order_details[0]['id'], true, $emp_order_details[0]['id_employee']);
					if (!empty($csv_file_path))
					{
						$emp_attachments[] = $csv_file_path;
					}
				}
				
				$emp_attachments = array_merge($emp_attachments, $custom_f_attach);

				/**
				 * Reload employee e-mail template using the orders related to the specified employee.
				 *
				 * @since 1.6
				 */
				$emp_tmpl = self::loadEmployeeEmailTemplate($emp_order_details);
				$_html 	  = self::parseEmployeesEmailTemplate($emp_tmpl, $emp_order_details);
				
				$vik->sendMail($sendermail, $adminname, $emp_mail, $admin_mail_list[0], $subject, $_html, $emp_attachments, true);
				
				if (!empty($ics_file_path) && file_exists($ics_file_path))
				{
					unlink($ics_file_path);
				}

				if (!empty($csv_file_path) && file_exists($csv_file_path))
				{
					unlink($csv_file_path);
				}
			}
		}

		self::destroyMailAttachments($custom_f_attach);
	}
	
	/**
	 * Method used to parse the e-mail template for the administrator(s).
	 *
	 * @param 	string 	$tmpl 			The template string to parse.
	 * @param 	array 	$order_details 	The orders list.
	 *
	 * @return 	string 	The parsed template.
	 */
	public static function parseAdminEmailTemplate($tmpl, $order_details)
	{
		// parse coupon string

		if (!empty($order_details[0]['coupon_str']))
		{
			list($code, $pt, $value) = explode(';;', $order_details[0]['coupon_str']);
			$coupon_str = $code . " : " . ($pt == 1 ? $value . '%' : self::printPriceCurrencySymb($value));
		}
		else
		{
			$coupon_str = JText::_('VAPADMINEMAILNOCOUPON');
		}

		// parse payment name

		$payment_name = !empty($order_details[0]['payment_name']) ? $order_details[0]['payment_name'] : JText::_('VAPADMINEMAILNOPAYMENT');

		// parse order total cost

		$order_total = self::printPriceCurrencySymb($order_details[0]['total_cost'] + $order_details[0]['payment_charge']);

		// fetch appointment details

		/**
		 * @deprecated 1.8 	the appointment details are parsed within the e-mail template
		 */
		$appointment_details = "";
		for ($i = ($order_details[0]['id_service'] == -1 ? 1 : 0); $i < count($order_details); $i++)
		{
			$row = $order_details[$i];
			
			$appointment_details .= '<div class="appointment">';
			$appointment_details .= '<div class="content ' . ($row['total_cost'] > 0 || count($row['options']) ? '' : 'fill-bottom') . '">';
			$appointment_details .= $row['sname'] . ' - ' . $row['ename'] . '<br />';
			$appointment_details .= $row['formatted_checkin'] . ' - ' . $row['formatted_duration'];
			$appointment_details .= '</div>';
			
			if (count($row['options']))
			{
				$appointment_details .= '<div class="options-list'.($row['total_cost'] > 0 ? '' : ' fill-bottom').'">';

				foreach ($row['options'] as $opt)
				{
					$appointment_details .= '<div class="option">';
					$appointment_details .= '<div class="name">- ' . $opt['full_name'] . '</div>';
					$appointment_details .= '<div class="quantity">' . $opt['formatted_quantity'] . '</div>';
					if ($opt['price'] != 0)
					{
						$appointment_details .= '<div class="price">' . $opt['formatted_price'] . '</div>';
					}
					$appointment_details .= '</div>';
				}
				$appointment_details .= '</div>';
			}

			if ($row['total_cost'] > 0)
			{
				$appointment_details .= '<div class="cost"><span>' . $row['formatted_total'] . '</span></div>';
			}

			$appointment_details .= '</div>';
		}

		// customer details
		
		$custom_fields = json_decode($order_details[0]['custom_f'], true);

		/**
		 * @deprecated 1.8 	the customer details are parsed within the e-mail template
		 */
		$customer_details = "";
		foreach ($custom_fields as $kc => $vc)
		{
			$customer_details .= '<div class="info">';
			$customer_details .= '<div class="label">'.JText::_($kc).':</div>';
			$customer_details .= '<div class="value">'.$vc.'</div>';
			$customer_details .= '</div>';
		}

		// joomla user details
		$user_details = '';
		if (strlen($order_details[0]['user_email']) > 0)
		{
			/**
			 * @deprecated 1.8 	the joomla user details should be parsed within the e-mail template (if needed)
			 */

			$user_details = '<div class="separator"> </div>
			<div class="customer-details-wrapper">
				<div class="title">'.JText::_('VAPUSERDETAILS').'</div>
					<div class="customer-details">
						<div class="info">
							<div class="label">'.JText::_('VAPREGFULLNAME').':</div>
							<div class="value">'.$order_details[0]['user_name'].'</div>
						</div>
						<div class="info">
							<div class="label">'.JText::_('VAPREGUNAME').':</div>
							<div class="value">'.$order_details[0]['user_uname'].'</div>
						</div>
						<div class="info">
							<div class="label">'.JText::_('VAPREGEMAIL').':</div>
							<div class="value">'.$order_details[0]['user_email'].'</div>
						</div>
					</div>
				</div>';
		}

		$vik = UIApplication::getInstance();

		// order link

		$order_link_href = "index.php?option=com_vikappointments&view=order&ordnum={$order_details[0]['id']}&ordkey={$order_details[0]['sid']}";
		$order_link_href = $vik->routeForExternalUse($order_link_href);

		$confirmation_link = "";
		if ($order_details[0]['status'] == 'PENDING')
		{
			$confirmation_link = "index.php?option=com_vikappointments&task=confirmord&oid={$order_details[0]['id']}&conf_key={$order_details[0]['conf_key']}";
			$confirmation_link = $vik->routeForExternalUse($confirmation_link);

			// $confirmation_link .= '<div class="order-link">';
			// $confirmation_link .= '<div class="title">'.JText::_('VAPCONFIRMATIONLINK').'</div>';
			// $confirmation_link .= '<div class="content">';
			// $confirmation_link .= '<a href="'.$confirmation_link_href.'">'.$confirmation_link_href.'</a>';
			// $confirmation_link .= '</div>';
			// $confirmation_link .= '</div>';
		}

		// logo

		$logo_name = self::getCompanyLogoPath();
		
		$logo_str = "";
		if (!empty($logo_name) && file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $logo_name))
		{ 
			$logo_str = '<img src="' . VAPMEDIA_URI . $logo_name . '" />';
		}

		// order status color

		switch ($order_details[0]['status'])
		{
			case 'CONFIRMED':
				$order_status_color = '#006600';
				break;

			case 'PENDING':
				$order_status_color = '#D9A300';
				break;

			case 'REMOVED':
				$order_status_color = '#B20000';
				break;

			case 'CANCELED':
				$order_status_color = '#F01B17';
				break;

			default:
				$order_status_color = 'inherit';
		}

		// replace tags from template

		$tmpl = str_replace('{company_name}'		, self::getAgencyName()									, $tmpl);
		$tmpl = str_replace('{order_number}'		, $order_details[0]['id']								, $tmpl);
		$tmpl = str_replace('{order_key}'			, $order_details[0]['sid']								, $tmpl);
		$tmpl = str_replace('{order_status_class}'	, strtolower($order_details[0]['status'])				, $tmpl);
		$tmpl = str_replace('{order_status}'		, JText::_('VAPSTATUS' . $order_details[0]['status'])	, $tmpl);
		$tmpl = str_replace('{order_status_color}'	, $order_status_color									, $tmpl);
		$tmpl = str_replace('{order_payment}'		, $payment_name											, $tmpl);
		$tmpl = str_replace('{order_coupon_code}'	, $coupon_str											, $tmpl);
		$tmpl = str_replace('{order_total_cost}'	, $order_total 											, $tmpl);
		$tmpl = str_replace('{order_link}'			, $order_link_href										, $tmpl);
		$tmpl = str_replace('{confirmation_link}'	, $confirmation_link 									, $tmpl);
		$tmpl = str_replace('{logo}'				, $logo_str												, $tmpl);

		/**
		 * @deprecated 1.8
		 */
		$tmpl = str_replace('{appointment_details}'	, $appointment_details	, $tmpl);
		$tmpl = str_replace('{customer_details}'	, $customer_details		, $tmpl);
		$tmpl = str_replace('{user_details}'		, $user_details			, $tmpl);

		return $tmpl;
	}

	// EMPLOYEE E-MAIL

	/**
	 * Filters the orders (in case of shop enabled) by employee.
	 * The method will return an associative key built as follows:
	 * - the key is the e-mail of the employee;
	 * - the value is the list of all the related orders.
	 *
	 * @param 	array 	$order_details 	The orders to filter.
	 *
	 * @return 	array 	The resulting list.
	 */
	public static function filterOrdersByEmployee($order_details)
	{
		$arr = array();

		for ($i = ($order_details[0]['id_service'] == -1 ? 1 : 0); $i < count($order_details); $i++)
		{
			$row = $order_details[$i];

			if (!empty($row['empmail'])) 
			{
				if (empty($arr[$row['empmail']]))
				{
					$arr[$row['empmail']] = array();
					
					if ($i != 0)
					{
						// push always the parent order
						$arr[$row['empmail']][] = $order_details[0];
						// unset total cost
						$arr[$row['empmail']][0]['total_cost'] = 0.0;
					}
				}

				$arr[$row['empmail']][] = $row;

				if ($i != 0)
				{
					// recalculate the sum of the related orders
					$arr[$row['empmail']][0]['total_cost'] += $row['total_cost'];
				}
			}
		}

		return $arr;
	}

	/**
	 * Loads the employee e-mail template that should be parsed.
	 *
	 * @param 	array 	$orders 	The orders list.
	 *
	 * @return 	string 	The e-mail template.
	 */
	public static function loadEmployeeEmailTemplate(array $orders)
	{
		/**
		 * @deprecated 1.8 	Use _JEXEC instead
		 */
		defined('_VIKAPPOINTMENTSEXEC') or define('_VIKAPPOINTMENTSEXEC', '1');

		ob_start();
		include VAPHELPERS . DIRECTORY_SEPARATOR . 'mail_tmpls' . DIRECTORY_SEPARATOR . self::getEmployeeMailTemplateName();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
	
	/**
	 * Method used to parse the e-mail template for the employee(s).
	 *
	 * @param 	string 	$tmpl 			The template string to parse.
	 * @param 	array 	$order_details 	The orders list.
	 *
	 * @return 	string 	The parsed template.
	 */
	public static function parseEmployeesEmailTemplate($tmpl, $order_details)
	{
		// parse coupon string

		if (!empty($order_details[0]['coupon_str']))
		{
			list($code, $pt, $value) = explode(';;', $order_details[0]['coupon_str']);
			$coupon_str = $code . " : " . ($pt == 1 ? $value . '%' : self::printPriceCurrencySymb($value));
		}
		else
		{
			$coupon_str = JText::_('VAPADMINEMAILNOCOUPON');
		}

		// parse payment name

		$payment_name = !empty($order_details[0]['payment_name']) ? $order_details[0]['payment_name'] : JText::_('VAPADMINEMAILNOPAYMENT');

		// parse order total cost

		$order_total = self::printPriceCurrencySymb($order_details[0]['total_cost'] + $order_details[0]['payment_charge']);

		// fetch appointment details

		/**
		 * @deprecated 1.8 	the appointment details are parsed within the e-mail template
		 */
		$appointment_details = "";
		for ($i = ($order_details[0]['id_service'] == -1 ? 1 : 0); $i < count($order_details); $i++)
		{
			$row = $order_details[$i];
			
			$appointment_details .= '<div class="appointment">';
			$appointment_details .= '<div class="content ' . ($row['total_cost'] > 0 || count($row['options']) ? '' : 'fill-bottom') . '">';
			$appointment_details .= $row['id'] . '-' . $row['sid'] . '<br />';
			$appointment_details .= $row['sname'] . '<br />';
			$appointment_details .= $row['formatted_checkin'] . ' - ' . $row['formatted_duration'];
			$appointment_details .= '</div>';
			
			if (count($row['options']))
			{
				$appointment_details .= '<div class="options-list'.($row['total_cost'] > 0 ? '' : ' fill-bottom').'">';

				foreach ($row['options'] as $opt)
				{
					$appointment_details .= '<div class="option">';
					$appointment_details .= '<div class="name">- ' . $opt['full_name'] . '</div>';
					$appointment_details .= '<div class="quantity">' . $opt['formatted_quantity'] . '</div>';
					if ($opt['price'] != 0)
					{
						$appointment_details .= '<div class="price">' . $opt['formatted_price'] . '</div>';
					}
					$appointment_details .= '</div>';
				}
				$appointment_details .= '</div>';
			}

			if ($row['total_cost'] > 0)
			{
				$appointment_details .= '<div class="cost"><span>' . $row['formatted_total'] . '</span></div>';
			}

			$appointment_details .= '</div>';
		}

		// customer details
		
		$custom_fields = json_decode($order_details[0]['custom_f'], true);

		/**
		 * @deprecated 1.8 	the customer details are parsed within the e-mail template
		 */
		$customer_details = "";
		foreach ($custom_fields as $kc => $vc)
		{
			$customer_details .= '<div class="info">';
			$customer_details .= '<div class="label">'.JText::_($kc).':</div>';
			$customer_details .= '<div class="value">'.$vc.'</div>';
			$customer_details .= '</div>';
		}

		// joomla user details
		$user_details = '';
		if (strlen($order_details[0]['user_email']) > 0)
		{
			/**
			 * @deprecated 1.8 	the joomla user details should be parsed within the e-mail template (if needed)
			 */

			$user_details = '<div class="separator"> </div>
			<div class="customer-details-wrapper">
				<div class="title">'.JText::_('VAPUSERDETAILS').'</div>
					<div class="customer-details">
						<div class="info">
							<div class="label">'.JText::_('VAPREGFULLNAME').':</div>
							<div class="value">'.$order_details[0]['user_name'].'</div>
						</div>
						<div class="info">
							<div class="label">'.JText::_('VAPREGUNAME').':</div>
							<div class="value">'.$order_details[0]['user_uname'].'</div>
						</div>
						<div class="info">
							<div class="label">'.JText::_('VAPREGEMAIL').':</div>
							<div class="value">'.$order_details[0]['user_email'].'</div>
						</div>
					</div>
				</div>';
		}

		$vik = UIApplication::getInstance();

		// order link

		$order_link_href = "index.php?option=com_vikappointments&view=order&ordnum={$order_details[0]['id']}&ordkey={$order_details[0]['sid']}";
		$order_link_href = $vik->routeForExternalUse($order_link_href);

		$confirmation_link = "";
		if ($order_details[0]['status'] == 'PENDING' && count($order_details) == 1)
		{
			/**
			 * @todo 	If the employee is not authorised to confirm the orders manually,
			 * 			the system should not report the confirmation link within the e-mail template.
			 *
			 * 			Or should the confirmation link be removed directly from the template?
			 * 			Since the confirmation link may be placed directly within the template, it could be
			 * 			complex to add such type of restriction.
			 * 
			 * @see 	EmployeeAuth::resconfirm()
			 */

			$confirmation_link = "index.php?option=com_vikappointments&task=confirmord&oid={$order_details[0]['id']}&conf_key={$order_details[0]['conf_key']}";
			$confirmation_link = $vik->routeForExternalUse($confirmation_link);

			// $confirmation_link .= '<div class="order-link">';
			// $confirmation_link .= '<div class="title">'.JText::_('VAPCONFIRMATIONLINK').'</div>';
			// $confirmation_link .= '<div class="content">';
			// $confirmation_link .= '<a href="'.$confirmation_link_href.'">'.$confirmation_link_href.'</a>';
			// $confirmation_link .= '</div>';
			// $confirmation_link .= '</div>';
		}

		// logo

		$logo_name = self::getCompanyLogoPath();
		
		$logo_str = "";
		if (!empty($logo_name) && file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $logo_name))
		{ 
			$logo_str = '<img src="' . VAPMEDIA_URI . $logo_name . '" />';
		}

		// order status color

		switch ($order_details[0]['status'])
		{
			case 'CONFIRMED':
				$order_status_color = '#006600';
				break;

			case 'PENDING':
				$order_status_color = '#D9A300';
				break;

			case 'REMOVED':
				$order_status_color = '#B20000';
				break;

			case 'CANCELED':
				$order_status_color = '#F01B17';
				break;

			default:
				$order_status_color = 'inherit';
		}

		// replace tags from template

		$tmpl = str_replace('{company_name}'		, self::getAgencyName()									, $tmpl);
		$tmpl = str_replace('{order_status_class}'	, strtolower($order_details[0]['status'])				, $tmpl);
		$tmpl = str_replace('{order_status}'		, JText::_('VAPSTATUS' . $order_details[0]['status'])	, $tmpl);
		$tmpl = str_replace('{order_status_color}'	, $order_status_color									, $tmpl);
		$tmpl = str_replace('{order_payment}'		, $payment_name											, $tmpl);
		$tmpl = str_replace('{order_coupon_code}'	, $coupon_str											, $tmpl);
		$tmpl = str_replace('{order_total_cost}'	, $order_total											, $tmpl);
		$tmpl = str_replace('{order_link}'			, $order_link_href										, $tmpl);
		$tmpl = str_replace('{confirmation_link}'	, $confirmation_link									, $tmpl);
		$tmpl = str_replace('{logo}'				, $logo_str												, $tmpl);

		/**
		 * @deprecated 1.8
		 */
		$tmpl = str_replace('{appointment_details}'	, $appointment_details, $tmpl);
		$tmpl = str_replace('{customer_details}'	, $customer_details, $tmpl);
		$tmpl = str_replace('{user_details}'		, $user_details, $tmpl);

		return $tmpl;
	}

	// CUSTOMER E-MAIL
	
	/**
	 * Loads the e-mail template that should be parsed.
	 *
	 * @param 	array 	$orders 	The orders list.
	 *
	 * @return 	string 	The e-mail template.
	 */
	public static function loadEmailTemplate(array $orders)
	{
		/**
		 * @deprecated 1.8 	Use _JEXEC instead
		 */
		defined('_VIKAPPOINTMENTSEXEC') or define('_VIKAPPOINTMENTSEXEC', '1');

		ob_start();
		include VAPHELPERS . DIRECTORY_SEPARATOR . 'mail_tmpls' . DIRECTORY_SEPARATOR . self::getMailTemplateName();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
	
	/**
	 * Sends the notification e-mail to the customer.
	 *
	 * @param 	array 	$order_details 	The orders that should be notified.
	 *
	 * @return 	void
	 */
	public static function sendCustomerEmail($order_details)
	{
		if (!$order_details)
		{
			return;
		}
		
		self::loadLanguage($order_details[0]['langtag']);
		
		$admin_mail_list 	= self::getAdminMailList();
		$sendermail 		= self::getSenderMail();
		$fromname 			= self::getAgencyName();
		
		$subject = JText::sprintf('VAPCUSTOMEREMAILSUBJECT', $fromname);
		
		$tmpl = self::loadEmailTemplate($order_details);
		$html = self::parseEmailTemplate($tmpl, $order_details);
		
		$ics_prop = self::getAttachmentPropertiesICS();
		$csv_prop = self::getAttachmentPropertiesCSV();
		
		$attachments = array();
		$mail_attach = self::getMailAttachmentURL();

		if (!empty($mail_attach))
		{
			$attachments[] = $mail_attach;
		}
			  
		$ics_file_path = "";
		if ($ics_prop['customer'])
		{
			$ics_file_path = self::composeFileICS($order_details[0]['id']);

			if (!empty($ics_file_path))
			{
				$attachments[] = $ics_file_path;
			}
		}
		
		$csv_file_path = "";
		if ($csv_prop['customer'])
		{
			$csv_file_path = self::composeFileCSV($order_details[0]['id']);

			if (!empty($csv_file_path))
			{
				$attachments[] = $csv_file_path;
			}
		}
		
		$vik = UIApplication::getInstance();
		$vik->sendMail($sendermail, $fromname, $order_details[0]['purchaser_mail'], $admin_mail_list[0], $subject, $html, $attachments, true);
		
		if (!empty($ics_file_path) && file_exists($ics_file_path))
		{
			unlink($ics_file_path);
		}

		if (!empty($csv_file_path) && file_exists($csv_file_path))
		{
			unlink($csv_file_path);
		}
	}

	/**
	 * Method used to parse the e-mail template for the customers.
	 *
	 * @param 	string 	$tmpl 			The template string to parse.
	 * @param 	array 	$order_details 	The orders list.
	 *
	 * @return 	string 	The parsed template.
	 */
	public static function parseEmailTemplate($tmpl, $order_details)
	{
		// parse payment name

		$payment_name = "";
		if (!empty($order_details[0]['payment_name']))
		{
			$payment_name = $order_details[0]['payment_name'];
			// $payment_name = '<div class="box'.($order_details[0]['total_cost'] > 0 ? '' : ' large').'">'.$order_details[0]['payment_name'].'</div>';
		}

		// parse total cost

		$total_cost = "";
		if ($order_details[0]['total_cost'] > 0)
		{
			$total_cost = self::printPriceCurrencySymb($order_details[0]['total_cost'] + $order_details[0]['payment_charge']);
			// $total_cost = '<div class="box'.(!empty($order_details[0]['payment_name']) ? '' : ' large').'">'.$total_cost.'</div>';
		}

		// parse coupon string

		$coupon_str = "";
		if (!empty($order_details[0]['coupon_str']))
		{
			list($code, $pt, $value) = explode(';;', $order_details[0]['coupon_str']);
			$coupon_str = $code . " : " . ($pt == 1 ? $value . '%' : self::printPriceCurrencySymb($value));
			// $coupon_str = '<div class="box large">'.$coupon_str.'</div>';
		}

		// fetch appointment details

		/**
		 * @deprecated 1.8 	the appointment details are parsed within the e-mail template
		 */
		$appointment_details = "";
		for ($i = ($order_details[0]['id_service'] == -1 ? 1 : 0); $i < count($order_details); $i++)
		{
			$row = $order_details[$i];
			
			$appointment_details .= '<div class="appointment">';
			$appointment_details .= '<div class="content ' . ($row['total_cost'] > 0 || count($row['options']) ? '' : 'fill-bottom') . '">';
			$appointment_details .= $row['sname'] . ' - ' . $row['ename'] . '<br />';
			$appointment_details .= $row['formatted_checkin'] . ' - ' . $row['formatted_duration'];
			$appointment_details .= '</div>';
			
			if (count($row['options']))
			{
				$appointment_details .= '<div class="options-list'.($row['total_cost'] > 0 ? '' : ' fill-bottom').'">';

				foreach ($row['options'] as $opt)
				{
					$appointment_details .= '<div class="option">';
					$appointment_details .= '<div class="name">- ' . $opt['full_name'] . '</div>';
					$appointment_details .= '<div class="quantity">' . $opt['formatted_quantity'] . '</div>';
					if ($opt['price'] != 0)
					{
						$appointment_details .= '<div class="price">' . $opt['formatted_price'] . '</div>';
					}
					$appointment_details .= '</div>';
				}
				$appointment_details .= '</div>';
			}

			if ($row['total_cost'] > 0)
			{
				$appointment_details .= '<div class="cost"><span>' . $row['formatted_total'] . '</span></div>';
			}

			$appointment_details .= '</div>';
		}

		// customer details
		
		$custom_fields = json_decode($order_details[0]['custom_f'], true);

		/**
		 * @deprecated 1.8 	the customer details are parsed within the e-mail template
		 */
		$customer_details = "";
		foreach ($custom_fields as $kc => $vc)
		{
			$customer_details .= '<div class="info">';
			$customer_details .= '<div class="label">'.JText::_($kc).':</div>';
			$customer_details .= '<div class="value">'.$vc.'</div>';
			$customer_details .= '</div>';
		}

		// joomla user details
		$user_details = '';

		if (strlen($order_details[0]['user_email']) > 0)
		{
			/**
			 * @deprecated 1.8 	the joomla user details should be parsed within the e-mail template (if needed)
			 */

			$user_details = '<div class="separator"> </div>
			<div class="customer-details-wrapper">
				<div class="title">'.JText::_('VAPUSERDETAILS').'</div>
					<div class="customer-details">
						<div class="info">
							<div class="label">'.JText::_('VAPREGFULLNAME').':</div>
							<div class="value">'.$order_details[0]['user_name'].'</div>
						</div>
						<div class="info">
							<div class="label">'.JText::_('VAPREGUNAME').':</div>
							<div class="value">'.$order_details[0]['user_uname'].'</div>
						</div>
						<div class="info">
							<div class="label">'.JText::_('VAPREGEMAIL').':</div>
							<div class="value">'.$order_details[0]['user_email'].'</div>
						</div>
					</div>
				</div>';
		}

		// order link

		$order_link_href = "index.php?option=com_vikappointments&view=order&ordnum={$order_details[0]['id']}&ordkey={$order_details[0]['sid']}";
		$order_link_href = UIApplication::getInstance()->routeForExternalUse($order_link_href);

		$cancellation_link = "";
		if ($order_details[0]['status'] == 'CONFIRMED' && self::isCancellationEnabled())
		{
			$cancellation_link = $order_link_href . "#cancel";

			// $cancellation_link .= '<div class="order-link">';
			// $cancellation_link .= '<div class="title">'.JText::_('VAPCANCELLATIONLINK').'</div>';
			// $cancellation_link .= '<div class="content">';
			// $cancellation_link .= '<a href="'.$cancellation_link_href.'">'.$cancellation_link_href.'</a>';
			// $cancellation_link .= '</div>';
			// $cancellation_link .= '</div>';
		}

		// logo

		$logo_name = self::getCompanyLogoPath();
		
		$logo_str = "";
		if (!empty($logo_name) && file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $logo_name))
		{ 
			$logo_str = '<img src="' . VAPMEDIA_URI . $logo_name . '" />';
		}

		// order status color

		switch ($order_details[0]['status'])
		{
			case 'CONFIRMED':
				$order_status_color = '#006600';
				break;

			case 'PENDING':
				$order_status_color = '#D9A300';
				break;

			case 'REMOVED':
				$order_status_color = '#B20000';
				break;

			case 'CANCELED':
				$order_status_color = '#F01B17';
				break;

			default:
				$order_status_color = 'inherit';
		}

		// replace tags from template

		$tmpl = str_replace('{company_name}'		, self::getAgencyName()									, $tmpl);
		$tmpl = str_replace('{order_number}'		, $order_details[0]['id']								, $tmpl);
		$tmpl = str_replace('{order_key}'			, $order_details[0]['sid']								, $tmpl);
		$tmpl = str_replace('{order_status_class}'	, strtolower($order_details[0]['status'])				, $tmpl);
		$tmpl = str_replace('{order_status}'		, JText::_('VAPSTATUS' . $order_details[0]['status'])	, $tmpl);
		$tmpl = str_replace('{order_status_color}'	, $order_status_color									, $tmpl);
		$tmpl = str_replace('{order_payment}'		, $payment_name											, $tmpl);
		$tmpl = str_replace('{order_payment_notes}'	, $order_details[0]['payment_note']						, $tmpl);
		$tmpl = str_replace('{order_coupon_code}'	, $coupon_str											, $tmpl);
		$tmpl = str_replace('{order_total_cost}'	, $total_cost 											, $tmpl);
		$tmpl = str_replace('{order_link}'			, $order_link_href										, $tmpl);
		$tmpl = str_replace('{cancellation_link}'	, $cancellation_link 									, $tmpl);
		$tmpl = str_replace('{logo}'				, $logo_str 											, $tmpl);

		/**
		 * @deprecated 1.8
		 */
		$tmpl = str_replace('{appointment_details}'	, $appointment_details 	, $tmpl);
		$tmpl = str_replace('{customer_details}'	, $customer_details 	, $tmpl);
		$tmpl = str_replace('{user_details}'		, $user_details 		, $tmpl);
		
		// apply custom text

		/**
		 * Retrieve the list of the services and employees booked within this order.
		 *
		 * @since 1.6
		 */
		$services_booked  = array();
		$employees_booked = array();

		foreach ($order_details as $order)
		{
			if ($order['id_service'] > 0)
			{
				$services_booked[] = $order['id_service'];
			}

			if ($order['id_employee'] > 0)
			{
				$employees_booked[] = $order['id_employee'];
			}
		}

		$services_booked = array_unique($services_booked);
		//

		$tmpl = self::parseEmailCustomText($tmpl, $order_details[0]['status'], $order_details[0]['langtag'], $services_booked, $employees_booked);

		return $tmpl;
	}

	/**
	 * Parses the e-mail custom texts.
	 *
	 * @param 	string 	$tmpl 		The e-mail template (HTML).
	 * @param 	string 	$status 	The required status.
	 * @param 	string 	$lang 		The required language.
	 * @param 	array 	$services 	A list of requested services (@since 1.6).
	 * @param 	array 	$employees 	A list of requested employees (@since 1.6).
	 *
	 * @return 	string 	The parsed HTML template.
	 */
	private static function parseEmailCustomText($tmpl, $status, $lang = null, array $services = array(), array $employees = array())
	{
		if (empty($lang))
		{
			$lang = JFactory::getLanguage()->getTag();
		}

		// get file name
		$file = self::getMailTemplateName();
		
		$rows = array();
		
		$dbo = JFactory::getDbo();

		// push 0 to catch all the records that do not specify a service or an employee
		array_unshift($services, 0);
		array_unshift($employees, 0);

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_cust_mail'))
			->where(array(
				$dbo->qn('tag') . ' = ' . $dbo->q($lang),
				$dbo->qn('file') . ' = ' . $dbo->q($file),
				$dbo->qn('status') . ' = ' . $dbo->q($status),
				$dbo->qn('id_service') . ' IN (' . implode(', ', array_map('intval', $services)) . ')',
				$dbo->qn('id_employee') . ' IN (' . implode(', ', array_map('intval', $employees)) . ')',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
		}

		// lookup of available positions
		$positions = array(
			"{custom_position_top}" 	=> '',
			"{custom_position_middle}" 	=> '',
			"{custom_position_bottom}" 	=> '',
			"{custom_position_joomla3810ter}" 	=> '',
		);
		
		// Iterate the records to attach the mail contents.
		// It is required to replace the placeholders after the iteration
		// because 2 ore more records may share the same position.
		foreach ($rows as $r)
		{
			/**
			 * Render HTML description to interpret attached plugins.
			 * 
			 * @since 1.6.3
			 */
			$r['content'] = static::renderHtmlDescription($r['content'], 'custmail');

			// append the content to the existing position
			$positions[$r['position']] .= $r['content'];
		}
		
		// replace any existing placeholder
		foreach ($positions as $k => $v)
		{
			$tmpl = str_replace($k, $v, $tmpl);
		}
		
		return $tmpl;	
	}
	
	/**
	 * Includes the files uploaded by the customers as attachment.
	 * See the custom fields of type "file".
	 *
	 * @param 	array 	$order_details 	The orders list.
	 *
	 * @return 	array 	The attachments array.
	 */
	public static function includeMailAttachments($order_details)
	{
		$attachments = array();

		foreach ($order_details[0]['uploads'] as $key => $name)
		{
			if (strlen($name))
			{
				$original 	= VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . $name;
				$rename 	= VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . strtolower($key . substr($name, strrpos($name, '.')));
				
				if (copy($original, $rename))
				{
					$attachments[] = $rename;
				}
			}
		}
		
		return $attachments;
	}
	
	/**
	 * Destroys the attachments that have been sent to the administrator.
	 * See the custom fields of type "file".
	 *
	 * @param 	array 	$attachments 	The list of attachments to remove.
	 *
	 * @return 	void
	 */
	public static function destroyMailAttachments(array $attachments)
	{
		foreach ($attachments as $file)
		{
			unlink($file);
		}
	}
	
	/**
	 * Creates the ICS file to attach within the e-mail.
	 *
	 * @param 	integer  $id_order	The order to export.
	 * @param 	boolean  $is_admin 	True if the e-mail is sent to an administrator.
	 * @param 	integer  $id_emp 	The ID of the employee.
	 *
	 * @return 	mixed 	 The path of the ICS file on success, otherwise null.
	 *
	 * @uses 	composeExportableFile()
	 */
	public static function composeFileICS($id_order, $is_admin = false, $id_emp = -1)
	{
		return self::composeExportableFile('ics', $id_order, $is_admin, $id_emp);
	}
	
	/**
	 * Creates the CSV file to attach within the e-mail.
	 *
	 * @param 	integer  $id_order	The order to export.
	 * @param 	boolean  $is_admin 	True if the e-mail is sent to an administrator.
	 * @param 	integer  $id_emp 	The ID of the employee.
	 *
	 * @return 	mixed 	 The path of the CSV file on success, otherwise null.
	 *
	 * @uses 	composeExportableFile()
	 */
	public static function composeFileCSV($id_order, $is_admin = false, $id_emp = -1)
	{
		return self::composeExportableFile('csv', $id_order, $is_admin, $id_emp);
	}

	/**
	 * Creates the exportable file to attach within the e-mail.
	 *
	 * @param 	integer  $id_order	The order to export.
	 * @param 	boolean  $is_admin 	True if the e-mail is sent to an administrator.
	 * @param 	integer  $id_emp 	The ID of the employee.
	 *
	 * @return 	mixed 	 The path of the exported file on success, otherwise null.
	 */
	protected static function composeExportableFile($class, $id_order, $is_admin = false, $id_emp = -1)
	{
		$filename = VAPHELPERS . DIRECTORY_SEPARATOR . 'mail_attach' . DIRECTORY_SEPARATOR . date('Y-m-d-H-i-s');
		$tmp 	  = $filename;

		$cont = 1;

		while (file_exists($tmp . '.' . $class))
		{
			$tmp = $filename . '-' . $cont;
			$cont++;
		}

		$filename = $tmp . '.' . $class;
		
		$driver = VAPADMIN . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $class . '.php';

		if (!file_exists($driver))
		{
			return null;
		}

		require_once $driver;

		$classname = 'VikExporter' . strtoupper($class);
		
		$exp = new $classname(0, 0, 0);

		$exp->forceOrderID($id_order);
		$exp->setAutoDownload(false);
		$exp->setAdminInterface($is_admin);
		$exp->setEmployee($id_emp);
		
		$str = $exp->getString();

		$exp->export($str, $filename);

		return $filename;
	}

	// CANCELLATION E-MAIL

	/**
	 * Sends the notification e-mail to the administrator(s)
	 * and related employee(s).
	 *
	 * @param 	array 	$order_details 	The orders that should be notified.
	 *
	 * @return 	void
	 */
	public static function sendCancellationAdminEmail($order_details)
	{
		if (!$order_details)
		{
			return;
		}
		
		self::loadLanguage(self::getDefaultLanguage('site'));
		
		$subject = JText::_('VAPORDERCANCELEDSUBJECT');
		
		$admin_mail_list 	= self::getAdminMailList();
		$sendermail 		= self::getSenderMail();
		$adminname 			= self::getAgencyName();
		
		$canc_tmpl 	 = self::loadCancellationEmailTemplate($order_details, 1);
		$html_mess 	 = self::parseCancellationEmailTemplate($canc_tmpl, $order_details, 1);
		$emp_details = self::filterOrdersByEmployee($order_details);
			
		$vik = UIApplication::getInstance();

		foreach ($admin_mail_list as $_m)
		{
			$vik->sendMail($_m, $_m, $_m, $_m, $subject, $html_mess, array(), true);
		}
		
		foreach ($emp_details as $emp_mail => $emp_order_details)
		{
			/**
			 * Reload cancellation e-mail template using the orders related to the specified employee.
			 *
			 * @since 1.6
			 */
			$canc_tmpl 	= self::loadCancellationEmailTemplate($emp_order_details, 2);
			$_html 		= self::parseCancellationEmailTemplate($canc_tmpl, $emp_order_details, 2);
			
			$vik->sendMail($sendermail, $adminname, $emp_mail, $admin_mail_list[0], $subject, $_html, array(), true);
		}
	}

	/**
	 * Loads the cancellation e-mail template that should be parsed.
	 *
	 * @param 	array 	 $orders 	The orders list.
	 * @param 	integer  $type 		The entity type to render the template (1 for administrator, 2 for employee).
	 *
	 * @return 	string 	 The e-mail template.
	 */
	public static function loadCancellationEmailTemplate(array $orders, $type)
	{
		/**
		 * @deprecated 1.8 	Use _JEXEC instead
		 */
		defined('_VIKAPPOINTMENTSEXEC') or define('_VIKAPPOINTMENTSEXEC', '1');

		ob_start();
		include VAPHELPERS . DIRECTORY_SEPARATOR . 'mail_tmpls' . DIRECTORY_SEPARATOR . self::getCancellationMailTemplateName();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
	
	/**
	 * Method used to parse the e-mail template for the administrator(s).
	 *
	 * @param 	string 	 $tmpl 			 The template string to parse.
	 * @param 	array 	 $order_details  The orders list.
	 * @param 	integer  $type 			 The entity type to render the template (1 for administrator, 2 for employee).
	 *
	 * @return 	string 	 The parsed template.
	 */
	public static function parseCancellationEmailTemplate($tmpl, $order_details, $type)
	{
		$vik = UIApplication::getInstance();

		// retrieve cancellation content

		if ($type == 1)
		{
			$cancellation_content = JText::_('VAPORDERCANCELEDCONTENT');
		}
		else
		{
			$cancellation_content = JText::_('VAPORDERCANCELEDCONTENTEMP');
		}

		// fetch appointment details

		/**
		 * @deprecated 1.8 	the appointment details are parsed within the e-mail template
		 */
		$appointment_details = "";
		for ($i = ($order_details[0]['id_service'] == -1 ? 1 : 0); $i < count($order_details); $i++)
		{
			$row = $order_details[$i];

			if ($type == 1)
			{
				/**
				 * Route administrator URL depending on the current platform.
				 *
				 * @since 1.6.3
				 */
				$url = $vik->adminUrl('index.php?option=com_vikappointments&task=editreservation&cid[]=' . $row['id']);
			}
			else
			{
				$url = 'index.php?option=com_vikappointments&view=empmanres&cid[]=' . $row['id'];
				$url = $vik->routeForExternalUse($url);
			}
			
			$appointment_details .= '<div class="appointment">';

			$appointment_details .= '<div class="content">';
			$appointment_details .= '<div class="left">' . $row['id'] . ' - ' . $row['sid'] . '</div>';
			$appointment_details .= '<div class="right">' . JText::_('VAPSTATUSCANCELED') . '</div>';
			$appointment_details .= '</div>';

			$appointment_details .= '<div class="subcontent">';
			$appointment_details .= $row['sname'] . ($type == 1 ? ' - '.$row['ename'] : '') . '<br />';
			$appointment_details .= $row['formatted_checkin'] . ' - ' . $row['formatted_duration'];
			$appointment_details .= '</div>';

			$appointment_details .= '<div class="link"><a href="' . $url . '">' . $url . '</a></div>';

			$appointment_details .= '</div>';
		}

		// customer details
		
		$custom_fields = json_decode($order_details[0]['custom_f'], true);

		/**
		 * @deprecated 1.8 	the customer details are parsed within the e-mail template
		 */
		$customer_details = "";
		foreach ($custom_fields as $kc => $vc)
		{
			$customer_details .= '<div class="info">';
			$customer_details .= '<div class="label">'.JText::_($kc).':</div>';
			$customer_details .= '<div class="value">'.$vc.'</div>';
			$customer_details .= '</div>';
		}

		// order link

		if ($type == 1)
		{
			/**
			 * Route administrator URL depending on the current platform.
			 *
			 * @since 1.6.3
			 */
			$order_link = $vik->adminUrl('index.php?option=com_vikappointments&task=reservations&res_id=' . $order_details[0]['id']);
		}
		else
		{
			$order_link = '';
		}

		// logo

		$logo_name = self::getCompanyLogoPath();
		
		$logo_str = "";
		if (!empty($logo_name) && file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $logo_name))
		{
			$logo_str = '<img src="' . VAPMEDIA_URI . $logo_name . '" />';
		}

		// replace tags from template

		$tmpl = str_replace('{company_name}'			, self::getAgencyName()	, $tmpl);
		$tmpl = str_replace('{cancellation_content}'	, $cancellation_content	, $tmpl);
		$tmpl = str_replace('{logo}'					, $logo_str				, $tmpl);
		$tmpl = str_replace('{order_link}'				, $order_link 			, $tmpl);

		/**
		 * @deprecated 1.8
		 */
		$tmpl = str_replace('{appointment_details}'		, $appointment_details	, $tmpl);
		$tmpl = str_replace('{customer_details}'		, $customer_details		, $tmpl);

		return $tmpl;
	}

	// WAITING LIST NOTIFICATIONS

	/**
	 * Checks all the customers subscribed to the waiting list that should
	 * be notified after a cancellation of a confirmed appointment.
	 *
	 * @param 	array 	$order_details 	The details of the order that is no more confirmed.
	 *
	 * @return  void
	 */
	public static function notifyCustomersInWaitingList($order_details)
	{
		if (!self::isWaitingList() || !$order_details)
		{
			return false;
		}

		$dbo = JFactory::getDbo();

		$services = array();

		// fetch common services
		for ($i = ($order_details[0]['id_service'] == -1 ? 1 : 0); $i < count($order_details); $i++)
		{
			$sid = $order_details[$i]['id_service'];
			$eid = $order_details[$i]['id_employee'];

			$arr_date = getdate($order_details[$i]['checkin_ts']);

			$checkin_day = mktime(0, 0, 0, $arr_date['mon'], $arr_date['mday'], $arr_date['year']);
			$checkin_time = $arr_date['hours'] * 60 + $arr_date['minutes'];

			if (empty($services[$sid]))
			{
				$services[$sid] = array();
			}

			if (empty($services[$sid][$eid]))
			{
				$services[$sid][$eid] = array();
			}

			if (empty($services[$sid][$eid][$checkin_day]))
			{
				$services[$sid][$eid][$checkin_day] = array();
			}

			$services[$sid][$eid][$checkin_day][] = $checkin_time;
		}

		/**
		 * Here we should have a tree structure built as follows:
		 *
		 * - ID service (20)
		 * 		- ID employee (2)
		 *			- checkin day (unix timestamp)
		 * 				- time (10:30)
		 * 				- time (11:30)
		 * 			- checkin day (unix timestamp)
		 * 				- time (16:00)
		 */

		// get compatible customers in waiting list
		foreach ($services as $id_service => $employees)
		{
			foreach ($employees as $id_employee => $timestamps)
			{
				foreach ($timestamps as $ts => $times)
				{
					$q = $dbo->getQuery(true);

					$q->select('*');
					$q->from($dbo->qn('#__vikappointments_waitinglist'));

					// Check if the employee is set and matches the specified one.
					// In this case, the slot has been emptied even if the order was referring to a different service.
					$q->where(array(
						$dbo->qn('id_employee') . ' = ' . (int) $id_employee,
						$dbo->qn('id_employee') . ' > 0',
					));

					// Otherwise make sure the ID of the service matches the specified one.
					// In addition, the employee must be not set or equals to the specified one.
					$q->orWhere(array(
						$dbo->qn('id_service') . ' = ' . (int) $id_service,
						'(' . $dbo->qn('id_employee') . ' = ' . (int) $id_employee . ' OR ' . $dbo->qn('id_employee') . ' <= 0)',
					), 'AND');

					// extend the previous statement and make sure the subscription is for the specified day
					$q->andWhere($dbo->qn('timestamp') . ' = ' . $ts);

					// $q = "SELECT `w`.* 
					// FROM `#__vikappointments_waitinglist` AS `w` 
					// LEFT JOIN `#__vikappointments_service` AS `s` ON `w`.`id_service`=`s`.`id`
					// WHERE `w`.`id_service`=$id_service AND (`w`.`id_employee`=$id_employee OR `s`.`choose_emp`=0) AND `w`.`timestamp`=$ts;";

					$dbo->setQuery($q);
					$dbo->execute();

					if ($dbo->getNumRows())
					{
						$waiting_list = $dbo->loadAssocList();

						self::sendWaitingListNotifications($order_details, $id_service, $id_employee, $ts, $times, $waiting_list);
					}
				}
			}
		}
	}

	/**
	 * Helper method used to send the notifications to the subscribed customers.
	 * The notifications are send via e-email and SMS, depending on how they are configured.
	 *
	 * @param 	array 	 $order_details  The order details array.
	 * @param 	integer  $id_service 	 The ID of the service.
	 * @param 	integer  $id_employee 	 The ID of the employee (if set).
	 * @param 	integer  $checkin_ts 	 The checkin day of the appointment.
	 * @param 	array 	 $times 		 All the times that have been emptied for the specified day.
	 * @param 	array 	 $waiting_list 	 A list containing all the subscriptions that match the parameters.
	 *
	 * @return 	void
	 */
	private static function sendWaitingListNotifications($order_details, $id_service, $id_employee, $checkin_ts, $times, $waiting_list)
	{
		$i = 0;
		
		// iterate the order details until we find the appointment that matches the given parameters
		for ($i; $i < count($order_details) && ($order_details[$i]['id_service'] != $id_service || $order_details[$i]['id_employee'] != $id_employee); $i++);

		self::sendWaitingListNotificationMail($order_details[$i], $checkin_ts, $times, $waiting_list);
		self::sendWaitingListNotificationSMS($order_details[$i], $checkin_ts, $times, $waiting_list);
	}

	/**
	 * Loads the e-mail template that will be used to notify
	 * the waiting list subscriptions.
	 *
	 * @return 	string 	The HTML contents of the template.
	 */
	public static function loadWaitListEmailTemplate()
	{
		defined('_VIKAPPOINTMENTSEXEC') or define('_VIKAPPOINTMENTSEXEC', '1');
		
		ob_start();
		include VAPBASE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "mail_tmpls" . DIRECTORY_SEPARATOR . self::getWaitListMailTemplateName();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Notifies all the records in the waiting list via e-mail.
	 *
	 * @param 	array 	 $order_details  The order details array.
	 * @param 	integer  $checkin_ts 	 The checkin day of the appointment.
	 * @param 	array 	 $times 		 All the times that have been emptied for the specified day.
	 * @param 	array 	 $waiting_list 	 A list containing all the subscriptions that match the parameters.
	 *
	 * @return 	void
	 */
	private static function sendWaitingListNotificationMail($order_details, $checkin_ts, $times, $waiting_list)
	{
		if (!$order_details)
		{
			return;
		}

		$vik = UIApplication::getInstance();
		
		self::loadLanguage($order_details['langtag']);
		
		$admin_mail_list = self::getAdminMailList();
		$sendermail 	 = self::getSenderMail();
		$fromname 		 = self::getAgencyName();
		
		$subject = JText::sprintf('VAPWAITLISTEMAILSUBJECT', $fromname);
		
		$tmpl = self::loadWaitListEmailTemplate();
		// $_html_content = self::parseWaitListEmailTemplate($tmpl, $order_details, $checkin_ts, $times[0]);

		foreach ($waiting_list as $c)
		{
			if (strlen($c['email']))
			{
				/**
				 * For further details:
				 * @see fixOrderForSubscriptionWL()
				 */
				self::fixOrderForSubscriptionWL($order_details, $c);

				// replace placeholders
				$_html_content = self::parseWaitListEmailTemplate($tmpl, $order_details, $checkin_ts, $times[0]);
				// send e-mail to customer
				$vik->sendMail($sendermail, $fromname, $c['email'], $admin_mail_list[0], $subject, $_html_content, null, true);
			}
		}
	}

	/**
	 * Parses the e-mail template used for the notifications of the waiting list.
	 * The placeholders contained in the template will be replaced with real values.
	 *
	 * @param  	string 	 $tmpl 			 The default HTML template to parse.
	 * @param 	array 	 $order_details  The order details array.
	 * @param 	integer  $checkin_ts 	 The checkin day of the appointment.
	 * @param 	integer	 $time 		 	 The first time available.
	 *
	 * @return 	string 	 The e-mail message to send in HTML format.
	 */
	public static function parseWaitListEmailTemplate($tmpl, $order_details, $checkin_ts, $time)
	{
		$vik = UIApplication::getInstance();

		// get settings

		$date_format = self::getDateFormat();
		$time_format = self::getTimeFormat();

		$checkin_time = mktime(floor($time / 60), $time % 60, 0, 1, 1, 1970);

		$formatted_date = date($date_format, $checkin_ts);
		$formatted_time = date($time_format, $checkin_time);

		// set the link to access the service details page
		$path = "index.php?option=com_vikappointments&view=servicesearch&id_ser={$order_details['id_service']}&date={$formatted_date}";

		if ($order_details['id_employee'] > 0)
		{
			$path .= "&id_emp={$order_details['id_employee']}";
		}

		$details_link_href = $vik->routeForExternalUse($path);

		// unsubscribe link

		$unsubscribe_link_href = $vik->routeForExternalUse('index.php?option=com_vikappointments&view=unsubscr_waiting_list');

		// logo

		$logo_name = self::getCompanyLogoPath();
		
		$logo_str = "";

		if (!empty($logo_name) && file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $logo_name))
		{ 
			$logo_str = '<img src="' . VAPMEDIA_URI . $logo_name . '" />';
		}

		// replace tags from template

		$tmpl = str_replace('{company_name}'	, self::getAgencyName()		, $tmpl);
		$tmpl = str_replace('{service}'			, $order_details['sname']	, $tmpl);
		$tmpl = str_replace('{checkin_day}'		, $formatted_date			, $tmpl);
		$tmpl = str_replace('{checkin_time}'	, $formatted_time			, $tmpl);
		$tmpl = str_replace('{details_link}'	, $details_link_href		, $tmpl);
		$tmpl = str_replace('{unsubscribe_link}', $unsubscribe_link_href	, $tmpl);
		$tmpl = str_replace('{logo}'			, $logo_str					, $tmpl);

		return $tmpl;
	}

	/**
	 * Notifies all the records in the waiting list via SMS.
	 *
	 * @param 	array 	 $order_details  The order details array.
	 * @param 	integer  $checkin_ts 	 The checkin day of the appointment.
	 * @param 	array 	 $times 		 All the times that have been emptied for the specified day.
	 * @param 	array 	 $waiting_list 	 A list containing all the subscriptions that match the parameters.
	 *
	 * @return 	void
	 */
	private static function sendWaitingListNotificationSMS($order_details, $checkin_ts, $times, $waiting_list)
	{
		$sms_api_name = self::getSmsApi();
		$sms_api_path = VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $sms_api_name;
		
		if (!strlen($sms_api_name) || !file_exists($sms_api_path))
		{
			// SMS API not configured
			return false;
		}

		require_once $sms_api_path;

		$sms_api_params = self::getSmsApiFields();
			
		$sms_api = new VikSmsApi($order_details, $sms_api_params);
		
		$response_obj = null;
		
		foreach ($waiting_list as $c)
		{
			if (!empty($c['phone_number']))
			{
				/**
				 * For further details:
				 * @see fixOrderForSubscriptionWL()
				 */
				self::fixOrderForSubscriptionWL($order_details, $c);

				$sms_message = self::parseWaitingListSMS($order_details, $checkin_ts, $times);

				if (strlen($sms_message))
				{
					$response_obj = $sms_api->sendMessage($c['phone_prefix'] . $c['phone_number'], $sms_message);
				
					if (!$sms_api->validateResponse($response_obj))
					{
						self::sendAdminMailSmsFailed($sms_api->getLog());
					}
				}
			}
		}
	}

	/**
	 * Helper method used to adjust the order details depending on the
	 * specified subscription. This is needed because the appointment 
	 * may be cancelled for the same employee but for a different service.
	 * In this case, we need to adjust the details related to the service.
	 *
	 * @param 	array 	&$order 	The order details.
	 * @param 	array 	$subscr 	The waiting list subscription.
	 *
	 * @return 	void
	 */
	private static function fixOrderForSubscriptionWL(&$order, $subscr)
	{
		$dbo = JFactory::getDbo();

		if ($order['id_service'] != $subscr['id_service'])
		{
			// inject the real service to which the customers is subscribed
			$order['id_service'] = $subscr['id_service'];

			// recalculate also the name of the service
			$q = $dbo->getQuery(true)
				->select($dbo->qn('name'))
				->from($dbo->qn('#__vikappointments_service'))
				->where($dbo->qn('id') . ' = ' . $subscr['id_service']);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$order['sname'] = $dbo->loadResult();

				// get translated name
				$translations = self::getTranslatedServices($subscr['id_service'], $order['langtag'], $dbo);
				// try to translate the name of the service
				$order['sname'] = self::getTranslation($subscr['id_service'], $order, $translations, 'sname', 'name');
			}
		}
	}

	/**
	 * Parses the e-mail template used for the notifications of the waiting list.
	 * The placeholders contained in the template will be replaced with real values.
	 *
	 * The SMS template message to use depends on the language and the number
	 * of times that are available again (1 or more).
	 *
	 * @param 	array 	 $order_details  The order details array.
	 * @param 	integer  $checkin_ts 	 The checkin day of the appointment.
	 * @param 	array 	 $times 		 All the times that have been emptied for the given day.
	 *
	 * @return 	string 	 The SMS plain message to send.
	 */
	private static function parseWaitingListSMS($order_details, $checkin_ts, $times)
	{
		$vik = UIApplication::getInstance();

		$sms_map = self::getWaitingListContentSMS();

		$def_lang = JFactory::getLanguage()->getTag();
		$sms = $sms_map[(count($times) == 1 ? 0 : 1)][$def_lang];

		$default_tz = date_default_timezone_get();
		if (!empty($order_details['ord_timezone']))
		{
			self::setCurrentTimezone($order_details['ord_timezone']);
		}

		$checkin_time = mktime(floor($times[0] / 60), $times[0] % 60, 0, 1, 1, 1970);

		$date_format = self::getDateFormat();
		$time_format = self::getTimeFormat();
		$agency_name = self::getAgencyName();

		$formatted_date = date($date_format, $checkin_ts);
		$formatted_time = date($time_format, $checkin_time);

		// set the link to access the service details page
		$path = "index.php?option=com_vikappointments&view=servicesearch&id_ser={$order_details['id_service']}&date={$formatted_date}";

		if ($order_details['id_employee'] > 0)
		{
			$path .= "&id_emp={$order_details['id_employee']}";
		}

		$url = $vik->routeForExternalUse($path, false);

		$sms = str_replace('{checkin_day}'	, $formatted_date			, $sms);
		$sms = str_replace('{checkin_time}'	, $formatted_time			, $sms);
		$sms = str_replace('{service}'		, $order_details['sname']	, $sms);
		$sms = str_replace('{company}'		, $agency_name				, $sms);
		$sms = str_replace('{details_url}'	, $url 						, $sms);
		
		self::setCurrentTimezone($default_tz);

		return $sms;
	}

	/**
	 * Removes from the waiting list the subscription that has been notified.
	 * Usually, when a customer receives the notification, it proceeds with the
	 * purchase of the appointment. At the end of this process, its subscription
	 * is automatically removed using this method.
	 *
	 * In addition, removes all the waiting list subscriptions 
	 * that are older than the current day.
	 *
	 * @param 	array 	$order_details  The order details array.
	 *
	 * @return 	void
	 */
	public static function flushWaitingList($order_details)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->delete($dbo->qn('#__vikappointments_waitinglist'))
			->where($dbo->qn('timestamp') . ' + 86400 < ' . time());

		$dbo->setQuery($q);
		$dbo->execute();

		foreach ($order_details as $o)
		{
			$date = getdate($o['checkin_ts']);
			$date = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);

			$q = $dbo->getQuery(true)
				->delete($dbo->qn('#__vikappointments_waitinglist'))
				->where($dbo->qn('id_service') . ' = ' . (int) $o['id_service'])
				->where($dbo->qn('timestamp') . ' = ' . (int) $date)
				->andWhere(array(
					$dbo->qn('id_employee') . ' = ' . (int) $o['id_employee'],
					$dbo->qn('id_employee') . ' = -1',
				))
				->andWhere(array(
					$dbo->qn('email') . ' = ' . $dbo->q($o['purchaser_mail']),
					$dbo->qn('phone_number') . ' = ' . $dbo->q($o['purchaser_phone']),
				));

			$dbo->setQuery($q);
			$dbo->execute();    		
		}
	}

	// PACKAGES NOTIFICATIONS

	/**
	 * Returns the packages order details of the given ID.
	 *
	 * @param 	integer  $order_id 	 The order number (ID).
	 * @param 	string 	 $order_key  The order key (sid). If not provided, 
	 * 								 this field won't used while fetching the records.
	 * @param 	string 	 $langtag 	 The translation language. If not provided
	 * 								 it will be used the default one.
	 * 
	 * @return 	mixed 	 A list of orders on success, otherwise false.
	 */
	public static function fetchPackagesOrderDetails($order_id, $order_key = null, $langtag = null)
	{
		$dbo = JFactory::getDbo();

		$order_id = (int) $order_id;

		$q = $dbo->getQuery(true);

		// order
		$q->select('`o`.*');
		$q->from($dbo->qn('#__vikappointments_package_order', 'o'));

		// payment
		$q->select(array(
			$dbo->qn('gp.name', 'payment_name'),
			$dbo->qn('gp.note', 'payment_note'),
			$dbo->qn('gp.prenote', 'payment_prenote'),
			$dbo->qn('gp.charge', 'payment_charge'),
		));
		$q->leftjoin($dbo->qn('#__vikappointments_gpayments', 'gp') . ' ON ' . $dbo->qn('gp.id') . ' = ' . $dbo->qn('o.id_payment'));

		// order item
		$q->select(array(
			$dbo->qn('i.id_package'),
			$dbo->qn('i.price', 'item_price'),
			$dbo->qn('i.quantity', 'item_quantity'),
			$dbo->qn('i.num_app', 'item_num_app'),
			$dbo->qn('i.used_app', 'item_used_app'),
			$dbo->qn('i.modifiedon', 'item_modifiedon'),
		));
		$q->leftjoin($dbo->qn('#__vikappointments_package_order_item', 'i') . ' ON ' . $dbo->qn('i.id_order') . ' = ' . $dbo->qn('o.id'));

		// item
		$q->select(array(
			$dbo->qn('p.id_group'),
			$dbo->qn('p.name', 'package_name'),
		));
		$q->leftjoin($dbo->qn('#__vikappointments_package', 'p') . ' ON ' . $dbo->qn('i.id_package') . ' = ' . $dbo->qn('p.id'));

		// item group
		$q->select($dbo->qn('g.title', 'group_title'));
		$q->leftjoin($dbo->qn('#__vikappointments_package_group', 'g') . ' ON ' . $dbo->qn('p.id_group') . ' = ' . $dbo->qn('g.id'));

		// joomla user
		$q->select(array(
			$dbo->qn('ju.id', 'jid'),
			$dbo->qn('ju.name', 'user_name'),
			$dbo->qn('ju.username', 'user_uname'),
			$dbo->qn('ju.email', 'user_email'),
		));
		$q->leftjoin($dbo->qn('#__vikappointments_users', 'u') . ' ON ' . $dbo->qn('u.id') . ' = ' . $dbo->qn('o.id_user'));
		$q->leftjoin($dbo->qn('#__users', 'ju') . ' ON ' . $dbo->qn('ju.id') . ' = ' . $dbo->qn('u.jid'));

		$q->where($dbo->qn('o.id') . ' = ' . $order_id);

		if (!is_null($order_key))
		{
			$q->andWhere($dbo->qn('o.sid') . ' = ' . $dbo->q($order_key));
		}

		$q->order(array(
			$dbo->qn('g.ordering') . ' ASC',
			$dbo->qn('p.ordering') . ' ASC',
		));
		
		$dbo->setQuery($q);
		$dbo->execute();
		
		if (!$dbo->getNumRows())
		{
			return false;
		}

		$rows = $dbo->loadAssocList();
		
		$assoc = $rows[0];
		$assoc['items'] = array();

		if ($assoc['id_package'])
		{
			foreach ($rows as $r)
			{
				$assoc['items'][] = array(
					'id' 			=> $r['id_package'],
					'name' 			=> $r['package_name'],
					'price' 		=> $r['item_price'],
					'quantity' 		=> $r['item_quantity'],
					'num_app' 		=> $r['item_num_app'],
					'used_app' 		=> $r['item_used_app'],
					'modifiedon' 	=> $r['item_modifiedon'],
					'id_group' 		=> $r['id_group'],
					'group_title' 	=> $r['group_title'],
				);
			}
		}

		// translations
		
		if (empty($langtag))
		{
			$langtag = $assoc['langtag'];

			if (empty($langtag))
			{
				$langtag = JFactory::getLanguage()->getTag();
			}
		}
		else
		{
			$assoc['langtag'] = $langtag;
		}
		
		$lang_packgroups 	= self::getTranslatedPackGroups('', $langtag, $dbo);
		$lang_packages 		= self::getTranslatedPackages('', $langtag, $dbo);
		$lang_payments 		= self::getTranslatedPayments($assoc['id_payment'], $langtag, $dbo);

		$assoc['payment_name'] 		= self::getTranslation($assoc['id_payment'], $assoc, $lang_payments, 'payment_name', 'name');
		$assoc['payment_prenote'] 	= self::getTranslation($assoc['id_payment'], $assoc, $lang_payments, 'payment_prenote', 'prenote');
		$assoc['payment_note'] 		= self::getTranslation($assoc['id_payment'], $assoc, $lang_payments, 'payment_note', 'note');
		
		for ($i = 0; $i < count($assoc['items']); $i++)
		{
			$a = &$assoc['items'][$i];
			
			$a['name'] 			= self::getTranslation($a['id'], $a, $lang_packages, 'name', 'name');
			$a['group_title'] 	= self::getTranslation($a['id_group'], $a, $lang_packgroups, 'group_title', 'title');
		}

		// CUSTOM FIELDS TRANSLATION

		// get the fields for the customers
		$fields = VAPCustomFields::getList(0);
		// translate the fields
		VAPCustomFields::translate($fields, $langtag);

		// decode stored CF data
		$data = (array) json_decode($assoc['custom_f'], true);
		
		// translate CF data object
		$data = VAPCustomFields::translateObject($data, $fields);

		// update order info
		$assoc['custom_f'] = json_encode($data);
		
		return $assoc;
	}

	// PACKAGES E-MAIL

	/**
	 * Loads the packages e-mail template that should be parsed.
	 *
	 * @param 	array 	$order 	The order details.
	 *
	 * @return 	string 	The e-mail template.
	 */
	public static function loadPackagesEmailTemplate(array $order)
	{
		/**
		 * @deprecated 1.8 	Use _JEXEC instead
		 */
		defined('_VIKAPPOINTMENTSEXEC') or define('_VIKAPPOINTMENTSEXEC', '1');

		ob_start();
		include VAPHELPERS . DIRECTORY_SEPARATOR . 'mail_tmpls' . DIRECTORY_SEPARATOR . self::getPackagesMailTemplateName();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Sends the notification e-mail to the customer for the packages.
	 *
	 * @param 	array 	$order_details 	The orders that should be notified.
	 *
	 * @return 	void
	 */
	public static function sendPackagesCustomerEmail($order_details)
	{	
		if (!$order_details)
		{
			return;
		}
		
		self::loadLanguage($order_details['langtag']);
		
		$admin_mail_list 	= self::getAdminMailList();
		$sendermail 		= self::getSenderMail();
		$fromname 			= self::getAgencyName();
		
		$subject = JText::sprintf('VAPPACKAGESEMAILSUBJECT', $fromname);
		
		$tmpl = self::loadPackagesEmailTemplate($order_details);
		$html = self::parsePackagesEmailTemplate($tmpl, $order_details);
		
		$vik = UIApplication::getInstance();
		$vik->sendMail($sendermail, $fromname, $order_details['purchaser_mail'], $admin_mail_list[0], $subject, $html, null, true);
	}

	/**
	 * Sends the notification e-mail to the administrator(s) for the packages.
	 *
	 * @param 	array 	$order_details 	The orders that should be notified.
	 *
	 * @return 	void
	 */
	public static function sendPackagesAdminEmail($order_details)
	{	
		if (!$order_details)
		{
			return;
		}
		
		self::loadLanguage($order_details['langtag']);
		
		$admin_mail_list 	= self::getAdminMailList();
		$sendermail 		= self::getSenderMail();
		$fromname 			= self::getAgencyName();
		
		$subject = JText::sprintf('VAPPACKAGESADMINEMAILSUBJECT', $fromname);
		
		$tmpl = self::loadPackagesEmailTemplate($order_details);
		$html = self::parsePackagesEmailTemplate($tmpl, $order_details);
		
		$vik = UIApplication::getInstance();

		foreach ($admin_mail_list as $_m)
		{
			$vik->sendMail($_m, $_m, $_m, $_m, $subject, $html, null, true);
		}	 
	}

	/**
	 * Method used to parse the e-mail template for packages (admin and customers).
	 *
	 * @param 	string 	$tmpl 			The template string to parse.
	 * @param 	array 	$order_details 	The orders list.
	 *
	 * @return 	string 	The parsed template.
	 */
	public static function parsePackagesEmailTemplate($tmpl, $order_details)
	{
		// parse payment name

		$payment_name = "";
		if (!empty($order_details['payment_name']))
		{
			$payment_name = $order_details['payment_name'];
			// $payment_name = '<div class="box'.($order_details['total_cost'] > 0 ? '' : ' large').'">'.$order_details['payment_name'].'</div>';
		}

		// parse total cost

		$total_cost = "";
		if ($order_details['total_cost'] > 0)
		{
			$total_cost = self::printPriceCurrencySymb($order_details['total_cost']);
			// $total_cost = '<div class="box'.(!empty($order_details['payment_name']) ? '' : ' large').'">'.$total_cost.'</div>';
		}

		// fetch package details

		/**
		 * @deprecated 1.8 	the package details are parsed within the e-mail template
		 */
		$packages_details = "";
		foreach ($order_details['items'] as $p)
		{
			$packages_details .= '<div class="package">';
			$packages_details .= '<div class="content '.($p['price'] > 0 ? '' : 'fill-bottom').'">';
			$packages_details .= '<span class="name">'.$p['name'].'</span>';
			$packages_details .= '<span class="numapp">'.JText::sprintf('VAPPACKAGESMAILAPP', $p['num_app']).'</span>';
			$packages_details .= '<span class="quantity">x'.$p['quantity'].'</span>';
			$packages_details .= '</div>';
			
			if ($p['price'] > 0)
			{
				$packages_details .= '<div class="cost"><span>'.self::printPriceCurrencySymb($p['price']*$p['quantity']).'</span></div>';
			}

			$packages_details .= '</div>';
		}

		// customer details
		
		$custom_fields = json_decode($order_details['custom_f'], true);

		/**
		 * @deprecated 1.8 	the customer details are parsed within the e-mail template
		 */
		$customer_details = "";
		foreach ($custom_fields as $kc => $vc)
		{
			$customer_details .= '<div class="info">';
			$customer_details .= '<div class="label">'.JText::_($kc).':</div>';
			$customer_details .= '<div class="value">'.$vc.'</div>';
			$customer_details .= '</div>';
		}

		// joomla user details

		$user_details = '';
		if (strlen($order_details['user_email']))
		{
			/**
			 * @deprecated 1.8 	the joomla user details should be parsed within the e-mail template (if needed)
			 */

			$user_details = '<div class="separator"> </div>
			<div class="customer-details-wrapper">
				<div class="title">'.JText::_('VAPUSERDETAILS').'</div>
					<div class="customer-details">
						<div class="info">
							<div class="label">'.JText::_('VAPREGFULLNAME').':</div>
							<div class="value">'.$order_details['user_name'].'</div>
						</div>
						<div class="info">
							<div class="label">'.JText::_('VAPREGUNAME').':</div>
							<div class="value">'.$order_details['user_uname'].'</div>
						</div>
						<div class="info">
							<div class="label">'.JText::_('VAPREGEMAIL').':</div>
							<div class="value">'.$order_details['user_email'].'</div>
						</div>
					</div>
				</div>';
		}

		// order link

		$order_link_href = "index.php?option=com_vikappointments&view=packagesorder&ordnum={$order_details['id']}&ordkey={$order_details['sid']}";
		$order_link_href = UIApplication::getInstance()->routeForExternalUse($order_link_href);

		// logo

		$logo_name = self::getCompanyLogoPath();
		
		$logo_str = "";
		if (!empty($logo_name) && file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $logo_name))
		{
			$logo_str = '<img src="' . VAPMEDIA_URI . $logo_name . '" />';
		}

		// order status color

		switch ($order_details['status'])
		{
			case 'CONFIRMED':
				$order_status_color = '#006600';
				break;

			case 'PENDING':
				$order_status_color = '#D9A300';
				break;

			case 'REMOVED':
				$order_status_color = '#B20000';
				break;

			case 'CANCELED':
				$order_status_color = '#F01B17';
				break;

			default:
				$order_status_color = 'inherit';
		}

		// replace tags from template

		$tmpl = str_replace('{company_name}'		, self::getAgencyName()								, $tmpl);
		$tmpl = str_replace('{order_number}'		, $order_details['id']								, $tmpl);
		$tmpl = str_replace('{order_key}'			, $order_details['sid']								, $tmpl);
		$tmpl = str_replace('{order_status_class}'	, strtolower($order_details['status'])				, $tmpl);
		$tmpl = str_replace('{order_status}'		, JText::_('VAPSTATUS' . $order_details['status'])	, $tmpl);
		$tmpl = str_replace('{order_status_color}'	, $order_status_color								, $tmpl);
		$tmpl = str_replace('{order_payment}'		, $payment_name										, $tmpl);
		$tmpl = str_replace('{order_payment_notes}'	, $order_details['payment_note']					, $tmpl);
		$tmpl = str_replace('{order_total_cost}'	, $total_cost										, $tmpl);
		$tmpl = str_replace('{order_link}'			, $order_link_href									, $tmpl);
		$tmpl = str_replace('{logo}'				, $logo_str											, $tmpl);

		/**
		 * @deprecated 1.8
		 */
		$tmpl = str_replace('{packages_details}', $packages_details	, $tmpl);
		$tmpl = str_replace('{customer_details}', $customer_details	, $tmpl);
		$tmpl = str_replace('{user_details}'	, $user_details		, $tmpl);

		return $tmpl;
	}

	// SMS
	
	/**
	 * Sends a notification about the purchased order to the specified number.
	 * If allowed, a notificatio will be sent also to the employees and the administrators.
	 *
	 * @param 	string 	 $phone_number 	The phone number of the customer.
	 * @param 	array 	 $order_info 	An associative array with the order details.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 *
	 * @uses 	sendAdminMailSmsFailed()
	 */
	public static function sendSmsAction($phone_number, $order_info)
	{	
		$_str = '';
		
		$sms_api_name = self::getSmsApi();
		$sms_api_path = VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $sms_api_name;
		
		if (!self::getSmsApiEnabled() || !file_exists($sms_api_path) || !strlen($sms_api_name))
		{
			// SMS framework is probably not configured
			return false;
		}
		
		// require SMS gateway
		require_once $sms_api_path;

		$dbo = JFactory::getDbo();

		/**
		 * Fixed an issue that was retrieving the employee ID
		 * from an undefined variable ($order_details).
		 *
		 * @since 1.6.2
		 */
		$q = $dbo->getQuery(true)
			->select($dbo->qn('phone'))
			->from($dbo->qn('#__vikappointments_employee'))
			->where($dbo->qn('id') . ' = ' . (int) $order_info[0]['id_employee']);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$emp_number = $dbo->loadResult();
		}
		else
		{
			$emp_number = '';
		}
		
		// array with the entities to notify
		$sms_api_to = array(
			self::getSmsApiToCustomer(),
			self::getSmsApiToEmployee(),
			self::getSmsApiToAdmin(),
		);

		// array with the phone number of the entities
		$sms_api_to_phone = array(
			$phone_number,
			$emp_number,
			self::getSmsApiAdminPhoneNumber(),
		);

		// SMS messages
		$sms_api_to_text = array(
			self::getSmsCustomerTextMessage($order_info),
			self::getSmsAdminTextMessage($order_info),
		);
		$sms_api_to_text[] = $sms_api_to_text[1];
		
		// get API params
		$sms_api_params = self::getSmsApiFields();
		
		$sms_api = new VikSmsApi($order_info, $sms_api_params);
		
		$response_obj = null;
		$result 	  = true;
		
		for ($i = 0; $i < count($sms_api_to); $i++)
		{
			// check if this entity (customer, employee or admin) should receive a SMS notification
			if ($sms_api_to[$i] && !empty($sms_api_to_phone[$i]))
			{
				// invoke the gateway to send the message
				$response_obj = $sms_api->sendMessage($sms_api_to_phone[$i], $sms_api_to_text[$i]);
				
				if (!$sms_api->validateResponse($response_obj))
				{
					// an error occurred, notify the administrator via mail
					self::sendAdminMailSmsFailed($sms_api->getLog());
					$result = false;
				}
			}
			
		}

		return $result;
	}
	
	/**
	 * Returns the SMS message that should be sent to the customers.
	 *
	 * @param 	array 	$order_info  The array containing the order details.
	 *
	 * @return 	string 	The SMS message to send to the customers.
	 *
	 * @uses 	parseContentSMS()
	 */
	public static function getSmsCustomerTextMessage($order_info)
	{
		// get order language for translation
		$def_lang = empty($order_info[0]['langtag']) ? JFactory::getLanguage()->getTag() : $order_info[0]['langtag'];
		
		$sms_map = array();
		
		if (count($order_info) == 1)
		{
			$sms_map = json_decode(self::getFieldFromConfig('smstmplcust', 'vapGetSMSTmplCust'), true);
		}
		else
		{
			$sms_map = json_decode(self::getFieldFromConfig('smstmplcustmulti', 'vapGetSMSTmplCustMulti'), true);
		}
		
		$sms = "";

		if (!empty($sms_map[$def_lang]))
		{
			// get translated message
			$sms = $sms_map[$def_lang];
		}
		else
		{
			// message not provided, use the default ones
			if (count($order_info) == 1)
			{
				$sms = JText::_('VAPSMSMESSAGECUSTOMER');
			}
			else
			{
				$sms = JText::_('VAPSMSMESSAGECUSTOMERMULTI');
			}
		}
		
		return self::parseContentSMS($order_info, $sms);
	}
	
	/**
	 * Returns the SMS message that should be sent to the administrator.
	 *
	 * @param 	array 	$order_info  The array containing the order details.
	 *
	 * @return 	string 	The SMS message to send to the administrator.
	 *
	 * @uses 	parseContentSMS()
	 */
	public static function getSmsAdminTextMessage($order_info)
	{
		$sms = "";

		if (count($order_info) == 1)
		{
			$sms = self::getFieldFromConfig('smstmpladmin', 'vapGetSMSTmplAdmin');
		}
		else
		{
			$sms = self::getFieldFromConfig('smstmpladminmulti', 'vapGetSMSTmplAdminMulti');
		}
		
		if (empty($sms))
		{
			// SMS template not provided, use the default ones
			if (count($order_info) == 1)
			{
				$sms = JText::_('VAPSMSMESSAGEADMIN');
			}
			else
			{
				$sms = JText::_('VAPSMSMESSAGEADMINMULTI');
			}
		}
		
		return self::parseContentSMS($order_info, $sms);
	}
	
	/**
	 * Parses the SMS template to inject the details of the given order.
	 *
	 * @param 	array 	$order_info  The array containing the order details.
	 * @param 	string 	$sms 		 The SMS template.
	 *
	 * @return 	string 	The SMS message to send.
	 */
	private static function parseContentSMS($order_info, $sms)
	{
		$default_tz = date_default_timezone_get();

		if (!empty($order_info[0]['ord_timezone']))
		{
			// set employee timezone (if any)
			self::setCurrentTimezone($order_info[0]['ord_timezone']);
		}

		$config = UIFactory::getConfig();

		$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');
		
		$sms = str_replace('{total_cost}' , self::printPriceCurrencySymb($order_info[0]['total_cost']) , $sms);
		$sms = str_replace('{checkin}'	  , date($dt_format, $order_info[0]['checkin_ts'])			   , $sms);
		$sms = str_replace('{service}'	  , $order_info[0]['sname']									   , $sms);
		$sms = str_replace('{employee}'	  , $order_info[0]['ename']									   , $sms);
		$sms = str_replace('{company}'	  , self::getAgencyName()									   , $sms);
		$sms = str_replace('{customer}'	  , $order_info[0]['purchaser_nominative']					   , $sms);
		$sms = str_replace('{created_on}' , date($dt_format, $order_info[0]['createdon'])			   , $sms);
		
		// reset default timezone
		self::setCurrentTimezone($default_tz);

		return $sms;
	}
	
	/**
	 * Sends a notification e-mail to the administrator(s) to
	 * inform that a SMS was not sent correctly.
	 *
	 * @param 	string 	$text 	The error message.
	 *
	 * @return 	void
	 */
	public static function sendAdminMailSmsFailed($text)
	{
		$vik = UIApplication::getInstance();
					
		$admin_mail_list = self::getAdminMailList();
		$subject 		 = JText::_('VAPSMSFAILEDSUBJECT');
		
		foreach ($admin_mail_list as $_m)
		{
			$vik->sendMail($_m, $_m, $_m, $_m, $subject, $text, '', true);
		}
	}
	
	// LANGUAGE TRANSLATIONS

	/**
	 * Returns the default language of the specified section.
	 *
	 * @param 	string 	$section 	The section to check (site or administrator).
	 *
	 * @return 	atring 	The default language tag.
	 */
	public static function getDefaultLanguage($section = 'site')
	{
		return JComponentHelper::getParams('com_languages')->get($section);
	}
	
	/**
	 * Method used to force the site language of VikAppointments
	 * according to the specified language tag. If the language is
	 * not specified, the default one will be used.
	 *
	 * @param 	string 	$tag 	The language tag.
	 *
	 * @return 	void
	 */
	public static function loadLanguage($tag = null)
	{
		if (empty($tag) && JFactory::getApplication()->isAdmin())
		{
			$tag = 'en-GB';
		}
		
		JFactory::getLanguage()->load('com_vikappointments', JPATH_SITE, $tag, true);
	}
	
	/**
	 * Returns a list of the installed languages.
	 *
	 * @return 	array 	The languages list.
	 */
	public static function getKnownLanguages()
	{
		$def_lang 		 = self::getDefaultLanguage('site');
		$known_languages = JLanguage::getKnownLanguages();
		$languages 		 = array();

		foreach ($known_languages as $k => $v)
		{
			if ($k == $def_lang)
			{
				// default language, push it as first
				array_unshift($languages, $k);
			}
			else
			{
				array_push($languages, $k);
			}
		}
		
		return $languages;
	}
	
	/**
	 * Returns a list of translated groups.
	 *
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated groups. Each object can be easily accessed by using its PK.
	 *
	 * @uses 	getTranslatedObjects()
	 */
	public static function getTranslatedGroups($id = null, $tag = null, $dbo = null)
	{
		return self::getTranslatedObjects('group', 'id_group', $id, $tag, $dbo);
	}
	
	/**
	 * Returns a list of translated employees groups.
	 *
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated groups. Each object can be easily accessed by using its PK.
	 *
	 * @uses 	getTranslatedObjects()
	 */
	public static function getTranslatedEmployeeGroups($id = null, $tag = null, $dbo = null)
	{
		return self::getTranslatedObjects('empgroup', 'id_empgroup', $id, $tag, $dbo);
	}
	
	/**
	 * Returns a list of translated services.
	 *
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated services. Each object can be easily accessed by using its PK.
	 *
	 * @uses 	getTranslatedObjects()
	 */
	public static function getTranslatedServices($id = null, $tag = null, $dbo = null)
	{
		return self::getTranslatedObjects('service', 'id_service', $id, $tag, $dbo);
	}
	
	/**
	 * Returns a list of translated employees.
	 *
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated employees. Each object can be easily accessed by using its PK.
	 *
	 * @uses 	getTranslatedObjects()
	 */
	public static function getTranslatedEmployees($id = null, $tag = null, $dbo = null)
	{
		return self::getTranslatedObjects('employee', 'id_employee', $id, $tag, $dbo);
	}
	
	/**
	 * Returns a list of translated options.
	 *
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated options. Each object can be easily accessed by using its PK.
	 *
	 * @uses 	getTranslatedObjects()
	 */
	public static function getTranslatedOptions($id = null, $tag = null, $dbo = null)
	{
		$options = self::getTranslatedObjects('option', 'id_option', $id, $tag, $dbo);

		foreach ($options as $k => $opt)
		{
			// decode variations, which are stored in JSON format
			$options[$k]['vars_json'] = json_decode($opt['vars_json'], true);
		}

		return $options;
	}

	/**
	 * Returns a list of translated packages.
	 *
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated packages. Each object can be easily accessed by using its PK.
	 *
	 * @uses 	getTranslatedObjects()
	 */
	public static function getTranslatedPackages($id = null, $tag = null, $dbo = null)
	{
		return self::getTranslatedObjects('package', 'id_package', $id, $tag, $dbo);
	}

	/**
	 * Returns a list of translated packages groups.
	 *
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated groups. Each object can be easily accessed by using its PK.
	 *
	 * @uses 	getTranslatedObjects()
	 */
	public static function getTranslatedPackGroups($id = null, $tag = null, $dbo = null)
	{
		return self::getTranslatedObjects('package_group', 'id_package_group', $id, $tag, $dbo);
	}

	/**
	 * Returns a list of translated subscriptions.
	 *
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated subscriptions. Each object can be easily accessed by using its PK.
	 *
	 * @uses 	getTranslatedObjects()
	 *
	 * @since 	1.6
	 */
	public static function getTranslatedSubscriptions($id = null, $tag = null, $dbo = null)
	{
		return self::getTranslatedObjects('subscr', 'id_subscr', $id, $tag, $dbo);
	}

	/**
	 * Returns a list of translated payments.
	 *
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated payments. Each object can be easily accessed by using its PK.
	 *
	 * @uses 	getTranslatedObjects()
	 *
	 * @since 	1.6
	 */
	public static function getTranslatedPayments($id = null, $tag = null, $dbo = null)
	{
		return self::getTranslatedObjects('payment', 'id_payment', $id, $tag, $dbo);
	}
	
	/**
	 * Returns a list of translated objects.
	 *
	 * @param 	string 	$object  The table suffix of the objects to get.
	 * @param 	string 	$column  The column name used to match the specified IDs.
	 * @param 	mixed 	$id 	 The ID of the record or a list of IDs. Leave empty to retrieve all the records.
	 * @param 	string 	$tag 	 The language tag. Leave empty to get the default one.
	 * @param 	mixed 	$dbo 	 The database object.
	 *
	 * @return 	array 	The translated objects. Each object can be easily accessed by using its PK.
	 */
	private static function getTranslatedObjects($object, $column, $id = null, $tag = null, $dbo = null)
	{
		if (!self::isMultilanguage())
		{
			return array();
		}
		
		if (!$tag)
		{
			$tag = JFactory::getLanguage()->getTag();
		}

		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		$lim = null;

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_lang_' . $object))
			->where($dbo->qn('tag') . ' = ' . $dbo->q($tag));

		if ($id)
		{
			if (is_array($id))
			{
				$q->where($dbo->qn($column) . ' IN (' . implode(',', array_map('intval', $id)) . ')');
				$lim = count($id);
			}
			else
			{
				$q->where($dbo->qn($column) . ' = ' . (int) $id);
				$lim = 1;
			}
		}
		
		$dbo->setQuery($q, 0, $lim);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			return array();
		}
		
		$list = array();

		foreach ($dbo->loadAssocList() as $r)
		{
			$list[$r[$column]] = $r;
		}
		
		return $list;
	}
	
	/**
	 * Obtains the translated value. If the element is not translated, the default one will be used.
	 *
	 * @param 	integer  $id 		The ID of the record to translate.
	 * @param 	array 	 $original 	The original record (associative array).
	 * @param 	array 	 $transl 	The array containing all the translations.
	 * @param 	string 	 $match1 	The column name of the record to translate.
	 * @param 	string 	 $match2 	The column name of the translation.
	 * @param 	mixed 	 $default 	The default value to return in case it is empty.
	 *
	 * @return 	mixed 	 The translated value.
	 */
	public static function getTranslation($id, $original, $transl, $match1, $match2, $default = '')
	{	
		if (empty($transl[$id][$match2]))
		{
			// the record doesn't own a translation of this column
			if (!empty($original[$match1]))
			{
				// get the original value
				return $original[$match1];
			}
			else
			{
				// get the default value
				return $default;
			}
		}
		
		// get the translated value
		return $transl[$id][$match2];
	}
	
	// EMPLOYEE AREA SETTINGS
	
	/**
	 * Returns the settings of the given employee.
	 *
	 * @param 	integer  $id_employee 	The employee ID.
	 * @param 	string 	 $cache 		True to retrieve the cached settings (if any).
	 *
	 * @return 	array 	 The settings associative array.
	 */
	public static function getEmployeeSettings($id_employee, $cache = true)
	{
		if (empty($id_employee))
		{
			return array();
		}
		
		$session  = JFactory::getSession();
		$settings = $session->get('vap-emp-settings', '', 'employee');
		
		if (!empty($settings) && $cache)
		{
			return $settings;
		}
		
		$dbo = JFactory::getDbo();
		
		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_employee_settings'))
			->where($dbo->qn('id_employee') . ' = ' . (int) $id_employee);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$settings = $dbo->loadAssoc();
		}
		else
		{
			// create settings record for this employee
			$q = $dbo->getQuery(true)
				->insert($dbo->qn('#__vikappointments_employee_settings'))
				->columns($dbo->qn('id_employee'))
				->values((int) $id_employee);
			
			$dbo->setQuery($q);
			$dbo->execute();

			$lid = $dbo->insertid();
			
			// refresh settings
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_employee_settings'))
				->where($dbo->qn('id') . ' = ' . (int) $lid);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$settings = $dbo->loadAssoc();
			}
		}
		
		$session->set('vap-emp-settings', $settings, 'employee');
		
		return $settings;
	}

	/**
	 * Refreshes the employee settings stored within the user state.
	 *
	 * @param 	integer  $id_employee 	The employee ID.
	 *
	 * @return 	void
	 *
	 * @uses 	getEmployeeSettings()
	 */
	public static function refreshEmployeeSettings($id_employee)
	{
		// do not retrieve cached settings in order to refresh them
		self::getEmployeeSettings($id_employee, false);
	}
	
	// PDF
	
	/**
	 * Returns an array containing the invoice arguments.
	 *
	 * @param 	string 	$group 	The invoice group.
	 *
	 * @return 	array 	The invoice arguments.
	 */
	public static function getPdfParams($group = 'appointments')
	{
		UILoader::import('libraries.invoice.factory');

		return VAPInvoiceFactory::getInstance(null, $group)->getParams();
	}
	
	/**
	 * Returns an object containing the invoice properties.
	 *
	 * @param 	string 	$group 	The invoice group.
	 *
	 * @return 	object 	The invoice properties.
	 */
	public static function getPdfConstraints($group = 'appointments')
	{
		UILoader::import('libraries.invoice.factory');

		return VAPInvoiceFactory::getInstance(null, $group)->getConstraints();
	}

	/**
	 * Helper method used to generate the invoices related to the specified
	 * order, which belong to the given group.
	 *
	 * @param 	array 	 $order_details  The order details.
	 * @param 	string 	 $group 		 The invoices group.
	 *
	 * @return 	boolean  True on success, otherwise false.
	 *
	 * @since 	1.6
	 */
	public static function generateInvoice($order, $group = '')
	{
		if (!self::isAutoGenerateInvoice())
		{
			return false;
		}

		if (!$group)
		{
			$group = 'appointments';
		}

		UILoader::import('libraries.invoice.factory');

		// get invoice handler
		$invoice = VAPInvoiceFactory::getInstance($order, $group);
		
		// generate invoice
		$path = $invoice->generate();

		// get invoice params
		$params = $invoice->getParams();

		if ($path && $params['sendinvoice'])
		{
			// auto-send enabled, send the invoice via e-mail to the customer
			$invoice->send($path);
		}

		return $path;
	}

	// EMPLOYEES FILTERING

	/**
	 * Extends the search query using the custom filters.
	 *
	 * @param 	mixed 	 &$q 		The query builder object.
	 * @param 	array 	 $filters 	The associative array of filters.
	 * @param 	string 	 $alias 	The alias used for "employees" DB table.
	 * @param 	mixed 	 $dbo 		The database object.
	 *
	 * @return 	boolean  True if the query has been altered, otherwise false.
	 *
	 * @since 	1.6
	 */
	public static function extendQueryWithCustomFilters(&$q, array $filters = array(), $alias = null, $dbo = null)
	{
		if (!$dbo)
		{
			$dbo = JFactory::getDbo();
		}

		$lookup = array();

		foreach ($filters as $k => $v)
		{
			if ($v && strpos($k, 'field_') === 0)
			{
				$lookup[] = substr($k, 6);
			}
		}

		if (!$lookup)
		{
			// no custom filters
			return false;
		}

		$lookup = array_map(array($dbo, 'q'), $lookup);

		$q2 = $dbo->getQuery(true)
			->select($dbo->qn('formname'))
			->from($dbo->qn('#__vikappointments_custfields'))
			->where(array(
				$dbo->qn('group') . ' = 1',
				$dbo->qn('formname') . ' IN (' . implode(',', $lookup) . ')',
			));

		$dbo->setQuery($q2);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// no custom fields, possible hack attempt
			return false;
		}

		$fields = $dbo->loadColumn();

		foreach ($fields as $field)
		{
			$key = 'field_' . $field;

			$q->where($dbo->qn(($alias ? $alias . '.' : '') . $key) . ' = ' . $dbo->q($filters[$key]));
		}

		return true;
	}

	// FRONT BUILDING

	/**
	 * Prepares the document related to the specified view.
	 * Used also to implement OPEN GRAPH protocol and to include
	 * global meta data.
	 *
	 * @param 	mixed 	$page 	The view object.
	 *
	 * @return 	void
	 */
	public static function prepareContent($page)
	{
		UILoader::import('libraries.view.contents');

		$handler = VAPViewContents::getInstance($page);

		/**
		 * Set the browser page title.
		 *
		 * @since 1.6.1
		 */
		$handler->setPageTitle();

		// show the page heading (if not provided, an empty string will be returned)
		$handler->getPageHeading(true);

		// set the META description of the page
		$handler->setMetaDescription();

		// set the META keywords of the page
		$handler->setMetaKeywords();

		// set the META robots of the page
		$handler->setMetaRobots();

		// create OPEN GRAPH protocol
		$handler->buildOpenGraph();

		// create MICRODATA
		$handler->buildMicrodata();
	}

	// USERS

	/**
	 * Helper method used to check if the current user is logged.
	 *
	 * @param 	mixed 	 $user 	The user object.
	 *
	 * @return 	boolean  True if logged, false otherwise.
	 */
	public static function isUserLogged($user = null)
	{
		if (!$user)
		{
			$user = JFactory::getUser();
		}
		
		return !$user->guest;
	}
	
	/**
	 * Helper method used to check if the provided arguments are correct
	 * in order to register a new Joomla user.
	 *
	 * @param  	array 	 $args 	The arguments to check.
	 *
	 * @return 	boolean  True if correct, false otherwise.
	 */
	public static function checkUserArguments(array $args)
	{
		if (!self::isUserLogged())
		{
			// proceed only in case the user is not logged

			return (
				!empty($args['firstname'])
				&& !empty($args['lastname'])
				&& !empty($args['username'])
				&& !empty($args['password'])
				&& self::validateUserEmail($args['email'])
				&& !strcmp($args['password'], $args['confpassword'])
			);
		}
		
		return false;
	}
	
	/**
	 * Validates the specified e-mail.
	 *
	 * @param 	string 	 $email  The email to check.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 */
	public static function validateUserEmail($email = '')
	{
		$isValid = true;
		$atIndex = strrpos($email, "@");

		if (is_bool($atIndex) && !$atIndex)
		{
			return false;
		}
		
		$domain 	= substr($email, $atIndex +1);
		$local  	= substr($email, 0, $atIndex);
		$localLen 	= strlen($local);
		$domainLen 	= strlen($domain);

		if ($localLen < 1 || $localLen > 64)
		{
			// local part length exceeded or too short
			return false;
		}

		if ($domainLen < 1 || $domainLen > 255)
		{
			// domain part length exceeded or too short
			return false;
		}
			
		if ($local[0] == '.' || $local[$localLen -1] == '.')
		{
			// local part starts or ends with '.'
			return false;
		}
				
		if (preg_match('/\\.\\./', $local))
		{
			// local part has two consecutive dots
			return false;
		}
					
		if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		{
			// character not valid in domain part
			return false;
		}
		
		if (preg_match('/\\.\\./', $domain))
		{
			// domain part has two consecutive dots
			return false;
		} 

		if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local)))
		{
			// character not valid in local part unless local part is quoted
			if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local)))
			{
				return false;
			}
		}

		if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A"))
		{
			// domain not found in DNS
			return false;
		}
		
		return true;
	}

	/**
	 * Registers a new Joomla User with the details
	 * specified in the given $args associative array.
	 *
	 * @param 	array 	 $args 	The user details.
	 * @param 	integer  $type 	The registration type (for employee [1] or for users [2]).
	 *
	 * @return 	mixed 	The user ID on success, false on failure,
	 * 					the string status during the activation.
	 */
	public static function createNewJoomlaUser(array $args, $type = 2)
	{
		$app = JFactory::getApplication();

		// load com_users site language
		JFactory::getLanguage()->load('com_users', JPATH_SITE, JFactory::getLanguage()->getTag(), true);
		
		// load UsersModelRegistration
		JModelLegacy::addIncludePath(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_users' . DIRECTORY_SEPARATOR . 'models');
		$model = JModelLegacy::getInstance('registration', 'UsersModel');

		// adapt data for model
		$args['name'] 		= $args['firstname'] . ' ' . $args['lastname'];
		$args['email1'] 	= $args['email'];
		$args['password1'] 	= $args['password'];
		$args['block'] 		= 0;

		if ($type == self::REGISTER_EMPLOYEE)
		{
			$auth = EmployeeAuth::getInstance();
			$args['groups'] = array($auth->getSignUpUserGroup());
		}

		// register user
		$return = $model->register($args);

		if ($return === false)
		{
			// impossible to save the user
			$app->enqueueMessage($model->getError(), 'error');
		}
		else if ($return === 'adminactivate')
		{
			// user saved: admin activation required
			$app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
		}
		else if ($return === 'useractivate')
		{
			// user saved: self activation required
			$app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
		}
		else
		{
			// user saved: can login immediately
			$app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
		}

		return $return;
	}
	
	const REGISTER_EMPLOYEE = 1;
	const REGISTER_USERS 	= 2;
}
