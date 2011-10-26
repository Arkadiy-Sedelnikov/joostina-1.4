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
require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_parser.php');
class dom_xmlrpc_object_parser extends dom_xmlrpc_parser {
	var $testingForObject = false;

	var $objectDefinitionHandler = null;

	function dom_xmlrpc_object_parser(&$objectDefinitionHandler) {
		$this->objectDefinitionHandler = &$objectDefinitionHandler;
	}

	function startElement($parser,$name,$attrs) {
		switch($name) {
			case DOM_XMLRPC_TYPE_METHODCALL:
			case DOM_XMLRPC_TYPE_METHODRESPONSE:
			case DOM_XMLRPC_TYPE_FAULT:
				$this->arrayDocument->methodType = $name;

				break;
			case DOM_XMLRPC_TYPE_ARRAY:
			case DOM_XMLRPC_TYPE_STRUCT:
				$this->lastArrayType[] = $name;
				$this->lastArray[] = array();
				$this->testingForObject = true;
				break;
		}
	}

	function endElement($parser,$name) {
		switch($name) {
			case DOM_XMLRPC_TYPE_STRING:

				$this->addValue($this->charContainer);
				break;
			case DOM_XMLRPC_TYPE_I4:
			case DOM_XMLRPC_TYPE_INT:
				$this->addValue((int)($this->charContainer));
				break;
			case DOM_XMLRPC_TYPE_DOUBLE:
				$this->addValue(floatval($this->charContainer));
				break;
			case DOM_XMLRPC_TYPE_BOOLEAN:
				$this->addValue((bool)(trim($this->charContainer)));
				break;
			case DOM_XMLRPC_TYPE_BASE64:
				require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_base64.php');
				$base64 = new dom_xmlrpc_base64();
				$base64->fromString($this->charContainer);
				$this->addValue($base64);

				break;
			case DOM_XMLRPC_TYPE_DATETIME:
				require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_datetime_iso8601.php');
				$dateTime = new dom_xmlrpc_datetime_iso8601($this->charContainer);
				$this->addValue($dateTime);

				break;
			case DOM_XMLRPC_TYPE_VALUE:


				$myValue = trim($this->charContainer);

				if($myValue != '')
					$this->addValue($myValue);
				break;
			case DOM_XMLRPC_TYPE_ARRAY:
			case DOM_XMLRPC_TYPE_STRUCT:
				$value = &array_pop($this->lastArray);
				$this->addValue($value);
				array_pop($this->lastArrayType);
				break;
			case DOM_XMLRPC_TYPE_MEMBER:
				array_pop($this->lastStructName);
				break;
			case DOM_XMLRPC_TYPE_NAME:
				$cn = trim($this->charContainer);
				$this->lastStructName[] = $cn;
				$this->charContainer = '';
				if($this->testingForObject && ($cn == DOM_XMLRPC_PHPOBJECT)) {
					$this->lastArrayType[(count($this->lastArray) - 1)] = DOM_XMLRPC_PHPOBJECT;
				}
				$this->testingForObject = false;
				break;
			case DOM_XMLRPC_TYPE_METHODNAME:
				$this->arrayDocument->methodName = trim($this->charContainer);
				$this->charContainer = '';
				break;
		}
	}

	function addValue($value) {
		$upper = count($this->lastArray) - 1;
		if($upper > -1) {
			$lastArrayType = $this->lastArrayType[$upper];
			if($lastArrayType == DOM_XMLRPC_TYPE_STRUCT) {
				$currentName = $this->lastStructName[(count($this->lastStructName) - 1)];
				switch($currentName) {
					case DOM_XMLRPC_NODEVALUE_FAULTCODE:
						$this->arrayDocument->faultCode = $value;
						break;
					case DOM_XMLRPC_NODEVALUE_FAULTSTRING:
						$this->arrayDocument->faultString = $value;
						break;
					default:

						$this->lastArray[$upper][$currentName] = $value;
				}
			} else
				if($lastArrayType == DOM_XMLRPC_PHPOBJECT) {
					$currentName = $this->lastStructName[(count($this->lastStructName) - 1)];
					if($currentName == DOM_XMLRPC_PHPOBJECT) {

						call_user_func($this->objectDefinitionHandler,$value);
						$this->lastArray[$upper] = new $value;
					} else {
						if($currentName == DOM_XMLRPC_SERIALIZED) {



							$serialized = &$value->getBinary();
							$this->lastArray[$upper] = &unserialize($serialized);
						} else {

							$myObj = &$this->lastArray[$upper];
							$myObj->$currentName = &$value;
						}
					}
				} else {

					$this->lastArray[$upper][] = &$value;
				}
		} else {

			array_push($this->arrayDocument->params,$value);
		}
		$this->charContainer = '';
	}

}




?>
