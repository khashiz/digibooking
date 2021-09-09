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

$rows 		= $this->handler->getRecords();
$columns 	= $this->handler->getColumns();

$vik = UIApplication::getInstance();

?>

<form action="index.php?option=com_vikappointments" method="post" name="adminForm" id="adminForm">

	<div class="span12 vap-recerror-box" style="display: none;margin-top: 10px;" id="custom-error">
		<span><?php echo JText::_('VAPMANAGECUSTOMERERR3'); ?></span>
	</div>

	<div class="span12" style="margin-left: 0;">
		<?php echo $vik->openEmptyFieldset('scrollable-hor'); ?>

			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
				<?php echo $vik->openTableHead(); ?>
					<tr>
						<?php for ($i = 0, $n = count($rows[0]); $i < $n; $i++) { ?>
							<th class="<?php echo $vik->getAdminThClass(); ?>" width="<?php echo floor(100 / $n); ?>%" style="text-align: center;">
								
								<select name="column[<?php echo $rows[0][$i]; ?>]" class="vap-column-sel" data-col-index="<?php echo $i; ?>">
									<option></option>
									<?php
									foreach ($columns as $col)
									{
										$req = $col->required ? ' *' : '';

										?><option value="<?php echo $col->name; ?>" data-required="<?php echo $col->required; ?>">
											<?php echo $col->label . $req; ?>
										</option><?php
									}
									?>
								</select>

								<br /><br /><?php echo ucwords($rows[0][$i]); ?>

							</th>
						<?php } ?>
					</tr>
				<?php echo $vik->closeTableHead(); ?>

				<?php
				for ($i = 1, $n = count($rows); $i < $n; $i++) { ?>
					<tr class="row<?php echo ($i % 2); ?>">

						<?php for ($j = 0; $j < count($rows[$i]); $j++) { ?>
							
							<td style="text-align: center;" data-col-index="<?php echo $j; ?>"><?php echo $rows[$i][$j]; ?></td>

						<?php } ?>
						
					</tr>
				<?php } ?>

			</table>

			<div style="text-align: center;">
				<small><?php echo JText::sprintf('VAPIMPORTTABLEJOOMLA3810TER', count($rows) - 1); ?></small>
			</div>

		<?php echo $vik->closeEmptyFieldset(); ?>
	</div>

	<?php foreach ($this->args as $k => $v) { ?>

		<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>" />
		<input type="hidden" name="import_args[<?php echo $k; ?>]" value="<?php echo $v; ?>" />

	<?php } ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="manageimport" />
	<input type="hidden" name="import_type" value="<?php echo $this->type; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script>

	var COLUMNS_MAP = <?php echo json_encode($columns); ?>;

	jQuery(document).ready(function() {

		jQuery('.vap-column-sel').select2({
			placeholder: '<?php echo addslashes(JText::_('VAPPAYMENTPOSOPT1')); ?>'.toLowerCase(),
			allowClear: true,
			width: '100%'
		});

		jQuery('.vap-column-sel').on('change', function() {

			var index = jQuery(this).data('col-index');

			var column = null;

			if (COLUMNS_MAP.hasOwnProperty(jQuery(this).val())) {
				column = COLUMNS_MAP[jQuery(this).val()];
			}

			jQuery('td[data-col-index="'+index+'"]').each(function() {

				if (jQuery(this).data('value') === undefined)
				{
					jQuery(this).data('value', jQuery(this).html());
				}

				var value = jQuery(this).data('value');

				var style = '';
				var title = '';

				if (column != null)
				{
					if (value.length == 0)
					{
						value = column.default;
						title = 'Default';
						style += 'color:#2b3ce3;';
					}

					if (column.options.hasOwnProperty(value))
					{
						value = column.options[value];
						style += 'font-style:italic;';
					}

					if (style.length || title.length)
					{
						value = '<span style="' + style + '" title="' + title + '">' + value + '</span>';
					}
				}

				jQuery(this).html(value);

				if (title)
				{
					jQuery(this).find('span').tooltip();
				}

			});

			var old = jQuery(this).data('old-value');
			var val = jQuery(this).val();

			// disable this option on all the other select

			if (val.length)
			{
				jQuery('.vap-column-sel')
					.not(this)
					.find('option[value="' + val + '"]')
					.attr('disabled', true);
			}

			// enable old option on all the other select

			jQuery('.vap-column-sel')
					.not(this)
					.find('option[value="' + old + '"]')
					.attr('disabled', false);

			jQuery(this).data('old-value', val);
		});

	});

	// validate

	var validator = new VikFormValidator('#adminForm');

	Joomla.submitbutton = function(task) {
		if (task.indexOf('save') !== -1) {
			if (validator.validate(customValidation)) {
				Joomla.submitform(task, document.adminForm);	
			}
		} else {
			Joomla.submitform(task, document.adminForm);
		}
	}

	function customValidation() {

		var ok = true;

		jQuery('select.vap-column-sel').first().find('option[data-required="1"]').each(function() {

			var val = jQuery(this).val();

			if (!hasRequiredValue(val)) {
				ok = false;
				return false;
			}

		});

		if (!ok) {
			jQuery('#custom-error').show();
		} else {
			jQuery('#custom-error').hide();
		}

		return ok;
	}

	function hasRequiredValue(val) {

		var has = false;

		jQuery('.vap-column-sel').each(function() {

			if (jQuery(this).val() == val) {
				has = true;
				return false;
			}

		});

		return has;
	}

</script>
