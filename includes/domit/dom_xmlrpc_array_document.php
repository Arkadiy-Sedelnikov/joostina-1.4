<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*
* dom_xmlrpc_array_document wraps a PHP array with the DOM XML-RPC API
* @package dom-xmlrpc
* @copyright (C) 2004 John Heinstein. All rights reserved
* @license http://www.gnu.org/copyleft/lesser.html LGPL License
* @author John Heinstein <johnkarl@nbnet.nb.ca>
* @link http://www.engageinteractive.com/dom_xmlrpc/ DOM XML-RPC Home Page
* DOM XML-RPC is Free Software
**/

defined('_VALID_MOS') or die();
if(!defined('DOM_XMLRPC_INCLUDE_PATH')) {
	define('DOM_XMLRPC_INCLUDE_PATH',(dirname(__file__)."/"));
}
require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_constants.php');
class dom_xmlrpc_array_document {
	var $methodType;
	var $methodName = "";
	var $params = array();
	var $faultCode = null;
	var $faultString = null;
	function dom_xmlrpc_array_document() {

	}

	function getMethodType() {
		return $this->methodType;
	}

	function getMethodName() {
		return $this->methodName;

	}

	function &getParams() {
		return $this->params;
	}

	function &getParam($index) {
		return $this->params[$index];
	}

	function getParamCount() {
		return count($this->params);
	}

	function isFault() {
		return ($this->methodType == DOM_XMLRPC_TYPE_FAULT);
	}

	function getFaultCode() {
		return $this->faultCode;
	}

	function getFaultString() {
		return $this->faultString;
	}

	function getParamType($param) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_utilities.php');
		return (dom_xmlrpc_utilities::getTypeFromValue($param));
	}

	function toString() {
		ob_start();
		print_r($this->params);
		$ob_contents = ob_get_contents();
		ob_end_clean();
		return $ob_contents;
	}

	function toNormalizedString() {
		return $this->toString();
	}

}




?>
