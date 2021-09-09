<?php
/** 
 * @package       VikAppointments
 * @subpackage     com_vikappointments
 * @author        Matteo Galletti - e4j
 * @copyright     Copyright (C) 2019 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link         https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * VikAppointments View
 */
class VikAppointmentsViewservicesearch extends JViewUI
{
    /**
     * VikAppointments view display method.
     *
     * @return     void
     */
    function display($tpl = null)
    {    
        VikAppointments::load_complex_select();
        VikAppointments::load_fancybox();
        VikAppointments::load_currency_js();
        VikAppointments::load_font_awesome();
        
        $app     = JFactory::getApplication();
        $input     = $app->input;
        $dbo     = JFactory::getDbo();
        $user     = JFactory::getUser();

        $itemid = $input->getInt('Itemid', 0);
        
        // request args
        $id_service = $input->getUint('id_ser', 0);
        $id_emp     = $input->getInt('id_emp', -1);
        $last_day     = $input->getString('date');
        $month         = $input->getUint('month', 0);

        if (VikAppointments::getLoginRequirements() == 3 && !VikAppointments::isUserLogged())
        {
            // login is required
            $tpl = 'login';
        }
        else
        {
            // inner query to calculate the average rating of the service
            $rating = $dbo->getQuery(true)
                ->select('AVG(' . $dbo->qn('re.rating') . ')')
                ->from($dbo->qn('#__vikappointments_reviews', 're'))
                ->where(array(
                    $dbo->qn('s.id') . ' = ' . $dbo->qn('re.id_service'),
                    $dbo->qn('re.published') . ' = 1',
                ));

            // get service details
            $q = $dbo->getQuery(true)
                ->select('`s`.*')
                ->select('(' . $rating . ') AS ' . $dbo->qn('rating_avg'))
                ->from($dbo->qn('#__vikappointments_service', 's'))
                ->where(array(
                    $dbo->qn('s.id') . ' = ' . $id_service,
                    $dbo->qn('s.published') . ' = 1',
                ))
                ->andWhere(array(
                    $dbo->qn('s.end_publishing') . ' <= 0',
                    $dbo->qn('s.end_publishing') . ' >= ' . time(),
                ), 'OR');

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
            
            $dbo->setQuery($q, 0, 1);
            $dbo->execute();

            if (!$dbo->getNumRows())
            {
                $app->enqueueMessage(JText::_('VAPSERNOTFOUNDERROR'), 'error');
                $app->redirect(JRoute::_('index.php?option=com_vikappointments&view=serviceslist' . ($itemid ? '&Itemid=' . $itemid : ''), false));
                exit;
            }
            
            $ser = $dbo->loadAssoc();

            $lang_services = VikAppointments::getTranslatedServices($ser['id']);

            $ser['name']         = VikAppointments::getTranslation($ser['id'], $ser, $lang_services, 'name', 'name');
            $ser['description'] = VikAppointments::getTranslation($ser['id'], $ser, $lang_services, 'description', 'description');
            
            $this->service = &$ser;
            
            // get employees assigned to this service
            
            $employees = array();

            $q = $dbo->getQuery(true)
                ->select($dbo->qn(array('e.id', 'e.nickname', 'a.rate', 'a.duration', 'a.sleep', 'a.description')))
                ->from($dbo->qn('#__vikappointments_employee', 'e'))
                ->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('a.id_employee'))
                ->where($dbo->qn('a.id_service') . ' = ' . $id_service)
                ->andWhere(array(
                    $dbo->qn('e.active_to') . ' = -1',
                    $dbo->qn('e.active_to') . ' >= ' . time()
                ), 'OR')
                ->order($dbo->qn('e.nickname') . ' ASC');

            $dbo->setQuery($q);
            $dbo->execute();

            if ($dbo->getNumRows())
            {
                $employees         = $dbo->loadAssocList();
                $lang_employees = VikAppointments::getTranslatedEmployees();

                foreach ($employees as $k => $e)
                {
                    $employees[$k]['nickname'] = VikAppointments::getTranslation($e['id'], $e, $lang_employees, 'nickname', 'nickname');
                }
            }
            
            if ($id_emp <= 0 || !$this->getSelectedEmployee($id_emp, $employees))
            {
                // find the default employee
                $id_emp = -1;
                
                if ($ser['choose_emp'] && count($employees) > 0)
                {
                    $id_emp = $employees[0]['id'];
                }
            }

            // calculate overrides
            if ($this->service['choose_emp'])
            {
                foreach ($employees as $e)
                {
                    if ($e['id'] == $id_emp)
                    {
                        // an employee was selected, use its overrides
                        $this->service['price']    = $e['rate'];
                        $this->service['duration'] = $e['duration'];

                        /**
                         * Append description override to default one.
                         * Original service is already translated.
                         *
                         * @since 1.6.2
                         */
                        $this->service['description'] .= (string) $e['description'];
                    }
                }
            }

            /**
             * Cast service price to float as it is always displayed.
             *
             * @since 1.6.1
             */
            $this->service['price'] = (float) $this->service['price'];

            // set employee timezone
            $ser['timezone'] = VikAppointments::getEmployeeTimezone($id_emp);
            VikAppointments::setCurrentTimezone($ser['timezone']);
            
            // remove all the expired reservations (switch status from PENDING to REMOVED)
            VikAppointments::removeAllServicesReservationsOutOfTime($ser['id'], $dbo);
            
            // animate the page when the date is specified

            $doAnimation = true;
            
            if (empty($last_day))
            {

                // try to obtain the date from the 'day' value
                $ts = $input->getUint('day');

                if (!$ts)
                {
                    // day not set, use the current day
                    $ts = time();
                }

                $last_day = ArasJoomlaVikApp::jgetdate($ts);
                $last_day = ArasJoomlaVikApp::jmktime(0, 0, 0, $last_day['mon'], $last_day['mday'], $last_day['year']);
                
                $doAnimation = false;
            }
            else
            {
                $last_day = VikAppointments::jcreateTimestamp($last_day, 0, 0);

            }

            $this->doAnimation = &$doAnimation;

            // get options assigned to this service

            $options = array();

            $q = $dbo->getQuery(true)
                ->select('`o`.*')
                ->select(array(
                    $dbo->qn('v.id', 'id_var'),
                    $dbo->qn('v.name', 'var_name'),
                    $dbo->qn('v.inc_price'),
                ))
                ->from($dbo->qn('#__vikappointments_option', 'o'))
                ->leftjoin($dbo->qn('#__vikappointments_ser_opt_assoc', 'a') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('a.id_option'))
                ->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('v.id_option'))
                ->where(array(
                    $dbo->qn('a.id_service') . ' = ' . $ser['id'],
                    $dbo->qn('o.published') . ' = 1',
                ))
                ->order(array(
                    $dbo->qn('o.ordering') . ' ASC',
                    $dbo->qn('v.ordering') . ' ASC',
                ));
            
