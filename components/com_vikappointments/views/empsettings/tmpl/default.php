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

$auth       = $this->auth;
$employee   = $auth->getEmployee();

$settings = $this->settings;

$vik = UIApplication::getInstance();

$itemid = JFactory::getApplication()->input->getInt('Itemid');

?>

<?php
/**
 * The employees area toolbar is displayed from the layout below:
 * /components/com_vikappointments/layouts/emparea/toolbar.php
 * 
 * If you need to change something from this layout, just create
 * an override of this layout by following the instructions below:
 * - open the back-end of your Joomla
 * - visit the Extensions > Templates > Templates page
 * - edit the active template
 * - access the "Create Overrides" tab
 * - select Layouts > com_vikappointments > emparea
 * - start editing the toolbar.php file on your template to create your own layout
 *
 * @since 1.6
 */
echo JLayoutHelper::render('emparea.toolbar', array('active' => false));
?>

<div class="vapeditempheaderdiv">
	<div class="vapeditemptitlediv">
		<h2><?php echo JText::_('VAPEMPSETTINGSTITLE'); ?></h2>
	</div>
	
	<div class="vapeditempactionsdiv">
		<?php if ($auth->manage()) { ?>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveSettings(0);" id="vapempbtnsave" class="vap-btn blue employee"><?php echo JText::_('VAPSAVE'); ?></button>
			</div>
			<div class="vapempbtn">
				<button type="button" onClick="vapSaveSettings(1);" id="vapempbtnsaveclose" class="vap-btn blue employee"><?php echo JText::_('VAPSAVEANDCLOSE'); ?></button>
			</div>
		<?php } ?>
		<div class="vapempbtn">
			<button type="button" onClick="vapCloseSettings();" id="vapempbtnclose" class="vap-btn blue employee"><?php echo JText::_('VAPCLOSE'); ?></button>
		</div>
	</div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&view=empsettings'); ?>" method="post" name="empareaForm" id="empareaForm">
	
	<?php if (VikAppointments::isMultipleTimezones()) { ?>

		<fieldset class="adminform">
			<legend class="adminlegend"><?php echo JText::_('VAPEMPSETTINGSGLOBAL'); ?></legend>
			<table class="admintable table" cellspacing="1">
				
				<!-- TIMEZONE - Dropdown -->
				<?php
				$zones = array();
				foreach (timezone_identifiers_list() as $zone)
				{
					$parts = explode('/', $zone);

					$continent  = isset($parts[0]) ? $parts[0] : '';
					$city 		= (isset($parts[1]) ? $parts[1] : $continent) . (isset($parts[2]) ? '/' . $parts[2] : '');
					$city 		= ucwords(str_replace('_', ' ', $city));

					if (!isset($zones[$continent]))
					{
						$zones[$continent] = array();
					}

					$zones[$continent][] = $vik->initOptionElement($zone, $city, $settings['timezone'] == $zone);
				}

				$options = array();
				$options[] = $vik->initOptionElement('', '', false);
				foreach ($zones as $continent => $list)
				{
					$options[] = $vik->getDropdownGroup($continent);
					foreach ($list as $opt)
					{
						$options[] = $opt;
					}
				}
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPEMPSETTING13"); ?></b> </td>
					<td>
						<label for="vap-timezone-sel" class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING13"); ?></label>
						<?php echo $vik->dropdown('timezone', $options, 'vap-timezone-sel'); ?>
					</td>
				</tr>
				
			</table>
		</fieldset>

	<?php } ?>
	
	<fieldset class="adminform">
		<legend class="adminlegend"><?php echo JText::_('VAPEMPSETTINGSUPCOMING'); ?></legend>
		<table class="admintable table" cellspacing="1">
			
			<!-- LIST LIMIT - Dropdown -->
			<?php
			$elements = array();
			foreach (array(5, 10, 15, 20, 50) as $limit)
			{
				$elements[] = $vik->initOptionElement($limit, $limit, $limit == $settings['listlimit']);
			}
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPEMPSETTING1"); ?></b> </td>
				<td>
					<label for="vap-listlimit-sel" class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING1"); ?></label>
					<?php echo $vik->dropdown('listlimit', $elements, 'vap-listlimit-sel', 'vap-dropdown'); ?>
				</td>
			</tr>
			
			<!-- LIST ORDERING - Dropdown -->
			<?php
			$elements = array(
				$vik->initOptionElement('ASC', JText::_('VAPEMPSETTING6'), $settings['listordering'] == 'ASC'),
				$vik->initOptionElement('DESC', JText::_('VAPEMPSETTING7'), $settings['listordering'] == 'DESC'),
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPEMPSETTING5"); ?></b> </td>
				<td>
					<label for="vap-listord-sel" class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING5"); ?></label>
					<?php echo $vik->dropdown('listordering', $elements, 'vap-listord-sel', 'vap-dropdown'); ?>
				</td>
			</tr>
			
			<!-- LIST POSITION - Dropdown -->
			<?php
			$elements = array(
				$vik->initOptionElement(1, JText::_('VAPEMPSETTING3'), $settings['listposition'] == 1),
				$vik->initOptionElement(2, JText::_('VAPEMPSETTING4'), $settings['listposition'] == 2),
			);
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPEMPSETTING2"); ?></b> </td>
				<td>
					<label for="vap-listpos-sel" class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING2"); ?></label>
					<?php echo $vik->dropdown('listposition', $elements, 'vap-listpos-sel', 'vap-dropdown'); ?>
				</td>
			</tr>
			
		</table>
	</fieldset>
	
	<fieldset class="adminform">
		<legend class="adminlegend"><?php echo JText::_('VAPEMPSETTINGSCALENDARS'); ?></legend>
		<table class="admintable table" cellspacing="1">
			
			<!-- NUM OF CALS - Dropdown -->
			<?php
			$elements = array();
			foreach (array(1, 3, 6, 9, 12) as $cals)
			{
				$elements[] = $vik->initOptionElement($cals, $cals, $cals == $settings['numcals']);
			}
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPEMPSETTING8"); ?></b> </td>
				<td>
					<label for="vap-numcals-sel" class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING8"); ?></label>
					<?php echo $vik->dropdown('numcals', $elements, 'vap-numcals-sel', 'vap-dropdown'); ?>
				</td>
			</tr>
			
			<!-- FIRST MONTH - Dropdown -->
			<?php
			$elements = array();
			$elements[] = $vik->initOptionElement('', '', $settings['firstmonth'] == -1);

			for ($i = 1; $i <= 12; $i++)
			{
				$elements[] = $vik->initOptionElement($i, JText::_('VAPMONTH'.$i), $i == $settings['firstmonth']);
			}
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPEMPSETTING9"); ?></b> </td>
				<td>
					<label for="vap-firstmonth-sel" class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING9"); ?></label>
					<?php echo $vik->dropdown('firstmonth', $elements, 'vap-firstmonth-sel'); ?>
				</td>
			</tr>
			
		</table>
	</fieldset>
	
	<fieldset class="adminform">
		<legend class="adminlegend"><?php echo JText::_('VAPEMPSETTINGSAPPSYNC'); ?></legend>
		<table class="admintable table" cellspacing="1">
			
			<!-- ADMIN SYNC URL - Response -->
			<?php
			$sync_href = JUri::root()."index.php?option=com_vikappointments&task=appsync&employee={$employee['id']}&key={$settings['synckey']}";
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPEMPSETTING11"); ?></b> </td>
				<td>
					<label for="syncurl" class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING11"); ?></label>
					<a href="<?php echo $sync_href; ?>" id="syncurl" data-original-title="<?php echo JText::_('VAPICSURL'); ?>" class="hasTooltip" target="_blank">
						<?php echo $sync_href; ?>
					</a>
				</td>
			</tr>
			
			<!-- SYNC PASSWORD - Text -->
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPEMPSETTING12"); ?></b> </td>
				<td>
					<label for="synckey" class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING12"); ?></label>
					<input type="text" name="synckey" id="synckey" value="<?php echo $settings['synckey']; ?>" size="40" maxlength="32" />
				</td>
			</tr>
			
		</table>
	</fieldset>

	<fieldset class="adminform">
		<legend class="adminlegend"><?php echo JText::_('VAPEMPSETTINGSZIPRESTR'); ?></legend>
		<table class="admintable table" cellspacing="1">

			<!-- ZIP FIELD - Dropdown -->
			<?php
			$elements = array();
			$elements[] = $vik->initOptionElement('', '', $settings['zip_field_id'] == -1);
			foreach ($this->customFields as $cf)
			{
				$elements[] = $vik->initOptionElement($cf['id'], JText::_($cf['name']), $cf['id'] == $settings['zip_field_id']);
			}
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPEMPSETTING18"); ?></b> </td>
				<td>
					<label for="vap-zip-select" class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING18"); ?></label>
					<?php echo $vik->dropdown('zip_field_id', $elements, 'vap-zip-select'); ?>
				</td>
			</tr>
			
			<!-- ZIP CODES - Form -->
			<?php
			$zip_codes = empty($settings['zipcodes']) ? array() : json_decode($settings['zipcodes'], true);
			?>
			<tr>
				<td width="200" class="adminparamcol" style="vertical-align: top;"> <b><?php echo JText::_("VAPEMPSETTING14"); ?></b> </td>
				<td>
					<label class="label-mobile-only"><?php echo JText::_("VAPEMPSETTING14"); ?></label>
					<div id="vapzipcodescont">
						<?php foreach ($zip_codes as $i => $zip) { ?>
							<div id="vapzcrow<?php echo $i; ?>" style="margin-bottom: 5px;">
								<span>
									<input type="text" name="zip_code_from[]" style="vertical-align: middle;" value="<?php echo $zip['from']; ?>" size="10" placeholder="<?php echo JText::_('VAPEMPSETTING15'); ?>"/>
									&nbsp;-&nbsp;
									<input type="text" name="zip_code_to[]" style="vertical-align: middle;" value="<?php echo $zip['to']; ?>" size="10" placeholder="<?php echo JText::_('VAPEMPSETTING16'); ?>"/>
									<a href="javascript: void(0);" onClick="vapRemoveZipCode(<?php echo $i; ?>)"><i class="fa fa-times big" style="margin-left: 5px;"></i></a>
								</span>
							</div>
						<?php } ?>
					</div>
					<div>
						<button type="button" class="vap-btn blue" onClick="vapPutZipCode();"><?php echo JText::_('VAPEMPSETTING17'); ?></button>
					</div>
				</td>
			</tr>
			
		</table>
	</fieldset>
	
	<input type="hidden" name="return" value="0" id="vaphiddenreturn" /> 

	<input type="hidden" name="task" value="empsettings.save" />
	<input type="hidden" name="option" value="com_vikappointments" />
	
	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
</form>

<?php
JText::script('VAPEMPSETTING10');
JText::script('VAPEMPSETTING19');
?>

<script>
	
	function vapCloseSettings() {
		document.location.href = '<?php echo JRoute::_('index.php?option=com_vikappointments&view=emplogin&Itemid=' . $itemid, false); ?>';
	}
	
	<?php if ($auth->manage()) { ?>

		function vapSaveSettings(close) {
			
			if(close) {
				jQuery('#vaphiddenreturn').val('1');
			}
			
			document.empareaForm.submit();
			
		}

	<?php } ?>
	
	jQuery(document).ready(function() {

		jQuery('#synckey').on('keyup', function(e){
			updateSyncURL();
		});

		jQuery('#vap-firstmonth-sel').select2({
			placeholder: Joomla.JText._('VAPEMPSETTING10'),
			allowClear: true,
			width: 300
		});

		jQuery('#vap-zip-select').select2({
			placeholder: Joomla.JText._('VAPEMPSETTING19'),
			allowClear: true,
			width: 300
		});

		jQuery('#vap-timezone-sel').select2({
			placeholder: Joomla.JText._('VAPEMPSETTING19'),
			allowClear: true,
			width: 300
		});
		
		jQuery('.vap-dropdown').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300
		});

	});

	function updateSyncURL() {
		var key = jQuery('#synckey').val();
		var url = jQuery('#syncurl').html();
		var last_part = url.lastIndexOf('=');
		url = url.substr(0, last_part+1)+key;
		jQuery('#syncurl').attr('href', url);
		jQuery('#syncurl').html(url);
	}

	var _ZIP_CONT_ = <?php echo $i; ?>;

	function vapPutZipCode() {
		jQuery('#vapzipcodescont').append('<div id="vapzcrow'+_ZIP_CONT_+'" style="margin-bottom: 5px;">\n'+
			'<span>\n'+
				'<input type="text" name="zip_code_from[]" onBlur="vapFillNextZip(this);" style="vertical-align: middle;" value="" size="10" placeholder="<?php echo addslashes(JText::_('VAPEMPSETTING15')); ?>"/>\n'+
				'&nbsp;-&nbsp;\n'+
				'<input type="text" name="zip_code_to[]" style="vertical-align: middle;" value="" size="10" placeholder="<?php echo addslashes(JText::_('VAPEMPSETTING16')); ?>"/>\n'+
				'<a href="javascript: void(0);" onClick="vapRemoveZipCode('+_ZIP_CONT_+')"><i class="fa fa-times big" style="margin-left: 5px;"></i></a>\n'+
			'</span>\n'+
		'</div>');
		_ZIP_CONT_++;
	}

	function vapRemoveZipCode(index) {
		jQuery('#vapzcrow'+index).remove();
	}

	function vapFillNextZip(from) {
		var to = jQuery(from).next()
		if (jQuery(from).val().length > 0 && jQuery(to).val().length == 0) {
			jQuery(to).val(jQuery(from).val());
		}
	}

</script>
