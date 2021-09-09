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
class VikAppointmentsViewmanagecronjob extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return void
	 */
	function display($tpl = null)
	{
		AppointmentsHelper::load_css_js();
		VikAppointments::load_complex_select();
		VikAppointments::load_font_awesome();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$type = $input->getString('type');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$cronjob = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));

			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_cronjob'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$cronjob = $dbo->loadAssoc();
			}
		}

		if (empty($cronjob))
		{
			$cronjob = $this->getBlankItem();
		}
		
		VikAppointments::loadCronLibrary();

		$cron_classes = array();

		foreach (CronDispatcher::includeAll() as $file => $cron)
		{
			$cron_classes[$file] = $cron::title();
		}

		asort($cron_classes);
		
		$this->cronjob 		= &$cronjob;
		$this->allCronFiles = &$cron_classes;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($type)
	{
		// Add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITCRONJOB'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWCRONJOB'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveCronjob', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseCronjob', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewCronjob', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelCronjob', JText::_('VAPCANCEL'));
	}

	/**
	 * Returns a blank item.
	 *
	 * @return 	array 	 A blank item for new requests.
	 */
	protected function getBlankItem()
	{
		return array(
			'id'		=> -1,
			'name' 		=> '',
			'class' 	=> '',
			'published' => 0,
		);
	}
}
