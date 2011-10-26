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
if(!defined('SAXY_INCLUDE_PATH')) {
	define('SAXY_INCLUDE_PATH',(dirname(__file__)."/"));
}
define('SAXY_VERSION','1.0');
define('SAXY_XML_NAMESPACE','http://www.w3.org/xml/1998/namespace');
define('SAXY_STATE_PROLOG_NONE',0);
define('SAXY_STATE_PROLOG_PROCESSINGINSTRUCTION',1);
define('SAXY_STATE_PROLOG_EXCLAMATION',2);
define('SAXY_STATE_PROLOG_DTD',3);
define('SAXY_STATE_PROLOG_INLINEDTD',4);
define('SAXY_STATE_PROLOG_COMMENT',5);
define('SAXY_STATE_PARSING',6);
define('SAXY_STATE_PARSING_COMMENT',7);

define('SAXY_XML_ERROR_NONE',0);
define('SAXY_XML_ERROR_NO_MEMORY',1);
define('SAXY_XML_ERROR_SYNTAX',2);
define('SAXY_XML_ERROR_NO_ELEMENTS',3);
define('SAXY_XML_ERROR_INVALID_TOKEN',4);
define('SAXY_XML_ERROR_UNCLOSED_TOKEN',5);
define('SAXY_XML_ERROR_PARTIAL_CHAR',6);
define('SAXY_XML_ERROR_TAG_MISMATCH',7);
define('SAXY_XML_ERROR_DUPLICATE_ATTRIBUTE',8);
define('SAXY_XML_ERROR_JUNK_AFTER_DOC_ELEMENT',9);
define('SAXY_XML_ERROR_PARAM_ENTITY_REF',10);
define('SAXY_XML_ERROR_UNDEFINED_ENTITY',11);
define('SAXY_XML_ERROR_RECURSIVE_ENTITY_REF',12);
define('SAXY_XML_ERROR_ASYNC_ENTITY',13);
define('SAXY_XML_ERROR_BAD_CHAR_REF',14);
define('SAXY_XML_ERROR_BINARY_ENTITY_REF',15);
define('SAXY_XML_ERROR_ATTRIBUTE_EXTERNAL_ENTITY_REF',16);
define('SAXY_XML_ERROR_MISPLACED_XML_PI',17);
define('SAXY_XML_ERROR_UNKNOWN_ENCODING',18);
define('SAXY_XML_ERROR_INCORRECT_ENCODING',19);
define('SAXY_XML_ERROR_UNCLOSED_CDATA_SECTION',20);
define('SAXY_XML_ERROR_EXTERNAL_ENTITY_HANDLING',21);
require_once (SAXY_INCLUDE_PATH.'xml_saxy_shared.php');
class SAXY_Parser extends SAXY_Parser_Base {
	var $errorCode = SAXY_XML_ERROR_NONE;
	var $DTDHandler = null;
	var $commentHandler = null;
	var $processingInstructionHandler = null;
	var $startNamespaceDeclarationHandler = null;
	var $endNamespaceDeclarationHandler = null;
	var $isNamespaceAware = false;
	var $namespaceMap = array();
	var $namespaceStack = array();
	var $defaultNamespaceStack = array();
	var $elementNameStack = array();
	function SAXY_Parser() {
		$this->SAXY_Parser_Base();
		$this->state = SAXY_STATE_PROLOG_NONE;
	}

	function xml_set_doctype_handler($handler) {
		$this->DTDHandler = &$handler;
	}

	function xml_set_comment_handler($handler) {
		$this->commentHandler = &$handler;
	}

	function xml_set_processing_instruction_handler($handler) {
		$this->processingInstructionHandler = &$handler;
	}

	function xml_set_start_namespace_decl_handler($handler) {
		$this->startNamespaceDeclarationHandler = &$handler;
	}

	function xml_set_end_namespace_decl_handler($handler) {
		$this->endNamespaceDeclarationHandler = &$handler;
	}

	function setNamespaceAwareness($isNamespaceAware) {
		$this->isNamespaceAware = &$isNamespaceAware;
	}

	function getVersion() {
		return SAXY_VERSION;
	}

