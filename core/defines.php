<?php
/**
 * Joostina Lotos CMS 1.4
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
define('JPATH_BASE',_JLPATH_ROOT);

// TODO временная заглушка
define('JPATH_ROOT',_JLPATH_ROOT);

// путь до библиотек
define('_JLPATH_LIBRARIES',		_JLPATH_ROOT . '/libraries');

// путь до sef-файлов
define('_JLPATH_SEF',		_JLPATH_ROOT . '/settings/sef');

// путь до каталога панели управления
define('_JLPATH_ADMINISTRATOR',	_JLPATH_ROOT . '/administrator');

//define('JPATH_SITE',			JPATH_ROOT);
//define('JPATH_CONFIGURATION',	JPATH_ROOT);
//define('JPATH_PLUGINS',			JPATH_ROOT . '/plugins'  );
//define('JPATH_INSTALLATION',	JPATH_ROOT . '/installation');
//define('JPATH_THEMES',			JPATH_BASE . '/templates');
//define('JPATH_CACHE',			JPATH_BASE . '/cache');
//define('JPATH_MANIFESTS',		JPATH_ADMINISTRATOR . '/manifests');



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






























