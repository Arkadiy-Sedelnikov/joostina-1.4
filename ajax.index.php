<?php

/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
// Установка флага, что это - родительский файл
define('_VALID_MOS', 1);
// корень файлов
define('JPATH_BASE', dirname(__FILE__));
// разделитель каталогов
define('DS', DIRECTORY_SEPARATOR);

require (JPATH_BASE . '/includes/globals.php');
require_once ('./configuration.php');
// live_site
define('JPATH_SITE', $mosConfig_live_site);
// для совместимости
$mosConfig_absolute_path = JPATH_BASE;

require_once ('includes/joostina.php');

// отображение состояния выключенного сайта
if ($mosConfig_offline == 1) {
	echo 'syte-offline';
	exit();
}

if (file_exists(JPATH_BASE . '/components/com_sef/sef.php')) {
	require_once (JPATH_BASE . '/components/com_sef/sef.php');
} else {
	require_once (JPATH_BASE . '/includes/sef.php');
}

// автоматическая перекодировка в юникод, по умолчанию актвино
$utf_conv = intval(mosGetParam($_REQUEST, 'utf', 1));
$option = strval(strtolower(mosGetParam($_REQUEST, 'option', '')));
$task = strval(mosGetParam($_REQUEST, 'task', ''));

$commponent = str_replace('com_', '', $option);

if ($mosConfig_mmb_ajax_starts_off == 0) {
	$_MAMBOTS->loadBotGroup('system');
	$_MAMBOTS->trigger('onAjaxStart');
}

// mainframe - основная рабочая среда API, осуществляет взаимодействие с 'ядром'
$mainframe = mosMainFrame::getInstance();

$mainframe->initSession();

// загрузка файла русского языка по умолчанию
if ($mosConfig_lang == '') {
	$mosConfig_lang = 'russian';
}
$mainframe->set('lang', $mosConfig_lang);
include_once($mainframe->getLangFile());

$my = $mainframe->getUser();

$gid = intval($my->gid);

if ($mosConfig_mmb_ajax_starts_off == 0) {
	$_MAMBOTS->trigger('onAfterAjaxStart');
}

header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate ");

// проверяем, какой файл необходимо подключить, данные берутся из пришедшего GET запроса
if (file_exists(JPATH_BASE . "/components/$option/$commponent.ajax.php")) {
	include_once (JPATH_BASE . "/components/$option/$commponent.ajax.php");
} else {
	die('error-1');
}