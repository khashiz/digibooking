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

$sel 		= $this->reservation;
$cfields 	= $this->custom_fields;

$vik = UIApplication::getInstance();

$cf_data = json_decode($sel['custom_f'] ? $sel['custom_f'] : '{}', true);

$uploaded_files = json_decode($sel['uploads'], true);

?>

<div class="span8">
	<?php echo $vik->openEmptyFieldset(); ?>

		<?php
		foreach ($cfields as $cf)
		{
			if (!empty($cf['poplink']))
			{
				$fname = "<a href=\"" . $cf['poplink'] . "\" id=\"vapcf" . $cf['id'] . "\" rel=\"{handler: 'iframe', size: {x: 750, y: 600}}\" target=\"_blank\" class=\"modal\">" . JText::_($cf['name']) . "</a>";
			}
			else
			{
				$fname = "<span id=\"vapcf" . $cf['id'] . "\">" . JText::_($cf['name']) . "</span>";
			}
			
			$_val = "";
			if (count($cf_data) > 0 && !empty($cf_data[$cf['name']]))
			{
				/**
				 * Prevent XSS attacks by escaping submitted data.
				 *
				 * @since 1.6.3
				 */
				$_val = $this->escape($cf_data[$cf['name']]);
			}

			if (VAPCustomFields::isSeparator($cf))
			{
				echo '<div class="control-group"><h3>' . $fname . '</h3>';
			}
			else
			{
				echo $vik->openControl($fname . ':');
			}
			
			if (VAPCustomFields::isInputText($cf))
			{
				$input_class = '';
				
				if (VAPCustomFields::isEmail($cf))
				{
					$input_class = 'vapemailfield';
				}
				else if (VAPCustomFields::isPhoneNumber($cf))
				{
					$input_class = 'vapphonefield';
				}
				
				$text_width = '';
				
				if (VAPCustomFields::isPhoneNumber($cf))
				{
					$text_width = 'width: 168px !important';
					echo '<select name="vapcf'.$cf['id'].'_prfx" class="vap-phones-select">';
					foreach ($this->countries as $i => $ctry) {
						$suffix = "";
						if (($i != 0 && $this->countries[$i-1]['phone_prefix'] == $ctry['phone_prefix']) 
							|| ($i != count($this->countries) - 1 && $this->countries[$i+1]['phone_prefix'] == $ctry['phone_prefix'])) {
							$suffix = ' : '.$ctry['country_2_code'];
						}
						echo '<option value="'.$ctry['id']."_".$ctry['country_2_code'].'" title="'.trim($ctry['country_name']).'" '.($sel['purchaser_country'] == $ctry['country_2_code'] ? 'selected="selected"' : '').'>'.$ctry['phone_prefix'].$suffix.'</option>';
					}
					echo '</select>';
				}    
				?>
				<input type="text" name="vapcf<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" class="<?php echo $input_class; ?>" size="40" style="<?php echo $text_width; ?>" data-name="<?php echo $cf['name']; ?>"/>
					
			<?php } else if (VAPCustomFields::isTextArea($cf)) { ?>
				
				<textarea name="vapcf<?php echo $cf['id']; ?>" rows="5" cols="30" class="vaptextarea"><?php echo $_val; ?></textarea>
			
			<?php } else if (VAPCustomFields::isInputNumber($cf)) { ?>

				<input type="number" name="vapcf<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" />

			<?php } else if (VAPCustomFields::isCalendar($cf)) { ?>
				
				<?php echo $vik->calendar($_val, 'vapcf'.$cf['id'], 'vapcf'.$cf['id'].'date'); ?>
			
			<?php } else if (VAPCustomFields::isSelect($cf)) { ?>

				<?php
				$choose = array_filter(explode(";;__;;", $cf['choose']));
				$values = $cf['multiple'] ? json_decode($_val ? $_val : '[]') : array($_val);
				?>

				<select 
					name="vapcf<?php echo $cf['id'] . ($cf['multiple'] ? '[]' : ''); ?>"
					class="vap-cf-select"
					<?php echo $cf['multiple'] ? 'multiple' : ''; ?>
				>

					<?php foreach ($choose as $aw) { ?>

						<option value="<?php echo $aw; ?>" <?php echo (in_array($aw, $values) ? 'selected="selected"' : ''); ?>><?php echo $aw; ?></option>

					<?php } ?>

				</select>

			<?php } else if (VAPCustomFields::isSeparator($cf)) { ?>
				
			<?php } else if (VAPCustomFields::isInputFile($cf)) { ?>
				
				<?php if (!empty($uploaded_files[JText::_($cf['name'])]) && file_exists(VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . $uploaded_files[JText::_($cf['name'])])) { ?>
					<a href="<?php echo VAPCUSTOMERS_UPLOADS_URI . $uploaded_files[JText::_($cf['name'])]; ?>" target="_blank">
						<i class="fa fa-cloud-download big" style="font-size: 26px;"></i>
					</a>
				<?php } ?>

			<?php } else if (VAPCustomFields::isCheckbox($cf)){ ?>

				<input type="checkbox" name="vapcf<?php echo $cf['id']; ?>" value="1" <?php echo ($_val ? 'checked="checked"' : ''); ?> />
			
			<?php } ?>
			
			<?php
			if (VAPCustomFields::isSeparator($cf))
			{
				echo '</div>';
			}
			else
			{
				echo $vik->closeControl();
			}
			?>
			
		<?php } ?>
	
	<?php echo $vik->closeEmptyFieldset(); ?>
</div>
