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

$row = $this->row;

$vik = UIApplication::getInstance();

?>

<form action="index.php" method="POST" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 48px;">
		<div class="btn-group pull-left">
			<button type="submit" class="btn btn-success" style="width: 120px;">
				<i class="icon-apply icon-white"></i>&nbsp;<?php echo JText::_('VAPSAVE'); ?>
			</button>
		</div>

		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="toggleLangSection(this);">
				<i class="icon-eye"></i>&nbsp;<?php echo JText::_('VAPSEEORIGINAL'); ?>
			</button>
		</div>
	</div>
	
	<div id="translation">
		<?php echo $vik->openEmptyFieldset(); ?>

			<!-- NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF1').':'); ?>
				<?php if ($this->default['type'] != 'separator') { ?>
					<input type="text" name="name" value="<?php echo (!empty($row['name']) ? $row['name'] : ''); ?>" size="40" />
				<?php } else { ?>
					<textarea name="name" style="width: 80%;height: 200px;"><?php echo (!empty($row['name']) ? $row['name'] : ''); ?></textarea>
				<?php } ?>
			<?php echo $vik->closeControl(); ?>

			<?php
			if ($this->default['type'] == 'select')
			{
				echo $vik->openControl(JText::_('VAPCUSTOMFTYPEOPTION4').':');
				
				$options = array_filter(explode(';;__;;', $this->default['choose']));

				if (!empty($row['choose']))
				{
					$lang_options = explode(';;__;;', $row['choose']);
				}
				else
				{
					$lang_options = array();
				}

				foreach ($options as $i => $opt)
				{
					?>
					<div style="margin-bottom: 10px;">
						<input type="text" name="select_choose[]" value="<?php echo (!empty($lang_options[$i]) ? $lang_options[$i] : ''); ?>" size="40" />
					</div>
					<?php
				}
				echo $vik->closeControl();
			}
			else if ($this->default['type'] == 'checkbox')
			{
				echo $vik->openControl(JText::_('VAPMANAGECUSTOMF5').':');
				?><input type="text" name="poplink" value="<?php echo (!empty($row['poplink']) ? $row['poplink'] : ''); ?>" size="40" /><?php
				echo $vik->closeControl();
			}
			else if ($this->default['type'] == 'separator')
			{
				echo $vik->openControl(JText::_('VAPSUFFIXCLASS').':');
				?><input type="text" name="sep_suffix" value="<?php echo (!empty($row['choose']) ? $row['choose'] : ''); ?>" size="40" /><?php
				echo $vik->closeControl();
			}
			else if (VAPCustomFields::isPhoneNumber($this->default))
			{
				echo $vik->openControl(JText::_('VAPMANAGECUSTOMF9').':');

				$options = array();
				$options[] = JHtml::_('select.option', '', '');

				foreach ($this->countries as $country)
				{
					$options[] = JHtml::_('select.option', $country['country_2_code'], $country['country_name']);
				}

				?>
				<select name="country_code" id="vap-countrylang-sel">
					<?php echo JHtml::_('select.options', $options, 'value', 'text', (!empty($row['choose']) ? $row['choose'] : '')); ?>
				</select>
				<?php
				echo $vik->closeControl();
			}
			?>

		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>

	<div style="display: none;" id="original">
		<?php echo $vik->openEmptyFieldset(); ?>

			<!-- NAME - Text -->
			<?php echo $vik->openControl(JText::_('VAPMANAGECUSTOMF1').':'); ?>
				<input type="text" value="<?php echo (!empty($this->default['name']) ? $this->default['name'] : ''); ?>" size="40" readonly />
			<?php echo $vik->closeControl(); ?>

			<?php
			if ($this->default['type'] == 'select')
			{
				echo $vik->openControl(JText::_('VAPCUSTOMFTYPEOPTION4').':');
				
				$options = array_filter(explode(';;__;;', $this->default['choose']));

				foreach ($options as $opt)
				{
					?>
					<div style="margin-bottom: 10px;">
						<input type="text" value="<?php echo $opt; ?>" size="40" readonly />
					</div>
					<?php
				}
				echo $vik->closeControl();
			}
			else if ($this->default['type'] == 'checkbox')
			{
				echo $vik->openControl(JText::_('VAPMANAGECUSTOMF5').':');
				?><input type="text" value="<?php echo (!empty($this->default['poplink']) ? $this->default['poplink'] : ''); ?>" size="40" readonly /><?php
				echo $vik->closeControl();
			}
			else if ($this->default['type'] == 'separator')
			{
				echo $vik->openControl(JText::_('VAPSUFFIXCLASS').':');
				?><input type="text" value="<?php echo (!empty($this->default['choose']) ? $this->default['choose'] : ''); ?>" size="40" readonly /<?php
				echo $vik->closeControl();
			}
			else if (VAPCustomFields::isPhoneNumber($this->default))
			{
				$country = $this->default['choose'];

				if (!empty($country))
				{
					foreach ($this->countries as $c)
					{
						if ($c['country_2_code'] == $country)
						{
							$country = $c['country_name'];
							break;
						}
					}
				}

				echo $vik->openControl(JText::_('VAPMANAGECUSTOMF9').':');
				?><input type="text" value="<?php echo $country; ?>" size="40" readonly /><?php
				echo $vik->closeControl();
			}
			?>

		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>
		
	<input type="hidden" name="id" value="<?php echo (!empty($row['id']) ? $row['id'] : -1); ?>" />
	<input type="hidden" name="id_customf" value="<?php echo $this->idCustomField; ?>" />
	<input type="hidden" name="tag" value="<?php echo $this->tag; ?>" />
	<input type="hidden" name="task" value="saveLangCustomf" />
	<input type="hidden" name="option" value="com_vikappointments" />
</form>

<script>

	jQuery(document).ready(function() {

		jQuery('#vap-countrylang-sel').select2({
			placeholder: '--',
			allowClear: true,
			width: 300
		});

	});

	function toggleLangSection(button) {

		if (jQuery(button).hasClass('active'))
		{
			jQuery('#translation').show();
			jQuery('#original').hide();
			jQuery(button).removeClass('active').find('i').removeClass('icon-eye-close').addClass('icon-eye');
		}
		else
		{
			jQuery('#original').show();
			jQuery('#translation').hide();
			jQuery(button).addClass('active').find('i').removeClass('icon-eye').addClass('icon-eye-close');
		}

	}

</script>
