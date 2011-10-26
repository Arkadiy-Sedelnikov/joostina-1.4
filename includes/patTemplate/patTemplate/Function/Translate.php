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
define('PATTEMPLATE_FUNCTION_TRANSLATE_WARNING_LANGFOLDER_NOT_CREATABLE','patTemplate:Function:Translate:01');
define('PATTTEMPLATE_FUNCTION_TRANSLATE_WARNING_LANGFILE_NOT_CREATABLE','patTemplate:Function:Translate:02');
class patTemplate_Function_Translate extends patTemplate_Function {
var $_name = 'Translate';
function call($params,$content) {
$escape = isset($params['escape'])?$params['escape']:'';
if(class_exists('JText')) {

if(count($params) > 0 && key_exists('key',$params)) {
$text = JText::_($params['key']);
} else {
$text = JText::_($content);
}
} else {
if(defined($content)) {
$text = constant($content);
} else {
$text = $content;
}
}
if($escape == 'yes' || $escape == 'true') {
$text = addslashes($text);
}
return $text;
}
}
?>