	function preprocessXML($xmlText) {

		$xmlText = trim($xmlText);
		$startChar = -1;
		$total = strlen($xmlText);
		for($i = 0; $i < $total; $i++) {
			$currentChar = $xmlText{$i};
			switch($this->state) {
				case SAXY_STATE_PROLOG_NONE:
					if($currentChar == '<') {
						$nextChar = $xmlText{($i + 1)};
						if($nextChar == '?') {
							$this->state = SAXY_STATE_PROLOG_PROCESSINGINSTRUCTION;
							$this->charContainer = '';
						} else
							if($nextChar == '!') {
								$this->state = SAXY_STATE_PROLOG_EXCLAMATION;
								$this->charContainer .= $currentChar;
								break;
							} else {
								$this->charContainer = '';
								$startChar = $i;
								$this->state = SAXY_STATE_PARSING;
								return (substr($xmlText,$startChar));
							}
					}
					break;
				case SAXY_STATE_PROLOG_EXCLAMATION:
					if($currentChar == 'D') {
						$this->state = SAXY_STATE_PROLOG_DTD;
						$this->charContainer .= $currentChar;
					} else
						if($currentChar == '-') {
							$this->state = SAXY_STATE_PROLOG_COMMENT;
							$this->charContainer = '';
						} else {

							$this->charContainer .= $currentChar;
						}
						break;
				case SAXY_STATE_PROLOG_PROCESSINGINSTRUCTION:
					if($currentChar == '>') {
						$this->state = SAXY_STATE_PROLOG_NONE;
						$this->parseProcessingInstruction($this->charContainer);
						$this->charContainer = '';
					} else {
						$this->charContainer .= $currentChar;
					}
					break;
				case SAXY_STATE_PROLOG_COMMENT:
					if($currentChar == '>') {
						$this->state = SAXY_STATE_PROLOG_NONE;
						$this->parseComment($this->charContainer);
						$this->charContainer = '';
					} else
						if($currentChar == '-') {
							if((($xmlText{($i + 1)} == '-') && ($xmlText{($i + 2)} == '>')) || ($xmlText{($i +
								1)} == '>') || (($xmlText{($i - 1)} == '-') && ($xmlText{($i - 2)} == '!'))) {

							} else {
								$this->charContainer .= $currentChar;
							}
						} else {
							$this->charContainer .= $currentChar;
						}
						break;
				case SAXY_STATE_PROLOG_DTD:
					if($currentChar == '[') {
						$this->charContainer .= $currentChar;
						$this->state = SAXY_STATE_PROLOG_INLINEDTD;
					} else
						if($currentChar == '>') {
							$this->state = SAXY_STATE_PROLOG_NONE;
							if($this->DTDHandler != null) {
								$this->fireDTDEvent($this->charContainer.$currentChar);
							}
							$this->charContainer = '';
						} else {
							$this->charContainer .= $currentChar;
						}
						break;
				case SAXY_STATE_PROLOG_INLINEDTD:
					$previousChar = $xmlText{($i - 1)};
					if(($currentChar == '>') && ($previousChar == ']')) {
						$this->state = SAXY_STATE_PROLOG_NONE;
						if($this->DTDHandler != null) {
							$this->fireDTDEvent($this->charContainer.$currentChar);
						}
						$this->charContainer = '';
					} else {
						$this->charContainer .= $currentChar;
					}
					break;
			}
		}
	}

