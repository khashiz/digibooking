<?php
/**
 * @package    PlgUserCMAvatar
 * @copyright  Copyright (C) 2015 CMExtension Team http://www.cmext.vn/
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('file');

/**
 * Form field for avatar.
 *
 * @package  PlgUserCMAvatar
 * @since    1.0.0
 */
class JFormFieldCMAvatar extends JFormFieldFile
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'CMAvatar';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0.0
	 */
	protected function getInput()
	{
        $user = JFactory::getUser();
		if (empty($this->value))
		{
			$currentAvatar = '<div class="container"><div class="uk-grid-small avatar-upload" data-uk-grid><div class="avatar-preview"><div id="imagePreview" class="uk-background-muted uk-border-rounded" style="background-image: url('.JUri::base().'/templates/digibooking/img/person-bounding-box.svg);"></div></div><div class="uk-flex uk-flex-middle avatar-edit"><div><span class="uk-text-secondary uk-display-block font">'.$user->name.'</span><label for="jform_cmavatar_cmavatar" class="uk-text-tiny uk-text-muted font">'.JText::sprintf('EDIT_USER_AVATAR').'</label></div></div></div></div>';
		}
		else
		{
            $currentAvatar = '<div class="container"><div class="uk-grid-small avatar-upload" data-uk-grid><div class="avatar-preview"><div id="imagePreview" class="uk-background-muted uk-border-rounded" style="background-image: url(' . $this->value . ');"></div></div><div class="uk-flex uk-flex-middle avatar-edit"><div><span class="uk-text-secondary uk-display-block font">'.$user->name.'</span><label for="jform_cmavatar_cmavatar" class="uk-text-tiny uk-text-muted font">'.JText::sprintf('EDIT_USER_AVATAR').'</label></div></div></div></div>';
		}

		$uploadField = parent::getInput();

		if (empty($this->value))
		{
			$deleteField = '';
		}
		else
		{
			$deleteField = '<input type="checkbox" value="yes" name="delete-avatar" id="deleteAvatar">';
		}

		$data = array(
			'current_avatar'	=> $currentAvatar,
			'upload_field'		=> $uploadField,
			'delete_field'		=> $deleteField,
		);

		$layout = new JLayoutFile('default', $basePath = JPATH_PLUGINS . '/user/cmavatar/layouts');
		$html = $layout->render($data);

		return $html;
	}
}
