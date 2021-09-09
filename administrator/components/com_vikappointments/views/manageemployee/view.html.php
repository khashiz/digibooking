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
use Hekmatinasser\Verta\Verta;
/**
 * VikAppointments View
 */
class VikAppointmentsViewmanageemployee extends JViewUI
{
    /**
     * VikAppointments view display method
     *
     * @return 	void
     */
    function display($tpl = null)
    {
        VikAppointments::load_font_awesome();
        VikAppointments::load_complex_select();

        $dbo   = JFactory::getDbo();
        $app   = JFactory::getApplication();
        $input = $app->input;

        $type = $input->getString('type', 'new');
        $tab  = $app->getUserStateFromRequest('vapsaveemp.tab', 'tabname', 'employee_details', 'string');
        $date = $app->getUserStateFromRequest('vapsaveemp.day', 'dayfrom', null, 'string');
        $rule = $app->getUserStateFromRequest('vapsaveemp.rule', 'dayrule', null, 'string');

        /**
         * Since the working days of the year are stored in UTC format,
         * the timestamp used for the query should be forced to UTC offset,
         * so that also the working times of the current day are properly retrieved.
         *
         * @since 1.6.2
         */
        $tz = date_default_timezone_get();

        // set UTC timezone
        date_default_timezone_set('Asia/Tehran');

        /**
         * Fixed DateTime::__construct() error while fetching
         * dates with a non-standard format (e.g. dd/mm/YYYY).
         *
         * @since 1.6.1
         */
        if (is_null($date))
        {
            //$day = strtotime('today 00:00:00');
            $jgetdate = ArasJoomlaVikApp::jgetdate();
            $day = ArasJoomlaVikApp::jmktime(0,0,0,$jgetdate['mon'],$jgetdate['mday'],$jgetdate['year']);
        }
        else
        {
            $day = VikAppointments::jcreateTimestamp($date, 0, 0);
        }

        // set UTC timezone
        date_default_timezone_set('Asia/Tehran');

        if ($rule)
        {
            $day = strtotime($rule, $day);
        }

        // Set the toolbar
        $this->addToolBar($type);

        $employee = array();
        $worktime = array();

        $worktime_days = array();
        $worktime_date = array();

        if ($type == 'edit')
        {
            $ids = $input->getUint('cid', array(0));

            $q = $dbo->getQuery(true)
                ->select('*')
                ->from($dbo->qn('#__vikappointments_employee'))
                ->where($dbo->qn('id') . ' = ' . $ids[0]);

            $dbo->setQuery($q, 0, 1);
            $dbo->execute();

            if ($dbo->getNumRows())
            {
                $employee = $dbo->loadAssoc();
            }

            // set UTC timezone
            date_default_timezone_set('Asia/Tehran');

            /**
             * Fetch range timestamps without using JDate helper.
             *
             * @since 1.6.1
             */
            $range = array(
                $day,
                addDay($day,7),
                //strtotime('+1 week', $day),
            );

            // restore default timezone
            date_default_timezone_set($tz);

            $q = $dbo->getQuery(true)
                ->select('*')
                ->from($dbo->qn('#__vikappointments_emp_worktime'))
                ->where(array(
                    $dbo->qn('id_employee') . ' = ' . $ids[0],
                    $dbo->qn('id_service') . ' = -1',
                ))
                ->andWhere(array(
                    $dbo->qn('ts') . ' = -1',
                    $dbo->qn('ts') . ' BETWEEN ' . $range[0] . ' AND ' . $range[1],
                ))
                ->order(array(
                    $dbo->qn('day') . ' ASC',
                    $dbo->qn('ts') . ' ASC',
                    $dbo->qn('fromts') . ' ASC',
                    $dbo->qn('closed') . ' ASC',
                ));

            $dbo->setQuery($q);
            $dbo->execute();

            if ($dbo->getNumRows())
            {
                $worktime = $dbo->loadAssocList();
            }

            foreach ($worktime as $w)
            {
                // create shorten aliases
                $w['from']  = $w['fromts'];
                $w['to'] 	= $w['endts'];

                unset($w['fromts']);
                unset($w['endts']);

                if ($w['ts'] == -1)
                {
                    $worktime_days[] = $w;
                }
                else
                {
                    $worktime_date[] = $w;
                }
            }
        }

        if (empty($employee))
        {
            $employee = $this->getBlankItem();
        }

        // groups

        $groups = array();

        $q = $dbo->getQuery(true)
            ->select('*')
            ->from($dbo->qn('#__vikappointments_employee_group'))
            ->order($dbo->qn('ordering') . ' ASC');

        $dbo->setQuery($q);
        $dbo->execute();

        if ($dbo->getNumRows())
        {
            $groups = $dbo->loadAssocList();
        }

        // joomla users

        $jusers = array();

        $inner = $dbo->getQuery(true)
            ->select(1)
            ->from($dbo->qn('#__vikappointments_employee', 'a'))
            ->where($dbo->qn('a.jid') . ' = ' . $dbo->qn('u.id'));

        $q = $dbo->getQuery(true);

        $q->select($dbo->qn(array('u.id', 'u.name', 'u.username')))
            ->from($dbo->qn('#__users', 'u'))
            ->where($dbo->qn('u.id') . ' = ' . (int) $employee['jid'], 'OR')
            ->where('NOT EXISTS (' . $inner . ')')
            ->order($dbo->qn('u.name') . ' ASC');

        $dbo->setQuery($q);
        $dbo->execute();

        if ($dbo->getNumRows())
        {
            $jusers = $dbo->loadAssocList();
        }

        // subscriptions

        $q = $dbo->getQuery(true)
            ->select("1")
            ->from($dbo->qn('#__vikappointments_subscription'))
            ->where($dbo->qn('published') . ' = 1');

        $dbo->setQuery($q, 0, 1);
        $dbo->execute();

        $hasSubscr = (bool) $dbo->getNumRows();

        // custom fields

        $this->customFields = VAPCustomFields::getList(1, null, null, CF_EXCLUDE_REQUIRED_CHECKBOX);

        //

        $this->employee  	 = &$employee;
        $this->worktime 	 = &$worktime_days;
        $this->worktime_date = &$worktime_date;
        $this->groups 		 = &$groups;
        $this->jusers 		 = &$jusers;
        $this->hasSubscr	 = &$hasSubscr;
        $this->tab 			 = &$tab;
        $this->day 			 = &$day;

        // Display the template
        parent::display($tpl);
    }

