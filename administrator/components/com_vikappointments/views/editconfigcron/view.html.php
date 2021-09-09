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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * VikAppointments View
 */
class VikAppointmentsVieweditconfigcron extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$dbo = JFactory::getDbo();

		AppointmentsHelper::load_css_js();
		VikAppointments::load_complex_select();
		
		// Set the toolbar
		$this->addToolBar();
		
		// get config
		
		$params = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('param', 'setting')))
			->from($dbo->qn('#__vikappointments_config'));
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadAssocList() as $r)
			{
				$params[$r['param']] = $r['setting'];
			}
		}

		// get cron jobs

		$cron_jobs = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name', 'published')))
			->from($dbo->qn('#__vikappointments_cronjob'))
			->order($dbo->qn('published') . ' DESC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$cron_jobs = array();
			
			foreach ($dbo->loadAssocList() as $c)
			{
				$key = $c['published'] ? 'JPUBLISHED' : 'JUNPUBLISHED';

				$cron_jobs[$key][] = $c;
			}
		}
		
		$this->params 	= &$params;
		$this->cronJobs = &$cron_jobs;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar()
	{
		// Add menu title and some buttons to the page
		JToolBarHelper::title(JText::_('VAPMAINTITLECONFIG'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveConfigurationCron', JText::_('VAPSAVE'));
			JToolBarHelper::divider();
		}
	
		JToolBarHelper::cancel('dashboard', JText::_('VAPCANCEL'));
	}
}
