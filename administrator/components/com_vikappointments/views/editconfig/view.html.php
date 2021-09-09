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
class VikAppointmentsVieweditconfig extends JViewUI
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
		
		$dbo = JFactory::getDbo();

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

		// get custom fields

		$mask = CF_EXCLUDE_REQUIRED_CHECKBOX | CF_EXCLUDE_SEPARATOR;
		$custom_fields = VAPCustomFields::getList(0, 0, 0, $mask);

		// get groups
		
		$groups = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_group'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}

		// load e-mail templates

		$all_tmpl_files = glob(VAPBASE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mail_tmpls' . DIRECTORY_SEPARATOR . '*.php');

		$templates = array();
		foreach ($all_tmpl_files as $file)
		{
			$file = basename($file);
			$templates[] = JHtml::_('select.option', $file, $file);
		}
		
		$this->params 		= &$params;
		$this->customFields = &$custom_fields;
		$this->groups 		= &$groups;
		$this->templates 	= &$templates;

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
			JToolBarHelper::apply('saveConfiguration', JText::_('VAPSAVE'));
			JToolBarHelper::divider();
		}
	
		JToolBarHelper::cancel('dashboard', JText::_('VAPCANCEL'));
	}
}
