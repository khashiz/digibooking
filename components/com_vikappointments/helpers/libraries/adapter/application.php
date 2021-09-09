<?php
/** 
 * @package   	VikAppointments
 * @subpackage 	com_vikappointments
 * @author    	Matteo Galletti - e4j
 * @copyright 	Copyright (C) 2019 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license  	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link 		https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// this should be already loaded from autoload.php
UILoader::import('libraries.adapter.version.listener');

/**
 * Helper class used to adapt the application to the requirements
 * of the installed platform version.
 *
 * @see 	VersionListener 	Used to evaluate the current platform version.
 *
 * @since  	1.6
 */
abstract class UIApplication
{
	/**
	 * The instance to handle the singleton.
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Used to keep a single instance of the object.
	 *
	 * @return 	self 	The class singleton.
	 */
	public static function getInstance()
	{
		if (static::$instance === null)
		{
			// get current platform
			$platform = VersionListener::getPlatform();

			if (!$platform)
			{
				throw new Exception('Platform not supported', 404);
			}

			// try to include the application platform
			if (!UILoader::import('libraries.adapter.platform.' . $platform))
			{
				throw new Exception(sprintf('Application [%s] not found', $platform), 404);
			}

			// define class name
			$class = 'UIApplication' . ucfirst($platform);

			// check if the class exists and inherits UIApplication abstraction
			if (class_exists($class) && is_subclass_of($class, 'UIApplication'))
			{
				static::$instance = new $class();
			}
		}

		return static::$instance;
	}
	
	/**
	 * Backward compatibility for admin list <table> class.
	 *
	 * @return 	string 	The class selector to use.
	 */
	abstract public function getAdminTableClass();
	
	/**
	 * Backward compatibility for admin list <table> head opening.
	 *
	 * @return 	string 	The <thead> tag to use.
	 */
	abstract public function openTableHead();
	
	/**
	 * Backward compatibility for admin list <table> head closing.
	 *
	 * @return 	string 	The </thead> tag to use.
	 */
	abstract public function closeTableHead();
	
	/**
	 * Backward compatibility for admin list <th> class.
	 *
	 * @param 	string 	$align 	The additional class to use for horizontal alignment.
	 *							Accepted rules should be: left, center or right.
	 *
	 * @return 	string 	The class selector to use.
	 */
	abstract public function getAdminThClass($align = 'center');
	
	/**
	 * Backward compatibility for admin list checkAll JS event.
	 *
	 * @param 	integer  The total count of rows in the table.	
	 *
	 * @return 	string 	 The check all checkbox input to use.
	 */
	abstract public function getAdminToggle($count);
	
	/**
	 * Backward compatibility for admin list isChecked JS event.
	 *
	 * @return 	string 	The JS function to use.
	 */
	abstract public function checkboxOnClick();

	/**
	 * Helper method to send e-mails.
	 *
	 * @param 	string 	 $from_address	The e-mail address of the sender.
	 * @param 	string 	 $from_name 		The name of the sender.
	 * @param 	string 	 $to 			The e-mail address of the receiver.
	 * @param 	string 	 $reply_address 	The reply to e-mail address.
	 * @param 	string 	 $subject 		The subject of the e-mail.
	 * @param 	string 	 $hmess 			The body of the e-mail (HTML is supported).
	 * @param 	array 	 $attachments 	The list of the attachments to include.
	 * @param 	boolean  $is_html 		True to support HTML body, otherwise false for plain text.
	 * @param 	string 	 $encoding 		The encoding to use.
	 *
	 * @return 	boolean  True if the e-mail was sent successfully, otherwise false.
	 */
	public function sendMail($from_address, $from_name, $to, $reply_address, $subject, $hmess, $attachments = null, $is_html = true, $encoding = 'base64')
	{
		$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
		
		if ($is_html)
		{
			$hmess = "<html>\n<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head>\n<body>$hmess</body>\n</html>";
		}
		
		$mailer = JFactory::getMailer();

		$sender = array($from_address, $from_name);
		$mailer->setSender($sender);
		$mailer->addRecipient($to);
		$mailer->addReplyTo($reply_address);
		$mailer->setSubject($subject);
		$mailer->setBody($hmess);
		$mailer->isHTML($is_html);

		$mailer->Encoding = $encoding;

		if ($attachments !== null && is_array($attachments))
		{
			foreach ($attachments as $attach)
			{
				if (!empty($attach) && file_exists($attach))
				{
					$mailer->addAttachment($attach);
				}
			}
		}

		return $mailer->Send();
	}
	
