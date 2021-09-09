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
 * Helper class used to manipulate the alias of the records.
 *
 * @since 1.6
 */
class AliasHelper
{
	/**
	 * A list containing all the tables that support the alias.
	 *
	 * @var array
	 */
	protected static $tables = array(
		'service' 	=> array(
			'pk'		 => 'id',
			'table'		 => 'service',
			'lang_assoc' => 'id_service',
			'lang_table' => 'lang_service',
		),

		'employee'	=> array(
			'pk'		 => 'id',
			'table'		 => 'employee',
			'lang_assoc' => 'id_employee',
			'lang_table' => 'lang_employee',
		),
	);

	/**
	 * Method used to make URL-safe any strings.
	 *
	 * @param 	string 	$src  The string to convert.
	 *
	 * @return 	string 	The safe string.
	 */
	public static function stringToAlias($src)
	{
		return JFilterOutput::stringURLSafe($src);
	}

	/**
	 * Returns a valid and unique alias for SEO usages.
	 *
	 * @param 	string 	 $src 	The source name.
	 * @param 	string 	 $type 	The type name (e.g. service, employee, etc...).
	 * @param 	integer  $id 	The ID of record to exclude while serching other aliases.
	 *
	 * @return 	string 	 The resulting alias.
	 *
	 * @uses 	isExistingAlias()
	 */
	public static function getUniqueAlias($src, $type = '', $id = null)
	{
		$src = static::stringToAlias($src);
		
		$alias = $src;
		$cont  = 1;
		
		do
		{
			if ($cont > 1)
			{
				$alias = $src . "-" . $cont;
			}
			
			$cont++;
		} while (static::isExistingAlias($alias, $type, $id));

		return $alias;
	}

	/**
	 * Checks if the given alias already exists.
	 * An alias must be unique across all the supported tables.
	 *
	 * @param 	string 	 $src 	The source name.
	 * @param 	string 	 $type 	The type name (e.g. service, employee, etc...).
	 * @param 	integer  $id 	The ID of record to exclude while serching other aliases.
	 *
	 * @return 	boolean  True if the alias already exists, false otherwise.
	 */
	public static function isExistingAlias($alias, $type = '', $id = null)
	{
		$dbo = JFactory::getDbo();

		foreach (static::$tables as $k => $opt)
		{
			$q = $dbo->getQuery(true)
				->select(1)
				->from($dbo->qn('#__vikappointments_' . $opt['table']))
				->where($dbo->qn('alias') . ' = ' . $dbo->q($alias));

			if ($type == $k && $id)
			{
				$q->where($dbo->qn($opt['pk']) . ' <> ' . (int) $id);
			}

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the record that owns the specified alias.
	 *
	 * @param 	string 	 $alias	 The alias to find.
	 * @param 	string 	 $type   The type name (e.g. service, employee, etc...).
	 *
	 * @return 	mixed 	 The ID of the record found on success, null otherwise.
	 */
	public static function getRecordWithAlias($alias, $type)
	{
		$dbo = JFactory::getDbo();

		if (isset(static::$tables[$type]))
		{
			// create base query
			$q = static::createSearchQuery($type, $dbo);
			
			// get records with matching alias
			$q->where(array(
				$dbo->qn('r.alias') . ' = ' . $dbo->q($alias),
				$dbo->qn('l.alias') . ' = ' . $dbo->q($alias),
			), 'OR');

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				// return only the record ID
				return (int) $dbo->loadResult();
			}
		}

		return null;
	}

	/**
	 * Returns the alias used by the specified record.
	 *
	 * @param 	integer  $id	The ID of the record.
	 * @param 	string 	 $type  The type name (e.g. service, employee, etc...).
	 * @param 	mixed 	 $lang 	The current language for translated aliases.
	 *
	 * @return 	mixed 	 The alias of the record found, null otherwise.
	 *
	 * @uses 	createSearchQuery()
	 */
	public static function getRecordAlias($id, $type, $lang = null)
	{
		if (!$lang)
		{
			$lang = JFactory::getLanguage()->getTag();
		}

		if (isset(static::$tables[$type]))
		{
			$dbo = JFactory::getDbo();

			// create base query
			$q = static::createSearchQuery($type, $dbo);

			// return the records with matching ID
			$q->where($dbo->qn('r.id') . ' = ' . (int) $id);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$rows = $dbo->loadObjectList();

				// fallback to obtain a translated alias
				foreach ($rows as $row)
				{
					if ($row->tag == $lang && $row->lang_alias)
					{
						return $row->lang_alias;
					}
				}

				// return default alias
				return $rows[0]->alias;
			}
		}

		return null;
	}

	/**
	 * Returns the specified column of the record to search.
	 *
	 * @param 	integer  $id	The ID of the record.
	 * @param 	mixed  	 $key	The column of the record, or an array of columns to include the translation.
	 * @param 	string 	 $type  The type name (e.g. service, employee, etc...).
	 * @param 	mixed 	 $lang 	The current language for translated values.
	 *
	 * @return 	mixed 	 The alias of the record found, null otherwise.
	 *
	 * @uses 	createSearchQuery()
	 */
	public static function getRecordColumn($id, $key, $type, $lang = null)
	{
		if (!$lang)
		{
			$lang = JFactory::getLanguage()->getTag();
		}

		if (isset(static::$tables[$type]))
		{
			$dbo = JFactory::getDbo();

			if (is_scalar($key) || !$key)
			{
				$key = array($key);
			}

			// create base query
			$q = static::createSearchQuery($type, $dbo);

			$q->clear('select');
			$q->select($dbo->qn('r.' . $key[0]));

			if (isset($key[1]))
			{
				$lang_col = 'lang_' . $key[0];
				$q->select($dbo->qn('l.' . $key[1], $lang_col));
			}
			else
			{
				$lang_col = null;
			}

			// return the records with matching ID
			$q->where($dbo->qn('r.id') . ' = ' . (int) $id);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$rows = $dbo->loadObjectList();

				if ($lang_col)
				{
					// fallback to obtain a translated column
					foreach ($rows as $row)
					{
						if ($row->tag == $lang && $row->{$lang_col})
						{
							return $row->{$lang_col};
						}
					}
				}

				// return default value
				return $rows[0]->{$key[0]};
			}
		}

		return null;
	}

	/**
	 * Creates the base query used to search the records by ID/alias.
	 *
	 * @param 	string 	 $type  The type name (e.g. service, employee, etc...).
	 * @param 	mixed 	 $dbo 	The database object.
	 *
	 * @return 	mixed 	 The query builder instance.
	 */
	protected static function createSearchQuery($type, $dbo)
	{
		$q = $dbo->getQuery(true);

		$opt = static::$tables[$type];

		// record ID and alias
		$q->select($dbo->qn('r.' . $opt['pk']));
		$q->select($dbo->qn('r.alias'));
		// translation alias and lang tag
		$q->select($dbo->qn('l.alias', 'lang_alias'));
		$q->select($dbo->qn('l.tag'));
		// join between record and translation
		$q->from($dbo->qn('#__vikappointments_' . $opt['table'], 'r'));
		$q->leftjoin($dbo->qn('#__vikappointments_' . $opt['lang_table'], 'l') . ' ON ' . $dbo->qn('r.' . $opt['pk']) . ' = ' . $dbo->qn('l.' . $opt['lang_assoc']));

		return $q;
	}
}
