<?php
/*##################################################
 *                              NotationService.class.php
 *                            -------------------
 *   begin                : February 14, 2010
 *   copyright            : (C) 2010 K�vin MASSY
 *   email                : soldier.weasel@gmail.com
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
 
 /**
 * @author K�vin MASSY <soldier.weasel@gmail.com>
 * @package {@package}
 */
class NotationService
{
	private static $user;
	private static $db_querier;
	private static $lang;
	
	public static function __static()
	{
		self::$user = AppContext::get_user();
		self::$db_querier = PersistenceContext::get_querier();
		self::$lang = LangLoader::get('main');
	}
	
	/*
	 * This function required object Notation containing the module_name, module_id and notation_scale.
	 */
	public static function display_static_image(Notation $notation)
	{
		if ($notation->get_notation_scale() > 0)
		{
			$nbr_notes = self::$db_querier->count(DB_TABLE_NOTATION, "WHERE module_name = '" . $notation->get_module_name() . "' AND module_id = '". $notation->get_module_id() ."' ");
			if ($nbr_notes > 0)
			{
				$template = new StringTemplate('
				# START notation #
					<img src="{PATH_TO_ROOT}/templates/{THEME}/images/{notation.PICTURE}" alt="" class="valign_middle" />
				# END notation #');
				
				$average_notes = self::get_average_notes($notation);
				for ($i = 1; $i <= $notation->get_notation_scale(); $i++)
				{
					$star_img = 'stars.png';
					if ($average_notes < $i)
					{
						$decimal = $i - $average_notes;
						if ($decimal >= 1)
							$star_img = 'stars0.png';
						elseif ($decimal >= 0.75)
							$star_img = 'stars1.png';
						elseif ($decimal >= 0.50)
							$star_img = 'stars2.png';
						else
							$star_img = 'stars3.png';
					}
					
					$template->assign_block_vars('notation', array(
						'PICTURE' => $star_img
					));
				}
				return $template->render();
			}
			throw new Exception('Not notation');
		}
		else
		{
			throw new Exception('Not display, notation scale is null');
		}
	}
	
	/*
	 * This function required object Notation containing the module_name, module_id, user_id, login, note and notation_scale.
	 */
	public static function display_active_image(Notation $notation)
	{
		$note_post = AppContext::get_request()->get_int('note', 0);
		
		if (AppContext::get_request()->get_bool('valid_note', false))
		{
			if (!empty($note_post))
				self::register_notation($notation);

			throw new Exception('Register !');
		}
		else
		{
			$template = new FileTemplate('framework/notation.tpl');
			
			$average_notes = self::get_average_notes($notation);
			
			$select = '<option value="-1" selected="selected">' . self::$lang['note'] . '</option>';
				for ($i = 0; $i <= $notation->get_notation_scale(); $i++)
			$select .= '<option value="' . $i . '">' . $i . '</option>';
					
			$ajax_note = '<div style="width:' . $notation->get_notation_scale() *16 . 'px;margin:auto;display:none" id="note_stars' . $notation->get_module_id() . '" onmouseout="out_div(' . $notation->get_module_id() . ', array_note[' . $notation->get_module_id() . '])" onmouseover="over_div()">';
			for ($i = 1; $i <= $notation->get_notation_scale(); $i++)
			{
				$star_img = 'stars.png';
				if ($average_notes < $i)
				{
					$decimal = $i - $average_notes;
					if ($decimal >= 1)
						$star_img = 'stars0.png';
					elseif ($decimal >= 0.75)
						$star_img = 'stars1.png';
					elseif ($decimal >= 0.50)
						$star_img = 'stars2.png';
					else
						$star_img = 'stars3.png';
				}
				$ajax_note .= '<a href="javascript:send_note(' . $notation->get_module_id() . ', ' . $i . ', \'' . AppContext::get_session()->get_token() . '\')" onmouseover="select_stars(' . $notation->get_module_id() . ', ' . $i . ');"><img src="../templates/'. get_utheme() . '/images/' . $star_img . '" alt="" class="valign_middle" id="n' . $notation->get_module_id() . '_stars' . $i . '" /></a>';
			}

			$ajax_note .= '</div> <span id="noteloading' . $notation->get_module_id() . '"></span>';
			
			/*
			else
				$ajax_note .= '</div> <span id="noteloading' . $notation->get_module_id() . '"></span> <div style="display:' . $display . '" id="nbrnote' . $notation->get_module_id() . '">(' . $row_note['nbrnote'] . ' ' . (($row_note['nbrnote'] > 1) ? strtolower($LANG['notes']) : strtolower($LANG['note'])) . ')</div>';
			*/
			$template->put_all(array(
				'C_JS_NOTE' => !defined('HANDLE_NOTE'),
				'ID' => $notation->get_module_id(),
				'NOTE_MAX' => $notation->get_notation_scale(),
				'SELECT' => $select,
				'NOTE' =>  /* TODO */ '<span id="note_value' . $notation->get_module_id() . '">' . ((self::get_number_notes($notation) > 0) ? '<strong>' . $average_notes . '</strong>' : '<em>' . self::$lang['no_note'] . '</em>') . '</span>' . $ajax_note,
				'ARRAY_NOTE' => 'array_note[' . $notation->get_module_id() . '] = \'' . $average_notes . '\';',
				'DISPLAY' => /* TODO */ '',
				'L_AUTH_ERROR' => /* TODO */ '',
				'L_ALERT_ALREADY_VOTE' => addslashes(self::$lang['already_vote']),
				'L_ALREADY_VOTE' => '',
				'L_NOTE' => addslashes(self::$lang['note']),
				'L_NOTES' => addslashes(self::$lang['notes']),
				'L_VALID_NOTE' => self::$lang['valid_note']
			));
				
			
			return $template->render();
		}
	}
	
	private static function register_notation(Notation $notation)
	{
		if (self::$user->check_level(MEMBER_LEVEL))
		{
			$note_is_valid = $notation->get_note() >= 0 && $notation->get_note() <= $notation->get_notation_scale() ? true : false;
			$member_already_notation = self::$db_querier->count(DB_TABLE_NOTATION, "WHERE user_id = '" . $notation->get_user_id() . "' AND module_name = '" . $notation->get_module_name() . "' AND module_id = '". $notation->get_module_id() ."' ") > 0 ? true : false;
			
			if (!$member_already_notation && $note_is_valid)
			{
				self::insert_note($notation);
			}
			else
			{
				return -1;
				throw new Exception('Already post note');
			}
		}
		else
		{
			return -2;
			throw new Exception('Not level member');
		}
	}
	
	private static function insert_note(Notation $notation)
	{
		self::$db_querier->inject(
			"INSERT INTO " . DB_TABLE_NOTATION . " (module_name, module_id, user_id, login, note)
			VALUES (:module_name, :module_id, :user_id, :login, :note)", array(
                'module_name' => $notation->get_module_name(),
				'module_id' => $notation->get_module_id(),
				'user_id' => $notation->get_user_id(),
				'login' => $notation->get_login(),
				'note' => $notation->get_note(),				
		));
	}
	
	private static function get_average_notes(Notation $notation)
	{
		$nbr_notes = self::get_number_notes($notation);
		if ($nbr_notes > 0)
		{
			$result = self::$db_querier->select("SELECT note
				FROM " . DB_TABLE_NOTATION . "
				WHERE module_name = '" . $notation->get_module_name() . "' 
				AND module_id = '". $notation->get_module_id() ."'
			");
			
			$notes = 0;
			while ($row = $result->fetch())
			{
				$notes += $row['note'];
			}

			return (round(($notes / $nbr_notes) / 0.25) * 0.25);
		}
	}
	
	private static function get_number_notes(Notation $notation)
	{
		return self::$db_querier->count(DB_TABLE_NOTATION, "WHERE module_name = '" . $notation->get_module_name() . "' AND module_id = '". $notation->get_module_id() ."' ");
	}

}

?>