	/**
	 * Backward compatibility for add script.
	 *
	 * @param   string  $file     Path to file.
	 * @param   array   $options  Array of options. Example: array('version' => 'auto', 'conditional' => 'lt IE 9').
	 * @param   array   $attribs  Array of attributes. Example: array('id' => 'scriptid', 'async' => 'async', 'data-test' => 1).
	 */
	abstract public function addScript($file = '', $options = array(), $attribs = array());
	
	/**
	 * Backward compatibility for framework loading.
	 *
	 * @param 	string 	$fw  The framework to load. 
	 */
	abstract public function loadFramework($fw = '');
	
	/**
	 * Backward compatibility for punycode conversion.
	 *
	 * @param 	string 	$mail 	The e-mail to convert in punycode.
	 *
	 * @return 	string 	The punycode conversion of the e-mail.
	 *
	 * @since 	1.4
	 */
	public function emailToPunycode($email = '')
	{
		// do nothing by default
		return $email;
	}
	
	/**
	 * Helper method to build a input radio object.
	 *
	 * @param 	string 		$id 		The id of the input.
	 * @param 	string 		$label 		The text of the label.
	 * @param 	boolean 	$checked 	True if the input is checked.
	 * @param 	string 		$htmlAttr 	The additional html attributes to include.
	 *
	 * @return 	object 		The object to represent radio inputs. 
	 *
	 * @since 	1.2
	 */
	public function initRadioElement($id = '', $label = '', $checked = false, $htmlAttr = '')
	{
		$elem = new stdClass();
		$elem->id 		= $id;
		$elem->label 	= $label;
		$elem->checked 	= $checked;
		$elem->htmlAttr = $htmlAttr;

		return $elem;
	}
	
	/**
	 * Helper method to build a select <option> object.
	 *
	 * @param 	string 		$value 		The value of the option.
	 * @param 	string 		$label 		The text of the option.
	 * @param 	boolean 	$selected 	True if the option is selected.
	 * @param 	boolean 	$isoptgrp 	True if the option is a <optgroup> tag.
	 * @param 	boolean 	$disabled 	True if the option is disabled.
	 * @param 	string 		$htmlAttr 	The additional html attributes to include.
	 *
	 * @return 	object 		The object to represent select options. 
	 *
	 * @since 	1.2
	 */
	public function initOptionElement($value, $label, $selected = false, $isoptgrp = false, $disabled = false, $htmlAttr = '')
	{
		$elem = new stdClass();
		$elem->value 	= $value;
		$elem->label 	= $label;
		$elem->selected = $selected;
		$elem->isoptgrp = $isoptgrp;
		$elem->disabled = $disabled;
		$elem->htmlAttr = $htmlAttr;

		return $elem;
	}
	
	/**
	 * Helper method to build a select <optgroup> object.
	 *
	 * @param 	string 	$label 	The text of the optgroup.
	 *
	 * @return 	object 	The object to represent select optgroups.
	 *
	 * @uses 	initOptionElement()  Create an optgroup starting from an option.
	 *
	 * @since 	1.2
	 */
	public function getDropdownGroup($label)
	{
		return $this->initOptionElement('', $label, 0, true);
	}
	
