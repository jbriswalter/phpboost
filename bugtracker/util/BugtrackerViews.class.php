<?php
/*##################################################
 *                          BugtrackerViews.class.php
 *                            -------------------
 *   begin                : April 29, 2013
 *   copyright            : (C) 2013 Julien BRISWALTER
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
class BugtrackerViews
{
	public static function build_body_view(View $view, $current_page, $bug_id = 0)
	{
		$lang = LangLoader::get('common', 'bugtracker');
		
		$request = AppContext::get_request();
		$back_page = $request->get_value('back_page', '');
		$page = $request->get_int('page', 1);
		$back_filter = $request->get_value('back_filter', '');
		$filter = $request->get_value('filter', '');
		$filter_id = $request->get_value('filter_id', '');
		
		$config = BugtrackerConfig::load();
		$versions = $config->get_versions();
		$nbr_versions = array_keys($versions);
		
		$body_view = new FileTemplate('bugtracker/BugtrackerBody.tpl');
		$body_view->add_lang($lang);
		$body_view->put_all(array(
			'TEMPLATE'					=> $view,
			'C_ROADMAP_ACTIVATED' 		=> $config->is_roadmap_enabled() && !empty($nbr_versions),
			'C_STATS_ACTIVATED' 		=> $config->are_stats_enabled(),
			'C_HIDE'					=> in_array($current_page, array('fix', 'assign', 'history', 'detail', 'add', 'edit', 'reject', 'reopen')),
			'CLASS_BUG_UNSOLVED'		=> $current_page == 'unsolved' ? 'current' : '',
			'CLASS_BUG_SOLVED'			=> $current_page == 'solved' ? 'current' : '',
			'CLASS_BUG_ROADMAP'			=> $current_page == 'roadmap' ? 'current' : '',
			'CLASS_BUG_STATS'			=> $current_page == 'stats' ? 'current' : '',
			'TITLE'						=> $lang['bugs.titles.' . $current_page] . (in_array($current_page, array('fix', 'assign', 'history', 'detail', 'edit', 'reject', 'reopen')) ? ' #' . $bug_id : ''),
			'LINK_BUG_UNSOLVED'			=> BugtrackerUrlBuilder::unsolved()->rel(),
			'LINK_BUG_SOLVED'			=> BugtrackerUrlBuilder::solved()->rel(),
			'LINK_BUG_ROADMAP'			=> BugtrackerUrlBuilder::roadmap()->rel(),
			'LINK_BUG_STATS'			=> BugtrackerUrlBuilder::stats()->rel(),
			'LINK_SYNDICATION_UNSOLVED'	=> SyndicationUrlBuilder::rss('bugtracker', 0)->rel(),
			'LINK_SYNDICATION_SOLVED'	=> SyndicationUrlBuilder::rss('bugtracker', 1)->rel()
		));
		
		return $body_view;
	}
	
	public static function build_filters($current_page, $nbr_bugs = 0)
	{
		$lang = LangLoader::get('common', 'bugtracker');
		$object = new self();
		
		$request = AppContext::get_request();
		$page = $request->get_int('page', 1);
		$filter = $request->get_value('filter', '');
		$filter_id = $request->get_value('filter_id', '');
		
		if (!empty($filter) && empty($filter_id))
		{
			$filter = $filter_id = '';
		}
		
		$filters_tmp = $filters = !empty($filter) ? explode('-', $filter) : array();
		$filters_ids_tmp = $filters_ids = !empty($filter_id) ? explode('-', $filter_id) : array();
		
		$display_save_button = AppContext::get_current_user()->check_level(User::MEMBER_LEVEL) && count($filters) >= 1;
		
		$config = BugtrackerConfig::load();
		$types = $config->get_types();
		$categories = $config->get_categories();
		$severities = $config->get_severities();
		$versions = $config->get_versions_detected();
		$all_versions = $config->get_versions();
		
		$display_types = count($types) > 1;
		$display_categories = count($categories) > 1;
		$display_severities = count($severities) > 1;
		$display_versions = count($versions) > 1;
		
		$filters_number = 1;
		if ($display_types == true) $filters_number = $filters_number + 1;
		if ($display_categories == true) $filters_number = $filters_number + 1;
		if ($display_severities == true) $filters_number = $filters_number + 1;
		if ($display_versions == true) $filters_number = $filters_number + 1;
		if (!empty($filters)) $filters_number = $filters_number + 1;
		
		$filters_view = new FileTemplate('bugtracker/BugtrackerFilter.tpl');
		$filters_view->add_lang($lang);
		
		$result = PersistenceContext::get_querier()->select("SELECT *
		FROM " . BugtrackerSetup::$bugtracker_users_filters_table . "
		WHERE page = :page AND user_id = :user_id",
			array(
				'page' => $current_page,
				'user_id' => AppContext::get_current_user()->get_id()
			), SelectQueryResult::FETCH_ASSOC
		);
		
		$saved_filters = false;
		while ($row = $result->fetch())
		{
			$row_filters_tmp = $row_filters = !empty($row['filters']) ? explode('-', $row['filters']) : array();
			$row_filters_ids_tmp = $row_filters_ids = !empty($row['filters_ids']) ? explode('-', $row['filters_ids']) : array();
			
			sort($filters_tmp, SORT_STRING);
			sort($row_filters_tmp, SORT_STRING);
			sort($filters_ids_tmp, SORT_STRING);
			sort($row_filters_ids_tmp, SORT_STRING);
			if (implode('-', $filters_tmp) == implode('-', $row_filters_tmp) && implode('-', $filters_ids_tmp) == implode('-', $row_filters_ids_tmp))
				$display_save_button = false;
			
			$filter_not_saved_value = '-------------';
			
			$filters_view->assign_block_vars('filters', array(
				'ID'					=> $row['id'],
				'FILTER'				=> '|	' . (in_array('type', $row_filters) && $row_filters_ids[array_search('type', $row_filters)] ? $types[$row_filters_ids[array_search('type', $row_filters)]] : $filter_not_saved_value) . '	|	' . (in_array('category', $row_filters) && $row_filters_ids[array_search('category', $row_filters)] ? $categories[$row_filters_ids[array_search('category', $row_filters)]] : $filter_not_saved_value) . '	|	' . (in_array('severity', $row_filters) && $row_filters_ids[array_search('severity', $row_filters)] ? $severities[$row_filters_ids[array_search('severity', $row_filters)]]['name'] : $filter_not_saved_value) . '	|	' . (in_array('status', $row_filters) && $row_filters_ids[array_search('status', $row_filters)] ? $lang['bugs.status.' . $row_filters_ids[array_search('status', $row_filters)]] : $filter_not_saved_value) . '	|	' . ($current_page == 'unsolved' ? (in_array('detected_in', $row_filters) && $row_filters_ids[array_search('detected_in', $row_filters)] ? $versions[$row_filters_ids[array_search('detected_in', $row_filters)]]['name'] : $filter_not_saved_value) : (in_array('fixed_in', $row_filters) && $row_filters_ids[array_search('fixed_in', $row_filters)] ? $all_versions[$row_filters_ids[array_search('fixed_in', $row_filters)]]['name'] : $filter_not_saved_value)) . '	|',
				'LINK_FILTER'			=> ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . $row['filters'] . '/' . $row['filters_ids'])->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . $row['filters'] . '/' . $row['filters_ids'])->rel()),
				'LINK_FILTER_DELETE'	=> BugtrackerUrlBuilder::delete_filter($row['id'] . '/' . $current_page . '/' . $page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel()
			));
			$saved_filters = true;
		}
		
		$filters_view->put_all(array(
			'L_FILTERS'				=> $filters_number > 1 ? $lang['bugs.titles.filters'] : $lang['bugs.titles.filter'],
			'C_FILTER'				=> count($filters) == 1,
			'C_FILTERS'				=> count($filters) > 1,
			'C_DISPLAY_TYPES'		=> $display_types,
			'C_DISPLAY_CATEGORIES'	=> $display_categories,
			'C_DISPLAY_SEVERITIES'	=> $display_severities,
			'C_DISPLAY_VERSIONS'	=> $display_versions,
			'C_DISPLAY_SAVE_BUTTON'	=> $display_save_button,
			'C_SAVED_FILTERS'		=> $saved_filters,
			'C_HAS_SELECTED_FILTERS'=> $filters,
			'FILTERS_NUMBER'		=> $filters_number,
			'BUGS_NUMBER'			=> $nbr_bugs,
			'LINK_FILTER_SAVE'		=> BugtrackerUrlBuilder::add_filter($current_page . '/' . $page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel(),
			'SELECT_TYPE'			=> $object->build_types_form($current_page,($filter == 'type') ? $filter_id : (in_array('type', $filters) ? $filters_ids[array_search('type', $filters)] : 0), $filters, $filters_ids)->display(),
			'SELECT_CATEGORY'		=> $object->build_categories_form($current_page, ($filter == 'category') ? $filter_id : (in_array('category', $filters) ? $filters_ids[array_search('category', $filters)] : 0), $filters, $filters_ids)->display(),
			'SELECT_SEVERITY'		=> $object->build_severities_form($current_page, ($filter == 'severity') ? $filter_id : (in_array('severity', $filters) ? $filters_ids[array_search('severity', $filters)] : 0), $filters, $filters_ids)->display(),
			'SELECT_STATUS'			=> $object->build_status_form($current_page, ($filter == 'status') ? $filter_id : (in_array('status', $filters) ? $filters_ids[array_search('status', $filters)] : 0), $filters, $filters_ids, $lang)->display(),
			'SELECT_VERSION'		=> $object->build_versions_form($current_page, ($current_page == 'unsolved' ? (($filter == 'detected_in') ? $filter_id : (in_array('detected_in', $filters) ? $filters_ids[array_search('detected_in', $filters)] : 0)) : (($filter == 'fixed_in') ? $filter_id : (in_array('fixed_in', $filters) ? $filters_ids[array_search('fixed_in', $filters)] : 0))), $filters, $filters_ids)->display()
		));
		
		return $filters_view;
	}
	
	public static function build_legend($list, $current_page)
	{
		$lang = LangLoader::get('common', 'bugtracker');
		
		$config = BugtrackerConfig::load();
		$severities = $config->get_severities();
		
		$legend_view = new FileTemplate('bugtracker/BugtrackerLegend.tpl');
		$legend_view->add_lang($lang);
		
		$legend_colspan = 0;
		
		foreach ($list as $element)
		{
			if (($current_page == 'solved') || (!empty($element) && isset($severities[$element])))
			{
				$legend_view->assign_block_vars('legend', array(
					'COLOR'	=> $current_page == 'solved' ? ($element == 'fixed' ? $config->get_fixed_bug_color() : $config->get_rejected_bug_color()) : stripslashes($severities[$element]['color']),
					'NAME'	=> $current_page == 'solved' ? $lang['bugs.status.' . $element] : stripslashes($severities[$element]['name'])
				));
				
				$legend_colspan = $legend_colspan + 3;
			}
		}
		
		$legend_view->put_all(array(
			'LEGEND_COLSPAN'	=> $legend_colspan
		));
		
		return ($legend_colspan ? $legend_view : new StringTemplate(''));
	}
	
	private function build_types_form($current_page, $requested_type, $filters, $filters_ids)
	{
		if (in_array('type', $filters))
		{
			$key = array_search('type', $filters);
			unset($filters[$key]);
			unset($filters_ids[$key]);
		}
		
		$filter = implode('-', $filters);
		$filter_id = implode('-', $filters_ids);
		
		$form = new HTMLForm('type-form');
		$fieldset = new FormFieldsetHorizontal('filter-type');
		
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('filter_type', '', $requested_type, $this->build_select_types(),
			array('events' => array('change' => 'if (HTMLForms.getField("filter_type").getValue() > 0) {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'type/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'type/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel()) .'" + HTMLForms.getField("filter_type").getValue();
			} else {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel()) .'";
			}')
		)));
		
		return $form;
	}
	
	private function build_select_types()
	{
		$types = BugtrackerConfig::load()->get_types();
		
		$array_types = array();
		$array_types[] = new FormFieldSelectChoiceOption('', 0);
		foreach ($types as $key => $type)
		{
			$array_types[] = new FormFieldSelectChoiceOption(stripslashes($type), $key);
		}
		return $array_types;
	}
	
	private function build_categories_form($current_page, $requested_category, $filters, $filters_ids)
	{
		if (in_array('category', $filters))
		{
			$key = array_search('category', $filters);
			unset($filters[$key]);
			unset($filters_ids[$key]);
		}
		
		$filter = implode('-', $filters);
		$filter_id = implode('-', $filters_ids);
		
		$form = new HTMLForm('category-form');
		$fieldset = new FormFieldsetHorizontal('filter-category');
		
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('filter_category', '', $requested_category, $this->build_select_categories(),
			array('events' => array('change' => 'if (HTMLForms.getField("filter_category").getValue() > 0) {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'category/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'category/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel()) .'" + HTMLForms.getField("filter_category").getValue();
			} else {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel()) .'";
			}')
		)));
		
		return $form;
	}
	
	private function build_select_categories()
	{
		$categories = BugtrackerConfig::load()->get_categories();
		
		$array_categories = array();
		$array_categories[] = new FormFieldSelectChoiceOption('', 0);
		foreach ($categories as $key => $category)
		{
			$array_categories[] = new FormFieldSelectChoiceOption(stripslashes($category), $key);
		}
		return $array_categories;
	}
	
	private function build_severities_form($current_page, $requested_severity, $filters, $filters_ids)
	{
		if (in_array('severity', $filters))
		{
			$key = array_search('severity', $filters);
			unset($filters[$key]);
			unset($filters_ids[$key]);
		}
		
		$filter = implode('-', $filters);
		$filter_id = implode('-', $filters_ids);
		
		$form = new HTMLForm('severity-form');
		$fieldset = new FormFieldsetHorizontal('filter-severity');
		
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('filter_severity', '', $requested_severity, $this->build_select_severities(),
			array('events' => array('change' => 'if (HTMLForms.getField("filter_severity").getValue() > 0) {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'severity/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'severity/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel()) .'" + HTMLForms.getField("filter_severity").getValue();
			} else {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel()) .'";
			}')
		)));
		
		return $form;
	}
	
	private function build_select_severities()
	{
		$severities = BugtrackerConfig::load()->get_severities();
		
		$array_categories = array();
		$array_severities[] = new FormFieldSelectChoiceOption('', 0);
		foreach ($severities as $key => $severity)
		{
			$array_severities[] = new FormFieldSelectChoiceOption(stripslashes($severity['name']), $key);
		}
		return $array_severities;
	}
	
	private function build_status_form($current_page, $requested_status, $filters, $filters_ids, $lang)
	{
		if (in_array('status', $filters))
		{
			$key = array_search('status', $filters);
			unset($filters[$key]);
			unset($filters_ids[$key]);
		}
		
		$filter = implode('-', $filters);
		$filter_id = implode('-', $filters_ids);
		
		$form = new HTMLForm('status-form');
		$fieldset = new FormFieldsetHorizontal('filter-status');
		
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('filter_status', '', $requested_status, $this->build_select_status($current_page, $lang),
			array('events' => array('change' => 'if (HTMLForms.getField("filter_status").getValue()) {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'status/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'status/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel()) .'" + HTMLForms.getField("filter_status").getValue();
			} else {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel()) .'";
			}')
		)));
		
		return $form;
	}
	
	private function build_select_status($current_page, $lang)
	{
		$status_list = BugtrackerConfig::load()->get_status_list();
		
		$array_status = array();
		$array_status[] = new FormFieldSelectChoiceOption('', '');
		foreach ($status_list as $status => $progress)
		{
			if (($current_page == 'unsolved' && !in_array($status, array(Bug::FIXED, Bug::REJECTED))) || ($current_page == 'solved' && in_array($status, array(Bug::FIXED, Bug::REJECTED))))
				$array_status[] = new FormFieldSelectChoiceOption($lang['bugs.status.' . $status], $status);
		}
		return $array_status;
	}
	
	private function build_versions_form($current_page, $requested_version, $filters, $filters_ids)
	{
		$search_field = ($current_page == 'unsolved' ? 'detected_in' : 'fixed_in');
		
		if (in_array($search_field , $filters))
		{
			$key = array_search($search_field , $filters);
			unset($filters[$key]);
			unset($filters_ids[$key]);
		}
		
		$filter = implode('-', $filters);
		$filter_id = implode('-', $filters_ids);
		
		$form = new HTMLForm('version-form');
		$fieldset = new FormFieldsetHorizontal('filter-version');
		
		$form->add_fieldset($fieldset);
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('filter_version', '', $requested_version, $this->build_select_versions($current_page),
			array('events' => array('change' => 'if (HTMLForms.getField("filter_version").getValue() > 0) {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'detected_in/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter . '-' : '') . 'fixed_in/' . (!empty($filter_id) ? $filter_id . '-' : ''))->rel()) .'" + HTMLForms.getField("filter_version").getValue();
			} else {
				document.location = "'. ($current_page == 'unsolved' ? BugtrackerUrlBuilder::unsolved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel() : BugtrackerUrlBuilder::solved('name/desc/1/' . (!empty($filter) ? $filter : '') . '/' . (!empty($filter_id) ? $filter_id : ''))->rel()) .'";
			}')
		)));
		
		return $form;
	}
	
	private function build_select_versions($current_page)
	{
		$versions = ($current_page == 'unsolved' ? BugtrackerConfig::load()->get_versions_detected() : BugtrackerConfig::load()->get_versions());
		$versions = array_reverse($versions, true);
		
		$array_versions = array();
		$array_versions[] = new FormFieldSelectChoiceOption('', '');
		foreach ($versions as $key => $version)
		{
			$array_versions[] = new FormFieldSelectChoiceOption(stripslashes($version['name']), $key);
		}
		return $array_versions;
	}
}
?>
