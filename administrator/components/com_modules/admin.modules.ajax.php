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

$acl = gacl::getInstance();

if(!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'modules', 'all') | $acl->acl_check('administration', 'install', 'users', $my->usertype, 'modules', 'all'))){
	die('error-acl');
}

$task = mosGetParam($_GET, 'task', 'publish');
$id = intval(mosGetParam($_GET, 'id', 0));


switch($task){
	case 'publish':
		echo x_publish($id);
		return;
	case 'position':
		echo x_get_position($id);
		return;
	case 'save_position':
		echo x_save_position($id);
		return;
	case 'access':
		echo x_access($id);
		return;
	case 'apply':
		echo x_apply();
		return;
	default:
		echo 'error-task';
		return;
}

function x_apply(){
	josSpoofCheck();

	$database = database::getInstance();

	$params = mosGetParam($_POST, 'params', array());
	$client = strval(mosGetParam($_REQUEST, 'client', ''));

	foreach($params as $key => $val){
		$params[$key] = joostina_api::convert($val);
	}

	if(is_array($params)){
		$txt = array();
		foreach($params as $k => $v){
			$txt[] = "$k=$v";
		}
		$_POST['params'] = mosParameters::textareaHandling($txt);
	}

	$row = new mosModule($database);

	$_POST['title'] = joostina_api::convert($_POST['title']);
	if(isset($_POST['content'])){
		$_POST['content'] = joostina_api::convert(strval($_POST['content']));
	}

	if(!$row->bind($_POST, 'selections')) return 'error-bind';
	if(!$row->check()) return 'error-check';
	if(!$row->store()) return 'error-store';
	if(!$row->checkin()) return 'error-checkin';

	if($client == 'admin'){
		$where = "client_id=1";
	} else{
		$where = "client_id=0";
	}
	$row->updateOrder('position=' . $database->Quote($row->position) . " AND ($where)");

	// delete old module to menu item associations
	$sql = "DELETE FROM #__modules_com WHERE moduleid = " . (int)$row->id;
	$database->setQuery($sql);
	$database->query();

	$menus = isset($_POST['selections']) ? $_POST['selections'] : array();
	if(!count($menus) or in_array('0-0-0-', $menus)){
		$sql = "INSERT INTO `#__modules_com`  (`id`, `moduleid`, `option`, `directory`, `category`, `task`)
		 		VALUES (NULL, '" . $row->id . "', '0',  '0',  '0',  '');";
	} elseif(in_array('-0-0-', $menus)){
		$sql = "INSERT INTO `#__modules_com` (`id`, `moduleid`, `option`, `directory`, `category`, `task`)
		 		VALUES (NULL, '" . $row->id . "', '',  '0',  '0',  '');";
	} else{
		$sql = '';
		foreach($menus as $menu){
			if($menu != '-999'){
				$tmp = preg_match("#^([a-z0-9_-]*)-(\d+)-(\d+)-([a-z0-9_-]*)$#i", $menu, $arr_sel);
				if($tmp){
					$sel['option'] = isset($arr_sel[1]) ? $arr_sel[1] : '';
					$sel['directory'] = isset($arr_sel[2]) ? $arr_sel[2] : 0;
					$sel['category'] = isset($arr_sel[3]) ? $arr_sel[3] : 0;
					$sel['task'] = isset($arr_sel[4]) ? $arr_sel[4] : '';
					$sql .= "INSERT INTO #__modules_com (`id`, `moduleid`, `option`, `directory`, `category`, `task`)
		 					VALUES (
		 						NULL,
		 						'" . $row->id . "',
		 						'" . $sel['option'] . "',
		 						'" . $sel['directory'] . "',
		 						'" . $sel['category'] . "',
		 						'" . $sel['task'] . "'
		 					);";
				}
			}
		}
	}
	$database->setQuery($sql);
	$database->multiQuery();

	mosCache::cleanCache('com_boss');

	$msg = $row->title . ' - ' . _ALL_MODULE_CHANGES_SAVED;
	return $msg;
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
	$row = new mosModule($database);
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
	return '<a href="#" onclick="ch_access(' . $row->id . ',\'' . $task_access . '\',\'' . $option . '\')" ' . $color_access . '>' . $text_href . '</a>';
}

function x_publish($id = null){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if(!$id) return 'error-id';

	$query = "SELECT published FROM #__modules WHERE id = " . (int)$id;
	$database->setQuery($query);
	$state = $database->loadResult();

	if($state == '1'){
		$ret_img = 'publish_x.png';
		$state = '0';
	} else{
		$ret_img = 'publish_g.png';
		$state = '1';
	}
	$query = "UPDATE #__modules"
		. "\n SET published = " . (int)$state
		. "\n WHERE id = " . $id
		. "\n AND ( checked_out = 0 OR ( checked_out = " . (int)$my->id . " ) )";
	$database->setQuery($query);
	if(!$database->query()){
		return 'error-db';
	} else{
		return $ret_img;
	}
}

// получение списка позиций модулей
function x_get_position($id){
	$database = database::getInstance();

	$row = new mosModule($database);
	$row->load((int)$id);
	$active = ($row->position ? $row->position : 'left');

	$query = "SELECT position, description FROM #__template_positions WHERE position != '' ORDER BY position";
	$database->setQuery($query);
	$positions = $database->loadObjectList();

	$orders2 = array();
	$pos = array();
	$pos[] = mosHTML::makeOption($active, '--' . _CLOSE . '--');
	foreach($positions as $position){
		if($position->description == '') $position->description = $position->position;
		if($row->position == $position->position) $position->description = '--' . $position->description . '--';
		$pos[] = mosHTML::makeOption($position->position, $position->description);
	}
	return mosHTML::selectList($pos, 'position', 'class="inputbox" size="1" onchange="ch_sav_pos(' . $id . ',this.value)"', 'value', 'text', $active);
}

function x_save_position($id){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	$new_pos = strval(mosGetParam($_GET, 'new_pos', 'left'));
	if($new_pos == '0') return 1;
	$query = "UPDATE #__modules SET position = '" . $new_pos . "' WHERE id = " . $id . " AND ( checked_out = 0 OR ( checked_out = " . (int)$my->id . " ) )";
	$database->setQuery($query);
	if($database->query()){
		return 1; // новая позиция сохранения
	} else{
		return 2; // во время сохранения новой позиции произошла ошибка
	}
}