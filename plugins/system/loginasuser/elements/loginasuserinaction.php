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

jimport('joomla.form.formfield');

class JFormFieldloginasuserinaction extends JFormField {
	
	protected $type = 'loginasuserinaction';

	protected function getInput()
	{
		return ' ';
	}
	
	protected function getLabel()
	{
		$more_description = '<p><a href="index.php?option=com_loginasuser&plg=loginasuser" class="btn btn-default btn-warning"><strong>To Login as User, visit Component\'s page and click on <em>Login as Username</em> &raquo;</strong></a></p>';
		return $more_description;		
	}
}