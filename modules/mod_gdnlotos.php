<?php
/**
 * @package GDNLotos - Главные новости
 * @copyright Авторские права (C) 2000-2011 Gold Dragon.
 * @license http://www.gnu.org/licenses/gpl.htm GNU/GPL
 * GDNLotos - Главные новости - модуль позволяет выводить основные материалы по определённым критериям для Joostina 1.4.0.x
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл view/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();
// подключаем вспомогательный класс
$module->get_helper($mainframe);

// выводим модуль
$module->helper->getHTML($params, $module->id);