	/**
	 * Helper method to build a tiny YES/NO radio button.
	 *
	 * @param 	string 		$name 		The name of the input.
	 * @param 	object 		$elem_1 	The first input object.
	 * @param 	object 		$elem_2 	The second input object.
	 * @param 	boolean 	wrapped 	True if the input is wrapped in a control class, otherwise false..
	 *
	 * @return 	string 		The html to display.
	 *
	 * @since 	1.2
	 */
	public function radioYesNo($name, $elem_1, $elem_2, $wrapped = true, $layout = null)
	{
		$elements 	= array($elem_1, $elem_2);
		$options 	= array('wrapped' => $wrapped);

		//

		if (!$layout)
		{
			// if not specified, get default layout from config
			$layout = UIFactory::getConfig()->get('uiradio', 'ios');
		}
		
		// load radio widget
		UILoader::import('libraries.widget.radio.' . $layout);

		$radio_class = 'UIRadio' . ucwords($layout);

		//

		$radio = new $radio_class($name, $elements, $options);

		return $radio->display();	
	}

	/**
	 * Helper method to build a normal HTML select.
	 *
	 * @param 	string 	$name 		The name of the select.
	 * @param 	array 	$elems 		The list containing all the option objects.
	 * @param 	string 	$id 		The ID attribute of the select.
	 * @param 	string 	$class 		The class attribute of the select.
	 * @param 	string 	htmlAttr 	The additional html attributes to include.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.2
	 */
	public function dropdown($name, $elems, $id = '', $class = '', $htmlAttr = '')
	{
		$first = true;
		$select = '<select name="'.$name.'" id="'.$id.'" class="vik-dropdown '.$class.'" '.$htmlAttr.'>';

		foreach ($elems as $elem)
		{
			if (!$elem->isoptgrp)
			{
				$selected = $elem->selected ? ' selected="selected"' : '';
				$disabled = $elem->disabled ? ' disabled' : '';

				$select .= '<option value="' . $elem->value . '"' . $selected . $disabled . ' ' . $elem->htmlAttr . '>' . $elem->label . '</option>';
			}
			else
			{
				if (!$first)
				{
					$select .= '</optgroup>';
				}

				$select .= '<optgroup label="' . $elem->label . '">';
				$first = false;
			}
		}

		if (!$first)
		{
			$select .= '</optgroup>';
		}

		$select .= '</select>';

		return $select;
	}
	
	/**
	 * Backward compatibility for fieldset opening.
	 *
	 * @param 	string 	$legend  The title of the fieldset.
	 * @param 	string 	$class 	 The class attribute for the fieldset.
	 * @param 	string 	$id 	 The ID attribute for the fieldset.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	abstract public function openFieldset($legend, $class = '', $id = '');
	
	/**
	 * Backward compatibility for fieldset closing.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	abstract public function closeFieldset();

	/**
	 * Backward compatibility for empty fieldset opening.
	 *
	 * @param 	string 	$class 	An additional class to use for the fieldset.
	 * @param 	string 	$id 	The ID attribute for the fieldset.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	abstract public function openEmptyFieldset($class = '', $id = '');
	
	/**
	 * Backward compatibility for empty fieldset opening.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	abstract public function closeEmptyFieldset();
	
	/**
	 * Backward compatibility for control opening.
	 *
	 * @param 	string 	$label 	The label of the control field.
	 * @param 	string 	$class 	The class of the control field.
	 * @param 	mixed 	$attr 	The additional attributes to add (string or array).
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	abstract public function openControl($label, $class = '', $attr = '');
	
	/**
	 * Backward compatibility for control closing.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	abstract public function closeControl();
	
	/**
	 * Returns the codemirror editor.
	 *
	 * @param 	string 	$name 	The name of the textarea.
	 * @param 	string 	$value 	The value of the textarea.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	abstract public function getCodeMirror($name, $value);

	/**
	 * Prepares the editor scripts for being used.
	 *
	 * @param 	string 	$name 	The name of the editor.
	 *
	 * @return 	void
	 *
	 * @since 	1.6.3
	 */
	public function prepareEditor($name)
	{
		// do nothing by default
	}
	
