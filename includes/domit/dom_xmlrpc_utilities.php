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
class dom_xmlrpc_utilities {
	function stripHeader($myResponse) {
		$body = '';
		$total = strlen($myResponse);
		for($i = 0; $i < $total; $i++) {
			if($myResponse{$i} == '<') {
				$body = substr($myResponse,$i);
				break;
			}
		}
		return $body;
	}

	function getScalarTypeFromValue(&$value) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_constants.php');
		if(is_string($value)) {
			return DOM_XMLRPC_TYPE_STRING;
		} else
			if(is_int($value)) {
				return DOM_XMLRPC_TYPE_INT;
			} else
				if(is_float($value)) {
					return DOM_XMLRPC_TYPE_DOUBLE;
				} else
					if(is_bool($value)) {
						return DOM_XMLRPC_TYPE_BOOLEAN;
					} else
						if(is_object($value)) {
							require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_datetime_iso8601.php');
							require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_base64.php');
							if(get_class($value) == 'dom_xmlrpc_datetime_iso8601') {
								return DOM_XMLRPC_TYPE_DATETIME;
							} else
								if(get_class($value) == 'dom_xmlrpc_base64') {
									return DOM_XMLRPC_TYPE_BASE64;
								}
						}
		return '';
	}

	function getTypeFromValue(&$value) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_constants.php');
		$scalarType = dom_xmlrpc_utilities::getScalarTypeFromValue($value);
		if($scalarType == '') {
			require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_struct.php');
			if(is_array($value)) {
				if(dom_xmlrpc_utilities::isAssociativeArray($value)) {
					return DOM_XMLRPC_TYPE_STRUCT;
				} else {
					return DOM_XMLRPC_TYPE_ARRAY;
				}
			} else
				if(get_class($value) == 'dom_xmlrpc_struct') {
					return DOM_XMLRPC_TYPE_STRUCT;
				} else
					if(is_object($value)) {
						return DOM_XMLRPC_TYPE_STRUCT;
					}
		} else {
			return $scalarType;
		}
	}

	function getScalarValue(&$value,$type) {
		require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_constants.php');
		switch($type) {
			case DOM_XMLRPC_TYPE_BOOLEAN:
				return (($value == true)?'1':'0');
				break;
			case DOM_XMLRPC_TYPE_DATETIME:
				require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_datetime_iso8601.php');
				if(is_object($value) && (get_class($value) == 'dom_xmlrpc_datetime_iso8601')) {
					return ($value->getDateTime_iso());
				}
				break;
			case DOM_XMLRPC_TYPE_BASE64:
				require_once (DOM_XMLRPC_INCLUDE_PATH.'dom_xmlrpc_base64.php');
				if(is_object($value) && (get_class($value) == 'dom_xmlrpc_base64')) {
					return ($value->getEncoded());
				}
				break;
			default:
				return (''.$value);
		}
		return (''.$value);
	}

	function isAssociativeArray(&$myArray) {
		reset($myArray);
		$myKey = key($myArray);
		if(is_string($myKey)) {
			return true;
		}
		return false;
	}

	function getInverseTranslationTable() {
		$trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
		$trans = array_flip($trans);
		$trans['&amp;'] = "'";
		return $trans;
	}

}




?>
