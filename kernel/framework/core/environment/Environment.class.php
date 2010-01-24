<?php
/*##################################################
 *                          Environment.class.php
 *                            -------------------
 *   begin                : September 28, 2009
 *   copyright            : (C) 2009 Benoit Sautel, Lo�c Rouchon
 *   email                : ben.popeye@phpboost.com, loic.rouchon@phpboost.com
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
 * @package core
 * @subpackage environment
 * This class manages all the environment that PHPBoost need to run.
 * <p>It's able to initialize the environment that contains services (database,
 * users management...) as well as the graphical environment.</p>
 * @author Benoit Sautel <ben.popeye@phpboost.com>
 *
 */
class Environment
{
	private static $running_module_name = '';

	/**
	 * @var GraphicalEnvironment
	 */
	private static $graphical_environment = null;

	/**
	 * Loads all the files that the environment requires
	 */
	public static function load_imports()
	{
		require_once PATH_TO_ROOT . '/kernel/framework/functions.inc.php';
		require_once PATH_TO_ROOT . '/kernel/framework/helper/deprecated_helper.inc.php';

		import('core/ClassLoader');
		ClassLoader::init_autoload();

		AppContext::init_bench();
	}
	
	/**
	 * Inits the environment and all its services.
	 */
	public static function init()
	{
		self::write_default_http_headers();
		self::fit_to_php_configuration();
		self::init_services();
		self::load_static_constants();

		// TODO Suppress uses of $Sql in the framework
		global $Sql;
		$Sql = AppContext::get_sql();
		/* END DEPRECATED */

		self::load_cache();
		self::load_dynamic_constants();
		self::init_session();

		// TODO move in begin
		/* DEPRECATED VARS */
		global $Session, $User, $Template;
		$Session = AppContext::get_session();
		$User = AppContext::get_user();
		$Template = new DeprecatedTemplate();
		/* END DEPRECATED */

		self::init_output_bufferization();
		self::load_lang_files();
		self::process_changeday_tasks_if_needed();
		self::compute_running_module_name();
		self::csrf_protect_post_requests();
		self::enable_errors_and_exceptions_management();
	}

