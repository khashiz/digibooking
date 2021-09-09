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
class VikAppointmentsViewmanagepackorder extends JViewUI
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
		$tab  = $app->getUserStateFromRequest('vapsavepackord.tab', 'tabname', 'packorder_details', 'string');
		
		// Set the toolbar
		$this->addToolBar($type);
		
		$order = array();
		$assoc = array();
		
		if ($type == 'edit')
		{
			$ids = $input->getUint('cid', array(0));
			
			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_package_order'))
				->where($dbo->qn('id') . ' = ' . $ids[0]);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$order = $dbo->loadAssoc();

				$q = $dbo->getQuery(true)
					->select('`a`.*')->select($dbo->qn('p.name'))
					->from($dbo->qn('#__vikappointments_package_order_item', 'a'))
					->leftjoin($dbo->qn('#__vikappointments_package', 'p') . ' ON ' . $dbo->qn('a.id_package') . ' = ' . $dbo->qn('p.id'))
					->where($dbo->qn('a.id_order') . ' = ' . $order['id']);
				
				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$assoc = $dbo->loadAssocList();
				}
			}
		}

		if (empty($order))
		{
			$order = $this->getBlankItem();
		}

		// get payments

		$payments = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name', 'charge')))
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('subscr') . ' = 1')
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$payments = $dbo->loadAssocList();
		}

		// get custom fields

		$custom_fields = VAPCustomFields::getList(0, 0, 0, CF_EXCLUDE_REQUIRED_CHECKBOX | CF_EXCLUDE_FILE);

		// get packages and groups

		$packages_groups = array();

		$q = $dbo->getQuery(true)
			->select('`p`.*')->select($dbo->qn('g.title', 'group_title'))
			->from($dbo->qn('#__vikappointments_package', 'p'))
			->leftjoin($dbo->qn('#__vikappointments_package_group', 'g') . ' ON ' . $dbo->qn('g.id') . ' = ' . $dbo->qn('p.id_group'))
			->order(array(
				$dbo->qn('g.ordering') . ' ASC',
				$dbo->qn('p.ordering') . ' ASC',
			));

		$dbo->setQuery($q);
		$dbo->execute();
		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			
			// push empty groups at the end of the list
			$i = 0;
			while ($rows[0]['id_group'] == -1 && $i < count($rows))
			{
				$rows[] = array_shift($rows);
				$i++;
			}

			$last_id = -1;
			foreach ($rows as $r)
			{
				if ($last_id != $r['id_group'])
				{
					$packages_groups[] = array(
						'id' 		=> $r['id_group'],
						'title' 	=> $r['group_title'],
						'packages' 	=> array(),
					);

					$last_id = $r['id_group'];
				}

				$packages_groups[count($packages_groups)-1]['packages'][] = $r;

			} 
		}

		$countries = VikAppointmentsLocations::getCountries('phone_prefix');
		
		$this->order 			= &$order;
		$this->payments 		= &$payments;
		$this->countries 		= &$countries;
		$this->custom_fields 	= &$custom_fields;
		$this->packagesGroups 	= &$packages_groups;
		$this->packagesAssoc 	= &$assoc;
		$this->tab 				= &$tab;

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
			'id'					=> -1,
			'id_payment' 			=> -1,
			'id_user' 				=> '',
			'total_cost' 			=> 0.0,
			'status' 				=> 'PENDING',
			'custom_f' 				=> '',
			'purchaser_nominative' 	=> '',
			'purchaser_mail' 		=> '',
			'purchaser_phone' 		=> '',
			'purchaser_prefix' 		=> '',
			'purchaser_country' 	=> '',
		);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void.
	 */
	protected function addToolBar($type)
	{
		// Add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLEEDITPACKORDER'), 'vikappointments');
		}
		else
		{
			JToolBarHelper::title(JText::_('VAPMAINTITLENEWPACKORDER'), 'vikappointments');
		}
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::apply('savePackageOrder', JText::_('VAPSAVE'));
			JToolBarHelper::save('saveAndClosePackageOrder', JText::_('VAPSAVEANDCLOSE'));
			JToolBarHelper::custom('saveAndNewPackageOrder', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::cancel('cancelPackageOrder', JText::_('VAPCANCEL'));
	}
}
