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

global $my;

$task = mosGetParam($_GET, 'task', 'publish');
$id = intval(mosGetParam($_REQUEST, 'id', '0'));

// Editor usertype check
$access = new stdClass();
$access->canEdit = $acl->acl_check('action', 'edit', 'users', $my->usertype, 'content', 'all');
$access->canEditOwn = $acl->acl_check('action', 'edit', 'users', $my->usertype, 'content', 'own');
$access->canPublish = $acl->acl_check('action', 'publish', 'users', $my->usertype, 'content', 'all');


// обрабатываем полученный параметр task
switch ($task) {
	case 'publish':
		echo x_publish($id);
		return;

	case 'jsave':
		echo x_jsave($id);
		return;

	default:
		echo 'error-task';
		return;
}

function x_jsave($id) {
	global $my, $access;

	if (!($access->canEdit || $access->canEditOwn)) {
		mosNotAuth();
		return;
	}

	$database = database::getInstance();

	$introtext = trim(mosGetParam($_POST, 'introtext', '', _MOS_ALLOWRAW));
	$fulltext = trim(mosGetParam($_POST, 'fulltext', null, _MOS_ALLOWRAW));

	if ($fulltext)
		$fulltext = ', `fulltext` = \'' . $fulltext . '=\'';

	$query = "UPDATE #__content"
			. "\n SET `introtext` = '" . $introtext . "', `modified` = '" . $database->getEscaped(date('Y-m-d H:i:s')) . '\''
			. $fulltext
			. "\n WHERE id = " . $id . " AND ( checked_out = 0 OR (checked_out = " . (int) $my->id . ") )";
	$database->setQuery($query);
	if (!$database->query()) {
		return 'error!';
	} else {
		mosCache::cleanCache('com_content');
		return 'Saved: ' . date('Y-m-d H:i:s') . ', id=' . $id;
	}
}

/* публикация объекта
 * $id - идентификатор объекта
 */

function x_publish($id = null) {
	global $my, $access;
	// id содержимого для обработки не получен - выдаём ошибку
	if (!$id)
		return 'error-id';

	if (!($access->canEdit || ($access->canEditOwn))) {
		return 'error-access';
	}

	$database = database::getInstance();

	$state = new stdClass();
	$query = "SELECT state, publish_up, publish_down FROM #__content WHERE id = " . (int) $id;
	$row = $database->setQuery($query)->loadobjectList();

	$row = $row['0']; // результат запроса с элементами выбранных значений

	$now = _CURRENT_SERVER_TIME;
	$nullDate = $database->getNullDate();
	$ret_img = ''; // сюда надо изображения ошибки выполнения аякс скрипта поместить
	if ($now <= $row->publish_up && $row->state == 1) {
		// снимаем с публикации, опубликовано, но еще не доступно  - возвращаем значок "Неопубликовано"
		$ret_img = 'publish_x.png';
		$state = '0'; // было опубликовано - снимаем с публикации
	} elseif ($now <= $row->publish_up && $row->state == 0) {
		// снимаем с публикации, не опубликовано, и еще не доступно  - возвращаем значок "Не активно"
		$ret_img = 'publish_y.png';
		$state = '1';
	} elseif (($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state == 1) {
		// доступно и опубликовано, снимаем с публикации и возвращаем значок "Не опубликовано"
		$ret_img = 'publish_x.png';
		$state = '0'; // было опубликовано - снимаем с публикации
	} elseif (($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state == 0) {
		// доступно и опубликовано, снимаем с публикации и возвращаем значок "Не опубликовано"
		$ret_img = 'publish_g.png';
		$state = '1';
	} elseif ($now > $row->publish_down && $row->state == 1) {
		// опубликовано, но срок публикации истёк, снимаем с публикации и возвращаем значок "Не опубликовано"
		$ret_img = 'publish_x.png';
		$state = '0';
	} elseif ($now > $row->publish_down && $row->state == 0) {
		// опубликовано, но срок публикации истёк, снимаем с публикации и возвращаем значок "Не опубликовано"
		$ret_img = 'publish_r.png';
		$state = '1';
	}

	$query = "UPDATE #__content"
			. "\n SET state = " . (int) $state . ", modified = " . $database->Quote(date('Y-m-d H:i:s'))
			. "\n WHERE id = " . $id . " AND ( checked_out = 0 OR (checked_out = " . (int) $my->id . ") )";
	$database->setQuery($query);
	if (!$database->query()) {
		return 'error!';
	} else {
		mosCache::cleanCache('com_content');
		return $ret_img;
	}
}