<?php
/*##################################################
 *                             functions.inc.php
 *                            -------------------
 *   begin                : June 13, 2005
 *   copyright            : (C) 2005 R�gis Viarre, Lo�c Rouchon
 *   email                : crowkait@phpboost.com, horn@phpboost.com
 *
 *   Function 2.0.0
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

define('HTML_NO_PROTECT', false);
define('HTML_PROTECT', true);
//Automatique : �chappe seulement si le serveur n'�chappe pas automatiquement
define('ADDSLASHES_AUTO', 0);
//Force l'�chappement des caract�res critique
define('ADDSLASHES_FORCE', 1);
//Aucun �chappement
define('ADDSLASHES_NONE', 2);
define('MAGIC_QUOTES_DISABLED', false);
define('NO_UPDATE_PAGES', true);
define('NO_FATAL_ERROR', false);
define('NO_EDITOR_UNPARSE', false);
define('TIMEZONE_SITE', 1);
define('TIMEZONE_SYSTEM', 2);
define('TIMEZONE_USER', 3);

//R�cup�re les superglobales
function retrieve($var_type, $var_name, $default_value, $force_type = NULL)
{
	switch ($var_type)
	{
        case GET:
            if (isset($_GET[$var_name]))
                $var = $_GET[$var_name];
            else
                return $default_value;
            break;
        case POST:
            if (isset($_POST[$var_name]))
                $var = $_POST[$var_name];
            else
                return $default_value;
            break;
		case REQUEST:
            if (isset($_REQUEST[$var_name]))
                $var = $_REQUEST[$var_name];
            else
                return $default_value;
            break;
        case COOKIE:
            if (isset($_COOKIE[$var_name]))
                $var = $_COOKIE[$var_name];
            else
                return $default_value;
            break;
        case FILES:
            if (isset($_FILES[$var_name]))
                $var = $_FILES[$var_name];
            else
                return $default_value;
            break;
        default:
            return;
	}
	
	$force_type = !isset($force_type) ? gettype($default_value) : $force_type;
	switch ($force_type)
	{
		case TINTEGER:
			return (int)$var;
		case TSTRING:
			return strprotect($var); //Chaine prot�g�e.
		case TSTRING_UNCHANGE:
			if (MAGIC_QUOTES)
				$var = trim(stripslashes($var));
			else
				$var = trim($var);
			return (string)$var; //Chaine non prot�g�e.
		case TSTRING_PARSE:
			return strparse($var); //Chaine pars�e.
		case TBOOL:
			return (bool)$var;
		case TUNSIGNED_INT:
			$var = (int)$var;
			return $var > 0 ? $var : max(0, $default_value);
		case TUNSIGNED_DOUBLE:
			$var = (double)$var;
			return $var > 0.0 ? $var : max(0.0, $default_value);
		case TSTRING_HTML:
			return strprotect($var, HTML_NO_PROTECT); //Chaine non prot�g�e pour l'html.
		case TSTRING_AS_RECEIVED:
			return (string)$var;
		case TARRAY:
			return (array)$var;
	    case TDOUBLE:
			return (double)$var;
		case TNONE: // ajout par alain91
			return $var;
	}
	
	return;
}

//Passe � la moulinette les entr�es (cha�nes) utilisateurs.
function strprotect($var, $html_protect = HTML_PROTECT, $addslashes = ADDSLASHES_AUTO)
{
    $var = trim((string)$var);
    
    //Protection contre les balises html.
    if ($html_protect)
        $var = htmlspecialchars($var);
    
	switch ($addslashes)
	{
		case ADDSLASHES_FORCE:
			//On force l'�chappement de caract�res
				$var = addslashes($var);
			break;
		case ADDSLASHES_NONE:
		    //On ne touche pas la cha�ne
			$var = stripslashes($var);
			break;
		//Mode automatique
		case ADDSLASHES_AUTO:
		default:
			//On �chappe les ' si la fonction magic_quotes_gpc() n'est pas activ�e sur le serveur.
			if (!MAGIC_QUOTES)
				$var = addslashes($var);
	}

    return $var;
}

//V�rifie les entr�es numeriques.
function numeric($var, $type = 'int')
{
    if (is_numeric($var)) //Retourne un nombre
    {
        if ($type === 'float')
            return (float)$var; //Nbr virgule flottante.
        else
            return (int)$var; //Nombre entier
    }
    else
        return 0;
}

//Unserialisation.
function sunserialize($content)
{
	if (MAGIC_QUOTES == false)
		return unserialize($content);
	else
		return unserialize(stripslashes($content));
}

//R�cup�re le th�me utilisateur
function get_utheme()
{
    global $User;
	return !empty($User) != null ? $User->get_attribute('user_theme') : 'default';
}

//R�cup�re le th�me utilisateur
function get_ulang()
{
    global $User;
	return $User->get_attribute('user_lang');
}

//D�coupage avec retour � la ligne, d'une cha�ne, en prenant compte les entit�s html.
function wordwrap_html(&$str, $lenght, $cut_char = '<br />', $boolean = true)
{
    $str = wordwrap(html_entity_decode($str), $lenght, $cut_char, $boolean);
    return str_replace('&lt;br /&gt;', '<br />', htmlspecialchars($str, ENT_NOQUOTES));
}

//D�coupe d'une cha�ne, en prenant compte les entit�s html.
function substr_html(&$str, $start, $end = '')
{
    if ($end == '')
        return htmlspecialchars(substr(html_entity_decode($str), $start), ENT_NOQUOTES);
    else
        return htmlspecialchars(substr(html_entity_decode($str), $start, $end), ENT_NOQUOTES);
}

//Affichage de l'�diteur de contenu.
function display_editor($field = 'contents', $forbidden_tags = array())
{
	$content_editor = new ContentFormattingFactory();
	$editor = $content_editor->get_editor();
	if (!empty($forbidden_tags) && is_array($forbidden_tags))
		$editor->set_forbidden_tags($forbidden_tags);
	$editor->set_identifier($field);
	
	return $editor->display();
}

//Affichage des commentaires.
function display_comments($script, $idprov, $vars, $module_folder = '')
{

	import('content/comments');
	$comments = new Comments($script, $idprov, $vars, $module_folder);
	
	return $comments->display();
}

//Chercher le dossier langue d'un module, s'il n'est pas trouv� on retourne la premi�re langue.
function load_module_lang($module_name)
{
    global $LANG;

	$file = PATH_TO_ROOT . '/' . $module_name . '/lang/' . get_ulang() . '/' . $module_name . '_' . get_ulang() . '.php';
	if (!DEBUG) {
		$result = @include_once($file);
	} else {
		$result = include_once($file);
	}
    if (!$result)
    {
        $lang = find_require_dir(PATH_TO_ROOT . '/' . $module_name . '/lang/', get_ulang(), NO_FATAL_ERROR);
		$file2 = PATH_TO_ROOT . '/' . $module_name . '/lang/' . $lang . '/' . $module_name . '_' . $lang . '.php';
		if (!DEBUG) {
			$result2 = @include_once($file2);
		} else {
			$result2 = include_once($file2);
		}
        if (!$result2)
        {
            global $Errorh;
            
            //D�clenchement d'une erreur fatale.
            $Errorh->handler(sprintf('Unable to load lang file \'%s\'!', PATH_TO_ROOT . '/' . $module_name . '/lang/' . $lang . '/' . $module_name . '_' . $lang . '.php'), E_USER_ERROR, __LINE__, __FILE__);
            exit;
        }
    }
}

//Charge un fichier de configuration. S'il n'est pas trouv�, on parcourt le dossier pass� en argument � la recherche du premier dossier.
function load_ini_file($dir_path, $require_dir, $ini_name = 'config.ini')
{
    $dir = find_require_dir($dir_path, $require_dir, false);
	$file = $dir_path . $require_dir . '/' . $ini_name;
	if (!DEBUG) {
		$result = @parse_ini_file($file);
	} elseif(file_exists($file)) {
		$result = parse_ini_file($file);
	} else {
		$result = FALSE;
	}
    return $result;
}

//Parcours d'une chaine sous la forme d'un simili tableau php. Retourne un tableau correctement construit.
function parse_ini_array($links_format)
{
    $links_format = preg_replace('` ?=> ?`', '=', $links_format);
    $links_format = preg_replace(' ?, ?', ',', $links_format) . ' ';
    list($key, $value, $open, $cursor, $check_value, $admin_links) = array('', '', '', 0, false, array());
    $string_length = strlen($links_format);
    while ($cursor < $string_length) //Parcours lin�aire.
    {
        $char = substr($links_format, $cursor, 1);
        if (!$check_value) //On r�cup�re la cl�.
        {
            if ($char != '=')
                $key .= $char;
            else
                $check_value =  true;
        }
        else //On r�cup�re la valeur associ� � la cl�, une fois celle-ci r�cup�r�e.
        {
            if ($char == '(') //On marque l'ouverture de la parenth�se.
                $open = $key;
            
            if ($char != ',' && $char != '(' && $char != ')' && ($cursor+1) < $string_length) //Si ce n'est pas un caract�re d�limiteur, on la fin => on concat�ne.
                $value .= $char;
            else
            {
                if (!empty($open) && !empty($value)) //On ins�re dans la cl� marqu� pr�c�demment � l'ouveture de la parenth�se.
                    $admin_links[$open][$key] = $value;
                else
                    $admin_links[$key] = $value; //Ajout simple.
                list($key, $value, $check_value) = array('', '', false);
            }
            if ($char == ')')
            {
                $open = ''; //On supprime le marqueur.
                $cursor++; //On avance le curseur pour faire sauter la virugle apr�s la parenth�se.
            }
        }
        $cursor++;
    }
    return $admin_links;
}

//R�cup�ration du dernier champ de configuration du config.ini du module.
function get_ini_config($dir_path, $require_dir, $ini_name = 'config.ini')
{
    $dir = find_require_dir($dir_path, $require_dir, false);
    import('io/filesystem/file');
    
    $module_config_file = new File($dir_path . $dir . '/config.ini', READ);
    $module_config_file->open();
    $module_config_text = $module_config_file->get_contents();
    
    //Maintenant qu'on a le contenu du fichier, on tente d'extraire la derni�re ligne qui est comment�e car sa syntaxe est incorrecte
    $result = array();
    
    //Si on d�tecte le bon motif, on le renvoie
    if (preg_match('`;config="(.*)"\s*$`s', $module_config_text, $result))
    {
        return $result[1];
    }
    //Sinon, on renvoie une cha�ne vide
    else
    {
        return '';
    }
}

//Cherche un dossier s'il n'est pas trouv�, on parcourt le dossier pass� en argument � la recherche du premier dossier.
function find_require_dir($dir_path, $require_dir, $fatal_error = true)
{
    //Si le dossier de langue n'existe pas on prend le suivant exisant.
    if (!@file_exists($dir_path . $require_dir))
    {
        if (@is_dir($dir_path) && $dh = @opendir($dir_path)) //Si le dossier existe et qu'on a les permissions suffisantes
        {
            while (!is_bool($dir = readdir($dh)))
            {
                if (strpos($dir, '.') === false  )
				{
					closedir($dh);
                    return $dir;
				}
            }
            closedir($dh);
        }
    }
    else
        return $require_dir;
        
    if ($fatal_error)
    {
        global $Errorh;
    
        //D�clenchement d'une erreur fatale.
        $Errorh->handler(sprintf('Unable to load required directory \'%s\'!', $dir_path . $require_dir), E_USER_ERROR, __LINE__, __FILE__);
        exit;
    }
}

//R�cup�re le dossier du module.
function get_module_name()
{
	$module_name = explode('/', SCRIPT);
	array_pop($module_name);
	
	return array_pop($module_name);
}

//Redirection.
function redirect($url)
{
    global $Sql;
    
    if (!empty($Sql) && is_object($Sql)) //Coupure de la connexion mysql.
        $Sql->close();
        
    header('Location:' . $url);
    exit;
}

//Redirection.
function redirect_confirm($url_error, $l_error, $delay_redirect = 3)
{
	global $LANG;
	
	$template = new Template('framework/confirm.tpl');
	
	$template->assign_vars(array(
		'URL_ERROR' => !empty($url_error) ? $url_error : get_start_page(),
		'DELAY_REDIRECT' => $delay_redirect,
		'L_ERROR' => $l_error,
		'L_REDIRECT' => $LANG['redirect']
	));
	
	return $template->parse();
}

//R�cup�ration de la page de d�marrage du site.
function get_start_page()
{
    global $CONFIG;
    
    $start_page = (substr($CONFIG['start_page'], 0, 1) == '/') ? url(HOST . DIR . $CONFIG['start_page']) : $CONFIG['start_page'];
    return $start_page;
}

//V�rifie que le message ne contient pas plus de x liens
function check_nbr_links($contents, $max_nbr)
{
    if ($max_nbr == -1)
        return true;
    
    $nbr_link = preg_match_all('`(?:ftp|https?)://`', $contents, $array);
    if ($nbr_link !== false && $nbr_link > $max_nbr)
        return false;
    
    return true;
}

//This will be a static method of the class when we will be only in PHP 5 :)
function com_display_link($nbr_com, $path, $idprov, $script, $options = 0)
{
	global $CONFIG, $LANG;
	
	$link = '';
	$l_com = ($nbr_com > 1) ? $LANG['com_s'] : $LANG['com'];
	$l_com = !empty($nbr_com) ? $l_com . ' (' . $nbr_com . ')' : $LANG['post_com'];
	
	$link_pop = "#\" onclick=\"popup('" . HOST . DIR . url('/kernel/framework/ajax/pop_up_comments.php?com=' . $idprov . $script) . "&path_to_root=" . PATH_TO_ROOT . "', '" . $script . "');";
	$link_current = $path . '#anchor_' . $script;
	
	$link .= '<a class="com" href="' . (($CONFIG['com_popup'] == '0') ? $link_current : $link_pop) . '">' . $l_com . '</a>';
	
	return $link;
}

//V�rifie la validit� du mail
function check_mail($mail)
{
	return preg_match('`^[a-z0-9._-]+@(?:[a-z0-9_-]{2,}\.)+[a-z]{2,4}$`i', $mail);
}

//Charge le parseur.
function strparse(&$content, $forbidden_tags = array(), $addslashes = true)
{
	//On utilise le gestionnaire de contenu
    $content_manager = new ContentFormattingFactory();
    //On lui demande le parser ad�quat
	$parser = $content_manager->get_parser();
	
	//On assigne le contenu � interpr�ter. Il supprime les antislashes d'�chappement seulement si ils ont �t� ajout�s par magic_quotes
    $parser->set_content($content, MAGIC_QUOTES);
    
    //Si il y a des balises interdites, on lui signale
	if (!empty($forbidden_tags))
		$parser->set_forbidden_tags($forbidden_tags);
	//Au travail maintenant !
    $parser->parse();
	
    //Renvoie le r�sultat. Echappe par d�faut les caract�res critiques afin d'�tre envoy� en base de donn�es
	return $parser->get_content($addslashes);
}

//Charge l'unparseur.
function unparse(&$content)
{
	$content_manager = new ContentFormattingFactory();
	$parser = $content_manager->get_unparser();
    $parser->set_content($content, PARSER_DO_NOT_STRIP_SLASHES);
    $parser->unparse();
	
	return $parser->get_content(DO_NOT_ADD_SLASHES);
}

//Parse temps r�el
function second_parse(&$content)
{
	$content_manager = new ContentFormattingFactory();
	
	$parser = $content_manager->get_second_parser();
    $parser->set_content($content, PARSER_DO_NOT_STRIP_SLASHES);
    $parser->second_parse();
	
    return $parser->get_content(DO_NOT_ADD_SLASHES);
}

//Transmet le session_id et le user_id � traver l'url pour les connexions sans cookies. Permet le support de l'url rewritting!
function url($url, $mod_rewrite = '', $ampersand = '&amp;')
{
    global $CONFIG, $Session;
    
    if (!is_object($Session))
        $session_mod = 0;
    else
        $session_mod = $Session->session_mod;
        
    if ($session_mod == 0)
    {
        if ($CONFIG['rewrite'] == 1 && !empty($mod_rewrite)) //Activation du mod rewrite => cookies activ�s.
            return $mod_rewrite;
        else
            return $url;
    }
    elseif ($session_mod == 1)
        return $url . ((strpos($url, '?') === false) ? '?' : $ampersand) . 'sid=' . $Session->data['session_id'] . $ampersand . 'suid=' . $Session->data['user_id'];
}

//Nettoie l'url de tous les caract�res sp�ciaux, accents, etc....
function url_encode_rewrite($string)
{
    $string = strtolower(html_entity_decode($string));
	$string = strtr($string, ' ������������', '-eeeaauuuiioc');
    $string = preg_replace('`([^a-z0-9]|[\s])`', '-', $string);
    $string = preg_replace('`[-]{2,}`', '-', $string);
    $string = trim($string, ' -');
    
    return $string;
}

//Formate la date au format GMT, suivant la configuration du fuseau horaire du serveur.
function gmdate_format($format, $timestamp = false, $timezone_system = 0)
{
    global $User, $CONFIG, $LANG;
    
    if (strpos($format, 'date_format') !== false) //Inutile de tout tester si ce n'est pas un formatage pr�d�fini.
    {
        switch ($format)
        {
            case 'date_format':
            $format = $LANG['date_format'];
            break;
            case 'date_format_tiny':
            $format = $LANG['date_format_tiny'];
            break;
            case 'date_format_short':
            $format = $LANG['date_format_short'];
            
            break;
            case 'date_format_long':
            $format = $LANG['date_format_long'];
            break;
        }
    }
    
    if ($timestamp === false)
        $timestamp = time();

    // D�callage du serveur par rapport au m�ridien de greenwitch et � l'heure d'�t�
    $serveur_hour = number_round(date('Z')/3600, 0) - date('I');
    
    if ($timezone_system == 1) //Timestamp du site, non d�pendant de l'utilisateur.
        $timezone = $CONFIG['timezone'] - $serveur_hour;
    elseif ($timezone_system == 2) //Timestamp du serveur, non d�pendant de l'utilisateur et du fuseau par d�faut du site.
        $timezone = 0;
    else //Timestamp utilisateur d�pendant de la localisation de l'utilisateur par rapport � serveur.
        $timezone = $User->get_attribute('user_timezone') - $serveur_hour;

    if ($timezone != 0)
        $timestamp += $timezone * 3600;
    
    if ($timestamp <= 0)
        return '';
        
    return date($format, $timestamp);
}

//Convertit une cha�ne au format $LANG['date_format'] (ex:d/m/y) en timestamp, si la date saisie est valide sinon retourne 0.
function strtotimestamp($str, $date_format)
{
    global $CONFIG, $User;
    
    list($month, $day, $year) = array(0, 0, 0);
    $array_timestamp = explode('/', $str);
    $array_date = explode('/', $date_format);
    for ($i = 0; $i < 3; $i++)
    {
        switch ($array_date[$i])
        {
            case 'd':
            $day = (isset($array_timestamp[$i])) ? numeric($array_timestamp[$i]) : 0;
            break;
            case 'm':
            $month = (isset($array_timestamp[$i])) ? numeric($array_timestamp[$i]) : 0;
            break;
            case 'y':
            $year = (isset($array_timestamp[$i])) ? numeric($array_timestamp[$i]) : 0;
            break;
        }
    }

    //V�rification du format de la date.
    if (checkdate($month, $day, $year))
        $timestamp = @mktime(0, 0, 1, $month, $day, $year);
	else
        $timestamp = time();
	
    $serveur_hour = number_round(date('Z')/3600, 0) - date('I'); //D�callage du serveur par rapport au m�ridien de greenwitch.
    $timezone = $User->get_attribute('user_timezone') - $serveur_hour;
    if ($timezone != 0)
        $timestamp -= $timezone * 3600;
        
    return ($timestamp > 0) ? $timestamp : time();
}

//Convertit une cha�ne au format $LANG['date_format'] (ex:DD/MM/YYYY) en type DATE, si la date saisie est valide sinon retourne 0000-00-00.
function strtodate($str, $date_format)
{
    list($month, $day, $year) = array(0, 0, 0);
    $array_date = explode('/', $str);
    $array_format = explode('/', $date_format);
    for ($i = 0; $i < 3; $i++)
    {
        switch ($array_format[$i])
        {
            case 'DD':
            $day = (isset($array_date[$i])) ? numeric($array_date[$i]) : 0;
            break;
            case 'MM':
            $month = (isset($array_date[$i])) ? numeric($array_date[$i]) : 0;
            break;
            case 'YYYY':
            $year = (isset($array_date[$i])) ? numeric($array_date[$i]) : 0;
            break;
        }
    }
    
    //V�rification du format de la date.
    if (checkdate($month, $day, $year))
        $date = $year . '-' . $month . '-' . $day;
    else
        $date = '0000-00-00';
        
    return $date;
}

//Suppression d'un fichier avec gestion des erreurs.
function delete_file($file)
{
    global $LANG;
    
    if (function_exists('unlink'))
        if (file_exists($file))
            return @unlink($file); //On supprime le fichier.
    else
        return false;
}

//Fonction r�cursive de suppression de dossier.
function delete_directory($dir_path, $path)
{
    $dir = dir($path);
    if (!is_object($dir))
        return false;
        
    while ($file = $dir->read())
    {
        if ($file != '.' && $file != '..')
        {
            $path_file = $path . '/' . $file;
            if (is_file($path_file))
                if (!@unlink($path_file))
                    return false;
            elseif (is_dir($path_file))
            {
                delete_directory($dir_path, $path_file);
                if (!@rmdir($path_file))
                    return false;
            }
        }
    }
    
    //Fermeture du dossier et suppression de celui-ci.
    if (!$file)
    {
        $dir->close();
        if (@rmdir($dir_path))
            return true;
    }
    return false;
}

//Compte le nombre de page vues.
function pages_displayed($no_update = false)
{
    if ($file = @fopen(PATH_TO_ROOT . '/cache/pages.txt', 'r+'))
    {
        $hour = gmdate_format('G');
        $data = unserialize(fgets($file, 4096)); //Renvoi la premi�re ligne du fichier (le array pr�c�dement cr�e).
        if (!$no_update)
        {
            if (isset($data[$hour])) //Robo repasse.
                $data[$hour]++; //Nbr de vue.
            else
                $data[$hour] = 1;
        }
        
        rewind($file);
        fwrite($file, serialize($data)); //On stock le tableau dans le fichier de donn�es
        fclose($file);
    }
    else if ($file = @fopen(PATH_TO_ROOT . '/cache/pages.txt', 'w+')) //Si le fichier n'existe pas on le cr�e avec droit d'�criture et lecture.
    {
        $data = array();
        fwrite($file, serialize($data)); //On ins�re un tableau vide.
        fclose($file);
    }
    
    return $data;
}

//Arrondi nbr au nbr de d�cimal voulu
function number_round($number, $dec)
{
    return trim(number_format($number, $dec, '.', ''));
}

//Remplacement de la fonction file_get_contents.
function file_get_contents_emulate($filename, $incpath = false, $resource_context = null)
{
    if (false === ($fh = @fopen($filename, 'rb', $incpath)))
    {
        user_error('file_get_contents_emulate() failed to open stream: No such file or directory', E_USER_WARNING);
        return false;
    }

    clearstatcache();
    if ($fsize = @filesize($filename))
        $data = fread($fh, $fsize);
    else
    {
        $data = '';
        while (!feof($fh))
            $data .= fread($fh, 8192);
    }
    fclose($fh);
    return $data;
}

//Emulation de la fonction PHP5 html_entity_decode().
if (!function_exists('html_entity_decode'))
{
    function html_entity_decode($string, $quote_style = ENT_COMPAT, $charset = null)
    {
        if (!is_int($quote_style))
        {
            user_error('html_entity_decode() expects parameter 2 to be long, ' .
            gettype($quote_style) . ' given', E_USER_WARNING);
            return;
        }

        $trans_tbl = array_flip(get_html_translation_table(HTML_ENTITIES));

        // Add single quote to translation table;
        $trans_tbl['&#039;'] = '\'';

        // Not translating double quotes
        if ($quote_style & ENT_NOQUOTES)
            unset($trans_tbl['&quot;']); // Remove double quote from translation table
        return strtr($string, $trans_tbl);
    }
}

//Emulation de la fonction PHP5 htmlspecialchars_decode().
if (!function_exists('htmlspecialchars_decode'))
{
    function htmlspecialchars_decode($string, $quote_style = null)
    {
        // Sanity check
        if (!is_scalar($string))
        {
            user_error('htmlspecialchars_decode() expects parameter 1 to be string, ' . gettype($string) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_int($quote_style) && $quote_style !== null)
        {
            user_error('htmlspecialchars_decode() expects parameter 2 to be integer, ' . gettype($quote_style) . ' given', E_USER_WARNING);
            return;
        }

        // Init
        $from = array('&amp;', '&lt;', '&gt;');
        $to = array('&', '<', '>');
        
        // The function does not behave as documented
        // This matches the actual behaviour of the function
        if ($quote_style & ENT_COMPAT || $quote_style & ENT_QUOTES)
        {
            $from[] = '&quot;';
            $to[] = '"';
            
            $from[] = '&#039;';
            $to[] = "'";
        }

        return str_replace($from, $to, $string);
    }
}

//Emulation de la fonction PHP5 array_combine
if (!function_exists('array_combine'))
{
    function array_combine($keys, $values)
    {
        if (!is_array($keys))
        {
            user_error('array_combine() expects parameter 1 to be array, ' .
                gettype($keys) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_array($values))
        {
            user_error('array_combine() expects parameter 2 to be array, ' .
                gettype($values) . ' given', E_USER_WARNING);
            return;
        }

        $key_count = count($keys);
        $value_count = count($values);
        if ($key_count !== $value_count) {
            user_error('array_combine() Both parameters should have equal number of elements', E_USER_WARNING);
            return false;
        }

        if ($key_count === 0 || $value_count === 0)
        {
            user_error('array_combine() Both parameters should have number of elements at least 0', E_USER_WARNING);
            return false;
        }

        $keys = array_values($keys);
        $values  = array_values($values);

        $combined = array();
        for ($i = 0; $i < $key_count; $i++)
        {
            $combined[$keys[$i]] = $values[$i];
        }

        return $combined;
    }
}

// Return the html string that print the menu to choose the wanted feed's type
function get_feed_menu($feed_url)
{
    global $LANG, $CONFIG;
    import('io/template');
    $feedMenu = new Template('framework/content/syndication/menu.tpl');
    $feedMenu->assign_vars(array(
        'PATH_TO_ROOT' => PATH_TO_ROOT,
		'PATH_TO_MENU' => dirname($feedMenu->tpl),
        'THEME' => get_utheme(),
        'U_FEED' => trim($CONFIG['server_name'], '/') . '/' . (!empty($CONFIG['server_path']) ? trim($CONFIG['server_path'], '/') . '/' : '') . trim($feed_url, '/'),
        'L_RSS' => $LANG['rss'],
        'L_ATOM' => $LANG['atom']
    ));
    return $feedMenu->parse(TEMPLATE_STRING_MODE);
}


/**
 * @desc Return a SHA256 hash of the $str string [with a salt]
 * @param string $str the string to hash
 * @param mixed $salt If true, add the default salt : md5($str)
 * if a string, use this string as the salt
 * if false, do not use any salt
 * @return string a SHA256 hash of the $str string [with a salt]
 */
