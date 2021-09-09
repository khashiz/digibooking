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

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of VikAppointments component.
 */
class VikAppointmentsController extends JControllerUI
{
	/**
	 * Display task.
	 *
	 * @return 	void
	 *
	 * @uses 	handleView()
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$input = JFactory::getApplication()->input;

		$task 		= $input->get('task', null);
		// print the menu only if tmpl is not "component"
		$hasMenu 	= (bool) strcmp($input->getString('tmpl'), 'component');
		$public 	= false;

		// If task not set, handle the default view (dashboard or calendar)
		// and set the view as public.
		if (empty($task))
		{
			$task 		= VikAppointments::getDefaultTask();
			$public 	= true;
		}

		if ($task == 'calendar')
		{
			// in case the task is calendar, obtain the selected layout
			$task = UIFactory::getConfig()->get('calendarlayout', 'calendar');
		}
		
		$this->handleView($task, $hasMenu, $public);
	}

	/**
	 * Handle the view to render.
	 *
	 * @param 	string 	 $task 		The task to display.
	 * @param 	boolean  $hasMenu 	True to show the application menu and the footer.
	 * @param 	boolean  $public 	True to skip ACL validation.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	private function handleView($task, $hasMenu = true, $public = false)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$acl = isset($this->lookupACL[$task]) ? $this->lookupACL[$task] : 'access.' . $task;

		// validate ACL for private pages (skip if public)
		if (!$public && !JFactory::getUser()->authorise('core.' . $acl, 'com_vikappointments'))
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		// check if the menu should be printed 
		if ($hasMenu)
		{
			AppointmentsHelper::printMenu();
		}
		
		$app->input->set('view', $app->input->get('view', $task));

		parent::display();
		
		// check if the footer should be printed
		if ($hasMenu)
		{
			AppointmentsHelper::printJoomla3810ter();
		}
	}

	/**
	 * Handle the management view to render.
	 *
	 * @param 	string 	$task 	The task to display.
	 * @param 	string  $acl 	The ACL rule to validate (skip if NULL).
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	private function handleManagementView($task, $acl = null)
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		if (strpos($task, 'new') === 0)
		{
			$type 	= 'new';
			$target = substr($task, 3);
		}
		else
		{
			$type 	= 'edit';
			$target = substr($task, 4);
		}

		if (!$acl)
		{
			$acl = isset($this->lookupACL[$target]) ? $this->lookupACL[$target] : 'access.' . $target . 's';
		}

		// validate ACL for private pages (skip if ACL is null)
		if ($acl && !JFactory::getUser()->authorise('core.' . $acl, 'com_vikappointments'))
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}
		
		$app->input->set('type', $type);
		$app->input->set('view', $app->input->get('view', 'manage' . $target));

		parent::display();
	}

	/**
	 * FULL SCREEN VIEWS (no menu)
	 */
	
	function findreservation()
	{
		$this->handleView('findreservation', false);
	}

	function mailtextcust()
	{
		$this->handleView('mailtextcust', false);
	}

	function conversions()
	{
		$this->handleView('conversions', false);
	}
	
	function reportsall()
	{
		$this->handleView('reportsall', false);
	}

	function reportsallser()
	{
		$this->handleView('reportsallser', false);
	}

	function reportspack()
	{
		$this->handleView('reportspack', false);
	}

	function manageclosure()
	{
		$this->handleView('manageclosure', false);
	}
	
	function reportsemp()
	{
		$this->handleView('reportsemp', false);
	}

	function reportsser()
	{
		$this->handleView('reportsser', false);
	}
	
	function exportres()
	{
		$this->handleView('exportres', false);
	}

	function makerecurrence()
	{
		$this->handleView('makerecurrence', false);
	}

	function manageimport()
	{
		$this->handleView('manageimport', false);
	}
	
	function cronjobs()
	{
		$this->handleView('cronjobs', false);
	}

	function cronjoblogs()
	{
		$this->handleView('cronjoblogs', false);
	}

	function updateprogram()
	{
		$this->handleView('updateprogram', false);
	}

	/**
	 * MODAL BOXES
	 */
	
