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
class dom_xmlrpc_builder {
	var $methodType;
	var $params = "";
	var $objectMarshalling = DOM_XMLRPC_OBJECT_MARSHALLING_ANONYMOUS;
	function setObjectMarshalling($type) {
		$type = strtolower($type);
		switch($type) {
			case DOM_XMLRPC_OBJECT_MARSHALLING_ANONYMOUS:

			case DOM_XMLRPC_OBJECT_MARSHALLING_NAMED:
			case DOM_XMLRPC_OBJECT_MARSHALLING_SERIALIZED:
				$this->objectMarshalling = $type;
				break;
			default:
				XMLRPC_Client_Exception::raiseException(XMLRPC_CLIENT_RESPONSE_TYPE_ERR,('Invalid object marshalling type: '.
					$type));
		}
	}

	function createScalar($value,$type = '') {
		if($type == '') {
			$type = dom_xmlrpc_utilities::getScalarTypeFromValue($value);
		}
		switch($type) {
			case DOM_XMLRPC_TYPE_STRING:
				if($this->methodType == DOM_XMLRPC_TYPE_METHODRESPONSE) {


					$value = htmlentities($value,ENT_QUOTES);
				}
				break;
			case DOM_XMLRPC_TYPE_DOUBLE:

				$value = ''.$value;
				if(strpos($value,'.') === false) {
					$value .= '.0';
				}
				break;
			case DOM_XMLRPC_TYPE_BOOLEAN:
				if(is_bool($value)) {
					$value = ($value?'1':'0');
				}
				break;
			case DOM_XMLRPC_TYPE_BASE64:
				if(is_object($value)) {
					$value = $value->getEncoded();
				}
				break;
			case DOM_XMLRPC_TYPE_DATETIME:
				if(is_object($value)) {
					$value = $value->getDateTime_iso();
				}
				break;
		}
		return ("<value><$type>$value</$type></value>");
	}

	function addScalar($value,$type = '') {
		$this->params .= "\n\t\t<param>".$this->createScalar($value,$type).'</param>';
	}

	function createArray(&$myArray) {
		$data = '<value><array><data>';
		foreach($myArray as $key => $value) {
			$currDataItem = &$myArray[$key];
			$currType = dom_xmlrpc_utilities::getTypeFromValue($currDataItem);
			$data .= $this->create($currDataItem,$currType);
		}
		$data .= '</data></array></value>';
		return $data;
	}

	function addArray($myArray) {
		$this->addArrayByRef($myArray);
	}

	function addArrayByRef(&$myArray) {

		$this->params .= "\n\t\t<param>".$this->createArray($myArray).'</param>';
	}

	function createObject(&$myObject) {
		require_once ('dom_xmlrpc_object.php');
		if(get_class($myObject) == 'dom_xmlrpc_object') {

			$myObject = &$myObject->getObject();

		}
		switch($this->objectMarshalling) {
			case DOM_XMLRPC_OBJECT_MARSHALLING_ANONYMOUS:


				$data = '<value><struct>';
				foreach($myObject as $key => $value) {
					$currValue = &$myObject->$key;
					$currType = dom_xmlrpc_utilities::getTypeFromValue($currValue);
					$data .= $this->createMember($key,$currValue,$currType);
				}
				$data .= '</struct></value>';
				break;
			case DOM_XMLRPC_OBJECT_MARSHALLING_NAMED:





				$data = '<value><struct>';
				$data .= $this->createMember(DOM_XMLRPC_PHPOBJECT,get_class($myObject),
					DOM_XMLRPC_TYPE_STRING);
				foreach($myObject as $key => $value) {
					$currValue = &$myObject->$key;
					$currType = dom_xmlrpc_utilities::getTypeFromValue($currValue);
					$data .= $this->createMember($key,$currValue,$currType);
				}
				$data .= '</struct></value>';
				break;
			case DOM_XMLRPC_OBJECT_MARSHALLING_SERIALIZED:


				require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_base64.php');
				$data = '<value><struct>';
				$data .= $this->createMember(DOM_XMLRPC_PHPOBJECT,get_class($myObject),
					DOM_XMLRPC_TYPE_STRING);
				$serialized = &serialize($myObject);
				$currValue = &$this->createBase64($serialized);
				$data .= $this->createMember(DOM_XMLRPC_SERIALIZED,$currValue,
					DOM_XMLRPC_TYPE_BASE64);
				$data .= '</struct></value>';
				break;
		}

		return $data;
	}

