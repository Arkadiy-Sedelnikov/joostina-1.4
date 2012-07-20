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

$task = mosGetParam($_GET, 'task', 'rem_front');
$id = intval(mosGetParam($_GET, 'id', '0'));

// обрабатываем полученный параметр task
switch($task){
	case "publish":
		echo x_publish($id);
		return;
	case "rem_front":
		x_remfront($id);
		return;
	case "access":
		echo x_access($id);
		return;
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
	$row = new mosContent($database);
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

/* публикация объекта
* $id - идентификатор объекта
*/
function x_publish($id = null){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();
	// id содержимого для обработки не получен - выдаём ошибку
	if(!$id) return _UNKNOWN_ID;
	$directory = mosGetParam($_REQUEST, 'directory', 0);
	$state = new stdClass();
	$query = "SELECT published as state, date_publish as publish_up, date_unpublish as publish_down"
		. "\n FROM #__boss_" . $directory . "_contents "
		. "\n WHERE id = " . (int)$id . " LIMIT 1";
	$database->setQuery($query);
	$row = $database->loadobjectList();
	$row = $row['0']; // результат запроса с элементами выбранных значений

	$now = _CURRENT_SERVER_TIME;
	$nullDate = $database->getNullDate();
	$ret_img = ''; // сюда надо изображения ошибки выполнения аякс скрипта поместить
	if($now <= $row->publish_up && $row->state == 1){
		// снимаем с публикации, опубликовано, но еще не доступно  - возвращаем значок "Неопубликовано"
		$ret_img = 'publish_x.png';
		$state = '0'; // было опубликовано - снимаем с публикации
	} elseif($now <= $row->publish_up && $row->state == 0){
		// снимаем с публикации, не опубликовано, и еще не доступно  - возвращаем значок "Не активно"
		$ret_img = 'publish_y.png';
		$state = '1';
		/* не было опубликовано - публикуем*/
	} else
		if(($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state == 1){
			// доступно и опубликовано, снимаем с публикации и возвращаем значок "Не опубликовано"
			$ret_img = 'publish_x.png';
			$state = '0'; // было опубликовано - снимаем с публикации
		} else
			if(($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state == 0){
				// доступно и опубликовано, снимаем с публикации и возвращаем значок "Не опубликовано"
				$ret_img = 'publish_g.png';
				$state = '1';
				/* не было опубликовано - публикуем*/
			} else
				if($now > $row->publish_down && $row->state == 1){
					// опубликовано, но срок публикации истёк, снимаем с публикации и возвращаем значок "Не опубликовано"
					$ret_img = 'publish_x.png';
					$state = '0';
					/* не было опубликовано - публикуем*/
				} else
					if($now > $row->publish_down && $row->state == 0){
						// опубликовано, но срок публикации истёк, снимаем с публикации и возвращаем значок "Не опубликовано"
						$ret_img = 'publish_r.png';
						$state = '1';
						/* не было опубликовано - публикуем*/
					}

	$query = "UPDATE #__boss_" . $directory . "_contents"
		. "\n SET published = " . (int)$state
		. "\n WHERE id = " . $id;
	$database->setQuery($query);
	if(!$database->query()){
		return 'error-db';
	} else{
		return $ret_img;
	}
}

/* публикация объекта на главной(первой) странице
* $id - идентификатор содержимого
*/
function x_remfront($id){
	$database = database::getInstance();
	$directory = mosGetParam($_REQUEST, 'directory', 0);

	$query = "UPDATE #__boss_" . $directory . "_contents"
		. "\n SET frontpage = 0"
		. "\n WHERE id = " . (int)$id;
	$database->setQuery($query);
	if(!$database->query()){
		echo 'error-delete';
	} else{
		echo 1;
		mosCache::cleanCache('com_boss'); // почистим кэш контент
	}
}