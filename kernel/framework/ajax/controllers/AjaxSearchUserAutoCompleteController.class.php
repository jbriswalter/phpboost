<?php
/**
 * @package     Ajax
 * @subpackage  Controllers
 * @copyright   &copy; 2005-2019 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 5.2 - last update: 2016 04 28
 * @since       PHPBoost 4.0 - 2013 06 26
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
 * @contributor Arnaud GENET <elenwii@phpboost.com>
*/

class AjaxSearchUserAutoCompleteController extends AbstractController
{
	public function execute(HTTPRequestCustom $request)
	{
		$lang = LangLoader::get('common');
		$is_admin = AppContext::get_current_user()->check_level(User::ADMIN_LEVEL);
		$number_admins = UserService::count_admin_members();
		$suggestions = array();

		try {
			$result = PersistenceContext::get_querier()->select("SELECT user_id, display_name, level, groups FROM " . DB_TABLE_MEMBER . " WHERE display_name LIKE '" . str_replace('*', '%', $request->get_value('value', '')) . "%'");

			while($row = $result->fetch())
			{
				$user_group_color = User::get_group_color($row['groups'], $row['level']);

				$suggestion = '';

				if ($is_admin)
				{
					$edit_link = new LinkHTMLElement(UserUrlBuilder::edit_profile($row['user_id']), '', array('aria-label' => $lang['edit']), 'fa fa-edit');

					if ($row['level'] != User::ADMIN_LEVEL || ($row['level'] == User::ADMIN_LEVEL && $number_admins > 1))
						$delete_link = new LinkHTMLElement(AdminMembersUrlBuilder::delete($row['user_id']), '', array('aria-label' => $lang['delete'], 'data-confirmation' => 'delete-element'), 'fa fa-delete');
					else
						$delete_link = new LinkHTMLElement('', '', array('aria-label' => $lang['delete'], 'onclick' => 'return false;'), 'fa fa-delete icon-disabled');

					$suggestion .= $edit_link->display() . '&nbsp;' . $delete_link->display() . '&nbsp;';
				}

				$profile_link = new LinkHTMLElement(UserUrlBuilder::profile($row['user_id'])->rel(), $row['display_name'], array('style' => (!empty($user_group_color) ? 'color:' . $user_group_color : '')), UserService::get_level_class($row['level']));

				$suggestion .= $profile_link->display();

				$suggestions[] = $suggestion;
			}
			$result->dispose();
		} catch (Exception $e) {
		}

		return new JSONResponse(array('suggestions' => $suggestions));
	}
}
?>
