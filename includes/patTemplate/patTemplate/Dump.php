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

class patTemplate_Dump extends patTemplate_Module {
var $_tmpl;
function setTemplateReference(&$tmpl) {
$this->_tmpl = &$tmpl;
}
function displayHeader() {
}
function dumpGlobals($globals) {
}
function dumpTemplates($templates,$vars) {
}
function displayFooter() {
}
function _flattenVars($vars) {
$flatten = array();
foreach($vars['scalar'] as $var => $value) {
$flatten[$var] = $value;
}
foreach($vars['rows'] as $row) {
foreach($row as $var => $value) {
if(!isset($flatten[$var]) || !is_array($flatten[$var]))
$flatten[$var] = array();
array_push($flatten[$var],$value);
}
}
foreach($flatten as $var => $value) {
if(!is_array($value))
continue;
$flatten[$var] = '['.count($value).' rows] ('.implode(', ',$value).')';
}
return $flatten;
}
function _extractVars($template) {
$pattern = '/'.$this->_tmpl->getStartTag().'([^a-z]+)'.$this->_tmpl->getEndTag().'/U';
$matches = array();
$result = preg_match_all($pattern,$template,$matches);
if($result == false)
return array();
$vars = array();
foreach($matches[1] as $var) {
if(strncmp($var,'TMPL:',5) === 0)
continue;
array_push($vars,$var);
}
return array_unique($vars);
}
}
?>
