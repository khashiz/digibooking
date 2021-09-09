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
class VikAppointmentsViewserviceslist extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		VikAppointments::load_fancybox();

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$user 	= JFactory::getUser();
		
		$itemid 	= $input->getInt('Itemid');
		$sel_group 	= $input->getUint('service_group');
		
		$groups = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `s`.*');
		$q->select(array(
			$dbo->qn('g.id', 'gid'),
			$dbo->qn('g.name', 'gname'),
			$dbo->qn('g.description', 'gdesc'),
		));
		$q->select('(' . $this->getRatingQuery($dbo) . ') AS ' . $dbo->qn('rating_avg'));
		$q->select('(' . $this->getReviewsQuery($dbo) . ') AS ' . $dbo->qn('reviews_count'));

		$q->from($dbo->qn('#__vikappointments_service', 's'));
		$q->leftjoin($dbo->qn('#__vikappointments_group', 'g') . ' ON ' . $dbo->qn('s.id_group') . ' = ' . $dbo->qn('g.id'));

		// get only the published services
		$q->where($dbo->qn('s.published') . ' = 1');

		/**
		 * Retrieve only the services the belong to the view
		 * access level of the current user.
		 *
		 * @since 1.6
		 */
		$levels = $user->getAuthorisedViewLevels();

		if ($levels)
		{
			$q->where($dbo->qn('s.level') . ' IN (' . implode(', ', $levels) . ')');
		}

		if ($sel_group > 0)
		{
			// retrieve only the services that belong to the specified group
			$q->where($dbo->qn('g.id') . ' = ' . $sel_group);
		}

		// get the services that don't own a publishing restriction
		// or at least they are not yet expired.
		$q->andWhere(array(
			$dbo->qn('s.end_publishing') . ' <= 0',
			$dbo->qn('s.end_publishing') . ' >= ' . time(),
		), 'OR');

		$q->order(array(
			$dbo->qn('g.ordering') . ' ASC',
			$dbo->qn('s.ordering') . ' ASC',
			$dbo->qn('s.name') . ' ASC',
		));
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $this->groupServices($dbo->loadAssocList());
		}

		$langtag = JFactory::getLanguage()->getTag();
		
		$lang_groups   = VikAppointments::getTranslatedGroups('', $langtag, $dbo);
		$lang_services = VikAppointments::getTranslatedServices('', $langtag, $dbo);
		
		$listing_details = VikAppointments::getServicesListingDetails();
		
		$this->groups 		= &$groups;
		$this->itemid 		= &$itemid;
		$this->descLength 	= &$listing_details['desclength'];
		$this->linkHref 	= &$listing_details['linkhref'];
		$this->langGroups 	= &$lang_groups;
		$this->langServices = &$lang_services;

		// prepare page content
		VikAppointments::prepareContent($this);
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Returns the inner query that should be used to calculate the
	 * average rating of the services.
	 *
	 * @param 	mixed 	$dbo 	The database object.
	 *
	 * @return 	mixed 	The database query.
	 *
	 * @since 	1.6
	 */
	protected function getRatingQuery($dbo)
	{
		return $dbo->getQuery(true)
			->select('AVG(' . $dbo->qn('re.rating') . ')')
			->from($dbo->qn('#__vikappointments_reviews', 're'))
			->where(array(
				$dbo->qn('s.id') . ' = ' . $dbo->qn('re.id_service'),
				$dbo->qn('re.published') . ' = 1',
			));
	}

	/**
	 * Returns the inner query that should be used to calculate the
	 * number of reviews of the services.
	 *
	 * @param 	mixed 	$dbo 	The database object.
	 *
	 * @return 	mixed 	The database query.
	 *
	 * @since 	1.6
	 */
	protected function getReviewsQuery($dbo)
	{
		return $dbo->getQuery(true)
			->select('COUNT(' . $dbo->qn('re.rating') . ')')
			->from($dbo->qn('#__vikappointments_reviews', 're'))
			->where(array(
				$dbo->qn('s.id') . ' = ' . $dbo->qn('re.id_service'),
				$dbo->qn('re.published') . ' = 1',
			));
	}

	/**
	 * Groups the list of services within parent blocks.
	 * The resulting list will be an array of groups, which
	 * contain the list of children services.
	 *
	 * The services with no group will be placed at the end
	 * of the list, within an empty group.
	 *
	 * @param 	array 	$services 	The list of services to group.
	 *
	 * @return 	array 	The grouped list.
	 *
	 * @since 	1.6
	 */
	protected function groupServices(array $services)
	{
		$groups = array();

		foreach ($services as $s)
		{
			// if the service doesn't belong to a group,
			// the ID will be equals to 0 (as it is casted as INT).
			$id_group = (int) $s['gid'];

			if (!isset($groups[$id_group]))
			{
				$groups[$id_group] = array(
					'name' 			=> $s['gname'],
					'description' 	=> $s['gdesc'],
					'services' 		=> array(),
				);
			}

			$groups[$id_group]['services'][] = $s;
		}

		// check if there is the "uncategorized" group
		if (isset($groups[0]))
		{
			// get the group containing the services with no group
			$uncategorized = $groups[0];
			// unset that group
			unset($groups[0]);
			// move that group at the end of the list
			$groups[0] = $uncategorized;
		}

		return $groups;
	}
}