	/**
	 * Backward compatibility for Bootstrap tabset opening.
	 *
	 * @param 	string 	$group 	The group of the tabset.
	 * @param 	string 	$attr 	The attributes to use.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	abstract public function bootStartTabSet($group, $attr = array());
	
	/**
	 * Backward compatibility for Bootstrap tabset closing.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	abstract public function bootEndTabSet();
	
	/**
	 * Backward compatibility for Bootstrap add tab.
	 *
	 * @param 	string 	$group 	The tabset parent group.
	 * @param 	string 	$id 	The id of the tab.
	 * @param 	string 	$label 	The title of the tab.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	abstract public function bootAddTab($group, $id, $label);
	
	/**
	 * Backward compatibility for Bootstrap end tab.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	abstract public function bootEndTab();
	
	/**
	 * Backward compatibility for Bootstrap open modal JS event.
	 *
	 * @param 	string 	$onclose 	The javascript function to call on close event.
	 *
	 * @return 	string 	The javascript function.
	 *
	 * @since 	1.5
	 */
	abstract public function bootOpenModalJS($onclose = '');
	
	/**
	 * Backward compatibility for Bootstrap dismiss modal JS event.
	 *
	 * @param 	string 	$selector 	The selector to identify the modal box.
	 *
	 * @return 	string 	The javascript function.
	 *
	 * @since 	1.5
	 */
	abstract public function bootDismissModalJS($selector);

	/**
	 * Backward compatibility to fit the layout of the left main menu.
	 *
	 * @param 	JDocument 	$document 	The base document.
	 *
	 * @since 	1.5
	 */
	public function fixContentPadding($document = null)
	{

	}

	/**
	 * Add javascript support for Bootstrap popovers.
	 *
	 * @param 	string 	$selector   Selector for the popover.
	 * @param 	array 	$options    An array of options for the popover.
	 * 					Options for the popover can be:
	 * 						animation  boolean          apply a css fade transition to the popover
	 *                      html       boolean          Insert HTML into the popover. If false, jQuery's text method will be used to insert
	 *                                                  content into the dom.
	 *                      placement  string|function  how to position the popover - top | bottom | left | right
	 *                      selector   string           If a selector is provided, popover objects will be delegated to the specified targets.
	 *                      trigger    string           how popover is triggered - hover | focus | manual
	 *                      title      string|function  default title value if `title` tag isn't present
	 *                      content    string|function  default content value if `data-content` attribute isn't present
	 *                      delay      number|object    delay showing and hiding the popover (ms) - does not apply to manual trigger type
	 *                                                  If a number is supplied, delay is applied to both hide/show
	 *                                                  Object structure is: delay: { show: 500, hide: 100 }
	 *                      container  string|boolean   Appends the popover to a specific element: { container: 'body' }
	 *
	 * @since 	1.6
	 */
	abstract public function attachPopover($selector = '.vapPopover', array $options = array());

	/**
	 * Create a standard tag and attach a popover event.
	 * NOTE. FontAwesome framework MUST be loaded in order to work.
	 *
	 * @param 	array 	$options     An array of options for the popover.
	 *
	 * @see 	UIApplication::attachPopover() for further details about options keys.
	 *
	 * @since 	1.6
	 */
	abstract public function createPopover(array $options = array());

	/**
	 * Create a text span and attach a popover event.
	 *
	 * @param 	array 	$options    An array of options for the popover.
	 *
	 * @see 	UIApplication::attachPopover() for further details about options keys.
	 *
	 * @since 	1.6
	 */
	abstract public function textPopover(array $options = array());

	/**
	 * Return the date format specs.
	 *
	 * @param 	string 	$format 	  The format to use.
	 * @param 	array 	&$attributes  Some attributes to use.
	 *
	 * @return 	string 	The adapted date format.
	 *
	 * @since 	1.6
	 */
	abstract public function jdateFormat($format = null, array &$attributes = array());

	/**
	 * Provides support to handle the calendar across different frameworks.
	 *
	 * @param 	mixed 	$value 		 The date or the timestamp to fill.
	 * @param 	string 	$name 		 The input name.
	 * @param 	string 	$id 		 The input id attribute.
	 * @param 	string 	$format 	 The date format.
	 * @param 	array 	$attributes  Some attributes to use.
	 * 
	 * @return 	string 	The calendar field.
	 *
	 * @since 	1.6
	 */
	abstract public function calendar($value, $name, $id = null, $format = null, array $attributes = array());

