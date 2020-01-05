<?php
/**
 * @package     PHPBoost
 * @subpackage  Module
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2019 12 20
 * @since       PHPBoost 5.3 - 2019 12 20
*/

class ModulesUrlBuilder
{
	protected static $module_id;

	protected static function get_module_id($module_id = '')
	{
		if (self::$module_id === null || (!empty($module_id) && $module_id != self::$module_id))
			self::$module_id = !empty($module_id) ? $module_id : Environment::get_running_module_name();
		return self::$module_id;
	}

	protected static function get_dispatcher($module_id = '')
	{
		return '/' . self::get_module_id($module_id);
	}

	/**
	 * @return Url
	 */
	public static function configuration($module_id = '')
	{
		return DispatchManager::get_url(self::get_dispatcher($module_id), '/admin/config');
	}

	/**
	 * @return Url
	 */
	public static function home($module_id = '')
	{
		return DispatchManager::get_url(self::get_dispatcher($module_id), '/');
	}
}
?>