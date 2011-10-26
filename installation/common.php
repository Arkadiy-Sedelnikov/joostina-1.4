<?php
/**
* @version $Id: common.php 4675 2006-08-23 16:55:24Z stingrey $
* @package Joostina
* @copyright Авторские права (C) 2005 Open Source Matters. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, смотрите LICENSE.php
* Joomla! - свободное программное обеспечение. Эта версия может быть изменена
* в соответствии с Генеральной Общественной Лицензией GNU, поэтому возможно
* её дальнейшее распространение в составе результата работы, лицензированного
* согласно Генеральной Общественной Лицензией GNU или других лицензий свободных 
* программ или программ с открытым исходным кодом.
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/
defined('_VALID_MOS') or die();

error_reporting(E_ALL);

header('Content-type: text/html; charset=UTF-8');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

/**
* Сервисные функции для установок значений по умолчанию
*/
define("_MOS_NOTRIM",0x0001);
define("_MOS_ALLOWHTML",0x0002);
function mosGetParam(&$arr,$name,$def = null,$mask = 0) {
	if(isset($arr[$name])) {
		if(is_string($arr[$name])) {
			if(!($mask & _MOS_NOTRIM)) {
				$arr[$name] = trim($arr[$name]);
			}
			if(!($mask & _MOS_ALLOWHTML)) {
				$arr[$name] = strip_tags($arr[$name]);
			}
			if(!get_magic_quotes_gpc()) {
				$arr[$name] = addslashes($arr[$name]);
			}
		}
		return $arr[$name];
	} else {
		return $def;
	}
}

function mosMakePassword($length) {
	$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$len = strlen($salt);
	$makepass = "";
	mt_srand(10000000* (double)microtime());
	for($i = 0; $i < $length; $i++) $makepass .= $salt[mt_rand(0,$len - 1)];
	return $makepass;
}

/**
* Права доступа к файлам и директориям
* @param path начальный файл или директория (без слеша в конце)
* @param filemode Значение переменной для установки прав доступа к файлам. NULL = без установки прав доступа.
* @param dirmode Значение переменной для установки прав доступа к директориям. NULL = без установки прав доступа..
* @return TRUE=all следовательно FALSE=one для прав доступа ко всем файлам
*/
function mosChmodRecursive($path,$filemode = null,$dirmode = null) {
	$ret = true;
	if(is_dir($path)) {
		$dh = opendir($path);
		while($file = readdir($dh)) {
			if($file != '.' && $file != '..') {
				$fullpath = $path.'/'.$file;
				if(is_dir($fullpath)) {
					if(!mosChmodRecursive($fullpath,$filemode,$dirmode)) $ret = false;
				} else {
					if(isset($filemode))
						if(!@chmod($fullpath,$filemode)) $ret = false;
				} // если
			} // если
		} // в то время как
		closedir($dh);
		if(isset($dirmode))
			if(!@chmod($path,$dirmode)) $ret = false;
	} else {
		if(isset($filemode)) $ret = @chmod($path,$filemode);
	} // if
	return $ret;
} // mosChmodRecursive

require_once ('../includes/version.php');
$_VERSION = new joomlaVersion();
$versioninfo = $_VERSION->RELEASE.'.'.$_VERSION->CMS_ver;
$version = $_VERSION->CMS.' '.$_VERSION->CMS_ver.' '.$_VERSION->DEV_STATUS.' [ '.$_VERSION->CODENAME.' ] '.$_VERSION->RELDATE.' '.$_VERSION->RELTIME.' '.$_VERSION->RELTZ;