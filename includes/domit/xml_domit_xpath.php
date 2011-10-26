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
if(!defined('DOMIT_INCLUDE_PATH')) {
	define('DOMIT_INCLUDE_PATH',(dirname(__file__)."/"));
}
define('DOMIT_XPATH_SEPARATOR_ABSOLUTE','/');
define('DOMIT_XPATH_SEPARATOR_RELATIVE','//');
define('DOMIT_XPATH_SEPARATOR_OR','|');
define('DOMIT_XPATH_SEARCH_ABSOLUTE',0);
define('DOMIT_XPATH_SEARCH_RELATIVE',1);
define('DOMIT_XPATH_SEARCH_VARIABLE',2);
class DOMIT_XPath {
	var $callingNode;
	var $searchType;
	var $arPathSegments = array();
	var $nodeList;
	var $charContainer;
	var $currChar;
	var $currentSegment;
	var $globalNodeContainer;
	var $localNodeContainer;
	var $normalizationTable = array('child::' => '','self::' => '.','attribute::' =>
		'@','descendant::' => '*//',"\t" => ' ',"\x0B" => ' ');
	var $normalizationTable2 = array(' =' => '=','= ' => '=',' <' => '<',' >' => '>',
		'< ' => '<','> ' => '>',' !' => '!','( ' => '(',' )' => ')',' ]' => ']','] ' =>
		']',' [' => '[','[ ' => '[',' /' => '/','/ ' => '/','"' => "'");
	var $normalizationTable3 = array('position()=' => '',
		'/descendant-or-self::node()/' => "//",'self::node()' => '.','parent::node()' =>
		'..');
	function DOMIT_XPath() {
		require_once (DOMIT_INCLUDE_PATH.'xml_domit_nodemaps.php');
		$this->nodeList = new DOMIT_NodeList();
	}

	function &parsePattern(&$node,$pattern,$nodeIndex = 0) {
		$this->callingNode = &$node;
		$pattern = $this->normalize(trim($pattern));
		$this->splitPattern($pattern);
		$total = count($this->arPathSegments);

		for($i = 0; $i < $total; $i++) {
			$outerArray = &$this->arPathSegments[$i];
			$this->initSearch($outerArray);
			$outerTotal = count($outerArray);
			$isInitialMatchAttempt = true;

			for($j = 0; $j < $outerTotal; $j++) {
				$innerArray = &$outerArray[$j];
				$innerTotal = count($innerArray);
				if(!$isInitialMatchAttempt) {
					$this->searchType = DOMIT_XPATH_SEARCH_VARIABLE;
				}

				for($k = 0; $k < $innerTotal; $k++) {
					$currentPattern = $innerArray[$k];
					if(($k == 0) && ($currentPattern == null)) {
						if($innerTotal == 1) {
							$isInitialMatchAttempt = false;
						}

					} else {
						if(!$isInitialMatchAttempt && ($k > 0)) {
							$this->searchType = DOMIT_XPATH_SEARCH_RELATIVE;
						}
						$this->currentSegment = $currentPattern;
						$this->processPatternSegment();
						$isInitialMatchAttempt = false;
					}
				}
			}
		}
		if($nodeIndex > 0) {
			if($nodeIndex <= count($this->globalNodeContainer)) {
				return $this->globalNodeContainer[($nodeIndex - 1)];
			} else {
				$null = null;
				return $null;
			}
		}
		if(count($this->globalNodeContainer) != 0) {
			foreach($this->globalNodeContainer as $key => $value) {
				$currNode = &$this->globalNodeContainer[$key];
				$this->nodeList->appendNode($currNode);
			}
		}
		return $this->nodeList;
	}

	function processPatternSegment() {
		$total = strlen($this->currentSegment);
		$this->charContainer = '';
		$this->localNodeContainer = array();
		for($i = 0; $i < $total; $i++) {
			$this->currChar = $this->currentSegment {
				$i}
			;
			switch($this->currChar) {
				case '@':
					$this->selectAttribute(substr($this->currentSegment,($this->currChar + 1)));
					$this->updateNodeContainers();
					return;

				case '*':
					if($i == ($total - 1)) {
						$this->selectNamedChild('*');
					} else {
						$this->charContainer .= $this->currChar;
					}
					break;
				case '.':
					$this->charContainer .= $this->currChar;
					if($i == ($total - 1)) {
						if($this->charContainer == '..') {
							$this->selectParent();
						} else {
							return;
						}
					}
					break;
				case ')':
					$this->charContainer .= $this->currChar;
					$this->selectNodesByFunction();
					break;
				case '[':
					$this->parsePredicate($this->charContainer,substr($this->currentSegment,($i + 1)));
					return;

				default:
					$this->charContainer .= $this->currChar;
			}
		}
		if($this->charContainer != '') {
			$this->selectNamedChild($this->charContainer);
		}
		$this->updateNodeContainers();
	}

