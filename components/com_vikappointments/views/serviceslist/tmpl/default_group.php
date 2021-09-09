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

$name = $this->displayData['name'];
$desc = $this->displayData['description'];

/**
 * @todo 	Should we use a default label for
 * 			the group with no name (e.g. "uncategorized")?
 *
 * 			A default label should be used only in case
 * 			there are other services assigned to a specific group.
 * 			This because if the owner doesn't need to use groups,
 * 			it is not correct to display the "uncategorised" group.
 *
 * 			Anyhow, it could be helpful to evaluate the uncategorised
 * 			label within the view.html.php and to decide if it should
 * 			be used within this template file.
 */

?>

<?php
if(strlen($name) > 0)
{
	?>

	<div class="vapsergroupdiv">
		<?php echo $name; ?>
	</div>

	<?php
	if (strlen(strip_tags($desc)) > 0)
	{
		?>

		<div class="vapsergroupdescriptiondiv">
			<?php echo VikAppointments::renderHtmlDescription($desc, 'serviceslist'); ?>
		</div>

		<?php
	}

}
