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
 * The cron job is used to send a reminder SMS to the customers.
 *
 * @since   1.5
 * @see     CronJob
 */
class CronJobSmsReminder extends CronJob
{
	/**
	 * @override
	 * Performs the work that the cron job should do.
	 *
	 * @return  CronResponse  The response of the job.
	 */
	public function doJob()
	{
		$dbo = JFactory::getDbo();

		$now = time();
		$sms_in_advance = $this->get('sms_in_advance') * 60; // get time in seconds

		// init cron response
		$response = new CronJobResponse();
		// if no error response will be true
		$response->setStatus(true);

		// retrieve appointments to notify

		$q = $dbo->getQuery(true)
			->select('`r`.*')
			->select(array(
				$dbo->qn('s.name', 'sname'),
				$dbo->qn('e.nickname'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
			->where(array(
				$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn('r.sms_notified_' . $this->id()) . ' = 0',
				$dbo->qn('r.purchaser_phone') . ' <> ""',
				$dbo->qn('r.checkin_ts') . ' > ' . $now,
				'(' . $dbo->qn('r.checkin_ts') . ' - ' . $sms_in_advance . ') < ' . $now, 
			))
			->order($dbo->qn('r.checkin_ts') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			// no appointments retrieved : stop process [OK]
			return $response->setContent('Process terminated correctly! No appointment to notify.');
		}

		$rows = $dbo->loadAssocList();

		// include sms api file
		$sms_api 		= VikAppointments::getSmsApi(true);
		$sms_api_fields = VikAppointments::getSmsApiFields(true);

		if (empty($sms_api) || !file_exists(VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $sms_api))
		{
			// the sms api file does not exist : stop process [FAIL]
			return $response->setContent('SMS Cron Notification Error! The SMS Api provider does not exists.')->setStatus(false);
		}

		require_once VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $sms_api;

		$config = UIFactory::getConfig();

		// get settings
		$date_format    = $config->get('dateformat');
		$time_format    = $config->get('timeformat');
		$company_name   = $config->get('agencyname');
		$def_lang_tag   = VikAppointments::getDefaultLanguage();

		// for each appointment found, send a notification e-mail
		foreach ($rows as $r)
		{
			$r['checkin'] 		= date($date_format . ' ' . $time_format, $r['checkin_ts']);
			$r['checkin_time'] 	= date($time_format, $r['checkin_ts']);

			if (empty($r['langtag']))
			{
				$r['langtag'] = $def_lang_tag;
			}

			$lang_services 	= VikAppointments::getTranslatedServices($r['id_service'], $r['langtag'], $dbo);
			$r['sname'] 	= VikAppointments::getTranslation($r['id_service'], $r, $lang_services, 'sname', 'name');
			$lang_employees = VikAppointments::getTranslatedServices($r['id_employee'], $r['langtag'], $dbo);
			$r['nickname'] 	= VikAppointments::getTranslation($r['id_employee'], $r, $lang_employees, 'nickname', 'nickname');

			/**
			 * Fetch location string.
			 *
			 * @since 1.6.3
			 */
			$location = VikAppointments::getEmployeeLocationFromTime($r['id_employee'], $r['id_service'], $r['checkin_ts']);

			$loc_str_long  = '';
			$loc_str_short = '';
			$loc_str_name  = '';

			if ($location !== false)
			{
				// get location details
				$location = VikAppointments::fillEmployeeLocation($location);

				// retrieve location name
				$loc_str_name = $location['name'];

				// fetch full string
				$loc_str_long = VikAppointments::locationToString($location);

				// unset country and state to use a short address
				$location['country_name'] = $location['state_name'] = '';
				// fetch short string
				$loc_str_short = VikAppointments::locationToString($location);
			}

			// render message
			$msg_text = $this->get('sms_content_' . strtolower(substr($r['langtag'], 0, 2)));
			$msg_text = str_replace('{checkin}', $r['checkin'], $msg_text);
			$msg_text = str_replace('{checkin_time}', $r['checkin_time'], $msg_text);
			$msg_text = str_replace('{service}', $r['sname'], $msg_text);
			$msg_text = str_replace('{employee}', $r['nickname'], $msg_text);
			$msg_text = str_replace('{company}', $company_name, $msg_text);
			$msg_text = str_replace('{customer}', $r['purchaser_nominative'], $msg_text);
			$msg_text = str_replace('{location}', $loc_str_long, $msg_text);
			$msg_text = str_replace('{location_short}', $loc_str_short, $msg_text);
			$msg_text = str_replace('{location_name}', $loc_str_name, $msg_text);

			// instantiate SMS Api class
			$api = new VikSmsApi(array(), $sms_api_fields);
			$sms_res = $api->sendMessage($r['purchaser_prefix'] . $r['purchaser_phone'], $msg_text);

			if ($api->validateResponse($sms_res))
			{
				// update database order

				$q = $dbo->getQuery(true)
					->update($dbo->qn('#__vikappointments_reservation'))
					->set($dbo->qn('sms_notified_' . $this->id()) . ' = 1')
					->where($dbo->qn('id') . ' = ' . $r['id']);

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getAffectedRows())
				{
					if ($response->hasContent())
					{
						$response->appendContent("\n---------------\n");
					}

					$response->appendContent("SMS Cron Notification {$r['id']}-{$r['sid']} : {$r['purchaser_nominative']}");
				}
			}
			else
			{
				if ($response->hasContent())
				{
					$response->appendContent("\n---------------\n");
				}

				// if error : response will be false
				$response->appendContent("SMS Cron Notification Error!\n{$r['id']}-{$r['sid']} : {$r['purchaser_prefix']} {$r['purchaser_phone']}\n" . strip_tags($api->getLog()))
					->setStatus(false)
					->setNotify(true);
			}
		}

		return $response;
	}

	/**
	 * @override
	 * Returns the fields of the configuration in an array.
	 *
	 * @return 	array 	The CronFormField list used for the configuration. 
	 */
	public static function getConfiguration()
	{
		$fields = array();

		// SMS IN ADVANCE

		$f = new CronFormField('sms_in_advance', 'Send SMS in Advance');
		$f->isInputNumber()
			->setSecondaryLabel('min.')
			->setDefaultValue(60)
			->addConstraint('min', 1);

		$fields[] = $f;

		// EXAMPLE TOOLBAR

		$f = new CronFormField('custom_2', '');
		$f->setDefaultValue('These are all the possible accepted tags: {checkin}, {checkin_time}, {service}, {employee}, {company}, {customer}, {location}, {location_short}, {location_name}')
			->setRequired(false);

		$fields[] = $f;
		
		// SMS CONTENT
		
		foreach (VikAppointments::getKnownLanguages() as $lang)
		{	
			// put a sms content for each language
			
			$f = new CronFormField('sms_content_' . substr($lang, 0, 2), 'SMS Content ' . $lang);
			$f->isTextarea()
				->setDefaultValue('We would like to remind you the appointment {service} for {checkin} at {company}.')
				->addConstraint('style', 'width:80%;height:100px;');
	
			$fields[] = $f;
		}

		return $fields;
	}

	/**
	 * @override
	 * This function is called only once during the installation of the cron job.
	 * Returns true on success, otherwise false.
	 *
	 * @return  boolean	 The status of the installation.
	 */
	public function install()
	{
		$dbo = JFactory::getDbo();

		$id = $this->id();

		try
		{
			$q = "ALTER TABLE `#__vikappointments_reservation` ADD COLUMN `sms_notified_{$id}` tinyint(1) DEFAULT 0";

			$dbo->setQuery($q);
			$dbo->execute();
		}
		catch (Exception $e)
		{
			// impossible to perform the query, return false
			return false;
		}

		return true;
	}

	/**
	 * @override
	 * This function is called only once during the uninstallation of the cron job.
	 * Returns true on success, otherwise false.
	 *
	 * @return 	boolean  The status of the uninstallation.
	 */
	public function uninstall()
	{
		$dbo = JFactory::getDbo();

		$id = $this->id();

		try
		{
			$q = "ALTER TABLE `#__vikappointments_reservation` DROP COLUMN `sms_notified_{$id}`";

			$dbo->setQuery($q);
			$dbo->execute();
		}
		catch (Exception $e)
		{
			// Something went wrong while trying to uninstall the cron job.
			// We don't need to worry about it at all.
		}

		return true;
	}

	/**
	 * @override
	 * Returns the title of the cron job.
	 *
	 * @return 	string 	The title of the cron job.
	 */
	public static function title()
	{
		return "Appointment Reminder (SMS)";
	}
}
