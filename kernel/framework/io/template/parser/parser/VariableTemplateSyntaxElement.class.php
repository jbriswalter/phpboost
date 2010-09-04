<?php
/*##################################################
 *                    VariableTemplateSyntaxElement.class.php
 *                            -------------------
 *   begin                : July 0810 2010
 *   copyright            : (C) 2010 Loic Rouchon
 *   email                : horn@phpboost.com
 *
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

class VariableTemplateSyntaxElement extends AbstractTemplateSyntaxElement
{
	public static function is_element(StringInputStream $input)
	{
		return $input->assert_next('\s*(?![0-9])(?:\w+\.)*\w+\s*');
	}
	
	public function parse(StringInputStream $input, StringOutputStream $output)
	{
		$input->consume_next('\s*');
		$element = null;
		if (LoopVarTemplateSyntaxElement::is_element($input))
		{
			$element = new LoopVarTemplateSyntaxElement();
		}
		elseif (SimpleVarTemplateSyntaxElement::is_element($input))
		{
			$element = new SimpleVarTemplateSyntaxElement();
		}
		else
		{
			throw new TemplateParserException('invalid variable', $input);
		}
		$element->parse($input, $output);
		$input->consume_next('\s*');
	}
}
?>