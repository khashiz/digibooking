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
class VikAppointmentsViewpackages extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		VikAppointments::load_font_awesome();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$user 	= JFactory::getUser();

		$itemid = $input->getInt('Itemid', 0);

		$id_package_group = $input->getUint('package_group', 0);
		
		$langtag = JFactory::getLanguage()->getTag();
		
		$now = time();

		$rows = array();
		$packages_groups = array();

		$q = $dbo->getQuery(true)
			->select('`p`.*')
			->select($dbo->qn('g.title', 'group_title'))
			->select($dbo->qn('g.description', 'group_description'));

		$q->from($dbo->qn('#__vikappointments_package', 'p'))
			->leftjoin($dbo->qn('#__vikappointments_package_group', 'g') . ' ON ' . $dbo->qn('p.id_group') . ' = ' . $dbo->qn('g.id'));

		$q->where($dbo->qn('p.published') . ' = 1');

		if ($id_package_group)
		{
			$q->where($dbo->qn('g.id') . ' = ' . $id_package_group);
		}

		$q->andWhere(array(
			$dbo->qn('p.start_ts') . ' = -1',
			$now . ' BETWEEN ' . $dbo->qn('p.start_ts') . ' AND ' . $dbo->qn('p.end_ts'),
		), 'OR');

		/**
		 * Retrieve only the packages the belong to the view
		 * access level of the current user.
		 *
		 * @since 1.6
		 */
		$levels = $user->getAuthorisedViewLevels();

		if ($levels)
		{
			$q->where($dbo->qn('p.level') . ' IN (' . implode(', ', $levels) . ')');
		}

		$q->order(array(
			$dbo->qn('g.ordering') . ' ASC',
			$dbo->qn('p.ordering') . ' ASC',
		));
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			
			$cont = 0;
			while ($rows[0]['id_group'] <= 0 && $cont < count($rows))
			{
				$rows[] = array_shift($rows);
				$cont++;
			}
		}

		$last_id = -1;
		foreach ($rows as $r)
		{
			if ($last_id != $r['id_group'])
			{
				$packages_groups[] = array(
					'id' 			=> $r['id_group'],
					'title' 		=> $r['group_title'],
					'description' 	=> $r['group_description'],
					'packages' 		=> array(),
				);

				$last_id = $r['id_group'];
			}

			$packages_groups[count($packages_groups) - 1]['packages'][] = $r;
		} 

		// CART
		VikAppointments::loadCartPackagesLibrary();
		
		$core = new VikAppointmentsCartPackagesCore();
		$cart = $core->getCartObject();
		// END CART
		
		$this->packagesGroups 	= &$packages_groups;
		$this->cart 			= &$cart;
		$this->itemid 			= &$itemid;

		$lang_packgroups = VikAppointments::getTranslatedPackGroups('', $langtag, $dbo);
		$lang_packages 	 = VikAppointments::getTranslatedPackages('', $langtag, $dbo);
		
		$this->langPackGroups 	= &$lang_packgroups;
		$this->langPackages 	= &$lang_packages;

		// prepare page content
		VikAppointments::prepareContent($this);
		
		// Display the template
		parent::display($tpl);
	}
}
