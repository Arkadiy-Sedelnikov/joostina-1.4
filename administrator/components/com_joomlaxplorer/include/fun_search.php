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
function find_item($dir, $pat, &$list, $recur){
	$handle = @opendir(get_abs_dir($dir));
	if($handle === false)
		return;
	while(($new_item = readdir($handle)) !== false){
		if(!@file_exists(get_abs_item($dir, $new_item)))
			continue;
		if(!get_show_item($dir, $new_item))
			continue;
		if(@preg_match("/" . $pat . "/i", $new_item))
			$list[] = array($dir, $new_item);
		if(get_is_dir($dir, $new_item) && $recur){
			find_item(get_rel_item($dir, $new_item), $pat, $list, $recur);
		}
	}
	closedir($handle);
}

function make_list($dir, $item, $subdir){
	$pat = "^" . str_replace("?", ".", str_replace("*", ".*", str_replace(".", "\.", $item))) .
		"$";
	find_item($dir, $pat, $list, $subdir);
	if(is_array($list))
		sort($list);
	return $list;
}

function print_table($list){
	if(!is_array($list))
		return;
	$cnt = count($list);
	for($i = 0; $i < $cnt; ++$i){
		$dir = $list[$i][0];
		$item = $list[$i][1];
		$s_dir = $dir;
		if(strlen($s_dir) > 65)
			$s_dir = substr($s_dir, 0, 62) . "...";
		$s_item = $item;
		if(strlen($s_item) > 45)
			$s_item = substr($s_item, 0, 42) . "...";
		$link = "";
		$target = "";
		if(get_is_dir($dir, $item)){
			$img = "dir.png";
			$link = make_link("list", get_rel_item($dir, $item), null);
		} else{
			$img = get_mime_type($dir, $item, "img");
			$link = $GLOBALS["home_url"] . "/" . get_rel_item($dir, $item);
			$target = "_blank";
		}
		echo "<tr><td>" . "<img border=\"0\" width=\"22\" height=\"22\" ";
		echo "align=\"absmiddle\" src=\"" . _QUIXPLORER_URL . "/images/" . $img . "\" alt=\"\">&nbsp;";
		echo "<a href=\"" . $link . "\" target=\"" . $target . "\">";
		echo $s_item . "</a></td><td><a href=\"" . make_link("list", $dir, null) . "\"> /";
		echo $s_dir . "</a></td></tr>\n";
	}
}

function search_items($dir){
	if(isset($GLOBALS['__POST']["searchitem"])){
		$searchitem = stripslashes($GLOBALS['__POST']["searchitem"]);
		$subdir = (isset($GLOBALS['__POST']["subdir"]) && $GLOBALS['__POST']["subdir"] ==
			"y");
		$list = make_list($dir, $searchitem, $subdir);
	} else{
		$searchitem = null;
		$subdir = true;
	}
	$msg = $GLOBALS["messages"]["actsearchresults"];
	if($searchitem != null)
		$msg .= ": (/" . get_rel_item($dir, $searchitem) . ")";
	show_header($msg);
	echo "<br><table><form name=\"searchform\" action=\"" . make_link("search", $dir, null);
	echo "\" method=\"post\">\n<tr><td><input name=\"searchitem\" type=\"text\" size=\"25\" value=\"";
	echo $searchitem . "\"><INPUT type=\"submit\" value=\"" . $GLOBALS["messages"]["btnsearch"];
	echo "\">&nbsp;<input type=\"button\" value=\"" . $GLOBALS["messages"]["btnclose"];
	echo "\" onClick=\"javascript:location='" . make_link("list", $dir, null);
	echo "';\"></td></tr><tr><td><input type=\"checkbox\" name=\"subdir\" value=\"y\"";
	echo ($subdir ? " checked>" : ">") . $GLOBALS["messages"]["miscsubdirs"] .
		"</td></tr></form></table>\n";
	if($searchitem != null){
		echo "<table width=\"95%\"><tr><td colspan=\"2\"><hr></td></tr>\n";
		if(count($list) > 0){
			echo "<tr>\n<td width=\"42%\" class=\"header\"><b>" . $GLOBALS["messages"]["nameheader"];
			echo "</b></td>\n<td width=\"58%\" class=\"header\"><b>" . $GLOBALS["messages"]["pathheader"];
			echo "</b></td></tr>\n<tr><td colspan=\"2\"><hr></td></tr>\n";
			print_table($list);
			echo "<tr><td colspan=\"2\"><hr></td></tr>\n<tr><td class=\"header\">" . count($list) .
				" ";
			echo $GLOBALS["messages"]["miscitems"] . ".</td><td class=\"header\"></td></tr>\n";
		} else{
			echo "<tr><td>" . $GLOBALS["messages"]["miscnoresult"] . "</td></tr>";
		}
		echo "<tr><td colspan=\"2\"><hr></td></tr></table>\n";
	}
	?>
<script language="JavaScript1.2" type="text/javascript">
	<!--
	if (document.searchform) document.searchform.searchitem.focus();
	// -->
</script><?php
}


?>
