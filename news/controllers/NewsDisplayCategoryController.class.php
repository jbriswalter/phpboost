<?php
/*##################################################
 *		               NewsDisplayCategoryController.class.php
 *                            -------------------
 *   begin                : February 20, 2013
 *   copyright            : (C) 2013 Kevin MASSY
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

/**
 * @author Kevin MASSY <kevin.massy@phpboost.com>
 */
class NewsDisplayCategoryController extends ModuleController
{	
	private $lang;
	private $tpl;
	
	private $category;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();
		
		$this->init();
		
		$this->build_view();
					
		return $this->generate_response();
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'news');
		$this->tpl = new FileTemplate('news/NewsDisplaySeveralNewsController.tpl');
	}
		
	private function build_view()
	{	
		$number_columns_display_news = NewsConfig::load()->get_number_columns_display_news();
		
		$now = new Date(DATE_NOW, TIMEZONE_AUTO);
		$result = PersistenceContext::get_querier()->select('SELECT news.*, member.*, com.number_comments
		FROM '. NewsSetup::$news_table .' news
		LEFT JOIN '. DB_TABLE_MEMBER .' member ON member.user_id = news.author_user_id
		LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = news.id AND com.module_id = \'news\'
		WHERE (news.approbation_type = 1 OR (news.approbation_type = 2 AND news.start_date < :timestamp_now AND (news.end_date > :timestamp_now) OR news.end_date = 0)) AND news.id_category IN :authorized_categories
		ORDER BY top_list_enabled DESC, news.creation_date DESC', array(
			'timestamp_now' => $now->get_timestamp(),
			'authorized_categories' => $this->get_authorized_categories()));

		$i = 0;
		while ($row = $result->fetch())
		{
			if ($number_columns_display_news > 1)
			{
				$new_row = (($i % $number_columns_display_news) == 0 && $i > 0);
				$i++;
			}
			else
				$new_row = false;
				
			$news = new News();
			$news->set_properties($row);
			$category = NewsService::get_categories_manager()->get_categories_cache()->get_category($news->get_id_cat());
			
			$this->tpl->assign_block_vars('news', array(
				'C_NEWS_ROW' => $new_row,
				'C_EDIT' =>  NewsAuthorizationsService::check_authorizations($row['id_category'])->moderation() || NewsAuthorizationsService::check_authorizations($row['id_category'])->write() && $news->get_author_user_id() == AppContext::get_current_user()->get_id(),
				'C_DELETE' =>  NewsAuthorizationsService::check_authorizations($row['id_category'])->moderation(),
				'C_IMG' => $news->has_picture(),
			
				'ID' => $news->get_id(),
				'COM' => CommentsService::get_number_and_lang_comments('news', $row['id']),
				'NUMBER_COM' => !empty($row['number_comments']) ? $row['number_comments'] : 0,
				'NAME' => $news->get_name(),
				'CONTENTS' => FormatingHelper::second_parse($news->get_contents()),
				'IMG' => FormatingHelper::second_parse_url($news->get_picture()->rel()),
			
				'PSEUDO' => $news->get_author_user()->get_pseudo(),
				'DATE' => $news->get_creation_date()->format(DATE_FORMAT_SHORT, TIMEZONE_AUTO),
				
				'U_LINK' => NewsUrlBuilder::display_news($category->get_id(), $category->get_rewrited_name(), $news->get_id(), $news->get_rewrited_name())->rel(),
				'U_EDIT' => NewsUrlBuilder::edit_news($news->get_id())->rel(),
				'U_DELETE' => NewsUrlBuilder::delete_news($news->get_id())->rel(),
				'U_AUTHOR_PROFILE' => UserUrlBuilder::profile($news->get_author_user_id())->absolute(),
				'U_SYNDICATION' => SyndicationUrlBuilder::rss('news', $news->get_id_cat())->rel(),
			));
		}
		
		$this->tpl->put_all(array(
			'C_ADD' => NewsAuthorizationsService::check_authorizations($this->get_category()->get_id())->write(),
		
			'L_ADD' => $this->lang['news.add'],
		
			'U_ADD' => NewsUrlBuilder::add_news()->rel(),
		));
		
		if ($number_columns_display_news > 1)
		{
			$column_width = floor(100 / $number_columns_display_news);
			$this->tpl->put_all(array(
				'C_NEWS_BLOCK_COLUMN' => true,
				'COLUMN_WIDTH' => $column_width
			));
		}
	}
	
	private function get_authorized_categories()
	{
		$category = $this->get_category();
		$authorized_categories = array();
		if ($category->get_id() !== Category::ROOT_CATEGORY)
		{
			$authorized_categories[] = $category->get_id();
		}
		else
		{
			$search_category_children_options = new SearchCategoryChildrensOptions();
			$search_category_children_options->add_authorizations_bits(Category::READ_AUTHORIZATIONS);
			$categories = NewsService::get_categories_manager()->get_childrens($category->get_id(), $search_category_children_options);
			$authorized_categories = array_keys($categories);
		}
		return $authorized_categories;
	}
	
	private function get_category()
	{
		if ($this->category === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$row = PersistenceContext::get_querier()->select_single_row(NewsSetup::$news_cats_table, array('*'), 'WHERE id=:id', array('id' => $id));

					$category = new RichCategory();
					$category->set_properties($row);
					$this->category = $category;
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
   					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->category = NewsService::get_categories_manager()->get_categories_cache()->get_category(Category::ROOT_CATEGORY);
			}
		}
		return $this->category;
	}
	
	private function check_authorizations()
	{
		$id_cat = $this->get_category()->get_id();
		if ($id_cat !== Category::ROOT_CATEGORY)
		{
			if (!NewsAuthorizationsService::check_authorizations($id_cat)->read())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
		   		DispatchManager::redirect($error_controller);
			}
		}
	}
	
	private function generate_response()
	{
		$response = new NewsDisplayResponse();
		$response->set_page_title($this->get_category()->get_name());
		
		$response->add_breadcrumb_link($this->lang['news'], NewsUrlBuilder::home());
		
		$categories = array_reverse(NewsService::get_categories_manager()->get_parents($this->get_category()->get_id(), true));
		foreach ($categories as $id => $category)
		{
			if ($id != Category::ROOT_CATEGORY)
				$response->add_breadcrumb_link($category->get_name(), NewsUrlBuilder::display_category($id, $category->get_rewrited_name()));
		}
	
		return $response->display($this->tpl);
	}
}
?>