function strhash($str, $salt = true)
{
    
    if ($salt === true)
    {   // Default salt
        $str = md5($str) . $str;
    }
    elseif ($salt !== false)
    {   // Specific salt
        $str = $salt . $str;
    }
        
    if (phpversion() >= '5.1.2')
    {   // PHP5 Primitive
        return hash('sha256', $str);
    }
    else
    {   // With PHP4
        import('util/sha256');
        return SHA256::hash($str);
    }
}

// Returns a Unique Identifier in the whole application
function get_uid()
{
    static $uid = 1764;
    return $uid++;
}

// Returns a HTML unique Identifier.
// You could use it to define all your block's id
// and use it with no issues in javascript
function get_html_uid($prefix = 'html_uid_')
{
    return $prefix . get_uid();
}

define('CLASS_IMPORT', '.class.php');
define('LIB_IMPORT', '.inc.php');

/**
 * @desc import a class or a lib from the framework
 * @param string $path the class or lib path and its name withour .class.php or
 * .inc.php extensionlike content/bbcode_parser
 * @param string $import_type the import type. Default is CLASS_IMPORT,
 * but you could also import a library by using LIB_IMPORT
 */
function import($path, $import_type = CLASS_IMPORT)
{
	require_once(PATH_TO_ROOT . '/kernel/framework/' . $path . $import_type);
}

