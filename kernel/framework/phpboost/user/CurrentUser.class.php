<?php
/*##################################################
 *                               CurrentUser.class.php
 *                            -------------------
 *   begin                : February 18, 2008
 *   copyright            : (C) 2008 Viarre R�gis
 *   email                : crowkait@phpboost.com
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

define('RANK_TYPE', 1);
define('GROUP_TYPE', 2);
define('USER_TYPE', 3);

/**
 * @author R�gis VIARRE <crowkait@phpboost.com>
 * @desc This class manage user, it provide you methods to get or modify user informations, moreover methods allow you to control user authorizations
 * @package {@package}
 */
class CurrentUser extends User
{
	public static function from_session()
	{
		return new self(AppContext::get_session());
	}

	/**
	 * @desc Sets global authorizations which are given by all the user groups authorizations.
	 */
	public function __construct(SessionData $session)
	{
		$this->id = $session->get_user_id();
		$this->level = $session->get_cached_data('level', -1);
		$this->is_admin = ($this->level == 2);

		$this->display_name = $session->get_cached_data('display_name', LangLoader::get_message('guest', 'main'));
		$this->email = $session->get_cached_data('email', null);
		$this->show_email = $session->get_cached_data('show_email', false);
		$this->unread_pm = $session->get_cached_data('unread_pm', 0);
		$this->timestamp = $session->get_cached_data('timestamp', time());
		$this->warning_percentage = $session->get_cached_data('warning_percentage', 0);
		$this->is_banned = $session->get_cached_data('is_banned', 0);
		$this->is_readonly = $session->get_cached_data('is_readonly', 0);

		$user_accounts_config = UserAccountsConfig::load();
		$this->locale = $session->get_cached_data('locale', $user_accounts_config->get_default_lang());
		$this->theme = $session->get_cached_data('theme', $user_accounts_config->get_default_theme());
		$this->timezone = $session->get_cached_data('timezone', GeneralConfig::load()->get_site_timezone());
		$this->editor = $session->get_cached_data('editor', 'bbcode');

		$this->build_groups($session);
	}

	protected function build_groups(SessionData $session)
	{
		$groups_auth = array();
		foreach (GroupsService::get_groups() as $idgroup => $array_info)
		{
			$groups_auth[$idgroup] = $array_info['auth'];
		}
		$this->groups_auth = $groups_auth;

		//Groupes du membre.
		$this->groups = explode('|', $session->get_cached_data('groups', ''));
		array_unshift($this->groups, 'r' . $this->level); //Ajoute le groupe associ� au rang du membre.
		array_pop($this->groups); //Supprime l'�l�ment vide en fin de tableau.
	}
	
	/**
	 * @desc Accessor
	 * @param string $attribute The attribute name.
	 * @return unknown_type
	 * @deprecated
	 */
	public function get_attribute($attribute)
	{
		return isset($this->user_data[$attribute]) ? $this->user_data[$attribute] : '';
	}

	/**
	 * @desc Check the authorization level
	 * @param int $level Constant of level authorization to check (User::MEMBER_LEVEL, User::MODERATOR_LEVEL, User::ADMIN_LEVEL).
	 * @return boolean True if authorized, false otherwise.
	 */
	public function check_level($level)
	{
		return $this->level >= $level;
	}

	/**
	 * @desc Get the authorizations given by all the user groups. Then check the authorization.
	 * @param array $array_auth_groups The array passed to check the authorization.
	 * @param int $authorization_bit Value of position bit to check the authorization.
	 * This value has to be a multiple of two. You can use this simplified scripture :
	 * 0x01, 0x02, 0x04, 0x08 to set a new position bit to check.
	 * @return boolean True if authorized, false otherwise.
	 */
	public function check_auth($array_auth_groups, $authorization_bit)
	{
		//Si il s'agit d'un administrateur, �tant donn� qu'il a tous les droits, on renvoie syst�matiquement vrai
		if ($this->check_level(User::ADMIN_LEVEL))
		{
			return true;
		}

		//Si le tableau d'autorisation n'est pas valide, on renvoie faux pour des raisons de s�curit�
		if (!is_array($array_auth_groups))
		{
			return false;
		}

		//Enfin, on regarde si le rang, le groupe ou son identifiant lui donnent l'autorisation sur le bit demand�
		return (bool)($this->sum_auth_groups($array_auth_groups) & (int)$authorization_bit);
	}

