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
function archive_items($dir){
	if(($GLOBALS["permissions"] & 01) != 01)
		show_error($GLOBALS["error_msg"]["accessfunc"]);
	if(!$GLOBALS["zip"] && !$GLOBALS["tgz"])
		show_error($GLOBALS["error_msg"]["miscnofunc"]);
	$allowed_types = array('zip', 'tgz', 'tbz', 'tar');
	$actionURL = str_replace("index2.php", "index3.php", make_link("arch", $dir, null));
	if(isset($GLOBALS['__POST']["name"])){
		$saveToDir = $GLOBALS['__POST']['saveToDir'];
		if(!file_exists(get_abs_dir($saveToDir))){
			echo jx_scriptTag('', '$(\'loadingindicator\').style.display=\'none\';');
			echo jx_alertBox('The Save-To Directory you have specified does not exist.');
			die('The Save-To Directory you have specified does not exist.');
		}
		if(!is_writable(get_abs_dir($saveToDir))){
			echo jx_scriptTag('', '$(\'loadingindicator\').style.display=\'none\';');
			echo jx_alertBox('Please specify a writable directory to save the archive to.');
			die('Please specify a writable directory to save the archive to.');
		}
		require_once (_QUIXPLORER_PATH . '/libraries/Archive.php');
		if(!in_array(strtolower($GLOBALS['__POST']["type"]), $allowed_types)){
			echo ('Unknown Archive Format: ' . htmlspecialchars($GLOBALS['__POST']["type"]));
			jx_exit();
		}
		while(@ob_end_clean())
			;
		header('Status: 200 OK');
		echo '<?xml version="1.0" ?>' . "\n";
		$files_per_step = 2500;
		$cnt = count($GLOBALS['__POST']["selitems"]);
		$abs_dir = get_abs_dir($dir);
		$name = basename(stripslashes($GLOBALS['__POST']["name"]));
		if($name == "")
			show_error($GLOBALS["error_msg"]["miscnoname"]);
		$download = mosGetParam($_REQUEST, 'download', "n");
		$startfrom = mosGetParam($_REQUEST, 'startfrom', 0);
		$archive_name = get_abs_item($saveToDir, $name);
		$fileinfo = pathinfo($archive_name);
		if(empty($fileinfo['extension'])){
			$archive_name .= "." . $GLOBALS['__POST']["type"];
			$fileinfo['extension'] = $GLOBALS['__POST']["type"];
		}
		foreach($allowed_types as $ext){
			if($GLOBALS['__POST']["type"] == $ext && @$fileinfo['extension'] != $ext){
				$archive_name .= "." . $ext;
			}
		}
		for($i = 0; $i < $cnt; $i++){
			$selitem = stripslashes($GLOBALS['__POST']["selitems"][$i]);
			if(is_dir($abs_dir . "/" . $selitem)){
				$items = mosReadDirectory($abs_dir . "/" . $selitem, '.', true, true);
				foreach($items as $item){
					if(is_dir($item) || !is_readable($item) || $item == $archive_name)
						continue;
					$v_list[] = $item;
				}
			} else{
				$v_list[] = $abs_dir . "/" . $selitem;
			}
		}
		$cnt_filelist = count($v_list);
		$remove_path = $GLOBALS["home_dir"];
		if($dir){
			$remove_path .= $dir . $GLOBALS['separator'];
		}
		for($i = $startfrom; $i < $cnt_filelist && $i < ($startfrom + $files_per_step);
			$i++){
			$filelist[] = File_Archive::read($v_list[$i], str_replace($remove_path, '', $v_list[$i]));
		}
		ini_set('memory_limit', '128M');
		@set_time_limit(0);
		error_reporting(E_ERROR | E_PARSE);
		$result = File_Archive::extract($filelist, $archive_name);
		if(PEAR::isError($result)){
			echo $name . ": Failed saving Archive File. Error: " . $result->getMessage();
			jx_exit();
		}
		if($cnt_filelist > $startfrom + $files_per_step){
			echo "\n <script type=\"text/javascript\">document.archform.startfrom.value = '" . ($startfrom +
				$files_per_step) . "';</script>\n";
			echo '<script type="text/javascript"> doArchiving( \'' . $actionURL . '\' );</script>';
			printf($GLOBALS['messages']['processed_x_files'], $startfrom + $files_per_step,
				$cnt_filelist);
		} else{
			if($GLOBALS['__POST']["type"] == 'tgz' || $GLOBALS['__POST']["type"] == 'tbz'){
				chmod($archive_name, 0644);
			}
			if($download == "y"){
				echo '<script type="text/javascript">document.location=\'' . make_link('download',
					dirname($archive_name), basename($archive_name)) . '\';</script>';
			} else{
				echo '<script type="text/javascript">document.location=\'' . str_replace("index3.php",
					"index2.php", make_link("list", $dir, null)) .
					'&mosmsg=The%20Archive%20File%20has%20been%20created\';</script>';
			}
		}
		jx_exit();
	}
	?>
<script type="text/javascript">
	function showLoadingIndicator(el, replaceContent) {
		if (!el) return;
		var loadingimg = 'images/aload.gif';
		var imgtag = '<img src="' + loadingimg + '" alt="Загрузка..." border="0" name="Loading" align="absmiddle" />';

		if (replaceContent) {
			el.innerHTML = imgtag;
		}
		else {
			el.innerHTML += imgtag;
		}
	}
	function doArchiving(url) {
		showLoadingIndicator($('loadingindicator'), true);
		$('loadingindicator').style.display = '';

		var controller = new Ajax(url, {    postBody:$('adminform'),
				evalScripts:true,
				update:'statustext'
			}
		);
		controller.request();
		return false;
	}</script>
<?php
	show_header($GLOBALS["messages"]["actarchive"]);
	?><br/>

<form name="archform" method="post" action="<?php echo $actionURL ?>" onsubmit="return doArchiving(this.action);" id="adminform">

	<input type="hidden" name="no_html" value="1"/>
	<input type="hidden" name="startfrom" value="0"/>
	<?php
	$cnt = count($GLOBALS['__POST']["selitems"]);
	for($i = 0; $i < $cnt; ++$i){
		echo '<input type="hidden" name="selitems[]" value="' . stripslashes($GLOBALS['__POST']["selitems"][$i]) .
			'">';
	}

	?>
	<table class="adminform" style="width:600px;">
		<tr>
			<td colspan="2" style="text-align:center;display:none;" id="loadingindicator"><strong><?php echo
			$GLOBALS['messages']['creating_archive'] ?></strong></td>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold;text-align:center" id="statustext">&nbsp;</td>
		</tr>
		<tr>
			<td><?php echo $GLOBALS['messages']['archive_name'] ?>:</td>
			<td align="left">
				<input type="text" name="name" size="25" value="<?php echo ($dir != '' ?
					basename($dir) : $GLOBALS['__POST']["selitems"][0]) ?>"/>
			</td>
		</tr>
		<tr>
			<td><?php echo $GLOBALS["messages"]["typeheader"] ?>:</td>
			<td align="left">
				<select name="type">
					<?php
					if(extension_loaded("zlib")){
						echo '<option value="zip">Zip (' . $GLOBALS["messages"]['normal_compression'] .
							')</option>' . "\n";
						echo '<option value="tgz">Tar/Gz (' . $GLOBALS["messages"]['good_compression'] .
							')</option>' . "\n";
					}
					if(extension_loaded("bz2")){
						echo '<option value="tbz">Tar/Bzip2 (' . $GLOBALS["messages"]['best_compression'] .
							')</option>' . "\n";
					}
					echo '<option value="" disabled="disabled"> - - - - - - -</option>' . "\n";
					echo '<option value="tar">Tar (' . $GLOBALS["messages"]['no_compression'] .
						')</option>' . "\n";
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo $GLOBALS['messages']['archive_saveToDir'] ?>:</td>
			<td align="left">
				<input type="text" name="saveToDir" size="50" value="<?php echo $dir ?>"/>
			</td>
		</tr>
		<tr>
			<td><?php echo $GLOBALS["messages"]["downlink"] ?>?:</td>
			<td align="left">
				<input type="checkbox" checked="checked" name="download" value="y"/>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input type="submit" value="<?php echo $GLOBALS["messages"]["btncreate"] ?>">
				<input type="button" value="<?php echo $GLOBALS["messages"]["btncancel"] ?>" onclick="javascript:location='<?php echo
				make_link("list", $dir, null) ?>';">
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</table>
</form>
<br/>
<script type="text/javascript">if (document.archform) document.archform.name.focus();</script>
<?php
}

function extract_item($dir, $item){
	if(!jx_isArchive($item)){
		show_error($GLOBALS["error_msg"]["extract_noarchive"]);
	} else{
		$archive_name = realpath(get_abs_item($dir, $item));
		$file_info = pathinfo($archive_name);
		if(empty($dir)){
			$extract_dir = realpath($GLOBALS['home_dir']);
		} else{
			$extract_dir = realpath($GLOBALS['home_dir'] . "/" . $dir);
		}
		$ext = $file_info["extension"];
		require_once (_QUIXPLORER_PATH . "/libraries/Archive.php");
		$archive_name .= '/';
		$result = File_Archive::extract($archive_name, $extract_dir);
		if(PEAR::isError($result)){
			show_error($GLOBALS["error_msg"]["extract_failure"]);
		}
	}
	mosRedirect(make_link("list", $dir, null), $GLOBALS['messages']['extract_success']);
}