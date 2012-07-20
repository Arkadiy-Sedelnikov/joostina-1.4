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
$GLOBALS['isWindows'] = substr(PHP_OS, 0, 3) == 'WIN';
function make_link($_action, $_dir, $_item = null, $_order = null, $_srt = null,
	$languages = null){
	if($_action == "" || $_action == null)
		$_action = "list";
	if($_item == "")
		$_item = null;
	if($_order == null)
		$_order = $GLOBALS["order"];
	if($_srt == null)
		$_srt = $GLOBALS["srt"];
	if($languages == null)
		$languages = (isset($GLOBALS["lang"]) ? $GLOBALS["lang"] : null);
//	$link = $GLOBALS["script_name"]."?option=com_joomlaxplorer&action=".$_action;
	$link = "?option=com_joomlaxplorer&action=" . $_action;
	$link .= "&dir=" . urlencode($_dir);
	if($_item != null)
		$link .= "&item=" . urlencode($_item);
	if($_order != null)
		$link .= "&order=" . $_order;
	if($_srt != null)
		$link .= "&srt=" . $_srt;
	if($languages != null)
		$link .= "&lang=" . $languages;
	return $link;
}

function get_abs_dir($dir){
	if(jx_isFTPMode()){
		if($dir != '' && $dir[0] != '/'){
			$dir = '/' . $dir;
		}
		return $dir;
	}
	$abs_dir = $GLOBALS["home_dir"];
	if($dir != "" && !stristr($dir, $abs_dir))
		$abs_dir .= "/" . $dir;
	elseif(stristr($dir, $abs_dir))
		$abs_dir = "/" . $dir;
	$realpath = realpath($abs_dir);
	return $realpath;
}

function get_abs_item($dir, $item){
	if(is_array($item)){
		$abs_item = '/' . get_abs_dir($dir) . "/" . $item['name'];
		if(get_is_dir($item))
			$abs_item .= '/';
		return $abs_item;
	}
	return get_abs_dir($dir) . "/" . $item;
}

function get_item_info($dir, $item){
	$ls = getCachedFTPListing($dir);
	foreach($ls as $entry){
		if($entry['name'] == $item){
			return $entry;
		}
	}
	if($dir != ''){
		return $dir . '/' . $item;
	}
	return $item;
}

function get_rel_item($dir, $item){
	if($dir != "")
		return $dir . "/" . $item;
	else
		return $item;
}

function get_is_file($abs_item){
	if(jx_isFTPMode() && is_array($abs_item)){
		return empty($abs_item['is_dir']);
	} elseif(jx_isFTPMode()){
		$info = get_item_info(dirname($abs_item), basename($abs_item));
		return empty($info['is_dir']);
	}
	return @is_file($abs_item);
}

function get_is_dir($abs_item){
	if(jx_isFTPMode() && is_array($abs_item)){
		return !empty($abs_item['is_dir']);
	} elseif(jx_isFTPMode()){
		$info = get_item_info(dirname($abs_item), basename($abs_item));
		return !empty($info['is_dir']);
	}
	return @is_dir($abs_item);
}

function parse_file_type($abs_item){
	if(@get_is_dir($abs_item))
		return "d";
	if(@is_link($abs_item))
		return "l";
	return "-";
}

function get_file_perms($item){
	if(jx_isFTPMode()){
		$perms = decoct(bindec(decode_ftp_rights($item['rights'])));
		return $perms;
	}
	return @decoct(@fileperms($item) & 0777);
}

function parse_file_perms($mode){
	if(strlen($mode) < 3)
		return "---------";
	$parsed_mode = "";
	for($i = 0; $i < 3; $i++){
		if(($mode{$i} & 04))
			$parsed_mode .= "r";
		else
			$parsed_mode .= "-";
		if(($mode{$i} & 02))
			$parsed_mode .= "w";
		else
			$parsed_mode .= "-";
		if(($mode{$i} & 01))
			$parsed_mode .= "x";
		else
			$parsed_mode .= "-";
	}
	return $parsed_mode;
}

