<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*
* @version		3.1.0
* @package		patTemplate
* @author		Stephan Schmidt <schst@php.net>
* @license		LGPL
* @link		http://www.php-tools.net
*/
// запрет прямого доступа
defined('_VALID_MOS') or die();

define('PATTEMPLATE_FUNCTION_COMPILE',1);
define('PATTEMPLATE_FUNCTION_RUNTIME',2);
class patTemplate_Function extends patTemplate_Module {
var $_reader;
var $type = PATTEMPLATE_FUNCTION_COMPILE;
function setReader(&$reader) {
$this->_reader = &$reader;
}
function call($params,$content) {
}
}
?>
