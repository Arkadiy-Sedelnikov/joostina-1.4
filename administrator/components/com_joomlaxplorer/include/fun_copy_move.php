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
function dir_list($dir){
	$dir_list = array();
	$handle = @$GLOBALS['jx_File']->opendir(get_abs_dir($dir));
	if($handle === false)
		return;
	while(($new_item = $GLOBALS['jx_File']->readdir($handle)) !== false){
		if(!get_show_item($dir, $new_item))
			continue;
		if(jx_isFTPMode()){
			$new_item_name = $new_item['name'];
		} else{
			$new_item_name = $new_item;
			$new_item = get_abs_item($dir, $new_item);
		}
		if(!get_is_dir($new_item))
			continue;
		$dir_list[$new_item_name] = $new_item;
	}
	if(is_array($dir_list))
		ksort($dir_list);
	return $dir_list;
}

function dir_print($dir_list, $new_dir){
	$mainframe = mosMainFrame::getInstance();
	$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';

	$dir_up = dirname($new_dir);
	if($dir_up == "."){
		$dir_up = "";
	}
	echo "<tr><td><a href=\"javascript:NewDir('" . $dir_up . "');\"><img border=\"0\" align=\"absmiddle\" src=\"" . $cur_file_icons_path . "/uparrow.png\" alt=\"\">&nbsp;..</a></td></tr>\n";
	if(!is_array($dir_list))
		return;
	while(list($new_item, $info) = each($dir_list)){
		if(is_array($info)){
			$new_item = $info['name'];
		}
		$s_item = $new_item;
		if(strlen($s_item) > 40)
			$s_item = substr($s_item, 0, 37) . "...";
		echo "<tr><td><a href=\"javascript:NewDir('" . get_rel_item($new_dir, $new_item) . "');\"><img border=\"0\"  align=\"absmiddle\" " . "src=\"" . $cur_file_icons_path . "/folder.png\" alt=\"\">&nbsp;" . $s_item . "</a></td></tr>\n";
	}
}

