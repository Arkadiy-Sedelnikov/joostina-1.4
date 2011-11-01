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

require_once ($mainframe->getPath('class', 'com_frontpage'));
$db = $database::getInstance();
$frontpageConf = null;
$configObject = new frontpageConfig($db);
$frontpageConf->directory = $configObject->get('directory', 0);
$frontpageConf->task = $configObject->get('page', 'front');

$isFrontpage = 1;

// code handling has been shifted into content.php
require_once (JPATH_BASE.'/components/com_boss/boss.php');