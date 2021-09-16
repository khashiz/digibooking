<?php
/**
 * @version        1.0
 * @package        DJ-Notifications
 * @copyright    Copyright (C) 2019 DJ-Extensions.com LTD, All rights reserved.
 * @license        http://www.gnu.org/licenses GNU/GPL
 * @author        url: http://design-joomla.eu
 * @author        email contact@design-joomla.eu
 * @developer    Mateusz Maciejewski - mateusz.maciejewski@indicoweb.com
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ Toastr. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldDJNotification extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'djnotification';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{

	    return
            '<a class="btn" target="_blank" href="' . JUri::root(true) . '/index.php?djnotifications=error' . '">Error</a>' .
            '<a class="btn" target="_blank" href="' . JUri::root(true) . '/index.php?djnotifications=warning' . '">Warning</a>' .
            '<a class="btn" target="_blank" href="' . JUri::root(true) . '/index.php?djnotifications=info' . '">Info</a>' .
            '<a class="btn" target="_blank" href="' . JUri::root(true) . '/index.php?djnotifications=success' . '">Success</a>' .
            '<a class="btn" target="_blank" href="' . JUri::root(true) . '/index.php?djnotifications=all' . '">All</a>';

	}
}
