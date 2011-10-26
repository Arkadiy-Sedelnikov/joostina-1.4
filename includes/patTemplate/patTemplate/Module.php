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


class patTemplate_Module {
var $_name = null;
var $_params = array();
function getName() {
return $this->_name;
}
function setParams($params,$clear = false) {
if($clear === true)
$this->_params = array();
$this->_params = array_merge($this->_params,$params);
}
function getParam($name) {
if(isset($this->_params[$name]))
return $this->_params[$name];
return false;
}
}
?>
