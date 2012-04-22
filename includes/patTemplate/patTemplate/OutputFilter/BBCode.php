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
class patTemplate_OutputFilter_BBCode extends patTemplate_OutputFilter {
var $_name = 'BBCode';
var $BBCode = null;
function apply($data) {
if(!$this->_prepare())
return $data;
$data = $this->BBCode->parseString($data);
return $data;
}
function _prepare() {

if(is_object($this->BBCode)) {
return true;
}

if(isset($this->_params['BBCode'])) {
$this->BBCode = &$this->_params['BBCode'];
return true;
}

if(!class_exists('patBBCode')) {
if(!@include_once 'pat/patBBCode.php')
return false;
}
$this->BBCode = new patBBCode();
if(isset($this->_params['skinDir']))
$this->BBCode->setSkinDir($this->_params['skinDir']);
$reader = &$this->BBCode->createConfigReader($this->_params['reader']);

$this->BBCode->setConfigReader($reader);
return true;
}
}
?>
