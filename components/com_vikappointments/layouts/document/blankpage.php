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

$body = $displayData['body'];

$app 		= JFactory::getApplication();
$document 	= JFactory::getDocument();

if (!headers_sent())
{
	// declare headers with content type to avoid encoding errors
	header('Content-Type: text/html; charset=' . $document->getCharset());
}

?>

<!DOCTYPE html>
<html lang="<?php echo $document->getLanguage(); ?>" dir="<?php echo $document->getDirection(); ?>">
	<head>
		<meta charset="<?php echo $document->getCharset(); ?>" />
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $document->getCharset(); ?>" />
		<meta name="robots" content="nofollow" />
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="HandheldFriendly" content="true" />
		<title><?php echo $app->get('sitename'); ?></title>
	</head>
	<body>
		<?php echo $body; ?>
	</body>
</html>
