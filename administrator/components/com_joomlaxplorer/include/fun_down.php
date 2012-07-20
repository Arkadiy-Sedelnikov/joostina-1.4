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
function download_item($dir, $item, $unlink = false){
	global $action, $mosConfig_cache_path;
	$item = basename($item);
	while(@ob_end_clean())
		;
	ob_start();
	if(jx_isFTPMode()){
		$abs_item = $dir . '/' . $item;
	} else{
		$abs_item = get_abs_item($dir, $item);
		if(!strstr($abs_item, realpath($GLOBALS['home_dir'])))
			$abs_item = realpath($GLOBALS['home_dir']) . $abs_item;
	}
	if(($GLOBALS["permissions"] & 01) != 01)
		show_error($GLOBALS["error_msg"]["accessfunc"]);
	if(!$GLOBALS['jx_File']->file_exists($abs_item))
		show_error($item . ": " . $GLOBALS["error_msg"]["fileexist"]);
	if(!get_show_item($dir, $item))
		show_error($item . ": " . $GLOBALS["error_msg"]["accessfile"]);
	if(jx_isFTPMode()){
		$abs_item = jx_ftp_make_local_copy($abs_item);
		$unlink = true;
	}
	$browser = id_browser();
	header('Content-Type: ' . (($browser == 'IE' || $browser == 'OPERA') ?
		'application/octetstream' : 'application/octet-stream'));
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . filesize(realpath($abs_item)));
	if($browser == 'IE'){
		header('Content-Disposition: attachment; filename="' . $item . '"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	} else{
		header('Content-Disposition: attachment; filename="' . $item . '"');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
	}
	@set_time_limit(0);
	@readFileChunked($abs_item);
	if($unlink == true){
		unlink($abs_item);
	}
	ob_end_flush();
	jx_exit();
}


?>
