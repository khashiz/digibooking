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
class VikAppointmentsViewcoupongroups extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		AppointmentsHelper::load_css_js();

		$app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();
		
		$ordering = OrderingManager::getColumnToOrder('coupongroups', 'ordering', 1);
		
		// Set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['keysearch'] = $app->getUserStateFromRequest('vapcoupongroups.keysearch', 'keysearch', '', 'string');

		//db object
		$lim  	= $app->getUserStateFromRequest('com_vikappointments.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut = "";

		$rows = array();

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS `g`.*, COUNT(1) AS `count`')
			->select($dbo->qn('c.id', 'coupon_id'))
			->from($dbo->qn('#__vikappointments_coupon_group', 'g'))
			->leftjoin($dbo->qn('#__vikappointments_coupon', 'c') . ' ON ' . $dbo->qn('g.id') . ' = ' . $dbo->qn('c.id_group'))
			->group($dbo->qn('g.id'))
			->order($ordering['column'] . ' ' . ($ordering['type'] == 2 ? 'DESC' : 'ASC'));

		if (strlen($filters['keysearch']))
		{
			$q->where($dbo->qn('g.name') . ' LIKE ' . $dbo->q("%{$filters['keysearch']}%"));
		}
		
		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim);

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = "<table align=\"center\"><tr><td>" . $pageNav->getListFooter() . "</td></tr></table>";
		}
		
		$new_type = OrderingManager::getSwitchColumnType('coupongroups', $ordering['column'], $ordering['type'], array(1, 2));
		$ordering = array($ordering['column'] => $new_type);

		// get ordering bounds

		$q = $dbo->getQuery(true)
			->select('MIN(`ordering`) AS `min`, MAX(`ordering`) AS `max`')
			->from($dbo->qn('#__vikappointments_coupon_group'));

		$dbo->setQuery($q);
		$dbo->execute();
		$bounds = $dbo->loadAssoc();
		
		$this->rows 	= &$rows;
		$this->navbut 	= &$navbut;
		$this->ordering = &$ordering;
		$this->filters 	= &$filters;
		$this->bounds 	= &$bounds;
		
		// Display the template (default.php)
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
		JToolBarHelper::title(JText::_('VAPMAINTITLEVIEWCOUPONGROUPS'), 'vikappointments');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikappointments'))
		{
			JToolBarHelper::addNew('newcoupongroup', JText::_('VAPNEW'));
			JToolBarHelper::divider();	
		}

		if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
		{
			JToolBarHelper::editList('editcoupongroup', JText::_('VAPEDIT'));
			JToolBarHelper::spacer();
		}
		
		if (JFactory::getUser()->authorise('core.delete', 'com_vikappointments'))
		{
			JToolBarHelper::deleteList(VikAppointments::getConfirmSystemMessage(), 'deleteCouponGroups', JText::_('VAPDELETE'));
		}

		JToolBarHelper::cancel('cancelCoupon', JText::_('VAPCANCEL'));
	}
}