	function updateNodeContainers() {
		$this->globalNodeContainer = &$this->localNodeContainer;
		unset($this->localNodeContainer);
	}

	function parsePredicate($nodeName,$patternSegment) {
		$arPredicates = &explode('][',$patternSegment);
		$total = count($arPredicates);
		$lastIndex = $total - 1;
		$arPredicates[$lastIndex] = substr($arPredicates[$lastIndex],0,(strlen($arPredicates[$lastIndex]) -
			1));
		for($i = 0; $i < $total; $i++) {
			$isRecursive = ($this->searchType == DOMIT_XPATH_SEARCH_VARIABLE)?true:false;
			$currPredicate = $arPredicates[$i];
			if(is_numeric($currPredicate)) {
				if($i == 0) {
					$this->filterByIndex($nodeName,intval($currPredicate),$isRecursive);
				} else {
					$this->refilterByIndex(intval($currPredicate));
				}
			} else {
				if($i == 0) {
					$this->selectNamedChild($nodeName);
					$this->updateNodeContainers();
				}
				$phpExpression = $this->predicateToPHP($currPredicate);
				$this->filterByPHPExpression($phpExpression);
			}
			$this->updateNodeContainers();
		}
		$this->charContainer = '';
	}

	function predicateToPHP($predicate) {
		$phpExpression = $predicate;
		$currChar = '';
		$charContainer = '';
		$totalChars = strlen($predicate);
		for($i = 0; $i < $totalChars; $i++) {
			$currChar = substr($predicate,$i,1);
			switch($currChar) {
				case '(':
				case ')':
				case ' ':
					if($charContainer != '') {
						$convertedPredicate = $this->expressionToPHP($charContainer);
						$phpExpression = str_replace($charContainer,$convertedPredicate,$phpExpression);
						$charContainer = '';
					}
					break;
				default:
					$charContainer .= $currChar;
			}
		}
		if($charContainer != '') {
			$convertedPredicate = $this->expressionToPHP($charContainer);
			$phpExpression = str_replace($charContainer,$convertedPredicate,$phpExpression);
		}
		return $phpExpression;
	}

