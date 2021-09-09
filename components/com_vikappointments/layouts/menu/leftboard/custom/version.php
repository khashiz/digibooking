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

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   boolean  $newupdate   True if there is a new available update.
 * @var   boolean  $vikupdater  True if VikUpdater plugin is active.
 * @var   boolean  $connect     True to auto-search for new updates.
 * @var   string   $url         The fallback remote URL.
 * @var   string   $title       The item title.
 * @var   string   $label       The item label.
 */

JText::script('VAPCHECKINGVERSION');
JText::script('ERROR');

?>

<div class="version-box custom<?php echo $newupdate ? ' upd-avail' : ''; ?>">
	
	<?php
	if ($vikupdater)
	{
		// VikUpdater plugin is enabled

		$document = JFactory::getDocument();
		$document->addScriptDeclaration(
<<<JS
function callVersionChecker() {
	jQuery.noConflict();

	setVersionContent(Joomla.JText._('VAPCHECKINGVERSION'));

	var jqxhr = jQuery.ajax({
		type: "POST",
		url: "index.php?option=com_vikappointments&task=check_version_listener&tmpl=component",
		data: {}
	}).done(function(resp) {
		var obj = jQuery.parseJSON(resp);

		console.log(obj);

		if (obj["status"] == 1) {

			if (obj.response.status == 1) {

				if (obj.response.compare == 1) {
					jQuery("#vap-versioncheck-link").attr("onclick", "");
					jQuery("#vap-versioncheck-link").attr("href", "index.php?option=com_vikappointments&task=updateprogram");

					obj.response.shortTitle += '<i class="upd-avail fa fa-exclamation-triangle"></i>';

					jQuery(".version-box.custom").addClass("upd-avail");
				}

				setVersionContent(obj.response.shortTitle, obj.response.title);

			} else {
				console.log(obj.response.error);
				setVersionContent(Joomla.JText._('ERROR'));
			}

		} else {
			console.log("plugin disabled");
			setVersionContent(Joomla.JText._('ERROR'));
		}

	}).fail(function(resp){
		console.log(resp);
		setVersionContent(Joomla.JText._('ERROR'));
	});
}

function setVersionContent(cont, title) {
	jQuery("#vap-version-content").html(cont);

	if (title === undefined) {
		var title = "";
	}

	jQuery("#vap-version-content").attr("title", title);
}
JS
		);

		if ($connect)
		{
			$document->addScriptDeclaration(
<<<JS
jQuery(document).ready(function() {
	callVersionChecker();
});
JS
			);
		}
		?>
		<a
			href="<?php echo ($newupdate ? 'index.php?option=com_vikappointments&task=updateprogram' : 'javascript: void(0);'); ?>"
			onclick="<?php echo ($newupdate ? '' : 'callVersionChecker();'); ?>"
			id="vap-versioncheck-link"
		>
			<i class="fa fa-joomla"></i>
			<span id="vap-version-content" title="<?php echo $title; ?>">
				<?php 
				echo $label;

				if ($newupdate)
				{
					?><i class="upd-avail fa fa-exclamation-triangle"></i><?php
				}
				?>
			</span>
		</a>
		<?php
	}
	else
	{
		// VikUpdater plugin is disabled, fallback to remote url
		JHtml::_('behavior.modal');
		?>
		<a
			id="vcheck"
			href=""
			class="modal"
			rel="{handler: 'iframe'}"
			target="_blank"
			onclick="this.href='<?php echo $url; ?>';"
		>
			<i class="fa fa-joomla"></i>
			<span><?php echo $label; ?></span>
		</a>
		<?php
	}
	?>

</div>