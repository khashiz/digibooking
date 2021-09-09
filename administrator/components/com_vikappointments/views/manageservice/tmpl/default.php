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

JHtml::_('behavior.modal');

$sel = $this->service;

$options 	= $this->options;
$employees 	= $this->employees;
$groups 	= $this->groups;

$serviceOptions 	= $this->serviceOptions;
$serviceEmployees 	= $this->serviceEmployees;
ArasJoomlaVikApp::datePicker();

$vik = UIApplication::getInstance();

$config = UIFactory::getConfig();

$editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

$img_url = '';
if (!empty($sel['image']))
{
	$img_url = VAPMEDIA_URI . $sel['image'];
}

$media_prop = array(
	'name' 		=> 'media',
	'id' 		=> 'vapmediaselect',
	'onChange' 	=> 'imageSelectChanged();',
);

$_ASSETS = VAPASSETS_ADMIN_URI . 'images/';

$interval = VikAppointments::getMinuteIntervals();

// MEDIA

$is_media_prop = VikAppointments::isMediaPropertiesConfigured();

$def_val = array(
	'ORI_W' => VikAppointments::getOriginalWidthResize(),
	'ORI_H' => VikAppointments::getOriginalHeightResize(),
	'SML_W' => VikAppointments::getSmallWidthResize(),
	'SML_H' => VikAppointments::getSmallHeightResize(),
	'ISRES' => VikAppointments::isImageResize()
);

$date_format = $config->get('dateformat');

$nowdf = $vik->jdateFormat($date_format);

if ($sel['start_publishing'] > 0)
{
	$sel['start_publishing'] 	= ArasJoomlaVikApp::jdate($date_format,$sel['start_publishing']);
	$sel['end_publishing'] 		= ArasJoomlaVikApp::jdate($date_format,$sel['end_publishing']);
}
else
{
	$sel['start_publishing'] = $sel['end_publishing'] = '';
}

/**
 * Get service metadata.
 *
 * @since 1.6.1
 */
$meta = $sel['metadata'] ? (array) json_decode($sel['metadata'], true) : array();

?>

<?php if ($sel['id'] != -1 && VikAppointments::isMultilanguage()) { ?>

	<div class="btn-toolbar" id="filter-bar">
		<div class="btn-group pull-right">
			<?php foreach (VikAppointments::getKnownLanguages() as $lang) { 
				$lang_name = explode('-', $lang);
				$lang_name = $lang_name[1];
				?>
				<button type="button" class="btn" onClick="SELECTED_TAG='<?php echo $lang; ?>';vapOpenJModal('langtag', null, true);">
					<i class="icon">
						<img src="<?php echo VAPASSETS_URI . 'css/flags/' . strtolower($lang_name) . '.png'; ?>" />
					</i>
					&nbsp;<?php echo $lang; ?>
				</button>
			<?php } ?>
		</div>
	</div>

<?php } ?>

