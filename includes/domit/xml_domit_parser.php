<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * dom_xmlrpc_array_document wraps a PHP array with the DOM XML-RPC API
 * @package dom-xmlrpc
 * @copyright (C) 2004 John Heinstein. All rights reserved
 * @license http://www.gnu.org/copyleft/lesser.html LGPL License
 * @author John Heinstein <johnkarl@nbnet.nb.ca>
 * @link http://www.engageinteractive.com/dom_xmlrpc/ DOM XML-RPC Home Page
 * DOM XML-RPC is Free Software
 **/

defined('_JLINDEX') or die();
if(!defined('DOMIT_INCLUDE_PATH')){
	define('DOMIT_INCLUDE_PATH', (dirname(__file__) . "/"));
}
define('DOMIT_VERSION', '1.01');
define('DOMIT_XML_NAMESPACE', 'http://www.w3.org/xml/1998/namespace');
define('DOMIT_XMLNS_NAMESPACE', 'http://www.w3.org/2000/xmlns/');
$GLOBALS['DOMIT_defined_entities_flip'] = array();
require_once (DOMIT_INCLUDE_PATH . 'xml_domit_shared.php');
class DOMIT_Node{
	var $nodeName = null;
	var $nodeValue = null;
	var $nodeType = null;
	var $parentNode = null;
	var $childNodes = null;
	var $firstChild = null;
	var $lastChild = null;
	var $previousSibling = null;
	var $nextSibling = null;
	var $attributes = null;
	var $ownerDocument = null;
	var $namespaceURI = null;
	var $prefix = null;
	var $localName = null;
	var $uid;
	var $childCount = 0;

	function DOMIT_Node(){
		DOMIT_DOMException::raiseException(DOMIT_ABSTRACT_CLASS_INSTANTIATION_ERR,
			'Cannot instantiate abstract class DOMIT_Node');
	}

	function _constructor(){
		global $uidFactory;
		$this->uid = $uidFactory->generateUID();
	}

	function &appendChild(&$child){
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Method appendChild cannot be called by class ' . get_class($this)));
	}

	function &insertBefore(&$newChild, &$refChild){
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Method insertBefore cannot be called by class ' . get_class($this)));
	}

	function &replaceChild(&$newChild, &$oldChild){
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Method replaceChild cannot be called by class ' . get_class($this)));
	}

	function &removeChild(&$oldChild){
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Method removeChild cannot be called by class ' . get_class($this)));
	}

	function getChildNodeIndex(&$arr, &$child){
		$index = -1;
		$total = count($arr);
		for($i = 0; $i < $total; $i++){
			if($child->uid == $arr[$i]->uid){
				$index = $i;
				break;
			}
		}
		return $index;
	}

	function hasChildNodes(){
		return ($this->childCount > 0);
	}

	function hasAttributes(){

		return false;
	}

	function normalize(){
		if(($this->nodeType == DOMIT_DOCUMENT_NODE) && ($this->documentElement != null)){
			$this->documentElement->normalize();
		}
	}

	function &cloneNode(){
		DOMIT_DOMException::raiseException(DOMIT_ABSTRACT_METHOD_INVOCATION_ERR, 'Cannot invoke abstract method DOMIT_Node->cloneNode($deep). Must provide an overridden method in your subclass.');
	}

	function getNamedElements(&$nodeList, $tagName){
		return;
	}

	function setOwnerDocument(&$rootNode){
		if($rootNode->ownerDocument == null){
			unset($this->ownerDocument);
			$this->ownerDocument = null;
		} else{
			$this->ownerDocument = &$rootNode->ownerDocument;
		}
		$total = $this->childCount;
		for($i = 0; $i < $total; $i++){
			$this->childNodes[$i]->setOwnerDocument($rootNode);
		}
	}

	function &nvl(&$value, $default){
		if(is_null($value))
			return $default;
		return $value;
	}

	function &getElementsByPath($pattern, $nodeIndex = 0){
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Method getElementsByPath cannot be called by class ' . get_class($this)));
	}

	function &getElementsByAttributePath($pattern, $nodeIndex = 0){
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Method getElementsByAttributePath cannot be called by class ' . get_class($this)));
	}

	function getTypedNodes(&$nodeList, $type){
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Method getTypedNodes cannot be called by class ' . get_class($this)));
	}

	function getValuedNodes(&$nodeList, $value){
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Method getValuedNodes cannot be called by class ' . get_class($this)));
	}

	function getText(){
		return $this->nodeValue;
	}

	function isSupported($feature, $version = null){


		if(($version == '1.0') || ($version == '2.0') || ($version == null)){
			if(strtoupper($feature) == 'XML'){
				return true;
			}
		}
		return false;
	}

	function forHTML($str, $doPrint = false){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_utilities.php');
		return DOMIT_Utilities::forHTML($str, $doPrint);
	}

