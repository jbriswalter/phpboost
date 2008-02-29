<?php
/*##################################################
 *                               web.php
 *                            -------------------
 *   begin                : July 28, 2005
 *   copyright          : (C) 2005 Viarre R�gis
 *   email                : crowkait@phpboost.com
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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
###################################################*/

require_once('../includes/begin.php'); 
require_once('../web/web_begin.php'); 
require_once('../includes/header.php'); 

if( !empty($idweb) && !empty($CAT_WEB[$idcat]['name']) && !empty($idcat) ) //Contenu du lien.
{
	$Template->Set_filenames(array('web' => '../templates/' . $CONFIG['theme'] . '/web/web.tpl'));
	
	if( !$Member->Check_level($CAT_WEB[$idcat]['secure']) )
		$Errorh->Error_handler('e_auth', E_USER_REDIRECT); 
	if( empty($web['id']) )
		$Errorh->Error_handler('e_unexist_link_web', E_USER_REDIRECT);
		
	if( $Member->Check_level(ADMIN_LEVEL) )
	{
		$java = "<script language='JavaScript' type='text/javascript'>
		<!--
		function Confirm() {
		return confirm('" . $LANG['delete_link'] . "');
		}
		-->
		</script>";
		
		$edit = '&nbsp;&nbsp;<a href="../web/admin_web' . transid('.php?id=' . $web['id']) . '" title="' . $LANG['edit'] . '"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/edit.png" class="valign_middle" /></a>';
		$del = '&nbsp;&nbsp;<a href="../web/admin_web.php?delete=1&amp;id=' . $web['id'] . '" title="' . $LANG['delete'] . '" onClick="javascript:return Confirm();"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/delete.png" class="valign_middle" /></a>';
	}
	else
	{
		$edit = '';
		$del = '';
		$java = '';
	}

	$Template->Assign_vars(array(
		'JAVA' => $java,
		'EDIT' => $edit,
		'DEL' => $del
	));
		
	//Notation
	if( $Member->Get_attribute('user_id') !== -1 ) //Utilisateur connect�
		$link_note = '<a class="com" style="font-size:10px;" href="web' . transid('.php?note=' . $web['id'] . '&amp;id=' . $idweb . '&amp;cat=' . $idcat, '-' . $idcat . '-' . $idweb . '-0-0-' . $web['id'] . '.php?note=' . $web['id']) . '#note" title="' . $LANG['note'] . '">' . $LANG['note'] . '</a>';
	else
		$link_note = $LANG['note'];
	
	$note = ($web['nbrnote'] > 0 ) ? $web['note'] : '<em>' . $LANG['no_note'] . '</em>';

	//Commentaires
	$link_pop = "<a class=\"com\" href=\"#\" onclick=\"popup('" . HOST . DIR . transid("/includes/com.php?i=" . $idweb . "web") . "', 'web');\">";
	$link_current = '<a class="com" href="' . HOST . DIR . '/web/web' . transid('.php?cat=' . $idcat . '&amp;id=' . $idweb . '&amp;i=0', '-' . $idcat . '-' . $idweb . '.php?i=0') . '#web">';	
	$link = ($CONFIG['com_popup'] == '0') ? $link_current : $link_pop;
	
	$com_true = ($web['nbr_com'] > 1) ? $LANG['com_s'] : $LANG['com'];
	$com_false = $LANG['post_com'] . '</a>';
	$l_com = !empty($web['nbr_com']) ? $com_true . ' (' . $web['nbr_com'] . ')</a>' : $com_false;
	
	$Template->Assign_vars(array(
		'C_DISPLAY_WEB' => true,
		'MODULE_DATA_PATH' => $Template->Module_data_path('web'),
		'IDWEB' => $web['id'],		
		'NAME' => $web['title'],
		'CONTENTS' => $web['contents'],
		'URL' => $web['url'],
		'CAT' => $CAT_WEB[$idcat]['name'],
		'DATE' => gmdate_format('date_format_short', $web['timestamp']),
		'COMPT' => $web['compt'],
		'THEME' => $CONFIG['theme'],
		'LANG' => $CONFIG['lang'],
		'COM' => $link . $l_com,
		'NOTE' => $note,
		'U_WEB_CAT' => transid('.php?cat=' . $idcat, '-' . $idcat . '.php'),
		'L_NOTE' => $link_note,
		'L_DESC' => $LANG['description'],
		'L_CAT' => $LANG['category'],
		'L_DATE' => $LANG['date'],
		'L_TIMES' => $LANG['n_time'],
		'L_VIEWS' => $LANG['views']
	));

	//Affichage et gestion de la notation
	if( !empty($get_note) && !empty($CAT_WEB[$idcat]['name']) )
	{
		$Template->Assign_vars(array(
			'L_ACTUAL_NOTE' => $LANG['actual_note'],
			'L_VOTE' => $LANG['vote'],
			'L_NOTE' => $LANG['note']
		));
				
		if( $Member->Check_level(MEMBER_LEVEL) ) //Utilisateur connect�.
		{
			if( !empty($_POST['valid_note']) )
			{
				$note = numeric($_POST['note']);
				
				//Echelle de notation.
				$check_note = ( ($note >= 0) && ($note <= $CONFIG_WEB['note_max']) ) ? true : false;				
				$users_note = $Sql->Query("SELECT users_note FROM ".PREFIX."web WHERE idcat = '" . $idcat . "' AND id = '" . $get_note . "'", __LINE__, __FILE__);
				
				$array_users_note = explode('/', $users_note);
				if( !in_array($Member->Get_attribute('user_id'), $array_users_note) && $Member->Get_attribute('user_id') != '' && ($check_note === true) )
				{
					$row_note = $Sql->Query_array('web', 'users_note', 'nbrnote', 'note', "WHERE id = '" . $get_note . "'", __LINE__, __FILE__);
					$note = ( ($row_note['note'] * $row_note['nbrnote']) + $note ) / ($row_note['nbrnote'] + 1);
					
					$row_note['nbrnote']++;
					
					$users_note = !empty($row_note['users_note']) ? $row_note['users_note'] . '/' . $Member->Get_attribute('user_id') : $Member->Get_attribute('user_id'); //On ajoute l'id de l'utilisateur.
					
					$Sql->Query_inject("UPDATE ".PREFIX."web SET note = '" . $note . "', nbrnote = '" . $row_note['nbrnote'] . "', 
					users_note = '" . $users_note . "' WHERE id = '" . $get_note . "' AND idcat = '" . $idcat . "'", __LINE__, __FILE__);
					
					//Success.
					redirect(HOST . DIR . '/web/web' . transid('.php?cat=' . $idcat . '&id=' . $get_note, '-' . $idcat . '-' . $get_note . '.php', '&'));
				}
				else
					redirect(HOST . DIR . '/web/web' . transid('.php?cat=' . $idcat . '&id=' . $get_note, '-' . $idcat . '-' . $get_note . '.php', '&'));
			}
			elseif( $Member->Get_attribute('user_id') != '' )
			{
				$row = $Sql->Query_array('web', 'users_note', 'nbrnote', 'note', "WHERE idcat = '" . $idcat . "' AND id = '" . $get_note . "'", __LINE__, __FILE__);
				
				$array_users_note = explode('/', $row['users_note']);
				$select = '';
				if( in_array($Member->Get_attribute('user_id'), $array_users_note) ) //D�j� vot�
					$select .= '<option value="-1">' . $LANG['already_vote'] . '</option>';
				else 
				{
					//G�n�ration de l'�chelle de notation.
					for( $i = -1; $i <= $CONFIG_WEB['note_max']; $i++)
					{
						if( $i == -1 )
							$select = '<option value="-1">' . $LANG['note'] . '</option>';
						else
							$select .= '<option value="' . $i . '">' . $i . '</option>';
					}
				}
				
				$Template->Assign_vars(array(
					'C_DISPLAY_WEB_NOTE' => true,
					'NOTE' => ($row['nbrnote'] > 0) ? $row['note'] : '<em>' . $LANG['no_note'] . '</em>',
					'SELECT' => $select,
					'U_WEB_ACTION_NOTE' => transid('.php?note=' . $get_note . '&amp;id=' . $get_note . '&amp;cat=' . $idcat, '-' . $idcat . '-' . $get_note . '.php?note=' . $get_note)
				));
			}	
			else
				$Errorh->Error_handler('e_auth', E_USER_REDIRECT); 
		}
		else 
			$Errorh->Error_handler('e_auth', E_USER_REDIRECT); 
	}
	
	//Affichage commentaires.
	if( isset($_GET['i']) )
	{
		$_com_vars = 'web.php?cat=' . $idcat . '&amp;id=' . $idweb . '&amp;i=%d';
		$_com_vars_e = 'web.php?cat=' . $idcat . '&id=' . $idweb . '&i=1';
		$_com_vars_r = 'web-' . $idcat . '-' . $idweb . '.php?i=%d%s';
		$_com_idprov = $idweb;
		$_com_script = 'web';
		include_once('../includes/com.php');
	}	

	$Template->Pparse('web');
}
elseif( !empty($idcat) && empty($idweb) ) //Cat�gories.
{
	$Template->Set_filenames(array('web' => '../templates/' . $CONFIG['theme'] . '/web/web.tpl'));
	
	if( !$Member->Check_level($CAT_WEB[$idcat]['secure']) )
		$Errorh->Error_handler('e_auth', E_USER_REDIRECT); 
	
	$nbr_web = $Sql->Query("SELECT COUNT(*) as compt 
	FROM ".PREFIX."web 
	WHERE aprob = 1 AND idcat = '" . $idcat . "'", __LINE__, __FILE__);
	
	$Template->Assign_vars(array(
		'C_WEB_LINK' => true,
		'CAT_NAME' => $CAT_WEB[$idcat]['name'],		
		'NO_CAT' => ($nbr_web == 0) ? $LANG['none_link'] : '',
		'MAX_NOTE' => $CONFIG_WEB['note_max'],
		'L_LINK' => $LANG['link'],
		'L_DATE' => $LANG['date'],
		'L_VIEW' => $LANG['views'],
		'L_NOTE' => $LANG['note'],
		'L_COM' => $LANG['com'],
		'U_WEB_ALPHA_TOP' => transid('.php?sort=alpha&amp;mode=desc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=alpha&amp;mode=desc'),
		'U_WEB_ALPHA_BOTTOM' => transid('.php?sort=alpha&amp;mode=asc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=alpha&amp;mode=asc'),
		'U_WEB_DATE_TOP' => transid('.php?sort=date&amp;mode=desc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=date&amp;mode=desc'),
		'U_WEB_DATE_BOTTOM' => transid('.php?sort=date&amp;mode=asc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=date&amp;mode=asc'),
		'U_WEB_VIEW_TOP' => transid('.php?sort=view&amp;mode=desc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=view&amp;mode=desc'),
		'U_WEB_VIEW_BOTTOM' => transid('.php?sort=view&amp;mode=asc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=view&amp;mode=asc'),
		'U_WEB_NOTE_TOP' => transid('.php?sort=note&amp;mode=desc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=note&amp;mode=desc'),
		'U_WEB_NOTE_BOTTOM' => transid('.php?sort=note&amp;mode=asc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=note&amp;mode=asc'),
		'U_WEB_COM_TOP' => transid('.php?sort=com&amp;mode=desc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=com&amp;mode=desc'),
		'U_WEB_COM_BOTTOM' => transid('.php?sort=com&amp;mode=asc&amp;cat=' . $idcat, '-' . $idcat . '.php?sort=com&amp;mode=asc')
	));		
	
	$get_sort = !empty($_GET['sort']) ? trim($_GET['sort']) : '';	
	switch($get_sort)
	{
		case 'alpha' : 
		$sort = 'title';
		break;		
		case 'date' : 
		$sort = 'timestamp';
		break;		
		case 'view' : 
		$sort = 'compt';
		break;		
		case 'note' :
		$sort = 'note/' . $CONFIG_WEB['note_max'];
		break;		
		case 'com' :
		$sort = 'nbr_com';
		break;	
		default :
		$sort = 'timestamp';
	}
	
	$get_mode = !empty($_GET['mode']) ? trim($_GET['mode']) : '';	
	$mode = ($get_mode == 'asc' || $get_mode == 'desc') ? strtoupper(trim($_GET['mode'])) : 'DESC';	
	$unget = (!empty($get_sort) && !empty($mode)) ? '?sort=' . $get_sort . '&amp;mode=' . $get_mode : '';

	//On cr�e une pagination si le nombre de lien est trop important.
	include_once('../includes/pagination.class.php'); 
	$Pagination = new Pagination();
		
	$Template->Assign_vars(array(
		'PAGINATION' => $Pagination->Display_pagination('web' . transid('.php' . (!empty($unget) ? $unget . '&amp;' : '?') . 'cat=' . $idcat . '&amp;p=%d', '-' . $idcat . '-0-%d.php' . (!empty($unget) ? '?' . $unget : '')), $nbr_web, 'p', $CONFIG_WEB['nbr_web_max'], 3)
	));

	$result = $Sql->Query_while("SELECT id, title, timestamp, compt, note, nbrnote, nbr_com
	FROM ".PREFIX."web
	WHERE aprob = 1 AND idcat = '" . $idcat . "'
	ORDER BY " . $sort . " " . $mode . 
	$Sql->Sql_limit($Pagination->First_msg($CONFIG_WEB['nbr_web_max'], 'p'), $CONFIG_WEB['nbr_web_max']), __LINE__, __FILE__);
	while( $row = $Sql->Sql_fetch_assoc($result) )
	{
		//On reccourci le lien si il est trop long.
		$row['title'] = (strlen($row['title']) > 45 ) ? substr(html_entity_decode($row['title']), 0, 45) . '...' : $row['title'];
		
		//Commentaires
		$link_pop = "<a href=\"#\" onclick=\"popup('" . HOST . DIR . transid("/includes/com.php?i=" . $row['id'] . "web") . "', 'web');\">";
		$link_current = '<a href="' . HOST . DIR . '/web/web' . transid('.php?cat=' . $idcat . '&amp;id=' . $row['id'] . '&amp;i=0', '-' . $idcat . '-' . $row['id'] . '.php?i=0') . '#web">';	
		$link = ($CONFIG['com_popup'] == '0') ? $link_current : $link_pop;
	
		$Template->Assign_block_vars('web', array(			
			'NAME' => $row['title'],
			'CAT' => $CAT_WEB[$idcat]['name'],
			'DATE' => gmdate_format('date_format_short', $row['timestamp']),
			'COMPT' => $row['compt'],
			'NOTE' => ($row['nbrnote'] > 0) ? $row['note'] . '/' . $CONFIG_WEB['note_max'] : '<em>' . $LANG['no_note'] . '</em>',
			'COM' => $link . $row['nbr_com'] . '</a>',
			'U_WEB_LINK' => transid('.php?cat=' . $idcat . '&amp;id=' . $row['id'], '-' .  $idcat . '-' . $row['id'] . '.php')
		));
	}
	$Sql->Close($result);
	
	$Template->Pparse('web');
}
else
{
	$Template->Set_filenames(array('web' => '../templates/' . $CONFIG['theme'] . '/web/web.tpl'));
	
	$total_link = $Sql->Query("SELECT COUNT(*) FROM ".PREFIX."web_cat wc
	LEFT JOIN ".PREFIX."web w ON w.idcat = wc.id
	WHERE w.aprob = 1 AND wc.aprob = 1 AND wc.secure <= '" . $Member->Get_attribute('level') . "'", __LINE__, __FILE__);
	$total_cat = $Sql->Query("SELECT COUNT(*) as compt FROM ".PREFIX."web_cat WHERE aprob = 1 AND secure <= '" . $Member->Get_attribute('level') . "'", __LINE__, __FILE__);
	
	$edit = '';
	if( $Member->Check_level(ADMIN_LEVEL) )
		$edit = '&nbsp;&nbsp;<a href="admin_web_cat.php' .  SID . '" title=""><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/edit.png" class="valign_middle" /></a>';

	//On cr�e une pagination si le nombre de cat�gories est trop important.
	include_once('../includes/pagination.class.php'); 
	$Pagination = new Pagination();

	$CONFIG_WEB['nbr_column'] = ($total_cat > $CONFIG_WEB['nbr_column']) ? $CONFIG_WEB['nbr_column'] : $total_cat;
	$CONFIG_WEB['nbr_column'] = !empty($CONFIG_WEB['nbr_column']) ? $CONFIG_WEB['nbr_column'] : 1;
	
	$Template->Assign_vars(array(
		'C_WEB_CAT' => true,
		'PAGINATION' => $Pagination->Display_pagination('web' . transid('.php?p=%d', '-0-0-%d.php'), $total_cat, 'p', $CONFIG_WEB['nbr_cat_max'], 3),
		'EDIT' => $edit,
		'TOTAL_FILE' => $total_link,
		'L_CATEGORIES' => $LANG['categories'],
		'L_PROPOSE_LINK' => $LANG['propose_link'],
		'L_HOW_LINK' => $LANG['how_link'],
		'U_WEB_ADD' => transid('.php?web=true')
	));
	
	//Cat�gorie disponibles	
	$column_width = floor(100/$CONFIG_WEB['nbr_column']);
	$result = $Sql->Query_while(
	"SELECT aw.id, aw.name, aw.contents, aw.icon, COUNT(w.id) as count
	FROM ".PREFIX."web_cat aw
	LEFT JOIN ".PREFIX."web w ON w.idcat = aw.id AND w.aprob = 1
	WHERE aw.aprob = 1 AND aw.secure <= '" . $Member->Get_attribute('level') . "'
	GROUP BY aw.id
	ORDER BY aw.class
	" . $Sql->Sql_limit($Pagination->First_msg($CONFIG_WEB['nbr_cat_max'], 'p'), $CONFIG_WEB['nbr_cat_max']), __LINE__, __FILE__);
	while( $row = $Sql->Sql_fetch_assoc($result) )
	{
		$Template->Assign_block_vars('cat_list', array(
			'WIDTH' => $column_width,
			'TOTAL' => $row['count'],
			'CAT' => $row['name'],
			'CONTENTS' => $row['contents'],	
			'U_IMG_CAT' => !empty($row['icon']) ? '<a href="../web/web' . transid('.php?cat=' . $row['id'], '-' . $row['id'] . '.php') . '"><img src="' . $row['icon'] . '" alt="" /></a><br />' : '',
			'U_WEB_CAT' => transid('.php?cat=' . $row['id'], '-' . $row['id'] . '.php')
		));
	}
	$Sql->Close($result);
	
	$Template->Pparse('web');
}
			
require_once('../includes/footer.php'); 

?>