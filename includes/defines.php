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
defined('_VALID_MOS') or die();

// Глобальные определения.


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






























