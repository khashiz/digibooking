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
class VikAppointmentsViewmanageconversion extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_font_awesome();
		VikAppointments::load_complex_select();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$type 	= $input->getString('type');

		// Set the toolbar
		$this->addToolBar($type);
		
		$conversion = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_conversion'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$conversion = $dbo->loadAssoc();
			}
		}

		if (empty($conversion))
		{
			$conversion = $this->getBlankItem();
		}
		else
		{
			$conversion['statuses'] = (array) json_decode($conversion['statuses']);
		}

		UILoader::import('libraries.models.conversion');
		$pages = VAPConversion::getSupportedPages();
		$types = VAPConversion::getSupportedTypes();
		
		$this->conversion 	= &$conversion;
		$this->pages 		= &$pages;
		$this->types 		= &$types;
		
		// Display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Returns a blank item.
	 *
	 * @return 	array 	A blank item for new requests.
	 */
	protected function getBlankItem()
	{
		return array(
			'id'			=> -1,
			'title' 		=> '',
			'jsfile'		=> '',
			'snippet'		=> '',
			'published' 	=> 0,
			'statuses'		=> array(),
			'page'			=> 'order',
			'type'			=> 'reservation',	
		);
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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITCONVERSION'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWCONVERSION'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveConversion', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseConversion', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::save2new('saveAndNewConversion', JText::_('VAPSAVEANDNEW'));
			JToolBarHelper::save2copy('saveAsCopyConversion');
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelConversion', JText::_('VAPCANCEL'));
	}
}
