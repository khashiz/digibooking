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
class VikAppointmentsViewreportspack extends JViewUI
{
	/**
	 * VikAppointments view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		AppointmentsHelper::load_css_js();
		VikAppointments::load_charts();
		VikAppointments::load_currency_js();
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		// Set the toolbar
		$this->addToolbar();

		// get groups
		
		$groups = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'title')))
			->from($dbo->qn('#__vikappointments_package_group'));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$groups = $dbo->loadAssocList();
		}
		
		// get selected groups
		$sel_groups = $input->get('groups', array(), 'array');
		
		// get range dates
		$start_range 	= $input->getString('startrange');
		$end_range 		= $input->getString('endrange');

		list($start_range, $end_range) = $this->getRangeDates($start_range, $end_range);

		// get Y-AXIS value type
		$value_type = $input->get('valuetype');

		// get line chart array
		
		$line_chart_stat = array();

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('o.id', 'id_order'),
				$dbo->qn('g.id', 'id_group'),
				$dbo->qn('g.title', 'name'),
				'FROM_UNIXTIME(' . $dbo->qn('o.createdon') . ', "%Y-%c") AS ' . $dbo->qn('month'),
				'SUM(' . $dbo->qn('i.price') . ') AS ' . $dbo->qn('earning'),
				'COUNT(1) AS ' . $dbo->qn('ordcount'),
			))
			->from($dbo->qn('#__vikappointments_package_order', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_package_order_item', 'i') . ' ON ' . $dbo->qn('i.id_order') . ' = ' . $dbo->qn('o.id'))
			->leftjoin($dbo->qn('#__vikappointments_package', 'p') . ' ON ' . $dbo->qn('i.id_package') . ' = ' . $dbo->qn('p.id'))
			->leftjoin($dbo->qn('#__vikappointments_package_group', 'g') . ' ON ' . $dbo->qn('p.id_group') . ' = ' . $dbo->qn('g.id'))
			->where(array(
				$dbo->qn('o.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn('o.createdon') . ' BETWEEN ' . $start_range . ' AND ' . $end_range,
			))
			->group($dbo->qn(array('o.id', 'p.id_group', 'month')))
			->order(array(
				$dbo->qn('g.ordering') . ' ASC',
				$dbo->qn('o.createdon') . ' ASC',
			));

		if (count($sel_groups) > 0 && count($sel_groups) < count($groups))
		{
			$q->where($dbo->qn('g.id') . ' IN (' . implode(',', $sel_groups) . ')');
		}

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$line_chart_stat = $dbo->loadAssocList();
		}

		// get pie chart array
		
		$pie_chart_stat = array();

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('g.id', 'id_group'),
				$dbo->qn('p.id', 'id_package'),
				$dbo->qn('g.title', 'group_name'),
				$dbo->qn('p.name', 'name'),
				'SUM(' . $dbo->qn('i.price') . ') AS ' . $dbo->qn('earning'),
				'COUNT(1) AS ' . $dbo->qn('ordcount'),
			))
			->from($dbo->qn('#__vikappointments_package_order', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_package_order_item', 'i') . ' ON ' . $dbo->qn('i.id_order') . ' = ' . $dbo->qn('o.id'))
			->leftjoin($dbo->qn('#__vikappointments_package', 'p') . ' ON ' . $dbo->qn('i.id_package') . ' = ' . $dbo->qn('p.id'))
			->leftjoin($dbo->qn('#__vikappointments_package_group', 'g') . ' ON ' . $dbo->qn('p.id_group') . ' = ' . $dbo->qn('g.id'))
			->where(array(
				$dbo->qn('o.status') . ' = ' . $dbo->q('CONFIRMED'),
				$dbo->qn('o.createdon') . ' BETWEEN ' . $start_range . ' AND ' . $end_range,
			))
			->group($dbo->qn(array('g.id', 'p.id')))
			->order(array(
				$dbo->qn('g.ordering') . ' ASC',
				$dbo->qn('p.ordering') . ' ASC',
			));

		if (count($sel_groups) > 0 && count($sel_groups) < count($groups))
		{
			$q->where($dbo->qn('g.id') . ' IN (' . implode(',', $sel_groups) . ')');
		}
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			foreach($dbo->loadAssocList() as $row)
			{
				if (!isset($pie_chart_stat[$row['id_group']]))
				{
					$pie_chart_stat[$row['id_group']] = array(
						'name'    => $row['group_name'] ? $row['group_name'] : JText::_('VAPUNCATEGORIZED'),
						'earning' => 0,
						'count'   => 0,
						'list'    => array(),
					);
				}

				$pie_chart_stat[$row['id_group']]['list'][] = $row;

				$pie_chart_stat[$row['id_group']]['earning'] += $row['earning'];
				$pie_chart_stat[$row['id_group']]['count']   += $row['ordcount'];
			}
		}

		$this->groups 		  = &$groups;
		$this->lineChartStat  = &$line_chart_stat;
		$this->pieChartStat   = &$pie_chart_stat;
		$this->startRange 	  = &$start_range;
		$this->endRange 	  = &$end_range;
		$this->valueType 	  = &$value_type;
		$this->selectedGroups = &$sel_groups;
		
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
		JToolBarHelper::title(JText::_('VAPREPORTSPACKTITLE'), 'vikappointments');
		
		JToolBarHelper::cancel('cancelPackageOrder', JText::_('VAPCANCEL'));
	}
	
	/**
	 * Returns the proper range dates.
	 *
	 * @param 	string 	$start_range 	The starting date.
	 * @param 	string 	$end_range 		The ending date.
	 *
	 * @return 	array 	A list containing the resulting starting (at [0] index) 
	 * 					and ending (at [1] index) range.
	 */
	private function getRangeDates($start_range, $end_range)
	{	
		if (!empty($start_range))
		{
			$start_range = VikAppointments::createTimestamp($start_range, 0, 0);
			
			if ($start_range == -1)
			{
				$start_range = '';
			}
		}
		
		if (!empty($end_range))
		{
			$end_range = VikAppointments::createTimestamp($end_range, 23, 59);
			
			if ($end_range == -1)
			{
				$end_range = '';
			}
		}

		// make sure the start range is lower than the end range and that the selected period is not higher than 3 years
		if (intval($start_range) > intval($end_range) || intval($end_range) - intval($start_range) > 86400 * 366 * 3)
		{
			$start_range = $end_range = '';
		}
		
		if (empty($start_range) || empty($end_range))
		{
			$now = getdate();

			$start_range 	= mktime(0, 0, 0, $now['mon'] - 2, 1, $now['year']);
			$end_range 		= mktime(0, 0, 0, $now['mon'] + 4, 1, $now['year']) - 1;
		}
		
		return array($start_range, $end_range);
	}
}