            $dbo->setQuery($q);
            $dbo->execute();

            if ($dbo->getNumRows())
            {
                $options         = $this->groupOptions($dbo->loadAssocList());
                $lang_options     = VikAppointments::getTranslatedOptions();

                // translate the options
                foreach ($options as $k => $option)
                {
                    $options[$k]['name']         = VikAppointments::getTranslation($option['id'], $option, $lang_options, 'name', 'name');
                    $options[$k]['description'] = VikAppointments::getTranslation($option['id'], $option, $lang_options, 'description', 'description');

                    $vars_json = VikAppointments::getTranslation($option['id'], $option, $lang_options, 'vars_json', 'vars_json', array());

                    foreach ($options[$k]['variations'] as $j => $var)
                    {
                        if (isset($vars_json[$var['id']]))
                        {
                            $options[$k]['variations'][$j]['name'] = $vars_json[$var['id']];
                        }
                    }
                }
            }
            
            // prepare calendar settings

            $arr     = ArasJoomlaVikApp::jgetdate();
            $year     = $arr['year'];
            
            $num_cals         = VikAppointments::getNumberOfCalendars();
            $first_month     = VikAppointments::getCalendarFirstMonth();
            $first_year     = VikAppointments::getCalendarFirstYear();
            
            $check_month = 0;
            $check_year  = 0;
            
            if ($arr['year'] < $first_year || ($arr['year'] == $first_year && $arr['mon'] <= $first_month))
            {
                // The first year/month pair is in the future,
                // we need to use them to start the calendar.
                $check_month = $first_month;
                $check_year  = $first_year;
            }
            else
            {
                // The first year/month pair is in the past,
                // we need to use the current period to start the calendar.
                $check_month = $arr['mon'];
                $check_year  = $arr['year'];
            }
            
            if (empty($month) || $month <= 0 || $month > 12)
            {
                // the selected month is invalid, reset it
                $month = $first_month = $check_month;
                // reset the year too
                $first_year = $year = $check_year; 
            }
            else if ($month < $check_month)
            {
                // the selected month is prior than the first month, reset it
                $first_month = $check_month;
                // reset the year too
                $first_year  = $check_year;

                $year++;
            }
            else
            {
                // all fine, setup the first month/year pair
                $first_month = $check_month;

                $first_year = $year = $check_year;
            }


            
            // get very first and last timestamps that will be available on the calendar
            $arr = ArasJoomlaVikApp::jgetdate(ArasJoomlaVikApp::jmktime(0, 0, 0, $month, 1, $year));