	function toArray(){
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Method toArray cannot be called by class ' .
			get_class($this)));
	}

	function onLoad(){


	}

	function clearReferences(){
		if($this->previousSibling != null){
			unset($this->previousSibling);
			$this->previousSibling = null;
		}
		if($this->nextSibling != null){
			unset($this->nextSibling);
			$this->nextSibling = null;
		}
		if($this->parentNode != null){
			unset($this->parentNode);
			$this->parentNode = null;
		}
	}

	function delete($node){
		if($this->parentNode != null){
			$this->parentNode->removeChild($node);
		}
	}

	function toNormalizedString($htmlSafe = false, $subEntities = false){

		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_utilities.php');
		global $DOMIT_defined_entities_flip;
		$result = DOMIT_Utilities::toNormalizedString($this, $subEntities, $DOMIT_defined_entities_flip);
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_ChildNodes_Interface extends DOMIT_Node{
	function DOMIT_ChildNodes_Interface(){
		DOMIT_DOMException::raiseException(DOMIT_ABSTRACT_CLASS_INSTANTIATION_ERR,
			'Cannot instantiate abstract class DOMIT_ChildNodes_Interface');
	}

	function &appendChild(&$child){
		if($child->nodeType == DOMIT_ATTRIBUTE_NODE){
			DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot add a node of type ' .
				get_class($child) . ' using appendChild'));
		} else
			if($child->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE){
				$total = $child->childCount;
				for($i = 0; $i < $total; $i++){
					$currChild = &$child->childNodes[$i];
					$this->appendChild($currChild);
				}
			} else{
				if(!($this->hasChildNodes())){
					$this->childNodes[0] = &$child;
					$this->firstChild = &$child;
				} else{

					$index = $this->getChildNodeIndex($this->childNodes, $child);
					if($index != -1){
						$this->removeChild($child);
					}

					$numNodes = $this->childCount;

					if($numNodes > 0){
						$prevSibling = &$this->childNodes[($numNodes - 1)];
					}
					$this->childNodes[$numNodes] = &$child;


					if(isset($prevSibling)){
						$child->previousSibling = &$prevSibling;
						$prevSibling->nextSibling = &$child;
					} else{
						unset($child->previousSibling);
						$child->previousSibling = null;
						$this->firstChild = &$child;
					}
				}
			}
		$this->lastChild = &$child;
		$child->parentNode = &$this;
		unset($child->nextSibling);
		$child->nextSibling = null;
		$child->setOwnerDocument($this);
		$this->childCount++;
		return $child;
	}

	function &insertBefore(&$newChild, &$refChild){
		if($newChild->nodeType == DOMIT_ATTRIBUTE_NODE){
			DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot add a node of type ' .
				get_class($newChild) . ' using insertBefore'));
		}
		if(($refChild->nodeType == DOMIT_DOCUMENT_NODE) ||
			($refChild->parentNode == null)
		){
			DOMIT_DOMException::raiseException(DOMIT_NOT_FOUND_ERR,
				'Reference child not present in the child nodes list.');
		}


		if($refChild->uid == $newChild->uid){
			return $newChild;
		}


		if($newChild->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE){
			$total = $newChild->childCount;
			for($i = 0; $i < $total; $i++){
				$currChild = &$newChild->childNodes[$i];
				$this->insertBefore($currChild, $refChild);
			}
			return $newChild;
		}

		$index = $this->getChildNodeIndex($this->childNodes, $newChild);
		if($index != -1){
			$this->removeChild($newChild);
		}

		$index = $this->getChildNodeIndex($this->childNodes, $refChild);
		if($index != -1){

			if($refChild->previousSibling != null){
				$refChild->previousSibling->nextSibling = &$newChild;
				$newChild->previousSibling = &$refChild->previousSibling;
			} else{
				$this->firstChild = &$newChild;
				if($newChild->previousSibling != null){
					unset($newChild->previousSibling);
					$newChild->previousSibling = null;
				}
			}
			$newChild->parentNode = &$refChild->parentNode;
			$newChild->nextSibling = &$refChild;
			$refChild->previousSibling = &$newChild;

			$i = $this->childCount;
			while($i >= 0){
				if($i > $index){
					$this->childNodes[$i] = &$this->childNodes[($i - 1)];
				} else
					if($i == $index){
						$this->childNodes[$i] = &$newChild;
					}
				$i--;
			}
			$this->childCount++;
		} else{
			$this->appendChild($newChild);
		}
		$newChild->setOwnerDocument($this);
		return $newChild;
	}

	function &replaceChild(&$newChild, &$oldChild){
		if($newChild->nodeType == DOMIT_ATTRIBUTE_NODE){
			DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot add a node of type ' .
				get_class($newChild) . ' using replaceChild'));
		} else
			if($newChild->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE){


				$total = $newChild->childCount;
				if($total > 0){
					$newRef = &$newChild->lastChild;
					$this->replaceChild($newRef, $oldChild);
					for($i = 0; $i < ($total - 1); $i++){
						$currChild = &$newChild->childNodes[$i];
						$this->insertBefore($currChild, $newRef);
					}
				}
				return $newChild;
			} else{
				if($this->hasChildNodes()){

					$index = $this->getChildNodeIndex($this->childNodes, $newChild);
					if($index != -1){
						$this->removeChild($newChild);
					}

					$index = $this->getChildNodeIndex($this->childNodes, $oldChild);
					if($index != -1){
						$newChild->ownerDocument = &$oldChild->ownerDocument;
						$newChild->parentNode = &$oldChild->parentNode;

						if($oldChild->previousSibling == null){
							unset($newChild->previousSibling);
							$newChild->previousSibling = null;
						} else{
							$oldChild->previousSibling->nextSibling = &$newChild;
							$newChild->previousSibling = &$oldChild->previousSibling;
						}
						if($oldChild->nextSibling == null){
							unset($newChild->nextSibling);
							$newChild->nextSibling = null;
						} else{
							$oldChild->nextSibling->previousSibling = &$newChild;
							$newChild->nextSibling = &$oldChild->nextSibling;
						}
						$this->childNodes[$index] = &$newChild;
						if($index == 0)
							$this->firstChild = &$newChild;
						if($index == ($this->childCount - 1))
							$this->lastChild = &$newChild;
						$newChild->setOwnerDocument($this);
						return $newChild;
					}
				}
				DOMIT_DOMException::raiseException(DOMIT_NOT_FOUND_ERR, ('Reference node for replaceChild not found.'));
			}
	}

	function &removeChild(&$oldChild){
		if($this->hasChildNodes()){

			$index = $this->getChildNodeIndex($this->childNodes, $oldChild);
			if($index != -1){

				if(($oldChild->previousSibling != null) && ($oldChild->nextSibling != null)){
					$oldChild->previousSibling->nextSibling = &$oldChild->nextSibling;
					$oldChild->nextSibling->previousSibling = &$oldChild->previousSibling;
				} else
					if(($oldChild->previousSibling != null) && ($oldChild->nextSibling == null)){
						$this->lastChild = &$oldChild->previousSibling;
						unset($oldChild->previousSibling->nextSibling);
						$oldChild->previousSibling->nextSibling = null;
					} else
						if(($oldChild->previousSibling == null) && ($oldChild->nextSibling != null)){
							unset($oldChild->nextSibling->previousSibling);
							$oldChild->nextSibling->previousSibling = null;
							$this->firstChild = &$oldChild->nextSibling;
						} else
							if(($oldChild->previousSibling == null) && ($oldChild->nextSibling == null)){
								unset($this->firstChild);
								$this->firstChild = null;
								unset($this->lastChild);
								$this->lastChild = null;
							}
				$total = $this->childCount;

				for($i = 0; $i < $total; $i++){
					if($i == ($total - 1)){
						array_splice($this->childNodes, $i, 1);
					} else
						if($i >= $index){
							$this->childNodes[$i] = &$this->childNodes[($i + 1)];
						}
				}
				$this->childCount--;
				$oldChild->clearReferences();
				return $oldChild;
			}
		}
		DOMIT_DOMException::raiseException(DOMIT_NOT_FOUND_ERR, ('Target node for removeChild not found.'));
	}

	function &getElementsByAttribute($attrName = 'id', $attrValue = '', $returnFirstFoundNode = false,
		$treatUIDAsAttribute = false){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_nodemaps.php');
		$nodelist = new DOMIT_NodeList();
		switch($this->nodeType){
			case DOMIT_ELEMENT_NODE:
				$this->_getElementsByAttribute($nodelist, $attrName, $attrValue, $returnFirstFoundNode,
					$treatUIDAsAttribute);
				break;
			case DOMIT_DOCUMENT_NODE:
				if($this->documentElement != null){
					$this->documentElement->_getElementsByAttribute($nodelist, $attrName, $attrValue,
						$returnFirstFoundNode, $treatUIDAsAttribute);
				}
				break;
		}
		if($returnFirstFoundNode){
			if($nodelist->getLength() > 0){
				return $nodelist->item(0);
			} else{
				$null = null;
				return $null;
			}
		} else{
			return $nodelist;
		}
	}

	function _getElementsByAttribute(&$nodelist, $attrName, $attrValue, $returnFirstFoundNode,
		$treatUIDAsAttribute, $foundNode = false){
		if(!($foundNode && $returnFirstFoundNode)){
			if(($this->getAttribute($attrName) == $attrValue) || ($treatUIDAsAttribute && ($attrName ==
				'uid') && ($this->uid == $attrValue))
			){
				$nodelist->appendNode($this);
				$foundNode = true;
				if($returnFirstFoundNode)
					return;
			}
			$total = $this->childCount;
			for($i = 0; $i < $total; $i++){
				$currNode = &$this->childNodes[$i];
				if($currNode->nodeType == DOMIT_ELEMENT_NODE){
					$currNode->_getElementsByAttribute($nodelist, $attrName, $attrValue, $returnFirstFoundNode,
						$treatUIDAsAttribute, $foundNode);
				}
			}
		}
	}

	function &selectNodes($pattern, $nodeIndex = 0){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_xpath.php');
		$xpParser = new DOMIT_XPath();
		return $xpParser->parsePattern($this, $pattern, $nodeIndex);
	}

	function &childNodesAsNodeList(){
		require_once ('xml_domit_nodemaps.php');
		$myNodeList = new DOMIT_NodeList();
		$total = $this->childCount;
		for($i = 0; $i < $total; $i++){
			$myNodeList->appendNode($this->childNodes[$i]);
		}
		return $myNodeList;
	}

}

class DOMIT_Document extends DOMIT_ChildNodes_Interface{
	var $xmlDeclaration;
	var $doctype;
	var $documentElement;
	var $parser;
	var $implementation;
	var $isModified;
	var $preserveWhitespace = false;
	var $definedEntities = array();
	var $doResolveErrors = false;
	var $doExpandEmptyElementTags = false;
	var $expandEmptyElementExceptions = array();
	var $isNamespaceAware = false;
	var $errorCode = 0;
	var $errorString = '';
	var $httpConnection = null;
	var $doUseHTTPClient = false;
	var $namespaceURIMap = array();

	function DOMIT_Document(){
		$this->_constructor();
		$this->xmlDeclaration = null;
		$this->doctype = null;
		$this->documentElement = null;
		$this->nodeType = DOMIT_DOCUMENT_NODE;
		$this->nodeName = '#document';
		$this->ownerDocument = &$this;
		$this->parser = '';
		$this->implementation = new DOMIT_DOMImplementation();
	}

	function resolveErrors($truthVal){
		$this->doResolveErrors = $truthVal;
	}

	function setNamespaceAwareness($truthVal){
		$this->isNamespaceAware = $truthVal;
	}

	function preserveWhitespace($truthVal){
		$this->preserveWhitespace = $truthVal;
	}

	function setConnection($host, $path = '/', $port = 80, $timeout = 0, $user = null, $password = null){
		require_once (DOMIT_INCLUDE_PATH . 'php_http_client_generic.php');
		$this->httpConnection = new php_http_client_generic($host, $path, $port, $timeout,
			$user, $password);
	}

	function setAuthorization($user, $password){
		$this->httpConnection->setAuthorization($user, $password);
	}