	function parse($xmlText) {
		$xmlText = $this->preprocessXML($xmlText);
		$total = strlen($xmlText);
		for($i = 0; $i < $total; $i++) {
			$currentChar = $xmlText{$i};
			switch($this->state) {
				case SAXY_STATE_PARSING:
					switch($currentChar) {
						case '<':
							if(substr($this->charContainer,0,SAXY_CDATA_LEN) == SAXY_SEARCH_CDATA) {
								$this->charContainer .= $currentChar;
							} else {
								$this->parseBetweenTags($this->charContainer);
								$this->charContainer = '';
							}
							break;
						case '-':
							if(($xmlText{($i - 1)} == '-') && ($xmlText{($i - 2)} == '!') && ($xmlText{($i -
								3)} == '<')) {
								$this->state = SAXY_STATE_PARSING_COMMENT;
								$this->charContainer = '';
							} else {
								$this->charContainer .= $currentChar;
							}
							break;
						case '>':
							if((substr($this->charContainer,0,SAXY_CDATA_LEN) == SAXY_SEARCH_CDATA) && !(($this->getCharFromEnd
								($this->charContainer,0) == ']') && ($this->getCharFromEnd($this->charContainer,
								1) == ']'))) {
								$this->charContainer .= $currentChar;
							} else {
								$this->parseTag($this->charContainer);
								$this->charContainer = '';
							}
							break;
						default:
							$this->charContainer .= $currentChar;
					}
					break;
				case SAXY_STATE_PARSING_COMMENT:
					switch($currentChar) {
						case '>':
							if(($xmlText{($i - 1)} == '-') && ($xmlText{($i - 2)} == '-')) {
								$this->fireCommentEvent(substr($this->charContainer,0,(strlen($this->charContainer) -
									2)));
								$this->charContainer = '';
								$this->state = SAXY_STATE_PARSING;
							} else {
								$this->charContainer .= $currentChar;
							}
							break;
						default:
							$this->charContainer .= $currentChar;
					}
					break;
			}
		}
		return ($this->errorCode == 0);
	}

	function parseTag($tagText) {
		$tagText = trim($tagText);
		$firstChar = $tagText{0};
		$myAttributes = array();
		switch($firstChar) {
			case '/':
				$tagName = substr($tagText,1);
				$this->_fireEndElementEvent($tagName);
				break;
			case '!':
				$upperCaseTagText = strtoupper($tagText);
				if(strpos($upperCaseTagText,SAXY_SEARCH_CDATA) !== false) {

					$total = strlen($tagText);
					$openBraceCount = 0;
					$textNodeText = '';
					for($i = 0; $i < $total; $i++) {
						$currentChar = $tagText{$i};
						if(($currentChar == ']') && ($tagText{($i + 1)} == ']')) {
							break;
						} else
							if($openBraceCount > 1) {
								$textNodeText .= $currentChar;
							} else
								if($currentChar == '[') {

									$openBraceCount++;
								}
					}
					if($this->cDataSectionHandler == null) {
						$this->fireCharacterDataEvent($textNodeText);
					} else {
						$this->fireCDataSectionEvent($textNodeText);
					}
				} else
					if(strpos($upperCaseTagText,SAXY_SEARCH_NOTATION) !== false) {

						return;
					}
				break;
			case '?':

				$this->parseProcessingInstruction($tagText);
				break;
			default:
				if((strpos($tagText,'"') !== false) || (strpos($tagText,"'") !== false)) {
					$total = strlen($tagText);
					$tagName = '';
					for($i = 0; $i < $total; $i++) {
						$currentChar = $tagText{$i};
						if(($currentChar == ' ') || ($currentChar == "\t") || ($currentChar == "\n") ||
							($currentChar == "\r") || ($currentChar == "\x0B")) {
							$myAttributes = $this->parseAttributes(substr($tagText,$i));
							break;
						} else {
							$tagName .= $currentChar;
						}
					}
					if(strrpos($tagText,'/') == (strlen($tagText) - 1)) {

						$this->_fireStartElementEvent($tagName,$myAttributes);
						$this->_fireEndElementEvent($tagName);
					} else {
						$this->_fireStartElementEvent($tagName,$myAttributes);
					}
				} else {
					if(strpos($tagText,'/') !== false) {
						$tagText = trim(substr($tagText,0,(strrchr($tagText,'/') - 1)));
						$this->_fireStartElementEvent($tagText,$myAttributes);
						$this->_fireEndElementEvent($tagText);
					} else {
						$this->_fireStartElementEvent($tagText,$myAttributes);
					}
				}
		}
	}

	function _fireStartElementEvent($tagName,&$myAttributes) {
		$this->elementNameStack[] = $tagName;
		if($this->isNamespaceAware) {
			$this->detectStartNamespaceDeclaration($myAttributes);
			$tagName = $this->expandNamespacePrefix($tagName);
			$this->expandAttributePrefixes($myAttributes);
		}
		$this->fireStartElementEvent($tagName,$myAttributes);
	}

