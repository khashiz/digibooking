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
 * The cron job is used to send an e-mail to the employees
 * to remind them to renew their subscription.
 *
 * @since   1.5
 * @see     CronJob
 */
class CronJobEmployeeSubscriptionReminder extends CronJob
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
		$notify_before = $this->get('notify_before') * 86400; // get time in seconds

		// init cron response
		$response = new CronJobResponse();
		// if no error response will be true
		$response->setStatus(true);

		// reset all employees notification for not upcoming expiring subscriptions

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_employee'))
			->set($dbo->qn('employee_subscr_notified_' . $this->id()) . ' = 0')
			->where(array(
				$dbo->qn('active_to') . ' < ' . $now,
				$dbo->qn('active_to') . ' > ' . ($now + $notify_before),
			), 'OR');

		$dbo->setQuery($q);
		$dbo->execute();

		// retrieve employees to notify

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'nickname', 'email', 'phone', 'active_to')))
			->from($dbo->qn('#__vikappointments_employee'))
			->where(array(
				$dbo->qn('employee_subscr_notified_' . $this->id()) . ' = 0',
				$dbo->qn('email') . ' <> ""',
				$dbo->qn('active_to') . ' BETWEEN ' . $now . ' AND ' . ($now + $notify_before),
			))
			->order($dbo->qn('active_to') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			// no employees retrieved : stop process [OK]
			return $response->setContent('Process terminated correctly! No employee to notify.');
		}

		$rows = $dbo->loadAssocList();

		$config = UIFactory::getConfig();

		// get settings
		$date_format    = $config->get('dateformat');
		$time_format    = $config->get('timeformat');
		$company_name   = $config->get('agencyname');
		$sender_mail    = $config->get('senderemail');

		// foreach appointment > send mail notification
		foreach ($rows as $r)
		{
			$r['active_to'] = date($date_format . ' ' . $time_format, $r['active_to']);

			// mail subject
			$mail_subject = $this->get('mail_subject');

			// render message
			$mail_text = $this->get('mail_content');
			$mail_text = str_replace('{employee_name}', $r['nickname'], $mail_text);
			$mail_text = str_replace('{employee_mail}', $r['email'], $mail_text);
			$mail_text = str_replace('{employee_phone}', $r['phone'], $mail_text);
			$mail_text = str_replace('{active_until}', $r['active_to'], $mail_text);

			// send e-mail notification
			$vik = UIApplication::getInstance();
			$vik->sendMail($sender_mail, $company_name, $r['email'], $sender_mail, $mail_subject, $mail_text, array(), true);

			// update database order

			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_employee'))
				->set($dbo->qn('employee_subscr_notified_' . $this->id()) . ' = 1')
				->where($dbo->qn('id') . ' = ' . $r['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getAffectedRows())
			{
				if ($response->hasContent())
				{
					$response->appendContent("\n---------------\n");
				}

				$response->appendContent("Employee Subscription Cron Notification {$r['nickname']} : {$r['email']}");
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

		// NOTIFY AFTER

		$f = new CronFormField('notify_before', 'Send E-Mail Notification Before');
		$f->isInputNumber()
			->setSecondaryLabel('days')
			->setDefaultValue(7)
			->addConstraint('min', 1);

		$fields[] = $f;

		// EXAMPLE TOOLBAR

		$f = new CronFormField('custom_2', '');
		$f->isHtml()
			->setDefaultValue('These are all the possible accepted tags: {employee_name}, {employee_mail}, {employee_phone}, {active_until}')
			->setRequired(false);

		$fields[] = $f;
		
		// MAIL SUBJECT

		$f = new CronFormField('mail_subject', 'E-Mail Subject');
		$f->isInputText()
			->setDefaultValue('e4j.com - Employee Subscription Reminder')
			->addConstraint('size', 64);

		$fields[] = $f;
		
		// MAIL CONTENT
		
		$f = new CronFormField('mail_content', 'E-Mail Content');
		$f->isTextarea()
			->setDefaultValue('Hi {employee_name},<br /><br />your subscription is going to expire, please remember to renew your subscription plan or to purchase a new one within 7 days, otherwise you won\'t be listed on our website.')
			->addConstraint('style', 'width:80%;height:100px;');

		$fields[] = $f;

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
			$q = "ALTER TABLE `#__vikappointments_employee` ADD COLUMN `employee_subscr_notified_{$id}` tinyint(1) DEFAULT 0";

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
			$q = "ALTER TABLE `#__vikappointments_employee` DROP COLUMN `employee_subscr_notified_{$id}`";

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
		return "Employee Subscription Reminder (E-Mail)";
	}
}
