<?php
/*##################################################
 *                      AdminUninstallLangController.class.php
 *                            -------------------
 *   begin                : January 20, 2012
 *   copyright            : (C) 2012 Kevin MASSY
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

class AdminUninstallLangController extends AdminController
{
	private $form;
	private $lang;
	private $submit_button;
	private $id;
	private $tpl;
	
	public function execute(HTTPRequest $request)
	{
		$this->init();
		$this->id = $request->get_value('id', null);
		
		if ($this->exists())
		{
			$this->build_form();
			if ($this->submit_button->has_been_submited() && $this->form->validate())
			{
				$this->uninstall($this->form->get_value('drop_files')->get_raw_value());

				AppContext::get_response()->redirect(AdminLangsUrlBuilder::list_installed_langs());
			}
			
			$this->tpl->put('FORM', $this->form->display());

			return new AdminLangsDisplayResponse($this->tpl, $this->lang['langs.uninstall']);
		}
		else
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('admin-langs-common');
		$this->tpl = new StringTemplate('# INCLUDE FORM #');
		$this->tpl->add_lang($this->lang);
	}
	
	private function build_form()
	{
		$form = new HTMLForm('uninstall_lang');
		
		$fieldset = new FormFieldsetHTML('uninstall_lang', $this->lang['langs.uninstall']);
		$form->add_fieldset($fieldset);
	
		$fieldset->add_field(new FormFieldRadioChoice('drop_files', $this->lang['langs.uninstall.drop_files'], '0',
			array(
				new FormFieldRadioChoiceOption($this->lang['langs.yes'], '1'),
				new FormFieldRadioChoiceOption($this->lang['langs.no'], '0')
			)
		));
		
		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);

		$this->form = $form;
	}
	
	private function uninstall($drop_files)
	{
		LangManager::uninstall($this->id, $drop_files);
	}
	
	private function exists()
	{
		if ($this->id == null)
		{
			return false;
		}
		return LangManager::get_lang_existed($this->id);
	}
}

?>