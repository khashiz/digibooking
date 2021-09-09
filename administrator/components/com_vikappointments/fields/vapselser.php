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
defined('_JEXEC') or die('Restricted Area');

jimport('joomla.form.formfield');

/**
 * Form field used to display a list of services.
 *
 * @since 1.1
 */
class JFormFieldVapselser extends JFormField
{
	/**
	 * Always override type property (probably useless).
	 *
	 * @var string
	 */
	protected $type = 'vapselser';
	
	/**
	 * Method used to render the form field.
	 *
	 * @return 	string 	The HTML field.
	 */
	function getInput()
	{	
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('s.id', 'value'))
			->select($dbo->qn('s.name', 'text'))
			->from($dbo->qn('#__vikappointments_service', 's'))
			->order($dbo->qn('s.name') . ' ASC');
		
		$dbo->setQuery($q);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			$services = $dbo->loadObjectList();
		}
		else
		{
			$services = array();
		}

		/**
		 * Specify required class.
		 *
		 * @since 1.6.3
		 */
		$req = $this->required !== 'false' && $this->required ? ' required' : '';

		$html  = '<select class="inputbox' . $req . '" name="' . $this->name . '">';
		$html .= '<option value=""></option>';
		$html .= JHtml::_('select.options', $services, 'value', 'text', $this->value);
		$html .='</select>';

		return $html;
    }
}
