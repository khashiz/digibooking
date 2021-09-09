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

UILoader::import('libraries.cron.dispatcher');
UILoader::import('libraries.cron.job');
UILoader::import('libraries.cron.response');
UILoader::import('libraries.cron.formfield');
UILoader::import('libraries.cron.formfieldconstraints');
UILoader::import('libraries.cron.formbuilder');
