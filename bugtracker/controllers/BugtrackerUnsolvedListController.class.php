<?php
/*##################################################
 *                      BugtrackerUnsolvedListController.class.php
 *                            -------------------
 *   begin                : November 12, 2012
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

class BugtrackerUnsolvedListController extends ModuleController
{
	private $lang;
	private $view;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		
		$this->check_authorizations();
		
		$this->build_view($request);
		
		return $this->build_response($this->view);
	}
	
	private function build_view($request)
	{
		//Configuration load
		$config = BugtrackerConfig::load();
		$severities = $config->get_severities();
		
		$field = $request->get_value('field', 'date');
		$sort = $request->get_value('sort', 'desc');
		$current_page = $request->get_getint('page', 1);
		$filter = $request->get_value('filter', '');
		$filter_id = $request->get_value('filter_id', '');
		
		if (!empty($filter) && empty($filter_id))
		{
			$filter = $filter_id = '';
		}
		
		$filters = !empty($filter) ? explode('-', $filter) : array();
		$filters_ids = !empty($filter_id) ? explode('-', $filter_id) : array();
		
		$mode = ($sort == 'top') ? 'ASC' : 'DESC';
		
		switch ($field)
		{
			case 'id' :
				$field_bdd = 'id';
				break;
			case 'title' :
				$field_bdd = 'title';
				break;
			case 'status' :
				$field_bdd = 'status';
				break;
			default :
				$field_bdd = 'submit_date';
				break;
		}
		
		$select_filters = '';
		foreach ($filters as $key => $f)
		{
			$select_filters .= in_array($f, array('type', 'category', 'severity', 'status', 'detected_in')) && $filters_ids[$key] ? "AND " . $f . " = '" . $filters_ids[$key] . "'" : '';
		}
		
		$stats_cache = BugtrackerStatsCache::load();
		$bugs_number = BugtrackerService::count("WHERE status <> '" . Bug::FIXED . "' AND status <> '" . Bug::REJECTED . "'" . $select_filters);
		
		$pagination = $this->get_pagination($bugs_number, $current_page, $field, $sort, $filter, $filter_id);
		
		$result = PersistenceContext::get_querier()->select("SELECT b.*, member.*
		FROM " . BugtrackerSetup::$bugtracker_table . " b
		LEFT JOIN " . DB_TABLE_MEMBER . " member ON member.user_id = b.author_id AND member.user_aprob = 1
		WHERE status <> '" . Bug::FIXED . "' AND status <> '" . Bug::REJECTED . "'" .
		$select_filters . "
		ORDER BY " . $field_bdd . " " . $mode . "
		LIMIT :number_items_per_page OFFSET :display_from",
			array(
				'number_items_per_page' => $pagination->get_number_items_per_page(),
				'display_from' => $pagination->get_display_from()
			)
		);
		
		$displayed_severities = array();
		
		while ($row = $result->fetch())
		{
			$bug = new Bug();
			$bug->set_properties($row);
			
			if (!in_array($bug->get_severity(), $displayed_severities)) $displayed_severities[] = $bug->get_severity();
			
			$this->view->assign_block_vars('bug', array_merge($bug->get_array_tpl_vars(), array(
				'C_LINE_COLOR'		=> $bug->get_severity() && isset($severities[$bug->get_severity()]),
				'LINE_COLOR' 		=> stripslashes($severities[$bug->get_severity()]['color']),
				'U_CHANGE_STATUS'	=> BugtrackerUrlBuilder::change_status($bug->get_id())->rel(),
				'U_EDIT'			=> BugtrackerUrlBuilder::edit($bug->get_id() . '/unsolved/' . $current_page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel(),
				'U_DELETE'			=> BugtrackerUrlBuilder::delete($bug->get_id(), 'unsolved', $current_page, (!empty($filter) ? $filter : ''), (!empty($filter) ? $filter_id : ''))->rel(),
			)));
		}
		$result->dispose();
		
		$bugs_colspan = BugtrackerAuthorizationsService::check_authorizations()->moderation() ? 5 : 4;
		if ($config->is_type_column_displayed()) $bugs_colspan++;
		if ($config->is_category_column_displayed()) $bugs_colspan++;
		if ($config->is_priority_column_displayed()) $bugs_colspan++;
		if ($config->is_detected_in_column_displayed()) $bugs_colspan++;
		
		$this->view->put_all(array(
			'C_IS_ADMIN'					=> BugtrackerAuthorizationsService::check_authorizations()->moderation(),
			'C_UNSOLVED' 					=> true,
			'C_BUGS' 						=> $result->get_rows_count() > 0,
			'C_DISPLAY_AUTHOR'				=> true,
			'C_DISPLAY_TYPE_COLUMN'			=> $config->is_type_column_displayed(),
			'C_DISPLAY_CATEGORY_COLUMN'		=> $config->is_category_column_displayed(),
			'C_DISPLAY_PRIORITY_COLUMN'		=> $config->is_priority_column_displayed(),
			'C_DISPLAY_DETECTED_IN_COLUMN'	=> $config->is_detected_in_column_displayed(),
			'C_PAGINATION'					=> $pagination->has_several_pages(),
			'PAGINATION' 					=> $pagination->display(),
			'BUGS_COLSPAN' 					=> $bugs_colspan,
			'L_NO_BUG' 						=> empty($filters) ? $this->lang['notice.no_bug'] : (count($filters) > 1 ? $this->lang['notice.no_bug_matching_filters'] : $this->lang['notice.no_bug_matching_filter']),
			'FILTER_LIST'					=> BugtrackerViews::build_filters('unsolved', $bugs_number),
			'LEGEND'						=> BugtrackerViews::build_legend($displayed_severities, 'unsolved'),
			'LINK_BUG_ID_TOP' 				=> BugtrackerUrlBuilder::unsolved('id/top/'. $current_page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel(),
			'LINK_BUG_ID_BOTTOM' 			=> BugtrackerUrlBuilder::unsolved('id/bottom/'. $current_page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel(),
			'LINK_BUG_TITLE_TOP' 			=> BugtrackerUrlBuilder::unsolved('title/top/'. $current_page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel(),
			'LINK_BUG_TITLE_BOTTOM' 		=> BugtrackerUrlBuilder::unsolved('title/bottom/'. $current_page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel(),
			'LINK_BUG_STATUS_TOP'			=> BugtrackerUrlBuilder::unsolved('status/top/'. $current_page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel(),
			'LINK_BUG_STATUS_BOTTOM'		=> BugtrackerUrlBuilder::unsolved('status/bottom/'. $current_page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel(),
			'LINK_BUG_DATE_TOP' 			=> BugtrackerUrlBuilder::unsolved('date/top/'. $current_page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel(),
			'LINK_BUG_DATE_BOTTOM' 			=> BugtrackerUrlBuilder::unsolved('date/bottom/'. $current_page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : ''))->rel()
		));
		
		return $this->view;
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'bugtracker');
		$this->view = new FileTemplate('bugtracker/BugtrackerListController.tpl');
		$this->view->add_lang($this->lang);
	}
	
	private function check_authorizations()
	{
		if (!BugtrackerAuthorizationsService::check_authorizations()->read())
		{
			$error_controller = PHPBoostErrors::user_not_authorized();
			DispatchManager::redirect($error_controller);
		}
	}
	
	private function get_pagination($bugs_number, $page, $field, $sort, $filter, $filter_id)
	{
		$pagination = new ModulePagination($page, $bugs_number, (int)BugtrackerConfig::load()->get_items_per_page());
		$pagination->set_url(BugtrackerUrlBuilder::unsolved($field . '/' . $sort . '/%d' . (!empty($filter) ? '/' . $filter . '/' . $filter_id : '')));
		
		if ($pagination->current_page_is_empty() && $page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
		
		return $pagination;
	}
	
	public static function get_view()
	{
		$object = new self();
		$object->init();
		$object->check_authorizations();
		$object->build_view(AppContext::get_request());
		return BugtrackerViews::build_body_view($object->view, 'unsolved');
	}
	
	private function build_response(View $view)
	{
		$request = AppContext::get_request();
		$success = $request->get_value('success', '');
		$bug_id = $request->get_int('id', 0);
		
		$field = $request->get_value('field', 'date');
		$sort = $request->get_value('sort', 'desc');
		$page = $request->get_int('page', 1);
		$filter = $request->get_value('filter', '');
		$filter_id = $request->get_value('filter_id', '');
		
		$body_view = BugtrackerViews::build_body_view($view, 'unsolved');
		
		//Success messages
		switch ($success)
		{
			case 'add':
				$errstr = StringVars::replace_vars($this->lang['success.add'], array('id' => $bug_id));
				break;
			case 'edit':
				$errstr = StringVars::replace_vars($this->lang['success.edit'], array('id' => $bug_id));
				break;
			case 'delete':
				$errstr = StringVars::replace_vars($this->lang['success.delete'], array('id' => $bug_id));
				break;
			default:
				$errstr = '';
		}
		if (!empty($errstr))
			$body_view->put('MSG', MessageHelper::display($errstr, E_USER_SUCCESS, 5));
		
		$response = new SiteDisplayResponse($body_view);
		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['titles.unsolved']);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(BugtrackerUrlBuilder::unsolved($field . '/' . $sort . '/' . $page));
		
		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['module_title'], BugtrackerUrlBuilder::home());
		$breadcrumb->add($this->lang['titles.unsolved'], BugtrackerUrlBuilder::unsolved($field . '/' . $sort . '/' . $page . (!empty($filter) ? '/' . $filter . '/' . $filter_id : '')));
		
		return $response;
	}
}
?>
