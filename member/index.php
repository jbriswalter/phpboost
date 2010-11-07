<?php
/*##################################################
 *                           index.php
 *                            -------------------
 *   begin                : September 18, 2010 2009
 *   copyright            : (C) 2010 K�vin MASSY
 *   email                : soldier.weasel@gmail.com
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

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/begin.php';

$url_controller_mappers = array(
	new UrlControllerMapper('ConfirmController', '`^/confirm(?:/([0-9]+))?/?$`', array('key')),
	new UrlControllerMapper('RegisterController', '`^/register/?$`'),
	new UrlControllerMapper('ViewProfilController', '`^/profil(?:/([0-9]+))?/?$`', array('user_id')),
	new UrlControllerMapper('EditProfilController', '`^/profil/edit/?$`'),
	new UrlControllerMapper('404Controller', '`^/404/?$`'),
	new UrlControllerMapper('MaintainController', '`^/maintain/?$`'),
	new UrlControllerMapper('ErrorController', '`^/error/([a-z0-9]+)/?$`', array('type')),
	new UrlControllerMapper('MemberHomeController', '`^/?$`')
);
DispatchManager::dispatch($url_controller_mappers);

?>