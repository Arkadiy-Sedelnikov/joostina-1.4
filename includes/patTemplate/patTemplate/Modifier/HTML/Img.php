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
class patTemplate_Modifier_HTML_Img extends patTemplate_Modifier {
function modify($value,$params = array()) {
$size = getimagesize($value);
$params['src'] = $value;
$params['width'] = $size[0];
$params['height'] = $size[1];
return '<img'.$this->arrayToAttributes($params).' />';
}
function arrayToAttributes($array) {
$string = '';
foreach($array as $key => $val) {
$string .= ' '.$key.'="'.htmlspecialchars($val).'"';
}
return $string;
}
}
?>
