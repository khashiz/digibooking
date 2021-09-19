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
    <div class="uk-height-medium uk-background-secondary  uk-padding uk-flex uk-flex-middle uk-flex-right">
        <?php if ($this->params->get('show_page_heading')) : ?>
            <div class="page-header">
                <h1 class="font uk-text-white uk-h2 f500 uk-margin-remove"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
            </div>
        <?php endif; ?>
    </div>
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
        <div></div>
        <div></div>
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

<?php if ($user->requireReset) { ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            UIkit.modal('#resetPassModal').show();
        });
    </script>
    <div id="resetPassModal" class="uk-modal-full" data-uk-modal="esc-close:false">
        <div class="uk-modal-dialog uk-background-primary transparented">
            <div class="uk-padding uk-height-viewport uk-flex uk-flex-middle uk-flex-center">
                <div class="uk-text-center">
                    <h3 class="uk-h3 uk-text-white uk-margin-medium-bottom f500 font"><?php echo JText::sprintf('RESET_PASS_TEXT'); ?></h3>
                    <a href="<?php echo JUri::base().'profile'; ?>" class="uk-button uk-button-primary uk-button-large uk-width-medium uk-margin-small-top" target=""><?php echo JText::sprintf('CHANGE_PASSWORD'); ?>&ensp;<img src="<?php echo JUri::base().'images/sprite.svg#arrow-left-short'; ?>" width="24" height="24" alt="" data-uk-svg></a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>