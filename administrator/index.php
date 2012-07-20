<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// Установка флага родительского файла
define('_JLINDEX', 1);

// корень файлов
define('_JLPATH_ROOT',dirname(dirname(__FILE__)));

// подключение основных глобальных переменных
require_once _JLPATH_ROOT . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'defines.php';

// корень файлов
define('JPATH_BASE', dirname(dirname(__FILE__)));

if(!defined('IS_ADMIN')) define('IS_ADMIN', 1);

if(!file_exists(JPATH_BASE . DS . 'configuration.php')){
	header('Location: ../installation/index.php');
	exit();
}

(ini_get('register_globals') == 1) ? require_once (JPATH_BASE . DS . 'includes' . DS . 'globals.php') : null;
require_once (JPATH_BASE . DS . 'configuration.php');

// для совместимости
$mosConfig_absolute_path = JPATH_BASE;

// Проверка SSL - $http_host возвращает <url_сайта>:<номер_порта, если он 443>
$http_host = explode(':', $_SERVER['HTTP_HOST']);
if((!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' || isset($http_host[1]) && $http_host[1] == 443) && substr($mosConfig_live_site, 0, 8) != 'https://'){
	$mosConfig_live_site = 'https://' . substr($mosConfig_live_site, 7);
}
unset($http_host);

// live_site
define('JPATH_SITE', $mosConfig_live_site);

require_once (JPATH_BASE . DS . 'includes/joostina.php');


$mainframe = mosMainFrame::getInstance(true);
$database = database::getInstance();
$config = &$mainframe->config;

// получение шаблона страницы
$cur_template = $mainframe->getTemplate();
define('JTEMPLATE', $cur_template);


// загрузка файла русского языка по умолчанию
if($config->config_lang == ''){
	$config->config_lang = 'russian';
	$mosConfig_lang = 'russian';
}

$mainframe->set('lang', $mosConfig_lang);
include_once($mainframe->getLangFile());

//Installation sub folder check, removed for work with SVN
if(file_exists('../installation/index.php') && joomlaVersion::get('SVN') == 0){
	define('_INSTALL_CHECK', 1);
	include (JPATH_BASE . DS . 'templates' . DS . 'system' . DS . 'offline.php');
	exit();
}

$option = strtolower(strval(mosGetParam($_REQUEST, 'option', null)));

session_name(md5($mosConfig_live_site));
session_start();

header('Content-type: text/html; charset=UTF-8');

$bad_auth_count = intval(mosGetParam($_SESSION, 'bad_auth', 0));

if(isset($_POST['submit'])){
	$usrname = stripslashes(mosGetParam($_POST, 'usrname', null));
	$pass = stripslashes(mosGetParam($_POST, 'pass', null));

	if($pass == null){
		mosRedirect(JPATH_SITE . '/' . JADMIN_BASE . '/', _PLEASE_ENTER_PASSWORDWORD);
		exit();
	}

	if($config->config_captcha OR ((int)$config->config_admin_bad_auth >= 0 && $config->config_admin_bad_auth <= $bad_auth_count)){
		$captcha = mosGetParam($_POST, 'captcha', '');
		$captcha_keystring = mosGetParam($_SESSION, 'captcha_keystring', '');
		if($captcha_keystring != $captcha){
			mosRedirect(JPATH_SITE . '/' . JADMIN_BASE . '/?' . $config->config_admin_secure_code, _BAD_CAPTCHA_STRING);
			unset($_SESSION['captcha_keystring']);
			exit;
		}
	}
	/*
	if((int) $config->config_admin_bad_auth >= 0 && $config->config_admin_bad_auth <= $bad_auth_count) {
		mosRedirect($config->config_live_site.'/'.JADMIN_BASE.'/',_USER_BLOKED);
		unset($_SESSION['captcha_keystring']);
		exit;
	}
	*/
	$my = null;
	$query = 'SELECT * FROM #__users WHERE username =' . $database->Quote($usrname) . ' AND block = 0';
	$database->setQuery($query);
	$database->loadObject($my);

	/** find the user group (or groups in the future)*/
	if(isset($my->id)){
		$acl = &gacl::getInstance();

		$grp = $acl->getAroGroup($my->id);
		$my->gid = $grp->group_id;
		$my->usertype = $grp->name;

		// Conversion to new type
		if((strpos($my->password, ':') === false) && $my->password == md5($pass)){
			// Old password hash storage but authentic ... lets convert it
			$salt = mosMakePassword(16);
			$crypt = md5($pass . $salt);
			$my->password = $crypt . ':' . $salt;

			// Now lets store it in the database
			$query = 'UPDATE #__users SET password = ' . $database->Quote($my->password) . 'WHERE id = ' . (int)$my->id;
			$database->setQuery($query);
			$database->query();
		}

		list($hash, $salt) = explode(':', $my->password);
		$cryptpass = md5($pass . $salt);

		if(strcmp($hash, $cryptpass) || !$acl->acl_check('administration', 'login', 'users', $my->usertype)){
			// ошибка авторизации
			$query = 'UPDATE #__users SET bad_auth_count = bad_auth_count + 1 WHERE id = ' . (int)$my->id;
			$database->setQuery($query);
			$database->query();
			$_SESSION['bad_auth'] = $bad_auth_count + 1;

			if($_SESSION['bad_auth'] >= $config->config_count_for_user_block){
				$query = 'UPDATE #__users SET block = 1 WHERE id = ' . (int)$my->id;
				$database->setQuery($query);
				$database->query();
			}

			mosRedirect(JPATH_SITE . '/' . JADMIN_BASE . '/index.php?' . $config->config_admin_secure_code, _BAD_USERNAME_OR_PASSWORDWORD);
			exit();
		}

		session_destroy();
		session_unset();
		session_write_close();

		// construct Session ID
		$logintime = time();
		$session_id = md5($my->id . $my->username . $my->usertype . $logintime);

		session_name(md5(JPATH_SITE));
		session_id($session_id);
		session_start();

		// add Session ID entry to DB
		$query = "INSERT INTO #__session SET time = " . $database->Quote($logintime) . ", session_id = " . $database->Quote($session_id) . ", userid = " . (int)$my->id . ", usertype = " . $database->Quote($my->usertype) . ", username = " . $database->Quote($my->username);
		$database->setQuery($query);
		if(!$database->query()){
			echo $database->stderr();
		}

		// check if site designated as a production site
		// for a demo site allow multiple logins with same user account
		if(joomlaVersion::get('SITE') == 1){
			// delete other open admin sessions for same account
			$query = "DELETE FROM #__session WHERE userid = " . (int)$my->id . " AND username = " . $database->Quote($my->username) . "\n AND usertype = " . $database->Quote($my->usertype) . "\n AND session_id != " . $database->Quote($session_id) . "\n AND guest = 1" . "\n AND gid = 0";
			$database->setQuery($query);
			if(!$database->query()){
				echo $database->stderr();
			}
		}

		$_SESSION['session_id'] = $session_id;
		$_SESSION['session_user_id'] = $my->id;
		$_SESSION['session_USER'] = $my->username;
		$_SESSION['session_usertype'] = $my->usertype;
		$_SESSION['session_gid'] = $my->gid;
		$_SESSION['session_logintime'] = $logintime;
		$_SESSION['session_user_params'] = $my->params;
		$_SESSION['session_bad_auth_count'] = $my->bad_auth_count;
		$_SESSION['session_userstate'] = array();

		session_write_close();

		$expired = 'index2.php';

		// check if site designated as a production site
		// for a demo site disallow expired page functionality
		if(joomlaVersion::get('SITE') == 1 && $mosConfig_admin_expired === '1'){
			$file = $mainframe->getPath('com_xml', 'com_users');
			$params = new mosParameters($my->params, $file, 'component');

			$now = time();

			// expired page functionality handling
			$expired = $params->def('expired', '');
			$expired_time = $params->def('expired_time', '');

			// if now expired link set or expired time is more than half the admin session life set, simply load normal admin homepage
			$checktime = ($mosConfig_session_life_admin ? $mosConfig_session_life_admin : 1800) / 2;
			if(!$expired || (($now - $expired_time) > $checktime)){
				$expired = 'index2.php';
			}
			// link must also be a Joomla link to stop malicious redirection
			if(strpos($expired, 'index2.php?option=com_') !== 0){
				$expired = 'index2.php';
			}

			// clear any existing expired page data
			$params->set('expired', '');
			$params->set('expired_time', '');

			// param handling
			if(is_array($params->toArray())){
				$txt = array();
				foreach($params->toArray() as $k => $v){
					$txt[] = "$k=$v";
				}
				$saveparams = implode("\n", $txt);
			}

			// save cleared expired page info to user data
			$query = "UPDATE #__users SET params = " . $database->Quote($saveparams) . " WHERE id = " . (int)$my->id . " AND username = " . $database->Quote($my->username) . " AND usertype = " . $database->Quote($my->usertype);
			$database->setQuery($query);
			$database->query();

			// скидываем счетчик неудачных авторзаций в админке
			$query = 'UPDATE #__users SET bad_auth_count = 0 WHERE id = ' . $my->id;
			$database->setQuery($query);
			$database->query();

		}

		/** cannot using mosredirect as this stuffs up the cookie in IIS*/
		// redirects page to admin homepage by default or expired page
		echo "<script>document.location.href='$expired';</script>\n";
		exit();
	} else{
		mosRedirect(JPATH_SITE . '/' . JADMIN_BASE . '/index.php?' . $config->config_admin_secure_code, _BAD_USERNAME_OR_PASSWORDWORD);
		exit();
	}
} else{
	initGzip();
	header('Content-type: text/html; charset=UTF-8');
	if($config->config_admin_bad_auth <= $bad_auth_count && (int)$config->config_admin_bad_auth >= 0){
		$config->config_captcha = 1;
	}
	$path = JPATH_BASE . DS . JADMIN_BASE . DS . 'templates' . DS . JTEMPLATE . DS . 'login.php';
	require_once ($path);
	doGzip();
}