	public static function write_default_http_headers()
	{
		$response = new HTTPResponse();
		$response->set_header('Content-type', 'text/html; charset=iso-8859-1');
		$response->set_header('Expires', 'Mon, 1 Dec 2003 01:00:00 GMT');
		$response->set_header('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
		$response->set_header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->set_header('Cache-Control', 'post-check=0, pre-check=0');
		$response->set_header('Pragma', 'no-cache');
		AppContext::set_response($response);
	}
	
	public static function init_services()
	{
		AppContext::init_session();
	}

	public static function enable_errors_and_exceptions_management()
	{
		set_error_handler(array(new IntegratedErrorHandler(), 'handle'));
		set_exception_handler(array(new ExceptionHandler(), 'handle'));
	}

	public static function fit_to_php_configuration()
	{
		define('ERROR_REPORTING',   E_ALL | E_NOTICE | E_STRICT);
		@error_reporting(ERROR_REPORTING);
		set_error_handler(array(new ErrorHandler(), 'handle'));
		set_exception_handler(array(new RawExceptionHandler(), 'handle'));

		@ini_set('open_basedir', NULL);

		//Disabling magic quotes if possible
		@set_magic_quotes_runtime(0);

		//If the register globals option is enabled, we clear the automatically assigned variables
		if (@ini_get('register_globals') == '1' || strtolower(@ini_get('register_globals')) == 'on')
		{
			require_once PATH_TO_ROOT . '/kernel/framework/util/unusual_functions.inc.php';
			cancel_register_globals_effect();
		}

		if (get_magic_quotes_gpc())
		{
			//If magic_quotes_sybase is enabled
			if (ini_get('magic_quotes_sybase') &&
			(strtolower(ini_get('magic_quotes_sybase')) != "off"))
			{
				//We consider the magic quotes as disabled
				define('MAGIC_QUOTES', false);

				//We treat the content: it must be as if the magic_quotes option is disabled
				foreach ($_REQUEST as $var_name => $value)
				{
					$_REQUEST[$var_name] = str_replace('\'\'', '\'', $value);
				}
			}
			//Magic quotes GPC
			else
			{
				define('MAGIC_QUOTES', true);
			}
		}
		else
		{
			define('MAGIC_QUOTES', false);
		}
	}

	public static function load_static_constants()
	{
		### Common constants ###
		define('GUEST_LEVEL', 		-1);
		define('MEMBER_LEVEL', 		0);
		define('MODO_LEVEL', 		1);
		define('MODERATOR_LEVEL', 	1);
		define('ADMIN_LEVEL', 		2);
		//Path from the server root
		define('SCRIPT', 			$_SERVER['PHP_SELF']);
		//Get parameters
		define('QUERY_STRING', 		addslashes($_SERVER['QUERY_STRING']));
		define('PHPBOOST', 			true);
		define('E_UNKNOWN', 		0);
		define('E_TOKEN', 			-3);
		define('E_USER_REDIRECT', 	-1);
		define('E_USER_SUCCESS', 	-2);
		define('HTML_UNPROTECT', 	false);

		### Authorizations ###
		define('AUTH_MENUS', 		0x01);
		define('AUTH_FILES', 		0x01);
		define('AUTH_MAINTAIN', 	0x01);
		define('ACCESS_MODULE', 	0x01);
		define('AUTH_FLOOD', 		'auth_flood');
		define('PM_GROUP_LIMIT', 	'pm_group_limit');
		define('DATA_GROUP_LIMIT', 	'data_group_limit');

		### Variable types ###
		define('GET', 		1);
		define('POST', 		2);
		define('REQUEST', 	3);
		define('COOKIE', 	4);
		define('FILES', 	5);

		define('TBOOL', 			'boolean');
		define('TINTEGER', 			'integer');
		define('TDOUBLE', 			'double');
		define('TFLOAT', 			'double');
		define('TSTRING', 			'string');
		define('TSTRING_PARSE', 	'string_parse');
		define('TSTRING_UNCHANGE', 	'string_unsecure');
		define('TSTRING_HTML', 		'string_html');
		define('TSTRING_AS_RECEIVED', 'string_unchanged');
		define('TARRAY', 			'array');
		define('TUNSIGNED_INT', 	'uint');
		define('TUNSIGNED_DOUBLE', 	'udouble');
		define('TUNSIGNED_FLOAT', 	'udouble');
		define('TNONE', 			'none');

		define('USE_DEFAULT_IF_EMPTY', 1);

		### User IP address ###
		define('USER_IP', self::get_user_ip());

		### Regex options ###
		define('REGEX_MULTIPLICITY_NOT_USED', 0x01);
		define('REGEX_MULTIPLICITY_OPTIONNAL', 0x02);
		define('REGEX_MULTIPLICITY_REQUIRED', 0x03);
		define('REGEX_MULTIPLICITY_AT_LEAST_ONE', 0x04);
		define('REGEX_MULTIPLICITY_ALL', 0x05);

		@include PATH_TO_ROOT . '/kernel/db/config.php';
	}

	public static function load_cache()
	{
		global $Cache;
		$CONFIG = array();
		$Cache->load('config');
		$Cache->load('themes');
		$Cache->load('langs');
	}

	public static function load_dynamic_constants()
	{
		global $CONFIG;

		define('DIR', $CONFIG['server_path']);
		define('HOST', $CONFIG['server_name']);
		define('TPL_PATH_TO_ROOT', !empty($CONFIG['server_path']) ? $CONFIG['server_path'] : '');
	}

	public static function init_session()
	{
		global $CONFIG, $THEME_CONFIG, $LANGS_CONFIG;
		AppContext::get_session()->load();
		AppContext::get_session()->act();

		AppContext::init_user();

		// TODO do we need to keep that feature? It's not supported every where
		if (AppContext::get_session()->supports_cookies())
		{
			define('SID', 'sid=' . AppContext::get_user()->get_attribute('session_id') .
				'&amp;suid=' . AppContext::get_user()->get_attribute('user_id'));
			define('SID2', 'sid=' . AppContext::get_user()->get_attribute('session_id') .
				'&suid=' . AppContext::get_user()->get_attribute('user_id'));
		}
		else
		{
			define('SID', '');
			define('SID2', '');
		}

		$user_theme = AppContext::get_user()->get_attribute('user_theme');
		//Is that theme authorized for this member? If not, we assign it the default theme
		if (UserAccountsConfig::load()->is_users_theme_forced() || !isset($THEME_CONFIG[$user_theme]['secure'])
		|| !AppContext::get_user()->check_level($THEME_CONFIG[$user_theme]['secure']))
		{
			$user_theme = $CONFIG['theme'];
		}
		//If the user's theme doesn't exist, we assign it a default one which exists
		$user_theme = find_require_dir(PATH_TO_ROOT . '/templates/', $user_theme);
		AppContext::get_user()->set_user_theme($user_theme);

		$user_lang = AppContext::get_user()->get_attribute('user_lang');
		//Is that member authorized to use this lang? If not, we assign it the default lang
		if (!isset($LANGS_CONFIG[$user_lang]['secure']) ||
		!AppContext::get_user()->check_level($LANGS_CONFIG[$user_lang]['secure']))
		{
			$user_lang = $CONFIG['lang'];
		}
		$user_lang = find_require_dir(PATH_TO_ROOT . '/lang/', $user_lang);
		AppContext::get_user()->set_user_lang($user_lang);
	}

	public static function init_output_bufferization()
	{
		global $CONFIG;
		if ($CONFIG['ob_gzhandler'] == 1)
		{
			ob_start('ob_gzhandler');
		}
		else
		{
			ob_start();
		}
	}

	public static function load_lang_files()
	{
		LangLoader::set_locale(get_ulang());

		global $LANG;
		$LANG = array();
		require_once(PATH_TO_ROOT . '/lang/' . get_ulang() . '/main.php');
		require_once(PATH_TO_ROOT . '/lang/' . get_ulang() . '/errors.php');
	}

	public static function process_changeday_tasks_if_needed()
	{
		//If the day changed compared to the last request, we execute the daily tasks

		$last_use_config = LastUseDateConfig::load();
		$last_use_date = $last_use_config->get_last_use_date();
		$current_date = new Date();
		$current_date->set_hours(0);
		$current_date->set_minutes(0);
		$current_date->set_seconds(0);
		if ($last_use_date->is_anterior_to($current_date))
		{

			$lock_file = new File(PATH_TO_ROOT . '/cache/changeday_lock');
			if (!$lock_file->exists())
			{
				$lock_file->write('');
				$lock_file->flush();
			}
			if ($lock_file->lock(false))
			{
				$yesterday_timestamp = self::get_yesterday_timestamp();

				$num_entry_today = AppContext::get_sql()->query("SELECT COUNT(*) FROM " . DB_TABLE_STATS
				. " WHERE stats_year = '" . gmdate_format('Y', $yesterday_timestamp,
				TIMEZONE_SYSTEM) . "' AND stats_month = '" . gmdate_format('m',
				$yesterday_timestamp, TIMEZONE_SYSTEM) . "' AND stats_day = '" . gmdate_format(
				  'd', $yesterday_timestamp, TIMEZONE_SYSTEM) . "'", __LINE__, __FILE__);

				if ((int) $num_entry_today == 0)
				{
					$last_use_config->set_last_use_date(new Date());
					LastUseDateConfig::save();

					self::perform_changeday();
				}
			}
			$lock_file->close();
		}
	}

	private static function perform_changeday()
	{
		self::perform_stats_changeday();

		self::clear_all_temporary_cache_files();

		self::execute_modules_changedays_tasks();

		self::remove_old_unactivated_member_accounts();

		self::remove_captcha_entries();

		self::check_updates();
	}

	private static function perform_stats_changeday()
	{
		$yesterday_timestamp = self::get_yesterday_timestamp();

		//We insert today's entry in the stats table
		AppContext::get_sql()->query_inject("INSERT INTO " . DB_TABLE_STATS . " (stats_year, stats_month, " .
		"stats_day, nbr, pages, pages_detail) VALUES ('" . gmdate_format('Y',
		$yesterday_timestamp, TIMEZONE_SYSTEM) . "', '" . gmdate_format('m', $yesterday_timestamp,
		TIMEZONE_SYSTEM) . "', '" . gmdate_format('d', $yesterday_timestamp, TIMEZONE_SYSTEM) .
		"', 0, 0, '')", __LINE__, __FILE__);

		//We retrieve the id we just come to create
		$last_stats = AppContext::get_sql()->insert_id("SELECT MAX(id) FROM " . PREFIX . "stats");

		AppContext::get_sql()->query_inject("UPDATE " . DB_TABLE_STATS_REFERER .
			" SET yesterday_visit = today_visit", __LINE__, __FILE__);
		AppContext::get_sql()->query_inject("UPDATE " . DB_TABLE_STATS_REFERER .
			" SET today_visit = 0, nbr_day = nbr_day + 1", __LINE__, __FILE__);
		//We delete the referer entries older than one week
		AppContext::get_sql()->query_inject("DELETE FROM " . DB_TABLE_STATS_REFERER .
		" WHERE last_update < '" . (self::get_yesterday_timestamp()) . "'", __LINE__, __FILE__);

		//We retrieve the number of pages seen until now
		$pages_displayed = StatsSaver::retrieve_stats('pages');

		//We delete the file containing the displayed pages

		$pages_file = new File(PATH_TO_ROOT . '/cache/pages.txt');
		$pages_file->delete();

		//How much visitors were there today?
		$total_visit = AppContext::get_sql()->query("SELECT total FROM " . DB_TABLE_VISIT_COUNTER .
			" WHERE id = 1", __LINE__, __FILE__);
		//We truncate the table containing the visitors of today
		AppContext::get_sql()->query_inject("DELETE FROM " . DB_TABLE_VISIT_COUNTER .
			" WHERE id <> 1", __LINE__, __FILE__);
		//We update the last changeday date
		AppContext::get_sql()->query_inject("UPDATE " . DB_TABLE_VISIT_COUNTER .
			" SET time = '" . gmdate_format('Y-m-d', time(), TIMEZONE_SYSTEM) .
				"', total = 1 WHERE id = 1", __LINE__, __FILE__);
		//We insert this visitor as a today visitor
		AppContext::get_sql()->query_inject("INSERT INTO " . DB_TABLE_VISIT_COUNTER .
			" (ip, time, total) VALUES('" . USER_IP . "', '" . gmdate_format('Y-m-d', time(),
		TIMEZONE_SYSTEM) . "', '0')", __LINE__, __FILE__);

		//We update the stats table: the number of visits today
		AppContext::get_sql()->query_inject("UPDATE " . DB_TABLE_STATS . " SET nbr = '" . $total_visit .
		"', pages = '" . array_sum($pages_displayed) . "', pages_detail = '" .
		addslashes(serialize($pages_displayed)) . "' WHERE id = '" . $last_stats . "'",
		__LINE__, __FILE__);

		//Deleting all the invalid sessions
		AppContext::get_session()->garbage_collector();
	}

	private static function clear_all_temporary_cache_files()
	{
		//We delete all the images generated by the LaTeX formatter

		$cache_image_folder_path = new Folder(PATH_TO_ROOT . '/images/maths/');
		foreach ($cache_image_folder_path->get_files('`\.png$`') as $image)
		{
			if ($image->get_last_modification_date() < self::get_one_week_ago_timestamp())
			{
				$image->delete();
			}
		}
	}

	private static function execute_modules_changedays_tasks()
	{

		$modules_loader = new ModulesDiscoveryService();
		$modules = $modules_loader->get_available_modules('on_changeday');
		foreach ($modules as $module)
		{
			if ($module->is_enabled())
			{
				$module->functionality('on_changeday');
			}
		}
	}

	private static function remove_old_unactivated_member_accounts()
	{
		$user_account_settings = UserAccountsConfig::load();

		$delay_unactiv_max = $user_account_settings->get_unactivated_accounts_timeout() * 3600 * 24;
		//If the user configured a delay and member accounts must be activated
		if ($delay_unactiv_max > 0 && $user_account_settings->get_member_accounts_validation_method() != 2)
		{
			AppContext::get_sql()->query_inject("DELETE FROM " . DB_TABLE_MEMBER .
				" WHERE timestamp < '" . (time() - $delay_unactiv_max) .
				"' AND user_aprob = 0", __LINE__, __FILE__);
		}
	}

	private static function remove_captcha_entries()
	{
		AppContext::get_sql()->query_inject("DELETE FROM " . DB_TABLE_VERIF_CODE .
			" WHERE timestamp < '" . (self::get_yesterday_timestamp()) . "'",
		__LINE__, __FILE__);
	}

	private static function check_updates()
	{
		new Updates();
	}

	public static function compute_running_module_name()
	{
		$path = str_replace(DIR, '', SCRIPT);
		$path = trim($path, '/');
		if (strpos($path, '/'))
		{
			$module_name = explode('/', $path);
			self::$running_module_name = $module_name[0];
		}
		else
		{
			self::$running_module_name = '';
		}
	}
	
	/**
	 * @desc Retrieves the identifier (name of the folder) of the module which is currently executed.
	 * @return string The module identifier.
	 */
	public static function get_running_module_name()
	{
		return self::$running_module_name;
	}

	public static function csrf_protect_post_requests()
	{
		// Verify that the user really wanted to do this POST (only for the registered ones)
		if (AppContext::get_user()->check_level(MEMBER_LEVEL))
		{
			AppContext::get_session()->csrf_post_protect();
		}
	}

	/**
	 * @desc Retrieves the site start page.
	 * @return The absolute start page URL.
	 */
	public static function get_home_page()
	{
		global $CONFIG;
	
		return (substr($CONFIG['start_page'], 0, 1) == '/') ? url(HOST . DIR . $CONFIG['start_page']) : $CONFIG['start_page'];
	}
	
	/**
	 * @desc Returns the full phpboost version with its build number
	 * @return string the full phpboost version with its build number
	 */
	public static function get_phpboost_version()
	{
		global $CONFIG;

		$file = new File(PATH_TO_ROOT . '/kernel/.build');
		$build =  $file->read();
		$file->close();
		return $CONFIG['version'] . '.' . trim($build);
	}

	/**
	 * Displays the top of the page.
	 */
	public static function display_header()
	{
		
		self::get_graphical_environment()->display_header();
	}

	/**
	 * Displays the bottom of the page.
	 */
	public static function display_footer()
	{
		self::get_graphical_environment()->display_footer();
	}

	public static function set_graphical_environment(GraphicalEnvironment $env)
	{
		self::$graphical_environment = $env;
	}

	public static function destroy()
	{
		AppContext::close_db_connection();

		ob_end_flush();
	}

	private static function get_yesterday_timestamp()
	{
		return time() - 86400;
	}

	private static function get_one_week_ago_timestamp()
	{
		return time() - 3600 * 24 * 7;
	}

	private static function get_user_ip()
	{
		if ($_SERVER)
		{
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			elseif (isset($_SERVER['HTTP_CLIENT_IP']))
			{
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
			else
			{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		}
		else
		{
			if (getenv('HTTP_X_FORWARDED_FOR'))
			{
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			}
			elseif (getenv('HTTP_CLIENT_IP'))
			{
				$ip = getenv('HTTP_CLIENT_IP');
			}
			else
			{
				$ip = getenv('REMOTE_ADDR');
			}
		}

		if (preg_match('`^[a-z0-9:.]{7,}$`', $ip))
		{
			return $ip;
		}
		else
		{
			return '0.0.0.0';
		}
	}

	/**
	 * @return GraphicalEnvironment
	 */
	private static function get_graphical_environment()
	{
		if (self::$graphical_environment === null)
		{
			//Default graphical environment

			self::$graphical_environment = new SiteDisplayGraphicalEnvironment();
		}
		return self::$graphical_environment;
	}
}

?>