	function expressionToPHP($expression) {
		if($expression == 'and') {
			$expression = '&&';
		} else
			if($expression == 'or') {
				$expression = '||';
			} else
				if($expression == 'not') {
					$expression = '!';
				} else {
					$expression = trim($expression);
					if(strpos($expression,'@') !== false) {
						if(strpos($expression,'>=') !== false) {
							$expression = str_replace('@',('floatval($'."contextNode->getAttribute('"),$expression);
							$expression = str_replace('>=',"')) >= floatval(",$expression);
							if(!is_numeric($expression))
								$expression = str_replace('floatval','',$expression);
							$expression .= ')';
						} else
							if(strpos($expression,'<=') !== false) {
								$expression = str_replace('@',('floatval($'."contextNode->getAttribute('"),$expression);
								$expression = str_replace('<=',"')) <= floatval(",$expression);
								if(!is_numeric($expression))
									$expression = str_replace('floatval','',$expression);
								$expression .= ')';
							} else
								if(strpos($expression,'!=') !== false) {
									$expression = str_replace('@',('$'."contextNode->getAttribute('"),$expression);
									$expression = str_replace('!=',"') != ",$expression);
								} else
									if(strpos($expression,'=') !== false) {
										$expression = str_replace('@',('$'."contextNode->getAttribute('"),$expression);
										$expression = str_replace('=',"') == ",$expression);
									} else
										if(strpos($expression,'>') !== false) {
											$expression = str_replace('>',"')) > floatval(",$expression);

											$expression = str_replace('@',('floatval($'."contextNode->getAttribute('"),$expression);
											if(!is_numeric($expression))
												$expression = str_replace('floatval','',$expression);
											$expression .= ')';
										} else
											if(strpos($expression,'<') !== false) {
												$expression = str_replace('@',('floatval($'."contextNode->getAttribute('"),$expression);
												$expression = str_replace('<',"')) < floatval(",$expression);
												if(!is_numeric($expression))
													$expression = str_replace('floatval','',$expression);
												$expression .= ')';
											} else {
												$expression = str_replace('@',('$'."contextNode->hasAttribute('"),$expression);
												$expression .= "')";
											}
					} else {
						if(strpos($expression,'>=') !== false) {
							$signPos = strpos($expression,'>=');
							$elementName = trim(substr($expression,0,$signPos));
							$elementValue = trim(substr($expression,($signPos + 2)));
							$expression = '$'."this->hasNamedChildElementGreaterThanOrEqualToValue(".'$'.
								"contextNode, '".$elementName."', ".$elementValue.')';
						} else
							if(strpos($expression,'<=') !== false) {
								$signPos = strpos($expression,'>=');
								$elementName = trim(substr($expression,0,$signPos));
								$elementValue = trim(substr($expression,($signPos + 2)));
								$expression = '$'."this->hasNamedChildElementLessThanOrEqualToValue(".'$'.
									"contextNode, '".$elementName."', ".$elementValue.')';
							} else
								if(strpos($expression,'!=') !== false) {
									$signPos = strpos($expression,'>=');
									$elementName = trim(substr($expression,0,$signPos));
									$elementValue = trim(substr($expression,($signPos + 2)));
									$expression = '$'."this->hasNamedChildElementNotEqualToValue(".'$'.
										"contextNode, '".$elementName."', ".$elementValue.')';
								} else
									if(strpos($expression,'=') !== false) {
										$signPos = strpos($expression,'=');
										$elementName = trim(substr($expression,0,$signPos));
										$elementValue = trim(substr($expression,($signPos + 1)));
										$expression = '$'."this->hasNamedChildElementEqualToValue(".'$'.
											"contextNode, '".$elementName."', ".$elementValue.')';
									} else
										if(strpos($expression,'>') !== false) {
											$signPos = strpos($expression,'=');
											$elementName = trim(substr($expression,0,$signPos));
											$elementValue = trim(substr($expression,($signPos + 1)));
											$expression = '$'."this->hasNamedChildElementGreaterThanValue(".'$'.
												"contextNode, '".$elementName."', ".$elementValue.')';
										} else
											if(strpos($expression,'<') !== false) {
												$signPos = strpos($expression,'=');
												$elementName = trim(substr($expression,0,$signPos));
												$elementValue = trim(substr($expression,($signPos + 1)));
												$expression = '$'."this->hasNamedChildElementLessThanValue(".'$'.
													"contextNode, '".$elementName."', ".$elementValue.')';
											} else {
												$expression = '$'."this->hasNamedChildElement(".'$'."contextNode, '".$expression.
													"')";
											}
					}
				}
				return $expression;
	}

	function filterByPHPExpression($expression) {
		if(count($this->globalNodeContainer) != 0) {
			foreach($this->globalNodeContainer as $key => $value) {
				$contextNode = &$this->globalNodeContainer[$key];
				if($contextNode->nodeType == DOMIT_ELEMENT_NODE) {
					$evaluatedExpression = 'if ('.$expression.") $".
						'this->localNodeContainer[] =& $'.'contextNode;';
					eval($evaluatedExpression);
				}
			}
		}
	}

