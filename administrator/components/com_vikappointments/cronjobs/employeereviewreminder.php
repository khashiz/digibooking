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
 * The cron job is used to send an e-mail to the customers
 * to ask them if they want to leave a review for the employee.
 *
 * @since   1.5
 * @see     CronJob
 */
class CronJobEmployeeReviewReminder extends CronJob
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
		$notify_after = $this->get('notify_after') * 86400; // get time in seconds

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
				$dbo->qn('r.view_emp') . ' = 1',
				$dbo->qn('r.employee_review_notified_' . $this->id()) . ' = 0',
				$dbo->qn('r.purchaser_mail') . ' <> ""',
				'(' . $dbo->qn('r.checkin_ts') . ' + ' . $notify_after . ') <= ' . $now, 
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

		$config = UIFactory::getConfig();

		// get settings
		$date_format    = $config->get('dateformat');
		$time_format    = $config->get('timeformat');
		$company_name   = $config->get('agencyname');
		$sender_mail    = $config->get('senderemail');
		$def_lang_tag   = VikAppointments::getDefaultLanguage();

		$vik = UIApplication::getInstance();

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

			// mail subject
			$mail_subject = $this->get('mail_subject_' . strtolower(substr($r['langtag'], 0, 2)));

			$url = "index.php?option=com_vikappointments&view=employeesearch&id_employee=" . $r['id_employee'];
			$url = $vik->routeForExternalUse($url);

			// render message
			$mail_text = $this->get('mail_content_' . strtolower(substr($r['langtag'], 0, 2)));
			$mail_text = str_replace('{checkin}', $r['checkin'], $mail_text);
			$mail_text = str_replace('{checkin_time}', $r['checkin_time'], $mail_text);
			$mail_text = str_replace('{service}', $r['sname'], $mail_text);
			$mail_text = str_replace('{employee}', $r['nickname'], $mail_text);
			$mail_text = str_replace('{company}', $company_name, $mail_text);
			$mail_text = str_replace('{customer}', $r['purchaser_nominative'], $mail_text);
			$mail_text = str_replace('{employee_url}', $url, $mail_text);

			// send e-mail notification
			$vik->sendMail($sender_mail, $company_name, $r['purchaser_mail'], $sender_mail, $mail_subject, $mail_text, array(), true);

			// update database order

			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_reservation'))
				->set($dbo->qn('employee_review_notified_' . $this->id()) . ' = 1')
				->where($dbo->qn('id') . ' = ' . $r['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getAffectedRows())
			{
				if ($response->hasContent())
				{
					$response->appendContent("\n---------------\n");
				}

				$identifier = strlen($r['purchaser_nominative']) ? $r['purchaser_nominative'] : $r['purchaser_mail'];
				$response->appendContent("Employee Review Cron Notification {$r['id']}-{$r['sid']} : {$identifier}");
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
	public static function getConfiguration() {

		$fields = array();

		// NOTIFY AFTER

		$f = new CronFormField('notify_after', 'Send E-Mail Notification After');
		$f->isInputNumber()
			->setSecondaryLabel('days')
			->setDefaultValue('2')
			->addConstraint('min', 1);

		$fields[] = $f;

		// EXAMPLE TOOLBAR

		$f = new CronFormField('custom_2', '');
		$f->isHtml()
			->setDefaultValue('These are all the possible accepted tags: {checkin}, {checkin_time}, {service}, {employee}, {company}, {customer}, {employee_url}')
			->setRequired(false);

		$fields[] = $f;
		
		// MAIL CONTENT
		
		foreach (VikAppointments::getKnownLanguages() as $lang)
		{
			// put a mail subject for each language

			$f = new CronFormField('mail_subject_' . substr($lang, 0, 2), 'E-Mail Subject ' . $lang);
			$f->isInputText()
				->setDefaultValue('e4j.com - Employee Review Reminder')
				->addConstraint('size', 64);
	
			$fields[] = $f;
			
			// put a mail content for each language
			
			$f = new CronFormField('mail_content_' . substr($lang, 0, 2), 'E-Mail Content ' . $lang);
			$f->isTextarea()
				->setDefaultValue('Please, post a review for the employee {employee} from the following link {employee_url}.')
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
			$q = "ALTER TABLE `#__vikappointments_reservation` ADD COLUMN `employee_review_notified_{$id}` tinyint(1) DEFAULT 0";

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
			$q = "ALTER TABLE `#__vikappointments_reservation` DROP COLUMN `employee_review_notified_{$id}`";

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
		return "Employee Review Reminder (E-Mail)";
	}
}
