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
$database = database::getInstance();

$acl = &gacl::getInstance();

if(!($acl->acl_check('administration', 'edit', 'users', $my->usertype, 'modules', 'all') | $acl->acl_check('administration', 'install', 'users', $my->usertype, 'modules', 'all'))){
	die('error-acl');
}

$task = mosGetParam($_GET, 'task', 'publish');
$id = intval(mosGetParam($_GET, 'id', '0'));

switch($task){
	case "publish":
		echo x_publish($id);
		return;
	case "access":
		echo x_access($id);
		return;
	case "apply":
		echo x_apply();
		return;
	default:
		echo 'error-task';
		return;
}


/**
 * Saves the module after an edit form submit
 */
function x_apply(){
	$database = database::getInstance();
	josSpoofCheck();
	$params = mosGetParam($_POST, 'params', '');
	$client = strval(mosGetParam($_REQUEST, 'client', ''));

	foreach($_POST as $key => $val){
		$_POST[$key] = joostina_api::convert($val);
	}

	if(is_array($params)){
		$txt = array();
		foreach($params as $k => $v){
			$txt[] = "$k=$v";
		}
		$_POST['params'] = mosParameters::textareaHandling($txt);
	}

	$row = new mosMambot($database);


	if(!$row->bind($_POST)) return 'error-bind';
	if(!$row->check()) return 'error-check';
	if(!$row->store()) return 'error-store';
	if(!$row->checkin()) return 'error-checkin';

	if($client == 'admin'){
		$where = "client_id='1'";
	} else{
		$where = "client_id='0'";
	}
	$row->updateOrder("folder = " . $database->Quote($row->folder) . " AND ordering > -10000 AND ordering < 10000 AND ( $where )");
	$msg = sprintf(_COM_MAMBOTS_APPLY, $row->name);
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
	$row = new mosMambot($database);
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

	$query = "SELECT published FROM #__mambots WHERE id = " . (int)$id;
	$database->setQuery($query);
	$state = $database->loadResult();

	if($state == '1'){
		$ret_img = 'publish_x.png';
		$state = '0';
	} else{
		$ret_img = 'publish_g.png';
		$state = '1';
	}
	$query = "UPDATE #__mambots SET published = " . (int)$state . " WHERE id = " . $id . " " . "\n AND ( checked_out = 0 OR ( checked_out = " . (int)$my->id . " ) )";
	$database->setQuery($query);
	if(!$database->query()){
		return 'error-db';
	} else{
		mosCache::cleanCache('com_boss');
		return $ret_img;
	}
}