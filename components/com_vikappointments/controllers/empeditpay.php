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
 * Employee edit payments controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpEditPay extends UIControllerAdmin
{
	/**
	 * Save employee payments.
	 *
	 * @return 	void
	 */
	public function save()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$ui     = UIApplication::getInstance();

		$auth = EmployeeAuth::getInstance();

		// get args
		$args = array();
		$args['name'] 			= $input->getString('name');
		$args['file'] 			= $input->getString('file');
		$args['published'] 		= $input->getUint('published', 0);
		$args['charge'] 		= $input->getFloat('charge', 0);
		$args['setconfirmed'] 	= $input->getUint('setconfirmed', 0);
		$args['icontype'] 		= $input->getUint('icontype', 0);
		$args['prenote'] 		= $input->getRaw('prenote');
		$args['note'] 			= $input->getRaw('note');
		$args['createdby']		= JFactory::getUser()->id;
		$args['id_employee']	= $auth->id;
		$args['id'] 			= $input->getInt('id', -1);

		switch ($args['icontype'])
		{
			case 1:
				$args['icon'] = $input->getString('font_icon');
				break;

			default:
				$args['icon'] = '';
		}

		if (!$auth->managePayments())
		{
			$app->enqueueMessage(JText::_($args['id'] > 0 ? 'VAPEMPPAYEDITAUTH0' : 'VAPEMPPAYNEWAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emppaylist');
			exit;
		}

		// validate required fields
		$required = array('name', 'file');

		foreach ($required as $key)
		{
			if (empty($args[$key]))
			{
				$app->enqueueMessage(JText::_('VAPEMPPAYUPDATED0'), 'error');
				$this->redirect('index.php?option=com_vikappointments&view=emppaylist');
				exit;
			} 
		}

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
			$app->enqueueMessage(JText::_('VAPEMPPAYUPDATED0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emppaylist');
			exit;
		}
		
		// get payment settings
		$args['params'] = array();

		foreach ($form as $k => $p)
		{
			$args['params'][$k] = $input->getString($k, '');
		}
		
		$args['params'] = json_encode($args['params']);

		// bind data
		$data = (object) $args;

		// insert new payment
		if ($args['id'] <= 0)
		{
			// get ordering
			$q = $dbo->getQuery(true)
				->select('MAX(' . $dbo->qn('ordering') . ')')
				->from($dbo->qn('#__vikappointments_gpayments'))
				->where($dbo->qn('id_employee') . ' = ' . $auth->id);

			$dbo->setQuery($q);
			$dbo->execute();

			$data->ordering = (int) $dbo->loadResult() + 1;

			$dbo->insertObject('#__vikappointments_gpayments', $data, 'id');

			if ($data->id > 0)
			{
				$app->enqueueMessage(JText::_('VAPEMPPAYCREATED1'));
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPPAYCREATED0'), 'error');
			}
		}
		// update existing payment
		else
		{
			$dbo->updateObject('#__vikappointments_gpayments', $data, array('id', 'id_employee'));

			if ($dbo->getAffectedRows())
			{
				$app->enqueueMessage(JText::_('VAPEMPPAYUPDATED1'));
			}
		}

		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=emppaylist';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empeditpay&cid[]=' . $data->id;
		}

		$this->redirect($url);
	}

	/**
	 * Deletes a list of selected services.
	 *
	 * @return 	void
	 */
	public function delete()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->managePayments())
		{
			$app->enqueueMessage(JText::_('VAPEMPPAYDELAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emppaylist');
			exit;
		}
		
		$cid = $input->getUint('cid', array());
		
		// shift the ID if the array contains only one value
		if (count($cid) == 1)
		{
			$cid  = array_shift($cid);
		}

		// if the employee is the owner, remove the payments from the DB
		$removed = $this->_delete($cid, '#__vikappointments_gpayments', array('id_employee' => $auth->id));

		if ($removed)
		{
			$app->enqueueMessage(JText::_('VAPEMPPAYREMOVED1'));
		}

		$this->redirect('index.php?option=com_vikappointments&view=emppaylist&limitstart=0');
	}

	/**
	 * Moves the selected record up or down.
	 *
	 * @return 	void
	 */
	public function move()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$auth = EmployeeAuth::getInstance();

		$cid = $input->getUint('cid', array(0));
		$id  = array_shift($cid);

		// make sure the employee is the owner
		if (!$auth->managePayments($id))
		{
			$app->enqueueMessage(JText::_('VAPEMPPAYEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emppaylist');
			exit;
		}

		$mode = $input->getString('mode', 'up');

		// dispatch sort to parent class
		$this->_move($id, '#__vikappointments_gpayments', $mode, array('id_employee' => $auth->id));

		$this->redirect('index.php?option=com_vikappointments&view=emppaylist');
	}

	/**
	 * Publishes the payment.
	 *
	 * @return 	void
	 */
	public function publish()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$auth = EmployeeAuth::getInstance();

		$cid = $input->getUint('cid', array(0));
		
		// shift the ID if the array contains only one value
		if (count($cid) == 1)
		{
			$cid  = array_shift($cid);
		}

		// don't need to authorise the ID because this check is made while publishing
		if (!$auth->managePayments())
		{
			$app->enqueueMessage(JText::_('VAPEMPPAYEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emppaylist');
			exit;
		}

		$status = $input->getUint('status', 0);

		// dispatch publish to parent class
		$options = array();
		$options['table']  = '#__vikappointments_gpayments';
		$options['status'] = 'published';

		$this->_publish($cid, $status, $options, array('id_employee' => $auth->id));

		$this->redirect('index.php?option=com_vikappointments&view=emppaylist');
	}

	/**
	 * Method used to get the fields of a payment.
	 *
	 * @return 	void
	 */
	public function getPaymentFields()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->managePayments()) {
			$app->enqueueMessage(JText::_('VAPEMPPAYRESTRICTED'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=emppaylist');
			exit;
		}
		
		$gpn 	= $input->getString('gpn', '');
		$id_gp 	= $input->getInt('id_gp', -1);
		
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
			->where(array(
				$dbo->qn('id') . ' = ' . $id_gp,
				$dbo->qn('id_employee') . ' = ' . $auth->id,
			));
		
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
		
		$displayData = array(
			'fields' => $form,
			'params' => $params,
		);

		$html = JLayoutHelper::render('payments.fields', $displayData);
		
		echo json_encode(array($html));
		die;
	}
}
