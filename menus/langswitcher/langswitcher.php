<?php
/*##################################################
 *                               langswitcher.php
 *                            -------------------
 *   begin                : November 16, 2008
 *   copyright            : (C) 2008 Viarre R�gis
 *   email                : crowkait@phpboost.com
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

function menu_langswitcher_langswitcher($position, $block)
{
	global $CONFIG, $LANGS_CONFIG, $User, $LANG, $Session;

	load_menu_lang('langswitcher');
	
	$switchlang = !empty($_GET['switchlang']) ? urldecode($_GET['switchlang']) : '';
    if (!empty($switchlang))
    {
        if ($User->check_level(MEMBER_LEVEL))
        {
            $Session->csrf_get_protect();
        }
        
    	if (preg_match('`[ a-z0-9_-]{3,20}`i', $switchlang) && strpos($switchlang, '\'') === false)
    	{
    		$User->update_user_lang($switchlang); //Mise � jour du th�me du membre.
    		if (QUERY_STRING != '')
    		{
				$query_string = preg_replace('`token=[^&]+`', '', QUERY_STRING);
				$query_string = preg_replace('`&switchlang=[^&]+`', '', $query_string);
				AppContext::get_response()->redirect(trim(HOST . SCRIPT . (!empty($query_string) ? '?' . $query_string : '')));
    		}
    		else
    			AppContext::get_response()->redirect(HOST . SCRIPT);
    	}
    }
    
    $tpl = new FileTemplate('menus/langswitcher/langswitcher.tpl');
    
    MenuService::assign_positions_conditions($tpl, $block);
    
    $array_js_identifier = '';
    $ulang = get_ulang();
    foreach($LANGS_CONFIG as $lang => $array_info)
    {
    	if ($User->check_level($array_info['secure']))
    	{
    		$info_lang = load_ini_file(PATH_TO_ROOT . '/lang/', $lang);
    		
			$selected = ($ulang == $lang) ? ' selected="selected"' : '';
    		$tpl->assign_block_vars('langs', array(
    			'NAME' => $info_lang['name'],
    			'IDNAME' => $lang,
    			'SELECTED' => $selected
    		));
    	}
    }
    
    $lang_identifier = str_replace('en', 'uk', $LANG['xml_lang']);
    $tpl->assign_vars(array(
    	'DEFAULT_LANG' => $CONFIG['lang'],
    	'IMG_LANG_IDENTIFIER' => TPL_PATH_TO_ROOT . '/images/stats/countries/' . $lang_identifier . '.png',
    	'L_SWITCH_LANG' => $LANG['switch_lang'],
    	'L_DEFAULT_LANG' => $LANG['default_lang'],
    	'L_SUBMIT' => $LANG['submit']
    ));
    
    return $tpl->to_string();
}

?>