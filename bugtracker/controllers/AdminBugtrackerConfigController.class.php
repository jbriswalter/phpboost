<?php
/*##################################################
 *                              AdminBugtrackerConfigController.class.php
 *                            -------------------
 *   begin                : October 18, 2012
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

class AdminBugtrackerConfigController extends AdminModuleController
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
	
	private $max_input = 50;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		
		$this->check_authorizations();
		
		$this->build_form();
		
		$tpl = new StringTemplate('# INCLUDE MSG # # INCLUDE FORM #');
		
		$tpl->add_lang($this->lang);
		
		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$tpl->put('MSG', MessageHelper::display(LangLoader::get_message('message.success.config', 'errors-common'), E_USER_SUCCESS, 5));
		}
		
		$tpl->put('FORM', $this->form->display());
		
		return new AdminBugtrackerDisplayResponse($tpl, $this->lang['bugs.titles.admin.module_config']);
	}
	
	private function init()
	{
		$this->config = BugtrackerConfig::load();
		$this->lang = LangLoader::get('common', 'bugtracker');
	}
	
	private function check_authorizations()
	{
		if (!BugtrackerAuthorizationsService::check_authorizations()->moderation())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}
	
	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);
		$types = $this->config->get_types();
		$categories = $this->config->get_categories();
		$severities = $this->config->get_severities();
		$priorities = $this->config->get_priorities();
		$versions = $this->config->get_versions();
		
		$main_lang = LangLoader::get('main');
		
		$fieldset = new FormFieldsetHTML('config', $this->lang['bugs.titles.admin.config']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldTextEditor('items_per_page', $this->lang['bugs.config.items_per_page'], (int)$this->config->get_items_per_page(), array(
			'maxlength' => 2, 'size' => 3, 'required' => true),
			array(new FormFieldConstraintIntegerRange(1, 50))
		));
		
		$fieldset->add_field(new FormFieldColorPicker('fixed_bug_color', $this->lang['bugs.config.fixed_bug_color_label'], $this->config->get_fixed_bug_color()));
		
		$fieldset->add_field(new FormFieldColorPicker('rejected_bug_color', $this->lang['bugs.config.rejected_bug_color_label'], $this->config->get_rejected_bug_color()));
		
		$fieldset->add_field(new FormFieldRadioChoice('date_form', $this->lang['bugs.labels.date_format'], $this->config->get_date_form(),
			array(
				new FormFieldRadioChoiceOption(LangLoader::get_message('date', 'date-common'), Date::FORMAT_DAY_MONTH_YEAR),
				new FormFieldRadioChoiceOption($this->lang['bugs.labels.date_time'], Date::FORMAT_DAY_MONTH_YEAR_HOUR_MINUTE)
			)
		));
		
		$fieldset->add_field(new FormFieldCheckbox('cat_in_title_displayed', $this->lang['bugs.config.activ_cat_in_title'], $this->config->is_cat_in_title_displayed()));
		
		$fieldset->add_field(new FormFieldCheckbox('roadmap_enabled', $this->lang['bugs.config.activ_roadmap'], $this->config->is_roadmap_enabled(),
			array('description' => $this->lang['bugs.explain.roadmap'])
		));
		
		$fieldset->add_field(new FormFieldCheckbox('progress_bar_displayed', $this->lang['bugs.config.activ_progress_bar'], $this->config->is_progress_bar_displayed()));
		
		$fieldset = new FormFieldsetHTML('admin_alerts', $this->lang['bugs.config.admin_alerts']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldCheckbox('admin_alerts_enabled', $this->lang['bugs.config.activ_admin_alerts'], $this->config->are_admin_alerts_enabled(),
			array('events' => array('click' => '
				if (HTMLForms.getField("admin_alerts_enabled").getValue()) {
					HTMLForms.getField("admin_alerts_fix_action").enable();
					HTMLForms.getField("admin_alerts_levels").enable();
				} else {
					HTMLForms.getField("admin_alerts_fix_action").disable();
					HTMLForms.getField("admin_alerts_levels").disable();
				}')
			)
		));
		
		$fieldset->add_field(new FormFieldMultipleCheckbox('admin_alerts_levels', $this->lang['bugs.config.admin_alerts_levels'], $this->config->get_admin_alerts_levels(), $this->build_admin_alerts_levels($severities),
			array('hidden' => !$this->config->are_admin_alerts_enabled())
		));
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('admin_alerts_fix_action', $this->lang['bugs.config.admin_alerts_fix_action'], $this->config->get_admin_alerts_fix_action(),
			array(
				new FormFieldSelectChoiceOption($this->lang['bugs.labels.alert_fix'], BugtrackerConfig::FIX),
				new FormFieldSelectChoiceOption($this->lang['bugs.labels.alert_delete'], BugtrackerConfig::DELETE)
			),
			array('hidden' => !$this->config->are_admin_alerts_enabled())
		));
		
		$fieldset = new FormFieldsetHTML('stats', $this->lang['bugs.titles.stats']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldCheckbox('stats_enabled', $this->lang['bugs.config.activ_stats'], $this->config->are_stats_enabled(),
			array('events' => array('click' => '
				if (HTMLForms.getField("stats_enabled").getValue()) {
					HTMLForms.getField("stats_top_posters_enabled").enable();
					HTMLForms.getField("stats_top_posters_number").enable();
				} else {
					HTMLForms.getField("stats_top_posters_enabled").disable();
					HTMLForms.getField("stats_top_posters_number").disable();
				}')
			)
		));
		
		$fieldset->add_field(new FormFieldCheckbox('stats_top_posters_enabled', $this->lang['bugs.config.activ_stats_top_posters'], $this->config->are_stats_top_posters_enabled(),
			array('hidden' => !$this->config->are_stats_enabled(), 'events' => array('click' => '
				if (HTMLForms.getField("stats_top_posters_enabled").getValue()) {
					HTMLForms.getField("stats_top_posters_number").enable();
				} else {
					HTMLForms.getField("stats_top_posters_number").disable();
				}')
		)));
		
		$fieldset->add_field(new FormFieldTextEditor('stats_top_posters_number', $this->lang['bugs.config.stats_top_posters_number'], (int)$this->config->get_stats_top_posters_number(), array(
			'maxlength' => 3, 'size' => 3, 'hidden' => !$this->config->are_stats_top_posters_enabled()),
			array(new FormFieldConstraintRegex('`^[0-9]+$`i'))
		));
		
		$fieldset = new FormFieldsetHTML('pm', $this->lang['bugs.config.pm']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldCheckbox('pm_enabled', $this->lang['bugs.config.activ_pm'], $this->config->are_pm_enabled() ? FormFieldCheckbox::CHECKED : FormFieldCheckbox::UNCHECKED,
			array('events' => array('click' => '
				if (HTMLForms.getField("pm_enabled").getValue()) {
					HTMLForms.getField("pm_comment_enabled").enable();
					HTMLForms.getField("pm_fix_enabled").enable();
					HTMLForms.getField("pm_assign_enabled").enable();
					HTMLForms.getField("pm_edit_enabled").enable();
					HTMLForms.getField("pm_reject_enabled").enable();
					HTMLForms.getField("pm_reopen_enabled").enable();
					HTMLForms.getField("pm_delete_enabled").enable();
				} else {
					HTMLForms.getField("pm_comment_enabled").disable();
					HTMLForms.getField("pm_fix_enabled").disable();
					HTMLForms.getField("pm_assign_enabled").disable();
					HTMLForms.getField("pm_edit_enabled").disable();
					HTMLForms.getField("pm_reject_enabled").disable();
					HTMLForms.getField("pm_reopen_enabled").disable();
					HTMLForms.getField("pm_delete_enabled").disable();
				}')
		)));
		
		$fieldset->add_field(new FormFieldCheckbox('pm_comment_enabled', $this->lang['bugs.config.activ_pm.comment'], $this->config->are_pm_comment_enabled(), array(
			'hidden' => !$this->config->are_pm_enabled())
		));
		
		$fieldset->add_field(new FormFieldCheckbox('pm_fix_enabled', $this->lang['bugs.config.activ_pm.fix'], $this->config->are_pm_fix_enabled(), array(
			'hidden' => !$this->config->are_pm_enabled())
		));
		
		$fieldset->add_field(new FormFieldCheckbox('pm_assign_enabled', $this->lang['bugs.config.activ_pm.assign'], $this->config->are_pm_assign_enabled(), array(
			'hidden' => !$this->config->are_pm_enabled())
		));
		
		$fieldset->add_field(new FormFieldCheckbox('pm_edit_enabled', $this->lang['bugs.config.activ_pm.edit'], $this->config->are_pm_edit_enabled(), array(
			'hidden' => !$this->config->are_pm_enabled())
		));
		
		$fieldset->add_field(new FormFieldCheckbox('pm_reject_enabled', $this->lang['bugs.config.activ_pm.reject'], $this->config->are_pm_reject_enabled(), array(
			'hidden' => !$this->config->are_pm_enabled())
		));
		
		$fieldset->add_field(new FormFieldCheckbox('pm_reopen_enabled', $this->lang['bugs.config.activ_pm.reopen'], $this->config->are_pm_reopen_enabled(), array(
			'hidden' => !$this->config->are_pm_enabled())
		));
		
		$fieldset->add_field(new FormFieldCheckbox('pm_delete_enabled', $this->lang['bugs.config.activ_pm.delete'], $this->config->are_pm_delete_enabled(), array(
			'hidden' => !$this->config->are_pm_enabled())
		));
		
		$fieldset = new FormFieldsetHTML('contents-value', $this->lang['bugs.titles.contents_value_title']);
		$fieldset->set_description($this->lang['bugs.explain.contents_value']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldRichTextEditor('contents_value', $this->lang['bugs.titles.contents_value'], $this->config->get_contents_value(), 
			array('rows' => 8, 'cols' => 47)
		));
		
		$fieldset = new FormFieldsetHTML('types-fieldset', $this->lang['bugs.titles.types']);
		$fieldset->set_description($this->lang['bugs.explain.type'] . '<br /><br />' . $this->lang['bugs.explain.remarks']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldRadioChoice('type_mandatory', $this->lang['bugs.labels.type_mandatory'], $this->config->is_type_mandatory(),
			array(
				new FormFieldRadioChoiceOption($main_lang['yes'], 1),
				new FormFieldRadioChoiceOption($main_lang['no'], 0)
			)
		));
		
		$types_table = new FileTemplate('bugtracker/AdminBugtrackerTypesListController.tpl');
		$types_table->add_lang($this->lang);
		
		foreach ($types as $key => $type)
		{
			$types_table->assign_block_vars('types', array(
				'C_IS_DEFAULT'	=> $this->config->get_default_type() == $key,
				'ID'			=> $key,
				'NAME'			=> stripslashes($type),
				'LINK_DELETE'	=> BugtrackerUrlBuilder::delete_parameter('type', $key)->rel()
			));
		}
		
		$types_table->put_all(array(
			'C_TYPES'							=> !empty($types),
			'C_DISPLAY_DEFAULT_DELETE_BUTTON'	=> $this->config->get_default_type(),
			'MAX_INPUT'							=> $this->max_input,
		 	'NEXT_ID'							=> $key + 1,
			'LINK_DELETE_DEFAULT'				=> BugtrackerUrlBuilder::delete_default_parameter('type')->rel()
		));
		
		$fieldset->add_field(new FormFieldHTML('types_table', $types_table->render()));
		
		$fieldset = new FormFieldsetHTML('categories-fieldset', $this->lang['bugs.titles.categories']);
		$fieldset->set_description($this->lang['bugs.explain.category'] . '<br /><br />' . $this->lang['bugs.explain.remarks']);
		$form->add_fieldset($fieldset);
		
		$categories_table = new FileTemplate('bugtracker/AdminBugtrackerCategoriesListController.tpl');
		$categories_table->add_lang($this->lang);
		
		$fieldset->add_field(new FormFieldRadioChoice('category_mandatory', $this->lang['bugs.labels.category_mandatory'], $this->config->is_category_mandatory(),
			array(
				new FormFieldRadioChoiceOption($main_lang['yes'], 1),
				new FormFieldRadioChoiceOption($main_lang['no'], 0)
			)
		));
		
		foreach ($categories as $key => $category)
		{
			$categories_table->assign_block_vars('categories', array(
				'C_IS_DEFAULT'	=> $this->config->get_default_category() == $key,
				'ID'			=> $key,
				'NAME'			=> stripslashes($category),
				'LINK_DELETE'	=> BugtrackerUrlBuilder::delete_parameter('category', $key)->rel()
			));
		}
		
		$categories_table->put_all(array(
			'C_CATEGORIES'						=> !empty($categories),
			'MAX_INPUT'							=> $this->max_input,
		 	'NEXT_ID'							=> $key + 1,
			'C_DISPLAY_DEFAULT_DELETE_BUTTON'	=> $this->config->get_default_category(),
			'LINK_DELETE_DEFAULT'				=> BugtrackerUrlBuilder::delete_default_parameter('category')->rel()
		));
		
		$fieldset->add_field(new FormFieldHTML('categories_table', $categories_table->render()));
		
		$fieldset = new FormFieldsetHTML('severities-fieldset', $this->lang['bugs.titles.severities']);
		$fieldset->set_description($this->lang['bugs.explain.severity'] . '<br /><br />' . $this->lang['bugs.explain.remarks']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldRadioChoice('severity_mandatory', $this->lang['bugs.labels.severity_mandatory'], $this->config->is_severity_mandatory(),
			array(
				new FormFieldRadioChoiceOption($main_lang['yes'], 1),
				new FormFieldRadioChoiceOption($main_lang['no'], 0)
			)
		));
		
		$severities_table = new FileTemplate('bugtracker/AdminBugtrackerSeveritiesListController.tpl');
		$severities_table->add_lang($this->lang);
		
		foreach ($severities as $key => $severity)
		{
			$severities_table->assign_block_vars('severities', array(
				'C_IS_DEFAULT'	=> $this->config->get_default_severity() == $key,
				'ID'			=> $key,
				'NAME'			=> stripslashes($severity['name']),
				'COLOR'			=> $severity['color']
			));
		}
		
		$severities_table->put_all(array(
			'C_SEVERITIES'						=> !empty($severities),
			'C_DISPLAY_DEFAULT_DELETE_BUTTON'	=> $this->config->get_default_severity(),
			'LINK_DELETE_DEFAULT'				=> BugtrackerUrlBuilder::delete_default_parameter('severity')->rel()
		));
		
		$fieldset->add_field(new FormFieldHTML('severities_table', $severities_table->render()));
		
		$fieldset = new FormFieldsetHTML('priorities-fieldset', $this->lang['bugs.titles.priorities']);
		$fieldset->set_description($this->lang['bugs.explain.priority'] . '<br /><br />' . $this->lang['bugs.explain.remarks']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldRadioChoice('priority_mandatory', $this->lang['bugs.labels.priority_mandatory'], $this->config->is_priority_mandatory(),
			array(
				new FormFieldRadioChoiceOption($main_lang['yes'], 1),
				new FormFieldRadioChoiceOption($main_lang['no'], 0)
			)
		));
		
		$priorities_table = new FileTemplate('bugtracker/AdminBugtrackerPrioritiesListController.tpl');
		$priorities_table->add_lang($this->lang);
		
		foreach ($priorities as $key => $priority)
		{
			$priorities_table->assign_block_vars('priorities', array(
				'C_IS_DEFAULT'	=> $this->config->get_default_priority() == $key,
				'ID'			=> $key,
				'NAME'			=> stripslashes($priority)
			));
		}
		
		$priorities_table->put_all(array(
			'C_PRIORITIES'						=> !empty($priorities),
			'C_DISPLAY_DEFAULT_DELETE_BUTTON'	=> $this->config->get_default_priority(),
			'LINK_DELETE_DEFAULT'				=> BugtrackerUrlBuilder::delete_default_parameter('priority')->rel()
		));
		
		$fieldset->add_field(new FormFieldHTML('priorities_table', $priorities_table->render()));
		
		$fieldset = new FormFieldsetHTML('versions-fieldset', $this->lang['bugs.titles.versions']);
		$fieldset->set_description($this->lang['bugs.explain.version'] . '<br /><br />' . $this->lang['bugs.explain.remarks']);
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldRadioChoice('detected_in_version_mandatory', $this->lang['bugs.labels.detected_in_mandatory'], $this->config->is_detected_in_version_mandatory(),
			array(
				new FormFieldRadioChoiceOption($main_lang['yes'], 1),
				new FormFieldRadioChoiceOption($main_lang['no'], 0)
			)
		));
		
		$versions_table = new FileTemplate('bugtracker/AdminBugtrackerVersionsListController.tpl');
		$versions_table->add_lang($this->lang);
		
		foreach ($versions as $key => $version)
		{
			$versions_table->assign_block_vars('versions', array(
				'C_IS_DEFAULT'		=> $this->config->get_default_version() == $key,
				'C_DETECTED_IN'		=> $version['detected_in'],
				'ID'				=> $key,
				'NAME'				=> stripslashes($version['name']),
				'RELEASE_DATE'		=> !empty($version['release_date']) ? $version['release_date']->format(Date::FORMAT_DAY_MONTH_YEAR) : '',
				'DAY'				=> !empty($version['release_date']) ? $version['release_date']->get_day() : date('d'),
				'MONTH'				=> !empty($version['release_date']) ? $version['release_date']->get_month() : date('n'),
				'YEAR'				=> !empty($version['release_date']) ? $version['release_date']->get_year() : date('Y'),
				'LINK_DELETE'		=> BugtrackerUrlBuilder::delete_parameter('version', $key)->rel()
			));
		}
		
		$versions_table->put_all(array(
			'C_VERSIONS'						=> !empty($versions),
			'MAX_INPUT'							=> $this->max_input,
		 	'NEXT_ID'							=> $key + 1,
			'DAY'								=> date('d'),
			'MONTH'								=> date('n'),
			'YEAR'								=> date('Y'),
			'C_DISPLAY_DEFAULT_DELETE_BUTTON'	=> $this->config->get_default_version(),
			'LINK_DELETE_DEFAULT'				=> BugtrackerUrlBuilder::delete_default_parameter('version')->rel()
		));
		
		$fieldset->add_field(new FormFieldHTML('versions_table', $versions_table->render()));
		
		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());
		
		$this->form = $form;
	}
	
	private function build_admin_alerts_levels($severities)
	{
		$list = array();
		
		foreach ($severities as $key => $severity)
		{
			$list[] = new FormFieldMultipleCheckboxOption($key, stripslashes($severity['name']));
		}
		
		return $list;
	}
	
	private function save()
	{
		$request = AppContext::get_request();
		
		$types = $this->config->get_types();
		$categories = $this->config->get_categories();
		$severities = $this->config->get_severities();
		$priorities = $this->config->get_priorities();
		$versions = $this->config->get_versions();
		
		foreach ($types as $key => $type)
		{
			$new_type_name = $request->get_value('type' . $key, '');
			$types[$key] = (!empty($new_type_name) && $new_type_name != $type) ? $new_type_name : $type;
		}
		
		$nb_types = count($types);
		for ($i = 1; $i <= $this->max_input; $i++)
		{
			$type = 'type_' . $i;
			if ($request->has_postparameter($type) && $request->get_poststring($type))
			{
				if (empty($nb_types))
					$types[1] = addslashes($request->get_poststring($type));
				else
					$types[] = addslashes($request->get_poststring($type));
				$nb_types++;
			}
		}
		
		foreach ($categories as $key => $category)
		{
			$new_category_name = $request->get_value('category' . $key, '');
			$categories[$key] = (!empty($new_category_name) && $new_category_name != $category) ? $new_category_name : $category;
		}
		
		$nb_categories = count($categories);
		for ($i = 1; $i <= $this->max_input; $i++)
		{
			$category = 'category_' . $i;
			if ($request->has_postparameter($category) && $request->get_poststring($category))
			{
				if (empty($nb_categories))
					$categories[1] = addslashes($request->get_poststring($category));
				else
					$categories[] = addslashes($request->get_poststring($category));
				$nb_categories++;
			}
		}
		
		foreach ($severities as $key => $severity)
		{
			$new_severity_name = $request->get_value('severity' . $key, '');
			$new_severity_color = $request->get_value('color' . $key, '');
			$severities[$key]['name'] = (!empty($new_severity_name) && $new_severity_name != $severity['name']) ? $new_severity_name : $severity['name'];
			$severities[$key]['color'] = ($new_severity_color != $severity['color']) ? $new_severity_color : $severity['color'];
		}
		
		foreach ($priorities as $key => $priority)
		{
			$new_priority_name = $request->get_value('priority' . $key, '');
			$priorities[$key] = (!empty($new_priority_name) && $new_priority_name != $priority) ? $new_priority_name : $priority;
		}
		
		foreach ($versions as $key => $version)
		{
			$new_version_name = $request->get_value('version' . $key, '');
			$new_version_release_date = $request->get_value('release_date' . $key, '');
			$new_version_detected_in = (bool)$request->get_value('detected_in' . $key, '');
			$versions[$key]['name'] = (!empty($new_version_name) && $new_version_name != $version['name']) ? $new_version_name : $version['name'];
			$versions[$key]['release_date'] = $new_version_release_date ? new Date(DATE_FROM_STRING, TIMEZONE_AUTO, $new_version_release_date, LangLoader::get_message('date_format_day_month_year', 'date-common')) : '';
			$versions[$key]['detected_in'] = ($new_version_detected_in != $version['detected_in']) ? $new_version_detected_in : $version['detected_in'];
		}
		
		$nb_versions = count($versions);
		for ($i = 1; $i <= $this->max_input; $i++)
		{
			$version = 'version_' . $i;
			if ($request->has_postparameter($version) && $request->get_poststring($version))
			{
				$array_id = empty($nb_versions) ? 1 : ($nb_versions + 1);
				$version_release_date = $request->get_value('release_date_' . $i, '');
				
				$versions[$array_id] = array(
					'name'			=> addslashes($request->get_poststring($version)),
					'release_date'	=> $version_release_date ? new Date(DATE_FROM_STRING, TIMEZONE_AUTO, $version_release_date, LangLoader::get_message('date_format_day_month_year', 'date-common')) : '',
					'detected_in'	=> (bool)$request->get_value('detected_in' . $i, '')
				);
				$nb_versions++;
			}
		}
		
		$this->config->set_items_per_page($this->form->get_value('items_per_page'));
		$this->config->set_rejected_bug_color($this->form->get_value('rejected_bug_color'));
		$this->config->set_fixed_bug_color($this->form->get_value('fixed_bug_color'));
		$this->config->set_date_form($this->form->get_value('date_form')->get_raw_value());
		
		if ($this->form->get_value('cat_in_title_displayed'))
			$this->config->display_cat_in_title();
		else
			$this->config->hide_cat_in_title();
		
		if ($this->form->get_value('roadmap_enabled'))
			$this->config->enable_roadmap();
		else
			$this->config->disable_roadmap();
		
		if ($this->form->get_value('progress_bar_displayed'))
			$this->config->display_progress_bar();
		else
			$this->config->hide_progress_bar();
		
		if ($this->form->get_value('stats_enabled'))
		{
			$this->config->enable_stats();
			if ($this->form->get_value('stats_top_posters_enabled'))
			{
				$this->config->enable_stats_top_posters();
				$this->config->set_stats_top_posters_number($this->form->get_value('stats_top_posters_number'));
			}
			else
				$this->config->disable_stats_top_posters();
		}
		else
			$this->config->disable_stats();
		
		if ($this->form->get_value('admin_alerts_enabled'))
		{
			$this->config->enable_admin_alerts();
			$this->config->set_admin_alerts_fix_action($this->form->get_value('admin_alerts_fix_action')->get_raw_value());
			
			$admin_alerts_levels = array();
			foreach ($this->form->get_value('admin_alerts_levels') as $level => $value)
			{
				$admin_alerts_levels[] = (string)$value->get_id();
			}
			$this->config->set_admin_alerts_levels($admin_alerts_levels);
		}
		else
			$this->config->disable_admin_alerts();
		
		if ($this->form->get_value('pm_enabled'))
		{
			$this->config->enable_pm();
			
			if ($this->form->get_value('pm_comment_enabled'))
				$this->config->enable_pm_comment();
			else
				$this->config->disable_pm_comment();
			
			if ($this->form->get_value('pm_fix_enabled'))
				$this->config->enable_pm_fix();
			else
				$this->config->disable_pm_fix();
			
			if ($this->form->get_value('pm_assign_enabled'))
				$this->config->enable_pm_assign();
			else
				$this->config->disable_pm_assign();
			
			if ($this->form->get_value('pm_edit_enabled'))
				$this->config->enable_pm_edit();
			else
				$this->config->disable_pm_edit();
			
			if ($this->form->get_value('pm_reject_enabled'))
				$this->config->enable_pm_reject();
			else
				$this->config->disable_pm_reject();
			
			if ($this->form->get_value('pm_reopen_enabled'))
				$this->config->enable_pm_reopen();
			else
				$this->config->disable_pm_reopen();
			
			if ($this->form->get_value('pm_delete_enabled'))
				$this->config->enable_pm_delete();
			else
				$this->config->disable_pm_delete();
		}
		else
			$this->config->disable_pm();
		
		$this->config->set_contents_value($this->form->get_value('contents_value'));
		
		if ($this->form->get_value('type_mandatory')->get_raw_value())
			$this->config->type_mandatory();
		else
			$this->config->type_not_mandatory();
		
		$this->config->set_types($types);
		$this->config->set_default_type($request->get_value('default_type', 0));
		
		if ($this->form->get_value('category_mandatory')->get_raw_value())
			$this->config->category_mandatory();
		else
			$this->config->category_not_mandatory();
		
		$this->config->set_categories($categories);
		$this->config->set_default_category($request->get_value('default_category', 0));
		
		if ($this->form->get_value('severity_mandatory')->get_raw_value())
			$this->config->severity_mandatory();
		else
			$this->config->severity_not_mandatory();
		
		$this->config->set_severities($severities);
		$this->config->set_default_severity($request->get_value('default_severity', 0));
		
		if ($this->form->get_value('priority_mandatory')->get_raw_value())
			$this->config->priority_mandatory();
		else
			$this->config->priority_not_mandatory();
		
		$this->config->set_priorities($priorities);
		$this->config->set_default_priority($request->get_value('default_priority', 0));
		
		if ($this->form->get_value('detected_in_version_mandatory')->get_raw_value())
			$this->config->detected_in_version_mandatory();
		else
			$this->config->detected_in_version_not_mandatory();
		
		$this->config->set_versions($versions);
		$this->config->set_default_version($request->get_value('default_version', 0));
		
		BugtrackerConfig::save();
		
		BugtrackerStatsCache::invalidate();
	}
}
?>
