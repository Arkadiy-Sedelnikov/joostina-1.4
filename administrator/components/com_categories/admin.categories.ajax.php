<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

$mainframe = mosMainFrame::getInstance();
$my = $mainframe->getUser();

$task = mosGetParam($_GET,'task','publish');
$id = intval(mosGetParam($_GET,'id','0'));

switch($task) {
	case 'publish':
		echo x_publish($id);
		return;
	case 'access':
		echo x_access($id);
		return;
	case 'apply':
		js_menu_cache_clear();
		echo x_apply();
		return;
	default:
		echo 'error-task';
		return;
}


/**
 * Saves the catefory after an edit form submit
 * @param string The name of the category section
 */
function x_apply() {
	$database = database::getInstance();
	josSpoofCheck();

	$redirect = strval(mosGetParam($_POST,'redirect',''));
	$oldtitle = stripslashes(strval(mosGetParam($_POST,'oldtitle',null)));

	$row = new mosCategory($database);

	foreach($_POST as $key => $val) {
		$_POST[$key] = joostina_api::convert($val);
	}

	if(!$row->bind($_POST,'folders')) return 'error-bind';
	$row->title = addslashes($row->title);
	$row->name = addslashes($row->name);

	// handling for MOSImage directories
	if($row->section > 0) {
		$folders = mosGetParam($_POST,'folders',array());
		$folders = implode(',',$folders);

		if(strpos($folders,'*2*') !== false) {
			$folders = '*2*';
		} else
		if(strpos($folders,'*1*') !== false) {
			$folders = '*1*';
		} else
		if(strpos($folders,'*0*') !== false) {
			$folders = '*0*';
		} else
		if(strpos($folders,',*#*') !== false) {
			$folders = str_replace(',*#*','',$folders);
		} else
		if(strpos($folders,'*#*,') !== false) {
			$folders = str_replace('*#*,','',$folders);
		} else
		if(strpos($folders,'*#*') !== false) {
			$folders = str_replace('*#*','',$folders);
		}

		$row->params = 'imagefolders='.$folders;
	}

	if(!$row->check()) return 'error-check';
	if(!$row->store()) return 'error-store';
	if(!$row->checkin()) return 'error-checkin';

	$row->updateOrder("section = ".$database->Quote($row->section));

	if($oldtitle) {
		if($oldtitle != $row->title) {
			$query = "UPDATE #__menu SET name = ".$database->Quote($row->title)." WHERE name = ".
					$database->Quote($oldtitle)."\n AND type = 'content_category'";
			$database->setQuery($query);
			$database->query();
		}
	}

	if(!$database->query()) return 'error-update';

	if($redirect == 'content') {
		// clean any existing cache files
		mosCache::cleanCache('com_content');
	}
	return _CATEGORY_CHANGES_SAVED;
}


function x_access($id) {
	$database = database::getInstance();
	$access = mosGetParam($_GET,'chaccess','accessregistered');
	$option = strval(mosGetParam($_REQUEST,'option',''));
	switch($access) {
		case 'accesspublic':
			$access = 0;
			break;
		case 'accessregistered':
			$access = 1;
			break;
		case 'accessspecial':
			$access = 2;
			break;
		default:
			$access = 0;
			break;
	}
	$row = new mosCategory($database);
	$row->load((int)$id);
	$row->access = $access;

	if(!$row->check()) return 'error-check';
	if(!$row->store()) return 'error-store';

	if(!$row->access) {
		$color_access	= 'style="color: green;"';
		$task_access	= 'accessregistered';
		$text_href		= _USER_GROUP_ALL;
	} elseif($row->access == 1) {
		$color_access	= 'style="color: red;"';
		$task_access	= 'accessspecial';
		$text_href		= _USER_GROUP_REGISTERED;
	} else {
		$color_access	= 'style="color: black;"';
		$task_access	= 'accesspublic';
		$text_href		= _USER_GROUP_SPECIAL;
	}
	// чистим кэш
	mosCache::cleanCache('com_content');
	return '<a href="#" onclick="ch_access('.$row->id.',\''.$task_access.'\',\''.$option.'\')" '.$color_access.'>'.$text_href.'</a>';
}

function x_publish($id = null) {
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
    $database = database::getInstance();

	if(!$id) return 'error-id';


	$query = "SELECT published"
			."\n FROM #__categories"
			."\n WHERE id = ".(int)$id;
	$database->setQuery($query);
	$state = $database->loadResult();

	if($state == '1') {
		$ret_img = 'publish_x.png';
		$state = '0';
	} else {
		$ret_img = 'publish_g.png';
		$state = '1';
	}
	$query = "UPDATE #__categories"
			."\n SET published = ".(int)$state
			."\n WHERE id = ".$id." "
			."\n AND ( checked_out = 0 OR ( checked_out = ".(int)$my->id." ) )";
	$database->setQuery($query);
	if(!$database->query()) {
		return 'error-db';
	} else {
		mosCache::cleanCache('com_content');
		return $ret_img;
	}
}