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

defined('CF_NO_FILTER') or define('CF_NO_FILTER', 0);
defined('CF_EXCLUDE_REQUIRED_CHECKBOX') or define('CF_EXCLUDE_REQUIRED_CHECKBOX', 1);
defined('CF_EXCLUDE_SEPARATOR') or define('CF_EXCLUDE_SEPARATOR', 2);
defined('CF_EXCLUDE_FILE') or define('CF_EXCLUDE_FILE', 4);

/**
 * VikAppointments custom fields class handler.
 *
 * @since  	1.6
 */
abstract class VAPCustomFields
{
	/**
	 * The default country code in case it is not specified.
	 *
	 * @var string
	 */
	public static $defaultCountry = 'US';

	/**
	 * A list containing all the columns that will be used
	 * in the SELECT query.
	 *
	 * @var array
	 */
	public static $listColumns = array(
		'id',
		'name',
		'formname',
		'type',
		'choose',
		'multiple',
		'required',
		'rule',
		'ordering',
		'poplink',
		'id_employee',
		'group',
	);

	/**
	 * Return the list of the custom fields for the specified section.
	 *
	 * @param 	integer  $group  	The section of the program.
	 * @param 	integer  $employee  The employee ID for customers fields.
	 * @param 	mixed  	 $service   The service ID or a list of services (for customers).
	 * @param 	integer  $flag 		A mask to filter the custom fields.
	 *
	 * @return 	array 	 The list of custom fields.
	 */
	public static function getList($group = 0, $employee = 0, $service = null, $flag = 0)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$columns = array_map(function($elem)
		{
			return "c.{$elem}";
		}, static::$listColumns);

		$q = $q->select($dbo->qn($columns))
			->from($dbo->qn('#__vikappointments_custfields', 'c'))
			->where($dbo->qn('group') . ' = ' . (int) $group)
			->group($dbo->qn('c.id'))
			->order($dbo->qn('c.ordering') . ' ASC');

		// custom fields for customers
		if ($group == static::GROUP_CUSTOMERS)
		{
			// employees
			$ids = array(-1);

			if ($employee > 0)
			{
				$ids[] = (int) $employee;
			}

			// filter by employee
			$q->where($dbo->qn('c.id_employee') . ' IN (' . implode(', ', $ids) . ')');

			// build query to count services
			$countServices = $dbo->getQuery(true)
				->select('COUNT(1)')
				->from($dbo->qn('#__vikappointments_cf_service_assoc', 'a2'))
				->where($dbo->qn('a2.id_field') . ' = ' . $dbo->qn('c.id'));

			// filter by services
			if ($service)
			{
				$ids = array_map('intval', (array) $service);

				$q->leftjoin($dbo->qn('#__vikappointments_cf_service_assoc', 'a') . ' ON ' . $dbo->qn('a.id_field') . ' = ' . $dbo->qn('c.id'));
				$q->andWhere(array(
					$dbo->qn('a.id_service') . ' IN (' . implode(', ', $ids) . ')',
					'(' . $countServices . ') = 0',
				), 'OR');
			}
			// otherwise exclude the fields assigned to specific services
			else
			{
				$q->where('(' . $countServices . ') = 0');
			}
		}

		// exclude the required checkbox
		if ($flag & CF_EXCLUDE_REQUIRED_CHECKBOX)
		{
			$q->andWhere(array(
				$dbo->qn('c.type') . ' <> ' . $dbo->q('checkbox'),
				$dbo->qn('c.required') . ' = 0',
			));
		}

		// exclude separator
		if ($flag & CF_EXCLUDE_SEPARATOR)
		{
			$q->where($dbo->qn('c.type') . ' <> ' . $dbo->q('separator'));
		}

		// exclude file
		if ($flag & CF_EXCLUDE_FILE)
		{
			$q->where($dbo->qn('c.type') . ' <> ' . $dbo->q('file'));
		}

