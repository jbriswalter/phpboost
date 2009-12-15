<?php
/*##################################################
 *                           DBMSUtils.class.php
 *                            -------------------
 *   begin                : November 3, 2009
 *   copyright            : (C) 2009 Loic Rouchon
 *   email                : loic.rouchon@phpboost.com
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

/**
 * @author loic rouchon <loic.rouchon@phpboost.com>
 * @package io
 * @subpackage db/dbms
 * @desc
 *
 */
interface DBMSUtils
{
	function get_dbms_version();

	function list_databases();

	function get_database_name();

	function create_database($database_name);

	function list_tables();

	function list_and_desc_tables();

	function desc_table($table);

    function create_table($table_name, array $fields, array $options = array());

	function optimize($tables);

	function repair($tables);

	function truncate($tables);

	function drop($tables);

	function export_phpboost($file = null);

	function export_table($table, $file = null);
}

?>