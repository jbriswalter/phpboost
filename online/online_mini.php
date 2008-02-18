<?php
/*##################################################
 *                              online_mini.php
 *                            -------------------
 *   begin                : July 20, 2005
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
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
###################################################*/

if( defined('PHP_BOOST') !== true)	exit;

if( strpos(SCRIPT, '/online/online.php') === false )
{
	//Chargement de la langue du module.
	@load_module_lang('online', $CONFIG['lang']);
	$Cache->Load_file('online');
	
	$Template->Set_filenames(array(
		'online' => '../templates/' . $CONFIG['theme'] . '/online/online_mini.tpl'
	));

	//On compte les visiteurs en ligne dans la bdd, en prenant en compte le temps max de connexion.
	list($count_visit, $count_member, $count_modo, $count_admin) = array(0, 0, 0, 0);  

	$i = 0;
	$result = $Sql->Query_while("SELECT s.user_id, s.level, s.session_time, m.login
	FROM ".PREFIX."sessions s 
	LEFT JOIN ".PREFIX."member m ON m.user_id = s.user_id 
	WHERE s.session_time > '" . (time() - $CONFIG['site_session_invit']) . "'
	ORDER BY " . $CONFIG_ONLINE['display_order_online'], __LINE__, __FILE__); //4 Membres enregistr�s max.
	while( $row = $Sql->Sql_fetch_assoc($result) )
	{
		if( $i < $CONFIG_ONLINE['online_displayed'] )
		{
			switch($row['level']) //Coloration du membre suivant son level d'autorisation. 
			{ 		
				case '0':
				$status = 'member';
				break;
				
				case '1': 
				$status = 'modo';
				break;
				
				case '2': 
				$status = 'admin';
				break;
				
				default:
				$status = 'member';
			} 
			
			//Visiteurs non pris en compte.
			if( $row['level'] !== '-1' )
			{
				$Template->Assign_block_vars('online', array(
					'MEMBER' => '<a href="../member/member' . transid('.php?id=' . $row['user_id'], '-' . $row['user_id'] . '.php') . '" class="' . $status . '">' . wordwrap_html($row['login'], 19) . '</a><br />'	
				));
				$i++;
			}		 
		}
		
		switch($row['level'])
		{
			case '-1':
			$count_visit++;
			break;
			case '0':
			$count_member++;
			break;
			case '1':
			$count_modo++;
			break;
			case '2':
			$count_admin++;
			break;
		}
	}
	$Sql->Close($result);


	$count_visit = (empty($count_visit) && empty($count_member) && empty($count_modo) && empty($count_admin)) ? '1' : $count_visit;

	$total = $count_visit + $count_member + $count_modo + $count_admin;
	$total_member = $count_member + $count_modo + $count_admin;

	$member_online = $LANG['member_s'] . ' ' . strtolower($LANG['online']);
	$more = '<br /><a href="../online/online.php' . SID . '" title="' . $member_online . '">' . $member_online . '</a><br />';
	$more = ($total_member > $CONFIG_ONLINE['online_displayed']) ? $more : ''; //Plus de 4 membres connect�s.

	$l_guest = ($count_visit > 1) ? $LANG['guest_s'] : $LANG['guest'];
	$l_member = ($count_member > 1) ? $LANG['member_s'] : $LANG['member'];
	$l_modo = ($count_modo > 1) ? $LANG['modo_s'] : $LANG['modo'];
	$l_admin = ($count_admin > 1) ? $LANG['admin_s'] : $LANG['admin'];

	$Template->Assign_vars(array(
		'VISIT' => $count_visit,
		'MEMBER' => $count_member,
		'MODO' => $count_modo,
		'ADMIN' => $count_admin,
		'MORE' => $more,	
		'TOTAL' => $total,
		'L_VISITOR' => $l_guest,
		'L_MEMBER' => $l_member,
		'L_MODO' => $l_modo,
		'L_ADMIN' => $l_admin,
		'L_ONLINE' => $LANG['online'],
		'L_TOTAL' => $LANG['total']
	));

	$Template->Pparse('online'); 
}	

?>