<?php
/*##################################################
 *                              news_begin.php
 *                            -------------------
 *   begin                : November 28, 2007
 *   copyright          : (C) 2007 Viarre r�gis
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

if( defined('PHPBOOST') !== true)	
	exit;
	
//Autorisation sur le module.
if( !$Member->Check_auth($SECURE_MODULE['news'], ACCESS_MODULE) )
	$Errorh->Error_handler('e_auth', E_USER_REDIRECT); 

load_module_lang('news'); //Chargement de la langue du module.

$idnews = !empty($_GET['id']) ? numeric($_GET['id']) : 0;	
$idcat = !empty($_GET['cat']) ? numeric($_GET['cat']) : 0;
if( !empty($idnews) && empty($idcat) )
{
	$result = $Sql->Query_while("SELECT n.contents, n.extend_contents, n.title, n.id, n.archive, n.timestamp, n.user_id, n.img, n.alt, n.nbr_com, nc.id AS idcat, nc.icon, m.login
	FROM ".PREFIX."news n
	LEFT JOIN ".PREFIX."news_cat nc ON nc.id = n.idcat
	LEFT JOIN ".PREFIX."member m ON m.user_id = n.user_id		
	WHERE n.visible = 1 AND n.id = '" . $idnews . "'", __LINE__, __FILE__);
	$news = $Sql->Sql_fetch_assoc($result);
	
	define('TITLE', $LANG['title_news'] . ' - ' . addslashes($news['title']));
}
else 
	define('TITLE', $LANG['title_news']);
	
$news_title = !empty($idnews) ? $news['title'] : '';
$Speed_bar->Add_link($LANG['title_news'], transid('news.php'));
$Speed_bar->Add_link($news_title, (!empty($_GET['i']) ? transid('news.php?id=' . $idnews) : ''));
$Speed_bar->Add_link((isset($_GET['i']) ? $LANG['com'] : ''), '');

//Chargement du cache
$Cache->Load_file('news');
//Css alternatif.
define('ALTERNATIVE_CSS', 'news');

?>