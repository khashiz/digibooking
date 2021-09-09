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
class VikAppointmentsViewmanagerate extends JViewUI
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

		$type = $input->getString('type');
		$tab  = $app->getUserStateFromRequest('vapsaverate.tab', 'tabname', 'rate_options', 'string');

		// Set the toolbar
		$this->addToolBar($type);
		
		$rate = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_special_rates'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$rate = $dbo->loadAssoc();

				/**
				 * Extract params from loaded rate.
				 *
				 * @since 1.6.2
				 */
				$rate['params'] = (array) json_decode($rate['params'], true);
			}
		}

		if (empty($rate))
		{
			$rate = $this->getBlankItem();
		}
		else
		{
			$rate['weekdays'] 	= strlen($rate['weekdays']) 	? explode(',', $rate['weekdays']) 	: array();
			$rate['usergroups'] = strlen($rate['usergroups']) 	? explode(',', $rate['usergroups']) : array();

			// get assigned services
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_service'))
				->from($dbo->qn('#__vikappointments_ser_rates_assoc'))
				->where($dbo->qn('id_special_rate') . ' = ' . $rate['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$rate['services'] = $dbo->loadColumn();
			}
			else
			{
				$rate['services'] = array();
			}

			if ($rate['fromdate'] && $rate['todate'])
			{
				/**
				 * Convert the publishing date by using the configuration format.
				 *
				 * @since 1.6.1
				 */
				$format = UIFactory::getConfig()->get('dateformat');

				$rate['fromdate'] = JDate::getInstance($rate['fromdate'])->format($format);
				$rate['todate']   = JDate::getInstance($rate['todate'])->format($format);
			}
		}

		// get services

		$services = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('s.id', 's.name', 's.id_group')))
			->select($dbo->qn('g.name', 'group_name'))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('s.id_group') . ' = ' . $dbo->qn('g.id'))
			->order(array(
				$dbo->qn('g.ordering') . ' ASC',
				$dbo->qn('s.ordering') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$last_id_group = -2;

			foreach ($dbo->loadAssocList() as $s)
			{
				if ($last_id_group != $s['id_group'])
				{
					$services[] = array(
						'id' 	=> $s['id_group'],
						'name' 	=> $s['group_name'],
						'list' 	=> array(),
					);

					$last_id_group = $s['id_group'];
				}

				$services[count($services)-1]['list'][] = array(
					'id' 	=> $s['id'],
					'name' 	=> $s['name'],
				);
			}
		}
		
		$this->rate     = &$rate;
		$this->services = &$services;
		$this->tab      = &$tab;
		
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
			'name' 			=> '',
			'description'	=> '',
			'published' 	=> 0,
			'charge'		=> 0,
			'people'		=> 0,
			'weekdays'		=> array(),
			'fromtime' 		=> 0,
			'totime' 		=> 0,
			'fromdate' 		=> null,
			'todate' 		=> null,
			'params' 		=> array(),
			'usergroups' 	=> array(),
			'services'		=> array(),
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
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITSPECIALRATE'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWSPECIALRATE'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('saveSpecialRate', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndCloseSpecialRate', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::save2new('saveAndNewSpecialRate', JText::_('VAPSAVEANDNEW'));
			JToolBarHelper::save2copy('saveAsCopySpecialRate');
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::cancel('cancelSpecialRate', JText::_('VAPCANCEL'));
	}
}
