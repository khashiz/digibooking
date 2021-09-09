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
class VikAppointmentsViewmanagelangoption extends JViewUI
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
		
		$tag 		= $input->getString('tag', '');
		$id_option 	= $input->getUint('id_option', 0);
		
		$row = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_lang_option'))
			->where(array(
				$dbo->qn('tag') . ' = ' . $dbo->q($tag),
				$dbo->qn('id_option') . ' = ' . $id_option,
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$row = $dbo->loadAssoc();
		}

		$vars = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikappointments_option_value'))
			->where($dbo->qn('id_option') . ' = ' . $id_option)
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$vars = $dbo->loadAssocList();
		}

		// get default

		$default = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('o.name', 'o.description')))
			->select($dbo->qn(
				array('v.id', 'v.name'),
				array('var_id', 'var_name')
			))
			->from($dbo->qn('#__vikappointments_option', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('v.id_option') . ' = ' . $dbo->qn('o.id'))
			->where($dbo->qn('o.id') . ' = ' . $id_option)
			->order($dbo->qn('v.ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadAssocList() as $r)
			{
				if (empty($default))
				{
					$default = array(
						'name' 			=> $r['name'],
						'description' 	=> $r['description'],
						'variations'	=> array(),
					);
				}

				if (!empty($r['var_id']))
				{
					$default['variations'][] = array(
						'id' 	=> $r['var_id'],
						'name' 	=> $r['var_name'],
					);
				}
			}
		}
		
		$this->tag 		= &$tag;
		$this->idOption = &$id_option;
		$this->row 		= &$row;
		$this->vars 	= &$vars;
		$this->default 	= &$default;

		// Display the template
		parent::display($tpl);
	}
}