	/**
	 * Returns a masked e-mail address. The e-mail are masked using 
	 * a technique to encode the bytes in hexadecimal representation.
	 * The chunk of the masked e-mail will be also encoded to be HTML readable.
	 *
	 * @param 	string 	 $email 	The e-mail to mask.
	 * @param 	boolean  $reverse 	True to reverse the e-mail address.
	 * 								Only if the e-mail is not contained into an attribute.
	 *
	 * @return 	string 	 The masked e-mail address.
	 *
	 * @since 	1.6
	 */
	public function maskMail($email, $reverse = false)
	{
		if ($reverse)
		{
			// reverse the e-mail address
			$email = strrev($email);
		}

		// converts the e-mail address from bin to hex
		$email = bin2hex($email);
		// append ;&#x sequence after every chunk of the masked e-mail
		$email = chunk_split($email, 2, ";&#x");
		// prepend &#x sequence before the address and trim the ending sequence
		$email = "&#x" . substr($email, 0, -3);

		return $email;
	}

	/**
	 * Returns a safemail tag to avoid the bots spoof a plain address.
	 *
	 * @param 	string 	 $email 	The e-mail address to mask.
	 * @param 	boolean  $mail_to 	True if the address should be wrapped
	 * 								within a "mailto" link.
	 *
	 * @return 	string 	 The HTML tag containing the masked address.
	 *
	 * @uses 	maskMail()
	 *
	 * @since 	1.6
	 */
	public function safeMailTag($email, $mail_to = false)
	{
		// include the CSS declaration to reverse the text contained in the <safemail> tags
		JFactory::getDocument()->addStyleDeclaration('safemail {direction: rtl;unicode-bidi: bidi-override;}');

		// mask the reversed e-mail address
		$masked = $this->maskMail($email, true);

		// include the address into a custom <safemail> tag
		$tag = "<safemail>$masked</safemail>";

		if ($mail_to)
		{
			// mask the address for mailto command (do not use reverse)
			$mailto = $this->maskMail($email);

			// wrap the safemail tag within a mailto link
			$tag = "<a href=\"mailto:$mailto\" class=\"mailto\">$tag</a>";
		}

		return $tag;
	}

	/**
	 * Method used to obtain a media form field.
	 *
	 * @return 	string 	The media in HTML.
	 *
	 * @since 	1.6
	 */
	abstract public function getMediaField($name, $value = null, array $data = array());

	/**
	 * Method used to handle the reCAPTCHA events.
	 *
	 * @param 	string 	$event 		The reCAPTCHA event to trigger.
	 * 								Here's the list of the accepted events:
	 * 								- display 	Returns the HTML used to 
	 *											display the reCAPTCHA input.
	 *								- check 	Validates the POST data to make sure
	 * 											the reCAPTCHA input was checked.
	 * @param 	array  	$options 	A configuration array.
	 *
	 * @return 	mixed 	The event response.
	 *
	 * @since 	1.6
	 */
	abstract public function reCaptcha($event = 'display', array $options = array());

	/**
	 * Checks if the com_user captcha is configured.
	 * In case the parameter is set to global, the default one
	 * will be retrieved.
	 * 
	 * @param 	string 	 $plugin  The plugin name to check ('recaptcha' by default).
	 *
	 * @return 	boolean  True if configured, otherwise false.
	 *
	 * @since 	1.6
	 */
	abstract public function isCaptcha($plugin = 'recaptcha');

	/**
	 * Checks if the global captcha is configured.
	 * 
	 * @param 	string 	 $plugin  The plugin name to check ('recaptcha' by default).
	 *
	 * @return 	boolean  True if configured, otherwise false.
	 *
	 * @since 	1.6
	 */
	abstract public function isGlobalCaptcha($plugin = 'recaptcha');