function copy_move_items($dir){
	$mainframe = mosMainFrame::getInstance();
	$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
	if(($GLOBALS["permissions"] & 01) != 01)
		show_error($GLOBALS["error_msg"]["accessfunc"]);
	$first = $GLOBALS['__POST']["first"];
	if($first == "y")
		$new_dir = $dir;
	else
		$new_dir = stripslashes($GLOBALS['__POST']["new_dir"]);
	if($new_dir == ".")
		$new_dir = "";
	$cnt = count($GLOBALS['__POST']["selitems"]);
	if($GLOBALS["action"] != "move"){
		$images = "" . $cur_file_icons_path . "/copy.png";
	} else{
		$images = "" . $cur_file_icons_path . "/copy.png";
	}
	if(!isset($GLOBALS['__POST']["confirm"]) || $GLOBALS['__POST']["confirm"] != "true"){
		show_header(($GLOBALS["action"] != "move" ? $GLOBALS["messages"]["actcopyitems"] : $GLOBALS["messages"]["actmoveitems"]));

		?>
	<script language="JavaScript1.2" type="text/javascript">
		<!--
		function NewDir(newdir) {
			document.selform.new_dir.value = newdir;
			document.selform.submit();
		}

		function Execute() {
			document.selform.confirm.value = "true";
		}
		//-->
	</script><?php


		$s_dir = $dir;
		if(strlen($s_dir) > 40)
			$s_dir = "..." . substr($s_dir, -37);
		$s_ndir = $new_dir;
		if(strlen($s_ndir) > 40)
			$s_ndir = "..." . substr($s_ndir, -37);
		echo "<br /><img src=\"" . $images . "\" align=\"absmiddle\" alt=\"\" />&nbsp;<strong>";
		echo sprintf(($GLOBALS["action"] != "move" ? $GLOBALS["messages"]["actcopyfrom"] :
			$GLOBALS["messages"]["actmovefrom"]), $s_dir, $s_ndir);
		echo "<br /><form name=\"selform\" method=\"post\" action=\"";
		echo make_link("post", $dir, null) . "\"><table style=\"width:500px;\" class=\"adminform\">\n";
		echo "<input type=\"hidden\" name=\"do_action\" value=\"" . $GLOBALS["action"] . "\">\n";
		echo "<input type=\"hidden\" name=\"confirm\" value=\"false\">\n";
		echo "<input type=\"hidden\" name=\"first\" value=\"n\">\n";
		echo "<input type=\"hidden\" name=\"new_dir\" value=\"" . $new_dir . "\">\n";
		dir_print(dir_list($new_dir), $new_dir);
		echo "</table><br />
		<table style=\"width:500px;\" class=\"adminform\">\n";
		for($i = 0; $i < $cnt; ++$i){
			$selitem = stripslashes($GLOBALS['__POST']["selitems"][$i]);
			if(isset($GLOBALS['__POST']["newitems"][$i])){
				$newitem = stripslashes($GLOBALS['__POST']["newitems"][$i]);
				if($first == "y")
					$newitem = $selitem;
			} else
				$newitem = $selitem;
			$s_item = $selitem;
			if(strlen($s_item) > 50)
				$s_item = substr($s_item, 0, 47) . "...";
			echo "<tr><td><img src=\"images/info.png\" align=\"absmiddle\" alt=\"\">";
			echo "<input type=\"hidden\" name=\"selitems[]\" value=\"";
			echo $selitem . "\">&nbsp;" . $s_item . "&nbsp;";
			echo "</td><td><input type=\"text\" size=\"25\" name=\"newitems[]\" value=\"";
			echo $newitem . "\"></td></tr>\n";
		}
		echo "</table><br /><table><tr>\n<td>";
		echo "<input type=\"submit\" value=\"";
		echo ($GLOBALS["action"] != "move" ? $GLOBALS["messages"]["btncopy"] : $GLOBALS["messages"]["btnmove"]);
		echo "\" onclick=\"javascript:Execute();\"></td>\n<td>";
		echo "<input type=\"button\" value=\"" . $GLOBALS["messages"]["btncancel"];
		echo "\" onclick=\"javascript:location='" . make_link("list", $dir, null);
		echo "';\"></td>\n</tr></table><br /></form>\n";
		return;
	}
	if(!@$GLOBALS['jx_File']->file_exists(get_abs_dir($new_dir)))
		show_error(get_abs_dir($new_dir) . ": " . $GLOBALS["error_msg"]["targetexist"]);
	if(!get_show_item($new_dir, ""))
		show_error($new_dir . ": " . $GLOBALS["error_msg"]["accesstarget"]);
	if(!down_home(get_abs_dir($new_dir)))
		show_error($new_dir . ": " . $GLOBALS["error_msg"]["targetabovehome"]);
	$err = false;
	for($i = 0; $i < $cnt; ++$i){
		$tmp = stripslashes($GLOBALS['__POST']["selitems"][$i]);
		$new = basename(stripslashes($GLOBALS['__POST']["newitems"][$i]));
		if(jx_isFTPMode()){
			$abs_item = get_item_info($dir, $tmp);
			$abs_new_item = get_item_info('/' . $new_dir, $new);
		} else{
			$abs_item = get_abs_item($dir, $tmp);
			$abs_new_item = get_abs_item($new_dir, $new);
		}
		$items[$i] = $tmp;
		if($new == ""){
			$error[$i] = $GLOBALS["error_msg"]["miscnoname"];
			$err = true;
			continue;
		}
		if(!@$GLOBALS['jx_File']->file_exists($abs_item)){
			$error[$i] = $GLOBALS["error_msg"]["itemexist"];
			$err = true;
			continue;
		}
		if(!get_show_item($dir, $tmp)){
			$error[$i] = $GLOBALS["error_msg"]["accessitem"];
			$err = true;
			continue;
		}
		if(@$GLOBALS['jx_File']->file_exists($abs_new_item)){
			$error[$i] = $GLOBALS["error_msg"]["targetdoesexist"];
			$err = true;
			continue;
		}
		if($GLOBALS["action"] == "copy"){
			if(@is_link($abs_item) || get_is_file($abs_item)){
				if(jx_isFTPMode())
					$abs_item = '/' . $dir . '/' . $abs_item['name'];
				$ok = @$GLOBALS['jx_File']->copy($abs_item, $abs_new_item);
			} elseif(@get_is_dir($abs_item)){
				$dir = jx_isFTPMode() ? '/' . $dir . '/' . $abs_item['name'] . '/' : $abs_item;
				if(jx_isFTPMode())
					$abs_new_item .= '/';
				$ok = $GLOBALS['jx_File']->copy_dir($dir, $abs_new_item);
			}
		} else{
			$ok = $GLOBALS['jx_File']->rename($abs_item, $abs_new_item);
		}
		if($ok === false || PEAR::isError($ok)){
			$error[$i] = ($GLOBALS["action"] == "copy" ? $GLOBALS["error_msg"]["copyitem"] : $GLOBALS["error_msg"]["moveitem"]);
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
			$err_msg .= $items[$i] . " : " . $error[$i] . "<br />\n";
		}
		show_error($err_msg);
	}
	header("Location: " . make_link("list", $dir, null));
}


?>