	function addObject($myObject) {
		$this->addObjectByRef($myObject);
	}

	function addObjectByRef(&$myObject) {

		$this->params .= "\n\t\t<param>".$this->createObject($myObject).'</param>';
	}

	function createStruct(&$myStruct) {
		require_once ('dom_xmlrpc_object.php');
		$className = get_class($myStruct);

		if($className == 'dom_xmlrpc_object') {
			$myObject = &$myStruct->getObject();

			return $this->createObject($myObject);
		} else {


			if($className == 'dom_xmlrpc_struct') {
				$myStruct = &$myStruct->getStruct();

			}
			$isArrayNotObj = is_array($myStruct);

			if($isArrayNotObj && ($this->objectMarshalling !=
				DOM_XMLRPC_OBJECT_MARSHALLING_ANONYMOUS)) {
				return $this->createObject($myStruct);
			} else {
				$data = '<value><struct>';
				foreach($myStruct as $key => $value) {
					$isArrayNotObj?($currValue = &$myStruct[$key]):($currValue = &$value);
					$currType = dom_xmlrpc_utilities::getTypeFromValue($currValue);
					$data .= $this->createMember($key,$currValue,$currType);
				}
				$data .= '</struct></value>';
				return $data;
			}
		}
	}

	function addStruct($myStruct) {
		$this->addStructByRef($myStruct);
	}

	function addStructByRef(&$myStruct) {

		$this->params .= "\n\t\t<param>".$this->createStruct($myStruct).'</param>';
	}

	function createMember($name,&$value,$type) {
		$data = '<member><name>'.$name.'</name>';
		if($type == '') {
			$type = dom_xmlrpc_utilities::getScalarTypeFromValue($value);
		}
		$data .= $this->create($value,$type).'</member>';
		return $data;
	}

	function create(&$value,$type = '') {
		$data = '';
		if($type == '') {
			require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_utilities.php');
			$type = dom_xmlrpc_utilities::getTypeFromValue($value);
		}
		switch($type) {
			case DOM_XMLRPC_TYPE_STRING:
			case DOM_XMLRPC_TYPE_INT:
			case DOM_XMLRPC_TYPE_I4:
			case DOM_XMLRPC_TYPE_DOUBLE:
			case DOM_XMLRPC_TYPE_BOOLEAN:
			case DOM_XMLRPC_TYPE_BASE64:
			case DOM_XMLRPC_TYPE_DATETIME:
				$data .= $this->createScalar($value,$type);
				break;
			case DOM_XMLRPC_TYPE_STRUCT:
				$data .= $this->createStruct($value,$type);
				break;
			case DOM_XMLRPC_TYPE_ARRAY:
				$data .= $this->createArray($value,$type);
				break;
		}
		return $data;
	}

	function add($value,$type = '') {
		$this->addByRef($value,$type);
	}

	function addByRef(&$value,$type = '') {

		$this->params .= "\n\t\t<param>".$this->create($value,$type).'</param>';
	}

	function addList() {
		$total = func_num_args();
		for($i = 0; $i < $total; $i++) {
			$this->add(func_get_arg($i));
		}
	}

	function &createBase64($binaryData) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_base64.php');
		$base64 = new dom_xmlrpc_base64();
		$base64->fromBinary($binaryData);
		return $base64;
	}

	function &createBase64FromFile($fileName) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_base64.php');
		$base64 = new dom_xmlrpc_base64();
		$base64->fromFile($fileName);
		return $base64;
	}

	function &createDateTimeISO($time) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_datetime_iso8601.php');
		$isoDateTime = new dom_xmlrpc_datetime_iso8601($time);
		return $isoDateTime;
	}

}



?>
