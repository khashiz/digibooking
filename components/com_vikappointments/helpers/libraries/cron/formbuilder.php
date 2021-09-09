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

/**
 * Class used to build a form with a list of cron form fields.
 *
 * @since 	 1.5
 * @see 	 CronFormField
 * @see 	 CronFormFieldConstraints
 */
class CronFormBuilder
{
	/**
	 * The list of all the CronFormField objects, required to build the settings form.
	 *
	 * @var  array
	 */
	private $fields = array();

	/**
	 * A prefix to extend the CSS classes inside the form.
	 * An empty value may cause warnings.
	 *
	 * @var  string
	 */
	private $classname = 'cronform';

	/**
	 * The construct of the cron form builder to initialize the required parameters of this object.
	 *
	 * @param 	array  	$fields  The fields to push into the form.
	 *
	 * @uses 	setFields()
	 */
	public function __construct(array $fields = array())
	{
		$this->setFields($fields);
	}

	/**
	 * Sets all the fields in the list.
	 *
	 * @param 	array 	$fields  The list of CronFormField objects.
	 */
	public function setFields(array $fields)
	{
		$this->fields = $fields;
	}

	/**
	 * Returns the list containing all the CronFormField objects.
	 *
	 * @return 	array 	The list of CronFormField objects.
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * Overwrite the CSS class prefix.
	 *
	 * @param 	string $classname 	The new CSS class prefix to use.
	 */
	public function setClassname($classname)
	{
		$this->classname = $classname;
	}

	/**
	 * Builds the settings form based on the specified fields.
	 * The form is returned as string, nothing is echoed inside this function.
	 *
	 * @param 	array $args 	The associative array containing the 
	 *							stored values of the settings.
	 *
	 * @return 	string 			The Html structure of the form.
	 *
	 * @uses buildInputText 		Renders the html of text inputs.
	 * @uses buildInputNumber 		Renders the html of number inputs.
	 * @uses buildInputPassword 	Renders the html of password inputs.
	 * @uses buildTextarea		 	Renders the html of textareas.
	 * @uses buildDropdown		 	Renders the html of dropdowns.
	 * @uses buildHtml		 		Renders the html of custom HTML elements.
	 * @uses wrapField				Renders the html structure to wrap the a single field.
	 */
	public function build($args = array())
	{
		$html = "";

		foreach ($this->fields as $field)
		{
			// if the type of the field is not HTML and the field has a value in the list,
			// insert the new value into the object as default
			if ($field->getType() != CronFormField::HTML && array_key_exists($field->getName(), $args))
			{
				$field->setDefaultValue($args[$field->getName()]);
			}

			// proceed only if the bind() method returns true
			if ($this->bind($field))
			{
				$app = '';

				if ($field->getType() == CronFormField::INPUT_TEXT)
				{
					$app = $this->buildInputText($field);
				}
				else if ($field->getType() == CronFormField::INPUT_NUMBER)
				{ 
					$app = $this->buildInputNumber($field);
				}
				else if ($field->getType() == CronFormField::INPUT_PASSWORD)
				{ 
					$app = $this->buildInputPassword($field);
				}
				else if ($field->getType() == CronFormField::TEXTAREA)
				{ 
					$app = $this->buildTextarea($field);
				}
				else if ($field->getType() == CronFormField::DROPDOWN)
				{
					$app = $this->buildDropdown($field);
				}
				else
				{
					$app = $this->buildHtml($field);
				}

				// wrap the generated html within the structure of the field
				$html .= $this->wrapField($app, $field);
			}
		}

		return $html;
	}

