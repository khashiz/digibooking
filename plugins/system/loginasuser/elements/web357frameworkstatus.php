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
defined('JPATH_BASE') or die;
		
jimport('joomla.form.formfield');
jimport( 'joomla.form.form' );

class JFormFieldweb357frameworkstatus extends JFormField {
	
	protected $type = 'web357frameworkstatus';

	protected function getLabel()
	{
		// BEGIN: Check if Web357 Framework plugin exists
		jimport('joomla.plugin.helper');
		if(!JPluginHelper::isEnabled('system', 'web357framework')):
			return JText::_('<div style="border:1px solid red; padding:10px; width: 50%"><strong style="color:red;">The Web357 Framework Plugin is unpublished.</strong><br>It should be enabled to assign multiple Admins to speific User Groups. Please, enable the plugin first and then try to navigate to this tab again!</div>');
		else:
			return '';	
		endif;
		// END: Check if Web357 Framework plugin exists
	}

	protected function getInput() 
	{
		return '';
	}
	
}