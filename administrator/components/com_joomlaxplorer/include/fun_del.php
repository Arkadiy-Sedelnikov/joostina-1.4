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
defined('_JLINDEX') or die();
function del_items($dir){
	if(($GLOBALS["permissions"] & 01) != 01)
		show_error($GLOBALS["error_msg"]["accessfunc"]);
	$cnt = count($GLOBALS['__POST']["selitems"]);
	$err = false;
	for($i = 0; $i < $cnt; ++$i){
		$items[$i] = stripslashes($GLOBALS['__POST']["selitems"][$i]);
		if(jx_isFTPMode()){
			$abs = get_item_info($dir, $items[$i]);
		} else{
			$abs = get_abs_item($dir, $items[$i]);
		}
		if(!@$GLOBALS['jx_File']->file_exists($abs)){
			$error[$i] = $GLOBALS["error_msg"]["itemexist"];
			$err = true;
			continue;
		}
		if(!get_show_item($dir, $items[$i])){
			$error[$i] = $GLOBALS["error_msg"]["accessitem"];
			$err = true;
			continue;
		}
		if(jx_isFTPMode())
			$abs = get_abs_item($dir, $abs);
		$ok = $GLOBALS['jx_File']->remove($abs);
		if($ok === false || PEAR::isError($ok)){
			$error[$i] = $GLOBALS["error_msg"]["delitem"];
			if(PEAR::isError($ok)){
				$error[$i] .= ' [' . $ok->getMessage() . ']';
			}
			$err = true;
			continue;
		}
		$error[$i] = null;
	}
	if($err){
		$err_msg = "";
		for($i = 0; $i < $cnt; ++$i){
			if($error[$i] == null)
				continue;
			$err_msg .= $items[$i] . " : " . $error[$i] . "<br/>\n";
		}
		show_error($err_msg);
	}
	mosRedirect(make_link("list", $dir, null), $GLOBALS['messages']['success_delete_file']);
}


?>