	/**
	 * Rewrites an internal URI that needs to be used outside of the website.
	 * This means that the routed URI MUST start with the base path of the site.
	 *
	 * @param 	mixed 	 $query 	The query string or an associative array of data.
	 * @param 	boolean  $xhtml  	Replace & by &amp; for XML compliance.
	 * @param 	mixed 	 $itemid 	The itemid to use. If null, the current one will be used.
	 *
	 * @return 	string 	 The complete routed URI.
	 *
	 * @since 	1.6
	 */
	abstract public function routeForExternalUse($query = '', $xhtml = true, $itemid = null);

	/**
	 * Routes an admin URL for being used outside from the website (complete URI).
	 *
	 * @param 	mixed 	 $query 	The query string or an associative array of data.
	 * @param 	boolean  $xhtml  	Replace & by &amp; for XML compliance.
	 *
	 * @return 	string 	The complete routed URI. 
	 *
	 * @since 	1.6.3
	 */
	abstract public function adminUrl($query = '', $xhtml = true);

	/**
	 * Returns an helper class suffix used to adjust the layout of the component
	 * depending on the theme used by the template (e.g. light or dark).
	 *
	 * @param   mixed   $suffix  The rules to affect (array or string).
	 * 							 Null to use all the existing rules.
	 * 							 Here's the list of supported rules:
	 * 							 - background 	used when it is needed to change the background color;
	 * 							 - color 		used when it is needed to change the foreground color.
	 *
	 * @return  string  The string containing the classes to use.
	 *
	 * @since   1.6
	 */
	public function getThemeClass($suffix = null)
	{
		// a list of allowed suffix
		$lookup = array(
			'background',
			'color',
		);

		if (is_null($suffix))
		{
			// use all supported suffix
			$suffix = $lookup;
		}
		else if (!is_array($suffix))
		{
			// use only the specified suffix
			$suffix = array($suffix);
		}

		// get theme setting (light or dark)
		$theme = UIFactory::getConfig()->get('sitetheme', '');
		$class = array();

		if ($theme)
		{
			// iterate the specified list
			foreach ($suffix as $tmp)
			{
				// if supported, push the class suffix within the list
				if (in_array($tmp, $lookup))
				{
					$class[] = $theme . '-theme-' . $tmp;
				}
			}
		}

		// join the classes with a blank space
		return implode(' ', $class);
	}


	/**
	 * Helper method to pre-load the assets needed for the Employee Area.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	public function loadEmployeeAreaAssets()
	{
		$document = JFactory::getDocument();
		// css
		$document->AddStyleSheet(VAPASSETS_URI . 'css/vap-emparea.css');
		VikAppointments::load_font_awesome();

		// js
		VikAppointments::load_css_js();
		VikAppointments::load_fancybox();
		VikAppointments::load_complex_select();
		VikAppointments::load_utils();
		VikAppointments::load_currency_js();
		$this->addScript(VAPASSETS_URI . 'js/jquery-ui.sortable.min.js');
		$this->addScript(VAPASSETS_URI . 'js/vap-emparea.js');

		$document->addScriptDeclaration(
<<<JS
var EmployeeArea = new EmployeeArea('#empareaForm');

jQuery(document).ready(function() {
	jQuery('.vap-list-pagination .hasTooltip').removeClass('hasTooltip').removeAttr('title').attr('data-original-title', '');
});
JS
		);
	}

	/**
	 * Returns a list of users that are currently logged-in.
	 *
	 * @param 	mixed 	 $limit   The query limit, if specified.
	 * @param 	integer  $offset  The query offset, if specified.
	 *
	 * @return 	array 	 A list of users.
	 *
	 * @since 	1.6.3
	 */
	abstract public function getLoggedUsers($limit = null, $offset = 0);

	/**
	 * Prepares the specified content before being displayed.
	 *
	 * @param 	mixed 	 &$content  The table content instance.
	 * @param 	boolean  &full 		True to return the full description.
	 * 								False to return the short description, if any.
	 *
	 * @return 	void
	 *
	 * @since 	1.6.3
	 */
	abstract public function onContentPrepare(&$content, $full = true);

	/**
	 * Returns a list of supported payment gateways.
	 *
	 * @return 	array 	A list of paths.
	 *
	 * @since 	1.6.3
	 */
	abstract public function getPaymentDrivers();

