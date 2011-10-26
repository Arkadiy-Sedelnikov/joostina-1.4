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
class dom_xmlrpc_domxml_document {
	var $xmldoc = null;
	function parseXML($xmlText) {

		$xmlText = preg_replace('/>'."[[:space:]]+".'</iu','><',$xmlText);

		$this->xmldoc = domxml_open_mem($xmlText);
		if(is_object($this->xmldoc))
			$success = true;
		else
			$success = false;
		return $success;
	}

	function getDocument() {
		return $this->xmldoc;
	}

	function getMethodType() {
		$root = $this->xmldoc->document_element();
		return $root->node_name();
	}

	function getMethodName() {
		if($this->getMethodType() == DOM_XMLRPC_TYPE_METHODCALL) {
			$node = $this->xmldoc->document_element();
			$childNodes = $node->child_nodes();
			$node = $childNodes[0];
			$node = $node->first_child();
			return $node->node_value();
		}

	}

	function getParams() {
		switch($this->getMethodType()) {
			case DOM_XMLRPC_TYPE_METHODCALL:
				$node = $this->xmldoc->document_element();
				$childNodes = $node->child_nodes();
				return $childNodes[1];
				break;
			case DOM_XMLRPC_TYPE_METHODRESPONSE:
				if(!$this->isFault()) {
					$node = $this->xmldoc->document_element();
					return $node->first_child();
				}
				break;
		}

	}

	function getParam($index) {
		switch($this->getMethodType()) {
			case DOM_XMLRPC_TYPE_METHODCALL:
				$node = $this->xmldoc->document_element();
				$childNodes = $node->child_nodes();
				$node = $childNodes[1];
				$childNodes = $node->child_nodes();
				return $childNodes[$index];
				break;
			case DOM_XMLRPC_TYPE_METHODRESPONSE:
				if(!$this->isFault()) {
					$node = $this->xmldoc->document_element();
					$node = $node->first_child();
					$childNodes = $node->child_nodes();
					return $childNodes[$index];
				}
				break;
		}

	}

	function getParamCount() {
		switch($this->getMethodType()) {
			case DOM_XMLRPC_TYPE_METHODCALL:
				$node = $this->xmldoc->document_element();
				$childNodes = $node->child_nodes();
				$node = $childNodes[1];
				$childNodes = $node->child_nodes();
				return count($childNodes);
				break;
			case DOM_XMLRPC_TYPE_METHODRESPONSE:
				if(!$this->isFault()) {
					$node = $this->xmldoc->document_element();
					$node = $node->first_child();
					$childNodes = $node->child_nodes();
					return count($childNodes);
				}
				break;
		}
		return - 1;

	}

	function isFault() {
		$node = $this->xmldoc->document_element();
		$node = $node->first_child();
		return ($node->node_name() == DOM_XMLRPC_TYPE_FAULT);
	}

	function getFaultCode() {
		if($this->isFault()) {
			$node = $this->xmldoc->document_element();
			$node = $node->first_child();
			$node = $node->first_child();
			$faultStruct = $node->first_child();
			$childNodes = $faultStruct->child_nodes();
			$node = $childNodes[0];
			$childNodes = $node->child_nodes();
			$node = $childNodes[1];
			$node = $node->first_child();
			$node = $node->first_child();
			return ($node->node_value());
		}
	}

	function getFaultString() {
		if($this->isFault()) {
			$node = $this->xmldoc->document_element();
			$node = $node->first_child();
			$node = $node->first_child();
			$faultStruct = $node->first_child();
			$childNodes = $faultStruct->child_nodes();
			$node = $childNodes[1];
			$childNodes = $node->child_nodes();
			$node = $childNodes[1];
			$node = $node->first_child();
			$node = $node->first_child();
			return ($node->nodeValue);
		}
	}

	function getParamType($node) {
		switch($node->node_name()) {
			case DOM_XMLRPC_TYPE_PARAM:
				$node = $node->first_child();
				$node = $node->first_child();
				return $node->node_name();
				break;
			default:

		}
	}

	function toString() {
		if(func_num_args() > 0) {
			$node = func_get_arg(0);
		} else {
			$node = $this->xmldoc->document_element();
		}
		$str = '';
		$str = @$this->xmldoc->dump_node($node);
		if($str == '') {
			$str = @$node->dump_node($node);
		}
		return $str;
	}

	function toNormalizedString() {



		if(func_num_args() > 0) {
			return $this->toString(func_get_arg(0));
		}
		return $this->toString();
	}

}



?>
