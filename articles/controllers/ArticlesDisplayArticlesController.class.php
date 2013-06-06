<?php
/*##################################################
 *		       ArticlesDisplayArticlesController.class.php
 *                            -------------------
 *   begin                : April 03, 2013
 *   copyright            : (C) 2013 Patrick DUBEAU
 *   email                : daaxwizeman@gmail.com
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

class ArticlesDisplayArticlesController extends ModuleController
{	
	private $lang;
	private $form;
	private $view;
	private $auth_moderation;
	private $auth_write;
	private $article;
	private $category;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();
		
		$this->init();
		
		$this->check_pending_article();
		
		$this->build_view($request);
					
		return $this->generate_response();
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('articles-common', 'articles');
		$this->view = new FileTemplate('articles/ArticlesDisplayArticlesController.tpl');
		$this->view->add_lang($this->lang);
	}
	
	private function get_article()
	{
		if ($this->article === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try 
				{
					$this->article = ArticlesService::get_article('WHERE id=:id', array('id' => $id));
				} 
				catch (RowNotFoundException $e) 
				{
					$error_controller = PHPBoostErrors::unexisting_page();
   					DispatchManager::redirect($error_controller);
				}
			}
			else
			    $this->article = new Articles();
		}
		return $this->article;
	}
	
	private function check_pending_article()
	{
	    if (!$this->article->is_published())
	    {
		    $this->view->put('MSG', MessageHelper::display($this->lang['articles.not_published'], MessageHelper::WARNING));
	    }
	    else
	    {
		    $this->update_number_view();
	    }
	}
	private function build_view($request)
	{
		$current_page = $request->get_getint('page', 1);
		$comments_enabled = ArticlesConfig::load()->get_comments_enabled();
		
		$this->category = ArticlesService::get_categories_manager()->get_categories_cache()->get_category($this->article->get_id_category());
		
		$article_contents = $this->article->get_contents();
		
		//If article doesn't begin with a page, we insert one
		if (substr(trim($article_contents), 0, 6) != '[page]')
		{
			$article_contents = '[page]&nbsp;[/page]' . $article_contents;
		}
		else
		{
			$article_contents = ' ' . $article_contents;
		}
		
		//Removing [page] bbcode
		$article_contents_clean = preg_split('`\[page\].+\[/page\](.*)`Us', $article_contents, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		
		//Retrieving pages 
		preg_match_all('`\[page\]([^[]+)\[/page\]`U', $article_contents, $array_page);
		
		$nbr_pages = count($array_page[1]);
		
		// @todo : option de rendre les pages sous forme de lien (comme wiki, dans div flottant � droite)
		//$pages_link_list = '<ul class="">';
		$this->build_form($array_page, $current_page);
		
		$this->build_view_sources();
		
		$this->build_view_keywords($this->article->get_id());
		
		$pagination = new ArticlesPagination($current_page, $nbr_pages, 1);
		$pagination->set_url(ArticlesUrlBuilder::display_article($this->category->get_id(), $this->category->get_rewrited_name(), $this->article->get_id(), $this->article->get_rewrited_title())->absolute() . '%d');
		
		$notation = new Notation();
		$notation->set_module_name('articles');
		$notation->set_notation_scale(ArticlesConfig::load()->get_notation_scale());
		$notation->set_id_in_module($this->article->get_id());
		
		$user = $this->article->get_author_user();
		$user_group_color = User::get_group_color($user->get_groups(), $user->get_level(), true);
		
		$this->view->put_all(array(
			'C_EDIT' => $this->auth_moderation || $this->auth_write && $this->article->get_author_user()->get_id() == AppContext::get_current_user()->get_id(),
			'C_DELETE' => $this->auth_moderation,
			'C_USER_GROUP_COLOR' => !empty($user_group_color),
			'C_COMMENTS_ENABLED' => $comments_enabled,
			'C_AUTHOR_DISPLAYED' => $this->article->get_author_name_displayed(),
			'C_NOTATION_ENABLED' => $this->article->get_notation_enabled(),
			'TITLE' => $this->article->get_title(),
			'PICTURE' => $this->article->get_picture(),
			'DATE' => $this->article->get_date_created()->format(DATE_FORMAT_SHORT, TIMEZONE_AUTO),
			'L_COMMENTS' => CommentsService::get_number_and_lang_comments('articles', $this->article->get_id()),
			'L_PREVIOUS_PAGE' => LangLoader::get_message('previous_page', 'main'),
			'L_NEXT_PAGE' => LangLoader::get_message('next_page', 'main'),
			'L_DATE' => LangLoader::get_message('date', 'main'),
			'L_VIEW' => LangLoader::get_message('views', 'main'),
			'L_ON' => $this->lang['articles.written.on'],
			'L_WRITTEN' => $this->lang['articles.written.by'],
			'L_NO_AUTHOR_DISPLAYED' => $this->lang['articles.no_author_diplsayed'],
			'L_ALERT_DELETE_ARTICLE' => $this->lang['articles.form.alert_delete_article'],
			'L_SOURCE' => $this->lang['articles.sources'],
			'L_SUMMARY' => $this->lang['articles.summary'],
			'L_PRINTABLE_VERSION' => LangLoader::get_message('printable_version', 'main'),
			'KERNEL_NOTATION' => NotationService::display_active_image($notation),
			'CONTENTS' => isset($article_contents_clean[$current_page-1]) ? FormatingHelper::second_parse($article_contents_clean[$current_page-1]) : '',
			'PSEUDO' => $user->get_pseudo(),
			'USER_LEVEL_CLASS' => UserService::get_level_class($user->get_level()),
			'USER_GROUP_COLOR' => $user_group_color,
			'PAGINATION_ARTICLES' => ($nbr_pages > 1) ? $pagination->display()->render() : '',
			'PAGE_NAME' => (isset($array_page[1][$current_page-1]) && $array_page[1][$current_page-1] != '&nbsp;') ? $array_page[1][($current_page-1)] : '',
			'U_PAGE_PREVIOUS_ARTICLES' => ($current_page > 1 && $current_page <= $nbr_pages && $nbr_pages > 1) ? ArticlesUrlBuilder::display_article($this->category->get_id(), $this->category->get_rewrited_name(), $this->article->get_id(), $this->article->get_rewrited_title())->absolute() . ($current_page - 1) : '',
			'L_PREVIOUS_TITLE' => ($current_page > 1 && $current_page <= $nbr_pages && $nbr_pages > 1) ? $array_page[1][$current_page-2] : '',
			'U_PAGE_NEXT_ARTICLES' => ($current_page > 0 && $current_page < $nbr_pages && $nbr_pages > 1) ? ArticlesUrlBuilder::display_article($this->category->get_id(), $this->category->get_rewrited_name(), $this->article->get_id(), $this->article->get_rewrited_title())->absolute() . ($current_page + 1) : '',
			'L_NEXT_TITLE' => ($current_page > 0 && $current_page < $nbr_pages && $nbr_pages > 1) ? $array_page[1][$current_page] : '', 
			'U_COMMENTS' => ArticlesUrlBuilder::display_comments_article($this->category->get_id(), $this->category->get_rewrited_name(), $this->article->get_id(), $this->article->get_rewrited_title())->absolute(),
			'U_AUTHOR' => UserUrlBuilder::profile($this->article->get_author_user()->get_id())->absolute(),
			'U_EDIT_ARTICLE' => ArticlesUrlBuilder::edit_article($this->article->get_id())->absolute(),
			'U_DELETE_ARTICLE' => ArticlesUrlBuilder::delete_article($this->article->get_id())->absolute(),
			'U_PRINT_ARTICLE' => ArticlesUrlBuilder::print_article($this->article->get_id(), $this->article->get_rewrited_title())->absolute(),
			'U_SYNDICATION' => ArticlesUrlBuilder::category_syndication($this->article->get_id_category())->rel()
		));
		
		//Affichage commentaires
		if ($comments_enabled)
		{
		    $comments_topic = new ArticlesCommentsTopic();
		    $comments_topic->set_id_in_module($this->article->get_id());
		    $comments_topic->set_url(ArticlesUrlBuilder::display_comments_article($this->category->get_id(), $this->category->get_rewrited_name(), $this->article->get_id(), $this->article->get_rewrited_title()));


		    $this->view->put('COMMENTS', $comments_topic->display());
		}
		
		$this->view->put('FORM', $this->form->display());
	}
	
	private function build_form($array_page, $current_page)
	{
		$form = new HTMLForm(__CLASS__);
		
		$fieldset = new FormFieldsetHorizontal('pages');
		$form->add_fieldset($fieldset);
		
		$article_pages = $this->list_article_pages($array_page);
		
		$fieldset->add_field(new FormFieldSimpleSelectChoice('article_pages', '', $current_page, $article_pages,
			array('events' => array('change' => 'document.location = "'. ArticlesUrlBuilder::display_article($this->category->get_id(), $this->category->get_rewrited_name(), $this->article->get_id(), $this->article->get_rewrited_title())->absolute() .'" + HTMLForms.getField("article_pages").getValue();'))
		));
		
		$this->form = $form;
	}
	
	private function list_article_pages($array_page)
	{
		$options = array();
		
		$options[] = new FormFieldSelectChoiceOption($this->lang['articles.select_page'], '1');
		
		$i = 1;
		foreach ($array_page[1] as $page_name)
		{
			$options[] = new FormFieldSelectChoiceOption($page_name, $i++);
		}
		
		return $options;
	}
	
	private function build_view_sources()
	{
	    $sources = $this->article->get_sources();
	    
	    $this->view->put('C_SOURCES', !empty($sources));
	    
	    $i = 0;
	    foreach ($sources as $name => $url)
	    {	
		    $this->view->assign_block_vars('sources', array(
			    'I' => $i,
			    'NAME' => $name,
			    'URL' => $url,
			    'COMMA' => $i > 0 ? ', ' : ' '
		    ));
		    $i++;
	    }	
	}
	
	private function build_view_keywords($id_article)
	{
		$keywords = ArticlesKeywordsService::get_article_keywords($id_article);
		
		$this->view->put('C_KEYWORDS', !empty($keywords));
		
		$i = 0;
		while ($row = $keywords->fetch())
		{	
			$this->view->assign_block_vars('keywords', array(
				'I' => $i,
				'NAME' => $row['name'],
				'U_KEYWORD' => ArticlesUrlBuilder::display_tag($row['rewrited_name']),
				'COMMA' => $i > 0 ? ', ' : ' '
			));
			$i++;
		}	
	}
	
	private function check_authorizations()
	{
		$article = $this->get_article();
		
		$this->auth_write = ArticlesAuthorizationsService::check_authorizations($article->get_id_category())->write();
		$this->auth_moderation = ArticlesAuthorizationsService::check_authorizations($article->get_id_category())->moderation();
		
		$no_reading_authorizations = !ArticlesAuthorizationsService::check_authorizations($article->get_id_category())->read() && !ArticlesAuthorizationsService::check_authorizations()->read();
		$no_reading_authorizations_no_approval = $no_reading_authorizations && !$this->auth_moderation && (!$this->auth_write && $article->get_author_user()->get_id() != AppContext::get_current_user()->get_id());
		
		switch ($article->get_publishing_state()) 
		{
			case Articles::PUBLISHED_NOW:
				if ($no_reading_authorizations)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Articles::NOT_PUBLISHED:
				if ($no_reading_authorizations_no_approval)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			case Articles::PUBLISHED_DATE:
				if (!$article->is_published() && $no_reading_authorizations_no_approval)
				{
					$error_controller = PHPBoostErrors::user_not_authorized();
		   			DispatchManager::redirect($error_controller);
				}
			break;
			default:
				$error_controller = PHPBoostErrors::unexisting_page();
   				DispatchManager::redirect($error_controller);
			break;
		}
	}
	
	private function update_number_view()
	{
		PersistenceContext::get_querier()->inject('UPDATE ' . LOW_PRIORITY . ' ' . ArticlesSetup::$articles_table . ' SET number_view = number_view + 1 WHERE id=:id', 
			array(
				'id' => $this->article->get_id()
			)
		);
	}
	
	private function generate_response()
	{
		$response = new ArticlesDisplayResponse();
		$response->set_page_title($this->article->get_title());
		$response->add_breadcrumb_link($this->lang['articles'], ArticlesUrlBuilder::home());
		
		$categories = array_reverse(ArticlesService::get_categories_manager()->get_parents($this->article->get_id_category(), true));
		foreach ($categories as $id => $category)
		{
			if ($id != Category::ROOT_CATEGORY)
				$response->add_breadcrumb_link($category->get_name(), ArticlesUrlBuilder::display_category($id, $category->get_rewrited_name()));
		}
		$category = $categories[$this->article->get_id_category()];
		if (!$this->article->is_published())
			$response->add_breadcrumb_link ($this->lang['articles.pending_articles'], ArticlesUrlBuilder::display_pending_articles()->absolute());
		$response->add_breadcrumb_link($this->article->get_title(), ArticlesUrlBuilder::display_article($category->get_id(), $category->get_rewrited_name(), $this->article->get_id(), $this->article->get_rewrited_title()));
		
		return $response->display($this->view);
	}
}
?>