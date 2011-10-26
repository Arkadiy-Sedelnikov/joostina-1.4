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
function load_users() {
	require _QUIXPLORER_PATH."/config/.htusers.php";
}
function save_users() {
	$cnt = count($GLOBALS["users"]);
	if($cnt > 0)
		sort($GLOBALS["users"]);
	$content = '<?php 
	/** ensure this file is being included by a parent file*/
	defined( "_VALID_MOS" ) or die( "Direct Access to this location is not allowed." );
	$GLOBALS["users"]=array(';
	for($i = 0; $i < $cnt; ++$i) {
		$content .= "\r\n\tarray(\"".$GLOBALS["users"][$i][0].'","'.$GLOBALS["users"][$i][1].
			'","'.$GLOBALS["users"][$i][2].'","'.$GLOBALS["users"][$i][3].'",'.$GLOBALS["users"][$i][4].
			',"'.$GLOBALS["users"][$i][5].'",'.$GLOBALS["users"][$i][6].','.$GLOBALS["users"][$i][7].
			'),';
	}
	$content .= "\r\n); ?>";
	$fp = @fopen(_QUIXPLORER_PATH."/config/.htusers.php", "w");
	if($fp === false)
		return false;
	fputs($fp, $content);
	fclose($fp);
	return true;
}
function &find_user($user, $pass) {
	$cnt = count($GLOBALS["users"]);
	for($i = 0; $i < $cnt; ++$i) {
		if($user == $GLOBALS["users"][$i][0]) {
			if($pass == null || ($pass == $GLOBALS["users"][$i][1] && $GLOBALS["users"][$i][7])) {
				return $GLOBALS["users"][$i];
			}
		}
	}
	return null;
}
function activate_user($user, $pass) {
	$data = find_user($user, $pass);
	if($data == null)
		return false;
	$GLOBALS['__SESSION']["s_user"] = $data[0];
	$GLOBALS['__SESSION']["s_pass"] = $data[1];
	$GLOBALS["home_dir"] = $data[2];
	$GLOBALS["home_url"] = $data[3];
	$GLOBALS["show_hidden"] = $data[4];
	$GLOBALS["no_access"] = $data[5];
	$GLOBALS["permissions"] = $data[6];
	return true;
}
function update_user($user, $new_data) {
	$data = &find_user($user, null);
	if($data == null)
		return false;
	$data = $new_data;
	return save_users();
}
function add_user($data) {
	if(find_user($data[0], null))
		return false;
	$GLOBALS["users"][] = $data;
	return save_users();
}
function remove_user($user) {
	$data = &find_user($user, null);
	if($data == null)
		return false;
	$data = null;
	$cnt = count($GLOBALS["users"]);
	for($i = 0; $i < $cnt; ++$i) {
		if($GLOBALS["users"][$i] != null)
			$save_users[] = $GLOBALS["users"][$i];
	}
	$GLOBALS["users"] = $save_users;
	return save_users();
}






?>
