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
 * Employee edit custom field controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpEditCustField extends UIControllerAdmin
{
	/**
	 * Save employee custom field.
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
		$args['name'] 			= $input->getRaw('name', ''); // html is allowed
		$args['type'] 			= $input->getString('type', '');
		$args['required'] 		= $input->getUint('required', 0);
		$args['multiple'] 		= $input->getUint('multiple', 0);
		$args['rule'] 			= $input->getUint('rule', 0);
		$args['poplink'] 		= $input->getString('poplink', '');
		$args['id_employee'] 	= $input->getInt('id_employee', 0);
		$args['id'] 			= $input->getInt('id', -1);
		$args['id_employee']	= $auth->id;
		$args['choose'] 		= '';

		$settings = array();
		$settings['choose_select'] 	 = $input->getString('choose', array());
		$settings['choose_filter'] 	 = $input->getString('filters', '');
		$settings['def_prfx'] 		 = $input->getString('def_prfx', '');
		$settings['sep_suffix'] 	 = $input->getString('sep_suffix', '');
		$settings['number_min']		 = $input->getString('number_min', '');
		$settings['number_max']		 = $input->getString('number_max', '');
		$settings['number_decimals'] = $input->getUint('number_decimals', 0);
		
		// authorise
		if (!$auth->manageCustomFields())
		{
			$app->enqueueMessage(JText::_('VAPEMPCUSTOMFEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empcustfields');
			exit;
		}

		// validation

		if ($args['type'] == 'select')
		{
			$args['choose'] = implode(';;__;;', array_filter($settings['choose_select']));
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
			$args['choose'] = empty($settings['choose_filter']) ? 'png, jpg, pdf' : $settings['choose_filter'];
		}
		else if ($args['type'] == 'separator')
		{
			$args['choose'] 	= $settings['sep_suffix'];
			$args['rule']		= 0;
			$args['required']	= 0;
		}
		else if (VAPCustomFields::isPhoneNumber($args))
		{
			$args['choose'] = $settings['def_prfx'];
		}
		
		$required = array('name', 'type');

		foreach ($required as $key)
		{
			if (empty($args[$key]))
			{
				$app->enqueueMessage(JText::_('VAPERRINSUFFCUSTF'), 'error');
				$this->redirect('index.php?option=com_vikappointments&view=empcustfields');
				exit;
			} 
		}

		// bind data
		$data = (object) $args;

		// insert new custom field
		if ($data->id <= 0)
		{
			// get ordering
			$q = $dbo->getQuery(true)
				->select('MAX(' . $dbo->qn('ordering') . ')')
				->from($dbo->qn('#__vikappointments_custfields'));

			$dbo->setQuery($q);
			$dbo->execute();

			$data->ordering = (int) $dbo->loadResult() + 1;

			$dbo->insertObject('#__vikappointments_custfields', $data, 'id');

			if ($data->id > 0)
			{
				$app->enqueueMessage(JText::_('VAPEMPCUSTOMFCREATED1'));
			}
			else
			{
				$app->enqueueMessage(JText::_('VAPEMPCUSTOMFCREATED0'), 'error');
			}
		}
		// update existing custom field
		else
		{
			$dbo->updateObject('#__vikappointments_custfields', $data, array('id', 'id_employee'));

			if ($dbo->getAffectedRows())
			{
				$app->enqueueMessage(JText::_('VAPEMPCUSTOMFUPDATED1'));
			}
		}
		
		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=empcustfields';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empeditcustfield&cid[]=' . $data->id;
		}

		$this->redirect($url);
	}

	/**
	 * Removes the custom field.
	 *
	 * @return 	void
	 */
	public function delete()
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

		// don't need to authorise the ID because this check is made while deleting
		if (!$auth->manageCustomFields())
		{
			$app->enqueueMessage(JText::_('VAPEMPCUSTOMFEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empcustfields');
			exit;
		}

		// dispatch delete to parent class
		$del = $this->_delete($cid, '#__vikappointments_custfields', array('id_employee' => $auth->id));

		if ($del)
		{
			$app->enqueueMessage(JText::_('VAPEMPCUSTOMFREMOVED1'));
		}

		$this->redirect('index.php?option=com_vikappointments&view=empcustfields&limitstart=0');
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
		if (!$auth->manageCustomFields($id))
		{
			$app->enqueueMessage(JText::_('VAPEMPCUSTOMFEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empcustfields');
			exit;
		}

		$mode = $input->getString('mode', 'up');

		// dispatch sort to parent class
		$this->_move($id, '#__vikappointments_custfields', $mode, array('id_employee' => $auth->id));

		$this->redirect('index.php?option=com_vikappointments&view=empcustfields');
	}

	/**
	 * Publishes the custom field.
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
		if (!$auth->manageCustomFields())
		{
			$app->enqueueMessage(JText::_('VAPEMPCUSTOMFEDITAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empcustfields');
			exit;
		}

		$status = $input->getUint('status', 0);

		// dispatch publish to parent class
		$options = array();
		$options['table']  = '#__vikappointments_custfields';
		$options['status'] = 'required';

		$this->_publish($cid, $status, $options, array('id_employee' => $auth->id));

		$this->redirect('index.php?option=com_vikappointments&view=empcustfields');
	}
}
