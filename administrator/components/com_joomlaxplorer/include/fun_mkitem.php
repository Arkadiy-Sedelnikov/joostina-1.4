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
function make_item($dir) {
	if(($GLOBALS["permissions"] & 01) != 01)
		show_error($GLOBALS["error_msg"]["accessfunc"]);
	$mkname = $GLOBALS['__POST']["mkname"];
	$mktype = $GLOBALS['__POST']["mktype"];
	$symlink_target = $GLOBALS['__POST']['symlink_target'];
	$mkname = basename(stripslashes($mkname));
	if($mkname == "")
		show_error($GLOBALS["error_msg"]["miscnoname"]);
	$new = get_abs_item($dir, $mkname);
	if(@$GLOBALS['jx_File']->file_exists($new))
		show_error($mkname.": ".$GLOBALS["error_msg"]["itemdoesexist"]);
	if($mktype == "dir") {
		$ok = @$GLOBALS['jx_File']->mkdir($new, 0777);
		$err = $GLOBALS["error_msg"]["createdir"];
	} elseif($mktype == 'file') {
		$ok = @$GLOBALS['jx_File']->mkfile($new);
		$err = $GLOBALS["error_msg"]["createfile"];
	} elseif($mktype == 'symlink') {
		if(empty($symlink_target)) {
			show_error('Please provide a valid <strong>target</strong> for the symbolic link.');
		}
		if(!file_exists($symlink_target) || !is_readable($symlink_target)) {
			show_error('The file you wanted to make a symbolic link to does not exist or is not accessible by PHP.');
		}
		$ok = symlink($symlink_target, $new);
		$err = 'The symbolic link could not be created.';
	}
	if($ok === false || PEAR::isError($ok)) {
		if(PEAR::isError($ok))
			$err .= $ok->getMessage();
		show_error($err);
	}
	header("Location: ".make_link("list", $dir, null));
}





?>
