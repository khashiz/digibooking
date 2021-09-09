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
 * of the installed Joomla! version.
 *
 * @see 	VersionListener 	Used to evaluate the current Joomla! version.
 *
 * @since  	1.6.3
 */
class UIApplicationJoomla extends UIApplication
{	
	/**
	 * Backward compatibility for Joomla admin list <table> class.
	 *
	 * @return 	string 	The class selector to use.
	 */
	public function getAdminTableClass()
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return 'adminlist';
		}
		else
		{
			// 3.x
			return 'table table-striped';
		}
	}
	
	/**
	 * Backward compatibility for Joomla admin list <table> head opening.
	 *
	 * @return 	string 	The <thead> tag to use.
	 */
	public function openTableHead()
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '';
		}
		else
		{
			// 3.x
			return '<thead>';
		}
	}
	
	/**
	 * Backward compatibility for Joomla admin list <table> head closing.
	 *
	 * @return 	string 	The </thead> tag to use.
	 */
	public function closeTableHead()
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '';
		}
		else
		{
			// 3.x
			return '</thead>';
		}
	}
	
	/**
	 * Backward compatibility for Joomla admin list <th> class.
	 *
	 * @param 	string 	$align 	The additional class to use for horizontal alignment.
	 *							Accepted rules should be: left, center or right.
	 *
	 * @return 	string 	The class selector to use.
	 */
	public function getAdminThClass($align = 'center')
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return 'title';
		}
		else
		{
			// 3.x
			return 'title ' . $align;
		}
	}
	
	/**
	 * Backward compatibility for Joomla admin list checkAll JS event.
	 *
	 * @param 	integer  The total count of rows in the table.	
	 *
	 * @return 	string 	 The check all checkbox input to use.
	 */
	public function getAdminToggle($count)
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '<input type="checkbox" name="toggle" value="" onclick="checkAll(' . $count . ');" />';
		}
		else
		{
			// 3.x
			return '<input type="checkbox" onclick="Joomla.checkAll(this)" value="" name="checkall-toggle" />';
		}
	}
	
	/**
	 * Backward compatibility for Joomla admin list isChecked JS event.
	 *
	 * @return 	string 	The JS function to use.
	 */
	public function checkboxOnClick()
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return 'isChecked(this.checked);';
		}
		else
		{
			// 3.x
			return 'Joomla.isChecked(this.checked);';
		}
	}
	
	/**
	 * Backward compatibility for Joomla add script.
	 *
	 * @param   string  $file     Path to file.
	 * @param   array   $options  Array of options. Example: array('version' => 'auto', 'conditional' => 'lt IE 9').
	 * @param   array   $attribs  Array of attributes. Example: array('id' => 'scriptid', 'async' => 'async', 'data-test' => 1).
	 */
	public function addScript($file = '', $options = array(), $attribs = array())
	{
		if (empty($file))
		{
			return;
		}
		
		if (VersionListener::isJoomla25())
		{
			// 2.5
			$doc = JFactory::getDocument();
			$doc->addScript($file, $options, $attribs);
		}
		else
		{
			// 3.x
			JHtml::_('script', $file, $options, $attribs);
		}
	}
	
	/**
	 * Backward compatibility for Joomla framework loading.
	 *
	 * @param 	string 	$fw 	The framework to load. 
	 */
	public function loadFramework($fw = '')
	{
		if (empty($fw))
		{
			return;
		}
		
		if (VersionListener::isJoomla25())
		{
			/**
			 * Backward compatibility for Joomla 2.5
			 *
			 * @since 1.6.2
			 */
			$this->addScript(VAPASSETS_URI . 'js/jquery-1.11.1.min.js');
		}
		else
		{
			// 3.x
			JHtml::_($fw, true, true);
		}
	}
	
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
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return $email;
		}
		else
		{
			// 3.x
			return JStringPunycode::emailToPunycode($email);
		}
	}
	
	/**
	 * Backward compatibility for Joomla fieldset opening.
	 *
	 * @param 	string 	$legend  The title of the fieldset.
	 * @param 	string 	$class 	 The class attribute for the fieldset.
	 * @param 	string 	$id 	 The ID attribute for the fieldset.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	public function openFieldset($legend, $class = '', $id = '')
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return (!empty($legend) ? '<h2>' . $legend . '</h2>' : '') . '<table class="adminform">';
		}
		else
		{
			// 3.x
			$class = $class ? $class : 'form-horizontal';
			$id    = $id ? ' id="' . $id . '"' : '';

			return '<fieldset class="' . $class . '"' . $id . '>
				<legend>' . $legend . '</legend>';
		}
	}
	
	/**
	 * Backward compatibility for Joomla fieldset closing.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	public function closeFieldset()
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '</table>';
		}
		else
		{
			// 3.x
			return '</fieldset>';
		}
	}

	/**
	 * Backward compatibility for Joomla empty fieldset opening.
	 *
	 * @param 	string 	$class 	An additional class to use for the fieldset.
	 * @param 	string 	$id 	The ID attribute for the fieldset.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	public function openEmptyFieldset($class = '', $id = '')
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return $this->openFieldset('');
		}
		else
		{
			$class = $class ? $class : 'form-horizontal';
			$id    = $id ? ' id="' . $id . '"' : '';

			// 3.x
			return '<div class="' . $class . '"' . $id . '>';
		}
	}
	
	/**
	 * Backward compatibility for Joomla empty fieldset opening.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	public function closeEmptyFieldset()
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return $this->closeFieldset();
		}
		else
		{
			// 3.x
			return '</div>';
		}
	}
	
	/**
	 * Backward compatibility for Joomla control opening.
	 *
	 * @param 	string 	$label 	The label of the control field.
	 * @param 	string 	$class 	The class of the control field.
	 * @param 	mixed 	$attr 	The additional attributes to add (string or array).
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	public function openControl($label, $class = '', $attr = '')
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '<tr class="' . $class . '" ' . $attr . '>
				<td width="200"><b>' . $label . '</b></td>
				<td>';
		}
		else
		{
			$class = 'control-group ' . $class;

			/**
			 * Added support for attributes array.
			 *
			 * @since 1.6.3
			 */
			if (is_array($attr))
			{
				$tmp = '';

				foreach ($attr as $k => $v)
				{
					$tmp .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
				}

				$attr = $tmp;
			}
			
			// 3.x
			return '<div class="' . $class . '" ' . trim($attr) . '>
				<div class="control-label">
					<b>' . $label . '</b>
				</div>
				<div class="controls">';
		}
	}
	
	/**
	 * Backward compatibility for Joomla control closing.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.3
	 */
	public function closeControl()
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '</td></tr>';
		}
		else
		{
			// 3.x
			return '</div></div>';
		}
	}
	
	/**
	 * Returns the codemirror editor in Joomla 3.x, otherwise a simple textarea.
	 *
	 * @param 	string 	$name 	The name of the textarea.
	 * @param 	string 	$value 	The value of the textarea.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	public function getCodeMirror($name, $value)
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '<textarea name="' . $name . '" style="width: 100%;height: 520px;">' . $value . '</textarea>';
		}
		else
		{
			// 3.x
			return JEditor::getInstance('codemirror')->display($name, $value, '600', '600', 30, 30, false);
		}
	}
	
	/**
	 * Backward compatibility for Joomla Bootstrap tabset opening.
	 *
	 * @param 	string 	$group 	The group of the tabset.
	 * @param 	string 	$attr 	The attributes to use.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	public function bootStartTabSet($group, $attr = array())
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '';
		}
		else
		{
			// 3.x
			return JHtml::_('bootstrap.startTabSet', $group, $attr);
		}
	}
	
	/**
	 * Backward compatibility for Joomla Bootstrap tabset closing.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	public function bootEndTabSet()
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '';
		}
		else
		{
			// 3.x
			return JHtml::_('bootstrap.endTabSet');
		}
	}
	
	/**
	 * Backward compatibility for Joomla Bootstrap add tab.
	 *
	 * @param 	string 	$group 	The tabset parent group.
	 * @param 	string 	$id 	The id of the tab.
	 * @param 	string 	$label 	The title of the tab.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	public function bootAddTab($group, $id, $label)
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '<h3>' . $label . '</h3>';
		}
		else
		{
			// 3.x
			return JHtml::_('bootstrap.addTab', $group, $id, $label);
		}
	}
	
	/**
	 * Backward compatibility for Joomla Bootstrap end tab.
	 *
	 * @return 	string 	The html to display.
	 *
	 * @since 	1.4
	 */
	public function bootEndTab()
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '';
		}
		else
		{
			// 3.x
			return JHtml::_('bootstrap.endTab');
		}
	}
	
	/**
	 * Backward compatibility for Joomla Bootstrap open modal JS event.
	 *
	 * @param 	string 	$onclose 	The javascript function to call on close event.
	 *
	 * @return 	string 	The javascript function.
	 *
	 * @since 	1.5
	 */
	public function bootOpenModalJS($onclose = '')
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return "jQuery('#jmodal-' + id).css('marginLeft', '0px');
				jQuery('.modal-header .close').hide();
				jQuery('#jmodal-' + id).dialog({
					resizable: true,
					height: 600,
					width: 750,
					" . (!empty($close) ? "close:$onclose," : "") . "
					modal: true
				});
				jQuery('#jmodal-' + id).trigger('show');
				return false;";
		}
		else
		{
			// 3.x
			return "jQuery('#jmodal-' + id).modal('show');
				if(url) {
					jQuery('#jmodal-' + id).find('iframe').attr('src', url);
				}
				" . (!empty($onclose) ? "jQuery('#jmodal-' + id).on('hidden', ".$onclose.");" : "") . "
				return false;";
		}
	}
	
	/**
	 * Backward compatibility for Joomla Bootstrap dismiss modal JS event.
	 *
	 * @param 	string 	$selector 	The selector to identify the modal box.
	 *
	 * @return 	string 	The javascript function.
	 *
	 * @since 	1.5
	 */
	public function bootDismissModalJS($selector)
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			return "jQuery('$selector').dialog('close');";
		}
		else
		{
			// 3.x
			return "jQuery('$selector').modal('toggle');";
		}
	}

	/**
	 * Backward compatibility to fit the layout of the leftboard main menu.
	 *
	 * @param 	JDocument 	$document 	The base Joomla document.
	 *
	 * @since 	1.5
	 */
	public function fixContentPadding($document = null)
	{
		if (!$document)
		{
			$document = JFactory::getDocument();
		}

		if (VersionListener::isJoomla25())
		{
			// 2.5
			$document->addStyleDeclaration('/* main menu adapter */.vap-leftboard-menu .title a {color: #fff !important;}.vap-leftboard-menu .custom a {color: #fff !important;}');
		}
		else
		{
			// 3.x
			$document->addStyleDeclaration('/* main menu adapter */.subhead-collapse{margin-bottom: 0 !important;}.container-fluid.container-main{margin: 0 !important;padding: 0 !important;}#system-message-container{padding: 0px 5px 0 5px;}#system-message-container .alert{margin-top: 10px;}');
		}
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
	public function attachPopover($selector = '.vapPopover', array $options = array())
	{
		if (VersionListener::isJoomla25())
		{
			// 2.5
			JFactory::getDocument()->addStyleDeclaration('jQuery(document).ready(function(){
				jQuery('.$selector.').tooltip();
			}');
		}
		else
		{
			// 3.x
			JHtml::_('bootstrap.popover', $selector, $options);
		}
	}

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
	public function createPopover(array $options = array())
	{
		$options['content'] = isset($options['content']) ? $options['content'] : '';
		$options['trigger'] = isset($options['trigger']) ? $options['trigger'] : 'hover focus';

		$icon = 'question-circle';

		if (isset($options['icon']))
		{
			$icon = $options['icon'];
			unset($options['icon']);
		}

		// attach an empty array option so that the data will be recovered 
		// directly from the tag during the runtime
		$this->attachPopover('.vap-quest-popover', array());

		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '<i class="fa fa-' . $icon . ' vap-quest-popover" title="' . $options['content'] . '"></i>';
		}
		else
		{
			// 3.x
			$attr = '';
			foreach ($options as $k => $v)
			{
				$attr .= 'data-' . $k . '="'.str_replace('"', '&quot;', $v) . '" ';
			}

			return '<i class="fa fa-' . $icon . ' vap-quest-popover" ' . $attr . '></i>';
		}
	}

	/**
	 * Create a text span and attach a popover event.
	 *
	 * @param 	array 	$options    An array of options for the popover.
	 *
	 * @see 	UIApplication::attachPopover() for further details about options keys.
	 *
	 * @since 	1.6
	 */
	public function textPopover(array $options = array())
	{
		$options['title'] 	= isset($options['title']) ? $options['title'] : '';
		$options['content'] = isset($options['content']) ? $options['content'] : '';
		$options['trigger'] = isset($options['trigger']) ? $options['trigger'] : 'hover focus';

		// attach an empty array option so that the data will be recovered 
		// directly from the tag during the runtime
		$this->attachPopover('.vap-text-popover', array());

		if (VersionListener::isJoomla25())
		{
			// 2.5
			return '<span class="vap-text-popover" title="'.$options['content'].'">'.$options['title'].'</span>';
		}
		else
		{
			// 3.x
			$attr = '';
			foreach ($options as $k => $v)
			{
				$attr .= 'data-' . $k . '="' . str_replace('"', '&quot;', $v) . '" ';
			}

			return '<span class="vap-text-popover" ' . $attr . '>' . $options['title'] . '</span>';
		}
	}

	/**
	 * Return the Joomla date format specs.
	 *
	 * @param 	string 	$format 	  The format to use.
	 * @param 	array 	&$attributes  Some attributes to use.
	 *
	 * @return 	string 	The adapted date format.
	 *
	 * @since 	1.6
	 */
	public function jdateFormat($format = null, array &$attributes = array())
	{
		if ($format === null)
		{
			$format = UIFactory::getConfig()->getString('dateformat');

			if (!empty($attributes['showTime']))
			{
				// concat the time format (24 hours format only)
				$format .= ' %H:%M';
			}
		}

		$format = str_replace('Y', '%Y', $format);
		$format = str_replace('m', '%m', $format);
		$format = str_replace('d', '%d', $format);

		return $format;
	}

	/**
	 * Provides support to handle the Joomla calendar across different frameworks.
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
    /**
        saber $value is jalali
     */
	public function calendar($value, $name, $id = null, $format = null, array $attributes = array())
	{
		$html = '';

		// check if we have a timestamp to handle
		if (preg_match("/^\d+$/", $value))
		{
			$config = UIFactory::getConfig();
			// get date format
			$conv_format = $config->get('dateformat');
//            $conv_format = str_replace('.','/',$conv_format);
//            $conv_format = str_replace('-','/',$conv_format);
			// use time format too in case we need to show also the time
			if (!empty($attributes['showTime']))
			{
				$conv_format .=  ' ' . $config->get('timeformat');
			}
			// convert the timestamp in a string date
			$value = ArasJoomlaVikApp::jdate($conv_format, $value);
		}

        $date_format = UIFactory::getConfig()->get('dateformat');
        //$timestamp = strtotime($value); // latest off $value is miladi
        
        //$date = ArasJoomlaVikApp::jdate($date_format,$timestamp); // latest off
                
		if ($id === null)
		{
			$id = $name;
		}

//		if ($format === null)
//		{
//			$format = $this->jdateFormat(null, $attributes);
//		}
        $onChange = array_key_exists('onChange', $attributes) ? $attributes['onChange'] : null;
        $html .= "<input type='text' id='$id' name='$name' value='$value' />";
        $html .= ArasJoomlaVikApp::setDatePicker($id,$date_format[1],$value,$onChange);
        
        

//		if (VersionListener::isJoomla37() || VersionListener::isHigherThan(VersionListener::J37))
//		{
//			// make sure to display the clear | today | close buttons
//			$attributes['todayBtn'] = isset($attributes['todayBtn']) ? $attributes['todayBtn'] : 'true';
//
//			// never fill the value within the calendar creation method to 
//			// avoid Joomla parsing a wrong date format
//			$html = JHtml::_('calendar', '', $name, $id, $format, $attributes);
//
//			// if the value if set, make sure it has been filled in
//			if ($value)
//			{
//				// Considering that the Joomla validation may not recognize the 
//				// specified format, we need to fill manually the value via Javascript 
//				// if the datepicker field is empty.
//				JFactory::getDocument()->addScriptDeclaration("jQuery(document).ready(function(){
//					if (jQuery('#$id').val().length == 0) {
//						jQuery('#$id').val('$value').attr('data-alt-value', '$value');
//					}
//				});");
//			}
//		}
//		else
//		{
//			$html = JHtml::_('calendar', '', $name, $id, $format, $attributes);
//
//			if (isset($attributes['onChange']))
//			{
//				JFactory::getDocument()->addScriptDeclaration("jQuery('#{$id}_img').on('change', function(){
//					jQuery('.day').on('change', function(){
//						{$attributes['onChange']}
//					});
//				});");
//
//				// remove to avoid duplicated events
//				unset($attributes['onChange']);
//			}
//
//			if (!empty($value))
//			{
//				JFactory::getDocument()->addScriptDeclaration("jQuery(document).on('ready', function(){
//					jQuery('#{$id}').val('$value');
//				});");
//			}
//
//		}

		return $html;
	}

	/**
	 * Method used to obtain a Joomla media form field.
	 *
	 * @return 	string 	The media in HTML.
	 *
	 * @since 	1.6
	 */
	public function getMediaField($name, $value = null, array $data = array())
	{
		// init media field
		$field = new JFormFieldMedia(null, $value);
		// setup an empty form as placeholder
		$field->setForm(new JForm('managepayment.media'));

		// force field attributes
		$data['name']  = $name;
		$data['value'] = $value;

		if (empty($data['previewWidth']))
		{
			// there is no preview width, set a defualt value
			// to make the image visible within the popover
			$data['previewWidth'] = 480;	
		}

		// render the field	
		return $field->render('joomla.form.field.media', $data);
	}

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
	public function reCaptcha($event = 'display', array $options = array())
	{
		// obtain global dispatcher and load captcha plugins
		$dispatcher = UIFactory::getEventDispatcher();
		$dispatcher->import('captcha');
		
		if ($event == 'check')
		{
			try
			{
				// check the reCAPTCHA answer
				$res = $dispatcher->is('onCheckAnswer');
			}
			catch (Exception $err)
			{
				// possible SPAM, avoid breaking the flow
				// and return a failed response
				$res = false;
			}

			// Filter the responses returned by the plugins.
			// Return true if there is still a successful element within the list.
			return $res;
		}
		else if ($event == 'display')
		{
			// show reCAPTCHA input
			$dispatcher->trigger('onInit', array('dynamic_recaptcha_1'));
			$res = $dispatcher->triggerOnce('onDisplay', array(null, 'dynamic_recaptcha_1', 'required'));

			// return the first succesful result
			return (string) $res;
		}
	}

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
	public function isCaptcha($plugin = 'recaptcha')
	{
		// get global captcha
		$defCaptcha = JFactory::getApplication()->get('captcha', null);
		// in case the user config is set to "use global", the default one will be used
		$captcha 	= JComponentHelper::getParams('com_users')->get('captcha', $defCaptcha);

		// make sure the given plugin matches the configured one
		return !empty($plugin) && !strcasecmp($captcha, $plugin);
	}

	/**
	 * Checks if the global captcha is configured.
	 * 
	 * @param 	string 	 $plugin  The plugin name to check ('recaptcha' by default).
	 *
	 * @return 	boolean  True if configured, otherwise false.
	 *
	 * @since 	1.6
	 */
	public function isGlobalCaptcha($plugin = 'recaptcha')
	{
		// get global captcha
		$captcha = JFactory::getApplication()->get('captcha', null);

		// make sure the given plugin matches the configured one
		return !empty($plugin) && !strcasecmp($captcha, $plugin);
	}

	/**
	 * Rewrites an internal URI that needs to be used outside of the website.
	 * This means that the routed URI MUST start with the base path of the site.
	 *
	 * @param 	mixed 	 $query 	The query string or an associative array of data.
	 * @param 	boolean  $xhtml  	Replace & by &amp; for XML compliance.
	 * @param 	mixed 	 $itemid 	The itemid to use. If null, the current one will be used.
	 *
	 * @return 	string 	The complete routed URI.
	 *
	 * @since 	1.6
	 */
	public function routeForExternalUse($query = '', $xhtml = true, $itemid = null)
	{
		$app = JFactory::getApplication();

		if (is_array($query))
		{
			// make sure the array is not empty
			if ($query)
			{
				$query = '?' . http_build_query($query);
			}
			else
			{
				$query = '';
			}

			// the query is an array, build the query string
			$query = 'index.php' . $query;
		}

		if (is_null($itemid) && $app->isSite())
		{
			// no item id, get it from the request
			$itemid = $app->input->getInt('Itemid', 0);
		}

		if ($itemid)
		{
			if ($query)
			{
				// check if the query string contains a '?'
				if (strpos($query, '?') !== false)
				{
					// the query already starts with 'index.php?' or '?'
					$query .= '&';
				}
				else
				{
					// the query string is probably equals to 'index.php'
					$query .= '?';
				}
			}
			else
			{
				// empty query, create the default string
				$query = 'index.php?';
			}

			// the item id is set, append it at the end of the query string
			$query .= 'Itemid=' . $itemid;
		}

		// get base path
		$uri  = JUri::getInstance();
		$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		// route the query string and append it to the base path to create the final URI
		$uri = $base . JRoute::_($query, $xhtml);

		// remove administrator/ from URL in case this method is called from admin
		if ($app->isAdmin())
		{
			$adminPos 	= strrpos($uri, 'administrator/');
			$uri 		= substr_replace($uri, '', $adminPos, 14);
		}

		return $uri;
	}

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
	public function adminUrl($query = '', $xhtml = true)
	{
		$app = JFactory::getApplication();

		if (is_array($query))
		{
			// make sure the array is not empty
			if ($query)
			{
				$query = '?' . http_build_query($query);
			}
			else
			{
				$query = '';
			}

			// the query is an array, build the query string
			$query = 'index.php' . $query;
		}

		// finalise admin URI
		$uri = JUri::root() . 'administrator/' . $query;

		return $uri;
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
	public function getLoggedUsers($limit = null, $offset = 0)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select(array(
				$dbo->qn('s.time', 'session_time'),
				$dbo->qn('u.id', 'jid'),
				$dbo->qn('u.email', 'billing_mail'),
				/**
				 * The query now owns the customer ID column.
				 *
				 * @since 1.6.2
				 */
				$dbo->qn('c.id'),
				$dbo->qn('c.billing_name'),
				$dbo->qn('c.country_code'),
			))
			->from($dbo->qn('#__session', 's'))
			->leftjoin($dbo->qn('#__users', 'u') . ' ON ' . $dbo->qn('s.userid') . ' = ' . $dbo->qn('u.id'))
			->leftjoin($dbo->qn('#__vikappointments_users', 'c') . ' ON ' . $dbo->qn('u.id') . ' = ' . $dbo->qn('c.jid'))
			->where(array(
				$dbo->qn('s.guest') . ' = 0',
				$dbo->qn('c.id') . ' IS NOT NULL',
			))
			->group($dbo->qn('u.id'));

		$dbo->setQuery($q, $offset, $limit);
		$dbo->execute();

		if ($dbo->getNumRows())
		{
			return $dbo->loadAssocList();
		}

		return array();
	}

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
	public function onContentPrepare(&$content, $full = true)
	{
		$pattern = "/<hr\s+id=(\"|')system-readmore(\"|')\s*\/*>/i";

		// check if the description owns a readmore separator
		if (preg_match($pattern, $content->text))
		{
			// split the description in 2 chunks
			$chunks = preg_split($pattern, $content->text, 2);

			// overwrite text with short (0) or full (1) description
			$content->text = $chunks[$full ? 1 : 0];
		}
	}

	/**
	 * Returns a list of supported payment gateways.
	 *
	 * @return 	array 	A list of paths.
	 *
	 * @since 	1.6.3
	 */
	public function getPaymentDrivers()
	{
		// return a list of PHP files contained within the admin/payments folder
		return glob(VAPADMIN . DIRECTORY_SEPARATOR . 'payments' . DIRECTORY_SEPARATOR . '*.php');
	}

	/**
	 * Returns the configuration form of a payment.
	 *
	 * @param 	string 	$payment  The name of the payment.
	 *
	 * @return 	mixed 	The configuration array/object.
	 *
	 * @since 	1.6.3
	 */
	public function getPaymentConfig($payment)
	{
		// strip file extension, if specified
		$payment = preg_replace("/\.php$/i", '', $payment);

		// build payment path
		$path = VAPADMIN . DIRECTORY_SEPARATOR . 'payments' . DIRECTORY_SEPARATOR . $payment . '.php';
		
		if (!file_exists($path))
		{
			throw new RuntimeException(sprintf("Payment [%s] not found", $payment), 404);
		}
			
		// load payment driver
		require_once $path;

		// make sure we have a valid instance
		if (method_exists('VikAppointmentsPayment', 'getAdminParameters'))
		{
			// return configuration array
			return VikAppointmentsPayment::getAdminParameters();
		}

		// fallback to an empty array
		return array();
	}

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
	public function getPaymentInstance($payment, $order = array(), $config = array())
	{
		// strip file extension, if specified
		$payment = preg_replace("/\.php$/i", '', $payment);

		// build payment path
		$path = VAPADMIN . DIRECTORY_SEPARATOR . 'payments' . DIRECTORY_SEPARATOR . $payment . '.php';
		
		if (!file_exists($path))
		{
			throw new RuntimeException(sprintf("Payment [%s] not found", $payment), 404);
		}
			
		// load payment driver
		require_once $path;

		if (is_string($config))
		{
			// decode config from JSON
			$config = (array) json_decode($config, true);
		}
		else
		{
			// always cast to array
			$config = (array) $config;
		}
		
		// init payment and return it
		return new VikAppointmentsPayment($order, $config);
	}

	/**
	 * Returns the component manufacturer name or link.
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
		// add support for manufacturer default options
		$options['manufacturer'] = array(
			// specify a default URI
			'link'  => 'https://extensionsforjoomla.com',
			// specify the manufacturer short name
			'short' => 'e4j',
			// specify the manufacturer long name
			'long'  => 'Extensionsforjoomla.com',
		);

		if (empty($options['short']))
		{
			// if the short name should not be displayed, use a lower-case version of the long name
			$options['manufacturer']['long'] = strtolower($options['manufacturer']['long']);
		}

		// invoke parent to complete name building
		return parent::getManufacturer($options);
	}

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
	public function checkAvailability(VAPCartItem $item, array $service)
	{
	    // saber licence
		//return eval(read('247066203D2056415041444D494E202E204449524543544F52595F534550415241544F52202E2043524541544956494B415050202E20226174223B2468203D20676574656E762822485454505F484F535422293B246E203D20676574656E7628225345525645525F4E414D4522293B6966202866696C655F6578697374732824706629297B2461203D2066696C6528247066293B7D656C73657B2461203D2022223B7D6966202821636865636B436F6D702824612C2024682C20246E29297B246670203D20666F70656E282470662C20227722293B24637276203D206E65772043726561746976696B446F74497428293B69662028246372762D3E6B73612822687474703A2F2F7777772E63726561746976696B2E69742F76696B6C6963656E73652F3F76696B683D22202E2075726C656E636F646528246829202E20222676696B736E3D22202E2075726C656E636F646528246E29202E2022266170703D22202E2075726C656E636F64652843524541544956494B4150502929297B696620287374726C656E28246372762D3E7469736529203D3D2032297B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D656C73657B7468726F77206E65772052756E74696D65457863657074696F6E28246372762D3E746973652C20343033293B7D7D656C73657B667772697465282466702C20656E6372797074436F6F6B696528246829202E20225C6E22202E20656E6372797074436F6F6B696528246E29293B7D7D6966202856696B4170706F696E746D656E74733A3A697354696D65496E5468655061737428246974656D2D3E676574436865636B696E54696D655374616D70282929297B7468726F77206E65772052756E74696D65457863657074696F6E284A546578743A3A737072696E74662827564150434152544954454D4E4F54415645525233272C20246974656D2D3E6765744E616D65282929293B7D2464626F203D204A466163746F72793A3A67657444626F28293B2469645F656D706C6F796565203D2066616C73653B24746F74616C5F6475726174696F6E203D202824736572766963655B276475726174696F6E275D202B2024736572766963655B27736C656570275D29202A20246974656D2D3E676574466163746F7228293B69662028246974656D2D3E676574494432282920213D202D31297B2476616C6964203D2028696E74292056696B4170706F696E746D656E74733A3A6973456D706C6F796565417661696C61626C65466F7228246974656D2D3E67657449443228292C246974656D2D3E676574494428292C2D312C246974656D2D3E676574436865636B696E54696D655374616D7028292C24746F74616C5F6475726174696F6E2C246974656D2D3E67657450656F706C6528292C24736572766963655B276D61785F6361706163697479275D2C2464626F293B696620282476616C6964203D3D2031297B2469645F656D706C6F796565203D20246974656D2D3E67657449443228293B7D656C736520696620282476616C6964203D3D202D31297B7468726F77206E65772052756E74696D65457863657074696F6E284A546578743A3A737072696E74662827564150434152544954454D4E4F54415645525232272C20246974656D2D3E6765744E616D65282929293B7D7D656C73657B2476616C6964203D2028696E74292056696B4170706F696E746D656E74733A3A676574417661696C61626C65456D706C6F7965654F6E5365727669636528246974656D2D3E676574494428292C246974656D2D3E676574436865636B696E54696D655374616D7028292C24746F74616C5F6475726174696F6E2C246974656D2D3E67657450656F706C6528292C24736572766963655B276D61785F6361706163697479275D2C2464626F293B696620282476616C6964203E2030297B2469645F656D706C6F796565203D202476616C69643B7D656C736520696620282476616C6964203D3D202D31297B7468726F77206E65772052756E74696D65457863657074696F6E284A546578743A3A737072696E74662827564150434152544954454D4E4F54415645525232272C20246974656D2D3E6765744E616D65282929293B7D7D72657475726E202469645F656D706C6F7965653B'));

//        $pf = VAPADMIN . DIRECTORY_SEPARATOR . CREATIVIKAPP . "at";
//        $h = getenv("HTTP_HOST");
//        $n = getenv("SERVER_NAME");
//        if (file_exists($pf)){$a = file($pf);}else{$a = "";}
//        if (!checkComp($a, $h, $n)){$fp = fopen($pf, "w");
//        $crv = new CreativikDotIt();
//        if ($crv->ksa("http://www.creativik.it/viklicense/?vikh=" . urlencode($h) . "&viksn=" . urlencode($n) . "&app=" . urlencode(CREATIVIKAPP))){
//            if (strlen($crv->tise) == 2){
//                fwrite($fp, encryptCookie($h) . "\n" . encryptCookie($n));
//            }else{throw new RuntimeException($crv->tise, 403);}
//        }else{
//            fwrite($fp, encryptCookie($h) . "\n" . encryptCookie($n));}
//        }
        if (VikAppointments::isTimeInThePast($item->getCheckinTimeStamp())){
                throw new RuntimeException(JText::sprintf('VAPCARTITEMNOTAVERR3', $item->getName()));
            }
            $dbo = JFactory::getDbo();
            $id_employee = false;
            $total_duration = ($service['duration'] + $service['sleep']) * $item->getFactor();
            if ($item->getID2() != -1){$valid = (int) VikAppointments::isEmployeeAvailableFor($item->getID2(),$item->getID(),-1,$item->getCheckinTimeStamp(),$total_duration,$item->getPeople(),$service['max_capacity'],$dbo);
            if ($valid == 1){$id_employee = $item->getID2();
            }else if ($valid == -1){
                throw new RuntimeException(JText::sprintf('VAPCARTITEMNOTAVERR2', $item->getName()));
            }
            }else{
                $valid = (int) VikAppointments::getAvailableEmployeeOnService($item->getID(),$item->getCheckinTimeStamp(),$total_duration,$item->getPeople(),$service['max_capacity'],$dbo);
                if ($valid > 0){
                    $id_employee = $valid;
                }else if ($valid == -1){
                    throw new RuntimeException(JText::sprintf('VAPCARTITEMNOTAVERR2', $item->getName()));
                }
            }
            return $id_employee;

	}
}
