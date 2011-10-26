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
if(!defined('PHP_TEXT_CACHE_INCLUDE_PATH')) {
	define('PHP_TEXT_CACHE_INCLUDE_PATH',(dirname(__file__)."/"));
}
require_once (PHP_TEXT_CACHE_INCLUDE_PATH.'php_http_connector.php');
class php_text_cache extends php_http_connector {
	var $cacheDir;
	var $cacheTime;
	var $doUseHTTPClient;
	var $httpTimeout;
	function php_text_cache($cacheDir = './',$cacheTime = -1,$timeout = 0) {
		$this->cacheDir = $cacheDir;
		$this->cacheTime = $cacheTime;
		$this->timeout = $timeout;
	}

	function setTimeout($timeout) {
		$this->timeout = $timeout;
	}

	function getData($url) {
		$cacheFile = $this->getCacheFileName($url);
		if(is_file($cacheFile)) {
			$fileStats = stat($cacheFile);
			$lastChangeTime = $fileStats[9];

			$currTime = time();
			if(($this->cacheTime != -1) && ($currTime - $lastChangeTime) > $this->cacheTime) {

				return $this->fromURL($url,$cacheFile);
			} else {

				return $this->fromCache($cacheFile);
			}
		} else {
			return $this->fromURL($url,$cacheFile);
		}
	}

	function getCacheFileName($url) {
		return ($this->cacheDir.md5($url));
	}

	function establishConnection($url) {
		require_once (PHP_TEXT_CACHE_INCLUDE_PATH.'php_http_client_generic.php');
		$host = php_http_connection::formatHost($url);
		$host = substr($host,0,strpos($host,'/'));
		$this->setConnection($host,'/',80,$this->timeout);
	}

	function useHTTPClient($truthVal) {


		$this->doUseHTTPClient = $truthVal;
	}

	function fromURL($url,$cacheFile) {
		$fileContents = '';
		if($this->httpConnection != null) {
			$response = &$this->httpConnection->get($url);
			if($response != null) {
				$fileContents = $response->getResponse();
			}
		} else
			if($this->doUseHTTPClient) {
				$this->establishConnection($url);
				$response = &$this->httpConnection->get($url);
				if($response != null) {
					$fileContents = $response->getResponse();
				}
			} else {
				$fileContents = $this->fromFile($url);
			}


			if(($fileContents == '') && !$this->doUseHTTPClient) {
				$this->establishConnection($url);
				$response = &$this->httpConnection->get($url);
				if($response != null) {
					$fileContents = $response->getResponse();
				}
			}
		if($fileContents != '') {
			require_once (PHP_TEXT_CACHE_INCLUDE_PATH.'php_file_utilities.php');
			php_file_utilities::putDataToFile($cacheFile,$fileContents,'w');
		}
		return $fileContents;
	}

	function fromCache($cacheFile) {
		return $this->fromFile($cacheFile);
	}

	function fromFile($filename) {
		if(function_exists('file_get_contents')) {
			return @file_get_contents($filename);
		} else {
			require_once (PHP_TEXT_CACHE_INCLUDE_PATH.'php_file_utilities.php');
			$fileContents = &php_file_utilities::getDataFromFile($filename,'r');
			return $fileContents;
		}
		return '';
	}

}




?>
