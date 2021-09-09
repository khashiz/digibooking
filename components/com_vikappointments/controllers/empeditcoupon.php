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

UILoader::import('libraries.controllers.admin');

/**
 * Employee edit coupon controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpEditCoupon extends UIControllerAdmin
{
	/**
	 * Save employee coupon.
	 *
	 * @return 	void
	 */
	public function save()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		// get args
		$args = array();
		$args['code'] 			= $input->getString('code');
		$args['type'] 			= $input->getUint('coupon_type', 1);
		$args['max_quantity'] 	= $input->getUint('max_quantity', 1);
		$args['used_quantity'] 	= $input->getUint('used_quantity', 0);
		$args['remove_gift'] 	= $input->getUint('remove_gift', 0);
		$args['percentot'] 		= $input->getUint('percentot', 1);
		$args['value'] 			= $input->getFloat('value', 0);
		$args['mincost'] 		= $input->getFloat('mincost', 0);
		$args['pubmode'] 		= $input->getUint('pubmode', 0);
		$args['dstart'] 		= $input->getString('datestart', '');
		$args['dend'] 			= $input->getString('dateend', '');
		$args['lastminute'] 	= $input->getUint('lastminute', 0);
		$args['notes'] 			= $input->getString('notes', '');
		$args['id'] 			= $input->getInt('id', -1);
		
		// authorise
		if (!$auth->manageCoupons($args['id']))
		{
			$app->enqueueMessage(JText::_('VAPEMPCOUPONEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empcoupons');
			exit;
		}

		$id_services = $input->getUint('id_services', array());
		$id_services = $this->filterServices($id_services, $auth->id);

		// validation
		$args['max_quantity'] 	= max(array(1, $args['max_quantity']));
		$args['value'] 			= abs($args['value']);
		$args['mincost'] 		= abs($args['mincost']);

		if (!in_array($args['type'], array(1, 2)))
		{
			$args['type'] = 1;
		}

		if (!in_array($args['percentot'], array(1, 2)))
		{
			$args['percentot'] = 1;
		}

		if (empty($args['dstart']) || empty($args['dend']))
		{
			$args['dstart'] = $args['dend'] = -1;
		}
		else
		{
			$args['dstart'] = VikAppointments::createTimestamp($args['dstart'], 0, 0);
			$args['dend'] 	= VikAppointments::createTimestamp($args['dend'], 0, 0);

			if ($args['dstart'] > $args['dend'])
			{
				$args['dstart'] = $args['dend'] = -1;
			}
		}
		
		$required = array('code');

		foreach ($required as $key)
		{
			if (empty($args[$key]))
			{
				$app->enqueueMessage(JText::_('VAPERRINSUFFCUSTF'), 'error');
				$this->redirect('index.php?option=com_vikappointments&view=empcoupons');
				exit;
			} 
		}

		// bind data
		$data = (object) $args;

		// insert new coupon
		if ($data->id <= 0)
		{
			$dbo->insertObject('#__vikappointments_coupon', $data, 'id');

			if ($data->id > 0)
			{
				$app->enqueueMessage(JText::_('VAPEMPCOUPONCREATED1'));
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPCOUPONCREATED0'), 'error');
			}

			// create employee relationship
			$this->insertEmployeeRelationship($auth->id, $data->id);

			// create services relationship
			$this->insertServicesRelationship($id_services, $data->id);
		}
		// update existing coupon
		else
		{
			$dbo->updateObject('#__vikappointments_coupon', $data, 'id');

			if ($dbo->getAffectedRows())
			{
				$app->enqueueMessage(JText::_('VAPEMPCOUPONUPDATED1'));
			}

			// update services relationship
			$this->updateServicesRelationship($id_services, $auth->id, $data->id);
		}
		
		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=empcoupons';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empeditcoupon&cid[]=' . $data->id;
		}

		$this->redirect($url);
	}

	/**
	 * Method used to filter a list of services.
	 * The scope is to unset all the services that doesn't 
	 * have a relation with this employee.
	 *
	 * @param 	array 	 $ids 	 	The services IDs list.
	 * @param 	integer  $employee  The employee ID.
	 *
	 * @return 	array 	 The filtered list.
	 */
	protected function filterServices(array $ids, $employee)
	{
		if (!count($ids))
		{
			return array();
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_service'))
			->from($dbo->qn('#__vikappointments_ser_emp_assoc'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . (int) $employee,
				$dbo->qn('id_service') . ' IN (' . implode(',', $ids) . ')',
			));

		$dbo->setQuery($q, 0, count($ids));
		$dbo->execute();

		if (!$dbo->getNumRows())
		{
			return array();
		}

		return $dbo->loadColumn();
	}

	/**
	 * Insert the employee relationship.
	 *
	 * @param 	integer  $id_employee 	The employee ID.
	 * @param 	integer  $id_coupon  	The coupon ID.
	 *
	 * @return 	void
	 */
	protected function insertEmployeeRelationship($id_employee, $id_coupon)
	{
		if ($id_coupon <= 0)
		{
			return;
		}

		$dbo = JFactory::getDbo();

		$rel = new stdClass;
		$rel->id_employee = $id_employee;
		$rel->id_coupon   = $id_coupon;

		$dbo->insertObject('#__vikappointments_coupon_employee_assoc', $rel, 'id');
	}

	/**
	 * Inserts the services relationship.
	 *
	 * @param 	array 	 $services 	 The services IDs list.
	 * @param 	integer  $id_coupon  The coupon ID.
	 *
	 * @return 	void
	 */
	protected function insertServicesRelationship(array $services, $id_coupon)
	{
		if ($id_coupon <= 0)
		{
			return;
		}

		$dbo = JFactory::getDbo();

		$rel = new stdClass;
		$rel->id_service = 0;
		$rel->id_coupon  = $id_coupon;

		foreach ($services as $s)
		{
			// unset ID assigned during the loop
			unset($rel->id);
			$rel->id_service = (int) $s;
			
			$dbo->insertObject('#__vikappointments_coupon_service_assoc', $rel, 'id');
		}
	}

	/**
	 * Updates the services relationship.
	 * It is meant to insert the new services and to remove
	 * the existing ones that are no more selected.
	 *
	 * @param 	array 	 $services 	 	The services IDs list.
	 * @param 	integer  $id_employee  	The employee ID.
	 * @param 	integer  $id_coupon  	The coupon ID.
	 *
	 * @return 	void
	 */
	protected function updateServicesRelationship(array $services, $id_employee, $id_coupon)
	{
		if ($id_coupon <= 0)
		{
			return;
		}

		$dbo = JFactory::getDbo();

		// get existing services
		$existing = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_service'))
			->from($dbo->qn('#__vikappointments_coupon_service_assoc'))
			->where($dbo->qn('id_coupon') . ' = ' . (int) $id_coupon);

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$existing = $dbo->loadColumn();
		}

		// create relationship for new services
		$missing = array();

		foreach ($services as $s)
		{
			if (!in_array($s, $existing))
			{
				$missing[] = $s;
			}
		}

		$this->insertServicesRelationship($missing, $id_coupon);

		// detach relationship for existing services that are no more selected
		$detach = array();

		foreach ($existing as $s)
		{
			if (!in_array($s, $services))
			{
				$detach[] = $s;
			}
		}

		$this->deleteServicesRelationships($id_coupon, $detach);
	}

	/**
	 * Removes the coupon.
	 *
	 * @return 	void
	 */
	public function delete()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		if (!$auth->manageCoupons())
		{
			$app->enqueueMessage(JText::_('VAPEMPCOUPONEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empcoupons');
			exit;
		}

		$cid = $input->getUint('cid', array(0));
		
		// filter the coupons to obtain only the ones owned by the employee
		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_coupon'))
			->from($dbo->qn('#__vikappointments_coupon_employee_assoc'))
			->where(array(
				$dbo->qn('id_employee') . ' = ' . $auth->id,
				$dbo->qn('id_coupon') . ' IN (' . implode(',', $cid) . ')',
			));

		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$cid = $dbo->loadColumn();
		}
		else
		{
			$cid = array();
		}

		// remove the coupons (the validation has been made previously)
		$removed = $this->_delete($cid, '#__vikappointments_coupon');

		// remove relationships
		$this->deleteEmployeeRelationship($cid, $auth->id);
		$this->deleteServicesRelationships($cid);

		if ($removed)
		{
			$app->enqueueMessage(JText::_('VAPEMPCOUPONREMOVED1'));
		}

		$this->redirect('index.php?option=com_vikappointments&view=empcoupons&limitstart=0');
	}

	/**
	 * Removes the employee relationship.
	 *
	 * @param 	mixed  	 $id_coupon  	The coupon ID or a list of coupons.
	 * @param 	integer  $id_employee 	The employee ID.
	 *
	 * @return 	void
	 */
	protected function deleteEmployeeRelationship($id_coupon, $id_employee)
	{
		$options = array();
		$options['table'] = '#__vikappointments_coupon_employee_assoc';
		$options['pk']	  = 'id_coupon';

		// detach all the employee-coupon relationships
		$this->_delete($id_coupon, $options, array('id_employee' => $id_employee));
	}

	/**
	 * Removes the services relationship.
	 *
	 * @param 	mixed  	 $id_coupon  The coupon ID or a list of coupons.
	 * @param 	array 	 $services 	 The services IDs list.
	 *
	 * @return 	void
	 */
	protected function deleteServicesRelationships($id_coupon, array $ids = null)
	{
		if (is_array($ids) && !count($ids))
		{
			return;
		}

		$options = array();
		$options['table'] = '#__vikappointments_coupon_service_assoc';
		$options['pk']	  = 'id_coupon';

		$where = array();

		if (!is_null($ids))
		{
			$where['id_service'] = $ids;
		}

		// detach all the service-coupon relationships
		$this->_delete($id_coupon, $options, $where);
	}
}