	function setProxyConnection($host, $path = '/', $port = 80, $timeout = 0, $user = null,
		$password = null){
		require_once (DOMIT_INCLUDE_PATH . 'php_http_proxy.php');
		$this->httpConnection = new php_http_proxy($host, $path, $port, $timeout, $user, $password);
	}

	function setProxyAuthorization($user, $password){
		$this->httpConnection->setProxyAuthorization($user, $password);
	}

	function useHTTPClient($truthVal){
		$this->doUseHTTPClient = $truthVal;
	}

	function getErrorCode(){
		return $this->errorCode;
	}

	function getErrorString(){
		return $this->errorString;
	}

	function expandEmptyElementTags($truthVal, $expandEmptyElementExceptions = false){
		$this->doExpandEmptyElementTags = $truthVal;
		if(is_array($expandEmptyElementExceptions)){
			$this->expandEmptyElementExceptions = $expandEmptyElementExceptions;
		}
	}

	function &setDocumentElement(&$node){
		if($node->nodeType == DOMIT_ELEMENT_NODE){
			if($this->documentElement == null){
				parent::appendChild($node);
			} else{
				parent::replaceChild($node, $this->documentElement);
			}
			$this->documentElement = &$node;
		} else{
			DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot add a node of type ' .
				get_class($node) . ' as a Document Element.'));
		}
		return $node;
	}

	function &importNode(&$importedNode, $deep = true){
		$parentNode = null;
		return $this->_importNode($parentNode, $importedNode, $deep);
	}

	function &_importNode($parentNode, &$sourceNode, $deep){
		$hasChildren = false;
		switch($sourceNode->nodeType){
			case DOMIT_ELEMENT_NODE:
				$hasChildren = true;
				if($this->isNamespaceAware){
					$importedNode = &$this->createElementNS($sourceNode->namespaceURI, ($sourceNode->prefix .
						":" . $sourceNode->localName));
				} else{
					$importedNode = &$this->createElement($sourceNode->nodeName);
				}
				$attrNodes = &$sourceNode->attributes;
				$total = $attrNodes->getLength();
				for($i = 0; $i < $total; $i++){
					$attrNode = &$attrNodes->item($i);
					if($this->isNamespaceAware){
						$importedNode->setAttributeNS($attrNode->namespaceURI, ($attrNode->prefix . ":" . $attrNode->localName),
							$attrNode->nodeValue);
					} else{
						$importedNode->setAttribute($attrNode->nodeName, $attrNode->nodeValue);
					}
				}
				break;
			case DOMIT_ATTRIBUTE_NODE:
				if($this->isNamespaceAware){
					$importedNode = &$this->createAttributeNS($sourceNode->namespaceURI, ($sourceNode->prefix .
						":" . $sourceNode->localName));
				} else{
					$importedNode = &$this->createAttribute($sourceNode->nodeValue);
				}
				break;
			case DOMIT_TEXT_NODE:
				$importedNode = &$this->createTextNode($sourceNode->nodeValue);
				break;
			case DOMIT_CDATA_SECTION_NODE:
				$importedNode = &$this->createCDATASection($sourceNode->nodeValue);
				break;
			case DOMIT_COMMENT_NODE:
				$importedNode = &$this->createComment($sourceNode->nodeValue);
				break;
			case DOMIT_DOCUMENT_FRAGMENT_NODE:
				$hasChildren = true;
				$importedNode = &$this->createDocumentFragment();
				break;
			case DOMIT_PROCESSING_INSTRUCTION_NODE:
				$importedNode = &$this->createProcessingInstruction($sourceNode->nodeName, $sourceNode->nodeValue);
				break;
			case DOMIT_DOCUMENT_NODE:
			case DOMIT_DOCUMENT_TYPE_NODE:
				DOMIT_DOMException::raiseException(DOMIT_NOT_SUPPORTED_ERR, ('Method importNode cannot be called by class ' .
					get_class($this)));
				break;
		}
		if($hasChildren && $deep){
			$total = $sourceNode->childCount;
			for($i = 0; $i < $total; $i++){
				$importedNode->appendChild($this->_importNode($importedNode, $sourceNode->childNodes[$i],
					$deep));
			}
		}
		return $importedNode;
	}

	function &appendChild(&$node){
		switch($node->nodeType){
			case DOMIT_ELEMENT_NODE:
				if($this->documentElement == null){
					parent::appendChild($node);
					$this->setDocumentElement($node);
				} else{

					DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot have more than one root node (documentElement) in a DOMIT_Document.'));
				}
				break;
			case DOMIT_PROCESSING_INSTRUCTION_NODE:
			case DOMIT_COMMENT_NODE:
				parent::appendChild($node);
				break;
			case DOMIT_DOCUMENT_TYPE_NODE:
				if($this->doctype == null){
					parent::appendChild($node);
				} else{
					DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot have more than one doctype node in a DOMIT_Document.'));
				}
				break;
			case DOMIT_DOCUMENT_FRAGMENT_NODE:
				$total = $node->childCount;
				for($i = 0; $i < $total; $i++){
					$currChild = &$node->childNodes[$i];
					$this->appendChild($currChild);
				}
				break;
			default:
				DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot add a node of type ' .
					get_class($node) . ' to a DOMIT_Document.'));
		}
		return $node;
	}

	function &replaceChild(&$newChild, &$oldChild){
		if($this->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE){
			$total = $newChild->childCount;
			if($total > 0){
				$newRef = &$newChild->lastChild;
				$this->replaceChild($newRef, $oldChild);
				for($i = 0; $i < ($total - 1); $i++){
					$currChild = &$newChild->childNodes[$i];
					parent::insertBefore($currChild, $newRef);
				}
			}
		} else{
			if(($this->documentElement != null) && ($oldChild->uid == $this->documentElement->uid)){
				if($newChild->nodeType == DOMIT_ELEMENT_NODE){

					$this->setDocumentElement($newChild);
				} else{
					DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot replace Document Element with a node of class ' .
						get_class($newChild)));
				}
			} else{
				switch($newChild->nodeType){
					case DOMIT_ELEMENT_NODE:
						if($this->documentElement != null){
							DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot have more than one root node (documentElement) in a DOMIT_Document.'));
						} else{
							parent::replaceChild($newChild, $oldChild);
						}
						break;
					case DOMIT_PROCESSING_INSTRUCTION_NODE:
					case DOMIT_COMMENT_NODE:
						parent::replaceChild($newChild, $oldChild);
						break;
					case DOMIT_DOCUMENT_TYPE_NODE:
						if($this->doctype != null){
							if($this->doctype->uid == $oldChild->uid){
								parent::replaceChild($newChild, $oldChild);
							} else{
								DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot have more than one doctype node in a DOMIT_Document.'));
							}
						} else{
							parent::replaceChild($newChild, $oldChild);
						}
						break;
					default:
						DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Nodes of class ' .
							get_class($newChild) . ' cannot be children of a DOMIT_Document.'));
				}
			}
		}
		return $newChild;
	}

	function &insertBefore(&$newChild, &$refChild){
		$type = $newChild->nodeType;
		if($this->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE){
			$total = $newChild->childCount;
			for($i = 0; $i < $total; $i++){
				$currChild = &$newChild->childNodes[$i];
				$this->insertBefore($currChild, $refChild);
			}
		} else
			if($type == DOMIT_ELEMENT_NODE){
				if($this->documentElement == null){
					parent::insertBefore($newChild, $refChild);
					$this->setDocumentElement($newChild);
				} else{

					DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot have more than one root node (documentElement) in a DOMIT_Document.'));
				}
			} else
				if($type == DOMIT_PROCESSING_INSTRUCTION_NODE){
					parent::insertBefore($newChild, $refChild);
				} else
					if($type == DOMIT_DOCUMENT_TYPE_NODE){
						if($this->doctype == null){
							parent::insertBefore($newChild, $refChild);
						} else{
							DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot have more than one doctype node in a DOMIT_Document.'));
						}
					} else{
						DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, ('Cannot insert a node of type ' .
							get_class($newChild) . ' to a DOMIT_Document.'));
					}
		return $newChild;
	}

	function &removeChild(&$oldChild){
		if($this->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE){
			$total = $oldChild->childCount;
			for($i = 0; $i < $total; $i++){
				$currChild = &$oldChild->childNodes[$i];
				$this->removeChild($currChild);
			}
		} else{
			if(($this->documentElement != null) && ($oldChild->uid == $this->documentElement->uid)){
				parent::removeChild($oldChild);
				$this->documentElement = null;
			} else{
				parent::removeChild($oldChild);
			}
		}
		$oldChild->clearReferences();
		return $oldChild;
	}

	function &createDocumentFragment(){
		$node = new DOMIT_DocumentFragment();
		$node->ownerDocument = &$this;
		return $node;
	}

	function &createAttribute($name){
		$node = new DOMIT_Attr($name);
		return $node;
	}

	function &createAttributeNS($namespaceURI, $qualifiedName){
		$node = new DOMIT_Attr($qualifiedName);
		$node->namespaceURI = $namespaceURI;
		$colonIndex = strpos($qualifiedName, ":");
		if($colonIndex !== false){
			$node->prefix = substr($qualifiedName, 0, $colonIndex);
			$node->localName = substr($qualifiedName, ($colonIndex + 1));
		} else{
			$node->prefix = '';
			$node->localName = $qualifiedName;
		}
		return $node;
	}

	function &createElement($tagName){
		$node = new DOMIT_Element($tagName);
		$node->ownerDocument = &$this;
		return $node;
	}

	function &createElementNS($namespaceURI, $qualifiedName){
		$node = new DOMIT_Element($qualifiedName);
		$colonIndex = strpos($qualifiedName, ":");
		if($colonIndex !== false){
			$node->prefix = substr($qualifiedName, 0, $colonIndex);
			$node->localName = substr($qualifiedName, ($colonIndex + 1));
		} else{
			$node->prefix = '';
			$node->localName = $qualifiedName;
		}
		$node->namespaceURI = $namespaceURI;
		$node->ownerDocument = &$this;
		return $node;
	}

	function &createTextNode($data){
		$node = new DOMIT_TextNode($data);
		$node->ownerDocument = &$this;
		return $node;
	}

	function &createCDATASection($data){
		$node = new DOMIT_CDATASection($data);
		$node->ownerDocument = &$this;
		return $node;
	}

	function &createComment($text){
		$node = new DOMIT_Comment($text);
		$node->ownerDocument = &$this;
		return $node;
	}

	function &createProcessingInstruction($target, $data){
		$node = new DOMIT_ProcessingInstruction($target, $data);
		$node->ownerDocument = &$this;
		return $node;
	}

	function &getElementsByTagName($tagName){
		$nodeList = new DOMIT_NodeList();
		if($this->documentElement != null){
			$this->documentElement->getNamedElements($nodeList, $tagName);
		}
		return $nodeList;
	}

	function &getElementsByTagNameNS($namespaceURI, $localName){
		$nodeList = new DOMIT_NodeList();
		if($this->documentElement != null){
			$this->documentElement->getNamedElementsNS($nodeList, $namespaceURI, $localName);
		}
		return $nodeList;
	}

	function &getElementByID($elementID, $isStrict = true){
		if($this->isNamespaceAware){
			if($this->documentElement != null){
				$targetAttrNode = &$this->documentElement->_getElementByID($elementID, $isStrict);
				return $targetAttrNode->ownerElement;
			}
			$null = null;
			return $null;
		} else{
			DOMIT_DOMException::raiseException(DOMIT_INVALID_ACCESS_ERR, 'Namespace awareness must be enabled to use method getElementByID');
		}
	}

	function &getElementsByPath($pattern, $nodeIndex = 0){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_getelementsbypath.php');
		$gebp = new DOMIT_GetElementsByPath();
		$myResponse = &$gebp->parsePattern($this, $pattern, $nodeIndex);
		return $myResponse;
	}

	function &getElementsByAttributePath($pattern, $nodeIndex = 0){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_getelementsbypath.php');
		$gabp = new DOMIT_GetElementsByAttributePath();
		$myResponse = &$gabp->parsePattern($this, $pattern, $nodeIndex);
		return $myResponse;
	}

	function &getNodesByNodeType($type, &$contextNode){
		$nodeList = new DOMIT_NodeList();
		if(($type == DOMIT_DOCUMENT_NODE) || ($contextNode->nodeType ==
			DOMIT_DOCUMENT_NODE)
		){
			$nodeList->appendNode($this);
		} else
			if($contextNode->nodeType == DOMIT_ELEMENT_NODE){
				$contextNode->getTypedNodes($nodeList, $type);
			} else
				if($contextNode->uid == $this->uid){
					if($this->documentElement != null){
						if($type == DOMIT_ELEMENT_NODE){
							$nodeList->appendNode($this->documentElement);
						}
						$this->documentElement->getTypedNodes($nodeList, $type);
					}
				}
		return $nodeList;
	}

	function &getNodesByNodeValue($value, &$contextNode){
		$nodeList = new DOMIT_NodeList();
		if($contextNode->uid == $this->uid){
			if($this->nodeValue == $value){
				$nodeList->appendNode($this);
			}
		}
		if($this->documentElement != null){
			$this->documentElement->getValuedNodes($nodeList, $value);
		}
		return $nodeList;
	}

	function parseXML($xmlText, $useSAXY = true, $preserveCDATA = true, $fireLoadEvent = false){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_utilities.php');
		if($this->doResolveErrors){
			require_once (DOMIT_INCLUDE_PATH . 'xml_domit_doctor.php');
			$xmlText = DOMIT_Doctor::fixAmpersands($xmlText);
		}
		if(DOMIT_Utilities::validateXML($xmlText)){
			$domParser = new DOMIT_Parser();
			if($useSAXY || (!function_exists('xml_parser_create'))){

				$this->parser = 'SAXY';
				$success = $domParser->parseSAXY($this, $xmlText, $preserveCDATA, $this->definedEntities);
			} else{

				$this->parser = 'EXPAT';
				$success = $domParser->parse($this, $xmlText, $preserveCDATA);
			}
			if($fireLoadEvent && ($this->documentElement != null))
				$this->load($this->documentElement);
			return $success;
		} else{
			return false;
		}
	}

	function loadXML($filename, $useSAXY = true, $preserveCDATA = true, $fireLoadEvent = false){
		$xmlText = $this->getTextFromFile($filename);
		return $this->parseXML($xmlText, true, $preserveCDATA, $fireLoadEvent);
	}

	function establishConnection($url){
		require_once (DOMIT_INCLUDE_PATH . 'php_http_client_generic.php');
		$host = php_http_connection::formatHost($url);
		$host = substr($host, 0, strpos($host, '/'));
		$this->setConnection($host);
	}

	function getTextFromFile($filename){
		if($this->doUseHTTPClient && (substr($filename, 0, 5) == 'http:')){
			$this->establishConnection($filename);
		}
		if($this->httpConnection != null){
			$response = &$this->httpConnection->get($filename);
			$this->httpConnection->disconnect();
			return $response->getResponse();
		} else
			if(function_exists('file_get_contents')){

				return file_get_contents($filename);

			} else{
				require_once (DOMIT_INCLUDE_PATH . 'php_file_utilities.php');
				$fileContents = &php_file_utilities::getDataFromFile($filename, 'r');
				return $fileContents;
			}
		return '';
	}

	function saveXML($filename, $normalized = false){
		if($normalized){
			$stringRep = $this->toNormalizedString(false, true);
		} else{
			$stringRep = $this->toString(false, true);
		}
		return $this->saveTextToFile($filename, $stringRep);
	}

	function saveTextToFile($filename, $text){
		if(function_exists('file_put_contents')){
			file_put_contents($filename, $text);
		} else{
			require_once (DOMIT_INCLUDE_PATH . 'php_file_utilities.php');
			php_file_utilities::putDataToFile($filename, $text, 'w');
		}
		return (file_exists($filename) && is_writable($filename));
	}

	function parsedBy(){
		return $this->parser;
	}

	function getText(){
		if($this->documentElement != null){
			$root = &$this->documentElement;
			return $root->getText();
		}
		return '';
	}

	function getDocType(){
		return $this->doctype;
	}

	function getXMLDeclaration(){
		return $this->xmlDeclaration;
	}

	function &getDOMImplementation(){
		return $this->implementation;
	}

	function load(&$contextNode){
		$total = $contextNode->childCount;
		for($i = 0; $i < $total; $i++){
			$currNode = &$contextNode->childNodes[$i];
			$currNode->ownerDocument->load($currNode);
		}
		$contextNode->onLoad();
	}

	function getVersion(){
		return DOMIT_VERSION;
	}

	function appendEntityTranslationTable($table){
		$this->definedEntities = $table;
		global $DOMIT_defined_entities_flip;
		$DOMIT_defined_entities_flip = array_flip($table);
	}

	function toArray(){
		$arReturn = array($this->nodeName => array());
		$total = $this->childCount;
		for($i = 0; $i < $total; $i++){
			$arReturn[$this->nodeName][$i] = $this->childNodes[$i]->toArray();
		}
		return $arReturn;
	}

	function &cloneNode($deep = false){
		$className = get_class($this);
		$clone = new $className($this->nodeName);
		if($deep){
			$total = $this->childCount;
			for($i = 0; $i < $total; $i++){
				$currentChild = &$this->childNodes[$i];
				$clone->appendChild($currentChild->cloneNode($deep));
				if($currentChild->nodeType == DOMIT_DOCUMENT_TYPE_NODE){
					$clone->doctype = &$clone->childNodes[$i];
				}
				if(($currentChild->nodeType == DOMIT_PROCESSING_INSTRUCTION_NODE) && ($currentChild->getTarget
				() == 'xml')
				){
					$clone->xmlDeclaration = &$clone->childNodes[$i];
				}
			}
		}
		return $clone;
	}

	function toString($htmlSafe = false, $subEntities = false){
		$result = '';
		$total = $this->childCount;
		for($i = 0; $i < $total; $i++){
			$result .= $this->childNodes[$i]->toString(false, $subEntities);
		}
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_Element extends DOMIT_ChildNodes_Interface{
	var $namespaceURIMap = array();

	function DOMIT_Element($tagName){
		$this->_constructor();
		$this->nodeType = DOMIT_ELEMENT_NODE;
		$this->nodeName = $tagName;
		$this->attributes = new DOMIT_NamedNodeMap_Attr();
		$this->childNodes = array();
	}

	function getTagName(){
		return $this->nodeName;
	}

	function getNamedElements(&$nodeList, $tagName){
		if(($this->nodeName == $tagName) || ($tagName == '*')){
			$nodeList->appendNode($this);
		}
		$total = $this->childCount;
		for($i = 0; $i < $total; $i++){
			$this->childNodes[$i]->getNamedElements($nodeList, $tagName);
		}
	}

	function declareNamespace($localname, $value){

		$this->setAttributeNS(DOMIT_XMLNS_NAMESPACE, ('xmlns:' . $localname), $value);

		$this->namespaceURIMap[$value] = $localname;
	}

	function declareDefaultNamespace($value){

		$this->setAttributeNS(DOMIT_XMLNS_NAMESPACE, 'xmlns', $value);

		$this->namespaceURIMap[$value] = 'xmlns';
	}

	function &getNamespaceDeclarationsInScope(){
		$nsMap = array();
		return $this->_getNameSpaceDeclarationsInScope($nsMap);
	}

	function &_getNamespaceDeclarationsInScope(&$nsMap){

		foreach($this->namespaceURIMap as $key => $value){
			if(!isset($nsMap[$key])){
				$nsMap[$key] = $value;
			}
		}

		if($this->parentNode->uid != $this->ownerDocument->uid){
			$this->parentNode->_getNamespaceDeclarationsInScope($nsMap);
		}
		return $nsMap;
	}

	function getDefaultNamespaceDeclaration(){
		if(in_array('xmlns', $this->namespaceURIMap)){
			foreach($this->namespaceURIMap as $key => $value){
				if($value == 'xmlns'){
					return $key;
				}
			}
		} else
			if($this->parentNode->uid != $this->ownerDocument->uid){
				return $this->parentNode->getDefaultNamespaceDeclaration();
			} else{
				return '';
			}
	}

	function copyNamespaceDeclarationsLocally(){
		$nsMap = $this->getNamespaceDeclarationsInScope();

		foreach($nsMap as $key => $value){
			if($value == 'xmlns'){
				$this->declareDefaultNamespace($key);
			} else{
				$this->declareNamespace($value, $key);
			}
		}
	}

	function getNamedElementsNS(&$nodeList, $namespaceURI, $localName){
		if((($namespaceURI == $this->namespaceURI) || ($namespaceURI == '*')) && (($localName ==
			$this->localName) || ($localName == '*'))
		){
			$nodeList->appendNode($this);
		}
		$total = $this->childCount;
		for($i = 0; $i < $total; $i++){
			if($this->childNodes[$i]->nodeType == DOMIT_ELEMENT_NODE){
				$this->childNodes[$i]->getNamedElementsNS($nodeList, $namespaceURI, $localName);
			}
		}
	}

	function getText(){
		$text = '';
		$numChildren = $this->childCount;
		for($i = 0; $i < $numChildren; $i++){
			$child = &$this->childNodes[$i];
			$text .= $child->getText();
		}
		return $text;
	}

	function setText($data){
		switch($this->childCount){
			case 1:
				if($this->firstChild->nodeType == DOMIT_TEXT_NODE){
					$this->firstChild->setText($data);
				}
				break;
			case 0:
				$childTextNode = &$this->ownerDocument->createTextNode($data);
				$this->appendChild($childTextNode);
				break;
			default:

		}
	}

	function &getElementsByTagName($tagName){
		$nodeList = new DOMIT_NodeList();
		$this->getNamedElements($nodeList, $tagName);
		return $nodeList;
	}

	function &getElementsByTagNameNS($namespaceURI, $localName){
		$nodeList = new DOMIT_NodeList();
		$this->getNamedElementsNS($nodeList, $namespaceURI, $localName);
		return $nodeList;
	}

	function &_getElementByID($elementID, $isStrict){
		if($isStrict){
			$myAttrNode = &$this->getAttributeNodeNS(DOMIT_XML_NAMESPACE, 'id');
			if(($myAttrNode != null) && ($myAttrNode->getValue() == $elementID))
				return $myAttrNode;
		} else{
			$myAttrNode = &$this->getAttributeNodeNS('', 'ID');
			if(($myAttrNode != null) && ($myAttrNode->getValue() == $elementID))
				return $myAttrNode;
			$myAttrNode = &$this->getAttributeNodeNS('', 'id');
			if(($myAttrNode != null) && ($myAttrNode->getValue() == $elementID))
				return $myAttrNode;
		}
		$total = $this->childCount;
		for($i = 0; $i < $total; $i++){
			if($this->childNodes[$i]->nodeType == DOMIT_ELEMENT_NODE){
				$foundNode = &$this->childNodes[$i]->_getElementByID($elementID, $isStrict);
				if($foundNode != null){
					return $foundNode;
				}
			}
		}
		$null = null;
		return $null;
	}

	function &getElementsByPath($pattern, $nodeIndex = 0){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_getelementsbypath.php');
		$gebp = new DOMIT_GetElementsByPath();
		$myResponse = &$gebp->parsePattern($this, $pattern, $nodeIndex);
		return $myResponse;
	}

	function &getElementsByAttributePath($pattern, $nodeIndex = 0){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_getelementsbypath.php');
		$gabp = new DOMIT_GetElementsByAttributePath();
		$myResponse = &$gabp->parsePattern($this, $pattern, $nodeIndex);
		return $myResponse;
	}

	function getTypedNodes(&$nodeList, $type){
		$numChildren = $this->childCount;
		for($i = 0; $i < $numChildren; $i++){
			$child = &$this->childNodes[$i];
			if($child->nodeType == $type){
				$nodeList->appendNode($child);
			}
			if($child->hasChildNodes()){
				$child->getTypedNodes($nodeList, $type);
			}
		}
	}

	function getValuedNodes(&$nodeList, $value){
		$numChildren = $this->childCount;
		for($i = 0; $i < $numChildren; $i++){
			$child = &$this->childNodes[$i];
			if($child->nodeValue == $value){
				$nodeList->appendNode($child);
			}
			if($child->hasChildNodes()){
				$child->getValuedNodes($nodeList, $value);
			}
		}
	}

	function getAttribute($name){
		$returnNode = &$this->attributes->getNamedItem($name);
		if($returnNode == null){
			return '';
		} else{
			return $returnNode->getValue();
		}
	}

	function getAttributeNS($namespaceURI, $localName){
		$returnNode = &$this->attributes->getNamedItemNS($namespaceURI, $localName);
		if($returnNode == null){
			return '';
		} else{
			return $returnNode->getValue();
		}
	}

	function setAttribute($name, $value){
		$returnNode = &$this->attributes->getNamedItem($name);
		if($returnNode == null){
			$newAttr = new DOMIT_Attr($name);
			$newAttr->setValue($value);
			$this->attributes->setNamedItem($newAttr);
		} else{
			$returnNode->setValue($value);
		}
	}

	function setAttributeNS($namespaceURI, $qualifiedName, $value){

		$colonIndex = strpos($qualifiedName, ":");
		if($colonIndex !== false){
			$localName = substr($qualifiedName, ($colonIndex + 1));
		} else{
			$localName = $qualifiedName;
		}
		$returnNode = &$this->attributes->getNamedItemNS($namespaceURI, $localName);
		if($returnNode == null){

			$newAttr = new DOMIT_Attr($qualifiedName);
			$newAttr->prefix = substr($qualifiedName, 0, $colonIndex);
			$newAttr->localName = $localName;
			$newAttr->namespaceURI = $namespaceURI;
			$newAttr->setValue($value);
			$this->attributes->setNamedItemNS($newAttr);
			$newAttr->ownerElement = &$this;
		} else{
			$returnNode->setValue($value);
		}
	}

	function removeAttribute($name){
		return $this->attributes->removeNamedItem($name);
	}

	function removeAttributeNS($namespaceURI, $localName){
		$returnNode = &$this->attributes->removeNamedItemNS($namespaceURI, $localName);
		unset($returnNode->ownerElement);
		$returnNode->ownerElement = null;
	}

	function hasAttribute($name){
		$returnNode = &$this->attributes->getNamedItem($name);
		return ($returnNode != null);
	}

	function hasAttributeNS($namespaceURI, $localName){
		$returnNode = &$this->attributes->getNamedItemNS($namespaceURI, $localName);
		return ($returnNode != null);
	}

	function hasAttributes(){
		return ($this->attributes->getLength() > 0);
	}

	function &getAttributeNode($name){
		$returnNode = &$this->attributes->getNamedItem($name);
		return $returnNode;
	}

	function &getAttributeNodeNS($namespaceURI, $localName){
		$returnNode = &$this->attributes->getNamedItemNS($namespaceURI, $localName);
		return $returnNode;
	}

	function &setAttributeNode(&$newAttr){
		$returnNode = &$this->attributes->setNamedItem($newAttr);
		return $returnNode;
	}

	function &setAttributeNodeNS(&$newAttr){
		$returnNode = &$this->attributes->setNamedItemNS($newAttr);
		$newAttr->ownerElement = &$this;
		return $returnNode;
	}

	function &removeAttributeNode(&$oldAttr){
		$attrName = $oldAttr->getName();
		$returnNode = &$this->attributes->removeNamedItem($attrName);
		if($returnNode == null){
			DOMIT_DOMException::raiseException(DOMIT_NOT_FOUND_ERR,
				'Target attribute not found.');
		} else{
			return $returnNode;
		}
	}

	function normalize(){
		if($this->hasChildNodes()){
			$currNode = &$this->childNodes[0];
			while($currNode->nextSibling != null){
				$nextNode = &$currNode->nextSibling;
				if(($currNode->nodeType == DOMIT_TEXT_NODE) && ($nextNode->nodeType ==
					DOMIT_TEXT_NODE)
				){
					$currNode->nodeValue .= $nextNode->nodeValue;
					$this->removeChild($nextNode);
				} else{
					$currNode->normalize();
				}
				if($currNode->nextSibling != null){
					$currNode = &$currNode->nextSibling;
				}
			}
		}
	}

	function toArray(){
		$arReturn = array($this->nodeName => array("attributes" => $this->attributes->toArray
		()));
		$total = $this->childCount;
		for($i = 0; $i < $total; $i++){
			$arReturn[$this->nodeName][$i] = $this->childNodes[$i]->toArray();
		}
		return $arReturn;
	}

	function &cloneNode($deep = false){
		$className = get_class($this);
		$clone = new $className($this->nodeName);
		$clone->attributes = &$this->attributes->createClone($deep);
		if($this->namespaceURI){
			$clone->namespaceURI = $this->namespaceURI;
			$clone->localName = $this->localName;
		}
		if($deep){
			$total = $this->childCount;
			for($i = 0; $i < $total; $i++){
				$currentChild = &$this->childNodes[$i];
				$clone->appendChild($currentChild->cloneNode($deep));
			}
		}
		return $clone;
	}

	function toString($htmlSafe = false, $subEntities = false){
		$result = '<' . $this->nodeName;
		$result .= $this->attributes->toString(false, $subEntities);
		if($this->ownerDocument->isNamespaceAware){
			foreach($this->namespaceURIMap as $key => $value){
				$result .= ' xmlns:' . $value . '="' . $key . '"';
			}
		}

		$myNodes = &$this->childNodes;
		$total = count($myNodes);
		if($total != 0){
			$result .= '>';
			for($i = 0; $i < $total; $i++){
				$child = &$myNodes[$i];
				$result .= $child->toString(false, $subEntities);
			}
			$result .= '</' . $this->nodeName . '>';
		} else{
			if($this->ownerDocument->doExpandEmptyElementTags){
				if(in_array($this->nodeName, $this->ownerDocument->expandEmptyElementExceptions)){
					$result .= ' />';
				} else{
					$result .= '></' . $this->nodeName . '>';
				}
			} else{
				if(in_array($this->nodeName, $this->ownerDocument->expandEmptyElementExceptions)){
					$result .= '></' . $this->nodeName . '>';
				} else{
					$result .= ' />';
				}
			}
		}
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_CharacterData extends DOMIT_Node{
	function DOMIT_CharacterData(){
		DOMIT_DOMException::raiseException(DOMIT_ABSTRACT_CLASS_INSTANTIATION_ERR,
			'Cannot instantiate abstract class DOMIT_CharacterData');
	}

	function getData(){
		return $this->nodeValue;
	}

	function setData($data){
		$this->nodeValue = $data;
	}

	function getLength(){
		return strlen($this->nodeValue);
	}

	function substringData($offset, $count){
		$totalChars = $this->getLength();
		if(($offset < 0) || (($offset + $count) > $totalChars)){
			DOMIT_DOMException::raiseException(DOMIT_INDEX_SIZE_ERR,
				'Character Data index out of bounds.');
		} else{
			$data = $this->getData();
			return substr($data, $offset, $count);
		}
	}

	function appendData($arg){
		$this->setData($this->getData() . $arg);
	}

	function insertData($offset, $arg){
		$totalChars = $this->getLength();
		if(($offset < 0) || ($offset > $totalChars)){
			DOMIT_DOMException::raiseException(DOMIT_INDEX_SIZE_ERR,
				'Character Data index out of bounds.');
		} else{
			$data = $this->getData();
			$pre = substr($data, 0, $offset);
			$post = substr($data, $offset);
			$this->setData(($pre . $arg . $post));
		}
	}

	function deleteData($offset, $count){
		$totalChars = $this->getLength();
		if(($offset < 0) || (($offset + $count) > $totalChars)){
			DOMIT_DOMException::raiseException(DOMIT_INDEX_SIZE_ERR,
				'Character Data index out of bounds.');
		} else{
			$data = $this->getData();
			$pre = substr($data, 0, $offset);
			$post = substr($data, ($offset + $count));
			$this->setData(($pre . $post));
		}
	}

	function replaceData($offset, $count, $arg){
		$totalChars = $this->getLength();
		if(($offset < 0) || (($offset + $count) > $totalChars)){
			DOMIT_DOMException::raiseException(DOMIT_INDEX_SIZE_ERR,
				'Character Data index out of bounds.');
		} else{
			$data = $this->getData();
			$pre = substr($data, 0, $offset);
			$post = substr($data, ($offset + $count));
			$this->setData(($pre . $arg . $post));
		}
	}

}

class DOMIT_TextNode extends DOMIT_CharacterData{
	function DOMIT_TextNode($data){
		$this->_constructor();
		$this->nodeType = DOMIT_TEXT_NODE;
		$this->nodeName = '#text';
		$this->setText($data);
	}

	function getText(){
		return $this->nodeValue;
	}

	function setText($data){
		$this->nodeValue = $data;
	}

	function &splitText($offset){
		$totalChars = $this->getLength();
		if(($offset < 0) || ($offset > $totalChars)){
			DOMIT_DOMException::raiseException(DOMIT_INDEX_SIZE_ERR,
				'Character Data index out of bounds.');
		} else{
			$data = $this->getData();
			$pre = substr($data, 0, $offset);
			$post = substr($data, $offset);
			$this->setText($pre);

			$className = get_class($this);
			$newTextNode = new $className($post);
			$newTextNode->ownerDocument = &$this->ownerDocument;
			if($this->parentNode->lastChild->uid == $this->uid){
				$this->parentNode->appendChild($newTextNode);
			} else{
				$this->parentNode->insertBefore($newTextNode, $this);
			}
			return $newTextNode;
		}
	}

	function toArray(){
		return $this->toString();
	}

	function &cloneNode(){
		$className = get_class($this);
		$clone = new $className($this->nodeValue);
		return $clone;
	}

	function toString($htmlSafe = false, $subEntities = false){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_utilities.php');
		global $DOMIT_defined_entities_flip;
		$result = $subEntities ? DOMIT_Utilities::convertEntities($this->nodeValue, $DOMIT_defined_entities_flip) :
			$this->nodeValue;
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_CDATASection extends DOMIT_TextNode{
	function DOMIT_CDATASection($data){
		$this->_constructor();
		$this->nodeType = DOMIT_CDATA_SECTION_NODE;
		$this->nodeName = '#cdata-section';
		$this->setText($data);
	}

	function toString($htmlSafe = false, $subEntities = false){
		$result = '<![CDATA[';
		$result .= $subEntities ? str_replace("]]>", "]]&gt;", $this->nodeValue) : $this->nodeValue;
		$result .= ']]>';
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_Attr extends DOMIT_Node{
	var $specified = false;
	var $ownerElement = null;

	function DOMIT_Attr($name){
		$this->_constructor();
		$this->nodeType = DOMIT_ATTRIBUTE_NODE;
		$this->nodeName = $name;
	}

	function getName(){
		return $this->nodeName;
	}

	function getSpecified(){
		return $this->specified;
	}

	function getValue(){
		return $this->nodeValue;
	}

	function setValue($value){
		$this->nodeValue = $value;
	}

	function getText(){
		return $this->nodeValue;
	}

	function setText($data){
		$this->nodeValue = $data;
	}

	function &cloneNode(){
		$className = get_class($this);
		$clone = new $className($this->nodeName);
		$clone->nodeValue = $this->nodeValue;
		if($this->namespaceURI){
			$clone->namespaceURI = $this->namespaceURI;
			$clone->localName = $this->localName;
		}
		return $clone;
	}

	function toString($htmlSafe = false, $subEntities = false){
		require_once (DOMIT_INCLUDE_PATH . 'xml_domit_utilities.php');
		global $DOMIT_defined_entities_flip;
		$result = ' ' . $this->nodeName . '="';
		$result .= $subEntities ? DOMIT_Utilities::convertEntities($this->nodeValue, $DOMIT_defined_entities_flip) :
			$this->nodeValue;
		$result .= '"';
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_DocumentFragment extends DOMIT_ChildNodes_Interface{
	function DOMIT_DocumentFragment(){
		$this->_constructor();
		$this->nodeType = DOMIT_DOCUMENT_FRAGMENT_NODE;
		$this->nodeName = '#document-fragment';
		$this->nodeValue = null;
		$this->childNodes = array();
	}

	function toArray(){
		$arReturn = array();
		$total = $this->childCount;
		for($i = 0; $i < $total; $i++){
			$arReturn[$i] = $this->childNodes[$i]->toArray();
		}
		return $arReturn;
	}

	function &cloneNode($deep = false){
		$className = get_class($this);
		$clone = new $className();
		if($deep){
			$total = $this->childCount;
			for($i = 0; $i < $total; $i++){
				$currentChild = &$this->childNodes[$i];
				$clone->appendChild($currentChild->cloneNode($deep));
			}
		}
		return $clone;
	}

	function toString($htmlSafe = false, $subEntities = false){

		$result = '';
		$myNodes = &$this->childNodes;
		$total = count($myNodes);
		if($total != 0){
			for($i = 0; $i < $total; $i++){
				$child = &$myNodes[$i];
				$result .= $child->toString(false, $subEntities);
			}
		}
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_Comment extends DOMIT_CharacterData{
	function DOMIT_Comment($nodeValue){
		$this->_constructor();
		$this->nodeType = DOMIT_COMMENT_NODE;
		$this->nodeName = '#comment';
		$this->nodeValue = $nodeValue;
	}

	function getText(){
		return $this->nodeValue;
	}

	function setText($data){
		$this->nodeValue = $data;
	}

	function toArray(){
		return $this->toString();
	}

	function &cloneNode(){
		$className = get_class($this);
		$clone = new $className($this->nodeValue);
		return $clone;
	}

	function toString($htmlSafe = false){
		$result = '<!--' . $this->nodeValue . '-->';
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_ProcessingInstruction extends DOMIT_Node{
	function DOMIT_ProcessingInstruction($target, $data){
		$this->_constructor();
		$this->nodeType = DOMIT_PROCESSING_INSTRUCTION_NODE;
		$this->nodeName = $target;
		$this->nodeValue = $data;
	}

	function getTarget(){
		return $this->nodeName;
	}

	function getData(){
		return $this->nodeValue;
	}

	function getText(){
		return ($this->nodeName . ' ' . $this->nodeValue);
	}

	function toArray(){
		return $this->toString();
	}

	function &cloneNode(){
		$className = get_class($this);
		$clone = new $className($this->nodeName, $this->nodeValue);
		return $clone;
	}

	function toString($htmlSafe = false){
		$result = '<' . '?' . $this->nodeName . ' ' . $this->nodeValue . '?' . '>';
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_DocumentType extends DOMIT_Node{
	var $name;
	var $entities;
	var $notations;
	var $elements;
	var $text;
	var $publicID;
	var $systemID;
	var $internalSubset;

	function DOMIT_DocumentType($name, $text){
		$this->_constructor();
		$this->nodeType = DOMIT_DOCUMENT_TYPE_NODE;
		$this->nodeName = $name;
		$this->name = $name;
		$this->entities = null;

		$this->notations = null;

		$this->elements = null;

		$this->text = $text;
	}

	function getText(){
		return $this->text;
	}

	function getName(){
		return $this->name;
	}

	function toArray(){
		return $this->toString();
	}

	function &cloneNode(){
		$className = get_class($this);
		$clone = new $className($this->nodeName, $this->text);
		return $clone;
	}

	function toString($htmlSafe = false){
		$result = $this->text;
		if($htmlSafe)
			$result = $this->forHTML($result);
		return $result;
	}

}

class DOMIT_Notation extends DOMIT_Node{
	function DOMIT_Notation(){
		DOMIT_DOMException::raiseException(DOMIT_NOT_SUPPORTED_ERR,
			'Cannot instantiate DOMIT_Notation class. Notation nodes not yet supported.');
	}

}

class DOMIT_Parser{
	var $xmlDoc = null;
	var $currentNode = null;
	var $lastChild = null;
	var $inCDATASection = false;

	var $inTextNode = false;
	var $preserveCDATA;
	var $parseContainer = '';
	var $parseItem = '';
	var $waitingForElementToDeclareNamespaces = false;
	var $tempNamespaceURIMap = array();

	function parse(&$myXMLDoc, $xmlText, $preserveCDATA = true){
		$this->xmlDoc = &$myXMLDoc;
		$this->lastChild = &$this->xmlDoc;
		$this->preserveCDATA = $preserveCDATA;

		if(version_compare(phpversion(), '5.0', '<=')){
			if($this->xmlDoc->isNamespaceAware){
				$parser = xml_parser_create_ns('');
			} else{
				$parser = xml_parser_create('');
			}
		} else{
			if($this->xmlDoc->isNamespaceAware){
				$parser = xml_parser_create_ns();
			} else{
				$parser = xml_parser_create();
			}
		}

		xml_set_object($parser, $this);
		xml_set_character_data_handler($parser, 'dataElement');
		xml_set_default_handler($parser, 'defaultDataElement');
		xml_set_notation_decl_handler($parser, 'notationElement');
		xml_set_processing_instruction_handler($parser, 'processingInstructionElement');
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		if(!$this->xmlDoc->preserveWhitespace){
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		} else{
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
		}
		if($this->xmlDoc->isNamespaceAware){
			xml_set_start_namespace_decl_handler($parser, 'startNamespaceDeclaration');
			xml_set_end_namespace_decl_handler($parser, 'endNamespaceDeclaration');
			xml_set_element_handler($parser, 'startElementNS', 'endElement');
			$this->xmlDoc->namespaceURIMap[DOMIT_XML_NAMESPACE] = 'xml';
		} else{
			xml_set_element_handler($parser, 'startElement', 'endElement');
		}


		if(!$this->xmlDoc->preserveWhitespace){
			$xmlText = preg_replace('/>' . "[[:space:]]+" . '</iu', '><', $xmlText);
		}
		$success = xml_parse($parser, $xmlText);
		$this->xmlDoc->errorCode = xml_get_error_code($parser);
		$this->xmlDoc->errorString = xml_error_string($this->xmlDoc->errorCode);
		xml_parser_free($parser);
		return $success;
	}

	function parseSAXY(&$myXMLDoc, $xmlText, $preserveCDATA = true, $definedEntities){
		require_once (DOMIT_INCLUDE_PATH . 'xml_saxy_parser.php');
		$this->xmlDoc = &$myXMLDoc;
		$this->lastChild = &$this->xmlDoc;

		$parser = new SAXY_Parser();
		$parser->appendEntityTranslationTable($definedEntities);

		$parser->preserveWhitespace = $this->xmlDoc->preserveWhitespace;
		if($this->xmlDoc->isNamespaceAware){
			$parser->setNamespaceAwareness(true);
			$parser->xml_set_start_namespace_decl_handler(array(&$this,
				'startNamespaceDeclaration'));
			$parser->xml_set_end_namespace_decl_handler(array(&$this,
				'endNamespaceDeclaration'));
			$parser->xml_set_element_handler(array(&$this, 'startElementNS'), array(&$this,
				'endElement'));
			$this->xmlDoc->namespaceURIMap[DOMIT_XML_NAMESPACE] = 'xml';
		} else{
			$parser->xml_set_element_handler(array(&$this, 'startElement'), array(&$this,
				'endElement'));
		}
		$parser->xml_set_character_data_handler(array(&$this, 'dataElement'));
		$parser->xml_set_doctype_handler(array(&$this, 'doctypeElement'));
		$parser->xml_set_comment_handler(array(&$this, 'commentElement'));
		$parser->xml_set_processing_instruction_handler(array(&$this,
			'processingInstructionElement'));
		if($preserveCDATA){
			$parser->xml_set_cdata_section_handler(array(&$this, 'cdataElement'));
		}
		$success = $parser->parse($xmlText);
		$this->xmlDoc->errorCode = $parser->xml_get_error_code();
		$this->xmlDoc->errorString = $parser->xml_error_string($this->xmlDoc->errorCode);
		return $success;
	}

	function dumpTextNode(){
		$currentNode = &$this->xmlDoc->createTextNode($this->parseContainer);
		$this->lastChild->appendChild($currentNode);
		$this->inTextNode = false;
		$this->parseContainer = '';
	}

	function startElement(&$parser, $name, $attrs){
		if($this->inTextNode){
			$this->dumpTextNode();
		}
		$currentNode = &$this->xmlDoc->createElement($name);
		$this->lastChild->appendChild($currentNode);
		reset($attrs);
		while(list($key, $value) = each($attrs)){
			$currentNode->setAttribute($key, $value);
		}
		$this->lastChild = &$currentNode;
	}

	function startElementNS(&$parser, $name, $attrs){
		if($this->inTextNode){
			$this->dumpTextNode();
		}
		$colonIndex = strrpos($name, ":");
		if($colonIndex !== false){

			$namespaceURI = strtolower(substr($name, 0, $colonIndex));
			$prefix = $this->xmlDoc->namespaceURIMap[$namespaceURI];
			if($prefix != ''){
				$qualifiedName = $prefix . ":" . substr($name, ($colonIndex + 1));
			} else{
				$qualifiedName = substr($name, ($colonIndex + 1));
			}
		} else{
			$namespaceURI = '';
			$qualifiedName = $name;
		}
		$currentNode = &$this->xmlDoc->createElementNS($namespaceURI, $qualifiedName);
		$this->lastChild->appendChild($currentNode);

		reset($attrs);
		while(list($key, $value) = each($attrs)){
			$colonIndex = strrpos($key, ":");
			if($colonIndex !== false){

				$namespaceURI = strtolower(substr($key, 0, $colonIndex));
				$qualifiedName = $this->xmlDoc->namespaceURIMap[$namespaceURI] . ":" . substr($key,
					($colonIndex + 1));
				;
			} else{


				$namespaceURI = '';
				$qualifiedName = $key;
			}
			$currentNode->setAttributeNS($namespaceURI, $qualifiedName, $value);
		}

		if($this->waitingForElementToDeclareNamespaces){

			foreach($this->tempNamespaceURIMap as $key => $value){
				$currentNode->namespaceURIMap[$key] = $value;

				$currentNode->setAttributeNS(DOMIT_XMLNS_NAMESPACE, ('xmlns:' . $value), $key);
			}

			$this->tempNamespaceURIMap = array();
			$this->waitingForElementToDeclareNamespaces = false;
		}
		$this->lastChild = &$currentNode;
	}

	function endElement(){
		if($this->inTextNode){
			$this->dumpTextNode();
		}
		$this->lastChild = &$this->lastChild->parentNode;
	}

	function dataElement(&$parser, $data){
		if(!$this->inCDATASection){
			$this->inTextNode = true;
		}
		$this->parseContainer .= $data;
	}

	function cdataElement(&$parser, $data){
		$currentNode = &$this->xmlDoc->createCDATASection($data);
		$this->lastChild->appendChild($currentNode);
	}

	function defaultDataElement(&$parser, $data){
		if((strlen($data) > 2) && ($this->parseItem == '')){
			$pre = strtoupper(substr($data, 0, 3));
			switch($pre){
				case '<?X':

					$this->processingInstructionElement($parser, 'xml', substr($data, 6, (strlen($data) -
						6 - 2)));
					break;
				case '<!E':

					$this->xmlDoc->doctype .= "\n   " . $data;
					break;
				case '<![':

					if($this->preserveCDATA){
						$this->inCDATASection = true;
					}
					break;
				case '<!-':

					$currentNode = $this->commentElement($this, substr($data, 4, (strlen($data) - 7)));
					break;
				case '<!D':

					$this->parseItem = 'doctype';
					$this->parseContainer = $data;
					break;
				case ']]>':

					if($this->preserveCDATA){
						$currentNode = &$this->xmlDoc->createCDATASection($this->parseContainer);
						$this->lastChild->appendChild($currentNode);
						$this->inCDATASection = false;
						$this->parseContainer = '';
					} else{
						$this->dumpTextNode();
					}
					break;
			}
		} else{
			switch($this->parseItem){
				case 'doctype':
					$this->parseContainer .= $data;
					if($data == '>'){
						$this->doctypeElement($parser, $this->parseContainer);
						$this->parseContainer = '';
						$this->parseItem = '';
					} else
						if($data == '['){
							$this->parseItem = 'doctype_inline';
						}
					break;
				case 'doctype_inline':
					$this->parseContainer .= $data;
					if($data == ']'){
						$this->parseItem = 'doctype';
					} else
						if($data{(strlen($data) - 1)} == '>'){
							$this->parseContainer .= "\n   ";
						}
					break;
			}
		}
	}

	function doctypeElement(&$parser, $data){
		$start = strpos($data, '<!DOCTYPE');
		$name = trim(substr($data, $start));
		$end = strpos($name, ' ');
		$name = substr($name, 0, $end);
		$currentNode = new DOMIT_DocumentType($name, $data);
		$currentNode->ownerDocument = &$this->xmlDoc;
		$this->lastChild->appendChild($currentNode);
		$this->xmlDoc->doctype = &$currentNode;
	}

	function notationElement(&$parser, $data){

		if(($this->parseItem == 'doctype_inline') || ($this->parseItem == 'doctype')){
			$this->parseContainer .= $data;
		}
	}

	function commentElement(&$parser, $data){
		if($this->inTextNode){
			$this->dumpTextNode();
		}
		$currentNode = &$this->xmlDoc->createComment($data);
		$this->lastChild->appendChild($currentNode);
	}

	function processingInstructionElement(&$parser, $target, $data){
		if($this->inTextNode){
			$this->dumpTextNode();
		}
		$currentNode = &$this->xmlDoc->createProcessingInstruction($target, $data);
		$this->lastChild->appendChild($currentNode);
		if(strtolower($target) == 'xml'){
			$this->xmlDoc->xmlDeclaration = &$currentNode;
		}
	}

	function startNamespaceDeclaration(&$parser, $prefix, $uri){

		$this->xmlDoc->namespaceURIMap[strtolower($uri)] = $prefix;

		$this->waitingForElementToDeclareNamespaces = true;
		$this->tempNamespaceURIMap[strtolower($uri)] = $prefix;
	}

	function endNamespaceDeclaration(&$parser, $prefix){

	}

}


?>
