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
define('PATTEMPLATE_FUNCTION_CALL_ERROR_NO_TEMPLATE','patTemplate::Function::Call::NT');
class patTemplate_Function_Call extends patTemplate_Function {
var $_name = 'Call';
var $_tmpl;
function setTemplateReference(&$tmpl) {
$this->_tmpl = &$tmpl;
}
function call($params,$content) {

if(isset($params['template'])) {
$tmpl = $params['template'];
unset($params['template']);
} elseif(isset($params['_originalTag'])) {
$tmpl = $params['_originalTag'];
unset($params['_originalTag']);
} else {
return patErrorManager::raiseError(PATTEMPLATE_FUNCTION_CALL_ERROR_NO_TEMPLATE,'No template for Call function specified.');
}
if(!$this->_tmpl->exists($tmpl)) {
$tmpl = strtolower($tmpl);

$componentLocation = $this->_tmpl->getOption('componentFolder');
$componentExtension = $this->_tmpl->getOption('componentExtension');
$filename = $componentLocation.'/'.$tmpl.'.'.$componentExtension;
$this->_tmpl->readTemplatesFromInput($filename);

if(!$this->_tmpl->exists($tmpl)) {
return patErrorManager::raiseError(PATTEMPLATE_FUNCTION_CALL_ERROR_NO_TEMPLATE,'Template '.$tmpl.' does not exist');
}
}
$this->_tmpl->clearTemplate($tmpl,true);
$this->_tmpl->addVars($tmpl,$params);
$this->_tmpl->addVar($tmpl,'CONTENT',$content);
return $this->_tmpl->getParsedTemplate($tmpl);
}
}
?>
