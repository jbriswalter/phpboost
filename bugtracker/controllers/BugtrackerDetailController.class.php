<?php
/*##################################################
 *                      BugtrackerDetailController.class.php
 *                            -------------------
 *   begin                : November 11, 2012
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

class BugtrackerDetailController extends ModuleController
{
	private $lang;
	private $view;
	private $bug;
	private $current_user;
	
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
		$types = $config->get_types();
		$categories = $config->get_categories();
		$severities = $config->get_severities();
		$priorities = $config->get_priorities();
		$versions = $config->get_versions_detected();
		
		switch ($this->bug->get_status())
		{
			case Bug::NEW_BUG :
			case Bug::ASSIGNED :
			case Bug::REOPEN :
				$c_reopen = false;
				$c_reject = true;
				break;
			case Bug::FIXED :
			case Bug::REJECTED :
				$c_reopen = true;
				$c_reject = false;
				break;
			default :
				$c_reopen = false;
				$c_reject = true;
		}
		
		if ($this->current_user->get_id() == $this->bug->get_author_user()->get_id() && $this->bug->get_author_user()->get_id() != User::VISITOR_LEVEL)
		{
			$this->view->put_all(array(
				'C_EDIT_BUG' => true
			));
		}
		
		if ($this->current_user->get_id() == $this->bug->get_assigned_to_id())
		{
			$this->view->put_all(array(
				'C_FIX_BUG'		=> !$c_reopen,
				'C_ASSIGN_BUG'	=> !$c_reopen,
				'C_REOPEN_BUG'	=> $c_reopen,
				'C_REJECT_BUG'	=> $c_reject,
				'C_EDIT_BUG'	=> true
			));
		}
		
		if (BugtrackerAuthorizationsService::check_authorizations()->moderation())
		{
			$this->view->put_all(array(
				'C_FIX_BUG'		=> !$c_reopen,
				'C_ASSIGN_BUG'	=> !$c_reopen,
				'C_REOPEN_BUG'	=> $c_reopen,
				'C_REJECT_BUG'	=> $c_reject,
				'C_EDIT_BUG'	=> true,
				'C_DELETE_BUG'	=> true
			));
		}
		
		$user_assigned = $this->bug->get_assigned_to_id() ? UserService::get_user('WHERE user_aprob = 1 AND user_id=:user_id', array('user_id' => $this->bug->get_assigned_to_id())) : '';
		$user_assigned_group_color = $this->bug->get_assigned_to_id() ? User::get_group_color($user_assigned->get_groups(), $user_assigned->get_level(), true) : '';
		
		$this->view->put_all($this->bug->get_array_tpl_vars());
		
		$this->view->put_all(array(
			'C_TYPES' 						=> $types,
			'C_CATEGORIES' 					=> $categories,
			'C_SEVERITIES' 					=> $severities,
			'C_PRIORITIES' 					=> $priorities,
			'C_VERSIONS' 					=> $versions,
			'C_USER_ASSIGNED_GROUP_COLOR'	=> !empty($user_assigned_group_color),
			'C_IS_DATE_FORM_SHORT'			=> $config->is_date_form_short(),
			'TYPE'							=> (isset($types[$this->bug->get_type()])) ? stripslashes($types[$this->bug->get_type()]) : $this->lang['bugs.notice.none'],
			'CATEGORY'						=> (isset($categories[$this->bug->get_category()])) ? stripslashes($categories[$this->bug->get_category()]) : $this->lang['bugs.notice.none_e'],
			'SEVERITY'						=> (isset($severities[$this->bug->get_severity()])) ? stripslashes($severities[$this->bug->get_severity()]['name']) : $this->lang['bugs.notice.none'],
			'PRIORITY'						=> (isset($priorities[$this->bug->get_priority()])) ? stripslashes($priorities[$this->bug->get_priority()]) : $this->lang['bugs.notice.none_e'],
			'DETECTED_IN' 					=> (isset($versions[$this->bug->get_detected_in()])) ? stripslashes($versions[$this->bug->get_detected_in()]['name']) : $this->lang['bugs.notice.not_defined'],
			'FIXED_IN'						=> (isset($versions[$this->bug->get_fixed_in()])) ? stripslashes($versions[$this->bug->get_fixed_in()]['name']) : $this->lang['bugs.notice.not_defined'],
			'USER_ASSIGNED'					=> $user_assigned,
			'USER_ASSIGNED'					=> $user_assigned ? $user_assigned->get_pseudo() : '',
			'USER_ASSIGNED_LEVEL_CLASS'		=> $user_assigned ? UserService::get_level_class($user_assigned->get_level()) : '',
			'USER_ASSIGNED_GROUP_COLOR'		=> $user_assigned_group_color,
			'U_FIX'							=> BugtrackerUrlBuilder::fix($this->bug->get_id(), 'detail')->rel(),
			'U_ASSIGN'						=> BugtrackerUrlBuilder::assign($this->bug->get_id(), 'detail')->rel(),
			'U_REJECT'						=> BugtrackerUrlBuilder::reject($this->bug->get_id(), 'detail')->rel(),
			'U_REOPEN'						=> BugtrackerUrlBuilder::reopen($this->bug->get_id(), 'detail')->rel(),
			'U_EDIT'						=> BugtrackerUrlBuilder::edit($this->bug->get_id() . '/detail')->rel(),
			'U_DELETE'						=> BugtrackerUrlBuilder::delete($this->bug->get_id(), 'unsolved')->rel(),
		));
		
		$comments_topic = new BugtrackerCommentsTopic();
		$comments_topic->set_id_in_module($this->bug->get_id());
		$comments_topic->set_url(BugtrackerUrlBuilder::detail($this->bug->get_id()));
		$this->view->put('COMMENTS', $comments_topic->display());
	}
	
	private function init()
	{
		$this->current_user = AppContext::get_current_user();
		$request = AppContext::get_request();
		$id = $request->get_int('id', 0);
		
		$this->lang = LangLoader::get('common', 'bugtracker');
		
		try {
			$this->bug = BugtrackerService::get_bug('WHERE id=:id', array('id' => $id));
		} catch (RowNotFoundException $e) {
			$error_controller = new UserErrorController(LangLoader::get_message('error', 'errors-common'), $this->lang['bugs.error.e_unexist_bug']);
			DispatchManager::redirect($error_controller);
		}
		
		$this->view = new FileTemplate('bugtracker/BugtrackerDetailController.tpl');
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
	
	private function build_response(View $view)
	{
		$request = AppContext::get_request();
		$success = $request->get_value('success', '');
		
		$body_view = BugtrackerViews::build_body_view($view, 'detail', $this->bug->get_id());
		
		//Success messages
		switch ($success)
		{
			case 'add':
				$errstr = StringVars::replace_vars($this->lang['bugs.success.add'], array('id' => $this->bug->get_id()));
				break;
			case 'edit':
				$errstr = StringVars::replace_vars($this->lang['bugs.success.edit'], array('id' => $this->bug->get_id()));
				break;
			case 'fixed':
				$errstr = StringVars::replace_vars($this->lang['bugs.success.fixed'], array('id' => $this->bug->get_id()));
				break;
			case 'delete':
				$errstr = StringVars::replace_vars($this->lang['bugs.success.delete'], array('id' => $this->bug->get_id()));
				break;
			case 'reject':
				$errstr = StringVars::replace_vars($this->lang['bugs.success.reject'], array('id' => $this->bug->get_id()));
				break;
			case 'reopen':
				$errstr = StringVars::replace_vars($this->lang['bugs.success.reopen'], array('id' => $this->bug->get_id()));
				break;
			case 'assign':
				$errstr = StringVars::replace_vars($this->lang['bugs.success.assigned'], array('id' => $bug_id));
				break;
			default:
				$errstr = '';
		}
		if (!empty($errstr))
			$body_view->put('MSG', MessageHelper::display($errstr, E_USER_SUCCESS, 5));
		
		$response = new BugtrackerDisplayResponse();
		$response->add_breadcrumb_link($this->lang['bugs.module_title'], BugtrackerUrlBuilder::home());
		$response->add_breadcrumb_link($this->lang['bugs.titles.detail'] . ' #' . $this->bug->get_id(), BugtrackerUrlBuilder::detail($this->bug->get_id()));
		$response->set_page_title($this->lang['bugs.titles.detail'] . ' #' . $this->bug->get_id());
		
		return $response->display($body_view);
	}
}
?>
