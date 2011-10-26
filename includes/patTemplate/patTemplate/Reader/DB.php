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
define('PATTEMPLATE_READER_DB_ERROR_CLASS_NOT_FOUND','patTemplate::Reader::DB::001');
define('PATTEMPLATE_READER_DB_ERROR_NO_CONNECTION','patTemplate::Reader::DB::002');
define('PATTEMPLATE_READER_DB_ERROR_NO_INPUT','patTemplate::Reader::DB::003');
define('PATTEMPLATE_READER_DB_ERROR_UNKNOWN_INPUT','patTemplate::Reader::DB::004');
class patTemplate_Reader_DB extends patTemplate_Reader {
var $_name = 'DB';
function readTemplates($input) {
$content = $this->getDataFromDb($input);
if(patErrorManager::isError($content)) {
return $content;
}
$templates = $this->parseString($content);
return $templates;
}
function getDataFromDb($input) {

if(!class_exists('DB')) {
@include_once 'DB.php';
if(!class_exists('DB')) {
return patErrorManager::raiseError(PATTEMPLATE_READER_DB_ERROR_CLASS_NOT_FOUND,'This reader requires PEAR::DB which could not be found on your system.');
}
}

$db = &DB::connect($this->getTemplateRoot());
if(PEAR::isError($db)) {
return patErrorManager::raiseError(PATTEMPLATE_READER_DB_ERROR_NO_CONNECTION,'Could not establish database connection: '.$db->getMessage());
}
$input = $this->parseInputStringToQuery($input,$db);
if(patErrorManager::isError($input)) {
return $input;
}
$content = $db->getOne($input);
if(PEAR::isError($content)) {
return patErrorManager::raiseError(PATTEMPLATE_READER_DB_ERROR_NO_INPUT,'Could not fetch template: '.$content->getMessage());
}
return $content;
}
function parseInputStringToQuery($input,$db) {

if(strstr($input,'SELECT') !== false) {
return $input;
}
$matches = array();
if(!preg_match('/^([a-z]+)\[([^]]+)\]\/@([a-z]+)$/i',$input,$matches)) {
return patErrorManager::raiseError(PATTEMPLATE_READER_DB_ERROR_UNKNOWN_INPUT,'Could not parse input string.');
}
$table = $matches[1];
$templateField = $matches[3];
$where = array();
$tmp = explode(',',$matches[2]);
foreach($tmp as $clause) {
list($field,$value) = explode('=',trim($clause));
if($field{0} !== '@') {
return patErrorManager::raiseError(PATTEMPLATE_READER_DB_ERROR_UNKNOWN_INPUT,'Could not parse input string.');
}
$field = substr($field,1);
array_push($where,$field.'='.$db->quoteSmart($value));
}
$query = sprintf('SELECT %s FROM %s WHERE %s',$templateField,$table,implode(' AND ',$where));
return $query;
}
function loadTemplate($input) {
$content = $this->getDataFromDb($input);
return $content;
}
}
?>
