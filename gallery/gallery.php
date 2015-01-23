<?php
/*##################################################
 *                               gallery.php
 *                            -------------------
 *   begin                : August 12, 2005
 *   copyright            : (C) 2005 Viarre R�gis
 *   email                : crowkait@phpboost.com
 *
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

require_once('../kernel/begin.php');
require_once('../gallery/gallery_begin.php');
require_once('../kernel/header.php');

$config = GalleryConfig::load();

$g_idpics = retrieve(GET, 'id', 0);
$g_del = retrieve(GET, 'del', 0);
$g_move = retrieve(GET, 'move', 0);
$g_add = retrieve(GET, 'add', false);
$g_page = retrieve(GET, 'p', 1);
$g_views = retrieve(GET, 'views', false);
$g_notes = retrieve(GET, 'notes', false);
$g_sort = retrieve(GET, 'sort', '');
$g_sort = !empty($g_sort) ? 'sort=' . $g_sort : '';

//R�cup�ration du mode d'ordonnement.
if (preg_match('`([a-z]+)_([a-z]+)`', $g_sort, $array_match))
{
	$g_type = $array_match[1];
	$g_mode = $array_match[2];
}
else
	list($g_type, $g_mode) = array('date', 'desc');

$Gallery = new Gallery();

if (!empty($g_del)) //Suppression d'une image.
{
	if (AppContext::get_current_user()->is_readonly())
	{
		$controller = PHPBoostErrors::user_in_read_only();
		DispatchManager::redirect($controller);
	}
	
	AppContext::get_session()->csrf_get_protect(); //Protection csrf

	$Gallery->Del_pics($g_del);

	//R�g�n�ration du cache des photos al�atoires.
	$Cache->Generate_module_file('gallery');

	AppContext::get_response()->redirect('/gallery/gallery' . url('.php?cat=' . $g_idcat, '-' . $g_idcat . '.php', '&'));
}
elseif (!empty($g_idpics) && $g_move) //D�placement d'une image.
{
	if (AppContext::get_current_user()->is_readonly())
	{
		$controller = PHPBoostErrors::user_in_read_only();
		DispatchManager::redirect($controller);
	}
	
	AppContext::get_session()->csrf_get_protect(); //Protection csrf

	$g_move = max($g_move, 0);
	$Gallery->Move_pics($g_idpics, $g_move);

	//R�g�n�ration du cache des photos al�atoires.
	$Cache->Generate_module_file('gallery');

	AppContext::get_response()->redirect('/gallery/gallery' . url('.php?cat=' . $g_move, '-' . $g_move . '.php', '&'));
}
elseif (isset($_FILES['gallery'])) //Upload
{
	if (AppContext::get_current_user()->is_readonly())
	{
		$controller = PHPBoostErrors::user_in_read_only();
		DispatchManager::redirect($controller);
	}
	
	$g_idcat = retrieve(POST, 'cat', 0);
	if (!empty($g_idcat))
	{
		if (!isset($CAT_GALLERY[$g_idcat]) || $CAT_GALLERY[$g_idcat]['aprob'] == 0)
			AppContext::get_response()->redirect('/gallery/gallery' . url('.php?error=unexist_cat', '', '&'));
	}
	else //Racine.
		$CAT_GALLERY[0]['auth'] = $config->get_authorizations();

	//Niveau d'autorisation de la cat�gorie, acc�s en �criture.
	if (!AppContext::get_current_user()->check_auth($CAT_GALLERY[$g_idcat]['auth'], GalleryAuthorizationsService::WRITE_AUTHORIZATIONS))
	{
		$error_controller = PHPBoostErrors::user_not_authorized();
		DispatchManager::redirect($error_controller);
	}

	//Niveau d'autorisation de la cat�gorie, acc�s en �criture.
	if (!$Gallery->auth_upload_pics(AppContext::get_current_user()->get_id(), AppContext::get_current_user()->get_level()))
		AppContext::get_response()->redirect('/gallery/gallery' . url('.php?add=1&cat=' . $g_idcat . '&error=upload_limit', '-' . $g_idcat . '.php?add=1&error=upload_limit', '&') . '#message_helper');

	$dir = 'pics/';

	$Upload = new Upload($dir);

	$idpic = 0;
	$idcat_post = retrieve(POST, 'cat', '');
	$name_post = retrieve(POST, 'name', '', TSTRING_AS_RECEIVED);

	$Upload->file('gallery', '`([a-z0-9()_-])+\.(jpg|jpeg|gif|png)+$`i', Upload::UNIQ_NAME, $config->get_max_weight());
	if ($Upload->get_error() != '') //Erreur, on arr�te ici
	{
		AppContext::get_response()->redirect(GalleryUrlBuilder::get_link_cat_add($g_idcat,$Upload->get_error()) . '#message_helper');
	}
	else
	{
		$path = $dir . $Upload->get_filename();
		$error = $Upload->check_img($config->get_max_width(), $config->get_max_height(), Upload::DELETE_ON_ERROR);
		if (!empty($error)) //Erreur, on arr�te ici
			AppContext::get_response()->redirect(GalleryUrlBuilder::get_link_cat_add($g_idcat,$error) . '#message_helper');
		else
		{
			//Enregistrement de l'image dans la bdd.
			$Gallery->Resize_pics($path);
			if ($Gallery->get_error() != '')
				AppContext::get_response()->redirect(GalleryUrlBuilder::get_link_cat_add($g_idcat,$Upload->get_error()) . '#message_helper');

			$idpic = $Gallery->Add_pics($idcat_post, $name_post, $Upload->get_filename(), AppContext::get_current_user()->get_id());
			if ($Gallery->get_error() != '')
				AppContext::get_response()->redirect(GalleryUrlBuilder::get_link_cat_add($g_idcat,$Upload->get_error()) . '#message_helper');

			//R�g�n�ration du cache des photos al�atoires.
			$Cache->Generate_module_file('gallery');
		}
	}

	AppContext::get_response()->redirect(GalleryUrlBuilder::get_link_item_add($idcat_post,$idpic));
}
elseif ($g_add)
{
	if (AppContext::get_current_user()->is_readonly())
	{
		$controller = PHPBoostErrors::user_in_read_only();
		DispatchManager::redirect($controller);
	}
	
	$tpl = new FileTemplate('gallery/gallery_add.tpl');

	if (!empty($g_idcat))
	{
		if (!isset($CAT_GALLERY[$g_idcat]) || $CAT_GALLERY[$g_idcat]['aprob'] == 0)
			AppContext::get_response()->redirect('/gallery/gallery' . url('.php?error=unexist_cat', '', '&'));

		$cat_links = '';
		foreach ($CAT_GALLERY as $id => $array_info_cat)
		{
			if ($id > 0)
			{
				if ($CAT_GALLERY[$g_idcat]['id_left'] >= $array_info_cat['id_left'] && $CAT_GALLERY[$g_idcat]['id_right'] <= $array_info_cat['id_right'] && $array_info_cat['level'] <= $CAT_GALLERY[$g_idcat]['level'])
					$cat_links .= ' <a href="' . GalleryUrlBuilder::get_link_cat($id) . '">' . $array_info_cat['name'] . '</a> &raquo;';
			}
		}
	}
	else //Racine.
	{
		$cat_links = '';
		$CAT_GALLERY[0]['auth'] = $config->get_authorizations();
		$CAT_GALLERY[0]['aprob'] = 1;
		$CAT_GALLERY[0]['name'] = $LANG['root'];
	}

	//Niveau d'autorisation de la cat�gorie, acc�s en �criture.
	if (!AppContext::get_current_user()->check_auth($CAT_GALLERY[$g_idcat]['auth'], GalleryAuthorizationsService::WRITE_AUTHORIZATIONS))
	{
		$error_controller = PHPBoostErrors::user_not_authorized();
		DispatchManager::redirect($error_controller);
	}

	$auth_cats = '<option value="0">' . $LANG['root'] . '</option>';
	foreach ($CAT_GALLERY as $idcat => $key)
	{
		if ($idcat != 0  && $CAT_GALLERY[$idcat]['aprob'] == 1)
		{
			if (AppContext::get_current_user()->check_auth($CAT_GALLERY[$idcat]['auth'], GalleryAuthorizationsService::READ_AUTHORIZATIONS) && AppContext::get_current_user()->check_auth($CAT_GALLERY[$idcat]['auth'], GalleryAuthorizationsService::WRITE_AUTHORIZATIONS))
			{
				$margin = ($CAT_GALLERY[$idcat]['level'] > 0) ? str_repeat('--------', $CAT_GALLERY[$idcat]['level']) : '--';
				$selected = ($idcat == $g_idcat) ? ' selected="selected"' : '';
				$auth_cats .= '<option value="' . $idcat . '"' . $selected . '>' . $margin . ' ' . $CAT_GALLERY[$idcat]['name'] . '</option>';
			}
		}
	}

	//Gestion erreur.
	$get_error = retrieve(GET, 'error', '');
	$array_error = array('e_upload_invalid_format', 'e_upload_max_weight', 'e_upload_max_dimension', 'e_upload_error', 'e_upload_php_code', 'e_upload_failed_unwritable', 'e_upload_already_exist', 'e_unlink_disabled', 'e_unsupported_format', 'e_unabled_create_pics', 'e_error_resize', 'e_no_graphic_support', 'e_unabled_incrust_logo', 'delete_thumbnails', 'upload_limit');
	if (in_array($get_error, $array_error))
		$tpl->put('message_helper', MessageHelper::display(LangLoader::get_message($get_error, 'errors'), MessageHelper::WARNING));
	elseif ($get_error == 'unexist_cat')
		$tpl->put('message_helper', MessageHelper::display(LangLoader::get_message('element.unexist', 'status-messages-common'), MessageHelper::NOTICE));

	$module_data_path = $tpl->get_pictures_data_path();
	$path_pics = PersistenceContext::get_querier()->get_column_value(PREFIX . "gallery", 'path', 'WHERE id = :id', array('id' => $g_idpics));

	//Aficchage de la photo upload�e.
	if (!empty($g_idpics))
	{
		$imageup = PersistenceContext::get_querier()->select_single_row(PREFIX . "gallery", array('idcat', 'name', 'path'), 'WHERE id = :id', array('id' => $g_idpics));
		$tpl->assign_block_vars('image_up', array(
			'NAME' => $imageup['name'],
			'IMG' => '<a href="gallery.php?cat=' . $imageup['idcat'] . '&amp;id=' . $g_idpics . '#pics_max"><img src="pics/' . $imageup['path'] . '" alt="" /></a>',
			'L_SUCCESS_UPLOAD' => $LANG['success_upload_img'],
			'U_CAT' => '<a href="gallery.php?cat=' . $imageup['idcat'] . '">' . $CAT_GALLERY[$imageup['idcat']]['name'] . '</a>'
		));
	}

	//Affichage du quota d'image upload�e.
	$quota = isset($CAT_GALLERY[$g_idcat]['auth']['r-1']) ? ($CAT_GALLERY[$g_idcat]['auth']['r-1'] != '3') : true;
	if ($quota)
	{
		switch (AppContext::get_current_user()->get_level())
		{
			case 2:
			$l_pics_quota = $LANG['illimited'];
			break;
			case 1:
			$l_pics_quota = $config->get_moderator_max_pics_number();
			break;
			default:
			$l_pics_quota = $config->get_member_max_pics_number();
		}
		$nbr_upload_pics = $Gallery->get_nbr_upload_pics(AppContext::get_current_user()->get_id());

		$tpl->assign_block_vars('image_quota', array(
			'L_IMAGE_QUOTA' => sprintf($LANG['image_quota'], $nbr_upload_pics, $l_pics_quota)
		));
	}

	$tpl->put_all(array(
		'CAT_ID' => $g_idcat,
		'GALLERY' => !empty($g_idcat) ? $CAT_GALLERY[$g_idcat]['name'] : $LANG['gallery'],
		'CATEGORIES' => $auth_cats,
		'WIDTH_MAX' => $config->get_max_width(),
		'HEIGHT_MAX' => $config->get_max_height(),
		'WEIGHT_MAX' => $config->get_max_weight(),
		'IMG_FORMAT' => 'JPG, PNG, GIF',
		'L_IMG_FORMAT' => $LANG['img_format'],
		'L_WIDTH_MAX' => $LANG['width_max'],
		'L_HEIGHT_MAX' => $LANG['height_max'],
		'L_WEIGHT_MAX' => $LANG['weight_max'],
		'L_ADD_IMG' => $LANG['add_pic'],
		'L_GALLERY' => $LANG['gallery'],
		'L_GALLERY_INDEX' => $LANG['gallery_index'],
		'L_CATEGORIES' => $LANG['categories'],
		'L_NAME' => $LANG['name'],
		'L_UNIT_PX' => LangLoader::get_message('unit.pixels', 'common'),
		'L_UNIT_KO' => LangLoader::get_message('unit.kilobytes', 'common'),
		'L_UPLOAD' => $LANG['upload_img'],
		'U_GALLERY_CAT_LINKS' => $cat_links,
		'U_GALLERY_ACTION_ADD' => GalleryUrlBuilder::get_link_cat_add($g_idcat,null,AppContext::get_session()->get_token()),
		'U_INDEX' => url('.php')
	));

	$tpl->display();
}
else
{
	$module = AppContext::get_extension_provider_service()->get_provider('gallery');
	if ($module->has_extension_point(HomePageExtensionPoint::EXTENSION_POINT))
	{
		echo $module->get_extension_point(HomePageExtensionPoint::EXTENSION_POINT)->get_home_page()->get_view()->display();
	}
}

require_once('../kernel/footer.php');

?>