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

$def_itemid = $params->get('def_itemid', false);
$params->get('item_title', 1);
$params->get('link_titles', 1);
$params->get('template', 'default.php');

//Подключаем вспомогательный класс
$module->get_helper($mainframe);

//вывод содержимого объектов
$rows = $module->helper->get_items($params);

$params->def('numrows', count($rows));
$params->set('intro_only',1);

//Подключаем шаблон
if($module->set_template($params)) {
	require($module->template);
}