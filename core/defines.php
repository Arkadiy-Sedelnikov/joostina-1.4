<?php
/**
 * Joostina Lotos CMS 1.4.1
 * @package   DEFINITIONS
 * @version   1.0
 * @author    Gold Dragon <illusive@bk.ru>
 * @link      http://gd.joostina-cms.ru
 * @copyright 2000-2012 Gold Dragon
 * @license   GNU GPL: http://www.gnu.org/licenses/gpl-3.0.html
 *            Joostina Lotos CMS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL. (help/copyright.php)
 *            Date: 18.06.2012
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

// Глобальные определения.
define('DS', DIRECTORY_SEPARATOR);

// TODO временная заглушка
define('JPATH_BASE', _JLPATH_ROOT);

// TODO временная заглушка
define('JPATH_ROOT', _JLPATH_ROOT);

// путь до библиотек
define('_JLPATH_LIBRARIES', _JLPATH_ROOT . DS . 'libraries');

// путь до sef-файлов
define('_JLPATH_SEF', _JLPATH_ROOT . DS . 'settings' . DS . 'sef');

// путь до каталога панели управления
define('_JLPATH_ADMINISTRATOR', _JLPATH_ROOT . DS . 'administrator');

// путь до каталога с языковыми файлами
define('_JLPATH_LANG', _JLPATH_ROOT . DS . 'language');

// путь до каталога с языковыми файлами
define('_JLPATH_TEMPLATES', _JLPATH_ROOT . DS . 'templates');


// функции отладки
function _xdump($var, $text = '<pre>'){
	echo $text;
	print_r($var);
	echo "\n";
}

function _vdump($var){
	echo '<pre style="border:1px solid #ff0000;color:#ff0000;padding:5px;background-color:#ffffff;">';
	var_dump($var);
	echo "</pre>";
}

function _pdump($var){
	echo '<pre style="border:1px solid #ff0000;color:#ff0000;padding:5px;background-color:#ffffff;">';
	print_r($var);
	echo "</pre>";
}






























