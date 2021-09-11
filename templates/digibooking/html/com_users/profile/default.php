<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="profile<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>
	<?php if (JFactory::getUser()->id == $this->data->id) : ?>
		<ul class="btn-toolbar pull-right">
			<li class="btn-group">
				<a class="btn" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>">
					<span class="icon-user"></span>
					<?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?>
				</a>
			</li>
		</ul>
	<?php endif; ?>
    <div class="uk-child-width-1-1" data-uk-grid>
        <div>aaaaaa</div>
        <div>aaaaaa</div>
    </div>
</div>
<?php
$user = JFactory::getUser();

// Get a db connection.
$db = JFactory::getDbo();
// Create a new query object.
$query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
$query->select($db->quoteName(array('user_id', 'profile_key', 'profile_value', 'ordering')));
$query->from($db->quoteName('#__user_profiles'));
$query->where($db->quoteName('profile_key') . ' LIKE ' . $db->quote('custom.%'));
$query->order('ordering ASC');

// Reset the query using our newly populated query object.
$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$results = $db->loadObjectList();
?>
<?php /* echo $this->loadTemplate('core'); ?>
<?php echo $this->loadTemplate('params'); ?>
<?php echo $this->loadTemplate('custom'); */ ?>
