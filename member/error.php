<?php
/*##################################################
 *                                error.php
 *                            -------------------
 *   begin                : August 08 2005
 *   copyright          : (C) 2005 CrowkaiT
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

include_once('../includes/begin.php'); 
define('TITLE', $LANG['title_error']);
include_once('../includes/header.php'); 

$id_error = !empty($_GET['e']) ? trim($_GET['e']) : '';
	
$array_error = array('e_member_ban', 'e_member_ban_w', 'e_unexist_member', 'e_unactiv_member', 'e_member_flood');	
if( in_array($id_error, $array_error) )
{
	if( $session->data['user_id'] === -1 )
	{
		$template->set_filenames(array(
			'error' => '../templates/' . $CONFIG['theme'] . '/error.tpl'
		));
			
		$template->assign_block_vars('connexion', array(
		));
		
		$errno = E_USER_WARNING;
		switch($id_error)
		{ 
			case 'e_member_ban':
				$ban = !empty($_GET['ban']) ? numeric($_GET['ban']) : '';
				if( $ban > 0 )
				{
					if( $ban < 60 )
						$delay_ban = $ban . ' ' . ($ban > 1) ? $LANG['minutes'] : $LANG['minute'];
					elseif( $ban < 1440 )
					{
						$delay_ban = arrondi($ban/60, 0);
						$delay_ban = $delay_ban . ' ' . (($delay_ban > 1) ? $LANG['hours'] : $LANG['hour']);				
					}
					elseif( $ban < 10080 )
					{
						$delay_ban = arrondi($ban/1440, 0);
						$delay_ban = $delay_ban . ' ' . (($delay_ban > 1) ? $LANG['days'] : $LANG['day']);				
					}
					elseif( $ban < 43200 )
					{
						$delay_ban = arrondi($ban/10080, 0);	
						$delay_ban = $delay_ban . ' ' . (($delay_ban > 1) ? $LANG['weeks'] : $LANG['week']);				
					}
					elseif( $ban < 525600 )
					{
						$delay_ban = arrondi($ban/43200, 0);
						$delay_ban = $delay_ban . ' ' . (($delay_ban > 1) ? $LANG['months'] : $LANG['month']);
					}
					else
					{
						$delay_ban = arrondi($ban/525600, 0);
						$delay_ban = $delay_ban . ' ' . (($delay_ban > 1) ? $LANG['years'] : $LANG['year']);				
					}
				}
				else
					$delay_ban = 0 . ' ' . $LANG['minutes'];
				$errstr = $LANG['e_member_ban'] . ' ' . $delay_ban;
			break;
			case 'e_member_ban_w':			
				$errstr = $LANG['e_member_ban_w'];
			break;
			case 'e_unexist_member':			
				$errstr = $LANG['e_unexist_member'];
			break;
			case 'e_unactiv_member':			
				$errstr = $LANG['e_unactiv_member'];
			break;
			case 'e_member_flood':			
				$flood = (!empty($_GET['flood']) && $_GET['flood'] > 0 && $_GET['flood'] <= 5) ? numeric($_GET['flood']) : '0';		
				$flood = ($flood > 0) ? sprintf($LANG['e_test_connect'], $flood) : $LANG['e_nomore_test_connect'];
				$errstr = $flood;
			break;
			default:
			$errstr = '';
		}	
			
		if( !empty($errstr) )
			$errorh->error_handler($errstr, $errno, NO_LINE_ERROR, NO_FILE_ERROR, 'connexion.');		
			
		$template->assign_vars(array(
			'L_CONNECT' => $LANG['connect'],
			'L_PSEUDO' => $LANG['pseudo'],
			'L_PASSWORD' => $LANG['password'],
			'L_REGISTER' => $LANG['register'],
			'L_FORGOT_PASS' => $LANG['forget_pass'],
			'L_AUTOCONNECT' => $LANG['autoconnect'],
			'U_REGISTER' => $CONFIG_MEMBER['activ_register'] ? '<a href="../member/register.php">' . $LANG['register'] . '</a><br />' : ''
		));
		
		$template->pparse('error');
	}
	else
	{	
		header('location: ' . get_start_page());
		exit;
	}
}
elseif( $session->data['user_id'] === -1 )
{
	$template->set_filenames(array(
	'error' => '../templates/' . $CONFIG['theme'] . '/error.tpl'
	));
	
	$template->assign_block_vars('connexion', array(
	));

	include_once('../includes/connect.php');
	
	$template->assign_vars(array(
		'L_CONNECT' => $LANG['connect'],
		'L_PSEUDO' => $LANG['pseudo'],
		'L_PASSWORD' => $LANG['password'],
		'L_REGISTER' => $LANG['register'],
		'L_FORGOT_PASS' => $LANG['forget_pass'],
		'L_AUTOCONNECT' => $LANG['autoconnect'],
		'U_REGISTER' => $CONFIG_MEMBER['activ_register'] ? '<a href="../member/register.php">' . $LANG['register'] . '</a><br />' : ''
	));
	
	$template->pparse('error');
}
elseif( !empty($id_error) )
{
	$template->set_filenames(array(
		'error' => '../templates/' . $CONFIG['theme'] . '/error.tpl'
	));

	//Inclusion des langues des erreurs pour le module si elle existe.
	$module = substr(strrchr($id_error, '_'), 1);
	$path_module_error = '../' . $module . '/lang/' . $CONFIG['lang'] . '/' . $module. '_' . $CONFIG['lang'] . '.php';
	if( is_file($path_module_error) )
		include_once($path_module_error);

	$template->assign_vars(array(
		'THEME' => $CONFIG['theme'],		
		'L_ERROR' => $LANG['error'],
		'U_BACK' => !empty($_SERVER['HTTP_REFERER']) ? '<a href="' . transid($_SERVER['HTTP_REFERER']) .'">' . $LANG['back'] . '</a>' : '<a href="javascript:history.back(1)">' . $LANG['back'] . '</a>',
		'U_INDEX' => '<a href="' . transid(get_start_page()) .'">' . $LANG['index'] . '</a>', 
	));
	
	$template->assign_block_vars('error', array(
		'IMG' => 'important',
		'CLASS' => 'error_warning',
		'L_ERROR' => isset($LANG[$id_error]) ? $LANG[$id_error] : $LANG['unknow_error']
	));
	
	$template->pparse('error');
}

else
{
	header('location: ' . get_start_page());
	exit;
}

include_once('../includes/footer.php'); 

?>