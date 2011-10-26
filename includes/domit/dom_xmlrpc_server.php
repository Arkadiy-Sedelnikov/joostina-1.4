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


define('DOM_XMLRPC_PARSE_ERROR_NOT_WELL_FORMED',-32700);
define('DOM_XMLRPC_PARSE_ERROR_UNSUPPORTED_ENCODING',-32701);
define('DOM_XMLRPC_PARSE_ERROR_INVALID_CHARACTER_ENCODING',-32702);
define('DOM_XMLRPC_SERVER_ERROR_INVALID_XMLRPC_NONCONFORMANT',-32600);
define('DOM_XMLRPC_SERVER_ERROR_REQUESTED_METHOD_NOT_FOUND',-32601);
define('DOM_XMLRPC_SERVER_ERROR_INVALID_METHOD_PARAMETERS',-32602);
define('DOM_XMLRPC_SERVER_ERROR_INTERNAL_XMLRPC',-32603);
define('DOM_XMLRPC_APPLICATION_ERROR',-32500);
define('DOM_XMLRPC_SYSTEM_ERROR',-32400);
define('DOM_XMLRPC_TRANSPORT_ERROR',-32300);
require_once (DOM_XMLRPC_INCLUDE_PATH.'php_http_server_generic.php');
require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_constants.php');
class dom_xmlrpc_server extends php_http_server_generic {
	var $methodmapper;
	var $tokenizeParamsArray = false;
	var $serverError = -1;
	var $serverErrorString = '';
	var $methodNotFoundHandler = null;
	var $objectAwareness = false;
	var $objectDefinitionHandler = null;
	var $multiresponse = array();
	function dom_xmlrpc_server($customMethods = null,$postData = null) {
		$this->php_http_server_generic();
		$this->setHTTPEvents();
		$this->setHeaders();
		$this->methodmapper = new dom_xmlrpc_methodmapper();
		$this->addSystemMethods();
		if($customMethods != null)
			$this->addCustomMethods($customMethods);
		if($postData != null)
			$this->fireHTTPEvent('onPost');

	}

	function setHeaders() {
		$this->setHeader('Content-Type','text/xml');
		$this->setHeader('Server','DOM XML-RPC Server/0.1');
	}

	function setHTTPEvents() {
		$this->setHTTPEvent('onPost',array(&$this,'onPost'));
		$defaultHandler = array(&$this,'onWrongRequestMethod');
		$this->setHTTPEvent('onGet',$defaultHandler);
		$this->setHTTPEvent('onHead',$defaultHandler);
		$this->setHTTPEvent('onPut',$defaultHandler);
	}

	function addSystemMethods() {
		$this->methodmapper->addMethod(new dom_xmlrpc_method(array('name' =>
			'system.listMethods','method' => array(&$this,'listMethods'),'help' =>
			'Lists available server methods.','signature' => array(DOM_XMLRPC_TYPE_ARRAY))));
		$this->methodmapper->addMethod(new dom_xmlrpc_method(array('name' =>
			'system.methodSignature','method' => array(&$this,'methodSignature'),'help' =>
			'Returns signature of specified method.','signature' => array(DOM_XMLRPC_TYPE_ARRAY,
			DOM_XMLRPC_TYPE_STRING))));
		$this->methodmapper->addMethod(new dom_xmlrpc_method(array('name' =>
			'system.methodHelp','method' => array(&$this,'methodHelp'),'help' =>
			'Returns help for the specified method.','signature' => array(DOM_XMLRPC_TYPE_STRING,
			DOM_XMLRPC_TYPE_STRING))));
		$this->methodmapper->addMethod(new dom_xmlrpc_method(array('name' =>
			'system.getCapabilities','method' => array(&$this,'getCapabilities'),'help' =>
			'Returns an array of supported server specifications.','signature' => array(DOM_XMLRPC_TYPE_ARRAY))));
		$this->methodmapper->addMethod(new dom_xmlrpc_method(array('name' =>
			'system.multicall','method' => array(&$this,'multicall'),'help' =>
			'Handles multiple, asynchronous XML-RPC calls bundled into a single request.',
			'signature' => array(DOM_XMLRPC_TYPE_ARRAY,DOM_XMLRPC_TYPE_ARRAY))));
	}

