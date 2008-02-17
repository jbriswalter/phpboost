<?php
/*##################################################
 *                               admin_faq_cats.php
 *                            -------------------
 *   begin                : December 26, 2007
 *   copyright          : (C) 2007 Sautel Benoit
 *   email                : ben.popeye@phpboost.com
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

include_once('../includes/admin_begin.php');
include_once('faq_begin.php'); //Chargement de la langue du module.
define('TITLE', $LANG['administration']);
include_once('../includes/admin_header.php');

$cache->load_file('faq');

include_once('faq_cats.class.php');
$categories = new FaqCats();

$id_up = !empty($_GET['id_up']) ? numeric($_GET['id_up']) : 0;
$id_down = !empty($_GET['id_down']) ? numeric($_GET['id_down']) : 0;
$cat_to_del = !empty($_GET['del']) ? numeric($_GET['del']) : 0;

if( $id_up > 0 )
{
	$categories = new FaqCats();
	$categories->Move_category($id_up, 'up');
	redirect(transid('admin_faq_cats.php'));
}
elseif( $id_down > 0 )
{
	$categories = new FaqCats();
	$categories->Move_category($id_down, 'down');
}
elseif( $cat_to_del > 0 )
{
	
}
else
{
	$template->set_filenames(array(
	'admin_faq_cat' => '../templates/' . $CONFIG['theme'] . '/faq/admin_faq_cats.tpl'
	));
	
	$cat_config = array(
		'xmlhttprequest_file' => 'xmlhttprequest_cats.php',
		'administration_file_name' => 'admin_faq_cats.php',
		'url' => array(
			'unrewrited' => '../faq/faq.php?id=%d',
			'rewrited' => '../faq/faq-%d+%s.php'),
		);
		
	$categories->Set_displaying_configuration($cat_config);
	
	echo $categories->Build_administration_list($FAQ_CATS);

	$template->pparse('admin_faq_cat');

}

include_once('../includes/admin_footer.php');

?>