	/**
	 * Returns the configuration form of a payment.
	 *
	 * @param 	string 	$payment  The name of the payment.
	 *
	 * @return 	mixed 	The configuration array/object.
	 *
	 * @since 	1.6.3
	 */
	abstract public function getPaymentConfig($payment);

	/**
	 * Provides a new payment instance for the specified arguments.
	 *
	 * @param 	string 	  $payment 	The name of the payment that should be instantiated.
	 * @param 	mixed 	  $order 	The details of the order that has to be paid.
	 * @param 	mixed 	  $config 	The payment configuration array or a JSON string.
	 *
	 * @return 	mixed 	  The payment instance.
	 *
	 * @throws 	RuntimeException
	 *
	 * @since 	1.6.3
	 */
	abstract public function getPaymentInstance($payment, $order = array(), $config = array());

	/**
	 * Checks whether the reservation can be completed.
	 *
	 * @param 	VAPCartItem  $item        The item to book.
	 * @param 	array        $service     The details of the service.
	 *
	 * @return  integer      The ID of the employee found, false otherwise.
	 *
	 * @throws  RuntimeException  An exception might be thrown in case of
	 * 							  unexcpected behaviors, such as a time in
	 * 							  the past or an invalid license.
	 *
	 * @since   1.6.3
	 */
	abstract public function checkAvailability(VAPCartItem $item, array $service);

	/**
	 * Returns the component manufacturer name or link.
	 * Children that inherits this method must pass the default values to
	 * use to build the manufacturer name. This can be done by pushing within the
	 * $options array the 'manufacturer' key, which should contain the following keys:
	 * 'link', 'short' and 'long'.
	 *
	 * @param 	array   $options  An array of options:
	 * 							  - link (boolean) True to return a link, false to return the
	 * 								name only (false by default);
	 *							  - short (boolean) True to display the short manufacturer name,
	 * 								false otherwise (false by default);
	 * 							  - long (boolean) True to display the long manufacturer name,
	 * 								false otherwise (true by default);
	 * 							  - separator (string) A separator string to insert between the
	 * 								names fetched ('-' by default).
	 *
	 * @return  string  The manufacturer name or link.
	 *
	 * @since   1.6.3
	 */
	public function getManufacturer(array $options = array())
	{
		$defaults = array(
			'link'      => false,
			'short'     => false,
			'long'      => true,
			'separator' => '-',
		);

		// merge specified options within default values
		$options = array_merge($defaults, $options);

		$parts = array();

		// look for short name
		if ($options['short'] && isset($options['manufacturer']['short']))
		{
			$parts[] = $options['manufacturer']['short'];
		}

		// look for long name
		if ($options['long'] && isset($options['manufacturer']['long']))
		{
			$parts[] = $options['manufacturer']['long'];
		}

		// make sure the separator is not a blank space
		if (trim($options['separator']))
		{
			// add an empty space at the beginning and at the end
			$options['separator'] = ' ' . $options['separator'] . ' ';
		}
		else
		{
			// use an empty space otherwise
			$options['separator'] = ' ';
		}

		if ($parts)
		{
			// implode the manufacturer chunks by using the specified separator
			$str = implode($options['separator'], $parts);
		}
		else if (isset($options['manufacturer']['link']))
		{
			// use the specified link as fallback
			$str = $options['manufacturer']['link'];
		}
		else
		{
			// return empty string as we don't have anything to display
			return '';
		}

		// check if we should wrap the name within a link
		if ($options['link'])
		{
			// check if we have a custom link
			if (!is_string($options['link']))
			{
				// the link is not a string, use a default value
				if (isset($options['manufacturer']['link']))
				{
					$options['link'] = $options['manufacturer']['link'];
				}
				else
				{
					// do not use a link as we don't have a valid URI
					return $str;
				}
			}

			// build HTML link tag
			$str = sprintf(
				'<a href="%s" target="_blank">%s</a>',
				$options['link'],
				$str
			);
		}

		return $str;
	}
}