	function addCustomMethods($customMethods) {
		foreach($customMethods as $key => $value) {
			$this->methodmapper->addMethod($customMethods[$key]);
		}
	}

	function addMethods($methodsArray) {
		foreach($methodsArray as $key => $value) {
			$this->methodmapper->addMethod($methodsArray[$key]);
		}
	}

	function addMethod($method) {

		$this->methodmapper->addMethod($method);
	}

	function addMappedMethods(&$methodmap,$methodNameList) {
		$this->methodmapper->addMappedMethods($methodmap,$methodNameList);
	}

	function tokenizeParams($truthVal) {


		$this->tokenizeParamsArray = $truthVal;
	}



	function listMethods() {
		return $this->methodmapper->getMethodNames();
	}

	function methodSignature($name) {
		$myMethod = &$this->methodmapper->getMethod($name);
		return $myMethod->signature;
	}

	function methodHelp($name) {
		$myMethod = &$this->methodmapper->getMethod($name);
		return $myMethod->help;
	}

	function getCapabilities() {
		$capabilities = array('xmlrpc' => array('specUrl' =>
			'http://www.xmlrpc.com/spec','specVersion' => 1),'introspect' => array('specUrl' =>
			'http://xmlrpc.usefulinc.com/doc/reserved.html','specVersion' => 1),
			'system.multicall' => array('specUrl' =>
			'http://www.xmlrpc.com/discuss/msgReader$1208','specVersion' => 1),
			'faults_interop' => array('specUrl' =>
			'http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php','specVersion' => 3));
		return $capabilities;
	}

	function &multicall(&$myArray) {


		foreach($myArray as $key => $value) {
			$currCall = &$myArray[$key];
			$methodName = $currCall[DOM_XMLRPC_TYPE_METHODNAME];
			$method = &$this->methodmapper->getMethod($methodName);
			$params = $currCall[DOM_XMLRPC_TYPE_PARAMS];
			if(!($method == null)) {
				if($this->tokenizeParamsArray) {
					$this->multiresponse[] = &call_user_func_array($method->method,$params);

				} else {
					if(count($params) == 1) {


						$this->multiresponse[] = &call_user_func($method->method,$params[0]);
					} else {

						$this->multiresponse[] = &call_user_func($method->method,$params);
					}
				}
			} else {


			}
		}
		return $this->multiresponse;
	}



	function onPost($postData) {
		global $HTTP_RAW_POST_DATA;
		if($postData == null)
			$postData = $HTTP_RAW_POST_DATA;
		$this->respond($this->invokeMethod($postData));
	}

	function &invokeMethod($xmlText) {
		$xmlrpcdoc = $this->parseRequest($xmlText);
		if(!$this->isError()) {
			$methodName = $xmlrpcdoc->getMethodName();
			$method = &$this->methodmapper->getMethod($methodName);
			$params = &$xmlrpcdoc->getParams();
			if(!($method == null)) {
				if($this->tokenizeParamsArray) {
					$response = &call_user_func_array($method->method,$params);

				} else {
					if(count($params) == 1) {


						$response = &call_user_func($method->method,$params[0]);
					} else {

						$response = &call_user_func($method->method,$params);
					}
				}
				return $this->buildResponse($response);
			} else {

				return $this->handleMethodNotFound($methodName,$params);
			}
		}
	}

	function &handleMethodNotFound($methodName,&$params) {
		if($this->methodNotFoundHandler == null) {

			$this->serverError = DOM_XMLRPC_SERVER_ERROR_REQUESTED_METHOD_NOT_FOUND;
			$this->serverErrorString =
				'DOM XML-RPC Server Error - Requested method not found.';
			return $this->raiseFault();
		} else {


			return call_user_func($this->methodNotFoundHandler,$this,$methodName,$params);
		}
	}