	/**
	 * Returns the html of field wrapped with the following structure:
	 *	[
	 *		[LABEL]
	 *		[$html]
	 *	]
	 *
	 * @param 	string 			$html 	The html of the field to wrap.
	 * @param 	CronFormField 	$f 		The CronFormField object containing the info of the field.
	 *
	 * @return 	string 			The html of the field.
	 */
	protected function wrapField($html, $f)
	{
		return '<div class="'.$this->classname.'-field">'.
			(strlen($f->getLabel()) > 0 ? 
			'<div class="'.$this->classname.'"-field-label>
				<label for="'.$this->classname.'-'.$f->getName().'"><strong>'.$f->getLabel().($f->isRequired() ? '*' : '').':</strong></label>
			</div>' : '').
			'<div class="'.$this->classname.'"-field-control>'.$html.'</div>'.
			( strlen($f->getSecondaryLabel()) > 0 ? '<div class="'.$this->classname.'-field-sndlabel">'.$f->getSecondaryLabel().'</div>' : '').
		'</div>';
	}

	/**
	 * Method used to bind data before building the field.
	 *
	 * @param 	CronFormField 	$f 	The CronFormField object containing the info of the field.
	 *
	 * @return 	boolean 		True to start building, otherwise false.
	 */
	protected function bind(&$f)
	{
		// do something here

		return true;
	}

	/**
	 * Builds the html structure of a Input Text field.
	 *
	 * @param 	CronFormField 	$f 	The CronFormField object containing the info of the field.
	 *
	 * @return 	string 			The html structure of the field.
	 *
	 * @uses 	buildConstraints()
	 */
	protected function buildInputText($f)
	{
		return '<input type="text" name="'.$f->getName().'" id="'.$this->classname.'-'.$f->getName().'" value="'.$f->getDefaultValue().'" '.$this->buildConstraints($f->getConstraints()).'/>';
	}

	/**
	 * Builds the html structure of a Input Number field.
	 *
	 * @param 	CronFormField 	$f 	The CronFormField object containing the info of the field.
	 *
	 * @return 	string 			The html structure of the field.
	 *
	 * @uses 	buildConstraints()
	 */
	protected function buildInputNumber($f)
	{
		return '<input type="number" name="'.$f->getName().'" id="'.$this->classname.'-'.$f->getName().'" value="'.$f->getDefaultValue().'" '.$this->buildConstraints($f->getConstraints()).'/>';
	}

	/**
	 * Builds the html structure of a Input Password field.
	 *
	 * @param 	CronFormField 	$f 	The CronFormField object containing the info of the field.
	 *
	 * @return 	string 			The html structure of the field.
	 *
	 * @uses 	buildConstraints()
	 */
	protected function buildInputPassword($f)
	{
		return '<input type="password" name="'.$f->getName().'" id="'.$this->classname.'-'.$f->getName().'" value="'.$f->getDefaultValue().'" '.$this->buildConstraints($f->getConstraints()).'/>';
	}

	/**
	 * Builds the html structure of a Textarea field.
	 *
	 * @param 	CronFormField 	$f 	The CronFormField object containing the info of the field.
	 *
	 * @return 	string 			The html structure of the field.
	 *
	 * @uses 	buildConstraints()
	 */
	protected function buildTextarea($f)
	{
		return '<textarea name="'.$f->getName().'" id="'.$this->classname.'-'.$f->getName().'" '.$this->buildConstraints($f->getConstraints()).'>'.$f->getDefaultValue().'</textarea>';
	}

	/**
	 * Builds the html structure of a Select field.
	 *
	 * @param 	CronFormField 	$f 	The CronFormField object containing the info of the field.
	 *
	 * @return 	string 			The html structure of the field.
	 *
	 * @uses 	buildConstraints()
	 */
	protected function buildDropdown($f)
	{
		$str = '<select name="'.$f->getName().'" id="'.$this->classname.'-'.$f->getName().'" '.$this->buildConstraints($f->getConstraints()).'>';
		
		foreach ($f->getListValues() as $key => $val)
		{
			$str .= '<option value="'.$key.'" '.($key == $f->getDefaultValue() ? 'selected="selected"' : '').'>'.$val.'</option>';
		}

		return $str . '</select>';
	}

	/**
	 * Builds the html structure of a HTML field.
	 *
	 * @param 	CronFormField 	$f 	The CronFormField object containing the info of the field.
	 *
	 * @return 	string 			The html structure of the field.
	 */
	protected function buildHtml($f)
	{
		return $f->getDefaultValue();
	}

	/**
	 * Returns all the constraints concatenated with a space.
	 *
	 * @param 	object 	$constraints 	The CronFormFieldConstraints object.
	 *
	 * @return 	string 	The attributes string.
	 */
	protected function buildConstraints($constraints)
	{
		$str = '';

		$protected = array('name', 'value', 'id', 'type');

		if ($constraints !== null)
		{
			foreach ($constraints->getAttributes() as $key => $val)
			{
				if (!in_array($key, $protected))
				{
					$str .= "$key=\"$val\" ";
				}
			}
		}

		return $str;
	}
}
