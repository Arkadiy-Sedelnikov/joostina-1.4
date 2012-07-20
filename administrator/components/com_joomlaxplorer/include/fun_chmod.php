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
function chmod_item($dir, $item){
	if(($GLOBALS["permissions"] & 01) != 01)
		show_error($GLOBALS["error_msg"]["accessfunc"]);
	if(!empty($GLOBALS['__POST']["selitems"])){
		$cnt = count($GLOBALS['__POST']["selitems"]);
	} else{
		$GLOBALS['__POST']["selitems"][] = $item;
		$cnt = 1;
	}
	if(!empty($GLOBALS['__POST']['do_recurse'])){
		$do_recurse = true;
	} else{
		$do_recurse = false;
	}
	if(isset($GLOBALS['__POST']["confirm"]) && $GLOBALS['__POST']["confirm"] ==
		"true"
	){
		$bin = '';
		for($i = 0; $i < 3; $i++)
			for($j = 0; $j < 3; $j++){
				$tmp = "r_" . $i . $j;
				if(isset($GLOBALS['__POST'][$tmp]) && $GLOBALS['__POST'][$tmp] == "1"){
					$bin .= '1';
				} else{
					$bin .= '0';
				}
			}
		if($bin == '0'){
			show_error($item . ": " . $GLOBALS["error_msg"]["permchange"]);
		}
		$old_bin = $bin;
		for($i = 0; $i < $cnt; ++$i){
			if(jx_isFTPMode()){
				$mode = decoct(bindec($bin));
			} else{
				$mode = bindec($bin);
			}
			$item = $GLOBALS['__POST']["selitems"][$i];
			if(jx_isFTPMode()){
				$abs_item = get_item_info($dir, $item);
			} else{
				$abs_item = get_abs_item($dir, $item);
			}
			if(!$GLOBALS['jx_File']->file_exists($abs_item)){
				show_error($item . ": " . $GLOBALS["error_msg"]["fileexist"]);
			}
			if(!get_show_item($dir, $item)){
				show_error($item . ": " . $GLOBALS["error_msg"]["accessfile"]);
			}
			if($do_recurse){
				$ok = $GLOBALS['jx_File']->chmodRecursive($abs_item, $mode);
			} else{
				if(get_is_dir($abs_item)){
					$bin = substr_replace($bin, '1', 2, 1);
					$bin = substr_replace($bin, '1', 5, 1);
					$bin = substr_replace($bin, '1', 8, 1);
					if(jx_isFTPMode()){
						$mode = decoct(bindec($bin));
					} else{
						$mode = bindec($bin);
					}
				}
				$ok = @$GLOBALS['jx_File']->chmod($abs_item, $mode);
			}
			$bin = $old_bin;
		}
		if(!$ok || PEAR::isError($ok)){
			show_error($abs_item . ": " . $GLOBALS["error_msg"]["permchange"]);
		}
		header("Location: " . make_link("link", $dir, null));
		return;
	}
	if(jx_isFTPMode()){
		$abs_item = get_item_info($dir, $GLOBALS['__POST']["selitems"][0]);
	} else{
		$abs_item = get_abs_item($dir, $GLOBALS['__POST']["selitems"][0]);
	}
	$mode = parse_file_perms(get_file_perms($abs_item));
	if($mode === false){
		show_error($GLOBALS['__POST']["selitems"][0] . ": " . $GLOBALS["error_msg"]["permread"]);
	}
	$pos = "rwx";
	$text = "";
	for($i = 0; $i < $cnt; ++$i){
		$s_item = get_rel_item($dir, $GLOBALS['__POST']["selitems"][$i]);
		if(strlen($s_item) > 50)
			$s_item = "..." . substr($s_item, -47);
		$text .= ", " . $s_item;
	}
	show_header($GLOBALS["messages"]["actperms"]);
	echo "<br/><br/><div style=\"max-height: 200px; max-width: 800px;overflow:auto;\">/" .
		$text . '</div>';
	echo '<br /><form method="post" action="' . make_link("chmod", $dir, $item) . "\">
	<input type=\"hidden\" name=\"confirm\" value=\"true\" />";
	if($cnt > 1 || empty($GLOBALS['__GET']["item"])){
		for($i = 0; $i < $cnt; ++$i){
			echo "<input type=\"hidden\" name=\"selitems[]\" value=\"" . stripslashes($GLOBALS['__POST']["selitems"][$i]) .
				"\" />\n";
		}
	} else{
		echo "<input type=\"hidden\" name=\"item\" value=\"" . stripslashes($GLOBALS['__GET']["item"]) .
			"\" />\n";
	}
	echo "
	<table class=\"adminform\" style=\"width:175px;\">\n";
	for($i = 0; $i < 3; ++$i){
		echo "<tr><td>" . $GLOBALS["messages"]["miscchmod"][$i] . "</td>";
		for($j = 0; $j < 3; ++$j){
			echo "<td><label for=\"r_" . $i . $j . "\"\">" . $pos{$j} . "&nbsp;</label><input type=\"checkbox\"";
			if($mode{(3 * $i) + $j} != "-")
				echo " checked=\"checked\"";
			echo " name=\"r_" . $i . $j . "\" id=\"r_" . $i . $j . "\" value=\"1\" /></td>";
		}
		echo "</tr>\n";
	}
	echo "</table>\n<br/>";
	echo "<table>\n<tr><tr><td colspan=\"2\">\n<input name=\"do_recurse\" id=\"do_recurse\" type=\"checkbox\" value=\"1\" /><label for=\"do_recurse\">" .
		$GLOBALS["messages"]["recurse_subdirs"] . "</label></td></tr>\n";
	echo "<tr><tr><td>\n<input type=\"submit\" value=\"" . $GLOBALS["messages"]["btnchange"];
	echo "\"></td>\n<td><input type=\"button\" value=\"" . $GLOBALS["messages"]["btncancel"];
	echo "\" onclick=\"javascript:location='" . make_link("list", $dir, null) . "';\">\n</td></tr></form></table><br />\n";
}


?>
