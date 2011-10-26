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
$id = intval(mosGetParam($_REQUEST, 'id', 0));

// обрабатываем полученный параметр task
switch ($task) {
	case 'publish':
		echo x_publish($id);
		return;
	case 'frontpage':
		echo x_frontpage($id);
		return;
	case 'to_trash':
		echo x_to_trash($id);
		return;
	case 'apply':
		echo x_save($id);
		return;
	case 'access':
		echo x_access($id);
		return;
	case 'metakey':
		echo x_metakey();
		return;
	case 'resethits':
		echo x_resethits($id);
		return;

	default:
		echo 'error-task';
		return;
}

function x_resethits($id) {
	$database = database::getInstance();

	$row = new mosContent($database);
	$row->load((int) $id);
	$row->hits = 0;
	$row->store();
	$row->checkin();

	echo _COUNTER_RESET;
}

function x_metakey($count = 25, $minlench = 4) {
	$mainframe = mosMainFrame::getInstance(true);

	// подключаем файл стоп-слов
	include JPATH_BASE . DS . 'language' . DS . $mainframe->lang . DS . 'ignore.php';

	$introtext = mosGetParam($_POST, 'introtext', '', _MOS_ALLOWRAW);
	$fulltext = mosGetParam($_POST, 'fulltext', '', _MOS_ALLOWRAW);
	$notetext = mosGetParam($_POST, 'notetext', '', _MOS_ALLOWRAW);

	$text = $introtext . ' ' . $fulltext . ' ' . $notetext;
	$text = Jstring::trim(strip_tags($text)); // чистим от тегов
	$remove = array('mosimage', 'nbsp', "rdquo", "laquo", "raquo", "quota", "quot", "ndash", "mdash", "«", "»", "\t", '\n', '\r', "\n", "\r", '\\', "'", ",", ".", "/", "¬", "#", ";", ":", "@", "~", "[", "]", "{", "}", "=", "-", "+", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"');
	$text = str_replace($remove, ' ', $text); // чистим от спецсимволов
	$arr = explode(' ', $text); // делим текст на массив из слов
	$arr = str_replace($bad_text, '', $arr); // чистим от стоп-слов
	$ret = array();
	foreach ($arr as $sl) {
		if (Jstring::strlen($sl) > $minlench)
			$ret[] = Jstring::strtolower($sl); // собираем в массив тока слова не меньше указанной длины
	}
	$ret = array_count_values($ret); // собираем слова с количеством
	arsort($ret); // сортируем массив, чем чаще встречается слово - тем выше его ставим
	$ret = array_keys($ret);
	$ret = array_slice($ret, 0, $count); // берём первые значения массива
	$headers = implode(', ', $ret); // собираем итог
	return $headers;
}

function x_access($id) {
	$database = database::getInstance();

	$access = mosGetParam($_GET, 'chaccess', 'accessregistered');
	$option = strval(mosGetParam($_REQUEST, 'option', ''));
	switch ($access) {
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
	$row->load((int) $id);
	$row->access = $access;

	if (!$row->check())
		return 'error-check';
	if (!$row->store())
		return 'error-store';

	if (!$row->access) {
		$color_access = 'style="color: green;"';
		$task_access = 'accessregistered';
		$text_href = _USER_GROUP_ALL;
	} elseif ($row->access == 1) {
		$color_access = 'style="color: red;"';
		$task_access = 'accessspecial';
		$text_href = _USER_GROUP_REGISTERED;
	} else {
		$color_access = 'style="color: black;"';
		$task_access = 'accesspublic';
		$text_href = _USER_GROUP_SPECIAL;
	}
	// чистим кэш
	mosCache::cleanCache('com_content');
	return '<a href="#" onclick="return ch_access(' . $row->id . ',\'' . $task_access . '\',\'' . $option . '\')" ' . $color_access . '>' . $text_href . '</a>';
}

function x_to_trash($id) {
	$database = database::getInstance();

	$state = '-2';
	$ordering = '0';

	$query = "UPDATE #__content SET state = " . (int) $state . ", ordering = " . (int) $ordering . " WHERE id=" . $id;
	$database->setQuery($query);
	if (!$database->query()) {
		return 2; // ошибка перемещения в корзину
	} else {
		mosCache::cleanCache('com_content');
		return 1; // перемещение в корзину успешно
	}
	// чистим кэш
}

/**
 * Saves the content item an edit form submit
 * @param database A database connector object
 * boston, добавил параметр -  возврат в редактирование содержимого после сохранения для добавления нового
 */
function x_save() {
	global $my;

	$mainframe = mosMainFrame::getInstance(true);
	$database = $mainframe->getDBO();

	$menu = strval(mosGetParam($_POST, 'menu', 'mainmenu'));
	$menuid = intval(mosGetParam($_POST, 'menuid', 0));
	$nullDate = $database->getNullDate();
	$sectionid = intval(mosGetParam($_POST, 'sectionid', 0));

	foreach ($_POST as $key => $val) {
		$_POST[$key] = joostina_api::convert($val);
	}

	$row = new mosContent($database);
	if (!$row->bind($_POST)) {
		echo $row->getError();
		exit();
	}

	// sanitise id field
	$row->id = (int) $row->id;

	if ($row->id) {
		$row->modified = date('Y-m-d H:i:s');
		$row->modified_by = $my->id;
	}

	$row->created_by = $row->created_by ? $row->created_by : $my->id;

	if ($row->created && strlen(trim($row->created)) <= 10) {
		$row->created .= ' 00:00:00';
	}
	$row->created = $row->created ? mosFormatDate($row->created, '%Y-%m-%d %H:%M:%S', -$mainframe->config->config_offset) : date('Y-m-d H:i:s');

	if (strlen(trim($row->publish_up)) <= 10) {
		$row->publish_up .= ' 00:00:00';
	}
	$row->publish_up = mosFormatDate($row->publish_up, _CURRENT_SERVER_TIME_FORMAT, - $mainframe->config->config_offset);

	if (trim($row->publish_down) == 'Никогда' || trim($row->publish_down) == '') {
		$row->publish_down = $nullDate;
	} else {
		if (strlen(trim($row->publish_down)) <= 10) {
			$row->publish_down .= ' 00:00:00';
		}
		$row->publish_down = mosFormatDate($row->publish_down, _CURRENT_SERVER_TIME_FORMAT, -$mainframe->config->config_offset);
	}

	$row->state = intval(mosGetParam($_REQUEST, 'published', 0));

	$params = mosGetParam($_POST, 'params', '');
	if (is_array($params)) {
		$txt = array();
		foreach ($params as $k => $v) {
			if (get_magic_quotes_gpc()) {
				$v = stripslashes($v);
			}
			$txt[] = "$k=$v";
		}
		$row->attribs = implode("\n", $txt);
	}

	// code cleaner for xhtml transitional compliance
	$row->introtext = str_replace('<br>', '<br />', $row->introtext);
	$row->fulltext = str_replace('<br>', '<br />', $row->fulltext);

	// remove <br /> take being automatically added to empty fulltext
	$length = strlen($row->fulltext) < 9;
	$search = strstr($row->fulltext, '<br />');
	if ($length && $search) {
		$row->fulltext = null;
	}

	$row->title = ampReplace($row->title);

	if (!$row->check()) {
		echo $row->getError();
		exit();
	}

	$row->version++;

	if ($mainframe->getCfg('use_content_save_mambots')) {
		global $_MAMBOTS;
		$_MAMBOTS->loadBotGroup('content');
		$_MAMBOTS->trigger('onSaveContent', array($row));
	}

	if (!$row->store()) {
		echo $row->getError();
		exit();
	}

	if ($mainframe->getCfg('use_content_save_mambots')) {
		$_MAMBOTS->trigger('onAfterSaveContent', array($row));
	}

	//Подготовка тэгов
	$tags = explode(',', trim($_POST['tags']));
	if ($tags[0] != '') {
		/* вспомогательная библиотека работы с массивами */
		mosMainFrame::addLib('array');
		$tags = ArrayHelper::clear($tags);
		$tag = new contentTags($database);
		$tags = $tag->clear_tags($tags);
		$row->obj_type = 'com_content';
		$tag->update($tags, $row);
	}

	// manage frontpage items
	require_once ($mainframe->getPath('class', 'com_frontpage'));
	$fp = new mosFrontPage($database);

	if (intval(mosGetParam($_REQUEST, 'frontpage', 0))) {
		// toggles go to first place
		if (!$fp->load((int) $row->id)) {
			// new entry
			$query = "INSERT INTO #__content_frontpage VALUES ( " . (int) $row->id . ", 1 )";
			$database->setQuery($query);
			if (!$database->query()) {
				echo $database->stderr();
				exit();
			}
			$fp->ordering = 1;
		}
	} else {
		// no frontpage mask
		if (!$fp->delete((int) $row->id)) {
			$msg .= $fp->stderr();
		}
		$fp->ordering = 0;
	}
	$fp->updateOrder();

	$row->checkin();
	$row->updateOrder("catid = " . (int) $row->catid . " AND state >= 0");

	mosCache::cleanCache('com_content');

	echo _C_CONTENT_AJAX_SAVE . ': ' . gmdate('H:i:s ( d.m.y )');
}

/* публикация объекта
 * $id - идентификатор объекта
 */

function x_publish($id = null) {
	global $my;

	$database = database::getInstance();

	// id содержимого для обработки не получен - выдаём ошибку
	if (!$id)
		return 'error-id';

	$state = 0;
	$ret_img = 'publish_x.png';

	$query = "SELECT state, publish_up, publish_down FROM #__content WHERE id = " . (int) $id;
	$database->setQuery($query);
	$row = $database->loadobjectList();
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
		/* не было опубликовано - публикуем */
	} else
	if (($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state == 1) {
		// доступно и опубликовано, снимаем с публикации и возвращаем значок "Не опубликовано"
		$ret_img = 'publish_x.png';
		$state = '0'; // было опубликовано - снимаем с публикации
	} else
	if (($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state == 0) {
		// доступно и опубликовано, снимаем с публикации и возвращаем значок "Не опубликовано"
		$ret_img = 'publish_g.png';
		$state = '1';
		/* не было опубликовано - публикуем */
	} else
	if ($now > $row->publish_down && $row->state == 1) {
		// опубликовано, но срок публикации истёк, снимаем с публикации и возвращаем значок "Не опубликовано"
		$ret_img = 'publish_x.png';
		$state = '0';
		/* не было опубликовано - публикуем */
	} else
	if ($now > $row->publish_down && $row->state == 0) {
		// опубликовано, но срок публикации истёк, снимаем с публикации и возвращаем значок "Не опубликовано"
		$ret_img = 'publish_r.png';
		$state = '1';
		/* не было опубликовано - публикуем */
	}

	$query = "UPDATE #__content"
			. "\n SET state = " . (int) $state . ", modified = " . $database->Quote(date('Y-m-d H:i:s'))
			. "\n WHERE id = " . $id . " AND ( checked_out = 0 OR (checked_out = " . (int) $my->id . ") )";
	$database->setQuery($query);
	if (!$database->query()) {
		return 'error-db';
	} else {
		mosCache::cleanCache('com_content');
		return $ret_img;
	}
}

/* публикация объекта на главной(первой) странице
 * $id - идентификатор содержимого
 */

function x_frontpage($id) {
	$mainframe = mosMainFrame::getInstance(true);
	$database = $mainframe->getDBO();

	require_once ($mainframe->getPath('class', 'com_frontpage'));

	$fp = new mosFrontPage($database);
	if ($fp->load($id)) {
		if (!$fp->delete($id)) {
			$ret_img = 'error!';
		}
		$fp->ordering = 0;
		$ret_img = 'publish_x.png';
	} else {
		$query = "INSERT INTO #__content_frontpage" . "\n VALUES ( " . (int) $id . ", 0 )";
		$database->setQuery($query);
		if (!$database->query()) {
			$ret_img = 'error!';
		}
		$fp->ordering = 0;
		$ret_img = 'tick.png';
	}
	$fp->updateOrder();
	mosCache::cleanCache('com_content'); // почистим кэш контент
	return $ret_img;
}