<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data" id="adminForm">

	<?php echo $vik->bootStartTabSet('service', array('active' => $this->tab)); ?>

		<!-- DETAILS -->
			
		<?php echo $vik->bootAddTab('service', 'service_details', JText::_('VAPORDERSERVICE')); ?>
	
			<div class="span7">
				<?php echo $vik->openEmptyFieldset(); ?>
				
					<!-- SERVICE NAME - Text -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE2').'*:'); ?>
						<input type="text" name="name" class="required" value="<?php echo $sel['name']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>

					<!-- SERVICE ALIAS - Text -->
					<?php echo $vik->openControl(JText::_('JFIELD_ALIAS_LABEL').':'); ?>
						<input type="text" name="alias" value="<?php echo $sel['alias']; ?>" size="40" />
					<?php echo $vik->closeControl(); ?>
					
					<!-- DURATION - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE4').'*:'); ?>
						<input type="number" name="duration" class="required" value="<?php echo $sel['duration']; ?>" size="10" min="1" max="99999999" id="vapdurationinput" onChange="ASK_CONFIRMATION = true;durationValueChanged();" />
						&nbsp;<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
					<?php echo $vik->closeControl(); ?> 
					
					<!-- SLEEP - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE19').':'); ?>
						<input type="number" name="sleep" value="<?php echo $sel['sleep']; ?>" size="10" min="-9999999" max="99999999" id="vapsleepinput" onChange="ASK_CONFIRMATION = true;durationValueChanged();" />
						&nbsp;<?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGESERVICE19'),
							'content' 	=> JText::_('VAPMANAGESERVICE19_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?> 
					
					<!-- TIME SLOTS LENGTH - Select -->
					<?php
					$elems = array(
						JHtml::_('select.option', 1, JText::sprintf('VAPSERVICETIMESLOTSLEN1', ($sel['duration']+$sel['sleep']))),
						JHtml::_('select.option', 2, JText::sprintf('VAPSERVICETIMESLOTSLEN2', $interval)),
					);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE20').':'); ?>
						<select name="interval" id="vap-duration-sel">
							<?php echo JHtml::_('select.options', $elems, 'value', 'text', $sel['interval']); ?>
						</select>
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGESERVICE20'),
							'content' 	=> JText::_('VAPMANAGESERVICE20_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?> 
					
					<!-- PRICE - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE5').':'); ?>
						<input type="number" name="price" value="<?php echo $sel['price']; ?>" size="10" min="0" step="any" onChange="ASK_CONFIRMATION = true;" />
						&nbsp;<?php echo $config->get('currencysymb'); ?>
					<?php echo $vik->closeControl(); ?> 
					
					<!-- MAX CAPACITY - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE21').':'); ?>
						<input type="number" name="max_capacity" value="<?php echo $sel['max_capacity']; ?>" size="10" min="<?php echo $sel['max_per_res']; ?>" max="999999" onChange="maximumCapacityChanged();" />
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGESERVICE21'),
							'content' 	=> JText::_('VAPMANAGESERVICE21_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- MIN PEOPLE PER APP - Number -->
					<?php
					$capControl = array();
					$capControl['style'] = $sel['max_capacity'] <= 1 ? 'display: none;' : '';
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE22').':', 'vaptrmaxcapchild', $capControl); ?>
						<input type="number" name="min_per_res" value="<?php echo $sel['min_per_res']; ?>" size="10" min="1" max="<?php echo $sel['max_per_res']; ?>" onChange="minimumPeopleChanged();" />
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGESERVICE22'),
							'content' 	=> JText::_('VAPMANAGESERVICE22_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- MAX PEOPLE PER APP - Number -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE23').':', 'vaptrmaxcapchild', $capControl); ?>
						<input type="number" name="max_per_res" value="<?php echo $sel['max_per_res']; ?>" size="10" min="<?php echo $sel['min_per_res']; ?>" max="<?php echo $sel['max_capacity']; ?>" onChange="maximumPeopleChanged();" />
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGESERVICE22'),
							'content' 	=> JText::_('VAPMANAGESERVICE22_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- PRICE PER PEOPLE - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['priceperpeople'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['priceperpeople'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE26').':', 'vaptrmaxcapchild', $capControl); ?>
						<?php echo $vik->radioYesNo('priceperpeople', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- APP PER SLOT - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['app_per_slot'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['app_per_slot'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE34').':', 'vaptrmaxcapchild', $capControl); ?>
						<?php echo $vik->radioYesNo('app_per_slot', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title'		=> JText::_('VAPMANAGESERVICE34'),
							'content' 	=> JText::_('VAPMANAGESERVICE34_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- START PUBLISHING - Date -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE29').':'); ?>
						<?php echo $vik->calendar($sel['start_publishing'], 'start_publishing', 'vap-startpub-date'); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- END PUBLISHING - Date -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE30').':'); ?>
						<?php echo $vik->calendar($sel['end_publishing'], 'end_publishing', 'vap-endpub-date'); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- UPLOAD IMAGE - File -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE7').':'); ?>
						<span id="vapimagespan">
							<input type="file" name="image" id="vapimagefileinput" size="35" onChange="uploadImage();" onClick="return openMediaPropertiesDialog();">
						</span>
					<?php echo $vik->closeControl(); ?>
					
					<!-- CHOOSE IMAGE - Select -->
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE8').':'); ?>
						<span>
							<?php echo AppointmentsHelper::composeMediaSelect($sel['image'], true, $media_prop); ?>
							<a href="<?php echo $img_url ?>" id="vapimagelink" class="modal no-decoration" target="_blank" style="margin-left: 5px;">
								<?php if ($sel['image']) { ?>
									<i class="fa fa-camera big"></i>
								<?php } ?>
							</a>
						</span>
					<?php echo $vik->closeControl(); ?>
					
					<!-- GROUP - Select -->
					<?php
					$elems = array();
					$elems[] = JHtml::_('select.option', '', '');
					foreach ($groups as $g)
					{
						$elems[] = JHtml::_('select.option', $g['id'], $g['name']);
					}
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE10').':'); ?>
						<select name="group" id="vap-group-sel">
							<?php echo JHtml::_('select.options', $elems, 'value', 'text', $sel['id_group']); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- ACCESS - Select -->
					<?php
					echo $vik->openControl(JText::_('JFIELD_ACCESS_LABEL').':');
					echo JHtml::_('access.level', 'level', $sel['level'], '', false, 'vap-level-select');
					echo $vik->createPopover(array(
						'title' 	=> JText::_('JFIELD_ACCESS_LABEL'),
						'content' 	=> JText::_('JFIELD_ACCESS_DESC'),
					));
					echo $vik->closeControl();
					?>

				<?php echo $vik->closeEmptyFieldset(); ?>

			</div>

			<div class="span5">

				<?php echo $vik->openEmptyFieldset(); ?>

					<!-- PUBLISHED - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['published'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['published'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE6').':'); ?>
						<?php echo $vik->radioYesNo('published', $elem_yes, $elem_no, false); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- QUICK CONTACT - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['quick_contact'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['quick_contact'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE17').':'); ?>
						<?php echo $vik->radioYesNo('quick_contact', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGESERVICE17'),
							'content' 	=> JText::_('VAPMANAGESERVICE17_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- EMPLOYEE CHOOSABLE - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['choose_emp'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['choose_emp'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE18').':'); ?>
						<?php echo $vik->radioYesNo('choose_emp', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGESERVICE18'),
							'content' 	=> JText::_('VAPMANAGESERVICE18_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- HAS OWN CALENDAR - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['has_own_cal'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['has_own_cal'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE33').':'); ?>
						<?php echo $vik->radioYesNo('has_own_cal', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGESERVICE33'),
							'content' 	=> JText::_('VAPHASOWNCALMESSAGE'),
						)); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- CHECKOUT SELECTION - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['checkout_selection'] == 1, 'onclick="layoutValueChanged(\'checkout_selection\');"');
					$elem_no  = $vik->initRadioElement('', '', $sel['checkout_selection'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE35').':'); ?>
						<?php echo $vik->radioYesNo('checkout_selection', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGESERVICE35'),
							'content' 	=> JText::_('VAPMANAGESERVICE35_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>

					<!-- DISPLAY SEATS - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['display_seats'] == 1, 'onclick="layoutValueChanged(\'display_seats\');"');
					$elem_no  = $vik->initRadioElement('', '', $sel['display_seats'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE36').':', 'vaptrmaxcapchild', $capControl); ?>
						<?php echo $vik->radioYesNo('display_seats', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGESERVICE36'),
							'content' 	=> JText::_('VAPMANAGESERVICE36_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
					<!-- ENABLE ZIP - Radio Button -->
					<?php
					$elem_yes = $vik->initRadioElement('', '', $sel['enablezip'] == 1);
					$elem_no  = $vik->initRadioElement('', '', $sel['enablezip'] == 0);
					?>
					<?php echo $vik->openControl(JText::_('VAPMANAGESERVICE24').':'); ?>
						<?php echo $vik->radioYesNo('enablezip', $elem_yes, $elem_no, false); ?>
						<?php echo $vik->createPopover(array(
							'title' 	=> JText::_('VAPMANAGESERVICE24'),
							'content' 	=> JText::_('VAPMANAGESERVICE24_DESC'),
						)); ?>
					<?php echo $vik->closeControl(); ?>
					
					<?php
					/**
					 * Display recurrence setting only if globally enabled.
					 *
					 * @since 1.6.3
					 */
					if ($config->getBool('enablerecur'))
					{
						?>
						<!-- USE RECURRENCE - Radio Button -->
						<?php
						$elem_yes = $vik->initRadioElement('', '', $sel['use_recurrence'] == 1);
						$elem_no  = $vik->initRadioElement('', '', $sel['use_recurrence'] == 0);
						echo $vik->openControl(JText::_('VAPMANAGESERVICE27').':');
							echo $vik->radioYesNo('use_recurrence', $elem_yes, $elem_no, false);
							echo $vik->createPopover(array(
								'title' 	=> JText::_('VAPMANAGESERVICE27'),
								'content' 	=> JText::_('VAPMANAGESERVICE27_DESC'),
							));
						echo $vik->closeControl();
					}
					?>
			
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- DESCRIPTION -->

		<?php echo $vik->bootAddTab('service', 'service_description', JText::_('VAPMANAGESERVICE3')); ?>
	
			<div class="span8">
				<?php echo $vik->openEmptyFieldset(); ?>
					<div class="control-group">
						<?php echo $editor->display( "description", $sel['description'], 400, 200, 70, 20 ); ?>
					</div>
				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>


		<!-- ASSIGNMENTS -->

		<?php echo $vik->bootAddTab('service', 'service_assoc', JText::_('VAPFIELDSETASSOC')); ?>

			<div class="span6">
				<?php echo $vik->openFieldset(JText::_('VAPMANAGESERVICE13'), 'horizontal-form'); ?>
					<?php
					/**
					 * Group the employees by category.
					 *
					 * @since 1.6.3
					 */
					$elems = array(
						0 => array(JHtml::_('select.option', '', '')),
					);

					// no group label
					$no_group = JText::_('VAPSERVICENOGROUP');

					foreach ($employees as $e)
					{
						// use 'no group' if missing
						$e['group_name'] = $e['group_name'] ? $e['group_name'] : $no_group;

						if (!isset($elems[$e['group_name']]))
						{
							$elems[$e['group_name']] = array();
						}

						$elems[$e['group_name']][] = JHtml::_('select.option', $e['id'], $e['lastname'] . ' ' . $e['firstname']);
					}

					// check if we have a 'no group' optgroup
					if (isset($elems[$no_group]))
					{
						// keep the array before removing it
						$tmp = $elems[$no_group];
						// unset from the list
						unset($elems[$no_group]);
						// push as last element
						$elems[$no_group] = $tmp;
					}

					?>
					<?php echo $vik->openControl(''); ?>
						<?php if (count($employees)) { ?>
							<div style="margin-bottom: 15px;">
								<?php
								$params = array(
									'id' 			=> 'vapempsel',
									'group.items' 	=> null,
									'list.select'	=> null,
								);
								echo JHtml::_('select.groupedList', $elems, null, $params);
								?>
								<button type="button" id="add-emp-btn" class="btn btn-success" disabled="disabled" onclick="addEmployee();" style="vertical-align: middle;line-height: 12px;">
									<i class="icon-new icon-white"></i>
									<?php echo JText::_("VAPMANAGESERVICE14"); ?>
								</button>
							</div>

							<div id="vapempsdiv">
								<?php foreach ($serviceEmployees as $e) { ?>
									<div id="vapemprow<?php echo $e['id']; ?>">
										<input type="text" readonly value="<?php echo $e['lastname'] . ' ' . $e['firstname']; ?>" style="vertical-align: middle;" size="40" />

										<a href="javascript: void(0);" class="" onClick="removeEmployee(<?php echo $e['id']; ?>)">
											<i class="fa fa-trash input-align"></i>
										</a>
									</div>
								<?php } ?>
							</div>
						<?php } else {
							echo JText::_('VAPNOEMPLOYEE');        
						} ?>
					<?php echo $vik->closeControl(); ?>
				<?php echo $vik->closeFieldset(); ?>
			</div>

			<div class="span6">
				<?php echo $vik->openFieldset(JText::_('VAPMANAGESERVICE11'), 'horizontal-form'); ?>
					<?php
					$elems = array();
					$elems[0][] = JHtml::_('select.option', '', '');

					$unpublished_opt_group = JText::_('VAPMANAGECRONJOB10');

					foreach ($options as $o)
					{
						$group = $o['published'] ? 0 : $unpublished_opt_group;

						if (!isset($elems[$group]))
						{
							$elems[$group] = array();
						}

						$elems[$group][] = JHtml::_('select.option', $o['id'], $o['name']);
					}
					?>
					<?php echo $vik->openControl(''); ?>
						<?php if (count($options)) { ?>
							<div style="margin-bottom: 15px;">
								<?php
								$params = array(
									'id' 			=> 'vapoptionsel',
									'group.items' 	=> null,
									'list.select'	=> null,
								);
								echo JHtml::_('select.groupedList', $elems, '', $params);
								?>

								<button type="button" id="add-opt-btn" class="btn btn-success" disabled="disabled" onclick="addOption();" style="vertical-align: middle;line-height: 12px;">
									<i class="icon-new icon-white"></i>
									<?php echo JText::_("VAPMANAGESERVICE12"); ?>
								</button>
							</div>

							<div id="vapoptionsdiv">
								<?php foreach ($serviceOptions as $o) { ?>
									<div id="vapoptionrow<?php echo $o['id']; ?>">
										<input type="text" readonly value="<?php echo $o['name']; ?>" style="vertical-align: middle;" <?php echo ($o['published'] ? '' : 'class="invalid"'); ?> size="40" />
										<a href="javascript: void(0);" onClick="removeOption(<?php echo $o['id']; ?>)">
											<i class="fa fa-trash input-align"></i>
										</a>
									</div>
								<?php } ?>
							</div>
						<?php } else {
							echo JText::_('VAPNOOPTION');        
						} ?>
					<?php echo $vik->closeControl(); ?>
				<?php echo $vik->closeFieldset(); ?>
			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- METADATA -->
			
		<?php echo $vik->bootAddTab('service', 'service_metadata', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS')); ?>

			<?php echo $vik->openEmptyFieldset(); ?>

				<!-- BROWSER PAGE TITLE - Text -->
				<?php echo $vik->openControl(JText::_('COM_CONTENT_FIELD_BROWSER_PAGE_TITLE_LABEL').':'); ?>
					<input type="text" name="metadata[title]" value="<?php echo isset($meta['title']) ? $meta['title'] : ''; ?>" size="40" />
					<?php
					echo $vik->createPopover(array(
						'title' 	=> JText::_('COM_CONTENT_FIELD_BROWSER_PAGE_TITLE_LABEL'),
						'content' 	=> JText::_('COM_CONTENT_FIELD_BROWSER_PAGE_TITLE_DESC'),
					));
					?>
				<?php echo $vik->closeControl(); ?>

				<!-- META DESCRIPTION - Textarea -->
				<?php echo $vik->openControl(JText::_('JFIELD_META_DESCRIPTION_LABEL').':'); ?>
					<textarea name="metadata[description]" cols="40" rows="3"><?php echo isset($meta['description']) ? $meta['description'] : ''; ?></textarea>
					<?php
					echo $vik->createPopover(array(
						'title' 	=> JText::_('JFIELD_META_DESCRIPTION_LABEL'),
						'content' 	=> JText::_('JFIELD_META_DESCRIPTION_DESC'),
					));
					?>
				<?php echo $vik->closeControl(); ?>

				<!-- META KEYWORDS - Textarea -->
				<?php echo $vik->openControl(JText::_('JFIELD_META_KEYWORDS_LABEL').':'); ?>
					<textarea name="metadata[keywords]" cols="40" rows="3"><?php echo isset($meta['keywords']) ? $meta['keywords'] : ''; ?></textarea>
					<?php
					echo $vik->createPopover(array(
						'title' 	=> JText::_('JFIELD_META_KEYWORDS_LABEL'),
						'content' 	=> JText::_('JFIELD_META_KEYWORDS_DESC'),
					));
					?>
				<?php echo $vik->closeControl(); ?>

			<?php echo $vik->closeEmptyFieldset(); ?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>
	
	<input type="hidden" name="id" value="<?php echo $sel['id']; ?>" />
	<input type="hidden" name="task" value="uploadImage" />
	<input type="hidden" name="option" value="com_vikappointments" />
	<input type="hidden" name="tabname" value="<?php echo $this->tab; ?>" />
</form>

<div id="dialog-media" title="<?php echo JText::_('VAPMEDIAPROPBOXTITLE');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-gear" style="float: left; margin: 0 7px 20px 0;"></span>
		<span style="font-size: 13px;font-weight: bold;"><?php echo JText::_('VAPMEDIAPROPBOXMESSAGE'); ?></span>
		<table class="adminform">
			
			<tr>
				<td width="125"><b><?php echo JText::_('VAPMANAGEMEDIA6');?>:</b></td>
				<td><span>
					<?php echo JText::_('VAPMANAGEMEDIA9'); ?>
					<input type="number" id="vaporiw" value="<?php echo $def_val['ORI_W']; ?>" size="4" min="64" max="9999" <?php echo (!$def_val['ISRES'] ? 'readonly' : ''); ?>/>px &nbsp;
					<?php echo JText::_('VAPMANAGEMEDIA10'); ?>
					<input type="number" id="vaporih" value="<?php echo $def_val['ORI_H']; ?>" size="4" min="64" max="9999" <?php echo (!$def_val['ISRES'] ? 'readonly' : ''); ?>/>px &nbsp;&nbsp;
					<label for="vaporiresize"><?php echo JText::_('VAPMANAGEMEDIA8'); ?></label>
					<input type="checkbox" id="vaporiresize" value="1" onChange="resizeStatusChanged();" <?php echo (($def_val['ISRES']) ? 'checked="checked"' : '' ); ?>/>
				</span></td>
			</tr>
			
			<tr>
				<td width="125"><b><?php echo JText::_('VAPMANAGEMEDIA7');?>:</b> </td>
				<td><span>
					<?php echo JText::_('VAPMANAGEMEDIA9'); ?>
					<input type="number" id="vapsmallw" value="<?php echo $def_val['SML_W']; ?>" size="4" min="16" max="1024" />px &nbsp;
					<?php echo JText::_('VAPMANAGEMEDIA10'); ?>
					<input type="number" id="vapsmallh" value="<?php echo $def_val['SML_H']; ?>" size="4" min="16" max="1024" />px
				</span></td>
			</tr>
			
		</table>
	</p>
</div>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-langtag',
	array(
		'title'       => JText::_('VAPSERLANGTRANSLATION') . '<span id="tag-target"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '',
	)
);
?>

<div id="dialog-confirm" title="<?php echo JText::_('VAPSERCONFTITLE');?>" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-locked" style="float: left; margin: 0 7px 20px 0;"></span>
		<span>
			<?php echo JText::_('VAPSERCONFMESSAGE'); ?>
		</span>
	</p>
</div>

<?php
JText::script('VAPSERVICENOGROUP');
JText::script('VAPMANAGESERVICE31');
JText::script('VAPMANAGESERVICE32');
?>

<script>

	jQuery(document).ready(function() {
		
		jQuery('#vap-group-sel').select2({
			placeholder: Joomla.JText._("VAPSERVICENOGROUP"),
			allowClear: true,
			width: 300
		});

		jQuery('#vap-duration-sel').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 200
		});

		jQuery('#vap-level-select').select2({
			minimumResultsForSearch: -1,
			allowClear: false,
			width: 300
		});

	});

	function addEmployee() {
		var emp = jQuery('#vapempsel').val();
		if (emp.length == 0) {
			return;
		}

		var emp_name = jQuery('#vapempsel :selected').text();
		
		if (!entryAlreadyExists(emp, jQuery('#vapempsdiv'), 'vapemprow')) {
			jQuery('#vapempsdiv').append('<div id="vapemprow'+emp+'">\n'+
				'<input type="text" readonly value="'+emp_name+'" style="vertical-align: middle;" size="40" />\n'+
				'<a href="javascript: void(0);" onClick="removeEmployee('+emp+')">\n'+
					'<i class="fa fa-trash input-align"></i>\n'+
				'</a>\n'+
				'</div>');
		
			jQuery('#adminForm').append('<input type="hidden" name="new_emp[]" value="'+emp+'"/>');
		}
	}
	
	function removeEmployee(id) {
		jQuery('#vapemprow'+id).remove();
		jQuery('#adminForm').append('<input type="hidden" name="del_emp[]" value="'+id+'"/>');
	}
	
	function addOption() {
		var opt = jQuery('#vapoptionsel').val();
		if (opt.length == 0) {
			return;
		}

		var opt_name = jQuery('#vapoptionsel :selected').text();
		
		if (!entryAlreadyExists(opt, jQuery('#vapoptionsdiv'), 'vapoptionrow')) {
			jQuery('#vapoptionsdiv').append('<div id="vapoptionrow'+opt+'">\n'+
				'<input type="text" readonly value="'+opt_name+'" style="vertical-align: middle;" size="40" />\n'+
				'<a href="javascript: void(0);" onClick="removeOption('+opt+')">\n'+
					'<i class="fa fa-trash input-align"></i>\n'+
				'</a>\n'+
				'</div>');
		
			jQuery('#adminForm').append('<input type="hidden" name="new_opt[]" value="'+opt+'"/>');
		}
	}
	
	function removeOption(id) {
		jQuery('#vapoptionrow'+id).remove();
		jQuery('#adminForm').append('<input type="hidden" name="del_opt[]" value="'+id+'"/>');
	}
	
	function entryAlreadyExists(id, div, text) {
		var children = div.children();
		for (var i = 0; i < children.length; i++) {
			if (children[i].id.split(text)[1] == id) {
				return true;
			}
		}
		
		return false;
	}
	
	var old_dur_sleep_value = <?php echo ($sel['duration']+$sel['sleep']); ?>;
	
	function durationValueChanged() {
		var duration = parseInt(jQuery('#vapdurationinput').val());
		var sleep 	 = parseInt(jQuery('#vapsleepinput').val());
		
		var label = jQuery('#vap-duration-sel').children()[0].text;
		
		jQuery('#vap-duration-sel').children()[0].text = label.replace(old_dur_sleep_value, (duration+sleep));
		old_dur_sleep_value = parseInt(duration+sleep);

		jQuery('#vap-duration-sel').select2('val', jQuery('#vap-duration-sel').val());
	}
	
	// MAXIMUM CAPACITY
	
	function maximumCapacityChanged() {
		var max = getByName('max_capacity').val();
		
		if (max <= 1) {
			jQuery('.vaptrmaxcapchild').hide();
		} else {
			jQuery('.vaptrmaxcapchild').show();
		}
		
		getByName('max_per_res').prop('max', max );
	}
	
	function minimumPeopleChanged() {
		var min = getByName('min_per_res').val();
		getByName('max_per_res').prop('min', min );
	}
	
	function maximumPeopleChanged() {
		var max = getByName('max_per_res').val();
		getByName('min_per_res').prop('max', max );
		getByName('max_capacity').prop('min', max );
	}
	
	function getByName(name) {
		return jQuery(':input[name='+name+']');
	}

	function layoutValueChanged(name) {

		var lookup = ['checkout_selection', 'display_seats'];

		for (var i = 0; i < lookup.length; i++) {
			if (lookup[i] != name) {
				getByName(lookup[i]).prop('checked', false);
			}
		}
	}
	
	// IMAGE LISTENER //
	
	function uploadImage() {
		if (jQuery('#vapverifyimg').length == 0) {
			jQuery('#vapimagespan').append('<img src="<?php echo $_ASSETS . 'loading.gif'; ?>" id="vapverifyimg" />');
		} else {
			jQuery('#vapverifyimg').prop('src', '<?php echo $_ASSETS . 'loading.gif'; ?>');
		}
		
		var formData = new FormData(jQuery('form#adminForm')[0]);
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=uploadImage&tmpl=component",
			data: formData,
			cache: false,
			processData: false,
			contentType: false
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0] == 1) {
				jQuery('#vapverifyimg').prop('src', '<?php echo $_ASSETS . 'ok.png'; ?>');
				jQuery('#vapmediaselect').append('<option value="'+obj[1]+'" selected="selected">'+obj[1]+'</option>');
				jQuery('#vapmediaselect').select2('val', obj[1]);
				imageSelectChanged();
			} else {
				jQuery('#vapverifyimg').prop('src', '<?php echo $_ASSETS . 'no.png'; ?>');
				alert(obj[1]);
			}
			
		});
	}
	
	function imageSelectChanged() {
		var img = jQuery('#vapmediaselect').val();
		if (img.length > 0) {
			jQuery('#vapimagelink').prop('href', '<?php echo VAPMEDIA_URI; ?>'+img);
			jQuery('#vapimagelink').html('\n<i class="fa fa-camera big"></i>\n');
		} else {
			jQuery('#vapimagelink').prop('href', '');
			jQuery('#vapimagelink').html('');
		}
	}
	
	var configure_media = <?php echo ($is_media_prop ? 0 : 1); ?>;
	
	function openMediaPropertiesDialog() {
		if (configure_media) {
			
			jQuery("#dialog-media").dialog({
				resizable: false,
				width: 480,
				height: 260,
				modal: true,
				buttons: {
					"<?php echo JText::_('JSAVE'); ?>": function() {
						jQuery(this).dialog("close");
						storeMediaProperties();
						jQuery('#vapimagefileinput').click();
					},
					"<?php echo JText::_('JCANCEL'); ?>": function() {
						jQuery(this).dialog("close");
					}
				}
			});
			
			if (configure_media) {
				return false;
			}
			
		}
		
	}
	
	function storeMediaProperties() {
		var prop = new Array(
			jQuery('#vaporiw').val(),
			jQuery('#vaporih').val(),
			jQuery('#vapsmallw').val(),
			jQuery('#vapsmallh').val(),
			jQuery('#vaporiresize').is(':checked') ? 1 : 0
		);
		
		configure_media = 0;
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_vikappointments&task=storeMediaProperties&tmpl=component",
			data: {
				oriwres: prop[0],
				orihres: prop[1],
				smallwres: prop[2],
				smallhres: prop[3],
				isresize: prop[4] 
			}
		}).done(function(resp){
			
		});
	}
	
	function resizeStatusChanged() {
		var is = jQuery('#vaporiresize').is(':checked');
		jQuery('#vaporiw').prop('readonly', ((is) ? false : true) );
		jQuery('#vaporih').prop('readonly', ((is) ? false : true) );
	}
	
	// TRANSLATIONS

	jQuery(document).ready(function(){

		jQuery('#vapempsel').select2({
			placeholder: Joomla.JText._("VAPMANAGESERVICE31"),
			allowClear: true,
			width: 300,
		});

		jQuery('#vapoptionsel').select2({
			placeholder: Joomla.JText._("VAPMANAGESERVICE32"),
			allowClear: true,
			width: 300,
		});

		jQuery('#vapempsel').on('change', function() {

			jQuery('#add-emp-btn').prop('disabled', jQuery(this).val().length == 0 ? true : false);

		});

		jQuery('#vapoptionsel').on('change', function() {

			jQuery('#add-opt-btn').prop('disabled', jQuery(this).val().length == 0 ? true : false);

		});
		
	});
	
	var SELECTED_TAG = '';

	function vapOpenJModal(id, url, jqmodal) {
		if (id == 'langtag') {
			url = 'index.php?option=com_vikappointments&task=managelangservice&id_service=<?php echo $sel['id']; ?>&tag=' + SELECTED_TAG;

			jQuery('#tag-target').text(' ' + SELECTED_TAG);
		}

		<?php echo $vik->bootOpenModalJS(); ?>
	}

	// tab handler

	jQuery(document).ready(function() {
		
		jQuery('a[href^="#service_"]').on('click', function() {
			var href = jQuery(this).attr('href').substr(1);
			jQuery('input[name="tabname"]').val(href);
		});

	});

	// validate

	var validator = new VikFormValidator('#adminForm');

	Joomla.submitbutton = function(task)
	{
		if (task.indexOf('save') !== -1)
		{
			if (validator.validate())
			{
				// Ask to update the overrides only in case the service already exists.
				// Because a new service cannot be already assigned to any employee and
				// this means all the new employees will be automatically updated.
				if (ASK_CONFIRMATION && <?php echo (int) ($sel['id'] > 0); ?>)
				{
					openConfirmationDialog(task);
				}
				else
				{
					Joomla.submitform(task, document.adminForm);
				}
			}
		}
		else
		{
			Joomla.submitform(task, document.adminForm);
		}
	}

	//

	var ASK_CONFIRMATION = false;

	function openConfirmationDialog(task) {
		jQuery("#dialog-confirm").dialog({
			resizable: false,
			width: 360,
			height:160,
			modal: true,
			buttons: {
				"<?php echo JText::_('JYES'); ?>": function() {
					jQuery('#adminForm').append('<input type="hidden" name="update_employees" value="1" />');
					jQuery(this).dialog("close");
					Joomla.submitform(task, document.adminForm);
				},
				"<?php echo JText::_('JNO'); ?>": function() {
					jQuery(this).dialog("close");
					Joomla.submitform(task, document.adminForm);
				},
				"<?php echo JText::_('JCANCEL'); ?>": function() {
					jQuery(this).dialog("close");
				}
			}
		});
	}
	
</script>
