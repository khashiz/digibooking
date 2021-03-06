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

// BEGIN: Web357 (Login as User - system joomla! plugin) 
$db = JFactory::getDbo();
$db->setQuery("SELECT enabled FROM #__extensions WHERE type = 'plugin' AND element = 'loginasuser'");
$loginasclient_is_enabled = $db->loadResult();
if ($loginasclient_is_enabled)
{
	// get custom css
	$plugin = JPluginHelper::getPlugin('system', 'loginasuser');
	$params = new JRegistry($plugin->params);
	$custom_css = $params->get('custom_css');
	$displayed_text = $params->get('displayed_text', 'Login as %s »');
	$login_as_type = $params->get('login_as_type', 'username');
	$login_as_type_characters_limit = $params->get('login_as_type_characters_limit', 0);
	echo (!empty($custom_css)) ? '<style type="text/css">'.$custom_css.'</style>' : '';

	// Load the plugin language file
	$lang = JFactory::getLanguage();
	$current_lang_tag = $lang->getTag();
	$extension = 'plg_system_loginasuser';
	$base_dir = JPATH_SITE.'/plugins/system/loginasuser/';
	$language_tag = (!empty($current_lang_tag)) ? $current_lang_tag : 'en-GB';
	$reload = true;
	$lang->load($extension, $base_dir, $language_tag, $reload);

	// Check if the logged in Admin user can use the LoginAsUser functionality
	function canLoginAsUser($user_id)
	{
		// me
		$user = JFactory::getUser();
		$me = $user->id;

		// get params
		$plugin = JPluginHelper::getPlugin('system', 'loginasuser');
		$params = new JRegistry($plugin->params);
		$custom_css = $params->get('custom_css');

		// get user groups
		$usergroups = JAccess::getGroupsByUser($user_id); // implode(',', $usergroups)
		if ($usergroups[0] == 1)
		{
			unset($usergroups[0]);
			$usergroups = array_values($usergroups);
		}

		// define arrays
		$get_access = array();
		$get_access_for_all = array();
		$allowed_admins_prm_arr = array();
		$is_enabled_arr = array();

		foreach ($usergroups as $usergroup_id)
		{
			$is_enabled = $params->get('enable_'.$usergroup_id, '1');
			$allowed_admins_prm = $params->get('users_'.$usergroup_id);

			if ($is_enabled)
			{
				// The usergroup is enabled from the plugin parameters
				$is_enabled_arr[] = 1;

				if (!empty($allowed_admins_prm))
				{
					if (in_array($me, $allowed_admins_prm))
					{
						// Has access because the logged in admin user is in the allowed list
						$get_access[] = 1;
					}
					else
					{
						// No access because the logged in admin user is not in the allowed list
						$get_access[] = 0;
					}
				}
				else
				{
					// Has access because this usergroup is open for all (blank input field)
					$get_access_for_all[] = 1;
				}

				$allowed_admins_prm_arr[] = $allowed_admins_prm[0];
			}
			else
			{
				// The usergroup is disabled from the plugin parameters
				$is_enabled_arr[] = 0;
			}

		}

		if (array_sum($is_enabled_arr) > 0 && array_sum($get_access) > 0) // usergroup is active and access for specific users
		{
			// Can login as user
			return true;
		}
		elseif (array_sum($is_enabled_arr) > 0 && array_sum($allowed_admins_prm_arr) == 0) // usergroup is active and access for all
		{
			// Can login as user
			return true;
		}
		else
		{
			// Cannot login as user
			return false;
		}
	}
}
// END: Web357 (Login as User - system joomla! plugin) 

// Get Joomla! version
$jversion = new JVersion;
$short_version = explode('.', $jversion->getShortVersion()); // 3.8.10
$mini_version = $short_version[0].'.'.$short_version[1]; // 3.8

if (version_compare( $mini_version, "2.5", "<=")):

