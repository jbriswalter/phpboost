<?php
/*##################################################
 *                      AdminDownloadManageController.class.php
 *                            -------------------
 *   begin                : August 24, 2014
 *   copyright            : (C) 2014 Julien BRISWALTER
 *   email                : julienseth78@phpboost.com
 *
 *
 ###################################################
 *
 * This program is a free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

 /**
 * @author Julien BRISWALTER <julienseth78@phpboost.com>
 */

class AdminDownloadManageController extends AdminModuleController
{
	const NUMBER_ITEMS_PER_PAGE = 20;
	
	private $lang;
	private $view;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		
		$this->build_view($request);
		
		return new AdminDownloadDisplayResponse($this->view, $this->lang['download.management']);
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'download');
		$this->view = new FileTemplate('download/AdminDownloadManageController.tpl');
		$this->view->add_lang($this->lang);
	}
	
	private function build_view(HTTPRequestCustom $request)
	{
		$categories = DownloadService::get_categories_manager()->get_categories_cache()->get_categories();
		
		$mode = $request->get_getvalue('sort', 'desc');
		$field = $request->get_getvalue('field', 'date');
		
		$sort_mode = ($mode == 'asc') ? 'ASC' : 'DESC';
		
		switch ($field)
		{
			case 'category':
				$sort_field = 'id_category';
				break;
			case 'author':
				$sort_field = 'display_name';
				break;
			case 'name':
				$sort_field = 'name';
				break;
			default:
				$sort_field = 'creation_date';
				break;
		}
		
		$page = $request->get_getint('page', 1);
		$pagination = $this->get_pagination($field, $mode, $page);
		
		$result = PersistenceContext::get_querier()->select('SELECT download.*, member.*, com.number_comments, notes.average_notes, notes.number_notes, note.note
		FROM '. DownloadSetup::$download_table .' download
		LEFT JOIN '. DB_TABLE_MEMBER .' member ON member.user_id = download.author_user_id
		LEFT JOIN ' . DB_TABLE_COMMENTS_TOPIC . ' com ON com.id_in_module = download.id AND com.module_id = \'download\'
		LEFT JOIN ' . DB_TABLE_AVERAGE_NOTES . ' notes ON notes.id_in_module = download.id AND notes.module_name = \'download\'
		LEFT JOIN ' . DB_TABLE_NOTE . ' note ON note.id_in_module = download.id AND note.module_name = \'download\' AND note.user_id = :user_id
		ORDER BY ' . $sort_field . ' ' . $sort_mode . '
		LIMIT :number_per_page OFFSET :start_limit',
			array(
				'user_id' => AppContext::get_current_user()->get_id(),
				'number_per_page' => $pagination->get_number_items_per_page(),
				'start_limit' => $pagination->get_display_from()
		));
		
		while($row = $result->fetch())
		{
			$downloadfile = new DownloadFile();
			$downloadfile->set_properties($row);
			
			$this->view->assign_block_vars('downloadfiles', $downloadfile->get_array_tpl_vars(DownloadUrlBuilder::manage($field, $mode, $page)->relative()));
		}
		$result->dispose();
		
		$this->view->put_all(array(
			'C_PAGINATION' => $pagination->has_several_pages(),
			'C_FILES' => !$pagination->current_page_is_empty(),
			'PAGINATION' => $pagination->display(),
			'U_SORT_NAME_ASC' => DownloadUrlBuilder::manage('name', 'asc', $page)->rel(),
			'U_SORT_NAME_DESC' => DownloadUrlBuilder::manage('name', 'desc', $page)->rel(),
			'U_SORT_CATEGORY_ASC' => DownloadUrlBuilder::manage('category', 'asc', $page)->rel(),
			'U_SORT_CATEGORY_DESC' => DownloadUrlBuilder::manage('category', 'desc', $page)->rel(),
			'U_SORT_AUTHOR_ASC' => DownloadUrlBuilder::manage('author', 'asc', $page)->rel(),
			'U_SORT_AUTHOR_DESC' => DownloadUrlBuilder::manage('author', 'desc', $page)->rel(),
			'U_SORT_DATE_ASC' => DownloadUrlBuilder::manage('date', 'asc', $page)->rel(),
			'U_SORT_DATE_DESC' => DownloadUrlBuilder::manage('date', 'desc', $page)->rel()
		));
	}
	
	private function get_pagination($sort_field, $sort_mode, $page)
	{
		$downloadfiles_number = DownloadService::count();
		
		$pagination = new ModulePagination($page, $downloadfiles_number, self::NUMBER_ITEMS_PER_PAGE);
		$pagination->set_url(DownloadUrlBuilder::manage($sort_field, $sort_mode, '%d'));
		
		if ($pagination->current_page_is_empty() && $page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
		
		return $pagination;
	}
}
?>
