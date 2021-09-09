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
 * Employee edit reservation controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpManRes extends UIControllerAdmin
{
	/**
	 * Save reservation details.
	 *
	 * @return 	void
	 */
	public function save()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		$args = array();
		$args['id'] 					= $input->getInt('id', -1);
		$args['id_service'] 			= $input->getUint('id_service');
		$args['id_employee'] 			= $auth->id;
		$args['checkin_ts'] 			= $input->getUint('checkin_ts');
		$args['people'] 				= $input->getUint('people');
		$args['purchaser_nominative'] 	= $input->getString('purchaser_nominative');
		$args['purchaser_mail'] 		= $input->getString('purchaser_mail');
		$args['purchaser_phone'] 		= $input->getString('purchaser_phone');
		$args['total_cost'] 			= $input->getFloat('total_cost');
		$args['duration'] 				= $input->getUint('duration');
		$args['paid'] 					= $input->getUint('paid', 0);
		$args['status'] 				= $input->getString('status');
		$args['id_payment'] 			= $input->getInt('id_payment');
		$args['notes'] 					= $input->getRaw('notes', '');
		$args['id_user'] 				= $input->getInt('id_user', -1);
		$args['purchaser_prefix'] 		= "";
		$args['purchaser_country'] 		= "";

		// authorise
		if ($args['id'] > 0 && !$auth->resmanage($args['id']))
		{
			$app->enqueueMessage(JText::_('VAPEMPRESEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emplogin');
			exit;
		}
		else if ($args['id'] <= 0 && !$auth->rescreate())
		{
			$app->enqueueMessage(JText::_('VAPEMPRESNEWAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emplogin');
			exit;
		}

		/**
		 * @note 	If the status was already confirmed, resconfirm() will return 
		 * 			true even if the rule is disabled.
		 *
		 * @since 	1.6
		 */ 
		if ($args['status'] == 'CONFIRMED' && !$auth->resconfirm($args['id']))
		{
			// the employee cannot confirm the reservation
			$args['status'] = 'PENDING';
		}

		// validate
		$args['people'] 	= max(array(1, $args['people']));
		$args['total_cost'] = max(array(0, $args['total_cost']));

		if (empty($args['id_payment']) || !VikAppointments::getPayment($args['id_payment']))
		{
			$args['id_payment'] = -1;
		}

		// get service details
		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('a.sleep'),
				$dbo->qn('s.max_capacity'),
				$dbo->qn('s.choose_emp', 'view_emp'),
			))
			->from($dbo->qn('#__vikappointments_ser_emp_assoc', 'a'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('a.id_service'))
			->where(array(
				$dbo->qn('a.id_service') . ' = ' . $args['id_service'],
				$dbo->qn('a.id_employee') . ' = ' . $args['id_employee'],
			));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows() == 0)
		{
			$app->enqueueMessage(JText::_('VAPRESERVATIONEDITED0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emplogin');
			exit;
		}
		
		$args = array_merge($args, $dbo->loadAssoc());

		// get custom fields
		$_cf = VAPCustomFields::getList(0, $auth->id, $args['id_service'], CF_EXCLUDE_REQUIRED_CHECKBOX);
		
		$tmp = array(); // used to attach the rules to a dummy var

		// validate custom fields
		$cust_req = VAPCustomFields::loadFromRequest($_cf, $tmp, false);

		// JSON encode custom fields list and uploads
		$args['custom_f'] = json_encode($cust_req);

		// fill user details
		if (empty($args['purchaser_nominative']) && !empty($tmp['purchaser_nominative']))
		{
			$args['purchaser_nominative'] = $tmp['purchaser_nominative'];
		}

		if (empty($args['purchaser_mail']) && !empty($tmp['purchaser_mail']))
		{
			$args['purchaser_mail'] = $tmp['purchaser_mail'];
		}

		if (empty($args['purchaser_phone']) && !empty($tmp['purchaser_phone']))
		{
			$args['purchaser_phone'] = $tmp['purchaser_phone'];
		}

		if (!empty($tmp['purchaser_prefix']))
		{
			$args['purchaser_prefix'] = $tmp['purchaser_prefix'];
		}

		if (!empty($tmp['purchaser_country']))
		{
			$args['purchaser_country'] = $tmp['purchaser_country'];
		}

		// validate reservation
		$valid = VikAppointments::isEmployeeAvailableFor(
			$args['id_employee'],
			$args['id_service'],
			$args['id'],
			$args['checkin_ts'],
			$args['duration'] + $args['sleep'],
			$args['people'],
			$args['max_capacity'],
			$dbo
		);

		$args['locked_until'] = time() + VikAppointments::getAppointmentsLockedTime() * 60;

		// is datetime available?
		if ($valid != 1)
		{
			$app->enqueueMessage(JText::_('VAPRESDATETIMENOTAVERR'), 'error');

			if ($args['id'] > 0)
			{
				$this->redirect('index.php?option=com_vikappointments&view=empmanres&cid[]=' . $args['id']);
			}
			else
			{
				$this->redirect('index.php?option=com_vikappointments&view=emplogin');
			}
			exit;
		}

		// unset useless vars to avoid errors
		unset($args['max_capacity']);

		$data = (object) $args;
		
		// insert reservation
		if ($data->id <= 0)
		{
			$data->sid 		 = VikAppointments::generateSerialCode(16);
			$data->conf_key  = VikAppointments::generateSerialCode(12);
			$data->createdon = time();
			$data->createdby = $auth->jid;

			$dbo->insertObject('#__vikappointments_reservation', $data, 'id');

			if ($data->id > 0)
			{
				$app->enqueueMessage(JText::_('VAPNEWRESERVATIONCREATED1'));

				// update parent ID
				$tmp = new stdClass;
				$tmp->id 		= $data->id;
				$tmp->id_parent = $data->id;

				$dbo->updateObject('#__vikappointments_reservation', $tmp, 'id');
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPNEWRESERVATIONCREATED0'));
			}
		}
		// update reservation
		else
		{
			// register status update before altering the database
			VAPOrderStatus::getInstance()->change($data->status, $data->id, 'VAP_STATUS_CHANGED_EMP_MANAGE');
			//

			$dbo->updateObject('#__vikappointments_reservation', $data, 'id');

			if ($dbo->getAffectedRows())
			{
				$app->enqueueMessage(JText::_('VAPRESERVATIONEDITED1'));
			}

			// get SID
			$q = $dbo->getQuery(true)
				->select($dbo->qn('sid'))
				->from($dbo->qn('#__vikappointments_reservation'))
				->where($dbo->qn('id') . ' = ' . $data->id);

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			$data->sid = $dbo->loadResult();
		}

		$options = array();
		$options['id'] 		 = $input->getInt('new_opt_id', array());
		$options['var']		 = $input->getInt('new_opt_var', array());
		$options['price'] 	 = $input->getFloat('new_opt_price', array());
		$options['quantity'] = $input->getUint('new_opt_quant', array());

		$remove_options = $input->getUint('del_opt_id', array());

		if ($data->id)
		{
			// delete options
			$this->deleteOptions($remove_options, $dbo);

			// save options
			$this->saveOptions($data->id, $options, $dbo);

			if ($input->getBool('notifycust'))
			{
				$order_details = VikAppointments::fetchOrderDetails($data->id, $data->sid);
				VikAppointments::sendCustomerEmail($order_details);
			}
		}
		
		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=emplogin';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empmanres&cid[]=' . $args['id'];
		}

		$this->redirect($url);
	}

	/**
	 * Saves a list of options.
	 *
	 * @param 	integer  $id 	   The reservation ID.
	 * @param 	array 	 $options  The options to save.
	 * @param 	mixed 	 $dbo 	   The database object.
	 *
	 * @return 	void  
	 */
	public function saveOptions($id, $options, $dbo = null)
	{
		if (!count($options))
		{
			return;
		}

		if (is_null($dbo))
		{
			$dbo = JFactory::getDbo();
		}
			
		for ($i = 0; $i < count($options['id']); $i++)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('id', 'inc_price', 'quantity')))
				->from($dbo->qn('#__vikappointments_res_opt_assoc'))
				->where(array(
					$dbo->qn('id_reservation') . ' = ' . $id,
					$dbo->qn('id_option') . ' = ' . (int) $options['id'][$i],
				));

			if ($options['var'][$i] > 0)
			{
				$q->where($dbo->qn('id_variation') . ' = ' . (int) $options['var'][$i]);
			}

			$dbo->setQuery($q, 0, 1);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				// the options already exists, update it
				$obj = $dbo->loadObject();
				$obj->inc_price += (float) $options['price'][$i];
				$obj->quantity  += (int) $options['quantity'][$i];

				$dbo->updateObject('#__vikappointments_res_opt_assoc', $obj, 'id');
			}
			else
			{
				// create a new option
				$obj = new stdClass;
				$obj->id_reservation 	= $id;
				$obj->id_option 		= (int) $options['id'][$i];
				$obj->id_variation 		= (int) $options['var'][$i];
				$obj->inc_price 		= (float) $options['price'][$i];
				$obj->quantity 			= (int) $options['quantity'][$i];

				$dbo->insertObject('#__vikappointments_res_opt_assoc', $obj, 'id');
			}
		}
	}

	/**
	 * Deletes a list of options.
	 *
	 * @param 	array 	$options  The list of IDs to remove.
	 * @param 	mixed 	$dbo 	  The database object.
	 *
	 * @return 	void  
	 */
	public function deleteOptions($options, $dbo = null)
	{
		if (!count($options))
		{
			return;
		}

		if (is_null($dbo))
		{
			$dbo = JFactory::getDbo();
		}

		$q = $dbo->getQuery(true)
			->delete($dbo->qn('#__vikappointments_res_opt_assoc'))
			->where($dbo->qn('id') . ' IN (' . implode(',', $options) . ')');

		$dbo->setQuery($q);
		$dbo->execute();
	}

	/**
	 * Deletes the given reservation.
	 *
	 * @return 	void
	 */
	public function delete()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->resremove())
		{
			$app->enqueueMessage(JText::_('VAPEMPRESDELAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emplogin');
			exit;
		}
		
		$cid  = $input->getUint('cid', array(0));
		$cid  = array_shift($cid);

		// get reservation details
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('r.id', 'r.sid', 'r.id_employee', 'r.total_cost', 'r.tot_paid', 'r.checkin_ts')))
			->select($dbo->qn(array('e.lastname', 'e.firstname')))
			->select($dbo->qn('s.name', 'sname'));

		$q->from($dbo->qn('#__vikappointments_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikappointments_employee', 'e') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('r.id_employee'))
			->leftjoin($dbo->qn('#__vikappointments_service', 's') . ' ON ' . $dbo->qn('s.id') . ' = ' . $dbo->qn('r.id_service'));

		$q->where(array(
			$dbo->qn('r.id_employee') . ' = ' . $auth->id,
			$dbo->qn('r.id') . ' = ' . $cid,
		));
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		if ($dbo->getNumRows())
		{	
			$row = $dbo->loadObject();

			// if the employee is the owner, remove the reservation from the DB
			$removed = $this->_delete($cid, '#__vikappointments_reservation', array('id_employee' => $auth->id));
			
			// detach relationship
			$options = array();
			$options['table'] 	= '#__vikappointments_res_opt_assoc';
			$options['pk']		= 'id_reservation';

			$removed = $this->_delete($cid, $options) || $removed;
			
			if ($auth->isNotifyOnReservationDelete())
			{
				$config = UIFactory::getConfig();

				$dt_format = $config->get('dateformat') . ' ' . $config->get('timeformat');

				$tot_paid = '';
				if ($row->tot_paid > 0)
				{
					$tot_paid = ' (' . VikAppointments::printPriceCurrencySymb($row->tot_paid) . ')';
				}

				$mail_sub  = JText::sprintf('VAPREMRESMAILSUBJECT', $row->id_employee);

				$mail_cont = JText::_('VAPMANAGERESERVATION1') . ": {$row->id} - {$row->sid}<br />";
				$mail_cont .= "{$row->sname} - {$row->firstname} {$row->lastname}<br />";
				$mail_cont .= date($dt_format, $row->checkin_ts) . ' - ' . VikAppointments::printPriceCurrencySymb($row->total_cost) . $tot_paid;

				$mail_cont = JText::sprintf('VAPREMRESMAILCONT', $mail_cont);
					
				$vik = UIApplication::getInstance();

				$mail_list = VikAppointments::getAdminMailList();
				foreach ($mail_list as $_m)
				{
					$vik->sendMail($_m, $_m, $_m, $_m, $mail_sub, $mail_cont, null, true);
				}
			}
			
			if ($removed)
			{
				$app->enqueueMessage(JText::_('VAPEMPRESREMOVED1'));
			}
		}
		else
		{
			$app->enqueueMessage(JText::_('VAPEMPRESREMOVED0'), 'error');
		}
		
		$this->redirect('index.php?option=com_vikappointments&view=emplogin');
	}

	/**
	 * Returns the details of the given option.
	 * Used in the reservation management to insert/update 
	 * the option details (quantity, proce, variation, etc...).
	 *
	 * @return 	void
	 */
	public function getOptionDetails()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();

		$res = array(0, 'Error');

		if ($auth->isEmployee())
		{
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

			if ($dbo->getNumRows())
			{
				$option = $dbo->loadAssocList();
				$option[0]['variations'] = array();

				foreach ($option as $o)
				{
					if (!empty($o['id_variation']))
					{
						$option[0]['variations'][] = array(
							'id' 	=> $o['id_variation'],
							'name' 	=> $o['var_name'],
							'price' => $o['var_price'],
						);
					}
				}

				$res = array(1, $option[0]);
			}
		}
		
		echo json_encode($res);
		die;
	}

	/**
	 * Returns a list of users that match the query.
	 * The users are filtered using the specified "term" via request.
	 * If the ID is provided, the specific user will be returned instead.
	 *
	 * @return 	void
	 */
	function searchusers()
	{
		$auth = EmployeeAuth::getInstance();

		if (!$auth->isEmployee())
		{
			die ('you are not authorized to view this resource!');
		}

		$dbo 	= JFactory::getDbo();
		$input 	= JFactory::getApplication()->input;
		
		$search = $input->getString('term', '');
		$id 	= $input->getInt('id', null);

		$rows = array();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('u.id', 'u.billing_name', 'u.billing_mail', 'u.billing_phone', 'u.country_code', 'u.fields')))
			->from($dbo->qn('#__vikappointments_users', 'u'))
			->where(1);

		if (!is_null($id))
		{
			$q->where($dbo->qn('u.id') . ' = ' . $id);
		}
		else
		{
			/**
			 * Extend where to search customers also by e-mail and phone number.
			 *
			 * @since 1.6
			 */

			$q->andWhere(array(
				$dbo->qn('u.billing_name') . ' LIKE ' . $dbo->q("%$search%"),
				$dbo->qn('u.billing_mail') . ' LIKE ' . $dbo->q("%$search%"),
				$dbo->qn('u.billing_phone') . ' = ' . $dbo->q($search),
			));
		}

		/**
		 * @issue 56
		 * In case there is at least a subscription, the website is probably
		 * configured as a portal. This means that the employees are not related 
		 * each other. So, they shouldn't be able to see the details of the
		 * customers that booked reservations for other employees.
		 * 
		 * @since 1.6
		 */
		if (VikAppointments::isSubscriptions())
		{
			$q->leftjoin($dbo->qn('#__vikappointments_reservation', 'r') . ' ON ' . $dbo->qn('u.id') . ' = ' . $dbo->qn('r.id_user'));
			$q->where($dbo->qn('r.id_employee') . ' = ' . $auth->id);
			$q->group($dbo->qn('u.id'));
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
}
