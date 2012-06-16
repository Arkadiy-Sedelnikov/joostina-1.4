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
function rename_item($dir, $item){
	if(($GLOBALS["permissions"] & 01) != 01){
		show_error($GLOBALS["error_msg"]["accessfunc"]);
	}
	if(isset($GLOBALS['__POST']["confirm"]) && $GLOBALS['__POST']["confirm"] ==
		"true"
	){
		$newitemname = $GLOBALS['__POST']["newitemname"];
		$newitemname = trim(basename(stripslashes($newitemname)));
		if($newitemname == ''){
			show_error($GLOBALS["error_msg"]["miscnoname"]);
		}
		if(!jx_isFTPMode()){
			$abs_old = get_abs_item($dir, $item);
			$abs_new = get_abs_item($dir, $newitemname);
		} else{
			$abs_old = get_item_info($dir, $item);
			$abs_new = get_item_info($dir, $newitemname);
		}
		if(@$GLOBALS['jx_File']->file_exists($abs_new)){
			show_error($newitemname . ": " . $GLOBALS["error_msg"]["itemdoesexist"]);
		}
		$perms_old = $GLOBALS['jx_File']->fileperms($abs_old);
		$ok = $GLOBALS['jx_File']->rename(get_abs_item($dir, $item), get_abs_item($dir,
			$newitemname));
		if(jx_isFTPMode()){
			$abs_new = get_item_info($dir, $newitemname);
		}
		$GLOBALS['jx_File']->chmod($abs_new, $perms_old);
		if($ok === false || PEAR::isError($ok)){
			show_error('Could not rename ' . $item . ' to ' . $newitemname);
		}
		$msg = sprintf($GLOBALS['messages']['success_rename_file'], $item, $newitemname);
		mosRedirect(make_link("list", $dir, null), $msg);
	}
	show_header($GLOBALS['messages']['rename_file']);
	echo '<br /><form method="post" action="';
	echo make_link("rename", $dir, $item) . "\">\n";
	echo "<input type=\"hidden\" name=\"confirm\" value=\"true\" />\n";
	echo "<input type=\"hidden\" name=\"item\" value=\"" . stripslashes($GLOBALS['__GET']["item"]) .
		"\" />\n";
	echo "<table>\n<tr><tr><td colspan=\"2\">\n";
	echo "<label for=\"newitemname\">" . $GLOBALS["messages"]["newname"] .
		":</label>&nbsp;&nbsp;&nbsp;<input name=\"newitemname\" id=\"newitemname\" type=\"text\" size=\"60\" value=\"" .
		stripslashes($_GET['item']) . "\" /><br /><br /><br /></td></tr>\n";
	echo "<tr><tr><td>\n<input type=\"submit\" value=\"" . $GLOBALS["messages"]["btnchange"];
	echo "\"></td>\n<td><input type=\"button\" value=\"" . $GLOBALS["messages"]["btncancel"];
	echo "\" onclick=\"javascript:location='" . make_link("list", $dir, null) . "';\">\n</td></tr></form></table><br />\n";
}


?>
