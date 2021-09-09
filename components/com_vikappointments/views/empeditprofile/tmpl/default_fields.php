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

$sel 		= $this->auth->getEmployee();
$cfields 	= $this->fields;

$vik = UIApplication::getInstance();

?>

<?php echo $vik->openEmptyFieldset(); ?>

	<?php
	foreach ($cfields as $cf)
	{
		$settings = (array) json_decode($cf['choose'], true);

		if (!empty($cf['poplink']))
		{
			$fname = "<a href=\"" . $cf['poplink'] . "\" id=\"vapcf" . $cf['id'] . "\" rel=\"{handler: 'iframe', size: {x: 750, y: 600}}\" target=\"_blank\" class=\"modal\">" . JText::_($cf['name']) . "</a>";
		}
		else
		{
			if (VAPCustomFields::isTextArea($cf) && !empty($settings['editor']))
			{
				// do not use the ID equals to the field name because TinyMCE uses
				// the name as ID, which would cause a conflict
				$fname = "<span id=\"vapcf" . $cf['id'] . "-editor\">" . JText::_($cf['name']) . "</span>";
			}
			else
			{
				$fname = "<span id=\"vapcf" . $cf['id'] . "\">" . JText::_($cf['name']) . "</span>";
			}
		}

		$formname = 'field_' . $cf['formname'];
		
		$_val = "";
		if (!empty($sel[$formname]))
		{
			$_val = $sel[$formname];
		}

		if (VAPCustomFields::isSeparator($cf))
		{
			echo '<div class="control-group"><h3>' . $fname . '</h3>';
		}
		else
		{
			echo $vik->openControl($fname . ':');
		}

		$required = $cf['required'] ? 'required' : '';

		// field rendering
		
		if (VAPCustomFields::isInputText($cf))
		{
			?>
			
			<input type="text" name="vapcf<?php echo $cf['id']; ?>" value="<?php echo $this->escape($_val); ?>" class="<?php echo $required; ?>" size="40" data-name="<?php echo $cf['name']; ?>" />
				
			<?php
		}
		else if (VAPCustomFields::isTextArea($cf))
		{		
			if (empty($settings['editor']))
			{
				?>
				<textarea name="vapcf<?php echo $cf['id']; ?>" rows="5" cols="30" class="vaptextarea <?php echo $required; ?>"><?php echo $_val; ?></textarea>
				<?php
			}
			else
			{
				/**
				 * Display visual editor according to the settings
				 * of the custom field.
				 *
				 * @since 1.6.3
				 */
				echo JFactory::getEditor()
					->display('vapcf' . $cf['id'], $_val, 600, 400, 70, 20, false);
			}
		}
		else if (VAPCustomFields::isInputNumber($cf))
		{
			?>

			<input type="number" name="vapcf<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" class="<?php echo $required; ?>" />

			<?php
		}
		else if (VAPCustomFields::isCalendar($cf))
		{
			?>

			<input type="text" name="vapcf<?php echo $cf['id']; ?>" id="vapcfinput<?php echo $cf['id']; ?>" value="<?php echo $_val; ?>" size="25" class="vapinput calendar <?php echo $required; ?>" />
		
			<?php
		}
		else if (VAPCustomFields::isSelect($cf))
		{
			$choose = array_filter(explode(";;__;;", $cf['choose']));
			$_vals = $cf['multiple'] ? json_decode($_val ? $_val : '[]') : array($_val);
			?>

			<select 
				name="vapcf<?php echo $cf['id'] . ($cf['multiple'] ? '[]' : ''); ?>"
				class="vap-cf-select <?php echo $required; ?>"
				<?php echo $cf['multiple'] ? 'multiple' : ''; ?>
			>

				<?php
				foreach ($choose as $aw)
				{
					?>
					<option value="<?php echo $aw; ?>" <?php echo (in_array($aw, $_vals) ? 'selected="selected"' : ''); ?>><?php echo $aw; ?></option>
					<?php
				}
				?>

			</select>

			<?php
		}
		else if (VAPCustomFields::isSeparator($cf))
		{
	
		}
		else if (VAPCustomFields::isInputFile($cf))
		{ 
			$file_exists = !empty($_val) && file_exists(VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . $_val);
			?>

			<div id="vapcf<?php echo $cf['id']; ?>upldiv" style="<?php echo ($file_exists ? 'display: none;' : ''); ?>">

				<input type="file" name="vapcf<?php echo $cf['id']; ?>" style="display: inline-block;" class="<?php echo $file_exists ? '' : $required; ?>" />

				<?php
				if ($cf['choose'])
				{
					?>
					<span style="font-size: smaller;font-style: italic;"><?php echo '(' . $cf['choose'] . ')'; ?></span>
					<?php
				}
				?>

			</div>

			<?php if ($file_exists) { ?>

				<div id="vapcf<?php echo $cf['id']; ?>filediv" style="height:32px;line-height:32px;">
					<span>
						<a href="<?php echo VAPCUSTOMERS_UPLOADS_URI . $_val; ?>" target="_blank">
							<?php echo JText::sprintf('VAPCFFILEUPLOADED', substr($_val, strrpos($_val, '.'))); ?>
						</a>
					</span>

					<span>
						<a href="javascript: void(0);" onclick="vapCustomFieldUploadAction(<?php echo $cf['id']; ?>);">
							<i class="fa fa-times"></i>
						</a>
					</span>
				</div>
				
			<?php } ?>

			<input type="hidden" name="old_vapcf<?php echo $cf['id']; ?>" value="<?php echo ($file_exists ? $_val : ''); ?>" />

		<?php
		}
		else if (VAPCustomFields::isCheckbox($cf))
		{
			?>

			<input type="checkbox" name="vapcf<?php echo $cf['id']; ?>" value="1" <?php echo ($_val ? 'checked="checked"' : ''); ?> class="<?php echo $required; ?>" />
		
			<?php
		}

		// end field rendering

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

<script>

	jQuery(document).ready(function() {

		jQuery('.vap-cf-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300,
		});

	});

	function vapCustomFieldUploadAction(id) {
		jQuery('#vapcf'+id+'filediv').css('display', 'none');
		jQuery('#vapcf'+id+'upldiv').fadeIn('fast');
	}

	jQuery(function(){

		var sel_format 	 = "<?php echo UIFactory::getConfig()->get('dateformat'); ?>";
		var df_separator = sel_format[1];

		sel_format = sel_format.replace(new RegExp('\\'+df_separator, 'g'), "");

		if (sel_format == "Ymd") {

			Date.prototype.format = "yy"+df_separator+"mm"+df_separator+"dd";

		} else if (sel_format == "mdY") {

			Date.prototype.format = "mm"+df_separator+"dd"+df_separator+"yy";

		} else {

			Date.prototype.format = "dd"+df_separator+"mm"+df_separator+"yy";

		}

		jQuery(document).ready(function() {
			jQuery(".vapinput.calendar").datepicker({
				dateFormat: new Date().format
			});
		});
		
	});

</script>
