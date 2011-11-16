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

// Получение опции (компонента)
$option = mosGetParam($_REQUEST, 'option', '');

//Файл расширения компонента
$file = JPATH_BASE . DS . 'includes' . DS . 'sef' . DS . $option . '.php';

//если есть расширение компонента, то подключаем его, если нет, то подключаем стандартный сеф
if(is_file($file)){
    require_once($file);
}
else{
    require_once(JPATH_BASE . DS . 'includes' . DS . 'sef' . DS . 'standard.php');
}