function decode_ftp_rights($rights){
	$parsed_mode = "";
	for($i = 0; $i < 9; $i++){
		if(isset($rights[$i]) && ($rights[$i] != '-')){
			$parsed_mode .= '1';
		} else{
			$parsed_mode .= '0';
		}
	}
	return $parsed_mode;
}

function get_file_size($abs_item){
	return @$GLOBALS['jx_File']->filesize($abs_item);
}

function parse_file_size($size){
	if($size >= 1073741824){
		$size = round($size / 1073741824 * 100) / 100 . " GB";
	} elseif($size >= 1048576){
		$size = round($size / 1048576 * 100) / 100 . " MB";
	} elseif($size >= 1024){
		$size = round($size / 1024 * 100) / 100 . " KB";
	} else
		$size = $size . " Bytes";
	if($size == 0)
		$size = "-";
	return $size;
}

function get_file_date($item){
	return @$GLOBALS['jx_File']->filemtime($item);
}

function parse_file_date($date){
	return @date($GLOBALS["date_fmt"], $date);
}

function get_is_image($abs_item){
	if(!get_is_file($abs_item))
		return false;
	if(isset($abs_item['name'])){
		$abs_item = $abs_item['name'];
	}
	return @preg_match("/" . $GLOBALS["images_ext"] . "/i", $abs_item);
}

function get_is_editable($abs_item){
	if(!get_is_file($abs_item))
		return false;
	if(is_array($abs_item)){
		$abs_item = $abs_item['name'];
	}
	foreach($GLOBALS["editable_ext"] as $pat)
		if(@preg_match("/" . $pat . "/i", $abs_item))
			return true;
	return strpos(basename($abs_item), ".") ? false : true;
}

function get_mime_type($abs_item, $query){
	if(get_is_dir($abs_item)){
		$mime_type = $GLOBALS["super_mimes"]["dir"][0];
		$image = $GLOBALS["super_mimes"]["dir"][1];
		if($query == "img")
			return $image;
		else
			return $mime_type;
	}
	$extra = $GLOBALS['jx_File']->is_link($abs_item) ? ' (' . $GLOBALS['mimes']['symlink'] .
		')' : '';
	if(jx_isFTPMode() && isset($abs_item['name'])){
		$abs_item = $abs_item['name'];
	}
	foreach($GLOBALS["used_mime_types"] as $mime){
		list($desc, $img, $ext) = $mime;
		if(@preg_match("/" . $ext . "/i", basename($abs_item))){
			$mime_type = $desc;
			$image = $img;
			if($query == "img")
				return $image;
			else
				return $mime_type . $extra;
		}
	}
	if((function_exists("is_executable") && @is_executable($abs_item)) || @preg_match("/" . $GLOBALS["super_mimes"]["exe"][2] . "/i",
		$abs_item)
	){
		$mime_type = $GLOBALS["super_mimes"]["exe"][0];
		$image = $GLOBALS["super_mimes"]["exe"][1];
	} else{
		$mime_type = $GLOBALS["super_mimes"]["file"][0];
		$image = $GLOBALS["super_mimes"]["file"][1];
	}
	if($query == "img")
		return $image;
	else
		return $mime_type . $extra;
}

function get_show_item($dir, $item){
	if(is_array($item)){
		$item = $item['name'];
	}
	if($item == "." || $item == ".." || (substr($item, 0, 1) == "." && $GLOBALS["show_hidden"] == false))
		return false;
	if($GLOBALS["no_access"] != "" && @preg_match("/" . $GLOBALS["no_access"] . "/i", $item))
		return false;
	if($GLOBALS["show_hidden"] == false){
		$dirs = explode("/", $dir);
		foreach($dirs as $i)
			if(substr($i, 0, 1) == ".")
				return false;
	}
	return true;
}

function get_dir_list($dir = ''){
	$files = mosReadDirectory(get_abs_dir($dir), '.', false, true);
	$dirs = array();
	foreach($files as $item){
		$item = str_replace('\\', '/', $item);
		if(get_is_dir($item)){
			$index = str_replace($GLOBALS['home_dir'] . $GLOBALS['separator'], '', $item);
			$dirs[$index] = basename($index);
		}
	}
	return $dirs;
}

