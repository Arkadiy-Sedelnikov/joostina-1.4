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

define('DOMIT_RSS_ELEMENT_CHANNEL', 'channel');
define('DOMIT_RSS_ELEMENT_ITEM', 'item');
define('DOMIT_RSS_ELEMENT_TITLE', 'title');
define('DOMIT_RSS_ELEMENT_LINK', 'link');
define('DOMIT_RSS_ELEMENT_DESCRIPTION', 'description');
define('DOMIT_RSS_ATTR_VERSION', 'version');
define('DOMIT_RSS_ARRAY_ITEMS', 'item');

define('DOMIT_RSS_ARRAY_CHANNELS', 'channel');

define('DOMIT_RSS_ARRAY_CATEGORIES', 'category');

define('DOMIT_RSS_ABSTRACT_METHOD_INVOCATION_ERR', 101);
define('DOMIT_RSS_ELEMENT_NOT_FOUND_ERR', 102);
define('DOMIT_RSS_ATTR_NOT_FOUND_ERR', 103);
define('DOMIT_RSS_PARSING_ERR', 104);

define('DOMIT_RSS_ONERROR_CONTINUE', 1);
define('DOMIT_RSS_ONERROR_DIE', 2);
define('DOMIT_RSS_ONERROR_RETURN', 3);
class xml_domit_rss_base{
	var $node = null;
	var $rssDefinedElements = array();

	function getNode(){
		return $this->node;
	}

	function getAttribute($attr){
		if($this->node->hasAttribute($attr)){
			return $this->node->getAttribute($attr);
		}
		return "";
	}

	function hasAttribute($attr){
		return (($this->node->nodeType == DOMIT_ELEMENT_NODE) && $this->node->hasAttribute
		($attr));
	}

	function isRSSDefined($elementName){
		$isDefined = false;
		foreach($this->rssDefinedElements as $key => $value){
			if($elementName == $value){
				$isDefined = true;
				break;
			}
		}
		return $isDefined;
	}

	function isSimpleRSSElement($elementName){
		$elementName = strtolower($elementName);
		if(isset($this->DOMIT_RSS_indexer[$elementName])){
			return (get_class($this->getElement($elementName)) ==
				'xml_domit_rss_simpleelement');
		} else{
			return false;
		}
	}

	function get($htmlSafe = false, $subEntities = false){
		return $this->node->toString($htmlSafe, $subEntities);
	}

	function toNormalizedString($htmlSafe = false, $subEntities = false){
		return $this->node->toNormalizedString($htmlSafe, $subEntities);
	}

}

class xml_domit_rss_collection extends xml_domit_rss_elementindexer{
	var $elements = array();
	var $elementCount = 0;

	function addElement(&$node){
		$this->elements[] = &$node;
		$this->elementCount++;
		return;
	}

	function &getElementAt($index){
		return $this->elements[$index];
	}

	function &getElement($index){
		return $this->getElementAt($index);
	}

	function getElementCount(){
		return $this->elementCount;
	}

	function getElementText($elementName = false){
		$total = $this->getElementCount();
		$result = '';
		for($i = 0; $i < $total; $i++){
			$result .= $currElement->toString();
		}
		return $result;
	}

}

class xml_domit_rss_elementindexer extends xml_domit_rss_base{
	var $DOMIT_RSS_indexer = array();
	var $DOMIT_RSS_numericalIndexer;

	function _init(){
		$total = $this->node->childCount;
		for($i = 0; $i < $total; $i++){
			$currNode = &$this->node->childNodes[$i];

			$this->addIndexedElement($currNode);
		}
	}

	function addIndexedElement(&$node){
		$tagName = strtolower($node->nodeName);
		if(isset($this->DOMIT_RSS_indexer[$tagName])){
			if(strtolower(get_class($this->DOMIT_RSS_indexer[$tagName])) == 'domit_element'){
				$collection = new xml_domit_rss_collection();
				$collection->addElement($this->DOMIT_RSS_indexer[$tagName]);
				$collection->addElement($node);
				$this->DOMIT_RSS_indexer[$tagName] = &$collection;
			} else{


			}
		} else{
			$this->DOMIT_RSS_indexer[$tagName] = &$node;
		}
	}