	function printorders()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('printorders', false);
	}
	
	function managefile()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managefile', false);
	}

	/**
	 * COUNTRIES
	 */
	
	function newcountry()
	{
		$this->handleManagementView('newcountry', 'access.countries');
	}
	
	function editcountry()
	{
		$this->handleManagementView('editcountry', 'access.countries');
	}

	function newstate()
	{
		$this->handleManagementView('newstate', 'access.countries');
	}
	
	function editstate()
	{
		$this->handleManagementView('editstate', 'access.countries');
	}
	
	function newcity()
	{
		$this->handleManagementView('newcity', 'access.countries');
	}
	
	function editcity()
	{
		$this->handleManagementView('editcity', 'access.countries');
	}

	/**
	 * COUPONS
	 */
	
	function newcoupon()
	{
		$this->handleManagementView('newcoupon');
	}
	
	function editcoupon()
	{
		$this->handleManagementView('editcoupon');
	}

	function newcoupongroup()
	{
		$this->handleManagementView('newcoupongroup', 'access.coupons');
	}
	
	function editcoupongroup()
	{
		$this->handleManagementView('editcoupongroup', 'access.coupons');
	}

	/**
	 * CRON JOBS
	 */
	
	function newcronjob()
	{
		$this->handleManagementView('newcronjob', 'access.config');
	}
	
	function editcronjob()
	{
		$this->handleManagementView('editcronjob', 'access.config');
	}

	/**
	 * CUSTOMERS
	 */
	
	function newcustomer()
	{
		$this->handleManagementView('newcustomer');
	}
	
	function editcustomer()
	{
		$this->handleManagementView('editcustomer');
	}

	/**
	 * CUSTOM FIELDS
	 */
	
	function newcustomf()
	{
		$this->handleManagementView('newcustomf', 'access.custfields');
	}
	
	function editcustomf()
	{
		$this->handleManagementView('editcustomf', 'access.custfields');
	}

	function managelangcustomf()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelangcustomf', false);
	}

	/**
	 * EMPLOYEES
	 */
	
	function newemployee()
	{
		$this->handleManagementView('newemployee');
	}
	
	function editemployee()
	{
		$this->handleManagementView('editemployee');
	}
	
	function newemplocation()
	{
		$this->handleManagementView('newemplocation', 'access.employees');
	}
	
	function editemplocation()
	{
		$this->handleManagementView('editemplocation', 'access.employees');
	}

	function newemppayment()
	{
		$input = JFactory::getApplication()->input;

		$input->set('id_employee', $input->getUint('id_emp', 0));

		$this->newpayment();
	}

	function editemppayment()
	{
		$input = JFactory::getApplication()->input;

		$input->set('id_employee', $input->getUint('id_emp', 0));

		$this->editpayment();
	}

	function managelangemployee()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelangemployee', false);
	}

	/**
	 * GROUPS
	 */
	 
	function newgroup()
	{
		$this->handleManagementView('newgroup');
	}
	
	function editgroup()
	{
		$this->handleManagementView('editgroup');
	}
	
	function newempgroup()
	{
		$this->handleManagementView('newempgroup', 'access.groups');
	}
	
	function editempgroup()
	{
		$this->handleManagementView('editempgroup', 'access.groups');
	}

	function managelanggroup()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelanggroup', false);
	}
	
	function managelangempgroup()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelangempgroup', false);
	}

	/**
	 * LOCATIONS
	 */

	function newlocation()
	{
		$this->handleManagementView('newlocation');
	}
	
	function editlocation()
	{
		$this->handleManagementView('editlocation');
	}

	/**
	 * MAIL TEXT
	 */
	
	function newmailtext()
	{
		$this->handleManagementView('newmailtext', 'access.config');
	}
	
	function editmailtext()
	{
		$this->handleManagementView('editmailtext', 'access.config');
	}

	/**
	 * CONVERSIONS
	 */
	
	function newconversion()
	{
		$this->handleManagementView('newconversion', 'access.config');
	}
	
	function editconversion()
	{
		$this->handleManagementView('editconversion', 'access.config');
	}

	/**
	 * MEDIA
	 */
	
	function newmedia()
	{
		$this->handleManagementView('newmedia', 'access.media');
	}

	/**
	 * OPTIONS
	 */

	function newoption()
	{
		$this->handleManagementView('newoption');
	}
	
	function editoption()
	{
		$this->handleManagementView('editoption');
	}

	function managelangoption()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelangoption', false);
	}

	/**
	 * PACKAGES
	 */

	function packages()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleView('packages');
	}

	function packgroups()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleView('packgroups');
	}

	function packorders()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleView('packorders');
	}

	function packorderinfo()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleView('packorderinfo', false);
	}

	//

	function newpackage()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleManagementView('newpackage');
	}
	
	function editpackage()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleManagementView('editpackage');
	}

	function newpackgroup()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleManagementView('newpackgroup', 'access.packages');
	}
	
	function editpackgroup()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleManagementView('editpackgroup', 'access.packages');
	}

	function newpackorder()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleManagementView('newpackorder', 'access.packages');
	}
	
	function editpackorder()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleManagementView('editpackorder', 'access.packages');
	}

	//

	function managelangpackage()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelangpackage', false);
	}

	function managelangpackgroup()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isPackagesEnabled())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelangpackgroup', false);
	}

	/**
	 * PAYMENTS
	 */
	
	function newpayment()
	{
		$this->handleManagementView('newpayment');
	}
	
	function editpayment()
	{
		$this->handleManagementView('editpayment');
	}

	function managelangpayment()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelangpayment', false);
	}

	/**
	 * RESERVATIONS
	 */
	 
	function newreservation()
	{
		$this->handleManagementView('newreservation');
	}

	function editreservation()
	{
		$this->handleManagementView('editreservation');
	}

	/**
	 * REVIEWS
	 */
	
	function newrev()
	{
		$this->handleManagementView('newrev', 'access.reviews');
	}
	
	function editrev()
	{
		$this->handleManagementView('editrev', 'access.reviews');
	}

	/**
	 * SERVICES
	 */

	function newservice()
	{
		$this->handleManagementView('newservice');
	}
	
	function editservice()
	{
		$this->handleManagementView('editservice');
	}

	function managelangservice()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelangservice', false);
	}

	/**
	 * SPECIAL RATES
	 */

	function newspecialrate()
	{
		$this->handleManagementView('newrate', 'access.services');
	}
	
	function editspecialrate()
	{
		$this->handleManagementView('editrate', 'access.services');
	}

	/**
	 * SUBSCRIPTIONS
	 */
	
	function newsubscription()
	{
		$this->handleManagementView('newsubscription');
	}
	
	function editsubscription()
	{
		$this->handleManagementView('editsubscription');
	}

	function newsubscrorder()
	{
		$this->handleManagementView('newsubscrorder', 'access.subscriptions');
	}
	
	function editsubscrorder()
	{
		$this->handleManagementView('editsubscrorder', 'access.subscriptions');
	}

	function managelangsubscr()
	{
		JFactory::getApplication()->input->set('tmpl', 'component');
		$this->handleView('managelangsubscr', false);
	}

	/**
	 * WAITING LIST
	 */

	function waitinglist()
	{
		// skip if the packages are disabled
		if (!VikAppointments::isWaitingList())
		{
			$app->redirect('index.php?option=com_vikappointments');
			exit;
		}

		$this->handleView('waitinglist');
	}

	function newwaiting()
	{
		$this->handleManagementView('newwaiting', 'access.waitinglist');
	}
	
	function editwaiting()
	{
		$this->handleManagementView('editwaiting', 'access.waitinglist');
	}
	 
	/**
	 * SAVE GROUP
	 */
	
	function saveAndCloseGroup()
	{
		$this->saveGroup('index.php?option=com_vikappointments&task=groups');
	}

	function saveAndNewGroup()
	{
		$this->saveGroup('index.php?option=com_vikappointments&task=newgroup');
	}
	
	function saveGroup($return_url = '')
	{	
		$dbo 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		$args = array();
		$args['name'] 		 = $input->getString('name');
		$args['description'] = $input->getRaw('description', '');
		$args['id'] 		 = $input->getInt('id', -1);
		
		$blank_keys = AppointmentsHelper::validateGroup($args);
		
		if (!count($blank_keys))
		{	
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewGroup($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedGroup($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newgroup' : 'editgroup&cid[]=' . $args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editgroup&cid[]=' . $args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewGroup(array $args, $dbo, $app)
	{	
		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('ordering'))
			->from($dbo->qn('#__vikappointments_group'))
			->order($dbo->qn('ordering') . ' DESC');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$ordering = (int) $dbo->loadResult() + 1;

		$group = (object) $args;
		$group->ordering = $ordering;

		$res = $dbo->insertObject('#__vikappointments_group', $group, 'id');

		if ($res && $group->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWGROUPCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWGROUPCREATED0'), 'error');
		}
		
		return $group->id;
	}
	
	private function editSelectedGroup(array $args, $dbo, $app)
	{	
		$group = (object) $args;

		$dbo->updateObject('#__vikappointments_group', $group, 'id');

		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPGROUPEDITED1'));
		}
	}
	
	function saveLangGroup()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['description'] 	= $input->getRaw('langdesc', '');
		$args['tag'] 			= $input->getString('tag');
		$args['id'] 			= $input->getInt('id');
		$args['id_group'] 		= $input->getInt('id_group');

		$lang = (object) $args;
		
		if ($args['id'] == -1)
		{
			unset($lang->id);
			$dbo->insertObject('#__vikappointments_lang_group', $lang, 'id');
		}
		else
		{
			unset($lang->tag);
			unset($lang->id_group);
			$dbo->updateObject('#__vikappointments_lang_group', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPGROUPLANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelanggroup&tag={$args['tag']}&id_group={$args['id_group']}");
	}

	/**
	 * SAVE EMPLOYEE GROUP
	 */
	
	function saveAndCloseEmployeeGroup()
	{
		$this->saveEmployeeGroup('index.php?option=com_vikappointments&task=groups');
	}

	function saveAndNewEmployeeGroup()
	{
		$this->saveEmployeeGroup('index.php?option=com_vikappointments&task=newempgroup');
	}
	
	function saveEmployeeGroup($return_url = '')
	{	
		$dbo 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		$args = array();
		$args['name'] 		 = $input->getString('name');
		$args['description'] = $input->getRaw('description', '');
		$args['id'] 		 = $input->getInt('id', -1);
		
		$blank_keys = AppointmentsHelper::validateGroup($args);
		
		if (!count($blank_keys))
		{	
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewEmployeeGroup($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedEmployeeGroup($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newempgroup' : 'editempgroup&cid[]=' . $args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editempgroup&cid[]=' . $args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewEmployeeGroup(array $args, $dbo, $app)
	{	
		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('ordering'))
			->from($dbo->qn('#__vikappointments_employee_group'))
			->order($dbo->qn('ordering') . ' DESC');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$ordering = (int) $dbo->loadResult() + 1;

		$group = (object) $args;
		$group->ordering = $ordering;

		$res = $dbo->insertObject('#__vikappointments_employee_group', $group, 'id');

		if ($res && $group->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWGROUPCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWGROUPCREATED0'), 'error');
		}
		
		return $group->id;
	}
	
	private function editSelectedEmployeeGroup(array $args, $dbo, $app)
	{	
		$group = (object) $args;

		$dbo->updateObject('#__vikappointments_employee_group', $group, 'id');

		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPGROUPEDITED1'));
		}
	}
	
	function saveLangEmployeeGroup()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['description'] 	= $input->getRaw('langdesc', '');
		$args['tag'] 			= $input->getString('tag');
		$args['id'] 			= $input->getInt('id');
		$args['id_empgroup'] 	= $input->getInt('id_empgroup');

		$lang = (object) $args;
		
		if ($args['id'] == -1 )
		{
			unset($lang->id);
			$dbo->insertObject('#__vikappointments_lang_empgroup', $lang, 'id');
		}
		else
		{
			unset($lang->tag);
			unset($lang->id_empgroup);
			$dbo->updateObject('#__vikappointments_lang_empgroup', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPGROUPLANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelangempgroup&tag={$args['tag']}&id_empgroup={$args['id_empgroup']}");	
	}

	/**
	 * SAVE EMPLOYEE PAYMENT
	 */

	function saveAndCloseEmployeePayment()
	{
		$id_emp = JFactory::getApplication()->input->getUint('id_employee', 0);
		$this->savePayment('index.php?option=com_vikappointments&task=emppayments&id_emp=' . $id_emp);
	}
	
	function saveAndNewEmployeePayment()
	{
		$id_emp = JFactory::getApplication()->input->getUint('id_employee', 0);
		$this->savePayment('index.php?option=com_vikappointments&task=newpayment&id_employee=' . $id_emp);
	}
	 
	/**
	 * SAVE SERVICE
	 */
	
	function saveAndCloseService()
	{
		$this->saveService('index.php?option=com_vikappointments&task=services');
	}
	
	function saveAndNewService()
	{
		$this->saveService('index.php?option=com_vikappointments&task=newservice');
	}
	
	function saveService($return_url = '')
	{
		$dbo 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		$args = array();
		$args['name'] 				= $input->getString('name');
		$args['alias'] 				= $input->getString('alias');
		$args['description'] 		= $input->getRaw('description');
		$args['duration'] 			= $input->getUint('duration');
		$args['sleep'] 				= $input->getInt('sleep');
		$args['interval'] 			= $input->getUint('interval');
		$args['price'] 				= $input->getFloat('price');
		$args['max_capacity'] 		= $input->getUint('max_capacity');
		$args['max_per_res'] 		= $input->getUint('max_per_res');
		$args['min_per_res'] 		= $input->getUint('min_per_res');
		$args['priceperpeople'] 	= $input->getUint('priceperpeople', 0);
		$args['app_per_slot'] 		= $input->getUint('app_per_slot', 0);
		$args['published'] 			= $input->getUint('published', 0);
		$args['quick_contact'] 		= $input->getUint('quick_contact', 0);
		$args['choose_emp'] 		= $input->getUint('choose_emp', 0);
		$args['has_own_cal'] 		= $input->getUint('has_own_cal', 0);
		$args['checkout_selection'] = $input->getUint('checkout_selection', 0);
		$args['display_seats'] 		= $input->getUint('display_seats', 0);
		$args['use_recurrence'] 	= $input->getUint('use_recurrence', 0);
		$args['enablezip'] 			= $input->getUint('enablezip', 0);
		$args['image'] 				= $input->getString('media'); 
		$args['start_publishing'] 	= $input->getString('start_publishing'); 
		$args['end_publishing'] 	= $input->getString('end_publishing');
		$args['level'] 				= $input->getUint('level', 0);
		$args['id_group'] 			= $input->getInt('group');
		$args['metadata']			= $input->get('metadata', array(), 'array');
		$args['id'] 				= $input->getInt('id');

		$new_opt = $input->getUint('new_opt', array());
		$del_opt = $input->getUint('del_opt', array());
		
		$new_emp = $input->getUint('new_emp', array());
		$del_emp = $input->getUint('del_emp', array());

		$args['max_capacity'] = max(array($args['max_capacity'], 1));
		$args['max_per_res']  = min(array($args['max_per_res'], $args['max_capacity']));
		$args['min_per_res']  = min(array($args['min_per_res'], $args['max_per_res']));

		if ($args['max_capacity'] == 1)
		{
			$args['app_per_slot']  = 1;
			$args['display_seats'] = 0;
		}

		if (empty($args['id_group']))
		{
			$args['id_group'] = -1;
		}

		if (empty($args['start_publishing']) || empty($args['end_publishing']))
		{
			$args['start_publishing'] = $args['end_publishing'] = -1;
		}
		else
		{
			$args['start_publishing'] 	= VikAppointments::jcreateTimestamp($args['start_publishing'], 0, 0);
			$args['end_publishing'] 	= VikAppointments::jcreateTimestamp($args['end_publishing'], 23, 59);

			if ($args['start_publishing'] > $args['end_publishing'])
			{
				$args['start_publishing'] = $args['end_publishing'] = -1;
			}
		}

		/**
		 * Create alias.
		 *
		 * @since 1.6
		 */
		if (!$args['alias'])
		{
			$args['alias'] = $args['name'];
		}

		UILoader::import('libraries.helpers.alias');
		$args['alias'] = AliasHelper::getUniqueAlias($args['alias'], 'service', $args['id']);

		/**
		 * Stringify service metadata.
		 *
		 * @since 1.6.1
		 */
		$args['metadata'] = json_encode($args['metadata']);
		
		// validate service args
		$blank_keys = AppointmentsHelper::validateService($args);

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsaveser.tab', 'tabname', null, 'string');
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewService($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedService($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args["id"] == -1 ? 'newservice' : 'editservice&cid[]='.$args['id']));
		}

		if ($args['id'] > 0)
		{
			// delete options
			if (count($del_opt))
			{
				$q = $dbo->getQuery(true)
					->delete($dbo->qn('#__vikappointments_ser_opt_assoc'))
					->where(array(
						$dbo->qn('id_service') . ' = ' . $args['id'],
						$dbo->qn('id_option') . ' IN (' . implode(',', $del_opt) . ')',
					));

				$dbo->setQuery($q);
				$dbo->execute();
			}
			
			// insert options
			foreach ($new_opt as $o)
			{
				$option = new stdClass;
				$option->id_service = $args['id'];
				$option->id_option  = $o;

				$dbo->insertObject('#__vikappointments_ser_opt_assoc', $option, 'id');
			}

			// delete employees
			if (count($del_emp))
			{
				$q = $dbo->getQuery(true)
					->delete($dbo->qn('#__vikappointments_ser_emp_assoc'))
					->where(array(
						$dbo->qn('id_service') . ' = ' . $args['id'],
						$dbo->qn('id_employee') . ' IN (' . implode(',', $del_emp) . ')',
					));

				$dbo->setQuery($q);
				$dbo->execute();

				$q = $dbo->getQuery(true)
					->delete($dbo->qn('#__vikappointments_emp_worktime'))
					->where(array(
						$dbo->qn('id_service') . ' = ' . $args['id'],
						$dbo->qn('id_employee') . ' IN (' . implode(',', $del_emp) . ')',
					));

				$dbo->setQuery($q);
				$dbo->execute();
			}
			
			foreach ($new_emp as $e)
			{
				$employee = new stdClass;
				$employee->id_service 	= $args['id'];
				$employee->id_employee 	= $e;
				$employee->rate 		= $args['price'];
				$employee->duration 	= $args['duration'];
				$employee->sleep 		= $args['sleep'];

				$dbo->insertObject('#__vikappointments_ser_emp_assoc', $employee, 'id');
				
				// duplicate the  employee working days for this service
				$q = $dbo->getQuery(true)
					->select('*')
					->from($dbo->qn('#__vikappointments_emp_worktime'))
					->where(array(
						$dbo->qn('id_employee') . ' = ' . $e,
						$dbo->qn('id_service') . ' = -1',
					));

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					foreach ($dbo->loadObjectList() as $w)
					{
						/**
						 * Keep a relation with the parent working day
						 * while assigning a new employee to this service.
						 *
						 * @since 1.6.2
						 */
						$w->parent     = $w->id;
						$w->id_service = $args['id'];
						// unset ID to avoid duplicated primary key errors
						unset($w->id);
						$dbo->insertObject('#__vikappointments_emp_worktime', $w, 'id');
					}
				} 
				
			}
			
			// check if the changes should be applied to all the overrides
			if ($input->getBool('update_employees'))
			{
				$q = $dbo->getQuery(true)
					->update($dbo->qn('#__vikappointments_ser_emp_assoc'))
					->set($dbo->qn('rate') . ' = ' . $args['price'])
					->set($dbo->qn('duration') . ' = ' . $args['duration'])
					->set($dbo->qn('sleep') . ' = ' . $args['sleep'])
					->where($dbo->qn('id_service') . ' = ' . $args['id']);

				$dbo->setQuery($q);
				$dbo->execute();
			}
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editservice&cid[]=' . $args['id'];
		}
		
		$app->redirect($return_url);
	}
	
	private function saveNewService(array $args, $dbo, $app)
	{
		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('ordering'))
			->from($dbo->qn('#__vikappointments_service'))
			->order($dbo->qn('ordering') . ' DESC');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$ordering = (int) $dbo->loadResult() + 1;

		$service = (object) $args;
		$service->ordering = $ordering;

		$res = $dbo->insertObject('#__vikappointments_service', $service, 'id');

		if ($res && $service->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWSERVICECREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWSERVICECREATED0'), 'error');
		}
		
		return $service->id;
	}
	
	private function editSelectedService(array $args, $dbo, $app)
	{
		$service = (object) $args;

		$dbo->updateObject('#__vikappointments_service', $service, 'id');

		// show always update message as the user may change only the assignments
		$app->enqueueMessage(JText::_('VAPSERVICEEDITED1'));
	}
	
	function restoreSerWorkDays()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$id_ser = $input->getUint('id', 0);
		$id_emp = $input->getUint('id_emp', 0);

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_emp_worktime'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $id_emp,
				$dbo->qn('id_service') . ' = -1',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$worktimes = $dbo->loadObjectList();
			
			$q = $dbo->getQuery(true)
				->select('COUNT(' . $dbo->qn('id') . ')')
				->from($dbo->qn('#__vikappointments_emp_worktime'))
				->where(array(
					$dbo->qn('id_employee') . ' = ' . $id_emp,
					$dbo->qn('id_service') . ' = ' . $id_ser,
				));

			$dbo->setQuery($q);
			$dbo->execute();
			
			$count = (int) $dbo->loadResult();
			
			// make sure that the service doesn't own already all the working days
			if ($count != count($worktimes))
			{
				$q = $dbo->getQuery(true)
					->delete($dbo->qn('#__vikappointments_emp_worktime'))
					->where(array(
						$dbo->qn('id_employee') . ' = ' . $id_emp,
						$dbo->qn('id_service') . ' = ' . $id_ser,
					));

				$dbo->setQuery($q);
				$dbo->execute();
				
				foreach ($worktimes as $w)
				{
					// keep a relation with the parent
					$w->parent 		= $w->id;
					$w->id_service  = $id_ser;

					// unset ID to avoid duplicated primary key errors
					unset($w->id);

					$dbo->insertObject('#__vikappointments_emp_worktime', $w, 'id');
				}
				
				$app->enqueueMessage(JText::_('VAPSERWORKDAYSRESTORED1'));
			}
			// nothing to restore
			else
			{
				$app->enqueueMessage(JText::_('VAPSERWORKDAYSRESTORED0'), 'warning');
			}
		} 
		
		$app->redirect("index.php?option=com_vikappointments&task=serworkdays&id={$id_ser}&id_emp={$id_emp}");
	}
	
	function saveLangService()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['alias'] 			= $input->getString('alias');
		$args['description'] 	= $input->getRaw('langdesc', '');
		$args['tag'] 			= $input->getString('tag');
		$args['id'] 			= $input->getInt('id');
		$args['id_service'] 	= $input->getInt('id_service');

		if ($args['alias'])
		{
			UILoader::import('libraries.helpers.alias');
			$args['alias'] = AliasHelper::getUniqueAlias($args['alias'], 'service', $args['id']);
		}
		
		$lang = (object) $args;

		if ($args['id'] == -1)
		{
			unset($lang->id);
			$dbo->insertObject('#__vikappointments_lang_service', $lang, 'id');
		}
		else
		{
			unset($lang->tag);
			unset($lang->id_service);
			$dbo->updateObject('#__vikappointments_lang_service', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPSERLANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelangservice&tag={$args['tag']}&id_service={$args['id_service']}");
	}

	/**
	 * SAVE SPECIAL RATE
	 */
	
	function saveAndCloseSpecialRate()
	{
		$this->saveSpecialRate('index.php?option=com_vikappointments&task=rates');
	}
	
	function saveAndNewSpecialRate()
	{
		$this->saveSpecialRate('index.php?option=com_vikappointments&task=newspecialrate');
	}

	function saveAsCopySpecialRate()
	{
		$this->saveSpecialRate('', true);
	}
	
	function saveSpecialRate($return_url = '', $as_copy = false)
	{
		$dbo 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		$args = array();
		$args['name'] 				= $input->getString('name');
		$args['description'] 		= $input->getRaw('description');
		$args['charge']				= abs($input->getFloat('charge')) * $input->getInt('factor', 1);
		$args['people']				= $input->getUint('people', 0);
		$args['published']			= $input->getUint('published', 0);
		$args['weekdays']			= $input->getUint('weekdays', array());
		$args['usergroups']			= $input->getUint('usergroups', array());
		$args['fromdate']			= $input->getString('fromdate');
		$args['todate']				= $input->getString('todate');
		$args['fromtime']			= $input->getUint('fromhour', 0) * 60 + $input->getUint('frommin', 0);
		$args['totime']				= $input->getUint('tohour', 0) * 60 + $input->getUint('tomin', 0);
		$args['params']				= $input->get('params', array(), 'array');
		$args['id'] 				= $as_copy ? -1 : $input->getInt('id', -1);

		// unset people if it should be ignored
		if (!$input->getUint('enablepeople', 0))
		{
			$args['people'] = 0;
		}

		// unset range if the time shouldn't be used
		if (!$input->getUint('usetime', 0) || $args['fromtime'] >= $args['totime'])
		{
			$args['fromtime'] = $args['totime'] = 0;
		}

		// build the weekdays list
		if ($args['weekdays'])
		{
			$args['weekdays'] = implode(',', $args['weekdays']);
		}
		else
		{
			$args['weekdays'] = '';
		}

		// build the usergroups list
		if ($args['usergroups'])
		{
			$args['usergroups'] = implode(',', $args['usergroups']);
		}
		else
		{
			$args['usergroups'] = '';
		}

		// if the date is empty or 'null-date', unset it
		if (!$args['fromdate'] || $args['fromdate'] == $dbo->getNullDate())
		{
			$args['fromdate'] = null;
		}
		// if the date is empty or 'null-date', unset it
		if (!$args['todate'] || $args['todate'] == $dbo->getNullDate())
		{
			$args['todate'] = null;
		}

		if ($args['fromdate'] && $args['todate'])
		{
			// if both the publishing dates are set, make sure 
			// the from date is not higher than the end date
			$args['fromdate'] = VikAppointments::createTimestamp($args['fromdate']);
			$args['todate']   = VikAppointments::createTimestamp($args['todate']);

			if ($args['fromdate'] <= $args['todate'])
			{
				/**
				 * Convert the UNIX timestamps into SQL format.
				 *
				 * @since 1.6.1
				 */
				$args['fromdate'] = JDate::getInstance($args['fromdate'])->toSql();
				$args['todate']   = JDate::getInstance($args['todate'])->toSql();
			}
			else
			{
				$args['fromdate'] = $args['todate'] = null;
			}
		}
		
		// make sure we are not going to save an empty name
		if (empty($args['name']))
		{
			$args['name'] = 'Special Rate';
		}

		/**
		 * Adjust class suffix.
		 *
		 * @since 1.6.2
		 */
		if (!empty($args['params']['class_sfx']))
		{
			$args['params']['class_sfx'] = preg_replace("/^[^a-z]*|[^a-z0-9_\- ]*/i", '', $args['params']['class_sfx']);
		}
		
		// encode params in JSON format before save them
		$args['params'] = json_encode($args['params']);

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsaverate.tab', 'tabname', null, 'string');
		
		if ($args['id'] == -1)
		{
			$args['id'] = $this->saveNewSpecialRate($args, $dbo, $app);
		}
		else
		{
			$this->editSelectedSpecialRate($args, $dbo, $app);
		}

		// get services relations
		$services = $input->getUint('services', array());
		// update the relations
		$this->updateRateServiceAssoc($args['id'], $services, $dbo);
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editspecialrate&cid[]=' . $args['id'];
		}
		
		$app->redirect($return_url);
	}
	
	private function saveNewSpecialRate(array $args, $dbo, $app)
	{
		$rate = (object) $args;
		$rate->createdon = JDate::getInstance()->toSql();

		$res = $dbo->insertObject('#__vikappointments_special_rates', $rate, 'id');

		if ($res && $rate->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWSPECIALRATECREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWSPECIALRATECREATED0'), 'error');
		}
		
		return $rate->id;
	}
	
	private function editSelectedSpecialRate(array $args, $dbo, $app)
	{
		$rate = (object) $args;

		// update NULLs to unset the publishing dates (if needed)
		$dbo->updateObject('#__vikappointments_special_rates', $rate, 'id', true);

		// show always update message as the user may change only the assignments
		$app->enqueueMessage(JText::_('VAPSPECIALRATEEDITED1'));
	}

	private function updateRateServiceAssoc($id, array $records, $dbo)
	{
		if (empty($id))
		{
			return false;
		}

		// get existing records

		$existing = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_service'))
			->from($dbo->qn('#__vikappointments_ser_rates_assoc'))
			->where($dbo->qn('id_special_rate') . ' = ' . $id);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$existing = $dbo->loadColumn();
		}

		// insert new records

		$has = false;

		$q = $dbo->getQuery(true)
			->insert($dbo->qn('#__vikappointments_ser_rates_assoc'))
			->columns($dbo->qn(array('id_special_rate', 'id_service')));

		foreach ($records as $s)
		{
			// make sure the record to push doesn't exist yet
			if (!in_array($s, $existing))
			{
				$q->values($id . ', ' . $s);
				$has = true;
			}
		}

		if ($has)
		{
			$dbo->setQuery($q);
			$dbo->execute();
		}

		// delete records

		$delete = array();

		foreach ($existing as $s)
		{
			// make sure the records to delete is not contained in the selected records
			if (!in_array($s, $records))
			{
				$delete[] = $s;
			}
		}

		if (count($delete))
		{
			$q = $dbo->getQuery(true)
				->delete($dbo->qn('#__vikappointments_ser_rates_assoc'))
				->where(array(
					$dbo->qn('id_special_rate') . ' = ' . $id,
					$dbo->qn('id_service') . ' IN (' . implode(',', $delete) . ')',
				));

			$dbo->setQuery($q);
			$dbo->execute();
		}
	}
	
	/**
	 * SAVE EMPLOYEE
	 */
	
	function saveAndCloseEmployee()
	{
		$this->saveEmployee('index.php?option=com_vikappointments&task=employees');
	}
	
	function saveAndNewEmployee()
	{
		$this->saveEmployee('index.php?option=com_vikappointments&task=newemployee');
	}
	
	function saveEmployee($return_url = '')
	{

		$app   = JFactory::getApplication();
		$dbo   = JFactory::getDbo();
		$input = $app->input;
		
		$args = array();
		$args['firstname'] 		= $input->getString('firstname');
		$args['lastname'] 		= $input->getString('lastname');
		$args['nickname'] 		= $input->getString('nickname');
		$args['alias']			= $input->getString('alias');
		$args['email'] 			= $input->getString('email');
		$args['notify'] 		= $input->getUint('notify', 0);
		$args['showphone'] 		= $input->getUint('showphone', 0);
		$args['quick_contact'] 	= $input->getUint('quick_contact', 0);
		$args['listable'] 		= $input->getUint('listable', 0);
		$args['phone'] 			= $input->getString('phone');
		$args['note'] 			= $input->getRaw('note');
		$args['image'] 			= $input->getString('media');
		$args['synckey'] 		= $input->getString('synckey');
		$args['id_group'] 		= $input->getInt('group', -1);
		$args['jid'] 			= $input->getInt('jid', -1);
		$args['active_to'] 		= $input->getString('active_to', '-1');
		$args['timezone'] 		= $input->getString('timezone');
		$args['id'] 			= $input->getInt('id', -1);


		switch ($input->getString('active_to_type', ''))
		{
			case 'lifetime':
				$args['active_to'] = -1;
				break;

			case 'pending':
				$args['active_to'] = 0;
				break;

			default:
				$args['active_to'] = VikAppointments::jcreateTimestamp($args['active_to'], 0, 0);
		}
		
		if (empty($args['synckey']))
		{
			$args['synckey'] = VikAppointments::generateSerialCode(12);
		}

		if (empty($args['id_group']))
		{
			$args['id_group'] = -1;
		}

		$wd_new = json_decode($input->getString('wdjson'), true);

		if (!is_array($wd_new))
		{
			$wd_new = array();
		}

		$wd_remove = json_decode($input->getString('wdremove'), true);

		if (!is_array($wd_remove))
		{
			$wd_remove = array();
		}

		// get custom fields

		$_cf = VAPCustomFields::getList(1, null, null, CF_EXCLUDE_REQUIRED_CHECKBOX);
		
		$tmp = array(); // used to attach the rules to a dummy var

		$cust_req = VAPCustomFields::loadFromRequest($_cf, $tmp, false);

		if (!empty($tmp['uploads']))
		{
			// inject uploads within the custom fields array
			$cust_req = array_merge($cust_req, $tmp['uploads']);
		}

		foreach ($cust_req as $k => $v)
		{
			// update employee column
			$args['field_' . $k] = $v;
		}

		/**
		 * Create alias.
		 *
		 * @since 1.6
		 */
		if (!$args['alias'])
		{
			$args['alias'] = $args['firstname'] . ' ' . $args['lastname'];
		}

		UILoader::import('libraries.helpers.alias');
		$args['alias'] = AliasHelper::getUniqueAlias($args['alias'], 'employee', $args['id']);

		// validate 
		
		$blank_keys = AppointmentsHelper::validateEmployee($args);

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsaveemp.tab', 'tabname', null, 'string');
		
		if (count($blank_keys) == 0)
		{	
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewEmployee($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedEmployee($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newemployee' : 'editemployee&cid[]=' . $args['id']));
		}

		if ($args['id'] > 0)
		{
			// get services to assign working day
			$services = array();

			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_service'))
				->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
				->where($dbo->qn('id_employee') . ' = ' . $args['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$services = $dbo->loadColumn();
			}

			// insert and update working days
			foreach ($wd_new as $wd)
			{
				$is_new = ($wd['db'] == 0);

				$obj = new stdClass;
				$obj->day 		  = $wd['day'];// matin
				$obj->fromts 	  = $wd['from'];
				$obj->endts 	  = $wd['to'];
				$obj->closed 	  = $wd['closed'];
				$obj->id_employee = $args['id'];

				if ($wd['ts'] == 1)
				{
					/**
					 * Timestamp has to be stored in UTC.
					 *
					 * @since 1.6.1
					 */
					$default_tz = date_default_timezone_get();
					date_default_timezone_set('UTC');

					$obj->ts = VikAppointments::jcreateTimestamp($wd['date'], 0, 0);

					// restore default timezone
					date_default_timezone_set($default_tz);
				}
				else
				{
					$obj->ts = -1;
				}

				if ($is_new)
				{
					$dbo->insertObject('#__vikappointments_emp_worktime', $obj, 'id');

					// set the parent ID to have a link to all the children
					$obj->parent = $obj->id;
					foreach ($services as $s)
					{
						// unset ID to avoid duplicated primary key errors
						unset($obj->id);

						$obj->id_service = $s;
						$dbo->insertObject('#__vikappointments_emp_worktime', $obj, 'id');
					}
				}
				else
				{

					$obj->id = $wd['id'];

					$dbo->updateObject('#__vikappointments_emp_worktime', $obj, 'id');

					// set the parent ID to have a link to all the children
					$obj->parent = $obj->id;
					foreach ($services as $s)
					{
						// unset ID to avoid updating the primary key
						unset($obj->id);

						$dbo->updateObject('#__vikappointments_emp_worktime', $obj, 'parent');
					}
				}
			}

			// remove existing working days
			foreach ($wd_remove as $id)
			{
				$q = $dbo->getQuery(true)
					->delete($dbo->qn('#__vikappointments_emp_worktime'))
					->where(array(
						$dbo->qn('id') . ' = ' . $id,
						$dbo->qn('parent') . ' = ' . $id,
					), 'OR');

				$dbo->setQuery($q);
				$dbo->execute();
			}
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editemployee&cid[]=' . $args['id'];
		}


		
		$app->redirect($return_url);
	}
	
	private function saveNewEmployee(array $args, $dbo, $app)
	{
		$employee = (object) $args;
		
		if (!isset($employee->active_since))
		{
			$employee->active_since = time();
		}

		$res = $dbo->insertObject('#__vikappointments_employee', $employee, 'id');
	
		if ($res && $employee->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWEMPLOYEECREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWEMPLOYEECREATED0'), 'error');
		}
		
		return $employee->id;
	}
	
	private function editSelectedEmployee(array $args, $dbo, $app)
	{
		$employee = (object) $args;

		$res = $dbo->updateObject('#__vikappointments_employee', $employee, 'id');
		
		// show always update message as the user may change only the working days
		$app->enqueueMessage(JText::_('VAPEMPLOYEEEDITED1'));
	}
	
	function saveAndCloseEmployeeRates()
	{
		$this->saveEmployeeRates('index.php?option=com_vikappointments&task=employees');
	}

	function saveEmployeeRates($return_url = '')
	{	
		$app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();
		
		$args = array();
		$args['id']          = $input->getUint('assoc');
		$args['rate']        = $input->getFloat('rate', 0.0);
		$args['duration']    = $input->getUint('duration', 0);
		$args['sleep']       = $input->getInt('sleep', 0);
		$args['description'] = $input->getRaw('description', '');

		$id_emp = $input->getUint('id_emp');
		$id_ser = $input->getUint('id_ser');
		
		if (empty($id_emp) || empty($id_ser))
		{
			$app->enqueueMessage(JText::_('VAPEMPRATESUPDATED0'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=employees');
			exit;
		}

		$override = (object) $args;

		$dbo->updateObject('#__vikappointments_ser_emp_assoc', $override, 'id');

		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPEMPRATESUPDATED1'));
		}

		if (empty($return_url))
		{
			$return_url = "index.php?option=com_vikappointments&task=emprates&id_emp={$id_emp}&id_ser={$id_ser}";
		}

		$app->redirect($return_url);
	}
	
	function duplicateEmployee()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();

		UILoader::import('libraries.helpers.alias');
	
		$cid = $input->getUint('cid', array());
	
		foreach ($cid as $id)
		{
			$lid = 0;
			
			// get employee details

			$employee = array();

			$q = $dbo->getQuery(true);

			$q->select('*')
				->from($dbo->qn('#__vikappointments_employee'))
				->where($dbo->qn('id') . ' = ' . $id);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->loadAssocList())
			{
				$employee = $dbo->loadAssoc();

				// unset ID to avoid duplicated primary key errors
				unset($employee['id']);

				/**
				 * Overwrite sync key to avoid having 2 employees
				 * that share the same password for ICS sync.
				 *
				 * @since 1.6
				 */
				$employee['synckey'] = VikAppointments::generateSerialCode(12);

				/**
				 * Re-generate the alias to avoid duplicates.
				 *
				 * @since 1.6.2
				 */
				$employee['alias'] = AliasHelper::getUniqueAlias($employee['alias'], 'employee');

				// duplicate the employee
				$lid = $this->saveNewEmployee($employee, $dbo, $app);
			}
			
			if ($lid > 0)
			{
				// get employee assigned services

				$q = $dbo->getQuery(true);

				$q->select('*')
					->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
					->where($dbo->qn('id_employee') . ' = ' . $id);

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$list = $dbo->loadObjectList();

					foreach ($list as $assoc)
					{
						// overwrite the employee with the created one
						$assoc->id_employee = $lid;
						// unset ID to avoid duplicated primary key errors
						unset($assoc->id);

						// insert the service assignment
						$dbo->insertObject('#__vikappointments_ser_emp_assoc', $assoc, 'id');
					}
				}

				// get employee working days
				
				$q = $dbo->getQuery(true);

				$q->select('*')
					->from($dbo->qn('#__vikappointments_emp_worktime'))
					->where($dbo->qn('id_employee') . ' = ' . $id);

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$list = $dbo->loadObjectList();
				
					foreach ($list as $wd)
					{
						// overwrite the employee with the created one
						$wd->id_employee = $lid;
						// unset ID to avoid duplicated primary key errors
						unset($wd->id);

						// insert the service assignment
						$dbo->insertObject('#__vikappointments_emp_worktime', $wd, 'id');
					}
				}
			}
		}
		
		$app->redirect('index.php?option=com_vikappointments&task=employees');
	}

	function saveLangEmployee()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['nickname'] 	 = $input->getString('nickname');
		$args['alias'] 	 	 = $input->getString('alias');
		$args['note'] 		 = $input->getRaw('langnote', '');
		$args['tag'] 		 = $input->getString('tag');
		$args['id'] 		 = $input->getInt('id');
		$args['id_employee'] = $input->getInt('id_employee');
		
		if ($args['alias'])
		{
			UILoader::import('libraries.helpers.alias');
			$args['alias'] = AliasHelper::getUniqueAlias($args['alias'], 'employee', $args['id']);
		}

		$lang = (object) $args;

		if ($args['id'] == -1)
		{
			unset($lang->id);
			$dbo->insertObject('#__vikappointments_lang_employee', $lang, 'id');
		}
		else
		{
			unset($lang->tag);
			unset($lang->id_employee);
			$dbo->updateObject('#__vikappointments_lang_employee', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPGROUPLANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelangemployee&tag={$args['tag']}&id_employee={$args['id_employee']}");
	}
	
	/**
	 * SAVE OPTION
	 */
	
	function saveAndCloseOption()
	{
		$this->saveOption('index.php?option=com_vikappointments&task=options');
	}
	
	function saveAndNewOption()
	{
		$this->saveOption('index.php?option=com_vikappointments&task=newoption');
	}
	
	function saveOption($return_url = '')
	{
		$dbo 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		$args = array();
		$args['name'] 			= $input->getString('name', '');
		$args['description'] 	= $input->getString('description');
		$args['price'] 			= $input->getFloat('price', 0.0);
		$args['published'] 		= $input->getUint('published', 0);
		$args['single'] 		= $input->getUint('single', 0);
		$args['maxq'] 			= $input->getUint('maxq', 1);
		$args['required'] 		= $input->getUint('required', 0);
		$args['image'] 			= $input->getString('media');
		$args['displaymode'] 	= $input->getUint('displaymode', 0);
		$args['id'] 			= $input->getInt('id');
		
		if (!$args['single'])
		{
			$args['maxq'] = 1;
		}
		
		$args['maxq'] = max(array($args['maxq'], 1));
		
		if (empty($args['image']))
		{
			$args['displaymode'] = 0;
		}

		$ids_remove = $input->getUint('remove_variation', array());
		
		$blank_keys = AppointmentsHelper::validateOption($args);

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsaveopt.tab', 'tabname', null, 'string');
		
		if (count($blank_keys) == 0)
		{	
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewOption($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedOption($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args["id"] == -1 ? 'newoption' : 'editoption&cid[]='.$args['id']));
			exit;
		}

		$this->handleDelete($ids_remove, '#__vikappointments_option_value', 'id');
		$this->saveOptionValues($args['id'], $dbo, $app);
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editoption&cid[]='.$args['id'];
		}
		
		$app->redirect($return_url);
	}
	
	private function saveNewOption(array $args, $dbo, $app)
	{
		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('ordering'))
			->from($dbo->qn('#__vikappointments_option'))
			->order($dbo->qn('ordering') . ' DESC');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$ordering = (int) $dbo->loadResult() + 1;

		$option = (object) $args;
		$option->ordering = $ordering;

		$res = $dbo->insertObject('#__vikappointments_option', $option, 'id');

		if ($res && $option->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWOPTIONCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWOPTIONCREATED0'), 'error');
		}
		
		return $option->id;
	}
	
	private function editSelectedOption(array $args, $dbo, $app)
	{
		$option = (object) $args;

		$dbo->updateObject('#__vikappointments_option', $option, 'id');

		// show always update message as the user may change only the variations
		$app->enqueueMessage(JText::_('VAPOPTIONEDITED1'));
	}

	private function saveOptionValues($id_option, $dbo, $app)
	{
		$input = $app->input;

		$var_id 	= $input->getInt('var_id', array());
		$var_name 	= $input->getString('var_name', array());
		$var_price 	= $input->getFloat('var_price', array());

		for ($i = 0; $i < count($var_id); $i++)
		{
			$var = new stdClass;
			$var->id 		= $var_id[$i];
			$var->name 		= $var_name[$i];
			$var->inc_price = $var_price[$i];
			$var->ordering 	= $i + 1;
			$var->id_option = $id_option;

			if ($var->id <= 0)
			{
				unset($var->id);
				$dbo->insertObject('#__vikappointments_option_value', $var, 'id');
			}
			else
			{
				$dbo->updateObject('#__vikappointments_option_value', $var, 'id');
			}
		}
	}
	
	function saveLangOption()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['description'] 	= $input->getString('description');
		$args['vars_json'] 		= json_encode($input->get('vars_json', array(), 'array'));
		$args['tag'] 			= $input->getString('tag');
		$args['id'] 			= $input->getInt('id');
		$args['id_option'] 		= $input->getInt('id_option');
		
		$lang = (object) $args;

		if ($args['id'] == -1)
		{
			unset($lang->id);
			$dbo->insertObject('#__vikappointments_lang_option', $lang, 'id');
		}
		else
		{
			unset($lang->tag);
			unset($lang->id_option);
			$dbo->updateObject('#__vikappointments_lang_option', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPOPTLANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelangoption&tag={$args['tag']}&id_option={$args['id_option']}");
	}

	/** 
	 * SAVE EMPLOYEE LOCATION
	 */
	
	function saveAndCloseEmployeeLocation()
	{
		$this->saveEmployeeLocation('index.php?option=com_vikappointments&task=emplocations');
	}

	function saveAndNewEmployeeLocation()
	{
		$this->saveEmployeeLocation('index.php?option=com_vikappointments&task=newemplocation');
	}
	
	function saveEmployeeLocation($return_url = '')
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['id_employee'] 	= $input->getInt('id_employee');
		$args['id_country'] 	= $input->getInt('id_country');
		$args['id_state'] 		= $input->getInt('id_state');
		$args['id_city'] 		= $input->getInt('id_city');
		$args['address'] 		= $input->getString('address');
		$args['zip'] 			= $input->getString('zip');
		$args['latitude'] 		= $input->getString('latitude');
		$args['longitude'] 		= $input->getString('longitude');
		$args['id'] 			= $input->getInt('id');
		
		$blank_keys = AppointmentsHelper::validateEmployeeLocation($args);
		
		if (!strlen($args['latitude']) || !strlen($args['longitude']))
		{
			$args['latitude'] = $args['longitude'] = null;
		}
		else
		{
			$args['latitude'] 	= floatval($args['latitude']);
			$args['longitude'] 	= floatval($args['longitude']);
		}
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewEmployeeLocation($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedEmployeeLocation($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect("index.php?option=com_vikappointments&id_emp={$args['id_employee']}&task=" . ($args['id'] == -1 ? "newemplocation" : "editemplocation&cid[]={$args['id']}"));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editemplocation&cid[]='.$args['id'];
		}

		$return_url .= '&id_emp=' . $args['id_employee'];

		$app->redirect($return_url);
	}
	
	private function saveNewEmployeeLocation(array $args, $dbo, $app)
	{
		$location = (object) $args;

		$res = $dbo->insertObject('#__vikappointments_employee_location', $location, 'id');
			
		if ($res && $location->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWEMPLOCATIONCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWEMPLOCATIONCREATED0'), 'error');
		}
		
		return $location->id;
	}
	
	private function editSelectedEmployeeLocation(array $args, $dbo, $app)
	{
		$location = (object) $args;

		$dbo->updateObject('#__vikappointments_employee_location', $location, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPEMPLOCATIONEDITED1'));
		}
	}
	
	function updateLocationsWorktimesAssoc()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$id_emp = $input->getUint('id_employee', 0);
		
		$locations = $input->get('location', array(), 'array');
		
		foreach ($locations as $id_wd => $id_loc)
		{
			if (empty($id_loc))
			{
				$id_loc = -1;
			}

			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_emp_worktime'))
				->set($dbo->qn('id_location') . ' = ' . (int) $id_loc)
				->where(array(
					$dbo->qn('id') . ' = ' . (int) $id_wd,
					$dbo->qn('parent') . ' = ' . (int) $id_wd,
				), 'OR');

			$dbo->setQuery($q);
			$dbo->execute();
		}
		
		$app->enqueueMessage(JText::_('VAPWDLOCATIONSUPDATED'));
		$app->redirect('index.php?option=com_vikappointments&task=emplocwdays&tmpl=component&id_emp=' . $id_emp);
	}

	/**
	 * SAVE LOCATION
	 */
	
	function saveAndCloseLocation()
	{
		$this->saveLocation('index.php?option=com_vikappointments&task=locations');
	}

	function saveAndNewLocation()
	{
		$this->saveLocation('index.php?option=com_vikappointments&task=newlocation');
	}
	
	function saveLocation($return_url = '')
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['id_employee'] 	= $input->getInt('id_employee');
		$args['id_country'] 	= $input->getInt('id_country');
		$args['id_state'] 		= $input->getInt('id_state');
		$args['id_city'] 		= $input->getInt('id_city');
		$args['address'] 		= $input->getString('address');
		$args['zip'] 			= $input->getString('zip');
		$args['latitude'] 		= $input->getString('latitude');
		$args['longitude'] 		= $input->getString('longitude');
		$args['id'] 			= $input->getInt('id');

		if (empty($args['id_employee']))
		{
			$args['id_employee'] = -1;
		}
		
		$blank_keys = AppointmentsHelper::validateEmployeeLocation($args);
		
		if (!strlen($args['latitude']) || !strlen($args['longitude']))
		{
			$args['latitude'] = $args['longitude'] = null;
		}
		else
		{
			$args['latitude'] 	= floatval($args['latitude']);
			$args['longitude'] 	= floatval($args['longitude']);
		}
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewEmployeeLocation($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedEmployeeLocation($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args["id"] == -1 ? 'newlocation' : 'editlocation&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editlocation&cid[]='.$args['id'];
		}

		$app->redirect($return_url);
	}
	
	/**
	 * SAVE PACKAGE
	 */
	
	function saveAndClosePackage()
	{
		$this->savePackage('index.php?option=com_vikappointments&task=packages');
	}
	
	function saveAndNewPackage()
	{
		$this->savePackage('index.php?option=com_vikappointments&task=newpackage');
	}
	
	function savePackage($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['description'] 	= $input->getRaw('description');
		$args['price'] 			= $input->getFloat('price');
		$args['num_app'] 		= $input->getInt('num_app');
		$args['published'] 		= $input->getInt('published', 0);
		$args['start_ts'] 		= $input->getString('start_ts');
		$args['end_ts'] 		= $input->getString('end_ts');
		$args['level'] 			= $input->getUint('level');
		$args['id_group'] 		= $input->getInt('id_group');
		$args['id_services'] 	= $input->getInt('id_services', array());
		$args['id'] 			= $input->getInt('id');

		$args['num_app'] = max(array($args['num_app'], 1));
		$args['price']	 = abs($args['price']);

		if (!empty($args['start_ts']))
		{
			$args['start_ts'] = VikAppointments::jcreateTimestamp($args['start_ts'], 0, 0);
		}
		
		if (!empty($args['end_ts']))
		{
			$args['end_ts'] = VikAppointments::jcreateTimestamp($args['end_ts'], 23, 59);
		}

		if ($args['start_ts'] >= $args['end_ts'])
		{
			$args['start_ts'] = $args['end_ts'] = -1;
		}
		
		$blank_keys = AppointmentsHelper::validatePackage($args);

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsavepack.tab', 'tabname', null, 'string');
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewPackage($args, $dbo, $app);
				$this->saveNewPackageServiceAssoc($args['id'], $args['id_services'], $dbo);
			}
			else
			{
				$this->editSelectedPackage($args, $dbo, $app);
				$this->updatePackageServiceAssoc($args['id'], $args['id_services'], $dbo);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newpackage' : 'editpackage&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editpackage&cid[]='.$args['id'];
		}
		
		$app->redirect($return_url);
	}

	private function saveNewPackage(array $args, $dbo, $app)
	{
		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('ordering'))
			->from($dbo->qn('#__vikappointments_package'))
			->order($dbo->qn('ordering') . ' DESC');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$ordering = (int) $dbo->loadResult() + 1;

		$package = (object) $args;
		$package->ordering = $ordering;

		$dbo->insertObject('#__vikappointments_package', $package, 'id');

		if ($package->id > 0)
		{
			$app->enqueueMessage(JText::_('VAPNEWPACKAGECREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWPACKAGECREATED0'), 'error');
		}
		
		return $package->id;
	}
	
	private function editSelectedPackage(array $args, $dbo, $app)
	{
		$package = (object) $args;	
		
		$dbo->updateObject('#__vikappointments_package', $package, 'id');

		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPPACKAGEEDITED1'));
		}
	}

	private function saveNewPackageServiceAssoc($id, $services, $dbo)
	{
		if (empty($id))
		{
			return false;
		}

		$q = $dbo->getQuery(true);

		$q->insert($dbo->qn('#__vikappointments_package_service'))
			->columns($dbo->qn(array('id_package', 'id_service')));

		foreach ($services as $s) {
			$q->values($id . ', ' . $s);
		}

		$dbo->setQuery($q);
		$dbo->execute();
	}

	private function updatePackageServiceAssoc($id, $services, $dbo)
	{
		if (empty($id))
		{
			return false;
		}

		// get existing services

		$existing = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_service'))
			->from($dbo->qn('#__vikappointments_package_service'))
			->where($dbo->qn('id_package') . ' = ' . $id);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$existing = $dbo->loadColumn();
		}

		// insert new services

		$has = false;

		$q = $dbo->getQuery(true)
			->insert($dbo->qn('#__vikappointments_package_service'))
			->columns($dbo->qn(array('id_package', 'id_service')));

		foreach ($services as $s)
		{
			// make sure the service to push doesn't exist yet
			if (!in_array($s, $existing))
			{
				$q->values($id . ', ' . $s);
				$has = true;
			}
		}

		if ($has)
		{
			$dbo->setQuery($q);
			$dbo->execute();
		}

		// delete services

		$delete = array();

		foreach ($existing as $s)
		{
			// make sure the service to delete is not contained in the selected services
			if (!in_array($s, $services))
			{
				$delete[] = $s;
			}
		}

		if (count($delete))
		{
			$q = $dbo->getQuery(true)
				->delete($dbo->qn('#__vikappointments_package_service'))
				->where(array(
					$dbo->qn('id_package') . ' = ' . $id,
					$dbo->qn('id_service') . ' IN (' . implode(',', $delete) . ')',
				));

			$dbo->setQuery($q);
			$dbo->execute();
		}
	}

	public function duplicatePackage()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$cid = $input->getUint('cid', array());

		foreach ($cid as $id_package)
		{
			$q = $dbo->getQuery(true)
				->select('`p`.*')->select($dbo->qn('a.id_service'))
				->from($dbo->qn('#__vikappointments_package', 'p'))
				->leftjoin($dbo->qn('#__vikappointments_package_service', 'a') . ' ON ' . $dbo->qn('p.id') . ' = ' . $dbo->qn('a.id_package'))
				->where($dbo->qn('p.id') . ' = ' . $id_package);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$rows = $dbo->loadAssocList();

				$services = array();
				foreach ($rows as $a)
				{
					if (!empty($a['id_service']))
					{
						$services[] = $a['id_service'];
					}
				}

				$args = $rows[0];

				$args['name'] .= ' (copy)';
				unset($args['id']);
				unset($args['id_service']);

				$new_id = $this->saveNewPackage($args, $dbo, $app);
				$this->saveNewPackageServiceAssoc($new_id, $services, $dbo);

				// duplicate translations too
				$q = $dbo->getQuery(true)
					->select('*')
					->from($dbo->qn('#__vikappointments_lang_package'))
					->where($dbo->qn('id_package') . ' = ' . $id_package);

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					foreach ($dbo->loadObjectList() as $lang)
					{
						// unset ID for insert and switch package ID
						unset($lang->id);
						$lang->id_package = $new_id;

						$dbo->insertObject('#__vikappointments_lang_package', $lang, 'id');
					}
				}
			}
		}

		$app->redirect('index.php?option=com_vikappointments&task=packages');
	}

	function saveLangPackage()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['description'] 	= $input->getRaw('langdesc');
		$args['tag'] 			= $input->getString('tag');
		$args['id'] 			= $input->getInt('id');
		$args['id_package'] 	= $input->getInt('id_package');
		
		$lang = (object) $args;

		if ($args['id'] == -1)
		{
			unset($lang->id);

			$dbo->insertObject('#__vikappointments_lang_package', $lang, 'id');
		}
		else
		{
			unset($lang->tag);
			unset($lang->id_package);

			$dbo->updateObject('#__vikappointments_lang_package', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPPACKAGELANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelangpackage&tag={$args['tag']}&id_package={$args['id_package']}");		
	}

	/**
	 * SAVE PACKAGE GROUP
	 */
	
	function saveAndClosePackageGroup()
	{
		$this->savePackageGroup('index.php?option=com_vikappointments&task=packgroups');
	}
	
	function saveAndNewPackageGroup()
	{
		$this->savePackageGroup('index.php?option=com_vikappointments&task=newpackgroup');
	}
	
	function savePackageGroup($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['title'] 			= $input->getString('title');
		$args['description'] 	= $input->getRaw('description');
		$args['id'] 			= $input->getInt('id');
		
		$blank_keys = AppointmentsHelper::validatePackageGroup($args);
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewPackageGroup($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedPackageGroup($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newpackgroup' : 'editpackgroup&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editpackgroup&cid[]='.$args['id'];
		}
		
		$app->redirect($return_url);
	}

	private function saveNewPackageGroup(array $args, $dbo, $app)
	{
		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('ordering'))
			->from($dbo->qn('#__vikappointments_package_group'))
			->order($dbo->qn('ordering') . ' DESC');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$ordering = (int) $dbo->loadResult() + 1;

		$group = (object) $args;
		$group->ordering = $ordering;

		$dbo->insertObject('#__vikappointments_package_group', $group, 'id');
				
		if ($group->id > 0)
		{
			$app->enqueueMessage(JText::_('VAPNEWPACKGROUPCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWPACKGROUPCREATED0'), 'error');
		}
		
		return $group->id;
	}
	
	private function editSelectedPackageGroup(array $args, $dbo, $app)
	{
		$group = (object) $args;

		$dbo->updateObject('#__vikappointments_package_group', $group, 'id');	
		
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPPACKGROUPEDITED1'));
		}
	}

	function saveLangPackageGroup()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['title'] 				= $input->getString('title');
		$args['description'] 		= $input->getRaw('langdesc');
		$args['tag'] 				= $input->getString('tag');
		$args['id'] 				= $input->getInt('id');
		$args['id_package_group'] 	= $input->getInt('id_package_group');

		$lang = (object) $args;
		
		if ($args['id'] == -1)
		{
			unset($lang->id);

			$dbo->insertObject('#__vikappointments_lang_package_group', $lang, 'id');
		}
		else
		{
			unset($lang->id_package_group);
			unset($lang->tag);

			$dbo->updateObject('#__vikappointments_lang_package_group', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPPACKGROUPLANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelangpackgroup&tag={$args['tag']}&id_package_group={$args['id_package_group']}");
	}

	/**
	 * SAVE PACKAGE ORDER
	 */
	
	function saveAndClosePackageOrder()
	{
		$this->savePackageOrder('index.php?option=com_vikappointments&task=packorders');
	}
	
	function saveAndNewPackageOrder()
	{
		$this->savePackageOrder('index.php?option=com_vikappointments&task=newpackorder');
	}
	
	function savePackageOrder($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['id_user'] 				= $input->getInt('id_user', 0);
		$args['purchaser_nominative'] 	= $input->getString('purchaser_nominative');
		$args['purchaser_mail'] 		= $input->getString('purchaser_mail');
		$args['purchaser_phone'] 		= $input->getString('purchaser_phone');
		$args['purchaser_prefix'] 		= $input->getString('phone_prefix');
		$args['id_payment'] 			= $input->getInt('id_payment', -1);
		$args['total_cost'] 			= $input->getFloat('total_cost');
		$args['status'] 				= $input->getString('status');
		$args['id'] 					= $input->getInt('id', -1);

		$args['purchaser_prefix']  = '';
		$args['purchaser_country'] = '';

		$p_name 		= "";
		$p_mail 		= "";
		$p_phone 		= "";
		$p_prefix 		= "";
		$p_country_code = "";

		// user data
		
		$user_arr = array();

		$q = $dbo->getQuery(true)
			->select('`u`.*')->select($dbo->qn('c.phone_prefix'))
			->from($dbo->qn('#__vikappointments_users', 'u'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.country_2_code') . ' = ' . $dbo->qn('u.country_code'))
			->where($dbo->qn('u.id') . ' = ' . $args['id_user']);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$user_arr = $dbo->loadAssoc();
		}

		// custom fields

		$_cf = VAPCustomFields::getList(0, 0, 0, CF_EXCLUDE_REQUIRED_CHECKBOX | CF_EXCLUDE_FILE);

		// get custom fields from request
		
		$tmp = array(); // used to attach the rules to a dummy var

		$cust_req = VAPCustomFields::loadFromRequest($_cf, $tmp, false);

		if (!empty($user_arr['id']))
		{
			if (empty($p_name))
			{
				$p_name = $user_arr['billing_name'];
			}
			
			if (empty($p_mail))
			{
				$p_mail = $user_arr['billing_mail'];
			}

			if (empty($p_phone))
			{
				$p_phone 		= $user_arr['billing_phone'];
				$p_prefix 		= $user_arr['phone_prefix'];
				$p_country_code = $user_arr['country_code'];
			}
		}
		
		if (empty($args['purchaser_nominative']))
		{
			$args['purchaser_nominative'] = $p_name;
		}
		
		if (empty( $args['purchaser_mail']))
		{
			$args['purchaser_mail'] = $p_mail;
		}
		
		if (empty($args['purchaser_phone']))
		{
			$args['purchaser_phone'] = $p_phone;
		}
		
		if ((empty($p_prefix) || empty($p_country_code)) && !empty($args['purchaser_phone']))
		{
			$country_key = $args['purchaser_prefix'];
			if (!empty($country_key))
			{
				$country_key = explode('_', $country_key);
				$country = VikAppointmentsLocations::getCountryFromCode($country_key[1]);
				if ($country !== false)
				{
					$p_prefix = $country['phone_prefix'];
					$p_country_code = $country['country_2_code'];
				}
			}
		}
		
		$args['purchaser_prefix'] 	= $p_prefix;
		$args['purchaser_country'] 	= $p_country_code;

		$args['custom_f'] = json_encode($cust_req);

		// validation
		if ($args['id_user'] <= 0) {
			$args['id_user'] = '';
		}

		$args['total_cost'] = abs($args['total_cost']);

		$blank_keys = AppointmentsHelper::validatePackageOrder($args);

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsavepackord.tab', 'tabname', null, 'string');
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['sid'] 	= VikAppointments::generateSerialCode(16);
				$args['id'] 	= $this->saveNewPackageOrder($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedPackageOrder($args, $dbo, $app);

				$q = $dbo->getQuery(true)
					->select($dbo->qn('sid'))
					->from($dbo->qn('#__vikappointments_package_order'))
					->where($dbo->qn('id') . ' = ' . $args['id']);

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				$args['sid'] = $dbo->loadResult();
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newpackorder' : 'editpackorder&cid[]='.$args['id']));
		}

		// remove order items
		$remove_packages = $input->getUint('remove_package', array());

		if (count($remove_packages))
		{
			$q = $dbo->getQuery(true)
				->delete($dbo->qn('#__vikappointments_package_order_item'))
				->where($dbo->qn('id') . ' IN (' . implode(',', $remove_packages) . ')');

			$dbo->setQuery($q);
			$dbo->execute();
		}

		// insert and update order items
		$packages = array();
		$packages['id_package'] = $input->getInt('id_package', array());
		$packages['num_app'] 	= $input->getInt('num_app', array());
		$packages['used_app'] 	= $input->getInt('used_app', array());
		$packages['quantity'] 	= $input->getInt('quantity', array());
		$packages['price'] 		= $input->getFloat('price', array());
		$packages['id_assoc'] 	= $input->getInt('id_assoc', array());

		for ($i = 0; $i < count($packages['id_package']); $i++)
		{
			$pack = array(
				'id_order' 	 => $args['id'],
				'id_package' => $packages['id_package'][$i],
				'num_app' 	 => max(array(1, $packages['num_app'][$i])),
				'used_app' 	 => abs($packages['used_app'][$i]),
				'quantity' 	 => max(array(1, $packages['quantity'][$i])),
				'price' 	 => abs($packages['price'][$i]),
				'id' 		 => $packages['id_assoc'][$i],
			);

			if ($pack['id'] <= 0)
			{
				$this->insertPackageOrderItem($pack, $dbo);
			}
			else
			{
				$this->updatePackageOrderItem($pack, $dbo);
			}
		}

		// e-mail
		if ($input->getBool('notifycust'))
		{
			$order_details = VikAppointments::fetchPackagesOrderDetails($args['id'], $args['sid']);
			VikAppointments::sendPackagesCustomerEmail($order_details, true);
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editpackorder&cid[]='.$args['id'];
		}
		
		$app->redirect($return_url);
	}

	private function saveNewPackageOrder(array $args, $dbo, $app)
	{
		$order = (object) $args;
		$order->createdon = time();
		$order->createdby = JFactory::getUser()->id;
		unset($order->id_assoc);

		$res = $dbo->insertObject('#__vikappointments_package_order', $order, 'id');
				
		if ($res && $order->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWPACKORDERCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWPACKORDERCREATED0'), 'error');
		}
		
		return $order->id;
	}
	
	private function editSelectedPackageOrder(array $args, $dbo, $app)
	{
		$order = (object) $args;

		$dbo->updateObject('#__vikappointments_package_order', $order, 'id');

		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPPACKORDEREDITED1'));
		}
	}

	private function insertPackageOrderItem(array $args, $dbo)
	{
		$item = (object) $args;
		$item->modifiedon 	= ($item->used_app > 0 ? time() : -1);
		$item->num_app 		*= $item->quantity;

		$dbo->insertObject('#__vikappointments_package_order_item', $item, 'id');
	}
	
	private function updatePackageOrderItem(array $args, $dbo)
	{
		$total = $args['num_app'] * $args['quantity'];

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_package_order_item'))
			->set(array(
				$dbo->qn('used_app') . ' = ' . $args['used_app'],
				$dbo->qn('num_app') . ' = (' . $total . ') / ' . $dbo->qn('quantity'),
				$dbo->qn('quantity') . ' = ' . $args['quantity'],
				$dbo->qn('price') . ' = ' . $args['price'],
			))
			->where($dbo->qn('id') . ' = ' . $args['id']);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getAffectedRows())
		{
			$upd = new stdClass;
			$upd->modifiedon = time();
			$upd->id 		 = $args['id'];

			$dbo->updateObject('#__vikappointments_package_order_item', $upd, 'id');
		}
	}

	/**
	 * SAVE RESERVATION
	 */
	 
	function saveAndCloseReservation()
	{
		$task = JFactory::getApplication()->input->get('from');

		if (!$task)
		{
			$task = 'reservations';
		}

		$this->saveReservation('index.php?option=com_vikappointments&task=' . $task);
	}
	
	function saveAndNewReservation()
	{
		$this->saveReservation('index.php?option=com_vikappointments&task=findreservation');
	}
	 
	function saveReservation($return_url = '')
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsaveres.tab', 'tabname', null, 'string');
		
		$args = array();
		$args['id_employee'] 			= $input->getInt('id_employee');
		$args['id_service'] 			= $input->getInt('id_service');
		$args['checkin_ts'] 			= $input->getInt('checkin_ts');
		$args['people'] 				= $input->getInt('people', 1);
		$args['purchaser_nominative'] 	= $input->getString('purchaser_nominative');
		$args['purchaser_mail'] 		= $input->getString('purchaser_mail');
		$args['purchaser_phone'] 		= $input->getString('purchaser_phone');
		$args['purchaser_prefix']		= $input->getString('phone_prefix');
		$args['purchaser_country'] 		= "";
		// $args['coupon_str'] 			= $input->getString('coupon_str');
		$args['total_cost'] 			= $input->getFloat('total_cost', 0);
		$args['duration'] 				= $input->getInt('duration', 0);
		$args['paid'] 					= $input->getInt('paid', 0);
		$args['status'] 				= $input->getString('status');
		$args['id_payment'] 			= $input->getInt('id_payment', 0);
		$args['notes'] 					= $input->getRaw('notes', '');
		$args['id_user'] 				= $input->getInt('id_user', -1);
		$args['id'] 					= $input->getInt('id');

		$coupon_id = $input->getInt('coupon', 0);
		
		$new_opt_id 	= $input->get('new_opt_id', array(), 'array');
		$new_opt_var 	= $input->get('new_opt_var', array(), 'array');
		$new_opt_price 	= $input->get('new_opt_price', array(), 'array');
		$new_opt_time 	= $input->get('new_opt_time', array(), 'array');
		$new_opt_quant 	= $input->get('new_opt_quant', array(), 'array');
		$del_opt 		= $input->get('del_opt_id', array(), 'array');
		
		$p_name 		= "";
		$p_mail 		= "";
		$p_phone 		= "";
		$p_prefix 		= "";
		$p_country_code = "";
		
		// get custom fields

		$_cf = VAPCustomFields::getList(0, $args['id_employee'], $args['id_service'], CF_EXCLUDE_REQUIRED_CHECKBOX);

		// get user fields
		
		$user_arr = array();

		$q = $dbo->getQuery(true)
			->select('`u`.*')->select($dbo->qn('c.phone_prefix'))
			->from($dbo->qn('#__vikappointments_users', 'u'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.country_2_code') . ' = ' . $dbo->qn('u.country_code'))
			->where($dbo->qn('u.id') . ' = ' . $args['id_user']);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$user_arr = $dbo->loadAssoc();
		}

		// get custom fields from request
		
		$tmp = array(); // used to attach the rules to a dummy var

		$cust_req = VAPCustomFields::loadFromRequest($_cf, $tmp, false);

		if (!empty($user_arr['id']))
		{
			if (empty($p_name))
			{
				$p_name = $user_arr['billing_name'];
			}
			
			if (empty($p_mail))
			{
				$p_mail = $user_arr['billing_mail'];
			}

			if (empty($p_phone))
			{
				$p_phone 		= $user_arr['billing_phone'];
				$p_prefix 		= $user_arr['phone_prefix'];
				$p_country_code = $user_arr['country_code'];
			}
		}
		
		if (empty($args['purchaser_nominative']))
		{
			$args['purchaser_nominative'] = $p_name;
		}
		
		if (empty( $args['purchaser_mail']))
		{
			$args['purchaser_mail'] = $p_mail;
		}
		
		if (empty($args['purchaser_phone']))
		{
			$args['purchaser_phone'] = $p_phone;
		}
		
		if ((empty($p_prefix) || empty($p_country_code)) && !empty($args['purchaser_phone']))
		{
			$country_key = $args['purchaser_prefix'];
			if (!empty($country_key))
			{
				$country_key = explode('_', $country_key);
				$country = VikAppointmentsLocations::getCountryFromCode($country_key[1]);
				if ($country !== false)
				{
					$p_prefix = $country['phone_prefix'];
					$p_country_code = $country['country_2_code'];
				}
			}
		}
		
		$args['purchaser_prefix'] 	= $p_prefix;
		$args['purchaser_country'] 	= $p_country_code;

		$args['custom_f'] = json_encode($cust_req);
		
		// get service parameters

		$sleep 		  = 0;
		$max_capacity = 1;

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('sleep', 'max_capacity')))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . $args['id_service']);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$service = $dbo->loadAssoc();
			$sleep  		= $service['sleep'];
			$max_capacity 	= $service['max_capacity'];
		}

		/**
		 * Update sleep time while saving a reservation
		 * from the back-end too.
		 *
		 * @since 1.6.2
		 */
		$args['sleep'] = $sleep;
		
		// validate selected payment

		$payment = VikAppointments::getPayment($args['id_payment'], false);
		
		if (!$payment)
		{
			$args['id_payment'] = -1;
		}

		// validate availability

		$timezone = VikAppointments::getEmployeeTimezone($args['id_employee']);
		VikAppointments::setCurrentTimezone($timezone);
		
		$valid = VikAppointments::isEmployeeAvailableFor($args['id_employee'], $args['id_service'], $args['id'], $args['checkin_ts'], $args['duration']+$sleep, $args['people'], $max_capacity, $dbo);
		
		if ($valid != 1)
		{
			$date = getdate($args['checkin_ts']);
			$attach_url = "&id_emp={$args['id_employee']}&id_ser={$args['id_service']}&day=".mktime(0,0,0,$date['mon'],$date['mday'],$date['year'])."&hour={$date['hours']}&min={$date['minutes']}";
			
			$err_key = 'VAPRESDATETIMENOTAVERR';
			if ($valid == -1)
			{
				$err_key = 'VAPRESOUTOFCLOSINGTIME';
			}
			
			$app->enqueueMessage(JText::_($err_key), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newreservation' . $attach_url : 'editreservation&cid[]=' . $args['id']));
		}

		// get coupon code

		if (!empty($coupon_id))
		{
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_coupon'))
				->where($dbo->qn('id') . ' = ' . $coupon_id);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$coupon = $dbo->loadAssoc();
				$args['coupon_str'] = "{$coupon['code']};;{$coupon['percentot']};;{$coupon['value']}";

				VikAppointments::couponUsed($coupon, $dbo);
			}
		}

		$args['locked_until'] = time() + UIFactory::getConfig()->getUint('keepapplock') * 60;
		
		if ($args['id'] == -1)
		{
			$args['sid'] = VikAppointments::generateSerialCode(16);
			$args['id']  = $this->saveNewReservation($args, $dbo, $app);
		}
		else
		{
			$orderStatus = VAPOrderStatus::getInstance();

			// get previous order status
			$args['old_status'] = $orderStatus->getStatus($args['id']);

			// register status update before altering the database
			$comment = $input->getString('comment');

			if (!$comment)
			{
				$comment = 'VAP_STATUS_CHANGED_ON_MANAGE';
			}

			$orderStatus->change($args['status'], $args['id'], $comment);
			//

			$this->editSelectedReservation($args, $dbo, $app);

			// retrieve SID

			$q = $dbo->getQuery(true)
				->select($dbo->qn('sid'))
				->from($dbo->qn('#__vikappointments_reservation'))
				->where($dbo->qn('id') . ' = ' . $args['id']);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			$args['sid'] = $dbo->loadResult();			
		}

		// if the record has been created/updated, check for the options
		
		if ($args['id'] > 0)
		{
			// delete options

			$this->handleDelete($del_opt, '#__vikappointments_res_opt_assoc', 'id');

			// insert/update options
			
			for ($i = 0; $i < count($new_opt_id); $i++)
			{
				$q = $dbo->getQuery(true)
					->select($dbo->qn(array('id', 'inc_price', 'quantity')))
					->from($dbo->qn('#__vikappointments_res_opt_assoc'))
					->where(array(
						$dbo->qn('id_reservation') . ' = ' . $args['id'],
						$dbo->qn('id_option') . ' = ' . (int) $new_opt_id[$i],
					));

				if ($new_opt_var[$i] > 0)
				{
					$q->where($dbo->qn('id_variation') . ' = ' . (int) $new_opt_var[$i]);
				}

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				$final_opt_price = (float) $new_opt_price[$i] * (int) $new_opt_quant[$i];

				if ($dbo->getNumRows())
				{
					$obj = $dbo->loadObject();
					$obj->inc_price += $final_opt_price;
					$obj->quantity  += (int) $new_opt_quant[$i];

					$dbo->updateObject('#__vikappointments_res_opt_assoc', $obj, 'id');
				}
				else
				{
					$obj = new stdClass;
					$obj->id_reservation 	= $args['id'];
					$obj->id_option 		= (int) $new_opt_id[$i];
					$obj->id_variation 		= (int) $new_opt_var[$i];
					$obj->inc_price 		= $final_opt_price;
					$obj->quantity 			= (int) $new_opt_quant[$i];

					$dbo->insertObject('#__vikappointments_res_opt_assoc', $obj, 'id');
				}
			}	
		}

		$order_details = null;
		
		// send customer email

		if ($input->getBool('notifycust'))
		{
			if (!$order_details)
			{
				$order_details = VikAppointments::fetchOrderDetails($args['id'], $args['sid']);
			}

			VikAppointments::sendCustomerEmail($order_details);
		}

		// send admin email

		if ($input->getBool('notifyemp'))
		{
			$order_details_original = VikAppointments::fetchOrderDetails($args['id'], $args['sid'], VikAppointments::getDefaultLanguage('site'));

			VikAppointments::sendAdminEmail($order_details_original);
		}

		// send waiting list notification

		if ($input->getBool('notifywl') && $args['status'] == 'CANCELED')
		{
			if (!$order_details)
			{
				$order_details = VikAppointments::fetchOrderDetails($args['id'], $args['sid']);
			}

			VikAppointments::notifyCustomersInWaitingList($order_details);
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editreservation&cid[]=' . $args['id'];
		}

		$app->redirect($return_url);
	}

	private function saveNewReservation(array $args, $dbo, $app = null)
	{
		$reservation = (object) $args;

		$reservation->createdon = time();
		$reservation->createdby = JFactory::getUser()->id;

		/**
		 * Count the remaining services to check whether a package can be used.
		 * The package can be redeemed only in case the number of participants
		 * doesn't exceed the number of remaining slots.
		 *
		 * @since 1.6.3
		 */
		$count = VikAppointments::countRemainingServicePackages($reservation->id_service, $reservation->id_user);

		if ($count >= $reservation->people)
		{
			// unset reservation cost
			$reservation->total_cost = 0;
		}

		$res = $dbo->insertObject('#__vikappointments_reservation', $reservation, 'id');
				
		if ($res && $reservation->id)
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_reservation'))
				->set($dbo->qn('id_parent') . ' = ' . $dbo->qn('id'))
				->where($dbo->qn('id') . ' = ' . $reservation->id);

			$dbo->setQuery($q);
			$dbo->execute();
			
			if ($app)
			{
				$app->enqueueMessage(JText::_('VAPNEWRESERVATIONCREATED1'));
			}

			if ($count >= $reservation->people)
			{
				// get order details
				$order = VikAppointments::fetchOrderDetails($reservation->id);
				// redeem package
				$redeemed = VikAppointments::registerPackagesUsed($order);

				if ($redeemed)
				{
					// register the status to inform the admin that a package have been redeemed
					VAPOrderStatus::getInstance()->keepTrack($reservation->status, $reservation->id, 'VAP_STATUS_PACKAGE_REDEEMED');
				}

				if ($app && $redeemed)
				{
					$app->enqueueMessage(JText::sprintf('VAPORDERREDEEMEDPACKS', $redeemed));
				}
			}
		}
		else
		{
			if ($app)
			{
				$app->enqueueMessage(JText::_('VAPNEWRESERVATIONCREATED0'), 'error');
			}
		}
		
		return $reservation->id;
	}
	
	private function editSelectedReservation(array $args, $dbo, $app = null)
	{
		$reservation = (object) $args;
		
		/**
		 * Try to unredeem a package if the order has been cancelled.
		 * The cost must be zero too in order to prove that a package was redeemed.
		 *
		 * @since 1.6.3
		 */
		if ($reservation->status == 'CANCELED'
			&& $reservation->old_status == 'CONFIRMED'
			&& $reservation->total_cost == 0)
		{
			// get order details
			$order = VikAppointments::fetchOrderDetails($reservation->id);

			// unredeem packages
			$unredeemed = VikAppointments::registerPackagesUsed($order, $increase = false);

			if ($unredeemed && $app)
			{
				$app->enqueueMessage(JText::sprintf('VAPORDERUNREDEEMEDPACKS', $unredeemed));
			}
		}

		// unset reservation status
		unset($reservation->old_status);

		$dbo->updateObject('#__vikappointments_reservation', $reservation, 'id');
			
		if ($dbo->getAffectedRows() && $app)
		{
			$app->enqueueMessage(JText::_('VAPRESERVATIONEDITED1'));
		}
	}
	
	function notifyCustomer($oid = null)
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		if (is_null($oid))
		{
			$oid = $input->getString('ordnum');
			$redirect = true;
		}
		else
		{
			$redirect = false;
		}

		$q = $dbo->getQuery(true)
			->select($dbo->qn('sid'))
			->from($dbo->qn('#__vikappointments_reservation'))
			->where($dbo->qn('id') . ' = ' . $dbo->q($oid));
		
		$dbo->setQuery($q);
		$dbo->execute();

		$sid = $dbo->loadResult();

		if (!empty($sid))
		{
			$order_details = VikAppointments::fetchOrderDetails($oid, $sid);
			VikAppointments::sendCustomerEmail($order_details);
			$app->enqueueMessage(JText::_('VAPNOTIFYCUSTOK'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNOTIFYCUSTERR'), 'error');
		}
		
		if ($redirect)
		{
			$app->redirect('index.php?option=com_vikappointments&task=reservations');
		}
	}

	/**
	 * SAVE MULTIPLE ORDER
	 */

	function saveAndCloseMultiOrder()
	{
		$this->saveMultiOrder('index.php?option=com_vikappointments&task=reservations');
	}

	function saveMultiOrder($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsaveres.tab', 'tabname', null, 'string');
		
		$args = array();
		$args['purchaser_nominative'] 	= $input->getString('purchaser_nominative');
		$args['purchaser_mail'] 		= $input->getString('purchaser_mail');
		$args['purchaser_phone'] 		= $input->getString('purchaser_phone');
		$args['purchaser_prefix']		= $input->getString('phone_prefix');
		$args['purchaser_country'] 		= "";
		$args['total_cost'] 			= $input->getFloat('total_cost', 0);
		$args['paid'] 					= $input->getInt('paid', 0);
		$args['status'] 				= $input->getString('status');
		$args['id_payment'] 			= $input->getInt('id_payment', 0);
		$args['notes'] 					= $input->getRaw('notes', '');
		$args['id_user'] 				= $input->getInt('id_user', -1);
		$args['id'] 					= $input->getInt('id');

		$employee_id = $input->getUint('id_employee');
		$service_id  = $input->getUint('id_service', array()); // can be an array
		$coupon_id 	 = $input->getInt('coupon', 0);
		
		$new_opt_id 	= $input->get('new_opt_id', array(), 'array');
		$new_opt_var 	= $input->get('new_opt_var', array(), 'array');
		$new_opt_price 	= $input->get('new_opt_price', array(), 'array');
		$new_opt_time 	= $input->get('new_opt_time', array(), 'array');
		$new_opt_quant 	= $input->get('new_opt_quant', array(), 'array');
		$del_opt 		= $input->get('del_opt_id', array(), 'array');
		
		$p_name 		= "";
		$p_mail 		= "";
		$p_phone 		= "";
		$p_prefix 		= "";
		$p_country_code = "";
		
		// get custom fields

		$_cf = VAPCustomFields::getList(0, $employee_id, $service_id, CF_EXCLUDE_REQUIRED_CHECKBOX);

		// get user fields
		
		$user_arr = array();

		$q = $dbo->getQuery(true)
			->select('`u`.*')->select($dbo->qn('c.phone_prefix'))
			->from($dbo->qn('#__vikappointments_users', 'u'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('c.country_2_code') . ' = ' . $dbo->qn('u.country_code'))
			->where($dbo->qn('u.id') . ' = ' . $args['id_user']);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$user_arr = $dbo->loadAssoc();
		}

		// get custom fields from request
		
		$tmp = array(); // used to attach the rules to a dummy var

		$cust_req = VAPCustomFields::loadFromRequest($_cf, $tmp, false);

		if (!empty($user_arr['id']))
		{
			if (empty($p_name))
			{
				$p_name = $user_arr['billing_name'];
			}
			
			if (empty($p_mail))
			{
				$p_mail = $user_arr['billing_mail'];
			}

			if (empty($p_phone))
			{
				$p_phone 		= $user_arr['billing_phone'];
				$p_prefix 		= $user_arr['phone_prefix'];
				$p_country_code = $user_arr['country_code'];
			}
		}
		
		if (empty($args['purchaser_nominative']))
		{
			$args['purchaser_nominative'] = $p_name;
		}
		
		if (empty( $args['purchaser_mail']))
		{
			$args['purchaser_mail'] = $p_mail;
		}
		
		if (empty($args['purchaser_phone']))
		{
			$args['purchaser_phone'] = $p_phone;
		}
		
		if ((empty($p_prefix) || empty($p_country_code)) && !empty($args['purchaser_phone']))
		{
			$country_key = $args['purchaser_prefix'];
			if (!empty($country_key))
			{
				$country_key = explode('_', $country_key);
				$country = VikAppointmentsLocations::getCountryFromCode($country_key[1]);
				if ($country !== false)
				{
					$p_prefix = $country['phone_prefix'];
					$p_country_code = $country['country_2_code'];
				}
			}
		}
		
		$args['purchaser_prefix'] 	= $p_prefix;
		$args['purchaser_country'] 	= $p_country_code;

		$args['custom_f'] = json_encode($cust_req);
		
		// validate selected payment

		$payment = VikAppointments::getPayment($args['id_payment'], false);
		
		if (!$payment)
		{
			$args['id_payment'] = -1;
		}

		// get coupon code

		if (!empty($coupon_id))
		{
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikappointments_coupon'))
				->where($dbo->qn('id') . ' = ' . $coupon_id);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$coupon = $dbo->loadAssoc();
				$args['coupon_str'] = "{$coupon['code']};;{$coupon['percentot']};;{$coupon['value']}";

				VikAppointments::couponUsed($coupon, $dbo);
			}
		}

		$args['locked_until'] = time() + UIFactory::getConfig()->getUint('keepapplock') * 60;

		$orderStatus = VAPOrderStatus::getInstance();

		// Use the same status because we don't want to restore the packages
		// in case only the multi-order record is CANCELLED.
		// Packages will be restored one-by-one (if needed) during the
		// update of children records.
		$args['old_status'] = $args['status'];

		// register status update before altering the database
		$comment = $input->getString('comment');

		if (!$comment)
		{
			$comment = 'VAP_STATUS_CHANGED_ON_MANAGE';
		}

		$orderStatus->change($args['status'], $args['id'], $comment);
		//

		$this->editSelectedReservation($args, $dbo, $app);

		if ($input->getBool('updatechild'))
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id'))
				->from($dbo->qn('#__vikappointments_reservation'))
				->where($dbo->qn('id_parent') . ' = ' . $args['id']);

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				foreach ($dbo->loadColumn() as $id)
				{
					$child = $args;
					$child['id'] = $id;

					// get previous order status
					$child['old_status'] = $orderStatus->getStatus($child['id']);

					// do not pass $app to avoid registering duplicated messages
					$this->editSelectedReservation($child, $dbo);
				}
			}
		}

		// send customer email

		if ($input->getBool('notifycust'))
		{
			$this->notifyCustomer($args['id']);
		}

		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editreservation&cid[]=' . $args['id'];
		}

		$app->redirect($return_url);
	}

	/**
	 * SAVE WAITING CUSTOMER
	 */
	
	function saveAndCloseWaiting()
	{
		$this->saveWaiting('index.php?option=com_vikappointments&task=waitinglist');
	}

	function saveAndNewWaiting()
	{
		$this->saveWaiting('index.php?option=com_vikappointments&task=newwaiting');
	}
	
	function saveWaiting($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['id_service'] 	= $input->getInt('id_service', 0);
		$args['id_employee'] 	= $input->getInt('id_employee', 0);
		$args['timestamp'] 		= $input->getString('timestamp');
		$args['email'] 			= $input->getString('email');
		$args['phone_number'] 	= $input->getString('phone_number');
		$args['id'] 			= $input->getInt('id');
		
		$args['jid'] 			= -1;
		$args['phone_prefix'] 	= '';
		
		$blank_keys = AppointmentsHelper::validateWaiting($args);

		$id_user = $input->getUint('id_user', 0);
		
		if ($id_user > 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('u.jid', 'u.billing_mail', 'u.billing_phone', 'c.phone_prefix')))
				->from($dbo->qn('#__vikappointments_users', 'u'))
				->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('u.country_code') . ' = ' . $dbo->qn('c.country_2_code'))
				->where($dbo->qn('u.id') . ' = ' . $id_user);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$row = $dbo->loadAssoc();

				$args['jid'] = $row['jid'];
				
				if (empty($args['email']))
				{
					$args['email'] = $row['billing_mail'];
				}

				if (empty($args['phone_number']))
				{
					$args['phone_number'] = $row['billing_phone'];
				}

				if (empty($args['phone_prefix']))
				{
					$args['phone_prefix'] = $row['phone_prefix'];
				}
			}
		}

		if (empty($args['email']))
		{
			$blank_keys[] = 'email';
		}

		$args['timestamp'] = VikAppointments::createTimestamp($args['timestamp'], 0, 0);
		
		if ($args['timestamp'] == -1)
		{
			$args['timestamp'] = time();
		}
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewWaiting($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedWaiting($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newwaiting' : 'editwaiting&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editwaiting&cid[]='.$args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewWaiting(array $args, $dbo, $app)
	{
		$waiting = (object) $args;
		$waiting->created_on = time();

		$res = $dbo->insertObject('#__vikappointments_waitinglist', $waiting, 'id');
				
		if ($res && $waiting->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWWAITINGCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWWAITINGCREATED0'), 'error');
		}
		
		return $waiting->id;
	}
	
	private function editSelectedWaiting(array $args, $dbo, $app)
	{
		$waiting = (object) $args;

		$dbo->updateObject('#__vikappointments_waitinglist', $waiting, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPWAITINGEDITED1'));
		}
	}
	
	/**
	 * SAVE CUSTOMER
	 */
	 
	function saveAndCloseCustomer()
	{
		$this->saveCustomer('index.php?option=com_vikappointments&task=customers');
	}
	
	function saveAndNewCustomer()
	{
		$this->saveCustomer('index.php?option=com_vikappointments&task=newcustomer');
	}
	 
	function saveCustomer($return_url = '')
	{	
		$app = JFactory::getApplication();
		$input = $app->input;
		$dbo = JFactory::getDbo();
		
		$args = array();
		$args['jid'] 				= $input->getInt('jid');
		$args['billing_name'] 		= $input->getString('billing_name');
		$args['billing_mail'] 		= $input->getString('billing_mail');
		$args['billing_phone'] 		= $input->getString('billing_phone');
		$args['country_code'] 		= $input->getString('country_code');
		$args['billing_state'] 		= $input->getString('billing_state');
		$args['billing_city'] 		= $input->getString('billing_city');
		$args['billing_address'] 	= $input->getString('billing_address');
		$args['billing_address_2'] 	= $input->getString('billing_address_2');
		$args['billing_zip'] 		= $input->getString('billing_zip');
		$args['company'] 			= $input->getString('company');
		$args['vatnum'] 			= $input->getString('vatnum');
		$args['ssn'] 				= $input->getString('ssn');
		$args['notes'] 				= $input->getString('notes');
		$args['credit'] 			= $input->getFloat('credit', 0.0);
		$args['id'] 				= $input->getInt('id');

		// make SSN uppercase
		$args['ssn'] = strtoupper($args['ssn']);
		
		$create_new = $input->getBool('create_new_user');

		$valid = 1;

		$juser = array();
		
		if ($create_new)
		{
			$juser['user_name'] = $input->getString('user_name');

			if (empty($juser['user_name']))
			{
				$juser['user_name'] = $args['billing_name'];
			}

			$juser['user_mail'] = $input->getString('user_mail');

			if (empty($juser['user_mail']))
			{
				$juser['user_mail'] = $args['billing_mail'];
			}

			$juser['user_pwd1'] = $input->getString('user_pwd1');
			$juser['user_pwd2'] = $input->getString('user_pwd2');
			
			if (empty($juser['user_pwd1']) || empty($juser['user_pwd2']))
			{
				$valid = -1;
			}
			else if (strcmp($juser['user_pwd1'], $juser['user_pwd2']))
			{
				$valid = -2;
			}
		}

		// get custom fields

		$_cf = VAPCustomFields::getList(0, 0, 0, CF_EXCLUDE_REQUIRED_CHECKBOX);
		
		$tmp = array(); // used to attach the rules to a dummy var

		$cust_req = VAPCustomFields::loadFromRequest($_cf, $tmp, false);

		// inject uploads in custom fields array
		foreach ($tmp['uploads'] as $k => $v)
		{
			$cust_req[$k] = $v;
		}

		$args['fields'] = json_encode($cust_req);

		// validate
		
		$blank_keys = AppointmentsHelper::validateCustomer($args);
		
		if (count($blank_keys))
		{
			$valid = -3;
		}
		
		if ($valid == -2 || ($valid == -1 && empty($juser['user_pwd1'])))
		{
			$blank_keys[] = 'user_pwd1';
		}
		
		if ($valid == -2 || ($valid == -1 && empty($juser['user_pwd2'])))
		{
			$blank_keys[] = 'user_pwd2';
		}
		
		if ($valid == 1 && $create_new && empty($args['jid']))
		{
			// insert new Joomla user
			$args['jid'] = (int) AppointmentsHelper::createNewJoomlaUser($juser);
		}

		if (!UIFactory::getConfig()->getBool('usercredit'))
		{
			// unset user credit as it is not supported
			unset($args['credit']);
		}

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsavecustomer.tab', 'tabname', null, 'string');
		
		if ($valid == 1)
		{
			// prepare external plugins

			$dispatcher = UIFactory::getEventDispatcher();

			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewCustomer($args, $dbo, $app);

				$is_new = true;
			}
			else
			{
				$this->editSelectedCustomer($args, $dbo, $app);

				$is_new = false;
			}

			if ($args['id'])
			{
				// trigger event customer save
				$dispatcher->trigger('onCustomerSave', array(&$args, $is_new));
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPMANAGECUSTOMERERR' . abs($valid)), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newcustomer'.$attach_url : 'editcustomer&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editcustomer&cid[]='.$args['id'];
		}

		$app->redirect($return_url);
	}

	private function saveNewCustomer(array $args, $dbo, $app)
	{
		$customer = (object) $args;
		
		$res = $dbo->insertObject('#__vikappointments_users', $customer, 'id');
				
		if ($res && $customer->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWCUSTOMERCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWCUSTOMERCREATED0'), 'error');
		}
		
		return $customer->id;
	}
	
	private function editSelectedCustomer(array $args, $dbo, $app)
	{
		$customer = (object) $args;

		$dbo->updateObject('#__vikappointments_users', $customer, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPCUSTOMEREDITED1'));
		}
	}

	/**
	 * SAVE MEDIA
	 */

	function saveAndCloseMedia()
	{
		$this->saveMedia('index.php?option=com_vikappointments&task=media');
	}
	
	function saveMedia($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		$config = UIFactory::getConfig();

		$prop = array();	
		
		$prop['oriwres'] 	= $input->getUint('oriwres');
		$prop['orihres'] 	= $input->getUint('orihres');
		$prop['smallwres'] 	= $input->getUint('smallwres');
		$prop['smallhres'] 	= $input->getUint('smallhres');
		$prop['isresize'] 	= $input->getUint('is_res', 0);
		
		if ($prop['oriwres'] < 64 || $prop['oriwres'] > 9999 || $prop['isresize'] != 1)
		{
			$prop['oriwres'] = $config->getUint('oriwres');
		} 

		if ($prop['orihres'] < 64 || $prop['orihres'] > 9999 || $prop['isresize'] != 1)
		{
			$prop['orihres'] = $config->getUint('orihres');
		}
		
		if ($prop['smallwres'] < 16 || $prop['smallwres'] > 1024)
		{
			$prop['smallwres'] = $config->getUint('smallwres');
		}
		
		if ($prop['smallhres'] < 16 || $prop['smallhres'] > 1024)
		{
			$prop['smallhres'] = $config->getUint('smallhres');
		}
		
		foreach ($prop as $key => $val)
		{
			$config->set($key, $val);
		} 

		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=newmedia';
		}

		$app->redirect($return_url);
	}

	function uploadimageajax()
	{
		$input = JFactory::getApplication()->input;

		$config = UIFactory::getConfig();

		$prop = array();    
		
		$img = $input->files->get('image', null, 'array');
		
		$prop['oriwres'] 	= $input->getUint('oriwres');
		$prop['orihres'] 	= $input->getUint('orihres');
		$prop['smallwres'] 	= $input->getUint('smallwres');
		$prop['smallhres'] 	= $input->getUint('smallhres');
		$prop['isresize'] 	= $input->getUint('is_res', 0);
		
		if ($prop['oriwres'] < 64 || $prop['oriwres'] > 9999 || $prop['isresize'] != 1)
		{
			$prop['oriwres'] = $config->getUint('oriwres');
		} 

		if ($prop['orihres'] < 64 || $prop['orihres'] > 9999 || $prop['isresize'] != 1)
		{
			$prop['orihres'] = $config->getUint('orihres');
		}
		
		if ($prop['smallwres'] < 16 || $prop['smallwres'] > 1024)
		{
			$prop['smallwres'] = $config->getUint('smallwres');
		}
		
		if ($prop['smallhres'] < 16 || $prop['smallhres'] > 1024)
		{
			$prop['smallhres'] = $config->getUint('smallhres');
		}
		
		$ori = VikAppointments::uploadImage($img, VAPMEDIA . DIRECTORY_SEPARATOR);
		
		$file_name = '';
		
		// THUMB
		if ($ori['esit'] == -1)
		{
			
		}
		else if ($ori['esit'] == -2)
		{
			
		}
		else
		{
			UILoader::import('libraries.image.resizer');

			$file_name = $ori['name'];
			
			$original 	= VAPMEDIA . DIRECTORY_SEPARATOR . $ori['name'];
			$thumb 		= VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $ori['name'];
			VikAppointmentsImageResizer::proportionalImage($original,  $thumb, $prop['smallwres'], $prop['smallhres']);
			
			if ($prop['isresize'])
			{
				$final_dest = VAPMEDIA . DIRECTORY_SEPARATOR . $ori['name'];
				$crop_dest 	= str_replace($ori['name'], '$_' . $ori['name'], $final_dest);
				VikAppointmentsImageResizer::proportionalImage($final_dest, $crop_dest, $prop['oriwres'], $prop['orihres']);
				
				copy($crop_dest, $final_dest);
				unlink($crop_dest);
			}
		}
		
		if (empty($file_name))
		{
			echo json_encode(array(0));
		}
		else
		{
			echo json_encode(array(1, $file_name));
		}

		exit;
	} 

	function renameMedia()
	{
		$input = JFactory::getApplication()->input;

		$oldname = $input->getString('oldname');
		$newname = $input->getString('newname');
		$id 	 = $input->getInt('id');
		
		$ext = substr($oldname, strrpos($oldname, "."));
		$newname .= $ext;
		
		$errs = array();
		
		if ($oldname != $newname)
		{
			if (!file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $oldname))
			{
				$errs[] = JText::sprintf('VAPIMAGENOTEXISTSERROR', $oldname);
			}
			
			if (file_exists(VAPMEDIA . DIRECTORY_SEPARATOR . $newname))
			{
				$errs[] = JText::sprintf('VAPIMAGEEXISTSERROR', $newname);
			}
			
			if (count($errs) == 0)
			{
				$res = rename(VAPMEDIA . DIRECTORY_SEPARATOR . $oldname, VAPMEDIA . DIRECTORY_SEPARATOR . $newname);
				
				if (!$res)
				{
					$errs[] = JText::_('VAPIMAGECOPYERROR');
				}
				
				$res = rename(VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $oldname, VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $newname);
			}
		}
		
		$obj = array(count($errs) ? 0 : 1);
		
		if ($obj[0])
		{
			$obj[1] = $newname;
			$obj[2] = $id;
			
			$this->renameImageField($oldname, $newname);
		}
		else
		{
			$obj[1] = implode("\n", $errs);
		}
		
		
		echo json_encode($obj);
		die;
	}

	function renameImageField($oldn, $newn)
	{
		$dbo = JFactory::getDbo();
		
		$oldname = $dbo->q($oldn);
		$newname = $dbo->q($newn);
		
		$queries = array(
			"UPDATE `#__vikappointments_service` 	SET `image`	  = $newname WHERE `image`	 = $oldname",
			"UPDATE `#__vikappointments_employee` 	SET `image`	  = $newname WHERE `image`	 = $oldname",
			"UPDATE `#__vikappointments_option` 	SET `image`	  = $newname WHERE `image`	 = $oldname",
			"UPDATE `#__vikappointments_config` 	SET `setting` = $newname WHERE `setting` = $oldname AND `param`= 'companylogo'",
		);
		
		foreach ($queries as $q)
		{
			$dbo->setQuery($q);
			$dbo->execute();
		}
	}
	
	function storeMediaProperties()
	{	
		$input = JFactory::getApplication()->input;
		
		$config = UIFactory::getConfig();

		$prop = array();	
		
		$prop['oriwres'] 	= $input->getUint('oriwres');
		$prop['orihres'] 	= $input->getUint('orihres');
		$prop['smallwres'] 	= $input->getUint('smallwres');
		$prop['smallhres'] 	= $input->getUint('smallhres');
		$prop['isresize'] 	= $input->getUint('is_res', 0);
		$prop['isconfig'] 	= 1;
		
		if ($prop['oriwres'] < 64 || $prop['oriwres'] > 9999 || $prop['isresize'] != 1)
		{
			$prop['oriwres'] = $config->getUint('oriwres');
		} 

		if ($prop['orihres'] < 64 || $prop['orihres'] > 9999 || $prop['isresize'] != 1)
		{
			$prop['orihres'] = $config->getUint('orihres');
		}
		
		if ($prop['smallwres'] < 16 || $prop['smallwres'] > 1024)
		{
			$prop['smallwres'] = $config->getUint('smallwres');
		}
		
		if ($prop['smallhres'] < 16 || $prop['smallhres'] > 1024)
		{
			$prop['smallhres'] = $config->getUint('smallhres');
		}
		
		foreach ($prop as $key => $val)
		{
			$config->set($key, $val);
		}

		exit;
	}
	
	function uploadImage()
	{
		$input = JFactory::getApplication()->input;

		$img = $input->files->get('image', null, 'array');
		
		$ori = VikAppointments::uploadImage($img, VAPMEDIA . DIRECTORY_SEPARATOR);
		
		$prop = array(
			'oriwres'   => VikAppointments::getOriginalWidthResize(true),
			'orihres'   => VikAppointments::getOriginalHeightResize(true),
			'smallwres' => VikAppointments::getSmallWidthResize(true),
			'smallhres' => VikAppointments::getSmallHeightResize(true),
			'isresize'  => VikAppointments::isImageResize(true)
		);
		
		// THUMB
		if ($ori['esit'] == -1)
		{
			$ori['name'] = JText::_('VAPCONFIGUPLOADERROR');
		}
		else if ($ori['esit'] == -2)
		{
			$ori['name'] = JText::_('VAPCONFIGFILETYPEERROR');
		}
		else
		{
			UILoader::import('libraries.image.resizer');
			
			$original 	= VAPMEDIA . DIRECTORY_SEPARATOR . $ori['name'];
			$thumb 		= VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $ori['name'];
			VikAppointmentsImageResizer::proportionalImage($original,  $thumb, $prop['smallwres'], $prop['smallhres']);
			
			if ($prop['isresize'])
			{
				$final_dest = VAPMEDIA . DIRECTORY_SEPARATOR . $ori['name'];
				$crop_dest 	= str_replace($ori['name'], '$_' . $ori['name'], $final_dest);
				VikAppointmentsImageResizer::proportionalImage($final_dest,  $crop_dest, $prop['oriwres'], $prop['orihres']);
				
				copy($crop_dest, $final_dest);
				unlink($crop_dest);
			}
		}
		
		echo json_encode(array($ori['esit'], $ori['name']));
		die;	
	}

	function loadmedia()
	{
		$input = JFactory::getApplication()->input;

		$vik = UIApplication::getInstance();

		$config = UIFactory::getConfig();
		
		$start_limit = $input->getInt('start_limit');
		$limit 		 = $input->getInt('limit');
		$_key 		 = $input->getString('keysearch', '');
		
		$not_all = true;
		
		$all_img = AppointmentsHelper::getAllMedia(true);
		
		if (!empty($_key))
		{
			$app = array();
			foreach ($all_img as $img)
			{
				$file_name = basename($img);
				if (strpos($file_name, $_key) !== false)
				{
					$app[] = $img;
				}
			}
			$all_img = $app;
			unset($app);
		}
		
		$max_limit = count($all_img);
		if ($max_limit <= $start_limit+$limit)
		{
			$limit = $max_limit-$start_limit;
			$not_all = false;
		}
		$all_img = array_slice($all_img, $start_limit, $limit);
		
		$medias_html = array();
		
		$cont = $start_limit;
		$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');
		
		foreach ($all_img as $img)
		{
			$cont++; 
			$img_name = basename($img);
			
			$short_name = $img_name;
			if (strlen($short_name) > 24)
			{
				$short_name = substr($short_name, 0, 16) . '...' . substr($short_name, strrpos($short_name, '.')-2);
			}
			
			$name_no_ext = substr($img_name, 0, strrpos($img_name, '.'));
			$img_ext = substr($img_name, strrpos($img_name, '.'));
			
			$file_to_get = str_replace('media@small', 'media', $img);
			$file_prop = AppointmentsHelper::getFileProperties($file_to_get, array('dateformat' => $dt_format));
			
			$html = '<div class="vap-mediaimg-block" id="vapblock'.$cont.'">
				<div class="vap-mediaimg-innerblock">
					<div class="vap-mediaimg-wrapper" id="vapwrapper'.$cont.'">
						<div class="vap-mediaimg-thumb">
							<div class="vap-mediaimg-thumbchild">
								<a href="javascript: void(0);" onClick="jQuery(\'#vapblock'.$cont.'\').addClass(\'hover\');">
									<img src="'.VAPMEDIA_SMALL_URI . $img_name.'" id="vap-media-img'.$cont.'"/>
								</a>
							</div>
							<div class="vap-mediaimg-infos">
								<div class="vap-mediaimg-info pull-left">'.$file_prop['creation'].'</div>
								<div class="vap-mediaimg-info pull-right">'.$file_prop['size'].'</div>
							</div>
						</div>
						<div class="vap-mediaimg-controls">
							<div class="vap-mediaimg-control pull-left">
								<input type="checkbox" id="cb'.$cont.'" name="cid[]" class="vap-mediaimg-check" value="'.$img_name.'" onClick="mediaCheckedAction('.$cont.');'.$vik->checkboxOnClick().'">
								<label for="cb'.$cont.'">'.$short_name.'</label>
							</div>
							<div class="vap-mediaimg-control pull-right">
								<a href="javascript: void(0)" class="no-decoration" onClick="jQuery(\'#vapblock'.$cont.'\').addClass(\'hover\');">
									<i class="fa fa-pencil big"></i>
								</a>
								<a href="javascript: void(0)" class="no-decoration" onClick="openRemoveMediaDialog(\''.$img_ext.'\', '.$cont.');">
									<i class="fa fa-trash big"></i>
								</a>
							</div>
						</div>
					</div>
					
					<div class="vap-mediaimg-wrapper-back">
						<div class="vap-mediaimg-details">
							<h3>'.JText::_('VAPMANAGEMEDIA2').'</h3>
							<input type="text" value="'.$name_no_ext.'" id="vap-imgname-input'.$cont.'" size="24" onkeypress="return event.keyCode != 13;"/>'.$img_ext.'
							<input type="hidden" value="'.$name_no_ext.'" id="vap-imgold-input'.$cont.'"/>
							<div class="vap-mediaimg-stats" id="vapmediastats'.$cont.'"></div>
						</div>
						<div class="vap-mediaimg-controls">
							<div class="vap-mediaimg-control pull-right">
								<button type="button" class="btn" onClick="cancelMediaDetails(\''.$img_ext.'\', '.$cont.');">'.JText::_('VAPCANCEL').'</button>
								<button type="button" class="btn btn-success" onClick="doneMediaDetails(\''.$img_ext.'\', '.$cont.');">'.JText::_('VAPDONE').'</button>
							</div>
						</div>
					</div>
				</div>
			</div>';
			
			$medias_html[] = $html;
		}
		
		echo json_encode(array(1, $cont, $not_all, $medias_html));
		exit; 
	} 

	function analyzemedia()
	{
		$input 	= JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();
		
		$files = $input->get('cid', array(), 'string');
		
		if (count($files) == 0)
		{
			exit;
		}
		
		$companylogo = VikAppointments::getCompanyLogoPath();
		
		$where = '';
		foreach ($files as $f)
		{
			if (!empty($where))
			{
				$where .= ' OR ';
			}
			$where .= '`image`='.$dbo->quote($f);
		}
		
		$table = array();

		$queries = array(
			"SELECT `image` FROM `#__vikappointments_service` WHERE $where;",
			"SELECT `image` FROM `#__vikappointments_employee` WHERE $where;",
			"SELECT `image` FROM `#__vikappointments_option` WHERE $where;",
		);

		foreach ($queries as $q)
		{
			$dbo->setQuery($q);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$rows = array();
				foreach ($dbo->loadAssocList() as $k => $v)
				{
					$rows[] = $v['image'];
				}
				$table = array_merge($table, $rows);
			}
		}
		
		$table = array_count_values($table);
		
		foreach ($files as $f)
		{
			if (!empty($table[$f]))
			{
				$table[$f] = array('count' => $table[$f], 'label' => '');
			}
			else
			{
				$table[$f] = array('count' => 0, 'label' => '');
			}

			if (!strcmp($f, $companylogo))
			{
				$table[$f]['count']++;    
			}
			
			if ($table[$f]['count'] == 0)
			{
				$table[$f]['label'] = JText::_('VAPMEDIASTATNEVERUSED');
			}
			else if ($table[$f]['count'] == 1)
			{
				$table[$f]['label'] = JText::_('VAPMEDIASTATUSEDONE');
			}
			else
			{
				$table[$f]['label'] = JText::sprintf('VAPMEDIASTATUSEDMULTI', $table[$f]['count']);
			}
		}
		
		echo json_encode($table);
		exit;
	}

	/**
	 * SAVE IMPORT
	 */

	function saveImport()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$assoc 	= $input->get('column', array(), 'array');
		$type 	= $input->getString('import_type');
		$args 	= $input->get('import_args', array(), 'array');

		UILoader::import('libraries.import.factory');

		$handler = ImportFactory::getObject($type);

		if (!$handler)
		{
			throw new Exception('Import type not supported.', 404);
		}

		if ($handler->hasFile())
		{
			$count = $handler->save($assoc, $args);

			// flush imported file
			$this->deleteImportedFilesAjax($type);

			$alert = $count ? 'success' : 'error';

			$app->enqueueMessage(JText::sprintf('VAPIMPORTRECORDSADDED', $count, $handler->getTotalCount()), $alert);

			// check for errors
			$errors = $handler->getErrors();

			if (count($errors))
			{
				$msg = implode('', $errors);
				$msg = '<a href="javascript: void(0);" onclick="jQuery(this).next().toggle();">' . 
					JText::_('VAPIMPORTMOREDETAILSERR') . 
				'</a><div style="display: none;">' . $msg . '</div>';
				
				$app->enqueueMessage($msg, 'error');
			}
		}

		$this->cancelImport();
	}

	function uploadimportajax()
	{
		$input = JFactory::getApplication()->input;
		
		$csv 	= $input->files->get('source', null, 'array');
		$type 	= $input->getString('import_type');

		// flush all existing imported files of this type
		$this->deleteImportedFilesAjax($type);

		$file_name = $type . '_' . $csv['name'];
		
		/**
		 * Added support for the following MIME types:
		 * - text/plain (.txt)
		 * - application/vnd.ms-excel (.csv on Windows)
		 *
		 * @since 1.6.3
		 */
		$res = VikAppointments::uploadFile(
			$csv, 
			'text/csv,text/plain,application/vnd.ms-excel',
			VAPADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR,
			$file_name
		);
		
		if ($res['esit'] != 1)
		{
			echo json_encode(array(0, $res));
		}
		else
		{
			$file_name = substr($file_name, strpos($file_name, '_') + 1);
			echo json_encode(array(1, $file_name));
		}

		exit;
	}

	function downloadSampleImport()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$type = $input->getString('import_type');

		UILoader::import('libraries.import.factory');

		$handler = ImportFactory::getObject($type);

		if (!$handler)
		{
			throw new Exception('Import type not supported.', 404);
		}

		$file = $handler->getSampleFile();

		if ($file === false)
		{
			throw new Exception('This type does not own any sample data.', 404);
		}

		header('Content-Type: application/csv');
		header('Content-Disposition: attachement; filename="' . basename($file) . '"');
		readfile($file);
		exit;
	}

	/**
	 * DOWNLOAD EXPORT
	 */

	function downloadExport()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$type 	= $input->getString('import_type');
		$class  = $input->getString('export_class');
		$name 	= $input->getString('filename');
		$args 	= $input->get('import_args', array(), 'array');

		UILoader::import('libraries.import.factory');

		// get object handler
		$handler = ImportFactory::getObject($type);

		if (!$handler)
		{
			throw new Exception('Import type not supported.', 404);
		}

		// get export handler
		$exportable = ImportFactory::getExportable($class, $args);

		if (!$exportable)
		{
			throw new Exception('Export handler not supported.', 404);
		}

		// finalise export using the specified class
		$handler->export($exportable, $name);
	}

	function get_export_params()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$type 	= $input->getString('import_type');
		$class  = $input->getString('export_class');

		UILoader::import('libraries.import.factory');

		// get object handler
		$handler = ImportFactory::getObject($type);

		if (!$handler)
		{
			throw new Exception('Import type not supported.', 404);
		}

		// get export handler
		$form = ImportFactory::getExportableForm($class);

		$html = '';

		if ($form)
		{
			$html = $form->renderFieldset(null);
		}

		echo json_encode($html);
		exit;
	}

	/**
	 * SAVE COUPON GROUP
	 */

	function saveAndCloseCouponGroup()
	{
		$this->saveCouponGroup('index.php?option=com_vikappointments&task=coupongroups');
	}

	function saveAndNewCouponGroup()
	{
		$this->saveCouponGroup('index.php?option=com_vikappointments&task=newcoupongroup');
	}
	
	function saveCouponGroup($return_url = '')
	{	
		$dbo 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		$args = array();
		$args['name'] 		 = $input->getString('name');
		$args['description'] = $input->getRaw('description', '');
		$args['id'] 		 = $input->getInt('id', -1);
		
		if (empty($args['name']))
		{
			$args['name'] = 'Group';
		}

		if ($args['id'] == -1)
		{
			$args['id'] = $this->saveNewCouponGroup($args, $dbo, $app);
		}
		else
		{
			$this->editSelectedCouponGroup($args, $dbo, $app);
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editcoupongroup&cid[]=' . $args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewCouponGroup(array $args, $dbo, $app)
	{	
		$q = $dbo->getQuery(true);

		$q->select('MAX(' . $dbo->qn('ordering') . ')')
			->from($dbo->qn('#__vikappointments_coupon_group'));

		$dbo->setQuery($q);
		$dbo->execute();

		$ordering = (int) $dbo->loadResult() + 1;

		$group = (object) $args;
		$group->ordering = $ordering;

		$res = $dbo->insertObject('#__vikappointments_coupon_group', $group, 'id');

		if ($res && $group->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWGROUPCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWGROUPCREATED0'), 'error');
		}
		
		return $group->id;
	}
	
	private function editSelectedCouponGroup(array $args, $dbo, $app)
	{	
		$group = (object) $args;

		$dbo->updateObject('#__vikappointments_coupon_group', $group, 'id');

		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPGROUPEDITED1'));
		}
	}

	/**
	 * SAVE COUPON
	 */
	
	function saveAndCloseCoupon()
	{
		$this->saveCoupon('index.php?option=com_vikappointments&task=coupons');
	}
	
	function saveAndNewCoupon()
	{
		$this->saveCoupon('index.php?option=com_vikappointments&task=newcoupon');
	}
	
	function saveCoupon($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['code'] 			= $input->getString('code');
		$args['type'] 			= $input->getInt('type');
		$args['max_quantity'] 	= $input->getInt('max_quantity');
		$args['used_quantity'] 	= $input->getInt('used_quantity');
		$args['remove_gift'] 	= $input->getInt('remove_gift');
		$args['percentot'] 		= $input->getInt('percentot');
		$args['value'] 			= $input->getFloat('value');
		$args['mincost'] 		= $input->getFloat('mincost');
		$args['pubmode'] 		= $input->getUint('pubmode');
		$args['dstart'] 		= $input->getString('dstart');
		$args['dend'] 			= $input->getString('dend');
		$args['lastminute'] 	= $input->getInt('lastminute', 0);
		$args['notes'] 			= $input->getString('notes');
		$args['id_group']		= $input->getUint('id_group', 0);
		$args['id'] 			= $input->getInt('id', -1);

		$services  = $input->getUint('id_services', array());
		$employees = $input->getUint('id_employees', array());
		
		if (!empty($args['dstart']))
		{
			$args['dstart'] = VikAppointments::jcreateTimestamp($args['dstart'], 0, 0);
		}
		
		if (!empty($args['dend']))
		{
			$args['dend'] = VikAppointments::jcreateTimestamp($args['dend'], 23, 59);
		}
		
		if ($args['dstart'] >= $args['dend'])
		{
			$args['dstart'] = $args['dend'] = -1;
		}
		
		$blank_keys = AppointmentsHelper::validateCoupon($args);

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsavecoupon.tab', 'tabname', null, 'string');
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewCoupon($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedCoupon($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newcoupon' : 'editcoupon&cid[]='.$args['id']));
		}

		$this->updateCouponAssoc($args['id'], $services, 'service', $dbo);
		$this->updateCouponAssoc($args['id'], $employees, 'employee', $dbo);
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editcoupon&cid[]='.$args['id'];
		}
		
		$app->redirect($return_url);
	}

	private function saveNewCoupon(array $args, $dbo, $app)
	{
		$coupon = (object) $args;

		$res = $dbo->insertObject('#__vikappointments_coupon', $coupon, 'id');
				
		if ($res && $coupon->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWCOUPONCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWCOUPONCREATED0'), 'error');
		}
		
		return $coupon->id;
	}
	
	private function editSelectedCoupon(array $args, $dbo, $app)
	{
		$coupon = (object) $args;

		$dbo->updateObject('#__vikappointments_coupon', $coupon, 'id');
		
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPCOUPONEDITED1'));
		}
	}

	private function updateCouponAssoc($id, array $records, $column, $dbo)
	{
		if (empty($id) || !in_array($column, array('service', 'employee')))
		{
			return false;
		}

		// get existing records

		$existing = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_' . $column))
			->from($dbo->qn('#__vikappointments_coupon_' . $column . '_assoc'))
			->where($dbo->qn('id_coupon') . ' = ' . $id);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$existing = $dbo->loadColumn();
		}

		// insert new records

		$has = false;

		$q = $dbo->getQuery(true)
			->insert($dbo->qn('#__vikappointments_coupon_' . $column . '_assoc'))
			->columns($dbo->qn(array('id_coupon', 'id_' . $column)));

		foreach ($records as $s)
		{
			// make sure the record to push doesn't exist yet
			if (!in_array($s, $existing))
			{
				$q->values($id . ', ' . $s);
				$has = true;
			}
		}

		if ($has)
		{
			$dbo->setQuery($q);
			$dbo->execute();
		}

		// delete records

		$delete = array();

		foreach ($existing as $s)
		{
			// make sure the records to delete is not contained in the selected records
			if (!in_array($s, $records))
			{
				$delete[] = $s;
			}
		}

		if (count($delete))
		{
			$q = $dbo->getQuery(true)
				->delete($dbo->qn('#__vikappointments_coupon_' . $column . '_assoc'))
				->where(array(
					$dbo->qn('id_coupon') . ' = ' . $id,
					$dbo->qn('id_' . $column) . ' IN (' . implode(',', $delete) . ')',
				));

			$dbo->setQuery($q);
			$dbo->execute();
		}
	}
	
	/**
	 * SAVE PAYMENT
	 */
	
	function saveAndClosePayment()
	{
		$this->savePayment('index.php?option=com_vikappointments&task=payments');
	}
	
	function saveAndNewPayment()
	{
		$this->savePayment('index.php?option=com_vikappointments&task=newpayment');
	}
	
	function savePayment($return_url = '')
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$ui     = UIApplication::getInstance();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['file'] 			= $input->getString('file');
		$args['charge'] 		= $input->getFloat('charge');
		$args['published'] 		= $input->getInt('published', 0);
		$args['setconfirmed'] 	= $input->getInt('setconfirmed', 0);
		$args['position'] 		= $input->getString('position');
		$args['icontype'] 		= $input->getUint('icontype', 0);
		$args['level'] 			= $input->getUint('level', 0);
		$args['prenote'] 		= $input->getRaw('prenote');
		$args['note'] 			= $input->getRaw('note');
		$args['id'] 			= $input->getInt('id');
		$args['id_employee']	= $input->getUint('id_employee', 0);

		switch ($args['icontype'])
		{
			case 1: $args['icon'] = $input->getString('font_icon'); break;
			case 2: $args['icon'] = $input->getString('upload_icon'); break;

			default: $args['icon'] = '';
		}

		$allowedfor 			= $input->getUint('allowedfor', 1);
		$args['appointments'] 	= 0;
		$args['subscr'] 		= 0;

		if ($allowedfor == 1 || $allowedfor == 3)
		{
			$args['appointments'] = 1;
		}
		
		if ($allowedfor == 2 || $allowedfor == 3)
		{
			$args['subscr'] = 1;
		}
		
		$blank_keys = AppointmentsHelper::validatePayment($args);
		
		try
		{
			/**
			 * Access payment config through platform handler.
			 *
			 * @since 1.6.3
			 */
			$form = $ui->getPaymentConfig($args['file']);
		}
		catch (Exception $e)
		{
			$blank_keys[] = 'file';	
		}
		
		if (count($blank_keys) == 0)
		{	
			$params = array();
			
			foreach ($form as $k => $p)
			{
				$params[$k] = $input->getString($k);
			}
			
			$args['params'] = json_encode($params);
			
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewPayment($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedPayment($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newpayment' : 'editpayment&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editpayment&cid[]=' . $args['id'];

			if ($args['id_employee'] > 0)
			{
				$return_url .= '&id_employee=' . $args['id_employee'];
			}
		}
		
		$app->redirect($return_url);
	}
	
	private function saveNewPayment(array $args, $dbo, $app)
	{
		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('ordering'))
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('id_employee') . ' = ' . $args['id_employee'])
			->order($dbo->qn('ordering') . ' DESC');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$ordering = (int) $dbo->loadResult() + 1;

		$payment = (object) $args;
		$payment->ordering  = $ordering;
		$payment->createdby = JFactory::getUser()->id;

		$res = $dbo->insertObject('#__vikappointments_gpayments', $payment, 'id');
				
		if ($res && $payment->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWPAYMENTCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWPAYMENTCREATED0'), 'error');
		}

		return $payment->id;
	}
	
	private function editSelectedPayment(array $args, $dbo, $app)
	{
		$payment = (object) $args;

		$dbo->updateObject('#__vikappointments_gpayments', $payment, 'id');

		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPPAYMENTEDITED1'));
		}
	}

	function saveLangPayment()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 		= $input->getString('name');
		$args['prenote'] 	= $input->getRaw('langprenote');
		$args['note'] 		= $input->getRaw('langnote');
		$args['tag'] 		= $input->getString('tag');
		$args['id'] 		= $input->getInt('id');
		$args['id_payment'] = $input->getInt('id_payment');

		$lang = (object) $args;

		$lang = (object) $args;
		
		if ($args['id'] == -1)
		{
			unset($lang->id);
			$dbo->insertObject('#__vikappointments_lang_payment', $lang, 'id');
		}
		else
		{
			unset($lang->tag);
			unset($lang->id_payment);
			$dbo->updateObject('#__vikappointments_lang_payment', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPSERLANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelangpayment&tag={$args['tag']}&id_payment={$args['id_payment']}");
	}
	
	/**
	 * SAVE CUSTOM FIELDS
	 */
	
	function saveAndCloseCustomf()
	{
		$this->saveCustomf('index.php?option=com_vikappointments&task=customf');
	}
	
	function saveAndNewCustomf()
	{
		$this->saveCustomf('index.php?option=com_vikappointments&task=newcustomf');
	}
	
	function saveCustomf($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
	
		$args = array();
		$args['group']			= $input->getUint('group', null);
		$args['name'] 			= $input->getRaw('name', ''); // html is allowed
		$args['type'] 			= $input->getString('type', '');
		$args['required'] 		= $input->getUint('required', 0);
		$args['multiple'] 		= $input->getUint('multiple', 0);
		$args['rule'] 			= $input->getUint('rule', 0);
		$args['poplink'] 		= $input->getString('poplink', '');
		$args['id'] 			= $input->getInt('id', -1);
		$args['choose'] 		= '';

		if ($args['group'] == 0)
		{
			// customers group
			$args['id_employee'] = $input->getInt('id_employee', 0);

			if (empty($args['id_employee']))
			{
				$args['id_employee'] = -1;
			}
		}
		else if ($args['group'] == 1)
		{
			// employees group
			$args['formname'] = $input->getString('formname');

			if (!$args['formname'])
			{
				// form name not provided, take it from the field name
				$args['formname'] = $args['name'] ? $args['name'] : $args['type'];
			}

			// strip all the unsupported characters
			$args['formname'] = strtolower(preg_replace("/^[\d]+|[^a-zA-Z0-9_]/", '', $args['formname']));
			// make sure the length of the form name doesn't exceed the limit
			$args['formname'] = substr($args['formname'], 0, 32);

			/**
			 * @todo 	Should we validate the form name
			 * 			to make sure it is unique?
			 * 			In case 2 fields own the same form name,
			 * 			the second one won't work.
			 */

			// unset rule
			$args['rule'] = 0;
		}

		$settings = array();
		$settings['choose_select'] 	 = $input->getString('choose', array());
		$settings['choose_filter'] 	 = $input->getString('filters', '');
		$settings['def_prfx'] 		 = $input->getString('def_prfx', '');
		$settings['sep_suffix'] 	 = $input->getString('sep_suffix', '');
		$settings['number_min']		 = $input->getString('number_min', '');
		$settings['number_max']		 = $input->getString('number_max', '');
		$settings['number_decimals'] = $input->getUint('number_decimals', 0);
		$settings['use_editor']      = $input->getUint('use_editor', 0);
		
		if ($args['type'] == 'select')
		{
			$args['choose'] = implode(';;__;;', array_filter($settings['choose_select']));
		}
		else if ($args['type'] == 'textarea')
		{
			$textarea = array(
				'editor' => $settings['use_editor'],
			);

			$args['choose'] = json_encode($textarea);
		}
		else if ($args['type'] == 'number')
		{
			$number = array(
				'min' 		=> strlen($settings['number_min']) ? intval($settings['number_min']) : '',
				'max' 		=> strlen($settings['number_max']) ? intval($settings['number_max']) : '',
				'decimals' 	=> $settings['number_decimals'],
			);

			$args['choose'] = json_encode($number);
		}
		else if ($args['type'] == 'file')
		{
			$args['choose'] = $settings['choose_filter'];
		}
		else if ($args['type'] == 'separator')
		{
			$args['choose']   = $settings['sep_suffix'];
			$args['rule']	  = 0;
			$args['required'] = 0;
		}
		else if (VAPCustomFields::isPhoneNumber($args))
		{
			$args['choose'] = $settings['def_prfx'];
		}
	
		$blank_keys = AppointmentsHelper::validateCustomf($args);
	
		if (count($blank_keys ) == 0)
		{		
			if ($args['id'] == -1)
			{
				$isNew = true;
				$args['id'] = $this->saveNewCustomf($args, $dbo, $app);
			}
			else
			{
				$isNew = false;
				$this->editSelectedCustomf($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newcustomf' : 'editcustomf&cid[]=' . $args['id']));
			exit;
		}

		if ($args['group'] == 0)
		{
			// customers fields
			$services = $input->getUint('id_services', array());

			$this->updateCustomFieldServiceAssoc($args['id'], $services, $dbo);
		}
		else if ($args['group'] == 1)
		{
			// employees field
			
			if ($isNew)
			{
				// alter employees table to add this field as new column
				$this->alterEmployeesTable($args, $dbo);
			}
		}
	
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editcustomf&cid[]=' . $args['id'];
		}
		
		$app->redirect($return_url);
	}
	
	private function saveNewCustomf(array $args, $dbo, $app)
	{
		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('ordering'))
			->from($dbo->qn('#__vikappointments_custfields'))
			->where($dbo->qn('group') . ' = ' . $args['group'])
			->order($dbo->qn('ordering') . ' DESC');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$ordering = (int) $dbo->loadResult() + 1;

		$field = (object) $args;
		$field->ordering = $ordering;

		$res = $dbo->insertObject('#__vikappointments_custfields', $field, 'id');

		if ($res && $field->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWCUSTOMFCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWCUSTOMFCREATED0'), 'error');
		}
		
		return $field->id;
	}
	
	private function editSelectedCustomf(array $args, $dbo, $app)
	{
		$field = (object) $args;

		$dbo->updateObject('#__vikappointments_custfields', $field, 'id');
		
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPCUSTOMFEDITED1'));
		}
	}

	private function updateCustomFieldServiceAssoc($id, array $records, $dbo)
	{
		if (empty($id))
		{
			return false;
		}

		// get existing records

		$existing = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_service'))
			->from($dbo->qn('#__vikappointments_cf_service_assoc'))
			->where($dbo->qn('id_field') . ' = ' . $id);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$existing = $dbo->loadColumn();
		}

		// insert new records

		$has = false;

		$q = $dbo->getQuery(true)
			->insert($dbo->qn('#__vikappointments_cf_service_assoc'))
			->columns($dbo->qn(array('id_field', 'id_service')));

		foreach ($records as $s)
		{
			// make sure the record to push doesn't exist yet
			if (!in_array($s, $existing))
			{
				$q->values($id . ', ' . $s);
				$has = true;
			}
		}

		if ($has)
		{
			$dbo->setQuery($q);
			$dbo->execute();
		}

		// delete records

		$delete = array();

		foreach ($existing as $s)
		{
			// make sure the records to delete is not contained in the selected records
			if (!in_array($s, $records))
			{
				$delete[] = $s;
			}
		}

		if (count($delete))
		{
			$q = $dbo->getQuery(true)
				->delete($dbo->qn('#__vikappointments_cf_service_assoc'))
				->where(array(
					$dbo->qn('id_field') . ' = ' . $id,
					$dbo->qn('id_service') . ' IN (' . implode(',', $delete) . ')',
				));

			$dbo->setQuery($q);
			$dbo->execute();
		}
	}

	private function alterEmployeesTable(array $args, $dbo)
	{
		try
		{
			/**
			 * Use a text type instead of a varchar for textarea fields,
			 * which might be used as editors.
			 *
			 * @since 1.6.3
			 */
			$type = $args['type'] == 'textarea' ? 'text' : 'varchar(128)';

			$q = "ALTER TABLE `#__vikappointments_employee` ADD COLUMN `field_{$args['formname']}` $type DEFAULT NULL";

			$dbo->setQuery($q);
			$dbo->execute();
		}
		catch (Exception $e)
		{
			/**
			 * Raise error to inform that a manual installation is needed.
			 *
			 * @since 1.6.2
			 */
			JFactory::getApplication()->enqueueMessage(JText::sprintf('VAPCFFORMNAMEALTER_ERROR', $q), 'error');
		}
	}

	private function dropEmployeesCustomFields(array $ids)
	{
		if (!count($ids))
		{
			return;
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('formname'))
			->from($dbo->qn('#__vikappointments_custfields'))
			->where($dbo->qn('group') . ' = 1')
			->where($dbo->qn('id') . ' IN (' . implode(', ', $ids) . ')');

		$dbo->setQuery($q);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			return;
		}

		$fields = array_filter($dbo->loadColumn());

		if (!$fields)
		{
			return;
		}

		$q = "ALTER TABLE `#__vikappointments_employee`";

		foreach ($fields as $name)
		{
			$q .= "\nDROP COLUMN `field_{$name}`,";
		}

		$q = rtrim($q, ',') . ';';

		try
		{
			$dbo->setQuery($q);
			$dbo->execute();
		}
		catch (Exception $e)
		{
			/**
			 * Probably the column wasn't installed properly.
			 * Catch error to avoid breaking the flow.
			 *
			 * @since 1.6.2
			 */
		}
	}

	function saveLangCustomf()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 		= $input->getRaw('name', '');
		$args['poplink'] 	= $input->getString('poplink', '');
		$args['tag'] 		= $input->getString('tag');
		$args['id'] 		= $input->getInt('id');
		$args['id_customf'] = $input->getInt('id_customf');
		$args['choose']		= '';

		$select  = $input->getString('select_choose', array());
		$country = $input->getString('country_code', '');
		$suffix  = $input->getString('sep_suffix', '');

		if ($select)
		{
			$args['choose'] = implode(';;__;;', array_filter($select));
		}
		else if ($suffix)
		{
			$args['choose'] = $suffix;
		}
		else if ($country)
		{
			$args['choose'] = $country;
		}

		$lang = (object) $args;
		
		if ($args['id'] == -1)
		{
			unset($lang->id);
			$dbo->insertObject('#__vikappointments_lang_customf', $lang, 'id');
		}
		else
		{
			unset($lang->tag);
			unset($lang->id_customf);
			$dbo->updateObject('#__vikappointments_lang_customf', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPCUSTOMFLANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelangcustomf&tag={$args['tag']}&id_customf={$args['id_customf']}");
	}

	/**
	 * UTILS
	 */
	
	function sortField()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$sortid = $input->getUint('cid', array(0));
		$mode 	= $input->getString('mode', 'up');
		
		$db_table 	 = $input->getString('db_table');
		$return_task = $input->getString('return_task');

		$params = $input->get('params', array(), 'array');
		$where  = $input->get('where', array(), 'array');

		$url = "index.php?option=com_vikappointments&task={$return_task}";

		// get selected record
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'ordering')))
			->from($dbo->qn('#__vikappointments_' . $db_table))
			->where($dbo->qn('id') . ' = ' . $sortid[0]);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// row not found
			$app->redirect($url);
		}

		$data = $dbo->loadObject();

		// get next/prev record
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'ordering')))
			->from($dbo->qn('#__vikappointments_' . $db_table))
			->where($dbo->qn('ordering') . ($mode == 'up' ? '<' : '>') . $data->ordering)
			->order($dbo->qn('ordering') . ' ' . ($mode == 'up' ? 'DESC' : 'ASC'));

		// create WHERE statement with custom claus
		foreach ($where as $column => $values)
		{
			if (is_array($values) && count($values) > 1)
			{
				$values = array_map(array($dbo, 'q'), $values);
				$q->where($dbo->qn($column) . ' IN (' . implode(',', $values) . ')');
			}
			else
			{
				$values = is_array($values) ? array_shift($values) : $values;
				$q->where($dbo->qn($column) . ' = ' . $dbo->q($values));
			}
		}
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			// the row is probably the first/last
			$app->redirect($url);
		}

		$next = $dbo->loadObject();

		// swap orderings
		$tmp = $data->ordering;
		$data->ordering = $next->ordering;
		$next->ordering = $tmp;

		// update the records
		$dbo->updateObject('#__vikappointments_' . $db_table, $data, 'id');
		$dbo->updateObject('#__vikappointments_' . $db_table, $next, 'id');

		// append custom params in query string
		foreach ($params as $k => $v)
		{
			$url .= "&$k=" . (string) $v;
		}

		$app->redirect($url);
	}

	function saveServiceSort()
	{
		$this->saveManualSort('service', 'services');
	}
	
	function saveOptionSort()
	{
		$this->saveManualSort('option', 'options');
	}
	
	function saveGroupSort()
	{
		$this->saveManualSort('group', 'groups');
	}

	function saveCouponGroupSort()
	{
		$this->saveManualSort('coupon_group', 'coupongroups');
	}
	
	function saveEmployeeGroupSort()
	{
		$this->saveManualSort('employee_group', 'groups');
	}

	function savePackageSort()
	{
		$this->saveManualSort('package', 'packages');
	}

	function savePackGroupSort()
	{
		$this->saveManualSort('package_group', 'packgroups');
	}

	function saveCustomFieldSort()
	{
		$this->saveManualSort('custfields', 'customf');
	}
	
	function savePaymentSort()
	{
		$this->saveManualSort('gpayments', 'payments');
	}

	function saveEmployeePaymentSort()
	{
		$params = array();
		$params['id_emp'] = JFactory::getApplication()->input->getUint('id_emp', 0);

		$this->saveManualSort('gpayments', 'emppayments', $params);
	}

	private function saveManualSort($db_table, $return_task, array $params = array())
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$ord_arr = $input->get('row_ord', array(), 'array');
		
		foreach ($ord_arr as $id => $arr)
		{
			$ordering = abs(intval($arr[0]));
			$ordering = max(array(1, $ordering));

			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_' . $db_table))
				->set($dbo->qn('ordering') . ' = ' . $ordering)
				->where($dbo->qn('id') . ' = ' . (int) $id);
			
			$dbo->setQuery($q);
			$dbo->execute();
		}

		$url = "index.php?option=com_vikappointments&task={$return_task}";

		if (count($params))
		{
			$url .= "&" . http_build_query($params);
		}
		
		$app->redirect($url);
	}
	
	/**
	 * SAVE MAIL CUSTOM TEXT
	 */
	
	function saveAndCloseCustMail()
	{
		$this->saveCustMail('index.php?option=com_vikappointments&task=mailtextcust');
	}

	function saveAndNewCustMail()
	{
		$this->saveCustMail('index.php?option=com_vikappointments&task=newmailtext');
	}

	function saveAsCopyCustMail()
	{
		$this->saveCustMail('', true);
	}
	
	function saveCustMail($return_url = '', $copy = false)
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 		 = $input->getString('name');
		$args['position'] 	 = $input->getString('position');
		$args['status'] 	 = $input->getString('status');
		$args['file'] 		 = $input->getString('file');
		$args['tag'] 		 = $input->getString('tag');
		$args['id_service']  = $input->getUint('id_service', 0);
		$args['id_employee'] = $input->getUint('id_employee', 0);
		$args['content'] 	 = $input->getRaw('cont', '');
		$args['id'] 		 = $input->getInt('id');

		if ($copy)
		{
			$args['id'] = -1;
		}

		// keep the last tab used for future usages
		$app->getUserStateFromRequest('vapsavemailtext.tab', 'tabname', null, 'string');
		
		if ($args['id'] == -1)
		{
			$args['id'] = $this->saveNewCustMail($args, $dbo, $app);
		}
		else
		{
			$this->editSelectedCustMail($args, $dbo, $app);
		}
	
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editmailtext&cid[]=' . $args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewCustMail(array $args, $dbo, $app)
	{
		$mail = (object) $args;

		$res = $dbo->insertObject('#__vikappointments_cust_mail', $mail, 'id');
				
		if ($res && $mail->id)
		{
			$app->enqueueMessage(JText::_('VAPCUSTMAILCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPCUSTMAILCREATED0'), 'error');
		}
		
		return $mail->id;
	}
	
	private function editSelectedCustMail(array $args, $dbo, $app)
	{
		$mail = (object) $args;

		$dbo->updateObject('#__vikappointments_cust_mail', $mail, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPCUSTMAILUPDATED1'));
		}
	}

	/**
	 * SAVE CONVERSION
	 */
	
	function saveAndCloseConversion()
	{
		$this->saveConversion('index.php?option=com_vikappointments&task=conversions');
	}

	function saveAndNewConversion()
	{
		$this->saveConversion('index.php?option=com_vikappointments&task=newconversion');
	}

	function saveAsCopyConversion()
	{
		$this->saveConversion('', true);
	}
	
	function saveConversion($return_url = '', $copy = false)
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['title'] 		= $input->getString('title');
		$args['published'] 	= $input->getUint('published', 0);
		$args['statuses'] 	= $input->getString('statuses', array());
		$args['jsfile']		= $input->getString('jsfile');
		$args['snippet']	= $input->getRaw('snippet', '');
		$args['page']		= $input->getString('page', '');
		$args['type']		= $input->getString('type', '');
		$args['id'] 		= $input->getInt('id', -1);

		if ($copy)
		{
			$args['id'] = -1;
		}

		$args['statuses'] = json_encode($args['statuses']);
		
		if ($args['id'] == -1)
		{
			$args['id'] = $this->saveNewConversion($args, $dbo, $app);
		}
		else
		{
			$this->editSelectedConversion($args, $dbo, $app);
		}
	
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editconversion&cid[]=' . $args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewConversion(array $args, $dbo, $app)
	{
		$conversion = (object) $args;
		$conversion->createdon = JDate::getInstance()->toSql();

		$res = $dbo->insertObject('#__vikappointments_conversion', $conversion, 'id');
				
		if ($res && $conversion->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWCONVERSIONCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWCONVERSIONCREATED0'), 'error');
		}
		
		return $conversion->id;
	}
	
	private function editSelectedConversion(array $args, $dbo, $app)
	{
		$conversion = (object) $args;

		$dbo->updateObject('#__vikappointments_conversion', $conversion, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPCONVERSIONEDITED1'));
		}
	}

	/**
	 * SAVE CLOSURE
	 */

	function saveAndNewClosure()
	{
		$this->saveClosure('index.php?option=com_vikappointments&task=manageclosure');
	}
	
	function saveClosure($return_url = '')
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['employees'] 	= $input->getUint('employees', array());
		$args['fromdate'] 	= $input->getString('fromdate', '');
		$args['fromhour']	= $input->getUint('fromhour', 0);
		$args['frommin']	= $input->getUint('frommin', 0);
		$args['tohour']		= $input->getUint('tohour', 0);
		$args['tomin']		= $input->getUint('tomin', 0);

		// build data
		$order = new stdClass;
		$order->status 		= 'CONFIRMED';
		$order->checkin_ts 	= VikAppointments::createTimestamp($args['fromdate'], $args['fromhour'], $args['frommin']);
		$order->duration 	= abs(VikAppointments::createTimestamp($args['fromdate'], $args['tohour'], $args['tomin']) - $order->checkin_ts) / 60;
		$order->closure 	= 1;
		$order->createdby 	= JFactory::getUser()->id;
		$order->createdon 	= time();

		$ok = false;

		foreach ($args['employees'] as $e)
		{
			$order->id_employee = $e;

			// get first service assigned to this employee
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_service'))
				->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
				->where($dbo->qn('id_employee') . ' = ' . $e);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$order->id_service = $dbo->loadResult();

				$dbo->insertObject('#__vikappointments_reservation', $order, 'id');

				$ok = $ok | ($order->id > 0);

				// unset order ID to avoid duplicated PK errors
				unset($order->id);
			}
		}

		$from = $input->get('from', 'employees');
	
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=' . $from;
		}
		else
		{
			$return_url .= '&from=' . $from;
		}

		if ($ok)
		{
			$app->enqueueMessage(JText::_('VAPNEWCLOSURECREATED1'));
		}

		$app->redirect($return_url);
	}
	
	/**
	 * SAVE COUNTRY
	 */
	
	function saveAndCloseCountry()
	{
		$this->saveCountry('index.php?option=com_vikappointments&task=countries');
	}

	function saveAndNewCountry()
	{
		$this->saveCountry('index.php?option=com_vikappointments&task=newcountry');
	}
	
	function saveCountry($return_url = '')
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['country_name'] 	= $input->getString('country_name');
		$args['country_2_code'] = strtoupper($input->getString('country_2_code'));
		$args['country_3_code'] = strtoupper($input->getString('country_3_code'));
		$args['phone_prefix'] 	= $input->getString('phone_prefix');
		$args['published'] 		= $input->getInt('published', 0);
		$args['id'] 			= $input->getInt('id');
		
		$blank_keys = AppointmentsHelper::validateCountry($args);
		
		// search for duplicated codes
		if (count($blank_keys) == 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('country_2_code', 'country_3_code')))
				->from($dbo->qn('#__vikappointments_countries'))
				->where($dbo->qn('id') . ' <> ' . $args['id'])
				->andWhere(array(
					$dbo->qn('country_2_code') . ' = ' . $dbo->q($args['country_2_code']),
					$dbo->qn('country_3_code') . ' = ' . $dbo->q($args['country_3_code']),
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows() > 0)
			{
				$assoc = $dbo->loadAssoc();
				if ($assoc['country_2_code'] == $args['country_2_code'])
				{
					$blank_keys[] = 'country_2_code';
				}
				if ($assoc['country_3_code'] == $args['country_3_code'])
				{
					$blank_keys[] = 'country_3_code';
				}

				$unique_err = 1;
			}
		}
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewCountry($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedCountry($args, $dbo, $app);
			}
		}
		else
		{
			if (empty($unique_err))
			{
				$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPCOUNTRYUNIQUEERROR'), 'error');
			}

			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newcountry' : 'editcountry&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editcountry&cid[]='.$args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewCountry(array $args, $dbo, $app)
	{
		$country = (object) $args;

		$res = $dbo->insertObject('#__vikappointments_countries', $country, 'id');

		if ($res && $country->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWCOUNTRYCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWCOUNTRYCREATED0'), '');
		}
		
		return $country->id;
	}
	
	private function editSelectedCountry(array $args, $dbo, $app)
	{
		$country = (object) $args;

		$dbo->updateObject('#__vikappointments_countries', $country, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPCOUNTRYEDITED1'));
		}
	}

	/**
	 * SAVE STATE
	 */
	
	function saveAndCloseState()
	{
		$this->saveState('index.php?option=com_vikappointments&task=states');
	}

	function saveAndNewState()
	{
		$this->saveState('index.php?option=com_vikappointments&task=newstate');
	}
	
	function saveState($return_url = '')
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['state_name'] 	= $input->getString('state_name');
		$args['state_2_code'] 	= strtoupper($input->getString('state_2_code'));
		$args['state_3_code'] 	= strtoupper($input->getString('state_3_code'));
		$args['published'] 		= $input->getUint('published', 0);
		$args['id'] 			= $input->getInt('id');
		$args['id_country'] 	= $input->getInt('country');
		
		$blank_keys = AppointmentsHelper::validateState($args);
		
		// check for duplicated codes
		if (count($blank_keys) == 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('state_2_code', 'state_3_code')))
				->from($dbo->qn('#__vikappointments_states'))
				->where(array(
					$dbo->qn('id') . ' <> ' . $args['id'],
					$dbo->qn('id_country') . ' = ' . $args['id_country'],
				))
				->andWhere(array(
					$dbo->qn('state_2_code') . ' = ' . $dbo->q($args['state_2_code']),
					$dbo->qn('state_3_code') . ' = ' . $dbo->q($args['state_3_code']),
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows() > 0)
			{
				$assoc = $dbo->loadAssoc();
				if ($assoc['state_2_code'] == $args['state_2_code'])
				{
					$blank_keys[] = 'state_2_code';
					$unique_err = 1;
				}
				// since the 3 letters code is optional, raise an error only if it is not empty
				if ($assoc['state_3_code'] == $args['state_3_code'] && !empty($args['state_3_code']))
				{
					$blank_keys[] = 'state_3_code';
					$unique_err = 1;
				}
			}
		}
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewState($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedState($args, $dbo, $app);
			}
		}
		else
		{
			if (empty($unique_err))
			{
				$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPSTATEUNIQUEERROR'), 'error');
			}

			$app->redirect('index.php?option=com_vikappointments&country='.$args['id_country'].'&task=' . ($args['id'] == -1 ? 'newstate' : 'editstate&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editstate&cid[]='.$args['id'];
		}

		$app->redirect($return_url . '&country=' . $args['id_country']);
	}
	
	private function saveNewState(array $args, $dbo, $app)
	{
		$state = (object) $args;
		
		$res = $dbo->insertObject('#__vikappointments_states', $state, 'id');
		
		if ($res && $state->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWSTATECREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWSTATECREATED0'), 'error');
		}
		
		return $state->id;
	}
	
	private function editSelectedState(array $args, $dbo, $app)
	{
		$state = (object) $args;
		
		$dbo->updateObject('#__vikappointments_states', $state, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPSTATEEDITED1'));
		}
	}

	/**
	 * SAVE CITY
	 */
	
	function saveAndCloseCity()
	{
		$this->saveCity('index.php?option=com_vikappointments&task=cities');
	}

	function saveAndNewCity()
	{
		$this->saveCity('index.php?option=com_vikappointments&task=newcity');
	}
	
	function saveCity($return_url = '')
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['city_name'] 		= $input->getString('city_name');
		$args['city_2_code'] 	= strtoupper($input->getString('city_2_code'));
		$args['city_3_code'] 	= strtoupper($input->getString('city_3_code'));
		$args['latitude'] 		= $input->getString('latitude');
		$args['longitude'] 		= $input->getString('longitude');
		$args['published'] 		= $input->getUint('published', 0);
		$args['id'] 			= $input->getInt('id');
		$args['id_state'] 		= $input->getInt('state');

		$id_country = $input->getUint('country');
		
		$blank_keys = AppointmentsHelper::validateCity($args);

		// check for duplicated codes
		if (count($blank_keys) == 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('city_2_code', 'city_3_code')))
				->from($dbo->qn('#__vikappointments_cities'))
				->where(array(
					$dbo->qn('id') . ' <> ' . $args['id'],
					$dbo->qn('id_state') . ' = ' . $args['id_state'],
				))
				->andWhere(array(
					$dbo->qn('city_2_code') . ' = ' . $dbo->q($args['city_2_code']),
					$dbo->qn('city_3_code') . ' = ' . $dbo->q($args['city_3_code']),
				));
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();
			
			if ($dbo->getNumRows() > 0)
			{
				$assoc = $dbo->loadAssoc();
				if ($assoc['city_2_code'] == $args['city_2_code'])
				{
					$blank_keys[] = 'city_2_code';
					$unique_err = 1;
				}
				// since the 3 letters code is optional, raise an error only if it is not empty
				if ($assoc['city_3_code'] == $args['city_3_code'] && !empty($args['city_3_code']))
				{
					$blank_keys[] = 'city_3_code';
					$unique_err = 1;
				}
			}
		}
		
		if (!strlen($args['latitude']) || !strlen($args['longitude']))
		{
			$args['latitude'] = $args['longitude'] = null;
		}
		else
		{
			$args['latitude'] 	= floatval($args['latitude']);
			$args['longitude'] 	= floatval($args['longitude']);
		}
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewCity($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedCity($args, $dbo, $app);
			}
		}
		else
		{
			if (empty($unique_err))
			{
				$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPCITYUNIQUEERROR'), 'error');
			}

			$app->redirect("index.php?option=com_vikappointments&country={$id_country}&state={$args['id_state']}&task=" . ($args['id'] == -1 ? 'newcity' : 'editcity&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editcity&cid[]='.$args['id'];
		}

		$app->redirect($return_url . '&country='.$id_country.'&state='.$args['id_state']);
	}
	
	private function saveNewCity(array $args, $dbo, $app)
	{
		$city = (object) $args;

		$res = $dbo->insertObject('#__vikappointments_cities', $city, 'id');
		
		if ($res && $city->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWCITYCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWCITYCREATED0'), 'error');
		}
		
		return $city->id;
	}
	
	private function editSelectedCity(array $args, $dbo, $app)
	{
		$city = (object) $args;

		$dbo->updateObject('#__vikappointments_cities', $city, 'id', true);
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPCITYEDITED1'));
		}
	}

	/**
	 * SAVE REVIEW
	 */
	
	function saveAndCloseReview()
	{
		$this->saveReview('index.php?option=com_vikappointments&task=revs');
	}

	function saveAndNewReview()
	{
		$this->saveReview('index.php?option=com_vikappointments&task=newrev');
	}
	
	function saveReview($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['title'] 		= $input->getString('title');
		$args['jid'] 		= $input->getUint('jid');
		$args['name'] 		= $input->getString('name');
		$args['email'] 		= $input->getString('email');
		$args['timestamp'] 	= $input->getString('timestamp');
		$args['hour'] 		= $input->getUint('hour');
		$args['min'] 		= $input->getUint('min');
		$args['rating'] 	= $input->getUint('rating', 3);
		$args['published'] 	= $input->getUint('published', 0);
		$args['langtag'] 	= $input->getString('langtag');
		$args['comment'] 	= $input->getString('comment');
		$args['id'] 		= $input->getInt('id');
		
		$id_seremp = $input->getString('id_seremp');

		$args['id_service'] 	= -1;
		$args['id_employee'] 	= -1;
        $getDate = ArasJoomlaVikApp::jgetdate();
		if (empty($args['timestamp']))
		{

			$args['timestamp'] = $getDate[0];
		}
		else
		{
			$args['timestamp'] = VikAppointments::jcreateTimestamp($args['timestamp'], $args['hour'], $args['min']);

			if ($args['timestamp'] == -1)
			{
				$args['timestamp'] = $getDate[0];
			}
		}

		unset($args['hour']);
		unset($args['min']);
		
		$id_seremp = explode('-', $id_seremp);

		if ($id_seremp[0] == 'ser')
		{
			$args['id_service'] = $id_seremp[1];
		}
		if ($id_seremp[0] == 'emp')
		{
			$args['id_employee'] = $id_seremp[1];
		}
		
		$blank_keys = AppointmentsHelper::validateReview($args);
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewReview($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedReview($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newrev' : 'editrev&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editrev&cid[]='.$args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewReview(array $args, $dbo, $app)
	{
		$review = (object) $args;

		$res = $dbo->insertObject('#__vikappointments_reviews', $review, 'id');
				
		if ($res && $review->id > 0)
		{
			$app->enqueueMessage(JText::_('VAPNEWREVIEWCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWREVIEWCREATED0'), 'error');
		}
		
		return $review->id;
	}
	
	private function editSelectedReview(array $args, $dbo, $app)
	{
		$review = (object) $args;

		$dbo->updateObject('#__vikappointments_reviews', $review, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPREVIEWEDITED1'));
		}
	}
	
	/**
	 * SAVE SUBSCRIPTION
	 */
	
	function saveAndCloseSubscription()
	{
		$this->saveSubscription('index.php?option=com_vikappointments&task=subscriptions');
	}

	function saveAndNewSubscription()
	{
		$this->saveSubscription('index.php?option=com_vikappointments&task=newsubscription');
	}
	
	function saveSubscription($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 		= $input->getString('name');
		$args['amount'] 	= $input->getInt('amount', 1);
		$args['type'] 		= $input->getInt('type', 1);
		$args['price'] 		= $input->getFloat('price', 0.0);
		$args['published'] 	= $input->getInt('published', 0);
		$args['trial'] 		= $input->getInt('trial', 0);
		$args['id'] 		= $input->getInt('id');
		
		$blank_keys = AppointmentsHelper::validateSubscription($args);
		
		if (count($blank_keys) == 0)
		{
			if ($args['trial'] == 1)
			{
				// set all subscriptions as no trial
				$this->setSubscrTrial(0, null, $dbo);
			}

			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewSubscription($args, $dbo, $app);
			}
			else
			{
				$this->editSelectedSubscription($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newsubscription' : 'editsubscription&cid[]='.$args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editsubscription&cid[]='.$args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewSubscription(array $args, $dbo, $app)
	{
		$subscr = (object) $args;

		$res = $dbo->insertObject('#__vikappointments_subscription', $subscr, 'id');

		if ($res && $subscr->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWSUBSCRIPTIONCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWSUBSCRIPTIONCREATED0'), 'error');
		}
		
		return $subscr->id;
	}
	
	private function editSelectedSubscription(array $args, $dbo, $app)
	{
		$subscr = (object) $args;

		$dbo->updateObject('#__vikappointments_subscription', $subscr, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPSUBSCRIPTIONEDITED1'));
		}
	}

	private function setSubscrTrial($val, $id = null, $dbo = null)
	{
		if ($dbo === null)
		{
			$dbo = JFactory::getDbo();
		}

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_subscription'))
			->set($dbo->qn('trial') . ' = ' . (int) $val);

		if ($id !== null)
		{
			$q->where($dbo->qn('id') . ' = ' . (int) $id);
		}
		
		$dbo->setQuery($q);
		$dbo->execute();
	}

	function saveLangSubscription()
	{		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['tag'] 			= $input->getString('tag');
		$args['id'] 			= $input->getInt('id');
		$args['id_subscr'] 		= $input->getInt('id_subscr');
		
		$lang = (object) $args;

		if ($args['id'] == -1)
		{
			unset($lang->id);
			$dbo->insertObject('#__vikappointments_lang_subscr', $lang, 'id');
		}
		else
		{
			unset($lang->tag);
			unset($lang->id_subscr);
			$dbo->updateObject('#__vikappointments_lang_subscr', $lang, 'id');
		}
		
		$app->enqueueMessage(JText::_('VAPSUBSCRLANGUPDATED'));
		$app->redirect("index.php?option=com_vikappointments&task=managelangsubscr&tag={$args['tag']}&id_subscr={$args['id_subscr']}");
	}
	
	/**
	 * SAVE SUBSCRIPTION ORDER
	 */
	
	function saveAndCloseSubscriptionOrder()
	{
		$this->saveSubscriptionOrder('index.php?option=com_vikappointments&task=subscrorders');
	}

	function saveAndNewSubscriptionOrder()
	{
		$this->saveSubscriptionOrder('index.php?option=com_vikappointments&task=newsubscrorder');
	}
	
	function saveSubscriptionOrder($return_url = '')
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['id_subscr'] 		= $input->getUint('id_subscr', 0);
		$args['id_employee'] 	= $input->getUint('id_employee', 0);
		$args['id_payment'] 	= $input->getInt('id_payment', 0);
		$args['total_cost'] 	= $input->getFloat('total_cost', 0.0);
		$args['status'] 		= $input->getString('status');
		$args['id'] 			= $input->getInt('id');

		// get subscription

		$subscription = null;

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('amount', 'type')))
			->from($dbo->qn('#__vikappointments_subscription'))
			->where($dbo->qn('id') . ' = ' . $args['id_subscr']);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$subscription = $dbo->loadAssoc();
		}

		// get employee

		$employee = null;

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'nickname', 'active_to')))
			->from($dbo->qn('#__vikappointments_employee'))
			->where($dbo->qn('id') . ' = ' . $args['id_employee']);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employee = $dbo->loadAssoc();
		}
		
		$blank_keys = AppointmentsHelper::validateSubscriptionOrder($args);
		
		if (count($blank_keys) == 0 && $employee && $subscription)
		{
			if ($args['id'] == -1)
			{
				$old_status = null;

				$args['id'] = $this->saveNewSubscriptionOrder($args, $dbo, $app);
			}
			else
			{
				$q = $dbo->getQuery(true)
					->select($dbo->qn('status'))
					->from($dbo->qn('#__vikappointments_subscr_order'))
					->where($dbo->qn('id') . ' = ' . $args['id']);

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				$old_status = $dbo->loadResult();

				$this->editSelectedSubscriptionOrder($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newsubscrorder' : 'editsubscrorder&cid[]='.$args['id']));
		}

		// extend expiration only if the order wasn't confirmed yet
		if ($args['status'] == 'CONFIRMED' && $args['status'] != $old_status)
		{
			/**
			 * Use the method provided by the helper class instead than
			 * the one provided by this class, which is deprecated since 1.7 version.
			 *
			 * @since 1.6.1
			 */
			VikAppointments::applyAdditionalSubscription($subscription, $employee, $app, $dbo);
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editsubscrorder&cid[]='.$args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewSubscriptionOrder(array $args, $dbo, $app)
	{
		$order = (object) $args;
		$order->sid 		= VikAppointments::generateSerialCode(16);
		$order->createdon 	= time();
		
		$res = $dbo->insertObject('#__vikappointments_subscr_order', $order, 'id');
				
		if ($res && $order->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWSUBSCRORDERCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWSUBSCRORDERCREATED0'), 'error');
		}
		
		return $order->id;
	}
	
	private function editSelectedSubscriptionOrder(array $args, $dbo, $app)
	{	
		$order = (object) $args;

		$dbo->updateObject('#__vikappointments_subscr_order', $order, 'id');
		
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPSUBSCRIPTIONEDITED1'));
		}
	}
	
	/**
	 * @deprecated 1.7 	Use VikAppointments::applyAdditionalSubscription() instead.
	 */
	private function applyAdditionalSubscription(array $subscr, array $employee, $dbo, $app)
	{
		if ($subscr['type'] != 5)
		{
			// override the active to only if it is lifetime or pending
			// because you may extend a license already active
			if ($employee['active_to'] == 0 || $employee['active_to'] == -1)
			{
				$employee['active_to'] = time();
			}
		
			$arr = getdate($employee['active_to']);
			
			switch ($subscr['type'])
			{
				// week
				case 2:
					$add_days = 7;
					break;

				// month
				case 3:
					$add_days = 30;
					break;

				// year
				case 4:
					$add_days = 365;
					break;

				// day
				default:
					$add_days = 1;
			}

			$add_days = $add_days * $subscr['amount'] + 1;

			$config 	= UIFactory::getConfig();
			$dt_format 	= $config->get('dateformat') . ' ' . $config->get('timeformat');
			
			$to  = mktime($arr['hour'], $arr['minute'], 0, $arr['mon'], $arr['mday'] + $add_days, $arr['year']);
			$str = date($dt_format, $to);
		}
		else
		{
			$to 	= -1;
			$str 	= JText::_('VAPSUBSCRTYPE5');
		}

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_employee'))
			->set(array(
				$dbo->qn('active_to') . ' = ' . $to,
				$dbo->qn('listable') . ' = 1',
			))
			->where($dbo->qn('id') . ' = ' . $employee['id']);

		$dbo->setQuery($q);
		$dbo->execute();
		
		$app->enqueueMessage(JText::sprintf('VAPSUBSCRIPTIONEXTENDED', $employee['nickname'], $str));
	}
	
	/**
	 * SAVE CRON JOB
	 */
	
	function saveAndCloseCronjob()
	{
		$this->saveCronjob('index.php?option=com_vikappointments&task=cronjobs');
	}

	function saveAndNewCronjob()
	{
		$this->saveCronjob('index.php?option=com_vikappointments&task=newcronjob');
	}
	
	function saveCronjob($return_url = '')
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['name'] 		= $input->getString('name');
		$args['class'] 		= $input->getString('class');
		$args['published'] 	= $input->getUint('published', 0);
		$args['id'] 		= $input->getInt('id');
		$args['params'] 	= array();
		
		$blank_keys = AppointmentsHelper::validateCronjob($args);
		
		if (!empty($args['class']))
		{
			VikAppointments::loadCronLibrary();
			$job = CronDispatcher::includeJob($args['class']);

			if ($job === null)
			{
				$blank_keys[] = 'class';
			}
			else
			{
				foreach ($job::getConfiguration() as $f)
				{
					$args['params'][$f->getName()] = $input->getRaw($f->getName(), '');
					
					if ($f->isRequired() && empty($args['params'][$f->getName()]))
					{
						$blank_keys[] = $f->getName();
					}
				}
			}
		}

		$args['params'] = json_encode($args['params']);
		
		if (count($blank_keys) == 0)
		{
			if ($args['id'] == -1)
			{
				$args['id'] = $this->saveNewCronjob($args, $dbo, $app);

				$cron = new $job($args['id']);
				
				if ($cron->install())
				{
					$app->enqueueMessage(JText::sprintf('VAPCRONJOBINSTALLED1', $args['class']));
				}
				else
				{
					$app->enqueueMessage(JText::sprintf('VAPCRONJOBINSTALLED0', $args['class']), 'error');
				}
			}
			else
			{
				$this->editSelectedCronjob($args, $dbo, $app);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPREQUIREDFIELDSERROR'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=' . ($args['id'] == -1 ? 'newcronjob' : 'editcronjob&cid[]=' . $args['id']));
		}
		
		if (empty($return_url))
		{
			$return_url = 'index.php?option=com_vikappointments&task=editcronjob&cid[]=' . $args['id'];
		}

		$app->redirect($return_url);
	}
	
	private function saveNewCronjob(array $args, $dbo, $app)
	{
		$cron = (object) $args;

		$res = $dbo->insertObject('#__vikappointments_cronjob', $cron, 'id');
		
		if ($res && $cron->id)
		{
			$app->enqueueMessage(JText::_('VAPNEWCRONJOBCREATED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPNEWCRONJOBCREATED0'), 'error');
		}
		
		return $cron->id;
	}
	
	private function editSelectedCronjob(array $args, $dbo, $app)
	{
		$cron = (object) $args;

		$dbo->updateObject('#__vikappointments_cronjob', $cron, 'id');
			
		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPCRONJOBEDITED1'));
		}
	}
	
	/**
	 * SAVE CONFIGURATION
	 */
	
	function saveConfiguration()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['agencyname'] 		= $input->getString('agencyname');
		$args['ismultilang'] 		= $input->getUint('ismultilang', 0);
		$args['adminemail'] 		= $input->getString('adminemail');
		$args['senderemail'] 		= $input->getString('senderemail');
		$args['companylogo'] 		= $input->getString('companylogo');
		$args['mailattach'] 		= $input->files->get('mailattach', null, 'array');
		$args['dateformat'] 		= $input->getString('dateformat');
		$args['timeformat'] 		= $input->getString('timeformat');
		$args['formatduration'] 	= $input->getUint('formatduration', 0);
		$args['currencysymb'] 		= $input->getString('currencysymb');
		$args['currencyname'] 		= $input->getString('currencyname');
		$args['currsymbpos'] 		= $input->getInt('currsymbpos');
		$args['currdecimalsep'] 	= $input->getString('currdecimalsep');
		$args['currthousandssep'] 	= $input->getString('currthousandssep');
		$args['currdecimaldig'] 	= $input->getInt('currdecimaldig');
		$args['defstatus'] 			= $input->getString('defstatus');
		$args['minuteintervals'] 	= $input->getInt('minuteintervals');
		$args['numcals'] 			= $input->getInt('numcals');
		$args['nummonths'] 			= $input->getInt('nummonths');
		$args['calsfrom'] 			= $input->getInt('calsfrom');
		$args['calsfromyear'] 		= $input->getInt('calsfromyear', 2016);
		$args['legendcal'] 			= $input->getInt('legendcal', 0);
		$args['minrestr'] 			= $input->getInt('minrestr');
		$args['emplistlim'] 		= $input->getInt('emplistlim');
		$args['emplistmode'] 		= $input->get('emplistmode', array(), 'array');
		$args['empdesclength'] 		= $input->getInt('empdesclength', 256);
		$args['emplinkhref'] 		= $input->getInt('emplinkhref', 1);
		$args['empgroupfilter'] 	= $input->getUint('empgroupfilter', 0);
		$args['empordfilter'] 		= $input->getUint('empordfilter', 0);
		$args['empajaxsearch'] 		= $input->getUint('empajaxsearch', 0);
		$args['serdesclength'] 		= $input->getInt('serdesclength', 256);
		$args['serlinkhref'] 		= $input->getInt('serlinkhref', 1);
		$args['keepapplock'] 		= $input->getInt('keepapplock');
		$args['deftask'] 			= $input->getString('deftask');
		$args['askconfirm'] 		= $input->getUint('askconfirm', 0);
		$args['loadjquery'] 		= $input->getUint('loadjquery', 0);
		$args['showfooter'] 		= $input->getUint('showfooter', 0);
		$args['enablecanc'] 		= $input->getUint('enablecanc', 0);
		$args['canctime'] 			= $input->getInt('canctime');
		$args['usercredit'] 		= $input->getUint('usercredit', 0);
		$args['loginreq'] 			= $input->getInt('loginreq');
		$args['enablecart'] 		= $input->getUint('enablecart', 0);
		$args['maxcartsize'] 		= $input->getInt('maxcartsize', -1);
		$args['cartallowsync'] 		= $input->getInt('cartallowsync');
		$args['shoplink'] 			= $input->getInt('shoplink');
		$args['shoplinkcustom'] 	= $input->getString('shoplinkcustom');
		$args['confcartdisplay'] 	= $input->getUint('confcartdisplay', 0);
		$args['refreshtime'] 		= $input->getInt('refreshtime', 30);
		$args['usedeposit'] 		= $input->getUint('usedeposit', 0);
		$args['depositafter'] 		= $input->getFloat('depositafter', 300);
		$args['depositvalue'] 		= abs($input->getFloat('depositvalue', 40));
		$args['deposittype'] 		= $input->getInt('deposittype', 1);
		$args['showphprefix'] 		= $input->getUint('showphprefix', 0);
		$args['printorders'] 		= $input->getUint('printorders', 0);
		$args['invoiceorders'] 		= $input->getUint('invoiceorders', 0);
		$args['showcheckout'] 		= $input->getUint('showcheckout', 0);
		$args['mailcustwhen'] 		= $input->getInt('mailcustwhen', 1);
		$args['mailempwhen'] 		= $input->getInt('mailempwhen', 1);
		$args['mailadminwhen'] 		= $input->getInt('mailadminwhen', 2);
		$args['mailtmpl'] 			= $input->getString('mailtmpl', '');
		$args['adminmailtmpl'] 		= $input->getString('adminmailtmpl', '');
		$args['empmailtmpl'] 		= $input->getString('empmailtmpl', '');
		$args['cancmailtmpl'] 		= $input->getString('cancmailtmpl', '');
		$args['packmailtmpl'] 		= $input->getString('packmailtmpl', '');
		$args['synckey'] 			= $input->getString('synckey', 'secret');
		$args['multitimezone'] 		= $input->getUint('multitimezone', 0);
		$args['firstday'] 			= $input->getInt('firstday');
		$args['googleapikey'] 		= $input->getString('googleapikey');
		$args['sitetheme'] 			= $input->getString('sitetheme');
		$args['router'] 			= $input->getUint('router', 0);
		$args['icsattach'] 			= $input->getUint('icsattach1', 0).';'.$input->getUint('icsattach2', 0).';'.$input->getUint('icsattach3', 0);
		$args['csvattach'] 			= $input->getUint('csvattach1', 0).';'.$input->getUint('csvattach2', 0).';'.$input->getUint('csvattach3', 0);
		
		$args['openingtime'] 		= $input->getUint('openinghour') . ':' . $input->getUint('openingmin');
		$args['closingtime'] 		= $input->getUint('closinghour') . ':' . $input->getUint('closingmin');
		
		$args['enablerecur'] 		= $input->getUint('enablerecur', 0);
		$args['repeatbyrecur'] 		= $input->getUint('repeatby1', 0).';'.$input->getUint('repeatby2', 0).';'.$input->getUint('repeatby3', 0);
		$args['minamountrecur'] 	= abs($input->getInt('minamountrecur', 1));
		$args['maxamountrecur'] 	= $input->getInt('maxamountrecur', 12);
		$args['fornextrecur'] 		= $input->getUnt('fornext1', 0).';'.$input->getUint('fornext2', 0).';'.$input->getUint('fornext3', 0);
		
		$args['enablereviews'] 		= $input->getUint('enablereviews', 0);
		$args['revservices'] 		= $input->getUint('revservices', 0);
		$args['revemployees'] 		= $input->getUint('revemployees', 0);
		$args['revcommentreq'] 		= $input->getUint('revcommentreq', 0);
		$args['revminlength'] 		= max(array(0, $input->getInt('revminlength')));
		$args['revmaxlength'] 		= min(array(2048, $input->getInt('revmaxlength')));
		$args['revlimlist'] 		= $input->getInt('revlimlist', 5);
		$args['revlangfilter'] 		= $input->getUint('revlangfilter', 0);
		$args['revautopublished'] 	= $input->getUint('revautopublished', 0);
		$args['revloadmode'] 		= $input->getInt('revloadmode', 1);

		$args['enablewaitlist'] 	= $input->getUint('enablewaitlist', 0);
		$args['waitlistsmscont'] 	= $input->get('waitlistsmscont', array(), 'array');
		$args['waitlistmailtmpl'] 	= $input->getString('waitlistmailtmpl', '');

		$args['enablepackages'] 	= $input->getUint('enablepackages', 0);
		$args['packsperrow'] 		= $input->getInt('packsperrow');
		$args['maxpackscart'] 		= $input->getInt('maxpackscart');
		$args['packsreguser'] 		= $input->getUint('packsreguser', 0);
		
		$args['listablecols'] 		= $input->get('listablecols', array(), 'array');
		$args['listablecf'] 		= $input->get('listablecf', array(), 'array');

		$args['gdpr'] 				= $input->getUint('gdpr', 0);
		$args['policylink'] 		= $input->getString('policylink', '');

		$args['conversion_track']	= $input->get('conversion_track', 0, 'uint');
		
		$languages = VikAppointments::getKnownLanguages();	

		////////

		$sms_wl_cont = array();
		for ($i = 0; $i < count($languages); $i++)
		{
			for ($j = 0; $j < 2; $j++)
			{
				$sms_wl_cont[$j][$languages[$i]] = $args['waitlistsmscont'][$j][$i];
			}
		}
		$args['waitlistsmscont'] = json_encode($sms_wl_cont);
		
		$args['zipcfid'] 	= $input->getInt('zipcfid', -1);
		$args['zipcodes'] 	= array();

		if ($args['zipcfid'] == 0)
		{
			$args['zipcfid'] = -1;
		}
		
		$zc_from_arr = array();
		$zc_to_arr 	 = array();
		
		if ($args['zipcfid'] != -1)
		{
			$zc_from_arr = $input->get('zip_code_from', array(), 'array');
			$zc_to_arr 	 = $input->get('zip_code_to', array(), 'array');
		}

		// invoice

		$invoice = VikAppointments::getPdfParams();

		$invoice['invoicenumber'] 	= $input->getInt('attr_invoicenumber', 1);
		$invoice['invoicesuffix'] 	= $input->getString('attr_invoicesuffix', '');
		$invoice['datetype'] 		= $input->getInt('attr_datetype', 1);
		$invoice['taxes'] 			= $input->getFloat('attr_taxes', 20);
		$invoice['legalinfo'] 		= $input->getString('attr_legalinfo', '');
		$invoice['sendinvoice'] 	= $input->getUint('attr_sendinvoice', 0);
		
		$i_properties = VikAppointments::getPdfConstraints();

		$i_properties->pageOrientation 	= $input->getString('prop_page_orientation', 'P');
		$i_properties->pageFormat 		= $input->getString('prop_page_format', 'A4');
		$i_properties->unit 			= $input->getString('prop_unit', 'mm');
		$i_properties->imageScaleRatio 	= max(array(5, $input->getFloat('prop_scale', 125))) / 100;

		$args['pdfparams'] 		= json_encode($invoice);
		$args['pdfconstraints'] = json_encode($i_properties);
		
		// validation

		if (empty($args['senderemail']))
		{
			$args['senderemail'] = JFactory::getUser()->email;
		}

		if ($args['shoplink'] != -2)
		{
			$args['shoplinkcustom'] = '';
		}
		else if (!strlen($args['shoplinkcustom']))
		{
			$args['shoplinkcustom'] = 'index.php';
		}
		
		if ($args['keepapplock'] < 5)
		{
			$args['keepapplock'] = 5;
		}
		
		if ($args['deposittype'] == 1)
		{
			if ($args['depositvalue'] > 99)
			{
				$args['depositvalue'] = 40;
			}
		}
		
		if (empty($args['mailtmpl']))
		{
			$args['mailtmpl'] = 'email_tmpl.php';
		}
		else
		{
			$args['mailtmpl'] = basename($args['mailtmpl']);
		}

		if (empty($args['adminmailtmpl']))
		{
			$args['adminmailtmpl'] = 'admin_email_tmpl.php';
		}
		else
		{
			$args['adminmailtmpl'] = basename($args['adminmailtmpl']);
		}

		if (empty($args['empmailtmpl']))
		{
			$args['empmailtmpl'] = 'employee_email_tmpl.php';
		}
		else
		{
			$args['empmailtmpl'] = basename($args['empmailtmpl']);
		}

		if (empty($args['cancmailtmpl']))
		{
			$args['cancmailtmpl'] = 'cancellation_email_tmpl.php';
		}
		else
		{
			$args['cancmailtmpl'] = basename($args['cancmailtmpl']);
		}

		if (empty($args['packmailtmpl']))
		{
			$args['packmailtmpl'] = 'packages_email_tmpl.php';
		}
		else
		{
			$args['packmailtmpl'] = basename($args['packmailtmpl']);
		}

		if (empty($args['waitlistmailtmpl']))
		{
			$args['waitlistmailtmpl'] = 'waitlist_email_tmpl.php';
		}
		else
		{
			$args['waitlistmailtmpl'] = basename($args['waitlistmailtmpl']);
		}

		// emp list mode
		$active = false;
		foreach ($args['emplistmode'] as $v)
		{
			$active |= $v;
		}

		if (!$active)
		{
			$args['emplistmode'][1] = 1;
		}

		$args['emplistmode'] = json_encode($args['emplistmode']);
		
		// recurrence
		if ($args['minamountrecur'] > $args['maxamountrecur'])
		{
			$args['maxamountrecur'] = $args['minamountrecur'];
		}
		
		if ($args['repeatbyrecur'] == '0;0;0')
		{
			$args['repeatbyrecur'] = '0;1;1';
		}
		
		if ($args['fornextrecur'] == '0;0;0')
		{
			$args['fornextrecur'] = '1;1;1';
		}

		// listable columns
		$listable_cols = array();

		foreach ($args['listablecols'] as $k => $v)
		{
			$tmp = explode(':', $v);

			if ($tmp[1] == 1)
			{
				$listable_cols[] = $tmp[0];
			} 
		}

		$args['listablecols'] = implode(',', $listable_cols);
		
		// listable columns CF
		$listable_cols = array();

		foreach ($args['listablecf'] as $k => $v)
		{
			$tmp = explode(':', $v);

			if ($tmp[1] == 1)
			{
				$listable_cols[] = $tmp[0];
			} 
		}

		$args['listablecf'] = json_encode($listable_cols);
		//

		if (count($zc_from_arr))
		{
			$_len = min(array(count($zc_from_arr), count($zc_to_arr)));

			for ($i = 0; $i < $_len; $i++)
			{
				if (!empty($zc_from_arr[$i]))
				{
					if (empty($zc_to_arr[$i]))
					{
						$zc_to_arr[$i] = $zc_from_arr[$i];
					}
					
					$args['zipcodes'][] = array(
						'from' 	=> $zc_from_arr[$i],
						'to' 	=> $zc_to_arr[$i],
					);
				}
			}
		}

		$args['zipcodes'] = json_encode($args['zipcodes']);
		
		if (isset($args['mailattach']) && strlen(trim($args['mailattach']['name'])))
		{
			$file_name = $args['mailattach']['name'];
			$attachment = VikAppointments::uploadFile($args['mailattach'], '*', VAPBASE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mail_attach' . DIRECTORY_SEPARATOR, $file_name);
			$args['mailattach'] = $attachment['name'];
		}
		else
		{
			$args['mailattach'] = VikAppointments::getMailAttachment(true);
			
			if ($input->getBool('remove_mail_attach', false))
			{
				unlink(VAPBASE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mail_attach' . DIRECTORY_SEPARATOR . $args['mailattach']);
				$args['mailattach'] = '';
			}
		}
		
		// end validation

		$affected = false;
		
		foreach ($args as $key => $val)
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_config'))
				->set($dbo->qn('setting') . ' = ' . $dbo->q($val))
				->where($dbo->qn('param') . ' = ' . $dbo->q($key));

			$dbo->setQuery($q);
			$dbo->execute();

			$affected = $affected || (bool) $dbo->getAffectedRows();
		}
		
		$app->enqueueMessage(JText::_('VAPCONFIGEDITED1'));
		$app->redirect('index.php?option=com_vikappointments&task=editconfig');
	}

	/**
	 * SAVE CONFIGURATION EMPLOYEE
	 */
	
	function saveConfigurationEmployees()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['empsignup'] 			= $input->getUint('empsignup', 0);
		$args['empsignstatus'] 		= $input->getUint('empsignstatus', 1);
		$args['empsignrule'] 		= $input->getString('empsignrule', 3); // use string for Wordpress BC
		$args['empassignser'] 		= implode(',', $input->get('empassignser', array(), 'array'));
		$args['empcreate'] 			= $input->getUint('empcreate', 0);
		$args['empmanage'] 			= $input->getUint('empmanage', 0);
		$args['empattachser']		= $input->getUint('empattachser', 0);
		$args['empmanageser'] 		= $input->getUint('empmanageser', 0);
		$args['empmanagerate'] 		= $input->getUint('empmanagerate', 0);
		$args['empmanagepay'] 		= $input->getUint('empmanagepay', 0);
		$args['empmanagewd'] 		= $input->getUint('empmanagewd', 0);
		$args['empmanageloc'] 		= $input->getUint('empmanageloc', 0);
		$args['empmanagecoupon'] 	= $input->getUint('empmanagecoupon', 0);
		$args['empmanagecustfield'] = $input->getUint('empmanagecustfield', 0);
		$args['empremove'] 			= $input->getUint('empremove', 0);
		$args['empmaxser'] 			= $input->getUint('empmaxser', 5);
		$args['emprescreate'] 		= $input->getUint('emprescreate', 0);
		$args['empresmanage'] 		= $input->getUint('empresmanage', 0);
		$args['empresremove'] 		= $input->getUint('empresremove', 0);
		$args['empresnotify'] 		= $input->getUint('empresnotify', 0);
		$args['empresconfirm'] 		= $input->getUint('empresconfirm', 0);

		if ($args['empmaxser'] < 1)
		{
			$args['empmaxser'] = 1;
		}

		// if employees can create, then they can also attach esisting services
		$args['empattachser'] |= $args['empcreate'];
		
		$affected = false;
		
		foreach ($args as $key => $val)
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_config'))
				->set($dbo->qn('setting') . ' = ' . $dbo->q($val))
				->where($dbo->qn('param') . ' = ' . $dbo->q($key));

			$dbo->setQuery($q);
			$dbo->execute();

			$affected = $affected || (bool) $dbo->getAffectedRows();
		}
		
		$app->enqueueMessage(JText::_('VAPCONFIGEDITED1'));
		$app->redirect('index.php?option=com_vikappointments&task=editconfigemp');
	}

	/**
	 * SAVE CONFIGURATION CLOSING DAYS
	 */
	
	function saveConfigurationClosingDays()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();

		$cd_arr = $input->get('closing_days', array(), 'array');
		$cp_arr = $input->get('closing_periods', array(), 'array');
		
		// fetch closing days
		
		$args['closingdays'] = array();
		
		for ($i = 0; $i < count($cd_arr); $i++)
		{
			$_cd = explode(':', $cd_arr[$i]);

			$services = empty($_cd[2]) || $_cd[2] == '*' ? '*' : $_cd[2];

			$args['closingdays'][] = VikAppointments::jcreateTimestamp($_cd[0] , 0, 0) . ':' . $_cd[1] . ':' . $services;
		}

		$args['closingdays'] = implode(';;', $args['closingdays']);

		// fetch closing periods

		$args['closingperiods'] = array(); 
		
		for ($i = 0; $i < count($cp_arr); $i++)
		{
			$_cp = explode(';;', $cp_arr[$i]);
			
			$start 	= VikAppointments::jcreateTimestamp($_cp[0], 0, 0);
			$end 	= VikAppointments::jcreateTimestamp($_cp[1], 23, 59);

			if ($start != -1 && $end != -1 && $start < $end)
			{
				$services = empty($_cp[2]) || $_cp[2] == '*' ? '*' : $_cp[2];

				$args['closingperiods'][] = $start . '-' . $end . '-' . $services;
			}
		}

		$args['closingperiods'] = implode(';;', $args['closingperiods']);
		
		// end validation
		
		$affected = false;
		
		foreach ($args as $key => $val)
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_config'))
				->set($dbo->qn('setting') . ' = ' . $dbo->q($val))
				->where($dbo->qn('param') . ' = ' . $dbo->q($key));

			$dbo->setQuery($q);
			$dbo->execute();

			$affected = $affected || (bool) $dbo->getAffectedRows();
		}
		
		$app->enqueueMessage(JText::_('VAPCONFIGEDITED1'));
		$app->redirect('index.php?option=com_vikappointments&task=editconfigcldays');
	}

	/**
	 * SAVE CONFIGURATION SMS API
	 */
	
	function saveConfigurationSmsApi()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['smsapi'] 			= $input->getString('smsapi');
		$args['smsenabled'] 		= $input->getUint('smsenabled', 0);
		$args['smsapito'] 			= $input->getUint('smsapitocust', 0).','.$input->getUint('smsapitoemp', 0).','.$input->getUint('smsapitoadmin', 0);
		$args['smsapiadminphone'] 	= $input->getString('smsapiadminphone');
		$args['smsapifields'] 		= '';
		$args['smstmplcust'] 		= array();
		$args['smstmpladmin'] 		= array();

		$sms_cust_tmpl  = $input->get('smstmplcust', array(), 'array');
		$sms_admin_tmpl = $input->get('smstmpladmin', array(), 'array');
		
		$languages = VikAppointments::getKnownLanguages();
		
		for ($i = 0; $i < count($languages); $i++)
		{
			$args['smstmplcust'][$languages[$i]] 		= $sms_cust_tmpl[0][$i];
			$args['smstmplcustmulti'][$languages[$i]] 	= $sms_cust_tmpl[1][$i];
		}

		$args['smstmplcust'] 		= json_encode($args['smstmplcust']);
		$args['smstmplcustmulti'] 	= json_encode($args['smstmplcustmulti']);
		
		$args['smstmpladmin'] 		= $sms_admin_tmpl[0];
		$args['smstmpladminmulti'] 	= $sms_admin_tmpl[1];
		
		// validation

		$sms_api_path = VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $args['smsapi'];
		
		if (file_exists($sms_api_path) && strlen($args['smsapi']))
		{
			require_once $sms_api_path;

			$sms_params = array();

			foreach (VikSmsApi::getAdminParameters() as $k => $p)
			{
				$sms_params[$k] = $input->getString($k, '');
			}
			
			$args['smsapifields'] = json_encode($sms_params);
		}
		
		// end validation
		
		$affected = false;
		
		foreach ($args as $key => $val)
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_config'))
				->set($dbo->qn('setting') . ' = ' . $dbo->q($val))
				->where($dbo->qn('param') . ' = ' . $dbo->q($key));

			$dbo->setQuery($q);
			$dbo->execute();

			$affected = $affected || (bool) $dbo->getAffectedRows();
		}
		
		$app->enqueueMessage(JText::_('VAPCONFIGEDITED1'));
		$app->redirect('index.php?option=com_vikappointments&task=editconfigsmsapi');
	}

	/**
	 * SAVE CONFIGURATION CRON
	 */
	
	function saveConfigurationCron()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$args = array();
		$args['cron_secure_key'] 	= $input->getString('cron_secure_key');
		$args['cron_log_mode'] 		= $input->getInt('cron_log_mode');
		
		if (empty($args['cron_secure_key']))
		{
			$args['cron_secure_key'] = VikAppointments::generateSerialCode(16);
		}
		
		$affected = false;
		
		foreach ($args as $key => $val)
		{
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_config'))
				->set($dbo->qn('setting') . ' = ' . $dbo->q($val))
				->where($dbo->qn('param') . ' = ' . $dbo->q($key));

			$dbo->setQuery($q);
			$dbo->execute();

			$affected = $affected || (bool) $dbo->getAffectedRows();
		}
		
		$app->enqueueMessage(JText::_('VAPCONFIGEDITED1'));
		$app->redirect('index.php?option=com_vikappointments&task=editconfigcron');
	}

	/**
	 * Changes the status of the specified column.
	 * The parameters are retrieved from the REQUEST pool.
	 *
	 * @param 	string 	 $table_db 		The database table.
	 * @param 	string 	 $column_db 	The column to affect.
	 * @param 	integer  $val 			The current value.
	 * @param 	integer  $id 			The ID of the record.
	 * @param 	string 	 $return_task 	The return task after the update.
	 * @param 	array 	 $params 		An associative array of custom parameters
	 * 									that can be pushed within the return URL.
	 *
	 * @return 	void
	 */
	function changeStatusColumn()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$table 	= $input->getString('table_db', '');
		$column = $input->getString('column_db', '');
		$val 	= $input->getInt('val', 0);
		$id 	= $input->getInt('id', 0);
		$task 	= $input->getString('return_task', '');
		$params = $input->get('params', array(), 'array');

		$val = ($val + 1) % 2;
		
		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_' . $table))
			->set($dbo->qn($column) . ' = ' . $val)
			->where($dbo->qn('id') . ' = ' . $id);
		
		$dbo->setQuery($q);
		$dbo->execute();

		if (count($params))
		{
			$task .= '&' . http_build_query($params);
		}
		
		$app->redirect('index.php?option=com_vikappointments&task=' . $task);
	}
	
	function changeReservationStatusColumn()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$lookup = array('CONFIRMED', 'REMOVED', 'PENDING');
		
		$status_index 	= $input->getUint('s_index');
		$id_res 		= $input->getUint('id_res');

		$status_index = ($status_index + 1) % 3;
		
		$locked_until = time() + UIFactory::getConfig()->getUint('keepapplock') * 60;

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_reservation'))
			->set($dbo->qn('status') . ' = ' . $dbo->q($lookup[$status_index]))
			->set($dbo->qn('locked_until') . ' = ' . $locked_until)
			->where(array(
				$dbo->qn('id') . ' = ' . $id_res,
				$dbo->qn('id_parent') . ' = ' . $id_res,
			), 'OR');
		
		$dbo->setQuery($q);
		$dbo->execute();

		// The status has been changed, we can track it.
		// The children of a multi-order won't be affected here.
		VAPOrderStatus::getInstance()->keepTrack($lookup[$status_index], $id_res, 'VAP_STATUS_CHANGED_FROM_LIST');
		
		$app->redirect('index.php?option=com_vikappointments&task=reservations');
	}
	
	function changeReservationPaidColumn()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$paid 	= $input->getUint('val', 0);
		$id_res = $input->getUint('id_res');

		$paid = ($paid + 1) % 2;

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_reservation'))
			->set($dbo->qn('paid') . ' = ' . $paid)
			->where(array(
				$dbo->qn('id') . ' = ' . $id_res,
				$dbo->qn('id_parent') . ' = ' . $id_res,
			), 'OR');
		
		$dbo->setQuery($q);
		$dbo->execute();
		
		$app->redirect('index.php?option=com_vikappointments&task=reservations');
	}

	function changeSubscrTrial()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$val 	= $input->getUint('val', 0);
		$id 	= $input->getUint('id', 0);

		$val = ($val + 1) % 2;
		
		if ($val == 1)
		{
			// set all subscriptions as no trial
			$this->setSubscrTrial(0, null, $dbo);
		}

		// switch column value for the specified ID
		$this->setSubscrTrial($val, $id, $dbo);
		
		$app->redirect('index.php?option=com_vikappointments&task=subscriptions');
	}
	
	function store_total_cost()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$id_res = $input->getUint('id');
		$tcost 	= $input->getFloat('total_cost');

		$tcost = abs($tcost);

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_reservation'))
			->set($dbo->qn('total_cost') . ' = ' . $tcost)
			->where($dbo->qn('id') . ' = ' . $id_res);
		
		$dbo->setQuery($q);
		$dbo->execute();
		
		echo json_encode(array(1, $tcost));
		die;
	}

	function store_dashboard_properties()
	{
		$input = JFactory::getApplication()->input;

		$prop = array(
			'appointments' 	=> $input->getUint('a_page', 1),
			'waiting' 		=> $input->getUint('w_page', 1),
			'customers' 	=> $input->getUint('c_page', 1),
			'packages' 		=> $input->getUint('p_page', 1),
		);
		
		JFactory::getSession()->set('dashboard-properties', $prop, 'vap');
		exit;
	}

	function store_mainmenu_status()
	{
		$status = JFactory::getApplication()->input->getUint('status', 1);

		UIFactory::getConfig()->set('mainmenustatus', $status);
	}
	
	function upload_zip_file()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$config = UIFactory::getConfig();
		
		/**
		 * The file must contain only one ZIP code per line.
		 * The system will sort the zip codes and will try
		 * to group them in case of contiguous codes.
		 */
		$file = $input->files->get('file', null, 'array');
		
		jimport('joomla.filesystem.file');
		
		$dest = VAPADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR;
		
		$esit = 0;
		
		$finaldest = '';
		
		if (isset($file) && strlen(trim($file['name'])))
		{
			$filename 	= JFile::makeSafe(str_replace(" ", "_", strtolower($file['name'])));
			$src 		= $file['tmp_name'];
			$j 			= "";

			if (file_exists($dest . $filename))
			{
				$j = rand(1, 999);
				while (file_exists($dest . $j . $filename))
				{
					$j++;
				}
			}

			$finaldest = $dest . $j . $filename;
			
			if (JFile::upload($src, $finaldest ))
			{
				$esit = 1;
			}
		} 
		
		if ($esit)
		{
			$handle = fopen($finaldest, 'r');

			$zips = array();

			while (!feof($handle))
			{
				$zips[] = trim(preg_replace('/\s+/', ' ', fgets($handle)));
			}

			fclose($handle);

			if (count($zips))
			{
				sort($zips);
			
				$from_zip 	= array();
				$to_zip 	= array();
				
				$start 	= $zips[0];
				$end 	= $zips[0];
				$ok 	= false;

				for ($i = 1; $i < count($zips); $i++)
				{
					if ($zips[$i-1] + 1 == $zips[$i])
					{
						$end = $zips[$i];
						$ok  = true;
					}
					else
					{
						$from_zip[] = $start;
						$to_zip[] 	= $end;
						
						$start 	= $zips[$i];
						$end 	= $zips[$i];
						$ok 	= false;
					}
					
					if ($i == count($zips) - 1)
					{
						$from_zip[] = $start;
						$to_zip[] 	= $end;
					}
				}
				
				$counter = 0;

				$args = $config->getJSON('zipcodes', array());
				
				if (count($from_zip))
				{
					$_len = min(array(count($from_zip), count($to_zip)));
					
					for ($i = 0; $i < $_len; $i++)
					{
						if (!empty($from_zip[$i]))
						{
							if (empty($to_zip[$i]))
							{
								$to_zip[$i] = $from_zip[$i];
							}
							
							$args[] = array(
								"from" 	=> $from_zip[$i],
								"to" 	=> $to_zip[$i],
							);
							
							$counter++;
						}
					}

					$config->set('zipcodes', $args);
					
					$app->enqueueMessage(JText::sprintf('VAPCONFIGZIPUPLOADEDMSG', $counter));
				}
			}

			unlink($finaldest);
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPCONFIGUPLOADFILEERR'), 'error');
		}
		
		$app->redirect('index.php?option=com_vikappointments&task=editconfig');
	}

	function storefile()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		if (!JFactory::getUser()->authorise('core.admin'))
		{
			/**
			 * File management is restricted to super users only.
			 *
			 * @since 1.6
			 */
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$file 		= $input->getString('file');
		$code 		= $input->getRaw('code', '');
		$as_copy 	= $input->getUint('ascopy', 0);
		
		$file_name = basename($file);
		
		if ($as_copy)
		{
			$file_path = rtrim(dirname($file), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
			$new_name  = $input->getString('newname', '');

			if (empty($new_name))
			{
				$j = 2;
				
				while (file_exists($file_path . $j . $file_name))
				{
					$j++;
				}

				$file = $file_path . $j . $file_name;
			}
			else
			{
				$file = $file_path . $new_name;
			}
		}
		
		$handle = fopen($file, 'wb');
		$bytes  = fwrite($handle, $code);
		fclose($handle);
		
		if ($bytes)
		{
			$app->enqueueMessage(JText::_('VAPMANAGEFILESAVED1'));
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPMANAGEFILESAVED0'), 'error');
		}

		$app->redirect('index.php?option=com_vikappointments&task=managefile&file=' . $file);
	}
	
	// AJAX UTILS
	
	function get_day_time_line()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$id_emp = $input->getUint('id_emp', 0);
		$day 	= $input->getUint('day', 0);
		$id_ser = $input->getUint('id_ser', 0);
		$id_res = $input->getInt('id_res', -1);
		$people = $input->getUint('people', 1);

		if (empty($id_res))
		{
			$id_res = -1;
		}
		
		$employee_tz = VikAppointments::getEmployeeTimezone($id_emp);
		VikAppointments::setCurrentTimezone($employee_tz);
		
		if (VikAppointments::isClosingDay($day, $id_ser))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESCLOSINGDAY')));
			die;
		}
		
		$worktime = VikAppointments::getEmployeeWorkingTimes($id_emp, $id_ser, $day);
		
		if (!count($worktime))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESNODAYEMPLOYEE')));
			die;
		}
		
		$bookings = VikAppointments::getAllEmployeeReservationsExcludingResId($id_emp, $id_ser, $id_res, $day, $day + 86399);
		
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'duration', 'sleep', 'interval', 'max_capacity', 'app_per_slot', 'display_seats', 'priceperpeople')))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . $id_ser);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo json_encode(array(0, JText::_('VAPFINDRESSERVICENOTEXISTS')));
			die;
		}
		
		$service = $dbo->loadAssoc();

		$seats = array();
		
		if ($service['max_capacity'] == 1 || $service['app_per_slot'] == 0)
		{
			$arr = VikAppointments::elaborateTimeLineTimezone($worktime, $bookings, $service, $employee_tz);
		}
		else
		{
			$arr = array(VikAppointments::elaborateTimeLineGroupServiceTimezone($worktime, $bookings, $service, $people, $employee_tz, $seats));
		}

		$_d = getdate($day);

		$json  = array();
		$rates = array();

		for ($i = 0; $i < count($arr); $i++)
		{
			foreach ($arr[$i] as $k => $v)
			{
				if ($k >= 1440)
				{
					/**
					 * Unset the time slots that exceeds the midnight.
					 *
					 * @since 1.6
					 */
					unset($arr[$i][$k]);
				}
				else
				{
					$hour = floor($k / 60);
					$min  = $k % 60;

					$checkin = mktime($hour, $min, 0, $_d['mon'], $_d['mday'], $_d['year']);

					// calculate rate for the current time block
					$rates[$k] = VAPSpecialRates::getRate($id_ser, $id_emp, $checkin, $people);

					if ($service['priceperpeople'])
					{
						$rates[$k] *= $people;
					}
				}
			}

			if (count($arr[$i]))
			{
				$json[] = $arr[$i];
			}
		}

		// build array data for timeline layout
		$data = array(
			'id_service'  => $id_ser,
			'id_employee' => $id_emp,
			'checkinDay'  => $day,
			'duration' 	  => $service['duration'],
			'times'		  => $json,
			'rates'		  => $rates,
		);

		// switch timeline layout depending on the configuration of the system
		if ($service['display_seats'])
		{
			/**
			 * We are here as the service needs to display the remaining seats.
			 *
			 * @layout 	timeline/seats.php
			 */
			$layout = 'seats';

			// make $seats array available for layout
			$data['seats'] 		= $seats;
			$data['totalSeats'] = $service['max_capacity'];
		}
		else if (count(array_unique($rates)) > 1 || (float) reset($rates) != VAPSpecialRates::getBaseCost($id_ser, $id_emp))
		{
			/**
			 * We are here as a result of these conditions:
			 * - the rates list contains different prices
			 * - the first element (as well as any other element) is not equals to the base cost
			 *
			 * @layout 	timeline/ratesgrid.php
			 */
			$layout = 'ratesgrid';
		}
		else
		{
			/**
			 * Use the standard timeline layout.
			 *
			 * @layout 	timeline/default.php
			 */
			$layout = 'default';
		}

		/**
		 * The timeline HTML block is displayed from the layout below:
		 * /components/com_vikappointments/layouts/timeline/default.php
		 * 
		 * If you need to change something from this layout, just create
		 * an override of this layout by following the instructions below:
		 * - open the back-end of your Joomla
		 * - visit the Extensions > Templates > Templates page
		 * - edit the active template
		 * - access the "Create Overrides" tab
		 * - select Layouts > com_vikappointments > timeline
		 * - start editing all the files on your template to create your own layouts
		 *
		 * @since 1.6
		 */
		$base     = VAPBASE . DIRECTORY_SEPARATOR . 'layouts';
		$timeline = JLayoutHelper::render('timeline.' . $layout, $data, $base);
		
		echo json_encode(array(1, $json, $timeline, null));
		die;
	}
	
	function get_day_time_line_all_employees()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$day 	= $input->getUint('day');
		$id_ser = $input->getUint('id_ser', 0);
		$people = $input->getUint('people', 1);
		
		$date = getdate();

		if ($day < mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESTIMEINTHEPAST')));
			die;
		}
		
		if (VikAppointments::isClosingDay($day, $id_ser))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESCLOSINGDAY')));
			die;
		}

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'duration', 'sleep', 'interval', 'max_capacity', 'min_per_res', 'max_per_res', 'app_per_slot', 'display_seats', 'priceperpeople')))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . $id_ser);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo json_encode(array(0, JText::_('VAPFINDRESSERVICENOTEXISTS')));
			die;
		}
		
		$service = $dbo->loadAssoc();
		
		if ($service['max_capacity'] > 1 && ($people > $service['max_per_res'] || $people < $service['min_per_res']))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESPEOPLENOTVALID')));
			die;
		}
		
		$employees = VikAppointments::getEmployeesRelativeToService($id_ser);
		
		$default_tz = date_default_timezone_get();
		
		$emp_arr = array();

		$_d = getdate($day);

		foreach ($employees as $e)
		{
			if (empty($e['timezone']))
			{
				$e['timezone'] = $default_tz;
			}
			
			$worktime = VikAppointments::getEmployeeWorkingTimes($e['id'], $id_ser, $day);
			VikAppointments::setCurrentTimezone($e['timezone']);
			$bookings = VikAppointments::getAllEmployeeReservations( $e['id'], $id_ser, $day, $day + 86399, $dbo);

			$seats = array();
			
			if ($service['max_capacity'] == 1 || $service['app_per_slot'] == 0)
			{
				$t_data = array( 
					"id" 		=> $e['id'],
					"label" 	=> $e['nickname'],
					"timeline" 	=> VikAppointments::elaborateTimeLineServiceTimezone($worktime, $bookings, $service, $e['timezone']),
				);
			}
			else
			{
				$t_data = array( 
					"id" 		=> $e['id'],
					"label" 	=> $e['nickname'],
					"timeline" 	=> VikAppointments::elaborateTimeLineGroupServiceTimezone($worktime, $bookings, $service, $people, $e['timezone'], $seats),
				);
			}

			$arr = array($t_data['timeline']);

			$json  = array();
			$rates = array();

			for ($i = 0; $i < count($arr); $i++)
			{
				foreach ($arr[$i] as $k => $v)
				{
					if ($k >= 1440)
					{
						/**
						 * Unset the time slots that exceeds the midnight.
						 *
						 * @since 1.6
						 */
						unset($arr[$i][$k]);
					}
					else
					{
						$hour = floor($k / 60);
						$min  = $k % 60;

						$checkin = mktime($hour, $min, 0, $_d['mon'], $_d['mday'], $_d['year']);

						// calculate rate for the current time block
						$rates[$k] = VAPSpecialRates::getRate($id_ser, $e['id'], $checkin, $people);

						if ($service['priceperpeople'])
						{
							$rates[$k] *= $people;
						}
					}
				}

				if (count($arr[$i]))
				{
					$json[] = $arr[$i];
				}
			}

			// build array data for timeline layout
			$data = array(
				'id_service'  => $id_ser,
				'id_employee' => $e['id'],
				'checkinDay'  => $day,
				'duration' 	  => $service['duration'],
				'times'		  => $json,
				'rates'		  => $rates,
			);

			// switch timeline layout depending on the configuration of the system
			if ($service['display_seats'])
			{
				/**
				 * We are here as the service needs to display the remaining seats.
				 *
				 * @layout 	timeline/seats.php
				 */
				$layout = 'seats';

				// make $seats array available for layout
				$data['seats'] 		= $seats;
				$data['totalSeats'] = $service['max_capacity'];
			}
			else if (count(array_unique($rates)) > 1 || (float) reset($rates) != VAPSpecialRates::getBaseCost($id_ser, $e['id']))
			{
				/**
				 * We are here as a result of these conditions:
				 * - the rates list contains different prices
				 * - the first element (as well as any other element) is not equals to the base cost
				 *
				 * @layout 	timeline/ratesgrid.php
				 */
				$layout = 'ratesgrid';
			}
			else
			{
				/**
				 * Use the standard timeline layout.
				 *
				 * @layout 	timeline/default.php
				 */
				$layout = 'default';
			}

			/**
			 * The timeline HTML block is displayed from the layout below:
			 * /components/com_vikappointments/layouts/timeline/default.php
			 * 
			 * If you need to change something from this layout, just create
			 * an override of this layout by following the instructions below:
			 * - open the back-end of your Joomla
			 * - visit the Extensions > Templates > Templates page
			 * - edit the active template
			 * - access the "Create Overrides" tab
			 * - select Layouts > com_vikappointments > timeline
			 * - start editing all the files on your template to create your own layouts
			 *
			 * @since 1.6
			 */
			$base     = VAPBASE . DIRECTORY_SEPARATOR . 'layouts';
			$timeline = JLayoutHelper::render('timeline.' . $layout, $data, $base);

			$t_data['html'] = $timeline;
			$emp_arr[] 		= $t_data;
		}
		
		echo json_encode(array(1, $emp_arr));
		die;
	}
	
	function get_appointment_details()
	{
		$input = JFactory::getApplication()->input;

		$id_emp = $input->getUint('id_emp', 0);
		$day 	= $input->getUint('day', 0);
		$hour 	= $input->getUint('hour', 0);
		$min 	= $input->getUint('min', 0);
		
		$dt = getdate($day);
		VikAppointments::setCurrentTimezone(VikAppointments::getEmployeeTimezone($id_emp));
		$ts = mktime($hour, $min, 0, $dt['mon'], $dt['mday'], $dt['year']);
		
		$app = VikAppointments::getEmployeeAppointmentAt($id_emp, $ts);
		
		$res = array(0, JText::_('VAPEMPLOYEEAPPNOTFOUND'));

		if (count($app))
		{
			$res = array(1, $app);
		}
		
		echo json_encode($res);
		die;
	}
	
	function get_option_details()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$id_opt = $input->getUint('id_opt', 0);
		
		$option = array();

		$q = $dbo->getQuery(true)
			->select('`o`.*')
			->select(array(
				$dbo->qn('v.id', 'id_variation'),
				$dbo->qn('v.name', 'var_name'),
				$dbo->qn('v.inc_price', 'var_price'),
			))
			->from($dbo->qn('#__vikappointments_option', 'o'))
			->leftjoin($dbo->qn('#__vikappointments_option_value', 'v') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('v.id_option'))
			->where($dbo->qn('o.id') . ' = ' . $id_opt);
		
		$dbo->setQuery($q);
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			echo json_encode(array(0, JText::_('VAPNOOPTION')));
			die;
		}

		$option = $dbo->loadAssocList();
		$option[0]['variations'] = array();

		foreach ($option as $o)
		{
			if (!empty($o['id_variation']))
			{
				$option[0]['variations'][] = array(
					"id" 	=> $o['id_variation'],
					"name" 	=> $o['var_name'],
					"price" => $o['var_price'],
				);
			}
		}

		echo json_encode(array(1, $option[0]));
		die;
	}

	function get_day_reservations()
	{
		$input = JFactory::getApplication()->input;
		$user  = JFactory::getUser();

		$id_emp = $input->getUint('id_emp', 0);
		$day 	= $input->getUint('day', 0);
		$id_ser = $input->getUint('id_ser', 0);

		/**
		 * Fixed issue while accessing the back-end calendar
		 * with an employee that uses a custom timezone.
		 *
		 * @since 1.6.3
		 */
		$emp_tz = VikAppointments::getEmployeeTimezone($id_emp);
		VikAppointments::setCurrentTimezone($emp_tz);
		
		if (VikAppointments::isClosingDay($day, $id_ser))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESCLOSINGDAY')));
			die;
		}
		
		$worktime = VikAppointments::getEmployeeWorkingTimes($id_emp, $id_ser, $day);
		
		if (!count($worktime))
		{
			echo json_encode(array(0, JText::_('VAPFINDRESNODAYEMPLOYEE')));
			die;
		}
		
		$res = VikAppointments::getAllEmployeeExtendedReservations($id_emp, $id_ser, $day, $day + 86399);
		
		if (!count($res))
		{
			echo json_encode(array( 1, JText::_('VAPNORESERVATION') ));
			die;
		}

		$time_format = UIFactory::getConfig()->get('timeformat');

		$core_edit   = $user->authorise('core.edit', 'com_vikappointments');
		$core_delete = $user->authorise('core.delete', 'com_vikappointments'); 
		
		$vik = UIApplication::getInstance();
		
		$tab = '<table cellpadding="4" cellspacing="0" border="0" width="100%" class="' . $vik->getAdminTableClass() . '">';
		$tab .= $vik->openTableHead();
		$tab .=	'<tr>';
		$tab .=	'<th class="'.$vik->getAdminThClass('left').'" width="50" style="text-align: left;">'.JText::_('VAPMANAGERESERVATION0').'</th>';
		$tab .=	'<th class="'.$vik->getAdminThClass().'" width="100" style="text-align: center;">'.JText::_('VAPMANAGERESERVATION5').'</th>';
		$tab .=	'<th class="'.$vik->getAdminThClass().'" width="100" style="text-align: center;">'.JText::_('VAPMANAGERESERVATION6').'</th>';
		$tab .=	'<th class="'.$vik->getAdminThClass().'" width="100" style="text-align: center;">'.JText::_('VAPMANAGERESERVATION4').'</th>';
		$tab .=	'<th class="'.$vik->getAdminThClass().'" width="100" style="text-align: center;">'.JText::_('VAPMANAGERESERVATION25').'</th>';
		$tab .=	'<th class="'.$vik->getAdminThClass().'" width="100" style="text-align: center;">'.JText::_('VAPMANAGERESERVATION13').'</th>';
		$tab .=	'<th class="'.$vik->getAdminThClass().'" width="100" style="text-align: center;">'.JText::_('VAPMANAGERESERVATION9').'</th>';
		if ($core_edit)
		{
			$tab .=	'<th class="'.$vik->getAdminThClass().'" width="50" style="text-align: center;">'.JText::_('VAPMANAGERESERVATION22').'</th>';
		}
		if ($core_delete)
		{
			$tab .=	'<th class="'.$vik->getAdminThClass().'" width="50" style="text-align: center;">'.JText::_('VAPMANAGERESERVATION23').'</th>';
		}
		$tab .=	'</tr>';
		$tab .=	$vik->closeTableHead();
		
		$employee_tz = VikAppointments::getEmployeeTimezone($id_emp);
		VikAppointments::setCurrentTimezone($employee_tz);

		$closure_lbl = '<span class="vapreservationstatusclosure">' . JText::_('VAPSTATUSCLOSURE') . '</span>';

		foreach ($res as $i => $row)
		{	
			if (empty($row['pname']))
			{
				$row['pname'] = '/';
			}
			
			$tot_paid = '/';
			if ($row['total_cost'] > 0)
			{
				$tot_paid = VikAppointments::printPriceCurrencySymb($row['total_cost']);

				if ($row['tot_paid'] > 0)
				{
					$tot_paid .= ' (' . VikAppointments::printPriceCurrencySymb($row['tot_paid']) . ')';
				}
			}
			
			$tab .= '<tr class="row' . ($i % 2) . '" id="vaptabrow'.$row['rid'].'" data-id="'.$row['rid'].'">';
			$tab .= '<td>'.$row['rid'].'</td>';
			$tab .= '<td style="text-align: center;">'.date($time_format, $row['checkin']).'</td>';
			$tab .= '<td style="text-align: center;">'.date($time_format, VikAppointments::getCheckout($row['checkin'], $row['rduration'])).'</td>';
			$tab .= '<td style="text-align: center;">'.($row['closure'] ? $closure_lbl : $row['sname']).'</td>';
			$tab .= '<td style="text-align: center;">'.($row['closure'] ? '/' : $row['people']).'</td>';
			$tab .= '<td style="text-align: center;">'.$row['pname'].'</td>';
			$tab .= '<td style="text-align: center;">'.$tot_paid.'</td>';

			if ($core_edit)
			{
				if (!$row['closure'])
				{
					$tab .= '<td style="text-align: center;"><a href="javascript: void(0);" onclick="onEditReservation('.$row['rid'].');"><i class="fa fa-edit big"></i></a></td>';
				}
				else
				{
					$tab .= '<td style="text-align: center;">/</td>';
				}
			}

			if ($core_delete)
			{
				$tab .= '<td style="text-align: center;"><a href="javascript: void(0);" onclick="onDeleteReservation('.$row['rid'].');"><i class="fa fa-trash big"></i></a></td>';
			}

			$tab .= '</tr>';
		}		
		
		$tab .= '</table>';
		
		echo json_encode(array(1, $tab));
		die;
	}

	function get_reservation_at()
	{
		$input = JFactory::getApplication()->input;

		$id_emp = $input->getUint('id_emp', 0);
		$day 	= $input->getUint('day', 0);
		$hour 	= $input->getUint('hour', 0);
		$min 	= $input->getUint('min', 0);
		
		$date = getdate($day);
		$ts   = mktime($hour, $min, 0, $date['mon'], $date['mday'], $date['year']);
		
		$app = VikAppointments::getEmployeeAppointmentAt($id_emp, $ts);
		
		$count = count($app);

		if ($count == 1)
		{
			$res = array(1, (int) $app[0]['rid']);
		}
		else if ($count > 1)
		{
			$ids = array_map(function($elem)
			{
				return (int) $elem['rid'];
			}, $app);

			$res = array(1, $ids);
		}
		else
		{
			$res = array(0, JText::_('VAPEMPLOYEEAPPNOTFOUND'));
		}
		
		echo json_encode($res);
		die;
	}

	function get_reservations_det_at()
	{
		$dbo   = JFactory::getDbo();
		$input = JFactory::getApplication()->input;

		$id_emp = $input->getUint('id_emp', 0);
		$date 	= $input->getString('date', '');
		$day 	= $input->getUint('day', 0);
		$hour 	= $input->getUint('hour', 0);
		$min 	= $input->getUint('min', 0);
		
		if ($date)
		{
			// get timestamp from string
			$ts = VikAppointments::createTimestamp($date, $hour, $min);
		}
		else
		{
			// get date from timestamp
			$date = getdate($day);
			$ts   = mktime($hour, $min, 0, $date['mon'], $date['mday'], $date['year']);
		}

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('r.id'),
				$dbo->qn('r.purchaser_nominative', 'name'),
				$dbo->qn('r.people', 'people'),
				$dbo->qn('r.id_service'),
				$dbo->qn('s.name', 'service_name'),
			))
			->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('r.id_service'));
		
		$app = VikAppointments::getEmployeeAppointmentAt($id_emp, $ts, $dbo, $q);
		
		echo json_encode($app);
		die;
	}
	
	function save_is_stat()
	{
		$input 	 = JFactory::getApplication()->input;
		$is_stat = $input->getUint('is_stat', 0);

		UIFactory::getConfig()->set('is_stat', $is_stat);
		die;
	}
	
	function get_payment_fields()
	{
		$input 	= JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();
		
		$gpn 	= $input->getString('gpn');
		$id_gp 	= $input->getInt('id_gp', 0);
		
		/**
		 * Access payment config through platform handler.
		 *
		 * @since 1.6.3
		 */
		$form = UIApplication::getInstance()->getPaymentConfig($gpn);
		
		$params = array();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_gpayments'))
			->where($dbo->qn('id') . ' = ' . $id_gp);
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$payment = $dbo->loadAssoc();

			if (!empty($payment['params']))
			{
				$params = json_decode($payment['params'], true);
			}
		}
		
		$html = $this->buildPaymentForm($form, $params);
		
		echo json_encode(array($html));
		die;
	}

	private function buildPaymentForm($fields, $params)
	{
		$html = '';

		$vik = UIApplication::getInstance();

		$hasPassword = false;
		
		foreach ($fields as $key => $f)
		{
			$def_val = '';

			if (!empty($params[$key]))
			{
				$def_val = $params[$key];
			}
			else if (!empty($f['default']))
			{
				$def_val = $f['default'];
			}
			
			$_label_arr = explode('//', $f['label']);
			$label 		= str_replace(':', '', $_label_arr[0]);

			$title = $label;

			if (!empty($label))
			{
				$label .= (!empty($f['required']) ? '*' : '').':';
			}

			unset($_label_arr[0]);
			$helplabel = implode('//', $_label_arr);

			$row = $vik->openControl($label);
			
			$input = '';

			if ($f['type'] == 'text')
			{
				$input = '<input type="text" class="'.(!empty($f['required']) ? 'required' : '').'" value="'.$def_val.'" name="'.$key.'" size="40" />';	
			}
			else if ($f['type'] == 'password')
			{
				$input = '<input type="password" class="'.(!empty($f['required']) ? 'required' : '').'" value="'.$def_val.'" name="'.$key.'" size="40" />';

				$input .= '<a href="javascript: void(0);" class="input-align" onclick="switchPasswordField(this);"><i class="fa fa-lock big" style="margin-left: 10px;"></i></a>';

				$hasPassword = true;
			}
			else if ($f['type'] == 'select')
			{
				$is_assoc = (array_keys($f['options']) !== range(0, count($f['options']) - 1));

				$input = '<select name="'.$key.(!empty($f['multiple']) ? '[]' : '').'" class="'.(!empty($f['required']) ? 'required' : '').'" '.(!empty($f['multiple']) ? 'multiple' : '').'>';
				
				foreach ($f['options'] as $opt_key => $opt_val)
				{
					if (!$is_assoc)
					{
						$opt_key = $opt_val;
					}

					$input .= '<option value="'.$opt_key.'" '.( (is_array($def_val) && in_array($opt_key, $def_val)) || $opt_key == $def_val ? 'selected="selected"' : '').'>'.$opt_val.'</option>';
				}
				$input .= '</select>';
			}
			else
			{
				$input = $f['html']; 
			}
			
			$row .= $input;
			
			if ($helplabel)
			{
				$row .= $vik->createPopover(array(
					'title' 	=> $title,
					'content' 	=> strtoupper($helplabel[0]) . substr($helplabel, 1),
				));
			}
			
			$row .= $vik->closeControl();

			$html .= $row;
		};
		
		if (empty($html))
		{
			$html = '<div class="vappaymentparam">'.JText::_('VAPMANAGEPAYMENT9').'</div>';
		}
		else if ($hasPassword)
		{
			$html .= "<script>
			function switchPasswordField(link) {
				
				if (jQuery(link).prev().is(':password'))
				{
					jQuery(link).prev().attr('type', 'text');
					jQuery(link).find('i.fa').removeClass('fa-lock').addClass('fa-unlock');
				}
				else
				{
					jQuery(link).prev().attr('type', 'password');
					jQuery(link).find('i.fa').removeClass('fa-unlock').addClass('fa-lock');
				}

			}
			</script>";
		}

		return $html;
	}
	
	function get_cron_fields()
	{
		$dbo 	= JFactory::getDbo();
		$input  = JFactory::getApplication()->input;

		VikAppointments::loadCronLibrary();
		
		$class 	= $input->getString('cron');
		$id 	= $input->getInt('id', -1);
		
		$cron_config = CronDispatcher::getJobConfiguration($class);

		if ($cron_config === null)
		{
			echo json_encode(array(0, JText::_('VAPCRONJOBERROR1')));
			exit;
		}
		else if (count($cron_config) == 0)
		{
			echo json_encode(array(1, JText::_('VAPMANAGECRONJOB7')));
			exit;
		}
		
		$params = array();
		if ($id > 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('params'))
				->from($dbo->qn('#__vikappointments_cronjob'))
				->where($dbo->qn('id') . ' = ' . $id);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$params = json_decode($dbo->loadResult(), true);
			}
		}
		
		$builder 	= new VikAppointmentsCronFormBuilder($cron_config);
		$built_html = $builder->build($params);
		
		echo json_encode(array(1, $built_html));
		exit;
	}
	
	function store_tab_selected()
	{
		$input = JFactory::getApplication()->input;

		$tab 	= $input->getUint('tab', 1);
		$group 	= $input->getString('group');
		
		JFactory::getSession()->set('vaptabactive', $tab, $group);
		die;
	}
	
	function validate_zip_code()
	{
		$input 		= JFactory::getApplication()->input;
		$zip_code 	= $input->getString('zipcode');
		
		$_resp = VikAppointments::validateZipCode($zip_code, array(-1), true);
		
		echo json_encode(array($_resp));
		die;
	}
	
	function get_states_with_country()
	{
		$input = JFactory::getApplication()->input;

		$id_country = $input->getUint('id_country', 0);
		$states 	= VikAppointmentsLocations::getStates($id_country, 'state_name');

		echo json_encode($states);
		exit;	
	}
	
	function get_cities_with_state()
	{
		$input = JFactory::getApplication()->input;

		$id_state 	= $input->getUint('id_state', 0);
		$cities 	= VikAppointmentsLocations::getCities($id_state, 'city_name');

		echo json_encode($cities);
		exit;
	}
	
	function search_users()
	{	
		$dbo 	= JFactory::getDbo();
		$input 	= JFactory::getApplication()->input;
		
		$search = $input->getString('term', '');
		$id 	= $input->getInt('id', null);

		$rows = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'billing_name', 'billing_mail', 'billing_phone', 'country_code', 'fields')))
			->from($dbo->qn('#__vikappointments_users'));

		if (!is_null($id))
		{
			$q->where($dbo->qn('id') . ' = ' . $id);
		}
		else
		{
			$q->where($dbo->qn('billing_name') . ' LIKE ' . $dbo->q("%$search%"));
		}

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			foreach ($dbo->loadObjectList() as $row)
			{
				$row->fields = json_decode($row->fields);
				$rows[] = $row;
			}
		}

		// check if we are searching for a single record
		if (!is_null($id))
		{
			// on success, get only the first element
			if (count($rows))
			{
				$rows = $rows[0];
			}
			// on failure, get an empty object
			else
			{
				$rows = new stdClass;
			}
		}
		
		echo json_encode($rows);
		exit;
	}

	function search_jusers()
	{	
		$dbo 	= JFactory::getDbo();
		$input 	= JFactory::getApplication()->input;
		
		$search = $input->getString('term', '');
		$id 	= $input->getInt('id', null);

		$rows = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'username', 'name', 'email')))
			->from($dbo->qn('#__users'));

		if (!is_null($id))
		{
			$q->where($dbo->qn('id') . ' = ' . $id);
		}
		else
		{
			$q->where(array(
				$dbo->qn('username') . ' LIKE ' . $dbo->q("%$search%"),
				$dbo->qn('name') . ' LIKE ' . $dbo->q("%$search%"),
				$dbo->qn('email') . ' LIKE ' . $dbo->q("%$search%"),
			), 'OR');
		}

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadObjectList();
		}

		// check if we are searching for a single record
		if (!is_null($id))
		{
			// on success, get only the first element
			if (count($rows))
			{
				$rows = $rows[0];
			}
			// on failure, get an empty object
			else
			{
				$rows = new stdClass;
			}
		}
		
		echo json_encode($rows);
		exit;
	}

	function search_jusers_customers()
	{	
		$dbo 	= JFactory::getDbo();
		$input 	= JFactory::getApplication()->input;
		
		$search = $input->getString('term', '');
		$id 	= $input->getInt('id', null);

		$rows = array();

		$jusers = array();

		$exists = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_users', 'a'))
			->where($dbo->qn('a.jid') . ' = ' . $dbo->qn('u.id'));

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('u.id', 'u.name', 'u.username', 'u.email')))
			->select('(' . $dbo->qn('u.id') . ' <> ' . $id . ') AND EXISTS (' . $exists . ') AS ' . $dbo->qn('disabled'))
			->from($dbo->qn('#__users', 'u'))
			->where(array(
				$dbo->qn('u.username') . ' LIKE ' . $dbo->q("%$search%"),
				$dbo->qn('u.name') . ' LIKE ' . $dbo->q("%$search%"),
				$dbo->qn('u.email') . ' LIKE ' . $dbo->q("%$search%"),
			), 'OR')
			->order($dbo->qn('u.username') . ' ASC');
			

		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadObjectList();
		}
		
		echo json_encode($rows);
		exit;
	}

	/**
	 * AJAX end-point to obtain all the employees assigned to the given service.
	 * 
	 * @param 	integer  id_ser  The service ID.
	 * @param 	boolean  all 	 True to return all the employees.
	 *							 False to obtain only the employees listed in the front-end.
	 */
	function get_services_employees()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$id_ser = $input->getUint('id_ser', 0);
		$all 	= $input->getBool('all');

		$employees = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('e.id', 'e.nickname', 'a.rate')))
			->from($dbo->qn('#__vikappointments_employee', 'e'))
			->leftjoin($dbo->qn('#__vikappointments_ser_emp_assoc', 'a') . ' ON ' . $dbo->qn('a.id_employee') . ' = ' . $dbo->qn('e.id'))
			->where($dbo->qn('a.id_service') . ' = ' . $id_ser)
			->order($dbo->qn('e.nickname') . ' ASC');			

		if (!$all)
		{
			$q->where($dbo->qn('listable') . ' = 1');
		}

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$employees = $dbo->loadAssocList();
		}

		echo json_encode($employees);
		exit;
	}

	/**
	 * AJAX end-point used to test how the special rates are applied.
	 *
	 * @param 	integer  id_service  	The service ID.
	 * @param 	integer  id_employee 	The employee ID (optional).
	 * @param 	string 	 checkin 		The checkin date and time.
	 * @param 	integer  people 		The number of people (optional).
	 */
	function test_special_rates()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$id_service  = $input->getUint('id_service', 0);
		$id_employee = $input->getUint('id_employee', 0);
		$usergroup 	 = $input->getUint('usergroup', 0);
		$checkin	 = $input->getString('checkin');
		$people		 = $input->getUint('people', 1);
		$is_debug 	 = $input->getBool('debug', 0);

		// store the last search in the user state
		$app->setUserState('ratestest.id_service', $id_service);
		$app->setUserState('ratestest.id_employee', $id_employee);
		$app->setUserState('ratestest.usergroup', $usergroup);
		$app->setUserState('ratestest.checkin', $checkin);
		$app->setUserState('ratestest.people', $people);
		$app->setUserState('ratestest.debug', $is_debug);

		// create checkin timestamp
		list($date, $time) = explode(' ', $checkin);
		list($hour, $min)  = explode(':', $time);

		$ts = VikAppointments::createTimestamp($date, (int) $hour, (int) $min);

		// var used to trace the rates calculation
		$trace = array();

		if ($usergroup)
		{
			// inject property to force usergroup
			$trace['usergroup'] = $usergroup;
		}

		if ($is_debug)
		{
			// inject property to force debugging
			$trace['debug'] = array();
		}

		// calculate rate
		$rate = VAPSpecialRates::getRate($id_service, $id_employee, $ts, $people, $trace);

		echo json_encode(array($rate, $trace));
		exit;
	}

	/**
	 * AJAX end-point used to change the thumb color of the given service.
	 * 
	 * @param 	integer  id  	The service ID.
	 * @param 	string   color 	The hex color.
	 */
	function change_service_color()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$id  	= $input->getUint('id', 0);
		$color 	= $input->getString('color', '');

		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_service'))
			->set($dbo->qn('color') . ' = ' . $dbo->q($color))
			->where($dbo->qn('id') . ' = ' . $id);

		$dbo->setQuery($q);
		$dbo->execute();
	}
	
	/**
	 * REMOVE FUNCTIONS
	 */

	/**
	 * Handle the records to delete.
	 *
	 * @param 	array 	 $ids 	 The array of IDs to remove.
	 * @param 	string   $table  The DB table name.
	 * @param 	mixed    $pk 	 The name of the Primary Key or an
	 * 							 associative array for AND clauses.
	 *
	 * @return 	integer  The number of affected rows.
	 *
	 * @since 	1.6
	 */
	private function handleDelete($ids, $table, $pk = 'id')
	{
		if (!count($ids) && is_scalar($pk))
		{
			return 0;
		}

		if (is_scalar($pk))
		{
			$pk = array($pk => $ids);
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->delete($dbo->qn($table));

		foreach ($pk as $k => $v)
		{
			if (is_array($v))
			{
				$v = array_map(array($dbo, 'q'), $v);

				$q->where($dbo->qn($k) . ' IN (' . implode(', ', $v) . ')');
			}
			else if (!is_null($v))
			{
				$q->where($dbo->qn($k) . ' = ' . $dbo->q($v));
			}
		}

		$dbo->setQuery($q);
		$dbo->execute();

		return $dbo->getAffectedRows();
	}
	
	function deleteReservations()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$ids = $input->getUint('cid', array());
		
		if (count($ids))
		{
			// get the selected reservations and related children
			$list = array();

			foreach ($ids as $id)
			{
				$q = $dbo->getQuery(true)
					->select($dbo->qn('id'))
					->from($dbo->qn('#__vikappointments_reservation'))
					->where(array(
						$dbo->qn('id') . ' = ' . $id,
						$dbo->qn('id_parent') . ' = ' . $id,
					), 'OR');

				$dbo->setQuery($q);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$list = array_merge($list, $dbo->loadColumn());
				}
			}

			// remove reservations
			$this->handleDelete($list, '#__vikappointments_reservation', 'id');
			// remove reservations options
			$this->handleDelete($list, '#__vikappointments_res_opt_assoc', 'id_reservation');
			// remove order statuses
			$this->handleDelete(null, '#__vikappointments_order_status', array('id_order' => $list, 'type' => 'reservation'));
		}
		
		$from = $input->getString('from', 'reservations');

		$app->redirect("index.php?option=com_vikappointments&task={$from}");
	}

	function deleteWaitingRows()
	{	
		$input = JFactory::getApplication()->input;

		$ids = $input->getUint('cid', array());
		
		$this->handleDelete($ids, '#__vikappointments_waitinglist', 'id');

		$this->cancelWaitinglist();
	}
	
	function deleteCustomers()
	{	
		$input 	= JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();
		
		$ids = $input->getUint('cid', array());
		
		// delete customers
		$this->handleDelete($ids, '#__vikappointments_users', 'id');

		if (count($ids))
		{
			// unset customers assigned to the reservations
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_reservation'))
				->set($dbo->qn('id_user') . ' = -1')
				->where($dbo->qn('id_user') . ' IN (' . implode(',', $ids) . ')');

			$dbo->setQuery($q);
			$dbo->execute();

			// unset customers assigned to the package orders
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_package_order'))
				->set($dbo->qn('id_user') . ' = -1')
				->where($dbo->qn('id_user') . ' IN (' . implode(',', $ids) . ')');

			$dbo->setQuery($q);
			$dbo->execute();
		}
		
		$this->cancelCustomers();
	}
	
	function deleteGroups()
	{	
		$input 	= JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();
		
		$ids = $input->getUint('cid', array());

		// delete grups
		$this->handleDelete($ids, '#__vikappointments_group', 'id');
		// delete language groups
		$this->handleDelete($ids, '#__vikappointments_lang_group', 'id_group');

		// unset group from children services
		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_service'))
			->set($dbo->qn('id_group') . ' = -1')
			->where($dbo->qn('id_group') . ' IN (' . implode(',', $ids) . ')');

		$dbo->setQuery($q);
		$dbo->execute();
		
		$this->cancelGroup();
	}
	
	function deleteEmployeeGroups()
	{	
		$input 	= JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();
		
		$ids = $input->getUint('cid', array());

		// delete grups
		$this->handleDelete($ids, '#__vikappointments_employee_group', 'id');
		// delete groups language
		$this->handleDelete($ids, '#__vikappointments_lang_empgroup', 'id_empgroup');

		// unset group from children services
		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_employee'))
			->set($dbo->qn('id_group') . ' = -1')
			->where($dbo->qn('id_group') . ' IN (' . implode(',', $ids) . ')');

		$dbo->setQuery($q);
		$dbo->execute();
		
		$this->cancelGroup();
	}
	
	function deleteServices()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());
		
		// delete services
		$this->handleDelete($ids, '#__vikappointments_service', 'id');
		// delete services-employees assignments
		$this->handleDelete($ids, '#__vikappointments_ser_emp_assoc', 'id_service');
		// delete services-options assignments
		$this->handleDelete($ids, '#__vikappointments_ser_opt_assoc', 'id_service');
		// delete services working days
		$this->handleDelete($ids, '#__vikappointments_emp_worktime', 'id_service');
		// delete services special rates 
		$this->handleDelete($ids, '#__vikappointments_ser_rates_assoc', 'id_service');
		// delete services language 
		$this->handleDelete($ids, '#__vikappointments_lang_service', 'id_service');
		// delete service custom fields
		$this->handleDelete($ids, '#__vikappointments_cf_service_assoc', 'id_service');
		
		$this->cancelService();
	}
	
	function deleteServiceWorkTimes()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$ids 	= $input->getUint('cid', array());
		$id_ser = $input->getUint('id');
		$id_emp = $input->getUint('id_emp');

		$this->handleDelete($ids, '#__vikappointments_emp_worktime', 'id');
		
		$app->redirect("index.php?option=com_vikappointments&task=serworkdays&id={$id_ser}&id_emp={$id_emp}");
	}

	function deleteSpecialRates()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());
		
		// delete special rates
		$this->handleDelete($ids, '#__vikappointments_special_rates', 'id');
		// delete rate relations 
		$this->handleDelete($ids, '#__vikappointments_ser_rates_assoc', 'id_special_rate');
		
		$this->cancelSpecialRate();
	}
	
	function deleteEmployees()
	{	
		$input = JFactory::getApplication()->input;

		$ids = $input->getUint('cid', array());

		// delete employees
		$this->handleDelete($ids, '#__vikappointments_employee', 'id');
		// delete employees services assoc
		$this->handleDelete($ids, '#__vikappointments_ser_emp_assoc', 'id_employee');
		// delete employees working days
		$this->handleDelete($ids, '#__vikappointments_emp_worktime', 'id_employee');
		// delete employees locations
		$this->handleDelete($ids, '#__vikappointments_employee_location', 'id_employee');
		// delete employees payments
		$this->handleDelete($ids, '#__vikappointments_gpayments', 'id_employee');
		// delete employees language
		$this->handleDelete($ids, '#__vikappointments_lang_employee', 'id_employee');
		// delete employees custom fields
		$this->handleDelete($ids, '#__vikappointments_custfields', 'id_employee');
		// delete employees settings
		$this->handleDelete($ids, '#__vikappointments_employee_settings', 'id_employee');
		
		$this->cancelEmployee();
	}
	
	function deleteOptions()
	{	
		$input = JFactory::getApplication()->input;

		$ids = $input->getUint('cid', array());

		// delete options
		$this->handleDelete($ids, '#__vikappointments_option', 'id');
		// delete services-options assignments
		$this->handleDelete($ids, '#__vikappointments_ser_opt_assoc', 'id_option');
		// delete options variations
		$this->handleDelete($ids, '#__vikappointments_option_value', 'id_option');
		// delete options languages
		$this->handleDelete($ids, '#__vikappointments_lang_option', 'id_option');
		
		$this->cancelOption();
	}

	function deleteOneMediaAjax($image = null)
	{
		// do not die if the image has been passed has argument
		$die = false;

		if (is_null($image))
		{
			// no provided image, get it from the request
			$image = JFactory::getApplication()->input->getString('image', '');
			// the flow can die because this is an AJAX request
			$die   = true;
		}
		
		if (is_file(VAPMEDIA . DIRECTORY_SEPARATOR . $image))
		{
			unlink(VAPMEDIA . DIRECTORY_SEPARATOR . $image);
			unlink(VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $image);
		}
		
		if ($die)
		{
			exit;
		}
	}
	
	function deleteMedia()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getString('cid', array());
		
		foreach ($ids as $image)
		{
			$this->deleteOneMediaAjax($image);
		}
		
		$this->cancelMedia();
	}

	function deleteImportedFilesAjax($type = null)
	{
		// do not die if the image has been passed has argument
		$die = false;

		if (is_null($type))
		{
			// no provided file, get it from the request
			$type = JFactory::getApplication()->input->getString('import_type');
			// the flow can die because this is an AJAX request
			$die   = true;
		}

		$folder = VAPADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;

		// get all files that starts with the provided type: [TYPE]_*.csv
		$files = glob($folder . $type . '_*.csv');

		foreach ($files as $file)
		{
			unlink($file);
		}

		if ($die)
		{
			exit;
		}
	}

	function deleteImportedFiles()
	{
		$type = JFactory::getApplication()->input->getString('import_type');
		
		$this->deleteImportedFilesAjax($type);

		$this->cancelImport();
	}
	
	function deleteInvoices()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$ids 	= $input->getString('cid', array());
		$year 	= $input->getInt('year');
		$month 	= $input->getInt('month');

		$search = $input->getString('keysearch');
		$group 	= $input->getString('group');

		$parts = array(
			VAPINVOICE,
		);

		if ($group)
		{
			$parts[] = $group;
		}

		$base = implode(DIRECTORY_SEPARATOR, $parts);
		
		foreach ($ids as $invoice)
		{
			unlink($base . DIRECTORY_SEPARATOR . $invoice);
		}
		
		$app->redirect("index.php?option=com_vikappointments&task=invfiles&year={$year}&month={$month}&group{$group}&keysearch={$search}");
	}
	
	function deleteCoupons()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());
		
		// delete coupons
		$this->handleDelete($ids, '#__vikappointments_coupon', 'id');
		// delete coupons-services assignments
		$this->handleDelete($ids, '#__vikappointments_coupon_service_assoc', 'id_coupon');
		// delete coupons-employees assignments
		$this->handleDelete($ids, '#__vikappointments_coupon_employee_assoc', 'id_coupon');
		
		$this->cancelCoupon();
	}

	function deleteCouponGroups()
	{	
		$input  = JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();
		
		$ids = $input->getUint('cid', array());
		
		// delete coupon groups
		$this->handleDelete($ids, '#__vikappointments_coupon_group', 'id');
		
		if (count($ids))
		{
			// detach coupons from deleted groups
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikappointments_coupon'))
				->set($dbo->qn('id_group') . ' = 0')
				->where($dbo->qn('id_group') . ' IN (' . implode(', ', $ids) . ')');

			$dbo->setQuery($q);
			$dbo->execute();
		}
		
		$this->cancelCouponGroup();
	}
	
	function deletePayments()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());
		
		// delete payments
		$this->handleDelete($ids, '#__vikappointments_gpayments', 'id');
		// delete payments language
		$this->handleDelete($ids, '#__vikappointments_lang_payment', 'id_payment');
		
		$this->cancelPayment();
	}
	
	function deleteEmployeePayments()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$ids = $input->getUint('cid', array());
		$emp = $input->getUint('id_emp', 0);

		// $this->handleDelete($ids, '#__vikappointments_gpayments', 'id');
		$this->handleDelete(null, '#__vikappointments_gpayments', array('id' => $ids, 'id_employee' => $emp));
		
		$this->cancelEmployeePayment();
	}

	function deleteLocations()
	{
		$this->deleteEmployeeLocations('locations');
	}
	
	function deleteEmployeeLocations($from = 'emplocations')
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$ids = $input->getUint('cid', array());
		$emp = $input->getUint('id_emp');

		// delete employee locations
		$this->handleDelete($ids, '#__vikappointments_employee_location', 'id');
		
		// unset deleted locations from working days
		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_emp_worktime'))
			->set($dbo->qn('id_location') . ' = -1')
			->where($dbo->qn('id_location') . ' IN (' . implode(',', $ids) . ')');
				
		$dbo->setQuery($q);
		$dbo->execute();
		
		if ($from == 'locations')
		{
			$this->cancelLocation();
		}
		else
		{
			$app->redirect("index.php?option=com_vikappointments&task=emplocations&id_emp={$emp}");
		}
	}

	function deletePackGroups()
	{	
		$input 	= JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();

		$ids = $input->getUint('cid', array());
		
		// delete packages groups
		$this->handleDelete($ids, '#__vikappointments_package_group', 'id');

		// unset deleted groups from packages
		$q = $dbo->getQuery(true)
			->update($dbo->qn('#__vikappointments_package'))
			->set($dbo->qn('id_group') . ' = -1')
			->where($dbo->qn('id_group') . ' IN (' . implode(',', $ids) . ')');
				
		$dbo->setQuery($q);
		$dbo->execute();

		// delete custom fields languages
		$this->handleDelete($ids, '#__vikappointments_lang_package_group', 'id_package_group');
		
		$this->cancelPackageGroup();
	}

	function deletePackages()
	{	
		$input = JFactory::getApplication()->input;

		$ids = $input->getUint('cid', array());

		$this->handleDelete($ids, '#__vikappointments_package', 'id');

		// DO NOT remove package-service assoc.
		// The associations are required for the orders already stored,
		// otherwise customers wouldn't be able to select the proper services in the front-end.

		// delete custom fields languages
		$this->handleDelete($ids, '#__vikappointments_lang_package', 'id_package');
		
		$this->cancelPackage();
	}

	function deletePackOrders()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());
		
		// delete packages orders
		$this->handleDelete($ids, '#__vikappointments_package_order', 'id');
		// delete packages orders items
		$this->handleDelete($ids, '#__vikappointments_package_order_item', 'id_order');
		
		$this->cancelPackageOrder();
	}
	
	function deleteCustomf()
	{
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());

		if (count($ids))
		{
			// remove the employees custom fields from the table
			$this->dropEmployeesCustomFields($ids);
		}
		
		// delete custom fields
		$this->handleDelete($ids, '#__vikappointments_custfields', 'id');
		// delete custom fields languages
		$this->handleDelete($ids, '#__vikappointments_lang_customf', 'id_customf');
		// delete custom fields services assoc
		$this->handleDelete($ids, '#__vikappointments_cf_service_assoc', 'id_field');
	
		$this->cancelCustomf();
	}
	
	function deleteCustMail()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());

		$this->handleDelete($ids, '#__vikappointments_cust_mail', 'id');
		
		$this->cancelCustmail();
	}

	function deleteConversions()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());

		$this->handleDelete($ids, '#__vikappointments_conversion', 'id');
		
		$this->cancelConversion();
	}
	
	function deleteCountries()
	{	
		$input  = JFactory::getApplication()->input;
		$dbo 	= JFactory::getDbo();
		
		$ids = $input->getUint('cid', array());

		// delete countries
		$this->handleDelete($ids, '#__vikappointments_countries', 'id');
		
		if (count($ids))
		{
			// get states children
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id'))
				->from($dbo->qn('#__vikappointments_states'))
				->where($dbo->qn('id_country') . ' IN (' . implode(',', $ids) . ')');
					
			$dbo->setQuery($q);
			$dbo->execute();
			
			if ($dbo->getNumRows())
			{
				$states = $dbo->loadColumn();

				// delete states
				$this->handleDelete($states, '#__vikappointments_states', 'id');
				// delete cities
				$this->handleDelete($states, '#__vikappointments_cities', 'id_state');
			}
		}
		
		$this->cancelCountry();
	}
	
	function deleteStates()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());
		
		// delete states
		$this->handleDelete($ids, '#__vikappointments_states', 'id');
		// delete cities
		$this->handleDelete($ids, '#__vikappointments_cities', 'id_state');
		
		$this->cancelState();
	}
	
	function deleteCities()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());
		
		$this->handleDelete($ids, '#__vikappointments_cities', 'id');
		
		$this->cancelCity();
	}
	
	function deleteReviews()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());

		$this->handleDelete($ids, '#__vikappointments_reviews', 'id');
		
		$this->cancelReview();
	}
	
	function deleteSubscriptions()
	{	
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());

		// delete subscriptions
		$this->handleDelete($ids, '#__vikappointments_subscription', 'id');
		// delete subscription languages
		$this->handleDelete($ids, '#__vikappointments_lang_subscr', 'id_subscr');
		
		$this->cancelSubscription();
	}
	
	function deleteSubscriptionOrders() {
		
		$input = JFactory::getApplication()->input;
		
		$ids = $input->getUint('cid', array());

		$this->handleDelete($ids, '#__vikappointments_subscr_order', 'id');
		
		$this->cancelSubscriptionOrder();
	}

	function deleteCronjobs()
	{
		$app 	= JFactory::getApplication();
		$input  = $app->input;
		$dbo 	= JFactory::getDbo();
		
		$ids = $input->getUint('cid', array());

		VikAppointments::loadCronLibrary();

		if (count($ids))
		{
			// get cron jobs classes

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('id', 'class')))
				->from($dbo->qn('#__vikappointments_cronjob'))
				->where($dbo->qn('id') . ' IN (' . implode(',', $ids) . ')');

			$dbo->setQuery($q);
			$dbo->execute();
			
			foreach ($dbo->loadObjectList() as $obj)
			{
				$job = CronDispatcher::getJob($obj->class, $obj->id);
				
				if ($job->uninstall())
				{
					$app->enqueueMessage(JText::sprintf('VAPCRONJOBUNINSTALLED1', $obj->class));
				}
				else
				{
					$app->enqueueMessage(JText::sprintf('VAPCRONJOBUNINSTALLED0', $obj->class), 'error');
				}
			}

			// delete cron jobs
			$this->handleDelete($ids, '#__vikappointments_cronjob', 'id');
			// delete cron jobs logs
			$this->handleDelete($ids, '#__vikappointments_cronjob_log', 'id_cronjob');
		}
		
		$this->cancelCronjob();
	}

	function deleteCronjoblogs()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		$ids 	= $input->getUint('cid', array());
		$cron 	= $input->getInt('id_cron');
		
		$this->handleDelete($ids, '#__vikappointments_cronjob_log', 'id');
		
		$app->redirect("index.php?option=com_vikappointments&task=cronjoblogs&id_cron={$cron}");
	}
	
	/**
	 * CANCEL TASKS
	 */
	
	function dashboard()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments');
	}
	
	function cancelCalendar()
	{
		$app = JFactory::getApplication();

		$services = $app->input->get('services', array(), 'uint');

		if ($services)
		{
			$services = implode('', array_map(function($s)
			{
				return '&services[]=' . $s;
			}, $services));
		}
		else
		{
			$services = '';
		}

		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=calendar' . $services);
	}
	
	function cancelReservation()
	{
		$app = JFactory::getApplication();

		$id = $app->input->getInt('id_res', 0);

		if (empty($id_res) || $id_res == -1)
		{
			$app->redirect("index.php?option=com_vikappointments&task=reservations");
		}
		else
		{
			$app->redirect("index.php?option=com_vikappointments&task=editreservation&cid[]={$id}");
		}
	}

	function cancelWaitinglist()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=waitinglist');
	}
	
	function cancelCustomers()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=customers');
	}
	
	function cancelGroup()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=groups');
	}
	
	function cancelService()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=services');
	}

	function cancelSpecialRate()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=rates');
	}
	
	function cancelEmployee()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=employees');
	}
	
	function cancelEmployeeLocation()
	{
		$app = JFactory::getApplication();

		$id = $app->input->getInt('id_employee');

		$app->redirect("index.php?option=com_vikappointments&task=emplocations&id_emp={$id}");
	}
	
	function cancelOption()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=options');
	}

	function cancelLocation()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=locations');
	}

	function cancelPackage()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=packages');
	}

	function cancelPackageGroup()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=packgroups');
	}

	function cancelPackageOrder()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=packorders');
	}
	
	function cancelMedia()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=media');
	}

	function cancelImport()
	{
		$app = JFactory::getApplication();

		$type = $app->input->get('import_type', '', 'string');
		$args = $app->input->get('import_args', array(), 'array');

		$query = '';
		foreach ($args as $k => $v)
		{
			$query .= "&import_args[{$k}]={$v}";
		}

		$app->redirect('index.php?option=com_vikappointments&task=import&import_type=' . $type . $query);
	}
	
	function cancelCoupon()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=coupons');
	}

	function cancelCouponGroup()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=coupongroups');
	}
	
	function cancelPayment()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=payments');
	}

	function cancelEmployeePayment()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		// Since this page can be invoked by manageemployee (on cancel) and by emppayments (on delete),
		// we need to get the ID of the employee from 2 possible keys: 'id_employee' and 'id_emp'.
		$id_emp = $input->getUint('id_emp', 0) | $app->input->getUint('id_employee', 0);

		$app->redirect('index.php?option=com_vikappointments&task=emppayments&id_emp=' . $id_emp);
	}
	
	function cancelCustomf()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=customf');
	}
	
	function cancelCustmail()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=mailtextcust');
	}

	function cancelConversion()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=conversions');
	}

	function cancelClosure()
	{
		$app = JFactory::getApplication();

		$app->redirect('index.php?option=com_vikappointments&task=' . $app->input->get('from', 'employees'));
	}
	
	function cancelCountry()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=countries');
	}
	
	function cancelState()
	{
		$app = JFactory::getApplication();

		$country = $app->input->getInt('country');

		$app->redirect("index.php?option=com_vikappointments&task=states&country={$country}");
	}
	
	function cancelCity()
	{
		$app = JFactory::getApplication();

		$country = $app->input->getInt('country');
		$state 	 = $app->input->getInt('state');

		$app->redirect("index.php?option=com_vikappointments&task=cities&country={$country}&state={$state}");
	}
	
	function cancelReview()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=revs');
	}
	
	function cancelSubscription()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=subscriptions');
	}
	
	function cancelSubscriptionOrder()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=subscrorders');
	}
	
	function cancelCronjob()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=cronjobs');
	}

	function cancelConfig()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=editconfig');
	}

	function cancelConfigCron()
	{
		JFactory::getApplication()->redirect('index.php?option=com_vikappointments&task=editconfigcron');
	}

	function switchgroup()
	{
		$app = JFactory::getApplication();

		$page = $app->input->getUint('pagegroup', 1) == 1 ? 2 : 1;

		UIFactory::getConfig()->set('pagegroup', $page);
		
		$app->redirect('index.php?option=com_vikappointments&task=groups');
	}

	function switchcal()
	{
		$app = JFactory::getApplication();

		$page = $app->input->get('layout', 'calendar');

		UIFactory::getConfig()->set('calendarlayout', $page);
		
		$app->redirect('index.php?option=com_vikappointments&task=' . $page);
	}
	
	/**
	 * EXPORT RESERVATIONS
	 */
	
	function exportReservations()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$filename 		= $input->getString('filename');
		$export_class 	= $input->getString('export_type');
		$dstart 		= $input->getString('date_start');
		$dend 			= $input->getString('date_end');
		$id_emp 		= $input->getInt('employee', 0);
		
		if (strlen($filename) == 0)
		{
			$filename = 'name';
		}

		$dstart = VikAppointments::createTimestamp($dstart, 0, 0);
		$dend 	= VikAppointments::createTimestamp($dend, 23, 59);
		
		$file_path = VAPADMIN . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $export_class . '.php';

		if (!file_exists($file_path))
		{
			$app->enqueueMessage(JText::sprintf('VAPEXPORTFILENOTFOUNDERR', $export_class . '.php'), 'error');
			$app->redirect('index.php?option=com_vikappointments&task=reservations');
			exit;
		}
		
		require_once $file_path;

		$class = 'VikExporter' . ucwords($export_class);

		if (!class_exists($class))
		{
			throw Exception('Exporter class [' . $class . '] not found!', 404);
		}
		
		// instantiate exporter
		$exporter = new $class($dstart, $dend, $id_emp);
		// fetch string
		$str = $exporter->getString();
		// download the file
		$exporter->export($str, $filename . '.' . $export_class);
	}
	
	/**
	 * SMS
	 */
	
	function get_sms_api_fields()
	{
		$input = JFactory::getApplication()->input;

		$sms_api = $input->getString('sms_api');
		
		$sms_api_path = VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $sms_api;

		$admin_params = array();
		
		if (file_exists($sms_api_path) && strlen($sms_api))
		{
			require_once $sms_api_path;
		
			if (method_exists('VikSmsApi', 'getAdminParameters'))
			{
				$admin_params = VikSmsApi::getAdminParameters();
			}
		}
		
		$sms_api_params = VikAppointments::getSmsApiFields(true);
		
		$html = $this->buildPaymentForm($admin_params, $sms_api_params);

		echo json_encode(array($html));
		die;
	}
	
	function get_sms_api_credit()
	{
		$input = JFactory::getApplication()->input;

		$sms_api 		= $input->getString('sms_api');
		$phone_number 	= $input->getString('sms_api_phone');
		
		if (empty($phone_number))
		{
			$phone_number = '3333333333';
		}
		
		$sms_api_path = VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $sms_api;
		
		if (!file_exists($sms_api_path) || strlen($sms_api) == 0)
		{
			echo json_encode(array(0, JText::_('VAPSMSESTIMATEERR1')));
			die;
		}
		
		require_once $sms_api_path;
		
		if (!method_exists('VikSmsApi', 'estimate'))
		{
			echo json_encode(array(0, JText::_('VAPSMSESTIMATEERR2')));
			die;
		}
		
		$fields = VikAppointments::getSmsApiFields();

		$api = new VikSmsApi(array(), $fields);
		
		$array_result = $api->estimate($phone_number, 'An example message to estimate...');
		
		if ($array_result->errorCode != 0)
		{
			echo json_encode(array(0, JText::_('VAPSMSESTIMATEERR3')));
			die;
		}
		
		echo json_encode(array(1, $array_result->userCredit, VikAppointments::printPriceCurrencySymb($array_result->userCredit)));
		die;
	}

	function sendsms()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$sms_api_name = VikAppointments::getSmsApi();
		$sms_api_path = VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $sms_api_name;

		if (file_exists($sms_api_path) && strlen($sms_api_name))
		{
			require_once $sms_api_path;

			$sms_api_params = VikAppointments::getSmsApiFields();

			$api = new VikSmsApi(array(), $sms_api_params);

			$ids = $input->getUint('cid', array());

			$success = false;
			$error 	 = false;
			$errstr  = '';

			foreach ($ids as $id)
			{
				$q = $dbo->getQuery(true)
					->select($dbo->qn('sid'))
					->from($dbo->qn('#__vikappointments_reservation'))
					->where(array(
						$dbo->qn('id') . ' = ' . $id,
						$dbo->qn('closure') . ' = 0',
					));

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$order_details = VikAppointments::fetchOrderDetails($id, $dbo->loadResult());
					
					$message = VikAppointments::getSmsCustomerTextMessage($order_details);
					$phone 	 = $order_details[0]['purchaser_prefix'] . $order_details[0]['purchaser_phone'];

					$response_obj = $api->sendMessage($phone, $message);

					if (!$api->validateResponse($response_obj))
					{
						$error = true;

						if (empty($response_obj))
						{
							$response_obj = new stdClass;
							$response_obj->failed  = empty($phone) ? 'Missing phone number' : $phone;
							$response_obj->message = $message;
						}

						$errstr .= '<pre>' . print_r($response_obj, true) . '</pre>';
					}
					else
					{
						$success = true;
					}
				}
			}

			if ($success)
			{
				$app->enqueueMessage(JText::_('VAPSMSMESSAGESENT1'));
			}
			
			if ($error)
			{
				$app->enqueueMessage(JText::_('VAPSMSMESSAGESENT0'), 'error');

				if ($errstr)
				{
					$err = '<a href="javascript: void(0);" onclick="jQuery(this).next().toggle();">' . 
							JText::_('VAPIMPORTMOREDETAILSERR') . 
							'</a><div style="display: none;">' . $errstr . '</div>';

					$app->enqueueMessage($err, 'error');
				}
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPSMSESTIMATEERR1'), 'error');
		}
		
		$app->redirect('index.php?option=com_vikappointments&task=reservations');
	}

	function sendcustsms()
	{	
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$id_cust = $input->getUint('id_cust', 0);
		$message = $input->getString('msg', '');
		$keep 	 = $input->getBool('keepdef', 0);

		// get phone number

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('c.phone_prefix', 'u.billing_phone')))
			->from($dbo->qn('#__vikappointments_users', 'u'))
			->leftjoin($dbo->qn('#__vikappointments_countries', 'c') . ' ON ' . $dbo->qn('u.country_code') . ' = ' . $dbo->qn('c.country_2_code'))
			->where($dbo->qn('u.id') . ' = ' . $id_cust);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			$app->enqueueMessage(JText::_('VAPCUSTSMSSENT0'), 'error');
			$app->redirect($return_url);
			exit;
		}
			
		$row = $dbo->loadAssoc();

		$sms_api_name = VikAppointments::getSmsApi();
		$sms_api_path = VAPADMIN . DIRECTORY_SEPARATOR . 'smsapi' . DIRECTORY_SEPARATOR . $sms_api_name;
		
		$return_url = 'index.php?option=com_vikappointments&task=customers';

		if (file_exists($sms_api_path) && strlen($sms_api_name))
		{
			require_once $sms_api_path;

			$sms_api_params = VikAppointments::getSmsApiFields(true);

			$api = new VikSmsApi(array(), $sms_api_params);
			
			$response_obj = $api->sendMessage($row['phone_prefix'] . $row['billing_phone'], $message);
			
			if (!$api->validateResponse($response_obj))
			{
				$app->enqueueMessage(JText::_('VAPCUSTSMSSENT0'), 'error');

				if (!empty($response_obj))
				{
					$err = '<a href="javascript: void(0);" onclick="jQuery(this).next().toggle();">' . 
						JText::_('VAPIMPORTMOREDETAILSERR') . 
						'</a><pre style="display: none;">' . print_r($response_obj, true) . '</pre>';

					$app->enqueueMessage($err, 'error');
				}
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPCUSTSMSSENT1'));
			}
			
			if ($keep)
			{
				UIFactory::getConfig()->set('smstextcust', $message);
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPSMSESTIMATEERR1'), 'error');
		}
		
		$app->redirect($return_url);
	}

	/**
	 * MAKE RECURRENCE
	 */

	function get_recurrence_preview()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$config = UIFactory::getConfig();

		$id = $input->getUint('id', 0);

		$q = $dbo->getQuery(true)
			->select($dbo->qn('sid'))
			->from($dbo->qn('#__vikappointments_reservation'))
			->where(array(
				$dbo->qn('id') . ' = ' . $id,
				$dbo->qn('id_parent') . ' <> -1',
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			echo json_encode(array('status' => 0, 'errstr' => JText::_('VAPMAKERECNOROWS')));
			exit;
		}

		$order = VikAppointments::fetchOrderDetails($id, $dbo->loadResult());

		if ($order === null)
		{
			echo json_encode(array('status' => 0, 'errstr' => JText::_('VAPMAKERECNOROWS')));
			exit;
		}

		$order = $order[0];

		$recurrence = array();
		$recurrence['by'] 		= $input->getUint('r_by', 0);
		$recurrence['amount'] 	= $input->getUint('r_amount', 0);
		$recurrence['for'] 		= $input->getUint('r_for', 0);

		// compose timestamp recurrence
		
		$timestamp_array = $this->get_recurrence_timestamp_array($order['checkin_ts'], $recurrence);

		if (!count($timestamp_array))
		{
			echo json_encode(array('status' => 0, 'errstr' => JText::_('VAPMAKERECNOROWS')));
			exit;
		}

		$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

		$dates = array();

		foreach ($timestamp_array as $ts)
		{
			$avail 	= $this->validate_recurring_availability($order['id_service'], $order['id_employee'], $ts, $order['people'], $dbo);
			$msg 	= $avail ? JText::_('VAPMAKERECDATEOK') : JText::_('VAPMAKERECDATEFAIL');
			
			$dates[] = array(
				'format' 	=> date($dt_format, $ts),
				'available' => $avail,
				'message' 	=> $msg,
			);
		}

		echo json_encode(array('status' => 1, 'dates' => $dates));
		exit;
	}

	function create_recurrence_for()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$config = UIFactory::getConfig();

		$order_id = $input->getUint('id', 0);

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikappointments_reservation'))
			->where(array(
				$dbo->qn('id') . ' = ' . $order_id,
				$dbo->qn('id_parent') . ' <> -1',
			));

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			echo json_encode(array('status' => 0, 'errstr' => JText::_('VAPMAKERECNOROWS')));
			exit;
		}

		$order = $dbo->loadAssoc();
		// avoid duplicated errors
		unset($order['id']);

		$recurrence = array();
		$recurrence['by'] 		= $input->getUint('r_by', 0);
		$recurrence['amount'] 	= $input->getUint('r_amount', 0);
		$recurrence['for'] 		= $input->getUint('r_for', 0);

		// compose timestamp recurrence
		
		$timestamp_array = $this->get_recurrence_timestamp_array($order['checkin_ts'], $recurrence);

		if (!count($timestamp_array))
		{
			echo json_encode(array('status' => 0, 'errstr' => JText::_('VAPMAKERECNOROWS')));
			exit;
		}

		$count = 0;
		foreach ($timestamp_array as $ts)
		{
			$avail = $this->validate_recurring_availability($order['id_service'], $order['id_employee'], $ts, $order['people'], $dbo);

			if ($avail)
			{
				$order['checkin_ts'] = $ts;

				// do not specify $app to avoid enqueuing messages
				$lid = $this->saveNewReservation($order, $dbo);

				if ($lid)
				{
					// get assigned options

					$q = $dbo->getQuery(true)
						->select('*')
						->from($dbo->qn('#__vikappointments_res_opt_assoc'))
						->where($dbo->qn('id_reservation') . ' = ' . $order_id);

					$dbo->setQuery($q);
					$dbo->execute();

					if ($dbo->getNumRows())
					{
						foreach ($dbo->loadObjectList() as $opt)
						{
							$opt->id_reservation = $lid;
							$dbo->insertObject('#__vikappointments_res_opt_assoc', $opt, 'id');
						}
					}

					$count++;
				}
			}
		}

		if ($count)
		{
			echo json_encode(array('status' => 1, 'message' => JText::sprintf('VAPMAKERECSUCCESS1', $count)));
		}
		else
		{
			echo json_encode(array('status' => 0, 'errstr' => JText::_('VAPMAKERECSUCCESS0')));
		}
		
		exit;
	}

	private function get_recurrence_timestamp_array($ts, $recurrence)
	{
		$days_in_months = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

		$arr = getdate($ts);
		
		$timestamp_array = array();

		// Weeks
		if ($recurrence['by'] == 2)
		{
			$steps = 7;
		}
		// Months
		else if ($recurrence['by'] == 3)
		{
			$steps = $days_in_months[$arr['mon'] - 1];
		}
		// Days
		else
		{
			$steps = 1;
		}
		
		// Days
		$num_iter = $recurrence['amount'];

		// Weeks
		if ($recurrence['for'] == 2)
		{
			$num_iter *= 7;
		}
		// Months
		else if ($recurrence['for'] == 3)
		{
			$num_iter *= 31;
		}
		
		for ($i = $steps; $i <= $num_iter; $i += $steps)
		{
			// Weeks
			if ($recurrence['by'] == 2)
			{
				$new_ts = mktime($arr['hours'], $arr['minutes'], 0, $arr['mon'], $arr['mday']+7, $arr['year']);
			}
			// Months
			else if ($recurrence['by'] == 3)
			{
				$new_ts = mktime($arr['hours'], $arr['minutes'], 0, $arr['mon']+1, $arr['mday'], $arr['year']);
			}
			// Days
			else
			{
				$new_ts = mktime($arr['hours'], $arr['minutes'], 0, $arr['mon'], $arr['mday']+1, $arr['year']);
			}
			
			$timestamp_array[] = $new_ts;

			$arr = getdate($new_ts);

			if ($recurrence['by'] == 3)
			{
				$steps = $days_in_months[$arr['mon'] - 1];
			}
		}

		return $timestamp_array;
	}

	private function validate_recurring_availability($id_ser, $id_emp, $ts, $people, $dbo)
	{
		// get service and make sure is still published for the specified day

		$service = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('duration', 'sleep', 'choose_emp', 'max_capacity', 'start_publishing', 'end_publishing')))
			->from($dbo->qn('#__vikappointments_service'))
			->where($dbo->qn('id') . ' = ' . (int) $id_ser);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			return false;
		} 

		$service = $dbo->loadAssoc();

		if ($service['start_publishing'] > 0 && ($service['start_publishing'] > $ts || $service['end_publishing'] < $ts))
		{
			return false;
		}

		// get overrides

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('duration', 'sleep')))
			->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . (int) $id_emp,
				$dbo->qn('id_service') . ' = ' . (int) $id_ser,
			));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			return false;
		}

		$overrides = $dbo->loadAssoc();
		$service['duration'] 	= $overrides['duration'];
		$service['sleep'] 		= $overrides['sleep'];

		// check for the availability

		if (!VikAppointments::isTimeInThePast($ts))
		{
			if ($id_emp != -1)
			{
				$valid = VikAppointments::isEmployeeAvailableFor($id_emp, $id_ser, -1, $ts, $service['duration'] + $service['sleep'], $people, $service['max_capacity'], $dbo);
				
				if ($valid == 1)
				{
					return true;
				}
			}
			else
			{
				$id_emp = VikAppointments::getAvailableEmployeeOnService($id_ser, $ts, $service['duration'] + $service['sleep'], $people, $service['max_capacity'], $dbo);
				
				if ($id_emp != -1)
				{
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * MAIL TEMPLATE
	 */

	function preview_mail_template()
	{
		$dbo 	= JFactory::getDbo();
		$input 	= JFactory::getApplication()->input;
		$config = UIFactory::getConfig();

		$id 	= $input->getUint('id', 0);
		$layout = $input->getString('layout');
		$type 	= $input->getString('type', 'customer');

		$args = array();

		// get type
		switch ($type)
		{
			case 'customer':
				$layoutSetting 	= 'mailtmpl';
				$tmplMethod 	= 'loadEmailTemplate';
				$parseMethod 	= 'parseEmailTemplate';
				break;

			case 'admin':
				$layoutSetting 	= 'adminmailtmpl';
				$tmplMethod 	= 'loadAdminEmailTemplate';
				$parseMethod 	= 'parseAdminEmailTemplate';
				break;

			case 'employee':
				$layoutSetting 	= 'empmailtmpl';
				$tmplMethod 	= 'loadEmployeeEmailTemplate';
				$parseMethod 	= 'parseEmployeesEmailTemplate';
				break;

			case 'cancellation':
				$layoutSetting 	= 'cancmailtmpl';
				$tmplMethod 	= 'loadCancellationEmailTemplate';
				$parseMethod 	= 'parseCancellationEmailTemplate';
				$args[]			= $input->getUint('canctype', 1); // add type for cancellation methods (1 = for admin, 2 = for employee)
				break;

			case 'package':
				$layoutSetting 	= 'packmailtmpl';
				$tmplMethod 	= 'loadPackagesEmailTemplate';
				$parseMethod 	= 'parsePackagesEmailTemplate';
				break;

			case 'waitlist':
				$layoutSetting 	= 'waitlistmailtmpl';
				$tmplMethod 	= 'loadWaitListEmailTemplate';
				$parseMethod 	= 'parseWaitListEmailTemplate';
				$args[] 		= strtotime('today 00:00:00'); 	// add current day for waiting list methods
				$args[] 		= 630; 							// add sample time for waiting list methods
				break;

			default:
				throw new Exception('Type [' . $type . '] not found.', 404);
		}

		if ($layout)
		{
			$old = $config->get($layoutSetting);

			if ($old != $layout)
			{
				// update configuration for test purposes
				$config->set($layoutSetting, $layout);
				$layout = $old;
			}
			else
			{
				// unset layout as it is equals to the current one
				$layout = null;
			}
		}

		// fetch order details

		$order = null;

		if ($type == 'package')
		{
			if (!$id)
			{
				$q = $dbo->getQuery(true);

				$q->select($dbo->qn('id'))
					->from($dbo->qn('#__vikappointments_package_order'))
					->order($dbo->qn('id') . ' DESC');

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$id = $dbo->loadResult();
				}
			}

			$order = VikAppointments::fetchPackagesOrderDetails($id);
		}
		else if ($type == 'waitlist')
		{
			$q = $dbo->getQuery(true);

			$q->select($dbo->qn('id'))
				->from($dbo->qn('#__vikappointments_reservation'))
				->where($dbo->qn('id_parent') . ' <> -1')
				->order($dbo->qn('id') . ' DESC');

			if ($id)
			{
				$q->where($dbo->qn('id') . ' = ' . $id);
			}

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$id = $dbo->loadResult();
			}
			else
			{
				$id = 0;
			}

			$order = VikAppointments::fetchOrderDetails($id);

			if ($order)
			{
				// get only first element
				$order = $order[0];
			}
		}
		else
		{
			if (!$id)
			{
				$q = $dbo->getQuery(true);

				$q->select($dbo->qn(array('id', 'id_parent')))
					->from($dbo->qn('#__vikappointments_reservation'))
					->order($dbo->qn('id') . ' DESC');

				$dbo->setQuery($q, 0, 1);
				$dbo->execute();

				if ($dbo->getNumRows())
				{
					$obj = $dbo->loadObject();
					$id  = $obj->id;

					if ($obj->id_parent != $id && $obj->id_parent > 0)
					{
						// the last order found is part of a multi-order, use the parent ID
						$id = $obj->id_parent;
					}
				}
			}

			$order = VikAppointments::fetchOrderDetails($id);
		}

		if (!$order)
		{
			if ($id)
			{
				throw new Exception('Order/Package [' . $id . '] not found.', 404);
			}
			else
			{
				throw new Exception('Before to see a preview of the e-mail template, you have to create at least a reservation/package first.', 400);
			}
		}

		// load site language
		VikAppointments::loadLanguage(JFactory::getLanguage()->getTag());

		// prepend the order details to the arguments list
		array_unshift($args, $order);

		// get template
		$tmpl = call_user_func_array(array('VikAppointments', $tmplMethod), $args);

		// prepend the template to the arguments list
		array_unshift($args, $tmpl);

		// parse e-mail template
		$tmpl = call_user_func_array(array('VikAppointments', $parseMethod), $args);

		if ($layout)
		{
			// restore previous layout
			$config->set($layoutSetting, $layout);
		}

		$tmpl = '<div class="mail-template-preview">' . $tmpl . '</div>';

		// display resulting template
		$base = VAPBASE . DIRECTORY_SEPARATOR . 'layouts';
		echo JLayoutHelper::render('document.blankpage', array('body' => $tmpl), $base);
		exit;
	}

	/**
	 * PDF INVOICES
	 */
	
	function generateInvoices()
	{
		UILoader::import('libraries.invoice.factory');

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		
		$ids = $input->get('cid', array(), 'uint');

		sort($ids);
		
		$args = array();
		$args['invoicenumber'] 	= $input->getUint('invoicenumber', 1);
		$args['invoicesuffix'] 	= $input->getString('invoicesuffix', '');
		$args['datetype'] 		= $input->getUint('datetype', 1);
		$args['taxes'] 			= $input->getFloat('taxes', 20);
		$args['legalinfo'] 		= $input->getString('legalinfo', '');
		$args['sendinvoice'] 	= $input->getUint('sendinvoice', 0);
		
		$properties = VikAppointments::getPdfConstraints();
		$properties->pageOrientation 	= $input->getString('page_orientation', 'P');
		$properties->pageFormat 		= $input->getString('page_format', 'A4');
		$properties->unit 				= $input->getString('unit', 'mm');
		$properties->imageScaleRatio 	= max(array(5, $input->getFloat('scale', 125))) / 100;

		$config = UIFactory::getConfig();

		$config->set('pdfparams', $args);
		$config->set('pdfconstraints', $properties);
		
		$count_valid = 0;
		
		// GENERATE PDFs
		foreach ($ids as $id)
		{
			$order = VikAppointments::fetchOrderDetails($id);
				
			if ($order && $order[0]['status'] == 'CONFIRMED' 
				&& ($order[0]['id_parent'] == -1 || $order[0]['id_parent'] == $order[0]['id']))
			{
				// get invoice handler
				$invoice = VAPInvoiceFactory::getInstance($order, 'appointments');

				// generate invoice
				$path = $invoice->generate();
				
				if ($path && $args['sendinvoice'])
				{
					// send invoice to customer via e-mail
					$invoice->send($path);
				}

				$count_valid++;
			}
		}
		
		$app->enqueueMessage(JText::sprintf('VAPINVGENERATEDMSG', $count_valid));
		$app->redirect('index.php?option=com_vikappointments&task=reservations&limitstart=' . $input->getUint('limitstart', 0));
	}

	function loadinvoices()
	{			
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$vik = UIApplication::getInstance();

		$config = UIFactory::getConfig();
		
		$year 			= $input->getInt('year');
		$month 			= $input->getInt('month');
		$start_limit 	= $input->getInt('start_limit');
		$limit 			= $input->getInt('limit');
		
		$not_all = true;
		
		$_key 		= $input->getString('keysearch', '');
		$_group 	= $input->getString('group', '');

		if ($_group == 'packages')
		{
			$table = '#__vikappointments_package_order';
		}
		else if ($_group == 'employees')
		{
			$table = '#__vikappointments_subscr_order';
		}
		else
		{
			$table = '#__vikappointments_reservation';
		}

		/**
		 * Obtain the invoices related to the specifie group.
		 *
		 * @since 1.6
		 */
		$all_inv = AppointmentsHelper::getAllInvoices($_group, true);

		if (!empty($_key))
		{
			$app = array();
			
			foreach ($all_inv as $invoice)
			{
				$file_name = basename($invoice);
				
				if (strpos($file_name, $_key) !== false)
				{
					$app[] = $invoice;
				}
			}

			$all_inv = $app;
			unset($app);
		}
		
		$each = array();

		foreach ($all_inv as $invoice)
		{
			$file_name 	= basename($invoice);
			$split 		= explode('-', $file_name);

			$each[$invoice] = array(
				'id' 		=> intval($split[0]),
				'filename' 	=> $file_name,
				'details'	=> '',
				'attr' 		=> array(),
			);

			$q = $dbo->getQuery(true)
				->select($dbo->qn('createdon'))
				->from($dbo->qn($table))
				->where($dbo->qn('id') . ' = ' . $each[$invoice]['id']);
			
			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$each[$invoice]['attr'] = $dbo->loadAssoc();
			}
		}
		
		// filter by date

		$rows = array();

		foreach ($all_inv as $invoice)
		{
			if (!empty($each[$invoice]['attr']['createdon']))
			{
				$createdon = $each[$invoice]['attr']['createdon'];
				$arr = getdate($createdon);

				if (($year == -1 && $month = -1 && $createdon == -1) || ($arr['mon'] == $month && $arr['year'] == $year))
				{
					$rows[] = $invoice;
				}
			}
		}

		$all_inv = $rows;

		// slice the list
		
		$max_limit = count($all_inv);
		if ($max_limit <= $start_limit + $limit)
		{
			$limit 	 = $max_limit - $start_limit;
			$not_all = false;
		}
		$all_inv = array_slice($all_inv, $start_limit, $limit);

		// compose invoice details

		if (count($all_inv))
		{
			$ids = array_map(function($elem) use ($each)
			{
				return $each[$elem]['id']; 
			}, $all_inv);

			$q = $dbo->getQuery(true)
				->select($dbo->qn('o.id'))
				->from($dbo->qn($table, 'o'))
				->where($dbo->qn('o.id') . ' IN (' . implode(',', $ids) . ')');

			if ($_group == 'employees')
			{
				$q->select(array(
					$dbo->qn('e.nickname', 'name'),
					$dbo->qn('e.email', 'mail'),
				));

				$q->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('o.id_employee'));
			}
			else
			{
				$q->select(array(
					$dbo->qn('o.purchaser_nominative', 'name'),
					$dbo->qn('o.purchaser_mail', 'mail'),
				));
			}

			$dbo->setQuery($q, 0, count($ids));
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				foreach ($dbo->loadAssocList() as $details)
				{
					$index = false;

					// invoiceAt() function
					foreach ($each as $k => $row)
					{
						if (array_key_exists('id', $row) && $row['id'] == $details['id'])
						{
							$index = $k;
							break;
						}
					}
					//

					$each[$index]['details'] = !empty($details['name']) ? $details['name'] : $details['mail']; 
				}
			}
		}

		// build HTML
		
		$invoices_html = array();
		
		$cont = $start_limit;

		$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

		foreach ($all_inv as $inv)
		{
			$cont++;

			$url = VAPINVOICE_URI . ($_group ? $_group . '/' : '') . basename($inv);
			
			$html = '
				<div class="vap-archive-fileblock">
					<div class="vap-archive-fileicon">
						<img src="' . VAPASSETS_ADMIN_URI . 'images/invoice@big.png' . '" />
					</div>
					<div class="vap-archive-filename">
						<a href="' . $url . '" target="_blank">
							' . $each[$inv]['filename'] . '
						</a>
						<div>' . $each[$inv]['details'] . '</div>
					</div>
					<input type="checkbox" id="cb' . $cont . '" name="cid[]" class="cid" value="' . $each[$inv]['filename'] . '" onChange="' . $vik->checkboxOnClick() . '">
				</div>
			';
			
			$invoices_html[] = $html;
		}
		
		if (count($invoices_html) == 0)
		{
			$invoices_html[] = '<p>' . JText::_('VAPNOINVOICESONARCHIVE') . '</p>';
		}
		
		echo json_encode(array(1, $cont, $not_all, $invoices_html, count($all_inv)));
		exit; 
	}

	function downloadInvoices()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$ids 	= $input->getString('cid', array(0));
		$year 	= $input->getInt('year');
		$month 	= $input->getInt('month');
		$group 	= $input->getString('group');
		
		if (!count($ids))
		{
			$app->redirect("index.php?option=com_vikappointments&task=invfiles&year={$year}&month={$month}");
			exit;
		}

		$parts = array(
			VAPINVOICE,
		);

		if ($group)
		{
			$parts[] = $group;
		}

		$base = implode(DIRECTORY_SEPARATOR, $parts);

		$pool = array();

		foreach ($ids as $id)
		{
			$pool[] = array(
				'name' => $id,
				'path' => $base . DIRECTORY_SEPARATOR . $id,
			);
		}
		
		if (count($pool) == 1)
		{
			header("Content-disposition: attachment; filename={$pool[0]['name']}");
			header("Content-type: application/pdf");
			readfile($pool[0]['path']);
			exit;
		}
		else
		{
			if (!class_exists('ZipArchive'))
			{
				$app->enqueueMessage('The ZipArchive class is not installed on your server.', 'error');
				$app->redirect("index.php?option=com_vikappointments&task=invfiles&year={$year}&month={$month}");
				exit;
			}
			
			$zipname = $base . DIRECTORY_SEPARATOR . 'invoices.zip';
			
			$zip = new ZipArchive;
			$zip->open($zipname, ZipArchive::CREATE);

			foreach ($pool as $file)
			{
			  $zip->addFile($file['path'], $file['name']);
			}

			$zip->close();
			
			header('Content-Type: application/zip');
			header('Content-disposition: attachment; filename=invoices.zip');
			header('Content-Length: '.filesize($zipname));
			readfile($zipname);
			unlink($zipname);
			exit;
		}
	}

	/**
	 * REPORTS
	 */

	function download_employee_report()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$ids 			= $input->getUint('cid', array());
		$start_range 	= $input->getString('startrange');
		$end_range 		= $input->getString('endrange');
		$ts_way 		= $input->getString('tsway');

		if ($ts_way != 'checkin_ts' && $ts_way != 'createdon')
		{
			$ts_way = 'checkin_ts';
		}

		$config = UIFactory::getConfig();

		$config->set('tswayreport', $ts_way == 'checkin_ts' ? 1 : 2);

		$curr_symb 	= $config->get('currencysymb');
		$symb_pos 	= $config->get('currsymbpos');
		
		// validate date range
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

		if (intval($start_range) > intval($end_range) || intval($end_range)-intval($start_range) > 86400*366*3)
		{
			$start_range = $end_range = '';
		}
		
		if (empty($start_range) || empty($end_range))
		{
			$now = getdate();

			$start_range 	= mktime(0, 0, 0, $now['mon'], 1, $now['year']);
			$end_range 		= mktime(0, 0, 0, $now['mon'] + 1, 1, $now['year']) - 1;
		}
		/////////////

		$csv_list = array();

		foreach ($ids as $id)
		{
			$csv = array(
				array(
					JText::_('VAPMANAGECONFIGRECSINGOPT3'),
					JText::_('VAPCALSTATTHEAD1'),
					JText::_('VAPCALSTATTHEAD2'),
					JText::_('VAPCALSTATTHEAD3'),
				),
			);

			$q = $dbo->getQuery(true)
				->select(array(
					$dbo->qn('r.id_service'),
					$dbo->qn('s.name', 'sname'),
					$dbo->qn('e.nickname'),
					'FROM_UNIXTIME(' . $dbo->qn('r.' . $ts_way) . ', "%Y-%c") AS ' . $dbo->qn('month'),
					'SUM(' . $dbo->qn('r.total_cost') . ') AS ' . $dbo->qn('earning'),
					'COUNT(1) AS ' . $dbo->qn('rescount'),
				))
				->from($dbo->qn('#__vikappointments_reservation', 'r'))
				->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('r.id_employee') . ' = ' . $dbo->qn('e.id'))
				->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('r.id_service') . ' = ' . $dbo->qn('s.id'))
				->where(array(
					$dbo->qn('r.status') . ' = ' . $dbo->q('CONFIRMED'),
					$dbo->qn('r.id_employee') . ' = ' . $id,
					$dbo->qn('r.id_parent') . ' <> -1',
					$dbo->qn('r.' . $ts_way) . ' BETWEEN ' . $start_range . ' AND ' . $end_range,
				))
				->group($dbo->qn(array('r.id_service', 'month')))
				->order(array(
					$dbo->qn('r.' . $ts_way) . ' ASC',
					$dbo->qn('s.ordering') . ' ASC',
				));

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$rows = $dbo->loadAssocList();

				$last_month = -1;

				$sum_earning = $sum_app = $grand_sum_earning = $grand_sum_app = 0;

				foreach ($rows as $r)
				{
					$date = explode('-', $r['month']);

					if ($last_month != -1 && $last_month != $date[1])
					{
						$csv[] = array(
							JText::_('VAPCALSTATTOTALLABEL'), 
							"",
							VikAppointments::printPriceCurrencySymb($sum_earning), 
							$sum_app,
						);

						$sum_earning = $sum_app = 0;
					}

					$csv[] = array(
						$last_month != $date[1] ? JText::_('VAPMONTH' . $date[1]) . ' ' . $date[0] : '',
						$r['sname'],
						VikAppointments::printPriceCurrencySymb($r['earning']),
						$r['rescount'],
					);

					$last_month 		= $date[1];
					$sum_earning 		+= $r['earning'];
					$sum_app 			+= $r['rescount'];
					$grand_sum_earning 	+= $r['earning'];
					$grand_sum_app 		+= $r['rescount'];
				}

				if ($last_month != -1 && count($csv))
				{
					$csv[] = array(
						JText::_('VAPCALSTATTOTALLABEL'), 
						"",
						VikAppointments::printPriceCurrencySymb($sum_earning),
						$sum_app,
					);

					if ($sum_earning != $grand_sum_earning)
					{
						$csv[] = array(
							"",
							"",
							VikAppointments::printPriceCurrencySymb($grand_sum_earning),
							$grand_sum_app,
						);
					}
				}

				$csv_list[] = array(
					'name' 		=> $rows[0]['nickname'],
					'fields' 	=> $csv,
				);
			}
		}

		$pool = array();
		foreach ($csv_list as $csv)
		{
			$src = VAPADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $csv['name'].time() . '.csv';
			
			$handle = fopen($src, 'w');
			foreach ($csv['fields'] as $fields)
			{
				fputcsv($handle, $fields);
			}
			fclose($handle);

			$pool[] = array(
				'name' => $csv['name'] . '.csv',
				'path' => $src,
			);
		}

		if (count($pool) == 0)
		{
			$app->redirect('index.php?option=com_vikappointments&task=employees');
		}
		else if (count($pool) == 1)
		{
			header("Content-disposition: attachment; filename=" . $pool[0]['name']);
			header("Content-type: text/csv");
			readfile($pool[0]['path']);
		}
		else
		{
			if (!class_exists('ZipArchive'))
			{
				$app->enqueueMessage('The ZipArchive class is not installed on your server.', 'error');
				$app->redirect('index.php?option=com_vikappointments&task=employees');
				exit;
			}
			
			$zipname = VAPADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'archive' . time() . '.zip';
			
			$zip = new ZipArchive;
			$zip->open($zipname, ZipArchive::CREATE);
			foreach ($pool as $file)
			{
			  $zip->addFile($file['path'], $file['name']);
			}
			$zip->close();
			
			header('Content-Type: application/zip');
			header('Content-disposition: attachment; filename=reports.zip');
			header('Content-Length: ' . filesize($zipname));
			readfile($zipname);
			unlink($zipname);
		}

		foreach ($pool as $p)
		{
			unlink($p['path']);
		}

		exit;
	}

	/**
	 * CRON JOB
	 */

	function download_cron_installation_file()
	{
		$id_cron 			= JFactory::getApplication()->input->getUint('id_cron');
		$cron_secure_key 	= UIFactory::getConfig()->get('cron_secure_key');
		$root 				= JUri::root();

		/**
		 * The code of the cron job now escapes the internal variables properly.
		 *
		 * @since 1.6.2
		 */

		$php_cron_code = "<?php

define(\"ID_CRON\", $id_cron); // replace the number with the real ID of the cron job
define(\"SECURE_KEY\", \"$cron_secure_key\"); // replace this string with your secure key

// make sure the domain is correct
\$url = \"{$root}index.php?option=com_vikappointments&task=cronjob_listener_rq&tmpl=component\";

\$fields = array(
	\"id_cron\" => ID_CRON,
	\"secure_key\" => md5(SECURE_KEY),
);

\$ch = curl_init();
curl_setopt(\$ch, CURLOPT_URL, \$url);
curl_setopt(\$ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt(\$ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt(\$ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt(\$ch, CURLOPT_TIMEOUT, 20);
curl_setopt(\$ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt(\$ch, CURLOPT_POST, count(\$fields));
curl_setopt(\$ch, CURLOPT_POSTFIELDS, http_build_query(\$fields));

\$output = curl_exec(\$ch);

curl_close(\$ch);

echo \$output;

?>";
	
		header("Content-disposition: attachment; filename=cron_runnable.php");
		header("Content-type: text/php");
		echo $php_cron_code;
		exit;
	}

	/**
	 * UPDATES
	 */

	private function getSoftwareParams()
	{
		$config = UIFactory::getConfig();

		$params = new stdClass;
		$params->version 	= $config->get('version');
		$params->alias 		= CREATIVIKAPP;

		return $params;
	}

	public function check_version_listener()
	{
		$params = $this->getSoftwareParams();

		$dispatcher = UIFactory::getEventDispatcher();

		$result = $dispatcher->triggerOnce('checkVersion', array(&$params));

		if (!$result)
		{
			$result = new stdClass;
			$result->status = 0;
		}

		echo json_encode($result);
		exit;
	}

	public function launch_update()
	{
		$params = $this->getSoftwareParams();

		$dispatcher = UIFactory::getEventDispatcher();

		$json = new stdClass;
		$json->status = false;

		try
		{
			$json->status = $dispatcher->is('doUpdate', array(&$params));

			if (!$json->status)
			{
				$json->error = 'Plugin disabled';
			}
		}
		catch (Exception $e)
		{
			$json->status = false;
			$json->error  = $e->getMessage();
		}

		echo json_encode($json);
		exit;
	}

	/**
	 * Lookup to obtain the right ACL rule to use.
	 *
	 * @var array
	 */
	private $lookupACL = array(
		// Calendar
		'reportsall' 			=> 'access.calendar',
		'reportsallser'			=> 'access.calendar',
		'caldays'				=> 'access.calendar',
		// Configuration
		'editconfig' 			=> 'access.config',
		'editconfigemp' 		=> 'access.config',
		'editconfigcldays' 		=> 'access.config',
		'editconfigsmsapi' 		=> 'access.config',
		'editconfigcron' 		=> 'access.config',
		'conversions'			=> 'access.config',
		'cronjobs'				=> 'access.config',
		'cronjoblogs'			=> 'access.config',
		'cronjobloginfo'		=> 'access.config',
		'managefile'			=> 'access.config',
		'mailtextcust'			=> 'access.config',
		'ccdetails' 			=> 'access.config',
		// Countries
		'states' 				=> 'access.countries',
		'cities' 				=> 'access.countries',
		'country'				=> 'access.countries',
		'state'					=> 'access.countries',
		'city'					=> 'access.countries',
		// Coupons
		'coupongroups'			=> 'access.coupons',
		// Customers
		'customerinfo' 			=> 'access.customers',
		// Custom fields
		'customf' 				=> 'access.custfields',
		'managelangcustomf' 	=> 'access.custfields',
		// Employees
		'emprates' 				=> 'access.employees',
		'emppayments'			=> 'access.employees',
		'emplocations' 			=> 'access.employees',
		'emplocwdays'			=> 'access.employees',
		'reportsemp' 			=> 'access.employees',
		'managelangemployee'	=> 'access.employees',
		// Export
		'export'				=> 'manage', // always accessible
		// Groups
		'managelanggroup' 		=> 'access.groups',
		'managelangempgroup' 	=> 'access.groups',
		// Import
		'import'				=> 'create',
		'manageimport'			=> 'create',
		// Invoices
		'invfiles'				=> 'access.archive',
		// Options
		'managelangoption'		=> 'access.options',
		// Packages
		'packgroups'			=> 'access.packages',
		'packorders'			=> 'access.packages',
		'packorderinfo'			=> 'access.packages',
		'managelangpackage'		=> 'access.packages',
		'managelangpackgroup'	=> 'access.packages',
		'reportspack'			=> 'access.packages',
		// Payments
		'managelangpayment'		=> 'access.payments',
		// Rates
		'rates'					=> 'access.services',
		'ratestest'				=> 'manage', // always accessible
		// Reservations
		'purchaserinfo' 		=> 'access.reservations',
		'exportres' 			=> 'access.reservations',
		'makerecurrence' 		=> 'access.reservations',
		'printorders'			=> 'access.reservations',
		'findreservation'		=> 'access.reservations',
		'manageclosure'			=> 'access.reservations',
		// Reviews
		'revs' 					=> 'access.reviews',
		// Services
		'reportsser'			=> 'access.services',
		'serworkdays' 			=> 'access.services',
		'serviceinfo' 			=> 'access.services',
		'managelangservice' 	=> 'access.services',
		// Subscriptions
		'subscrorders' 			=> 'access.subscriptions',
		'subscremp' 			=> 'access.subscriptions',
		// Dashboard
		'vikappointments' 		=> 'access.dashboard',
	);
}
