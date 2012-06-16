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
$act = mosGetParam($_GET, 'act', '');
$id = intval(mosGetParam($_GET, 'id', '0'));

switch($task){
	case 'publish':
		switch($act){
			case 'cat_publish':
				echo x_cat_publish($id);
				return;
			case 'client_publish':
				echo x_client_publish($id);
				return;
		}
	default:
		echo 'error-task';
		return;
}

function x_cat_publish($id = null){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if(!$id) return 'error-id';

	$query = "SELECT published FROM #__banners_categories WHERE id = " . (int)$id;
	$database->setQuery($query);
	$state = $database->loadResult();

	if($state == '1'){
		$ret_img = 'publish_x.png';
		$state = '0';
	} else{
		$ret_img = 'publish_g.png';
		$state = '1';
	}
	$query = "UPDATE #__banners_categories"
		. "\n SET published = " . (int)$state
		. "\n WHERE id = " . $id . " "
		. "\n AND ( checked_out = 0 OR ( checked_out = " . (int)$my->id . " ) )";
	$database->setQuery($query);
	if(!$database->query()){
		return 'error-db';
	} else{
		mosCache::cleanCache('com_banners');
		return $ret_img;
	}
}

function x_client_publish($id = null){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if(!$id) return 'error-id';


	$query = "SELECT published FROM #__banners_clients WHERE cid = " . (int)$id;
	$database->setQuery($query);
	$state = $database->loadResult();

	if($state == '1'){
		$ret_img = 'publish_x.png';
		$state = '0';
	} else{
		$ret_img = 'publish_g.png';
		$state = '1';
	}
	$query = "UPDATE #__banners_clients"
		. "\n SET published = " . (int)$state
		. "\n WHERE cid = " . $id;
	$database->setQuery($query);
	if(!$database->query()){
		return 'error-db';
	} else{
		mosCache::cleanCache('com_banners');
		return $ret_img;
	}
}