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
 * Employee edit profile controller.
 *
 * @since 	1.6
 */
class VikAppointmentsControllerEmpEditProfile extends UIControllerAdmin
{
	/**
	 * Save employee profile details.
	 *
	 * @return 	void
	 */
	public function save()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();

		$auth = EmployeeAuth::getInstance();
		
		if (!$auth->manage())
		{
			$app->enqueueMessage(JText::_('VAPEDITEMPAUTH0'), 'error');
			$this->redirect('index.php?option=com_vikappointments&view=empeditprofile');
			exit;
		}
		
		$args = array();
		$args['firstname'] 		= $input->getString('firstname', '');
		$args['lastname'] 		= $input->getString('lastname', '');
		$args['nickname'] 		= $input->getString('nickname', '');
		$args['email'] 			= $input->getString('email', '');
		$args['phone'] 			= $input->getString('phone', '');
		$args['notify'] 		= $input->getUint('notify', 0);
		$args['showphone'] 		= $input->getUint('showphone', 0);
		$args['id_group'] 		= $input->getInt('id_group', -1);
		$args['quick_contact'] 	= $input->getUint('quick_contact', 0);
		$args['note'] 			= $input->getRaw('note', '');
		$args['image'] 			= $auth->image;
		$args['id']				= $auth->id;
		
		$img = $input->files->get('image', null, 'array');
		
		$required = array('firstname', 'lastname', 'nickname', 'email', 'phone');

		foreach ($required as $key)
		{
			if (empty($args[$key]))
			{
				$app->enqueueMessage(JText::_('VAPEDITEMPUPDATED0'), 'error');
				$this->redirect('index.php?option=com_vikappointments&view=empeditprofile');
				exit;
			} 
		}
		
		// upload picture
		$ori = VikAppointments::uploadImage($img, VAPMEDIA . DIRECTORY_SEPARATOR);
		
		if ($ori['esit'] == -1)
		{
			$app->enqueueMessage(JText::_('VAPCONFIGUPLOADERROR'), 'error');
		}
		else if ($ori['esit'] == -2)
		{
			$app->enqueueMessage(JText::_('VAPCONFIGFILETYPEERROR'), 'error');
		}
		else if ($ori['esit'] == 1)
		{
			UILoader::import('libraries.image.resizer');
			
			$original 	= VAPMEDIA . DIRECTORY_SEPARATOR . $ori['name'];
			$thumb 		= VAPMEDIA_SMALL . DIRECTORY_SEPARATOR . $ori['name'];

			VikAppointmentsImageResizer::proportionalImage($original,  $thumb, VikAppointments::getSmallWidthResize(), VikAppointments::getSmallHeightResize());

			/**
			 * Crop also the original image, if needed.
			 *
			 * @since 1.6.1
			 */
			if (VikAppointments::isImageResize())
			{
				$final_dest = VAPMEDIA . DIRECTORY_SEPARATOR . $ori['name'];
				$crop_dest 	= str_replace($ori['name'], '$_' . $ori['name'], $final_dest);
				VikAppointmentsImageResizer::proportionalImage($final_dest, $crop_dest, VikAppointments::getOriginalWidthResize(), VikAppointments::getOriginalHeightResize());
				
				copy($crop_dest, $final_dest);
				unlink($crop_dest);
			}

			$args['image'] = $ori['name'];

			$args['image'] = $ori['name'];
		}

		// validate group
		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikappointments_employee_group'))
			->where($dbo->qn('id') . ' = ' . $args['id_group']);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		
		if (!$dbo->getNumRows())
		{
			$args['id_group'] = -1;
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

		// update object
		$data = (object) $args;
		
		$dbo->updateObject('#__vikappointments_employee', $data, 'id');

		if ($dbo->getAffectedRows())
		{
			$app->enqueueMessage(JText::_('VAPEDITEMPUPDATED1'));
		}
		
		// redirect
		if ($input->getBool('return', 0))
		{
			$url = 'index.php?option=com_vikappointments&view=emplogin';
		}
		else
		{
			$url = 'index.php?option=com_vikappointments&view=empeditprofile';
		}

		$this->redirect($url);
	}
}
