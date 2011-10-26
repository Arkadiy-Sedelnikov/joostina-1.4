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
require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_builder.php');
class dom_xmlrpc_methodcall extends dom_xmlrpc_builder {
	var $methodName;
	var $multicall = array();
	function dom_xmlrpc_methodcall($methodName = '') {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_constants.php');
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_utilities.php');
		$this->methodType = DOM_XMLRPC_TYPE_METHODCALL;
		$this->methodName = $methodName;
		$total = func_num_args();
		for($i = 1; $i < $total; $i++) {
			$this->add(func_get_arg($i));
		}
	}

	function setMethodName($name = '') {
		$this->methodName = $name;
	}

	function addMultiCall($methodName) {
		$total = func_num_args();
		$paramsArray = array();
		for($i = 1; $i < $total; $i++) {
			$paramsArray[] = func_get_arg($i);
		}
		$this->addMultiCallByRef($methodName,$paramsArray);
	}

	function addMultiCallByRef($methodName,&$paramsArray) {
		$myCall = array(DOM_XMLRPC_TYPE_METHODNAME => $methodName,
			DOM_XMLRPC_TYPE_PARAMS => $paramsArray);
		$this->multicall[] = &$myCall;
	}

	function toString() {
		if($this->methodName == "system.multicall") {
			$this->addArrayByRef($this->multicall);
		}
		$data = <<< METHODCALL
<?xml version='1.0'?>
<methodCall>
	<methodName>$this->methodName</methodName>
	<params>$this->params
	</params>
</methodCall>
METHODCALL;
		return $data;
	}

	function toXML() {
		return $this->toString();
	}

}



?>
