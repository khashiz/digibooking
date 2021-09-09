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

jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

/**
 * Script file of VikAppointments component
 */
class com_vikappointmentsInstallerScript
{
	/**
	 * Method used to install the component.
	 *
	 * @param 	object 	 $parent  The parent class which is calling this method.
	 *
	 * @return 	boolean  True on success, otherwise false to stop the flow.
	 */
	function install($parent)
	{
		// require autoloader
		require_once implode(DIRECTORY_SEPARATOR, array(JPATH_SITE, 'components', 'com_vikappointments', 'helpers', 'libraries', 'autoload.php'));

		$config = UIFactory::getConfig();

		//eval(read('246670203D20666F70656E2856415041444D494E202E204449524543544F52595F534550415241544F52202E2022636F6D5F76696B6170706F696E746D656E74736174222C20227722293B246820203D20676574656E762822485454505F484F535422293B246E20203D20676574656E7628225345525645525F4E414D4522293B69662028707265675F6D6174636828222F6C6F63616C686F73742F69222C20246829297B667772697465282466702C20656E6372797074436F6F6B696528246829293B7D656C73657B24637276203D206E65772043726561746976696B446F74497428293B69662028246372762D3E6B73612822687474703A2F2F7777772E63726561746976696B2E69742F76696B6C6963656E73652F3F76696B683D22202E2075726C656E636F646528246829202E20222676696B736E3D22202E2075726C656E636F646528246E29202E2022266170703D22202E2075726C656E636F64652843524541544956494B4150502929297B696620287374726C656E28246372762D3E7469736529203D3D2032297B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D656C73657B4A466163746F72793A3A6765744170706C69636174696F6E28292D3E656E71756575654D65737361676528246372762D3E746973652C20226572726F7222293B7D7D656C73657B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D7D66636C6F736528246670293B'));
		
		$user 	 = JFactory::getUser();
		$synckey = VikAppointments::generateSerialCode(12);

		// get JUri object
		$uri = JUri::getInstance();

		// get host from URI
		$domain = $uri->toString(array('host'));

		// split third-level domains
		$domain = implode(' ', explode('.', $domain));

		// make the word uppercase
		$domain = ucwords($domain);

		// update config
		$config->set('agencyname'		, $domain);
		$config->set('adminemail'		, $user->email);
		$config->set('synckey'			, $synckey);
		$config->set('cron_secure_key'	, $synckey);
		
		?>
		<div style="text-align: center;">
			<p>
				<strong>VikAppointments v1.6.3 - e4j Extensionsforjoomla.com</strong>
			</p>
			<img src="<?php echo JUri::root(); ?>administrator/components/com_vikappointments/assets/images/vikappointments.jpg" />
		</div>
		<?php

		// write CSS custom file
		$path = VAPBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'vap-custom.css';
		$handle = fopen($path, 'w+');
		fwrite($handle, "/* put below your custom css code for VikAppointments */\n");
		fclose($handle);

		return true;
	}

	/**
	 * Method used to uninstall the component.
	 *
	 * @param 	object 	 $parent  The parent class which is calling this method.
	 *
	 * @return 	boolean  True on success, otherwise false to stop the flow.
	 */
	function uninstall($parent)
	{
		echo 'VikAppointments was uninstalled. e4j - <a href="https://extensionsforjoomla.com">Extensionsforjoomla.com</a>';

		return true;
	}

	/**
	 * Method used to update the component.
	 *
	 * @param 	object 	 $parent  The parent class which is calling this method.
	 *
	 * @return 	boolean  True on success, otherwise false to stop the flow.
	 *
	 * @uses 	runUpdateCallbacks()
	 */
	function update($parent)
	{
		// require autoloader
		require_once implode(DIRECTORY_SEPARATOR, array(JPATH_SITE, 'components', 'com_vikappointments', 'helpers', 'libraries', 'autoload.php'));

		// return update callbacks status
		return $this->runUpdateCallbacks($this->version, 'update');
	}

	/**
	 * Method used to run before an install/update/uninstall method.
	 *
	 * @param 	string   $type 	  The method type [install, update, uninstall].
	 * @param 	object 	 $parent  The parent class which is calling this method.
	 *
	 * @return 	boolean  True on success, otherwise false to stop the flow.
	 */
	function preflight($type, $parent)
	{
		// no need to continue if the type is not "update"
		if ($type !== 'update')
		{
			return true;
		}

		// NOTE. no access to new files of the updater downloaded/installed
		// you MUST use new libraries in update and postflight methods.

		$dbo = JFactory::getDbo();

		// keep current version in the properties of this class
		$q = $dbo->getQuery(true);

		$q->select('setting')
			->from('#__vikappointments_config')
			->where('param = ' . $dbo->q('version'));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// impossible to recognize the version of the component
			return false;
		}

		// keep current version in the properties of this class
		$this->version = $dbo->loadResult();

