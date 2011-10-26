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
class patTemplate_Modifier_Surround extends patTemplate_Modifier {
function modify($value,$params = array()) {

$delimiter = "\n";
$start = '';
$end = '';

if(isset($params['delimiter'])) {
$delimiter = $params['delimiter'];
}
if(isset($params['start'])) {
$start = $params['start'];
}
if(isset($params['end'])) {
$end = $params['end'];
}

if(isset($params['keepdelimiter']) && $params['keepdelimiter'] === 'yes') {
$end .= $delimiter;
}
$split = explode($delimiter,$value);
$value = implode($end.$start,$split);

if(!isset($params['withfirst']) || $params['withfirst'] !== 'no') {
$value = $start.$value;
}

if(!isset($params['withlast']) || $params['withlast'] !== 'no') {
$value .= $end;
}
return $value;
}
}
?>