	function isCollection($elementName){
		$elementName = strtolower($elementName);
		if(isset($this->DOMIT_RSS_indexer[$elementName])){
			return (get_class($this->DOMIT_RSS_indexer[$elementName]) ==
				'xml_domit_rss_collection');
		} else{
			return false;
		}
	}

	function isNode($elementName){
		$elementName = strtolower($elementName);
		if(isset($this->DOMIT_RSS_indexer[$elementName])){
			return (strtolower(get_class($this->DOMIT_RSS_indexer[$elementName])) ==
				'domit_element');
		} else{
			return false;
		}
	}

	function isCustomRSSElement($elementName){
		return isNode($elementName);
	}

	function getElementList(){
		return array_keys($this->DOMIT_RSS_indexer);
	}

	function hasElement($elementName){
		return isset($this->DOMIT_RSS_indexer[strtolower($elementName)]);
	}

	function &getElement($elementName){
		$elementName = strtolower($elementName);
		if(isset($this->DOMIT_RSS_indexer[$elementName])){
			return $this->DOMIT_RSS_indexer[$elementName];
		} else{
			xml_domit_rss_exception::raiseException(DOMIT_RSS_ELEMENT_NOT_FOUND_ERR,
				'Element ' . $elementName . ' not present.');
		}
	}

	function &getElementAt($index){
		$this->indexNumerically();
		if(isset($this->DOMIT_RSS_numericalIndexer[$index])){
			return $this->DOMIT_RSS_numericalIndexer[$index];
		} else{
			xml_domit_rss_exception::raiseException(DOMIT_RSS_ELEMENT_NOT_FOUND_ERR,
				'Element ' . $index . ' not present.');
		}
	}

	function indexNumerically(){
		if(!isset($this->DOMIT_RSS_numericalIndexer)){
			$counter = 0;
			foreach($this->DOMIT_RSS_indexer as $key => $value){
				$this->DOMIT_RSS_numericalIndexer[$counter] = &$this->DOMIT_RSS_indexer[$key];
				$counter++;
			}
		}
	}

	function getElementText($elementName){
		$elementName = strtolower($elementName);
		return $this->_getElementText($elementName, $this->DOMIT_RSS_indexer);
	}

	function getElementTextAt($index){
		$this->indexNumerically();
		return $this->_getElementText($index, $this->DOMIT_RSS_numericalIndexer);
	}

	function _getElementText($index, &$myArray){
		if(isset($myArray[$index])){
			$element = &$myArray[$index];
			$result = '';
			if(is_array($element)){


			} else{
				switch(strtolower(get_class($element))){
					case 'xml_domit_rss_simpleelement':
						$result = $element->getElementText();
						break;
					case 'xml_domit_rss_collection':
						$result = $element->getElementText();
						break;
					case 'domit_element':
						$total = $element->childCount;
						for($i = 0; $i < $total; $i++){
							$currNode = &$element->childNodes[$i];
							if($currNode->nodeType == DOMIT_CDATA_SECTION_NODE){
								$result .= $currNode->nodeValue;
							} else{
								$result .= $currNode->toString();
							}
						}
						break;
				}
			}
			return $result;
		}
		return '';
	}

}

class xml_domit_rss_base_document extends xml_domit_rss_elementindexer{
	var $domit_rss_items = array();
	var $domit_rss_channels = array();
	var $domit_rss_categories = array();
	var $cacheEnabled = true;
	var $cache;
	var $useCacheLite = false;
	var $doUseHTTPClient = false;
	var $parser;
	var $httpConnection = null;
	var $rssTimeout = 0;

