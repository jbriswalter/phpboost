<?php
/*##################################################
 *                           index.php
 *                            -------------------
 *   begin                : March 11, 2011
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
	new UrlControllerMapper('AdminNewsletterConfigController', '`^/admin(?:/config)?/?$`'),
	
	new UrlControllerMapper('HomeAddNewsletterController', '`^/add/?/?$`'),
	new UrlControllerMapper('AddNewsletterController', '`^/add/([a-z]+)?/?$`', array('type')),
	new UrlControllerMapper('EditNewsletterController', '`^/edit/([a-z]+)?/?$`', array('type')),
	
	new UrlControllerMapper('AdminNewsletterCategoriesListController', '`^/admin/categories/list/?(?:/([a-z]+))?/?([a-z]+)?/?([0-9]+)?/?$`', array('field', 'sort', 'page')),
	new UrlControllerMapper('AdminNewsletterAddCategorieController', '`^/admin/categories/add/?$`'),
	new UrlControllerMapper('AdminNewsletterEditCategorieController', '`^/admin/categories/([0-9]+)/edit/?$`', array('id')),
	new UrlControllerMapper('AdminNewsletterDeleteCategorieController', '`^/admin/categories/([0-9]+)/delete/?$`', array('id')),
	
	new UrlControllerMapper('NewsletterSubscribersListController', '`^/subscribers/list/?(?:/([a-z]+))?/?([a-z]+)?/?([0-9]+)?/?$`', array('field', 'sort', 'page')),
);
DispatchManager::dispatch($url_controller_mappers);

?>