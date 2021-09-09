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
class VikAppointmentsViewmedia extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;

		$search = $input->getString('keysearch', '');

		// Set the toolbar
		$this->addToolBar();
		
		$all_img = AppointmentsHelper::getAllMedia(true);

		if (!empty($search))
		{
			$app = array();
			foreach ($all_img as $img)
			{
				$file_name = basename($img);

				if (strpos($file_name, $search) !== false)
				{
					$app[] = $img;
				}
			}
			$all_img = $app;
			unset($app);
		}
		
		$limit = 15;
		$max_limit = count($all_img);
		if ($max_limit > $limit)
		{
			$all_img = array_slice($all_img, 0, $limit);
		}
		
		$loadedAll = $max_limit <= $limit;
		
		$this->imgFiles 	= &$all_img;
		$this->keyFilter 	= &$search;
		$this->loadedAll 	= &$loadedAll;
		$this->mediaLimit 	= &$limit;
		$this->maxLimit 	= &$max_limit;
		
		// Display the template (default.php)
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWMEDIA'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newmedia', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::custom('analyzemedia', 'bars', 'bars', JText::_('VAPANALYZE'), true, false);
			JToolBarHelper::divider();  
		}

		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteMedia', JText::_('VAPDELETE'));
		}
	}
}
