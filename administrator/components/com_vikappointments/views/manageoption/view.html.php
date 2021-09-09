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
class VikAppointmentsViewmanageoption extends JViewUI
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

		$dbo   = JFactory::getDbo();
		$app   = JFactory::getApplication();
		$input = $app->input;
		
		$type = $input->getString('type', 'new');
		$tab  = $app->getUserStateFromRequest('vapsaveopt.tab', 'tabname', 'option_details', 'string');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$option 		= array();
		$variations 	= array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));

			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_option'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$option = $dbo->loadAssoc();

				$q = $dbo->getQuery(true);

				$q->select('*')
					->from($dbo->qn('#__vikappointments_option_value'))
					->where($dbo->qn('id_option') . ' = ' . $option['id'])
					->order($dbo->qn('ordering') . ' ASC');

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$variations = $dbo->loadAssocList();
				}
			}
		}

		if (empty($option))
		{
			$option = $this->getBlankItem();
		}
		
		$this->option 		= &$option;
		$this->variations 	= &$variations;
		$this->tab 			= &$tab;

		// Display the template
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
			'name' 			=> '',
			'description' 	=> '',
			'price' 		=> 0.0,
			'image' 		=> '',
			'published' 	=> 1,
			'single' 		=> 0,
			'maxq' 			=> 1,
			'required' 		=> 0,
			'displaymode' 	=> 1,
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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITOPTION'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWOPTION'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveOption', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseOption', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewOption', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelOption', JText::_('VAPCANCEL'));
	}
}