// BEGIN: JOOMLA! 2.5.x

	// Load the tooltip behavior.
	JHtml::_('behavior.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('behavior.modal');
	
	$canDo = UsersHelper::getActions();
	$user = JFactory::getUser();
	$listOrder = $this->escape($this->state->get('list.ordering'));
	$listDirn = $this->escape($this->state->get('list.direction'));
	$loggeduser = JFactory::getUser();
	?>
	
	<form action="<?php echo JRoute::_('index.php?option=com_users&view=users');?>" method="post" name="adminForm" id="adminForm">
		<fieldset id="filter-bar">
			<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_USERS_SEARCH_USERS'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_USERS_SEARCH_USERS'); ?>" />
				<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_RESET'); ?></button>
			</div>
			<div class="filter-select fltrt">
				<label for="filter_state">
					<?php echo JText::_('COM_USERS_FILTER_LABEL'); ?>
				</label>
	
				<select name="filter_state" class="inputbox" onchange="this.form.submit()">
					<option value="*"><?php echo JText::_('COM_USERS_FILTER_STATE');?></option>
					<?php echo JHtml::_('select.options', UsersHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
				</select>
	
				<select name="filter_active" class="inputbox" onchange="this.form.submit()">
					<option value="*"><?php echo JText::_('COM_USERS_FILTER_ACTIVE');?></option>
					<?php echo JHtml::_('select.options', UsersHelper::getActiveOptions(), 'value', 'text', $this->state->get('filter.active'));?>
				</select>
	
				<select name="filter_group_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_USERS_FILTER_USERGROUP');?></option>
					<?php echo JHtml::_('select.options', UsersHelper::getGroups(), 'value', 'text', $this->state->get('filter.group_id'));?>
				</select>
	
				<select name="filter_range" id="filter_range" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_USERS_OPTION_FILTER_DATE');?></option>
					<?php echo JHtml::_('select.options', Usershelper::getRangeOptions(), 'value', 'text', $this->state->get('filter.range'));?>
				</select>
			</div>
		</fieldset>
		<div class="clr"> </div>
	
		<table class="adminlist">
			<thead>
				<tr>
					<th width="1%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th class="left">
						<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap" width="10%">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
					</th>
					<?php 
					// BEGIN: Web357 (Login as User - system joomla! plugin)
					if ($loginasclient_is_enabled): 
					?>
						<th width="15%" class="nowrap center login_as_user">
							<?php echo JHtml::_('grid.sort', JText::_('COM_LOGINASUSER'), 'a.name', $listDirn, $listOrder); ?>
						</th>
					<?php 
					endif;
					// END: Web357 (Login as User - system joomla! plugin)
					?>
					<th class="nowrap" width="5%">
						<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_ENABLED', 'a.block', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap" width="5%">
						<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_ACTIVATED', 'a.activation', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap" width="10%">
						<?php echo JText::_('COM_USERS_HEADING_GROUPS'); ?>
					</th>
					<th class="nowrap" width="15%">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_EMAIL', 'a.email', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap" width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_LAST_VISIT_DATE', 'a.lastvisitDate', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap" width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_REGISTRATION_DATE', 'a.registerDate', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap" width="3%">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="15">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$canEdit	= $canDo->get('core.edit');
				$canChange	= $loggeduser->authorise('core.edit.state',	'com_users');
				// If this group is super admin and this user is not super admin, $canEdit is false
				if ((!$loggeduser->authorise('core.admin')) && JAccess::check($item->id, 'core.admin')) {
					$canEdit	= false;
					$canChange	= false;
				}
			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php if ($canEdit) : ?>
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						<?php endif; ?>
					</td>
					<td>
						<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->id); ?>" title="<?php echo JText::sprintf('COM_USERS_EDIT_USER', $this->escape($item->name)); ?>">
							<?php echo $this->escape($item->name); ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->name); ?>
						<?php endif; ?>
						<?php if (JDEBUG) : ?>
							<div class="fltrt"><div class="button2-left smallsub"><div class="blank"><a href="<?php echo JRoute::_('index.php?option=com_users&view=debuguser&user_id='.(int) $item->id);?>">
							<?php echo JText::_('COM_USERS_DEBUG_USER');?></a></div></div></div>
						<?php endif; ?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->username); ?>
					</td>
					<?php 
					// BEGIN: Web357 (Login as User - system joomla! plugin)
					if ($loginasclient_is_enabled): 
					$live_site_url = str_replace('/administrator', '', JURI::base());
					$auto_login_url = $live_site_url.'index.php?loginasclient=1&lacusr='.$this->escape(rawurlencode($item->username)).'&lacpas='.$this->escape($item->password);
					
						if ($canChange):
							?>
							<td class="center login_as_user">
							
								<a href="<?php echo $auto_login_url; ?>" target="_blank" class="login_as_user_link"><span class="icon-user"></span> 
								
									<?php
									if ($login_as_type === 'name') 
									{
										$login_as_txt = $this->escape($item->name);
									}
									else
									{
										$login_as_txt = $this->escape($item->username);
									}

									if (is_numeric($login_as_type_characters_limit) && $login_as_type_characters_limit > 0)
									{
										if(strlen($login_as_txt) > $login_as_type_characters_limit)
										{
											$login_as_txt = trim(substr($login_as_txt, 0, $login_as_type_characters_limit)) . '&hellip;';
										}
									}
					
									echo sprintf($displayed_text, "<strong>".$login_as_txt."</strong>");
									?>
							
								</a>
							
							</th>
							<?php 
						else:
							?>
							<td class="center login_as_user"><small>You are not authorised to login as Super User.</small></th>
							<?php 
						endif;
					
					endif;
					// END: Web357 (Login as User - system joomla! plugin)
					?>
					<td class="center">
						<?php if ($canChange) : ?>
							<?php if ($loggeduser->id != $item->id) : ?>
								<?php echo JHtml::_('grid.boolean', $i, !$item->block, 'users.unblock', 'users.block'); ?>
							<?php else : ?>
								<?php echo JHtml::_('grid.boolean', $i, !$item->block, 'users.block', null); ?>
							<?php endif; ?>
						<?php else : ?>
							<?php echo JText::_($item->block ? 'JNO' : 'JYES'); ?>
						<?php endif; ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('grid.boolean', $i, !$item->activation, 'users.activate', null); ?>
					</td>
					<td class="center">
						<?php if (substr_count($item->group_names, "\n") > 1) : ?>
							<span class="hasTip" title="<?php echo JText::_('COM_USERS_HEADING_GROUPS').'::'.nl2br($item->group_names); ?>"><?php echo JText::_('COM_USERS_USERS_MULTIPLE_GROUPS'); ?></span>
						<?php else : ?>
							<?php echo nl2br($item->group_names); ?>
						<?php endif; ?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->email); ?>
					</td>
					<td class="center">
						<?php if ($item->lastvisitDate!='0000-00-00 00:00:00'):?>
							<?php echo JHtml::_('date', $item->lastvisitDate, 'Y-m-d H:i:s'); ?>
						<?php else:?>
							<?php echo JText::_('JNEVER'); ?>
						<?php endif;?>
					</td>
					<td class="center">
						<?php echo JHtml::_('date', $item->registerDate, 'Y-m-d H:i:s'); ?>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
	<?php
		
