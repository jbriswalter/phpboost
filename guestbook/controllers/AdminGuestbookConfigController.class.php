<?php
/*##################################################
 *                              AdminGuestbookConfigController.class.php
 *                            -------------------
 *   begin                : November 30, 2012
 *   copyright            : (C) 2012 Julien BRISWALTER
 *   email                : julienseth78@phpboost.com
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
 * @author Julien BRISWALTER <julienseth78@phpboost.com>
 */
class AdminGuestbookConfigController extends AdminModuleController
{
	private $lang;
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonDefaultSubmit
	 */
	private $submit_button;
	private $config;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		$this->build_form();
		
		$tpl = new StringTemplate('# INCLUDE MSG # # INCLUDE FORM #');
		
		$tpl->add_lang($this->lang);
		
		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
		}
		
		//Message helper
		$error = $request->get_value('error', '');
		switch ($error)
		{
			case 'require_items_per_page':
				$errstr = $this->lang['admin.config.error.require_items_per_page'];
				break;
			default:
				$errstr = '';
		}
		if (!empty($errstr))
			$tpl->put('MSG', MessageHelper::display($errstr, E_USER_NOTICE));
		
		$success = $request->get_value('success', '');
		switch ($success)
		{
			case 'config_modified':
				$errstr = $this->lang['admin.config.success'];
				break;
			default:
				$errstr = '';
		}
		if (!empty($errstr))
			$tpl->put('MSG', MessageHelper::display($errstr, E_USER_SUCCESS, 5));
		
		$tpl->put('FORM', $this->form->display());
		
		return new AdminGuestbookDisplayResponse($tpl, $this->lang['module_title']);
	}
	
	private function init()
	{
		$this->config = GuestbookConfig::load();
		$this->lang = LangLoader::get('common', 'guestbook');
	}
	
	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
		
		$fieldset = new FormFieldsetHTML('config', $this->lang['admin.config']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldTextEditor('items_per_page', $this->lang['admin.config.items_per_page'], (int)$this->config->get_items_per_page(), array(
			'class' => 'text', 'maxlength' => 3, 'size' => 3, 'required' => true),
			array(new FormFieldConstraintRegex('`^[0-9]+$`i'))
		));
		
		$fieldset->add_field(new FormFieldCheckbox('enable_captcha', $this->lang['admin.config.enable_captcha'], $this->config->is_captcha_enabled(),
			array('events' => array('click' => 'if (HTMLForms.getField("enable_captcha").getValue()) { HTMLForms.getField("captcha_difficulty_level").enable(); } else { HTMLForms.getField("captcha_difficulty_level").disable(); }'))));
			
		$fieldset->add_field(new FormFieldSimpleSelectChoice('captcha_difficulty_level', $this->lang['admin.config.captcha_difficulty'], $this->config->get_captcha_difficulty_level(), $this->generate_difficulty_level_options(),
			array('hidden' => !$this->config->is_captcha_enabled())));
		
		$fieldset->add_field(new FormFieldMultipleSelectChoice('forbidden_tags', $this->lang['admin.config.forbidden-tags'], $this->config->get_forbidden_tags(),
			$this->generate_forbidden_tags_option(), array('size' => 10)
		));
		
		$fieldset->add_field(new FormFieldTextEditor('max_link', $this->lang['admin.config.max_links'], $this->config->get_maximum_links_message(), array(
			'class' => 'text', 'description' => $this->lang['admin.config.max_links_explain'], 'maxlength' => 3, 'size' => 3, 'required' => true),
			array(new FormFieldConstraintRegex('`^([-]?[0-9]+)$`i', '`^([-]?[0-9]+)$`i', $this->lang['admin.config.error.number-required']))
		));
		
		$fieldset_authorizations = new FormFieldsetHTML('authorizations', $this->lang['admin.authorizations']);
		$form->add_fieldset($fieldset_authorizations);
		
		$auth_settings = new AuthorizationsSettings(array(
			new ActionAuthorization($this->lang['admin.authorizations.read'], GuestbookAuthorizationsService::READ_AUTHORIZATIONS),
			new ActionAuthorization($this->lang['admin.authorizations.write'], GuestbookAuthorizationsService::WRITE_AUTHORIZATIONS),
			new ActionAuthorization($this->lang['admin.authorizations.moderation'], GuestbookAuthorizationsService::MODERATION_AUTHORIZATIONS)
		));
		
		$auth_settings->build_from_auth_array($this->config->get_authorizations());
		$fieldset_authorizations->add_field(new FormFieldAuthorizationsSetter('authorizations', $auth_settings));
		
		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());
		
		$this->form = $form;
	}
	
	private function generate_difficulty_level_options()
	{
		$options = array();
		for ($i = 0; $i <= 4; $i++)
		{
			$options[] = new FormFieldSelectChoiceOption($i, $i);
		}
		return $options;
	}
	
	private function generate_forbidden_tags_option()
	{
		$options = array();
		foreach (AppContext::get_content_formatting_service()->get_available_tags() as $identifier => $name)
		{
			$options[] = new FormFieldSelectChoiceOption($name, $identifier);
		}
		return $options;
	}
	
	private function save()
	{
		$items_per_page = $this->form->get_value('items_per_page');
		if (!empty($items_per_page))
		{
			$this->config->set_items_per_page($items_per_page);
			if ($this->form->get_value('enable_captcha'))
			{
				$this->config->enable_captcha();
				$this->config->set_captcha_difficulty_level($this->form->get_value('captcha_difficulty_level')->get_raw_value());
			}
			else
				$this->config->disable_captcha();
			
			$forbidden_tags = array();
			foreach ($this->form->get_value('forbidden_tags') as $field => $option)
			{
				$forbidden_tags[] = $option->get_raw_value();
			}
			
			$this->config->set_forbidden_tags($forbidden_tags);
			$this->config->set_maximum_links_message($this->form->get_value('max_link', -1));
			$this->config->set_authorizations($this->form->get_value('authorizations')->build_auth_array());
			
			GuestbookConfig::save();
			GuestbookMessagesCache::invalidate();
			
			AppContext::get_response()->redirect(GuestbookUrlBuilder::configuration_success('config_modified'));
		}
		else
			AppContext::get_response()->redirect(GuestbookUrlBuilder::configuration_error('require_items_per_page'));
	}
}
?>