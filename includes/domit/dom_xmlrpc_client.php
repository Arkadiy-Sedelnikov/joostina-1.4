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

define('XMLRPC_CLIENT_RESPONSE_TYPE_ERR',1);
define('XMLRPC_CLIENT_MALFORMED_XML_ERR',2);
require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_methodcall.php');
require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_constants.php');
require_once (DOM_XMLRPC_INCLUDE_PATH.'php_http_client_generic.php');
class dom_xmlrpc_client extends php_http_client_generic {
	var $responseType = DOM_XMLRPC_RESPONSE_TYPE_XML_DOMIT;
	var $isMultiCall = false;
	function dom_xmlrpc_client($host = '',$path = '/',$port = 80,$proxy = '',$timeout =
		0) {
		if($proxy != '') {
			$host = $proxy;
		}
		$this->php_http_client_generic($host,$path,$port,$timeout);
		$this->setHeaders();
	}

	function setHeaders() {
		$this->setHeader('Content-Type','text/xml');
		$this->setHeader('Host',$this->connection->host);
		$this->setHeader('User-Agent','DOM XML-RPC Client/0.1');
		$this->setHeader('Connection','close');
	}

	function evaluateMessage(&$message) {
		if(!is_string($message)) {
			if($message->methodName == 'system.multicall')
				$this->isMultiCall = true;
			return $message->toXML();
		} else {
			if(strpos($message,'system.multicall') !== false)
				$this->isMultiCall = true;
		}
		return $message;
	}

	function &send(&$message) {
		if(!$this->isConnected()) {
			$this->connect();
		}
		$message = $this->evaluateMessage($message);
		$this->setHeader('Content-Length',strlen($message));
		$response = &parent::send($message);
		return $this->formatResponse($response->getResponse());
	}

	function &sendAndDisconnect($message) {
		$response = &$this->send($message);
		$this->disconnect();
		return $response;
	}

	function &formatResponse($response) {
		switch($this->responseType) {
			case DOM_XMLRPC_RESPONSE_TYPE_XML_DOMIT:
			case DOM_XMLRPC_RESPONSE_TYPE_XML_DOMIT_LITE:
			case DOM_XMLRPC_RESPONSE_TYPE_XML_DOMXML:
				return $this->returnAsXML($response);
				break;
			case DOM_XMLRPC_RESPONSE_TYPE_ARRAY:
				return $this->returnAsArray($response);
				break;
			case DOM_XMLRPC_RESPONSE_TYPE_STRING:
				return $response;
				break;
		}
	}

	function &returnAsXML($response) {
		switch($this->responseType) {
			case DOM_XMLRPC_RESPONSE_TYPE_XML_DOMIT:
				require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_domit_parser.php');
				$xmlrpcDoc = new dom_xmlrpc_domit_document();
				$success = $xmlrpcDoc->parseXML($response,false);
				break;
			case DOM_XMLRPC_RESPONSE_TYPE_XML_DOMIT_LITE:
				require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_domit_lite_parser.php');
				$xmlrpcDoc = new dom_xmlrpc_domit_lite_document();
				$success = $xmlrpcDoc->parseXML($response,false);
				break;
			case DOM_XMLRPC_RESPONSE_TYPE_XML_XML_DOMXML:
				require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_domxml_parser.php');
				$xmlrpcDoc = new dom_xmlrpc_domxml_document();
				$success = $xmlrpcDoc->parseXML($response);
				break;
		}
		if($success) {
			return $xmlrpcDoc;
		}
		XMLRPC_Client_Exception::raiseException(XMLRPC_CLIENT_MALFORMED_XML_ERR,("Malformed xml returned: \n $response"));
	}

	function &returnAsArray($response) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_array_parser.php');
		$arrayParser = new dom_xmlrpc_array_parser();
		if($arrayParser->parseXML($response,false)) {
			return $arrayParser->getArrayDocument();
		} else {
			XMLRPC_Client_Exception::raiseException(XMLRPC_CLIENT_MALFORMED_XML_ERR,("Malformed xml returned:  \n $response"));
		}
	}

	function &arraysToObjects(&$myArray) {
		foreach($myArray as $key => $value) {
			$currItem = &$myArray[$key];
			if(is_array($currItem)) {
				$currItem = &$this->arraysToObjects($currItem);
			}
		}
		if(dom_xmlrpc_utilities::isAssociativeArray($myArray)) {
			$obj = new stdclass();
			foreach($myArray as $key => $value) {
				$obj->$key = &$myArray[$value];
			}
			return $obj;
		} else {
			return $myArray;
		}
	}

	function setResponseType($type) {
		$type = strtolower($type);
		switch($type) {
			case DOM_XMLRPC_RESPONSE_TYPE_ARRAY:
			case DOM_XMLRPC_RESPONSE_TYPE_XML_DOMIT:
			case DOM_XMLRPC_RESPONSE_TYPE_XML_DOMIT_LITE:
			case DOM_XMLRPC_RESPONSE_TYPE_XML_DOMXML:
			case DOM_XMLRPC_RESPONSE_TYPE_STRING:
				$this->responseType = $type;
				break;
			default:
				XMLRPC_Client_Exception::raiseException(XMLRPC_CLIENT_RESPONSE_TYPE_ERR,('Invalid response type: '.
					$type));
		}
	}

	function getResponseType() {
		return $this->responseType;
	}

}

class XMLRPC_Client_Exception {
	function raiseException($errorNum,$errorString) {
		$errorMessage = $errorNum."\n ".$errorString;
		if((!isset($GLOBALS['DOMIT_XMLRPC_ERROR_FORMATTING_HTML'])) || ($GLOBALS['DOMIT_XMLRPC_ERROR_FORMATTING_HTML'] == true)) {
			$errorMessage = "<p><pre>".$errorMessage."</pre></p>";
		}
		die($errorMessage);
	}

}





?>