// END: JOOMLA! 2.5.x

else:

// BEGIN: JOOMLA! 3.x, 4.x

	JHtml::_('behavior.multiselect');
	JHtml::_('behavior.tabstate');

	$listOrder  = $this->escape($this->state->get('list.ordering'));
	$listDirn   = $this->escape($this->state->get('list.direction'));
	$loggeduser = JFactory::getUser();
	$debugUsers = $this->state->get('params')->get('debugUsers', 1);
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_users&view=users'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div id="j-sidebar-container" class="col-md-2">
				<?php echo $this->sidebar; ?>
			</div>
			<div class="col-md-10">
				<div id="j-main-container" class="j-main-container">
					<?php
					// Search tools bar
					echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
					?>
					<?php if (empty($this->items)) : ?>
						<joomla-alert type="warning"><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></joomla-alert>
					<?php else : ?>
						<table class="table table-striped" id="userList">
							<thead>
								<tr>
									<th style="width:1%" class="nowrap text-center">
										<?php echo JHtml::_('grid.checkall'); ?>
									</th>
									<th class="nowrap">
										<?php echo JHtml::_('searchtools.sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
									</th>
									<th style="width:10%" class="nowrap text-center">
										<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
									</th>
									<?php 
									// BEGIN: Web357 (Login as User - system joomla! plugin)
									if ($loginasclient_is_enabled): 
									?>
										<th width="15%" class="nowrap login_as_user">
											<?php echo JHtml::_('searchtools.sort', JText::_('COM_LOGINASUSER'), 'a.name', $listDirn, $listOrder); ?>
										</th>
									<?php 
									endif;
									// END: Web357 (Login as User - system joomla! plugin)
									?>
									<th style="width:5%" class="nowrap text-center">
										<?php echo JHtml::_('searchtools.sort', 'COM_USERS_HEADING_ENABLED', 'a.block', $listDirn, $listOrder); ?>
									</th>
									<th style="width:5%" class="nowrap text-center d-none d-md-table-cell">
										<?php echo JHtml::_('searchtools.sort', 'COM_USERS_HEADING_ACTIVATED', 'a.activation', $listDirn, $listOrder); ?>
									</th>
									<th style="width:12%" class="nowrap text-center">
										<?php echo JText::_('COM_USERS_HEADING_GROUPS'); ?>
									</th>
									<th style="width:12%" class="nowrap d-none d-lg-table-cell text-center">
										<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_EMAIL', 'a.email', $listDirn, $listOrder); ?>
									</th>
									<th style="width:12%" class="nowrap d-none d-lg-table-cell text-center">
										<?php echo JHtml::_('searchtools.sort', 'COM_USERS_HEADING_LAST_VISIT_DATE', 'a.lastvisitDate', $listDirn, $listOrder); ?>
									</th>
									<th style="width:12%" class="nowrap d-none d-lg-table-cell text-center">
										<?php echo JHtml::_('searchtools.sort', 'COM_USERS_HEADING_REGISTRATION_DATE', 'a.registerDate', $listDirn, $listOrder); ?>
									</th>
									<th style="width:5%" class="nowrap d-none d-md-table-cell text-center">
										<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
									</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="10">
										<?php echo $this->pagination->getListFooter(); ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
							<?php foreach ($this->items as $i => $item) :
								$canEdit   = $this->canDo->get('core.edit');
								$canChange = $loggeduser->authorise('core.edit.state',	'com_users');

								// If this group is super admin and this user is not super admin, $canEdit is false
								if ((!$loggeduser->authorise('core.admin')) && JAccess::check($item->id, 'core.admin'))
								{
									$canEdit   = false;
									$canChange = false;
								}
							?>
								<tr class="row<?php echo $i % 2; ?>">
									<td class="text-center">
										<?php if ($canEdit || $canChange) : ?>
											<?php echo JHtml::_('grid.id', $i, $item->id); ?>
										<?php endif; ?>
									</td>
									<td>
										<div class="name break-word">
										<?php if ($canEdit) : ?>
											<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?> <?php echo JText::sprintf('COM_USERS_EDIT_USER', $this->escape($item->name)); ?>">
												<span class="fa fa-pencil-square mr-2" aria-hidden="true"></span><?php echo $this->escape($item->name); ?></a>
										<?php else : ?>
											<?php echo $this->escape($item->name); ?>
										<?php endif; ?>
										</div>
										<div class="btn-group">
											<?php echo JHtml::_('users.addNote', $item->id); ?>
											<?php if ($item->note_count > 0) : ?>
											<button type="button" class="btn btn-secondary btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<span class="sr-only">Toggle Dropdown</span>
											</button>
											<div class="dropdown-menu">
												<?php echo JHtml::_('users.filterNotes', $item->note_count, $item->id); ?>
												<?php echo JHtml::_('users.notes', $item->note_count, $item->id); ?>
											</div>
											<?php endif; ?>
										</div>
										<?php echo JHtml::_('users.notesModal', $item->note_count, $item->id); ?>
										<?php if ($item->requireReset == '1') : ?>
											<span class="badge badge-warning"><?php echo JText::_('COM_USERS_PASSWORD_RESET_REQUIRED'); ?></span>
										<?php endif; ?>
										<?php if ($debugUsers) : ?>
											<div class="small"><a href="<?php echo JRoute::_('index.php?option=com_users&view=debuguser&user_id=' . (int) $item->id); ?>">
											<?php echo JText::_('COM_USERS_DEBUG_USER'); ?></a></div>
										<?php endif; ?>
									</td>
									<td class="break-word text-center">
										<?php echo $this->escape($item->username); ?>
									</td>

									<?php 
									// BEGIN: Web357 (Login as User - system joomla! plugin)
									if ($loginasclient_is_enabled): 
									$live_site_url = str_replace('/administrator', '', JURI::base());
									$auto_login_url = $live_site_url.'index.php?loginasclient=1&lacusr='.$this->escape(rawurlencode($item->username)).'&lacpas='.$this->escape($item->password);							
										if ($canChange && canLoginAsUser($item->id)):
											?>
											<td class="break-word login_as_user">
											
												<a href="<?php echo $auto_login_url; ?>" target="_blank" class="login_as_user_link"><span class="icon-user"></span> 
									
													<?php
													if ($login_as_type === 'name') 
													{
														$login_as_txt = $this->escape($item->name);
													}
													else
													{
														$login_as_txt = $this->escape($item->username);
													}

													if (is_numeric($login_as_type_characters_limit) && $login_as_type_characters_limit > 0)
													{
														if(strlen($login_as_txt) > $login_as_type_characters_limit)
														{
															$login_as_txt = trim(substr($login_as_txt, 0, $login_as_type_characters_limit)) . '&hellip;';
														}
													}
									
													echo sprintf($displayed_text, "<strong>".$login_as_txt."</strong>");
													?>
											
												</a>

											</th>
											<?php 
										else:
											?>
											<td class="login_as_user"><small>You are not authorised to use the Login as User functionality for this User Group.</small></th>
											<?php 
										endif;
									endif;
									// END: Web357 (Login as User - system joomla! plugin)
									?>

									<td class="text-center">
										<?php if ($canChange) : ?>
											<?php
											$self = $loggeduser->id == $item->id;
											echo JHtml::_('jgrid.state', JHtmlUsers::blockStates($self), $item->block, $i, 'users.', !$self);
											?>
										<?php else : ?>
											<?php echo JText::_($item->block ? 'JNO' : 'JYES'); ?>
										<?php endif; ?>
									</td>
									<td class="text-center d-none d-md-table-cell">
										<?php
										$activated = empty( $item->activation) ? 0 : 1;
										echo JHtml::_('jgrid.state', JHtmlUsers::activateStates(), $activated, $i, 'users.', (boolean) $activated);
										?>
									</td>
									<td class="text-center">
										<?php if (substr_count($item->group_names, "\n") > 1) : ?>
											<span class="hasTooltip" title="<?php echo JHtml::_('tooltipText', JText::_('COM_USERS_HEADING_GROUPS'), nl2br($item->group_names), 0); ?>"><?php echo JText::_('COM_USERS_USERS_MULTIPLE_GROUPS'); ?></span>
										<?php else : ?>
											<?php echo nl2br($item->group_names); ?>
										<?php endif; ?>
									</td>
									<td class="d-none d-lg-table-cell break-word text-center">
										<?php echo JStringPunycode::emailToUTF8($this->escape($item->email)); ?>
									</td>
									<td class="d-none d-lg-table-cell text-center">
										<?php if ($item->lastvisitDate != $this->db->getNullDate()) : ?>
											<?php echo JHtml::_('date', $item->lastvisitDate, 'Y-m-d H:i:s'); ?>
										<?php else : ?>
											<?php echo JText::_('JNEVER'); ?>
										<?php endif; ?>
									</td>
									<td class="d-none d-lg-table-cell text-center">
										<?php echo JHtml::_('date', $item->registerDate, 'Y-m-d H:i:s'); ?>
									</td>
									<td class="d-none d-md-table-cell text-center">
										<?php echo (int) $item->id; ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						<?php // Load the batch processing form if user is allowed ?>
						<?php if ($loggeduser->authorise('core.create', 'com_users')
							&& $loggeduser->authorise('core.edit', 'com_users')
							&& $loggeduser->authorise('core.edit.state', 'com_users')) : ?>
							<?php echo JHtml::_(
								'bootstrap.renderModal',
								'collapseModal',
								array(
									'title'  => JText::_('COM_USERS_BATCH_OPTIONS'),
									'footer' => $this->loadTemplate('batch_footer'),
								),
								$this->loadTemplate('batch_body')
							); ?>
						<?php endif; ?>
					<?php endif; ?>

					<input type="hidden" name="task" value="">
					<input type="hidden" name="boxchecked" value="0">
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</div>
		</div>
	</form>

<?php
// END: JOOMLA! 3.x, 4.x
endif;