	function xml_domit_rss_base_document($url = '', $cacheDir = './', $cacheTime = 3600){
		$success = null;
		$this->createDocument();
		if($url != ''){

			if(substr($url, 0, 4) != "http"){
				$rssText = $this->getTextFromFile($url);
				$this->parseRSS($rssText);
			} else{
				$this->createDefaultCache($cacheDir, $cacheTime);
				$success = $this->loadRSS($url, $cacheDir, $cacheTime);
			}
		}
		return $success;
	}

	function setRSSTimeout($rssTimeout){
		$this->rssTimeout = $rssTimeout;
		if(!$this->useCacheLite && !($this->cache == null)){
			$this->cache->setTimeout($rssTimeout);
		}
	}

	function setConnection($host, $path = '/', $port = 80, $timeout = 0, $user = null, $password = null){
		require_once (DOMIT_RSS_INCLUDE_PATH . 'php_http_client_generic.php');
		$this->httpConnection = new php_http_client_generic($host, $path, $port, $timeout, $user, $password);
	}

	function setAuthorization($user, $password){
		$this->httpConnection->setAuthorization($user, $password);
	}

	function setProxyConnection($host, $path = '/', $port = 80, $timeout = 0, $user = null,
		$password = null){
		require_once (DOMIT_RSS_INCLUDE_PATH . 'php_http_proxy.php');
		$this->httpConnection = new php_http_proxy($host, $path, $port, $timeout, $user, $password);
	}

	function setProxyAuthorization($user, $password){
		$this->httpConnection->setProxyAuthorization($user, $password);
	}

	function useHTTPClient($truthVal){
		$this->doUseHTTPClient = $truthVal;
	}

	function parsedBy(){
		return $this->parser;
	}

	function createDocument(){
		require_once (DOMIT_RSS_INCLUDE_PATH . 'xml_domit_include.php');
		$this->node = new DOMIT_Document();
		$this->node->resolveErrors(true);
	}

	function useCacheLite($doUseCacheLite, $pathToLibrary = './Lite.php', $cacheDir = './', $cacheTime = 3600){
		$this->useCacheLite = $doUseCacheLite;
		if($doUseCacheLite){
			if(!file_exists($pathToLibrary)){
				$this->useCacheLite(false);
			} else{
				require_once ($pathToLibrary);
				$cacheOptions = array(
					'cacheDir' => $cacheDir,
					'lifeTime' => $cacheTime

				);
				$this->cache = new Cache_Lite($cacheOptions);
			}
		} else{
			$this->createDefaultCache($cacheDir, $cacheTime);
		}
	}

	function createDefaultCache($cacheDir = './', $cacheTime = 3600){
		require_once (DOMIT_RSS_INCLUDE_PATH . 'php_text_cache.php');
		$this->cache = new php_text_cache($cacheDir, $cacheTime, $this->rssTimeout);
	}

	function disableCache(){
		$this->cacheEnabled = false;
	}

	function loadRSS($url){
		if(substr($url, 0, 4) != "http"){
			$rssText = $this->getTextFromFile($url);
			return $this->parseRSS($rssText);
		} else{
			if($this->cacheEnabled && !isset($this->cache)){
				$this->createDefaultCache();
				$this->cache->httpConnection = &$this->httpConnection;
			}
			$success = $this->loadRSSData($url);
			if($success){
				$this->_init();
			}
			return $success;
		}
	}

	function parseRSS($rssText){
		if($this->cacheEnabled && !isset($this->cache))
			$this->createDefaultCache();
		$success = $this->parseRSSData($rssText);
		if($success){
			$this->_init();
		}
		return $success;
	}

	function loadRSSData($url){
		$rssText = $this->getDataFromCache($url);
		return $this->parseRSSData($rssText);
	}

