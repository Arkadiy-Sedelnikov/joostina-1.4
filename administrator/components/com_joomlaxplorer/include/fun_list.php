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
 * @author The The QuiX project (http://quixplorer.sourceforge.net)
 **/
defined('_JLINDEX') or die();
require_once (_QUIXPLORER_PATH . '/include/fun_bookmarks.php');
function make_list($_list1, $_list2){
	$list = array();
	if($GLOBALS["srt"] == "yes"){
		$list1 = $_list1;
		$list2 = $_list2;
	} else{
		$list1 = $_list2;
		$list2 = $_list1;
	}
	if(is_array($list1)){
		while(list($key, $val) = each($list1)){
			$list[$key] = $val;
		}
	}
	if(is_array($list2)){
		while(list($key, $val) = each($list2)){
			$list[$key] = $val;
		}
	}
	return $list;
}

function make_tables($dir, &$dir_list, &$file_list, &$tot_file_size, &$num_items){
	$homedir = realpath($GLOBALS['home_dir']);
	$tot_file_size = $num_items = 0;
	$handle = @$GLOBALS['jx_File']->opendir(get_abs_dir($dir));
	if($handle === false && $dir == ""){
		$handle = @$GLOBALS['jx_File']->opendir($homedir . $GLOBALS['separator']);
	}
	if($handle === false)
		show_error($dir . ": " . $GLOBALS["error_msg"]["opendir"]);
	while(($new_item = @$GLOBALS['jx_File']->readdir($handle)) !== false){
		if(is_array($new_item)){
			$abs_new_item = $new_item;
		} else{
			$abs_new_item = get_abs_item($dir, $new_item);
		}
		if($new_item == "." || $new_item == "..")
			continue;
		if(!@$GLOBALS['jx_File']->file_exists($abs_new_item))
			if(!get_show_item($dir, $new_item))
				continue;
		$new_file_size = @$GLOBALS['jx_File']->filesize($abs_new_item);
		$tot_file_size += $new_file_size;
		$num_items++;
		$new_item_name = $new_item;
		if(jx_isFTPMode()){
			$new_item_name = $new_item['name'];
		}
		if(get_is_dir($abs_new_item)){
			if($GLOBALS["order"] == "mod"){
				$dir_list[$new_item_name] = @$GLOBALS['jx_File']->filemtime($abs_new_item);
			} else{
				$dir_list[$new_item_name] = $new_item;
			}
		} else{
			if($GLOBALS["order"] == "size"){
				$file_list[$new_item_name] = $new_file_size;
			} elseif($GLOBALS["order"] == "mod"){
				$file_list[$new_item_name] = @$GLOBALS['jx_File']->filemtime($abs_new_item);
			} elseif($GLOBALS["order"] == "type"){
				$file_list[$new_item_name] = get_mime_type($abs_new_item, "type");
			} else{
				$file_list[$new_item_name] = $new_item;
			}
		}
	}
	@$GLOBALS['jx_File']->closedir($handle);
	if(is_array($dir_list)){
		if($GLOBALS["order"] == "mod"){
			if($GLOBALS["srt"] == "yes")
				arsort($dir_list);
			else
				asort($dir_list);
		} else{
			if($GLOBALS["srt"] == "yes")
				ksort($dir_list);
			else
				krsort($dir_list);
		}
	}
	if(is_array($file_list)){
		if($GLOBALS["order"] == "mod"){
			if($GLOBALS["srt"] == "yes")
				arsort($file_list);
			else
				asort($file_list);
		} elseif($GLOBALS["order"] == "size" || $GLOBALS["order"] == "type"){
			if($GLOBALS["srt"] == "yes")
				asort($file_list);
			else
				arsort($file_list);
		} else{
			if($GLOBALS["srt"] == "yes")
				ksort($file_list);
			else
				krsort($file_list);
		}
	}
}

function print_table($dir, $list, $allow){
	global $dir_up;
	$mainframe = mosMainFrame::getInstance(true);
	$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/file_ico/';
	if(!is_array($list))
		return;
	if($dir != "" || strstr($dir, _QUIXPLORER_PATH)){
		echo "<tr class=\"row1\">
	 			<td>&nbsp;</td>
	 			<td valign=\"baseline\">
	 				<a href=\"" . make_link("list", $dir_up, null) . "\">
	 				<img border=\"0\" align=\"absmiddle\" src=\"" . $cur_file_icons_path . "/uparrow.png\" alt=\"" . $GLOBALS["messages"]["uplink"] . "\" title=\"" . $GLOBALS["messages"]["uplink"] . "\"/>&nbsp;&nbsp;..</a>
	 			</td>
	 			<td>&nbsp;</td>
	 			<td>&nbsp;</td>
	 			<td>&nbsp;</td>
	 			<td>&nbsp;</td>
	 			<td>&nbsp;</td>";
		if(extension_loaded("posix")){
			echo "<td>&nbsp;</td>";
		}
		echo "</tr>";
	}
	$i = 0;
	$toggle = false;


	while(list($item, $info) = each($list)){
		if(is_array($info)){
			$abs_item = $info;
			if(extension_loaded('posix')){
				$user_info = posix_getpwnam($info['user']);
				$file_info['uid'] = $user_info['uid'];
				$file_info['gid'] = $user_info['gid'];
			}
		} else{
			$abs_item = get_abs_item($dir, $item);
			$file_info = @stat($abs_item);
		}
		$is_writable = @$GLOBALS['jx_File']->is_writable($abs_item);
		$is_chmodable = @$GLOBALS['jx_File']->is_chmodable($abs_item);
		$is_readable = @$GLOBALS['jx_File']->is_readable($abs_item);
		$is_deletable = @$GLOBALS['jx_File']->is_deletable($abs_item);
		$target = "";
		$extra = "";
		if(@$GLOBALS['jx_File']->is_link($abs_item)){
			$extra = " -> " . @readlink($abs_item);
		}
		if(@get_is_dir($abs_item, '')){
			$link = make_link("list", get_rel_item($dir, $item), null);
		} else{
			if(get_is_editable($abs_item) && $is_writable){
				$link = make_link('edit', $dir, $item);
			} elseif($is_readable){
				if(strstr(get_abs_dir($dir), JPATH_BASE) && !$GLOBALS['jx_File']->is_link($abs_item)){
					$link = $GLOBALS["home_url"] . "/" . get_rel_item($dir, $item);
					$target = '_blank';
				} else{
					$link = make_link('download', $dir, $item);
				}
			}
		}
		if(jx_isIE()){
			echo '<tr onmouseover="style.backgroundColor=\'#D8ECFF\';" onmouseout="style.backgroundColor=\'#EAECEE\';" bgcolor=\'#EAECEE\'>';
		} else{
			$toggle = ($toggle) ? '1' : '0';
			echo "<tr class=\"row$toggle\">";
			$toggle = !$toggle;
		}
		echo "<td><input type=\"checkbox\" id=\"item_$i\" name=\"selitems[]\" value=\"";
		echo urlencode($item) . "\" onclick=\"javascript:Toggle(this);\" /></td>\n";
		echo "<td nowrap=\"nowrap\" align=\"left\">";
		if($is_readable){
			echo "<a href=\"" . $link . "\" target=\"" . $target . "\">";
		}
		echo "<img border=\"0\" ";
		echo "align=\"absmiddle\" src=\"{$cur_file_icons_path}" . get_mime_type($abs_item, "img") . "\" alt=\"\" />&nbsp;";
		$s_item = $item;
		if(strlen($s_item) > 50)
			$s_item = substr($s_item, 0, 47) . "...";
		echo htmlspecialchars($s_item . $extra);
		if($is_readable){
			echo "</a>";
		}
		echo "</td>\n";
		echo "<td>" . parse_file_size(get_file_size($abs_item)) . "</td>\n";
		echo "<td>" . get_mime_type($abs_item, "type") . "</td>\n";
		echo "<td>" . parse_file_date(get_file_date($abs_item)) . "</td>\n";
		echo "<td>";
		if($allow && $is_chmodable){
			echo "<a href=\"" . make_link("chmod", $dir, $item) . "\" title=\"";
			echo $GLOBALS["messages"]["permlink"] . "\">";
		}
		$perms = get_file_perms($abs_item);
		if(strlen($perms) > 3){
			$perms = substr($perms, 2);
		}
		echo '<strong>' . $perms . '</strong> ' . parse_file_type($dir, $item) . parse_file_perms($perms);
		if($allow && $is_chmodable){
			echo "</a>";
		}
		echo "</td>\n";
		error_reporting(E_ALL);
		if(extension_loaded("posix")){
			echo "<td>\n";
			$user_info = posix_getpwuid($file_info["uid"]);
			$group_info = posix_getgrgid($file_info["gid"]);
			echo $user_info["name"] . " (" . $file_info["uid"] . ") /<br/>";
			echo $group_info["name"] . " (" . $file_info["gid"] . ")";
			echo "</td>\n";
		}
		echo "<td style=\"white-space:nowrap;\">\n";
		if($allow && $is_deletable){
			echo "<a href=\"" . make_link("rename", $dir, $item) . "\">";
			echo "<img border=\"0\" ";
			echo "src=\"{$cur_file_icons_path}rename.png\" alt=\"" . $GLOBALS["messages"]["renamelink"] . "\" title=\"";
			echo $GLOBALS["messages"]["renamelink"] . "\" /></a>\n";
		} else{
			echo "<img border=\"0\" ";
			echo "src=\"{$cur_file_icons_path}rename.png\" alt=\"" . $GLOBALS["messages"]["renamelink"] . "\" title=\"";
			echo $GLOBALS["messages"]["renamelink"] . "\" />\n";
		}
		if(get_is_editable($abs_item)){
			if($allow && $is_writable){
				echo "<a href=\"" . make_link("edit", $dir, $item) . "\">";
				echo "<img border=\"0\" ";
				echo "src=\"{$cur_file_icons_path}edit.png\" alt=\"" . $GLOBALS["messages"]["editlink"] . "\" title=\"";
				echo $GLOBALS["messages"]["editlink"] . "\" /></a>\n";
			} else{
				echo "<img border=\"0\" ";
				echo "src=\"{$cur_file_icons_path}edit.png\" alt=\"" . $GLOBALS["messages"]["editlink"] . "\" title=\"";
				echo $GLOBALS["messages"]["editlink"] . "\" />\n";
			}
		} else{
			if(jx_isArchive($item) && !jx_isFTPMode()){
				echo "<a ";
				echo "onclick=\"javascript: ClearAll(); getElementById('item_$i').checked = true; if( confirm('" . ($GLOBALS["messages"]["extract_warning"]) . "') ) { document.selform.do_action.value='extract'; document.selform.submit(); } else { getElementById('item_$i').checked = false; return false;}\" ";
				echo "href=\"" . make_link("extract", $dir, $item) . "\" title=\"" . $GLOBALS["messages"]["extractlink"] . "\">";
				echo "<img border=\"0\" width=\"22\" height=\"20\" ";
				echo "src=\"{$cur_file_icons_path}compress.png\" alt=\"" . $GLOBALS["messages"]["extractlink"];
				echo "\" title=\"" . $GLOBALS["messages"]["extractlink"] . "\" /></a>\n";
			} else{
				echo "<img border=\"0\" src=\"{$cur_file_icons_path}none.gif\" alt=\"\" />\n";
			}
		}
		if(get_is_editable($abs_item) && $GLOBALS['jx_File']->is_readable($abs_item) &&
			get_is_file($abs_item)
		){
			$link = str_replace('/index2.php', '/index3.php', make_link("view", $dir, $item));
			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=750,height=580,directories=no,location=no,screenX=100,screenY=100';
			echo "<a href=\"" . $link . "\" onclick=\"window.open('$link','win2','$status'); return false;\" title=\"" .
				$GLOBALS["messages"]["viewlink"] . "\">";
			echo "<img border=\"0\" ";
			echo "src=\"{$cur_file_icons_path}view.png\" alt=\"" . $GLOBALS["messages"]["viewlink"] .
				"\" /></a>\n";
		}
		if(get_is_file($abs_item)){
			if($allow){
				echo "<a href=\"" . make_link("download", $dir, $item) . "\" title=\"" . $GLOBALS["messages"]["downlink"] . "\">";
				echo "<img border=\"0\" ";
				echo "src=\"{$cur_file_icons_path}down.png\" alt=\"" . $GLOBALS["messages"]["downlink"];
				echo "\" title=\"" . $GLOBALS["messages"]["downlink"] . "\" /></a>\n";
			} else
				if(!$allow){
					echo "<td><img border=\"0\" ";
					echo "src=\"/{$cur_file_icons_path}down.png\" alt=\"" . $GLOBALS["messages"]["downlink"];
					echo "\" title=\"" . $GLOBALS["messages"]["downlink"] . "\" />\n";
				}
		} else{
			echo "<img border=\"0\" src=\"{$cur_file_icons_path}none.gif\" alt=\"\" />\n";
		}
		if(get_is_file($abs_item)){
			if($allow && $GLOBALS['jx_File']->is_deletable($abs_item)){
				$confirm_msg = sprintf($GLOBALS["messages"]["confirm_delete_file"], $item);
				echo "<a name=\"link_item_$i\" href=\"#link_item_$i\" title=\"" . $GLOBALS["messages"]["dellink"] . "\" onclick=\"javascript: ClearAll(); document.getElementById('item_$i').checked = true; if( confirm('" . $confirm_msg . "') ) { document.selform.do_action.value='delete'; document.selform.submit(); } else { document.getElementById('item_$i').checked = false; return false;}\">";
				echo "<img border=\"0\" src=\"{$cur_file_icons_path}delete.png\" alt=\"" . $GLOBALS["messages"]["dellink"] . "\" title=\"" . $GLOBALS["messages"]["dellink"] . "\" /></a>\n";
			} else{
				echo "<img border=\"0\" src=\"/{$cur_file_icons_path}delete.png\" alt=\"" . $GLOBALS["messages"]["dellink"];
				echo "\" title=\"" . $GLOBALS["messages"]["dellink"] . "\" />\n";
			}
		} else{
			echo "<img border=\"0\" src=\"{$cur_file_icons_path}none.gif\" alt=\"\" />\n";
		}
		echo "</td></tr>\n";
		$i++;
	}
}

function list_dir($dir){
	global $dir_up;
	$mainframe = mosMainFrame::getInstance();
	mosCommonHTML::loadOverlib();
	$mainframe->addJS(JPATH_SITE . '/' . JADMIN_BASE . '/components/com_joomlaxplorer/scripts/joomlaxplorer.js');
	$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
	?>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
<?php

	$allow = ($GLOBALS["permissions"] & 01) == 01;
	$admin = ((($GLOBALS["permissions"] & 04) == 04) || (($GLOBALS["permissions"] & 02) == 02));
	$dir_up = dirname($dir);
	if($dir_up == ".")
		$dir_up = "";
	if(!get_show_item($dir_up, basename($dir))){
		show_error($dir . " : " . $GLOBALS["error_msg"]["accessdir"]);
	}
	make_tables($dir, $dir_list, $file_list, $tot_file_size, $num_items);
	$dirs = explode("/", $dir);
	$implode = "";
	$dir_links = "<a href=\"" . make_link("list", "", null) . "\">..</a>/";
	foreach($dirs as $directory){
		if($directory != ""){
			$implode .= $directory . "/";
			$dir_links .= "<a href=\"" . make_link("list", $implode, null) . "\">$directory</a>/";
		}
	}
	$images = "&nbsp;<img width=\"10\" height=\"10\" border=\"0\" align=\"absmiddle\" src=\"" . $cur_file_icons_path . "/";
	if($GLOBALS["srt"] == "yes"){
		$_srt = "no";
		$images .= "uparrow.png\" alt=\"^\">";
	} else{
		$_srt = "yes";
		$images .= "downarrow.png\" alt=\"v\">";
	}
	echo '<table class="adminheading"><tbody><tr><th class="filemanager">' . $GLOBALS["messages"]["actdir"] . ": " . $dir_links . '</th></tr></tbody></table>';

	echo "<form name=\"selform\" method=\"post\" action=\"" . make_link("post", $dir, null) . "\">
	<input type=\"hidden\" name=\"do_action\" /><input type=\"hidden\" name=\"first\" value=\"y\" />
	<table class=\"adminlist\" width=\"95%\">\n";
	if(extension_loaded("posix")){
		$owner_info = '<th width="15%" class="title">' . $GLOBALS['messages']['miscowner'] . '&nbsp;';
		if(jx_isFTPMode()){
			$my_user_info = posix_getpwnam($_SESSION['ftp_login']);
			$my_group_info = posix_getgrgid($my_user_info['gid']);
		} else{
			$my_user_info = posix_getpwuid(posix_geteuid());
			$my_group_info = posix_getgrgid(posix_getegid());
		}
		$owner_info .= mosTooltip(mysql_real_escape_string(sprintf($GLOBALS['messages']['miscownerdesc'], $my_user_info['name'], $my_user_info['uid'], $my_group_info['name'], $my_group_info['gid'])));
		$owner_info .= "</th>\n";
		$colspan = 8;
	} else{
		$owner_info = "";
		$colspan = 7;
	}
	echo "<tr><th width=\"2%\" class=\"title\"><input type=\"checkbox\" name=\"toggleAllC\" onclick=\"javascript:ToggleAll(this);\" /></th><th width=\"34%\" class=\"title\">\n";
	if($GLOBALS["order"] == "name")
		$new_srt = $_srt;
	else
		$new_srt = "yes";
	echo "<a href=\"" . make_link("list", $dir, null, "name", $new_srt) . "\">" . $GLOBALS["messages"]["nameheader"];
	if($GLOBALS["order"] == "name")
		echo $images;
	echo '</a>';
	echo "</th><th width=\"10%\" class=\"title\">";
	if($GLOBALS["order"] == "size")
		$new_srt = $_srt;
	else
		$new_srt = "yes";
	echo "<a href=\"" . make_link("list", $dir, null, "size", $new_srt) . "\">" . $GLOBALS["messages"]["sizeheader"];
	if($GLOBALS["order"] == "size")
		echo $images;
	echo "</a></th><th width=\"10%\" class=\"title\">";
	if($GLOBALS["order"] == "type")
		$new_srt = $_srt;
	else
		$new_srt = "yes";
	echo "<a href=\"" . make_link("list", $dir, null, "type", $new_srt) . "\">" . $GLOBALS["messages"]["typeheader"];
	if($GLOBALS["order"] == "type")
		echo $images;
	echo "</a></th><th width=\"10%\" class=\"title\">";
	if($GLOBALS["order"] == "mod")
		$new_srt = $_srt;
	else
		$new_srt = "yes";
	echo "<a href=\"" . make_link("list", $dir, null, "mod", $new_srt) . "\">" . $GLOBALS["messages"]["modifheader"];
	if($GLOBALS["order"] == "mod")
		echo $images;
	echo "</a></th><th width=\"10%\" class=\"title\">" . $GLOBALS["messages"]["permheader"] . "\n";
	echo "</th>";
	echo $owner_info;
	echo "<th width=\"10%\" class=\"title\">" . $GLOBALS["messages"]["actionheader"] . "</th></tr>\n";
	print_table($dir, make_list($dir_list, $file_list), $allow);
	echo "<tr><td colspan=\"$colspan\"><hr/></td></tr><tr>\n<td></td>";
	echo "<td>" . $num_items . " " . $GLOBALS["messages"]["miscitems"] . " (";
	if(function_exists("disk_free_space")){
		$size = disk_free_space($GLOBALS['home_dir'] . $GLOBALS['separator']);
		$free = parse_file_size($size);
	} elseif(function_exists("diskfreespace")){
		$size = diskfreespace($GLOBALS['home_dir'] . $GLOBALS['separator']);
		$free = parse_file_size($size);
	} else
		$free = "?";
	echo $GLOBALS["messages"]["miscfree"] . ": " . $free . ")</td>\n";
	echo '<td>' . parse_file_size($tot_file_size) . '</td>';
	for($i = 0; $i < ($colspan - 3); ++$i)
		echo "<td></td>";
	echo "</tr>\n<tr><td colspan=\"$colspan\"><hr/></td></tr></table></form>";

	show_header($allow, $dir);

	?>
<script type="text/javascript"><!--
// Uncheck all items (to avoid problems with new items)
var ml = document.selform;
var len = ml.elements.length;
for (var i = 0; i < len; ++i) {
	var e = ml.elements[i];
	if (e.name == "selitems[]" && e.checked == true) {
		e.checked = false;
	}
}
// --></script>

<?php
}


?>