            /**
             * Consider service start publishing in case the fetched
             * timestamp is lower.
             *
             * @since 1.6.2
             */
            if ($arr[0] < (int) $this->service['start_publishing'])
            {
                // update initial date
                $arr = ArasJoomlaVikApp::jgetdate($this->service['start_publishing']);

                // update first year and month too
                $first_month = $month = $arr['mon'];
                $first_year  = $year  = $arr['year'];
            }

            $start     = $arr[0];
            $end     = $arr[0] + (60 * 60 * 24 * ($num_cals * 31)); // 31 max days in a month
            
            if ($last_day < $start)
            {
                // The last day cannot be used as it is prior than the first timestamp available.
                // Use the very first timestamp instead.
                $last_day = $arr[0];
            }
            else if ($last_day > $end)
            {
                /**
                 * The last day cannot be used as it is after the last timestamp available.
                 * Use the very first timestamp instead.
                 *
                 * @since 1.6
                 */

                $today = ArasJoomlaVikApp::jgetdate();

                if ($arr['mon'] == $today['mon'])
                {
                    // same month, use the current day
                    $last_day = ArasJoomlaVikApp::jmktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);
                }
                else
                {
                    // different month, use the very first day
                    $last_day = $arr[0];
                }
            }

            // get all employee bookings
            
            $bookings = array();

            if ($ser['choose_emp'])
            {
                // Make sure the employee is selected.
                // Avoid queries if the service is not assigned to any employee.
                if ($id_emp > 0)
                {
                    // get the bookings of the selected employee
                    $bookings = VikAppointments::getAllEmployeeReservations($id_emp, $ser['id'], $start, $end, $dbo);
                }
            }
            else
            {
                // The employee is not selectable.
                // Get the bookings of all the employees assigned to this service.
                $bookings = VikAppointments::getAllServiceReservations($ser['id'], $start, $end, $dbo);
            }

            // load cart instance
            
            VikAppointments::loadCartLibrary();
            
            $core = new VikAppointmentsCartCore();
            $cart = $core->getCartObject();

            $cart_is_empty = $cart->isEmpty();

            // get locations

            $locations = array();
            $loc_ids   = array();

            /**
             * The locations should be retrieved even if the employee is not choosable.
             * In this case, we should obtain the locations of all the employees
             * assigned to the current service.
             *
             * @since 1.6
             */
            if ($id_emp > 0 || count($employees))
            {
                /**
                 * @todo     Since the query is using a group by, we need to split
                 *             `l`.* to retrieve the exact columns that will be used.
                 *             The STAR/GROUP BY pair is not a SQL standard.
                 */

                $all_emp_ids = array();
                foreach ($employees as $emp)
                {
                    $all_emp_ids[] = (int) $emp['id'];
                }

                $q = $dbo->getQuery(true);

                $q->select('`l`.*');
                    
                $q->from($dbo->qn('#__vikappointments_employee_location', 'l'));
                $q->leftjoin($dbo->qn('#__vikappointments_emp_worktime', 'w') . ' ON ' . $dbo->qn('l.id') . ' = ' . $dbo->qn('w.id_location'));
                
                $q->where($dbo->qn('w.id_service') . ' = ' . $ser['id']);

                if ($id_emp > 0)
                {
                    $q->where($dbo->qn('w.id_employee') . ' = ' . $id_emp);

                    $q->andWhere(array(
                        $dbo->qn('l.id_employee') . ' = -1',
                        $dbo->qn('l.id_employee') . ' = ' . $id_emp,
                    ), 'OR');
                }
                else
                {
                    $q->where($dbo->qn('w.id_employee') . ' IN (' . implode(',', $all_emp_ids) . ')');

                    $q->andWhere(array(
                        $dbo->qn('l.id_employee') . ' = -1',
                        $dbo->qn('l.id_employee') . ' IN (' . implode(',', $all_emp_ids) . ')',
                    ), 'OR');
                }

                $q->group($dbo->qn('l.id'));

                $dbo->setQuery($q);
                $dbo->execute();

                if ($dbo->getNumRows())
                {
                    $locations = $dbo->loadAssocList();

                    foreach ($locations as $loc)
                    {
                        $loc_ids[] = $loc['id'];
                    }
                }
            }

            // get locations from request

            $req_locations = $input->getUint('locations', array());

            /**
             * It is needed to unset all the locations that don't belong
             * to the current employee, as they may have been submitted
             * after selecting a different employee.
             *
             * @since 1.6
             */
            $req_locations = array_values(array_intersect($req_locations, $loc_ids));
            
            if (count($req_locations) == 0 && count($locations) > 1)
            {
                // use all the locations if not provided
                $req_locations = $loc_ids;
            }
            
            // get reviews

            $base_uri = "index.php?option=com_vikappointments&view=servicesearch&id_ser={$ser['id']}&id_emp={$id_emp}";

            $limit_start     = $input->getUint('limitstart', 0);
            $rev_ord_by     = $input->getString('revordby', '');
            $rev_ord_mode     = $input->getString('revordmode', '');
    
            $this->service['rating_avg'] = VikAppointments::roundHalfClosest($ser['rating_avg']);
            $can_leave_reviews              = VikAppointments::userCanLeaveServiceReview($ser['id']);
            $reviews_ordering_links      = VikAppointments::getReviewsOrderingLinks($base_uri, $rev_ord_by, $rev_ord_mode);

            $reviews = VikAppointments::loadReviews('service', $ser['id'], $limit_start);

            if ($ser['checkout_selection'])
            {
                /**
                 * In case the checkout selection is allowed, we need to include
                 * the script used to handle the checkin/checkout events.
                 *
                 * @since 1.6
                 */
                $js = JLayoutHelper::render('javascript.timeline.dropdown');
                $this->document->addScriptDeclaration($js);
            }

            // define properties
            //dd(ArasJoomlaVikApp::jgetdate($last_day));
            $this->employees     = &$employees;
            $this->bookings     = &$bookings;
            $this->id_emp         = &$id_emp;
            $this->last_day     = &$last_day;
            $this->defMonth     = &$first_month;
            $this->year         = &$year;
            $this->defYear         = &$first_year;
            $this->options         = &$options;
            $this->cart_empty     = &$cart_is_empty;

            $this->locations     = &$locations;
            $this->reqLocations = &$req_locations;
            
            $this->reviews                 = &$reviews;
            $this->reviewsOrderingLinks = &$reviews_ordering_links;
            $this->userCanLeaveReview     = &$can_leave_reviews;
        }

        $this->idService  = $id_service;
        $this->idEmployee = $id_emp;
        $this->lastDay       = $last_day;
        $this->month       = $month;
        $this->itemid       = $itemid;

        if (!$tpl)
        {
            // prepare view contents and microdata
            VikAppointments::prepareContent($this);

            // extend pathway for breadcrumbs module
            $this->extendPathway($app);
        }
        
        // Display the template
        parent::display($tpl);
    }

    /**
     * Groups the list of options with the related variations.
     *
     * @param     array     $options     The list of options to group.
     *
     * @return     array     The grouped list.
     *
     * @since     1.6
     */
    protected function groupOptions(array $options)
    {
        $arr = array();

        $last_opt_id = -1;

        foreach ($options as $o)
        {
            if ($last_opt_id != $o['id'])
            {
                $last_opt_id      = $o['id'];
                $o['variations'] = array();

                $arr[] = $o;
            }

            if (!empty($o['id_var']))
            {
                $arr[count($arr) - 1]['variations'][] = array(
                    'id'         => $o['id_var'],
                    'name'         => $o['var_name'],
                    'inc_price' => $o['inc_price'],
                );
            }
        }

        return $arr;
    }

    /**
     * Finds the specified employee located in the given array.
     *
     * @param     integer  $id_emp      The employee to search.
     * @param     array      $employees     The haystack.
     *
     * @return     mixed      The employee array on success, otherwise false.
     *
     * @since     1.6
     */
    protected function getSelectedEmployee($id_emp, array $employees)
    {
        foreach ($employees as $e)
        {
            if ($e['id'] == $id_emp)
            {
                return $e;
            }
        }

        return false;
    }

    /**
     * Extends the pathway for breadcrumbs module.
     *
     * @param     mixed     $app  The application instance.
     *
     * @return     void
     *
     * @since     1.6
     */
    protected function extendPathway($app)
    {
        $pathway = $app->getPathway();
        $items   = $pathway->getPathway();
        $last      = end($items);

        $name = $this->service['name'];
        $id   = $this->service['id'];

        // Make sure this service is not a menu item, otherwise
        // the pathway will display something like:
        // Home > Menu > Item > Item
        if ($last && strpos($last->link, '&id_ser=' . $id) === false)
        {
            $link = 'index.php?option=com_vikappointments&view=servicesearch&id_ser=' . $id;

            $pathway->addItem($name, $link);
        }
    }
}
