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
require_once (DOM_XMLRPC_INCLUDE_PATH.'xml_domit_lite_parser.php');
require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_constants.php');
class dom_xmlrpc_domit_lite_document extends DOMIT_Lite_Document {
	function dom_xmlrpc_domit_lite_document() {
		$this->DOMIT_Lite_Document();
	}

	function getMethodType() {
		return $this->documentElement->nodeName;
	}

	function getMethodName() {
		if($this->getMethodType() == DOM_XMLRPC_TYPE_METHODCALL) {
			return $this->documentElement->childNodes[0]->firstChild->nodeValue;
		}

	}

	function &getParams() {
		switch($this->getMethodType()) {
			case DOM_XMLRPC_TYPE_METHODCALL:
				return $this->documentElement->childNodes[1];
				break;
			case DOM_XMLRPC_TYPE_METHODRESPONSE:
				if(!$this->isFault()) {
					return $this->documentElement->firstChild;
				}
				break;
		}

	}

	function &getParam($index) {
		switch($this->getMethodType()) {
			case DOM_XMLRPC_TYPE_METHODCALL:
				return $this->documentElement->childNodes[1]->childNodes[$index];
				break;
			case DOM_XMLRPC_TYPE_METHODRESPONSE:
				if(!$this->isFault()) {
					return $this->documentElement->firstChild->childNodes[$index];
				}
				break;
		}

	}

	function getParamCount() {
		switch($this->getMethodType()) {
			case DOM_XMLRPC_TYPE_METHODCALL:
				return $this->documentElement->childNodes[1]->childCount;
				break;
			case DOM_XMLRPC_TYPE_METHODRESPONSE:
				if(!$this->isFault()) {
					return $this->documentElement->firstChild->childCount;

				}
				break;
		}
		return - 1;

	}

	function isFault() {
		return ($this->documentElement->firstChild->nodeName == DOM_XMLRPC_TYPE_FAULT);
	}

	function getFaultCode() {
		if($this->isFault()) {
			$faultStruct = &$this->documentElement->firstChild->firstChild->firstChild;
			return ($faultStruct->childNodes[0]->childNodes[1]->firstChild->firstChild->nodeValue);
		}
	}

	function getFaultString() {
		if($this->isFault()) {
			$faultStruct = &$this->documentElement->firstChild->firstChild->firstChild;
			return ($faultStruct->childNodes[1]->childNodes[1]->firstChild->firstChild->nodeValue);
		}
	}

	function getParamType(&$node) {
		switch($node->nodeName) {
			case DOM_XMLRPC_TYPE_PARAM:
				return $node->firstChild->firstChild->nodeName;
				break;
			default:

		}
	}

}



?>