function get_dir_selects($dir){
	$dirs = explode("/", $dir);
	$subdirs = get_dir_list();
	if(empty($dirs[0]))
		array_shift($dirs);
	$dirsCopy = $dirs;
	$implode = '';
	$selectedDir = @$dirs[0];
	$dir_links = jx_selectList('dirselect1', $selectedDir, $subdirs, 1, '', 'onchange="theDir=this.options[this.selectedIndex].value;if(theDir!=\'jx_disabled\' ) chDir(theDir);"');
	$i = 2;
	foreach($dirs as $directory){
		if($directory != ""){
			$implode .= $directory;
			$next = next($dirsCopy);
			$subdirs = get_dir_list($implode);
			if($next !== false){
				$selectedDir .= '/' . $next;
			} else{
				if(sizeof($subdirs) > 0){
					$subdirs = array_merge(array('jx_disabled' => '-'), $subdirs);
				}
			}
			$dir_links .= ' / ' . jx_selectList('dirselect' . $i++, $selectedDir, $subdirs, 1, '', 'onchange="theDir=this.options[this.selectedIndex].value;if(theDir!=\'jx_disabled\' ) chDir(theDir);"');
			$implode .= '/';
		}
	}
	return $dir_links;
}

function copy_dir($source, $dest){
	$ok = true;
	if(!@mkdir($dest, 0777))
		return false;
	$itemlist = mosReadDirectory($source, '.', true, true);
	if(empty($itemlist))
		return false;
	foreach($itemlist as $file){
		if(($file == ".." || $file == "."))
			continue;
		$new_dest = str_replace($source, $dest, $file);
		if(@is_dir($file)){
			@mkdir($new_dest, 0777);
		} else{
			$ok = @copy($file, $new_dest);
		}
	}
	return $ok;
}

function remove($item){
	if(!is_link($item)){
		$item = realpath($item);
	}
	$ok = true;
	if(is_link($item) || is_file($item))
		$ok = unlink($item);
	elseif(is_dir($item)){
		if(($handle = opendir($item)) === false)
			show_error(basename($item) . ": " . $GLOBALS["error_msg"]["opendir"]);
		while(($file = readdir($handle)) !== false){
			if(($file == ".." || $file == "."))
				continue;
			$new_item = $item . "/" . $file;
			if(!file_exists($new_item))
				show_error(basename($item) . ": " . $GLOBALS["error_msg"]["readdir"]);
			if(is_dir($new_item)){
				$ok = remove($new_item);
			} else{
				$ok = unlink($new_item);
			}
		}
		closedir($handle);
		$ok = @rmdir($item);
	}
	return $ok;
}

function chmod_recursive($item, $mode){
	$ok = true;
	if(@is_link($item) || @is_file($item)){
		$ok = @chmod($item, $mode);
	} elseif(@is_dir($item)){
		if(($handle = @opendir($item)) === false){
			add_error(basename($item) . ": " . $GLOBALS["error_msg"]["opendir"]);
			return false;
		}
		while(($file = readdir($handle)) !== false){
			if(($file == ".." || $file == "."))
				continue;
			$new_item = $item . "/" . $file;
			if(!@file_exists($new_item)){
				add_error(basename($item) . ": " . $GLOBALS["error_msg"]["readdir"]);
				continue;
			}
			if(@is_dir($new_item)){
				$ok = chmod_recursive($new_item, $mode);
				if($ok)
					add_message($GLOBALS['messages']['permchange'] . ' ' . $new_item);
			} else{
				$ok = @chmod($new_item, $mode);
				if($ok)
					add_message($GLOBALS['messages']['permchange'] . ' ' . $new_item);
			}
		}
		closedir($handle);
		if(is_dir($item)){
			$bin = decbin($mode);
			$bin = substr_replace($bin, '1', 2, 1);
			$bin = substr_replace($bin, '1', 5, 1);
			$bin = substr_replace($bin, '1', 8, 1);
			$mode = bindec($bin);
		}
		$ok = @chmod($item, $mode);
		if($ok)
			add_message($GLOBALS['messages']['permchange'] . ' ' . $new_item);
	}
	return $ok;
}