	function expandAttributePrefixes(&$myAttributes) {
		$arTransform = array();
		foreach($myAttributes as $key => $value) {
			if(strpos($key,'xmlns') === false) {
				if(strpos($key,':') !== false) {
					$expandedTag = $this->expandNamespacePrefix($key);
					$arTransform[$key] = $expandedTag;
				}
			}
		}
		foreach($arTransform as $key => $value) {
			$myAttributes[$value] = $myAttributes[$key];
			unset($myAttributes[$key]);
		}
	}

	function expandNamespacePrefix($tagName) {
		$stackLen = count($this->defaultNamespaceStack);
		$defaultNamespace = $this->defaultNamespaceStack[($stackLen - 1)];
		$colonIndex = strpos($tagName,':');
		if($colonIndex !== false) {
			$prefix = substr($tagName,0,$colonIndex);
			if($prefix != 'xml') {
				$tagName = $this->getNamespaceURI($prefix).substr($tagName,$colonIndex);
			} else {
				$tagName = SAXY_XML_NAMESPACE.substr($tagName,$colonIndex);
			}
		} else
			if($defaultNamespace != '') {
				$tagName = $defaultNamespace.':'.$tagName;
			}
		return $tagName;
	}

	function getNamespaceURI($prefix) {
		$total = count($this->namespaceMap);
		$uri = $prefix;



		for($i = ($total - 1); $i >= 0; $i--) {
			$currMap = &$this->namespaceMap[$i];
			if(isset($currMap[$prefix])) {
				$uri = $currMap[$prefix];
				break;
			}
		}
		return $uri;
	}

	function detectStartNamespaceDeclaration(&$myAttributes) {
		$namespaceExists = false;
		$namespaceMapUpper = 0;
		$userDefinedDefaultNamespace = false;
//		$total = count($myAttributes);
		foreach($myAttributes as $key => $value) {
			if(strpos($key,'xmlns') !== false) {

				if(!$namespaceExists) {
					$this->namespaceMap[] = array();
					$namespaceMapUpper = count($this->namespaceMap) - 1;
				}

				if(strpos($key,':') !== false) {
					$prefix = $namespaceMapKey = substr($key,6);
					$this->namespaceMap[$namespaceMapUpper][$namespaceMapKey] = $value;
				} else {
					$prefix = '';
					$userDefinedDefaultNamespace = true;

					$this->namespaceMap[$namespaceMapUpper][':'] = $value;
					$this->defaultNamespaceStack[] = $value;
				}
				$this->fireStartNamespaceDeclarationEvent($prefix,$value);
				$namespaceExists = true;
				unset($myAttributes[$key]);
			}
		}

		if(!$userDefinedDefaultNamespace) {
			$stackLen = count($this->defaultNamespaceStack);
			if($stackLen == 0) {
				$this->defaultNamespaceStack[] = '';
			} else {
				$this->defaultNamespaceStack[] = $this->defaultNamespaceStack[($stackLen - 1)];
			}
		}
		$this->namespaceStack[] = $namespaceExists;
	}

	function _fireEndElementEvent($tagName) {
		$lastTagName = array_pop($this->elementNameStack);

		if($lastTagName != $tagName) {
			$this->errorCode = SAXY_XML_ERROR_TAG_MISMATCH;
		}
		if($this->isNamespaceAware) {
			$tagName = $this->expandNamespacePrefix($tagName);
			$this->fireEndElementEvent($tagName);
			$this->detectEndNamespaceDeclaration();
//			$defaultNamespace = array_pop($this->defaultNamespaceStack);
		} else {
			$this->fireEndElementEvent($tagName);
		}
	}

	function detectEndNamespaceDeclaration() {
		$isNamespaceEnded = array_pop($this->namespaceStack);
		if($isNamespaceEnded) {
			$map = array_pop($this->namespaceMap);
			foreach($map as $key => $value) {
				if($key == ':') {
					$key = '';
				}
				$this->fireEndNamespaceDeclarationEvent($key);
			}
		}
	}

