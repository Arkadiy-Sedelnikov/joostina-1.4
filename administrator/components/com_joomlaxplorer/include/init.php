<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
* @package joomlaXplorer
* @copyright soeren 2007
* @author The joomlaXplorer project (http://joomlacode.org/gf/project/joomlaxplorer/)
* @author The  The QuiX project (http://quixplorer.sourceforge.net)
**/
defined('_VALID_MOS') or die();
if(isset($_SERVER)) {
	$GLOBALS['__GET'] = &$_GET;
	$GLOBALS['__POST'] = &$_POST;
	$GLOBALS['__SERVER'] = &$_SERVER;
	$GLOBALS['__FILES'] = &$_FILES;
} elseif(isset($HTTP_SERVER_VARS)) {
	$GLOBALS['__GET'] = &$HTTP_GET_VARS;
	$GLOBALS['__POST'] = &$HTTP_POST_VARS;
	$GLOBALS['__SERVER'] = &$HTTP_SERVER_VARS;
	$GLOBALS['__FILES'] = &$HTTP_POST_FILES;
} else {
	die("<strong>ERROR: Your PHP version is too old</strong><br/>".
		"You need at least PHP 4.0.0 to run joomlaXplorer; preferably PHP 4.4.4 or higher.");
}
if(isset($_REQUEST["item"]))
	$GLOBALS["item"] = $item = stripslashes(urldecode($_REQUEST["item"]));
else
	$GLOBALS["item"] = $item = "";
if(!empty($GLOBALS['__POST']["selitems"])) {
	if(!is_array($GLOBALS['__POST']["selitems"]))
		$GLOBALS['__POST']["selitems"] = array($GLOBALS['__POST']["selitems"]);
	foreach($GLOBALS['__POST']["selitems"] as $i => $myItem) {
		$GLOBALS['__POST']["selitems"][$i] = urldecode($myItem);
	}
}
if(isset($GLOBALS['__GET']["order"]))
	$GLOBALS["order"] = stripslashes($GLOBALS['__GET']["order"]);
else
	$GLOBALS["order"] = "name";
if($GLOBALS["order"] == "")
	$GLOBALS["order"] == "name";
if(isset($GLOBALS['__GET']["srt"]))
	$GLOBALS["srt"] = stripslashes($GLOBALS['__GET']["srt"]);
else
	$GLOBALS["srt"] = "yes";
if($GLOBALS["srt"] == "")
	$GLOBALS["srt"] == "yes";
if(isset($GLOBALS['__GET']["lang"]))
	$GLOBALS["lang"] = $GLOBALS["language"] = $GLOBALS['__GET']["lang"];
elseif(isset($GLOBALS['__POST']["lang"]))
	$GLOBALS["lang"] = $GLOBALS["language"] = $GLOBALS['__POST']["lang"];
if(!isset($_REQUEST['file_mode']) && !empty($_SESSION['file_mode'])) {
	$GLOBALS['file_mode'] = mosGetParam($_SESSION, 'file_mode', 'file');
} else {
	if(@$_REQUEST['file_mode'] == 'ftp' && @$_SESSION['file_mode'] == 'file') {
		if(empty($_SESSION['ftp_login']) && empty($_SESSION['ftp_pass'])) {
			mosRedirect('index2.php?option=com_joomlaxplorer&action=ftp_authentication');
		} else {
			$GLOBALS['file_mode'] = $_SESSION['file_mode'] = mosGetParam($_REQUEST,
				'file_mode', 'file');
		}
	} elseif(isset($_REQUEST['file_mode'])) {
		$GLOBALS['file_mode'] = $_SESSION['file_mode'] = mosGetParam($_REQUEST,
			'file_mode', 'file');
	} else {
		$GLOBALS['file_mode'] = mosGetParam($_SESSION, 'file_mode', 'file');
	}
}
require _QUIXPLORER_PATH."/config/conf.php";
if(file_exists(_QUIXPLORER_PATH."/languages/".$GLOBALS["language"].".php")) {
	require _QUIXPLORER_PATH."/languages/".$GLOBALS["language"].".php";
} else {
	require _QUIXPLORER_PATH."/languages/russian.php";
}
if(file_exists(_QUIXPLORER_PATH."/languages/".$GLOBALS["language"]."_mimes.php")) {
	require _QUIXPLORER_PATH."/languages/".$GLOBALS["language"]."_mimes.php";
} else {
	require _QUIXPLORER_PATH."/languages/russian_mimes.php";
}
require _QUIXPLORER_PATH."/config/mimes.php";
require _QUIXPLORER_PATH."/libraries/File_Operations.php";
require _QUIXPLORER_PATH."/include/fun_extra.php";
require _QUIXPLORER_PATH."/include/header.php";
require _QUIXPLORER_PATH."/include/footer.php";
require _QUIXPLORER_PATH."/include/error.php";
jx_RaiseMemoryLimit('8M');
$GLOBALS['jx_File'] = new jx_File();
if(jx_isFTPMode()) {
	$ftp_host = mosGetParam($_POST, 'ftp_host', 'localhost:21');
	$url = @parse_url('ftp://'.$ftp_host);
	$port = empty($url['port'])?21:$url['port'];
	$ftp = new Net_FTP($url['host'], $port, 20);
	$GLOBALS['FTPCONNECTION'] = new Net_FTP($url['host'], $port, 20);
	$res = $GLOBALS['FTPCONNECTION']->connect();
	if(PEAR::isError($res)) {
		echo $res->getMessage();
		$GLOBALS['file_mode'] = $_SESSION['file_mode'] = 'file';
	} else {
		if(empty($_SESSION['ftp_login']) && empty($_SESSION['ftp_pass'])) {
			mosRedirect('index2.php?option=com_joomlaxplorer&action=ftp_authentication&file_mode=file');
		}
		$login_res = $GLOBALS['FTPCONNECTION']->login($_SESSION['ftp_login'], $_SESSION['ftp_pass']);
		if(PEAR::isError($res)) {
			echo $login_res->getMessage();
			$GLOBALS['file_mode'] = $_SESSION['file_mode'] = 'file';
		}
	}
}
if($GLOBALS["require_login"]) {
	require _QUIXPLORER_PATH."/include/login.php";
	if($GLOBALS["action"] == "logout") {
		logout();
	} else {
		login();
	}
}
if(!isset($_REQUEST['dir'])) {
	$GLOBALS["dir"] = $dir = mosGetParam($_SESSION, 'jx_'.$GLOBALS['file_mode'].'dir', '');
	if(!empty($dir)) {
		$dir = @$dir[0] == '/'?substr($dir, 1):$dir;
	}
	$try_this = jx_isFTPMode()?'/'.$dir:$GLOBALS['home_dir'].'/'.$dir;
	if(!empty($dir) && !$GLOBALS['jx_File']->file_exists($try_this)) {
		$dir = '';
	}
} else {
	$GLOBALS["dir"] = $dir = urldecode(stripslashes(mosGetParam($_REQUEST, "dir")));
}
if($dir == 'jx_root') {
	$GLOBALS["dir"] = $dir = '';
}
if(jx_isFTPMode() && $dir != '') {
	$GLOBALS['FTPCONNECTION']->cd($dir);
}
$abs_dir = get_abs_dir($GLOBALS["dir"]);
if(!file_exists($GLOBALS["home_dir"])) {
	if(!file_exists($GLOBALS["home_dir"].$GLOBALS["separator"])) {
		if($GLOBALS["require_login"]) {
			$extra = "<a href=\"".make_link("logout", null, null)."\">".$GLOBALS["messages"]["btnlogout"].
				"</A>";
		} else
			$extra = null;
		show_error($GLOBALS["error_msg"]["home"]." (".$GLOBALS["home_dir"].")", $extra);
	}
}
if(!down_home($abs_dir))
	show_error($GLOBALS["dir"]." : ".$GLOBALS["error_msg"]["abovehome"]);
if(!get_is_dir($abs_dir))
	if(!get_is_dir($abs_dir.$GLOBALS["separator"]))
		show_error($abs_dir." : ".$GLOBALS["error_msg"]["direxist"]);
$_SESSION['jx_'.$GLOBALS['file_mode'].'dir'] = $dir;





?>
