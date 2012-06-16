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
function upload_items($dir){
	if(($GLOBALS["permissions"] & 01) != 01)
		show_error($GLOBALS["error_msg"]["accessfunc"]);
	if(isset($GLOBALS['__POST']["confirm"]) && $GLOBALS['__POST']["confirm"] ==
		"true"
	){
		$cnt = count($GLOBALS['__FILES']['userfile']['name']);
		$err = false;
		$err_avaliable = isset($GLOBALS['__FILES']['userfile']['error']);
		for($i = 0; $i < $cnt; $i++){
			$errors[$i] = null;
			$tmp = $GLOBALS['__FILES']['userfile']['tmp_name'][$i];
			$items[$i] = stripslashes($GLOBALS['__FILES']['userfile']['name'][$i]);
			if($err_avaliable)
				$up_err = $GLOBALS['__FILES']['userfile']['error'][$i];
			else
				$up_err = (file_exists($tmp) ? 0 : 4);
			$abs = get_abs_item($dir, $items[$i]);
			if($items[$i] == "" || $up_err == 4)
				continue;
			if($up_err == 1 || $up_err == 2){
				$errors[$i] = $GLOBALS["error_msg"]["miscfilesize"];
				$err = true;
				continue;
			}
			if($up_err == 3){
				$errors[$i] = $GLOBALS["error_msg"]["miscfilepart"];
				$err = true;
				continue;
			}
			if(!@is_uploaded_file($tmp)){
				$errors[$i] = $GLOBALS["error_msg"]["uploadfile"];
				$err = true;
				continue;
			}
			if(@file_exists($abs) && empty($_REQUEST['overwrite_files'])){
				$errors[$i] = $GLOBALS["error_msg"]["itemdoesexist"];
				$err = true;
				continue;
			}
			$ok = @$GLOBALS['jx_File']->move_uploaded_file($tmp, $abs);
			if($ok === false || PEAR::isError($ok)){
				$errors[$i] = $GLOBALS["error_msg"]["uploadfile"];
				if(PEAR::isError($ok))
					$errors[$i] .= ' [' . $ok->getMessage() . ']';
				$err = true;
				continue;
			} else{
				$mode = jx_isFTPMode() ? 644 : 0644;
				@$GLOBALS['jx_File']->chmod($abs, $mode);
			}
		}
		if($err){
			$err_msg = "";
			for($i = 0; $i < $cnt; $i++){
				if($errors[$i] == null)
					continue;
				$err_msg .= $items[$i] . " : " . $errors[$i] . "<BR>\n";
			}
			show_error($err_msg);
		}
		header("Location: " . make_link("list", $dir, null));
		return;
	}
	show_header($GLOBALS["messages"]["actupload"]);
	echo "<br /><form enctype=\"multipart/form-data\" action=\"" . make_link("upload",
		$dir, null) . "\" method=\"post\">
			<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"" . get_max_file_size() .
		"\" />
			<input type=\"hidden\" name=\"confirm\" value=\"true\" />
		<table style=\"width:60%;\" border=\"1\" class=\"adminform\">
			<tr><td class=\"quote\" colspan=\"2\">Maximum File Size = <strong>" . ((get_max_file_size
	() / 1024) / 1024) . " MB</strong><br />
				Maximum Upload Limit = <strong>" . ((get_max_upload_limit() / 1024) / 1024) .
		" MB</strong>
			</td></tr>
			";
	for($i = 0; $i < 10; $i++){
		$class = $i % 2 ? 'row0' : 'row1';
		echo "<tr class=\"$class\"><td colspan=\"2\">";
		echo "<input name=\"userfile[]\" type=\"file\" size=\"50\" class=\"inputbox\" /></td></tr>\n";
	}
	echo "<tr><td colspan=\"2\">
				<input type=\"checkbox\" checked=\"checked\" value=\"1\" name=\"overwrite_files\" id=\"overwrite_files\" /><label for=\"overwrite_files\">" .
		$GLOBALS["messages"]["overwrite_files"] . "</label>
			</td>
			</tr>
			<tr>
				<td width=\"40%\" style=\"text-align:right;\">
					<input type=\"submit\" value=\"" . $GLOBALS["messages"]["btnupload"] . "\" class=\"button\" />&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
				<td width=\"60%\" style=\"text-align:left;\">&nbsp;&nbsp;&nbsp;&nbsp;
					<input type=\"button\" value=\"" . $GLOBALS["messages"]["btncancel"] . "\" class=\"button\" onclick=\"javascript:location='" .
		make_link("list", $dir, null) . "';\" />
				</td>
			</tr>
		</table>
		</form><br />\n";
	return;
}


?>