	/**
	 * @desc Get the maximum value of authorization in all user groups.
	 * @param int $key_auth
	 * @param int $max_value_compare Maximal value to compare
	 * @return int
	 */
	public function check_max_value($key_auth, $max_value_compare = 0)
	{
		if (!is_array($this->groups_auth))
		{
			return false;
		}

		//R�cup�re les autorisations de tout les groupes dont le membre fait partie.
		$array_user_auth_groups = $this->array_group_intersect($this->groups_auth);
		$max_auth = $max_value_compare;
		foreach ($array_user_auth_groups as $idgroup => $group_auth)
		{
			if ($group_auth[$key_auth] == -1)
			{
				return -1;
			}
			else
			{
				$max_auth = max($max_auth, $group_auth[$key_auth]);
			}
		}

		return $max_auth;
	}

	/**
	 * @desc Modify the user theme.
	 * @param string $user_theme The new theme.
	 * @deprecated
	 */
	public function set_user_theme($user_theme)
	{
		$this->user_data['user_theme'] = $user_theme;
	}

	/**
	 * @desc Modify the theme for guest in the database (sessions table).
	 * @param string $user_theme The new theme
	 * @deprecated
	 */
	public function update_user_theme($user_theme)
	{
		global $Sql;

		if (!UserAccountsConfig::load()->is_users_theme_forced()) //Th�mes aux membres autoris�s.
		{
			if ($this->user_data['level'] > -1)
			{
				$Sql->query_inject("UPDATE " . DB_TABLE_MEMBER . " SET user_theme = '" . TextHelper::strprotect($user_theme) . "' WHERE user_id = '" . $this->user_data['user_id'] . "'", __LINE__, __FILE__);
			}
			else
			{
				$Sql->query_inject("UPDATE " . DB_TABLE_SESSIONS . " SET user_theme = '" . TextHelper::strprotect($user_theme) . "' WHERE level = -1 AND session_id = '" . $this->user_data['session_id'] . "'", __LINE__, __FILE__);
			}
		}
	}

	/**
	 * @desc Modify the user lang.
	 * @param string $user_lang The new lang
	 * @deprecated
	 */
	public function set_user_lang($user_lang)
	{
		$this->user_data['user_lang'] = $user_lang;
	}

	/**
	 * @desc Modify the lang for guest in the database (sessions table).
	 * @param string $user_theme The new lang
	 * @deprecated
	 */
	public function update_user_lang($user_lang)
	{
		global $Sql;

		if ($this->user_data['level'] > -1)
		{
			$Sql->query_inject("UPDATE " . DB_TABLE_MEMBER . " SET user_lang = '" . TextHelper::strprotect($user_lang) . "' WHERE user_id = '" . $this->user_data['user_id'] . "'", __LINE__, __FILE__);
		}
		else
		{
			$Sql->query_inject("UPDATE " . DB_TABLE_SESSIONS . " SET user_lang = '" . TextHelper::strprotect($user_lang) . "' WHERE level = -1 AND session_id = '" . $this->user_data['session_id'] . "'", __LINE__, __FILE__);
		}
	}

	/**
	 * @desc Return the maximal authorization given by the user groups
	 * @param array $array_auth_groups
	 * @return string binary authorizations
	 */
	private function sum_auth_groups($array_auth_groups)
	{
		//R�cup�re les autorisations de tout les groupes dont le membre fait partie.
		$array_user_auth_groups = $this->array_group_intersect($array_auth_groups);
		$max_auth = 0;
		foreach ($array_user_auth_groups as $idgroup => $group_auth)
		{
			$max_auth |= (int)$group_auth;
		}

		return $max_auth;
	}

	/**
	 * @desc Compute the group <strong>intersection</strong> between the user groups and the group array in argument
	 * @param array $array_auth_groups Array of groups id
	 * @return array The new array computed.
	 */
	private function array_group_intersect($array_auth_groups)
	{
		$array_user_auth_groups = array();
		foreach ($array_auth_groups as $idgroup => $auth_group)
		{
			if (is_numeric($idgroup)) //Groupe
			{
				if (in_array($idgroup, $this->user_groups))
				{
					$array_user_auth_groups[$idgroup] = $auth_group;
				}
			}
			elseif (substr($idgroup, 0, 1) == 'r') //Rang
			{
				if ($this->level >= (int)str_replace('r', '', $idgroup))
				{
					$array_user_auth_groups[$idgroup] = $auth_group;
				}
			}
			else //Membre
			{
				if ($this->id == (int)str_replace('m', '', $idgroup))
				{
					$array_user_auth_groups[$idgroup] = $auth_group;
				}
			}
		}

		return $array_user_auth_groups;
	}
}
?>