    /**
     * Setting the toolbar.
     *
     * @param 	string 	$type 	The request type (new or edit).
     *
     * @return 	void
     */
    protected function addToolBar($type)
    {
        // Add menu title and some buttons to the page
        if ($type == 'edit')
        {
            JToolBarHelper::title(JText::_('VAPMAINTITLEEDITEMPLOYEE'), 'vikappointments');
        }
        else
        {
            JToolBarHelper::title(JText::_('VAPMAINTITLENEWEMPLOYEE'), 'vikappointments');
        }

        if (JFactory::getUser()->authorise('core.edit', 'com_vikappointments'))
        {
            JToolBarHelper::apply('saveEmployee', JText::_('VAPSAVE'));
            JToolBarHelper::save('saveAndCloseEmployee', JText::_('VAPSAVEANDCLOSE'));
            JToolBarHelper::custom('saveAndNewEmployee', 'save-new', 'save-new', JText::_('VAPSAVEANDNEW'), false);
            JToolBarHelper::divider();
        }

        JToolBarHelper::cancel( 'cancelEmployee', JText::_('VAPCANCEL'));
    }

    /**
     * Returns a blank item.
     *
     * @return 	array 	A blank item for new requests.
     */
    protected function getBlankItem()
    {
        return array(
            'id'				=> -1,
            'firstname' 		=> '',
            'lastname' 			=> '',
            'nickname' 			=> '',
            'alias' 			=> '',
            'email' 			=> '',
            'phone' 			=> '',
            'notify' 			=> 0,
            'showphone' 		=> 0,
            'quick_contact' 	=> 0,
            'listable' 			=> 1,
            'image' 			=> '',
            'note' 				=> '',
            'jid' 				=> -1,
            'id_group'			=> -1,
            'active_to'			=> -1,
            'timezone' 			=> '',
            'synckey' 			=> VikAppointments::generateSerialCode(12),
        );
    }