	function parseProcessingInstruction($data) {
		$endTarget = 0;
		$total = strlen($data);
		for($x = 2; $x < $total; $x++) {
			if(trim($data{$x}) == '') {
				$endTarget = $x;
				break;
			}
		}
		$target = substr($data,1,($endTarget - 1));
		$data = substr($data,($endTarget + 1),($total - $endTarget - 2));
		if($this->processingInstructionHandler != null) {
			$this->fireProcessingInstructionEvent($target,$data);
		}
	}

	function parseComment($data) {
		if($this->commentHandler != null) {
			$this->fireCommentEvent($data);
		}
	}

	function fireDTDEvent($data) {
		call_user_func($this->DTDHandler,$this,$data);
	}

	function fireCommentEvent($data) {
		call_user_func($this->commentHandler,$this,$data);
	}

	function fireProcessingInstructionEvent($target,$data) {
		call_user_func($this->processingInstructionHandler,$this,$target,$data);
	}

	function fireStartNamespaceDeclarationEvent($prefix,$uri) {
		call_user_func($this->startNamespaceDeclarationHandler,$this,$prefix,$uri);
	}

	function fireEndNamespaceDeclarationEvent($prefix) {
		call_user_func($this->endNamespaceDeclarationHandler,$this,$prefix);
	}

	function xml_get_error_code() {
		return $this->errorCode;
	}

	function xml_error_string($code) {
		switch($code) {
			case SAXY_XML_ERROR_NONE:
				return "No error";
				break;
			case SAXY_XML_ERROR_NO_MEMORY:
				return "Out of memory";
				break;
			case SAXY_XML_ERROR_SYNTAX:
				return "Syntax error";
				break;
			case SAXY_XML_ERROR_NO_ELEMENTS:
				return "No elements in document";
				break;
			case SAXY_XML_ERROR_INVALID_TOKEN:
				return "Invalid token";
				break;
			case SAXY_XML_ERROR_UNCLOSED_TOKEN:
				return "Unclosed token";
				break;
			case SAXY_XML_ERROR_PARTIAL_CHAR:
				return "Partial character";
				break;
			case SAXY_XML_ERROR_TAG_MISMATCH:
				return "Tag mismatch";
				break;
			case SAXY_XML_ERROR_DUPLICATE_ATTRIBUTE:
				return "Duplicate attribute";
				break;
			case SAXY_XML_ERROR_JUNK_AFTER_DOC_ELEMENT:
				return "Junk encountered after document element";
				break;
			case SAXY_XML_ERROR_PARAM_ENTITY_REF:
				return "Parameter entity reference error";
				break;
			case SAXY_XML_ERROR_UNDEFINED_ENTITY:
				return "Undefined entity";
				break;
			case SAXY_XML_ERROR_RECURSIVE_ENTITY_REF:
				return "Recursive entity reference";
				break;
			case SAXY_XML_ERROR_ASYNC_ENTITY:
				return "Asynchronous internal entity found in external entity";
				break;
			case SAXY_XML_ERROR_BAD_CHAR_REF:
				return "Bad character reference";
				break;
			case SAXY_XML_ERROR_BINARY_ENTITY_REF:
				return "Binary entity reference";
				break;
			case SAXY_XML_ERROR_ATTRIBUTE_EXTERNAL_ENTITY_REF:
				return "Attribute external entity reference";
				break;
			case SAXY_XML_ERROR_MISPLACED_XML_PI:
				return "Misplaced processing instruction";
				break;
			case SAXY_XML_ERROR_UNKNOWN_ENCODING:
				return "Unknown encoding";
				break;
			case SAXY_XML_ERROR_INCORRECT_ENCODING:
				return "Incorrect encoding";
				break;
			case SAXY_XML_ERROR_UNCLOSED_CDATA_SECTION:
				return "Unclosed CDATA Section";
				break;
			case SAXY_XML_ERROR_EXTERNAL_ENTITY_HANDLING:
				return "Problem in external entity handling";
				break;
			default:
				return "No definition for error code ".$code;
				break;
		}
	}

}



?>
