<?php
/**
 * @version $Id: djnotificationstheme.php 22 2021-04-12 14:25 m.maciejewski $
 * @package DJ-Notifications
 * @copyright Copyright (C) 2017  DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 *
 * DJ-Notifications is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-Notifications is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-Notifications. If not, see <http://www.gnu.org/licenses/>.
 *
 */
jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');

class JFormFieldDJNotificationsTheme extends JFormFieldList
{
    protected $type = 'djnotificationstheme';

    protected function getOptions()
    {
        $options = array();

        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $levels = JAccess::getAuthorisedViewLevels($user->id);

        $themesPath = JPATH_ROOT . '/plugins/system/djnotifications/themes';
        $themes = array_filter(glob($themesPath . '/*'), 'is_dir');
        foreach ($themes as $theme) {
            $name = basename($theme);
            $options[] = JHtml::_('select.option', $name, $name );
        }

        return $options;
    }
}
