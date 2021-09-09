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

$params = $this->params;

$custom_fields = $this->customFields;

$clogourl = '';
if (!empty($params['companylogo']))
{
	$clogourl = VAPMEDIA_URI . $params['companylogo'];
}

$media_prop = array(
	'name' 		=> 'companylogo',
	'id' 		=> 'vapmediaselect',
	'onChange' 	=> 'imageSelectChanged()'
);

if (empty($params['calsfromyear']))
{
	$arr = getdate();
	$params['calsfromyear'] = $arr['year'];
}

$templates = $this->templates;

$vik = UIApplication::getInstance();

?>

<!-- System Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE1'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- COMPANY NAME - Text -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG0"); ?></b> </td>
			<td><input type="text" name="agencyname" value="<?php echo $params['agencyname']?>" size="40"></td>
		</tr>
		
		<!-- LOGO IMAGE - Media Select -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG4"); ?></b> </td>
			<td><span>
				<?php echo AppointmentsHelper::composeMediaSelect($params['companylogo'], true, $media_prop); ?>
				<a href="<?php echo $clogourl ?>" id="vapimagelink" class="modal no-decoration" target="_blank" style="margin-left: 5px;">
					<?php if (!empty($params['companylogo'])) { ?>
						<i class="fa fa-camera big"></i>
					<?php } ?>
				</a>
			</span></td>
		</tr>
		
		<!-- ENABLE MULTILANGUAGE - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['ismultilang'] == 1);
		$elem_no  = $vik->initRadioElement('', '', $params['ismultilang'] == 0);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG67"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('ismultilang', $elem_yes, $elem_no); ?></td>
		</tr>
		
		<!-- DATE FORMAT - Select -->
		<?php
		$elements = array(
			JHtml::_('select.option', 'Y/m/d', JText::_('VAPCONFIGDATEFORMAT1')),
			JHtml::_('select.option', 'm/d/Y', JText::_('VAPCONFIGDATEFORMAT2')),
			JHtml::_('select.option', 'd/m/Y', JText::_('VAPCONFIGDATEFORMAT3')),
			JHtml::_('select.option', 'Y-m-d', JText::_('VAPCONFIGDATEFORMAT4')),
			JHtml::_('select.option', 'm-d-Y', JText::_('VAPCONFIGDATEFORMAT5')),
			JHtml::_('select.option', 'd-m-Y', JText::_('VAPCONFIGDATEFORMAT6')),
			JHtml::_('select.option', 'Y.m.d', JText::_('VAPCONFIGDATEFORMAT7')),
			JHtml::_('select.option', 'm.d.Y', JText::_('VAPCONFIGDATEFORMAT8')),
			JHtml::_('select.option', 'd.m.Y', JText::_('VAPCONFIGDATEFORMAT9')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG5"); ?></b> </td>
			<td>
				<select name="dateformat" class="small-medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['dateformat']); ?>
				</select>
			</td>
		</tr>
		
		<!-- TIME FORMAT - Select -->
		<?php
		$elements = array(
			JHtml::_('select.option', 'h:i A', JText::_('VAPCONFIGTIMEFORMAT1')),
			JHtml::_('select.option', 'H:i', JText::_('VAPCONFIGTIMEFORMAT2')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG6"); ?></b> </td>
			<td>
				<select name="timeformat" class="small-medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['timeformat']); ?>
				</select>
			</td>
		</tr>
		
		<!-- FORMAT DURATION - Radio Buttons -->
		<?php
		$elem_yes = $vik->initRadioElement('fd1', '', $params['formatduration'] == "1");
		$elem_no  = $vik->initRadioElement('fd0', '', $params['formatduration'] == "0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG55"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo("formatduration", $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG55'),
					'content' 	=> JText::_('VAPMANAGECONFIG55_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- MINUTES INTERVALS - Select -->
		<?php 
		$elements = array();
		foreach (array(5, 10, 15, 30, 60) as $min)
		{
			$elements[] = JHtml::_('select.option', $min, $min);
		}
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG10"); ?></b> </td>
			<td>
				<select name="minuteintervals" class="short">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['minuteintervals']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG10'),
					'content' 	=> JText::_('VAPMANAGECONFIG10_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- OPENING HOUR - Select -->
		<?php 
		list($params['openinghour'], $params['openingmin']) = explode(':', $params['openingtime']);

		$tf = str_replace(':i', '', $params['timeformat']);
		
		$hours = array();
		for ($h = 0; $h <= 24; $h++)
		{
			$hours[] = JHtml::_('select.option', $h, date($tf, mktime($h, 0, 0, 1, 1, 2000)));
		}

		$minutes = array();
		for ($m = 0; $m < 60; $m += 5)
		{
			$minutes[] = JHtml::_('select.option', $m, date('i', mktime(0, $m, 0, 1, 1, 2000)));
		}
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG15"); ?></b> </td>
			<td><span>
				<select name="openinghour" class="short">
					<?php echo JHtml::_('select.options', $hours, 'value', 'text', $params['openinghour']); ?>
				</select>&nbsp;
				<select name="openingmin" class="short">
					<?php echo JHtml::_('select.options', $minutes, 'value', 'text', $params['openingmin']); ?>
				</select>
			</span></td>
		</tr>
		
		<!-- CLOSING HOUR - Select -->
		<?php
		list($params['closinghour'], $params['closingmin']) = explode(':', $params['closingtime']);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG16"); ?></b> </td>
			<td><span>  
				<select name="closinghour" class="short">
					<?php echo JHtml::_('select.options', $hours, 'value', 'text', $params['closinghour']); ?>
				</select>&nbsp;
				<select name="closingmin" class="short">
					<?php echo JHtml::_('select.options', $minutes, 'value', 'text', $params['closingmin']); ?>
				</select>
			</span></td>
		</tr>
		
		<!-- MINUTES RESTRICTIONS - Number -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG22"); ?></b> </td>
			<td>
				<input type="number" name="minrestr" value="<?php echo $params['minrestr']; ?>" size="10" min="0" max="9999"/> <?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG22'),
					'content' 	=> JText::_('VAPMANAGECONFIG22_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- SHOW PHONE PREFIX - Radio Buttons -->
		<?php 
		$elem_yes = $vik->initRadioElement('', '', $params['showphprefix'] == "1");
		$elem_no  = $vik->initRadioElement('', '' , $params['showphprefix'] == "0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG58"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('showphprefix', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG58'),
					'content' 	=> JText::_('VAPMANAGECONFIG58_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- KEEP APPOINTMENTS LOCKED - Number -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG20"); ?></b> </td>
			<td>
				<input type="number" name="keepapplock" value="<?php echo $params['keepapplock']; ?>" size="40" min="5" max="9999"> <?php echo JText::_('VAPSHORTCUTMINUTE'); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG20'),
					'content' 	=> JText::_('VAPMANAGECONFIG20_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- DEFAULT TASK - Select -->
		<?php
		$elements = array(
			JHtml::_('select.option', 'vikappointments', JText::_('VAPMENUDASHBOARD')),
			JHtml::_('select.option', 'calendar', JText::_('VAPMENUCALENDAR')),
		);
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG21"); ?></b> </td>
			<td>
				<select name="deftask" class="small-medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['deftask']); ?>
				</select>
			</td>
		</tr>
		
		<!-- DASHBOARD REFRESH TIME - Number -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG48"); ?></b> </td>
			<td><input type="number" name="refreshtime" value="<?php echo $params['refreshtime']; ?>" size="40" min="15" max="9999"> <?php echo JText::_('VAPSHORTCUTSEC'); ?></td>
		</tr>
		
		<!-- ASK CONFIRM - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['askconfirm'] == "1", '');
		$elem_no  = $vik->initRadioElement('', '' , $params['askconfirm'] == "0", '');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG83"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('askconfirm', $elem_yes, $elem_no); ?></td>
		</tr>
		
		<?php
		/**
		 * Display router setting only in case we are running Joomla!
		 *
		 * @since 1.6.3
		 */
		if (VersionListener::getPlatform() == 'joomla')
		{
			?>
			<!-- ENABLE ROUTER - Radio Button -->
			<?php
			$elem_yes = $vik->initRadioElement('', '', $params['router'] == 1, '');
			$elem_no  = $vik->initRadioElement('', '' , $params['router'] == 0, '');
			?>
			<tr>
				<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG70"); ?></b> </td>
				<td><?php echo $vik->radioYesNo('router', $elem_yes, $elem_no); ?></td>
			</tr>
			<?php
		}
		?>
		
		<!-- LOAD JQUERY - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['loadjquery'] == "1", '');
		$elem_no  = $vik->initRadioElement('', '' , $params['loadjquery'] == "0", '');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG13"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('loadjquery', $elem_yes, $elem_no); ?></td>
		</tr>
		
		<!-- DISPLAY JOOMLA3810TER - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['showfooter'] == "1", '');
		$elem_no  = $vik->initRadioElement('', '' , $params['showfooter'] == "0", '');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG14"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('showfooter', $elem_yes, $elem_no); ?></td>
		</tr>

		<!-- GOOGLE API KEY - Number -->
		<tr>
			<td width="200" class="adminparamcol"> <b>Google API Key</b> </td>
			<td>
				<input type="text" name="googleapikey" value="<?php echo $params['googleapikey']?>" size="40" <?php echo (strlen($params['googleapikey']) ? 'readonly="readonly"' : ''); ?> />
				<?php if( strlen($params['googleapikey']) ) { ?>
					<a href="javascript: void(0);" onClick="lockUnlockInput(this);">
						<i class="fa fa-lock big"></i>
					</a>
				<?php } ?>
			</td>
		</tr>

		<!-- SITE THEME - Select -->
		<?php
		$themes   = glob(VAPBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . '*.css');
		$elements = array();

		foreach ($themes as $theme)
		{
			$theme = basename($theme);
			$theme = substr($theme, 0, strrpos($theme, '.'));

			$elements[] = JHtml::_('select.option', $theme, ucwords($theme));
		}
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG117"); ?></b> </td>
			<td>
				<select name="sitetheme" class="small-medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['sitetheme']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG117'),
					'content' 	=> JText::_('VAPMANAGECONFIG117_DESC'),
				)); ?>
			</td>
		</tr>

		<!-- CONVERSION TRACK - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['conversion_track'] == "1", 'onclick="jQuery(\'.manage-conversion-track a\').show();"');
		$elem_no  = $vik->initRadioElement('', '' , $params['conversion_track'] == "0", 'onclick="jQuery(\'.manage-conversion-track a\').hide();"');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG111"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('conversion_track', $elem_yes, $elem_no); ?>
				<span style="display: inline-block;vertical-align: top;margin-left: 20px;" class="manage-conversion-track">
					<a href="index.php?option=com_vikappointments&task=conversions" 
						style="<?php echo $params['conversion_track'] == '0' ? "display:none;" : ""; ?>" 
						class="btn" target="_blank"><?php echo JText::_('VAPMANAGECONFIGEMP9'); ?></a>
				</span>
			</td>
		</tr>
		
	</table>
</fieldset>

<!-- Calendars Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE7'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- NUMBER OF CALENDARS - Select -->
		<?php
		$elements = array();
		foreach (array(1, 2, 3, 6, 12) as $cal)
		{
			$elements[] = JHtml::_('select.option', $cal, $cal);
		}
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG19"); ?></b> </td>
			<td>
				<select name="numcals" class="short">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['numcals']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG19'),
					'content' 	=> JText::_('VAPMANAGECONFIG19_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- NUMBER OF MONTHS - Select -->
		<?php
		$elements = array();
		for ($mon = 1; $mon <= 12; $mon++)
		{
			$elements[] = JHtml::_('select.option', $mon, $mon);
		}
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG26"); ?></b> </td>
			<td>
				<select name="nummonths" class="short">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['nummonths']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG26'),
					'content' 	=> JText::_('VAPMANAGECONFIG26_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- SHOW CALENDARS FROM - Number -->
		<?php
		$elements = array();
		for ($i = 1; $i <= 12; $i++)
		{
			$elements[] = JHtml::_('select.option', $i, JText::_(ArasJoomlaVikApp::getMonthName($i)));
		}
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG56"); ?></b> </td>
			<td>
				<select name="calsfrom" class="small-medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['calsfrom']); ?>
				</select>&nbsp;
				<input type="number" name="calsfromyear" value="<?php echo $params['calsfromyear']; ?>" min="1397" max="9999"/>
				&nbsp;<small><?php echo JText::_("VAPMANAGECONFIG57"); ?></small></td>
		</tr>
		
		<!-- DAYS LEGEND - Select -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['legendcal'] == "1");
		$elem_no  = $vik->initRadioElement('', '', $params['legendcal'] == "0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG68"); ?></b> </td>
			<td><?php echo $vik->radioYesNo('legendcal', $elem_yes, $elem_no); ?></td>
		</tr>

		<!-- FIRST WEEK DAY - Dropown -->
		<?php
		$elements = array();
		for ($i = 1; $i <= 7; $i++)
		{
			$elements[] = JHtml::_('select.option', ($i == 7 ? 0 : $i), JText::_('VAPDAY' . $i));
		}
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG94"); ?></b> </td>
			<td>
				<select name="firstday" class="small-medium">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['firstday']); ?>
				</select>
			</td>
		</tr>
		
	</table>
</fieldset>

<!-- GDPR Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend">GDPR</legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- GDPR - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['gdpr'] == "1", 'onclick="jQuery(\'.gdpr-child\').show();"');
		$elem_no  = $vik->initRadioElement('', '', $params['gdpr'] == "0", 'onclick="jQuery(\'.gdpr-child\').hide();"');
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG112"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('gdpr', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG112'),
					'content' 	=> JText::_('VAPMANAGECONFIG112_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- PRIVACY POLICY - text -->
		<tr class="gdpr-child" style="<?php echo ($params['gdpr'] == 0 ? 'display: none;' : ''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG113"); ?></b> </td>
			<td><input type="text" name="policylink" value="<?php echo $params['policylink']; ?>" size="64" /></td>
		</tr>
		
	</table>
</fieldset>

<!-- Timezone Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE13'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- MULTI TIMEZONE - Radio Button -->
		<?php
		$elem_yes = $vik->initRadioElement('', '', $params['multitimezone']=="1");
		$elem_no  = $vik->initRadioElement('', '', $params['multitimezone']=="0");
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG86"); ?></b> </td>
			<td>
				<?php echo $vik->radioYesNo('multitimezone', $elem_yes, $elem_no); ?>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG86'),
					'content' 	=> JText::_('VAPMANAGECONFIG86_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- CURRENT TIMEZONE - Label -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG87"); ?></b> </td>
			<td><?php echo "<b>" . str_replace('_', ' ', date_default_timezone_get()) . "</b> | " . date('Y-m-d H:i:s T'); ?></td>
		</tr>
		
	</table>
</fieldset>

<!-- Appointments Sync Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE10'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- ADMIN SYNC URL - Response -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG63"); ?></b> </td>
			<td>
				<span id="syncurl"><?php echo JUri::root().'index.php?option=com_vikappointments&task=appsync&key=' . $params['synckey']; ?></span>

				<span style="line-height: 28px;">
					<?php echo $vik->createPopover(array(
						'title' 	=> JText::_('VAPMANAGECONFIG63'),
						'content' 	=> JText::_('VAPICSURL'),
					)); ?>
				</span>

				<a href="javascript: void(0);" id="syncurltest" style="margin-left:5px;">
					<i class="fa fa-link big"></i>
				</a>
			</td>
		</tr>
		
		<!-- SYNC PASSWORD - Text -->
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG64"); ?></b> </td>
			<td><input type="text" name="synckey" id="synckey" value="<?php echo $params['synckey']; ?>" size="40" maxlength="32"></td>
		</tr>
		
	</table>
</fieldset>

<!-- ZIP Fieldset -->
<fieldset class="adminform">
	<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE2'); ?></legend>
	<table class="admintable table" cellspacing="1">
		
		<!-- ZIP CODE ID - Select --> 

		<?php
		$elements = array(
			JHtml::_('select.option', '', ''),
		);
		foreach ($custom_fields as $cf)
		{
			// push the ZIP only if the field is an input text (as it is handled only by this field type)
			if (VAPCustomFields::isInputText($cf))
			{
				$tname = JText::_($cf['name']);

				if (VAPCustomFields::isZipCode($cf))
				{
					$tname .= ' ( !! )';
				}

				$elements[] = JHtml::_('select.option', $cf['id'], $tname);
			}
		}
		?>
		<tr>
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG32"); ?><br>&nbsp; <small>(<?php echo JText::_("VAPMANAGECONFIG33"); ?>)</small>:</b> </td>
			<td>
				<select name="zipcfid" id="vapzipfieldselect" onChange="zipFieldValueChanged();">
					<?php echo JHtml::_('select.options', $elements, 'value', 'text', $params['zipcfid']); ?>
				</select>
				<?php echo $vik->createPopover(array(
					'title' 	=> JText::_('VAPMANAGECONFIG32'),
					'content' 	=> JText::_('VAPMANAGECONFIG32_DESC'),
				)); ?>
			</td>
		</tr>
		
		<!-- ZIP CODES LIST -->

		<?php
		$zip_codes = empty($params['zipcodes']) ? array() : json_decode($params['zipcodes'], true);
		?>
		<tr class="vapzipchild" style="<?php echo (($params['zipcfid'] == -1)?'display: none;':''); ?>">
			<td style="vertical-align: top;" width="200" class="adminparamcol"> <b><?php echo JText::_('VAPMANAGECONFIG34');?></b> </td>
				<td><div id="vapzipcodescont">
					<?php 
					$zip_cont = 0;
					foreach ($zip_codes as $zip) { ?>
						<div id="vapzcrow<?php echo $zip_cont; ?>" style="margin-bottom: 5px;">
							<input type="text" name="zip_code_from[]" style="vertical-align: middle;" value="<?php echo $zip['from']; ?>" size="10" placeholder="<?php echo JText::_('VAPMANAGEWD3'); ?>" /> -
							<input type="text" name="zip_code_to[]" style="vertical-align: middle;" value="<?php echo $zip['to']; ?>" size="10" placeholder="<?php echo JText::_('VAPMANAGEWD4'); ?>" />
							<a href="javascript: void(0);" onClick="removeZipCode(<?php echo $zip_cont; ?>)">
								<i class="fa fa-times"></i>
							</a>
						</div>
					<?php 
						$zip_cont++;
					}
					?>
				</div>
				<div>
					<button type="button" class="btn vapaddzipbutton" onClick="putZipCode();"><?php echo JText::_('VAPMANAGECONFIG35'); ?></button>
				</div>
			</td>
		</tr>
		
		<!-- VALIDATE ZIP CODE - Test -->

		<tr class="vapzipchild" style="<?php echo (($params['zipcfid'] == -1)?'display: none;':''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG36"); ?></b> </td>
			<td>
				<div class="btn-toolbar">
				   <div class="btn-group pull-left">
					   <input type="text" value="" id="vaptryziptext" size="10" onChange="validateZipCode();"/>
				   </div>
				   <div class="btn-group pull-left">
					   <button type="button" onClick="validateZipCode();" class="btn"><?php echo JText::_("VAPMANAGECONFIG37"); ?></button>
				   </div class="btn-group pull-left">
				   <div class="btn-group pull-left">
					   <span id="vaploadimgspan"></span>
				   </div>
				</div>
			</td>
		</tr>
		
		<!-- UPLOAD ZIP LIST - File -->

		<tr class="vapzipchild" style="<?php echo (($params['zipcfid'] == -1)?'display: none;':''); ?>">
			<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGECONFIG39"); ?></b> </td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<input type="file" name="file" />
					</div>
					<div class="btn-group">
						<button type="button" onClick="uploadZipFile();" class="btn"><?php echo JText::_("VAPMANAGECONFIG40"); ?></button>
					</div>

					<?php echo $vik->createPopover(array(
						'title' 	=> JText::_('VAPMANAGECONFIG39'),
						'content' 	=> JText::_('VAPMANAGECONFIG41'),
					)); ?>
				</div>
			</td>
		</tr>

	</table>
</fieldset>

<div>
	<!-- Reservations Columns Fieldset -->
	<fieldset class="adminform pull-left" style="width:47%;">
		<legend class="adminlegend"><?php echo JText::_('VAPCONFIGGLOBTITLE4'); ?></legend>
		<table class="admintable table" cellspacing="1">
			<?php 
			$all_list_fields = array(
				'1'  => 'id',
				'2'  => 'sid',
				'13' => 'payment',
				'5'  => 'checkin_ts',
				'6'  => 'checkout',
				'3'  => 'employee',
				'4'  => 'service',
				'25' => 'people',
				'20' => 'info',
				'21' => 'coupon',
				'32' => 'nominative',
				'8'  => 'mail',
				'27' => 'phone',
				'9'  => 'total',
				'11' => 'paid',
				'35' => 'invoice',
				'12' => 'status',
			);

			$listable_fields = array();
			if (!empty($params['listablecols']))
			{
				$listable_fields = explode(',', $params['listablecols']);
			}
			
			$i = 0;
			foreach ($all_list_fields as $k => $f)
			{
				$selected = in_array($f, $listable_fields); 
				$elem_yes = $vik->initRadioElement('', '', $selected, 'onClick="toggleListField(\''.$f.'\', 1);"');
				$elem_no  = $vik->initRadioElement('', '', !$selected, 'onClick="toggleListField(\''.$f.'\', 0);"');
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_("VAPMANAGERESERVATION".$k); ?></b></td>
					<td>
						<?php echo $vik->radioYesNo($f.'listcol', $elem_yes, $elem_no); ?>
						<input type="hidden" name="listablecols[]" value="<?php echo $f.':'.$selected; ?>" id="vaphidden<?php echo $f; ?>"/>
					</td>
				</tr>
			<?php 
				$i++;
			} 
			?>
		</table>
	</fieldset>

	<!-- Reservations Columns CF Fieldset -->
	<fieldset class="adminform pull-right" style="width:47%;">
		<legend class="adminlegend"><?php echo JText::_('VAPMENUCUSTOMF'); ?></legend>
		<table class="admintable table" cellspacing="1">
			<?php 
			$all_list_fields = array();
			foreach ($custom_fields as $field)
			{
				$all_list_fields[$field['id']] = $field['name']; 
			}

			$listable_fields = (array) json_decode($params['listablecf'], true);
			
			foreach ($all_list_fields as $k => $field)
			{
				$selected = in_array($field, $listable_fields); 
				$elem_yes = $vik->initRadioElement('', '', $selected, 'onClick="toggleListFieldCF(\''.addslashes($field).'\', ' . $k . ', 1);"');
				$elem_no  = $vik->initRadioElement('', '', !$selected, 'onClick="toggleListFieldCF(\''.addslashes($field).'\', ' . $k . ', 0);"');
				?>
				<tr>
					<td width="200" class="adminparamcol"> <b><?php echo JText::_($field); ?></b></td>
					<td>
						<?php echo $vik->radioYesNo('listcf' . $k, $elem_yes, $elem_no); ?>
						<input type="hidden" name="listablecf[]" value="<?php echo $field.':'.$selected; ?>" id="vapcfhidden<?php echo $k; ?>"/>
					</td>
				</tr>
			<?php } ?>
		</table>
	</fieldset>
</div>

<?php
JText::script('VAPMANAGECONFIG38');
JText::script('VAPMANAGEWD3');
JText::script('VAPMANAGEWD4');
?>

<script>

	var zipCont = <?php echo $zip_cont; ?>;

	jQuery(document).ready(function() {

		jQuery('#synckey').on('keyup', function(e) {
			updateSyncURL();
		});

		jQuery('#syncurltest').click(function() {
			window.open(jQuery('#syncurl').html(), '_blank');
		});

		jQuery('#vapzipfieldselect').select2({
			placeholder: Joomla.JText._('VAPMANAGECONFIG38'),
			allowClear: true,
			width: 250
		});

	});

	function updateSyncURL() {
		var key = jQuery('#synckey').val();
		var url = jQuery('#syncurl').html();
		var last_part = url.lastIndexOf('=');
		url = url.substr(0, last_part+1)+key;
		jQuery('#syncurl').html(url);
	}
	
	function imageSelectChanged() {
		var img = jQuery('#vapmediaselect').val();
		if( img.length > 0 ) {
			jQuery('#vapimagelink').prop('href', '<?php echo VAPMEDIA_URI; ?>'+img);
			jQuery('#vapimagelink').html('\n<i class="fa fa-camera big"></i>\n');
		} else {
			jQuery('#vapimagelink').prop('href', '');
			jQuery('#vapimagelink').html('');
		}
	}
	
	function zipFieldValueChanged() {
		if (parseInt(jQuery('#vapzipfieldselect').val()) > 0) {
			jQuery('.vapzipchild').show();
		} else {
			jQuery('.vapzipchild').hide();
		}
		
	}
	
	function putZipCode() {
		jQuery('#vapzipcodescont').append('<div id="vapzcrow'+zipCont+'" style="margin-bottom: 5px;">\n'+
			'<input type="text" name="zip_code_from[]" style="vertical-align: middle;" value="" size="10" placeholder="'+Joomla.JText._('VAPMANAGEWD3')+'" />\n - \n'+
			'<input type="text" name="zip_code_to[]" style="vertical-align: middle;" value="" size="10" placeholder="'+Joomla.JText._('VAPMANAGEWD4')+'" />\n'+
			'<a href="javascript: void(0);" onClick="removeZipCode('+zipCont+')">\n'+
				'<i class="fa fa-times"></i>\n'+
			'</a>\n'+
		'</div>');
		zipCont++;
	}
	
	function removeZipCode(index) {
		jQuery('#vapzcrow'+index).remove();
	}
	
	function validateZipCode() {
		
		var zip = jQuery('#vaptryziptext').val();
		
		if( jQuery('#vapzipvalidimg').length == 0 ) {
			jQuery('#vaploadimgspan').append('<img src="<?php echo VAPASSETS_ADMIN_URI . 'images/loading.gif'; ?>" id="vapzipvalidimg" />');
		} else {
			jQuery('#vapzipvalidimg').prop('src', '<?php echo VAPASSETS_ADMIN_URI . 'images/loading.gif'; ?>');
		}
		
		jQuery.noConflict();
		
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "index.php",
			data: { option: "com_vikappointments", task: "validate_zip_code", zipcode: zip, tmpl: "component" }
		}).done(function(resp){
			var obj = jQuery.parseJSON(resp); 
			
			var img_src = '<?php echo VAPASSETS_ADMIN_URI . 'images/no.png'; ?>';
			if( obj[0] ) {
				var img_src = '<?php echo VAPASSETS_ADMIN_URI . 'images/ok.png'; ?>';
			}
			
			jQuery('#vapzipvalidimg').prop('src', img_src);
			
		});
		
	}
	
	function uploadZipFile() {
		jQuery('#vapconfigtask').val('upload_zip_file');
		document.adminForm.submit();
	}

	function toggleListField(id, value) {
		jQuery('#vaphidden'+id).val(id + ':' + value);
	}

	function toggleListFieldCF(cf, id, value) {
		jQuery('#vapcfhidden'+id).val(cf + ':' + value);
	}

</script>
