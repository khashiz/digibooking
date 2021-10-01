<?php
/* ======================================================
 # Login as User for Joomla! - v3.4.2 (pro version)
 # -------------------------------------------------------
 # For Joomla! CMS (v3.x)
 # Author: Web357 (Yiannis Christodoulou)
 # Copyright (©) 2014-2021 Web357. All rights reserved.
 # License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
 # Website: https:/www.web357.com
 # Demo: https://demo.web357.com/joomla/login-as-user
 # Support: support@web357.com
 # Last modified: Wednesday 02 June 2021, 11:36:58 AM
 ========================================================= */
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemLoginAsUser extends JPlugin
{
	public function onAfterInitialise()
	{
		jimport('joomla.environment.uri' );
		$host = JURI::root();
		$app = JFactory::getApplication();
		$option = JFactory::getApplication()->input->get('option', '', 'STRING');
		
		// CSS - backend
		if ($app->isAdmin() && ($option == 'com_loginasuser' || $option == 'com_users'))
		{
			JFactory::getDocument()->addStyleSheet($host.'plugins/system/loginasuser/assets/css/loginasuser.css');
		}
		
		// get more useful vars
		$app = JFactory::getApplication();

		// check if is frontend
		$is_frontend = ($app->isSite()) ? true : false; 

		// get vars from user
		$loginasclient = JFactory::getApplication()->input->get('loginasclient', '', 'INT');
		$username = JFactory::getApplication()->input->get('lacusr', '', 'RAW');
		$password = JFactory::getApplication()->input->get('lacpas', '', 'RAW');
		
		// login user
		if ($loginasclient && !empty($username) && !empty($password))
		{			
			$db = JFactory::getDBO();

			// get user details from db
			$query = $db->getQuery(true)
				->select('id, name, username, password, params, lastvisitDate')
				->from('#__users')
				->where('username=' . $db->quote($username))
				->where('password=' . $db->quote($password));

			$db->setQuery($query);
			$sql_data = $db->loadObject();
	
			// get default site language
			$default_language = JComponentHelper::getParams('com_languages')->get('site','en-GB');

			// get user params
			$user_params = json_decode($sql_data->params);

			// build data object
			$data = new stdClass();
			$data->id = $sql_data->id;
			$data->fullname = $sql_data->name;
			$data->username = $sql_data->username;
			$data->password = $sql_data->password;
			$data->language = (!empty($user_params->language)) ? $user_params->language : $default_language;
			$data->lastvisitDate = $sql_data->lastvisitDate;

			// get lastvisitDate from user
			$lastvisitDate = $data->lastvisitDate;
	
			if ($data)
			{
				// get params
				$this->_plugin = JPluginHelper::getPlugin( 'system', 'loginasuser' );
				$this->_params = new JRegistry( $this->_plugin->params ); 
				$login_system = $this->_params->get('login_system', 'joomla');
				$send_message_to_admin = $this->_params->get('send_message_to_admin', 1);
				$admin_email = $this->_params->get('admin_email');
				$url_redirection_type_after_login = $this->_params->get('url_redirection_type_after_login', 'link');
				$url_redirect = $this->_params->get('url_redirect', 'index.php?option=com_users&view=profile');
				$redirect_to_a_menu_item = (int) $this->_params->get('redirect_to_a_menu_item', '');

				// Redirect to this link after login as user.
				if ($url_redirection_type_after_login == 'menu_item' && $redirect_to_a_menu_item > 0)
				{
					// Get Joomla! version
					$jversion = new JVersion;
					$short_version = explode('.', $jversion->getShortVersion()); // 3.9.15
					$mini_version = $short_version[0].'.'.$short_version[1]; // 3.9
					
					if (version_compare($mini_version, "4.0", ">="))
					{
						// j4
						$router = Factory::getApplication()->getRouter();
					}
					else
					{
						$router = JApplication::getInstance('site')->getRouter();
					}

					$url = $router->build('index.php?Itemid='.$redirect_to_a_menu_item);
					$url = $url->toString();
					$url_redirect = str_replace('/administrator', '', $url);
				}

				// login as user
				// Default Login - Plugin
				if ($login_system == 'joomla')
				{					
					JPluginHelper::importPlugin('user'); // (plugin/user/joomla/)
					$options = array();
					$options['action'] = 'core.login.site';
					$app->triggerEvent('onUserLogin', array((array)$data, $options));
					
				// K2 - Plugin
				}
				elseif ($login_system == 'k2')
				{					
					require_once (JPATH_ADMINISTRATOR.'/components/com_k2/tables/table.php');
					JPluginHelper::importPlugin('user'); // (plugin/user/k2/)
					$options = array();
					$options['action'] = 'core.login.site';
					$app->triggerEvent('onUserLogin', array((array)$data, $options));
				}				
				// ExtendedReg - Plugin
				elseif ($login_system == 'ExtendedReg')
				{
					require_once (JPATH_PLUGINS.'/user/extendedreguser/extendedreguser.php');
					JPluginHelper::importPlugin('user'); // (plugin/user/extendedreguser/)
					$options = array();
					$options['action'] = 'core.login.site';
					$app->triggerEvent('onUserLogin', array((array)$data, $options));
				}
				
				// insert back the correct last visit date
				$query = 'UPDATE #__users SET lastvisitDate = "'.$lastvisitDate.'" WHERE username='.$db->Quote($username).' AND password=' . $db->Quote($password);
				$db->setQuery($query);
				$db->execute();
				
				// Send a message to Admin, to inform that a user logged in from backend, via 'Login as User' plugin.
				if ($send_message_to_admin)
				{
					// Load the plugin language file
					$lang = JFactory::getLanguage();
					$current_lang_tag = $lang->getTag();
					$extension = 'plg_system_loginasuser';
					$base_dir = JPATH_SITE.'/plugins/system/loginasuser/';
					$language_tag = (!empty($current_lang_tag)) ? $current_lang_tag : 'en-GB';
					$reload = true;
					$lang->load($extension, $base_dir, $language_tag, $reload);

					// Send email
					$mailer = JFactory::getMailer();
					$config = new JConfig();
					$sitename = $config->sitename;
					$email_from = $config->mailfrom;
					$email_fromname = $config->fromname;
					$sender = array($email_from, $email_fromname);
					$mailer->setSender($sender);
					$recipient = (!empty($admin_email) && filter_var($admin_email, FILTER_VALIDATE_EMAIL)) ? $admin_email : $email_from;
					$mailer->addRecipient($recipient);
					$body = JText::_('PLG_LOGINASUSER_EMAIL_BODY');
					$mailer->setSubject(JText::sprintf(JText::_('PLG_LOGINASUSER_EMAIL_SUBJECT'), $username, $sitename));
					$mailer->isHTML(true);
					$mailer->Encoding = 'base64';
					$mailer->setBody($body);
					$mailer->Send();				
				}
				
				// redirect to user profile page
				if ($app->isSite())
				{
					$url_redirect = (!empty($url_redirect)) ? $url_redirect : 'index.php?option=com_users&view=profile';
					$app->redirect(JRoute::_($url_redirect, false));
				}
				elseif ($app->isAdmin())
				{
					$url_redirect = (!empty($url_redirect)) ? $url_redirect : 'index.php?option=com_loginasuser';
					$app->redirect($url_redirect);
				}
			}
		}
	}	
}