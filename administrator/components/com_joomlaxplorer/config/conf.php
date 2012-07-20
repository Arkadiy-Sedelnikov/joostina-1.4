<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

//------------------------------------------------------------------------------
// Configuration Variables
global $mosConfig_joomlaxplorer_dir;
// login to use joomlaXplorer: (true/false)
$GLOBALS["require_login"] = false;

$GLOBALS["language"] = $mosConfig_lang;

// the filename of the QuiXplorer script: (you rarely need to change this)
if($_SERVER['SERVER_PORT'] == 443){
	$GLOBALS["script_name"] = "https://" . $GLOBALS['__SERVER']['HTTP_HOST'] . $GLOBALS['__SERVER']["PHP_SELF"];
} else{
	$GLOBALS["script_name"] = "http://" . $GLOBALS['__SERVER']['HTTP_HOST'] . $GLOBALS['__SERVER']["PHP_SELF"];
}

// allow Zip, Tar, TGz -> Only (experimental) Zip-support
if(function_exists("gzcompress")){
	$GLOBALS["zip"] = $GLOBALS["tgz"] = true;
} else{
	$GLOBALS["zip"] = $GLOBALS["tgz"] = false;
}

if(strstr(JPATH_BASE, "/")){
	$GLOBALS["separator"] = "/";
} else{
	$GLOBALS["separator"] = "\\";
}

// если в глобальной конфигурации не прописан конкретный путь к корню файлового менеджера - то будем считать им корень сайта
if(($mosConfig_joomlaxplorer_dir == '') OR ($mosConfig_joomlaxplorer_dir == '0')) $mosConfig_joomlaxplorer_dir = JPATH_BASE;
// the home directory for the filemanager: (use '/', not '\' or '\\', no trailing '/')

$GLOBALS["home_dir"] = $mosConfig_joomlaxplorer_dir;
$GLOBALS["home_url"] = JPATH_SITE;

// show hidden files in QuiXplorer: (hide files starting with '.', as in Linux/UNIX)
$GLOBALS["show_hidden"] = true;

// filenames not allowed to access: (uses PCRE regex syntax)
$GLOBALS["no_access"] = "^\.ht";

// user permissions bitfield: (1=modify, 2=password, 4=admin, add the numbers)
$GLOBALS["permissions"] = 7;