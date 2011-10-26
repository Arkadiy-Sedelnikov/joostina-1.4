<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

$type = intval($params->get('type', 1));
$def_itemid = $params->get('def_itemid', false);
$params->def('item_title', 1);
$params->def('link_titles', 1);
$params->def('template', 'default.php');

//Подключаем вспомогательный класс
$module->get_helper($mainframe);

// Выбор между выводом содержимого объектов, 
//статического содержимого или сразу обоих
switch ($type) {
	case 2:
	//Только статическое содержимое
		$rows = $module->helper->get_static_items($params);
		break;

	case 3:
	//Оба типа
		$rows = $module->helper->get_items_both($params);
		break;

	case 1:
	default:
	//Только содержимое категорий
		$rows = $module->helper->get_category_items($params);
		break;
}

$params->def('numrows', count($rows));
$params->set('intro_only',1);

if(!$def_itemid>0) {
	// требование уменьшить запросы, используемые getItemid для объектов содержимого
	if (($type == 1) || ($type == 3)) {
		$params->def('bs', 1);
		$params->def('bc', 1);
		$params->def('gbs',1);
	}
}

//Подключаем шаблон
if($module->set_template($params)) {
	require($module->template);
}