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

$sel = $this->reservation;

$vik = UIApplication::getInstance();

$editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

?>

<div class="span12">
	<?php echo $vik->openEmptyFieldset(); ?>

		<div class="control-group">
			<?php echo $editor->display('notes', $sel['notes'], 400, 200, 70, 20); ?>
		</div>

	<?php echo $vik->closeEmptyFieldset(); ?>
</div>
