<?php
/*##################################################
 *                               management.php
 *                            -------------------
 *   begin                :  August 16, 2009
 *   copyright            : (C) 2009 Maurel Nicolas
 *   email                : crunchfamily@free.fr
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

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

require_once('../kernel/begin.php');
require_once('articles_begin.php');

$articles_categories = new ArticlesCats();

$now = new Date(DATE_NOW, TIMEZONE_AUTO);

$new = retrieve(GET, 'new', 0);
$edit = retrieve(GET, 'edit', 0);
$delete = retrieve(GET, 'del', 0);
$id = retrieve(GET, 'id', 0);
$cat = retrieve(GET, 'cat', 0);
$file_approved = retrieve(POST, 'visible', false);

if ($delete > 0)
{
	$Session->csrf_get_protect();
	$articles = $Sql->query_array(DB_TABLE_ARTICLES, '*', "WHERE id = '" . $delete . "'", __LINE__, __FILE__);

	if (empty($articles['id']))
	{
		$controller = new UserErrorController(LangLoader::get_message('error', 'errors'), 
            $LANG['e_unexist_articles']);
        DispatchManager::redirect($controller);
	}
	elseif (!$User->check_auth($ARTICLES_CAT[$articles['idcat']]['auth'], AUTH_ARTICLES_MODERATE))
	{
		$error_controller = PHPBoostErrors::unexisting_page();
		DispatchManager::redirect($error_controller);
	}

	$Sql->query_inject("DELETE FROM " . DB_TABLE_ARTICLES . " WHERE id = '" . $articles['id'] . "'", __LINE__, __FILE__);
	$Sql->query_inject("DELETE FROM " . DB_TABLE_EVENTS . " WHERE module = 'articles' AND id_in_module = '" . $articles['id'] . "'", __LINE__, __FILE__);

	$articles_cat_info= $Sql->query_array(DB_TABLE_ARTICLES_CAT, "id", "nbr_articles_visible", "nbr_articles_unvisible","WHERE id = '".$articles['idcat']."'", __LINE__, __FILE__);
	
	if($articles['visible'] == 1)
	{
		$nb=$articles_cat_info['nbr_articles_visible'] - 1;
		$Sql->query_inject("UPDATE " . DB_TABLE_ARTICLES_CAT. " SET nbr_articles_visible = '" . $nb. "' WHERE id = '" . $articles['idcat'] . "'", __LINE__, __FILE__);
	}
	else
	{
		$nb=$articles_cat_info['nbr_articles_unvisible'] - 1;
		$Sql->query_inject("UPDATE " . DB_TABLE_ARTICLES_CAT. " SET nbr_articles_unvisible = '" . $nb. "' WHERE id = '" . $articles['idcat'] . "'", __LINE__, __FILE__);
	}
	if ($articles['nbr_com'] > 0)
	{
		
		$Comments = new CommentsTopic('articles', $articles['id'], url('articles.php?id=' . $articles['id'] . '&amp;com=%s', 'articles-' . $articles['idcat'] . '-' . $articles['id'] . '.php?com=%s'));
		$Comments->delete_all($delete_articles);
	}

	// Feeds Regeneration
	
	Feed::clear_cache('articles');

	AppContext::get_response()->redirect('articles' . url('.php?cat=' . $articles['idcat'], '-' . $articles['idcat'] . '+' . Url::encode_rewrite($ARTICLES_CAT[$articles['idcat']]['name']) . '.php'));
}
elseif(retrieve(POST,'submit',false))
{
	$begining_date  = MiniCalendar::retrieve_date('start');
	$end_date = MiniCalendar::retrieve_date('end');
	$release = MiniCalendar::retrieve_date('release');
	$icon=retrieve(POST, 'icon', '', TSTRING);

	if(retrieve(POST,'icon_path',false))
	$icon=retrieve(POST,'icon_path','');

	$sources = array();
	for ($i = 0;$i < 100; $i++)
	{	
		if (retrieve(POST,'a'.$i,false,TSTRING))
		{				
			$sources[$i]['sources'] = retrieve(POST, 'a'.$i, '',TSTRING);
			$sources[$i]['url'] = preg_replace('`\?.*`', '', retrieve(POST, 'v'.$i, '',TSTRING_UNCHANGE));
		}
	}
		
	$articles = array(
		'id' => retrieve(POST, 'id', 0, TINTEGER),
		'idcat' => retrieve(POST, 'idcat', 0),
		'user_id' => retrieve(POST, 'user_id', 0, TINTEGER),
		'title' => retrieve(POST, 'title', '', TSTRING),
		'desc' => retrieve(POST, 'contents', '', TSTRING_PARSE),
		'counterpart' => retrieve(POST, 'counterpart', '', TSTRING_PARSE),
		'visible' => retrieve(POST, 'visible', 0, TINTEGER),
		'start' => $begining_date->get_timestamp(),
		'start_hour' => retrieve(POST, 'start_hour', 0, TINTEGER),
		'start_min' => retrieve(POST, 'start_min', 0, TINTEGER),
		'end' => $end_date->get_timestamp(),
		'end_hour' => retrieve(POST, 'end_hour', 0, TINTEGER),
		'end_min' => retrieve(POST, 'end_min', 0, TINTEGER),
		'release' => $release->get_timestamp(),
		'release_hour' => retrieve(POST, 'release_hour', 0, TINTEGER),
		'release_min' => retrieve(POST, 'release_min', 0, TINTEGER),
		'icon' => $icon,		
		'sources'=>addslashes(serialize($sources)),
		'description'=>retrieve(POST, 'description', '', TSTRING_PARSE)
	);

	if ($articles['id'] == 0 && ($User->check_auth($ARTICLES_CAT[$articles['idcat']]['auth'], AUTH_ARTICLES_WRITE) || $User->check_auth($ARTICLES_CAT[$articles['idcat']]['auth'], AUTH_ARTICLES_CONTRIBUTE)) || $articles['id'] > 0 && ($User->check_auth($ARTICLES_CAT[$articles['idcat']]['auth'], AUTH_ARTICLES_MODERATE) || $User->check_auth($ARTICLES_CAT[$articles['idcat']]['auth'], AUTH_ARTICLES_WRITE) && $articles['user_id'] == $User->get_attribute('user_id')))
	{
		// Errors.
		if (empty($articles['title']))
		{
			//TODO � d�gager, g�r� par le formBuilder
			$controller = new UserErrorController(LangLoader::get_message('error', 'errors'), 
                $LANG['e_require_title']);
            DispatchManager::redirect($controller);
		}
		elseif (empty($articles['desc']))
		{
			//TODO � d�gager, g�r� par le formBuilder
			$controller = new UserErrorController(LangLoader::get_message('error', 'errors'), 
                $LANG['e_require_desc']);
            DispatchManager::redirect($controller);
		}
		else
		{
			// $start & $end.
			if ($articles['visible'] == 2)
			{
				// Start.
				$articles['start'] += ($articles['start_hour'] * 60 + $articles['start_min']) * 60;
				if ($articles['start'] <= $now->get_timestamp())
				$articles['start'] = 0;
				// End.
				$articles['end'] += ($articles['end_hour'] * 60 + $articles['end_min']) * 60;
				if ($articles['end'] <= $now->get_timestamp())
				$articles['end'] = 0;

				$articles['visible'] = 1;
			}
			else
				$articles['start'] = $articles['end'] = 0;

			// Release.
			$articles['release'] += ($articles['release_hour'] * 60 + $articles['release_min']) * 60;
			if ($articles['release'] == 0)
				$articles['release'] = $now->get_timestamp();

			// Image.
			$img = $articles['icon'];

			if ($articles['id'] > 0)
			{
				$visible = 1;
				$date_now = new Date(DATE_NOW);

				switch ($articles['visible'])
				{
					//If it's a time interval
					case 2:
						if ($begining_date->get_timestamp() < $date_now->get_timestamp() &&  $end_date->get_timestamp() > $date_now->get_timestamp())
						{
							$start_timestamp = $begining_date->get_timestamp();
							$end_timestamp = $end_date->get_timestamp();
						}
						else
						$visible = 0;

						break;
						//If it's always visible
					case 1:
						list($start_timestamp, $end_timestamp) = array(0, 0);
						break;
					default:
						list($visible, $start_timestamp, $end_timestamp) = array(0, 0, 0);
				}
				$articles_properties = $Sql->query_array(PREFIX . "articles", "visible", "WHERE id = '" . $articles['id'] . "'", __LINE__, __FILE__);
			
				$Sql->query_inject("UPDATE " . DB_TABLE_ARTICLES . " SET idcat = '" . $articles['idcat'] . "', title = '" . $articles['title'] . "', contents = '" . $articles['desc'] . "',  icon = '" . $img . "',  visible = '" . $visible . "', start = '" .  $articles['start'] . "', end = '" . $articles['end'] . "', timestamp = '" . $articles['release'] . "', sources = '".$articles['sources']."', description = '".$articles['description']."'
				WHERE id = '" . $articles['id'] . "'", __LINE__, __FILE__);

				//If it wasn't approved and now it's, we try to consider the corresponding contribution as processed
				if ($file_approved && !$articles_properties['visible'])
				{
					
					
						
					$corresponding_contributions = ContributionService::find_by_criteria('articles', $articles['id']);
					if (count($corresponding_contributions) > 0)
					{
						$articles_cat_info = $Sql->query_array(DB_TABLE_ARTICLES_CAT, "id", "nbr_articles_visible", "nbr_articles_unvisible","WHERE id = '".$articles['idcat']."'", __LINE__, __FILE__);
						$nb_visible = $articles_cat_info['nbr_articles_visible'] + 1 ;
						$nb_unvisible = $articles_cat_info['nbr_articles_unvisible'] - 1;
						$Sql->query_inject("UPDATE " . DB_TABLE_ARTICLES_CAT. " SET nbr_articles_visible = '" . $nb_visible. "',nbr_articles_unvisible = '".$nb_unvisible."' WHERE id = '" . $articles['idcat'] . "'", __LINE__, __FILE__);
						$file_contribution = $corresponding_contributions[0];
						//The contribution is now processed
						$file_contribution->set_status(Event::EVENT_STATUS_PROCESSED);
						//We save the contribution
						ContributionService::save_contribution($file_contribution);
					}
				}
				$Cache->Generate_module_file('articles');
			}
			else
			{
				$auth_contrib = !$User->check_auth($ARTICLES_CAT[$articles['idcat']]['auth'], AUTH_ARTICLES_WRITE) && $User->check_auth($ARTICLES_CAT[$articles['idcat']]['auth'], AUTH_ARTICLES_CONTRIBUTE);
			
				$auth = $articles['auth'];
					
				$Sql->query_inject("INSERT INTO " . DB_TABLE_ARTICLES . " (idcat, title, contents,timestamp, visible, start, end, user_id, icon, sources, description)
				VALUES('" . $articles['idcat'] . "', '" . $articles['title'] . "', '" . $articles['desc'] . "', '" . $articles['release'] . "', '" . $articles['visible'] . "', '" . $articles['start'] . "', '" . $articles['end'] . "', '" . $User->get_attribute('user_id') . "', '" . $img . "', '".$articles['sources']."', '".$articles['description']."')", __LINE__, __FILE__);
				$articles['id'] = $Sql->insert_id("SELECT MAX(id) FROM " . DB_TABLE_ARTICLES);

				$articles_cat_info= $Sql->query_array(DB_TABLE_ARTICLES_CAT, "id", "nbr_articles_visible", "nbr_articles_unvisible","WHERE id = '".$articles['idcat']."'", __LINE__, __FILE__);

				if($articles['visible'] == 1)
				{
					$nb=$articles_cat_info['nbr_articles_visible'] + 1;
					$Sql->query_inject("UPDATE " . DB_TABLE_ARTICLES_CAT. " SET nbr_articles_visible = '" . $nb. "' WHERE id = '" . $articles['idcat'] . "'", __LINE__, __FILE__);
				}
				else
				{
					$nb=$articles_cat_info['nbr_articles_unvisible'] + 1;
					$Sql->query_inject("UPDATE " . DB_TABLE_ARTICLES_CAT. " SET nbr_articles_unvisible = '" . $nb. "' WHERE id = '" . $articles['idcat'] . "'", __LINE__, __FILE__);
				}

				//If the poster couldn't write, it's a contribution and we put it in the contribution panel, it must be approved
				if ($auth_contrib)
				{
					//Importing the contribution classes
					
					
					$articles_contribution = new Contribution();

					//The id of the file in the module. It's useful when the module wants to search a contribution (we will need it in the file edition)
					$articles_contribution->set_id_in_module($articles['id']);
					//The description of the contribution (the counterpart) to explain why did the contributor contributed
					$articles_contribution->set_description(stripslashes($articles['counterpart']));
					//The entitled of the contribution
					$articles_contribution->set_entitled(sprintf($ARTICLES_LANG['contribution_entitled'], $articles['title']));
					//The URL where a validator can treat the contribution (in the file edition panel)
					$articles_contribution->set_fixing_url('/articles/management.php?edit=' . $articles['id']);
					//Who is the contributor?
					$articles_contribution->set_poster_id($User->get_attribute('user_id'));
					//The module
					$articles_contribution->set_module('articles');
					//Assignation des autorisations d'�criture / Writing authorization assignation
					$articles_contribution->set_auth(
					//On d�place le bit sur l'autorisation obtenue pour le mettre sur celui sur lequel travaille les contributions, � savoir Contribution::CONTRIBUTION_AUTH_BIT
					//We shift the authorization bit to the one with which the contribution class works, Contribution::CONTRIBUTION_AUTH_BIT
					Authorizations::capture_and_shift_bit_auth(
					//On fusionne toutes les autorisations pour obtenir l'autorisation d'�criture dans la cat�gorie s�lectionn�e :
					//C'est la fusion entre l'autorisation de la racine et de l'ensemble de la branche des cat�gories
					//We merge the whole authorizations of the branch constituted by the selected category
					Authorizations::merge_auth(
					$CONFIG_ARTICLES['global_auth'],
					//Autorisation de l'ensemble de la branche des cat�gories jusqu'� la cat�gorie demand�e
					$articles_categories->compute_heritated_auth($articles['idcat'], AUTH_ARTICLES_MODERATE, Authorizations::AUTH_CHILD_PRIORITY),
					AUTH_ARTICLES_MODERATE, Authorizations::AUTH_CHILD_PRIORITY
					),
					AUTH_ARTICLES_MODERATE, Contribution::CONTRIBUTION_AUTH_BIT
					)
					);
					//Sending the contribution to the kernel. It will place it in the contribution panel to be approved
					ContributionService::save_contribution($articles_contribution);

					//Redirection to the contribution confirmation page
					AppContext::get_response()->redirect(UserUrlBuilder::contribution_success()->absolute());
				}
			}

			// Feeds Regeneration
			
			Feed::clear_cache('articles');

			if ($articles['visible'])
			AppContext::get_response()->redirect('./articles' . url('.php?id=' . $articles['id'].'&cat='.$articles['idcat'] , '-' . $articles['idcat'] . '-' . $articles['id'] . '+' . Url::encode_rewrite($articles['title']) . '.php'));
			else
			AppContext::get_response()->redirect(url('articles.php'));
		}
	}
	else
	{
		$error_controller = PHPBoostErrors::unexisting_page();
		DispatchManager::redirect($error_controller);
	}
}
else
{
	$tpl = new FileTemplate('articles/management.tpl');

	if ($edit > 0)
	{
		$articles = $Sql->query_array(DB_TABLE_ARTICLES, '*', "WHERE id = '" . $edit . "'", __LINE__, __FILE__);

		if (!empty($articles['id']) && ($User->check_auth($ARTICLES_CAT[$articles['idcat']]['auth'], AUTH_ARTICLES_MODERATE) || $User->check_auth($ARTICLES_CAT[$articles['idcat']]['auth'], AUTH_ARTICLES_WRITE) && $articles['user_id'] == $User->get_attribute('user_id')))
		{
			$articles_categories->bread_crumb($articles['idcat']);
			$Bread_crumb->remove_last();
			$Bread_crumb->add($articles['title'], 'articles' . url('.php?id=' . $articles['id'].'&amp;cat='.$articles['idcat'], '-' . $articles['idcat'] . '-' . $articles['id'] . '+' . Url::encode_rewrite($articles['title']) . '.php'));
			$Bread_crumb->add($ARTICLES_LANG['edit_articles'], url('management.php?edit=' . $articles['id']));

			$special_auth = (unserialize($articles['auth']) !== $ARTICLES_CAT[$articles['idcat']]['auth']) && ($articles['auth'] != '')  ? true : false;
			$articles['auth'] = $special_auth ? unserialize($articles['auth']) : $ARTICLES_CAT[$articles['idcat']]['auth'];

			// Calendrier.
			$start_calendar = new MiniCalendar('start');
			$start = new Date(DATE_TIMESTAMP, TIMEZONE_AUTO, ($articles['start'] > 0 ? $articles['start'] : $now->get_timestamp()));
			$start_calendar->set_date($start);
			$end_calendar = new MiniCalendar('end');
			$end = new Date(DATE_TIMESTAMP, TIMEZONE_AUTO, ($articles['end'] > 0 ? $articles['end'] : $now->get_timestamp()));
			$end_calendar->set_date($end);
			$end_calendar->set_style('margin-left:150px;');
			$release_calendar = new MiniCalendar('release');
			$release = new Date(DATE_TIMESTAMP, TIMEZONE_AUTO, ($articles['timestamp'] > 0 ? $articles['timestamp'] : $now->get_timestamp()));
			$release_calendar->set_date($release);

			$img_direct_path = (strpos($articles['icon'], '/') !== false);
			$image_list = '<option value=""' . ($img_direct_path ? ' selected="selected"' : '') . '>--</option>';
			
			$image_list = '<option value="">--</option>';
			$image_folder_path = new Folder('./');
			foreach ($image_folder_path->get_files('`\.(png|jpg|bmp|gif|jpeg|tiff)$`i') as $images)
			{
				$image = $images->get_name();
				$selected = $image == $articles['icon'] ? ' selected="selected"' : '';
				$image_list .= '<option value="' . $image . '"' . ($img_direct_path ? '' : $selected) . '>' . $image . '</option>';
			}			
			
			// sources
			$array_sources = unserialize($articles['sources']);
			$i = 0;
			foreach ($array_sources as $sources)
			{	
				$tpl->assign_block_vars('sources', array(
					'I' => $i,
					'SOURCE' => stripslashes($sources['sources']),
					'URL' => $sources['url'],
				));
				$i++;
			}	
			
			if($i==0)
			{
				$tpl->assign_block_vars('sources', array(
						'I' => 0,
						'SOURCE' => '',
						'URL' => '',
					));
			}

			$tpl->put_all(array(
				'C_ADD' => true,
				'C_CONTRIBUTION' => false,
				'JS_CONTRIBUTION' => 'false',
				'RELEASE_CALENDAR_ID' => $release_calendar->get_html_id(),
				'TITLE_ART' => $articles['title'],
				'CONTENTS' => FormatingHelper::unparse($articles['contents']),
				'VISIBLE_WAITING' => $articles['visible'] && (!empty($articles['start']) || !empty($articles['end'])),
				'VISIBLE_ENABLED' => $articles['visible'] && empty($articles['start']) && empty($articles['end']),
				'VISIBLE_UNAPROB' => !$articles['visible'],
				'START_CALENDAR' => $start_calendar->display(),
				'START_HOUR' => !empty($articles['start']) ? $start->get_hours() : '',
				'START_MIN' => !empty($articles['start']) ? $start->get_minutes() : '',
				'END_CALENDAR' => $end_calendar->display(),
				'END_HOUR' => !empty($articles['end']) ? $end->get_hours() : '',
				'END_MIN' => !empty($articles['end']) ? $end->get_minutes() : '',
				'RELEASE_CALENDAR' => $release_calendar->display(),
				'RELEASE_HOUR' => !empty($articles['timestamp']) ? $release->get_hours() : '',
				'RELEASE_MIN' => !empty($articles['timestamp']) ? $release->get_minutes() : '',
				'IMG_PATH' => $img_direct_path ? $articles['icon'] : '',
				'IMG_ICON' => !empty($articles['icon']) ? '<img src="' . $articles['icon'] . '" alt="" class="valign_middle" />' : '',		
				'IMG_LIST'=>$image_list,
				'IDARTICLES' => $articles['id'],
				'USER_ID' => $articles['user_id'],
				'NB_SOURCE' => $i == 0 ? 1 : $i
			));

			$articles_categories->build_select_form($articles['idcat'], 'idcat', 'idcat', 0, AUTH_ARTICLES_READ, $CONFIG_ARTICLES['global_auth'], IGNORE_AND_CONTINUE_BROWSING_IF_A_CATEGORY_DOES_NOT_MATCH, $tpl);
		}
		else
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
	}
	else
	{
		if (!$User->check_auth($ARTICLES_CAT[$cat]['auth'], AUTH_ARTICLES_CONTRIBUTE) && !$User->check_auth($ARTICLES_CAT[$cat]['auth'], AUTH_ARTICLES_WRITE))
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
		else
		{
			$auth_contrib = !$User->check_auth($CONFIG_ARTICLES['global_auth'], AUTH_ARTICLES_WRITE) && $User->check_auth($CONFIG_ARTICLES['global_auth'], AUTH_ARTICLES_CONTRIBUTE);			
			$Bread_crumb->add($ARTICLES_LANG['articles_add'],url('management.php?new=1&amp;cat=' . $cat));
			
			//Images disponibles
			$image_list = '<option value="" selected="selected">--</option>';
			
			$image_list = '<option value="">--</option>';
			$image_folder_path = new Folder('./');
			foreach ($image_folder_path->get_files('`\.(png|jpg|bmp|gif|jpeg|tiff)$`i') as $images)
			{
				$image = $images->get_name();
				$image_list .= '<option value="' . $image . '">' . $image . '</option>';
			}

			// Calendrier.
			$now = new Date(DATE_NOW, TIMEZONE_AUTO);
			$start_calendar = new MiniCalendar('start');
			$start_calendar->set_date(new Date(DATE_NOW, TIMEZONE_AUTO));
			$end_calendar = new MiniCalendar('end');
			$end_calendar->set_date(new Date(DATE_NOW, TIMEZONE_AUTO));
			$end_calendar->set_style('margin-left:150px;');
			$release_calendar = new MiniCalendar('release');
			$release_calendar->set_date(new Date(DATE_NOW, TIMEZONE_AUTO));

			$tpl->put_all(array(
				'C_ADD' => false,
				'C_CONTRIBUTION' => $auth_contrib ,
				'JS_CONTRIBUTION' => $auth_contrib ? 'true' : 'false',
				'RELEASE_CALENDAR_ID' => $release_calendar->get_html_id(),
				'TITLE' => '',
				'CONTENTS' => '',
				'DESCRIPTION'=>'',
				'EXTEND_CONTENTS' => '',
				'VISIBLE_WAITING' => 0,
				'VISIBLE_ENABLED' => 1,
				'VISIBLE_UNAPROB' => 0,
				'START_CALENDAR' => $start_calendar->display(),
				'START_HOUR' => '',
				'START_MIN' => '',
				'END_CALENDAR' => $end_calendar->display(),
				'END_HOUR' => '',
				'END_MIN' => '',
				'RELEASE_CALENDAR' => $release_calendar->display(),
				'RELEASE_HOUR' => $now->get_hours(),
				'RELEASE_MIN' => $now->get_minutes(),
				'IMG' => '',
				'ALT' => '',
				'IDARTICLES' => '0',
				'USER_ID' => $User->get_attribute('user_id'),
				'IMG_PATH' => '',
				'IMG_ICON' => '',	
				'IMG_LIST' => $image_list,
				'NB_SOURCE' => 1,
			));
				
			$tpl->assign_block_vars('sources', array(
				'I' => 0,
				'SOURCE' => '',
				'URL' => '',
			));
			
			$cat = $cat > 0 && ($User->check_auth($ARTICLES_CAT[$cat]['auth'], AUTH_ARTICLES_CONTRIBUTE) || $User->check_auth($ARTICLES_CAT[$cat]['auth'], AUTH_ARTICLES_WRITE)) ? $cat : 0;
			$articles_categories->build_select_form($cat, 'idcat', 'idcat', 0, AUTH_ARTICLES_READ, $CONFIG_ARTICLES['global_auth'], IGNORE_AND_CONTINUE_BROWSING_IF_A_CATEGORY_DOES_NOT_MATCH, $tpl);
		}
	}
	require_once('../kernel/header.php');

	$user_pseudo = !empty($user_pseudo) ? $user_pseudo : '';

	$editor = AppContext::get_content_formatting_service()->get_default_editor();
	$editor->set_identifier('contents');
	
	$desc_editor = AppContext::get_content_formatting_service()->get_default_editor();
	$desc_editor->set_identifier('description');
	
	$tpl->put_all(array(
		'KERNEL_EDITOR' => $editor->display(),
		'KERNEL_EDITOR_DESC' => $desc_editor->display(),
		'TITLE' => '',	
		'NOW_DATE' => $now->format(DATE_FORMAT_SHORT, TIMEZONE_AUTO),
		'NOW_HOUR' => $now->get_hours(),
		'NOW_MIN' => $now->get_minutes(),
		'VISIBLE_ENABLED' => 'checked="checked"',
		'L_REQUIRE_CAT' => $ARTICLES_LANG['require_cat'],
		'L_PAGE_PROMPT' => $ARTICLES_LANG['page_prompt'],
		'L_ARTICLES_DATE' => $ARTICLES_LANG['articles_date'],
		'L_ARTICLES_ADD' => $ARTICLES_LANG['articles_add'],
		'L_ARTICLE_ICON' => $ARTICLES_LANG['article_icon'],
		'L_RELEASE_DATE' => $ARTICLES_LANG['release_date'],
		'L_ARTICLES_DATE' => $ARTICLES_LANG['articles_date'],
		'L_ARTICLE_DESCRIPTION'=>$ARTICLES_LANG['article_description'],
		'L_EXPLAIN_PAGE' => $ARTICLES_LANG['explain_page'],
		'L_NOTICE_CONTRIBUTION' => $ARTICLES_LANG['notice_contribution'],
		'L_CONTRIBUTION_COUNTERPART' => $ARTICLES_LANG['contribution_counterpart'],
		'L_CONTRIBUTION_COUNTERPART_EXPLAIN' => $ARTICLES_LANG['contribution_counterpart_explain'],	
		'L_OR_DIRECT_PATH' => $ARTICLES_LANG['or_direct_path'],
		'L_SOURCE'=>$ARTICLES_LANG['source'],
		'L_ADD_SOURCE'=>$ARTICLES_LANG['add_source'],
		'L_SOURCE_LINK'=>$ARTICLES_LANG['source_link'],
		'L_SPECIAL_AUTH' => $ARTICLES_LANG['special_auth'],
		'L_SPECIAL_AUTH_EXPLAIN_ARTICLES' => $ARTICLES_LANG['special_auth_explain_articles'],
		'L_AUTH_READ' => $ARTICLES_LANG['auth_read'],
		'L_REQUIRE' => $LANG['require'],
		'L_PREVIEW' => $LANG['preview'],
		'L_CATEGORY' => $LANG['categories'],
		'L_TITLE' => $LANG['title'],
		'L_UNTIL' => $LANG['until'],
		'L_IMMEDIATE' => $LANG['now'],
		'L_UNAPROB' => $LANG['unaprob'],
		'L_TEXT' => $LANG['content'],
		'L_SUBMIT' => $LANG['submit'],
		'L_RESET' => $LANG['reset'],
		'L_REQUIRE_TITLE' => $LANG['require_title'],
		'L_REQUIRE_TEXT' => $LANG['require_text'],
		'L_CONTRIBUTION_LEGEND' => $LANG['contribution']
	));

	//Gestion erreur.
	$get_error = retrieve(GET, 'error', '');
	if ($get_error == 'incomplete')
	{
		$tpl->put('message_helper', MessageHelper::display($LANG['e_incomplete'], E_USER_NOTICE));
	}
	$tpl->display();
}

require_once('../kernel/footer.php');

?>
