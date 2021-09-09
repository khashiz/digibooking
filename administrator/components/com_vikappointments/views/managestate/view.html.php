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
class VikAppointmentsViewmanagestate extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		AppointmentsHelper::load_css_js();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$type = $input->getString('type');

		$country = $input->getUint('country', 0);

		$q = $dbo->getQuery(true)
			->select($dbo->qn('country_name'))
			->from($dbo->qn('#__vikappointments_countries'))
			->where($dbo->qn('id') . ' = ' . $country);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			$app->redirect('index.php?option=com_vikappointments&task=countries');
			exit;
		}
		
		// Set the toolbar
		$this->addToolBar($type, $dbo->loadResult());
		
		$state = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_states'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$state = $dbo->loadAssoc();
			}
		}

		if (empty($state))
		{
			$state = $this->getBlankItem();
		}
		
		$this->state 	= &$state;
		$this->country 	= &$country;

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar($type, $country_name)
	{
		// Add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolBarHelper::title(JText::sprintf('VAPMAINTITLEEDITSTATE', $country_name), 'vikappointments');
		} else {
			JToolBarHelper::title(JText::sprintf('VAPMAINTITLENEWSTATE', $country_name), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveState', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseState', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewState', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelState', JText::_('VAPCANCEL'));
	}

	/**
	 * Returns a blank item.
	 *
	 * @return 	array 	 A blank item for new requests.
	 */
	protected function getBlankItem()
	{
		return array(
			'id'			=> -1,
			'state_name' 	=> '',
			'state_2_code' 	=> '',
			'state_3_code' 	=> '',
			'published' 	=> 0,
		);
	}
}
