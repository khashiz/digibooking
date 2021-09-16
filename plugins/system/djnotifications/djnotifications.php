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

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');


class PlgSystemDJNotifications extends JPlugin
{

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();


    }


    public function onBeforeRender() {
        JText::script('PLG_SYSTEM_DJNOTIFICATIONS_MESSAGE_HEADER_INFO');
        JText::script('PLG_SYSTEM_DJNOTIFICATIONS_MESSAGE_HEADER_SUCCESS');
        JText::script('PLG_SYSTEM_DJNOTIFICATIONS_MESSAGE_HEADER_WARNING');
        JText::script('PLG_SYSTEM_DJNOTIFICATIONS_MESSAGE_HEADER_ERROR');
    }

    /**
     * Add the CSS for debug.
     * We can't do this in the constructor because stuff breaks.
     *
     * @return  void
     *
     * @since   2.5
     */
    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        $alert = $app->input->get('djnotifications');

        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $layout = $app->input->get('layout');



        if (!$app->isClient('site') || ($layout == 'complete')) return;

        if($this->params->get('debug') && $alert) {

            if(strtolower($alert) == 'all') {
                $app->enqueueMessage(JText::_('PLG_SYSTEM_DJNOTIFICATIONS_SAMPLE_ALERT') . ' success', 'success');
                $app->enqueueMessage(JText::_('PLG_SYSTEM_DJNOTIFICATIONS_SAMPLE_ALERT'). ' info', 'info');
                $app->enqueueMessage(JText::_('PLG_SYSTEM_DJNOTIFICATIONS_SAMPLE_ALERT') . ' warning', 'warning');
                $app->enqueueMessage(JText::_('PLG_SYSTEM_DJNOTIFICATIONS_SAMPLE_ALERT') . ' error', 'error');
            }else {
                $app->enqueueMessage(JText::_('PLG_SYSTEM_DJNOTIFICATIONS_SAMPLE_ALERT'), $alert);
            }
        }


        $messages = $app->getMessageQueue(true);





        JHtml::_('script', 'system/core.js');

        if ($this->params->get('jquery')) {
            JHtml::_('jquery.framework');
        }

        $doc = JFactory::getDocument();
        $doc->addScript(JUri::base() . 'plugins/system/djnotifications/assets/js/toastr.js');
        $doc->addScript(JUri::base() . 'plugins/system/djnotifications/assets/js/script.js');
        $doc->addScript(JUri::base() . 'plugins/system/djnotifications/assets/js/jquery.easings.1.3.js');
        $doc->addStylesheet(JUri::base() . 'plugins/system/djnotifications/assets/css/toastr.min.css');
        $doc->addStylesheet(JUri::base() . 'plugins/system/djnotifications/themes/' . $this->params->get('theme', 'flat') . '/style.css');


        $doc->addScriptDeclaration('jQuery(document).ready(function() {
            if(typeof jQuery !== \'undefined\') {
                 jQuery().DJNotifications({
                      "closeButton": ' . (int)$this->params->get('close_button') . ',
                      "debug": ' . (int)$this->params->get('debug') . ',
                      "newestOnTop":  ' . (int)$this->params->get('newest_on_top') . ',
                      "progressBar": ' . (int)$this->params->get('progress_bar') . ',
                      "positionClass": "' . $this->params->get('position') . '",
                      "preventDuplicates": ' . (int)$this->params->get('prevent_duplicates') . ',
                      "onclick": null,
                      "showDuration": "' . $this->params->get('show_duration') . '",
                      "hideDuration": "' . $this->params->get('hide_duration') . '",
                      "timeOut": "' . $this->params->get('time_out') . '",
                      "extendedTimeOut": "' . $this->params->get('extended_time_out') . '",
                      "showEasing": "' . $this->params->get('show_easing') . '",
                      "hideEasing": "' . $this->params->get('hide_easing') . '",
                      "showMethod": "' . $this->params->get('show_method') . '",
                      "displayIcons": ' . $this->params->get('display_icons', 1) . ',
                      "displayHeaders": ' . $this->params->get('display_headers', 1) . ',
                      "hideMethod": "' . $this->params->get('hide_method') . '"
                })
            }
        });');
        if (count($messages)) {
            $doc->addScriptDeclaration('jQuery(document).ready(function () {
                jQuery(document).trigger("djtoastr:renderMessages", [' . json_encode($messages) . ']);
            });');
        }
    }
}
