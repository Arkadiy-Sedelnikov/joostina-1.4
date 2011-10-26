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
define('PATTEMPLATE_OUTPUTFILTER_TIDY_ERROR_NOT_SUPPORTED','patTemplate::Outputfilter::Tidy::1');
class patTemplate_OutputFilter_Tidy extends patTemplate_OutputFilter {
var $_name = 'Tidy';
function apply($data) {
if(!function_exists('tidy_parse_string')) {
return $data;
}
if(function_exists('tidy_setopt') && is_array($this->_params)) {
foreach($this->_params as $opt => $value) {
tidy_setopt($opt,$value);
}
tidy_parse_string($data);
tidy_clean_repair();
$data = tidy_get_output();
} else {
$tidy = tidy_parse_string($data,$this->_params);
tidy_clean_repair($tidy);
$data = tidy_get_output($tidy);
}
return $data;
}
}
?>