	function getDataFromCache($url){
		if($this->cacheEnabled){
			if($this->useCacheLite){
				if($rssText = $this->cache->get($url)){
					return $rssText;
				} else{
					$rssText = $this->getTextFromFile($url);
					if($rssText != '')
						$this->cache->save($rssText, $url);
					return $rssText;
				}
			} else{
				$this->cache->useHTTPClient($this->doUseHTTPClient);
				return $this->cache->getData($url);
			}
		} else{
			return $this->getTextFromFile($url);
		}
	}

	function parseRSSData($rssText){
		if($rssText != ''){
			return $this->fromString($rssText);
		} else{
			return false;
		}
	}

	function &fromFile($filename){
		$success = $this->node->loadXML($filename, false);
		return $success;
	}

	function &fromString($rssText){
		$success = $this->node->parseXML($rssText, false);
		return $success;
	}

	function establishConnection($url){
		require_once (DOMIT_RSS_INCLUDE_PATH . 'php_http_client_generic.php');
		$host = php_http_connection::formatHost($url);
		$host = substr($host, 0, strpos($host, '/'));
		$this->setConnection($host, '/', 80, $this->rssTimeout);
	}

	function getTextFromFile($filename){
		$fileContents = '';
		if($this->doUseHTTPClient){
			$this->establishConnection($filename);
			$response = &$this->httpConnection->get($filename);
			if($response != null){
				$fileContents = $response->getResponse();
			}
		} else{
			if(function_exists('file_get_contents')){
				$fileContents = @file_get_contents($filename);
			} else{
				require_once (DOMIT_RSS_INCLUDE_PATH . 'php_file_utilities.php');
				$fileContents = &php_file_utilities::getDataFromFile($filename, 'r');
			}
			if($fileContents == ''){
				$this->establishConnection($filename);
				$response = &$this->httpConnection->get($filename);
				if($response != null){
					$fileContents = $response->getResponse();
				}
			}
		}
		$fileContents = Jstring::to_utf8($fileContents);
		return $fileContents;
	}

	function &getDocument(){
		return $this->node;
	}

	function getNode(){
		return $this->node->documentElement;
	}

	function handleChannelElementsEmbedded(){
		if(count($this->domit_rss_items) > 0){
			foreach($this->domit_rss_channels as $key => $value){
				$this->domit_rss_channels[$key]->domit_rss_items = &$this->domit_rss_items;
				if(count($this->DOMIT_RSS_indexer) > 0){
					foreach($this->DOMIT_RSS_indexer as $ikey => $ivalue){
						if($ikey != DOMIT_RSS_ARRAY_CHANNELS){
							$this->domit_rss_channels[$key]->DOMIT_RSS_indexer[$ikey] = &$this->DOMIT_RSS_indexer[$ikey];
							unset($this->DOMIT_RSS_indexer[$ikey]);
						}
					}
				}
			}
		}
	}

	function getRSSVersion(){
		$version = $this->node->documentElement->getAttribute(DOMIT_RSS_ATTR_VERSION);
		if($version == ''){
			$xmlns = $this->node->documentElement->getAttribute('xmlns');
			$total = strlen($xmlns);
			if(substr($xmlns, $total) == '/'){
				$total--;
			}
			for($i = ($total - 1); $i > -1; $i--){
				$currentChar = substr($xmlns, $i);
				if($currentChar == '/'){
					break;
				} else{
					$version = $currentChar . $version;
				}
			}
		}
		return $version;
	}

	function getChannelCount(){
		return count($this->domit_rss_channels);
	}

	function &getChannel($index){
		return $this->domit_rss_channels[$index];
	}

}

class xml_domit_rss_simpleelement extends xml_domit_rss_elementindexer{
	function xml_domit_rss_simpleelement(&$element){
		$this->node = &$element;
	}

	function getElementText($elementName = false){
		$element = &$this->node;
		$result = '';
		$total = $element->childCount;
		for($i = 0; $i < $total; $i++){
			$currNode = &$element->childNodes[$i];
			if($currNode->nodeType == DOMIT_CDATA_SECTION_NODE){
				$result .= $currNode->nodeValue;
			} else{
				$result .= $currNode->toString();
			}
		}
		return $result;
	}

}

