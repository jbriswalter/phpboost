<?php
/*##################################################
 *                          FormFieldset.class.php
 *                            -------------------
 *   begin                : February 15, 2010
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
 * @package builder
 * @subpackage form/fieldset
 * @desc
 * @author R�gis Viarre <crowkait@phpboost.com>
 */
interface FormFieldset
{
	function set_form(HTMLForm  $form_name);

	/**
	 * @desc Adds a list in the container
	 * @param FormField $form_field The field to add
	 */
	function add_field(FormField $form_field);

	function validate();

	/**
	 * @desc Return the form
	 * @param Template $Template Optionnal template
	 * @return string
	 */
	function display();

	function get_onsubmit_validations();

	function get_validation_error_messages();

	/**
	 * @param string $title The fieldset title
	 */
	function set_title($title);

	/**
	 * @param boolean $display_required
	 */
	function set_display_required($display_required);
	

	/**
	 * @return string The fieldset title
	 */
	function get_title();

	/**
	 * @return bool
	 */
	function has_field($field_id);

	/**
	 * @return FormField
	 */
	function get_field($field_id);
}

?>