    /**
     * Returns the list of working times for the given date.
     * The method gives higher priority to the CUSTOM working days (@see $workdays).
     *
     * @param 	JDate 	$date 		The given date object.
     * @param 	array 	$worktimes 	The array of weekly working times.
     * @param 	array 	$workdays 	The array of yearly working times.
     *
     * @return 	array  	The list of working days.
     */
    protected function getWorkingDays($date, array $worktimes, array $workdays)
    {
        $pool = array();

        foreach ($workdays as $row)
        {
            $ymd = Verta::instance($row['ts'])->format('Y-m-d');

            if ($ymd == $date->format('Y-m-d'))
            {
                $pool[] = $row;
            }
        }

        if (count($pool))
        {
            return $pool;
        }

        foreach ($worktimes as $row)
        {
            $day = $date->format('N');

            // mod 7 because one of the 2 values may
            // represent sunday as 7 instead than 0
            if ((ArasJoomlaVikApp::fixDayIndex($day % 7)) == ($row['day'] % 7))
            {
                $pool[] = $row;
            }
        }

        return $pool;
    }

    /**
     * Checks if the given day is closed.
     *
     * @param 	JDate 	 $date 	 	The given date object.
     * @param 	array 	 $workdays 	The array of working days.
     *
     * @return 	boolean  True if closed, otherwise false.
     */
    protected function isClosed($date, array $workdays)
    {
        foreach ($workdays as $wd)
        {
            if ($wd['ts'] == -1)
            {
                $week_day = $wd['day'];
            }
            else
            {
                //$week_day = JDate::getInstance($wd['ts'])->format('N');
                $week_day = Verta::instance($wd['ts'])->format('N');
            }

            if (($date->format('N') % 7) == ($week_day % 7) && $wd['closed'])
            {
                return $wd;
            }
        }

        return false;
    }

    /**
     * Checks if there is a working day that starts at the specified hour.
     * This method doesn't consider the minutes (e.g. 9:30 starts @ 9:00).
     *
     * @param 	integer  $hour 		The hour to check.
     * @param 	array 	 $workdays 	The working days list.
     *
     * @return 	mixed  	 The starting working day, otherwise false.
     */
    protected function startsHere($hour, array $workdays)
    {
        foreach ($workdays as $wd)
        {
            $h = floor($wd['from'] / 60);

            if ($h == $hour)
            {
                return $wd;
            }
        }

        return false;
    }

    /**
     * Checks if there is a working day that ends at the specified hour.
     * This method doesn't consider the minutes (e.g. 9:30 ends @ 9:00).
     *
     * @param 	integer  $hour 		The hour to check.
     * @param 	array 	 $workdays 	The working days list.
     *
     * @return 	mixed  	 The ending working day, otherwise false.
     */
    protected function endsHere($hour, array $workdays)
    {
        foreach ($workdays as $wd)
        {
            if ($wd['to'] > $hour * 60 && $wd['to'] <= ($hour + 1) * 60)
            {
                return $wd;
            }
        }

        return false;
    }

    /**
     * Checks if there is a working day between the given hour and the next one.
     * This method doesn't consider the minutes.
     *
     * @param 	integer  $hour 		The hour to check.
     * @param 	array 	 $workdays 	The working days list.
     *
     * @return 	mixed  	 The contained working day, otherwise false.
     */
    protected function isContained($hour, array $workdays)
    {
        $hour *= 60;
        foreach ($workdays as $wd)
        {
            if ($wd['from'] < $hour && $hour < $wd['to'])
            {
                return $wd;
            }
        }

        return false;
    }
}