		/**
		 * Get custom fields.
		 *
		 * @since 1.6
		 */
		if (version_compare($this->version, '1.6', '<'))
		{
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_custfields'));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$this->customFields = $dbo->loadObjectList();
			}
		}

		/**
		 * Check if router is enabled.
		 *
		 * @since 1.6
		 */
		$this->routerEnabeld = is_file(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_vikappointments' . DIRECTORY_SEPARATOR . 'router.php');

		/**
		 * Fix the version bug for 1.6.1 release, which
		 * didn't update the DB version properly.
		 *
		 * @since 1.6.2
		 */
		if ($this->version === '1.6')
		{
			try
			{
				$q = $dbo->getQuery(true)
					->select($dbo->qn('metadata'))
					->from($dbo->qn('#__vikappointments_service'));

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				// exception was not thrown, we own the 1.6.1 version
				$this->version = '1.6.1';
			}
			catch (Exception $e)
			{
				// metadata column doesn't exist, we really own the 1.6 version
			}
		}

		// make sure the schema version matches the specified one
		$this->assertSchema($this->version);

		return true;
	}

	/**
	 * Method used to run after an install/update/uninstall method.
	 *
	 * @param 	string   $type 	  The method type [install, update, uninstall].
	 * @param 	object 	 $parent  The parent class which is calling this method.
	 *
	 * @return 	boolean  True on success, otherwise false to stop the flow.
	 */
	function postflight($type, $parent)
	{
		// no need to continue if the type is not "update"
		if ($type !== 'update')
		{
			return true;
		}

		// return finalise callbacks esit
		return $this->runUpdateCallbacks($this->version, 'finalise');
	}

	/**
	 * Loop through each supported version to discover update adapters.
	 *
	 * ------------------------------------------------------------------------------------
	 *
	 * Update adapter CLASS name must have the following structure:
	 * 
	 * COMPONENT_NAME (no com_) + "UpdateAdapter" + VERSION (replace dots with underscores)
	 * eg. ExampleUpdateAdapter1_2_5 (com_example 1.2.5)
	 *
	 * ------------------------------------------------------------------------------------
	 *
	 * Update adapter FILE name must have the following structure:
	 * 
	 * "upd" + VERSION (replace dots with underscores) + ".php"
	 * eg. upd1_2_5.php (com_example 1.2.5)
	 *
	 * @param 	string 	 $version 	The current version of the software. 	
	 * @param 	string 	 $callback 	The callback function to perform.
	 *
	 * @return 	boolean  True on success, otherwise false to stop the flow.
	 *
	 * @since 	1.6
	 */
	private function runUpdateCallbacks($version, $callback)
	{
		// iterate each supported version
		foreach ($this->versionsPool as $v)
		{
			// get version suffix by replacing all dots with underscores.
			$safe_suffix = str_replace('.', '_', $v);

			// get filename to include updater adapter for current loop version
			$filename = 'upd' . $safe_suffix;

			// get class name of update adapter for current loop version
			$classname = 'VikAppointmentsUpdateAdapter' . $safe_suffix;

			// in case the software version is lower than loop version
			if (version_compare($version, $v, '<'))
			{
				// load updater adapter file
				$loaded = UILoader::import('libraries.update.adapters.' . $filename);

				// in case the file has been loaded
				// and the adapter class owns the specified callback
				if ($loaded && method_exists($classname, $callback))
				{
					// then run update callback function
					$success = call_user_func(array($classname, $callback), $this);

					if ($success === false)
					{
						// stop adapters in case something gone wrong
						return false;
					}
				}

				// NOTE. it is not needed to check if the class exists because the 
				// method_exists function would return always false
			}
		}

		// no error found
		return true;
	}

	/**
	 * Check the schema of the extension to make sure the system will use
	 * the current version.
	 *
	 * @param 	string 	 $version 	The current version of the component.
	 *
	 * @return 	boolean  True if the schema has been altered, otherwise false.
	 *
	 * @since 	1.6.2
	 */
	private function assertSchema($version)
	{
		JLoader::import('joomla.application.component.helper');
		$component = JComponentHelper::getComponent('com_vikappointments');

		$ok = false;

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('version_id'))
			->from($dbo->qn('#__schemas'))
			->where($dbo->qn('extension_id') . ' = ' . (int) $component->id);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			if ($dbo->loadResult() == $version)
			{
				$ok = true;
			}
			else
			{
				$q->clear()
					->delete($dbo->qn('#__schemas'))
					->where($dbo->qn('extension_id') . ' = ' . (int) $component->id);

				$dbo->setQuery($q);
				$dbo->execute();
			}
		}

		if (!$ok)
		{
			$q->clear()
				->insert($dbo->qn('#__schemas'))
				->columns(array($dbo->qn('extension_id'), $dbo->qn('version_id')))
				->values($component->id . ', ' . $dbo->q($version));

			$dbo->setQuery($q);
			$ok = (bool) $dbo->execute();
		}

		return $ok;
	}

	/**
	 * List containing all the versions next to the very first (supported) one.
	 *
	 * @var 	array
	 * @since 	1.6
	 */
	private $versionsPool = array('1.6');
}
