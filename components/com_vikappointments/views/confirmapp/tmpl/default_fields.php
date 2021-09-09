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

?>

<h3 class="vap-confirmapp-h3"><?php echo JText::_('VAPCOMPLETEORDERHEADTITLE'); ?></h3>
		
<div id="vapordererrordiv" class="vapordererrordiv" style="display: none;">
	
</div>

<div class="vapcustomfields">
	<?php
	$user_fields = json_decode($this->user['fields'], true);
	
	foreach ($this->customFields as $cf)
	{
		$langName = empty($cf['langname']) ? JText::_($cf['name']) : $cf['langname'];
		
		$textval = '';

		if (!empty($user_fields[$cf['name']]))
		{
			$textval = $user_fields[$cf['name']];
		}

		$displayData = array(
			'label' 	=> $langName,
			'value' 	=> $textval,
			'field' 	=> $cf,
			'user'  	=> $this->user,
			'zip'		=> $this->zip_field_id,
			'enableZip'	=> $this->enableZip,
			'countries' => $this->countries,
		);

		try
		{
			/**
			 * The form field is displayed from the layout below:
			 * /components/com_vikappointments/layouts/form/fields/[TYPE].php
			 * 
			 * If you need to change something from this layout, just create
			 * an override of this layout by following the instructions below:
			 * - open the back-end of your Joomla
			 * - visit the Extensions > Templates > Templates page
			 * - edit the active template
			 * - access the "Create Overrides" tab
			 * - select Layouts > com_vikappointments > form
			 * - start editing the fields/text.php file on your template to 
			 *   create your own layout of the text input.
			 *
			 * @since 1.6
			 */
			echo JLayoutHelper::render('form.fields.' . $cf['type'], $displayData);
		}
		catch (Exception $e)
		{
			// type not supported
		}
	}

	/**
	 * Trigger event to retrieve an optional field that could be used
	 * to confirm the subscription to a mailing list.
	 *
	 * @param 	array 	$user     The user details.
	 * @param 	array 	$options  An array of options.
	 *
	 * @return  string  The HTML to display.
	 *
	 * @since 1.6.3
	 */
	$html = UIFactory::getEventDispatcher()->triggerOnce('onDisplayMailingSubscriptionInput', array($this->user));
	
	// display field if provided
	if ($html)
	{
		?>
		<div>
			<span class="cf-value"><?php echo $html; ?></span>
		</div>
		<?php
	}
	?>
</div>