	function hasNamedChildElement(&$parentNode,$nodeName) {
		$total = $parentNode->childCount;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$parentNode->childNodes[$i];
			if(($currNode->nodeType == DOMIT_ELEMENT_NODE) && ($currNode->nodeName == $nodeName)) {
				return true;
			}
		}
		return false;
	}

	function hasNamedChildElementEqualToValue(&$parentNode,$nodeName,$nodeValue) {
		$total = $parentNode->childCount;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$parentNode->childNodes[$i];
			if(($currNode->nodeType == DOMIT_ELEMENT_NODE) && ($currNode->nodeName == $nodeName) &&
				($currNode->getText() == $nodeValue)) {
				return true;
			}
		}
		return false;
	}

	function hasNamedChildElementGreaterThanOrEqualToValue(&$parentNode,$nodeName,$nodeValue) {
		$isNumeric = false;
		if(is_numeric($nodeValue)) {
			$isNumeric = true;
			$nodeValue = floatval($nodeValue);
		}
		$total = $parentNode->childCount;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$parentNode->childNodes[$i];
			if(($currNode->nodeType == DOMIT_ELEMENT_NODE) && ($currNode->nodeName == $nodeName)) {
				if($isNumeric) {
					$compareVal = floatval($currNode->getText());
				} else {
					$compareVal = $currNode->getText();
				}
				if($compareVal >= $nodeValue)
					return true;
			}
		}
		return false;
	}

	function hasNamedChildElementLessThanOrEqualToValue(&$parentNode,$nodeName,$nodeValue) {
		$isNumeric = false;
		if(is_numeric($nodeValue)) {
			$isNumeric = true;
			$nodeValue = floatval($nodeValue);
		}
		$total = $parentNode->childCount;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$parentNode->childNodes[$i];
			if(($currNode->nodeType == DOMIT_ELEMENT_NODE) && ($currNode->nodeName == $nodeName)) {
				if($isNumeric) {
					$compareVal = floatval($currNode->getText());
				} else {
					$compareVal = $currNode->getText();
				}
				if($compareVal <= $nodeValue)
					return true;
			}
		}
		return false;
	}

	function hasNamedChildElementNotEqualToValue(&$parentNode,$nodeName,$nodeValue) {
		$isNumeric = false;
		if(is_numeric($nodeValue)) {
			$isNumeric = true;
			$nodeValue = floatval($nodeValue);
		}
		$total = $parentNode->childCount;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$parentNode->childNodes[$i];
			if(($currNode->nodeType == DOMIT_ELEMENT_NODE) && ($currNode->nodeName == $nodeName)) {
				if($isNumeric) {
					$compareVal = floatval($currNode->getText());
				} else {
					$compareVal = $currNode->getText();
				}
				if($compareVal != $nodeValue)
					return true;
			}
		}
		return false;
	}

	function hasNamedChildElementGreaterThanValue(&$parentNode,$nodeName,$nodeValue) {
		$isNumeric = false;
		if(is_numeric($nodeValue)) {
			$isNumeric = true;
			$nodeValue = floatval($nodeValue);
		}
		$total = $parentNode->childCount;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$parentNode->childNodes[$i];
			if(($currNode->nodeType == DOMIT_ELEMENT_NODE) && ($currNode->nodeName == $nodeName)) {
				if($isNumeric) {
					$compareVal = floatval($currNode->getText());
				} else {
					$compareVal = $currNode->getText();
				}
				if($compareVal > $nodeValue)
					return true;
			}
		}
		return false;
	}

	function hasNamedChildElementLessThanValue(&$parentNode,$nodeName,$nodeValue) {
		$isNumeric = false;
		if(is_numeric($nodeValue)) {
			$isNumeric = true;
			$nodeValue = floatval($nodeValue);
		}
		$total = $parentNode->childCount;
		for($i = 0; $i < $total; $i++) {
			$currNode = &$parentNode->childNodes[$i];
			if(($currNode->nodeType == DOMIT_ELEMENT_NODE) && ($currNode->nodeName == $nodeName)) {
				if($isNumeric) {
					$compareVal = floatval($currNode->getText());
				} else {
					$compareVal = $currNode->getText();
				}
				if($compareVal < $nodeValue)
					return true;
			}
		}
		return false;
	}

	function refilterByIndex($index) {
		if($index > 1) {
			if(count($this->globalNodeContainer) != 0) {
				$counter = 0;
				$lastParentID = null;
				foreach($this->globalNodeContainer as $key => $value) {
					$currNode = &$this->globalNodeContainer[$key];
					if(($lastParentID != null) && ($currNode->parentNode->uid != $lastParentID)) {
						$counter = 0;
					}
					$counter++;
					if(($counter == $index) && ($currNode->parentNode->uid == $lastParentID)) {
						$this->localNodeContainer[] = &$currNode;
					}
					$lastParentID = $currNode->parentNode->uid;
				}
			}
		} else {
			$this->localNodeContainer = &$this->globalNodeContainer;
		}
	}

	function filterByIndex($nodeName,$index,$deep) {
		if(count($this->globalNodeContainer) != 0) {
			foreach($this->globalNodeContainer as $key => $value) {
				$currNode = &$this->globalNodeContainer[$key];
				$this->_filterByIndex($currNode,$nodeName,$index,$deep);
			}
		}
	}

	function _filterByIndex(&$contextNode,$nodeName,$index,$deep) {
		if(($contextNode->nodeType == DOMIT_ELEMENT_NODE) || ($contextNode->nodeType ==
			DOMIT_DOCUMENT_NODE)) {
			$total = $contextNode->childCount;
			$nodeCounter = 0;
			for($i = 0; $i < $total; $i++) {
				$currChildNode = &$contextNode->childNodes[$i];
				if($currChildNode->nodeName == $nodeName) {
					$nodeCounter++;
					if($nodeCounter == $index) {
						$this->localNodeContainer[] = &$currChildNode;
					}
				}
				if($deep) {
					$this->_filterByIndex($currChildNode,$nodeName,$index,$deep);
				}
			}
		}
	}

	function filterByChildName($nodeName,$childName,$deep) {
		if(count($this->globalNodeContainer) != 0) {
			foreach($this->globalNodeContainer as $key => $value) {
				$currNode = &$this->globalNodeContainer[$key];
				$this->_filterByChildName($currNode,$nodeName,$childName,$deep);
			}
		}
	}

	function _filterByChildName(&$contextNode,$nodeName,$childName,$deep) {
		if(($contextNode->nodeType == DOMIT_ELEMENT_NODE) || ($contextNode->nodeType ==
			DOMIT_DOCUMENT_NODE)) {
			$total = $contextNode->childCount;
			for($i = 0; $i < $total; $i++) {
				$currChildNode = &$contextNode->childNodes[$i];
				if(($currChildNode->nodeName == $nodeName) && ($currChildNode->nodeType ==
					DOMIT_ELEMENT_NODE)) {
					$total2 = $currChildNode->childCount;
					for($j = 0; $j < $total2; $j++) {
						$currChildChildNode = &$currChildNode->childNodes[$j];
						if($currChildChildNode->nodeName == $childName) {
							$this->localNodeContainer[] = &$currChildNode;
						}
					}
				}
				if($deep) {
					$this->_filterByChildName($currChildNode,$nodeName,$childName,$deep);
				}
			}
		}
	}

	function selectAttribute($attrName) {
		if(count($this->globalNodeContainer) != 0) {
			foreach($this->globalNodeContainer as $key => $value) {
				$currNode = &$this->globalNodeContainer[$key];
				$isRecursive = ($this->searchType == DOMIT_XPATH_SEARCH_VARIABLE)?true:false;
				$this->_selectAttribute($currNode,$attrName,$isRecursive);
			}
		}
		$this->charContainer = '';
	}

	function _selectAttribute(&$contextNode,$attrName,$deep) {
		if(($contextNode->nodeType == DOMIT_ELEMENT_NODE) || ($contextNode->nodeType ==
			DOMIT_DOCUMENT_NODE)) {
			$total = $contextNode->childCount;
			for($i = 0; $i < $total; $i++) {
				$currNode = &$contextNode->childNodes[$i];
				if($currNode->nodeType == DOMIT_ELEMENT_NODE) {
					if($attrName == '*') {
						$total2 = $currNode->attributes->getLength();
						for($j = 0; $j < $total2; $j++) {
							$this->localNodeContainer[] = &$currNode->attributes->item($j);
						}
					} else {
						if($currNode->hasAttribute($attrName)) {
							$this->localNodeContainer[] = &$currNode->getAttributeNode($attrName);
						}
					}
				}
				if($deep) {
					$this->_selectAttribute($currNode,$attrName,$deep);
				}
			}
		}
	}

	function selectNamedChild($tagName) {
		if(count($this->globalNodeContainer) != 0) {
			foreach($this->globalNodeContainer as $key => $value) {
				$currNode = &$this->globalNodeContainer[$key];
				$isRecursive = ($this->searchType == DOMIT_XPATH_SEARCH_VARIABLE)?true:false;
				$this->_selectNamedChild($currNode,$tagName,$isRecursive);
			}
		}
		$this->charContainer = '';
	}

	function _selectNamedChild(&$contextNode,$tagName,$deep = false) {
		if(($contextNode->nodeType == DOMIT_ELEMENT_NODE) || ($contextNode->nodeType ==
			DOMIT_DOCUMENT_NODE)) {
			$total = $contextNode->childCount;
			for($i = 0; $i < $total; $i++) {
				$currChildNode = &$contextNode->childNodes[$i];
				if(($currChildNode->nodeType == DOMIT_ELEMENT_NODE) || ($currChildNode->nodeType ==
					DOMIT_DOCUMENT_NODE)) {
					if(($tagName == '*') || ($tagName == $currChildNode->nodeName)) {
						$this->localNodeContainer[] = &$currChildNode;
					}
					if($deep) {
						$this->_selectNamedChild($currChildNode,$tagName,$deep);
					}
				}
			}
		}
	}

	function selectParent() {
		if(count($this->globalNodeContainer) != 0) {
			foreach($this->globalNodeContainer as $key => $value) {
				$currNode = &$this->globalNodeContainer[$key];
				$isRecursive = ($this->searchType == DOMIT_XPATH_SEARCH_VARIABLE)?true:false;
				$this->_selectParent($currNode,$isRecursive);
			}
		}
		$this->charContainer = '';
	}

	function _selectParent(&$contextNode,$deep = false) {
		if($contextNode->nodeType == DOMIT_ELEMENT_NODE) {
			if($contextNode->parentNode != null) {
				$this->localNodeContainer[] = &$contextNode->parentNode;
			}
		}
		if($deep) {
			if(($contextNode->nodeType == DOMIT_ELEMENT_NODE) || ($contextNode->nodeType ==
				DOMIT_DOCUMENT_NODE)) {
				$total = $contextNode->childCount;
				for($i = 0; $i < $total; $i++) {
					$currNode = &$contextNode->childNodes[$i];
					if($currNode->nodeType == DOMIT_ELEMENT_NODE) {
						$this->_selectParent($contextNode,$deep);
					}
				}
			}
		}
	}

	function selectNodesByFunction() {
		$doProcess = false;
		$targetNodeType = -1;
		switch(strtolower(trim($this->charContainer))) {
			case 'last()':
				if(count($this->globalNodeContainer) != 0) {
					foreach($this->globalNodeContainer as $key => $value) {
						$currNode = &$this->globalNodeContainer[$key];
						if($currNode->nodeType == DOMIT_ELEMENT_NODE) {
							if($currNode->lastChild != null) {
								$this->localNodeContainer[] = &$currNode->lastChild;
							}
						}
					}
				}
				break;
			case 'text()':
				$doProcess = true;
				$targetNodeType = DOMIT_TEXT_NODE;
				break;
			case 'comment()':
				$doProcess = true;
				$targetNodeType = DOMIT_COMMENT_NODE;
				break;
			case 'processing-instruction()':
				$doProcess = true;
				$targetNodeType = DOMIT_PROCESSING_INSTRUCTION_NODE;
				break;
		}
		if($doProcess) {
			if(count($this->globalNodeContainer) != 0) {
				foreach($this->globalNodeContainer as $key => $value) {
					$currNode = &$this->globalNodeContainer[$key];
					if($currNode->nodeType == DOMIT_ELEMENT_NODE) {
						$total = $currNode->childCount;
						for($j = 0; $j < $total; $j++) {
							if($currNode->childNodes[$j]->nodeType == $targetNodeType) {
								$this->localNodeContainer[] = &$currNode->childNodes[$j];
							}
						}
					}
				}
			}
		}
		$this->charContainer = '';
	}

	function splitPattern($pattern) {

		$this->arPathSegments = &explode(DOMIT_XPATH_SEPARATOR_OR,$pattern);

		$total = count($this->arPathSegments);
		for($i = 0; $i < $total; $i++) {
			$this->arPathSegments[$i] = &explode(DOMIT_XPATH_SEPARATOR_RELATIVE,trim($this->arPathSegments[$i]));
			$currArray = &$this->arPathSegments[$i];
			$total2 = count($currArray);
			for($j = 0; $j < $total2; $j++) {
				$currArray[$j] = &explode(DOMIT_XPATH_SEPARATOR_ABSOLUTE,$currArray[$j]);
			}
		}
	}

	function normalize($pattern) {
		$pattern = strtr($pattern,$this->normalizationTable);
		while(strpos($pattern,'  ') !== false) {
			$pattern = str_replace('  ',' ',$pattern);
		}
		$pattern = strtr($pattern,$this->normalizationTable2);
		$pattern = strtr($pattern,$this->normalizationTable3);
		return $pattern;
	}

	function initSearch(&$currArPathSegments) {
		$this->globalNodeContainer = array();
		if(is_null($currArPathSegments[0])) {
			if(count($currArPathSegments) == 1) {

				$this->searchType = DOMIT_XPATH_SEARCH_VARIABLE;
				$this->globalNodeContainer[] = &$this->callingNode->ownerDocument;
			} else {

				$this->searchType = DOMIT_XPATH_SEARCH_ABSOLUTE;
				$this->globalNodeContainer[] = &$this->callingNode->ownerDocument;
			}
		} else {

			$this->searchType = DOMIT_XPATH_SEARCH_RELATIVE;
			if($this->callingNode->uid != $this->callingNode->ownerDocument->uid) {
				$this->globalNodeContainer[] = &$this->callingNode;
			} else {
				$this->globalNodeContainer[] = &$this->callingNode->ownerDocument;
			}
		}
	}

}



?>