	function setMethodNotFoundHandler($method) {
		$this->methodNotFoundHandler = &$method;
	}

	function &buildResponse(&$response) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_fault.php');
		if(is_object($response) && (get_class($response) == 'dom_xmlrpc_fault')) {
			return $this->buildFault($response);
		} else {
			return $this->buildMethodResponse($response);
		}
	}

	function buildMethodResponse($response) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_methodresponse.php');
		$methodResponse = new dom_xmlrpc_methodresponse($response);
		return $methodResponse->toXML();
	}

	function isError() {
		return ($this->serverError != -1);
	}

	function &raiseFault() {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_fault.php');
		$fault = new dom_xmlrpc_fault($this->serverError,$this->serverErrorString);
		return $this->buildFault($fault);
	}

	function buildFault($response) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_methodresponse_fault.php');
		$fault = new dom_xmlrpc_methodresponse_fault($response);
		return $fault->toXML();
	}

	function setObjectAwareness($truthVal) {
		$this->objectAwareness = $truthVal;
	}

	function setObjectDefinitionHandler($handler) {
		$this->objectDefinitionHandler = &$handler;
	}

	function &parseRequest($xmlText) {
		if($this->objectAwareness) {
			require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_object_parser.php');
			$parser = new dom_xmlrpc_object_parser($this->objectDefinitionHandler);
		} else {
			require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_array_parser.php');
			$parser = new dom_xmlrpc_array_parser();
		}
		if($parser->parseXML($xmlText,false)) {
			return $parser->getArrayDocument();
		} else {

			$this->serverError = DOM_XMLRPC_PARSE_ERROR_NOT_WELL_FORMED;
			$this->serverErrorString =
				'DOM XML-RPC Parse Error - XML document not well formed.';
			return null;
		}
	}

	function onWrongRequestMethod() {

		$this->serverError = DOM_XMLRPC_SERVER_ERROR_INTERNAL_XMLRPC;
		$this->serverErrorString = 'DOM XML-RPC Server Error - '.
			'Only POST method is allowed by the XML-RPC specification.';
		$this->respond($this->raiseFault());
	}

}

class dom_xmlrpc_methods {
	var $methods = array();
	function addMethod(&$method) {
		$this->methods[$method->name] = &$method;
	}

	function &getMethod($name) {
		if(isset($this->methods[$name])) {
			return $this->methods[$name];
		}
		return null;
	}

	function getMethodNames() {
		return array_keys($this->methods);
	}

}

class dom_xmlrpc_methodmapper {
	var $mappedmethods = array();
	var $unmappedmethods;
	function dom_xmlrpc_methodmapper() {
		$this->unmappedmethods = new dom_xmlrpc_methods();
	}

	function addMethod(&$method) {
		$this->unmappedmethods->addMethod($method);
		$this->mappedmethods[$method->name] = &$this->unmappedmethods;
	}

	function addMappedMethods(&$methodmap,$methodNameList) {
		$total = count($methodNameList);
		for($i = 0; $i < $total; $i++) {
			$this->mappedmethods[$methodNameList[$i]] = &$methodmap;
		}
	}

	function &getMethod($name) {
		if(isset($this->mappedmethods[$name])) {
			$methodmap = &$this->mappedmethods[$name];
			return $methodmap->getMethod($name);
		}
		return null;
	}

	function getMethodNames() {
		return array_keys($this->mappedmethods);
	}

}

class dom_xmlrpc_method {
	var $name;
	var $method;
	var $help = '';
	var $signature = '';
	function dom_xmlrpc_method($paramArray) {
		$this->name = $paramArray['name'];
		$this->method = &$paramArray['method'];
		if(isset($paramArray['help'])) {
			$this->help = $paramArray['help'];
		}
		if(isset($paramArray['signature'])) {
			$this->signature = $paramArray['signature'];
		}
	}

}

class dom_xmlrpc_methodmap {
	function &getMethod($methodName) {

	}

}





?>
