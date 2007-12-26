<?php
/*##################################################
 *                                pm.php
 *                            -------------------
 *   begin                : July 12, 2006
 *   copyright          : (C) 2006 Viarre R�gis
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

include_once('../includes/begin.php'); 
define('TITLE', $LANG['title_pm']);
$speed_bar = array(
	$LANG['member_area'] => transid('member.php?id=' . $session->data['user_id'] . '&amp;view=1', 'member-' . $session->data['user_id'] . '.php?view=1'), $LANG['title_pm'] => transid('pm.php')
);
include_once('../includes/header.php'); 

//Interdit aux non membres.
if( !$session->check_auth($session->data, 0) )
{
	$errorh->error_handler('e_auth', E_USER_REDIRECT); 
	exit;
}

include_once('../includes/pm.class.php');
$privatemsg = new Privatemsg();

$pm_get = !empty($_GET['pm']) ? numeric($_GET['pm']) : '';
$pm_id_get = !empty($_GET['id']) ? numeric($_GET['id']) : '';
$pm_del_convers = !empty($_GET['del_convers']) ? trim($_GET['del_convers']) : '';
$quote_get = !empty($_GET['quote']) ? numeric($_GET['quote']) : '';	
$page = !empty($_GET['p']) ? numeric($_GET['p']) : 1;
$post = !empty($_GET['post']) ? trim($_GET['post']) : '';
$pm_edit = !empty($_GET['edit']) ? numeric($_GET['edit']) : '';
$pm_del = !empty($_GET['del']) ? numeric($_GET['del']) : '';

//Marque les messages priv�s comme lus
if( !empty($_GET['read']) )
{
	$visibility = $session->check_auth($session->data, 1) || $groups->check_auth($groups->user_groups_auth, PM_NO_LIMIT) ? '' : 'AND pm.visible = 1';
	
	$result = $sql->query_while("SELECT pm.last_msg_id, pm.user_view_pm
	FROM ".PREFIX."pm_topic AS pm
	LEFT JOIN ".PREFIX."pm_msg AS msg ON msg.idconvers = pm.id AND msg.id = pm.last_msg_id
	WHERE " . $session->data['user_id'] . " IN (pm.user_id, pm.user_id_dest) AND last_user_id != '" . $session->data['user_id'] . "' AND msg.view_status = 0 ".  $visibility, __LINE__, __FILE__);
	while( $row = $sql->sql_fetch_assoc($result) )
	{
		$sql->query_inject("UPDATE ".PREFIX."pm_msg SET view_status = 1 WHERE id = '" . $row['last_msg_id'] . "'", __LINE__, __FILE__); 		
	}
	$sql->close($result);
	
	$nbr_waiting_pm = $sql->query("SELECT COUNT(*) FROM ".PREFIX."pm_topic WHERE user_id_dest = '" . $session->data['user_id'] . "' AND visible = 0", __LINE__, __FILE__);
	$sql->query_inject("UPDATE ".PREFIX."member SET user_pm = '" . $nbr_waiting_pm . "' WHERE user_id = '" . $session->data['user_id'] . "'", __LINE__, __FILE__); 
	
	header('Location:' . HOST . DIR . transid('/member/pm.php', '', '&'));
	exit;
}

if( !empty($_POST['convers']) && empty($pm_edit) && empty($pm_del) ) //Envoi de conversation.
{
	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
	$contents = !empty($_POST['contents']) ? trim($_POST['contents']) : '';
	$login = !empty($_POST['login']) ? securit($_POST['login']) : '';
	
	//V�rification de la boite de l'exp�diteur.
	if( $privatemsg->get_total_convers_pm($session->data['user_id']) >= $CONFIG['pm_max'] && (!$session->check_auth($session->data, 1) && !$groups->check_auth($groups->user_groups_auth, PM_NO_LIMIT)) )
	{
		//Bo�te de l'exp�diteur pleine.
		header('Location:' . HOST . DIR . '/member/pm' . transid('.php?post=1&error=e_pm_full_post', '', '&') . '#errorh');
		exit;	
	}	
				
	if( !empty($title) && !empty($contents) && !empty($login) )
	{	
		//On essaye de r�cup�rer le user_id, si le membre n'a pas cliqu� une fois la recherche AJAX termin�e.
		$user_id_dest = $sql->query("SELECT user_id FROM ".PREFIX."member WHERE login = '" . $login . "'", __LINE__, __FILE__); 
		if( !empty($user_id_dest) && $user_id_dest != $session->data['user_id'] ) 
		{
			//Envoi de la conversation, v�rification de la boite si pleine => erreur
			$privatemsg->send_pm($user_id_dest, $title, $contents, $session->data['user_id']);
			//Succ�s redirection vers la conversation.
			header('location:' . HOST . DIR . '/member/pm' . transid('.php?id=' . $privatemsg->pm_convers_id, '-0-' . $privatemsg->pm_convers_id . '.php', '&') . '#m' . $privatemsg->pm_msg_id);
			exit;
		}
		else
		{
			//Destinataire non trouv�.
			header('Location:' . HOST . DIR . '/member/pm' . transid('.php?post=1&error=e_unexist_user', '', '&') . '#errorh');
			exit;	
		}
	}
	else
	{
		//Champs manquants.
		header('Location:' . HOST . DIR . '/member/pm' . transid('.php?post=1&error=e_incomplete', '', '&') . '#errorh');
		exit;
	}
}
elseif( !empty($post) || (!empty($pm_get) && $pm_get != $session->data['user_id']) && $pm_get > '0' ) //Interface pour poster la conversation.
{
	$template->set_filenames(array(
		'pm' => '../templates/' . $CONFIG['theme'] . '/pm.tpl'
	));

	$template->assign_vars(array(
		'LANG' => $CONFIG['lang'],
		'THEME' => $CONFIG['theme'],
		'L_REQUIRE_RECIPIENT' => $LANG['require_recipient'],
		'L_REQUIRE_MESSAGE' => $LANG['require_text'],
		'L_REQUIRE_TITLE' => $LANG['require_title'],
		'L_REQUIRE' => $LANG['require'],
		'L_PRIVATE_MESSAGE' => $LANG['private_message'],
		'L_POST_NEW_CONVERS' => $LANG['post_new_convers'],
		'L_RECIPIENT' => $LANG['recipient'],
		'L_SEARCH' => $LANG['search'],
		'L_TITLE' => $LANG['title'],
		'L_MESSAGE' => $LANG['message'],
		'L_SUBMIT' => $LANG['submit'],
		'L_PREVIEW' => $LANG['preview'],
		'L_RESET' => $LANG['reset']
	));
	
	$login = !empty($pm_get) ? $sql->query("SELECT login FROM ".PREFIX."member WHERE user_id = '" . $pm_get . "'", __LINE__, __FILE__) : '';
	
	$template->assign_block_vars('post_convers', array(
		'U_ACTION_CONVERS' => transid('.php'),
		'U_PM_BOX' => '<a href="pm.php' . SID . '">' . $LANG['pm_box'] . '</a>',
		'U_MEMBER_VIEW' => '<a href="' . transid('member.php?id=' . $session->data['user_id'] . '&amp;view=1', 'member-' . $session->data['user_id'] . '.php?view=1') . '">' . $LANG['member_area'] . '</a>',
		'LOGIN' => $login
	));
	
	$nbr_pm = $privatemsg->get_total_convers_pm($session->data['user_id']);
	if( !$session->check_auth($session->data, 1) && !$groups->check_auth($groups->user_groups_auth, PM_NO_LIMIT) && $nbr_pm >= $CONFIG['pm_max'] ) 
	{
		$errorh->error_handler($LANG['e_pm_full_post'], E_USER_WARNING, NO_LINE_ERROR, NO_FILE_ERROR, 'post_convers.');
	}	
	else
	{
		//Gestion des erreurs
		$get_error = !empty($_GET['error']) ? trim($_GET['error']) : '';
		switch($get_error)
		{
			case 'e_unexist_user':
				$errstr = $LANG['e_unexist_user'];
				$type = E_USER_WARNING;
				break;
			case 'e_pm_full_post':
				$errstr = $LANG['e_pm_full_post'];
				$type = E_USER_WARNING;
				break;
			case 'e_incomplete':
				$errstr = $LANG['e_incomplete'];
				$type = E_USER_NOTICE;				
			break;
			default:
				$errstr = '';
		}
		if( !empty($errstr) )
			$errorh->error_handler($errstr, $type, NO_LINE_ERROR, NO_FILE_ERROR, 'post_convers.');
	}
	
	$template->assign_block_vars('post_convers.user_id_dest', array(
	));
	
	include_once('../includes/bbcode.php');
	$template->assign_var_from_handle('BBCODE', 'bbcode');
	
	$template->pparse('pm');
}
elseif( !empty($_POST['prw_convers']) && empty($mp_edit) ) //Pr�visualisation de la conversation.
{
	$template->set_filenames(array(
		'pm' => '../templates/' . $CONFIG['theme'] . '/pm.tpl'
	));
	
	$template->assign_vars(array(		
		'LANG' => $CONFIG['lang'],
		'THEME' => $CONFIG['theme'],
		'L_REQUIRE_MESSAGE' => $LANG['require_text'],
		'L_REQUIRE_TITLE' => $LANG['require_title'],
		'L_REQUIRE' => $LANG['require'],
		'L_PRIVATE_MESSAGE' => $LANG['private_message'],
		'L_POST_NEW_CONVERS' => $LANG['post_new_convers'],
		'L_RECIPIENT' => $LANG['recipient'],
		'L_SEARCH' => $LANG['search'],
		'L_TITLE' => $LANG['title'],
		'L_MESSAGE' => $LANG['message'],
		'L_SUBMIT' => $LANG['submit'],
		'L_PREVIEW' => $LANG['preview'],
		'L_RESET' => $LANG['reset']
	));
	
	$template->assign_block_vars('post_convers', array(
		'U_ACTION_CONVERS' => transid('.php'),
		'U_PM_BOX' => '<a href="pm.php' . SID . '">' . $LANG['pm_box'] . '</a>',
		'U_MEMBER_VIEW' => '<a href="' . transid('member.php?id=' . $session->data['user_id'] . '&amp;view=1', 'member-' . $session->data['user_id'] . '.php?view=1') . '">' . $LANG['member_area'] . '</a>',
		'LOGIN' => !empty($_POST['login']) ? stripslashes($_POST['login']) : '',
		'TITLE' => !empty($_POST['title']) ? stripslashes($_POST['title']) : '',
		'CONTENTS' => !empty($_POST['contents']) ? stripslashes($_POST['contents']) : ''
	));
	
	$template->assign_block_vars('post_convers.show_convers', array(
		'DATE' => date('d/m/Y ' . $LANG['at'] . ' H\Hi', time()),
		'CONTENTS' => second_parse(stripslashes(parse($_POST['contents'])))
	));
	
	$template->assign_block_vars('post_convers.user_id_dest', array(
	));
	
	include_once('../includes/bbcode.php');
	$template->assign_var_from_handle('BBCODE', 'bbcode');
	
	$template->pparse('pm');
}
elseif( !empty($_POST['prw']) && empty($pm_edit) && empty($pm_del) ) //Pr�visualisation du message.
{
	//On r�cup�re les info de la conversation.
	$convers_title = $sql->query("SELECT title FROM ".PREFIX."pm_topic WHERE id = '" . $pm_id_get . "'", __LINE__, __FILE__);
	
	$template->set_filenames(array(
		'pm' => '../templates/' . $CONFIG['theme'] . '/pm.tpl'
	));

	$template->assign_vars(array(
		'LANG' => $CONFIG['lang'],
		'L_REQUIRE_MESSAGE' => $LANG['require_text'],
		'L_DELETE_MESSAGE' => $LANG['alert_delete_msg'],
		'L_PRIVATE_MESSAGE' => $LANG['private_message'],
		'L_SUBMIT' => $LANG['submit'],
		'L_PREVIEW' => $LANG['preview'],
		'L_RESET' => $LANG['reset']
	));
	
	$template->assign_block_vars('show_pm', array(
		'DATE' => date('d/m/Y ' . $LANG['at'] . ' H\Hi', time()),
		'CONTENTS' => second_parse(stripslashes(parse($_POST['contents']))),
		'U_PM_BOX' => '<a href="pm.php' . SID . '">' . $LANG['pm_box'] . '</a>',
		'U_TITLE_CONVERS' => '<a href="pm' . transid('.php?id=' . $pm_id_get, '-0-' . $pm_id_get .'.php') . '">' . $convers_title . '</a>',
		'U_MEMBER_VIEW' => '<a href="' . transid('member.php?id=' . $session->data['user_id'] . '&amp;view=1', 'member-' . $session->data['user_id'] . '.php?view=1') . '">' . $LANG['member_area'] . '</a>',
	));
	
	$template->assign_block_vars('post_pm', array(
		'CONTENTS' => !empty($_POST['contents']) ? stripslashes($_POST['contents']) : '',
		'U_PM_ACTION_POST' => transid('.php?id=' . $pm_id_get)
	));
	
	include_once('../includes/bbcode.php');
	$template->assign_var_from_handle('BBCODE', 'bbcode');

	$template->pparse('pm');
}	
elseif( !empty($_POST['pm']) && !empty($pm_id_get) && empty($pm_edit) && empty($pm_del) ) //Envoi de messages.
{
	$contents = !empty($_POST['contents']) ? trim($_POST['contents']) : '';
	if( !empty($contents) )
	{
		//user_view_pm => nombre de messages non lu par l'un des 2 participants.
		
		//On r�cup�re les info de la conversation.
		$convers = $sql->query_array('pm_topic', 'user_id', 'user_id_dest', 'user_convers_status', 'nbr_msg', 'user_view_pm', 'last_user_id', "WHERE id = '" . $pm_id_get . "'", __LINE__, __FILE__);
		
		//R�cup�ration de l'id du destinataire.
		$user_id_dest = ($convers['user_id_dest'] == $session->data['user_id']) ? $convers['user_id'] : $convers['user_id_dest'];
		
		if( $convers['user_convers_status'] == '0' && $user_id_dest > '0' ) //On v�rifie que la conversation n'a pas �t� supprim�e chez le destinataire, et que ce n'est pas un mp automatique du site.
		{
			//Vu par exp et pas par dest  => 1
			//Vu par dest et pas par exp  => 2	
			if( $convers['user_id'] == $session->data['user_id'] ) //Le membre est le cr�ateur de la conversation.
				$status = 1;
			elseif( $convers['user_id_dest'] == $session->data['user_id'] ) //Le membre est le destinataire de la conversation.
				$status = 2;
			
			//Cas assez sournois, l'expediteur a re�u un message pendant qu'il lisait la conversation juste avant de r�pondre => on marque le message comme lu.
			if( $convers['user_view_pm'] > 1 && $convers['last_user_id'] != $session->data['user_id'] )
				$sql->query_inject("UPDATE ".PREFIX."member SET user_pm = user_pm - '" . $convers['user_view_pm'] . "' WHERE user_id = '" . $session->data['user_id'] . "'", __LINE__, __FILE__);
				
			//Envoi du message priv�.
			$privatemsg->send_single_pm($user_id_dest, $pm_id_get, $contents, $session->data['user_id'], $status);

			//Calcul de la page vers laquelle on redirige.
			$last_page = ceil( ($convers['nbr_msg'] + 1) / 25);
			$last_page_rewrite = ($last_page > 1) ? '-' . $last_page : '';
			$last_page = ($last_page > 1) ? '&p=' . $last_page : '';
			
			header('location:' . HOST . DIR . '/member/pm' . transid('.php?id=' . $pm_id_get . $last_page, '-0-' . $pm_id_get . $last_page_rewrite . '.php', '&') . '#m' . $privatemsg->pm_msg_id);
			exit;
		}
		else
		{
			//Le destinataire a supprim� la conversation.
			header('Location:' . HOST . DIR . '/member/pm' . transid('.php?id=' . $pm_id_get . '&error=e_pm_del', '-0-' . $pm_id_get . '-0.php?error=e_pm_del', '&') . '#errorh');
			exit;
		}
	}
	else
	{
		//Champs manquants.
		header('Location:' . HOST . DIR . '/member/pm' . transid('.php?id=' . $pm_id_get . '&error=e_incomplete', '-0-' . $pm_id_get . '-0-e_incomplete.php', '&') . '#errorh');
		exit;
	}
}
elseif( !empty($pm_del_convers) ) //Suppression de conversation.
{
	include_once('../includes/pagination.class.php'); 
	$pagination = new Pagination();
	$pagination_pm = 25;

	//Conversation pr�sente chez les deux membres: user_convers_status => 0.
	//Conversation supprim�e chez l'expediteur: user_convers_status => 1.
	//Conversation supprim�e chez le destinataire: user_convers_status => 2.
	$result = $sql->query_while("SELECT id, user_id, user_id_dest, user_convers_status, last_msg_id
	FROM ".PREFIX."pm_topic
	WHERE 
	(
		" . $session->data['user_id'] . " IN (user_id, user_id_dest)
	) 
	AND 
	(
		user_convers_status = 0 
		OR 
		(
			(user_id_dest = '" . $session->data['user_id'] . "' AND user_convers_status = 1) 
			OR 
			(user_id = '" . $session->data['user_id'] . "' AND user_convers_status = 2)
		)
	)
	ORDER BY last_timestamp DESC 
	" . $sql->sql_limit($pagination->first_msg($pagination_pm, 'p'), $pagination_pm), __LINE__, __FILE__);
	while( $row = $sql->sql_fetch_assoc($result) )
	{
		$del_convers = isset($_POST[$row['id']]) ? trim($_POST[$row['id']]) : '';
		if( $del_convers == 'on' )
		{			
			$del_convers = false;					
			if( $row['user_id'] == $session->data['user_id'] ) //Expediteur.
			{	
				$expd = true;
				if( $row['user_convers_status'] == 2 )
					$del_convers = true;
			}
			elseif( $row['user_id_dest'] == $session->data['user_id'] ) //Destinataire
			{	
				$expd = false;
				if( $row['user_convers_status'] == 1 )
					$del_convers = true;
			}
			
			$view_status = $sql->query("SELECT view_status FROM ".PREFIX."pm_msg WHERE id = '" . $row['last_msg_id'] . "'", __LINE__, __FILE__);	
			$update_nbr_pm = ($view_status == '0') ? true : false;	
			$privatemsg->del_pm($session->data['user_id'], $row['id'], $expd, $del_convers, $update_nbr_pm);
		}
	}
	
	header('location:' . HOST . DIR . '/member/pm' . transid('.php?pm=' . $session->data['user_id'], '-' . $session->data['user_id'] . '.php', '&'));
	exit;
}
elseif( !empty($pm_del) ) //Suppression du message priv�, si le destinataire ne la pas encore lu.
{
	$pm = $sql->query_array('pm_msg', 'idconvers', 'contents', 'view_status', "WHERE id = '" . $pm_del . "' AND user_id = '" . $session->data['user_id'] . "'", __LINE__, __FILE__);
	
	if( !empty($pm['idconvers']) ) //Permet de v�rifier si le message appartient bien au membre.
	{		
		//On r�cup�re les info de la conversation.
		$convers = $sql->query_array('pm_topic', 'title', 'user_id', 'user_id_dest', 'last_msg_id', "WHERE id = '" . $pm['idconvers'] . "'", __LINE__, __FILE__);	
		if( $pm_del ==  $convers['last_msg_id'] ) //On �dite uniquement le dernier message.
		{
			if( $convers['user_id'] == $session->data['user_id'] ) //Expediteur.
			{	
				$expd = true;
				$pm_to = $convers['user_id_dest'];
			}
			elseif( $convers['user_id_dest'] == $session->data['user_id'] ) //Destinataire
			{	
				$expd = false;
				$pm_to = $convers['user_id'];
			}	

			$view = false;
			if( $pm['view_status'] == '1' ) //Le membre a d�j� lu le message => �chec.
				$view = true;
				
			//Le destinataire n'a pas lu le message => on peut �diter.
			if( $view === false )
			{
				$id_first = $sql->query("SELECT MIN(id) FROM ".PREFIX."pm_msg WHERE idconvers = '" . $pm['idconvers'] . "'", __LINE__, __FILE__);
				if( $pm_del > $id_first ) //Suppression du message.
				{	
					$pm_last_msg = $privatemsg->del_single_pm($pm_to, $pm_del, $pm['idconvers']);					
					header('location:' . HOST . DIR . '/member/pm' . transid('.php?id=' . $pm['idconvers'], '-0-' . $pm['idconvers'] . '.php', '&') . '#m' . $pm_last_msg);
					exit;
				}	
				elseif( $pm_del == $id_first ) //Suppression de la conversation.
				{	
					$privatemsg->del_pm($pm_to, $pm['idconvers'], $expd, DEL_PM_CONVERS, UPDATE_MBR_PM);
					header('location:' . HOST . DIR . '/member/pm.php' . SID2);
					exit;
				}					
			}
			else
			{
				//Le membre a d�j� lu le message on ne peux plus le supprimer.
				$errorh->error_handler('e_pm_nodel', E_USER_REDIRECT);
				exit;
			}
		}
		else //Echec.
		{		
			$errorh->error_handler('e_auth', E_USER_REDIRECT); 
			exit;
		}
	}
	else //Echec.
	{		
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
}
elseif( !empty($pm_edit) ) //Edition du message priv�, si le destinataire ne la pas encore lu.
{		
	$pm = $sql->query_array('pm_msg', 'idconvers', 'contents', 'view_status', "WHERE id = '" . $pm_edit . "' AND user_id = '" . $session->data['user_id'] . "'", __LINE__, __FILE__);
	
	if( !empty($pm['idconvers']) ) //Permet de v�rifier si le message appartient bien au membre.
	{		
		//On r�cup�re les info de la conversation.
		$convers = $sql->query_array('pm_topic', 'title', 'user_id', 'user_id_dest', "WHERE id = '" . $pm['idconvers'] . "'", __LINE__, __FILE__);	
		
		$view = false;
		if( $pm['view_status'] == '1' ) //Le membre a d�j� lu le message => �chec.
				$view = true;
			
		//Le destinataire n'a pas lu le message => on peut �diter.
		if( $view === false )
		{
			$id_first = $sql->query("SELECT MIN(id) as id FROM ".PREFIX."pm_msg WHERE idconvers = '" . $pm['idconvers'] . "'", __LINE__, __FILE__);			
			
			if( !empty($_POST['convers']) XOR !empty($_POST['edit_pm']) )
			{
				$contents = !empty($_POST['contents']) ? parse($_POST['contents']) : '';
				$title = !empty($_POST['title']) ? securit($_POST['title']) : '';
				
				if( !empty($_POST['edit_pm']) && !empty($contents) )
				{
					if( $pm_edit > $id_first ) //Maj du message.
						$sql->query_inject("UPDATE ".PREFIX."pm_msg SET contents = '" . $contents . "', timestamp = '" . time() . "' WHERE id = '" . $pm_edit . "'", __LINE__, __FILE__);	
					else
					{
						//Echec.
						$errorh->error_handler('e_auth', E_USER_REDIRECT); 
						exit;
					}
				}
				elseif( !empty($_POST['convers']) && !empty($title) ) //Maj de la conversation, si il s'agit du premier message.
				{
					if( $pm_edit == $id_first )
					{	
						$sql->query_inject("UPDATE ".PREFIX."pm_topic SET title = '" . $title . "', last_timestamp = '" . time() . "' WHERE id = '" . $pm['idconvers'] . "' AND last_msg_id = '" . $pm_edit . "'", __LINE__, __FILE__);
						$sql->query_inject("UPDATE ".PREFIX."pm_msg SET contents = '" . $contents . "', timestamp = '" . time() . "' WHERE id = '" . $pm_edit . "'", __LINE__, __FILE__);	
					}
					else //Echec.
					{						
						$errorh->error_handler('e_auth', E_USER_REDIRECT); 
						exit;
					}
				}						
				else //Champs manquants.
				{					
					$errorh->error_handler('e_incomplete', E_USER_REDIRECT);
					exit;
				}
				
				//Succ�s redirection vers la conversation.
				header('location:' . HOST . DIR . '/member/pm' . transid('.php?id=' . $pm['idconvers'], '-0-' . $pm['idconvers'] . '.php', '&') . '#m' . $pm_edit);
				exit;
			}
			else //Interface d'�dition
			{
				$template->set_filenames(array(
					'pm' => '../templates/' . $CONFIG['theme'] . '/pm.tpl'
				));
				
				$template->assign_vars(array(
					'LANG' => $CONFIG['lang'],
					'THEME' => $CONFIG['theme'],					
					'L_REQUIRE_MESSAGE' => $LANG['require_text'],
					'L_REQUIRE' => $LANG['require'],
					'L_EDIT' => $LANG['edit'],
					'L_PRIVATE_MESSAGE' => $LANG['private_message'],
					'L_MESSAGE' => $LANG['message'],
					'L_SUBMIT' => $LANG['update'],
					'L_PREVIEW' => $LANG['preview'],
					'L_RESET' => $LANG['reset']
				));
				
				$contents = !empty($_POST['contents']) ? stripslashes($_POST['contents']) : '';
				$title = !empty($_POST['title']) ? stripslashes($_POST['title']) : '';
				
				$template->assign_block_vars('edit_pm', array(
					'CONTENTS' => (!empty($_POST['prw_convers']) XOR !empty($_POST['prw'])) ? $contents : unparse($pm['contents']),
					'U_ACTION_EDIT' => transid('.php?edit=' . $pm_edit),
					'U_PM_BOX' => '<a href="pm.php' . SID . '">' . $LANG['pm_box'] . '</a>',
					'U_MEMBER_VIEW' => '<a href="' . transid('member.php?id=' . $session->data['user_id'] . '&amp;view=1', 'member-' . $session->data['user_id'] . '.php?view=1') . '">' . $LANG['member_area'] . '</a>'
				));
				
				if( !empty($_POST['prw_convers']) XOR !empty($_POST['prw']) )
				{				
					$template->assign_block_vars('edit_pm.show_pm', array(
						'DATE' => date('d/m/Y ' . $LANG['at'] . ' H\Hi', time()),
						'CONTENTS' => second_parse(stripslashes(parse($_POST['contents']))),
					));				
				}

				if( $id_first == $pm_edit ) //Premier message de la convers => Edition de celle-ci
				{
					$template->assign_vars(array(
						'SUBMIT_NAME' => 'convers',
						'L_TITLE' => $LANG['title'],
					));
					
					$template->assign_block_vars('edit_pm.title', array(
						'TITLE' => (!empty($_POST['prw_convers']) XOR !empty($_POST['prw']) ) ? $title : $convers['title']
					));
				}
				else
					$template->assign_vars(array(
						'SUBMIT_NAME' => 'edit_pm',
					));
					
				include_once('../includes/bbcode.php');
				$template->assign_var_from_handle('BBCODE', 'bbcode');
				
				$template->pparse('pm');
			}
		}
		else
		{
			//Le membre a d�j� lu le message on ne peux plus �diter.
			$errorh->error_handler('e_pm_noedit', E_USER_REDIRECT); 
			exit;
		}
	}
	else //Echec.
	{		
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
}
elseif( !empty($pm_id_get) ) //Messages associ�s � la conversation.
{
	$template->set_filenames(array(
		'pm' => '../templates/' . $CONFIG['theme'] . '/pm.tpl'
	));
	
	//On cr�e une pagination si le nombre de MP est trop important.
	include_once('../includes/pagination.class.php'); 
	$pagination = new Pagination();

	$visibility = ($session->check_auth($session->data, 1) || $groups->check_auth($groups->user_groups_auth, PM_NO_LIMIT)) ? '' : ' OR visible = 1';
	
	//On r�cup�re les info de la conversation.
	$convers = $sql->query_array('pm_topic', 'id', 'title', 'user_id', 'user_id_dest', 'nbr_msg', 'last_msg_id', 'last_user_id', 'user_view_pm', "WHERE id = '" . $pm_id_get . "' AND ('" . $session->data['user_id'] . "' IN (user_id, user_id_dest) " . $visibility . ")", __LINE__, __FILE__);

	//V�rification des autorisations.
	if( empty($convers['id']) || ($convers['user_id'] != $session->data['user_id'] && $convers['user_id_dest'] != $session->data['user_id']) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	
	if( $convers['user_view_pm'] > 0 && $convers['last_user_id'] != $session->data['user_id'] ) //Membre n'ayant pas encore lu la conversation.
	{
		$sql->query_inject("UPDATE ".LOW_PRIORITY." ".PREFIX."member SET user_pm = user_pm - '" . $convers['user_view_pm'] . "' WHERE user_id = '" . $session->data['user_id'] . "'", __LINE__, __FILE__); 
		$sql->query_inject("UPDATE ".LOW_PRIORITY." ".PREFIX."pm_topic SET user_view_pm = 0 WHERE id = '" . $pm_id_get . "'", __LINE__, __FILE__);
		$sql->query_inject("UPDATE ".LOW_PRIORITY." ".PREFIX."pm_msg SET view_status = 1 WHERE idconvers = '" . $convers['id'] . "' AND user_id != '" . $session->data['user_id'] . "'", __LINE__, __FILE__);
	}	
	
	$pagination_msg = 25;	
	$template->assign_block_vars('pm', array(
		'PAGINATION' => $pagination->show_pagin('pm' . transid('.php?id=' . $pm_id_get . '&amp;p=%d', '-0-' . $pm_id_get . '-%d.php'), $convers['nbr_msg'], 'p', $pagination_msg, 3),
		'U_PM_BOX' => '<a href="pm.php' . SID . '">' . $LANG['pm_box'] . '</a>',
		'U_TITLE_CONVERS' => '<a href="pm' . transid('.php?id=' . $pm_id_get, '-0-' . $pm_id_get .'.php') . '">' . $convers['title'] . '</a>',
		'U_MEMBER_VIEW' => '<a href="' . transid('member.php?id=' . $session->data['user_id'] . '&amp;view=1', 'member-' . $session->data['user_id'] . '.php?view=1') . '">' . $LANG['member_area'] . '</a>'		
	));

	$template->assign_vars(array(
		'THEME' => $CONFIG['theme'],
		'L_REQUIRE_MESSAGE' => $LANG['require_text'],
		'L_REQUIRE_TITLE' => $LANG['require_title'],
		'L_DELETE_MESSAGE' => $LANG['alert_delete_msg'],
		'L_PRIVATE_MESSAGE' => $LANG['private_message'],				
		'L_RESPOND' => $LANG['respond'],
		'L_SUBMIT' => $LANG['submit'],
		'L_PREVIEW' => $LANG['preview'],
		'L_RESET' => $LANG['reset']
	));
	
	//Message non lu par autre membre que user_id view_status => 0.
	//Message lu par autre membre que user_id view_status => 1.
	
	//Cr�ation du tableau des rangs.
	$array_ranks = array(-1 => $LANG['guest'], 0 => $LANG['member'], 1 => $LANG['modo'], 2 => $LANG['admin']);
	
	//Gestion des rangs.	
	$cache->load_file('ranks');
	$page = isset($_GET['pt']) ? numeric($_GET['pt']) : 0; //Red�finition de la variable $page pour prendre en compte les redirections.
	$quote_last_msg = ($page > 1) ? 1 : 0; //On enl�ve 1 au limite si on est sur une page > 1, afin de r�cup�rer le dernier msg de la page pr�c�dente.
	$i = 0;	
	$result = $sql->query_while("SELECT msg.id, msg.user_id, msg.timestamp, msg.view_status, m.login, m.level, m.user_mail, m.user_show_mail, m.timestamp AS registered, m.user_avatar, m.user_msg, m.user_local, m.user_web, m.user_sex, m.user_msn, m.user_yahoo, m.user_sign, m.user_warning, m.user_ban, m.user_groups, s.user_id AS connect, msg.contents
	FROM ".PREFIX."pm_msg as msg
	LEFT JOIN ".PREFIX."member AS m ON m.user_id = msg.user_id
	LEFT JOIN ".PREFIX."sessions AS s ON s.user_id = msg.user_id AND s.session_time > '" . (time() - $CONFIG['site_session_invit']) . "'
	WHERE msg.idconvers = '" . $pm_id_get . "'
	ORDER BY msg.timestamp 
	" . $sql->sql_limit($pagination->first_msg($pagination_msg, 'p'), $pagination_msg), __LINE__, __FILE__);
	while( $row = $sql->sql_fetch_assoc($result) )
	{
		$row['user_id'] = (int)$row['user_id'];
		$is_admin = ($row['user_id'] === -1);
		if( $is_admin )
			$row['level'] = 2;
		
		$edit = '';
		$del = '';
		//Dernier mp �ditable.
		if( $session->data['user_id'] === $row['user_id'] && $row['id'] === $convers['last_msg_id'] ) 
		{
			if( $row['view_status'] === '0' ) //Si le destinataire ne la pas encore lu
			{
				$edit = '<a href="pm' . transid('.php?edit=' . $row['id']) . '" title="'. $LANG['edit'] . '"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/edit.png" alt="" /></a>';
				$del = '&nbsp;&nbsp;<a href="pm' . transid('.php?del=' . $row['id']) . '" title="'. $LANG['delete'] . '"  onclick="javascript:return Confirm_pm();"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/delete.png" alt="" /></a>';
			}
		}
		
		//Rang de l'utilisateur.
		$user_rank = ($row['level'] === '0') ? $LANG['member'] : $LANG['guest'];
		$user_group = $user_rank;
		if( $row['level'] === '2' ) //Rang sp�cial (admins).  
		{
			$user_rank = $_array_rank[-2][0];
			$user_group = $user_rank;
			$user_rank_icon = $_array_rank[-2][1];
		}
		elseif( $row['level'] === '1' ) //Rang sp�cial (modos).  
		{
			$user_rank = $_array_rank[-1][0];
			$user_group = $user_rank;
			$user_rank_icon = $_array_rank[-1][1];
		}
		else
		{
			foreach($_array_rank as $msg => $ranks_info)
			{
				if( $msg >= 0 && $msg <= $row['user_msg'] )
				{ 
					$user_rank = $ranks_info[0];
					$user_rank_icon = $ranks_info[1];
					break;
				}
			}
		}
		
		//Image associ�e au rang.
		$user_assoc_img = isset($user_rank_icon) ? '<img src="../templates/' . $CONFIG['theme'] . '/images/ranks/' . $user_rank_icon . '" alt="" />' : '';
					
		//Affichage des groupes du membre.		
		if( !empty($row['user_groups']) && $_array_groups_auth ) 
		{	
			$user_groups = '';
			$array_user_groups = explode('|', $row['user_groups']);
			foreach($_array_groups_auth as $idgroup => $array_group_info)
			{
				if( is_numeric(array_search($idgroup, $array_user_groups)) )
					$user_groups .= !empty($array_group_info[1]) ? '<img src="../images/group/' . $array_group_info[1] . '" alt="' . $array_group_info[0] . '" title="' . $array_group_info[0] . '"/><br />' : $LANG['group'] . ': ' . $array_group_info[0];
			}
		}
		else
			$user_groups = $LANG['group'] . ': ' . $user_group;
		
		//Membre en ligne?
		$user_online = !empty($row['connect']) ? 'online' : 'offline';
		
		//Avatar			
		if( empty($row['user_avatar']) ) 
			$user_avatar = ($CONFIG_MEMBER['activ_avatar'] == '1' && !empty($CONFIG_MEMBER['avatar_url'])) ? '<img src="../templates/' . $CONFIG['theme'] . '/images/' .  $CONFIG_MEMBER['avatar_url'] . '" alt="" />' : '';
		else
			$user_avatar = '<img src="' . $row['user_avatar'] . '" alt=""	/>';
		
		//Affichage du sexe et du statut (connect�/d�connect�).	
		$user_sex = '';
		if( $row['user_sex'] == 1 )	
			$user_sex = $LANG['sex'] . ': <img src="../templates/' . $CONFIG['theme'] . '/images/man.png" alt="" /><br />';	
		elseif( $row['user_sex'] == 2 ) 
			$user_sex = $LANG['sex'] . ': <img src="../templates/' . $CONFIG['theme'] . '/images/woman.png" alt="" /><br />';
				
		//Nombre de message.
		$user_msg = ($row['user_msg'] > 1) ? $LANG['message_s'] . ': ' . $row['user_msg'] : $LANG['message'] . ': ' . $row['user_msg'];
		
		//Localisation.
		if( !empty($row['user_local']) ) 
		{
			$user_local = $LANG['place'] . ': ' . $row['user_local'];
			$user_local = $user_local > 15 ? substr_html($user_local, 0, 15) . '...<br />' : $user_local . '<br />';			
		}
		else $user_local = '';

		//Reprise du dernier message de la page pr�c�dente.
		$row['contents'] = ($quote_last_msg == 1 && $i == 0) ? '<span class="text_strong">' . $LANG['forum_quote_last_msg'] . '</span><br /><br />' . $row['contents'] : $row['contents'];
		$i++;
		
		$template->assign_block_vars('pm.msg', array(
			'ID' => $row['id'],
			'CONTENTS' => second_parse($row['contents']),
			'DATE' => $LANG['on'] . ' ' . date($LANG['date_format'] . ' ' . $LANG['at'] . ' H\hi', $row['timestamp']),
			'USER_ONLINE' => '<img src="../templates/' . $CONFIG['theme'] . '/images/' . $user_online . '.png" alt="" class="valign_middle" />',
			'USER_PSEUDO' => ($is_admin) ? $LANG['admin'] : (!empty($row['login']) ? wordwrap_html($row['login'], 13) : $LANG['guest']),			
			'USER_RANK' => ($is_admin) ? '' : (($row['user_warning'] < '100' || (time() - $row['user_ban']) < 0) ? $user_rank : $LANG['banned']),
			'USER_IMG_ASSOC' => ($is_admin) ? '' : $user_assoc_img,
			'USER_AVATAR' => ($is_admin) ? '' : $user_avatar,			
			'USER_GROUP' => ($is_admin) ? '' : $user_groups,
			'USER_DATE' => ($is_admin) ? '' : $LANG['registered_on'] . ': ' . date($LANG['date_format'], $row['registered']),
			'USER_SEX' => ($is_admin) ? '' : $user_sex,
			'USER_MSG' => ($is_admin) ? '' : $user_msg,
			'USER_LOCAL' => ($is_admin) ? '' : $user_local,
			'USER_MAIL' => ($is_admin) ? '' : ( !empty($row['user_mail']) && ($row['user_show_mail'] == '1' ) ) ? '<a href="mailto:' . $row['user_mail'] . '"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/email.png" alt="' . $row['user_mail']  . '" title="' . $row['user_mail']  . '" /></a>' : '',			
			'USER_MSN' => ($is_admin) ? '' : (!empty($row['user_msn'])) ? '<a href="mailto:' . $row['user_msn'] . '"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/msn.png" alt="' . $row['user_msn']  . '" title="' . $row['user_msn']  . '" /></a>' : '',
			'USER_YAHOO' => ($is_admin) ? '' : (!empty($row['user_yahoo'])) ? '<a href="mailto:' . $row['user_yahoo'] . '"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/yahoo.png" alt="' . $row['user_yahoo']  . '" title="' . $row['user_yahoo']  . '" /></a>' : '',
			'USER_SIGN' => ($is_admin) ? '' : (!empty($row['user_sign'])) ? '____________________<br />' . $row['user_sign'] : '',
			'USER_WEB' => ($is_admin) ? '' : (!empty($row['user_web'])) ? '<a href="' . $row['user_web'] . '"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/user_web.png" alt="' . $row['user_web']  . '" title="' . $row['user_yahoo']  . '" /></a>' : '',
			'WARNING' => ($is_admin) ? '' : $row['user_warning'] . '%',
			'EDIT' => $edit,
			'DEL' => $del,
			'U_MEMBER_ID' => ($is_admin) ? '' : transid('.php?id=' . $row['user_id'], '-' . $row['user_id'] . '.php'),
			'U_VARS_ANCRE' => transid('.php?id=' . $pm_id_get . (!empty($page) ? '&amp;p=' . $page : ''), '-0-' . $pm_id_get . (!empty($page) ? '-' . $page : '') . '.php'),
			'U_VARS_QUOTE' => ($is_admin) ? '' : ('<a href="pm' . transid('.php?quote=' . $row['id'] . '&amp;id=' . $pm_id_get . (!empty($page) ? '&amp;p=' . $page : ''), '-0-' . $pm_id_get . (!empty($page) ? '-' . $page : '-0') . '-' . $row['id'] . '.php') . '#quote" title="' . $LANG['quote'] . '"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/quote.png" alt="" /></a>'),
			'U_MEMBER_PM' => ($is_admin) ? '' : '<a href="../member/pm' . transid('.php?pm=' . $row['user_id'], '-' . $row['user_id'] . '.php') . '"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/pm.png" alt="" /></a>',
		));
		
		//Marqueur de suivis du sujet.
		if( !empty($row['track']) ) $track = true;
	}
	$sql->close($result);

	//R�cup�ration du message quot�.
	if( !empty($quote_get) )
	{	
		$quote_msg = $sql->query_array('pm_msg', 'user_id', 'contents', "WHERE id = '" . $quote_get . "'", __LINE__, __FILE__);
		$pseudo = $sql->query("SELECT login FROM ".PREFIX."member WHERE user_id = '" . $quote_msg['user_id'] . "'", __LINE__, __FILE__);
		
		$contents = '[quote=' . $pseudo . ']' . unparse($quote_msg['contents']) . '[/quote]';
	}
	else
		$contents = '';
	
	if( $convers['user_id'] != -1 )
	{		
		$template->assign_block_vars('post_pm', array(
			'CONTENTS' => $contents,
			'U_PM_ACTION_POST' => transid('.php?id=' . $pm_id_get, '-0-' . $pm_id_get . '.php')
		));
		
		//Gestion des erreurs
		$get_error = !empty($_GET['error']) ? trim($_GET['error']) : '';
		switch($get_error)
		{
			case 'e_incomplete':
				$errstr = $LANG['e_incomplete'];
				$type = E_USER_NOTICE;				
				break;
			case 'e_pm_del':
				$errstr = $LANG['e_pm_del'];
				$type = E_USER_WARNING;				
				break;
			default:
				$errstr = '';
		}
		if( !empty($errstr) )
			$errorh->error_handler($errstr, $type, NO_LINE_ERROR, NO_FILE_ERROR, 'post_pm.');
		
		include_once('../includes/bbcode.php');
		$template->assign_var_from_handle('BBCODE', 'bbcode');
	}
	
	$template->pparse('pm');
}
else //Liste des conversation, dans la boite du membre.
{
	$template->set_filenames(array(
		'pm' => '../templates/' . $CONFIG['theme'] . '/pm.tpl'
	));

	$nbr_pm = $privatemsg->get_total_convers_pm($session->data['user_id']);
	
	//On cr�e une pagination si le nombre de MP est trop important.
	include_once('../includes/pagination.class.php'); 
	$pagination = new Pagination();

	$pagination_pm = 25;
	$pagination_msg = 25;
		
	$unlimited_pm = $session->check_auth($session->data, 1) || $groups->check_auth($groups->user_groups_auth, PM_NO_LIMIT);	
	$pm_max = $unlimited_pm ? $LANG['illimited'] : $CONFIG['pm_max'];
	
	$template->assign_block_vars('convers', array(
		'NBR_PM' => $pagination_pm,
		'PM_POURCENT' => '<strong>' . $nbr_pm . '</strong> / <strong>' . $pm_max . '</strong>',
		'PAGINATION' => $pagination->show_pagin('pm' . transid('.php?p=%d', '-0-0-%d.php'), $nbr_pm, 'p', $pagination_pm, 3),
		'U_MARK_AS_READ' => '<a href="pm.php?read=1" class="small_link">' . $LANG['mark_pm_as_read'] . '</a>',
		'U_MEMBER_ACTION_PM' => transid('.php?del_convers=1&amp;p=' . $page),
		'U_MEMBER_VIEW' => '<a href="' . transid('member.php?id=' . $session->data['user_id'] . '&amp;view=1', 'member-' . $session->data['user_id'] . '.php?view=1') . '">' . $LANG['member_area'] . '</a>',	
		'U_PM_BOX' => '<a href="pm.php' . SID . '">' . $LANG['pm_box'] . '</a>',
		'U_POST_NEW_CONVERS' => '<a href="pm' . transid('.php?post=1', '') . '" title="' . $LANG['post_new_convers'] . '"><img src="../templates/' . $CONFIG['theme'] . '/images/' . $CONFIG['lang'] . '/post.png" alt="' . $LANG['post_new_convers'] . '" title="' . $LANG['post_new_convers'] . '" class="valign_middle" /></a>'
	));
	
	//Aucun message priv�.
	if( $nbr_pm == 0 )
	{
		$template->assign_block_vars('convers.no_pm', array(
			'L_NO_PM' => $LANG['no_pm']
		));
	}
	
	if( !$unlimited_pm )
	{
		$nbr_waiting_pm = $sql->query("SELECT COUNT(*) FROM ".PREFIX."pm_topic WHERE user_id_dest = '" . $session->data['user_id'] . "' AND visible = 0", __LINE__, __FILE__);
		//Gestion erreur.
		if( $nbr_waiting_pm > 0 )
			$errorh->error_handler(sprintf($LANG['e_pm_full'], $nbr_waiting_pm), E_USER_WARNING, NO_LINE_ERROR, NO_FILE_ERROR, 'convers.');
	}
	
	$template->assign_vars(array(
		'THEME' => $CONFIG['theme'],
		'L_REQUIRE_MESSAGE' => $LANG['require_text'],
		'L_REQUIRE_TITLE' => $LANG['require_title'],
		'L_DELETE_MESSAGE' => $LANG['alert_delete_msg'],
		'L_PRIVATE_MSG' => $LANG['private_message'],
		'L_PM_BOX' => $LANG['pm_box'],				
		'L_TITLE' => $LANG['title'],
		'L_PARTICIPANTS' => $LANG['participants'],
		'L_MESSAGE' => $LANG['replies'],
		'L_LAST_MESSAGE' => $LANG['last_message'],
		'L_STATUS' => $LANG['status'],
		'L_DELETE' => $LANG['delete'],
		'L_READ' => $LANG['read'],
		'L_TRACK' => $LANG['pm_track'],
		'L_NOT_READ' => $LANG['not_read']
	));

	$visibility = $unlimited_pm ? '' : "AND (pm.visible = 1 OR pm.user_id = '" . $session->data['user_id'] . "')";
	
	//Conversation pr�sente chez les deux membres: user_convers_status => 0.
	//Conversation supprim�e chez l'expediteur: user_convers_status => 1.
	//Conversation supprim�e chez le destinataire: user_convers_status => 2.
	$i = 0;
	$result = $sql->query_while("SELECT pm.id, pm.title, pm.user_id, pm.user_id_dest, pm.user_convers_status, pm.nbr_msg, pm.last_user_id, pm.last_msg_id, pm.last_timestamp, msg.view_status, m.login AS login, m1.login AS login_dest, m2.login AS last_login
	FROM ".PREFIX."pm_topic AS pm
	LEFT JOIN ".PREFIX."pm_msg AS msg ON msg.id = pm.last_msg_id
	LEFT JOIN ".PREFIX."member AS m ON m.user_id = pm.user_id
	LEFT JOIN ".PREFIX."member AS m1 ON m1.user_id = pm.user_id_dest
	LEFT JOIN ".PREFIX."member AS m2 ON m2.user_id = pm.last_user_id
	WHERE 
	(
		" . $session->data['user_id'] . " IN (pm.user_id, pm.user_id_dest)
	) 
	AND 
	(
		pm.user_convers_status = 0 
		OR 
		(
			(pm.user_id_dest = '" . $session->data['user_id'] . "' AND pm.user_convers_status = 1) 
			OR 
			(pm.user_id = '" . $session->data['user_id'] . "' AND pm.user_convers_status = 2)
		)
	) " . $visibility . "
	ORDER BY pm.last_timestamp DESC 
	" . $sql->sql_limit($pagination->first_msg($pagination_pm, 'p'), $pagination_pm), __LINE__, __FILE__);
	while( $row = $sql->sql_fetch_assoc($result) )
	{
		$view = false;
		$track = false;
		if( $row['last_user_id'] == $session->data['user_id'] ) //Le membre est le dernier posteur.
		{	
			$view = true;	
			if( $row['view_status'] === '0' ) //Le d�stinataire n'a pas encore lu le message.
				$track = true;	
		}
		else //Le membre n'est pas le dernier posteur.
		{	
			if( $row['view_status'] === '1' ) //Le membre a d�j� lu le message.
				$view = true;
		}
	
		$img_announce = 'announce';
		//V�rifications des messages Lu/non Lus.
		if( $view === false ) //Nouveau message (non lu).
			$img_announce = ($row['nbr_msg'] > $pagination_msg) ? 'new_' . $img_announce . '_hot' : 'new_' . $img_announce;
		if( $track === true ) //Marqueur de reception du message
			$img_announce = $img_announce . '_track';
			
		//Ancre vers vers le dernier message post�.		
		$last_page = ceil( $row['nbr_msg'] / $pagination_msg);
		$last_page_rewrite = ($last_page > 1) ? '-' . $last_page : '';
		$last_page = ($last_page > 1) ? 'p=' . $last_page . '&amp;' : '';
		
		if( $row['user_id'] == -1 ) 
			$author = $LANG['admin'];
		elseif( !empty($row['login']) )
			$author = '<a href="../member/member' . transid('.php?id=' . $row['user_id'], '-' . $row['user_id'] . '.php') . '" class="small_link">' . $row['login'] . '</a>';
		else
			$author = $LANG['guest'];
			
		$participants = ($row['login_dest'] != $session->data['login']) ? $row['login_dest'] : $row['login'];
		$participants = !empty($participants) ? '<a href="../member/member' . transid('.php?id=' . $row['user_id_dest'], '-' . $row['user_id_dest'] . '.php') . '">' . $participants . '</a>' : '<strike>' . $LANG['admin']. '</strike>';
		
		//Affichage du dernier message post�.
		$last_msg = '<a href="pm' . transid('.php?' . $last_page . 'id=' . $row['id'], '-0-' . $row['id'] . $last_page_rewrite . '.php') . '#m' . $row['last_msg_id'] . '" title=""><img src="../templates/' . $CONFIG['theme'] . '/images/ancre.png" alt="" /></a>' . ' ' . $LANG['on'] . ' ' . date($LANG['date_format'] . ' ' . $LANG['at'] . ' H\hi', $row['last_timestamp']) . '<br />'; 
		$last_msg .= ($row['user_id'] == -1) ? $LANG['by'] . ' ' . $LANG['admin'] : $LANG['by'] . ' <a href="../member/member' . transid('.php?id=' . $row['last_user_id'], '-' . $row['last_user_id'] . '.php') . '" class="small_link">' . $row['last_login'] . '</a>';

		$template->assign_block_vars('convers.list', array(
			'INCR' => $i,
			'ID' => $row['id'],	
			'ANNOUNCE' => '<img src="../templates/' . $CONFIG['theme'] . '/images/' . $img_announce . '.gif" alt="" />',
			'TITLE' => $row['title'],
			'MSG' => ($row['nbr_msg'] - 1),
			'U_PARTICIPANTS' => (($row['user_convers_status'] != 0) ? '<strike>' . $participants . '</strike>' : $participants),
			'U_CONVERS'	=> transid('.php?id=' . $row['id'], '-0-' . $row['id'] . '.php'),
			'U_AUTHOR' => $LANG['by'] . ' ' . $author,
			'U_LAST_MSG' => $last_msg
		));
		$i++;
	}
	$sql->close($result);
	
	$template->pparse('pm');
}

	
include('../includes/footer.php');

?>