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

$acl = &gacl::getInstance();

if(!$acl->acl_check('administration', 'config', 'users', $my->usertype)){
	die('error-acl');
}

$task = mosGetParam($_GET, 'task', 'publish');

switch($task){
	case 'apply':
		js_menu_cache_clear(false);
		x_saveconfig($task);
		break;
	default:
		echo 'error-task';
		break;
}

/**
 * Сохранение конфигурации
 */
function x_saveconfig($task){
	global $database, $mosConfig_password, $mosConfig_session_type;
	josSpoofCheck();

	$row = new JConfig();
	if(!$row->bind($_POST, 'config_tseparator')){
		echo 'error-bind';
	}

	// if Session Authentication Type changed, delete all old Frontend sessions only - which used old Authentication Type
	if($mosConfig_session_type != $row->config_session_type){
		$past = time();
		$query = "DELETE FROM #__session"
			. "\n WHERE time < " . $database->Quote($past)
			. "\n AND ("
			. "\n ( guest = 1 AND userid = 0 ) OR ( guest = 0 AND gid > 0 )"
			. "\n )";
		$database->setQuery($query);
		$database->query();
	}

	$server_time = date('O') / 100;
	$offset = $_POST['config_offset_user'] - $server_time;
	$row->config_offset = $offset;
	$row->config_password = $mosConfig_password;
	$row->config_sitename = htmlspecialchars($row->config_sitename, ENT_QUOTES);

	$row->config_offline_message = ampReplace($row->config_offline_message);
	$row->config_offline_message = str_replace('"', '&quot;', $row->config_offline_message);
	$row->config_offline_message = str_replace("'", '&#039;', $row->config_offline_message);

	$row->config_error_message = ampReplace($row->config_error_message);
	$row->config_error_message = str_replace('"', '&quot;', $row->config_error_message);
	$row->config_error_message = str_replace("'", '&#039;', $row->config_error_message);

	if($row->config_joomlaxplorer_dir == $row->config_absolute_path) $row->config_joomlaxplorer_dir = 0;

	// ключ кэша
	$row->config_cache_key = time();

	$config = "<?php \n";
	$config .= $row->getVarText();
	$config .= "setlocale (LC_TIME, \$mosConfig_locale);\n";
	$config .= '?>';
	$fname = JPATH_BASE . '/configuration.php';

	$enable_write = intval(mosGetParam($_POST, 'enable_write', 0));
	$oldperms = fileperms($fname);
	if($enable_write){
		@chmod($fname, $oldperms | 0222);
	}

	if($fp = fopen($fname, 'w')){
		fputs($fp, $config, strlen($config));
		fclose($fp);
		if($enable_write){
			@chmod($fname, $oldperms);
		} else{
			if(mosGetParam($_POST, 'disable_write', 0)) @chmod($fname, $oldperms & 0777555);
		} // if

		$msg = _CONFIGURATION_IS_UPDATED;

		// apply file and directory permissions if requested by user
		$applyFilePerms = mosGetParam($_POST, 'applyFilePerms', 0) && $row->config_fileperms != '';
		$applyDirPerms = mosGetParam($_POST, 'applyDirPerms', 0) && $row->config_dirperms != '';
		if($applyFilePerms || $applyDirPerms){
			$mosrootfiles = array(JADMIN_BASE, 'cache', 'components', 'images', 'language', 'mambots', 'media', 'modules', 'templates', 'configuration.php');
			$filemode = null;
			if($applyFilePerms){
				$filemode = octdec($row->config_fileperms);
			}
			$dirmode = null;
			if($applyDirPerms){
				$dirmode = octdec($row->config_dirperms);
			}
			foreach($mosrootfiles as $file){
				mosChmodRecursive(JPATH_BASE . DS . $file, $filemode, $dirmode);
			}
		} // if
		mosCache::cleanCache('com_boss');
		echo $msg;
	} else{
		if($enable_write){
			@chmod($fname, $oldperms);
		}
		echo _CANNOT_OPEN_CONF_FILE;
	}
}