$GLOBALS['DOMIT_RSS_Exception_errorHandler'] = null;


$GLOBALS['DOMIT_RSS_Exception_mode'] = 1;
$GLOBALS['DOMIT_RSS_Exception_log'] = null;
class xml_domit_rss_exception{
	function raiseException($errorNum, $errorString){
		if($GLOBALS['DOMIT_RSS_Exception_errorHandler'] != null){
			call_user_func($GLOBALS['DOMIT_RSS_Exception_errorHandler'], $errorNum, $errorString);
		} else{
			$errorMessageText = $errorNum . ' ' . $errorString;
			$errorMessage = 'Error: ' . $errorMessageText;
			if((!isset($GLOBALS['DOMIT_RSS_ERROR_FORMATTING_HTML'])) || ($GLOBALS['DOMIT_RSS_ERROR_FORMATTING_HTML'] == true)){
				$errorMessage = "<p><pre>" . $errorMessage . "</pre></p>";
			}

			if((isset($GLOBALS['DOMIT_RSS_Exception_log'])) && ($GLOBALS['DOMIT_RSS_Exception_log'] != null)){
				require_once (DOMIT_RSS_INCLUDE_PATH . 'php_file_utilities.php');
				$logItem = "\n" . date('Y-m-d H:i:s') . 'DOMIT! RSS Error ' . $errorMessageText;
				php_file_utilities::putDataToFile($GLOBALS['DOMIT_RSS_Exception_log'], $logItem,
					'a');
			}
			switch($GLOBALS['DOMIT_RSS_Exception_mode']){
				case DOMIT_RSS_ONERROR_CONTINUE:
					return;
					break;
				case DOMIT_RSS_ONERROR_DIE:
					die($errorMessage);
					break;
			}
		}
	}

	function setErrorHandler($method){
		$GLOBALS['DOMIT_RSS_Exception_errorHandler'] = &$method;
		require_once (DOMIT_RSS_INCLUDE_PATH . 'php_http_exceptions.php');
		$GLOBALS['HTTP_Exception_errorHandler'] = &$method;
		require_once (DOMIT_RSS_INCLUDE_PATH . 'xml_domit_shared.php');
		$GLOBALS['HTTP_Exception_errorHandler'] = &$method;
	}

	function setErrorMode($mode){
		$GLOBALS['DOMIT_RSS_Exception_mode'] = $mode;
		require_once (DOMIT_RSS_INCLUDE_PATH . 'php_http_exceptions.php');
		require_once (DOMIT_RSS_INCLUDE_PATH . 'xml_domit_shared.php');
		if($mode == DOMIT_RSS_ONERROR_CONTINUE){
			$GLOBALS['HTTP_Exception_mode'] = HTTP_ONERROR_CONTINUE;
			$GLOBALS['DOMIT_DOMException_mode'] = DOMIT_ONERROR_CONTINUE;
		} else{
			$GLOBALS['HTTP_Exception_mode'] = HTTP_ONERROR_DIE;
			$GLOBALS['DOMIT_DOMException_mode'] = DOMIT_ONERROR_DIE;
		}
	}

	function setErrorLog($doLogErrors, $logfile){
		require_once (DOMIT_RSS_INCLUDE_PATH . 'php_http_exceptions.php');
		if($doLogErrors){
			$GLOBALS['DOMIT_RSS_Exception_log'] = $logfile;
			$GLOBALS['HTTP_Exception_log'] = $logfile;
			$GLOBALS['DOMIT_Exception_log'] = $logfile;
		} else{
			$GLOBALS['DOMIT_RSS_Exception_log'] = null;
			$GLOBALS['HTTP_Exception_log'] = null;
			$GLOBALS['DOMIT_Exception_log'] = null;
		}
	}

}


?>
