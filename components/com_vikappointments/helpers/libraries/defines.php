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

if (defined('WPINC'))
{
	// do not proceed in case of WordPress
	return;
}

// Software version
define('VIKAPPOINTMENTS_SOFTWARE_VERSION', '1.6.3');

// Software alias
define('CREATIVIKAPP', 'com_vikappointments');

// Base path
define('VAPBASE', JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_vikappointments');
define('VAPBASE_URI', JUri::root() . 'components/com_vikappointments/');

// Admin path
define('VAPADMIN', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_vikappointments');
define('VAPADMIN_URI', JUri::root() . 'administrator/components/com_vikappointments/');

// Helpers path
define('VAPHELPERS', VAPBASE . DIRECTORY_SEPARATOR . 'helpers');

// Libraries path
define('VAPLIB', VAPBASE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'libraries');

// Assets URI
define('VAPASSETS_URI', JUri::root() . 'components/com_vikappointments/assets/');
define('VAPASSETS_ADMIN_URI', JUri::root() . 'administrator/components/com_vikappointments/assets/');

// Customers Uploads path
define('VAPCUSTOMERS_UPLOADS', VAPBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cust_tmp');

// Customers Uploads path
define('VAPCUSTOMERS_UPLOADS_URI', VAPASSETS_URI . 'cust_tmp/');

// Customers Uploads path
define('VAPCUSTOMERS_AVATAR', VAPBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'customers');

// Customers Uploads path
define('VAPCUSTOMERS_AVATAR_URI', VAPASSETS_URI . 'customers/');

// Media path
define('VAPMEDIA', VAPBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media');

// Media small path
define('VAPMEDIA_SMALL', VAPBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media@small');

// Media URI
define('VAPMEDIA_URI', JUri::root() . 'components/com_vikappointments/assets/media/');

// Media small URI
define('VAPMEDIA_SMALL_URI', JUri::root() . 'components/com_vikappointments/assets/media@small/');

// Invoice path
define('VAPINVOICE', VAPHELPERS . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'archive');

// Invoice URI
define('VAPINVOICE_URI', JUri::root() . 'components/com_vikappointments/helpers/pdf/archive/');
