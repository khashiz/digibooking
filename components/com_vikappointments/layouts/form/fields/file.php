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

$label = $displayData['label'];
$value = $displayData['value'];
$cf    = $displayData['field'];

if (empty($cf['choose']))
{
	$cf['choose'] = '*';
}

$file_exists = !empty($value) && file_exists(VAPCUSTOMERS_UPLOADS . DIRECTORY_SEPARATOR . $value);

JFactory::getDocument()->addScriptDeclaration(
<<<JS
function vapCustomFieldUploadAction(id) {
	jQuery('#vapcf'+id+'filediv').css('display', 'none');
	jQuery('#vapcf'+id+'upldiv').fadeIn('fast');
}
JS
);

?>

<div>

	<span class="cf-label">

		<?php if ($cf['required']) { ?>

			<span class="vaprequired"><sup>*</sup></span>

		<?php } ?>

		<span id="vapcf<?php echo $cf['id']; ?>"><?php echo $label; ?></span>

	</span>
	
	<span class="cf-value">

		<div id="vapcf<?php echo $cf['id']; ?>upldiv" style="<?php echo ($file_exists ? 'display: none;' : ''); ?>">

			<input type="file" name="vapcf<?php echo $cf['id']; ?>" style="display: inline-block;" />

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

			<div id="vapcf<?php echo $cf['id']; ?>filediv">
				<span>
					<?php echo JText::sprintf('VAPCFFILEUPLOADED', substr($value, strrpos($value, '.'))); ?>&nbsp;
				</span>

				<span>
					<a href="javascript: void(0);" onclick="vapCustomFieldUploadAction(<?php echo $cf['id']; ?>);">
						<img src="<?php echo VAPASSETS_URI . 'css/images/delete.png'; ?>" />
					</a>
				</span>
			</div>
			
		<?php } ?>

		<input type="hidden" name="old_vapcf<?php echo $cf['id']; ?>" value="<?php echo ($file_exists ? $this->escape($value) : ''); ?>" />

	</span>

</div>
