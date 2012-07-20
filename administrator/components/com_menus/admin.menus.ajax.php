<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();
$mainframe = mosMainFrame::getInstance();
$my = $mainframe->getUser();


$task = mosGetParam($_GET, 'task', 'publish');
$id = intval(mosGetParam($_GET, 'id', '0'));

switch($task){
	case "publish":
		echo x_publish($id);
		return;

	case "access":
		echo x_access($id);
		return;

	case "get_category_content":
		echo getCategoryContent($directory);
		break;

	default:
		echo 'error-task';
		return;
}


function x_access($id){
	$database = database::getInstance();
	$access = mosGetParam($_GET, 'chaccess', 'accessregistered');
	$option = strval(mosGetParam($_REQUEST, 'option', ''));
	switch($access){
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
	$row = new mosMenu($database);
	$row->load((int)$id);
	$row->access = $access;

	if(!$row->check()) return 'error-check';
	if(!$row->store()) return 'error-store';

	if(!$row->access){
		$color_access = 'style="color: green;"';
		$task_access = 'accessregistered';
		$text_href = _USER_GROUP_ALL;
	} elseif($row->access == 1){
		$color_access = 'style="color: red;"';
		$task_access = 'accessspecial';
		$text_href = _USER_GROUP_REGISTERED;
	} else{
		$color_access = 'style="color: black;"';
		$task_access = 'accesspublic';
		$text_href = _USER_GROUP_SPECIAL;
	}
	// чистим кэш
	mosCache::cleanCache('com_boss');
	return '<a href="#" onclick="ch_access(' . $row->id . ',\'' . $task_access . '\',\'' . $option . '\')" ' . $color_access . '>' . $text_href . '</a>';
}

function x_publish($id = null){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if(!$id) return 'error-id';

	$query = "SELECT published FROM #__menu WHERE id = " . (int)$id;
	$database->setQuery($query);
	$state = $database->loadResult();

	if($state == '1'){
		$ret_img = 'publish_x.png';
		$state = '0';
	} else{
		$ret_img = 'publish_g.png';
		$state = '1';
	}
	$query = "UPDATE #__menu SET published = " . (int)$state
		. "\n WHERE id = " . $id . " AND ( checked_out = 0 OR ( checked_out = " . (int)$my->id . " ) )";
	$database->setQuery($query);
	if(!$database->query()){
		return 'error-db';
	} else{
		mosCache::cleanCache('com_boss');
		return $ret_img;
	}
}

function getCategoryContent(){
	$database = database::getInstance();
	$catid = mosGetParam($_REQUEST, 'catid', 0);
	$directory = mosGetParam($_REQUEST, 'directory', 0);

	if($catid == 0 || $directory == 0)
		return;

	$q = "SELECT content.id, content.name ";
	$q .= "FROM #__boss_" . $directory . "_contents as content, ";
	$q .= "#__boss_" . $directory . "_content_category_href as cch ";
	$q .= "WHERE cch.category_id = $catid ";
	$q .= "AND cch.content_id = content.id ";
	$q .= "ORDER BY content.name";

	$contents = $database->setQuery($q)->loadObjectList();
	$options = '';
	foreach($contents as $content){
		$options .= '<option value="' . $content->id . '">' . $content->name . '</option> ';
	}
	return $options;
}

?>