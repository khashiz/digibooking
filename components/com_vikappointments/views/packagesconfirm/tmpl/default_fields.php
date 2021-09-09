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

<div class="vap-packconf-box">

	<h3><?php echo JText::_('VAPCOMPLETEORDERHEADTITLE'); ?></h3>
	
	<div id="vapordererrordiv" class="vapordererrordiv" style="display: none;"></div>
	
	<div class="vap-packconf-custfields">
		
		<div class="vapcustomfields">
			<?php
			$user_fields = (array) json_decode($this->user['fields'], true);
			
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
					'countries' => $this->countries,
				);

				try
				{
					echo JLayoutHelper::render('form.fields.' . $cf['type'], $displayData);
				}
				catch (Exception $e)
				{
					// type not supported
				}
			}
			?>
		</div>

	</div>

</div>
