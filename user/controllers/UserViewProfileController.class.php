<?php
/*##################################################
 *                       UserViewProfileController.class.php
 *                            -------------------
 *   begin                : October 07, 2011
 *   copyright            : (C) 2011 Kevin MASSY
 *   email                : kevin.massy@phpboost.com
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

class UserViewProfileController extends AbstractController
{
	private $lang;
	private $form;
	private $user;
	private $tpl;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$user_id = $request->get_getint('user_id', AppContext::get_current_user()->get_id());

		try {
			$this->user = UserService::get_user_approved($user_id);
		} catch (RowNotFoundException $e) {
			$error_controller = PHPBoostErrors::unexisting_element();
			DispatchManager::redirect($error_controller);
		}
		
		$this->build_form();

		$this->tpl->put('FORM', $this->form->display());

		return $this->build_response($this->tpl, $user_id);
	}

	private function init()
	{
		$this->lang = LangLoader::get('user-common');
		$this->tpl = new StringTemplate('# INCLUDE FORM #');
		$this->tpl->add_lang($this->lang);
		$this->user = AppContext::get_current_user();
	}

	private function build_form()
	{
		$form = new HTMLForm('member-view-profile', '', false);

		$fieldset = new FormFieldsetHTML('profile', $this->lang['profile']);
		$form->add_fieldset($fieldset);

		if (AppContext::get_current_user()->check_level(User::ADMIN_LEVEL))
		{
			$link_edit = '<a href="'. AdminMembersUrlBuilder::edit($this->user->get_id())->rel() .'" title="'.$this->lang['profile.edit'].'" class="fa fa-edit"></a>';
			$fieldset->add_field(new FormFieldFree('profile_edit', $this->lang['profile.edit'], $link_edit));
		}

		$fieldset->add_field(new FormFieldFree('pseudo', $this->lang['pseudo'], $this->user->get_login()));

		$fieldset->add_field(new FormFieldFree('level', $this->lang['level'], $this->get_level_lang()));

		$fieldset->add_field(new FormFieldFree('groups', $this->lang['groups'], $this->build_groups($this->user->get_groups())));
		
		$registration_date = $this->user->get_registration_date();
		$fieldset->add_field(new FormFieldFree('registered_on', $this->lang['registration_date'], $registration_date ? $registration_date->format(Date::FORMAT_DAY_MONTH_YEAR) : ''));
		
		$fieldset->add_field(new FormFieldFree('nbr_msg', $this->lang['number-messages'], $this->user->get_nbr_messages() . '<br>' . '<a href="' . UserUrlBuilder::messages($user_id)->rel() . '">'. $this->lang['messages'] .'</a>'));
		
		$last_connection_date = $this->user->get_last_connection_date();
		$fieldset->add_field(new FormFieldFree('last_connect', $this->lang['last_connection'], $last_connection_date ? $last_connection_date->format(Date::FORMAT_DAY_MONTH_YEAR) : ''));

		if (AppContext::get_current_user()->check_auth(UserAccountsConfig::load()->get_auth_read_members(), UserAccountsConfig::AUTH_READ_MEMBERS_BIT) && $this->user->get_show_email())
		{
			$link_email = '<a href="mailto:'. $this->user->get_email() .'" class="basic-button smaller">Mail</a>';
			$fieldset->add_field(new FormFieldFree('email', $this->lang['email'], $link_email));
		}

		if (!$this->same_user_view_profile($this->user->get_id()) && AppContext::get_current_user()->check_level(User::MEMBER_LEVEL))
		{
			$link_mp = '<a href="'. UserUrlBuilder::personnal_message($this->user->get_id())->rel() .'" class="basic-button smaller">MP</a>';
			$fieldset->add_field(new FormFieldFree('private_message', $this->lang['private_message'], $link_mp));
		}

		MemberExtendedFieldsService::display_profile_fields($form, $this->user->get_id());

		$this->form = $form;
	}

	private function same_user_view_profile($user_id)
	{
		return $user_id == AppContext::get_current_user()->get_id();
	}

	private function get_level_lang()
	{
		if (!$this->user->is_banned())
		{
			return UserService::get_level_lang($this->user->get_level());
		}
		return $this->lang['banned'];
	}

	private function build_groups($user_groups)
	{
		$groups_cache = GroupsCache::load();
		$user_groups_html = '';
		$user_groups = explode('|', $user_groups);
		foreach ($user_groups as $key => $group_id)
		{
			if ($group_id > 0)
			{
				if ($groups_cache->group_exists($group_id))
				{
					$group = $groups_cache->get_group($group_id);
					$group_image = !empty($group['img']) ? '<img src="'. TPL_PATH_TO_ROOT .'/images/group/' . $group['img'] . '" alt="' . $group['name'] . '" title="' . $group['name'] . '" class="valign-middle" />' : $group['name'];
					$user_groups_html .= '<li><a href="' . UserUrlBuilder::group($group_id)->rel() . '">' . $group_image . '</a></li>';
				}
			}
		}
		return !empty($user_groups_html) ? '<ul style="list-style-type:none;">' . $user_groups_html . '</ul>' : $this->lang['user'];
	}

	private function build_response(View $view, $user_id)
	{
		$response = new UserDisplayResponse();
		$response->set_page_title(StringVars::replace_vars($this->lang['profile_of'], array('name' => $this->user_informations['login'])));
		$response->add_breadcrumb($this->lang['user'], UserUrlBuilder::users()->rel());
		$response->add_breadcrumb(StringVars::replace_vars($this->lang['profile_of'], array('name' => $this->user_informations['login'])), UserUrlBuilder::profile($user_id)->rel());
		return $response->display($view);
	}
}
?>