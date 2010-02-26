<?php
/*##################################################
 *                       AbstractFormField.class.php
 *                            -------------------
 *   begin                : January 08, 2010
 *   copyright            : (C) 2010 Benoit Sautel
 *   email                : ben.popeye@phpboost.com
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author R�gis Viarre <crowkait@phpboost.com>
 * @desc Abstract class that proposes a default implementation for the FormField interface.
 * @package builder/field
 * @subpackage form
 */
abstract class AbstractFormField implements FormField
{
	/**
	 * @var string
	 */
	protected $id = '';
	/**
	 * @var string
	 */
	private $prefix = '';
	/**
	 * @var string
	 */
	protected $label = '';
	/**
	 * @var string
	 */
	protected $description = '';
	/**
	 * @var string
	 */
	protected $value = null;
	/**
	 * @var boolean
	 */
	protected $disabled = false;
	/**
	 * @var string
	 */
	protected $css_class = '';
	/**
	 * @var string
	 */
	protected $onblur = '';
	/**
	 * @var string
	 */
	protected $required = false;
	/**
	 * @var string
	 */
	protected $validation_error_message = '';
	/**
	 * @var FormFieldConstraint[]
	 */
	protected $constraints = array();
	/**
	 * @var FormConstraint[]
	 */
	protected $form_constraints = array();

	/**
	 * @desc Constructs and set parameters to the field.
	 * The specific parameters of this abstract class (common with many fields) are the following:
	 * <ul>
	 * 	<li>description at which you must associate the description of the field</li>
	 * 	<li>class which corresponds to the HTML class of the field</li>
	 * 	<li>required which tells whether this field must be completed. If it's the case, it will display a * beside the label and will add a non empty constraint to the constraints.</li>
	 * </ul>
	 * None of these parameters is required.
	 * @param string $id Field identifier
	 * @param string $label Field label
	 * @param mixed $value Field value 
	 * @param string[] $field_options Map associating the parameters values to the parameters names.
	 * @param FormFieldConstraint[] $constraints List of the constraints which must be checked when the form is been validated
	 */
	protected function __construct($id, $label, $value, array $field_options = array(), array $constraints = array())
	{
		$this->set_id($id);
		$this->set_label($label);
		$this->set_value($value);
		$this->compute_options($field_options);

		foreach ($constraints as $constraint)
		{
			$this->add_constraint($constraint);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_id($id)
	{
		$this->id = $id;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function set_prefix($prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * Returns the label
	 * @return string The label
	 */
	public function get_label()
	{
		return $this->label;
	}

	/**
	 * Sets the label
	 * @param string $label The label
	 */
	public function set_label($label)
	{
		$this->label = $label;
	}

	/**
	 * Returns the description
	 * @return string the description
	 */
	public function get_description()
	{
		return $this->description;
	}

	/**
	 * Sets the description
	 * @param string $description The description
	 */
	public function set_description($description)
	{
		$this->description = $description;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_value()
	{
		return $this->value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_value($value)
	{
		$this->value = $value;
	}

	/**
	 * @desc Tells whether the field is required
	 * @return true if it is, false otherwise
	 */
	public function is_required()
	{
		return $this->required;
	}

	/**
	 * @desc Changes the fact that the field is required or not.
	 * @param bool $required true if it's required, false otherwise
	 */
	public function set_required($required)
	{
		$this->required = $required;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate()
	{
		$this->retrieve_value();
		foreach ($this->constraints as $constraint)
		{
			if (!$constraint->validate($this))
			{
				return false;
			}
		}
		return true;
	}

	public function get_validation_error_message()
	{
		return $this->validation_error_message;
	}
	
	public function set_validation_error_message($error_message)
	{
		$this->validation_error_message = $error_message;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function retrieve_value()
	{
		$request = AppContext::get_request();
		if ($request->has_parameter($this->get_html_id()))
		{
			$this->set_value($request->get_value($this->get_html_id()));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_html_id()
	{
		return $this->prefix . '_' . $this->get_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_constraint(FormFieldConstraint $constraint)
	{
		$this->constraints[] = $constraint;
	}
	
	public function add_form_constraint(FormConstraint $constraint)
	{
		$this->form_constraints[] = $constraint;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function has_constraints()
	{
		return (count($this->constraints) > 0 || count($this->form_constraints) > 0);
	}
	
	public function get_js_validations()
	{
		$validations = array();
		foreach ($this->constraints as $constraint)
		{
			$validation = $constraint->get_js_validation($this);
			if (!empty($validation))
			{
				$validations[] = $validation;
			}
		}
		foreach ($this->form_constraints as $constraint)
		{
			$validation = $constraint->get_js_validation();
			if (!empty($validation))
			{
				$validations[] = $validation;
			}
		}
		return $validations;
	}
	
	public function get_onblur_validation()
	{
		return 'formFieldConstraintsOnblurValidation(this, Array(' . 
			implode(",", $this->get_js_validations()) . '));';
	}

	protected function compute_options(array &$field_options)
	{
		foreach($field_options as $attribute => $value)
		{
			$attribute = strtolower($attribute);
			switch ($attribute)
			{
				case 'description':
					$this->set_description($value);
					unset($field_options['subtitle']);
					break;
				case 'onblur':
					$this->set_onblur($value);
					unset($field_options['onblur']);
					break;
				case 'disabled':
					$this->set_disabled($value);
					unset($field_options['disabled']);
					break;
				case 'class':
					$this->set_css_class($value);
					unset($field_options['class']);
					break;
				case 'required':
					$this->set_required($value);
					if ($value)
					{
						$this->add_constraint(new NotEmptyFormFieldConstraint());
					}
					unset($field_options['required']);
					break;
				default :
					throw new FormBuilderException('The class ' . get_class($this) . ' hasn\'t the ' . $attribute . ' attribute');
			}
		}
	}

	protected function assign_common_template_variables(Template $template)
	{
		$template->assign_vars(array(
			'ID' => $this->get_html_id(),
			'LABEL' => $this->get_label(),
			'DESCRIPTION' => $this->get_description(),
			'C_REQUIRED' => $this->is_required(),
			'VALUE' => $this->get_value(),
			'C_HAS_CONSTRAINTS' => $this->has_constraints(),
			'CLASS' => $this->get_css_class()
		));
	}
	
	protected function get_onblur()
	{
		return $this->onblur;
	}

	protected function set_onblur($onblur)
	{
		$this->onblur = $onblur;
	}
	
	protected function get_css_class()
	{
		return $this->css_class;
	}

	protected function set_css_class($css_class)
	{
		$this->css_class = $css_class;
	}
	
	protected function is_disabled()
	{
		return $this->disabled;
	}

	protected function set_disabled($disabled)
	{
		$this->disabled = $disabled;
	}
}

?>