function get_max_file_size(){
	return calc_php_setting_bytes(ini_get("upload_max_filesize"));
}

function get_max_upload_limit(){
	return calc_php_setting_bytes(ini_get('post_max_size'));
}

function calc_php_setting_bytes($value){
	if(@preg_match("/G$/i", $value)){
		$value = substr($value, 0, -1);
		$value = round($value * 1073741824);
	} elseif(@preg_match("/M$/i", $value)){
		$value = substr($value, 0, -1);
		$value = round($value * 1048576);
	} elseif(@preg_match("/K$/i", $value)){
		$value = substr($value, 0, -1);
		$value = round($value * 1024);
	}
	return $value;
}

function down_home($abs_dir){
	if(jx_isFTPMode()){
		return true;
	}
	$real_home = @realpath($GLOBALS["home_dir"]);
	$real_dir = @realpath($abs_dir);
	if($real_home === false || $real_dir === false){
		if(@preg_match("/\\.\\./i", $abs_dir))
			return false;
	} else
		if(strcmp($real_home, @substr($real_dir, 0, strlen($real_home)))){
			return false;
		}
	return true;
}

function id_browser(){
	$browser = $GLOBALS['__SERVER']['HTTP_USER_AGENT'];
	if(preg_match('/Opera(/| )([0-9].[0-9]{1,2})/', $browser)){
		return 'OPERA';
	} else
		if(preg_match('/MSIE ([0-9].[0-9]{1,2})/', $browser)){
			return 'IE';
		} else
			if(preg_match('/OmniWeb/([0-9].[0-9]{1,2})/', $browser)){
				return 'OMNIWEB';
			} else
				if(preg_match('/(Konqueror/)(.*)/', $browser)){
					return 'KONQUEROR';
				} else
					if(preg_match('/Mozilla/([0-9].[0-9]{1,2})/', $browser)){
						return 'MOZILLA';
					} else{
						return 'OTHER';
					}
}

function jx_isArchive($file){
	$file_info = pathinfo($file);
	$ext = @$file_info["extension"];
	if($ext == "tar" || $ext == "gz" || $ext == "tgz" || $ext == "zip" || $ext ==
		"bzip2" || $ext == "bz2" || $ext == "tbz"
	){
		return true;
	}
	return false;
}

