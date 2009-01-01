<?php
/*##################################################
 *                           admin_link_menus.php
 *                            -------------------
 *   begin                : November, 13 2008
 *   copyright            : (C) 2008 Lo�c Rouchon
 *   email                : horn@phpboost.com
 *
 *
 *
###################################################
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
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

define('PATH_TO_ROOT', '../..');
require_once(PATH_TO_ROOT . '/admin/admin_begin.php');
define('TITLE', $LANG['administration']);
require_once(PATH_TO_ROOT . '/admin/admin_header.php');

import('core/menu_service');

$menu_id = retrieve(REQUEST, 'id', 0);
$action = retrieve(REQUEST, 'action', '');

if ($action == 'save')
{   // Save a Menu (New / Edit)    
    $menu_uid = retrieve(POST, 'menu_uid', 0);
//    echo 'UID : ' . $menu_uid . '<hr />';
    
	//Properties of the menu we are creating/editing
	$title = retrieve(POST, 'name', '');
	$url = retrieve(POST, 'url', '');
	$image = retrieve(POST, 'image', '');
	$type = retrieve(POST, 'type', VERTICAL_MENU);    
    
    function build_menu_from_form(&$elements_ids, $root_uid = 0)
    {
        $menu = null;
    	$array_size = count($elements_ids);
    	if ($array_size == 1)
    	{   // If it's a menu, there's only one element
            $menu_element_id = $elements_ids['id'];
    		$menu = new LinksMenuLink(
    			retrieve(POST, 'menu_element_' . $menu_element_id . '_name', ''),
    			retrieve(POST, 'menu_element_' . $menu_element_id . '_url', ''),
    			retrieve(POST, 'menu_element_' . $menu_element_id . '_image', '')
    		);
    		$menu->set_auth(Authorizations::build_auth_array_from_form(AUTH_MENUS, 'menu_element_' . $menu_element_id . '_auth'));
    	}
    	else
    	{
//            $menu_element_id = !empty($elements_ids['id']) || $elements_ids['id'] == 0 ?
//                $elements_ids['id'] : $root_uid;
    	    $menu_element_id = $elements_ids['id'];
//    		echo '<pre>'; print_r($elements_ids); echo '</pre><hr />';
            $menu = new LinksMenu(
    			retrieve(POST, 'menu_element_' . $menu_element_id . '_name', ''),
    			retrieve(POST, 'menu_element_' . $menu_element_id . '_url', ''),
    			retrieve(POST, 'menu_element_' . $menu_element_id . '_image', '')
    		);
    		$menu->set_auth(Authorizations::build_auth_array_from_form(AUTH_MENUS, 'menu_element_' . $menu_element_id . '_auth'));
    		
    		//We unset the id key of the array
    		unset($elements_ids['id']);
    		$array_size = count($elements_ids);
    		for ($i = 0; $i < $array_size; $i++)
	    	{
//	    	    echo 'ID : ' . $menu_element_id . ' - i : ' . $i .  '/' . $array_size . '<br />';
//	    	    echo '<pre>'; print_r($elements_ids[$i]); echo '</pre><hr /><hr/>';
	    		//We build all its children and add it to its father
	    		$menu->add(build_menu_from_form($elements_ids[$i]));
	    	}
    	}
//    	echo '||' . $menu->display() . '||';
    	return $menu;
    }
    
    //We build the array representing the tree
    $result = array();
    parse_str('tree=' . retrieve(POST, 'menu_tree', ''), $result);
    
    //We build the tree
    if (empty($result['tree']))
        die('Error saving the menu!');
        
    //The parsed tree is not absolutely regular, we correct it
    $id_first_menu = preg_replace('`[^=]*=([0-9]+)`isU', '$1', $result['tree']);
//    echo 'AA' . $id_first_menu . 'AA';
    // Correcting the first element
    $result['amp;menu_element_' . $menu_uid . '_list'][0] = array_merge(
    	array('id' => $id_first_menu),
    	$result['amp;menu_element_' . $menu_uid . '_list'][0]
    );
    // Adding the root element
    $menu_tree = array_merge(
    	array('id' => $menu_uid),
    	$result['amp;menu_element_' . $menu_uid . '_list']
    );
//    echo $result['tree'] . '<br />';
//    echo '<pre>'; print_r($menu_tree); echo '</pre>';
    //We build the menu
    $menu = build_menu_from_form($menu_tree, $menu_uid);
    if ($menu == null)
        die('Error saving the menu : null menu exception');
    
    $menu->set_title($title);
    $menu->set_url($url);
    $menu->set_image($image);
    $menu->set_type($type);
    
    //If we edit the menu
    if ($menu_id > 0)
    {   // Edit the Menu
        $menu->id($menu_id);
    }
    
    //Menu enabled?
    $menu->enabled(retrieve(POST, 'enabled', MENU_NOT_ENABLED));
    
    if ($menu->is_enabled())
        $menu->set_block(retrieve(POST, 'location', BLOCK_POSITION__NOT_ENABLED));
        
    $menu->set_auth(Authorizations::build_auth_array_from_form(AUTH_MENUS, "menu_auth"));
//    echo '<pre>'; print_r($menu); echo '</pre>';
//    exit;
    MenuService::save($menu);
   	MenuService::generate_cache();
    redirect(HOST . DIR . '/admin/menus/menus.php#m' . $menu->get_id());
}
elseif ($action == 'delete' && !empty($menu_id))
{   // Delete a Menu
    MenuService::delete($menu_id);
    MenuService::generate_cache();
    
    redirect('menus.php');
}

// Display the Menu administration

include('lateral_menu.php');
lateral_menu();

$tpl = new Template('admin/menus/links.tpl');

$tpl->assign_vars(array(
	'L_REQUIRE_TITLE' => $LANG['require_title'],
	'L_REQUIRE_TEXT' => $LANG['require_text'],
	'L_NAME' => $LANG['name'],
	'L_URL' => $LANG['url'],
	'L_IMAGE' => $LANG['img'],
	'L_STATUS' => $LANG['status'],
	'L_AUTHS' => $LANG['auths'],
	'L_ENABLED' => $LANG['enabled'],
	'L_DISABLED' => $LANG['disabled'],
	'L_ACTIVATION' => $LANG['activation'],
	'L_GUEST' => $LANG['guest'],
	'L_USER' => $LANG['member'],
	'L_MODO' => $LANG['modo'],
	'L_ADMIN' => $LANG['admin'],
	'L_LOCATION' => $LANG['location'],
	'L_ACTION_MENUS' => ($menu_id > 0) ? $LANG['menus_edit'] : $LANG['add'],
	'L_ACTION' => ($menu_id > 0) ? $LANG['update'] : $LANG['submit'],
	'L_RESET' => $LANG['reset'],
    'ACTION' => 'save',
    'L_TYPE' => $LANG['type'],
    'L_CONTENT' => $LANG['contents'],
    'L_AUTHORIZATIONS' => $LANG['authorizations'],
    'L_ADD' => $LANG['add'],
    'J_AUTH_FORM' => to_js_string(Authorizations::generate_select(AUTH_MENUS, array(), array(), 'menu_element_##UID##_auth')),
    'JL_AUTHORIZATIONS' => to_js_string($LANG['authorizations']),
    'JL_PROPERTIES' => to_js_string($LANG['properties']),
    'JL_NAME' => to_js_string($LANG['name']),
    'JL_URL' => to_js_string($LANG['url']),
    'JL_IMAGE' => to_js_string($LANG['img']),
    'JL_DELETE_ELEMENT' => to_js_string($LANG['confirm_delete_element']),
    'JL_MORE' => to_js_string($LANG['more_details']),
    'JL_DELETE' => to_js_string($LANG['delete']),
    'JL_ADD_SUB_ELEMENT' => to_js_string($LANG['add_sub_element']),
    'JL_ADD_SUB_MENU' => to_js_string($LANG['add_sub_menu']),
    'JL_NEW_SUB_ELEMENT' => to_js_string($LANG['new_sub_element']),
    'JL_NEW_SUB_MENU' => to_js_string($LANG['new_sub_menu']),
));

//Localisation possibles.
$block = BLOCK_POSITION__HEADER;
$array_location = array(
    BLOCK_POSITION__HEADER => $LANG['menu_header'],
    BLOCK_POSITION__SUB_HEADER => $LANG['menu_subheader'],
    BLOCK_POSITION__LEFT => $LANG['menu_left'],
    BLOCK_POSITION__TOP_CENTRAL => $LANG['menu_top_central'],
    BLOCK_POSITION__BOTTOM_CENTRAL => $LANG['menu_bottom_central'],
    BLOCK_POSITION__RIGHT => $LANG['menu_right'],
    BLOCK_POSITION__TOP_FOOTER => $LANG['menu_top_footer'],
    BLOCK_POSITION__FOOTER => $LANG['menu_top_footer']
);

$edit_menu_tpl = new Template('admin/menus/menu_edition.tpl');
$edit_menu_tpl->assign_vars(array(
    'L_NAME' => $LANG['name'],
    'L_IMAGE' => $LANG['img'],
    'L_URL' => $LANG['url'],
    'L_PROPERTIES' => $LANG['properties'],
    'L_AUTHORIZATIONS' => $LANG['authorizations'],
    'L_ADD_SUB_ELEMENT' => $LANG['add_sub_element'],
    'L_ADD_SUB_MENU' => $LANG['add_sub_menu'],
    'L_MORE' => $LANG['more_details'],
    'L_DELETE' => $LANG['delete']
));

if ($menu_id > 0)
{
	$menu = MenuService::load($menu_id);
	
    if (!of_class($menu, LINKS_MENU__CLASS))
        redirect('menus.php');	
}
else
{
	$auth = array('r2' => 1, 'r1' => 1, 'm1' => 1);
	$menu = new LinksMenu('Google', 'http://www.google.com', '', VERTICAL_SCROLLING_MENU);
	$menu1 = new LinksMenu('Menu 1', 'http://www.google.com');
	$menu1->set_auth($auth);
	$menu2 = new LinksMenu('Menu 2', 'http://www.google.com');
	$menu3 = new LinksMenu('Menu 3', 'http://www.google.com');
	$menu4 = new LinksMenu('Menu 4', 'http://www.google.com');
	$menu5 = new LinksMenu('Menu 5', 'http://www.google.com');
	$menu6 = new LinksMenu('Menu 6', 'http://www.google.com');
	$menu7 = new LinksMenu('Menu 7', 'http://www.google.com');
	$element1 = new LinksMenuLink('Element 1', '/forum/index.php');
	$element1->set_auth($auth);
	$element2 = new LinksMenuLink('Element 2', 'http://www.google.com');
	
	$aelts0 = array($element1, $element2, $element1, $element1);
	$menu7->add_array($aelts0);
	$aelts1 = array($menu7, $element1, $element1, $element1, $element1);
	$menu6->add_array($aelts1);
	$aelts2 = array($menu6, $element1, $element1, $element2, $element2);
	$aelts3 = array($element1, $element1, $element2, $element2);
	$aelts4 = array($element2, $element2, $element1, $element1);
	$aelts5 = array($element2, $element2, $element2, $element2);
	
	$menu1->add_array($aelts1);
	$menu2->add_array($aelts2);
	$menu3->add_array($aelts3);
	$menu4->add_array($aelts4);
	$menu5->add_array($aelts5);
	
	$amenu = array($menu1, $menu2, $menu3, $menu4, $menu5, $element1, $element2);
	
	$menu->add_array($amenu);
	echo $menu->display();
}

{
	$block = $menu->get_block();
	$tpl->assign_vars(array(
		'IDMENU' => $menu_id,
		'AUTH_MENUS' => Authorizations::generate_select(AUTH_MENUS, $menu->get_auth()),
	    'C_ENABLED' => $menu->is_enabled(),
		'MENU_ID' => $menu->get_id(),
		'MENU_TREE' => $menu->display($edit_menu_tpl, LINKS_MENU_ELEMENT__FULL_DISPLAYING),
		'MENU_NAME' => $menu->get_title(),
		'MENU_URL' => $menu->get_url(false),
		'MENU_IMG' => $menu->get_image(false),
	    'ID' => $menu->get_uid()
	));
}

foreach (LinksMenu::get_menu_types_list() as $type_name)
{
	$tpl->assign_block_vars('type', array(
		'NAME' => $type_name,
		'L_NAME' => $LANG[$type_name . '_menu'],
		'SELECTED' => $menu->get_type() == $type_name ? ' selected="selected"' : ''
	));
}

$locations = '';
foreach ($array_location as $key => $name)
    $locations .= '<option value="' . $key . '" ' . (($block == $key) ? 'selected="selected"' : '') . '>' . $name . '</option>';

$tpl->assign_vars(array(
    'LOCATIONS' => $locations,
    'ID_MAX' => get_uid()
));
$tpl->parse();

require_once(PATH_TO_ROOT . '/admin/admin_footer.php');

?>