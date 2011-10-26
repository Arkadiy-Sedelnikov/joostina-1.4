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
define('DOMIT_ATTRIBUTEPARSER_STATE_ATTR_NONE',0);
define('DOMIT_ATTRIBUTEPARSER_STATE_ATTR_KEY',1);
define('DOMIT_ATTRIBUTEPARSER_STATE_ATTR_VALUE',2);
$GLOBALS['DOMIT_PREDEFINED_ENTITIES'] = array('&' => '&amp;','<' => '&lt;','>' =>
	'&gt;','"' => '&quot;',"'" => '&apos;');
class nodetools {
	function parseAttributes($attrText,$convertEntities = true,$definedEntities = null) {
		$attrText = trim($attrText);
		$attrArray = array();
		$maybeEntity = false;
		$total = strlen($attrText);
		$keyDump = '';
		$valueDump = '';
		$currentState = DOMIT_ATTRIBUTEPARSER_STATE_ATTR_NONE;
		$quoteType = '';

		for($i = 0; $i < $total; $i++) {
			$currentChar = $attrText{$i};
			if($currentState == DOMIT_ATTRIBUTEPARSER_STATE_ATTR_NONE) {
				if(trim($currentChar != '')) {
					$currentState = DOMIT_ATTRIBUTEPARSER_STATE_ATTR_KEY;
				}
			}
			switch($currentChar) {
				case "\t":
					if($currentState == DOMIT_ATTRIBUTEPARSER_STATE_ATTR_VALUE) {
						$valueDump .= $currentChar;
					} else {
						$currentChar = '';
					}
					break;
				case "\x0B":

				case "\n":
				case "\r":
					$currentChar = '';
					break;
				case '=':
					if($currentState == DOMIT_ATTRIBUTEPARSER_STATE_ATTR_VALUE) {
						$valueDump .= $currentChar;
					} else {
						$currentState = DOMIT_ATTRIBUTEPARSER_STATE_ATTR_VALUE;
						$quoteType = '';
						$maybeEntity = false;
					}
					break;
				case '"':
					if($currentState == DOMIT_ATTRIBUTEPARSER_STATE_ATTR_VALUE) {
						if($quoteType == '') {
							$quoteType = '"';
						} else {
							if($quoteType == $currentChar) {
								if($convertEntities && $maybeEntity) {
									$valueDump = strtr($valueDump,DOMIT_PREDEFINED_ENTITIES);
									$valueDump = strtr($valueDump,$definedEntities);
								}
								$attrArray[trim($keyDump)] = $valueDump;
								$keyDump = $valueDump = $quoteType = '';
								$currentState = DOMIT_ATTRIBUTEPARSER_STATE_ATTR_NONE;
							} else {
								$valueDump .= $currentChar;
							}
						}
					}
					break;
				case "'":
					if($currentState == DOMIT_ATTRIBUTEPARSER_STATE_ATTR_VALUE) {
						if($quoteType == '') {
							$quoteType = "'";
						} else {
							if($quoteType == $currentChar) {
								if($convertEntities && $maybeEntity) {
									$valueDump = strtr($valueDump,$predefinedEntities);
									$valueDump = strtr($valueDump,$definedEntities);
								}
								$attrArray[trim($keyDump)] = $valueDump;
								$keyDump = $valueDump = $quoteType = '';
								$currentState = DOMIT_ATTRIBUTEPARSER_STATE_ATTR_NONE;
							} else {
								$valueDump .= $currentChar;
							}
						}
					}
					break;
				case '&':

					$maybeEntity = true;
					$valueDump .= $currentChar;
					break;
				default:
					if($currentState == DOMIT_ATTRIBUTEPARSER_STATE_ATTR_KEY) {
						$keyDump .= $currentChar;
					} else {
						$valueDump .= $currentChar;
					}
			}
		}
		return $attrArray;
	}

	function moveUp(&$node) {
		if(($node->previousSibling != null) && ($node->parentNode != null)) {
			$parent = &$node->parentNode;
			$previous = &$node->previousSibling;
			$node = &$parent->removeChild($node);
			$parent->insertBefore($node,$previous);
		}
	}

	function moveDown(&$node) {
		if(($node->nextSibling != null) && ($node->parentNode != null)) {
			$parent = &$node->parentNode;
			if($node->nextSibling->nextSibling == null) {
				$node = &$parent->removeChild($node);
				$parent->appendChild($node);
			} else {
				$insertionPoint = &$node->nextSibling->nextSibling;
				$node = &$parent->removeChild($node);
				$parent->insertBefore($node,$insertionPoint);
			}
		}
	}

	function &nodeExists(&$callingNode,$path) {
		$foundNode = &$callingNode->getElementsByPath($path,1);
		if($foundNode == null)
			return false;
		return $foundNode;
	}

	function &fromPath(&$xmldoc,$path,$text = null) {
		$pathSegments = explode('/',$path);
		$parent = null;
		$lastNode = null;
		$total = count($pathSegments);
		for($i = 0; $i < $total; $i++) {
			if($pathSegments[$i] != '') {
				$currNode = &$xmldoc->createElement($pathSegments[$i]);
				if($parent == null) {
					$parent = &$currNode;
				} else {
					$lastNode->appendChild($currNode);
				}
				$lastNode = &$currNode;
			}
		}
		if($text != null) {
			$currNode = &$xmldoc->createTextNode($text);
			$lastNode->appendChild($currNode);
		}
		return $parent;
	}

}




?>
