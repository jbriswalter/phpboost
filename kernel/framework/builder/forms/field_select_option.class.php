<?php
/*##################################################
 *                             field_select_option.class.php
 *                            -------------------
 *   begin                : April 28, 2009
 *   copyright            : (C) 2009 Viarre R�gis
 *   email                : crowkait@phpboost.com
 *
###################################################
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
###################################################*/

import('builder/form/form_field');

/**
 * @author R�gis Viarre <crowkait@phpboost.com>
 * @desc This class manage select field options.
 * @package builder
 * @subpackage form
 */
class FormSelectOption extends FormField
{
	function FormSelectOption($field_options)
	{
		parent::FormField('', $field_options);
		
		foreach($field_options as $attribute => $value)
		{
			$attribute = strtolower($attribute);
			switch ($attribute)
			{
				case 'optiontitle' :
					$this->option_title = $value;
				break;
				case 'selected' :
					$this->option_selected = $value;
				break;
			}
		}
	}
	
	/**
	 * @return string The html code for the select.
	 */
	function display()
	{
		$option = '<option ';
		$option .= !empty($this->field_value) ? 'value="' . $this->field_value . '" ' : '';
		$option .= (boolean)$this->option_selected ? 'selected="selected" ' : '';
		$option .= '> ' . $this->option_title . '</option>' . "\n";
		
		return $option;
	}

	var $option_title = '';
	var $option_selected = false;
}

?>