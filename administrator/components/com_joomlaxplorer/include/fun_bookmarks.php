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
if(!function_exists('file_put_contents')) {
	function file_put_contents($filename, $filecont) {
		$handle = fopen($filename, 'w');
		if(is_array($filecont)) {
			$size = 0;
			foreach($filecont as $filestring) {
				fwrite($handle, $filestring);
				$size += strlen($filestring);
			}
			fclose($handle);
			return $size;
		} else {
			fwrite($handle, $filecont);
			fclose($handle);
			return strlen($filecont);
		}
	}
}
function read_bookmarks() {
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
	$bookmarkfile = _QUIXPLORER_PATH.'/config/bookmarks_'.$GLOBALS['file_mode'].'_'.
		$my->id.'.php';
	if(file_exists($bookmarkfile)) {
		return parse_ini_file($bookmarkfile);
	} else {
		if(!is_writable(dirname($bookmarkfile))) {
			return array();
		} else {
			file_put_contents($bookmarkfile,
				";<?php if( !defined( '_JEXEC' ) && !defined( '_VALID_MOS' ) ) die( 'Restricted access' ); ?>\n{$GLOBALS['messages']['homelink']}=\n");
			return array($GLOBALS['messages']['homelink'] => '');
		}
	}
}
function modify_bookmark($task, $dir) {
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
	$alias = substr(mosGetParam($_REQUEST, 'alias'), 0, 150);
	$bookmarks = read_bookmarks();
	$bookmarkfile = _QUIXPLORER_PATH.'/config/bookmarks_'.$GLOBALS['file_mode'].'_'.
		$my->id.'.php';
	header("Status: 200 OK");
	switch($task) {
		case 'add':
			if(in_array($dir, $bookmarks)) {
				echo jx_alertBox($GLOBALS['messages']['already_bookmarked']);
				exit;
			}
			$alias = preg_replace('~[^\w-.\/\\\]~', '', $alias);
			$bookmarks[$alias] = $dir;
			$msg = jx_alertBox($GLOBALS['messages']['bookmark_was_added']);
			break;
		case 'remove':
			if(!in_array($dir, $bookmarks)) {
				echo jx_alertBox($GLOBALS['messages']['not_a_bookmark']);
				exit;
			}
			$bookmarks = array_flip($bookmarks);
			unset($bookmarks[$dir]);
			$bookmarks = array_flip($bookmarks);
			$msg = jx_alertBox($GLOBALS['messages']['bookmark_was_removed']);
	}
	$inifile = "; <?php if( !defined( '_JEXEC' ) && !defined( '_VALID_MOS' ) ) die( 'Restricted access' ); ?>\n";
	$inifile .= $GLOBALS['messages']['homelink']."=\n";
	foreach($bookmarks as $alias => $directory) {
		if(empty($directory) || empty($alias))
			continue;
		$inifile .= "$alias=$directory\n";
	}
	if(!is_writable($bookmarkfile)) {
		echo jx_alertBox(sprintf($GLOBALS['messages']['bookmarkfile_not_writable'], $task,
			$bookmarkfile));
		exit;
	}
	file_put_contents($bookmarkfile, $inifile);
	echo $msg;
	echo list_bookmarks($dir);
	jx_exit();
}
function list_bookmarks($dir) {
	$bookmarks = read_bookmarks();
	$bookmarks = array_flip($bookmarks);
	foreach($bookmarks as $bookmark) {
		$len = strlen($bookmark);
		if($len > 40) {
			$first_part = substr($bookmark, 0, 20);
			$last_part = substr($bookmark, -20);
			$bookmarks[$bookmark] = $first_part.'...'.$last_part;
		}
	}
	$html = $GLOBALS['messages']['quick_jump'].': ';
	$html .= jx_selectList('dirselector', $dir, $bookmarks, 1, '',
		'onchange="document.location=\''.make_link('list', null).'&dir=\' + this.options[this.options.selectedIndex].value;" style="max-width: 100px;"');
	$img_add = '<img src="'._QUIXPLORER_URL.
		'/images/bookmark_add.gif" border="0" alt="'.$GLOBALS['messages']['lbl_add_bookmark'].
		'" align="absmiddle" />';
	$img_remove = '<img src="'._QUIXPLORER_URL.
		'/images/publish_x.png" border="0" alt="'.$GLOBALS['messages']['lbl_remove_bookmark'].
		'" align="absmiddle" />';
	$addlink = $removelink = '';
	if(!isset($bookmarks[$dir]) && $dir != '' && $dir != '/') {
		$addlink = '<a href="'.make_link('modify_bookmark', $dir).
			'&task=add" onclick="var alias = prompt(\''.$GLOBALS['messages']['enter_alias_name'].
			':\', \''.$dir.'\');if( alias==\'\' || alias == null )return false; adder = new Ajax(\''.
			make_link('modify_bookmark', $dir).'&task=add&alias=\' + alias, { method: \'get\', postBody: \'action=modify_bookmark&task=add&dir='.
			$dir.'&alias=\' + alias + \'&option=com_joomlaxplorer\', evalScripts:true, update: \'quick_jumpto\' } );adder.request(); return false;" title="'.
			$GLOBALS['messages']['lbl_add_bookmark'].'" >'.$img_add.'</a>';
	} elseif($dir != '' && $dir != '/') {
		$removelink = '<a href="'.make_link('modify_bookmark', $dir).
			'&task=remove" onclick="remover = new Ajax(\''.make_link('modify_bookmark', $dir).
			'&task=remove\', { method: \'get\', update: \'quick_jumpto\', postBody: \'action=modify_bookmark&task=remove&dir='.
			$dir.'&option=com_joomlaxplorer\', evalScripts:true } );remover.request(); return false;"  title="'.
			$GLOBALS['messages']['lbl_remove_bookmark'].'">'.$img_remove.'</a>';
	}
	$html .= $addlink.'&nbsp;'.$removelink;
	return $html;
}

?>
