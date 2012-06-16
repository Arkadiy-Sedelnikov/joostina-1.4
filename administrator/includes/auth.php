<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

// разделитель каталогов
define('DS', DIRECTORY_SEPARATOR);
// корень файлов
define('JPATH_BASE', dirname(dirname(dirname(__FILE__))));
// корень файлов админкиы
define('JPATH_BASE_ADMIN', dirname(dirname(__FILE__)));

(ini_get('register_globals') == 1) ? require_once (JPATH_BASE . DS . 'includes' . DS . 'globals.php') : null;

require_once (JPATH_BASE . DS . 'configuration.php');

$basePath = dirname(__file__);

// SSL check - $http_host returns <live site url>:<port number if it is 443>
$http_host = explode(':', $_SERVER['HTTP_HOST']);
if((!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' || isset($http_host[1]) && $http_host[1] == 443) && substr($mosConfig_live_site, 0, 8) != 'https://'){
	$mosConfig_live_site = 'https://' . substr($mosConfig_live_site, 7);
}

require(JPATH_BASE . DS . 'includes/joostina.php');

$mainframe = mosMainFrame::getInstance();
$my = $mainframe->getUser();

session_name(md5($mosConfig_live_site));
session_start();

header('Content-type: text/html; charset=UTF-8');

$database = database::getInstance();

// restore some session variables
if(!isset($my)){
	$my = new mosUser($database);
}

$my->id = intval(mosGetParam($_SESSION, 'session_user_id', ''));
$my->username = strval(mosGetParam($_SESSION, 'session_USER', ''));
$my->usertype = strval(mosGetParam($_SESSION, 'session_usertype', ''));
$my->gid = intval(mosGetParam($_SESSION, 'session_gid', ''));
$session_id = strval(mosGetParam($_SESSION, 'session_id', ''));
$logintime = strval(mosGetParam($_SESSION, 'session_logintime', ''));

if($session_id != md5($my->id . $my->username . $my->usertype . $logintime)){
	mosRedirect('index.php');
	die;
}