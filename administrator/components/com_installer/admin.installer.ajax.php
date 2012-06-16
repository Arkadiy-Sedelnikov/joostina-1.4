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

$task = mosGetParam($_GET, 'task', 'publish');
$id = intval(mosGetParam($_REQUEST, 'id', 0));

// обрабатываем полученный параметр task
switch($task){
	case 'publish':
		echo x_publish($id);
		return;

	default:
		echo 'error-task';
		return;
}

function x_publish($id = null){
	$database = database::getInstance();

	if(!$id) return 'error-id';

	$query = "SELECT menuid FROM #__components WHERE id = " . (int)$id;
	$database->setQuery($query);
	$state = $database->loadResult();

	if($state == 0){
		$ret_img = 'publish_x.png';
		$state = 1;
	} else{
		$ret_img = 'publish_g.png';
		$state = 0;
	}
	$query = "UPDATE #__components SET menuid = " . (int)$state . " WHERE id = " . $id;
	$database->setQuery($query);
	if(!$database->query()){
		return 'error-db';
	} else{
		return $ret_img;
	}
}