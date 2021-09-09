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
class VikAppointmentsViewmakerecurrence extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		AppointmentsHelper::load_css_js();
		VikAppointments::load_complex_select();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$this->addToolBar();
		
		$ids = $input->getUint('cid', array(0));

		$q = $dbo->getQuery(true)
			->select($dbo->qn('sid'))
			->from($dbo->qn('#__vikappointments_reservation'))
			->where(array(
				$dbo->qn('id') . ' = ' . $ids[0],
				$dbo->qn('id_parent') . ' <> -1',
				$dbo->qn('closure') . ' = 0',
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if ($dbo->getNumRows() == 0)
		{
			$app->redirect('index.php?option=com_vikappointments&task=reservations');
			exit;
		}

		$order = VikAppointments::fetchOrderDetails($ids[0], $dbo->loadResult(), VikAppointments::getDefaultLanguage('site'));
		
		if ($order === null)
		{
			$app->redirect('index.php?option=com_vikappointments&task=reservations');
			exit;
		}

		$this->order = $order[0];

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
		JToolBarHelper::title(JText::_('VAPMAINTITLEMAKERECURRENCE'), 'vikappointments');
		
		JToolBarHelper::cancel('cancelReservation', JText::_('VAPCANCEL'));
	}
}
