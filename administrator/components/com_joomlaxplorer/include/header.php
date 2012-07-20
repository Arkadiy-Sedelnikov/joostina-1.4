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
function show_header($allow, $dir = false){
	$url = str_replace('&dir=', '&ignore=', $_SERVER['REQUEST_URI']);
	echo "<div align=\"left\">";
	echo "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" ><tbody>";
	$mode = mosGetParam($_SESSION, 'file_mode', 'file');
	$logoutlink = $mode == 'ftp' ? ' <a href="index2.php?option=com_joomlaxplorer&amp;action=ftp_logout" title="' . $GLOBALS['messages']['logoutlink'] . '">[' . $GLOBALS['messages']['logoutlink'] . ']</a>' : '';
	$alternate_mode = $mode == 'file' ? 'ftp' : 'file';
	echo '<tr class="quote"><td>' . sprintf($GLOBALS['messages']['switch_file_mode'], $mode . $logoutlink, "<a href=\"" . $url . "&amp;file_mode=$alternate_mode\">$alternate_mode</a>") . '</td>';

	if($allow && @$GLOBALS['jx_File']->is_writable(get_abs_dir($dir))){
		echo "<td align=\"right\">
				<form action=\"" . make_link("mkitem", $dir, NULL) . "\" method=\"post\" name=\"mkitemform\">\n
					<select class=\"text_area\" name=\"mktype\" onchange=\"checkMkitemForm(this.options[this.selectedIndex])\">
						<option value=\"file\">" . $GLOBALS["mimes"]["file"] . "</option>
						<option value=\"dir\">" . $GLOBALS["mimes"]["dir"] . "</option>";
		if(!jx_isFTPMode() && !$GLOBALS['isWindows']){
			echo "	<option value=\"symlink\">" . $GLOBALS["mimes"]["symlink"] . "</option>\n";
		}
		echo "</select>
					<input name=\"symlink_target\" type=\"hidden\" size=\"25\" title=\"{$GLOBALS['messages']['symlink_target']}\" value=\"{JPATH_BASE}\" />
					<input class=\"text_area\" name=\"mkname\" type=\"text\" size=\"15\" title=\"{$GLOBALS['messages']['nameheader']}\" />
					<input class=\"text_area\" type=\"submit\" value=\"" . $GLOBALS["messages"]["btncreate"] . "\" />
				</form>
			</td>\n</tr>";
	}
	echo '</tbody></table>';
	echo "<script type=\"text/javascript\">function checkMkitemForm( el ) { if( el.value =='symlink' ) document.mkitemform.symlink_target.type='text'; else document.mkitemform.symlink_target.type='hidden';} </script>";
}

?>