if(!extension_loaded('posix')){
	function posix_geteuid(){
		return false;
	}

	function posix_getpwnam(){
	}
}
function jx_isIE(){
	return (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT']));
}

if(!function_exists('mosToolTip')){
	function mosToolTip($tooltip, $title = '', $width = '', $image = 'tooltip.png',
		$text = '', $href = '#', $link = 1){
		if($width){
			$width = ', WIDTH, \'' . $width . '\'';
		}
		if($title){
			$title = ', CAPTION, \'' . $title . '\'';
		}
		if(!$text){
			$image = JPATH_SITE . '/includes/js/ThemeOffice/' . $image;
			$text = '<img src="' . $image . '" border="0" />';
		}
		$style = 'style="text-decoration: none; color: #333;"';
		if($href){
			$style = '';
		} else{
			$href = "#";
		}
		if($link){
			$tip = "<a href=\"" . $href . "\" onmouseover=\"return overlib('" . $tooltip . "'" . $title .
				", BELOW, RIGHT" . $width . ");\" onmouseout=\"return nd();\" " . $style . ">" . $text .
				"</a>";
		} else{
			$tip = "<span onmouseover=\"return overlib('" . $tooltip . "'" . $title .
				", BELOW, RIGHT" . $width . ");\" onmouseout=\"return nd();\" " . $style . ">" . $text .
				"</span>";
		}
		return $tip;
	}
}
function jx_selectList($name, $value, $arr, $size = 1, $multiple = "", $extra =
""){
	$html = '';
	if(!empty($arr)){
		$html = "<select class=\"inputbox\" name=\"$name\" id=\"$name\" size=\"$size\" $multiple $extra>\n";
		while(list($key, $val) = each($arr)){
			$selected = "";
			if(is_array($value)){
				if(in_array($key, $value)){
					$selected = "selected=\"selected\"";
				}
			} else{
				if(strtolower($value) == strtolower($key)){
					$selected = "selected=\"selected\"";
				}
			}
			if($val == '-'){
				$val = '- - - - -';
			}
			$html .= "<option value=\"$key\" $selected>$val";
			$html .= "</option>\n";
		}
		$html .= "</select>\n";
	}
	return $html;
}

function jx_scriptTag($src = '', $script = ''){
	if($src != ''){
		return '<script type="text/javascript" src="' . $src . '"></script>';
	}
	if($script != ''){
		return '<script type="text/javascript">' . $script . '</script>';
	}
}

function jx_alertBox($msg){
	return jx_scriptTag('', 'alert( \'' . mysql_real_escape_string($msg) . '\' );');
}

function jx_docLocation($url){
	return jx_scriptTag('', 'document.location=\'' . $url . '\';');
}

function jx_isXHR(){
	return strtolower(mosGetParam($_SERVER, 'HTTP_X_REQUESTED_WITH')) ==
		'xmlhttprequest' || strtolower(mosGetParam($_POST, 'requestType')) ==
		'xmlhttprequest';
}

function jx_exit(){
	$mainframe = mosMainFrame::getInstance();
	;
	if(is_callable(array($mainframe, 'close'))){
		$mainframe->close();
	} else{
		session_write_close();
		exit;
	}
}

function jx_RaiseMemoryLimit($setLimit){
	$memLimit = @ini_get('memory_limit');
	if(stristr($memLimit, 'k')){
		$memLimit = str_replace('k', '', str_replace('K', '', $memLimit)) * 1024;
	} elseif(stristr($memLimit, 'm')){
		$memLimit = str_replace('m', '', str_replace('M', '', $memLimit)) * 1024 * 1024;
	}
	if(stristr($setLimit, 'k')){
		$setLimitB = str_replace('k', '', str_replace('K', '', $setLimit)) * 1024;
	} elseif(stristr($setLimit, 'm')){
		$setLimitB = str_replace('m', '', str_replace('M', '', $setLimit)) * 1024 * 1024;
	}
	if($memLimit < $setLimitB){
		@ini_set('memory_limit', $setLimit);
	}
}

function readFileChunked($filename, $retbytes = true){
	$chunksize = 1 * (1024 * 1024);
	$buffer = '';
	$cnt = 0;
	$handle = fopen($filename, 'rb');
	if($handle === false){
		return false;
	}
	while(!feof($handle)){
		$buffer = fread($handle, $chunksize);
		echo $buffer;
		sleep(1);
		ob_flush();
		flush();
		if($retbytes){
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if($retbytes && $status){
		return $cnt;
	}
	return $status;
}

if(!function_exists('file_put_contents')){
	function file_put_contents($filename, $filecont){
		$handle = fopen($filename, 'w');
		if(is_array($filecont)){
			$size = 0;
			foreach($filecont as $filestring){
				fwrite($handle, $filestring);
				$size += strlen($filestring);
			}
			fclose($handle);
			return $size;
		} else{
			fwrite($handle, $filecont);
			fclose($handle);
			return strlen($filecont);
		}
	}
}
if(!function_exists('scandir')){
	function scandir($dir, $listDirectories = false, $skipDots = true){
		$dirArray = array();
		if($handle = opendir($dir)){
			while(false !== ($file = readdir($handle))){
				if(($file != "." && $file != "..") || $skipDots == true){
					if($listDirectories == false){
						if(is_dir($file)){
							continue;
						}
					}
					array_push($dirArray, basename($file));
				}
			}
			closedir($handle);
		}
		return $dirArray;
	}
}
?>
