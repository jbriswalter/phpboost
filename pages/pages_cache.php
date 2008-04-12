<?php
/*##################################################
 *                                pages_cache.php
 *                            -------------------
 *   begin                : August 08, 2007
 *   copyright          : (C) 2007 Sautel Benoit
 *   email                : ben.popeye@phpboost.com
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

if( defined('PHPBOOST') !== true) exit;

function generate_module_file_pages()
{
	global $Sql;
	
	//Catégories des pages
	$config = 'global $_PAGES_CATS;' . "\n";
	$config .= '$_PAGES_CATS = array();' . "\n";
	$result = $Sql->Query_while("SELECT c.id, c.id_parent, c.id_page, p.title, p.auth
	FROM ".PREFIX."pages_cats c
	LEFT JOIN ".PREFIX."pages p ON p.id = c.id_page
	ORDER BY p.title", __LINE__, __FILE__);
	while( $row = $Sql->Sql_fetch_assoc($result) )
	{
		$config .= '$_PAGES_CATS[\'' . $row['id'] . '\'] = ' . var_export(array(
			'id_parent' => !empty($row['id_parent']) ? $row['id_parent'] : '0',
			'name' => $row['title'],
			'auth' => unserialize(stripslashes($row['auth']))
			), true) . ';' . "\n";
	}

	//Configuration du module de pages
	$code = 'global $_PAGES_CONFIG;' . "\n";
	$CONFIG_PAGES = unserialize($Sql->Query("SELECT value FROM ".PREFIX."configs WHERE name = 'pages'", __LINE__, __FILE__));
	
	if( is_array($CONFIG_PAGES) )
		$CONFIG_PAGES['auth'] = unserialize(stripslashes($CONFIG_PAGES['auth']));
	else
		$CONFIG_PAGES = array(
		'count_hits' => 1,
		'activ_com' => 1,
		'auth' => array (
			'r-1' => 5,
			'r0' => 5,
			'r1' => 7,
			'r2' => 7,
		));
	
	$code .=  '$_PAGES_CONFIG = ' . var_export($CONFIG_PAGES, true) . ';' . "\n";
	
	return $config . "\n\r" . $code;
}

?>