/**
 * @desc Require a file
 * @param string $file the file to require with an absolute path from the website root
 * @param bool $once if false use require instead of require_once
 */
function req($file, $once = true)
{
    if ($once)
        require_once(PATH_TO_ROOT . $file) !== false;
    else
        return (require PATH_TO_ROOT . $file) !== false;
}

/**
 * @desc Include a file
 * @param string $file the file to include with an absolute path from the website root
 * @param bool $once if false use include instead of include_once
 * @return bool true if the file have been included with success else, false
 */
function inc($file, $once = true)
{
    if ($once)
        return include_once(PATH_TO_ROOT . $file) !== false;
    return (include PATH_TO_ROOT . $file) !== false;
}

/**
 * @desc Tells if an object is an instance of a class
 * @param object $object the object to check its type
 * @param string $classname the classname you want to compare with
 * @return bool true if the $object is an instance of the $classname class
 */
function of_class(&$object, $classname)
{
    return strtolower(get_class($object)) == strtolower($classname);
}

/**
 * @param string $string A PHP string to convert to a JS one
 * @return string The js equivalent string
 */
function to_js_string($string)
{
    return '\'' . str_replace(array("\r\n", "\r", "\n"), array('\n', '\n', '\n'),
        addcslashes($string, '\'')) . '\'';
}

?>
