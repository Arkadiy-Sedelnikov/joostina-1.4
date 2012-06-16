<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
ob_start("ob_gzhandler");
header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60;
$ExpStr = "Expires: " .
	gmdate("D, d M Y H:i:s",
		time() + $offset) . " GMT";
header($ExpStr);
include("color_functions.js");
echo "\n\n";
include("js_color_picker_v2.js");
ob_flush();