		$dbo->setQuery($q);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			return array();
		}

		$fields = $dbo->loadAssocList();

		foreach ($fields as $i => $f)
		{
			/**
			 * Added support for missing fields in case the multilingual
			 * feature is disabled.
			 *
			 * @since 1.6.1
			 */

			// keep original 'choose' for select
			$fields[$i]['_choose']  = $f['choose'];
			// backward compatibility for old translation technique
			$fields[$i]['langname'] = JText::_($f['name']);
		}

		return $fields;
	}

	/**
	 * Return the default country code assigned to the phone number custom field.
	 *
	 * @param 	string 	$langtag 	The langtag to retrieve the proper country 
	 * 								depending on the current language.
	 * @param 	mixed 	$default 	The default return value in case of unsupported
	 * 								
	 *
	 * @return 	string 	The default country code.
	 */
	public static function getDefaultCountryCode($langtag = null, $default = true)
	{
		/**
		 * Auto-detect language tag if not specified.
		 *
		 * @since 1.6.3
		 */
		if (!$langtag)
		{
			$langtag = JFactory::getLanguage()->getTag();
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$q->select(array(
				$dbo->qn('c.id'),
				$dbo->qn('c.choose'),
				$dbo->qn('l.choose', 'lang_choose'),
				$dbo->qn('l.tag'),
			))
			->from($dbo->qn('#__vikappointments_custfields', 'c'))
			->leftjoin($dbo->qn('#__vikappointments_lang_customf', 'l') 
				. ' ON ' . $dbo->qn('l.id_customf') . ' = ' . $dbo->qn('c.id')
				. ' AND ' . $dbo->qn('l.tag') . ' = ' . $dbo->q($langtag))
			->where('`c`.`rule` = ' . self::PHONE_NUMBER);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			/**
			 * Evaluate to return default country code or specified value.
			 *
			 * @since 1.6.3
			 */
			return $default === true ? self::$defaultCountry : $default;
		}

		$row = $dbo->loadAssoc();

		// make sure we found a matching custom field
		if ($row['tag'] == $langtag && strlen($row['lang_choose']))
		{
			// use country code defined in langtag
			$row['choose'] = $row['lang_choose'];
		}
		// check if we should return the specified default value
		else if ($default !== true)
		{
			// unset string to return default value
			$row['choose'] = '';
		}

		$default === true ? self::$defaultCountry : $default;

		// if we have a valid country code, return it, otherwise return the default value
		return strlen($row['choose']) ? $row['choose'] : $default;
	}

	/**
	 * Translates the specified custom fields.
	 * The translation of the name will be placed in a different column 'langname'. 
	 * The original 'name' column won't be altered.
	 *
	 * @param 	array 	$fields  The records to translate.
	 *
	 * @return 	void
	 */
	public static function translate(array &$fields, $tag = null)
	{
		/**
		 * Added support for missing fields in case the multilingual
		 * feature is disabled.
		 *
		 * Since the custom fields may be recovered without using
		 * the apposite getList() method, we need to replicate the
		 * same code also in this translation method.
		 *
		 * @since 1.6.1
		 */
		foreach ($fields as $i => $f)
		{
			if (!isset($f['_choose']))
			{
				// keep original 'choose' for select
				$fields[$i]['_choose']  = $f['choose'];
			}

			if (!isset($f['langname']))
			{
				// backward compatibility for old translation technique
				$fields[$i]['langname'] = JText::_($f['name']);
			}
		}

		if (!VikAppointments::isMultiLanguage() || !count($fields))
		{
			return;
		}

		if (empty($tag))
		{
			$tag = JFactory::getLanguage()->getTag();
		}

		$dbo = JFactory::getDbo();

		// get all IDs
		$ids = array();

		foreach ($fields as $f)
		{
			$ids[] = $f['id'];
		}

		// obtain translations
		$translations = array();
		
		$q = $dbo->getQuery(true);

		$q->select('*')
			->from($dbo->qn('#__vikappointments_lang_customf'))
			->where(array(
				$dbo->qn('tag') . ' = ' . $dbo->q($tag),
				$dbo->qn('id_customf') . ' IN (' . implode(',', $ids) . ')',
			));

		$dbo->setQuery($q, 0, count($ids));
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadAssocList() as $assoc)
			{
				$translations[$assoc['id_customf']] = $assoc;
			}
		}

		// apply translations
		foreach ($fields as $i => $f)
		{
			$fields[$i]['langname'] = VikAppointments::getTranslation($f['id'], $f, $translations, null		, 'name'   , JText::_($f['name']));
			$fields[$i]['poplink']  = VikAppointments::getTranslation($f['id'], $f, $translations, 'poplink', 'poplink', ''					 );
			$fields[$i]['choose']   = VikAppointments::getTranslation($f['id'], $f, $translations, 'choose'	, 'choose' , ''					 );
		}
	}

	/**
	 * Translates the specified custom fields array data.
	 *
	 * @param 	array 	$data 	 The associative array with the CF data.
	 * @param 	array 	$fields  The custom fields (MUST BE already translated).
	 *
	 * @return 	array 	The translated CF data array.
	 *
	 * @uses 	findField()
	 */
	public static function translateObject(array $data, array $fields)
	{
		$tmp = array();

		foreach ($data as $k => $v)
		{
			// get custom field
			$cf = static::findField(array('name', $k), $fields, 1);

			if ($cf)
			{
				// get translated name
				$name = static::getName($cf);
				$name = static::getColumnValue($cf, 'langname', $name);

				// check if the CF is a select
				if (static::isSelect($cf))
				{
					// obtain accepted options
					$original = array_filter(explode(';;__;;', static::getColumnValue($cf, '_choose')));
					$options  = array_filter(explode(';;__;;', static::getColumnValue($cf, 'choose')));

					// handle multiple select
					if (static::getColumnValue($cf, 'multiple'))
					{
						// decode JSON string
						$json = json_decode($v);

						// iterate all the selected elements
						foreach ($json as $j => $opt)
						{
							// find the index of the selected option
							$index = array_search($opt, $original);
							// get the translated option at the same position
							$json[$j] = $index !== false ? $options[$index] : $opt;
						}

						// implode the list
						$v = implode(', ', $json);
					}
					// otherwise handle a single selection
					else
					{
						// find the index of the selected option
						$index = array_search($v, $original);
						// get the translated option at the same position
						$v = $index ? $options[$index] : $v;
					}
				}
				// check if the CF is a checkbox
				else if (static::isCheckbox($cf))
				{
					$v = JText::_($v ? 'JYES' : 'JNO');
				}

				// set the translated key/val pair
				$tmp[$name] = $v;
			}
			else
			{
				// The custom field doesn't exist anymore.
				// Keep the original data to avoid losing them.

				$json = (array) json_decode($v, true);

				if ($json)
				{
					// the value was a JSON object, implode the array
					// to have a more readable string
					$v = implode(', ', $json);
				}

				$tmp[$k] = $v;
			}
		}

		return $tmp;
	}

	/**
	 * Searches a custom field using the specified query string.
	 *
	 * @param 	mixed 	 $key 	  The query params (the value to search for or an array
	 * 							  containing the column and the value).
	 * @param 	array 	 $fields  The custom fields list.
	 * @param 	integer  $fields  The maximum number of records to get (0 to ignore the limit).
	 *
	 * @return 	mixed 	 The custom fields that match the query.
	 */
	protected static function findField($key, array $fields, $lim = 0)
	{
		$list = array();

		// if the key is a string, search by ID column
		if (is_string($key))
		{
			$key = array('id', $key);
		}

		foreach ($fields as $cf)
		{
			// check if the column value is equals to the key
			if (self::getColumnValue($cf, $key[0], null) == $key[1])
			{
				// push the custom field in the list
				$list[] = $cf;

				// stop iterating if we reached the limit
				if (count($list) == $lim)
				{
					break;
				}
			}
		}

		// return false if no matches
		if (!count($list))
		{
			return false;
		}
		// return the CF if the limit was set to 1
		else if ($lim == 1)
		{
			return reset($list);
		}

		// return the list of custom fields found (never empty)
		return $list;
	}

	/**
	 * Returns the custom fields values specified in the REQUEST.
	 *
	 * @param 	mixed 	 $fields 	The custom fields list to check for.
	 * 								If the list is not an array, the method will load
	 * 								all the custom fields that belong to the specified group.
	 * @param 	array 	 &$args 	The array data to fill-in in case of specific rules (name, e-mail, etc...).
	 * @param 	boolean  $strict 	True to raise an error when a mandatory field is missing.
	 *
	 * @return 	array 	The lookup array containing the values of the custom fields.
	 *
	 * @throws 	Exception 	When a mandatory field is empty or when a file hasn't been uploaded.
	 *
	 * @uses 	getList()
	 * @uses 	sanitizeFieldValue()
	 * @uses 	validateField()
	 * @uses 	dispatchRule()
	 * @uses 	helper methods to access fields properties
	 */
	public static function loadFromRequest($fields = 0, array &$args, $strict = true)
	{
		$lookup = array();

		// if not an array, get the fields from the DB using the specified section
		if (!is_array($fields))
		{
			$fields = static::getList($fields);
		}

		// return an empty list in case there are no published fields
		if (!count($fields))
		{
			return $lookup;
		}

		// if not exists, declare 'uploads' property to avoid warnings
		if (!isset($args['uploads']))
		{
			$args['uploads'] = array();
		}

		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		foreach ($fields as $cf)
		{
			$skip = false;

			$id = static::getID($cf);

			if ($cf['group'] == static::GROUP_CUSTOMERS)
			{
				$name = static::getName($cf);
			}
			else if ($cf['group'] == static::GROUP_EMPLOYEES)
			{
				$name = static::getColumnValue($cf, 'formname', null);
			}

			if (!static::isInputFile($cf))
			{
				$settings = (array) json_decode(static::getColumnValue($cf, 'choose', '{}'), true);

				/**
				 * In case of editor field, do not strip HTML tags.
				 *
				 * @since 1.6.3
				 */
				if (static::isTextArea($cf) && !empty($settings['editor']))
				{
					// get value from request without stripping HTML tags
					$lookup[$name] = $input->getRaw('vapcf' . $id, '');
				}
				else
				{
					// get value from request
					$lookup[$name] = $input->getString('vapcf' . $id, '');
				}

				// if MULTIPLE select, stringify the selected options
				if (is_array($lookup[$name]))
				{
					$lookup[$name] = json_encode($lookup[$name]);
				}
			}
			else
			{
				// get uploaded file from request
				$lookup[$name] = $input->files->get('vapcf' . $id, null, 'array');

				// obtain old file from request
				$old_file = $input->getString('old_vapcf' . $id, '');

				// check if the file hasn't been uploaded
				if (!isset($lookup[$name]) || strlen($lookup[$name]['name']) == 0)
				{
					// check if there is already an uploaded file for this field
					if (!empty($old_file) && file_exists(VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . $old_file))
					{
						// we don't need to validate this field
						$skip = true;
						// pushes the old file in the uploads list
						$args['uploads'][$name] = $old_file;
					}

					// unset always file array from custom fields
					$lookup[$name] = '';
				}
			}

			// sanitize the value obtained
			$lookup[$name] = static::sanitizeFieldValue($cf, $lookup[$name]);

			// make sure the field should be validated
			if (!$skip)
			{
				// validate the custom fields
				if (!static::validateField($cf, $lookup[$name]))
				{
					if ($strict)
					{
						// raise an error, the custom field is invalid
						throw new Exception(JText::_('VAPERRINSUFFCUSTF'));
					}
				}
				// validate the uploaded file
				else if (static::isInputFile($cf))
				{
					// upload the file and get the response
					$file_res = VikAppointments::uploadFile(
						$lookup[$name],
						static::getColumnValue($cf, 'choose', ''),
						VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR);
					
					// unset always file array from custom fields
					$lookup[$name] = '';
					
					// in case of failure raise an error
					if ($file_res['esit'] != 1 && static::isRequired($cf))
					{
						if ($strict)
						{
							throw new Exception(JText::_('VAPFILEUPLOADERR'));
						}
					}
					else
					{
						// update the uploads map with the name of the file
						$args['uploads'][$name] = $file_res['name'];
						
						// unlink the previous file if exists
						if (!empty($old_file) && file_exists(VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . $old_file))
						{
							unlink(VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . $old_file);
						}
					}
				}
			}

			// dispatch the rule to fill $args array
			static::dispatchRule($cf, $lookup[$name], $args);
		}

		return $lookup;
	}

	/**
	 * Sanitize the field value.
	 *
	 * @param 	mixed 	$field 	The custom field.
	 * @param 	string 	$value 	The value to sanitize.
	 *
	 * @return 	mixed 	The sanitized value.
	 */
	protected static function sanitizeFieldValue($field, $value)
	{
		// sanitize a input number
		if (static::isInputNumber($field))
		{
			// decode the settings
			$settings = json_decode(static::getColumnValue($field, 'choose', '{}'), true);

			// convert the string to float
			$value = floatval($value);
			
			// if min setting exists, make sure the value is not lower
			if (strlen($settings['min']))
			{
				$value = max(array($value, (float) $settings['min']));
			}

			// if max setting exists, make sure the value is not higher
			if (strlen($settings['max']))
			{
				$value = min(array($value, (float) $settings['max']));
			}

			// if decimals are not supported, round the value
			if (!$settings['decimals'])
			{
				$value = round($value);
			}
		}

		return $value;
	}

	/**
	 * Checks if the value of the field is accepted.
	 *
	 * @param 	mixed 	 $field  The custom field to evaluate.
	 * @param 	string 	 $value  The value of the field.
	 *
	 * @return 	boolean  True if valid, otherwise false.
	 */
	protected static function validateField($field, $value)
	{
		return (!static::isRequired($field)
			|| (!static::isInputFile($field) && strlen($value))
			|| (static::isInputFile($field) && !empty($value['name'])));
	}

	/**
	 * Dispatched the rule of the field.
	 *
	 * @param 	mixed 	$field 	The custom field to evaluate.
	 * @param 	string 	$value  The value of the field.
	 * @param 	array 	&$args 	The array data to fill-in in case of specific rules (name, e-mail, etc...).
	 *
	 * @return 	void
	 *
	 * @uses 	isNominative()
	 * @uses 	isEmail()
	 * @uses 	isPhoneNumber()
	 */
	protected static function dispatchRule($field, $value, array &$args)
	{
		// check if the field is a nominative
		if (static::isNominative($field))
		{
			if (!empty($args['purchaser_nominative']))
			{
				$args['purchaser_nominative'] .= ' ';
			}
			else
			{
				$args['purchaser_nominative'] = '';
			}

			$args['purchaser_nominative'] .= $value;
		}
		// check if the field is an e-mail
		else if (static::isEmail($field))
		{
			$args['purchaser_mail'] = $value;
		}
		// check if the field is a phone number
		else if (static::isPhoneNumber($field))
		{
			// get the prefix country (ID_C2CODE)
			$country_key = JFactory::getApplication()->input->getString('vapcf' . static::getID($field) . '_prfx', '');

			if (!empty($country_key))
			{
				// explode the string
				$country_key = explode('_', $country_key);

				// get the country using the 2 letters code
				$country = VikAppointmentsLocations::getCountryFromCode($country_key[1]);
				if ($country !== false)
				{
					$args['purchaser_prefix'] 	= $country['phone_prefix'];
					$args['purchaser_country'] 	= $country['country_2_code'];
				}
			}

			// sanitize phone number
			$args['purchaser_phone'] = str_replace(' ', '', $value);
		}
		// check if the field is a state or a province
		else if (static::isStateProvince($field))
		{
			$args['billing_state'] = $value;
		}
		// check if the field is a city
		else if (static::isCity($field))
		{
			$args['billing_city'] = $value;
		}
		// check if the field is an address
		else if (static::isAddress($field))
		{
			$args['billing_address'] = $value;
		}
		// check if the field is a ZIP code
		else if (static::isZipCode($field))
		{
			$args['billing_zip'] = $value;
		}
		// check if the field is a company name
		else if (static::isCompanyName($field))
		{
			$args['company'] = $value;
		}
		// check if the field is a VAT number
		else if (static::isVatNumber($field))
		{
			$args['vatnum'] = $value;
		}
	}

	/**
	 * Get the ID property of the specified custom field object.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @return 	integer  The ID of the custom field.
	 *
	 * @uses 	getColumnValue()
	 */
	public static function getID($cf)
	{
		return static::getColumnValue($cf, 'id', 0);
	}

	/**
	 * Get the NAME property of the specified custom field object.
	 *
	 * @param 	mixed 	$cf  The array or the object of the custom field.
	 *
	 * @return 	string 	The name of the custom field.
	 *
	 * @uses 	getColumnValue()
	 */
	public static function getName($cf)
	{
		return static::getColumnValue($cf, 'name', '');
	}

	/**
	 * Checks if the specified custom field is required.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @return 	boolean  True if required, otherwise false.
	 *
	 * @uses 	getColumnValue()
	 */
	public static function isRequired($cf)
	{
		return (bool) static::getColumnValue($cf, 'required', 0);
	}

	/**
	 * Get the TYPE property of the specified custom field object.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @return 	integer  The type of the custom field.
	 *
	 * @uses 	getColumnValue()
	 */
	public static function getType($cf)
	{
		return static::getColumnValue($cf, 'type', 'text');
	}

	/**
	 * Get the RULE property of the specified custom field object.
	 *
	 * @param 	mixed 	$cf  The array or the object of the custom field.
	 *
	 * @return 	string  The rule of the custom field, 
	 * 					'text' if it is not possible to estabilish it.
	 *
	 * @uses 	getColumnValue()
	 */
	public static function getRule($cf)
	{
		return static::getColumnValue($cf, 'rule', self::NONE);
	}

	/**
	 * Checks if the custom field is a nominative.
	 *
	 * @param 	mixed 	 $cf 	The array or the object of the custom field.
	 *
	 * @param 	boolean  True if nominative, otherwise false.
	 *
	 * @uses 	getRule()
	 */
	public static function isNominative($cf)
	{
		return static::getRule($cf) == self::NOMINATIVE;
	}

	/**
	 * Checks if the custom field is an e-mail.
	 *
	 * @param 	mixed 	$cf  The array or the object of the custom field.
	 *
	 * @param 	boolean 	 True if e-mail, otherwise false.
	 *
	 * @uses 	getRule()
	 */
	public static function isEmail($cf)
	{
		return static::getRule($cf) == self::EMAIL;
	}

	/**
	 * Checks if the custom field is a phone number.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @param 	boolean  True if phone number, otherwise false.
	 *
	 * @uses 	getRule()
	 */
	public static function isPhoneNumber($cf)
	{
		return static::getRule($cf) == self::PHONE_NUMBER;
	}

	/**
	 * Checks if the custom field is a state or a province.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @param 	boolean  True if a state, otherwise false.
	 *
	 * @uses 	getRule()
	 */
	public static function isStateProvince($cf)
	{
		return static::getRule($cf) == self::STATE;
	}

	/**
	 * Checks if the custom field is a city.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @param 	boolean  True if city, otherwise false.
	 *
	 * @uses 	getRule()
	 */
	public static function isCity($cf)
	{
		return static::getRule($cf) == self::CITY;
	}

	/**
	 * Checks if the custom field is an address.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @param 	boolean  True if address, otherwise false.
	 *
	 * @uses 	getRule()
	 */
	public static function isAddress($cf)
	{
		return static::getRule($cf) == self::ADDRESS;
	}

	/**
	 * Checks if the custom field is a ZIP code.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @param 	boolean  True if ZIP code, otherwise false.
	 *
	 * @uses 	getRule()
	 */
	public static function isZipCode($cf)
	{
		return static::getRule($cf) == self::ZIP;
	}

	/**
	 * Checks if the custom field is a company name.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @param 	boolean  True if company name, otherwise false.
	 *
	 * @uses 	getRule()
	 */
	public static function isCompanyName($cf)
	{
		return static::getRule($cf) == self::COMPANY;
	}

	/**
	 * Checks if the custom field is a VAT number.
	 *
	 * @param 	mixed 	 $cf  The array or the object of the custom field.
	 *
	 * @param 	boolean  True if VAT number, otherwise false.
	 *
	 * @uses 	getRule()
	 */
	public static function isVatNumber($cf)
	{
		return static::getRule($cf) == self::VATNUM;
	}

	/**
	 * Checks if the custom field is an input text.
	 *
	 * @param 	mixed 	 $cf 	The array or the object of the custom field.
	 *
	 * @param 	boolean  True if input text, otherwise false.
	 *
	 * @uses 	getType()
	 */
	public static function isInputText($cf)
	{
		return static::getType($cf) == 'text';
	}

	/**
	 * Checks if the custom field is a textarea.
	 *
	 * @param 	mixed 	 $cf 	The array or the object of the custom field.
	 *
	 * @param 	boolean  True if textarea, otherwise false.
	 *
	 * @uses 	getType()
	 */
	public static function isTextArea($cf)
	{
		return static::getType($cf) == 'textarea';
	}

	/**
	 * Checks if the custom field is an input number.
	 *
	 * @param 	mixed 	 $cf 	The array or the object of the custom field.
	 *
	 * @param 	boolean  True if input number, otherwise false.
	 *
	 * @uses 	getType()
	 */
	public static function isInputNumber($cf)
	{
		return static::getType($cf) == 'number';
	}

	/**
	 * Checks if the custom field is a select.
	 *
	 * @param 	mixed 	 $cf 	The array or the object of the custom field.
	 *
	 * @param 	boolean  True if select, otherwise false.
	 *
	 * @uses 	getType()
	 */
	public static function isSelect($cf)
	{
		return static::getType($cf) == 'select';
	}

	/**
	 * Checks if the custom field is a datepicker.
	 *
	 * @param 	mixed 	 $cf 	The array or the object of the custom field.
	 *
	 * @param 	boolean  True if datepicker, otherwise false.
	 *
	 * @uses 	getType()
	 */
	public static function isCalendar($cf)
	{
		return static::getType($cf) == 'date';
	}

	/**
	 * Checks if the custom field is an input file.
	 *
	 * @param 	mixed 	 $cf 	The array or the object of the custom field.
	 *
	 * @param 	boolean  True if input file, otherwise false.
	 *
	 * @uses 	getType()
	 */
	public static function isInputFile($cf)
	{
		return static::getType($cf) == 'file';
	}

	/**
	 * Checks if the custom field is a checkbox.
	 *
	 * @param 	mixed 	 $cf 	The array or the object of the custom field.
	 *
	 * @param 	boolean  True if checkbox otherwise false.
	 *
	 * @uses 	getType()
	 */
	public static function isCheckbox($cf)
	{
		return static::getType($cf) == 'checkbox';
	}

	/**
	 * Checks if the custom field is a separator.
	 *
	 * @param 	mixed 	 $cf 	The array or the object of the custom field.
	 *
	 * @param 	boolean  True if separator, otherwise false.
	 *
	 * @uses 	getType()
	 */
	public static function isSeparator($cf)
	{
		return static::getType($cf) == 'separator';
	}

	/**
	 * Method used to access the attributes and properties of the given
	 * custom field. Useful if we don't know if we are handling an array or an object.
	 *
	 * @param 	mixed 	$cf 	  The custom field array/object.
	 * @param 	string 	$column   The column to access.
	 * @param 	mixed 	$default  The default value in case the column does not exist.
	 *
	 * @return 	mixed 	The value at the specified column if exists, otherwise the default one.
	 */
	protected static function getColumnValue($cf, $column, $default = null)
	{
		// check if the field is an array
		if (is_array($cf))
		{
			// if the column key exists, return the value
			if (array_key_exists($column, $cf))
			{
				return $cf[$column];
			}
		}
		// check if the field is an object
		else if (is_object($cf))
		{
			// if the property exists, return the value
			if (property_exists($cf, $column))
			{
				return $cf->{$columns};
			}
		}

		// otherwise return the default one
		return $default;
	}

	/**
	 * Customers identifier group.
	 *
	 * @var integer
	 */
	const GROUP_CUSTOMERS = 0;

	/**
	 * Employees identifier group.
	 *
	 * @var integer
	 */
	const GROUP_EMPLOYEES = 1;

	/**
	 * NONE identifier rule.
	 *
	 * @var integer
	 */
	const NONE = 0;

	/**
	 * NOMINATIVE identifier rule.
	 *
	 * @var integer
	 */
	const NOMINATIVE = 1;

	/**
	 * EMAIL identifier rule.
	 *
	 * @var integer
	 */
	const EMAIL = 2;

	/**
	 * PHONE NUMBER identifier rule.
	 *
	 * @var integer
	 */
	const PHONE_NUMBER = 3;

	/**
	 * STATE/PROVINCE identifier rule.
	 *
	 * @var integer
	 */
	const STATE = 4;

	/**
	 * CITY identifier rule.
	 *
	 * @var integer
	 */
	const CITY = 5;

	/**
	 * ADDRESS identifier rule.
	 *
	 * @var integer
	 */
	const ADDRESS = 6;

	/**
	 * ZIP/CAP identifier rule.
	 *
	 * @var integer
	 */
	const ZIP = 7;

	/**
	 * COMPANY NAME identifier rule.
	 *
	 * @var integer
	 */
	const COMPANY = 8;

	/**
	 * VAT NUMBER identifier rule.
	 *
	 * @var integer
	 */
	const VATNUM = 9;
}
